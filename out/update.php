<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$base_path = __DIR__ . '/';

$carpetas = array_filter(glob($base_path . '/*'), function ($dir) {
    $excluidas = ['CLIENTES', 'CF-SYSTEMS', 'logs', 'PWA'];
    return is_dir($dir) && !in_array(basename($dir), $excluidas);
});

/*
|--------------------------------------------------------------------------
| 1) LEER TODAS LAS CONFIGURACIONES
|--------------------------------------------------------------------------
*/
$bases = [];

foreach ($carpetas as $carpeta) {

    $config_path = $carpeta . '/core/controller/Database.php';

    if (!file_exists($config_path)) {
        echo "❌ No existe Database.php en: " . basename($carpeta) . "<br><hr>";
        continue;
    }

    $contenido = file_get_contents($config_path);

    preg_match('/\$this->host\s*=\s*[\'"](.*?)[\'"]/', $contenido, $host);
    preg_match('/\$this->user\s*=\s*[\'"](.*?)[\'"]/', $contenido, $user);
    preg_match('/\$this->pass\s*=\s*[\'"](.*?)[\'"]/', $contenido, $pass);
    preg_match('/\$this->ddbb\s*=\s*[\'"](.*?)[\'"]/', $contenido, $db);

    $db_host = $host[1] ?? '';
    if ($db_host === 'localhost' || $db_host === '127.0.0.1') $db_host = '127.0.0.1'; // RENTCAR_HOST_PATCH
    $db_user = $user[1] ?? '';
    $db_pass = $pass[1] ?? '';
    $db_name = $db[1] ?? '';

    if (empty($db_host) || empty($db_user) || empty($db_name)) {
        echo "❌ Datos incompletos en: " . basename($carpeta) . "<br><hr>";
        continue;
    }

    $bases[] = [
        'folder' => basename($carpeta),
        'host'   => $db_host,
        'user'   => $db_user,
        'pass'   => $db_pass,
        'db'     => $db_name
    ];
}

/*
|--------------------------------------------------------------------------
| 2) ABRIR TODAS LAS CONEXIONES
|--------------------------------------------------------------------------
*/
$pdos = [];

foreach ($bases as $info) {

    try {

        $pdo = new PDO(
            "mysql:host={$info['host']};dbname={$info['db']};charset=utf8mb4",
            $info['user'],
            $info['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );

        $pdos[$info['db']] = [
            'pdo'    => $pdo,
            'folder' => $info['folder'],
            'db'     => $info['db']
        ];

        echo "✅ Conectado: <strong>{$info['db']}</strong><br>";

    } catch (Exception $e) {

        echo "❌ Error conectando {$info['db']}: " . $e->getMessage() . "<br><hr>";
    }
}

echo "<hr>";

/*
|--------------------------------------------------------------------------
| 3) PROCESAR CADA BASE
|--------------------------------------------------------------------------
*/
foreach ($pdos as $dbName => $data) {

    $pdo    = $data['pdo'];
    $folder = $data['folder'];

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
}
?>