import { Router } from "express";
import bcrypt from "bcryptjs";
import fs from "node:fs";
import path from "node:path";
import { one, q } from "../db.js";
import {
  companyById,
  invalidateCompanyCache,
  requireCompanyStaff,
  requireSuper,
  tryAuth,
} from "../auth.js";
import {
  carToArray,
  companyToArray,
  decodeBase64File,
  err,
  h,
  ok,
  pageParams,
  toInt,
  toNum,
  toStr,
  userToArray,
} from "../helpers.js";
import { CATALOG_KINDS } from "./misc.js";
import { PUBLIC_DIR } from "../storage.js";

export const adminRouter = Router();

const SLUG_RE = /^[a-z0-9][a-z0-9-]{1,48}[a-z0-9]$/;
const COLOR_RE = /^#[0-9a-fA-F]{3,8}$/;

function saveLogo(companyId: number, base64: string): string | null {
  const decoded = decodeBase64File(base64, 2 * 1024 * 1024);
  if (!decoded || decoded.ext === "pdf") return null;
  const dir = path.join(PUBLIC_DIR, "companies", String(companyId), "branding");
  fs.mkdirSync(dir, { recursive: true });
  const filename = `logo_${Date.now()}.${decoded.ext}`;
  fs.writeFileSync(path.join(dir, filename), decoded.buf);
  return `storage/companies/${companyId}/branding/${filename}`;
}

function companyPatch(body: any): { sets: string[]; vals: unknown[] } | { error: string } {
  const sets: string[] = [];
  const vals: unknown[] = [];
  const push = (col: string, v: unknown) => {
    vals.push(v);
    sets.push(`${col}=$${vals.length}`);
  };
  if (body.name !== undefined) {
    const name = toStr(body.name).trim();
    if (!name) return { error: "name no puede estar vacío" };
    push("name", name);
  }
  for (const col of ["color_primary", "color_secondary"] as const) {
    if (body[col] !== undefined) {
      const v = toStr(body[col]).trim();
      if (!COLOR_RE.test(v)) return { error: `${col} debe ser un color hex (#RRGGBB)` };
      push(col, v);
    }
  }
  for (const col of ["currency", "phone", "email", "address"] as const) {
    if (body[col] !== undefined) push(col, toStr(body[col]).trim());
  }
  if (body.active !== undefined) push("active", !!body.active);
  return { sets, vals };
}

// ================= SUPER ADMIN: empresas =================

adminRouter.get("/companies", requireSuper(), h(async (req, res) => {
  const r = await q("SELECT * FROM companies ORDER BY id ASC");
  return ok(res, { companies: r.rows.map((c) => companyToArray(req, c)) });
}));

adminRouter.get("/companies/:id(\\d+)", requireSuper(), h(async (req, res) => {
  const c = await companyById(toInt(req.params.id));
  if (!c) return err(res, "not_found", "Empresa no encontrada", 404);
  return ok(res, { company: companyToArray(req, c) });
}));

// POST /admin/companies { slug, name, color_primary?, color_secondary?, currency?, phone?, email?, address?, logo? (base64) }
adminRouter.post("/companies", requireSuper(), h(async (req, res) => {
  const body = req.body ?? {};
  const slug = toStr(body.slug).trim().toLowerCase();
  const name = toStr(body.name).trim();
  if (!SLUG_RE.test(slug)) {
    return err(res, "invalid_request", "slug inválido (minúsculas, números y guiones, 3-50 caracteres)", 400);
  }
  if (!name) return err(res, "invalid_request", "name requerido", 400);
  const dup = await one("SELECT id FROM companies WHERE LOWER(slug)=$1", [slug]);
  if (dup) return err(res, "conflict", "Ya existe una empresa con ese slug", 409);

  const patch = companyPatch(body);
  if ("error" in patch) return err(res, "invalid_request", patch.error, 400);

  const c = await one(
    `INSERT INTO companies (slug, name, color_primary, color_secondary, currency, phone, email, address)
     VALUES ($1,$2,$3,$4,$5,$6,$7,$8) RETURNING *`,
    [
      slug, name,
      COLOR_RE.test(toStr(body.color_primary)) ? toStr(body.color_primary) : "#fb3b54",
      COLOR_RE.test(toStr(body.color_secondary)) ? toStr(body.color_secondary) : "#111827",
      toStr(body.currency).trim() || "DOP",
      toStr(body.phone).trim(), toStr(body.email).trim(), toStr(body.address).trim(),
    ],
  );
  if (!c) return err(res, "server_error", "No se pudo crear la empresa", 500);

  if (body.logo) {
    const rel = saveLogo(c.id, toStr(body.logo));
    if (rel) await q("UPDATE companies SET logo=$1 WHERE id=$2", [rel, c.id]);
  }

  // Catálogos base para que la empresa arranque
  const defaults: Record<string, string[]> = {
    transmissions: ["Automática", "Manual"],
    fuels: ["Gasolina", "Diésel", "GLP", "Eléctrico", "Híbrido"],
  };
  for (const [kind, names] of Object.entries(defaults)) {
    for (const n of names) {
      await q(
        "INSERT INTO catalog_items (company_id, kind, name) VALUES ($1,$2,$3) ON CONFLICT DO NOTHING",
        [c.id, kind, n],
      );
    }
  }

  invalidateCompanyCache();
  const fresh = await companyById(c.id);
  return ok(res, { company: companyToArray(req, fresh) }, 201);
}));

adminRouter.patch("/companies/:id(\\d+)", requireSuper(), h(async (req, res) => {
  const id = toInt(req.params.id);
  const c = await companyById(id);
  if (!c) return err(res, "not_found", "Empresa no encontrada", 404);
  const patch = companyPatch(req.body ?? {});
  if ("error" in patch) return err(res, "invalid_request", patch.error, 400);
  if (req.body?.logo) {
    const rel = saveLogo(id, toStr(req.body.logo));
    if (!rel) return err(res, "invalid_request", "Logo inválido (imagen ≤ 2MB)", 400);
    patch.vals.push(rel);
    patch.sets.push(`logo=$${patch.vals.length}`);
  }
  if (!patch.sets.length) return err(res, "invalid_request", "Sin campos para actualizar", 400);
  patch.vals.push(id);
  const updated = await one(
    `UPDATE companies SET ${patch.sets.join(",")} WHERE id=$${patch.vals.length} RETURNING *`,
    patch.vals,
  );
  invalidateCompanyCache();
  return ok(res, { company: companyToArray(req, updated) });
}));

// POST /admin/companies/:id/logo { logo: base64 }
adminRouter.post("/companies/:id(\\d+)/logo", requireSuper(), h(async (req, res) => {
  const id = toInt(req.params.id);
  const c = await companyById(id);
  if (!c) return err(res, "not_found", "Empresa no encontrada", 404);
  const rel = saveLogo(id, toStr(req.body?.logo ?? req.body?.file));
  if (!rel) return err(res, "invalid_request", "Logo inválido (imagen ≤ 2MB)", 400);
  const updated = await one("UPDATE companies SET logo=$1 WHERE id=$2 RETURNING *", [rel, id]);
  invalidateCompanyCache();
  return ok(res, { company: companyToArray(req, updated) });
}));

// POST /admin/companies/:id/users — crear staff (kind 1 = admin de empresa)
adminRouter.post("/companies/:id(\\d+)/users", requireSuper(), h(async (req, res) => {
  const companyId = toInt(req.params.id);
  const c = await companyById(companyId);
  if (!c) return err(res, "not_found", "Empresa no encontrada", 404);
  const body = req.body ?? {};
  const username = toStr(body.username).trim();
  const password = toStr(body.password).trim();
  if (!username || password.length < 6) {
    return err(res, "invalid_request", "username y password (≥6) son requeridos", 400);
  }
  const dup = await one(
    "SELECT id FROM users WHERE company_id=$1 AND LOWER(username)=LOWER($2)",
    [companyId, username],
  );
  if (dup) return err(res, "conflict", "Ya existe un usuario con ese nombre en la empresa", 409);
  const hash = await bcrypt.hash(password, 10);
  const u = await one(
    `INSERT INTO users (company_id, username, email, password_hash, name, lastname, phone, kind)
     VALUES ($1,$2,$3,$4,$5,$6,$7,$8) RETURNING *`,
    [
      companyId, username, toStr(body.email).trim(), hash,
      toStr(body.name).trim(), toStr(body.lastname).trim(), toStr(body.phone).trim(),
      toInt(body.kind ?? 1),
    ],
  );
  return ok(res, { user: { ...userToArray(req, u), username } }, 201);
}));

// ================= STAFF DE EMPRESA: flota y catálogo =================

function staffCompanyId(req: any, res: any): number | null {
  const a = tryAuth(req)!;
  if (a.isSuper) {
    const cid = toInt(req.query.company_id ?? req.body?.company_id);
    if (cid > 0) return cid;
    err(res, "invalid_request", "Super admin debe indicar company_id", 400);
    return null;
  }
  return a.companyId;
}

const CAR_FIELDS = [
  "name", "year", "plate", "seat", "kms", "kms_current", "description",
] as const;
const CAR_INT_FIELDS = ["status", "brand_id", "category_id", "transmission_id", "fuel_id", "stock_id"] as const;

function carPatch(body: any): { sets: string[]; vals: unknown[] } {
  const sets: string[] = [];
  const vals: unknown[] = [];
  const push = (col: string, v: unknown) => {
    vals.push(v);
    sets.push(`${col}=$${vals.length}`);
  };
  for (const f of CAR_FIELDS) if (body[f] !== undefined) push(f, toStr(body[f]));
  for (const f of CAR_INT_FIELDS) if (body[f] !== undefined) push(f, toInt(body[f]));
  if (body.price !== undefined) push("price", toNum(body.price));
  return { sets, vals };
}

adminRouter.post("/cars", requireCompanyStaff(), h(async (req, res) => {
  const companyId = staffCompanyId(req, res);
  if (!companyId) return;
  const body = req.body ?? {};
  const name = toStr(body.name).trim();
  if (!name) return err(res, "invalid_request", "name requerido", 400);
  const car = await one(
    `INSERT INTO cars (company_id, name, year, plate, price, seat, kms, kms_current,
       status, brand_id, category_id, transmission_id, fuel_id, stock_id, description)
     VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15) RETURNING *`,
    [
      companyId, name, toStr(body.year), toStr(body.plate), toNum(body.price), toStr(body.seat),
      toStr(body.kms), toStr(body.kms_current), toInt(body.status), toInt(body.brand_id),
      toInt(body.category_id), toInt(body.transmission_id), toInt(body.fuel_id),
      toInt(body.stock_id), toStr(body.description),
    ],
  );
  return ok(res, { car: carToArray(req, car) }, 201);
}));

adminRouter.patch("/cars/:id(\\d+)", requireCompanyStaff(), h(async (req, res) => {
  const companyId = staffCompanyId(req, res);
  if (!companyId) return;
  const id = toInt(req.params.id);
  const { sets, vals } = carPatch(req.body ?? {});
  if (!sets.length) return err(res, "invalid_request", "Sin campos para actualizar", 400);
  vals.push(id, companyId);
  const car = await one(
    `UPDATE cars SET ${sets.join(",")} WHERE id=$${vals.length - 1} AND company_id=$${vals.length} RETURNING *`,
    vals,
  );
  if (!car) return err(res, "not_found", "Vehículo no encontrado", 404);
  return ok(res, { car: carToArray(req, car) });
}));

adminRouter.delete("/cars/:id(\\d+)", requireCompanyStaff(), h(async (req, res) => {
  const companyId = staffCompanyId(req, res);
  if (!companyId) return;
  const id = toInt(req.params.id);
  const active = await one(
    "SELECT id FROM bookings WHERE company_id=$1 AND car_id=$2 AND status IN (0,1,3) LIMIT 1",
    [companyId, id],
  );
  if (active) return err(res, "conflict", "El vehículo tiene reservas activas", 409);
  const car = await one("DELETE FROM cars WHERE id=$1 AND company_id=$2 RETURNING id", [id, companyId]);
  if (!car) return err(res, "not_found", "Vehículo no encontrado", 404);
  return ok(res, { deleted: true });
}));

// POST /admin/cars/:id/images { images: [base64...] } — reemplaza galería
adminRouter.post("/cars/:id(\\d+)/images", requireCompanyStaff(), h(async (req, res) => {
  const companyId = staffCompanyId(req, res);
  if (!companyId) return;
  const id = toInt(req.params.id);
  const car = await one("SELECT id FROM cars WHERE id=$1 AND company_id=$2", [id, companyId]);
  if (!car) return err(res, "not_found", "Vehículo no encontrado", 404);
  const inputs: unknown[] = Array.isArray(req.body?.images) ? req.body.images : [];
  if (!inputs.length) return err(res, "invalid_request", "images (array base64) requerido", 400);
  if (inputs.length > 12) return err(res, "invalid_request", "Máximo 12 imágenes", 400);

  const dir = path.join(PUBLIC_DIR, "companies", String(companyId), "cars", String(id));
  fs.mkdirSync(dir, { recursive: true });
  const rels: string[] = [];
  for (const [i, input] of inputs.entries()) {
    const s = toStr(input);
    // Permitir URLs ya existentes (mantener imágenes remotas migradas)
    if (/^https?:\/\//i.test(s)) {
      rels.push(s);
      continue;
    }
    const decoded = decodeBase64File(s, 5 * 1024 * 1024);
    if (!decoded || decoded.ext === "pdf") {
      return err(res, "invalid_request", `Imagen ${i + 1} inválida (imagen ≤ 5MB)`, 400);
    }
    const filename = `car_${id}_${Date.now()}_${i}.${decoded.ext}`;
    fs.writeFileSync(path.join(dir, filename), decoded.buf);
    rels.push(`storage/companies/${companyId}/cars/${id}/${filename}`);
  }
  const updated = await one(
    "UPDATE cars SET images=$1::jsonb, image=$2 WHERE id=$3 AND company_id=$4 RETURNING *",
    [JSON.stringify(rels), rels[0] ?? null, id, companyId],
  );
  return ok(res, { car: carToArray(req, updated) });
}));

// Catálogos por empresa
adminRouter.get("/catalog/:kind", requireCompanyStaff(), h(async (req, res) => {
  const companyId = staffCompanyId(req, res);
  if (!companyId) return;
  const kind = toStr(req.params.kind).toLowerCase();
  if (!(CATALOG_KINDS as readonly string[]).includes(kind)) {
    return err(res, "not_found", `Catálogo '${kind}' no encontrado`, 404);
  }
  const r = await q(
    "SELECT id, name FROM catalog_items WHERE company_id=$1 AND kind=$2 ORDER BY name ASC",
    [companyId, kind],
  );
  return ok(res, { [kind]: r.rows.map((x) => ({ id: toInt(x.id), name: toStr(x.name) })) });
}));

adminRouter.post("/catalog/:kind", requireCompanyStaff(), h(async (req, res) => {
  const companyId = staffCompanyId(req, res);
  if (!companyId) return;
  const kind = toStr(req.params.kind).toLowerCase();
  if (!(CATALOG_KINDS as readonly string[]).includes(kind)) {
    return err(res, "not_found", `Catálogo '${kind}' no encontrado`, 404);
  }
  const name = toStr(req.body?.name).trim();
  if (!name) return err(res, "invalid_request", "name requerido", 400);
  const item = await one(
    `INSERT INTO catalog_items (company_id, kind, name) VALUES ($1,$2,$3)
     ON CONFLICT (company_id, kind, name) DO UPDATE SET name=EXCLUDED.name RETURNING *`,
    [companyId, kind, name],
  );
  return ok(res, { item: { id: toInt(item!.id), name: toStr(item!.name) } }, 201);
}));

adminRouter.patch("/catalog/:kind/:id(\\d+)", requireCompanyStaff(), h(async (req, res) => {
  const companyId = staffCompanyId(req, res);
  if (!companyId) return;
  const kind = toStr(req.params.kind).toLowerCase();
  const name = toStr(req.body?.name).trim();
  if (!name) return err(res, "invalid_request", "name requerido", 400);
  const item = await one(
    "UPDATE catalog_items SET name=$1 WHERE id=$2 AND company_id=$3 AND kind=$4 RETURNING *",
    [name, toInt(req.params.id), companyId, kind],
  );
  if (!item) return err(res, "not_found", "Elemento no encontrado", 404);
  return ok(res, { item: { id: toInt(item.id), name: toStr(item.name) } });
}));

adminRouter.delete("/catalog/:kind/:id(\\d+)", requireCompanyStaff(), h(async (req, res) => {
  const companyId = staffCompanyId(req, res);
  if (!companyId) return;
  const kind = toStr(req.params.kind).toLowerCase();
  const item = await one(
    "DELETE FROM catalog_items WHERE id=$1 AND company_id=$2 AND kind=$3 RETURNING id",
    [toInt(req.params.id), companyId, kind],
  );
  if (!item) return err(res, "not_found", "Elemento no encontrado", 404);
  return ok(res, { deleted: true });
}));

adminRouter.all("*", (req, res) => err(res, "not_found", "Endpoint admin no encontrado", 404));
