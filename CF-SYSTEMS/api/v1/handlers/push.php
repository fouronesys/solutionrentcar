<?php
/**
 * POST   /push/register   { token, platform?, app_version?, device_info? }
 * DELETE /push/token      { token }   (or path /push/token/{token})
 */

$auth = ApiAuth::require();
$action = strtolower($segments[1] ?? '');

if ($action === 'register' && $method === 'POST') {
    $body = ApiResponse::input();
    $token = trim((string)($body['token'] ?? ''));
    if ($token === '') ApiResponse::err('invalid_request', 'token requerido', 400);
    $platform   = trim((string)($body['platform'] ?? 'expo'));
    $appVer     = trim((string)($body['app_version'] ?? ''));
    $deviceInfo = trim((string)($body['device_info'] ?? ''));

    $con = Database::getCon();
    $rt = $con->real_escape_string($auth['type']);
    $rid = intval($auth['id']);
    $tk = $con->real_escape_string($token);
    $pl = $con->real_escape_string($platform);
    $av = $con->real_escape_string($appVer);
    $di = $con->real_escape_string($deviceInfo);

    $sql = "INSERT INTO device_token (recipient_type,recipient_id,token,platform,app_version,device_info,created_at,updated_at)
            VALUES ('$rt',$rid,'$tk','$pl','$av','$di',NOW(),NOW())
            ON DUPLICATE KEY UPDATE recipient_type='$rt', recipient_id=$rid, platform='$pl',
                                    app_version='$av', device_info='$di', updated_at=NOW()";
    @$con->query($sql);
    ApiResponse::ok(['registered' => true]);
}

if ($action === 'token' && $method === 'DELETE') {
    $token = trim((string)($segments[2] ?? ''));
    if ($token === '') {
        $body = ApiResponse::input();
        $token = trim((string)($body['token'] ?? ''));
    }
    if ($token === '') ApiResponse::err('invalid_request', 'token requerido', 400);
    $con = Database::getCon();
    $tk = $con->real_escape_string($token);
    $rid = intval($auth['id']);
    $rt = $con->real_escape_string($auth['type']);
    @$con->query("DELETE FROM device_token WHERE token='$tk' AND recipient_type='$rt' AND recipient_id=$rid");
    ApiResponse::ok(['deleted' => true]);
}

ApiResponse::err('not_found', 'Endpoint de push no encontrado', 404);
