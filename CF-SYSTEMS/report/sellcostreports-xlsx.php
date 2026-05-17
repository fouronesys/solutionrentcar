<?php

/** Error reporting */

error_reporting(0);

include "../core/autoload.php";
include "../core/app/model/SellData.php";
include "../core/app/model/ProductData.php";
include "../core/app/model/PersonData.php";
include "../core/app/model/UserData.php";
include "../core/app/model/OperationData.php";
include "../core/app/model/DData.php";
include "../core/app/model/PData.php";
include "../core/app/model/ConfigurationData.php";

/** Include PHPExcel */
//require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
require_once '../core/controller/PHPExcel/Classes/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$products = array();
if($_GET["client_id"]!=""){
$products = SellData::getAllByDateBCOp($_GET["client_id"],$_GET["sd"],$_GET["ed"],2);
			}
if($_GET["user_id"]!="" and $_GET["client_id"]!=""){
$products = SellData::getAllByDateBCOpByUserId($_GET["user_id"],$_GET["client_id"],$_GET["sd"],$_GET["ed"],2);
			}else{
			$products = SellData::getAllByDateOp($_GET["sd"],$_GET["ed"],2);			
			} 
// Set document properties
$objPHPExcel->getProperties()->setCreator("SystePro v3.1")
							 ->setLastModifiedBy("SystePro v3.1")
							 ->setTitle("SystePro v3.1")
							 ->setSubject("SystePro v3.1")
							 ->setDescription("")
							 ->setKeywords("")
							 ->setCategory("");


// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Reporte de Ventas')
->setCellValue('A2', 'Id')
->setCellValue('B2', 'Cliente')
->setCellValue('C2', 'Vendedor')
->setCellValue('D2', 'Pago')
->setCellValue('E2', 'Entrega')
->setCellValue('F2', 'Producto Neto')
->setCellValue('G2', 'Ganancia')
->setCellValue('H2', 'Total Neto')
->setCellValue('I2', 'ITBIS')
->setCellValue('J2', 'Total')
->setCellValue('K2', 'Fecha');

$start = 3;
$total_total = 0;
foreach($products as $product){

$ppt= OperationData::getBySellId($product->id); 
	foreach ($ppt as $pt) {}

$gpt= OperationData::getBySellId2($product->id); 
	foreach ($gpt as $gp) {$gps=$gp-$pt;}

$ztotal = $product->total;
$iva_val = ConfigurationData::getByPreffix("imp-val")->val;
$sheet->setCellValue('A'.$start, $product->id)
->setCellValue('B'.$start, $product->getPerson()->name." ".$product->getPerson()->lastname)
->setCellValue('C'.$start, $product->getUser()->name." ".$product->getUser()->lastname)
->setCellValue('D'.$start, $product->getP()->name)
->setCellValue('E'.$start, $product->getD()->name)
->setCellValue('F'.$start, $pt)
->setCellValue('G'.$start, $gps)
->setCellValue('H'.$start, $ztotal)
->setCellValue('I'.$start, $ztotal*($iva_val/100))
->setCellValue('J'.$start, $ztotal+$ztotal*($iva_val/100))
->setCellValue('K'.$start, $product->created_at);
$start++;
$total_product+= $pt;
$total_gains+= $gps;
$total_total+= $ztotal;
$total_ITBIS+= $ztotal+($ztotal*($iva_val/100));
$ITBIS+= $ztotal*($iva_val/100);
}

$sheet->setCellValue('A'.$start, "")
->setCellValue('B'.$start, "")
->setCellValue('C'.$start, "")
->setCellValue('D'.$start, "")
->setCellValue('E'.$start, "")
->setCellValue('F'.$start, $total_product)
->setCellValue('G'.$start, $total_gains)
->setCellValue('H'.$start, $total_total)
->setCellValue('I'.$start, $ITBIS)
->setCellValue('J'.$start, $total_ITBIS)
->setCellValue('K'.$start, "");
$start++;

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="sellcostreports-'.time().'.xlsx"');
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
