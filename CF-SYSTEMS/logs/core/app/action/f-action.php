<?php if(isset($_GET["opt"]) && $_GET["opt"]=="add"){
	$user = new FData();
	$user->name = $_POST["name"];
	$user->add();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Agrego la forma de pago " .$_POST["name"]."";
          $user->add();
          
echo 'true';
}
elseif(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
		$user = FData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->update();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico la forma de pago " .$_POST["name"]."";
          $user->add();

echo 'true';
}
elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"){
$category = FData::getById($_GET["id"]);
$category->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino la forma de pago " .$_POST["name"]."";
          $user->add();
header('location:./?view=f&opt=all');
}


?>