import React, { useState } from "react";
import { Alert, KeyboardAvoidingView, Platform, Pressable, ScrollView, StyleSheet, Text, View } from "react-native";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { api, ApiError } from "@/api/client";
import { colors, radius, shadow, spacing } from "@/theme/colors";
import { t } from "@/i18n";
import { money } from "@/utils/format";

const METHODS = [
  { key: "cash", icon: "💵", label: "Efectivo" },
  { key: "card", icon: "💳", label: "Tarjeta" },
  { key: "transfer", icon: "🏦", label: "Transferencia" },
];

export default function PayScreen() {
  const { bookingId } = useLocalSearchParams<{ bookingId: string }>();
  const router = useRouter();
  const [amount, setAmount] = useState("");
  const [method, setMethod] = useState("cash");
  const [note, setNote] = useState("");
  const [loading, setLoading] = useState(false);

  const submit = async () => {
    const val = parseFloat(amount.replace(",", "."));
    if (!val || val <= 0) { Alert.alert(t("payment.invalidAmount")); return; }
    setLoading(true);
    try {
      await api.post("/payments", { booking_id: Number(bookingId), val, method, note: note.trim() || undefined });
      Alert.alert(t("payment.saved"), undefined, [{ text: t("common.ok"), onPress: () => router.back() }]);
    } catch (e) {
      Alert.alert(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <Stack.Screen options={{ headerShown: false }} />
      {/* Header */}
      <View style={styles.header}>
        <Pressable onPress={() => router.back()} style={styles.backBtn}>
          <Text style={styles.backText}>←</Text>
        </Pressable>
        <Text style={styles.headerTitle}>💳  {t("payment.register")}</Text>
      </View>

      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={styles.body} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>
          {/* Amount */}
          <View style={styles.section}>
            <Text style={styles.sectionLabel}>{t("payment.amount")}</Text>
            <View style={styles.amountRow}>
              <Text style={styles.currency}>RD$</Text>
              <Input
                value={amount}
                onChangeText={setAmount}
                keyboardType="decimal-pad"
                placeholder="0.00"
                containerStyle={{ flex: 1, marginBottom: 0 }}
              />
            </View>
            {amount ? <Text style={styles.amountPreview}>{money(parseFloat(amount.replace(",", ".")) || 0)}</Text> : null}
          </View>

          {/* Method */}
          <View style={styles.section}>
            <Text style={styles.sectionLabel}>{t("payment.method")}</Text>
            <View style={styles.methodGrid}>
              {METHODS.map((m) => (
                <Pressable
                  key={m.key}
                  onPress={() => setMethod(m.key)}
                  style={[styles.methodBtn, method === m.key && styles.methodBtnActive]}
                >
                  <Text style={styles.methodIcon}>{m.icon}</Text>
                  <Text style={[styles.methodLabel, method === m.key && styles.methodLabelActive]}>
                    {m.label}
                  </Text>
                </Pressable>
              ))}
            </View>
          </View>

          {/* Note */}
          <View style={styles.section}>
            <Input
              label={`${t("payment.note")} (${t("common.ok").toLowerCase()})`}
              value={note}
              onChangeText={setNote}
              placeholder="Observaciones…"
            />
          </View>

          {/* CTA */}
          <View style={styles.cta}>
            <Button title={t("payment.save")} onPress={submit} loading={loading} size="lg" />
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  header: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.md,
    backgroundColor: colors.dark,
    gap: 12,
  },
  backBtn: {
    width: 36, height: 36, borderRadius: 18,
    backgroundColor: "rgba(255,255,255,0.1)",
    alignItems: "center", justifyContent: "center",
  },
  backText: { fontSize: 20, fontWeight: "700", color: "#fff", marginTop: -2 },
  headerTitle: { fontSize: 18, fontWeight: "800", color: "#fff" },

  body: { paddingBottom: 40 },
  section: {
    backgroundColor: colors.card, marginTop: 8,
    padding: spacing.lg,
    borderTopWidth: 1, borderTopColor: colors.border,
    borderBottomWidth: 1, borderBottomColor: colors.border,
  },
  sectionLabel: {
    fontSize: 11, fontWeight: "700", color: colors.textMuted,
    textTransform: "uppercase", letterSpacing: 0.8, marginBottom: 14,
  },
  amountRow: { flexDirection: "row", alignItems: "center", gap: 8 },
  currency: { fontSize: 18, fontWeight: "700", color: colors.textMuted, paddingTop: 4 },
  amountPreview: { fontSize: 28, fontWeight: "800", color: colors.primaryDark, marginTop: 8, textAlign: "right" },

  methodGrid: { flexDirection: "row", gap: 10 },
  methodBtn: {
    flex: 1, alignItems: "center", paddingVertical: 14,
    borderRadius: radius.md,
    borderWidth: 1.5, borderColor: colors.border,
    backgroundColor: colors.borderLight,
  },
  methodBtnActive: { borderColor: colors.primary, backgroundColor: colors.primaryXLight },
  methodIcon: { fontSize: 24, marginBottom: 6 },
  methodLabel: { fontSize: 12, fontWeight: "600", color: colors.textSecondary },
  methodLabelActive: { color: colors.primaryDark, fontWeight: "700" },

  cta: { padding: spacing.lg },
});
