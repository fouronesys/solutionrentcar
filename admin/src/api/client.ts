// Cliente HTTP del panel: maneja el envelope {ok,data}/{ok:false,error},
// el JWT de acceso y la renovación automática con el refresh token.
import type { Session } from "./types";

const API = "/api/v1";
const STORAGE_KEY = "panel_session";

export class ApiError extends Error {
  code: string;
  status: number;
  constructor(code: string, message: string, status: number) {
    super(message);
    this.code = code;
    this.status = status;
  }
}

let session: Session | null = null;
let onSessionChange: ((s: Session | null) => void) | null = null;

export function loadSession(): Session | null {
  if (session) return session;
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    session = raw ? (JSON.parse(raw) as Session) : null;
  } catch {
    session = null;
  }
  return session;
}

export function setSession(s: Session | null) {
  session = s;
  if (s) localStorage.setItem(STORAGE_KEY, JSON.stringify(s));
  else localStorage.removeItem(STORAGE_KEY);
  onSessionChange?.(s);
}

export function subscribeSession(cb: (s: Session | null) => void) {
  onSessionChange = cb;
}

export function getSession(): Session | null {
  return session ?? loadSession();
}

async function rawRequest<T>(
  path: string,
  opts: { method?: string; body?: unknown; auth?: boolean; company?: string | null } = {},
): Promise<T> {
  const s = getSession();
  const headers: Record<string, string> = { "Content-Type": "application/json" };
  if (opts.auth !== false && s?.accessToken) headers.Authorization = `Bearer ${s.accessToken}`;
  const company = opts.company ?? s?.companySlug;
  if (company) headers["X-Company"] = company;
  const res = await fetch(`${API}${path}`, {
    method: opts.method ?? "GET",
    headers,
    body: opts.body !== undefined ? JSON.stringify(opts.body) : undefined,
  });
  let json: any = null;
  try {
    json = await res.json();
  } catch {
    throw new ApiError("bad_response", `Respuesta inválida del servidor (${res.status})`, res.status);
  }
  if (!json.ok) {
    const e = json.error ?? {};
    throw new ApiError(e.code ?? "error", e.message ?? "Error desconocido", res.status);
  }
  return json.data as T;
}

let refreshing: Promise<boolean> | null = null;

async function tryRefresh(): Promise<boolean> {
  const s = getSession();
  if (!s?.refreshToken) return false;
  refreshing ??= (async () => {
    try {
      const data = await rawRequest<{ tokens: { access_token: string; refresh_token: string } }>(
        "/auth/refresh",
        { method: "POST", body: { refresh_token: s.refreshToken }, auth: false },
      );
      setSession({ ...s, accessToken: data.tokens.access_token, refreshToken: data.tokens.refresh_token });
      return true;
    } catch {
      setSession(null);
      return false;
    } finally {
      refreshing = null;
    }
  })();
  return refreshing;
}

/** Petición con renovación automática del token si expira. */
export async function request<T>(
  path: string,
  opts: { method?: string; body?: unknown; auth?: boolean; company?: string | null } = {},
): Promise<T> {
  try {
    return await rawRequest<T>(path, opts);
  } catch (e) {
    if (e instanceof ApiError && e.status === 401 && opts.auth !== false && getSession()) {
      if (await tryRefresh()) return rawRequest<T>(path, opts);
    }
    throw e;
  }
}

/** Convierte un File a base64 (data URL) para subir logos/fotos. */
export function fileToBase64(file: File): Promise<string> {
  return new Promise((resolve, reject) => {
    const r = new FileReader();
    r.onload = () => resolve(String(r.result));
    r.onerror = () => reject(new Error("No se pudo leer el archivo"));
    r.readAsDataURL(file);
  });
}

/**
 * Los archivos privados (/files: documentos y firmas) requieren auth, pero un
 * <img> no puede mandar el header Authorization; el backend acepta ?token=.
 */
function withFileToken(p: string): string {
  if (!p.startsWith("/files/")) return p;
  const s = getSession();
  if (!s?.accessToken) return p;
  const sep = p.includes("?") ? "&" : "?";
  return `${p}${sep}token=${encodeURIComponent(s.accessToken)}`;
}

/** Normaliza rutas de imágenes que la API devuelve absolutas o relativas. */
export function imageUrl(v: string | null | undefined): string | null {
  if (!v) return null;
  if (/^https?:\/\//i.test(v)) {
    // Las URLs absolutas del propio backend se sirven vía proxy con ruta relativa
    try {
      const u = new URL(v);
      if (u.pathname.startsWith("/storage/") || u.pathname.startsWith("/files/")) {
        return withFileToken(u.pathname + u.search);
      }
    } catch {
      /* ignore */
    }
    return v;
  }
  return withFileToken(v.startsWith("/") ? v : `/${v}`);
}
