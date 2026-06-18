<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){

$user = new OilData();
$cars = CarsData::getById($_POST["car_id"]);
	$user->car_id = $_POST["car_id"];
	$user->user_id = $_POST["user_id"];
	$user->p_id = $_POST["p_id"];
	$user->f_id = $_POST["f_id"];
	$user->total = intval(str_replace(",", "", $_POST["total"]));
	$user->kms = $_POST["kms"];
	$user->created_att = $_POST["created_at"];
	$user->stock_id = $cars->stock_id;
	$user->add();

	$kms = CarsData::getById($_POST["car_id"]);
	$kms->kms_current = $_POST["kms"];
	$kms->update_ksc();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo el Cambio de Aceite";
          $user->add();
echo 'true';
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
    $cars = CarsData::getById($_POST["car_id"]);
	$user = OilData::getById($_POST["id"]);
	$user->p_id = $_POST["p_id"];
	$user->f_id = $_POST["f_id"];
	$user->car_id = $_POST["car_id"];
	$user->user_id = $_POST["user_id"];
	$user->total = intval(str_replace(",", "", $_POST["total"]));
	$user->kms = $_POST["kms"];
	$user->created_att = $_POST["created_at"];
	$user->stock_id = $cars->stock_id;
    $user->update();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el Cambio de Aceite";
          $user->add();
	
echo 'true';

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"){
$category = OilData::getById($_GET["id"]);
$category->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el Cambio de Aceite";
          $user->add();
header('location:./?view=finance&opt=all&spends=Oil');
}


?>