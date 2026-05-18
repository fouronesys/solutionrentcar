# Build & release — Solutions Rent Car (mobile)

This guide covers three ways to produce installable binaries:

1. **GitHub Actions → EAS Build** (push to `master` → automatic APK / IPA).
2. **Local Xcode build** for iOS (when you want a native Xcode workflow).
3. **Local Android Studio build** (optional; EAS handles this for you).

Everything ultimately uses **EAS Build** under the hood for Android +
iOS so you don't need a Mac to publish iOS, and you don't need to
maintain Java/Gradle/Cocoapods to publish Android.

---

## 1. GitHub Actions

Two workflows live in `.github/workflows/`:

| Workflow                       | Trigger                                                  | Output                                                |
| ------------------------------ | -------------------------------------------------------- | ----------------------------------------------------- |
| `mobile-android.yml`           | push to `master`/`main` touching `mobile/**`, or manual  | APK (profile `preview`) or AAB (profile `production`) |
| `mobile-ios.yml`               | push to `master`/`main`, push of `v*` tags, or manual    | IPA (profile `preview`) or store IPA (`production`)   |

Both download the finished EAS artifact and attach it to the
GitHub Actions run as a downloadable artifact (`android-preview`,
`ios-preview`, etc.).

### Required GitHub secrets

Set these in **Settings → Secrets and variables → Actions**:

| Secret                                | Required for | What it is                                                                                  |
| ------------------------------------- | ------------ | ------------------------------------------------------------------------------------------- |
| `EXPO_TOKEN`                          | both         | Personal access token from <https://expo.dev/accounts/[you]/settings/access-tokens>         |
| `EXPO_PUBLIC_API_BASE_URL`            | both         | e.g. `https://solutionsrentcar.com/CF-SYSTEMS/api/v1` — baked into the JS bundle            |
| `EXPO_APPLE_APP_SPECIFIC_PASSWORD`    | iOS submit   | Only if you later add `eas submit` to the workflow. Generated at <https://appleid.apple.com> |

That's the minimum to build. **Apple signing certificates / provisioning
profiles and the Android upload keystore are *not* GitHub secrets** — EAS
keeps them in its own secure store. You set them up once locally with:

```bash
cd mobile
eas login                # one-time, uses your Expo account
eas credentials          # interactive — generate or upload certs
```

After that, every CI build pulls the right credentials from EAS
automatically.

### What happens on push

`push` to `master` touching anything under `mobile/` →

1. CI installs deps with `npm ci` in `mobile/`.
2. `eas build --platform android --profile preview --non-interactive --wait`
   uploads the project to EAS, EAS builds it on their hosted workers,
   and the CLI streams logs.
3. The finished artifact URL is fetched with `eas build:list ... --json`
   and downloaded with `curl`.
4. `actions/upload-artifact` attaches it to the workflow run.

To build the **production** AAB / store IPA, run the workflow manually
("Run workflow" → choose `production`) or push a `v1.2.3` tag (iOS only).

---

## 2. Building iOS in Xcode locally

Expo apps are *managed* by default — there's no `ios/` folder until you
run **prebuild**. The flow is:

```bash
cd mobile
npm install
npx expo prebuild --platform ios       # generates ios/SolutionsRentCar.xcworkspace
cd ios && pod install && cd ..
open ios/SolutionsRentCar.xcworkspace   # opens Xcode
```

Inside Xcode:

1. Select the **SolutionsRentCar** scheme + a real device (or "Any iOS
   Device" for archive).
2. **Signing & Capabilities**: pick your Team (the one in
   `eas.json → submit.production.ios.appleTeamId`). Xcode will create
   the provisioning profile automatically.
3. **Product → Archive** to build for App Store / Ad Hoc.

Notes:

- Don't commit the generated `ios/` directory — it's git-ignored. Re-run
  `expo prebuild` whenever you change `app.json`, plugins, or icon/splash
  assets.
- Push notifications need the **Push Notifications** capability and an
  APNs key uploaded to Expo (`eas credentials`).
- The first launch on a real device may prompt to trust the developer
  profile (**Settings → General → VPN & Device Management**).

If you'd rather Xcode never see this and have EAS build the IPA for you,
just push to `master` and download the `ios-preview` artifact from the
GitHub Actions run, then drag it into **Transporter.app** or
TestFlight.

---

## 3. Android local build (optional)

```bash
cd mobile
npx expo prebuild --platform android
cd android
./gradlew assembleRelease    # APK in android/app/build/outputs/apk/release/
# or
./gradlew bundleRelease      # AAB for Play Store
```

This requires a JDK 17 + Android SDK. EAS handles this for you in CI, so
this path is only needed if you want to debug native Android changes.

---

## 4. EAS project setup (one-time, before the first CI build works)

The first build will fail unless these placeholders in `app.json` are
replaced:

| Placeholder                          | What to put there                                                |
| ------------------------------------ | ---------------------------------------------------------------- |
| `REPLACE_WITH_EXPO_USERNAME`         | `owner` — your Expo username or org                              |
| `REPLACE_WITH_EAS_PROJECT_ID` (×2)   | Filled in automatically by `eas init` (extra.eas + updates.url)  |
| `REPLACE_WITH_DOMAIN`                | `extra.apiBaseUrl` — your live API base URL                      |

And in `eas.json`:

| Placeholder                | What to put there                                          |
| -------------------------- | ---------------------------------------------------------- |
| `REPLACE_WITH_APPLE_ID`    | Your Apple ID email (`submit.production.ios.appleId`)      |
| `REPLACE_WITH_ASC_APP_ID`  | The App Store Connect app id (10-digit number)             |
| `REPLACE_WITH_TEAM_ID`     | Your 10-char Apple Developer Team ID                        |

Then run, locally **once**:

```bash
cd mobile
npm install
npx expo install            # syncs versions
eas login
eas init                    # creates the EAS project, writes the project id
eas credentials             # generate / upload signing for both platforms
```

After this, CI builds succeed end-to-end without any further interaction.

---

## 5. OTA updates (no rebuild)

Once a binary is on TestFlight / Play Store, you don't need to rebuild
for JS-only changes:

```bash
cd mobile
eas update --branch production --message "Booking summary spacing"
```

The `UpdatesWatcher` in `app/_layout.tsx` pulls the update on the next
cold start.

---

## Troubleshooting

| Symptom                                                 | Fix                                                                                       |
| ------------------------------------------------------- | ----------------------------------------------------------------------------------------- |
| CI: `Error: ENOENT … package-lock.json`                 | Run `npm install` locally and commit `mobile/package-lock.json`.                          |
| CI: `Expo error: project owner missing`                 | Replace `REPLACE_WITH_EXPO_USERNAME` in `app.json → owner`.                               |
| CI: `Error: Project does not exist`                     | Run `eas init` locally inside `mobile/` and commit the updated `app.json`.                |
| iOS build fails on signing                              | Run `eas credentials` locally and choose **Set up a new Distribution Certificate**.       |
| Android build fails on missing keystore                 | Run `eas credentials --platform android` and let EAS generate one (saved to EAS, not git).|
| Push notifications never arrive                         | Upload an APNs key with `eas credentials` for iOS; for Android make sure FCM is enabled.  |
| `__DEV__` keys appearing in the binary                  | You're building the `development` profile by accident — pass `--profile preview/production`.|
