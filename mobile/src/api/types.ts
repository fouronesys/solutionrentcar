export type ApiOk<T> = { ok: true; data: T };
export type ApiErr = { ok: false; error: { code: string; message: string } };
export type ApiResponse<T> = ApiOk<T> | ApiErr;

export type Tokens = {
  access_token: string;
  refresh_token: string;
  token_type: string;
  expires_in: number;
};

export type Role = "client" | "staff";

export type ClientDocuments = {
  cedula?: string | null;
  passport?: string | null;
  license?: string | null;
  home?: string | null;
};

export type Profile = {
  id: number;
  name?: string;
  lastname?: string;
  email?: string;
  phone?: string;
  phone2?: string;
  username?: string;
  stock_id?: number;
  kind?: number;
  language?: string;
  address?: string;
  address2?: string;
  nationality?: string;
  passport?: string;
  license?: string;
  image?: string;
  documents?: ClientDocuments;
};

export type Car = {
  id: number;
  name?: string;
  brand?: string;
  brand_id?: number;
  model?: string;
  year?: string | number;
  plate?: string;
  color?: string;
  seat?: string | number;
  transmission?: string;
  transmission_id?: number;
  fuel?: string;
  fuel_id?: number;
  category?: string;
  category_id?: number;
  price?: number | string;
  price_day?: number | string;
  status?: number;
  stock_id?: number;
  image?: string | null;
  images?: string[];
  description?: string;
};

export type Booking = {
  id: number;
  code?: string;
  car_id: number;
  person_id?: number;
  user_id?: number;
  stock_id?: number;
  start_at?: string;
  end_at?: string;
  day?: string | number;
  days?: number;
  price?: number | string;
  total?: number | string;
  payment?: number | string;
  status: number;
  place_start?: string;
  place_end?: string;
  comment?: string;
  signature?: string | null;
  has_signature?: boolean;
  created_at?: string;
};

export type BookingDetail = {
  booking: Booking;
  car: Car | null;
  client: Profile | null;
};

export type Payment = {
  id: number;
  booking_id: number;
  val: number | string;
  payment_type_id?: number;
  payment_type?: string;
  created_at?: string;
};

export type Notification = {
  id: number;
  recipient_type: "user" | "client";
  recipient_id: number;
  event_type: string;
  title: string;
  body: string;
  data?: Record<string, unknown> | string | null;
  read_at?: string | null;
  created_at: string;
};

export type Preference = {
  event_type: string;
  channel: "push" | "email" | "sms" | "whatsapp" | "in_app";
  enabled: boolean;
};

export type AgendaItem = {
  booking: Booking;
  car: Car | null;
  client: Profile | null;
};

export type Insurance = {
  id: number;
  name: string;
  price?: number | string;
};

export type Agenda = {
  date: string;
  deliveries: AgendaItem[];
  returns: AgendaItem[];
};
