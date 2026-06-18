<?php


/** Error reporting */
error_reporting(0);


include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/DData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ConfigurationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/StockData.php";
$symbol = ConfigurationData::getByPreffix("currency")->val;

/** Include PHPExcel */
include "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$objPHPExcel = new Spreadsheet();
$stocks = StockData::getAll();

$products = array();
if(isset($_GET["sd"]) && isset($_GET["ed"]) ){
if($_GET["sd"]!=""&&$_GET["ed"]!=""){
			
			$operations = array();

			$operations = OperationData::getAllByDateOfficial($_GET["stock_id"],$_GET["sd"],$_GET["ed"]);
			}

if(count($operations)>0){
$products = ProductData::getAllByCategoryId($_GET["product_id"]);


// Add some data
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', 'Reporte de Ventas')
->setCellValue('A2', 'Id')
->setCellValue('B2', 'Producto')
->setCellValue('C2', 'Entradas')
->setCellValue('D2', 'Precio Entradas')
->setCellValue('E2', 'Salidas')
->setCellValue('F2', 'Precio Salidas')
->setCellValue('G2', 'E-S')
->setCellValue('H2', 'Precio E-S');

foreach($products as $p){
$ni = 0;
$no = 0;
foreach($operations as $o){
	if($o->operation_type_id==1&& $o->product_id==$p->id){ $ni+=$o->q; }
	else if($o->operation_type_id==2&& $o->product_id==$p->id){ $no+=$o->q; }
}
$start = 3;
$sheet->setCellValue('A'.$start, $p->id)
->setCellValue('B'.$start, $p->name)
->setCellValue('C'.$start, $ni)
->setCellValue('D'.$start, $symbol." ".($p->price_in*$ni))
->setCellValue('E'.$start, $no)
->setCellValue('F'.$start, $symbol." ".($p->price_in*$no))
->setCellValue('G'.$start, ($ni-$no))
->setCellValue('H'.$start, $symbol." ".(($p->price_in*$ni)-($p->price_out*$no)));
$start++;
}
}
}
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
////////////////////////////////////////////////////
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reports-'.time().'.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//////////////////////////////////////////////////////
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');
