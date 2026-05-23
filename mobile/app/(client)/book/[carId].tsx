import React, { useEffect, useMemo, useState } from "react";
import {
  Alert,
  KeyboardAvoidingView,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from "react-native";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import DateTimePicker, { DateTimePickerEvent } from "@react-native-community/datetimepicker";
import { Button } from "@/components/Button";
import { Card } from "@/components/Card";
import { Input } from "@/components/Input";
import { api, ApiError } from "@/api/client";
import type { Car, Insurance } from "@/api/types";
import { colors } from "@/theme/colors";
import { i18n, t } from "@/i18n";
import { money, toDbDateTime } from "@/utils/format";

type Location = { id: number; name: string };

function parseDbDate(s: string | undefined): Date | null {
  if (!s) return null;
  const d = new Date(s.replace(" ", "T"));
  return isNaN(d.getTime()) ? null : d;
}
function defaultStart() {
  const d = new Date();
  d.setDate(d.getDate() + 1);
  d.setHours(10, 0, 0, 0);
  return d;
}
function defaultEnd() {
  const d = new Date();
  d.setDate(d.getDate() + 4);
  d.setHours(18, 0, 0, 0);
  return d;
}
function fmt(d: Date) {
  return d.toLocaleString(i18n.locale === "en" ? "en-US" : "es-ES", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

export default function BookCar() {
  const params = useLocalSearchParams<{ carId: string; start?: string; end?: string }>();
  const router = useRouter();

  const [car, setCar] = useState<Car | null>(null);
  const [start, setStart] = useState<Date>(parseDbDate(params.start) ?? defaultStart());
  const [end, setEnd] = useState<Date>(parseDbDate(params.end) ?? defaultEnd());
  const [showStart, setShowStart] = useState(false);
  const [showStartTime, setShowStartTime] = useState(false);
  const [pendingStart, setPendingStart] = useState<Date | null>(null);
  const [showEnd, setShowEnd] = useState(false);
  const [showEndTime, setShowEndTime] = useState(false);
  const [pendingEnd, setPendingEnd] = useState<Date | null>(null);

  const [placeStart, setPlaceStart] = useState("");
  const [placeEnd, setPlaceEnd] = useState("");
  const [customStart, setCustomStart] = useState(false);
  const [customEnd, setCustomEnd] = useState(false);
  const [locations, setLocations] = useState<Location[]>([]);
  const [comment, setComment] = useState("");
  const [extras, setExtras] = useState<Insurance[]>([]);
  const [selectedExtras, setSelectedExtras] = useState<Set<number>>(new Set());
  const [paymentMethod, setPaymentMethod] = useState<"cash">("cash");
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    (async () => {
      try {
        const r = await api.get<{ car: Car }>(`/cars/${params.carId}`);
        if (r.car) setCar(r.car);
      } catch {
        /* ignore */
      }
      try {
        const ins = await api.get<{ insurances: Insurance[] }>("/catalog/insurances");
        if (Array.isArray(ins.insurances)) setExtras(ins.insurances);
      } catch {
        /* ignore */
      }
      // RD airports fallback (siempre disponibles aunque el backend no los traiga)
      const RD_AIRPORTS: Location[] = [
        { id: -1, name: "Aeropuerto Las Américas (SDQ) — Santo Domingo" },
        { id: -2, name: "Aeropuerto Punta Cana (PUJ)" },
        { id: -3, name: "Aeropuerto Cibao (STI) — Santiago" },
        { id: -4, name: "Aeropuerto Gregorio Luperón (POP) — Puerto Plata" },
        { id: -5, name: "Aeropuerto La Romana (LRM)" },
        { id: -6, name: "Aeropuerto Samaná El Catey (AZS)" },
        { id: -7, name: "Aeropuerto La Isabela / JBQ — Santo Domingo Norte" },
        { id: -8, name: "Aeropuerto María Montez (BRX) — Barahona" },
      ] as unknown as Location[];

      let finalAirports: Location[] = RD_AIRPORTS;
      try {
        const loc = await api.get<{ locations: Location[] }>("/catalog/locations");
        if (Array.isArray(loc.locations) && loc.locations.length) {
          const apiAirports = loc.locations.filter((l) =>
            /aeropuerto|airport/i.test(l.name ?? ""),
          );
          if (apiAirports.length) finalAirports = apiAirports;
        }
      } catch {
        /* mantener fallback */
      }
      setLocations(finalAirports);
      if (finalAirports.length) {
        setPlaceStart(finalAirports[0].name);
        setPlaceEnd(finalAirports[0].name);
      }
    })();
  }, [params.carId]);

  const days = useMemo(() => {
    return Math.max(1, Math.ceil((end.getTime() - start.getTime()) / 86400000));
  }, [start, end]);

  const pricePerDay = Number(car?.price ?? car?.price_day ?? 0);
  const extrasTotal = useMemo(() => {
    let sum = 0;
    extras.forEach((e) => {
      if (selectedExtras.has(e.id)) sum += Number(e.price ?? 0) * days;
    });
    return sum;
  }, [extras, selectedExtras, days]);

  const subtotal = pricePerDay * days;
  const total = subtotal + extrasTotal;

  const toggleExtra = (id: number) => {
    setSelectedExtras((prev) => {
      const next = new Set(prev);
      if (next.has(id)) next.delete(id);
      else next.add(id);
      return next;
    });
  };

  const submit = async () => {
    if (!placeStart.trim() || !placeEnd.trim()) {
      Alert.alert(t("booking.placeStart"));
      return;
    }
    setLoading(true);
    try {
      const extraIds = Array.from(selectedExtras);
      const r = await api.post<{ booking: { id: number } }>("/bookings", {
        car_id: Number(params.carId),
        start_at: toDbDateTime(start),
        end_at: toDbDateTime(end),
        place_start: placeStart,
        place_end: placeEnd,
        comment,
        insurance_ids: extraIds,
        extras: extraIds,
        price: pricePerDay,
        total,
        sure: extrasTotal,
        payment_method: paymentMethod,
      });
      router.replace({ pathname: "/(client)/sign/[id]", params: { id: String(r.booking.id) } });
    } catch (e) {
      Alert.alert(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={{ flex: 1, backgroundColor: colors.bg }}>
      <Stack.Screen options={{ headerShown: true, title: t("booking.title") }} />
      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={{ padding: 16 }} keyboardShouldPersistTaps="handled">
          {car ? (
            <Card>
              <Text style={styles.carTitle}>
                {car.brand ? `${car.brand} ` : ""}
                {car.name ?? car.model ?? ""}
              </Text>
              <Text style={styles.carMeta}>
                {car.year ? `${car.year} · ` : ""}
                {car.transmission ?? ""} {car.fuel ? `· ${car.fuel}` : ""}
              </Text>
              <Text style={styles.carPrice}>
                {money(pricePerDay)} <Text style={styles.per}>{t("cars.perDay")}</Text>
              </Text>
            </Card>
          ) : null}

          <Card>
            <Text style={styles.label}>{t("booking.start")}</Text>
            <Pressable style={styles.dateBtn} onPress={() => setShowStart(true)}>
              <Text style={styles.dateText}>{fmt(start)}</Text>
            </Pressable>
            {showStart && (
              <DateTimePicker
                value={start}
                mode={Platform.OS === "ios" ? "datetime" : "date"}
                display={Platform.OS === "ios" ? "spinner" : "calendar"}
                minimumDate={new Date()}
                onChange={(event: DateTimePickerEvent, d?: Date) => {
                  setShowStart(false);
                  if (event.type === "set" && d) {
                    if (Platform.OS === "ios") {
                      setStart(d);
                      if (d >= end) setEnd(new Date(d.getTime() + 3 * 86400000));
                    } else {
                      setPendingStart(d);
                      setShowStartTime(true);
                    }
                  }
                }}
              />
            )}
            {showStartTime && Platform.OS === "android" && (
              <DateTimePicker
                value={pendingStart ?? start}
                mode="time"
                display="clock"
                onChange={(event: DateTimePickerEvent, d?: Date) => {
                  setShowStartTime(false);
                  setPendingStart(null);
                  if (event.type === "set" && d && pendingStart) {
                    const combined = new Date(pendingStart);
                    combined.setHours(d.getHours(), d.getMinutes(), 0, 0);
                    setStart(combined);
                    if (combined >= end) setEnd(new Date(combined.getTime() + 3 * 86400000));
                  }
                }}
              />
            )}

            <Text style={styles.label}>{t("booking.end")}</Text>
            <Pressable style={styles.dateBtn} onPress={() => setShowEnd(true)}>
              <Text style={styles.dateText}>{fmt(end)}</Text>
            </Pressable>
            {showEnd && (
              <DateTimePicker
                value={end}
                mode={Platform.OS === "ios" ? "datetime" : "date"}
                display={Platform.OS === "ios" ? "spinner" : "calendar"}
                minimumDate={new Date(start.getTime() + 3600000)}
                onChange={(event: DateTimePickerEvent, d?: Date) => {
                  setShowEnd(false);
                  if (event.type === "set" && d) {
                    if (Platform.OS === "ios") {
                      setEnd(d);
                    } else {
                      setPendingEnd(d);
                      setShowEndTime(true);
                    }
                  }
                }}
              />
            )}
            {showEndTime && Platform.OS === "android" && (
              <DateTimePicker
                value={pendingEnd ?? end}
                mode="time"
                display="clock"
                onChange={(event: DateTimePickerEvent, d?: Date) => {
                  setShowEndTime(false);
                  setPendingEnd(null);
                  if (event.type === "set" && d && pendingEnd) {
                    const combined = new Date(pendingEnd);
                    combined.setHours(d.getHours(), d.getMinutes(), 0, 0);
                    setEnd(combined);
                  }
                }}
              />
            )}
          </Card>

          <Card>
            <Text style={styles.label}>{t("booking.placeStart")}</Text>
            <View style={styles.chipRow}>
              {locations.map((l) => (
                <Pressable
                  key={`s-${l.id}`}
                  onPress={() => {
                    setCustomStart(false);
                    setPlaceStart(l.name);
                  }}
                  style={[styles.chip, !customStart && placeStart === l.name && styles.chipActive]}
                >
                  <Text
                    style={[styles.chipText, !customStart && placeStart === l.name && styles.chipTextActive]}
                  >
                    {l.name}
                  </Text>
                </Pressable>
              ))}
              <Pressable
                onPress={() => {
                  setCustomStart(true);
                  setPlaceStart("");
                }}
                style={[styles.chip, customStart && styles.chipActive]}
              >
                <Text style={[styles.chipText, customStart && styles.chipTextActive]}>
                  {t("booking.placeOther")}
                </Text>
              </Pressable>
            </View>
            {customStart ? (
              <Input
                value={placeStart}
                onChangeText={setPlaceStart}
                maxLength={250}
                multiline
                placeholder={t("booking.placeCustomPlaceholder")}
              />
            ) : null}

            <Text style={styles.label}>{t("booking.placeEnd")}</Text>
            <View style={styles.chipRow}>
              {locations.map((l) => (
                <Pressable
                  key={`e-${l.id}`}
                  onPress={() => {
                    setCustomEnd(false);
                    setPlaceEnd(l.name);
                  }}
                  style={[styles.chip, !customEnd && placeEnd === l.name && styles.chipActive]}
                >
                  <Text
                    style={[styles.chipText, !customEnd && placeEnd === l.name && styles.chipTextActive]}
                  >
                    {l.name}
                  </Text>
                </Pressable>
              ))}
              <Pressable
                onPress={() => {
                  setCustomEnd(true);
                  setPlaceEnd("");
                }}
                style={[styles.chip, customEnd && styles.chipActive]}
              >
                <Text style={[styles.chipText, customEnd && styles.chipTextActive]}>
                  {t("booking.placeOther")}
                </Text>
              </Pressable>
            </View>
            {customEnd ? (
              <Input
                value={placeEnd}
                onChangeText={setPlaceEnd}
                maxLength={250}
                multiline
                placeholder={t("booking.placeCustomPlaceholder")}
              />
            ) : null}

            <Input label={t("booking.comment")} value={comment} onChangeText={setComment} multiline numberOfLines={3} />
          </Card>

          {extras.length > 0 ? (
            <Card>
              <Text style={styles.label}>{t("booking.extras")}</Text>
              {extras.map((ex) => {
                const active = selectedExtras.has(ex.id);
                return (
                  <Pressable
                    key={ex.id}
                    onPress={() => toggleExtra(ex.id)}
                    style={[styles.extraRow, active && styles.extraActive]}
                  >
                    <Text style={[styles.extraName, active && { color: colors.primaryDark }]}>
                      {active ? "☑  " : "☐  "}
                      {ex.name}
                    </Text>
                    {ex.price ? <Text style={styles.extraPrice}>{money(ex.price)}/{t("booking.days").toLowerCase()}</Text> : null}
                  </Pressable>
                );
              })}
            </Card>
          ) : null}

          <Card>
            <Text style={styles.label}>{t("booking.paymentMethod")}</Text>
            <Pressable
              onPress={() => setPaymentMethod("cash")}
              style={[styles.extraRow, paymentMethod === "cash" && styles.extraActive]}
            >
              <Text style={[styles.extraName, paymentMethod === "cash" && { color: colors.primaryDark }]}>
                {paymentMethod === "cash" ? "●  " : "○  "}
                {t("booking.cashOnPickup")}
              </Text>
            </Pressable>
          </Card>

          <Card>
            <Text style={styles.label}>{t("booking.summary")}</Text>
            <View style={styles.kv}>
              <Text style={styles.k}>{t("booking.days")}</Text>
              <Text style={styles.v}>{days}</Text>
            </View>
            <View style={styles.kv}>
              <Text style={styles.k}>{t("booking.subtotal")}</Text>
              <Text style={styles.v}>{money(subtotal)}</Text>
            </View>
            {extrasTotal > 0 ? (
              <View style={styles.kv}>
                <Text style={styles.k}>{t("booking.extrasTotal")}</Text>
                <Text style={styles.v}>{money(extrasTotal)}</Text>
              </View>
            ) : null}
            <View style={[styles.kv, { marginTop: 6, paddingTop: 6, borderTopWidth: 1, borderTopColor: colors.border }]}>
              <Text style={[styles.k, { fontWeight: "700", color: colors.text }]}>{t("booking.total")}</Text>
              <Text style={[styles.v, { fontWeight: "700", fontSize: 18, color: colors.primaryDark }]}>{money(total)}</Text>
            </View>
          </Card>

          <View style={{ height: 8 }} />
          <Button title={t("booking.submit")} onPress={submit} loading={loading} />
          <Text style={styles.hint}>{t("booking.signRequired")}</Text>
        </ScrollView>
      </KeyboardAvoidingView>
    </View>
  );
}

const styles = StyleSheet.create({
  carTitle: { fontSize: 17, fontWeight: "700", color: colors.text },
  carMeta: { color: colors.textMuted, fontSize: 13, marginTop: 2 },
  carPrice: { fontSize: 18, color: colors.primaryDark, fontWeight: "700", marginTop: 8 },
  per: { fontSize: 12, color: colors.textMuted, fontWeight: "400" },
  label: { fontSize: 13, color: colors.textMuted, marginBottom: 6, marginTop: 6, fontWeight: "500" },
  dateBtn: {
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 8,
    padding: 12,
    backgroundColor: "#fff",
    marginBottom: 4,
  },
  dateText: { color: colors.text, fontSize: 14, fontWeight: "600" },
  chipRow: { flexDirection: "row", flexWrap: "wrap", marginBottom: 4 },
  chip: {
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 16,
    borderWidth: 1,
    borderColor: colors.border,
    backgroundColor: "#fff",
    marginRight: 6,
    marginBottom: 6,
  },
  chipActive: { borderColor: colors.primary, backgroundColor: colors.primary + "15" },
  chipText: { color: colors.text, fontSize: 13 },
  chipTextActive: { color: colors.primaryDark, fontWeight: "700" },
  extraRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingVertical: 12,
    paddingHorizontal: 12,
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 8,
    backgroundColor: "#fff",
    marginBottom: 6,
  },
  extraActive: { borderColor: colors.primary, backgroundColor: colors.primary + "11" },
  extraName: { color: colors.text, fontSize: 14, flex: 1 },
  extraPrice: { color: colors.textMuted, fontSize: 13 },
  kv: { flexDirection: "row", justifyContent: "space-between", paddingVertical: 4 },
  k: { color: colors.textMuted, fontSize: 13 },
  v: { color: colors.text, fontSize: 14 },
  hint: { color: colors.textMuted, fontSize: 12, marginTop: 10, textAlign: "center", fontStyle: "italic" },
});
