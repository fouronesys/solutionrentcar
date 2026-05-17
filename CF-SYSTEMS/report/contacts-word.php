<?php
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";

include "../vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;


$word = new  PhpOffice\PhpWord\PhpWord();
$clients = PersonData::getContacts();


$section1 = $word->AddSection();
$section1->addText("CONTACTOS",array("size"=>22,"bold"=>true,"align"=>"right"));


$styleTable = array('borderSize' => 6, 'borderColor' => '888888', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'AAAAAA');

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Nombre");
$table1->addCell()->addText("Direccion");
$table1->addCell()->addText("Email");
$table1->addCell()->addText("Telefono");
foreach($clients as $client){
$table1->addRow();
$table1->addCell(5000)->addText($client->name." ".$client->lastname);
$table1->addCell(2500)->addText($client->address1);
$table1->addCell(2000)->addText($client->email1);
$table1->addCell(2000)->addText($client->phone1);

}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
/// datos bancarios

$filename = "contacts-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>