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
include "../core/app/model/CategoryData.php";
include "../core/app/model/LocationData.php";
include "../core/app/model/DeliveryData.php";
include "../core/app/model/PaymentData.php";
include "../core/app/model/PreferenceData.php";
include "../CF-SYSTEMS/fpdf/fpdf.php";

session_start();
if (isset($_SESSION["user_id"])) {
    Core::$user = UserData::getById($_SESSION["user_id"]);
}

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
if ($id <= 0) {
    die("ID requerido");
}

$principal = StockData::getPrincipal();
if (!$principal) {
    die("No se pudo cargar la configuración principal.");
}

$symbol = $principal->currency ?? '';
if ($symbol == "€") {
    $symbol = chr(128);
} else if ($symbol == "₡") {
    $symbol = '₡';
}

$rnc = $principal->rnc ?? '';
$title = $principal->name ?? '';
$iva_val = $principal->imp_val ?? 0;
$divisa = $principal->divisa ?? '';
$stock = $principal;

$sell = BookingData::getById($id);
if (!$sell) {
    die("No se encontró la renta.");
}

$cars = !empty($sell->car_id) ? CarsData::getById($sell->car_id) : null;
$clients = !empty($sell->person_id) ? PersonData::getById($sell->person_id) : null;
$clients2 = !empty($sell->person2_id) ? PersonData::getById($sell->person2_id) : null;
$gerente = UserData::getById(38);
$delivery = DeliveryData::getBySell(0, 2, $id);
$delivery2 = DeliveryData::getBySell(1, 2, $id);
$receiver = DeliveryData::getBySell(0, 1, $id);
$receiver2 = DeliveryData::getBySell(1, 1, $id);

$hasDelivery = !empty($delivery);
$hasReceiver = !empty($receiver);

if ($hasDelivery) {
    $user_delivery = $delivery->getUser();
}

if ($hasReceiver) {
    $user_receiver = $receiver->getUser();
}

if (!$cars) {
    $cars = new stdClass();
    $cars->year = '';
    $cars->name = '';
    $cars->plate = '';
    $cars->chassis = '';
}

if (!$clients) {
    $clients = new stdClass();
    $clients->language = 'ES';
    $clients->name = '';
    $clients->address = '';
    $clients->phone = '';
    $clients->nationality = '';
    $clients->estado = '';
    $clients->no = '';
}

$cars2 = !empty($sell->car2_id) ? CarsData::getById($sell->car2_id) : null;
$user = method_exists($sell, 'getUser') ? $sell->getUser() : null;
if (!$user) {
    $user = new stdClass();
    $user->name = '';
    $user->lastname = '';
    $user->address = '';
}

$color = explode(",", $principal->color ?? "0,0,0");
$color = array_pad($color, 3, 0);
$color[0] = (int) trim((string) $color[0]);
$color[1] = (int) trim((string) $color[1]);
$color[2] = (int) trim((string) $color[2]);

$totpayments = 0;
$payments = PaymentData::getByPayment($id);
if (!empty($payments) && isset($payments[0]) && isset($payments[0]->t) && $payments[0]->t !== null) {
    $totpayments = (float) $payments[0]->t;
}

$subtotal = 0;
$ticket_image2 = $ticket_image2 ?? '';

if (!$hasDelivery) {
    $delivery = new stdClass();
    $delivery->cat = 0;
    $delivery->radio = 0;
    $delivery->replacement = 0;
    $delivery->antenna = 0;
    $delivery->keyring = 0;
    $delivery->carpets = 0;
    $delivery->belts = 0;
    $delivery->roof_lining = 0;
    $delivery->mirrors = 0;
    $delivery->board = 0;
    $delivery->document = 0;
    $delivery->watches = 0;
    $delivery->lighter = 0;
    $delivery->cd = 0;
    $delivery->cup_holder = 0;
    $delivery->equalizer = 0;
    $delivery->rearview = 0;
    $delivery->batery = 0;
    $delivery->logo = 0;
    $delivery->top = 0;
    $delivery->bumper = 0;
    $delivery->crystals = 0;
    $delivery->plate = 0;
    $delivery->seats = 0;
    $delivery->firma = '';
}

if (!$hasReceiver) {
    $receiver = new stdClass();
    $receiver->cat = 0;
    $receiver->radio = 0;
    $receiver->replacement = 0;
    $receiver->antenna = 0;
    $receiver->keyring = 0;
    $receiver->carpets = 0;
    $receiver->belts = 0;
    $receiver->roof_lining = 0;
    $receiver->mirrors = 0;
    $receiver->board = 0;
    $receiver->document = 0;
    $receiver->watches = 0;
    $receiver->lighter = 0;
    $receiver->cd = 0;
    $receiver->cup_holder = 0;
    $receiver->equalizer = 0;
    $receiver->rearview = 0;
    $receiver->batery = 0;
    $receiver->logo = 0;
    $receiver->top = 0;
    $receiver->bumper = 0;
    $receiver->crystals = 0;
    $receiver->plate = 0;
    $receiver->seats = 0;
    $receiver->fuel = 0;
}



$pdf = new FPDF($orientation='P',$unit='mm', array(203.2,279.4));

$pdf->AddPage();

include('../core/app/layouts/ticketheader.php');


$pdf->Ln(10);

$pdf->setX(10);
$pdf->SetDrawColor($color[0],$color[1],$color[2]);
$pdf->SetFillColor($color[0],$color[1],$color[2]);
/// derecha altura tamano anchura
$pdf->Rect(10, 43, 189, 0.3, 'DF');



$pdf->setX(10);
$pdf->SetDrawColor($color[0],$color[1],$color[2]);
$pdf->SetFillColor($color[0],$color[1],$color[2]);
/// derecha altura tamano anchura
$pdf->Rect(10, 265, 189, 0.3, 'DF');


$pdf->SetDrawColor(235,238,236);
$pdf->SetFillColor(239, 239, 239 );
/// derecha altura tamano anchura
$pdf->Rect(10, 60, 189, 30, 'DF');


$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(245, 245, 245);
/// derecha altura tamano anchura
$pdf->Rect(10, 107.4, 67.5, 100, 'DF');
///////////////////////////////////////////////////

$pdf->SetFillColor(215, 215, 215);
/// derecha altura tamano anchura
$pdf->Rect(105, 60, 1, 30, 'DF');

 
$pdf->setX(10);
$pdf->SetDrawColor($color[0],$color[1],$color[2]);
$pdf->SetFillColor($color[0],$color[1],$color[2]);
/// derecha altura tamano anchura
$pdf->Rect(10, 127, 67.5, 31.5, 'DF');


$pdf->setX(10);
$pdf->SetDrawColor($color[0],$color[1],$color[2]);
$pdf->SetFillColor($color[0],$color[1],$color[2]);
/// derecha altura tamano anchura
$pdf->Rect(10, 158, 189, 0.3, 'DF');


$pdf->setX(10);
$pdf->SetDrawColor(215, 215, 215);
$pdf->SetFillColor(255, 195, 0);
/// derecha altura tamano anchura
$pdf->Rect(10, 143, 67.5, 0.3, 'DF');




$pdf->setX(10);
$pdf->SetDrawColor(215, 215, 215);
$pdf->SetFillColor(255, 195, 0);
/// derecha altura tamano anchura
$pdf->Rect(10, 207, 189, 0.3, 'DF');


$pdf->setX(10);
$pdf->SetDrawColor(215, 215, 215);
$pdf->SetFillColor(255, 195, 0);
/// derecha altura tamano anchura
$pdf->Rect(10, 107, 189, 0.3, 'DF');


$pdf->setX(10);
$pdf->SetDrawColor(215, 215, 215);
$pdf->SetFillColor(255, 195, 0);
/// derecha altura tamano anchura
$pdf->Rect(10, 173, 67.5, 0.3, 'DF');


$pdf->setX(10);
$pdf->SetDrawColor(215, 215, 215);
$pdf->SetFillColor(255, 195, 0);
/// derecha altura tamano anchura
$pdf->Rect(10, 188, 67.5, 0.3, 'DF');

$pdf->SetTextColor (0,0,0);

$pdf->SetFont('Arial','B',12);    //Letra Arial, negrita (Bold)
$pdf->Ln(10);
$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->MultiCell(180,3.5,strtoupper(utf8_decode("DETALLE DE LA RENTA")),0,'C'); break;
  case 'EN': $pdf->MultiCell(180,3.5,strtoupper(utf8_decode("RENT DETAILS")),0,'C'); break;
}


$pdf->SetFont('Arial','',8);    //Letra Arial, negrita (Bold)
$pdf->Ln(-13);

$pdf->setX(12);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"NOMBRE COMPLETO:"); break;
  case 'EN': $pdf->Cell(5,51,"FULL NAME: "); break;
}


$pdf->setX(107);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"ENTREGAR: ".date("d-m-Y", strtotime($sell->start_at))); break;
  case 'EN': $pdf->Cell(5,51,"DELIVER: ".date("d-m-Y", strtotime($sell->start_at)));  break;
}


$pdf->SetFont('Arial','',8);    //Letra Arial, negrita (Bold)
$pdf->SetTextColor (2, 159, 205);
$pdf->Ln(4);

$pdf->setX(12);
$pdf->Cell(5,51,mb_strtoupper(utf8_decode($clients->name), 'ISO-8859-1'));

$pdf->setX(107);
$pdf->Cell(5,51,strtoupper(utf8_decode($sell->place_start)));

$pdf->SetTextColor (0,0,0);

$pdf->SetFont('Arial','',8);    //Letra Arial, negrita (Bold)
$pdf->Ln(5);

$pdf->setX(12);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"DIRECCION: "); break;
  case 'EN': $pdf->Cell(5,51,"ADDRESS: "); break;
}


$pdf->setX(107);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"DEVOLVER: ".date("d-m-Y", strtotime($sell->end_at))); break;
  case 'EN': $pdf->Cell(5,51,"RETURN: ".date("d-m-Y", strtotime($sell->end_at))); break;
}

$pdf->Ln(9);
$pdf->setX(12);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"TELEFONO:"); break;
  case 'EN': $pdf->Cell(5,51,"PHONE:"); break;
}


$pdf->SetFont('Arial','',8);    //Letra Arial, negrita (Bold)
$pdf->SetTextColor (2, 159, 205);
$pdf->Ln(4);

$pdf->setX(12);
$pdf->Cell(5,32,mb_strtoupper(utf8_decode($clients->address), 'ISO-8859-1'));

$pdf->Ln(0);
$pdf->setX(107);
$pdf->Cell(5,32,strtoupper(utf8_decode($sell->place_end)));

$pdf->Ln(0);
$pdf->setX(12);
$pdf->Cell(5,51,strtoupper(utf8_decode($clients->phone)));



$pdf->SetTextColor (2,2,2);

$pdf->SetFont('Arial','B',10);    //Letra Arial, negrita (Bold)

$pdf->Ln(16);
$pdf->setX(12);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("DATOS DEL VEHICULO")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("VEHICLE DATA")); break;
}

;    //Letra Arial, negrita (Bold)

$pdf->setX(115);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("UNIDAD                  COSTO                  TOTAL")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("UNIT                    COST               TOTAL")); break;
}



$pdf->SetFont('Arial','',8); 

$pdf->Ln(14.5);
$pdf->setX(12);switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"CATEGORIA: "); break;
  case 'EN': $pdf->Cell(5,51,"CATEGORY: "); break;
}

$pdf->setX(80);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"ASIENTO DE BEBE: "); break;
  case 'EN': $pdf->Cell(5,51,"CARSEAT: "); break;
}


if($sell->price_extra1>0):
$pdf->setX(120);
$pdf->Cell(5,51,$sell->unit_extra1);


$pdf->setX(155);
$pdf->Cell(5,51,$sell->price_extra1);

$pdf->setX(189);
$pdf->Cell(5,51,$sell->unit_extra1*$sell->price_extra1);
endif;
$pdf->Ln(5);

$pdf->SetTextColor (2, 159, 205);

$pdf->setX(12);
$pdf->Cell(5,51,strtoupper(utf8_decode($cars->getCategory()->name)));

$pdf->SetTextColor (255,255,255);
$pdf->Ln(10);
$pdf->setX(12);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"MARCA: "); break;
  case 'EN': $pdf->Cell(5,51,"BRAND: "); break;
}

$pdf->SetTextColor (0, 0, 0);
$pdf->setX(80);
$pdf->Cell(5,51,"INTERNET/ SIM: ");


if($sell->price_extra2>0):
$pdf->setX(120);
$pdf->Cell(5,51,$sell->unit_extra2);


$pdf->setX(155);
$pdf->Cell(5,51,$sell->price_extra2);

$pdf->setX(189);
$pdf->Cell(5,51,$sell->unit_extra2*$sell->price_extra2);
endif;

$pdf->Ln(5);
$pdf->SetTextColor (255,255,255);

$pdf->setX(12);
$pdf->Cell(5,51,strtoupper(utf8_decode($cars->getBrand()->name)));


$pdf->Ln(10);
$pdf->setX(12);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"MODELO: "); break;
  case 'EN': $pdf->Cell(5,51,"MODEL: "); break;
}

$pdf->SetTextColor (0, 0, 0);
$pdf->setX(80);
$pdf->Cell(5,51,"TRAILER: ");


if($sell->price_extra3>0):
$pdf->setX(120);
$pdf->Cell(5,51,$sell->unit_extra3);


$pdf->setX(155);
$pdf->Cell(5,51,$sell->price_extra3);

$pdf->setX(189);
$pdf->Cell(5,51,$sell->unit_extra3*$sell->price_extra3);
endif;

$pdf->Ln(5);
$pdf->SetTextColor (255,255,255);

$pdf->setX(12);
$pdf->Cell(5,51,strtoupper(utf8_decode($cars->name)));


$pdf->SetTextColor (0,0,0);
$pdf->Ln(10);
$pdf->setX(12);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper(utf8_decode("AÑO"))); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("YEAR")); break;
}


$pdf->setX(118);
$pdf->SetFont('Arial','B',10);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("DIA                        COSTO               TOTAL")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("DAY                        COST            TOTAL")); break;
}



$pdf->SetFont('Arial','',9);

$pdf->SetTextColor (2, 159, 205);
$pdf->Ln(5);

$pdf->setX(12);
$pdf->Cell(5,51,strtoupper($cars->year));


$pdf->SetTextColor (0,0,0);
$pdf->Ln(10);
$pdf->setX(12);
$pdf->Cell(5,51,"COLOR: ");


$pdf->setX(80);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"COSTO POR DIA: "); break;
  case 'EN': $pdf->Cell(5,51,"COST PER DAY: "); break;
}


$pdf->setX(120);
$pdf->Cell(5,51,$sell->day);



$pdf->setX(150);
$pdf->Cell(5,51,number_format($sell->price,2,".",","));


$pdf->setX(175);
$pdf->Cell(5,51,number_format($sell->price*$sell->day,2,".",","));


$pdf->SetTextColor (2, 159, 205);
$pdf->Ln(5);

$pdf->setX(12);
$pdf->Cell(5,51,strtoupper(utf8_decode($cars->getExColor()->name)));


$pdf->SetTextColor (0,0,0);
$pdf->Ln(10);
$pdf->setX(12);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"PLACA: "); break;
  case 'EN': $pdf->Cell(5,51,"PLATE: "); break;
}

$pdf->setX(80);


$pdf->SetTextColor (254,14,14);

$pdf->SetFont('Arial','B',10);


switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"BALANCE PENDIENTE: "); break;
  case 'EN': $pdf->Cell(5,51,"BALANCE DUE: "); break;
}


$pdf->setX(80);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,61,"TASA A PESO DOMINICANO: ($sell->tasa_dolar)"); break;
  case 'EN': $pdf->Cell(5,61,"RATE TO DOMINICAN PESO: ($sell->tasa_dolar)"); break;
}

$subtotal += (((float) $sell->price * (float) $sell->day) + (float) $sell->xtotal); 
$iva_subtotal = ($subtotal*($sell->iva/100));

$pdf->setX(175);
$pdf->Cell(5,51,number_format((($subtotal+$iva_subtotal)-$totpayments),2,".",","));

$pdf->Ln(6);
$pdf->setX(175);
$pdf->Cell(5,51,number_format((($subtotal+$iva_subtotal)-$totpayments)*$sell->tasa_dolar,2,".",","));



$pdf->SetFont('Arial','',8);
$pdf->SetTextColor (2, 159, 205);
$pdf->Ln(5);
$pdf->setX(12);
$pdf->Cell(5,51,strtoupper(utf8_decode($cars->plate)));


$pdf->SetY(190); // o menor si la imagen es más pequeña



$pdf->Ln(-3.5);
$pdf->SetTextColor (0,0,0);
$pdf->setX(80);
$pdf->Cell(5,51,"SUBTOTAL: ");

$pdf->setX(184);
$pdf->Cell(5,51,number_format($subtotal,2,".",","));



$pdf->Ln(3.5);
$pdf->SetTextColor (0,0,0);
$pdf->setX(80);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"SEGURO: ".$sell->getSure()->name); break;
  case 'EN': $pdf->Cell(5,51,"SURE: ".$sell->getSure()->name); break;
}

$pdf->setX(184);
$pdf->Cell(5,51,number_format($sell->sure,2,".",","));

$pdf->Ln(4);
$pdf->setX(80);
$pdf->Cell(5,51,"ITBIS (18%): ");

$pdf->setX(184);
if($sell->iva>0):
$pdf->Cell(5,51,number_format(($subtotal*($sell->iva/100)),2,".",","));
else:
$pdf->Cell(5,51,number_format(0,2,".",","));
endif;

$pdf->Ln(3.5);
$pdf->setX(80);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"OTROS COBROS: "); break;
  case 'EN': $pdf->Cell(5,51,"OTHER CHARGES: "); break;
}

$pdf->setX(184);
if($sell->plane>0):
$pdf->Cell(5,51,number_format($sell->plane,2,".",","));
else:
$pdf->Cell(5,51,number_format(0,2,".",","));
endif;

$pdf->Ln(3.5);
$pdf->setX(80);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"TARJETA (".StockData::getPrincipal()->card."%): "); break;
  case 'EN': $pdf->Cell(5,51,"CARD (".StockData::getPrincipal()->card."%): "); break;
}

$card = ($sell->total*($sell->card/100));

$pdf->setX(184);
if($sell->card>0):
$pdf->Cell(5,51,number_format($card,2,".",","));
else:
$pdf->Cell(5,51,number_format(0,2,".",","));
endif;

$pdf->Ln(3.5);
$pdf->setX(80);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"ABONADO: "); break;
  case 'EN': $pdf->Cell(5,51,"SUBSCRIBER : "); break;
}


$pdf->setX(184);
$pdf->Cell(5,51,number_format($totpayments,2,".",","));

$pdf->Ln(3.5);
$pdf->setX(80);
$pdf->Cell(5,51,"TOTAL: ");

$pdf->setX(184);
$pdf->Cell(5,51,number_format(($sell->total-$totpayments)+($sell->sure+$card+$sell->plane+($subtotal*($sell->iva/100))),2,".",","));


if (!empty($ticket_image2)){
   $src = "../CF-SYSTEMS/storage/configuration/".$ticket_image2;
    if(file_exists($src)){
        $pdf->Image($src,25,205,35);   
    }
}



///////////////////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->SetY(250); // o menor si la imagen es más pequeña

$pdf->SetTextColor (254,14,14);

$pdf->SetFont('Arial','',10);


$pdf->setX(10);


switch ($clients->language){
  case 'ES': $pdf->MultiCell(189,3.5,mb_strtoupper(utf8_decode("HE LEIDO LOS TERMINOS Y CONDICIONES EN AMBOS LADOS DE ESTE CONTRATO DE ARRENDAMIENTO Y FIRMO DE CONFORMIDAD."), 'ISO-8859-1'),0,'C'); break;
  
  case 'EN': $pdf->MultiCell(189,3.5,mb_strtoupper(utf8_decode("I HAVE READ THE TERMS AND CONDITIONS ON BOTH SIDES THIS SING LEASE AND UNDER."), 'ISO-8859-1'),0,'C'); break;
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->SetTextColor (0,0,0);


$notario = !empty(StockData::getPrincipal()->notario)
  ? StockData::getPrincipal()->notario
  : "_______________________________";
  
$no_notario = !empty(StockData::getPrincipal()->no_notario)
  ? StockData::getPrincipal()->no_notario
  : "_______________________________";
  
$witness1 = !empty(StockData::getPrincipal()->witness1)
  ? StockData::getPrincipal()->witness1
  : "_______________________________";


$witness2 = !empty(StockData::getPrincipal()->witness2)
  ? StockData::getPrincipal()->witness2
  : "_______________________________";
  
  
$no_witness1 = !empty(StockData::getPrincipal()->no_witness1)
  ? StockData::getPrincipal()->no_witness1
  : "_______________________________";


$no_witness2 = !empty(StockData::getPrincipal()->no_witness2)
  ? StockData::getPrincipal()->no_witness2
  : "_______________________________";

$pdf->AddPage();

$pdf->SetFont('Times','B',10);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->MultiCell(180,6,"ACTO NOTARIAL - CONTRATO DE ARRENDAMIENTO DE VEHICULO", 0, 'C'); break;
  case 'EN': $pdf->MultiCell(180,6,"NOTARIAL DEED - VEHICLE LEASE AGREEMENT", 0, 'C'); break;
}

$pdf->SetFont('Times','I',9);
$pdf->Ln(6);
$pdf->setX(6);

switch ($clients->language){

/* =============================== ES =============================== */
case 'ES':

$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"EN LA CIUDAD DE ".$sell->getLocation()->name.", REPÚBLICA DOMINICANA, A LOS ".date('d', strtotime($sell->start_at))." DÍAS DEL MES ".date('m', strtotime($sell->start_at))." DEL AÑO ".date('Y', strtotime($sell->start_at)).", ANTE MÍ, NOTARIO PÚBLICO DE LOS DEL NÚMERO PARA $notario, MAT. NO. $no_notario, COMPARECIERON LIBRE Y VOLUNTARIAMENTE LAS PERSONAS QUE MÁS ADELANTE SE INDICAN, HÁBILES PARA CONTRATAR, QUIENES ME DECLARARON LO SIGUIENTE:"
),'ISO-8859-1'),0,'J');

$pdf->ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"ENTRE LOS ABAJO FIRMADOS: DE UNA PARTE, LA SOCIEDAD ".$title.", ENTIDAD ORGANIZADA Y EXISTENTE DE ACUERDO CON LAS LEYES DE LA REPÚBLICA DOMINICANA, PORTADORA DE SU REGISTRO NACIONAL DE CONTRIBUYENTE (RNC) NO. ".StockData::getPrincipal()->rnc.", CON SU DOMICILIO SOCIAL ESTABLECIDO EN ".StockData::getPrincipal()->address.", QUIEN EN LO QUE SIGUE DEL PRESENTE CONTRATO SE DENOMINARÁ EL ARRENDADOR (LA PRIMERA PARTE). ----------------------------------------------------------------------------"
),'ISO-8859-1'),0,'J');

$pdf->ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"Y DE LA OTRA PARTE, EL/LA SEÑOR/A ".$clients->name.", ".$clients->nationality.", DOMINICANO/A, MAYOR DE EDAD, ".$clients->estado.", TITULAR DE LA CÉDULA DE IDENTIDAD Y ELECTORAL NÚMERO ".$clients->no.", DOMICILIADO/A Y RESIDENTE EN ".$clients->address.", QUIEN EN LO QUE SIGUE DE ESTE CONTRATO SE DENOMINARÁ EL ARRENDATARIO (LA SEGUNDA PARTE).--------"
),'ISO-8859-1'),0,'J');

$pdf->ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"CUANDO SE HAGA REFERENCIA A EL ARRENDADOR Y EL ARRENDATARIO EN CONJUNTO, SE LES DENOMINARÁ LAS PARTES."
),'ISO-8859-1'),0,'J');


/* =========================== CLAUSULAS =========================== */

$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("PRIMERO:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"EL ARRENDADOR ES PROPIETARIO DEL VEHÍCULO, MARCA ".strtoupper($cars->getBrand()->name).", MODELO ".strtoupper($cars->name).", AÑO DE FABRICACIÓN ".strtoupper($cars->year).", DE COLOR ".strtoupper($cars->getExColor()->name).", NO. DE CHASIS ".strtoupper($cars->chassis)." Y CON PLACA ".strtoupper($cars->plate)."."
)),'J');


$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("SEGUNDO:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"EL ARRENDADOR DEJA CONSTANCIA QUE EL VEHÍCULO A QUE SE REFIERE LA CLÁUSULA ANTERIOR SE ENCUENTRA EN EXCELENTES CONDICIONES, EN BUEN ESTADO DE FUNCIONAMIENTO MECÁNICO Y CONSERVACIÓN DE CARROCERÍA, PINTURA Y ACCESORIOS, SIN MAYOR DESGASTE QUE EL PRODUCIDO POR EL USO NORMAL Y ORDINARIO."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("TERCERO:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"POR EL PRESENTE CONTRATO, EL ARRENDADOR CEDE EL USO DEL BIEN DESCRITO EN LA CLÁUSULA PRIMERA EN FAVOR DE EL ARRENDATARIO, A TÍTULO DE ARRENDAMIENTO. POR SU PARTE, EL ARRENDATARIO SE OBLIGA A PAGAR A EL ARRENDADOR, EN CALIDAD DE CONTRAPRESTACIÓN POR EL USO DEL BIEN OBJETO DEL PRESENTE CONTRATO, UN MONTO QUE ASCIENDE A LA SUMA DE USD$ ".number_format($sell->total,2,".",",").", CANTIDAD QUE SERÁ PAGADA EN LA FORMA QUE LAS PARTES ACUERDEN."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("CUARTO:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"LA VIGENCIA DEL PRESENTE ARRENDAMIENTO SERÁ DESDE EL DÍA ".date('d', strtotime($sell->start_at))." DEL MES ".date('m', strtotime($sell->start_at))." DEL AÑO ".date('Y', strtotime($sell->start_at))." HASTA EL DÍA ".date('d', strtotime($sell->end_at))." DEL MES ".date('m', strtotime($sell->end_at))." DEL AÑO ".date('Y', strtotime($sell->end_at)).", O HASTA LA ENTREGA MATERIAL DEL VEHÍCULO A EL ARRENDADOR, LO QUE OCURRA ÚLTIMO."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("QUINTO:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"EL ARRENDATARIO SE OBLIGA A PAGAR PUNTUALMENTE EL MONTO DE LA RENTA, EL CUAL SE HARÁ DE LA FORMA SIGUIENTE: (ÚNICO PAGO A LA ENTREGA DEL VEHÍCULO)."
),'ISO-8859-1'),0,'J');

/* ========== NUEVA: USO + ILEGAL + NO FUMAR ========== */
$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("SEXTO:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"EL ARRENDATARIO SE OBLIGA A DESTINAR EL BIEN ARRENDADO ÚNICA Y EXCLUSIVAMENTE PARA TRANSPORTE PARTICULAR. EN CONSECUENCIA QUEDA ESTABLECIDO QUE EL ARRENDATARIO ES LA ÚNICA PERSONA AUTORIZADA PARA CONDUCIR EL VEHÍCULO, SIN PODER EMPLEARLO PARA TRANSPORTE DE CARGA, SERVICIO DE TAXI, MENSAJERÍA, REPARTO, SUBARRENDAMIENTO, REMOLQUE, COMPETENCIAS, NI PARA NINGÚN TIPO DE ACTIVIDAD COMERCIAL O LUCRATIVA."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("PÁRRAFO I:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"(PROHIBICIÓN DE USO ILEGAL): QUEDA TERMINANTEMENTE PROHIBIDO UTILIZAR EL VEHÍCULO PARA ACTOS ILÍCITOS O INMORALES, INCLUYENDO PERO NO LIMITADO A: TRANSPORTE DE PERSONAS EN SITUACIÓN MIGRATORIA IRREGULAR; TRANSPORTE, VENTA O DISTRIBUCIÓN DE DROGAS, ARMAS, CONTRABANDO, MERCANCÍA ROBADA; O PARA VENTA/TRANSPORTE PARA VENTA DE PRODUCTOS O MERCANCÍAS (INCLUYENDO AJO Y DEMÁS). CUALQUIER INDICIO O COMPROBACIÓN FACULTA AL ARRENDADOR A RESCINDIR DE INMEDIATO EL CONTRATO Y A REPORTAR A LAS AUTORIDADES, SIN RESPONSABILIDAD PARA EL ARRENDADOR."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("PÁRRAFO II:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"(NO FUMAR): QUEDA ESTRICTAMENTE PROHIBIDO FUMAR, VAPEAR, USAR HOOKAH, MARIHUANA O CUALQUIER SUSTANCIA DENTRO DEL VEHÍCULO. SI AL MOMENTO DE LA DEVOLUCIÓN SE DETECTA OLOR A HUMO, QUEMADURAS, MANCHAS O RESIDUOS, EL ARRENDATARIO PAGARÁ LA LIMPIEZA PROFUNDA, DETALLADO, OZONIZACIÓN (SI APLICA) Y/O REPARACIONES NECESARIAS, SIN PERJUICIO DE OTRAS ACCIONES."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("PÁRRAFO III:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"ASÍ TAMBIÉN, LE QUEDA PROHIBIDO A EL ARRENDATARIO REALIZAR CAMBIOS O ALTERACIONES INTERNAS O EXTERNAS AL VEHÍCULO Y SUS ACCESORIOS SIN EL CONSENTIMIENTO EXPRESO Y POR ESCRITO DE EL ARRENDADOR."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("SÉPTIMO:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"EL ARRENDATARIO ESTÁ OBLIGADO A EFECTUAR POR CUENTA Y COSTO PROPIO LAS REPARACIONES Y MANTENIMIENTOS QUE SEAN NECESARIOS PARA CONSERVAR EL VEHÍCULO EN EL MISMO ESTADO EN QUE SE LE FUE ENTREGADO, UTILIZANDO REPUESTOS ORIGINALES Y SERVICIO DE PRIMERA CATEGORÍA."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("OCTAVO:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"MIENTRAS EL VEHÍCULO SE ENCUENTRE EN POSESIÓN DE EL ARRENDATARIO, ÉSTE RESPONDERÁ EN FORMA EXCLUSIVA Y EXCLUYENTE POR LOS DAÑOS CAUSADOS A TERCERAS PERSONAS, VIAJEN O NO EN EL INTERIOR DEL VEHÍCULO; ASÍ COMO POR LOS DAÑOS CAUSADOS A PROPIEDADES PRIVADAS O PÚBLICAS."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("PÁRRAFO:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"CUALQUIER HECHO DELICTIVO COMETIDO EN EL VEHÍCULO OBJETO DEL PRESENTE CONTRATO, DURANTE EL PERIODO DE ARRENDAMIENTO, SERÁ RESPONSABILIDAD ÚNICA Y EXCLUSIVA DEL ARRENDATARIO."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("NOVENO:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"EL ARRENDATARIO NO PODRÁ CEDER A TERCEROS EL BIEN OBJETO DEL PRESENTE CONTRATO BAJO NINGÚN TÍTULO, NI SUBARRENDARLO, NI CEDER SU POSICIÓN CONTRACTUAL, SALVO QUE CUENTE CON EL CONSENTIMIENTO EXPRESO Y POR ESCRITO DE EL ARRENDADOR, EN CUYO CASO EL NUEVO ARRENDATARIO ESTARÁ OBLIGADO A FIRMAR UN DOCUMENTO SIMILAR AL PRESENTE CONTRATO."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("DÉCIMO:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"EN CASO DE ROBO O ACCIDENTE DONDE EL VEHÍCULO, OBJETO DEL PRESENTE CONTRATO SEA DECLARADO COMO PÉRDIDA TOTAL, EL ARRENDATARIO ESTARÁ OBLIGADO A PAGAR EL DEDUCIBLE QUE CORRESPONDA Y/O EL VALOR DEL VEHÍCULO SEGÚN APLIQUE, A FIN DE QUE EL ARRENDADOR PUEDA GESTIONAR UN VEHÍCULO SIMILAR Y CUBRIR LOS DAÑOS Y PERJUICIOS QUE TALES CIRCUNSTANCIAS PUEDAN CAUSARLE."
),'ISO-8859-1'),0,'J');

/* ========== NUEVA: EMBARGO / CHOQUE / EVENTUALIDADES ========== */
$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("DÉCIMO PRIMERO:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"(RESPONSABILIDAD POR CHOQUE, EVENTUALIDADES Y EMBARGO): EN CASO DE CHOQUE, COLISIÓN, VUELCO, INCENDIO, INUNDACIÓN, DAÑO MECÁNICO POR MAL USO, NEGLIGENCIA, USO INDEBIDO, ABANDONO, PÉRDIDA, ROBO PARCIAL O TOTAL, O CUALQUIER OTRA EVENTUALIDAD DURANTE LA VIGENCIA DEL ARRENDAMIENTO, EL ARRENDATARIO ASUME LA RESPONSABILIDAD TOTAL DE LOS DAÑOS, COSTOS Y PERJUICIOS OCASIONADOS, INCLUYENDO PERO NO LIMITADO A: PIEZAS, MANO DE OBRA, GRÚA, ALMACENAJE, PERITAJE, GASTOS ADMINISTRATIVOS, DEDUCIBLES, Y LUCRO CESANTE POR DÍAS SIN OPERACIÓN DEL VEHÍCULO. SI EL VEHÍCULO ES DECLARADO PÉRDIDA TOTAL O IRREPARABLE, EL ARRENDATARIO PAGARÁ EL VALOR COMERCIAL DEL VEHÍCULO SEGÚN TASACIÓN, PERITAJE, VALUACIÓN O COTIZACIÓN. PARA GARANTIZAR EL PAGO, EL ARRENDATARIO AUTORIZA EXPRESAMENTE AL ARRENDADOR A INICIAR ACCIONES DE COBRO Y A SOLICITAR MEDIDAS CONSERVATORIAS O EJECUTIVAS, INCLUYENDO EMBARGO RETENTIVO, CONSERVATORIO O EJECUTIVO SOBRE BIENES, CUENTAS, INGRESOS Y DERECHOS PRESENTES O FUTUROS DEL ARRENDATARIO, ASÍ COMO EL COBRO DE COSTAS Y HONORARIOS PROFESIONALES."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("DÉCIMO SEGUNDO:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"TODO LITIGIO O CONTROVERSIA DERIVADOS O RELACIONADOS CON ESTE CONTRATO SERÁ DE LA JURISDICCIÓN EXCLUSIVA DE LOS TRIBUNALES CORRESPONDIENTES A ".StockData::getPrincipal()->address.", CUYAS DECISIONES SERÁN CUMPLIDAS DE FORMA OBLIGATORIA POR LAS PARTES."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);

// TÍTULO EN NEGRITA
$pdf->SetFont('Times','B',9);
$pdf->Cell(0,4,utf8_decode("DÉCIMO TERCERO:"),0,1,'L');

// TEXTO NORMAL (alineado correctamente)
$pdf->SetFont('Times','I',9);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"LA FALTA DE PAGO DE LOS MONTOS ACORDADOS EN EL PRESENTE CONTRATO, ASÍ COMO LA VIOLACIÓN DE CUALQUIERA DE LAS OBLIGACIONES CONTENIDAS EN ESTE CONTRATO, LO HARÁ RESCINDIBLE DE PLENO DERECHO, A JUICIO DE EL ARRENDADOR, SIN NECESIDAD DE INTERPELACIÓN NI ACTO PREVIO."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"LEÍDO POR MÍ, NOTARIO, A LOS COMPARECIENTES, QUIENES MANIFESTARON ENTERA CONFORMIDAD, FIRMÁNDOLO EN DOS (2) ORIGINALES DE UN MISMO TENOR Y EFECTO, EN LA CIUDAD DE ".$user->address.", HOY ".date('d', strtotime($sell->start_at))." DEL MES ".date('m', strtotime($sell->start_at))." DEL AÑO ".date('Y', strtotime($sell->start_at))."."
),'ISO-8859-1'),0,'J');

$pdf->Ln(4);
$pdf->setX(6);
$pdf->SetFont('Times','B',8);
$pdf->MultiCell(190,4,utf8_decode("FIRMAS:"),0,'L');

$pdf->SetFont('Times','I',9);
$pdf->Ln(1);
$pdf->setX(6);


$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"POR EL ARRENDADOR (PRIMERA PARTE): ".$user->name." ".$user->lastname."   CÉDULA/RNC: ".StockData::getPrincipal()->rnc."
NOMBRE/RAZÓN SOCIAL: ".$title." 

POR EL ARRENDATARIO (SEGUNDA PARTE): ".$clients->name."  CÉDULA: ".$clients->no."

FIRMA DEL ARRENDATARIO: _______________________

TESTIGO 1: ".$witness1."  CÉDULA: ".$no_witness1."
TESTIGO 2: ".$witness2."  CÉDULA: ".$no_witness2."

NOTARIO PÚBLICO: ".$notario."  MAT. NO.: ".$no_notario."
SELLO:"
),'ISO-8859-1'),0,'L');

break;



/* =============================== EN =============================== */
case 'EN':

$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"IN THE CITY OF ".$sell->getLocation()->name.", DOMINICAN REPUBLIC, ON THE ".date('d', strtotime($sell->start_at))." DAY OF MONTH ".date('m', strtotime($sell->start_at))." OF YEAR ".date('Y', strtotime($sell->start_at)).", BEFORE ME, NOTARY PUBLIC OF THE NUMBER FOR ".$notario.", REG. NO. ".$no_notario.", THE FOLLOWING PERSONS APPEARED FREELY AND VOLUNTARILY, LEGALLY CAPABLE, WHO DECLARED AS FOLLOWS:"
),'ISO-8859-1'),0,'J');

$pdf->ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"BETWEEN THE UNDERSIGNED: ON THE ONE HAND, THE COMPANY ".$title.", AN ENTITY ORGANIZED AND EXISTING UNDER THE LAWS OF THE DOMINICAN REPUBLIC, HOLDER OF ITS NATIONAL TAXPAYER REGISTRY (RNC) NO. ".StockData::getPrincipal()->rnc.", WITH ITS REGISTERED OFFICE ESTABLISHED AT ".StockData::getPrincipal()->address.", HEREINAFTER REFERRED TO AS THE LESSOR (THE FIRST PARTY). ----------------------------------------------------------------------------"
),'ISO-8859-1'),0,'J');

$pdf->ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"AND ON THE OTHER HAND, MR./MS. ".$clients->name.", ".$clients->nationality.", DOMINICAN, OF LEGAL AGE, ".$clients->estado.", HOLDER OF THE IDENTITY AND ELECTORAL CARD NUMBER ".$clients->no.", DOMICILED AND RESIDING AT ".$clients->address.", HEREINAFTER REFERRED TO AS THE LESSEE (THE SECOND PARTY).--------"
),'ISO-8859-1'),0,'J');

$pdf->ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"WHEN JOINTLY REFERRED TO, THEY SHALL BE CALLED THE PARTIES."
),'ISO-8859-1'),0,'J');

/* =========================== CLAUSES =========================== */

$pdf->ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"FIRST: THE LESSOR IS THE OWNER OF THE VEHICLE, BRAND ".strtoupper($cars->getBrand()->name).", MODEL ".strtoupper($cars->name).", YEAR OF MANUFACTURE ".strtoupper($cars->year).", COLOR ".strtoupper($cars->getExColor()->name).", CHASSIS NO. ".strtoupper($cars->chassis).", AND LICENSE PLATE ".strtoupper($cars->plate)."."
),'ISO-8859-1'),0,'J');

$pdf->ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"SECOND: THE LESSOR CERTIFIES THAT THE VEHICLE REFERRED TO IN THE PREVIOUS CLAUSE IS IN EXCELLENT CONDITION, IN GOOD MECHANICAL WORKING ORDER, AND WELL PRESERVED IN BODYWORK, PAINT, AND ACCESSORIES, WITH NO GREATER WEAR THAN THAT PRODUCED BY NORMAL AND ORDINARY USE."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"THIRD: BY THIS AGREEMENT, THE LESSOR GRANTS THE USE OF THE VEHICLE DESCRIBED ABOVE IN FAVOR OF THE LESSEE, BY WAY OF LEASE. THE LESSEE UNDERTAKES TO PAY THE LESSOR, AS CONSIDERATION, THE AMOUNT OF USD$ ".number_format($sell->total,2,".",",").", WHICH SHALL BE PAID AS AGREED BY THE PARTIES."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"FOURTH: THE TERM OF THIS LEASE SHALL BE FROM ".date('d', strtotime($sell->start_at))."/".date('m', strtotime($sell->start_at))."/".date('Y', strtotime($sell->start_at))." UNTIL ".date('d', strtotime($sell->end_at))."/".date('m', strtotime($sell->end_at))."/".date('Y', strtotime($sell->end_at)).", OR UNTIL THE VEHICLE IS PHYSICALLY RETURNED TO THE LESSOR, WHICHEVER OCCURS LAST."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"FIFTH: THE LESSEE SHALL PAY THE RENTAL AMOUNT ON TIME, AS FOLLOWS: (SINGLE PAYMENT UPON DELIVERY OF THE VEHICLE)."
),'ISO-8859-1'),0,'J');

/* ===== NEW: USE + ILLEGAL + NO SMOKING ===== */
$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"SIXTH: THE LESSEE SHALL USE THE VEHICLE SOLELY AND EXCLUSIVELY FOR PRIVATE TRANSPORTATION. THE LESSEE SHALL BE THE ONLY PERSON AUTHORIZED TO DRIVE THE VEHICLE. THE VEHICLE SHALL NOT BE USED FOR CARGO TRANSPORT, TAXI SERVICE, DELIVERY/MESSENGER SERVICES, TRANSPORT FOR SALES, ANY COMMERCIAL/PROFIT ACTIVITY, SUBLEASING, TOWING, OR RACING."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"PARAGRAPH I (ILLEGAL USE PROHIBITED): IT IS STRICTLY FORBIDDEN TO USE THE VEHICLE FOR ANY ILLEGAL OR IMMORAL PURPOSES, INCLUDING BUT NOT LIMITED TO: TRANSPORTING PERSONS WITH IRREGULAR IMMIGRATION STATUS; TRANSPORTING, SELLING OR DISTRIBUTING DRUGS, WEAPONS, CONTRABAND, STOLEN GOODS; OR FOR THE SALE/TRANSPORT FOR SALE OF PRODUCTS OR GOODS (INCLUDING GARLIC AND OTHERS). ANY INDICATION OR PROOF AUTHORIZES THE LESSOR TO IMMEDIATELY TERMINATE THIS CONTRACT AND REPORT TO THE AUTHORITIES, WITHOUT LIABILITY FOR THE LESSOR."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"PARAGRAPH II (NO SMOKING): SMOKING, VAPING, HOOKAH, MARIJUANA OR ANY SUBSTANCE USE INSIDE THE VEHICLE IS STRICTLY PROHIBITED. IF UPON RETURN THERE IS SMOKE ODOR, BURNS, STAINS OR RESIDUE, THE LESSEE SHALL PAY FOR DEEP CLEANING, DETAILING, OZONE TREATMENT (IF APPLICABLE) AND/OR NECESSARY REPAIRS, WITHOUT PREJUDICE TO OTHER ACTIONS."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"PARAGRAPH III: THE LESSEE SHALL NOT MAKE ANY INTERNAL OR EXTERNAL CHANGES OR ALTERATIONS TO THE VEHICLE OR ITS ACCESSORIES WITHOUT THE EXPRESS WRITTEN CONSENT OF THE LESSOR."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"SEVENTH: THE LESSEE SHALL, AT THEIR OWN EXPENSE, CARRY OUT THE REPAIRS AND MAINTENANCE NECESSARY TO KEEP THE VEHICLE IN THE SAME CONDITION IN WHICH IT WAS DELIVERED, USING ORIGINAL SPARE PARTS AND FIRST-CLASS SERVICE."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"EIGHTH: WHILE THE VEHICLE IS IN THE POSSESSION OF THE LESSEE, THE LESSEE SHALL BE SOLELY AND EXCLUSIVELY RESPONSIBLE FOR ANY DAMAGES CAUSED TO THIRD PARTIES, WHETHER OR NOT THEY ARE TRAVELING INSIDE THE VEHICLE, AS WELL AS FOR DAMAGES CAUSED TO PRIVATE OR PUBLIC PROPERTY."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"PARAGRAPH: ANY CRIMINAL ACT COMMITTED IN THE VEHICLE SUBJECT TO THIS AGREEMENT DURING THE LEASE PERIOD SHALL BE THE SOLE AND EXCLUSIVE RESPONSIBILITY OF THE LESSEE."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"NINTH: THE LESSEE MAY NOT TRANSFER THE VEHICLE SUBJECT OF THIS CONTRACT TO THIRD PARTIES UNDER ANY TITLE, NOR SUBLEASE IT, NOR ASSIGN THEIR CONTRACTUAL POSITION, UNLESS WITH THE EXPRESS WRITTEN CONSENT OF THE LESSOR, IN WHICH CASE THE NEW LESSEE SHALL SIGN A DOCUMENT SIMILAR TO THIS AGREEMENT."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"TENTH: IN CASE OF THEFT OR ACCIDENT WHERE THE VEHICLE SUBJECT TO THIS CONTRACT IS DECLARED A TOTAL LOSS, THE LESSEE SHALL PAY THE APPLICABLE DEDUCTIBLE AND/OR THE VEHICLE VALUE AS APPLICABLE, SO THAT THE LESSOR MAY OBTAIN A SIMILAR VEHICLE AND COVER DAMAGES AND LOSSES."
),'ISO-8859-1'),0,'J');

/* ===== NEW: SEIZURE / ACCIDENTS / CONTINGENCIES ===== */
$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"ELEVENTH (LIABILITY FOR ACCIDENTS, CONTINGENCIES AND SEIZURE): IN CASE OF ACCIDENT, COLLISION, ROLLOVER, FIRE, FLOODING, MECHANICAL DAMAGE DUE TO MISUSE, NEGLIGENCE, IMPROPER USE, ABANDONMENT, LOSS, PARTIAL OR TOTAL THEFT, OR ANY OTHER CONTINGENCY DURING THE TERM, SEEING THAT THE VEHICLE IS UNDER THE LESSEE'S POSSESSION, THE LESSEE ASSUMES FULL RESPONSIBILITY FOR ALL DAMAGES, COSTS AND LOSSES, INCLUDING BUT NOT LIMITED TO: PARTS, LABOR, TOWING, STORAGE, APPRAISALS, ADMINISTRATIVE FEES, DEDUCTIBLES, AND LOSS OF PROFITS DUE TO VEHICLE DOWNTIME. IF THE VEHICLE IS DECLARED A TOTAL LOSS OR IRREPARABLE, THE LESSEE SHALL PAY THE VEHICLE'S COMMERCIAL VALUE ACCORDING TO APPRAISAL/VALUATION OR QUOTATION. TO GUARANTEE PAYMENT, THE LESSEE EXPRESSLY AUTHORIZES THE LESSOR TO INITIATE COLLECTION ACTIONS AND SEEK CONSERVATORY OR EXECUTORY MEASURES, INCLUDING RETENTIVE/CONSERVATORY/EXECUTORY SEIZURE (EMBARGO) AGAINST THE LESSEE'S ASSETS, ACCOUNTS, INCOME AND PRESENT OR FUTURE RIGHTS, INCLUDING LEGAL COSTS AND ATTORNEY FEES."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"TWELFTH: ANY DISPUTE OR CONTROVERSY ARISING OUT OF OR RELATED TO THIS CONTRACT SHALL BE UNDER THE EXCLUSIVE JURISDICTION OF THE COURTS CORRESPONDING TO ".StockData::getPrincipal()->address.", WHOSE DECISIONS SHALL BE BINDING ON THE PARTIES."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"THIRTEENTH: FAILURE TO PAY THE AMOUNTS AGREED HEREIN, AS WELL AS THE VIOLATION OF ANY OBLIGATION CONTAINED IN THIS CONTRACT, SHALL MAKE THIS CONTRACT TERMINABLE BY OPERATION OF LAW, AT THE SOLE DISCRETION OF THE LESSOR, WITHOUT PRIOR NOTICE."
),'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"READ BY ME, THE NOTARY, TO THE APPEARERS, WHO STATED THEIR FULL CONSENT, SIGNING TWO (2) ORIGINALS OF THE SAME TENOR AND EFFECT, IN THE CITY OF ".$user->address.", TODAY ".date('d', strtotime($sell->start_at))." OF MONTH ".date('m', strtotime($sell->start_at))." OF YEAR ".date('Y', strtotime($sell->start_at))."."
),'ISO-8859-1'),0,'J');

$pdf->Ln(4);
$pdf->setX(6);
$pdf->SetFont('Times','B',8);
$pdf->MultiCell(190,4,utf8_decode("SIGNATURES:"),0,'L');

$pdf->SetFont('Times','I',9);
$pdf->Ln(1);
$pdf->setX(6);
$pdf->MultiCell(190,4,mb_strtoupper(utf8_decode(
"FOR THE LESSOR (FIRST PARTY): ".$user->name." ".$user->lastname."   ID/RNC: ".StockData::getPrincipal()->rnc."
NAME/COMPANY: ".$title."

FOR THE LESSEE (SECOND PARTY): ".$clients->name."  ID: ".$clients->no."

TENANT'S SIGNATURE: _______________________

WITNESS 1: ".$witness1."  ID: ".$no_witness1."
WITNESS 2: ".$witness2."  ID: ".$no_witness2."

NOTARY PUBLIC: ".$notario."  REG. NO.: ".$no_notario."
SEAL:"
),'ISO-8859-1'),0,'L');

break;
}


$pdf->SetFont('Times','B',9);

$posY = $pdf->GetY() - 35;

// Firma del arrendatario
if ($hasDelivery && !empty($delivery->firma)):
    $src = '../' . $delivery->firma;
    if (file_exists($src)) {
        $x2 = ($clients->language == 'ES') ? 50 : 50;
        $pdf->Image($src, $x2, $posY, 30);
    }
endif;


$pdf->AddPage(); // <-- Inicia segunda hoja tamaño carta

$pdf->SetFont('Arial','B',12);    //Letra Arial, negrita (Bold)

$pdf->SetTextColor (0,0,0);


$pdf->Ln(-20);
$pdf->SetFont('Arial','B',12);    // Letra Arial, negrita
$pdf->SetTextColor (0,0,0);
$pdf->setX(70);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper(utf8_decode("DAÑOS E INSPECCIONES: "))); break;
  case 'EN': $pdf->Cell(5,51,strtoupper(utf8_decode("DAMAGES AND INSPECTIONS: "))); break;
}


$pdf->SetFont('Arial','',9);    //Letra Arial, negrita (Bold)



$pdf->ln(90);

$pdf->setX(5);
switch ($clients->language){
   case 'ES': $pdf->Cell(5,-105,utf8_decode('DAÑO ENTREGADO AL CLIENTE:')); break;
  case 'EN': $pdf->Cell(5,-105,utf8_decode('DAMAGE DELIVERED TO THE CUSTOMER:')); break;
}




$pdf->setX(120);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,-105,utf8_decode('DAÑO RECIBIDO DEL CLIENTE:')); break;
  case 'EN': $pdf->Cell(5,-105,utf8_decode('DAMAGE RECEIVED FROMCUSTOMER:')); break;
}



$pdf->setX(5);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,40,utf8_decode('REVISION ENTREGADO AL CLIENTE:')); break;
  case 'EN': $pdf->Cell(5,40,utf8_decode('CUSTOMER DELIVERED REVIEW:')); break;
}



$pdf->setX(120);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,40,utf8_decode('REVISION RECIBIDO DEL CLIENTE:')); break;
  case 'EN': $pdf->Cell(5,40,utf8_decode('REVIEW RECEIVED FROM CUSTOMER:')); break;
}

    
    
$pdf->SetFont('Times','',8);

$pdf->ln(20);

if($delivery->cat==1):
$pdf->Image('check.png', 5, 103.5, 5, 5);
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('GATO')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('CAT')); break;
}

if($receiver->cat==1):
$pdf->Image('check.png', 120, 103.5, 5, 5); 
endif;

if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('GATO')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('CAT')); break;
}
endif;

if($delivery->radio==1):
$pdf->Image('check.png', 45, 103.5, 5, 5);
endif;

$pdf->setX(50);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('RADIO')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('RADIO')); break;
}

if($receiver->radio==1):
$pdf->Image('check.png', 165, 103.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(170);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('RADIO')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('RADIO')); break;
}
endif;

$pdf->ln(6);
if($delivery->replacement==1):
$pdf->Image('check.png', 5, 109.5, 5, 5);
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('REPUESTO')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('REPLACEMENT')); break;
}


if($receiver->replacement==1):
$pdf->Image('check.png', 120, 109.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('REPUESTO')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('REPLACEMENT')); break;
}
endif;

if($delivery->antenna==1):
$pdf->Image('check.png', 45, 109.5, 5, 5);
endif;

$pdf->setX(50);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('ANTENA')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('ANTENNA')); break;
}

if($receiver->antenna==1):
$pdf->Image('check.png', 165, 109.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(170);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('ANTENA')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('ANTENNA')); break;
}
endif;

$pdf->ln(6);

if($delivery->keyring==1):
$pdf->Image('check.png', 5, 115.5, 5, 5);    
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('LLAVERO')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('KEY RING')); break;
}


if($receiver->keyring==1):
$pdf->Image('check.png', 120, 115.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('LLAVERO')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('KEY RING')); break;
}
endif;

if($delivery->carpets==1):
$pdf->Image('check.png', 45, 115.5, 5, 5); 
endif;

$pdf->setX(50);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('ALFOMBRAS')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('CARPETS')); break;
}

if($receiver->carpets==1):
$pdf->Image('check.png', 165, 115.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(170);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('ALFOMBRAS')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('CARPETS')); break;
}
endif;

$pdf->ln(6);
if($delivery->belts==1):
$pdf->Image('check.png', 5, 121.5, 5, 5); 
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('CINTURONES')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('BELTS')); break;
}


if($receiver->belts==1):
$pdf->Image('check.png', 120, 121.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('CINTURONES')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('BELTS')); break;
}
endif;

if($delivery->roof_lining==1):
$pdf->Image('check.png', 45, 121.5, 5, 5); 
endif;

$pdf->setX(50);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('FORRO TECHO')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('ROOF LINING')); break;
}


if($receiver->roof_lining==1):
$pdf->Image('check.png', 165, 121.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(170);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('FORRO TECHO')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('ROOF LINING')); break;
}
endif;

$pdf->ln(6);
if($delivery->mirrors==1):
$pdf->Image('check.png', 5, 127.5, 5, 5); 
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('ESPEJOS')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('MIRRORS')); break;
}


if($receiver->mirrors==1):
$pdf->Image('check.png', 120, 127.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('ESPEJOS')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('MIRRORS')); break;
}
endif;

if($delivery->board==1):
$pdf->Image('check.png', 45, 127.5, 5, 5); 
endif;

$pdf->setX(50);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('TABLERO')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('BOARD')); break;
}

if($receiver->board==1):
$pdf->Image('check.png', 165, 127.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(170);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('TABLERO')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('BOARD')); break;
}
endif;

$pdf->ln(6);
if($delivery->document==1):
$pdf->Image('check.png', 5, 133.5, 5, 5); 
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('DOCUMENTOS')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('DOCUMENTS')); break;
}


if($receiver->document==1):
$pdf->Image('check.png', 120, 133.5, 5, 5);
endif;

if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('DOCUMENTOS')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('DOCUMENTS')); break;
}
endif;

$pdf->ln(6);
if($delivery->watches==1):
$pdf->Image('check.png', 5, 139.5, 5, 5);
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('RELOJES')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('WATCHES')); break;
}


if($receiver->watches==1):
$pdf->Image('check.png', 120, 139.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('RELOJES')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('WATCHES')); break;
}
endif;

$pdf->ln(6);
if($delivery->rearview==1):
$pdf->Image('check.png', 5, 145.5, 5, 5);
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('RETREVISOR')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('REVIEWER')); break;
}


if($receiver->rearview==1):
$pdf->Image('check.png', 120, 145.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('RETREVISOR')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('REVIEWER')); break;
}
endif;

$pdf->ln(6);
if($delivery->lighter==1):
$pdf->Image('check.png', 5, 151.5, 5, 5);
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('ENCENDEDOR')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('LIGHTER')); break;
}

if($receiver->lighter==1):
$pdf->Image('check.png', 120, 151.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('ENCENDEDOR')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('LIGHTER')); break;
}
endif;

$pdf->ln(6);
if($delivery->crystals==1):
$pdf->Image('check.png', 5, 157.5, 5, 5);
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('CRISTALES')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('CRYSTALS')); break;
}


if($receiver->crystals==1):
$pdf->Image('check.png', 120, 157.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('CRISTALES')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('CRYSTALS')); break;
}
endif;

$pdf->ln(6);
if($delivery->cd==1):
$pdf->Image('check.png', 5, 163.5, 5, 5);
endif;
$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('PORTA CD')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('CD HOLDER')); break;
}


if($receiver->cd==1):
$pdf->Image('check.png', 120, 163.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('PORTA CD')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('CD HOLDER')); break;
}
endif;

$pdf->ln(6);
if($delivery->bumper==1):
$pdf->Image('check.png', 5, 169.5, 5, 5); 
endif;
$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('TAPA COV. BUMPER')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('VOC COVER BUMPER')); break;
}


if($receiver->bumper==1):
$pdf->Image('check.png', 120, 169.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('TAPA COV. BUMPER')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('VOC COVER BUMPER')); break;
}
endif;

$pdf->ln(6);
if($delivery->equalizer==1):
$pdf->Image('check.png', 5, 175.5, 5, 5); 
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('ECUALIZADOR')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('EQUALIZER')); break;
}

if($receiver->equalizer==1):
$pdf->Image('check.png', 120, 175.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('ECUALIZADOR')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('EQUALIZER')); break;
}
endif;

$pdf->ln(6);
if($delivery->cup_holder==1):
$pdf->Image('check.png', 5, 181.5, 5, 5); 
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('PORTA VASOS')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('CUP HOLDER')); break;
}

if($receiver->cup_holder==1):
$pdf->Image('check.png', 120, 181.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('PORTA VASOS')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('CUP HOLDER')); break;
}
endif;

$pdf->ln(6);
if($delivery->plate==1):
$pdf->Image('check.png', 5, 187.5, 5, 5);
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('PLACA')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('PLATE')); break;
}

if($receiver->plate==1):
$pdf->Image('check.png', 120, 187.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('PLACA')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('PLATE')); break;
}
endif;

$pdf->ln(6);
if($delivery->seats==1):
$pdf->Image('check.png', 5, 193.5, 5, 5);
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('ASIENTOS')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('SEATING')); break;
}


if($receiver->seats==1):
$pdf->Image('check.png', 120, 193.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('ASIENTOS')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('SEATING')); break;
}
endif;

$pdf->ln(6);
if($delivery->logo==1):
$pdf->Image('check.png', 5, 199.5, 5, 5);
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('LOGOS')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('LOGOS')); break;
}

if($receiver->logo==1):
$pdf->Image('check.png', 120, 199.5, 5, 5);
endif;


if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('LOGOS')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('LOGOS')); break;
}
endif;

$pdf->ln(6);
if($delivery->batery==1):
$pdf->Image('check.png', 5, 205.5, 5, 5);
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('BATERIA')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('BATTERY')); break;
}

if($receiver->batery==1):
$pdf->Image('check.png', 120, 205.5, 5, 5);
endif;

if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('BATERIA')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('BATTERY')); break;
}
endif;

$pdf->ln(6);
if($delivery->top==1):
$pdf->Image('check.png', 5, 211.5, 5, 5);
endif;

$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('TAPA COMBUSTIBLE')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('FUEL CAP')); break;
}

if($receiver->top==1 && $receiver>0):
$pdf->Image('check.png', 120, 211.5, 5, 5);
endif;

if($hasReceiver):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('TAPA COMBUSTIBLE')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('FUEL CAP')); break;
}
endif;




$posY = $pdf->GetY() - 15;


if (!empty($user_delivery->firma)) {
$src = '../' . $user_delivery->firma;
if (file_exists($src)) {
$x2 = ($clients->language == 'ES') ? 45 : 45;
$pdf->Image($src, $x2, $posY, 30);
}
}


$pdf->SetFont('Times','B',8);
 
 
if($hasReceiver):
    
if (!empty($user_receiver->firma)) {
$src = '../' . $user_receiver->firma;
if (file_exists($src)) {
$x2 = ($clients->language == 'ES') ? 160 : 160;
$pdf->Image($src, $x2, $posY, 30);
}
}    
endif;

$pdf->setX(45);
switch ($clients->language){
case 'ES': $pdf->Cell(5,5,'ENTREGADOR/A:'); break;
case 'EN': $pdf->Cell(5,5,'DELIVERY:'); break;
}

$pdf->setX(45);
$pdf->Cell(5,-10,'_________________________');

$pdf->setX(45);
$pdf->Cell(5,0,strtoupper(utf8_decode($user_delivery->name." ".$user_delivery->lastname)));


if($hasReceiver):

$pdf->setX(160);
switch ($clients->language){
case 'ES': $pdf->Cell(5,5,'RECIBIDOR/A:'); break;
case 'EN': $pdf->Cell(5,5,'RECEIVER:'); break;
}    
    
$pdf->setX(160);
$pdf->Cell(5,-10,'_________________________');

$pdf->setX(160);
$pdf->Cell(5,0,strtoupper(utf8_decode($user_receiver->name." ".$user_receiver->lastname)));
endif;

$pdf->setX(40);

if($sell->fuel=="R"):
       
      $src = "../CF-SYSTEMS/storage/configuration/FR.png";
    if(file_exists($src)){
        $pdf->Image($src,2,227,50);        
    }
    
    endif;

  if($sell->fuel=="1/4" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/F14.png";
    if(file_exists($src)){
            $pdf->Image($src,2,227,50);       
    }
    
    endif;
    
    if($sell->fuel=="3/4"):
       
      $src = "../CF-SYSTEMS/storage/configuration/F34.png";
    if(file_exists($src)){
           $pdf->Image($src,2,227,50);          
    }
    
    endif;
    
    if($sell->fuel=="1/2" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FM.png";
    if(file_exists($src)){
          $pdf->Image($src,2,227,50);             
    }
    
    endif;
    
    if($sell->fuel=="F" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FF.png";
    if(file_exists($src)){
          $pdf->Image($src,2,227,50);             
    }
    
   endif;


if($hasReceiver):

if($receiver->fuel=="R"):
       
      $src = "../CF-SYSTEMS/storage/configuration/FR.png";
    if(file_exists($src)){
        $pdf->Image($src,120,227,50);      
    }
    
    endif;

  if($receiver->fuel=="1/4" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/F14.png";
    if(file_exists($src)){
         $pdf->Image($src,120,227,50); 
    }
    
    endif;
    
    if($receiver->fuel=="3/4"):
       
      $src = "../CF-SYSTEMS/storage/configuration/F34.png";
    if(file_exists($src)){
          $pdf->Image($src,120,227,50);    
    }
    
    endif;
    
    if($receiver->fuel=="1/2" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FM.png";
    if(file_exists($src)){
        $pdf->Image($src,120,227,50);         
    }
    
    endif;
    
    if($receiver->fuel=="F" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FF.png";
    if(file_exists($src)){
         $pdf->Image($src,120,227,50);
    }
    
   endif;



/////////////////////////////////// AQUI VA LA MARCA DE DANOS DEL CLIENTE AL SALIR //////////////////////////////////////////////////////

$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT danger from delivery where random=0 and method=1 and booking_id=".$_GET["id"];
$query = $con->query($sql);

while ($row = $query->fetch_array()) {
    if (!empty($row['danger'])) {
        $imagenes = explode("|", $row['danger']); // Convertir la cadena en array

        $x = 100; // Posición inicial en X
        $y = 32; // Posición fija en Y
        $ancho = 35.3; // Ancho de cada imagen
        $alto = 35.3;  // Alto de cada imagen
        $espacio = 5; // Espacio entre imágenes
        $margen_derecho = 20; // Espacio para evitar que se corte al final de la hoja
        $max_ancho = 450 - $margen_derecho; // Máximo ancho antes de bajar de línea

        $contador = 0; // Contador de imágenes en la fila

        foreach ($imagenes as $img) {
            $imgPath = '../danger/' . trim($img);
            if (!empty($img) && file_exists($imgPath)) {
                // Si la siguiente imagen se saldría del ancho permitido, pasa a la siguiente columna
                if ($contador == 2) {
                    $contador = 0; // Reinicia el contador
                    $x = 6; // Reinicia la posición de X a la primera columna
                    $y += 32 ; // Baja a la siguiente fila
                }

                $pdf->Image($imgPath, $x, $y, $ancho); // Coloca la imagen
                $x += $ancho + $espacio; // Mueve la posición X para la siguiente imagen

                $contador++; // Incrementa el contador de imágenes en la fila
            }
        }
    }
}


$pdf->ln(110);



else:
/////////////////////////////////// AQUI VA LA MARCA DE DANOS DEL CLIENTE AL SALIR //////////////////////////////////////////////////////

$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT danger from delivery where random=0 and method=2 and booking_id=".$_GET["id"];
$query = $con->query($sql);

while ($row = $query->fetch_array()) {
    if (!empty($row['danger'])) {
        $imagenes = explode("|", $row['danger']); // Convertir la cadena en array

        $x = 6; // Posición inicial en X
        $y = 32; // Posición fija en Y
        $ancho = 35.3; // Ancho de cada imagen
        $alto = 35.3;  // Alto de cada imagen
        $espacio = 5; // Espacio entre imágenes
        $margen_derecho = 20; // Espacio para evitar que se corte al final de la hoja
        $max_ancho = 450 - $margen_derecho; // Máximo ancho antes de bajar de línea

        $contador = 0; // Contador de imágenes en la fila

        foreach ($imagenes as $img) {
            $imgPath = '../danger/' . trim($img);
            if (!empty($img) && file_exists($imgPath)) {
                // Si la siguiente imagen se saldría del ancho permitido, pasa a la siguiente columna
                if ($contador == 5) {
                    $contador = 0; // Reinicia el contador
                    $x = 6; // Reinicia la posición de X a la primera columna
                    $y += 32 ; // Baja a la siguiente fila
                }

                $pdf->Image($imgPath, $x, $y, $ancho); // Coloca la imagen
                $x += $ancho + $espacio; // Mueve la posición X para la siguiente imagen

                $contador++; // Incrementa el contador de imágenes en la fila
            }
        }
    }
}


endif;


$pdf->Output(); 


?>
