<?php
class SpendData {
	public static $tablename = "spend";

	public function __construct(){
		$this->kind=1;
		$this->created_at = "NOW()";
	}

	public function getCategory(){ return CategoryData::getById($this->category_id);}
	public function getPerson(){ return PersonData::getById($this->person_id);}
	public function getTG(){ return TGData::getById($this->type_g);}
	public function getSG(){ return SGData::getById($this->type_sg);}
	public function getP(){ return PData::getById($this->p_id);}
	public function getF(){ return FData::getById($this->f_id);}
	public function getUser(){ return UserData::getById($this->user_id);}

	public function add(){
		$sql = "insert into ".self::$tablename." (person_id,name,voucher_code,created_date,invoice_code,p_id,f_id,expiry_spend,type_g,type_sg,imp_rent,itbis_ret,user_id,kind,price,stock_id,created_at)";
		$sql .= "value (\"$this->person_id\",\"$this->name\",\"$this->voucher_code\",\"$this->created_date\",\"$this->invoice_code\",\"$this->p_id\",\"$this->f_id\",\"$this->expiry_spend\",\"$this->type_g\",\"$this->type_sg\",\"$this->imp_rent\",\"$this->itbis_ret\",\"$this->user_id\",1,\"$this->price\",\"$this->stock_id\",\"$this->created_att\")";
		Executor::doit($sql);
	}
	
	
	public function add_ext(){
		$sql = "insert into ".self::$tablename." (name,p_id,f_id,user_id,kind,price,stock_id,created_at)";
		$sql .= "value (\"$this->name\",\"$this->p_id\",\"$this->f_id\",\"$this->user_id\",2,\"$this->price\",\"$this->stock_id\",\"$this->created_att\")";
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

// partiendo de que ya tenemos creado un objecto CategoryData previamente utilizamos el contexto

	public function update(){
		$sql = "update ".self::$tablename." set person_id=\"$this->person_id\",name=\"$this->name\",voucher_code=\"$this->voucher_code\",created_date=\"$this->created_date\",invoice_code=\"$this->invoice_code\",p_id=\"$this->p_id\",f_id=\"$this->f_id\",expiry_spend=\"$this->expiry_spend\",type_g=\"$this->type_g\",type_sg=\"$this->type_sg\",imp_rent=\"$this->imp_rent\",itbis_ret=\"$this->itbis_ret\",user_id=\"$this->user_id\",price=\"$this->price\",created_at=\"$this->created_att\" where id=$this->id";
		Executor::doit($sql);
	}
	
	public function update_ext(){
		$sql = "update ".self::$tablename." set name=\"$this->name\",f_id=\"$this->f_id\",price=\"$this->price\",created_at=\"$this->created_att\" where id=$this->id";
		Executor::doit($sql);
	}
	
		public function update2(){
		$sql = "update ".self::$tablename." set price=\"$this->price\",total=\"$this->total\",created_pg=NOW() where id=$this->id";
		Executor::doit($sql);
	}

    public static function getCreditsByClientId($id,$stock){
		$sql = "select * from ".self::$tablename." where p_id=4 and person_id=$id and stock_id=$stock  order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}

	public function update_box(){
		$sql = "update ".self::$tablename." set box_id=$this->box_id where id=$this->id";
		Executor::doit($sql);
	}


	public function del_category(){
		$sql = "update ".self::$tablename." set category_id=NULL where id=$this->id";
		Executor::doit($sql);
	}


	public function update_image(){
		$sql = "update ".self::$tablename." set image=\"$this->image\" where id=$this->id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new SpendData());

	}



	public static function getAll(){
		$sql = "select * from ".self::$tablename." order by created_at ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}



	public static function getAllByID(){
		$sql = "select MAX(id) as id from ".self::$tablename." ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}

	public static function getAllUnBoxed(){
		$sql = "select * from ".self::$tablename." where box_id=0 and kind=1 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}

	public static function getUnBoxedByUser($u){
		$sql = "select * from ".self::$tablename." where box_id=0 and user_id=$u order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}

	public static function getDepUnBoxed(){
		$sql = "select * from ".self::$tablename." where box_id=0 and (kind=3) order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}

	public static function getDepBoxedByUser($u){
		$sql = "select * from ".self::$tablename." where box_id=0 and (kind=3) and user_id=$u order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}

	public static function getAllByPage($start_from,$limit){
		$sql = "select * from ".self::$tablename." where id>=$start_from limit $limit";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}

public static function getAllBySQL($sqlextra){
		$sql = "select * from ".self::$tablename." $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}
		public static function getAllBySQL2($sql){
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}

	public static function getLike($p){
		$sql = "select * from ".self::$tablename." where barcode like '%$p%' or name like '%$p%' or id like '%$p%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}



	public static function getAllByUserId($user_id){
		$sql = "select * from ".self::$tablename." where user_id=$user_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}

	public static function getSpendsByBoxId($user_id){
		$sql = "select * from ".self::$tablename." where box_id=$user_id and (kind=1 or kind=2)order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}
		public static function getSpends(){
		$sql = "select * from ".self::$tablename." where kind=1 order by created_at desc LIMIT 1";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}
	
	public static function getDepsByBoxId($user_id){
		$sql = "select * from ".self::$tablename." where box_id=$user_id and (kind=3)order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}
	public static function getAllByCategoryId($category_id){
		$sql = "select * from ".self::$tablename." where category_id=$category_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}

		public static function getGroupByDateOp($start,$end,$kind,$stock){
 		$sql = "select *,sum(price) as t from ".self::$tablename." where stock_id=$stock and date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and kind=$kind";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}

		public static function getGroupByDateOpdev($start,$end,$stock){
 		$sql = "select *,sum(price) as t from ".self::$tablename." where stock_id=$stock and date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and kind=2";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}


		public static function getGroupByDateOp2($start,$end){
 		$sql = "select *,sum(price) as t from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and (kind=3)";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}
	
	    public static function getAllByDateOp2($start,$end,$stock){
	    $sql = "select * from ".self::$tablename." where stock_id=\"$stock\" and date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and voucher_code<>'' order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}
	
	    public static function getAllByDateOpByUserId2($user,$start,$end,$stock){
	    $sql = "select * from ".self::$tablename." where stock_id=\"$stock\" and date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and user_id=$user and voucher_code<>'' order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}
	
	    public static function getAllByDateBCOp2($cid,$start,$end,$stock){
 		$sql = "select * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and type_g=$cid and voucher_code<>'' and stock_id=\"$stock\"  order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());

	}
	
	public static function getAllByDateBCOpByUserId2($user,$cid,$start,$end,$op){
 		$sql = "select * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and type_g=$cid and user_id=$user and voucher_code<>'' order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());

	}
	
		public static function getAllByDateOp($start,$end){
	  $sql = "select * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}

		public static function getAllByDateOpByUserId($user,$start,$end){
	  $sql = "select * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and user_id=$user order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}

		public static function getAllByDateBCOp($clientid,$start,$end){
 		$sql = "select * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and person_id=$clientid  order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());

	}

		public static function getAllByDateBCOpByUserId($user,$clientid,$start,$end){
 		$sql = "select * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and person_id=$clientid  and user_id=$user order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());

	}


	public static function getGroupByDateTk($start,$end,$op){
  $sql = "select id,sum(price) as tot,count(*) as c from ".self::$tablename." where kind=$op and box_id>0 and p_id!=4 and date(created_at) between '$start' and '$end'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}
	
public static function getGroupByDateTPk($start,$end,$op){
  $sql = "select id,sum(price) as tot,count(*) as c from ".self::$tablename." where kind=$op and p_id=4 and box_id>0 and date(created_at) between '$start' and '$end'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}

public static function getGroupByDateTks($box,$op){
  $sql = "select id,sum(price) as tot,count(*) as c from ".self::$tablename." where kind=$op and box_id=$box";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}
	
public static function getGroupByDateTPks($box,$op){
  $sql = "select id,sum(price) as tot,count(*) as c from ".self::$tablename." where kind=$op and p_id=4 and box_id=$box";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SpendData());
	}   

}

?>