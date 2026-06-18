<?php 

$title = StockData::getPrincipal()->name;
$iva_val = StockData::getPrincipal()->imp_val;
$ticket_image = StockData::getPrincipal()->ticket_image;
$ticket_image2 = StockData::getPrincipal()->ticket_image2;
$rnc = StockData::getPrincipal()->rnc;

$symbol = StockData::getPrincipal()->currency;
$iva_name = StockData::getPrincipal()->imp_name;
$divisa = StockData::getPrincipal()->divisa;
$logo_val = explode(",", StockData::getPrincipal()->logo_val ?? "0,0,0");

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

if (!empty($ticket_image)) {
    $src = "../CF-SYSTEMS/storage/configuration/" . trim($ticket_image);
    ponerImagenSegura($pdf, $src, 10, 10, 30);
}

$pdf->setY(2);

$pdf->Ln(-15);
$pdf->SetFont('Arial','B',14);
$pdf->setX(55);
$pdf->Cell(5,51,strtoupper($title));

$pdf->Ln(13);
$pdf->setX(140);
switch ($clients->language ?? "ES"){
    case 'ES':
        $pdf->Cell(5,15,"FACTURA: ".substr(str_repeat(0, 5).$sell->id, -5));
        break;
    case 'EN':
        $pdf->Cell(5,15,"BILL: ".substr(str_repeat(0, 5).$sell->id, -5));
        break;
    default:
        $pdf->Cell(5,15,"FACTURA: ".substr(str_repeat(0, 5).$sell->id, -5));
        break;
}

$pdf->Ln(18);
$pdf->SetFont('Arial','B',9);
$pdf->setX(55);
$pdf->MultiCell(160,3.5,mb_strtoupper(utf8_decode($stock->address), 'ISO-8859-1'),0,'L');
$pdf->Ln(-4);

ponerImagenSegura($pdf, '../CF-SYSTEMS/storage/redes-sociales/telefono.png', 56, $pdf->GetY()+6, 3);
ponerImagenSegura($pdf, '../CF-SYSTEMS/storage/redes-sociales/whatsapp.png', 60, $pdf->GetY()+6, 3);

$pdf->setX(64);
$pdf->Cell(5,15,": ".strtoupper($stock->phone."; ".$stock->phone2));

$pdf->Ln(5);

ponerImagenSegura($pdf, '../CF-SYSTEMS/storage/redes-sociales/instagram.png', 56, $pdf->GetY()+6, 3);

$pdf->setX(60);
$pdf->Cell(5,15,": ".strtoupper($stock->field2));

$pdf->setX(120);
$pdf->Cell(5,5,strtoupper("RNC: ".$rnc));

$pdf->Ln(-18);

if (($sell->type_iva ?? 0) > 0):
    $c_id = CData::getById($sell->type_iva);

    if ($c_id):
        $pdf->setX(100);
        $pdf->Cell(5,51,strtoupper(($c_id->name ?? "")." : ".($sell->number_iva ?? "")));

        $pdf->setX(160);
        $pdf->Cell(5,51,strtoupper("VENCE: ".($c_id->expiration ?? "")));
    endif;
endif;

$pdf->Ln(25);