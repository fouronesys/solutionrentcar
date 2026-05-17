<?php


/** Error reporting */
error_reporting(0);


include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/RepairData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";

/** Include PHPExcel */
include "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$objPHPExcel = new Spreadsheet();

$operations = RepairData::getAll();

// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Reporte de Reparaciones')
->setCellValue('A2', 'Nombre')
->setCellValue('B2', 'Equipo')
->setCellValue('C2', 'Modelo')
->setCellValue('D2', 'Reparaciones')
->setCellValue('F2', 'Fecha');

$start = 3;
foreach($operations as $operation){
$client=  PersonData::getById($operation->person_id);
$sheet->setCellValue('A'.$start, $client->name." ".$client->lastname)
->setCellValue('B'.$start, $operation->equipment)
->setCellValue('C'.$start, $operation->model)
->setCellValue('D'.$start, $operation->changer." ".$operation->screen." ".$operation->unlocking." ".$operation->software." ".$operation->batery." ".$operation->general)
->setCellValue('F'.$start, $operation->created_at);
$start++;
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
////////////////////////////////////////////////////
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="repair-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//////////////////////////////////////////////////////
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');
