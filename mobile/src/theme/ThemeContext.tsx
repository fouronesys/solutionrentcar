/**
 * Theme system — dark/light with persisted preference (default: dark).
 *
 * How it works:
 *  - `applyTheme(mode)` mutates the shared `colors` object in place.
 *  - The root layout re-keys the app tree on `mode`, so every screen remounts.
 *  - Screens build their styles in render via `useThemedStyles(makeStyles)`,
 *    so remounting produces styles with the new palette.
 */
import React, { createContext, useCallback, useContext, useEffect, useMemo, useState } from "react";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { applyTheme, ThemeMode } from "@/theme/colors";

const STORAGE_KEY = "yowell.theme";

type ThemeCtx = {
  mode: ThemeMode;
  isDark: boolean;
  /** True once the persisted preference has been read. */
  ready: boolean;
  setMode: (m: ThemeMode) => void;
  toggle: () => void;
};

const Ctx = createContext<ThemeCtx>({
  mode: "dark",
  isDark: true,
  ready: false,
  setMode: () => {},
  toggle: () => {},
});

export function ThemeProvider({ children }: { children: React.ReactNode }) {
  const [mode, setModeState] = useState<ThemeMode>("dark"); // default dark
  const [ready, setReady] = useState(false);

  useEffect(() => {
    (async () => {
      try {
        const saved = await AsyncStorage.getItem(STORAGE_KEY);
        if (saved === "light" || saved === "dark") {
          applyTheme(saved);
          setModeState(saved);
        } else {
          applyTheme("dark");
        }
      } catch {
        applyTheme("dark");
      } finally {
        setReady(true);
      }
    })();
  }, []);

  const setMode = useCallback((m: ThemeMode) => {
    applyTheme(m);
    setModeState(m);
    AsyncStorage.setItem(STORAGE_KEY, m).catch(() => {});
  }, []);

  const toggle = useCallback(() => {
    setModeState((prev) => {
      const next: ThemeMode = prev === "dark" ? "light" : "dark";
      applyTheme(next);
      AsyncStorage.setItem(STORAGE_KEY, next).catch(() => {});
      return next;
    });
  }, []);

  const value = useMemo(
    () => ({ mode, isDark: mode === "dark", ready, setMode, toggle }),
    [mode, ready, setMode, toggle],
  );

  return <Ctx.Provider value={value}>{children}</Ctx.Provider>;
}

export function useTheme() {
  return useContext(Ctx);
}

/**
 * Build (and memoize per theme) styles from a factory that reads the mutable
 * `colors` palette. Usage:
 *   const makeStyles = () => StyleSheet.create({ ... uses colors ... });
 *   const styles = useThemedStyles(makeStyles);
 */
export function useThemedStyles<T>(factory: () => T): T {
  const { mode } = useTheme();
  // eslint-disable-next-line react-hooks/exhaustive-deps
  return useMemo(factory, [mode]);
}
