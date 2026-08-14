/**
 * Mis reservas — client bookings list.
 * White header pattern (no gradient), card list.
 */
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
import { TAB_BAR_HEIGHT, useTabBarScroll } from "@/components/TabBarScrollContext";
import { SafeAreaView } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons";
import Animated, { useAnimatedStyle, useSharedValue, withTiming } from "react-native-reanimated";
import { EmptyState } from "@/components/EmptyState";
import { RowSkeleton } from "@/components/Skeleton";
import { Button } from "@/components/Button";
import { ScreenHeader } from "@/components/ScreenHeader";
import { api, ApiError } from "@/api/client";
import type { Booking } from "@/api/types";
import { bookingStatus, colors, font, radius, shadow, spacing, type } from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";
import { i18n, t } from "@/i18n";
import { money, shortDate } from "@/utils/format";
import { useAuth } from "@/auth/AuthContext";

const AnimatedPressable = Animated.createAnimatedComponent(Pressable);

function LoginPrompt() {
  const router = useRouter();
  const styles = useThemedStyles(makeStyles);
  const locale = i18n.locale === "en" ? "en" : "es";
  return (
    <View style={styles.prompt}>
      <View style={styles.promptIcon}>
        <Ionicons name="calendar-outline" size={36} color={colors.cta} />
      </View>
      <Text style={styles.promptTitle}>{t("login.requiredTitle")}</Text>
      <Text style={styles.promptSub}>{t("login.requiredSubtitle")}</Text>
      <Button
        title={t("login.goToLogin")}
        onPress={() => router.push("/login/client")}
        style={{ marginBottom: 12, alignSelf: "stretch" }}
        size="lg"
        icon="log-in-outline"
      />
      <Pressable onPress={() => router.push("/register/client")}>
        <Text style={styles.registerLink}>
          {t("login.noAccount")}{" "}
          <Text style={styles.registerLinkBold}>{t("login.createAccount")}</Text>
        </Text>
      </Pressable>
    </View>
  );
}

function BookingCard({ booking, onPress }: { booking: Booking; onPress: () => void }) {
  const styles = useThemedStyles(makeStyles);
  const s = bookingStatus[Number(booking.status ?? 0)];
  const locale = i18n.locale === "en" ? "en" : "es";
  const total = Number(booking.total ?? 0);
  const paid = Number(booking.payment ?? 0);
  const balance = Math.max(0, total - paid);
  const car = (booking as any).car;

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
          {car ? (
            <Text style={styles.cardCarName} numberOfLines={1}>
              {car.brand ? `${car.brand} ` : ""}{car.name ?? car.model ?? ""}
            </Text>
          ) : null}
          <Text style={styles.cardCode}>#{booking.code ?? booking.id}</Text>
          <View style={styles.cardDatesRow}>
            <Ionicons name="calendar-outline" size={13} color={colors.textMuted} />
            <Text style={styles.cardDates}>
              {shortDate(booking.start_at)} → {shortDate(booking.end_at)}
            </Text>
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
  const { onScroll, showTabBar } = useTabBarScroll();
  useFocusEffect(useCallback(() => { showTabBar(); }, [showTabBar]));
  const styles = useThemedStyles(makeStyles);
  const [items, setItems] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [err, setErr] = useState<string | null>(null);
  const locale = i18n.locale === "en" ? "en" : "es";

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

  const showSkeleton = !bootstrapped || (!!role && loading);

  if (bootstrapped && !role) {
    return (
      <SafeAreaView style={styles.screen} edges={["top"]}>
        <ScreenHeader
          title={t("booking.myBookings")}
          subtitle={locale === "en" ? "Track your reservations." : "Chequea tus reservas."}
        />
        <LoginPrompt />
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <FlatList
        contentContainerStyle={[styles.list, { paddingBottom: TAB_BAR_HEIGHT + 16 }]}
        data={showSkeleton ? [] : items}
        keyExtractor={(b) => String(b.id)}
        showsVerticalScrollIndicator={false}
        onScroll={onScroll}
        scrollEventThrottle={16}
        ListHeaderComponent={
          <View>
            <ScreenHeader
              title={t("booking.myBookings")}
              subtitle={locale === "en" ? "Track your reservations." : "Chequea tus reservas."}
            />
            <View style={styles.listMeta}>
              {!loading && (
                <Text style={styles.countText}>
                  {items.length} {locale === "en" ? "reservations" : "reservas"}
                </Text>
              )}
              {err ? (
                <View style={styles.errBox}>
                  <Ionicons name="warning-outline" size={16} color={colors.danger} />
                  <Text style={styles.errText}>{err}</Text>
                </View>
              ) : null}
            </View>
          </View>
        }
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => { setRefreshing(true); load(); }}
            tintColor={colors.cta}
          />
        }
        ListEmptyComponent={
          showSkeleton ? (
            <View style={styles.skeletonWrap}>
              {[0, 1, 2, 3].map((i) => <RowSkeleton key={i} />)}
            </View>
          ) : (
            <EmptyState
              title={t("booking.noneClient")}
              subtitle={locale === "en" ? "Browse cars to make a reservation." : "Dale una vuelta a los carros y móntate."}
              icon="bookings"
            />
          )
        }
        renderItem={({ item }) => (
          <BookingCard
            booking={item}
            onPress={() =>
              router.push({ pathname: "/(client)/booking/[id]", params: { id: String(item.id) } })
            }
          />
        )}
      />
    </SafeAreaView>
  );
}

const makeStyles = () => StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  list: { paddingBottom: 28 },
  listMeta: { paddingHorizontal: spacing.xl, paddingTop: spacing.lg, paddingBottom: spacing.sm },
  countText: { ...type.label, color: colors.textMuted },

  errBox: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    marginTop: spacing.sm,
    padding: 14,
    backgroundColor: colors.dangerBg,
    borderRadius: radius.md,
  },
  errText: { ...type.caption, color: colors.danger, flex: 1 },
  skeletonWrap: { paddingTop: spacing.lg },

  card: {
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    marginHorizontal: spacing.xl,
    marginTop: spacing.md,
    overflow: "hidden",
    ...shadow.sm,
  },
  cardHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
    padding: spacing.lg,
    paddingBottom: spacing.md,
  },
  cardCarName: { ...type.title, color: colors.text, marginBottom: 2 },
  cardCode: { ...type.caption, color: colors.textMuted },
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
    backgroundColor: colors.ctaXLight,
    borderWidth: 1, borderColor: colors.ctaLight,
    alignItems: "center", justifyContent: "center", marginBottom: 20,
  },
  promptTitle: { ...type.h2, color: colors.text, textAlign: "center", marginBottom: 8 },
  promptSub: { ...type.callout, color: colors.textMuted, textAlign: "center", marginBottom: 28, lineHeight: 20 },
  registerLink: { ...type.callout, color: colors.textMuted },
  registerLinkBold: { color: colors.cta, fontFamily: font.bold },
});
