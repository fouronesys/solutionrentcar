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
$symbol = ConfigurationData::getByPreffix("currency")->val;

include "../vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;

//Autoloader::register();

$word = new  PhpOffice\PhpWord\PhpWord();

$products = array();
if($_GET["client_id"]==""){
			$products = SellData::getAllByDateOp($_GET["sd"],$_GET["ed"],1);
			}
			else{
			$products = SellData::getAllByDateBCOp($_GET["client_id"],$_GET["sd"],$_GET["ed"],1);
			} 
$section1 = $word->AddSection();
$section1->addText("REPORTE DE COMPRAS",array("size"=>22,"bold"=>true,"align"=>"right"));


$styleTable = array('borderSize' => 6, 'borderColor' => '888888', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'AAAAAA');

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Id");
$table1->addCell()->addText("Pago");
$table1->addCell()->addText("Entrega");
$table1->addCell()->addText("Total");
$table1->addCell()->addText("Fecha");

foreach($products as $product){
$total=0;
$ops = OperationData::getAllProductsBySellId($product->id);
foreach($ops as $op){
	//$product  = $op->getProduct();
	$total += $op->q*$op->price_in;
	$Supertotal +=$total;

}

$table1->addRow();
$table1->addCell(5000)->addText($product->id);

$table1->addCell(2500)->addText($product->getP()->name);

$table1->addCell(2500)->addText($product->getD()->name);

$table1->addCell(2500)->addText($symbol." ".number_format($total,2,".",","));

$table1->addCell(2000)->addText($product->created_at);

}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
$section1->addText("Total: $symbol".number_format($Supertotal,2,".",","),array("size"=>22,"bold"=>true,"align"=>"right"));
/// datos bancarios

$filename = "resreports-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>