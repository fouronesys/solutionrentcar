/**
 * Ubicaciones — pantalla de sucursales con mapa real.
 *
 * Estrategia cross-platform:
 *   • Native (iOS/Android): WebView con Leaflet + teselas de Mapbox
 *   • Web (Replit preview): iframe con el mismo HTML de Leaflet
 * Sin módulos nativos de mapas: evita crashes por falta de API keys nativas.
 */
import React, { useCallback, useEffect, useRef, useState } from "react";
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
import { useFocusEffect } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { TAB_BAR_HEIGHT, useTabBarScroll } from "@/components/TabBarScrollContext";
import { Ionicons } from "@expo/vector-icons";
import { StatusBar } from "expo-status-bar";
import * as Location from "expo-location";
import { ScreenHeader } from "@/components/ScreenHeader";
import { colors, font, radius, shadow, spacing, type } from "@/theme/colors";
import { useThemedStyles, useTheme } from "@/theme/ThemeContext";
import { i18n } from "@/i18n";
import { BRANCHES, CITIES, type Branch } from "@/config/locations";

// ─── City center coordinates ─────────────────────────────────────────────────

const CITY_REGIONS: Record<string, { lat: number; lng: number; zoom: number }> = {
  "San José de las Matas": { lat: 19.3610, lng: -71.0103, zoom: 13 },
};

// ─── Native map: WebView (lazy import so web doesn't crash) ─────────────────

let RNWebView: any = null;

if (Platform.OS !== "web") {
  try {
    RNWebView = require("react-native-webview").WebView;
  } catch (_) {
    // react-native-webview not available in this environment
  }
}

// ─── Leaflet map HTML (Mapbox tiles si hay token, OSM como fallback) ─────────

const MAPBOX_TOKEN = process.env.EXPO_PUBLIC_MAPBOX_TOKEN ?? "";

function tileLayerJS(): string {
  if (MAPBOX_TOKEN) {
    return `L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v12/tiles/512/{z}/{x}/{y}@2x?access_token=${MAPBOX_TOKEN}',{maxZoom:19,tileSize:512,zoomOffset:-1}).addTo(map);`;
  }
  return `L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);`;
}

function buildLeafletHTML(branches: Branch[], selected: Branch): string {
  const markers = branches
    .map(
      (b) =>
        `L.marker([${b.lat}, ${b.lng}], {
          icon: L.divIcon({
            className: '',
            html: '<div style="width:28px;height:28px;background:${b.id === selected.id ? "#C79323" : "#8A5F12"};border-radius:50% 50% 50% 0;border:3px solid #fff;transform:rotate(-45deg);box-shadow:0 2px 6px rgba(0,0,0,.35)"></div>',
            iconSize: [28,28], iconAnchor: [14,28], popupAnchor: [0,-30]
          })
        }).addTo(map).bindPopup('<b>${b.name}</b><br/>${b.address}').on('click', function(){ if(window.ReactNativeWebView){ window.ReactNativeWebView.postMessage('${b.id}'); } });`
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
  ${tileLayerJS()}
  ${markers}
<\/script>
</body>
</html>`;
}

function WebMapView({ branches, selectedId }: { branches: Branch[]; selectedId: string | null }) {
  const styles = useThemedStyles(makeStyles);
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
  const styles = useThemedStyles(makeStyles);
  const webRef = useRef<any>(null);
  const selected = branches.find((b) => b.id === selectedId) ?? branches[0];

  useEffect(() => {
    if (!selected || !webRef.current) return;
    webRef.current.injectJavaScript(
      `map.setView([${selected.lat},${selected.lng}],14,{animate:true}); true;`
    );
  }, [selected?.id]);

  if (!RNWebView || !selected) {
    return (
      <View style={[styles.mapBox, styles.mapFallback]}>
        <Ionicons name="map-outline" size={32} color={colors.textFaint} />
        <Text style={styles.mapFallbackText}>Mapa no disponible</Text>
      </View>
    );
  }

  return (
    <View style={styles.nativeMapWrap}>
      <RNWebView
        ref={webRef}
        originWhitelist={["*"]}
        source={{ html: buildLeafletHTML(branches, selected) }}
        style={styles.nativeMap}
        javaScriptEnabled
        domStorageEnabled
        onMessage={(e: any) => {
          const id = e?.nativeEvent?.data;
          if (id) onMarkerPress(String(id));
        }}
      />

      {/* My location button */}
      <Pressable style={styles.locationBtn} onPress={() => requestLocation(webRef)}>
        <Ionicons name="locate" size={18} color={colors.text} />
      </Pressable>
    </View>
  );
}

async function requestLocation(webRef: React.RefObject<any>) {
  try {
    const { status } = await Location.requestForegroundPermissionsAsync();
    if (status !== "granted") return;
    const loc = await Location.getCurrentPositionAsync({ accuracy: Location.Accuracy.Balanced });
    webRef.current?.injectJavaScript(
      `map.setView([${loc.coords.latitude},${loc.coords.longitude}],15,{animate:true}); true;`
    );
  } catch (_) {}
}

// ─── Main screen ─────────────────────────────────────────────────────────────

export default function LocationsScreen() {
  const styles = useThemedStyles(makeStyles);
  const { isDark } = useTheme();
  const locale = i18n.locale === "en" ? "en" : "es";
  const { onScroll, showTabBar } = useTabBarScroll();
  useFocusEffect(useCallback(() => { showTabBar(); }, [showTabBar]));
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

  // Waze: deep link on native, universal link everywhere as fallback.
  const openWaze = (b: Branch) => {
    const webUrl = `https://waze.com/ul?ll=${b.lat},${b.lng}&navigate=yes`;
    if (Platform.OS === "web") {
      Linking.openURL(webUrl);
      return;
    }
    Linking.openURL(`waze://?ll=${b.lat},${b.lng}&navigate=yes`).catch(() =>
      Linking.openURL(webUrl),
    );
  };

  // Google Maps: turn-by-turn directions to the branch.
  const openGoogleMaps = (b: Branch) => {
    const webUrl =
      `https://www.google.com/maps/dir/?api=1` +
      `&destination=${b.lat},${b.lng}&travelmode=driving`;
    if (Platform.OS === "web") {
      Linking.openURL(webUrl);
      return;
    }
    const appUrl =
      Platform.OS === "ios"
        ? `comgooglemaps://?daddr=${b.lat},${b.lng}&directionsmode=driving`
        : `google.navigation:q=${b.lat},${b.lng}`;
    Linking.openURL(appUrl).catch(() => Linking.openURL(webUrl));
  };

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <StatusBar style={isDark ? "light" : "dark"} />
      <ScreenHeader
        title={locale === "en" ? "Where we are" : "Dónde estamos"}
        subtitle={locale === "en" ? "Branches across the island" : "Bases locales en toda la isla"}
      />

      <ScrollView contentContainerStyle={[styles.body, { paddingBottom: TAB_BAR_HEIGHT + 16 }]} showsVerticalScrollIndicator={false} onScroll={onScroll} scrollEventThrottle={16}>
        {/* Eyebrow + heading */}
        <View style={styles.headingBlock}>
          <Text style={styles.eyebrow}>
            {locale === "en" ? "CASA RIVAS POINTS" : "PUNTOS CASA RIVAS"}
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
              <Pressable style={styles.dirBtn} onPress={() => openWaze(b)}>
                <Ionicons name="navigate" size={16} color="#FFFFFF" />
                <Text style={styles.dirBtnText}>Waze</Text>
              </Pressable>
              <Pressable
                style={[styles.dirBtn, styles.dirBtnAlt]}
                onPress={() => openGoogleMaps(b)}
              >
                <Ionicons name="map-outline" size={16} color={colors.cta} />
                <Text style={[styles.dirBtnText, styles.dirBtnAltText]}>
                  Maps
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

const makeStyles = () => StyleSheet.create({
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
  dirBtnAlt: {
    backgroundColor: "transparent",
    borderWidth: 1,
    borderColor: colors.cta,
  },
  dirBtnAltText: { color: colors.cta },
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
