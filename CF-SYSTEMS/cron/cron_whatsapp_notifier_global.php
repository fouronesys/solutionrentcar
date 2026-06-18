<?php
// ===============================================
// 🚀 CRON GLOBAL WHATSAPP (Usuarios + Clientes + Choferes)
// ===============================================
date_default_timezone_set('America/Santo_Domingo');
$base_path = dirname(__DIR__, 2) . '/';

// ======================================================
// 🧾 FUNCIONES GENERALES
// ======================================================
function sendWhatsAppTemplate($url, $token, $payload) {
    $opts = [
        "http" => [
            "method" => "POST",
            "header" => "Authorization: Bearer {$token}\r\nContent-Type: application/json\r\n",
            "content" => json_encode($payload),
            "ignore_errors" => true,
            "timeout" => 30
        ]
    ];
    $ctx = stream_context_create($opts);
    $res = @file_get_contents($url, false, $ctx);
    if ($res === false) return false;
    $json = json_decode($res, true);
    return isset($json['messages'][0]['id']);
}

// ======================================================
// 📁 RUTA BASE Y LOG
// ======================================================
$log_global = __DIR__ . "/whatsapp_global.log";
$log_delivery = __DIR__ . "/whatsapp_delivery.log";
foreach ([$log_global, $log_delivery] as $log) {
    if (!file_exists($log)) file_put_contents($log, "=== NUEVO LOG CREADO ===\n");
    if (filesize($log) > 1024 * 1024) {
        $bk = str_replace(".log", "_" . date("Ymd_His") . ".bak", $log);
        rename($log, $bk);
        file_put_contents($log, "=== LOG REINICIADO AUTOMÁTICAMENTE ===\n");
    }
}

$carpetas = array_filter(glob($base_path . '*'), function ($dir) {
    $excluidas = ['CLIENTES', 'CF-SYSTEMS', 'logs'];
    return is_dir($dir) && !in_array(basename($dir), $excluidas);
});

$total_mensajes = 0;
$total_enviados = 0;

// ======================================================
// 🔹 CREDENCIALES WHATSAPP
// ======================================================
$PHONE_ID = "875308285657798";
$API_URL = "https://graph.facebook.com/v24.0/{$PHONE_ID}/messages";
$WHATSAPP_TOKEN = "EAAQWTTTuumYBPlbC2FLM4qjmenCyntudVJu90IuOE1t5ju3heD4ZBXH3cykrXYYo7DY7nO2EPY1SIpuqQiYAXUoeQIbADZCtn7k6jBYH9T5qSgVZCanmhv06IADZBcIZB2fZCmsjyHRk4HrPmnFYUtGfZArJe12CEwi7cBaRReYJxTgAtIzZCZAWAUK8rINL8shhZB8QZDZD";

// ======================================================
// 🔹 1. NOTIFICAR A USUARIOS INTERNOS
// ======================================================
foreach ($carpetas as $carpeta) {
    $empresa = basename($carpeta);
    $cfg = "$carpeta/core/controller/Database.php";
    if (!file_exists($cfg)) continue;

    preg_match('/\$this->host\s*=\s*[\'"](.*?)[\'"]/', file_get_contents($cfg), $h);
    preg_match('/\$this->user\s*=\s*[\'"](.*?)[\'"]/', file_get_contents($cfg), $u);
    preg_match('/\$this->pass\s*=\s*[\'"](.*?)[\'"]/', file_get_contents($cfg), $p);
    preg_match('/\$this->ddbb\s*=\s*[\'"](.*?)[\'"]/', file_get_contents($cfg), $d);

    $con = @new mysqli($h[1], $u[1], $p[1], $d[1]);
    if ($con->connect_error) continue;

    $sql = "SELECT b.*, s.phone AS stock_phone, c.name AS client_name,
                   car.name AS car_name, car.plate, br.name AS brand_name, co.name AS color_name
            FROM booking b
            INNER JOIN stock s ON s.id = b.stock_id AND s.update = 1
            INNER JOIN person c ON c.id = b.person_id
            INNER JOIN cars car ON car.id = COALESCE(b.car_id, b.car2_id)
            LEFT JOIN brand br ON br.id = car.brand_id
            LEFT JOIN color co ON co.id = car.exterior_id
            WHERE b.notified = 0
              AND b.status <> 3
              AND ((TIMESTAMPDIFF(HOUR, NOW(), b.start_at) BETWEEN 0 AND 3)
                OR (TIMESTAMPDIFF(HOUR, NOW(), b.end_at) BETWEEN 0 AND 3))";

    $res = $con->query($sql);
    while ($r = $res->fetch_assoc()) {
        $phones = [];
        $resU = $con->query("SELECT phone FROM user WHERE stock_id = {$r['stock_id']} AND phone<>''");
        while ($u = $resU->fetch_assoc()) {
            $num = preg_replace('/\D/', '', trim($u["phone"]));
            if (!empty($num)) $phones[] = $num;
        }
        if (empty($phones) && !empty($r["stock_phone"]))
            $phones[] = preg_replace('/\D/', '', trim($r["stock_phone"]));

        foreach ($phones as $num) {
            $payload = [
                "messaging_product" => "whatsapp",
                "to" => $num,
                "type" => "template",
                "template" => [
                    "name" => "entrega_recordatorio",
                    "language" => ["code" => "es"],
                    "components" => [[
                        "type" => "header",
                        "parameters" => [[
                            "type" => "image",
                            "image" => ["link" => "https://rentals.assanpos.com/CF-SYSTEMS/cron/rentcar.png"]
                        ]]
                    ]]
                ]
            ];
            if (sendWhatsAppTemplate($API_URL, $WHATSAPP_TOKEN, $payload)) {
                $con->query("UPDATE booking SET notified=1 WHERE id=" . $r["id"]);
                $total_mensajes++;
            }
        }
    }
    $con->close();
}

// ======================================================
// 🔹 2. NOTIFICAR A CLIENTES
// ======================================================
foreach ($carpetas as $carpeta) {
    $empresa = basename($carpeta);
    $cfg = "$carpeta/core/controller/Database.php";
    if (!file_exists($cfg)) continue;

    preg_match('/\$this->host\s*=\s*[\'"](.*?)[\'"]/', file_get_contents($cfg), $h);
    preg_match('/\$this->user\s*=\s*[\'"](.*?)[\'"]/', file_get_contents($cfg), $u);
    preg_match('/\$this->pass\s*=\s*[\'"](.*?)[\'"]/', file_get_contents($cfg), $p);
    preg_match('/\$this->ddbb\s*=\s*[\'"](.*?)[\'"]/', file_get_contents($cfg), $d);

    $con = @new mysqli($h[1], $u[1], $p[1], $d[1]);
    if ($con->connect_error) continue;

    $sql = "SELECT b.*, s.name AS stock_name, s.ticket_image, c.name AS client_name, c.phone1 AS client_phone,
                   car.name AS car_name, br.name AS brand_name, co.name AS color_name
            FROM booking b
            INNER JOIN stock s ON s.id=b.stock_id AND s.update=1
            INNER JOIN person c ON c.id=b.person_id
            INNER JOIN cars car ON car.id=COALESCE(b.car_id,b.car2_id)
            LEFT JOIN brand br ON br.id=car.brand_id
            LEFT JOIN color co ON co.id=car.exterior_id
            WHERE b.notified_clients=0
              AND b.status<>3
              AND ((TIMESTAMPDIFF(HOUR,NOW(),b.start_at) BETWEEN 0 AND 1)
                OR (TIMESTAMPDIFF(HOUR,NOW(),b.end_at) BETWEEN 0 AND 1))";

    $res = $con->query($sql);
    while ($r = $res->fetch_assoc()) {
        $num = preg_replace('/\D/', '', trim($r["client_phone"] ?? ""));
        if (!$num) continue;

        $header_image = !empty($r["ticket_image"])
            ? "https://rentals.assanpos.com/{$empresa}/CF-SYSTEMS/storage/configuration/{$r["ticket_image"]}"
            : "https://rentals.assanpos.com/CF-SYSTEMS/cron/rentcar.png";

        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $num,
            "type" => "template",
            "template" => [
                "name" => "clientes_recordatorio",
                "language" => ["code" => "es"],
                "components" => [[
                    "type" => "header",
                    "parameters" => [[
                        "type" => "image",
                        "image" => ["link" => $header_image]
                    ]]
                ]]
            ]
        ];
        if (sendWhatsAppTemplate($API_URL, $WHATSAPP_TOKEN, $payload)) {
            $con->query("UPDATE booking SET notified_clients=1 WHERE id=" . $r["id"]);
            $total_mensajes++;
        }
    }
    $con->close();
}

// ======================================================
// 🔹 3. NOTIFICAR A CHOFERES
// ======================================================
foreach ($carpetas as $carpeta) {
    $empresa = basename($carpeta);
    $cfg = "$carpeta/core/controller/Database.php";
    if (!file_exists($cfg)) continue;

    preg_match('/\$this->host\s*=\s*[\'"](.*?)[\'"]/', file_get_contents($cfg), $h);
    preg_match('/\$this->user\s*=\s*[\'"](.*?)[\'"]/', file_get_contents($cfg), $u);
    preg_match('/\$this->pass\s*=\s*[\'"](.*?)[\'"]/', file_get_contents($cfg), $p);
    preg_match('/\$this->ddbb\s*=\s*[\'"](.*?)[\'"]/', file_get_contents($cfg), $d);

    $con = @new mysqli($h[1], $u[1], $p[1], $d[1]);
    if ($con->connect_error) continue;

    $sql = "SELECT b.id, b.code, b.start_at, b.end_at, b.place_start, b.place_end,
                   s.name AS stock_name, s.ticket_image, c.name AS client_name,
                   car.name AS car_name, car.plate, br.name AS brand_name, co.name AS color_name, u.phone AS delivery_phone
            FROM booking b
            INNER JOIN stock s ON s.id=b.stock_id
            INNER JOIN person c ON c.id=b.person_id
            INNER JOIN cars car ON car.id=COALESCE(b.car_id,b.car2_id)
            LEFT JOIN brand br ON br.id=car.brand_id
            LEFT JOIN color co ON co.id=car.exterior_id
            INNER JOIN delivery d ON d.booking_id=b.id
            LEFT JOIN user u ON u.id=d.delivery_id
            WHERE b.status<>3 AND s.update=1
              AND ((TIMESTAMPDIFF(MINUTE,NOW(),b.start_at) BETWEEN 0 AND 60)
                OR (TIMESTAMPDIFF(MINUTE,NOW(),b.end_at) BETWEEN 0 AND 60))";

    $res = $con->query($sql);
    while ($r = $res->fetch_assoc()) {
        $phone = preg_replace('/\D/', '', trim($r['delivery_phone'] ?? ''));
        if (!$phone) continue;

        $header_image = !empty($r["ticket_image"])
            ? "https://rentals.assanpos.com/{$empresa}/CF-SYSTEMS/storage/configuration/{$r["ticket_image"]}"
            : "https://rentals.assanpos.com/CF-SYSTEMS/cron/rentcar.png";

        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $phone,
            "type" => "template",
            "template" => [
                "name" => "repuesta_chofer",
                "language" => ["code" => "es"],
                "components" => [[
                    "type" => "header",
                    "parameters" => [[
                        "type" => "image",
                        "image" => ["link" => $header_image]
                    ]]
                ]]
            ]
        ];
        if (sendWhatsAppTemplate($API_URL, $WHATSAPP_TOKEN, $payload)) {
            $total_enviados++;
        }
    }
    $con->close();
}

file_put_contents($log_global, "✅ CRON FINALIZADO ({$total_mensajes} mensajes usuarios/clientes) - ".date('Y-m-d H:i:s')."\n", FILE_APPEND);
file_put_contents($log_delivery, "🏁 FIN CRON CHOFERES (enviados: {$total_enviados}) - ".date('Y-m-d H:i:s')."\n", FILE_APPEND);
?>
