import type { NextFunction, Request, Response } from "express";
import { one, q } from "./db.js";
import { err } from "./helpers.js";
import { hashRefresh, randomToken, signAccess, verifyAccess, type JwtPayload } from "./jwt.js";

export type AuthInfo = {
  id: number;
  type: "user" | "client";
  companyId: number | null; // null solo para super admin
  stockId: number;
  kind: number;
  isSuper: boolean;
  payload: JwtPayload;
};

export type CompanyRow = {
  id: number;
  slug: string;
  name: string;
  logo: string | null;
  color_primary: string;
  color_secondary: string;
  currency: string;
  phone: string;
  email: string;
  address: string;
  active: boolean;
  created_at: Date;
};

declare global {
  // eslint-disable-next-line @typescript-eslint/no-namespace
  namespace Express {
    interface Request {
      auth?: AuthInfo | null;
      company?: CompanyRow | null;
    }
  }
}

// ---------- Company resolution ----------

const companyCache = new Map<string, { row: CompanyRow | null; at: number }>();
const CACHE_TTL = 30_000;

export async function companyBySlug(slug: string): Promise<CompanyRow | null> {
  const key = slug.toLowerCase();
  const hit = companyCache.get(key);
  if (hit && Date.now() - hit.at < CACHE_TTL) return hit.row;
  const row = await one<CompanyRow>(
    "SELECT * FROM companies WHERE LOWER(slug) = $1 AND active = TRUE",
    [key],
  );
  companyCache.set(key, { row, at: Date.now() });
  return row;
}

export async function companyById(id: number): Promise<CompanyRow | null> {
  return one<CompanyRow>("SELECT * FROM companies WHERE id = $1", [id]);
}

export function invalidateCompanyCache(): void {
  companyCache.clear();
}

/** Slug pedido por la app: header X-Company, ?company= o body.company_slug. */
export function requestedCompanySlug(req: Request): string {
  const hdr = (req.headers["x-company"] as string) || "";
  const qp = (req.query.company as string) || "";
  const body = (req.body && (req.body.company_slug as string)) || "";
  return (hdr || qp || body || process.env.DEFAULT_COMPANY_SLUG || "").trim();
}

/**
 * Resuelve la empresa del request. Prioridad: token (company_id) > slug pedido.
 * Deja req.company. Responde 400 si no se puede resolver y required=true.
 */
export async function resolveCompany(req: Request, res: Response, required = true): Promise<CompanyRow | null> {
  if (req.company) return req.company;
  if (req.auth && req.auth.companyId) {
    const c = await companyById(req.auth.companyId);
    if (!c || !c.active) {
      if (required) err(res, "forbidden", "Empresa inactiva", 403);
      return null;
    }
    req.company = c;
    return c;
  }
  const slug = requestedCompanySlug(req);
  if (slug) {
    const c = await companyBySlug(slug);
    if (!c) {
      if (required) err(res, "company_not_found", "Empresa no encontrada", 404);
      return null;
    }
    req.company = c;
    return c;
  }
  if (required) {
    err(res, "company_required", "Debes indicar la empresa (header X-Company o ?company=)", 400);
  }
  return null;
}

// ---------- Bearer auth ----------

export function bearerToken(req: Request): string | null {
  const hdr = (req.headers.authorization as string) || "";
  const m = hdr.match(/Bearer\s+(.+)/i);
  return m ? m[1].trim() : null;
}

export function tryAuth(req: Request): AuthInfo | null {
  if (req.auth !== undefined) return req.auth;
  const token = bearerToken(req);
  if (!token) {
    req.auth = null;
    return null;
  }
  const payload = verifyAccess(token);
  if (!payload || !payload.sub || !payload.typ) {
    req.auth = null;
    return null;
  }
  if (payload.typ !== "user" && payload.typ !== "client") {
    req.auth = null;
    return null;
  }
  req.auth = {
    id: Number(payload.sub),
    type: payload.typ,
    companyId: payload.company_id === null || payload.company_id === undefined ? null : Number(payload.company_id),
    stockId: Number(payload.stock_id ?? 0),
    kind: Number(payload.kind ?? 0),
    isSuper: !!payload.is_super,
    payload,
  };
  return req.auth;
}

export function requireAuth(type?: "user" | "client") {
  return (req: Request, res: Response, next: NextFunction) => {
    const a = tryAuth(req);
    if (!a) return err(res, "unauthorized", "Token requerido o inválido", 401);
    if (type && a.type !== type) return err(res, "forbidden", "Acceso restringido", 403);
    next();
  };
}

export function requireSuper() {
  return (req: Request, res: Response, next: NextFunction) => {
    const a = tryAuth(req);
    if (!a) return err(res, "unauthorized", "Token requerido o inválido", 401);
    if (!a.isSuper) return err(res, "forbidden", "Solo super administradores", 403);
    next();
  };
}

/** Staff de empresa (no super): requiere company_id en token. */
export function requireCompanyStaff() {
  return (req: Request, res: Response, next: NextFunction) => {
    const a = tryAuth(req);
    if (!a) return err(res, "unauthorized", "Token requerido o inválido", 401);
    if (a.type !== "user") return err(res, "forbidden", "Solo staff", 403);
    if (!a.isSuper && !a.companyId) return err(res, "forbidden", "Token sin empresa", 403);
    next();
  };
}

// ---------- Token issuing ----------

export async function issueTokens(
  type: "user" | "client",
  id: number,
  companyId: number | null,
  extra: Record<string, unknown> = {},
) {
  const access = signAccess({ sub: id, typ: type, company_id: companyId, ...extra } as JwtPayload, 3600);
  const refreshRaw = randomToken();
  const hash = hashRefresh(refreshRaw);
  const expires = new Date(Date.now() + 30 * 24 * 3600 * 1000);
  await q(
    `INSERT INTO refresh_tokens (company_id, recipient_type, recipient_id, token_hash, expires_at)
     VALUES ($1, $2, $3, $4, $5)`,
    [companyId, type, id, hash, expires],
  );
  return {
    access_token: access,
    refresh_token: refreshRaw,
    token_type: "Bearer",
    expires_in: 3600,
  };
}

export async function consumeRefresh(raw: string): Promise<{ type: "user" | "client"; id: number; companyId: number | null } | null> {
  const hash = hashRefresh(raw);
  const row = await one(
    `UPDATE refresh_tokens SET revoked_at = NOW()
     WHERE token_hash = $1 AND revoked_at IS NULL AND expires_at > NOW()
     RETURNING recipient_type, recipient_id, company_id`,
    [hash],
  );
  if (!row) return null;
  return {
    type: row.recipient_type as "user" | "client",
    id: Number(row.recipient_id),
    companyId: row.company_id === null ? null : Number(row.company_id),
  };
}

export async function revokeAllFor(type: string, id: number): Promise<void> {
  await q(
    "UPDATE refresh_tokens SET revoked_at = NOW() WHERE recipient_type = $1 AND recipient_id = $2 AND revoked_at IS NULL",
    [type, id],
  );
}
