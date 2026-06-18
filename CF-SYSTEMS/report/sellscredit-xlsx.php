<?php

/** Error reporting */
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/DData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PaymentData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ConfigurationData.php";
$symbol = ConfigurationData::getByPreffix("currency")->val;

/** Include PHPExcel */
include "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$objPHPExcel = new Spreadsheet();
$products = SellData::getCredits();

// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Ventas a Credito')
->setCellValue('A2', 'Id')
->setCellValue('B2', 'Pago')
->setCellValue('C2', 'Entrega')
->setCellValue('D2', 'Total')
->setCellValue('E2', 'Pendiente')
->setCellValue('F2', 'Fecha');

$start = 3;
foreach($products as $product){
$creditsum= 0;
  $tx = PaymentData::sumBySellId($product->id)->total;

if($tx>=0){
//$credit_array[] = array("sell_id"=>$sell->id,"total"=>$tx);
  $creditsum=$tx;
}
$sheet->setCellValue('A'.$start, $product->id)
->setCellValue('B'.$start, $product->getP()->name)
->setCellValue('C'.$start, $product->getD()->name)
->setCellValue('D'.$start, $symbol." ".number_format($creditsum,2,".",","))
->setCellValue('E'.$start, $symbol." ".number_format($product->total,2,".",","))
->setCellValue('F'.$start, $product->created_at);
$start++;
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
////////////////////////////////////////////////////
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="sellscredit-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//////////////////////////////////////////////////////
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');