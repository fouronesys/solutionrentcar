/**
 * Toast — lightweight in-app notification that replaces native Alert for
 * transient success/error/info feedback.
 *
 * Usage:
 *   const toast = useToast();
 *   toast({ message: "Reserva creada", type: "success" });
 *
 * Add <ToastProvider> to the root layout (wrapping the rest of the tree).
 */
import React, {
  createContext,
  useCallback,
  useContext,
  useRef,
  useState,
} from "react";
import { Animated, StyleSheet, Text } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { colors, radius, shadow, type } from "@/theme/colors";

export type ToastType = "success" | "error" | "info";
export interface ToastConfig {
  message: string;
  type?: ToastType;
  /** ms before auto-dismiss – default 3200 */
  duration?: number;
}

type ShowFn = (cfg: ToastConfig) => void;

const ToastCtx = createContext<ShowFn>(() => {});

export const useToast = (): ShowFn => useContext(ToastCtx);

const ICON_MAP: Record<ToastType, keyof typeof Ionicons.glyphMap> = {
  success: "checkmark-circle",
  error: "warning",
  info: "information-circle",
};

const BG_MAP: Record<ToastType, string> = {
  success: "#059669",
  error: "#DC2626",
  info: "#9A7B12",
};

export function ToastProvider({ children }: { children: React.ReactNode }) {
  const insets = useSafeAreaInsets();
  const [cfg, setCfg] = useState<ToastConfig | null>(null);
  const opacity = useRef(new Animated.Value(0)).current;
  const timer = useRef<ReturnType<typeof setTimeout> | null>(null);

  const show = useCallback<ShowFn>(
    (incoming) => {
      if (timer.current) clearTimeout(timer.current);
      setCfg(incoming);
      opacity.setValue(0);
      Animated.timing(opacity, {
        toValue: 1,
        duration: 220,
        useNativeDriver: true,
      }).start();
      timer.current = setTimeout(() => {
        Animated.timing(opacity, {
          toValue: 0,
          duration: 220,
          useNativeDriver: true,
        }).start(() => setCfg(null));
      }, incoming.duration ?? 3200);
    },
    [opacity],
  );

  const kind = cfg?.type ?? "info";
  const iconName = ICON_MAP[kind];
  const bg = BG_MAP[kind];

  return (
    <ToastCtx.Provider value={show}>
      {children}
      {cfg ? (
        <Animated.View
          pointerEvents="none"
          style={[
            styles.toast,
            { opacity, backgroundColor: bg, bottom: insets.bottom + 88 },
          ]}
        >
          <Ionicons name={iconName} size={20} color="#FFF" />
          <Text style={styles.msg} numberOfLines={3}>
            {cfg.message}
          </Text>
        </Animated.View>
      ) : null}
    </ToastCtx.Provider>
  );
}

const styles = StyleSheet.create({
  toast: {
    position: "absolute",
    left: 16,
    right: 16,
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
    paddingHorizontal: 16,
    paddingVertical: 14,
    borderRadius: radius.lg,
    zIndex: 9999,
    ...shadow.lg,
  },
  msg: { ...type.bodyMed, color: "#FFF", flex: 1, lineHeight: 20 },
});
