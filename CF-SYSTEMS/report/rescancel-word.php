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
$sells = SellData::getAllBySQL(" where operation_type_id=1 and p_id=3 and d_id=3");

$section1 = $word->AddSection();
$section1->addText("COMPRAS CANCELADAS",array("size"=>22,"bold"=>true,"align"=>"right"));


$styleTable = array('borderSize' => 6, 'borderColor' => '888888', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'AAAAAA');

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Id");
$table1->addCell()->addText("Pago");
$table1->addCell()->addText("Entrega");
$table1->addCell()->addText("Total");
$table1->addCell()->addText("Fecha");

foreach($sells as $sell){
//    $q=OperationData::getQYesF($sell->id);
//$q=OperationData::getQByStock($sell->id,$stock);

    $table1->addRow();
    $table1->addCell(300)->addText("#".$sell->id);
    $table1->addCell(2000)->addText($sell->getP()->name);
    $table1->addCell(2000)->addText($sell->getD()->name);
    $table1->addCell(11000)->addText($symbol." ".($sell->total-$sell->discount));
    $table1->addCell(11000)->addText($sell->created_at);

}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
/// datos bancarios

$filename = "rescancel-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>