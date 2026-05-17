<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="addpayment"){
	$user = new PData();
	$user->name = $_POST["name"];
	$user->add();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Agrego el tipo de pago " .$_POST["name"]."";
          $user->add();
          
echo 'true';
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="updpayment"){
		$user = PData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->update();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el tipo de pago " .$_POST["name"]."";
          $user->add();

echo 'true';
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="delpayment"){
$category = PData::getById($_GET["id"]);

$category->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el tipo de pago " .$_POST["name"]."";
          $user->add();
header('location:./?view=typepayment&opt=shape');
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="addshape"){
	$user = new FData();
	$user->name = $_POST["name"];
	$user->add();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Agrego la forma de pago " .$_POST["name"]."";
          $user->add();
          
echo 'true';
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="updshape"){
		$user = FData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->update();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico la forma de pago " .$_POST["name"]."";
          $user->add();

echo 'true';
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="delshape"){
$category = FData::getById($_GET["id"]);

$category->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino la forma de pago " .$_POST["name"]."";
          $user->add();
header('location:./?view=typepayment&opt=payment');
}


?>