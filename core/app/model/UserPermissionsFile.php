<?php
class UserPermissionsFile {
    private static $file = __DIR__ . "/permisos_data.php";

    private static function cargar() {
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate(self::$file, true);
        }
        return file_exists(self::$file) ? include self::$file : [];
    }

    private static function guardar($data) {
        file_put_contents(self::$file, '<?php return ' . var_export($data, true) . ';');
    }

    public $id;
    public $user_id;
    public $permits_id;

    public function __construct() {
        $this->created_at = date("Y-m-d H:i:s");
    }

    public function add() {
        $data = self::cargar();
        $uid = (int)$this->user_id;
        $pid = (int)$this->permits_id;

        if (!isset($data[$uid])) $data[$uid] = [];
        if (!in_array($pid, $data[$uid])) {
            $data[$uid][] = $pid;
            self::guardar($data);
        }
    }

    public static function delById($id) {
        // En archivo no hay ID único, este método no aplica directamente
        // Se puede ignorar o lanzar error si deseas
    }

    public function del() {
        $data = self::cargar();
        $uid = (int)$this->user_id;
        $pid = (int)$this->permits_id;

        if (isset($data[$uid])) {
            $data[$uid] = array_values(array_diff($data[$uid], [$pid]));
            if (empty($data[$uid])) unset($data[$uid]);
            self::guardar($data);
        }
    }

    public static function getAllByUserId($user_id) {
        $data = self::cargar();
        $uid = (int)$user_id;
        $result = [];

        if (isset($data[$uid])) {
            foreach ($data[$uid] as $pid) {
                $perm = new self();
                $perm->user_id = $uid;
                $perm->permits_id = $pid;
                $result[] = $perm;
            }
        }
        return $result;
    }

    public static function getGroupByOp($user_id, $permits_id) {
        $data = self::cargar();
        $uid = (int)$user_id;
        $pid = (int)$permits_id;

        $c = (isset($data[$uid]) && in_array($pid, $data[$uid])) ? 1 : 0;

        $r = new stdClass();
        $r->id = $pid;  // Aquí se usa el permiso como ID ficticio
        $r->c = $c;

        return [$r];
    }

    // Métodos que acceden por ID única no aplican directamente
    public static function getById($id) { return null; }
    public static function getAll() { return []; }
    public static function getLike($q) { return []; }
    public static function getAllByPermitsId($id) { return []; }
    public static function getAllByPermitsId2($id) { return []; }
    public static function getAllActive() { return []; }
    public static function getAllUnActive() { return []; }
    public static function getAllByPage($start_from, $limit) { return []; }
    public static function getActiveLike($p) { return []; }
}
?>
