import React, { useEffect, useState } from "react";
import {
  Image,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from "react-native";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { StatusBar } from "expo-status-bar";
import { Button } from "@/components/Button";
import { Loading } from "@/components/Loading";
import { api, ApiError } from "@/api/client";
import type { Car } from "@/api/types";
import { carStatus, colors, font, gradients, radius, shadow, spacing, type } from "@/theme/colors";
import { i18n, t } from "@/i18n";
import { money } from "@/utils/format";

type Spec = { icon: keyof typeof Ionicons.glyphMap; value: string; label: string };

export default function CarDetail() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const [car, setCar] = useState<Car | null>(null);
  const [err, setErr] = useState<string | null>(null);
  const [imgIndex, setImgIndex] = useState(0);

  useEffect(() => {
    (async () => {
      try {
        const r = await api.get<{ car: Car }>(`/cars/${id}`);
        setCar(r.car);
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
        <Button title={t("common.retry")} onPress={() => router.back()} variant="ghost" style={{ marginTop: 12 }} />
      </View>
    );
  }
  if (!car) return <Loading />;

  const locale = i18n.locale === "en" ? "en" : "es";
  const status = carStatus[Number(car.status ?? 0)];
  const gallery = car.images?.length ? car.images : car.image ? [car.image] : [];
  const isAvailable = Number(car.status ?? 0) === 0;

  const specs: Spec[] = [
    car.year ? { icon: "calendar-outline", value: String(car.year), label: locale === "en" ? "Year" : "Año" } : null,
    car.transmission ? { icon: "cog-outline", value: car.transmission, label: locale === "en" ? "Transmission" : "Transmisión" } : null,
    car.fuel ? { icon: "water-outline", value: car.fuel, label: locale === "en" ? "Fuel" : "Combustible" } : null,
    car.seat ? { icon: "people-outline", value: String(car.seat), label: locale === "en" ? "Seats" : "Asientos" } : null,
    car.color ? { icon: "color-palette-outline", value: car.color, label: locale === "en" ? "Color" : "Color" } : null,
    car.plate ? { icon: "card-outline", value: car.plate, label: locale === "en" ? "Plate" : "Placa" } : null,
  ].filter(Boolean) as Spec[];

  return (
    <View style={styles.screen}>
      <Stack.Screen options={{ headerShown: false }} />
      <StatusBar style="light" />

      <ScrollView contentContainerStyle={{ paddingBottom: 140 }} showsVerticalScrollIndicator={false}>
        {/* Image gallery */}
        <View style={styles.gallery}>
          {gallery.length > 0 ? (
            <Image source={{ uri: gallery[imgIndex] }} style={styles.galleryImg} resizeMode="cover" />
          ) : (
            <View style={[styles.galleryImg, styles.galleryPlaceholder]}>
              <Ionicons name="car-sport" size={72} color={colors.textFaint} />
            </View>
          )}
          <LinearGradient colors={gradients.imageScrim} style={styles.galleryScrim} />
          <View style={[styles.topBar, { paddingTop: insets.top + 8 }]}>
            <TouchableOpacity onPress={() => router.back()} style={styles.backBtn} activeOpacity={0.8}>
              <Ionicons name="arrow-back" size={22} color="#fff" />
            </TouchableOpacity>
            {status ? (
              <View style={[styles.statusBadge, { backgroundColor: status.bg }]}>
                <View style={[styles.statusDot, { backgroundColor: status.color }]} />
                <Text style={[styles.statusText, { color: status.color }]}>{status[locale]}</Text>
              </View>
            ) : null}
          </View>
          {gallery.length > 1 && (
            <View style={styles.dots}>
              {gallery.map((_, i) => (
                <TouchableOpacity key={i} onPress={() => setImgIndex(i)} hitSlop={8}>
                  <View style={[styles.dot, i === imgIndex && styles.dotActive]} />
                </TouchableOpacity>
              ))}
            </View>
          )}
        </View>

        {/* Header */}
        <View style={styles.infoHeader}>
          {car.brand ? <Text style={styles.brand}>{car.brand}</Text> : null}
          <Text style={styles.model}>{car.name ?? car.model ?? ""}</Text>
          <View style={styles.priceRow}>
            <Text style={styles.price}>{money(car.price_day ?? car.price)}</Text>
            <Text style={styles.priceLabel}>/ {t("cars.perDay")}</Text>
          </View>
        </View>

        {/* Specs grid */}
        {specs.length > 0 && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>{locale === "en" ? "Specifications" : "Especificaciones"}</Text>
            <View style={styles.specsGrid}>
              {specs.map((sp) => (
                <View key={sp.label} style={styles.specCard}>
                  <View style={styles.specIconWrap}>
                    <Ionicons name={sp.icon} size={18} color={colors.primaryDark} />
                  </View>
                  <Text style={styles.specValue}>{sp.value}</Text>
                  <Text style={styles.specLabel}>{sp.label}</Text>
                </View>
              ))}
            </View>
          </View>
        )}

        {/* Description */}
        {car.description ? (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>{locale === "en" ? "About this car" : "Acerca del auto"}</Text>
            <Text style={styles.description}>{car.description}</Text>
          </View>
        ) : null}
      </ScrollView>

      {/* CTA */}
      <View style={[styles.cta, { paddingBottom: insets.bottom + 16 }]}>
        <View>
          <Text style={styles.ctaPer}>{locale === "en" ? "Total per day" : "Precio por día"}</Text>
          <Text style={styles.ctaPrice}>{money(car.price_day ?? car.price)}</Text>
        </View>
        <Button
          title={isAvailable ? t("cars.book") : (status?.[locale] ?? t("common.empty"))}
          disabled={!isAvailable}
          icon={isAvailable ? "arrow-forward" : undefined}
          iconRight
          onPress={() => router.push({ pathname: "/(client)/book/[carId]", params: { carId: String(car.id) } })}
          style={{ flex: 1, marginLeft: 16 }}
          size="lg"
        />
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  errContainer: { flex: 1, padding: 24, justifyContent: "center", alignItems: "center", backgroundColor: colors.bg, gap: 12 },
  errText: { ...type.body, color: colors.danger, textAlign: "center" },

  gallery: { height: 320, position: "relative", backgroundColor: colors.dark },
  galleryImg: { width: "100%", height: 320 },
  galleryPlaceholder: { alignItems: "center", justifyContent: "center" },
  galleryScrim: { ...StyleSheet.absoluteFillObject },
  topBar: {
    position: "absolute",
    top: 0, left: 0, right: 0,
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: spacing.lg,
    paddingBottom: 8,
  },
  backBtn: {
    width: 42, height: 42,
    borderRadius: 21,
    backgroundColor: "rgba(11,18,32,0.45)",
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
  dots: { position: "absolute", bottom: 16, alignSelf: "center", flexDirection: "row", gap: 6 },
  dot: { width: 6, height: 6, borderRadius: 3, backgroundColor: "rgba(255,255,255,0.5)" },
  dotActive: { backgroundColor: "#fff", width: 20 },

  infoHeader: {
    backgroundColor: colors.card,
    padding: spacing.xl,
    marginTop: -24,
    borderTopLeftRadius: radius.xxl,
    borderTopRightRadius: radius.xxl,
  },
  brand: { ...type.label, color: colors.textMuted, marginBottom: 4 },
  model: { ...type.h1, color: colors.text },
  priceRow: { flexDirection: "row", alignItems: "baseline", marginTop: 10, gap: 6 },
  price: { ...type.h1, color: colors.primaryDark, fontFamily: font.extrabold },
  priceLabel: { ...type.callout, color: colors.textMuted },

  section: { paddingHorizontal: spacing.xl, paddingVertical: spacing.xl, borderTopWidth: 1, borderTopColor: colors.borderLight },
  sectionTitle: { ...type.label, color: colors.textMuted, marginBottom: 16 },

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
  specLabel: { ...type.caption, color: colors.textMuted, marginTop: 2, textAlign: "center" },

  description: { ...type.body, color: colors.textSecondary, lineHeight: 24 },

  cta: {
    position: "absolute",
    bottom: 0, left: 0, right: 0,
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
