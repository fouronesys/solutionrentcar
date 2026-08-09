/**
 * Ubicaciones — pantalla de sucursales con mapa real.
 *
 * Estrategia cross-platform:
 *   • Native (iOS/Android): MapView de react-native-maps
 *   • Web (Replit preview): iframe de OpenStreetMap/Leaflet (sin API key)
 */
import React, { useEffect, useRef, useState } from "react";
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
import * as Location from "expo-location";
import { ScreenHeader } from "@/components/ScreenHeader";
import { colors, font, radius, shadow, spacing, type } from "@/theme/colors";
import { i18n } from "@/i18n";

// ─── Branch data ────────────────────────────────────────────────────────────

interface Branch {
  id: string;
  city: string;
  name: string;
  address: string;
  hours: string;
  phone?: string;
  lat: number;
  lng: number;
  distanceKm?: number;
}

const BRANCHES: Branch[] = [
  {
    id: "sd-piantini",
    city: "Santo Domingo",
    name: "Santo Domingo",
    address: "Av. Abraham Lincoln 1003",
    hours: "08:00 – 20:00",
    phone: "+1 849-564-4488",
    lat: 18.4764,
    lng: -69.9312,
  },
  {
    id: "sd-gazcue",
    city: "Santo Domingo",
    name: "Santo Domingo – Gazcue",
    address: "Av. Independencia 456",
    hours: "08:00 – 20:00",
    phone: "+1 849-564-4488",
    lat: 18.4721,
    lng: -69.9019,
  },
  {
    id: "punta-cana",
    city: "Punta Cana",
    name: "Punta Cana – Aeropuerto",
    address: "Terminal Internacional AILA",
    hours: "06:00 – 22:00",
    phone: "+1 809-000-0002",
    lat: 18.5674,
    lng: -68.3597,
  },
  {
    id: "santiago",
    city: "Santiago",
    name: "Santiago – Centro",
    address: "Calle del Sol 78",
    hours: "08:00 – 20:00",
    phone: "+1 809-000-0003",
    lat: 19.4517,
    lng: -70.6970,
  },
];

const CITIES = ["Santo Domingo", "Punta Cana", "Santiago"];

// ─── City center coordinates ─────────────────────────────────────────────────

const CITY_REGIONS: Record<string, { lat: number; lng: number; zoom: number }> = {
  "Santo Domingo": { lat: 18.4735, lng: -69.9312, zoom: 13 },
  "Punta Cana":    { lat: 18.5674, lng: -68.3597, zoom: 13 },
  "Santiago":      { lat: 19.4517, lng: -70.6970, zoom: 13 },
};

// ─── Native map (react-native-maps) — lazy import so web doesn't crash ──────

let MapView: any = null;
let Marker: any = null;
let PROVIDER_DEFAULT: any = null;

if (Platform.OS !== "web") {
  try {
    const maps = require("react-native-maps");
    MapView = maps.default;
    Marker = maps.Marker;
    PROVIDER_DEFAULT = maps.PROVIDER_DEFAULT;
  } catch (_) {
    // react-native-maps not available in this environment
  }
}

// ─── Web map: Leaflet (canvas, no WebGL) via srcDoc ──────────────────────────

function buildLeafletHTML(branches: Branch[], selected: Branch): string {
  const markers = branches
    .map(
      (b) =>
        `L.marker([${b.lat}, ${b.lng}], {
          icon: L.divIcon({
            className: '',
            html: '<div style="width:28px;height:28px;background:${b.id === selected.id ? "#E8002D" : "#1828E8"};border-radius:50% 50% 50% 0;border:3px solid #fff;transform:rotate(-45deg);box-shadow:0 2px 6px rgba(0,0,0,.35)"></div>',
            iconSize: [28,28], iconAnchor: [14,28], popupAnchor: [0,-30]
          })
        }).addTo(map).bindPopup('<b>${b.name}</b><br/>${b.address}');`
    )
    .join("\n");

  return `<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"><\/script>
<style>
  html,body,#map{margin:0;padding:0;width:100%;height:100%;background:#E8EFF5}
  .leaflet-control-zoom{display:none}
</style>
</head>
<body>
<div id="map"></div>
<script>
  var map = L.map('map',{zoomControl:false,attributionControl:false}).setView([${selected.lat},${selected.lng}],13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);
  ${markers}
<\/script>
</body>
</html>`;
}

function WebMapView({ branches, selectedId }: { branches: Branch[]; selectedId: string | null }) {
  const selected = branches.find((b) => b.id === selectedId) ?? branches[0];
  if (!selected) return null;

  const html = buildLeafletHTML(branches, selected);

  return (
    <View style={styles.webMapWrap}>
      {/* @ts-ignore — iframe + srcDoc only available on web */}
      <iframe
        srcDoc={html}
        style={{ width: "100%", height: "100%", border: "none", borderRadius: 16 }}
        title="Mapa de sucursales"
        sandbox="allow-scripts"
      />
      <View style={styles.mapOverlay}>
        <View style={styles.mapBadge}>
          <Ionicons name="location" size={14} color={colors.cta} />
          <Text style={styles.mapBadgeText}>{selected.name}</Text>
        </View>
      </View>
    </View>
  );
}

// ─── Native MapView component ─────────────────────────────────────────────────

function NativeMapView({
  branches,
  selectedId,
  onMarkerPress,
}: {
  branches: Branch[];
  selectedId: string | null;
  onMarkerPress: (id: string) => void;
}) {
  const mapRef = useRef<any>(null);
  const selected = branches.find((b) => b.id === selectedId) ?? branches[0];
  const region = selected
    ? {
        latitude: selected.lat,
        longitude: selected.lng,
        latitudeDelta: 0.04,
        longitudeDelta: 0.04,
      }
    : undefined;

  useEffect(() => {
    if (!selected || !mapRef.current) return;
    mapRef.current.animateToRegion(
      {
        latitude: selected.lat,
        longitude: selected.lng,
        latitudeDelta: 0.02,
        longitudeDelta: 0.02,
      },
      600
    );
  }, [selected?.id]);

  if (!MapView) {
    return (
      <View style={[styles.mapBox, styles.mapFallback]}>
        <Ionicons name="map-outline" size={32} color={colors.textFaint} />
        <Text style={styles.mapFallbackText}>Mapa no disponible</Text>
      </View>
    );
  }

  return (
    <View style={styles.nativeMapWrap}>
      <MapView
        ref={mapRef}
        style={styles.nativeMap}
        initialRegion={region}
        showsUserLocation
        showsMyLocationButton={false}
        showsCompass={false}
        rotateEnabled={false}
      >
        {branches.map((b) => (
          <Marker
            key={b.id}
            coordinate={{ latitude: b.lat, longitude: b.lng }}
            title={b.name}
            description={b.address}
            pinColor={b.id === selectedId ? colors.cta : colors.primary}
            onPress={() => onMarkerPress(b.id)}
          />
        ))}
      </MapView>

      {/* My location button */}
      <Pressable style={styles.locationBtn} onPress={() => requestLocation(mapRef)}>
        <Ionicons name="locate" size={18} color={colors.text} />
      </Pressable>
    </View>
  );
}

async function requestLocation(mapRef: React.RefObject<any>) {
  try {
    const { status } = await Location.requestForegroundPermissionsAsync();
    if (status !== "granted") return;
    const loc = await Location.getCurrentPositionAsync({ accuracy: Location.Accuracy.Balanced });
    mapRef.current?.animateToRegion(
      {
        latitude: loc.coords.latitude,
        longitude: loc.coords.longitude,
        latitudeDelta: 0.02,
        longitudeDelta: 0.02,
      },
      800
    );
  } catch (_) {}
}

// ─── Main screen ─────────────────────────────────────────────────────────────

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
    const query = encodeURIComponent(`${b.lat},${b.lng}`);
    const label = encodeURIComponent(b.name);
    let url: string;
    if (Platform.OS === "ios") {
      url = `maps:?q=${label}&ll=${b.lat},${b.lng}`;
    } else if (Platform.OS === "android") {
      url = `geo:${b.lat},${b.lng}?q=${b.lat},${b.lng}(${label})`;
    } else {
      url = `https://www.openstreetmap.org/?mlat=${b.lat}&mlon=${b.lng}&zoom=16`;
    }
    Linking.openURL(url).catch(() => {
      Linking.openURL(`https://www.openstreetmap.org/?mlat=${b.lat}&mlon=${b.lng}&zoom=16`);
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

        {/* MAP — real implementation */}
        {Platform.OS === "web" ? (
          <WebMapView branches={filtered.length ? filtered : BRANCHES} selectedId={selected} />
        ) : (
          <NativeMapView
            branches={filtered.length ? filtered : BRANCHES}
            selectedId={selected}
            onMarkerPress={(id) => setSelected(id)}
          />
        )}

        {/* City pills */}
        <ScrollView
          horizontal
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.pillsRow}
        >
          {CITIES.map((c) => (
            <Pressable
              key={c}
              onPress={() => {
                setCity(c);
                const first = BRANCHES.find((b) => b.city === c);
                setSelected(first?.id ?? null);
              }}
              style={[styles.pill, c === city && styles.pillActive]}
            >
              <Text style={[styles.pillText, c === city && styles.pillTextActive]}>{c}</Text>
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
                    {b.distanceKm < 10 ? `${b.distanceKm} km` : `${Math.round(b.distanceKm)} km`}
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
              <View style={styles.branchMetaRow}>
                <Ionicons name="navigate-outline" size={14} color={colors.textMuted} />
                <Text style={styles.branchMetaText}>
                  {b.lat.toFixed(4)}, {b.lng.toFixed(4)}
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

// ─── Styles ───────────────────────────────────────────────────────────────────

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

  // Web map
  webMapWrap: {
    marginHorizontal: spacing.xl,
    height: 200,
    borderRadius: radius.xl,
    marginBottom: spacing.lg,
    overflow: "hidden",
    position: "relative",
    ...shadow.sm,
  },
  mapOverlay: {
    position: "absolute",
    bottom: 10,
    left: 10,
    right: 10,
    flexDirection: "row",
    alignItems: "center",
  },
  mapBadge: {
    flexDirection: "row",
    alignItems: "center",
    gap: 5,
    paddingHorizontal: 12,
    paddingVertical: 7,
    backgroundColor: "rgba(255,255,255,0.92)",
    borderRadius: radius.full,
    ...shadow.xs,
  },
  mapBadgeText: { ...type.captionMed, color: colors.text },

  // Native map
  nativeMapWrap: {
    marginHorizontal: spacing.xl,
    height: 220,
    borderRadius: radius.xl,
    marginBottom: spacing.lg,
    overflow: "hidden",
    ...shadow.sm,
    position: "relative",
  },
  nativeMap: { ...StyleSheet.absoluteFillObject },
  locationBtn: {
    position: "absolute",
    bottom: 12,
    right: 12,
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: colors.card,
    alignItems: "center",
    justifyContent: "center",
    ...shadow.sm,
  },

  // Fallback
  mapBox: {
    marginHorizontal: spacing.xl,
    height: 160,
    backgroundColor: "#D9E5E0",
    borderRadius: radius.xl,
    marginBottom: spacing.lg,
    overflow: "hidden",
    alignItems: "center",
    justifyContent: "center",
  },
  mapFallback: { backgroundColor: colors.borderLight, gap: 10 },
  mapFallbackText: { ...type.caption, color: colors.textMuted },

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
