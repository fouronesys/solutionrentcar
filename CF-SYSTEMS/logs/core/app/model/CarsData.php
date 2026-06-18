<?php
class CarsData {
	public static $tablename = "cars";

	public function getStock(){ return StockData::getById($this->stock_id);}
	public function getBrand(){ return BrandData::getById($this->brand_id);}
	public function getFuel(){ return FuelData::getById($this->fuel_id);}
	public function getTransmission(){ return TransmissionData::getById($this->transmission_id);}
	public function getCategory(){ return CategoryData::getById($this->category_id);}
	public function getInColor(){ return ColorData::getById($this->interior_id);}
	public function getExColor(){ return ColorData::getById($this->exterior_id);}
	public function getInsurance(){ return InsuranceData::getById($this->insurance_id);}
	public function getInsurance2(){ return InsuranceData::getById($this->insurance2_id);}

		public function add(){
		$sql = "insert into ".self::$tablename." (provider_id,provider_price,charge_kms,token,name,year,stock_id,user_id,brand_id,category_id,insurance_id,insurance2_id,interior_id,exterior_id,invoice_file,plate,date_insurance,date2_insurance,insurance_file,insurance2_file,kms_current,tuition,chassis,seat,price,transmission_id,fuel_id,created_at) ";
		$sql .= "value (\"$this->provider_id\",\"$this->provider_price\",\"$this->charge_kms\",\"$this->token\",\"$this->name\",\"$this->year\",\"$this->stock_id\",\"$this->user_id\",\"$this->brand_id\",\"$this->category_id\",\"$this->insurance_id\",\"$this->insurance2_id\",\"$this->interior_id\",\"$this->exterior_id\",\"$this->invoice_file\",\"$this->plate\",\"$this->date_insurance\",\"$this->date2_insurance\",\"$this->insurance_file\",\"$this->insurance2_file\",\"$this->kms_current\",\"$this->tuition\",\"$this->chassis\",\"$this->seat\",\"$this->price\",\"$this->transmission_id\",\"$this->fuel_id\",NOW())";
		return Executor::doit($sql);
	}
	
	public function add_ext(){
		$sql = "insert into ".self::$tablename." (provider_id,provider_price,price,brand_id,category_id,name,year,plate,chassis,status,stock_id,user_id,created_at) ";
		$sql .= "value (\"$this->provider_id\",\"$this->provider_price\",\"$this->price\",\"$this->brand_id\",\"$this->category_id\",\"$this->name\",\"$this->year\",\"$this->plate\",\"$this->chassis\",\"$this->status\",\"$this->stock_id\",\"$this->user_id\",NOW())";
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

// partiendo de que ya tenemos creado un objecto CarsData previamente utilizamos el contexto
	public function update_status(){
		$sql = "update ".self::$tablename." set status=\"$this->status\" where id=$this->id";
		Executor::doit($sql);
	}


// partiendo de que ya tenemos creado un objecto CarsData previamente utilizamos el contexto
	public function update_device(){
		$sql = "update ".self::$tablename." set gps_id=\"$this->gps_id\" where id=$this->id";
		Executor::doit($sql);
	}

	public function update_kms(){
		$sql = "update ".self::$tablename." set kms=\"$this->kms\",status=\"$this->status\" where id=$this->id";
		Executor::doit($sql);
	}

	public function update_ksc(){
		$sql = "update ".self::$tablename." set kms_current=\"$this->kms\" where id=$this->id";
		Executor::doit($sql);
	}

	public function update_stock(){
		$sql = "update ".self::$tablename." set stock_id=\"$this->stock_id\" where id=$this->id";
		Executor::doit($sql);
	}

	public function update(){
		$sql = "update ".self::$tablename." set no_batery=\"$this->no_batery\",tuition=\"$this->tuition\",chassis=\"$this->chassis\",charge_kms=\"$this->charge_kms\",token=\"$this->token\",kms_current=\"$this->kms_current\",stock_id=\"$this->stock_id\",name=\"$this->name\",year=\"$this->year\",brand_id=\"$this->brand_id\",category_id=\"$this->category_id\",insurance_id=\"$this->insurance_id\",insurance2_id=\"$this->insurance2_id\",interior_id=\"$this->interior_id\",exterior_id=\"$this->exterior_id\",invoice_file=\"$this->invoice_file\",plate=\"$this->plate\",date_insurance=\"$this->date_insurance\",insurance_file=\"$this->insurance_file\",date2_insurance=\"$this->date2_insurance\",insurance2_file=\"$this->insurance2_file\",price=\"$this->price\",seat=\"$this->seat\",transmission_id=\"$this->transmission_id\",fuel_id=\"$this->fuel_id\" where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new CarsData());
	}

			public static function getAllnoCat(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where category_id=0";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CarsData());
	}



	public static function getAllByCategoryId2($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where category_id=$id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CarsData());
	}




	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new CarsData());
	}


	public static function getLike($p,$stock){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where (name like '%$p%' or year like '%$p%') and stock_id=$stock";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CarsData());
	}


		public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CarsData());
	}
	
	public static function getSQL($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new CarsData());
	}
	
	
	public static function getAllByID(){
		$sql = "select MAX(id) as id from ".self::$tablename." ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CarsData());
	}


}

?>