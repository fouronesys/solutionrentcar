<?php

/** Error reporting */
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/IncomeData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ConfigurationData.php";
$symbol = ConfigurationData::getByPreffix("currency")->val;

/** Include PHPExcel */
include "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$objPHPExcel = new Spreadsheet();
$products = IncomeData::getAll();

// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Ingresos')
->setCellValue('A2', 'Concepto')
->setCellValue('B2', 'Costo')
->setCellValue('C2', 'Fecha');

$start = 3;
foreach($products as $product){

$sheet->setCellValue('A'.$start, $product->name)
->setCellValue('B'.$start, $symbol." ".number_format($product->price,2,".",","))
->setCellValue('C'.$start, $product->created_at);
$start++;

}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
////////////////////////////////////////////////////
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="income-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//////////////////////////////////////////////////////
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');