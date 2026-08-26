#!/usr/bin/env bash
set -euo pipefail
set +x

: "${FTP_HOST:?missing FTP_HOST}"
: "${FTP_USER:?missing FTP_USER}"
: "${FTP_PASSWORD:?missing FTP_PASSWORD}"

run_lftp() {
  LFTP_PASSWORD="$FTP_PASSWORD" lftp -u "$FTP_USER" --env-password "$FTP_HOST" -e \
    "set ssl:verify-certificate no; set ftp:ssl-allow no; set net:max-retries 1; set net:timeout 20; $1; bye"
}

mkdir -p /tmp/public-html-auth out
run_lftp "cls -la /" >out/public-html-root-listing.txt 2>&1

awk '
  /^d/ && $NF ~ /^\/[A-Za-z0-9._-]+\/$/ {
    folder = $NF
    sub(/^\/+/, "", folder)
    sub(/\/+$/, "", folder)
    if (folder != "." && folder != ".." && folder != "logs" &&
        folder !~ /^PRESTAMOS$/ && folder !~ /^prestamos$/) print folder
  }
' out/public-html-root-listing.txt | sort -u >/tmp/public-html-folders.txt

printf 'excluded=PRESTAMOS,prestamos\n' >out/public-html-scan-status.txt
printf 'folders=%s\n' "$(wc -l </tmp/public-html-folders.txt | tr -d ' ')" >>out/public-html-scan-status.txt

# Download a bounded set of common entry points in one FTP session. This
# avoids a recursive traversal and avoids opening one FTP connection per file.
: >/tmp/public-html-auth-paths.txt
for root_file in /index.php /login.php /default.php /actualizar.php /update.php; do
  printf '%s\n' "$root_file" >>/tmp/public-html-auth-paths.txt
done
while IFS= read -r folder; do
  for suffix in \
    /index.php /login.php /auth.php /session.php \
    /core/app/action/users-action.php /core/app/view/login-view.php \
    /core/controller/login.php /api/v1/handlers/auth.php /api/v1/index.php; do
    printf '/%s%s\n' "$folder" "$suffix" >>/tmp/public-html-auth-paths.txt
  done
done </tmp/public-html-folders.txt
sort -u /tmp/public-html-auth-paths.txt -o /tmp/public-html-auth-paths.txt

: >/tmp/public-html-auth-map.tsv
: >/tmp/public-html-get-commands
index=0
while IFS= read -r remote_path; do
  [ -n "$remote_path" ] || continue
  case "$remote_path" in
    /PRESTAMOS/*|/prestamos/*) continue ;;
  esac
  index=$((index + 1))
  local_path="/tmp/public-html-auth/${index}.source"
  printf 'get %s -o %s\n' "$remote_path" "$local_path" >>/tmp/public-html-get-commands
done </tmp/public-html-auth-paths.txt

LFTP_PASSWORD="$FTP_PASSWORD" lftp -u "$FTP_USER" --env-password "$FTP_HOST" \
  -f <(cat <<EOF
set ssl:verify-certificate no
set ftp:ssl-allow no
set net:max-retries 1
set net:timeout 20
set cmd:fail-exit no
$(cat /tmp/public-html-get-commands)
bye
EOF
) >/tmp/public-html-get.log 2>&1 || true

index=0
while IFS= read -r remote_path; do
  [ -n "$remote_path" ] || continue
  case "$remote_path" in
    /PRESTAMOS/*|/prestamos/*) continue ;;
  esac
  index=$((index + 1))
  local_path="/tmp/public-html-auth/${index}.source"
  if [ -s "$local_path" ]; then
    printf '%s\t%s\n' "$index" "$remote_path" >>/tmp/public-html-auth-map.tsv
  fi
done </tmp/public-html-auth-paths.txt

python3 - /tmp/public-html-auth-map.tsv /tmp/public-html-auth out/public-html-login-audit.md <<'PY'
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
    if "Access-Control-Allow-Origin: *" in text:
        signals.append("CORS abierto")
    if re.search(r"\b(?:mysql_query|mysqli_query|query)\s*\([^;]*(?:\.\s*\$|\$\w+\s*\.)", text, re.I | re.S):
        signals.append("SQL construido dinámicamente")
    if "session_start" in text.lower() and not re.search(
        r"(?:session_set_cookie_params|setcookie)\s*\(", text, re.I
    ):
        signals.append("cookie de sesión no visible")
    session = "sí" if re.search(r"\bsession_start\s*\(", text) else "no visible"
    regeneration = "sí" if re.search(r"session_regenerate_id\s*\(", text) else "no visible"
    cookie = "sí" if re.search(r"(?:session_set_cookie_params|setcookie)\s*\(", text, re.I) else "no visible"
    database = "sí" if re.search(r"\b(?:PDO|mysqli|mysql_connect)\b", text, re.I) else "no visible"
    legacy = "sí" if re.search(r"\b(?:md5|sha1)\s*\(", text, re.I) else "no visible"
    post = "sí" if re.search(r"\$_POST|\$_REQUEST", text) else "no visible"
    json = "sí" if re.search(r"json_encode|application/json", text, re.I) else "no visible"
    rows.append((remote_path, source, session, regeneration, cookie, database, legacy, post, json, signals))

lines = [
    "# Auditoría de autenticación bajo public_html",
    "",
    "Auditoría de solo lectura. PRESTAMOS/prestamos fue excluido porque tiene una auditoría independiente.",
    "No se incluye código fuente, credenciales ni valores de configuración.",
    "",
    f"Archivos relacionados descargados: {len(rows)}",
    "",
    "| Ruta | Tamaño | SHA-256 | Sesión | Regeneración | Cookie | BD | Hash | POST | JSON | Señales |",
    "|---|---:|---|---|---|---|---|---|---|---|---|",
]
for remote_path, source, session, regeneration, cookie, database, legacy, post, json, signals in rows:
    digest = hashlib.sha256(source.read_bytes()).hexdigest()
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

cat out/public-html-scan-status.txt
cat out/public-html-login-audit.md