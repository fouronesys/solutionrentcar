---
name: Rent-car API web-preview CORS
description: Why the rent-car-mobile web preview shows no data, and how to actually validate it.
---

The `rent-car-mobile` Expo app talks to an external backend (`solutionsrentcar.do/.../api/v1`). That host does not send CORS headers for the Expo web preview origin, so in the browser preview the car list comes back empty ("0 vehicles") and protected calls fail with 401 / "Token requerido o inválido".

**Why:** the API is third-party/external; we cannot change its CORS policy. The browser blocks it; native (Expo Go) does not.

**How to apply:**
- Do NOT treat an empty list / 401 in the *web* preview as a bug. The app chrome (heros, search, filters, skeletons, tab bar, auth screens) still renders correctly there, so web screenshots are fine for validating *design/layout*.
- To validate *data flow* (browse → detail → reserve, bookings, staff agenda/payments), run on a real device via Expo Go, not the web preview.
- After adding native modules (e.g. expo-linear-gradient, @expo/vector-icons, fonts), restart the `artifacts/rent-car-mobile: expo` workflow before screenshotting so Metro reloads them.
