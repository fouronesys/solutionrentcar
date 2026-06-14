import React from "react";
import { StyleSheet, Text, View } from "react-native";
import { colors, font, radius } from "@/theme/colors";

export function Badge({
  label,
  color,
  bg,
  size = "md",
  dot = true,
}: {
  label: string;
  color?: string;
  bg?: string;
  size?: "sm" | "md";
  dot?: boolean;
}) {
  const textColor = color ?? colors.primaryDark;
  const bgColor = bg ?? textColor + "1A";

  return (
    <View style={[styles.box, { backgroundColor: bgColor }, size === "sm" && styles.sm]}>
      {dot ? <View style={[styles.dot, { backgroundColor: textColor }]} /> : null}
      <Text style={[styles.txt, { color: textColor }, size === "sm" && styles.txtSm]}>
        {label}
      </Text>
    </View>
  );
}

const styles = StyleSheet.create({
  box: {
    flexDirection: "row",
    alignItems: "center",
    alignSelf: "flex-start",
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: radius.full,
    gap: 5,
  },
  sm: { paddingHorizontal: 8, paddingVertical: 3 },
  dot: { width: 6, height: 6, borderRadius: 3 },
  txt: { fontFamily: font.semibold, fontSize: 12, letterSpacing: 0.1 },
  txtSm: { fontSize: 11 },
});
