import React, { useCallback, useEffect, useState } from "react";
import { Alert, Linking, Pressable, ScrollView, StyleSheet, Text, View } from "react-native";
import { Image } from "expo-image";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { StatusBar } from "expo-status-bar";
import { Button } from "@/components/Button";
import { Card } from "@/components/Card";
import { Loading } from "@/components/Loading";
import { api, ApiError } from "@/api/client";
import type { BookingDetail, Payment } from "@/api/types";
import { bookingStatus, colors, font, gradients, radius, shadow, spacing, type } from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";
import { i18n, t } from "@/i18n";
import { dateTime, money } from "@/utils/format";
import { useAuth } from "@/auth/AuthContext";
import { whatsappUrl } from "@/config/contact";

function InfoRow({ icon, label, value }: { icon: keyof typeof Ionicons.glyphMap; label: string; value?: string | number }) {
  const styles = useThemedStyles(makeStyles);
  if (value === undefined || value === null || value === "") return null;
  return (
    <View style={styles.infoRow}>
      <View style={styles.infoLabelRow}>
        <Ionicons name={icon} size={15} color={colors.textMuted} />
        <Text style={styles.infoLabel}>{label}</Text>
      </View>
      <Text style={styles.infoValue}>{String(value)}</Text>
    </View>
  );
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  const styles = useThemedStyles(makeStyles);
  return (
    <Card style={styles.section} elevation="md">
      <Text style={styles.sectionTitle}>{title}</Text>
      {children}
    </Card>
  );
}

export default function BookingDetailScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const { role } = useAuth();
  const styles = useThemedStyles(makeStyles);
  const [detail, setDetail] = useState<BookingDetail | null>(null);
  const [payments, setPayments] = useState<Payment[]>([]);
  const [err, setErr] = useState<string | null>(null);
  const [acting, setActing] = useState(false);

  const load = useCallback(async () => {
    setErr(null);
    try {
      const b = await api.get<BookingDetail>(`/bookings/${id}`);
      setDetail(b);
      const p = await api.get<{ payments: Payment[] }>("/payments", { booking_id: id });
      setPayments(p.payments ?? []);
    } catch (e) {
      setErr(e instanceof ApiError ? e.message : t("common.error"));
    }
  }, [id]);

  useEffect(() => { load(); }, [load]);

  const cancel = async () => {
    Alert.alert(t("booking.confirmCancel"), undefined, [
      { text: t("common.cancel"), style: "cancel" },
      {
        text: t("common.yes"), style: "destructive",
        onPress: async () => {
          setActing(true);
          try { await api.post(`/bookings/${id}/cancel`); await load(); }
          catch (e) { Alert.alert(e instanceof ApiError ? e.message : t("common.error")); }
          finally { setActing(false); }
        },
      },
    ]);
  };

  const confirmWhatsapp = () => {
    if (!detail) return;
    const { booking, car } = detail;
    const code = booking.code ?? String(booking.id);
    const carName = car ? `${car.brand ? `${car.brand} ` : ""}${car.name ?? car.model ?? ""}`.trim() : "";
    const message = t("book.whatsappMessage", {
      code,
      car: carName,
      start: dateTime(booking.start_at),
      end: dateTime(booking.end_at),
    });
    Linking.openURL(whatsappUrl(message)).catch(() =>
      Alert.alert(t("common.error")),
    );
  };

  if (err) return (
    <View style={styles.errContainer}>
      <Stack.Screen options={{ headerShown: true, title: "Error" }} />
      <Ionicons name="warning-outline" size={40} color={colors.danger} />
      <Text style={styles.errText}>{err}</Text>
      <Button title={t("common.retry")} onPress={() => load()} variant="ghost" style={{ marginTop: 12 }} />
    </View>
  );
  if (!detail) return <Loading />;

  const { booking, car } = detail;
  const s = bookingStatus[Number(booking.status ?? 0)];
  const locale = i18n.locale === "en" ? "en" : "es";
  const paid = Number(booking.payment ?? 0);
  const total = Number(booking.total ?? 0);
  const balance = Math.max(0, total - paid);
  const isActive = [0, 1].includes(Number(booking.status));

  return (
    <View style={styles.screen}>
      <Stack.Screen options={{ headerShown: false }} />
      <StatusBar style="light" />

      <ScrollView contentContainerStyle={styles.body} showsVerticalScrollIndicator={false}>
        {/* Car banner */}
        <View style={styles.carBanner}>
          {car?.image ? (
            <Image source={{ uri: car.image }} style={styles.carImg} contentFit="cover" transition={150} cachePolicy="memory-disk" />
          ) : (
            <View style={[styles.carImg, styles.carImgPlaceholder]}>
              <Ionicons name="car-sport" size={64} color={colors.textFaint} />
            </View>
          )}
          <LinearGradient colors={gradients.imageScrim} style={StyleSheet.absoluteFill} />

          <View style={[styles.topBar, { paddingTop: insets.top + 8 }]}>
            <Pressable onPress={() => router.back()} style={styles.backBtn} hitSlop={8}>
              <Ionicons name="arrow-back" size={22} color="#fff" />
            </Pressable>
            {s ? (
              <View style={[styles.statusPill, { backgroundColor: s.bg }]}>
                <View style={[styles.statusDot, { backgroundColor: s.color }]} />
                <Text style={[styles.statusText, { color: s.color }]}>{s[locale]}</Text>
              </View>
            ) : null}
          </View>

          <View style={styles.carOverlay}>
            <Text style={styles.bookingCode}>#{booking.code ?? booking.id}</Text>
            {car ? (
              <Text style={styles.carModel}>
                {car.brand ? `${car.brand} ` : ""}{car.name ?? car.model ?? ""}
              </Text>
            ) : null}
          </View>
        </View>

        <View style={styles.content}>
          {/* Financial summary */}
          <Card style={styles.finRow} padding={0} elevation="md">
            <View style={styles.finCard}>
              <Text style={styles.finLabel}>{t("booking.total")}</Text>
              <Text style={styles.finValue}>{money(total)}</Text>
            </View>
            <View style={[styles.finCard, styles.finDivider]}>
              <Text style={styles.finLabel}>{t("booking.paid")}</Text>
              <Text style={[styles.finValue, { color: colors.success }]}>{money(paid)}</Text>
            </View>
            <View style={[styles.finCard, styles.finDivider]}>
              <Text style={styles.finLabel}>{t("booking.balance")}</Text>
              <Text style={[styles.finValue, { color: balance > 0 ? colors.danger : colors.success }]}>
                {money(balance)}
              </Text>
            </View>
          </Card>

          {/* WhatsApp confirmation */}
          <Pressable style={styles.waButton} onPress={confirmWhatsapp}>
            <Ionicons name="logo-whatsapp" size={20} color="#FFFFFF" />
            <Text style={styles.waButtonText}>{t("book.confirmWhatsapp")}</Text>
          </Pressable>

          {/* Details */}
          <Section title={locale === "en" ? "Booking Details" : "Detalles"}>
            <InfoRow icon="pricetag-outline" label={t("booking.code")} value={booking.code ?? String(booking.id)} />
            <InfoRow icon="calendar-outline" label={t("common.from")} value={dateTime(booking.start_at)} />
            <InfoRow icon="flag-outline" label={t("common.to")} value={dateTime(booking.end_at)} />
            <InfoRow icon="time-outline" label={t("booking.days")} value={booking.day} />
            <InfoRow icon="location-outline" label={t("booking.placeStart")} value={booking.place_start} />
            <InfoRow icon="navigate-outline" label={t("booking.placeEnd")} value={booking.place_end} />
            {booking.comment ? <InfoRow icon="chatbubble-ellipses-outline" label={locale === "en" ? "Notes" : "Notas"} value={booking.comment} /> : null}
          </Section>

          {/* Payments */}
          {payments.length > 0 ? (
            <Section title={t("payment.title")}>
              {payments.map((p) => (
                <View key={p.id} style={styles.payRow}>
                  <View style={styles.payLeft}>
                    <View style={styles.payIcon}>
                      <Ionicons name="card-outline" size={16} color={colors.primaryDark} />
                    </View>
                    <Text style={styles.payDate}>{dateTime(p.created_at)}</Text>
                  </View>
                  <Text style={styles.payAmount}>{money(p.val)}</Text>
                </View>
              ))}
            </Section>
          ) : null}

          {/* Signature */}
          {booking.signature ? (
            <Section title={t("booking.signed")}>
              <Image source={{ uri: booking.signature }} style={styles.sigImg} contentFit="contain" cachePolicy="memory-disk" />
            </Section>
          ) : role === "client" && isActive ? (
            <Section title={t("booking.needsSignature")}>
              <Button
                title={t("booking.signNow")}
                variant="secondary"
                icon="create-outline"
                onPress={() => router.push({ pathname: "/(client)/sign/[id]", params: { id: String(booking.id) } })}
              />
            </Section>
          ) : null}

          {/* Actions */}
          {role === "client" && isActive ? (
            <View style={styles.actionsSection}>
              <Button title={t("booking.cancel")} variant="danger" icon="close-circle-outline" onPress={cancel} loading={acting} />
            </View>
          ) : null}
        </View>
      </ScrollView>
    </View>
  );
}

const makeStyles = () => StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  errContainer: { flex: 1, padding: 24, justifyContent: "center", alignItems: "center", backgroundColor: colors.bg, gap: 12 },
  errText: { ...type.body, color: colors.danger, textAlign: "center" },

  body: { paddingBottom: 40 },

  carBanner: { height: 240, position: "relative", backgroundColor: colors.dark },
  carImg: { width: "100%", height: 240 },
  carImgPlaceholder: { alignItems: "center", justifyContent: "center" },
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
    width: 42, height: 42, borderRadius: 21,
    backgroundColor: "rgba(0,0,0,0.5)",
    alignItems: "center", justifyContent: "center",
  },
  statusPill: {
    flexDirection: "row",
    alignItems: "center",
    gap: 5,
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: radius.full,
  },
  statusDot: { width: 6, height: 6, borderRadius: 3 },
  statusText: { ...type.small, fontFamily: font.semibold },
  carOverlay: {
    position: "absolute", bottom: 0, left: 0, right: 0,
    padding: spacing.xl,
  },
  bookingCode: { ...type.label, color: "rgba(255,255,255,0.7)", marginBottom: 4 },
  carModel: { ...type.h1, color: "#FFFFFF" },

  content: { marginTop: -20 },

  finRow: {
    flexDirection: "row",
    marginHorizontal: spacing.lg,
    overflow: "hidden",
  },
  finCard: { flex: 1, paddingVertical: spacing.lg, paddingHorizontal: spacing.sm, alignItems: "center" },
  finDivider: { borderLeftWidth: 1, borderLeftColor: colors.borderLight },
  finLabel: { ...type.label, color: colors.textMuted, fontSize: 10 },
  finValue: { ...type.h3, color: colors.text, marginTop: 4 },

  section: {
    marginHorizontal: spacing.lg,
    padding: spacing.lg,
  },
  sectionTitle: { ...type.label, color: colors.textMuted, marginBottom: 14 },

  infoRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: colors.borderLight,
    gap: 12,
  },
  infoLabelRow: { flexDirection: "row", alignItems: "center", gap: 7, flex: 1 },
  infoLabel: { ...type.callout, color: colors.textMuted },
  infoValue: { ...type.bodyMed, color: colors.text, maxWidth: "55%", textAlign: "right" },

  payRow: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", paddingVertical: 8 },
  payLeft: { flexDirection: "row", alignItems: "center", gap: 10 },
  payIcon: {
    width: 32, height: 32, borderRadius: radius.full,
    backgroundColor: colors.primaryXLight,
    alignItems: "center", justifyContent: "center",
  },
  payDate: { ...type.caption, color: colors.textSecondary },
  payAmount: { ...type.title, color: colors.primaryDark, fontFamily: font.bold },

  sigImg: { width: "100%", height: 130, backgroundColor: colors.borderLight, borderRadius: radius.md },
  actionsSection: { paddingHorizontal: spacing.lg, marginTop: 4 },

  waButton: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 10,
    height: 54,
    marginHorizontal: spacing.lg,
    marginTop: spacing.md,
    backgroundColor: "#25D366",
    borderRadius: radius.lg,
    ...shadow.md,
  },
  waButtonText: { ...type.title, color: "#FFFFFF", fontFamily: font.bold },
});
