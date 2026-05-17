import React, { createContext, useCallback, useContext, useEffect, useMemo, useRef, useState } from "react";
import * as Notifications from "expo-notifications";
import { router } from "expo-router";
import { Platform } from "react-native";
import { api } from "@/api/client";
import { useAuth } from "@/auth/AuthContext";
import { registerForPush, registerTokenWithServer } from "./push";

type Ctx = {
  unread: number;
  refreshUnread: () => Promise<void>;
};

const NCtx = createContext<Ctx | null>(null);

export function NotificationsProvider({ children }: { children: React.ReactNode }) {
  const { role } = useAuth();
  const [unread, setUnread] = useState(0);
  const tokenRef = useRef<string | null>(null);

  const refreshUnread = useCallback(async () => {
    if (!role) return;
    try {
      const r = await api.get<{ count: number }>("/notifications/unread_count");
      setUnread(r.count ?? 0);
    } catch {
      /* ignore */
    }
  }, [role]);

  useEffect(() => {
    if (!role) {
      setUnread(0);
      return;
    }
    refreshUnread();

    let mounted = true;
    (async () => {
      const token = await registerForPush();
      if (!mounted || !token) return;
      tokenRef.current = token;
      await registerTokenWithServer(token);
    })();

    const recv = Notifications.addNotificationReceivedListener(() => {
      refreshUnread();
    });
    const tap = Notifications.addNotificationResponseReceivedListener((resp) => {
      const data = resp.notification.request.content.data as Record<string, unknown> | undefined;
      handleTap(data, role);
      refreshUnread();
    });

    const interval = setInterval(refreshUnread, 60_000);

    if (Platform.OS === "ios") Notifications.setBadgeCountAsync(0).catch(() => {});

    return () => {
      mounted = false;
      recv.remove();
      tap.remove();
      clearInterval(interval);
    };
  }, [role, refreshUnread]);

  const value = useMemo<Ctx>(() => ({ unread, refreshUnread }), [unread, refreshUnread]);
  return <NCtx.Provider value={value}>{children}</NCtx.Provider>;
}

function handleTap(data: Record<string, unknown> | undefined, role: "client" | "staff" | null) {
  if (!data || !role) return;
  const bookingId = Number(data.booking_id ?? 0);
  if (bookingId > 0) {
    const pathname = role === "staff" ? "/(staff)/booking/[id]" : "/(client)/booking/[id]";
    router.push({ pathname, params: { id: String(bookingId) } } as never);
    return;
  }
  router.push(role === "staff" ? "/(staff)/notifications" : "/(client)/notifications");
}

export function useNotificationsCtx() {
  const v = useContext(NCtx);
  if (!v) throw new Error("useNotificationsCtx must be used within NotificationsProvider");
  return v;
}
