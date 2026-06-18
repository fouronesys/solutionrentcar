-- ============================================================================
  -- Notification System Phase 1 — Idempotent migration
  -- Run on Hostinger u144787244_solutionsrent
  -- Safe to run multiple times.
  -- ============================================================================

  CREATE TABLE IF NOT EXISTS notification (
      id INT(11) NOT NULL AUTO_INCREMENT,
      recipient_type VARCHAR(20) NOT NULL DEFAULT 'user',
      recipient_id INT(11) NOT NULL DEFAULT 0,
      stock_id INT(11) NOT NULL DEFAULT 0,
      type VARCHAR(60) NOT NULL DEFAULT '',
      title VARCHAR(255) NOT NULL DEFAULT '',
      body TEXT NULL,
      data_json TEXT NULL,
      url VARCHAR(500) NULL,
      read_at DATETIME NULL DEFAULT NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      KEY idx_recipient (recipient_type, recipient_id, read_at),
      KEY idx_stock (stock_id, created_at),
      KEY idx_type (type, created_at)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  CREATE TABLE IF NOT EXISTS notification_preference (
      id INT(11) NOT NULL AUTO_INCREMENT,
      recipient_type VARCHAR(20) NOT NULL DEFAULT 'user',
      recipient_id INT(11) NOT NULL DEFAULT 0,
      event_type VARCHAR(60) NOT NULL DEFAULT '',
      channel VARCHAR(20) NOT NULL DEFAULT 'inapp',
      enabled TINYINT(1) NOT NULL DEFAULT 1,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      UNIQUE KEY uniq_pref (recipient_type, recipient_id, event_type, channel)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  CREATE TABLE IF NOT EXISTS notification_log (
      id INT(11) NOT NULL AUTO_INCREMENT,
      notification_id INT(11) NOT NULL DEFAULT 0,
      channel VARCHAR(20) NOT NULL DEFAULT '',
      status VARCHAR(20) NOT NULL DEFAULT '',
      detail TEXT NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      KEY idx_notif (notification_id),
      KEY idx_status (status, created_at)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  