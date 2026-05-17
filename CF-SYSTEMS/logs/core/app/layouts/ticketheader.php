<?php 

$title =StockData::getPrincipal()->name;
$iva_val = StockData::getPrincipal()->imp_val;
$ticket_image = StockData::getPrincipal()->ticket_image;
$ticket_image2 = StockData::getPrincipal()->ticket_image2;
$rnc = StockData::getPrincipal()->rnc;


$symbol = StockData::getPrincipal()->currency;
$iva_name = StockData::getPrincipal()->imp_name;
$divisa = StockData::getPrincipal()->divisa;
$logo_val = explode(",", StockData::getPrincipal()->logo_val);

if($ticket_image<>""){
   $src = "../CF-SYSTEMS/storage/configuration/".$ticket_image;
    if(file_exists($src)){
        $pdf->Image($src,10,10,30);   
    }
}


$pdf->setY(2);

$pdf->Ln(-15);
$pdf->SetFont('Arial','B',14);    //Letra Arial, negrita (Bold), 
$pdf->setX(55);
$pdf->Cell(5,51,strtoupper($title));


$pdf->Ln(13);
$pdf->setX(140);
switch ($clients->language){
  case 'ES': $pdf->Cell(5,15,"FACTURA: ".substr(str_repeat(0, 5).$sell->id, - 5)); break;
  case 'EN': $pdf->Cell(5,15,"BILL: ".substr(str_repeat(0, 5).$sell->id, - 5)); break;
}

$pdf->Ln(18);
$pdf->SetFont('Arial','B',9);    //Letra Arial, negrita (Bold), 
$pdf->setX(55);
$pdf->MultiCell(160,3.5,mb_strtoupper(utf8_decode($stock->address), 'ISO-8859-1'),0,'L');
$pdf->Ln(-4);


$pdf->setX(64);
$pdf->Cell(5,15,$pdf->Image('../CF-SYSTEMS/storage/redes-sociales/telefono.png',56, $pdf->GetY()+6,3)."".$pdf->Image('../CF-SYSTEMS/storage/redes-sociales/whatsapp.png', 60, $pdf->GetY()+6,3).": ".strtoupper($stock->phone."; ".$stock->phone2)); 

$pdf->Ln(5);
$pdf->setX(60);
$pdf->Cell(5,15,$pdf->Image('../CF-SYSTEMS/storage/redes-sociales/instagram.png', 56, $pdf->GetY()+6,3).": ".strtoupper($stock->field2));

$pdf->Ln(-18);
$pdf->setX(100);
$pdf->Cell(5,51,strtoupper("RNC: ".$rnc));

$pdf->Ln(25);
