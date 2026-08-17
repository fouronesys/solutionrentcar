// Funciones tipadas contra la API multiempresa. Todas devuelven data del envelope.
// Las funciones de admin de empresa aceptan companyId opcional: solo lo usa el
// super admin (se envía como ?company_id=); el staff de empresa lo omite.
import { request, getSession, setSession } from "./client";
import type { Booking, Car, CatalogItem, Company, LoginResponse, StaffUser } from "./types";

const cq = (companyId?: number) => (companyId ? `?company_id=${companyId}` : "");
const cqa = (companyId?: number) => (companyId ? `&company_id=${companyId}` : "");

// ---------- Auth ----------
/** Login de staff/super. `company` = slug de la empresa (vacío para super admin). */
export async function login(username: string, password: string, company: string | null): Promise<LoginResponse> {
  const data = await request<LoginResponse>("/auth/login", {
    method: "POST",
    body: { username, password },
    auth: false,
    company,
  });
  setSession({
    accessToken: data.tokens.access_token,
    refreshToken: data.tokens.refresh_token,
    role: data.is_super ? "super" : "staff",
    isSuper: !!data.is_super,
    user: data.user ?? null,
    companySlug: data.is_super ? null : company,
  });
  return data;
}

export async function logout(): Promise<void> {
  const s = getSession();
  try {
    if (s) await request("/auth/logout", { method: "POST", body: { refresh_token: s.refreshToken } });
  } finally {
    setSession(null);
  }
}

// ---------- Super admin: empresas ----------
export type CompanyInput = Partial<{
  slug: string; name: string; color_primary: string; color_secondary: string;
  currency: string; phone: string; email: string; address: string;
  active: boolean; logo: string; // logo = base64 data URL
}>;

export const listCompanies = () =>
  request<{ companies: Company[] }>("/admin/companies").then((d) => d.companies);
export const getCompany = (id: number) =>
  request<{ company: Company }>(`/admin/companies/${id}`).then((d) => d.company);
export const createCompany = (input: CompanyInput) =>
  request<{ company: Company }>("/admin/companies", { method: "POST", body: input }).then((d) => d.company);
export const updateCompany = (id: number, input: CompanyInput) =>
  request<{ company: Company }>(`/admin/companies/${id}`, { method: "PATCH", body: input }).then((d) => d.company);
/** Eliminación definitiva: requiere confirmar con el slug exacto de la empresa. */
export const deleteCompany = (id: number, confirmSlug: string) =>
  request<{ deleted: boolean }>(`/admin/companies/${id}?confirm=${encodeURIComponent(confirmSlug)}`, { method: "DELETE" });

export const uploadCompanyLogo = (id: number, logoBase64: string) =>
  request<{ company: Company }>(`/admin/companies/${id}/logo`, { method: "POST", body: { logo: logoBase64 } }).then((d) => d.company);

// ---------- Empresa propia (staff) ----------
/** Datos/branding de la empresa del usuario autenticado (o de company_id si es super). */
export const getMyCompany = (companyId?: number) =>
  request<{ company: Company }>(`/admin/company${cq(companyId)}`).then((d) => d.company);

// ---------- Personal (staff de empresa) ----------
export type StaffInput = Partial<{
  username: string; password: string; name: string; lastname: string;
  email: string; phone: string; kind: number; status: number;
}>;

export const listStaff = (companyId?: number) =>
  request<{ users: StaffUser[] }>(`/admin/users${cq(companyId)}`).then((d) => d.users);
export const createStaff = (input: StaffInput, companyId?: number) =>
  request<{ user: StaffUser }>(`/admin/users${cq(companyId)}`, { method: "POST", body: input }).then((d) => d.user);
export const updateStaff = (id: number, input: StaffInput, companyId?: number) =>
  request<{ user: StaffUser }>(`/admin/users/${id}${cq(companyId)}`, { method: "PATCH", body: input }).then((d) => d.user);

// ---------- Flota ----------
export type CarInput = Partial<{
  name: string; year: string; plate: string; seat: string; kms: string; kms_current: string;
  description: string; price: number; status: number;
  brand_id: number; category_id: number; transmission_id: number; fuel_id: number; stock_id: number;
}>;

export const listCars = (companyId?: number) =>
  request<{ cars: Car[] }>(`/cars?limit=200${cqa(companyId)}`).then((d) => d.cars);
export const getCar = (id: number, companyId?: number) =>
  request<{ car: Car }>(`/cars/${id}${cq(companyId)}`).then((d) => d.car);
export const createCar = (input: CarInput, companyId?: number) =>
  request<{ car: Car }>(`/admin/cars${cq(companyId)}`, { method: "POST", body: input }).then((d) => d.car);
export const updateCar = (id: number, input: CarInput, companyId?: number) =>
  request<{ car: Car }>(`/admin/cars/${id}${cq(companyId)}`, { method: "PATCH", body: input }).then((d) => d.car);
export const deleteCar = (id: number, companyId?: number) =>
  request<{ deleted: boolean }>(`/admin/cars/${id}${cq(companyId)}`, { method: "DELETE" });
/** Reemplaza la galería del vehículo. `images`: base64 data URLs o URLs http existentes. */
export const setCarImages = (id: number, images: string[], companyId?: number) =>
  request<{ car: Car }>(`/admin/cars/${id}/images${cq(companyId)}`, { method: "POST", body: { images } }).then((d) => d.car);

// ---------- Catálogos ----------
export const CATALOG_KINDS = [
  "brands", "categories", "transmissions", "fuels", "colors", "locations", "stocks", "insurances",
] as const;
export type CatalogKind = (typeof CATALOG_KINDS)[number];

export const CATALOG_LABELS: Record<CatalogKind, string> = {
  brands: "Marcas", categories: "Categorías", transmissions: "Transmisiones", fuels: "Combustibles",
  colors: "Colores", locations: "Ubicaciones", stocks: "Sucursales", insurances: "Seguros",
};

export const listCatalog = (kind: CatalogKind, companyId?: number) =>
  request<Record<string, CatalogItem[]>>(`/admin/catalog/${kind}${cq(companyId)}`).then((d) => d[kind] ?? []);
export const addCatalogItem = (kind: CatalogKind, name: string, companyId?: number) =>
  request<{ item: CatalogItem }>(`/admin/catalog/${kind}${cq(companyId)}`, { method: "POST", body: { name } }).then((d) => d.item);
export const updateCatalogItem = (kind: CatalogKind, id: number, name: string, companyId?: number) =>
  request<{ item: CatalogItem }>(`/admin/catalog/${kind}/${id}${cq(companyId)}`, { method: "PATCH", body: { name } }).then((d) => d.item);
export const deleteCatalogItem = (kind: CatalogKind, id: number, companyId?: number) =>
  request<{ deleted: boolean }>(`/admin/catalog/${kind}/${id}${cq(companyId)}`, { method: "DELETE" });

// ---------- Reservas ----------
// Estados reales del backend: 0 reservada, 1 firmada, 3 entregada, 4 devuelta, 2 cancelada
export const BOOKING_STATUS: Record<number, string> = {
  0: "Reservada", 1: "Firmada", 2: "Cancelada", 3: "Entregada", 4: "Devuelta",
};

export const listBookings = (opts: { status?: number; limit?: number } = {}) => {
  const p = new URLSearchParams({ limit: String(opts.limit ?? 100) });
  if (opts.status !== undefined) p.set("status", String(opts.status));
  return request<{ bookings: Booking[] }>(`/bookings?${p}`).then((d) => d.bookings);
};
// El detalle devuelve booking, car y client por separado; los fusionamos
export const getBooking = (id: number) =>
  request<{ booking: Booking; car: Booking["car"]; client: Booking["client"] }>(`/bookings/${id}`)
    .then((d) => ({ ...d.booking, car: d.car ?? null, client: d.client ?? null }));
export const deliverBooking = (id: number, body: { fuel?: string; kms?: string; comment?: string } = {}) =>
  request<{ booking: Booking }>(`/bookings/${id}/deliver`, { method: "POST", body }).then((d) => d.booking);
export const returnBooking = (id: number, body: { fuel?: string; kms?: string; comment?: string } = {}) =>
  request<{ booking: Booking }>(`/bookings/${id}/return`, { method: "POST", body }).then((d) => d.booking);
export const cancelBooking = (id: number) =>
  request<{ booking: Booking }>(`/bookings/${id}/cancel`, { method: "POST" }).then((d) => d.booking);
