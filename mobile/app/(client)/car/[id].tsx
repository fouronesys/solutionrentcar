/**
 * Car detail screen.
 * Design: swipeable photo carousel with 1/N counter, horizontal spec strip,
 * inline date-range selector before the "Book" CTA.
 */
import React, { useEffect, useRef, useState } from "react";
import {
  Dimensions,
  FlatList,
  NativeScrollEvent,
  NativeSyntheticEvent,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from "react-native";
import { Image } from "expo-image";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { StatusBar } from "expo-status-bar";
import { Button } from "@/components/Button";
import { Loading } from "@/components/Loading";
import { RangeCalendar } from "@/components/RangeCalendar";
import { api, ApiError } from "@/api/client";
import type { Car } from "@/api/types";
import {
  carStatus,
  colors,
  font,
  gradients,
  radius,
  shadow,
  spacing,
  type,
} from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";
import { i18n, t } from "@/i18n";
import { money, toDbDateTime } from "@/utils/format";

const { width: SW } = Dimensions.get("window");
const GALLERY_H = 320;

type Spec = { icon: keyof typeof Ionicons.glyphMap; value: string; label: string };

// Key specs shown as icon pills in the horizontal strip
function buildKeySpecs(car: Car, locale: string): Spec[] {
  return [
    car.seat
      ? {
          icon: "people-outline" as const,
          value: String(car.seat),
          label: locale === "en" ? "Seats" : "Asientos",
        }
      : null,
    car.transmission
      ? {
          icon: "cog-outline" as const,
          value: car.transmission,
          label: locale === "en" ? "Transmission" : "Transmisión",
        }
      : null,
    car.fuel
      ? {
          icon: "water-outline" as const,
          value: car.fuel,
          label: locale === "en" ? "Fuel" : "Combustible",
        }
      : null,
    car.year
      ? {
          icon: "calendar-outline" as const,
          value: String(car.year),
          label: locale === "en" ? "Year" : "Año",
        }
      : null,
  ].filter(Boolean) as Spec[];
}

// Full spec grid for "Specifications" section
function buildFullSpecs(car: Car, locale: string): Spec[] {
  return [
    car.year
      ? { icon: "calendar-outline" as const, value: String(car.year), label: locale === "en" ? "Year" : "Año" }
      : null,
    car.transmission
      ? { icon: "cog-outline" as const, value: car.transmission, label: locale === "en" ? "Transmission" : "Transmisión" }
      : null,
    car.fuel
      ? { icon: "water-outline" as const, value: car.fuel, label: locale === "en" ? "Fuel" : "Combustible" }
      : null,
    car.seat
      ? { icon: "people-outline" as const, value: String(car.seat), label: locale === "en" ? "Seats" : "Asientos" }
      : null,
    car.color
      ? { icon: "color-palette-outline" as const, value: car.color, label: locale === "en" ? "Color" : "Color" }
      : null,
    car.plate
      ? { icon: "card-outline" as const, value: car.plate, label: locale === "en" ? "Plate" : "Placa" }
      : null,
  ].filter(Boolean) as Spec[];
}

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
function fmtShort(d: Date, locale: string) {
  return d.toLocaleDateString(locale === "en" ? "en-US" : "es-ES", {
    day: "2-digit",
    month: "short",
  });
}
function daysBetween(a: Date, b: Date) {
  return Math.max(1, Math.ceil((b.getTime() - a.getTime()) / 86400000));
}

export default function CarDetail() {
  const styles = useThemedStyles(makeStyles);
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const [car, setCar] = useState<Car | null>(null);
  const [err, setErr] = useState<string | null>(null);
  const [imgIndex, setImgIndex] = useState(0);
  const [calendarOpen, setCalendarOpen] = useState(false);
  const [dateStart, setDateStart] = useState<Date>(defaultStart);
  const [dateEnd, setDateEnd] = useState<Date>(defaultEnd);
  const [datesSet, setDatesSet] = useState(false);
  const galleryRef = useRef<FlatList>(null);

  const locale = i18n.locale === "en" ? "en" : "es";

  useEffect(() => {
    (async () => {
      try {
        const r = await api.get<{ car: Car }>(`/cars/${id}`);
        setCar(r.car);
        const extra = r.car.images?.slice(1) ?? [];
        if (extra.length) Image.prefetch(extra).catch(() => {});
      } catch (e) {
        setErr(e instanceof ApiError ? e.message : t("common.error"));
      }
    })();
  }, [id]);

  if (err) {
    return (
      <View style={styles.errContainer}>
        <Stack.Screen options={{ headerShown: true, title: "Error" }} />
        <Ionicons name="warning-outline" size={40} color={colors.danger} />
        <Text style={styles.errText}>{err}</Text>
        <Button
          title={t("common.retry")}
          onPress={() => router.back()}
          variant="ghost"
          style={{ marginTop: 12 }}
        />
      </View>
    );
  }
  if (!car) return <Loading />;

  const status = carStatus[Number(car.status ?? 0)];
  const gallery =
    car.images?.length ? car.images : car.image ? [car.image] : [];
  const isAvailable = Number(car.status ?? 0) === 0;

  const keySpecs = buildKeySpecs(car, locale);
  const fullSpecs = buildFullSpecs(car, locale);

  const days = daysBetween(dateStart, dateEnd);
  const perDay = Number(car.price_day ?? car.price ?? 0);
  const totalForDates = perDay * days;

  const onScroll = (e: NativeSyntheticEvent<NativeScrollEvent>) => {
    const idx = Math.round(e.nativeEvent.contentOffset.x / SW);
    setImgIndex(idx);
  };

  const handleBookPress = () => {
    const params: Record<string, string> = { carId: String(car.id) };
    if (datesSet) {
      params.start = toDbDateTime(dateStart);
      params.end = toDbDateTime(dateEnd);
    }
    router.push({ pathname: "/(client)/book/[carId]", params });
  };

  return (
    <View style={styles.screen}>
      <Stack.Screen options={{ headerShown: false }} />
      <StatusBar style="light" />

      <ScrollView
        contentContainerStyle={{ paddingBottom: 140 }}
        showsVerticalScrollIndicator={false}
      >
        {/* ── Carousel ──────────────────────────────────────────────── */}
        <View style={styles.gallery}>
          {gallery.length > 0 ? (
            <FlatList
              ref={galleryRef}
              data={gallery}
              horizontal
              pagingEnabled
              showsHorizontalScrollIndicator={false}
              keyExtractor={(_, i) => String(i)}
              onMomentumScrollEnd={onScroll}
              renderItem={({ item }) => (
                <Image
                  source={{ uri: item }}
                  style={{ width: SW, height: GALLERY_H }}
                  contentFit="cover"
                  transition={200}
                  cachePolicy="memory-disk"
                />
              )}
            />
          ) : (
            <View style={styles.galleryPlaceholder}>
              <Ionicons name="car-sport" size={72} color={colors.textFaint} />
            </View>
          )}

          <LinearGradient
            colors={gradients.imageScrim}
            style={StyleSheet.absoluteFill}
          />

          {/* Back button */}
          <View style={[styles.topBar, { paddingTop: insets.top + 8 }]}>
            <TouchableOpacity
              onPress={() => router.back()}
              style={styles.backBtn}
              activeOpacity={0.8}
            >
              <Ionicons name="arrow-back" size={22} color="#fff" />
            </TouchableOpacity>
            {status ? (
              <View
                style={[
                  styles.statusBadge,
                  { backgroundColor: status.bg },
                ]}
              >
                <View
                  style={[
                    styles.statusDot,
                    { backgroundColor: status.color },
                  ]}
                />
                <Text
                  style={[styles.statusText, { color: status.color }]}
                >
                  {status[locale]}
                </Text>
              </View>
            ) : null}
          </View>

          {/* Photo counter "1 / N" */}
          {gallery.length > 1 ? (
            <View style={styles.counter}>
              <Text style={styles.counterText}>
                {imgIndex + 1} / {gallery.length}
              </Text>
            </View>
          ) : null}
        </View>

        {/* ── Header ────────────────────────────────────────────────── */}
        <View style={styles.infoHeader}>
          {car.brand ? (
            <Text style={styles.brand}>{car.brand}</Text>
          ) : null}
          <Text style={styles.model}>{car.name ?? car.model ?? ""}</Text>
          <View style={styles.priceRow}>
            <Text style={styles.price}>
              {money(car.price_day ?? car.price)}
            </Text>
            <Text style={styles.priceLabel}>{t("cars.perDay")}</Text>
          </View>
        </View>

        {/* ── Horizontal spec strip ─────────────────────────────────── */}
        {keySpecs.length > 0 && (
          <View style={styles.specStrip}>
            {keySpecs.map((sp, i) => (
              <View
                key={sp.label}
                style={[
                  styles.specPill,
                  i < keySpecs.length - 1 && styles.specPillBorder,
                ]}
              >
                <View style={styles.specPillIcon}>
                  <Ionicons name={sp.icon} size={16} color={colors.cta} />
                </View>
                <Text style={styles.specPillValue}>{sp.value}</Text>
                <Text style={styles.specPillLabel}>{sp.label}</Text>
              </View>
            ))}
          </View>
        )}

        {/* ── Date range selector ───────────────────────────────────── */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>
            {locale === "en" ? "When do you need it?" : "¿Cuándo lo necesitas?"}
          </Text>
          <Pressable
            style={styles.datePicker}
            onPress={() => setCalendarOpen(true)}
          >
            {datesSet ? (
              <View style={styles.datePickerFilled}>
                <View style={styles.datePickerChip}>
                  <Ionicons
                    name="calendar-outline"
                    size={14}
                    color={colors.cta}
                  />
                  <Text style={styles.datePickerChipLabel}>
                    {locale === "en" ? "From" : "Desde"}
                  </Text>
                  <Text style={styles.datePickerChipDate}>
                    {fmtShort(dateStart, locale)}
                  </Text>
                </View>
                <Ionicons
                  name="arrow-forward"
                  size={14}
                  color={colors.textMuted}
                />
                <View style={styles.datePickerChip}>
                  <Ionicons
                    name="flag-outline"
                    size={14}
                    color={colors.primary}
                  />
                  <Text style={styles.datePickerChipLabel}>
                    {locale === "en" ? "Until" : "Hasta"}
                  </Text>
                  <Text style={styles.datePickerChipDate}>
                    {fmtShort(dateEnd, locale)}
                  </Text>
                </View>
                <View style={styles.daysPill}>
                  <Text style={styles.daysPillText}>
                    {days} {locale === "en" ? "days" : "días"}
                  </Text>
                </View>
              </View>
            ) : (
              <View style={styles.datePickerHint}>
                <Ionicons
                  name="calendar-outline"
                  size={18}
                  color={colors.primary}
                />
                <Text style={styles.datePickerHintText}>
                  {locale === "en"
                    ? "Pick dates to see total price"
                    : "Elige fechas para ver el precio total"}
                </Text>
                <Ionicons
                  name="chevron-forward"
                  size={16}
                  color={colors.textMuted}
                />
              </View>
            )}
          </Pressable>
        </View>

        {/* ── Full spec grid ────────────────────────────────────────── */}
        {fullSpecs.length > 0 && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>
              {locale === "en" ? "Specifications" : "Especificaciones"}
            </Text>
            <View style={styles.specsGrid}>
              {fullSpecs.map((sp) => (
                <View key={sp.label} style={styles.specCard}>
                  <View style={styles.specIconWrap}>
                    <Ionicons name={sp.icon} size={18} color={colors.info} />
                  </View>
                  <Text style={styles.specValue}>{sp.value}</Text>
                  <Text style={styles.specLabel}>{sp.label}</Text>
                </View>
              ))}
            </View>
          </View>
        )}

        {/* ── Description ───────────────────────────────────────────── */}
        {car.description ? (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>
              {locale === "en" ? "About this car" : "Acerca del auto"}
            </Text>
            <Text style={styles.description}>{car.description}</Text>
          </View>
        ) : null}
      </ScrollView>

      {/* ── Sticky CTA ────────────────────────────────────────────── */}
      <View style={[styles.cta, { paddingBottom: insets.bottom + 16 }]}>
        <View>
          <Text style={styles.ctaPer}>
            {datesSet
              ? `${days} ${locale === "en" ? "days" : "días"} · total`
              : locale === "en"
              ? "Total per day"
              : "Precio por día"}
          </Text>
          <Text style={styles.ctaPrice}>
            {datesSet
              ? money(totalForDates)
              : money(car.price_day ?? car.price)}
          </Text>
        </View>
        <Button
          title={
            isAvailable
              ? t("cars.book")
              : (status?.[locale] ?? t("common.empty"))
          }
          disabled={!isAvailable}
          icon={isAvailable ? "arrow-forward" : undefined}
          iconRight
          onPress={handleBookPress}
          style={{ flex: 1, marginLeft: 16 }}
          size="lg"
        />
      </View>

      {/* ── Date range calendar modal ─────────────────────────────── */}
      <RangeCalendar
        visible={calendarOpen}
        start={dateStart}
        end={dateEnd}
        onClose={() => setCalendarOpen(false)}
        onConfirm={(s, e) => {
          setDateStart(s);
          setDateEnd(e);
          setDatesSet(true);
          setCalendarOpen(false);
        }}
      />
    </View>
  );
}

// ─── Styles ────────────────────────────────────────────────────────────────────
const makeStyles = () =>
  StyleSheet.create({
    screen: { flex: 1, backgroundColor: colors.bg },
    errContainer: {
      flex: 1,
      padding: 24,
      justifyContent: "center",
      alignItems: "center",
      backgroundColor: colors.bg,
      gap: 12,
    },
    errText: { ...type.body, color: colors.danger, textAlign: "center" },

    // Gallery
    gallery: {
      height: GALLERY_H,
      position: "relative",
      backgroundColor: colors.dark,
    },
    galleryPlaceholder: {
      width: SW,
      height: GALLERY_H,
      alignItems: "center",
      justifyContent: "center",
    },
    topBar: {
      position: "absolute",
      top: 0,
      left: 0,
      right: 0,
      flexDirection: "row",
      justifyContent: "space-between",
      alignItems: "center",
      paddingHorizontal: spacing.lg,
      paddingBottom: 8,
    },
    backBtn: {
      width: 42,
      height: 42,
      borderRadius: 21,
      backgroundColor: "rgba(0,0,0,0.5)",
      alignItems: "center",
      justifyContent: "center",
    },
    statusBadge: {
      flexDirection: "row",
      alignItems: "center",
      gap: 5,
      paddingHorizontal: 10,
      paddingVertical: 6,
      borderRadius: radius.full,
    },
    statusDot: { width: 6, height: 6, borderRadius: 3 },
    statusText: { ...type.small, fontFamily: font.semibold },
    // Counter "1 / 5"
    counter: {
      position: "absolute",
      bottom: 14,
      right: 14,
      backgroundColor: "rgba(0,0,0,0.55)",
      borderRadius: radius.full,
      paddingHorizontal: 12,
      paddingVertical: 5,
    },
    counterText: {
      fontFamily: font.semibold,
      fontSize: 12,
      color: "#FFFFFF",
      letterSpacing: 0.4,
    },

    // Info header
    infoHeader: {
      backgroundColor: colors.card,
      padding: spacing.xl,
      marginTop: -24,
      borderTopLeftRadius: radius.xxl,
      borderTopRightRadius: radius.xxl,
    },
    brand: { ...type.label, color: colors.textMuted, marginBottom: 4 },
    model: { ...type.h1, color: colors.text },
    priceRow: {
      flexDirection: "row",
      alignItems: "baseline",
      marginTop: 10,
      gap: 6,
    },
    price: { ...type.h1, color: colors.info, fontFamily: font.extrabold },
    priceLabel: { ...type.callout, color: colors.textMuted },

    // Horizontal spec strip
    specStrip: {
      flexDirection: "row",
      backgroundColor: colors.card,
      borderTopWidth: 1,
      borderTopColor: colors.borderLight,
      borderBottomWidth: 1,
      borderBottomColor: colors.borderLight,
    },
    specPill: {
      flex: 1,
      alignItems: "center",
      paddingVertical: 14,
      paddingHorizontal: 4,
    },
    specPillBorder: {
      borderRightWidth: 1,
      borderRightColor: colors.borderLight,
    },
    specPillIcon: {
      width: 36,
      height: 36,
      borderRadius: radius.full,
      backgroundColor: colors.ctaXLight,
      alignItems: "center",
      justifyContent: "center",
      marginBottom: 6,
    },
    specPillValue: { ...type.title, color: colors.text, textAlign: "center", fontSize: 13 },
    specPillLabel: { ...type.small, color: colors.textMuted, marginTop: 2 },

    // Date picker row
    section: {
      paddingHorizontal: spacing.xl,
      paddingVertical: spacing.xl,
      borderTopWidth: 1,
      borderTopColor: colors.borderLight,
    },
    sectionTitle: { ...type.label, color: colors.textMuted, marginBottom: 14 },

    datePicker: {
      backgroundColor: colors.card,
      borderRadius: radius.lg,
      borderWidth: 1.5,
      borderColor: colors.border,
      overflow: "hidden",
    },
    datePickerHint: {
      flexDirection: "row",
      alignItems: "center",
      gap: 12,
      padding: 16,
    },
    datePickerHintText: {
      ...type.bodyMed,
      color: colors.textSecondary,
      flex: 1,
    },
    datePickerFilled: {
      flexDirection: "row",
      alignItems: "center",
      gap: 10,
      padding: 14,
      flexWrap: "wrap",
    },
    datePickerChip: {
      flex: 1,
      flexDirection: "row",
      alignItems: "center",
      gap: 6,
    },
    datePickerChipLabel: { ...type.small, color: colors.textMuted },
    datePickerChipDate: { ...type.title, color: colors.text },
    daysPill: {
      backgroundColor: colors.infoBg,
      borderRadius: radius.full,
      paddingHorizontal: 10,
      paddingVertical: 4,
    },
    daysPillText: { ...type.small, color: colors.info },

    // Full spec grid
    specsGrid: { flexDirection: "row", flexWrap: "wrap", gap: 10 },
    specCard: {
      flexGrow: 1,
      flexBasis: "28%",
      backgroundColor: colors.bg,
      borderRadius: radius.md,
      padding: 14,
      alignItems: "center",
      borderWidth: 1,
      borderColor: colors.borderLight,
    },
    specIconWrap: {
      width: 38,
      height: 38,
      borderRadius: radius.full,
      backgroundColor: colors.primaryXLight,
      alignItems: "center",
      justifyContent: "center",
      marginBottom: 8,
    },
    specValue: { ...type.title, color: colors.text, textAlign: "center" },
    specLabel: {
      ...type.caption,
      color: colors.textMuted,
      marginTop: 2,
      textAlign: "center",
    },

    description: { ...type.body, color: colors.textSecondary, lineHeight: 24 },

    // Sticky CTA
    cta: {
      position: "absolute",
      bottom: 0,
      left: 0,
      right: 0,
      backgroundColor: colors.card,
      flexDirection: "row",
      alignItems: "center",
      paddingTop: 16,
      paddingHorizontal: spacing.xl,
      borderTopWidth: 1,
      borderTopColor: colors.border,
      ...shadow.lg,
    },
    ctaPer: { ...type.caption, color: colors.textMuted },
    ctaPrice: { ...type.h2, color: colors.text, fontFamily: font.extrabold },
  });
