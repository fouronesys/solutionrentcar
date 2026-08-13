/**
 * Autos — vehicle catalog.
 * Design: white header, category pills (red active), large 16:10 car cards.
 */
import React, { useCallback, useEffect, useMemo, useState } from "react";
import {
  Dimensions,
  FlatList,
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
import { Image } from "expo-image";
import { Ionicons } from "@expo/vector-icons";
import Animated, {
  useAnimatedStyle,
  useSharedValue,
  withTiming,
} from "react-native-reanimated";
import DateTimePicker, {
  DateTimePickerEvent,
} from "@react-native-community/datetimepicker";
import { EmptyState } from "@/components/EmptyState";
import { ListSkeleton } from "@/components/Skeleton";
import { BellButton, ScreenHeader } from "@/components/ScreenHeader";
import { api, ApiError } from "@/api/client";
import {
  carStatus,
  colors,
  font,
  radius,
  shadow,
  spacing,
  type,
} from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";
import type { Car } from "@/api/types";
import { i18n, t } from "@/i18n";
import { money, toDbDateTime } from "@/utils/format";
import { useNotificationsCtx } from "@/notifications/NotificationsContext";

const { width: SW } = Dimensions.get("window");
// 16:10 image height — card spans full width minus xl margins on each side
const CARD_IMG_H = Math.round((SW - spacing.xl * 2) * (10 / 16));

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
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
}
function fmtTime(d: Date) {
  return d.toLocaleTimeString(i18n.locale === "en" ? "en-US" : "es-ES", {
    hour: "2-digit",
    minute: "2-digit",
  });
}
function daysBetween(a: Date, b: Date) {
  return Math.max(1, Math.ceil((b.getTime() - a.getTime()) / 86400000));
}

const CATEGORIES = ["Todos", "SUV", "Sedán", "Económico"];

// Derive a category badge label from the car data
function getCategoryLabel(car: Car): string | null {
  const hay =
    `${car.type ?? ""} ${car.name ?? ""} ${car.model ?? ""} ${car.transmission ?? ""}`.toLowerCase();
  if (
    hay.includes("suv") ||
    hay.includes("jeep") ||
    hay.includes("pickup") ||
    hay.includes("4x4") ||
    hay.includes("highlander") ||
    hay.includes("pilot") ||
    hay.includes("pathfinder") ||
    hay.includes("prado") ||
    hay.includes("runner") ||
    hay.includes("tahoe") ||
    hay.includes("sorento") ||
    hay.includes("santa fe") ||
    hay.includes("sportage") ||
    hay.includes("tucson") ||
    hay.includes("fortuner")
  )
    return "SUV";
  if (
    hay.includes("sedán") ||
    hay.includes("sedan") ||
    hay.includes("corolla") ||
    hay.includes("civic") ||
    hay.includes("accord") ||
    hay.includes("altima") ||
    hay.includes("sentra") ||
    hay.includes("camry")
  )
    return "Sedán";
  if (
    hay.includes("económico") ||
    hay.includes("economico") ||
    hay.includes("compact") ||
    hay.includes("fit") ||
    hay.includes("swift") ||
    hay.includes("yaris") ||
    hay.includes("march") ||
    hay.includes("sonet")
  )
    return "Económico";
  // Fall back to car.type if set
  return car.type ? car.type : null;
}

// Spec icon pills for the card
function SpecPills({
  car,
  locale,
}: {
  car: Car;
  locale: string;
}) {
  const specs: { icon: keyof typeof Ionicons.glyphMap; label: string }[] = [];
  if (car.seat)
    specs.push({
      icon: "people-outline",
      label: `${car.seat} ${locale === "en" ? "seats" : "pas."}`,
    });
  if (car.transmission)
    specs.push({ icon: "cog-outline", label: car.transmission });
  if (car.fuel) specs.push({ icon: "water-outline", label: car.fuel });
  return (
    <View style={specPillStyle.row}>
      {specs.map((s, i) => (
        <View key={i} style={specPillStyle.pill}>
          <Ionicons name={s.icon} size={11} color={colors.textMuted} />
          <Text style={specPillStyle.text}>{s.label}</Text>
        </View>
      ))}
    </View>
  );
}
const specPillStyle = StyleSheet.create({
  row: { flexDirection: "row", flexWrap: "wrap", gap: 5, marginTop: 6 },
  pill: {
    flexDirection: "row",
    alignItems: "center",
    gap: 4,
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: radius.full,
    backgroundColor: colors.cardAlt,
    borderWidth: 1,
    borderColor: colors.borderLight,
  },
  text: {
    fontFamily: font.medium,
    fontSize: 11,
    color: colors.textSecondary,
    lineHeight: 14,
  },
});

// ─── Car card ─────────────────────────────────────────────────────────────────
const CarCard = React.memo(function CarCard({
  car,
  onPress,
  index,
  filtered,
  days,
}: {
  car: Car;
  onPress: () => void;
  index: number;
  filtered: boolean;
  days: number;
}) {
  const styles = useThemedStyles(makeStyles);
  const locale = i18n.locale === "en" ? "en" : "es";
  const scale = useSharedValue(1);
  const animStyle = useAnimatedStyle(() => ({
    transform: [{ scale: scale.value }],
  }));

  const available = filtered || Number(car.status ?? 0) === 0;
  const chipStatus = carStatus[0];
  const perDay = Number(car.price_day ?? car.price ?? 0);
  const total = perDay * days;

  // Featured badge (first two cards)
  const featuredBadge =
    index === 0
      ? locale === "en"
        ? "Most popular"
        : "Más elegido"
      : index === 1 && car.price_day
      ? locale === "en"
        ? "Save 12%"
        : "Ahorra 12%"
      : null;

  // Category badge from car data
  const categoryLabel = getCategoryLabel(car);

  return (
    <AnimatedPressable
      onPress={onPress}
      onPressIn={() => {
        scale.value = withTiming(0.97, { duration: 90 });
      }}
      onPressOut={() => {
        scale.value = withTiming(1, { duration: 160 });
      }}
      style={[animStyle, styles.carCard]}
    >
      {/* ── Image 16:10 ────────────────────────────────────────── */}
      <View style={styles.imageBox}>
        {car.image ? (
          <Image
            source={{ uri: car.image }}
            style={styles.carImg}
            contentFit="cover"
            transition={180}
            cachePolicy="memory-disk"
            recyclingKey={String(car.id ?? car.image)}
          />
        ) : (
          <View style={[styles.carImg, styles.imgPlaceholder]}>
            <Ionicons name="car-sport" size={56} color={colors.textFaint} />
          </View>
        )}

        {/* Category badge — top left */}
        {categoryLabel ? (
          <View style={styles.categoryBadge}>
            <Text style={styles.categoryBadgeText}>{categoryLabel}</Text>
          </View>
        ) : featuredBadge ? (
          <View style={styles.featuredBadge}>
            <Text style={styles.featuredBadgeText}>{featuredBadge}</Text>
          </View>
        ) : null}

        {/* Wishlist button — top right */}
        <Pressable style={styles.heartBtn} hitSlop={8}>
          <Ionicons name="heart-outline" size={18} color={colors.text} />
        </Pressable>

        {/* Bottom scrim + availability */}
        {!available ? (
          <View style={styles.unavailableOverlay}>
            <Text style={styles.unavailableText}>
              {t("cars.unavailable")}
            </Text>
          </View>
        ) : null}
      </View>

      {/* ── Info ──────────────────────────────────────────────── */}
      <View style={styles.infoRow}>
        <View style={{ flex: 1 }}>
          {car.brand ? (
            <Text style={styles.carBrand} numberOfLines={1}>
              {car.brand}
            </Text>
          ) : null}
          <Text style={styles.carModel} numberOfLines={1}>
            {car.name ?? car.model ?? ""}
          </Text>

          {/* Spec pills */}
          <SpecPills car={car} locale={locale} />

          {/* Rating */}
          <View style={styles.ratingRow}>
            <Text style={styles.starIcon}>★</Text>
            <Text style={styles.ratingText}>4.9</Text>
            {available ? (
              <View style={[styles.availChip, styles.availChipOn]}>
                <View
                  style={[
                    styles.availDot,
                    { backgroundColor: chipStatus.color },
                  ]}
                />
                <Text style={[styles.availText, styles.availTextOn]}>
                  {t("cars.available")}
                </Text>
              </View>
            ) : null}
          </View>
        </View>

        {/* Price block — dominant */}
        <View style={styles.priceBlock}>
          <Text style={styles.priceAmount}>
            {money(car.price_day ?? car.price)}
          </Text>
          <Text style={styles.pricePer}>{t("cars.perDay")}</Text>
          {filtered ? (
            <Text style={styles.priceTotal} numberOfLines={2}>
              {money(total)}{"\n"}
              <Text style={{ fontSize: 10 }}>
                {days} {locale === "en" ? "days" : "días"}
              </Text>
            </Text>
          ) : null}
          <Pressable onPress={onPress} style={styles.pickBtn}>
            <Text style={styles.pickBtnText}>
              {locale === "en" ? "Choose" : "Elegir"}
            </Text>
            <Ionicons name="chevron-forward" size={13} color="#FFFFFF" />
          </Pressable>
        </View>
      </View>
    </AnimatedPressable>
  );
});

// ─── Screen ────────────────────────────────────────────────────────────────────
export default function CarsScreen() {
  const styles = useThemedStyles(makeStyles);
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
  const [start, setStart] = useState<Date>(defaultStart);
  const [end, setEnd] = useState<Date>(defaultEnd);
  const [showStart, setShowStart] = useState(false);
  const [showEnd, setShowEnd] = useState(false);
  const [pendingDate, setPendingDate] = useState<Date | null>(null);
  const [pendingField, setPendingField] = useState<"start" | "end" | null>(
    null,
  );
  const [showTime, setShowTime] = useState(false);

  const locale = i18n.locale === "en" ? "en" : "es";
  const days = useMemo(() => daysBetween(start, end), [start, end]);

  const load = useCallback(async () => {
    setErr(null);
    try {
      const params: Record<string, string | number | undefined> = {
        limit: 50,
        q: q || undefined,
      };
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

  useEffect(() => {
    load();
  }, [load]);

  const displayed = useMemo(() => {
    if (category === "Todos") return cars;
    return cars.filter((c) => {
      const hay =
        `${c.type ?? ""} ${c.transmission ?? ""} ${c.name ?? ""} ${c.model ?? ""}`.toLowerCase();
      const needle = category.toLowerCase();
      return hay.includes(needle);
    });
  }, [cars, category]);

  const handleDateChange = (e: DateTimePickerEvent, d?: Date) => {
    if (Platform.OS === "ios") {
      if (!d) return;
      if (pendingField === "start") {
        setStart(d);
        if (d >= end) setEnd(new Date(d.getTime() + 3 * 86400000));
      } else if (pendingField === "end") setEnd(d);
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
    } else setEnd(combined);
    setPendingDate(null);
    setPendingField(null);
  };

  const renderHeader = () => (
    <View>
      {/* Heading */}
      <View style={styles.headingBlock}>
        <Text style={styles.eyebrow}>
          {locale === "en" ? "CHOOSE YOUR COMPANION" : "ELIGE TU COMPAÑERO"}
        </Text>
        <Text style={styles.heading}>
          {locale === "en" ? "Move your way." : "Muévete a tu manera."}
        </Text>
      </View>

      {/* Search + filter toggle */}
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
            <Pressable
              onPress={() => {
                setQ("");
                load();
              }}
              hitSlop={8}
            >
              <Ionicons
                name="close-circle"
                size={18}
                color={colors.textMuted}
              />
            </Pressable>
          ) : null}
        </View>
        <Pressable
          style={[styles.filterBtn, filterOpen && styles.filterBtnActive]}
          onPress={() => setFilterOpen((v) => !v)}
        >
          <Ionicons
            name="funnel"
            size={18}
            color={filterOpen ? "#FFFFFF" : colors.text}
          />
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
            <Text
              style={[
                styles.pillText,
                c === category && styles.pillTextActive,
              ]}
            >
              {c}
            </Text>
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
              onPress={() => {
                setPendingField("start");
                setShowStart(true);
              }}
              style={styles.datePill}
            >
              <Text style={styles.datePillLabel}>
                {locale === "en" ? "From" : "Desde"}
              </Text>
              <Text style={styles.datePillDate}>{fmtDate(start)}</Text>
              <Text style={styles.datePillTime}>{fmtTime(start)}</Text>
            </Pressable>
            <View style={styles.dateArrow}>
              <Ionicons
                name="arrow-forward"
                size={16}
                color={colors.textMuted}
              />
            </View>
            <Pressable
              onPress={() => {
                setPendingField("end");
                setShowEnd(true);
              }}
              style={styles.datePill}
            >
              <Text style={styles.datePillLabel}>
                {locale === "en" ? "Until" : "Hasta"}
              </Text>
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
                onPress={() => {
                  setFiltered(false);
                  setFilterOpen(false);
                  load();
                }}
              >
                <Text style={styles.filterClearText}>
                  {locale === "en" ? "Clear" : "Limpiar"}
                </Text>
              </Pressable>
            ) : null}
            <Pressable
              style={styles.filterApply}
              onPress={() => {
                setFiltered(true);
                setFilterOpen(false);
                load();
              }}
            >
              <Ionicons name="search" size={16} color="#FFFFFF" />
              <Text style={styles.filterApplyText}>
                {t("cars.applyFilter")}
              </Text>
            </Pressable>
          </View>
        </View>
      )}

      {/* Active filter row */}
      {filtered ? (
        <View style={styles.activeFilterRow}>
          <Ionicons name="calendar" size={14} color={colors.cta} />
          <Text style={styles.activeFilterText}>
            {fmtDate(start)} → {fmtDate(end)}
          </Text>
          <Pressable
            hitSlop={8}
            onPress={() => {
              setFiltered(false);
              load();
            }}
          >
            <Ionicons
              name="close-circle"
              size={18}
              color={colors.textMuted}
            />
          </Pressable>
        </View>
      ) : null}

      {!loading && (
        <Text style={styles.countText}>
          {displayed.length}{" "}
          {locale === "en" ? "vehicles" : "vehículos"}
        </Text>
      )}

      {err ? (
        <View style={styles.errBox}>
          <Ionicons
            name="warning-outline"
            size={16}
            color={colors.danger}
          />
          <Text style={styles.errText}>{err}</Text>
        </View>
      ) : null}
    </View>
  );

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <ScreenHeader
        title={locale === "en" ? "Our fleet" : "Nuestra flota"}
        subtitle={
          locale === "en"
            ? "Ready for your next trip."
            : "Lista para tu próxima ruta."
        }
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
            onRefresh={() => {
              setRefreshing(true);
              load();
            }}
            tintColor={colors.cta}
          />
        }
        ListEmptyComponent={
          loading ? (
            <ListSkeleton count={3} />
          ) : (
            <EmptyState
              title={
                locale === "en"
                  ? "No cars available"
                  : "Sin autos disponibles"
              }
              subtitle={
                filtered
                  ? t("cars.clearFilter")
                  : locale === "en"
                  ? "Pull to refresh or try a different search"
                  : "Recarga o prueba otra búsqueda"
              }
              icon="cars"
              action={
                locale === "en" ? "Explore all" : "Ver toda la flota"
              }
              onAction={() => {
                setFiltered(false);
                setCategory("Todos");
                setQ("");
                load();
              }}
            />
          )
        }
        renderItem={({ item, index }) => (
          <CarCard
            car={item}
            index={index}
            filtered={filtered}
            days={days}
            onPress={() => {
              if (filtered) {
                router.push({
                  pathname: "/(client)/book/[carId]",
                  params: {
                    carId: String(item.id),
                    start: toDbDateTime(start),
                    end: toDbDateTime(end),
                  },
                });
              } else {
                router.push({
                  pathname: "/(client)/car/[id]",
                  params: { id: String(item.id) },
                });
              }
            }}
          />
        )}
      />
    </SafeAreaView>
  );
}

// ─── Styles ───────────────────────────────────────────────────────────────────
const makeStyles = () =>
  StyleSheet.create({
    screen: { flex: 1, backgroundColor: colors.bg },
    listContent: { paddingBottom: 28 },

    headingBlock: {
      paddingHorizontal: spacing.xl,
      paddingTop: spacing.xl,
      paddingBottom: spacing.md,
    },
    eyebrow: { ...type.label, color: colors.cta, marginBottom: 8 },
    heading: {
      ...type.display,
      color: colors.text,
      fontSize: 28,
      letterSpacing: -0.6,
    },

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
    searchInput: {
      ...type.bodyMed,
      color: colors.text,
      flex: 1,
      height: 52,
    },
    filterBtn: {
      width: 52,
      height: 52,
      borderRadius: radius.md,
      backgroundColor: colors.dark,
      alignItems: "center",
      justifyContent: "center",
    },
    filterBtnActive: { backgroundColor: colors.cta },

    pillsRow: {
      flexDirection: "row",
      gap: 8,
      paddingHorizontal: spacing.xl,
      marginBottom: spacing.md,
    },
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
    dateRow: {
      flexDirection: "row",
      alignItems: "center",
      gap: 8,
      marginBottom: spacing.md,
    },
    dateArrow: { paddingHorizontal: 4 },
    datePill: {
      flex: 1,
      backgroundColor: colors.bg,
      borderRadius: radius.md,
      padding: 12,
      borderWidth: 1,
      borderColor: colors.border,
    },
    datePillLabel: {
      ...type.label,
      color: colors.textMuted,
      fontSize: 10,
      marginBottom: 5,
    },
    datePillDate: { ...type.title, color: colors.text },
    datePillTime: {
      ...type.caption,
      color: colors.textSecondary,
      marginTop: 1,
    },
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
    activeFilterText: {
      ...type.captionMed,
      color: colors.cta,
      flex: 1,
    },

    countText: {
      ...type.label,
      color: colors.textMuted,
      paddingHorizontal: spacing.xl,
      paddingBottom: spacing.sm,
    },

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

    // ── Car card ──────────────────────────────────────────────────────────────
    carCard: {
      marginHorizontal: spacing.xl,
      marginBottom: spacing.md,
      backgroundColor: colors.card,
      borderRadius: radius.xl,
      overflow: "hidden",
      ...shadow.md,
    },
    imageBox: {
      position: "relative",
      height: CARD_IMG_H,
      backgroundColor: colors.borderLight,
    },
    carImg: { width: "100%", height: CARD_IMG_H },
    imgPlaceholder: { alignItems: "center", justifyContent: "center" },

    categoryBadge: {
      position: "absolute",
      top: 12,
      left: 12,
      backgroundColor: colors.cta,
      borderRadius: radius.full,
      paddingHorizontal: 12,
      paddingVertical: 5,
    },
    categoryBadgeText: {
      fontFamily: font.bold,
      fontSize: 11,
      color: "#FFFFFF",
      letterSpacing: 0.3,
    },
    featuredBadge: {
      position: "absolute",
      top: 12,
      left: 12,
      backgroundColor: "rgba(0,0,0,0.80)",
      borderRadius: radius.full,
      paddingHorizontal: 12,
      paddingVertical: 5,
    },
    featuredBadgeText: {
      fontFamily: font.semibold,
      fontSize: 11,
      color: "#FFFFFF",
    },
    heartBtn: {
      position: "absolute",
      top: 10,
      right: 10,
      width: 36,
      height: 36,
      borderRadius: 18,
      backgroundColor: "rgba(255,255,255,0.92)",
      alignItems: "center",
      justifyContent: "center",
      ...shadow.sm,
    },
    unavailableOverlay: {
      position: "absolute",
      bottom: 0,
      left: 0,
      right: 0,
      padding: 8,
      backgroundColor: "rgba(0,0,0,0.55)",
      alignItems: "center",
    },
    unavailableText: {
      fontFamily: font.semibold,
      fontSize: 12,
      color: "#FFFFFF",
    },

    infoRow: {
      flexDirection: "row",
      alignItems: "flex-end",
      padding: 16,
      gap: 12,
    },
    carBrand: {
      ...type.label,
      color: colors.textMuted,
      marginBottom: 2,
      fontSize: 10,
    },
    carModel: { ...type.h3, color: colors.text },
    ratingRow: {
      flexDirection: "row",
      alignItems: "center",
      gap: 4,
      marginTop: 8,
      flexWrap: "wrap",
    },
    starIcon: { fontSize: 13, color: "#F59E0B" },
    ratingText: { ...type.captionMed, color: colors.text, marginRight: 6 },
    availChip: {
      flexDirection: "row",
      alignItems: "center",
      gap: 4,
      paddingHorizontal: 8,
      paddingVertical: 3,
      borderRadius: radius.full,
    },
    availChipOn: { backgroundColor: carStatus[0].bg },
    availDot: { width: 6, height: 6, borderRadius: 3 },
    availText: { fontFamily: font.medium, fontSize: 11, lineHeight: 14 },
    availTextOn: { color: carStatus[0].color },

    // Price — dominant element
    priceBlock: { alignItems: "flex-end", gap: 3 },
    priceAmount: { fontFamily: font.extrabold, fontSize: 22, color: colors.text },
    pricePer: { ...type.caption, color: colors.textMuted, marginTop: -2 },
    priceTotal: {
      ...type.small,
      color: colors.textSecondary,
      textAlign: "right",
      maxWidth: 120,
      marginTop: 2,
    },
    pickBtn: {
      flexDirection: "row",
      alignItems: "center",
      gap: 4,
      backgroundColor: colors.cta,
      borderRadius: radius.full,
      paddingHorizontal: 14,
      paddingVertical: 9,
      marginTop: 8,
      ...shadow.cta,
    },
    pickBtnText: { fontFamily: font.bold, fontSize: 13, color: "#FFFFFF" },
  });
