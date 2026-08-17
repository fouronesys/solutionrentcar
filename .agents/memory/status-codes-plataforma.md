---
name: Semántica de estados rent-car
description: Lección durable sobre códigos de estado de bookings/cars al construir UIs nuevas.
---

# Semántica de estados (lección)

Regla: cualquier UI nueva sobre bookings/cars debe verificar los códigos de estado contra las queries UPDATE del server antes de escribir etiquetas, filtros o acciones — nunca asumirlos ni copiarlos de la web legada.

**Why:** El primer panel admin salió con la semántica invertida de disponibilidad de vehículos y las etiquetas de reserva corridas; el e2e feliz no lo detectó (los flujos "funcionaban" con etiquetas erróneas), lo encontró la revisión de código. Además, la web legada CF-SYSTEMS usa una escala distinta a la de su propia API: la API manda.

**How to apply:** Antes de mapear estados en una pantalla nueva, grep de `SET status=` en las rutas del server y confirmar el ciclo real; validar con una transición de extremo a extremo (entregar→devolver), no solo con lecturas.
