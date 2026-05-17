<?php
class WaitData {
	public static $tablename = "wait";

	public function getUser(){ return UserData::getById($this->user_id);}
	public function getCars(){ return CarsData::getById($this->car_id);}
	public function getStock(){ return StockData::getById($this->stock_id);}
	public function getPerson(){ return PersonData::getById($this->person_id);}

	public function add(){
		$sql = "insert into ".self::$tablename." (start_at,end_at,person_id,stock_id,price,total,day,car_id,place_start,place_end,created_at) ";
		$sql .= "value (\"$this->start_at\",\"$this->end_at\",\"$this->person_id\",\"$this->stock_id\",\"$this->price\",\"$this->total\",\"$this->day\",\"$this->car_id\",\"$this->place_start\",\"$this->place_end\",NOW())";
		return Executor::doit($sql);
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
		return Model::one($query[0],new WaitData());
	}
	

	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new WaitData());

	}

	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new WaitData());
	}


	public static function getSQL($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new WaitData());
	}

		public static function getAllByID(){
		$sql = "select MAX(id) as id from ".self::$tablename." ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new WaitData());
	}


		public static function getAllBySQL2($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new WaitData());
	
	}


}

?>