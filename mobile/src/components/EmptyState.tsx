import React from "react";
import { StyleSheet, Text, View } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { colors, radius, type } from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";

const ICONS: Record<string, keyof typeof Ionicons.glyphMap> = {
  cars: "car-sport-outline",
  bookings: "calendar-outline",
  notifications: "notifications-outline",
  search: "search-outline",
  payments: "card-outline",
  profile: "person-outline",
  locations: "location-outline",
  default: "file-tray-outline",
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
  const styles = useThemedStyles(makeStyles);
  const name = (icon && ICONS[icon]) || ICONS.default;
  return (
    <View style={styles.box}>
      <View style={styles.iconWrap}>
        <Ionicons name={name} size={34} color={colors.cta} />
      </View>
      <Text style={styles.title}>{title}</Text>
      {subtitle ? <Text style={styles.sub}>{subtitle}</Text> : null}
    </View>
  );
}

const makeStyles = () => StyleSheet.create({
  box: { paddingVertical: 60, paddingHorizontal: 32, alignItems: "center" },
  iconWrap: {
    width: 84,
    height: 84,
    borderRadius: radius.full,
    backgroundColor: colors.ctaXLight,
    alignItems: "center",
    justifyContent: "center",
    marginBottom: 18,
    borderWidth: 1,
    borderColor: colors.ctaLight,
  },
  title: { ...type.h3, color: colors.text, marginBottom: 6, textAlign: "center" },
  sub: { ...type.callout, color: colors.textMuted, textAlign: "center" },
});
