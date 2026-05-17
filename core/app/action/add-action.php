<?php if(isset($_GET["opt"]) && $_GET["opt"]=="payment"):

$payment2 = new PaymentData();
 	$payment2->val = -1*$_POST["val"];
 	$payment2->sell_id = $_POST["sell_id"];
 	$payment2->person_id = $_POST["client_id"];
 	$payment2->user_id = $_SESSION["user_id"];
    $payment2->stock_id = StockData::getPrincipal()->id;
 	$payment2->f_id = $_POST["f_id"];
	$payment2->is_stock = 0;
 	$payment2->add_payment();
 	
 	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Realizo un Pago del cliente ID " .$_POST["client_id"]."";
          $user->add();
        
header("location:./?view=credit&opt=client_modal&id=".$_POST["client_id"]."");

elseif(isset($_GET["opt"]) && $_GET["opt"]=="contractpayment"):

$payment2 = new PaymentData();
 	$payment2->val = -1*$_POST["val"];
 	$payment2->sell_id = $_POST["sell_id"];
 	$payment2->person_id = $_POST["client_id"];
 	$payment2->user_id = $_SESSION["user_id"];
    $payment2->stock_id = StockData::getPrincipal()->id;
 	$payment2->f_id = $_POST["f_id"];
 	$payment2->is_stock = 0;
 	$payment2->add_payment();
	// code...


 	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Realizo un Pago del cliente ID " .$_POST["client_id"]."";
          $user->add();
        
header("location:./?view=credit&opt=client_modal&id=".$_POST["client_id"]."");

elseif(isset($_GET["opt"]) && $_GET["opt"]=="renewpayment"):

                
		foreach(PaymentData::getAllBySQL("where payment_type_id=1 and booking_id=".$_POST["sell_id"]." order by created_at asc limit 1") as $payment2):
				 	$payment2->val = $_POST["txtal"];
				 	$payment2->update();	
              endforeach; 


$payment2 = new PaymentData();
 	$payment2->val = -1*$_POST["val"];
 	$payment2->sell_id = $_POST["sell_id"];
 	$payment2->person_id = $_POST["client_id"];
 	$payment2->user_id = $_SESSION["user_id"];
    $payment2->stock_id = StockData::getPrincipal()->id;
 	$payment2->f_id = $_POST["f_id"];
	$payment2->is_stock = 0;
 	$payment2->add_payment();
	// code...


 	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Realizo un Pago del cliente ID " .$_POST["client_id"]."";
          $user->add();
        
header("location:./?view=credit&opt=client_modal&id=".$_POST["client_id"]."");


elseif(isset($_GET["opt"]) && $_GET["opt"]=="contractrenew"):

$payment2 = new PaymentData();
 	$payment2->val = -1*$_POST["val"];
 	$payment2->sell_id = $_POST["sell_id"];
 	$payment2->person_id = $_POST["client_id"];
 	$payment2->user_id = $_SESSION["user_id"];
    $payment2->stock_id = StockData::getPrincipal()->id;
 	$payment2->f_id = $_POST["f_id"];
 	$payment2->is_stock = 0;
 	$payment2->add_payment();
    
                $payment = new PaymentData();
			 	$payment->sell_id = $_POST["sell_id"];
			 	$payment->val = $_POST["val"];
			 	$payment->user_id = $_SESSION["user_id"];
                $payment->stock_id = StockData::getPrincipal()->id;
			 	$payment->person_id= $_POST["client_id"];
			 	$payment->add();

 	$bk = BookingData::getById($_POST["sell_id"]);
 	$bk->end_at = date("Y-m-d H:i:s",strtotime($bk->end_at."+".$bk->day." day")); 
 	$bk->upd_end();


 	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Realizo un Pago del cliente ID " .$_POST["client_id"]."";
          $user->add();
        
header("location:./?view=credit&opt=client_modal&id=".$_POST["client_id"]."");


elseif(isset($_GET["opt"]) && $_GET["opt"]=="paymentstock"):

    $payment2 = new PaymentData();
 	$payment2->val = -1*$_POST["val"];
 	$payment2->sell_id = $_POST["sell_id"];
 	$payment2->person_id = $_POST["client_id"];
 	$payment2->user_id = $_SESSION["user_id"];
    $payment2->stock_id = StockData::getPrincipal()->id;
 	$payment2->f_id = $_POST["f_id"];
	$payment2->is_stock = 1;
 	$payment2->add_payment();
	
$user = new SpendData();
$user->person_id = $_POST["client_id"];
$user->f_id = $_POST["f_id"];
$user->p_id = 1;
$user->name = "PAGO DE RENTA";
$valor = intval(str_replace(",", "", $_POST["val"]));
$user->price = $_POST["val"]; 
$user->user_id = $_SESSION["user_id"];
$user->stock_id = StockData::getPrincipal()->id;
$user->add_ext();   
  
header("location:./?view=credit&opt=client_modal&id=".$_POST["client_id"]."");
   
elseif(isset($_GET["opt"]) && $_GET["opt"]=="spendpayment"):
	$payment2 = new PaymentData();
 	$payment2->val = -1*$_POST["val"];
 	$payment2->sell_id = $_POST["spend_id"];
 	$payment2->person_id = $_POST["client_id"];
 	$payment2->user_id = $_SESSION["user_id"];
    $payment2->stock_id = StockData::getPrincipal()->id;
 	$payment2->f_id = $_POST["f_id"];
 	$payment2->operation_type_id = 1;
 	$payment2->add_spendpayment();

 	$user = new SpendData();
	$user->name = "Pago de Proveedor ID".$_POST["client_id"];
	$user->price = 1*$_POST["val"];
	$user->add();

 	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Realizo un Pago del proveedor ID " .$_POST["client_id"]."";
          $user->add();

	header("location:./?view=credit&opt=provider_modal&id=".$_POST["client_id"]."");

///////////////////////////////////////////////////////// PAYMENT //////////////////////////////////////////////////////


$user = SpendData::getById($_POST["user_id"]);
	    $user->price = $_POST["price"];
	    $user->total = 0;
	    $user->update2();
          header('location:./?view=finance&opt=spends');


///////////////////////////////////////////////////////////// RECEIPT //////////////////////////////////////////////////

elseif(isset($_GET["opt"]) && $_GET["opt"]=="receipt"):
/// si es Consumidor Final....
if($_POST["c_id"]==1){
$cf = new CFData();
$cf->name_in = $_POST["name_in"];
$cf->name_out = $_POST["name_out"];
$cf->created_at = $_POST["created_at"];

for($i = $cf->name_in; $i < $cf->name_out; $i++){
$cf->name_in =$cf->name_in+1;
$cf->add();
}
}
/// si es Credito Fiscal....
elseif($_POST["c_id"]==2){
$cfs = new CFSData();
$cfs->name_in = $_POST["name_in"];
$cfs->name_out = $_POST["name_out"];
$cfs->created_at = $_POST["created_at"];

for($i = $cfs->name_in; $i < $cfs->name_out; $i++){
$cfs->name_in =$cfs->name_in+1;
$cfs->add();
}
}
/// si es Gubernamental....
elseif($_POST["c_id"]==3){
$cg = new CGData();
$cg->name_in = $_POST["name_in"];
$cg->name_out = $_POST["name_out"];
$cg->created_at = $_POST["created_at"];

for($i = $cg->name_in; $i < $cg->name_out; $i++){
$cg->name_in =$cg->name_in+1;
$cg->add();
}
}
/// si es Nota de Credito....
elseif($_POST["c_id"]==4){
$cnc = new CNCData();
$cnc->name_in = $_POST["name_in"];
$cnc->name_out = $_POST["name_out"];
$cnc->created_at = $_POST["created_at"];

for($i = $cnc->name_in; $i < $cnc->name_out; $i++){
$cnc->name_in =$cnc->name_in+1;
$cnc->add();
}
}
/// si es Nota de Debito....
elseif($_POST["c_id"]==8){
$cnd = new CNDData();
$cnd->name_in = $_POST["name_in"];
$cnd->name_out = $_POST["name_out"];
$cnd->created_at = $_POST["created_at"];

for($i = $cnd->name_in; $i < $cnd->name_out; $i++){
$cnd->name_in =$cnd->name_in+1;
$cnd->add();
}
}
/// si es Compras....
elseif($_POST["c_id"]==9){
$ccp = new CCPData();
$ccp->name_in = $_POST["name_in"];
$ccp->name_out = $_POST["name_out"];
$ccp->created_at = $_POST["created_at"];

for($i = $ccp->name_in; $i < $ccp->name_out; $i++){
$ccp->name_in =$ccp->name_in+1;
$ccp->add();
}
}
/// si es Gastos....
elseif($_POST["c_id"]==10){
$cgt = new CGTData();
$cgt->name_in = $_POST["name_in"];
$cgt->name_out = $_POST["name_out"];
$cgt->created_at = $_POST["created_at"];

for($i = $cgt->name_in; $i < $cgt->name_out; $i++){
$cgt->name_in =$cgt->name_in+1;
$cgt->add();
}
}
/// si es Regimen Special....
elseif($_POST["c_id"]==11){
$csr = new CRSData();
$csr->name_in = $_POST["name_in"];
$csr->name_out = $_POST["name_out"];
$csr->created_at = $_POST["created_at"];

for($i = $csr->name_in; $i < $csr->name_out; $i++){
$csr->name_in =$csr->name_in+1;
$csr->add();
}
}
echo 'true';

//////////////////////////////////////////////////////////// USERPERMISSION ////////////////////////////////////////////

elseif(isset($_GET["opt"]) && $_GET["opt"]=="userpermissions"):
	$user = new UserPermissionsData();
	$user->user_id = $_GET["user_id"];
	$user->permits_id = $_GET["id"];
	$user->add();
      
header('location:./?view=userpermissions&id='.$_GET["user_id"]);


elseif(isset($_GET["opt"]) && $_GET["opt"]=="tocart"):


if(!isset($_SESSION["cotization"])){
		$product = array("product_id"=>$_POST["product_id"],"price"=>$_POST['price'],"q"=>$_POST["q"]);
	$_SESSION["cotization"] = array($product);


	$cart = $_SESSION["cotization"];


///////////////////////////////////////////////////////////////////
		$num_succ = 0;
		$process=false;
		$errors = array();
		foreach($cart as $c){

			///
			$product = CarsData::getById($c["product_id"]);
			if($c["q"]<=1){
				$num_succ++;


			}


		}
///////////////////////////////////////////////////////////////////

//echo $num_succ;
if($num_succ==count($cart)){
	$process = true;
}


}else {

$found = false;
$cart = $_SESSION["cotization"];
$index=0;

			$product = CarsData::getById($_POST["product_id"]);

$can = true;
if($can==true){
foreach($cart as $c){
	if($c["product_id"]==$_POST["product_id"]){
		echo "found";
		$found=true;
		break;
	}
	$index++;
}

if($found==true){
	$q1 = $cart[$index]["q"];
	$q2 = $_POST["q"];
	$cart[$index]["q"]=$q1+$q2;
	$_SESSION["cotization"] = $cart;
}

if($found==false){
    $nc = count($cart);
$product = array("product_id"=>$_POST["product_id"],"price"=>$_POST['price'],"q"=>$_POST["q"]);	$cart[$nc] = $product;
//	print_r($cart);
	$_SESSION["cotization"] = $cart;
}

}
}
header('location:./?view=cotization&opt=new');
///////////////////////////////////////////////////////////////////// TOCART /////////////////////////////////////////

elseif(isset($_GET["opt"]) && $_GET["opt"]=="tochat"):

$search=rtrim($_POST["name"]); 
$products = ChatData::getLike($search);
$cart = $_SESSION["chat"];

foreach($products  as $mesero):
$lastname = $mesero->response;
$url = $mesero->url;
endforeach;    


    $nc = count($cart);
	$product = array("name"=>$_POST["name"],"lastname"=>$lastname,"url"=>$url,"created_at"=>$_POST["created_at"]);
	$cart[$nc] = $product;
	$_SESSION["chat"] = $cart;

endif;?>