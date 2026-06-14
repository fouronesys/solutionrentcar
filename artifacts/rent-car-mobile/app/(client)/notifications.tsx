import React, { useCallback, useEffect, useState } from "react";
import { FlatList, Pressable, RefreshControl, StyleSheet, Text, View } from "react-native";
import { useFocusEffect, useRouter } from "expo-router";
import { Card } from "@/components/Card";
import { Loading } from "@/components/Loading";
import { EmptyState } from "@/components/EmptyState";
import { Button } from "@/components/Button";
import { api, ApiError } from "@/api/client";
import type { Notification } from "@/api/types";
import { colors } from "@/theme/colors";
import { t } from "@/i18n";
import { dateTime } from "@/utils/format";
import { useNotificationsCtx } from "@/notifications/NotificationsContext";
import { useAuth } from "@/auth/AuthContext";

function LoginPrompt() {
  const router = useRouter();
  return (
    <View style={styles.promptContainer}>
      <Text style={styles.promptIcon}>🔔</Text>
      <Text style={styles.promptTitle}>{t("login.requiredTitle")}</Text>
      <Text style={styles.promptSubtitle}>{t("login.requiredSubtitle")}</Text>
      <Button title={t("login.goToLogin")} onPress={() => router.push("/login/client")} style={{ marginBottom: 10 }} />
      <Pressable onPress={() => router.push("/register/client")}>
        <Text style={styles.registerLink}>
          {t("login.noAccount")} <Text style={styles.registerLinkBold}>{t("login.createAccount")}</Text>
        </Text>
      </Pressable>
    </View>
  );
}

export default function NotificationsScreen() {
  const { role, bootstrapped } = useAuth();
  const [items, setItems] = useState<Notification[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [err, setErr] = useState<string | null>(null);
  const { refreshUnread } = useNotificationsCtx();

  const load = useCallback(async () => {
    if (!role) return;
    setErr(null);
    try {
      const r = await api.get<{ notifications: Notification[] }>("/notifications", { limit: 100 });
      setItems(r.notifications ?? []);
      refreshUnread();
    } catch (e) {
      setErr(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [role, refreshUnread]);

  useEffect(() => {
    if (role) load();
    else setLoading(false);
  }, [role, load]);

  useFocusEffect(useCallback(() => { if (role) load(); }, [role, load]));

  const markOne = async (n: Notification) => {
    if (n.read_at) return;
    try {
      await api.post(`/notifications/${n.id}/read`);
      setItems((prev) => prev.map((x) => (x.id === n.id ? { ...x, read_at: new Date().toISOString() } : x)));
      refreshUnread();
    } catch { /* ignore */ }
  };

  const markAll = async () => {
    try { await api.post("/notifications/read_all"); load(); } catch { /* ignore */ }
  };

  if (!bootstrapped || (role && loading)) return <Loading />;
  if (!role) return <LoginPrompt />;

  return (
    <View style={{ flex: 1, backgroundColor: colors.bg }}>
      {err ? <Text style={styles.err}>{err}</Text> : null}
      <View style={{ padding: 12, paddingBottom: 0, alignItems: "flex-end" }}>
        <Pressable onPress={markAll}>
          <Text style={{ color: colors.primaryDark, fontWeight: "600" }}>{t("notifications.markAllRead")}</Text>
        </Pressable>
      </View>
      <FlatList
        contentContainerStyle={{ padding: 12 }}
        data={items}
        keyExtractor={(n) => String(n.id)}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />
        }
        ListEmptyComponent={<EmptyState title={t("notifications.empty")} />}
        renderItem={({ item }) => (
          <Card onPress={() => markOne(item)}>
            <View style={{ flexDirection: "row" }}>
              {!item.read_at ? <View style={styles.dot} /> : <View style={{ width: 14 }} />}
              <View style={{ flex: 1 }}>
                <Text style={styles.title}>{item.title}</Text>
                <Text style={styles.body}>{item.body}</Text>
                <Text style={styles.date}>{dateTime(item.created_at)}</Text>
              </View>
            </View>
          </Card>
        )}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  dot: { width: 8, height: 8, borderRadius: 4, backgroundColor: colors.danger, marginRight: 6, marginTop: 6 },
  title: { fontWeight: "700", fontSize: 15, color: colors.text },
  body: { color: colors.text, marginTop: 4, fontSize: 13 },
  date: { color: colors.textMuted, marginTop: 6, fontSize: 11 },
  err: { color: colors.danger, padding: 12, textAlign: "center" },
  promptContainer: { flex: 1, alignItems: "center", justifyContent: "center", padding: 32 },
  promptIcon: { fontSize: 48, marginBottom: 16 },
  promptTitle: { fontSize: 20, fontWeight: "700", color: colors.text, textAlign: "center", marginBottom: 8 },
  promptSubtitle: { color: colors.textMuted, fontSize: 14, textAlign: "center", marginBottom: 24 },
  registerLink: { color: colors.textMuted, fontSize: 14, marginTop: 8 },
  registerLinkBold: { color: colors.primaryDark, fontWeight: "700" },
});
