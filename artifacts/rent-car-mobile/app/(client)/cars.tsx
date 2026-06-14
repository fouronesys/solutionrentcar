import React, { useCallback, useEffect, useMemo, useState } from "react";
import {
  FlatList,
  Image,
  Platform,
  Pressable,
  RefreshControl,
  StyleSheet,
  Text,
  TextInput,
  View,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from "expo-router";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import Animated, { useAnimatedStyle, useSharedValue, withTiming } from "react-native-reanimated";
import DateTimePicker, { DateTimePickerEvent } from "@react-native-community/datetimepicker";
import { EmptyState } from "@/components/EmptyState";
import { ListSkeleton } from "@/components/Skeleton";
import { api, ApiError } from "@/api/client";
import { carStatus, colors, font, gradients, radius, shadow, spacing, type } from "@/theme/colors";
import type { Car } from "@/api/types";
import { i18n, t } from "@/i18n";
import { money, toDbDateTime } from "@/utils/format";

const AnimatedPressable = Animated.createAnimatedComponent(Pressable);

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

const SPEC_ICONS: Record<string, keyof typeof Ionicons.glyphMap> = {
  year: "calendar-outline",
  transmission: "cog-outline",
  fuel: "water-outline",
  seats: "people-outline",
};

function CarCard({ car, onPress }: { car: Car; onPress: () => void }) {
  const s = carStatus[Number(car.status ?? 0)];
  const locale = i18n.locale === "en" ? "en" : "es";
  const scale = useSharedValue(1);
  const animStyle = useAnimatedStyle(() => ({ transform: [{ scale: scale.value }] }));

  const specs: { key: string; value: string }[] = [
    car.year ? { key: "year", value: String(car.year) } : null,
    car.transmission ? { key: "transmission", value: car.transmission } : null,
    car.fuel ? { key: "fuel", value: car.fuel } : null,
    car.seat ? { key: "seats", value: `${car.seat}` } : null,
  ].filter(Boolean) as { key: string; value: string }[];

  return (
    <AnimatedPressable
      onPress={onPress}
      onPressIn={() => { scale.value = withTiming(0.98, { duration: 90 }); }}
      onPressOut={() => { scale.value = withTiming(1, { duration: 140 }); }}
      style={[animStyle, styles.carCard]}
    >
      <View style={styles.imageContainer}>
        {car.image ? (
          <Image source={{ uri: car.image }} style={styles.carImg} resizeMode="cover" />
        ) : (
          <View style={[styles.carImg, styles.imgPlaceholder]}>
            <Ionicons name="car-sport" size={52} color={colors.textFaint} />
          </View>
        )}
        <LinearGradient colors={gradients.cardScrim} style={styles.imgScrim} />
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
        {car.brand ? <Text style={styles.carBrand}>{car.brand}</Text> : null}
        <Text style={styles.carModel}>{car.name ?? car.model ?? ""}</Text>
        {specs.length > 0 && (
          <View style={styles.specsRow}>
            {specs.slice(0, 4).map((sp) => (
              <View key={sp.key} style={styles.specChip}>
                <Ionicons name={SPEC_ICONS[sp.key]} size={13} color={colors.textSecondary} />
                <Text style={styles.specText}>{sp.value}</Text>
              </View>
            ))}
          </View>
        )}
      </View>
    </AnimatedPressable>
  );
}

function DatePickerField({
  icon,
  label,
  value,
  onPress,
}: {
  icon: keyof typeof Ionicons.glyphMap;
  label: string;
  value: Date;
  onPress: () => void;
}) {
  return (
    <Pressable onPress={onPress} style={styles.dateField}>
      <View style={styles.dateFieldLabelRow}>
        <Ionicons name={icon} size={12} color={colors.textMuted} />
        <Text style={styles.dateFieldLabel}>{label}</Text>
      </View>
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
    return `${fmtDate(start)}  →  ${fmtDate(end)}`;
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
      <LinearGradient colors={gradients.hero} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={styles.hero}>
        <View style={styles.heroBrandRow}>
          <View style={styles.heroLogo}>
            <Ionicons name="car-sport" size={20} color={colors.dark} />
          </View>
          <Text style={styles.heroBrandLabel}>SOLUTION RENT CAR</Text>
        </View>
        <Text style={styles.heroTitle}>{t("cars.title")}</Text>
        <Text style={styles.heroSub}>
          {i18n.locale === "en"
            ? "Premium vehicles, ready when you are."
            : "Vehículos premium, listos cuando lo estés."}
        </Text>

        {/* Search */}
        <View style={styles.searchBar}>
          <Ionicons name="search" size={18} color={colors.textMuted} />
          <TextInput
            placeholder={t("common.search")}
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
      </LinearGradient>

      {/* Filter toggle */}
      <View style={styles.toolbar}>
        <Pressable
          onPress={() => setFilterOpen((v) => !v)}
          style={[styles.filterToggle, (filterOpen || filtered) && styles.filterToggleActive]}
        >
          <Ionicons
            name="calendar-outline"
            size={16}
            color={filterOpen || filtered ? colors.primaryDark : colors.textSecondary}
          />
          <Text style={[styles.filterToggleText, (filterOpen || filtered) && { color: colors.primaryDark }]}>
            {filtered ? filterLabel : t("cars.filterDates")}
          </Text>
          <Ionicons
            name={filterOpen ? "chevron-up" : "chevron-down"}
            size={16}
            color={colors.textMuted}
          />
        </Pressable>
        {filtered ? (
          <Pressable style={styles.clearChip} onPress={() => { setFiltered(false); setFilterOpen(false); load(); }}>
            <Ionicons name="close" size={16} color={colors.textSecondary} />
          </Pressable>
        ) : null}
      </View>

      {/* Filter panel */}
      {filterOpen && (
        <View style={styles.filterPanel}>
          <View style={styles.dateRow}>
            <DatePickerField
              icon="location-outline"
              label={t("cars.pickup")}
              value={start}
              onPress={() => { setPendingField("start"); setShowStart(true); }}
            />
            <View style={styles.dateArrow}>
              <Ionicons name="arrow-forward" size={16} color={colors.textMuted} />
            </View>
            <DatePickerField
              icon="flag-outline"
              label={t("cars.dropoff")}
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

          <Pressable
            style={styles.filterApplyBtn}
            onPress={() => { setFiltered(true); setFilterOpen(false); load(); }}
          >
            <LinearGradient
              colors={gradients.gold}
              start={{ x: 0, y: 0 }}
              end={{ x: 1, y: 1 }}
              style={StyleSheet.absoluteFill}
            />
            <Ionicons name="search" size={16} color="#1A1100" />
            <Text style={styles.filterApplyText}>{t("cars.applyFilter")}</Text>
          </Pressable>
        </View>
      )}

      {/* Count */}
      {!loading && (
        <View style={styles.countRow}>
          <Text style={styles.countText}>
            {cars.length} {i18n.locale === "en" ? "vehicles available" : "vehículos disponibles"}
          </Text>
        </View>
      )}

      {err ? (
        <View style={styles.errBox}>
          <Ionicons name="warning-outline" size={16} color={colors.danger} />
          <Text style={styles.errText}>{err}</Text>
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
          loading ? (
            <ListSkeleton count={4} />
          ) : (
            <EmptyState
              title={t("common.empty")}
              subtitle={filtered ? t("cars.clearFilter") : undefined}
              icon="cars"
            />
          )
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
  listContent: { paddingBottom: 28 },

  hero: {
    paddingTop: 24,
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
  heroTitle: { ...type.display, color: "#FFFFFF" },
  heroSub: { ...type.callout, color: "rgba(255,255,255,0.6)", marginTop: 4 },

  searchBar: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    backgroundColor: "rgba(255,255,255,0.95)",
    borderRadius: radius.md,
    paddingHorizontal: 14,
    marginTop: 18,
    height: 50,
    ...shadow.md,
  },
  searchInput: { ...type.bodyMed, color: colors.text, height: 50, flex: 1 },

  toolbar: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    paddingHorizontal: spacing.lg,
    paddingTop: spacing.lg,
  },
  filterToggle: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    paddingHorizontal: 14,
    height: 46,
    backgroundColor: colors.card,
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: colors.border,
    ...shadow.xs,
  },
  filterToggleActive: { borderColor: colors.primaryLight, backgroundColor: colors.primaryXLight },
  filterToggleText: { ...type.captionMed, color: colors.textSecondary, flex: 1 },
  clearChip: {
    width: 46,
    height: 46,
    borderRadius: radius.md,
    backgroundColor: colors.card,
    borderWidth: 1,
    borderColor: colors.border,
    alignItems: "center",
    justifyContent: "center",
    ...shadow.xs,
  },

  filterPanel: {
    backgroundColor: colors.card,
    marginHorizontal: spacing.lg,
    marginTop: spacing.sm,
    padding: spacing.lg,
    borderRadius: radius.lg,
    borderWidth: 1,
    borderColor: colors.border,
    ...shadow.sm,
  },
  dateRow: { flexDirection: "row", alignItems: "center", marginBottom: spacing.md },
  dateArrow: { paddingHorizontal: 8 },
  dateField: {
    flex: 1,
    backgroundColor: colors.bg,
    borderRadius: radius.md,
    padding: 12,
    borderWidth: 1,
    borderColor: colors.border,
  },
  dateFieldLabelRow: { flexDirection: "row", alignItems: "center", gap: 4, marginBottom: 5 },
  dateFieldLabel: { ...type.label, color: colors.textMuted, fontSize: 10 },
  dateFieldDate: { ...type.title, color: colors.text },
  dateFieldTime: { ...type.caption, color: colors.textSecondary, marginTop: 1 },

  filterApplyBtn: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 8,
    height: 48,
    borderRadius: radius.md,
    overflow: "hidden",
    ...shadow.gold,
  },
  filterApplyText: { ...type.title, color: "#1A1100", fontFamily: font.bold },

  countRow: { paddingHorizontal: spacing.lg, paddingTop: spacing.lg, paddingBottom: 2 },
  countText: { ...type.label, color: colors.textMuted },

  errBox: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    marginHorizontal: spacing.lg,
    marginTop: 10,
    padding: 14,
    backgroundColor: colors.dangerBg,
    borderRadius: radius.md,
  },
  errText: { ...type.caption, color: colors.danger, flex: 1 },

  carCard: {
    marginHorizontal: spacing.lg,
    marginTop: spacing.md,
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    overflow: "hidden",
    ...shadow.md,
  },
  imageContainer: { position: "relative", height: 200 },
  carImg: { width: "100%", height: 200, backgroundColor: colors.borderLight },
  imgPlaceholder: { alignItems: "center", justifyContent: "center" },
  imgScrim: { position: "absolute", left: 0, right: 0, bottom: 0, height: 90 },
  priceTag: {
    position: "absolute",
    bottom: 12,
    right: 12,
    backgroundColor: "rgba(11,18,32,0.82)",
    borderRadius: radius.md,
    paddingHorizontal: 12,
    paddingVertical: 7,
    flexDirection: "row",
    alignItems: "baseline",
    gap: 3,
  },
  priceAmount: { color: colors.primaryLight, fontFamily: font.extrabold, fontSize: 17 },
  pricePer: { color: "rgba(255,255,255,0.6)", ...type.small },
  statusBadge: {
    position: "absolute",
    top: 12,
    left: 12,
    flexDirection: "row",
    alignItems: "center",
    gap: 5,
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: radius.full,
  },
  statusDot: { width: 6, height: 6, borderRadius: 3 },
  statusText: { ...type.small, fontFamily: font.semibold },
  carInfo: { padding: spacing.lg },
  carBrand: { ...type.label, color: colors.textMuted, marginBottom: 3 },
  carModel: { ...type.h2, color: colors.text },
  specsRow: { flexDirection: "row", flexWrap: "wrap", gap: 8, marginTop: 12 },
  specChip: {
    flexDirection: "row",
    alignItems: "center",
    gap: 4,
    backgroundColor: colors.bg,
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: radius.full,
  },
  specText: { ...type.captionMed, color: colors.textSecondary },
});
