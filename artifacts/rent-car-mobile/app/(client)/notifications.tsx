import React, { useCallback, useEffect, useState } from "react";
import {
  FlatList,
  Pressable,
  RefreshControl,
  StyleSheet,
  Text,
  View,
} from "react-native";
import { useFocusEffect, useRouter } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { Loading } from "@/components/Loading";
import { EmptyState } from "@/components/EmptyState";
import { Button } from "@/components/Button";
import { api, ApiError } from "@/api/client";
import type { Notification } from "@/api/types";
import { colors, radius, shadow, spacing } from "@/theme/colors";
import { t } from "@/i18n";
import { dateTime } from "@/utils/format";
import { useNotificationsCtx } from "@/notifications/NotificationsContext";
import { useAuth } from "@/auth/AuthContext";

function LoginPrompt() {
  const router = useRouter();
  return (
    <View style={styles.prompt}>
      <View style={styles.promptIcon}><Text style={{ fontSize: 48 }}>🔔</Text></View>
      <Text style={styles.promptTitle}>{t("login.requiredTitle")}</Text>
      <Text style={styles.promptSub}>{t("login.requiredSubtitle")}</Text>
      <Button title={t("login.goToLogin")} onPress={() => router.push("/login/client")} size="lg" style={{ marginBottom: 12 }} />
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

  const unread = items.filter((n) => !n.read_at).length;

  if (!bootstrapped || (role && loading)) return <Loading />;

  if (!role) {
    return (
      <SafeAreaView style={styles.screen} edges={["top"]}>
        <View style={styles.pageHeader}>
          <Text style={styles.pageTitle}>{t("notifications.title")}</Text>
        </View>
        <LoginPrompt />
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <View style={styles.pageHeader}>
        <Text style={styles.pageTitle}>{t("notifications.title")}</Text>
        <Pressable onPress={markAll} style={styles.markAllBtn}>
          <Text style={styles.markAllText}>{t("notifications.markAllRead")}</Text>
        </Pressable>
      </View>
      {unread > 0 ? (
        <View style={styles.unreadBanner}>
          <Text style={styles.unreadText}>🔔  {unread} {t("notifications.unread")}</Text>
        </View>
      ) : null}
      {err ? <View style={styles.errBox}><Text style={styles.errText}>⚠️  {err}</Text></View> : null}
      <FlatList
        contentContainerStyle={styles.list}
        data={items}
        keyExtractor={(n) => String(n.id)}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => { setRefreshing(true); load(); }}
            tintColor={colors.primaryDark}
          />
        }
        ListEmptyComponent={<EmptyState title={t("notifications.empty")} icon="🔔" />}
        renderItem={({ item }) => (
          <Pressable
            onPress={() => markOne(item)}
            style={({ pressed }) => [styles.notifCard, !item.read_at && styles.unreadCard, pressed && { opacity: 0.88 }]}
          >
            {!item.read_at ? <View style={styles.unreadDot} /> : null}
            <View style={styles.notifContent}>
              <Text style={[styles.notifTitle, !item.read_at && { color: colors.text }]}>{item.title}</Text>
              <Text style={styles.notifBody}>{item.body}</Text>
              <Text style={styles.notifDate}>{dateTime(item.created_at)}</Text>
            </View>
          </Pressable>
        )}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  pageHeader: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: spacing.lg,
    paddingTop: spacing.md,
    paddingBottom: spacing.lg,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
    backgroundColor: colors.card,
  },
  pageTitle: { flex: 1, fontSize: 20, fontWeight: "800", color: colors.text },
  markAllBtn: { paddingVertical: 6, paddingHorizontal: 10, borderRadius: radius.md, backgroundColor: colors.borderLight },
  markAllText: { fontSize: 12, color: colors.primaryDark, fontWeight: "700" },
  unreadBanner: {
    backgroundColor: colors.primaryXLight,
    paddingVertical: 8,
    paddingHorizontal: spacing.lg,
    borderBottomWidth: 1,
    borderBottomColor: colors.primaryLight,
  },
  unreadText: { fontSize: 13, color: colors.primaryDark, fontWeight: "600" },
  errBox: { margin: spacing.lg, padding: 12, backgroundColor: colors.dangerBg, borderRadius: radius.md },
  errText: { color: colors.danger, fontSize: 13 },
  list: { padding: spacing.lg, paddingTop: spacing.md },

  notifCard: {
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    marginBottom: 8,
    padding: spacing.lg,
    flexDirection: "row",
    alignItems: "flex-start",
    ...shadow.sm,
  },
  unreadCard: {
    backgroundColor: colors.primaryXLight,
    borderLeftWidth: 3,
    borderLeftColor: colors.primary,
  },
  unreadDot: {
    width: 8, height: 8, borderRadius: 4,
    backgroundColor: colors.primaryDark,
    marginTop: 6, marginRight: 12, flexShrink: 0,
  },
  notifContent: { flex: 1 },
  notifTitle: { fontSize: 15, fontWeight: "700", color: colors.textSecondary, marginBottom: 4 },
  notifBody: { fontSize: 14, color: colors.textSecondary, lineHeight: 20 },
  notifDate: { fontSize: 11, color: colors.textMuted, marginTop: 6, fontWeight: "500" },

  prompt: { flex: 1, alignItems: "center", justifyContent: "center", padding: spacing.xxl },
  promptIcon: {
    width: 88, height: 88, borderRadius: 44,
    backgroundColor: colors.primaryXLight,
    alignItems: "center", justifyContent: "center", marginBottom: 20,
  },
  promptTitle: { fontSize: 20, fontWeight: "800", color: colors.text, textAlign: "center", marginBottom: 8 },
  promptSub: { color: colors.textMuted, fontSize: 14, textAlign: "center", marginBottom: 28, lineHeight: 20 },
  registerLink: { color: colors.textMuted, fontSize: 14 },
  registerLinkBold: { color: colors.primaryDark, fontWeight: "700" },
});
