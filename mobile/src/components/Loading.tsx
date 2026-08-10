import React from "react";
import { ActivityIndicator, StyleSheet, Text, View } from "react-native";
import { colors, radius, shadow, type } from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";
import { t } from "@/i18n";

export function Loading({ label, overlay }: { label?: string; overlay?: boolean }) {
  const styles = useThemedStyles(makeStyles);
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
      <ActivityIndicator color={colors.primaryDark} size="large" />
      {label ? <Text style={styles.label}>{label}</Text> : null}
    </View>
  );
}

const makeStyles = () => StyleSheet.create({
  container: { flex: 1, alignItems: "center", justifyContent: "center", padding: 24, backgroundColor: colors.bg },
  label: { ...type.callout, color: colors.textMuted, marginTop: 14 },
  overlay: {
    ...StyleSheet.absoluteFillObject,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "rgba(0,0,0,0.5)",
    zIndex: 99,
  },
  pill: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: colors.card,
    paddingVertical: 14,
    paddingHorizontal: 22,
    borderRadius: radius.full,
    gap: 10,
    ...shadow.lg,
  },
  pillLabel: { ...type.bodyMed, color: colors.text },
});
