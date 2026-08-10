import React, { useState } from "react";
import { StyleSheet, Text, TextInput, TextInputProps, View, ViewStyle } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { colors, font, radius, type } from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";

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
  icon?: keyof typeof Ionicons.glyphMap;
}) {
  const styles = useThemedStyles(makeStyles);
  const [focused, setFocused] = useState(false);

  const borderColor = error ? colors.danger : focused ? colors.cta : colors.border;

  return (
    <View style={[{ marginBottom: 16 }, containerStyle]}>
      {label ? <Text style={styles.label}>{label}</Text> : null}
      <View
        style={[
          styles.wrap,
          { borderColor },
          focused && !error ? styles.wrapFocused : null,
        ]}
      >
        {icon ? (
          <Ionicons
            name={icon}
            size={18}
            color={focused ? colors.cta : colors.textMuted}
            style={styles.icon}
          />
        ) : null}
        <TextInput
          placeholderTextColor={colors.textMuted}
          style={[styles.input, icon ? { paddingLeft: 44 } : null]}
          onFocus={(e) => { setFocused(true); rest.onFocus?.(e); }}
          onBlur={(e) => { setFocused(false); rest.onBlur?.(e); }}
          {...rest}
        />
      </View>
      {error ? (
        <View style={styles.errorRow}>
          <Ionicons name="alert-circle" size={13} color={colors.danger} />
          <Text style={styles.error}>{error}</Text>
        </View>
      ) : null}
    </View>
  );
}

const makeStyles = () => StyleSheet.create({
  label: {
    ...type.captionMed,
    color: colors.textSecondary,
    marginBottom: 7,
  },
  wrap: {
    flexDirection: "row",
    alignItems: "center",
    borderWidth: 1.5,
    borderColor: colors.border,
    borderRadius: radius.md,
    backgroundColor: colors.card,
  },
  wrapFocused: {
    backgroundColor: colors.ctaXLight,
  },
  icon: { position: "absolute", left: 14, zIndex: 1 },
  input: {
    flex: 1,
    minHeight: 52,
    paddingHorizontal: 16,
    fontFamily: font.medium,
    fontSize: 15,
    color: colors.text,
  },
  errorRow: { flexDirection: "row", alignItems: "center", gap: 4, marginTop: 5 },
  error: { ...type.small, color: colors.danger },
});
