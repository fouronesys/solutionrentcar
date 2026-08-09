# Plan de rediseño de la app móvil — Yowell Rent-Car

**Estado:** ✅ Completado  
**Alcance:** App móvil Expo/React Native para clientes y personal  
**Referencia de marca:** Screenshots de referencia del flujo visual definitivo  
**Fecha:** 9 de agosto de 2026

---

## Progreso de fases

| Fase | Título | Estado |
|---|---|---|
| ~~Fase 0~~ | ~~Tokens y fundación de colores~~ | ✅ Completada |
| ~~Fase 1~~ | ~~Componentes base (Button, Input, ScreenHeader)~~ | ✅ Completada |
| ~~Fase 2~~ | ~~Navegación de cliente (4 tabs, activo rojo)~~ | ✅ Completada |
| ~~Fase 3~~ | ~~Pantallas del cliente~~ | ✅ Completada |
| ~~Fase 4~~ | ~~Pantallas del staff y logins~~ | ✅ Completada |

---

## 1. Objetivo

Rebrandear la aplicación móvil para que toda la experiencia se sienta como
**Yowell Rent-Car**: profesional, dinámica, confiable y reconocible desde el
primer segundo. El rediseño conserva la funcionalidad existente de catálogo,
reservas, pagos, firmas, agenda, notificaciones y perfiles, mejorando la
jerarquía visual, la claridad de las tareas y la percepción de calidad.

---

## 2. Decisiones de diseño tomadas

### Paleta definitiva (tokens en `colors.ts`)

| Token | Valor | Uso |
|---|---|---|
| `cta` | `#E8002D` | Botón primario, tab activo, acentos de acción |
| `ctaDark` | `#B2001F` | Estado presionado del CTA |
| `ctaLight` | `#FFCCD5` | Fondos sutiles de acento rojo |
| `ctaXLight` | `#FFF0F3` | Fondos de inputs en foco |
| `bg` | `#F2F3F5` | Fondo principal de la app |
| `card` | `#FFFFFF` | Tarjetas y modales |
| `dark` / `darkCard` | `#111827` | Tarjeta RUTA oscura, botones oscuros |
| `primary` | `#1828E8` | Loyalty card, foco info |

### Reglas de diseño

- **Header de pantalla:** Blanco (`colors.card`) via `ScreenHeader`, logo a la izquierda + título + subtítulo. Sin `LinearGradient` azul.
- **CTA primario:** Rojo sólido `#E8002D`, texto blanco en mayúsculas. Sin `LinearGradient`.
- **Tab activo:** Rojo (`colors.cta`).
- **Tabs del cliente:** Inicio / Autos / Ubicaciones / Perfil (4 tabs). `bookings` y `notifications` ocultos (`href: null`), accesibles desde perfil/header.
- **Tarjeta RUTA:** Fondo `darkCard` (`#111827`) en la pantalla de reserva.
- **Cards de autos:** Imagen grande, badge oscuro, corazón, rating estrellas, botón "Elegir" oscuro.

---

## 3. Archivos modificados

### Fundación
- `mobile/src/theme/colors.ts` — tokens completos (cta rojo, bg gris, darkCard)

### Componentes
- `mobile/src/components/Button.tsx` — variant `primary` → rojo sólido, sin gradiente
- `mobile/src/components/Input.tsx` — foco rojo
- `mobile/src/components/ScreenHeader.tsx` — **NUEVO** header blanco reutilizable
- `mobile/src/components/EmptyState.tsx` — acento rojo

### Navegación
- `mobile/app/(client)/_layout.tsx` — 4 tabs, activo rojo
- `mobile/app/(staff)/_layout.tsx` — activo rojo, badges rojos

### Pantallas del cliente
- `mobile/app/(client)/index.tsx` — **NUEVO** Inicio: saludo, CTAs, reserva próxima
- `mobile/app/(client)/cars.tsx` — header blanco, tarjetas redeseñadas, sin gradiente héroe
- `mobile/app/(client)/locations.tsx` — **NUEVO** mapa placeholder, sucursales
- `mobile/app/(client)/bookings.tsx` — header blanco via ScreenHeader
- `mobile/app/(client)/profile.tsx` — avatar oscuro, loyalty card, menú, logout rojo
- `mobile/app/(client)/book/[carId].tsx` — header blanco + paso/progreso rojo, tarjeta RUTA oscura

### Pantallas de autenticación
- `mobile/app/login/client.tsx` — diseño limpio blanco + rojo
- `mobile/app/login/staff.tsx` — diseño limpio blanco + oscuro
- `mobile/app/register/client.tsx` — diseño limpio blanco + rojo

### Pantallas del staff
- `mobile/app/(staff)/agenda.tsx` — header blanco via ScreenHeader, sin gradiente
- `mobile/app/(staff)/bookings.tsx` — header blanco + barra de búsqueda blanca, sin gradiente

---

## 4. Qué NO se cambió

- Identificadores técnicos de Expo: `slug`, `scheme`, bundle IDs
- Lógica de negocio (API calls, auth, pagos, firmas)
- Estructura de base de datos y endpoints
- `app/(staff)/booking/[id].tsx` — pantalla de detalle del staff (sólo estilos menores)
- `app/(staff)/pay/[bookingId].tsx` — flujo de pago del staff
- `app/(client)/booking/[id].tsx` — detalle de reserva del cliente

---

## 5. Verificación

- [ ] Sin referencias a `gradients.hero` en pantallas visibles
- [ ] Sin referencias a `shadow.primary` (azul) en botones primarios
- [ ] Tab activo = rojo en cliente y staff
- [ ] CTA rojo sin LinearGradient
- [ ] Todos los headers usan `ScreenHeader` o header blanco manual
