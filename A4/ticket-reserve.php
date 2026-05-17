<?php

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);
ob_start();

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
include "../core/app/model/CategoryData.php";
include "../core/app/model/DeliveryData.php";
include "../core/app/model/PaymentData.php";
include "../CF-SYSTEMS/fpdf/fpdf.php";

if (!function_exists('ponerImagenSegura')) {
    function ponerImagenSegura($pdf, $src, $x, $y, $w = 0, $h = 0){
        if (!is_string($src) || trim($src) === '') {
            return false;
        }

        $src = trim($src);

        if (!file_exists($src) || !is_file($src)) {
            return false;
        }

        $bin = @file_get_contents($src);
        if ($bin === false || $bin === '') {
            return false;
        }

        $im = @imagecreatefromstring($bin);
        if (!$im) {
            return false;
        }

        $width  = imagesx($im);
        $height = imagesy($im);

        if ($width <= 0 || $height <= 0) {
            imagedestroy($im);
            return false;
        }

        $bg = imagecreatetruecolor($width, $height);
        if (!$bg) {
            imagedestroy($im);
            return false;
        }

        $white = imagecolorallocate($bg, 255, 255, 255);
        imagefill($bg, 0, 0, $white);
        imagecopy($bg, $im, 0, 0, 0, 0, $width, $height);

        $tmpBase = tempnam(sys_get_temp_dir(), 'fpdf_img_');
        if ($tmpBase === false) {
            imagedestroy($im);
            imagedestroy($bg);
            return false;
        }

        $tmpJpg = $tmpBase . '.jpg';
        @unlink($tmpBase);

        $ok = @imagejpeg($bg, $tmpJpg, 90);

        imagedestroy($im);
        imagedestroy($bg);

        if (!$ok || !file_exists($tmpJpg) || filesize($tmpJpg) <= 0) {
            @unlink($tmpJpg);
            return false;
        }

        try {
            $pdf->Image($tmpJpg, $x, $y, $w, $h, 'JPG');
        } catch (Throwable $e) {
            @unlink($tmpJpg);
            return false;
        }

        @unlink($tmpJpg);
        return true;
    }
}

session_start();

if (isset($_SESSION["user_id"])) {
    Core::$user = UserData::getById($_SESSION["user_id"]);
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("ID inválido");
}

$principal = StockData::getPrincipal();
if (!$principal) {
    die("No se encontró la configuración principal");
}

$sell = BookingData::getById((int)$_GET["id"]);
if (!$sell) {
    die("Reservación no encontrada");
}

$cars = CarsData::getById($sell->car_id);
$clients = PersonData::getById($sell->person_id);
$clients2 = !empty($sell->person2_id) ? PersonData::getById($sell->person2_id) : null;
$cars2 = !empty($sell->car2_id) ? CarsData::getById($sell->car2_id) : null;

if (!$cars || !$clients) {
    die("Datos incompletos de la reservación");
}

$symbol = $principal->currency ?? "";
if ($symbol == "€") {
    $symbol = chr(128);
} elseif ($symbol == "₡") {
    $symbol = "₡";
}

$rnc = $principal->rnc ?? "";
$title = $principal->name ?? "";
$iva_val = $principal->imp_val ?? 0;
$divisa = $principal->divisa ?? "";
$stock = $principal;

$color = explode(",", $principal->color ?? "0,0,0");
$r = isset($color[0]) ? intval($color[0]) : 0;
$g = isset($color[1]) ? intval($color[1]) : 0;
$b = isset($color[2]) ? intval($color[2]) : 0;

$totpayments = 0;
$payments = PaymentData::getByPayment($sell->id);
if (is_array($payments) && isset($payments[0]) && isset($payments[0]->t)) {
    $totpayments = $payments[0]->t ?? 0;
}

$ticket_image = $principal->ticket_image ?? "";
$ticket_image2 = $principal->ticket_image2 ?? "";
$user = $sell->getUser();

$pdf = new FPDF('P', 'mm', array(210,390));
$pdf->AddPage();

if (($sell->fuel ?? "") == "R") {
    ponerImagenSegura($pdf, "../CF-SYSTEMS/storage/configuration/FR.png", 75, 259, 36);
}
if (($sell->fuel ?? "") == "1/4") {
    ponerImagenSegura($pdf, "../CF-SYSTEMS/storage/configuration/F14.png", 75, 259, 36);
}
if (($sell->fuel ?? "") == "3/4") {
    ponerImagenSegura($pdf, "../CF-SYSTEMS/storage/configuration/F34.png", 75, 259, 36);
}
if (($sell->fuel ?? "") == "1/2") {
    ponerImagenSegura($pdf, "../CF-SYSTEMS/storage/configuration/FM.png", 75, 259, 36);
}
if (($sell->fuel ?? "") == "F") {
    ponerImagenSegura($pdf, "../CF-SYSTEMS/storage/configuration/FF.png", 75, 259, 36);
}

if (!empty($ticket_image)) {
    $src = "../CF-SYSTEMS/storage/configuration/" . trim($ticket_image);
    ponerImagenSegura($pdf, $src, 70,10, 70);
}

$pdf->Ln(10);

$pdf->SetFont('Arial','B',25);
$pdf->setX(10);

$pdf->SetDrawColor($r,$g,$b);
$pdf->SetFillColor($r,$g,$b);
$pdf->SetTextColor(255,255,255);
$pdf->Rect(10, 76, 189, 15, 'DF');

$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(167,167,167);
$pdf->SetTextColor(255,255,255);
$pdf->Rect(10, 117, 189, 11, 'DF');

$pdf->SetDrawColor(235,238,236);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(10, 144.4, 67.5, 19.5, 'DF');

$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(235,238,236);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(78.5, 144, 121, 20.5, 'DF');

$pdf->SetDrawColor(235,238,236);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(10, 179.4, 67.5, 13.5, 'DF');

$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(235,238,236);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(78.5, 179, 121, 14.5, 'DF');

$pdf->SetDrawColor(235,238,236);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(10, 209.4, 67.5, 13.5, 'DF');

$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(235,238,236);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(78.5, 209, 121, 14.5, 'DF');

$pdf->SetDrawColor(235,238,236);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(10, 238.4, 67.5, 16, 'DF');

$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(235,238,236);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(78.5, 238, 121, 17, 'DF');

$pdf->SetDrawColor(235,238,236);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(10, 300.4, 67.5, 16, 'DF');

$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(235,238,236);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(78.5, 300, 121, 17, 'DF');

$pdf->SetDrawColor(235,238,236);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(10, 334.4, 67.5, 16, 'DF');

$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(235,238,236);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(78.5, 334, 121, 17, 'DF');


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(235,238,236);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(10, 94, 38, 7, 'DF');

$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(235,238,236);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(80, 101, 22, 7, 'DF');

$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(235,238,236);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(10, 101, 23, 7, 'DF');

$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(235,238,236);
$pdf->SetTextColor(0,0,0);
$pdf->Rect(10, 108, 20, 7, 'DF');
$pdf->SetTextColor(0,0,0);

$pdf->Ln(40);
$pdf->SetFont('Arial','B',9);
$pdf->setX(10);
switch ($clients->language){
    case 'ES': $pdf->MultiCell(180,3.5,"DIRECCION: ".mb_strtoupper(utf8_decode($stock->address), 'ISO-8859-1'),0,'C'); break;
    case 'EN': $pdf->MultiCell(180,3.5,"ADDRESS: ".mb_strtoupper(utf8_decode($stock->address), 'ISO-8859-1'),0,'C'); break;
    default:   $pdf->MultiCell(180,3.5,"DIRECCION: ".mb_strtoupper(utf8_decode($stock->address), 'ISO-8859-1'),0,'C'); break;
}

$pdf->Ln(-4);

ponerImagenSegura($pdf, '../CF-SYSTEMS/storage/redes-sociales/telefono.png', 20, $pdf->GetY()+6, 3);
ponerImagenSegura($pdf, '../CF-SYSTEMS/storage/redes-sociales/whatsapp.png', 24, $pdf->GetY()+6, 3);

$pdf->setX(27);
$pdf->Cell(5,15,": ".strtoupper($stock->phone."; ".$stock->phone2));

ponerImagenSegura($pdf, '../CF-SYSTEMS/storage/redes-sociales/instagram.png', 82, $pdf->GetY()+6, 3);

$pdf->setX(85);
$pdf->Cell(5,15,": ".strtoupper($stock->field2));

$pdf->setX(125);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,15,"CORREO: ".strtoupper($stock->email)); break;
    case 'EN': $pdf->Cell(5,15,"EMAIL: ".strtoupper($stock->email)); break;
    default:   $pdf->Cell(5,15,"CORREO: ".strtoupper($stock->email)); break;
}

$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',18);
$pdf->Ln(23);
$pdf->setX(10);
switch ($clients->language){
    case 'ES': $pdf->MultiCell(180,3.5,strtoupper(utf8_decode("RESERVACIÓN DE VEHÍCULO")),0,'C'); break;
    case 'EN': $pdf->MultiCell(180,3.5,strtoupper(utf8_decode("VEHICLE RESERVATION")),0,'C'); break;
    default:   $pdf->MultiCell(180,3.5,strtoupper(utf8_decode("RESERVACIÓN DE VEHÍCULO")),0,'C'); break;
}
$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','B',9);
$pdf->Ln(-14);
$pdf->setX(10);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"NOMBRE DEL CLIENTE: ".strtoupper($clients->name)); break;
    case 'EN': $pdf->Cell(5,51,"CUSTOMER NAME: ".strtoupper($clients->name)); break;
    default:   $pdf->Cell(5,51,"NOMBRE DEL CLIENTE: ".strtoupper($clients->name)); break;
}

$pdf->setX(158);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"CEDULA: ".strtoupper($clients->no)); break;
    case 'EN': $pdf->Cell(5,51,"ID: ".strtoupper($clients->no)); break;
    default:   $pdf->Cell(5,51,"CEDULA: ".strtoupper($clients->no)); break;
}

$pdf->Ln(7);
$pdf->setX(10);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"REFERENCIA: ".strtoupper($clients->reference)); break;
    case 'EN': $pdf->Cell(5,51,"REFERENCE: ".strtoupper($clients->reference)); break;
    default:   $pdf->Cell(5,51,"REFERENCIA: ".strtoupper($clients->reference)); break;
}

$pdf->setX(80);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"PASAPORTE: ".strtoupper($clients->passport)); break;
    case 'EN': $pdf->Cell(5,51,"PASSPORT: ".strtoupper($clients->passport)); break;
    default:   $pdf->Cell(5,51,"PASAPORTE: ".strtoupper($clients->passport)); break;
}

$pdf->setX(158);
$pdf->Cell(5,51,"TEL.: ".strtoupper($clients->phone));

$pdf->Ln(7);
$pdf->setX(10);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"DIRECCION: ".strtoupper($clients->address)); break;
    case 'EN': $pdf->Cell(5,51,"ADDRESS: ".strtoupper($clients->address)); break;
    default:   $pdf->Cell(5,51,"DIRECCION: ".strtoupper($clients->address)); break;
}

$pdf->setX(158);
$pdf->Cell(5,51,"TEL.: ".strtoupper($clients->phone2));

$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',15);
$pdf->Ln(34.5);
$pdf->setX(10);
switch ($clients->language){
    case 'ES': $pdf->MultiCell(180,3.5,strtoupper(utf8_decode("DATOS DE LA RESERVACIÓN")),0,'C'); break;
    case 'EN': $pdf->MultiCell(180,3.5,strtoupper(utf8_decode("RESERVATION DATA")),0,'C'); break;
    default:   $pdf->MultiCell(180,3.5,strtoupper(utf8_decode("DATOS DE LA RESERVACIÓN")),0,'C'); break;
}
$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','B',10);
$pdf->Ln(-10.5);

$pdf->setX(12);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"CATEGORIA DEL VEHICULO: "); break;
    case 'EN': $pdf->Cell(5,51,"VEHICLE CATEGORY: "); break;
    default:   $pdf->Cell(5,51,"CATEGORIA DEL VEHICULO: "); break;
}

$categoryObj = $cars->getCategory();
$brandObj = $cars->getBrand();

$pdf->setX(80);
$pdf->Cell(5,51,strtoupper($categoryObj ? $categoryObj->name : ""));

$pdf->Ln(18.5);

$pdf->setX(12);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"VEHICULO: "); break;
    case 'EN': $pdf->Cell(5,51,"VEHICLE: "); break;
    default:   $pdf->Cell(5,51,"VEHICULO: "); break;
}

$pdf->setX(80);
$pdf->Cell(5,51,strtoupper(($brandObj ? $brandObj->name : "")." ".$cars->name." [".$cars->token."]"));

$pdf->Ln(14.5);

$pdf->setX(12);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"COSTO POR DIA: "); break;
    case 'EN': $pdf->Cell(5,51,"COST PER DAY: "); break;
    default:   $pdf->Cell(5,51,"COSTO POR DIA: "); break;
}

$pdf->setX(80);
$pdf->Cell(5,51,$sell->price." ".$principal->currency);

$pdf->Ln(15);

$pdf->setX(12);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"LUGAR DE ENTREGA AL CLIENTE: "); break;
    case 'EN': $pdf->Cell(5,51,"PLACE OF DELIVERY TO THE CUSTOMER: "); break;
    default:   $pdf->Cell(5,51,"LUGAR DE ENTREGA AL CLIENTE: "); break;
}

$pdf->setX(80);
$pdf->Cell(5,51,strtoupper(utf8_decode($sell->place_start)));

$pdf->Ln(14.5);

$pdf->setX(12);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"FECHA DE ENTREGA AL CLIENTE: "); break;
    case 'EN': $pdf->Cell(5,51,"DELIVERY DATE TO THE CUSTOMER: "); break;
    default:   $pdf->Cell(5,51,"FECHA DE ENTREGA AL CLIENTE: "); break;
}

$pdf->setX(80);
$pdf->Cell(5,51,date("d-m-Y", strtotime($sell->start_at)));

$pdf->Ln(14.5);

$pdf->setX(12);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"FECHA DE ENTREGA DEL CLIENTE: "); break;
    case 'EN': $pdf->Cell(5,51,"CUSTOMER DELIVERY DATE: "); break;
    default:   $pdf->Cell(5,51,"FECHA DE ENTREGA DEL CLIENTE: "); break;
}

$pdf->setX(80);
$pdf->Cell(5,51,date("d-m-Y", strtotime($sell->end_at)));

$pdf->Ln(15.5);

$pdf->setX(12);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"TOTAL DE DIAS: "); break;
    case 'EN': $pdf->Cell(5,51,"TOTAL DAYS: "); break;
    default:   $pdf->Cell(5,51,"TOTAL DE DIAS: "); break;
}

$pdf->setX(80);
$pdf->Cell(5,51,$sell->day);

$pdf->Ln(15.5);

$pdf->setX(12);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"BALANCE DE RESERVACION: "); break;
    case 'EN': $pdf->Cell(5,51,"RESERVATION BALANCE: "); break;
    default:   $pdf->Cell(5,51,"BALANCE DE RESERVACION: "); break;
}
$pdf->setX(80);
$pdf->Cell(5,51,$totpayments." ".$principal->currency);

$pdf->Ln(31);

$pdf->setX(12);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"ENTREGA DE COMBUSTIBLE: "); break;
    case 'EN': $pdf->Cell(5,51,"FUEL DELIVERY: "); break;
    default:   $pdf->Cell(5,51,"ENTREGA DE COMBUSTIBLE: "); break;
}

$pdf->setX(120);
$pdf->Cell(5,31,"SUBTOTAL: ".($sell->price*$sell->day)." ".$principal->currency);

$pdf->setX(120);
$pdf->Cell(5,51,"TOTAL EXTRAS: ".$sell->xtotal." ".$principal->currency);

$pdf->setX(120);
$pdf->Cell(5,71,"OTROS COBROS: ".$sell->plane." ".$principal->currency);

$pdf->Ln(31);

$pdf->setX(12);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"MONTO PENDIENTE: "); break;
    case 'EN': $pdf->Cell(5,51,"PENDING AMOUNT: "); break;
    default:   $pdf->Cell(5,51,"MONTO PENDIENTE: "); break;
}

$pdf->setX(80);
$pdf->Cell(5,51,(($sell->price*$sell->day)-$totpayments)." ".$principal->currency);

$pdf->Ln(16);

$pdf->setX(12);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"TOTAL FACTURADO: "); break;
    case 'EN': $pdf->Cell(5,51,"TOTAL BILLED: "); break;
    default:   $pdf->Cell(5,51,"TOTAL FACTURADO: "); break;
}
$pdf->setX(80);
$pdf->Cell(5,51,($sell->price*$sell->day)." ".$principal->currency);

$pdf->Ln(17);

$pdf->setX(12);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,"NOTA DE RESERVACION: "); break;
    case 'EN': $pdf->Cell(5,51,"RESERVATION NOTE: "); break;
    default:   $pdf->Cell(5,51,"NOTA DE RESERVACION: "); break;
}
$pdf->setX(80);
$pdf->Cell(5,51,strtoupper($sell->comment));

$pdf->AddPage();

$pdf->SetFont('Arial','B',12);
$pdf->SetTextColor(0,0,0);

$pdf->Ln(-20);
$pdf->SetFont('Arial','B',12);
$pdf->SetTextColor(0,0,0);
$pdf->setX(70);

switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,strtoupper(utf8_decode("CLAUSULAS Y CONDICIONES: "))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper(utf8_decode("CLAUSES AND CONDITIONS: "))); break;
    default:   $pdf->Cell(5,51,strtoupper(utf8_decode("CLAUSULAS Y CONDICIONES: "))); break;
}

$pdf->SetFont('Arial','',9);
$pdf->Ln(35);
$pdf->setX(10);

switch ($clients->language){
    case 'ES': $pdf->MultiCell(189,4,mb_strtoupper(utf8_decode("* RESERVAS CON UN DEPÓSITO DE $50 DÓLARES.

* Al momento que recibas el vehículo, debes entregarnos tu pasaporte si eres ciudadano americano, o tu residencia si eres residente de los Estados Unidos, esa es la garantía del vehículo y norma fundamental de nuestra empresa.

* Al momento de cancelar una reservación automáticamente pierde su depósito. El depósito solo es reembolsable 48 horas después de haber reservado.

* En caso de un accidente el cliente se compromete a arreglos y daños, estos arreglos deben realizarse antes de salir de la República Dominicana.

* NOTA: Si usted no quiere dejar los documentos se le deberá de pagar un depósito de 1000 a 1500 dólares que se le será devuelto al momento de la entrega del vehículo, ese depósito tiene que ser cash.

* 24 horas antes del viaje nuestro chofer le escribirá para ponerse de acuerdo con usted para la entrega del vehículo, que será en el aeropuerto.

* GRACIAS POR PREFERIR LOS SERVICIOS DE ".strtoupper($principal->name)), 'ISO-8859-1'),0,'J'); break;

    case 'EN': $pdf->MultiCell(189,4,mb_strtoupper(utf8_decode("* RESERVATIONS REQUIRE A $50 DEPOSIT.

• Upon receiving the vehicle, you must provide your passport if you are a U.S. citizen, or your U.S. residency card if you are a U.S. resident. This serves as the vehicle guarantee and is a fundamental company policy.

• If a reservation is canceled, the deposit is automatically forfeited. The deposit is only refundable within 48 hours after the reservation is made.

• In case of an accident, the client agrees to cover all repairs and damages. These must be settled before leaving the Dominican Republic.

• NOTE: If you prefer not to leave your documents, a deposit of $1,000 to $1,500 USD is required. This deposit will be refunded upon returning the vehicle and must be paid in cash.

• 24 hours before your trip, our driver will contact you to coordinate the vehicle delivery, which will take place at the airport.

• THANK YOU FOR CHOOSING THE SERVICES OF ". strtoupper($principal->name)), 'ISO-8859-1'),0,'J'); break;

    default: $pdf->MultiCell(189,4,mb_strtoupper(utf8_decode("* RESERVAS CON UN DEPÓSITO DE $50 DÓLARES.

* Al momento que recibas el vehículo, debes entregarnos tu pasaporte si eres ciudadano americano, o tu residencia si eres residente de los Estados Unidos, esa es la garantía del vehículo y norma fundamental de nuestra empresa.

* Al momento de cancelar una reservación automáticamente pierde su depósito. El depósito solo es reembolsable 48 horas después de haber reservado.

* En caso de un accidente el cliente se compromete a arreglos y daños, estos arreglos deben realizarse antes de salir de la República Dominicana.

* NOTA: Si usted no quiere dejar los documentos se le deberá de pagar un depósito de 1000 a 1500 dólares que se le será devuelto al momento de la entrega del vehículo, ese depósito tiene que ser cash.

* 24 horas antes del viaje nuestro chofer le escribirá para ponerse de acuerdo con usted para la entrega del vehículo, que será en el aeropuerto.

* GRACIAS POR PREFERIR LOS SERVICIOS DE ".strtoupper($principal->name)), 'ISO-8859-1'),0,'J'); break;
}

if(!empty($sell->firma)){
    $srcx = '../'.trim($sell->firma);
    ponerImagenSegura($pdf, $srcx, 10, 330, 50);
}

if(!empty($ticket_image2)){
    $src = "../CF-SYSTEMS/storage/configuration/" . trim($ticket_image2);
    ponerImagenSegura($pdf, $src, 120, 210, 30);
}

$pdf->Ln(22);
$pdf->setX(10);
switch ($clients->language){
    case 'ES': $pdf->Cell(5,51,strtoupper(utf8_decode("FIRMA DEL CLIENTE: "))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper(utf8_decode("CUSTOMER SIGNATURE: "))); break;
    default:   $pdf->Cell(5,51,strtoupper(utf8_decode("FIRMA DEL CLIENTE: "))); break;
}

$pdf->SetFont('Arial','',12);
$pdf->Ln(-2);
$pdf->setX(120);
$pdf->MultiCell(189,4,strtoupper(utf8_decode($title)),0,'J');

$pdf->SetFont('Arial','',9);
$pdf->Ln(5);
$pdf->setX(120);
$pdf->MultiCell(80,4,mb_strtoupper(utf8_decode($stock->address), 'ISO-8859-1'),0,'L');

$pdf->Output();
ob_end_flush();
?>