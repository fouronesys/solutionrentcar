<?php
class OilData {
	public static $tablename = "oil";

	public function __construct(){
		$this->name = "";
		$this->created_at = "NOW()";
	}


	public function getCars(){ return CarsData::getById($this->car_id);}
	public function getStock(){ return StockData::getById($this->stock_id);}
	public function getUser(){ return UserData::getById($this->user_id);}

	public function add(){
		$sql = "insert into ".self::$tablename." (car_id,kms,user_id,total,f_id,stock_id,created_at) ";
		$sql .= "value (\"$this->car_id\",\"$this->kms\",\"$this->user_id\",\"$this->total\",\"$this->f_id\",\"$this->stock_id\",\"$this->created_att\")";
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

// partiendo de que ya tenemos creado un objecto OilData previamente utilizamos el contexto
public function update(){
		$sql = "update ".self::$tablename." set kms=\"$this->kms\",total=\"$this->total\",car_id=\"$this->car_id\",user_id=\"$this->user_id\",stock_id=\"$this->stock_id\",created_at=\"$this->created_at\" where id=$this->id";
		Executor::doit($sql);
	}
	

	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new OilData());
	}



	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new OilData());
	}


	public static function getLike($q){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OilData());
	}

		public static function getGroupByDateOp($start,$end,$stock){
  $sql = "select id,sum(total) as t,count(*) as c from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and stock_id=$stock";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OilData());
	}

	public static function getAllUnBoxed(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where box_id=0  order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OilData());
	}

	public function update_box(){
		$sql = "update ".self::$tablename." set box_id=$this->box_id where id=$this->id";
		Executor::doit($sql);
	}

	public static function getByBoxId($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where box_id=$id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OilData());
	}


	public static function getAllByDateOp($start,$end){
	  $sql = "select SQL_BIG_RESULT * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OilData());
	}


	public static function getAllByDateOpByUserId($user,$start,$end){
	  $sql = "select SQL_BIG_RESULT * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and user_id=$user order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OilData());
	}

	public static function getAllByDateBCOp($clientid,$start,$end){
 		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and car_id=$clientid order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OilData());

	}

	public static function getAllByDateBCOpByUserId($user,$clientid,$start,$end){
 		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and car_id=$clientid  and user_id=$user order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OilData());

	}

	public static function getAllBySQL2($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new OilData());
	
	}



}

?>