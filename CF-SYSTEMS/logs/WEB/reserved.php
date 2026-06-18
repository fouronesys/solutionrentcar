
<?php include "layout/header.php";

$base = new Database();
$con = $base->connect();
$sql = "SELECT SQL_BIG_RESULT * FROM cars WHERE id=".$_GET["id"];
$query = $con->query($sql);
if ($query && $query->num_rows > 0): while($r = $query->fetch_array()): $cars = CarsData::getById($r["id"]);?>     

    <section class="hero-wrap hero-wrap-2 js-fullheight" style="background-image: url(<?php echo  "https://".$xhost;?>/CF-SYSTEMS/storage/invoice_files/<?php echo $r['invoice_file'];?>);" data-stellar-background-ratio="0.5">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text js-fullheight align-items-end justify-content-start">
          <div class="col-md-9 ftco-animate pb-5">
          	<p class="breadcrumbs"><span class="mr-2"><a href="index.html">Home <i class="ion-ios-arrow-forward"></i></a></span> </p>
            <h1 class="mb-3 bread"><?php echo BrandData::getById($r['brand_id'])->name." ".$r['name']." ".$r['year'];?></h1>
          </div>
        </div>
      </div>
    </section>

    <section class="ftco-section contact-section">
      <div class="container">
           <h3 class="mb-3 bread"><?php echo "From: ". $_GET['date_at']." : ".$_GET['time_pick']." - Until: ".$_GET['date_end']." : ".$_GET['time_pick'];?> </h3>
        <div class="row d-flex mb-5 contact-info">
        	<div class="col-md-4">
        		<div class="row mb-5">
		          <div class="col-md-12">
		          	<div class="border w-100 p-4 rounded mb-2 d-flex">
			          	<div class="icon mr-3">
			          		<span class="fas fa-palette"></span>
			          	</div>
			            <p><span>Color:</span> <?php echo ColorData::getById($r['exterior_id'])->name;?></p>
			          </div>
		          </div>
		          <div class="col-md-12">
		          	<div class="border w-100 p-4 rounded mb-2 d-flex">
			          	<div class="icon mr-3">
			          		<span class="flaticon-pistons"></span>
			          	</div>
			            <p><span>Transmission:</span> <?php echo $cars->getTransmission()->name;?></p>
			          </div>
		          </div>
		          <div class="col-md-12">
		          	<div class="border w-100 p-4 rounded mb-2 d-flex">
			          	<div class="icon mr-3">
			          		<span class="flaticon-car-seat"></span>
			          	</div>
			            <p><span>Seats:</span> <?php echo $cars->seat;?></p>
			          </div>
		          </div>
		           <div class="col-md-12">
		          	<div class="border w-100 p-4 rounded mb-2 d-flex">
			          	<div class="icon mr-3">
			          		<span class="flaticon-backpack"></span>
			          	</div>
			            <p><span>Luggage:</span> 4 Bags</p>
			          </div>
		          </div>
		           <div class="col-md-12">
		          	<div class="border w-100 p-4 rounded mb-2 d-flex">
			          	<div class="icon mr-3">
			          		<span class="flaticon-diesel"></span>
			          	</div>
			            <p><span>Fuel:</span> <?php echo $cars->getFuel()->name;?></p>
			          </div>
		          </div>
		         
		        </div>
          </div>
          <div class="col-md-8 block-9 mb-md-5">
            <form enctype="multipart/form-data" action="action/reserved.php" method="post" class="bg-light p-5 contact-form">
              <div class="form-group">
                <input type="text" class="form-control" name="name" required placeholder="Your Name">
                <input type="hidden"  name="car_id" required value="<?php echo $r["id"];?>">
                <input type="hidden"  name="selstock" required value="<?php echo $selstock;?>">
                <input type="hidden"  name="date_at" required value="<?php echo $_GET['date_at'];?>">
                <input type="hidden"  name="date_end" required value="<?php echo $_GET['date_end'];?>">
                <input type="hidden"  name="time_pick" required value="<?php echo $_GET['time_pick'];?>">
              </div>
              <div class="form-group">
                <input type="email" class="form-control" name="email" required  placeholder="Your Email">
              </div>
              <div class="form-group">
                <input type="text" class="form-control" name="no" required  placeholder="Your ID / Passport">
              </div>
              <div class="form-group">
<textarea name="comment" cols="30" rows="7" class="form-control" readonly>Hello,

I'm interested in renting this vehicle for this date.

Please confirm availability and requirements so I can complete the reservation as soon as possible.

I look forward to hearing from you.

Thank you.
</textarea>
              </div>
              <div class="form-group">
                <input type="submit" value="Send Message" class="btn py-3 px-5" style="color:white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">
              </div>
            </form>
          
          </div>
        </div>
    
      </div>
    </section>
<?php endwhile; endif;

include "layout/footer.php";?>