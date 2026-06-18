import axios, { AxiosError, AxiosInstance, AxiosRequestConfig } from "axios";
import { Platform } from "react-native";
import Constants from "expo-constants";
import { getTokens, saveTokens, clearTokens, getGuestTokens, saveGuestTokens } from "@/auth/storage";
import { GUEST_USERNAME, GUEST_PASSWORD, guestEnabled } from "@/auth/guest";
import type { ApiResponse, Tokens } from "./types";

function resolveBaseUrl(): string {
  const fromEnv = process.env.EXPO_PUBLIC_API_BASE_URL;
  if (fromEnv) return fromEnv.replace(/\/+$/, "");
  const fromExtra = (Constants.expoConfig?.extra as { apiBaseUrl?: string } | undefined)?.apiBaseUrl;
  if (fromExtra) return fromExtra.replace(/\/+$/, "");
  return "http://localhost:5000/CF-SYSTEMS/api/v1";
}

export const API_BASE = resolveBaseUrl();

const appVersion =
  (Constants.expoConfig?.version as string | undefined) ??
  (Constants.expoConfig?.runtimeVersion as string | undefined) ??
  "1.0.0";

const platformLabel =
  Platform.OS === "ios" ? "iOS" : Platform.OS === "android" ? "Android" : "Web";

// Explicit, identifiable User-Agent. Some shared hosting WAFs (mod_security /
// Hostinger hSecurity / Cloudflare-style rules) reject requests with the
// default "axios/x.y.z" UA as suspected bot traffic and return an HTML 403
// page instead of a JSON response — which is what made the app surface
// "Request failed with status code 403" during Apple review.
const userAgent = `SolutionsRentCar/${appVersion} (${platformLabel}; ${Platform.Version})`;

// Pick an Origin/Referer that matches the API host so WAFs that look for an
// Origin header from the same domain are satisfied.
function deriveOrigin(base: string): string {
  try {
    const u = new URL(base);
    return `${u.protocol}//${u.host}`;
  } catch {
    return "https://solutionsrentcar.do";
  }
}
const originHeader = deriveOrigin(API_BASE);

const raw: AxiosInstance = axios.create({
  baseURL: API_BASE,
  timeout: 15000,
  // Don't throw on non-2xx so we can inspect the body ourselves and map HTML
  // error pages from the WAF to a clean error type instead of axios's raw
  // "Request failed with status code 403" message.
  validateStatus: () => true,
  // React Native fetch/XHR will rewrite some headers (User-Agent on iOS is
  // typically set by the network stack) but the explicit values are still
  // sent when the platform allows it (e.g. Android, web, EAS dev clients).
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
    "X-Requested-With": "XMLHttpRequest",
    "User-Agent": userAgent,
    Origin: originHeader,
    Referer: originHeader + "/",
    "X-App-Version": appVersion,
    "X-App-Platform": platformLabel,
  },
});

let pendingRefresh: Promise<Tokens | null> | null = null;

async function tryRefresh(): Promise<Tokens | null> {
  if (pendingRefresh) return pendingRefresh;
  pendingRefresh = (async () => {
    const t = await getTokens();
    if (!t?.refresh_token) return null;
    try {
      const res = await raw.post<ApiResponse<{ tokens: Tokens }>>("/auth/refresh", {
        refresh_token: t.refresh_token,
      });
      const data = res.data as ApiResponse<{ tokens: Tokens }> | undefined;
      if (data && typeof data === "object" && (data as { ok?: boolean }).ok) {
        const tokens = (data as { ok: true; data: { tokens: Tokens } }).data.tokens;
        await saveTokens(tokens);
        return tokens;
      }
      return null;
    } catch {
      return null;
    } finally {
      pendingRefresh = null;
    }
  })();
  return pendingRefresh;
}

let pendingGuest: Promise<Tokens | null> | null = null;

async function tryGuestLogin(): Promise<Tokens | null> {
  if (!guestEnabled) return null;
  if (pendingGuest) return pendingGuest;
  pendingGuest = (async () => {
    try {
      const res = await raw.post<ApiResponse<{ tokens: Tokens }>>("/auth/login", {
        username: GUEST_USERNAME,
        password: GUEST_PASSWORD,
        role: "client",
      });
      const data = res.data as ApiResponse<{ tokens: Tokens }> | undefined;
      if (data && typeof data === "object" && (data as { ok?: boolean }).ok) {
        const tokens = (data as { ok: true; data: { tokens: Tokens } }).data.tokens;
        await saveGuestTokens(tokens);
        return tokens;
      }
      return null;
    } catch {
      return null;
    } finally {
      pendingGuest = null;
    }
  })();
  return pendingGuest;
}

// Ensures a guest token exists so the catalog is browsable without a real login.
export async function ensureGuestSession(): Promise<boolean> {
  if (!guestEnabled) return false;
  const existing = await getGuestTokens();
  if (existing?.access_token) return true;
  const t = await tryGuestLogin();
  return !!t;
}

raw.interceptors.request.use(async (config) => {
  let access = (await getTokens())?.access_token;
  if (!access) access = (await getGuestTokens())?.access_token;
  if (access) {
    config.headers = config.headers ?? {};
    (config.headers as Record<string, string>).Authorization = `Bearer ${access}`;
  }
  return config;
});

export class ApiError extends Error {
  code: string;
  status: number;
  constructor(code: string, message: string, status = 0) {
    super(message);
    this.code = code;
    this.status = status;
  }
}

function isJsonContentType(ct: string | undefined | null): boolean {
  if (!ct) return false;
  return ct.toLowerCase().includes("application/json");
}

function looksLikeHtml(body: unknown): boolean {
  if (typeof body !== "string") return false;
  const trimmed = body.trim().toLowerCase();
  return trimmed.startsWith("<!doctype") || trimmed.startsWith("<html") || trimmed.startsWith("<");
}

/**
 * Map a non-JSON / WAF error response to a clean ApiError so the UI can
 * display a localized message instead of the raw axios string.
 */
function mapNonJsonError(status: number): ApiError {
  if (status === 0) {
    return new ApiError("network_unreachable", "network_unreachable", 0);
  }
  if (status === 403 || status === 401) {
    return new ApiError("service_blocked", "service_blocked", status);
  }
  if (status === 503 || status === 502 || status === 504) {
    return new ApiError("service_unavailable", "service_unavailable", status);
  }
  return new ApiError("service_unavailable", "service_unavailable", status);
}

async function retryWithToken<T>(
  method: "get" | "post" | "put" | "patch" | "delete",
  path: string,
  body: unknown,
  params: Record<string, unknown> | undefined,
  token: string,
): Promise<T> {
  const retry = await raw.request<ApiResponse<T>>({
    method,
    url: path,
    data: body,
    params,
    headers: { Authorization: `Bearer ${token}` },
  });
  const retryPayload = retry.data as ApiResponse<T> | string;
  if (typeof retryPayload === "object" && retryPayload && "ok" in retryPayload) {
    if (retryPayload.ok) return retryPayload.data;
    throw new ApiError(retryPayload.error.code, retryPayload.error.message, retry.status);
  }
  throw mapNonJsonError(retry.status);
}

async function call<T>(
  method: "get" | "post" | "put" | "patch" | "delete",
  path: string,
  body?: unknown,
  params?: Record<string, unknown>,
): Promise<T> {
  let res;
  try {
    res = await raw.request<ApiResponse<T> | string>({
      method,
      url: path,
      data: body,
      params,
    });
  } catch (e) {
    const ax = e as AxiosError;
    // True network failure (timeout, DNS, no connection, TLS).
    throw new ApiError("network_unreachable", "network_unreachable", ax.response?.status ?? 0);
  }

  const status = res.status;
  const contentType = (res.headers?.["content-type"] ?? res.headers?.["Content-Type"]) as
    | string
    | undefined;
  const payload = res.data;

  // Token refresh on 401 (except for auth endpoints themselves).
  if (status === 401 && !path.includes("/auth/")) {
    const real = await getTokens();
    if (real?.refresh_token) {
      const refreshed = await tryRefresh();
      if (refreshed) {
        return await retryWithToken<T>(method, path, body, params, refreshed.access_token);
      }
      await clearTokens();
    } else {
      // No real session: guest token missing or expired — re-acquire and retry once.
      const g = await tryGuestLogin();
      if (g) {
        return await retryWithToken<T>(method, path, body, params, g.access_token);
      }
    }
  }

  // Proper JSON envelope from the API.
  if (
    typeof payload === "object" &&
    payload !== null &&
    "ok" in (payload as Record<string, unknown>) &&
    isJsonContentType(contentType)
  ) {
    const p = payload as ApiResponse<T>;
    if (p.ok) return p.data;
    throw new ApiError(p.error.code, p.error.message, status);
  }

  // Non-JSON response (WAF HTML page, redirect page, plain text, etc.).
  if (looksLikeHtml(payload) || !isJsonContentType(contentType)) {
    throw mapNonJsonError(status);
  }

  // Defensive fallback: status was 2xx but body wasn't the expected envelope.
  if (status >= 200 && status < 300) {
    return payload as unknown as T;
  }
  throw mapNonJsonError(status);
}

export const api = {
  get: <T,>(p: string, params?: Record<string, unknown>) => call<T>("get", p, undefined, params),
  post: <T,>(p: string, body?: unknown) => call<T>("post", p, body),
  put: <T,>(p: string, body?: unknown) => call<T>("put", p, body),
  patch: <T,>(p: string, body?: unknown) => call<T>("patch", p, body),
  del: <T,>(p: string, body?: unknown) => call<T>("delete", p, body),
};
