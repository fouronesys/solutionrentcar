<?php
/*
| Construye el índice de usuarios (email → instalación) por lotes.
| Cada request escanea hasta RT_MAX_CONN_PER_REQUEST instalaciones y
| persiste el avance en logs/central_user_index.json.
| Llamar repetidamente hasta ver "PENDIENTES=0".
|   ?rescan=1  → vuelve a escanear todo desde cero
*/
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/rtcommon.php';

$base_path = __DIR__;
rt_require_admin_key($base_path);
$idx = rt_index_load($base_path);

/*
| ?selftest=N → valida la ruta índice→conexión→consulta de login en una
| muestra de N instalaciones indexadas con usuarios (sin credenciales reales).
*/
if (isset($_GET['selftest'])) {
    $n = max(1, min(15, (int)$_GET['selftest']));
    $configs = [];
    foreach (rt_folders($base_path) as $carpeta) {
        $cfg = rt_read_config($carpeta);
        if ($cfg !== null) $configs[$cfg['folder']] = $cfg;
    }
    $candidates = [];
    foreach ($idx['folders'] as $folder => $data) {
        if (!empty($data['users']) && isset($configs[$folder])) $candidates[] = $folder;
    }
    shuffle($candidates);
    $ok = 0; $fail = 0;
    foreach (array_slice($candidates, 0, $n) as $folder) {
        $cfg = $configs[$folder];
        $email = $idx['folders'][$folder]['users'][0];
        $c = rt_connect($cfg, 'utf8');
        if (isset($c['error'])) { echo "$folder|" . $c['error'] . "\n"; $fail++; continue; }
        try {
            $st = $c['pdo']->prepare("SELECT id FROM user WHERE email = :u AND password = :p LIMIT 1");
            $st->execute(['u' => $email, 'p' => 'selftest-invalid']);
            $st->fetch();
            echo "$folder|LOGIN_PATH_OK\n"; $ok++;
        } catch (Exception $e) {
            echo "$folder|QUERY_ERROR\n"; $fail++;
        } finally { $c['pdo'] = null; }
    }
    echo "\nSELFTEST_OK=$ok SELFTEST_FAIL=$fail INDEXED=" . count($candidates) . "\n";
    exit;
}

if (isset($_GET['rescan'])) {
    $idx = ['folders' => []];
    echo "RESCAN: índice reiniciado\n";
}

$configs = [];
foreach (rt_folders($base_path) as $carpeta) {
    $cfg = rt_read_config($carpeta);
    if ($cfg !== null) $configs[$cfg['folder']] = $cfg;
}

$scanned = 0; $errors = 0; $quota = false;
foreach ($configs as $folder => $cfg) {
    if (isset($idx['folders'][$folder])) continue;
    if (rt_quota_left() <= 0) break;
    $s = rt_index_scan_folder($idx, $cfg);
    if (isset($s['error'])) {
        if ($s['error'] === 'QUOTA') { echo "$folder|QUOTA\n"; $quota = true; break; }
        echo "$folder|" . $s['error'] . "\n";
        $errors++;
        // marcar para no reintentar en cada pasada (AUTH no se arregla solo)
        $idx['folders'][$folder] = ['scanned_at' => date('c'), 'users' => [], 'note' => $s['error']];
        continue;
    }
    echo "$folder|OK|users=" . $s['count'] . "\n";
    $scanned++;
}

rt_index_save($base_path, $idx);

$pending = 0;
foreach ($configs as $folder => $cfg) {
    if (!isset($idx['folders'][$folder])) $pending++;
}
echo "\nESCANEADAS_AHORA=$scanned ERRORES=$errors TOTAL=" . count($configs) . "\n";
echo "PENDIENTES=$pending\n";
