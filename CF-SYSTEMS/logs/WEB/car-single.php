
<?php include "layout/header.php"; $cars = CarsData::getById($_GET["id"]);?>
    
    <section class="hero-wrap hero-wrap-2 js-fullheight" style="background-image: url('img/bg_3.jpg');" data-stellar-background-ratio="0.5">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text js-fullheight align-items-end justify-content-start">
          <div class="col-md-9 ftco-animate pb-5">
          	<p class="breadcrumbs"><span class="mr-2"><a href="index.html">Home <i class="ion-ios-arrow-forward"></i></a></span> <span>Car details <i class="ion-ios-arrow-forward"></i></span></p>
            <h1 class="mb-3 bread">Car Details</h1>
          </div>
        </div>
      </div>
    </section>
		

		<section class="ftco-section ftco-car-details">
      <div class="container">
      	<div class="row justify-content-center">
      		<div class="col-md-12">
      			<div class="car-details">
      				<div class="img rounded" style="background-image: url(<?php echo  "https://".$xhost;?>/CF-SYSTEMS/storage/invoice_files/<?php echo $cars->invoice_file;?>);"></div>
      				<div class="text text-center">
      					<span class="subheading">Cheverolet</span>
      					<h2><?php echo BrandData::getById($cars->brand_id)->name." ".$cars->name." ".$cars->year;?></h2>
      				</div>
      			</div>
      		</div>
      	</div>
       	<div class="row">
      		<div class="col-md d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services">
              <div class="media-body py-md-4">
              	<div class="d-flex mb-3 align-items-center">
	              	<div class="icon d-flex align-items-center justify-content-center" style="background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"><span style="color:white;" class="fas fa-palette" ></span></div>
	              	<div class="text">
		                <h3 class="heading mb-0 pl-3">
		                	Color
		                	<span><?php echo ColorData::getById($cars->exterior_id)->name;?></span>
		                </h3>
	                </div>
                </div>
              </div>
            </div>      
          </div>
          <div class="col-md d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services">
              <div class="media-body py-md-4">
              	<div class="d-flex mb-3 align-items-center">
	              	<div class="icon d-flex align-items-center justify-content-center" style="background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"><span style="color:white;" class="flaticon-pistons"></span></div>
	              	<div class="text">
		                <h3 class="heading mb-0 pl-3">
		                	Transmission
		                	<span><?php echo $cars->getTransmission()->name;?></span>
		                </h3>
	                </div>
                </div>
              </div>
            </div>      
          </div>
          <div class="col-md d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services">
              <div class="media-body py-md-4">
              	<div class="d-flex mb-3 align-items-center">
	              	<div class="icon d-flex align-items-center justify-content-center" style="background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"><span style="color:white;" class="flaticon-car-seat"></span></div>
	              	<div class="text">
		                <h3 class="heading mb-0 pl-3">
		                	Seats
		                	<span><?php echo $cars->seat;?></span>
		                </h3>
	                </div>
                </div>
              </div>
            </div>      
          </div>
          <div class="col-md d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services">
              <div class="media-body py-md-4">
              	<div class="d-flex mb-3 align-items-center">
	              	<div class="icon d-flex align-items-center justify-content-center" style="background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"><span style="color:white;" class="flaticon-backpack"></span></div>
	              	<div class="text">
		                <h3 class="heading mb-0 pl-3">
		                	Luggage
		                	<span>4 Bags</span>
		                </h3>
	                </div>
                </div>
              </div>
            </div>      
          </div>
          <div class="col-md d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services">
              <div class="media-body py-md-4">
              	<div class="d-flex mb-3 align-items-center">
	              	<div class="icon d-flex align-items-center justify-content-center" style="background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"><span style="color:white;" class="flaticon-diesel"></span></div>
	              	<div class="text">
		                <h3 class="heading mb-0 pl-3">
		                	Fuel
		                	<span><?php echo $cars->getFuel()->name;?></span>
		                </h3>
	                </div>
                </div>
              </div>
            </div>      
          </div>
      	</div>
      	<div class="row">
      		<div class="col-md-12 pills">
						<div class="bd-example bd-example-tabs">
							<div class="d-flex justify-content-center">
							  <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">

							    <li class="nav-item">
							      <a class="nav-link active" id="pills-description-tab" data-toggle="pill" href="#pills-description" role="tab" aria-controls="pills-description" aria-expanded="true">Features</a>
							    </li>
						
							   
							  </ul>
							</div>

						  <div class="tab-content" id="pills-tabContent">
						    <div class="tab-pane fade show active" id="pills-description" role="tabpanel" aria-labelledby="pills-description-tab">
						    	<div class="row">
						    		<div class="col-md-4">
						    			<ul class="features">
						    				<li class="check"><span class="ion-ios-checkmark"></span>Airconditions</li>
						    				<li class="check"><span class="ion-ios-checkmark"></span>Child Seat</li>
						    				<li class="check"><span class="ion-ios-checkmark"></span>GPS</li>
						    				<li class="check"><span class="ion-ios-checkmark"></span>Luggage</li>
						    				<li class="check"><span class="ion-ios-checkmark"></span>Music</li>
						    			</ul>
						    		</div>
						    		<div class="col-md-4">
						    			<ul class="features">
						    				<li class="check"><span class="ion-ios-checkmark"></span>Seat Belt</li>
						    				<li class="remove"><span class="ion-ios-close"></span>Sleeping Bed</li>
						    				<li class="check"><span class="ion-ios-checkmark"></span>Water</li>
						    				<li class="check"><span class="ion-ios-checkmark"></span>Bluetooth</li>
						    				<li class="remove"><span class="ion-ios-close"></span>Onboard computer</li>
						    			</ul>
						    		</div>
						    		<div class="col-md-4">
						    			<ul class="features">
						    				<li class="check"><span class="ion-ios-checkmark"></span>Audio input</li>
						    				<li class="check"><span class="ion-ios-checkmark"></span>Long Term Trips</li>
						    				<li class="check"><span class="ion-ios-checkmark"></span>Car Kit</li>
						    				<li class="check"><span class="ion-ios-checkmark"></span>Remote central locking</li>
						    				<li class="check"><span class="ion-ios-checkmark"></span>Climate control</li>
						    			</ul>
						    		</div>
						    	</div>
						    </div>

						    
						  </div>
						</div>
		      </div>
				</div>
      </div>
    </section>
<?php 
$base = new Database();
$con = $base->connect();
$sql = "SELECT * FROM cars WHERE category_id = ".$cars->category_id." AND id <> ".$cars->id." AND status <> 1 LIMIT 3";
$query = $con->query($sql);
if ($query && $query->num_rows > 0):?>     

    <section class="ftco-section ftco-no-pt">
    	<div class="container">
    		<div class="row justify-content-center">
          <div class="col-md-12 heading-section text-center ftco-animate mb-5">
          	<span class="subheading">Choose Car</span>
            <h2 class="mb-2">Related Cars</h2>
          </div>
        </div>
        <div class="row">
<?php while($r = $query->fetch_array()): $cars = CarsData::getById($r["id"]);?>
        <div class="col-md-4">
				<div class="car-wrap rounded ftco-animate">
					<div class="img rounded d-flex align-items-end" style="background-image: url(https://<?php echo $xhost;?>/CF-SYSTEMS/storage/invoice_files/<?php echo $cars->invoice_file;?>);">
					</div>
    					<div class="text">
    					<h6 class="mb-0" style="color:black;"><?php echo BrandData::getById($cars->brand_id)->name." ".$cars->name." ".$cars->year;?></h6>
		    						<div class="d-flex mb-3">
			    						<span class="cat" style="color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"><?php echo ColorData::getById($cars->exterior_id)->name;?></span>
	    					<p class="price ml-auto">$<?php echo number_format($cars->price, 2);?> <span>/day</span></p>
    						</div>
    						<p class="d-flex mb-0 d-block"><a href="booking?id=<?php echo $cars->id;?>" class="btn  py-2 mr-1" style="color: white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">Rent Now</a>
							<a href="car-single.php?id=<?php echo $cars->id;?>" class="btn py-2 ml-1" style="color: white; background-color:gray;">Details</a></p>
    					</div>
    				</div>
    			</div>
   <?php endwhile;?> 		
    	
        </div>
    	</div>
    </section>
    
<?php endif;  include "layout/footer.php";?>