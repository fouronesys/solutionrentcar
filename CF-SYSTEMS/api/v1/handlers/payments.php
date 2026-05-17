<?php
/**
 * GET  /payments                    list (client: own; staff: stock-scoped)
 * GET  /payments?booking_id=ID      list payments for a booking
 * POST /payments                    staff-only: register a payment for a booking
 *      body: { booking_id, val, payment_type_id?=1 }
 */

$auth = ApiAuth::require();

if ($method === 'POST') {
    if ($auth['type'] !== 'user') ApiResponse::err('forbidden', 'Solo staff puede registrar pagos', 403);
    $body = ApiResponse::input();

    $booking_id      = intval($body['booking_id'] ?? 0);
    $val             = floatval($body['val'] ?? 0);
    $payment_type_id = intval($body['payment_type_id'] ?? 1);
    if ($booking_id <= 0 || $val <= 0) {
        ApiResponse::err('invalid_request', 'booking_id y val (>0) son requeridos', 400);
    }

    $b = BookingData::getById($booking_id);
    if (!$b || !$b->id) ApiResponse::err('not_found', 'Reserva no encontrada', 404);
    if (intval($auth['stock_id']) > 0 && intval($b->stock_id) !== intval($auth['stock_id'])) {
        ApiResponse::err('forbidden', 'Reserva fuera de tu sucursal', 403);
    }

    // PaymentData::add() / add_payment() hardcode payment_type_id, so we
    // INSERT directly to honor the caller's value end-to-end.
    $con = Database::getCon();
    $uid = intval($auth['id']);
    $sid = intval($b->stock_id);
    $pid = intval($b->person_id);
    $bid = intval($b->id);
    $valEsc = floatval($val);
    $ptid   = intval($payment_type_id);
    if ($ptid <= 0) $ptid = 1;
    $ok = @$con->query("INSERT INTO payment
            (user_id, stock_id, person_id, booking_id, val, is_stock, payment_type_id, created_at)
          VALUES ($uid, $sid, $pid, $bid, $valEsc, 0, $ptid, NOW())");
    if (!$ok) ApiResponse::err('server_error', 'No se pudo registrar el pago', 500);
    $newId = intval($con->insert_id);

    // Update booking running totals
    @$con->query("UPDATE booking
                  SET payment = COALESCE(payment,0) + $valEsc
                  WHERE id=$bid");

    if (class_exists('NotificationService') && intval($b->person_id) > 0) {
        $evt = defined('NotificationService::EVENT_PAYMENT_RECEIVED')
               ? NotificationService::EVENT_PAYMENT_RECEIVED
               : 'payment_received';
        NotificationService::notify('client', intval($b->person_id), $evt,
            'Pago registrado',
            'Recibimos un pago de '.number_format($val, 2).' para tu reserva #'.$bid.'.',
            ['booking_id' => $bid, 'payment_id' => $newId, 'val' => $val]);
    }

    ApiResponse::ok([
        'payment' => [
            'id'              => $newId,
            'booking_id'      => $bid,
            'person_id'       => $pid,
            'stock_id'        => $sid,
            'val'             => $valEsc,
            'payment_type_id' => $ptid,
        ],
    ], 201);
}

if ($method !== 'GET') ApiResponse::err('method_not_allowed', 'Use GET o POST', 405);

$con = Database::getCon();
$where = ["is_stock=0"];

if ($auth['type'] === 'client') {
    $where[] = "person_id=" . intval($auth['id']);
} else {
    $sid = intval($auth['stock_id']);
    if ($sid > 0) $where[] = "stock_id=$sid";
}
if (!empty($_GET['booking_id'])) $where[] = "booking_id=" . intval($_GET['booking_id']);

$limit  = max(1, min(200, intval($_GET['limit'] ?? 50)));
$offset = max(0, intval($_GET['offset'] ?? 0));
$sqlExtra = "where " . implode(' AND ', $where) . " ORDER BY created_at DESC LIMIT $offset,$limit";

$rows = PaymentData::getAllBySQL($sqlExtra);
$out = [];
foreach ($rows as $p) {
    $out[] = [
        'id'              => intval($p->id),
        'booking_id'      => intval($p->booking_id ?? 0),
        'person_id'       => intval($p->person_id ?? 0),
        'val'             => floatval($p->val ?? 0),
        'payment_type_id' => intval($p->payment_type_id ?? 0),
        'stock_id'        => intval($p->stock_id ?? 0),
        'created_at'      => (string)($p->created_at ?? ''),
    ];
}

ApiResponse::ok([
    'payments' => $out,
    'limit'    => $limit,
    'offset'   => $offset,
    'count'    => count($out),
]);
