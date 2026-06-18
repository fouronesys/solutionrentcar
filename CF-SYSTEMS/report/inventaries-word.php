<?php

/** Error reporting */
error_reporting(0);


include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationTypeData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/StockData.php";

include "../vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;


$word = new  PhpOffice\PhpWord\PhpWord();
$products = ProductData::getAll();
$sucursales = StockData::getAll();


$section1 = $word->AddSection();
$section1->addText("INVENTARIO GLOBAL",array("size"=>22,"bold"=>true,"align"=>"right"));


$styleTable = array('borderSize' => 6, 'borderColor' => '888888', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'AAAAAA');

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Codigo");
$table1->addCell()->addText("Nombre");
foreach($sucursales as $suc){
$table1->addCell()->addText($suc->name);
}

foreach($products as $product){
$table1->addRow();
$table1->addCell(300)->addText($product->code);
$table1->addCell(11000)->addText($product->name);
foreach($sucursales as $suc){
$q=OperationData::getQByStock($product->id,$suc->id);
$table1->addCell(500)->addText($q);
}
};


$word->addTableStyle('table1', $styleTable,$styleFirstRow);
/// datos bancarios

$filename = "inventary-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>