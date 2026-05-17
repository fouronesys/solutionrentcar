<?php
class Database {
    public static $db = null;
    public static $con = null;

    public string $user;
    public string $pass;
    public string $host;
    public string $ddbb;
    public string $socket;

    public function __construct(){
        $this->user = "u144787244_solutionsrent";
        $this->pass = "PSsolutions99";
        $this->host = "31.220.16.1";
        $this->ddbb = "u144787244_solutionsrent";
        $this->socket = "";
    }

    public function connect(){
        $con = new mysqli(
            $this->host,
            $this->user,
            $this->pass,
            $this->ddbb,
            3306
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
