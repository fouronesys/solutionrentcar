import React, { useCallback, useEffect, useMemo, useState } from "react";
import {
  FlatList,
  Image,
  Platform,
  Pressable,
  RefreshControl,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from "expo-router";
import DateTimePicker, { DateTimePickerEvent } from "@react-native-community/datetimepicker";
import { EmptyState } from "@/components/EmptyState";
import { Input } from "@/components/Input";
import { api, ApiError } from "@/api/client";
import { carStatus, colors, radius, shadow, spacing } from "@/theme/colors";
import type { Car } from "@/api/types";
import { i18n, t } from "@/i18n";
import { money, toDbDateTime } from "@/utils/format";

function defaultStart() {
  const d = new Date();
  d.setDate(d.getDate() + 1);
  d.setHours(10, 0, 0, 0);
  return d;
}
function defaultEnd() {
  const d = new Date();
  d.setDate(d.getDate() + 4);
  d.setHours(18, 0, 0, 0);
  return d;
}
function fmtDate(d: Date) {
  return d.toLocaleDateString(i18n.locale === "en" ? "en-US" : "es-ES", {
    day: "2-digit", month: "short", year: "numeric",
  });
}
function fmtTime(d: Date) {
  return d.toLocaleTimeString(i18n.locale === "en" ? "en-US" : "es-ES", {
    hour: "2-digit", minute: "2-digit",
  });
}

function CarCard({ car, onPress }: { car: Car; onPress: () => void }) {
  const s = carStatus[Number(car.status ?? 0)];
  const locale = i18n.locale === "en" ? "en" : "es";

  const specs = [
    car.year ? String(car.year) : null,
    car.transmission,
    car.fuel,
    car.seat ? `${car.seat} ${locale === "es" ? "asientos" : "seats"}` : null,
  ].filter(Boolean) as string[];

  return (
    <TouchableOpacity onPress={onPress} activeOpacity={0.88} style={styles.carCard}>
      <View style={styles.imageContainer}>
        {car.image ? (
          <Image source={{ uri: car.image }} style={styles.carImg} resizeMode="cover" />
        ) : (
          <View style={[styles.carImg, styles.imgPlaceholder]}>
            <Text style={{ fontSize: 48 }}>🚗</Text>
          </View>
        )}
        <View style={styles.priceTag}>
          <Text style={styles.priceAmount}>{money(car.price_day ?? car.price)}</Text>
          <Text style={styles.pricePer}>{t("cars.perDay")}</Text>
        </View>
        {s ? (
          <View style={[styles.statusBadge, { backgroundColor: s.bg }]}>
            <View style={[styles.statusDot, { backgroundColor: s.color }]} />
            <Text style={[styles.statusText, { color: s.color }]}>{s[locale]}</Text>
          </View>
        ) : null}
      </View>
      <View style={styles.carInfo}>
        <View style={styles.carNameRow}>
          {car.brand ? <Text style={styles.carBrand}>{car.brand}</Text> : null}
          <Text style={styles.carModel}>{car.name ?? car.model ?? ""}</Text>
        </View>
        {specs.length > 0 && (
          <View style={styles.specsRow}>
            {specs.slice(0, 3).map((sp, i) => (
              <React.Fragment key={i}>
                {i > 0 ? <View style={styles.specDot} /> : null}
                <Text style={styles.specText}>{sp}</Text>
              </React.Fragment>
            ))}
          </View>
        )}
      </View>
    </TouchableOpacity>
  );
}

function DatePickerField({
  label,
  value,
  onPress,
}: {
  label: string;
  value: Date;
  onPress: () => void;
}) {
  return (
    <Pressable onPress={onPress} style={styles.dateField}>
      <Text style={styles.dateFieldLabel}>{label}</Text>
      <Text style={styles.dateFieldDate}>{fmtDate(value)}</Text>
      <Text style={styles.dateFieldTime}>{fmtTime(value)}</Text>
    </Pressable>
  );
}

export default function CarsScreen() {
  const router = useRouter();
  const [cars, setCars] = useState<Car[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [err, setErr] = useState<string | null>(null);
  const [q, setQ] = useState("");
  const [filtered, setFiltered] = useState(false);
  const [filterOpen, setFilterOpen] = useState(false);
  const [start, setStart] = useState<Date>(defaultStart());
  const [end, setEnd] = useState<Date>(defaultEnd());
  const [showStart, setShowStart] = useState(false);
  const [showEnd, setShowEnd] = useState(false);
  const [pendingDate, setPendingDate] = useState<Date | null>(null);
  const [pendingField, setPendingField] = useState<"start" | "end" | null>(null);
  const [showTime, setShowTime] = useState(false);

  const load = useCallback(async () => {
    setErr(null);
    try {
      const params: Record<string, string | number | undefined> = { limit: 50, q: q || undefined };
      if (filtered) {
        params.available_from = toDbDateTime(start);
        params.available_to = toDbDateTime(end);
      } else {
        params.status = 0;
      }
      const r = await api.get<{ cars: Car[] }>("/cars", params);
      setCars(r.cars ?? []);
    } catch (e) {
      setErr(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [q, filtered, start, end]);

  useEffect(() => { load(); }, [load]);

  const filterLabel = useMemo(() => {
    if (!filtered) return null;
    return `${fmtDate(start)} → ${fmtDate(end)}`;
  }, [filtered, start, end]);

  const handleDateChange = (e: DateTimePickerEvent, d?: Date) => {
    if (Platform.OS === "ios") {
      if (!d) return;
      if (pendingField === "start") {
        setStart(d);
        if (d >= end) setEnd(new Date(d.getTime() + 3 * 86400000));
      } else if (pendingField === "end") {
        setEnd(d);
      }
      setShowStart(false);
      setShowEnd(false);
    } else {
      if (e.type !== "set" || !d) {
        setShowStart(false);
        setShowEnd(false);
        return;
      }
      setPendingDate(d);
      setShowStart(false);
      setShowEnd(false);
      setShowTime(true);
    }
  };

  const handleTimeChange = (e: DateTimePickerEvent, d?: Date) => {
    setShowTime(false);
    if (e.type !== "set" || !d || !pendingDate) return;
    const combined = new Date(pendingDate);
    combined.setHours(d.getHours(), d.getMinutes(), 0, 0);
    if (pendingField === "start") {
      setStart(combined);
      if (combined >= end) setEnd(new Date(combined.getTime() + 3 * 86400000));
    } else {
      setEnd(combined);
    }
    setPendingDate(null);
    setPendingField(null);
  };

  const renderHeader = () => (
    <View>
      {/* Hero */}
      <View style={styles.hero}>
        <Text style={styles.heroTitle}>Solutions</Text>
        <Text style={styles.heroAccent}>Rent Car</Text>
        <Text style={styles.heroSub}>{t("cars.title")}</Text>
      </View>

      {/* Search */}
      <View style={styles.searchWrap}>
        <Input
          placeholder={`🔍  ${t("common.search")}`}
          value={q}
          onChangeText={setQ}
          autoCapitalize="none"
          returnKeyType="search"
          onSubmitEditing={() => load()}
          containerStyle={{ marginBottom: 0 }}
        />
      </View>

      {/* Filter toggle */}
      <Pressable
        onPress={() => setFilterOpen((v) => !v)}
        style={[styles.filterToggle, filterOpen && styles.filterToggleActive]}
      >
        <Text style={[styles.filterToggleText, filterOpen && { color: colors.primaryDark }]}>
          📆  {t("cars.filterDates")}
        </Text>
        {filtered ? (
          <View style={styles.activePill}>
            <Text style={styles.activePillText}>{t("cars.clearFilter")}</Text>
          </View>
        ) : (
          <Text style={[styles.filterChevron, filterOpen && { transform: [{ rotate: "180deg" }] }]}>
            ›
          </Text>
        )}
      </Pressable>

      {/* Filter panel */}
      {filterOpen && (
        <View style={styles.filterPanel}>
          <View style={styles.dateRow}>
            <DatePickerField
              label={`📍  ${t("cars.pickup")}`}
              value={start}
              onPress={() => { setPendingField("start"); setShowStart(true); }}
            />
            <View style={styles.dateArrow}><Text style={{ fontSize: 18, color: colors.textMuted }}>→</Text></View>
            <DatePickerField
              label={`📍  ${t("cars.dropoff")}`}
              value={end}
              onPress={() => { setPendingField("end"); setShowEnd(true); }}
            />
          </View>

          {showStart && (
            <DateTimePicker
              value={start}
              mode={Platform.OS === "ios" ? "datetime" : "date"}
              display={Platform.OS === "ios" ? "spinner" : "calendar"}
              minimumDate={new Date()}
              onChange={handleDateChange}
            />
          )}
          {showEnd && (
            <DateTimePicker
              value={end}
              mode={Platform.OS === "ios" ? "datetime" : "date"}
              display={Platform.OS === "ios" ? "spinner" : "calendar"}
              minimumDate={new Date(start.getTime() + 3600000)}
              onChange={handleDateChange}
            />
          )}
          {showTime && (
            <DateTimePicker
              value={pendingDate ?? new Date()}
              mode="time"
              display="clock"
              onChange={handleTimeChange}
            />
          )}

          <View style={styles.filterActions}>
            <Pressable
              style={styles.filterApplyBtn}
              onPress={() => { setFiltered(true); setFilterOpen(false); load(); }}
            >
              <Text style={styles.filterApplyText}>{t("cars.applyFilter")}</Text>
            </Pressable>
            {filtered ? (
              <Pressable style={styles.filterClearBtn} onPress={() => { setFiltered(false); load(); }}>
                <Text style={styles.filterClearText}>{t("cars.clearFilter")}</Text>
              </Pressable>
            ) : null}
          </View>
          {filterLabel ? (
            <Text style={styles.filterSummary}>{filterLabel}</Text>
          ) : null}
        </View>
      )}

      {/* Count */}
      <View style={styles.countRow}>
        <Text style={styles.countText}>
          {cars.length} {i18n.locale === "en" ? "vehicles" : "vehículos"}
        </Text>
        {filtered && (
          <View style={styles.activeDot} />
        )}
      </View>

      {err ? (
        <View style={styles.errBox}>
          <Text style={styles.errText}>⚠️  {err}</Text>
        </View>
      ) : null}
    </View>
  );

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <FlatList
        data={loading ? [] : cars}
        keyExtractor={(c) => String(c.id)}
        contentContainerStyle={styles.listContent}
        showsVerticalScrollIndicator={false}
        ListHeaderComponent={renderHeader}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => { setRefreshing(true); load(); }}
            tintColor={colors.primaryDark}
          />
        }
        ListEmptyComponent={
          !loading ? (
            <EmptyState
              title={t("common.empty")}
              subtitle={filtered ? t("cars.clearFilter") : undefined}
              icon="🚗"
            />
          ) : null
        }
        renderItem={({ item }) => (
          <CarCard
            car={item}
            onPress={() => {
              if (filtered) {
                router.push({
                  pathname: "/(client)/book/[carId]",
                  params: { carId: String(item.id), start: toDbDateTime(start), end: toDbDateTime(end) },
                });
              } else {
                router.push({ pathname: "/(client)/car/[id]", params: { id: String(item.id) } });
              }
            }}
          />
        )}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  listContent: { paddingBottom: 24 },

  hero: {
    backgroundColor: colors.dark,
    paddingTop: 32,
    paddingBottom: 28,
    paddingHorizontal: spacing.xl,
  },
  heroTitle: { color: "rgba(255,255,255,0.7)", fontSize: 15, fontWeight: "600", letterSpacing: 2, textTransform: "uppercase" },
  heroAccent: { color: colors.primary, fontSize: 36, fontWeight: "800", letterSpacing: -0.5, marginTop: 2 },
  heroSub: { color: "rgba(255,255,255,0.55)", fontSize: 13, marginTop: 4 },

  searchWrap: { paddingHorizontal: spacing.lg, paddingTop: spacing.lg, paddingBottom: 0, backgroundColor: colors.card },

  filterToggle: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    paddingHorizontal: spacing.lg,
    paddingVertical: 14,
    backgroundColor: colors.card,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
  },
  filterToggleActive: { backgroundColor: colors.primaryXLight },
  filterToggleText: { fontSize: 14, color: colors.textSecondary, fontWeight: "600" },
  filterChevron: { fontSize: 22, color: colors.textMuted, fontWeight: "300" },
  activePill: {
    backgroundColor: colors.primary,
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: radius.full,
  },
  activePillText: { fontSize: 11, fontWeight: "700", color: colors.dark },

  filterPanel: {
    backgroundColor: colors.card,
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.md,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
  },
  dateRow: { flexDirection: "row", alignItems: "center", marginBottom: spacing.md },
  dateArrow: { paddingHorizontal: 8 },
  dateField: {
    flex: 1,
    backgroundColor: colors.borderLight,
    borderRadius: radius.md,
    padding: 12,
    borderWidth: 1,
    borderColor: colors.border,
  },
  dateFieldLabel: { fontSize: 10, color: colors.textMuted, fontWeight: "700", textTransform: "uppercase", letterSpacing: 0.5 },
  dateFieldDate: { fontSize: 14, color: colors.text, fontWeight: "700", marginTop: 3 },
  dateFieldTime: { fontSize: 12, color: colors.textSecondary, marginTop: 1 },

  filterActions: { flexDirection: "row", gap: 8 },
  filterApplyBtn: {
    flex: 1,
    backgroundColor: colors.primary,
    borderRadius: radius.md,
    paddingVertical: 12,
    alignItems: "center",
    ...shadow.primary,
  },
  filterApplyText: { color: colors.dark, fontWeight: "700", fontSize: 14 },
  filterClearBtn: {
    flex: 1,
    backgroundColor: colors.card,
    borderRadius: radius.md,
    paddingVertical: 12,
    alignItems: "center",
    borderWidth: 1.5,
    borderColor: colors.border,
  },
  filterClearText: { color: colors.textSecondary, fontWeight: "600", fontSize: 14 },
  filterSummary: { color: colors.textMuted, fontSize: 11, marginTop: 8, textAlign: "center" },

  countRow: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: spacing.lg,
    paddingTop: spacing.lg,
    paddingBottom: 4,
  },
  countText: { fontSize: 13, color: colors.textMuted, fontWeight: "600", textTransform: "uppercase", letterSpacing: 0.5 },
  activeDot: { width: 6, height: 6, borderRadius: 3, backgroundColor: colors.primary, marginLeft: 6 },

  errBox: {
    marginHorizontal: spacing.lg,
    marginTop: 8,
    padding: 12,
    backgroundColor: colors.dangerBg,
    borderRadius: radius.md,
  },
  errText: { color: colors.danger, fontSize: 13, fontWeight: "500" },

  // Car card
  carCard: {
    marginHorizontal: spacing.lg,
    marginBottom: spacing.md,
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    overflow: "hidden",
    ...shadow.md,
  },
  imageContainer: { position: "relative", height: 200 },
  carImg: { width: "100%", height: 200, backgroundColor: colors.borderLight },
  imgPlaceholder: { alignItems: "center", justifyContent: "center" },
  priceTag: {
    position: "absolute",
    bottom: 12,
    right: 12,
    backgroundColor: colors.dark,
    borderRadius: radius.md,
    paddingHorizontal: 12,
    paddingVertical: 6,
    flexDirection: "row",
    alignItems: "baseline",
    gap: 3,
  },
  priceAmount: { color: colors.primary, fontSize: 17, fontWeight: "800" },
  pricePer: { color: "rgba(255,255,255,0.6)", fontSize: 11 },
  statusBadge: {
    position: "absolute",
    top: 12,
    left: 12,
    flexDirection: "row",
    alignItems: "center",
    gap: 5,
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: radius.full,
  },
  statusDot: { width: 6, height: 6, borderRadius: 3 },
  statusText: { fontSize: 11, fontWeight: "700" },
  carInfo: { padding: spacing.md },
  carNameRow: { marginBottom: 6 },
  carBrand: {
    fontSize: 11,
    color: colors.textMuted,
    fontWeight: "700",
    textTransform: "uppercase",
    letterSpacing: 0.8,
    marginBottom: 2,
  },
  carModel: { fontSize: 18, fontWeight: "800", color: colors.text },
  specsRow: { flexDirection: "row", alignItems: "center" },
  specText: { fontSize: 13, color: colors.textSecondary },
  specDot: { width: 3, height: 3, borderRadius: 1.5, backgroundColor: colors.textMuted, marginHorizontal: 6 },
});
