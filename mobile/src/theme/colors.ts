export const colors = {
  primary: "#0E2A47",
  primaryDark: "#091B2E",
  primaryLight: "#1B4475",
  accent: "#F2A900",
  bg: "#F5F7FA",
  card: "#FFFFFF",
  text: "#1A1A1A",
  textMuted: "#6B7280",
  border: "#E5E7EB",
  success: "#16A34A",
  warning: "#F59E0B",
  danger: "#DC2626",
  info: "#0284C7",
};

export const radius = { sm: 6, md: 10, lg: 16, xl: 24 };
export const spacing = { xs: 4, sm: 8, md: 12, lg: 16, xl: 24, xxl: 32 };

export const bookingStatus: Record<number, { en: string; es: string; color: string }> = {
  0: { en: "Pending", es: "Pendiente", color: colors.warning },
  1: { en: "Confirmed", es: "Confirmada", color: colors.info },
  2: { en: "Cancelled", es: "Cancelada", color: colors.danger },
  3: { en: "Delivered", es: "Entregada", color: colors.success },
  4: { en: "Returned", es: "Devuelta", color: colors.textMuted },
};

export const carStatus: Record<number, { en: string; es: string; color: string }> = {
  0: { en: "Available", es: "Disponible", color: colors.success },
  1: { en: "In use", es: "En uso", color: colors.warning },
  2: { en: "Maintenance", es: "Mantenimiento", color: colors.info },
};
