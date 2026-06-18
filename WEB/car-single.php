<?php
include "layout/header.php";

$base = new Database();
$con = $base->connect();

$car_id = isset($_GET["car_id"]) ? intval($_GET["car_id"]) : 0;

if($car_id <= 0){
    echo "<div class='container pt-5'><h2>Vehículo no encontrado.</h2></div>";
    include "layout/footer.php";
    exit;
}

$sql = "SELECT * FROM cars WHERE id=$car_id LIMIT 1";
$query = $con->query($sql);

if(!$query || $query->num_rows == 0){
    echo "<div class='container pt-5'><h2>Vehículo no encontrado.</h2></div>";
    include "layout/footer.php";
    exit;
}

$r = $query->fetch_assoc();

/* ================= DATOS ================= */

$brand_name = "";

if(isset($r["brand_id"]) && intval($r["brand_id"]) > 0){

    $brand = BrandData::getById(intval($r["brand_id"]));

    if($brand && isset($brand->name)){
        $brand_name = strtoupper((string)$brand->name);
    }

}

$car_name = isset($r["name"]) ? strtoupper((string)$r["name"]) : "VEHICULO";

$car_year = isset($r["year"]) ? $r["year"] : "";
$car_price = isset($r["price"]) ? floatval($r["price"]) : 0;
$car_description = isset($r["description"]) ? $r["description"] : "";

$car_token = isset($r["token"]) ? $r["token"] : "";

$img_path = "img/default.jpg";

if(isset($r["invoice_file"]) && trim((string)$r["invoice_file"]) != ""){

    $tmp = "../../CF-SYSTEMS/storage/invoice_files/".trim((string)$r["invoice_file"]);

    if(file_exists($tmp)){
        $img_path = $tmp;
    }

}
?>

<style>

.car-single-section{
    padding:120px 0;
    background:#f8fafc;
}

.car-gallery{
    background:#fff;
    border-radius:30px;
    overflow:hidden;
    box-shadow:0 20px 60px rgba(0,0,0,.08);
}

.car-gallery img{
    width:100%;
    height:600px;
    object-fit:cover;
}

.car-info{
    background:#fff;
    border-radius:30px;
    padding:50px;
    box-shadow:0 20px 60px rgba(0,0,0,.08);
}

.car-badge{
    display:inline-flex;
    align-items:center;
    gap:8px;
    background:rgba(255,193,7,.12);
    color:#ffb400;
    padding:10px 18px;
    border-radius:999px;
    font-size:13px;
    font-weight:800;
    margin-bottom:20px;
}

.car-title{
    font-size:55px;
    font-weight:900;
    color:#111827;
    line-height:1.1;
    margin-bottom:20px;
}

.car-description{
    color:#64748b;
    font-size:17px;
    line-height:1.9;
    margin-bottom:35px;
}

.car-price{
    font-size:50px;
    font-weight:900;
    color:#111827;
    margin-bottom:35px;
}

.car-price span{
    font-size:18px;
    color:#64748b;
}

.car-features{
    margin-bottom:40px;
}

.feature-item{
    display:flex;
    align-items:center;
    gap:14px;
    margin-bottom:18px;
    font-size:16px;
    font-weight:700;
    color:#111827;
}

.feature-item i{
    width:45px;
    height:45px;
    border-radius:14px;
    background:rgba(255,193,7,.12);
    color:#ffb400;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
}

.reserve-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    height:65px;
    padding:0 40px;
    border-radius:18px;
    background:#111827;
    color:#fff !important;
    font-size:16px;
    font-weight:900;
    text-transform:uppercase;
    transition:.3s;
    box-shadow:0 18px 40px rgba(0,0,0,.15);
}

.reserve-btn:hover{
    background:var(--main-color);
    transform:translateY(-3px);
}

@media(max-width:991px){

    .car-gallery img{
        height:350px;
    }

    .car-title{
        font-size:38px;
    }

    .car-price{
        font-size:38px;
    }

    .car-info{
        padding:30px;
    }

}

</style>

<section class="car-single-section">

<div class="container">

<div class="row">

<div class="col-lg-7 mb-4">

<div class="car-gallery">

<img 
src="<?php echo htmlspecialchars($tmp, ENT_QUOTES, 'UTF-8'); ?>"
alt="<?php echo htmlspecialchars($car_name, ENT_QUOTES, 'UTF-8'); ?>"
loading="lazy"
>

</div>

</div>

<div class="col-lg-5">

<div class="car-info">

<div class="car-badge">
    <i class="fa fa-car"></i>
    Disponible Ahora
</div>

<h1 class="car-title">

<?php
echo htmlspecialchars(
    trim($brand_name." ".$car_name." ".$car_year),
    ENT_QUOTES,
    'UTF-8'
);
?>

</h1>

<p class="car-description">

<?php
echo $car_description != ""
? nl2br(htmlspecialchars($car_description, ENT_QUOTES, 'UTF-8'))
: "Vehículo premium con excelente confort, tecnología moderna y una experiencia de conducción inolvidable.";
?>

</p>

<div class="car-price">
    US$ <?php echo number_format($car_price,2); ?>
    <span>/ por día</span>
</div>

<div class="car-features">

<div class="feature-item">
    <i class="fa fa-calendar"></i>
    Año: <?php echo htmlspecialchars($car_year, ENT_QUOTES, 'UTF-8'); ?>
</div>

<div class="feature-item">
    <i class="fa fa-tag"></i>
    Ficha: <?php echo htmlspecialchars($car_token, ENT_QUOTES, 'UTF-8'); ?>
</div>

<div class="feature-item">
    <i class="fa fa-car"></i>
    Marca: <?php echo htmlspecialchars($brand_name, ENT_QUOTES, 'UTF-8'); ?>
</div>

</div>

<a href="<?php echo $base_url_safe; ?>reservation.php?car_id=<?php echo $car_id; ?>&from=<?php echo $_GET["from"]; ?>&to=<?php echo $_GET["to"]; ?>" class="reserve-btn">
    Reservar Ahora
</a>

</div>

</div>

</div>

</div>

</section>

<?php include "layout/footer.php";?>