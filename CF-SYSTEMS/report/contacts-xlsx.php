<?php

/** Error reporting */
include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/CategoryData.php";

include "../vendor/autoload.php";

/** Include PHPExcel */
//require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
//require_once '../../T&S-SYSTEMS-14.6/core/controller/PHPExcel/Classes/PHPExcel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
//require __DIR__ . '/../Header.php';

//$spreadsheet = new Spreadsheet();
// Create new PHPExcel object
$objPHPExcel = new Spreadsheet();
$products = PersonData::getContacts();


// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Reporte de Contactos')
->setCellValue('A2', 'Nombre completo')
->setCellValue('B2', 'Direccion')
->setCellValue('C2', 'Email')
->setCellValue('D2', 'Telefono');


$start = 3;
foreach($products as $product){
$sheet->setCellValue('A'.$start, $product->name." ".$product->lastname)
->setCellValue('B'.$start, $product->address1)
->setCellValue('C'.$start, $product->email1)
->setCellValue('D'.$start, $product->phone1);

$start++;
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


//$sheet->setCellValue('A5', 'Hello World !');
////////////////////////////////////////////////////
  // Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="contacts-'.time().'.xlsx"');
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

