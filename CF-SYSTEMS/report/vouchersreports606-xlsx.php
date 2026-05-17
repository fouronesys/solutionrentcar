<?php

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ConfigurationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/DData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/FData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PRData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/UserData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CFData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CFSData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CGData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CNCData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CNDData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CRSData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/TGData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SGData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SpendData.php";

$symbol = ConfigurationData::getByPreffix("currency")->val;

/** Include PHPExcel */
include "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$objPHPExcel = new Spreadsheet();

$rnc = ConfigurationData::getByPreffix("rnc")->val;
$iva_name = ConfigurationData::getByPreffix("imp-name")->val;
$iva_val = ConfigurationData::getByPreffix("imp-val")->val;

$cp = TGData::getAll();
$cs = SGData::getAll();
$tp= FData::getAll();

$operations = array();
		    if($_GET["c_id"]==""){
			$operations = SellData::getAllByDateOp2($_GET["sd"],$_GET["ed"],1);
			}
			else if($_GET["c_id"]!=""){
			$operations = SellData::getAllByDateBCOp2($_GET["c_id"],$_GET["sd"],$_GET["ed"],1);
			}  

$products= array();
			if($_GET["c_id"]==""){
			$products = SpendData::getAllByDateOp2($_GET["sd"],$_GET["ed"]);
			}
			else if($_GET["c_id"]!=""){
			$products= SpendData::getAllByDateBCOp2($_GET["c_id"],$_GET["sd"],$_GET["ed"]);
			}

$counts=(count($products)+count($operations));
// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', $rnc)
->setCellValue('B1', date("Ym", strtotime($_GET["sd"])))
->setCellValue('C1', $counts)
->setCellValue('A3', 'RNC o Cedula')
->setCellValue('B3', 'Tipo Id')
->setCellValue('C3', 'Tipo Bienes y Servicios Comprados')
->setCellValue('D3', 'NCF')
->setCellValue('E3', 'NCF o Documento Modficado')
->setCellValue('F3', 'Fecha Comprobante')
->setCellValue('G3', 'Fecha Pago')
->setCellValue('H3', 'Monto Facturado en Servicio')
->setCellValue('I3', 'Monto Facturado en Bienes')
->setCellValue('J3', 'Total Monto Facturado')
->setCellValue('K3', 'ITBIS Facturado')
->setCellValue('L3', 'ITBIS Retenido')
->setCellValue('M3', 'ITBIS Sujeto a Proporcionalidad (Art.349)')
->setCellValue('N3', 'ITBIS llevado al costo')
->setCellValue('O3', 'ITBIS por Adelantar')
->setCellValue('P3', 'ITBIS Percibido en Compras')
->setCellValue('Q3', 'Tipo de Retencion en ISR')
->setCellValue('R3', 'Monto Retenido Renta')
->setCellValue('S3', 'ISR Percibido en Compras')
->setCellValue('T3', 'Impuesto Selectivo al Consumo')
->setCellValue('U3', 'Otros Impuesto/Tasas')
->setCellValue('V3', 'Monto Propina Legal')
->setCellValue('W3', 'Forma de Pago');





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
