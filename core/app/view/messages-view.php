<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<!-- Main content -->
<section class="content">
        <!-- Content Header (Page header) -->
<div class="content-header">
<div class="container-fluid">
<div class="row mb-2">
<div class="col-sm-6">
<h1 class="m-0"><i class="fa fa-comments"></i> Entregas
</div><!-- /.col -->

<div class="col-sm-6">
  <ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">
      <i class='fa fa-history'></i> 
      <span id="reloj"></span>
    </li>
  </ol>
</div><!-- /.col -->

<script>
// Función para actualizar la hora en tiempo real
function actualizarReloj() {
  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  document.getElementById("reloj").textContent = `${horas}:${minutos}:${segundos}`;
}

// Actualiza cada segundo
setInterval(actualizarReloj, 1000);
actualizarReloj(); // Llamada inicial
</script>


</div>

<div class="row">
<div class="col-md-12">

<div class="box box-solid">
  <div class="card" style="background-color:#222; color: #fff;">
    <div class="modal-body">
      <div class="text-center py-2">
        <?php
          $book_inicio = date('Y-m-d');
          $book_fin = date('Y-m-d', strtotime('+1 day'));
          echo "<strong>$book_inicio / $book_fin</strong>";
        ?>
      </div>
    </div>
  </div>
</div>

<!-- /. box -->

<!-- /.box -->
</div>
<!-- /.col -->
<div class="col-md-6">  
<!-- /.box-header -->

<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
<thead>
<th>Cliente</th>
<th>Vehiculo</th>
<th>Entrega</th>
</thead>
 
<?php 
$base = new Database();
$con = $base->connect();

$book_inicio = date('Y-m-d H:i:s');
$book_fin = date('Y-m-d H:i:s', strtotime('+1 day'));

$book_sql = "SELECT c.id, c.name, c.phone, c.address, c.latitud, c.longitud,
                    b.start_at, b.place_end,
                    car.name AS car_name, car.year AS car_year,
                    brand.name AS brand_name
             FROM person c
             INNER JOIN booking b ON b.person_id = c.id
             INNER JOIN cars car ON b.car_id = car.id
             INNER JOIN brand ON car.brand_id = brand.id
             WHERE b.status<>3 and b.start_at BETWEEN '$book_inicio' AND '$book_fin'
             ORDER BY b.start_at ASC";



$book_query = $con->query($book_sql);
while($book = $book_query->fetch_assoc()):?>
<tr>
  <td class="mailbox-name"><?php echo $book["name"]; ?></td>
  <td class="mailbox-name">
    <?php echo strtoupper($book["brand_name"] . ' ' . $book["car_name"]) . ' (' . $book["car_year"] . ')'; ?>
  </td>
  <td class="mailbox-date">
    <?php echo date('d/m/Y h:i A', strtotime($book["end_at"])); ?>
  </td>
</tr>
<tr>
  <td colspan="3">
   <?php echo $book["place_end"]; ?>
  </td>
</tr>

  <?php endwhile; ?>
</table>
</div>
</div>
</div>

                <!-- /.table -->
             

          </div>
          
<div class="col-md-6">  
<!-- /.box-header -->

<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example3">
<thead>
<th>Cliente</th>
<th>Vehiculo</th>
<th>Devolución</th>
</thead>
 
<?php 
$base = new Database();
$con = $base->connect();

$book_inicio2 = date('Y-m-d H:i:s');
$book_fin2 = date('Y-m-d H:i:s', strtotime('+1 day'));

$book_sql2 = "SELECT c.id, c.name, c.phone, c.address, c.latitud, c.longitud,
                    b.end_at, b.place_end,
                    car.name AS car_name, car.year AS car_year,
                    brand.name AS brand_name
             FROM person c
             INNER JOIN booking b ON b.person_id = c.id
             INNER JOIN cars car ON b.car_id = car.id
             INNER JOIN brand ON car.brand_id = brand.id
             WHERE b.status<>3 and b.end_at BETWEEN '$book_inicio2' AND '$book_fin2'
             ORDER BY b.end_at ASC";



$book_query2 = $con->query($book_sql2);

while($book2 = $book_query2->fetch_assoc()): ?>
<tr>
  <td class="mailbox-name"><?php echo $book2["name"]; ?></td>
  <td class="mailbox-name">
    <?php echo strtoupper($book2["brand_name"] . ' ' . $book2["car_name"]) . ' (' . $book2["car_year"] . ')'; ?>
  </td>
  <td class="mailbox-date">
    <?php echo date('d/m/Y h:i A', strtotime($book2["end_at"])); ?>
  </td>
</tr>
<tr>
  <td colspan="3">
   <?php echo $book2["place_end"]; ?>
  </td>
</tr>

  <?php endwhile; ?>


</table>
</div>
</div>
</div>

                <!-- /.table -->
             

          </div>
          <!-- /. box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      </div><!-- /.row -->
</div><!-- /.container-fluid -->
    </section>
   


<?php endif;?>