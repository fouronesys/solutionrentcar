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
import { useAuth } from "@/auth/AuthContext";

function InfoRow({ label, value, bold, large }: { label: string; value?: string | number; bold?: boolean; large?: boolean }) {
  if (value === undefined || value === null || value === "") return null;
  return (
    <View style={styles.infoRow}>
      <Text style={styles.infoLabel}>{label}</Text>
      <Text style={[styles.infoValue, bold && styles.infoValueBold, large && styles.infoValueLarge]}>
        {String(value)}
      </Text>
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

export default function BookingDetailScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const { role } = useAuth();
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

  if (err) return (
    <View style={styles.errContainer}>
      <Stack.Screen options={{ headerShown: true }} />
      <Text style={styles.errText}>⚠️  {err}</Text>
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
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <Stack.Screen options={{ headerShown: false }} />
      {/* Header */}
      <View style={styles.header}>
        <Pressable onPress={() => router.back()} style={styles.backBtn}>
          <Text style={styles.backText}>←</Text>
        </Pressable>
        <View style={{ flex: 1 }}>
          <Text style={styles.headerTitle}>#{booking.code ?? booking.id}</Text>
          {s ? (
            <View style={[styles.statusPill, { backgroundColor: s.bg }]}>
              <Text style={[styles.statusText, { color: s.color }]}>{s[locale]}</Text>
            </View>
          ) : null}
        </View>
      </View>

      <ScrollView contentContainerStyle={styles.body} showsVerticalScrollIndicator={false}>
        {/* Car banner */}
        {car ? (
          <View style={styles.carBanner}>
            {car.image ? (
              <Image source={{ uri: car.image }} style={styles.carImg} resizeMode="cover" />
            ) : (
              <View style={[styles.carImg, styles.carImgPlaceholder]}>
                <Text style={{ fontSize: 48 }}>🚗</Text>
              </View>
            )}
            <View style={styles.carOverlay}>
              {car.brand ? <Text style={styles.carBrand}>{car.brand}</Text> : null}
              <Text style={styles.carModel}>{car.name ?? car.model ?? ""}</Text>
            </View>
          </View>
        ) : null}

        {/* Financial summary */}
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
            <Text style={[styles.finValue, { color: balance > 0 ? colors.danger : colors.success }]}>
              {money(balance)}
            </Text>
          </View>
        </View>

        {/* Details */}
        <Section title={locale === "en" ? "Booking Details" : "Detalles"}>
          <InfoRow label={t("booking.code")} value={booking.code ?? String(booking.id)} />
          <InfoRow label={t("common.from")} value={dateTime(booking.start_at)} />
          <InfoRow label={t("common.to")} value={dateTime(booking.end_at)} />
          <InfoRow label={t("booking.days")} value={booking.day} />
          <InfoRow label={t("booking.placeStart")} value={booking.place_start} />
          <InfoRow label={t("booking.placeEnd")} value={booking.place_end} />
          {booking.comment ? <InfoRow label={locale === "en" ? "Notes" : "Notas"} value={booking.comment} /> : null}
        </Section>

        {/* Payments */}
        {payments.length > 0 ? (
          <Section title={t("payment.title")}>
            {payments.map((p) => (
              <View key={p.id} style={styles.payRow}>
                <Text style={styles.payDate}>{dateTime(p.created_at)}</Text>
                <Text style={styles.payAmount}>{money(p.val)}</Text>
              </View>
            ))}
          </Section>
        ) : null}

        {/* Signature */}
        {booking.signature ? (
          <Section title={t("booking.signed")}>
            <Image source={{ uri: booking.signature }} style={styles.sigImg} resizeMode="contain" />
          </Section>
        ) : role === "client" && isActive ? (
          <Section title={t("booking.needsSignature")}>
            <Button
              title={t("booking.signNow")}
              variant="secondary"
              onPress={() => router.push({ pathname: "/(client)/sign/[id]", params: { id: String(booking.id) } })}
            />
          </Section>
        ) : null}

        {/* Actions */}
        {role === "client" && isActive ? (
          <View style={styles.actionsSection}>
            <Button title={t("booking.cancel")} variant="danger" onPress={cancel} loading={acting} />
          </View>
        ) : null}
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
    backgroundColor: colors.card,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
  },
  backBtn: {
    width: 36, height: 36, borderRadius: 18,
    backgroundColor: colors.borderLight,
    alignItems: "center", justifyContent: "center",
    marginRight: 12,
  },
  backText: { fontSize: 20, fontWeight: "700", color: colors.text, marginTop: -2 },
  headerTitle: { fontSize: 18, fontWeight: "800", color: colors.text },
  statusPill: { alignSelf: "flex-start", paddingHorizontal: 8, paddingVertical: 3, borderRadius: radius.full, marginTop: 4 },
  statusText: { fontSize: 11, fontWeight: "700" },

  body: { paddingBottom: 32 },

  carBanner: { height: 180, position: "relative", backgroundColor: colors.dark },
  carImg: { width: "100%", height: 180 },
  carImgPlaceholder: { alignItems: "center", justifyContent: "center" },
  carOverlay: {
    position: "absolute", bottom: 0, left: 0, right: 0,
    padding: spacing.lg,
    backgroundColor: "rgba(15,23,42,0.6)",
  },
  carBrand: { fontSize: 11, color: "rgba(255,255,255,0.6)", fontWeight: "700", textTransform: "uppercase", letterSpacing: 0.8 },
  carModel: { fontSize: 18, fontWeight: "800", color: "#fff" },

  finRow: {
    flexDirection: "row",
    backgroundColor: colors.card,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
  },
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
  sectionTitle: { fontSize: 11, fontWeight: "700", color: colors.textMuted, textTransform: "uppercase", letterSpacing: 0.8, marginBottom: 14 },

  infoRow: { flexDirection: "row", justifyContent: "space-between", alignItems: "flex-start", paddingVertical: 8, borderBottomWidth: 1, borderBottomColor: colors.borderLight },
  infoLabel: { fontSize: 14, color: colors.textMuted, flex: 1 },
  infoValue: { fontSize: 14, color: colors.text, maxWidth: "55%", textAlign: "right" },
  infoValueBold: { fontWeight: "700" },
  infoValueLarge: { fontSize: 17, fontWeight: "800", color: colors.primaryDark },

  payRow: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", paddingVertical: 8 },
  payDate: { fontSize: 13, color: colors.textSecondary },
  payAmount: { fontSize: 15, fontWeight: "700", color: colors.primaryDark },

  sigImg: { width: "100%", height: 120, backgroundColor: colors.borderLight, borderRadius: radius.md },
  actionsSection: { padding: spacing.lg, marginTop: 8 },
});
