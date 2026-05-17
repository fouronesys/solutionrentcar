<?php
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationTypeData.php";

//require_once '../../T&S-SYSTEMS-14.6/core/controller/PhpWord/Autoloader.php';
include "../vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;

//Autoloader::register();

$word = new  PhpOffice\PhpWord\PhpWord();
$operations = array();

			$operations = OperationData::getPPByDateOfficial($_GET["sd"],$_GET["ed"]);


$section1 = $word->AddSection();
$section1->addText("PRODUCTOS POPULARES",array("size"=>22,"bold"=>true,"align"=>"right"));


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
$table1->addCell(500)->addText($operation->id);
$table1->addCell(5000)->addText($operation->getProduct()->name);
$table1->addCell(2000)->addText($operation->total);
$table1->addCell(2000)->addText($operation->getOperationType()->name);
$table1->addCell(2000)->addText($operation->created_at);

}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
/// datos bancarios

$filename = "productsreports-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>