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

$users = RepairData::getAll();

foreach($users as $user){
if($user->payment<=$user->price){
    $found=true;
    break; 

  }
}

// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Reporte de Pagos Pendiente de Reparaciones')
->setCellValue('A2', 'Nombre Completo')
->setCellValue('B2', 'Direccion')
->setCellValue('C2', 'Telefono')
->setCellValue('D2', 'Saldo Pendiente');

$start = 3;
foreach($users as $user){
$client=  PersonData::getById($user->person_id);
  if($user->payment==0 or $user->payment2==0){
$q=$user->price-$user->payment;
$sheet->setCellValue('A'.$start, $client->name." ".$client->lastname)
->setCellValue('B'.$start, $client->address1)
->setCellValue('C'.$start, $operation->phone1)
->setCellValue('D'.$start, $q);
$start++;
}
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
////////////////////////////////////////////////////
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="pending-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//////////////////////////////////////////////////////
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');
