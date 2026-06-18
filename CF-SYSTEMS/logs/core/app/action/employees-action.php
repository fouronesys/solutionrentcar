<?php
if(isset($_GET["opt"]) && $_GET["opt"] == "add"):

    $user = $_POST['name'];
    $base = new Database();
    $con = $base->connect();
    $sql = "SELECT name FROM user WHERE name = \"{$user}\" and kind=3";
    $query = $con->query($sql);

    $found = false;
    while ($r = $query->fetch_array()) {
        $found = true;
    }

    if (!$found) {
    $user = new UserData();
	$user->kind = 3;
	$user->stock_id = StockData::getPrincipal()->id;
	$user->name = $_POST["name"];
	$user->no = $_POST["no"];
	$user->lastname = $_POST["lastname"];
	if ($_POST["gender"]==1) {$user->image="man.png";}else{$user->image="woman.png";}
	$user->add_employees();

        $log = new ACData();
        $log->user_id = $_SESSION["user_id"];
        $log->accion = "Agrego el empleado " .$_POST["name"]."";
        $log->add();

        echo "OK";
        exit;
    }

    echo "NOT";
    exit;

elseif(isset($_GET["opt"]) && $_GET["opt"] == "add_offline"):

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data || !isset($data["name"])) {
        echo "ERROR";
        exit;
    }

    $name = $data["name"];
    $base = new Database();
    $con = $base->connect();
    $sql = "SELECT name FROM user WHERE name = \"{$user}\" and kind=3";
    $query = $con->query($sql);

    $found = false;
    while ($r = $query->fetch_array()) {
        $found = true;
    }

    if (!$found) {
    $user = new UserData();
	$user->kind = 3;
	$user->stock_id = $_POST["stock_id"];
	$user->name = $_POST["name"];
	$user->no = $_POST["no"];
	$user->lastname = $_POST["lastname"];
	if ($_POST["gender"]==1) {$user->image="man.png";}else{$user->image="woman.png";}
	$user->add_employees();

        $log = new ACData();
        $log->user_id = $_SESSION["user_id"];
        $log->accion = "Agrego el empleado " .$_POST["name"]."";
        $log->add();

        echo "OK";
        exit;
    }

    echo "NOT";
    exit;
    
elseif(isset($_GET["opt"]) && $_GET["opt"]=="upd"):
	$user = UserData::getById($_POST["user_id"]);

	$user->stock_id = isset($_POST["stock_id"])?$_POST["stock_id"]:"NULL";
	$user->comision = isset($_POST["comision"])&&$_POST["comision"]!=""?$_POST["comision"]:"NULL";
    
	$user->name = $_POST["name"];
	$user->lastname = $_POST["lastname"];
	$user->email = $_POST["email"];
	if ($_POST["gender"]==1) {
	$user->image="man.png";
	}else{
	$user->image="woman.png";
	}
	$user->update();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el empleado " .$_POST["name"]."";
          $user->add();
          
echo 'true';
elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"):

$user = UserData::getById($_SESSION["user_id"]);

if($user->kind==1){
	$userx  = UserData::getById($_GET["id"]);

	if($user->id!=$userx->id){
		$userx->del();

	}else{
		Core::alert("Error. No te puedes eliminar a ti mismo!");
	}

}else{
	Core::alert("Error. No tienes permisos!");
}

header('location:./?view=employees&opt=all');
endif;

?>