import { Router } from "express";
import { one, pool, q } from "../db.js";
import { requireAuth, resolveCompany, tryAuth } from "../auth.js";
import {
  DATE_ONLY_RE,
  bookingToArray,
  carToArray,
  err,
  h,
  notificationToArray,
  ok,
  pageParams,
  paymentToArray,
  personToArray,
  toInt,
  toNum,
  toStr,
} from "../helpers.js";

// ---------------- agenda ----------------

export const agendaRouter = Router();
agendaRouter.get("/", requireAuth("user"), h(async (req, res) => {
  const a = tryAuth(req)!;
  const company = await resolveCompany(req, res);
  if (!company) return;
  const date = toStr(req.query.date).trim() || new Date().toISOString().slice(0, 10);
  if (!DATE_ONLY_RE.test(date)) return err(res, "invalid_request", "date debe ser YYYY-MM-DD", 400);

  const stockFilter = a.stockId > 0 ? " AND stock_id=$3" : "";
  const baseVals: unknown[] = [company.id, date];
  if (a.stockId > 0) baseVals.push(a.stockId);

  const deliveries = await q(
    `SELECT * FROM bookings WHERE company_id=$1 AND status IN (0,1) AND start_at::date=$2::date${stockFilter} ORDER BY start_at ASC`,
    baseVals,
  );
  const returns = await q(
    `SELECT * FROM bookings WHERE company_id=$1 AND status=3 AND end_at::date=$2::date${stockFilter} ORDER BY end_at ASC`,
    baseVals,
  );

  async function entry(b: any) {
    const car = b.car_id ? await one("SELECT * FROM cars WHERE id=$1 AND company_id=$2", [b.car_id, company!.id]) : null;
    const client = b.person_id ? await one("SELECT * FROM persons WHERE id=$1 AND company_id=$2", [b.person_id, company!.id]) : null;
    return {
      booking: bookingToArray(req, b),
      car: car ? carToArray(req, car) : null,
      client: client ? personToArray(req, client) : null,
    };
  }

  return ok(res, {
    date,
    deliveries: await Promise.all(deliveries.rows.map(entry)),
    returns: await Promise.all(returns.rows.map(entry)),
  });
}));
agendaRouter.all("/", (req, res) => err(res, "method_not_allowed", "Use GET", 405));

// ---------------- notifications ----------------

export const notificationsRouter = Router();
notificationsRouter.use(requireAuth());

notificationsRouter.get("/unread_count", h(async (req, res) => {
  const a = tryAuth(req)!;
  const r = await one(
    `SELECT COUNT(*)::int AS c FROM notifications
     WHERE recipient_type=$1 AND recipient_id=$2 AND read_at IS NULL AND ($3::int IS NULL OR company_id=$3)`,
    [a.type === "user" ? "user" : "client", a.id, a.companyId],
  );
  return ok(res, { unread: toInt(r?.c) });
}));

notificationsRouter.post("/read_all", h(async (req, res) => {
  const a = tryAuth(req)!;
  await q(
    `UPDATE notifications SET read_at=NOW()
     WHERE recipient_type=$1 AND recipient_id=$2 AND read_at IS NULL AND ($3::int IS NULL OR company_id=$3)`,
    [a.type === "user" ? "user" : "client", a.id, a.companyId],
  );
  return ok(res, { marked: true });
}));

notificationsRouter.post("/:id(\\d+)/read", h(async (req, res) => {
  const a = tryAuth(req)!;
  await q(
    `UPDATE notifications SET read_at=NOW()
     WHERE id=$1 AND recipient_type=$2 AND recipient_id=$3 AND ($4::int IS NULL OR company_id=$4)`,
    [toInt(req.params.id), a.type === "user" ? "user" : "client", a.id, a.companyId],
  );
  return ok(res, { marked: true });
}));

async function getPreferences(req: any, res: any): Promise<void> {
  const a = tryAuth(req)!;
  const r = await q(
    `SELECT event_type, channel, enabled FROM notification_preferences
     WHERE recipient_type=$1 AND recipient_id=$2 AND ($3::int IS NULL OR company_id=$3)`,
    [a.type === "user" ? "user" : "client", a.id, a.companyId],
  );
  return ok(res, { preferences: r.rows.map((p) => ({ event_type: p.event_type, channel: p.channel, enabled: !!p.enabled })) });
}

async function putPreference(req: any, res: any): Promise<void> {
  const a = tryAuth(req)!;
  const eventType = toStr(req.body?.event_type).trim();
  const channel = toStr(req.body?.channel).trim();
  if (!eventType || !channel || req.body?.enabled === undefined) {
    return err(res, "invalid_request", "event_type, channel y enabled son requeridos", 400);
  }
  const enabled = !!req.body.enabled && req.body.enabled !== "0" && req.body.enabled !== "false";
  await q(
    `INSERT INTO notification_preferences (company_id, recipient_type, recipient_id, event_type, channel, enabled)
     VALUES (COALESCE($1, 0), $2, $3, $4, $5, $6)
     ON CONFLICT (company_id, recipient_type, recipient_id, event_type, channel)
     DO UPDATE SET enabled=EXCLUDED.enabled`,
    [a.companyId ?? 0, a.type === "user" ? "user" : "client", a.id, eventType, channel, enabled],
  );
  return ok(res, { updated: true, preference: { event_type: eventType, channel, enabled } });
}

notificationsRouter.get("/preferences", h(getPreferences));
notificationsRouter.put("/preferences", h(putPreference));
notificationsRouter.post("/preferences", h(putPreference));

notificationsRouter.get(["/", "/:id(\\d+)"], h(async (req, res) => {
  const a = tryAuth(req)!;
  const where: string[] = ["recipient_type=$1", "recipient_id=$2", "($3::int IS NULL OR company_id=$3)"];
  const vals: unknown[] = [a.type === "user" ? "user" : "client", a.id, a.companyId];

  const filter = toStr(req.query.filter).trim() || "all";
  if (filter === "unread") where.push("read_at IS NULL");
  if (filter === "read") where.push("read_at IS NOT NULL");
  if (req.query.event_type) {
    vals.push(toStr(req.query.event_type));
    where.push(`type=$${vals.length}`);
  }
  if (req.query.from && DATE_ONLY_RE.test(toStr(req.query.from))) {
    vals.push(toStr(req.query.from));
    where.push(`created_at::date >= $${vals.length}::date`);
  }
  if (req.query.to && DATE_ONLY_RE.test(toStr(req.query.to))) {
    vals.push(toStr(req.query.to));
    where.push(`created_at::date <= $${vals.length}::date`);
  }

  const page = Math.max(1, toInt(req.query.page ?? 1));
  const perPage = Math.max(1, Math.min(100, toInt(req.query.per_page ?? 20)));
  const total = await one(`SELECT COUNT(*)::int AS c FROM notifications WHERE ${where.join(" AND ")}`, vals);
  vals.push(perPage, (page - 1) * perPage);
  const r = await q(
    `SELECT * FROM notifications WHERE ${where.join(" AND ")} ORDER BY id DESC LIMIT $${vals.length - 1} OFFSET $${vals.length}`,
    vals,
  );
  return ok(res, {
    notifications: r.rows.map(notificationToArray),
    total: toInt(total?.c),
    page,
    per_page: perPage,
  });
}));

notificationsRouter.all("*", (req, res) => err(res, "not_found", "Endpoint de notificaciones no encontrado", 404));

// ---------------- preferences (recurso raíz) ----------------

export const preferencesRouter = Router();
preferencesRouter.use(requireAuth());
preferencesRouter.get("/", h(getPreferences));
preferencesRouter.put("/", h(async (req, res) => putPreference(req, res)));
preferencesRouter.post("/", h(async (req, res) => putPreference(req, res)));
preferencesRouter.patch("/", h(async (req, res) => putPreference(req, res)));
preferencesRouter.all("/", (req, res) => err(res, "method_not_allowed", "Método no permitido", 405));

// ---------------- payments ----------------

export const paymentsRouter = Router();
paymentsRouter.use(requireAuth());

paymentsRouter.get("/", h(async (req, res) => {
  const a = tryAuth(req)!;
  const company = await resolveCompany(req, res);
  if (!company) return;
  const where: string[] = ["company_id=$1"];
  const vals: unknown[] = [company.id];
  if (a.type === "client") {
    vals.push(a.id);
    where.push(`person_id=$${vals.length}`);
  } else if (a.stockId > 0) {
    vals.push(a.stockId);
    where.push(`stock_id=$${vals.length}`);
  }
  if (req.query.booking_id) {
    vals.push(toInt(req.query.booking_id));
    where.push(`booking_id=$${vals.length}`);
  }
  const { limit, offset } = pageParams(req);
  vals.push(limit, offset);
  const r = await q(
    `SELECT * FROM payments WHERE ${where.join(" AND ")} ORDER BY id DESC LIMIT $${vals.length - 1} OFFSET $${vals.length}`,
    vals,
  );
  return ok(res, { payments: r.rows.map(paymentToArray), limit, offset, count: r.rows.length });
}));

paymentsRouter.post("/", h(async (req, res) => {
  const a = tryAuth(req)!;
  if (a.type !== "user") return err(res, "forbidden", "Solo staff", 403);
  const company = await resolveCompany(req, res);
  if (!company) return;
  const bookingId = toInt(req.body?.booking_id);
  const val = toNum(req.body?.val);
  let paymentTypeId = toInt(req.body?.payment_type_id);
  if (paymentTypeId <= 0) paymentTypeId = 1;
  if (bookingId <= 0 || val <= 0) return err(res, "invalid_request", "booking_id y val (>0) son requeridos", 400);

  const b = await one("SELECT * FROM bookings WHERE id=$1 AND company_id=$2", [bookingId, company.id]);
  if (!b) return err(res, "not_found", "Reserva no encontrada", 404);
  if (a.stockId > 0 && toInt(b.stock_id) !== a.stockId) return err(res, "forbidden", "Reserva de otra sucursal", 403);

  const p = await one(
    `INSERT INTO payments (company_id, booking_id, person_id, stock_id, val, payment_type_id)
     VALUES ($1,$2,$3,$4,$5,$6) RETURNING *`,
    [company.id, bookingId, toInt(b.person_id), toInt(b.stock_id), val, paymentTypeId],
  );
  await q("UPDATE bookings SET payment = payment + $1 WHERE id=$2", [val, bookingId]);
  return ok(res, {
    payment: {
      id: toInt(p!.id),
      booking_id: bookingId,
      person_id: toInt(b.person_id),
      stock_id: toInt(b.stock_id),
      val,
      payment_type_id: paymentTypeId,
    },
  }, 201);
}));

paymentsRouter.all("/", (req, res) => err(res, "method_not_allowed", "Método no permitido", 405));

// ---------------- push ----------------

export const pushRouter = Router();
pushRouter.use(requireAuth());

pushRouter.post("/register", h(async (req, res) => {
  const a = tryAuth(req)!;
  const company = await resolveCompany(req, res);
  if (!company) return;
  const token = toStr(req.body?.token).trim();
  if (!token) return err(res, "invalid_request", "token requerido", 400);
  await q(
    `INSERT INTO device_tokens (company_id, recipient_type, recipient_id, token, platform, app_version, device_info)
     VALUES ($1,$2,$3,$4,$5,$6,$7)
     ON CONFLICT (token) DO UPDATE SET
       company_id=EXCLUDED.company_id, recipient_type=EXCLUDED.recipient_type,
       recipient_id=EXCLUDED.recipient_id, platform=EXCLUDED.platform,
       app_version=EXCLUDED.app_version, device_info=EXCLUDED.device_info, updated_at=NOW()`,
    [
      company.id, a.type === "user" ? "user" : "client", a.id, token,
      toStr(req.body?.platform) || "expo", toStr(req.body?.app_version), toStr(req.body?.device_info),
    ],
  );
  return ok(res, { registered: true });
}));

async function deleteToken(req: any, res: any, token: string): Promise<void> {
  if (!token) return err(res, "invalid_request", "token requerido", 400);
  const a = tryAuth(req)!;
  // Solo el propio destinatario puede eliminar su token
  await q(
    "DELETE FROM device_tokens WHERE token=$1 AND recipient_type=$2 AND recipient_id=$3",
    [token, a.type === "user" ? "user" : "client", a.id],
  );
  return ok(res, { deleted: true });
}
pushRouter.delete("/token/:token", h(async (req, res) => deleteToken(req, res, toStr(req.params.token))));
pushRouter.delete("/token", h(async (req, res) => deleteToken(req, res, toStr(req.body?.token).trim())));
pushRouter.all("*", (req, res) => err(res, "not_found", "Endpoint push no encontrado", 404));

// ---------------- catalog ----------------

export const CATALOG_KINDS = [
  "brands", "categories", "transmissions", "fuels", "colors", "locations", "stocks", "insurances",
] as const;

export const catalogRouter = Router();
catalogRouter.get("/:kind", requireAuth(), h(async (req, res) => {
  const kind = toStr(req.params.kind).toLowerCase();
  if (!(CATALOG_KINDS as readonly string[]).includes(kind)) {
    return err(res, "not_found", `Catálogo '${kind}' no encontrado`, 404);
  }
  const company = await resolveCompany(req, res);
  if (!company) return;
  const r = await q(
    "SELECT id, name FROM catalog_items WHERE company_id=$1 AND kind=$2 ORDER BY name ASC",
    [company.id, kind],
  );
  return ok(res, { [kind]: r.rows.map((x) => ({ id: toInt(x.id), name: toStr(x.name) })) });
}));
catalogRouter.all("/:kind", (req, res) => err(res, "method_not_allowed", "Use GET", 405));

// ---------------- health ----------------

export const healthRouter = Router();
healthRouter.get("/", h(async (_req, res) => {
  let db = false;
  try {
    await pool.query("SELECT 1");
    db = true;
  } catch {
    db = false;
  }
  return ok(res, { status: "ok", db, time: new Date().toISOString() });
}));
