<!DOCTYPE html>
<?php include "layout/header.php";?>

    <div class="hero-wrap ftco-degree-bg" style="background-image: url('img/<?php echo $webimg;?>');" data-stellar-background-ratio="0.5">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text justify-content-start align-items-center justify-content-center">
          <div class="col-lg-8 ftco-animate">
          	<div class="text w-100 text-center mb-md-5 pb-md-5">
	            <h1 class="mb-4">Fast &amp; Easy Way To Rent A Car</h1>
	            <p style="font-size: 18px;">A small river flows by their place and supplies it with the necessary regelialia. It is a paradisematic country, in which roasted parts</p>
	       
            </div>
          </div>
        </div>
      </div>
    </div>

     <section class="ftco-section ftco-no-pt bg-light">
    	<div class="container">
    		<div class="row no-gutters">
    			<div class="col-md-12	featured-top">
    				<div class="row no-gutters">
	  					<div class="col-md-4 d-flex align-items-center">
	  					 <form method="post" action="action/search" enctype="multipart/form-data" class="request-form ftco-animate bg-secondary">
		          		<h2>Make your trip</h2>
			    				<div class="form-group">
			    					<label for="" class="label">Pick-up location</label>
			    					<input type="text" class="form-control" name="place_start" required placeholder="City, Airport, Station, etc">
			    				</div>
			    				<div class="form-group">
			    					<label for="" class="label">Drop-off location</label>
			    					<input type="text" class="form-control" name="place_end" required placeholder="City, Airport, Station, etc">
			    				</div>
			    				<div class="d-flex">
			    					<div class="form-group mr-2">
			                <label for="" class="label">Pick-up date</label>
			                <input type="text" class="form-control" id="book_pick_date" required name="date_at" placeholder="Date">
			              </div>
			              <div class="form-group ml-2">
			                <label for="" class="label">Drop-off date</label>
			                <input type="text" class="form-control" id="book_off_date" required name="date_end"  placeholder="Date">
			              </div>
		              </div>
		              <div class="form-group">
		                <label for="" class="label">Pick-up time</label>
		                <input type="text" class="form-control" id="time_pick" required name="time_pick" placeholder="Time">
		              </div>
			            <div class="form-group">
			              <input type="submit" value="Reserve Your Perfect Car" class="btn py-3 px-4" style="color:white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">
			            </div>
			    			</form>
	  					</div>
	  					<div class="col-md-8 d-flex align-items-center">
	  						<div class="services-wrap rounded-right w-100">
	  							<h3 class="heading-section mb-4">Better Way to Rent Your Perfect Cars</h3>
	  							<div class="row d-flex mb-4">
					          <div class="col-md-4 d-flex align-self-stretch ftco-animate">
					            <div class="services w-100 text-center">
				              	<div class="icon d-flex align-items-center justify-content-center"><span class="flaticon-route"></span></div>
				              	<div class="text w-100">
					                <h3 class="heading mb-2">Choose Your Pickup Location</h3>
				                </div>
					            </div>      
					          </div>
					          <div class="col-md-4 d-flex align-self-stretch ftco-animate">
					            <div class="services w-100 text-center">
				              	<div class="icon d-flex align-items-center justify-content-center"><span class="flaticon-handshake"></span></div>
				              	<div class="text w-100">
					                <h3 class="heading mb-2">Select the Best Deal</h3>
					              </div>
					            </div>      
					          </div>
					          <div class="col-md-4 d-flex align-self-stretch ftco-animate">
					            <div class="services w-100 text-center">
				              	<div class="icon d-flex align-items-center justify-content-center"><span class="flaticon-rent"></span></div>
				              	<div class="text w-100">
					                <h3 class="heading mb-2">Reserve Your Rental Car</h3>
					              </div>
					            </div>      
					          </div>
					        </div>
					        <p><a href="pricing.php" class="btn  py-3 px-4" style="color:white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);" >Rent A Car Now</a></p>
	  						</div>
	  					</div>
	  				</div>
				</div>
  		</div>
    </section>

<?php 
$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT * from cars where status<>1 and stock_id=".$selstock." limit 10";
$query = $con->query($sql);
    if(count($query)>0):?>
    <section class="ftco-section ftco-no-pt bg-light">
    	<div class="container">
    		<div class="row justify-content-center">
          <div class="col-md-12 heading-section text-center ftco-animate mb-5">
          	<span class="subheading" style="color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">What we offer</span>
            <h2 class="mb-2">Available Vehicles</h2>
          </div>
        </div>
    		<div class="row">
    			<div class="col-md-12">
    				<div class="carousel-car owl-carousel">
    				    <?php while($r = $query->fetch_array()):?>    		    
    					<div class="item">
    						<div class="car-wrap rounded ftco-animate">
		    					<div class="img rounded d-flex align-items-end" style="background-image: url(<?php echo  "https://".$xhost;?>/CF-SYSTEMS/storage/invoice_files/<?php echo $r['invoice_file'];?>);">
		    					</div>
		    					<div class="text">
		    						<h6 class="mb-0" style="color:black;"><?php echo BrandData::getById($r['brand_id'])->name." ".$r['name']." ".$r['year'];?></h6>
		    						<div class="d-flex mb-3">
			    						<span class="cat" style="color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"><?php echo ColorData::getById($r['exterior_id'])->name;?></span>
	    						<p class="price ml-auto">$<?php echo $r['price'];?> <span>/day</span></p>
		    						</div>
		    						<p class="d-flex mb-0 d-block"><a href="booking?id=<?php echo $r['id'];?>" class="btn py-2 mr-1" style="color:white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">Rent Now</a> <a  href="car-single.php?id=<?php echo $r['id'];?>" class="btn py-2 ml-1" style="color:white; background-color:gray;">Details</a></p>
		    					</div>
		    				</div>
    					</div>
    				<?php endwhile; ?>
    				
    				</div>
    			</div>
    		</div>
    	</div>
    </section>
 <?php else:?>
     
         <div class="card">
              <div class="card-header">
    <h2>No hay Vehiculos</h2>
    <p>No se ha realizado ningun registro.</p>
    </div>
</div>
  <?php endif;?>  

</style>
    <section class="ftco-section">
			<div class="container">
				<div class="row no-gutters">
					<div class="col-md-6 p-md-5 img img-2 d-flex justify-content-center align-items-center" style="background-image: url(img/about.jpg);"></div>
				<div class="col-md-6 wrap-about ftco-animate">

	          <div class="heading-section heading-section-black pl-md-5">
	          	<span class="subheading" style="color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">About us</span>
	            <h2 class="mb-4"><?php echo $title;?></h2>

	            <p>At <?php echo $title;?> we offer reliable, fast service with the best prices on the market. Our modern and well-maintained fleet is ready to accompany you on every adventure.</p>
                <p>Whether you need a vehicle for a business trip, a family vacation, or everyday transportation, we have the perfect option for you. We offer personalized service, available insurance, and home delivery according to your preference.</p>

                <p>Trust us to get your trip off to a great start. At Odisea Rent a Car, your destination is our priority.</p>
	          
	          </div>
					</div>
				</div>
			</div>
		</section>

		<section class="ftco-section">
			<div class="container">
				<div class="row justify-content-center mb-5">
          <div class="col-md-7 text-center heading-section ftco-animate">
          	<span class="subheading" style="color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">Services</span>
            <h2 class="mb-3">Our Latest Services</h2>
          </div>
        </div>
				<div class="row">
					<div class="col-md-3">
						<div class="services services-2 w-100 text-center">
            	<div class="icon d-flex align-items-center justify-content-center" style="color:white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"><span class="flaticon-wedding-car"></span></div>
            	<div class="text w-100">
                <h3 class="heading mb-2">Wedding Ceremony</h3>
                <p>Celebrate your special day with elegance and style. Our premium vehicles ensure a smooth and memorable ride for your wedding ceremony.</p>
              </div>
            </div>
					</div>
					<div class="col-md-3">
						<div class="services services-2 w-100 text-center">
            	<div class="icon d-flex align-items-center justify-content-center" style="color:white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"><span class="flaticon-transportation"></span></div>
            	<div class="text w-100">
                <h3 class="heading mb-2">City Transfer</h3>
                <p>Move comfortably and on time across the city. We offer reliable and efficient transportation for your daily urban travel needs.</p>
              </div>
            </div>
					</div>
					<div class="col-md-3">
						<div class="services services-2 w-100 text-center">
            	<div class="icon d-flex align-items-center justify-content-center" style="color:white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"><span class="flaticon-car"></span></div>
            	<div class="text w-100">
                <h3 class="heading mb-2">Airport Transfer</h3>
                <p>Start or end your journey stress-free. Our punctual airport transfer service gets you to and from the terminal with ease and comfort.</p>
              </div>
            </div>
					</div>
					<div class="col-md-3">
						<div class="services services-2 w-100 text-center">
            	<div class="icon d-flex align-items-center justify-content-center" style="color:white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"><span class="flaticon-transportation"></span></div>
            	<div class="text w-100">
                <h3 class="heading mb-2">Whole City Tour</h3>
                <p>Discover the city’s highlights with our guided tour service. Enjoy every corner and landmark in comfort and safety.</p>
              </div>
            </div>
					</div>
				</div>
			</div>
		</section>

		<section class="ftco-section ftco-intro" style="background-image: url(img/bg_3.jpg);">
			<div class="overlay" style="color:white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"></div>
			<div class="container">
				<div class="row justify-content-end">
					<div class="col-md-6 heading-section heading-section-white ftco-animate">
            <h2 class="mb-3">Come see the list of available vehicles.</h2>
            <a href="car.php" class="btn btn-lg" style="color:white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">List of Vehicles</a>
          </div>
				</div>
			</div>
		</section>


   <?php 
$base = new Database();
$con = $base->connect();
$zql = "select SQL_BIG_RESULT * from cars where status=1 and stock_id=".$selstock." limit 4";
$zuery = $con->query($zql);
    if(count($zuery)>0):?>
   <section class="ftco-section">
      <div class="container">
        <div class="row justify-content-center mb-5">
          <div class="col-md-7 heading-section text-center ftco-animate">
              <span class="subheading" style="color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">Recent Vehicles</span>
            <h2>Rented Vehicles</h2>
          </div>
        </div>
    		<div class="row">
    			<div class="col-md-12">
    				<div class="carousel-car owl-carousel">
    				    
    					    <?php while($z = $zuery->fetch_array()):?>    		    
    					<div class="item">
    						<div class="car-wrap rounded ftco-animate">
		    					<div class="img rounded d-flex align-items-end" style="background-image: url(<?php echo  "https://".$xhost;?>/CF-SYSTEMS/storage/invoice_files/<?php echo $r['invoice_file'];?>);">
		    					</div>
		    					<div class="text">
		    					<h6 class="mb-0" style="color:black;"><?php echo BrandData::getById($z['brand_id'])->name." ".$z['name']." ".$z['year'];?></h6>
		    						<div class="d-flex mb-3">
			    						<span class="cat" style="color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"><?php echo ColorData::getById($z['exterior_id'])->name;?></span>
	    						<p class="price ml-auto">$<?php echo $z['price'];?> <span>/day</span></p>
		    						</div>
		    						<p class="d-flex mb-0 d-block">
		    						     <a href="car-single.php?id=<?php echo $z['id'];?>" class="btn py-2 ml-1" style="color:white; background-color:gray;">Details</a></p>
		    					</div>
		    				</div>
    					</div>
    				<?php endwhile; ?>
    				
    				</div>
    			</div>
    		</div>
    	</div>
    </section>
 <?php else:?>
     
         <div class="card">
              <div class="card-header">
    <h2>No hay Vehiculos</h2>
    <p>No se ha realizado ningun registro.</p>
    </div>
</div>
  <?php endif;?>

    <section class="ftco-counter ftco-section img bg-light" id="section-counter">
			<div class="overlay" style="color:white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"></div>
    	<div class="container">
    		<div class="row">
          <div class="col-md-6 col-lg-3 justify-content-center counter-wrap ftco-animate">
            <div class="block-18">
              <div class="text text-border d-flex align-items-center">
                <strong class="number" data-number="<?php echo count(BookingData::getAllBySQL("where stock_id=".$selstock));?>" ><?php echo count(BookingData::getAllBySQL("where stock_id=".$selstock));?></strong>
                <span>Total <br>Rentals</span>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3 justify-content-center counter-wrap ftco-animate">
            <div class="block-18">
              <div class="text text-border d-flex align-items-center">
                <strong class="number" data-number="<?php echo count(CarsData::getAllBySQL("where stock_id=".$selstock));?>"><?php echo count(CarsData::getAllBySQL("where stock_id=".$selstock));?></strong>
                <span>Total <br>Cars</span>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3 justify-content-center counter-wrap ftco-animate">
            <div class="block-18">
              <div class="text text-border d-flex align-items-center">
                <strong class="number" data-number="<?php echo count(PersonData::getAllBySQL("where stock_id=".$selstock));?>"><?php echo count(PersonData::getAllBySQL("where stock_id=".$selstock));?></strong>
                <span>Happy <br>Customers</span>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3 justify-content-center counter-wrap ftco-animate">
            <div class="block-18">
              <div class="text d-flex align-items-center">
                <strong class="number" data-number="<?php echo count(BrandData::getAll());?>"><?php echo count(BrandData::getAll());?></strong>
                <span>Total <br>Branches</span>
              </div>
            </div>
          </div>
        </div>
    	</div>
    </section>	

   
    
  
<?php include "layout/footer.php";?>