<?php
/**
 * API v1 — single router entry point.
 * URL convention:  /CF-SYSTEMS/api/v1/<resource>[/<id>][/<sub>]
 * When .htaccess is not active (built-in PHP server), use ?path=<resource>/...
 */

require_once __DIR__ . '/bootstrap.php';

// ---- Route extraction ----
$path = '';
if (!empty($_GET['path'])) {
    $path = $_GET['path'];
} else {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $marker = '/api/v1/';
    $pos = strpos($uri, $marker);
    if ($pos !== false) {
        $path = substr($uri, $pos + strlen($marker));
    }
}
$path = trim((string)$path, '/');
if ($path === '' || $path === 'index.php') {
    ApiResponse::ok([
        'name'    => 'Solutions Rent Car API',
        'version' => 'v1',
        'status'  => 'ok',
        'time'    => date('c'),
    ]);
}

$segments = explode('/', $path);
$resource = strtolower($segments[0] ?? '');
$method   = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

$handlers = [
    'auth'          => 'handlers/auth.php',
    'me'            => 'handlers/me.php',
    'cars'          => 'handlers/cars.php',
    'bookings'      => 'handlers/bookings.php',
    'notifications' => 'handlers/notifications.php',
    'payments'      => 'handlers/payments.php',
    'push'          => 'handlers/push.php',
    'catalog'       => 'handlers/catalog.php',
    'health'        => 'handlers/health.php',
];

if (!isset($handlers[$resource])) {
    ApiResponse::err('not_found', "Recurso '$resource' no encontrado", 404);
}

require __DIR__ . '/' . $handlers[$resource];
ApiResponse::err('not_found', 'Endpoint no encontrado', 404);
