import { Router } from "express";
import fs from "node:fs";
import path from "node:path";
import { one, pool, q } from "../db.js";
import { requireAuth, resolveCompany, tryAuth, type AuthInfo } from "../auth.js";
import {
  DATE_ONLY_RE,
  bookingToArray,
  carToArray,
  decodeBase64File,
  err,
  h,
  ok,
  pageParams,
  personToArray,
  publicBase,
  toInt,
  toNum,
  toStr,
} from "../helpers.js";
import { EVENTS, notify, notifyCompanyStaff } from "../notify.js";
import { PRIVATE_DIR } from "../storage.js";

export const bookingsRouter = Router();
bookingsRouter.use(requireAuth());

function canView(a: AuthInfo, b: any): boolean {
  if (!b) return false;
  if (a.type === "client") return toInt(b.person_id) === a.id;
  if (a.stockId > 0) return toInt(b.stock_id) === a.stockId || a.stockId === 0;
  return true;
}

async function getBooking(companyId: number, id: number) {
  return one("SELECT * FROM bookings WHERE id=$1 AND company_id=$2", [id, companyId]);
}

// GET /bookings/:id
bookingsRouter.get("/:id(\\d+)", h(async (req, res) => {
  const a = tryAuth(req)!;
  const company = await resolveCompany(req, res);
  if (!company) return;
  const b = await getBooking(company.id, toInt(req.params.id));
  if (!b || !canView(a, b)) return err(res, "not_found", "Reserva no encontrada", 404);
  const car = b.car_id ? await one("SELECT * FROM cars WHERE id=$1 AND company_id=$2", [b.car_id, company.id]) : null;
  const person = b.person_id ? await one("SELECT * FROM persons WHERE id=$1 AND company_id=$2", [b.person_id, company.id]) : null;
  return ok(res, {
    booking: bookingToArray(req, b),
    car: car ? carToArray(req, car) : null,
    client: person ? personToArray(req, person) : null,
  });
}));

// GET /bookings
bookingsRouter.get("/", h(async (req, res) => {
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
  if (req.query.status !== undefined && req.query.status !== "") {
    vals.push(toInt(req.query.status));
    where.push(`status=$${vals.length}`);
  }
  const from = toStr(req.query.from).trim();
  if (from) {
    if (!DATE_ONLY_RE.test(from)) return err(res, "invalid_request", "from debe ser YYYY-MM-DD", 400);
    vals.push(from);
    where.push(`created_at::date >= $${vals.length}::date`);
  }
  const to = toStr(req.query.to).trim();
  if (to) {
    if (!DATE_ONLY_RE.test(to)) return err(res, "invalid_request", "to debe ser YYYY-MM-DD", 400);
    vals.push(to);
    where.push(`created_at::date <= $${vals.length}::date`);
  }
  if (a.type === "user") {
    if (req.query.client_id) {
      vals.push(toInt(req.query.client_id));
      where.push(`person_id=$${vals.length}`);
    }
    if (req.query.car_id) {
      vals.push(toInt(req.query.car_id));
      where.push(`car_id=$${vals.length}`);
    }
    if (req.query.q) {
      vals.push(`%${toStr(req.query.q)}%`);
      where.push(`(code ILIKE $${vals.length} OR person_id IN (
        SELECT id FROM persons WHERE company_id=$1 AND (name ILIKE $${vals.length} OR lastname ILIKE $${vals.length} OR phone ILIKE $${vals.length})))`);
    }
  }

  const { limit, offset } = pageParams(req);
  vals.push(limit, offset);
  const r = await q(
    `SELECT * FROM bookings WHERE ${where.join(" AND ")} ORDER BY id DESC LIMIT $${vals.length - 1} OFFSET $${vals.length}`,
    vals,
  );
  return ok(res, {
    bookings: r.rows.map((b) => bookingToArray(req, b)),
    limit,
    offset,
    count: r.rows.length,
  });
}));

// POST /bookings/:id/deliver | /return  (solo staff)
bookingsRouter.post("/:id(\\d+)/:sub(deliver|return)", h(async (req, res) => {
  const a = tryAuth(req)!;
  if (a.type !== "user") return err(res, "forbidden", "Solo staff", 403);
  const company = await resolveCompany(req, res);
  if (!company) return;
  const id = toInt(req.params.id);
  const b = await getBooking(company.id, id);
  if (!b || !canView(a, b)) return err(res, "not_found", "Reserva no encontrada", 404);
  const current = toInt(b.status);
  const sub = req.params.sub;

  let title: string, msg: string, evt: string;
  let updatedRow: any;
  if (sub === "deliver") {
    if (![0, 1].includes(current)) return err(res, "conflict", "La reserva no está en estado entregable", 409);
    // Update condicional: evita carreras que reviertan otro cambio de estado concurrente
    updatedRow = await one(
      "UPDATE bookings SET status=3 WHERE id=$1 AND company_id=$2 AND status IN (0,1) RETURNING *",
      [id, company.id],
    );
    if (!updatedRow) return err(res, "conflict", "La reserva cambió de estado, recarga e intenta de nuevo", 409);
    if (b.car_id) await q("UPDATE cars SET status=1 WHERE id=$1 AND company_id=$2", [b.car_id, company.id]);
    title = "Vehículo entregado";
    msg = `Tu reserva #${id} ha sido entregada. ¡Buen viaje!`;
    evt = EVENTS.BOOKING_DELIVERED;
  } else {
    if (current !== 3) return err(res, "conflict", "La reserva no está entregada", 409);
    updatedRow = await one(
      "UPDATE bookings SET status=4 WHERE id=$1 AND company_id=$2 AND status=3 RETURNING *",
      [id, company.id],
    );
    if (!updatedRow) return err(res, "conflict", "La reserva cambió de estado, recarga e intenta de nuevo", 409);
    if (b.car_id) await q("UPDATE cars SET status=0 WHERE id=$1 AND company_id=$2", [b.car_id, company.id]);
    title = "Vehículo devuelto";
    msg = `Hemos recibido el vehículo de tu reserva #${id}. Gracias.`;
    evt = EVENTS.BOOKING_RETURNED;
  }
  if (toInt(b.person_id) > 0) {
    await notify(company.id, "client", toInt(b.person_id), evt, title, msg, {
      booking_id: id,
      stock_id: toInt(b.stock_id),
    });
  }
  return ok(res, { booking: bookingToArray(req, updatedRow) });
}));

// POST /bookings/:id/sign
bookingsRouter.post("/:id(\\d+)/sign", h(async (req, res) => {
  const a = tryAuth(req)!;
  const company = await resolveCompany(req, res);
  if (!company) return;
  const id = toInt(req.params.id);
  const b = await getBooking(company.id, id);
  if (!b || !canView(a, b)) return err(res, "not_found", "Reserva no encontrada", 404);
  if (a.type === "client" && toInt(b.person_id) !== a.id) {
    return err(res, "forbidden", "No puedes firmar esta reserva", 403);
  }
  if ([2, 4].includes(toInt(b.status))) {
    return err(res, "conflict", "No se puede firmar una reserva cancelada o finalizada", 409);
  }
  const sig = toStr(req.body?.signature);
  if (!sig) return err(res, "invalid_request", "signature requerido (base64 PNG)", 400);
  const decoded = decodeBase64File(sig);
  if (!decoded) return err(res, "invalid_request", "Firma inválida", 400);

  const dir = path.join(PRIVATE_DIR, "companies", String(company.id), "firmas");
  fs.mkdirSync(dir, { recursive: true });
  const filename = `booking_${id}_${Date.now()}.png`;
  fs.writeFileSync(path.join(dir, filename), decoded.buf);
  const rel = `files/companies/${company.id}/firmas/${filename}`;

  const updated = await one(
    `UPDATE bookings SET signature=$1, status = CASE WHEN status=0 THEN 1 ELSE status END
     WHERE id=$2 AND company_id=$3 AND status NOT IN (2,4) RETURNING *`,
    [rel, id, company.id],
  );
  if (!updated) return err(res, "conflict", "La reserva cambió de estado, recarga e intenta de nuevo", 409);

  await notifyCompanyStaff(company.id, EVENTS.BOOKING_SIGNED, "Reserva firmada por cliente",
    `El cliente firmó la reserva #${id}.`, { booking_id: id, stock_id: toInt(b.stock_id) });

  return ok(res, {
    booking: bookingToArray(req, updated),
    signature_url: `${publicBase(req)}/${rel}`,
  });
}));

// POST /bookings/:id/cancel
bookingsRouter.post("/:id(\\d+)/cancel", h(async (req, res) => {
  const a = tryAuth(req)!;
  const company = await resolveCompany(req, res);
  if (!company) return;
  const id = toInt(req.params.id);
  const b = await getBooking(company.id, id);
  if (!b || !canView(a, b)) return err(res, "not_found", "Reserva no encontrada", 404);
  if (toInt(b.status) === 2) return err(res, "conflict", "Reserva ya cancelada", 409);
  const cancelled = await one(
    "UPDATE bookings SET status=2 WHERE id=$1 AND company_id=$2 AND status<>2 RETURNING *",
    [id, company.id],
  );
  if (!cancelled) return err(res, "conflict", "Reserva ya cancelada", 409);

  const carId = toInt(b.car_id);
  if (carId > 0) {
    const other = await one(
      `SELECT id FROM bookings WHERE company_id=$1 AND car_id=$2 AND id<>$3 AND status IN (0,1,3) LIMIT 1`,
      [company.id, carId, id],
    );
    if (!other) await q("UPDATE cars SET status=0 WHERE id=$1 AND company_id=$2", [carId, company.id]);
  }

  if (a.type === "client") {
    await notifyCompanyStaff(company.id, EVENTS.BOOKING_CANCELED, "Reserva cancelada por cliente",
      `Reserva #${id} fue cancelada por el cliente.`, { booking_id: id, stock_id: toInt(b.stock_id) });
  } else if (toInt(b.person_id) > 0) {
    await notify(company.id, "client", toInt(b.person_id), EVENTS.BOOKING_CANCELED,
      "Tu reserva fue cancelada", `La reserva #${id} fue cancelada.`,
      { booking_id: id, stock_id: toInt(b.stock_id) });
  }
  const updated = await getBooking(company.id, id);
  return ok(res, { booking: bookingToArray(req, updated) });
}));

// POST /bookings  (crear)
bookingsRouter.post("/", h(async (req, res) => {
  const a = tryAuth(req)!;
  const company = await resolveCompany(req, res);
  if (!company) return;
  const body = req.body ?? {};

  const carId = toInt(body.car_id);
  const startAt = toStr(body.start_at).trim();
  const endAt = toStr(body.end_at).trim();
  if (carId <= 0 || !startAt || !endAt) {
    return err(res, "invalid_request", "car_id, start_at y end_at son requeridos", 400);
  }
  const tsStart = Date.parse(startAt.replace(" ", "T"));
  const tsEnd = Date.parse(endAt.replace(" ", "T"));
  if (isNaN(tsStart) || isNaN(tsEnd)) {
    return err(res, "invalid_request", "start_at/end_at no son fechas válidas", 400);
  }
  if (tsEnd <= tsStart) return err(res, "invalid_request", "end_at debe ser posterior a start_at", 400);

  const car = await one("SELECT * FROM cars WHERE id=$1 AND company_id=$2", [carId, company.id]);
  if (!car) return err(res, "not_found", "Vehículo no encontrado", 404);

  let personId: number, stockId: number, userId: number;
  if (a.type === "client") {
    const guest = await one("SELECT is_guest FROM persons WHERE id=$1", [a.id]);
    if (guest?.is_guest) return err(res, "forbidden", "Inicia sesión para reservar", 403);
    personId = a.id;
    stockId = toInt(car.stock_id);
    userId = 0;
  } else {
    personId = toInt(body.person_id);
    if (personId <= 0) return err(res, "invalid_request", "person_id requerido", 400);
    const person = await one("SELECT id FROM persons WHERE id=$1 AND company_id=$2", [personId, company.id]);
    if (!person) return err(res, "invalid_request", "person_id no pertenece a esta empresa", 400);
    const authStock = a.stockId;
    if (body.stock_id !== undefined && body.stock_id !== "" && body.stock_id !== null) {
      stockId = toInt(body.stock_id);
    } else if (authStock > 0) {
      stockId = authStock;
    } else {
      stockId = toInt(car.stock_id);
    }
    if (authStock > 0) {
      if (stockId !== authStock) return err(res, "forbidden", "No puedes crear reservas fuera de tu sucursal", 403);
      if (toInt(car.stock_id) !== authStock) return err(res, "forbidden", "El vehículo pertenece a otra sucursal", 403);
    }
    userId = a.id;
  }

  const days = Math.max(1, Math.ceil((tsEnd - tsStart) / 86_400_000));
  let sure = toNum(body.sure);
  let price: number, total: number;
  if (a.type === "client") {
    price = toNum(car.price);
    if (sure < 0) sure = 0;
    total = price * days + sure;
  } else {
    price = body.price !== undefined ? toNum(body.price) : toNum(car.price);
    total = body.total !== undefined ? toNum(body.total) : price * days + sure;
  }

  // Transacción con lock consultivo por (empresa, vehículo): dos requests simultáneos
  // no pueden pasar ambos la verificación de solapamiento.
  const client = await pool.connect();
  let b: any;
  try {
    await client.query("BEGIN");
    await client.query("SELECT pg_advisory_xact_lock($1, $2)", [company.id, carId]);
    const overlap = await client.query(
      `SELECT id FROM bookings WHERE company_id=$1 AND car_id=$2 AND status IN (0,1,3)
       AND NOT (end_at < $3::timestamp OR start_at > $4::timestamp) LIMIT 1`,
      [company.id, carId, startAt, endAt],
    );
    if (overlap.rows.length) {
      await client.query("ROLLBACK");
      return err(res, "conflict", "El vehículo no está disponible en ese rango", 409);
    }
    const ins = await client.query(
      `INSERT INTO bookings (company_id, person_id, car_id, user_id, stock_id, start_at, end_at,
         place_start, place_end, price, total, xtotal, day, comment, status, fuel, deposit, sure, payment)
       VALUES ($1,$2,$3,$4,$5,$6::timestamp,$7::timestamp,$8,$9,$10,$11,$11,$12,$13,0,$14,$15,$16,0)
       RETURNING *`,
      [
        company.id, personId, carId, userId, stockId, startAt, endAt,
        toStr(body.place_start) || "No especificado",
        toStr(body.place_end) || "No especificado",
        price, total, String(days), toStr(body.comment), toStr(body.fuel), toNum(body.deposit), sure,
      ],
    );
    b = ins.rows[0];
    await client.query("UPDATE cars SET status=1 WHERE id=$1 AND company_id=$2", [carId, company.id]);
    await client.query("COMMIT");
  } catch (e) {
    await client.query("ROLLBACK").catch(() => {});
    throw e;
  } finally {
    client.release();
  }
  if (!b) return err(res, "server_error", "No se pudo crear la reserva", 500);

  const person = await one("SELECT name FROM persons WHERE id=$1", [personId]);
  await notifyCompanyStaff(company.id, EVENTS.BOOKING_CREATED, "Nueva reserva desde la app",
    `Cliente: ${toStr(person?.name)} — Reserva #${b.id}`,
    { booking_id: toInt(b.id), stock_id: stockId });
  await notify(company.id, "client", personId, EVENTS.BOOKING_CREATED, "Tu reserva fue creada",
    `Hemos registrado tu reserva #${b.id}. Te contactaremos pronto.`,
    { booking_id: toInt(b.id), stock_id: stockId });

  // booking_id acompaña a booking por compatibilidad con consumidores legados
  return ok(res, { booking: bookingToArray(req, b), booking_id: toInt(b.id) }, 201);
}));

bookingsRouter.all("*", (req, res) => err(res, "not_found", "Endpoint de reservas no encontrado", 404));
