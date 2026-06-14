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
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import Animated, { useAnimatedStyle, useSharedValue, withTiming } from "react-native-reanimated";
import { EmptyState } from "@/components/EmptyState";
import { RowSkeleton } from "@/components/Skeleton";
import { Button } from "@/components/Button";
import { api, ApiError } from "@/api/client";
import type { Notification } from "@/api/types";
import { colors, font, gradients, radius, shadow, spacing, type } from "@/theme/colors";
import { t } from "@/i18n";
import { dateTime } from "@/utils/format";
import { useNotificationsCtx } from "@/notifications/NotificationsContext";
import { useAuth } from "@/auth/AuthContext";

const AnimatedPressable = Animated.createAnimatedComponent(Pressable);

function notifIcon(eventType?: string): keyof typeof Ionicons.glyphMap {
  const e = (eventType ?? "").toLowerCase();
  if (e.includes("payment") || e.includes("pay")) return "card-outline";
  if (e.includes("deliver")) return "car-outline";
  if (e.includes("return")) return "checkmark-done-outline";
  if (e.includes("cancel")) return "close-circle-outline";
  if (e.includes("booking") || e.includes("reserv")) return "calendar-outline";
  return "notifications-outline";
}

function Hero({
  unread,
  showMarkAll,
  onMarkAll,
}: {
  unread: number;
  showMarkAll: boolean;
  onMarkAll: () => void;
}) {
  return (
    <LinearGradient colors={gradients.hero} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={styles.hero}>
      <View style={styles.heroBrandRow}>
        <View style={styles.heroLogo}>
          <Ionicons name="car-sport" size={20} color={colors.dark} />
        </View>
        <Text style={styles.heroBrandLabel}>SOLUTION RENT CAR</Text>
      </View>
      <View style={styles.heroTitleRow}>
        <Text style={styles.heroTitle}>{t("notifications.title")}</Text>
        {showMarkAll ? (
          <Pressable onPress={onMarkAll} style={styles.markAllBtn} hitSlop={6}>
            <Ionicons name="checkmark-done" size={15} color={colors.primaryLight} />
            <Text style={styles.markAllText}>{t("notifications.markAllRead")}</Text>
          </Pressable>
        ) : null}
      </View>
      {unread > 0 ? (
        <View style={styles.unreadChip}>
          <View style={styles.unreadChipDot} />
          <Text style={styles.unreadChipText}>
            {unread} {t("notifications.unread")}
          </Text>
        </View>
      ) : null}
    </LinearGradient>
  );
}

function LoginPrompt() {
  const router = useRouter();
  return (
    <View style={styles.prompt}>
      <View style={styles.promptIcon}>
        <Ionicons name="notifications-outline" size={38} color={colors.primaryDark} />
      </View>
      <Text style={styles.promptTitle}>{t("login.requiredTitle")}</Text>
      <Text style={styles.promptSub}>{t("login.requiredSubtitle")}</Text>
      <Button title={t("login.goToLogin")} onPress={() => router.push("/login/client")} size="lg" style={{ alignSelf: "stretch", marginBottom: 12 }} />
      <Pressable onPress={() => router.push("/register/client")}>
        <Text style={styles.registerLink}>
          {t("login.noAccount")} <Text style={styles.registerLinkBold}>{t("login.createAccount")}</Text>
        </Text>
      </Pressable>
    </View>
  );
}

function NotifCard({ item, onPress }: { item: Notification; onPress: () => void }) {
  const unread = !item.read_at;
  const scale = useSharedValue(1);
  const animStyle = useAnimatedStyle(() => ({ transform: [{ scale: scale.value }] }));
  return (
    <AnimatedPressable
      onPress={onPress}
      onPressIn={() => { scale.value = withTiming(0.985, { duration: 90 }); }}
      onPressOut={() => { scale.value = withTiming(1, { duration: 140 }); }}
      style={[animStyle, styles.notifCard, unread && styles.unreadCard]}
    >
      <View style={[styles.notifIconWrap, unread && styles.notifIconWrapUnread]}>
        <Ionicons
          name={notifIcon(item.event_type)}
          size={20}
          color={unread ? colors.primaryDark : colors.textMuted}
        />
      </View>
      <View style={styles.notifContent}>
        <View style={styles.notifTitleRow}>
          <Text style={[styles.notifTitle, unread && styles.notifTitleUnread]} numberOfLines={1}>
            {item.title}
          </Text>
          {unread ? <View style={styles.unreadDot} /> : null}
        </View>
        <Text style={styles.notifBody} numberOfLines={3}>{item.body}</Text>
        <Text style={styles.notifDate}>{dateTime(item.created_at)}</Text>
      </View>
    </AnimatedPressable>
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

  if (!role) {
    return (
      <SafeAreaView style={styles.screen} edges={["top"]}>
        <Hero unread={0} showMarkAll={false} onMarkAll={markAll} />
        <LoginPrompt />
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <Hero unread={unread} showMarkAll={unread > 0} onMarkAll={markAll} />

      {err ? (
        <View style={styles.errBox}>
          <Ionicons name="warning-outline" size={16} color={colors.danger} />
          <Text style={styles.errText}>{err}</Text>
        </View>
      ) : null}

      {!bootstrapped || loading ? (
        <View style={styles.skeletonWrap}>
          {Array.from({ length: 5 }).map((_, i) => (
            <RowSkeleton key={i} />
          ))}
        </View>
      ) : (
        <FlatList
          contentContainerStyle={styles.list}
          data={items}
          keyExtractor={(n) => String(n.id)}
          showsVerticalScrollIndicator={false}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={() => { setRefreshing(true); load(); }}
              tintColor={colors.primaryDark}
            />
          }
          ListEmptyComponent={<EmptyState title={t("notifications.empty")} icon="notifications" />}
          renderItem={({ item }) => <NotifCard item={item} onPress={() => markOne(item)} />}
        />
      )}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },

  hero: {
    paddingTop: 20,
    paddingBottom: 22,
    paddingHorizontal: spacing.xl,
    borderBottomLeftRadius: radius.xxl,
    borderBottomRightRadius: radius.xxl,
  },
  heroBrandRow: { flexDirection: "row", alignItems: "center", gap: 8, marginBottom: 18 },
  heroLogo: {
    width: 30,
    height: 30,
    borderRadius: radius.sm,
    backgroundColor: colors.primary,
    alignItems: "center",
    justifyContent: "center",
  },
  heroBrandLabel: { ...type.label, color: "rgba(255,255,255,0.65)" },
  heroTitleRow: { flexDirection: "row", alignItems: "center", justifyContent: "space-between", gap: 12 },
  heroTitle: { ...type.display, color: "#FFFFFF", flexShrink: 1 },
  markAllBtn: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: radius.full,
    backgroundColor: "rgba(245,158,11,0.16)",
  },
  markAllText: { ...type.small, color: colors.primaryLight },
  unreadChip: {
    flexDirection: "row",
    alignItems: "center",
    gap: 7,
    alignSelf: "flex-start",
    marginTop: 14,
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: radius.full,
    backgroundColor: "rgba(255,255,255,0.08)",
  },
  unreadChipDot: { width: 7, height: 7, borderRadius: 4, backgroundColor: colors.primaryLight },
  unreadChipText: { ...type.captionMed, color: "rgba(255,255,255,0.85)" },

  errBox: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    marginHorizontal: spacing.lg,
    marginTop: spacing.lg,
    padding: 14,
    backgroundColor: colors.dangerBg,
    borderRadius: radius.md,
  },
  errText: { ...type.caption, color: colors.danger, flex: 1 },

  skeletonWrap: { paddingTop: spacing.lg },
  list: { padding: spacing.lg, paddingTop: spacing.lg, flexGrow: 1 },

  notifCard: {
    flexDirection: "row",
    alignItems: "flex-start",
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    padding: spacing.lg,
    marginBottom: spacing.md,
    ...shadow.sm,
  },
  unreadCard: {
    borderLeftWidth: 3,
    borderLeftColor: colors.primary,
    backgroundColor: colors.bgWarm,
  },
  notifIconWrap: {
    width: 44,
    height: 44,
    borderRadius: radius.md,
    backgroundColor: colors.borderLight,
    alignItems: "center",
    justifyContent: "center",
    marginRight: 14,
  },
  notifIconWrapUnread: { backgroundColor: colors.primaryXLight },
  notifContent: { flex: 1 },
  notifTitleRow: { flexDirection: "row", alignItems: "center", gap: 8 },
  notifTitle: { ...type.title, color: colors.textSecondary, flex: 1 },
  notifTitleUnread: { color: colors.text },
  unreadDot: { width: 8, height: 8, borderRadius: 4, backgroundColor: colors.primaryDark, flexShrink: 0 },
  notifBody: { ...type.callout, color: colors.textSecondary, marginTop: 4 },
  notifDate: { ...type.small, color: colors.textMuted, marginTop: 8 },

  prompt: { flex: 1, alignItems: "center", justifyContent: "center", padding: spacing.xxl },
  promptIcon: {
    width: 84, height: 84, borderRadius: radius.full,
    backgroundColor: colors.primaryXLight,
    alignItems: "center", justifyContent: "center", marginBottom: 20,
    borderWidth: 1, borderColor: colors.primaryLight,
  },
  promptTitle: { ...type.h2, color: colors.text, textAlign: "center", marginBottom: 8 },
  promptSub: { ...type.callout, color: colors.textMuted, textAlign: "center", marginBottom: 28 },
  registerLink: { ...type.callout, color: colors.textMuted },
  registerLinkBold: { color: colors.primaryDark, fontFamily: font.bold },
});
