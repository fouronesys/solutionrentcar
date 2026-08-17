// Utilidades de formato es-DO
export function fmtMoney(v: number | undefined | null, currency = "DOP"): string {
  if (v === undefined || v === null) return "—";
  try {
    return new Intl.NumberFormat("es-DO", { style: "currency", currency, maximumFractionDigits: 2 }).format(v);
  } catch {
    return `${currency} ${v.toFixed(2)}`;
  }
}

export function fmtDate(v: string | undefined | null): string {
  if (!v) return "—";
  const d = new Date(v.includes("T") || v.includes(" ") ? v.replace(" ", "T") : v + "T00:00:00");
  if (isNaN(d.getTime())) return v;
  return new Intl.DateTimeFormat("es-DO", { day: "2-digit", month: "short", year: "numeric" }).format(d);
}

export function fmtDateTime(v: string | undefined | null): string {
  if (!v) return "—";
  const d = new Date(v.replace(" ", "T"));
  if (isNaN(d.getTime())) return v;
  return new Intl.DateTimeFormat("es-DO", {
    day: "2-digit", month: "short", hour: "2-digit", minute: "2-digit",
  }).format(d);
}

export function errMsg(e: unknown): string {
  if (e instanceof Error) return e.message;
  return "Ocurrió un error inesperado";
}
