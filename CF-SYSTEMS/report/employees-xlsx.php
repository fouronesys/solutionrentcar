<?php

/** Error reporting */
error_reporting(0);
include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/HEData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SData.php";

include "../vendor/autoload.php";

/** Include PHPExcel */
include "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$objPHPExcel = new Spreadsheet();
$products = PersonData::getEmployees(); 

// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Reporte de Empleados')
->setCellValue('A2', 'Nombre completo')
->setCellValue('B2', 'Direccion')
->setCellValue('C2', 'Telefono')
->setCellValue('D2', 'Cargo')
->setCellValue('E2', 'Horario')
->setCellValue('F2', 'Sueldo')
->setCellValue('G2', 'Hora Extras');

$start = 3;
foreach($products as $product){

foreach (SData::getAll() as $scs) {
  if($scs->id!=null and $scs->id==$product->s_id ){
        $pays=$scs->name; }}

foreach (HEData::getAll() as $op) {
if($op->person_id!=null and $op->person_id==$product->id ){
$pay+=$op->h_id; }}

$sheet->setCellValue('A'.$start, $product->name." ".$product->lastname)
->setCellValue('B'.$start, $product->address1)
->setCellValue('C'.$start, $product->phone1)
->setCellValue('D'.$start, $product->loads)
->setCellValue('E'.$start, $pays)
->setCellValue('F'.$start, $product->money)
->setCellValue('G'.$start, $pay);
$start++;
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


//$sheet->setCellValue('A5', 'Hello World !');
////////////////////////////////////////////////////
  // Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="employees-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//////////////////////////////////////////////////////
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');

