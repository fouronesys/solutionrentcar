<?php
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


$word = new  PhpOffice\PhpWord\PhpWord();
$sells = SellData::getAllBySQL(" where operation_type_id=6");

$section1 = $word->AddSection();
$section1->addText("TRASPASOS",array("size"=>22,"bold"=>true,"align"=>"right"));


$styleTable = array('borderSize' => 6, 'borderColor' => '888888', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'AAAAAA');

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Factura");
$table1->addCell()->addText("Total");
$table1->addCell()->addText("Vendedor");
$table1->addCell()->addText("Origen");
$table1->addCell()->addText("Destino");
$table1->addCell()->addText("Fecha");

 foreach($products as $sell){
  	$operations = OperationData::getAllProductsBySellId($sell->id);

    $table1->addRow();
    $table1->addCell(300)->addText("#".$sell->id);
    if($sell->user_id!=null){$c= $sell->getUser();
    $table1->addCell(2000)->addText($c->name." ".$c->lastname);
}
$total= $sell->total-$sell->discount;
  	$table1->addCell(11000)->addText($symbol." ".number_format($total,2,".",","));
    $table1->addCell(11000)->addText($sell->getStockFrom()->name);
     $table1->addCell(11000)->addText($sell->getStockTo()->name);
    $table1->addCell(11000)->addText($sell->created_at);

}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
/// datos bancarios

$filename = "trasps-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>