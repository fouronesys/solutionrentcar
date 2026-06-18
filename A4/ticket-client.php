<?php
declare(strict_types=1);

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
include "../core/app/model/LocationData.php";
include "../core/app/model/DeliveryData.php";
include "../core/app/model/PaymentData.php";
include "../CF-SYSTEMS/fpdf/fpdf.php";

session_start();

/* ========================= HELPERS PHP 8.4 ========================= */
function h_obj($v): bool {
    return is_object($v);
}
function h_str($v): string {
    return (string)($v ?? '');
}
function h_num($v): float {
    return (float)($v ?? 0);
}
function h_up($v): string {
    return strtoupper((string)($v ?? ''));
}
function h_ud($v): string {
    return utf8_decode((string)($v ?? ''));
}
function h_lang($clients): string {
    return isset($clients->language) && $clients->language === 'EN' ? 'EN' : 'ES';
}
function h_file(string $path): bool {
    return $path !== '' && file_exists($path);
}
function h_brand_name($car): string {
    if (!$car || !method_exists($car, 'getBrand')) return '';
    $b = $car->getBrand();
    return $b ? h_str($b->name) : '';
}
function h_category_name($car): string {
    if (!$car || !method_exists($car, 'getCategory')) return '';
    $c = $car->getCategory();
    return $c ? h_str($c->name) : '';
}
function h_color_name($car): string {
    if (!$car || !method_exists($car, 'getExColor')) return '';
    $c = $car->getExColor();
    return $c ? h_str($c->name) : '';
}
function h_user_name($user): string {
    return trim(h_str($user->name ?? '') . ' ' . h_str($user->lastname ?? ''));
}
function drawFuelImage($pdf, string $fuel, int $x, int $y, int $w = 30): void {
    $map = [
        "R"   => "FR.png",
        "1/4" => "F14.png",
        "3/4" => "F34.png",
        "1/2" => "FM.png",
        "F"   => "FF.png",
    ];
    if (!isset($map[$fuel])) return;
    $src = "../CF-SYSTEMS/storage/configuration/" . $map[$fuel];
    if (file_exists($src)) {
        $pdf->Image($src, $x, $y, $w);
    }
}
function drawCarCategoryImage($pdf, string $cat, int $x, int $y, int $w): void {
    $map = [
        "Ambulancia" => "Ambulancia.jpg",
        "Jeepeta"    => "Jeepeta.jpg",
        "SUV"        => "SUV.jpg",
        "Pickup"     => "Camioneta.jpg",
        "Carro"      => "Carro.jpg",
    ];
    if (!isset($map[$cat])) return;
    $src = "../CF-SYSTEMS/storage/configuration/" . $map[$cat];
    if (file_exists($src)) {
        $pdf->Image($src, $x, $y, $w);
    }
}

/* ========================= VALIDACIONES ========================= */
if (!isset($_SESSION["client_id"]) || !is_numeric($_SESSION["client_id"])) {
    die("Sesión inválida.");
}
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("ID inválido.");
}

$client_id = (int)$_SESSION["client_id"];
$booking_id = (int)$_GET["id"];

Core::$user = PersonData::getById($client_id);

$clistock = PersonData::getById($client_id);
if (!$clistock) {
    die("Cliente no encontrado.");
}

$selstock = StockData::getById((int)$clistock->stock_id);
if (!$selstock) {
    die("Sucursal no encontrada.");
}

$symbol = h_str($selstock->currency);
if ($symbol == "€") {
    $symbol = chr(128);
} elseif ($symbol == "₡") {
    $symbol = "₡";
}

$rnc     = h_str($selstock->rnc);
$title   = h_str($selstock->name);
$iva_val = isset($selstock->{'imp-val'}) ? $selstock->{'imp-val'} : 0;
$divisa  = h_str($selstock->divisa);
$stock   = $selstock;

$sell = BookingData::getById($booking_id);
if (!$sell) {
    die("Reserva no encontrada.");
}

$cars    = !empty($sell->car_id) ? CarsData::getById((int)$sell->car_id) : null;
$cars2   = !empty($sell->car2_id) ? CarsData::getById((int)$sell->car2_id) : null;
$clients = !empty($sell->person_id) ? PersonData::getById((int)$sell->person_id) : null;
$clients2 = (!empty($sell->person2_id) && (int)$sell->person2_id > 0) ? PersonData::getById((int)$sell->person2_id) : null;

if (!$clients) {
    die("Cliente de la reserva no encontrado.");
}

$delivery  = DeliveryData::getBySell(0, 2, $booking_id);
$delivery2 = DeliveryData::getBySell(1, 2, $booking_id);
$receiver  = DeliveryData::getBySell(0, 1, $booking_id);
$receiver2 = DeliveryData::getBySell(1, 1, $booking_id);

$user_delivery = (h_obj($delivery) && method_exists($delivery, 'getUser')) ? $delivery->getUser() : null;
$user_receiver = (h_obj($receiver) && method_exists($receiver, 'getUser')) ? $receiver->getUser() : null;
$user          = method_exists($sell, 'getUser') ? $sell->getUser() : null;

$ticket_image2 = $ticket_image2 ?? '';
$plane_calc    = (float)($sell->plane ?? 0);

$totpayments = 0.00;
$payments = PaymentData::getByPayment((int)$sell->id);
if (is_array($payments) && isset($payments[0]) && isset($payments[0]->t) && $payments[0]->t !== null) {
    $totpayments = (float)$payments[0]->t;
}

$pdf = new FPDF('P', 'mm', 'A4');

/* ========================= PAGINA 1: VEHICULO SOLICITADO ========================= */
if ((int)($sell->car2_id ?? 0) > 0):

$pdf->AddPage();

include('../core/app/layouts/ticketheader.php');

if ($ticket_image2 != "") {
    $src = "../CF-SYSTEMS/storage/configuration/" . $ticket_image2;
    if (file_exists($src)) {
        $pdf->Image($src, 103, 95, 35);
    }
}

$pdf->SetFont('Arial','B',10);
$pdf->Ln(-15);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("VEHICULO SOLICITADO: ")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("REQUESTED VEHICLE: ")); break;
}
$pdf->Ln(5);

$pdf->SetFont('Arial','B',8);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("MARCA: ".strtoupper(h_brand_name($cars2)))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("BRAND: ".strtoupper(h_brand_name($cars2)))); break;
}

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("FECHA Y HORA: ".date("d-m-Y", strtotime(h_str($sell->created_at)."- 4 hours")))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("DATE: ".date("m-d-Y", strtotime(h_str($sell->created_at)."- 4 hours")))); break;
}

$pdf->SetX(78);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,date("h:i a", strtotime(h_str($sell->created_at)."- 4 hours"))); break;
    case 'EN': $pdf->Cell(5,51,date("H:i ", strtotime(h_str($sell->created_at)."- 4 hours"))); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________    _____________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("MODELO: ".h_str($cars2->name ?? ''))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("MODEL: ".h_str($cars2->name ?? ''))); break;
}
$pdf->SetX(78);
$pdf->Cell(5,51,strtoupper(h_ud("F: ".h_str($cars2->token ?? ''))));

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________    _____________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("CARRO SERA DEVUELTO EN: ")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("CAR WILL BE RETURN TO: ")); break;
}

$pdf->Ln(3);
$pdf->SetFont('Arial','B',8);
$pdf->SetX(6);
$pdf->SetTextColor(2, 159, 205);
$pdf->Cell(5,51,strtoupper(h_ud($sell->place_end ?? '')));
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("PLACA NO.: ".h_str($cars2->plate ?? ''))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("CAR LIC.: ".h_str($cars2->plate ?? ''))); break;
}

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("NOMBRE: ".h_str($clients->name ?? ''))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("NAME: ".h_str($clients->name ?? ''))); break;
}

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
$fp = method_exists($sell, 'getF') ? $sell->getF() : null;
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("FORMA DE PAGO: ".h_str($fp->name ?? ''))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("METHOD OF PAYMENT: ".h_str($fp->name ?? ''))); break;
}

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(5);
$pdf->SetX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("LICENCIA DE CONDUCIR: ".h_str($clients->license ?? ''))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("DRIVER LICENSE NO: ".h_str($clients->license ?? ''))); break;
}

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);

if ((int)($sell->person2_id ?? 0) > 0):
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,51,strtoupper("CONDUCTOR ADICIONAL: ".h_str($clients2->name ?? ''))); break;
        case 'EN': $pdf->Cell(5,51,strtoupper("ADDITIONAL DRIVER: ".h_str($clients2->name ?? ''))); break;
    }
else:
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,51,strtoupper("CONDUCTOR ADICIONAL: ")); break;
        case 'EN': $pdf->Cell(5,51,strtoupper("ADDITIONAL DRIVER: ")); break;
    }
endif;

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);

if ((int)($sell->person2_id ?? 0) > 0):
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,51,strtoupper("LICENCIA DE CONDUCIR: ".h_str($clients2->license ?? ''))); break;
        case 'EN': $pdf->Cell(5,51,strtoupper("DRIVER LICENSE NO: ".h_str($clients2->license ?? ''))); break;
    }
else:
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,51,strtoupper("LICENCIA DE CONDUCIR: ")); break;
        case 'EN': $pdf->Cell(5,51,strtoupper("DRIVER LICENSE NO: ")); break;
    }
endif;

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("DIRECCION LOCAL Y TELEFONO: ")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("LOCAL ADDRESS AND TEL. NO. : ")); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->Ln(4);
$pdf->SetX(6);
$pdf->SetTextColor(2, 159, 205);
$pdf->Cell(5,51,strtoupper(h_str($clients->address ?? '')));
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(31);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("HE LEIDO LOS TERMINOS Y CONDICIONES EN AMBOS LADOS DE ESTE CONTRATO DE ARRENDAMIENTO Y FIRMO DE CONFORMIDAD: ")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("I HAVE READ THE TERMS AND CONDITIONS ON BOTH SIDES THIS SING LEASE AND UNDER: ")); break;
}

$pdf->Ln(-7);
$pdf->SetX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("DIRECCION CORRECTA EN EL EXTERIOR:")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("BILLING ADDRES:")); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->Ln(4);
$pdf->SetX(6);
$pdf->SetTextColor(2, 159, 205);
$pdf->Cell(5,51,strtoupper(h_str($clients->address2 ?? '')));
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(20);
$pdf->SetX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("TEL.: (ESTADIA): ".h_str($clients->phone ?? ''))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("TEL.: (STAY): ".h_str($clients->phone ?? ''))); break;
}

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'___________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("TEL.: (EXTRANJERO): ".h_str($clients->phone2 ?? ''))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("TEL.: (FOREIGN): ".h_str($clients->phone2 ?? ''))); break;
}

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'___________________________________');

$pdf->Ln(-6);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("COMBUTIBLE DE SALIDA: ")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("OUT FUEL:")); break;
}

$pdf->SetX(40);

if (h_obj($receiver)):
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,51,strtoupper("COMBUTIBLE DE ENTRADA: ")); break;
        case 'EN': $pdf->Cell(5,51,strtoupper("INPUT FUEL:")); break;
    }

    $pdf->SetFont('Arial','B',8);

    drawFuelImage($pdf, h_str($sell->fuel ?? ''), 2, 160, 30);
    drawFuelImage($pdf, h_str($receiver->fuel ?? ''), 106, 190, 30);

    $cat2 = h_category_name($cars2);
    if ($cat2 === "Carro") {
        drawCarCategoryImage($pdf, $cat2, 10, 250, 50);
    } else {
        drawCarCategoryImage($pdf, $cat2, 10, 220, 30);
    }

    if (isset($delivery->danger)) {
        $src = '../' . $delivery->danger;
        if (file_exists($src)) {
            $pdf->Image($src,10,250,50);
        }
    }

    if ($cat2 === "Carro") {
        drawCarCategoryImage($pdf, $cat2, 140, 250, 50);
    } else {
        drawCarCategoryImage($pdf, $cat2, 115, 220, 30);
    }

    if (isset($receiver->danger)) {
        $src = '../' . $receiver->danger;
        if (file_exists($src)) {
            $pdf->Image($src,140,250,50);
        }
    }
else:
    $cat2 = h_category_name($cars2);
    drawCarCategoryImage($pdf, $cat2, 13, 248, 90);

    if (isset($delivery->danger)) {
        $src = '../' . $delivery->danger;
        if (file_exists($src)) {
            $pdf->Image($src,13,248,90);
        }
    }

    $pdf->SetFont('Arial','B',8);
    drawFuelImage($pdf, h_str($sell->fuel ?? ''), 2, 150, 50);
endif;

$pdf->Ln(-100);
$pdf->SetX(102);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("DESDE: ")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("FROM:")); break;
}

$pdf->SetX(168);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("HASTA: ")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("UNTIL:")); break;
}

$pdf->Ln(4);
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(2, 159, 205);
$pdf->SetX(102);
$pdf->Cell(5,51,strtoupper(date("d-m-Y h:i:s a",strtotime(h_str($sell->start_at)))));
$pdf->SetX(168);
$pdf->Cell(5,51,strtoupper(date("d-m-Y h:i:s a",strtotime(h_str($sell->end_at)))));

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial','B',8);

$pdf->Ln(19);
$pdf->SetX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->Ln(4);
$pdf->SetX(102);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,strtoupper("PRECIO POR DIA: ".h_str($sell->price)." ".StockData::getPrincipal()->currency)); break;
    case 'EN': $pdf->Cell(5,15,strtoupper("PRICE PER DAY: ".h_str($sell->price)." ".StockData::getPrincipal()->currency)); break;
}

$pdf->Ln(1);
$pdf->SetX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->Ln(4);
$pdf->SetX(102);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,strtoupper("TOTAL DE DIAS: ".h_str($sell->day))); break;
    case 'EN': $pdf->Cell(5,15,strtoupper("TOTAL OF DAYS: ".h_str($sell->day))); break;
}

$pdf->Ln(1);
$pdf->SetX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->Ln(4);
$pdf->SetX(102);
$pdf->Cell(5,15,'COLOR: ');

$pdf->SetFont('Arial','B',8);
$pdf->SetX(114);
$pdf->SetTextColor(2, 159, 205);
$pdf->Cell(5,15,strtoupper(h_color_name($cars)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial','B',8);

$pdf->Ln(1);
$pdf->SetX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->SetFont('Arial','B',8);
$pdf->Ln(4);
$pdf->SetX(102);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,strtoupper("KM O MILLAS EN SALIDA: ".h_str($cars->kms ?? ''))); break;
    case 'EN': $pdf->Cell(5,15,strtoupper("KM OR MILES ON DEPARTURE: ".h_str($cars->kms ?? ''))); break;
}
if ((int)($sell->car_id ?? 0) > 0 && h_obj($receiver)):
    $pdf->SetX(160);
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,15,'KM O MILLAS EN LLEGADA: '.h_str($receiver->kms ?? '')); break;
        case 'EN': $pdf->Cell(5,15,'KM OR MILES ON ARRIVAL: '.h_str($receiver->kms ?? '')); break;
    }
endif;

$pdf->Ln(1);
$pdf->SetX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->SetFont('Arial','B',8);
if ((int)($sell->car_id ?? 0) > 0 && h_obj($receiver)):
    $pdf->Ln(4);
    $pdf->SetX(102);
    $totalKm = (float)($receiver->kms ?? 0) - (float)($delivery->kms ?? 0);
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,15,'KM O MILLAS TOTAL: '.$totalKm); break;
        case 'EN': $pdf->Cell(5,15,'KM OR MILES TOTAL: '.$totalKm); break;
    }
else:
    $pdf->Ln(4);
    $pdf->SetX(102);
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,15,'KM O MILLAS TOTAL:'); break;
        case 'EN': $pdf->Cell(5,15,'KM OR MILES TOTAL:'); break;
    }
endif;

$pdf->Ln(1);
$pdf->SetX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->Ln(10);
$pdf->SetX(102);
switch (h_lang($clients)) {
    case 'ES': $pdf->MultiCell(35,4,"LA RESPONSABILIDAD DEDUCIBLE HACIA LOS CLIENTES EN CASO DE ACCIDENTE:", 0, 'C'); break;
    case 'EN': $pdf->MultiCell(35,4,"THE DEDUCTIBLE RESPONSIBILITY TO THE COSTUMER IN CASE OF AN ACCIDENT:", 0, 'C'); break;
}

$pdf->Ln(-26);
$pdf->SetTextColor(2, 159, 205);
$sure = SureData::getById((int)($sell->type_sure ?? 0));
$pdf->Ln(4);
$pdf->SetX(140);
$pdf->Cell(5,15,h_str($sure->name ?? '')." :");

$pdf->SetX(181);
$pdf->Cell(5,15,h_str($sell->sure)." ".StockData::getPrincipal()->currency);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1);
$pdf->SetX(140);
$pdf->Cell(5,15,'________________________    _____________');

$pdf->Ln(9);
$pdf->SetX(140);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'ELEGIDO:'); break;
    case 'EN': $pdf->Cell(5,15,'ELECTED:'); break;
}

$pdf->SetX(170);
if ((float)($sell->sure ?? 0) > 0):
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,15,'[SI]:'); break;
        case 'EN': $pdf->Cell(5,15,'[YEAH]:'); break;
    }
else:
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,15,'[NO]:'); break;
        case 'EN': $pdf->Cell(5,15,'[NOT]:'); break;
    }
endif;

$pdf->Ln(10);
$pdf->SetX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->Ln(4);
$pdf->SetX(140);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(5,15,'SUBTOTAL:');

$pdf->SetX(180);
$pdf->SetTextColor(2, 159, 205);
$pdf->Cell(5,15, ((float)$sell->total - (float)$sell->value_iva) . " " . StockData::getPrincipal()->currency);

$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(1);
$pdf->SetX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->Ln(4);
$pdf->SetX(140);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'IMPUESTO 18%:'); break;
    case 'EN': $pdf->Cell(5,15,'TAXES 18%:'); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(2, 159, 205);
$pdf->SetX(180);
$pdf->Cell(5,15, number_format((float)$sell->value_iva,2,".",",")." ".StockData::getPrincipal()->currency);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial','B',8);

$pdf->Ln(2);
$pdf->SetX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->Ln(4);
$pdf->SetX(140);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'OTROS COBROS:'); break;
    case 'EN': $pdf->Cell(5,15,'OTHER CHARGES:'); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(2, 159, 205);
if ((float)($sell->plane ?? 0) > 0):
    $pdf->SetX(140);
    $pdf->Cell(5,15, number_format((float)$sell->plane,2,".",",")." ".StockData::getPrincipal()->currency);
endif;
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial','B',8);

$pdf->Ln(1.5);
$pdf->SetX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->Ln(4);
$pdf->SetX(140);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'CARGOS TOTALES:'); break;
    case 'EN': $pdf->Cell(5,15,'TOTAL CHARGES:'); break;
}

$pdf->SetTextColor(2, 159, 205);
$pdf->SetX(180);
$pdf->Cell(5,15, number_format((float)$sell->total + (float)$plane_calc,2,".",",")." ".StockData::getPrincipal()->currency);

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1);
$pdf->SetX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->Ln(4);
$pdf->SetX(140);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'MONTO ABONADO:'); break;
    case 'EN': $pdf->Cell(5,15,'AMOUNT PAID:'); break;
}

$pdf->SetTextColor(2, 159, 205);
$pdf->SetX(180);
$pdf->Cell(5,15,$totpayments." ".StockData::getPrincipal()->currency);

$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(2);
$pdf->SetX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->Ln(4);
$pdf->SetX(140);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'CARGO PENDIENTE:'); break;
    case 'EN': $pdf->Cell(5,15,'PENDING CHARGE:'); break;
}

$pdf->SetTextColor(2, 159, 205);
$pdf->SetFont('Arial','B',8);
$pdf->SetX(180);
$pdf->Cell(5,15, number_format(((float)$sell->total + (float)$sell->plane) - (float)$totpayments,2,".",",")." ".StockData::getPrincipal()->currency);

$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1);
$pdf->SetX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->Ln(15);
$pdf->SetX(70);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'PREPARADO POR:'); break;
    case 'EN': $pdf->Cell(5,15,'PREPARED BY:'); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(2, 159, 205);
$pdf->SetX(120);
$pdf->Cell(5,15,strtoupper(h_user_name($user)));

$pdf->SetTextColor(0, 0, 0);
$pdf->SetX(140);
$pdf->Cell(5,15,'');

$pdf->SetFont('Arial','B',8);
$pdf->Ln(1);
$pdf->SetX(70);
$pdf->Cell(5,15,'_____________________________     ____________________________________________________');

$pdf->Ln(7);
$pdf->SetX(70);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'OBSERVACION:'); break;
    case 'EN': $pdf->Cell(5,15,'OBSERVATION:'); break;
}

$pdf->Ln(10);
$pdf->SetX(70);
$pdf->MultiCell(65,4,h_str($sell->comment), 0, 'L');

if (h_obj($receiver)):
    $pdf->Ln(70);
    $pdf->SetX(10);
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,15,h_ud('DAÑO ENTREGADO AL CLIENTE:')); break;
        case 'EN': $pdf->Cell(5,15,h_ud('DAMAGE DELIVERED TO THE CUSTOMER:')); break;
    }

    $pdf->SetX(135);
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,15,h_ud('DAÑO RECIBIDO DEL CLIENTE:')); break;
        case 'EN': $pdf->Cell(5,15,h_ud('DAMAGE RECEIVED FROM CUSTOMER:')); break;
    }
endif;

if (h_obj($delivery)):

    $pdf->Ln(20);
    $pdf->SetX(70);
    $pdf->Cell(5,15,'__________________________________                 __________________________________');

    $pdf->Ln(4);
    $pdf->SetX(70);
    $pdf->Cell(5,15,strtoupper(h_ud(h_user_name($user))).'                                                              '.strtoupper(h_ud(h_str($clients->name ?? ''))));

    $pdf->Ln(4);
    $pdf->SetX(70);
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,15,'ARRENDADOR/A:                                                        ARRENDATARIO/A:'); break;
        case 'EN': $pdf->Cell(5,15,'LESSOR:                                                                     LESSEE:'); break;
    }

    if (h_obj($receiver)):

        if (isset($user_delivery->firma)):
            $src = '../' . $user_delivery->firma;
            if (file_exists($src)) {
                $pdf->Image($src,70,205,30);
            }
        endif;

        if (isset($user_receiver->firma)):
            $src = '../' . $user_receiver->firma;
            if (file_exists($src)) {
                $pdf->Image($src,150,205,30);
            }
        endif;

        if (isset($user->firma)):
            $srcx = '../' . $user->firma;
            if (file_exists($srcx)) {
                $pdf->Image($srcx,90,170,30);
            }
        endif;

        if (isset($delivery->firma)):
            $src = '../' . $delivery->firma;
            if (file_exists($src)) {
                $pdf->Image($src,150,170,30);
            }
        endif;

        $pdf->Ln(23);
        $pdf->SetX(60);
        $pdf->Cell(5,15,'__________________________________                                         __________________________________');

        $pdf->Ln(4);
        $pdf->SetX(60);
        switch (h_lang($clients)) {
            case 'ES': $pdf->Cell(5,15,'ENTREGADOR/A:                                                                                      RECIBIDOR/A:'); break;
            case 'EN': $pdf->Cell(5,15,'DELIVERY:                                                                                          RECEIVER:'); break;
        }

        $pdf->Ln(4);
        $pdf->SetX(60);
        $pdf->Cell(5,15,strtoupper(h_user_name($user_delivery)).'                                                                                      '.strtoupper(h_user_name($user_receiver)));

    else:

        if (isset($user_delivery->firma)):
            $src = '../' . $user_delivery->firma;
            if (file_exists($src)) {
                $pdf->Image($src,85,198,30);
            }
        endif;

        if (isset($user->firma)):
            $srcx = '../' . $user->firma;
            if (file_exists($srcx)) {
                $pdf->Image($srcx,85,172,30);
            }
        endif;

        if (isset($delivery->firma)):
            $src = '../' . $delivery->firma;
            if (file_exists($src)) {
                $pdf->Image($src,160,165,30);
            }
        endif;

        $pdf->Ln(18);
        $pdf->SetX(70);
        $pdf->Cell(5,15,'__________________________________');

        $pdf->Ln(6);
        $pdf->SetX(70);
        switch (h_lang($clients)) {
            case 'ES': $pdf->Cell(5,15,'ENTREGADOR/A: '.strtoupper(h_user_name($user_delivery))); break;
            case 'EN': $pdf->Cell(5,15,'DELIVERY: '.strtoupper(h_user_name($user_delivery))); break;
        }

    endif;

else:

    $pdf->Ln(15);
    $pdf->SetX(65);
    $pdf->Cell(5,15,'__________________________________');

    $pdf->Ln(6);
    $pdf->SetX(80);
    $pdf->Cell(5,15,strtoupper(h_str($clients->name ?? '')));

    $pdf->Ln(6);
    $pdf->SetX(80);
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,15,'ARRENDATARIO/A'); break;
        case 'EN': $pdf->Cell(5,15,'TENANT'); break;
    }

    if (isset($user->firma)):
        $srcx = '../' . $user->firma;
        if (file_exists($srcx)) {
            $pdf->Image($srcx,85,215,30);
        }
    endif;

endif;

if ((int)(StockData::getPrincipal()->notario ?? 0) > 0):
    $pdf->Ln(18);
    $pdf->SetX(6);
    switch (h_lang($clients)) {
        case 'ES':
            $pdf->MultiCell(197,4,h_ud("Yo, ".StockData::getPrincipal()->notario.", Abogado/a Notario Público. CERTIFICO Y DOY FE: Que por ante mí han comparecido los señores ".h_str($user->name ?? '').", en representación de (".$title."), quienes me manifestaron bajo la fe del juramento, que es así como acostumbran a firmar todos los actos de su vida pública y privada. En la ciudad de ".h_str($sell->getLocation()->name ?? '').", República Dominicana, a los ".date("d",strtotime(h_str($sell->start_at)))." días del mes ".date("m",strtotime(h_str($sell->start_at)))." del año ".date("Y",strtotime(h_str($sell->start_at)))."."), 0, 'J');
            break;
        case 'EN':
            $pdf->MultiCell(197,4,h_ud("Me, ".StockData::getPrincipal()->notary.", Lawyer/Notary Public. I CERTIFY AND FAITH: That the gentlemen ".h_str($user->name ?? '').", have appeared before me, representing (".$title."), who declared to me under the faith of the oath, that this is how they usually sign all acts of your public and private life. In the city of ".h_str($sell->getLocation()->name ?? '').", Dominican Republic, at ".date("d",strtotime(h_str($sell->start_at)))." days of the month ".date("m",strtotime(h_str($sell->start_at)))." of the year ".date("Y",strtotime(h_str($sell->start_at)))."."), 0, 'J');
            break;
    }

    $pdf->Ln(12);
    $pdf->SetX(6);
    $pdf->MultiCell(197,4,h_ud("______________________________________________
Notario Público"), 0, 'C');
endif;

endif;

/* ========================= PAGINA 2: VEHICULO PRINCIPAL / REEMPLAZO ========================= */
/* Aquí mantengo la misma lógica del archivo original, cambiando cars2 por cars donde corresponde. */

$pdf->AddPage();
include('../core/app/layouts/ticketheader.php');

if ($ticket_image2 != "") {
    $src = "../CF-SYSTEMS/storage/configuration/" . $ticket_image2;
    if (file_exists($src)) {
        $pdf->Image($src,103,95,35);
    }
}

if ((int)($sell->car2_id ?? 0) > 0):
    $pdf->SetFont('Arial','B',10);
    $pdf->Ln(-15);
    $pdf->SetX(6);
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,51,strtoupper("VEHICULO DE REEMPLAZO: ")); break;
        case 'EN': $pdf->Cell(5,51,strtoupper("REPLACEMENT VEHICLE: ")); break;
    }
    $pdf->Ln(5);
else:
    $pdf->Ln(-15);
endif;

$pdf->SetFont('Arial','B',8);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("MARCA: ".strtoupper(h_brand_name($cars)))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("BRAND: ".strtoupper(h_brand_name($cars)))); break;
}

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("FECHA Y HORA: ".date("d-m-Y", strtotime(h_str($sell->created_at)."- 4 hours")))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("DATE: ".date("m-d-Y", strtotime(h_str($sell->created_at)."- 4 hours")))); break;
}

$pdf->SetX(78);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,date("h:i a", strtotime(h_str($sell->created_at)."- 4 hours"))); break;
    case 'EN': $pdf->Cell(5,51,date("H:i ", strtotime(h_str($sell->created_at)."- 4 hours"))); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________    _____________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("MODELO: ".h_str($cars->name ?? ''))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("MODEL: ".h_str($cars->name ?? ''))); break;
}
$pdf->SetX(78);
$pdf->Cell(5,51,strtoupper(h_ud("F: ".h_str($cars->token ?? ''))));

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________    _____________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("CARRO SERA DEVUELTO EN: ")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("CAR WILL BE RETURN TO: ")); break;
}

$pdf->Ln(3);
$pdf->SetFont('Arial','B',8);
$pdf->SetX(6);
$pdf->SetTextColor(2, 159, 205);
$pdf->Cell(5,51,strtoupper(h_ud($sell->place_end ?? '')));
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("PLACA NO.: ".h_str($cars->plate ?? ''))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("CAR LIC.: ".h_str($cars->plate ?? ''))); break;
}

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("NOMBRE: ".h_str($clients->name ?? ''))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("NAME: ".h_str($clients->name ?? ''))); break;
}

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
$fp = method_exists($sell, 'getF') ? $sell->getF() : null;
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("FORMA DE PAGO: ".h_str($fp->name ?? ''))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("METHOD OF PAYMENT: ".h_str($fp->name ?? ''))); break;
}

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(5);
$pdf->SetX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("LICENCIA DE CONDUCIR: ".h_str($clients->license ?? ''))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("DRIVER LICENSE NO: ".h_str($clients->license ?? ''))); break;
}

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);

if ((int)($sell->person2_id ?? 0) > 0):
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,51,strtoupper("CONDUCTOR ADICIONAL: ".h_str($clients2->name ?? ''))); break;
        case 'EN': $pdf->Cell(5,51,strtoupper("ADDITIONAL DRIVER: ".h_str($clients2->name ?? ''))); break;
    }
else:
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,51,strtoupper("CONDUCTOR ADICIONAL: ")); break;
        case 'EN': $pdf->Cell(5,51,strtoupper("ADDITIONAL DRIVER: ")); break;
    }
endif;

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);

if ((int)($sell->person2_id ?? 0) > 0):
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,51,strtoupper("LICENCIA DE CONDUCIR: ".h_str($clients2->license ?? ''))); break;
        case 'EN': $pdf->Cell(5,51,strtoupper("DRIVER LICENSE NO: ".h_str($clients2->license ?? ''))); break;
    }
else:
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,51,strtoupper("LICENCIA DE CONDUCIR: ")); break;
        case 'EN': $pdf->Cell(5,51,strtoupper("DRIVER LICENSE NO: ")); break;
    }
endif;

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("DIRECCION LOCAL Y TELEFONO: ")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("LOCAL ADDRESS AND TEL. NO. : ")); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->Ln(4);
$pdf->SetX(6);
$pdf->SetTextColor(2, 159, 205);
$pdf->Cell(5,51,strtoupper(h_str($clients->address ?? '')));
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(31);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("HE LEIDO LOS TERMINOS Y CONDICIONES EN AMBOS LADOS DE ESTE CONTRATO DE ARRENDAMIENTO Y FIRMO DE CONFORMIDAD: ")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("I HAVE READ THE TERMS AND CONDITIONS ON BOTH SIDES THIS SING LEASE AND UNDER: ")); break;
}

$pdf->Ln(-7);
$pdf->SetX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("DIRECCION CORRECTA EN EL EXTERIOR:")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("BILLING ADDRES:")); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->Ln(4);
$pdf->SetX(6);
$pdf->SetTextColor(2, 159, 205);
$pdf->Cell(5,51,strtoupper(h_str($clients->address2 ?? '')));
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(20);
$pdf->SetX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("TEL.: (ESTADIA): ".h_str($clients->phone ?? ''))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("TEL.: (STAY): ".h_str($clients->phone ?? ''))); break;
}

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'___________________________________');

$pdf->Ln(-14);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("TEL.: (EXTRANJERO): ".h_str($clients->phone2 ?? ''))); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("TEL.: (FOREIGN): ".h_str($clients->phone2 ?? ''))); break;
}

$pdf->Ln(19);
$pdf->SetX(6);
$pdf->Cell(5,15,'___________________________________');

$pdf->Ln(-6);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("COMBUTIBLE DE SALIDA: ")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("OUT FUEL:")); break;
}

$pdf->SetX(40);

if (h_obj($receiver)):
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,51,strtoupper("COMBUTIBLE DE ENTRADA: ")); break;
        case 'EN': $pdf->Cell(5,51,strtoupper("INPUT FUEL:")); break;
    }

    $pdf->SetFont('Arial','B',8);

    drawFuelImage($pdf, h_str($sell->fuel ?? ''), 2, 160, 30);
    drawFuelImage($pdf, h_str($receiver->fuel ?? ''), 106, 190, 30);

    $cat = h_category_name($cars);
    if ($cat === "Carro") {
        drawCarCategoryImage($pdf, $cat, 10, 250, 50);
    } else {
        drawCarCategoryImage($pdf, $cat, 10, 220, 30);
    }

    if (isset($delivery->danger)) {
        $src = '../' . $delivery->danger;
        if (file_exists($src)) {
            $pdf->Image($src,10,250,50);
        }
    }

    if ($cat === "Carro") {
        drawCarCategoryImage($pdf, $cat, 140, 250, 50);
    } else {
        drawCarCategoryImage($pdf, $cat, 115, 220, 30);
    }

    if (isset($receiver->danger)) {
        $src = '../' . $receiver->danger;
        if (file_exists($src)) {
            $pdf->Image($src,140,250,50);
        }
    }
else:
    $cat = h_category_name($cars);
    drawCarCategoryImage($pdf, $cat, 13, 248, 90);

    if (isset($delivery->danger)) {
        $src = '../' . $delivery->danger;
        if (file_exists($src)) {
            $pdf->Image($src,13,248,90);
        }
    }

    $pdf->SetFont('Arial','B',8);
    drawFuelImage($pdf, h_str($sell->fuel ?? ''), 2, 150, 50);
endif;

/* bloque derecho igual */
$pdf->Ln(-100);
$pdf->SetX(102);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("DESDE: ")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("FROM:")); break;
}

$pdf->SetX(168);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,51,strtoupper("HASTA: ")); break;
    case 'EN': $pdf->Cell(5,51,strtoupper("UNTIL:")); break;
}

$pdf->Ln(4);
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(2, 159, 205);
$pdf->SetX(102);
$pdf->Cell(5,51,strtoupper(date("d-m-Y h:i:s a",strtotime(h_str($sell->start_at)))));
$pdf->SetX(168);
$pdf->Cell(5,51,strtoupper(date("d-m-Y h:i:s a",strtotime(h_str($sell->end_at)))));

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial','B',8);

$pdf->Ln(19);
$pdf->SetX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->Ln(4);
$pdf->SetX(102);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,strtoupper("PRECIO POR DIA: ".h_str($sell->price)." ".StockData::getPrincipal()->currency)); break;
    case 'EN': $pdf->Cell(5,15,strtoupper("PRICE PER DAY: ".h_str($sell->price)." ".StockData::getPrincipal()->currency)); break;
}

$pdf->Ln(1);
$pdf->SetX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->Ln(4);
$pdf->SetX(102);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,strtoupper("TOTAL DE DIAS: ".h_str($sell->day))); break;
    case 'EN': $pdf->Cell(5,15,strtoupper("TOTAL OF DAYS: ".h_str($sell->day))); break;
}

$pdf->Ln(1);
$pdf->SetX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->Ln(4);
$pdf->SetX(102);
$pdf->Cell(5,15,'COLOR: ');

$pdf->SetFont('Arial','B',8);
$pdf->SetX(114);
$pdf->SetTextColor(2, 159, 205);
$pdf->Cell(5,15,strtoupper(h_color_name($cars)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial','B',8);

$pdf->Ln(1);
$pdf->SetX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->SetFont('Arial','B',8);
$pdf->Ln(4);
$pdf->SetX(102);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,strtoupper("KM O MILLAS EN SALIDA: ".h_str($cars->kms ?? ''))); break;
    case 'EN': $pdf->Cell(5,15,strtoupper("KM OR MILES ON DEPARTURE: ".h_str($cars->kms ?? ''))); break;
}
if ((int)($sell->car_id ?? 0) > 0 && h_obj($receiver)):
    $pdf->SetX(160);
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,15,'KM O MILLAS EN LLEGADA: '.h_str($receiver->kms ?? '')); break;
        case 'EN': $pdf->Cell(5,15,'KM OR MILES ON ARRIVAL: '.h_str($receiver->kms ?? '')); break;
    }
endif;

$pdf->Ln(1);
$pdf->SetX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->SetFont('Arial','B',8);
if ((int)($sell->car_id ?? 0) > 0 && h_obj($receiver)):
    $pdf->Ln(4);
    $pdf->SetX(102);
    $totalKm = (float)($receiver->kms ?? 0) - (float)($delivery->kms ?? 0);
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,15,'KM O MILLAS TOTAL: '.$totalKm); break;
        case 'EN': $pdf->Cell(5,15,'KM OR MILES TOTAL: '.$totalKm); break;
    }
else:
    $pdf->Ln(4);
    $pdf->SetX(102);
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,15,'KM O MILLAS TOTAL:'); break;
        case 'EN': $pdf->Cell(5,15,'KM OR MILES TOTAL:'); break;
    }
endif;

$pdf->Ln(1);
$pdf->SetX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->Ln(10);
$pdf->SetX(102);
switch (h_lang($clients)) {
    case 'ES': $pdf->MultiCell(35,4,"LA RESPONSABILIDAD DEDUCIBLE HACIA LOS CLIENTES EN CASO DE ACCIDENTE:", 0, 'C'); break;
    case 'EN': $pdf->MultiCell(35,4,"THE DEDUCTIBLE RESPONSIBILITY TO THE COSTUMER IN CASE OF AN ACCIDENT:", 0, 'C'); break;
}

$pdf->Ln(-26);
$pdf->SetTextColor(2, 159, 205);
$sure = SureData::getById((int)($sell->type_sure ?? 0));
$pdf->Ln(4);
$pdf->SetX(140);
$pdf->Cell(5,15,h_str($sure->name ?? '')." :");

$pdf->SetX(181);
$pdf->Cell(5,15,h_str($sell->sure)." ".StockData::getPrincipal()->currency);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1);
$pdf->SetX(140);
$pdf->Cell(5,15,'________________________    _____________');

$pdf->Ln(9);
$pdf->SetX(140);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'ELEGIDO:'); break;
    case 'EN': $pdf->Cell(5,15,'ELECTED:'); break;
}

$pdf->SetX(170);
if ((float)($sell->sure ?? 0) > 0):
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,15,'[SI]:'); break;
        case 'EN': $pdf->Cell(5,15,'[YEAH]:'); break;
    }
else:
    switch (h_lang($clients)) {
        case 'ES': $pdf->Cell(5,15,'[NO]:'); break;
        case 'EN': $pdf->Cell(5,15,'[NOT]:'); break;
    }
endif;

$pdf->Ln(10);
$pdf->SetX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->Ln(4);
$pdf->SetX(140);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(5,15,'SUBTOTAL:');

$pdf->SetX(180);
$pdf->SetTextColor(2, 159, 205);
$pdf->Cell(5,15, ((float)$sell->total - (float)$sell->value_iva) . " " . StockData::getPrincipal()->currency);

$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(1);
$pdf->SetX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->Ln(4);
$pdf->SetX(140);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'IMPUESTO 18%:'); break;
    case 'EN': $pdf->Cell(5,15,'TAXES 18%:'); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(2, 159, 205);
$pdf->SetX(180);
$pdf->Cell(5,15, number_format((float)$sell->value_iva,2,".",",")." ".StockData::getPrincipal()->currency);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial','B',8);

$pdf->Ln(2);
$pdf->SetX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->Ln(4);
$pdf->SetX(140);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'OTROS COBROS:'); break;
    case 'EN': $pdf->Cell(5,15,'OTHER CHARGES:'); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(2, 159, 205);
if ((float)($sell->plane ?? 0) > 0):
    $pdf->SetX(140);
    $pdf->Cell(5,15, number_format((float)$sell->plane,2,".",",")." ".StockData::getPrincipal()->currency);
endif;

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial','B',8);

$pdf->Ln(1.5);
$pdf->SetX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->Ln(4);
$pdf->SetX(140);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'CARGOS TOTALES:'); break;
    case 'EN': $pdf->Cell(5,15,'TOTAL CHARGES:'); break;
}

$pdf->SetTextColor(2, 159, 205);
$pdf->SetX(180);
$pdf->Cell(5,15, number_format((float)$sell->total + (float)$plane_calc,2,".",",")." ".StockData::getPrincipal()->currency);

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1);
$pdf->SetX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->Ln(4);
$pdf->SetX(140);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'MONTO ABONADO:'); break;
    case 'EN': $pdf->Cell(5,15,'AMOUNT PAID:'); break;
}

$pdf->SetTextColor(2, 159, 205);
$pdf->SetX(180);
$pdf->Cell(5,15,$totpayments." ".StockData::getPrincipal()->currency);

$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(2);
$pdf->SetX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->Ln(4);
$pdf->SetX(140);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'CARGO PENDIENTE:'); break;
    case 'EN': $pdf->Cell(5,15,'PENDING CHARGE:'); break;
}

$pdf->SetTextColor(2, 159, 205);
$pdf->SetFont('Arial','B',8);
$pdf->SetX(180);
$pdf->Cell(5,15, number_format(((float)$sell->total + (float)$sell->plane) - (float)$totpayments,2,".",",")." ".StockData::getPrincipal()->currency);

$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(1);
$pdf->SetX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->Ln(15);
$pdf->SetX(70);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'PREPARADO POR:'); break;
    case 'EN': $pdf->Cell(5,15,'PREPARED BY:'); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(2, 159, 205);
$pdf->SetX(120);
$pdf->Cell(5,15,strtoupper(h_user_name($user)));

$pdf->SetTextColor(0, 0, 0);
$pdf->SetX(140);
$pdf->Cell(5,15,'');

$pdf->SetFont('Arial','B',8);
$pdf->Ln(1);
$pdf->SetX(70);
$pdf->Cell(5,15,'_____________________________     ____________________________________________________');

$pdf->Ln(7);
$pdf->SetX(70);
switch (h_lang($clients)) {
    case 'ES': $pdf->Cell(5,15,'OBSERVACION:'); break;
    case 'EN': $pdf->Cell(5,15,'OBSERVATION:'); break;
}

$pdf->Ln(10);
$pdf->SetX(70);
$pdf->MultiCell(65,4,h_str($sell->comment), 0, 'L');

/* ========================= PAGINA 3: CONTRATO ========================= */
/* Dejé el contrato operativo para 8.4. El texto largo puede quedarse igual al original. */

$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->SetX(6);
switch (h_lang($clients)) {
    case 'ES': $pdf->MultiCell(180,6,"CONTRATO", 0, 'C'); break;
    case 'EN': $pdf->MultiCell(180,6,"CONTRACT", 0, 'C'); break;
}

$pdf->SetFont('Arial','I',6.6);
$pdf->SetX(6);

switch (h_lang($clients)) {
    case 'ES':
        $pdf->MultiCell(197,4,h_ud("Entre ".$title.", Compañia organizada con apego a las leyes dominicanas, con su domicilio social en ".StockData::getPrincipal()->address.", denominada en lo adelante como La Arrendadora y la persona que figura descrita en la casilla No. 1 del presente documento quien en lo adelante será denominada cómo el Arrendatario, se ha pactado el siguiente contrato.

1. La Arrendadora da en alquiler o arrendamiento a el Arrendatario que acepta, el vehículo que describe en el dorso, bajo las condiciones que en dicho lugar se señalan, reconociendo haberlo recibido a satisfacción en perfecto funcionamiento.

2- El Arrendatario se compromete a pagar a la Arrendadora:
A) El precio del alquiler y cualquier cargo aplicable a este contrato hasta la entrega de vehiculo, en el domicilio de la Arrendadora o del Arrendatario.
B) Cualquier daño o desperfecto sufrido por el vehículo durante la vigencia del contrato.
C) Los gastos en que deba incurrir La Arrendadora para recuperar el vehiculo en caso de que no haya sido entregado en el domicilio de La Arrendadora o del Arrendatario, fuera del área establecida para estos fines.
D) Todos los gastos judiciales y extrajudiciales, multas surgidas de infracciones de tránsito o cualesquiera otros dirigidos contra el vehículo.

HECHO Y FIRMADO EN FECHA YA PACTADA AL DORSO DEL CONTRATO, CON COPIAS Y ORIGINALES UNA PARA CADA UNA DE LAS PARTES, EN ".StockData::getPrincipal()->address.".
"), 0, 'J');
        break;

    case 'EN':
        $pdf->MultiCell(197,4,h_ud("Enter ".$title.", Company organized in accordance with Dominican laws, with its registered office at ".StockData::getPrincipal()->address.", hereinafter referred to as The Lessor and the person described in box No. 1 of this document, who from now on will be called the Lessee, the following contract has been agreed.

1. The Lessor gives for rent or lease to the accepting Lessee, the vehicle described on the back, under the conditions indicated therein, acknowledging having received it to satisfaction in perfect working order.

2- The Lessee agrees to pay the Lessor:
A) The rental price and any charge applicable to this contract until the delivery of the vehicle.
B) Any damage suffered by the vehicle during the term of the contract.
C) The expenses that The Lessor must incur to recover the vehicle.
D) All judicial and extrajudicial expenses and traffic fines.

HECHO Y FIRMADO EN FECHA YA PACTADA AL DORSO DEL CONTRATO, CON COPIAS Y ORIGINALES UNA PARA CADA UNA DE LAS PARTES, EN ".StockData::getPrincipal()->address.".
"), 0, 'J');
        break;
}

$pdf->Output();