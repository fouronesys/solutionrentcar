<?php

#[AllowDynamicProperties]
class StockData {

	public static $tablename = "stock";

	public $id;
	public $code;
	public $location;
	public $field1;
	public $field2;
	public $name;
	public $address;
	public $phone;
	public $phone2;
	public $email;
	public $ticket_image;
	public $ticket_image2;
	public $web_img;
	public $type_img;
	public $imp_val;
	public $imp_name;
	public $is_ext;
	public $is_principal;
	public $method;
	public $notario;
	public $no_notario;
	public $card;
	public $currency;
	public $rnc;
	public $frame;
	public $color;
	public $witness1;
	public $witness2;
	public $no_witness1;
	public $no_witness2;

	public function add(){
		$sql = "insert into ".self::$tablename." (location,field1,field2,name,address,phone2,phone) ";
		$sql .= "value (\"$this->location\",\"$this->field1\",\"$this->field2\",\"$this->name\",\"$this->address\",\"$this->phone2\",\"$this->phone\")";
		Executor::doit($sql);
	}

	public function add_ext(){
		$sql = "insert into ".self::$tablename." (location,name,imp_val,is_ext) ";
		$sql .= "value (\"$this->location\",\"$this->name\",\"$this->imp_val\",1)";
		Executor::doit($sql);
	}

	public static function delById($id){
		$id = intval($id);
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}

	public function del(){
		$id = intval($this->id);
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}

	public static function unset_principal(){
		$sql = "update ".self::$tablename." set is_principal=0";
		Executor::doit($sql);
	}

	public static function set_principal($id){
		$id = intval($id);
		$sql = "update ".self::$tablename." set is_principal=1 where id=$id";
		Executor::doit($sql);
	}

	public function update(){
		$id = intval($this->id);

		$sql = "update ".self::$tablename." set 
			method=\"$this->method\",
			notario=\"$this->notario\",
			no_notario=\"$this->no_notario\",
			card=\"$this->card\",
			email=\"$this->email\",
			currency=\"$this->currency\",
			location=\"$this->location\",
			ticket_image=\"$this->ticket_image\",
			web_img=\"$this->web_img\",
			type_img=\"$this->type_img\",
			field1=\"$this->field1\",
			field2=\"$this->field2\",
			name=\"$this->name\",
			address=\"$this->address\",
			rnc=\"$this->rnc\",
			phone=\"$this->phone\",
			phone2=\"$this->phone2\",
			imp_name=\"$this->imp_name\",
			imp_val=\"$this->imp_val\",
			frame=\"$this->frame\",
			color=\"$this->color\",
			witness1=\"$this->witness1\",
			witness2=\"$this->witness2\",
			no_witness1=\"$this->no_witness1\",
			no_witness2=\"$this->no_witness2\"
			where id=$id";

		Executor::doit($sql);
	}

	public static function getById($id){
		$id = intval($id);
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0], new StockData());
	}

	public static function getPrincipal(){
		$stock_id = 0;

		if(isset(Core::$user) && isset(Core::$user->stock_id)){
			$stock_id = intval(Core::$user->stock_id);
		}

		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$stock_id";
		$query = Executor::doit($sql);
		return Model::one($query[0], new StockData());
	}

	public static function getFPrincipal($id){
		$id = intval($id);
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0], new StockData());
	}

	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0], new StockData());
	}

	public static function getLike($q){
		$q = addslashes($q);
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0], new StockData());
	}

	public static function getSQL($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0], new StockData());
	}

	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0], new StockData());
	}

	public static function getAllBySQL2($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0], new StockData());
	}

	public static function getAllByID(){
		$sql = "select MAX(id) as id from ".self::$tablename." ";
		$query = Executor::doit($sql);
		return Model::many($query[0], new StockData());
	}
}

?>