<?php
class Database {
    public static $db = null;
    public static $con = null;

    public string $user;
    public string $pass;
    public string $host;
    public string $ddbb;
    public string $socket;
    public int    $port;

    public function __construct(){
        $this->host   = self::env('DB_HOST',   'srv500.hstgr.io');
        $this->user   = self::env('DB_USER',   'u144787244_solutionsrent');
        $this->pass   = self::env('DB_PASS',   'PSsolutions99');
        $this->ddbb   = self::env('DB_NAME',   'u144787244_solutionsrent');
        $this->socket = self::env('DB_SOCKET', '');
        $this->port   = intval(self::env('DB_PORT', '3306'));
    }

    private static function env(string $key, string $default): string {
        $v = getenv($key);
        if ($v === false || $v === '') {
            $v = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        }
        return (string)$v;
    }

    public function connect(){
        $con = new mysqli(
            $this->host,
            $this->user,
            $this->pass,
            $this->ddbb,
            $this->port
        );

        if ($con->connect_errno) {
            die("Error de conexión MySQL: " . $con->connect_error);
        }

        $con->set_charset("utf8mb4");
        $con->query("SET sql_mode=''");

        return $con;
    }

    public static function getCon(){
        if (self::$con === null) {
            self::$db = new Database();
            self::$con = self::$db->connect();
        }

        return self::$con;
    }
}
?>
