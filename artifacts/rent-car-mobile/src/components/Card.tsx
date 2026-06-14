import React from "react";
import { Pressable, StyleSheet, View, ViewStyle } from "react-native";
import { colors, radius, shadow } from "@/theme/colors";

export function Card({
  children,
  onPress,
  style,
  variant = "default",
  padding = 16,
}: {
  children: React.ReactNode;
  onPress?: () => void;
  style?: ViewStyle;
  variant?: "default" | "dark" | "flat";
  padding?: number;
}) {
  const bg =
    variant === "dark" ? colors.dark :
    variant === "flat" ? colors.borderLight :
    colors.card;

  const cardStyle = [
    styles.card,
    shadow.md,
    { backgroundColor: bg, padding },
    style,
  ];

  if (onPress) {
    return (
      <Pressable
        onPress={onPress}
        style={({ pressed }) => [
          cardStyle,
          pressed && { opacity: 0.92, transform: [{ scale: 0.99 }] },
        ]}
      >
        {children}
      </Pressable>
    );
  }
  return <View style={cardStyle}>{children}</View>;
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    marginBottom: 12,
  },
});
