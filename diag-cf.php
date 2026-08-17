<?php
// Diagnóstico temporal — eliminar tras usar.
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');
echo "PHP: " . PHP_VERSION . "\n";
echo "mysqli: " . (extension_loaded('mysqli') ? 'ok' : 'FALTA') . "\n";

$cfgFile = __DIR__ . '/core/db.local.php';
echo "db.local.php: " . (is_file($cfgFile) ? 'presente' : 'AUSENTE') . "\n";
$cfg = is_file($cfgFile) ? include $cfgFile : [];

mysqli_report(MYSQLI_REPORT_OFF);
$host = $cfg['host'] ?? 'srv500.hstgr.io';
$con = @new mysqli($host, $cfg['user'] ?? '', $cfg['pass'] ?? '', $cfg['ddbb'] ?? '', (int)($cfg['port'] ?? 3306));
if ($con->connect_errno) {
    echo "MySQL: ERROR {$con->connect_errno} {$con->connect_error}\n";
} else {
    $r = $con->query("SELECT COUNT(*) c FROM cars");
    echo "MySQL: ok, cars=" . ($r ? $r->fetch_assoc()['c'] : 'sin tabla') . "\n";
}

echo "\n== Cargando core ==\n";
try {
    require_once __DIR__ . '/core/autoload.php';
    echo "autoload: ok\n";
} catch (Throwable $e) {
    echo "autoload: " . get_class($e) . ": " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine() . "\n";
}
