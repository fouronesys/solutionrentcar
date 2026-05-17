<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){
	$user = new SGData();
	$user->name = $_POST["name"];
     $user->add();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo el tipo de gasto " .$_POST["name"]."";
          $user->add();
echo 'true';
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
	$user = SGData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->update();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el tipo de gasto " .$_POST["name"]."";
          $user->add();
echo 'true';

}
else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){
$category = SGData::getById($_GET["id"]);
$category->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el tipo de gasto " .$_POST["name"]."";
          $user->add();
header('location:./?view=spendtypeisr&opt=all');
}


?>