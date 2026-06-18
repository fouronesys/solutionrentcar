<?php

/** Error reporting */
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/UserData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationTypeData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
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
$products = SellData::getAllBySQL(" where operation_type_id=5 and status=1");


// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Devoluciones')
->setCellValue('A2', 'Factura')
->setCellValue('B2', 'Total')
->setCellValue('C2', 'Producto')
->setCellValue('D2', 'Cliente')
->setCellValue('E2', 'Vendedor')
->setCellValue('F2', 'Almacen')
->setCellValue('G2', 'Fecha');

$start = 3;
foreach($products as $sell){
$operations = OperationData::getAllProductsBySellId($sell->id);

foreach ($operations as $operation) {
$total+=$operation->q*$operation->price_out;

if ($total>0) {

$sheet->setCellValue('A'.$start, $sell->id)
->setCellValue('B'.$start, $symbol." ".number_format($total,2,".",","))
->setCellValue('C'.$start, $operation->getProduct()->name)
->setCellValue('D'.$start, $sell->getPerson()->name." ".$sell->getPerson()->lastname)
->setCellValue('E'.$start, $sell->getUser()->name." ".$sell->getUser()->lastname)
->setCellValue('F'.$start, $sell->getStockTo()->name)
->setCellValue('G'.$start, $sell->created_at);
$start++;
}
}
}
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
////////////////////////////////////////////////////
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="devs-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//////////////////////////////////////////////////////
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');