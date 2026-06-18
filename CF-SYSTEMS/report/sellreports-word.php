<?php

/** Error reporting */
error_reporting(0);


include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ConfigurationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PRData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/UserData.php";
$symbol = ConfigurationData::getByPreffix("currency")->val;

include "../vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;

//Autoloader::register();

$word = new  PhpOffice\PhpWord\PhpWord();
$operation= OperationData::getAll();
$products = array();
if($_GET["client_id"]==""){
			$products = SellData::getAllByDateOp($_GET["sd"],$_GET["ed"],2);
			}
			else{
			$products = SellData::getAllByDateBCOp($_GET["client_id"],$_GET["sd"],$_GET["ed"],2);
			} 
$section1 = $word->AddSection();
$section1->addText("REPORTE DE VENTAS",array("size"=>22,"bold"=>true,"align"=>"right"));


$styleTable = array('borderSize' => 6, 'borderColor' => '888888', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'AAAAAA');

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Id");
$table1->addCell()->addText("Total");
$table1->addCell()->addText("Cliente");
$table1->addCell()->addText("Vendedor");
$table1->addCell()->addText("Fecha");
$total=0;
foreach($products as $product){
$supertotal+= ($product->total);
$rnc = PRData::getAll();
foreach ($rnc as $op ) {if ($op->rnc==$product->getPerson()->no) {
$ops=$op->name;
}}
$table1->addRow();
$table1->addCell(5000)->addText($product->id);

$table1->addCell(2500)->addText($symbol." ".number_format($product->total,2,".",","));

$table1->addCell(2500)->addText($product->getPerson()->name." ".$product->getPerson()->lastname." ".$ops);

$table1->addCell(2500)->addText($product->getUser()->name." ".$product->getUser()->lastname);

$table1->addCell(2500)->addText($product->created_at);

}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
$section1->addText("Total: $symbol".number_format($supertotal,2,".",","),array("size"=>22,"bold"=>true,"align"=>"right"));
/// datos bancarios

$filename = "sellreports-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>