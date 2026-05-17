<?php class UserPermissionsData {
	public static $tablename = "permits_user";

	public function __construct(){
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (user_id,permits_id,created_at) ";
		$sql .= "value (\"$this->user_id\",\"$this->permits_id\",$this->created_at)";
		Executor::doit($sql);
	}

	public static function delById($id){
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}
	public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		Executor::doit($sql);
	}

// partiendo de que ya tenemos creado un objecto UserPermissionsData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set is_active=0 where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new UserPermissionsData());
	}


	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserPermissionsData());
	}


	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserPermissionsData());
	}

	

	public static function getLike($q){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserPermissionsData());
	}

	public static function getAllByPermitsId($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where user_id=".$id;
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserPermissionsData());
	}
	
		public static function getGroupByOp($start,$end){
  $sql = "select id,count(*) as c from ".self::$tablename." where user_id= \"$start\" and permits_id= \"$end\"";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}

	public static function getAllByPermitsId2($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where user_id=$id  order by permits_id asc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserPermissionsData());
	}

	public static function getAllActive(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename."  where is_active=1";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserPermissionsData());
	}


	public static function getAllUnActive(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename."  where is_active=0";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserPermissionsData());
	}



	public static function getAllByPage($start_from,$limit){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id>=$start_from limit $limit";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserPermissionsData());
	}



	public static function getActiveLike($p){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where (name like '%$p%' or id like '%$p%') and is_active=1 ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserPermissionsData());
	}


	public static function getAllByUserId($user_id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where user_id=$user_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserPermissionsData());
	}

	


}

?>