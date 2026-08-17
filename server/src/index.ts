import express from "express";
import fs from "node:fs";
import { err, h, ok } from "./helpers.js";
import { PRIVATE_DIR, PUBLIC_DIR } from "./storage.js";
import { filesRouter } from "./routes/files.js";
import { authRouter } from "./routes/auth.js";
import { meRouter } from "./routes/me.js";
import { carsRouter } from "./routes/cars.js";
import { bookingsRouter } from "./routes/bookings.js";
import {
  agendaRouter,
  catalogRouter,
  healthRouter,
  notificationsRouter,
  paymentsRouter,
  preferencesRouter,
  pushRouter,
} from "./routes/misc.js";
import { placesRouter } from "./routes/places.js";
import { enforceLiveStaff } from "./auth.js";
import { brandingRouter } from "./routes/branding.js";
import { adminRouter } from "./routes/admin.js";

const app = express();
app.set("trust proxy", true);
app.use(express.json({ limit: "12mb" }));
app.use(express.urlencoded({ extended: true, limit: "12mb" }));

// CORS (idéntico en espíritu al legado)
app.use((req, res, next) => {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Methods", "GET, POST, PUT, PATCH, DELETE, OPTIONS");
  res.header("Access-Control-Allow-Headers", "Content-Type, Authorization, X-Requested-With, X-Company, X-App-Version, X-App-Platform");
  if (req.method === "OPTIONS") return res.sendStatus(204);
  next();
});

// Archivos públicos (logos, fotos de flota); los privados (documentos, firmas) van por /files con auth
fs.mkdirSync(PUBLIC_DIR, { recursive: true });
fs.mkdirSync(PRIVATE_DIR, { recursive: true });
app.use("/storage", express.static(PUBLIC_DIR, { maxAge: "1d" }));
app.use("/files", filesRouter);

const api = express.Router();
api.use("/auth", authRouter);
// Verificación viva global: staff desactivado (o de empresa desactivada) pierde
// acceso de inmediato en TODAS las rutas, aunque su access token siga vigente.
api.use(enforceLiveStaff());
api.use("/me", meRouter);
api.use("/cars", carsRouter);
api.use("/bookings", bookingsRouter);
api.use("/agenda", agendaRouter);
api.use("/notifications", notificationsRouter);
api.use("/preferences", preferencesRouter);
api.use("/payments", paymentsRouter);
api.use("/push", pushRouter);
api.use("/catalog", catalogRouter);
api.use("/places", placesRouter);
api.use("/health", healthRouter);
api.use("/branding", brandingRouter);
api.use("/admin", adminRouter);

api.get("/", h(async (_req, res) => {
  return ok(res, {
    name: "Rent-Car Platform API",
    version: "v1",
    status: "ok",
    time: new Date().toISOString(),
  });
}));

api.all("*", (req, res) => {
  const resource = req.path.split("/")[1] ?? "";
  err(res, "not_found", `Recurso '${resource}' no encontrado`, 404);
});

// Compatibilidad: misma API bajo ambos prefijos (el móvil legado usa /CF-SYSTEMS/api/v1)
app.use("/api/v1", api);
app.use("/CF-SYSTEMS/api/v1", api);

app.get("/", (_req, res) => {
  res.json({ ok: true, data: { name: "Rent-Car Platform API", version: "v1", status: "ok", time: new Date().toISOString() } });
});

const PORT = parseInt(process.env.API_PORT || "8000", 10);
app.listen(PORT, "0.0.0.0", () => {
  console.log(`API multi-empresa escuchando en 0.0.0.0:${PORT}`);
});
