# Solutions Rent Car — Publicación a App Store y Google Play

Esta guía está pensada para que sigas los pasos en orden. Cada bloque es independiente.
Todo lo que se puede automatizar **ya está hecho**; aquí solo quedan los pasos que
requieren tu cuenta personal (Apple/Google/Expo/Firebase) o subir archivos a tiendas.

---

## 1. Cuenta Expo (5 min, una sola vez)

1. Ve a https://expo.dev/signup y crea la cuenta con `admin@fourone.com.do`.
2. Cuando confirmes el correo, anota el **username** que elijas (ej. `fourone`).
   Lo necesitamos para `app.json → owner`.
3. Desde tu PC instala el CLI:

```bash
npm install -g eas-cli
cd mobile
eas login          # usa el correo + contraseña recién creados
eas init           # esto crea el proyecto en Expo y rellena app.json automáticamente
```

`eas init` agrega dos campos a `app.json`:
- `expo.owner` (tu username)
- `expo.extra.eas.projectId` (UUID del proyecto)

**Sube esos cambios** con `git push` para que el repo refleje el proyecto Expo.

---

## 2. Notificaciones push en Android (FCM v1) — 10 min

Expo necesita credenciales de Firebase para entregar notificaciones a Android.

1. Ve a https://console.firebase.google.com → **Add project** → llámalo
   `Solutions Rent Car`. Acepta los defaults, sin Google Analytics.
2. Dentro del proyecto: **⚙️ Project settings → Cloud Messaging**.
   - Verifica que **Firebase Cloud Messaging API (V1)** esté **Enabled**.
3. **⚙️ Project settings → Service accounts → Generate new private key**.
   - Descarga el JSON. Renómbralo a `google-services-firebase.json`.
4. Súbelo a EAS:

```bash
cd mobile
eas credentials
# Selecciona: Android → production → Google Service Account → Set up a Google Service Account Key for Push Notifications
# Cuando te pida el path, pega el path al JSON descargado.
```

Listo. Expo ahora puede mandar push a Android.

---

## 3. Notificaciones push en iOS (APNs) — automático

No tienes que hacer nada manual. Cuando ejecutes `eas build --platform ios`,
EAS te preguntará si quiere generar/gestionar el APNs key por ti. Responde **Yes**.
Requiere haber iniciado sesión con Apple ID (`eas credentials` o durante el build).

---

## 4. Subida a Google Play (Service Account) — 15 min

Necesitas un Service Account JSON con permisos para subir builds.

1. Crea tu app en Google Play Console: https://play.google.com/console
   - **Create app** → nombre: *Solutions Rent Car* → idioma: Español
   - Completa el cuestionario inicial (categoría: Travel & Local, gratis o pagada, etc.)
2. Vincula la cuenta de servicio:
   - **Setup → API access** → vincula al proyecto Firebase del paso 2
     (acepta el banner que pide enlazarlo).
   - Aparecerá un Service Account llamado `firebase-adminsdk-...` →
     click **Grant access** → permisos: *Release apps to production* +
     *Manage store presence*. Guarda.
3. En Google Cloud Console (https://console.cloud.google.com), proyecto Firebase →
   **IAM & Admin → Service Accounts** → busca el `firebase-adminsdk-...` →
   menú ⋮ → **Manage keys → Add key → JSON**.
4. El archivo descargado guárdalo como `mobile/google-service-account.json`
   (está ignorado por git, así que no se sube).

Ya está. `eas submit --platform android` lo usará automáticamente.

---

## 5. Subida a App Store Connect — 10 min

1. Ve a https://appstoreconnect.apple.com/apps → **+** → **New App**
   - Plataforma: iOS
   - Nombre: *Solutions Rent Car*
   - Idioma principal: Español
   - Bundle ID: selecciona `com.solutionsrent.app` (debe crearse antes en
     developer.apple.com → Identifiers; EAS lo crea automáticamente en el primer build)
   - SKU: `solutions-rent-car-001`
2. Cuando esté creada, copia el **Apple ID** numérico (10 dígitos) que aparece
   en la URL: `appstoreconnect.apple.com/apps/<ESTE_NUMERO>/...`
3. Pégalo en `mobile/eas.json` reemplazando `REPLACE_AFTER_CREATING_APP_IN_APP_STORE_CONNECT`.

---

## 6. Builds y subidas — comandos finales

Desde `mobile/`:

```bash
# Builds (la primera vez te pedirá credenciales — acepta defaults)
eas build --platform android --profile production
eas build --platform ios     --profile production

# Submits (cuando los builds estén listos)
eas submit --platform android --latest
eas submit --platform ios     --latest
```

Las builds salen en ~15-25 min. Te llega notificación por email cuando estén.

---

## 7. Checklist final antes de publicar

- [ ] API en producción responde: `curl https://solutionsrentcar.do/CF-SYSTEMS/api/v1/health` → `{"ok":true}`
- [ ] `mobile/app.json` tiene `owner` y `extra.eas.projectId` (rellenados por `eas init`)
- [ ] FCM service account subido a EAS (`eas credentials`)
- [ ] `mobile/google-service-account.json` existe localmente (Play submit)
- [ ] `eas.json → submit.production.ios.ascAppId` tiene el Apple ID numérico real
- [ ] Capturas de pantalla 6.5" y 5.5" para iOS, y 16:9 para Android
- [ ] Política de privacidad publicada (necesaria para ambas tiendas).
      Mínimo una página `https://solutionsrentcar.do/privacy.html`.
