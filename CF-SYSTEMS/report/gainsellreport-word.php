<?php
/** Error reporting */
error_reporting(0);

include "../core/autoload.php";
include "../core/app/model/ProductData.php";
include "../core/app/model/OperationData.php";
include "../core/app/model/SpendData.php";
include "../core/app/model/ConfigurationData.php";
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
$products = array();
$products = OperationData::getAllByDateOp2($_GET["sd"],$_GET["ed"]);

$spends = array();
$spends = SpendData::getGroupByDateOp(date($_GET["sd"]),date($_GET["ed"]));

$section1 = $word->AddSection();
$section1->addText("$title",array("size"=>22,"bold"=>true,"align"=>"right"));
$section1->addText("RNC: $rnc",array("size"=>12,"bold"=>false,"align"=>"right"));
$section1->addText("Fecha: ". date("d-m-Y h:i:s"),array("size"=>12,"bold"=>false,"align"=>"right"));
$section1->addText("$stock->address $stock->address2",array("size"=>12,"bold"=>false,"align"=>"right"));


$section1->addText("REPORTE DE GANANCIA",array("size"=>14,"bold"=>true,"align"=>"right"));

$styleTable = array('borderSize' => 6, 'borderColor' => 'FFFFFF', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'gray');

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Id");
$table1->addCell()->addText("Producto");
$table1->addCell()->addText("Ganancia");
$table1->addCell()->addText("Fecha");

foreach($products as $product){
$table1->addRow();
$table1->addCell(500)->addText($product->id);
$table1->addCell(5000)->addText($product->getProduct()->name);
$table1->addCell(2000)->addText((($product->price_out-$product->price_in)*$product->q)-$product->discount);
$table1->addCell(2000)->addText($product->created_at);
$supertotal+=(($product->price_out-$product->price_in)*$product->q)-$product->discount;
}
$word->addTableStyle('table1', $styleTable,$styleFirstRow);
$section1->addText("Total: $symbol".number_format($supertotal,2,".",","),array("size"=>22,"bold"=>true,"align"=>"right"));
/// datos bancarios

$filename = "gainsellreport-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>