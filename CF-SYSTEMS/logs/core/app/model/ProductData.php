<?php
class ProductData {
    
    public function getStock(){ return StockData::getById($this->stock_id);}
    
	public function add(){
		$sql = "insert into products (p_type,p_code,hsn_code,p_name,p_color,p_model,p_serial,category_id,created_by)";
		$sql .= "value (\"$this->p_type\",\"$this->p_code\",\"$this->hsn_code\",\"$this->p_name\",\"$this->p_color\",\"$this->p_model\",\"$this->p_serial\",\"$this->category_id\",\"$this->created_by\")";
		Executor::doit($sql);
	}
	
	public static function getByBarcode($id){
		$sql = "select SQL_BIG_RESULT * from products where hsn_code=\"$id\"";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ProductData());

	}

	 public function update(){
		$sql = "update products set p_type=\"$this->p_type\",p_code=\"$this->p_code\",hsn_code=\"$this->hsn_code\",p_name=\"$this->p_name\",p_color=\"$this->p_color\",p_serial=\"$this->p_serial\",p_date=\"$this->p_date\",p_model=\"$this->p_model\",category_id=\"$this->category_id\" where p_id=$this->p_id";
		Executor::doit($sql);
	}
	
	public function add_to_store(){
		$sql = "insert into product_to_store (product_id,store_id,brand_id,purchase_price,usd_price,quantity_in_stock,alert_quantity,sup_id,p_date,tasa_dolar,preference)";
		$sql .= "value (\"$this->product_id,\",\"$this->store_id\",\"$this->brand_id\",\"$this->purchase_price\",\"$this->usd_price\",\"$this->quantity_in_stock\",\"$this->alert_quantity\",\"$this->sup_id\",\"$this->p_date\",\"$this->tasa_dolar\",\"$this->preference\")";
		Executor::doit($sql);
	}
	
	 
	 public function upd_to_store(){
		$sql = "update product_to_store set store_id=\"$this->store_id\",brand_id=\"$this->brand_id\",purchase_price=\"$this->purchase_price\",usd_price=\"$this->usd_price\",quantity_in_stock=\"$this->quantity_in_stock\",alert_quantity=\"$this->alert_quantity\",p_date=\"$this->p_date\",sup_id=\"$this->sup_id\",tasa_dolar=\"$this->tasa_dolar\",status=\"$this->status\" where id=$this->id";
		Executor::doit($sql);
	}
	
	 public function upd_Qt(){
		$sql = "update product_to_store set quantity_in_stock=\"$this->quantity_in_stock\" where id=$this->id";
		Executor::doit($sql);
	}

    
	public static function delById($id){
		$sql = "delete from products where p_id=$id";
		Executor::doit($sql);
	}


	public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from products where p_id=\"$id\"";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ProductData());

	}
	
	public function del(){
		$sql = "delete from products where p_id=$this->p_id";
		Executor::doit($sql);
	}
	
	
	public function delByStore(){
		$sql = "delete from product_to_store where product_id=$this->p_id";
		Executor::doit($sql);
	}

	public static function getAll(){
		$sql = "select SQL_BIG_RESULT * from products";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
		
	public static function getAllByOT($stock){
	$sql = "select id,sum(purchase_price*quantity_in_stock) AS total FROM product_to_store where type_id=1 and quantity_in_stock>0 and store_id=\"$stock\"";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	public static function getLike($p){
		$sql = "select SQL_BIG_RESULT * from products where (p_code like '%$p%' or hsn_code like '%$p%' or p_name like '%$p%')";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	
		public static function getAllnoCat(){
		$sql = "select SQL_BIG_RESULT * from products where category_id=0";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
		public static function getByStoreId($id){
		$sql = "select SQL_BIG_RESULT * from product_to_store where product_id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ProductData());

	}
	
	public static function getAllByCategoryId($id){
		$sql = "select SQL_BIG_RESULT * from products where category_id=$id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	public static function getAllBySQL($sqlextra){
		$sql = "select SQL_BIG_RESULT * from products $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	public static function getAllBySQL2($sqlextra){
		$sql = "select SQL_BIG_RESULT * from product_to_store $sqlextra";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	public static function getAllByID(){
		$sql = "select MAX(p_id) as id from products";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

}

?>