import { TextStyle } from "react-native";

export const colors = {
  primary: "#F59E0B",
  primaryDark: "#D97706",
  primaryDeep: "#B45309",
  primaryLight: "#FCD34D",
  primaryXLight: "#FFFBEB",
  accent: "#1E293B",

  bg: "#F6F7F9",
  bgWarm: "#FFFDF7",
  card: "#FFFFFF",

  text: "#0B1220",
  textSecondary: "#475569",
  textMuted: "#94A3B8",
  textFaint: "#CBD5E1",

  border: "#E7EBF0",
  borderLight: "#F1F4F8",

  success: "#059669",
  successBg: "#ECFDF5",
  warning: "#D97706",
  warningBg: "#FFFBEB",
  danger: "#DC2626",
  dangerBg: "#FEF2F2",
  info: "#2563EB",
  infoBg: "#EFF6FF",

  dark: "#0F172A",
  darkDeep: "#0B1220",
  darkCard: "#1E293B",
  darkBorder: "#243042",
  onDark: "#F8FAFC",
  onDarkMuted: "#94A3B8",
};

// Gradients (consumed by expo-linear-gradient as readonly tuples)
export const gradients = {
  hero: ["#0B1220", "#1E293B", "#27364B"] as const,
  heroSoft: ["#1E293B", "#0F172A"] as const,
  gold: ["#FCD34D", "#F59E0B", "#D97706"] as const,
  goldSoft: ["#FBBF24", "#F59E0B"] as const,
  imageScrim: ["transparent", "rgba(11,18,32,0.0)", "rgba(11,18,32,0.85)"] as const,
  cardScrim: ["transparent", "rgba(11,18,32,0.75)"] as const,
  shimmer: ["#EEF1F5", "#F6F8FA", "#EEF1F5"] as const,
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
    shadowColor: "#0B1220",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.04,
    shadowRadius: 2,
    elevation: 1,
  },
  sm: {
    shadowColor: "#0B1220",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06,
    shadowRadius: 6,
    elevation: 2,
  },
  md: {
    shadowColor: "#0B1220",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.1,
    shadowRadius: 16,
    elevation: 5,
  },
  lg: {
    shadowColor: "#0B1220",
    shadowOffset: { width: 0, height: 12 },
    shadowOpacity: 0.16,
    shadowRadius: 28,
    elevation: 10,
  },
  gold: {
    shadowColor: "#F59E0B",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.35,
    shadowRadius: 14,
    elevation: 6,
  },
};

export const bookingStatus: Record<number, { en: string; es: string; color: string; bg: string }> = {
  0: { en: "Pending", es: "Pendiente", color: "#D97706", bg: "#FFFBEB" },
  1: { en: "Confirmed", es: "Confirmada", color: "#2563EB", bg: "#EFF6FF" },
  2: { en: "Cancelled", es: "Cancelada", color: "#DC2626", bg: "#FEF2F2" },
  3: { en: "Delivered", es: "Entregada", color: "#059669", bg: "#ECFDF5" },
  4: { en: "Returned", es: "Devuelta", color: "#64748B", bg: "#F1F5F9" },
};

export const carStatus: Record<number, { en: string; es: string; color: string; bg: string }> = {
  0: { en: "Available", es: "Disponible", color: "#059669", bg: "#ECFDF5" },
  1: { en: "In use", es: "En uso", color: "#D97706", bg: "#FFFBEB" },
  2: { en: "Maintenance", es: "Mantenimiento", color: "#2563EB", bg: "#EFF6FF" },
};
