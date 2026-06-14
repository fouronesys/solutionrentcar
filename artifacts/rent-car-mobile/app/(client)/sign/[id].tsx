import React, { useRef, useState } from "react";
import { Alert, Pressable, StyleSheet, Text, View } from "react-native";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { StatusBar } from "expo-status-bar";
import SignatureScreen, { SignatureViewRef } from "react-native-signature-canvas";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { Button } from "@/components/Button";
import { api, ApiError } from "@/api/client";
import { colors, font, gradients, radius, spacing, type } from "@/theme/colors";
import { t } from "@/i18n";

export default function SignScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const sigRef = useRef<SignatureViewRef>(null);
  const [saving, setSaving] = useState(false);

  const onSave = async (sig: string) => {
    setSaving(true);
    try {
      await api.post(`/bookings/${id}/sign`, { signature: sig });
      Alert.alert(t("booking.signed"), undefined, [
        { text: t("common.ok"), onPress: () => router.back() },
      ]);
    } catch (e) {
      Alert.alert(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setSaving(false);
    }
  };

  return (
    <View style={styles.screen}>
      <Stack.Screen options={{ headerShown: false }} />
      <StatusBar style="light" />

      {/* Hero header */}
      <LinearGradient
        colors={gradients.hero}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
        style={[styles.hero, { paddingTop: insets.top + 10 }]}
      >
        <View style={styles.heroTopRow}>
          <Pressable onPress={() => router.back()} style={styles.backBtn} hitSlop={8}>
            <Ionicons name="arrow-back" size={22} color="#fff" />
          </Pressable>
          <View style={styles.heroBrandRow}>
            <View style={styles.heroLogo}>
              <Ionicons name="create-outline" size={16} color={colors.dark} />
            </View>
            <Text style={styles.heroBrandLabel}>SOLUTION RENT CAR</Text>
          </View>
        </View>
        <Text style={styles.heroTitle}>{t("booking.signNow")}</Text>
      </LinearGradient>

      {/* Instructions */}
      <View style={styles.instructionCard}>
        <Ionicons name="information-circle-outline" size={16} color={colors.primaryDark} />
        <Text style={styles.instructionText}>{t("booking.signHint")}</Text>
      </View>

      {/* Signature pad */}
      <View style={styles.padWrapper}>
        <SignatureScreen
          ref={sigRef}
          onOK={onSave}
          onEmpty={() => Alert.alert(t("booking.signEmpty"))}
          descriptionText=""
          clearText={t("booking.signClear")}
          confirmText={t("booking.signConfirm")}
          webStyle={sigWebStyle}
        />
      </View>

      {/* Actions */}
      <View style={[styles.actions, { paddingBottom: insets.bottom + spacing.lg }]}>
        <Button
          title={t("booking.signClear")}
          variant="secondary"
          icon="trash-outline"
          onPress={() => sigRef.current?.clearSignature()}
          style={{ flex: 1 }}
        />
        <Button
          title={t("booking.signConfirm")}
          icon="checkmark-circle-outline"
          onPress={() => sigRef.current?.readSignature()}
          loading={saving}
          style={{ flex: 2 }}
        />
      </View>
    </View>
  );
}

const sigWebStyle = `
  .m-signature-pad {
    border: none;
    box-shadow: none;
    background: #FAFAFA;
  }
  .m-signature-pad--body {
    border: 2px dashed #E6E6E3;
    border-radius: 12px;
    margin: 12px;
  }
  .m-signature-pad--footer { display: none; }
  body { background: #FAFAFA; }
`;

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },

  hero: {
    paddingBottom: 22,
    paddingHorizontal: spacing.xl,
    borderBottomLeftRadius: radius.xxl,
    borderBottomRightRadius: radius.xxl,
  },
  heroTopRow: { flexDirection: "row", alignItems: "center", gap: 12, marginBottom: 18 },
  backBtn: {
    width: 40, height: 40, borderRadius: 20,
    backgroundColor: "rgba(255,255,255,0.12)",
    alignItems: "center", justifyContent: "center",
  },
  heroBrandRow: { flexDirection: "row", alignItems: "center", gap: 8 },
  heroLogo: {
    width: 26, height: 26, borderRadius: radius.xs,
    backgroundColor: colors.primary,
    alignItems: "center", justifyContent: "center",
  },
  heroBrandLabel: { ...type.label, color: "rgba(255,255,255,0.65)" },
  heroTitle: { ...type.display, color: "#FFFFFF" },

  instructionCard: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    backgroundColor: colors.primaryXLight,
    borderBottomWidth: 1,
    borderBottomColor: colors.primaryLight,
    paddingHorizontal: spacing.lg,
    paddingVertical: 12,
  },
  instructionText: { ...type.captionMed, color: colors.primaryDark, flex: 1 },

  padWrapper: {
    flex: 1,
    backgroundColor: colors.card,
    margin: spacing.lg,
    borderRadius: radius.lg,
    overflow: "hidden",
    borderWidth: 1,
    borderColor: colors.border,
  },

  actions: {
    flexDirection: "row",
    gap: 10,
    padding: spacing.lg,
    paddingTop: 0,
    backgroundColor: colors.bg,
  },
});
