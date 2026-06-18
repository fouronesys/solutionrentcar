<?php

/** Error reporting */
error_reporting(0);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PaymentData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PaymentTypeData.php";

/** Include PHPExcel */

include "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$objPHPExcel = new Spreadsheet();

$client = PersonData::getById($_GET["id"]);
$products = PaymentData::getAllByClientId($client->id);
$total = PaymentData::sumByClientId($client->id)->total;



// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Historial de pagos ')
->setCellValue('A2', 'Proveedor: '.$client->name." ".$client->lastname)
->setCellValue('A4', 'Tipo')
->setCellValue('B4', 'Valor')
->setCellValue('C4', 'Fecha')
->setCellValue('F4', 'Saldo Pendiente');

$start = 5;
foreach($products as $product){
$sheet->setCellValue('A'.$start, $product->getPaymentType()->name)
->setCellValue('B'.$start, "$". $product->val)
->setCellValue('C'.$start, $product->created_at);
 $total-=$product->val;
$pd=$total-=$product->val;
$start++;
}
$sheet->setCellValue('F'.$start, $symbol." ".number_format($pd,2,".",","));
$start++;

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
////////////////////////////////////////////////////
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="paymentrhistory-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//////////////////////////////////////////////////////
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');