import { TextStyle } from "react-native";

export type ThemeMode = "light" | "dark";

// ─── Palettes ────────────────────────────────────────────────────────────────
// Brand stays constant; surfaces/text/borders flip per theme.

const brand = {
  primary: "#4C5BFF",        // blue – informational / links (readable on dark)
  primaryDark: "#111B9A",
  primaryDeep: "#0B126A",
  primaryLight: "#7D89FF",

  cta: "#E8002D",            // RED – primary CTA / active states / tabs
  ctaDark: "#B5001F",
  ctaLight: "#FF4060",

  accent: "#E8002D",         // alias → same as cta for backwards compat
};

export const lightColors = {
  ...brand,
  primary: "#1828E8",
  primaryLight: "#5D6BFF",
  primaryXLight: "#EEF0FF",
  ctaXLight: "#FFECEF",

  // ─── Backgrounds ─────────────────────────────────────────────────────────
  bg: "#F2F3F5",             // screen background (cool light gray)
  bgWarm: "#FFFDF5",
  card: "#FFFFFF",
  cardAlt: "#F7F7F9",        // subtle inset surface on top of card

  // ─── Text ─────────────────────────────────────────────────────────────────
  text: "#0A0A0A",
  textSecondary: "#52525B",
  textMuted: "#A1A1AA",
  textFaint: "#D4D4D8",

  // ─── Borders ──────────────────────────────────────────────────────────────
  border: "#E6E6E3",
  borderLight: "#F2F2F0",

  // ─── Semantic ─────────────────────────────────────────────────────────────
  success: "#059669",
  successBg: "#ECFDF5",
  warning: "#B7791F",
  warningBg: "#FFF7E6",
  danger: "#DC2626",
  dangerBg: "#FEF2F2",
  info: "#111B9A",
  infoBg: "#EEF0FF",

  // ─── Dark surfaces (avatar squares, highlight cards) ─────────────────────
  dark: "#141414",
  darkDeep: "#000000",
  darkCard: "#111827",       // dark navy for route/highlight cards
  darkBorder: "#2A2A2A",
  onDark: "#FAFAFA",
  onDarkMuted: "#A1A1AA",

  // ─── Overlays / misc ──────────────────────────────────────────────────────
  overlay: "rgba(0,0,0,0.45)",
  tint: "#EEF4FF",           // loyalty / soft blue tint card
  tintBorder: "#D0DFFF",
};

export const darkColors: typeof lightColors = {
  ...brand,
  // Brighter red for dark surfaces — #E8002D falls below 4.5:1 contrast on
  // near-black; buttons still read fine, but text/labels need this lift.
  cta: "#FF2D50",
  ctaLight: "#FF5C77",
  accent: "#FF2D50",
  primaryXLight: "#1B2040",  // dark blue tint surface
  ctaXLight: "#3A0E19",      // dark red tint surface

  bg: "#0B0B0F",             // near-black with a hint of blue
  bgWarm: "#101014",
  card: "#17171D",
  cardAlt: "#1F1F27",

  text: "#F5F5F7",
  textSecondary: "#B3B3BD",
  textMuted: "#8E8E9A",
  textFaint: "#44444E",

  border: "#26262E",
  borderLight: "#1E1E25",

  success: "#34D399",
  successBg: "#0C2B21",
  warning: "#F5B94E",
  warningBg: "#2E2410",
  danger: "#F87171",
  dangerBg: "#331216",
  info: "#8B96FF",
  infoBg: "#1B2040",

  dark: "#1F1F27",           // "dark accent" squares become elevated surfaces
  darkDeep: "#000000",
  darkCard: "#1B2337",
  darkBorder: "#33333D",
  onDark: "#FAFAFA",
  onDarkMuted: "#A1A1AA",

  overlay: "rgba(0,0,0,0.6)",
  tint: "#161C2E",
  tintBorder: "#28324E",
};

// Mutable palette — DEFAULT DARK. `applyTheme` swaps values in place so every
// `makeStyles()` factory called after a theme change picks up the new colors.
export const colors = { ...darkColors };

export const bookingStatus: Record<number, { en: string; es: string; color: string; bg: string }> = {
  0: { en: "Pending",   es: "Pendiente",  color: "#D97706", bg: "#FFFBEB" },
  1: { en: "Confirmed", es: "Confirmada", color: "#059669", bg: "#ECFDF5" },
  2: { en: "Cancelled", es: "Cancelada",  color: "#DC2626", bg: "#FEF2F2" },
  3: { en: "Delivered", es: "Entregada",  color: "#1828E8", bg: "#EEF0FF" },
  4: { en: "Returned",  es: "Devuelta",   color: "#71717A", bg: "#F4F4F5" },
};

export const carStatus: Record<number, { en: string; es: string; color: string; bg: string }> = {
  0: { en: "Available",    es: "Disponible",    color: "#059669", bg: "#ECFDF5" },
  1: { en: "In use",       es: "En uso",        color: "#D97706", bg: "#FFFBEB" },
  2: { en: "Maintenance",  es: "Mantenimiento", color: "#3F3F46", bg: "#F4F4F5" },
};

const bookingStatusDark: Record<number, { color: string; bg: string }> = {
  0: { color: "#F5B94E", bg: "#2E2410" },
  1: { color: "#34D399", bg: "#0C2B21" },
  2: { color: "#F87171", bg: "#331216" },
  3: { color: "#8B96FF", bg: "#1B2040" },
  4: { color: "#A1A1AA", bg: "#22222A" },
};
const bookingStatusLight: Record<number, { color: string; bg: string }> = {
  0: { color: "#D97706", bg: "#FFFBEB" },
  1: { color: "#059669", bg: "#ECFDF5" },
  2: { color: "#DC2626", bg: "#FEF2F2" },
  3: { color: "#1828E8", bg: "#EEF0FF" },
  4: { color: "#71717A", bg: "#F4F4F5" },
};
const carStatusDark: Record<number, { color: string; bg: string }> = {
  0: { color: "#34D399", bg: "#0C2B21" },
  1: { color: "#F5B94E", bg: "#2E2410" },
  2: { color: "#B3B3BD", bg: "#22222A" },
};
const carStatusLight: Record<number, { color: string; bg: string }> = {
  0: { color: "#059669", bg: "#ECFDF5" },
  1: { color: "#D97706", bg: "#FFFBEB" },
  2: { color: "#3F3F46", bg: "#F4F4F5" },
};

// Gradients (kept for AnimatedSplash; screens no longer use hero gradients)
export const gradients = {
  hero: ["#080B2D", "#111B9A", "#1828E8"] as const,
  heroSoft: ["#0B0B0D", "#111B9A"] as const,
  primary: ["#5D6BFF", "#1828E8", "#111B9A"] as const,
  primarySoft: ["#6F7BFF", "#1828E8"] as const,
  imageScrim: ["transparent", "rgba(0,0,0,0.0)", "rgba(0,0,0,0.88)"] as const,
  cardScrim: ["transparent", "rgba(0,0,0,0.78)"] as const,
  shimmer: ["#1F1F27", "#2A2A33", "#1F1F27"] as [string, string, string],
};

const shimmerLight: [string, string, string] = ["#E7E9FF", "#F7F8FF", "#E7E9FF"];
const shimmerDark: [string, string, string] = ["#1F1F27", "#2A2A33", "#1F1F27"];

/**
 * Swap the active palette in place. Every object exported from this module is
 * mutated so existing imports (`import { colors } from "@/theme/colors"`) see
 * the new values. Screens must build styles inside render (via
 * `useThemedStyles`) for the change to take effect.
 */
export function applyTheme(mode: ThemeMode) {
  const next = mode === "dark" ? darkColors : lightColors;
  Object.assign(colors, next);
  gradients.shimmer = mode === "dark" ? shimmerDark : shimmerLight;
  const bs = mode === "dark" ? bookingStatusDark : bookingStatusLight;
  const cs = mode === "dark" ? carStatusDark : carStatusLight;
  for (const k of Object.keys(bookingStatus)) Object.assign(bookingStatus[+k], bs[+k]);
  for (const k of Object.keys(carStatus)) Object.assign(carStatus[+k], cs[+k]);
}

export const radius = { xs: 6, sm: 10, md: 14, lg: 18, xl: 24, xxl: 32, full: 9999 };
export const spacing = { xs: 4, sm: 8, md: 12, lg: 16, xl: 20, xxl: 28, xxxl: 40 };

// ─── Typography (Inter) ──────────────────────────────────────────────────────
export const font = {
  regular: "Inter_400Regular",
  medium: "Inter_500Medium",
  semibold: "Inter_600SemiBold",
  bold: "Inter_700Bold",
  extrabold: "Inter_800ExtraBold",
};

export const type = {
  display: { fontFamily: font.extrabold, fontSize: 30, lineHeight: 34, letterSpacing: -0.6 },
  h1: { fontFamily: font.bold, fontSize: 24, lineHeight: 29, letterSpacing: -0.4 },
  h2: { fontFamily: font.bold, fontSize: 20, lineHeight: 25, letterSpacing: -0.3 },
  h3: { fontFamily: font.semibold, fontSize: 17, lineHeight: 22, letterSpacing: -0.2 },
  title: { fontFamily: font.semibold, fontSize: 15, lineHeight: 20, letterSpacing: -0.1 },
  body: { fontFamily: font.regular, fontSize: 15, lineHeight: 22 },
  bodyMed: { fontFamily: font.medium, fontSize: 15, lineHeight: 22 },
  callout: { fontFamily: font.regular, fontSize: 14, lineHeight: 20 },
  caption: { fontFamily: font.regular, fontSize: 13, lineHeight: 18 },
  captionMed: { fontFamily: font.medium, fontSize: 13, lineHeight: 18 },
  small: { fontFamily: font.medium, fontSize: 12, lineHeight: 16 },
  label: {
    fontFamily: font.semibold,
    fontSize: 11,
    lineHeight: 14,
    letterSpacing: 0.7,
    textTransform: "uppercase",
  } as TextStyle,
  mono: { fontFamily: font.semibold, fontSize: 15, lineHeight: 20, letterSpacing: 0.2 },
} satisfies Record<string, TextStyle>;

export const shadow = {
  xs: {
    shadowColor: "#000000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.04,
    shadowRadius: 2,
    elevation: 1,
  },
  sm: {
    shadowColor: "#000000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06,
    shadowRadius: 6,
    elevation: 2,
  },
  md: {
    shadowColor: "#000000",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.1,
    shadowRadius: 16,
    elevation: 5,
  },
  lg: {
    shadowColor: "#000000",
    shadowOffset: { width: 0, height: 12 },
    shadowOpacity: 0.16,
    shadowRadius: 28,
    elevation: 10,
  },
  cta: {
    shadowColor: "#E8002D",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.30,
    shadowRadius: 14,
    elevation: 6,
  },
  primary: {
    shadowColor: "#E8002D",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.30,
    shadowRadius: 14,
    elevation: 6,
  },
};
