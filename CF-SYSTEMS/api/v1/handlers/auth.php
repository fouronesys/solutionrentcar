<?php
/**
 * POST /auth/login    { username, password, role?: 'staff'|'client' }
 * POST /auth/refresh  { refresh_token }
 * POST /auth/logout   (Bearer)
 */

$action = strtolower($segments[1] ?? '');

if ($method !== 'POST') {
    ApiResponse::err('method_not_allowed', 'Use POST', 405);
}

if ($action === 'login') {
    $body = ApiResponse::input();
    $username = trim((string)($body['username'] ?? ''));
    $password = trim((string)($body['password'] ?? ''));
    $role     = strtolower(trim((string)($body['role'] ?? '')));
    if ($username === '' || $password === '') {
        ApiResponse::err('invalid_credentials', 'Usuario o contraseña vacíos', 400);
    }

    $con = Database::getCon();
    $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL) !== false;

    // ---- STAFF (user table) ----
    $tryStaff = ($role === '' || $role === 'staff' || $role === 'user');
    if ($tryStaff) {
        $hashed = sha1(md5($password));
        if ($isEmail) {
            $u  = $con->real_escape_string(strtolower($username));
            $h  = $con->real_escape_string($hashed);
            $sql = "SELECT * FROM user WHERE LOWER(email)='$u' AND password='$h' AND status=1 LIMIT 1";
        } else {
            $u  = $con->real_escape_string($username);
            $h  = $con->real_escape_string($hashed);
            $sql = "SELECT * FROM user WHERE (username='$u' OR email='$u') AND password='$h' AND status=1 LIMIT 1";
        }
        $r = @$con->query($sql);
        if ($r && $row = $r->fetch_assoc()) {
            $user = (object)$row;
            $tokens = ApiAuth::issueTokens('user', intval($user->id), [
                'stock_id' => intval($user->stock_id ?? 0),
                'kind'     => intval($user->kind ?? 0),
            ]);
            ApiResponse::ok([
                'role'    => 'staff',
                'user'    => ApiHelpers::userToArray($user),
                'tokens'  => $tokens,
            ]);
        }
        if ($role === 'staff' || $role === 'user') {
            ApiResponse::err('invalid_credentials', 'Credenciales inválidas', 401);
        }
    }

    // ---- CLIENT (person table) ----
    if ($role === '' || $role === 'client') {
        $variants = ApiHelpers::phoneVariants($username);
        // also allow the username/password fields stored explicitly
        $variants[] = $username;
        $variants = array_values(array_unique($variants));

        $passVariants = ApiHelpers::phoneVariants($password);
        $passVariants[] = $password;
        $passVariants = array_values(array_unique($passVariants));

        $inUser = implode(',', array_map(fn($v) => "'".$con->real_escape_string($v)."'", $variants));
        $inPass = implode(',', array_map(fn($v) => "'".$con->real_escape_string($v)."'", $passVariants));

        // Match either explicit username/password column or by phone variants
        $sql = "SELECT * FROM person
                WHERE (
                    username IN ($inUser)
                    OR REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''),' ',''),'(',''),')',''),'+','') IN ($inUser)
                )
                AND (
                    password IN ($inPass)
                    OR REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''),' ',''),'(',''),')',''),'+','') IN ($inPass)
                )
                LIMIT 1";
        $r = @$con->query($sql);
        if ($r && $row = $r->fetch_assoc()) {
            $client = (object)$row;
            $tokens = ApiAuth::issueTokens('client', intval($client->id), [
                'stock_id' => intval($client->stock_id ?? 0),
            ]);
            ApiResponse::ok([
                'role'    => 'client',
                'user'    => ApiHelpers::personToArray($client),
                'tokens'  => $tokens,
            ]);
        }
    }

    ApiResponse::err('invalid_credentials', 'Credenciales inválidas', 401);
}

if ($action === 'register') {
    $body     = ApiResponse::input();
    $name     = trim((string)($body['name'] ?? ''));
    $lastname = trim((string)($body['lastname'] ?? ''));
    $phone    = trim((string)($body['phone'] ?? ''));
    $email    = trim((string)($body['email'] ?? ''));
    $password = trim((string)($body['password'] ?? ''));
    $passport = trim((string)($body['passport'] ?? ''));
    $license  = trim((string)($body['license'] ?? ''));

    if ($name === '' || $phone === '' || $password === '') {
        ApiResponse::err('invalid_request', 'Nombre, teléfono y contraseña son requeridos', 400);
    }
    if (strlen($password) < 6) {
        ApiResponse::err('invalid_request', 'La contraseña debe tener al menos 6 caracteres', 400);
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        ApiResponse::err('invalid_request', 'Email inválido', 400);
    }

    $con = Database::getCon();

    // Check phone uniqueness (across username + phone variants)
    $variants = ApiHelpers::phoneVariants($phone);
    $variants[] = $phone;
    $variants = array_values(array_unique(array_filter($variants)));
    if (!empty($variants)) {
        $inUser = implode(',', array_map(fn($v) => "'".$con->real_escape_string($v)."'", $variants));
        $sql = "SELECT id FROM person WHERE
                username IN ($inUser)
                OR REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''),' ',''),'(',''),')',''),'+','') IN ($inUser)
                LIMIT 1";
        $r = @$con->query($sql);
        if ($r && $r->fetch_assoc()) {
            ApiResponse::err('conflict', 'Ya existe una cuenta con ese teléfono', 409);
        }
    }
    if ($email !== '') {
        $eSafe = $con->real_escape_string($email);
        $r = @$con->query("SELECT id FROM person WHERE email='$eSafe' LIMIT 1");
        if ($r && $r->fetch_assoc()) {
            ApiResponse::err('conflict', 'Ya existe una cuenta con ese email', 409);
        }
    }

    // Build full name when lastname is provided (table has no lastname column)
    $fullName = $lastname !== '' ? trim($name . ' ' . $lastname) : $name;

    // Insert directly (consistent with login flow which uses raw SQL).
    $esc = fn($v) => $con->real_escape_string((string)$v);
    $sql = "INSERT INTO person
        (reference,no,rnc,passport,issuedlicense,invoice_file,passport_file,license_file,home_file,
         invoice_date,passport_date,license_date,home_date,
         name,address,address2,email,phone,phone2,license,license2,expirelicense,nationality,
         username,password,user_id,is_rental,stock_id,location,latitud,longitud,language,created_at,estado)
        VALUES
        ('','','','".$esc($passport)."','','','','','',
         '0000-00-00','0000-00-00','0000-00-00','0000-00-00',
         '".$esc($fullName)."','','','".$esc($email)."','".$esc($phone)."','','".$esc($license)."','','','',
         '".$esc($phone)."','".$esc($password)."',0,1,0,2,'','','ES',NOW(),'Soltero')";
    if (!$con->query($sql)) {
        // Avoid leaking DB internals in production responses.
        error_log('[auth/register] insert failed: '.$con->error);
        ApiResponse::err('server_error', 'No se pudo crear la cuenta', 500);
    }
    $newId = intval($con->insert_id);
    if ($newId <= 0) ApiResponse::err('server_error', 'No se pudo crear la cuenta', 500);

    // Re-read the inserted row to return the full profile shape personToArray expects.
    $r = $con->query("SELECT * FROM person WHERE id=$newId LIMIT 1");
    $row = $r ? $r->fetch_assoc() : null;
    $client = (object)($row ?: ['id' => $newId, 'name' => $fullName, 'phone' => $phone, 'email' => $email]);

    $tokens = ApiAuth::issueTokens('client', $newId, ['stock_id' => 0]);
    ApiResponse::ok([
        'role'   => 'client',
        'user'   => ApiHelpers::personToArray($client),
        'tokens' => $tokens,
    ], 201);
}

if ($action === 'refresh') {
    $body = ApiResponse::input();
    $rt = trim((string)($body['refresh_token'] ?? ''));
    if ($rt === '') ApiResponse::err('invalid_request', 'refresh_token requerido', 400);
    $res = ApiAuth::consumeRefresh($rt);
    if (!$res) ApiResponse::err('invalid_grant', 'Refresh token inválido o expirado', 401);
    $extra = [];
    if ($res['type'] === 'user') {
        $u = UserData::getById($res['id']);
        if ($u) $extra['stock_id'] = intval($u->stock_id);
    } else {
        $p = PersonData::getById($res['id']);
        if ($p) $extra['stock_id'] = intval($p->stock_id);
    }
    $tokens = ApiAuth::issueTokens($res['type'], $res['id'], $extra);
    ApiResponse::ok(['tokens' => $tokens]);
}

if ($action === 'logout') {
    $auth = ApiAuth::require();
    ApiAuth::revokeAllFor($auth['type'], $auth['id']);
    ApiResponse::ok(['logged_out' => true]);
}

ApiResponse::err('not_found', "Auth action '$action' desconocida", 404);
