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
include "../core/app/model/DeliveryData.php";
include "../core/app/model/PaymentData.php";
include "../CF-SYSTEMS/fpdf/fpdf.php";

session_start();
if(isset($_SESSION["client_id"])){ Core::$user = PersonData::getById($_SESSION["client_id"]); }
$clistock= PersonData::getById($_SESSION["client_id"]);
$selstock = StockData::getById($clistock->stock_id);
$symbol =  $selstock->currency;
if($symbol=="€"){ $symbol=chr(128); }
else if($symbol=="₡"){ 
//echo intval("€");
	$symbol=    '₡';}


$rnc =  $selstock->rnc;
$title =  $selstock->name;
$iva_val =  $selstock->imp-val;
$divisa =  $selstock->divisa;
$stock = $selstock;
$sell = BookingData::getById($_GET["id"]);
$cars = CarsData::getById($sell->car_id);
$clients = PersonData::getById($sell->person_id);
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

$ticket_image = $selstock->ticket_image;
$user = $sell->getUser();

$pdf = new FPDF($orientation='P',$unit='mm', array(210,307));
$pdf->AddPage();

include('../core/app/layouts/ticketheader.php');


   if($sell->fuel=="R"):
   
     $src = "../CF-SYSTEMS/storage/configuration/FR.png";
    if(file_exists($src)){
         $pdf->Image($src,25,220,36);  
    } 
  
      endif;
   
    if($sell->fuel=="1/4"):
       
      $src = "../CF-SYSTEMS/storage/configuration/F14.png";
    if(file_exists($src)){
        $pdf->Image($src,25,220,36);     
    }
    
    endif;
    
    if($sell->fuel=="3/4"):
       
      $src = "../CF-SYSTEMS/storage/configuration/F34.png";
    if(file_exists($src)){
         $pdf->Image($src,25,220,36);  
    }
    
    endif;
    
    if($sell->fuel=="1/2"):
       
      $src = "../CF-SYSTEMS/storage/configuration/FM.png";
    if(file_exists($src)){
        $pdf->Image($src,25,220,36);       
    }
    
    endif;
    
    if($sell->fuel=="F"):
       
      $src = "../CF-SYSTEMS/storage/configuration/FF.png";
    if(file_exists($src)){
       $pdf->Image($src,25,220,36);         
    }
    
   endif;
 


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
$pdf->Rect(77, 60, 1, 147.5, 'DF');

 
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

$pdf->SetFont('Arial','',12);    //Letra Arial, negrita (Bold)
$pdf->Ln(10);
$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->MultiCell(180,3.5,strtoupper(utf8_decode("DETALLE DE RESERVACIÓN")),0,'C'); break;
  case 'EN': $pdf->MultiCell(180,3.5,strtoupper(utf8_decode("RESERVATION DETAILS")),0,'C'); break;
}


$pdf->SetFont('Arial','',10);    //Letra Arial, negrita (Bold)
$pdf->Ln(-13);

$pdf->setX(12);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"ENTREGAR: ".date("d/m/Y h:s", strtotime($sell->start_at))); break;
  case 'EN': $pdf->Cell(5,51,"DELIVER: ".date("m/d/Y H:s", strtotime($sell->start_at))); break;
}


$pdf->setX(80);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"NOMBRE COMPLETO:"); break;
  case 'EN': $pdf->Cell(5,51,"FULL NAME:"); break;
}
$pdf->setX(140);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"TELEFONO:"); break;
  case 'EN': $pdf->Cell(5,51,"PHONE:"); break;
}

$pdf->SetFont('Arial','',8);    //Letra Arial, negrita (Bold)
$pdf->SetTextColor (2, 159, 205);
$pdf->Ln(4);

$pdf->setX(12);
$pdf->Cell(5,51,strtoupper(utf8_decode($sell->place_start)));


$pdf->setX(80);
$pdf->Cell(5,51,strtoupper($clients->name));


$pdf->setX(141);
$pdf->Cell(5,51,strtoupper(utf8_decode($clients->phone)));

$pdf->SetTextColor (0,0,0);

$pdf->SetFont('Arial','',10);    //Letra Arial, negrita (Bold)
$pdf->Ln(9);

$pdf->setX(12);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"DEVOLVER: ".date("d/m/Y h:s a", strtotime($sell->end_at))); break;
  case 'EN': $pdf->Cell(5,51,"RETURN: ".date("m/d/Y H:s", strtotime($sell->end_at))); break;
}


$pdf->setX(80);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"DIRECCION: "); break;
  case 'EN': $pdf->Cell(5,51,"ADDRESS: "); break;
}


$pdf->SetFont('Arial','',8);    //Letra Arial, negrita (Bold)
$pdf->SetTextColor (2, 159, 205);
$pdf->Ln(4);

$pdf->setX(12);
$pdf->Cell(5,51,strtoupper(utf8_decode($sell->place_end)));

$pdf->Ln(0);
$pdf->setX(80);
$pdf->Cell(5,51,strtoupper(utf8_decode($clients->address)));


$pdf->SetTextColor (2,2,2);

$pdf->SetFont('Arial','',14);    //Letra Arial, negrita (Bold)

$pdf->Ln(16);
$pdf->setX(12);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("DATOS DEL VEHICULO")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("VEHICLE DATA")); break;
}


$pdf->SetFont('Arial','',10);    //Letra Arial, negrita (Bold)

$pdf->setX(116);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("UNIDAD                        COSTO                    TOTAL")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("UNIT                          COST                    TOTAL")); break;
}



$pdf->SetFont('Arial','',9); 

$pdf->Ln(17.5);
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
  case 'ES': $pdf->Cell(utf8_decode("AÑO")); break;
  case 'EN': $pdf->Cell(5,51,"YEAR: "); break;
}


$pdf->setX(120);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("DIA                                  COSTO                    TOTAL")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("DAY                                  COST                    TOTAL")); break;
}



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



$pdf->setX(157);
$pdf->Cell(5,51,number_format($sell->price,2,".",","));


$pdf->setX(184);
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

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"BALANCE DE RESERVACION: "); break;
  case 'EN': $pdf->Cell(5,51,"RESERVATION BALANCE: "); break;
}

$pdf->setX(184);
$pdf->Cell(5,51,number_format($totpayments,2,".",","));

$pdf->SetTextColor (2, 159, 205);
$pdf->Ln(5);


$pdf->setX(12);
$pdf->Cell(5,51,strtoupper(utf8_decode($cars->plate)));


$pdf->SetTextColor (0,0,0);
$pdf->Ln(15);
$pdf->setX(80);
$pdf->Cell(5,51,"SUBTOTAL: ");

$subtotal += (($sell->price*$sell->day)+$sell->xtotal); 

$pdf->setX(184);
$pdf->Cell(5,51,number_format($subtotal,2,".",","));


$pdf->SetTextColor (0,0,0);

$pdf->Ln(8);
$pdf->setX(80);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"SEGURO: ".$sell->getSure()->name); break;
  case 'EN': $pdf->Cell(5,51,"SURE: ".$sell->getSure()->name); break;
}

$pdf->setX(184);
$pdf->Cell(5,51,number_format($sell->sure,2,".",","));

$pdf->Ln(8);
$pdf->setX(80);
$pdf->Cell(5,51,"ITBIS (18%): ");

$pdf->setX(184);
if($sell->iva>0):
$pdf->Cell(5,51,number_format(($subtotal*($sell->iva/100)),2,".",","));
else:
$pdf->Cell(5,51,number_format(0,2,".",","));
endif;

$pdf->Ln(8);
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

$pdf->Ln(8);
$pdf->setX(80);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"TARJETA (".StockData::getPrincipal()->card."%): "); break;
  case 'EN': $pdf->Cell(5,51,"CARD (".StockData::getPrincipal()->card."%): "); break;
}

$pdf->setX(184);
if($sell->card>0):
$pdf->Cell(5,51,number_format(($sell->total*($sell->card/100)),2,".",","));
else:
$pdf->Cell(5,51,number_format(0,2,".",","));
endif;

$pdf->Ln(8);
$pdf->setX(80);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,"ABONADO: "); break;
  case 'EN': $pdf->Cell(5,51,"SUBSCRIBER : "); break;
}


$pdf->setX(184);
$pdf->Cell(5,51,number_format($totpayments,2,".",","));

$pdf->Ln(8);
$pdf->setX(80);
$pdf->Cell(5,51,"TOTAL: ");

$pdf->setX(184);
$pdf->Cell(5,51,number_format(($sell->total-$totpayments)+$sell->sure,2,".",","));

$pdf->AddPage();
$pdf->SetFont('Arial','',8);    //Letra Arial, negrita (Bold)

$pdf->SetTextColor (254,14,14);

$pdf->setX(10);


switch ($clients->language){
  case 'ES': $pdf->MultiCell(189,3.5,strtoupper(utf8_decode("Nota: Puede realizar una cancelación gratuita antes de los 5 Dias de su llegada, de lo contrario el balance de reservación puede ser exigido como credito o perder el total de su balance de reservación.")),0,'J'); break;
  
  case 'EN': $pdf->MultiCell(189,3.5,strtoupper(utf8_decode("Note: You can make a free cancellation before 5 Days of your arrival, otherwise the reservation balance may be required as a credit or you may lose the entire reservation balance.")),0,'J'); break;
}


$pdf->setX(10);
$pdf->SetDrawColor($color[0],$color[1],$color[2]);
$pdf->SetFillColor($color[0],$color[1],$color[2]);
/// derecha altura tamano anchura
$pdf->Rect(10, 20, 189,13, 'DF');


$pdf->SetDrawColor(235,238,236);
$pdf->SetFillColor(239, 239, 239 );
/// derecha altura tamano anchura
$pdf->Rect(10, 152, 189, 30, 'DF');


$pdf->SetDrawColor(235,238,236);
$pdf->SetFillColor(185, 185, 185 );
/// derecha altura tamano anchura
$pdf->Rect(10, 152, 105, 30, 'DF');



$pdf->SetFont('Arial','B',12);    //Letra Arial, negrita (Bold)

$pdf->SetTextColor (255,255,255);

$pdf->Ln(-16);
$pdf->setX(70);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper(utf8_decode("CLAUSULAS Y CONDICIONES: "))); break;
  case 'EN': $pdf->Cell(5,51,strtoupper(utf8_decode("CLAUSES AND CONDITIONS: "))); break;
}


$pdf->SetFont('Arial','',9);    //Letra Arial, negrita (Bold)

$pdf->SetTextColor (0,0,0);

$pdf->Ln(40);
$pdf->setX(10);


switch ($clients->language){
  case 'ES': $pdf->MultiCell(189,4,strtoupper(utf8_decode("* Si al momento de llegar el dia de su renta y el vehículo presenta fallas o a tenido un accidente previo a su renta, esta sujeto a cambio por un vehículo similar en la misma gama, en caso de querer cambiarlo por otro mayor a su costo debera pagar la diferencia al momento de ser entregado.

* Todas las rentas efectuadas en horas de la madrugada tienen un costo extra de US$ 40 (en el aeropuerto internacional las americas ) De ser fuera del distrito nacional el costo dependerá del lugar de su llegada y se lo notificara el encargado o gestor.

* Al momento de recibir el vehículo exija la documentación del vehículo tales como: Matricula, seguro y marbete.

* Si desea cancelar o acreditar su reservación para una nueva fecha debe ser notificado 5 días antes de su reservación, de lo contrario perderá el derecho a credito y el monto total de la reservación.

* Si aplica para acreditación debe informar la fecha nueva antes de los 5 días de su reservación pautada y en caso de cancelar su nueva reservación perderá el total de su balance de reservación.

* Si aplica para la cancelación gratuita el balance de reservación sele sera devuelto sin la empresa asumir comisiones o gastos para su devolución, y esperar un plazo de 5 a 7 días hábiles para la devolución del balance de reservación.

* En caso de pagar la totalidad de la renta y ser cancelada, el costo por cancelación dentro de los 5 dias antes de su llegada seria de US$ 200.00, y el monto restante seria devuelto en un plazo de 5 a 7 días hábiles sin la empresa asumir comisiones o gastos para su devolución.")),0,'J'); break;

  case 'EN': $pdf->MultiCell(189,4,strtoupper(utf8_decode("* If at the time of arrival on the day of your rental and the vehicle has defects or has had an accident prior to your rental, it is subject to exchange for a similar vehicle in the same range. If you want to exchange it for another greater than its cost, you must pay. the difference at the time of delivery.

* All rentals made in the early morning hours have an extra cost of US$ 40 (at Las Americas International Airport). If outside the national district, the cost will depend on the place of your arrival and the manager or manager will notify you.

* When receiving the vehicle, require vehicle documentation such as: Registration, insurance and tag.

* If you wish to cancel or credit your reservation for a new date, you must be notified 5 days before your reservation, otherwise you will lose the right to credit and the total amount of the reservation.

* If you apply for accreditation, you must inform the new date within 5 days of your scheduled reservation and if you cancel your new reservation, you will lose the entire reservation balance.

* If you apply for free cancellation, the reservation balance will be returned without the company assuming commissions or expenses for its return, and waiting a period of 5 to 7 business days for the return of the reservation balance.

* If you pay the entire rent and it is cancelled, the cost for cancellation within 5 days before your arrival would be US$ 200.00, and the remaining amount would be returned within a period of 5 to 7 business days without the company assume commissions or expenses for its return.")),0,'J'); break;
}


if($sell->firma<>""):
 $src = '../'.$sell->firma;
    if(file_exists($src)){
        $pdf->Image($src,40,150,50);      
    }
endif;


$pdf->Ln(22);
$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper(utf8_decode("FIRMA DEL CLIENTE: "))); break;
  case 'EN': $pdf->Cell(5,51,strtoupper(utf8_decode("CUSTOMER SIGNATURE: "))); break;
}


$pdf->SetFont('Arial','',12);    //Letra Arial, negrita (Bold)

$pdf->Ln(-2);
$pdf->setX(120);
$pdf->MultiCell(189,4,strtoupper(utf8_decode("$title")),0,'J');

$pdf->SetFont('Arial','',9);    //Letra Arial, negrita (Bold)
$pdf->Ln(5);
$pdf->setX(120);
$pdf->MultiCell(80,4,mb_strtoupper(utf8_decode($stock->address), 'ISO-8859-1'),0,'L');

$pdf->output();
