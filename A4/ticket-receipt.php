<?php
ob_start(); // 🔥 importante en PHP 8.4

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

// 🔒 evitar errores si no existe sesión
Core::$user = isset($_SESSION["user_id"]) ? UserData::getById($_SESSION["user_id"]) : null;

$stock = StockData::getPrincipal();

$symbol = $stock->currency ?? '';
if($symbol=="€"){ $symbol=chr(128); }
elseif($symbol=="₡"){ $symbol='₡'; }

$rnc     = $stock->rnc ?? '';
$title   = $stock->name ?? '';
$iva_val = $stock->{"imp-val"} ?? 0;
$divisa  = $stock->divisa ?? '';

$sell = BookingData::getById($_GET["id"] ?? 0);
if(!$sell){ die("Venta no encontrada"); }

$clients = PersonData::getById($sell->person_id);
$cars    = CarsData::getById($sell->car_id);
$cars2   = CarsData::getById($sell->car2_id);

$delivery  = DeliveryData::getBySell(0,2,$sell->id);
$delivery2 = DeliveryData::getBySell(1,2,$sell->id);

$totpayments = 0;
$payments = PaymentData::getByPayment($sell->id);
$totpayments = ($payments && isset($payments[0]->t)) ? $payments[0]->t : 0;

$ticket_image = $stock->ticket_image ?? '';
$user = $sell->getUser();

$pdf = new FPDF('P','mm', [210,390]);
$pdf->AddPage();

// 🔥 LOGO
if(!empty($ticket_image)){
    $src = "../CF-SYSTEMS/storage/configuration/".$ticket_image;
    if(file_exists($src)){
        $pdf->Image($src,160,10,30);
    }
}

// 🔥 FUNCIÓN SEGURA UTF (PHP 8.4)
function txt($text){
    return mb_strtoupper(iconv('UTF-8','ISO-8859-1//TRANSLIT', (string)$text));
}

// FECHA
$pdf->SetFont('Arial','B',14);
$pdf->Cell(10,10,'Date: '.date("d/m/Y", strtotime($sell->created_at."-4 hours")),0,1);

// FROM / TO
$pdf->SetFont('Arial','B',12);
$pdf->Cell(100,10,'From',0,0);
$pdf->Cell(90,10,'To',0,1);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(100,5,strtoupper($title),0,0);
$pdf->Cell(90,5,strtoupper($clients->name ?? ''),0,1);

// DIRECCIONES
$pdf->SetFont('Arial','',10);
$pdf->MultiCell(100,3.5, txt($stock->address ?? ''),0,'L');

$pdf->Ln(-7);
$pdf->SetX(110);

$pdf->MultiCell(100,3.5, txt($clients->address ?? 'ADDRESS:'),0,'L');

// CONTACTO
$pdf->Ln(5);
$pdf->Cell(100,5,"PHONE: ".strtoupper(($stock->phone ?? '')."; ".($stock->phone2 ?? '')),0,0);
$pdf->Cell(90,5,"PHONE: ".($clients->phone ?? ''),0,1);

$pdf->Cell(100,5,"EMAIL: ".strtoupper($stock->email ?? ''),0,0);
$pdf->Cell(90,5,"EMAIL: ".strtoupper($clients->email ?? ''),0,1);

// 🔥 CODIGO
function generateCode($id){
    return strtoupper(base_convert((int)$id,10,36));
}

$invoice_code = str_pad(generateCode($sell->id),6,'X');

// FACTURA
$pdf->Ln(5);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,10,'Invoice #'.str_pad($sell->id,5,'0',STR_PAD_LEFT),0,1);

$pdf->SetFont('Arial','',10);
$pdf->Cell(190,5,'ORDER ID: '.$invoice_code,0,1);
$pdf->Cell(190,5,'PAYMENT DUE: '.date("d/m/Y", strtotime($sell->created_at."-4 hours")),0,1);

// ACCOUNT
function formatAccountFromPhone($phone){
    $phone = preg_replace('/\D/','',$phone);
    return strlen($phone)>=8 ? substr($phone,0,3).'-'.substr($phone,-5) : $phone;
}

$pdf->Cell(190,5,'ACCOUNT: '.formatAccountFromPhone($clients->phone ?? ''),0,1);

// TABLA
$pdf->Ln(10);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(15,7,'Day',1);
$pdf->Cell(50,7,'Vehicle',1);
$pdf->Cell(65,7,'Chassis #',1);
$pdf->Cell(30,7,'Plate',1);
$pdf->Cell(30,7,'Color',1);
$pdf->Ln();

$pdf->SetFont('Arial','',9);

$pdf->Cell(15,7,$sell->day ?? '',1);
$pdf->Cell(50,7,strtoupper(($cars->getBrand()->name ?? '')." ".$cars->name." ".$cars->year),1);
$pdf->Cell(65,7,strtoupper($cars2->chassis ?? ''),1);
$pdf->Cell(30,7,strtoupper($cars->plate ?? ''),1);
$pdf->Cell(30,7,strtoupper($cars->getExColor()->name ?? ''),1);
$pdf->Ln();

// PAGOS
$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(95,10,'Payment Methods:',0,0);
$pdf->Cell(95,10,'Amount Due',0,1);

$pdf->SetFont('Arial','',8);
$pdf->MultiCell(95,5, txt("Visa, Mastercard, American Express, Paypal"),0);

// TOTALES
$pdf->SetXY(110,128);
$pdf->SetFont('Arial','',10);

$pdf->Cell(50,7,'SUBTOTAL:',1);
$pdf->Cell(30,7,number_format($sell->total ?? 0,2),1,1,'R');

$pdf->SetX(110);
$pdf->Cell(50,7,'TAXES:',1);
$pdf->Cell(30,7,number_format($sell->value_iva ?? 0,2),1,1,'R');

$pdf->SetX(110);
$pdf->Cell(50,7,'OTHER CHARGES:',1);
$pdf->Cell(30,7,number_format($sell->plane ?? 0,2),1,1,'R');

$pdf->SetX(110);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(50,7,'PENDING TOTAL:',1);
$pdf->Cell(30,7,number_format((($sell->total ?? 0)+($sell->plane ?? 0)+($sell->value_iva ?? 0))-$totpayments,2),1,1,'R');

$pdf->Output();
ob_end_flush();