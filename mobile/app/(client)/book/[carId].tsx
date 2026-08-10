/**
 * Reserva tu auto — booking form.
 * Design: white header, "PASO 1 DE 2" label + red progress bar,
 * dark RUTA card, white summary cards, red CTA.
 */
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
import { Image } from "expo-image";
import { Ionicons } from "@expo/vector-icons";
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
import { colors, font, radius, shadow, spacing, type } from "@/theme/colors";
import { useTheme, useThemedStyles } from "@/theme/ThemeContext";
import { i18n, t } from "@/i18n";
import { money, toDbDateTime } from "@/utils/format";

function defaultStart() {
  const d = new Date(); d.setDate(d.getDate() + 1); d.setHours(10, 0, 0, 0); return d;
}
function defaultEnd() {
  const d = new Date(); d.setDate(d.getDate() + 4); d.setHours(18, 0, 0, 0); return d;
}
function fmtShort(d: Date) {
  return d.toLocaleDateString(i18n.locale === "en" ? "en-US" : "es-ES", {
    day: "2-digit", month: "short",
  });
}
function fmtTime(d: Date) {
  return d.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
}
function daysBetween(a: Date, b: Date) {
  return Math.max(1, Math.ceil((b.getTime() - a.getTime()) / 86400000));
}

// ─── Inline auth guard ──────────────────────────────────────────────────────
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
  const styles = useThemedStyles(makeStyles);

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
        <Ionicons name="lock-closed" size={28} color={colors.cta} />
      </View>
      <Text style={styles.authTitle}>{t("login.requiredTitle")}</Text>
      <Text style={styles.authSub}>{t("login.requiredSubtitle")}</Text>
      <View style={styles.authToggle}>
        {(["login", "register"] as const).map((m) => (
          <Pressable key={m} onPress={() => setMode(m)} style={[styles.authTab, mode === m && styles.authTabActive]}>
            <Text style={[styles.authTabText, mode === m && styles.authTabTextActive]}>
              {m === "login" ? t("login.goToLogin") : t("login.createAccount")}
            </Text>
          </Pressable>
        ))}
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

// ─── Main booking screen ────────────────────────────────────────────────────
export default function BookScreen() {
  const { carId, start: paramStart, end: paramEnd } = useLocalSearchParams<{
    carId: string; start?: string; end?: string;
  }>();
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const { role } = useAuth();
  const scrollRef = useRef<ScrollView>(null);
  const styles = useThemedStyles(makeStyles);
  const { isDark } = useTheme();

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
  const dailyRate = Number(car?.price_day ?? car?.price ?? 0);
  const subtotal = days * dailyRate;
  const protection = Math.round(dailyRate * 0.1 * days);
  const total = subtotal + protection;
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
      <StatusBar style={isDark ? "light" : "dark"} />

      {/* White header */}
      <View style={[styles.header, { paddingTop: insets.top + 10 }]}>
        <Pressable onPress={() => router.back()} style={styles.backBtn} hitSlop={8}>
          <Ionicons name="arrow-back" size={22} color={colors.text} />
        </Pressable>
        <Image
          source={require("../../../assets/images/logo.png")}
          style={styles.headerLogo}
          contentFit="contain"
        />
        <View style={{ flex: 1 }}>
          <Text style={styles.headerTitle}>
            {locale === "en" ? "New booking" : "Nueva reserva"}
          </Text>
          <Text style={styles.headerSub}>
            {locale === "en" ? "Your next move." : "Tu próximo movimiento."}
          </Text>
        </View>
      </View>

      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView
          ref={scrollRef}
          contentContainerStyle={styles.body}
          keyboardShouldPersistTaps="handled"
          showsVerticalScrollIndicator={false}
        >
          {/* Step indicator */}
          <View style={styles.stepBlock}>
            <Text style={styles.stepLabel}>
              {locale === "en" ? "STEP 1 OF 2" : "PASO 1 DE 2"}
            </Text>
            <View style={styles.progressRow}>
              <Text style={styles.stepHeading}>
                {locale === "en" ? "Book your car" : "Reserva tu auto"}
              </Text>
              <Text style={styles.progressPct}>50%</Text>
            </View>
            <View style={styles.progressBar}>
              <View style={[styles.progressFill, { width: "50%" }]} />
            </View>
          </View>

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
              {/* RUTA — dark card */}
              <View style={styles.rutaCard}>
                <Text style={styles.rutaLabel}>
                  {locale === "en" ? "ROUTE" : "RUTA"}
                </Text>

                {/* Pickup location */}
                <Pressable
                  style={styles.rutaLocationRow}
                  onPress={() => scrollRef.current?.scrollToEnd({ animated: true })}
                >
                  <View style={styles.rutaIconRed}>
                    <Ionicons name="location" size={16} color="#FFFFFF" />
                  </View>
                  <View style={{ flex: 1 }}>
                    <Text style={styles.rutaLocationSub}>
                      {locale === "en" ? "Pick up & return at" : "Recoger y devolver en"}
                    </Text>
                    <Text style={styles.rutaLocationName} numberOfLines={1}>
                      {placeStart || (locale === "en" ? "Enter pickup location" : "Ingresa lugar de recogida")}
                    </Text>
                  </View>
                  <Ionicons name="chevron-forward" size={18} color={colors.onDarkMuted} />
                </Pressable>

                {/* Dates */}
                <View style={styles.rutaDatesRow}>
                  <Pressable
                    style={styles.rutaDateCell}
                    onPress={() => { setShowPicker("start"); setPickerMode("date"); }}
                  >
                    <Ionicons name="calendar-outline" size={14} color={colors.primary} />
                    <View>
                      <Text style={styles.rutaDateLabel}>{locale === "en" ? "From" : "Desde"}</Text>
                      <Text style={styles.rutaDateValue}>{fmtShort(start)}, {fmtTime(start)}</Text>
                    </View>
                  </Pressable>
                  <View style={styles.rutaDivider} />
                  <Pressable
                    style={styles.rutaDateCell}
                    onPress={() => { setShowPicker("end"); setPickerMode("date"); }}
                  >
                    <Ionicons name="time-outline" size={14} color={colors.primary} />
                    <View>
                      <Text style={styles.rutaDateLabel}>{locale === "en" ? "Until" : "Hasta"}</Text>
                      <Text style={styles.rutaDateValue}>{fmtShort(end)}, {fmtTime(end)}</Text>
                    </View>
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

              {/* Selected car */}
              {car ? (
                <View style={styles.carRow}>
                  <Text style={styles.carRowTitle}>
                    {locale === "en" ? "Selected car" : "Auto seleccionado"}
                  </Text>
                  <Pressable onPress={() => router.back()} style={styles.changeCta}>
                    <Text style={styles.changeCtaText}>
                      {locale === "en" ? "Change" : "Cambiar"}
                    </Text>
                  </Pressable>
                </View>
              ) : null}

              {car ? (
                <View style={styles.carCard}>
                  {car.image ? (
                    <Image source={{ uri: car.image }} style={styles.carImg} contentFit="cover" transition={150} cachePolicy="memory-disk" />
                  ) : (
                    <View style={styles.carImgPlaceholder}>
                      <Ionicons name="car-sport" size={32} color={colors.textFaint} />
                    </View>
                  )}
                  <View style={{ flex: 1 }}>
                    <Text style={styles.carName}>
                      {car.brand ? `${car.brand} ` : ""}{car.name ?? car.model ?? ""}
                    </Text>
                    {[car.type, car.transmission, car.seat ? `${car.seat} ${locale === "en" ? "seats" : "pasajeros"}` : null]
                      .filter(Boolean).join(" · ") ? (
                      <Text style={styles.carSpecs}>
                        {[car.type, car.transmission, car.seat ? `${car.seat} ${locale === "en" ? "seats" : "pasajeros"}` : null].filter(Boolean).join(" · ")}
                      </Text>
                    ) : null}
                    <View style={styles.carPriceRow}>
                      <Text style={styles.carPrice}>{money(dailyRate)}</Text>
                      <Text style={styles.carPricePer}> / {locale === "en" ? "day" : "día"}</Text>
                    </View>
                  </View>
                </View>
              ) : null}

              {/* Locations */}
              <Card style={styles.section} elevation="sm">
                <View style={styles.sectionLabelRow}>
                  <Ionicons name="location-outline" size={14} color={colors.cta} />
                  <Text style={styles.sectionLabelText}>
                    {locale === "en" ? "LOCATIONS" : "LUGARES"}
                  </Text>
                </View>
                <Input
                  label={t("booking.placeStart")}
                  value={placeStart}
                  onChangeText={setPlaceStart}
                  icon="navigate-outline"
                  placeholder={locale === "en" ? "Airport, hotel, address…" : "Aeropuerto, hotel, dirección…"}
                />
                <Input
                  label={t("booking.placeEnd")}
                  value={placeEnd}
                  onChangeText={setPlaceEnd}
                  icon="flag-outline"
                  placeholder={locale === "en" ? "Same as pickup if empty" : "Igual al de recogida si está vacío"}
                />
              </Card>

              {/* Price summary */}
              <View style={styles.summaryCard}>
                <View style={styles.summaryHeader}>
                  <Text style={styles.summaryTitle}>
                    {locale === "en" ? "Price summary" : "Resumen de precio"}
                  </Text>
                  <View style={styles.daysPill}>
                    <Text style={styles.daysPillText}>
                      {days} {locale === "en" ? "days" : "días"}
                    </Text>
                  </View>
                </View>
                {[
                  {
                    label: `${locale === "en" ? "Rental" : "Alquiler"} · ${days} ${locale === "en" ? "days" : "días"}`,
                    amount: subtotal,
                  },
                  {
                    label: locale === "en" ? "Basic protection" : "Protección básica",
                    amount: protection,
                  },
                ].map((row, idx) => (
                  <View key={idx} style={styles.summaryRow}>
                    <Text style={styles.summaryRowLabel}>{row.label}</Text>
                    <Text style={styles.summaryRowAmount}>{money(row.amount)}</Text>
                  </View>
                ))}
                <View style={styles.summaryDivider} />
                <View style={styles.summaryTotalRow}>
                  <Text style={styles.summaryTotalLabel}>
                    {locale === "en" ? "Estimated total" : "Total estimado"}
                  </Text>
                  <Text style={styles.summaryTotalAmount}>{money(total)}</Text>
                </View>
              </View>

              {/* Notes */}
              <Card style={styles.section} elevation="sm">
                <View style={styles.sectionLabelRow}>
                  <Ionicons name="chatbubble-ellipses-outline" size={14} color={colors.cta} />
                  <Text style={styles.sectionLabelText}>
                    {locale === "en" ? "NOTES (OPTIONAL)" : "NOTAS (OPCIONAL)"}
                  </Text>
                </View>
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

              {/* CTA */}
              <Button
                title={locale === "en" ? "CONFIRM BOOKING" : "CONFIRMAR RESERVA"}
                onPress={submit}
                loading={submitting}
                size="lg"
                icon="chevron-forward"
                iconRight
                style={styles.ctaBtn}
              />
            </>
          )}
        </ScrollView>
      </KeyboardAvoidingView>
    </View>
  );
}

const makeStyles = () => StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },

  // Header
  header: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 16,
    paddingBottom: 14,
    backgroundColor: colors.card,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
    gap: 10,
  },
  backBtn: {
    width: 40, height: 40, borderRadius: 20,
    alignItems: "center", justifyContent: "center",
  },
  headerLogo: { width: 46, height: 46 },
  headerTitle: { fontFamily: font.bold, fontSize: 16, color: colors.text, letterSpacing: -0.2 },
  headerSub: { fontFamily: font.regular, fontSize: 12, color: colors.textMuted, marginTop: 1 },

  body: { padding: spacing.lg, paddingBottom: 48 },

  // Step indicator
  stepBlock: { marginBottom: spacing.lg },
  stepLabel: { ...type.label, color: colors.cta, marginBottom: 6 },
  progressRow: { flexDirection: "row", alignItems: "baseline", justifyContent: "space-between", marginBottom: 8 },
  stepHeading: { ...type.h2, color: colors.text },
  progressPct: { ...type.captionMed, color: colors.textMuted },
  progressBar: { height: 4, backgroundColor: colors.borderLight, borderRadius: 2, overflow: "hidden" },
  progressFill: { height: 4, backgroundColor: colors.cta, borderRadius: 2 },

  errBox: {
    flexDirection: "row", alignItems: "center", gap: 8,
    marginBottom: spacing.md, padding: 14,
    backgroundColor: colors.dangerBg, borderRadius: radius.md,
  },
  errText: { ...type.caption, color: colors.danger, flex: 1 },

  // RUTA dark card
  rutaCard: {
    backgroundColor: colors.darkCard,
    borderRadius: radius.xl,
    padding: spacing.lg,
    marginBottom: spacing.md,
    ...shadow.md,
  },
  rutaLabel: { ...type.label, color: colors.ctaLight, fontSize: 9, letterSpacing: 0.8, marginBottom: 12 },
  rutaLocationRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
    backgroundColor: "rgba(255,255,255,0.07)",
    borderRadius: radius.md,
    padding: 14,
    marginBottom: 12,
  },
  rutaIconRed: {
    width: 32, height: 32, borderRadius: radius.sm,
    backgroundColor: colors.cta,
    alignItems: "center", justifyContent: "center",
  },
  rutaLocationSub: { ...type.small, color: colors.onDarkMuted, marginBottom: 2 },
  rutaLocationName: { ...type.bodyMed, color: colors.onDark },
  rutaDatesRow: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "rgba(255,255,255,0.07)",
    borderRadius: radius.md,
    padding: 14,
    gap: 8,
  },
  rutaDateCell: { flex: 1, flexDirection: "row", alignItems: "center", gap: 8 },
  rutaDivider: { width: 1, height: 32, backgroundColor: "rgba(255,255,255,0.12)" },
  rutaDateLabel: { ...type.small, color: colors.onDarkMuted },
  rutaDateValue: { ...type.captionMed, color: colors.onDark, marginTop: 2 },

  // Car row + card
  carRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: spacing.sm,
  },
  carRowTitle: { ...type.title, color: colors.text },
  changeCta: { paddingVertical: 4, paddingHorizontal: 8 },
  changeCtaText: { ...type.captionMed, color: colors.primary },
  carCard: {
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    padding: 14,
    marginBottom: spacing.md,
    ...shadow.sm,
  },
  carImg: { width: 80, height: 60, borderRadius: radius.md },
  carImgPlaceholder: {
    width: 80, height: 60, borderRadius: radius.md,
    backgroundColor: colors.borderLight,
    alignItems: "center", justifyContent: "center",
  },
  carName: { ...type.title, color: colors.text },
  carSpecs: { ...type.caption, color: colors.textSecondary, marginTop: 2 },
  carPriceRow: { flexDirection: "row", alignItems: "baseline", marginTop: 6 },
  carPrice: { fontFamily: font.extrabold, fontSize: 18, color: colors.text },
  carPricePer: { ...type.caption, color: colors.textMuted },

  section: { marginBottom: spacing.md },
  sectionLabelRow: { flexDirection: "row", alignItems: "center", gap: 6, marginBottom: 14 },
  sectionLabelText: { ...type.label, color: colors.textMuted },

  // Summary card
  summaryCard: {
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    padding: spacing.lg,
    marginBottom: spacing.md,
    ...shadow.sm,
  },
  summaryHeader: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", marginBottom: 14 },
  summaryTitle: { ...type.title, color: colors.text },
  daysPill: {
    paddingHorizontal: 10, paddingVertical: 4,
    backgroundColor: colors.infoBg,
    borderRadius: radius.full,
  },
  daysPillText: { ...type.small, color: colors.info },
  summaryRow: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", marginBottom: 8 },
  summaryRowLabel: { ...type.callout, color: colors.textSecondary },
  summaryRowAmount: { ...type.callout, color: colors.text },
  summaryDivider: { height: 1, backgroundColor: colors.border, marginVertical: 12 },
  summaryTotalRow: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
  summaryTotalLabel: { ...type.title, color: colors.text },
  summaryTotalAmount: { fontFamily: font.extrabold, fontSize: 20, color: colors.text },

  textareaWrap: {
    borderWidth: 1.5, borderColor: colors.border,
    borderRadius: radius.md, backgroundColor: colors.card,
  },
  textarea: { padding: 14, minHeight: 80, ...type.body, color: colors.text },

  ctaBtn: { marginTop: spacing.sm, borderRadius: radius.lg, height: 58 },

  // Auth card
  authCard: { borderTopWidth: 4, borderTopColor: colors.cta, marginBottom: spacing.md },
  authIconWrap: {
    alignSelf: "center", width: 64, height: 64, borderRadius: radius.full,
    backgroundColor: colors.ctaXLight, borderWidth: 1, borderColor: colors.ctaLight,
    alignItems: "center", justifyContent: "center", marginBottom: 14,
  },
  authTitle: { ...type.h2, color: colors.text, textAlign: "center", marginBottom: 4 },
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
