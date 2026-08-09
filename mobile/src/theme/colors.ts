import { TextStyle } from "react-native";

export const colors = {
  // ─── Brand ────────────────────────────────────────────────────────────────
  primary: "#1828E8",        // blue – informational / links
  primaryDark: "#111B9A",
  primaryDeep: "#0B126A",
  primaryLight: "#5D6BFF",
  primaryXLight: "#EEF0FF",

  cta: "#E8002D",            // RED – primary CTA / active states / tabs
  ctaDark: "#B5001F",
  ctaLight: "#FF4060",
  ctaXLight: "#FFECEF",

  accent: "#E8002D",         // alias → same as cta for backwards compat

  // ─── Backgrounds ─────────────────────────────────────────────────────────
  bg: "#F2F3F5",             // screen background (cool light gray)
  bgWarm: "#FFFDF5",
  card: "#FFFFFF",

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

  // ─── Dark surfaces ────────────────────────────────────────────────────────
  dark: "#141414",
  darkDeep: "#000000",
  darkCard: "#111827",       // dark navy for route/highlight cards
  darkBorder: "#2A2A2A",
  onDark: "#FAFAFA",
  onDarkMuted: "#A1A1AA",
};

// Gradients (kept for AnimatedSplash; screens no longer use hero gradients)
export const gradients = {
  hero: ["#080B2D", "#111B9A", "#1828E8"] as const,
  heroSoft: ["#0B0B0D", "#111B9A"] as const,
  primary: ["#5D6BFF", "#1828E8", "#111B9A"] as const,
  primarySoft: ["#6F7BFF", "#1828E8"] as const,
  imageScrim: ["transparent", "rgba(0,0,0,0.0)", "rgba(0,0,0,0.88)"] as const,
  cardScrim: ["transparent", "rgba(0,0,0,0.78)"] as const,
  shimmer: ["#E7E9FF", "#F7F8FF", "#E7E9FF"] as const,
};

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
