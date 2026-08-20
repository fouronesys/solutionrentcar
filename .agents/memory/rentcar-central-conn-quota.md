---
name: RENTCAR central y cuota de conexiones MySQL
description: Cuota MySQL e índice de acceso multiinstancia del portal central RENTCAR.
---
El portal central debe respetar una cuota inferior a 20 conexiones MySQL por petición y refrescar su índice de usuarios por lotes persistentes. Un fallo de índice debe reintentar entradas reparadas y priorizar las menos recientes.

**Why:** una cuenta creada después del escaneo inicial queda invisible si el índice solo revisa instalaciones nuevas; intentar todas las bases en una petición supera la cuota del hosting y produce SQLSTATE 2002.

**How to apply:** conectar secuencialmente, cerrar cada conexión, reservar una conexión para autenticar después de cada escaneo y guardar el avance. Probar primero el host configurado; usar el alternativo solo ante un 2002 que no sea de cuota.
