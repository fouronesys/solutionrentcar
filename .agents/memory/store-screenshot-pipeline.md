---
name: Store screenshot pipeline
description: How to capture/compose App Store & Play screenshots for the Expo web app, and the /tmp + chromium pitfalls.
---
- Capture via headless chromium + CDP scripts (pattern in session): emulate 430×932@3 (iOS 1290×2796) or 360×640@3 (Play 1080×1920), locale es-DO, TZ Santo Domingo.
- **Why:** two recurring failures — background chromium dies between ShellExec calls (must launch with `setsid ... &` in the SAME command as the client), and `/tmp` files vanish between calls.
- **How to apply:** always launch browser + driver in one shell command, and copy every capture into `store-screenshots/` in the workspace immediately after the run.
- Hiding the tab bar via a temp flag in `mobile/app/(client)/_layout.tsx` takes effect on Metro's next on-demand rebuild even without a workflow restart — capture state can silently flip after an edit; verify a capture before composing.
- User preferences: clean screenshots must show NO added design; branded set uses app's dark+red branding, one phrase split across screenshots with cropped peek words; Dynamic Island must not cover app content (pad image down inside the frame); temporarily swap DB car photos for stock images via CDP img-src injection.
