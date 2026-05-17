import React, { useEffect, useState } from "react";
import { Alert, KeyboardAvoidingView, Platform, ScrollView, StyleSheet, Text, View } from "react-native";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { api, ApiError } from "@/api/client";
import { colors } from "@/theme/colors";
import { t } from "@/i18n";

type PaymentType = { id: number; name: string };

export default function PaymentScreen() {
  const { bookingId } = useLocalSearchParams<{ bookingId: string }>();
  const router = useRouter();
  const [val, setVal] = useState("");
  const [typeId, setTypeId] = useState<number>(1);
  const [types, setTypes] = useState<PaymentType[]>([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    (async () => {
      try {
        const r = await api.get<{ items: PaymentType[] }>("/catalog/payment_types");
        if (r.items?.length) {
          setTypes(r.items);
          setTypeId(r.items[0].id);
        }
      } catch {
        /* keep default */
      }
    })();
  }, []);

  const submit = async () => {
    const amount = Number(val);
    if (!amount || amount <= 0) {
      Alert.alert(t("payment.amount"));
      return;
    }
    setLoading(true);
    try {
      await api.post("/payments", {
        booking_id: Number(bookingId),
        val: amount,
        payment_type_id: typeId,
      });
      Alert.alert(t("payment.saved"));
      router.back();
    } catch (e) {
      Alert.alert(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={{ flex: 1, backgroundColor: colors.bg }}>
      <Stack.Screen options={{ headerShown: true, title: t("payment.title") }} />
      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={{ padding: 16 }} keyboardShouldPersistTaps="handled">
          <Input
            label={t("payment.amount")}
            keyboardType="decimal-pad"
            value={val}
            onChangeText={setVal}
            placeholder="0.00"
          />
          {types.length > 0 && (
            <View style={{ marginBottom: 12 }}>
              <Text style={styles.label}>{t("payment.method")}</Text>
              <View style={styles.chips}>
                {types.map((pt) => (
                  <Text
                    key={pt.id}
                    onPress={() => setTypeId(pt.id)}
                    style={[styles.chip, typeId === pt.id && styles.chipActive]}
                  >
                    {pt.name}
                  </Text>
                ))}
              </View>
            </View>
          )}
          <Button title={t("payment.submit")} onPress={submit} loading={loading} />
        </ScrollView>
      </KeyboardAvoidingView>
    </View>
  );
}

const styles = StyleSheet.create({
  label: { fontSize: 13, color: colors.textMuted, marginBottom: 6, fontWeight: "500" },
  chips: { flexDirection: "row", flexWrap: "wrap" },
  chip: {
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 16,
    backgroundColor: "#fff",
    borderWidth: 1,
    borderColor: colors.border,
    color: colors.text,
    marginRight: 8,
    marginBottom: 8,
    overflow: "hidden",
  },
  chipActive: { backgroundColor: colors.primary, borderColor: colors.primary, color: "#fff" },
});
