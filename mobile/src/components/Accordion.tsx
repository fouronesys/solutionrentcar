/**
 * Accordion — collapsible section with animated chevron.
 */
import React, { useRef, useState } from "react";
import { Animated, Pressable, StyleSheet, Text, View } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { colors, radius, spacing, type } from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";

interface AccordionProps {
  title: string;
  icon?: keyof typeof Ionicons.glyphMap;
  /** Small badge text shown in the header (e.g. a price total) */
  badge?: string;
  children: React.ReactNode;
  defaultOpen?: boolean;
}

export function Accordion({
  title,
  icon,
  badge,
  children,
  defaultOpen = false,
}: AccordionProps) {
  const styles = useThemedStyles(makeStyles);
  const [open, setOpen] = useState(defaultOpen);
  const rotation = useRef(new Animated.Value(defaultOpen ? 1 : 0)).current;

  const toggle = () => {
    const toValue = open ? 0 : 1;
    Animated.timing(rotation, {
      toValue,
      duration: 200,
      useNativeDriver: true,
    }).start();
    setOpen((v) => !v);
  };

  const rotate = rotation.interpolate({
    inputRange: [0, 1],
    outputRange: ["0deg", "180deg"],
  });

  return (
    <View style={styles.wrapper}>
      <Pressable style={styles.header} onPress={toggle} hitSlop={4}>
        <View style={styles.headerLeft}>
          {icon ? (
            <Ionicons name={icon} size={14} color={colors.cta} />
          ) : null}
          <Text style={styles.title}>{title}</Text>
          {badge ? (
            <View style={styles.badge}>
              <Text style={styles.badgeText}>{badge}</Text>
            </View>
          ) : null}
        </View>
        <Animated.View style={{ transform: [{ rotate }] }}>
          <Ionicons name="chevron-down" size={16} color={colors.textMuted} />
        </Animated.View>
      </Pressable>
      {open ? <View style={styles.body}>{children}</View> : null}
    </View>
  );
}

const makeStyles = () =>
  StyleSheet.create({
    wrapper: {
      backgroundColor: colors.card,
      borderRadius: radius.lg,
      marginBottom: spacing.md,
      overflow: "hidden",
      borderWidth: 1,
      borderColor: colors.border,
    },
    header: {
      flexDirection: "row",
      alignItems: "center",
      justifyContent: "space-between",
      paddingHorizontal: spacing.lg,
      paddingVertical: 14,
    },
    headerLeft: {
      flexDirection: "row",
      alignItems: "center",
      gap: 8,
      flex: 1,
    },
    title: { ...type.label, color: colors.textMuted, flex: 1 },
    badge: {
      paddingHorizontal: 8,
      paddingVertical: 3,
      borderRadius: radius.full,
      backgroundColor: colors.infoBg,
    },
    badgeText: { ...type.small, color: colors.info },
    body: {
      paddingHorizontal: spacing.lg,
      paddingBottom: spacing.lg,
    },
  });
