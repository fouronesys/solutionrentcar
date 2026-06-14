import React, { useRef, useState } from "react";
import { Alert, Pressable, StyleSheet, Text, View } from "react-native";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import SignatureScreen, { SignatureViewRef } from "react-native-signature-canvas";
import { SafeAreaView } from "react-native-safe-area-context";
import { Button } from "@/components/Button";
import { api, ApiError } from "@/api/client";
import { colors, radius, spacing } from "@/theme/colors";
import { t } from "@/i18n";

export default function SignScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
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
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <Stack.Screen options={{ headerShown: false }} />
      {/* Header */}
      <View style={styles.header}>
        <Pressable onPress={() => router.back()} style={styles.backBtn}>
          <Text style={styles.backText}>←</Text>
        </Pressable>
        <Text style={styles.headerTitle}>✍️  {t("booking.signNow")}</Text>
      </View>

      {/* Instructions */}
      <View style={styles.instructionCard}>
        <Text style={styles.instructionText}>
          {t("booking.signHint")}
        </Text>
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
      <View style={styles.actions}>
        <Button
          title={`🗑  ${t("booking.signClear")}`}
          variant="secondary"
          onPress={() => sigRef.current?.clearSignature()}
          style={{ flex: 1 }}
        />
        <Button
          title={saving ? "…" : `✓  ${t("booking.signConfirm")}`}
          onPress={() => sigRef.current?.readSignature()}
          loading={saving}
          style={{ flex: 2 }}
        />
      </View>
    </SafeAreaView>
  );
}

const sigWebStyle = `
  .m-signature-pad {
    border: none;
    box-shadow: none;
    background: #FAFAFA;
  }
  .m-signature-pad--body {
    border: 2px dashed #E2E8F0;
    border-radius: 12px;
    margin: 12px;
  }
  .m-signature-pad--footer { display: none; }
  body { background: #FAFAFA; }
`;

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

  instructionCard: {
    backgroundColor: colors.primaryXLight,
    borderBottomWidth: 1,
    borderBottomColor: colors.primaryLight,
    paddingHorizontal: spacing.lg,
    paddingVertical: 12,
  },
  instructionText: { fontSize: 13, color: colors.primaryDark, fontWeight: "500", textAlign: "center" },

  padWrapper: {
    flex: 1,
    backgroundColor: colors.bg,
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
