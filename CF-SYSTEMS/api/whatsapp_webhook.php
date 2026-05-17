<?php
// ==========================================
// 🌐 WEBHOOK WHATSAPP: RECEPCIÓN DE RESPUESTAS
// ==========================================
date_default_timezone_set('America/Santo_Domingo');

$input = file_get_contents('php://input');
$data = json_decode($input, true);
file_put_contents(__DIR__ . '/whatsapp_log.json', $input . "\n", FILE_APPEND);

// ==========================================
// 📩 Verificar mensaje entrante
// ==========================================
if (!isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
    echo "NO_MESSAGE";
    exit;
}

$msg = $data['entry'][0]['changes'][0]['value']['messages'][0];
$telefono = preg_replace('/\D/', '', $msg['from']); // Número del chofer
$texto    = trim($msg['text']['body']);              // Respuesta del chofer

file_put_contents(__DIR__ . '/whatsapp_log.txt', "[".date('Y-m-d H:i:s')."] {$telefono}: {$texto}\n", FILE_APPEND);

// ==========================================
// 🔍 Buscar código de reserva en el mensaje (#BRV47-241020)
// ==========================================
if (preg_match('/#([A-Z0-9\-]+)/', $texto, $match)) {
    $codigo = trim($match[0]); // Ej: #BRV47-241020

    $base_path = dirname(__DIR__, 1) . '/';
    $carpetas = array_filter(glob($base_path . '*'), function ($dir) {
        $excluidas = ['CLIENTES', 'CF-SYSTEMS'];
        return is_dir($dir) && !in_array(basename($dir), $excluidas);
    });

    $encontrado = false;

    foreach ($carpetas as $carpeta) {
        $config_path = $carpeta . '/core/controller/Database.php';
        if (!file_exists($config_path)) continue;

        $contenido = file_get_contents($config_path);
        preg_match('/\$this->host\s*=\s*[\'"](.*?)[\'"]/', $contenido, $host);
        preg_match('/\$this->user\s*=\s*[\'"](.*?)[\'"]/', $contenido, $user);
        preg_match('/\$this->pass\s*=\s*[\'"](.*?)[\'"]/', $contenido, $pass);
        preg_match('/\$this->ddbb\s*=\s*[\'"](.*?)[\'"]/', $contenido, $db);

        try {
            $pdo = new PDO("mysql:host={$host[1]};dbname={$db[1]};charset=utf8", $user[1], $pass[1]);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Buscar booking con ese código
            $check = $pdo->prepare("SELECT id FROM booking WHERE code = ?");
            $check->execute([$codigo]);
            $booking = $check->fetch(PDO::FETCH_ASSOC);

            if ($booking) {
                // ✅ Actualizar campo update_time con la respuesta
                $sql = "UPDATE booking SET update_time = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$texto, $booking['id']]);

                file_put_contents(__DIR__ . '/whatsapp_log.txt',
                    "[".date('Y-m-d H:i:s')."] ✅ Respuesta actualizada en " . basename($carpeta) . " (booking_id={$booking['id']})\n",
                    FILE_APPEND
                );

                $encontrado = true;
                break;
            }

        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/whatsapp_log.txt',
                "[".date('Y-m-d H:i:s')."] ⚠️ Error en " . basename($carpeta) . ": {$e->getMessage()}\n",
                FILE_APPEND
            );
        }
    }

    if (!$encontrado) {
        file_put_contents(__DIR__ . '/whatsapp_log.txt',
            "[".date('Y-m-d H:i:s')."] ❌ Código {$codigo} no encontrado en ninguna base\n",
            FILE_APPEND
        );
    }

} else {
    file_put_contents(__DIR__ . '/whatsapp_log.txt',
        "[".date('Y-m-d H:i:s')."] ⚠️ Mensaje sin código (#XXX###-######): {$texto}\n",
        FILE_APPEND
    );
}

// ==========================================
// RESPUESTA A META
// ==========================================
echo "EVENT_RECEIVED";
?>
