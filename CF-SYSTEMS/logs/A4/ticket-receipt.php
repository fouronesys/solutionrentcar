<?php

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
include "../../CF-SYSTEMS/fpdf/fpdf.php";

session_start();
if(isset($_SESSION["user_id"])){ Core::$user = UserData::getById($_SESSION["user_id"]); }
$symbol =  StockData::getPrincipal()->currency;
if($symbol=="€"){ $symbol=chr(128); }
else if($symbol=="₡"){ 
//echo intval("€");
	$symbol=    '₡';}


$rnc =  StockData::getPrincipal()->rnc;
$title =  StockData::getPrincipal()->name;
$iva_val =  StockData::getPrincipal()->imp-val;
$divisa =  StockData::getPrincipal()->divisa;
$stock = StockData::getPrincipal();
$sell = BookingData::getById($_GET["id"]);
$clients = PersonData::getById($sell->person_id);
$cars = CarsData::getById($sell->car_id);
$delivery = DeliveryData::getBySell(0,2,$_GET["id"]);
$delivery2 = DeliveryData::getBySell(1,2,$_GET["id"]);
$color =  $receiptIdAndName = explode(",", StockData::getPrincipal()->color);

$receiver = DeliveryData::getBySell(0,1,$_GET["id"]);
$receiver2 = DeliveryData::getBySell(1,1,$_GET["id"]);
$clients2 = PersonData::getById($sell->person2_id);

$cars = CarsData::getById($sell->car_id);
$cars2 = CarsData::getById($sell->car2_id);

$totpayments = 0;
$payments = PaymentData::getByPayment($sell->id);
$totpayments = $payments[0]->t!=null?$payments[0]->t:0;

$ticket_image = StockData::getPrincipal()->ticket_image;
$user = $sell->getUser();

$pdf = new FPDF($orientation='P',$unit='mm', array(210,390));

$pdf->AddPage();

if($ticket_image<>""){
   $src = "../CF-SYSTEMS/storage/configuration/".$ticket_image;
    if(file_exists($src)){
        $pdf->Image($src,160,10,30);   
    }
}


$pdf->SetFont('Arial','B',14);

// Encabezado
$pdf->Cell(10,10,'Date: '.date("d/m/Y", strtotime($sell->created_at."- 4 hours")),0,1);

// Info fila
$pdf->SetFont('Arial','B',12);
$pdf->Cell(100,10,'From',0,0);
$pdf->Cell(90,10,'To',0,1);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(100,5,strtoupper($title),0,0);
$pdf->Cell(90,5,strtoupper($clients->name),0,1);


$pdf->SetFont('Arial','',10);
$pdf->MultiCell(100,3.5,mb_strtoupper(utf8_decode($stock->address), 'ISO-8859-1'),0,'L');

$pdf->Ln(-7);
$pdf->setX(110);
if($clients->address>0):
$pdf->MultiCell(100,3.5,mb_strtoupper(utf8_decode($clients->address), 'ISO-8859-1'),0,'L');
else:
$pdf->MultiCell(100,3.5,mb_strtoupper(utf8_decode("ADDRESS:"), 'ISO-8859-1'),0,'L');   
endif;

$pdf->Ln(5);
$pdf->Cell(100,5,"PHONE: ".strtoupper($stock->phone."; ".$stock->phone2),0,0);
$pdf->Cell(90,5,"PHONE: ".$clients->phone,0,1);

$pdf->Cell(100,5,"EMAIL: ".strtoupper($stock->email),0,0);
$pdf->Cell(90,5,"EMAIL: ".strtoupper($clients->email),0,1);

// Función para convertir ID numérico a código alfanumérico
function generateCode($id) {
    // Base 36 (0-9 + A-Z)
    return strtoupper(base_convert($id, 10, 36));
}

// Ejemplo de ID de factura
$invoice_id = $sell->id; // este es tu "00014"
$invoice_code = generateCode($invoice_id); // → "E"

// Si quieres siempre 6 caracteres, rellenamos con letras/números
$invoice_code = str_pad($invoice_code, 6, 'X', STR_PAD_RIGHT); // → "EXXXXX"

// Factura info
$pdf->Ln(5);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,10,'Invoice #'.substr(str_repeat(0, 5).$sell->id, - 5),0,1);

$pdf->SetFont('Arial','',10);
$pdf->Cell(190,5,'ORDER ID: '.$invoice_code,0,1);
$pdf->Cell(190,5,'PAYMENT DUE: '.date("d/m/Y", strtotime($sell->created_at."- 4 hours")),0,1);

// 👉 Función para dar formato estilo ACCOUNT
function formatAccountFromPhone($phone) {
    // quitar todo lo que no sea número
    $phone = preg_replace('/\D/', '', $phone);

    // si tiene al menos 8 dígitos, tomar primeros 3 y últimos 5
    if(strlen($phone) >= 8){
        return substr($phone,0,3) . '-' . substr($phone,-5);
    }

    return $phone;
}

// ejemplo: viene de la base de datos
$invoice_phone = $clients->phone; // aquí ya tienes 1809999636

$pdf->Cell(190,5,'ACCOUNT: '.formatAccountFromPhone($invoice_phone),0,1);

// Espacio
$pdf->Ln(10);

// Tabla
$pdf->SetFont('Arial','B',10);
$pdf->Cell(15,7,'Day',1);
$pdf->Cell(50,7,'Vehicle',1);
$pdf->Cell(65,7,'Chassis #',1);
$pdf->Cell(30,7,'Plate',1);
$pdf->Cell(30,7,'Color',1);
$pdf->Ln();

$pdf->SetFont('Arial','',9);

$items = [
    
    [$sell->day,strtoupper($cars->getBrand()->name)." ".strtoupper($cars->name)." ".strtoupper($cars->year),strtoupper($cars2->chassis),strtoupper($cars->plate),strtoupper($cars->getExColor()->name)],
];

foreach($items as $row){
    $pdf->Cell(15,7,$row[0],1);
    $pdf->Cell(50,7,$row[1],1);
    $pdf->Cell(65,7,$row[2],1);
    $pdf->Cell(30,7,$row[3],1);
    $pdf->Cell(30,7,$row[4],1);
    $pdf->Ln();
}

// Espacio
$pdf->Ln(10);

// Métodos de pago
$pdf->SetFont('Arial','B',12);
$pdf->Cell(95,10,'Payment Methods:',0,0);
$pdf->Cell(95,10,'Amount Due '.date("d/m/Y", strtotime($sell->created_at."- 4 hours")),0,1);

$pdf->SetFont('Arial','',8);
$pdf->MultiCell(95,5,mb_strtoupper(utf8_decode("Visa, Mastercard, American Express, Paypal\n\nPor favor realice el pago del alquiler antes de la fecha de vencimiento indicada. En caso de retraso se aplicarán cargos adicionales."), 'ISO-8859-1'),0);

// Totales
$pdf->SetXY(110,128);
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,7,'SUBTOTAL:',1);
$pdf->Cell(30,7, number_format($sell->total,2,".",",")." ".StockData::getPrincipal()->currency,1,1,'R');

$pdf->SetX(110);
$pdf->Cell(50,7,'TAXES ('.$iva_val.'%):',1);
$pdf->Cell(30,7,number_format($sell->value_iva,2,".",",")." ".StockData::getPrincipal()->currency,1,1,'R');

$pdf->SetX(110);
$pdf->Cell(50,7,'OTHER CHARGES:',1);
$pdf->Cell(30,7,number_format($sell->plane,2,".",",")." ".StockData::getPrincipal()->currency,1,1,'R');

$pdf->SetX(110);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(50,7,'PENDING TOTAL:',1);
$pdf->Cell(30,7,number_format(($sell->total+$sell->plane+$sell->value_iva)-$totpayments,2,".",",")." ".StockData::getPrincipal()->currency,1,1,'R');



$pdf->Ln(18);
$pdf->setX(20);
$pdf->Cell(5,20,'__________________________________                 __________________________________');


$posY = $pdf->GetY() + 0;

// Firma del arrendador
if (isset($user->firma)) {
    $srcx = '../' . $user->firma;
    if (file_exists($srcx)) {
        $x = ($clients->language == 'ES') ? 40 : 50;
        $pdf->Image($srcx, $x, $posY, 30);
    }
}


if (isset($delivery->firma)) {
    $src = '../' . $delivery->firma;
    if (file_exists($src)) {
        $x2 = ($clients->language == 'ES') ? 130 : 40;
        $pdf->Image($src, $x2, $posY, 30);
    }
}

$pdf->ln(8);
$pdf->setX(20);
$pdf->Cell(5,15,'ARRENDADOR/A (LESSOR):                                     ARRENDATARIO/A (LESSEE):');
$pdf->output();
