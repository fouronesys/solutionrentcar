<?php
#[AllowDynamicProperties]
class PersonData {

    public static $tablename = "person";

    /* ================= PROPIEDADES PHP 8.4 ================= */

    public int $id = 0;

    public string $invoice_date = "";
    public string $passport_date = "";
    public string $license_date = "";
    public string $home_date = "";
    
     public string $license2 = "";

    public string $language = "";
    public mixed $birthday = "";
   public mixed $gender = "";

    public string $rnc = "";
    public string $estado = "";

    public string $username = "";
    public string $password = "";

    public string $location = "";
    public string $latitud = "";
    public string $longitud = "";

    public string $reference = "";

    public string $invoice_file = "";
    public string $passport_file = "";
    public string $license_file = "";
    public string $home_file = "";

    public string $name = "";
    public string $lastname = "";

    public string $address = "";
    public string $address2 = "";

    public string $phone = "";
    public string $phone2 = "";

    public string $phone1 = "";

    public string $email = "";
    public string $email1 = "";

    public string $no = "";

    public string $nationality = "";
    public string $passport = "";
    public string $expirelicense = "";
    public string $issuedlicense = "";
    public string $license = "";

    public int $user_id = 0;
    public int $stock_id = 0;

    public string $created_at = "";

    public int $is_rental = 0;

    /* ================= SUPPLIER ================= */

    public int $sup_id = 0;

    public string $code_name = "";
    public string $sup_name = "";
    public string $sup_address = "";
    public string $sup_email = "";
    public string $sup_mobile = "";

    public string $gtin = "";

    public int $type_id = 0;

    public string $is_id = "";
    public string $is_type = "";
    public string $address1 = "";

  public function __construct(){
		$this->created_at = "NOW()";
	}


	public function add(){
		$sql = "insert into ".self::$tablename." (invoice_date,passport_date,license_date,home_date,language,birthday,gender,rnc,username,password,location,latitud,longitud,reference,invoice_file,passport_file,license_file,home_file,name,address,address2,phone2,phone,no,nationality,passport,expirelicense,issuedlicense,email,license,user_id,stock_id,created_at) ";
		$sql .= "value (\"$this->invoice_date\",\"$this->passport_date\",\"$this->license_date\",\"$this->home_date\",\"$this->language\",\"$this->birthday\",\"$this->gender\",\"$this->rnc\",\"$this->username\",\"$this->password\",\"$this->location\",\"$this->latitud\",\"$this->longitud\",\"$this->reference\",\"$this->invoice_file\",\"$this->passport_file\",\"$this->license_file\",\"$this->home_file\",\"$this->name\",\"$this->address\",\"$this->address2\",\"$this->phone2\",\"$this->phone\",\"$this->no\",\"$this->nationality\",\"$this->passport\",\"$this->expirelicense\",\"$this->issuedlicense\",\"$this->email\",\"$this->license\",\"$this->user_id\",\"$this->stock_id\",$this->created_at)";
		Executor::doit($sql);
	}
	
		public function add_ext(){
		$sql = "insert into ".self::$tablename." (reference,name,address,phone,no,stock_id,is_rental,created_at) ";
		$sql .= "value (\"$this->reference\",\"$this->name\",\"$this->address\",\"$this->phone\",\"$this->no\",\"$this->stock_id\",\"$this->is_rental\",$this->created_at)";
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

// partiendo de que ya tenemos creado un objecto PersonData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set invoice_date=\"$this->invoice_date\",passport_date=\"$this->passport_date\",license_date=\"$this->license_date\",home_date=\"$this->home_date\",language=\"$this->language\",birthday=\"$this->birthday\",gender=\"$this->gender\",rnc=\"$this->rnc\",location=\"$this->location\",longitud=\"$this->longitud\",latitud=\"$this->latitud\",invoice_file=\"$this->invoice_file\",passport_file=\"$this->passport_file\",license_file=\"$this->license_file\",home_file=\"$this->home_file\",reference=\"$this->reference\",name=\"$this->name\",phone2=\"$this->phone2\",address=\"$this->address\",phone=\"$this->phone\",no=\"$this->no\",email=\"$this->email\",nationality=\"$this->nationality\",passport=\"$this->passport\",license=\"$this->license\" where id=$this->id";
		Executor::doit($sql);
	}
	
	public function update_username(){
		$sql = "update ".self::$tablename." set username=\"$this->username\",password=\"$this->password\" where id=$this->id";
		Executor::doit($sql);
	}

    
	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PersonData());
	}


	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new PersonData());

	}


	public static function getLike($q){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PersonData());

	}

	public static function getSQL($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new PersonData());
	}

	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PersonData());
	}

	public static function getAllBySQL2($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new PersonData());
	}

   
   public static function getAllByID(){
		$sql = "select MAX(id) as id from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new PersonData());
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function add_provider(){
		$sql = "insert into supplier (code_name,sup_name,sup_address,sup_email,sup_mobile,gtin,type_id,created_at) ";
		$sql .= "value (\"$this->code_name\",\"$this->sup_name\",\"$this->sup_address\",\"$this->sup_email\",\"$this->sup_mobile\",\"$this->gtin\",\"$this->type_id\",$this->created_at)";
		Executor::doit($sql);
	}
	
	
	public static function getByIdProviders($id){
		$sql = "select SQL_BIG_RESULT * from supplier where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PersonData());
	}


	public static function getProviders(){
		$sql = "select SQL_BIG_RESULT * from supplier";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PersonData());

	}
	
	public static function getAllBySQLProviders($sqlextra){
		$sql = "select SQL_BIG_RESULT * from supplier $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PersonData());
	}
	
	
	public static function getAllBySQL4($sqlextra){
		$sql = "select SQL_BIG_RESULT * from supplier_to_store $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PersonData());
	}
  
  
	public function delSup(){
		$sql = "DELETE FROM supplier WHERE sup_id=$this->sup_id";
		Executor::doit($sql);
	}
	
	
	public function update_provider(){
		$sql = "update supplier set no=\"$this->no\",is_id=\"$this->is_id\",is_type=\"$this->is_type\",name=\"$this->name\",email1=\"$this->email1\",address1=\"$this->address1\",lastname=\"$this->lastname\",phone1=\"$this->phone1\" where id=$this->id";
		Executor::doit($sql);
	}
	
	public static function getBySupId($id){
		$sql = "select SQL_BIG_RESULT * from supplier where sup_id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PersonData());
	}

	


}

?>