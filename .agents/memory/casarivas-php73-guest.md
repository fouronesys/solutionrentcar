---
name: Casa Rivas — PHP 7.3 y cuenta guest
description: Restricciones del servidor Hostinger (PHP 7.3) y diseño de la sesión guest de la app.
---

## PHP 7.3 obligatorio en assanpos.com
El hosting debe quedarse en PHP 7.3 (decisión del usuario). Todo `core/` y `CF-SYSTEMS/` fue convertido con Rector (DOWN_TO_PHP_73); **no introducir sintaxis 7.4+/8** (propiedades tipadas, `fn()`, `?->`, `match`, `??=`).
**Cómo validar:** `phpcs` + PHPCompatibility con `testVersion 7.3` (instalados vía composer en /tmp; regenerar si hace falta). El lint local `php -l` es 8.2 y rechaza libs legacy (PHPExcel) que sí funcionan en 7.3 — ignorarlas. `CF-SYSTEMS/report/sellsbycat-xlsx.php` ya venía con error de sintaxis en el repo original.

## Sesión guest (catálogo público)
- La app usa una cuenta cliente `casarivas.guest` (persona id 4 en la BD de Casa Rivas) para mostrar el catálogo sin login. Credenciales embebidas en el bundle por diseño (no secretas).
- **Solo lectura**: el login marca claim `guest=1` y `ApiAuth::require` rechaza métodos != GET con 403. La app solo hace guest-retry en GETs.
- Las env vars `EXPO_PUBLIC_GUEST_USERNAME/PASSWORD` (shared) pisan los defaults del código en el bundle — si el guest falla con invalid_credentials, revisar esas env vars primero.
- Metro cachea agresivo: tras cambiar código o env vars, borrar `/tmp/metro-*`, `mobile/.expo`, `mobile/node_modules/.cache` y reiniciar el workflow; verificar con grep sobre el bundle servido.

## BD y credenciales
`core/controller/Database.php` ya no tiene credenciales hardcodeadas: carga `core/db.local.php` (generado por el deploy desde secrets DB_*) o env vars DB_*. La contraseña vieja de Yowell (`u144787244_solutionsrent`) quedó expuesta en el historial de git — se recomendó rotarla.
- Imágenes de autos: `CF-SYSTEMS/storage/` está excluido del mirror principal (con --delete); para subir archivos al storage se usa el segundo mirror de `deploy_assets/car_images/` → `/CF-SYSTEMS/storage/invoice_files/` (sin --delete). FTP directo desde Replit no es posible (puerto 21 bloqueado).
- La API construye URLs de imagen derivando el prefijo de subdirectorio desde SCRIPT_NAME (el sistema vive bajo /RENTCAR/CASARIVAS-RENTCAR); si se muda de carpeta no hay que tocar nada.
- El paso "Install lftp" del deploy se cuelga a veces por apt-get update; quedó con timeout de 5 min e instalación sin update primero. "mirror: max-retries exceeded" sin líneas de transferencia = no conectó; reintentar suele bastar.
