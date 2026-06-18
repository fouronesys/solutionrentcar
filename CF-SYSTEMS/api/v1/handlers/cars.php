<?php
/**
 * GET /cars                  list (optional ?stock_id=&status=&q=&available_from=&available_to=)
 * GET /cars/{id}             detail
 */

if ($method !== 'GET') ApiResponse::err('method_not_allowed', 'Use GET', 405);

ApiAuth::require(); // any authenticated principal can view cars

$id = isset($segments[1]) ? intval($segments[1]) : 0;

if ($id > 0) {
    $c = CarsData::getById($id);
    if (!$c || !$c->id) ApiResponse::err('not_found', 'Vehículo no encontrado', 404);
    ApiResponse::ok(['car' => ApiHelpers::carToArray($c)]);
}

$con = Database::getCon();
$where = [];
if (!empty($_GET['stock_id'])) $where[] = "stock_id=" . intval($_GET['stock_id']);
if (isset($_GET['status']) && $_GET['status'] !== '') $where[] = "status=" . intval($_GET['status']);
if (!empty($_GET['q'])) {
    $q = $con->real_escape_string((string)$_GET['q']);
    $where[] = "(name LIKE '%$q%' OR year LIKE '%$q%' OR plate LIKE '%$q%')";
}

$from = trim((string)($_GET['available_from'] ?? ''));
$to   = trim((string)($_GET['available_to'] ?? ''));
if ($from !== '' && $to !== '') {
    $dateRe = '/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}(:\d{2})?)?$/';
    if (!preg_match($dateRe, $from) || !preg_match($dateRe, $to)) {
        ApiResponse::err('invalid_request',
            'available_from y available_to deben ser YYYY-MM-DD o YYYY-MM-DD HH:MM[:SS]', 400);
    }
    if (strtotime($from) === false || strtotime($to) === false) {
        ApiResponse::err('invalid_request', 'available_from/available_to no son fechas válidas', 400);
    }
    $f = $con->real_escape_string($from);
    $t = $con->real_escape_string($to);
    // Exclude cars with overlapping active bookings (status 0,1,3)
    $where[] = "id NOT IN (SELECT car_id FROM booking
                  WHERE status IN (0,1,3)
                  AND NOT (end_at < '$f' OR start_at > '$t'))";
}

$sqlExtra = '';
if ($where) $sqlExtra = 'WHERE ' . implode(' AND ', $where);

$limit  = max(1, min(200, intval($_GET['limit'] ?? 50)));
$offset = max(0, intval($_GET['offset'] ?? 0));
$sqlExtra .= " ORDER BY id DESC LIMIT $offset,$limit";

$rows = CarsData::getAllBySQL($sqlExtra);
$out = [];
foreach ($rows as $c) $out[] = ApiHelpers::carToArray($c);

ApiResponse::ok([
    'cars'   => $out,
    'limit'  => $limit,
    'offset' => $offset,
    'count'  => count($out),
]);
