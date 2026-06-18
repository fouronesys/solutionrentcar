<?php
$base = new Database();
$con = $base->connect();

$name = $_POST['name'];
$imei = $_POST['imei'];

$stmt = $con->prepare("INSERT INTO gps_devices (name, imei) VALUES (:name, :imei)");
$stmt->execute([':name'=>$name, ':imei'=>$imei]);

echo "✅ GPS registrado correctamente";
?>
