import React, { useCallback, useEffect, useMemo, useState } from "react";
import {
  FlatList,
  Image,
  Platform,
  Pressable,
  RefreshControl,
  StyleSheet,
  Text,
  View,
} from "react-native";
import { useRouter } from "expo-router";
import DateTimePicker, { DateTimePickerEvent } from "@react-native-community/datetimepicker";
import { Card } from "@/components/Card";
import { Loading } from "@/components/Loading";
import { EmptyState } from "@/components/EmptyState";
import { Badge } from "@/components/Badge";
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { api, ApiError } from "@/api/client";
import { carStatus, colors } from "@/theme/colors";
import type { Car } from "@/api/types";
import { i18n, t } from "@/i18n";
import { money, toDbDateTime } from "@/utils/format";

function defaultStartDate() {
  const d = new Date();
  d.setDate(d.getDate() + 1);
  d.setHours(10, 0, 0, 0);
  return d;
}
function defaultEndDate() {
  const d = new Date();
  d.setDate(d.getDate() + 4);
  d.setHours(18, 0, 0, 0);
  return d;
}
function fmt(d: Date) {
  return d.toLocaleString(i18n.locale === "en" ? "en-US" : "es-ES", {
    day: "2-digit",
    month: "short",
    hour: "2-digit",
    minute: "2-digit",
  });
}

export default function CarsScreen() {
  const router = useRouter();
  const [cars, setCars] = useState<Car[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [err, setErr] = useState<string | null>(null);
  const [q, setQ] = useState("");

  const [filtered, setFiltered] = useState(false);
  const [start, setStart] = useState<Date>(defaultStartDate());
  const [end, setEnd] = useState<Date>(defaultEndDate());
  const [showStart, setShowStart] = useState(false);
  const [showEnd, setShowEnd] = useState(false);

  const load = useCallback(async () => {
    setErr(null);
    try {
      const params: Record<string, string | number | undefined> = {
        status: 0,
        q: q || undefined,
        limit: 50,
      };
      if (filtered) {
        params.available_from = toDbDateTime(start);
        params.available_to = toDbDateTime(end);
      }
      const r = await api.get<{ cars: Car[] }>("/cars", params);
      setCars(r.cars ?? []);
    } catch (e) {
      setErr(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [q, filtered, start, end]);

  useEffect(() => {
    load();
  }, [load]);

  const dateBadge = useMemo(() => (filtered ? `${fmt(start)} → ${fmt(end)}` : null), [filtered, start, end]);

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

        <Card>
          <Text style={styles.filterLabel}>{t("cars.filterDates")}</Text>
          <View style={styles.dateRow}>
            <Pressable style={styles.dateBtn} onPress={() => setShowStart(true)}>
              <Text style={styles.dateSmall}>{t("cars.pickup")}</Text>
              <Text style={styles.dateBig}>{fmt(start)}</Text>
            </Pressable>
            <Pressable style={styles.dateBtn} onPress={() => setShowEnd(true)}>
              <Text style={styles.dateSmall}>{t("cars.dropoff")}</Text>
              <Text style={styles.dateBig}>{fmt(end)}</Text>
            </Pressable>
          </View>
          {showStart && (
            <DateTimePicker
              value={start}
              mode="datetime"
              display={Platform.OS === "ios" ? "spinner" : "default"}
              minimumDate={new Date()}
              onChange={(e: DateTimePickerEvent, d?: Date) => {
                setShowStart(Platform.OS === "ios");
                if (d) {
                  setStart(d);
                  if (d >= end) {
                    const ne = new Date(d.getTime() + 3 * 86400000);
                    setEnd(ne);
                  }
                }
              }}
            />
          )}
          {showEnd && (
            <DateTimePicker
              value={end}
              mode="datetime"
              display={Platform.OS === "ios" ? "spinner" : "default"}
              minimumDate={new Date(start.getTime() + 3600000)}
              onChange={(e: DateTimePickerEvent, d?: Date) => {
                setShowEnd(Platform.OS === "ios");
                if (d) setEnd(d);
              }}
            />
          )}
          <View style={{ flexDirection: "row", marginTop: 8 }}>
            <Button
              title={t("cars.applyFilter")}
              onPress={() => {
                setFiltered(true);
                load();
              }}
              style={{ flex: 1, marginRight: 6 }}
            />
            {filtered ? (
              <Button
                title={t("cars.clearFilter")}
                variant="secondary"
                onPress={() => {
                  setFiltered(false);
                }}
                style={{ flex: 1, marginLeft: 6 }}
              />
            ) : null}
          </View>
          {dateBadge ? <Text style={styles.dateInfo}>{dateBadge}</Text> : null}
        </Card>
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
            <Card
              onPress={() => {
                if (filtered) {
                  router.push({
                    pathname: "/(client)/book/[carId]",
                    params: {
                      carId: String(item.id),
                      start: toDbDateTime(start),
                      end: toDbDateTime(end),
                    },
                  });
                } else {
                  router.push({ pathname: "/(client)/car/[id]", params: { id: String(item.id) } });
                }
              }}
            >
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
  filterLabel: { fontSize: 13, fontWeight: "700", color: colors.text, marginBottom: 8 },
  dateRow: { flexDirection: "row", justifyContent: "space-between" },
  dateBtn: {
    flex: 1,
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 8,
    padding: 10,
    marginHorizontal: 3,
    backgroundColor: "#fff",
  },
  dateSmall: { color: colors.textMuted, fontSize: 11, textTransform: "uppercase" },
  dateBig: { color: colors.text, fontSize: 14, fontWeight: "600", marginTop: 2 },
  dateInfo: { color: colors.textMuted, fontSize: 12, marginTop: 6, textAlign: "center" },
});
