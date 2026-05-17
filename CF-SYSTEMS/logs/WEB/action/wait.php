<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include "../../core/controller/Core.php";
include "../../core/controller/Database.php";
include "../../core/controller/Executor.php";
include "../../core/controller/Model.php";

include "../../core/app/model/StockData.php";
include "../../core/app/model/FuelData.php";
include "../../core/app/model/TransmissionData.php";
include "../../core/app/model/CarsData.php";
include "../../core/app/model/ColorData.php";
include "../../core/app/model/BrandData.php";

// Obtener los datos del formulario
$nombre = strtoupper($_POST['name']);
$correo = strtoupper($_POST['email']);
$no = strtoupper($_POST['no']);

$cars = CarsData::getById($_POST["car_id"]);


// Número de teléfono de WhatsApp (formato internacional sin + ni espacios)

  function limpiarTelefono($telefono) {
    return preg_replace('/\D/', '', $telefono); // Elimina todo excepto dígitos
   }
 
   $telefonoCrudo = StockData::getFPrincipal($_POST['selstock'])->phone; // Ej: "+1 (829) 674-1075"
   $telefonoLimpio = limpiarTelefono($telefonoCrudo);

    // Número de teléfono (incluye el código del país, sin signos + o espacios)
   $telefono = preg_replace('/\D/', '', "+".StockData::getFPrincipal($_POST['selstock'])->phone); // Reemplaza con tu número de WhatsApp


// Armar mensaje
$mensaje = "Hello,

My name is $nombre.

I'm interested in renting this vehicle today.

Brand: " . BrandData::getById($cars->brand_id)->name . "
Model: " . $cars->name . "
Year: " . $cars->year . "
Plate: " . $cars->plate . "
Color: " . ColorData::getById($cars->exterior_id)->name . "

Please confirm availability and requirements so I can complete the reservation as soon as possible.

I look forward to your response.

Thank you.";

// Codifica correctamente todo el texto
$enlace = "https://api.whatsapp.com/send?phone=$telefonoLimpio&text=" . urlencode($mensaje);


// Redirigir
header("Location: $enlace");
exit();
?>
