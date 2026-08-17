import jwt from "jsonwebtoken";
import crypto from "node:crypto";

const SECRET = process.env.JWT_SECRET || process.env.SESSION_SECRET;
if (!SECRET) throw new Error("JWT_SECRET o SESSION_SECRET requerido");

export type JwtPayload = {
  sub: number;
  typ: "user" | "client";
  company_id: number | null;
  stock_id?: number;
  kind?: number;
  is_super?: boolean;
  [k: string]: unknown;
};

export function signAccess(payload: JwtPayload, ttlSeconds = 3600): string {
  return jwt.sign(payload, SECRET as string, { algorithm: "HS256", expiresIn: ttlSeconds });
}

export function verifyAccess(token: string): JwtPayload | null {
  try {
    return jwt.verify(token, SECRET as string, { algorithms: ["HS256"] }) as unknown as JwtPayload;
  } catch {
    return null;
  }
}

export function randomToken(): string {
  return crypto.randomBytes(48).toString("base64url");
}

export function hashRefresh(raw: string): string {
  return crypto.createHash("sha256").update(raw).digest("hex");
}

/** Legacy CF-SYSTEMS staff password hash: sha1(md5(password)) */
export function legacySha1Md5(password: string): string {
  const md5 = crypto.createHash("md5").update(password).digest("hex");
  return crypto.createHash("sha1").update(md5).digest("hex");
}
