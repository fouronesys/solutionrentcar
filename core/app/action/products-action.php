<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){

  $product = new ProductData();
  $product->p_type = 1;
  $product->p_code  = $_POST["p_code"];
  $product->p_name =  ucwords($_POST["p_name"]);
  $product->p_model = $_POST["p_model"];
  $product->created_by = $_SESSION["user_id"];
  $product->add();
  
  $id_product = ProductData::getAllByID();
  $prod = $id_product[0]->id!=null?$id_product[0]->id:0;
  
  $product = new ProductData();
  $product->product_id = $prod;
  $product->store_id = StockData::getPrincipal()->id;
  $product->brand_id=$_POST["brand_id"];
  $product->purchase_price = $_POST["purchase_price"];
  $product->usd_price = $_POST["usd_price"];
  $product->tasa_dolar = $_POST["tasa_dolar"];
  $product->quantity_in_stock = $_POST["quantity_in_stock"];
  $product->alert_quantity=$_POST["alert_quantity"];
  $product->sup_id = $_POST["sup_id"];
  $product->add_to_store();

echo 'true';

}else if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
    
  $product = ProductData::getById($_POST["product_id"]);
  $product->p_code = $_POST["p_code"];
  $product->p_name =  ucwords($_POST["p_name"]);
  $product->p_date = $_POST["p_date"];
  $product->p_model = $_POST["p_model"];
  $product->update();
  
  $product = ProductData::getByStoreId($_POST["product_id"]);
  $product->brand_id=$_POST["brand_id"];
  $product->purchase_price = $_POST["purchase_price"];
  $product->usd_price = $_POST["usd_price"];
  $product->tasa_dolar = $_POST["tasa_dolar"];
  $product->quantity_in_stock = $_POST["quantity_in_stock"];
  $product->alert_quantity=$_POST["alert_quantity"];
  $product->sup_id = $_POST["sup_id"];
  $product->status = $_POST["status"]?1:0;
  $product->upd_to_store();
 
echo 'true';

}else if(isset($_GET["opt"]) && $_GET["opt"]=="exit"){
    
  $product = ProductData::getByStoreId($_POST["product_id"]);
  
  $sell = new SellData();
  $sell->store_id = $_SESSION["stock_id"];
  $sell->item_id = $_POST["product_id"];
  $sell->type = $_POST["type"];
  $sell->sup_id =  $product->sup_id;
  $sell->price_item = $product->sell_price;
  $sell->total_quantity = $_POST["quantity_in_stock"];
  $sell->total_amount = ($_POST["quantity_in_stock"]*$_POST["product_id"]);
  $sell->created_by = $_SESSION["user_id"];
  $sell->add_readjustment();
 
  $product = ProductData::getByStoreId($_POST["product_id"]);
  $product->quantity_in_stock = ($product->quantity_in_stock-$_POST["quantity_in_stock"]);
  $product->upd_Qt();
  
echo 'true';
}else if(isset($_GET["opt"]) && $_GET["opt"]=="entrance"){
    
  $product = ProductData::getByStoreId($_POST["product_id"]);
  
  $sell = new PurchaseData();
  $sell->store_id = $_SESSION["stock_id"];
  $sell->item_id = $_POST["product_id"];
  $sell->type = $_POST["type"];
  $sell->sup_id =  $product->sup_id;
  $sell->price_item = $product->sell_price;
  $sell->total_quantity = $_POST["quantity_in_stock"];
  $sell->total_amount = ($_POST["quantity_in_stock"]*$_POST["product_id"]);
  $sell->created_by = $_SESSION["user_id"];
  $sell->add_readjustment();
 
  $product = ProductData::getByStoreId($_POST["product_id"]);
  $product->quantity_in_stock = ($product->quantity_in_stock+$_POST["quantity_in_stock"]);
  $product->upd_Qt();
  
echo 'true';
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){
$product = ProductData::getById($_GET["id"]);
$operations = ProductData::getAllBySQL2("where product_id=".$_GET["id"]);

foreach ($operations as $op) {
$op->delByStore();
}

$product->del();

header('location:./?view=products&opt=all');
}

?>
