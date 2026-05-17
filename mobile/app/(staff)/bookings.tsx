import React, { useCallback, useEffect, useState } from "react";
import { FlatList, RefreshControl, StyleSheet, Text, View } from "react-native";
import { useFocusEffect, useRouter } from "expo-router";
import { Card } from "@/components/Card";
import { Loading } from "@/components/Loading";
import { EmptyState } from "@/components/EmptyState";
import { Badge } from "@/components/Badge";
import { Input } from "@/components/Input";
import { api, ApiError } from "@/api/client";
import type { Booking } from "@/api/types";
import { bookingStatus, colors } from "@/theme/colors";
import { i18n, t } from "@/i18n";
import { money, shortDate } from "@/utils/format";

export default function StaffBookingsList() {
  const router = useRouter();
  const [items, setItems] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [err, setErr] = useState<string | null>(null);
  const [q, setQ] = useState("");
  const [status, setStatus] = useState<number | "">("");
  const [from, setFrom] = useState("");
  const [to, setTo] = useState("");

  const load = useCallback(async () => {
    setErr(null);
    try {
      const r = await api.get<{ bookings: Booking[] }>("/bookings", {
        q: q || undefined,
        status: status === "" ? undefined : status,
        from: from || undefined,
        to: to || undefined,
        limit: 100,
      });
      setItems(r.bookings ?? []);
    } catch (e) {
      setErr(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [q, status, from, to]);

  useEffect(() => { load(); }, [load]);
  useFocusEffect(useCallback(() => { load(); }, [load]));

  if (loading) return <Loading />;

  return (
    <View style={{ flex: 1, backgroundColor: colors.bg }}>
      <View style={{ padding: 12, paddingBottom: 0 }}>
        <Input
          placeholder={t("common.search")}
          value={q}
          onChangeText={setQ}
          autoCapitalize="none"
          returnKeyType="search"
          onSubmitEditing={() => load()}
        />
        <View style={{ flexDirection: "row" }}>
          <View style={{ flex: 1, marginRight: 6 }}>
            <Input
              placeholder={`${t("common.from")} (YYYY-MM-DD)`}
              value={from}
              onChangeText={setFrom}
              autoCapitalize="none"
              returnKeyType="done"
              onSubmitEditing={() => load()}
            />
          </View>
          <View style={{ flex: 1, marginLeft: 6 }}>
            <Input
              placeholder={`${t("common.to")} (YYYY-MM-DD)`}
              value={to}
              onChangeText={setTo}
              autoCapitalize="none"
              returnKeyType="done"
              onSubmitEditing={() => load()}
            />
          </View>
        </View>
        <View style={styles.filters}>
          {[
            { v: "", label: "Todas" },
            { v: 0, label: i18n.locale === "en" ? "Pending" : "Pendiente" },
            { v: 1, label: i18n.locale === "en" ? "Confirmed" : "Confirmada" },
            { v: 3, label: i18n.locale === "en" ? "Delivered" : "Entregada" },
            { v: 4, label: i18n.locale === "en" ? "Returned" : "Devuelta" },
            { v: 2, label: i18n.locale === "en" ? "Cancelled" : "Cancelada" },
          ].map((f) => (
            <Text
              key={String(f.v)}
              onPress={() => setStatus(f.v as number | "")}
              style={[styles.chip, status === f.v && styles.chipActive]}
            >
              {f.label}
            </Text>
          ))}
        </View>
      </View>
      {err ? <Text style={styles.err}>{err}</Text> : null}
      <FlatList
        contentContainerStyle={{ padding: 12 }}
        data={items}
        keyExtractor={(b) => String(b.id)}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />
        }
        ListEmptyComponent={<EmptyState title={t("booking.noneStaff")} />}
        renderItem={({ item }) => {
          const s = bookingStatus[Number(item.status ?? 0)];
          return (
            <Card onPress={() => router.push({ pathname: "/(staff)/booking/[id]", params: { id: String(item.id) } })}>
              <View style={styles.row}>
                <Text style={styles.title}>#{item.code ?? item.id}</Text>
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
  filters: { flexDirection: "row", flexWrap: "wrap", marginTop: 4, marginBottom: 8 },
  chip: {
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 12,
    backgroundColor: "#fff",
    borderWidth: 1,
    borderColor: colors.border,
    color: colors.textMuted,
    marginRight: 6,
    marginBottom: 6,
    fontSize: 12,
    overflow: "hidden",
  },
  chipActive: { backgroundColor: colors.primary, borderColor: colors.primary, color: "#fff" },
  row: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
  title: { fontSize: 15, fontWeight: "700", color: colors.text, flexShrink: 1, marginRight: 8 },
  meta: { color: colors.textMuted, fontSize: 13, marginTop: 4 },
  total: { color: colors.primary, fontWeight: "700", fontSize: 16, marginTop: 6 },
  err: { color: colors.danger, padding: 12, textAlign: "center" },
});
