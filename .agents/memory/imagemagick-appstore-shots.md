---
name: ImageMagick App Store screenshot gotchas
description: Non-obvious ImageMagick (magick v7) traps when generating App Store screenshots from phone captures
---

Generating App Store assets from the app's phone screenshots (e.g. resizing the 5 marketing shots). Tooling note: `magick`/`magick identify` are available; `sharp` and the `zip` CLI are NOT — bundle with `python3 -c` + `zipfile`, or `tar`.

**Two traps cost multiple attempts — both produce a silently broken composite:**

1. **Grayscale gradient desaturates the foreground.** `magick -size WxH gradient:#1A1A1A-#000000` produces an image of `type=Grayscale` because both endpoints are gray. When you later `-composite` a COLOR screenshot onto it, the output canvas inherits the destination's grayscale type and the whole result loses color (HSL saturation drops to 0). **Fix:** force the background to color — append `-colorspace sRGB -type TrueColor` to the gradient (and optionally to the final composite). Verify with `magick out.png -colorspace HSL -channel G -separate -format "%[fx:mean]\n" info:` (should be > 0).

2. **Rounded-corner mask must be white-on-black.** For `-alpha off -compose CopyOpacity -composite`, CopyOpacity uses the mask's gray INTENSITY. A mask drawn with the default black fill (`xc:none -draw "roundrectangle ..."`) is intensity 0 inside → makes the foreground fully TRANSPARENT (composite shows only the background). **Fix:** `magick -size WxH xc:black -draw "fill white roundrectangle 0,0,W-1,H-1,R,R" mask.png`.

**Sizes used (Solution Rent Car):** iPhone 6.9" = 1320×2868 (full-bleed cover, also covers the 6.7" slot); iPad 13"/12.9" = 2048×2732. Source shots are 1290×2795 (≈ 6.7"), so iPhone is a near-lossless cover crop (~2px sides, no vertical crop). Phone shots on iPad must NOT be stretched — center the sharp screenshot (rounded corners + soft shadow) on a dark gradient background.

**Perf:** large Gaussian blur (`-blur 0x24+`) on 2048px images is slow — 5 images blew past a 120s timeout. A solid/gradient background (no blur) is both cleaner-looking and fast.
