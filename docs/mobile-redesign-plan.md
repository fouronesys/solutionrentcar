# Plan de rediseño de la app móvil — Yowell Rent-Car

**Estado:** Propuesta de diseño  
**Alcance:** App móvil Expo/React Native para clientes y personal  
**Referencia de marca:** `attached_assets/IMG-20260804-WA0058_1786211750788.jpg`  
**Fecha:** 8 de agosto de 2026

## 1. Objetivo

Rebrandear la aplicación móvil para que toda la experiencia se sienta como
**Yowell Rent-Car**: profesional, dinámica, confiable y reconocible desde el
primer segundo. El rediseño debe conservar la funcionalidad existente de
catálogo, reservas, pagos, firmas, agenda, notificaciones y perfiles, pero
mejorar de forma visible la jerarquía visual, la claridad de las tareas y la
percepción de calidad.

La app tiene dos recorridos principales:

- **Cliente:** explorar vehículos, consultar el detalle, reservar, revisar
  reservas, pagar, firmar y recibir notificaciones.
- **Personal:** revisar agenda, gestionar reservas, entregar/devolver
  vehículos, cancelar, registrar pagos y consultar notificaciones.

## 2. Diagnóstico del punto de partida

### Fortalezas que se deben conservar

- La navegación ya está separada por rol mediante Expo Router.
- Existen flujos funcionales de cliente y personal.
- Hay componentes compartidos para botones, inputs, tarjetas, estados vacíos,
  carga y badges.
- La app ya contempla español e inglés.
- El splash animado, las notificaciones y el almacenamiento seguro forman una
  buena base para una experiencia cuidada.

### Oportunidades de mejora visual

- El tema actual está dominado por dorado y negro, mientras que el nuevo logo
  comunica azul intenso, rojo, blanco y negro.
- La marca visible en el splash todavía dice “Solutions Rent Car”.
- Los cuatro slots de icono/splash usan la identidad anterior y deben
  sustituirse por versiones preparadas a partir del logo de Yowell.
- Cliente y personal comparten una navegación funcional, pero necesitan una
  jerarquía más clara para sus tareas prioritarias.
- Las pantallas de acceso, estados vacíos, error y carga deben sentirse parte
  de la marca, no estados genéricos.
- El logo recibido tiene fondo blanco y bastante detalle; debe prepararse una
  versión optimizada para tamaños pequeños antes de usarlo como icono.

## 3. Dirección de marca propuesta

### Personalidad

Yowell debe sentirse como una empresa de alquiler de vehículos que combina
energía automotriz con confianza operativa: rápida al reservar, transparente
al pagar y muy clara al gestionar una entrega o devolución.

La interfaz debe ser atractiva sin parecer infantil, deportiva sin sacrificar
legibilidad y moderna sin depender de efectos excesivos.

### Paleta base

Los valores definitivos se validarán contra el logo exportado, pero la primera
propuesta es:

| Token | Uso | Valor inicial |
|---|---|---|
| `yowellBlue` | Acción principal, enlaces, foco, selección | `#1828E8` |
| `yowellBlueDark` | Texto/acción sobre fondos claros, estados activos | `#111B9A` |
| `yowellRed` | Acentos, urgencia, promociones y llamadas secundarias | `#E51018` |
| `ink` | Encabezados, navegación oscura, contraste | `#0B0B0D` |
| `surface` | Fondo principal de la app | `#F5F7FB` |
| `white` | Tarjetas, campos y texto sobre fondos oscuros | `#FFFFFF` |
| `muted` | Texto auxiliar y metadatos | `#667085` |
| `success` | Disponible, confirmado, completado | `#138A55` |
| `warning` | Pendiente o requiere atención | `#B7791F` |

Reglas:

- El azul debe ser el color de acción y orientación.
- El rojo debe reservarse para énfasis, riesgo, cancelaciones y promociones;
  no usarlo como color general de botones.
- Negro y blanco deben sostener el contraste y conectar la interfaz con el
  contorno del logo.
- No usar fondos blancos con texto gris como solución final sin un acento de
  marca visible.
- Validar contraste WCAG AA para texto, botones, badges y estados.

### Tipografía

- Mantener Inter como base si no aparece una razón fuerte para cambiarla.
- Usar una jerarquía más expresiva:
  - títulos de pantalla: peso 700/800;
  - datos de reserva y precios: peso 700, con cifras fáciles de escanear;
  - etiquetas y metadatos: peso 600, nunca demasiado pequeñas;
  - textos de ayuda: peso 400 con interlineado generoso.
- Reservar el tratamiento más contundente para el nombre Yowell, precios,
  disponibilidad y acciones primarias.

### Tratamiento del logo

Preparar a partir de la imagen entregada:

1. **Logo maestro horizontal/escudo** para splash, login, perfil y piezas de
   marca.
2. **Logo compacto** con el escudo y “Yowell” para cabeceras.
3. **Isotipo** para icono de app, notificaciones y avatar de marca.
4. Versiones sobre fondo claro, fondo oscuro y monocromática.
5. PNG con transparencia y resolución suficiente para iOS/Android.
6. Área de seguridad y tamaños mínimos documentados para evitar que el texto
   “Rent-Car” se vuelva ilegible.

No conviene colocar el JPG original directamente como icono: primero se debe
recortar/limpiar el fondo blanco y producir variantes adaptativas.

## 4. Principios de experiencia

1. **Reserva en pocos pasos:** el usuario debe saber siempre qué vehículo
   está eligiendo, para qué fechas, dónde lo recoge y cuánto pagará.
2. **Una acción principal por vista:** cada pantalla debe tener una decisión
   dominante y un CTA claro.
3. **Confianza visible:** disponibilidad, precio, estado de reserva, políticas,
   pagos y próximos pasos deben ser explícitos.
4. **Diseño por rol:** el cliente necesita inspiración y tranquilidad; el
   personal necesita velocidad, densidad controlada y acciones seguras.
5. **Feedback inmediato:** toda acción de guardar, pagar, firmar, entregar,
   devolver o cancelar debe mostrar progreso y resultado.
6. **Accesibilidad desde el diseño:** áreas táctiles mínimas de 44 pt, estados
   no dependientes solo del color, soporte para texto ampliado y contraste
   suficiente.
7. **Consistencia bilingüe:** las composiciones deben soportar textos en
   español e inglés sin desbordamientos.

## 5. Arquitectura visual y componentes

Crear o normalizar un pequeño sistema de diseño compartido antes de rehacer
las pantallas:

- tokens de color, tipografía, espaciado, radios, sombras y elevación;
- `BrandHeader` con logo compacto, título y acciones contextuales;
- `PrimaryButton`, `SecondaryButton`, `DangerButton` y estados de carga;
- `VehicleCard` con foto, disponibilidad, atributos clave, precio y CTA;
- `StatusBadge` con icono + texto, no solo color;
- `BookingSummary` reutilizable para fechas, ubicación, vehículo, extras y
  total;
- `PriceDisplay` para moneda local, descuentos y totales;
- `SegmentedControl`/filtros para fechas, estado y disponibilidad;
- `EmptyState`, `ErrorState`, `Skeleton` y confirmaciones con lenguaje de
  marca;
- `BottomSheet` para filtros, acciones de reserva y confirmaciones;
- `Toast` o banner accesible para éxitos y errores;
- componentes de formulario con labels persistentes, ayuda y validación
  inline;
- iconografía Ionicons consistente, con azul para activo y rojo solo para
  acciones de riesgo.

La meta es que una actualización futura de la marca pueda hacerse desde los
tokens y componentes, sin corregir cada pantalla manualmente.

## 6. Plan por fases

### Fase 0 — Preparación y auditoría

**Prioridad:** P0  
**Resultado:** base lista para diseñar sin romper los flujos actuales.

- Revisar todas las rutas bajo `mobile/app/` por rol.
- Crear un inventario de estados de cada pantalla: carga, vacío, error,
  éxito, sin sesión y sesión expirada.
- Identificar qué componentes ya son compartidos y cuáles duplican estilos.
- Confirmar tamaños objetivo: teléfonos pequeños, teléfonos grandes, tablet y
  modo oscuro del sistema si se mantiene desactivado.
- Definir nombres de tokens y una matriz de estados de reserva/vehículo.
- Verificar que el idioma español y el inglés no rompan la nueva composición.

**Criterio de aceptación:** existe un mapa de pantallas y estados, sin rutas
funcionales olvidadas.

### ~~Fase 1 — Kit de marca y superficies nativas~~

**Estado: COMPLETADA — 8 de agosto de 2026**
**Prioridad:** P0  
**Resultado:** Yowell es reconocible incluso antes de iniciar sesión.

~~- Preparar los cuatro assets de marca para Expo:
  - icono principal;
  - icono adaptativo Android;
  - icono iOS;
  - splash.~~
~~- Sustituir el nombre “Solutions Rent Car” por “Yowell Rent-Car” en splash,
  configuraciones visibles y textos de marca.~~
~~- Rediseñar `AnimatedSplash` usando azul, rojo, blanco y negro, con una
  animación breve y elegante.~~
~~- Actualizar `StatusBar`, fondos, iconografía y colores de notificaciones.~~
~~- Definir una cabecera de marca reutilizable para las áreas cliente y staff.~~
~~- Revisar el nombre de la app, descripción, favicon y textos de tienda para
  que no queden referencias a la marca anterior.~~

**Entregado:**

- Assets Yowell limpios y documentados en `mobile/assets/branding/`.
- Icono principal, icono iOS, icono adaptativo Android, favicon, logo de
  interfaz y splash reemplazados.
- Tema actualizado a azul Yowell, rojo de acento, negro y blanco; los botones
  primarios y sombras ya no dependen del tema dorado anterior.
- Splash animado, cabeceras, pantallas de acceso, catálogo, reservas, perfil,
  notificaciones, firma y agenda actualizados a “YOWELL RENT-CAR”.
- Nombre visible de Expo, canal de notificaciones, textos de tienda y README
  actualizados.
- Identificadores técnicos de Expo y del paquete preservados para no romper
  instalaciones existentes, actualizaciones OTA ni configuración de tiendas.

~~**Criterio de aceptación:** al abrir la app, instalarla o verla en una
notificación, la identidad visible es Yowell y el logo permanece legible.~~

**Validación realizada:**

- Dimensiones verificadas: iconos 1024 × 1024, favicon 196 × 196 y splash
  1284 × 2778.
- Búsqueda de referencias antiguas completada en las pantallas y recursos
  visibles de la app móvil.
- `mobile/app.json` conserva el `slug`, `scheme`, bundle identifiers y API
  existentes; solo cambia el nombre visible y los assets.

### Fase 2 — Acceso y primera impresión

**Prioridad:** P0  
**Resultado:** entrada clara y profesional para cada tipo de usuario.

- Rediseñar bienvenida, login de cliente, login de personal y registro.
- Mostrar la diferencia entre reservar como cliente y operar como personal
  sin duplicar información.
- Añadir estados de validación, recuperación de sesión y errores de red con
  mensajes accionables.
- Mantener el acceso de invitado al catálogo, pero explicar cuándo se
  solicitará iniciar sesión.
- Aplicar el logo compacto en cabecera y el logo maestro en la superficie de
  bienvenida.

**Criterio de aceptación:** un usuario nuevo entiende qué puede hacer sin
  cuenta y llega al catálogo o al acceso correcto sin ambigüedad.

### Fase 3 — Experiencia de cliente

**Prioridad:** P0  
**Resultado:** explorar y reservar se siente rápido y confiable.

- **Catálogo de vehículos:** mejorar la jerarquía de foto, precio,
  disponibilidad, categoría, transmisión y combustible; añadir filtros
  fáciles de descubrir y estado de resultados.
- **Detalle del vehículo:** destacar galería, condiciones, características,
  ubicación, fechas y CTA de reserva; evitar que el precio quede escondido.
- **Crear reserva:** convertir el proceso en pasos comprensibles con resumen
  persistente, validación en línea y total visible antes de confirmar.
- **Mis reservas:** separar próximas, activas e históricas; mostrar el próximo
  paso de cada reserva.
- **Detalle de reserva:** presentar cronología de estados, pagos pendientes,
  firma, documentación y acciones disponibles.
- **Pago y firma:** reducir distracciones, comunicar seguridad y mostrar
  confirmación inequívoca.
- **Notificaciones:** priorizar reservas, pagos y acciones requeridas; usar
  estados leídos/no leídos accesibles.
- **Perfil:** agrupar datos personales, idioma, preferencias, soporte y cierre
  de sesión con una jerarquía simple.

**Criterio de aceptación:** un cliente puede pasar de catálogo a reserva
  confirmada entendiendo fechas, vehículo, ubicación y total en cada paso.

### Fase 4 — Experiencia de personal

**Prioridad:** P0  
**Resultado:** el equipo puede operar reservas con rapidez y menos errores.

- **Agenda:** priorizar lo que ocurre hoy y las próximas entregas/devoluciones.
- **Lista de reservas:** mejorar filtros, búsqueda y estados para localizar
  una reserva rápidamente.
- **Detalle operativo:** separar datos de consulta de acciones destructivas;
  agrupar entregar, devolver, cancelar y registrar pago.
- **Confirmaciones críticas:** exigir revisión del estado y mostrar
  claramente el efecto de cancelar, entregar o devolver.
- **Registro de pago:** destacar monto, moneda, método y comprobante.
- **Notificaciones:** diferenciar alertas operativas de mensajes informativos.
- **Perfil staff:** incluir contexto de sesión y una salida segura, sin mezclar
  opciones de cliente.

**Criterio de aceptación:** una persona del equipo identifica la tarea
  prioritaria del día y completa una acción operativa sin perder contexto.

### Fase 5 — Pulido, accesibilidad y motion

**Prioridad:** P1  
**Resultado:** la app se percibe sólida y cuidada en todos sus estados.

- Añadir transiciones cortas entre catálogo, detalle y reserva.
- Usar microinteracciones para selección de vehículo, filtros, guardado y
  confirmaciones.
- Normalizar skeletons, estados vacíos y errores con la identidad Yowell.
- Revisar áreas táctiles, contraste, foco, labels para lectores de pantalla y
  mensajes no dependientes del color.
- Comprobar layouts con textos largos en español e inglés.
- Evitar animaciones que retrasen tareas operativas o consuman batería.

**Criterio de aceptación:** las animaciones refuerzan la comprensión y no
interfieren con navegación, accesibilidad ni rendimiento.

### Fase 6 — Validación y entrega

**Prioridad:** P1  
**Resultado:** rediseño listo para distribuir como actualización.

- Probar en iOS y Android, teléfono pequeño y teléfono grande.
- Validar login, catálogo invitado, reserva, pago, firma, notificaciones y
  acciones staff con datos reales de prueba.
- Revisar iconos, splash, permisos y textos de tienda.
- Comparar capturas antes/después para validar coherencia de marca.
- Ejecutar lint/typecheck y una compilación preview de Android e iOS.
- Documentar el sistema de diseño y añadir una checklist para futuras pantallas.
- Preparar la actualización de versión y notas de lanzamiento.

**Criterio de aceptación:** no hay regresiones funcionales en los flujos
principales y cada plataforma muestra correctamente la identidad Yowell.

## 7. Orden recomendado de implementación

1. Tokens y sistema de componentes.
2. Assets de logo, icono y splash.
3. Bienvenida y autenticación.
4. Catálogo y detalle de vehículo.
5. Flujo de reserva, pago y firma.
6. Reservas y notificaciones del cliente.
7. Agenda y operación staff.
8. Perfil, estados secundarios y accesibilidad.
9. QA visual y compilaciones preview.

Este orden permite obtener una primera versión visible de la marca temprano,
sin dejar el flujo de reserva para el final ni duplicar trabajo entre roles.

## 8. Riesgos y decisiones a confirmar

- **Calidad del logo:** el JPG entregado tiene fondo blanco y detalles pequeños;
  se requiere una exportación limpia/transparente para iconos y splash.
- **Nombre legal/comercial:** confirmar si la marca visible será “Yowell
  Rent-Car”, “Yowell Rent Car” o una variante exacta.
- **Color de marca:** medir el azul y rojo del archivo maestro antes de fijar
  tokens definitivos.
- **Modo oscuro:** decidir si se mantiene `light` como experiencia oficial o
  si se diseña una variante oscura completa.
- **Moneda y región:** validar formato de precios y textos según los mercados
  donde opera la empresa.
- **Recursos fotográficos:** definir si se usarán fotos actuales de vehículos
  o un nuevo set con encuadres y fondos consistentes.
- **Alcance de producto:** este documento cubre el rediseño visual y de
  interacción de la app móvil; no propone cambiar API, permisos de negocio ni
  reglas de reservas.

## 9. Checklist de “listo para producción”

- [ ] No quedan referencias visibles a “Solutions Rent Car”.
- [ ] Logo maestro, compacto e isotipo exportados y optimizados.
- [ ] Iconos iOS/Android y splash actualizados.
- [ ] Tokens Yowell aplicados a cliente, staff y estados globales.
- [ ] Todos los CTAs principales tienen estados normal, carga, éxito y error.
- [ ] Catálogo → detalle → reserva → confirmación probado en ambos idiomas.
- [ ] Pago, firma, entrega, devolución y cancelación probados con confirmación.
- [ ] Texto, contraste, tamaño táctil y lectores de pantalla revisados.
- [ ] Capturas de tienda y descripción actualizadas.
- [ ] Build preview validada en iOS y Android.
