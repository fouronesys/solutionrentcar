import React from "react";
import { StyleSheet, Text, View } from "react-native";
import { radius } from "@/theme/colors";

export function Badge({
  label,
  color,
  bg,
  size = "md",
}: {
  label: string;
  color?: string;
  bg?: string;
  size?: "sm" | "md";
}) {
  const textColor = color ?? "#D97706";
  const bgColor = bg ?? textColor + "18";

  return (
    <View style={[styles.box, { backgroundColor: bgColor }, size === "sm" && styles.sm]}>
      <Text style={[styles.txt, { color: textColor }, size === "sm" && styles.txtSm]}>
        {label}
      </Text>
    </View>
  );
}

const styles = StyleSheet.create({
  box: {
    alignSelf: "flex-start",
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: radius.full,
  },
  sm: { paddingHorizontal: 8, paddingVertical: 3 },
  txt: { fontSize: 12, fontWeight: "700", letterSpacing: 0.2 },
  txtSm: { fontSize: 11 },
});
