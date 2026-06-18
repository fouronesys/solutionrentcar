<?php
ob_start(); // IMPORTANTE PARA FPDF EN PHP 8.4

include "../core/controller/Core.php";
include "../core/controller/Database.php";
include "../core/controller/Executor.php";
include "../core/controller/Model.php";

include "../core/app/model/CData.php";
include "../core/app/model/UserData.php";
include "../core/app/model/BookingData.php";
include "../core/app/model/BrandData.php";
include "../core/app/model/FData.php";
include "../core/app/model/SureData.php";
include "../core/app/model/CarsData.php";
include "../core/app/model/ColorData.php";
include "../core/app/model/StockData.php";
include "../core/app/model/PersonData.php";
include "../core/app/model/PaymentData.php";
include "../core/app/model/CategoryData.php";
include "../core/app/model/DeliveryData.php";
include "../CF-SYSTEMS/fpdf/fpdf.php";

session_start();

if(isset($_SESSION["user_id"])){
    Core::$user = UserData::getById($_SESSION["user_id"]);
}

// 🔒 VALIDACIÓN PHP 8.4
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$person_id = isset($_GET["person_id"]) ? intval($_GET["person_id"]) : 0;

if($id <= 0){
    die("ID inválido");
}

// ================= DATOS =================
$stock = StockData::getPrincipal();

$symbol = $stock->currency ?? '';
if($symbol=="€"){ $symbol=chr(128); }
else if($symbol=="₡"){ $symbol='₡'; }

$title   = $stock->name ?? '';
$rnc     = $stock->rnc ?? '';
$iva_val = $stock->imp_val ?? 0;

$sell = BookingData::getById($id);
if(!$sell){ die("Factura no encontrada"); }

$cars  = CarsData::getById($sell->car_id);
$cars2 = CarsData::getById($sell->car2_id);

$clients = PersonData::getById($person_id);

// evitar null
$clients = $clients ?? new stdClass();
$clients->name    = $clients->name ?? '';
$clients->address = $clients->address ?? '';
$clients->phone   = $clients->phone ?? '';
$clients->email   = $clients->email ?? '';

$delivery = DeliveryData::getBySell(0,2,$id);

// ================= PAGOS =================
$payments = PaymentData::getByPayment($sell->id);
$totpayments = 0;

if(!empty($payments) && isset($payments[0]->t)){
    $totpayments = $payments[0]->t ?? 0;
}

// ================= PDF =================
$pdf = new FPDF('P','mm', array(210,390));
$pdf->AddPage();

// logo
if(!empty($stock->ticket_image)){
    $src = "../CF-SYSTEMS/storage/configuration/".$stock->ticket_image;
    if(file_exists($src)){
        $pdf->Image($src,160,10,30);
    }
}

$pdf->SetFont('Arial','B',14);
$pdf->Cell(10,10,'Date: '.date("d/m/Y", strtotime($sell->created_at)),0,1);

// ================= ENCABEZADO =================
$pdf->SetFont('Arial','B',12);
$pdf->Cell(100,10,'From',0,0);
$pdf->Cell(90,10,'To',0,1);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(100,5,strtoupper($title),0,0);
$pdf->Cell(90,5,strtoupper($clients->name),0,1);

$pdf->SetFont('Arial','',10);
$pdf->MultiCell(100,4,strtoupper($stock->address ?? ''),0,'L');

$pdf->Ln(-7);
$pdf->SetX(110);

$pdf->MultiCell(100,4,
    !empty($clients->address) ? strtoupper($clients->address) : "ADDRESS:",
0,'L');

$pdf->Ln(5);

$pdf->Cell(100,5,"PHONE: ".($stock->phone ?? ''),0,0);
$pdf->Cell(90,5,"PHONE: ".$clients->phone,0,1);

$pdf->Cell(100,5,"EMAIL: ".($stock->email ?? ''),0,0);
$pdf->Cell(90,5,"EMAIL: ".$clients->email,0,1);

// ================= FACTURA =================
function generateCode($id){
    return strtoupper(base_convert($id, 10, 36));
}

$invoice_code = str_pad(generateCode($sell->id),6,'X',STR_PAD_RIGHT);

$pdf->Ln(5);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,10,'Invoice #'.str_pad($sell->id,5,'0',STR_PAD_LEFT),0,1);

$pdf->SetFont('Arial','',10);
$pdf->Cell(190,5,'ORDER ID: '.$invoice_code,0,1);

// ================= TABLA =================
$pdf->Ln(10);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(15,7,'Day',1);
$pdf->Cell(50,7,'Vehicle',1);
$pdf->Cell(40,7,'Chassis',1);
$pdf->Cell(55,7,'Plate',1);
$pdf->Cell(30,7,'Color',1);
$pdf->Ln();

$pdf->SetFont('Arial','',9);

$pdf->Cell(15,7,$sell->day ?? '',1);
$pdf->Cell(50,7,strtoupper(($cars->name ?? '')),1);
$pdf->Cell(40,7,strtoupper(($cars2->chassis ?? '')),1);
$pdf->Cell(55,7,strtoupper(($cars->plate ?? '')),1);
$pdf->Cell(30,7,strtoupper(($cars->getExColor()->name ?? '')),1);
$pdf->Ln();

// ================= TOTALES =================
$pdf->Ln(10);

$pdf->SetX(110);
$pdf->Cell(50,7,'SUBTOTAL:',1);
$pdf->Cell(30,7,number_format($sell->total ?? 0,2),1,1,'R');

$pdf->SetX(110);
$pdf->Cell(50,7,'TAXES:',1);
$pdf->Cell(30,7,number_format($sell->value_iva ?? 0,2),1,1,'R');

$pdf->SetX(110);
$pdf->Cell(50,7,'OTHER:',1);
$pdf->Cell(30,7,number_format($sell->plane ?? 0,2),1,1,'R');

$pdf->SetX(110);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(50,7,'TOTAL:',1);
$pdf->Cell(30,7,number_format(
    (($sell->total ?? 0)+($sell->plane ?? 0)+($sell->value_iva ?? 0)) - $totpayments
,2),1,1,'R');

// ================= SALIDA =================
ob_end_clean(); // 🔥 CLAVE en PHP 8.4
$pdf->Output();