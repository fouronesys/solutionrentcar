import React, { useCallback, useEffect, useState } from "react";
import { Alert, ScrollView, StyleSheet, Text, TouchableOpacity, View } from "react-native";
import { Image } from "expo-image";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { StatusBar } from "expo-status-bar";
import { Button } from "@/components/Button";
import { Loading } from "@/components/Loading";
import { api, ApiError } from "@/api/client";
import type { BookingDetail, Payment } from "@/api/types";
import { bookingStatus, colors, font, gradients, radius, shadow, spacing, type } from "@/theme/colors";
import { i18n, t } from "@/i18n";
import { dateTime, money } from "@/utils/format";

function InfoRow({ icon, label, value }: { icon: keyof typeof Ionicons.glyphMap; label: string; value?: string }) {
  if (!value) return null;
  return (
    <View style={styles.infoRow}>
      <View style={styles.infoLabelWrap}>
        <Ionicons name={icon} size={15} color={colors.textMuted} />
        <Text style={styles.infoLabel}>{label}</Text>
      </View>
      <Text style={styles.infoValue}>{value}</Text>
    </View>
  );
}

function Section({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <View style={styles.section}>
      <Text style={styles.sectionTitle}>{label}</Text>
      {children}
    </View>
  );
}

export default function StaffBookingDetail() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const insets = useSafeAreaInsets();
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

  const confirmCall = (msg: string, action: () => Promise<unknown>) =>
    Alert.alert(msg, undefined, [
      { text: t("common.cancel"), style: "cancel" },
      { text: t("common.yes"), onPress: async () => {
        setActing(true);
        try { await action(); await load(); }
        catch (e) { Alert.alert(e instanceof ApiError ? e.message : t("common.error")); }
        finally { setActing(false); }
      }},
    ]);

  if (err) return (
    <View style={styles.errContainer}>
      <Stack.Screen options={{ headerShown: true, title: "Error" }} />
      <Ionicons name="warning-outline" size={40} color={colors.danger} />
      <Text style={styles.errText}>{err}</Text>
      <Button title={t("common.retry")} onPress={() => router.back()} variant="ghost" style={{ marginTop: 12 }} />
    </View>
  );
  if (!detail) return <Loading />;

  const { booking, car, client } = detail;
  const s = bookingStatus[Number(booking.status ?? 0)];
  const locale = i18n.locale === "en" ? "en" : "es";
  const total = Number(booking.total ?? 0);
  const paid = Number(booking.payment ?? 0);
  const balance = Math.max(0, total - paid);
  const status = Number(booking.status);
  const clientName = `${client?.name ?? ""} ${client?.lastname ?? ""}`.trim();

  return (
    <View style={styles.screen}>
      <Stack.Screen options={{ headerShown: false }} />
      <StatusBar style="light" />

      {/* Hero header */}
      <LinearGradient colors={gradients.hero} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={[styles.hero, { paddingTop: insets.top + 8 }]}>
        <View style={styles.heroTop}>
          <TouchableOpacity onPress={() => router.back()} style={styles.backBtn} activeOpacity={0.8}>
            <Ionicons name="arrow-back" size={22} color="#fff" />
          </TouchableOpacity>
          {s ? (
            <View style={[styles.statusPill, { backgroundColor: s.bg }]}>
              <View style={[styles.statusDot, { backgroundColor: s.color }]} />
              <Text style={[styles.statusText, { color: s.color }]}>{s[locale]}</Text>
            </View>
          ) : null}
        </View>
        <Text style={styles.heroLabel}>{t("booking.code")}</Text>
        <Text style={styles.heroCode}>#{booking.code ?? booking.id}</Text>
      </LinearGradient>

      <ScrollView contentContainerStyle={styles.body} showsVerticalScrollIndicator={false}>
        {/* Car banner */}
        {car ? (
          <View style={styles.carBanner}>
            {car.image ? (
              <Image source={{ uri: car.image }} style={styles.carImg} contentFit="cover" transition={150} cachePolicy="memory-disk" />
            ) : (
              <View style={[styles.carImg, styles.carImgPlaceholder]}>
                <Ionicons name="car-sport" size={56} color={colors.textFaint} />
              </View>
            )}
            <LinearGradient colors={gradients.cardScrim} style={styles.carScrim} />
            <View style={styles.carOverlay}>
              {car.brand ? <Text style={styles.carBrand}>{car.brand}</Text> : null}
              <Text style={styles.carModel}>{car.name ?? car.model ?? ""}</Text>
            </View>
          </View>
        ) : null}

        {/* Financial summary */}
        <View style={styles.finCard}>
          <View style={styles.finItem}>
            <Text style={styles.finLabel}>{t("booking.total")}</Text>
            <Text style={styles.finValue}>{money(total)}</Text>
          </View>
          <View style={styles.finDivider} />
          <View style={styles.finItem}>
            <Text style={styles.finLabel}>{t("booking.paid")}</Text>
            <Text style={[styles.finValue, { color: colors.success }]}>{money(paid)}</Text>
          </View>
          <View style={styles.finDivider} />
          <View style={styles.finItem}>
            <Text style={styles.finLabel}>{t("booking.balance")}</Text>
            <Text style={[styles.finValue, { color: balance > 0 ? colors.danger : colors.success }]}>{money(balance)}</Text>
          </View>
        </View>

        {/* Client */}
        <Section label={t("booking.client")}>
          <InfoRow icon="person-outline" label={locale === "en" ? "Name" : "Nombre"} value={clientName} />
          <InfoRow icon="call-outline" label={t("profile.phone")} value={client?.phone} />
          <InfoRow icon="mail-outline" label={t("profile.email")} value={client?.email} />
        </Section>

        {/* Booking info */}
        <Section label={locale === "en" ? "Details" : "Detalles"}>
          <InfoRow icon="calendar-outline" label={t("common.from")} value={dateTime(booking.start_at)} />
          <InfoRow icon="calendar-outline" label={t("common.to")} value={dateTime(booking.end_at)} />
          <InfoRow icon="location-outline" label={t("booking.placeStart")} value={booking.place_start} />
          <InfoRow icon="flag-outline" label={t("booking.placeEnd")} value={booking.place_end} />
          <InfoRow icon="time-outline" label={t("booking.days")} value={booking.day ? String(booking.day) : undefined} />
          {booking.comment ? <InfoRow icon="chatbox-outline" label={locale === "en" ? "Notes" : "Notas"} value={booking.comment} /> : null}
        </Section>

        {/* Actions */}
        <Section label={t("booking.actions")}>
          {[0, 1].includes(status) && (
            <>
              <Button
                title={t("booking.deliver")}
                icon="checkmark-circle-outline"
                onPress={() => confirmCall(t("booking.confirmDeliver"), () => api.post(`/bookings/${id}/deliver`))}
                loading={acting}
                style={{ marginBottom: 10 }}
              />
              <Button
                title={t("booking.cancel")}
                variant="danger"
                icon="close-circle-outline"
                onPress={() => confirmCall(t("booking.confirmCancel"), () => api.post(`/bookings/${id}/cancel`))}
                loading={acting}
                style={{ marginBottom: 10 }}
              />
            </>
          )}
          {status === 3 && (
            <Button
              title={t("booking.return")}
              icon="return-down-back-outline"
              onPress={() => confirmCall(t("booking.confirmReturn"), () => api.post(`/bookings/${id}/return`))}
              loading={acting}
              style={{ marginBottom: 10 }}
            />
          )}
          <Button
            title={t("booking.registerPayment")}
            variant="secondary"
            icon="card-outline"
            onPress={() => router.push({ pathname: "/(staff)/pay/[bookingId]", params: { bookingId: String(id) } })}
          />
        </Section>

        {/* Payments */}
        {payments.length > 0 && (
          <Section label={t("payment.title")}>
            {payments.map((p) => (
              <View key={p.id} style={styles.payRow}>
                <View style={styles.payLeft}>
                  <View style={styles.payIcon}>
                    <Ionicons name="cash-outline" size={16} color={colors.success} />
                  </View>
                  <Text style={styles.payDate}>{dateTime(p.created_at)}</Text>
                </View>
                <Text style={styles.payAmount}>{money(p.val)}</Text>
              </View>
            ))}
          </Section>
        )}
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  errContainer: { flex: 1, padding: 24, justifyContent: "center", alignItems: "center", backgroundColor: colors.bg, gap: 12 },
  errText: { ...type.body, color: colors.danger, textAlign: "center" },

  hero: {
    paddingHorizontal: spacing.xl,
    paddingBottom: 22,
    borderBottomLeftRadius: radius.xxl,
    borderBottomRightRadius: radius.xxl,
  },
  heroTop: { flexDirection: "row", alignItems: "center", justifyContent: "space-between", marginBottom: 16 },
  backBtn: {
    width: 42,
    height: 42,
    borderRadius: 21,
    backgroundColor: "rgba(255,255,255,0.12)",
    alignItems: "center",
    justifyContent: "center",
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
  heroLabel: { ...type.label, color: "rgba(255,255,255,0.55)" },
  heroCode: { ...type.display, color: "#FFFFFF", marginTop: 2 },

  body: { paddingBottom: 36 },

  carBanner: {
    height: 180,
    position: "relative",
    backgroundColor: colors.borderLight,
    marginHorizontal: spacing.lg,
    marginTop: spacing.lg,
    borderRadius: radius.lg,
    overflow: "hidden",
    ...shadow.md,
  },
  carImg: { width: "100%", height: 180 },
  carImgPlaceholder: { alignItems: "center", justifyContent: "center" },
  carScrim: { position: "absolute", left: 0, right: 0, bottom: 0, height: 110 },
  carOverlay: { position: "absolute", bottom: 0, left: 0, right: 0, padding: spacing.lg },
  carBrand: { ...type.label, color: "rgba(255,255,255,0.7)" },
  carModel: { ...type.h2, color: "#fff", marginTop: 2 },

  finCard: {
    flexDirection: "row",
    backgroundColor: colors.card,
    marginHorizontal: spacing.lg,
    marginTop: spacing.lg,
    borderRadius: radius.lg,
    paddingVertical: spacing.lg,
    ...shadow.md,
  },
  finItem: { flex: 1, alignItems: "center" },
  finDivider: { width: 1, backgroundColor: colors.borderLight },
  finLabel: { ...type.label, color: colors.textMuted, fontSize: 10 },
  finValue: { ...type.h3, color: colors.text, fontFamily: font.extrabold, marginTop: 5 },

  section: {
    backgroundColor: colors.card,
    marginHorizontal: spacing.lg,
    marginTop: spacing.lg,
    borderRadius: radius.lg,
    padding: spacing.lg,
    ...shadow.sm,
  },
  sectionTitle: { ...type.label, color: colors.textMuted, marginBottom: 14 },

  infoRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: colors.borderLight,
  },
  infoLabelWrap: { flexDirection: "row", alignItems: "center", gap: 7 },
  infoLabel: { ...type.callout, color: colors.textMuted },
  infoValue: { ...type.bodyMed, color: colors.text, maxWidth: "55%", textAlign: "right" },

  payRow: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", paddingVertical: 8 },
  payLeft: { flexDirection: "row", alignItems: "center", gap: 10 },
  payIcon: {
    width: 30,
    height: 30,
    borderRadius: radius.full,
    backgroundColor: colors.successBg,
    alignItems: "center",
    justifyContent: "center",
  },
  payDate: { ...type.caption, color: colors.textSecondary },
  payAmount: { ...type.title, color: colors.primaryDark, fontFamily: font.bold },
});
