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
import DateTimePicker, { DateTimePickerEvent } from "@react-native-community/datetimepicker";
import { SafeAreaView } from "react-native-safe-area-context";
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { Loading } from "@/components/Loading";
import { api, ApiError } from "@/api/client";
import { useAuth } from "@/auth/AuthContext";
import type { Car } from "@/api/types";
import { colors, radius, shadow, spacing } from "@/theme/colors";
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
    <View style={styles.authCard}>
      <View style={styles.authIconWrap}><Text style={{ fontSize: 40 }}>🔐</Text></View>
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
            <Input label={t("register.name")} value={name} onChangeText={setName} autoCapitalize="words" />
          </View>
          <View style={{ flex: 1 }}>
            <Input label={t("register.lastname")} value={lastname} onChangeText={setLastname} autoCapitalize="words" />
          </View>
        </View>
      )}
      <Input label={t("login.client.phone")} value={phone} onChangeText={setPhone} keyboardType="phone-pad" autoCapitalize="none" placeholder="809-000-0000" />
      {mode === "register" && (
        <Input label={t("register.email")} value={email} onChangeText={setEmail} keyboardType="email-address" autoCapitalize="none" placeholder="opcional" />
      )}
      <Input label={t("login.client.password")} value={password} onChangeText={setPassword} secureTextEntry placeholder="••••••••" />
      {mode === "register" && (
        <Input label={t("register.passwordConfirm")} value={confirm} onChangeText={setConfirm} secureTextEntry placeholder="Repite la contraseña" />
      )}
      <Button title={mode === "login" ? t("login.client.submit") : t("register.submit")} onPress={submit} loading={loading} size="lg" />
    </View>
  );
}

// ─── Booking form ──────────────────────────────────────────────────────────────
export default function BookScreen() {
  const { carId, start: paramStart, end: paramEnd } = useLocalSearchParams<{
    carId: string; start?: string; end?: string;
  }>();
  const router = useRouter();
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
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <Stack.Screen options={{ headerShown: false }} />
      {/* Header */}
      <View style={styles.header}>
        <Pressable onPress={() => router.back()} style={styles.backBtn}>
          <Text style={styles.backText}>←</Text>
        </Pressable>
        <View style={{ flex: 1 }}>
          <Text style={styles.headerTitle}>{t("cars.book")}</Text>
          {car ? (
            <Text style={styles.headerSub}>{car.brand ? `${car.brand} ` : ""}{car.name ?? car.model ?? ""}</Text>
          ) : null}
        </View>
      </View>

      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView ref={scrollRef} contentContainerStyle={styles.body} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>
          {carErr ? <View style={styles.errBox}><Text style={styles.errText}>⚠️  {carErr}</Text></View> : null}

          {!authDone ? (
            <LoginRequired onSuccess={() => setAuthDone(true)} />
          ) : (
            <>
              {/* Dates */}
              <View style={styles.section}>
                <Text style={styles.sectionLabel}>{locale === "en" ? "📅 Rental Period" : "📅 Período de renta"}</Text>
                <View style={styles.datesRow}>
                  <Pressable onPress={() => { setShowPicker("start"); setPickerMode("date"); }} style={styles.datePill}>
                    <Text style={styles.datePillLabel}>{locale === "en" ? "Pickup" : "Recogida"}</Text>
                    <Text style={styles.datePillDate}>{fmtDate(start)}</Text>
                    <Text style={styles.datePillTime}>{fmtTime(start)}</Text>
                  </Pressable>
                  <Text style={styles.dateArrow}>→</Text>
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
              </View>

              {/* Price summary */}
              {car ? (
                <View style={styles.summaryCard}>
                  <View style={styles.summaryRow}>
                    <Text style={styles.summaryLabel}>{money(car.price_day ?? car.price)} × {days} {locale === "en" ? "days" : "días"}</Text>
                    <Text style={styles.summaryTotal}>{money(total)}</Text>
                  </View>
                  <Text style={styles.summaryNote}>{locale === "en" ? "Estimated total" : "Total estimado"}</Text>
                </View>
              ) : null}

              {/* Locations */}
              <View style={styles.section}>
                <Text style={styles.sectionLabel}>{locale === "en" ? "📍 Locations" : "📍 Lugares"}</Text>
                <Input label={t("booking.placeStart")} value={placeStart} onChangeText={setPlaceStart} placeholder={locale === "en" ? "Airport, hotel, address…" : "Aeropuerto, hotel, dirección…"} />
                <Input label={t("booking.placeEnd")} value={placeEnd} onChangeText={setPlaceEnd} placeholder={locale === "en" ? "Same as pickup if empty" : "Igual al recogido si está vacío"} />
              </View>

              {/* Notes */}
              <View style={styles.section}>
                <Text style={styles.sectionLabel}>{locale === "en" ? "💬 Notes (optional)" : "💬 Notas (opcional)"}</Text>
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
              </View>

              <View style={styles.cta}>
                <Button title={`${t("book.confirm")} · ${money(total)}`} onPress={submit} loading={submitting} size="lg" />
              </View>
            </>
          )}
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  header: {
    flexDirection: "row", alignItems: "center",
    paddingHorizontal: spacing.lg, paddingVertical: spacing.md,
    backgroundColor: colors.dark,
  },
  backBtn: {
    width: 36, height: 36, borderRadius: 18,
    backgroundColor: "rgba(255,255,255,0.1)",
    alignItems: "center", justifyContent: "center", marginRight: 12,
  },
  backText: { fontSize: 20, fontWeight: "700", color: "#fff", marginTop: -2 },
  headerTitle: { fontSize: 18, fontWeight: "800", color: "#fff" },
  headerSub: { fontSize: 13, color: "rgba(255,255,255,0.6)", marginTop: 1 },

  body: { paddingBottom: 32 },
  errBox: { margin: spacing.lg, padding: 12, backgroundColor: colors.dangerBg, borderRadius: radius.md },
  errText: { color: colors.danger, fontSize: 13 },

  section: {
    backgroundColor: colors.card, marginTop: 8,
    padding: spacing.lg,
    borderTopWidth: 1, borderTopColor: colors.border,
    borderBottomWidth: 1, borderBottomColor: colors.border,
  },
  sectionLabel: { fontSize: 14, fontWeight: "700", color: colors.text, marginBottom: 14 },

  datesRow: { flexDirection: "row", alignItems: "center" },
  datePill: {
    flex: 1, backgroundColor: colors.borderLight,
    borderRadius: radius.md, padding: 12,
    borderWidth: 1.5, borderColor: colors.border,
  },
  datePillLabel: { fontSize: 10, color: colors.textMuted, fontWeight: "700", textTransform: "uppercase", letterSpacing: 0.4 },
  datePillDate: { fontSize: 15, fontWeight: "700", color: colors.text, marginTop: 3 },
  datePillTime: { fontSize: 12, color: colors.primaryDark, marginTop: 1, fontWeight: "600" },
  dateArrow: { paddingHorizontal: 10, fontSize: 18, color: colors.textMuted },

  summaryCard: {
    backgroundColor: colors.primaryXLight,
    padding: spacing.lg,
    borderTopWidth: 2, borderTopColor: colors.primaryLight,
    borderBottomWidth: 1, borderBottomColor: colors.primaryLight,
  },
  summaryRow: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
  summaryLabel: { fontSize: 15, color: colors.textSecondary, fontWeight: "500" },
  summaryTotal: { fontSize: 22, fontWeight: "800", color: colors.primaryDark },
  summaryNote: { fontSize: 11, color: colors.textMuted, marginTop: 4 },

  textareaWrap: { borderWidth: 1.5, borderColor: colors.border, borderRadius: radius.md, backgroundColor: "#fff" },
  textarea: { padding: 14, minHeight: 80, fontSize: 15, color: colors.text },
  cta: { padding: spacing.lg, paddingTop: spacing.md },

  // Auth card
  authCard: {
    backgroundColor: colors.card,
    margin: spacing.lg, borderRadius: radius.xl,
    padding: spacing.xl,
    ...shadow.lg,
    borderTopWidth: 4, borderTopColor: colors.primary,
  },
  authIconWrap: { alignItems: "center", marginBottom: 12 },
  authTitle: { fontSize: 20, fontWeight: "800", color: colors.text, marginBottom: 4, textAlign: "center" },
  authSub: { fontSize: 13, color: colors.textMuted, textAlign: "center", marginBottom: 20, lineHeight: 18 },
  authToggle: {
    flexDirection: "row", backgroundColor: colors.borderLight,
    borderRadius: radius.md, padding: 3, marginBottom: 20,
  },
  authTab: { flex: 1, paddingVertical: 10, alignItems: "center", borderRadius: radius.sm },
  authTabActive: { backgroundColor: "#fff", ...shadow.sm },
  authTabText: { fontSize: 13, color: colors.textMuted, fontWeight: "600" },
  authTabTextActive: { color: colors.text, fontWeight: "700" },
  nameRow: { flexDirection: "row" },
});
