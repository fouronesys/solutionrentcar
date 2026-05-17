<?php

/** Error reporting */
error_reporting(0);


include "../core/autoload.php";
include "../core/app/model/SellData.php";
include "../core/app/model/ProductData.php";
include "../core/app/model/OperationData.php";
include "../core/app/model/ConfigurationData.php";
include "../core/app/model/PRData.php";
include "../core/app/model/PersonData.php";
include "../core/app/model/PData.php";
include "../core/app/model/DData.php";
include "../core/app/model/UserData.php";
include "../core/app/model/StockData.php";

$symbol = ConfigurationData::getByPreffix("currency")->val;

include "../../CF-SYSTEMS/vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;

$title = ConfigurationData::getByPreffix("ticket_title")->val;
$iva_val = ConfigurationData::getByPreffix("imp-val")->val;
$ticket_image = ConfigurationData::getByPreffix("ticket_image")->val;
$rnc = ConfigurationData::getByPreffix("rnc")->val;


$stock = StockData::getPrincipal();
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
$section1->addText("$title",array("size"=>22,"bold"=>true,"align"=>"right"));
$section1->addText("RNC: $rnc",array("size"=>12,"bold"=>false,"align"=>"right"));
$section1->addText("Fecha: ". date("d-m-Y h:i:s"),array("size"=>12,"bold"=>false,"align"=>"right"));
$section1->addText("$stock->address $stock->address2",array("size"=>12,"bold"=>false,"align"=>"right"));


$section1->addText("REPORTE DE VENTAS",array("size"=>14,"bold"=>true,"align"=>"right"));

$styleTable = array('borderSize' => 6, 'borderColor' => 'FFFFFF', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'gray');

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Id");
$table1->addCell()->addText("Cliente");
$table1->addCell()->addText("Vendedor");
$table1->addCell()->addText("Pago");
$table1->addCell()->addText("Entrega");
$table1->addCell()->addText("Producto Neto");
$table1->addCell()->addText("Ganancia");
$table1->addCell()->addText("Total Neto");
$table1->addCell()->addText("ITBIS");
$table1->addCell()->addText("Total");
$table1->addCell()->addText("Fecha");
$total=0;
foreach($products as $product){
$supertotal+= ($product->total);

$ppt= OperationData::getBySellId($product->id); 
	foreach ($ppt as $pt) {}

$gpt= OperationData::getBySellId2($product->id); 
	foreach ($gpt as $gp) {$gps=$gp-$pt;}

$rnc = PRData::getAll();
foreach ($rnc as $op ) {if ($op->rnc==$product->getPerson()->no) {
$ops=$op->name;
}}
$table1->addRow();
$table1->addCell(5000)->addText($product->id);

$table1->addCell(2500)->addText($product->getPerson()->name." ".$product->getPerson()->lastname." ".$ops);

$table1->addCell(2500)->addText($product->getUser()->name." ".$product->getUser()->lastname);

$table1->addCell(2500)->addText($symbol." ".$product->getP()->name);

$table1->addCell(2500)->addText($symbol." ".$product->getD()->name);

$table1->addCell(2500)->addText($symbol." ".$pt);

$table1->addCell(2500)->addText($symbol." ".$gps);

$table1->addCell(2500)->addText($symbol." ".number_format($product->total/(1 + ($iva_val/100)),2,".",","));

$table1->addCell(2500)->addText($symbol." ".number_format(($product->total/1.18)*$iva_val/100,2,".",","));

$table1->addCell(2500)->addText($symbol." ".number_format($product->total,2,".",","));

$table1->addCell(2500)->addText($product->created_at);

}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
$section1->addText("Total: $symbol".number_format($supertotal,2,".",","),array("size"=>22,"bold"=>true,"align"=>"right"));
/// datos bancarios

$filename = "sellcostreports-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>