<?php
/**
 * GET /preferences   — list notification preferences for the authenticated
 *                      principal (one row per event_type × channel, with the
 *                      effective enabled state).
 * PUT /preferences   — update one preference.
 *      body: { event_type, channel, enabled }
 *
 * This is the top-level alias for /notifications/preferences, exposed at
 * /preferences per the API spec.
 */

$auth = ApiAuth::require();
$rt   = ($auth['type'] === 'user') ? 'user' : 'client';
$rid  = intval($auth['id']);

if ($method === 'GET') {
    // Mirror /notifications/preferences exactly so consumers see one shape.
    $prefs = class_exists('NotificationPreferenceData')
        ? NotificationPreferenceData::getAllFor($rt, $rid)
        : new \stdClass();
    ApiResponse::ok(['preferences' => $prefs]);
}

if ($method === 'PUT' || $method === 'POST' || $method === 'PATCH') {
    $body = ApiResponse::input();
    $event   = trim((string)($body['event_type'] ?? ''));
    $channel = trim((string)($body['channel'] ?? ''));
    if ($event === '' || $channel === '') {
        ApiResponse::err('invalid_request', 'event_type y channel son requeridos', 400);
    }
    $enabled = !empty($body['enabled']) ? 1 : 0;

    $con = Database::getCon();
    $rtE = $con->real_escape_string($rt);
    $ev  = $con->real_escape_string($event);
    $ch  = $con->real_escape_string($channel);
    @$con->query("INSERT INTO notification_preference
                    (recipient_type, recipient_id, event_type, channel, enabled, updated_at)
                  VALUES ('$rtE', $rid, '$ev', '$ch', $enabled, NOW())
                  ON DUPLICATE KEY UPDATE enabled=VALUES(enabled), updated_at=NOW()");

    ApiResponse::ok(['preference' => [
        'event_type' => $event,
        'channel'    => $channel,
        'enabled'    => (bool)$enabled,
    ]]);
}

ApiResponse::err('method_not_allowed', 'Use GET o PUT', 405);
