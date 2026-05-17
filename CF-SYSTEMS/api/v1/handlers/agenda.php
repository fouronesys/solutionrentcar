<?php
/**
 * GET /agenda?date=YYYY-MM-DD   — staff-only agenda of deliveries (entregas)
 * and returns (devoluciones) for the given day (defaults to today).
 *
 * Response: { date, deliveries:[...], returns:[...] }
 *  - deliveries: bookings whose start_at falls within the day and are still
 *    pending or confirmed (status 0,1).
 *  - returns:    bookings whose end_at falls within the day and are currently
 *    delivered (status 3).
 */
$auth = ApiAuth::require();
if ($auth['type'] !== 'user') ApiResponse::err('forbidden', 'Solo staff', 403);
if ($method !== 'GET')        ApiResponse::err('method_not_allowed', 'Use GET', 405);

$con  = Database::getCon();
$date = trim((string)($_GET['date'] ?? date('Y-m-d')));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    ApiResponse::err('invalid_request', 'date debe ser YYYY-MM-DD', 400);
}
$dEsc  = $con->real_escape_string($date);
$sid   = intval($auth['stock_id']);
$stockFilter = $sid > 0 ? " AND stock_id=$sid" : '';

$deliveries = BookingData::getAllBySQL(
    "WHERE date(start_at)='$dEsc' AND status IN (0,1) $stockFilter ORDER BY start_at ASC"
);
$returns = BookingData::getAllBySQL(
    "WHERE date(end_at)='$dEsc' AND status=3 $stockFilter ORDER BY end_at ASC"
);

$expand = function($b) {
    $car    = $b->car_id    ? CarsData::getById($b->car_id)    : null;
    $person = $b->person_id ? PersonData::getById($b->person_id) : null;
    return [
        'booking' => ApiHelpers::bookingToArray($b),
        'car'     => $car ? ApiHelpers::carToArray($car) : null,
        'client'  => $person ? ApiHelpers::personToArray($person) : null,
    ];
};

ApiResponse::ok([
    'date'       => $date,
    'deliveries' => array_map($expand, $deliveries),
    'returns'    => array_map($expand, $returns),
]);
