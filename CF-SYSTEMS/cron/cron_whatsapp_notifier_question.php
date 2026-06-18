<?php
// ===============================================
// 🚀 CRON GLOBAL WHATSAPP TEMPLATE + IMAGEN
// ===============================================
date_default_timezone_set('America/Santo_Domingo');
$log_file = __DIR__ . "/whatsapp_global.log";

if (!file_exists($log_file)) file_put_contents($log_file, "=== NUEVO LOG CREADO ===\n");
if (filesize($log_file) > 1024 * 1024) {
    $backup = __DIR__ . "/whatsapp_global_" . date("Ymd_His") . ".bak";
    rename($log_file, $backup);
    file_put_contents($log_file, "=== LOG REINICIADO AUTOMÁTICAMENTE ===\n");
}

$is_cli = (php_sapi_name() === 'cli');
$origen = $is_cli ? '🖥️ CRON SERVER' : '🌐 Manual';
file_put_contents($log_file, "\n\n[" . date('Y-m-d H:i:s') . "] 🔄 CRON INICIADO - {$origen}\n", FILE_APPEND);

// =============================
// 🔍 Buscar carpetas de clientes
// =============================
$base_path = dirname(__DIR__, 2) . '/';
$carpetas = array_filter(glob($base_path . '*'), function ($dir) {
    $excluidas = ['CLIENTES', 'CF-SYSTEMS'];
    return is_dir($dir) && !in_array(basename($dir), $excluidas);
});

$total_mensajes = 0;


// ========================================================
// 🔹 CRON → ENVIAR A CLIENTES CUANDO update_time TIENE VALOR
// ========================================================
foreach ($carpetas as $carpeta) {
    $config = $carpeta . '/core/controller/Database.php';
    if (!file_exists($config)) continue;

    $contenido = file_get_contents($config);
    preg_match('/\$this->host\s*=\s*[\'"](.*?)[\'"]/', $contenido, $host);
    preg_match('/\$this->user\s*=\s*[\'"](.*?)[\'"]/', $contenido, $user);
    preg_match('/\$this->pass\s*=\s*[\'"](.*?)[\'"]/', $contenido, $pass);
    preg_match('/\$this->ddbb\s*=\s*[\'"](.*?)[\'"]/', $contenido, $db);

    $empresa = basename($carpeta);

    try {
        $con = new mysqli($host[1], $user[1], $pass[1], $db[1]);
        if ($con->connect_error) throw new Exception("❌ Error conexión DB");

        // 🔍 Solo cuando update_time tiene algún valor
        $sql = "SELECT b.*, 
                       s.id AS stock_id, s.phone AS stock_phone, s.name AS stock_name, s.ticket_image,
                       c.name AS client_name, c.phone1 AS client_phone,
                       car.name AS car_name, car.plate,
                       br.name AS brand_name, co.name AS color_name
                FROM booking b
                INNER JOIN stock s ON s.id = b.stock_id
                INNER JOIN person c ON c.id = b.person_id
                INNER JOIN cars car ON car.id = COALESCE(b.car_id, b.car2_id)
                LEFT JOIN brand br ON br.id = car.brand_id
                LEFT JOIN color co ON co.id = car.exterior_id
                WHERE b.update_time IS NOT NULL AND b.update_time <> ''
                AND b.status <> 3 AND s.update = 1
                GROUP BY b.id";

        $res = $con->query($sql);
        if ($res && $res->num_rows > 0) {
            while ($r = $res->fetch_assoc()) {
                $phones = [];
                if (!empty($r["client_phone"])) {
                    $num = preg_replace('/\D/', '', trim($r["client_phone"]));
                    if (!empty($num)) $phones[] = $num;
                }
                if (empty($phones)) continue;

                $cliente  = ucfirst(strtolower($r["client_name"]));
                $vehiculo = strtolower("{$r['brand_name']} {$r['car_name']}");
                $color    = strtolower($r['color_name'] ?: 'sin color');
                $lugar    = !empty($r['place_start']) ? $r['place_start'] : $r['place_end'];
                $hora     = !empty($r['start_at']) ? date("g:i A", strtotime($r['start_at'])) : date("g:i A", strtotime($r['end_at']));
                $tipo     = !empty($r['start_at']) ? "salida" : "entrega";
                $nombre_empresa = trim($r["stock_name"]);

                $ticket_image = trim($r["ticket_image"]);
                if (!empty($ticket_image))
                    $header_image_url = "https://rentals.assanpos.com/{$empresa}/CF-SYSTEMS/storage/configuration/" . $ticket_image;
                else
                    $header_image_url = "https://rentals.assanpos.com/CF-SYSTEMS/cron/rentcar.png";

                $url = "https://graph.facebook.com/v20.0/875308285657798/messages";
                $whatsapp_token = "EAAQWTTTuumYBPlbC2FLM4qjmenCyntudVJu90IuOE1t5ju3heD4ZBXH3cykrXYYo7DY7nO2EPY1SIpuqQiYAXUoeQIbADZCtn7k6jBYH9T5qSgVZCanmhv06IADZBcIZB2fZCmsjyHRk4HrPmnFYUtGfZArJe12CEwi7cBaRReYJxTgAtIzZCZAWAUK8rINL8shhZB8QZDZD";

                foreach ($phones as $num) {
                    $payload = [
                        "messaging_product" => "whatsapp",
                        "to" => $num,
                        "type" => "template",
                        "template" => [
                            "name" => "llegada_recordatorio",
                            "language" => ["code" => "es"],
                            "components" => [
                                [
                                    "type" => "header",
                                    "parameters" => [[
                                        "type" => "image",
                                        "image" => ["link" => $header_image_url]
                                    ]]
                                ],
                                [
                                    "type" => "body",
                                    "parameters" => [
                                        ["type" => "text", "text" => $cliente],           // {{1}} → Nombre del cliente
                                        ["type" => "text", "text" => $vehiculo],          // {{2}} → Marca + modelo
                                        ["type" => "text", "text" => $color],             // {{3}} → Color del vehículo
                                        ["type" => "text", "text" => $lugar],             // {{4}} → Lugar (Aeropuerto, Hotel, etc.)
                                        ["type" => "text", "text" => $r["update_time"]],  // {{5}} → Tiempo (ej. 15 minutos)
                                        ["type" => "text", "text" => $tipo],              // {{6}} → Tipo (ej. entrega / recogida)
                                        ["type" => "text", "text" => $nombre_empresa]     // {{7}} → Nombre del RentCar
                                    ]           
                                ]
                            ]
                        ]
                    ];

                    if (sendWhatsAppTemplate($url, $whatsapp_token, $payload)) {
                        // ✅ Limpiar update_time después de enviar
                        $con->query("UPDATE booking SET update_time = '' WHERE id = " . $r["id"]);
                        $total_mensajes++;
                    }
                }
            }
        }
        $con->close();
    } catch (Exception $e) {
        file_put_contents($log_file, "[{$empresa}] ⚠️ ".$e->getMessage()."\n", FILE_APPEND);
    }
}


// ========================================================
// 🔧 FUNCIÓN PARA ENVIAR PLANTILLA CON IMAGEN
// ========================================================
function sendWhatsAppTemplate($url, $token, $payload) {
    $opts = [
        "http" => [
            "method" => "POST",
            "header" => "Authorization: Bearer {$token}\r\nContent-Type: application/json\r\n",
            "content" => json_encode($payload),
            "ignore_errors" => true
        ]
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);
    if ($response === FALSE) return false;
    $json = json_decode($response, true);
    return isset($json['messages'][0]['id']);
}

file_put_contents($log_file, "✅ CRON FINALIZADO ({$total_mensajes} mensajes enviados) - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
?>