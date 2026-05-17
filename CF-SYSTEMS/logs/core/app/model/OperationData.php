<?php class OperationData {
	public static $tablename = "operation";

	public function getCars(){ return CarsData::getById($this->car_id);}

	public function add(){
    $sql = "insert into ".self::$tablename." (price,car_id,day,cotization_id,created_at) ";
	$sql .= "value (\"$this->price\",\"$this->car_id\",\"$this->day\",\"$this->cotization_id\",NOW())";
		return Executor::doit($sql);
	}

	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OperationData());
	}
	
		public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		Executor::doit($sql);
	}
	
		public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new OperationData());
	}
	
	
}

?>