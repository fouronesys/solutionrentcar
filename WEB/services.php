<?php include "layout/header.php"; ?>

<style>
body{background:#fff;color:#111827;}

.services-hero{
  background:#0f172a;
  color:#fff;
  padding:150px 0 90px;
}

.services-hero h1{
  font-size:64px;
  font-weight:900;
  letter-spacing:-2px;
  color:#fff;
}

.services-hero p{
  color:rgba(255,255,255,.70);
  font-size:18px;
  max-width:680px;
}

.section-mini{
  color:rgba(<?php echo $mainColor; ?>,1);
  font-size:13px;
  font-weight:900;
  letter-spacing:2px;
  text-transform:uppercase;
  margin-bottom:15px;
  display:block;
}

.section-title{
  font-size:52px;
  font-weight:900;
  color:#111827;
  letter-spacing:-2px;
}

.service-card{
  background:#fff;
  border:1px solid rgba(<?php echo $mainColor; ?>,0.06);
  border-radius:30px;
  padding:40px 32px;
  height:100%;
  box-shadow:0 18px 50px rgba(<?php echo $mainColor; ?>,0.06);
  transition:.3s;
}

.service-card:hover{
  transform:translateY(-7px);
  box-shadow:0 30px 80px rgba(<?php echo $mainColor; ?>,0.12);
}

.service-icon{
  width:88px;
  height:88px;
  border-radius:24px;
  background:rgba(<?php echo $mainColor; ?>,0.06);
  color:rgba(<?php echo $mainColor; ?>,1);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:34px;
  margin-bottom:25px;
}

.service-card h3{
  font-size:26px;
  font-weight:900;
  color:#111827;
  margin-bottom:15px;
}

.service-card p{
  color:#64748b;
  line-height:1.8;
  margin:0;
}

.process-box{
  background:#0f172a;
  border-radius:40px;
  padding:70px 40px;
  color:#fff;
}

.process-card{
  background:rgba(255,255,255,.07);
  border:1px solid rgba(255,255,255,.10);
  border-radius:28px;
  padding:35px 25px;
  height:100%;
}

.process-number{
  width:58px;
  height:58px;
  border-radius:50%;
  background:rgba(<?php echo $mainColor; ?>,1);
  color:#fff;
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:900;
  font-size:22px;
  margin-bottom:22px;
}

.process-card h3{
  color:#fff;
  font-size:24px;
  font-weight:900;
  margin-bottom:14px;
}

.process-card p{
  color:rgba(255,255,255,.68);
  line-height:1.8;
  margin:0;
}

.cta-box{
  background:rgba(<?php echo $mainColor; ?>,1);
  color:#fff;
  border-radius:40px;
  padding:70px 40px;
  text-align:center;
}

.cta-box h2{
  font-size:48px;
  font-weight:900;
  color:#fff;
  letter-spacing:-2px;
  margin-bottom:18px;
}

.cta-box p{
  color:rgba(255,255,255,.86);
  max-width:720px;
  margin:0 auto 30px;
  line-height:1.8;
}

.cta-btn{
  display:inline-block;
  background:#111827;
  color:#fff !important;
  padding:17px 34px;
  border-radius:999px;
  font-weight:900;
  transition:.3s;
}

.cta-btn:hover{
  background:#fff;
  color:#111827 !important;
  transform:translateY(-3px);
}

@media(max-width:767px){
  .services-hero{padding:130px 0 70px;}
  .services-hero h1{font-size:42px;}
  .section-title{font-size:36px;}
  .cta-box h2{font-size:34px;}
}
</style>

<section class="services-hero">
  <div class="container">
    <span class="section-mini">Our Services</span>
    <h1>Premium Rental Services</h1>
    <p>
      We offer flexible car rental solutions for daily trips, business travel, airport pickup and special occasions.
    </p>
  </div>
</section>

<section class="ftco-section">
  <div class="container">

    <div class="row justify-content-center mb-5">
      <div class="col-lg-8 text-center">
        <span class="section-mini">What We Offer</span>
        <h2 class="section-title">Services Built For Comfort</h2>
      </div>
    </div>

    <div class="row">

      <div class="col-lg-4 col-md-6 mb-4">
        <div class="service-card">
          <div class="service-icon">
            <i class="fa fa-car-side"></i>
          </div>
          <h3>Daily Car Rental</h3>
          <p>
            Rent a vehicle for one day or multiple days with a fast, simple and professional process.
          </p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 mb-4">
        <div class="service-card">
          <div class="service-icon">
            <i class="fa fa-calendar-alt"></i>
          </div>
          <h3>Weekly Rental</h3>
          <p>
            Flexible weekly rental plans designed for clients who need comfort and better pricing.
          </p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 mb-4">
        <div class="service-card">
          <div class="service-icon">
            <i class="fa fa-clock"></i>
          </div>
          <h3>Long Term Rental</h3>
          <p>
            Monthly or long-term rental options for businesses, personal use and extended stays.
          </p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 mb-4">
        <div class="service-card">
          <div class="service-icon">
            <i class="fa fa-plane-arrival"></i>
          </div>
          <h3>Airport Pickup</h3>
          <p>
            Vehicle delivery and pickup service for travelers who need a smooth arrival experience.
          </p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 mb-4">
        <div class="service-card">
          <div class="service-icon">
            <i class="fa fa-shield-alt"></i>
          </div>
          <h3>Safe Vehicles</h3>
          <p>
            Clean, inspected and well-maintained vehicles prepared to give you safety and confidence.
          </p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 mb-4">
        <div class="service-card">
          <div class="service-icon">
            <i class="fa fa-headset"></i>
          </div>
          <h3>Customer Support</h3>
          <p>
            Professional support before, during and after your rental to make your experience better.
          </p>
        </div>
      </div>

    </div>

  </div>
</section>

<section class="ftco-section pt-0">
  <div class="container">

    <div class="process-box">

      <div class="row justify-content-center mb-5">
        <div class="col-lg-8 text-center">
          <span class="section-mini">How It Works</span>
          <h2 style="font-size:52px; font-weight:900; color:#fff; letter-spacing:-2px;">Easy Rental Process</h2>
        </div>
      </div>

      <div class="row">

        <div class="col-lg-3 col-md-6 mb-4">
          <div class="process-card">
            <div class="process-number">1</div>
            <h3>Choose Car</h3>
            <p>Select the vehicle that fits your trip, style and budget.</p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
          <div class="process-card">
            <div class="process-number">2</div>
            <h3>Send Request</h3>
            <p>Contact us or make your reservation request quickly online.</p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
          <div class="process-card">
            <div class="process-number">3</div>
            <h3>Confirm Details</h3>
            <p>We confirm dates, pickup location, documents and rental terms.</p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
          <div class="process-card">
            <div class="process-number">4</div>
            <h3>Enjoy Driving</h3>
            <p>Receive your vehicle and enjoy a safe, comfortable rental experience.</p>
          </div>
        </div>

      </div>

    </div>

  </div>
</section>

<section class="ftco-section pt-0">
  <div class="container">

    <div class="cta-box">
      <h2>Need A Vehicle Today?</h2>
      <p>
        Explore our available fleet and book the right car for your next trip with premium service and full confidence.
      </p>

      <a href="<?php echo $base_url_safe; ?>cars.php" class="cta-btn">
        View Cars
      </a>
    </div>

  </div>
</section>

<?php include "layout/footer.php"; ?>