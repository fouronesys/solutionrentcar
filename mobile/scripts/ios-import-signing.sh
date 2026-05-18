#!/usr/bin/env bash
# Imports the Apple Distribution certificate (.p12) and a provisioning
# profile into a temporary keychain so xcodebuild can sign the archive.
#
# Required env vars (all set by the CI workflow from GitHub secrets):
#   BUILD_CERTIFICATE_BASE64        — `base64 -i Distribution.p12`
#   P12_PASSWORD                    — password used when exporting the .p12
#   BUILD_PROVISION_PROFILE_BASE64  — `base64 -i SolutionsRentCar_AdHoc.mobileprovision`
#   PROVISION_PROFILE_UUID          — UUID inside the .mobileprovision file
#   KEYCHAIN_PATH                   — where to create the temp keychain
#   KEYCHAIN_PASSWORD               — temp keychain password (ephemeral)
#
# After running, xcodebuild will find the identity in the temp keychain
# and the provisioning profile in ~/Library/MobileDevice/Provisioning Profiles.

set -euo pipefail

if [[ -z "${BUILD_CERTIFICATE_BASE64:-}" ]]; then
  echo "::error::IOS_DIST_CERT_P12_BASE64 secret is not set"
  exit 1
fi
if [[ -z "${BUILD_PROVISION_PROFILE_BASE64:-}" ]]; then
  echo "::error::IOS_PROVISIONING_PROFILE_BASE64 secret is not set"
  exit 1
fi

CERT_PATH="${RUNNER_TEMP:-/tmp}/distribution.p12"
PROFILE_PATH="${RUNNER_TEMP:-/tmp}/profile.mobileprovision"

echo "$BUILD_CERTIFICATE_BASE64" | base64 --decode > "$CERT_PATH"
echo "$BUILD_PROVISION_PROFILE_BASE64" | base64 --decode > "$PROFILE_PATH"

# 1. Create temp keychain
security create-keychain -p "$KEYCHAIN_PASSWORD" "$KEYCHAIN_PATH"
security set-keychain-settings -lut 21600 "$KEYCHAIN_PATH"
security unlock-keychain -p "$KEYCHAIN_PASSWORD" "$KEYCHAIN_PATH"

# 2. Add it to the search list (so xcodebuild can find it)
ORIGINAL_LIST=$(security list-keychains -d user | sed -e 's/[[:space:]]*"//g' -e 's/"//g')
security list-keychains -d user -s "$KEYCHAIN_PATH" $ORIGINAL_LIST

# 3. Import the certificate
security import "$CERT_PATH" \
  -k "$KEYCHAIN_PATH" \
  -P "$P12_PASSWORD" \
  -A -t cert -f pkcs12

# 4. Allow codesign to use the key without a UI prompt
security set-key-partition-list \
  -S apple-tool:,apple: \
  -k "$KEYCHAIN_PASSWORD" \
  "$KEYCHAIN_PATH" > /dev/null

# 5. Install the provisioning profile
PROFILE_DIR="$HOME/Library/MobileDevice/Provisioning Profiles"
mkdir -p "$PROFILE_DIR"
cp "$PROFILE_PATH" "$PROFILE_DIR/${PROVISION_PROFILE_UUID}.mobileprovision"

echo "✓ Imported signing certificate and provisioning profile $PROVISION_PROFILE_UUID"

# Clean up the decoded files
rm -f "$CERT_PATH" "$PROFILE_PATH"
