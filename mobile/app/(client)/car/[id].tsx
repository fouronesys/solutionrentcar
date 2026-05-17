import React, { useEffect, useState } from "react";
import { Image, ScrollView, StyleSheet, Text, View } from "react-native";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import { Button } from "@/components/Button";
import { Loading } from "@/components/Loading";
import { Badge } from "@/components/Badge";
import { api, ApiError } from "@/api/client";
import type { Car } from "@/api/types";
import { carStatus, colors } from "@/theme/colors";
import { i18n, t } from "@/i18n";
import { money } from "@/utils/format";

export default function CarDetail() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const [car, setCar] = useState<Car | null>(null);
  const [err, setErr] = useState<string | null>(null);

  useEffect(() => {
    (async () => {
      try {
        const r = await api.get<{ car: Car }>(`/cars/${id}`);
        setCar(r.car);
      } catch (e) {
        setErr(e instanceof ApiError ? e.message : t("common.error"));
      }
    })();
  }, [id]);

  if (err)
    return (
      <View style={{ flex: 1, padding: 24, justifyContent: "center" }}>
        <Text style={{ color: colors.danger, textAlign: "center" }}>{err}</Text>
      </View>
    );
  if (!car) return <Loading />;

  const status = carStatus[Number(car.status ?? 0)];
  const gallery = car.images && car.images.length ? car.images : car.image ? [car.image] : [];

  return (
    <View style={{ flex: 1, backgroundColor: colors.bg }}>
      <Stack.Screen options={{ headerShown: true, title: car.name ?? `${car.brand ?? ""} ${car.model ?? ""}` }} />
      <ScrollView contentContainerStyle={{ padding: 16 }}>
        {gallery.length > 0 ? (
          <ScrollView horizontal pagingEnabled showsHorizontalScrollIndicator={false} style={styles.gallery}>
            {gallery.map((src, i) => (
              <Image key={i} source={{ uri: src }} style={styles.galleryImg} />
            ))}
          </ScrollView>
        ) : null}

        <View style={{ flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
          <Text style={styles.title}>
            {car.brand ? `${car.brand} ` : ""}
            {car.name ?? car.model ?? ""}
          </Text>
          {status ? <Badge label={status[i18n.locale === "en" ? "en" : "es"]} color={status.color} /> : null}
        </View>
        <Text style={styles.meta}>
          {car.year ? `${car.year} · ` : ""}
          {car.transmission ?? ""} {car.fuel ? `· ${car.fuel}` : ""} {car.color ? `· ${car.color}` : ""}
        </Text>
        <Text style={styles.price}>
          {money(car.price_day ?? car.price)} <Text style={styles.per}>{t("cars.perDay")}</Text>
        </Text>

        {car.description ? <Text style={styles.desc}>{car.description}</Text> : null}

        <View style={{ height: 24 }} />
        <Button
          title={t("cars.book")}
          disabled={Number(car.status ?? 0) !== 0}
          onPress={() => router.push({ pathname: "/(client)/book/[carId]", params: { carId: String(car.id) } })}
        />
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  gallery: { marginBottom: 12 },
  galleryImg: { width: 320, height: 200, borderRadius: 12, marginRight: 10, backgroundColor: "#eee" },
  title: { fontSize: 22, fontWeight: "700", color: colors.text, flexShrink: 1, marginRight: 8 },
  meta: { color: colors.textMuted, fontSize: 14, marginTop: 4 },
  price: { fontSize: 24, color: colors.primary, fontWeight: "800", marginTop: 12 },
  per: { fontSize: 14, color: colors.textMuted, fontWeight: "400" },
  desc: { color: colors.text, marginTop: 16, fontSize: 14, lineHeight: 20 },
});
