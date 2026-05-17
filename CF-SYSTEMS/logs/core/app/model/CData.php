<?php
class CData {
	public static $tablename = "c";


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new CData());
	}

   public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CData());
	}

	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new CData());
	}
	
	
		public static function getGroupByDateOp($indicator,$stock){
  $sql = "select id,count(*) as c from ".self::$tablename." where (de > 0 and hasta > 0) and  indicator = \"$indicator\" and stock_id=$stock";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CData());
	}
	
	
// partiendo de que ya tenemos creado un objecto BrandData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set de=\"$this->de\",hasta=\"$this->hasta\",expiration=\"$this->expiration\" where id=$this->id";
		Executor::doit($sql);
	}
	
	
		public function update2(){
		$sql = "update ".self::$tablename." set de=\"$this->de\" where id=$this->id";
		Executor::doit($sql);
	}

	


	





}

?>