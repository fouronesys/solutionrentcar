<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){
$host = "localhost";
$usuario = "u144787244_datarentcar";
$password = "DataRentcar01";
$base_datos1 = "u144787244_datarentcar";

// Conexión al servidor
$conn = new mysqli($host, $usuario, $password);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Seleccionar las bases de datos según se necesiten
$conn->select_db($base_datos1);

  $no = $_POST["no"];
  $address = $_POST["address"];
  $nacimiento = $_POST["nacimiento"];
  $nacionalidad = $_POST["nacionalidad"];
  $gender = $_POST["gender"];
  $name = $_POST["name"];
  $comment = $_POST["comment"];
  $user_id = StockData::getPrincipal()->name;
  $stock_id = StockData::getPrincipal()->address;

// Consulta en la base de datos principal
$query = $conn->query("INSERT INTO `person`(`no`, `address`, `nacimiento`, `nacionalidad`, `gender`, `name`, `comment`, `user_id`, `stock_id`, `created_at`) VALUES ('$no','$address','$nacimiento','$nacionalidad','$gender','$name','$comment','$user_id','$stock_id','NOW()')");

echo 'true';
}

?>