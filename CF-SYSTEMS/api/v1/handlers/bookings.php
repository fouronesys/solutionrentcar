<?php
/**
 * GET  /bookings              list (client: own only; staff: stock-scoped) — filters ?status=&from=&to=
 * GET  /bookings/{id}         detail (client: own only)
 * POST /bookings              create (client or staff)
 * POST /bookings/{id}/cancel  cancel
 */

$auth = ApiAuth::require();
$id   = isset($segments[1]) ? intval($segments[1]) : 0;
$sub  = strtolower($segments[2] ?? '');

function _booking_can_view($auth, $b): bool {
    if (!$b || !$b->id) return false;
    if ($auth['type'] === 'client') return intval($b->person_id) === intval($auth['id']);
    return intval($b->stock_id) === intval($auth['stock_id']) || intval($auth['stock_id']) === 0;
}

if ($method === 'GET' && $id > 0 && $sub === '') {
    $b = BookingData::getById($id);
    if (!_booking_can_view($auth, $b)) ApiResponse::err('not_found', 'Reserva no encontrada', 404);
    $car = $b->car_id ? CarsData::getById($b->car_id) : null;
    $person = $b->person_id ? PersonData::getById($b->person_id) : null;
    ApiResponse::ok([
        'booking' => ApiHelpers::bookingToArray($b),
        'car'     => $car ? ApiHelpers::carToArray($car) : null,
        'client'  => $person ? ApiHelpers::personToArray($person) : null,
    ]);
}

if ($method === 'GET' && $id === 0) {
    $con = Database::getCon();
    $where = [];
    if ($auth['type'] === 'client') {
        $where[] = "person_id=" . intval($auth['id']);
    } else {
        $sid = intval($auth['stock_id']);
        if ($sid > 0) $where[] = "stock_id=$sid";
    }
    if (isset($_GET['status']) && $_GET['status'] !== '') $where[] = "status=" . intval($_GET['status']);
    $dateOnlyRe = '/^\d{4}-\d{2}-\d{2}$/';
    if (!empty($_GET['from'])) {
        $fromV = (string)$_GET['from'];
        if (!preg_match($dateOnlyRe, $fromV)) {
            ApiResponse::err('invalid_request', 'from debe ser YYYY-MM-DD', 400);
        }
        $f = $con->real_escape_string($fromV);
        $where[] = "date(created_at) >= '$f'";
    }
    if (!empty($_GET['to'])) {
        $toV = (string)$_GET['to'];
        if (!preg_match($dateOnlyRe, $toV)) {
            ApiResponse::err('invalid_request', 'to debe ser YYYY-MM-DD', 400);
        }
        $t = $con->real_escape_string($toV);
        $where[] = "date(created_at) <= '$t'";
    }
    // Staff-only filters
    if ($auth['type'] === 'user') {
        if (!empty($_GET['client_id'])) {
            $where[] = "person_id=" . intval($_GET['client_id']);
        }
        if (!empty($_GET['car_id'])) {
            $where[] = "car_id=" . intval($_GET['car_id']);
        }
        if (!empty($_GET['q'])) {
            $q = $con->real_escape_string((string)$_GET['q']);
            $where[] = "(code LIKE '%$q%' OR person_id IN (SELECT id FROM person WHERE name LIKE '%$q%' OR lastname LIKE '%$q%' OR phone LIKE '%$q%'))";
        }
    }
    $limit  = max(1, min(200, intval($_GET['limit'] ?? 50)));
    $offset = max(0, intval($_GET['offset'] ?? 0));
    $sqlExtra = ($where ? 'WHERE ' . implode(' AND ', $where) : '')
              . " ORDER BY id DESC LIMIT $offset,$limit";
    $rows = BookingData::getAllBySQL($sqlExtra);
    $out = array_map(fn($b) => ApiHelpers::bookingToArray($b), $rows);
    ApiResponse::ok([
        'bookings' => $out,
        'limit'    => $limit,
        'offset'   => $offset,
        'count'    => count($out),
    ]);
}

// Staff: mark vehicle delivered (status 3) / returned (status 4)
if ($method === 'POST' && $id > 0 && ($sub === 'deliver' || $sub === 'return')) {
    if ($auth['type'] !== 'user') ApiResponse::err('forbidden', 'Solo staff', 403);
    $b = BookingData::getById($id);
    if (!_booking_can_view($auth, $b)) ApiResponse::err('not_found', 'Reserva no encontrada', 404);
    $current = intval($b->status);

    if ($sub === 'deliver') {
        if (!in_array($current, [0, 1], true)) {
            ApiResponse::err('conflict', 'La reserva no está en estado entregable', 409);
        }
        $b->status = 3;
        $b->update_status();
        if ($b->car_id) {
            $car = CarsData::getById(intval($b->car_id));
            if ($car && $car->id) { $car->status = 1; $car->update_status(); }
        }
        $title = 'Vehículo entregado';
        $msg   = 'Tu reserva #'.$b->id.' ha sido entregada. ¡Buen viaje!';
        $evt   = defined('NotificationService::EVENT_BOOKING_DELIVERED')
                 ? NotificationService::EVENT_BOOKING_DELIVERED
                 : 'booking_delivered';
    } else {
        if ($current !== 3) {
            ApiResponse::err('conflict', 'La reserva no está entregada', 409);
        }
        $b->status = 4;
        $b->update_status();
        if ($b->car_id) {
            $car = CarsData::getById(intval($b->car_id));
            if ($car && $car->id) { $car->status = 0; $car->update_status(); }
        }
        $title = 'Vehículo devuelto';
        $msg   = 'Hemos recibido el vehículo de tu reserva #'.$b->id.'. Gracias.';
        $evt   = defined('NotificationService::EVENT_BOOKING_RETURNED')
                 ? NotificationService::EVENT_BOOKING_RETURNED
                 : 'booking_returned';
    }

    if (class_exists('NotificationService') && intval($b->person_id) > 0) {
        NotificationService::notify('client', intval($b->person_id), $evt, $title, $msg,
            ['booking_id' => intval($b->id), 'stock_id' => intval($b->stock_id)]);
    }
    ApiResponse::ok(['booking' => ApiHelpers::bookingToArray(BookingData::getById($id))]);
}

if ($method === 'POST' && $id > 0 && $sub === 'cancel') {
    $b = BookingData::getById($id);
    if (!_booking_can_view($auth, $b)) ApiResponse::err('not_found', 'Reserva no encontrada', 404);
    if (intval($b->status) === 2) ApiResponse::err('conflict', 'Reserva ya cancelada', 409);
    $b->status = 2;
    $b->update_status();
    // Free the vehicle if it isn't held by another active booking
    $car_id = intval($b->car_id);
    if ($car_id > 0) {
        $con = Database::getCon();
        $other = @$con->query("SELECT id FROM booking
                               WHERE car_id=$car_id AND id<>".intval($b->id)."
                               AND status IN (0,1,3) LIMIT 1");
        if (!$other || $other->num_rows === 0) {
            $car = CarsData::getById($car_id);
            if ($car && $car->id) { $car->status = 0; $car->update_status(); }
        }
    }
    // Notify counterpart
    if (class_exists('NotificationService')) {
        if ($auth['type'] === 'client') {
            NotificationService::notifyStockUsers(intval($b->stock_id), NotificationService::EVENT_BOOKING_CANCELED,
                'Reserva cancelada por cliente', 'Reserva #'.$b->id.' fue cancelada por el cliente.',
                ['booking_id' => intval($b->id), 'stock_id' => intval($b->stock_id)]);
        } else {
            if (intval($b->person_id) > 0) {
                NotificationService::notify('client', intval($b->person_id), NotificationService::EVENT_BOOKING_CANCELED,
                    'Tu reserva fue cancelada', 'La reserva #'.$b->id.' fue cancelada.',
                    ['booking_id' => intval($b->id), 'stock_id' => intval($b->stock_id)]);
            }
        }
    }
    ApiResponse::ok(['booking' => ApiHelpers::bookingToArray(BookingData::getById($id))]);
}

if ($method === 'POST' && $id === 0) {
    $body = ApiResponse::input();

    $car_id   = intval($body['car_id'] ?? 0);
    $start_at = trim((string)($body['start_at'] ?? ''));
    $end_at   = trim((string)($body['end_at'] ?? ''));
    if ($car_id <= 0 || $start_at === '' || $end_at === '') {
        ApiResponse::err('invalid_request', 'car_id, start_at y end_at son requeridos', 400);
    }
    $tsStart = strtotime($start_at);
    $tsEnd   = strtotime($end_at);
    if ($tsStart === false || $tsEnd === false) {
        ApiResponse::err('invalid_request', 'start_at/end_at no son fechas válidas', 400);
    }
    if ($tsEnd <= $tsStart) {
        ApiResponse::err('invalid_request', 'end_at debe ser posterior a start_at', 400);
    }
    $car = CarsData::getById($car_id);
    if (!$car || !$car->id) ApiResponse::err('not_found', 'Vehículo no encontrado', 404);

    // Determine person/stock
    if ($auth['type'] === 'client') {
        $person_id = intval($auth['id']);
        $stock_id  = intval($car->stock_id);
        $user_id   = 0;
    } else {
        $person_id = intval($body['person_id'] ?? 0);
        if ($person_id <= 0) ApiResponse::err('invalid_request', 'person_id requerido', 400);
        $authStock = intval($auth['stock_id'] ?? 0);
        if (array_key_exists('stock_id', $body) && $body['stock_id'] !== '' && $body['stock_id'] !== null) {
            $stock_id = intval($body['stock_id']);
        } elseif ($authStock > 0) {
            $stock_id = $authStock;
        } else {
            // Admin (auth.stock_id=0) with no explicit stock_id → inherit
            // from the chosen vehicle so the booking is correctly scoped.
            $stock_id = intval($car->stock_id);
        }
        // Stock-scoped staff: enforce that both the booking stock and the
        // chosen vehicle belong to the staff member's stock.
        if ($authStock > 0) {
            if ($stock_id !== $authStock) {
                ApiResponse::err('forbidden', 'No puedes crear reservas fuera de tu sucursal', 403);
            }
            if (intval($car->stock_id) !== $authStock) {
                ApiResponse::err('forbidden', 'El vehículo pertenece a otra sucursal', 403);
            }
        }
        $user_id   = intval($auth['id']);
    }

    // Overlap check
    $con = Database::getCon();
    $sa = $con->real_escape_string($start_at);
    $ea = $con->real_escape_string($end_at);
    $r = @$con->query("SELECT id FROM booking
                       WHERE car_id=$car_id AND status IN (0,1,3)
                       AND NOT (end_at < '$sa' OR start_at > '$ea') LIMIT 1");
    if ($r && $r->fetch_assoc()) {
        ApiResponse::err('conflict', 'El vehículo no está disponible en ese rango', 409);
    }

    $days = max(1, intval(ceil(($tsEnd - $tsStart) / 86400)));
    $price = isset($body['price']) ? floatval($body['price']) : floatval($car->price);
    $total = isset($body['total']) ? floatval($body['total']) : $price * $days;

    $b = new BookingData();
    $b->person_id   = $person_id;
    $b->car_id      = $car_id;
    $b->stock_id    = $stock_id;
    $b->user_id     = $user_id;
    $b->start_at    = $start_at;
    $b->end_at      = $end_at;
    $b->place_start = (string)($body['place_start'] ?? 'No especificado');
    $b->place_end   = (string)($body['place_end']   ?? 'No especificado');
    $b->price       = $price;
    $b->total       = $total;
    $b->xtotal      = $total;
    $b->day         = (string)$days;
    $b->comment     = (string)($body['comment'] ?? '');
    $b->type        = '1';
    $b->status      = 0; // pending
    $b->fuel        = (string)($body['fuel'] ?? '');
    $b->deposit     = floatval($body['deposit'] ?? 0);
    $b->sure        = floatval($body['sure'] ?? 0);
    $b->payment     = '0';
    $b->card        = '0';

    $res = $b->add();
    $newId = (is_array($res) && isset($res[1])) ? intval($res[1]) : 0;
    if ($newId <= 0) ApiResponse::err('server_error', 'No se pudo crear la reserva', 500);
    $b->id = $newId;

    // Mark car as reserved
    $car->status = 1;
    $car->update_status();

    // Notifications
    if (class_exists('NotificationService')) {
        $personObj = PersonData::getById($person_id);
        $pname = $personObj ? ($personObj->name ?? '') : '';
        NotificationService::notifyStockUsers($stock_id, NotificationService::EVENT_BOOKING_CREATED,
            'Nueva reserva desde la app',
            'Cliente: '.htmlspecialchars($pname).' — Reserva #'.$newId,
            ['booking_id' => $newId, 'url' => './?view=booking&opt=modal&id='.$newId, 'stock_id' => $stock_id]);
        NotificationService::notify('client', $person_id, NotificationService::EVENT_BOOKING_CREATED,
            'Tu reserva fue creada',
            'Hemos registrado tu reserva #'.$newId.'. Te contactaremos pronto.',
            ['booking_id' => $newId, 'stock_id' => $stock_id]);
    }

    ApiResponse::ok(['booking' => ApiHelpers::bookingToArray(BookingData::getById($newId))], 201);
}

ApiResponse::err('not_found', 'Endpoint de reservas no encontrado', 404);
