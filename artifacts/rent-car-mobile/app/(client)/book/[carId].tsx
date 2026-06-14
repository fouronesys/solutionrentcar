import React, { useEffect, useRef, useState } from "react";
import {
  Alert,
  KeyboardAvoidingView,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  View,
} from "react-native";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { StatusBar } from "expo-status-bar";
import DateTimePicker, { DateTimePickerEvent } from "@react-native-community/datetimepicker";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { Button } from "@/components/Button";
import { Card } from "@/components/Card";
import { Input } from "@/components/Input";
import { Loading } from "@/components/Loading";
import { api, ApiError } from "@/api/client";
import { useAuth } from "@/auth/AuthContext";
import type { Car } from "@/api/types";
import { colors, font, gradients, radius, shadow, spacing, type } from "@/theme/colors";
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
    weekday: "short", day: "2-digit", month: "short",
  });
}
function fmtTime(d: Date) {
  return d.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
}
function daysBetween(a: Date, b: Date) {
  return Math.max(1, Math.ceil((b.getTime() - a.getTime()) / 86400000));
}

function SectionLabel({ icon, text }: { icon: keyof typeof Ionicons.glyphMap; text: string }) {
  return (
    <View style={styles.sectionLabelRow}>
      <Ionicons name={icon} size={14} color={colors.primaryDark} />
      <Text style={styles.sectionLabel}>{text}</Text>
    </View>
  );
}

// ─── Inline auth guard ─────────────────────────────────────────────────────────
function LoginRequired({ onSuccess }: { onSuccess: () => void }) {
  const { role, loginClient, registerClient } = useAuth();
  const [mode, setMode] = useState<"login" | "register">("login");
  const [phone, setPhone] = useState("");
  const [password, setPassword] = useState("");
  const [name, setName] = useState("");
  const [lastname, setLastname] = useState("");
  const [email, setEmail] = useState("");
  const [confirm, setConfirm] = useState("");
  const [loading, setLoading] = useState(false);

  useEffect(() => { if (role === "client") onSuccess(); }, [role, onSuccess]);

  const submit = async () => {
    if (!phone.trim() || !password.trim()) { Alert.alert(t("login.errors.empty")); return; }
    if (mode === "register") {
      if (!name.trim()) { Alert.alert(t("register.errors.required")); return; }
      if (password.length < 6) { Alert.alert(t("register.errors.passwordShort")); return; }
      if (password !== confirm) { Alert.alert(t("register.errors.passwordMismatch")); return; }
    }
    setLoading(true);
    try {
      if (mode === "login") {
        await loginClient(phone.trim(), password);
      } else {
        await registerClient({ name: name.trim(), lastname: lastname.trim() || undefined, phone: phone.trim(), email: email.trim() || undefined, password });
      }
    } catch (e) {
      Alert.alert(e instanceof ApiError ? e.message : t("login.errors.invalid"));
    } finally {
      setLoading(false);
    }
  };

  return (
    <Card style={styles.authCard} padding={spacing.xl} elevation="lg">
      <View style={styles.authIconWrap}>
        <Ionicons name="lock-closed" size={28} color={colors.primaryDark} />
      </View>
      <Text style={styles.authTitle}>{t("login.requiredTitle")}</Text>
      <Text style={styles.authSub}>{t("login.requiredSubtitle")}</Text>

      <View style={styles.authToggle}>
        <Pressable
          onPress={() => setMode("login")}
          style={[styles.authTab, mode === "login" && styles.authTabActive]}
        >
          <Text style={[styles.authTabText, mode === "login" && styles.authTabTextActive]}>
            {t("login.goToLogin")}
          </Text>
        </Pressable>
        <Pressable
          onPress={() => setMode("register")}
          style={[styles.authTab, mode === "register" && styles.authTabActive]}
        >
          <Text style={[styles.authTabText, mode === "register" && styles.authTabTextActive]}>
            {t("login.createAccount")}
          </Text>
        </Pressable>
      </View>

      {mode === "register" && (
        <View style={styles.nameRow}>
          <View style={{ flex: 1, marginRight: 8 }}>
            <Input label={t("register.name")} value={name} onChangeText={setName} autoCapitalize="words" icon="person-outline" />
          </View>
          <View style={{ flex: 1 }}>
            <Input label={t("register.lastname")} value={lastname} onChangeText={setLastname} autoCapitalize="words" />
          </View>
        </View>
      )}
      <Input label={t("login.client.phone")} value={phone} onChangeText={setPhone} keyboardType="phone-pad" autoCapitalize="none" placeholder="809-000-0000" icon="call-outline" />
      {mode === "register" && (
        <Input label={t("register.email")} value={email} onChangeText={setEmail} keyboardType="email-address" autoCapitalize="none" placeholder="opcional" icon="mail-outline" />
      )}
      <Input label={t("login.client.password")} value={password} onChangeText={setPassword} secureTextEntry placeholder="••••••••" icon="lock-closed-outline" />
      {mode === "register" && (
        <Input label={t("register.passwordConfirm")} value={confirm} onChangeText={setConfirm} secureTextEntry placeholder="Repite la contraseña" icon="lock-closed-outline" />
      )}
      <Button title={mode === "login" ? t("login.client.submit") : t("register.submit")} onPress={submit} loading={loading} size="lg" icon="arrow-forward" iconRight />
    </Card>
  );
}

// ─── Booking form ──────────────────────────────────────────────────────────────
export default function BookScreen() {
  const { carId, start: paramStart, end: paramEnd } = useLocalSearchParams<{
    carId: string; start?: string; end?: string;
  }>();
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const { role } = useAuth();
  const scrollRef = useRef<ScrollView>(null);

  const [car, setCar] = useState<Car | null>(null);
  const [loadingCar, setLoadingCar] = useState(true);
  const [carErr, setCarErr] = useState<string | null>(null);

  const [start, setStart] = useState<Date>(() => (paramStart ? new Date(paramStart) : defaultStart()));
  const [end, setEnd] = useState<Date>(() => (paramEnd ? new Date(paramEnd) : defaultEnd()));
  const [showPicker, setShowPicker] = useState<"start" | "end" | null>(null);
  const [pickerMode, setPickerMode] = useState<"date" | "time">("date");
  const [tempDate, setTempDate] = useState<Date | null>(null);

  const [placeStart, setPlaceStart] = useState("");
  const [placeEnd, setPlaceEnd] = useState("");
  const [comment, setComment] = useState("");
  const [submitting, setSubmitting] = useState(false);

  const [authDone, setAuthDone] = useState(role === "client");
  useEffect(() => { if (role === "client") setAuthDone(true); }, [role]);

  useEffect(() => {
    (async () => {
      try {
        const r = await api.get<{ car: Car }>(`/cars/${carId}`);
        setCar(r.car);
      } catch (e) {
        setCarErr(e instanceof ApiError ? e.message : t("common.error"));
      } finally {
        setLoadingCar(false);
      }
    })();
  }, [carId]);

  const days = daysBetween(start, end);
  const total = days * Number(car?.price_day ?? car?.price ?? 0);
  const locale = i18n.locale === "en" ? "en" : "es";

  const handleDateChange = (e: DateTimePickerEvent, d?: Date) => {
    if (Platform.OS === "android") {
      if (e.type !== "set" || !d) { setShowPicker(null); return; }
      if (pickerMode === "date") { setTempDate(d); setPickerMode("time"); return; }
      const combined = new Date(tempDate ?? d);
      combined.setHours(d.getHours(), d.getMinutes(), 0, 0);
      applyDate(combined);
      setPickerMode("date"); setTempDate(null); setShowPicker(null);
    } else {
      if (d) applyDate(d);
      setShowPicker(null);
    }
  };

  const applyDate = (d: Date) => {
    if (showPicker === "start") {
      setStart(d);
      if (d >= end) setEnd(new Date(d.getTime() + 3 * 86400000));
    } else {
      if (d > start) setEnd(d);
    }
  };

  const submit = async () => {
    if (!placeStart.trim()) { Alert.alert(t("book.pickupRequired")); return; }
    setSubmitting(true);
    try {
      const r = await api.post<{ booking_id: number }>("/bookings", {
        car_id: Number(carId),
        start_at: toDbDateTime(start),
        end_at: toDbDateTime(end),
        place_start: placeStart.trim(),
        place_end: placeEnd.trim() || placeStart.trim(),
        comment: comment.trim() || undefined,
      });
      Alert.alert(t("book.success"), undefined, [{
        text: t("common.ok"),
        onPress: () => {
          if (r.booking_id) {
            router.replace({ pathname: "/(client)/booking/[id]", params: { id: String(r.booking_id) } });
          } else {
            router.replace("/(client)/bookings");
          }
        },
      }]);
    } catch (e) {
      Alert.alert(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setSubmitting(false);
    }
  };

  if (loadingCar) return <Loading />;

  return (
    <View style={styles.screen}>
      <Stack.Screen options={{ headerShown: false }} />
      <StatusBar style="light" />

      {/* Hero header */}
      <LinearGradient
        colors={gradients.hero}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
        style={[styles.hero, { paddingTop: insets.top + 10 }]}
      >
        <View style={styles.heroTopRow}>
          <Pressable onPress={() => router.back()} style={styles.backBtn} hitSlop={8}>
            <Ionicons name="arrow-back" size={22} color="#fff" />
          </Pressable>
          <View style={styles.heroBrandRow}>
            <View style={styles.heroLogo}>
              <Ionicons name="car-sport" size={16} color={colors.dark} />
            </View>
            <Text style={styles.heroBrandLabel}>SOLUTION RENT CAR</Text>
          </View>
        </View>
        <Text style={styles.heroTitle}>{t("cars.book")}</Text>
        {car ? (
          <Text style={styles.heroSub}>
            {car.brand ? `${car.brand} ` : ""}{car.name ?? car.model ?? ""}
          </Text>
        ) : null}
      </LinearGradient>

      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView ref={scrollRef} contentContainerStyle={styles.body} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>
          {carErr ? (
            <View style={styles.errBox}>
              <Ionicons name="warning-outline" size={16} color={colors.danger} />
              <Text style={styles.errText}>{carErr}</Text>
            </View>
          ) : null}

          {!authDone ? (
            <LoginRequired onSuccess={() => setAuthDone(true)} />
          ) : (
            <>
              {/* Dates */}
              <Card style={styles.section} elevation="md">
                <SectionLabel icon="calendar-outline" text={locale === "en" ? "Rental Period" : "Período de renta"} />
                <View style={styles.datesRow}>
                  <Pressable onPress={() => { setShowPicker("start"); setPickerMode("date"); }} style={styles.datePill}>
                    <Text style={styles.datePillLabel}>{locale === "en" ? "Pickup" : "Recogida"}</Text>
                    <Text style={styles.datePillDate}>{fmtDate(start)}</Text>
                    <Text style={styles.datePillTime}>{fmtTime(start)}</Text>
                  </Pressable>
                  <View style={styles.dateArrow}>
                    <Ionicons name="arrow-forward" size={16} color={colors.textMuted} />
                  </View>
                  <Pressable onPress={() => { setShowPicker("end"); setPickerMode("date"); }} style={styles.datePill}>
                    <Text style={styles.datePillLabel}>{locale === "en" ? "Return" : "Devolución"}</Text>
                    <Text style={styles.datePillDate}>{fmtDate(end)}</Text>
                    <Text style={styles.datePillTime}>{fmtTime(end)}</Text>
                  </Pressable>
                </View>
                {showPicker ? (
                  <DateTimePicker
                    value={showPicker === "start" ? start : end}
                    mode={Platform.OS === "ios" ? "datetime" : pickerMode}
                    display={Platform.OS === "ios" ? "spinner" : "default"}
                    minimumDate={showPicker === "end" ? new Date(start.getTime() + 3600000) : new Date()}
                    onChange={handleDateChange}
                  />
                ) : null}
              </Card>

              {/* Price summary */}
              {car ? (
                <View style={styles.summaryCard}>
                  <View style={styles.summaryIcon}>
                    <Ionicons name="cash-outline" size={20} color={colors.primaryDark} />
                  </View>
                  <View style={{ flex: 1 }}>
                    <Text style={styles.summaryLabel}>
                      {money(car.price_day ?? car.price)} × {days} {locale === "en" ? "days" : "días"}
                    </Text>
                    <Text style={styles.summaryNote}>{locale === "en" ? "Estimated total" : "Total estimado"}</Text>
                  </View>
                  <Text style={styles.summaryTotal}>{money(total)}</Text>
                </View>
              ) : null}

              {/* Locations */}
              <Card style={styles.section} elevation="md">
                <SectionLabel icon="location-outline" text={locale === "en" ? "Locations" : "Lugares"} />
                <Input label={t("booking.placeStart")} value={placeStart} onChangeText={setPlaceStart} icon="navigate-outline" placeholder={locale === "en" ? "Airport, hotel, address…" : "Aeropuerto, hotel, dirección…"} />
                <Input label={t("booking.placeEnd")} value={placeEnd} onChangeText={setPlaceEnd} icon="flag-outline" placeholder={locale === "en" ? "Same as pickup if empty" : "Igual al recogido si está vacío"} />
              </Card>

              {/* Notes */}
              <Card style={styles.section} elevation="md">
                <SectionLabel icon="chatbubble-ellipses-outline" text={locale === "en" ? "Notes (optional)" : "Notas (opcional)"} />
                <View style={styles.textareaWrap}>
                  <TextInput
                    value={comment}
                    onChangeText={setComment}
                    placeholder={locale === "en" ? "Any special requests…" : "Alguna petición especial…"}
                    placeholderTextColor={colors.textMuted}
                    multiline
                    numberOfLines={3}
                    style={styles.textarea}
                    textAlignVertical="top"
                  />
                </View>
              </Card>

              <View style={styles.cta}>
                <Button title={`${t("book.confirm")} · ${money(total)}`} onPress={submit} loading={submitting} size="lg" icon="checkmark-circle-outline" />
              </View>
            </>
          )}
        </ScrollView>
      </KeyboardAvoidingView>
    </View>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },

  hero: {
    paddingBottom: 22,
    paddingHorizontal: spacing.xl,
    borderBottomLeftRadius: radius.xxl,
    borderBottomRightRadius: radius.xxl,
  },
  heroTopRow: { flexDirection: "row", alignItems: "center", gap: 12, marginBottom: 18 },
  backBtn: {
    width: 40, height: 40, borderRadius: 20,
    backgroundColor: "rgba(255,255,255,0.12)",
    alignItems: "center", justifyContent: "center",
  },
  heroBrandRow: { flexDirection: "row", alignItems: "center", gap: 8 },
  heroLogo: {
    width: 26, height: 26, borderRadius: radius.xs,
    backgroundColor: colors.primary,
    alignItems: "center", justifyContent: "center",
  },
  heroBrandLabel: { ...type.label, color: "rgba(255,255,255,0.65)" },
  heroTitle: { ...type.display, color: "#FFFFFF" },
  heroSub: { ...type.callout, color: "rgba(255,255,255,0.6)", marginTop: 4 },

  body: { padding: spacing.lg, paddingBottom: 40 },
  errBox: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    marginBottom: spacing.md,
    padding: 14,
    backgroundColor: colors.dangerBg,
    borderRadius: radius.md,
  },
  errText: { ...type.caption, color: colors.danger, flex: 1 },

  section: { padding: spacing.lg },
  sectionLabelRow: { flexDirection: "row", alignItems: "center", gap: 6, marginBottom: 14 },
  sectionLabel: { ...type.label, color: colors.textMuted },

  datesRow: { flexDirection: "row", alignItems: "center" },
  datePill: {
    flex: 1, backgroundColor: colors.bg,
    borderRadius: radius.md, padding: 12,
    borderWidth: 1, borderColor: colors.border,
  },
  datePillLabel: { ...type.label, color: colors.textMuted, fontSize: 10 },
  datePillDate: { ...type.title, color: colors.text, marginTop: 3 },
  datePillTime: { ...type.caption, color: colors.primaryDark, marginTop: 1, fontFamily: font.semibold },
  dateArrow: { paddingHorizontal: 8 },

  summaryCard: {
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
    backgroundColor: colors.primaryXLight,
    padding: spacing.lg,
    borderRadius: radius.lg,
    borderWidth: 1,
    borderColor: colors.primaryLight,
    marginBottom: 12,
  },
  summaryIcon: {
    width: 40, height: 40, borderRadius: radius.full,
    backgroundColor: "#FFFFFF",
    alignItems: "center", justifyContent: "center",
  },
  summaryLabel: { ...type.bodyMed, color: colors.textSecondary },
  summaryNote: { ...type.small, color: colors.textMuted, marginTop: 2 },
  summaryTotal: { ...type.h2, color: colors.primaryDark, fontFamily: font.extrabold },

  textareaWrap: { borderWidth: 1.5, borderColor: colors.border, borderRadius: radius.md, backgroundColor: colors.card },
  textarea: { padding: 14, minHeight: 80, ...type.body, color: colors.text },
  cta: { paddingTop: spacing.sm },

  // Auth card
  authCard: {
    borderTopWidth: 4, borderTopColor: colors.primary,
  },
  authIconWrap: {
    alignItems: "center", justifyContent: "center",
    alignSelf: "center",
    width: 64, height: 64, borderRadius: radius.full,
    backgroundColor: colors.primaryXLight,
    borderWidth: 1, borderColor: colors.primaryLight,
    marginBottom: 14,
  },
  authTitle: { ...type.h2, color: colors.text, marginBottom: 4, textAlign: "center" },
  authSub: { ...type.caption, color: colors.textMuted, textAlign: "center", marginBottom: 20, lineHeight: 18 },
  authToggle: {
    flexDirection: "row", backgroundColor: colors.borderLight,
    borderRadius: radius.md, padding: 3, marginBottom: 20,
  },
  authTab: { flex: 1, paddingVertical: 10, alignItems: "center", borderRadius: radius.sm },
  authTabActive: { backgroundColor: colors.card, ...shadow.sm },
  authTabText: { ...type.captionMed, color: colors.textMuted },
  authTabTextActive: { color: colors.text, fontFamily: font.bold },
  nameRow: { flexDirection: "row" },
});
