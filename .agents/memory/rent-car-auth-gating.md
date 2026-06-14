---
name: Rent-car auth gating (external API requires token everywhere)
description: The solutionsrentcar.do API needs a Bearer token for ALL endpoints incl. GET /cars; logged-out users must go to login.
---

# Rent-car: every endpoint is auth-gated

The external production API (`solutionsrentcar.do/CF-SYSTEMS/api/v1`) returns 401 `{"ok":false,"error":{"code":"unauthorized","message":"Token requerido o inválido"}}` for **every** endpoint without a valid Bearer token — including `GET /cars`. There is **no public car browsing**.

**Why:** symptom "token requerido o inválido" on app open was the root-layout `Gate` sending logged-out users (no role) straight to the authenticated screen `/(client)/cars`, which fires `GET /cars` → 401.

**How to apply:** in `app/_layout.tsx` Gate, any user with no role who is not already on an auth screen must be redirected to login (`/login/staff` if they were in the staff area, else `/login/client`) — never to a `(client)`/`(staff)` screen. Don't add a public cars list unless the backend changes (and we were told not to build a backend).
