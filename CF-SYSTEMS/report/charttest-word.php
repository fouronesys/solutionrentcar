<?php
include "../vendor/autoload.php";

use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;

$word = new  PhpOffice\PhpWord\PhpWord();

$section1 = $word->AddSection();
$section1->addText("Crear y agregar una gráfica de Barras en PHPWord",array("size"=>40,"bold"=>true));

$categorias = array('Uva', 'Manzana', 'Pera', 'Melon', 'Sandia');
$valores = array(120, 500, 222, 305 , 144);
$colores  = array( '1abc9c', '2c3e50', '9b59b6', 'f1c40f', 'e74c3c' );

$chart = $section1->addChart("bar", $categorias, $valores); // Agregar grafica
$chart->getStyle()->setWidth(160 * 36000); // Asignar ancho
$chart->getStyle()->setHeight(90 * 36000); // Asignar Alto
$chart->getStyle()->setColors( $colores ); // Asignar colores

$chart->getStyle()->setShowGridX(true);
$chart->getStyle()->setShowGridY(true);
$chart->getStyle()->setShowAxisLabels(true);

$filename = "chart-".time().".docx";
$word->save($filename,"Word2007");

header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file

?>