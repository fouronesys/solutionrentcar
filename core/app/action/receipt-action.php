<?php if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
	$user = CData::getById($_POST["user_id"]);
	$user->de = $_POST["de"];
	$user->hasta = $_POST["hasta"];
	$user->expiration = $_POST["expiration"];
	$user->update();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el Comprobante " .$_POST["name"]."";
          $user->add();
	
echo 'true';

}

?>