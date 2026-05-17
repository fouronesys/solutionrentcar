<?php
/**
 * GET /payments                     list (client: own; staff: stock-scoped)
 * GET /payments?booking_id=         list for a booking
 */

$auth = ApiAuth::require();
if ($method !== 'GET') ApiResponse::err('method_not_allowed', 'Use GET', 405);

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
