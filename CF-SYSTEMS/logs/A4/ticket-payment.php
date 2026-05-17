<?php
include "../core/controller/Core.php";
include "../core/controller/Database.php";
include "../core/controller/Executor.php";
include "../core/controller/Model.php";


include "../core/app/model/PaymentData.php";
include "../core/app/model/PaymentTypeData.php";
include "../core/app/model/BookingData.php";
include "../core/app/model/StockData.php";
include "../core/app/model/PersonData.php";
include "../core/app/model/UserData.php";
include "../core/app/model/BrandData.php";
include "../core/app/model/CarsData.php";
include "../core/app/model/ColorData.php";
include "../core/app/model/CategoryData.php";
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
$client = PersonData::getById($_GET["id"]);

$clients = PaymentData::getAllByClientId2($client->id);

$pdf = new FPDF($orientation='P',$unit='mm',"A4");

$pdf->AddPage();

include('../core/app/layouts/ticketheader.php');

$pdf->Ln(23);
$pdf->setX(6);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->setX(108);
$pdf->Cell(5,15,'____________________________________________________________');
$pdf->Ln(10.5);
$pdf->setX(2);
$pdf->MultiCell(200,2,'HISTORIAL DE PAGO DEL CLIENTE',0,'C');
$pdf->ln(-7.5);
$pdf->setX(6);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->setX(108);
$pdf->Cell(5,15,'____________________________________________________________');
$pdf->Ln(10);
$pdf->setX(6);
$pdf->MultiCell(200,2.5,"NOMBRE: ".utf8_decode(strtoupper($client->name)), 0, 'L');
$pdf->Ln(-5);
$pdf->setX(6);
$pdf->Cell(5,15,"TEL: ".strtoupper($client->phone));
$pdf->Ln(10);
$pdf->setX(6);
$pdf->MultiCell(200,2.5,"DIRECCION: ".utf8_decode(strtoupper($client->address)), 0, 'L');

$pdf->Ln(-7);
$pdf->setX(6);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->setX(108);
$pdf->Cell(5,15,'____________________________________________________________');

$pdf->Ln(4);
$pdf->setX(5);
$pdf->Cell(5,15,'  TIPO                                                                                                                         VALOR');

$pdf->Ln(1);
$pdf->setX(6);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->setX(108);
$pdf->Cell(5,15,'____________________________________________________________');
foreach($clients as $clien){

$pdf->Ln(6);
$pdf->setX(6);
$pdf->Cell(5,15,strtoupper($clien->getPaymentType()->name).":");
$pdf->setX(108);
$pdf->Cell(5,15,"$symbol ".number_format(abs($clien->val),2,".",","));

$pdf->setX(160);
$pdf->Cell(5,15,"FECHA: ".strtoupper($clien->created_at));
$pdf->Ln(3);
}

$sells = BookingData::getCreditByClientId($client->id,StockData::getPrincipal()->id);
$sellx = BookingData::getCreditByCarsId($client->id,StockData::getPrincipal()->id);


$total=0;
foreach ($sells as $sell) {
$tx = PaymentData::sumBySellId2($sell->id,StockData::getPrincipal()->id)->total;
if($tx>0){
$total+=$tx;
}
}
$pdf->Ln(3);
$pdf->setX(6);
$pdf->Cell(5,15,"SALDO PENDIENTE: ");
$pdf->setX(108);
$pdf->Cell(5,15,"$symbol ".number_format($total,2,".",","));
$pdf->Ln(1);
$pdf->setX(6);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->setX(108);
$pdf->Cell(5,15,'____________________________________________________________');
$pdf->Ln(4);
$pdf->setX(6);
$pdf->Cell(5,15,"SUCURSAL: ".strtoupper($stock->name));
$pdf->setX(108);
$pdf->Cell(5,15,'ATENDIDO POR: '.strtoupper(Core::$user->name." ".Core::$user->lastname));

$pdf->Ln(10);
$pdf->setX(6);
$pdf->Cell(5,15,"VEHICULOS UTILIZADOS:");
$pdf->Ln(-8);
foreach ($sellx as $x){
$cars = CarsData::getById($x->car_id);
$pdf->setX(6);
$pdf->Cell(5,51,strtoupper($cars->getBrand()->name." ".$cars->name." ".$cars->year." (".$cars->plate.")"));
$pdf->Ln(6);
}


$pdf->Ln(30);
$pdf->setX(80);
$pdf->Cell(5,15,'_______________________________');
$pdf->Ln(4);
$pdf->setX(87);
$pdf->Cell(5,15,'FIRMA DEL PROPIETARIO');
$pdf->output();
