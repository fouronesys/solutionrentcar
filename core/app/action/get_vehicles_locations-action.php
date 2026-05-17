<?php
$base = new Database();
$con = $base->connect(); // mysqli

$mode = $_GET['mode'] ?? 'last';
$vehicle_id = isset($_GET['vehicle_id']) ? intval($_GET['vehicle_id']) : null;
$from = $_GET['from'] ?? null;
$to   = $_GET['to'] ?? null;

// Sanitizar fechas si vienen
if ($from) $from = $con->real_escape_string($from);
if ($to)   $to   = $con->real_escape_string($to);

// ===================== HISTORIAL =====================
if ($mode === 'history' && $vehicle_id) {
    $sql = "
        SELECT p.latitude, p.longitude, p.speed, p.created_at
        FROM gps_positions p
        INNER JOIN gps_devices g ON g.id = p.gps_id
        INNER JOIN cars c ON c.gps_id = g.id
        WHERE c.id = $vehicle_id
    ";

    if ($from && $to) {
        $sql .= " AND p.created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'";
    }

    $sql .= " ORDER BY p.created_at ASC";

    $query = $con->query($sql);
    $positions = [];
    while ($r = $query->fetch_assoc()) {
        $positions[] = $r;
    }

    header('Content-Type: application/json');
    echo json_encode([
        'mode'       => 'history',
        'vehicle_id' => $vehicle_id,
        'positions'  => $positions
    ]);
    exit;
}

// ===================== ÚLTIMA POSICIÓN =====================
$sql = "
    SELECT c.id AS vehicle_id, c.invoice_file, c.plate, c.name,
           p.latitude, p.longitude, p.speed, p.created_at
    FROM cars c
    INNER JOIN gps_devices g ON g.id = c.gps_id
    INNER JOIN gps_positions p ON p.gps_id = g.id
    WHERE p.created_at = (
        SELECT MAX(created_at) 
        FROM gps_positions 
        WHERE gps_id = g.id
    )
";

$query = $con->query($sql);
$vehicles = [];
while ($r = $query->fetch_assoc()) {
    $vehicles[] = $r;
}

header('Content-Type: application/json');
echo json_encode([
    'mode'     => 'last',
    'vehicles' => $vehicles
]);
