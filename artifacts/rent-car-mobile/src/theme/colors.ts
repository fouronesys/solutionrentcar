export const colors = {
  primary: "#F59E0B",
  primaryDark: "#D97706",
  primaryLight: "#FDE68A",
  primaryXLight: "#FFFBEB",
  accent: "#1E293B",
  bg: "#FAFAFA",
  bgWarm: "#FFFDF7",
  card: "#FFFFFF",
  text: "#0F172A",
  textSecondary: "#475569",
  textMuted: "#94A3B8",
  border: "#E2E8F0",
  borderLight: "#F1F5F9",
  success: "#10B981",
  successBg: "#ECFDF5",
  warning: "#F59E0B",
  warningBg: "#FFFBEB",
  danger: "#EF4444",
  dangerBg: "#FEF2F2",
  info: "#3B82F6",
  infoBg: "#EFF6FF",
  dark: "#1E293B",
  darkCard: "#0F172A",
};

export const radius = { xs: 4, sm: 8, md: 12, lg: 16, xl: 24, full: 9999 };
export const spacing = { xs: 4, sm: 8, md: 12, lg: 16, xl: 24, xxl: 32, xxxl: 48 };

export const shadow = {
  sm: {
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
    elevation: 1,
  },
  md: {
    shadowColor: "#0F172A",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 8,
    elevation: 3,
  },
  lg: {
    shadowColor: "#0F172A",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.12,
    shadowRadius: 16,
    elevation: 6,
  },
  primary: {
    shadowColor: "#F59E0B",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 4,
  },
};

export const bookingStatus: Record<number, { en: string; es: string; color: string; bg: string }> = {
  0: { en: "Pending", es: "Pendiente", color: "#D97706", bg: "#FFFBEB" },
  1: { en: "Confirmed", es: "Confirmada", color: "#3B82F6", bg: "#EFF6FF" },
  2: { en: "Cancelled", es: "Cancelada", color: "#EF4444", bg: "#FEF2F2" },
  3: { en: "Delivered", es: "Entregada", color: "#10B981", bg: "#ECFDF5" },
  4: { en: "Returned", es: "Devuelta", color: "#64748B", bg: "#F1F5F9" },
};

export const carStatus: Record<number, { en: string; es: string; color: string; bg: string }> = {
  0: { en: "Available", es: "Disponible", color: "#10B981", bg: "#ECFDF5" },
  1: { en: "In use", es: "En uso", color: "#F59E0B", bg: "#FFFBEB" },
  2: { en: "Maintenance", es: "Mantenimiento", color: "#3B82F6", bg: "#EFF6FF" },
};
