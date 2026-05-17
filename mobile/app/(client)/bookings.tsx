import React, { useCallback, useEffect, useState } from "react";
import { FlatList, RefreshControl, StyleSheet, Text, View } from "react-native";
import { useFocusEffect, useRouter } from "expo-router";
import { Card } from "@/components/Card";
import { Loading } from "@/components/Loading";
import { EmptyState } from "@/components/EmptyState";
import { Badge } from "@/components/Badge";
import { api, ApiError } from "@/api/client";
import type { Booking } from "@/api/types";
import { bookingStatus, colors } from "@/theme/colors";
import { i18n, t } from "@/i18n";
import { money, shortDate } from "@/utils/format";

export default function BookingsList() {
  const router = useRouter();
  const [items, setItems] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [err, setErr] = useState<string | null>(null);

  const load = useCallback(async () => {
    setErr(null);
    try {
      const r = await api.get<{ bookings: Booking[] }>("/bookings", { limit: 50 });
      setItems(r.bookings ?? []);
    } catch (e) {
      setErr(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    load();
  }, [load]);
  useFocusEffect(useCallback(() => { load(); }, [load]));

  if (loading) return <Loading />;

  return (
    <View style={{ flex: 1, backgroundColor: colors.bg }}>
      {err ? <Text style={styles.err}>{err}</Text> : null}
      <FlatList
        contentContainerStyle={{ padding: 12 }}
        data={items}
        keyExtractor={(b) => String(b.id)}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />
        }
        ListEmptyComponent={<EmptyState title={t("booking.noneClient")} />}
        renderItem={({ item }) => {
          const s = bookingStatus[Number(item.status ?? 0)];
          return (
            <Card onPress={() => router.push({ pathname: "/(client)/booking/[id]", params: { id: String(item.id) } })}>
              <View style={styles.row}>
                <Text style={styles.title}>
                  {item.car?.brand ? `${item.car.brand} ` : ""}
                  {item.car?.name ?? item.car?.model ?? `#${item.id}`}
                </Text>
                {s ? <Badge label={s[i18n.locale === "en" ? "en" : "es"]} color={s.color} /> : null}
              </View>
              <Text style={styles.meta}>
                {shortDate(item.start_at)} → {shortDate(item.end_at)}
              </Text>
              <Text style={styles.total}>{money(item.total)}</Text>
            </Card>
          );
        }}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  row: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
  title: { fontSize: 16, fontWeight: "700", color: colors.text, flexShrink: 1, marginRight: 8 },
  meta: { color: colors.textMuted, fontSize: 13, marginTop: 4 },
  total: { color: colors.primary, fontWeight: "700", fontSize: 16, marginTop: 6 },
  err: { color: colors.danger, padding: 12, textAlign: "center" },
});
