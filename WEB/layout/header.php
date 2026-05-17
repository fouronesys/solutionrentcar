<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . "/../lang.php";

/* ================= INCLUDES SEGUROS PHP 8.4 ================= */
include_once __DIR__ . "/../../core/controller/Core.php";
include_once __DIR__ . "/../../core/controller/Database.php";
include_once __DIR__ . "/../../core/controller/Executor.php";
include_once __DIR__ . "/../../core/controller/Model.php";

include_once __DIR__ . "/../../core/app/model/StockData.php";
include_once __DIR__ . "/../../core/app/model/PersonData.php";
include_once __DIR__ . "/../../core/app/model/FuelData.php";
include_once __DIR__ . "/../../core/app/model/TransmissionData.php";
include_once __DIR__ . "/../../core/app/model/CategoryData.php";
include_once __DIR__ . "/../../core/app/model/UserData.php";
include_once __DIR__ . "/../../core/app/model/KData.php";
include_once __DIR__ . "/../../core/app/model/CarsData.php";
include_once __DIR__ . "/../../core/app/model/ColorData.php";
include_once __DIR__ . "/../../core/app/model/BrandData.php";
include_once __DIR__ . "/../../core/app/model/BookingData.php";

/* ================= URL ================= */

$WEBRL = $_SERVER['HTTP_HOST'] ?? '';
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";

$home = $protocol . $WEBRL;

/* ================= BASE URL AUTOMATICA ================= */

$request_uri = $_SERVER['REQUEST_URI'] ?? '';

if (
    strpos($request_uri, '/WEB/') !== false
) {

    $base_url = $protocol . $WEBRL . "/WEB/";

} else {

    $base_url = $protocol . $WEBRL . "/WEB/";

}

/* ================= CONFIG ================= */
$selstock     = 0;
$title        = "";
$ticket_image = "";
$web_type     = "";
$webimg       = "";
$text         = "";
$type_img     = "";

$WEBRL_SAFE = addslashes($WEBRL);
$home_SAFE  = addslashes($home);

$stocks = StockData::getAllBySQL("
    WHERE web_url = '$WEBRL_SAFE'
    OR web_url2 = '$WEBRL_SAFE'
    OR web_url = '$home_SAFE'
    OR web_url2 = '$home_SAFE'
    LIMIT 1
");

if (!empty($stocks)) {

    $s = $stocks[0];

    $selstock = isset($s->id) ? intval($s->id) : 0;
    $phone = isset($s->phone) ? $s->phone : "";
    $web_type = isset($s->web_type) ? $s->web_type : "";
    $webimg   = isset($s->web_img) ? $s->web_img : "";
    $text     = isset($s->web_text) ? $s->web_text : "";
    $title    = isset($s->name) && trim((string)$s->name) != "" ? $s->name : "RENT CAR";
    $type_img = isset($s->type_img) ? $s->type_img : "";

    if (isset($s->ticket_image) && trim((string)$s->ticket_image) != "") {
        $ticket_image = trim((string)$s->ticket_image);
    }

    if (isset($s->color) && trim((string)$s->color) != "") {
        $tmp_color = explode(",", $s->color);

        if (count($tmp_color) >= 3) {
            $color = array(
                intval($tmp_color[0]),
                intval($tmp_color[1]),
                intval($tmp_color[2])
            );
        }
    }
}

$mainColor = intval($color[0]) . "," . intval($color[1]) . "," . intval($color[2]);


$color = array($mainColor);

$title_safe        = htmlspecialchars((string)$title, ENT_QUOTES, 'UTF-8');
$ticket_image_safe = htmlspecialchars((string)$ticket_image, ENT_QUOTES, 'UTF-8');
$base_url_safe     = htmlspecialchars((string)$base_url, ENT_QUOTES, 'UTF-8');

$version = time();
?>

<!DOCTYPE html>
<html lang="<?php echo $LANG; ?>">
<head>

<title><?php echo $title_safe; ?></title>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<link href="../../CF-SYSTEMS/storage/configuration/<?php echo $ticket_image_safe; ?>?v=<?php echo $version; ?>" rel="shortcut icon">

<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/bootstrap.min.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/open-iconic-bootstrap.min.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/animate.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/owl.carousel.min.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/owl.theme.default.min.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/magnific-popup.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/aos.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/ionicons.min.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/bootstrap-datepicker.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/jquery.timepicker.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/flaticon.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/icomoon.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/style.css?v=<?php echo $version; ?>">

<style>

:root{
    --main-color:rgb(<?php echo $mainColor; ?>);
}

*{
    font-family:Arial, Helvetica, sans-serif;
}

html,
body{
    background:#ffffff !important;
    color:#111827 !important;
    margin:0;
    padding:0;
    overflow-x:hidden;
}

body{
    padding-top:105px !important;
}

/* ================= FIX GENERAL PHP 8.4 ================= */

.ftco-section{
    position:relative !important;
    display:block !important;
    visibility:visible !important;
    opacity:1 !important;
    overflow:visible !important;
    background:#fff;
}

.section-mini,
.section-title,
.section-subtitle{
    display:block !important;
    visibility:visible !important;
    opacity:1 !important;
    position:relative !important;
    z-index:20 !important;
}

.section-mini{
    color:var(--main-color) !important;
}

.section-title{
    color:#111827 !important;
}

.section-subtitle{
    color:#64748b !important;
}

/* ================= NAVBAR ================= */

#ftco-navbar{
    background:#ffffff !important;
    border-bottom:1px solid rgba(<?php echo $mainColor; ?>,0.06);
    padding:22px 0;
    transition:.3s;
    position:fixed !important;
    top:0;
    left:0;
    right:0;
    z-index:9999 !important;
    box-shadow:0 10px 40px rgba(0,0,0,.04);
}

.navbar-brand{
    display:flex !important;
    align-items:center;
    gap:14px;
    color:#111827 !important;
    font-size:28px;
    font-weight:900;
    letter-spacing:-1px;
    text-decoration:none !important;
}

.logo-box{
    width:58px;
    height:58px;
    border-radius:18px;
    background:#fff;
    overflow:hidden;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 10px 30px rgba(<?php echo $mainColor; ?>,0.10);
    flex:none;
}

.logo-box img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.brand-mini{
    display:block;
    font-size:11px;
    color:var(--main-color) !important;
    font-weight:800;
    letter-spacing:2px;
    margin-bottom:2px;
    text-transform:uppercase;
}

.navbar-dark .navbar-nav .nav-link{
    color:#111827 !important;
    font-size:15px;
    font-weight:700;
    padding:14px 18px !important;
    border-radius:999px;
    transition:.3s;
}

.navbar-dark .navbar-nav .nav-link:hover{
    color:var(--main-color) !important;
    background:rgba(<?php echo $mainColor; ?>,0.08);
}

.navbar-dark .navbar-nav .active>.nav-link{
    color:var(--main-color) !important;
}

/* ================= BUTTON ================= */

.btn-book{
    background:#111827 !important;
    color:#ffffff !important;
    border-radius:999px !important;
    padding:14px 30px !important;
    margin-left:10px;
    box-shadow:0 15px 35px rgba(0,0,0,.18);
    min-width:135px;
    text-align:center !important;
    display:inline-flex !important;
    align-items:center !important;
    justify-content:center !important;
    font-size:14px !important;
    font-weight:900 !important;
    line-height:1 !important;
    height:auto !important;
    text-transform:uppercase;
    white-space:nowrap;
    opacity:1 !important;
    visibility:visible !important;
}

.navbar-dark .navbar-nav .nav-link.btn-book{
    color:#ffffff !important;
}

.navbar-dark .navbar-nav .nav-link.btn-book:hover{
    color:#ffffff !important;
    background:var(--main-color) !important;
    transform:translateY(-2px);
}

.btn-book:hover{
    background:var(--main-color) !important;
    transform:translateY(-2px);
}

/* ================= MOBILE ================= */

.navbar-toggler{
    width:60px;
    height:60px;
    border:none;
    border-radius:20px;
    background:#0b132b;
    display:flex;
    align-items:center;
    justify-content:center;
}

.navbar-toggler i{
    color:#fff;
    font-size:28px;
    font-weight:normal !important;
}

/* ================= LANG SWITCHER ================= */

.lang-switcher{
    display:flex;
    align-items:center;
    gap:6px;
    margin-left:10px;
}

.lang-btn{
    display:inline-flex !important;
    align-items:center;
    gap:4px;
    padding:8px 14px !important;
    border-radius:999px !important;
    font-size:13px !important;
    font-weight:800 !important;
    color:#111827 !important;
    background:rgba(0,0,0,0.05) !important;
    text-decoration:none;
    transition:.25s;
    border:1.5px solid transparent;
}

.lang-btn:hover{
    background:rgba(<?php echo $mainColor; ?>,0.12) !important;
    color:var(--main-color) !important;
    border-color:rgba(<?php echo $mainColor; ?>,0.20);
}

.lang-btn.lang-active{
    background:var(--main-color) !important;
    color:#fff !important;
    border-color:var(--main-color);
}

@media(max-width:991px){

    .lang-switcher{
        margin-left:0;
        margin-top:8px;
        justify-content:center;
    }

}

@media(max-width:991px){

    body{
        padding-top:90px !important;
    }

    #ftco-navbar{
        padding:16px 0;
    }

    .navbar-brand{
        font-size:18px;
    }

    .logo-box{
        width:45px;
        height:45px;
        border-radius:14px;
    }

    .navbar-collapse{
        background:#fff;
        margin-top:18px;
        padding:18px;
        border-radius:25px;
        box-shadow:0 20px 50px rgba(0,0,0,.08);
    }

    .navbar-dark .navbar-nav .nav-link{
        margin-bottom:8px;
    }

    .btn-book{
        margin-left:0;
        margin-top:10px;
        text-align:center;
    }

}

</style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar ftco-navbar-light" id="ftco-navbar">

<div class="container">
<a class="navbar-brand" href="<?php echo $base_url_safe; ?>">

    <div class="logo-box">
        <img 
        src="../../CF-SYSTEMS/storage/configuration/<?php echo $ticket_image_safe; ?>?v=<?php echo $version; ?>"
        onerror="this.onerror=null; this.src='img/default.jpg';"
        alt="<?php echo $title_safe; ?>"
        loading="lazy"
        decoding="async"
        >
    </div>

    <div>
        <span class="brand-mini">
           <?php echo "PHONE: ".$phone; ?>
        </span>

        <?php echo strtoupper($title_safe); ?>
    </div>

</a>

<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav">

    <i class="fa fa-stream"></i>

</button>

<div class="collapse navbar-collapse" id="ftco-nav">

<ul class="navbar-nav ml-auto align-items-lg-center">

<li class="nav-item active">
    <a href="<?php echo $base_url_safe; ?>" class="nav-link">
        <?php echo __('nav_home'); ?>
    </a>
</li>

<li class="nav-item">
    <a href="<?php echo $base_url_safe; ?>cars.php" class="nav-link">
        <?php echo __('nav_cars'); ?>
    </a>
</li>

<li class="nav-item">
    <a href="<?php echo $base_url_safe; ?>about.php" class="nav-link">
        <?php echo __('nav_about'); ?>
    </a>
</li>

<li class="nav-item">
    <a href="<?php echo $base_url_safe; ?>services.php" class="nav-link">
        <?php echo __('nav_services'); ?>
    </a>
</li>

<li class="nav-item">
    <a href="<?php echo $base_url_safe; ?>contact.php" class="nav-link">
        <?php echo __('nav_contact'); ?>
    </a>
</li>

<li class="nav-item lang-switcher">
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    $query = $_GET;
    $query['lang'] = 'es';
    $qs_es = http_build_query($query);
    $query['lang'] = 'en';
    $qs_en = http_build_query($query);
    ?>
    <a href="?<?php echo $qs_es; ?>" class="lang-btn <?php echo ($LANG === 'es') ? 'lang-active' : ''; ?>">
        🇩🇴 ES
    </a>
    <a href="?<?php echo $qs_en; ?>" class="lang-btn <?php echo ($LANG === 'en') ? 'lang-active' : ''; ?>">
        🇺🇸 EN
    </a>
</li>

</ul>

</div>

</div>

</nav>