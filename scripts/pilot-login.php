<?php
/**
 * Login piloto para una sola instalación.
 *
 * Uso:
 *   1. Copiar este archivo al directorio raíz de una instalación.
 *   2. Abrir /login-pilot.php en el navegador.
 *   3. Probar sin reemplazar login.php.
 *
 * No cambia contraseñas ni modifica la base de datos.
 */

declare(strict_types=1);

$appRoot = is_file(__DIR__ . '/core/controller/Database.php')
    ? __DIR__
    : dirname(__DIR__);

require_once $appRoot . '/core/controller/Database.php';

if (PHP_SESSION_ACTIVE !== session_status()) {
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function pilot_h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function pilot_log(string $code): void
{
    error_log('[login-pilot] code=' . $code);
}

$error = '';
$info = '';
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = strtolower(trim((string)($_POST['username'] ?? $_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');

    if ($identifier === '' || $password === '') {
        $error = 'Complete el usuario y la contraseña.';
        pilot_log('missing_fields');
    } else {
        mysqli_report(MYSQLI_REPORT_OFF);
        $database = null;
        $connection = null;
        $statement = null;

        try {
            $database = new Database();
            $socket = isset($database->socket) && $database->socket !== ''
                ? $database->socket
                : null;
            $connection = @new mysqli(
                (string)$database->host,
                (string)$database->user,
                (string)$database->pass,
                (string)$database->ddbb,
                (int)$database->port,
                $socket
            );

            if ($connection->connect_errno) {
                $error = 'No se pudo verificar el acceso en este momento.';
                pilot_log('database_connection');
            } else {
                $connection->set_charset('utf8mb4');
                $sql = <<<'SQL'
SELECT id, stock_id, kind, status, password
FROM user
WHERE LOWER(TRIM(email)) = ?
   OR LOWER(TRIM(username)) = ?
LIMIT 1
SQL;
                $statement = $connection->prepare($sql);

                if (!$statement) {
                    $error = 'No se pudo verificar el acceso en este momento.';
                    pilot_log('query_prepare');
                } else {
                    $statement->bind_param('ss', $identifier, $identifier);
                    if (!$statement->execute()) {
                        $error = 'No se pudo verificar el acceso en este momento.';
                        pilot_log('query_execute');
                    } else {
                        $statement->bind_result($userId, $stockId, $kind, $status, $storedHash);
                        $found = $statement->fetch();

                        if (!$found) {
                            $error = 'El usuario o la contraseña no son válidos.';
                            pilot_log('invalid_credentials');
                        } elseif ((int)$status !== 1) {
                            $error = 'La cuenta está inactiva. Solicite su activación.';
                            pilot_log('inactive_user');
                        } else {
                            $legacyHash = sha1(md5($password));
                            $valid = hash_equals((string)$storedHash, $legacyHash);

                            if (!$valid && password_get_info((string)$storedHash)['algo'] !== 0) {
                                $valid = password_verify($password, (string)$storedHash);
                            }

                            if (!$valid) {
                                $error = 'El usuario o la contraseña no son válidos.';
                                pilot_log('password_mismatch');
                            } else {
                                session_regenerate_id(true);
                                $_SESSION['user_id'] = (int)$userId;
                                $_SESSION['stock_id'] = (int)$stockId;
                                $_SESSION['kind'] = (int)$kind;
                                $_SESSION['login_type'] = 'user';
                                unset($_SESSION['client_id']);

                                pilot_log('success');
                                header('Location: ./?view=home', true, 302);
                                exit;
                            }
                        }
                    }
                }
            }
        } catch (Throwable $exception) {
            $error = 'No se pudo verificar el acceso en este momento.';
            pilot_log('unexpected_error');
        } finally {
            if ($statement instanceof mysqli_stmt) {
                $statement->close();
            }
            if ($connection instanceof mysqli) {
                $connection->close();
            }
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prueba de inicio de sesión</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 420px; margin: 48px auto; padding: 0 20px; }
        label { display: block; margin: 14px 0 6px; }
        input { box-sizing: border-box; width: 100%; padding: 10px; }
        button { margin-top: 18px; padding: 10px 16px; cursor: pointer; }
        .error { color: #a40000; margin: 16px 0; }
        .info { color: #14532d; margin: 16px 0; }
    </style>
</head>
<body>
    <h1>Prueba de inicio de sesión</h1>
    <p>Este archivo es piloto y no reemplaza el login actual.</p>
    <?php if ($error !== ''): ?>
        <p class="error"><?= pilot_h($error) ?></p>
    <?php endif; ?>
    <?php if ($info !== ''): ?>
        <p class="info"><?= pilot_h($info) ?></p>
    <?php endif; ?>
    <form method="post" autocomplete="on">
        <label for="username">Usuario o correo</label>
        <input id="username" name="username" type="text"
               value="<?= pilot_h($identifier) ?>" required autocomplete="username">
        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password"
               required autocomplete="current-password">
        <button type="submit">Probar acceso</button>
    </form>
</body>
</html>