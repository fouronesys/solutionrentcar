/**
 * PlaceAutocomplete — text input with place suggestions as you type.
 *
 * Suggestions come from the backend's `/places` endpoints, which proxy
 * OpenStreetMap's Nominatim geocoder server-side (identifying User-Agent,
 * caching, and shared rate limiting live there — clients never hit the
 * public geocoder directly). Results are biased to the Dominican Republic.
 *
 * Also offers a "use my current location" row (expo-location + reverse
 * geocoding) so the user can pick their exact position.
 */
import React, { useEffect, useRef, useState } from "react";
import {
  ActivityIndicator,
  Pressable,
  StyleSheet,
  Text,
  View,
  ViewStyle,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import * as Location from "expo-location";
import { colors, font, radius, shadow, type } from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";
import { Input } from "@/components/Input";
import { api } from "@/api/client";
import { i18n } from "@/i18n";

const DEBOUNCE_MS = 400;
const MIN_CHARS = 3;

export interface PlaceSuggestion {
  id: string;
  /** Short main label, e.g. "Aeropuerto Internacional de Punta Cana" */
  title: string;
  /** Secondary context, e.g. "Punta Cana, La Altagracia" */
  subtitle: string;
  /** Full label stored as the field value */
  label: string;
  lat: number;
  lng: number;
}

export function PlaceAutocomplete({
  label,
  value,
  onChange,
  icon = "navigate-outline",
  placeholder,
  containerStyle,
}: {
  label?: string;
  value: string;
  onChange: (text: string, place?: PlaceSuggestion) => void;
  icon?: keyof typeof Ionicons.glyphMap;
  placeholder?: string;
  containerStyle?: ViewStyle;
}) {
  const styles = useThemedStyles(makeStyles);
  const locale = i18n.locale === "en" ? "en" : "es";

  const [suggestions, setSuggestions] = useState<PlaceSuggestion[]>([]);
  const [open, setOpen] = useState(false);
  const [loading, setLoading] = useState(false);
  const [locating, setLocating] = useState(false);
  const [locError, setLocError] = useState<string | null>(null);

  const debounceTimer = useRef<ReturnType<typeof setTimeout> | null>(null);
  const blurTimer = useRef<ReturnType<typeof setTimeout> | null>(null);
  // Monotonic generations: async completions must match to apply state.
  // Search and location use separate counters so blurring the input (which
  // invalidates pending searches) cannot cancel an in-flight GPS lookup.
  const generation = useRef(0);
  const locGeneration = useRef(0);
  const mounted = useRef(true);

  useEffect(() => {
    mounted.current = true;
    return () => {
      mounted.current = false;
      generation.current++;
      locGeneration.current++;
      if (debounceTimer.current) clearTimeout(debounceTimer.current);
      if (blurTimer.current) clearTimeout(blurTimer.current);
    };
  }, []);

  const invalidate = () => {
    generation.current++;
    if (debounceTimer.current) clearTimeout(debounceTimer.current);
    setLoading(false);
  };

  const search = async (q: string) => {
    const gen = ++generation.current;
    setLoading(true);
    try {
      const data = await api.get<{ results: PlaceSuggestion[] }>(
        "/places/search",
        { q, lang: locale },
      );
      if (!mounted.current || gen !== generation.current) return;
      setSuggestions(Array.isArray(data.results) ? data.results : []);
      setOpen(true);
    } catch {
      if (!mounted.current || gen !== generation.current) return;
      setSuggestions([]);
    } finally {
      if (mounted.current && gen === generation.current) setLoading(false);
    }
  };

  const handleChange = (text: string) => {
    setLocError(null);
    onChange(text);
    invalidate();
    if (text.trim().length < MIN_CHARS) {
      setSuggestions([]);
      setOpen(false);
      return;
    }
    debounceTimer.current = setTimeout(() => search(text.trim()), DEBOUNCE_MS);
  };

  const pick = (s: PlaceSuggestion) => {
    invalidate();
    onChange(s.label, s);
    setSuggestions([]);
    setOpen(false);
  };

  const handleBlur = () => {
    if (blurTimer.current) clearTimeout(blurTimer.current);
    // Delay so a tap on a suggestion registers first
    blurTimer.current = setTimeout(() => {
      if (!mounted.current) return;
      invalidate();
      setOpen(false);
    }, 180);
  };

  const useMyLocation = async () => {
    const gen = ++locGeneration.current;
    // Tapping this row blurs the input; the pending blur close must not
    // interfere with the location flow.
    if (blurTimer.current) clearTimeout(blurTimer.current);
    invalidate(); // cancel any pending search
    setSuggestions([]);
    setOpen(false);
    setLocError(null);
    setLocating(true);
    try {
      const { status } = await Location.requestForegroundPermissionsAsync();
      if (!mounted.current || gen !== locGeneration.current) return;
      if (status !== "granted") {
        setLocError(
          locale === "en"
            ? "Location permission denied"
            : "Permiso de ubicación denegado",
        );
        return;
      }
      const pos = await Location.getCurrentPositionAsync({
        accuracy: Location.Accuracy.Balanced,
      });
      if (!mounted.current || gen !== locGeneration.current) return;
      const { latitude, longitude } = pos.coords;
      let picked: PlaceSuggestion = {
        id: `gps-${latitude},${longitude}`,
        title: locale === "en" ? "My current location" : "Mi ubicación actual",
        subtitle: "",
        label: `${latitude.toFixed(5)}, ${longitude.toFixed(5)}`,
        lat: latitude,
        lng: longitude,
      };
      try {
        const data = await api.get<{ result: PlaceSuggestion | null }>(
          "/places/reverse",
          { lat: latitude, lon: longitude, lang: locale },
        );
        if (!mounted.current || gen !== locGeneration.current) return;
        if (data.result) picked = data.result;
      } catch {
        // keep coordinates fallback
      }
      if (!mounted.current || gen !== locGeneration.current) return;
      onChange(picked.label, picked);
      setSuggestions([]);
      setOpen(false);
    } catch {
      if (!mounted.current || gen !== locGeneration.current) return;
      setLocError(
        locale === "en"
          ? "Couldn't get your location"
          : "No se pudo obtener tu ubicación",
      );
    } finally {
      if (mounted.current && gen === locGeneration.current) setLocating(false);
    }
  };

  const showPanel = (open || loading) && (loading || suggestions.length > 0);

  return (
    <View style={[{ zIndex: 20 }, containerStyle]}>
      <Input
        label={label}
        value={value}
        onChangeText={handleChange}
        icon={icon}
        placeholder={placeholder}
        containerStyle={{ marginBottom: 0 }}
        onBlur={handleBlur}
      />

      {/* Use-my-location shortcut */}
      <Pressable
        onPress={useMyLocation}
        style={styles.gpsRow}
        hitSlop={6}
        disabled={locating}
      >
        {locating ? (
          <ActivityIndicator size="small" color={colors.cta} />
        ) : (
          <Ionicons name="locate" size={15} color={colors.cta} />
        )}
        <Text style={styles.gpsText}>
          {locating
            ? locale === "en"
              ? "Locating…"
              : "Ubicándote…"
            : locale === "en"
            ? "Use my current location"
            : "Usar mi ubicación actual"}
        </Text>
      </Pressable>
      {locError ? <Text style={styles.gpsError}>{locError}</Text> : null}

      {/* Suggestions dropdown */}
      {showPanel ? (
        <View style={styles.panel}>
          {loading ? (
            <View style={styles.loadingRow}>
              <ActivityIndicator size="small" color={colors.cta} />
              <Text style={styles.loadingText}>
                {locale === "en" ? "Searching…" : "Buscando…"}
              </Text>
            </View>
          ) : (
            suggestions.map((s, i) => (
              <Pressable
                key={s.id}
                onPress={() => pick(s)}
                style={[
                  styles.row,
                  i < suggestions.length - 1 && styles.rowBorder,
                ]}
              >
                <View style={styles.rowIcon}>
                  <Ionicons name="location" size={15} color={colors.cta} />
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={styles.rowTitle} numberOfLines={1}>
                    {s.title}
                  </Text>
                  {s.subtitle ? (
                    <Text style={styles.rowSubtitle} numberOfLines={1}>
                      {s.subtitle}
                    </Text>
                  ) : null}
                </View>
              </Pressable>
            ))
          )}
        </View>
      ) : null}
    </View>
  );
}

const makeStyles = () =>
  StyleSheet.create({
    panel: {
      backgroundColor: colors.card,
      borderRadius: radius.lg,
      borderWidth: 1,
      borderColor: colors.border,
      marginTop: 6,
      overflow: "hidden",
      ...shadow.lg,
    },
    row: {
      flexDirection: "row",
      alignItems: "center",
      paddingVertical: 12,
      paddingHorizontal: 14,
      gap: 10,
    },
    rowBorder: {
      borderBottomWidth: 1,
      borderBottomColor: colors.border,
    },
    rowIcon: {
      width: 28,
      height: 28,
      borderRadius: 14,
      backgroundColor: colors.ctaXLight,
      alignItems: "center",
      justifyContent: "center",
    },
    rowTitle: { ...type.callout, color: colors.text, fontFamily: font.semibold },
    rowSubtitle: { ...type.small, color: colors.textMuted, marginTop: 1 },
    loadingRow: {
      flexDirection: "row",
      alignItems: "center",
      gap: 10,
      padding: 14,
    },
    loadingText: { ...type.small, color: colors.textMuted },
    gpsRow: {
      flexDirection: "row",
      alignItems: "center",
      gap: 6,
      marginTop: 8,
      alignSelf: "flex-start",
    },
    gpsText: { ...type.small, color: colors.cta, fontFamily: font.semibold },
    gpsError: { ...type.small, color: colors.danger, marginTop: 4 },
  });
