<?php

#[AllowDynamicProperties]
class LocationData {
	public static $tablename = "location";

	public $id = 0;
	public $name = "";
	public $longitud = "";
	public $latitud = "";

	public function add(){
		$name = addslashes($this->name);
		$sql = "insert into ".self::$tablename." (name) value (\"$name\")";
		Executor::doit($sql);
	}
	
    public static function delById($id){
		$id = intval($id);
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}

	public function del(){
		$id = intval($this->id);
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}

	public function update(){
		$id = intval($this->id);
		$name = addslashes($this->name);
		$sql = "update ".self::$tablename." set name=\"$name\" where id=$id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$id = intval($id);
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0], new LocationData());
	}

	public static function getAll(){
		$sql = "select * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0], new LocationData());
	}

	public static function getAllBySQL2($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0], new LocationData());
	}

	public static function getAllBySQL($sqlextra){
		$sql = "select * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0], new LocationData());
	}

	public static function getLike($q){
		$q = addslashes($q);
		$sql = "select * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0], new LocationData());
	}
}

?>