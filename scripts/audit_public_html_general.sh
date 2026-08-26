#!/usr/bin/env bash
set -euo pipefail
set +x

: "${FTP_HOST:?missing FTP_HOST}"
: "${FTP_USER:?missing FTP_USER}"
: "${FTP_PASSWORD:?missing FTP_PASSWORD}"

run_lftp() {
  LFTP_PASSWORD="$FTP_PASSWORD" lftp -u "$FTP_USER" --env-password "$FTP_HOST" -e \
    "set ssl:verify-certificate no; set ftp:ssl-allow no; set net:max-retries 1; set net:timeout 30; $1; bye"
}

mkdir -p /tmp/general-auth out
run_lftp "cls -la /" >out/general-root-listing.txt 2>&1

if grep -Eq '[[:space:]]/public_html/$' out/general-root-listing.txt; then
  remote_root="/public_html"
else
  remote_root="/"
fi
printf 'remote_root=%s\n' "$remote_root"

: >/tmp/general-auth-paths.txt
for candidate in \
  "$remote_root/index.php" \
  "$remote_root/login.php" \
  "$remote_root/default.php" \
  "$remote_root/CF-SYSTEMS/api/v1/index.php" \
  "$remote_root/CF-SYSTEMS/api/v1/bootstrap.php" \
  "$remote_root/CF-SYSTEMS/api/v1/handlers/auth.php" \
  "$remote_root/CF-SYSTEMS/api/v1/lib/ApiAuth.php" \
  "$remote_root/CF-SYSTEMS/api/v1/lib/ApiResponse.php" \
  "$remote_root/CF-SYSTEMS/api/v1/lib/Jwt.php" \
  "$remote_root/CF-SYSTEMS/api/v1/.htaccess"; do
  printf '%s\n' "$candidate" >>/tmp/general-auth-paths.txt
done

if run_lftp "find $remote_root/CF-SYSTEMS" > /tmp/general-find.txt 2>/tmp/general-find-error.txt; then
  grep -Ei '/(auth|login|session|user|token|jwt)[^/]*\.(php|htaccess)$' \
    /tmp/general-find.txt >>/tmp/general-auth-paths.txt || true
fi
sort -u /tmp/general-auth-paths.txt -o /tmp/general-auth-paths.txt

: >/tmp/general-auth-map.tsv
index=0
while IFS= read -r remote_path; do
  [ -n "$remote_path" ] || continue
  index=$((index + 1))
  local_path="/tmp/general-auth/${index}.source"
  if ! run_lftp "get $remote_path -o $local_path" >/tmp/general-get.log 2>&1; then
    continue
  fi
  if [ -s "$local_path" ]; then
    printf '%s\t%s\n' "$index" "$remote_path" >>/tmp/general-auth-map.tsv
  fi
done </tmp/general-auth-paths.txt

python3 - /tmp/general-auth-map.tsv /tmp/general-auth out/general-login-audit.md <<'PY'
import hashlib
import re
import sys
from pathlib import Path

mapping = Path(sys.argv[1])
source_dir = Path(sys.argv[2])
report = Path(sys.argv[3])
rows = []
for row in mapping.read_text().splitlines():
    index, remote_path = row.split("\t", 1)
    source = source_dir / f"{index}.source"
    text = source.read_text(errors="replace")
    signals = []
    if re.search(r"\b(?:md5|sha1)\s*\(", text, re.I):
        signals.append("hash heredado")
    if re.search(r"(?:password|passwd|pwd|dbpass)\s*(?:=|=>)\s*['\"][^'\"]{4,}['\"]", text, re.I):
        signals.append("posible secreto fijo")
    if re.search(r"\bcors\b.{0,80}\*", text, re.I | re.S) or "Access-Control-Allow-Origin: *" in text:
        signals.append("CORS abierto")
    if re.search(r"\b(?:mysql_query|mysqli_query|query)\s*\([^;]*(?:\.\s*\$|\$\w+\s*\.)", text, re.I | re.S):
        signals.append("SQL construido dinámicamente")
    if "session_start" in text.lower() and not re.search(r"(?:session_set_cookie_params|setcookie)\s*\(", text, re.I):
        signals.append("cookie de sesión no visible")
    syntax = "no verificado"
    digest = hashlib.sha256(source.read_bytes()).hexdigest()
    rows.append((remote_path, source, text, digest, syntax, signals))

lines = [
    "# Auditoría de autenticación de public_html",
    "",
    "Auditoría de solo lectura. No se incluye código fuente, credenciales ni valores de configuración.",
    "",
    f"Archivos relacionados descargados: {len(rows)}",
    "",
    "| Ruta | Tamaño | SHA-256 | Sesión | Regeneración | Cookie | BD | Hash | POST | JSON | Señales |",
    "|---|---:|---|---|---|---|---|---|---|---|---|",
]
for remote_path, source, text, digest, syntax, signals in rows:
    session = "sí" if re.search(r"\bsession_start\s*\(", text) else "no visible"
    regeneration = "sí" if re.search(r"\bsession_regenerate_id\s*\(", text) else "no visible"
    cookie = "sí" if re.search(r"(?:session_set_cookie_params|setcookie)\s*\(", text, re.I) else "no visible"
    database = "sí" if re.search(r"\b(?:PDO|mysqli|mysql_connect)\b", text, re.I) else "no visible"
    legacy = "sí" if re.search(r"\b(?:md5|sha1)\s*\(", text, re.I) else "no visible"
    post = "sí" if re.search(r"\$_POST|\$_REQUEST", text) else "no visible"
    json = "sí" if re.search(r"json_encode|application/json", text, re.I) else "no visible"
    safe_path = remote_path.replace("|", "%7C")
    lines.append(
        "| `{}` | {} | `{}` | {} | {} | {} | {} | {} | {} | {} | {} |".format(
            safe_path,
            source.stat().st_size,
            digest,
            session,
            regeneration,
            cookie,
            database,
            legacy,
            post,
            json,
            "; ".join(signals) if signals else "ninguna",
        )
    )
if not rows:
    raise SystemExit("No authentication-related files downloaded")
report.write_text("\n".join(lines) + "\n")
PY

cat out/general-login-audit.md