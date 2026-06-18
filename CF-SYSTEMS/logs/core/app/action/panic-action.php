<?php
session_start();

$user_id = $_SESSION["user_id"];
$now = date("Y-m-d H:i:s");

$base = new Database();
$con = $base->connect();

// Activar modo pánico
$sql = "UPDATE system_status SET status='panic', activated_by=$user_id, activated_at='$now'";
$query = $con->query($sql);

// Opcional: cerrar sesiones activas
session_destroy();

header("Location: https://rentals.assanpos.com/");
?>
