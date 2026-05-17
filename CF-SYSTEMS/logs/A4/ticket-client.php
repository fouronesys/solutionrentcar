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
include "../../CF-SYSTEMS/fpdf/fpdf.php";

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
$clients2 = PersonData::getById($sell->person2_id);

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

$pdf = new FPDF($orientation='P',$unit='mm', 'A4');


if($sell->car2_id>0):
$pdf->AddPage();

include('../core/app/layouts/ticketheader.php');


if($ticket_image2!=""){
   $src = "../CF-SYSTEMS/storage/configuration/".$ticket_image2;
    if(file_exists($src)){
        $pdf->Image($src,103,95,35);      
    }
}


if($sell->car2_id>0):

$pdf->SetFont('Arial','B',10); 
$pdf->Ln(-15);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("VEHICULO SOLICITADO: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("REQUESTED VEHICLE: ")); break;
}
$pdf->Ln(5);
else:
$pdf->Ln(-15);   
endif;
$pdf->SetFont('Arial','B',8); 
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("MARCA: ".strtoupper($cars2->getBrand()->name))); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("BRAND: ".strtoupper($cars2->getBrand()->name))); break;
}

$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("FECHA Y HORA: ".date("d-m-Y", strtotime($sell->created_at."- 4 hours")))); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("DATE: ".date("m-d-Y", strtotime($sell->created_at."- 4 hours")))); break;
}

$pdf->setX(78);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,date("h:i a", strtotime($sell->created_at."- 4 hours"))); break;
  case 'EN': $pdf->Cell(5,51,date("H:i ", strtotime($sell->created_at."- 4 hours"))); break;
}


$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor (0, 0, 0);

$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________    _____________');

$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("MODELO: ".$cars2->name)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("MODEL: ".$cars2->name)); break;
}
$pdf->setX(78);
$pdf->Cell(5,51,strtoupper(utf8_decode("F: ".$cars2->token)));
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor (0, 0, 0);

$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________    _____________');

$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("CARRO SERA DEVUELTO EN: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("CAR WILL BE RETURN TO: ")); break;
}

$pdf->Ln(3);
$pdf->SetFont('Arial','B',8);
$pdf->setX(6);
$pdf->SetTextColor (2, 159, 205);
$pdf->Cell(5,51,strtoupper(utf8_decode($sell->place_end)));
$pdf->SetTextColor (0, 0, 0);


$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("PLACA NO.: ".$cars2->plate)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("CAR LIC.: ".$cars2->plate)); break;
}


$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("NOMBRE: ".$clients->name)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("NAME: ".$clients->name)); break;
}


$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________');



$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("FORMA DE PAGO: ".$sell->getF()->name)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("METHOD OF PAYMENT: ".$sell->getF()->name)); break;
}


$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________');


$pdf->ln(5);
$pdf->setX(6);
$pdf->Cell(5,15,'___________________________________________________________');
$pdf->Ln(-14);
$pdf->setX(6);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("LICENCIA DE CONDUCIR: ".$clients->license)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("DRIVER LICENSE NO: ".$clients->license)); break;
}


$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);

if ($sell->person2_id>0):
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("CONDUCTOR ADICIONAL: ".$clients2->name)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("ADDITIONAL DRIVER: ".$clients2->name)); break;
}
else:
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("CONDUCTOR ADICIONAL: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("ADDITIONAL DRIVER: ")); break;
}    
endif;

$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);

if ($sell->person2_id>0):
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("LICENCIA DE CONDUCIR: ".$clients2->license)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("DRIVER LICENSE NO: ".$clients2->license)); break;
}
else:
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("LICENCIA DE CONDUCIR: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("DRIVER LICENSE NO: ")); break;
}    
endif;

$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("DIRECCION LOCAL Y TELEFONO: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("LOCAL ADDRESS AND TEL. NO. : ")); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->ln(4);
$pdf->setX(6);
$pdf->SetTextColor (2, 159, 205);
$pdf->Cell(5,51,strtoupper($clients->address));
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor (0, 0, 0);
$pdf->Ln(31);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("HE LEIDO LOS TERMINOS Y CONDICIONES EN AMBOS LADOS DE ESTE CONTRATO DE ARRENDAMIENTO Y FIRMO DE CONFORMIDAD: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("I HAVE READ THE TERMS AND CONDITIONS ON BOTH SIDES THIS SING LEASE AND UNDER: ")); break;
}


$pdf->ln(-7);
$pdf->setX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("DIRECCION CORRECTA EN EL EXTERIOR:")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("BILLING ADDRES:")); break;
}


$pdf->SetFont('Arial','B',8);
$pdf->ln(4);
$pdf->setX(6);
$pdf->SetTextColor (2, 159, 205);
$pdf->Cell(5,51,strtoupper($clients->address2));
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor (0, 0, 0);
$pdf->ln(20);
$pdf->setX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("TEL.: (ESTADIA): ".$clients->phone)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("TEL.: (STAY): ".$clients->phone)); break;
}

$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'___________________________________');

$pdf->Ln(-14);
$pdf->setX(6);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("TEL.: (EXTRANJERO): ".$clients->phone2)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("TEL.: (FOREIGN): ".$clients->phone2)); break;
}

$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'___________________________________');

$pdf->Ln(-6);
$pdf->setX(6);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("COMBUTIBLE DE SALIDA: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("OUT FUEL:")); break;
}


$pdf->setX(40);

if($receiver>0):
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("COMBUTIBLE DE ENTRADA: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("INPUT FUEL:")); break;
}

$pdf->SetFont('Arial','B',8);

if($sell->fuel=="R"):
       
      $src = "../CF-SYSTEMS/storage/configuration/FR.png";
    if(file_exists($src)){
        $pdf->Image($src,2,160,30);      
    }
    
    endif;

  if($sell->fuel=="1/4" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/F14.png";
    if(file_exists($src)){
          $pdf->Image($src,2,190,30);    
    }
    
    endif;
    
    if($sell->fuel=="3/4"):
       
      $src = "../CF-SYSTEMS/storage/configuration/F34.png";
    if(file_exists($src)){
           $pdf->Image($src,2,190,30);             
    }
    
    endif;
    
    if($sell->fuel=="1/2" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FM.png";
    if(file_exists($src)){
          $pdf->Image($src,2,190,30);    
    }
    
    endif;
    
    if($sell->fuel=="F" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FF.png";
    if(file_exists($src)){
          $pdf->Image($src,2,190,30);          
    }
    
   endif;


   
if($receiver->fuel=="R"):
       
      $src = "../CF-SYSTEMS/storage/configuration/FR.png";
    if(file_exists($src)){
        $pdf->Image($src,30,160,30);      
    }
    
    endif;

  if($receiver->fuel=="1/4" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/F14.png";
    if(file_exists($src)){
         $pdf->Image($src,106,190,30);  
    }
    
    endif;
    
    if($receiver->fuel=="3/4"):
       
      $src = "../CF-SYSTEMS/storage/configuration/F34.png";
    if(file_exists($src)){
          $pdf->Image($src,106,190,30);     
    }
    
    endif;
    
    if($receiver->fuel=="1/2" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FM.png";
    if(file_exists($src)){
        $pdf->Image($src,106,190,30);         
    }
    
    endif;
    
    if($receiver->fuel=="F" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FF.png";
    if(file_exists($src)){
         $pdf->Image($src,106,190,30);  
    }
    
   endif;



if($cars2->getCategory()->name=="Ambulancia"):
$src = "../CF-SYSTEMS/storage/configuration/Ambulancia.jpg";
    if(file_exists($src)){
        $pdf->Image($src,10,220,30);          
    }

endif;
    
if($cars2->getCategory()->name=="Jeepeta"):
$src = "../CF-SYSTEMS/storage/configuration/Jeepeta.jpg";
    if(file_exists($src)){
        $pdf->Image($src,10,220,30);          
    }

endif;

if($cars2->getCategory()->name=="SUV"):
$src = "../CF-SYSTEMS/storage/configuration/SUV.jpg";
    if(file_exists($src)){
        $pdf->Image($src,10,220,30);          
    }

endif;

if($cars2->getCategory()->name=="Pickup"):
$src = "../CF-SYSTEMS/storage/configuration/Camioneta.jpg";
    if(file_exists($src)){
        $pdf->Image($src,10,220,30);          
    }

endif;

if($cars2->getCategory()->name=="Carro"):
$src = "../CF-SYSTEMS/storage/configuration/Carro.jpg";
    if(file_exists($src)){
        $pdf->Image($src,10,250,50);           
    }

endif;



if(isset($delivery->danger)):
 $src = '../'.$delivery->danger;
    if(file_exists($src)){
        $pdf->Image($src,10,250,50); 
    }
endif;  




if($cars2->getCategory()->name=="Ambulancia"):
$src = "../CF-SYSTEMS/storage/configuration/Ambulancia.jpg";
    if(file_exists($src)){
        $pdf->Image($src,115,220,30);         
    }

endif;
    
if($cars2->getCategory()->name=="Jeepeta"):
$src = "../CF-SYSTEMS/storage/configuration/Jeepeta.jpg";
    if(file_exists($src)){
        $pdf->Image($src,115,220,30);         
    }

endif;

if($cars2->getCategory()->name=="SUV"):
$src = "../CF-SYSTEMS/storage/configuration/SUV.jpg";
    if(file_exists($src)){
        $pdf->Image($src,115,220,30);         
    }

endif;

if($cars2->getCategory()->name=="Pickup"):
$src = "../CF-SYSTEMS/storage/configuration/Camioneta.jpg";
    if(file_exists($src)){
        $pdf->Image($src,115,220,30);         
    }

endif;

if($cars2->getCategory()->name=="Carro"):
$src = "../CF-SYSTEMS/storage/configuration/Carro.jpg";
    if(file_exists($src)){
        $pdf->Image($src,140,250,50);          
    }

endif;


if(isset($receiver->danger)):
 $src = '../'.$receiver->danger;
    if(file_exists($src)){
         $pdf->Image($src,140,250,50);     
    }
endif;  


else:

if($cars2->getCategory()->name=="Ambulancia"):
$src = "../CF-SYSTEMS/storage/configuration/Ambulancia.jpg";
    if(file_exists($src)){
        $pdf->Image($src,13,248,90);         
    }

endif;
    
if($cars2->getCategory()->name=="Jeepeta"):
$src = "../CF-SYSTEMS/storage/configuration/Jeepeta.jpg";
    if(file_exists($src)){
        $pdf->Image($src,13,248,90);        
    }

endif;

if($cars2->getCategory()->name=="SUV"):
$src = "../CF-SYSTEMS/storage/configuration/SUV.jpg";
    if(file_exists($src)){
        $pdf->Image($src,13,248,90);         
    }

endif;

if($cars2->getCategory()->name=="Pickup"):
$src = "../CF-SYSTEMS/storage/configuration/Camioneta.jpg";
    if(file_exists($src)){
        $pdf->Image($src,13,248,90);         
    }

endif;

if($cars2->getCategory()->name=="Carro"):
$src = "../CF-SYSTEMS/storage/configuration/Carro.jpg";
    if(file_exists($src)){
        $pdf->Image($src,13,248,90);            
    }

endif;



if(isset($delivery->danger)):
 $src = '../'.$delivery->danger;
    if(file_exists($src)){
        $pdf->Image($src,13,248,90);  
    }
endif;  

$pdf->SetFont('Arial','B',8);

if($sell->fuel=="R"):
       
      $src = "../CF-SYSTEMS/storage/configuration/FR.png";
    if(file_exists($src)){
        $pdf->Image($src,2,150,50);        
    }
    
    endif;

  if($sell->fuel=="1/4" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/F14.png";
    if(file_exists($src)){
            $pdf->Image($src,2,150,50);       
    }
    
    endif;
    
    if($sell->fuel=="3/4"):
       
      $src = "../CF-SYSTEMS/storage/configuration/F34.png";
    if(file_exists($src)){
           $pdf->Image($src,2,150,50);          
    }
    
    endif;
    
    if($sell->fuel=="1/2" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FM.png";
    if(file_exists($src)){
          $pdf->Image($src,2,150,50);             
    }
    
    endif;
    
    if($sell->fuel=="F" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FF.png";
    if(file_exists($src)){
          $pdf->Image($src,2,150,50);             
    }
    
   endif;

endif;

$pdf->Ln(-100);
$pdf->setX(102);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("DESDE: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("FROM:")); break;
}


$pdf->setX(168);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("HASTA: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("UNTIL:")); break;
}


$pdf->ln(4);
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor (2, 159, 205);
$pdf->setX(102);
$pdf->Cell(5,51,strtoupper(date("d-m-Y h:i:s a",strtotime($sell->start_at))));

$pdf->setX(168);
$pdf->Cell(5,51,strtoupper(date("d-m-Y h:i:s a",strtotime($sell->end_at))));

$pdf->SetTextColor (0, 0, 0);
$pdf->SetFont('Arial','B',8);

$pdf->ln(19);
$pdf->setX(102);
$pdf->Cell(5,15,'_______________________________________________________________');
$pdf->ln(4);
$pdf->setX(102);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,strtoupper("PRECIO POR DIA: ".$sell->price." ".StockData::getPrincipal()->currency)); break;
  case 'EN': $pdf->Cell(5,15,strtoupper("PRICE PER DAY: ".$sell->price." ".StockData::getPrincipal()->currency)); break;
}


$pdf->ln(1);
$pdf->setX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->ln(4);

$pdf->setX(102);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,strtoupper("TOTAL DE DIAS: ".$sell->day)); break;
  case 'EN': $pdf->Cell(5,15,strtoupper("TOTAL OF DAYS: ".$sell->day)); break;
}


$pdf->ln(1);
$pdf->setX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->ln(4);
$pdf->setX(102);
$pdf->Cell(5,15,'COLOR: ');


$pdf->SetFont('Arial','B',8);

$pdf->setX(114);
$pdf->SetTextColor (2, 159, 205);
$pdf->Cell(5,15,strtoupper($cars->getExColor()->name));
$pdf->SetTextColor (0, 0, 0);
$pdf->SetFont('Arial','B',8);
$pdf->ln(1);
$pdf->setX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->SetFont('Arial','B',8); 
$pdf->ln(4);
$pdf->setX(102);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,strtoupper("KM O MILLAS EN SALIDA: ".$cars->kms)); break;
  case 'EN': $pdf->Cell(5,15,strtoupper("KM OR MILES ON DEPARTURE: ".$cars->kms)); break;
}
if ($sell->car_id>0 and $receiver>0):
$pdf->setX(160);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'KM O MILLAS EN LLEGADA: '.$receiver->kms); break;
  case 'EN': $pdf->Cell(5,15,'KM OR MILES ON ARRIVAL: '.$receiver->kms); break;
}

endif;


$pdf->ln(1);
$pdf->setX(102);
$pdf->Cell(5,15,'_______________________________________________________________');


$pdf->SetFont('Arial','B',8); 
if ($sell->car_id>0 and $receiver>0):
$pdf->ln(4);
$pdf->setX(102);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'KM O MILLAS TOTAL: '.($receiver->kms-$delivery->kms)); break;
  case 'EN': $pdf->Cell(5,15,'KM OR MILES TOTAL: '.($receiver->kms-$delivery->kms)); break;
}
else:
$pdf->ln(4);
$pdf->setX(102);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'KM O MILLAS TOTAL:'); break;
  case 'EN': $pdf->Cell(5,15,'KM OR MILES TOTAL:'); break;
}
endif;


$pdf->ln(1);
$pdf->setX(102);
$pdf->Cell(5,15,'_______________________________________________________________');


$pdf->ln(10);
$pdf->setX(102);

switch ($clients->language){
  case 'ES': $pdf->MultiCell(35,4,"LA RESPONSABILIDAD DEDUCIBLE HACIA LOS CLIENTES EN CASO DE ACCIDENTE:", 0, 'C'); break;
  case 'EN': $pdf->MultiCell(35,4,"THE DEDUCTIBLE RESPONSIBILITY TO THE COSTUMER IN CASE OF AN ACCIDENT:", 0, 'C'); break;
}

$pdf->ln(-26);
$pdf->SetTextColor (2, 159, 205);
$sure = SureData::getById($sell->type_sure);
$pdf->ln(4);
$pdf->setX(140);
$pdf->Cell(5,15,$sure->name." :");

$pdf->setX(181);
$pdf->Cell(5,15,$sell->sure." ".StockData::getPrincipal()->currency);
$pdf->SetTextColor (0, 0, 0);
$pdf->ln(1);
$pdf->setX(140);
$pdf->Cell(5,15,'________________________    _____________');




$pdf->ln(9);
$pdf->setX(140);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'ELEGIDO:'); break;
  case 'EN': $pdf->Cell(5,15,'ELECTED:'); break;
}

$pdf->setX(170);
if ($sell->sure>0):
    
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'[SI]:'); break;
  case 'EN': $pdf->Cell(5,15,'[YEAH]:'); break;
}

else:
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'[NO]:'); break;
  case 'EN': $pdf->Cell(5,15,'[NOT]:'); break;
}
endif;



$pdf->ln(10);
$pdf->setX(102);
$pdf->Cell(5,15,'_______________________________________________________________');



$pdf->ln(4);
$pdf->setX(140);
$pdf->SetTextColor (0, 0, 0);

$pdf->Cell(5,15,'SUBTOTAL:');
$pdf->setX(180);
$pdf->SetTextColor (2, 159, 205);
$pdf->Cell(5,15,$sell->total-$sell->value_iva." ".StockData::getPrincipal()->currency);


$pdf->SetTextColor (0, 0, 0);
$pdf->ln(1);
$pdf->setX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->ln(4);
$pdf->setX(140);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'IMPUESTO 18%:'); break;
  case 'EN': $pdf->Cell(5,15,'TAXES 18%:'); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor (2, 159, 205);

$pdf->setX(180);
$pdf->Cell(5,15, number_format($sell->value_iva,2,".",",")." ".StockData::getPrincipal()->currency);

$pdf->SetTextColor (0, 0, 0);
$pdf->SetFont('Arial','B',8);

$pdf->ln(2);
$pdf->setX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->ln(4);

$pdf->setX(140);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'OTROS COBROS:'); break;
  case 'EN': $pdf->Cell(5,15,'OTHER CHARGES:'); break;
}

$pdf->SetFont('Arial','B',8);

$pdf->SetTextColor (2, 159, 205);
if($sell->plane>0):
$pdf->setX(140);
$pdf->Cell(5,15, number_format($sell->plane,2,".",",")." ".StockData::getPrincipal()->currency);
endif;
$pdf->SetTextColor (0, 0, 0);
$pdf->SetFont('Arial','B',8);

$pdf->ln(1.5);
$pdf->setX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->ln(4);
$pdf->setX(140);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'CARGOS TOTALES:'); break;
  case 'EN': $pdf->Cell(5,15,'TOTAL CHARGES:'); break;
}

$pdf->SetTextColor (2, 159, 205);
$pdf->setX(180);
$pdf->Cell(5,15, number_format($sell->total+$plane_calc,2,".",",")." ".StockData::getPrincipal()->currency);

$pdf->SetFont('Arial','B',8);

$pdf->SetTextColor (0, 0, 0);
$pdf->ln(1);
$pdf->setX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$totpayments = 0;
$payments = PaymentData::getByPayment($sell->id);
$totpayments = $payments[0]->t!=null?$payments[0]->t:0;

$pdf->ln(4);
$pdf->setX(140);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'MONTO ABONADO:'); break;
  case 'EN': $pdf->Cell(5,15,'AMOUNT PAID:'); break;
}

$pdf->SetTextColor (2, 159, 205);
$pdf->setX(180);
$pdf->Cell(5,15,$totpayments." ".StockData::getPrincipal()->currency);

$pdf->SetTextColor (0, 0, 0);
$pdf->ln(2);
$pdf->setX(140);
$pdf->Cell(5,15,'_______________________    ______________');


$pdf->ln(4);
$pdf->setX(140);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'CARGO PENDIENTE:'); break;
  case 'EN': $pdf->Cell(5,15,'PENDING CHARGE:'); break;
}

$pdf->SetTextColor (2, 159, 205);
$pdf->SetFont('Arial','B',8);
$pdf->setX(180);
$pdf->Cell(5,15, number_format(($sell->total+$sell->plane)-$totpayments,2,".",",")." ".StockData::getPrincipal()->currency);

$pdf->SetTextColor (0, 0, 0);
$pdf->ln(1);
$pdf->setX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->ln(15);
$pdf->setX(70);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'PREPARADO POR:'); break;
  case 'EN': $pdf->Cell(5,15,'PREPARED BY:'); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor (2, 159, 205);
$pdf->setX(120);
$pdf->Cell(5,15,strtoupper($sell->getUser()->name." ".$sell->getUser()->lastname));

$pdf->SetTextColor (0, 0, 0);

$pdf->setX(140);
$pdf->Cell(5,15,'');

$pdf->SetFont('Arial','B',8);
$pdf->ln(1);
$pdf->setX(70);
$pdf->Cell(5,15,'_____________________________     ____________________________________________________');


$pdf->ln(7);
$pdf->setX(70);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'OBSERVACION:'); break;
  case 'EN': $pdf->Cell(5,15,'OBSERVATION:'); break;
}

$pdf->ln(10);

$pdf->setX(70);
$pdf->MultiCell(65,4,$sell->comment, 0, 'L');

if (count($receiver)>0):
$pdf->ln(70);
$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('DAÑO ENTREGADO AL CLIENTE:')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('DAMAGE DELIVERED TO THE CUSTOMER:')); break;
}

$pdf->setX(135);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('DAÑO RECIBIDO DEL CLIENTE:')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('DAMAGE RECEIVED FROM CUSTOMER:')); break;
}
endif;
if($delivery>0):



$pdf->ln(20);
$pdf->setX(70);
$pdf->Cell(5,15,'__________________________________                 __________________________________');

$pdf->ln(4);
$pdf->setX(70);
$pdf->Cell(5,15,strtoupper(utf8_decode($user->name." ".$user->lastname)).'                                                              '.strtoupper(utf8_decode($clients->name)).'');
$pdf->ln(4);
$pdf->setX(70);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'ARRENDADOR/A:                                                        ARRENDATARIO/A:'); break;
  case 'EN': $pdf->Cell(5,15,'LESSOR:                                                                     LESSEE:'); break;
}




if($receiver>0):
    
if(isset($user_delivery->firma)):
 $src = '../'.$user_delivery->firma;
    if(file_exists($src)){
        $pdf->Image($src,70,205,30);    
    }
endif;  

if(isset($user_receiver->firma)):
 $src = '../'.$user_receiver->firma;
    if(file_exists($src)){
        $pdf->Image($src,150,205,30);  
    }
endif;  

if(isset($user->firma)):
 $srcx = '../'.$user->firma;
    if(file_exists($srcx)){
        $pdf->Image($src,90,170,30);      
    }
endif;

if(isset($delivery->firma)):
 $src = '../'.$delivery->firma;
    if(file_exists($src)){
        $pdf->Image($src,150,170,30);    
    }
endif;   
    
$pdf->ln(23);
$pdf->setX(60);
$pdf->Cell(5,15,'__________________________________                                         __________________________________');

$pdf->ln(4);
$pdf->setX(60);


switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'ENTREGADOR/A:                                                                                      RECIBIDOR/A:'); break;
  case 'EN': $pdf->Cell(5,15,'DELIVERY:                                                                                          RECEIVER:'); break;
}

$pdf->ln(4);
$pdf->setX(60);
$pdf->Cell(5,15,strtoupper($user_delivery->name." ".$user_delivery->lastname).'                                                                                      '.strtoupper($user_receiver->name." ".$user_receiver->lastname));

else:
    
if(isset($user_delivery->firma)):
 $src = '../'.$user_delivery->firma;
    if(file_exists($src)){
        $pdf->Image($src,85,198,30);    
    }
endif;  

if(isset($user->firma)):
 $srcx = '../'.$user->firma;
    if(file_exists($srcx)){
        $pdf->Image($srcx,85,172,30);      
    }
endif;

if(isset($delivery->firma)):
 $src = '../'.$delivery->firma;
    if(file_exists($src)){
        $pdf->Image($src,160,165,30);    
    }
endif;   


$pdf->ln(18);
$pdf->setX(70);
$pdf->Cell(5,15,'__________________________________');


$pdf->ln(6);
$pdf->setX(70);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'ENTREGADOR/A: '.strtoupper($user_delivery->name." ".$user_delivery->lastname)); break;
  case 'EN': $pdf->Cell(5,15,'DELIVERY: '.strtoupper($user_delivery->name." ".$user_delivery->lastname)); break;
}
   
endif;

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
else:
  

$pdf->ln(15);
$pdf->setX(65);
$pdf->Cell(5,15,'__________________________________');

$pdf->ln(6);
$pdf->setX(80);
$pdf->Cell(5,15,strtoupper(strtoupper($clients->name)));
$pdf->ln(6);
$pdf->setX(80);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'ARRENDATARIO/A'); break;
  case 'EN': $pdf->Cell(5,15,'TENANT'); break;
}


if(isset($user->firma)):
 $srcx = '../'.$user->firma;
    if(file_exists($srcx)){
        $pdf->Image($srcx,85,215,30);      
    }
endif; 
    

endif;
    
if(StockData::getPrincipal()->notario>0):
$pdf->ln(18);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->MultiCell(197,4,utf8_decode("Yo, ".StockData::getPrincipal()->notario.", Abogado/a Notario Público. CERTIFICO Y DOY FE: Que por ante mí han comparecido los señores ".$user->name.", en representación de (".$title."), quienes me manifestaron bajo la fe del juramento, que es así como acostumbran a firmar todos los actos de su vida pública y privada. En la ciudad de ".$sell->getLocation()->name.", República Dominicana, a los ".date("d",strtotime($sell->start_at))." días del mes ".date("m",strtotime($sell->start_at))." del año ".date("Y",strtotime($sell->start_at))."."), 0, 'J'); break;
  
  case 'EN': $pdf->MultiCell(197,4,utf8_decode("Me, ".StockData::getPrincipal()->notary.", Lawyer/Notary Public. I CERTIFY AND FAITH: That the gentlemen ".$user->name.", have appeared before me, representing (".$title."), who declared to me under the faith of the oath, that this is how they usually sign all acts of your public and private life. In the city of ".$sell->getLocation()->name.", Dominican Republic, at ".date("d",strtotime($sell->start_at))." days of the month ".date("m",strtotime($sell->start_at))." of the year ".date("Y",strtotime($sell->start_at))."."), 0, 'J'); break;
}


$pdf->ln(12);
$pdf->setX(6);
$pdf->MultiCell(197,4,utf8_decode("______________________________________________
Notario Público"), 0, 'C');
endif; 

endif;

$pdf->AddPage();

include('../core/app/layouts/ticketheader.php');


if($ticket_image2!=""){
   $src = "../CF-SYSTEMS/storage/configuration/".$ticket_image2;
    if(file_exists($src)){
        $pdf->Image($src,103,95,35);      
    }
}


if($sell->car2_id>0):

$pdf->SetFont('Arial','B',10); 
$pdf->Ln(-15);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("VEHICULO DE REEMPLAZO: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("REPLACEMENT VEHICLE: ")); break;
}
$pdf->Ln(5);
else:
$pdf->Ln(-15);   
endif;
$pdf->SetFont('Arial','B',8); 
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("MARCA: ".strtoupper($cars->getBrand()->name))); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("BRAND: ".strtoupper($cars->getBrand()->name))); break;
}

$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("FECHA Y HORA: ".date("d-m-Y", strtotime($sell->created_at."- 4 hours")))); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("DATE: ".date("m-d-Y", strtotime($sell->created_at."- 4 hours")))); break;
}

$pdf->setX(78);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,date("h:i a", strtotime($sell->created_at."- 4 hours"))); break;
  case 'EN': $pdf->Cell(5,51,date("H:i ", strtotime($sell->created_at."- 4 hours"))); break;
}


$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor (0, 0, 0);

$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________    _____________');

$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("MODELO: ".$cars->name)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("MODEL: ".$cars->name)); break;
}
$pdf->setX(78);
$pdf->Cell(5,51,strtoupper(utf8_decode("F: ".$cars->token)));
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor (0, 0, 0);

$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________    _____________');

$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("CARRO SERA DEVUELTO EN: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("CAR WILL BE RETURN TO: ")); break;
}

$pdf->Ln(3);
$pdf->SetFont('Arial','B',8);
$pdf->setX(6);
$pdf->SetTextColor (2, 159, 205);
$pdf->Cell(5,51,strtoupper(utf8_decode($sell->place_end)));
$pdf->SetTextColor (0, 0, 0);


$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("PLACA NO.: ".$cars->plate)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("CAR LIC.: ".$cars->plate)); break;
}


$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("NOMBRE: ".$clients->name)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("NAME: ".$clients->name)); break;
}


$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________');



$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("FORMA DE PAGO: ".$sell->getF()->name)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("METHOD OF PAYMENT: ".$sell->getF()->name)); break;
}


$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________');


$pdf->ln(5);
$pdf->setX(6);
$pdf->Cell(5,15,'___________________________________________________________');
$pdf->Ln(-14);
$pdf->setX(6);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("LICENCIA DE CONDUCIR: ".$clients->license)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("DRIVER LICENSE NO: ".$clients->license)); break;
}


$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);

if ($sell->person2_id>0):
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("CONDUCTOR ADICIONAL: ".$clients2->name)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("ADDITIONAL DRIVER: ".$clients2->name)); break;
}
else:
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("CONDUCTOR ADICIONAL: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("ADDITIONAL DRIVER: ")); break;
}    
endif;

$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'____________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);

if ($sell->person2_id>0):
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("LICENCIA DE CONDUCIR: ".$clients2->license)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("DRIVER LICENSE NO: ".$clients2->license)); break;
}
else:
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("LICENCIA DE CONDUCIR: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("DRIVER LICENSE NO: ")); break;
}    
endif;

$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("DIRECCION LOCAL Y TELEFONO: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("LOCAL ADDRESS AND TEL. NO. : ")); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->ln(4);
$pdf->setX(6);
$pdf->SetTextColor (2, 159, 205);
$pdf->Cell(5,51,strtoupper($clients->address));
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor (0, 0, 0);
$pdf->Ln(31);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("HE LEIDO LOS TERMINOS Y CONDICIONES EN AMBOS LADOS DE ESTE CONTRATO DE ARRENDAMIENTO Y FIRMO DE CONFORMIDAD: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("I HAVE READ THE TERMS AND CONDITIONS ON BOTH SIDES THIS SING LEASE AND UNDER: ")); break;
}


$pdf->ln(-7);
$pdf->setX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("DIRECCION CORRECTA EN EL EXTERIOR:")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("BILLING ADDRES:")); break;
}


$pdf->SetFont('Arial','B',8);
$pdf->ln(4);
$pdf->setX(6);
$pdf->SetTextColor (2, 159, 205);
$pdf->Cell(5,51,strtoupper($clients->address2));
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor (0, 0, 0);
$pdf->ln(20);
$pdf->setX(6);
$pdf->Cell(5,15,'___________________________________________________________');

$pdf->Ln(-14);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("TEL.: (ESTADIA): ".$clients->phone)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("TEL.: (STAY): ".$clients->phone)); break;
}

$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'___________________________________');

$pdf->Ln(-14);
$pdf->setX(6);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("TEL.: (EXTRANJERO): ".$clients->phone2)); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("TEL.: (FOREIGN): ".$clients->phone2)); break;
}

$pdf->ln(19);
$pdf->setX(6);
$pdf->Cell(5,15,'___________________________________');

$pdf->Ln(-6);
$pdf->setX(6);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("COMBUTIBLE DE SALIDA: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("OUT FUEL:")); break;
}


$pdf->setX(40);

if($receiver>0):
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("COMBUTIBLE DE ENTRADA: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("INPUT FUEL:")); break;
}

$pdf->SetFont('Arial','B',8);

if($sell->fuel=="R"):
       
      $src = "../CF-SYSTEMS/storage/configuration/FR.png";
    if(file_exists($src)){
        $pdf->Image($src,2,160,30);      
    }
    
    endif;

  if($sell->fuel=="1/4" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/F14.png";
    if(file_exists($src)){
          $pdf->Image($src,2,190,30);    
    }
    
    endif;
    
    if($sell->fuel=="3/4"):
       
      $src = "../CF-SYSTEMS/storage/configuration/F34.png";
    if(file_exists($src)){
           $pdf->Image($src,2,190,30);             
    }
    
    endif;
    
    if($sell->fuel=="1/2" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FM.png";
    if(file_exists($src)){
          $pdf->Image($src,2,190,30);    
    }
    
    endif;
    
    if($sell->fuel=="F" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FF.png";
    if(file_exists($src)){
          $pdf->Image($src,2,190,30);          
    }
    
   endif;


   
if($receiver->fuel=="R"):
       
      $src = "../CF-SYSTEMS/storage/configuration/FR.png";
    if(file_exists($src)){
        $pdf->Image($src,30,160,30);      
    }
    
    endif;

  if($receiver->fuel=="1/4" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/F14.png";
    if(file_exists($src)){
         $pdf->Image($src,106,190,30);  
    }
    
    endif;
    
    if($receiver->fuel=="3/4"):
       
      $src = "../CF-SYSTEMS/storage/configuration/F34.png";
    if(file_exists($src)){
          $pdf->Image($src,106,190,30);     
    }
    
    endif;
    
    if($receiver->fuel=="1/2" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FM.png";
    if(file_exists($src)){
        $pdf->Image($src,106,190,30);         
    }
    
    endif;
    
    if($receiver->fuel=="F" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FF.png";
    if(file_exists($src)){
         $pdf->Image($src,106,190,30);  
    }
    
   endif;



if($cars->getCategory()->name=="Ambulancia"):
$src = "../CF-SYSTEMS/storage/configuration/Ambulancia.jpg";
    if(file_exists($src)){
        $pdf->Image($src,10,220,30);          
    }

endif;
    
if($cars->getCategory()->name=="Jeepeta"):
$src = "../CF-SYSTEMS/storage/configuration/Jeepeta.jpg";
    if(file_exists($src)){
        $pdf->Image($src,10,220,30);          
    }

endif;

if($cars->getCategory()->name=="SUV"):
$src = "../CF-SYSTEMS/storage/configuration/SUV.jpg";
    if(file_exists($src)){
        $pdf->Image($src,10,220,30);          
    }

endif;

if($cars->getCategory()->name=="Pickup"):
$src = "../CF-SYSTEMS/storage/configuration/Camioneta.jpg";
    if(file_exists($src)){
        $pdf->Image($src,10,220,30);          
    }

endif;

if($cars->getCategory()->name=="Carro"):
$src = "../CF-SYSTEMS/storage/configuration/Carro.jpg";
    if(file_exists($src)){
        $pdf->Image($src,10,250,50);           
    }

endif;



if(isset($delivery->danger)):
 $src = '../'.$delivery->danger;
    if(file_exists($src)){
        $pdf->Image($src,10,250,50); 
    }
endif;  




if($cars->getCategory()->name=="Ambulancia"):
$src = "../CF-SYSTEMS/storage/configuration/Ambulancia.jpg";
    if(file_exists($src)){
        $pdf->Image($src,115,220,30);         
    }

endif;
    
if($cars->getCategory()->name=="Jeepeta"):
$src = "../CF-SYSTEMS/storage/configuration/Jeepeta.jpg";
    if(file_exists($src)){
        $pdf->Image($src,115,220,30);         
    }

endif;

if($cars->getCategory()->name=="SUV"):
$src = "../CF-SYSTEMS/storage/configuration/SUV.jpg";
    if(file_exists($src)){
        $pdf->Image($src,115,220,30);         
    }

endif;

if($cars->getCategory()->name=="Pickup"):
$src = "../CF-SYSTEMS/storage/configuration/Camioneta.jpg";
    if(file_exists($src)){
        $pdf->Image($src,115,220,30);         
    }

endif;

if($cars->getCategory()->name=="Carro"):
$src = "../CF-SYSTEMS/storage/configuration/Carro.jpg";
    if(file_exists($src)){
        $pdf->Image($src,140,250,50);          
    }

endif;


if(isset($receiver->danger)):
 $src = '../'.$receiver->danger;
    if(file_exists($src)){
         $pdf->Image($src,140,250,50);     
    }
endif;  


else:

if($cars->getCategory()->name=="Ambulancia"):
$src = "../CF-SYSTEMS/storage/configuration/Ambulancia.jpg";
    if(file_exists($src)){
        $pdf->Image($src,13,248,90);         
    }

endif;
    
if($cars->getCategory()->name=="Jeepeta"):
$src = "../CF-SYSTEMS/storage/configuration/Jeepeta.jpg";
    if(file_exists($src)){
        $pdf->Image($src,13,248,90);        
    }

endif;

if($cars->getCategory()->name=="SUV"):
$src = "../CF-SYSTEMS/storage/configuration/SUV.jpg";
    if(file_exists($src)){
        $pdf->Image($src,13,248,90);         
    }

endif;

if($cars->getCategory()->name=="Pickup"):
$src = "../CF-SYSTEMS/storage/configuration/Camioneta.jpg";
    if(file_exists($src)){
        $pdf->Image($src,13,248,90);         
    }

endif;

if($cars->getCategory()->name=="Carro"):
$src = "../CF-SYSTEMS/storage/configuration/Carro.jpg";
    if(file_exists($src)){
        $pdf->Image($src,13,248,90);            
    }

endif;



if(isset($delivery->danger)):
 $src = '../'.$delivery->danger;
    if(file_exists($src)){
        $pdf->Image($src,13,248,90);  
    }
endif;  

$pdf->SetFont('Arial','B',8);

if($sell->fuel=="R"):
       
      $src = "../CF-SYSTEMS/storage/configuration/FR.png";
    if(file_exists($src)){
        $pdf->Image($src,2,150,50);        
    }
    
    endif;

  if($sell->fuel=="1/4" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/F14.png";
    if(file_exists($src)){
            $pdf->Image($src,2,150,50);       
    }
    
    endif;
    
    if($sell->fuel=="3/4"):
       
      $src = "../CF-SYSTEMS/storage/configuration/F34.png";
    if(file_exists($src)){
           $pdf->Image($src,2,150,50);          
    }
    
    endif;
    
    if($sell->fuel=="1/2" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FM.png";
    if(file_exists($src)){
          $pdf->Image($src,2,150,50);             
    }
    
    endif;
    
    if($sell->fuel=="F" ):
       
      $src = "../CF-SYSTEMS/storage/configuration/FF.png";
    if(file_exists($src)){
          $pdf->Image($src,2,150,50);             
    }
    
   endif;

endif;

$pdf->Ln(-100);
$pdf->setX(102);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("DESDE: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("FROM:")); break;
}


$pdf->setX(168);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,51,strtoupper("HASTA: ")); break;
  case 'EN': $pdf->Cell(5,51,strtoupper("UNTIL:")); break;
}


$pdf->ln(4);
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor (2, 159, 205);
$pdf->setX(102);
$pdf->Cell(5,51,strtoupper(date("d-m-Y h:i:s a",strtotime($sell->start_at))));

$pdf->setX(168);
$pdf->Cell(5,51,strtoupper(date("d-m-Y h:i:s a",strtotime($sell->end_at))));

$pdf->SetTextColor (0, 0, 0);
$pdf->SetFont('Arial','B',8);

$pdf->ln(19);
$pdf->setX(102);
$pdf->Cell(5,15,'_______________________________________________________________');
$pdf->ln(4);
$pdf->setX(102);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,strtoupper("PRECIO POR DIA: ".$sell->price." ".StockData::getPrincipal()->currency)); break;
  case 'EN': $pdf->Cell(5,15,strtoupper("PRICE PER DAY: ".$sell->price." ".StockData::getPrincipal()->currency)); break;
}


$pdf->ln(1);
$pdf->setX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->ln(4);

$pdf->setX(102);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,strtoupper("TOTAL DE DIAS: ".$sell->day)); break;
  case 'EN': $pdf->Cell(5,15,strtoupper("TOTAL OF DAYS: ".$sell->day)); break;
}


$pdf->ln(1);
$pdf->setX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->ln(4);
$pdf->setX(102);
$pdf->Cell(5,15,'COLOR: ');


$pdf->SetFont('Arial','B',8);

$pdf->setX(114);
$pdf->SetTextColor (2, 159, 205);
$pdf->Cell(5,15,strtoupper($cars->getExColor()->name));
$pdf->SetTextColor (0, 0, 0);
$pdf->SetFont('Arial','B',8);
$pdf->ln(1);
$pdf->setX(102);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->SetFont('Arial','B',8); 
$pdf->ln(4);
$pdf->setX(102);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,strtoupper("KM O MILLAS EN SALIDA: ".$cars->kms)); break;
  case 'EN': $pdf->Cell(5,15,strtoupper("KM OR MILES ON DEPARTURE: ".$cars->kms)); break;
}
if ($sell->car_id>0 and $receiver>0):
$pdf->setX(160);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'KM O MILLAS EN LLEGADA: '.$receiver->kms); break;
  case 'EN': $pdf->Cell(5,15,'KM OR MILES ON ARRIVAL: '.$receiver->kms); break;
}

endif;


$pdf->ln(1);
$pdf->setX(102);
$pdf->Cell(5,15,'_______________________________________________________________');


$pdf->SetFont('Arial','B',8); 
if ($sell->car_id>0 and $receiver>0):
$pdf->ln(4);
$pdf->setX(102);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'KM O MILLAS TOTAL: '.($receiver->kms-$delivery->kms)); break;
  case 'EN': $pdf->Cell(5,15,'KM OR MILES TOTAL: '.($receiver->kms-$delivery->kms)); break;
}
else:
$pdf->ln(4);
$pdf->setX(102);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'KM O MILLAS TOTAL:'); break;
  case 'EN': $pdf->Cell(5,15,'KM OR MILES TOTAL:'); break;
}
endif;


$pdf->ln(1);
$pdf->setX(102);
$pdf->Cell(5,15,'_______________________________________________________________');


$pdf->ln(10);
$pdf->setX(102);

switch ($clients->language){
  case 'ES': $pdf->MultiCell(35,4,"LA RESPONSABILIDAD DEDUCIBLE HACIA LOS CLIENTES EN CASO DE ACCIDENTE:", 0, 'C'); break;
  case 'EN': $pdf->MultiCell(35,4,"THE DEDUCTIBLE RESPONSIBILITY TO THE COSTUMER IN CASE OF AN ACCIDENT:", 0, 'C'); break;
}

$pdf->ln(-26);
$pdf->SetTextColor (2, 159, 205);
$sure = SureData::getById($sell->type_sure);
$pdf->ln(4);
$pdf->setX(140);
$pdf->Cell(5,15,$sure->name." :");

$pdf->setX(181);
$pdf->Cell(5,15,$sell->sure." ".StockData::getPrincipal()->currency);
$pdf->SetTextColor (0, 0, 0);
$pdf->ln(1);
$pdf->setX(140);
$pdf->Cell(5,15,'________________________    _____________');




$pdf->ln(9);
$pdf->setX(140);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'ELEGIDO:'); break;
  case 'EN': $pdf->Cell(5,15,'ELECTED:'); break;
}

$pdf->setX(170);
if ($sell->sure>0):
    
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'[SI]:'); break;
  case 'EN': $pdf->Cell(5,15,'[YEAH]:'); break;
}

else:
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'[NO]:'); break;
  case 'EN': $pdf->Cell(5,15,'[NOT]:'); break;
}
endif;



$pdf->ln(10);
$pdf->setX(102);
$pdf->Cell(5,15,'_______________________________________________________________');



$pdf->ln(4);
$pdf->setX(140);
$pdf->SetTextColor (0, 0, 0);

$pdf->Cell(5,15,'SUBTOTAL:');
$pdf->setX(180);
$pdf->SetTextColor (2, 159, 205);
$pdf->Cell(5,15,$sell->total-$sell->value_iva." ".StockData::getPrincipal()->currency);


$pdf->SetTextColor (0, 0, 0);
$pdf->ln(1);
$pdf->setX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->ln(4);
$pdf->setX(140);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'IMPUESTO 18%:'); break;
  case 'EN': $pdf->Cell(5,15,'TAXES 18%:'); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor (2, 159, 205);

$pdf->setX(180);
$pdf->Cell(5,15, number_format($sell->value_iva,2,".",",")." ".StockData::getPrincipal()->currency);

$pdf->SetTextColor (0, 0, 0);
$pdf->SetFont('Arial','B',8);

$pdf->ln(2);
$pdf->setX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->ln(4);

$pdf->setX(140);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'OTROS COBROS:'); break;
  case 'EN': $pdf->Cell(5,15,'OTHER CHARGES:'); break;
}

$pdf->SetFont('Arial','B',8);

$pdf->SetTextColor (2, 159, 205);
if($sell->plane>0):
$pdf->setX(140);
$pdf->Cell(5,15, number_format($sell->plane,2,".",",")." ".StockData::getPrincipal()->currency);
endif;
$pdf->SetTextColor (0, 0, 0);
$pdf->SetFont('Arial','B',8);

$pdf->ln(1.5);
$pdf->setX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->ln(4);
$pdf->setX(140);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'CARGOS TOTALES:'); break;
  case 'EN': $pdf->Cell(5,15,'TOTAL CHARGES:'); break;
}

$pdf->SetTextColor (2, 159, 205);
$pdf->setX(180);
$pdf->Cell(5,15, number_format($sell->total+$plane_calc,2,".",",")." ".StockData::getPrincipal()->currency);

$pdf->SetFont('Arial','B',8);

$pdf->SetTextColor (0, 0, 0);
$pdf->ln(1);
$pdf->setX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$totpayments = 0;
$payments = PaymentData::getByPayment($sell->id);
$totpayments = $payments[0]->t!=null?$payments[0]->t:0;

$pdf->ln(4);
$pdf->setX(140);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'MONTO ABONADO:'); break;
  case 'EN': $pdf->Cell(5,15,'AMOUNT PAID:'); break;
}

$pdf->SetTextColor (2, 159, 205);
$pdf->setX(180);
$pdf->Cell(5,15,$totpayments." ".StockData::getPrincipal()->currency);

$pdf->SetTextColor (0, 0, 0);
$pdf->ln(2);
$pdf->setX(140);
$pdf->Cell(5,15,'_______________________    ______________');


$pdf->ln(4);
$pdf->setX(140);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'CARGO PENDIENTE:'); break;
  case 'EN': $pdf->Cell(5,15,'PENDING CHARGE:'); break;
}

$pdf->SetTextColor (2, 159, 205);
$pdf->SetFont('Arial','B',8);
$pdf->setX(180);
$pdf->Cell(5,15, number_format(($sell->total+$sell->plane)-$totpayments,2,".",",")." ".StockData::getPrincipal()->currency);

$pdf->SetTextColor (0, 0, 0);
$pdf->ln(1);
$pdf->setX(140);
$pdf->Cell(5,15,'_______________________    ______________');

$pdf->ln(15);
$pdf->setX(70);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'PREPARADO POR:'); break;
  case 'EN': $pdf->Cell(5,15,'PREPARED BY:'); break;
}

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor (2, 159, 205);
$pdf->setX(120);
$pdf->Cell(5,15,strtoupper($sell->getUser()->name." ".$sell->getUser()->lastname));

$pdf->SetTextColor (0, 0, 0);

$pdf->setX(140);
$pdf->Cell(5,15,'');

$pdf->SetFont('Arial','B',8);
$pdf->ln(1);
$pdf->setX(70);
$pdf->Cell(5,15,'_____________________________     ____________________________________________________');


$pdf->ln(7);
$pdf->setX(70);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'OBSERVACION:'); break;
  case 'EN': $pdf->Cell(5,15,'OBSERVATION:'); break;
}

$pdf->ln(10);

$pdf->setX(70);
$pdf->MultiCell(65,4,$sell->comment, 0, 'L');

if (count($receiver)>0):
$pdf->ln(70);
$pdf->setX(10);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('DAÑO ENTREGADO AL CLIENTE:')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('DAMAGE DELIVERED TO THE CUSTOMER:')); break;
}

$pdf->setX(135);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,utf8_decode('DAÑO RECIBIDO DEL CLIENTE:')); break;
  case 'EN': $pdf->Cell(5,15,utf8_decode('DAMAGE RECEIVED FROM CUSTOMER:')); break;
}
endif;
if($delivery>0):



$pdf->ln(20);
$pdf->setX(70);
$pdf->Cell(5,15,'__________________________________                 __________________________________');

$pdf->ln(4);
$pdf->setX(70);
$pdf->Cell(5,15,strtoupper(utf8_decode($user->name." ".$user->lastname)).'                                                              '.strtoupper(utf8_decode($clients->name)).'');
$pdf->ln(4);
$pdf->setX(70);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'ARRENDADOR/A:                                                        ARRENDATARIO/A:'); break;
  case 'EN': $pdf->Cell(5,15,'LESSOR:                                                                     LESSEE:'); break;
}




if($receiver>0):
    
if(isset($user_delivery->firma)):
 $src = '../'.$user_delivery->firma;
    if(file_exists($src)){
        $pdf->Image($src,70,205,30);    
    }
endif;  

if(isset($user_receiver->firma)):
 $src = '../'.$user_receiver->firma;
    if(file_exists($src)){
        $pdf->Image($src,150,205,30);  
    }
endif;  

if(isset($user->firma)):
 $srcx = '../'.$user->firma;
    if(file_exists($srcx)){
        $pdf->Image($src,90,170,30);      
    }
endif;

if(isset($delivery->firma)):
 $src = '../'.$delivery->firma;
    if(file_exists($src)){
        $pdf->Image($src,150,170,30);    
    }
endif;   
    
$pdf->ln(23);
$pdf->setX(60);
$pdf->Cell(5,15,'__________________________________                                         __________________________________');

$pdf->ln(4);
$pdf->setX(60);


switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'ENTREGADOR/A:                                                                                      RECIBIDOR/A:'); break;
  case 'EN': $pdf->Cell(5,15,'DELIVERY:                                                                                          RECEIVER:'); break;
}

$pdf->ln(4);
$pdf->setX(60);
$pdf->Cell(5,15,strtoupper($user_delivery->name." ".$user_delivery->lastname).'                                                                                      '.strtoupper($user_receiver->name." ".$user_receiver->lastname));

else:
    
if(isset($user_delivery->firma)):
 $src = '../'.$user_delivery->firma;
    if(file_exists($src)){
        $pdf->Image($src,85,198,30);    
    }
endif;  

if(isset($user->firma)):
 $srcx = '../'.$user->firma;
    if(file_exists($srcx)){
        $pdf->Image($srcx,85,172,30);      
    }
endif;

if(isset($delivery->firma)):
 $src = '../'.$delivery->firma;
    if(file_exists($src)){
        $pdf->Image($src,160,165,30);    
    }
endif;   


$pdf->ln(18);
$pdf->setX(70);
$pdf->Cell(5,15,'__________________________________');


$pdf->ln(6);
$pdf->setX(70);

switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'ENTREGADOR/A: '.strtoupper($user_delivery->name." ".$user_delivery->lastname)); break;
  case 'EN': $pdf->Cell(5,15,'DELIVERY: '.strtoupper($user_delivery->name." ".$user_delivery->lastname)); break;
}
   
endif;

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
else:
  

$pdf->ln(15);
$pdf->setX(65);
$pdf->Cell(5,15,'__________________________________');

$pdf->ln(6);
$pdf->setX(80);
$pdf->Cell(5,15,strtoupper(strtoupper($clients->name)));
$pdf->ln(6);
$pdf->setX(80);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,'ARRENDATARIO/A'); break;
  case 'EN': $pdf->Cell(5,15,'TENANT'); break;
}


if(isset($user->firma)):
 $srcx = '../'.$user->firma;
    if(file_exists($srcx)){
        $pdf->Image($srcx,85,215,30);      
    }
endif; 
    

endif;
    
if(StockData::getPrincipal()->notario>0):
$pdf->ln(18);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->MultiCell(197,4,utf8_decode("Yo, ".StockData::getPrincipal()->notario.", Abogado/a Notario Público. CERTIFICO Y DOY FE: Que por ante mí han comparecido los señores ".$user->name.", en representación de (".$title."), quienes me manifestaron bajo la fe del juramento, que es así como acostumbran a firmar todos los actos de su vida pública y privada. En la ciudad de ".$sell->getLocation()->name.", República Dominicana, a los ".date("d",strtotime($sell->start_at))." días del mes ".date("m",strtotime($sell->start_at))." del año ".date("Y",strtotime($sell->start_at))."."), 0, 'J'); break;
  
  case 'EN': $pdf->MultiCell(197,4,utf8_decode("Me, ".StockData::getPrincipal()->notary.", Lawyer/Notary Public. I CERTIFY AND FAITH: That the gentlemen ".$user->name.", have appeared before me, representing (".$title."), who declared to me under the faith of the oath, that this is how they usually sign all acts of your public and private life. In the city of ".$sell->getLocation()->name.", Dominican Republic, at ".date("d",strtotime($sell->start_at))." days of the month ".date("m",strtotime($sell->start_at))." of the year ".date("Y",strtotime($sell->start_at))."."), 0, 'J'); break;
}


$pdf->ln(12);
$pdf->setX(6);
$pdf->MultiCell(197,4,utf8_decode("______________________________________________
Notario Público"), 0, 'C');
endif; 

////////////////////////////////////////////////////////////////////////////////////////////////////
  
$pdf->AddPage();

$pdf->SetFont('Arial','B',10);
$pdf->setX(6);
switch ($clients->language){
  case 'ES': $pdf->MultiCell(180,6,"CONTRATO", 0, 'C'); break;
  
  case 'EN': $pdf->MultiCell(180,6,"CONTRACT", 0, 'C'); break;
}

$pdf->SetFont('Arial','I',6.6);
$pdf->setX(6);

switch ($clients->language){
  case 'ES': $pdf->MultiCell(197,4,utf8_decode("Entre ". $title .", Compañia organizada con apego a las leyes dominicanas, con su domicilio social en ". StockData::getPrincipal()->address .", denominada en lo adelante como La Arrendadora y la persona que figura descrita en la casilla No. 1 del presente documento quien en lo adelante será denominada cómo el Arrendatario, se ha pactado el siguiente contrato.

1. La Arrendadora da en alquiler o arrendamiento a el Arrendatario que acepta, el vehículo que describe en el dorso, bajo las condiciones que en dicho lugar se señalan, reconociendo haberlo recibido a satisfacción en perfecto funcionamiento.

2- El Arrendatario se compromete a pagar a la Arrendadora:
    A) El precio del alquiler y cualquier cargo aplicable a este contrato hasta la entrega de vehiculo, en el domicilio de la Arrendadora o del Arrendatario.
    B) Cualquier daño o desperfecto sufrido por el vehículo durante la vigencia del contrato.
    C) Los gastos en que deba incurrir La Arrendadora para recuperar el vehiculo en caso de que no haya sido entregado en el domicilio de La Arrendadora o del Arrendatario, fuera del área establecida para estos fines.
    D) Todos los gastos judiciales y extrajudiciales, multas surgidas de infracciones de tránsito o cualesquiera otros dirigidos contra el vehículo. El Arrendatario o la Arrendadora durante la vigencia del contrato, hasta la entrega o recuperación del vehículo.
      
3- El Arrendatario no deberá manejar el vehículo en las circunstancias siguientes:
    A) En estado de embriagues o bajo la influencia de estupefacientes.
    B) En pruebas de velocidad o competencias.
    C) En violación a los reglamentos de tránsito y especificamente, a las disposiciones legales propias o inherentes al tipo de vehículo, objeto de este contrato.
    D) En actividades ilícitas.
    E) Remolcando otro vehículo.
    F) Transportando más pasajeros de lo establecido para la clase de vehículo alquilado, y mucho menos cobrando pasaje y flete, jamás podrá sacar el vehiculo del territorio nacional, a menos que la arrendadora lo autorice por escrito.
      
4- La Arrendadora no es responsable de riesgos que excedan los límites de la póliza del seguro que ampara el vehículo alquilado. Cualquier indemización que rebase tales límites estará a cargo del Arrendatario, quien tendrá también la responsabilidad de los daños que experimenten personas y propiedades que él transporte, a cualquier título en el vehículo alquilado.

El vehículo alquilado será conducido únicamente por el Arrendatario, a menos que la Arrendadora autorice expresamente por escrito lo contrario, debiendo proveer la información requerida para el (los) conductor(es) adicional(es) se obligan solidamente con el Arrendatario a cumplir con todas las cláusulas de este contrato renunciando a los beneficios de exclusión.

5- Si el Arrendatario acepta las condiciones y los términos de una póliza adicional de daños al vehículo alquilado, constituida por la Arrendadora la cual va a cuenta del Arrendatario, no será responsable de perjuicios y daños que ocasione al vehiculo alquilado, excepto por el deducible estipulado en la casilla. No. 31, que deberá pagar. En caso de no contraer este seguro, el Arrendatario será responsable de la totalidad de los daños ocasionados al vehículo alquilado.

6- En caso de accidente, el arrendatario se compromete a:
    A) Solicitar la intervención de las autoridades de tránsito.
    B) A dar un informe completo del accidente a la Arrendadora.
    C) No pactar ningún arreglo con los terceros, ni aceptar culpabilidad del mismo.
    D) Colaborar con la Arrendadora y la compañía de seguros en la investigación del accidente.
    E) Responsabilizarse de los gastos incurridos para la recuperación del vehículo, incluyendo gastos de grúa, aún cuando el valor en daños del vehículo exceda al valor del deducible aceptado.
      
7- Si el Arrendatario no entrega el vehículo a la Arrendadora el día y la hora acordada en este contrato podrá ser objeto de persecución judicial y al pago de las indemnizaciones procedentes.

8- El Arrendatario esta obligado a pagar a la Arrendadora el monto total de este contrato, de no hacerlo la Arrendadora podrá exigir el pago por vía judicial, cargándole el pago de intereses moratorios, así como los costos judiciales. Además en caso de que el Arrendatario no pague la suma adeudada por el alquiler del vehículo, salvo convención exprese en este contrato, la Arrendadorra hará uso de las disposiciones de la ley #13 de Diciembre del 1978. Para garantizar el cumplimiento y las obligaciones contraídas en este contrato, el Arrendatario y/o conductor adicional dejan en garantía los depósitos siguientes:

    A) Comprobante de cargo (boucher), firmado en blanco correspondiente a tarjeta de crédito, o cheques, con la cual autoriza a la Arrendadora llenarlo y cobrarlo en cualquier momento y sin previo aviso, por los valores que la Arrendadora indique, a cargo del Arrendatario, de conformidad con las cláusulas del presente contrato.
    B) La Arrendadora no se hará responsable sobre documentos o valores dejados como depósitos en caso de robo, asalto, incendio u otros accidentes.
      
9- A la conclusión del contrato, o en caso de que el Arrendatario abandone voluntariamente el vehículo alquilado, en el caso de que la Arrendadora aún durante la vigencia del contrato, requiera la entrega del vehículo alquilado, la Arrendadora podrá entrar en posesión del vehículo, bien fuese para comprobar su estado y/o en caso de que el Arrendatario violase la claúsula que le prohibe que otra persona distinta de el, maneje el vehículo alquilado, la Arrendadora podrá entrar en posesión del vehículo descrito en este contrato en cualesquiera manos que se encentre, ya que una de las circunstancias anteriores se considera causa de terminación automática del presente contrato, sin responsabilidad alguna a cargo de la Arrendadora, sin importar el lugar donde se encuentre depositado el vehículo.

10- El Arrendatario reconoce que todos sus bienes habidos y por haber, quedan afectados al fiel cumplimiento de pagos de las obligaciones derivadas del presente contrato, renunciando al fuero del domicilio y cualquier otra ley que le pudiere favorecer.

HECHO Y FIRMADO EN FECHA YA PACTADA AL DORSO DEL CONTRATO, CON COPIAS Y ORIGINALES UNA PARA CADA UNA DE LAS PARTES, EN ". StockData::getPrincipal()->address .".
"), 0, 'J');
 break;
  
  case 'EN': $pdf->MultiCell(197,4,utf8_decode("Enter ". $title .", Company organized in accordance with Dominican laws, with its registered office at ". StockData::getPrincipal()->address .", hereinafter referred to as The Lessor and the person described in box No. 1 of this document, who from now on will be called the Lessee, the following contract has been agreed.

1. The Lessor gives for rent or lease to the accepting Lessee, the vehicle described on the back, under the conditions indicated therein, acknowledging having received it to satisfaction in perfect working order.

2- The Lessee agrees to pay the Lessor:
    A) The rental price and any charge applicable to this contract until the delivery of the vehicle, at the address of the Lessor or the Lessee.
    B) Any damage or damage suffered by the vehicle during the term of the contract.
    C) The expenses that The Lessor must incur to recover the vehicle in the event that it has not been delivered to the address of The Lessor or the Lessee, outside the area established for these purposes.
    D) All judicial and extrajudicial expenses, fines arising from traffic violations or any others directed against the vehicle. The Lessee or the Lessor during the term of the contract, until the delivery or recovery of the vehicle.
      
3- The Lessee must not drive the vehicle in the following circumstances:
    A) In a state of intoxication or under the influence of narcotics.
    B) In speed tests or competitions.
    C) In violation of traffic regulations and specifically, the legal provisions inherent to or inherent to the type of vehicle, the object of this contract.
    D) In illegal activities.
    E) Towing another vehicle.
    F) Transporting more passengers than what is established for the class of rented vehicle, much less charging fare and freight, you will never be able to take the vehicle out of the national territory, unless the lessor authorizes it in writing.
      
4- The Lessor is not responsible for risks that exceed the limits of the insurance policy that covers the rented vehicle. Any compensation that exceeds such limits will be the responsibility of the Lessee, who will also be responsible for any damage suffered by people and property that he transports, in any capacity in the rented vehicle.

The rented vehicle will be driven solely by the Lessee, unless the Lessor expressly authorizes otherwise in writing, and must provide the information required for the additional driver(s) to be jointly obligated with the Lessee to comply with all the clauses of this contract waiving the benefits of exclusion.

5- If the Lessee accepts the conditions and terms of an additional policy for damage to the rented vehicle, established by the Lessor which is at the Lessee's expense, he will not be responsible for damages caused to the rented vehicle, except for the deductible. stipulated in the box. No. 31, which you must pay. If this insurance is not contracted, the Lessee will be responsible for all damages caused to the rented vehicle.

6- In the event of an accident, the lessee undertakes to:
    A) Request the intervention of the traffic authorities.
    B) To give a complete report of the accident to the Lessor.
    C) Do not agree to any settlement with third parties, nor accept their guilt.
    D) Collaborate with the Lessor and the insurance company in the investigation of the accident.
    E) Be responsible for the expenses incurred to recover the vehicle, including towing expenses, even when the damage value of the vehicle exceeds the value of the accepted deductible.
      
7- If the Lessee does not deliver the vehicle to the Lessor on the day and time agreed in this contract, they may be subject to judicial prosecution and payment of the appropriate compensation.

8- The Lessee is obliged to pay the Lessor the total amount of this contract; if the Lessor does not do so, the Lessor may demand payment through court, charging the payment of late payment interest, as well as legal costs. Furthermore, in the event that the Lessee does not pay the amount owed for the rental of the vehicle, unless otherwise expressly agreed in this contract, the Lessor will make use of the provisions of Law #13 of December 1978. To guarantee compliance and the obligations contracted In this contract, the Lessee and/or additional driver leave the following deposits as guarantee:

    A) Proof of charge (boucher), signed in blank corresponding to a credit card, or checks, with which you authorize the Lessor to fill it out and collect it at any time and without prior notice, for the values that the Lessor indicates, at the expense of the Lessee, in accordance with the clauses of this contract.
    B) The Lessor will not be responsible for documents or values left as deposits in case of theft, assault, fire or other accidents.
    
9- At the conclusion of the contract, or in the event that the Lessee voluntarily abandons the rented vehicle, in the event that the Lessor, even during the validity of the contract, requires the delivery of the rented vehicle, the Lessor may enter into possession of the vehicle, either was to check its condition and/or in the event that the Lessee violates the clause that prohibits another person other than him from driving the rented vehicle, the Lessor may take possession of the vehicle described in this contract in any hands that may be found, since one of the above circumstances is considered cause for automatic termination of this contract, without any responsibility borne by the Lessor, regardless of the place where the vehicle is stored.

10- The Lessee acknowledges that all his assets, existing and to be acquired, are affected by the faithful fulfillment of payments of the obligations derived from this contract, waiving the jurisdiction of the domicile and any other law that may favor him.

DONE AND SIGNED ON THE DATE ALREADY AGREED ON THE BACK OF THE CONTRACT, WITH COPIES AND ORIGINALS ONE FOR EACH OF THE PARTIES, IN ". StockData::getPrincipal()->address ."."), 0, 'J');
 break;
}

$pdf->output();
