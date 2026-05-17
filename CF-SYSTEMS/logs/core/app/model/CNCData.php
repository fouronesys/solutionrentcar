<?php
class CNCData {
	public static $tablename = "cncredito";

	public function __construct(){
		$this->status = "Usado";
	}


	public function add(){
		$sql = "insert into ".self::$tablename." (name_in,name_out,created_at) ";
		$sql .= "value (\"$this->name_in\",\"$this->name_out\",\"$this->created_at\")";
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

	public function update2(){
		$sql = "update ".self::$tablename." set name_in=\"$this->name_in\",name_out=\"$this->name_out\",created_at=\"$this->created_at\" where id=$this->id";
		Executor::doit($sql);
	}
	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new CNCData());
	}

	public static function getAllProductsBySellId($sell_id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where sell_id=$sell_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CNCData());
	}


	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where status='' limit 1";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CNCData());
	}

	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CNCData());
	}



	public function update(){
		$sql = "update ".self::$tablename." set sell_id=\"$this->sell_id\", status=\"$this->status\" where id=\"$this->cnc_id\"";
		Executor::doit($sql);
	}

	


	public function del_sell(){
		$sql = "update ".self::$tablename." set sell_id='', status='' where id=$this->id ";
		Executor::doit($sql);
	}


	public static function getGroupByDateOp(){
  $sql = "select id, count(*) as c from ".self::$tablename." where status!='Usado'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CNCData());
	}





}

?>