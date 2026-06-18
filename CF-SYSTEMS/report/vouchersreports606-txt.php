<?php

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ConfigurationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/DData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/FData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PRData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/UserData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CFData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CFSData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CGData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CNCData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CNDData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CRSData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/TGData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SGData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SpendData.php";

include "../vendor/autoload.php";


$rnc = ConfigurationData::getByPreffix("rnc")->val;
$iva_name = ConfigurationData::getByPreffix("imp-name")->val;
$iva_val = ConfigurationData::getByPreffix("imp-val")->val;
$cp = TGData::getAll();
$cs = SGData::getAll();
$tp= FData::getAll();

$operations = array();
		    if($_GET["c_id"]==""){
			$operations = SellData::getAllByDateOp2($_GET["sd"],$_GET["ed"],1);
			}
			else if($_GET["c_id"]!=""){
			$operations = SellData::getAllByDateBCOp2($_GET["c_id"],$_GET["sd"],$_GET["ed"],1);
			}  

$products= array();
			if($_GET["c_id"]==""){
			$products = SpendData::getAllByDateOp2($_GET["sd"],$_GET["ed"]);
			}
			else if($_GET["c_id"]!=""){
			$products= SpendData::getAllByDateBCOp2($_GET["c_id"],$_GET["sd"],$_GET["ed"]);
			}



	//le informamos que será un archivo txt
	header('Content-type: application/txt');
	
	//también le damos un nombre
	header('Content-Disposition: attachment; filename="DGII_F_606"'.date("Ym", strtotime($_GET["sd"])).'.txt');
	
	//generamos el contenido del archivo
	$counts=(count($products)+count($operations));
	echo '606|'.$rnc.'|'.date("Ym", strtotime($_GET["sd"])).'|'.$counts;
	echo"\r\n";

    foreach($operations as $product):
	if($product->person_id!=null){$c= $product->getPerson(); $psn=$c->no;}

	foreach ($cp as $op ) {if ($product->type_g == $op->id) {$pst=$op->name;}else{$pst="";}}	
	foreach ($tp as $pt ) {if ($product->f_id == $pt->id and $product->p_id!=4) {$ppt="0".$pt->id;}
	elseif ($product->f_id == $pt->id and $product->p_id==4) {$ppt="04";}}

    echo $psn.'|'.$pst.'|'.$product->voucher.'||'.date("Ym", strtotime($product->created_at)).'|'.date("d", strtotime($product->created_at)).'|'.number_format($product->total/1.18,2,'.',',').'|'.number_format($product->total/1.18,2,'.',',').'|'.number_format((($product->total/1.18)*$iva_val/100),2,'.',',').'|'.number_format((($product->total/1.18)*$iva_val/100),2,'.',',').'|||||||||||'.$ppt;
    echo"\r\n";
	endforeach;

///////////////////////////////////////////////////////////////////////////////////////////////////////

	 foreach($products as $operation):
	if($operation->person_id!=null){$c= $operation->getPerson(); $osn=$c->no; $type=$c->is_type;}else{$osn="";}
	if($operation->person_id!=null){$c= $operation->getPerson(); $isn=$c->is_id; $c->is_id;}else{$isn="";}

	foreach ($cp as $op ) {if ($operation->type_g == $op->id) {$ost=$op->name;}else{$ost="";}}	
	foreach ($cs as $op ) {if ($operation->type_sg == $op->id) {$ostg=$op->name;}else{$ostg="";}}
	foreach ($tp as $pt ) {if ($operation->f_id == $pt->id and $operation->p_id!=4) {$opn="0".$pt->id;}elseif ($operation->f_id == $pt->id and $operation->p_id==4) {$ppt="04";}}

	if($type==1){$tcs=$operation->price;}else{$tcs="0";}
	if($type==2){$tcn=$operation->price;}else{$tcn="0";}

	$tpv=($tcs+$tcn);

    echo $osn.'|'.$isn.'|'.$ost.'|'.$operation->voucher_code.'||'.date("Ym", strtotime($operation->created_at)).'|'.date("d", strtotime($operation->created_at)).'|'.number_format($tcs/1.18,2,'.',',').'|'.number_format($tcn/1.18,2,'.',',').'|'.number_format((($tpv/1.18)*$iva_val/100),2,'.',',').'|'.$operation->itbis_ret.'||'.number_format((($tpv/1.18)*$iva_val/100),2,'.',',').'|||'.$ostg.'|'.$operation->imp_rent.'|||||'.$opn;
    echo"\r\n";
	endforeach;



?>
