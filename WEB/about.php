<?php include "layout/header.php"; ?>

<style>
body{
  background:#fff;
  color:#111827;
}

.about-hero{
  background:#0f172a;
  color:#fff;
  padding:150px 0 90px;
}

.about-hero h1{
  font-size:64px;
  font-weight:900;
  letter-spacing:-2px;
  color:#fff;
}

.about-hero p{
  color:rgba(255,255,255,.70);
  font-size:18px;
  max-width:680px;
}

.section-mini{
  color:#ff5d00;
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
  margin-bottom:22px;
}

.about-text{
  color:#64748b;
  line-height:1.9;
  font-size:16px;
}

.about-img{
  min-height:560px;
  background-size:cover;
  background-position:center;
  border-radius:36px;
  box-shadow:0 30px 80px rgba(<?php echo $mainColor; ?>,0.14);
}

.about-card{
  background:#fff;
  border:1px solid rgba(<?php echo $mainColor; ?>,0.06);
  border-radius:30px;
  padding:38px;
  height:100%;
  box-shadow:0 18px 50px rgba(<?php echo $mainColor; ?>,0.06);
}

.about-card i{
  width:80px;
  height:80px;
  background:rgba(<?php echo $mainColor; ?>,0.10);
  color:rgba(<?php echo $mainColor; ?>,1);
  border-radius:24px;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:32px;
  margin-bottom:25px;
}

.about-card h3{
  font-size:25px;
  font-weight:900;
  color:#111827;
  margin-bottom:14px;
}

.about-card p{
  color:#64748b;
  line-height:1.8;
  margin:0;
}

.stats-section{
  background:#0f172a;
  border-radius:40px;
  padding:70px 40px;
  color:#fff;
}

.stat-box{
  text-align:center;
}

.stat-box h2{
  color:#fff;
  font-size:52px;
  font-weight:900;
  margin-bottom:5px;
}

.stat-box p{
  color:rgba(255,255,255,.65);
  font-weight:700;
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
  letter-spacing:-2px;
  color:#fff;
  margin-bottom:18px;
}

.cta-box p{
  color:rgba(255,255,255,.85);
  max-width:700px;
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
  .about-hero{
    padding:130px 0 70px;
  }

  .about-hero h1{
    font-size:42px;
  }

  .section-title{
    font-size:36px;
  }

  .about-img{
    min-height:360px;
    margin-bottom:30px;
  }

  .cta-box h2{
    font-size:34px;
  }
}
</style>

<section class="about-hero">
  <div class="container">
    <span class="section-mini">About Us</span>
    <h1><?php echo isset($title) ? htmlspecialchars((string)$title, ENT_QUOTES, 'UTF-8') : 'Rent Car'; ?></h1>
    <p>
      We provide modern vehicles, professional service and a smooth rental experience for every client.
    </p>
  </div>
</section>

<section class="ftco-section">
  <div class="container">

    <div class="row align-items-center">

      <div class="col-lg-6 mb-5 mb-lg-0">
        <div class="about-img"
     style="
     background-image:url('../../<?php echo StockData::getFPrincipal(1)->img_2; ?>');
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

      <div class="col-lg-6 pl-lg-5">

        <span class="section-mini">Who We Are</span>

        <h2 class="section-title">
          Your Trusted Rent Car Company
        </h2>

        <p class="about-text">
          At <?php echo $title; ?>, we believe renting a vehicle should be simple, elegant and reliable.
          Our goal is to offer every client a premium experience from the moment they choose a vehicle until the end of their trip.
        </p>

        <p class="about-text">
          Whether you need a car for business, vacation, personal use or a special occasion, we provide comfortable,
          clean and well-maintained vehicles ready for the road.
        </p>

        <p class="about-text">
          We focus on quality service, transparency and customer satisfaction, making every rental experience smooth and professional.
        </p>

      </div>

    </div>

  </div>
</section>

<section class="ftco-section pt-0">
  <div class="container">

    <div class="row">

      <div class="col-lg-4 mb-4">
        <div class="about-card">
          <i class="fa fa-car-side"></i>
          <h3>Premium Fleet</h3>
          <p>
            Modern vehicles selected to offer comfort, performance and style for every type of trip.
          </p>
        </div>
      </div>

      <div class="col-lg-4 mb-4">
        <div class="about-card">
          <i class="fa fa-shield-alt"></i>
          <h3>Safe & Reliable</h3>
          <p>
            Vehicles inspected and maintained to provide confidence, safety and peace of mind.
          </p>
        </div>
      </div>

      <div class="col-lg-4 mb-4">
        <div class="about-card">
          <i class="fa fa-headset"></i>
          <h3>Professional Support</h3>
          <p>
            Friendly assistance and clear communication before, during and after your rental.
          </p>
        </div>
      </div>

    </div>

  </div>
</section>

<section class="ftco-section pt-0">
  <div class="container">

    <div class="stats-section">

      <div class="row">

        <div class="col-md-3 col-6 mb-4 mb-md-0">
          <div class="stat-box">
            <h2>100%</h2>
            <p>Service Quality</p>
          </div>
        </div>

        <div class="col-md-3 col-6 mb-4 mb-md-0">
          <div class="stat-box">
            <h2>24/7</h2>
            <p>Support</p>
          </div>
        </div>

        <div class="col-md-3 col-6">
          <div class="stat-box">
            <h2>Clean</h2>
            <p>Vehicles</p>
          </div>
        </div>

        <div class="col-md-3 col-6">
          <div class="stat-box">
            <h2>Fast</h2>
            <p>Booking</p>
          </div>
        </div>

      </div>

    </div>

  </div>
</section>

<section class="ftco-section pt-0">
  <div class="container">

    <div class="cta-box">

      <h2>Ready To Book Your Car?</h2>

      <p>
        Choose your vehicle today and enjoy a premium rental experience with comfort, confidence and professional service.
      </p>

      <a href="<?php echo $base_url_safe; ?>cars.php" class="cta-btn">
        View Our Cars
      </a>

    </div>

  </div>
</section>

<?php include "layout/footer.php"; ?>