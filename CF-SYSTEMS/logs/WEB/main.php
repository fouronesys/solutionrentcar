
<?php include "layout/header.php";?>
    
    <section class="hero-wrap hero-wrap-2 js-fullheight" style="background-image: url('img/bg_3.jpg');" data-stellar-background-ratio="0.5">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text js-fullheight align-items-end justify-content-start">
          <div class="col-md-9 ftco-animate pb-5">
          	<p class="breadcrumbs"><span class="mr-2"><a href="index.html">Home <i class="ion-ios-arrow-forward"></i></a></span> <span>Pricing <i class="ion-ios-arrow-forward"></i></span></p>
            <h1 class="mb-3 bread">Pricing</h1>
          </div>
        </div>
      </div>
    </section>

    <section class="ftco-section ftco-cart">
			<div class="container">
				<div class="row">
				     <h3 class="mb-3 bread"><?php echo "From: ". $_GET['date_at']." : ".$_GET['time_pick']." - Until: ".$_GET['date_end']." : ".$_GET['time_pick'];?> </h3>
    			<div class="col-md-12 ftco-animate">
    				<div class="car-list">
	    				<table class="table">
						    <thead class="thead-primary">
						      <tr class="text-center">
						        <th>&nbsp;</th>
						        <th>&nbsp;</th>
						        <th class="bg-dark heading" style="color:white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">Per Day Rate</th>
						       
						      </tr>
						    </thead>
						    <tbody>
<?php  
$start = date("Y-m-d H:i", strtotime(str_replace("/", "-", $_GET['date_at']) . " " . $_GET['time_pick']));
$end = date("Y-m-d H:i", strtotime(str_replace("/", "-", $_GET['date_end']) . " " . $_GET['time_pick']));



$base = new Database();
$con = $base->connect();
$sql = "SELECT *
FROM cars
WHERE NOT EXISTS (
    SELECT car_id
    FROM booking
    WHERE cars.id = booking.car_id 
    AND (
        (booking.start_at BETWEEN \"".$start."\" AND \"".$end."\") 
        OR 
        (booking.end_at BETWEEN \"".$start."\" AND \"".$end."\")
    ) 
    AND booking.stock_id = \"".$selstock."\"
) 
AND cars.stock_id = \"".$selstock."\""; 

$query = $con->query($sql);
while($r = $query->fetch_array()): $cars = CarsData::getById($r["id"]);?>      
						        
						      <tr>
						      	<td class="car-image"><div class="img" style="background-image:url(<?php echo  "https://".$xhost;?>/CF-SYSTEMS/storage/invoice_files/<?php echo $r['invoice_file'];?>);"></div></td>
						        <td class="product-name">
						        	<h3><b><?php echo BrandData::getById($r['brand_id'])->name." ".$r['name']." ".$r['year'];?></b></h3>
						        	<p class="mb-0"><b  style="color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">Color:</b> <?php echo ColorData::getById($r['exterior_id'])->name;?></p>
						        	<p class="mb-0"><b  style="color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">Seat:</b> <?php echo $cars->seat;?></p>
						        	<p class="mb-0"><b  style="color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">Fuel:</b> <?php echo $cars->getFuel()->name;?></p>
						        	<p class="mb-0"><b  style="color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">Transmission:</b> <?php echo $cars->getTransmission()->name;?></p>
						        </td>
						        
						        <td class="price" style="color:white; background-color:#CFD1D1;">
						        	<p class="btn-custom"><a href="reserved.php?id=<?php echo $r['id'];?>&date_at=<?php echo $_GET['date_at'];?>&date_end=<?php echo $_GET['date_end'];?>&time_pick=<?php echo $_GET['time_pick'];?>" style="color:white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">Book Now</a></p>
						        	<div class="price-rate">
							        	<h3>
							        		<span class="num" style="color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"><small class="currency">$</small> <?php echo $r['price'];?></span>
							        		<span class="per" style="color:black;">/per day</span>
							        	</h3>
							        	<span class="subheading" style="color:black;">1/4 fuel upon delivery</span>
						        	</div>
						        </td>
						        
						      

						       
						      </tr><!-- END TR-->

						    
<?php endwhile;?>

						    </tbody>
						  </table>
					  </div>
    			</div>
    		</div>
			</div>
		</section>


   
<?php include "layout/footer.php";?>