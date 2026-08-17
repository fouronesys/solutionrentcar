/**
 * Crea (si no existen) dos empresas demo con datos propios para probar el
 * aislamiento multi-tenant, más un super admin de plataforma.
 *
 * Credenciales de prueba (solo desarrollo):
 *   super admin:  superadmin / Super123!
 *   empresa demo-a: staff adminA / DemoA123! — cliente 8090000001 / ClienteA1!
 *   empresa demo-b: staff adminB / DemoB123! — cliente 8090000002 / ClienteB1!
 */
import bcrypt from "bcryptjs";
import { one, pool, q } from "../src/db.js";

async function ensureCompany(slug: string, name: string, primary: string): Promise<number> {
  const existing = await one("SELECT id FROM companies WHERE slug=$1", [slug]);
  if (existing) return Number(existing.id);
  const c = await one(
    `INSERT INTO companies (slug, name, color_primary, currency, phone, email, address)
     VALUES ($1,$2,$3,'DOP','809-000-0000',$4,$5) RETURNING id`,
    [slug, name, primary, `info@${slug}.test`, `Av. Demo, ${name}`],
  );
  return Number(c!.id);
}

async function ensureCatalog(companyId: number, kind: string, names: string[]): Promise<Record<string, number>> {
  const map: Record<string, number> = {};
  for (const n of names) {
    const item = await one(
      `INSERT INTO catalog_items (company_id, kind, name) VALUES ($1,$2,$3)
       ON CONFLICT (company_id, kind, name) DO UPDATE SET name=EXCLUDED.name RETURNING id`,
      [companyId, kind, n],
    );
    map[n] = Number(item!.id);
  }
  return map;
}

async function main() {
  // Super admin
  const su = await one("SELECT id FROM users WHERE is_super AND LOWER(username)='superadmin'");
  if (!su) {
    await q(
      `INSERT INTO users (company_id, is_super, username, email, password_hash, name, kind)
       VALUES (NULL, TRUE, 'superadmin', 'super@plataforma.test', $1, 'Super Admin', 9)`,
      [await bcrypt.hash("Super123!", 10)],
    );
    console.log("Super admin creado: superadmin");
  }

  const companies = [
    { slug: "demo-a", name: "Demo Rent-Car A", color: "#fb3b54", staff: ["adminA", "DemoA123!"], client: ["8090000001", "ClienteA1!"], carPrefix: "A" },
    { slug: "demo-b", name: "Demo Rent-Car B", color: "#2563eb", staff: ["adminB", "DemoB123!"], client: ["8090000002", "ClienteB1!"], carPrefix: "B" },
  ];

  for (const def of companies) {
    const cid = await ensureCompany(def.slug, def.name, def.color);
    const brands = await ensureCatalog(cid, "brands", ["Toyota", "Hyundai"]);
    await ensureCatalog(cid, "categories", ["SUV", "Sedán"]);
    await ensureCatalog(cid, "transmissions", ["Automática", "Manual"]);
    await ensureCatalog(cid, "fuels", ["Gasolina"]);
    await ensureCatalog(cid, "stocks", ["Principal"]);

    const staff = await one("SELECT id FROM users WHERE company_id=$1 AND LOWER(username)=LOWER($2)", [cid, def.staff[0]]);
    if (!staff) {
      await q(
        `INSERT INTO users (company_id, username, email, password_hash, name, kind)
         VALUES ($1,$2,$3,$4,$5,1)`,
        [cid, def.staff[0], `${def.staff[0].toLowerCase()}@${def.slug}.test`, await bcrypt.hash(def.staff[1], 10), `Admin ${def.name}`],
      );
    }

    const phone = def.client[0];
    const client = await one("SELECT id FROM persons WHERE company_id=$1 AND phone_normalized=$2", [cid, phone]);
    if (!client) {
      await q(
        `INSERT INTO persons (company_id, name, phone, phone_normalized, username, password_hash)
         VALUES ($1,$2,$3,$3,$3,$4)`,
        [cid, `Cliente ${def.name}`, phone, await bcrypt.hash(def.client[1], 10)],
      );
    }

    const car = await one("SELECT id FROM cars WHERE company_id=$1 LIMIT 1", [cid]);
    if (!car) {
      await q(
        `INSERT INTO cars (company_id, name, year, plate, price, seat, brand_id, status)
         VALUES ($1,$2,'2024',$3, 3500, '5', $4, 0), ($1,$5,'2023',$6, 2800, '5', $7, 0)`,
        [
          cid, `Toyota RAV4 ${def.carPrefix}`, `${def.carPrefix}-111111`, brands["Toyota"],
          `Hyundai Elantra ${def.carPrefix}`, `${def.carPrefix}-222222`, brands["Hyundai"],
        ],
      );
    }
    console.log(`Empresa ${def.slug} lista (id ${cid}).`);
  }

  await pool.end();
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
