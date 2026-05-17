<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){

$user = new FuelsData();
$cars = CarsData::getById($_POST["car_id"]);
	$user->car_id = $_POST["car_id"];
	$user->user_id = $_POST["user_id"];
	$user->p_id = $_POST["p_id"];
	$user->f_id = $_POST["f_id"];
	$user->total = intval(str_replace(",", "", $_POST["total"]));
	$user->stock_id = $cars->stock_id;
	$user->add();
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hecho el Combustible";
          $user->add();
echo 'true';
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
    $cars = CarsData::getById($_POST["car_id"]);
	$user = FuelsData::getById($_POST["id"]);
	$user->car_id = $_POST["car_id"];
	$user->user_id = $_POST["user_id"];
	$user->p_id = $_POST["p_id"];
	$user->f_id = $_POST["f_id"];
	$user->created_date = $_POST["created_date"];
	$user->total = intval(str_replace(",", "", $_POST["total"]));
	$user->stock_id = $cars->stock_id;
    $user->update();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el Combustible echado";
          $user->add();
	
echo 'true';
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"){
$category = FuelsData::getById($_GET["id"]);
$category->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el Combustible echado";
          $user->add();
header('location:./?view=finance&opt=all&spends=Combustible');
}


?>