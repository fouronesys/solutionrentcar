<?php
class DeliveryData {
	public static $tablename = "delivery";
    
    
	public function getUser(){ return UserData::getById($this->user_id);}
	public function getDelivery(){ return UserData::getById($this->delivery_id);}
	public function getReceiver(){ return UserData::getById($this->receiver_id);}

	public function __construct(){
		$this->created_at = "NOW()";
	}


	public function add(){
		$sql = "insert into ".self::$tablename." (danger,firma,method,cat,radio,replacement,antenna,keyring,carpets,belts,roof_lining,mirrors,board,rearview,watches,document,lighter,crystals,cd,bumper,equalizer,cup_holder,plate,seats,logo,batery,top,comment,no_batery,car_id,booking_id,user_id,delivery_id,receiver_id,kms,fuel,random,created_at)";
		$sql .= "value (\"$this->danger\",\"$this->firma\",\"$this->method\",\"$this->cat\",\"$this->radio\",\"$this->replacement\",\"$this->antenna\",\"$this->keyring\",\"$this->carpets\",\"$this->belts\",\"$this->roof_lining\",\"$this->mirrors\",\"$this->board\",\"$this->rearview\",\"$this->watches\",\"$this->document\",\"$this->lighter\",\"$this->crystals\",\"$this->cd\",\"$this->bumper\",\"$this->equalizer\",\"$this->cup_holder\",\"$this->plate\",\"$this->seats\",\"$this->logo\",\"$this->batery\",\"$this->top\",\"$this->comment\",\"$this->no_batery\",\"$this->car_id\",\"$this->booking_id\",\"$this->user_id\",\"$this->delivery_id\",\"$this->receiver_id\",\"$this->kms\",\"$this->fuel\",\"$this->random\",$this->created_at)";
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

	public static function getBySell($op,$th,$sell_id){
	$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where random=$op and method=$th and booking_id=$sell_id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new DeliveryData());
	}


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new DeliveryData());
	}


	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new DeliveryData());
	}


	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new DeliveryData());
	}

	


	public static function getLike($q){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new DeliveryData());
	}

	public static function getMethod($m,$p){
	$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where booking_id=$m and method=$p";
		$query = Executor::doit($sql);
		return Model::many($query[0],new DeliveryData());
	}

	


}

?>