#!/usr/bin/env bash
# Decodes the upload keystore from a base64 secret and drops it where
# Gradle expects it. Configures build.gradle to read the credentials
# from gradle.properties so we don't have to patch the generated file.
#
# Required env vars:
#   KEYSTORE_BASE64       — `base64 -i upload.keystore`
#   KEYSTORE_PASSWORD     — store password
#   KEY_ALIAS             — alias inside the keystore (usually "upload")
#   KEY_PASSWORD          — alias password
#
# The matching Gradle properties (read via `signingConfigs.release`) are
# already passed as ORG_GRADLE_PROJECT_* env vars from the workflow.

set -euo pipefail

if [[ -z "${KEYSTORE_BASE64:-}" ]]; then
  echo "::error::ANDROID_KEYSTORE_BASE64 secret is not set"
  exit 1
fi

KEYSTORE_PATH="$(pwd)/android/app/upload.keystore"
mkdir -p "$(dirname "$KEYSTORE_PATH")"
echo "$KEYSTORE_BASE64" | base64 --decode > "$KEYSTORE_PATH"

# Append release signingConfig + buildType to the generated build.gradle
# (expo prebuild does not include one because the app is unsigned by default).
APP_GRADLE="$(pwd)/android/app/build.gradle"
if ! grep -q "SOLUTIONS_RENT_CAR_UPLOAD_STORE_FILE" "$APP_GRADLE"; then
  cat >> "$APP_GRADLE" <<'GRADLE'

android {
    signingConfigs {
        release {
            if (project.hasProperty('SOLUTIONS_RENT_CAR_UPLOAD_STORE_FILE')) {
                storeFile     file(SOLUTIONS_RENT_CAR_UPLOAD_STORE_FILE)
                storePassword SOLUTIONS_RENT_CAR_UPLOAD_STORE_PASSWORD
                keyAlias      SOLUTIONS_RENT_CAR_UPLOAD_KEY_ALIAS
                keyPassword   SOLUTIONS_RENT_CAR_UPLOAD_KEY_PASSWORD
            }
        }
    }
    buildTypes {
        release {
            signingConfig signingConfigs.release
        }
    }
}
GRADLE
fi

echo "✓ Imported upload keystore at $KEYSTORE_PATH"
