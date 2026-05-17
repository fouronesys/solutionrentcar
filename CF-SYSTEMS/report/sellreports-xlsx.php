<?php

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

include "../core/autoload.php";
include "../core/app/model/SellData.php";
include "../core/app/model/ProductData.php";
include "../core/app/model/OperationData.php";
include "../core/app/model/DData.php";
include "../core/app/model/PData.php";
include "../core/app/model/ConfigurationData.php";
			$iva_val = ConfigurationData::getByPreffix("imp-val")->val;
/** Include PHPExcel */
//require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
require_once '../core/controller/PHPExcel/Classes/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$products = array();
if($_GET["client_id"]==""){
			$products = SellData::getAllByDateOp($_GET["sd"],$_GET["ed"],2);
			}
			else{
			$products = SellData::getAllByDateBCOp($_GET["client_id"],$_GET["sd"],$_GET["ed"],2);
			} 

// Set document properties
$objPHPExcel->getProperties()->setCreator("Thunder Max v3.1")
							 ->setLastModifiedBy("Thunder Max v3.1")
							 ->setTitle("Thunder Max v3.1")
							 ->setSubject("Thunder Max v3.1")
							 ->setDescription("")
							 ->setKeywords("")
							 ->setCategory("");


// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Reporte de Ventas - Thunder Max')
->setCellValue('A2', 'Id')
->setCellValue('B2', 'Pago')
->setCellValue('C2', 'Entrega')
->setCellValue('D2', 'Total')
->setCellValue('E2', 'Fecha');

$start = 3;
$total_total = 0;
foreach($products as $product){
	$ztotal = $product->total-$product->discount;
$sheet->setCellValue('A'.$start, $product->id)
->setCellValue('B'.$start, $product->getP()->name)
->setCellValue('C'.$start, $product->getD()->name)
->setCellValue('D'.$start, $ztotal+($ztotal*($iva_val/100)))
->setCellValue('E'.$start, $product->created_at);
$start++;
$total_total+= $ztotal+($ztotal*($iva_val/100));
}
$sheet->setCellValue('A'.$start, "")
->setCellValue('B'.$start, "")
->setCellValue('C'.$start, "")
->setCellValue('D'.$start, $total_total)
->setCellValue('E'.$start, "");
$start++;

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="sellsreport-'.time().'.xlsx"');
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
