import React, { useCallback, useEffect, useState } from "react";
import {
  FlatList,
  Image,
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
import type { Booking } from "@/api/types";
import { bookingStatus, colors, font, gradients, radius, shadow, spacing, type } from "@/theme/colors";
import { i18n, t } from "@/i18n";
import { money, shortDate } from "@/utils/format";
import { useAuth } from "@/auth/AuthContext";

const AnimatedPressable = Animated.createAnimatedComponent(Pressable);

function Hero({ count }: { count?: number }) {
  return (
    <LinearGradient colors={gradients.hero} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={styles.hero}>
      <View style={styles.heroBrandRow}>
        <View style={styles.heroLogo}>
          <Image source={require("../../assets/images/logo.png")} style={{ width: 32, height: 32 }} resizeMode="contain" />
        </View>
        <Text style={styles.heroBrandLabel}>YOWELL RENT-CAR</Text>
      </View>
      <View style={styles.heroTitleRow}>
        <Text style={styles.heroTitle}>{t("booking.myBookings")}</Text>
        {typeof count === "number" ? (
          <View style={styles.heroCount}>
            <Text style={styles.heroCountText}>{count}</Text>
          </View>
        ) : null}
      </View>
      <Text style={styles.heroSub}>
        {i18n.locale === "en"
          ? "Track and manage your reservations."
          : "Sigue y administra tus reservas."}
      </Text>
    </LinearGradient>
  );
}

function LoginPrompt() {
  const router = useRouter();
  return (
    <View style={styles.prompt}>
      <View style={styles.promptIcon}>
        <Ionicons name="calendar-outline" size={36} color={colors.primaryDark} />
      </View>
      <Text style={styles.promptTitle}>{t("login.requiredTitle")}</Text>
      <Text style={styles.promptSub}>{t("login.requiredSubtitle")}</Text>
      <Button title={t("login.goToLogin")} onPress={() => router.push("/login/client")} style={{ marginBottom: 12, alignSelf: "stretch" }} size="lg" icon="log-in-outline" />
      <Pressable onPress={() => router.push("/register/client")}>
        <Text style={styles.registerLink}>
          {t("login.noAccount")} <Text style={styles.registerLinkBold}>{t("login.createAccount")}</Text>
        </Text>
      </Pressable>
    </View>
  );
}

function BookingCard({ booking, onPress }: { booking: Booking; onPress: () => void }) {
  const s = bookingStatus[Number(booking.status ?? 0)];
  const locale = i18n.locale === "en" ? "en" : "es";
  const total = Number(booking.total ?? 0);
  const paid = Number(booking.payment ?? 0);
  const balance = Math.max(0, total - paid);

  const scale = useSharedValue(1);
  const animStyle = useAnimatedStyle(() => ({ transform: [{ scale: scale.value }] }));

  return (
    <AnimatedPressable
      onPress={onPress}
      onPressIn={() => { scale.value = withTiming(0.985, { duration: 90 }); }}
      onPressOut={() => { scale.value = withTiming(1, { duration: 140 }); }}
      style={[animStyle, styles.card]}
    >
      <View style={styles.cardHeader}>
        <View style={{ flex: 1 }}>
          <Text style={styles.cardCode}>#{booking.code ?? booking.id}</Text>
          <View style={styles.cardDatesRow}>
            <Ionicons name="calendar-outline" size={13} color={colors.textMuted} />
            <Text style={styles.cardDates}>{shortDate(booking.start_at)} → {shortDate(booking.end_at)}</Text>
          </View>
        </View>
        {s ? (
          <View style={[styles.statusPill, { backgroundColor: s.bg }]}>
            <View style={[styles.statusDot, { backgroundColor: s.color }]} />
            <Text style={[styles.statusText, { color: s.color }]}>{s[locale]}</Text>
          </View>
        ) : null}
      </View>
      <View style={styles.cardDivider} />
      <View style={styles.cardFooter}>
        <View style={styles.cardAmount}>
          <Text style={styles.amountLabel}>{t("booking.total")}</Text>
          <Text style={styles.amountValue}>{money(total)}</Text>
        </View>
        {balance > 0 ? (
          <View style={styles.cardAmount}>
            <Text style={styles.amountLabel}>{t("booking.balance")}</Text>
            <Text style={[styles.amountValue, { color: colors.danger }]}>{money(balance)}</Text>
          </View>
        ) : (
          <View style={styles.paidBadge}>
            <Ionicons name="checkmark-circle" size={14} color={colors.success} />
            <Text style={styles.paidText}>{locale === "en" ? "Paid" : "Pagado"}</Text>
          </View>
        )}
        <Ionicons name="chevron-forward" size={20} color={colors.textMuted} style={styles.chevron} />
      </View>
    </AnimatedPressable>
  );
}

export default function BookingsList() {
  const router = useRouter();
  const { role, bootstrapped } = useAuth();
  const [items, setItems] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [err, setErr] = useState<string | null>(null);

  const load = useCallback(async () => {
    if (!role) return;
    setErr(null);
    try {
      const r = await api.get<{ bookings: Booking[] }>("/bookings", { limit: 50 });
      setItems(r.bookings ?? []);
    } catch (e) {
      setErr(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [role]);

  useEffect(() => {
    if (role) load();
    else setLoading(false);
  }, [role, load]);

  useFocusEffect(useCallback(() => { if (role) load(); }, [role, load]));

  if (bootstrapped && !role) {
    return (
      <SafeAreaView style={styles.screen} edges={["top"]}>
        <Hero />
        <LoginPrompt />
      </SafeAreaView>
    );
  }

  const showSkeleton = !bootstrapped || (!!role && loading);

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <FlatList
        contentContainerStyle={styles.list}
        data={showSkeleton ? [] : items}
        keyExtractor={(b) => String(b.id)}
        showsVerticalScrollIndicator={false}
        ListHeaderComponent={
          <View>
            <Hero count={loading ? undefined : items.length} />
            {err ? (
              <View style={styles.errBox}>
                <Ionicons name="warning-outline" size={16} color={colors.danger} />
                <Text style={styles.errText}>{err}</Text>
              </View>
            ) : null}
          </View>
        }
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => { setRefreshing(true); load(); }}
            tintColor={colors.primaryDark}
          />
        }
        ListEmptyComponent={
          loading ? (
            <View style={styles.skeletonWrap}>
              <RowSkeleton />
              <RowSkeleton />
              <RowSkeleton />
              <RowSkeleton />
            </View>
          ) : (
            <EmptyState title={t("booking.noneClient")} subtitle={t("cars.title")} icon="bookings" />
          )
        }
        renderItem={({ item }) => (
          <BookingCard
            booking={item}
            onPress={() => router.push({ pathname: "/(client)/booking/[id]", params: { id: String(item.id) } })}
          />
        )}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  list: { paddingBottom: 28 },

  hero: {
    paddingTop: 24,
    paddingBottom: 22,
    paddingHorizontal: spacing.xl,
    borderBottomLeftRadius: radius.xxl,
    borderBottomRightRadius: radius.xxl,
  },
  heroBrandRow: { flexDirection: "row", alignItems: "center", gap: 8, marginBottom: 18 },
  heroLogo: {
    width: 36, height: 36, borderRadius: radius.sm,
    backgroundColor: colors.card,
    alignItems: "center", justifyContent: "center",
    overflow: "hidden",
  },
  heroBrandLabel: { ...type.label, color: "rgba(255,255,255,0.65)" },
  heroTitleRow: { flexDirection: "row", alignItems: "center", gap: 10 },
  heroTitle: { ...type.display, color: "#FFFFFF" },
  heroCount: {
    minWidth: 28,
    paddingHorizontal: 9,
    height: 26,
    borderRadius: radius.full,
    backgroundColor: "rgba(245,158,11,0.22)",
    alignItems: "center",
    justifyContent: "center",
  },
  heroCountText: { ...type.captionMed, color: colors.primaryLight },
  heroSub: { ...type.callout, color: "rgba(255,255,255,0.6)", marginTop: 4 },

  errBox: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    marginHorizontal: spacing.lg,
    marginTop: spacing.md,
    padding: 14,
    backgroundColor: colors.dangerBg,
    borderRadius: radius.md,
  },
  errText: { ...type.caption, color: colors.danger, flex: 1 },

  skeletonWrap: { paddingTop: spacing.lg },

  card: {
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    marginHorizontal: spacing.lg,
    marginTop: spacing.md,
    overflow: "hidden",
    ...shadow.md,
  },
  cardHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
    padding: spacing.lg,
    paddingBottom: spacing.md,
  },
  cardCode: { ...type.h3, color: colors.text },
  cardDatesRow: { flexDirection: "row", alignItems: "center", gap: 5, marginTop: 4 },
  cardDates: { ...type.caption, color: colors.textMuted },
  statusPill: {
    flexDirection: "row",
    alignItems: "center",
    gap: 5,
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: radius.full,
  },
  statusDot: { width: 6, height: 6, borderRadius: 3 },
  statusText: { ...type.small, fontFamily: font.semibold },
  cardDivider: { height: 1, backgroundColor: colors.borderLight, marginHorizontal: spacing.lg },
  cardFooter: {
    flexDirection: "row",
    alignItems: "center",
    padding: spacing.lg,
    paddingTop: spacing.md,
  },
  cardAmount: { marginRight: 24 },
  amountLabel: { ...type.label, color: colors.textMuted, fontSize: 10 },
  amountValue: { ...type.h3, color: colors.text, marginTop: 2 },
  paidBadge: {
    flexDirection: "row",
    alignItems: "center",
    gap: 4,
    backgroundColor: colors.successBg,
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: radius.full,
  },
  paidText: { ...type.small, color: colors.success, fontFamily: font.semibold },
  chevron: { marginLeft: "auto" },

  prompt: { flex: 1, alignItems: "center", justifyContent: "center", padding: spacing.xxl },
  promptIcon: {
    width: 88, height: 88, borderRadius: radius.full,
    backgroundColor: colors.primaryXLight,
    borderWidth: 1, borderColor: colors.primaryLight,
    alignItems: "center", justifyContent: "center",
    marginBottom: 20,
  },
  promptTitle: { ...type.h2, color: colors.text, textAlign: "center", marginBottom: 8 },
  promptSub: { ...type.callout, color: colors.textMuted, textAlign: "center", marginBottom: 28, lineHeight: 20 },
  registerLink: { ...type.callout, color: colors.textMuted },
  registerLinkBold: { color: colors.primaryDark, fontFamily: font.bold },
});
