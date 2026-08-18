---
name: RENTCAR central y cuota de conexiones MySQL
description: Límite de ~20 conexiones MySQL por request PHP en el hosting central; login/update deben trabajar por lotes e índice.
---
- El hosting del sitio central (assanpos.com/RENTCAR) permite ~20 conexiones MySQL exitosas por request PHP; la conexión 21+ falla con SQLSTATE 2002 "Operation not permitted" (socket y TCP por igual). La cuota es por request: el siguiente request vuelve a tener 20.
- **Why:** un diagnóstico con orden alfabético y luego reverso mostró que siempre triunfan exactamente las primeras ~20 conexiones del request, sin importar qué base sea — no era un problema de GRANTs (la hipótesis original de "126 bases sin grant TCP" era falsa; 1045 en la IP pública era irrelevante).
- **How to apply:** cualquier script central que toque muchas bases debe conectar secuencialmente, cerrar cada conexión, procesar ≤15 bases por request y persistir avance (login usa índice email→carpeta en logs/central_user_index.json; update usa logs/central_update_progress.json). Fuente de los scripts: `scripts/central/` en el repo; se despliegan con la acción `deploy-central` del workflow inspect-remote.yml; `build-index` y `run-update` los ejecutan en bucle hasta PENDIENTES=0.
- socket 'localhost' funciona bien (no hace falta 127.0.0.1 ni la IP pública). La única instalación con credenciales realmente rotas es u144787244_lkrentacar (1045 genuino).
