/**
 * Migra los datos del sistema legado CF-SYSTEMS (MySQL) hacia la plataforma
 * multi-empresa (PostgreSQL), asignándolos a UNA empresa (por defecto "yowell").
 *
 * - SOLO LECTURA sobre MySQL.
 * - Toda la escritura en PostgreSQL ocurre dentro de UNA transacción:
 *   si algo falla, no queda migración parcial.
 * - Si la empresa destino ya tiene datos (en cualquier tabla), aborta salvo --force,
 *   que borra los datos de ESA empresa antes de importar (dentro de la transacción).
 *
 * Esquema legado real (ver core/app/model/*.php):
 *   - vehículos:  tabla `cars` (imagen principal en cars.invoice_file)
 *   - galería:    tabla `galery` (car_id, invoice_file)
 *   - reservas:   tabla `booking` (firma en booking.firma)
 *   - clientes:   tabla `person` (docs: invoice_file=cédula, passport_file, license_file, home_file)
 *   - staff:      tabla `user` (password = sha1(md5(...)))
 *   - pagos:      tabla `payment`
 *   - catálogos:  brand, category, transmission, fuel, color, location, stock, insurance
 *
 * Variables de entorno requeridas:
 *   LEGACY_DB_HOST, LEGACY_DB_USER, LEGACY_DB_PASSWORD, LEGACY_DB_NAME
 *   (opcional) LEGACY_DB_PORT=3306
 *   (opcional) COMPANY_SLUG=yowell  COMPANY_NAME="Yowell Rent-Car"
 *   (opcional) LEGACY_BASE_URL=https://solutionsrentcar.do  (para absolutizar imágenes)
 */
import mysql from "mysql2/promise";
import bcrypt from "bcryptjs";
import { pool } from "../src/db.js";

const SLUG = process.env.COMPANY_SLUG || "yowell";
const NAME = process.env.COMPANY_NAME || "Yowell Rent-Car";
const BASE = (process.env.LEGACY_BASE_URL || "https://solutionsrentcar.do").replace(/\/+$/, "");
const FORCE = process.argv.includes("--force");

function absUrl(v: unknown): string | null {
  const s = String(v ?? "").trim();
  if (!s) return null;
  if (/^https?:\/\//i.test(s)) return s;
  return `${BASE}/${s.replace(/^\/+/, "")}`;
}

function digits(v: unknown): string {
  return String(v ?? "").replace(/\D+/g, "");
}

async function main() {
  for (const k of ["LEGACY_DB_HOST", "LEGACY_DB_USER", "LEGACY_DB_PASSWORD", "LEGACY_DB_NAME"]) {
    if (!process.env[k]) throw new Error(`Falta variable de entorno ${k}`);
  }
  const my = await mysql.createConnection({
    host: process.env.LEGACY_DB_HOST,
    port: parseInt(process.env.LEGACY_DB_PORT || "3306", 10),
    user: process.env.LEGACY_DB_USER,
    password: process.env.LEGACY_DB_PASSWORD,
    database: process.env.LEGACY_DB_NAME,
    connectTimeout: 20000,
  });
  console.log("Conectado a MySQL legado (solo lectura).");

  const rows = async (sql: string, params: unknown[] = []): Promise<any[]> => {
    const [r] = await my.query(sql, params);
    return r as any[];
  };
  const tableExists = async (name: string): Promise<boolean> => {
    const r = await rows("SHOW TABLES LIKE ?", [name]);
    return r.length > 0;
  };

  // Verificación previa de las tablas imprescindibles
  for (const t of ["cars", "booking", "person", "user"]) {
    if (!(await tableExists(t))) throw new Error(`La tabla legada '${t}' no existe en la BD origen`);
  }

  const pg = await pool.connect();
  const pq = async (sql: string, params: unknown[] = []) => pg.query(sql, params as any[]);
  const pone = async (sql: string, params: unknown[] = []) => (await pq(sql, params)).rows[0] ?? null;

  try {
    await pq("BEGIN");

    // --- Empresa destino ---
    let company = await pone("SELECT * FROM companies WHERE slug=$1", [SLUG]);
    if (!company) {
      company = await pone(
        "INSERT INTO companies (slug, name, currency) VALUES ($1,$2,'DOP') RETURNING *",
        [SLUG, NAME],
      );
      console.log(`Empresa creada: ${SLUG} (id ${company.id})`);
    }
    const cid = Number(company.id);

    // Comprobación integral de datos previos (no solo cars)
    const existing = await pone(
      `SELECT
         (SELECT COUNT(*) FROM cars WHERE company_id=$1) +
         (SELECT COUNT(*) FROM persons WHERE company_id=$1) +
         (SELECT COUNT(*) FROM bookings WHERE company_id=$1) +
         (SELECT COUNT(*) FROM payments WHERE company_id=$1) +
         (SELECT COUNT(*) FROM catalog_items WHERE company_id=$1) +
         (SELECT COUNT(*) FROM users WHERE company_id=$1 AND NOT is_super) AS total`,
      [cid],
    );
    if (Number(existing.total) > 0 && !FORCE) {
      throw new Error(`La empresa ${SLUG} ya tiene datos (${existing.total} filas). Usa --force para reemplazarlos.`);
    }
    if (FORCE && Number(existing.total) > 0) {
      console.log("--force: limpiando datos previos de la empresa...");
      for (const t of ["payments", "notifications", "notification_preferences", "device_tokens", "bookings", "cars", "catalog_items", "persons"]) {
        await pq(`DELETE FROM ${t} WHERE company_id=$1`, [cid]);
      }
      await pq("DELETE FROM refresh_tokens WHERE company_id=$1", [cid]);
      await pq("DELETE FROM users WHERE company_id=$1 AND NOT is_super", [cid]);
    }

    // --- Catálogos ---
    const catalogMap: Record<string, Map<number, number>> = {};
    const catalogTables: Array<[string, string]> = [
      ["brand", "brands"], ["category", "categories"], ["transmission", "transmissions"],
      ["fuel", "fuels"], ["color", "colors"], ["location", "locations"],
      ["stock", "stocks"], ["insurance", "insurances"],
    ];
    for (const [table, kind] of catalogTables) {
      catalogMap[kind] = new Map();
      if (!(await tableExists(table))) {
        console.log(`  (tabla legada '${table}' no existe, se omite)`);
        continue;
      }
      const items = await rows(`SELECT id, name FROM \`${table}\``);
      for (const it of items) {
        const name = String(it.name ?? "").trim() || `(sin nombre ${it.id})`;
        const rec = await pone(
          `INSERT INTO catalog_items (company_id, kind, name) VALUES ($1,$2,$3)
           ON CONFLICT (company_id, kind, name) DO UPDATE SET name=EXCLUDED.name RETURNING id`,
          [cid, kind, name],
        );
        catalogMap[kind].set(Number(it.id), Number(rec.id));
      }
      console.log(`  ${kind}: ${items.length}`);
    }
    const remap = (kind: string, legacyId: unknown): number =>
      catalogMap[kind]?.get(Number(legacyId ?? 0)) ?? 0;

    // --- Staff (tabla `user`; password legado sha1(md5)) ---
    const users = await rows("SELECT * FROM `user`");
    const userMap = new Map<number, number>();
    for (const u of users) {
      const rec = await pone(
        `INSERT INTO users (company_id, username, email, password_hash, password_algo, name, lastname, phone, kind, stock_id, image, status)
         VALUES ($1,$2,$3,$4,'sha1md5',$5,$6,$7,$8,$9,$10,$11) RETURNING id`,
        [
          cid,
          String(u.username ?? u.email ?? `user${u.id}`),
          String(u.email ?? ""),
          String(u.password ?? ""),
          String(u.name ?? ""), String(u.lastname ?? ""), String(u.phone ?? ""),
          Number(u.kind ?? 0), remap("stocks", u.stock_id), absUrl(u.image), Number(u.status ?? 1),
        ],
      );
      userMap.set(Number(u.id), Number(rec.id));
    }
    console.log(`  staff: ${users.length}`);

    // --- Clientes (tabla `person`; password legado en texto plano -> bcrypt) ---
    const persons = await rows("SELECT * FROM person");
    const personMap = new Map<number, number>();
    for (const p of persons) {
      const plain = String(p.password ?? "").trim();
      const hash = plain ? await bcrypt.hash(plain, 10) : "";
      const phone = String(p.phone ?? "");
      const rec = await pone(
        `INSERT INTO persons (company_id, name, lastname, email, phone, phone2, phone_normalized, username,
           password_hash, password_algo, address, address2, nationality, passport, license, language, stock_id,
           doc_cedula, doc_passport, doc_license, doc_home)
         VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,'bcrypt',$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20) RETURNING id`,
        [
          cid, String(p.name ?? ""), String(p.lastname ?? ""), String(p.email ?? ""),
          phone, String(p.phone2 ?? ""), digits(phone),
          String(p.username ?? "").trim() || phone, hash,
          String(p.address ?? ""), String(p.address2 ?? ""), String(p.nationality ?? ""),
          String(p.passport ?? ""), String(p.license ?? ""), String(p.language ?? "") || "ES",
          remap("stocks", p.stock_id),
          // Docs legados: invoice_file = cédula
          absUrl(p.invoice_file), absUrl(p.passport_file), absUrl(p.license_file), absUrl(p.home_file),
        ],
      );
      personMap.set(Number(p.id), Number(rec.id));
    }
    console.log(`  clientes: ${persons.length}`);

    // --- Vehículos (tabla `cars`; galería en `galery.invoice_file`) ---
    const cars = await rows("SELECT * FROM cars");
    const hasGalery = await tableExists("galery");
    const carMap = new Map<number, number>();
    for (const c of cars) {
      let images: string[] = [];
      if (hasGalery) {
        const gal = await rows(
          "SELECT invoice_file FROM galery WHERE car_id=? AND invoice_file<>'' ORDER BY id ASC",
          [Number(c.id)],
        );
        images = gal.map((g) => absUrl(g.invoice_file)).filter(Boolean) as string[];
      }
      // Igual que el API legado: galería primero, cars.invoice_file como respaldo
      const fallback = absUrl(c.invoice_file);
      if (!images.length && fallback) images.push(fallback);
      const main = images[0] ?? null;
      const rec = await pone(
        `INSERT INTO cars (company_id, name, year, plate, price, seat, kms, kms_current, status,
           brand_id, category_id, transmission_id, fuel_id, stock_id, image, images)
         VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16::jsonb) RETURNING id`,
        [
          cid, String(c.name ?? ""), String(c.year ?? ""), String(c.plate ?? ""),
          Number(c.price ?? 0), String(c.seat ?? ""), String(c.kms ?? ""), String(c.kms_current ?? ""),
          Number(c.status ?? 0),
          remap("brands", c.brand_id), remap("categories", c.category_id),
          remap("transmissions", c.transmission_id), remap("fuels", c.fuel_id),
          remap("stocks", c.stock_id), main, JSON.stringify(images),
        ],
      );
      carMap.set(Number(c.id), Number(rec.id));
    }
    console.log(`  vehículos: ${cars.length}`);

    // --- Reservas (tabla `booking`; firma en `firma`) ---
    const bookings = await rows("SELECT * FROM booking");
    const bookingMap = new Map<number, number>();
    let skippedBookings = 0;
    for (const b of bookings) {
      if (!b.start_at || !b.end_at) { skippedBookings++; continue; }
      const rec = await pone(
        `INSERT INTO bookings (company_id, code, person_id, car_id, user_id, stock_id, start_at, end_at,
           place_start, place_end, day, price, total, xtotal, payment, fuel, comment, status, deposit, sure, signature, created_at)
         VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,COALESCE($22, NOW())) RETURNING id`,
        [
          cid, String(b.code ?? ""), personMap.get(Number(b.person_id)) ?? 0,
          carMap.get(Number(b.car_id)) ?? 0, userMap.get(Number(b.user_id)) ?? 0,
          remap("stocks", b.stock_id), b.start_at, b.end_at,
          String(b.place_start ?? "No especificado"), String(b.place_end ?? "No especificado"),
          String(b.day ?? "1"), Number(b.price ?? 0), Number(b.total ?? 0), Number(b.xtotal ?? b.total ?? 0),
          Number(b.payment ?? 0), String(b.fuel ?? ""), String(b.comment ?? ""), Number(b.status ?? 0),
          Number(b.deposit ?? 0), Number(b.sure ?? 0), absUrl(b.firma), b.created_at ?? null,
        ],
      );
      bookingMap.set(Number(b.id), Number(rec.id));
    }
    console.log(`  reservas: ${bookings.length - skippedBookings} (omitidas ${skippedBookings})`);

    // --- Pagos (tabla `payment`) ---
    let paymentsCount = 0;
    if (await tableExists("payment")) {
      const pays = await rows("SELECT * FROM payment");
      for (const p of pays) {
        await pq(
          `INSERT INTO payments (company_id, booking_id, person_id, stock_id, val, payment_type_id, created_at)
           VALUES ($1,$2,$3,$4,$5,$6,COALESCE($7, NOW()))`,
          [
            cid, bookingMap.get(Number(p.booking_id)) ?? 0, personMap.get(Number(p.person_id)) ?? 0,
            remap("stocks", p.stock_id), Number(p.val ?? 0), Number(p.payment_type_id ?? 1), p.created_at ?? null,
          ],
        );
        paymentsCount++;
      }
    } else {
      console.log("  (tabla 'payment' no existe, se omite)");
    }
    console.log(`  pagos: ${paymentsCount}`);

    // Resumen para verificación antes/después
    const counts = await pone(
      `SELECT
         (SELECT COUNT(*) FROM cars WHERE company_id=$1) AS cars,
         (SELECT COUNT(*) FROM persons WHERE company_id=$1) AS persons,
         (SELECT COUNT(*) FROM bookings WHERE company_id=$1) AS bookings,
         (SELECT COUNT(*) FROM payments WHERE company_id=$1) AS payments,
         (SELECT COUNT(*) FROM users WHERE company_id=$1 AND NOT is_super) AS staff`,
      [cid],
    );
    console.log("  destino:", counts);

    await pq("COMMIT");
    console.log(`\nMigración completada hacia la empresa '${SLUG}' (id ${cid}).`);
  } catch (e) {
    await pq("ROLLBACK").catch(() => {});
    throw e;
  } finally {
    pg.release();
    await my.end().catch(() => {});
    await pool.end().catch(() => {});
  }
}

main().catch((e) => {
  console.error("Migración fallida (sin cambios en PostgreSQL):", e?.message ?? e);
  process.exit(1);
});
