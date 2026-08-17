import { Router } from "express";
import fs from "node:fs";
import path from "node:path";
import { one } from "../db.js";
import { bearerToken, liveStaff } from "../auth.js";
import { verifyAccess } from "../jwt.js";
import { err, h, toInt, toStr } from "../helpers.js";
import { PRIVATE_DIR } from "../storage.js";

/**
 * Sirve archivos PRIVADOS (documentos de identidad y firmas) con autenticación.
 * Acepta el JWT por header Authorization o por ?token= (para componentes <Image>).
 * URL: /files/companies/:cid/(docs|firmas)/:filename
 */
export const filesRouter = Router();

const FILENAME_RE = /^[A-Za-z0-9._-]+$/;

filesRouter.get("/companies/:cid(\\d+)/:kind(docs|firmas)/:filename", h(async (req, res) => {
  const cid = toInt(req.params.cid);
  const kind = req.params.kind;
  const filename = toStr(req.params.filename);
  if (!FILENAME_RE.test(filename)) return err(res, "not_found", "Archivo no encontrado", 404);

  const token = bearerToken(req) || toStr(req.query.token).trim();
  const payload = token ? verifyAccess(token) : null;
  if (!payload || !payload.sub) return err(res, "unauthorized", "Token requerido o inválido", 401);

  const authCompany = payload.company_id === null || payload.company_id === undefined ? null : Number(payload.company_id);
  const authId = Number(payload.sub);

  let allowed = false;
  if (payload.typ === "user") {
    // Verificación viva: staff desactivado (o degradado, o de empresa desactivada)
    // pierde acceso a documentos/firmas de inmediato, sin esperar a que expire el JWT.
    const live = await liveStaff(authId);
    if (!live.ok) return err(res, "unauthorized", "La cuenta o la empresa está desactivada", 401);
    allowed = live.isSuper || live.companyId === cid;
  } else if (payload.typ === "client" && authCompany === cid) {
    if (kind === "docs") {
      allowed = filename.startsWith(`client_${authId}_`);
    } else {
      const m = filename.match(/^booking_(\d+)_/);
      if (m) {
        const b = await one(
          "SELECT person_id FROM bookings WHERE id=$1 AND company_id=$2",
          [toInt(m[1]), cid],
        );
        allowed = !!b && toInt(b.person_id) === authId;
      }
    }
  }
  if (!allowed) return err(res, "forbidden", "Acceso denegado", 403);

  const filePath = path.join(PRIVATE_DIR, "companies", String(cid), kind, filename);
  if (!filePath.startsWith(PRIVATE_DIR) || !fs.existsSync(filePath)) {
    return err(res, "not_found", "Archivo no encontrado", 404);
  }
  res.sendFile(filePath);
}));

filesRouter.all("*", (req, res) => err(res, "not_found", "Archivo no encontrado", 404));
