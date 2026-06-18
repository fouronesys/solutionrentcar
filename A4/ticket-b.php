<?php
include "../core/controller/Core.php";
include "../core/controller/Database.php";
include "../core/controller/Executor.php";
include "../core/controller/Model.php";

include "../core/app/model/UserData.php";
include "../core/app/model/SellData.php";
include "../core/app/model/OperationData.php";
include "../core/app/model/PData.php";
include "../core/app/model/OpeningData.php";
include "../core/app/model/WData.php";
include "../core/app/model/CData.php";
include "../core/app/model/TGData.php";
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




$box = BoxData::getById($_GET["id"]);
$stock = StockData::getPrincipal();
$repair = SellData::getRepairByBoxId($_GET["id"]);
$deps = IncomeData::getIncomeByBoxId($_GET["id"]);

$pdf = new FPDF($orientation='P',$unit='mm', 'A4');

$pdf->AddPage();

//$pdf->setXY(5,0);
$plusforimage =0;
if($ticket_image!=""){
	$src = "../CF-SYSTEMS/storage/configuration/".$ticket_image;
	if(file_exists($src)){
		$pdf->Image($src,4,10,40);		
		
	}
}

$textypos = 10+$plusforimage;

$textypos+=8;
$pdf->setY(2);
$pdf->setX(45);
$pdf->SetFont('Arial','B',12); 
$pdf->Cell(5,$textypos,strtoupper($title));
   //Letra Arial, negrita (Bold), 

$textypos+=10;
$pdf->setX(45);
$pdf->SetFont('Arial','B',8); 
$pdf->Cell(5,$textypos,strtoupper($stock->address." ".$stock->address2));
$pdf->setX(150);
$pdf->Cell(5,$textypos,"FECHA: ".date("d-m-Y", strtotime($box->created_at)));
$pdf->SetFont('Arial','I',8); 
$textypos+=8;

	$src = "../CF-SYSTEMS/storage/redes-sociales/whatsapp.png";
	if(file_exists($src)){
		$pdf->Image($src,50,18.5,3);		
		
	}

		$src = "../CF-SYSTEMS/storage/redes-sociales/telefono.png";
	if(file_exists($src)){
		$pdf->Image($src,45,18.5,3);		
		
	}

		$src = "../CF-SYSTEMS/storage/redes-sociales/instagram.png";
	if(file_exists($src)){
		$pdf->Image($src,95,18.5,3);		
		
	}


$pdf->setX(53);
$pdf->Cell(5,$textypos,": ".strtoupper($stock->phone."; ".$stock->phone2));

$pdf->setX(98);
$pdf->Cell(5,$textypos,": ".strtoupper($stock->field2));

$pdf->setX(150);
$stocks = StockData::getAll();
foreach ($stocks as $st) {
if ($st->id==$box->stock_id) {
$pdf->Cell(5,$textypos,"SUCURSAL: ".strtoupper($st->name));
}}


$textypos+=10;
$pdf->setX(45);
$pdf->Cell(5,$textypos,"RNC: ".strtoupper($rnc));





$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,46,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,46,"________________________________________________________________________________________________________________________________");
$pdf->Ln(3);
$pdf->setX(75);
$pdf->Cell(5,49,'REPORTE DE CORTE DE CAJA # '.$_GET["id"]);

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,46,"________________________________________________________________________________________________________________________________");



//-------------------------- REPARACIONES -----------------------------
$RVtotal_total = 0;
$operations = SellData::getAllBySQL(" where is_repair=1 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$RVtotal_total += $sell->total-$sell->discount;
	    
	}
$RAtotal_total = 0;
$operations = SellData::getAllBySQL(" where is_repair=1 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$RAtotal_total += $sell->payment;
	    
	}

$RRCtotal_total=0;
$operations = SellData::getAllBySQL(" where is_repair=1 and p_id=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$RRCtotal_total += $sell->total-$sell->discount;
	    
	}

//-------------------------- VENTAS -----------------------------
$total_total = 0;
$DCtotal_total = 0;
$operation = SellData::getAllBySQL(" where operation_type_id=2 and is_repair=0 and box_id=".$_GET["id"]);
	foreach($operation as $sp){
	$total_total += $sp->total-$sp->discount;
	$DCtotal_total += $sp->discount;
	    
	}



$Vtotal_total=0;
$operations = SellData::getAllBySQL(" where operation_type_id=2 and is_repair=0 and f_id=1 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$Vtotal_total += $sell->total-$sell->discount;
	    
	}
	
$RCtotal_total=0;
$operations = SellData::getAllBySQL(" where operation_type_id=2 and is_repair=0 and p_id=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$RCtotal_total += $sell->total-$sell->discount;
	    
	}
$Dtotal_total=0;
$operations = SellData::getAllBySQL(" where operation_type_id=2 and is_repair=0 and f_id=2 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$Dtotal_total += $sell->total-$sell->discount;
	    
	}

$Ctotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=2 and is_repair=0 and f_id=3 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$Ctotal_total += $sell->total-$sell->discount;
	    
	}
$Ttotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=2 and is_repair=0 and f_id=5 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$Ttotal_total += $sell->total-$sell->discount;
	    
	}

$DPStotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=2 and is_repair=0 and f_id=4 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$DPStotal_total += $sell->total-$sell->discount;
	    
	}
//-------------------------- COMPRAS -----------------------------

$Rtotal_total = 0;
$res = SellData::getAllBySQL(" where operation_type_id=1 and box_id=".$_GET["id"]);
	foreach($res as $sell){
	$Rtotal_total += $sell->total-$sell->discount;
	    
	}
	
$VRtotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=1  and f_id=1 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$VRtotal_total += $sell->total-$sell->discount;
	    
	}

$CRestotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=1 and p_id=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$CRestotal_total += $sell->total-$sell->discount;
	    
	}

$CRtotal_total= 0;
$operations = SellData::getAllBySQL(" where operation_type_id=1 and f_id=3 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$CRtotal_total += $sell->total-$sell->discount;
	    
	}
	
$DRtotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=1 and f_id=2 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$DRtotal_total += $sell->total-$sell->discount;
	    
	}

$DPRtotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=1 and f_id=4 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$DPRtotal_total += $sell->total-$sell->discount;
	    
	}


$RTtotal_total = 0;
$operations = SellData::getAllBySQL(" where operation_type_id=1 and f_id=5 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($operations as $sell){
	$RTtotal_total += $sell->total-$sell->discount;
	    
	}

//-------------------------- GASTOS -----------------------------

$Gtotal_total = 0;
$spends = SpendData::getAllBySQL(" where box_id=".$_GET["id"]);
	foreach($spends as $sell){
	$Gtotal_total += $sell->price;
	    
	}
	
$VGtotal_total = 0;
$spend = SpendData::getAllBySQL(" where f_id=1 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($spend as $sell){
	$VGtotal_total += $sell->price;
	    
	}

$CGtotal_total = 0;
$spend = SpendData::getAllBySQL(" where p_id=4 and box_id=".$_GET["id"]);
	foreach($spend as $sell){
	$CGtotal_total += $sell->price;
	    
	}

$PGtotal_total= 0;
$spend = SpendData::getAllBySQL(" where  f_id=3 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($spend as $sell){
	$PGtotal_total += $sell->price;
	    
	}
	
$DGtotal_total = 0;
$spend = SpendData::getAllBySQL(" where f_id=2 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($spend as $sell){
	$DGtotal_total += $sell->price;
	    
	}

$DPGtotal_total = 0;
$spend = SpendData::getAllBySQL(" where f_id=4 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($spend as $sell){
	$DPGtotal_total += $sell->price;
	    
	}


$RGtotal_total = 0;
$spend = SpendData::getAllBySQL(" where f_id=5 and p_id!=4 and box_id=".$_GET["id"]);
	foreach($spend as $sell){
	$RTtotal_total += $sell->price;
	    

	}
$DPtotal_total = 0;
foreach($deps as $sell){
	$dptotal=$sell->price;
	$DPtotal_total +=$dptotal;
}

$RPtotal_total = 0;
foreach($repair as $sell){
	$rptotal=$sell->total+$sell->payment;
	$RPtotal_total +=$rptotal;
}


$ops = OpeningData::getByBoxId($_GET["id"]);
$Ptotal_total = 0;
$PCtotal_total = 0;
foreach($ops as $op){
	$ptotal=$op->opening;
	$pctotal=$op->closing;
	$Ptotal_total =$ptotal;
	$PCtotal_total =$pctotal;
}


$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,46,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,48,"ENTRADAS: ");
$pdf->setX(200);
$pdf->Cell(5,48," +".$symbol." ".number_format($total_total+$RPtotal_total+$DPtotal_total,2,".",",") ,0,0,"R");
$sfd=$total_total+$RPtotal_total;
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,49,"SALIDAS: ");
$pdf->setX(200);
$pdf->Cell(5,49," -".$symbol." ".number_format($Rtotal_total+$Gtotal_total,2,".",",") ,0,0,"R");

$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,50,"APERTURA: ");
$pdf->setX(200);
$pdf->Cell(5,50," ".$symbol." ".number_format($ptotal,2,".",",") ,0,0,"R");

$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,51,"CIERRE: ");
$pdf->setX(200);
$pdf->Cell(5,51," ".$symbol." ".number_format($pctotal,2,".",",") ,0,0,"R");

$pdf->Ln(3);
if ($pctotal>$sfd) {
$pdf->setX(4);
$pdf->Cell(5,52,"SOBRANTE: ");
$pdf->setX(200);
$pdf->Cell(5,52," ".$symbol." ".number_format($sfd-$pctotal,2,".",",") ,0,0,"R");
}elseif ($pctotal<$sfd) {
$pdf->setX(4);
$pdf->Cell(5,52,"FALTANTE: ");
$pdf->setX(200);
$pdf->Cell(5,52," ".$symbol." ".number_format($sfd-$pctotal,2,".",",") ,0,0,"R");
}elseif ($pctotal==$sfd) {

	
}

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,48,"________________________________________________________________________________________________________________________________");


$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,48,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(90);
$pdf->Cell(5,50,'VENTAS');

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,48,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,48,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,50,"EFECTIVO: ");
$pdf->setX(200);;
$pdf->Cell(5,50,$symbol." ".number_format($Vtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,52,"TRANSFERENCIA: ");
$pdf->setX(200);;
$pdf->Cell(5,52,$symbol." ".number_format($Dtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,54,"CHEQUE: ");
$pdf->setX(200);;
$pdf->Cell(5,54,$symbol." ".number_format($Ctotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,56,"DEPOSITOS: ");
$pdf->setX(200);
$pdf->Cell(5,56,$symbol." ".number_format($DPStotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,58,"TARJETA: ");
$pdf->setX(200);;
$pdf->Cell(5,58,$symbol." ".number_format($Ttotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,60,"CREDITO: ");
$pdf->setX(200);;
$pdf->Cell(5,60,$symbol." ".number_format($RCtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,62,"DESCUENTO: ");
$pdf->setX(200);;
$pdf->Cell(5,62,$symbol." ".number_format($DCtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,64,"TOTAL: ");
$pdf->setX(200);;
$pdf->Cell(5,64,$symbol." ".number_format($total_total,2,".",",") ,0,0,"R");


$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,62,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,62,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,64,'FACTURA                    PRODUCTO                    CANTIDAD                  PRECIO SALIDA                   VENDEDOR                    TOTAL                    PAGO');

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,62,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,62,"________________________________________________________________________________________________________________________________");


foreach($operation as $sell){
$op = $sell->getOperations();
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,66,"#".strtoupper($sell->id));
$pdf->setX(31);
$pdf->Cell(5,66,strtoupper($op->getProduct()->name));
$pdf->setX(70);
$pdf->Cell(5,66,strtoupper($op->q));
$pdf->setX(92);
$pdf->Cell(5,66,$symbol." ".number_format($op->price_out,2,".",","));
$pdf->setX(129);
$pdf->Cell(5,66,strtoupper($sell->getUser()->name." ".$sell->getUser()->lastname));
$pdf->setX(160);
$pdf->Cell(5,66,$symbol." ".number_format(($op->q*$op->price_out)-$op->discount),2,".",",");
$pdf->setX(185);
$pdf->Cell(5,66,strtoupper($sell->getP()->name));
$pdf->Ln(4);
}

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,62,"________________________________________________________________________________________________________________________________");


$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,62,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(90);
$pdf->Cell(5,64,'COMPRAS');

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,62,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,62 ,"________________________________________________________________________________________________________________________________");

$pdf->Ln(4);
$pdf->setX(4);
$pdf->Cell(5,62,"EFECTIVO: ");
$pdf->setX(200);
$pdf->Cell(5,62,$symbol." ".number_format($VRtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,64,"TRANSFERENCIA: ");
$pdf->setX(200);
$pdf->Cell(5,64,$symbol." ".number_format($DRtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,66,"CHEQUE: ");
$pdf->setX(200);
$pdf->Cell(5,66,$symbol." ".number_format($CRtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,68,"DEPOSITOS: ");
$pdf->setX(200);
$pdf->Cell(5,68,$symbol." ".number_format($DPRtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,70,"TARJETA: ");
$pdf->setX(200);
$pdf->Cell(5,70,$symbol." ".number_format($RTtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,72,"CREDITO: ");
$pdf->setX(200);
$pdf->Cell(5,72,$symbol." ".number_format($CRestotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,74,"TOTAL: ");
$pdf->setX(200);
$pdf->Cell(5,74,$symbol." ".number_format($Rtotal_total,2,".",",") ,0,0,"R");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,72,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,72,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,74,'FACTURA                  PRODUCTO                    CANTIDAD                  PRECIO ENTRADA                VENDEDOR                    TOTAL                    PAGO');

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,72,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,72,"________________________________________________________________________________________________________________________________");


foreach($res as $sell){
$op = $sell->getOperations();
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,74,"#".strtoupper($sell->id));
$pdf->setX(31);
$pdf->Cell(5,74,strtoupper($op->getProduct()->name));
$pdf->setX(70);
$pdf->Cell(5,74,strtoupper($op->q));
$pdf->setX(92);
$pdf->Cell(5,74,$symbol." ".number_format($op->price_in,2,".",","));
$pdf->setX(129);
$pdf->Cell(5,74,strtoupper($sell->getUser()->name." ".$sell->getUser()->lastname));
$pdf->setX(160);
$pdf->Cell(5,74,$symbol." ".number_format(($op->q*$op->price_in)-$op->discount),2,".",",");
$pdf->setX(185);
$pdf->Cell(5,74,strtoupper($sell->getP()->name));
$pdf->Ln(4);
}

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,72,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,72,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(91);
$pdf->Cell(5,74,'GASTOS');


$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,72,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,72,"________________________________________________________________________________________________________________________________");
$pdf->Ln(4);
$pdf->setX(4);
$pdf->Cell(5,72,"EFECTIVO: ");
$pdf->setX(200);
$pdf->Cell(5,72,$symbol." ".number_format($VGtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,74,"TRANSFERENCIA: ");
$pdf->setX(200);
$pdf->Cell(5,74,$symbol." ".number_format($DGtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,76,"CHEQUE: ");
$pdf->setX(200);
$pdf->Cell(5,76,$symbol." ".number_format($PGtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,78,"DEPOSITOS: ");
$pdf->setX(200);
$pdf->Cell(5,78,$symbol." ".number_format($DPGtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,80,"TARJETA: ");
$pdf->setX(200);
$pdf->Cell(5,80,$symbol." ".number_format($RGtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,82,"CREDITO: ");
$pdf->setX(200);
$pdf->Cell(5,82,$symbol." ".number_format($CGtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,84,"TOTAL: ");
$pdf->setX(200);
$pdf->Cell(5,84,$symbol." ".number_format($Gtotal_total,2,".",",") ,0,0,"R");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,82,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,82,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,84,'TIPO                         TIPO DE GASTOS                                                 PROVEEDOR                                                            TOTAL                        PAGO');

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,82,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,82,"________________________________________________________________________________________________________________________________");

foreach($spends as $sell){
$pdf->Ln(3);
$pdf->setX(4);
if($sell->kind==1){
$pdf->setX(4);
$pdf->Cell(5,84,"GASTO");}
else if($sell->kind==2){
$pdf->setX(4);
$pdf->Cell(5,84,"DEVOLUCION");} 
$pdf->setX(31);

$pdf->setX(70);

$pdf->setX(92);
$pdf->Cell(5,84,$symbol." ".number_format($sell->price,2,".",","));
$pdf->setX(185);
$pdf->Cell(5,84,strtoupper($sell->getP()->name));
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,86,"CONCEPTO: ".strtoupper($sell->name));

$pdf->Ln(4);
}

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,82,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,82,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(88);
$pdf->Cell(5,84,'REPARACIONES');

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,84,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,84,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,86,"EFECTIVO: ");
$pdf->setX(200);
$pdf->Cell(5,86,$symbol." ".number_format($RVtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,88,"ABONO: ");
$pdf->setX(200);
$pdf->Cell(5,88,$symbol." ".number_format($RAtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,90,"CREDITO: ");
$pdf->setX(200);
$pdf->Cell(5,90,$symbol." ".number_format($RRCtotal_total,2,".",",") ,0,0,"R");
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,92,"TOTAL: ");
$pdf->setX(200);
$pdf->Cell(5,92,$symbol." ".number_format($RPtotal_total,2,".",",") ,0,0,"R");

$pdf->Ln(4);
$pdf->setX(3.3);
$pdf->Cell(5,90,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,90,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,93,'FACTURA                      NOMBRE                      MODELO                      TECNICO                      REPARACIONES                      TOTAL                      RECIBIDO');

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,90,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,90,"________________________________________________________________________________________________________________________________");


foreach($repair as $rp){
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,92,"#".strtoupper($rp->id));
$pdf->setX(33);
$pdf->Cell(5,92,strtoupper($rp->getPerson()->name." ".$rp->getPerson()->lastname));
$pdf->setX(63);
$pdf->Cell(5,92,strtoupper($rp->model));
$pdf->setX(93);
$pdf->Cell(5,92,strtoupper($rp->getTech()->name." ".$rp->getTech()->lastname));
$pdf->setX(123);
$pdf->Cell(5,92,strtoupper($rp->other));
$pdf->setX(163);
$pdf->Cell(5,92,strtoupper($rp->total));
$pdf->setX(189);
$pdf->Cell(5,92,strtoupper($rp->getP()->name));
$pdf->Ln(4);
}


$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,90,"________________________________________________________________________________________________________________________________");


$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,90,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(90);
$pdf->Cell(5,92,'INGRESOS');

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,90,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,90,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,92,'TOTAL:');
$pdf->setX(200);
$pdf->Cell(5,92,$symbol." ".number_format($DPtotal_total,2,".",",") ,0,0,"R");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,90,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,90,"________________________________________________________________________________________________________________________________");


foreach($deps as $dp){
$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,94,strtoupper($dp->name));
$pdf->setX(200);
$pdf->Cell(5,92,$symbol." ".number_format($dp->price,2,".",",") ,0,0,"R");
$pdf->Ln(4);
}



$pdf->Ln(3);
$pdf->setX(3.3);
$pdf->Cell(5,90,"________________________________________________________________________________________________________________________________");

$pdf->Ln(3);
$pdf->setX(4);
$pdf->Cell(5,94,'REALIZADO POR: '.strtoupper(Core::$user->name." ".Core::$user->lastname));
$pdf->output();