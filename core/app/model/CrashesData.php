<?php
class CrashesData {
	public static $tablename = "crashes";


	public function add(){
		$sql = "insert into ".self::$tablename." (car_id,person_id,type_id,price)";
		$sql .= "value (\"$this->car_id\",\"$this->person_id\",\"$this->type_id\",\"$this->price\")";
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

// partiendo de que ya tenemos creado un objecto CrashesData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set car_id=\"$this->car_id\",person_id=\"$this->person_id\",type_id=\"$this->type_id\",price=\"$this->price\" where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new CrashesData());
	}


	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CrashesData());
	}


	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new CrashesData());
	}



}

?>