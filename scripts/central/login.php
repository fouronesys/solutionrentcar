<?php
session_start();
header('Content-Type: application/json');
require __DIR__ . '/rtcommon.php';

$username = isset($_POST['username']) ? strtolower(trim($_POST['username'])) : '';
$password_raw = isset($_POST['password']) ? $_POST['password'] : '';

if ($username === '' || $password_raw === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor complete todos los campos.'
    ]);
    exit;
}

// Mantener el mismo sistema de contraseña de la aplicación
$password = sha1(md5($password_raw));

$base_path = __DIR__;
$idx = rt_index_load($base_path);
$idx_dirty = false;

function rt_try_login($cfg, $username, $password) {
    $c = rt_connect($cfg, 'utf8');
    if (isset($c['error'])) return $c;
    try {
        $stmt = $c['pdo']->prepare(
            "SELECT id, stock_id
             FROM user
             WHERE (
                 LOWER(TRIM(email)) = :email
                 OR LOWER(TRIM(username)) = :username
             )
             AND password = :password
             AND status = 1
             LIMIT 1"
        );
        $stmt->execute([
            'email' => $username,
            'username' => $username,
            'password' => $password
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['ok' => true, 'row' => $row ?: null];
    } catch (Exception $e) {
        return ['error' => 'OTHER', 'msg' => $e->getMessage()];
    } finally {
        $c['pdo'] = null;
    }
}

function rt_login_success($row, $folder) {
    session_regenerate_id(true); // evitar fijación de sesión
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['stock_id'] = $row['stock_id'];
    $_SESSION['login_type'] = 'user';
    unset($_SESSION['client_id']);
    echo json_encode([
        'success' => true,
        'redirect' => $folder . '/?view=home'
    ]);
    exit;
}

/*
| Login de cliente: person.phone como usuario Y como contraseña (sin cifrar),
| igual que el login de cada instalación. Compara sobre el teléfono
| normalizado (solo dígitos) admitiendo variantes con/sin "1" inicial.
*/
function rt_try_client_login($cfg, $username_raw, $password_raw) {
    $vu = rt_phone_variants($username_raw);
    $vp = rt_phone_variants($password_raw);
    if ($vu === [] || $vp === []) return ['ok' => true, 'row' => null];
    $c = rt_connect($cfg, 'utf8');
    if (isset($c['error'])) return $c;
    try {
        $norm = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(%s, '-', ''), ' ', ''), '(', ''), ')', ''), '+', '')";
        $np  = sprintf($norm, 'phone');
        $np2 = sprintf($norm, 'phone2');
        $ph_u = implode(',', array_fill(0, count($vu), '?'));
        $ph_p = implode(',', array_fill(0, count($vp), '?'));
        $sql = "SELECT id, stock_id FROM person
                WHERE ($np IN ($ph_u) OR $np2 IN ($ph_u))
                  AND ($np IN ($ph_p) OR $np2 IN ($ph_p))
                LIMIT 1";
        $stmt = $c['pdo']->prepare($sql);
        $stmt->execute(array_merge($vu, $vu, $vp, $vp));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['ok' => true, 'row' => $row ?: null];
    } catch (Exception $e) {
        return ['error' => 'OTHER', 'msg' => $e->getMessage()];
    } finally {
        $c['pdo'] = null;
    }
}

function rt_client_login_success($row, $folder) {
    session_regenerate_id(true);
    $_SESSION['client_id']  = (int)$row['id'];
    $_SESSION['stock_id']   = (int)$row['stock_id'];
    $_SESSION['login_type'] = 'client';
    unset($_SESSION['user_id']);
    echo json_encode([
        'success' => true,
        'redirect' => $folder . '/?view=home'
    ]);
    exit;
}

$configs = [];
foreach (rt_folders($base_path) as $carpeta) {
    $cfg = rt_read_config($carpeta);
    if ($cfg !== null) $configs[$cfg['folder']] = $cfg;
}

$quota_hit = false;
$attempted_folders = [];

/* 1) Camino rápido: el índice sabe en qué instalación vive el email (1-2 conexiones) */
foreach (rt_index_lookup($idx, $username) as $folder) {
    if (!isset($configs[$folder])) continue;
    if (rt_quota_left() <= 0) { $quota_hit = true; break; }
    $attempted_folders[$folder] = true;
    $r = rt_try_login($configs[$folder], $username, $password);
    if (isset($r['row']) && $r['row']) {
        if ($idx_dirty) rt_index_save($base_path, $idx);
        rt_login_success($r['row'], $folder);
    }
    if (isset($r['error']) && $r['error'] === 'QUOTA') { $quota_hit = true; break; }
}

/* 1b) Cliente por teléfono: el índice sabe dónde vive el número */
if (!$quota_hit) {
    foreach (rt_index_lookup_phone($idx, $username) as $folder) {
        if (!isset($configs[$folder])) continue;
        if (rt_quota_left() <= 0) { $quota_hit = true; break; }
        $attempted_folders[$folder] = true;
        $r = rt_try_client_login($configs[$folder], $username, $password_raw);
        if (isset($r['row']) && $r['row']) {
            if ($idx_dirty) rt_index_save($base_path, $idx);
            rt_client_login_success($r['row'], $folder);
        }
        if (isset($r['error']) && $r['error'] === 'QUOTA') { $quota_hit = true; break; }
    }
}

/* 2) Email no indexado (o cambió de instalación): escanear carpetas pendientes,
      dentro del presupuesto de conexiones del request, alimentando el índice. */
if (!$quota_hit) {
    foreach ($configs as $folder => $cfg) {
        if (isset($idx['folders'][$folder])) continue;      // ya escaneada
        if (rt_quota_left() <= 1) break;                     // reservar margen
        $attempted_folders[$folder] = true;
        $s = rt_index_scan_folder($idx, $cfg);
        if (isset($s['error'])) {
            if ($s['error'] === 'QUOTA') { $quota_hit = true; break; }
            $idx['folders'][$folder] = [
                'scanned_at' => date('c'),
                'users' => [],
                'phones' => [],
                'note' => $s['error']
            ];
            $idx_dirty = true;
            continue;
        }
        $idx_dirty = true;
        if (in_array(strtolower($username), $idx['folders'][$folder]['users'], true)) {
            $r = rt_try_login($cfg, $username, $password);
            if (isset($r['row']) && $r['row']) {
                rt_index_save($base_path, $idx);
                rt_login_success($r['row'], $folder);
            }
            if (isset($r['error']) && $r['error'] === 'QUOTA') { $quota_hit = true; break; }
        }
        // ¿el teléfono del cliente vive en esta instalación recién escaneada?
        $ph_vars = rt_phone_variants($username);
        if ($ph_vars !== [] && array_intersect($ph_vars, $idx['folders'][$folder]['phones'] ?? []) !== []) {
            if (rt_quota_left() <= 0) { $quota_hit = true; break; }
            $r = rt_try_client_login($cfg, $username, $password_raw);
            if (isset($r['row']) && $r['row']) {
                rt_index_save($base_path, $idx);
                rt_client_login_success($r['row'], $folder);
            }
            if (isset($r['error']) && $r['error'] === 'QUOTA') { $quota_hit = true; break; }
        }
    }
}

/*
| 3) El identificador puede pertenecer a un usuario creado después del último
| escaneo. Refrescar instalaciones ya conocidas, empezando por la más antigua.
| Si no caben todas en este request, el siguiente intento continúa con las
| restantes porque scanned_at se actualiza en cada lote.
*/
$refresh_pending = false;
if (!$quota_hit) {
    $refresh_candidates = [];
    foreach (rt_index_refresh_candidates($idx, $configs) as $cfg) {
        if (!isset($attempted_folders[$cfg['folder']])) {
            $refresh_candidates[] = $cfg;
        }
    }
    $processed = 0;

    foreach ($refresh_candidates as $cfg) {
        if (rt_quota_left() <= 1) {
            $refresh_pending = true;
            break;
        }

        $folder = $cfg['folder'];
        $attempted_folders[$folder] = true;
        $s = rt_index_scan_folder($idx, $cfg);
        if (isset($s['error'])) {
            if ($s['error'] === 'QUOTA') {
                $quota_hit = true;
                $refresh_pending = true;
                break;
            }
            $idx['folders'][$folder]['scanned_at'] = date('c');
            $idx['folders'][$folder]['note'] = $s['error'];
            $idx_dirty = true;
            $processed++;
            continue;
        }

        $idx_dirty = true;
        $processed++;

        if (in_array($username, $idx['folders'][$folder]['users'] ?? [], true)) {
            $r = rt_try_login($cfg, $username, $password);
            if (isset($r['row']) && $r['row']) {
                rt_index_save($base_path, $idx);
                rt_login_success($r['row'], $folder);
            }
            if (isset($r['error']) && $r['error'] === 'QUOTA') {
                $quota_hit = true;
                $refresh_pending = true;
                break;
            }
        }

        $phone_matches = array_intersect(
            rt_phone_variants($username),
            $idx['folders'][$folder]['phones'] ?? []
        );
        if ($phone_matches !== []) {
            if (rt_quota_left() <= 0) {
                $refresh_pending = true;
                break;
            }
            $r = rt_try_client_login($cfg, $username, $password_raw);
            if (isset($r['row']) && $r['row']) {
                rt_index_save($base_path, $idx);
                rt_client_login_success($r['row'], $folder);
            }
            if (isset($r['error']) && $r['error'] === 'QUOTA') {
                $quota_hit = true;
                $refresh_pending = true;
                break;
            }
        }
    }

    if ($processed < count($refresh_candidates)) $refresh_pending = true;
}

if ($idx_dirty) rt_index_save($base_path, $idx);

$pending = 0;
foreach ($configs as $folder => $cfg) {
    if (!isset($idx['folders'][$folder])) $pending++;
}

if (
    $quota_hit
    || $refresh_pending
    || ($pending > 0 && rt_index_lookup($idx, $username) === [] && rt_index_lookup_phone($idx, $username) === [])
) {
    // No se pudo verificar contra todas las instalaciones en este request
    echo json_encode([
        'success' => false,
        'message' => 'No se pudo verificar en este momento. Intente nuevamente en unos segundos.'
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Por favor verifique su nombre de usuario y contraseña.'
]);
exit;
