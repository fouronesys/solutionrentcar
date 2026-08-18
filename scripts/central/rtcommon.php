<?php
/*
|--------------------------------------------------------------------------
| RENTCAR central - utilidades compartidas
|--------------------------------------------------------------------------
| El hosting limita ~20 conexiones MySQL exitosas por request PHP
| (la conexión 21+ falla con SQLSTATE 2002 "Operation not permitted").
| Por eso todo acceso central se hace en lotes y con un índice de usuarios.
*/

define('RT_MAX_CONN_PER_REQUEST', 15); // margen bajo el límite (~20)

function rt_folders($base_path) {
    $excluidas = ['CLIENTES', 'CF-SYSTEMS', 'logs', 'PWA'];
    $dirs = array_filter(glob($base_path . '/*'), function ($dir) use ($excluidas) {
        return is_dir($dir) && !in_array(basename($dir), $excluidas);
    });
    sort($dirs);
    return $dirs;
}

function rt_read_config($carpeta) {
    $config_path = $carpeta . '/core/controller/Database.php';
    if (!file_exists($config_path)) return null;
    $contenido = file_get_contents($config_path);
    preg_match('/\$this->host\s*=\s*[\'"](.*?)[\'"]/', $contenido, $host);
    preg_match('/\$this->user\s*=\s*[\'"](.*?)[\'"]/', $contenido, $user);
    preg_match('/\$this->pass\s*=\s*[\'"](.*?)[\'"]/', $contenido, $pass);
    preg_match('/\$this->ddbb\s*=\s*[\'"](.*?)[\'"]/', $contenido, $db);
    $cfg = [
        'folder' => basename($carpeta),
        'host'   => $host[1] ?? '',
        'user'   => $user[1] ?? '',
        'pass'   => $pass[1] ?? '',
        'db'     => $db[1] ?? '',
    ];
    if ($cfg['host'] === '' || $cfg['user'] === '' || $cfg['db'] === '') return null;
    return $cfg;
}

/*
| Conecta a una instalación. Devuelve:
|   ['pdo' => PDO]                    en éxito
|   ['error' => 'QUOTA']              límite de conexiones del request (2002)
|   ['error' => 'AUTH'|'OTHER', 'msg' => ...]
| Cuenta conexiones intentadas en $GLOBALS['rt_conn_count'].
*/
function rt_connect($cfg, $charset = 'utf8mb4') {
    $hosts = ($cfg['host'] === 'localhost' || $cfg['host'] === '127.0.0.1')
        ? ['localhost', '127.0.0.1']
        : [$cfg['host']];
    $last = null;
    foreach ($hosts as $h) {
        $GLOBALS['rt_conn_count'] = ($GLOBALS['rt_conn_count'] ?? 0) + 1;
        try {
            $pdo = new PDO(
                "mysql:host={$h};dbname={$cfg['db']};charset={$charset}",
                $cfg['user'], $cfg['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 4]
            );
            return ['pdo' => $pdo];
        } catch (PDOException $e) {
            $last = $e;
            $msg = $e->getMessage();
            if (strpos($msg, '2002') !== false) {
                // socket/TCP denegado: casi siempre es la cuota por request
                return ['error' => 'QUOTA', 'msg' => $msg];
            }
            if (strpos($msg, '1045') !== false || strpos($msg, '1044') !== false) {
                // probar siguiente host no ayuda con credenciales inválidas
                return ['error' => 'AUTH', 'msg' => $msg];
            }
        }
    }
    return ['error' => 'OTHER', 'msg' => $last ? $last->getMessage() : 'unknown'];
}

function rt_quota_left() {
    return RT_MAX_CONN_PER_REQUEST - ($GLOBALS['rt_conn_count'] ?? 0);
}

/* ---------- índice de usuarios (email → carpetas) ---------- */

function rt_index_path($base_path) {
    $dir = $base_path . '/logs';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $ht = $dir . '/.htaccess';
    if (!file_exists($ht)) @file_put_contents($ht, "Require all denied\nDeny from all\n");
    return $dir . '/central_user_index.json';
}

function rt_index_load($base_path) {
    $p = rt_index_path($base_path);
    if (!file_exists($p)) return ['folders' => []];
    $j = json_decode(file_get_contents($p), true);
    return is_array($j) && isset($j['folders']) ? $j : ['folders' => []];
}

function rt_index_save($base_path, $idx) {
    $idx['updated_at'] = date('c');
    @file_put_contents(rt_index_path($base_path), json_encode($idx), LOCK_EX);
}

/* Escanea una instalación y guarda sus emails en el índice. */
function rt_index_scan_folder(&$idx, $cfg) {
    $c = rt_connect($cfg);
    if (isset($c['error'])) return $c;
    try {
        $emails = [];
        $st = $c['pdo']->query("SELECT email FROM user");
        foreach ($st->fetchAll(PDO::FETCH_COLUMN) as $em) {
            $em = strtolower(trim((string)$em));
            if ($em !== '') $emails[] = $em;
        }
        $idx['folders'][$cfg['folder']] = [
            'scanned_at' => date('c'),
            'users'      => array_values(array_unique($emails)),
        ];
        return ['ok' => true, 'count' => count($emails)];
    } catch (Exception $e) {
        // sin tabla user u otro problema: marcar como escaneada sin usuarios
        $idx['folders'][$cfg['folder']] = [
            'scanned_at' => date('c'),
            'users'      => [],
            'note'       => 'scan_error',
        ];
        return ['ok' => true, 'count' => 0];
    } finally {
        $c['pdo'] = null;
    }
}

function rt_index_lookup($idx, $email) {
    $email = strtolower(trim($email));
    $out = [];
    foreach ($idx['folders'] as $folder => $data) {
        if (in_array($email, $data['users'] ?? [], true)) $out[] = $folder;
    }
    return $out;
}
