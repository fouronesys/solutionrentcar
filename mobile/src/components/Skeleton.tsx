import React, { useEffect } from "react";
import { StyleSheet, View, ViewStyle } from "react-native";
import Animated, {
  useAnimatedStyle,
  useSharedValue,
  withRepeat,
  withSequence,
  withTiming,
} from "react-native-reanimated";
import { colors, radius, spacing } from "@/theme/colors";

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
  const opacity = useSharedValue(0.5);

  useEffect(() => {
    opacity.value = withRepeat(
      withSequence(
        withTiming(1, { duration: 700 }),
        withTiming(0.5, { duration: 700 }),
      ),
      -1,
      false,
    );
  }, [opacity]);

  const animStyle = useAnimatedStyle(() => ({ opacity: opacity.value }));

  return (
    <Animated.View
      style={[
        { width: width ?? "100%", height, borderRadius: rounded, backgroundColor: colors.border },
        animStyle,
        style,
      ]}
    />
  );
}

// Card-shaped placeholder matching the car list cards
export function CarCardSkeleton() {
  return (
    <View style={styles.card}>
      <Skeleton height={170} rounded={0} />
      <View style={styles.body}>
        <Skeleton width="55%" height={18} />
        <Skeleton width="35%" height={13} style={{ marginTop: 10 }} />
        <View style={styles.chips}>
          <Skeleton width={64} height={26} rounded={radius.full} />
          <Skeleton width={64} height={26} rounded={radius.full} />
          <Skeleton width={64} height={26} rounded={radius.full} />
        </View>
        <View style={styles.footer}>
          <Skeleton width={90} height={22} />
          <Skeleton width={96} height={40} rounded={radius.md} />
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

const styles = StyleSheet.create({
  card: {
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    marginHorizontal: spacing.lg,
    marginBottom: spacing.md,
    overflow: "hidden",
  },
  body: { padding: spacing.lg },
  chips: { flexDirection: "row", gap: 8, marginTop: 16 },
  footer: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", marginTop: 18 },
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
