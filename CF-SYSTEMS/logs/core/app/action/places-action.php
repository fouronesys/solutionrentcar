<?php if(isset($_GET["opt"]) && $_GET["opt"]=="add"):

$user = $_POST['name'];
$base = new Database();
$con = $base->connect();
$sql = "select name from place where name=\"".$user."\"";
//print $sql;
$query = $con->query($sql);
$found = false;
while($r = $query->fetch_array()){
	$found = true ;
}

if($found==false) {
$user = new PlaceData();
	$user->name = $_POST["name"];
	$user->add();
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo el Aeropuerto " .$_POST["name"]."";
          $user->add();
echo "OK";
exit;
}

echo "NOT";
exit;	

elseif(isset($_GET["opt"]) && $_GET["opt"]=="add_offline"):

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data["name"])) {
  echo "ERROR";
  exit;
}

$base = new Database();
$con = $base->connect();
$name = $data["name"];
$sql = "SELECT name FROM place WHERE name = \"{$name}\"";
$query = $con->query($sql);

$found = false;
while($r = $query->fetch_array()){
	$found = true;
}

if (!$found) {
    $place = new PlaceData();
    $user->name = $_POST["name"];
	$user->add();

    $log = new ACData();
    $log->user_id = $_SESSION["user_id"];
    $log->accion = "Hizo el Aeropuerto " . $name;
    $log->add();

    echo "OK";
    exit;
}

echo "NOT";
exit;


elseif(isset($_GET["opt"]) && $_GET["opt"]=="upd"):
	$user = PlaceData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->update();
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el Aeropuerto " .$_POST["name"]."";
          $user->add();
	
echo 'true';

elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"):
$category = PlaceData::getById($_GET["id"]);
$category->del();
header('Location:./?view=places&opt=all');
endif;

?>