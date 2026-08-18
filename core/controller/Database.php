<?php
class Database {
    public static $db = null;
    public static $con = null;

    public $user;
    public $pass;
    public $host;
    public $ddbb;
    public $socket;
    public $port;

    public function __construct(){
        // Valores por defecto (el deploy los reemplaza por los de la instancia)
        $this->host   = 'srv500.hstgr.io';
        $this->user   = 'u144787244_solutionsrent';
        $this->pass   = 'PSsolutions99';
        $this->ddbb   = 'u144787244_solutionsrent';
        $this->socket = '';
        $this->port   = 3306;

        // Override por instancia: core/db.local.php (no versionado, excluido del
        // deploy FTP) devuelve un array ['host'=>..,'user'=>..,'pass'=>..,'ddbb'=>..,'port'=>..]
        $local = __DIR__ . '/../db.local.php';
        if (is_file($local)) {
            $cfg = include $local;
            if (is_array($cfg)) {
                foreach (['host','user','pass','ddbb','socket'] as $k) {
                    if (isset($cfg[$k])) $this->$k = (string)$cfg[$k];
                }
                if (isset($cfg['port'])) $this->port = (int)$cfg['port'];
            }
        }
    }

    public function connect(){
        if ($this->host === '' || $this->user === '' || $this->ddbb === '') {
            die("Configuración de base de datos ausente: falta core/db.local.php o variables de entorno DB_*");
        }
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
