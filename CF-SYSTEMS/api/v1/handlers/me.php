<?php
/**
 * GET   /me        — return the authenticated principal's profile.
 * PATCH /me        — update basic profile fields (name, lastname, phone,
 *                    email, address, language). Returns the updated profile.
 *                    Also accepts POST for clients without PATCH support.
 */
$auth = ApiAuth::require();

if ($method === 'GET') {
    if ($auth['type'] === 'user') {
        $u = UserData::getById($auth['id']);
        if (!$u) ApiResponse::err('not_found', 'Usuario no encontrado', 404);
        ApiResponse::ok(['role' => 'staff', 'user' => ApiHelpers::userToArray($u)]);
    } else {
        $p = PersonData::getById($auth['id']);
        if (!$p) ApiResponse::err('not_found', 'Cliente no encontrado', 404);
        ApiResponse::ok(['role' => 'client', 'user' => ApiHelpers::personToArray($p)]);
    }
}

if ($method === 'PATCH' || $method === 'POST') {
    $body = ApiResponse::input();
    $con  = Database::getCon();

    if ($auth['type'] === 'user') {
        $u = UserData::getById($auth['id']);
        if (!$u) ApiResponse::err('not_found', 'Usuario no encontrado', 404);
        foreach (['name','lastname','phone','email','language'] as $f) {
            if (array_key_exists($f, $body)) $u->{$f} = (string)$body[$f];
        }
        // Use profile() for safe partial update (only name/lastname/image),
        // then a targeted SQL for the contact fields.
        $u->profile();
        $id    = intval($u->id);
        $phone = $con->real_escape_string((string)$u->phone);
        $email = $con->real_escape_string((string)$u->email);
        $lang  = $con->real_escape_string((string)($u->language ?? ''));
        @$con->query("UPDATE user SET phone='$phone', email='$email', language='$lang' WHERE id=$id");
        ApiResponse::ok(['role' => 'staff', 'user' => ApiHelpers::userToArray(UserData::getById($id))]);
    }

    // Client
    $p = PersonData::getById($auth['id']);
    if (!$p) ApiResponse::err('not_found', 'Cliente no encontrado', 404);
    $allowed = ['name','lastname','phone','phone2','email','address','address2',
                'nationality','passport','license','language'];
    $sets = [];
    foreach ($allowed as $f) {
        if (array_key_exists($f, $body)) {
            $v = $con->real_escape_string((string)$body[$f]);
            $sets[] = "`$f`='$v'";
        }
    }
    if (!$sets) ApiResponse::err('invalid_request', 'No hay campos para actualizar', 400);
    $pid = intval($p->id);
    @$con->query("UPDATE person SET ".implode(',', $sets)." WHERE id=$pid");
    ApiResponse::ok(['role' => 'client', 'user' => ApiHelpers::personToArray(PersonData::getById($pid))]);
}

ApiResponse::err('method_not_allowed', 'Use GET o PATCH', 405);
