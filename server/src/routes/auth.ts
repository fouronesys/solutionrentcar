import { Router } from "express";
import bcrypt from "bcryptjs";
import { one, q } from "../db.js";
import {
  consumeRefresh,
  issueTokens,
  requireAuth,
  resolveCompany,
  revokeAllFor,
  tryAuth,
} from "../auth.js";
import { legacySha1Md5 } from "../jwt.js";
import {
  err,
  h,
  normalizePhone,
  ok,
  personToArray,
  phoneVariants,
  toStr,
  userToArray,
} from "../helpers.js";

export const authRouter = Router();

async function verifyPassword(plain: string, hash: string, algo: string): Promise<boolean> {
  if (!hash) return false;
  if (algo === "sha1md5") return legacySha1Md5(plain) === hash;
  try {
    return await bcrypt.compare(plain, hash);
  } catch {
    return false;
  }
}

/** Re-hash legacy passwords to bcrypt on successful login. */
async function upgradeHash(table: "users" | "persons", id: number, plain: string, algo: string): Promise<void> {
  if (algo === "bcrypt") return;
  const newHash = await bcrypt.hash(plain, 10);
  await q(`UPDATE ${table} SET password_hash=$1, password_algo='bcrypt' WHERE id=$2`, [newHash, id]);
}

// POST /auth/login { username, password, role?: 'staff'|'client' }
authRouter.post("/login", h(async (req, res) => {
  const body = req.body ?? {};
  const username = toStr(body.username).trim();
  const password = toStr(body.password).trim();
  const role = toStr(body.role).trim().toLowerCase();
  if (!username || !password) {
    return err(res, "invalid_credentials", "Usuario o contraseña vacíos", 400);
  }

  const tryStaff = role === "" || role === "staff" || role === "user";

  // ---- Super admin (global, sin empresa) ----
  if (tryStaff) {
    const su = await one(
      `SELECT * FROM users WHERE is_super AND status=1
       AND (LOWER(username)=LOWER($1) OR LOWER(email)=LOWER($1)) LIMIT 1`,
      [username],
    );
    if (su && (await verifyPassword(password, su.password_hash, su.password_algo))) {
      await upgradeHash("users", su.id, password, su.password_algo);
      const tokens = await issueTokens("user", Number(su.id), null, {
        stock_id: 0,
        kind: Number(su.kind ?? 0),
        is_super: true,
      });
      return ok(res, { role: "staff", is_super: true, user: userToArray(req, su), tokens });
    }
  }

  const company = await resolveCompany(req, res);
  if (!company) return;

  // ---- Staff de empresa ----
  if (tryStaff) {
    const u = await one(
      `SELECT * FROM users WHERE company_id=$1 AND status=1 AND NOT is_super
       AND (LOWER(username)=LOWER($2) OR LOWER(email)=LOWER($2)) LIMIT 1`,
      [company.id, username],
    );
    if (u && (await verifyPassword(password, u.password_hash, u.password_algo))) {
      await upgradeHash("users", u.id, password, u.password_algo);
      const tokens = await issueTokens("user", Number(u.id), company.id, {
        stock_id: Number(u.stock_id ?? 0),
        kind: Number(u.kind ?? 0),
      });
      return ok(res, { role: "staff", user: userToArray(req, u), tokens });
    }
    if (role === "staff" || role === "user") {
      return err(res, "invalid_credentials", "Credenciales inválidas", 401);
    }
  }

  // ---- Cliente (username o variantes de teléfono) ----
  if (role === "" || role === "client") {
    const variants = [...new Set([...phoneVariants(username), username])];
    const p = await one(
      `SELECT * FROM persons WHERE company_id=$1
       AND (username = ANY($2::text[]) OR phone_normalized = ANY($2::text[]))
       LIMIT 1`,
      [company.id, variants],
    );
    if (p) {
      let passOk = await verifyPassword(password, p.password_hash, p.password_algo);
      if (passOk) {
        await upgradeHash("persons", p.id, password, p.password_algo);
      } else if (
        toStr(p.password_hash) === "" &&
        !p.is_guest &&
        p.phone_normalized !== "" &&
        phoneVariants(password).includes(p.phone_normalized)
      ) {
        // Compat legado: SOLO cuentas migradas sin contraseña pueden entrar con su teléfono.
        // No se fija el teléfono como contraseña; el cliente deberá definir una.
        passOk = true;
      }
      if (passOk) {
        const tokens = await issueTokens("client", Number(p.id), company.id, {
          stock_id: Number(p.stock_id ?? 0),
        });
        return ok(res, { role: "client", user: personToArray(req, p), tokens });
      }
    }
  }

  return err(res, "invalid_credentials", "Credenciales inválidas", 401);
}));

// POST /auth/register  (clientes)
authRouter.post("/register", h(async (req, res) => {
  const company = await resolveCompany(req, res);
  if (!company) return;

  const body = req.body ?? {};
  const name = toStr(body.name).trim();
  const lastname = toStr(body.lastname).trim();
  const phone = toStr(body.phone).trim();
  const email = toStr(body.email).trim();
  const password = toStr(body.password).trim();
  const passport = toStr(body.passport).trim();
  const license = toStr(body.license).trim();

  if (!name || !phone || !password) {
    return err(res, "invalid_request", "Nombre, teléfono y contraseña son requeridos", 400);
  }
  if (password.length < 6) {
    return err(res, "invalid_request", "La contraseña debe tener al menos 6 caracteres", 400);
  }
  if (email && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
    return err(res, "invalid_request", "Email inválido", 400);
  }

  const normalized = normalizePhone(phone);
  const variants = [...new Set([...phoneVariants(phone), phone])];
  const dupPhone = await one(
    `SELECT id FROM persons WHERE company_id=$1
     AND (username = ANY($2::text[]) OR phone_normalized = ANY($2::text[])) LIMIT 1`,
    [company.id, variants],
  );
  if (dupPhone) return err(res, "conflict", "Ya existe una cuenta con ese teléfono", 409);
  if (email) {
    const dupEmail = await one(
      "SELECT id FROM persons WHERE company_id=$1 AND LOWER(email)=LOWER($2) LIMIT 1",
      [company.id, email],
    );
    if (dupEmail) return err(res, "conflict", "Ya existe una cuenta con ese email", 409);
  }

  const fullName = lastname ? `${name} ${lastname}`.trim() : name;
  const hash = await bcrypt.hash(password, 10);
  const p = await one(
    `INSERT INTO persons (company_id, name, email, phone, phone_normalized, username, password_hash,
                          passport, license, language)
     VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,'ES') RETURNING *`,
    [company.id, fullName, email, phone, normalized, phone, hash, passport, license],
  );
  if (!p) return err(res, "server_error", "No se pudo crear la cuenta", 500);

  const tokens = await issueTokens("client", Number(p.id), company.id, { stock_id: 0 });
  return ok(res, { role: "client", user: personToArray(req, p), tokens }, 201);
}));

// POST /auth/guest — sesión de invitado por empresa (para navegar el catálogo)
authRouter.post("/guest", h(async (req, res) => {
  const company = await resolveCompany(req, res);
  if (!company) return;
  let p = await one(
    "SELECT * FROM persons WHERE company_id=$1 AND is_guest LIMIT 1",
    [company.id],
  );
  if (!p) {
    p = await one(
      `INSERT INTO persons (company_id, name, username, is_guest, language)
       VALUES ($1, 'Invitado', '__guest__', TRUE, 'ES') RETURNING *`,
      [company.id],
    );
  }
  if (!p) return err(res, "server_error", "No se pudo crear la sesión de invitado", 500);
  const tokens = await issueTokens("client", Number(p.id), company.id, { stock_id: 0, guest: true });
  return ok(res, { role: "client", user: personToArray(req, p), tokens });
}));

// POST /auth/refresh { refresh_token }
authRouter.post("/refresh", h(async (req, res) => {
  const rt = toStr(req.body?.refresh_token).trim();
  if (!rt) return err(res, "invalid_request", "refresh_token requerido", 400);
  const resu = await consumeRefresh(rt);
  if (!resu) return err(res, "invalid_grant", "Refresh token inválido o expirado", 401);

  const extra: Record<string, unknown> = {};
  if (resu.type === "user") {
    const u = await one("SELECT * FROM users WHERE id=$1 AND status=1", [resu.id]);
    if (!u) return err(res, "invalid_grant", "Cuenta no disponible", 401);
    extra.stock_id = Number(u.stock_id ?? 0);
    extra.kind = Number(u.kind ?? 0);
    if (u.is_super) extra.is_super = true;
  } else {
    const p = await one("SELECT * FROM persons WHERE id=$1", [resu.id]);
    if (!p) return err(res, "invalid_grant", "Cuenta no disponible", 401);
    extra.stock_id = Number(p.stock_id ?? 0);
  }
  const tokens = await issueTokens(resu.type, resu.id, resu.companyId, extra);
  return ok(res, { tokens });
}));

// POST /auth/logout
authRouter.post("/logout", requireAuth(), h(async (req, res) => {
  const a = tryAuth(req)!;
  await revokeAllFor(a.type, a.id);
  return ok(res, { logged_out: true });
}));

authRouter.all("/:action", h(async (req, res) => {
  if (req.method !== "POST") return err(res, "method_not_allowed", "Use POST", 405);
  return err(res, "not_found", `Auth action '${req.params.action}' desconocida`, 404);
}));
