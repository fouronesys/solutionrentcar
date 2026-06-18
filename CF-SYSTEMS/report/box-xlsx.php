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
include "../../T&S-SYSTEMS-14.6/core/app/model/SpendData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/IncomeData.php";


/** Include PHPExcel */
//require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

include "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$objPHPExcel = new Spreadsheet();

$clients = PersonData::getClients();
$sells = SellData::getByBoxId($_GET["id"]);
$res = SellData::getResByBoxId($_GET["id"]);
$spends = SpendData::getSpendsByBoxId($_GET["id"]);
$deps = IncomeData::getIncomeByBoxId($_GET["id"]);
// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'CORTE DE CAJA #'.$_GET["id"])
->setCellValue('A2', 'VENTAS ID')
->setCellValue('B2', 'Monto')
->setCellValue('C2', 'Total')
->setCellValue('D2', 'Fecha');


$start = 3;
$total_total = 0;
foreach($sells as $sell){
$total=0;
$operations = OperationData::getAllProductsBySellId($sell->id);
	foreach($operations as $operation){
		$product  = $operation->getProduct();
		$total += $operation->q*$product->price_out;
}

$total_total += $total;

$sheet->setCellValue('A'.$start, $sell->id)
->setCellValue('B'.$start, $symbol." ".number_format($total,2,".",","))
->setCellValue('C'.$start, $symbol." ".number_format($total_total,2,".",","))
->setCellValue('D'.$start, $sell->created_at);
}

$sheet->setCellValue('G2', 'COMPRAS ID')
->setCellValue('H2', 'Monto')
->setCellValue('I2', 'Total')
->setCellValue('J2', 'Fecha');


$start = 3;
$Rtotal_total = 0;
foreach($res as $sellR){
	$Rtotal=0;
$operations = OperationData::getAllProductsBySellId($sellR->id);
	foreach($operations as $operation){
		$product  = $operation->getProduct();
		$Rtotal += $operation->q*$product->price_in;
}
	$Rtotal_total +=$Rtotal;

$sheet->setCellValue('G'.$start, $sellR->id)
->setCellValue('H'.$start, $symbol." ".number_format($Rtotal,2,".",","))
->setCellValue('I'.$start, $symbol." ".number_format($Rtotal_total,2,".",","))
->setCellValue('J'.$start, $sellR->created_at);
}

$sheet->setCellValue('M2', 'GASTOS ID')
->setCellValue('N2', 'Concepto')
->setCellValue('O2', 'Monto')
->setCellValue('P2', 'Total')
->setCellValue('Q2', 'Fecha');


$start = 3;
$Gtotal_total = 0;
foreach($spends as $sellG){
	$Gtotal=$sellG->price;
	$Gtotal_total +=$Gtotal;

$sheet->setCellValue('M'.$start, $sellG->id)
->setCellValue('N'.$start, $sellG->name)
->setCellValue('O'.$start, $symbol." ".number_format($Gtotal,2,".",","))
->setCellValue('P'.$start, $symbol." ".number_format($Gtotal_total,2,".",","))
->setCellValue('Q'.$start, $sellG->created_at);
}

$sheet->setCellValue('S2', 'INGRESOS ID')
->setCellValue('T2', 'Concepto')
->setCellValue('U2', 'Monto')
->setCellValue('V2', 'Total')
->setCellValue('W2', 'Fecha');


$start = 3;
$Itotal_total = 0;
foreach($deps as $sellI){
	$Itotal=$sellI->price;
	$Itotal_total +=$Itotal;

$sheet->setCellValue('M'.$start, $sellI->id)
->setCellValue('N'.$start, $sellI->name)
->setCellValue('O'.$start, $symbol." ".number_format($Itotal,2,".",","))
->setCellValue('P'.$start, $symbol." ".number_format($Itotal_total,2,".",","))
->setCellValue('Q'.$start, $sellI->created_at);
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
////////////////////////////////////////////////////
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="b-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//////////////////////////////////////////////////////
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');