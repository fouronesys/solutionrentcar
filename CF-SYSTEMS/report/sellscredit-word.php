<?php
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/DData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PaymentData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ConfigurationData.php";
$symbol = ConfigurationData::getByPreffix("currency")->val;

include "../vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;


$word = new  PhpOffice\PhpWord\PhpWord();
$sells = SellData::getCredits();

$section1 = $word->AddSection();
$section1->addText("VENTAS A CREDITO",array("size"=>22,"bold"=>true,"align"=>"right"));


$styleTable = array('borderSize' => 6, 'borderColor' => '888888', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'AAAAAA');

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Id");
$table1->addCell()->addText("Pago");
$table1->addCell()->addText("Entrega");
$table1->addCell()->addText("Pendiente");
$table1->addCell()->addText("Total");
$table1->addCell()->addText("Fecha");

foreach($sells as $sell){
//    $q=OperationData::getQYesF($sell->id);
//$q=OperationData::getQByStock($sell->id,$stock);

    $table1->addRow();
    $table1->addCell(300)->addText("#".$sell->id);
    $table1->addCell(2000)->addText($sell->getP()->name);
    $table1->addCell(2000)->addText($sell->getD()->name);

  $creditsum= 0;
  $tx = PaymentData::sumBySellId($sell->id)->total;

if($tx>=0){
//$credit_array[] = array("sell_id"=>$sell->id,"total"=>$tx);
  $creditsum=$tx;
}
    $table1->addCell(11000)->addText($symbol." ".number_format($creditsum,2,".",","));
    $table1->addCell(11000)->addText($symbol." ".number_format($sell->total,2,".",","));
    $table1->addCell(11000)->addText($sell->created_at);

}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
/// datos bancarios

$filename = "sellcredit-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>