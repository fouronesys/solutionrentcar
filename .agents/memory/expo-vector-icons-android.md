---
name: Expo vector-icons font load on Android
description: Android shows CJK/tofu glyphs + missing icons unless the icon font is loaded via useFonts.
---

# @expo/vector-icons glyphs broken on Android

Symptom: on Android, `@expo/vector-icons` (e.g. Ionicons) icons render as **Chinese/tofu characters** in buttons and sections, or don't show at all. iOS looks fine.

**Why:** the icon font isn't guaranteed loaded before first render on Android; the icon code points fall back to a CJK system font. iOS is more forgiving and loads/re-renders gracefully.

**How to apply:** load the icon font alongside app fonts in the root `useFonts()` (and gate render on it):
```ts
import { Ionicons } from "@expo/vector-icons";
const [loaded] = useFonts({ ...Ionicons.font, /* Inter_... */ });
if (!loaded) return null;
```
Add `...<IconSet>.font` for every icon set used. This is the standard fix; don't chase per-component hacks.
