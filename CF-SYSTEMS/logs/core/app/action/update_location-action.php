<?php
$base = new Database();
$con = $base->connect();

$gps_id = $_POST['gps_id'];
$lat = $_POST['lat'];
$lng = $_POST['lng'];
$speed = $_POST['speed'] ?? 0;

$stmt = $con->prepare("INSERT INTO gps_locations (gps_id, lat, lng, speed) VALUES (:gps_id, :lat, :lng, :speed)");
$stmt->execute([
    ':gps_id' => $gps_id,
    ':lat' => $lat,
    ':lng' => $lng,
    ':speed' => $speed
]);

echo "Ubicación registrada";
?>
