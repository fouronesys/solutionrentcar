import { Router } from "express";
import fs from "node:fs";
import path from "node:path";
import { one, q } from "../db.js";
import { requireAuth, tryAuth } from "../auth.js";
import { decodeBase64File, err, h, normalizePhone, ok, personToArray, publicBase, toStr, userToArray } from "../helpers.js";
import { PRIVATE_DIR } from "../storage.js";

export const meRouter = Router();
meRouter.use(requireAuth());

const STAFF_FIELDS = ["name", "lastname", "phone", "email", "language"] as const;
const CLIENT_FIELDS = [
  "name", "lastname", "phone", "phone2", "email", "address", "address2",
  "nationality", "passport", "license", "language",
] as const;

meRouter.get("/", h(async (req, res) => {
  const a = tryAuth(req)!;
  if (a.type === "user") {
    const u = await one("SELECT * FROM users WHERE id=$1", [a.id]);
    if (!u) return err(res, "not_found", "Usuario no encontrado", 404);
    return ok(res, { role: "staff", user: userToArray(req, u) });
  }
  const p = await one("SELECT * FROM persons WHERE id=$1 AND company_id=$2", [a.id, a.companyId]);
  if (!p) return err(res, "not_found", "Cliente no encontrado", 404);
  return ok(res, { role: "client", user: personToArray(req, p) });
}));

async function updateMe(req: any, res: any): Promise<void> {
  const a = tryAuth(req)!;
  const body = req.body ?? {};
  const allowed = a.type === "user" ? STAFF_FIELDS : CLIENT_FIELDS;
  const sets: string[] = [];
  const vals: unknown[] = [];
  for (const f of allowed) {
    if (body[f] !== undefined) {
      vals.push(toStr(body[f]));
      sets.push(`${f}=$${vals.length}`);
      if (f === "phone" && a.type === "client") {
        vals.push(normalizePhone(toStr(body[f])));
        sets.push(`phone_normalized=$${vals.length}`);
      }
    }
  }
  if (!sets.length) return err(res, "invalid_request", "Sin campos para actualizar", 400);

  if (a.type === "user") {
    vals.push(a.id);
    const u = await one(`UPDATE users SET ${sets.join(",")} WHERE id=$${vals.length} RETURNING *`, vals);
    if (!u) return err(res, "not_found", "Usuario no encontrado", 404);
    return ok(res, { role: "staff", user: userToArray(req, u) });
  }
  vals.push(a.id, a.companyId);
  const p = await one(
    `UPDATE persons SET ${sets.join(",")} WHERE id=$${vals.length - 1} AND company_id=$${vals.length} RETURNING *`,
    vals,
  );
  if (!p) return err(res, "not_found", "Cliente no encontrado", 404);
  return ok(res, { role: "client", user: personToArray(req, p) });
}

meRouter.patch("/", h(updateMe));
meRouter.post("/", h(updateMe));

meRouter.delete("/", h(async (req, res) => {
  const a = tryAuth(req)!;
  if (a.type !== "client") return err(res, "forbidden", "Solo clientes pueden eliminar su cuenta", 403);
  const p = await one("SELECT * FROM persons WHERE id=$1 AND company_id=$2", [a.id, a.companyId]);
  if (!p) return err(res, "not_found", "Cliente no encontrado", 404);
  if (p.is_guest) return err(res, "forbidden", "La cuenta de invitado no puede eliminarse", 403);
  await q("DELETE FROM refresh_tokens WHERE recipient_type='client' AND recipient_id=$1", [a.id]);
  await q("DELETE FROM device_tokens WHERE recipient_type='client' AND recipient_id=$1 AND company_id=$2", [a.id, a.companyId]);
  await q("DELETE FROM persons WHERE id=$1 AND company_id=$2", [a.id, a.companyId]);
  return ok(res, { deleted: true });
}));

const DOC_KINDS: Record<string, string> = {
  cedula: "doc_cedula",
  invoice: "doc_cedula",
  passport: "doc_passport",
  pasaporte: "doc_passport",
  license: "doc_license",
  licencia: "doc_license",
  home: "doc_home",
  domicilio: "doc_home",
};

meRouter.post("/document", h(async (req, res) => {
  const a = tryAuth(req)!;
  if (a.type !== "client") return err(res, "forbidden", "Solo clientes", 403);
  const kindRaw = toStr(req.body?.kind).trim().toLowerCase();
  const field = DOC_KINDS[kindRaw];
  if (!field) return err(res, "invalid_request", "kind inválido", 400);
  const file = toStr(req.body?.file);
  if (!file) return err(res, "invalid_request", "file requerido (base64)", 400);
  const decoded = decodeBase64File(file);
  if (!decoded) return err(res, "invalid_request", "Archivo inválido (100B–8MB, JPG/PNG/WEBP/PDF)", 400);

  const dir = path.join(PRIVATE_DIR, "companies", String(a.companyId), "docs");
  fs.mkdirSync(dir, { recursive: true });
  const filename = `client_${a.id}_${field}_${Date.now()}.${decoded.ext}`;
  fs.writeFileSync(path.join(dir, filename), decoded.buf);
  const rel = `files/companies/${a.companyId}/docs/${filename}`;

  const p = await one(
    `UPDATE persons SET ${field}=$1 WHERE id=$2 AND company_id=$3 RETURNING *`,
    [rel, a.id, a.companyId],
  );
  if (!p) return err(res, "not_found", "Cliente no encontrado", 404);
  const kindKey = field.replace("doc_", "");
  return ok(res, {
    kind: kindKey,
    field,
    url: `${publicBase(req)}/${rel}`,
    user: personToArray(req, p),
  });
}));

meRouter.all("/", (req, res) => err(res, "method_not_allowed", "Método no permitido", 405));
