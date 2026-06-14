---
name: Rent-car brand palette
description: The Solution Rent Car app uses a black + gold palette, never navy/blue.
---

# Solution Rent Car palette

The brand palette is **black + gold (yellow)**, matching the company logo. **Never use navy/blue chrome.**

**Why:** The user explicitly rejected the earlier navy/blue (`#0F172A`/`#0B1220`/`#1E293B`) theme — "el color debe ser negro o amarillo, no azul". Blue reads as generic/unprofessional for this brand.

**How to apply:**
- All color tokens live in `src/theme/colors.ts` — change them there, not per-screen. Dark surfaces are true black/charcoal (`#000000`/`#141414`/`#1C1C1C`), gold primary is the `#F5B301` family, neutrals are zinc (not slate, to avoid blue tint).
- Semantic statuses avoid blue too: Confirmed/Maintenance/Returned/info use black or zinc grays, not blue.
- When adding new screens, watch for hardcoded blue-leaning hexes (e.g. slate `#E2E8F0`, `#94A3B8`, `rgba(11,18,32,...)`) — use zinc/black equivalents or the theme tokens instead.
