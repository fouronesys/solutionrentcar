---
name: EAS build debugging
description: Diagnóstico de builds EAS Android para la app móvil (custom workflow)
---
- Los logs de un build EAS se obtienen sin abrir el navegador: `eas build:view <id> --json` → `logFiles[0]`, y descargarlos con `curl --compressed` (sin ese flag llega binario gzip ilegible). Son JSON-lines con campo `msg`.
- **Why:** el usuario solo comparte capturas parciales; el log completo es la única fuente fiable.
- `npm install` en el builder de EAS puede morir con "npm error Exit handler never called!" sin instalar nada; el fallo aguas abajo aparece como `expo: not found`. Solución: usar `yarn install` (borra package-lock antes) con fallback a npm, y verificar `./node_modules/.bin/expo` antes del prebuild.
- El proyecto usa custom build workflow en `mobile/.eas/build/preview-android.yml`; EAS trata `mobile/` como raíz del proyecto, así que las rutas del yml son relativas a `mobile/` (no prefijar `mobile/`).
- Las carpetas nativas `android/`/`ios` commiteadas hacen que EAS detecte "bare workflow" y rechace `runtimeVersion: {policy}`; deben quedar fuera del repo (managed workflow, prebuild las genera).
- El agente puede correr comandos EAS de solo lectura (`build:list`, `build:view`) con `EXPO_TOKEN`; los builds los lanza el usuario.
