<?php
if(isset($_GET["opt"]) && $_GET["opt"] == "add"):

    $user = $_POST['name'];
    $base = new Database();
    $con = $base->connect();
    $sql = "SELECT name FROM brand WHERE name = \"{$user}\"";
    $query = $con->query($sql);

    $found = false;
    while ($r = $query->fetch_array()) {
        $found = true;
    }

    if (!$found) {
        $loc = new BrandData();
        $loc->name = $_POST["name"];
        $loc->add();

        $log = new ACData();
        $log->user_id = $_SESSION["user_id"];
        $log->accion = "Hizo la marca" . $_POST["name"];
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
    $sql = "SELECT name FROM brand WHERE name = \"{$name}\"";
    $query = $con->query($sql);

    $found = false;
    while ($r = $query->fetch_array()) {
        $found = true;
    }

    if (!$found) {
        $loc = new BrandData();
        $loc->name = $name;
        $loc->add();

        $log = new ACData();
        $log->user_id = $_SESSION["user_id"];
        $log->accion = "Hizo la marca" . $name;
        $log->add();

        echo "OK";
        exit;
    }

    echo "NOT";
    exit;

elseif(isset($_GET["opt"]) && $_GET["opt"] == "upd"):
    $user = BrandData::getById($_POST["user_id"]);
    $user->name = $_POST["name"];
    $user->update();

    $log = new ACData();
    $log->user_id = $_SESSION["user_id"];
    $log->accion = "Modificó la marca" . $_POST["name"];
    $log->add();

    echo "true";
    exit;

elseif(isset($_GET["opt"]) && $_GET["opt"] == "del"):
    $category = BrandData::getById($_GET["id"]);
    $category->del();

    $log = new ACData();
    $log->user_id = $_SESSION["user_id"];
    $log->accion = "Eliminó la marca" . $_GET["id"];
    $log->add();

    header('location:./?view=brands&opt=all');
    exit;
    
endif;?>
