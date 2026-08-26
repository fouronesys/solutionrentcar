---
name: GRUAS remote layout
description: La instalación GRUAS en assanpos.com mantiene la aplicación bajo DEMO aunque el endpoint público esté en /GRUAS.
---

La instalación remota de GRUAS puede exponer el front controller y `login.php` en `/GRUAS/`, mientras sus clases compartidas y configuración de base de datos viven bajo `/GRUAS/DEMO/core/`.

**Why:** Un archivo auxiliar copiado directamente en `/GRUAS/` que busque `core/` solo en ese nivel falla antes de mostrar la página, aunque `/GRUAS/` funcione.

**How to apply:** Los scripts auxiliares de diagnóstico o autenticación para GRUAS deben detectar primero `/GRUAS/core/` y luego `/GRUAS/DEMO/core/`, sin reemplazar el `login.php` de producción.