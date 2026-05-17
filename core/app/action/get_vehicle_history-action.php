<?php
$base = new Database();
$con = $base->connect();

$vehicle_id = $_GET['vehicle_id'];
$date = $_GET['date']; // formato YYYY-MM-DD

$sql = "
    SELECT l.lat, l.lng, l.speed, l.recorded_at
    FROM gps_locations l
    JOIN vehicle_gps vg ON l.gps_id = vg.gps_id
    WHERE vg.vehicle_id = :vehicle_id
    AND DATE(l.recorded_at) = :date
    ORDER BY l.recorded_at ASC
";

$stmt = $con->prepare($sql);
$stmt->execute([':vehicle_id' => $vehicle_id, ':date' => $date]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
?>
