<?php
class UserData {
	public static $tablename = "user";

	public function getStock(){ return StockData::getById($this->stock_id); }
	public function getK(){ return KData::getById($this->kind); }

	public function __construct(){
		$this->name = "";
		$this->lastname = "";
		$this->email = "";
		$this->password = "";
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (phone,language,no,comision,name,lastname,firma,email,image,kind,stock_id,password,status,created_at) ";
		$sql .= "value (\"$this->phone\",\"$this->language\",\"$this->no\",\"$this->comision\",\"$this->name\",\"$this->lastname\",\"$this->firma\",\"$this->email\",\"$this->image\",\"$this->kind\",\"$this->stock_id\",\"$this->password\",\"$this->status\",$this->created_at)";
		Executor::doit($sql);
	}
	
	public function add_employees(){
		$sql = "insert into ".self::$tablename." (no,name,lastname,image,kind,stock_id,created_at) ";
		$sql .= "value (\"$this->no\",\"$this->name\",\"$this->lastname\",\"$this->image\",\"$this->kind\",\"$this->stock_id\",$this->created_at)";
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

// partiendo de que ya tenemos creado un objecto UserData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set  phone=\"$this->phone\",language=\"$this->language\",no=\"$this->no\",comision=\"$this->comision\",firma=\"$this->firma\",name=\"$this->name\",email=\"$this->email\",lastname=\"$this->lastname\",image=\"$this->image\",status=\"$this->status\" where id=$this->id";
		Executor::doit($sql);
	}

	public function profile(){
		$sql = "update ".self::$tablename." set name=\"$this->name\",image=\"$this->image\",lastname=\"$this->lastname\" where id=$this->id";
		Executor::doit($sql);
	}


	public function update_passwd(){
		$sql = "update ".self::$tablename." set password=\"$this->password\" where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new UserData());
	}

	public static function getKind(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where kind=1 ";
		$query = Executor::doit($sql);
		return Model::one($query[0],new UserData());
	}

	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserData());
	}

	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where username!='krtavarez'";		
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserData());
	}

public static function getAllByID(){
		$sql = "select MAX(id) as id from ".self::$tablename." ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserData());
	}


	public static function getLike($q){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserData());
	}
	


}

?>