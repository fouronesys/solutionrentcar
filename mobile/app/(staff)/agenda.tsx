import React, { useCallback, useEffect, useState } from "react";
import { Image, Pressable, RefreshControl, ScrollView, StyleSheet, Text, View } from "react-native";
import { useFocusEffect, useRouter } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import Animated, { useAnimatedStyle, useSharedValue, withTiming } from "react-native-reanimated";
import { EmptyState } from "@/components/EmptyState";
import { RowSkeleton } from "@/components/Skeleton";
import { api, ApiError } from "@/api/client";
import type { Agenda, AgendaItem } from "@/api/types";
import { colors, font, gradients, radius, shadow, spacing, type } from "@/theme/colors";
import { i18n, t } from "@/i18n";
import { dateTime, todayIso } from "@/utils/format";

const AnimatedPressable = Animated.createAnimatedComponent(Pressable);

function AgendaCard({
  item,
  label,
  isDelivery,
  onPress,
}: {
  item: AgendaItem;
  label: string;
  isDelivery: boolean;
  onPress: () => void;
}) {
  const scale = useSharedValue(1);
  const animStyle = useAnimatedStyle(() => ({ transform: [{ scale: scale.value }] }));
  const accent = isDelivery ? colors.success : colors.info;
  const accentBg = isDelivery ? colors.successBg : colors.infoBg;
  const clientName = `${item.client?.name ?? ""} ${item.client?.lastname ?? ""}`.trim();

  return (
    <AnimatedPressable
      onPress={onPress}
      onPressIn={() => { scale.value = withTiming(0.98, { duration: 90 }); }}
      onPressOut={() => { scale.value = withTiming(1, { duration: 140 }); }}
      style={[animStyle, styles.agendaCard]}
    >
      <View style={[styles.agendaIcon, { backgroundColor: accentBg }]}>
        <Ionicons name={isDelivery ? "log-out-outline" : "log-in-outline"} size={22} color={accent} />
      </View>
      <View style={styles.agendaBody}>
        <View style={styles.agendaTopRow}>
          <Text style={styles.agendaCode}>#{item.booking.code ?? item.booking.id}</Text>
          <View style={[styles.typePill, { backgroundColor: accentBg }]}>
            <View style={[styles.typeDot, { backgroundColor: accent }]} />
            <Text style={[styles.typePillText, { color: accent }]}>{label}</Text>
          </View>
        </View>
        <Text style={styles.agendaCar}>
          {item.car?.brand ? `${item.car.brand} ` : ""}{item.car?.name ?? item.car?.model ?? "—"}
        </Text>
        {clientName ? (
          <View style={styles.agendaMetaRow}>
            <Ionicons name="person-outline" size={13} color={colors.textMuted} />
            <Text style={styles.agendaMeta} numberOfLines={1}>
              {clientName}{item.client?.phone ? ` · ${item.client.phone}` : ""}
            </Text>
          </View>
        ) : null}
        <View style={styles.agendaMetaRow}>
          <Ionicons name="time-outline" size={13} color={colors.primaryDark} />
          <Text style={[styles.agendaMeta, { color: colors.primaryDark, fontFamily: font.semibold }]}>
            {dateTime(isDelivery ? item.booking.start_at : item.booking.end_at)}
          </Text>
        </View>
      </View>
      <Ionicons name="chevron-forward" size={20} color={colors.textFaint} />
    </AnimatedPressable>
  );
}

export default function AgendaScreen() {
  const router = useRouter();
  const [data, setData] = useState<Agenda | null>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [err, setErr] = useState<string | null>(null);

  const load = useCallback(async () => {
    setErr(null);
    try {
      const r = await api.get<Agenda>("/agenda", { date: todayIso() });
      setData(r);
    } catch (e) {
      setErr(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { load(); }, [load]);
  useFocusEffect(useCallback(() => { load(); }, [load]));

  const navigate = (id: number) =>
    router.push({ pathname: "/(staff)/booking/[id]", params: { id: String(id) } });

  const locale = i18n.locale === "en" ? "en" : "es";
  const today = new Date().toLocaleDateString(locale === "en" ? "en-US" : "es-ES", {
    weekday: "long", day: "numeric", month: "long",
  });

  const renderHero = () => (
    <LinearGradient colors={gradients.hero} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={styles.hero}>
      <View style={styles.heroBrandRow}>
        <View style={styles.heroLogo}>
          <Image source={require("../../assets/images/logo.png")} style={{ width: 32, height: 32 }} resizeMode="contain" />
        </View>
        <Text style={styles.heroBrandLabel}>YOWELL RENT-CAR</Text>
      </View>
      <Text style={styles.heroTitle}>{t("agenda.title")}</Text>
      <Text style={styles.heroDate}>{today}</Text>
    </LinearGradient>
  );

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <ScrollView
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} tintColor={colors.primaryDark} />
        }
      >
        {renderHero()}

        {err ? (
          <View style={styles.errBox}>
            <Ionicons name="warning-outline" size={16} color={colors.danger} />
            <Text style={styles.errText}>{err}</Text>
          </View>
        ) : null}

        {loading ? (
          <View style={styles.body}>
            <RowSkeleton />
            <RowSkeleton />
            <RowSkeleton />
          </View>
        ) : (
          <View style={styles.body}>
            {/* Stats */}
            <View style={styles.statsRow}>
              <View style={styles.statCard}>
                <View style={[styles.statIcon, { backgroundColor: colors.successBg }]}>
                  <Ionicons name="log-out-outline" size={18} color={colors.success} />
                </View>
                <Text style={styles.statNum}>{data?.deliveries?.length ?? 0}</Text>
                <Text style={styles.statLabel}>{t("agenda.deliveries")}</Text>
              </View>
              <View style={styles.statCard}>
                <View style={[styles.statIcon, { backgroundColor: colors.infoBg }]}>
                  <Ionicons name="log-in-outline" size={18} color={colors.info} />
                </View>
                <Text style={styles.statNum}>{data?.returns?.length ?? 0}</Text>
                <Text style={styles.statLabel}>{t("agenda.returns")}</Text>
              </View>
            </View>

            {/* Deliveries */}
            <View style={styles.groupHeader}>
              <View style={[styles.groupDot, { backgroundColor: colors.success }]} />
              <Text style={styles.groupTitle}>{t("agenda.deliveries")}</Text>
            </View>
            {data?.deliveries?.length ? (
              data.deliveries.map((it) => (
                <AgendaCard
                  key={`d-${it.booking.id}`}
                  item={it}
                  label={t("agenda.deliveries")}
                  isDelivery
                  onPress={() => navigate(it.booking.id)}
                />
              ))
            ) : (
              <EmptyState title={t("agenda.noDeliveries")} icon="bookings" />
            )}

            {/* Returns */}
            <View style={[styles.groupHeader, { marginTop: spacing.xl }]}>
              <View style={[styles.groupDot, { backgroundColor: colors.info }]} />
              <Text style={styles.groupTitle}>{t("agenda.returns")}</Text>
            </View>
            {data?.returns?.length ? (
              data.returns.map((it) => (
                <AgendaCard
                  key={`r-${it.booking.id}`}
                  item={it}
                  label={t("agenda.returns")}
                  isDelivery={false}
                  onPress={() => navigate(it.booking.id)}
                />
              ))
            ) : (
              <EmptyState title={t("agenda.noReturns")} icon="bookings" />
            )}
          </View>
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  scrollContent: { paddingBottom: 32 },

  hero: {
    paddingTop: 24,
    paddingBottom: 26,
    paddingHorizontal: spacing.xl,
    borderBottomLeftRadius: radius.xxl,
    borderBottomRightRadius: radius.xxl,
  },
  heroBrandRow: { flexDirection: "row", alignItems: "center", gap: 8, marginBottom: 18 },
  heroLogo: {
    width: 36,
    height: 36,
    borderRadius: radius.sm,
    backgroundColor: colors.card,
    alignItems: "center",
    justifyContent: "center",
    overflow: "hidden",
  },
  heroBrandLabel: { ...type.label, color: "rgba(255,255,255,0.65)" },
  heroTitle: { ...type.display, color: "#FFFFFF" },
  heroDate: { ...type.callout, color: "rgba(255,255,255,0.6)", marginTop: 4, textTransform: "capitalize" },

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

  body: { padding: spacing.lg },

  statsRow: { flexDirection: "row", gap: 12, marginBottom: spacing.xl },
  statCard: {
    flex: 1,
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    padding: spacing.lg,
    ...shadow.md,
  },
  statIcon: {
    width: 38,
    height: 38,
    borderRadius: radius.full,
    alignItems: "center",
    justifyContent: "center",
    marginBottom: 12,
  },
  statNum: { ...type.display, color: colors.text },
  statLabel: { ...type.captionMed, color: colors.textMuted, marginTop: 2 },

  groupHeader: { flexDirection: "row", alignItems: "center", gap: 8, marginBottom: spacing.md },
  groupDot: { width: 8, height: 8, borderRadius: 4 },
  groupTitle: { ...type.label, color: colors.textSecondary },

  agendaCard: {
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    marginBottom: spacing.md,
    padding: spacing.lg,
    flexDirection: "row",
    alignItems: "center",
    gap: 14,
    ...shadow.md,
  },
  agendaIcon: {
    width: 46,
    height: 46,
    borderRadius: radius.md,
    alignItems: "center",
    justifyContent: "center",
  },
  agendaBody: { flex: 1 },
  agendaTopRow: { flexDirection: "row", alignItems: "center", justifyContent: "space-between", marginBottom: 4 },
  agendaCode: { ...type.h3, color: colors.text },
  typePill: {
    flexDirection: "row",
    alignItems: "center",
    gap: 5,
    paddingHorizontal: 9,
    paddingVertical: 4,
    borderRadius: radius.full,
  },
  typeDot: { width: 6, height: 6, borderRadius: 3 },
  typePillText: { ...type.small, fontFamily: font.semibold },
  agendaCar: { ...type.bodyMed, color: colors.textSecondary, marginBottom: 6 },
  agendaMetaRow: { flexDirection: "row", alignItems: "center", gap: 5, marginTop: 2 },
  agendaMeta: { ...type.caption, color: colors.textMuted, flex: 1 },
});
