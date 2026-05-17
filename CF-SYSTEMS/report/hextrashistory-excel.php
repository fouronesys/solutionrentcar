<?php

/** Error reporting */
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/HEData.php";


/** Include PHPExcel */

include "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$objPHPExcel = new Spreadsheet();

$client = PersonData::getById($_GET["id"]);
$products = HEData::getAll();

// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Historial de Horas Extras ')
->setCellValue('A2', 'Cliente: '.$client->name." ".$client->lastname)
->setCellValue('A4', 'Puesto')
->setCellValue('B4', 'Horas')
->setCellValue('C4', 'Fecha');

$start = 5;
foreach($products as $product){
$sheet->setCellValue('A'.$start, $product->loads)
->setCellValue('B'.$start, $product->payment)
->setCellValue('C'.$start, $product->created_at);
$start++;
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
////////////////////////////////////////////////////
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="hextrashistory-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//////////////////////////////////////////////////////
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');