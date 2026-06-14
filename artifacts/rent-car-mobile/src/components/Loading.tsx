import React from "react";
import { ActivityIndicator, StyleSheet, Text, View } from "react-native";
import { colors } from "@/theme/colors";
import { t } from "@/i18n";

export function Loading({ label, overlay }: { label?: string; overlay?: boolean }) {
  if (overlay) {
    return (
      <View style={styles.overlay}>
        <View style={styles.pill}>
          <ActivityIndicator color={colors.primary} size="small" />
          <Text style={styles.pillLabel}>{label ?? t("common.loading")}</Text>
        </View>
      </View>
    );
  }
  return (
    <View style={styles.container}>
      <View style={styles.spinner}>
        <ActivityIndicator color={colors.primaryDark} size="large" />
      </View>
      {label ? <Text style={styles.label}>{label}</Text> : null}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, alignItems: "center", justifyContent: "center", padding: 24 },
  spinner: { marginBottom: 12 },
  label: { color: colors.textMuted, fontSize: 14, fontWeight: "500" },
  overlay: {
    ...StyleSheet.absoluteFillObject,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "rgba(15,23,42,0.4)",
    zIndex: 99,
  },
  pill: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#fff",
    paddingVertical: 14,
    paddingHorizontal: 22,
    borderRadius: 40,
    gap: 10,
  },
  pillLabel: { color: colors.text, fontSize: 14, fontWeight: "600" },
});
