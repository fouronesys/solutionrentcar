import React from "react";
import { StyleSheet, Text, TextInput, TextInputProps, View, ViewStyle } from "react-native";
import { colors, radius } from "@/theme/colors";

export function Input({
  label,
  error,
  containerStyle,
  icon,
  ...rest
}: TextInputProps & {
  label?: string;
  error?: string;
  containerStyle?: ViewStyle;
  icon?: React.ReactNode;
}) {
  return (
    <View style={[{ marginBottom: 14 }, containerStyle]}>
      {label ? <Text style={styles.label}>{label}</Text> : null}
      <View style={[styles.wrap, error ? styles.wrapError : null]}>
        {icon ? <View style={styles.icon}>{icon}</View> : null}
        <TextInput
          placeholderTextColor={colors.textMuted}
          style={[styles.input, icon ? { paddingLeft: 42 } : null]}
          {...rest}
        />
      </View>
      {error ? <Text style={styles.error}>{error}</Text> : null}
    </View>
  );
}

const styles = StyleSheet.create({
  label: {
    fontSize: 13,
    color: colors.textSecondary,
    marginBottom: 6,
    fontWeight: "600",
    letterSpacing: 0.1,
  },
  wrap: {
    flexDirection: "row",
    alignItems: "center",
    borderWidth: 1.5,
    borderColor: colors.border,
    borderRadius: radius.md,
    backgroundColor: "#fff",
    overflow: "hidden",
  },
  wrapError: { borderColor: colors.danger },
  icon: { position: "absolute", left: 12, zIndex: 1, opacity: 0.5 },
  input: {
    flex: 1,
    minHeight: 50,
    paddingHorizontal: 14,
    fontSize: 15,
    color: colors.text,
    fontWeight: "400",
  },
  error: { color: colors.danger, fontSize: 12, marginTop: 4, fontWeight: "500" },
});
