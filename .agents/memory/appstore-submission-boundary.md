---
name: App Store submission boundary
description: What EAS submission can and cannot complete for App Store Connect review workflows.
---
EAS submission can upload a signed iOS binary to App Store Connect using an app-specific password, but it does not select that build in an editable App Store version or submit/resubmit the review. Those actions require an authenticated App Store Connect portal session or an App Store Connect API integration with suitable permissions.

**Why:** The transporter upload and App Store review workflow are separate Apple operations; an IPA being visible in TestFlight does not mean it is attached to the pending version or sent for review.

**How to apply:** Treat a successful EAS submission as upload-only. Verify the processed build in App Store Connect, attach it to the pending version, and use the review submission controls through an authorized Apple channel.