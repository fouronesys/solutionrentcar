<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){

$user = $_POST['name'];
$base = new Database();
$con = $base->connect();
$sql = "select name from category where name=\"".$user."\"";
//print $sql;
$query = $con->query($sql);
$found = false;
while($r = $query->fetch_array()){
     $found = true ;
}

if($found==false) {
$user = new CategoryData();
     $user->name = $_POST["name"];
     $user->add();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo la categoria " .$_POST["name"]."";
          $user->add();
echo 'true';
}

     
}
	
else if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
	$user = CategoryData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->update();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico la categoria " .$_POST["name"]."";
          $user->add();
echo 'true';

}
else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){
$category = CategoryData::getById($_GET["id"]);
$category->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino la categoria " .$_POST["name"]."";
          $user->add();
header('location:./?view=categories&opt=all');
}


?>