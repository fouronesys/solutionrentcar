<?php
declare(strict_types=1);

include "../core/controller/Core.php";
include "../core/controller/Database.php";
include "../core/controller/Executor.php";
include "../core/controller/Model.php";

include "../core/app/model/CData.php";
include "../core/app/model/UserData.php";
include "../core/app/model/BookingData.php";
include "../core/app/model/BrandData.php";
include "../core/app/model/FData.php";
include "../core/app/model/SureData.php";
include "../core/app/model/CarsData.php";
include "../core/app/model/ColorData.php";
include "../core/app/model/StockData.php";
include "../core/app/model/PersonData.php";
include "../core/app/model/CategoryData.php";
include "../core/app/model/DeliveryData.php";
include "../core/app/model/PaymentData.php";

require_once "../CF-SYSTEMS/fpdf/fpdf.php";

session_start();

if (isset($_SESSION["user_id"]) && is_numeric($_SESSION["user_id"])) {
    Core::$user = UserData::getById((int)$_SESSION["user_id"]);
}

/* ========================= HELPERS ========================= */
function safeStr($value): string {
    return (string)($value ?? '');
}

function safeInt($value): int {
    return (int)($value ?? 0);
}

function safeFloat($value): float {
    return (float)($value ?? 0);
}

function pdfText($value): string {
    return utf8_decode((string)($value ?? ''));
}

function convertir_fecha_pdf(string $fecha): string {
    $fecha = trim($fecha);

    if ($fecha === '') {
        return date("Y-m-d");
    }

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        return $fecha;
    }

    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fecha)) {
        $partes = explode('/', $fecha);
        return $partes[2] . '-' . $partes[1] . '-' . $partes[0];
    }

    $time = strtotime($fecha);
    if ($time !== false) {
        return date("Y-m-d", $time);
    }

    return date("Y-m-d");
}

function limpiarRutaArchivo(string $ruta): string {
    $ruta = trim($ruta);
    $ruta = ltrim($ruta, "/\\");
    return str_replace(["../", "..\\"], "", $ruta);
}

/* ========================= DB ========================= */
$base = new Database();
$con  = $base->connect();

if (!$con) {
    die("No se pudo conectar a la base de datos.");
}

mysqli_set_charset($con, "utf8");

/* ========================= SUCURSAL ACTIVA ========================= */
$stock_id = isset($_SESSION["stock_id"]) ? (int)$_SESSION["stock_id"] : 0;

if ($stock_id > 0) {
    $stock = StockData::getById($stock_id);
} else {
    $stock = StockData::getPrincipal();
    $stock_id = isset($stock->id) ? (int)$stock->id : 0;
}

if (!$stock) {
    die("No se encontró la sucursal.");
}

$symbol = safeStr($stock->currency);
if ($symbol === "€") {
    $symbol = chr(128);
} elseif ($symbol === "₡") {
    $symbol = "₡";
}

$title   = safeStr($stock->name);
$rnc     = safeStr($stock->rnc);
$address = safeStr($stock->address);
$phone   = safeStr($stock->phone);
$phone2  = safeStr($stock->phone2);
$email   = safeStr($stock->email);
$divisa  = safeStr($stock->divisa);

/* ========================= COLOR DEL SISTEMA ========================= */
$colorRGB = explode(",", safeStr($stock->color));
$r = isset($colorRGB[0]) ? (int)$colorRGB[0] : 52;
$g = isset($colorRGB[1]) ? (int)$colorRGB[1] : 152;
$b = isset($colorRGB[2]) ? (int)$colorRGB[2] : 219;

/* ========================= FECHAS DESDE GET ========================= */
$av_from = isset($_GET["av_from"]) && $_GET["av_from"] !== "" ? (string)$_GET["av_from"] : date("Y-m-d");
$av_to   = isset($_GET["av_to"])   && $_GET["av_to"]   !== "" ? (string)$_GET["av_to"]   : date("Y-m-d");

$av_from = convertir_fecha_pdf($av_from);
$av_to   = convertir_fecha_pdf($av_to);

if (strtotime($av_from) > strtotime($av_to)) {
    $tmp = $av_from;
    $av_from = $av_to;
    $av_to = $tmp;
}

/* ========================= CONSULTA EXACTA DE DISPONIBILIDAD ========================= */
$stock_id_sql = (int)$stock_id;
$av_from_sql  = mysqli_real_escape_string($con, $av_from);
$av_to_sql    = mysqli_real_escape_string($con, $av_to);

$sql = "
    SELECT c.id, c.name, c.plate, c.token, c.price, c.year, c.invoice_file
    FROM cars c
    WHERE c.stock_id = {$stock_id_sql}
      AND NOT EXISTS (
          SELECT 1
          FROM booking b
          WHERE b.car_id = c.id
            AND b.stock_id = {$stock_id_sql}
            AND b.status IN (0,1)
            AND (b.start_at <= '{$av_to_sql}' AND b.end_at >= '{$av_from_sql}')
      )
    ORDER BY c.name ASC
";

$query = mysqli_query($con, $sql);

$av_cars = [];
if ($query instanceof mysqli_result) {
    while ($row = mysqli_fetch_assoc($query)) {
        $av_cars[] = $row;
    }
}

/* ========================= PDF ========================= */
$pdf = new FPDF('P', 'mm', 'Letter');
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);

/* ========================= LOGO ========================= */
$ticket_image = safeStr($stock->ticket_image);
if ($ticket_image !== '') {
    $src = "../CF-SYSTEMS/storage/configuration/" . limpiarRutaArchivo($ticket_image);
    if (file_exists($src)) {
        $pdf->Image($src, 10, 8, 35);
    }
}

/* ========================= ENCABEZADO ========================= */
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor($r, $g, $b);
$pdf->Cell(0, 8, pdfText(strtoupper($title)), 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0, 0, 0);

if ($address !== '') {
    $pdf->Cell(0, 6, pdfText($address), 0, 1, 'C');
}
if ($phone !== '' || $phone2 !== '') {
    $telefonos = "Tel: " . $phone . ($phone2 !== '' ? " / " . $phone2 : "");
    $pdf->Cell(0, 6, pdfText($telefonos), 0, 1, 'C');
}
if ($email !== '') {
    $pdf->Cell(0, 6, $email, 0, 1, 'C');
}

$pdf->Ln(4);

/* ========================= TITULO ========================= */
$pdf->SetFillColor($r, $g, $b);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(0, 10, pdfText("VEHÍCULOS DISPONIBLES"), 0, 1, 'C', true);

$pdf->Ln(3);

/* ========================= FECHAS ========================= */
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 7, pdfText("Desde:"), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(55, 7, date("d-m-Y", strtotime($av_from)), 0, 0, 'L');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 7, "Hasta:", 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, date("d-m-Y", strtotime($av_to)), 0, 1, 'L');

$pdf->Ln(4);

/* ========================= TOTAL ========================= */
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(45, 7, "Total disponibles:", 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, (string)count($av_cars), 0, 1, 'L');

$pdf->Ln(4);

/* ========================= TABLA ========================= */
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor($r, $g, $b);
$pdf->SetTextColor(255, 255, 255);

$pdf->Cell(25, 8, "Foto", 1, 0, 'C', true);
$pdf->Cell(40, 8, "Marca", 1, 0, 'C', true);
$pdf->Cell(45, 8, "Modelo", 1, 0, 'C', true);
$pdf->Cell(25, 8, "Categoria", 1, 0, 'C', true);
$pdf->Cell(20, 8, pdfText("Año"), 1, 0, 'C', true);
$pdf->Cell(20, 8, "Placa", 1, 0, 'C', true);
$pdf->Cell(20, 8, "Precio", 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 8);

if (count($av_cars) > 0) {
    foreach ($av_cars as $car) {
        $carId = isset($car["id"]) ? (int)$car["id"] : 0;
        $carObj = $carId > 0 ? CarsData::getById($carId) : null;

        $brandName = "";
        $categoryName = "";

        if ($carObj) {
            if (method_exists($carObj, "getBrand")) {
                $brand = $carObj->getBrand();
                if ($brand) {
                    $brandName = safeStr($brand->name);
                }
            }

            if (method_exists($carObj, "getCategory")) {
                $category = $carObj->getCategory();
                if ($category) {
                    $categoryName = safeStr($category->name);
                }
            }
        }

        $price = isset($car["price"]) ? number_format((float)$car["price"], 2, ".", ",") : "0.00";

        $y = $pdf->GetY();
        $x = $pdf->GetX();

        $img = "";
        if (!empty($car["invoice_file"])) {
            $img = "../CF-SYSTEMS/storage/invoice_files/" . limpiarRutaArchivo((string)$car["invoice_file"]);
        }

        if ($y + 20 > 260) {
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor($r, $g, $b);
            $pdf->SetTextColor(255, 255, 255);

            $pdf->Cell(25, 8, "Foto", 1, 0, 'C', true);
            $pdf->Cell(40, 8, "Marca", 1, 0, 'C', true);
            $pdf->Cell(45, 8, "Modelo", 1, 0, 'C', true);
            $pdf->Cell(25, 8, "Categoria", 1, 0, 'C', true);
            $pdf->Cell(20, 8, pdfText("Año"), 1, 0, 'C', true);
            $pdf->Cell(20, 8, "Placa", 1, 0, 'C', true);
            $pdf->Cell(20, 8, "Precio", 1, 1, 'C', true);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 8);

            $y = $pdf->GetY();
            $x = $pdf->GetX();
        }

        if ($img !== "" && file_exists($img)) {
            $pdf->Cell(25, 20, "", 1, 0, 'C');
            $pdf->Image($img, $x + 2, $y + 2, 21, 16);
        } else {
            $pdf->Cell(25, 20, "Sin foto", 1, 0, 'C');
        }

        $pdf->Cell(40, 20, pdfText(substr($brandName, 0, 15)), 1, 0, 'L');
        $pdf->Cell(45, 20, pdfText(substr(safeStr($car["name"] ?? ''), 0, 25)), 1, 0, 'L');
        $pdf->Cell(25, 20, pdfText(substr($categoryName, 0, 12)), 1, 0, 'L');
        $pdf->Cell(20, 20, safeStr($car["year"] ?? ''), 1, 0, 'C');
        $pdf->Cell(20, 20, pdfText(substr(safeStr($car["plate"] ?? ''), 0, 10)), 1, 0, 'C');
        $pdf->Cell(20, 20, $price, 1, 1, 'R');
    }
} else {
    $pdf->Cell(195, 10, pdfText("No hay vehículos disponibles en ese rango de fechas."), 1, 1, 'C');
}

$pdf->Ln(8);

/* ========================= PIE ========================= */
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(90, 90, 90);
$pdf->MultiCell(
    0,
    5,
    pdfText("Documento generado automáticamente. Rango consultado: " . date("d-m-Y", strtotime($av_from)) . " hasta " . date("d-m-Y", strtotime($av_to))),
    0,
    'C'
);

$pdf->Output();
?>