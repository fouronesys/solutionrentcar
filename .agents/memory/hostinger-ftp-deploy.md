---
name: Hostinger FTP deploy
description: Cómo conectar el deploy FTP a Hostinger cuando el host falla
---
- El host correcto para FTP de Hostinger es la **IP del servidor que muestra hPanel** (ej. 213.190.5.68). `ftp.<dominio>` puede no existir en DNS (verificable consultando ns1.dns-parking.com directamente) y el dominio apex puede estar en parking con IPs rotativas.
- La cuenta FTP secundaria (`u144787244.<nombre>`) está enjaulada en su carpeta: el mirror debe ir a `/`, no a la ruta absoluta `/home/u.../public_html/...`.
- El puerto 21 saliente está bloqueado desde el workspace de Replit — no se pueden probar credenciales FTP localmente; probar disparando el workflow con `gh workflow run deploy-hostinger.yml` y leyendo logs (descarga del zip de logs con el token, el proxy de GitHub da Forbidden).
**Why:** el deploy falló 3 veces por "Name or service not known" y "max-retries exceeded" hasta usar la IP.
**How to apply:** ante fallos del workflow Deploy to Hostinger, revisar FTP_HOST primero; pedir la IP de hPanel.
