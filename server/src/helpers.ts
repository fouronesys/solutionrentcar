import type { Request, Response } from "express";

// ---------- Response envelope (identical to legacy ApiResponse) ----------

export function ok(res: Response, data: unknown, status = 200): void {
  res.status(status).json({ ok: true, data });
}

export function err(
  res: Response,
  code: string,
  message: string,
  status = 400,
  details?: unknown,
): void {
  const body: Record<string, unknown> = { ok: false, error: { code, message } };
  if (details !== undefined) body.details = details;
  res.status(status).json(body);
}

/** Wraps an async handler so rejections become 500s instead of hanging. */
export function h(
  fn: (req: Request, res: Response) => Promise<void> | void,
): (req: Request, res: Response) => void {
  return (req, res) => {
    Promise.resolve(fn(req, res)).catch((e) => {
      console.error(`[api] ${req.method} ${req.path}:`, e);
      if (!res.headersSent) err(res, "server_error", "Error interno", 500);
    });
  };
}

// ---------- Coercion (legacy PHP casts) ----------

export const toStr = (v: unknown): string => (v === null || v === undefined ? "" : String(v));
export const toInt = (v: unknown): number => {
  const n = parseInt(String(v ?? 0), 10);
  return Number.isFinite(n) ? n : 0;
};
export const toNum = (v: unknown): number => {
  const n = parseFloat(String(v ?? 0));
  return Number.isFinite(n) ? n : 0;
};

/** 'YYYY-MM-DD HH:MM:SS' like the legacy MySQL-backed API returned. */
export function fmtDateTime(v: unknown): string {
  if (!v) return "";
  const d = v instanceof Date ? v : new Date(String(v));
  if (isNaN(d.getTime())) return toStr(v);
  const p = (n: number) => String(n).padStart(2, "0");
  return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())} ${p(d.getHours())}:${p(d.getMinutes())}:${p(d.getSeconds())}`;
}

export function fmtDate(v: unknown): string {
  return fmtDateTime(v).slice(0, 10);
}

export const DATE_RE = /^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}(:\d{2})?)?$/;
export const DATE_ONLY_RE = /^\d{4}-\d{2}-\d{2}$/;

// ---------- URLs ----------

export function publicBase(req: Request): string {
  const proto = (req.headers["x-forwarded-proto"] as string)?.split(",")[0] || req.protocol || "http";
  const host = req.headers.host || "localhost";
  return `${proto}://${host}`;
}

/** Absolute-ize stored image/document paths. Absolute URLs and data URLs pass through. */
export function normalizeUrl(req: Request, v: unknown): string | null {
  const s = toStr(v).trim();
  if (s === "") return null;
  if (/^(https?:)?\/\//i.test(s) || s.startsWith("data:")) return s;
  return `${publicBase(req)}/${s.replace(/^\/+/, "")}`;
}

// ---------- Phones (legacy variants matching) ----------

export function normalizePhone(raw: string): string {
  return toStr(raw).replace(/\D+/g, "");
}

export function phoneVariants(raw: string): string[] {
  const digits = normalizePhone(raw);
  if (digits === "") return [];
  const out = new Set<string>();
  out.add(digits);
  if (digits.length >= 10) {
    const last10 = digits.slice(-10);
    out.add(last10);
    out.add("1" + last10);
  }
  if (digits.length >= 11) out.add(digits.slice(-11));
  if (digits.length === 10) out.add("1" + digits);
  if (digits.length === 11 && digits.startsWith("1")) out.add(digits.slice(1));
  return [...out];
}

// ---------- Serializers (exact legacy payload shapes) ----------

export function carToArray(req: Request, c: any) {
  const images: string[] = Array.isArray(c.images)
    ? c.images.map((x: unknown) => normalizeUrl(req, x)).filter(Boolean)
    : [];
  const image = images[0] ?? normalizeUrl(req, c.image);
  return {
    id: toInt(c.id),
    name: toStr(c.name),
    year: toStr(c.year),
    plate: toStr(c.plate),
    price: toNum(c.price),
    seat: toStr(c.seat),
    kms: toStr(c.kms),
    kms_current: toStr(c.kms_current),
    status: toInt(c.status),
    brand_id: toInt(c.brand_id),
    category_id: toInt(c.category_id),
    transmission_id: toInt(c.transmission_id),
    fuel_id: toInt(c.fuel_id),
    stock_id: toInt(c.stock_id),
    image: image ?? null,
    images: images.length ? images : image ? [image] : [],
  };
}

export function bookingToArray(req: Request, b: any) {
  const signature = normalizeUrl(req, b.signature);
  return {
    id: toInt(b.id),
    code: toStr(b.code),
    status: toInt(b.status),
    person_id: toInt(b.person_id),
    car_id: toInt(b.car_id),
    stock_id: toInt(b.stock_id),
    start_at: fmtDateTime(b.start_at),
    end_at: fmtDateTime(b.end_at),
    place_start: toStr(b.place_start),
    place_end: toStr(b.place_end),
    day: toStr(b.day),
    price: toNum(b.price),
    total: toNum(b.total),
    payment: toNum(b.payment),
    fuel: toStr(b.fuel),
    comment: toStr(b.comment),
    created_at: fmtDateTime(b.created_at),
    signature,
    has_signature: !!signature,
  };
}

export function personToArray(req: Request, p: any) {
  return {
    id: toInt(p.id),
    name: toStr(p.name),
    lastname: toStr(p.lastname),
    email: toStr(p.email),
    phone: toStr(p.phone),
    phone2: toStr(p.phone2),
    username: toStr(p.username),
    address: toStr(p.address),
    address2: toStr(p.address2),
    nationality: toStr(p.nationality),
    passport: toStr(p.passport),
    license: toStr(p.license),
    language: toStr(p.language),
    stock_id: toInt(p.stock_id),
    created_at: fmtDateTime(p.created_at),
    documents: {
      cedula: normalizeUrl(req, p.doc_cedula),
      passport: normalizeUrl(req, p.doc_passport),
      license: normalizeUrl(req, p.doc_license),
      home: normalizeUrl(req, p.doc_home),
    },
  };
}

export function userToArray(req: Request, u: any) {
  return {
    id: toInt(u.id),
    name: toStr(u.name),
    lastname: toStr(u.lastname),
    email: toStr(u.email),
    phone: toStr(u.phone),
    kind: toInt(u.kind),
    stock_id: toInt(u.stock_id),
    image: normalizeUrl(req, u.image),
  };
}

export function paymentToArray(p: any) {
  return {
    id: toInt(p.id),
    booking_id: toInt(p.booking_id),
    person_id: toInt(p.person_id),
    val: toNum(p.val),
    payment_type_id: toInt(p.payment_type_id),
    stock_id: toInt(p.stock_id),
    created_at: fmtDateTime(p.created_at),
  };
}

export function notificationToArray(n: any) {
  return {
    id: toInt(n.id),
    type: toStr(n.type),
    title: toStr(n.title),
    body: toStr(n.body),
    url: toStr(n.url),
    data: n.data ?? null,
    read_at: n.read_at ? fmtDateTime(n.read_at) : null,
    created_at: fmtDateTime(n.created_at),
  };
}

export function companyToArray(req: Request, c: any) {
  return {
    id: toInt(c.id),
    slug: toStr(c.slug),
    name: toStr(c.name),
    logo: normalizeUrl(req, c.logo),
    color_primary: toStr(c.color_primary),
    color_secondary: toStr(c.color_secondary),
    colors: { primary: toStr(c.color_primary), secondary: toStr(c.color_secondary) },
    currency: toStr(c.currency),
    phone: toStr(c.phone),
    email: toStr(c.email),
    address: toStr(c.address),
    active: !!c.active,
    created_at: fmtDateTime(c.created_at),
  };
}

// ---------- Misc ----------

export function pageParams(req: Request, defLimit = 50, maxLimit = 200) {
  const limit = Math.max(1, Math.min(maxLimit, toInt((req.query.limit as string) ?? defLimit)));
  const offset = Math.max(0, toInt((req.query.offset as string) ?? 0));
  return { limit, offset };
}

const B64_TYPES: Record<string, string> = {
  png: "image/png",
  jpg: "image/jpeg",
  jpeg: "image/jpeg",
  webp: "image/webp",
  pdf: "application/pdf",
};

/** Decode a base64 (optionally data-URL) file. Returns null when invalid. */
export function decodeBase64File(
  input: string,
  maxBytes = 8 * 1024 * 1024,
): { buf: Buffer; ext: string; mime: string } | null {
  let ext = "png";
  let data = input.trim();
  const m = data.match(/^data:(image\/(png|jpe?g|webp)|application\/pdf);base64,(.+)$/is);
  if (m) {
    const mime = m[1].toLowerCase();
    ext = mime === "application/pdf" ? "pdf" : mime.split("/")[1].replace("jpeg", "jpg");
    data = m[3];
  }
  data = data.replace(/\s/g, "");
  let buf: Buffer;
  try {
    buf = Buffer.from(data, "base64");
  } catch {
    return null;
  }
  if (buf.length < 100 || buf.length > maxBytes) return null;
  return { buf, ext, mime: B64_TYPES[ext] ?? "application/octet-stream" };
}
