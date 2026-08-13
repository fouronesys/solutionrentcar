/**
 * EmptyState — illustrated placeholder with optional action CTA.
 */
import React from "react";
import { Pressable, StyleSheet, Text, View } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { colors, radius, shadow, type } from "@/theme/colors";
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
  action,
  onAction,
}: {
  title: string;
  subtitle?: string;
  icon?: string;
  /** Label for the action button */
  action?: string;
  onAction?: () => void;
}) {
  const styles = useThemedStyles(makeStyles);
  const name = (icon && ICONS[icon]) || ICONS.default;
  return (
    <View style={styles.box}>
      {/* Illustrated icon container */}
      <View style={styles.iconWrap}>
        {/* Decorative ring */}
        <View style={styles.ring} />
        <Ionicons name={name} size={36} color={colors.cta} />
      </View>
      <Text style={styles.title}>{title}</Text>
      {subtitle ? <Text style={styles.sub}>{subtitle}</Text> : null}
      {action && onAction ? (
        <Pressable style={styles.actionBtn} onPress={onAction}>
          <Ionicons name="arrow-forward" size={15} color="#FFF" />
          <Text style={styles.actionText}>{action}</Text>
        </Pressable>
      ) : null}
    </View>
  );
}

const makeStyles = () =>
  StyleSheet.create({
    box: {
      paddingVertical: 60,
      paddingHorizontal: 32,
      alignItems: "center",
    },
    iconWrap: {
      width: 92,
      height: 92,
      borderRadius: radius.full,
      backgroundColor: colors.ctaXLight,
      alignItems: "center",
      justifyContent: "center",
      marginBottom: 20,
      borderWidth: 1.5,
      borderColor: colors.ctaLight,
    },
    ring: {
      position: "absolute",
      width: 108,
      height: 108,
      borderRadius: 54,
      borderWidth: 1,
      borderColor: colors.ctaLight,
      opacity: 0.35,
    },
    title: {
      ...type.h3,
      color: colors.text,
      marginBottom: 8,
      textAlign: "center",
    },
    sub: {
      ...type.callout,
      color: colors.textMuted,
      textAlign: "center",
      lineHeight: 22,
    },
    actionBtn: {
      flexDirection: "row",
      alignItems: "center",
      gap: 8,
      marginTop: 20,
      backgroundColor: colors.cta,
      paddingHorizontal: 20,
      paddingVertical: 12,
      borderRadius: radius.full,
      ...shadow.cta,
    },
    actionText: {
      ...type.title,
      color: "#FFF",
    },
  });
