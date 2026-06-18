<?php
class TransmissionData {
	public static $tablename = "transmission";

	public function __construct(){

		$this->user_id = "";
		$this->created_at = "NOW()";
		$this->kind=1;

	}

	public function add(){
		$sql = "insert into ".self::$tablename." (name,created_at) ";
		$sql .= "value (\"$this->name\",$this->created_at)";
		Executor::doit($sql);
	}

	public static function delById($id){
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}

	public static function getByBoxId($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where box_id=$id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new TransmissionData());
	}

	public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		Executor::doit($sql);
	}

	
	public static function getTransmissionUnBoxed(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where box_id=0 and (kind=1) order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new TransmissionData());
	}
// partiendo de que ya tenemos creado un objecto CategoryData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set name=\"$this->name\",price=$this->price where id=$this->id";
		Executor::doit($sql);
	}

	public function update_box(){
		$sql = "update ".self::$tablename." set box_id=$this->box_id where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new TransmissionData());

	}


	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." order by created_at ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new TransmissionData());
	}
	
	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new TransmissionData());
	}


		public static function getAllBySQL2($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new TransmissionData());
	}

	public static function getAllUnBoxed(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where box_id=0 and kind=1 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new TransmissionData());
	}

	public static function getUnBoxedByUser($u){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where box_id=0 and user_id=$u order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}


		public static function getGroupByDateOp($start,$end,$stock){
 		$sql = "select SQL_BIG_RESULT *,sum(price) as t from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\"  and stock_id=$stock";
		$query = Executor::doit($sql);
		return Model::many($query[0],new TransmissionData());
	}
	

		public static function getGroupByDateOp2($start,$end){
 		$sql = "select SQL_BIG_RESULT *,sum(price) as t from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and (kind=3)";
		$query = Executor::doit($sql);
		return Model::many($query[0],new TransmissionData());
	}

	public static function getGroupByDateTk($start,$end){
  $sql = "select id,sum(price) as tot,count(*) as c from ".self::$tablename." where box_id>0 and date(created_at) between '$start' and '$end'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new TransmissionData());
	}

	public static function getGroupByDateTks($box){
  $sql = "select id,sum(price) as tot,count(*) as c from ".self::$tablename." where box_id=$box";
		$query = Executor::doit($sql);
		return Model::many($query[0],new TransmissionData());
	}


}

?>