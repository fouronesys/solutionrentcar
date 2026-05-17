<?php
error_reporting(0);

include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/UserData.php";

include "../vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;


$word = new  PhpOffice\PhpWord\PhpWord();
$operations = array();

			$operations = SellData::getSQL("select *,sum(total-discount) as st from sell where date(created_at) >= \"$_GET[sd]\" and date(created_at) <= \"$_GET[ed]\" and operation_type_id=2 and is_draft=0 and p_id=1 and d_id=1 and user_id is not NULL group by user_id order by st desc");


			 if(count($operations)>0){
			 	$supertotal = 0;}

$section1 = $word->AddSection();
$section1->addText("VENDEDORES POPULARES",array("size"=>22,"bold"=>true,"align"=>"right"));


$styleTable = array('borderSize' => 6, 'borderColor' => '888888', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'AAAAAA');

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Id Vendedor");
$table1->addCell()->addText("Total Venta");
$table1->addCell()->addText("Vendedor");

foreach($operations as $operation){
$table1->addRow();
$table1->addCell(5000)->addText($operation->user_id);
$table1->addCell(5000)->addText($symbol." ".number_format($operation->st,2,".",","));
$table1->addCell(5000)->addText($operation->getUser()->name." ".$operation->getUser()->lastname);
}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
/// datos bancarios

$filename = "topvendor-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>