<?php if(isset($_GET["opt"]) && $_GET["opt"]=="pay"): 
    
if(StockData::getPrincipal()->update=="1"): 
 
$base = new Database();
$con = $base->connect();

$symbol   = StockData::getPrincipal()->currency;
$selstock = StockData::getPrincipal()->id;
$TicketMm = StockData::getPrincipal()->ticket_mm;

$deudas = []; // aquí agruparemos por suplidor

foreach (PersonData::getAll() as $suplidor) {

    $sql = "SELECT b.id AS booking_id, b.created_at, SUM(p.val) AS total
            FROM booking b
            LEFT JOIN payment p ON p.booking_id = b.id AND p.stock_id = $selstock
            WHERE b.type_id = 1 
              AND b.stock_id = $selstock
              AND b.person_id = {$suplidor->id}
              AND p.is_stock = 0
            GROUP BY b.id
            ORDER BY b.created_at DESC";
    $query = $con->query($sql);

    while ($row = $query->fetch_assoc()) {
        if ($row["total"] > 0) {
            $deudas[$suplidor->name][] = [
                "sell_id"   => $row["booking_id"],
                "total"     => $row["total"],
                "person_id" => $suplidor->id,
                "created_at"=> $row["created_at"]
            ];
        }
    }
}
?>

<section class="content">
<div class="row">
  <div class="col-12">

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
       <h1 class="m-0"><i class="fa fa-list"></i> Deudas y Cobros Pendientes</h1>
          </div>
          
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active">
                <i class='fa fa-history'></i> 
                <span id="reloj"></span>
              </li>
            </ol>
          </div>
      
<script>
// Función para actualizar la hora en tiempo real
function actualizarReloj() {
  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  document.getElementById("reloj").textContent = `${horas}:${minutos}:${segundos}`;
}
setInterval(actualizarReloj, 1000);
actualizarReloj();
</script>
</div>

<?php if (!empty($deudas)): $i=1; ?>
  <?php foreach ($deudas as $cliente => $bookings): $i++;?>
    <div class="card" style="background-color:#222; margin-bottom:20px;">
      <div class="card-body">
        <h5>
          <b><?php echo strtoupper($cliente); ?></b>
          — <span style="color:#f39c12;">
            <?php 
              $totalCliente = array_sum(array_column($bookings, "total"));
              echo $symbol." ".number_format($totalCliente,2); 
            ?>
          </span>
        </h5>
      <div class="table-responsive">
  <table class="table table-bordered" id="example<?php echo $i;?>">
    <thead>
      <tr>
        <th>Acción</th>
        <th>Factura</th>
        <th>Pendiente</th>
        <th>Fecha</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($bookings as $b): ?>
        <tr>
          <td class="text-center">
            <div class="btn-group btn-block">
              <a href="<?php echo $TicketMm;?>/ticket-receipt.php?id=<?php echo $b["sell_id"];?>&person_id=<?php echo $b["person_id"];?>" 
                 class="btn btn-warning btn-sm">
                 <i class="fa fa-file-invoice"></i>
              </a>
              <a href="./?view=make&opt=payment&id=<?php echo $b["sell_id"]; ?>&person_id=<?php echo $b["person_id"]; ?>" 
                 class="btn btn-success btn-sm">
                 <i class="fa fa-asterisk"></i>
              </a>
            </div>
          </td>
          <td>#<?php echo $b["sell_id"]; ?></td>
          <td>
            <b style="color:#f39c12;">
              <?php echo $symbol." ".number_format($b["total"],2); ?>
            </b>
          </td>
          <td><?php echo date("d-m-Y", strtotime($b["created_at"])); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <div class="card">
    <div class="card-header">
      <h2>No hay deudas registradas</h2>
      <p>No se ha realizado ninguna operación.</p>
    </div>
  </div>
<?php endif; ?>
</div>
</div>
</section>


<?php else: ?>
<section class="content">
<div class="row">
  <div class="col-12">

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-book"></i> Cobros Pendiente</h1>
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

 
<?php
$base = new Database();
$con = $base->connect();

$selstock = StockData::getPrincipal()->id;

// Obtener todas las reservas activas de tipo crédito (type_id = 2, status = 1) en la sucursal actual
$sql = "SELECT SQL_BIG_RESULT * FROM booking WHERE type_id = 1  AND stock_id = $selstock";
$query = $con->query($sql);

$clientes = [];

if ($query && $query->num_rows > 0) {
    while ($row = $query->fetch_assoc()) {
        $cliente_id = $row['person_id'];
        $booking_id = $row['id'];

        // Sumar pagos de ese booking
        $sql_pago = "SELECT SUM(val) AS total FROM payment WHERE booking_id = $booking_id AND stock_id = $selstock AND is_stock = 0";
        $q_pago = $con->query($sql_pago);
        $pago = $q_pago->fetch_assoc();
        $monto = floatval($pago['total']);

        if (!isset($clientes[$cliente_id])) {
            $clientes[$cliente_id] = [
                "total" => 0,
                "bookings" => [],
            ];
        }

        $clientes[$cliente_id]["total"] += $monto;
        $clientes[$cliente_id]["bookings"][] = $booking_id;
    }
}

// Obtener información del cliente solo si tiene deuda
$mostrar = [];

foreach ($clientes as $cliente_id => $datos) {
    if ($datos["total"] > 0) {
        $user = PersonData::getById($cliente_id);
        $mostrar[] = [
            "cliente" => $user,
            "saldo" => $datos["total"]
        ];
    }
}

if (count($mostrar) > 0):
?>
<div class="card" style="background-color:#222;">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="example1">
        <thead>
          <th>Acción</th>
          <th>Nombre completo</th>
          <th>Dirección</th>
          <th>Teléfono</th>
          <th>Saldo Pendiente</th>
        </thead>
        <tfoot>
          <th>Acción</th>
          <th>Nombre completo</th>
          <th>Dirección</th>
          <th>Teléfono</th>
          <th>Saldo Pendiente</th>
        </tfoot>
        <?php foreach ($mostrar as $item): 
            $user = $item["cliente"];
            $total = $item["saldo"];
        ?>
        <tr>
          <td class="text-right py-0 align-middle">
            <div class="btn-group btn-group-sm btn-block">
              <a href="./?view=make&opt=payment&id=<?php echo $user->id; ?>" class="btn btn-success"><i class="fa fa-asterisk"></i></a>
              <a href="./?view=make&opt=history&id=<?php echo $user->id; ?>" class="btn btn-info"><i class="fas fa-history"></i></a>
            </div>
          </td>
          <td><?php echo utf8_decode($user->name . " " . $user->lastname); ?></td>
          <td><?php echo $user->address1; ?></td>
          <td><?php echo $user->phone1; ?></td>
          <td><a style="color: white;" data-toggle="modal" data-target="#myModal<?php echo $user->id; ?>"><?php echo number_format($total); ?></a></td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</div>
<?php else: ?>
<div class='card'>
  <div class='card-header'>
    <h2>No hay clientes con créditos</h2>
    <p>No se ha realizado ninguna operación.</p>
  </div>
</div>
<?php endif; ?>



  </div>
</div>
</section>
<script type="text/javascript">
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-6:eq(0)');
</script>

<?php endif;  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
elseif(isset($_GET["opt"]) && $_GET["opt"]=="client_modal"): ?>

<section class="content">
<div class="row">
  <div class="col-12">
  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-book"></i> Abono A Cobro Pendiente</h1>
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
<?php
$user = PersonData::getById($_GET["id"]);
$TicketMm = StockData::getPrincipal()->ticket_mm;

print "<br><p class='alert alert-success'>Venta procesada exitosamente. <a  href='".$TicketMm."/ticket-payment.php?id=".$user->id."' id='printx' class='btn-xs btn btn-info'><i class='fa fa-ticket'></i> Ver Ticket</a> </p>";

echo '<div class="row"><div class="col-12 col-offset-3">
<div class="embed-responsive embed-responsive-16by9">
  <iframe id="ticket1" name="ticket1" class="embed-responsive-item" src='.$TicketMm.'/ticket-payment.php?id="'.$user->id.'" allowfullscreen></iframe>
</div>
</div></div>
';
echo "<script>window.frames['ticket1'].focus();
window.frames['ticket1'].print();</script>";
?>
  </div>
</div>

</section>
<?php 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
elseif(isset($_GET["opt"]) && $_GET["opt"]=="stock"):  

if(StockData::getPrincipal()->update=="1"): 
   
  
$base = new Database();
$con = $base->connect();

$symbol   = StockData::getPrincipal()->currency;
$selstock = StockData::getPrincipal()->id;
$TicketMm = StockData::getPrincipal()->ticket_mm;

$deudas = []; // aquí agruparemos por suplidor

foreach (StockData::getAll() as $suplidor) {

    $sql = "SELECT b.id AS booking_id, b.created_at, SUM(p.val) AS total
            FROM booking b
            LEFT JOIN payment p ON p.booking_id = b.id AND p.stock_id = $selstock
            WHERE b.type_id = 1 
              AND b.stock_id = $selstock
              AND p.person_id = {$suplidor->id}
              AND p.is_stock = 1
            GROUP BY b.id
            ORDER BY b.created_at DESC";
    $query = $con->query($sql);

    while ($row = $query->fetch_assoc()) {
        if ($row["total"] > 0) {
            $deudas[$suplidor->name][] = [
                "sell_id"   => $row["booking_id"],
                "total"     => $row["total"],
                "person_id" => $suplidor->id,
                "created_at"=> $row["created_at"]
            ];
        }
    }
}
?>

<section class="content">
<div class="row">
  <div class="col-12">

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
       <h1 class="m-0"><i class="fa fa-list"></i> Deudas y Pagos Pendientes</h1>
          </div>
          
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active">
                <i class='fa fa-history'></i> 
                <span id="reloj"></span>
              </li>
            </ol>
          </div>
      
<script>
// Función para actualizar la hora en tiempo real
function actualizarReloj() {
  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  document.getElementById("reloj").textContent = `${horas}:${minutos}:${segundos}`;
}
setInterval(actualizarReloj, 1000);
actualizarReloj();
</script>
</div>

<?php if (!empty($deudas)): $i=1; ?>
  <?php foreach ($deudas as $cliente => $bookings): $i++;?>
    <div class="card" style="background-color:#222; margin-bottom:20px;">
      <div class="card-body">
        <h5>
          <b><?php echo strtoupper($cliente); ?></b>
          — <span style="color:#f39c12;">
            <?php 
              $totalCliente = array_sum(array_column($bookings, "total"));
              echo $symbol." ".number_format($totalCliente,2); 
            ?>
          </span>
        </h5>
      <div class="table-responsive">
  <table class="table table-bordered" id="example<?php echo $i;?>">
    <thead>
      <tr>
        <th>Acción</th>
        <th>Factura</th>
        <th>Pendiente</th>
        <th>Fecha</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($bookings as $b): ?>
        <tr>
          <td class="text-center">
            <div class="btn-group btn-block">
              <a href="<?php echo $TicketMm;?>/ticket-extreceipt.php?id=<?php echo $b["sell_id"];?>&person_id=<?php echo $b["person_id"];?>" 
                 class="btn btn-warning btn-sm">
                 <i class="fa fa-file-invoice"></i>
              </a>
              <a href="./?view=make&opt=stock&id=<?php echo $b["sell_id"]; ?>&person_id=<?php echo $b["person_id"]; ?>" 
                 class="btn btn-success btn-sm">
                 <i class="fa fa-asterisk"></i>
              </a>
            </div>
          </td>
          <td>#<?php echo $b["sell_id"]; ?></td>
          <td>
            <b style="color:#f39c12;">
              <?php echo $symbol." ".number_format($b["total"],2); ?>
            </b>
          </td>
          <td><?php echo date("d-m-Y", strtotime($b["created_at"])); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <div class="card">
    <div class="card-header">
      <h2>No hay deudas registradas</h2>
      <p>No se ha realizado ninguna operación.</p>
    </div>
  </div>
<?php endif; ?>
</div>
</div>
</section>


<?php else: ?>
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-book'></i> Deuda de Rent Car</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-briefcase'></i> Finanzas</li>
           
            </ol>
          </div><!-- /.col -->
    
    </div>
<div class="callout callout-warning" style="background-color:#222;">
              <h5><i class="fas fa-info"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Nota"; break;
 case 'EN': echo "Note"; break;
}
?>:</h5>
             <?php 
switch (Core::$user->language){
 case 'ES': echo " Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo."; break;
 case 'EN': echo "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; break;
}
?>
            </div>
            
<?php 
if(isset($_GET["stock"])){ $selstock=$_GET["stock"]; }
else{ $selstock = StockData::getPrincipal()->id; }?>

<?php  $sells = PersonData::getAllBySQL("where name='".StockData::getPrincipal()->name."' and  stock_id=$selstock");
foreach($sells as $sell){
    $users = BookingData::getAllBySQL("where payment=0 and person_id=".$sell->id);
}

$TicketMm = StockData::getPrincipal()->ticket_mm;    
    if(count($sells)>0):?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      <th>Accion</th>
      <th>Vehiculo</th>
      <th>Cliente</th>
      <th>Tarifa</th>
      <th>Dias(s)</th>
      <th>Pendiente</th>
      <th>Ubicacion</th>
      <th>RentCar</th>
      <th>Entrega</th>
      <th>Recibir</th>
    </thead>

    <tfoot>
      <tr>
      <th>Accion</th>
      <th>Vehiculo</th>
      <th>Cliente</th>
      <th>Tarifa</th>
      <th>Dias(s)</th>
      <th>Pendiente</th>
      <th>Ubicacion</th>
      <th>RentCar</th>
      <th>Entrega</th>
      <th>Recibir</th>
      </tr>
    </tfoot>

      <?php foreach($users as $user): ?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">

 <a href="./?view=contract&opt=paymentstock&id=<?php echo $user->person_id;?>&stock=<?php echo $user->stock_id;?>" class="btn btn-success"><i class="fas fa-money-check-alt"></i></a>

</div>
                     
                      </div>
        </td>
        <td><?php if ($user->car2_id==0): $brand = BrandData::getById($user->getCars()->brand_id); echo $brand->name." ".$user->getCars()->name." ".$user->getCars()->year." [".$user->getCars()->token."]"; else: $brand = BrandData::getById($user->getCars2()->brand_id); echo $brand->name." ".$user->getCars2()->name." ".$user->getCars2()->year; endif ?></td>
        <td><?php echo $user->getPerson()->name; ?></td>
        <td><?php echo number_format($user->price,2,".",","); ?></td>
        <td><?php echo $user->day; ?></td>
        <td><?php echo number_format($user->total,2,".",","); ?></td>
        <td><?php echo $user->getLocation()->name; ?></td>
        <td><?php echo $user->getStock()->name; ?></td>
        <td><?php echo $user->start_at; ?></td>
        <td><?php echo $user->end_at; ?></td>
        
        
    </tr>
    
    <?php endforeach; ?>
      </table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->
      <?php else:?>
     
         <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Deuda</h2>
    <p>No se ha realizado ninguna operacion.</p>
    </div>
</div>
  
   <?php endif;?>



  </div>
</div>
    </div><!-- /.row -->
      </div><!-- /.container-fluid -->
<script type="text/javascript">
    $("#example1").DataTable();
</script>
</section>

<?php endif; elseif(isset($_GET["opt"]) && $_GET["opt"]=="payfree"):?>
<section class="content">
<div class="row">
  <div class="col-12">

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-book"></i> Cobros Pendiente</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-briefcase'></i> Finanzas</a></li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Cobros Pendiente</a></li>

            </ol>
          </div><!-- /.col -->

    </div>

 
<?php
$base = new Database();
$con = $base->connect();

$selstock = StockData::getPrincipal()->id;

// Obtener todas las reservas activas de tipo crédito (type_id = 2, status = 1) en la sucursal actual
$sql = "SELECT SQL_BIG_RESULT * FROM booking WHERE type_id = 2 AND stock_id = $selstock";
$query = $con->query($sql);

$clientes = [];

if ($query && $query->num_rows > 0) {
    while ($row = $query->fetch_assoc()) {
        $cliente_id = $row['person_id'];
        $booking_id = $row['id'];

        // Sumar pagos de ese booking
        $sql_pago = "SELECT SUM(val) AS total FROM payment WHERE booking_id = $booking_id AND stock_id = $selstock AND is_stock = 0";
        $q_pago = $con->query($sql_pago);
        $pago = $q_pago->fetch_assoc();
        $monto = floatval($pago['total']);

        if (!isset($clientes[$cliente_id])) {
            $clientes[$cliente_id] = [
                "total" => 0,
                "bookings" => [],
            ];
        }

        $clientes[$cliente_id]["total"] += $monto;
        $clientes[$cliente_id]["bookings"][] = $booking_id;
    }
}

// Obtener información del cliente solo si tiene deuda
$mostrar = [];

foreach ($clientes as $cliente_id => $datos) {
    if ($datos["total"] > 0) {
        $user = PersonData::getById($cliente_id);
        $mostrar[] = [
            "cliente" => $user,
            "saldo" => $datos["total"]
        ];
    }
}

if (count($mostrar) > 0):
?>
<div class="card" style="background-color:#222;">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="example1">
        <thead>
          <th>Acción</th>
          <th>Nombre completo</th>
          <th>Dirección</th>
          <th>Teléfono</th>
          <th>Saldo Pendiente</th>
        </thead>
        <tfoot>
          <th>Acción</th>
          <th>Nombre completo</th>
          <th>Dirección</th>
          <th>Teléfono</th>
          <th>Saldo Pendiente</th>
        </tfoot>
        <?php foreach ($mostrar as $item): 
            $user = $item["cliente"];
            $total = $item["saldo"];
            
            $fechaActual = date("Y-m-d"); 
            $fechaRegistro = date("Y-m-d", strtotime($item['start_at'])); 
            $segundosFechaActual = strtotime($fechaActual);
            $segundosFechaRegistro = strtotime($fechaRegistro);
            $segundosTranscurridos = $segundosFechaActual - $segundosFechaRegistro;
            $diasTranscurridos = $segundosTranscurridos / 86400;
            
            if(($total*$diasTranscurridos)>0):?>
        <tr>
          <td class="text-right py-0 align-middle">
            <div class="btn-group btn-group-sm btn-block">
              <a href="./?view=make&opt=paymentfree&id=<?php echo $user->id; ?>&pay=<?php echo ($total*$diasTranscurridos);?>" class="btn btn-success"><i class="fa fa-asterisk"></i></a>
              <a href="./?view=make&opt=historyfree&id=<?php echo $user->id; ?>" class="btn btn-info"><i class="fas fa-history"></i></a>
            </div>
          </td>
          <td><?php echo utf8_decode($user->name . " " . $user->lastname); ?></td>
          <td><?php echo $user->address1; ?></td>
          <td><?php echo $user->phone1; ?></td>
          <td><a style="color: white;" data-toggle="modal" data-target="#myModal<?php echo $user->id; ?>"><?php echo number_format($total*$diasTranscurridos); ?></a></td>
        </tr>
        <?php endif; endforeach; ?>
      </table>
    </div>
  </div>
</div>
<?php else: ?>
<div class='card'>
  <div class='card-header'>
    <h2>No hay clientes con créditos</h2>
    <p>No se ha realizado ninguna operación.</p>
  </div>
</div>
<?php endif; ?>



  </div>
</div>
</section>
<script type="text/javascript">
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-6:eq(0)');
</script>


<?php ////////////////////////////////////////////////////////////////////////// CLIENT_MODAL ///////////////////////////////////////////////////////////////////////////////////////
 elseif(isset($_GET["opt"]) && $_GET["opt"]=="payment"):

$client = PersonData::getById($_GET["id"]);
$sells = BookingData::getCreditByClientId($client->id,StockData::getPrincipal()->id);
$total=0;
$credit_array = array();
foreach ($sells as $sell) {
//  print_r($sell);
$tx = PaymentData::sumBySellId2($sell->id,StockData::getPrincipal()->id)->total;
$cars= CarsData::getById($sell->car_id);
if($tx>=0){
$credit_array[] = array("brand"=>$cars->getBrand()->name,"model"=>$cars->name,"token"=>$sell->getCars()->token,"sell_id"=>$sell->id,"total"=>$tx,"txtal"=>$sell->total);
$total+=$tx;
}
}

?>

<section class="content">
<div class="row">
  <div class="col-12">
  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-asterisk"></i> Realizar Pago</h1>
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
<div class="callout callout-warning" style="background-color:#222;">
              <h5><i class="fas fa-info"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Nota"; break;
 case 'EN': echo "Note"; break;
}
?>:</h5>
             <?php 
switch (Core::$user->language){
 case 'ES': echo " Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo."; break;
 case 'EN': echo "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; break;
}
?>
            </div>
 
 <p>NOMBRE: <?php echo strtoupper($client->name." ".$client->lastname);?></p>
  <h3>Deuda total: <?php echo number_format($total,2,".",","); ?></h3>

  <?php if(count($credit_array)>0):?>
    <?php foreach($credit_array as $ca):?>
 <div class="card" style="background-color:#222;">
<div class="card-body">
    <form class="form-horizontal" method="post" enctype="multipart/form-data" id="addpayment<?php echo $ca['sell_id']; ?>" action="./?action=add&opt=contractpayment" role="form">
<input type="hidden" name="sell_id" value="<?php echo $ca['sell_id'];?>">
<input type="hidden" name="client_id" class="form-control"  value="<?php echo $client->id; ?>">

<div class="row">
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total de Renta: <?php echo $ca['brand']." ".$ca['model']." [".$ca['token']."]"; ?></label>
        <div class="input-group">
  <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
      <input type="text" autocomplete="off" id="txtal<?php echo $ca['sell_id']; ?>"  class="form-control"  value="<?php echo $ca['txtal'] ?>" readonly>
    </div>
  
    </div>

   <div class="col-md-6 col-12">
      <label for="inputEmail1" class="col-12 col-md-12 control-label">Foma de Pago</label>
           <div class="input-group">
  <span class="input-group-text"><i class="fa fa-list-ol"></i></span>
 <select name="f_id" class="form-control">
    <?php foreach(FData::getAll() as $f):?>
      <option value="<?php echo $f->id;?>"><?php echo $f->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
</div>
  </div>
  
<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Deuda</label>
             <div class="input-group">
  <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
      <input type="text" autocomplete="off" name="" id="" class="form-control" placeholder="Total Deuda" value="$ <?php echo round($ca['total']); ?>" readonly>

      <input type="hidden" name="total" id="total<?php echo $ca['sell_id']; ?>" class="form-control"  value="<?php echo round($ca['total']); ?>">

    </div>
</div>

 <div class="col-md-6 col-12">
     <label for="inputEmail1" class="col-12 col-md-12 control-label">Pago a Realizar <span class="text-danger">*</span></label>
             <div class="input-group">
  <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
      <input type="text" autocomplete="off" autofocus  name="val" required id="val<?php echo $ca['sell_id']; ?>" class="form-control" placeholder="Pago a Realizar">

    </div>
</div>
  </div>

<div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=contract&opt=running" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
</form>
</div>
</div>
<script>
  $(document).ready(function(){
    $("#addpayment<?php echo $ca['sell_id']; ?>").submit(function(e){
      total = $("#total<?php echo $ca['sell_id']; ?>").val();
      txtal = $("#txtal<?php echo $ca['sell_id']; ?>").val();
      val = $("#val<?php echo $ca['sell_id']; ?>").val();
      if( val!="" && val>0 ){
        console.log(total);
        if(parseFloat(val)<=parseFloat(txtal)){
          // procesamos
          go = confirm("Esta seguro que desea continuar?");
          if(!go){ e.preventDefault(); }
        }else{
        alert("No es posible ingresar un pago mayor a la deuda total.")
        e.preventDefault();          
        }

      }else{
        alert("Debes ingresar un valor mayor que 0.")
        e.preventDefault();
      }
    });
});

</script>
<?php endforeach; ?>
<?php endif; ?>

  </div>
</div>

</section>

<?php endif; ?>