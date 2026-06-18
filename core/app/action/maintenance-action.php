<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){
    
if($_POST["type_id"]=="7"):

$cars = CarsData::getById($_POST["car_id"]);

    $user = new FuelsData();
	$user->car_id = $_POST["car_id"];
	$user->user_id = $_POST["user_id"];
	$user->f_id = $_POST["f_id"];
		$user->cup_dolar = $_POST["cup_dolar"];
	$user->purchase_price = $_POST["purchase_price"];
	$user->total = $_POST["total"];
	$user->created_att = $_POST["created_at"];
	$user->stock_id = $cars->stock_id;
	$user->add();
	
elseif($_POST["type_id"]=="2"):

$cars = CarsData::getById($_POST["car_id"]);

$user = new OilData();
	$user->car_id = $_POST["car_id"];
	$user->user_id = $_POST["user_id"];
	$user->f_id = $_POST["f_id"];
		$user->cup_dolar = $_POST["cup_dolar"];
	$user->purchase_price = $_POST["purchase_price"];
	$user->total = $_POST["total"];
	$user->kms = $_POST["kms"];
	$user->created_att = $_POST["created_at"];
	$user->stock_id = $cars->stock_id;
	$user->add();

	$kms = CarsData::getById($_POST["car_id"]);
	$kms->kms_current = $_POST["kms"];
	$kms->update_ksc();
	
elseif($_POST["type_id"]=="5"):

$cars = CarsData::getById($_POST["car_id"]);

$user = new TollData();
	$user->car_id = $_POST["car_id"];
	$user->user_id = $_POST["user_id"];
	$user->f_id = $_POST["f_id"];
		$user->cup_dolar = $_POST["cup_dolar"];
	$user->purchase_price = $_POST["purchase_price"];
	$user->total = $_POST["total"];
	$user->created_att = $_POST["created_at"];
	$user->stock_id = $cars->stock_id;
	$user->add();
	
else:
    
$cars = CarsData::getById($_POST["car_id"]);
$tym = MData::getById($_POST["type_id"]);

$user = new MaintenanceData();
    $user->maintenance = $tym->name;
	$user->car_id = $_POST["car_id"];
	$user->user_id = $_POST["user_id"];
	$user->f_id = $_POST["f_id"];
	$user->cup_dolar = $_POST["cup_dolar"];
	$user->purchase_price = $_POST["purchase_price"];
	$user->total = $_POST["total"];
	$user->created_att = $_POST["created_at"];
	$user->stock_id = $cars->stock_id;
	$user->add();

endif;

	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo el Mantenimiento";
          $user->add();
          
echo 'true';
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
    $cars = CarsData::getById($_POST["car_id"]);
	$user = MaintenanceData::getById($_POST["id"]);
	$user->p_id = $_POST["p_id"];
	$user->f_id = $_POST["f_id"];
	$user->car_id = $_POST["car_id"];
	$user->user_id = $_POST["user_id"];
	$user->total = $_POST["total"];
	$user->maintenance = $_POST["maintenance"];
	$user->created_att = $_POST["created_at"];
	$user->stock_id = $cars->stock_id;
    $user->update();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el Mantenimiento";
          $user->add();
	
echo 'true';

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"){

if($_GET["spends"]=="ACEITE"):
$category = OilData::getById($_GET["id"]);
$category->del();

elseif($_GET["spends"]=="COMBUSTIBLE"):
$category = FuelsData::getById($_GET["id"]);
$category->del();

elseif($_GET["spends"]=="PEAJE"):
$category = TollData::getById($_GET["id"]);
$category->del();
else:
$category = MaintenanceData::getById($_GET["id"]);
$category->del();
endif;

header('location:./?view=spends&opt=vehicle&car_id='.$_GET["car_id"]);
}


?>