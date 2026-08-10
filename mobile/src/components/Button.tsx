import React from "react";
import { ActivityIndicator, Pressable, StyleSheet, Text, View, ViewStyle } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import Animated, { useAnimatedStyle, useSharedValue, withTiming } from "react-native-reanimated";
import { colors, font, radius, shadow } from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";

type Variant = "primary" | "secondary" | "ghost" | "danger" | "dark";
type Size = "sm" | "md" | "lg";

const AnimatedPressable = Animated.createAnimatedComponent(Pressable);

export function Button({
  title,
  onPress,
  variant = "primary",
  disabled,
  loading,
  style,
  size = "md",
  icon,
  iconRight,
}: {
  title: string;
  onPress?: () => void;
  variant?: Variant;
  disabled?: boolean;
  loading?: boolean;
  style?: ViewStyle;
  size?: Size;
  icon?: keyof typeof Ionicons.glyphMap;
  iconRight?: boolean;
}) {
  const styles = useThemedStyles(makeStyles);
  const scale = useSharedValue(1);
  const animStyle = useAnimatedStyle(() => ({ transform: [{ scale: scale.value }] }));

  const heights: Record<Size, number> = { sm: 40, md: 52, lg: 58 };
  const fontSizes: Record<Size, number> = { sm: 13, md: 15, lg: 15 };
  const iconSizes: Record<Size, number> = { sm: 16, md: 18, lg: 18 };

  const isDisabled = disabled || loading;

  const onIn = () => { scale.value = withTiming(0.97, { duration: 90 }); };
  const onOut = () => { scale.value = withTiming(1, { duration: 130 }); };

  const bg =
    variant === "primary"   ? colors.cta :
    variant === "secondary" ? colors.card :
    variant === "ghost"     ? "transparent" :
    variant === "danger"    ? colors.danger :
    colors.dark;

  const fg =
    variant === "primary"   ? "#FFFFFF" :
    variant === "secondary" ? colors.text :
    variant === "ghost"     ? colors.primaryDark :
    "#FFFFFF";

  const border =
    variant === "secondary" ? colors.border :
    variant === "ghost"     ? "transparent" :
    bg;

  const shadowStyle =
    variant === "primary"   ? shadow.cta :
    variant === "secondary" ? shadow.xs :
    null;

  const content = loading ? (
    <ActivityIndicator color={fg} size="small" />
  ) : (
    <View style={styles.row}>
      {icon && !iconRight ? <Ionicons name={icon} size={iconSizes[size]} color={fg} style={styles.iconL} /> : null}
      <Text
        style={[styles.txt, { color: fg, fontSize: fontSizes[size] }]}
        numberOfLines={1}
      >
        {title}
      </Text>
      {icon && iconRight ? <Ionicons name={icon} size={iconSizes[size]} color={fg} style={styles.iconR} /> : null}
    </View>
  );

  return (
    <AnimatedPressable
      onPress={onPress}
      onPressIn={onIn}
      onPressOut={onOut}
      disabled={isDisabled}
      style={[
        animStyle,
        styles.btn,
        variant !== "ghost" ? styles.bordered : null,
        {
          backgroundColor: bg,
          borderColor: border,
          height: heights[size],
          opacity: isDisabled ? 0.55 : 1,
        },
        shadowStyle,
        style,
      ]}
    >
      {content}
    </AnimatedPressable>
  );
}

const makeStyles = () => StyleSheet.create({
  btn: {
    borderRadius: radius.md,
    paddingHorizontal: 20,
    alignItems: "center",
    justifyContent: "center",
    overflow: "hidden",
  },
  bordered: { borderWidth: 1.5 },
  row: { flexDirection: "row", alignItems: "center", justifyContent: "center" },
  txt: { fontFamily: font.bold, letterSpacing: 0.6, textTransform: "uppercase" },
  iconL: { marginRight: 8 },
  iconR: { marginLeft: 8 },
});
