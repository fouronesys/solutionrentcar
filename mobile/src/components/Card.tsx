import React from "react";
import { Pressable, StyleSheet, View, ViewStyle } from "react-native";
import Animated, { useAnimatedStyle, useSharedValue, withTiming } from "react-native-reanimated";
import { colors, radius, shadow } from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";

const AnimatedPressable = Animated.createAnimatedComponent(Pressable);

export function Card({
  children,
  onPress,
  style,
  variant = "default",
  padding = 16,
  elevation = "md",
}: {
  children: React.ReactNode;
  onPress?: () => void;
  style?: ViewStyle;
  variant?: "default" | "dark" | "flat";
  padding?: number;
  elevation?: "xs" | "sm" | "md" | "lg" | "none";
}) {
  const styles = useThemedStyles(makeStyles);
  const scale = useSharedValue(1);
  const animStyle = useAnimatedStyle(() => ({ transform: [{ scale: scale.value }] }));

  const bg =
    variant === "dark" ? colors.darkCard :
    variant === "flat" ? colors.borderLight :
    colors.card;

  const shadowStyle = elevation === "none" ? null : shadow[elevation];

  const base: ViewStyle[] = [
    styles.card,
    { backgroundColor: bg, padding },
    variant === "flat" ? styles.flatBorder : null,
    shadowStyle,
    style,
  ].filter(Boolean) as ViewStyle[];

  if (onPress) {
    return (
      <AnimatedPressable
        onPress={onPress}
        onPressIn={() => { scale.value = withTiming(0.985, { duration: 90 }); }}
        onPressOut={() => { scale.value = withTiming(1, { duration: 140 }); }}
        style={[animStyle, ...base]}
      >
        {children}
      </AnimatedPressable>
    );
  }
  return <View style={base}>{children}</View>;
}

const makeStyles = () => StyleSheet.create({
  card: {
    borderRadius: radius.lg,
    marginBottom: 12,
  },
  flatBorder: { borderWidth: 1, borderColor: colors.border },
});
