<?php
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/RepairData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";

//require_once '../../T&S-SYSTEMS-14.6/core/controller/PhpWord/Autoloader.php';
include "../vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;

//Autoloader::register();

$word = new  PhpOffice\PhpWord\PhpWord();
$operations = array();

			$operations = RepairData::getByDate($_GET["sd"],$_GET["ed"]);


$section1 = $word->AddSection();
$section1->addText("REPARACIONES",array("size"=>22,"bold"=>true,"align"=>"right"));


$styleTable = array('borderSize' => 6, 'borderColor' => '888888', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'AAAAAA');

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Name");
$table1->addCell()->addText("Equipo");
$table1->addCell()->addText("Modelo");
$table1->addCell()->addText("Reparacion");
$table1->addCell()->addText("Precio");
$table1->addCell()->addText("Fecha");

foreach($operations as $operation){
$client=  PersonData::getById($operation->person_id);
$table1->addRow();
$table1->addCell(500)->addText($client->name." ".$client->lastname);
$table1->addCell(5000)->addText($operation->equipment);
$table1->addCell(2000)->addText($operation->model);
if ($operation->changer!="") {
$table1->addCell(2000)->addText($operation->changer);
}
if ($operation->screen!="") {
$table1->addCell(2000)->addText($operation->screen);
}
if ($operation->unlocking!="") {
$table1->addCell(2000)->addText($operation->unlocking);
}
if ($operation->software!="") {
$table1->addCell(2000)->addText($operation->software);
}
if ($operation->batery!="") {
$table1->addCell(2000)->addText($operation->batery);
}
if ($operation->general!="") {
$table1->addCell(2000)->addText($operation->general);
}
$table1->addCell(2000)->addText($operation->payment+$operation->payment2+$operation->total);
$table1->addCell(2000)->addText($operation->created_at);

$repair+=$operation->payment+$operation->payment2+$operation->total;
}
$word->addTableStyle('table1', $styleTable,$styleFirstRow);
$section1->addText("Total: $symbol".number_format($repair,2,".",","),array("size"=>22,"bold"=>true,"align"=>"right"));
/// datos bancarios

$filename = "repairsreports-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>