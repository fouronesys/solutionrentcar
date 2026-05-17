<?php if(isset($_GET["opt"]) && $_GET["opt"]=="add"){

$TicketMm = StockData::getPrincipal()->ticket_mm;

$person_id = $_POST["person_id"];

if(isset($_SESSION["cotization"])){
$cart = $_SESSION["cotization"];

if(count($cart)>0){
$iva = $_POST["iva"];
$sell = new CotizationData();
      $sell->user_id = $_SESSION["user_id"];
			$sell->total = $_POST["total"]+($_POST["total"] * $iva) / 100;
			$sell->iva =  $iva;
			$sell->stock_id = StockData::getPrincipal()->id;
			$sell->person_id = $person_id;
			$s = $sell->add();

			foreach($cart as  $c){
			$op = new OperationData();
			$op->price = $c["price"];
			$op->car_id = $c["product_id"];
			$op->cotization_id=$s[1];
			$op->day= $c["q"];
			$op->add();			 		
		}


unset($_SESSION["cotization"]);
			setcookie("selled","selled");////////////////////


echo '<div class="row"><div class="col-12 col-offset-3">
<div class="embed-responsive embed-responsive-16by9">
  <iframe id="ticket1" name="ticket1" class="embed-responsive-item" src='.$TicketMm.'/ticket-cotiz.php?id="'.$s[1].'" allowfullscreen></iframe>
</div>
</div></div>
';
echo "<script>window.frames['ticket1'].focus();
window.frames['ticket1'].print();</script>";
 }
 
}



}elseif(isset($_GET["opt"]) && $_GET["opt"]=="addbook"){

$cotiz_id = $_POST["cotiz_id"];
$start_at = $_POST["start_at"];
$person_id = $_POST["person_id"];
$car_id = $_POST["car_id"];
$day = $_POST["day"];
$price = $_POST["price"];


header('location:./?view=cotization&opt=booking&start_at='.$start_at.'&person_id='.$person_id.'&car_id='.$car_id.'&day='.$day.'&price='.$price.'&cotiz_id='.$cotiz_id);
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"){
$category = CotizationData::getById($_GET["id"]);
$category->del();

foreach(OperationData::getBySQL("where cotization_id =".$_POST["id"]) as $p):
$p->del();
endforeach;

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino la Cotizacion " .$_POST["name"]."";
          $user->add();
header('location:./?view=cotization&opt=all');

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="delprocess"){
$category = OperationData::getById($_GET["id"]);
$category->del();

header('location:./?view=cotization&opt=process&id='.$_GET["cotization_id"]);
}


?>