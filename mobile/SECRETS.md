# Required GitHub Actions secrets — Solutions Rent Car mobile

Set these in **Settings → Secrets and variables → Actions → New
repository secret**. Each row is marked with which workflow(s) actually
need it.

Legend:
- 📦 `mobile-android.yml`   — Android build via EAS (cloud)
- 📦 `mobile-ios.yml`       — iOS build via EAS (cloud)
- 🛠️ `mobile-android-gradle.yml` — Android build via Gradle on Ubuntu
- 🛠️ `mobile-ios-xcode.yml`     — iOS build via Xcode on macOS

| Secret name                                | Used by               | What it is                                                                                          |
| ------------------------------------------ | --------------------- | --------------------------------------------------------------------------------------------------- |
| `EXPO_TOKEN`                               | 📦 📦                  | Personal access token from <https://expo.dev/accounts/[you]/settings/access-tokens>                  |
| `EXPO_PUBLIC_API_BASE_URL`                 | 📦 📦 🛠️ 🛠️             | Live API base URL, e.g. `https://solutionsrentcar.com/CF-SYSTEMS/api/v1` — baked into the JS bundle |
| `EXPO_APPLE_APP_SPECIFIC_PASSWORD`         | 📦 (iOS submit only)  | App-specific password from <https://appleid.apple.com>                                              |
| `IOS_TEAM_ID`                              | 🛠️ (iOS)              | 10-char Apple Developer team identifier                                                              |
| `IOS_BUNDLE_ID`                            | 🛠️ (iOS)              | The app's bundle id (must match `app.json → ios.bundleIdentifier`)                                  |
| `IOS_DIST_CERT_P12_BASE64`                 | 🛠️ (iOS)              | `base64 -i Distribution.p12` of your Apple Distribution certificate                                  |
| `IOS_DIST_CERT_P12_PASSWORD`               | 🛠️ (iOS)              | Password used when you exported the .p12 from Keychain Access                                       |
| `IOS_PROVISIONING_PROFILE_BASE64`          | 🛠️ (iOS)              | `base64 -i SolutionsRentCar.mobileprovision` (ad-hoc or App Store profile)                          |
| `IOS_PROVISIONING_PROFILE_UUID`            | 🛠️ (iOS)              | UUID inside the .mobileprovision (see one-liner below to extract)                                   |
| `ANDROID_KEYSTORE_BASE64`                  | 🛠️ (Android)          | `base64 -i upload.keystore` of your Play upload key                                                  |
| `ANDROID_KEYSTORE_PASSWORD`                | 🛠️ (Android)          | Store password for the keystore                                                                     |
| `ANDROID_KEY_ALIAS`                        | 🛠️ (Android)          | Alias inside the keystore (commonly `upload`)                                                       |
| `ANDROID_KEY_PASSWORD`                     | 🛠️ (Android)          | Password for the alias                                                                              |

If you go with the **EAS-only path** (📦 workflows), you only need the
first two rows; EAS handles all signing for you and stores the certs
itself.

If you go with the **native path** (🛠️ workflows), you also need to
generate the certs yourself — see step-by-step below.

---

## Generating each secret value

### `EXPO_TOKEN`

1. Visit <https://expo.dev/accounts/[you]/settings/access-tokens>.
2. **Create token** → copy the value — you only see it once.
3. Paste into the GitHub secret `EXPO_TOKEN`.

### `EXPO_APPLE_APP_SPECIFIC_PASSWORD`

Only needed if you later add `eas submit --platform ios` to CI.

1. Sign in at <https://appleid.apple.com> with the Apple ID that owns
   the app.
2. Under **Sign-In and Security → App-Specific Passwords**, create one
   labelled "Solutions Rent Car CI".
3. Copy the generated `xxxx-xxxx-xxxx-xxxx` string into the secret.

### iOS Distribution Certificate (`.p12`)

You need an **Apple Developer Program** account ($99/yr).

```bash
# On your Mac, using Keychain Access:
#   1. Keychain Access → Certificate Assistant → "Request a Certificate
#      from a Certificate Authority". Save the .certSigningRequest file.
#   2. https://developer.apple.com/account → Certificates → +
#      → Apple Distribution → upload the CSR → download the .cer.
#   3. Double-click the .cer to install it into login keychain.
#   4. In Keychain Access, expand the "Apple Distribution: <Name>" row,
#      right-click both rows → Export 2 items → Personal Information
#      Exchange (.p12). Set a strong password — save it as the secret
#      IOS_DIST_CERT_P12_PASSWORD.
# Then base64-encode it for GitHub:
base64 -i Distribution.p12 | pbcopy   # paste into IOS_DIST_CERT_P12_BASE64
```

### iOS Provisioning Profile (`.mobileprovision`)

1. <https://developer.apple.com/account> → **Profiles → +**.
2. Pick **Ad Hoc** for `preview`, or **App Store** for `production`.
3. Select your app id (e.g. `com.solutionsrent.app`).
4. Select the Distribution certificate you just created.
5. For Ad Hoc: pick the devices to provision.
6. Download the `.mobileprovision`.

```bash
# Encode for GitHub
base64 -i SolutionsRentCar.mobileprovision | pbcopy   # → IOS_PROVISIONING_PROFILE_BASE64

# Extract the UUID (paste into IOS_PROVISIONING_PROFILE_UUID)
security cms -D -i SolutionsRentCar.mobileprovision \
  | plutil -extract UUID raw -
```

### Android upload keystore

Only needed for the native Gradle workflow (EAS keeps its own keystore).

```bash
# Generate a fresh keystore (one-time; back it up somewhere safe!)
keytool -genkeypair -v \
  -keystore upload.keystore \
  -alias upload \
  -keyalg RSA -keysize 2048 -validity 10000 \
  -storepass <YOUR_STORE_PASSWORD> \
  -keypass <YOUR_KEY_PASSWORD> \
  -dname "CN=Solutions Rent Car, O=Solutions Rent Car, C=DO"

# Encode for GitHub:
base64 -i upload.keystore | pbcopy   # → ANDROID_KEYSTORE_BASE64
```

Then set:
- `ANDROID_KEYSTORE_PASSWORD` = `<YOUR_STORE_PASSWORD>`
- `ANDROID_KEY_ALIAS`         = `upload`
- `ANDROID_KEY_PASSWORD`      = `<YOUR_KEY_PASSWORD>`

> ⚠️ If you publish to Play Console, you **must** keep this keystore
> safe forever — losing it means you can never update the app on the
> same listing. Store it in a password manager and back it up.
