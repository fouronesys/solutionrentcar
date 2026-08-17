import path from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));

/** Carpeta raíz de archivos subidos. */
export const STORAGE_DIR = path.resolve(__dirname, "..", "storage");

/** Archivos públicos (logos, fotos de flota) — servidos sin auth en /storage. */
export const PUBLIC_DIR = path.join(STORAGE_DIR, "public");

/** Archivos privados (documentos de identidad, firmas) — servidos con auth en /files. */
export const PRIVATE_DIR = path.join(STORAGE_DIR, "private");
