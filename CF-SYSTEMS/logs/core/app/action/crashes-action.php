<?php
if(isset($_GET["opt"]) && $_GET["opt"] == "add"):

$car_id  = intval($_POST['car_id']);
$user_id = intval($_POST['person_id']);
$type_id = $_POST['type_id'];
$price = $_POST['price'];

$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT * from crashes where car_id= \"".$car_id."\" and person_id= \"".$user_id."\"";
//print $sql;
$query = $con->query($sql);
$found = false;
while($r = $query->fetch_array()){
	$found = true ;
	$id_key = $r['id'];
}
 
if($found==false) {
   // Si no existe → insertar con tu método add()
    $obj = new CrashesData();
    $obj->car_id  = $car_id;
    $obj->person_id = $user_id;
    $obj->type_id = $type_id;
    $obj->price = $price;
    $obj->add();

}else{
    // Si existe → actualiza con tu método update()
    $obj = CrashesData::getById($id_key);
    $obj->car_id  = $car_id;
    $obj->person_id = $user_id;
    $obj->type_id = $type_id;
    $obj->price = $price;
    $obj->update();
}
    echo "OK";
    exit;

elseif(isset($_GET["opt"]) && $_GET["opt"] == "add_offline"):

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data || !isset($data["car_id"])) {
        echo "ERROR";
        exit;
    }

    
$car_id  = intval($_POST['car_id']);
$user_id = intval($_POST['person_id']);
$type_id = $_POST['type_id'];
$price = $_POST['price'];

$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT * from crashes where car_id= \"".$car_id."\" and person_id= \"".$user_id."\"";
//print $sql;
$query = $con->query($sql);
$found = false;
while($r = $query->fetch_array()){
	$found = true ;
	$id_key = $r['id'];
}
 
if($found==false) {
   // Si no existe → insertar con tu método add()
    $obj = new CrashesData();
    $obj->car_id  = $car_id;
    $obj->person_id = $user_id;
    $obj->type_id = $type_id;
    $obj->price = $price;
    $obj->add();

}else{
    // Si existe → actualiza con tu método update()
    $obj = CrashesData::getById($id_key);
    $obj->car_id  = $car_id;
    $obj->person_id = $user_id;
    $obj->type_id = $type_id;
    $obj->price = $price;
    $obj->update();
}
    echo "OK";
    exit;
    
endif;?>
