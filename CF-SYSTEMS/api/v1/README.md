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

### `device_token` schema (created by ApiSchema::ensure)

| column         | type             | notes                                        |
|----------------|------------------|----------------------------------------------|
| id             | BIGINT PK        | auto-increment                               |
| recipient_type | ENUM('user','client') | who owns the token                      |
| recipient_id   | INT              | id in the `user` or `person` table           |
| token          | VARCHAR(255)     | Expo / FCM / APNs token (unique)             |
| platform       | VARCHAR(20)      | `ios`, `android`, etc.                       |
| app_version    | VARCHAR(40)      | optional, sent by the client                 |
| device_info    | VARCHAR(255)     | optional, free-form                          |
| created_at     | DATETIME         | first registration                           |
| updated_at     | DATETIME         | **acts as `last_seen`** — refreshed on every `POST /push/register` |

Note: earlier drafts of the task spec called this column `last_seen`.
The implementation uses MySQL's `ON UPDATE CURRENT_TIMESTAMP` on
`updated_at` to provide the same semantics — every re-registration of
the same token bumps `updated_at`.

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

### PATCH /me  (also accepts POST)
Updates basic profile fields. Whitelisted fields:
- Staff: `name, lastname, phone, email, language`
- Client: `name, lastname, phone, phone2, email, address, address2,
  nationality, passport, license, language`

Returns the updated profile in the same shape as `GET /me`.

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
Frees the vehicle if no other active booking holds it.

### POST /bookings/{id}/deliver   (staff only)
Marks the vehicle as delivered (booking `status=3`, car `status=1`).
Requires the booking to currently be in pending (0) or confirmed (1).
Notifies the client.

### POST /bookings/{id}/return    (staff only)
Marks the vehicle as returned (booking `status=4`, car `status=0`).
Requires the booking to currently be delivered (3). Notifies the client.

Staff list filters (in addition to client filters): `client_id`, `car_id`,
`q` (matches booking code OR client name / lastname / phone).

---

## Agenda  (staff only)

### GET /agenda?date=YYYY-MM-DD
Returns the day's deliveries and returns scoped to the staff member's stock.
`date` defaults to today.

```json
{
  "ok": true,
  "data": {
    "date": "2026-05-17",
    "deliveries": [ { "booking": {...}, "car": {...}, "client": {...} } ],
    "returns":    [ { "booking": {...}, "car": {...}, "client": {...} } ]
  }
}
```

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

### POST /payments  (staff only)
Register a payment against a booking.
```json
{ "booking_id": 123, "val": 250.00, "payment_type_id": 1 }
```
The booking's running `payment` total is incremented and the client is
notified.

---

## Preferences

### GET /preferences
List notification preferences (per event_type × channel) for the
authenticated principal.

### PUT /preferences  (POST/PATCH also accepted)
```json
{ "event_type": "booking_created", "channel": "push", "enabled": true }
```
Upserts the row and returns the saved preference.

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

- `JWT_SECRET` — environment variable for signing tokens (must be ≥32
  chars). If unset, a random 64-char secret is generated on first hit and
  stored at `CF-SYSTEMS/storage/runtime/jwt_secret` (mode 0600,
  git-ignored, and HTTP-blocked via an `.htaccess` deny rule in the
  `runtime/` directory). Delete that file to rotate all sessions.
- Push delivery uses the **Expo Push API** (`https://exp.host/--/api/v2/push/send`)
  with no auth required for standard Expo tokens. Tokens that respond with
  `DeviceNotRegistered` are removed automatically.
