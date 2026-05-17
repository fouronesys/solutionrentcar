<?php
class TariffData {
	public static $tablename = "tariff";

	public function __construct(){
		$this->created_at = "NOW()";
	}


	public function getPackage(){ return PackageData::getById($this->package_id);}
	public function getCars(){ return CarsData::getById($this->brand_id);}

	public function add(){
		$sql = "insert into tariff (package_id,brand_id,price,description,stock_id) ";
		$sql .= "value (\"$this->package_id\",\"$this->brand_id\",\"$this->price\",\"$this->description\",\"$this->stock_id\")";
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

// partiendo de que ya tenemos creado un objecto TariffData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set package_id=\"$this->package_id\",brand_id=\"$this->brand_id\",price=\"$this->price\" where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new TariffData());
	}


	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new TariffData());
	}


	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new TariffData());
	}

	


	public static function getLike($q){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new TariffData());
	}

	


}

?>