---
name: Rent-car master/main split & delivery
description: Why feature ports to the real Solution Rent Car app must be delivered as a manual apply-bundle, not a git push.
---

# Rent-car: external master vs Replit main, and how to deliver

The user's REAL app lives on the `master` branch (external GitHub: PHP backend + mature `mobile/` Expo app with App Store work). The Replit monorepo is on `main`. **`master` and `main` share NO common ancestor** (empty merge-base) — they are unrelated histories.

**Why:** Direct git commit/checkout/worktree/push/`rm` of `.git` files is BLOCKED for the main agent ("Destructive git operations are not allowed"). Project Tasks only merge into the monorepo `main`, never the external `master`. So there is no automated path to land changes on the user's real app.

**How to apply:** Deliver edits as an apply-ready archive that mirrors the `mobile/` folder, plus copy/paste git commands the user runs on their own Mac. To typecheck before delivery, work in a snapshot extracted via `git archive` that includes the committed `node_modules` (has `.bin/tsc`), so `node_modules/.bin/tsc --noEmit` works without install.

## Guest catalog browsing requires env vars
The guest-session feature needs `EXPO_PUBLIC_GUEST_USERNAME` / `EXPO_PUBLIC_GUEST_PASSWORD` pointing to a read-only client account on their backend (their App Store review "appdemo" account works). Without them, `guestEnabled` is false and the catalog won't load for logged-out users.

## Tooling gotchas in this env
- `zip` is NOT installed; use `tar czf` for bundles.
- The real app's `src/notifications/push.ts` has a pre-existing `NotificationBehavior` type error (missing `shouldShowBanner`/`shouldShowList`) from expo-notifications SDK drift — unrelated to feature work; don't "fix" it as part of a scoped port.
