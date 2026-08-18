<?php
/*
| update.php central por LOTES.
| El hosting permite ~20 conexiones MySQL por request; este script procesa
| hasta RT_MAX_CONN_PER_REQUEST bases por request y guarda el avance en
| logs/central_update_progress.json. Llamar repetidamente hasta PENDIENTES=0.
|   ?reset=1 → reinicia el progreso (nueva pasada completa)
*/
ini_set('display_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/rtcommon.php';

$base_path = __DIR__ . '/';

$progress_file = __DIR__ . '/logs/central_update_progress.json';
rt_index_path(__DIR__); // asegura logs/ y .htaccess
$progress = ['done' => []];
if (!isset($_GET['reset']) && file_exists($progress_file)) {
    $j = json_decode(file_get_contents($progress_file), true);
    if (is_array($j) && isset($j['done'])) $progress = $j;
}
if (isset($_GET['reset'])) echo "RESET: progreso reiniciado<br>";

$bases = [];
foreach (rt_folders(__DIR__) as $carpeta) {
    $cfg = rt_read_config($carpeta);
    if ($cfg === null) {
        echo "❌ Config incompleta o inexistente en: " . basename($carpeta) . "<br><hr>";
        continue;
    }
    $bases[] = $cfg;
}

$procesadas = 0; $errores = 0; $quota = false;

foreach ($bases as $info) {

    $folder = $info['folder'];
    $dbName = $info['db'];

    if (in_array($folder, $progress['done'], true)) continue;
    if (rt_quota_left() <= 0) break;

    $c = rt_connect($info);
    if (isset($c['error'])) {
        if ($c['error'] === 'QUOTA') {
            echo "⛔ Límite de conexiones del request alcanzado en {$folder}. Ejecutar de nuevo para continuar.<br><hr>";
            $quota = true;
            break;
        }
        echo "❌ Error conectando {$dbName}: " . $c['msg'] . "<br><hr>";
        $errores++;
        $progress['done'][] = $folder; // no bloquear la pasada por credenciales rotas
        continue;
    }

    $pdo = $c['pdo'];
    echo "✅ Conectado: <strong>{$dbName}</strong><br>";

    try {

        echo "<strong>Base de datos:</strong> {$dbName}<br>";

        /*
        |--------------------------------------------------------------------------
        | A) CAMPO subscription EN stock
        |--------------------------------------------------------------------------
        */
        $st_stock = $pdo->prepare("SHOW TABLES LIKE 'stock'");
        $st_stock->execute();

        if ($st_stock->rowCount() > 0) {

            $checkSubscription = $pdo->prepare("
                SHOW COLUMNS FROM stock LIKE 'subscription'
            ");
            $checkSubscription->execute();

            if ($checkSubscription->rowCount() == 0) {

                $pdo->exec("
                    ALTER TABLE stock
                    ADD COLUMN subscription DATE NULL DEFAULT NULL
                ");

                echo "✅ Campo subscription agregado en stock<br>";

            } else {

                echo "✔️ Campo subscription ya existe en stock<br>";
            }

            $verifyDate = $pdo->prepare("
                SELECT subscription 
                FROM stock 
                WHERE id = 1
                LIMIT 1
            ");
            $verifyDate->execute();

            $stockData = $verifyDate->fetch(PDO::FETCH_ASSOC);

            if ($stockData) {

                if (
                    empty($stockData["subscription"]) ||
                    $stockData["subscription"] == "0000-00-00"
                ) {

                    $pdo->exec("
                        UPDATE stock
                        SET subscription = DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                        WHERE id = 1
                    ");

                    echo "✅ Suscripción activada por 30 días<br>";

                } else {

                    echo "✔️ Stock ID 1 ya tiene suscripción<br>";
                }
            }

        } else {

            echo "⚠️ No existe tabla stock<br>";
        }

        /*
        |--------------------------------------------------------------------------
        | B) TABLA raffles
        |--------------------------------------------------------------------------
        */
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS raffles (
          id INT(11) NOT NULL AUTO_INCREMENT,
          stock_id INT(11) NOT NULL DEFAULT 1,
          title VARCHAR(200) NOT NULL,
          campaign_type VARCHAR(50) NOT NULL DEFAULT 'rental',
          start_date DATE NULL,
          end_date DATE NULL,
          min_rental_days INT(11) NOT NULL DEFAULT 1,
          winners_limit INT(11) NOT NULL DEFAULT 1,
          participation_type VARCHAR(50) NOT NULL DEFAULT 'automatic',
          description TEXT NULL,
          rule_description TEXT NULL,
          ticket_price DECIMAL(18,2) NOT NULL DEFAULT 0.00,
          total_tickets INT(11) NOT NULL DEFAULT 0,
          sold_tickets INT(11) NOT NULL DEFAULT 0,
          image VARCHAR(250) NULL,
          status VARCHAR(50) NOT NULL DEFAULT 'active',
          created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        echo "✅ Tabla raffles verificada/creada<br>";

        /*
        |--------------------------------------------------------------------------
        | C) TABLA raffle_prizes
        |--------------------------------------------------------------------------
        */
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS raffle_prizes (
          id INT(11) NOT NULL AUTO_INCREMENT,
          stock_id INT(11) NOT NULL DEFAULT 1,
          raffle_id INT(11) NOT NULL,
          prize_type VARCHAR(50) NOT NULL DEFAULT 'other',
          prize_title VARCHAR(200) NULL,
          prize_description VARCHAR(250) NULL,
          prize_value DECIMAL(18,2) DEFAULT 0.00,
          prize_order INT(11) NOT NULL DEFAULT 1,
          car_id INT(11) NULL,
          cash_amount DECIMAL(18,2) DEFAULT 0.00,
          other_description TEXT NULL,
          created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        echo "✅ Tabla raffle_prizes verificada/creada<br>";

        /*
        |--------------------------------------------------------------------------
        | D) TABLA raffle_tickets
        |--------------------------------------------------------------------------
        */
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS raffle_tickets (
          id INT(11) NOT NULL AUTO_INCREMENT,
          stock_id INT(11) NOT NULL DEFAULT 1,
          raffle_id INT(11) NOT NULL,
          booking_id INT(11) NULL,
          person_id INT(11) NULL,
          car_id INT(11) NULL,
          rental_days INT(11) NOT NULL DEFAULT 0,
          participation_source VARCHAR(50) NOT NULL DEFAULT 'booking',
          is_winner INT(11) NOT NULL DEFAULT 0,
          winner_position INT(11) NULL,
          prize_awarded VARCHAR(250) NULL,
          ticket_number VARCHAR(100) NULL,
          name VARCHAR(150) NULL,
          phone VARCHAR(100) NULL,
          cedula VARCHAR(100) NULL,
          quantity INT(11) NOT NULL DEFAULT 1,
          payment_method VARCHAR(100) NULL,
          payment_file VARCHAR(250) NULL,
          status INT(11) NOT NULL DEFAULT 1,
          created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        echo "✅ Tabla raffle_tickets verificada/creada<br>";

        /*
        |--------------------------------------------------------------------------
        | E) CAMPO comment EN booking
        |--------------------------------------------------------------------------
        */
        $st = $pdo->prepare("SHOW TABLES LIKE 'booking'");
        $st->execute();

        if ($st->rowCount() > 0) {

            $col = $pdo->prepare("SHOW COLUMNS FROM booking LIKE 'comment'");
            $col->execute();

            if ($col->rowCount() == 0) {

                $pdo->exec("
                    ALTER TABLE booking 
                    ADD COLUMN comment VARCHAR(1000) 
                    COLLATE latin1_swedish_ci 
                    NOT NULL DEFAULT '' 
                    AFTER status
                ");

                echo "✅ Campo comment creado en booking<br>";

            } else {

                echo "✔️ Campo comment ya existe en booking<br>";
            }

        } else {

            echo "⚠️ No existe tabla booking<br>";
        }

        /*
        |--------------------------------------------------------------------------
        | F) TABLA notification
        |--------------------------------------------------------------------------
        */
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS notification (
          id INT(11) NOT NULL AUTO_INCREMENT,
          recipient_type VARCHAR(20) NOT NULL DEFAULT 'user',
          recipient_id INT(11) NOT NULL DEFAULT 0,
          stock_id INT(11) NOT NULL DEFAULT 0,
          type VARCHAR(60) NOT NULL DEFAULT '',
          title VARCHAR(255) NOT NULL DEFAULT '',
          body TEXT DEFAULT NULL,
          data_json TEXT DEFAULT NULL,
          url VARCHAR(500) DEFAULT NULL,
          read_at DATETIME DEFAULT NULL,
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (id),
          KEY idx_recipient (recipient_type, recipient_id, read_at),
          KEY idx_stock (stock_id, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        echo "✅ Tabla notification verificada/creada<br>";

        /*
        |--------------------------------------------------------------------------
        | G) TABLA notification_log
        |--------------------------------------------------------------------------
        */
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS notification_log (
          id INT(11) NOT NULL AUTO_INCREMENT,
          notification_id INT(11) NOT NULL DEFAULT 0,
          channel VARCHAR(20) NOT NULL DEFAULT '',
          status VARCHAR(20) NOT NULL DEFAULT '',
          detail TEXT DEFAULT NULL,
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (id),
          KEY idx_notif (notification_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        echo "✅ Tabla notification_log verificada/creada<br>";

        /*
        |--------------------------------------------------------------------------
        | H) TABLA notification_preference
        |--------------------------------------------------------------------------
        */
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS notification_preference (
          id INT(11) NOT NULL AUTO_INCREMENT,
          recipient_type VARCHAR(20) NOT NULL DEFAULT 'user',
          recipient_id INT(11) NOT NULL DEFAULT 0,
          event_type VARCHAR(60) NOT NULL DEFAULT '',
          channel VARCHAR(20) NOT NULL DEFAULT 'inapp',
          enabled TINYINT(1) NOT NULL DEFAULT 1,
          updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
          ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (id),
          UNIQUE KEY uniq_pref (
            recipient_type,
            recipient_id,
            event_type,
            channel
          )
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        echo "✅ Tabla notification_preference verificada/creada<br>";

        /*
        |--------------------------------------------------------------------------
        | I) EXTRAS DINÁMICOS DE RESERVA / CONTRATO
        |--------------------------------------------------------------------------
        | Permite que CARSEAT, INTERNET, TRAILER y cualquier otro extra
        | sean configurables por el cliente desde el sistema.
        | stock_id = 0 aplica a todas las sucursales.
        | category_id = 0 aplica a todas las categorías.
        */
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS booking_extra_type (
          id INT(11) NOT NULL AUTO_INCREMENT,
          stock_id INT(11) NOT NULL DEFAULT 0,
          category_id INT(11) NOT NULL DEFAULT 0,
          name VARCHAR(120) NOT NULL,
          default_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
          is_active TINYINT(1) NOT NULL DEFAULT 1,
          sort_order INT(11) NOT NULL DEFAULT 0,
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at DATETIME NULL DEFAULT NULL,
          PRIMARY KEY (id),
          UNIQUE KEY uq_booking_extra_type (stock_id, category_id, name),
          KEY idx_booking_extra_type_stock (stock_id),
          KEY idx_booking_extra_type_category (category_id),
          KEY idx_booking_extra_type_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        echo "✅ Tabla booking_extra_type verificada/creada<br>";

        $pdo->exec("
        CREATE TABLE IF NOT EXISTS booking_extra_item (
          id INT(11) NOT NULL AUTO_INCREMENT,
          booking_id INT(11) NOT NULL,
          extra_id INT(11) NOT NULL DEFAULT 0,
          extra_name VARCHAR(120) NOT NULL,
          unit DECIMAL(12,2) NOT NULL DEFAULT 0.00,
          price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
          total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (id),
          KEY idx_booking_extra_item_booking (booking_id),
          KEY idx_booking_extra_item_extra (extra_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        echo "✅ Tabla booking_extra_item verificada/creada<br>";

        $pdo->exec("
        INSERT IGNORE INTO booking_extra_type
          (stock_id, category_id, name, default_price, is_active, sort_order, created_at)
        VALUES
          (0, 0, 'CARSEAT', 0.00, 1, 1, NOW()),
          (0, 0, 'INTERNET', 0.00, 1, 2, NOW()),
          (0, 0, 'TRAILER', 0.00, 1, 3, NOW())
        ");

        echo "✅ Extras base insertados/verificados: CARSEAT, INTERNET, TRAILER<br>";

        /*
        |--------------------------------------------------------------------------
        | J) CAMPOS LEGACY DE EXTRAS EN booking
        |--------------------------------------------------------------------------
        | Se crean solo si faltan, para que el sistema viejo y el dinámico
        | puedan convivir sin romper contratos existentes.
        */
        $st_booking_extras = $pdo->prepare("SHOW TABLES LIKE 'booking'");
        $st_booking_extras->execute();

        if ($st_booking_extras->rowCount() > 0) {

            $legacyExtraColumns = [
                'unit_extra1'  => "ADD COLUMN unit_extra1 DECIMAL(12,2) NOT NULL DEFAULT 0.00",
                'price_extra1' => "ADD COLUMN price_extra1 DECIMAL(12,2) NOT NULL DEFAULT 0.00",
                'unit_extra2'  => "ADD COLUMN unit_extra2 DECIMAL(12,2) NOT NULL DEFAULT 0.00",
                'price_extra2' => "ADD COLUMN price_extra2 DECIMAL(12,2) NOT NULL DEFAULT 0.00",
                'unit_extra3'  => "ADD COLUMN unit_extra3 DECIMAL(12,2) NOT NULL DEFAULT 0.00",
                'price_extra3' => "ADD COLUMN price_extra3 DECIMAL(12,2) NOT NULL DEFAULT 0.00",
                'unit_extra4'  => "ADD COLUMN unit_extra4 DECIMAL(12,2) NOT NULL DEFAULT 0.00",
                'price_extra4' => "ADD COLUMN price_extra4 DECIMAL(12,2) NOT NULL DEFAULT 0.00"
            ];

            foreach ($legacyExtraColumns as $colName => $alterSql) {
                $checkCol = $pdo->prepare("SHOW COLUMNS FROM booking LIKE '{$colName}'");
                $checkCol->execute();

                if ($checkCol->rowCount() == 0) {
                    $pdo->exec("ALTER TABLE booking {$alterSql}");
                    echo "✅ Campo {$colName} creado en booking<br>";
                } else {
                    echo "✔️ Campo {$colName} ya existe en booking<br>";
                }
            }

        } else {
            echo "⚠️ No existe tabla booking para campos legacy de extras<br>";
        }

        echo "<hr>";

    } catch (Exception $e) {

        echo "❌ Error en {$folder} ({$dbName}): " . $e->getMessage() . "<br><hr>";
    }

    $pdo = null; // cerrar conexión antes de la siguiente base
    $progress['done'][] = $folder;
    $procesadas++;
}

$progress['updated_at'] = date('c');
file_put_contents($progress_file, json_encode($progress), LOCK_EX);

$pendientes = 0;
foreach ($bases as $info) {
    if (!in_array($info['folder'], $progress['done'], true)) $pendientes++;
}

echo "<hr><strong>RESUMEN:</strong> procesadas ahora: {$procesadas}, errores: {$errores}, total: " . count($bases) . "<br>";
echo "PENDIENTES={$pendientes}<br>";
if ($pendientes > 0) {
    echo "➡️ Ejecutar update.php de nuevo para continuar con las bases pendientes.<br>";
} else {
    echo "🎉 Pasada completa: todas las bases procesadas.<br>";
}
