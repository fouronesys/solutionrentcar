<?php
// Diagnóstico temporal — eliminar tras usar.
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');
$_SERVER['REQUEST_METHOD'] = 'POST';
try {
    require_once __DIR__ . '/CF-SYSTEMS/api/v1/bootstrap.php';
    echo "bootstrap: ok\n";
    $rt = __DIR__ . '/CF-SYSTEMS/storage/runtime';
    echo "storage/runtime: " . (is_dir($rt) ? 'existe' : 'NO existe') . ", padre escribible: " . (is_writable(dirname($rt)) ? 'si' : 'NO') . "\n";
    $tok = ApiAuth::issueTokens('client', 4, ['stock_id' => 0]);
    echo "issueTokens: ok, access len=" . strlen($tok['access_token'] ?? '') . "\n";
} catch (Throwable $e) {
    echo get_class($e) . ": " . $e->getMessage() . "\n  en " . $e->getFile() . ":" . $e->getLine() . "\n" . substr($e->getTraceAsString(), 0, 800) . "\n";
}
