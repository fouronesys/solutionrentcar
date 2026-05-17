import React from "react";
import { StyleSheet, Text, TextInput, TextInputProps, View } from "react-native";
import { colors, radius } from "@/theme/colors";

export function Input({
  label,
  error,
  ...rest
}: TextInputProps & { label?: string; error?: string }) {
  return (
    <View style={{ marginBottom: 12 }}>
      {label ? <Text style={styles.label}>{label}</Text> : null}
      <TextInput
        placeholderTextColor={colors.textMuted}
        style={[styles.input, error ? { borderColor: colors.danger } : null]}
        {...rest}
      />
      {error ? <Text style={styles.error}>{error}</Text> : null}
    </View>
  );
}

const styles = StyleSheet.create({
  label: { fontSize: 13, color: colors.textMuted, marginBottom: 4, fontWeight: "500" },
  input: {
    minHeight: 48,
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: radius.md,
    backgroundColor: "#fff",
    paddingHorizontal: 12,
    fontSize: 16,
    color: colors.text,
  },
  error: { color: colors.danger, fontSize: 12, marginTop: 4 },
});
