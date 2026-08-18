<?php
// One-shot diagnostic: for each RENTCAR installation, try connecting via
// socket (localhost), 127.0.0.1 and 195.35.61.45 and report per-host results.
ini_set('display_errors', 0);
error_reporting(0);
header('Content-Type: text/plain; charset=utf-8');
echo "pdo_mysql.default_socket=" . ini_get('pdo_mysql.default_socket') . "\n";
echo "mysqli.default_socket=" . ini_get('mysqli.default_socket') . "\n\n";
$base_path = __DIR__ . '/';
$carpetas = array_filter(glob($base_path . '/*'), function ($dir) {
    $excluidas = ['CLIENTES', 'CF-SYSTEMS', 'logs', 'PWA'];
    return is_dir($dir) && !in_array(basename($dir), $excluidas);
});
$hosts = ['SOCKET' => 'localhost', 'LOCALIP' => '127.0.0.1', 'PUBIP' => '195.35.61.45'];
if (isset($_GET['host']) && $_GET['host'] === 'socket') $hosts = ['SOCKET' => 'localhost'];
if (isset($_GET['order']) && $_GET['order'] === 'rev') { $carpetas = array_reverse($carpetas); echo "ORDER=REVERSED\n"; }

/**
 * Extract a PHP string property value from a Database.php source, handling
 * both single-quoted and double-quoted forms with proper escape sequences.
 * Single-quoted PHP strings: only \\ and \' are meaningful escapes.
 * Double-quoted PHP strings: full C-style escape set via stripcslashes.
 */
function rt_extract_prop($contenido, $prop) {
    $escaped_prop = preg_quote($prop, '/');
    // Single-quoted form: 'value' where value may contain \\ or \'
    if (preg_match('/\$this->' . $escaped_prop . '\s*=\s*\'((?:[^\'\\\\]|\\\\.)*)\'/', $contenido, $m)) {
        return preg_replace_callback('/\\\\([\'\\\\])/', function($mm){ return $mm[1]; }, $m[1]);
    }
    // Double-quoted form
    if (preg_match('/\$this->' . $escaped_prop . '\s*=\s*"((?:[^"\\\\]|\\\\.)*)"/', $contenido, $m)) {
        return stripcslashes($m[1]);
    }
    return '';
}

$summary = [];
foreach ($carpetas as $carpeta) {
    $config_path = $carpeta . '/core/controller/Database.php';
    if (!file_exists($config_path)) { echo basename($carpeta) . "|NO_CONFIG\n"; continue; }
    $contenido = file_get_contents($config_path);
    $db_host = rt_extract_prop($contenido, 'host');
    $db_user = rt_extract_prop($contenido, 'user');
    $db_pass = rt_extract_prop($contenido, 'pass');
    $db_name = rt_extract_prop($contenido, 'ddbb');
    if (empty($db_host) || empty($db_user) || empty($db_name)) { echo basename($carpeta) . "|INCOMPLETE\n"; continue; }
    if ($db_host !== 'localhost' && $db_host !== '127.0.0.1') {
        echo basename($carpeta) . "|CUSTOM_HOST:" . $db_host . "\n"; continue;
    }
    $line = basename($carpeta);
    $ok = null;
    foreach ($hosts as $tag => $h) {
        try {
            $pdo = new PDO("mysql:host={$h};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 4]);
            $line .= "|{$tag}:OK";
            if ($ok === null) $ok = $tag;
            $pdo = null;
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            $code = 'ERR';
            if (preg_match('/\[(\d{4})\]/', $msg, $m)) $code = $m[1];
            elseif (strpos($msg, '2002') !== false) $code = '2002';
            elseif (strpos($msg, '1045') !== false) $code = '1045';
            elseif (strpos($msg, '1044') !== false) $code = '1044';
            $line .= "|{$tag}:{$code}";
        }
    }
    $key = $ok === null ? 'ALL_FAIL' : 'FIRST_OK_' . $ok;
    $summary[$key] = ($summary[$key] ?? 0) + 1;
    echo $line . "\n";
}
echo "\n== SUMMARY ==\n";
foreach ($summary as $k => $v) echo "$k: $v\n";
