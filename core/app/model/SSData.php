<?php
class SSData {
	public static $tablename = "session";

	public function __construct(){
		
		$this->created_in = "NOW()";
	}

	public function getUser(){ return UserData::getById($this->user_id);}
	public function getPerson(){ return PersonData::getById($this->client_id);}

	public function add(){
		$sql = "insert into ".self::$tablename." (user_id,created_in) ";
		$sql .= "value (\"$this->user_id\",$this->created_in)";
		Executor::doit($sql);
	}

		public function add_client(){
		$sql = "insert into ".self::$tablename." (client_id,created_in) ";
		$sql .= "value (\"$this->client_id\",$this->created_in)";
		Executor::doit($sql);
	}


// partiendo de que ya tenemos creado un objecto BrandData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set created_out=$this->created_in where user_id = \"$this->user_id\"";
		Executor::doit($sql);
	}


	public static function getSQL($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new SSData());
	}

	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SSData());
	}

	public static function getAllBySQL2($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new SSData());
	}



}

?>