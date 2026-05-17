<?php
/**
 * API v1 bootstrap — loads the existing CF/ASSANPOS core (models, controllers,
 * Database, NotificationService, etc.) without booting the layout/session UI.
 */

if (defined('API_V1_BOOTED')) return;
define('API_V1_BOOTED', true);

date_default_timezone_set('America/Santo_Domingo');
error_reporting(0);
@ini_set('display_errors', '0');

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Project root: /<root>/  (this file is at /CF-SYSTEMS/api/v1/bootstrap.php)
$ROOT = dirname(__DIR__, 3);
if (!defined('ROOT')) define('ROOT', $ROOT);

chdir($ROOT);

require_once $ROOT . '/core/autoload.php';

// Load API libs
require_once __DIR__ . '/lib/ApiResponse.php';
require_once __DIR__ . '/lib/ApiHelpers.php';
require_once __DIR__ . '/lib/ApiSchema.php';
require_once __DIR__ . '/lib/Jwt.php';
require_once __DIR__ . '/lib/ApiAuth.php';

ApiSchema::ensure();

if (class_exists('NotificationData')) {
    NotificationData::ensureSchema();
}

// Generic CORS headers (mirrors .htaccess for built-in PHP server).
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}
