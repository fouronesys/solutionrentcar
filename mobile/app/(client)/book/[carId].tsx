import React, { useState } from "react";
import { Alert, KeyboardAvoidingView, Platform, ScrollView, StyleSheet, Text, View } from "react-native";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { api, ApiError } from "@/api/client";
import { colors } from "@/theme/colors";
import { t } from "@/i18n";
import { toDbDateTime } from "@/utils/format";

function defaultStart() {
  const d = new Date();
  d.setDate(d.getDate() + 1);
  d.setHours(10, 0, 0, 0);
  return toDbDateTime(d);
}
function defaultEnd() {
  const d = new Date();
  d.setDate(d.getDate() + 4);
  d.setHours(18, 0, 0, 0);
  return toDbDateTime(d);
}

export default function BookCar() {
  const { carId } = useLocalSearchParams<{ carId: string }>();
  const router = useRouter();
  const [start, setStart] = useState(defaultStart());
  const [end, setEnd] = useState(defaultEnd());
  const [placeStart, setPlaceStart] = useState("");
  const [placeEnd, setPlaceEnd] = useState("");
  const [comment, setComment] = useState("");
  const [loading, setLoading] = useState(false);

  const submit = async () => {
    setLoading(true);
    try {
      const r = await api.post<{ booking: { id: number } }>("/bookings", {
        car_id: Number(carId),
        start_at: start,
        end_at: end,
        place_start: placeStart,
        place_end: placeEnd,
        comment,
      });
      Alert.alert(t("booking.created"));
      router.replace({ pathname: "/(client)/booking/[id]", params: { id: String(r.booking.id) } });
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
          <Text style={styles.hint}>YYYY-MM-DD HH:MM:SS</Text>
          <Input label={t("booking.start")} value={start} onChangeText={setStart} autoCapitalize="none" />
          <Input label={t("booking.end")} value={end} onChangeText={setEnd} autoCapitalize="none" />
          <Input label={t("booking.placeStart")} value={placeStart} onChangeText={setPlaceStart} />
          <Input label={t("booking.placeEnd")} value={placeEnd} onChangeText={setPlaceEnd} />
          <Input label={t("booking.comment")} value={comment} onChangeText={setComment} multiline numberOfLines={3} />
          <View style={{ height: 12 }} />
          <Button title={t("booking.submit")} onPress={submit} loading={loading} />
        </ScrollView>
      </KeyboardAvoidingView>
    </View>
  );
}

const styles = StyleSheet.create({
  hint: { color: colors.textMuted, fontSize: 12, marginBottom: 8 },
});
