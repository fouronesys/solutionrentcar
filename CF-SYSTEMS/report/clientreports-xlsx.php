<?php
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PRData.php";


include "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$objPHPExcel = new Spreadsheet();
$operations = array();

			$operations = SellData::getSQL("select *,sum(total-discount) as st from sell where date(created_at) >= \"$_GET[sd]\" and date(created_at) <= \"$_GET[ed]\" and operation_type_id=2 and is_draft=0 and p_id=1 and d_id=1 and person_id is not NULL group by person_id order by st desc");

			 if(count($operations)>0){
			 	$supertotal = 0;}

// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Reporte de Clientes Populares')
->setCellValue('A2', 'Id Cliente')
->setCellValue('B2', 'Total Venta')
->setCellValue('C2', 'Cliente');

$start = 3;
foreach($operations as $operation){
$rnc = PRData::getAll();
foreach ($rnc as $op ) {if ($op->rnc==$operation->getPerson()->no) {
$ops=$op->name;
}}
$sheet->setCellValue('A'.$start, $operation->person_id)
->setCellValue('B'.$start, $symbol." ".number_format($operation->st,2,".",","))
->setCellValue('C'.$start, $operation->getPerson()->name." ".$operation->getPerson()->lastname."".$ops);
$start++;
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


//$sheet->setCellValue('A5', 'Hello World !');
////////////////////////////////////////////////////
  // Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="topclients-'.time().'.xlsx"');
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

