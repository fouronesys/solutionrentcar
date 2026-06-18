<?php

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ConfigurationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/DData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PData.php";
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

include "../vendor/autoload.php";

$operations = array();
$rnc = ConfigurationData::getByPreffix("rnc")->val;
$iva_name = ConfigurationData::getByPreffix("imp-name")->val;
$iva_val = ConfigurationData::getByPreffix("imp-val")->val;

		    if($_GET["c_id"]==""){
			$operations = SellData::getAllByDateOp2($_GET["sd"],$_GET["ed"],2);
			}
			else if($_GET["c_id"]!=""){
			$operations = SellData::getAllByDateBCOp2($_GET["c_id"],$_GET["sd"],$_GET["ed"],2);
			}

	//le informamos que será un archivo txt
	header('Content-type: application/txt');
	
	//también le damos un nombre
	header('Content-Disposition: attachment; filename="DGII_F_607"'.date("Ym", strtotime($_GET["sd"])).'.txt');
	
	//generamos el contenido del archivo
	echo '607|'.$rnc.'|'.date("Ym", strtotime($_GET["sd"])).'|'.count($operations);
	echo"\r\n";

    foreach($operations as $product):
	if($product->person_id!=null){$c= $product->getPerson(); $psn=$c->no;}else{$psn="";}
	if($product->person_id!=null){$c= $product->getPerson(); $pst=$c->is_type;}else{$pst="";}

	if($product->type_ig>0){$tp=$product->type_ig;}else{$tp="";}

	if($product->c_id==1){$cf= $product->getCF(); $cd= "B0".$cf->name_in; $ct=$c->created_at;}
    elseif($product->c_id==2){$cfs= $product->getCFS(); $cd= "B0".$cfs->name_in; $ct=$c->created_at;}
    elseif($product->c_id==3){$cg= $product->getCG(); $cd= "B".$cg->name_in; $ct=$c->created_at;}
    elseif($product->c_id==4){$cnc= $product->getCNC(); $cd= "B0".$cnc->name_in; $ct=$c->created_at;}
    elseif($product->c_id==8){$cnd= $product->getCND(); $cd= "B0".$cnd->name_in; $ct=$c->created_at;}
    elseif($product->c_id==9){$ccp= $product->getCCP(); $cd= "B".$ccp->name_in; $ct=$c->created_at;}
    elseif($product->c_id==10){$cgt= $product->getCGT(); $cd= "B".$cgt->name_in; $ct=$c->created_at;}
    elseif($product->c_id==11){$crs= $product->getCRS(); $cd= "B".$crs->name_in; $ct=$c->created_at;}
    else{$cd="";}

	if($product->f_id==1){$f_1d= number_format($product->total,2,'.',',');}else{$f_1d="";}
	if($product->f_id==2 || $product->f_id==3 || $product->f_id==4)
	{$f_2d= number_format($product->total,2,'.',',');}else{$f_2d="";}
	if($product->f_id==5){$f_5d= number_format($product->total,2,'.',',');}else{$f_5d="";}
	if($product->f_id==6){$f_6d= number_format($product->total,2,'.',',');}else{$f_6d="";}
	if($product->p_id==4){$p_4d= number_format($product->total,2,'.',',');}else{$p_4d="";}

    echo $psn.'|'.$pst.'|'.$cd.'|'.$tp.'|'.date("Ymd", strtotime($ct)).'||'.number_format($product->total/1.18,2,'.',',').'|'.number_format((($product->total/1.18)*$iva_val/100),2,'.',',').'|||||||'.$f_1d.'|'.$f_2d.'|'.$f_5d.'|'.$p_4d.'|'.$f_6d.'||';
    echo"\r\n";
	endforeach;
?>
