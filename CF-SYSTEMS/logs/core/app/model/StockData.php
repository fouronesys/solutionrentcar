<?php
class StockData {
	public static $tablename = "stock";

	
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
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}
	public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		Executor::doit($sql);
	}

		public static function unset_principal(){
		$sql = "update ".self::$tablename." set is_principal=0";
		Executor::doit($sql);
	}
		public static function set_principal($id){
		$sql = "update ".self::$tablename." set is_principal=1 where id=$id";
		Executor::doit($sql);
	}

// partiendo de que ya tenemos creado un objecto StockData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set method=\"$this->method\",notario=\"$this->notario\",card=\"$this->card\",email=\"$this->email\",currency=\"$this->currency\",location=\"$this->location\",ticket_image=\"$this->ticket_image\",web_img=\"$this->web_img\",type_img=\"$this->type_img\",field1=\"$this->field1\",field2=\"$this->field2\",name=\"$this->name\",address=\"$this->address\",rnc=\"$this->rnc\",phone=\"$this->phone\",phone2=\"$this->phone2\",imp_name=\"$this->imp_name\",imp_val=\"$this->imp_val\",frame=\"$this->frame\",color=\"$this->color\" where id=$this->id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new StockData());
	}

	public static function getPrincipal(){
			$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=".Core::$user->stock_id;
			$query = Executor::doit($sql);
			return Model::one($query[0],new StockData());
	}
	
		public static function getFPrincipal($id){
			$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
			$query = Executor::doit($sql);
			return Model::one($query[0],new StockData());
	}



	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new StockData());
	}


	public static function getLike($q){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new StockData());
	}


	public static function getSQL($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new StockData());
	}

	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new StockData());
	}

	public static function getAllBySQL2($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new StockData());
	}
	
		public static function getAllByID(){
		$sql = "select MAX(id) as id from ".self::$tablename." ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new StockData());
	}



}

?>