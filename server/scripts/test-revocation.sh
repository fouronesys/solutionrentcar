#!/usr/bin/env bash
# Prueba de integración: revocación inmediata de acceso al desactivar staff.
# Verifica que un usuario staff desactivado pierde acceso de inmediato en la
# API (/api/v1) y en archivos privados (/files), aunque su access token siga
# vigente, y que las sesiones de clientes no se ven afectadas.
#
# Uso: bash server/scripts/test-revocation.sh [BASE_URL]
#   BASE_URL por defecto: http://127.0.0.1:8000
# Requiere datos seed de demo (adminA/DemoA123! y cliente 8090000001/ClienteA1! en demo-a).
set -euo pipefail

BASE="${1:-http://127.0.0.1:8000}"
API="$BASE/api/v1"
SLUG="demo-a"
FAILS=0

jsonget() { python3 -c "import sys,json,functools;d=json.load(sys.stdin);print(functools.reduce(lambda a,k:a[k], '$1'.split('.'), d))"; }

check() { # check <descripcion> <esperado> <obtenido>
  if [ "$2" = "$3" ]; then
    echo "OK   $1 -> $3"
  else
    echo "FAIL $1 -> esperado $2, obtenido $3"
    FAILS=$((FAILS + 1))
  fi
}

code() { # code <metodo> <url> <token> [body]
  if [ -n "${4:-}" ]; then
    curl -s -X "$1" "$2" -H "Authorization: Bearer $3" -H "X-Company: $SLUG" -H "Content-Type: application/json" -d "$4" -o /dev/null -w "%{http_code}"
  else
    curl -s -X "$1" "$2" -H "Authorization: Bearer $3" -H "X-Company: $SLUG" -o /dev/null -w "%{http_code}"
  fi
}

login() { # login <username> <password>
  curl -s -X POST "$API/auth/login" -H "Content-Type: application/json" -H "X-Company: $SLUG" \
    -d "{\"username\":\"$1\",\"password\":\"$2\"}" | jsonget data.tokens.access_token
}

ADMIN_TOK=$(login "adminA" "DemoA123!")
USERNAME="revtest$RANDOM"
NEW=$(curl -s -X POST "$API/admin/users" -H "Authorization: Bearer $ADMIN_TOK" -H "X-Company: $SLUG" \
  -H "Content-Type: application/json" -d "{\"username\":\"$USERNAME\",\"password\":\"Temp1234!\",\"name\":\"Prueba revocación\",\"kind\":1}")
NID=$(echo "$NEW" | jsonget data.user.id)
STAFF_TOK=$(login "$USERNAME" "Temp1234!")
COMPANY_ID=$(curl -s "$API/admin/company" -H "Authorization: Bearer $ADMIN_TOK" -H "X-Company: $SLUG" | jsonget data.company.id)
FILE_URL="$BASE/files/companies/$COMPANY_ID/firmas/booking_1_test.png"

echo "== Antes de desactivar (staff activo) =="
check "GET /bookings" 200 "$(code GET "$API/bookings" "$STAFF_TOK")"
check "GET /cars" 200 "$(code GET "$API/cars" "$STAFF_TOK")"
# Autorizado pero el archivo no existe -> 404 (la autorización pasa)
check "GET /files (autorizado)" 404 "$(code GET "$FILE_URL" "$STAFF_TOK")"
check "GET /files via ?token=" 404 "$(curl -s "$FILE_URL?token=$STAFF_TOK" -o /dev/null -w "%{http_code}")"

curl -s -X PATCH "$API/admin/users/$NID" -H "Authorization: Bearer $ADMIN_TOK" -H "X-Company: $SLUG" \
  -H "Content-Type: application/json" -d '{"status":0}' -o /dev/null

echo "== Después de desactivar (mismo access token) =="
check "GET /bookings" 401 "$(code GET "$API/bookings" "$STAFF_TOK")"
check "GET /cars" 401 "$(code GET "$API/cars" "$STAFF_TOK")"
check "POST /bookings/1/deliver" 401 "$(code POST "$API/bookings/1/deliver" "$STAFF_TOK" '{}')"
check "GET /files (header)" 401 "$(code GET "$FILE_URL" "$STAFF_TOK")"
check "GET /files via ?token=" 401 "$(curl -s "$FILE_URL?token=$STAFF_TOK" -o /dev/null -w "%{http_code}")"
check "refresh de otro admin sigue vivo: GET /admin/users" 200 "$(code GET "$API/admin/users" "$ADMIN_TOK")"

echo "== Sesiones de clientes no afectadas =="
CLIENT_TOK=$(login "8090000001" "ClienteA1!")
check "cliente GET /me" 200 "$(code GET "$API/me" "$CLIENT_TOK")"
check "cliente GET /bookings" 200 "$(code GET "$API/bookings" "$CLIENT_TOK")"

if [ "$FAILS" -gt 0 ]; then
  echo "RESULTADO: $FAILS fallos"
  exit 1
fi
echo "RESULTADO: todas las comprobaciones pasaron"
