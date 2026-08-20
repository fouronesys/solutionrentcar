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

/*
| Protección de endpoints administrativos. La clave se envía por header
| X-Admin-Key (nunca por query string, para no quedar en access logs).
| En el servidor solo se guarda su hash SHA-256 (logs/central_admin_key.hash),
| inútil si el archivo llegara a ser leído. Falla cerrado: sin archivo de hash
| o sin header válido → 403 y exit.
*/
function rt_require_admin_key($base_path) {
    $hash_file = $base_path . '/logs/central_admin_key.hash';
    $stored = file_exists($hash_file) ? trim((string)file_get_contents($hash_file)) : '';
    $given = isset($_SERVER['HTTP_X_ADMIN_KEY']) ? (string)$_SERVER['HTTP_X_ADMIN_KEY'] : '';
    if ($stored === '' || $given === '' || !hash_equals($stored, hash('sha256', $given))) {
        http_response_code(403);
        echo "Forbidden";
        exit;
    }
}

function rt_folders($base_path) {
    $excluidas = ['CLIENTES', 'CF-SYSTEMS', 'logs', 'PWA'];
    $dirs = array_filter(glob($base_path . '/*'), function ($dir) use ($excluidas) {
        return is_dir($dir) && !in_array(basename($dir), $excluidas);
    });
    sort($dirs);
    return $dirs;
}

/**
 * Extract a PHP string property value from Database.php source, handling
 * both single-quoted and double-quoted forms with proper escape sequences.
 * Single-quoted PHP strings: only \\ and \' are meaningful escapes.
 * Double-quoted PHP strings: full C-style escape set via stripcslashes.
 */
function rt_php_prop($contenido, $prop) {
    $ep = preg_quote($prop, '/');
    if (preg_match('/\$this->' . $ep . '\s*=\s*\'((?:[^\'\\\\]|\\\\.)*)\'/', $contenido, $m)) {
        return preg_replace_callback('/\\\\([\'\\\\])/', function($mm){ return $mm[1]; }, $m[1]);
    }
    if (preg_match('/\$this->' . $ep . '\s*=\s*"((?:[^"\\\\]|\\\\.)*)"/', $contenido, $m)) {
        return stripcslashes($m[1]);
    }
    return '';
}

/* Lee valores escalares del array devuelto por core/db.local.php sin ejecutarlo. */
function rt_php_array_value($contenido, $key) {
    $ek = preg_quote($key, '/');
    if (preg_match('/[\'"]' . $ek . '[\'"]\s*=>\s*\'((?:[^\'\\\\]|\\\\.)*)\'/', $contenido, $m)) {
        return preg_replace_callback('/\\\\([\'\\\\])/', function($mm){ return $mm[1]; }, $m[1]);
    }
    if (preg_match('/[\'"]' . $ek . '[\'"]\s*=>\s*"((?:[^"\\\\]|\\\\.)*)"/', $contenido, $m)) {
        return stripcslashes($m[1]);
    }
    if (preg_match('/[\'"]' . $ek . '[\'"]\s*=>\s*([0-9]+)/', $contenido, $m)) {
        return $m[1];
    }
    return '';
}

function rt_read_config($carpeta) {
    $config_path = $carpeta . '/core/controller/Database.php';
    if (!file_exists($config_path)) return null;
    $contenido = file_get_contents($config_path);
    $cfg = [
        'folder' => basename($carpeta),
        'host'   => rt_php_prop($contenido, 'host'),
        'user'   => rt_php_prop($contenido, 'user'),
        'pass'   => rt_php_prop($contenido, 'pass'),
        'db'     => rt_php_prop($contenido, 'ddbb'),
        'port'   => 3306,
    ];

    $local_path = $carpeta . '/core/db.local.php';
    if (file_exists($local_path)) {
        $local = file_get_contents($local_path);
        foreach (['host' => 'host', 'user' => 'user', 'pass' => 'pass', 'ddbb' => 'db'] as $source => $target) {
            $value = rt_php_array_value($local, $source);
            if ($value !== '') $cfg[$target] = $value;
        }
        $port = (int)rt_php_array_value($local, 'port');
        if ($port > 0) $cfg['port'] = $port;
    }

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
    if ($cfg['host'] === 'localhost') {
        $hosts = ['localhost', '127.0.0.1'];
    } elseif ($cfg['host'] === '127.0.0.1') {
        $hosts = ['127.0.0.1', 'localhost'];
    } else {
        $hosts = [$cfg['host']];
    }
    $last = null;
    foreach ($hosts as $position => $h) {
        if (rt_quota_left() <= 0) {
            return ['error' => 'QUOTA', 'msg' => 'connection budget exhausted'];
        }
        $GLOBALS['rt_conn_count'] = ($GLOBALS['rt_conn_count'] ?? 0) + 1;
        try {
            $port = isset($cfg['port']) ? (int)$cfg['port'] : 3306;
            $pdo = new PDO(
                "mysql:host={$h};port={$port};dbname={$cfg['db']};charset={$charset}",
                $cfg['user'], $cfg['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 4]
            );
            return ['pdo' => $pdo];
        } catch (PDOException $e) {
            $last = $e;
            $msg = $e->getMessage();
            $msg_lower = strtolower($msg);
            $is_quota = strpos($msg_lower, 'operation not permitted') !== false
                || strpos($msg_lower, 'too many connections') !== false
                || strpos($msg_lower, 'max_user_connections') !== false
                || strpos($msg_lower, 'user resource') !== false;
            if ($is_quota) {
                return ['error' => 'QUOTA', 'msg' => $msg];
            }
            if (strpos($msg, '1045') !== false || strpos($msg, '1044') !== false) {
                // probar siguiente host no ayuda con credenciales inválidas
                return ['error' => 'AUTH', 'msg' => $msg];
            }
            if (strpos($msg, '2002') !== false && $position < count($hosts) - 1) {
                continue;
            }
        }
    }
    return ['error' => 'OTHER', 'msg' => $last ? $last->getMessage() : 'unknown'];
}

function rt_quota_left() {
    return RT_MAX_CONN_PER_REQUEST - ($GLOBALS['rt_conn_count'] ?? 0);
}

/* ---------- índice de usuarios (email/username → carpetas) ---------- */

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
    if (!is_array($j) || !isset($j['folders'])) return ['folders' => []];
    // Entradas de versiones previas sin lista de teléfonos: forzar re-escaneo
    foreach ($j['folders'] as $folder => $data) {
        if (!array_key_exists('phones', $data) && !isset($data['note'])) {
            unset($j['folders'][$folder]);
        }
    }
    return $j;
}

function rt_index_save($base_path, $idx) {
    $idx['updated_at'] = date('c');
    @file_put_contents(rt_index_path($base_path), json_encode($idx), LOCK_EX);
}

/* Escanea una instalación y guarda sus identificadores de acceso en el índice. */
function rt_index_scan_folder(&$idx, $cfg) {
    $c = rt_connect($cfg);
    if (isset($c['error'])) return $c;
    $users = [];
    $phones = [];
    try {
        $st = $c['pdo']->query("SELECT email, username FROM user");
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
            foreach (['email', 'username'] as $field) {
                $value = strtolower(trim((string)$row[$field]));
                if ($value !== '') $users[] = $value;
            }
        }
    } catch (Exception $e) {
        // Algunas instalaciones antiguas no tienen username.
        try {
            $st = $c['pdo']->query("SELECT email FROM user");
            foreach ($st->fetchAll(PDO::FETCH_COLUMN) as $em) {
                $em = strtolower(trim((string)$em));
                if ($em !== '') $users[] = $em;
            }
        } catch (Exception $ignored) {
            // sin tabla user: seguir, quizá haya person
        }
    }
    try {
        // clientes: login con teléfono (guardar normalizado a solo dígitos)
        $st = $c['pdo']->query("SELECT phone, phone2 FROM person");
        foreach ($st->fetchAll(PDO::FETCH_NUM) as $row) {
            foreach ($row as $ph) {
                $ph = rt_clean_phone((string)$ph);
                if ($ph !== '') $phones[] = $ph;
            }
        }
    } catch (Exception $e) {
        // sin tabla person u otra estructura: seguir
    }
    $c['pdo'] = null;
    $idx['folders'][$cfg['folder']] = [
        'scanned_at' => date('c'),
        'users'      => array_values(array_unique($users)),
        'phones'     => array_values(array_unique($phones)),
    ];
    return ['ok' => true, 'count' => count($users) + count($phones)];
}

function rt_index_lookup($idx, $email) {
    $email = strtolower(trim($email));
    $out = [];
    foreach ($idx['folders'] as $folder => $data) {
        if (in_array($email, $data['users'] ?? [], true)) $out[] = $folder;
    }
    return $out;
}

/*
| Ordena las instalaciones ya indexadas desde la menos reciente.
| En un fallo de índice esto permite refrescarlas por lotes sin superar
| el límite de conexiones MySQL del hosting.
*/
function rt_index_refresh_candidates($idx, $configs) {
    $rows = [];
    foreach ($configs as $folder => $cfg) {
        if (!isset($idx['folders'][$folder])) continue;
        $rows[] = [
            'folder'     => $folder,
            'scanned_at' => isset($idx['folders'][$folder]['scanned_at'])
                ? (string)$idx['folders'][$folder]['scanned_at']
                : '',
            'config'     => $cfg,
        ];
    }

    usort($rows, function ($a, $b) {
        $by_date = strcmp($a['scanned_at'], $b['scanned_at']);
        if ($by_date !== 0) return $by_date;
        return strcmp($a['folder'], $b['folder']);
    });

    $out = [];
    foreach ($rows as $row) $out[] = $row['config'];
    return $out;
}

/* ---------- teléfonos (login de clientes: person.phone) ---------- */

function rt_clean_phone($tel) {
    return preg_replace('/[^0-9]/', '', (string)$tel);
}

/* Variantes con/sin código de país "1" (mismo criterio que la app). */
function rt_phone_variants($tel) {
    $n = rt_clean_phone($tel);
    if ($n === '') return [];
    $out = [$n];
    if (strlen($n) >= 10) {
        $last10 = substr($n, -10);
        $out[] = $last10;
        $out[] = '1' . $last10;
    }
    if (strlen($n) >= 11) {
        $last11 = substr($n, -11);
        $out[] = $last11;
        if ($last11[0] === '1') $out[] = (string)substr($last11, 1);
    }
    return array_values(array_unique(array_filter($out)));
}

/* Carpetas donde el índice conoce alguna variante de este teléfono. */
function rt_index_lookup_phone($idx, $tel) {
    $vars = rt_phone_variants($tel);
    if ($vars === []) return [];
    $out = [];
    foreach ($idx['folders'] as $folder => $data) {
        $phones = $data['phones'] ?? [];
        if ($phones === []) continue;
        foreach ($vars as $v) {
            if (in_array($v, $phones, true)) { $out[] = $folder; break; }
        }
    }
    return $out;
}
