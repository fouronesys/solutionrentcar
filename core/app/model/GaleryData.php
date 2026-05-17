<?php
class GaleryData {
	public static $tablename = "galery";



	public function __construct(){
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into galery (car_id,invoice_file,user_id,created_at) ";
		$sql .= "value (\"$this->car_id\",\"$this->invoice_file\",\"$this->user_id\",$this->created_at)";
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


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new GaleryData());
	}


		public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new GaleryData());
	}
	
	public static function getSQL($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new GaleryData());
	}
	


}

?>