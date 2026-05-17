<?php
class PayrollData {
	public static $tablename = "payroll";
	
	
	public function getUser(){ return UserData::getById($this->idemployee);}
	
		public function __construct(){
		
		$this->created_at = "NOW()";
	}


	public function add(){
		$sql = "insert into payroll (idemployee,amount,pay_day,user_id,created_at)";
		$sql .= "value (\"$this->idemployee\",\"$this->amount\",\"$this->pay_day\",\"$this->user_id\",$this->created_at)";
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

// partiendo de que ya tenemos creado un objecto PayrollData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set idemployee=\"$this->idemployee\", amount=\"$this->amount\", pay_day=\"$this->pay_day\" where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PayrollData());
	}


	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PayrollData());
	}


	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new PayrollData());
	}


	public static function getLike($q){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PayrollData());
	}
	
	
	

	


}

?>