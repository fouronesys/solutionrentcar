<?php
if(isset($_GET["opt"]) && $_GET["opt"] == "add"):
    
    $vehicle_id = $_POST['vehicle_id']; // id del carro
    $gps_id     = $_POST['gps_id'];     // id del gps

    $user = CarsData::getById($vehicle_id);
    $user->gps_id = $gps_id;
    $user->update_device();

    echo "true";
    exit;


elseif(isset($_GET["opt"]) && $_GET["opt"] == "addgps"):

    $user = new DeviceData;
    $user->name = $_POST['name'];
    $user->imei = $_POST['imei']; 
    $user->add();
    
    
    $id_speds = DeviceData::getAllByID();
    $spends = $id_speds[0]->id!=null?$id_speds[0]->id:0;
    
    $user2 = new DeviceData; 
    $user2->gps_id = $spends;
    $user2->latitude = $_POST['latitude'];
    $user2->longitude = $_POST['longitude']; 
    $user2->addStore();

    echo "true";
    exit;
    
elseif(isset($_GET["opt"]) && $_GET["opt"] == "upd"):
    
    $vehicle_id = $_POST['vehicle_id']; // id del carro
    $gps_id     = $_POST['gps_id'];     // id del gps

    $user = CarsData::getById($vehicle_id);
    $user->gps_id = $gps_id;
    $user->update_device();

    echo "true";
    exit;



endif;?>
