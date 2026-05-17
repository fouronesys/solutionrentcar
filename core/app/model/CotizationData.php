<?php class CotizationData {
	public static $tablename = "cotization";

	public function getPerson(){ return PersonData::getById($this->person_id);}
	public function getUser(){ return UserData::getById($this->user_id);}
	public function getStock(){ return StockData::getById($this->stock_id);}

	public function add(){
		$sql = "insert into ".self::$tablename." (person_id,iva,stock_id,total,user_id,created_at) ";
		$sql .= "value (\"$this->person_id\",\"$this->iva\",\"$this->stock_id\",\"$this->total\",\"$this->user_id\",NOW())";
		return Executor::doit($sql);
	}

	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." order by created_at desc ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CotizationData());
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
		return Model::one($query[0],new CotizationData());
	}

	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CotizationData());
	}

	public static function getLike($q){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CotizationData());
	}

	


}



?>