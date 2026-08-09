import React, { useCallback, useEffect, useState } from "react";
import {
  FlatList,
  Pressable,
  RefreshControl,
  StyleSheet,
  Text,
  TextInput,
  View,
} from "react-native";
import { useFocusEffect, useRouter } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons";
import Animated, { useAnimatedStyle, useSharedValue, withTiming } from "react-native-reanimated";
import { EmptyState } from "@/components/EmptyState";
import { ListSkeleton } from "@/components/Skeleton";
import { ScreenHeader } from "@/components/ScreenHeader";
import { api, ApiError } from "@/api/client";
import type { Booking } from "@/api/types";
import { bookingStatus, colors, font, radius, shadow, spacing, type } from "@/theme/colors";
import { i18n, t } from "@/i18n";
import { money, shortDate } from "@/utils/format";

const AnimatedPressable = Animated.createAnimatedComponent(Pressable);

type StatusFilter = number | "";
const STATUS_FILTERS: { v: StatusFilter; label: string; labelEs: string }[] = [
  { v: "",  label: "All",       labelEs: "Todas"     },
  { v: 0,   label: "Pending",   labelEs: "Pendiente" },
  { v: 1,   label: "Confirmed", labelEs: "Confirmada"},
  { v: 3,   label: "Delivered", labelEs: "Entregada" },
  { v: 4,   label: "Returned",  labelEs: "Devuelta"  },
  { v: 2,   label: "Cancelled", labelEs: "Cancelada" },
];

function BookingRow({ booking, onPress }: { booking: Booking; onPress: () => void }) {
  const s = bookingStatus[Number(booking.status ?? 0)];
  const locale = i18n.locale === "en" ? "en" : "es";
  const total  = Number(booking.total   ?? 0);
  const paid   = Number(booking.payment ?? 0);
  const balance = Math.max(0, total - paid);

  const scale = useSharedValue(1);
  const animStyle = useAnimatedStyle(() => ({ transform: [{ scale: scale.value }] }));

  return (
    <AnimatedPressable
      onPress={onPress}
      onPressIn={() => { scale.value = withTiming(0.985, { duration: 90 }); }}
      onPressOut={() => { scale.value = withTiming(1, { duration: 140 }); }}
      style={[animStyle, styles.row]}
    >
      <View style={styles.rowLeft}>
        <View style={styles.rowTop}>
          <Text style={styles.rowCode}>#{booking.code ?? booking.id}</Text>
          {s ? (
            <View style={[styles.rowStatus, { backgroundColor: s.bg }]}>
              <View style={[styles.rowStatusDot, { backgroundColor: s.color }]} />
              <Text style={[styles.rowStatusText, { color: s.color }]}>{s[locale]}</Text>
            </View>
          ) : null}
        </View>
        <View style={styles.rowDatesRow}>
          <Ionicons name="calendar-outline" size={13} color={colors.textMuted} />
          <Text style={styles.rowDates}>{shortDate(booking.start_at)} → {shortDate(booking.end_at)}</Text>
        </View>
        <View style={styles.rowBottom}>
          <Text style={styles.rowTotal}>{money(total)}</Text>
          {balance > 0 ? (
            <View style={styles.balancePill}>
              <Text style={styles.balanceText}>
                {locale === "en" ? "owes" : "debe"} {money(balance)}
              </Text>
            </View>
          ) : (
            <View style={styles.paidPill}>
              <Ionicons name="checkmark-circle" size={13} color={colors.success} />
              <Text style={styles.paidText}>{locale === "en" ? "Paid" : "Pagado"}</Text>
            </View>
          )}
        </View>
      </View>
      <Ionicons name="chevron-forward" size={20} color={colors.textFaint} />
    </AnimatedPressable>
  );
}

export default function StaffBookingsList() {
  const router = useRouter();
  const [items,     setItems]     = useState<Booking[]>([]);
  const [loading,   setLoading]   = useState(true);
  const [refreshing,setRefreshing]= useState(false);
  const [err,       setErr]       = useState<string | null>(null);
  const [q,         setQ]         = useState("");
  const [status,    setStatus]    = useState<StatusFilter>("");
  const locale = i18n.locale === "en" ? "en" : "es";

  const load = useCallback(async () => {
    setErr(null);
    try {
      const r = await api.get<{ bookings: Booking[] }>("/bookings", {
        q: q || undefined,
        status: status === "" ? undefined : status,
        limit: 100,
      });
      setItems(r.bookings ?? []);
    } catch (e) {
      setErr(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [q, status]);

  useEffect(() => { load(); }, [load]);
  useFocusEffect(useCallback(() => { load(); }, [load]));

  const renderHeader = () => (
    <View>
      <ScreenHeader
        title={t("booking.myBookings")}
        subtitle={locale === "en" ? "All reservations" : "Todas las reservas"}
      />

      {/* Search */}
      <View style={styles.searchBar}>
        <Ionicons name="search-outline" size={18} color={colors.textMuted} />
        <TextInput
          placeholder={`${t("common.search")}…`}
          placeholderTextColor={colors.textMuted}
          value={q}
          onChangeText={setQ}
          autoCapitalize="none"
          returnKeyType="search"
          onSubmitEditing={() => load()}
          style={styles.searchInput}
        />
        {q ? (
          <Pressable onPress={() => { setQ(""); load(); }} hitSlop={8}>
            <Ionicons name="close-circle" size={18} color={colors.textMuted} />
          </Pressable>
        ) : null}
      </View>

      {/* Status filters */}
      <FlatList
        horizontal
        data={STATUS_FILTERS}
        keyExtractor={(f) => String(f.v)}
        showsHorizontalScrollIndicator={false}
        contentContainerStyle={styles.filtersRow}
        style={styles.filters}
        renderItem={({ item: f }) => {
          const active = status === f.v;
          return (
            <Pressable
              onPress={() => setStatus(f.v)}
              style={[styles.filterChip, active && styles.filterChipActive]}
            >
              <Text style={[styles.filterChipText, active && styles.filterChipTextActive]}>
                {locale === "en" ? f.label : f.labelEs}
              </Text>
            </Pressable>
          );
        }}
      />

      {err ? (
        <View style={styles.errBox}>
          <Ionicons name="warning-outline" size={16} color={colors.danger} />
          <Text style={styles.errText}>{err}</Text>
        </View>
      ) : null}

      {!loading && (
        <View style={styles.countRow}>
          <Text style={styles.countLabel}>
            {items.length} {locale === "en" ? "bookings" : "reservas"}
          </Text>
        </View>
      )}
    </View>
  );

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <FlatList
        contentContainerStyle={styles.list}
        data={loading ? [] : items}
        keyExtractor={(b) => String(b.id)}
        showsVerticalScrollIndicator={false}
        ListHeaderComponent={renderHeader}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => { setRefreshing(true); load(); }}
            tintColor={colors.cta}
          />
        }
        ListEmptyComponent={
          loading ? (
            <ListSkeleton count={4} />
          ) : (
            <EmptyState title={t("booking.noneStaff")} icon="bookings" />
          )
        }
        renderItem={({ item }) => (
          <BookingRow
            booking={item}
            onPress={() =>
              router.push({ pathname: "/(staff)/booking/[id]", params: { id: String(item.id) } })
            }
          />
        )}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  list:   { paddingBottom: 28 },

  searchBar: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    backgroundColor: colors.card,
    borderRadius: radius.md,
    paddingHorizontal: 14,
    marginHorizontal: spacing.lg,
    marginTop: spacing.md,
    height: 52,
    borderWidth: 1,
    borderColor: colors.border,
    ...shadow.xs,
  },
  searchInput: { ...type.bodyMed, color: colors.text, flex: 1, height: 52 },

  filters:    { maxHeight: 60 },
  filtersRow: { paddingHorizontal: spacing.lg, paddingVertical: spacing.md, gap: 8 },
  filterChip: {
    paddingHorizontal: 16, height: 36, justifyContent: "center",
    borderRadius: radius.full,
    backgroundColor: colors.card,
    borderWidth: 1, borderColor: colors.border,
    ...shadow.xs,
  },
  filterChipActive:     { backgroundColor: colors.dark, borderColor: colors.dark },
  filterChipText:       { ...type.captionMed, color: colors.textSecondary },
  filterChipTextActive: { color: "#fff", fontFamily: font.semibold },

  errBox: {
    flexDirection: "row", alignItems: "center", gap: 8,
    marginHorizontal: spacing.lg, marginTop: 4,
    padding: 14, backgroundColor: colors.dangerBg, borderRadius: radius.md,
  },
  errText: { ...type.caption, color: colors.danger, flex: 1 },

  countRow:  { paddingHorizontal: spacing.lg, paddingBottom: 4 },
  countLabel:{ ...type.label, color: colors.textMuted },

  row: {
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    marginHorizontal: spacing.lg,
    marginTop: spacing.md,
    padding: spacing.lg,
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
    ...shadow.sm,
  },
  rowLeft:  { flex: 1 },
  rowTop:   { flexDirection: "row", alignItems: "center", gap: 8, marginBottom: 6 },
  rowCode:  { ...type.h3, color: colors.text },
  rowStatus: {
    flexDirection: "row", alignItems: "center", gap: 5,
    paddingHorizontal: 9, paddingVertical: 4, borderRadius: radius.full,
  },
  rowStatusDot:  { width: 6, height: 6, borderRadius: 3 },
  rowStatusText: { ...type.small, fontFamily: font.semibold },
  rowDatesRow:   { flexDirection: "row", alignItems: "center", gap: 5, marginBottom: 10 },
  rowDates:      { ...type.caption, color: colors.textMuted },
  rowBottom:     { flexDirection: "row", alignItems: "center", gap: 10 },
  rowTotal:      { ...type.h3, color: colors.text },
  balancePill: {
    backgroundColor: colors.dangerBg,
    paddingHorizontal: 9, paddingVertical: 4, borderRadius: radius.full,
  },
  balanceText: { ...type.small, color: colors.danger, fontFamily: font.semibold },
  paidPill: {
    flexDirection: "row", alignItems: "center", gap: 4,
    backgroundColor: colors.successBg,
    paddingHorizontal: 9, paddingVertical: 4, borderRadius: radius.full,
  },
  paidText: { ...type.small, color: colors.success, fontFamily: font.semibold },
});
