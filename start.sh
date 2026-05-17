#!/bin/bash

MYSQL_SOCKET=/tmp/mysql.sock
MYSQL_PID=/tmp/mysql.pid
MYSQL_DATA=/home/runner/mysql-data
MYSQL_CNF=/tmp/mysql.cnf

cat > "$MYSQL_CNF" << 'MYSQLEOF'
[mysqld]
datadir=/home/runner/mysql-data
socket=/tmp/mysql.sock
pid-file=/tmp/mysql.pid
skip-networking
mysqlx=0
user=runner
MYSQLEOF

start_mysql() {
    rm -f "$MYSQL_SOCKET" "$MYSQL_PID"
    mysqld --defaults-file="$MYSQL_CNF" > /tmp/mysql-out.log 2>&1 &
    echo "[mysql] Started mysqld background"
    for i in $(seq 1 60); do
        if [ -S "$MYSQL_SOCKET" ]; then
            mysql -u root --socket="$MYSQL_SOCKET" -e "SELECT 1;" > /dev/null 2>&1
            if [ $? -eq 0 ]; then
                echo "[mysql] Ready after ${i} attempts"
                return 0
            fi
        fi
        sleep 0.5
    done
    echo "[mysql] WARNING: Could not connect to MySQL"
    return 1
}

mysql_query() {
    mysql -u root --socket="$MYSQL_SOCKET" "$@"
}

run_migrations() {
    echo "[mysql] Running schema migrations..."

    mysql_query u144787244_solutionsrent << 'SQLEOF'
ALTER TABLE stock ADD COLUMN IF NOT EXISTS web_url varchar(255) DEFAULT NULL;
SQLEOF
    mysql_query u144787244_solutionsrent -e "ALTER TABLE stock ADD COLUMN web_url varchar(255) DEFAULT NULL;" 2>/dev/null || true
    mysql_query u144787244_solutionsrent -e "ALTER TABLE stock ADD COLUMN web_url2 varchar(255) DEFAULT NULL;" 2>/dev/null || true
    mysql_query u144787244_solutionsrent -e "ALTER TABLE stock ADD COLUMN web_type varchar(100) DEFAULT NULL;" 2>/dev/null || true
    mysql_query u144787244_solutionsrent -e "ALTER TABLE stock ADD COLUMN web_img varchar(255) DEFAULT NULL;" 2>/dev/null || true
    mysql_query u144787244_solutionsrent -e "ALTER TABLE stock ADD COLUMN web_text text DEFAULT NULL;" 2>/dev/null || true
    mysql_query u144787244_solutionsrent -e "ALTER TABLE stock ADD COLUMN web_title varchar(255) DEFAULT NULL;" 2>/dev/null || true

    mysql_query u144787244_solutionsrent << 'SQLEOF'
CREATE TABLE IF NOT EXISTS `cars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `provider_price` float DEFAULT NULL,
  `charge_kms` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `year` varchar(50) DEFAULT NULL,
  `stock_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `insurance_id` int(11) DEFAULT NULL,
  `insurance2_id` int(11) DEFAULT NULL,
  `interior_id` int(11) DEFAULT NULL,
  `exterior_id` int(11) DEFAULT NULL,
  `invoice_file` varchar(255) DEFAULT NULL,
  `plate` varchar(100) DEFAULT NULL,
  `date_insurance` varchar(100) DEFAULT NULL,
  `date2_insurance` varchar(100) DEFAULT NULL,
  `insurance_file` varchar(255) DEFAULT NULL,
  `insurance2_file` varchar(255) DEFAULT NULL,
  `kms_current` varchar(100) DEFAULT NULL,
  `kms` varchar(100) DEFAULT NULL,
  `tuition` varchar(100) DEFAULT NULL,
  `chassis` varchar(255) DEFAULT NULL,
  `seat` varchar(50) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `transmission_id` int(11) DEFAULT NULL,
  `fuel_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `gps_id` int(11) DEFAULT NULL,
  `no_batery` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` text DEFAULT NULL,
  `payment_day` varchar(100) DEFAULT NULL,
  `type_id` varchar(100) DEFAULT NULL,
  `payment` varchar(255) DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `st2rt_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `e2d_at` datetime DEFAULT NULL,
  `place_start` varchar(255) DEFAULT NULL,
  `place_end` varchar(255) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `person2_id` int(11) DEFAULT NULL,
  `location` int(11) DEFAULT NULL,
  `stock_id` int(11) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `car2_id` int(11) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `total` float DEFAULT NULL,
  `xtotal` float DEFAULT NULL,
  `day` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `deposit` float DEFAULT NULL,
  `fuel` varchar(100) DEFAULT NULL,
  `f_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  `plane` varchar(255) DEFAULT NULL,
  `sure` float DEFAULT NULL,
  `type_sure` int(11) DEFAULT NULL,
  `unit_extra1` varchar(255) DEFAULT NULL,
  `price_extra1` float DEFAULT NULL,
  `unit_extra2` varchar(255) DEFAULT NULL,
  `price_extra2` float DEFAULT NULL,
  `unit_extra3` varchar(255) DEFAULT NULL,
  `price_extra3` float DEFAULT NULL,
  `unit_extra4` varchar(255) DEFAULT NULL,
  `price_extra4` float DEFAULT NULL,
  `iva` float DEFAULT NULL,
  `type_iva` varchar(100) DEFAULT NULL,
  `number_iva` varchar(100) DEFAULT NULL,
  `value_iva` float DEFAULT NULL,
  `card` varchar(255) DEFAULT NULL,
  `usd_price` float DEFAULT NULL,
  `tasa_dolar` float DEFAULT NULL,
  `box_id` int(11) DEFAULT NULL,
  `firma` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `maintenance` (`id` int(11) NOT NULL AUTO_INCREMENT, `car_id` int(11) DEFAULT NULL, `maintenance` text DEFAULT NULL, `user_id` int(11) DEFAULT NULL, `total` float DEFAULT NULL, `f_id` int(11) DEFAULT NULL, `stock_id` int(11) DEFAULT NULL, `cup_dolar` float DEFAULT NULL, `purchase_price` float DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `maintenance_type` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `fuels` (`id` int(11) NOT NULL AUTO_INCREMENT, `car_id` int(11) DEFAULT NULL, `user_id` int(11) DEFAULT NULL, `total` float DEFAULT NULL, `f_id` int(11) DEFAULT NULL, `stock_id` int(11) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `fuel` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `insurance` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `location` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, `longitud` varchar(100) DEFAULT NULL, `latitud` varchar(100) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `color` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `transmission` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `tariff` (`id` int(11) NOT NULL AUTO_INCREMENT, `package_id` int(11) DEFAULT NULL, `brand_id` int(11) DEFAULT NULL, `price` float DEFAULT NULL, `description` text DEFAULT NULL, `stock_id` int(11) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `package` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, `free` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `sure` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `crashes` (`id` int(11) NOT NULL AUTO_INCREMENT, `car_id` int(11) DEFAULT NULL, `person_id` int(11) DEFAULT NULL, `type_id` int(11) DEFAULT NULL, `price` float DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `galery` (`id` int(11) NOT NULL AUTO_INCREMENT, `car_id` int(11) DEFAULT NULL, `invoice_file` varchar(255) DEFAULT NULL, `user_id` int(11) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `k` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `kay` (`id` int(11) NOT NULL AUTO_INCREMENT, `car_id` int(11) DEFAULT NULL, `user_id` int(11) DEFAULT NULL, `type_id` int(11) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `gps_devices` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, `imei` varchar(255) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `delivery` (`id` int(11) NOT NULL AUTO_INCREMENT, `danger` varchar(255) DEFAULT NULL, `firma` varchar(255) DEFAULT NULL, `method` varchar(255) DEFAULT NULL, `cat` varchar(255) DEFAULT NULL, `radio` varchar(255) DEFAULT NULL, `replacement` varchar(255) DEFAULT NULL, `antenna` varchar(255) DEFAULT NULL, `keyring` varchar(255) DEFAULT NULL, `carpets` varchar(255) DEFAULT NULL, `belts` varchar(255) DEFAULT NULL, `roof_lining` varchar(255) DEFAULT NULL, `mirrors` varchar(255) DEFAULT NULL, `board` varchar(255) DEFAULT NULL, `rearview` varchar(255) DEFAULT NULL, `watches` varchar(255) DEFAULT NULL, `document` varchar(255) DEFAULT NULL, `lighter` varchar(255) DEFAULT NULL, `crystals` varchar(255) DEFAULT NULL, `cd` varchar(255) DEFAULT NULL, `bumper` varchar(255) DEFAULT NULL, `equalizer` varchar(255) DEFAULT NULL, `cup_holder` varchar(255) DEFAULT NULL, `plate` varchar(255) DEFAULT NULL, `seats` varchar(255) DEFAULT NULL, `logo` varchar(255) DEFAULT NULL, `batery` varchar(255) DEFAULT NULL, `top` varchar(255) DEFAULT NULL, `comment` text DEFAULT NULL, `no_batery` varchar(255) DEFAULT NULL, `car_id` int(11) DEFAULT NULL, `booking_id` int(11) DEFAULT NULL, `user_id` int(11) DEFAULT NULL, `delivery_id` int(11) DEFAULT NULL, `receiver_id` int(11) DEFAULT NULL, `kms` varchar(100) DEFAULT NULL, `fuel` varchar(100) DEFAULT NULL, `random` varchar(255) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `cotization` (`id` int(11) NOT NULL AUTO_INCREMENT, `person_id` int(11) DEFAULT NULL, `iva` float DEFAULT NULL, `stock_id` int(11) DEFAULT NULL, `total` float DEFAULT NULL, `user_id` int(11) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `chat` (`id` int(11) NOT NULL AUTO_INCREMENT, `user_id` int(11) DEFAULT NULL, `message` text DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `reminder` (`id` int(11) NOT NULL AUTO_INCREMENT, `start_at` datetime DEFAULT NULL, `name` varchar(255) DEFAULT NULL, `user_id` int(11) DEFAULT NULL, `stock_id` int(11) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `payroll` (`id` int(11) NOT NULL AUTO_INCREMENT, `idemployee` int(11) DEFAULT NULL, `amount` float DEFAULT NULL, `pay_day` varchar(100) DEFAULT NULL, `user_id` int(11) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `oil` (`id` int(11) NOT NULL AUTO_INCREMENT, `car_id` int(11) DEFAULT NULL, `kms` varchar(100) DEFAULT NULL, `user_id` int(11) DEFAULT NULL, `total` float DEFAULT NULL, `f_id` int(11) DEFAULT NULL, `stock_id` int(11) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `toll` (`id` int(11) NOT NULL AUTO_INCREMENT, `car_id` int(11) DEFAULT NULL, `user_id` int(11) DEFAULT NULL, `total` float DEFAULT NULL, `f_id` int(11) DEFAULT NULL, `stock_id` int(11) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `tasks` (`id` int(11) NOT NULL AUTO_INCREMENT, `stock_id` int(11) DEFAULT NULL, `user_id` int(11) DEFAULT NULL, `source_type` varchar(100) DEFAULT NULL, `source_key` varchar(255) DEFAULT NULL, `ref_table` varchar(100) DEFAULT NULL, `ref_id` int(11) DEFAULT NULL, `title` varchar(255) DEFAULT NULL, `description` text DEFAULT NULL, `priority` int(11) DEFAULT NULL, `status` int(11) DEFAULT 0, `due_date` datetime DEFAULT NULL, `done_at` datetime DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `states` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `place` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `preference` (`id` int(11) NOT NULL AUTO_INCREMENT, `stock_id` int(11) DEFAULT NULL, `descripcion` text DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `wait` (`id` int(11) NOT NULL AUTO_INCREMENT, `start_at` datetime DEFAULT NULL, `end_at` datetime DEFAULT NULL, `person_id` int(11) DEFAULT NULL, `stock_id` int(11) DEFAULT NULL, `price` float DEFAULT NULL, `total` float DEFAULT NULL, `day` varchar(100) DEFAULT NULL, `car_id` int(11) DEFAULT NULL, `place_start` varchar(255) DEFAULT NULL, `place_end` varchar(255) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `banks` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, `status` int(11) DEFAULT 1, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `bank_accounts` (`id` int(11) NOT NULL AUTO_INCREMENT, `bank_id` int(11) DEFAULT NULL, `account_name` varchar(255) DEFAULT NULL, `account_number` varchar(255) DEFAULT NULL, `currency` varchar(50) DEFAULT NULL, `balance` float DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `bank_checks` (`id` int(11) NOT NULL AUTO_INCREMENT, `account_id` int(11) DEFAULT NULL, `check_number` varchar(100) DEFAULT NULL, `pay_to` varchar(255) DEFAULT NULL, `amount` float DEFAULT NULL, `issue_date` datetime DEFAULT NULL, `concept` text DEFAULT NULL, `status` int(11) DEFAULT 0, `created_by` int(11) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `bank_transactions` (`id` int(11) NOT NULL AUTO_INCREMENT, `account_id` int(11) DEFAULT NULL, `type` varchar(100) DEFAULT NULL, `person_id` int(11) DEFAULT NULL, `amount` float DEFAULT NULL, `exchange_rate` float DEFAULT NULL, `premium_percent` float DEFAULT NULL, `premium_amount` float DEFAULT NULL, `fee` float DEFAULT NULL, `total_local` float DEFAULT NULL, `direction` varchar(50) DEFAULT NULL, `description` text DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `bank_reconciliations` (`id` int(11) NOT NULL AUTO_INCREMENT, `account_id` int(11) DEFAULT NULL, `balance_bank` float DEFAULT NULL, `balance_system` float DEFAULT NULL, `difference` float DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `tm` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `xx` (`id` int(11) NOT NULL AUTO_INCREMENT, `car_id` int(11) DEFAULT NULL, `user_id` int(11) DEFAULT NULL, `stock_id` int(11) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `yy` (`id` int(11) NOT NULL AUTO_INCREMENT, `car_id` int(11) DEFAULT NULL, `user_id` int(11) DEFAULT NULL, `stock_id` int(11) DEFAULT NULL, `created_at` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQLEOF
    echo "[mysql] Migrations done"
}

mysql_watchdog() {
    while true; do
        sleep 5
        if ! mysql -u root --socket="$MYSQL_SOCKET" -e "SELECT 1;" > /dev/null 2>&1; then
            echo "[watchdog] MySQL down, restarting..."
            start_mysql
            run_migrations
        fi
    done
}

echo "[start.sh] Starting MySQL..."
start_mysql

if mysql_query -e "SELECT 1;" > /dev/null 2>&1; then
    mysql_query << 'SQLEOF'
CREATE DATABASE IF NOT EXISTS u144787244_solutionsrent CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'u144787244_solutionsrent'@'localhost' IDENTIFIED BY 'SolutionRentCar01';
GRANT ALL PRIVILEGES ON u144787244_solutionsrent.* TO 'u144787244_solutionsrent'@'localhost';
FLUSH PRIVILEGES;
SQLEOF

    TABLE_COUNT=$(mysql_query u144787244_solutionsrent -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='u144787244_solutionsrent';" 2>/dev/null | tail -1)
    echo "[start.sh] Tables in DB: $TABLE_COUNT"

    if [ -z "$TABLE_COUNT" ] || [ "$TABLE_COUNT" -lt 50 ] 2>/dev/null; then
        echo "[start.sh] Importing SQL backup..."
        mysql_query u144787244_solutionsrent --force < /home/runner/workspace/backups/db-backup-2022-01-01.sql 2>&1 | grep -v "Warning\|Truncated\|Incorrect" | tail -5 || true
        echo "[start.sh] Import done"
    fi

    run_migrations
else
    echo "[start.sh] WARNING: MySQL not available, continuing..."
fi

mysql_watchdog &

echo "[start.sh] Starting PHP web server on 0.0.0.0:5000..."
exec php -S 0.0.0.0:5000 -t /home/runner/workspace
