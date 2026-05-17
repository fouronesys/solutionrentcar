<?php
/** Error reporting */
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PaymentData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PaymentTypeData.php";

include "../vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;

//Autoloader::register();

$word = new  PhpOffice\PhpWord\PhpWord();


$client = PersonData::getById($_GET["id"]);
$total = PaymentData::sumByClientId($client->id)->total;


// $clients = PersonData::getClients();
$clients = PaymentData::getAllByClientId($client->id);

$section1 = $word->AddSection();
$section1->addText("HISTORIAL DE PAGOS",array("size"=>22,"bold"=>true,"align"=>"right"));
$section1->addText("Proveedor: ".$client->name." ".$client->lastname,array("size"=>18,"align"=>"right"));


$styleTable = array('borderSize' => 6, 'borderColor' => '888888', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'AAAAAA');

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Tipo");
$table1->addCell()->addText("Valor");
$table1->addCell()->addText("Fecha");

foreach($clients as $client){
$table1->addRow();
$table1->addCell(5000)->addText($client->getPaymentType()->name);
//$table1->addCell(2000)->addText("$". number_format(PaymentData::sumByClientId($client->val)->total,2,".",","));
$table1->addCell(2000)->addText("$". number_format(($client->val),2,".",","));
$table1->addCell(2000)->addText($client->created_at);
$total-=$client->val;
$pd=$total-=$client->val;
}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
$section1->addText("Saldo pendiente: $symbol".number_format($pd,2,".",","),array("size"=>18,"align"=>"right"));

$filename = "paymenthistory-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>