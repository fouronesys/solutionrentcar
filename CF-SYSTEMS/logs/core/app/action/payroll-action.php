<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"):
$user = $_POST['idemployee'];
$day = $_POST['pay_day'];

$base = new Database();
$con = $base->connect();
$sql = "select * from payroll where idemployee=\"".$user."\" and pay_day=\"".$day."\""; 
//print $sql;
$query = $con->query($sql);
$found = false;
while($r = $query->fetch_array()){
	$found = true ;
}

if($found==false):
$user = new PayrollData();
	$user->idemployee = $_POST["idemployee"];
	$user->amount = $_POST["amount"];
	$user->pay_day = $_POST["pay_day"];
    $user->user_id = $_SESSION["user_id"];
	$user->add();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo la nomina " .$_POST["name"]."";
          $user->add();
echo 'true';
endif;

	

elseif(isset($_GET["opt"]) && $_GET["opt"]=="upd"):
	$user = PayrollData::getById($_POST["user_id"]);
	$user->idemployee = $_POST["idemployee"];
	$user->amount = $_POST["amount"];
	$user->pay_day = $_POST["pay_day"];
	$user->update();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico la nomina " .$_POST["idemployee"]."";
          $user->add();
	
echo 'true';


elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"):
$category = PayrollData::getById($_GET["id"]);
$category->del();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino la nomina";
          $user->add();
header('location:./?view=payroll&opt=all');
endif;

?>