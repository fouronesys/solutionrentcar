/**
 * Reserva tu auto — booking form.
 * Design: white header, "PASO 1 DE 2" label + red progress bar,
 * dark RUTA card, white summary cards, red CTA.
 */
import React, { useEffect, useRef, useState } from "react";
import {
  Alert,
  KeyboardAvoidingView,
  Linking,
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
import { whatsappUrl } from "@/config/contact";
import { PICKUP_SUGGESTIONS } from "@/config/locations";

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

// ─── Main booking screen ────────────────────────────────────────────────────
export default function BookScreen() {
  const { carId, start: paramStart, end: paramEnd } = useLocalSearchParams<{
    carId: string; start?: string; end?: string;
  }>();
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const { role, registerClient } = useAuth();
  const scrollRef = useRef<ScrollView>(null);
  const placesY = useRef(0);
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
  const [sameReturn, setSameReturn] = useState(true);
  const [comment, setComment] = useState("");
  const [submitting, setSubmitting] = useState(false);

  // Guest checkout (book without a prior account)
  const isGuest = role !== "client";
  const [gName, setGName] = useState("");
  const [gLastname, setGLastname] = useState("");
  const [gPhone, setGPhone] = useState("");
  const [gEmail, setGEmail] = useState("");
  const [gPassword, setGPassword] = useState("");

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

  const goToLogin = () => router.push("/login/client");

  const registerGuest = async (): Promise<boolean> => {
    // Validate guest fields
    if (!gName.trim()) { Alert.alert(t("book.guestNameRequired")); return false; }
    if (!gPhone.trim()) { Alert.alert(t("book.guestPhoneRequired")); return false; }
    if (!gPassword.trim()) { Alert.alert(t("book.guestPasswordRequired")); return false; }
    if (gPassword.length < 6) { Alert.alert(t("book.guestPasswordShort")); return false; }
    // Reject a password identical to the phone (compare digits only).
    if (gPassword.replace(/\D/g, "") === gPhone.replace(/\D/g, "") && gPhone.replace(/\D/g, "") !== "") {
      Alert.alert(t("book.guestPasswordSameAsPhone")); return false;
    }

    // Create the account. POST /auth/register returns {role,user,tokens};
    // registerClient adopts that session directly (saves tokens + sets role),
    // so there is no separate login step that could fail or match a wrong account.
    try {
      await registerClient({
        name: gName.trim(),
        lastname: gLastname.trim() || undefined,
        phone: gPhone.trim(),
        email: gEmail.trim() || undefined,
        password: gPassword,
      });
    } catch (e) {
      if (e instanceof ApiError) {
        const msg = e.message?.toLowerCase() ?? "";
        const phoneTaken =
          /(exist|regist|taken|duplicat|already)/.test(msg) ||
          /(tel[eé]fono|correo|usuario)/.test(msg);
        Alert.alert(
          phoneTaken ? t("book.guestPhoneTaken") : (e.message || t("book.guestRegisterError")),
          undefined,
          phoneTaken
            ? [
                { text: t("common.cancel"), style: "cancel" },
                { text: t("book.guestSignIn"), onPress: goToLogin },
              ]
            : undefined,
        );
      } else {
        Alert.alert(t("book.guestRegisterError"));
      }
      return false;
    }
    return true;
  };

  const submit = async () => {
    if (!placeStart.trim()) { Alert.alert(t("book.pickupRequired")); return; }
    setSubmitting(true);
    try {
      // Guest checkout: create account + sign in before booking
      if (isGuest) {
        const ok = await registerGuest();
        if (!ok) { setSubmitting(false); return; }
      }
      const r = await api.post<{ booking_id: number }>("/bookings", {
        car_id: Number(carId),
        start_at: toDbDateTime(start),
        end_at: toDbDateTime(end),
        place_start: placeStart.trim(),
        place_end: sameReturn ? placeStart.trim() : (placeEnd.trim() || placeStart.trim()),
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

          {/* WhatsApp trust banner */}
          <View style={styles.waBanner}>
            <View style={styles.waIconWrap}>
              <Ionicons name="logo-whatsapp" size={20} color={colors.card} />
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.waTitle}>{t("book.whatsappTitle")}</Text>
              <Text style={styles.waBody}>{t("book.whatsappBody")}</Text>
            </View>
          </View>

          {/* RUTA — dark card */}
          <View style={styles.rutaCard}>
                <Text style={styles.rutaLabel}>
                  {locale === "en" ? "ROUTE" : "RUTA"}
                </Text>

                {/* Pickup location */}
                <Pressable
                  style={styles.rutaLocationRow}
                  onPress={() => scrollRef.current?.scrollTo({ y: placesY.current, animated: true })}
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
              <View onLayout={(e) => { placesY.current = Math.max(0, e.nativeEvent.layout.y - 12); }}>
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
                {/* Pickup suggestion chips */}
                <Text style={styles.chipsLabel}>{t("book.suggestionsLabel")}</Text>
                <ScrollView
                  horizontal
                  showsHorizontalScrollIndicator={false}
                  keyboardShouldPersistTaps="handled"
                  contentContainerStyle={styles.chipsRow}
                >
                  {PICKUP_SUGGESTIONS.map((s) => {
                    const active = placeStart === s.value;
                    return (
                      <Pressable
                        key={s.id}
                        onPress={() => setPlaceStart(s.value)}
                        style={[styles.chip, active && styles.chipActive]}
                      >
                        <Text style={[styles.chipText, active && styles.chipTextActive]}>
                          {s.label}
                        </Text>
                      </Pressable>
                    );
                  })}
                </ScrollView>

                {/* Same-place-for-return toggle */}
                <Pressable
                  style={styles.sameReturnRow}
                  onPress={() => setSameReturn((v) => !v)}
                  hitSlop={6}
                >
                  <View style={[styles.checkbox, sameReturn && styles.checkboxOn]}>
                    {sameReturn ? <Ionicons name="checkmark" size={14} color="#FFFFFF" /> : null}
                  </View>
                  <Text style={styles.sameReturnText}>{t("book.sameReturn")}</Text>
                </Pressable>

                {/* Return field + chips — only when returning elsewhere */}
                {!sameReturn ? (
                  <>
                    <Input
                      label={t("booking.placeEnd")}
                      value={placeEnd}
                      onChangeText={setPlaceEnd}
                      icon="flag-outline"
                      placeholder={locale === "en" ? "Same as pickup if empty" : "Igual al de recogida si está vacío"}
                    />
                    <Text style={styles.chipsLabel}>{t("book.suggestionsLabel")}</Text>
                    <ScrollView
                      horizontal
                      showsHorizontalScrollIndicator={false}
                      keyboardShouldPersistTaps="handled"
                      contentContainerStyle={styles.chipsRow}
                    >
                      {PICKUP_SUGGESTIONS.map((s) => {
                        const active = placeEnd === s.value;
                        return (
                          <Pressable
                            key={s.id}
                            onPress={() => setPlaceEnd(s.value)}
                            style={[styles.chip, active && styles.chipActive]}
                          >
                            <Text style={[styles.chipText, active && styles.chipTextActive]}>
                              {s.label}
                            </Text>
                          </Pressable>
                        );
                      })}
                    </ScrollView>
                  </>
                ) : null}
              </Card>
              </View>

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

              {/* Guest checkout — "Tus datos" */}
              {isGuest ? (
                <>
                  <Card style={styles.section} elevation="sm">
                    <View style={styles.sectionLabelRow}>
                      <Ionicons name="person-outline" size={14} color={colors.cta} />
                      <Text style={styles.sectionLabelText}>{t("book.guestSection")}</Text>
                    </View>
                    <View style={styles.nameRow}>
                      <View style={{ flex: 1, marginRight: 8 }}>
                        <Input label={t("book.guestName")} value={gName} onChangeText={setGName} autoCapitalize="words" icon="person-outline" />
                      </View>
                      <View style={{ flex: 1 }}>
                        <Input label={t("book.guestLastname")} value={gLastname} onChangeText={setGLastname} autoCapitalize="words" icon="people-outline" />
                      </View>
                    </View>
                    <Input
                      label={t("book.guestPhone")}
                      value={gPhone}
                      onChangeText={setGPhone}
                      keyboardType="phone-pad"
                      autoCapitalize="none"
                      placeholder="809-000-0000"
                      icon="call-outline"
                    />
                    <Input
                      label={t("book.guestEmail")}
                      value={gEmail}
                      onChangeText={setGEmail}
                      keyboardType="email-address"
                      autoCapitalize="none"
                      placeholder={locale === "en" ? "optional" : "opcional"}
                      icon="mail-outline"
                    />
                    <Input
                      label={t("book.guestPassword")}
                      value={gPassword}
                      onChangeText={setGPassword}
                      secureTextEntry
                      placeholder="••••••••"
                      icon="lock-closed-outline"
                    />
                    <Text style={styles.guestHelp}>{t("book.guestPasswordHelp")}</Text>
                  </Card>

                  {/* Guest info card */}
                  <View style={styles.guestInfoCard}>
                    <Ionicons name="information-circle-outline" size={18} color={colors.info} />
                    <View style={{ flex: 1 }}>
                      <Text style={styles.guestInfoTitle}>{t("book.guestInfoTitle")}</Text>
                      <Text style={styles.guestInfoBody}>{t("book.guestInfoBody")}</Text>
                      <Pressable onPress={goToLogin} hitSlop={6} style={styles.guestLoginLink}>
                        <Text style={styles.guestLoginText}>
                          {t("book.guestHaveAccount")} <Text style={styles.guestLoginTextBold}>{t("book.guestSignIn")}</Text>
                        </Text>
                      </Pressable>
                    </View>
                  </View>
                </>
              ) : null}
        </ScrollView>
      </KeyboardAvoidingView>

      {/* Sticky bottom CTA */}
      <View style={[styles.stickyBar, { paddingBottom: insets.bottom + 12 }]}>
        <Button
          title={`${t("book.ctaNow")} · ${money(total)}`}
          onPress={submit}
          loading={submitting}
          size="lg"
          icon="chevron-forward"
          iconRight
          style={styles.ctaBtn}
        />
      </View>
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

  body: { padding: spacing.lg, paddingBottom: 120 },

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

  // Location suggestion chips
  chipsLabel: { ...type.small, color: colors.textMuted, marginTop: 10, marginBottom: 8 },
  chipsRow: { gap: 8, paddingRight: 4 },
  chip: {
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: radius.full,
    backgroundColor: colors.bg,
    borderWidth: 1.5,
    borderColor: colors.border,
  },
  chipActive: { backgroundColor: colors.ctaXLight, borderColor: colors.cta },
  chipText: { ...type.small, color: colors.textSecondary, fontFamily: font.medium },
  chipTextActive: { color: colors.cta, fontFamily: font.bold },

  // Same-place-for-return toggle
  sameReturnRow: { flexDirection: "row", alignItems: "center", gap: 10, marginTop: 14 },
  checkbox: {
    width: 22, height: 22, borderRadius: radius.sm,
    borderWidth: 1.5, borderColor: colors.border,
    alignItems: "center", justifyContent: "center",
    backgroundColor: colors.bg,
  },
  checkboxOn: { backgroundColor: colors.cta, borderColor: colors.cta },
  sameReturnText: { ...type.callout, color: colors.text },

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

  ctaBtn: { borderRadius: radius.lg, height: 58 },

  // Sticky bottom CTA bar
  stickyBar: {
    paddingHorizontal: spacing.lg,
    paddingTop: 12,
    backgroundColor: colors.card,
    borderTopWidth: 1,
    borderTopColor: colors.border,
    ...shadow.lg,
  },

  // WhatsApp trust banner
  waBanner: {
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
    padding: 14,
    marginBottom: spacing.md,
    backgroundColor: colors.successBg,
    borderRadius: radius.lg,
    borderWidth: 1,
    borderColor: colors.success,
  },
  waIconWrap: {
    width: 38, height: 38, borderRadius: radius.full,
    backgroundColor: "#25D366",
    alignItems: "center", justifyContent: "center",
  },
  waTitle: { ...type.captionMed, color: colors.success, fontFamily: font.bold },
  waBody: { ...type.small, color: colors.textSecondary, marginTop: 2, lineHeight: 16 },

  // Guest checkout
  guestHelp: { ...type.small, color: colors.textMuted, marginTop: -4 },
  guestInfoCard: {
    flexDirection: "row",
    gap: 10,
    padding: 14,
    marginBottom: spacing.md,
    backgroundColor: colors.tint,
    borderRadius: radius.lg,
    borderWidth: 1,
    borderColor: colors.tintBorder,
  },
  guestInfoTitle: { ...type.captionMed, color: colors.text, fontFamily: font.bold },
  guestInfoBody: { ...type.small, color: colors.textSecondary, marginTop: 2, lineHeight: 16 },
  guestLoginLink: { marginTop: 8 },
  guestLoginText: { ...type.small, color: colors.textMuted },
  guestLoginTextBold: { color: colors.cta, fontFamily: font.bold },

  nameRow: { flexDirection: "row" },
});
