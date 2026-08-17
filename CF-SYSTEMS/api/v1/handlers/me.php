<?php
/**
 * GET    /me        — return the authenticated principal's profile.
 * PATCH  /me        — update basic profile fields (name, lastname, phone,
 *                     email, address, language). Returns the updated profile.
 *                     Also accepts POST for clients without PATCH support.
 * DELETE /me        — permanently delete the authenticated client account
 *                     (anonymises PII and logs out). Staff accounts cannot
 *                     self-delete via this endpoint.
 */
$auth   = ApiAuth::require();
$action = strtolower($segments[1] ?? '');

// POST /me/document  { kind: 'cedula'|'passport'|'license'|'home', file: base64 }
if ($action === 'document' && $method === 'POST') {
    if ($auth['type'] !== 'client') ApiResponse::err('forbidden', 'Solo clientes', 403);

    $body = ApiResponse::input();
    $kind = strtolower(trim((string)($body['kind'] ?? '')));
    $file = (string)($body['file'] ?? '');

    $kindMap = [
        'cedula'    => 'invoice_file',
        'invoice'   => 'invoice_file',
        'passport'  => 'passport_file',
        'pasaporte' => 'passport_file',
        'license'   => 'license_file',
        'licencia'  => 'license_file',
        'home'      => 'home_file',
        'domicilio' => 'home_file',
    ];
    if (!isset($kindMap[$kind])) ApiResponse::err('invalid_request', "kind inválido (usa: cedula, passport, license, home)", 400);
    if ($file === '')           ApiResponse::err('invalid_request', 'file requerido (base64)', 400);

    // Strict MIME → extension allowlist. Only images and PDF are accepted; any
    // other declared MIME (including spoofed `image/php`) is rejected.
    $allowed = ['image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'application/pdf' => 'pdf'];
    $ext = null;
    if (preg_match('#^data:([a-z0-9.+/-]+);base64,#i', $file, $m)) {
        $mime = strtolower($m[1]);
        if (!isset($allowed[$mime])) {
            ApiResponse::err('invalid_request', 'Tipo de archivo no permitido (usa JPG, PNG, WEBP o PDF)', 400);
        }
        $ext  = $allowed[$mime];
        $file = preg_replace('#^data:[a-z0-9.+/-]+;base64,#i', '', $file);
    } else {
        // No data-url prefix → assume PNG (signature-canvas raw payload).
        $ext = 'png';
    }
    $file = str_replace(' ', '+', $file);
    $bin  = base64_decode($file, true);
    if ($bin === false || strlen($bin) < 100) ApiResponse::err('invalid_request', 'Archivo inválido', 400);
    if (strlen($bin) > 8 * 1024 * 1024) ApiResponse::err('invalid_request', 'Archivo demasiado grande (máx 8 MB)', 400);

    // Cross-check magic bytes vs declared extension to block disguised payloads.
    $head = substr($bin, 0, 8);
    $isPng = strncmp($head, "\x89PNG\r\n\x1a\n", 8) === 0;
    $isJpg = strncmp($head, "\xFF\xD8\xFF", 3) === 0;
    $isPdf = strncmp($head, "%PDF-", 5) === 0;
    $isWebp = substr($bin, 0, 4) === 'RIFF' && (string) substr($bin, 8, 4) === 'WEBP';
    $sniff = $isPng ? 'png' : ($isJpg ? 'jpg' : ($isPdf ? 'pdf' : ($isWebp ? 'webp' : null)));
    if ($sniff === null) ApiResponse::err('invalid_request', 'Archivo inválido', 400);
    $ext = $sniff;

    $col = $kindMap[$kind];
    $dir = ROOT . '/CF-SYSTEMS/storage/profiles';
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    $filename = 'p' . intval($auth['id']) . '_' . $kind . '_' . time() . '.' . $ext;
    $abs      = $dir . '/' . $filename;
    if (file_put_contents($abs, $bin) === false) {
        ApiResponse::err('server_error', 'No se pudo guardar el archivo', 500);
    }

    $relative = 'storage/profiles/' . $filename;
    $con      = Database::getCon();
    $pid      = intval($auth['id']);
    $val      = $con->real_escape_string($relative);
    @$con->query("UPDATE person SET `$col`='$val' WHERE id=$pid");

    ApiResponse::ok([
        'kind'  => $kind,
        'field' => $col,
        'url'   => ApiHelpers::normalizeUrl($relative),
        'user'  => ApiHelpers::personToArray(PersonData::getById($pid)),
    ]);
}

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

// DELETE /me — permanently removes (anonymises) the authenticated client account.
// Staff accounts cannot self-delete; they must be removed by an administrator.
if ($method === 'DELETE') {
    if ($auth['type'] !== 'client') {
        ApiResponse::err('forbidden', 'Las cuentas de staff no se pueden eliminar por esta vía', 403);
    }

    $con = Database::getCon();
    $pid = intval($auth['id']);

    // Verify the account still exists.
    $p = PersonData::getById($pid);
    if (!$p) ApiResponse::err('not_found', 'Cuenta no encontrada', 404);

    // Protect the Apple App Store review demo account from deletion so the
    // reviewer-provided credentials keep working across submissions.
    $uname = strtolower(trim((string)($p->username ?? '')));
    $pphone = strtolower(trim((string)($p->phone ?? '')));
    if ($uname === 'appdemo' || $pphone === 'appdemo') {
        ApiResponse::err('forbidden', 'Esta cuenta de demostración no se puede eliminar', 403);
    }

    // Anonymise all personally-identifiable fields so no user data is retained,
    // but referential integrity with historical bookings is preserved.
    $stamp   = date('YmdHis');
    $anonName  = $con->real_escape_string("Cuenta Eliminada");
    $anonPhone = $con->real_escape_string("deleted_{$pid}_{$stamp}");
    $anonEmail = $con->real_escape_string("deleted_{$pid}_{$stamp}@deleted.invalid");

    $con->query(
        "UPDATE person SET
            name          = '$anonName',
            lastname      = '',
            phone         = '$anonPhone',
            phone2        = '',
            email         = '$anonEmail',
            address       = '',
            address2      = '',
            nationality   = '',
            passport      = '',
            license       = '',
            invoice_file  = '',
            passport_file = '',
            license_file  = '',
            home_file     = '',
            active        = 0
        WHERE id = $pid"
    );

    // Invalidate all refresh tokens for this client so existing sessions stop working.
    @$con->query("DELETE FROM refresh_tokens WHERE person_id = $pid");

    ApiResponse::ok(['deleted' => true]);
}

ApiResponse::err('method_not_allowed', 'Use GET o PATCH', 405);
