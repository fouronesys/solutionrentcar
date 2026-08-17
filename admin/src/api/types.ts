// Tipos que devuelve la API multiempresa (server/, prefijo /api/v1)

export type Company = {
  id: number;
  slug: string;
  name: string;
  logo: string | null;
  color_primary: string;
  color_secondary: string;
  currency: string;
  phone: string;
  email: string;
  address: string;
  active: boolean;
};

export type StaffUser = {
  id: number;
  username?: string;
  name: string;
  lastname: string;
  email: string;
  phone: string;
  kind: number; // 0 = staff, 1 = admin de empresa
  status: number; // 1 activo, 0 inactivo
  image: string | null;
};

export type CatalogItem = { id: number; name: string };

export type Car = {
  id: number;
  name: string;
  brand?: string;
  brand_id?: number;
  year?: string;
  plate?: string;
  seat?: string;
  kms?: string;
  kms_current?: string;
  transmission?: string;
  transmission_id?: number;
  fuel?: string;
  fuel_id?: number;
  category?: string;
  category_id?: number;
  stock_id?: number;
  price?: number;
  status?: number; // 0 disponible, 1 ocupado (reservado/rentado)
  image?: string | null;
  images?: string[];
  description?: string;
};

export type Booking = {
  id: number;
  code?: string;
  car_id: number;
  car?: Car | null;
  person_id?: number;
  client?: { id: number; name: string; lastname: string; phone: string } | null;
  user_id?: number;
  stock_id?: number;
  start_at?: string;
  end_at?: string;
  day?: string | number;
  price?: number;
  total?: number;
  payment?: number;
  status: number; // 0 reservada, 1 firmada, 3 entregada, 4 devuelta, 2 cancelada
  car_name?: string; // solo en el listado
  client_name?: string; // solo en el listado
  place_start?: string;
  place_end?: string;
  comment?: string;
  signature?: string | null;
  has_signature?: boolean;
  created_at?: string;
};

export type LoginResponse = {
  role: "staff" | "person" | "super";
  is_super?: boolean;
  user?: StaffUser;
  tokens: { access_token: string; refresh_token: string; expires_in: number };
};

export type Session = {
  accessToken: string;
  refreshToken: string;
  role: "staff" | "super";
  isSuper: boolean;
  user: StaffUser | null;
  companySlug: string | null; // slug usado al iniciar sesión (staff de empresa)
};
