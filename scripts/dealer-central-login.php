<?php
/**
 * Login central para las instalaciones Dealer.
 *
 * Este archivo se publica como /DEALER/login.php en el document root
 * de dealership.assanpos.com.
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

function dealer_login_error($message)
{
    echo json_encode(array(
        'success' => false,
        'message' => $message
    ));
    exit;
}

$username_raw = isset($_POST['username']) ? (string)$_POST['username'] : '';
$password_raw = isset($_POST['password']) ? (string)$_POST['password'] : '';

if (trim($username_raw) === '' || $password_raw === '') {
    dealer_login_error('Por favor complete todos los campos.');
}

$identifier = strtolower(trim($username_raw));
$password_hash = sha1(md5($password_raw));
$base_path = __DIR__ . '/';

function dealer_read_database_config($folder)
{
    $config_path = $folder . '/core/controller/Database.php';
    if (!is_file($config_path)) {
        return null;
    }

    $contents = file_get_contents($config_path);
    $matches = array();

    preg_match('/\$this->host\s*=\s*"(.*?)"/', $contents, $matches['host']);
    preg_match('/\$this->user\s*=\s*"(.*?)"/', $contents, $matches['user']);
    preg_match('/\$this->pass\s*=\s*"(.*?)"/', $contents, $matches['pass']);
    preg_match('/\$this->ddbb\s*=\s*"(.*?)"/', $contents, $matches['database']);

    if (
        !isset($matches['host'][1]) ||
        !isset($matches['user'][1]) ||
        !isset($matches['pass'][1]) ||
        !isset($matches['database'][1])
    ) {
        return null;
    }

    return array(
        'folder' => basename($folder),
        'host' => $matches['host'][1],
        'user' => $matches['user'][1],
        'password' => $matches['pass'][1],
        'database' => $matches['database'][1]
    );
}

function dealer_find_user($config, $identifier, $password_hash)
{
    $dsn = 'mysql:host=' . $config['host'] .
        ';dbname=' . $config['database'] .
        ';charset=utf8';

    $pdo = new PDO(
        $dsn,
        $config['user'],
        $config['password'],
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    );

    $email_statement = $pdo->prepare(
        'SELECT id, stock_id
         FROM `user`
         WHERE LOWER(TRIM(email)) = :email
           AND password = :password
           AND status = 1
         LIMIT 1'
    );
    $email_statement->execute(array(
        'email' => $identifier,
        'password' => $password_hash
    ));

    $user = $email_statement->fetch();
    if ($user) {
        return $user;
    }

    // Algunas instalaciones antiguas guardan el identificador en username.
    $columns = $pdo->query('SHOW COLUMNS FROM `user`')->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('username', $columns, true)) {
        return null;
    }

    $username_statement = $pdo->prepare(
        'SELECT id, stock_id
         FROM `user`
         WHERE LOWER(TRIM(username)) = :username
           AND password = :password
           AND status = 1
         LIMIT 1'
    );
    $username_statement->execute(array(
        'username' => $identifier,
        'password' => $password_hash
    ));

    return $username_statement->fetch() ?: null;
}

$folders = glob($base_path . '*', GLOB_ONLYDIR);
if ($folders === false) {
    dealer_login_error('No se pudo verificar en este momento. Intente nuevamente en unos segundos.');
}

foreach ($folders as $folder) {
    if (basename($folder) === 'CF-SYSTEMS') {
        continue;
    }

    $config = dealer_read_database_config($folder);
    if ($config === null) {
        continue;
    }

    try {
        $user = dealer_find_user($config, $identifier, $password_hash);
    } catch (Throwable $exception) {
        // Una instalación caída no debe bloquear la autenticación de las demás.
        continue;
    }

    if ($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['stock_id'] = (int)$user['stock_id'];
        $_SESSION['login_type'] = 'user';
        unset($_SESSION['client_id']);

        echo json_encode(array(
            'success' => true,
            'redirect' => $config['folder'] . '/?view=home'
        ));
        exit;
    }
}

dealer_login_error('Por favor verifique su nombre de usuario y contraseña.');