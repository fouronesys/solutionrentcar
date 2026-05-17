<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){
	$user = new StockData();
	$user->location = $_POST["location"];
	$user->name = $_POST["name"];
	$user->address = $_POST["address"];
	$user->phone = $_POST["phone"];
	$user->phone2 = $_POST["phone2"];
	$user->field1 = $_POST["field1"];
	$user->field2 = $_POST["field2"];
	$user->add();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Agrego la Rent Car " .$_POST["name"]."";
          $user->add();
          
echo 'true';
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
	$user = StockData::getById($_POST["id"]);
	$user->location = $_POST["location"];
	$user->field1 = $_POST["field1"];
	$user->field2 = $_POST["field2"];
	$user->name = $_POST["name"];
	$user->address = $_POST["address"];
	$user->phone = $_POST["phone"];
	$user->phone2 = $_POST["phone2"];
	$user->update();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el Rent Car " .$_POST["name"]."";
          $user->add();

echo 'true';
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){
$client = StockData::getById($_GET["id"]);

$client->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el Rent Car " .$_POST["name"]."";
          $user->add();
header('location:./?view=stocks&opt=all');
}


?>