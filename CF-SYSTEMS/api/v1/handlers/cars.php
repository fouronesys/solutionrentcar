<?php
/**
 * GET /cars                  list (optional ?stock_id=&status=&q=&available_from=&available_to=)
 * GET /cars/{id}             detail
 * GET /cars/{id}/availability  busy booking ranges (optional ?from=&to=)
 */

if ($method !== 'GET') ApiResponse::err('method_not_allowed', 'Use GET', 405);

ApiAuth::require(); // any authenticated principal can view cars

$id  = isset($segments[1]) ? intval($segments[1]) : 0;
$sub = strtolower((string)($segments[2] ?? ''));

if ($id > 0 && $sub === 'availability') {
    $c = CarsData::getById($id);
    if (!$c || !$c->id) ApiResponse::err('not_found', 'Vehículo no encontrado', 404);

    $con = Database::getCon();
    $dateRe = '/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}(:\d{2})?)?$/';

    // Default window: today .. +12 months
    $from = trim((string)($_GET['from'] ?? ''));
    $to   = trim((string)($_GET['to'] ?? ''));
    if ($from === '') $from = date('Y-m-d');
    if ($to === '')   $to   = date('Y-m-d', strtotime('+12 months'));
    if (!preg_match($dateRe, $from) || !preg_match($dateRe, $to)
        || strtotime($from) === false || strtotime($to) === false) {
        ApiResponse::err('invalid_request', 'from/to deben ser YYYY-MM-DD o YYYY-MM-DD HH:MM[:SS]', 400);
    }

    $f = $con->real_escape_string($from);
    $t = $con->real_escape_string($to);
    // Active bookings (pending=0, confirmed=1, in-progress=3) overlapping window
    $r = @$con->query("SELECT start_at, end_at FROM booking
                       WHERE car_id=$id AND status IN (0,1,3)
                       AND NOT (end_at < '$f' OR start_at > '$t')
                       ORDER BY start_at ASC");
    $busy = [];
    if ($r) {
        while ($row = $r->fetch_assoc()) {
            $busy[] = [
                'start_at' => (string)$row['start_at'],
                'end_at'   => (string)$row['end_at'],
            ];
        }
    }
    ApiResponse::ok([
        'car_id' => $id,
        'from'   => $from,
        'to'     => $to,
        'busy'   => $busy,
    ]);
}

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
