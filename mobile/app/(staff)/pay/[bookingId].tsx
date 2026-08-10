import React, { useState } from "react";
import { Alert, KeyboardAvoidingView, Platform, Pressable, ScrollView, StyleSheet, Text, View } from "react-native";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { StatusBar } from "expo-status-bar";
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { api, ApiError } from "@/api/client";
import { colors, font, gradients, radius, shadow, spacing, type } from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";
import { t } from "@/i18n";
import { money } from "@/utils/format";

const METHODS: { key: string; icon: keyof typeof Ionicons.glyphMap; label: string }[] = [
  { key: "cash", icon: "cash-outline", label: "Efectivo" },
  { key: "card", icon: "card-outline", label: "Tarjeta" },
  { key: "transfer", icon: "business-outline", label: "Transferencia" },
];

export default function PayScreen() {
  const { bookingId } = useLocalSearchParams<{ bookingId: string }>();
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const [amount, setAmount] = useState("");
  const [method, setMethod] = useState("cash");
  const [note, setNote] = useState("");
  const [loading, setLoading] = useState(false);
  const styles = useThemedStyles(makeStyles);

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

  const preview = amount ? money(parseFloat(amount.replace(",", ".")) || 0) : null;

  return (
    <View style={styles.screen}>
      <Stack.Screen options={{ headerShown: false }} />
      <StatusBar style="light" />

      {/* Hero header */}
      <LinearGradient colors={gradients.hero} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={[styles.hero, { paddingTop: insets.top + 8 }]}>
        <View style={styles.heroTop}>
          <Pressable onPress={() => router.back()} style={styles.backBtn} hitSlop={8}>
            <Ionicons name="arrow-back" size={22} color="#fff" />
          </Pressable>
        </View>
        <View style={styles.heroTitleRow}>
          <View style={styles.heroLogo}>
            <Ionicons name="card" size={18} color={colors.dark} />
          </View>
          <Text style={styles.heroTitle}>{t("payment.register")}</Text>
        </View>
      </LinearGradient>

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
            {preview ? <Text style={styles.amountPreview}>{preview}</Text> : null}
          </View>

          {/* Method */}
          <View style={styles.section}>
            <Text style={styles.sectionLabel}>{t("payment.method")}</Text>
            <View style={styles.methodGrid}>
              {METHODS.map((m) => {
                const active = method === m.key;
                return (
                  <Pressable
                    key={m.key}
                    onPress={() => setMethod(m.key)}
                    style={[styles.methodBtn, active && styles.methodBtnActive]}
                  >
                    <View style={[styles.methodIconWrap, active && styles.methodIconWrapActive]}>
                      <Ionicons
                        name={m.icon}
                        size={22}
                        color={active ? colors.primaryDark : colors.textMuted}
                      />
                    </View>
                    <Text style={[styles.methodLabel, active && styles.methodLabelActive]}>
                      {m.label}
                    </Text>
                  </Pressable>
                );
              })}
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
            <Button title={t("payment.save")} icon="checkmark-circle-outline" onPress={submit} loading={loading} size="lg" />
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </View>
  );
}

const makeStyles = () => StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },

  hero: {
    paddingHorizontal: spacing.xl,
    paddingBottom: 22,
    borderBottomLeftRadius: radius.xxl,
    borderBottomRightRadius: radius.xxl,
  },
  heroTop: { flexDirection: "row", alignItems: "center", marginBottom: 14 },
  backBtn: {
    width: 42,
    height: 42,
    borderRadius: 21,
    backgroundColor: "rgba(255,255,255,0.12)",
    alignItems: "center",
    justifyContent: "center",
  },
  heroTitleRow: { flexDirection: "row", alignItems: "center", gap: 10 },
  heroLogo: {
    width: 30,
    height: 30,
    borderRadius: radius.sm,
    backgroundColor: colors.primary,
    alignItems: "center",
    justifyContent: "center",
  },
  heroTitle: { ...type.h1, color: "#FFFFFF" },

  body: { paddingBottom: 40 },
  section: {
    backgroundColor: colors.card,
    marginHorizontal: spacing.lg,
    marginTop: spacing.lg,
    borderRadius: radius.lg,
    padding: spacing.lg,
    ...shadow.sm,
  },
  sectionLabel: { ...type.label, color: colors.textMuted, marginBottom: 14 },
  amountRow: { flexDirection: "row", alignItems: "center", gap: 8 },
  currency: { ...type.h3, color: colors.textMuted, paddingTop: 4 },
  amountPreview: { ...type.display, color: colors.primaryDark, marginTop: 10, textAlign: "right" },

  methodGrid: { flexDirection: "row", gap: 10 },
  methodBtn: {
    flex: 1,
    alignItems: "center",
    paddingVertical: 16,
    borderRadius: radius.md,
    borderWidth: 1.5,
    borderColor: colors.border,
    backgroundColor: colors.card,
  },
  methodBtnActive: { borderColor: colors.primary, backgroundColor: colors.primaryXLight },
  methodIconWrap: {
    width: 40,
    height: 40,
    borderRadius: radius.full,
    backgroundColor: colors.borderLight,
    alignItems: "center",
    justifyContent: "center",
    marginBottom: 8,
  },
  methodIconWrapActive: { backgroundColor: colors.card },
  methodLabel: { ...type.captionMed, color: colors.textSecondary },
  methodLabelActive: { color: colors.primaryDark, fontFamily: font.semibold },

  cta: { paddingHorizontal: spacing.lg, paddingTop: spacing.lg },
});
