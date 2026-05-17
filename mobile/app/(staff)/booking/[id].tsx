import React, { useCallback, useEffect, useState } from "react";
import { Alert, Image, ScrollView, StyleSheet, Text, View } from "react-native";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import { Button } from "@/components/Button";
import { Card } from "@/components/Card";
import { Loading } from "@/components/Loading";
import { Badge } from "@/components/Badge";
import { api, ApiError } from "@/api/client";
import type { Booking, Payment } from "@/api/types";
import { bookingStatus, colors } from "@/theme/colors";
import { i18n, t } from "@/i18n";
import { dateTime, money } from "@/utils/format";

export default function StaffBookingDetail() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const [booking, setBooking] = useState<Booking | null>(null);
  const [payments, setPayments] = useState<Payment[]>([]);
  const [err, setErr] = useState<string | null>(null);
  const [acting, setActing] = useState(false);

  const load = useCallback(async () => {
    setErr(null);
    try {
      const b = await api.get<{ booking: Booking }>(`/bookings/${id}`);
      setBooking(b.booking);
      const p = await api.get<{ payments: Payment[] }>("/payments", { booking_id: id });
      setPayments(p.payments ?? []);
    } catch (e) {
      setErr(e instanceof ApiError ? e.message : t("common.error"));
    }
  }, [id]);

  useEffect(() => { load(); }, [load]);

  const confirmAndCall = (msg: string, action: () => Promise<void>) => {
    Alert.alert(msg, undefined, [
      { text: t("common.cancel"), style: "cancel" },
      {
        text: t("common.yes"),
        onPress: async () => {
          setActing(true);
          try { await action(); await load(); }
          catch (e) { Alert.alert(e instanceof ApiError ? e.message : t("common.error")); }
          finally { setActing(false); }
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
  if (!booking) return <Loading />;

  const s = bookingStatus[Number(booking.status ?? 0)];
  const total = Number(booking.total ?? 0);
  const paid = Number(booking.payment ?? 0);
  const balance = Math.max(0, total - paid);
  const status = Number(booking.status);

  return (
    <View style={{ flex: 1, backgroundColor: colors.bg }}>
      <Stack.Screen options={{ headerShown: true, title: `#${booking.code ?? booking.id}` }} />
      <ScrollView contentContainerStyle={{ padding: 16 }}>
        <Card>
          <View style={styles.row}>
            <Text style={styles.title}>
              {booking.car?.brand ? `${booking.car.brand} ` : ""}
              {booking.car?.name ?? booking.car?.model ?? `#${booking.id}`}
            </Text>
            {s ? <Badge label={s[i18n.locale === "en" ? "en" : "es"]} color={s.color} /> : null}
          </View>
          {booking.car?.image ? <Image source={{ uri: booking.car.image }} style={styles.img} /> : null}
          <Row label={t("booking.client")} value={`${booking.client?.name ?? ""} ${booking.client?.lastname ?? ""}`.trim()} />
          <Row label={t("profile.phone")} value={booking.client?.phone} />
          <Row label={t("common.from")} value={dateTime(booking.start_at)} />
          <Row label={t("common.to")} value={dateTime(booking.end_at)} />
          <Row label={t("booking.placeStart")} value={booking.place_start} />
          <Row label={t("booking.placeEnd")} value={booking.place_end} />
          <Row label={t("booking.days")} value={String(booking.days ?? "")} />
          <Row label={t("booking.total")} value={money(total)} bold />
          <Row label={t("booking.paid")} value={money(paid)} />
          <Row label={t("booking.balance")} value={money(balance)} bold />
          {booking.comment ? <Text style={styles.comment}>{booking.comment}</Text> : null}
        </Card>

        <Card>
          <Text style={styles.section}>{t("booking.actions")}</Text>
          {[0, 1].includes(status) && (
            <>
              <Button
                title={t("booking.deliver")}
                onPress={() => confirmAndCall(t("booking.confirmDeliver"), () => api.post(`/bookings/${id}/deliver`))}
                loading={acting}
                style={{ marginBottom: 8 }}
              />
              <Button
                title={t("booking.cancel")}
                variant="danger"
                onPress={() => confirmAndCall(t("booking.confirmCancel"), () => api.post(`/bookings/${id}/cancel`))}
                loading={acting}
                style={{ marginBottom: 8 }}
              />
            </>
          )}
          {status === 3 && (
            <Button
              title={t("booking.return")}
              onPress={() => confirmAndCall(t("booking.confirmReturn"), () => api.post(`/bookings/${id}/return`))}
              loading={acting}
              style={{ marginBottom: 8 }}
            />
          )}
          <Button
            title={t("booking.registerPayment")}
            variant="secondary"
            onPress={() => router.push({ pathname: "/(staff)/pay/[bookingId]", params: { bookingId: String(id) } })}
          />
        </Card>

        {payments.length > 0 && (
          <Card>
            <Text style={styles.section}>{t("payment.title")}</Text>
            {payments.map((p) => (
              <View key={p.id} style={styles.payRow}>
                <Text style={{ color: colors.text }}>{dateTime(p.created_at)}</Text>
                <Text style={{ color: colors.primary, fontWeight: "700" }}>{money(p.val)}</Text>
              </View>
            ))}
          </Card>
        )}
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
  section: { fontSize: 16, fontWeight: "700", marginBottom: 10, color: colors.text },
  comment: { color: colors.textMuted, marginTop: 10, fontStyle: "italic" },
  payRow: { flexDirection: "row", justifyContent: "space-between", paddingVertical: 6 },
});
