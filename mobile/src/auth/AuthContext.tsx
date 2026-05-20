import React, { createContext, useCallback, useContext, useEffect, useMemo, useState } from "react";
import { api } from "@/api/client";
import {
  clearTokens,
  getProfile,
  getTokens,
  onAuthReset,
  saveProfile,
  saveTokens,
} from "@/auth/storage";
import type { Profile, Role, Tokens } from "@/api/types";

type LoginResult = { role: Role; user: Profile; tokens: Tokens };

type AuthCtx = {
  bootstrapped: boolean;
  role: Role | null;
  user: Profile | null;
  loginStaff: (username: string, password: string) => Promise<void>;
  loginClient: (phone: string, password: string) => Promise<void>;
  registerClient: (data: {
    name: string;
    lastname?: string;
    phone: string;
    email?: string;
    password: string;
  }) => Promise<void>;
  logout: () => Promise<void>;
  refreshMe: () => Promise<void>;
  setUser: (p: Profile) => void;
};

const Ctx = createContext<AuthCtx | null>(null);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [bootstrapped, setBootstrapped] = useState(false);
  const [role, setRole] = useState<Role | null>(null);
  const [user, setUserState] = useState<Profile | null>(null);

  useEffect(() => {
    (async () => {
      const tokens = await getTokens();
      const stored = await getProfile();
      if (tokens && stored) {
        setRole(stored.role);
        setUserState(stored.profile);
      }
      setBootstrapped(true);
    })();
  }, []);

  useEffect(() => {
    // When the API layer wipes tokens (e.g. refresh failure), drop in-memory
    // auth so the route gate immediately bounces the user back to "/".
    return onAuthReset(() => {
      setRole(null);
      setUserState(null);
    });
  }, []);

  const finalize = useCallback(async (res: LoginResult) => {
    await saveTokens(res.tokens);
    await saveProfile(res.role, res.user);
    setRole(res.role);
    setUserState(res.user);
  }, []);

  const loginStaff = useCallback(
    async (username: string, password: string) => {
      const res = await api.post<LoginResult>("/auth/login", { username, password, role: "staff" });
      await finalize(res);
    },
    [finalize],
  );

  const loginClient = useCallback(
    async (phone: string, password: string) => {
      const res = await api.post<LoginResult>("/auth/login", { username: phone, password, role: "client" });
      await finalize(res);
    },
    [finalize],
  );

  const registerClient = useCallback(
    async (data: { name: string; lastname?: string; phone: string; email?: string; password: string }) => {
      const res = await api.post<LoginResult>("/auth/register", data);
      await finalize(res);
    },
    [finalize],
  );

  const logout = useCallback(async () => {
    try {
      await api.post("/auth/logout", {});
    } catch {
      /* ignore */
    }
    await clearTokens();
    setRole(null);
    setUserState(null);
  }, []);

  const refreshMe = useCallback(async () => {
    try {
      const res = await api.get<{ role: Role; user: Profile }>("/me");
      setRole(res.role);
      setUserState(res.user);
      await saveProfile(res.role, res.user);
    } catch {
      /* ignore */
    }
  }, []);

  const setUser = useCallback((p: Profile) => {
    setUserState(p);
    if (role) saveProfile(role, p);
  }, [role]);

  const value = useMemo<AuthCtx>(
    () => ({ bootstrapped, role, user, loginStaff, loginClient, registerClient, logout, refreshMe, setUser }),
    [bootstrapped, role, user, loginStaff, loginClient, registerClient, logout, refreshMe, setUser],
  );

  return <Ctx.Provider value={value}>{children}</Ctx.Provider>;
}

export function useAuth(): AuthCtx {
  const v = useContext(Ctx);
  if (!v) throw new Error("useAuth must be used within AuthProvider");
  return v;
}
