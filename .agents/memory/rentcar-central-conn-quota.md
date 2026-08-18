---
name: RENTCAR central y cuota de conexiones MySQL
description: Límite de ~20 conexiones MySQL por request PHP en el hosting central; scripts multi-base deben trabajar por lotes con avance persistido.
---
- El hosting del sitio central RENTCAR permite ~20 conexiones MySQL exitosas por request PHP; la conexión 21+ falla con SQLSTATE 2002 "Operation not permitted" (socket y TCP por igual). La cuota es por request: el siguiente request vuelve a tener ~20. El socket 'localhost' funciona; no es un problema de GRANTs.
- **Why:** un diagnóstico en orden alfabético y luego reverso mostró que siempre triunfan exactamente las primeras ~20 conexiones del request, sin importar la base; el 1045 contra la IP pública era una pista falsa.
- **How to apply:** cualquier script central que toque muchas bases debe conectar secuencialmente, cerrar cada conexión, limitarse a <20 por request y persistir el avance para reanudar en el siguiente request (el login usa un índice email→instalación para conectar solo a 1-2 bases).
