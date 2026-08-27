---
name: EAS remote versioning
description: How EAS chooses iOS build numbers when the project uses remote app versioning.
---
When `appVersionSource` is `remote`, the iOS `buildNumber` in static app configuration is metadata only; EAS increments and assigns the remote project sequence to the binary.

**Why:** A production build can report a different build number than the value committed in `app.json`, even though the source configuration is correct.

**How to apply:** Verify the assigned build number in the EAS/GitHub build log and in App Store Connect; do not assume the local `buildNumber` is the binary’s final number.