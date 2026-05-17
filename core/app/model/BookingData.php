<?php
class BookingData {

    public static $tablename = "booking";

    public int $id = 0;

    public string $firma = "";
    
    public string $notified_clients = "";
    public string $notified = "";
    
     public string $code = "";
     public string $update_time = "";
    
    public string $comment = "";
    public string $payment_day = "";
    public string $type_id = "";
    public string $payment = "";
    public string $start_at = "";
    public string $st2rt_at = "";
    public string $end_at = "";
    public string $e2d_at = "";

    public string $place_start = "No especificado";
    public string $place_end = "No especificado";

    public int $person_id = 0;
    public int $person2_id = 0;

    public int $location = 0;
    public int $stock_id = 0;

    public int $car_id = 0;
    public int $car2_id = 0;

    public float $price = 0;
    public float $total = 0;
    public float $xtotal = 0;

    public string $day = "";

    public int $user_id = 0;

    public string $type = "";

    public float $deposit = 0;
    public string $fuel = "";

    public int $f_id = 0;

    public int $status = 0;

    public string $plane = "";

    public float $sure = 0;
    public int $type_sure = 0;

    public string $unit_extra1 = "";
    public float $price_extra1 = 0;

    public string $unit_extra2 = "";
    public float $price_extra2 = 0;

    public string $unit_extra3 = "";
    public float $price_extra3 = 0;

    public string $unit_extra4 = "";
    public float $price_extra4 = 0;

    public float $iva = 0;

    public string $type_iva = "";
    public string $number_iva = "";
    public float $value_iva = 0;

    public string $card = "";

    public float $usd_price = 0;
    public float $tasa_dolar = 0;

    public int $box_id = 0;

    public string $created_at = "";

	public function getUser(){ return UserData::getById($this->user_id);}
	public function getSure(){ return SureData::getById($this->type_sure);}
	public function getCars(){ return CarsData::getById($this->car_id);}
	public function getStock(){ return StockData::getById($this->stock_id);}
	public function getPerson(){ return PersonData::getById($this->person_id);}
	public function getPerson2(){ return PersonData::getById($this->person2_id);}
	public function getLocation(){ return LocationData::getById($this->location);}
	public function getCars2(){ return CarsData::getById($this->car2_id);}
	public function getF(){ return FData::getById($this->f_id);}

	private function safeString($value, $default = ""){
		$value = trim((string)$value);
		return $value !== "" ? $value : $default;
	}

	private function safeSql($value){
		if(function_exists("mysqli_real_escape_string")){
			$base = new Database();
			$con = $base->connect();
			if($con){ return mysqli_real_escape_string($con, (string)$value); }
		}
		return addslashes((string)$value);
	}

	private function prepareSafeValues(){
		$this->comment = $this->safeString($this->comment);
		$this->payment_day = $this->safeString($this->payment_day);
		$this->type_id = $this->safeString($this->type_id);
		$this->payment = $this->safeString($this->payment, "0");
		$this->start_at = $this->safeString($this->start_at);
		$this->end_at = $this->safeString($this->end_at);
		$this->place_start = $this->safeString($this->place_start, "No especificado");
		$this->place_end = $this->safeString($this->place_end, "No especificado");
		$this->day = $this->safeString($this->day, "0");
		$this->type = $this->safeString($this->type, "1");
		$this->fuel = $this->safeString($this->fuel);
		$this->plane = $this->safeString($this->plane, "0");
		$this->unit_extra1 = $this->safeString($this->unit_extra1, "0");
		$this->unit_extra2 = $this->safeString($this->unit_extra2, "0");
		$this->unit_extra3 = $this->safeString($this->unit_extra3, "0");
		$this->unit_extra4 = $this->safeString($this->unit_extra4, "0");
		$this->type_iva = $this->safeString($this->type_iva, "0");
		$this->number_iva = $this->safeString($this->number_iva);
		$this->card = $this->safeString($this->card, "0");
	}

	public function add(){
		$this->prepareSafeValues();
		$sql = "insert into ".self::$tablename." (comment,payment_day,type_id,payment,start_at,end_at,place_start,place_end,person2_id,person_id,location,stock_id,car_id,car2_id,price,total,xtotal,day,user_id,type,deposit,fuel,f_id,status,plane,sure,type_sure,unit_extra1,price_extra1,unit_extra2,price_extra2,unit_extra3,price_extra3,unit_extra4,price_extra4,iva,type_iva,number_iva,value_iva,card,usd_price,tasa_dolar,created_at) ";
		$sql .= "value (\"$this->comment\",\"$this->payment_day\",\"$this->type_id\",\"$this->payment\",\"$this->start_at\",\"$this->end_at\",\"$this->place_start\",\"$this->place_end\",\"$this->person2_id\",\"$this->person_id\",\"$this->location\",\"$this->stock_id\",\"$this->car_id\",\"$this->car2_id\",\"$this->price\",\"$this->total\",\"$this->xtotal\",\"$this->day\",\"$this->user_id\",\"$this->type\",\"$this->deposit\",\"$this->fuel\",\"$this->f_id\",\"$this->status\",\"$this->plane\",\"$this->sure\",\"$this->type_sure\",\"$this->unit_extra1\",\"$this->price_extra1\",\"$this->unit_extra2\",\"$this->price_extra2\",\"$this->unit_extra3\",\"$this->price_extra3\",\"$this->unit_extra4\",\"$this->price_extra4\",\"$this->iva\",\"$this->type_iva\",\"$this->number_iva\",\"$this->value_iva\",\"$this->card\",\"$this->usd_price\",\"$this->tasa_dolar\",NOW())";
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

// partiendo de que ya tenemos creado un objecto BookingData previamente utilizamos el contexto
		public function update(){
		$this->prepareSafeValues();
		$sql = "update ".self::$tablename." set person_id=\"$this->person_id\",person2_id=\"$this->person2_id\",start_at=\"$this->start_at\",end_at=\"$this->end_at\",type_sure=\"$this->type_sure\",sure=\"$this->sure\",f_id=\"$this->f_id\",fuel=\"$this->fuel\",place_start=\"$this->place_start\",place_end=\"$this->place_end\",card=\"$this->card\",car_id=\"$this->car_id\",stock_id=\"$this->stock_id\",price=\"$this->price\",total=\"$this->total\",xtotal=\"$this->xtotal\",payment=\"$this->payment\",day=\"$this->day\" where id=$this->id";
		Executor::doit($sql);
	}
	
		public function update2(){
		$this->prepareSafeValues();
		$sql = "update ".self::$tablename." set place_start=\"$this->place_start\",place_end=\"$this->place_end\",start_at=\"$this->start_at\",end_at=\"$this->end_at\",car_id=\"$this->car_id\",fuel=\"$this->fuel\",stock_id=\"$this->stock_id\",total=\"$this->total\",price=\"$this->price\",day=\"$this->day\" where id=$this->id";
		Executor::doit($sql);
	}
	
	
	public function update_firma(){
		$this->prepareSafeValues();
		$sql = "update ".self::$tablename." set firma=\"$this->firma\" where id=$this->id";
		Executor::doit($sql);
	}

	public function upd_random(){
		$this->prepareSafeValues();
		$sql = "update ".self::$tablename." set start_at=\"$this->start_at\",end_at=\"$this->end_at\",e2d_at=\"$this->e2d_at\",total=\"$this->total\",price=\"$this->price\",day=\"$this->day\" where id=$this->id";
		Executor::doit($sql);
	}
	
	
	public function upd_random2(){
		$this->prepareSafeValues();
		$sql = "update ".self::$tablename." set place_start=\"$this->place_start\",place_end=\"$this->place_end\",car2_id=\"$this->car2_id\" where id=$this->id";
		Executor::doit($sql);
	}
    
    	public function upd_start(){
		$this->prepareSafeValues();
		$sql = "update ".self::$tablename." set st2rt_at=\"$this->st2rt_at\" where id=$this->id";
		Executor::doit($sql);
	}
	
		public function upd_payment(){
		$this->prepareSafeValues();
		$sql = "update ".self::$tablename." set payment=\"$this->payment\" where id=$this->id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new BookingData());
	}
	

	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());

	}

	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}

	// partiendo de que ya tenemos creado un objecto CarsData previamente utilizamos el contexto
	public function update_status(){
		$this->prepareSafeValues();
		$sql = "update ".self::$tablename." set status=\"$this->status\" where id=$this->id";
		Executor::doit($sql);
	}
	

public function upd_end(){
		$this->prepareSafeValues();
		$sql = "update ".self::$tablename." set end_at=\"$this->end_at\" where id=$this->id";
		Executor::doit($sql);
	}

	public function delivery_booking(){
		$this->prepareSafeValues();
		$sql = "update ".self::$tablename." set status=\"$this->status\", car_id=\"$this->car_id\", fuel=\"$this->fuel\" where id=$this->id";
		Executor::doit($sql);
	}

	public static function getGroupByDateOp($start, $end, $stock){
    $start = addslashes($start);
    $end   = addslashes($end);
    $stock = intval($stock);

    $sql = "
        SELECT 
            SUM(total) AS t,
            COUNT(*) AS c
        FROM ".self::$tablename."
        WHERE created_at >= '$start 00:00:00'
        AND created_at <= '$end 23:59:59'
        AND stock_id = $stock
    ";

    $query = Executor::doit($sql);
    return Model::many($query[0], new BookingData());
}
	
	
	public static function getGroupByDateIncomeOp($start,$end,$stock){
  $sql = "select id,sum(total) as t,count(*) as c from ".self::$tablename." where date(start_at) >= \"$start\" and date(start_at) <= \"$end\" and stock_id=$stock";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}


	public static function getGroupByDateDp($start, $end, $stock){
    $start = addslashes($start);
    $end   = addslashes($end);
    $stock = intval($stock);

    $sql = "
        SELECT 
            COALESCE(SUM(sure), 0) AS t,
            COUNT(*) AS c
        FROM ".self::$tablename."
        WHERE DATE(created_at) >= \"$start\"
          AND DATE(created_at) <= \"$end\"
          AND stock_id = $stock
          AND status IN (1,3)
    ";

    $query = Executor::doit($sql);

    if(!$query || !isset($query[0])){
        return array();
    }

    return Model::many($query[0], new BookingData());
}
	
		public static function getGroupByCount($stock){
  $sql = "select id,sum(sure) as t,count(*) as c from ".self::$tablename." where stock_id=$stock";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}
    
    	public static function getGroupByStatus($stock,$op){
  $sql = "select id,count(*) as c from ".self::$tablename." where stock_id=$stock and status=$op";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}


	public static function getSellsUnBoxed($stock){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where box_id=0 and (status=3 || status=1) and stock_id=$stock order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}

	public function update_box(){
		$this->prepareSafeValues();
		$sql = "update ".self::$tablename." set box_id=$this->box_id where id=$this->id";
		Executor::doit($sql);
	}

	public static function getByBoxId($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where box_id=$id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}

	public static function get10Popular($stock,$start,$end){
 $sql = "select SQL_BIG_RESULT *,count(car_id) as total from ".self::$tablename." where (status=3 || status=1) and stock_id=\"$stock\" and (date(created_at) >= \"$start\" and date(created_at) <= \"$end\")  group by car_id order by total desc limit 4";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}


	public static function getSQL($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}

		public static function getAllByID(){
		$sql = "select MAX(id) as id from ".self::$tablename." ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}

	public static function getAllByDateOp($start,$end,$stock){
	  $sql = "select SQL_BIG_RESULT * from ".self::$tablename." where stock_id= \"$stock\" and date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and (status=3 || status=1) order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}
	
	public static function getAllByDateOpByUserId($user,$start,$end,$stock){
	    $sql = "select SQL_BIG_RESULT * from ".self::$tablename." where stock_id= \"$stock\" and user_id=\"$user\" and date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and (status=3 || status=1) order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}
	
		public static function getAllByDateBCOp($cars,$start,$end,$stock){
 	  $sql = "select SQL_BIG_RESULT * from ".self::$tablename." where stock_id= \"$stock\" and car_id=\"$cars\" and date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and (status=3 || status=1) order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
		}
		
	public static function getAllByDateOp2($start,$end,$stock){
	  $sql = "select SQL_BIG_RESULT * from ".self::$tablename." where stock_id= \"$stock\" and date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and type_iva<>''  order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}
	
		public static function getAllByDateIva($start,$end,$stock,$iva){
	  $sql = "select SQL_BIG_RESULT * from ".self::$tablename." where stock_id= \"$stock\" and date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and type_iva=\"$iva\"  order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}

	



	public static function getAllByDateBCOpByUserId($user,$clientid,$start,$end,$stock){
 		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where stock_id= \"$stock\" and date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and person_id=$clientid  and (status=3 || status=1)  and user_id=$user order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());

	}

		public static function getCreditByClientId($id,$stock){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where person_id=\"$id\" and stock_id=\"$stock\"  order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}
    
    	public static function getCreditByCarsId($id,$stock){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where person_id=$id and stock_id=$stock group by car_id  order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	}

		public static function getAllBySQL2($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new BookingData());
	
	}
	
	public function update_process(){
		$this->prepareSafeValues();

    $sql = "update ".self::$tablename." set 

    person_id=\"$this->person_id\",
    person2_id=\"$this->person2_id\",
    car_id=\"$this->car_id\",
    start_at=\"$this->start_at\",
    end_at=\"$this->end_at\",
    day=\"$this->day\",
    price=\"$this->price\",
    total=\"$this->total\",
    xtotal=\"$this->xtotal\",
    plane=\"$this->plane\",
    sure=\"$this->sure\",
    deposit=\"$this->deposit\",
    type_sure=\"$this->type_sure\",
    f_id=\"$this->f_id\",
    fuel=\"$this->fuel\",
    place_start=\"$this->place_start\",
    place_end=\"$this->place_end\",
    unit_carseat=\"$this->unit_carseat\",
    price_carseat=\"$this->price_carseat\",
    unit_wifi=\"$this->unit_wifi\",
    price_wifi=\"$this->price_wifi\",
    unit_trailer=\"$this->unit_trailer\",
    price_trailer=\"$this->price_trailer\",
    iva=\"$this->iva\",
    card=\"$this->card\",
    status=\"$this->status\",
    type=1

    where id=$this->id";

    Executor::doit($sql);

}


}

?>