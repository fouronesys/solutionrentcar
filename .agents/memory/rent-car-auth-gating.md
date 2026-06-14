---
name: Rent-car auth gating + guest catalog
description: solutionsrentcar.do API needs a Bearer token for ALL endpoints incl. GET /cars; catalog browsing without login uses an embedded read-only guest account auto-login.
---

# Rent-car: every endpoint is auth-gated, catalog uses a guest token

The external production API (`solutionsrentcar.do/CF-SYSTEMS/api/v1`) returns 401 `{"ok":false,"error":{"code":"unauthorized","message":"Token requerido o inválido"}}` for **every** endpoint without a valid Bearer token — including `GET /cars`. There is **no public car endpoint** (`/public/cars`,`/vehicles`,`/auth/guest` → 404; the public website lists cars via server-rendered PHP, not this API).

## Guest-catalog design (so catalog is browsable without login)
The owner chose NOT to add a backend public endpoint. Instead the app silently logs in with a dedicated **read-only "guest" client account** to obtain a token used only for browsing.

- Creds live in `EXPO_PUBLIC_GUEST_USERNAME` / `EXPO_PUBLIC_GUEST_PASSWORD` (shared env vars, inlined into the bundle — intentionally public, not secret). `src/auth/guest.ts` exposes `guestEnabled`.
- `src/api/client.ts`: request interceptor uses the real token if present, else the guest token. `ensureGuestSession()` logs in as guest when no guest token exists. On 401 with no real `refresh_token`, it re-acquires the guest token and retries once.
- `AuthContext` calls `ensureGuestSession()` on bootstrap (when no real session) and after logout. **Guest keeps `role === null`** — it does NOT set a role.
- The screens already gate on `role`: `bookings`/`profile`/`notifications` render `LoginPrompt` and `book/[carId]` renders `LoginRequired` when `role === null`; `NotificationsContext` skips polling when `!role`. So a guest can browse the catalog but is forced into a REAL login at reservation / personal tabs — no code in those screens needed changing.
- `app/_layout.tsx` Gate: logged-out users (`!role`) go to `/(client)/cars` (catalog); only the staff area redirects to `/login/staff`. (This REVERSED the earlier "always send logged-out users to login" rule.)

**Why:** the original app was designed for public browsing (screens gate on role), but production requires a token for `/cars`, so the catalog 401'd. The guest token fills that gap without a backend change.

**Gotchas / how to apply:**
- The `/auth/login` HTTP body is wrapped: `{ ok, data: { role, user, tokens:{access_token,...} } }`. Parse guest login as `res.data.data.tokens` (NOT `res.data.tokens`). A reviewer may flag this as a bug — it is correct because the body is wrapped.
- Do NOT make the 401 guest-fallback retry fire after a real refresh fails — that could replay a real user's mutation against the shared guest account. Guest fallback is only for the no-real-session path.
- **Hard requirement:** the guest account MUST be read-only / no mutation permission server-side, since its public creds ship in the bundle. The app cannot enforce this.
- Web preview still can't hit the external API (CORS) — validate the guest catalog on a real device via Expo Go.
