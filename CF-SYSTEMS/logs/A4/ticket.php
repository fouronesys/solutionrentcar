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
$iva_val =  StockData::getPrincipal()->imp_val;
$divisa =  StockData::getPrincipal()->divisa;
$stock = StockData::getPrincipal();

$sell = BookingData::getById($_GET["id"]);
$cars = CarsData::getById($sell->car_id);
$clients = PersonData::getById($sell->person_id);
$clients2 = PersonData::getById($sell->person2_id);
$gerente = UserData::getById(38);
$delivery = DeliveryData::getBySell(0,2,$_GET["id"]);
$delivery2 = DeliveryData::getBySell(1,2,$_GET["id"]);

if($delivery>0):
$user_delivery = $delivery->getUser();
endif;

$receiver = DeliveryData::getBySell(0,1,$_GET["id"]);
$receiver2 = DeliveryData::getBySell(1,1,$_GET["id"]);
if($receiver>0):
$user_receiver = $receiver->getUser();
endif;

$cars = CarsData::getById($sell->car_id);
$cars2 = CarsData::getById($sell->car2_id);
$user = $sell->getUser();
$color =  $receiptIdAndName = explode(",", StockData::getPrincipal()->color);

$totpayments = 0;
$payments = PaymentData::getByPayment($_GET["id"]);
$totpayments = $payments[0]->t!=null?$payments[0]->t:0;



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

$subtotal += (($sell->price*$sell->day)+$sell->xtotal); 


$pdf->setX(175);
$pdf->Cell(5,51,number_format($subtotal-$totpayments,2,".",","));

$pdf->Ln(6);
$pdf->setX(175);
$pdf->Cell(5,51,number_format(($subtotal-$totpayments)*$sell->tasa_dolar,2,".",","));



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
$pdf->Cell(5,51,number_format(($sell->total-$totpayments)+($sell->sure+$card+$sell->plane),2,".",","));


if($ticket_image2<>""){
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


$pdf->AddPage();

$pdf->SetFont('Times','B',10);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->MultiCell(180,6,"CONTRATO DE ARRENDAMIENTO DE VEHICULO", 0, 'C'); break;
  
  case 'EN': $pdf->MultiCell(180,6,"VEHICLE LEASE AGREEMENT", 0, 'C'); break;
}

$pdf->SetFont('Times','I',7);
$pdf->Ln(6);
$pdf->setX(6);

switch ($clients->language){
  case 'ES': $pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("ENTRE LOS ABAJO FIRMADOS: De una parte, la sociedad ". $title .", entidad organizada y existente de acuerdo con las leyes de la República Dominicana, portadora de su Registro Nacional de Contribuyente (RNC) No. ". StockData::getPrincipal()->rnc .", con su domicilio social establecido en ". StockData::getPrincipal()->address .", quien en lo que sigue del presente contrato se denominará EL ARRENDADOR ----------------------------------------------------------------------------"), 'ISO-8859-1'),0,'J');
  
$pdf->ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("Y de la otra parte, la señor/a ".$clients->name.", ".$clients->nationality.", dominicano, mayor de edad, ".$clients->estado.", titular de la cedula de identidad y electoral número ".$clients->no." domiciliado y residente en ".$clients->address.", quien en lo que sigue de este contrato se denominará EL ARRENDATARIO.--------"), 'ISO-8859-1'),0,'J');


$pdf->ln(2);
$pdf->setX(6);
$pdf->MultiCell(180,4,mb_strtoupper(utf8_decode("PRIMERO: EL ARRENDADOR es propietario del vehículo, marca ".strtoupper($cars->getBrand()->name)." , modelo ".strtoupper($cars->name).", año de fabricación ".strtoupper($cars->year).", de color ".strtoupper($cars->getExColor()->name)." , No de chasis ".strtoupper($cars2->chassis)." y con placa de ".strtoupper($cars->plate).".:"), 'ISO-8859-1'),0,'J');



$pdf->ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("SEGUNDO: EL ARRENDADOR deja constancia que el vehículo a que se refiere la cláusula anterior se encuentra en excelentes condiciones, en buen estado de funcionamiento mecánico y conservación de carrocería, pintura y accesorios, sin mayor desgaste que el producido por el uso normal y ordinario.--------------------------------------------
"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("TERCERO: Por el presente contrato, EL ARRENDADOR cede el uso del bien descrito en la cláusula primera en favor de EL ARRENDATARIO, a título de arrendamiento. Por su parte, EL ARRENDATARIO se obliga a pagar a EL ARRENDADOR, en calidad de contraprestación por el uso del bien objeto del presente contrato, un monto asciende a la suma de USD$ ".number_format($sell->total,2,".",",")." cantidad que será pagada en la forma que las partes acuerden.------------------------------------------- -------------------------------------------------------"), 'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("QUINTO: EL ARRENDATARIO se obliga a pagar puntualmente el monto de la renta, el cual se hará de la forma siguiente: (único pago a la entrega del vehículo).---------------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("SEXTO: EL ARRENDATARIO se obliga a destinar el bien arrendado única y exclusivamente para transporte particular. En consecuencia queda establecido que EL ARRENDATARIO es la única persona autorizada para conducir el vehículo, sin poder emplearlo para transporte de carga o servicio de taxi.-------------------------------------------------"), 'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("PARRAFO: Así también, le queda prohibido a EL ARRENDATARIO realizarle cambios o alteraciones internas y externas al bien arrendado y sus accesorios, sin el consentimiento expreso y por escrito de EL ARRENDADOR.------------------------------------------------------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("SEPTIMO: EL ARRENDATARIO está obligado a efectuar por cuenta y costo propio las reparaciones y mantenimientos que sean necesarios para conservar el vehículo en el mismo estado en que se le fue entregado, en cuyo caso deberá optar por repuestos originales y servicio de primera categoría.-----------------------------------------------------------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("OCTAVO: Queda entendido que mientras el vehículo se encuentre en posesión de EL ARRENDATARIO, éste responderá en forma exclusiva y excluyente por los daños causados a terceras personas, viajen o no en el interior del vehículo; también responderá por los daños causados a propiedades privadas o públicas.-----------------------------------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("PÁRRAFO: Cualquier hecho delictivo cometido en el vehículo objeto del presente contrato, durante periodo de arrendamiento, será responsabilidad única y exclusiva DEL ARRENDATARIO. -----------------------------------------------------------------------------------------------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("NOVENO: EL ARRENDATARIO no podrá ceder a terceros el bien objeto presente contrato bajo ningún título, ni subarrendarlo, ni ceder su posición contractual, salvo que cuente con el consentimiento expreso y por escrito de EL ARRENDADOR, en cuyo caso el nuevo ARRENDATARIO estará obligado a firmar un documento similar al presente contrato. ------------- ----------------------------------------------------------------------------------------------"), 'ISO-8859-1'),0,'J');



$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("PÁRRAFO: En caso de robo o accidente donde el vehículo, objeto del presente contrato sea declarado como pérdida total, EL ARRENDATARIO estará obligado a pagar un deducible del valor del vehículo, a fin de que EL ARRENDADOR pueda gestionar un vehículo similar y cubrir los daños y perjuicios que tales circunstancias puedan causarle. ----------------------"), 'ISO-8859-1'),0,'J');



$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("DÉCIMO: Todo litigio o controversia, derivados o relacionados con este contrato, en primera instancia será jurisdicción exclusiva de los tribunales correspondiente al Municipio de Moca, Provincia Espaillat, cuyas decisiones serán cumplidas de forma obligatoria por las partes.------- --------------------------------------------------------------------------------------------------"), 'ISO-8859-1'),0,'J');



$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("DÉCIMO PRIMERO: La falta de pago de los montos acordados en el presente contrato, en calidad de contraprestación por el uso del bien arrendado, en el lugar y fecha convenida así como la violación de cualquiera de las obligaciones contenidas en este contrato, lo hará rescindible de pleno derecho, a juicio de EL ARRENDADOR.----------------"), 'ISO-8859-1'),0,'J');



$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(180,4,mb_strtoupper(utf8_decode("HECHO Y FIRMADO en dos originales y de buena fe, en la ciudad de ".$user->address.", hoy ".date('d', strtotime($sell->start_at))." del mes de ".date('m', strtotime($sell->start_at))." del año (".date('Y', strtotime($sell->start_at)).").------------------------------------------------------------------------------------------------"), 'ISO-8859-1'),0,'J');


 break;
  
  case 'EN': $pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("BETWEEN THE UNDERSIGNED: On the one hand, the company ". $title .", an entity organized and existing under the laws of the Dominican Republic, holder of its National Taxpayer Registry (RNC) No. ". StockData::getPrincipal()->rnc .", with its registered office established at ". StockData::getPrincipal()->address .", hereinafter referred to in this contract as THE LESSOR ----------------------------------------------------------------------------"), 'ISO-8859-1'),0,'J');

$pdf->ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("And on the other hand, Mr./Ms. ".$clients->name.", ".$clients->nationality.", Dominican, of legal age, ".$clients->estado.", holder of the identity and electoral card number ".$clients->no." domiciled and residing at ".$clients->address.", hereinafter referred to in this contract as THE LESSEE.--------"), 'ISO-8859-1'),0,'J');


$pdf->ln(2);
$pdf->setX(6);
$pdf->MultiCell(180,4,mb_strtoupper(utf8_decode("FIRST: THE LESSOR is the owner of the vehicle, brand ".strtoupper($cars->getBrand()->name)." , model ".strtoupper($cars->name).", year of manufacture ".strtoupper($cars->year).", color ".strtoupper($cars->getExColor()->name)." , chassis No. ".strtoupper($cars2->chassis)." and license plate ".strtoupper($cars->plate).".:"), 'ISO-8859-1'),0,'J');


$pdf->ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("SECOND: THE LESSOR certifies that the vehicle referred to in the previous clause is in excellent condition, in good mechanical working order, and well preserved in bodywork, paint, and accessories, with no greater wear than that produced by normal and ordinary use.--------------------------------------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("THIRD: By this contract, THE LESSOR grants the use of the property described in the first clause in favor of THE LESSEE, by way of lease. In turn, THE LESSEE undertakes to pay THE LESSOR, as consideration for the use of the leased property, the sum of USD$ ".number_format($sell->total,2,".",",").", which shall be paid in the manner agreed upon by the parties.------------------------------------------- -------------------------------------------------------"), 'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("FIFTH: THE LESSEE undertakes to pay the rental amount on time, which shall be made as follows: (single payment upon delivery of the vehicle).---------------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("SIXTH: THE LESSEE undertakes to use the leased property solely and exclusively for private transportation. Consequently, it is established that THE LESSEE is the only person authorized to drive the vehicle, and may not use it for cargo transportation or taxi service.-------------------------------------------------"), 'ISO-8859-1'),0,'J');

$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("PARAGRAPH: Likewise, THE LESSEE is prohibited from making internal or external changes or alterations to the leased property and its accessories without the express written consent of THE LESSOR.------------------------------------------------------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("SEVENTH: THE LESSEE is obliged to carry out, at their own expense, the repairs and maintenance necessary to keep the vehicle in the same condition in which it was delivered, and in such case must use original spare parts and first-class service.-----------------------------------------------------------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("EIGHTH: It is understood that while the vehicle is in the possession of THE LESSEE, they shall be solely and exclusively responsible for any damages caused to third parties, whether or not they are traveling inside the vehicle; as well as for damages caused to private or public property.-----------------------------------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("PARAGRAPH: Any criminal act committed in the vehicle subject to this contract, during the rental period, shall be the sole and exclusive responsibility of THE LESSEE. -----------------------------------------------------------------------------------------------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("NINTH: THE LESSEE may not transfer the leased property subject of this contract to third parties under any title, nor sublease it, nor assign their contractual position, unless with the express written consent of THE LESSOR, in which case the new LESSEE shall be required to sign a document similar to this contract. ------------- ----------------------------------------------------------------------------------------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("PARAGRAPH: In case of theft or accident where the vehicle subject to this contract is declared a total loss, THE LESSEE shall be required to pay a deductible of the value of the vehicle, so that THE LESSOR may obtain a similar vehicle and cover the damages and losses that such circumstances may cause. ----------------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("TENTH: Any dispute or controversy arising out of or related to this contract shall, in the first instance, be under the exclusive jurisdiction of the courts corresponding to the Municipality of Moca, Province Espaillat, whose decisions shall be binding on the parties.------- --------------------------------------------------------------------------------------------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("ELEVENTH: The failure to pay the amounts agreed upon in this contract, as consideration for the use of the leased property, at the place and date agreed, as well as the violation of any of the obligations contained herein, shall make this contract rescindable by right, at the discretion of THE LESSOR.----------------"), 'ISO-8859-1'),0,'J');


$pdf->Ln(2);
$pdf->setX(6);
$pdf->MultiCell(180,4,mb_strtoupper(utf8_decode("DONE AND SIGNED in two originals and in good faith, in the city of ".$user->address.", today ".date('d', strtotime($sell->start_at))." of the month of ".date('m', strtotime($sell->start_at))." of the year (".date('Y', strtotime($sell->start_at)).").------------------------------------------------------------------------------------------------"), 'ISO-8859-1'),0,'J');

 break;
}



$pdf->SetFont('Times','B',9);

$posY = $pdf->GetY() + 20;

// Firma del arrendador
if(isset($user->firma)):
 $srcx = '../'.$user->firma;
    if(file_exists($srcx)){
        $x = ($clients->language == 'ES') ? 30 : 30;
        $pdf->Image($srcx, $x, $posY, 30);
}
endif;

// Firma del arrendatario
if (isset($delivery->firma)):
    $src = '../' . $delivery->firma;
    if (file_exists($src)) {
        $x2 = ($clients->language == 'ES') ? 120 : 110;
        $pdf->Image($src, $x2, $posY, 30);
    }
endif;
    
$pdf->ln(25);
$pdf->setX(30);
$pdf->Cell(5,15,'__________________________________                                         __________________________________');

$pdf->ln(4);
$pdf->setX(30);
$pdf->Cell(5,15,strtoupper(utf8_decode($user->name." ".$user->lastname)));

$pdf->setX(117);
$pdf->Cell(5,15,strtoupper(utf8_decode($clients->name)));

$pdf->ln(4);
$pdf->setX(30);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'ARRENDADOR/A:'); break;
  case 'EN': $pdf->Cell(5,15,'LESSOR:'); break;
}


$pdf->setX(117);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'ARRENDATARIO/A:'); break;
  case 'EN': $pdf->Cell(5,15,'LESSEE:'); break;
}
    
if(!empty(StockData::getPrincipal()->notario)):
$pdf->SetFont('Times','I',6);

$pdf->ln(12);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("Yo, ".StockData::getPrincipal()->notario.", Abogado/a Notario Público. CERTIFICO Y DOY FE: Que por ante mí han comparecido los señores ".$user->name." ".$user->lastname.", en representación de (".$title."), quienes me manifestaron bajo la fe del juramento, que es así como acostumbran a firmar todos los actos de su vida pública y privada. En la ciudad de ".$sell->getLocation()->name.", República Dominicana, a los ".date("d",strtotime($sell->start_at))." días del mes ".date("m",strtotime($sell->start_at))." del año ".date("Y",strtotime($sell->start_at))."."), 'ISO-8859-1'),0,'J');; break;
  
  case 'EN': $pdf->MultiCell(190,3,mb_strtoupper(utf8_decode("Me, ".StockData::getPrincipal()->notario.", Lawyer/Notary Public. I CERTIFY AND FAITH: That the gentlemen ".$user->name.", have appeared before me, representing (".$title."), who declared to me under the faith of the oath, that this is how they usually sign all acts of your public and private life. In the city of ".$sell->getLocation()->name.", Dominican Republic, at ".date("d",strtotime($sell->start_at))." days of the month ".date("m",strtotime($sell->start_at))." of the year ".date("Y",strtotime($sell->start_at))."."), 'ISO-8859-1'),0,'J'); break;
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

if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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

if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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


if($receiver>0):
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

if($receiver>0):
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

if($receiver>0):
$pdf->setX(125);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('TAPA COMBUSTIBLE')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('FUEL CAP')); break;
}
endif;




$posY = $pdf->GetY() - 15;


if (isset($user_delivery->firma)) {
$src = '../' . $user_delivery->firma;
if (file_exists($src)) {
$x2 = ($clients->language == 'ES') ? 45 : 45;
$pdf->Image($src, $x2, $posY, 30);
}
}


$pdf->SetFont('Times','B',8);
 
 
if($receiver>0):
    
if (isset($user_receiver->firma)) {
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


if($receiver>0):

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


if($receiver>0):

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

$pdf->output();

