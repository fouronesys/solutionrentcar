<?php

declare(strict_types=1);

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
include "../CF-SYSTEMS/fpdf/fpdf.php";

session_start();

if(isset($_SESSION["user_id"])) {
    Core::$user = UserData::getById((int)$_SESSION["user_id"]);
}

// VALIDAR ID
if(!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("ID inválido");
}

$id = (int)$_GET["id"];

// VARIABLES SEGURAS
$subtotal = 0;
$subtotal2 = 0;

// MONEDA
$symbol = StockData::getPrincipal()->currency ?? "RD$";
if($symbol=="€"){ $symbol=chr(128); }
else if($symbol=="₡"){ $symbol='₡'; }

// DATOS
$sell = CotizationData::getById($id);
if(!$sell){
    die("Cotización no encontrada");
}

$stock = StockData::getById($sell->stock_id);
$user  = $sell->getUser();
$person = $sell->getPerson();

// COLOR
$color = explode(",", StockData::getPrincipal()->color ?? "0,0,0");

// PDF
$pdf = new FPDF('P','mm', [203.2,279.4]);
$pdf->AddPage();

include('../core/app/layouts/ticketheader.php');

$pdf->Ln(10);

// FONDO
$pdf->SetDrawColor(235,238,236);
$pdf->SetFillColor(239,239,239);
$pdf->Rect(10, 60, 189, 30, 'DF');

$pdf->SetDrawColor(215,215,215);
$pdf->SetFillColor(215,215,215);
$pdf->Rect(77,60, 0.5, 30, 'DF');

// TITULO
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',12);
$pdf->Ln(12);
$pdf->SetX(10);
$pdf->MultiCell(180,3.5,strtoupper(utf8_decode("DETALLE DE LA COTIZACION")),0,'C');

// CLIENTE
$pdf->SetFont('Arial','',10);
$pdf->Ln(-13);

$pdf->SetX(80);
$pdf->Cell(5,51,"Nombre Completo:");

$pdf->SetX(140);
$pdf->Cell(5,51,"Telefono:");

$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(2,159,205);
$pdf->Ln(4);

$pdf->SetX(80);
$pdf->Cell(5,51,strtoupper($person->name ?? ""));

$pdf->SetX(141);
$pdf->Cell(5,51,strtoupper(utf8_decode($person->phone ?? "")));

$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','',10);
$pdf->Ln(9);

$pdf->SetX(80);
$pdf->Cell(5,51,"Direccion:");

$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(2,159,205);
$pdf->Ln(4);

$pdf->SetX(80);
$pdf->Cell(5,51,strtoupper(utf8_decode($person->address ?? "")));

$pdf->SetTextColor(0,0,0);

// VEHICULO
$pdf->SetFont('Arial','',14);
$pdf->Ln(16);
$pdf->SetX(12);
$pdf->Cell(5,51,strtoupper("DATOS DEL VEHICULO"));

// OPERACIONES
$operations = OperationData::getAllBySQL("WHERE cotization_id=".$id);

foreach($operations as $op){

    $cars = CarsData::getById($op->car_id);
    if(!$cars) continue;

    $pdf->SetFont('Arial','',9);
    $pdf->SetTextColor(0,0,0);

    $pdf->Ln(20);
    $pdf->SetX(12);
    $pdf->Cell(5,51,"DIAS: ".$op->day);

    $pdf->SetX(80);
    $pdf->Cell(5,51,"PRECIO X DIA: ".$symbol." ".$op->price);

    $pdf->SetX(160);
    $pdf->Cell(5,51,"IMPORTE: ".$symbol." ".number_format((float)$sell->total,2,".",","));

    // TIPO
    $pdf->Ln(8);
    $pdf->SetX(12);
    $pdf->Cell(5,51,"TIPO / TYPE: ");

    $pdf->SetX(120);
    $pdf->Cell(5,51,"AÑO / YEAR: ");

    $pdf->SetTextColor(2,159,205);
    $pdf->Ln(4);

    $pdf->SetX(12);
    $pdf->Cell(5,51,strtoupper(utf8_decode($cars->getCategory()->name ?? "")));

    $pdf->SetX(120);
    $pdf->Cell(5,51,strtoupper((string)$cars->year));

    // MARCA
    $pdf->Ln(10);
    $pdf->SetX(12);
    $pdf->SetTextColor(0,0,0);
    $pdf->Cell(5,51,"MARCA / BRAND: ");

    $pdf->SetX(120);
    $pdf->Cell(5,51,"COLOR: ");

    $pdf->SetTextColor(2,159,205);
    $pdf->Ln(4);

    $pdf->SetX(12);
    $pdf->Cell(5,51,strtoupper(utf8_decode($cars->getBrand()->name ?? "")));

    $pdf->SetX(120);
    $pdf->Cell(5,51,strtoupper(utf8_decode($cars->getExColor()->name ?? "")));

    // MODELO
    $pdf->Ln(10);
    $pdf->SetX(12);
    $pdf->SetTextColor(0,0,0);
    $pdf->Cell(5,51,"MODELO / MODEL: ");

    $pdf->SetX(120);
    $pdf->Cell(5,51,"PLACA / PLATE: ");

    $pdf->SetTextColor(2,159,205);
    $pdf->Ln(4);

    $pdf->SetX(12);
    $pdf->Cell(5,51,strtoupper(utf8_decode($cars->name ?? "")));

    $pdf->SetX(120);
    $pdf->Cell(5,51,strtoupper(utf8_decode($cars->plate ?? "")));

    // CALCULOS
    $subtotal += ((float)$op->price * (int)$op->day);
    $subtotal2 += (float)$sell->total;
}

// TOTALES
$pdf->SetFont('Arial','B',9);

$pdf->Ln(20);
$pdf->SetX(12);
$pdf->Cell(5,10,"SUBTOTAL: ".$symbol." ".number_format($subtotal,2,".",","));

$pdf->Ln(8);
$pdf->SetX(12);
$pdf->Cell(5,10,"ITBIS (18%): ".$symbol." ".number_format(($subtotal * (($sell->iva ?? 18)/100)),2,".",","));

$pdf->Ln(8);
$pdf->SetX(12);
$pdf->Cell(5,10,"TOTAL: ".$symbol." ".number_format($subtotal2,2,".",","));

$pdf->Output();