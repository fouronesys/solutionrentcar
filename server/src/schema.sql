-- Multi-tenant rent-car platform schema.
-- Every business entity carries company_id; nothing is shared across companies.

CREATE TABLE IF NOT EXISTS companies (
  id              SERIAL PRIMARY KEY,
  slug            TEXT NOT NULL UNIQUE,
  name            TEXT NOT NULL,
  logo            TEXT,                      -- data URL or absolute URL
  color_primary   TEXT NOT NULL DEFAULT '#fb3b54',
  color_secondary TEXT NOT NULL DEFAULT '#111827',
  currency        TEXT NOT NULL DEFAULT 'DOP',
  phone           TEXT NOT NULL DEFAULT '',
  email           TEXT NOT NULL DEFAULT '',
  address         TEXT NOT NULL DEFAULT '',
  active          BOOLEAN NOT NULL DEFAULT TRUE,
  created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Staff / platform users. Super admins have is_super = TRUE and company_id NULL.
CREATE TABLE IF NOT EXISTS users (
  id            SERIAL PRIMARY KEY,
  company_id    INTEGER REFERENCES companies(id),
  is_super      BOOLEAN NOT NULL DEFAULT FALSE,
  username      TEXT NOT NULL,
  email         TEXT NOT NULL DEFAULT '',
  password_hash TEXT NOT NULL,
  password_algo TEXT NOT NULL DEFAULT 'bcrypt',   -- bcrypt | sha1md5 (legacy)
  name          TEXT NOT NULL DEFAULT '',
  lastname      TEXT NOT NULL DEFAULT '',
  phone         TEXT NOT NULL DEFAULT '',
  kind          INTEGER NOT NULL DEFAULT 0,        -- 1 = company admin
  stock_id      INTEGER NOT NULL DEFAULT 0,
  image         TEXT,
  status        INTEGER NOT NULL DEFAULT 1,
  created_at    TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  CONSTRAINT users_super_or_company CHECK (is_super OR company_id IS NOT NULL)
);
CREATE UNIQUE INDEX IF NOT EXISTS users_company_username ON users(company_id, LOWER(username)) WHERE company_id IS NOT NULL;
CREATE UNIQUE INDEX IF NOT EXISTS users_super_username ON users(LOWER(username)) WHERE is_super;

-- Clients.
CREATE TABLE IF NOT EXISTS persons (
  id              SERIAL PRIMARY KEY,
  company_id      INTEGER NOT NULL REFERENCES companies(id),
  name            TEXT NOT NULL DEFAULT '',
  lastname        TEXT NOT NULL DEFAULT '',
  email           TEXT NOT NULL DEFAULT '',
  phone           TEXT NOT NULL DEFAULT '',
  phone2          TEXT NOT NULL DEFAULT '',
  phone_normalized TEXT NOT NULL DEFAULT '',
  username        TEXT NOT NULL DEFAULT '',
  password_hash   TEXT NOT NULL DEFAULT '',
  password_algo   TEXT NOT NULL DEFAULT 'bcrypt',
  address         TEXT NOT NULL DEFAULT '',
  address2        TEXT NOT NULL DEFAULT '',
  nationality     TEXT NOT NULL DEFAULT '',
  passport        TEXT NOT NULL DEFAULT '',
  license         TEXT NOT NULL DEFAULT '',
  language        TEXT NOT NULL DEFAULT 'ES',
  stock_id        INTEGER NOT NULL DEFAULT 0,
  is_guest        BOOLEAN NOT NULL DEFAULT FALSE,
  doc_cedula      TEXT,
  doc_passport    TEXT,
  doc_license     TEXT,
  doc_home        TEXT,
  created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS persons_company_phone ON persons(company_id, phone_normalized);
CREATE INDEX IF NOT EXISTS persons_company_username ON persons(company_id, LOWER(username));

-- Catalog entries (brands, categories, transmissions, fuels, colors, locations, stocks, insurances).
CREATE TABLE IF NOT EXISTS catalog_items (
  id         SERIAL PRIMARY KEY,
  company_id INTEGER NOT NULL REFERENCES companies(id),
  kind       TEXT NOT NULL,
  name       TEXT NOT NULL,
  UNIQUE (company_id, kind, name)
);
CREATE INDEX IF NOT EXISTS catalog_company_kind ON catalog_items(company_id, kind);

CREATE TABLE IF NOT EXISTS cars (
  id              SERIAL PRIMARY KEY,
  company_id      INTEGER NOT NULL REFERENCES companies(id),
  name            TEXT NOT NULL DEFAULT '',
  year            TEXT NOT NULL DEFAULT '',
  plate           TEXT NOT NULL DEFAULT '',
  price           NUMERIC(12,2) NOT NULL DEFAULT 0,
  seat            TEXT NOT NULL DEFAULT '',
  kms             TEXT NOT NULL DEFAULT '',
  kms_current     TEXT NOT NULL DEFAULT '',
  status          INTEGER NOT NULL DEFAULT 0,      -- 0 available, 1 reserved/out
  brand_id        INTEGER NOT NULL DEFAULT 0,
  category_id     INTEGER NOT NULL DEFAULT 0,
  transmission_id INTEGER NOT NULL DEFAULT 0,
  fuel_id         INTEGER NOT NULL DEFAULT 0,
  stock_id        INTEGER NOT NULL DEFAULT 0,
  image           TEXT,
  images          JSONB NOT NULL DEFAULT '[]',
  description     TEXT NOT NULL DEFAULT '',
  created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS cars_company ON cars(company_id, status);

CREATE TABLE IF NOT EXISTS bookings (
  id          SERIAL PRIMARY KEY,
  company_id  INTEGER NOT NULL REFERENCES companies(id),
  code        TEXT NOT NULL DEFAULT '',
  person_id   INTEGER NOT NULL DEFAULT 0,
  car_id      INTEGER NOT NULL DEFAULT 0,
  user_id     INTEGER NOT NULL DEFAULT 0,
  stock_id    INTEGER NOT NULL DEFAULT 0,
  start_at    TIMESTAMP NOT NULL,
  end_at      TIMESTAMP NOT NULL,
  place_start TEXT NOT NULL DEFAULT 'No especificado',
  place_end   TEXT NOT NULL DEFAULT 'No especificado',
  day         TEXT NOT NULL DEFAULT '1',
  price       NUMERIC(12,2) NOT NULL DEFAULT 0,
  total       NUMERIC(12,2) NOT NULL DEFAULT 0,
  xtotal      NUMERIC(12,2) NOT NULL DEFAULT 0,
  payment     NUMERIC(12,2) NOT NULL DEFAULT 0,
  fuel        TEXT NOT NULL DEFAULT '',
  comment     TEXT NOT NULL DEFAULT '',
  status      INTEGER NOT NULL DEFAULT 0,          -- 0 pending 1 confirmed 2 cancelled 3 delivered 4 returned
  deposit     NUMERIC(12,2) NOT NULL DEFAULT 0,
  sure        NUMERIC(12,2) NOT NULL DEFAULT 0,
  signature   TEXT,
  created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS bookings_company ON bookings(company_id, status);
CREATE INDEX IF NOT EXISTS bookings_car ON bookings(company_id, car_id, status);
CREATE INDEX IF NOT EXISTS bookings_person ON bookings(company_id, person_id);

CREATE TABLE IF NOT EXISTS payments (
  id              SERIAL PRIMARY KEY,
  company_id      INTEGER NOT NULL REFERENCES companies(id),
  booking_id      INTEGER NOT NULL,
  person_id       INTEGER NOT NULL DEFAULT 0,
  stock_id        INTEGER NOT NULL DEFAULT 0,
  val             NUMERIC(12,2) NOT NULL DEFAULT 0,
  payment_type_id INTEGER NOT NULL DEFAULT 1,
  created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS payments_company ON payments(company_id, booking_id);

CREATE TABLE IF NOT EXISTS notifications (
  id             SERIAL PRIMARY KEY,
  company_id     INTEGER NOT NULL REFERENCES companies(id),
  recipient_type TEXT NOT NULL,                    -- user | client
  recipient_id   INTEGER NOT NULL,
  type           TEXT NOT NULL DEFAULT '',
  title          TEXT NOT NULL DEFAULT '',
  body           TEXT NOT NULL DEFAULT '',
  url            TEXT NOT NULL DEFAULT '',
  data           JSONB,
  read_at        TIMESTAMPTZ,
  created_at     TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS notifications_recipient ON notifications(company_id, recipient_type, recipient_id, read_at);

CREATE TABLE IF NOT EXISTS notification_preferences (
  id             SERIAL PRIMARY KEY,
  company_id     INTEGER NOT NULL REFERENCES companies(id),
  recipient_type TEXT NOT NULL,
  recipient_id   INTEGER NOT NULL,
  event_type     TEXT NOT NULL,
  channel        TEXT NOT NULL,
  enabled        BOOLEAN NOT NULL DEFAULT TRUE,
  UNIQUE (company_id, recipient_type, recipient_id, event_type, channel)
);

CREATE TABLE IF NOT EXISTS refresh_tokens (
  id             SERIAL PRIMARY KEY,
  company_id     INTEGER REFERENCES companies(id), -- NULL for super admins
  recipient_type TEXT NOT NULL DEFAULT 'user',
  recipient_id   INTEGER NOT NULL DEFAULT 0,
  token_hash     CHAR(64) NOT NULL UNIQUE,
  expires_at     TIMESTAMPTZ NOT NULL,
  revoked_at     TIMESTAMPTZ,
  created_at     TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS refresh_recipient ON refresh_tokens(recipient_type, recipient_id, revoked_at);

CREATE TABLE IF NOT EXISTS device_tokens (
  id             SERIAL PRIMARY KEY,
  company_id     INTEGER NOT NULL REFERENCES companies(id),
  recipient_type TEXT NOT NULL DEFAULT 'user',
  recipient_id   INTEGER NOT NULL DEFAULT 0,
  token          TEXT NOT NULL UNIQUE,
  platform       TEXT NOT NULL DEFAULT 'expo',
  app_version    TEXT,
  device_info    TEXT,
  created_at     TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at     TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS device_recipient ON device_tokens(company_id, recipient_type, recipient_id);
