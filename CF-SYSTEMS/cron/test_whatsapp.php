<?php
// ====================================================
// 🚀 PRUEBA DE CONEXIÓN CON EL NUEVO NÚMERO WHATSAPP
// ====================================================
date_default_timezone_set('America/Santo_Domingo');

// ============================================
// 🔐 TOKEN Y PHONE ID
// ============================================
$whatsapp_token = "EAAQWTTTuumYBPlbC2FLM4qjmenCyntudVJu90IuOE1t5ju3heD4ZBXH3cykrXYYo7DY7nO2EPY1SIpuqQiYAXUoeQIbADZCtn7k6jBYH9T5qSgVZCanmhv06IADZBcIZB2fZCmsjyHRk4HrPmnFYUtGfZArJe12CEwi7cBaRReYJxTgAtIzZCZAWAUK8rINL8shhZB8QZDZD";
$phone_id = "875308285657798"; // ✅ Nuevo número verificado

// ============================================
// 📞 NÚMERO DE DESTINO (CÓDIGO PAÍS + NÚMERO)
// ============================================
$telefono_destino = "18099999636"; // 👈 CAMBIA AQUÍ tu número de WhatsApp real

// ============================================
// 💬 MENSAJE DE PRUEBA
// ============================================
$mensaje = "✅ Prueba exitosa desde *Assanpos*.\nTu conexión con el nuevo número está funcionando correctamente.";

// ============================================
// 🌐 ENDPOINT
// ============================================
$url = "https://graph.facebook.com/v24.0/{$phone_id}/messages";

// ============================================
// 📦 ESTRUCTURA DEL MENSAJE
// ============================================
$data = [
    "messaging_product" => "whatsapp",
    "to" => $telefono_destino,
    "type" => "text",
    "text" => ["preview_url" => false, "body" => $mensaje]
];

// ============================================
// 📤 ENVÍO DE LA SOLICITUD
// ============================================
$options = [
    "http" => [
        "header" => "Authorization: Bearer {$whatsapp_token}\r\n" .
                    "Content-Type: application/json\r\n",
        "method" => "POST",
        "content" => json_encode($data),
        "ignore_errors" => true
    ]
];

$context  = stream_context_create($options);
$response = file_get_contents($url, false, $context);

// ============================================
// 🧾 RESULTADO
// ============================================
echo "<pre>Respuesta de WhatsApp:\n" . htmlspecialchars($response) . "</pre>";
?>