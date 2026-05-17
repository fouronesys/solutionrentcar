<?php

/** Error reporting */
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PaymentData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PaymentTypeData.php";

include "../vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$products = array();
if(isset($_GET["client_id"]) && $_GET["client_id"]!=""){
			$products = PaymentData::getAllByDateAndRClient($_GET["sd"],$_GET["ed"],$_GET["client_id"]);

			}else{
				$products = PaymentData::getAllByRDate($_GET["sd"],$_GET["ed"]);

			}

// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);
$t=0;
foreach($products as $product){ $t+=$product->val; }

$sheet->setCellValue('A1', 'Reporte de Pagos')
->setCellValue('A2',"Total Recaudado: $". number_format(abs($t),2,".",","))
->setCellValue('A5', 'Proveedor')
->setCellValue('B5', 'Valor')
->setCellValue('C5', 'Fecha');

$start = 6;
foreach($products as $product){
$cli = $product->getClient();
$sheet->setCellValue('A'.$start, $cli->name." ".$cli->lastname)
->setCellValue('B'.$start, "$ ".number_format(abs($product->val),2,".",","))
->setCellValue('C'.$start, $product->created_at);
$start++;
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="paymentrreport-'.time().'.xlsx"');
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
