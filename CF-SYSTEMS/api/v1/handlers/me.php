<?php
/**
 * GET /me  — return the authenticated principal's profile.
 */
$auth = ApiAuth::require();

if ($auth['type'] === 'user') {
    $u = UserData::getById($auth['id']);
    if (!$u) ApiResponse::err('not_found', 'Usuario no encontrado', 404);
    ApiResponse::ok(['role' => 'staff', 'user' => ApiHelpers::userToArray($u)]);
} else {
    $p = PersonData::getById($auth['id']);
    if (!$p) ApiResponse::err('not_found', 'Cliente no encontrado', 404);
    ApiResponse::ok(['role' => 'client', 'user' => ApiHelpers::personToArray($p)]);
}
