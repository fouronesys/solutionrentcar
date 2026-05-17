<?php
if(isset($_GET["opt"]) && $_GET["opt"] == "add"):

    $user = $_POST['name'];
    $base = new Database();
    $con = $base->connect();
    $sql = "SELECT name FROM insurance WHERE name = \"{$user}\"";
    $query = $con->query($sql);

    $found = false;
    while ($r = $query->fetch_array()) {
        $found = true;
    }

    if (!$found) {
        $loc = new InsuranceData();
        $loc->name = $_POST["name"];
        $loc->add();

        $log = new ACData();
        $log->user_id = $_SESSION["user_id"];
        $log->accion = "Hizo el Seguro" . $_POST["name"];
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
    $sql = "SELECT name FROM insurance WHERE name = \"{$name}\"";
    $query = $con->query($sql);

    $found = false;
    while ($r = $query->fetch_array()) {
        $found = true;
    }

    if (!$found) {
        $loc = new InsuranceData();
        $loc->name = $name;
        $loc->add();

        $log = new ACData();
        $log->user_id = $_SESSION["user_id"];
        $log->accion = "Hizo el Seguro" . $name;
        $log->add();

        echo "OK";
        exit;
    }

    echo "NOT";
    exit;

elseif(isset($_GET["opt"]) && $_GET["opt"]=="upd"):
	$user = InsuranceData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->update();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el Seguro " .$_POST["name"]."";
          $user->add();
	
echo 'true';

elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"):
$category = InsuranceData::getById($_GET["id"]);
$category->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el Seguro " .$_POST["name"]."";
          $user->add();
header('location:./?view=insurance&opt=all');
endif;

?>