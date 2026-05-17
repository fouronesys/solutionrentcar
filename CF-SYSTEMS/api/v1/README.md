# Solutions Rent Car — Mobile API v1

Base URL (production): `https://<your-domain>/CF-SYSTEMS/api/v1/`
Base URL (built-in PHP server / Replit dev): `http://localhost:5000/CF-SYSTEMS/api/v1/index.php?path=<route>`

All responses follow:

```json
{ "ok": true,  "data":  { ... } }
{ "ok": false, "error": { "code": "...", "message": "..." } }
```

Authentication uses **JWT Bearer tokens** (HS256). Pass the token in
`Authorization: Bearer <access_token>`.

The API is self-bootstrapping: required tables (`device_token`,
`refresh_token`, and the notification tables) are created on first hit.

---

## Auth

### POST /auth/login
Body:
```json
{ "username": "admin@example.com", "password": "secret", "role": "staff" }
```
`role` is optional; supported values: `staff` (uses `user` table, password is
`sha1(md5(plain))`) or `client` (uses `person.username` or normalized phone
variants — both as username and password).

Response:
```json
{
  "ok": true,
  "data": {
    "role": "staff",
    "user": { "id": 1, "name": "...", ... },
    "tokens": {
      "access_token": "eyJ...",
      "refresh_token": "abc...",
      "token_type": "Bearer",
      "expires_in": 3600
    }
  }
}
```

### POST /auth/refresh
```json
{ "refresh_token": "<value>" }
```
Returns a new token pair (rotates the refresh token).

### POST /auth/logout
Revokes all refresh tokens for the authenticated principal.

---

## Profile

### GET /me
Returns the current authenticated user (staff or client).

---

## Cars

### GET /cars
Query params: `stock_id`, `status`, `q`, `available_from`, `available_to`,
`limit`, `offset`. Returns array of cars with image URLs.

### GET /cars/{id}
Car detail (includes image + image gallery).

---

## Bookings

### GET /bookings
List bookings. Clients see only their own; staff see bookings within their
`stock_id`. Filters: `status`, `from`, `to`, `limit`, `offset`.

### GET /bookings/{id}
Booking detail with embedded `car` and `client`.

### POST /bookings
Create a booking.

Client body:
```json
{
  "car_id": 12,
  "start_at": "2026-06-01 09:00:00",
  "end_at":   "2026-06-05 18:00:00",
  "place_start": "Aeropuerto",
  "place_end":   "Aeropuerto",
  "comment":     "Vuelo AA1234"
}
```
Staff body adds `person_id` and may pass `stock_id`, `price`, `total`, etc.

The endpoint validates date overlap, computes day count and total if missing,
sets the car as reserved, and fires `booking_created` notifications to the
client and stock staff.

### POST /bookings/{id}/cancel
Cancels a booking (only viewable bookings, i.e. owner or same stock).

---

## Notifications

| Method | Path                                | Description             |
|--------|-------------------------------------|-------------------------|
| GET    | `/notifications`                    | Paginated list          |
| GET    | `/notifications/unread_count`       | Number of unread        |
| POST   | `/notifications/{id}/read`          | Mark one as read        |
| POST   | `/notifications/read_all`           | Mark all as read        |
| GET    | `/notifications/preferences`        | Per-event channel prefs |
| PUT    | `/notifications/preferences`        | Update one preference   |

---

## Payments

### GET /payments
List payments visible to the principal. Optional `booking_id`.

---

## Push (Expo)

### POST /push/register
```json
{
  "token": "ExponentPushToken[xxx]",
  "platform": "ios",
  "app_version": "1.0.0",
  "device_info": "iPhone 15"
}
```
Stores the token against the authenticated principal. When
`NotificationService::notify(...)` runs, it dispatches the alert via the Expo
Push API to all registered tokens for that recipient.

### DELETE /push/token/{token}
Unregister a token (also accepted with `{ "token": "..." }` body).

---

## Catalog (read-only)

`GET /catalog/{kind}` — `kind` ∈ `brands`, `categories`, `transmissions`,
`fuels`, `colors`, `locations`, `stocks`, `insurances`.

---

## cURL examples

```bash
BASE="http://localhost:5000/CF-SYSTEMS/api/v1/index.php?path"

# Health check
curl "$BASE=health"

# Staff login
curl -X POST -H "Content-Type: application/json" \
  -d '{"username":"admin@example.com","password":"changeme","role":"staff"}' \
  "$BASE=auth/login"

# Client login by phone
curl -X POST -H "Content-Type: application/json" \
  -d '{"username":"8095551234","password":"8095551234","role":"client"}' \
  "$BASE=auth/login"

# List available cars in stock 1
curl -H "Authorization: Bearer $ACCESS" \
  "$BASE=cars&stock_id=1&status=0&limit=20"

# Create a booking (client)
curl -X POST -H "Authorization: Bearer $ACCESS" -H "Content-Type: application/json" \
  -d '{"car_id":12,"start_at":"2026-06-01 09:00:00","end_at":"2026-06-05 18:00:00"}' \
  "$BASE=bookings"

# Register an Expo push token
curl -X POST -H "Authorization: Bearer $ACCESS" -H "Content-Type: application/json" \
  -d '{"token":"ExponentPushToken[xxx]","platform":"ios","app_version":"1.0.0"}' \
  "$BASE=push/register"

# Refresh token
curl -X POST -H "Content-Type: application/json" \
  -d '{"refresh_token":"'$REFRESH'"}' \
  "$BASE=auth/refresh"
```

---

## Configuration

- `JWT_SECRET` — recommended environment variable for signing tokens. If
  unset, a deterministic fallback is used (not safe for production).
- Push delivery uses the **Expo Push API** (`https://exp.host/--/api/v2/push/send`)
  with no auth required for standard Expo tokens. Tokens that respond with
  `DeviceNotRegistered` are removed automatically.
