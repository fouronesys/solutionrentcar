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
$summary = [];
foreach ($carpetas as $carpeta) {
    $config_path = $carpeta . '/core/controller/Database.php';
    if (!file_exists($config_path)) { echo basename($carpeta) . "|NO_CONFIG\n"; continue; }
    $contenido = file_get_contents($config_path);
    preg_match('/\$this->host\s*=\s*[\'"](.*?)[\'"]/', $contenido, $host);
    preg_match('/\$this->user\s*=\s*[\'"](.*?)[\'"]/', $contenido, $user);
    preg_match('/\$this->pass\s*=\s*[\'"](.*?)[\'"]/', $contenido, $pass);
    preg_match('/\$this->ddbb\s*=\s*[\'"](.*?)[\'"]/', $contenido, $db);
    $db_host = $host[1] ?? ''; $db_user = $user[1] ?? ''; $db_pass = $pass[1] ?? ''; $db_name = $db[1] ?? '';
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
