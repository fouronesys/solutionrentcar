<?php


/** Error reporting */
error_reporting(0);


include "../CF-SYSTEMS/T&S-SYSTEMS-14.6/core/autoload.php";
include "../CF-SYSTEMS/T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../CF-SYSTEMS/T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../CF-SYSTEMS/T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../CF-SYSTEMS/T&S-SYSTEMS-14.6/core/app/model/ConfigurationData.php";
include "../CF-SYSTEMS/T&S-SYSTEMS-14.6/core/app/model/DData.php";
include "../CF-SYSTEMS/T&S-SYSTEMS-14.6/core/app/model/PData.php";
include "../CF-SYSTEMS/T&S-SYSTEMS-14.6/core/app/model/PRData.php";
include "../CF-SYSTEMS/T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../CF-SYSTEMS/T&S-SYSTEMS-14.6/core/app/model/UserData.php";
include "../CF-SYSTEMS/T&S-SYSTEMS-14.6/core/app/model/CData.php";
$symbol = ConfigurationData::getByPreffix("currency")->val;

/** Include PHPExcel */
include "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$objPHPExcel = new Spreadsheet();
$cp = CData::getAll();
$operation= OperationData::getAll();
$products = array();
if($_GET["c_id"]==""){
			$products = SellData::getAllByDateOp2($_GET["sd"],$_GET["ed"],2);
			}
			else{
			$products = SellData::getAllByDateBCOp2($_GET["c_id"],$_GET["sd"],$_GET["ed"],2);
			} 

// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Reporte de compro')
->setCellValue('A2', 'Id')
->setCellValue('B2', 'Total')
->setCellValue('C2', 'Cliente')
->setCellValue('D2', 'Vendedor')
->setCellValue('E2', 'Comprobante')
->setCellValue('F2', 'Fecha')
->setCellValue('J2', 'Precio Total');

$start = 3;
foreach($products as $product){
$supertotal+= ($product->total);
$rnc = PRData::getAll();
foreach ($rnc as $op ) {if ($op->rnc==$product->getPerson()->no) {
$ops=$op->name;
}}

foreach ($cp as $cps ) {if ($product->c_id== $cps->id) {
$ops3=$cps->name;
}}

$sheet->setCellValue('A'.$start, $product->id)
->setCellValue('B'.$start, $symbol." ".number_format($product->total,2,".",","))
->setCellValue('C'.$start, $product->getPerson()->name." ".$product->getPerson()->lastname." ".$ops)
->setCellValue('D'.$start, $product->getUser()->name." ".$product->getUser()->lastname)
->setCellValue('E'.$start, $ops3)
->setCellValue('F'.$start, $product->created_at);
$start++;
}
$sheet->setCellValue('J'.$start, $symbol." ".number_format($supertotal,2,".",","));
$start++;


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
////////////////////////////////////////////////////
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="vouchersreports-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//////////////////////////////////////////////////////
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');
