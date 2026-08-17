import { Router } from "express";
import { one, q } from "../db.js";
import { requireAuth, resolveCompany, tryAuth } from "../auth.js";
import { DATE_RE, carToArray, err, h, ok, pageParams, toInt, toStr } from "../helpers.js";

export const carsRouter = Router();
carsRouter.use(requireAuth());

// GET /cars/:id/availability
carsRouter.get("/:id(\\d+)/availability", h(async (req, res) => {
  const company = await resolveCompany(req, res);
  if (!company) return;
  const id = toInt(req.params.id);
  const car = await one("SELECT id FROM cars WHERE id=$1 AND company_id=$2", [id, company.id]);
  if (!car) return err(res, "not_found", "Vehículo no encontrado", 404);

  let from = toStr(req.query.from).trim();
  let to = toStr(req.query.to).trim();
  if (!from) from = new Date().toISOString().slice(0, 10);
  if (!to) {
    const d = new Date();
    d.setMonth(d.getMonth() + 12);
    to = d.toISOString().slice(0, 10);
  }
  if (!DATE_RE.test(from) || !DATE_RE.test(to)) {
    return err(res, "invalid_request", "from/to deben ser YYYY-MM-DD o YYYY-MM-DD HH:MM[:SS]", 400);
  }

  const r = await q(
    `SELECT start_at, end_at FROM bookings
     WHERE company_id=$1 AND car_id=$2 AND status IN (0,1,3)
     AND NOT (end_at < $3::timestamp OR start_at > $4::timestamp)
     ORDER BY start_at ASC`,
    [company.id, id, from, to],
  );
  return ok(res, {
    car_id: id,
    from,
    to,
    busy: r.rows.map((row) => ({
      start_at: toStr(row.start_at instanceof Date ? row.start_at.toISOString().slice(0, 19).replace("T", " ") : row.start_at),
      end_at: toStr(row.end_at instanceof Date ? row.end_at.toISOString().slice(0, 19).replace("T", " ") : row.end_at),
    })),
  });
}));

// GET /cars/:id
carsRouter.get("/:id(\\d+)", h(async (req, res) => {
  const company = await resolveCompany(req, res);
  if (!company) return;
  const car = await one(
    `SELECT c.*, br.name AS brand_name, cat.name AS category_name,
            tr.name AS transmission_name, fu.name AS fuel_name
     FROM cars c
     LEFT JOIN catalog_items br  ON br.id  = c.brand_id        AND br.company_id  = c.company_id
     LEFT JOIN catalog_items cat ON cat.id = c.category_id     AND cat.company_id = c.company_id
     LEFT JOIN catalog_items tr  ON tr.id  = c.transmission_id AND tr.company_id  = c.company_id
     LEFT JOIN catalog_items fu  ON fu.id  = c.fuel_id         AND fu.company_id  = c.company_id
     WHERE c.id=$1 AND c.company_id=$2`,
    [toInt(req.params.id), company.id],
  );
  if (!car) return err(res, "not_found", "Vehículo no encontrado", 404);
  return ok(res, { car: carToArray(req, car) });
}));

// GET /cars
carsRouter.get("/", h(async (req, res) => {
  const company = await resolveCompany(req, res);
  if (!company) return;

  const where: string[] = ["c.company_id=$1"];
  const vals: unknown[] = [company.id];

  if (req.query.stock_id) {
    vals.push(toInt(req.query.stock_id));
    where.push(`c.stock_id=$${vals.length}`);
  }
  if (req.query.status !== undefined && req.query.status !== "") {
    vals.push(toInt(req.query.status));
    where.push(`c.status=$${vals.length}`);
  }
  if (req.query.q) {
    vals.push(`%${toStr(req.query.q)}%`);
    where.push(`(c.name ILIKE $${vals.length} OR c.year ILIKE $${vals.length} OR c.plate ILIKE $${vals.length})`);
  }

  const from = toStr(req.query.available_from).trim();
  const to = toStr(req.query.available_to).trim();
  if (from && to) {
    if (!DATE_RE.test(from) || !DATE_RE.test(to)) {
      return err(res, "invalid_request", "available_from y available_to deben ser YYYY-MM-DD o YYYY-MM-DD HH:MM[:SS]", 400);
    }
    vals.push(from);
    const fromIdx = vals.length;
    vals.push(to);
    where.push(`c.id NOT IN (
      SELECT car_id FROM bookings WHERE company_id=$1 AND status IN (0,1,3)
      AND NOT (end_at < $${fromIdx}::timestamp OR start_at > $${vals.length}::timestamp))`);
  }

  const { limit, offset } = pageParams(req);
  vals.push(limit, offset);
  const r = await q(
    `SELECT c.*, br.name AS brand_name, cat.name AS category_name,
            tr.name AS transmission_name, fu.name AS fuel_name
     FROM cars c
     LEFT JOIN catalog_items br  ON br.id  = c.brand_id        AND br.company_id  = c.company_id
     LEFT JOIN catalog_items cat ON cat.id = c.category_id     AND cat.company_id = c.company_id
     LEFT JOIN catalog_items tr  ON tr.id  = c.transmission_id AND tr.company_id  = c.company_id
     LEFT JOIN catalog_items fu  ON fu.id  = c.fuel_id         AND fu.company_id  = c.company_id
     WHERE ${where.join(" AND ")} ORDER BY c.id DESC LIMIT $${vals.length - 1} OFFSET $${vals.length}`,
    vals,
  );
  return ok(res, {
    cars: r.rows.map((c) => carToArray(req, c)),
    limit,
    offset,
    count: r.rows.length,
  });
}));
