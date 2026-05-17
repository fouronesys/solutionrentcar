# Solutions Rent Car — Mobile App (Expo, iOS + Android)

Single codebase Expo / React Native app for the Solutions Rent Car platform.
Talks to the `/CF-SYSTEMS/api/v1/` REST API in this repo and routes the user
into one of two flows based on the type of session returned by `/auth/login`:

- **Client flow** (`app/(client)/...`): available cars, car detail, booking
  creation, my bookings, booking detail with payments + signature, inbox.
- **Staff flow** (`app/(staff)/...`): today's agenda, full booking list with
  filters, booking detail with deliver / return / cancel / register-payment
  actions, inbox.

Cross-cutting features:
- JWT auth with automatic refresh, secret storage via `expo-secure-store`.
- Push notifications via `expo-notifications` registered against
  `POST /push/register`; tap-to-open routes to the relevant booking.
- In-app inbox with unread badge on the tab.
- ES / EN i18n with system-language detection (override in Profile).
- OTA updates via `expo-updates` (EAS Update).
- Splash + adaptive icon generated from `assets/icon.png`.

## 1. Install (locally on your dev machine)

> The Replit container is set up for the PHP backend on port 5000. To work on
> the mobile app you should `git clone` this repo on your laptop and run the
> Expo dev server there.

```bash
cd mobile
npm install
cp .env.example .env
# edit .env and set EXPO_PUBLIC_API_BASE_URL to your live API URL
# e.g. https://solutionsrentcar.com/CF-SYSTEMS/api/v1
```

Edit `app.json` once:
- Replace `REPLACE_WITH_EXPO_USERNAME` with your Expo account.
- After `eas init`, replace `REPLACE_WITH_EAS_PROJECT_ID` in **two places**
  (`extra.eas.projectId` and `updates.url`).
- Adjust `ios.bundleIdentifier` / `android.package` if you don't own
  `com.solutionsrent.app`.

## 2. Run in development

```bash
npx expo start
# press i for iOS Simulator (Mac only)
# press a for Android Emulator
# or scan the QR with the Expo Go app on a physical phone
```

> Push notifications only work on a physical device, not in simulators.

## 3. Build for stores (EAS Build)

You need a free Expo account. From `mobile/`:

```bash
npm i -g eas-cli
eas login
eas init                 # creates the EAS project + updates the projectId
eas build:configure      # confirms eas.json (already in repo)

# Internal preview build for testing on a real device:
eas build --platform android --profile preview
eas build --platform ios --profile preview

# Production builds (Play Store / App Store):
eas build --platform all --profile production
```

iOS builds require an Apple Developer Program membership ($99/yr); EAS
generates and uploads the certificates for you when you run the command.

## 4. OTA updates (EAS Update)

Once a binary is shipped to TestFlight / Play Store, JS-only changes can be
delivered with:

```bash
eas update --branch production --message "Fix booking summary spacing"
```

Users get the update next time they open the app. The `UpdatesWatcher`
component in `app/_layout.tsx` checks on every cold start.

## 5. Submit to the stores

```bash
eas submit --platform android   # uploads to internal track
eas submit --platform ios       # uploads to TestFlight
```

Before submitting:
- Fill in `eas.json` → `submit.production.ios.appleId` / `ascAppId` /
  `appleTeamId`.
- Drop your Google service-account JSON at
  `mobile/google-service-account.json` (git-ignored).
- Use `store-assets/description.es.md` and `description.en.md` for the
  listings; use `PRIVACY.md` (host it on your domain and link from both
  store consoles).

## File map

```
mobile/
├── app.json, eas.json            # Expo + EAS config
├── package.json, tsconfig.json
├── assets/                       # icon, splash, adaptive icon
├── PRIVACY.md                    # privacy policy template
├── store-assets/                 # store listing copy (ES + EN)
├── app/                          # expo-router screens
│   ├── _layout.tsx               # root nav, OTA watcher, auth gate
│   ├── index.tsx                 # welcome (client / staff buttons)
│   ├── login/{client,staff}.tsx
│   ├── (client)/                 # tabs: cars, bookings, inbox, profile
│   └── (staff)/                  # tabs: agenda, bookings, inbox, profile
└── src/
    ├── api/                      # axios client, types, JWT refresh
    ├── auth/                     # AuthContext + SecureStore token cache
    ├── i18n/                     # i18n-js + es.json / en.json
    ├── notifications/            # Expo push registration + tap routing
    ├── components/               # Button, Input, Card, Loading, Badge…
    ├── theme/colors.ts
    └── utils/format.ts
```

## How the API is wired

- The base URL comes from `EXPO_PUBLIC_API_BASE_URL` (env) and falls back to
  `extra.apiBaseUrl` in `app.json`. Both should point at the live API on the
  same domain that hosts the PHP system.
- Every request goes through `src/api/client.ts`, which attaches the bearer
  token from `SecureStore`. On a 401 it auto-rotates via `/auth/refresh`
  exactly once; if that fails too, it wipes the tokens and the Auth gate
  bounces the user back to the welcome screen.
- The unified API envelope (`{ok, data|error}`) is unwrapped in `call()`
  and turned into either the data or an `ApiError`.

## What is **not** in this commit

- Real `eas build` runs (require your Expo / Apple / Google credentials).
- Real store submission (requires Apple App Store Connect + Google Play
  Console accounts).
- Custom-designed icon / splash (currently the existing `solutions.png` logo
  is reused for all four asset slots; replace the four files in `assets/` to
  brand them properly — Expo will regenerate platform variants on build).
- Native code (the app is fully Expo-managed; no `ios/` or `android/`
  folder, no Flutter, no extra native modules).
