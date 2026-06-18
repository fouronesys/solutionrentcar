<?php
/** Error reporting */
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/HEData.php";

include "../vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;

//Autoloader::register();

$word = new  PhpOffice\PhpWord\PhpWord();

$client = PersonData::getById($_GET["id"]);
$products = HEData::getAll();

$section1 = $word->AddSection();
$section1->addText("HISTORIAL DE HORAS EXTRAS",array("size"=>22,"bold"=>true,"align"=>"right"));
$section1->addText("Cliente: ".$client->name." ".$client->lastname,array("size"=>18,"align"=>"right"));


$styleTable = array('borderSize' => 6, 'borderColor' => '888888', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'AAAAAA');

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Puesto");
$table1->addCell()->addText("Horas");
$table1->addCell()->addText("Fecha");

foreach($products as $product){
$table1->addRow();
$table1->addCell(5000)->addText($product->loads);
$table1->addCell(2000)->addText($product->payment);
$table1->addCell(2000)->addText($product->created_at);
}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
/// datos bancarios

$filename = "hextrashistory-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>