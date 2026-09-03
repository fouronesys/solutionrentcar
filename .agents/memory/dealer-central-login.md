---
name: Login central de Dealer
description: Relación entre el portal dealership y las instalaciones JER-IMPORT2R y SEVENFAUTOS
---

El portal `dealership.assanpos.com` sirve desde `/DEALER`; las instalaciones `JER-IMPORT2R` y `SEVENFAUTOS` redirigen allí cuando no existe `$_SESSION["user_id"]`.

**Why:** ambas bases pueden estar disponibles y tener usuarios activos aunque el login central rechace cuentas que no tienen correo; el formulario dice “Usuario”, pero la implementación antigua solo compara `user.email`.

**How to apply:** al corregir el handler central, normalizar `trim/lowercase`, aceptar `user.email` o `user.username`, conservar el hash legado `sha1(md5(...))` y exigir `status=1`. Validar después con una cuenta de prueba de cada instalación antes de promover.