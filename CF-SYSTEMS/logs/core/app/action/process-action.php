<?php
////////////////////////////////////////// LOGIN 
if(isset($_GET["opt"]) && $_GET["opt"]=="box"){

$sells = null;
$sells = BookingData::getSellsUnBoxed(StockData::getPrincipal()->id);


	$box = new BoxData();
	$box->user_id = Core::$user->id; 
	$box->stock_id = StockData::getPrincipal()->id;
	$b = $box->add();

if(count($sells)){	
	foreach($sells as $sell){
		$sell->box_id = $b[1];
		$sell->update_box();
	}
}

$spends = SpendData::getAllUnBoxed($_SESSION["user_id"]);
if(count($spends)){
	foreach($spends as $sell){
		$sell->box_id = $b[1];
		$sell->update_box();
	}
}
$income = MaintenanceData::getAllUnBoxed();
if(count($income)){
	foreach($income as $sell){
		$sell->box_id = $b[1];
		$sell->update_box();
	}
}


	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo el corte caja " .$b[1];"";
          $user->add();

          header('location:./?view=b&id='.$b[1]);

////////////////////////////////////////////////////////////////////// INPUT //////////////////////////////////////
}
?>