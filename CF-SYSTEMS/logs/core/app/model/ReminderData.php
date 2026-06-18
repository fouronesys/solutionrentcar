<?php
class ReminderData {
	public static $tablename = "reminder";

	public function __construct(){
		$this->created_at = "NOW()";
	}


	public function add(){
		$sql = "insert into ".self::$tablename." (start_at,name,user_id,stock_id,created_at) ";
		$sql .= "value (\"$this->start_at\",\"$this->name\",\"$this->user_id\",\"$this->stock_id\",$this->created_at)";
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

// partiendo de que ya tenemos creado un objecto ReminderData previamente utilizamos el contexto
	public function upd(){
		$sql = "update ".self::$tablename." set start_at=\"$this->start_at\" where id=$this->id";
		Executor::doit($sql);
	}
	
		public function update(){
		$sql = "update ".self::$tablename." set start_at=\"$this->start_at\", name=\"$this->name\" where id=$this->id";
		Executor::doit($sql);
	}



	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ReminderData());
	}


	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ReminderData());
	}


	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new ReminderData());
	}


	public static function getLike($q){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ReminderData());
	}

	


}

?>