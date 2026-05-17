<?php

/** Error reporting */
error_reporting(0);
/****/

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/BoxData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/UserData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/StockData.php";


/** Include PHPExcel */
//require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

include "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$objPHPExcel = new Spreadsheet();

$boxes = BoxData::getAll();
$products = SellData::getSellsUnBoxed();

// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Historial de Caja')
->setCellValue('A2', 'Usuario')
->setCellValue('B2', 'Almacen')
->setCellValue('C2', 'Total')
->setCellValue('D2', 'Fecha')
->setCellValue('G2', 'Dinero Total');

$start = 3;
foreach($boxes as $box){
$sells = SellData::getByBoxId($box->id);
$total=0;

foreach($sells as $sell){
$operations = OperationData::getAllProductsBySellId($sell->id);
		$total += $sell->total-$sell->discount;

}
$total_total += $total;
$stock = StockData::getById($box->stock_id);
$user = UserData::getById($box->user_id);
$sheet->setCellValue('A'.$start, $user->name." ".$user->lastname)
->setCellValue('B'.$start, $stock->name)
->setCellValue('C'.$start, $symbol." ".number_format($total,2,".",","))
->setCellValue('D'.$start, $box->created_at)
->setCellValue('G'.$start, $symbol." ".number_format($total_total,2,".",","));

}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
////////////////////////////////////////////////////
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="boxhistory-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//////////////////////////////////////////////////////
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');