<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){

if ($_POST["price_normal"]>0) {
$user = new TariffData();
	$user->package_id = $_POST["package_id"];
	$user->brand_id = $_POST["brand_id"];
	$user->price = $_POST["price_normal"];
	$user->description = "Normal";
	$user->stock_id = StockData::getPrincipal()->id;
	$user->add();
}

if ($_POST["price_weekend"]>0) {
$user = new TariffData();
	$user->package_id = $_POST["package_id"];
	$user->brand_id = $_POST["brand_id"];
	$user->price = $_POST["price_weekend"];
	$user->description = "(Sabado-Domingo)";
	$user->stock_id = StockData::getPrincipal()->id;
	$user->add();
}

if ($_POST["price_peak"]>0) {
$user = new TariffData();
	$user->package_id = $_POST["package_id"];
	$user->brand_id = $_POST["brand_id"];
	$user->price = $_POST["price_peak"];
	$user->description = "Pico";
	$user->stock_id = StockData::getPrincipal()->id;
	$user->add();
}
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo la tarifa " .$_POST["name"]."";
          $user->add();
echo 'true';
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="addpackage"){

$user = new PackageData();
	$user->name = $_POST["name"];
	$user->free = $_POST["free"];
	$user->add();
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo el Paquete " .$_POST["name"]."";
          $user->add();
echo 'true';
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="upd"){

for ($i=0; $i < 3; $i++) { 
	$user = TariffData::getById($_POST["user_id"][$i]);
	$user->package_id = $_POST["package_id"];
	$user->brand_id = $_POST["brand_id"];
	$user->price = $_POST["price"][$i];
	$user->update();
}

	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico la tarifa " .$_POST["name"]."";
          $user->add();
	
echo 'true';

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="updpackage"){
	$user = PackageData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->free = $_POST["free"];
	$user->update();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el Paquete " .$_POST["name"]."";
          $user->add();
	
echo 'true';

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"){
$category = TariffData::getById($_GET["id"]);
$category->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino una tarifa";
          $user->add();
header('location:./?view=tariff&opt=all');

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="delpackage"){
$category = PackageData::getById($_GET["id"]);
$category->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el Paquete";
          $user->add();
header('location:./?view=tariff&opt=package');
}


?>