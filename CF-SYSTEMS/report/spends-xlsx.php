<?php

/** Error reporting */
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SpendData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/DData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ConfigurationData.php";
$symbol = ConfigurationData::getByPreffix("currency")->val;

/** Include PHPExcel */
include "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$objPHPExcel = new Spreadsheet();
$products = SpendData::getAllUnBoxed();

// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Gastos')
->setCellValue('A2', 'Tipo')
->setCellValue('B2', 'Concepto')
->setCellValue('C2', 'Costo')
->setCellValue('D2', 'Fecha');

$start = 3;
foreach($products as $product){
if($product->kind==1){
$sheet->setCellValue('A'.$start, 'Gastos')
->setCellValue('B'.$start, $product->name)
->setCellValue('C'.$start, $symbol." ".number_format($product->price,2,".",","))
->setCellValue('D'.$start, $product->created_at);
$start++;
} 

else if($product->kind==2){
$sheet->setCellValue('A'.$start, 'Devolucion')
->setCellValue('B'.$start, $product->name)
->setCellValue('C'.$start, $symbol." ".number_format($product->price,2,".",","))
->setCellValue('D'.$start, $product->created_at);
$start++;
}

}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
////////////////////////////////////////////////////
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="spends-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//////////////////////////////////////////////////////
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');