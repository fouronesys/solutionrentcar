import React, { useCallback, useEffect, useState } from "react";
import { RefreshControl, ScrollView, StyleSheet, Text, View } from "react-native";
import { useFocusEffect, useRouter } from "expo-router";
import { Card } from "@/components/Card";
import { Loading } from "@/components/Loading";
import { EmptyState } from "@/components/EmptyState";
import { api, ApiError } from "@/api/client";
import type { Agenda } from "@/api/types";
import { colors } from "@/theme/colors";
import { t } from "@/i18n";
import { dateTime, todayIso } from "@/utils/format";

export default function AgendaScreen() {
  const router = useRouter();
  const [data, setData] = useState<Agenda | null>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [err, setErr] = useState<string | null>(null);

  const load = useCallback(async () => {
    setErr(null);
    try {
      const r = await api.get<Agenda>("/agenda", { date: todayIso() });
      setData(r);
    } catch (e) {
      setErr(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { load(); }, [load]);
  useFocusEffect(useCallback(() => { load(); }, [load]));

  if (loading) return <Loading />;

  return (
    <ScrollView
      style={{ flex: 1, backgroundColor: colors.bg }}
      contentContainerStyle={{ padding: 12 }}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />}
    >
      {err ? <Text style={styles.err}>{err}</Text> : null}
      <Text style={styles.heading}>📦 {t("agenda.deliveries")}</Text>
      {data?.deliveries?.length ? (
        data.deliveries.map((it) => (
          <Card key={`d-${it.booking.id}`} onPress={() => router.push({ pathname: "/(staff)/booking/[id]", params: { id: String(it.booking.id) } })}>
            <Text style={styles.title}>{it.car?.brand ? `${it.car.brand} ` : ""}{it.car?.name ?? it.car?.model ?? ""}</Text>
            <Text style={styles.meta}>{it.client?.name} {it.client?.lastname ?? ""} · {it.client?.phone ?? ""}</Text>
            <Text style={styles.meta}>{dateTime(it.booking.start_at)}</Text>
          </Card>
        ))
      ) : (
        <EmptyState title={t("agenda.noDeliveries")} />
      )}

      <Text style={[styles.heading, { marginTop: 18 }]}>🔁 {t("agenda.returns")}</Text>
      {data?.returns?.length ? (
        data.returns.map((it) => (
          <Card key={`r-${it.booking.id}`} onPress={() => router.push({ pathname: "/(staff)/booking/[id]", params: { id: String(it.booking.id) } })}>
            <Text style={styles.title}>{it.car?.brand ? `${it.car.brand} ` : ""}{it.car?.name ?? it.car?.model ?? ""}</Text>
            <Text style={styles.meta}>{it.client?.name} {it.client?.lastname ?? ""} · {it.client?.phone ?? ""}</Text>
            <Text style={styles.meta}>{dateTime(it.booking.end_at)}</Text>
          </Card>
        ))
      ) : (
        <EmptyState title={t("agenda.noReturns")} />
      )}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  heading: { fontSize: 18, fontWeight: "700", color: colors.text, marginBottom: 10 },
  title: { fontSize: 16, fontWeight: "700", color: colors.text },
  meta: { color: colors.textMuted, fontSize: 13, marginTop: 4 },
  err: { color: colors.danger, padding: 12, textAlign: "center" },
});
