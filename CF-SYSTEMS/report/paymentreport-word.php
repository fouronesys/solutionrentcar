<?php
/** Error reporting */
error_reporting(0);

include "../core/autoload.php";
include "../core/app/model/PersonData.php";
include "../core/app/model/PaymentData.php";
include "../core/app/model/PaymentTypeData.php";

include "../core/app/model/StockData.php";
include "../core/app/model/ConfigurationData.php";

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
if(isset($_GET["client_id"]) && $_GET["client_id"]!=""){
			$products = PaymentData::getAllByDateAndClient($_GET["sd"],$_GET["ed"],$_GET["client_id"]);

			}else{
				$products = PaymentData::getAllByDate($_GET["sd"],$_GET["ed"]);

			}

$section1 = $word->AddSection();
$section1->addText("$title",array("size"=>22,"bold"=>true,"align"=>"right"));
$section1->addText("RNC: $rnc",array("size"=>12,"bold"=>false,"align"=>"right"));
$section1->addText("Fecha: ". date("d-m-Y h:i:s"),array("size"=>12,"bold"=>false,"align"=>"right"));
$section1->addText("$stock->address $stock->address2",array("size"=>12,"bold"=>false,"align"=>"right"));


$section1->addText("REPORTE DE PAGO DE CLIENTES",array("size"=>14,"bold"=>true,"align"=>"right"));

$styleTable = array('borderSize' => 6, 'borderColor' => 'FFFFFF', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'gray');


$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Id");
$table1->addCell()->addText("Cliente");
$table1->addCell()->addText("Valor");
$table1->addCell()->addText("Fecha");

foreach($products as $product){
$cli = $product->getClient();
$table1->addRow();
$table1->addCell(5000)->addText($product->id);
$table1->addCell(2000)->addText($cli->name." ".$cli->lastname);
$table1->addCell(2000)->addText(number_format(abs($product->val),2,".",","));
$table1->addCell(2000)->addText($product->created_at);
$supertotal+=abs($product->val);
}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
$section1->addText("Total: $symbol".number_format($supertotal,2,".",","),array("size"=>22,"bold"=>true,"align"=>"right"));
/// datos bancarios

$filename = "paymentreport-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>