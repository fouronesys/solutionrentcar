import React from "react";
import { ActivityIndicator, View, Text } from "react-native";
import { colors } from "@/theme/colors";
import { t } from "@/i18n";

export function Loading({ label }: { label?: string }) {
  return (
    <View style={{ flex: 1, alignItems: "center", justifyContent: "center", padding: 24 }}>
      <ActivityIndicator color={colors.primaryDark} size="large" />
      <Text style={{ color: colors.textMuted, marginTop: 12 }}>{label ?? t("common.loading")}</Text>
    </View>
  );
}
