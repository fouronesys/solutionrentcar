import { Router } from "express";
import { err, h, ok, toNum, toStr } from "../helpers.js";

export const placesRouter = Router();

type Place = {
  id: string;
  title: string;
  subtitle: string;
  label: string;
  lat: number;
  lng: number;
};

const cache = new Map<string, { at: number; value: unknown }>();
const TTL = 10 * 60 * 1000;

function cacheGet(key: string): unknown | undefined {
  const hit = cache.get(key);
  if (hit && Date.now() - hit.at < TTL) return hit.value;
  cache.delete(key);
  return undefined;
}

function cacheSet(key: string, value: unknown): void {
  if (cache.size > 500) cache.clear();
  cache.set(key, { at: Date.now(), value });
}

const UA = "RentCarPlatformAPI/1.0 (contact: admin)";

function mapNominatim(item: any): Place {
  const label = toStr(item.display_name);
  const parts = label.split(",").map((s: string) => s.trim());
  return {
    id: String(item.place_id ?? item.osm_id ?? label),
    title: parts[0] ?? label,
    subtitle: parts.slice(1).join(", "),
    label,
    lat: toNum(item.lat),
    lng: toNum(item.lon),
  };
}

placesRouter.get("/search", h(async (req, res) => {
  let qStr = toStr(req.query.q).trim();
  const langRaw = toStr(req.query.lang).toLowerCase();
  const lang = langRaw === "en" ? "en" : "es";
  if (qStr.length < 3) return ok(res, { results: [] });
  if (qStr.length > 120) qStr = qStr.slice(0, 120);

  const key = `s:${lang}:${qStr.toLowerCase()}`;
  const hit = cacheGet(key);
  if (hit !== undefined) return ok(res, hit);

  try {
    const url = new URL("https://nominatim.openstreetmap.org/search");
    url.searchParams.set("format", "jsonv2");
    url.searchParams.set("q", qStr);
    url.searchParams.set("limit", "8");
    url.searchParams.set("accept-language", lang);
    const r = await fetch(url, { headers: { "User-Agent": UA } });
    if (!r.ok) throw new Error(`upstream ${r.status}`);
    const data = (await r.json()) as any[];
    const value = { results: data.map(mapNominatim) };
    cacheSet(key, value);
    return ok(res, value);
  } catch (e) {
    console.error("[places/search]", e);
    return err(res, "upstream_error", "Servicio de lugares no disponible", 502);
  }
}));

placesRouter.get("/reverse", h(async (req, res) => {
  const lat = toNum(req.query.lat);
  const lon = toNum(req.query.lon);
  if (
    !isFinite(lat) || !isFinite(lon) ||
    lat < -90 || lat > 90 || lon < -180 || lon > 180 ||
    (lat === 0 && lon === 0)
  ) {
    return err(res, "invalid_request", "lat/lon inválidos", 400);
  }
  const langRaw = toStr(req.query.lang).toLowerCase();
  const lang = langRaw === "en" ? "en" : "es";

  const key = `r:${lang}:${lat.toFixed(5)}:${lon.toFixed(5)}`;
  const hit = cacheGet(key);
  if (hit !== undefined) return ok(res, hit);

  try {
    const url = new URL("https://nominatim.openstreetmap.org/reverse");
    url.searchParams.set("format", "jsonv2");
    url.searchParams.set("lat", String(lat));
    url.searchParams.set("lon", String(lon));
    url.searchParams.set("accept-language", lang);
    const r = await fetch(url, { headers: { "User-Agent": UA } });
    if (!r.ok) throw new Error(`upstream ${r.status}`);
    const data = (await r.json()) as any;
    const value = { result: data && data.display_name ? mapNominatim(data) : null };
    cacheSet(key, value);
    return ok(res, value);
  } catch (e) {
    console.error("[places/reverse]", e);
    return err(res, "upstream_error", "Servicio de lugares no disponible", 502);
  }
}));

placesRouter.all("*", (req, res) => err(res, "not_found", "Endpoint de lugares no encontrado", 404));
