import React from "react";
import { StyleSheet, Text, View } from "react-native";
import { colors } from "@/theme/colors";

export function EmptyState({ title, subtitle }: { title: string; subtitle?: string }) {
  return (
    <View style={styles.box}>
      <Text style={styles.title}>{title}</Text>
      {subtitle ? <Text style={styles.sub}>{subtitle}</Text> : null}
    </View>
  );
}

const styles = StyleSheet.create({
  box: { padding: 32, alignItems: "center", justifyContent: "center" },
  title: { color: colors.text, fontSize: 16, fontWeight: "600", marginBottom: 6, textAlign: "center" },
  sub: { color: colors.textMuted, fontSize: 13, textAlign: "center" },
});
