<?php
$base = new Database();
$con = $base->connect();

// Recibir datos del GPS
$imei = $_GET['imei'] ?? null;
$lat = $_GET['lat'] ?? null;
$lng = $_GET['lng'] ?? null;
$speed = $_GET['speed'] ?? 0;

if(!$imei || !$lat || !$lng){
    http_response_code(400);
    echo "❌ Datos incompletos";
    exit;
}

// Buscar GPS registrado
$stmt = $con->prepare("SELECT id FROM gps_devices WHERE imei = :imei");
$stmt->execute([':imei'=>$imei]);
$gps = $stmt->fetch();

if(!$gps){
    http_response_code(404);
    echo "❌ GPS no registrado";
    exit;
}

// Insertar posición
$stmt = $con->prepare("INSERT INTO gps_positions (gps_id, latitude, longitude, speed) VALUES (:gps_id, :lat, :lng, :speed)");
$stmt->execute([
    ':gps_id'=>$gps['id'],
    ':lat'=>$lat,
    ':lng'=>$lng,
    ':speed'=>$speed
]);

echo "✅ Posición guardada correctamente";
?>
