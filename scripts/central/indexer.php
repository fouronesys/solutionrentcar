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
$idx = rt_index_load($base_path);

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
