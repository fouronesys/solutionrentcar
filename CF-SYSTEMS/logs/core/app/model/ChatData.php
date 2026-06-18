<?php
class ChatData {
	public static $tablename = "chat";


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ChatData());
	}


	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ChatData());
	}


	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new ChatData());
	}

	public static function getLike($q){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where ask like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ChatData());
	}
	
	
	

	


}

?>