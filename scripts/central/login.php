<?php
session_start();
header('Content-Type: application/json');
require __DIR__ . '/rtcommon.php';

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
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
            "SELECT * FROM user WHERE email = :username AND password = :password LIMIT 1"
        );
        $stmt->execute(['username' => $username, 'password' => $password]);
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

/* 1) Camino rápido: el índice sabe en qué instalación vive el email (1-2 conexiones) */
foreach (rt_index_lookup($idx, $username) as $folder) {
    if (!isset($configs[$folder])) continue;
    $r = rt_try_login($configs[$folder], $username, $password);
    if (isset($r['row']) && $r['row']) {
        if ($idx_dirty) rt_index_save($base_path, $idx);
        rt_login_success($r['row'], $folder);
    }
    if (isset($r['error']) && $r['error'] === 'QUOTA') { $quota_hit = true; break; }
}

/* 2) Email no indexado (o cambió de instalación): escanear carpetas pendientes,
      dentro del presupuesto de conexiones del request, alimentando el índice. */
if (!$quota_hit) {
    foreach ($configs as $folder => $cfg) {
        if (isset($idx['folders'][$folder])) continue;      // ya escaneada
        if (rt_quota_left() <= 1) break;                     // reservar margen
        $s = rt_index_scan_folder($idx, $cfg);
        if (isset($s['error'])) {
            if ($s['error'] === 'QUOTA') { $quota_hit = true; break; }
            continue;                                        // AUTH/OTHER: seguir
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
    }
}

if ($idx_dirty) rt_index_save($base_path, $idx);

$pending = 0;
foreach ($configs as $folder => $cfg) {
    if (!isset($idx['folders'][$folder])) $pending++;
}

if ($quota_hit || ($pending > 0 && rt_index_lookup($idx, $username) === [])) {
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
