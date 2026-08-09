/**
 * Ubicaciones — locations / branches screen.
 * Mirrors the design from the Yowell reference screenshots:
 *   - White header with logo
 *   - "PUNTOS YOWELL" eyebrow + bold heading
 *   - Search bar
 *   - Placeholder map area
 *   - City filter pills (dark active)
 *   - Location cards with red icon, address, hours, directions CTA
 */
import React, { useState } from "react";
import {
  Linking,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  View,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons";
import { StatusBar } from "expo-status-bar";
import { ScreenHeader } from "@/components/ScreenHeader";
import { colors, font, radius, shadow, spacing, type } from "@/theme/colors";
import { i18n } from "@/i18n";

interface Branch {
  id: string;
  city: string;
  name: string;
  address: string;
  hours: string;
  phone?: string;
  lat?: number;
  lng?: number;
  distanceKm?: number;
}

const BRANCHES: Branch[] = [
  {
    id: "sd-piantini",
    city: "Santo Domingo",
    name: "Santo Domingo",
    address: "Av. Abraham Lincoln 1003",
    hours: "08:00 – 20:00",
    phone: "+1 809-000-0000",
    distanceKm: 1.8,
  },
  {
    id: "sd-gazcue",
    city: "Santo Domingo",
    name: "Santo Domingo – Gazcue",
    address: "Av. Independencia 456",
    hours: "08:00 – 20:00",
    phone: "+1 809-000-0001",
    distanceKm: 3.2,
  },
  {
    id: "punta-cana",
    city: "Punta Cana",
    name: "Punta Cana – Aeropuerto",
    address: "Terminal Internacional AILA",
    hours: "06:00 – 22:00",
    phone: "+1 809-000-0002",
    distanceKm: 185,
  },
  {
    id: "santiago",
    city: "Santiago",
    name: "Santiago – Centro",
    address: "Calle del Sol 78",
    hours: "08:00 – 20:00",
    phone: "+1 809-000-0003",
    distanceKm: 155,
  },
];

const CITIES = ["Santo Domingo", "Punta Cana", "Santiago"];

export default function LocationsScreen() {
  const locale = i18n.locale === "en" ? "en" : "es";
  const [q, setQ] = useState("");
  const [city, setCity] = useState(CITIES[0]);
  const [selected, setSelected] = useState<string | null>(BRANCHES[0].id);

  const filtered = BRANCHES.filter(
    (b) =>
      b.city === city &&
      (q === "" ||
        b.name.toLowerCase().includes(q.toLowerCase()) ||
        b.address.toLowerCase().includes(q.toLowerCase()))
  );

  const openMaps = (b: Branch) => {
    const query = encodeURIComponent(b.address);
    const url =
      Platform.OS === "ios"
        ? `maps:?q=${query}`
        : `geo:0,0?q=${query}`;
    Linking.openURL(url).catch(() => {
      Linking.openURL(`https://maps.google.com/maps?q=${query}`);
    });
  };

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <StatusBar style="dark" />
      <ScreenHeader
        title={locale === "en" ? "Where we are" : "Dónde estamos"}
        subtitle={locale === "en" ? "Branches across the island" : "Bases locales en toda la isla"}
      />

      <ScrollView contentContainerStyle={styles.body} showsVerticalScrollIndicator={false}>
        {/* Eyebrow + heading */}
        <View style={styles.headingBlock}>
          <Text style={styles.eyebrow}>
            {locale === "en" ? "YOWELL POINTS" : "PUNTOS YOWELL"}
          </Text>
          <Text style={styles.heading}>
            {locale === "en" ? "Easy to reach." : "Llega fácil."}
          </Text>
        </View>

        {/* Search */}
        <View style={styles.searchBar}>
          <Ionicons name="search-outline" size={18} color={colors.textMuted} />
          <TextInput
            placeholder={
              locale === "en"
                ? "Search city, zone or branch"
                : "Busca ciudad, zona o punto"
            }
            placeholderTextColor={colors.textMuted}
            value={q}
            onChangeText={setQ}
            style={styles.searchInput}
            returnKeyType="search"
          />
          {q ? (
            <Pressable onPress={() => setQ("")} hitSlop={8}>
              <Ionicons name="close-circle" size={18} color={colors.textMuted} />
            </Pressable>
          ) : null}
        </View>

        {/* Map placeholder */}
        <View style={styles.mapBox}>
          <View style={styles.mapPin1}>
            <Ionicons name="location" size={24} color={colors.cta} />
          </View>
          <View style={styles.mapPin2}>
            <Ionicons name="location" size={20} color={colors.primary} />
          </View>
          <View style={styles.mapPin3}>
            <Ionicons name="location" size={20} color={colors.primary} />
          </View>
          <Pressable style={styles.mapCovBtn}>
            <Ionicons name="navigate-outline" size={14} color={colors.text} />
            <Text style={styles.mapCovText}>
              {locale === "en" ? "Coverage map" : "Mapa de cobertura"}
            </Text>
          </Pressable>
        </View>

        {/* City pills */}
        <ScrollView
          horizontal
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.pillsRow}
        >
          {CITIES.map((c) => (
            <Pressable
              key={c}
              onPress={() => { setCity(c); setSelected(null); }}
              style={[styles.pill, c === city && styles.pillActive]}
            >
              <Text style={[styles.pillText, c === city && styles.pillTextActive]}>
                {c}
              </Text>
            </Pressable>
          ))}
        </ScrollView>

        {/* Branch cards */}
        {filtered.map((b) => (
          <View
            key={b.id}
            style={[styles.branchCard, selected === b.id && styles.branchCardSelected]}
          >
            <Pressable
              style={styles.branchTop}
              onPress={() => setSelected(b.id === selected ? null : b.id)}
            >
              <View style={styles.branchIconWrap}>
                <Ionicons name="location" size={20} color="#FFFFFF" />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.branchName}>{b.name}</Text>
                {b.distanceKm !== undefined ? (
                  <Text style={styles.branchDist}>
                    {b.distanceKm < 10
                      ? `${b.distanceKm} km`
                      : `${Math.round(b.distanceKm)} km`}
                  </Text>
                ) : null}
              </View>
              {selected === b.id ? (
                <Ionicons name="checkmark-circle" size={22} color={colors.success} />
              ) : null}
            </Pressable>

            <View style={styles.branchMeta}>
              <View style={styles.branchMetaRow}>
                <Ionicons name="location-outline" size={14} color={colors.textMuted} />
                <Text style={styles.branchMetaText}>{b.address}</Text>
              </View>
              <View style={styles.branchMetaRow}>
                <Ionicons name="time-outline" size={14} color={colors.textMuted} />
                <Text style={styles.branchMetaText}>
                  {locale === "en" ? "Open today" : "Abierto hoy"} · {b.hours}
                </Text>
              </View>
            </View>

            <View style={styles.branchActions}>
              <Pressable style={styles.dirBtn} onPress={() => openMaps(b)}>
                <Ionicons name="navigate-outline" size={16} color="#FFFFFF" />
                <Text style={styles.dirBtnText}>
                  {locale === "en" ? "Get directions" : "Cómo llegar"}
                </Text>
              </Pressable>
              {b.phone ? (
                <Pressable
                  style={styles.iconBtn}
                  onPress={() => Linking.openURL(`tel:${b.phone}`)}
                >
                  <Ionicons name="call-outline" size={18} color={colors.cta} />
                </Pressable>
              ) : null}
              <Pressable style={styles.iconBtn} onPress={() => openMaps(b)}>
                <Ionicons name="open-outline" size={18} color={colors.text} />
              </Pressable>
            </View>
          </View>
        ))}

        {filtered.length === 0 ? (
          <View style={styles.emptyBox}>
            <Ionicons name="location-outline" size={36} color={colors.textFaint} />
            <Text style={styles.emptyText}>
              {locale === "en" ? "No branches found" : "No hay sucursales"}
            </Text>
          </View>
        ) : null}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  body: { paddingBottom: 40 },

  headingBlock: { paddingHorizontal: spacing.xl, paddingTop: spacing.xl, paddingBottom: spacing.lg },
  eyebrow: { ...type.label, color: colors.cta, marginBottom: 8 },
  heading: { ...type.display, color: colors.text, fontSize: 28, letterSpacing: -0.6 },

  searchBar: {
    flexDirection: "row",
    alignItems: "center",
    gap: 10,
    backgroundColor: colors.card,
    borderRadius: radius.md,
    paddingHorizontal: 14,
    height: 52,
    marginHorizontal: spacing.xl,
    marginBottom: spacing.lg,
    borderWidth: 1,
    borderColor: colors.border,
    ...shadow.xs,
  },
  searchInput: { flex: 1, fontFamily: font.medium, fontSize: 15, color: colors.text },

  // Map placeholder
  mapBox: {
    marginHorizontal: spacing.xl,
    height: 160,
    backgroundColor: "#D9E5E0",
    borderRadius: radius.xl,
    marginBottom: spacing.lg,
    overflow: "hidden",
    position: "relative",
    alignItems: "center",
    justifyContent: "center",
  },
  mapPin1: { position: "absolute", top: 55, left: 75 },
  mapPin2: { position: "absolute", top: 35, right: 65 },
  mapPin3: { position: "absolute", bottom: 45, right: 110 },
  mapCovBtn: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    paddingHorizontal: 14,
    paddingVertical: 8,
    backgroundColor: "rgba(255,255,255,0.88)",
    borderRadius: radius.full,
    position: "absolute",
    bottom: 12,
    left: 12,
    ...shadow.xs,
  },
  mapCovText: { ...type.captionMed, color: colors.text },

  pillsRow: { paddingHorizontal: spacing.xl, gap: 8, marginBottom: spacing.lg },
  pill: {
    paddingHorizontal: 18,
    paddingVertical: 10,
    borderRadius: radius.full,
    backgroundColor: colors.card,
    borderWidth: 1.5,
    borderColor: colors.border,
  },
  pillActive: { backgroundColor: colors.dark, borderColor: colors.dark },
  pillText: { ...type.captionMed, color: colors.text },
  pillTextActive: { color: "#FFFFFF" },

  branchCard: {
    backgroundColor: colors.card,
    marginHorizontal: spacing.xl,
    marginBottom: spacing.md,
    borderRadius: radius.lg,
    ...shadow.sm,
    borderWidth: 1,
    borderColor: colors.borderLight,
  },
  branchCardSelected: { borderColor: colors.success, borderWidth: 1.5 },
  branchTop: {
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
    padding: spacing.lg,
    paddingBottom: spacing.md,
  },
  branchIconWrap: {
    width: 44,
    height: 44,
    borderRadius: radius.md,
    backgroundColor: colors.cta,
    alignItems: "center",
    justifyContent: "center",
  },
  branchName: { ...type.title, color: colors.text },
  branchDist: { ...type.caption, color: colors.textMuted, marginTop: 2 },
  branchMeta: { paddingHorizontal: spacing.lg, paddingBottom: spacing.md, gap: 6 },
  branchMetaRow: { flexDirection: "row", alignItems: "center", gap: 8 },
  branchMetaText: { ...type.caption, color: colors.textSecondary, flex: 1 },
  branchActions: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    padding: spacing.lg,
    paddingTop: spacing.sm,
    borderTopWidth: 1,
    borderTopColor: colors.borderLight,
  },
  dirBtn: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 6,
    backgroundColor: colors.dark,
    borderRadius: radius.md,
    height: 44,
  },
  dirBtnText: { ...type.captionMed, color: "#FFFFFF", fontFamily: font.bold },
  iconBtn: {
    width: 44,
    height: 44,
    borderRadius: radius.md,
    backgroundColor: colors.ctaXLight,
    alignItems: "center",
    justifyContent: "center",
    borderWidth: 1.5,
    borderColor: colors.ctaLight,
  },

  emptyBox: { alignItems: "center", paddingVertical: 40, gap: 10 },
  emptyText: { ...type.callout, color: colors.textMuted },
});
