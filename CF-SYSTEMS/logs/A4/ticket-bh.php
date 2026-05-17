<?php
include "../core/controller/Core.php";
include "../core/controller/Database.php";
include "../core/controller/Executor.php";
include "../core/controller/Model.php";

include "../core/app/model/UserData.php";
include "../core/app/model/SellData.php";
include "../core/app/model/OperationData.php";
include "../core/app/model/PData.php";

include "../core/app/model/WData.php";
include "../core/app/model/CData.php";
include "../core/app/model/CFData.php";
include "../core/app/model/CFSData.php";
include "../core/app/model/CGData.php";
include "../core/app/model/FData.php";
include "../core/app/model/ProductData.php";
include "../core/app/model/StockData.php";
include "../core/app/model/PersonData.php";
include "../core/app/model/BoxData.php";
include "../core/app/model/SpendData.php";
include "../core/app/model/IncomeData.php";
include "../../CF-SYSTEMS/fpdf/fpdf.php";

session_start();
if(isset($_SESSION["user_id"])){ Core::$user = UserData::getById($_SESSION["user_id"]); }
$symbol =  StockData::getPrincipal()->currency;
if($symbol=="€"){ $symbol=chr(128); }
else if($symbol=="₡"){ 
//echo intval("€");
	$symbol=    '₡';
}


$stock = StockData::getPrincipal();

$products = null;
$sql = "select SQL_BIG_RESULT * from box";
$whereparams = array();

if(isset($_GET["start_at"]) && isset($_GET["finish_at"]) && $_GET["start_at"]!="" && $_GET["finish_at"]!=""){
	$whereparams[] = " ( date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]' ) ";

 $sql2 = $sql." where ".implode(" and ", $whereparams)." order by created_at desc";

$products = BoxData::getAllBySQL2($sql2);

}

$pdf = new FPDF($orientation='P',$unit='mm', array(450,210));

$pdf->AddPage();

//$pdf->setXY(5,0);
$plusforimage =0;
if($ticket_image!=""){
	$src = "../CF-SYSTEMS/storage/configuration/".$ticket_image;
	if(file_exists($src)){
		$pdf->Image($src,2,2,30);		
		
	}
}

$textypos = 10+$plusforimage;

$textypos+=8;
$pdf->setY(2);
$pdf->setX(35);
$pdf->SetFont('Arial','B',12); 
$pdf->Cell(5,$textypos,strtoupper($title));
   //Letra Arial, negrita (Bold), 

$textypos+=10;
$pdf->setX(35);
$pdf->SetFont('Arial','I',8); 
$pdf->Cell(5,$textypos,strtoupper($stock->address));
$pdf->setX(150);
$pdf->Cell(5,$textypos,"FECHA: ".date("d-m-Y ", strtotime($_GET["start_at"])).' -  '.date("d-m-Y", strtotime($_GET["finish_at"])));


$textypos+=8;
$pdf->setX(35);
$pdf->Cell(5,$textypos,strtoupper($stock->address2));

$textypos+=8;

	$src = "../CF-SYSTEMS/storage/redes-sociales/whatsapp.png";
	if(file_exists($src)){
		$pdf->Image($src,40,22,3);		
		
	}

		$src = "../CF-SYSTEMS/storage/redes-sociales/telefono.png";
	if(file_exists($src)){
		$pdf->Image($src,36,22,3);		
		
	}


$pdf->setX(43);
$pdf->Cell(5,$textypos,": ".strtoupper($stock->phone."; ".$stock->phone2));


$textypos+=10;
$pdf->setX(35);
$pdf->Cell(5,$textypos,"RNC: ".strtoupper($rnc));



$textypos+=10;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");
$textypos+=10;
$pdf->setX(69);
$pdf->Cell(5,$textypos,'REPORTE DE CORTE DE CAJA POR FECHA');

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

foreach($products as $box){
$sells = SellData::getByBoxId($box->id);

$total_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=2 and box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
	foreach($operations as $sell){
	$total_total += $sell->total-$sell->discount;
	    
	}

$Vtotal_total=0;
$operations = SellData::getAllBySQL(" where operation_type_id=2 and f_id=1 and p_id=1 and box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
	foreach($operations as $sell){
	$Vtotal_total += $sell->total-$sell->discount;
	    
	}
	
$RCtotal_total=0;
$operations = SellData::getAllBySQL(" where operation_type_id=2 and p_id=4 and box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
	foreach($operations as $sell){
	$RCtotal_total += $sell->total-$sell->discount;
	    
	}
$Dtotal_total=0;
$operations = SellData::getAllBySQL(" where operation_type_id=2 and f_id=2 and p_id!=4 and box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
	foreach($operations as $sell){
	$Dtotal_total += $sell->total-$sell->discount;
	    
	}

$Ctotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=2 and f_id=3 and p_id!=4 and box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
	foreach($operations as $sell){
	$Ctotal_total += $sell->total-$sell->discount;
	    
	}
$Ttotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=2 and f_id=5 and p_id!=4 and box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
	foreach($operations as $sell){
	$Ttotal_total += $sell->total-$sell->discount;
	    
	}

$DPStotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=2 and f_id=4 and p_id!=4 and box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
	foreach($operations as $sell){
	$DPStotal_total += $sell->total-$sell->discount;
	    
	}
//-------------------------- COMPRAS -----------------------------

$Rtotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=1 and box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
	foreach($operations as $sell){
	$Rtotal_total += $sell->total-$sell->discount;
	    
	}
	
$VRtotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=1  and f_id=1 and p_id!=4 and box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
	foreach($operations as $sell){
	$VRtotal_total += $sell->total-$sell->discount;
	    
	}

$CRestotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=1 and p_id=4 and box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
	foreach($operations as $sell){
	$CRestotal_total += $sell->total-$sell->discount;
	    
	}

$CRtotal_total= 0;
$operations = SellData::getAllBySQL(" where operation_type_id=1 and f_id=3 and p_id!=4 and box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
	foreach($operations as $sell){
	$CRtotal_total += $sell->total-$sell->discount;
	    
	}
	
$DRtotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=1 and f_id=2 and p_id!=4 and box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
	foreach($operations as $sell){
	$DRtotal_total += $sell->total-$sell->discount;
	    
	}

$DPRtotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=1 and f_id=4 and p_id!=4 and box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
	foreach($operations as $sell){
	$DPRtotal_total += $sell->total-$sell->discount;
	    
	}


$RTtotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=1 and f_id=5 and p_id!=4 and box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
	foreach($operations as $sell){
	$RTtotal_total += $sell->total-$sell->discount;
	    
	}

$Gtotal_total = 0;
$spends = SpendData::getAllBySQL(" where box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
foreach($spends as $sell){
	$gtotal=$sell->price;
	$Gtotal_total +=$gtotal;
	    
	}
$DPtotal_total = 0;
$deps = IncomeData::getAllBySQL(" where  box_id!=0 and date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]'");
foreach($deps as $sell){
	$dptotal=$sell->price;
	$DPtotal_total +=$dptotal;
}



}

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"ENTRADAS: ");
$pdf->setX(200);
$pdf->Cell(5,$textypos," +".$symbol." ".number_format($total_total,2,".",",") ,0,0,"R");

$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"SALIDAS: ");
$pdf->setX(200);
$pdf->Cell(5,$textypos," -".$symbol." ".number_format($Rtotal_total,2,".",",") ,0,0,"R");

$gaints=$total_total-$Rtotal_total;

$textypos+=10;

if ($gaints>0) {
$pdf->setX(4);
$pdf->Cell(5,$textypos,"GANANCIAS: ");
$pdf->setX(200);
$pdf->Cell(5,$textypos," ".$symbol." ".number_format($gaints,2,".",",") ,0,0,"R");
}else{
$pdf->setX(4);
$pdf->Cell(5,$textypos,"PERDIDAS: ");
$pdf->setX(200);
$pdf->Cell(5,$textypos," ".$symbol." ".number_format($gaints,2,".",",") ,0,0,"R");
}

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");


$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=10;
$pdf->setX(90);
$pdf->Cell(5,$textypos,'VENTAS');

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"EFECTIVO: ");
$pdf->setX(200);;
$pdf->Cell(5,$textypos,$symbol." ".number_format($Vtotal_total,2,".",",") ,0,0,"R");
$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"TRANSFERENCIA: ");
$pdf->setX(200);;
$pdf->Cell(5,$textypos,$symbol." ".number_format($Dtotal_total,2,".",",") ,0,0,"R");
$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"CHEQUE: ");
$pdf->setX(200);;
$pdf->Cell(5,$textypos,$symbol." ".number_format($Ctotal_total,2,".",",") ,0,0,"R");
$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"DEPOSITOS: ");
$pdf->setX(200);;
$pdf->Cell(5,$textypos,$symbol." ".number_format($DPStotal_total,2,".",",") ,0,0,"R");
$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"TARJETA: ");
$pdf->setX(200);;
$pdf->Cell(5,$textypos,$symbol." ".number_format($Ttotal_total,2,".",",") ,0,0,"R");
$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"CREDITO: ");
$pdf->setX(200);;
$pdf->Cell(5,$textypos,$symbol." ".number_format($RCtotal_total,2,".",",") ,0,0,"R");
$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"TOTAL: ");
$pdf->setX(200);;
$pdf->Cell(5,$textypos,$symbol." ".number_format($total_total,2,".",",") ,0,0,"R");

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");


$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=10;
$pdf->setX(90);
$pdf->Cell(5,$textypos,'COMPRAS');

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"EFECTIVO: ");
$pdf->setX(200);
$pdf->Cell(5,$textypos,$symbol." ".number_format($VRtotal_total,2,".",",") ,0,0,"R");
$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"TRANSFERENCIA: ");
$pdf->setX(200);
$pdf->Cell(5,$textypos,$symbol." ".number_format($DRtotal_total,2,".",",") ,0,0,"R");
$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"CHEQUE: ");
$pdf->setX(200);
$pdf->Cell(5,$textypos,$symbol." ".number_format($CRtotal_total,2,".",",") ,0,0,"R");
$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"DEPOSITOS: ");
$pdf->setX(200);
$pdf->Cell(5,$textypos,$symbol." ".number_format($DPRtotal_total,2,".",",") ,0,0,"R");
$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"TARJETA: ");
$pdf->setX(200);
$pdf->Cell(5,$textypos,$symbol." ".number_format($RTtotal_total,2,".",",") ,0,0,"R");
$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"CREDITO: ");
$pdf->setX(200);
$pdf->Cell(5,$textypos,$symbol." ".number_format($CRestotal_total,2,".",",") ,0,0,"R");
$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,"TOTAL: ");
$pdf->setX(200);
$pdf->Cell(5,$textypos,$symbol." ".number_format($Rtotal_total,2,".",",") ,0,0,"R");

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");


$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=10;
$pdf->setX(90);
$pdf->Cell(5,$textypos,'GASTOS');

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,'TOTAL:');
$pdf->setX(200);
$pdf->Cell(5,$textypos,$symbol." ".number_format($Gtotal_total,2,".",",") ,0,0,"R");

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");


$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=10;
$pdf->setX(90);
$pdf->Cell(5,$textypos,'INGRESOS');

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=10;
$pdf->setX(4);
$pdf->Cell(5,$textypos,'TOTAL:');
$pdf->setX(200);
$pdf->Cell(5,$textypos,$symbol." ".number_format($DPtotal_total,2,".",",") ,0,0,"R");

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$textypos+=6;
$pdf->setX(3.3);
$pdf->Cell(5,$textypos,"________________________________________________________________________________________________________________________________");

$pdf->output();
