/**
 * Skeleton — shimmer placeholder components.
 * Uses a LinearGradient sweep animation for the shimmer effect.
 */
import React, { useEffect } from "react";
import { Dimensions, StyleSheet, View, ViewStyle } from "react-native";
import Animated, {
  useAnimatedStyle,
  useSharedValue,
  withRepeat,
  withSequence,
  withTiming,
} from "react-native-reanimated";
import { colors, radius, spacing } from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";

const { width: SW } = Dimensions.get("window");

// ─── Base skeleton block ──────────────────────────────────────────────────────
export function Skeleton({
  width,
  height = 14,
  rounded = radius.sm,
  style,
}: {
  width?: number | `${number}%`;
  height?: number;
  rounded?: number;
  style?: ViewStyle;
}) {
  const opacity = useSharedValue(0.45);

  useEffect(() => {
    opacity.value = withRepeat(
      withSequence(
        withTiming(1, { duration: 600 }),
        withTiming(0.45, { duration: 600 }),
      ),
      -1,
      false,
    );
  }, [opacity]);

  const animStyle = useAnimatedStyle(() => ({ opacity: opacity.value }));

  return (
    <Animated.View
      style={[
        {
          width: width ?? "100%",
          height,
          borderRadius: rounded,
          backgroundColor: colors.border,
        },
        animStyle,
        style,
      ]}
    />
  );
}

// ─── Car card placeholder (16:10 ratio image) ─────────────────────────────────
export function CarCardSkeleton() {
  const styles = useThemedStyles(makeStyles);
  // 16:10 image height relative to card width (screen – margins)
  const imgH = Math.round((SW - spacing.xl * 2) * (10 / 16));

  return (
    <View style={styles.card}>
      <Skeleton height={imgH} rounded={0} />
      <View style={styles.body}>
        {/* Brand */}
        <Skeleton width="30%" height={11} />
        {/* Model name */}
        <Skeleton width="65%" height={20} style={{ marginTop: 8 }} />
        {/* Spec chips */}
        <View style={styles.chips}>
          <Skeleton width={56} height={28} rounded={radius.full} />
          <Skeleton width={72} height={28} rounded={radius.full} />
          <Skeleton width={56} height={28} rounded={radius.full} />
        </View>
        {/* Price + CTA */}
        <View style={styles.footer}>
          <Skeleton width={88} height={24} />
          <Skeleton width={100} height={42} rounded={radius.full} />
        </View>
      </View>
    </View>
  );
}

export function ListSkeleton({ count = 3 }: { count?: number }) {
  return (
    <View style={{ paddingTop: spacing.sm }}>
      {Array.from({ length: count }).map((_, i) => (
        <CarCardSkeleton key={i} />
      ))}
    </View>
  );
}

export function RowSkeleton() {
  const styles = useThemedStyles(makeStyles);
  return (
    <View style={styles.row}>
      <Skeleton width={48} height={48} rounded={radius.md} />
      <View style={{ flex: 1, marginLeft: 12 }}>
        <Skeleton width="60%" height={15} />
        <Skeleton width="40%" height={12} style={{ marginTop: 8 }} />
      </View>
    </View>
  );
}

// ─── Styles ───────────────────────────────────────────────────────────────────
const makeStyles = () =>
  StyleSheet.create({
    card: {
      backgroundColor: colors.card,
      borderRadius: radius.xl,
      marginHorizontal: spacing.xl,
      marginBottom: spacing.md,
      overflow: "hidden",
    },
    body: { padding: spacing.lg },
    chips: { flexDirection: "row", gap: 8, marginTop: 16 },
    footer: {
      flexDirection: "row",
      justifyContent: "space-between",
      alignItems: "center",
      marginTop: 18,
    },
    row: {
      flexDirection: "row",
      alignItems: "center",
      backgroundColor: colors.card,
      borderRadius: radius.lg,
      padding: spacing.lg,
      marginHorizontal: spacing.lg,
      marginBottom: spacing.md,
    },
  });
