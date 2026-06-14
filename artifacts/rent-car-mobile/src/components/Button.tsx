import React from "react";
import { ActivityIndicator, Pressable, StyleSheet, Text, ViewStyle } from "react-native";
import { colors, radius, shadow } from "@/theme/colors";

type Variant = "primary" | "secondary" | "ghost" | "danger" | "dark";

export function Button({
  title,
  onPress,
  variant = "primary",
  disabled,
  loading,
  style,
  size = "md",
}: {
  title: string;
  onPress?: () => void;
  variant?: Variant;
  disabled?: boolean;
  loading?: boolean;
  style?: ViewStyle;
  size?: "sm" | "md" | "lg";
}) {
  const palettes: Record<Variant, { bg: string; fg: string; border: string }> = {
    primary: { bg: colors.primary, fg: "#1A1100", border: colors.primary },
    secondary: { bg: "#fff", fg: colors.accent, border: colors.border },
    ghost: { bg: "transparent", fg: colors.primaryDark, border: "transparent" },
    danger: { bg: colors.danger, fg: "#fff", border: colors.danger },
    dark: { bg: colors.dark, fg: "#fff", border: colors.dark },
  };
  const p = palettes[variant];

  const heights: Record<string, number> = { sm: 38, md: 50, lg: 56 };
  const fontSizes: Record<string, number> = { sm: 13, md: 15, lg: 17 };

  const isDisabled = disabled || loading;
  const hasShadow = variant === "primary" && !isDisabled;

  return (
    <Pressable
      onPress={onPress}
      disabled={isDisabled}
      style={({ pressed }) => [
        styles.btn,
        {
          backgroundColor: p.bg,
          borderColor: p.border,
          height: heights[size],
          opacity: isDisabled ? 0.5 : pressed ? 0.88 : 1,
          transform: [{ scale: pressed ? 0.98 : 1 }],
        },
        hasShadow ? shadow.primary : null,
        style,
      ]}
    >
      {loading ? (
        <ActivityIndicator color={p.fg} size="small" />
      ) : (
        <Text style={[styles.txt, { color: p.fg, fontSize: fontSizes[size] }]}>{title}</Text>
      )}
    </Pressable>
  );
}

const styles = StyleSheet.create({
  btn: {
    borderRadius: radius.md,
    borderWidth: 1.5,
    paddingHorizontal: 20,
    alignItems: "center",
    justifyContent: "center",
    flexDirection: "row",
  },
  txt: { fontWeight: "700", letterSpacing: 0.2 },
});
