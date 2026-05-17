<?php
// ===============================
// AJUSTADO PARA PHP 8.4 SIN BLOQUEARSE
// ===============================

// NO mostrar errores en pantalla porque daña el PDF
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Limpiar salida previa
if (ob_get_length()) {
	ob_end_clean();
}
ob_start();

session_start();

include "../core/controller/Core.php";
include "../core/controller/Database.php";
include "../core/controller/Executor.php";
include "../core/controller/Model.php";

include "../core/app/model/PaymentData.php";
include "../core/app/model/PaymentTypeData.php";
include "../core/app/model/BookingData.php";
include "../core/app/model/StockData.php";
include "../core/app/model/PersonData.php";
include "../core/app/model/UserData.php";
include "../core/app/model/BrandData.php";
include "../core/app/model/CarsData.php";
include "../core/app/model/ColorData.php";
include "../core/app/model/CategoryData.php";
include "../CF-SYSTEMS/fpdf/fpdf.php";

// ===============================
// HELPERS
// ===============================
function safe_text($text){
	if ($text === null) return "";
	return trim((string)$text);
}

function safe_upper($text){
	$text = safe_text($text);
	if ($text === "") return "";
	if (function_exists('mb_strtoupper')) {
		return mb_strtoupper($text, 'UTF-8');
	}
	return strtoupper($text);
}

function pdf_text($text){
	$text = safe_text($text);
	if ($text === "") return "";
	
	// Más seguro que utf8_decode en PHP 8.4
	$converted = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $text);
	if ($converted === false) {
		return $text;
	}
	return $converted;
}

function moneyf($n){
	return number_format((float)$n, 2, ".", ",");
}

// ===============================
// USUARIO
// ===============================
if (isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"])) {
	Core::$user = UserData::getById($_SESSION["user_id"]);
}

// ===============================
// DATOS PRINCIPALES
// ===============================
$stock = StockData::getPrincipal();
if (!$stock) {
	ob_end_clean();
	die("No se encontro la sucursal principal.");
}

$symbol = isset($stock->currency) ? $stock->currency : "RD$";
if ($symbol == "€") {
	$symbol = chr(128);
} else if ($symbol == "₡") {
	$symbol = "₡";
}

$client_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($client_id <= 0) {
	ob_end_clean();
	die("Cliente no valido.");
}

$client = PersonData::getById($client_id);
if (!$client) {
	ob_end_clean();
	die("Cliente no encontrado.");
}

$clients = PaymentData::getAllByClientId2($client->id);
if (!is_array($clients)) {
	$clients = array();
}

// ===============================
// PDF
// ===============================
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Si el header falla, no tumbes todo el archivo
$ticketHeaderPath = '../core/app/layouts/ticketheader.php';
if (file_exists($ticketHeaderPath)) {
	include($ticketHeaderPath);
}

// ===============================
// ENCABEZADO
// ===============================
$pdf->Ln(23);
$pdf->setX(6);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->setX(108);
$pdf->Cell(5,15,'____________________________________________________________');

$pdf->Ln(10.5);
$pdf->setX(2);
$pdf->MultiCell(200,2,pdf_text('HISTORIAL DE PAGO DEL CLIENTE'),0,'C');

$pdf->ln(-7.5);
$pdf->setX(6);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->setX(108);
$pdf->Cell(5,15,'____________________________________________________________');

$pdf->Ln(10);
$pdf->setX(6);
$pdf->MultiCell(200,2.5,pdf_text("NOMBRE: ".safe_upper(isset($client->name) ? $client->name : "")), 0, 'L');

$pdf->Ln(-5);
$pdf->setX(6);
$pdf->Cell(5,15,pdf_text("TEL: ".safe_upper(isset($client->phone) ? $client->phone : "")));

$pdf->Ln(10);
$pdf->setX(6);
$pdf->MultiCell(200,2.5,pdf_text("DIRECCION: ".safe_upper(isset($client->address) ? $client->address : "")), 0, 'L');

$pdf->Ln(-7);
$pdf->setX(6);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->setX(108);
$pdf->Cell(5,15,'____________________________________________________________');

$pdf->Ln(4);
$pdf->setX(5);
$pdf->Cell(5,15,pdf_text('  TIPO                                                                                                                         VALOR'));

$pdf->Ln(1);
$pdf->setX(6);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->setX(108);
$pdf->Cell(5,15,'____________________________________________________________');

// ===============================
// PAGOS
// ===============================
foreach($clients as $clien){

	if(!$clien){ continue; }

	$tipo = "PAGO";
	if (method_exists($clien, 'getPaymentType')) {
		$pt = $clien->getPaymentType();
		if ($pt && isset($pt->name) && $pt->name != "") {
			$tipo = $pt->name;
		}
	}

	$valor = isset($clien->val) ? abs((float)$clien->val) : 0;
	$fecha = isset($clien->created_at) ? $clien->created_at : "";

	$pdf->Ln(6);
	$pdf->setX(6);
	$pdf->Cell(5,15,pdf_text(safe_upper($tipo).":"));

	$pdf->setX(108);
	$pdf->Cell(5,15,$symbol." ".moneyf($valor));

	$pdf->setX(160);
	$pdf->Cell(5,15,pdf_text("FECHA: ".safe_upper($fecha)));
	$pdf->Ln(3);
}

// ===============================
// SALDO PENDIENTE
// ===============================
$sells = BookingData::getCreditByClientId($client->id,$stock->id);
$sellx = BookingData::getCreditByCarsId($client->id,$stock->id);

if (!is_array($sells)) { $sells = array(); }
if (!is_array($sellx)) { $sellx = array(); }

$total = 0;

foreach ($sells as $sell) {
	if (!$sell || !isset($sell->id)) { continue; }

	$sumObj = PaymentData::sumBySellId2($sell->id,$stock->id);
	$tx = 0;

	if ($sumObj && isset($sumObj->total)) {
		$tx = (float)$sumObj->total;
	}

	if($tx > 0){
		$total += $tx;
	}
}

$pdf->Ln(3);
$pdf->setX(6);
$pdf->Cell(5,15,pdf_text("SALDO PENDIENTE: "));

$pdf->setX(108);
$pdf->Cell(5,15,$symbol." ".moneyf($total));

$pdf->Ln(1);
$pdf->setX(6);
$pdf->Cell(5,15,'_______________________________________________________________');

$pdf->setX(108);
$pdf->Cell(5,15,'____________________________________________________________');

$pdf->Ln(4);
$pdf->setX(6);
$pdf->Cell(5,15,pdf_text("SUCURSAL: ".safe_upper(isset($stock->name) ? $stock->name : "")));

$atendido_por = "";
if (isset(Core::$user) && Core::$user) {
	$atendido_por = trim(
		(isset(Core::$user->name) ? Core::$user->name : "") . " " .
		(isset(Core::$user->lastname) ? Core::$user->lastname : "")
	);
}

$pdf->setX(108);
$pdf->Cell(5,15,pdf_text('ATENDIDO POR: '.safe_upper($atendido_por)));

// ===============================
// VEHICULOS
// ===============================
$pdf->Ln(10);
$pdf->setX(6);
$pdf->Cell(5,15,pdf_text("VEHICULOS UTILIZADOS:"));

$pdf->Ln(-8);

foreach ($sellx as $x){

	if (!$x || !isset($x->car_id)) { continue; }

	$cars = CarsData::getById($x->car_id);
	if (!$cars) { continue; }

	$brand_name = "";
	if (method_exists($cars, 'getBrand')) {
		$brand = $cars->getBrand();
		if ($brand && isset($brand->name)) {
			$brand_name = $brand->name;
		}
	}

	$car_name  = isset($cars->name) ? $cars->name : "";
	$car_year  = isset($cars->year) ? $cars->year : "";
	$car_plate = isset($cars->plate) ? $cars->plate : "";

	$texto_vehiculo = trim($brand_name." ".$car_name." ".$car_year." (".$car_plate.")");

	$pdf->setX(6);
	$pdf->Cell(5,51,pdf_text(safe_upper($texto_vehiculo)));
	$pdf->Ln(6);
}

// ===============================
// FIRMA
// ===============================
$pdf->Ln(30);
$pdf->setX(80);
$pdf->Cell(5,15,'_______________________________');

$pdf->Ln(4);
$pdf->setX(87);
$pdf->Cell(5,15,pdf_text('FIRMA DEL PROPIETARIO'));

// ===============================
// SALIDA PDF
// ===============================
if (ob_get_length()) {
	ob_end_clean();
}

$pdf->Output();
exit;
?>