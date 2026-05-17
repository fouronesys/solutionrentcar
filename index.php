<?php
/**
* @author ASSANPOS
**/

define("ROOT", dirname(__FILE__));

error_reporting(0);


include "core/autoload.php";
ob_start();
session_start();

// si quieres que se muestre las consultas SQL debes decomentar la siguiente linea
// Core::$debug_sql = true;

$lb = new Lb();
$lb->start();

?>