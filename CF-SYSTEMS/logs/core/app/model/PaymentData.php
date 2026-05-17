<?php
class PaymentData {
	public static $tablename = "payment";

	public function __construct(){
		$this->name = "";
	}

	public function getClient(){ return PersonData::getById($this->person_id); }
	public function getPaymentType(){ return PaymentTypeData::getById($this->payment_type_id); }



	public function add(){
		$sql = "insert into ".self::$tablename." (user_id,stock_id,person_id,booking_id,val,is_stock,payment_type_id,created_at) ";
		$sql .= "value (\"$this->user_id\",\"$this->stock_id\",\"$this->person_id\",\"$this->sell_id\",\"$this->val\",\"$this->is_stock\",1,NOW())";
		Executor::doit($sql);
	}


	public function add_payment(){
		$sql = "insert into ".self::$tablename." (stock_id,booking_id,person_id,val,payment_type_id,user_id,is_stock,created_at) ";
		$sql .= "value (\"$this->stock_id\",\"$this->sell_id\",\"$this->person_id\",\"$this->val\",2,\"$this->user_id\",\"$this->is_stock\",NOW())";
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
	
	public function update(){
		$sql = "update ".self::$tablename." set val=$this->val where id=$this->id";
		Executor::doit($sql);
	}


	public function update_box(){
		$sql = "update ".self::$tablename." set box_id=$this->box_id where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		 $sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PaymentData());
	}
	

	public static function getUnBoxed($stock){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where payment_type_id=2 and box_id=0 and stock_id=$stock order by created_at desc and is_stock=0";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());
	}


	public static function getByBoxId($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where payment_type_id=2 and box_id=$id  order by created_at desc and is_stock=0";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());
	}

	public static function getUnBoxedByUser($u,$stock){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where payment_type_id=2 and box_id=0 and user_id=$u and stock_id=$stock and is_stock=0 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());
	}


	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());
	}

	public static function getAllUnBoxed($stock){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where box_id=0 and stock_id=$stock and payment_type_id=2  and is_stock=0 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());
	}


	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where is_stock=0 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());	
	}

	public static function getAllByDate($start,$end,$stock){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where stock_id=\"$stock\" and (date(created_at)>=\"$start\" and date(created_at)<=\"$end\") and payment_type_id=2 and is_stock=0 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());	
	}

	public static function getAllByRDate($start,$end){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where (date(created_at)>=\"$start\" and date(created_at)<=\"$end\") and payment_type_id=2 and operation_type_id=1 and is_stock=0 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());	
	}




public static function sumAllByDate($start,$end){
		$sql = "select abs(sum(val)) as t from ".self::$tablename." where (date(created_at)>=\"$start\" and date(created_at)<=\"$end\") and payment_type_id=2 and is_stock=0 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PaymentData());	
	}

	public static function getAllByDateAndClient($start,$end,$id,$stock){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where stock_id=\"$stock\" and (date(created_at)>=\"$start\" and date(created_at)<=\"$end\")  and payment_type_id=2 and person_id=$id and is_stock=0 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());	
	}

	public static function getAllByDateAndStock($start,$end,$stock){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where stock_id=\"$stock\" and (date(created_at)>=\"$start\" and date(created_at)<=\"$end\")  and payment_type_id=2 and is_stock=0 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());	
	}

	public static function getAllByDateAndRClient($start,$end,$id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where (date(created_at)>=\"$start\" and date(created_at)<=\"$end\")  and payment_type_id=1 and operation_type_id=1 and person_id=$id and is_stock=0 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());	
	}

	public static function getAllByClientId($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where person_id=$id and is_stock=0 order by created_at asc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());	
	}

	public static function getAllByClientId2($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where person_id=$id and is_stock=0  order by created_at desc  LIMIT 1";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());	
	}

	public static function sumByClientId($id){
		$sql = "select SUM(val) as total from ".self::$tablename." where person_id=$id and is_stock=0";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PaymentData());	
	}

	public static function sumByClientId2($id){
		$sql = "select SUM(val) as total from ".self::$tablename." where payment_type_id=2 and person_id=$id and is_stock=0";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PaymentData());	
	}

	public static function sumByClientId3($id){
		$sql = "select SUM(val) as total from ".self::$tablename." where payment_type_id=1 and person_id=$id and is_stock=0";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PaymentData());	
	}

	public static function sumBySellId($id,$stock){
		$sql = "select SUM(val) as total from ".self::$tablename." where booking_id=$id and stock_id=$stock and is_stock=0";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PaymentData());	
	}

	public static function sumBySellId2($id,$stock){
		$sql = "select SUM(val) as total from ".self::$tablename." where booking_id=$id and  stock_id=$stock and is_stock=0";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PaymentData());	
	}
	
		public static function getByPayment($id){
 		$sql = "select SQL_BIG_RESULT *,sum(abs(val)) as t from ".self::$tablename." where payment_type_id=2  and booking_id=$id and is_stock=0";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());
	}


		public static function getGroupByDateOp($start,$end,$stock){
 		$sql = "select SQL_BIG_RESULT *,sum(abs(val)) as t from ".self::$tablename." where payment_type_id=2  and date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and stock_id=$stock and is_stock=0";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());
	}


		public static function getGroupByPersonDateOp($start,$end,$op,$stock,$person){
 		$sql = "select SQL_BIG_RESULT *,sum(abs(val)) as t from ".self::$tablename." where payment_type_id=2 and operation_type_id=$op and date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and stock_id=$stock and person_id=$person and is_stock=0";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());
	}
	
	public static function getClientByPayment($id){
    $sql = "select person_id 
            from ".self::$tablename." 
            where booking_id=$id 
            limit 1";
    $query = Executor::doit($sql);
    return Model::one($query[0], new PaymentData());
}






}

?>