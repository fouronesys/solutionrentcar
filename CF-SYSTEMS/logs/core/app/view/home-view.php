<?php if(isset($_SESSION['user_id'])):
$default_chart = StockData::getPrincipal()->default_chart;
$iva_val = StockData::getPrincipal()->imp_val;

if(Core::$user->kind==3){header('location:./?view=booking&opt=all'); }

$selstock = null;
if(isset($_GET["stock"])){ $selstock=$_GET["stock"]; }
else{ $selstock = StockData::getPrincipal()->id; }

  $dateB = date('Y-m-d', strtotime('-6 hours')); 
  if($default_chart=="apex"){
  $dateA = DateInterval::createFromDateString($dateB);

  }else if($default_chart=="morris"){
  $dateA = DateInterval::createFromDateString($dateB);

  }
//  $dateA = $dateB->sub(DateInterval::createFromDateString('15 days'));
  $sd= $dateA;
  $ed = date("Y-m-d", strtotime('-6 hours'));
  $ntot = 0;
  $nsells = 0;
  $totsells = 0;
  $totmaintenance = 0;
  $totfuel = 0;
  $totoll = 0;  
  $totdeposit = 0;
  $totspend = 0;
  $totpayments = 0;

  $operations = BookingData::getGroupByDateOp( date("Y-m-d", strtotime('-6 hours')), date("Y-m-d", strtotime('-6 hours')),$selstock);

  $deposit = BookingData::getGroupByDateDp( date("Y-m-d", strtotime('-6 hours')), date("Y-m-d", strtotime('-6 hours')),$selstock);
  $spends = SpendData::getGroupByDateOp( date("Y-m-d", strtotime('-6 hours')), date("Y-m-d", strtotime('-6 hours')),1,$selstock);
  $maintenance = MaintenanceData::getGroupByDateOp( date("Y-m-d", strtotime('-6 hours')), date("Y-m-d", strtotime('-6 hours')),$selstock);
  $fuel = FuelsData::getGroupByDateOp( date("Y-m-d", strtotime('-6 hours')), date("Y-m-d", strtotime('-6 hours')),$selstock);
  $toll = TollData::getGroupByDateOp( date("Y-m-d", strtotime('-6 hours')), date("Y-m-d", strtotime('-6 hours')),$selstock);
   $payments = PaymentData::getGroupByDateOp( date("Y-m-d", strtotime('-6 hours')), date("Y-m-d", strtotime('-6 hours')),$selstock);
//  echo $operations[0]->t;
  $sl = $operations[0]->t!=null?$operations[0]->t:0;
  $paymentsx = $payments[0]->t!=null?$payments[0]->t:0;
  $sp = $spends[0]->t!=null?$spends[0]->t:0;
  $dp = $deposit[0]->t!=null?$deposit[0]->t:0;
  $tl = $toll[0]->t!=null?$toll[0]->t:0;
  $fl = $fuel[0]->t!=null?$fuel[0]->t:0;
  $mt = $maintenance[0]->t!=null?$maintenance[0]->t:0;
  $ntot+=(($sl+$dp)-($mt+$sp+$tl+$fl));

  $nsells += $operations[0]->c;
  $totsells+=$sl;
  $totspend+=$sp;
  $totpayments+=$paymentsx;
  $totmaintenance+=$mt;
  $totoll+=$tl;
  $totfuel+=$fl;
  $totdeposit+=$dp;
  ?>

  <section class="content">
      <div class="container-fluid">

<br><br> 

      
<?php if(StockData::getPrincipal()->update=="1"):?>  
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <div class="col-md-8">
            <!-- MAP & BOX PANE -->
    
    
<div class="row">

            <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
            <div class="info-box mb-3" style="background-color: #222;"  >
                   <span class="navbar-badge"><b><a style="color: white;" href="./?view=contract&opt=running"><i class="fa fa-eye fa-2x"></i></a></b></span>
              <span class="info-box-icon bg-info elevation-1"><i class="fa fa-list-ul"></i></span>
                <a style="color: white; " >
              <div class="info-box-content">
                <span class="info-box-text"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculos"; break;
 case 'EN': echo "Vehicles"; break;
}
?></span>
                <span class="info-box-text"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Rentados"; break;
 case 'EN': echo "Rented"; break;
}
?></span>
             <span class="info-box-number"><?php echo count(BookingData::getAllBySQL("where  type_id=1 and status=1 and stock_id=".StockData::getPrincipal()->id)); ?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div>
 
 <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
            <div class="info-box mb-3" style="background-color: #222;" >
                  <span class="navbar-badge"><b><a style="color: white;"  href="./?view=cars&opt=all"><i class="fa fa-eye fa-2x"></i></a></b></span>
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-car"></i></span>
                <a style="color: white; ">
              <div class="info-box-content">
                <span class="info-box-text">Total</span>
                <span class="info-box-text"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculos"; break;
 case 'EN': echo "Vehicles"; break;
}
?></span>
            <span class="info-box-number"><?php echo count(CarsData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id)); ?></span>

              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div>
 


    <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
            <div class="info-box mb-3" style="background-color: #222;" >
                     <span class="navbar-badge"><b><a style="color: white;"  href="./?view=person&opt=all"><i class="fa fa-eye fa-2x"></i></a></b></span>
              <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-users"></i></span>
                <a style="color: white; " >
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Clientes"; break;
 case 'EN': echo "Customers"; break;
}
?></span>
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Registrado"; break;
 case 'EN': echo "Registered"; break;
}
?></span>
            <span class="info-box-number"><?php echo count(PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id)); ?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div>
 
       
             <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
            <div class="info-box mb-3" style="background-color: #222;" >
              <span class="info-box-icon bg-default elevation-1" style="background-color: gray;" ><i class="fa fa-asterisk"></i></span>
                <a style="color: white; ">
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Deducible"; break;
 case 'EN': echo "Deductible"; break;
}
?></span>

                 <span class="info-box-text"><?php echo Core::$symbol; ?> </span>
            <span class="info-box-number"><?php echo number_format($totdeposit,2,".",",");?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 
    

             <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
            <div class="info-box mb-3" style="background-color: #222;" >
              <span class="info-box-icon bg-default elevation-1" style="background-color: gray;"><i class="fas fa-check"></i></span>
                <a style="color: white; ">
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Pagos"; break;
 case 'EN': echo "Payments"; break;
}
?></span>



                 <span class="info-box-text"><?php echo Core::$symbol; ?> </span>
            <span class="info-box-number"><?php echo number_format($totpayments,2,".",",");?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 
    
    
          <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
            <div class="info-box mb-3" style="background-color: #222;" >
              <span class="info-box-icon bg-default elevation-1" style="background-color: gray;"><i class="fa fa-balance-scale"></i></span>
                <a style="color: white; ">
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Ganancias"; break;
 case 'EN': echo "Earnings"; break;
}
?></span>

                 <span class="info-box-text"><?php echo Core::$symbol; ?> </span>
            <span class="info-box-number"><?php echo number_format($totpayments-($totmaintenance+$totspend),2,".",",");?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 
     
    
 <?php if(StockData::getPrincipal()->method=="1"):?> 
<div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
            <div class="info-box mb-3" style="background-color: #222;" >
                      <span class="navbar-badge"><b><a style="color: white;"  href="./?view=booking&opt=all"><i class="fa fa-eye fa-2x"></i></a></b></span>
              <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-edit"></i></span>
                <a style="color: white; " >
              <div class="info-box-content">
                <span class="info-box-text"><?php 
switch (Core::$user->language){
 case 'ES': echo "Reservas"; break;
 case 'EN': echo "Reservations"; break;
}
?></span>
                 <span class="info-box-text">Total </span>
            <span class="info-box-number"><?php echo count(BookingData::getAllBySQL("where status=0 and stock_id=".StockData::getPrincipal()->id)); ?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 
      
 <?php endif;?>
 
      <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
            <div class="info-box mb-3" style="background-color: #222;" >
                    <span class="navbar-badge"><b><a style="color: white;"  href="./?view=finance&opt=all&spends=Negocio"><i class="fa fa-eye fa-2x"></i></a></b></span>
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-minus-square"></i></span>
              <a style="color: white; " >
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Gastos"; break;
 case 'EN': echo "Bills"; break;
}
?></span>

 
                 <span class="info-box-text"><?php echo Core::$symbol; ?> </span>
            <span class="info-box-number"><?php echo number_format($totspend,2,".",",");?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 
       
        
      <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
            <div class="info-box mb-3" style="background-color: #222;" >
                  <span class="navbar-badge"><b><a style="color: white;"  href="./?view=finance&opt=vehicle"><i class="fa fa-eye fa-2x"></i></a></b></span>
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cogs"></i></span>
                <a  style="color: white; " >
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Mantenimiento"; break;
 case 'EN': echo "Maintenance"; break;
}
?></span>


                 <span class="info-box-text"><?php echo Core::$symbol; ?> </span>
            <span class="info-box-number"><?php echo number_format($totmaintenance,2,".",",");?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 
    
             
       
        <!-- /.col -->
      </div>
    
           
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-4">
<?php
$base = new Database();
$con = $base->connect();

/* ================================
   Query: Clientes con más puntos
   ================================ */
$sql = "SELECT p.id, p.name, FLOOR(SUM(b.total) / 100) AS puntos
        FROM person p
        JOIN booking b ON p.id = b.person_id
        WHERE b.status = 1
        GROUP BY p.id
        ORDER BY puntos DESC
        LIMIT 20"; // traemos hasta 20 para probar el scroll

$res = $con->query($sql);

$clientes_top = [];
$total_puntos = 0;

while ($row = $res->fetch_assoc()) {
    $clientes_top[] = $row;
    $total_puntos += $row['puntos'];
}
?>

<div class="card" style="background-color: #222;" >
  <div class="card-header border-transparent">
    <h3 class="card-title">Fidelización - Clientes Frecuentes</h3>
   
  </div>
  <!-- /.card-header -->

  <div class="card-body p-0">
    <div class="table-responsive" style="max-height:305px; overflow-y:auto;"> 
      <!-- 👆 Scroll vertical, ~4 filas dependiendo del alto de cada una -->
      <table class="table m-0">
        <thead>
          <tr>
            <th>Cliente</th>
            <th>Puntos</th>
            <th>Nivel</th>
            
          </tr>
        </thead>
        <tbody>
          <?php foreach ($clientes_top as $c): 
            $porcentaje = $total_puntos > 0 ? round(($c['puntos'] / $total_puntos) * 100, 1) : 0;
            
            // Definir nivel por puntos
            if ($c['puntos'] >= 1000) {
              $nivel = "<span class='badge badge-warning'>Oro</span>";
            } elseif ($c['puntos'] >= 500) {
              $nivel = "<span class='badge badge-secondary'>Plata</span>";
            } else {
              $nivel = "<span class='badge badge-info'>Bronce</span>";
            }
          ?>
          <tr>
            <td><?php echo $c['name']; ?></td>
            <td><span class="badge badge-primary"><?php echo $c['puntos']; ?> pts</span></td>
            <td><?php echo $nivel; ?></td>
           
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <!-- /.table-responsive -->
  </div>
  <!-- /.card-body -->

  <!-- /.card-footer -->
</div>

            
            <!-- /.card -->

          </div>
          <!-- /.col -->
        </div>
        

 
 
      <!-- /.row -->


  <?php 
// Conexión con tu clase personalizada
$base = new Database();
$con = $base->connect();

$anio = date("Y");
$mes  = date("m");

/* ================================
   1. RESERVAS POR MES (Gráfico)
   ================================ */
$sql = "SELECT MONTH(start_at) AS mes, COUNT(*) AS total
        FROM booking
        WHERE status = 1 AND YEAR(start_at) = $anio
        GROUP BY mes ORDER BY mes";

$reservas = array_fill(1, 12, 0); // inicializa con 0
if ($res = $con->query($sql)) {
  while ($row = $res->fetch_assoc()) {
    $reservas[intval($row["mes"])] = intval($row["total"]);
  }
}

$labels = json_encode(["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"]);
$data   = json_encode(array_values($reservas));

/* =======================================
   2. VEHÍCULOS MENOS ALQUILADOS (Barras)
   ======================================= */
$sql2 = "SELECT 
            c.id,
            c.name,
            c.plate,
            COUNT(b.id) AS total_reservas
        FROM cars c
        LEFT JOIN booking b 
               ON c.id = b.car_id 
              AND YEAR(b.start_at) = $anio 
              AND MONTH(b.start_at) = $mes 
              AND b.status = 1
        GROUP BY c.id
        ORDER BY total_reservas ASC
        LIMIT 5";

$vehiculos = [];
if ($res2 = $con->query($sql2)) {
  while ($row = $res2->fetch_assoc()) {
    $vehiculos[] = $row;
  }
}
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
       <div class="card"  style="background-color: #222;">
        <div class="card-header">
          <h5 class="card-title">ANALISIS DE RESERVAS <?php echo $anio; ?></h5>
        </div>
        <div class="card-body">
          <div class="row">
            <!-- Gráfico -->
            <div class="col-md-8">
              <p class="text-center"><strong>RESERVAS POR MES</strong></p>
              <canvas id="bookingsChart" height="180" style="height:180px;"></canvas>
            </div>

            <!-- Vehículos menos alquilados -->
            <div class="col-md-4">
              <p class="text-center"><strong>VEHÍCULOS MENOS ALQUILADOS (MES ACTUAL)</strong></p>

              <?php foreach ($vehiculos as $v): 
                $meta = 20; // meta mensual
                $percent = ($meta > 0) ? ($v["total_reservas"] / $meta) * 100 : 0;
                $color = "bg-danger";
                if ($percent >= 75) $color = "bg-success";
                elseif ($percent >= 50) $color = "bg-warning";
                elseif ($percent >= 25) $color = "bg-primary";
              ?>
              <div class="progress-group">
                <?php echo strtoupper($v["name"] . " (" . $v["plate"] . ")"); ?>
                <span class="float-right"><b><?php echo $v["total_reservas"]; ?></b>/<?php echo $meta; ?></span>
                <div class="progress progress-sm">
                  <div class="progress-bar <?php echo $color; ?>" style="width: <?php echo $percent; ?>%"></div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Footer con métricas -->
        <div class="card-footer">
          <div class="row">
            <div class="col-sm-3 col-6">
              <div class="description-block border-right">
                <h5 class="description-header"><?php echo array_sum($reservas); ?></h5>
                <span class="description-text">TOTAL RESERVAS</span>
              </div>
            </div>
            <div class="col-sm-3 col-6">
              <div class="description-block border-right">
                <h5 class="description-header"><?php echo $reservas[intval($mes)]; ?></h5>
                <span class="description-text">ESTE MES</span>
              </div>
            </div>
            <div class="col-sm-3 col-6">
              <div class="description-block border-right">
                <h5 class="description-header"><?php echo count($vehiculos); ?></h5>
                <span class="description-text">VEHÍCULOS EVALUADOS</span>
              </div>
            </div>
            <div class="col-sm-3 col-6">
              <div class="description-block">
                <h5 class="description-header"><?php echo $anio; ?></h5>
                <span class="description-text">AÑO ACTUAL</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
#bookingsChart {
  height: 250px !important;   /* alto fijo */
  max-height: 250px !important;
  min-height: 250px !important;
}
</style>

<?php
// aseguramos que sea un array simple
$data   = json_encode(array_values($reservas), JSON_NUMERIC_CHECK);

// redondear al múltiplo de 10 más cercano hacia arriba
$maxY = ceil(max($reservas) / 10) * 10;
if ($maxY == 0) $maxY = 10; // valor mínimo por defecto
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
  var ctx = document.getElementById('bookingsChart').getContext('2d');
  var bookingsChart = new Chart(ctx, {
      type: 'line',
      data: {
          labels: <?php echo $labels; ?>,
          datasets: [{
              label: 'Reservas',
              backgroundColor: 'rgba(0,123,255,0.3)',
              borderColor: '#007bff',
              borderWidth: 2,
              pointRadius: 4,
              fill: true,
              data: <?php echo $data; ?>
          }]
      },
      options: {
          responsive: true,
          maintainAspectRatio: true,
          aspectRatio: 2.5, // ancho 2.5 veces mayor que alto
          plugins: {
            legend: { labels: { color: 'white' } }
          },
          scales: {
              x: {
                ticks: { color: 'white' }
              },
              y: {
                beginAtZero: true,
                suggestedMax: <?php echo $maxY; ?>,
                ticks: { color: 'white' }
              }
          }
      }
  });
});
</script>

<?php else:
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

        
<div class="row">

            <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;"  >
                   <span class="navbar-badge"><b><a style="color: white;" href="./?view=contract&opt=running"><i class="fa fa-eye fa-2x"></i></a></b></span>
              <span class="info-box-icon bg-info elevation-1"><i class="fa fa-list-ul"></i></span>
                <a style="color: white; " >
              <div class="info-box-content">
                <span class="info-box-text"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculos"; break;
 case 'EN': echo "Vehicles"; break;
}
?></span>
                <span class="info-box-text"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Rentados"; break;
 case 'EN': echo "Rented"; break;
}
?></span>
             <span class="info-box-number"><?php echo count(BookingData::getAllBySQL("where  type_id=1 and status=1 and stock_id=".StockData::getPrincipal()->id)); ?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div>
 
 <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
                  <span class="navbar-badge"><b><a style="color: white;"  href="./?view=cars&opt=all"><i class="fa fa-eye fa-2x"></i></a></b></span>
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-car"></i></span>
                <a style="color: white; ">
              <div class="info-box-content">
                <span class="info-box-text">Total</span>
                <span class="info-box-text"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculos"; break;
 case 'EN': echo "Vehicles"; break;
}
?></span>
            <span class="info-box-number"><?php echo count(CarsData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id)); ?></span>

              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div>
 


    <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
                     <span class="navbar-badge"><b><a style="color: white;"  href="./?view=person&opt=all"><i class="fa fa-eye fa-2x"></i></a></b></span>
              <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-users"></i></span>
                <a style="color: white; " >
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Clientes"; break;
 case 'EN': echo "Customers"; break;
}
?></span>
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Registrado"; break;
 case 'EN': echo "Registered"; break;
}
?></span>
            <span class="info-box-number"><?php echo count(PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id)); ?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div>
 
 <?php if(StockData::getPrincipal()->method=="1"):?> 
 <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
                      <span class="navbar-badge"><b><a style="color: white;"  href="./?view=booking&opt=all"><i class="fa fa-eye fa-2x"></i></a></b></span>
              <span class="info-box-icon bg-danger elevation-1"><i class="fa fa-edit"></i></span>
                <a style="color: white; " >
              <div class="info-box-content">
                <span class="info-box-text"><?php 
switch (Core::$user->language){
 case 'ES': echo "Reservas"; break;
 case 'EN': echo "Reservations"; break;
}
?></span>
                 <span class="info-box-text">Total </span>
            <span class="info-box-number"><?php echo count(BookingData::getAllBySQL("where status=0 and stock_id=".StockData::getPrincipal()->id)); ?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 
      
 <?php endif;?>
 
        <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
                    <span class="navbar-badge"><b><a style="color: white;"  href="./?view=finance&opt=all&spends=Negocio"><i class="fa fa-eye fa-2x"></i></a></b></span>
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-minus-square"></i></span>
              <a style="color: white; " >
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Gastos"; break;
 case 'EN': echo "Bills"; break;
}
?></span>
                 <span class="info-box-text"><?php echo Core::$symbol; ?> </span>
            <span class="info-box-number"><?php echo number_format($totspend,2,".",",");?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 
       
        
        <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
                  <span class="navbar-badge"><b><a style="color: white;"  href="./?view=finance&opt=vehicle"><i class="fa fa-eye fa-2x"></i></a></b></span>
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-cogs"></i></span>
                <a  style="color: white; " >
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Mantenimiento"; break;
 case 'EN': echo "Maintenance"; break;
}
?></span>
                 <span class="info-box-text"><?php echo Core::$symbol; ?> </span>
            <span class="info-box-number"><?php echo number_format($totmaintenance,2,".",",");?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 
<?php if(StockData::getPrincipal()->method=="1"):?> 
          <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
                 <span class="navbar-badge"><b><a style="color: white;"  href="./?view=finance&opt=vehicle"><i class="fa fa-eye fa-2x"></i></a></b></span>
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-building"></i></span>
                <a  style="color: white; " >
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Peajes"; break;
 case 'EN': echo "Tolls"; break;
}
?></span>
                 <span class="info-box-text"><?php echo Core::$symbol; ?> </span>
            <span class="info-box-number"><?php echo number_format($totoll,2,".",",");?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 

          <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
                 <span class="navbar-badge"><b><a style="color: white;"  href="./?view=finance&opt=vehicle"><i class="fa fa-eye fa-2x"></i></a></b></span>
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-tint"></i></span>
                  <a  style="color: white; " >
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Combustible"; break;
 case 'EN': echo "Fuel"; break;
}
?></span>
                 <span class="info-box-text"><?php echo Core::$symbol; ?> </span>
            <span class="info-box-number"><?php echo number_format($totfuel,2,".",",");?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 

<?php endif;?>
             <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
              <span class="info-box-icon bg-default elevation-1" style="background-color: gray;" ><i class="fa fa-asterisk"></i></span>
                <a style="color: white; ">
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Deducible"; break;
 case 'EN': echo "Deductible"; break;
}
?></span>
                 <span class="info-box-text"><?php echo Core::$symbol; ?> </span>
            <span class="info-box-number"><?php echo number_format($totdeposit,2,".",",");?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 
    

             <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
              <span class="info-box-icon bg-default elevation-1" style="background-color: gray;"><i class="fas fa-check"></i></span>
                <a style="color: white; ">
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Pagos"; break;
 case 'EN': echo "Payments"; break;
}
?></span>
                 <span class="info-box-text"><?php echo Core::$symbol; ?> </span>
            <span class="info-box-number"><?php echo number_format($totpayments,2,".",",");?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 
    
    
          <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
              <span class="info-box-icon bg-default elevation-1" style="background-color: gray;"><i class="fa fa-balance-scale"></i></span>
                <a style="color: white; ">
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Ganancias"; break;
 case 'EN': echo "Earnings"; break;
}
?></span>
                 <span class="info-box-text"><?php echo Core::$symbol; ?> </span>
            <span class="info-box-number"><?php echo number_format($totpayments-($totmaintenance+$totspend),2,".",",");?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 
     
          <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
              <span class="info-box-icon bg-default elevation-1" style="background-color: gray;"><i class="fas fa-bars"></i></span>
                
              <div class="info-box-content">
                <span class="info-box-text"><?php echo StockData::getPrincipal()->imp_name; ?></span>
               <span class="info-box-text"><?php echo Core::$symbol; ?> </span>
            <span class="info-box-number"><?php echo number_format(($totpayments*$iva_val/100),2,".",",");?></span>
              <!-- /.info-box-content -->
              </div>
              
            </div>
            <!-- /.info-box -->
          </div> 
              
            

       
        <!-- /.col -->
      </div>

      <div class="row">
          <div class="col-md-12">
            <div class="card"  style="background-color: #222;">
              <div class="card-header">
                <h5 class="card-title"><?php 
switch (Core::$user->language){
 case 'ES': echo "Balance de los ultimos 30 dias"; break;
 case 'EN': echo "Balance of the last 30 days"; break;
}
?></h5>

<?php 
  $dateB = new DateTime(date('Y-m-d')); 
  $dateA = $dateB->sub(DateInterval::createFromDateString('30 days'));
  $sd= strtotime(date_format($dateA,"Y-m-d"));
  $ed = strtotime(date("Y-m-d"));

?>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="row">
                  <div class="col-md-8">
                    <p class="text-center">
                      <strong><?php echo date_format($dateA,"d-m-Y");?> - <?php echo date("d-m-Y");?></strong>
                    </p>

                    <div class="chart">
                      <!-- Sales Chart Canvas -->
                    

<div id="graph" class="animate" data-animate="fadeInUp"  style="height: 180px;" ></div>
<?php if(Core::$user->language=="ES"):?>
<script>

<?php 
echo "var c=0;";
echo "var dates=Array();";
echo "var data=Array();";
echo "var sells=Array();";
echo "var spends=Array();";
echo "var maintenance=Array();";
echo "var total=Array();";
for($i=$sd;$i<=$ed;$i+=(60*60*24)){
 
  $operations = PaymentData::getGroupByDateOp(date("Y-m-d", $i),date("Y-m-d", $i),$selstock);
  $spends = SpendData::getGroupByDateOp(date("Y-m-d", $i),date("Y-m-d", $i),1,$selstock);
  $maintenance = MaintenanceData::getGroupByDateOp(date("Y-m-d", $i),date("Y-m-d", $i),$selstock);

//  echo $operations[0]->t;
  $sl = $operations[0]->t!=null?$operations[0]->t:0;
  $sp = $spends[0]->t!=null?$spends[0]->t:0;
  $mt = $maintenance[0]->t!=null?$maintenance[0]->t:0;
  echo "dates[c]=\"".date("Y-m-d", $i)."\";";
  echo "data[c]=".((($sl)-($sp+$mt))).";";
  echo "sells[c]=".(($sl)).";";
  echo "spends[c]=0-".(($sp)).";";
  echo "maintenance[c]=0-".(($mt)).";";
  echo "total[c]={x: dates[c],a: data[c], b: sells[c], c: spends[c] , d: maintenance[c] };";
  echo "c++;";
}
?>
// Use Morris.Area instead of Morris.Line
Morris.Line({
  element: 'graph',
  data: total,
  xkey: 'x',
  ykeys: ['a','b','c','d'],
  labels: ['Balance',"Rentas","Gastos","Mantenimientos"],
  lineColors: ['#3F51B5', "#27ae60" , "#e74c3c","gray"],
}).on('click', function(i, row){
  console.log(i, row);
});
</script>

<?php elseif(Core::$user->language=="EN"):?>
<script>

<?php 
echo "var c=0;";
echo "var dates=Array();";
echo "var data=Array();";
echo "var sells=Array();";
echo "var spends=Array();";
echo "var maintenance=Array();";
echo "var total=Array();";
for($i=$sd;$i<=$ed;$i+=(60*60*24)){
 
  $operations = PaymentData::getGroupByDateOp(date("Y-m-d", $i),date("Y-m-d", $i),$selstock);
  $spends = SpendData::getGroupByDateOp(date("Y-m-d", $i),date("Y-m-d", $i),1,$selstock);
  $maintenance = MaintenanceData::getGroupByDateOp(date("Y-m-d", $i),date("Y-m-d", $i),$selstock);

//  echo $operations[0]->t;
  $sl = $operations[0]->t!=null?$operations[0]->t:0;
  $sp = $spends[0]->t!=null?$spends[0]->t:0;
  $mt = $maintenance[0]->t!=null?$maintenance[0]->t:0;
  echo "dates[c]=\"".date("Y-m-d", $i)."\";";
  echo "data[c]=".((($sl)-($sp+$mt))).";";
  echo "sells[c]=".(($sl)).";";
  echo "spends[c]=0-".(($sp)).";";
  echo "maintenance[c]=0-".(($mt)).";";
  echo "total[c]={x: dates[c],a: data[c], b: sells[c], c: spends[c] , d: maintenance[c] };";
  echo "c++;";
}
?>
// Use Morris.Area instead of Morris.Line
Morris.Line({
  element: 'graph',
  data: total,
  xkey: 'x',
  ykeys: ['a','b','c','d'],
  labels: ['Balance',"Income","Bills","Maintenance"],
  lineColors: ['#3F51B5', "#27ae60" , "#e74c3c","gray"],
}).on('click', function(i, row){
  console.log(i, row);
});
</script>
<?php endif;?>

                    </div>
                    <!-- /.chart-responsive -->
                  </div>
                  <!-- /.col -->
                  <div class="col-md-4">
                    <p class="text-center">
                      <strong><?php 
switch (Core::$user->language){
 case 'ES': echo "Top 4 Vehiculos/Mes"; break;
 case 'EN': echo "Top 4 Vehicles/Month"; break;
}
?></strong>
                    </p>

<?php
$today = time();
$last30 = $today - (60*60*24*30);
$today_at = date("Y-m-d",$today);
$last30_at = date("Y-m-d",$last30);
$allops = BookingData::get10Popular($selstock,$last30_at,$today_at);
?>      

<?php foreach($allops as $opx): $product = CarsData::getById($opx->car_id);?>
                    <div class="progress-group">
                      <?php echo $product->getBrand()->name." ".$product->name." ".$product->year; ?>
                      <span class="float-right"><b><?php echo $opx->total; ?></b></span>
                      <div class="progress progress-sm">
                        <div class="progress-bar bg-success" style="width: <?php echo $opx->total; ?>%"></div>
                      </div>
                    </div>
                    <!-- /.progress-group -->
<?php endforeach; ?>

                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
              <!-- ./card-body -->
              <div class="card-footer" style="background-color: #333;">
                   <p class="text-center">
                      <strong><?php 
switch (Core::$user->language){
 case 'ES': echo "LOS 4 VEHICULOS CON MENOS RENTAS DEL AÑO"; break;
 case 'EN': echo "THE 4 VEHICLES WITH THE LEAST INCOME OF THE YEAR"; break;
}
?></strong>
                    </p>
                <div class="row">
                
                  <!-- /.col -->
                  
<?php 
$base = new Database();
$con = $base->connect();
$sql ="SELECT 
    v.id,
    v.name,
    COUNT(r.id) AS total_registros
FROM 
    cars v
LEFT JOIN 
    booking r
ON 
    v.id = r.car_id AND YEAR(r.created_at) = ".date("Y")."
GROUP BY 
    v.id
ORDER BY 
    total_registros ASC limit 4;";
$query = $con->query($sql);
while($r = $query->fetch_array()){ $cars = CarsData::getById($r["id"]);?>
                     <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                      <span class="description-percentage text-warning"><i class="fas fa-caret-right"></i> <?php echo $r["total_registros"];?>%</span>
                      <h5 class="description-header"><?php echo $cars->getBrand()->name." ".$cars->name; ?></h5>
                      <span class="description-text"><?php echo $cars->year." [".$cars->plate."]"; ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>
<?php }; ?>

                  <!-- /.col -->
               
              
                </div>
                <!-- /.row -->
              </div>
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

<?php endif;?>
</div>
      <!-- /.row -->

</section>        
<?php elseif(isset($_SESSION['client_id'])):
    
$clistock= PersonData::getById($_SESSION["client_id"]);
$selstock = StockData::getById($_SESSION['stock_id']); 
$default_chart = $selstock->default_chart;
$iva_val = $selstock->imp_val;
$TicketMm = $selstock->ticket_mm;


$dateB = new DateTime(date('Y-m-d', strtotime('-6 hours'))); 
  if($default_chart=="apex"){
  $dateA = $dateB->sub(DateInterval::createFromDateString('Y-m-d', strtotime('-6 hours')));

  }else if($default_chart=="morris"){
  $dateA = $dateB->sub(DateInterval::createFromDateString('Y-m-d', strtotime('-6 hours')));

}
//  $dateA = $dateB->sub(DateInterval::createFromDateString('15 days'));
  $sd= strtotime(date_format($dateA,"Y-m-d", strtotime('-6 hours')));
  $ed = strtotime(date("Y-m-d", strtotime('-6 hours')));
  $ntot = 0;
  $nsells = 0;
  $totsells = 0;
  $totmaintenance = 0;
  $totfuel = 0;
  $totoll = 0;  
  $totdeposit = 0;
  $totspend = 0;
  $totpayments = 0;

  $operations = BookingData::getGroupByDateOp( date("Y-m-d", strtotime('-6 hours')), date("Y-m-d", strtotime('-6 hours')),$selstock->id);

  $deposit = BookingData::getGroupByDateDp( date("Y-m-d", strtotime('-6 hours')), date("Y-m-d", strtotime('-6 hours')),$selstock->id);
  $spends = SpendData::getGroupByDateOp( date("Y-m-d", strtotime('-6 hours')), date("Y-m-d", strtotime('-6 hours')),1,$selstock->id);
  $maintenance = MaintenanceData::getGroupByDateOp( date("Y-m-d", strtotime('-6 hours')), date("Y-m-d", strtotime('-6 hours')),$selstock->id);
  $fuel = FuelsData::getGroupByDateOp( date("Y-m-d", strtotime('-6 hours')), date("Y-m-d", strtotime('-6 hours')),$selstock->id);
  $toll = TollData::getGroupByDateOp( date("Y-m-d", strtotime('-6 hours')), date("Y-m-d", strtotime('-6 hours')),$selstock->id);
   $payments = PaymentData::getGroupByDateOp( date("Y-m-d", strtotime('-6 hours')), date("Y-m-d", strtotime('-6 hours')),$selstock->id);
//  echo $operations[0]->t;
  $sl = $operations[0]->t!=null?$operations[0]->t:0;
  $paymentsx = $payments[0]->t!=null?$payments[0]->t:0;
  $sp = $spends[0]->t!=null?$spends[0]->t:0;
  $dp = $deposit[0]->t!=null?$deposit[0]->t:0;
  $tl = $toll[0]->t!=null?$toll[0]->t:0;
  $fl = $fuel[0]->t!=null?$fuel[0]->t:0;
  $mt = $maintenance[0]->t!=null?$maintenance[0]->t:0;
  $ntot+=(($sl+$dp)-($mt+$sp+$tl+$fl));

  $nsells += $operations[0]->c;
  $totsells+=$sl;
  $totspend+=$sp;
  $totpayments+=$paymentsx;
  $totmaintenance+=$mt;
  $totoll+=$tl;
  $totfuel+=$fl;
  $totdeposit+=$dp;
  ?>

  
  <section class="content">
      <div class="container-fluid">

    <div class="content-header">
      <div class="container-fluid">
         <div class="row ">
          <div class="">
           <h1 class="m-0"><i class="fa fa-car"></i> <?php echo  strtoupper($selstock->name);  ?></h1>
      
          </div><!-- /.col -->
          
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

<div class="row">

            <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
              <span class="info-box-icon bg-info elevation-1"><i class="fa fa-car"></i></span>
                <a style="color: white; " href="">
              <div class="info-box-content">
                <span class="info-box-text"> 
<?php 
switch ($clistock->language){
 case 'ES': echo "Vehiculos"; break;
 case 'EN': echo "Vehicles"; break;
}
?></span>
                <span class="info-box-text">
<?php 
switch ($clistock->language){
 case 'ES': echo "Rentados"; break;
 case 'EN': echo "Rented"; break;
}
?></span>
            <span class="info-box-number"><?php echo count(BookingData::getAllBySQL("where status=1 and person_id=".$clistock->id." and stock_id=".$selstock->id)); ?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div>
 
 <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-car"></i></span>
                <a style="color: white; " href="">
              <div class="info-box-content">
                <span class="info-box-text"> 
<?php 
switch ($clistock->language){
 case 'ES': echo "Vehiculos"; break;
 case 'EN': echo "Vehicles"; break;
}
?></span>
  
<?php 
switch ($clistock->language){
 case 'ES': echo "Reservados"; break;
 case 'EN': echo "Reserved"; break;
}
?>          
            <span class="info-box-number"><?php echo count(BookingData::getAllBySQL("where status=0 and person_id=".$clistock->id." and stock_id=".$selstock->id)); ?></span>

              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div>
 


    <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
              <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-random"></i></span>
                <a style="color: white; " href="">
              <div class="info-box-content">
                <span class="info-box-text"> 
<?php 
switch ($clistock->language){
 case 'ES': echo "Vehiculos"; break;
 case 'EN': echo "Vehicles"; break;
}
?></span>
                <span class="info-box-text"> 
<?php 
switch ($clistock->language){
 case 'ES': echo "Reemplazados"; break;
 case 'EN': echo "Replaced"; break;
}
?> </span>
            <span class="info-box-number"><?php echo count(BookingData::getAllBySQL("where car2_id>0 and person_id=".$clistock->id." and stock_id=".$selstock->id)); ?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div>
 
 <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="info-box mb-3" style="background-color: #222;" >
              <span class="info-box-icon bg-danger elevation-1"><i class="fa fa-check"></i></span>
                <a style="color: white; " href="">
              <div class="info-box-content">
                <span class="info-box-text">
<?php 
switch ($clistock->language){
 case 'ES': echo "Reservas"; break;
 case 'EN': echo "Reservations"; break;
}
?></span>
                 <span class="info-box-text">
<?php 
switch ($clistock->language){
 case 'ES': echo "Completadas"; break;
 case 'EN': echo "Completed"; break;
}
?> </span>
            <span class="info-box-number"><?php echo count(BookingData::getAllBySQL("where status=3 and person_id=".$clistock->id." and stock_id=".$selstock->id)); ?></span>
              <!-- /.info-box-content -->
              </div>
               </a>
            </div>
            <!-- /.info-box -->
          </div> 
          
              <!-- Default box -->
     <?php foreach(BookingData::getAllBySQL("where (status=1 || status=0) and person_id=".$clistock->id." and stock_id=".$selstock->id) as $book): $cars = CarsData::getById($book->car_id);?>
            <div class="col-12 col-sm-6 col-md-6 d-flex align-items-stretch flex-column">
              <div class="card">
                 <div class="card-footer">
                  <?php 
switch ($clistock->language){
 case 'ES': echo "DETALLES DE LA RESERVACION"; break;
 case 'EN': echo "RESERVATION DETAILS"; break;
}
?>
                </div>      
<?php if($book->firma==""):?>           
                <a href="./?view=booking&opt=signature&id=<?php echo $book->id; ?>" class="btn btn-sm btn-warning  text-white">
                      <i class="fas fa-check"></i> <?php 
switch ($clistock->language){
 case 'ES': echo "CONFIRMAR RESERVACION"; break;
 case 'EN': echo "CONFIRM RESERVATION"; break;
}
?>    </a>

<?php endif;?>
<br>
                <div class="card-body pt-0">
                  <div class="row">
                    <div class="col-12">
                      <h3><b><?php if($book->status==1):?><?php 
switch ($clistock->language){
 case 'ES': echo "ACTIVA"; break;
 case 'EN': echo "ACTIVE"; break;
}
?><?php else:?><?php 
switch ($clistock->language){
 case 'ES': echo "RESERVADA"; break;
 case 'EN': echo "RESERVED"; break;
}
?><?php endif;?></b></h3>
                      <p class="text-white text-sm"><b><?php 
switch ($clistock->language){
 case 'ES': echo "DESDE"; break;
 case 'EN': echo "FROM"; break;
}
?>: </b> <?php echo date("d-m-Y h:i:s a ",strtotime($book->start_at));?>
                       <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php 
switch ($clistock->language){
 case 'ES': echo "HASTA"; break;
 case 'EN': echo "UNTIL"; break;
}
?>: </b> <?php echo date("d-m-Y h:i:s a ",strtotime($book->end_at));?> </p>
                       
                       <p class="text-white text-sm"><b><?php 
switch ($clistock->language){
 case 'ES': echo "DIAS"; break;
 case 'EN': echo "DAYS"; break;
}
?>: </b> <?php echo $book->day;?> <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php 
switch ($clistock->language){
 case 'ES': echo "DEPOSITO"; break;
 case 'EN': echo "DEPOSIT"; break;
}
?>: </b> <?php echo $book->payment;?> <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php 
switch ($clistock->language){
 case 'ES': echo "DEDUCIBLE"; break;
 case 'EN': echo "DEDUCTIBLE"; break;
}
?>: </b> <?php echo $book->deposit;?> <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TOTAL: </b> <?php echo $book->total." ".StockData::getbyId($clistock->stock_id)->currency;?>  </p>
                       
                       <p class="text-white text-sm"><b> <?php 
switch ($clistock->language){
 case 'ES': echo "RECIBIR"; break;
 case 'EN': echo "RECEIVE"; break;
}
?>: </b> <?php echo strtoupper($book->place_start);?><br>
                        <b> <?php 
switch ($clistock->language){
 case 'ES': echo "ENTREGAR"; break;
 case 'EN': echo "DELIVER"; break;
}
?>: </b> <?php echo strtoupper($book->place_end);?></p>
                      </ul>
                    </div>
                  
                  </div>
                </div>
              
              </div>
            </div>
            <div class=" col-12 col-sm-6 col-md-6 d-flex align-items-stretch flex-column">
              <div class="card">
                <div class="card-header text-muted border-bottom-0">
                 <?php 
switch ($clistock->language){
 case 'ES': echo "DETALLES DEL VEHICULO"; break;
 case 'EN': echo "VEHICLE DETAILS"; break;
}
?>
                </div><br>
                <div class="card-body pt-0">
                  <div class="row">
                    <div class="col-7">
                      <h3><b><?php echo $cars->getBrand()->name." ".$cars->name;?></b></h3>
                      <p class="text-white text-sm"><b><?php 
switch ($clistock->language){
 case 'ES': echo "CATEGORIA"; break;
 case 'EN': echo "CATEGORY"; break;
}
?>: </b><?php echo $cars->getCategory()->name;?></p>
                      <p class="text-white text-sm"><b><?php 
switch ($clistock->language){
 case 'ES': echo "PLACA"; break;
 case 'EN': echo "PLATE"; break;
}
?>: </b><?php echo $cars->plate;?>
                      <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;COLOR: </b><?php echo strtoupper($cars->getExColor()->name);?>
                     
                    </div>
                    <div class="col-5 text-center">
                      <img src="../CF-SYSTEMS/storage/invoice_files/<?php echo $cars->invoice_file; ?>" style="height: 100px; width 120px; " class="img-fluid w-100 rounded-top">
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="text-right">
                    <?php if($book->status==1):?>
                   <a href="<?php echo $TicketMm; ?>/ticket-client.php?id=<?php echo $book->id; ?>" class="btn btn-sm btn-primary">
                      <i class="fas fa-eye"></i> <?php 
switch ($clistock->language){
 case 'ES': echo "VER CONTRATO"; break;
 case 'EN': echo "SEE CONTRACT"; break;
}
?></a>
                <?php else:?>
                <a href="<?php echo $TicketMm; ?>/ticket-reserve-client.php?id=<?php echo $book->id; ?>" class="btn btn-sm btn-primary">
                      <i class="fas fa-eye"></i> <?php 
switch ($clistock->language){
 case 'ES': echo "VER RESERVA"; break;
 case 'EN': echo "SEE RESERVATION"; break;
}
?>    </a>
                <?php endif;?>
                
                  </div>
                </div>
              </div>
            </div>
   
  <?php endforeach;?>        
        
      

       
        <!-- /.col -->
      </div>
 



</div>
      <!-- /.row -->
      
       
</section>  
 
<?php endif;?>




