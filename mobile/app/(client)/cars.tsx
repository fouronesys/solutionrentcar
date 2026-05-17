import React, { useCallback, useEffect, useState } from "react";
import { FlatList, Image, RefreshControl, StyleSheet, Text, View } from "react-native";
import { useRouter } from "expo-router";
import { Card } from "@/components/Card";
import { Loading } from "@/components/Loading";
import { EmptyState } from "@/components/EmptyState";
import { Badge } from "@/components/Badge";
import { Input } from "@/components/Input";
import { api, ApiError } from "@/api/client";
import { carStatus, colors } from "@/theme/colors";
import type { Car } from "@/api/types";
import { i18n, t } from "@/i18n";
import { money } from "@/utils/format";

export default function CarsScreen() {
  const router = useRouter();
  const [cars, setCars] = useState<Car[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [err, setErr] = useState<string | null>(null);
  const [q, setQ] = useState("");

  const load = useCallback(async () => {
    setErr(null);
    try {
      const r = await api.get<{ cars: Car[] }>("/cars", { status: 0, q: q || undefined, limit: 50 });
      setCars(r.cars ?? []);
    } catch (e) {
      setErr(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [q]);

  useEffect(() => {
    load();
  }, [load]);

  if (loading) return <Loading />;

  return (
    <View style={styles.container}>
      <View style={{ padding: 12, paddingBottom: 0 }}>
        <Input
          placeholder={t("common.search")}
          value={q}
          onChangeText={setQ}
          autoCapitalize="none"
          returnKeyType="search"
          onSubmitEditing={() => load()}
        />
      </View>
      {err ? <Text style={styles.err}>{err}</Text> : null}
      <FlatList
        contentContainerStyle={{ padding: 12 }}
        data={cars}
        keyExtractor={(c) => String(c.id)}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => {
              setRefreshing(true);
              load();
            }}
          />
        }
        ListEmptyComponent={<EmptyState title={t("common.empty")} />}
        renderItem={({ item }) => {
          const status = carStatus[Number(item.status ?? 0)];
          return (
            <Card onPress={() => router.push({ pathname: "/(client)/car/[id]", params: { id: String(item.id) } })}>
              {item.image ? (
                <Image source={{ uri: item.image }} style={styles.img} />
              ) : (
                <View style={[styles.img, styles.imgPlaceholder]}>
                  <Text style={{ color: colors.textMuted }}>{t("cars.noPhoto")}</Text>
                </View>
              )}
              <View style={styles.row}>
                <Text style={styles.name}>
                  {item.brand ? `${item.brand} ` : ""}
                  {item.name ?? item.model ?? ""}
                </Text>
                {status ? <Badge label={status[i18n.locale === "en" ? "en" : "es"]} color={status.color} /> : null}
              </View>
              <Text style={styles.meta}>
                {item.year ? `${item.year} · ` : ""}
                {item.transmission ?? ""} {item.fuel ? `· ${item.fuel}` : ""}
              </Text>
              <Text style={styles.price}>
                {money(item.price_day ?? item.price)} <Text style={styles.per}>{t("cars.perDay")}</Text>
              </Text>
            </Card>
          );
        }}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.bg },
  img: { width: "100%", height: 160, borderRadius: 12, marginBottom: 10, backgroundColor: "#eee" },
  imgPlaceholder: { alignItems: "center", justifyContent: "center" },
  row: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
  name: { fontSize: 16, fontWeight: "700", color: colors.text, flexShrink: 1, marginRight: 8 },
  meta: { color: colors.textMuted, fontSize: 13, marginTop: 2 },
  price: { fontSize: 18, color: colors.primary, fontWeight: "700", marginTop: 8 },
  per: { fontSize: 13, color: colors.textMuted, fontWeight: "400" },
  err: { color: colors.danger, padding: 12, textAlign: "center" },
});
