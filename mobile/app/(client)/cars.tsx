/**
 * Autos — vehicle catalog.
 * Design: white header, category pills (red active), large car cards.
 */
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
import Animated, { useAnimatedStyle, useSharedValue, withTiming } from "react-native-reanimated";
import DateTimePicker, { DateTimePickerEvent } from "@react-native-community/datetimepicker";
import { EmptyState } from "@/components/EmptyState";
import { ListSkeleton } from "@/components/Skeleton";
import { BellButton, ScreenHeader } from "@/components/ScreenHeader";
import { api, ApiError } from "@/api/client";
import { carStatus, colors, font, radius, shadow, spacing, type } from "@/theme/colors";
import type { Car } from "@/api/types";
import { i18n, t } from "@/i18n";
import { money, toDbDateTime } from "@/utils/format";
import { useNotificationsCtx } from "@/notifications/NotificationsContext";

const AnimatedPressable = Animated.createAnimatedComponent(Pressable);

function defaultStart() {
  const d = new Date(); d.setDate(d.getDate() + 1); d.setHours(10, 0, 0, 0); return d;
}
function defaultEnd() {
  const d = new Date(); d.setDate(d.getDate() + 4); d.setHours(18, 0, 0, 0); return d;
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

// Static categories – filtered client-side by transmission / type fields.
const CATEGORIES = ["Todos", "SUV", "Sedán", "Económico"];

function CarCard({ car, onPress, index }: { car: Car; onPress: () => void; index: number }) {
  const s = carStatus[Number(car.status ?? 0)];
  const locale = i18n.locale === "en" ? "en" : "es";
  const scale = useSharedValue(1);
  const animStyle = useAnimatedStyle(() => ({ transform: [{ scale: scale.value }] }));

  // Simple "featured" badge for the first two cards
  const badge = index === 0
    ? (locale === "en" ? "Most popular" : "Más elegido")
    : index === 1 && car.price_day
    ? (locale === "en" ? "Save 12%" : "Ahorra 12%")
    : null;

  const specs = [
    car.transmission ?? null,
    car.fuel ?? null,
    car.seat ? `${car.seat} ${locale === "en" ? "seats" : "pasajeros"}` : null,
  ].filter(Boolean).join(" · ");

  return (
    <AnimatedPressable
      onPress={onPress}
      onPressIn={() => { scale.value = withTiming(0.98, { duration: 90 }); }}
      onPressOut={() => { scale.value = withTiming(1, { duration: 140 }); }}
      style={[animStyle, styles.carCard]}
    >
      {/* Image */}
      <View style={styles.imageBox}>
        {car.image ? (
          <Image source={{ uri: car.image }} style={styles.carImg} resizeMode="cover" />
        ) : (
          <View style={[styles.carImg, styles.imgPlaceholder]}>
            <Ionicons name="car-sport" size={52} color={colors.textFaint} />
          </View>
        )}
        {badge ? (
          <View style={styles.badge}>
            <Text style={styles.badgeText}>{badge}</Text>
          </View>
        ) : null}
        <Pressable style={styles.heartBtn} hitSlop={8}>
          <Ionicons name="heart-outline" size={18} color={colors.text} />
        </Pressable>
      </View>

      {/* Info */}
      <View style={styles.infoRow}>
        <View style={{ flex: 1 }}>
          {car.brand ? (
            <Text style={styles.carBrand} numberOfLines={1}>{car.brand}</Text>
          ) : null}
          <Text style={styles.carModel} numberOfLines={1}>
            {car.name ?? car.model ?? ""}
          </Text>
          {specs ? (
            <Text style={styles.carSpecs} numberOfLines={1}>{specs}</Text>
          ) : null}
          {/* Rating (mock) */}
          <View style={styles.ratingRow}>
            <Text style={styles.starIcon}>★</Text>
            <Text style={styles.ratingText}>4.9</Text>
          </View>
        </View>
        <View style={styles.priceBlock}>
          <Text style={styles.priceAmount}>{money(car.price_day ?? car.price)}</Text>
          <Text style={styles.pricePer}>{t("cars.perDay")}</Text>
          <Pressable onPress={onPress} style={styles.pickBtn}>
            <Text style={styles.pickBtnText}>
              {i18n.locale === "en" ? "Choose" : "Elegir"}
            </Text>
            <Ionicons name="chevron-forward" size={14} color="#FFFFFF" />
          </Pressable>
        </View>
      </View>
    </AnimatedPressable>
  );
}

export default function CarsScreen() {
  const router = useRouter();
  const { unread } = useNotificationsCtx();
  const [cars, setCars] = useState<Car[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [err, setErr] = useState<string | null>(null);
  const [q, setQ] = useState("");
  const [category, setCategory] = useState("Todos");
  const [filterOpen, setFilterOpen] = useState(false);
  const [filtered, setFiltered] = useState(false);
  const [start, setStart] = useState<Date>(defaultStart());
  const [end, setEnd] = useState<Date>(defaultEnd());
  const [showStart, setShowStart] = useState(false);
  const [showEnd, setShowEnd] = useState(false);
  const [pendingDate, setPendingDate] = useState<Date | null>(null);
  const [pendingField, setPendingField] = useState<"start" | "end" | null>(null);
  const [showTime, setShowTime] = useState(false);

  const locale = i18n.locale === "en" ? "en" : "es";

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

  const displayed = useMemo(() => {
    if (category === "Todos") return cars;
    return cars.filter((c) => {
      const hay = `${c.type ?? ""} ${c.transmission ?? ""} ${c.name ?? ""} ${c.model ?? ""}`.toLowerCase();
      const needle = category.toLowerCase();
      return hay.includes(needle);
    });
  }, [cars, category]);

  const handleDateChange = (e: DateTimePickerEvent, d?: Date) => {
    if (Platform.OS === "ios") {
      if (!d) return;
      if (pendingField === "start") { setStart(d); if (d >= end) setEnd(new Date(d.getTime() + 3 * 86400000)); }
      else if (pendingField === "end") setEnd(d);
      setShowStart(false); setShowEnd(false);
    } else {
      if (e.type !== "set" || !d) { setShowStart(false); setShowEnd(false); return; }
      setPendingDate(d); setShowStart(false); setShowEnd(false); setShowTime(true);
    }
  };
  const handleTimeChange = (e: DateTimePickerEvent, d?: Date) => {
    setShowTime(false);
    if (e.type !== "set" || !d || !pendingDate) return;
    const combined = new Date(pendingDate);
    combined.setHours(d.getHours(), d.getMinutes(), 0, 0);
    if (pendingField === "start") { setStart(combined); if (combined >= end) setEnd(new Date(combined.getTime() + 3 * 86400000)); }
    else setEnd(combined);
    setPendingDate(null); setPendingField(null);
  };

  const renderHeader = () => (
    <View>
      {/* Eyebrow + heading */}
      <View style={styles.headingBlock}>
        <Text style={styles.eyebrow}>
          {locale === "en" ? "CHOOSE YOUR COMPANION" : "ELIGE TU COMPAÑERO"}
        </Text>
        <Text style={styles.heading}>
          {locale === "en" ? "Move your way." : "Muévete a tu manera."}
        </Text>
      </View>

      {/* Search + filter */}
      <View style={styles.searchRow}>
        <View style={styles.searchBar}>
          <Ionicons name="search-outline" size={18} color={colors.textMuted} />
          <TextInput
            placeholder={locale === "en" ? "Search model" : "Buscar modelo"}
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
        <Pressable
          style={[styles.filterBtn, filterOpen && styles.filterBtnActive]}
          onPress={() => setFilterOpen((v) => !v)}
        >
          <Ionicons name="funnel" size={18} color={filterOpen ? "#FFFFFF" : colors.text} />
        </Pressable>
      </View>

      {/* Category pills */}
      <View style={styles.pillsRow}>
        {CATEGORIES.map((c) => (
          <Pressable
            key={c}
            onPress={() => setCategory(c)}
            style={[styles.pill, c === category && styles.pillActive]}
          >
            <Text style={[styles.pillText, c === category && styles.pillTextActive]}>{c}</Text>
          </Pressable>
        ))}
      </View>

      {/* Date filter panel */}
      {filterOpen && (
        <View style={styles.filterPanel}>
          <Text style={styles.filterLabel}>
            {locale === "en" ? "RENTAL PERIOD" : "PERÍODO DE RENTA"}
          </Text>
          <View style={styles.dateRow}>
            <Pressable
              onPress={() => { setPendingField("start"); setShowStart(true); }}
              style={styles.datePill}
            >
              <Text style={styles.datePillLabel}>{locale === "en" ? "From" : "Desde"}</Text>
              <Text style={styles.datePillDate}>{fmtDate(start)}</Text>
              <Text style={styles.datePillTime}>{fmtTime(start)}</Text>
            </Pressable>
            <View style={styles.dateArrow}>
              <Ionicons name="arrow-forward" size={16} color={colors.textMuted} />
            </View>
            <Pressable
              onPress={() => { setPendingField("end"); setShowEnd(true); }}
              style={styles.datePill}
            >
              <Text style={styles.datePillLabel}>{locale === "en" ? "Until" : "Hasta"}</Text>
              <Text style={styles.datePillDate}>{fmtDate(end)}</Text>
              <Text style={styles.datePillTime}>{fmtTime(end)}</Text>
            </Pressable>
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
          <View style={styles.filterBtnsRow}>
            {filtered ? (
              <Pressable
                style={styles.filterClear}
                onPress={() => { setFiltered(false); setFilterOpen(false); load(); }}
              >
                <Text style={styles.filterClearText}>
                  {locale === "en" ? "Clear" : "Limpiar"}
                </Text>
              </Pressable>
            ) : null}
            <Pressable
              style={styles.filterApply}
              onPress={() => { setFiltered(true); setFilterOpen(false); load(); }}
            >
              <Ionicons name="search" size={16} color="#FFFFFF" />
              <Text style={styles.filterApplyText}>
                {t("cars.applyFilter")}
              </Text>
            </Pressable>
          </View>
        </View>
      )}

      {filtered ? (
        <View style={styles.activeFilterRow}>
          <Ionicons name="calendar" size={14} color={colors.cta} />
          <Text style={styles.activeFilterText}>
            {fmtDate(start)} → {fmtDate(end)}
          </Text>
          <Pressable hitSlop={8} onPress={() => { setFiltered(false); load(); }}>
            <Ionicons name="close-circle" size={18} color={colors.textMuted} />
          </Pressable>
        </View>
      ) : null}

      {!loading && (
        <Text style={styles.countText}>
          {displayed.length} {locale === "en" ? "vehicles" : "vehículos"}
        </Text>
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
      <ScreenHeader
        title={locale === "en" ? "Our fleet" : "Nuestra flota"}
        subtitle={locale === "en" ? "Ready for your next trip." : "Lista para tu próxima ruta."}
        right={
          <BellButton
            unread={unread}
            onPress={() => router.push("/(client)/notifications")}
          />
        }
      />
      <FlatList
        data={loading ? [] : displayed}
        keyExtractor={(c) => String(c.id)}
        contentContainerStyle={styles.listContent}
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
            <ListSkeleton count={3} />
          ) : (
            <EmptyState
              title={t("common.empty")}
              subtitle={filtered ? t("cars.clearFilter") : undefined}
              icon="cars"
            />
          )
        }
        renderItem={({ item, index }) => (
          <CarCard
            car={item}
            index={index}
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

  headingBlock: { paddingHorizontal: spacing.xl, paddingTop: spacing.xl, paddingBottom: spacing.md },
  eyebrow: { ...type.label, color: colors.cta, marginBottom: 8 },
  heading: { ...type.display, color: colors.text, fontSize: 28, letterSpacing: -0.6 },

  searchRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 10,
    paddingHorizontal: spacing.xl,
    marginBottom: spacing.md,
  },
  searchBar: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    backgroundColor: colors.card,
    borderRadius: radius.md,
    paddingHorizontal: 14,
    height: 52,
    borderWidth: 1,
    borderColor: colors.border,
    ...shadow.xs,
  },
  searchInput: { ...type.bodyMed, color: colors.text, flex: 1, height: 52 },
  filterBtn: {
    width: 52,
    height: 52,
    borderRadius: radius.md,
    backgroundColor: colors.dark,
    alignItems: "center",
    justifyContent: "center",
  },
  filterBtnActive: { backgroundColor: colors.cta },

  pillsRow: { flexDirection: "row", gap: 8, paddingHorizontal: spacing.xl, marginBottom: spacing.md },
  pill: {
    paddingHorizontal: 18,
    paddingVertical: 9,
    borderRadius: radius.full,
    backgroundColor: colors.card,
    borderWidth: 1.5,
    borderColor: colors.border,
  },
  pillActive: { backgroundColor: colors.cta, borderColor: colors.cta },
  pillText: { ...type.captionMed, color: colors.text },
  pillTextActive: { color: "#FFFFFF" },

  // Filter panel
  filterPanel: {
    backgroundColor: colors.card,
    marginHorizontal: spacing.xl,
    marginBottom: spacing.md,
    padding: spacing.lg,
    borderRadius: radius.lg,
    borderWidth: 1,
    borderColor: colors.border,
    ...shadow.sm,
  },
  filterLabel: { ...type.label, color: colors.textMuted, marginBottom: 12 },
  dateRow: { flexDirection: "row", alignItems: "center", gap: 8, marginBottom: spacing.md },
  dateArrow: { paddingHorizontal: 4 },
  datePill: {
    flex: 1,
    backgroundColor: colors.bg,
    borderRadius: radius.md,
    padding: 12,
    borderWidth: 1,
    borderColor: colors.border,
  },
  datePillLabel: { ...type.label, color: colors.textMuted, fontSize: 10, marginBottom: 5 },
  datePillDate: { ...type.title, color: colors.text },
  datePillTime: { ...type.caption, color: colors.textSecondary, marginTop: 1 },
  filterBtnsRow: { flexDirection: "row", gap: 8 },
  filterClear: {
    paddingHorizontal: 16,
    height: 46,
    borderRadius: radius.md,
    alignItems: "center",
    justifyContent: "center",
    borderWidth: 1.5,
    borderColor: colors.border,
    backgroundColor: colors.bg,
  },
  filterClearText: { ...type.captionMed, color: colors.textSecondary },
  filterApply: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 8,
    height: 46,
    borderRadius: radius.md,
    backgroundColor: colors.cta,
  },
  filterApplyText: { ...type.title, color: "#FFFFFF" },

  activeFilterRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    marginHorizontal: spacing.xl,
    marginBottom: spacing.md,
    padding: 10,
    backgroundColor: colors.ctaXLight,
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: colors.ctaLight,
  },
  activeFilterText: { ...type.captionMed, color: colors.cta, flex: 1 },

  countText: { ...type.label, color: colors.textMuted, paddingHorizontal: spacing.xl, paddingBottom: spacing.sm },

  errBox: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    marginHorizontal: spacing.xl,
    marginBottom: spacing.sm,
    padding: 14,
    backgroundColor: colors.dangerBg,
    borderRadius: radius.md,
  },
  errText: { ...type.caption, color: colors.danger, flex: 1 },

  // Car card
  carCard: {
    marginHorizontal: spacing.xl,
    marginBottom: spacing.md,
    backgroundColor: colors.card,
    borderRadius: radius.xl,
    overflow: "hidden",
    ...shadow.md,
  },
  imageBox: { position: "relative", height: 200 },
  carImg: { width: "100%", height: 200, backgroundColor: colors.borderLight },
  imgPlaceholder: { alignItems: "center", justifyContent: "center" },
  badge: {
    position: "absolute",
    top: 12,
    left: 12,
    backgroundColor: "rgba(0,0,0,0.80)",
    borderRadius: radius.full,
    paddingHorizontal: 12,
    paddingVertical: 6,
  },
  badgeText: { ...type.small, color: "#FFFFFF" },
  heartBtn: {
    position: "absolute",
    top: 10,
    right: 10,
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: "rgba(255,255,255,0.9)",
    alignItems: "center",
    justifyContent: "center",
    ...shadow.sm,
  },

  infoRow: {
    flexDirection: "row",
    alignItems: "flex-end",
    padding: 16,
    gap: 12,
  },
  carBrand: { ...type.label, color: colors.textMuted, marginBottom: 3, fontSize: 10 },
  carModel: { ...type.h3, color: colors.text },
  carSpecs: { ...type.caption, color: colors.textSecondary, marginTop: 2 },
  ratingRow: { flexDirection: "row", alignItems: "center", gap: 4, marginTop: 6 },
  starIcon: { fontSize: 14, color: "#F59E0B" },
  ratingText: { ...type.captionMed, color: colors.text },

  priceBlock: { alignItems: "flex-end", gap: 4 },
  priceAmount: { fontFamily: font.extrabold, fontSize: 20, color: colors.text },
  pricePer: { ...type.caption, color: colors.textMuted, marginTop: -2 },
  pickBtn: {
    flexDirection: "row",
    alignItems: "center",
    gap: 4,
    backgroundColor: colors.dark,
    borderRadius: radius.full,
    paddingHorizontal: 14,
    paddingVertical: 8,
    marginTop: 6,
  },
  pickBtnText: { fontFamily: font.bold, fontSize: 13, color: "#FFFFFF" },
});
