<?php

/** Error reporting */
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationTypeData.php";

/** Include PHPExcel */
include "../vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;


$word = new  PhpOffice\PhpWord\PhpWord();
$operations = array();

			if($_GET["product_id"]==""){
			$operations = OperationData::getAllByDateOfficial($_GET["stock_id"],$_GET["sd"],$_GET["ed"]);
			}
			else{
			$operations = OperationData::getAllByDateOfficialBP($_GET["stock_id"],$_GET["product_id"],$_GET["sd"],$_GET["ed"]);
			} 

$section1 = $word->AddSection();
$section1->addText("REPORTE DE INVENTARIO",array("size"=>22,"bold"=>true,"align"=>"right"));


$styleTable = array('borderSize' => 6, 'borderColor' => '888888', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'AAAAAA');

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Id");
$table1->addCell()->addText("Producto");
$table1->addCell()->addText("Cantidad");
$table1->addCell()->addText("Operacion");
$table1->addCell()->addText("Fecha");
foreach($operations as $operation){
$table1->addRow();
$table1->addCell(300)->addText($operation->id);
$table1->addCell(3000)->addText($operation->getProduct()->name);
$table1->addCell(500)->addText($operation->q);
$table1->addCell(2000)->addText($operation->getOperationType()->name);
$table1->addCell(2000)->addText($operation->created_at);

}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
/// datos bancarios

$filename = "report-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>