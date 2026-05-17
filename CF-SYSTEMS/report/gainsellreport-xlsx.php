<?php

/** Error reporting */

error_reporting(0);
include "../core/autoload.php";
include "../core/app/model/ProductData.php";
include "../core/app/model/OperationData.php";
include "../core/app/model/SpendData.php";

/** Include PHPExcel */
//require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
require_once '../core/controller/PHPExcel/Classes/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$products = array();
$products = OperationData::getAllByDateOp2($_GET["sd"],$_GET["ed"]);

$spends = array();
$spends = SpendData::getGroupByDateOp(date($_GET["sd"]),date($_GET["ed"]));
// Set document properties
$objPHPExcel->getProperties()->setCreator("SystePro v3.1")
							 ->setLastModifiedBy("SystePro v3.1")
							 ->setTitle("Products - SystePro v3.1")
							 ->setSubject("SystePro Products Report")
							 ->setDescription("")
							 ->setKeywords("")
							 ->setCategory("");


// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Reporte de Ganancia')
->setCellValue('A2', 'Id')
->setCellValue('B2', 'Producto')
->setCellValue('C2', 'Ganancia')
->setCellValue('D2', 'Fecha');


$start = 3;
foreach($products as $product){
$sheet->setCellValue('A'.$start, $product->id)
->setCellValue('B'.$start, $product->getProduct()->name)
->setCellValue('C'.$start, (($product->price_out-$product->price_in)*$product->q)-$product->discount)
->setCellValue('D'.$start, $product->created_at);
$start++;
$supertotal+=(($product->price_out-$product->price_in)*$product->q)-$product->discount;
}

$sheet->setCellValue('A'.$start, "")
->setCellValue('B'.$start, "TOTAL")
->setCellValue('C'.$start, $supertotal)
->setCellValue('D'.$start, "");
$start++;

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="gainsellreport-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
