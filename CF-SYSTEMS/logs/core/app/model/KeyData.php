<?php
class KeyData {
	public static $tablename = "kay";


	public function add(){
		$sql = "insert into ".self::$tablename." (car_id,user_id,type_id)";
		$sql .= "value (\"$this->car_id\",\"$this->user_id\",\"$this->type_id\")";
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

// partiendo de que ya tenemos creado un objecto KeyData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set car_id=\"$this->car_id\",user_id=\"$this->user_id\",type_id=\"$this->type_id\" where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new KeyData());
	}


	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new KeyData());
	}


	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new KeyData());
	}

	
	public static function getLike($q){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new KeyData());
	}
	
	
	

	


}

?>