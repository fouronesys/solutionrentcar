<?php
session_start();
header('Content-Type: application/json');

// Obtener datos del formulario
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password_raw = isset($_POST['password']) ? $_POST['password'] : '';

if ($username === '' || $password_raw === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor complete todos los campos.'
    ]);
    exit;
}

// Mantener el mismo sistema de contraseña de tu aplicación
$password = sha1(md5($password_raw));

$base_path = __DIR__ . '/';

$carpetas = array_filter(
    glob($base_path . '/*'),
    function ($dir) {
        $excluidas = array(
            'CF-SYSTEMS'
        );

        return is_dir($dir) && !in_array(basename($dir), $excluidas);
    }
);

$databases = array();

// Buscar y cargar configuraciones de cada instalación
foreach ($carpetas as $carpeta) {

    $config_path = $carpeta . '/core/controller/Database.php';

    if (!file_exists($config_path)) {
        continue;
    }

    $contenido = file_get_contents($config_path);

    $host = array();
    $user = array();
    $pass = array();
    $dbname = array();

    preg_match('/\$this->host\s*=\s*"(.*?)"/', $contenido, $host);
    preg_match('/\$this->user\s*=\s*"(.*?)"/', $contenido, $user);
    preg_match('/\$this->pass\s*=\s*"(.*?)"/', $contenido, $pass);
    preg_match('/\$this->ddbb\s*=\s*"(.*?)"/', $contenido, $dbname);

    if (
        isset($host[1]) &&
        isset($user[1]) &&
        isset($pass[1]) &&
        isset($dbname[1])
    ) {

        $nombre = basename($carpeta);

        $databases[$nombre] = array(
            'host' => $host[1],
            'dbname' => $dbname[1],
            'user' => $user[1],
            'password' => $pass[1]
        );
    }
}

$found = false;

// Buscar el usuario en todas las bases de datos
foreach ($databases as $folder => $db) {

    try {

        // RENTCAR_HOST_PATCH: mapear localhost → IP TCP real
        if ($db['host'] === 'localhost' || $db['host'] === '127.0.0.1') $db['host'] = '127.0.0.1';
        $dsn = "mysql:host=" . $db['host'] .
               ";dbname=" . $db['dbname'] .
               ";charset=utf8";

        $pdo = new PDO(
            $dsn,
            $db['user'],
            $db['password'],
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            )
        );

        $stmt = $pdo->prepare(
            "SELECT * 
             FROM user 
             WHERE email = :username 
             AND password = :password 
             LIMIT 1"
        );

        $stmt->execute(
            array(
                'username' => $username,
                'password' => $password
            )
        );

        $result = $stmt->fetch();

        if ($result) {

            $_SESSION['user_id'] = $result['id'];
            $_SESSION['stock_id'] = $result['stock_id'];

            echo json_encode(
                array(
                    'success' => true,
                    'redirect' => $folder . '/?view=home'
                )
            );

            exit;
        }

    } catch (PDOException $e) {

        // Si una instalación tiene problemas de conexión,
        // simplemente continúa con la siguiente.

        continue;
    }
}

// Usuario no encontrado
echo json_encode(
    array(
        'success' => false,
        'message' => 'Por favor verifique su nombre de usuario y contraseña.'
    )
);

exit;
?>