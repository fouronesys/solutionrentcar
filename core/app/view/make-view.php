<?php 
/////////////////////////////////////////////////////////////// PAYMENT //////////////////////////////////////////////////
if(isset($_GET["opt"]) && $_GET["opt"]=="payment"):

$base = new Database();
$con = $base->connect();

$selstock = StockData::getPrincipal()->id;

// ✅ SOLO FACTURA: id = booking_id
$booking_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if($booking_id <= 0){
  echo "<div class='card'><div class='card-body'><h4>Error: factura inválida.</h4></div></div>";
  return;
}

// Buscar booking (factura) y validar que sea de esta sucursal y tipo crédito (type_id=1)
$sqlB = "SELECT * FROM booking 
         WHERE id = $booking_id 
           AND stock_id = $selstock
           AND type_id = 1
         LIMIT 1";
$qB = $con->query($sqlB);

if(!$qB || $qB->num_rows <= 0){
  echo "<div class='card'><div class='card-body'><h4>Error: factura no encontrada.</h4></div></div>";
  return;
}

$sell = $qB->fetch_assoc();
$person_id = intval($sell["person_id"]);
$client = PersonData::getById($person_id);

// ✅ Monto (igual al listado): SUM(val) con is_stock=0
$sql_pago = "SELECT IFNULL(SUM(val),0) AS total 
             FROM payment 
             WHERE booking_id = $booking_id 
               AND stock_id = $selstock 
               AND is_stock = 0";
$res_pago = $con->query($sql_pago);
$row_pago = $res_pago ? $res_pago->fetch_assoc() : ["total"=>0];
$tx = floatval($row_pago["total"]);

// Vehículo
$car_id = intval($sell["car_id"]);
$car = CarsData::getById($car_id);
$brand = $car ? $car->getBrand() : null;

$vehiculo_text = ($car ? ($brand ? $brand->name." " : "").$car->name." [".$car->token."]" : "Vehículo no disponible");

// Total deuda mostrada (en tu lógica actual)
$total = $tx;
?>

<section class="content">
<div class="row">
  <div class="col-12">
    <div class="content-header">
      <div class="container-fluid">

        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><i class="fa fa-asterisk"></i> Realizar Pago</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item"><i class='fa fa-briefcase'></i> Finanzas</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Crédito</li>
              <li class="breadcrumb-item"><i class="fa fa-asterisk"></i> Abono</li>
            </ol>
          </div>
        </div>

        <div class="callout callout-warning" style="background-color:#222;">
          <h5><i class="fas fa-info"></i> Nota:</h5>
          <p>Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo.</p>
        </div>

        <p>NOMBRE: <?php echo strtoupper($client->name . " " . $client->lastname); ?></p>
        <h3>Deuda total: <?php echo number_format($total, 2, ".", ","); ?></h3>

        <?php if($tx > 0): ?>
          <div class="card" style="background-color:#222;">
            <div class="card-body">

              <h4>Vehículo: <?php echo $vehiculo_text; ?></h4>

              <form class="form-horizontal" method="post" enctype="multipart/form-data"
                    id="addpayment<?php echo $booking_id; ?>"
                    action="./?action=add&opt=payment" role="form">

                <input type="hidden" name="sell_id" value="<?php echo $booking_id; ?>">
                <input type="hidden" name="client_id" value="<?php echo $client->id; ?>">

                <div class="row">
                  <div class="col-md-6 col-12">   
                    <label>Factura</label>
                    <div class="input-group">
                      <span class="input-group-text" style="background-color:orange;"><i class="fa fa-clone"></i></span>
                      <input type="text" class="form-control" value="#<?php echo $booking_id; ?>" readonly>
                    </div>
                  </div>

                  <div class="col-md-6 col-12">
                    <label>Forma de Pago</label>
                    <div class="input-group">
                      <span class="input-group-text" style="background-color:orange;"><i class="fa fa-list-ol"></i></span>
                      <select name="f_id" class="form-control" required>
                        <?php foreach (FData::getAll() as $f): ?>
                          <option value="<?php echo $f->id; ?>"><?php echo $f->name; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row mt-2">
                  <div class="col-md-6 col-12">
                    <label>Total adeudado</label>
                    <div class="input-group">
                      <span class="input-group-text" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
                      <input type="text" class="form-control" value="$ <?php echo round($tx); ?>" readonly>
                      <input type="hidden" id="total<?php echo $booking_id; ?>" value="<?php echo round($tx); ?>">
                    </div>
                  </div>

                  <div class="col-md-6 col-12">
                    <label>Pago a Realizar <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
                      <input type="text" name="val" required id="val<?php echo $booking_id; ?>" class="form-control" placeholder="Pago a Realizar">
                    </div>
                  </div>
                </div>

                <div class="row my-2">
                  <div class="col-md-6 col-6">
                    <a href="./?view=credit&opt=pay" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
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
            $("#addpayment<?php echo $booking_id; ?>").submit(function(e){
              let total = parseFloat($("#total<?php echo $booking_id; ?>").val());
              let val = parseFloat($("#val<?php echo $booking_id; ?>").val());

              if (val && val > 0) {
                if (val <= total) {
                  let go = confirm("¿Está seguro que desea continuar?");
                  if (!go) { e.preventDefault(); }
                } else {
                  alert("No es posible ingresar un pago mayor a la deuda total.");
                  e.preventDefault();
                }
              } else {
                alert("Debes ingresar un valor mayor que 0.");
                e.preventDefault();
              }
            });
          });
          </script>

        <?php else: ?>
          <div class="card" style="background-color:#222;">
            <div class="card-body">
              <h4 style="color:#f39c12;">Esta factura no tiene deuda pendiente.</h4>
            </div>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>
</section>

<?php 
/////////////////////////////////////////////////////////////// PAYMENT //////////////////////////////////////////////////
elseif(isset($_GET["opt"]) && $_GET["opt"]=="stock"):
$base = new Database();
$con = $base->connect();

$selstock = StockData::getPrincipal()->id;
    
$client = StockData::getById($_GET["person_id"]);
// Obtener todas las reservas activas de tipo crédito (type_id = 2, status = 1) para ese cliente
$sql = "SELECT * FROM booking WHERE type_id = 1  AND stock_id = $selstock AND id =".$_GET["id"];
$query = $con->query($sql);


$credit_array = array();
$total = 0;

while ($sell = $query->fetch_assoc()) {
    $sell_id = $sell["id"];

    // Obtener monto total abonado a ese booking
    $sql_pago = "SELECT SUM(val) AS total FROM payment WHERE booking_id = $sell_id AND stock_id = $selstock";
    $res_pago = $con->query($sql_pago);
    $row_pago = $res_pago->fetch_assoc();
    $tx = floatval($row_pago["total"]);

    // Obtener información del vehículo
    $car_id = $sell["car_id"];
    $car = CarsData::getById($car_id);
    $brand = $car ? $car->getBrand() : null;

    if ($tx >= 0 && $car) {
        $credit_array[] = array(
            "sell_id" => $sell_id,
            "total" => $tx,
            "car_id" => $car->name,
            "brand_id" => $brand ? $brand->name : '',
            "token" => $car->token
        );
        $total += $tx;
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
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
                            <li class="breadcrumb-item"><i class='fa fa-briefcase'></i> Finanzas</li>
                        </ol>
                    </div>
                </div>

                <div class="callout callout-warning" style="background-color:#222;">
                    <h5><i class="fas fa-info"></i> Nota:</h5>
                    <p>Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo.</p>
                </div>

                <p>NOMBRE: <?php echo strtoupper($client->name . " " . $client->lastname); ?></p>
                <h3>Deuda total: <?php echo number_format($total, 2, ".", ","); ?></h3>

                <?php if (count($credit_array) > 0): ?>
                    <?php foreach ($credit_array as $ca): ?>
                        <div class="card" style="background-color:#222;">
                            <div class="card-body">
                                <h4>Vehículo: <?php echo $ca['brand_id'] . " " . $ca['car_id'] . " [" . $ca['token'] . "]"; ?></h4>
                                <form class="form-horizontal" method="post" enctype="multipart/form-data" id="addpayment<?php echo $ca['sell_id']; ?>" action="./?action=add&opt=paymentstock" role="form">
                                    <input type="hidden" name="sell_id" value="<?php echo $ca['sell_id']; ?>">
                                    <input type="hidden" name="client_id" value="<?php echo $client->id; ?>">

                                    <div class="row">
                                        <div class="col-md-6 col-12">   
                                        <label>Factura</label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background-color:orange;"><i class="fa fa-clone"></i></span>
                                                <input type="text" class="form-control" placeholder="Cliente" value="#<?php echo $ca['sell_id']; ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <label>Forma de Pago</label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background-color:orange;"><i class="fa fa-list-ol"></i></span>
                                                <select name="f_id" class="form-control">
                                                    <?php foreach (FData::getAll() as $f): ?>
                                                        <option value="<?php echo $f->id; ?>"><?php echo $f->name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-6 col-12">
                                            <label>Total adeudado</label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
                                                <input type="text" class="form-control" value="$ <?php echo round($ca['total']); ?>" readonly>
                                                <input type="hidden" id="total<?php echo $ca['sell_id']; ?>" value="<?php echo round($ca['total']); ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <label>Pago a Realizar <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
                                                <input type="text" name="val" required id="val<?php echo $ca['sell_id']; ?>" class="form-control" placeholder="Pago a Realizar">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-12 col-12">
                                            <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <script>
                        $(document).ready(function(){
                            $("#addpayment<?php echo $ca['sell_id']; ?>").submit(function(e){
                                let total = parseFloat($("#total<?php echo $ca['sell_id']; ?>").val());
                                let val = parseFloat($("#val<?php echo $ca['sell_id']; ?>").val());

                                if (val && val > 0) {
                                    if (val <= total) {
                                        let go = confirm("¿Está seguro que desea continuar?");
                                        if (!go) { e.preventDefault(); }
                                    } else {
                                        alert("No es posible ingresar un pago mayor a la deuda total.");
                                        e.preventDefault();
                                    }
                                } else {
                                    alert("Debes ingresar un valor mayor que 0.");
                                    e.preventDefault();
                                }
                            });
                        });
                        </script>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php
/////////////////////////////////////////////////////////////// PAYMENT //////////////////////////////////////////////////
elseif(isset($_GET["opt"]) && $_GET["opt"]=="paymentfree"):
$base = new Database();
$con = $base->connect();

$selstock = StockData::getPrincipal()->id;
$client = PersonData::getById($_GET["id"]);

// Obtener todas las reservas activas de tipo crédito (type_id = 2, status = 1) para ese cliente
$sql = "SELECT * FROM booking WHERE type_id = 2 AND status = 1 AND stock_id = $selstock AND person_id = $client->id";
$query = $con->query($sql);

$credit_array = array();
$total = 0;

while ($sell = $query->fetch_assoc()) {
    $sell_id = $sell["id"];

    // Obtener monto total abonado a ese booking
    $sql_pago = "SELECT SUM(val) AS total FROM payment WHERE booking_id = $sell_id AND stock_id = $selstock";
    $res_pago = $con->query($sql_pago);
    $row_pago = $res_pago->fetch_assoc();
    $tx = floatval($row_pago["total"]);

    // Obtener información del vehículo
    $car_id = $sell["car_id"];
    $car = CarsData::getById($car_id);
    $brand = $car ? $car->getBrand() : null;

    if ($tx >= 0 && $car) {
        $credit_array[] = array(
            "sell_id" => $sell_id,
            "total" => $tx,
            "car_id" => $car->name,
            "brand_id" => $brand ? $brand->name : '',
            "token" => $car->token,
            "txtal"=>$_GET["pay"]
        );
        $total += $_GET["pay"];
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
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
                            <li class="breadcrumb-item"><i class='fa fa-briefcase'></i> Finanzas</li>
                            <li class="breadcrumb-item"><i class='far fa-circle'></i> Crédito</li>
                            <li class="breadcrumb-item"><i class="fa fa-asterisk"></i> Abono</li>
                        </ol>
                    </div>
                </div>

                <div class="callout callout-warning" style="background-color:#222;">
                    <h5><i class="fas fa-info"></i> Nota:</h5>
                    <p>Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo.</p>
                </div>

                <p>NOMBRE: <?php echo strtoupper($client->name . " " . $client->lastname); ?></p>
                <h3>Deuda total: <?php echo number_format($total, 2, ".", ","); ?></h3>

                <?php if (count($credit_array) > 0): ?>
                    <?php foreach ($credit_array as $ca): ?>
                        <div class="card" style="background-color:#222;">
                            <div class="card-body">
                                <h4>Vehículo: <?php echo $ca['brand_id'] . " " . $ca['car_id'] . " [" . $ca['token'] . "]"; ?></h4>
                                <form class="form-horizontal" method="post" enctype="multipart/form-data" id="addpayment<?php echo $ca['sell_id']; ?>" action="./?action=add&opt=renewpayment" role="form">
                                    <input style="background-color:#333;" type="hidden" name="sell_id" value="<?php echo $ca['sell_id'];?>">
                                    <input style="background-color:#333;" type="hidden" name="client_id" class="form-control"  value="<?php echo $client->id; ?>">
                                    <input style="background-color:#333;" type="hidden" name="total" class="form-control"  value="<?php echo $ca['total']; ?>">
                                    <input style="background-color:#333;" type="hidden" name="txtal" id="total<?php echo $ca['sell_id']; ?>" class="form-control"  value="<?php echo $ca['txtal']; ?>">

                                    <div class="row">
                                        <div class="col-md-6 col-12">   
                                        <label>Factura</label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background-color:orange;"><i class="fa fa-clone"></i></span>
                                                <input type="text" class="form-control" placeholder="Cliente" value="#<?php echo $ca['sell_id']; ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <label>Forma de Pago</label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background-color:orange;"><i class="fa fa-list-ol"></i></span>
                                                <select name="f_id" class="form-control">
                                                    <?php foreach (FData::getAll() as $f): ?>
                                                        <option value="<?php echo $f->id; ?>"><?php echo $f->name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-6 col-12">
                                            <label>Total adeudado</label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
                                                 <input hidden id="txtal<?php echo $ca['sell_id']; ?>"  value="<?php echo $ca['txtal'] ?>">
                                                <input type="text" class="form-control" value="$ <?php echo number_format($_GET["pay"], 2, ".", ","); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <label>Pago a Realizar <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
                                                <input type="text" name="val" required id="val<?php echo $ca['sell_id']; ?>" class="form-control" placeholder="Pago a Realizar">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6 col-6">
                                            <a href="./?view=credit&opt=payfree" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
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
    </div>
</section>



<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="history"):

$client = PersonData::getById($_GET["id"]);
$sells = BookingData::getCreditByClientId($client->id,StockData::getPrincipal()->id);
$total=0;
$credit_array = array();
foreach ($sells as $sell) {
//  print_r($sell);
$tx = PaymentData::sumByClientId($client->id)->total;


if($tx>=0){
$credit_array[] = array("sell_id"=>$sell->id,"person_id"=>$sell->person_id,"car_id"=>$sell->car_id,"status"=>$sell->status);
$total=$tx;
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
           <h1 class="m-0"><i class="fa fa-book"></i> Historial de Pago</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item"><i class='fa fa-briefcase'></i> Finanzas</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Credito</li>

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

 
 <p>NOMBRE: <?php echo strtoupper($client->name." ".$client->lastname);?></p>
 
  <?php $i= count($credit_array);  if(count($credit_array)>0):?>
    <?php foreach($credit_array as $ca): $users = PaymentData:: getAllBySQL("where person_id=".$ca['person_id']." and booking_id=".$ca['sell_id']);  $cars = CarsData::getById($ca['car_id']);?>
   
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
     <h3>Vehiculo <span><?php if($ca['status']==1):$kxm = "Rentado"; elseif($ca['status']==0):$kxm = "Reservado"; elseif($ca['status']==3):$kxm = "Completado"; endif; echo $kxm; ?></span>: <?php echo $cars->getBrand()->name." ".$cars->name."[".$cars->token."] ";?> </h3>
<table class="table table-bordered" id="example<?php echo $i;?>">
      <thead>
      <th>Tipo</th>
      <th>Valor</th>
      <th>Fecha</th>
      <th>Accion</th>
      </thead>
      <tfoot>
    
       <th>Tipo</th>
      <th>Valor</th>
      <th>Fecha</th>
      <th>Accion</th>
      </tfoot>
      <?php foreach($users as $user):?>
        <tr>
       
        <td><?php echo $user->getPaymentType()->name; ?></td>

        
        <td><?php echo number_format(abs($user->val),2,".",",");?></td>
        <td><?php echo date("d-m-Y h:i:s",strtotime($user->created_at. "- 4 hours")); ?></td>
        <?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);?>
        <td>
<?php foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
        <a href="./?action=delete&opt=payment&id=<?php echo $user->id;?>&person_id=<?php echo $user->person_id;?>" class="btn btn-danger btn-block btn-sm"><i class='fa fa-trash'></i> Eliminar</a>
        
     <?php endif; endforeach; ?> 
       </td>
        </tr>
        <?php
        $total -=$user->val;

      endforeach; ?>
      </table>
      </div>
    </div>
  </div>
 <script>  
$("#example<?php echo $i;?>").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
<?php $i++; endforeach; ?>
<?php endif; ?>

	</div>
</div>

</section>

<?php endif; ?>