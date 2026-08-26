<?php
/**
 * Login de una instalación individual.
 *
 * Se publica como login.php dentro de una instalación. No modifica usuarios,
 * contraseñas ni ninguna tabla; solo crea la sesión cuando el acceso es válido.
 *
 * Compatible con las instalaciones PHP 7.3 existentes:
 * - descubre el núcleo en subcarpetas como DEMO/core/
 * - conserva el hash heredado sha1(md5(password))
 * - acepta hashes creados con password_hash()
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function installation_login_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function installation_login_log(string $code): void
{
    error_log('[installation-login] code=' . $code);
}

function installation_login_start_session(): void
{
    if (PHP_SESSION_ACTIVE === session_status()) {
        return;
    }

    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    if (!session_start()) {
        installation_login_log('session_start');
        installation_login_json([
            'success' => false,
            'message' => 'No se pudo verificar el acceso en este momento.',
        ], 503);
    }
}

function installation_login_php_string(string $source, string $property): string
{
    $propertyPattern = preg_quote($property, '/');
    $pattern = '/\$this->' . $propertyPattern
        . '\s*=\s*(["\'])(.*?)\1/s';

    if (!preg_match($pattern, $source, $matches)) {
        return '';
    }

    if ($matches[1] === "'") {
        return str_replace(["\\\\", "\\'"], ["\\", "'"], $matches[2]);
    }

    return stripcslashes($matches[2]);
}

function installation_login_php_array_value(string $source, string $key): string
{
    $keyPattern = preg_quote($key, '/');
    $pattern = '/[\'"]' . $keyPattern
        . '[\'"]\s*=>\s*(["\'])(.*?)\1/s';

    if (preg_match($pattern, $source, $matches)) {
        if ($matches[1] === "'") {
            return str_replace(["\\\\", "\\'"], ["\\", "'"], $matches[2]);
        }
        return stripcslashes($matches[2]);
    }

    if (preg_match(
        '/[\'"]' . $keyPattern . '[\'"]\s*=>\s*([0-9]+)/',
        $source,
        $matches
    )) {
        return $matches[1];
    }

    return '';
}

function installation_login_config(string $folderPath): ?array
{
    $databasePath = $folderPath . '/core/controller/Database.php';
    if (!is_file($databasePath)) {
        return null;
    }

    $source = (string)file_get_contents($databasePath);
    $config = [
        'folder' => basename($folderPath),
        'host' => installation_login_php_string($source, 'host'),
        'user' => installation_login_php_string($source, 'user'),
        'pass' => installation_login_php_string($source, 'pass'),
        'db' => installation_login_php_string($source, 'ddbb'),
        'port' => 3306,
    ];

    $localPath = $folderPath . '/core/db.local.php';
    if (is_file($localPath)) {
        $localSource = (string)file_get_contents($localPath);
        foreach ([
            'host' => 'host',
            'user' => 'user',
            'pass' => 'pass',
            'ddbb' => 'db',
        ] as $sourceKey => $targetKey) {
            $value = installation_login_php_array_value($localSource, $sourceKey);
            if ($value !== '') {
                $config[$targetKey] = $value;
            }
        }

        $port = (int)installation_login_php_array_value($localSource, 'port');
        if ($port > 0) {
            $config['port'] = $port;
        }
    }

    if ($config['host'] === '' || $config['user'] === '' || $config['db'] === '') {
        installation_login_log('invalid_database_config');
        return null;
    }

    return $config;
}

function installation_login_valid_password(string $rawPassword, string $storedHash): bool
{
    $legacyHash = sha1(md5($rawPassword));
    $storedHash = trim($storedHash);

    if (strlen($storedHash) === strlen($legacyHash)
        && hash_equals(strtolower($storedHash), $legacyHash)) {
        return true;
    }

    $passwordInfo = password_get_info($storedHash);
    if (is_array($passwordInfo) && !empty($passwordInfo['algo'])) {
        return password_verify($rawPassword, $storedHash);
    }

    return false;
}

function installation_login_directories(string $basePath): array
{
    $excluded = ['CLIENTES', 'CF-SYSTEMS', 'logs', 'PWA'];
    $directories = glob($basePath . '/*');
    if (!is_array($directories)) {
        return [];
    }

    $directories = array_filter($directories, function ($path) use ($excluded): bool {
        return is_dir($path) && !in_array(basename($path), $excluded, true);
    });
    sort($directories);
    return array_values($directories);
}

installation_login_start_session();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    installation_login_json([
        'success' => false,
        'message' => 'Faltan datos.',
    ], 400);
}

$username = strtolower(trim((string)($_POST['username'] ?? '')));
$rawPassword = (string)($_POST['password'] ?? '');

if ($username === '' || $rawPassword === '') {
    installation_login_log('missing_fields');
    installation_login_json([
        'success' => false,
        'message' => 'Faltan datos.',
    ], 400);
}

$basePath = __DIR__;
$databaseConfigs = [];
foreach (installation_login_directories($basePath) as $folderPath) {
    $config = installation_login_config($folderPath);
    if ($config !== null) {
        $databaseConfigs[] = $config;
    }
}

$availableDatabases = 0;
$databaseErrors = 0;

foreach ($databaseConfigs as $config) {
    $pdo = null;

    try {
        $port = (int)$config['port'];
        $dsn = 'mysql:host=' . $config['host']
            . ';port=' . $port
            . ';dbname=' . $config['db']
            . ';charset=utf8mb4';

        $pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 4,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        $statusStatement = $pdo->query('SELECT status FROM system_status LIMIT 1');
        $statusRow = $statusStatement ? $statusStatement->fetch() : false;
        if (!$statusRow || (string)$statusRow['status'] !== 'normal') {
            installation_login_log('system_blocked');
            unset($_SESSION['user_id'], $_SESSION['stock_id']);
            installation_login_json([
                'success' => false,
                'message' => 'Sistema bloqueado por emergencia. Contacte al administrador.',
            ], 503);
        }

        $availableDatabases++;
        $statement = $pdo->prepare(
            'SELECT id, stock_id, status, password
             FROM user
             WHERE LOWER(TRIM(email)) = :email
                OR LOWER(TRIM(username)) = :username
             LIMIT 1'
        );
        $statement->execute([
            'email' => $username,
            'username' => $username,
        ]);
        $user = $statement->fetch();

        if (!$user) {
            continue;
        }

        if ((int)$user['status'] !== 1) {
            installation_login_log('inactive_user');
            continue;
        }

        if (!installation_login_valid_password($rawPassword, (string)$user['password'])) {
            installation_login_log('password_mismatch');
            continue;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['stock_id'] = (int)$user['stock_id'];
        $_SESSION['login_type'] = 'user';
        unset($_SESSION['client_id']);

        installation_login_log('success');
        installation_login_json([
            'success' => true,
            'redirect' => $config['folder'] . '/?view=home',
        ]);
    } catch (PDOException $exception) {
        $databaseErrors++;
        installation_login_log('database_error');
    } catch (Throwable $exception) {
        $databaseErrors++;
        installation_login_log('unexpected_error');
    } finally {
        $pdo = null;
    }
}

if ($availableDatabases === 0 && $databaseErrors > 0) {
    installation_login_json([
        'success' => false,
        'message' => 'No se pudo verificar el acceso en este momento.',
    ], 503);
}

installation_login_json([
    'success' => false,
    'message' => 'Credenciales inválidas.',
], 401);