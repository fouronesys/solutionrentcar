<?php

/** Error reporting */
error_reporting(0);
include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CategoryData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationTypeData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/StockData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ConfigurationData.php";

$symbol = ConfigurationData::getByPreffix("currency")->val;
$title = ConfigurationData::getByPreffix("ticket_title")->val;
$iva_val = ConfigurationData::getByPreffix("imp-val")->val;

/** Include PHPExcel */
include "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$objPHPExcel = new Spreadsheet();
$product = ProductData::getById($_GET["id"]);
$stock = StockData::getById($_GET["stock_id"]);
$operations = OperationData::getAllByProductId($product->id);

$entradas = OperationData::GetInputQByStock($product->id,$stock->id);
$disponibles = OperationData::GetQByStock($product->id,$stock->id);
$salidas = $entradas-$disponibles;



// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Historial del Producto')
->setCellValue('A2', 'Entrada')
->setCellValue('B2', 'Disponible')
->setCellValue('C2', 'Salidas')
->setCellValue('E2', 'Cantidad')
->setCellValue('F2', 'Tipo')
->setCellValue('G2', 'Fecha');

$start = 3;


$sheet->setCellValue('A'.$start, $entradas)
->setCellValue('B'.$start, $disponibles)
->setCellValue('C'.$start, $salidas);
foreach($operations as $operation){
$sheet->setCellValue('E'.$start, $operation->q)
->setCellValue('F'.$start, $operation->getOperationType()->name)
->setCellValue('G'.$start, $operation->created_at);
$start++;
}
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
////////////////////////////////////////////////////
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="history-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//////////////////////////////////////////////////////
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');