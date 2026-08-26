---
name: Hostinger FTP deploy
description: Conexión y promoción segura de archivos PHP por FTP en Hostinger
---
- El host correcto para FTP de Hostinger es la **IP del servidor que muestra hPanel** (ej. 213.190.5.68). `ftp.<dominio>` puede no existir en DNS (verificable consultando ns1.dns-parking.com directamente) y el dominio apex puede estar en parking con IPs rotativas.
- La cuenta FTP secundaria (`u144787244.<nombre>`) está enjaulada en su carpeta: el mirror debe ir a `/`, no a la ruta absoluta `/home/u.../public_html/...`.
- El puerto 21 saliente está bloqueado desde el workspace de Replit — no se pueden probar credenciales FTP localmente; probar disparando el workflow con `gh workflow run deploy-hostinger.yml` y leyendo logs (descarga del zip de logs con el token, el proxy de GitHub da Forbidden).
**Why:** el deploy falló 3 veces por "Name or service not known" y "max-retries exceeded" hasta usar la IP.
**How to apply:** ante fallos del workflow Deploy to Hostinger, revisar FTP_HOST primero; pedir la IP de hPanel.

## Reemplazo seguro de un PHP en producción

No borrar el archivo vivo antes de promover su reemplazo. Guardar temporales en una ruta comprobada como inaccesible por HTTP, probar primero que el servidor admite reemplazo `RNTO`, promover por rename y restaurar desde una copia local verificada.

**Why:** un timeout entre `rm` y `mv` puede dejar el endpoint ausente, y una copia `.tmp` bajo el webroot puede exponer el código fuente. El FTP de Hostinger usado aquí sí admitió reemplazo `RNTO` tras una prueba inofensiva.

**How to apply:** antes de un hot-swap, comprobar 403/404 para staging, demostrar `RNTO` con archivos inofensivos, comparar bytes tras promover/restaurar y exigir limpieza de temporales antes de declarar éxito.

## Auditorías de instalaciones múltiples

Para inventariar muchos sistemas bajo la cuenta FTP, listar primero las carpetas de primer nivel y consultar solo los puntos de entrada conocidos. Evitar `find /` recursivo como mecanismo principal: el volumen de archivos puede agotar el tiempo y producir un inventario incompleto.

**Why:** el primer recorrido recursivo terminó antes de alcanzar todas las instalaciones y confundió vistas sin `session_start()` con controladores defectuosos.

**How to apply:** separar siempre vistas, guardias de sesión y controladores; probar redirecciones sin seguirlas y registrar el `Location` antes de evaluar el código HTTP final.

## Sesiones FTP por lotes

El binario `lftp` disponible en los runners usados para estas auditorías acepta comandos por `-e`, pero no la opción `-f`; para lotes se debe construir una sola cadena `-e` y desactivar el fallo global por archivo inexistente.

**Why:** usar `-f` hizo que los listados y descargas parecieran vacíos aunque la conexión y la lectura FTP fueran correctas.

**How to apply:** validar siempre la sintaxis del binario del runner con un comando inofensivo antes de lanzar un inventario grande y registrar por separado el estado de cada descarga.
