<?php
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/UserData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationTypeData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/StockData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ConfigurationData.php";

$symbol = ConfigurationData::getByPreffix("currency")->val;
$title = ConfigurationData::getByPreffix("ticket_title")->val;
$iva_val = ConfigurationData::getByPreffix("imp-val")->val;

include "../vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;


$word = new  PhpOffice\PhpWord\PhpWord();
$products = SellData::getAllBySQL(" where operation_type_id=5 and status=1");

$section1 = $word->AddSection();
$section1->addText("DEVOLUCIONES",array("size"=>22,"bold"=>true,"align"=>"right"));

$styleTable = array('borderSize' => 6, 'borderColor' => '888888', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'AAAAAA');

$total=0;

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell(1000)->addText("Factura");
$table1->addCell(1500)->addText("Total");
$table1->addCell(1500)->addText("Producto");
$table1->addCell(2000)->addText("Cliente");
$table1->addCell(2000)->addText("Vendedor");
$table1->addCell(1500)->addText("Almacen");
$table1->addCell(1500)->addText("Fecha");

foreach($products as $sell){
$operations = OperationData::getAllProductsBySellId($sell->id);

foreach ($operations as $operation) {
$total+=$operation->q*$operation->price_out;

if ($total!=0) {

$table1->addRow();
$table1->addCell()->addText($sell->id);

$table1->addCell()->addText($symbol." ".number_format($total,2,".",","));

$table1->addCell()->addText($operation->getProduct()->name);

if($sell->person_id!=null){$c= $sell->getPerson();
$table1->addCell()->addText($c->name." ".$c->lastname);
}

if($sell->user_id!=null){$c= $sell->getUser();
$table1->addCell()->addText($c->name." ".$c->lastname);
}

$table1->addCell()->addText($sell->getStockTo()->name);

$table1->addCell()->addText($sell->created_at);
}
}
}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
/// datos bancarios

$filename = "devs-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>