<?php
class TollData {
	public static $tablename = "toll";

	public function getCars(){ return CarsData::getById($this->car_id);}
	public function getStock(){ return StockData::getById($this->stock_id);}
	public function getUser(){ return UserData::getById($this->user_id);}


	public function add(){
		$sql = "insert into ".self::$tablename." (car_id,user_id,total,f_id,stock_id,created_at)";
		$sql .= "value (\"$this->car_id\",\"$this->user_id\",\"$this->total\",\"$this->f_id\",\"$this->stock_id\",\"$this->created_att\")";
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

// partiendo de que ya tenemos creado un objecto TollData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set name=\"$this->name\" where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new TollData());
	}


	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new TollData());
	}


	public static function getLike($q){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new TollData());
	}

	public static function getAllBySQL2($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new TollData());
	
	}

		public static function getGroupByDateOp($start,$end,$stock){
  $sql = "select id,sum(total) as t,count(*) as c from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and stock_id=$stock";
		$query = Executor::doit($sql);
		return Model::many($query[0],new TollData());	
	}


	public static function getSQL($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new TollData());
	}

	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new TollData());
	}




}

?>