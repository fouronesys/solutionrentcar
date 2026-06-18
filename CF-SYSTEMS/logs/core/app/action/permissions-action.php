<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){
    $product = new PUData();
  $product->name = $_POST["name"];
  $product->location = $_POST["location"];
  $product->add();
    
    $user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo el permiso " .$_POST["name"]."";
          $user->add();
          
echo 'true';
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
        $product = PUData::getById($_POST["product_id"]);

  $product->name = $_POST["name"];
  $product->location = $_POST["location"];
  $product->is_active = isset($_POST["is_active"])?1:0;
  $product->update();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el permiso " .$_POST["name"]."";
          $user->add();

echo 'true';
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){
$product =PUData::getById($_GET["id"]);
$product->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el permiso " .$_POST["name"]."";
          $user->add();
header('location:./?view=permissions&opt=all');
}


?>