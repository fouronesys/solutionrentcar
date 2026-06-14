import React, { useCallback, useEffect, useState } from "react";
import { FlatList, Pressable, RefreshControl, StyleSheet, Text, View } from "react-native";
import { useFocusEffect, useRouter } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { Loading } from "@/components/Loading";
import { EmptyState } from "@/components/EmptyState";
import { Input } from "@/components/Input";
import { api, ApiError } from "@/api/client";
import type { Booking } from "@/api/types";
import { bookingStatus, colors, radius, shadow, spacing } from "@/theme/colors";
import { i18n, t } from "@/i18n";
import { money, shortDate } from "@/utils/format";

type StatusFilter = number | "";
const STATUS_FILTERS: { v: StatusFilter; label: string; labelEs: string }[] = [
  { v: "", label: "All", labelEs: "Todas" },
  { v: 0, label: "Pending", labelEs: "Pendiente" },
  { v: 1, label: "Confirmed", labelEs: "Confirmada" },
  { v: 3, label: "Delivered", labelEs: "Entregada" },
  { v: 4, label: "Returned", labelEs: "Devuelta" },
  { v: 2, label: "Cancelled", labelEs: "Cancelada" },
];

function BookingRow({ booking, onPress }: { booking: Booking; onPress: () => void }) {
  const s = bookingStatus[Number(booking.status ?? 0)];
  const locale = i18n.locale === "en" ? "en" : "es";
  const total = Number(booking.total ?? 0);
  const paid = Number(booking.payment ?? 0);
  const balance = Math.max(0, total - paid);

  return (
    <Pressable onPress={onPress} style={({ pressed }) => [styles.row, pressed && { opacity: 0.9 }]}>
      <View style={styles.rowLeft}>
        <View style={styles.rowTop}>
          <Text style={styles.rowCode}>#{booking.code ?? booking.id}</Text>
          {s ? (
            <View style={[styles.rowStatus, { backgroundColor: s.bg }]}>
              <Text style={[styles.rowStatusText, { color: s.color }]}>{s[locale]}</Text>
            </View>
          ) : null}
        </View>
        <Text style={styles.rowDates}>{shortDate(booking.start_at)} → {shortDate(booking.end_at)}</Text>
        <View style={styles.rowBottom}>
          <Text style={styles.rowTotal}>{money(total)}</Text>
          {balance > 0 ? <Text style={styles.rowBalance}> · debe {money(balance)}</Text> : null}
        </View>
      </View>
      <Text style={styles.rowChevron}>›</Text>
    </Pressable>
  );
}

export default function StaffBookingsList() {
  const router = useRouter();
  const [items, setItems] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [err, setErr] = useState<string | null>(null);
  const [q, setQ] = useState("");
  const [status, setStatus] = useState<StatusFilter>("");
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

  if (loading) return <Loading />;

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      {/* Search bar */}
      <View style={styles.searchArea}>
        <Input
          placeholder={`🔍  ${t("common.search")}…`}
          value={q}
          onChangeText={setQ}
          autoCapitalize="none"
          returnKeyType="search"
          onSubmitEditing={() => load()}
          containerStyle={{ marginBottom: 0 }}
        />
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

      {err ? <View style={styles.errBox}><Text style={styles.errText}>⚠️  {err}</Text></View> : null}

      <FlatList
        contentContainerStyle={styles.list}
        data={items}
        keyExtractor={(b) => String(b.id)}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} tintColor={colors.primaryDark} />
        }
        ListEmptyComponent={<EmptyState title={t("booking.noneStaff")} icon="📋" />}
        ListHeaderComponent={
          <Text style={styles.countLabel}>
            {items.length} {locale === "en" ? "bookings" : "reservas"}
          </Text>
        }
        renderItem={({ item }) => (
          <BookingRow
            booking={item}
            onPress={() => router.push({ pathname: "/(staff)/booking/[id]", params: { id: String(item.id) } })}
          />
        )}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  searchArea: { padding: spacing.md, backgroundColor: colors.card, borderBottomWidth: 1, borderBottomColor: colors.border },
  filters: { backgroundColor: colors.card, borderBottomWidth: 1, borderBottomColor: colors.border, maxHeight: 52 },
  filtersRow: { paddingHorizontal: spacing.md, paddingVertical: 10, gap: 8 },
  filterChip: {
    paddingHorizontal: 14, paddingVertical: 6,
    borderRadius: radius.full,
    backgroundColor: colors.borderLight,
    borderWidth: 1, borderColor: colors.border,
  },
  filterChipActive: { backgroundColor: colors.primary, borderColor: colors.primary },
  filterChipText: { fontSize: 13, color: colors.textSecondary, fontWeight: "600" },
  filterChipTextActive: { color: colors.dark },
  errBox: { margin: spacing.lg, padding: 12, backgroundColor: colors.dangerBg, borderRadius: radius.md },
  errText: { color: colors.danger, fontSize: 13 },
  list: { padding: spacing.md, paddingBottom: 24 },
  countLabel: {
    fontSize: 11, color: colors.textMuted, fontWeight: "700",
    textTransform: "uppercase", letterSpacing: 0.5, marginBottom: 10,
  },
  row: {
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    marginBottom: 8,
    padding: spacing.lg,
    flexDirection: "row",
    alignItems: "center",
    ...shadow.sm,
  },
  rowLeft: { flex: 1 },
  rowTop: { flexDirection: "row", alignItems: "center", gap: 8, marginBottom: 4 },
  rowCode: { fontSize: 16, fontWeight: "800", color: colors.text },
  rowStatus: { paddingHorizontal: 8, paddingVertical: 3, borderRadius: radius.full },
  rowStatusText: { fontSize: 11, fontWeight: "700" },
  rowDates: { fontSize: 13, color: colors.textMuted, marginBottom: 6 },
  rowBottom: { flexDirection: "row", alignItems: "baseline" },
  rowTotal: { fontSize: 16, fontWeight: "700", color: colors.text },
  rowBalance: { fontSize: 13, color: colors.danger, fontWeight: "600" },
  rowChevron: { fontSize: 24, color: colors.textMuted, fontWeight: "300", marginLeft: 8 },
});
