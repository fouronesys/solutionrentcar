/**
 * ScreenHeader — shared white header matching the Yowell design language.
 * Shows logo (shield) + title + subtitle, with optional right actions.
 */
import React from "react";
import { Image, Pressable, StyleSheet, Text, View, ViewStyle } from "react-native";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons";
import { colors, font, radius, shadow, type } from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";

interface ScreenHeaderProps {
  title: string;
  subtitle?: string;
  style?: ViewStyle;
  /** Show back arrow (calls onBack) */
  showBack?: boolean;
  onBack?: () => void;
  /** Right-side custom element */
  right?: React.ReactNode;
}

export function ScreenHeader({
  title,
  subtitle,
  style,
  showBack,
  onBack,
  right,
}: ScreenHeaderProps) {
  const styles = useThemedStyles(makeStyles);
  const insets = useSafeAreaInsets();

  return (
    <View style={[styles.bar, { paddingTop: insets.top + 10 }, style]}>
      {showBack ? (
        <Pressable onPress={onBack} style={styles.backBtn} hitSlop={10}>
          <Ionicons name="arrow-back" size={22} color={colors.text} />
        </Pressable>
      ) : null}

      <View style={styles.logoWrap}>
        <Image
          source={require("../../assets/images/logo.png")}
          style={styles.logo}
          resizeMode="contain"
        />
      </View>

      <View style={styles.textBlock}>
        <Text style={styles.title} numberOfLines={1}>
          {title}
        </Text>
        {subtitle ? (
          <Text style={styles.subtitle} numberOfLines={1}>
            {subtitle}
          </Text>
        ) : null}
      </View>

      {right ? <View style={styles.rightSlot}>{right}</View> : null}
    </View>
  );
}

/** Bell icon with an optional badge count, ready to use in `right` prop */
export function BellButton({
  unread,
  onPress,
}: {
  unread?: number;
  onPress?: () => void;
}) {
  const styles = useThemedStyles(makeStyles);
  return (
    <Pressable onPress={onPress} style={styles.bellBtn} hitSlop={8}>
      <Ionicons
        name={unread ? "notifications" : "notifications-outline"}
        size={24}
        color={colors.text}
      />
      {unread && unread > 0 ? (
        <View style={styles.badge}>
          <Text style={styles.badgeText}>{unread > 9 ? "9+" : unread}</Text>
        </View>
      ) : null}
    </Pressable>
  );
}

const makeStyles = () => StyleSheet.create({
  bar: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 16,
    paddingBottom: 14,
    backgroundColor: colors.card,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
    ...shadow.xs,
    gap: 10,
  },
  backBtn: {
    width: 40,
    height: 40,
    borderRadius: radius.full,
    alignItems: "center",
    justifyContent: "center",
  },
  logoWrap: {
    width: 46,
    height: 46,
    borderRadius: radius.md,
    overflow: "hidden",
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: colors.bg,
  },
  logo: { width: 40, height: 40 },
  textBlock: { flex: 1 },
  title: {
    fontFamily: font.bold,
    fontSize: 17,
    color: colors.text,
    letterSpacing: -0.2,
  },
  subtitle: {
    fontFamily: font.regular,
    fontSize: 13,
    color: colors.textMuted,
    marginTop: 1,
  },
  rightSlot: { marginLeft: "auto" },
  bellBtn: {
    width: 44,
    height: 44,
    borderRadius: radius.full,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: colors.bg,
  },
  badge: {
    position: "absolute",
    top: 4,
    right: 4,
    minWidth: 17,
    height: 17,
    borderRadius: 9,
    backgroundColor: colors.cta,
    alignItems: "center",
    justifyContent: "center",
    paddingHorizontal: 3,
    borderWidth: 1.5,
    borderColor: colors.card,
  },
  badgeText: { color: "#fff", fontSize: 9, fontFamily: font.bold },
});
