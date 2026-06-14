import React, { useCallback, useEffect, useState } from "react";
import { Alert, Image, Pressable, ScrollView, StyleSheet, Text, View } from "react-native";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { Button } from "@/components/Button";
import { Loading } from "@/components/Loading";
import { api, ApiError } from "@/api/client";
import type { BookingDetail, Payment } from "@/api/types";
import { bookingStatus, colors, radius, shadow, spacing } from "@/theme/colors";
import { i18n, t } from "@/i18n";
import { dateTime, money } from "@/utils/format";

function InfoRow({ label, value }: { label: string; value?: string }) {
  if (!value) return null;
  return (
    <View style={styles.infoRow}>
      <Text style={styles.infoLabel}>{label}</Text>
      <Text style={styles.infoValue}>{value}</Text>
    </View>
  );
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <View style={styles.section}>
      <Text style={styles.sectionTitle}>{title}</Text>
      {children}
    </View>
  );
}

export default function StaffBookingDetail() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
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
      <Stack.Screen options={{ headerShown: true }} />
      <Text style={styles.errText}>⚠️  {err}</Text>
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

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <Stack.Screen options={{ headerShown: false }} />
      {/* Header */}
      <View style={styles.header}>
        <Pressable onPress={() => router.back()} style={styles.backBtn}>
          <Text style={styles.backText}>←</Text>
        </Pressable>
        <View style={{ flex: 1 }}>
          <Text style={styles.headerCode}>#{booking.code ?? booking.id}</Text>
          {s ? (
            <View style={[styles.statusPill, { backgroundColor: s.bg }]}>
              <Text style={[styles.statusText, { color: s.color }]}>{s[locale]}</Text>
            </View>
          ) : null}
        </View>
      </View>

      <ScrollView contentContainerStyle={styles.body} showsVerticalScrollIndicator={false}>
        {/* Car */}
        {car ? (
          <View style={styles.carBanner}>
            {car.image ? (
              <Image source={{ uri: car.image }} style={styles.carImg} resizeMode="cover" />
            ) : (
              <View style={[styles.carImg, styles.carImgPlaceholder]}><Text style={{ fontSize: 48 }}>🚗</Text></View>
            )}
            <View style={styles.carOverlay}>
              {car.brand ? <Text style={styles.carBrand}>{car.brand}</Text> : null}
              <Text style={styles.carModel}>{car.name ?? car.model ?? ""}</Text>
            </View>
          </View>
        ) : null}

        {/* Financial */}
        <View style={styles.finRow}>
          <View style={styles.finCard}>
            <Text style={styles.finLabel}>{t("booking.total")}</Text>
            <Text style={styles.finValue}>{money(total)}</Text>
          </View>
          <View style={[styles.finCard, { borderLeftWidth: 1, borderLeftColor: colors.border }]}>
            <Text style={styles.finLabel}>{t("booking.paid")}</Text>
            <Text style={[styles.finValue, { color: colors.success }]}>{money(paid)}</Text>
          </View>
          <View style={[styles.finCard, { borderLeftWidth: 1, borderLeftColor: colors.border }]}>
            <Text style={styles.finLabel}>{t("booking.balance")}</Text>
            <Text style={[styles.finValue, { color: balance > 0 ? colors.danger : colors.success }]}>{money(balance)}</Text>
          </View>
        </View>

        {/* Client */}
        <Section title="👤 Cliente">
          <InfoRow label={locale === "en" ? "Name" : "Nombre"} value={`${client?.name ?? ""} ${client?.lastname ?? ""}`.trim()} />
          <InfoRow label={t("profile.phone")} value={client?.phone} />
          <InfoRow label={t("profile.email")} value={client?.email} />
        </Section>

        {/* Booking info */}
        <Section title={locale === "en" ? "📋 Details" : "📋 Detalles"}>
          <InfoRow label={t("common.from")} value={dateTime(booking.start_at)} />
          <InfoRow label={t("common.to")} value={dateTime(booking.end_at)} />
          <InfoRow label={t("booking.placeStart")} value={booking.place_start} />
          <InfoRow label={t("booking.placeEnd")} value={booking.place_end} />
          <InfoRow label={t("booking.days")} value={booking.day ? String(booking.day) : undefined} />
          {booking.comment ? <InfoRow label={locale === "en" ? "Notes" : "Notas"} value={booking.comment} /> : null}
        </Section>

        {/* Actions */}
        <Section title={`⚡ ${t("booking.actions")}`}>
          {[0, 1].includes(status) && (
            <>
              <Button
                title={t("booking.deliver")}
                onPress={() => confirmCall(t("booking.confirmDeliver"), () => api.post(`/bookings/${id}/deliver`))}
                loading={acting}
                style={{ marginBottom: 10 }}
              />
              <Button
                title={t("booking.cancel")}
                variant="danger"
                onPress={() => confirmCall(t("booking.confirmCancel"), () => api.post(`/bookings/${id}/cancel`))}
                loading={acting}
                style={{ marginBottom: 10 }}
              />
            </>
          )}
          {status === 3 && (
            <Button
              title={t("booking.return")}
              onPress={() => confirmCall(t("booking.confirmReturn"), () => api.post(`/bookings/${id}/return`))}
              loading={acting}
              style={{ marginBottom: 10 }}
            />
          )}
          <Button
            title={`💳  ${t("booking.registerPayment")}`}
            variant="secondary"
            onPress={() => router.push({ pathname: "/(staff)/pay/[bookingId]", params: { bookingId: String(id) } })}
          />
        </Section>

        {/* Payments */}
        {payments.length > 0 && (
          <Section title={`💰 ${t("payment.title")}`}>
            {payments.map((p) => (
              <View key={p.id} style={styles.payRow}>
                <Text style={styles.payDate}>{dateTime(p.created_at)}</Text>
                <Text style={styles.payAmount}>{money(p.val)}</Text>
              </View>
            ))}
          </Section>
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  errContainer: { flex: 1, padding: 24, justifyContent: "center", alignItems: "center" },
  errText: { color: colors.danger, fontSize: 15, textAlign: "center" },

  header: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.md,
    backgroundColor: colors.dark,
  },
  backBtn: {
    width: 36, height: 36, borderRadius: 18,
    backgroundColor: "rgba(255,255,255,0.1)",
    alignItems: "center", justifyContent: "center",
    marginRight: 12,
  },
  backText: { fontSize: 20, fontWeight: "700", color: "#fff", marginTop: -2 },
  headerCode: { fontSize: 18, fontWeight: "800", color: "#fff" },
  statusPill: { alignSelf: "flex-start", paddingHorizontal: 8, paddingVertical: 3, borderRadius: radius.full, marginTop: 4 },
  statusText: { fontSize: 11, fontWeight: "700" },

  body: { paddingBottom: 32 },

  carBanner: { height: 180, position: "relative", backgroundColor: colors.dark },
  carImg: { width: "100%", height: 180 },
  carImgPlaceholder: { alignItems: "center", justifyContent: "center" },
  carOverlay: {
    position: "absolute", bottom: 0, left: 0, right: 0,
    padding: spacing.lg,
    backgroundColor: "rgba(15,23,42,0.65)",
  },
  carBrand: { fontSize: 11, color: "rgba(255,255,255,0.6)", fontWeight: "700", textTransform: "uppercase", letterSpacing: 0.8 },
  carModel: { fontSize: 18, fontWeight: "800", color: "#fff" },

  finRow: { flexDirection: "row", backgroundColor: colors.card, borderBottomWidth: 1, borderBottomColor: colors.border },
  finCard: { flex: 1, padding: spacing.lg, alignItems: "center" },
  finLabel: { fontSize: 11, color: colors.textMuted, fontWeight: "700", textTransform: "uppercase", letterSpacing: 0.4 },
  finValue: { fontSize: 17, fontWeight: "800", color: colors.text, marginTop: 4 },

  section: {
    backgroundColor: colors.card,
    marginTop: 8,
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.md,
    borderTopWidth: 1, borderTopColor: colors.border,
    borderBottomWidth: 1, borderBottomColor: colors.border,
  },
  sectionTitle: { fontSize: 13, fontWeight: "700", color: colors.text, marginBottom: 14 },

  infoRow: { flexDirection: "row", justifyContent: "space-between", paddingVertical: 8, borderBottomWidth: 1, borderBottomColor: colors.borderLight },
  infoLabel: { fontSize: 14, color: colors.textMuted },
  infoValue: { fontSize: 14, color: colors.text, maxWidth: "60%", textAlign: "right" },

  payRow: { flexDirection: "row", justifyContent: "space-between", paddingVertical: 8 },
  payDate: { fontSize: 13, color: colors.textSecondary },
  payAmount: { fontSize: 15, fontWeight: "700", color: colors.primaryDark },
});
