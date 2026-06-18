<?php

include "../core/controller/Core.php";
include "../core/controller/Database.php";
include "../core/controller/Executor.php";
include "../core/controller/Model.php";

include "../core/app/model/UserData.php";
include "../core/app/model/BrandData.php";
include "../core/app/model/CarsData.php";
include "../core/app/model/ColorData.php";
include "../core/app/model/StockData.php";
include "../core/app/model/PersonData.php";
include "../core/app/model/CategoryData.php";
include "../core/app/model/CotizationData.php";
include "../core/app/model/OperationData.php";
include "../../CF-SYSTEMS/fpdf/fpdf.php";

session_start();
if(isset($_SESSION["user_id"])){ Core::$user = UserData::getById($_SESSION["user_id"]); }
$symbol =  StockData::getPrincipal()->currency;
if($symbol=="€"){ $symbol=chr(128); }
else if($symbol=="₡"){ 
//echo intval("€");
    $symbol=    '₡';}


$sell = CotizationData::getById($_GET["id"]);
$stock = StockData::getById($sell->stock_id);
$user = $sell->getUser();

$color =  $receiptIdAndName = explode(",", StockData::getPrincipal()->color);

$symbol =  StockData::getPrincipal()->currency;

$pdf = new FPDF($orientation='P',$unit='mm', array(203.2,279.4));

$pdf->AddPage();

include('../core/app/layouts/ticketheader.php');



$pdf->Ln(10);

$pdf->SetDrawColor(235,238,236);
$pdf->SetFillColor(239, 239, 239 );
/// derecha altura tamano anchura
$pdf->Rect(10, 60, 189, 30, 'DF');


$pdf->SetDrawColor(215, 215, 215);
$pdf->SetFillColor(215, 215, 215);
/// derecha altura tamano anchura
$pdf->Rect(77,60, 0.5, 30, 'DF');


$pdf->SetTextColor (0,0,0);

$pdf->SetFont('Arial','',12);    //Letra Arial, negrita (Bold)
$pdf->Ln(12);
$pdf->setX(10);
$pdf->MultiCell(180,3.5,strtoupper(utf8_decode("DETALLE DE LA COTIZACION")),0,'C');

$pdf->SetFont('Arial','',10);    //Letra Arial, negrita (Bold)
$pdf->Ln(-13);

$pdf->setX(12);

$pdf->setX(80);
$pdf->Cell(5,51,"Nombre Completo:");

$pdf->setX(140);
$pdf->Cell(5,51,"Telefono:");

$pdf->SetFont('Arial','',8);    //Letra Arial, negrita (Bold)
$pdf->SetTextColor (2, 159, 205);
$pdf->Ln(4);

$pdf->setX(12);


$pdf->setX(80);
$pdf->Cell(5,51,strtoupper($sell->getPerson()->name));


$pdf->setX(141);
$pdf->Cell(5,51,strtoupper(utf8_decode($sell->getPerson()->phone)));

$pdf->SetTextColor (0,0,0);

$pdf->SetFont('Arial','',10);    //Letra Arial, negrita (Bold)
$pdf->Ln(9);

$pdf->setX(12);

$pdf->setX(80);
$pdf->Cell(5,51,"Direccion:");


$pdf->SetFont('Arial','',8);    //Letra Arial, negrita (Bold)
$pdf->SetTextColor (2, 159, 205);
$pdf->Ln(4);

$pdf->setX(12);
$pdf->Ln(0);
$pdf->setX(80);
$pdf->Cell(5,51,strtoupper(utf8_decode($sell->getPerson()->address)));


$pdf->SetTextColor (2,2,2);

$pdf->SetFont('Arial','',14);    //Letra Arial, negrita (Bold)

$pdf->Ln(16);
$pdf->setX(12);
$pdf->Cell(5,51,strtoupper("DATOS DEL VEHICULO"));

foreach(OperationData::getAllBySQL("where cotization_id=".$_GET['id']) as $op):

$pdf->setX(10);
$pdf->SetDrawColor($color[0],$color[1],$color[2]);
$pdf->SetFillColor($color[0],$color[1],$color[2]);
/// derecha altura tamano anchura
$pdf->Rect(10, 43, 189, 0.3, 'DF');

$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(245, 245, 245);
/// derecha altura tamano anchura
$pdf->Rect(10, $pdf->GetY()+45, 67.5, 50, 'DF');
///////////////////////////////////////////////////

$pdf->SetDrawColor(215, 215, 215);
$pdf->SetFillColor(215, 215, 215);
/// derecha altura tamano anchura
$pdf->Rect(10, $pdf->GetY()+40, 189.3, 10, 'DF');
///////////////////////////////////////////////////

$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(245, 245, 245);
/// derecha altura tamano anchura
$pdf->Rect(10, $pdf->GetY()+65, 189, 0.3, 'DF');


$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(245, 245, 245);
/// derecha altura tamano anchura
$pdf->Rect(10, $pdf->GetY()+78, 189, 0.3, 'DF');



$pdf->setX(10);
$pdf->SetDrawColor(215, 215, 215);
$pdf->SetFillColor(255, 195, 0);
/// derecha altura tamano anchura
$pdf->Rect(10, $pdf->GetY()+95.5, 189, 0.3, 'DF');


$pdf->SetDrawColor(215, 215, 215);
$pdf->SetFillColor(215, 215, 215);
/// derecha altura tamano anchura
$pdf->Rect(77, $pdf->GetY()+40.5, 0.5, 55, 'DF');



$cars = CarsData::getById($op->car_id);
$pdf->SetFont('Arial','',9); 

$pdf->SetTextColor (0, 0, 0);

$pdf->Ln(20);
$pdf->setX(12);
$pdf->Cell(5,51,"DIAS: ".$op->day);

$pdf->setX(80);
$pdf->Cell(5,51,utf8_decode("PRECIO X DIA: ".$symbol." ".$op->price));


$pdf->setX(160);
$pdf->Cell(5,51,utf8_decode("IMPORTE: ".$symbol." ".number_format($sell->total,2,".",",")));


$pdf->SetTextColor (0, 0, 0);

$pdf->Ln(8);
$pdf->setX(12);
$pdf->Cell(5,51,"TIPO / TYPE: ");

$pdf->setX(120);
$pdf->Cell(5,51,utf8_decode("AÑO / YEAR: "));

$pdf->SetTextColor (2, 159, 205);
$pdf->Ln(4);

$pdf->setX(12);
$pdf->Cell(5,51,strtoupper(utf8_decode($cars->getCategory()->name)));

$pdf->setX(120);
$pdf->Cell(5,51,strtoupper($cars->year));

$pdf->Ln(5);
$pdf->setX(12);

$pdf->SetTextColor (0, 0, 0);
$pdf->Ln(6);
$pdf->setX(12);
$pdf->Cell(5,51,"MARCA / BRAND: ");

$pdf->setX(120);
$pdf->Cell(5,51,"COLOR: ");

$pdf->SetTextColor (2, 159, 205);
$pdf->Ln(4);

$pdf->setX(12);
$pdf->Cell(5,51,strtoupper(utf8_decode($cars->getBrand()->name)));

$pdf->setX(120);
$pdf->Cell(5,51,strtoupper(utf8_decode($cars->getExColor()->name)));

$pdf->Ln(5);
$pdf->setX(12);

$pdf->SetTextColor (0, 0, 0);
$pdf->Ln(8);
$pdf->setX(12);
$pdf->Cell(5,51,"MODELO / MODEL: ");

$pdf->setX(120);
$pdf->Cell(5,51,"PLACA / PLATE: ");


$pdf->SetTextColor (2, 159, 205);
$pdf->Ln(4);

$pdf->setX(12);
$pdf->Cell(5,51,strtoupper(utf8_decode($cars->name)));


$pdf->setX(120);
$pdf->Cell(5,51,strtoupper(utf8_decode($cars->plate)));

$subtotal2 += ($sell->total);
$subtotal += ($op->price*$op->day);
endforeach;

$pdf->SetFont('Arial','B',9); 

$pdf->setX(10);
$pdf->SetDrawColor(215, 215, 215);
$pdf->SetFillColor(255, 195, 0);
/// derecha altura tamano anchura
$pdf->Rect(10, 107, 189, 0.3, 'DF');

$pdf->SetTextColor (0,0,0);
$pdf->Ln(-100);
$pdf->setX(12);
$pdf->Cell(5,51,"SUBTOTAL: ".$symbol." ".number_format($subtotal,2,".",","));
 
$pdf->Ln(8);
$pdf->setX(12);
$pdf->Cell(5,51,"ITBIS (18%): ".$symbol." ".number_format(($subtotal*($sell->iva/100)),2,".",","));

$pdf->Ln(8);
$pdf->setX(12);
$pdf->Cell(5,51,"TOTAL: ".$symbol." ".number_format($subtotal2,2,".",","));


$pdf->output();
