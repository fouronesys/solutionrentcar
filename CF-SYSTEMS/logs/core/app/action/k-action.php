<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){


$user = $_POST['name'];
$base = new Database();
$con = $base->connect();
$sql = "select name from k where name=\"".$user."\"";
//print $sql;
$query = $con->query($sql);
$found = false;
while($r = $query->fetch_array()){
	$found = true ;
}

if($found==false) {
$user = new KData();
	$user->name = $_POST["name"];
	$user->add();
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo el tipo de usuario " .$_POST["name"]."";
          $user->add();
echo 'true';
}

	
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
	$user = KData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->update();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el tipo de usuario " .$_POST["name"]."";
          $user->add();
	
echo 'true';

}
else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){
$category = KData::getById($_GET["id"]);

$category->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el tipo de usuario " .$_POST["name"]."";
          $user->add();
header('location:./?view=k&opt=all');
}


?>