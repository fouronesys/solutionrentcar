import React from "react";
import { StyleSheet, Text, View } from "react-native";
import { colors, radius } from "@/theme/colors";

export function Badge({ label, color }: { label: string; color?: string }) {
  return (
    <View style={[styles.box, { backgroundColor: (color ?? colors.primary) + "22", borderColor: color ?? colors.primary }]}>
      <Text style={[styles.txt, { color: color ?? colors.primary }]}>{label}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  box: {
    alignSelf: "flex-start",
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: radius.sm,
    borderWidth: 1,
  },
  txt: { fontSize: 12, fontWeight: "600" },
});
