<?php
$stockFooter = null;

if (isset($selstock) && intval($selstock) > 0) {
    $stockFooter = StockData::getFPrincipal($selstock);
}

$footerTitle = isset($title) && $title != "" ? $title : "Rent Car";

$footerAddress = "";
$footerEmail   = "";
$footerPhone   = "";
$footerPhone2  = "";

if ($stockFooter) {
    $footerAddress = isset($stockFooter->address) ? $stockFooter->address : "";
    $footerEmail   = isset($stockFooter->email) ? $stockFooter->email : "";
    $footerPhone   = isset($stockFooter->phone) ? $stockFooter->phone : "";
    $footerPhone2  = isset($stockFooter->phone2) ? $stockFooter->phone2 : "";
}

$whatsappNumber = preg_replace('/[^0-9]/', '', $footerPhone);

$footerTitleSafe   = htmlspecialchars($footerTitle, ENT_QUOTES, 'UTF-8');
$footerAddressSafe = htmlspecialchars($footerAddress, ENT_QUOTES, 'UTF-8');
$footerEmailSafe   = htmlspecialchars($footerEmail, ENT_QUOTES, 'UTF-8');
$footerPhoneSafe   = htmlspecialchars($footerPhone, ENT_QUOTES, 'UTF-8');
$footerPhone2Safe  = htmlspecialchars($footerPhone2, ENT_QUOTES, 'UTF-8');

$baseUrlFooter = isset($base_url) ? $base_url : "/";
$baseUrlFooterSafe = htmlspecialchars($baseUrlFooter, ENT_QUOTES, 'UTF-8');

$version = "1.0";
?>

<!-- Footer Start -->
<footer class="rent-footer">

  <div class="container">

    <div class="rent-footer-top">

      <div class="row">

        <div class="col-lg-5 mb-5 mb-lg-0">

          <div class="footer-brand">
            <?php echo $footerTitleSafe; ?>
          </div>

          <p class="footer-text">
            <?php echo __('footer_text'); ?>
          </p>

          <div class="footer-social">

            <a href="#">
              FACEBOOK
            </a>

            <a href="#">
              INSTAGRAM
            </a>

            <?php if ($whatsappNumber != ""): ?>
            <a href="https://wa.me/<?php echo $whatsappNumber; ?>" target="_blank">
              WHATSAPP
            </a>
            <?php endif; ?>

          </div>

        </div>

        <div class="col-lg-2 col-md-6 mb-5 mb-lg-0">

          <h4 class="footer-title">
            <?php echo __('footer_company'); ?>
          </h4>

          <ul class="footer-links">
            <li><a href="<?php echo $baseUrlFooterSafe; ?>"><?php echo __('footer_home'); ?></a></li>
            <li><a href="<?php echo $baseUrlFooterSafe; ?>cars.php"><?php echo __('footer_cars'); ?></a></li>
            <li><a href="<?php echo $baseUrlFooterSafe; ?>about.php"><?php echo __('footer_about'); ?></a></li>
            <li><a href="<?php echo $baseUrlFooterSafe; ?>services.php"><?php echo __('footer_services'); ?></a></li>
            <li><a href="<?php echo $baseUrlFooterSafe; ?>contact.php"><?php echo __('footer_contact'); ?></a></li>
            <li><a href="<?php echo $baseUrlFooterSafe; ?>privacy.php"><?php echo __('nav_privacy'); ?></a></li>
          </ul>

        </div>

        <div class="col-lg-2 col-md-6 mb-5 mb-lg-0">

          <h4 class="footer-title">
            <?php echo __('footer_services'); ?>
          </h4>

          <ul class="footer-links">
            <li><a href="<?php echo $baseUrlFooterSafe; ?>cars.php"><?php echo __('footer_luxury'); ?></a></li>
            <li><a href="<?php echo $baseUrlFooterSafe; ?>services.php"><?php echo __('footer_airport'); ?></a></li>
            <li><a href="<?php echo $baseUrlFooterSafe; ?>cars.php"><?php echo __('footer_daily'); ?></a></li>
            <li><a href="<?php echo $baseUrlFooterSafe; ?>cars.php"><?php echo __('footer_weekly'); ?></a></li>
            <li><a href="<?php echo $baseUrlFooterSafe; ?>cars.php"><?php echo __('footer_longterm'); ?></a></li>
          </ul>

        </div>

        <div class="col-lg-3">

          <h4 class="footer-title">
            <?php echo __('footer_contact'); ?>
          </h4>

          <ul class="footer-contact">

            <?php if ($footerAddressSafe != ""): ?>
            <li>
              <?php echo $footerAddressSafe; ?>
            </li>
            <?php endif; ?>

            <?php if ($footerEmailSafe != ""): ?>
            <li>
              <?php echo $footerEmailSafe; ?>
            </li>
            <?php endif; ?>

            <?php if ($footerPhoneSafe != ""): ?>
            <li>
              <?php echo $footerPhoneSafe; ?>
            </li>
            <?php endif; ?>

            <?php if ($footerPhone2Safe != ""): ?>
            <li>
              <?php echo $footerPhone2Safe; ?>
            </li>
            <?php endif; ?>

          </ul>

        

        </div>

      </div>

    </div>

    <div class="rent-footer-bottom">

      <p>
        © <?php echo date("Y"); ?> <?php echo $footerTitleSafe; ?>. <?php echo __('all_rights'); ?>
      </p>

    </div>

  </div>

</footer>

<style>

#ftco-loader,
.loader,
.preloader,
.fullscreen{
    display:none !important;
    opacity:0 !important;
    visibility:hidden !important;
    pointer-events:none !important;
}

.rent-footer{
  background:#0f172a;
  color:#fff;
  padding:90px 0 0 0;
  margin-top:40px;
}

.rent-footer-top{
  padding-bottom:60px;
}

.footer-brand{
  font-size:34px;
  font-weight:900;
  letter-spacing:-1px;
  color:#fff;
  margin-bottom:22px;
}

.footer-text{
  color:rgba(255,255,255,.68);
  line-height:1.9;
  font-size:15px;
  max-width:430px;
  margin-bottom:30px;
}

.footer-social{
  display:flex;
  gap:14px;
  flex-wrap:wrap;
}

.footer-social a{
  padding:12px 18px;
  border-radius:999px;
  background:rgba(255,255,255,.08);
  color:#fff;
  transition:.3s;
  text-decoration:none;
  font-size:13px;
  font-weight:700;
}

.footer-social a:hover{
  background:#ff5d00;
  color:#fff;
}

.footer-title{
  color:#fff;
  font-size:18px;
  font-weight:900;
  margin-bottom:26px;
}

.footer-links{
  padding:0;
  margin:0;
  list-style:none;
}

.footer-links li{
  margin-bottom:14px;
}

.footer-links a{
  color:rgba(255,255,255,.65);
  font-weight:600;
  transition:.3s;
  text-decoration:none;
}

.footer-links a:hover{
  color:#ff5d00;
  padding-left:6px;
}

.footer-contact{
  padding:0;
  margin:0 0 28px 0;
  list-style:none;
}

.footer-contact li{
  margin-bottom:17px;
  color:rgba(255,255,255,.70);
  line-height:1.6;
}


.rent-footer-bottom{
  border-top:1px solid rgba(255,255,255,.10);
  padding:28px 0;
  text-align:center;
}

.rent-footer-bottom p{
  margin:0;
  color:rgba(255,255,255,.58);
  font-size:14px;
  font-weight:600;
}

@media(max-width:767px){

  .rent-footer{
    padding-top:60px;
  }

  .footer-brand{
    font-size:26px;
  }

}

</style>

<script src="<?php echo $baseUrlFooterSafe; ?>js/jquery.min.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo $baseUrlFooterSafe; ?>js/popper.min.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo $baseUrlFooterSafe; ?>js/bootstrap.min.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo $baseUrlFooterSafe; ?>js/jquery.min.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo $baseUrlFooterSafe; ?>js/owl.carousel.min.js?v=<?php echo $version; ?>"></script>

<script>

document.addEventListener("DOMContentLoaded", function(){

    var loader = document.getElementById("ftco-loader");

    if(loader){
        loader.style.display = "none";
        loader.style.opacity = "0";
        loader.style.visibility = "hidden";
    }

});

window.onload = function(){

    var loader = document.getElementById("ftco-loader");

    if(loader){
        loader.style.display = "none";
        loader.style.opacity = "0";
        loader.style.visibility = "hidden";
    }

};

setTimeout(function(){

    var loader = document.getElementById("ftco-loader");

    if(loader){
        loader.style.display = "none";
        loader.style.opacity = "0";
        loader.style.visibility = "hidden";
    }

},500);

</script>

</body>
</html>