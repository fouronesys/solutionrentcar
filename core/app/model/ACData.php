<?php
class ACData {
	public static $tablename = "activity";


	public function __construct(){
		
		$this->created_at = "NOW()";
	}

	public function getUser(){ return UserData::getById($this->user_id);}

	public function add(){
		$sql = "insert into ".self::$tablename." (user_id,accion,created_at) ";
		$sql .= "value (\"$this->user_id\",\"$this->accion\",$this->created_at)";
		Executor::doit($sql);
	}

		public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ACData());
	}


		public static function getAllByUserId($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where user_id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ACData());
	}


	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." order by created_at desc ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ACData());
	}

	public static function getLike($q){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where user_id like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ACData());
	}
	
	public static function getAllBySQL($sqlextra){
		$sql = "select * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ACData());
	}
		public static function getAllBySQL2($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new ACData());
	}




}

?>