<?php

/** Error reporting */
error_reporting(0);


include "../../T&S-SYSTEMS-14.6/core/autoload.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/SellData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ProductData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/OperationTypeData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/ConfigurationData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/PersonData.php";
include "../../T&S-SYSTEMS-14.6/core/app/model/UserData.php";
$symbol = ConfigurationData::getByPreffix("currency")->val;

include "../vendor/autoload.php";
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;

//Autoloader::register();

$word = new  PhpOffice\PhpWord\PhpWord();

$operations = array();
if($_GET["stock_id"]==""){
            $operations = OperationData::getAllByDateOfficial($_GET["sd"],$_GET["ed"]);
            }else{

            $products = ProductData::getAllByCategoryId($_GET["stock_id"],);
            } 
$section1 = $word->AddSection();
$section1->addText("REPORTE DE VENTAS",array("size"=>22,"bold"=>true,"align"=>"right"));


$styleTable = array('borderSize' => 6, 'borderColor' => '888888', 'cellMargin' => 40);
$styleFirstRow = array('borderBottomColor' => '0000FF', 'bgColor' => 'AAAAAA');


$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell()->addText("Id");
$table1->addCell()->addText("Producto");
$table1->addCell()->addText("Entradas");
$table1->addCell()->addText("Precio Entradas");
$table1->addCell()->addText("Salidas");
$table1->addCell()->addText("Precio Salidas");
$table1->addCell()->addText("E-S");
$table1->addCell()->addText("Precio E-S");


foreach($products as $p){
$ni = 0;
$no = 0;
foreach($operations as $o){
    if($o->operation_type_id==1&& $o->product_id==$p->id){ $ni+=$o->q; }
    else if($o->operation_type_id==2&& $o->product_id==$p->id){ $no+=$o->q; }


$table1->addRow();
$table1->addCell(5000)->addText($p->id);
$table1->addCell(5000)->addText($p->name);
$table1->addCell(5000)->addText($ni);
$table1->addCell(2500)->addText($symbol." ".number_format($p->price_in*$ni,2,".",","));
$table1->addCell(5000)->addText($no);
$table1->addCell(2500)->addText($symbol." ".number_format($p->price_in*$no,2,".",","));
$table1->addCell(5000)->addText($ni-$no);
$table1->addCell(2500)->addText($symbol." ".number_format(($p->price_in*$ni)-($p->price_out*$no),2,".",","));

}
}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
/// datos bancarios

$filename = "sellreports-".time().".docx";
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file



?>