<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){

$user = new ReminderData();
	$user->start_at = $_POST["start_at"];
	$user->name = $_POST["name"];
	$user->user_id = $_SESSION["user_id"];
	$user->stock_id = StockData::getPrincipal()->id;
	$user->add();

echo 'true';
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
    $user = ReminderData::getById($_GET["id"]);
	$user->start_at = date("Y-m-d h:i:s", strtotime($user->start_at.'+1 month'));
	$user->upd();
	
header('location:./?view=reminder');


}elseif(isset($_GET["opt"]) && $_GET["opt"]=="update"){
    $user = ReminderData::getById($_POST["user_id"]);
	$user->start_at = $_POST["start_at"];
	$user->name = $_POST["name"];
	$user->update();
	
echo 'true';
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"){
$category = ReminderData::getById($_GET["id"]);
$category->del();

header('location:./?view=reminder');

}


?>