# Build & release — Solutions Rent Car (mobile)

This guide covers every way to produce installable binaries for the app.
There are two CI paths and one local path. Pick whichever fits.

| Path                                | Where it runs                     | iOS signing handled by | Android signing handled by |
| ----------------------------------- | --------------------------------- | ---------------------- | -------------------------- |
| **A. EAS Build (recommended)**      | Expo's hosted workers, driven from a tiny ubuntu CI job | EAS (via `eas credentials`) | EAS (via `eas credentials`) |
| **B. Native (Xcode + Gradle on CI)**| `macos-14` + `ubuntu-latest` runners on GitHub Actions | You — secrets in GitHub Actions | You — secrets in GitHub Actions |
| **C. Local Xcode / Android Studio** | Your laptop                       | Xcode (with your Apple ID)      | Android Studio / `keytool` |

All three end up with the same app — they just differ in *who holds the
signing keys* and *who pays for the compile time*.

> **First-timer?** Use **Path A** — it's a single `EXPO_TOKEN` secret
> and EAS does the rest. Switch to **B** only if you specifically want
> to keep certs in GitHub or if you don't want a dependency on Expo's
> build infra.

---

## Workflows in this repo

| File                                              | Path | Trigger                                                                     | Output                  |
| ------------------------------------------------- | ---- | --------------------------------------------------------------------------- | ----------------------- |
| `.github/workflows/mobile-android.yml`            | A    | push to `master`/`main` touching `mobile/**`, or manual                     | APK / AAB via EAS       |
| `.github/workflows/mobile-ios.yml`                | A    | push to `master`/`main`, push of `v*` tags, or manual                       | IPA via EAS             |
| `.github/workflows/mobile-android-gradle.yml`     | B    | push to `master`/`main`, push of `v*` tags, or manual                       | APK / AAB via Gradle    |
| `.github/workflows/mobile-ios-xcode.yml`          | B    | push to `master`/`main`, push of `v*` tags, or manual                       | IPA via Xcode (macOS)   |

All four upload the binary to the workflow run as a downloadable
GitHub Actions artifact (`android-preview`, `ios-xcode-ad-hoc`, …).

> The two workflow pairs are intentionally independent. You can disable
> the ones you don't want by either deleting the file or renaming it to
> `.yml.disabled`.

---

## Required GitHub secrets

Quick checklist — full setup instructions are in [`SECRETS.md`](./SECRETS.md).

**Path A (EAS only):**
- `EXPO_TOKEN`
- `EXPO_PUBLIC_API_BASE_URL`

**Path B adds (native):**
- iOS: `IOS_TEAM_ID`, `IOS_BUNDLE_ID`, `IOS_DIST_CERT_P12_BASE64`,
  `IOS_DIST_CERT_P12_PASSWORD`, `IOS_PROVISIONING_PROFILE_BASE64`,
  `IOS_PROVISIONING_PROFILE_UUID`
- Android: `ANDROID_KEYSTORE_BASE64`, `ANDROID_KEYSTORE_PASSWORD`,
  `ANDROID_KEY_ALIAS`, `ANDROID_KEY_PASSWORD`

---

## Path A — EAS Build (cloud)

### One-time setup (local, ~5 minutes)

```bash
cd mobile
npm install
npm i -g eas-cli
eas login
eas init                 # creates the EAS project, writes the project id
                         # into app.json (extra.eas.projectId + updates.url)
eas credentials          # generates / uploads signing for both platforms
                         # — answer the prompts; EAS stores everything itself
```

Then replace these placeholders in `app.json`:

| Placeholder                          | Replace with                                                     |
| ------------------------------------ | ---------------------------------------------------------------- |
| `REPLACE_WITH_EXPO_USERNAME`         | Your Expo account / org (used in `owner`)                        |
| `REPLACE_WITH_EAS_PROJECT_ID` (×2)   | Filled in automatically by `eas init` (extra.eas + updates.url)  |
| `REPLACE_WITH_DOMAIN`                | `extra.apiBaseUrl` — your live API base URL                      |

And in `eas.json` (only required if you'll use `eas submit`):

| Placeholder                | Replace with                                                |
| -------------------------- | ----------------------------------------------------------- |
| `REPLACE_WITH_APPLE_ID`    | Your Apple ID email (`submit.production.ios.appleId`)       |
| `REPLACE_WITH_ASC_APP_ID`  | App Store Connect app id (10-digit number)                  |
| `REPLACE_WITH_TEAM_ID`     | Your 10-char Apple Developer Team ID                        |

Commit the updated `app.json` and push. CI builds work end-to-end after
this — every push to `master` touching `mobile/**` produces an APK +
IPA on the run page.

### Manual triggers

- "Run workflow" on `mobile-android.yml` → pick `preview` (APK) or
  `production` (AAB).
- Push a `v1.2.3` tag → triggers the production iOS build.

---

## Path B — Native CI (Xcode + Gradle on GitHub-hosted runners)

Use this when you want signing material to live in GitHub Actions
secrets instead of EAS, or to keep a self-contained build pipeline.

### How it works

Both native workflows:

1. Check out the repo.
2. `npm ci` inside `mobile/`.
3. `npx expo prebuild --platform <plat> --no-install --clean` to
   materialize the `ios/` or `android/` project.
4. Decode signing material from secrets onto disk
   (`scripts/ios-import-signing.sh` or `scripts/android-import-signing.sh`).
5. Build with `xcodebuild` / `gradlew`.
6. Upload the `.ipa` / `.apk` / `.aab` as an Actions artifact.

### One-time setup (you generate the certs yourself)

Walk through [`SECRETS.md`](./SECRETS.md) — it tells you exactly what
to click in the Apple Developer portal and what to run with `keytool`,
and how to `base64`-encode each file for the GitHub secret.

### Cost note

- `mobile-android-gradle.yml` runs on ubuntu — same price as any other
  CI job.
- `mobile-ios-xcode.yml` runs on `macos-14` — **macOS minutes are
  billed at 10× the ubuntu rate** on GitHub-hosted runners. If you
  build a lot, Path A (EAS) is usually cheaper.

---

## Path C — Local Xcode / Android Studio

Useful for debugging native crashes, native-module work, or iterating
on the build settings.

### iOS (Xcode)

Expo apps are *managed* by default — there's no `ios/` folder until you
run **prebuild**. Generate it on demand:

```bash
cd mobile
npm install
npx expo prebuild --platform ios       # generates ios/SolutionsRentCar.xcworkspace
cd ios && pod install && cd ..
open ios/SolutionsRentCar.xcworkspace   # opens Xcode
```

Inside Xcode:

1. Select the **SolutionsRentCar** scheme + a real device (or
   "Any iOS Device" for archive).
2. **Signing & Capabilities**: pick your Team. With **Automatically
   manage signing** checked, Xcode creates the dev provisioning profile
   for you.
3. **Product → Archive** to build for App Store / Ad Hoc.
4. **Window → Organizer → Distribute App** to export the `.ipa` or
   upload to TestFlight via Transporter.app.

Notes:

- The generated `ios/` directory is git-ignored (regenerate it any time
  you change `app.json`, plugins, or icon/splash assets).
- Push notifications require **Push Notifications** capability + an
  APNs key uploaded to Expo (`eas credentials`) or to App Store Connect.
- First launch on a real device may prompt to trust the developer
  profile under **Settings → General → VPN & Device Management**.

### Android (Android Studio / Gradle CLI)

```bash
cd mobile
npx expo prebuild --platform android
cd android
./gradlew assembleRelease    # APK in android/app/build/outputs/apk/release/
# or
./gradlew bundleRelease      # AAB for Play Store
```

Requires JDK 17 + Android SDK. Android Studio handles signing
configuration via **Build → Generate Signed Bundle / APK**.

---

## OTA updates (no rebuild) — works with any path

Once a binary is on TestFlight / Play Store, JS-only changes can be
shipped without a rebuild:

```bash
cd mobile
eas update --branch production --message "Booking summary spacing"
```

The `UpdatesWatcher` in `app/_layout.tsx` pulls the update on the next
cold start.

---

## End-to-end checklist (do this once before the first CI build)

- [ ] `cd mobile && npm install && npm i -g eas-cli`
- [ ] `eas login`, `eas init` — replaces the EAS project id placeholders
- [ ] Replace `REPLACE_WITH_EXPO_USERNAME` and `REPLACE_WITH_DOMAIN` in `app.json`
- [ ] Replace `REPLACE_WITH_*` Apple values in `eas.json` (only if using `eas submit`)
- [ ] **Path A users:** add GitHub secrets `EXPO_TOKEN` + `EXPO_PUBLIC_API_BASE_URL`
- [ ] **Path B users:** also add all signing-related secrets from [`SECRETS.md`](./SECRETS.md)
- [ ] Commit + push `app.json` and `eas.json`
- [ ] Watch the first CI run in **Actions** — the binary will be attached as an artifact

---

## Troubleshooting

| Symptom                                                 | Fix                                                                                       |
| ------------------------------------------------------- | ----------------------------------------------------------------------------------------- |
| CI: `Error: ENOENT … package-lock.json`                 | Run `npm install` locally and commit `mobile/package-lock.json`.                          |
| CI: `Expo error: project owner missing`                 | Replace `REPLACE_WITH_EXPO_USERNAME` in `app.json → owner`.                               |
| CI: `Error: Project does not exist`                     | Run `eas init` locally inside `mobile/` and commit the updated `app.json`.                |
| iOS build fails on signing                              | Run `eas credentials` locally (Path A) or check the P12 password / profile UUID (Path B). |
| Android build fails on missing keystore                 | Path A: run `eas credentials --platform android`. Path B: verify `ANDROID_KEYSTORE_BASE64`. |
| Xcode workflow: `No matching provisioning profile`      | Bundle id in `app.json` must equal the one in the .mobileprovision (and `IOS_BUNDLE_ID`).  |
| Gradle: `Keystore was tampered with, or password incorrect` | The base64 was corrupted in copy/paste — re-run `base64 -i upload.keystore \| pbcopy`.   |
| Push notifications never arrive                         | Upload an APNs key with `eas credentials` for iOS; for Android make sure FCM is enabled.  |
| `__DEV__` keys appearing in the binary                  | You built the `development` profile by accident — pass `--profile preview/production`.    |
