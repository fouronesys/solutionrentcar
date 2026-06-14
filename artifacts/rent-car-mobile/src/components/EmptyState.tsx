import React from "react";
import { StyleSheet, Text, View } from "react-native";
import { colors } from "@/theme/colors";

const ICONS: Record<string, string> = {
  cars: "🚗",
  bookings: "📋",
  notifications: "🔔",
  search: "🔍",
  default: "📭",
};

export function EmptyState({
  title,
  subtitle,
  icon,
}: {
  title: string;
  subtitle?: string;
  icon?: string;
}) {
  const emoji = icon ? (ICONS[icon] ?? icon) : ICONS.default;
  return (
    <View style={styles.box}>
      <View style={styles.iconWrap}>
        <Text style={styles.emoji}>{emoji}</Text>
      </View>
      <Text style={styles.title}>{title}</Text>
      {subtitle ? <Text style={styles.sub}>{subtitle}</Text> : null}
    </View>
  );
}

const styles = StyleSheet.create({
  box: { paddingVertical: 56, paddingHorizontal: 32, alignItems: "center" },
  iconWrap: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: "#F1F5F9",
    alignItems: "center",
    justifyContent: "center",
    marginBottom: 16,
  },
  emoji: { fontSize: 36 },
  title: {
    color: colors.text,
    fontSize: 17,
    fontWeight: "700",
    marginBottom: 6,
    textAlign: "center",
  },
  sub: { color: colors.textMuted, fontSize: 14, textAlign: "center", lineHeight: 20 },
});
