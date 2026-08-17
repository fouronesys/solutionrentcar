# Yowell Rent-Car (solutionsrentcar.do)

## Overview
- PHP backend legado (CF-SYSTEMS) servido en el puerto 5000 (workflow "Start application"); incluye la API `CF-SYSTEMS/api/v1` y el panel admin web (front controller `index.php?view=...`).
- App móvil Expo en `mobile/` (workflow "Mobile App (Expo Web)", puerto 3000, `--no-dev`). Grupos de rutas: `(client)` y `(staff)`; logins en `/login/client` y `/login/staff`.
- CI en GitHub Actions (repo `fouronesys/solutionrentcar`): Deploy a Hostinger (FTP), build iOS vía EAS, build Android vía Gradle. Instalación de dependencias móviles **solo con yarn** (`yarn install --frozen-lockfile`); no debe existir `mobile/package-lock.json`.
- Screenshots de tiendas en `store-screenshots/` (limpios y con diseño).
- **Backend multi-empresa nuevo** en `server/` (Node + TypeScript + Express + PostgreSQL integrada/Neon): workflow "API Multiempresa", puerto 8000, prefijos `/api/v1` y `/CF-SYSTEMS/api/v1` (compatible con la app móvil). Multi-tenant por `company_id`; empresa resuelta por token JWT o header `X-Company`/`?company=<slug>`. Branding público en `/branding`; admin en `/admin` (super admin: CRUD empresas; staff: flota y catálogos). Scripts: `npm run db:setup` (esquema), `npm run db:seed-demo` (2 empresas demo + super admin), `npm run migrate:legacy` (MySQL legado → PG, requiere env `LEGACY_DB_*`, solo lectura sobre MySQL). Archivos subidos en `server/storage/` (servidos en `/storage`, fuera de git). JWT firma con `SESSION_SECRET`.

## User preferences
- Comunicación en español.
- No ejecutar tareas extensas de monitoreo (polling de builds CI, sleeps largos) que consuman créditos: hacer el cambio, avisar, y esperar a que el usuario notifique para verificar resultados.
- Los screenshots "limpios" no llevan ningún diseño añadido; el set con diseño usa el branding oscuro+rojo de la app y una frase dividida secuencialmente entre screenshots.
