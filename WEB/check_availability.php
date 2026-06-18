<?php
ob_start();
include "layout/header.php";
ob_end_clean();

header("Content-Type: application/json; charset=utf-8");

$base = new Database();
$con = $base->connect();

$car_id = isset($_POST["car_id"]) ? intval($_POST["car_id"]) : 0;
$from   = isset($_POST["from"]) ? trim($_POST["from"]) : "";
$to     = isset($_POST["to"]) ? trim($_POST["to"]) : "";

if($car_id <= 0 || $from == "" || $to == ""){
    echo json_encode([
        "status" => "error",
        "message" => "Seleccione las fechas."
    ]);
    exit;
}

if($to < $from){
    echo json_encode([
        "status" => "error",
        "message" => "La fecha final no puede ser menor."
    ]);
    exit;
}

$from_sql = $con->real_escape_string($from." 00:00:00");
$to_sql   = $con->real_escape_string($to." 23:59:59");

$sql = "
SELECT id, start_at, end_at
FROM booking
WHERE car_id = $car_id
AND (
    start_at <= '$to_sql'
    AND end_at >= '$from_sql'
)
ORDER BY end_at DESC
LIMIT 1
";

$query = $con->query($sql);

if(!$query){
    echo json_encode([
        "status" => "error",
        "message" => $con->error
    ]);
    exit;
}

if($query->num_rows > 0){

    $booking = $query->fetch_assoc();

    $next_date = date("Y-m-d", strtotime($booking["end_at"]." +1 day"));
    $next_date_text = date("d/m/Y", strtotime($next_date));

    echo json_encode([
        "status" => "busy",
        "message" => "Vehículo no disponible para esta fecha. Próxima fecha disponible: ".$next_date_text,
        "next_date" => $next_date
    ]);
    exit;
}

echo json_encode([
    "status" => "available",
    "message" => "Vehículo disponible.",
    "url" => "reservation.php?car_id=".$car_id."&from=".$from."&to=".$to
]);

exit;