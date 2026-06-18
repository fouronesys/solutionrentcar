<?php

class ApiSchema {
    private static $done = false;

    public static function ensure(): void {
        if (self::$done) return;
        self::$done = true;

        $con = Database::getCon();
        if (!$con) return;

        $sqls = [
            "CREATE TABLE IF NOT EXISTS device_token (
                id INT(11) NOT NULL AUTO_INCREMENT,
                recipient_type VARCHAR(20) NOT NULL DEFAULT 'user',
                recipient_id INT(11) NOT NULL DEFAULT 0,
                token VARCHAR(255) NOT NULL,
                platform VARCHAR(20) NOT NULL DEFAULT 'expo',
                app_version VARCHAR(40) NULL,
                device_info VARCHAR(255) NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_token (token),
                KEY idx_recipient (recipient_type, recipient_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE IF NOT EXISTS refresh_token (
                id INT(11) NOT NULL AUTO_INCREMENT,
                recipient_type VARCHAR(20) NOT NULL DEFAULT 'user',
                recipient_id INT(11) NOT NULL DEFAULT 0,
                token_hash CHAR(64) NOT NULL,
                expires_at DATETIME NOT NULL,
                revoked_at DATETIME NULL DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_hash (token_hash),
                KEY idx_recipient (recipient_type, recipient_id, revoked_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        ];

        foreach ($sqls as $sql) @$con->query($sql);
    }
}
