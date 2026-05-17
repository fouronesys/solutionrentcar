<!DOCTYPE html>
<html lang="en">
<?php 
include "../core/controller/Core.php";
include "../core/controller/Database.php";
include "../core/controller/Executor.php";
include "../core/controller/Model.php";


include "../core/app/model/StockData.php";
include "../core/app/model/PersonData.php";
include "../core/app/model/FuelData.php";
include "../core/app/model/TransmissionData.php";
include "../core/app/model/CategoryData.php";
include "../core/app/model/BookingData.php";
include "../core/app/model/UserData.php";
include "../core/app/model/KData.php";
include "../core/app/model/CarsData.php";
include "../core/app/model/ColorData.php";
include "../core/app/model/BrandData.php";

$WEBRL = $_SERVER['HTTP_HOST'];// URL actual, por ejemplo: edysa.com.do
$home = "https://".$WEBRL;

$stocks = StockData::getAllBySQL("WHERE web_url2 = '$WEBRL'");

if (!empty($stocks)) {
    foreach ($stocks as $s):
        $selstock = $s->id;

        // Capturar carpeta si existe en la URL (por ejemplo: /EDYSA-RENTCAR/WEB/)
        if (preg_match('#/([^/]+)/WEB/#', $_SERVER['REQUEST_URI'], $matches)) {
            $xhost = $s->web_url . '/' . $matches[1] . '/';
        } else {
            // Si no hay coincidencia, usar el nombre del tercer folder
            $path = dirname(__DIR__, 2);
            $thirdFolder = basename($path);
            $xhost = $s->web_url . '/' . $thirdFolder . '/';
        }

        // Variables asignadas desde el registro correspondiente
        $web_type = $s->web_type;
        $webimg = $s->web_img;
        $text = $s->web_text;
        $title = $s->name;
        $type_img = $s->type_img;
        $ticket_image = $s->ticket_image;
        $color = explode(",", $s->color);
    endforeach;
} else {
    // Si no hay coincidencia, puedes mostrar error o usar valores por defecto
    echo "No se encontró configuración para la URL: $WEBRL";
}
?>

<html lang="en">
  <head>
    <title><?php echo $title;?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">

    <link rel="stylesheet" href="css/aos.css">

    <link rel="stylesheet" href="css/ionicons.min.css">

    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/jquery.timepicker.css">

    
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  </head>
  <body>
    
	  <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
	    <div class="container">
	      <a class="navbar-brand" href="/"><?php echo $title;?></a>
	      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
	        <span class="oi oi-menu"></span> Menu
	      </button>

	      <div class="collapse navbar-collapse" id="ftco-nav">
	        <ul class="navbar-nav ml-auto">
	          <li class="nav-item active"><a href="/" class="nav-link">HOME</a></li>
	          <li class="nav-item"><a href="/"  class="nav-link">PRICING</a></li>
	          <li class="nav-item"><a href="car.php" class="nav-link">CARS</a></li>
	          <li class="nav-item"><a href="contact.php" class="nav-link">CONTACT</a></li>
	        </ul>
	      </div>
	    </div>
	  </nav>
    <!-- END nav -->
    


    