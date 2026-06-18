<?php
session_start();
// ---
// la tarea de este archivo es eliminar todo rastro de cookie

// -- eliminamos el usuario
if(isset($_SESSION['user_id'])){

$user = new SSData();
	$user->user_id = $_SESSION['user_id'];
	$user->update();
	
	unset($_SESSION['user_id']);
	unset($_SESSION['stock_id']);
}

session_destroy();
//estemos donde estemos nos redirije al index
header("Location: https://rentals.assanpos.com/");
?>