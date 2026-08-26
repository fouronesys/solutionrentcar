---
name: Validación de acceso GRUAS
description: Restricciones duraderas para validar autenticación de GRUAS antes de extender cambios a otras instalaciones.
---

La validación de GRUAS debe usar una cuenta autorizada propia de esa instalación. Una cuenta etiquetada para otro sistema no demuestra acceso a GRUAS, aunque el endpoint responda correctamente para credenciales inválidas.

**Why:** Las instalaciones pueden compartir el mismo proveedor y el mismo formato de login, pero no necesariamente comparten usuarios ni la base de datos; avanzar con una cuenta cruzada puede ocultar un rechazo real.

**How to apply:** Comprobar por separado éxito, cookie de sesión, redirección a `DEMO/?view=home`, usuario inexistente, contraseña errónea y cuenta inactiva. Si el éxito devuelve 401 o 503, detener el rollout y solicitar una cuenta GRUAS por el canal seguro, sin probar contraseñas por tanteo.