---
name: Yowell legacy migration
description: Restricciones no obvias para migrar los datos legados de Yowell al backend multi-empresa.
---
- El MySQL de origen es el de PRODUCCIÓN en Hostinger (el PHP legado se conecta directo; credenciales hardcodeadas en `core/controller/Database.php`). Tratarlo SIEMPRE como solo lectura.
- **Why:** no existe copia local utilizable — el `mysqld` del repl no escucha en TCP 127.0.0.1:3306, así que cualquier migración o verificación lee de producción o de un dump importado aparte.
- **How to apply:** al ejecutar la migración de Yowell, confirmar recuentos de entidades antes/después y no correr escrituras de prueba contra ese host.
