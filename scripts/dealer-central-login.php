<?php

session_start();
header('Content-Type: application/json; charset=utf-8');

$identifier = strtolower(trim((string)(isset($_POST['username']) ? $_POST['username'] : '')));
$password = (string)(isset($_POST['password']) ? $_POST['password'] : '');

if ($identifier === '' || $password === '') {
    echo json_encode(array(
        'success' => false,
        'message' => 'Por favor complete todos los campos.'
    ));
    exit;
}

$password_hash = sha1(md5($password));
$folders = glob(__DIR__ . '/*', GLOB_ONLYDIR);
$ordered_folders = array();
$priority_folders = array('JER-IMPORT2R', 'SEVENFAUTOS');

foreach ($priority_folders as $priority_folder) {
    foreach ($folders ?: array() as $folder) {
        if (basename($folder) === $priority_folder) {
            $ordered_folders[] = $folder;
            break;
        }
    }
}

foreach ($folders ?: array() as $folder) {
    if (!in_array($folder, $ordered_folders, true)) {
        $ordered_folders[] = $folder;
    }
}

foreach ($ordered_folders as $folder) {
    $folder_name = basename($folder);
    if ($folder_name === 'CF-SYSTEMS') {
        continue;
    }

    $config_file = $folder . '/core/controller/Database.php';
    if (!is_file($config_file)) {
        continue;
    }

    $config_source = file_get_contents($config_file);
    $host = array();
    $db_user = array();
    $db_password = array();
    $database = array();

    preg_match('/\$this->host\s*=\s*"(.*?)"/', $config_source, $host);
    preg_match('/\$this->user\s*=\s*"(.*?)"/', $config_source, $db_user);
    preg_match('/\$this->pass\s*=\s*"(.*?)"/', $config_source, $db_password);
    preg_match('/\$this->ddbb\s*=\s*"(.*?)"/', $config_source, $database);

    if (!isset($host[1], $db_user[1], $db_password[1], $database[1])) {
        continue;
    }

    try {
        $pdo = new PDO(
            'mysql:host=' . $host[1] . ';dbname=' . $database[1] . ';charset=utf8',
            $db_user[1],
            $db_password[1],
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 2
            )
        );

        $statement = $pdo->prepare(
            'SELECT id, stock_id
             FROM `user`
             WHERE LOWER(TRIM(email)) = :email
               AND password = :password
               AND status = 1
             LIMIT 1'
        );
        $statement->execute(array(
            'email' => $identifier,
            'password' => $password_hash
        ));
        $matched_user = $statement->fetch();

        if (!$matched_user) {
            $columns = $pdo->query('SHOW COLUMNS FROM `user`')->fetchAll(PDO::FETCH_COLUMN);
            if (in_array('username', $columns, true)) {
                $statement = $pdo->prepare(
                    'SELECT id, stock_id
                     FROM `user`
                     WHERE LOWER(TRIM(username)) = :username
                       AND password = :password
                       AND status = 1
                     LIMIT 1'
                );
                $statement->execute(array(
                    'username' => $identifier,
                    'password' => $password_hash
                ));
                $matched_user = $statement->fetch();
            }
        }

        if (is_array($matched_user) && isset($matched_user['id'])) {
            $_SESSION['user_id'] = (int)$matched_user['id'];
            $_SESSION['stock_id'] = isset($matched_user['stock_id'])
                ? (int)$matched_user['stock_id']
                : 0;
            $_SESSION['login_type'] = 'user';
            unset($_SESSION['client_id']);

            echo json_encode(array(
                'success' => true,
                'redirect' => $folder_name . '/?view=home'
            ));
            exit;
        }
    } catch (Throwable $exception) {
        continue;
    }
}

echo json_encode(array(
    'success' => false,
    'message' => 'Por favor verifique su nombre de usuario y contraseña.'
));