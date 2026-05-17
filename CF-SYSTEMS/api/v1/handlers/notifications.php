<?php
/**
 * GET  /notifications                  ?filter=all|unread|read&page=&per_page=
 * GET  /notifications/unread_count
 * POST /notifications/{id}/read
 * POST /notifications/read_all
 * GET  /notifications/preferences
 * PUT  /notifications/preferences      { event_type, channel, enabled }
 */

$auth = ApiAuth::require();
$first = strtolower($segments[1] ?? '');

if ($method === 'GET' && ($first === '' || ctype_digit($first))) {
    $page    = max(1, intval($_GET['page'] ?? 1));
    $perPage = max(1, min(100, intval($_GET['per_page'] ?? 20)));
    $filter  = (string)($_GET['filter'] ?? 'all');
    $eventType = (string)($_GET['event_type'] ?? '');
    $dateFrom  = (string)($_GET['from'] ?? '');
    $dateTo    = (string)($_GET['to'] ?? '');

    $res = NotificationData::getFiltered($auth['type'], $auth['id'], $filter, $eventType, $dateFrom, $dateTo, $page, $perPage);
    $rows = [];
    foreach ($res['rows'] as $n) {
        $rows[] = [
            'id'        => intval($n->id),
            'type'      => (string)$n->type,
            'title'     => (string)$n->title,
            'body'      => (string)$n->body,
            'url'       => (string)$n->url,
            'data'      => $n->data_json ? json_decode($n->data_json, true) : null,
            'read_at'   => $n->read_at,
            'created_at'=> (string)$n->created_at,
        ];
    }
    ApiResponse::ok([
        'notifications' => $rows,
        'total'         => $res['total'],
        'page'          => $res['page'],
        'per_page'      => $res['perPage'],
    ]);
}

if ($method === 'GET' && $first === 'unread_count') {
    ApiResponse::ok(['unread' => NotificationData::countUnread($auth['type'], $auth['id'])]);
}

if ($method === 'POST' && $first === 'read_all') {
    NotificationData::markAllRead($auth['type'], $auth['id']);
    ApiResponse::ok(['marked' => true]);
}

if ($method === 'POST' && ctype_digit($first) && strtolower($segments[2] ?? '') === 'read') {
    NotificationData::markRead(intval($first), $auth['type'], $auth['id']);
    ApiResponse::ok(['marked' => true]);
}

if ($first === 'preferences') {
    if ($method === 'GET') {
        ApiResponse::ok(['preferences' => NotificationPreferenceData::getAllFor($auth['type'], $auth['id'])]);
    }
    if ($method === 'PUT' || $method === 'POST') {
        $body = ApiResponse::input();
        $event = trim((string)($body['event_type'] ?? ''));
        $channel = trim((string)($body['channel'] ?? ''));
        $enabled = !empty($body['enabled']) ? 1 : 0;
        if ($event === '' || $channel === '') {
            ApiResponse::err('invalid_request', 'event_type y channel son requeridos', 400);
        }
        NotificationPreferenceData::setPreference($auth['type'], $auth['id'], $event, $channel, $enabled);
        ApiResponse::ok(['updated' => true]);
    }
}

ApiResponse::err('not_found', 'Endpoint de notificaciones no encontrado', 404);
