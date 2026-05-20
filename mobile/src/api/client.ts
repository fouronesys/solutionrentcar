import axios, { AxiosError, AxiosInstance, AxiosRequestConfig } from "axios";
import Constants from "expo-constants";
import { getTokens, saveTokens, clearTokens } from "@/auth/storage";
import type { ApiResponse, Tokens } from "./types";

function resolveBaseUrl(): string {
  const fromEnv = process.env.EXPO_PUBLIC_API_BASE_URL;
  if (fromEnv) return fromEnv.replace(/\/+$/, "");
  const fromExtra = (Constants.expoConfig?.extra as { apiBaseUrl?: string } | undefined)?.apiBaseUrl;
  if (fromExtra) return fromExtra.replace(/\/+$/, "");
  return "http://localhost:5000/CF-SYSTEMS/api/v1";
}

export const API_BASE = resolveBaseUrl();
console.log("[API] API_BASE =", API_BASE);

const raw: AxiosInstance = axios.create({
  baseURL: API_BASE,
  timeout: 15000,
  headers: { "Content-Type": "application/json", Accept: "application/json" },
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
      if (res.data.ok) {
        await saveTokens(res.data.data.tokens);
        return res.data.data.tokens;
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

raw.interceptors.request.use(async (config) => {
  const tokens = await getTokens();
  if (tokens?.access_token) {
    config.headers = config.headers ?? {};
    (config.headers as Record<string, string>).Authorization = `Bearer ${tokens.access_token}`;
  }
  return config;
});

raw.interceptors.response.use(
  (r) => r,
  async (error: AxiosError) => {
    const original = error.config as (AxiosRequestConfig & { _retried?: boolean }) | undefined;
    const status = error.response?.status;
    if (status === 401 && original && !original._retried && !original.url?.includes("/auth/")) {
      original._retried = true;
      const refreshed = await tryRefresh();
      if (refreshed) {
        original.headers = original.headers ?? {};
        (original.headers as Record<string, string>).Authorization = `Bearer ${refreshed.access_token}`;
        return raw.request(original);
      }
      await clearTokens();
    }
    return Promise.reject(error);
  },
);

export class ApiError extends Error {
  code: string;
  status: number;
  constructor(code: string, message: string, status = 0) {
    super(message);
    this.code = code;
    this.status = status;
  }
}

async function call<T>(
  method: "get" | "post" | "put" | "patch" | "delete",
  path: string,
  body?: unknown,
  params?: Record<string, unknown>,
): Promise<T> {
  try {
    const res = await raw.request<ApiResponse<T>>({ method, url: path, data: body, params });
    const payload = res.data;
    if (payload.ok) return payload.data;
    throw new ApiError(payload.error.code, payload.error.message, res.status);
  } catch (e) {
    if (e instanceof ApiError) throw e;
    const ax = e as AxiosError<ApiResponse<unknown>>;
    if (ax.response?.data && typeof ax.response.data === "object" && "error" in ax.response.data) {
      const err = (ax.response.data as ApiResponse<unknown> & { ok: false }).error;
      throw new ApiError(err.code, err.message, ax.response.status);
    }
    throw new ApiError("network_error", ax.message || "Network error", ax.response?.status ?? 0);
  }
}

export const api = {
  get: <T,>(p: string, params?: Record<string, unknown>) => call<T>("get", p, undefined, params),
  post: <T,>(p: string, body?: unknown) => call<T>("post", p, body),
  put: <T,>(p: string, body?: unknown) => call<T>("put", p, body),
  patch: <T,>(p: string, body?: unknown) => call<T>("patch", p, body),
  del: <T,>(p: string, body?: unknown) => call<T>("delete", p, body),
};
