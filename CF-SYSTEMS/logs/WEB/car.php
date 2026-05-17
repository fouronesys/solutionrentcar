
<?php include "layout/header.php";?>
    
    <section class="hero-wrap hero-wrap-2 js-fullheight" style="background-image: url('img/bg_3.jpg');" data-stellar-background-ratio="0.5">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text js-fullheight align-items-end justify-content-start">
          <div class="col-md-9 ftco-animate pb-5">
          	<p class="breadcrumbs"><span class="mr-2"><a href="index.html">Home <i class="ion-ios-arrow-forward"></i></a></span> <span>Cars <i class="ion-ios-arrow-forward"></i></span></p>
            <h1 class="mb-3 bread">Choose Your Car</h1>
          </div>
        </div>
      </div>
    </section>
		
<?php 
$base = new Database();
$con = $base->connect();

$registros_por_pagina = 12;
$pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$inicio = ($pagina_actual - 1) * $registros_por_pagina;

$selstock = (int)$selstock; // asegura que sea número

// Total de registros
$sql_total = "SELECT COUNT(*) as total FROM cars WHERE stock_id = $selstock";
$res_total = $con->query($sql_total);
$row_total = $res_total->fetch_assoc();
$total_registros = $row_total['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta con LIMIT para paginar
$sql = "SELECT SQL_BIG_RESULT * FROM cars WHERE stock_id = $selstock LIMIT $inicio, $registros_por_pagina";
$query = $con->query($sql);

if ($query->num_rows > 0):
?>
<section class="ftco-section bg-light">
  <div class="container">
    <div class="row">
      <?php while($r = $query->fetch_array()):?>    		    
        <div class="col-md-4">
          <div class="car-wrap rounded ftco-animate">
            <div class="img rounded d-flex align-items-end" style="background-image: url(<?php echo  "https://".$xhost;?>/CF-SYSTEMS/storage/invoice_files/<?php echo $r['invoice_file'];?>);">
            </div>
            <div class="text">
              	<h6 class="mb-0" style="color:black;"><?php echo BrandData::getById($r['brand_id'])->name." ".$r['name']." ".$r['year'];?></h6>
		    						<div class="d-flex mb-3">
			    						<span class="cat" style="color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);"><?php echo ColorData::getById($r['exterior_id'])->name;?></span>
                <p class="price ml-auto">$<?php echo $r['price'];?> <span>/day</span></p>
              </div>
              <p class="d-flex mb-0 d-block">
                <?php if($r['status'] <> 1): ?>
                 <p class="d-flex mb-0 d-block"><a href="booking?id=<?php echo $r['id'];?>" class="btn  py-2 mr-1" style="color: white; background-color:rgb(<?php echo $color[0];?>, <?php echo $color[1];?>,<?php echo $color[2];?>);">Rent Now</a>
                <?php endif;?>
                <a href="car-single.php?id=<?php echo $r['id'];?>"  class="btn py-2 ml-1" style="color:white; background-color:gray;">Details</a>
              </p>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

    <?php if ($total_paginas > 1): ?>
    <div class="row mt-5">
      <div class="col text-center">
        <div class="block-27">
          <ul>
            <?php if ($pagina_actual > 1): ?>
              <li><a href="?page=<?php echo $pagina_actual - 1; ?>">&lt;</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
              <li class="<?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                <a href="?page=<?php echo $i; ?>"><span><?php echo $i; ?></span></a>
              </li>
            <?php endfor; ?>

            <?php if ($pagina_actual < $total_paginas): ?>
              <li><a href="?page=<?php echo $pagina_actual + 1; ?>">&gt;</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
  </div>
</section>
<?php else: ?>
  <div class="card">
    <div class="card-header">
      <h2>No hay Vehiculos</h2>
      <p>No se ha realizado ningun registro.</p>
    </div>
  </div>
<?php endif; ?>


    
<?php include "layout/footer.php";?>