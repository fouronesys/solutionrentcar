<?php include "layout/header.php";?>

<style>

:root{
  --main-color:rgb(<?php echo $mainColor; ?>);
  --dark:#0f172a;
  --soft:#f8fafc;
  --text:#111827;
  --muted:#64748b;
  --border:rgba(<?php echo $mainColor; ?>,0.08);
}

body{
  background:#ffffff;
  color:var(--text);
}

/* ================= HERO ================= */

.hero-wrap{
  min-height:100vh !important;
  background-position:center center !important;
  background-size:cover !important;
  position:relative;
}

.hero-wrap .overlay{
  background:linear-gradient(
    to right,
    rgba(0,0,0,.78),
    rgba(0,0,0,.45),
    rgba(0,0,0,.15)
  ) !important;
}

.hero-wrap .slider-text{
  min-height:100vh !important;
  display:flex !important;
  align-items:center !important;
}

.hero-content{
  max-width:720px;
}

.hero-mini{
  display:inline-flex;
  align-items:center;
  gap:10px;
  background:rgba(255,255,255,.12);
  border:1px solid rgba(255,255,255,.18);
  backdrop-filter:blur(12px);
  color:#fff;
  padding:10px 18px;
  border-radius:999px;
  font-size:13px;
  font-weight:700;
  margin-bottom:25px;
}

.hero-title{
  color:#fff;
  font-size:74px;
  font-weight:900;
  line-height:1;
  letter-spacing:-3px;
  margin-bottom:25px;
}

.hero-title span{
  color:var(--main-color);
}

.hero-subtitle{
  color:rgba(255,255,255,.82);
  font-size:19px;
  line-height:1.8;
  margin-bottom:35px;
  max-width:620px;
}

.hero-buttons{
  display:flex;
  align-items:center;
  gap:18px;
  flex-wrap:wrap;
}

.hero-btn{
  background:var(--main-color);
  color:#fff !important;
  border-radius:999px;
  padding:18px 34px;
  font-size:15px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.5px;
  border:none;
  transition:.3s;
  box-shadow:0 18px 40px rgba(<?php echo $mainColor; ?>,0.28);
}

.hero-btn:hover{
  transform:translateY(-3px);
  background:#fff;
   color:#000 !important;
}

.hero-btn-outline{
  border:1px solid rgba(255,255,255,.22);
  color:#fff !important;
  border-radius:999px;
  padding:18px 34px;
  font-size:15px;
  font-weight:700;
  backdrop-filter:blur(12px);
  transition:.3s;
}

.hero-btn-outline:hover{
  background:#fff;
  color:#111827 !important;
}

/* ================= FLOATING SEARCH ================= */

.hero-search{
  margin-top:-80px;
  position:relative;
  z-index:20;
}

.hero-search-box{
  background:#fff;
  border-radius:32px;
  padding:30px;
  box-shadow:0 30px 80px rgba(<?php echo $mainColor; ?>,0.12);
  border:1px solid rgba(<?php echo $mainColor; ?>,0.05);
}

.hero-search-box .form-control{
  height:58px;
  border-radius:18px;
  border:1px solid rgba(<?php echo $mainColor; ?>,0.08);
  background:#f8fafc;
  box-shadow:none !important;
  padding-left:18px;
  font-weight:600;
}

.hero-search-box label{
  font-size:13px;
  font-weight:800;
  color:#111827;
  margin-bottom:10px;
}

.search-btn{
  height:58px;
  width:100%;
  border:none;
  border-radius:18px;
  background:var(--main-color);
  color:#fff;
  font-weight:800;
  font-size:15px;
  box-shadow:0 18px 40px rgba(<?php echo $mainColor; ?>,0.22);
}

/* ================= SECTIONS ================= */

.ftco-section{
  position:relative !important;
  display:block !important;
  width:100%;
  padding:110px 0;
  background:#fff;
  overflow:visible !important;
  z-index:2;
}

#cars{
  position:relative !important;
  display:block !important;
  visibility:visible !important;
  opacity:1 !important;
  z-index:5 !important;
  background:#fff !important;
  padding-top:140px !important;
}

#cars .container{
  position:relative;
  z-index:10;
}

#cars .row{
  display:flex !important;
  flex-wrap:wrap !important;
  visibility:visible !important;
  opacity:1 !important;
}

#cars .col-lg-8{
  display:block !important;
  visibility:visible !important;
  opacity:1 !important;
}

.section-title{
  display:block !important;
  visibility:visible !important;
  opacity:1 !important;
  font-size:52px;
  font-weight:900;
  color:#111827 !important;
  letter-spacing:-2px;
  margin-bottom:18px;
  position:relative;
  z-index:10;
}

.section-subtitle{
  display:block !important;
  visibility:visible !important;
  opacity:1 !important;
  color:var(--muted) !important;
  font-size:17px;
  line-height:1.8;
  max-width:700px;
  margin:auto;
  position:relative;
  z-index:10;
}

.section-mini{
  display:block !important;
  visibility:visible !important;
  opacity:1 !important;
  color:var(--main-color) !important;
  font-size:13px;
  font-weight:900;
  letter-spacing:2px;
  text-transform:uppercase;
  margin-bottom:15px;
  position:relative;
  z-index:10;
}

/* ================= CARS ================= */

.raffle-card{
  background:#fff;
  border-radius:28px;
  overflow:hidden;
  border:1px solid rgba(<?php echo $mainColor; ?>,0.06);
  transition:.35s;
  height:100%;
  box-shadow:0 15px 40px rgba(<?php echo $mainColor; ?>,0.05);
}

.raffle-card:hover{
  transform:translateY(-8px);
  box-shadow:0 30px 80px rgba(<?php echo $mainColor; ?>,0.12);
}

.raffle-card .img{
  height:260px;
  background-size:cover;
  background-position:center;
  position:relative;
}

.raffle-card .text{
  padding:28px;
}

.car-badge{
  display:inline-flex;
  align-items:center;
  gap:8px;
  background:rgba(<?php echo $mainColor; ?>,0.10);
  color:var(--main-color);
  padding:8px 16px;
  border-radius:999px;
  font-size:12px;
  font-weight:800;
  margin-bottom:18px;
}

.car-title{
  font-size:20px;
  font-weight:900;
  color:#111827;
  margin-bottom:12px;
  line-height:1.2;
}

.car-description{
  color:var(--muted);
  font-size:15px;
  line-height:1.7;
  margin-bottom:24px;
}

.car-price{
  font-size:34px;
  font-weight:900;
  color:#111827;
  letter-spacing:-1px;
}

.car-price span{
  color:var(--muted);
  font-size:14px;
  font-weight:700;
}

.car-footer{
  margin-top:22px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}

.car-btn{
  background:#111827;
  color:#fff !important;
  padding:14px 22px;
  border-radius:16px;
  font-weight:800;
  transition:.3s;
}

.car-btn:hover{
  background:var(--main-color);
}

/* ================= FEATURES ================= */

.feature-card{
  background:#fff;
  border-radius:28px;
  padding:40px 30px;
  height:100%;
  border:1px solid rgba(<?php echo $mainColor; ?>,0.06);
  transition:.3s;
}

.feature-card:hover{
  transform:translateY(-5px);
  box-shadow:0 20px 50px rgba(<?php echo $mainColor; ?>,0.08);
}

.feature-icon{
  width:90px;
  height:90px;
  border-radius:24px;
  background:rgba(<?php echo $mainColor; ?>,0.10);
  display:flex;
  align-items:center;
  justify-content:center;
  color:var(--main-color);
  font-size:34px;
  margin-bottom:25px;
}

.feature-title{
  font-size:26px;
  font-weight:900;
  margin-bottom:15px;
}

.feature-text{
  color:var(--muted);
  line-height:1.8;
}

/* ================= ABOUT ================= */

.about-section{
  background:#0f172a;
  border-radius:40px;
  overflow:hidden;
}

.about-image{
  min-height:100%;
  background-size:cover;
  background-position:center;
}

.about-content{
  padding:70px;
}

.about-content .section-title{
  color:#fff !important;
}

.about-content p{
  color:rgba(255,255,255,.75);
  line-height:1.9;
  margin-bottom:22px;
}

/* ================= MOBILE ================= */

@media(max-width:991px){

  .hero-title{
    font-size:48px;
    line-height:1.05;
  }

  .hero-subtitle{
    font-size:17px;
  }

  .hero-wrap{
    min-height:860px !important;
  }

  .hero-wrap .slider-text{
    min-height:860px !important;
    padding-top:100px;
    align-items:flex-start !important;
  }

  .hero-search{
    margin-top:-140px;
  }

  .hero-search-box{
    border-radius:28px;
    padding:25px;
  }

  .section-title{
    font-size:38px;
  }

  .about-content{
    padding:35px;
  }

}

</style>

<!-- HERO -->

<div class="hero-wrap lazy-bg"
     data-bg="../../<?php echo StockData::getFPrincipal(1)->img_1; ?>"
     style="
     min-height:100vh;
     position:relative;
     z-index:1;
     display:block !important;
     visibility:visible !important;
     opacity:1 !important;
     background-image:
     linear-gradient(
        rgba(0,0,0,0.65),
        rgba(0,0,0,0.65)
     ),
     url('../../<?php echo StockData::getFPrincipal(1)->img_1; ?>');
     background-size:cover;
     background-position:center center;
     background-repeat:no-repeat;
     ">

  <div class="overlay"
       style="
       position:absolute;
       top:0;
       left:0;
       width:100%;
       height:100%;
       background:linear-gradient(
         to right,
         rgba(0,0,0,.78),
         rgba(0,0,0,.45),
         rgba(0,0,0,.15)
       );
       z-index:1;
       ">
  </div>

  <div class="container"
       style="position:relative;z-index:5;">

    <div class="row no-gutters slider-text align-items-center"
         style="
         min-height:100vh;
         display:flex;
         align-items:center;
         ">

      <div class="col-lg-8">

        <div class="hero-content ftco-animate"
             style="
             display:block !important;
             visibility:visible !important;
             opacity:1 !important;
             ">

          <div class="hero-mini">
            <i class="fa fa-star"></i>

            <?php
            echo isset($title)
            ? htmlspecialchars((string)$title, ENT_QUOTES, 'UTF-8')
            : 'Rent Car';
            ?>
          </div>

          <h1 class="hero-title"
              style="
              color:#fff;
              font-size:74px;
              font-weight:900;
              line-height:1;
              letter-spacing:-3px;
              margin-bottom:25px;
              ">

           <?php echo __('hero_title_1'); ?> <?php echo __('hero_title_2'); ?>
<span style="color:var(--main-color);">
<?php echo __('hero_title_3'); ?>
</span>
<?php echo __('hero_title_4'); ?>

          </h1>

          <p class="hero-subtitle"
             style="
             color:rgba(255,255,255,.82);
             font-size:19px;
             line-height:1.8;
             margin-bottom:35px;
             max-width:620px;
             ">

            <?php echo __('hero_sub'); ?>

          </p>

          <div class="hero-buttons"
               style="
               display:flex;
               gap:18px;
               flex-wrap:wrap;
               ">

            <a href="#cars" class="hero-btn">
              <?php echo __('book_a_car'); ?>
            </a>

            <a href="#about" class="hero-btn-outline">
              <?php echo __('learn_more'); ?>
            </a>

          </div>

        </div>

      </div>

    </div>

  </div>

</div>

<!-- SEARCH -->

<section class="hero-search">

  <div class="container">

    <div class="hero-search-box">

      
<?php
$av_from = isset($_POST["av_from"]) && trim($_POST["av_from"]) != ""
? trim($_POST["av_from"])
: date("Y-m-d");

$av_to = isset($_POST["av_to"]) && trim($_POST["av_to"]) != ""
? trim($_POST["av_to"])
: date("Y-m-d", strtotime("+3 days"));

$av_error = "";
$cars_to_show = [];

if(strtotime($av_from) === false || strtotime($av_to) === false){

    $av_error = __('invalid_dates');

}elseif(strtotime($av_from) > strtotime($av_to)){

    $av_error = __('date_order_err');

}else{

    $fromSQL = date("Y-m-d 00:00:00", strtotime($av_from));
    $toSQL   = date("Y-m-d 23:59:59", strtotime($av_to));

    $base = new Database();
    $con = $base->connect();

    if($con){

        $stock_id = 0;

        if(class_exists("StockData") && method_exists("StockData", "getPrincipal")){

            $principal = $selstock;

            if($principal && isset($principal->id)){
                $stock_id = intval($principal->id);
            }

        }

        $sql = "
            SELECT c.*
            FROM cars c
            WHERE 1=1
        ";

        if($stock_id > 0){
            $sql .= " AND c.stock_id = $stock_id ";
        }

        $sql .= "
            AND NOT EXISTS (
                SELECT 1
                FROM booking b
                WHERE b.car_id = c.id
        ";

        if($stock_id > 0){
            $sql .= " AND b.stock_id = $stock_id ";
        }

        $sql .= "
                AND b.status IN (0,1)
                AND (
                    b.start_at <= '$toSQL'
                    AND b.end_at >= '$fromSQL'
                )
            )
            ORDER BY c.id ASC
        ";

        $query = $con->query($sql);

        if($query instanceof mysqli_result){

            while($r = $query->fetch_assoc()){
                $cars_to_show[] = $r;
            }

        }else{

            $av_error = $con->error;

        }

    }

}
?>

<style>

.car-date-range{
  display:inline-flex;
  align-items:center;
  gap:8px;
  background:rgba(<?php echo $mainColor; ?>,0.10);
  color:var(--main-color);
  padding:9px 14px;
  border-radius:999px;
  font-size:13px;
  font-weight:800;
  margin-bottom:18px;
}

.btn-av{
  height:58px;
  width:100%;
  border:none;
  border-radius:18px;
  background:var(--main-color);
  color:#fff !important;
  font-weight:900;
  font-size:15px;
  box-shadow:0 18px 40px rgba(<?php echo $mainColor; ?>,0.22);
}

.av-search-box{
  background:#fff;
  border-radius:28px;
  padding:28px;
  margin-bottom:45px;
  box-shadow:0 20px 60px rgba(<?php echo $mainColor; ?>,0.10);
  border:1px solid rgba(<?php echo $mainColor; ?>,0.08);
}

.av-search-box label{
  font-size:13px;
  font-weight:900;
  color:#111827;
  margin-bottom:10px;
}

.av-search-box .form-control{
  height:58px;
  border-radius:18px;
  border:1px solid rgba(<?php echo $mainColor; ?>,0.08);
  background:#f8fafc;
  box-shadow:none !important;
  padding-left:18px;
  font-weight:700;
}

.av-error{
  background:#fee2e2;
  color:#991b1b;
  padding:15px 20px;
  border-radius:16px;
  font-weight:800;
  margin-bottom:25px;
}

.car-carousel.owl-carousel .owl-stage{
  display:flex;
}

.car-carousel.owl-carousel .owl-item{
  display:flex;
  height:auto;
}

.car-carousel.owl-carousel .item{
  width:100%;
  padding:10px;
}

.car-carousel .owl-nav{
  margin-top:25px;
  text-align:center;
}

.car-carousel .owl-nav button{
  width:55px;
  height:55px;
  border-radius:999px !important;
  border:none !important;
  background:var(--main-color) !important;
  color:#fff !important;
  font-size:22px !important;
  margin:0 8px;
}

.car-carousel .owl-dots{
  margin-top:20px;
  text-align:center;
}

.car-carousel .owl-dot span{
  width:12px !important;
  height:12px !important;
  background:rgba(<?php echo $mainColor; ?>,0.25) !important;
}

.car-carousel .owl-dot.active span{
  background:rgb(<?php echo $mainColor; ?>) !important;
}

</style>


<form method="POST" action="#cars">

<input type="hidden" name="av_action" value="search">

<div class="row">

<div class="col-12 col-md-5 mb-3">
<label><?php echo __('date_from'); ?></label>
<input type="date" name="av_from" class="form-control" value="<?php echo htmlspecialchars($av_from, ENT_QUOTES, 'UTF-8'); ?>">
</div>

<div class="col-12 col-md-5 mb-3">
<label><?php echo __('date_to'); ?></label>
<input type="date" name="av_to" class="form-control" value="<?php echo htmlspecialchars($av_to, ENT_QUOTES, 'UTF-8'); ?>">
</div>

<div class="col-12 col-md-2 mb-3">
<label><?php echo __('search'); ?></label>
<button type="submit" class="btn-av">
<i class="fa fa-search"></i>
</button>
</div>

</div>

</form>


    </div>

  </div>

</section>

<!-- CARS -->

<section class="ftco-section" id="cars">

<div class="container">

<div class="row justify-content-center mb-5" style="display:flex !important;visibility:visible !important;opacity:1 !important;position:relative;z-index:9999;">

<div class="col-lg-8 text-center" style="display:block !important;visibility:visible !important;opacity:1 !important;">

<span class="section-mini"><?php echo __('premium_fleet'); ?></span>

<h2 class="section-title"><?php echo __('explore_cars'); ?></h2>

<p class="section-subtitle">
<?php echo __('explore_sub'); ?>
</p>

</div>

</div>


<?php if($av_error != ""): ?>

<div class="av-error">
<?php echo htmlspecialchars($av_error, ENT_QUOTES, 'UTF-8'); ?>
</div>

<?php endif; ?>

<div class="row car-carousel">

<?php

if(count($cars_to_show) > 0){

foreach($cars_to_show as $r){

    $img_path = "img/default.jpg";

    if(isset($r["invoice_file"]) && trim((string)$r["invoice_file"]) != ""){

        $tmp = "../../CF-SYSTEMS/storage/invoice_files/".trim((string)$r["invoice_file"]);

        if(file_exists($tmp)){
            $img_path = $tmp;
        }

    }

    $car_id = isset($r["id"]) ? intval($r["id"]) : 0;

    $brand_name = "";

    if(isset($r["brand_id"]) && intval($r["brand_id"]) > 0){

        $brand = BrandData::getById(intval($r["brand_id"]));

        if($brand && isset($brand->name)){
            $brand_name = strtoupper((string)$brand->name);
        }

    }

    $car_year = "";

    if(isset($r["year"]) && trim((string)$r["year"]) != ""){
        $car_year = trim((string)$r["year"]);
    }

    $car_token = "";

    if(isset($r["token"]) && trim((string)$r["token"]) != ""){
        $car_token = trim((string)$r["token"]);
    }

    if(isset($r["name"]) && trim((string)$r["name"]) != ""){

        $car_name = strtoupper(
            trim(
                $brand_name . " " .
                (string)$r["name"] . " " .
                $car_year . " [ " .
                $car_token . " ]"
            )
        );

    }else{

        $car_name = "VEHICULO";

    }

    $car_price = isset($r["price"]) ? floatval($r["price"]) : 0;

?>

<div class="col-lg-4 col-md-6 mb-4 car-slide-item">

<div class="raffle-card">

<div class="img"
     style="
     background-image:url('<?php echo htmlspecialchars($tmp, ENT_QUOTES, 'UTF-8'); ?>');
     background-size:cover;
     background-position:center center;
     background-repeat:no-repeat;
     ">
</div>

<div class="text">

<div class="car-badge">
  <i class="fa fa-car"></i>
  <?php echo __('available_now'); ?>
</div>

<h3 class="car-title">
  <?php echo htmlspecialchars($car_name, ENT_QUOTES, 'UTF-8'); ?>
</h3>

<p class="car-description">
  <?php echo __('car_desc_default'); ?>
</p>

<div class="car-date-range">
  <i class="fa fa-calendar"></i>
  <?php echo date("d/m/Y", strtotime($av_from)); ?> - <?php echo date("d/m/Y", strtotime($av_to)); ?>
</div>

<div class="car-footer">

<div class="car-price">
  US$ <?php echo number_format($car_price,2); ?>
  <span><?php echo __('per_day'); ?></span>
</div>

</div>

<a href="<?php echo $base_url_safe; ?>car-single.php?car_id=<?php echo $car_id; ?>&from=<?php echo urlencode($av_from); ?>&to=<?php echo urlencode($av_to); ?>" class="car-btn">
  <?php echo __('rent_now'); ?>
</a>

</div>

</div>

</div>

<?php

}

}else{

?>

<div class="col-12 text-center">
  <h3 style="font-weight:900;color:#111827;">
    <?php echo __('no_cars'); ?>
  </h3>
</div>

<?php

}

?>

</div>

</div>

</section>

<script>
window.addEventListener("load", function(){

    function iniciarCarrusel(){

        if(typeof jQuery !== "undefined" && typeof jQuery.fn.owlCarousel !== "undefined"){

            $(".car-carousel").removeClass("row");

            $(".car-slide-item")
                .removeClass("col-lg-4 col-md-6 mb-4 car-slide-item")
                .addClass("item");

            $(".car-carousel").addClass("owl-carousel");

            $(".car-carousel").owlCarousel({
                loop:true,
                margin:25,
                nav:true,
                dots:true,
                autoplay:true,
                autoplayTimeout:4000,
                smartSpeed:800,
                responsive:{
                    0:{ items:1 },
                    768:{ items:2 },
                    1200:{ items:3 }
                }
            });

        }else{

            setTimeout(iniciarCarrusel, 500);

        }

    }

    iniciarCarrusel();

});
</script>

<!-- FEATURES -->

<section class="ftco-section pt-0">

<div class="container">

<div class="row justify-content-center mb-5">

<div class="col-lg-8 text-center">

<span class="section-mini">
Why Choose Us
</span>

<h2 class="section-title">
Premium Services Built For You
</h2>

</div>

</div>

<div class="row">

<div class="col-lg-4 mb-4">

<div class="feature-card">

<div class="feature-icon">
  <i class="fa fa-car-side"></i>
</div>

<h3 class="feature-title">
Luxury Cars
</h3>

<p class="feature-text">
We offer a premium fleet of luxury and modern vehicles ready for every journey.
</p>

</div>

</div>

<div class="col-lg-4 mb-4">

<div class="feature-card">

<div class="feature-icon">
  <i class="fa fa-shield-alt"></i>
</div>

<h3 class="feature-title">
Safe & Secure
</h3>

<p class="feature-text">
Drive with confidence thanks to our professional support and fully insured vehicles.
</p>

</div>

</div>

<div class="col-lg-4 mb-4">

<div class="feature-card">

<div class="feature-icon">
  <i class="fa fa-headset"></i>
</div>

<h3 class="feature-title">
24/7 Support
</h3>

<p class="feature-text">
Our team is available anytime to assist you during your rental experience.
</p>

</div>

</div>

</div>

</div>

</section>

<!-- ABOUT -->

<section class="ftco-section" id="about">

<div class="container">

<div class="about-section">

<div class="row no-gutters">

<div class="col-lg-6">
<div class="about-image"
     style="
     background-image:url('../../<?php echo StockData::getFPrincipal(1)->img_3; ?>');
     background-size:135%;
     background-position:center center;
     background-repeat:no-repeat;
     height:100%;
     min-height:630px;
     border-radius:0;
     transform:scale(1.05);
     ">
</div>

</div>

<div class="col-lg-6">

<div class="about-content">

<span class="section-mini">
About Us
</span>

<h2 class="section-title">
<?php echo isset($title) ? htmlspecialchars((string)$title, ENT_QUOTES, 'UTF-8') : 'Rent Car'; ?>
</h2>

<p>
At <?php echo isset($title) ? htmlspecialchars((string)$title, ENT_QUOTES, 'UTF-8') : 'Rent Car'; ?> we believe renting a car should feel luxurious, simple and unforgettable.
</p>

<p>
We provide premium vehicles, exceptional customer service and the confidence you need for every trip.
</p>

<p>
Whether for business, vacations or special occasions, our mission is to deliver the perfect driving experience with style and comfort.
</p>

<a href="<?php echo $base_url_safe; ?>contact.php" class="hero-btn">
  Contact Us
</a>

</div>

</div>

</div>

</div>

</div>

</section>

<?php include "layout/footer.php";?>