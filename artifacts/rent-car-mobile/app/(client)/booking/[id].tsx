import React, { useCallback, useEffect, useState } from "react";
import { Alert, ScrollView, StyleSheet, Text, View, Image } from "react-native";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import { Button } from "@/components/Button";
import { Card } from "@/components/Card";
import { Loading } from "@/components/Loading";
import { Badge } from "@/components/Badge";
import { api, ApiError } from "@/api/client";
import type { BookingDetail, Payment } from "@/api/types";
import { bookingStatus, colors } from "@/theme/colors";
import { i18n, t } from "@/i18n";
import { dateTime, money } from "@/utils/format";
import { useAuth } from "@/auth/AuthContext";

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
        text: t("common.yes"),
        style: "destructive",
        onPress: async () => {
          setActing(true);
          try {
            await api.post(`/bookings/${id}/cancel`);
            await load();
          } catch (e) {
            Alert.alert(e instanceof ApiError ? e.message : t("common.error"));
          } finally {
            setActing(false);
          }
        },
      },
    ]);
  };

  if (err)
    return (
      <View style={{ flex: 1, padding: 24, justifyContent: "center" }}>
        <Text style={{ color: colors.danger, textAlign: "center" }}>{err}</Text>
      </View>
    );
  if (!detail) return <Loading />;

  const { booking, car } = detail;
  const s = bookingStatus[Number(booking.status ?? 0)];
  const paid = Number(booking.payment ?? 0);
  const total = Number(booking.total ?? 0);
  const balance = Math.max(0, total - paid);

  return (
    <View style={{ flex: 1, backgroundColor: colors.bg }}>
      <Stack.Screen options={{ headerShown: true, title: `#${booking.code ?? booking.id}` }} />
      <ScrollView contentContainerStyle={{ padding: 16 }}>
        <Card>
          <View style={styles.row}>
            <Text style={styles.title}>
              {car?.brand ? `${car.brand} ` : ""}{car?.name ?? car?.model ?? `#${booking.id}`}
            </Text>
            {s ? <Badge label={s[i18n.locale === "en" ? "en" : "es"]} color={s.color} /> : null}
          </View>
          {car?.image ? <Image source={{ uri: car.image }} style={styles.img} /> : null}
          <Row label={t("booking.code")} value={booking.code ?? String(booking.id)} />
          <Row label={t("common.from")} value={dateTime(booking.start_at)} />
          <Row label={t("common.to")} value={dateTime(booking.end_at)} />
          <Row label={t("booking.placeStart")} value={booking.place_start} />
          <Row label={t("booking.placeEnd")} value={booking.place_end} />
          <Row label={t("booking.days")} value={booking.day ? String(booking.day) : undefined} />
          <Row label={t("booking.price")} value={money(booking.price)} />
          <Row label={t("booking.total")} value={money(total)} bold />
          <Row label={t("booking.paid")} value={money(paid)} />
          <Row label={t("booking.balance")} value={money(balance)} bold />
          {booking.comment ? <Text style={styles.comment}>{booking.comment}</Text> : null}
        </Card>

        {payments.length > 0 ? (
          <Card>
            <Text style={styles.section}>{t("payment.title")}</Text>
            {payments.map((p) => (
              <View key={p.id} style={styles.payRow}>
                <Text style={{ color: colors.text }}>{dateTime(p.created_at)}</Text>
                <Text style={{ color: colors.primaryDark, fontWeight: "700" }}>{money(p.val)}</Text>
              </View>
            ))}
          </Card>
        ) : null}

        {booking.signature ? (
          <Card>
            <Text style={styles.section}>{t("booking.signed")}</Text>
            <Image source={{ uri: booking.signature }} style={styles.sig} resizeMode="contain" />
          </Card>
        ) : role === "client" && [0, 1].includes(Number(booking.status)) ? (
          <Card>
            <Text style={styles.section}>{t("booking.needsSignature")}</Text>
            <Button
              title={t("booking.signNow")}
              onPress={() => router.push({ pathname: "/(client)/sign/[id]", params: { id: String(booking.id) } })}
            />
          </Card>
        ) : null}

        {role === "client" && [0, 1].includes(Number(booking.status)) ? (
          <Button title={t("booking.cancel")} variant="danger" onPress={cancel} loading={acting} />
        ) : null}
      </ScrollView>
    </View>
  );
}

function Row({ label, value, bold }: { label: string; value?: string; bold?: boolean }) {
  if (!value) return null;
  return (
    <View style={styles.kv}>
      <Text style={styles.k}>{label}</Text>
      <Text style={[styles.v, bold ? { fontWeight: "700" } : null]}>{value}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  row: { flexDirection: "row", alignItems: "center", justifyContent: "space-between", marginBottom: 10 },
  title: { fontSize: 18, fontWeight: "700", color: colors.text, flexShrink: 1, marginRight: 8 },
  img: { width: "100%", height: 160, borderRadius: 10, marginBottom: 10, backgroundColor: "#eee" },
  kv: { flexDirection: "row", justifyContent: "space-between", paddingVertical: 4 },
  k: { color: colors.textMuted, fontSize: 13 },
  v: { color: colors.text, fontSize: 14, maxWidth: "60%", textAlign: "right" },
  comment: { color: colors.textMuted, marginTop: 10, fontStyle: "italic" },
  section: { fontSize: 16, fontWeight: "700", marginBottom: 8, color: colors.text },
  payRow: { flexDirection: "row", justifyContent: "space-between", paddingVertical: 6 },
  sig: { width: "100%", height: 120, backgroundColor: "#fff" },
});
