
<?php if(isset($_GET["opt"]) && $_GET["opt"]=="boxhistory"):
/////////////////////////////////////////////////////////////////////////// BOXHISTORY ///////////////////////////////////////

$products = null;
$TicketMm = StockData::getPrincipal()->ticket_mm;

// print_r(Core::$user);
if(isset($_SESSION["user_id"])){
//print_r($_GET);
$sql = "select SQL_BIG_RESULT * from box";
$whereparams = array();

if(isset($_GET["start_at"]) && isset($_GET["finish_at"]) && $_GET["start_at"]!="" && $_GET["finish_at"]!=""){
  $whereparams[] = " ( date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]' ) ";

 $sql2 = $sql." where ".implode(" and ", $whereparams)." order by created_at desc";

$products = BoxData::getAllBySQL2($sql2);

}
}

if(count($products)>0){$total_total = 0;?>


 <div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Haga clic en el boton verde para el detalle de cada corte de caja en la parte izquierda del apartado para probarlo.
            </div>

     <div class="row my-2" >
                <div class="col-md-12">
                  <a href="<?php echo $TicketMm; ?>/ticket-bh.php?start_at=<?php echo $_GET["start_at"]; ?>&finish_at=<?php echo $_GET["finish_at"];?>" class="btn btn-default btn-block"><i class='fa fa-print'></i> Imprimir Resumen</a>
                </div>
              </div>

<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
  <thead>
    <th></th>
    <th>Total</th>
    <th>Usuario</th>
    <th>Sucursal</th>
    <th>Fecha</th>
  </thead>
  <?php foreach($products as $box):
$sells = SellData::getByBoxId($box->id);
$res = SellData::getResByBoxId($box->id);
$income = IncomeData::getByBoxId($box->id);
$spends = SpendData::getSpendsByBoxId($box->id);
$payment = PaymentData::getByBoxId($box->id);
  ?>

  <tr>
    <td style="width:30px;">
<a href="./?view=b&id=<?php echo $box->id; ?>" class="btn btn-success"><i class="fa fa-arrow-right"></i></a>      
    </td>
    <td>

<?php
$total=0;
foreach($sells as $sell){
$operations = OperationData::getAllProductsBySellId($sell->id);
    $total += $sell->total-$sell->discount;
    $hand += $sell->hand;
    $sell_payment += $sell->payment;
}
$total_income=0;
foreach($income as $user){
    $total_income += $user->price;
}
$total_res=0;
foreach($res as $sell){
    $total_res += $sell->total;
}
$total_spends=0;
foreach($spends as $user){
    $total_spends += $user->price;
}
$total_payments=0;
foreach($payment as $user){
    $total_payments += abs($user->val);
}
    $total_total += ($total+$total_income+$total_payments+$hand+$sell_payment)-($total_res+$total_spends);
    echo "<b>".Core::$symbol." ".number_format( ($total+$total_income+$total_payments+$hand+$sell_payment)-($total_res+$total_spends),2,".",",")."</b>";

?>      

    </td>
    <td>
      <?php
      $u = UserData::getById($box->user_id);
      echo $u->name." ".$u->lastname;
      ?>
    </td>
    <td><?php echo $box->getStock()->name; ?></td>
    <td><?php echo date("d-m-Y h:i:s a", strtotime($box->created_at)); ?></td>
  </tr>
<?php endforeach; ?>
</table>
<div class='box-body'><h2>Total: <?php echo Core::$symbol." ".number_format($total_total,2,".",","); ?></h2></div>
</div>

  <?php
}else {

?>
  <div>
    <h2>No hay corte de caja</h2>
    <p>No se ha realizado ningun corte de caja.</p>
  </div>

<?php } ?>
  </div>
</div>
<script type="text/javascript">
      $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>


</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="calendar"):
/////////////////////////////////////////////////////////////// SELLS ////////////////////////
$TicketMm = StockData::getPrincipal()->ticket_mm;

$products = BookingData::getAllBySQL("where status=0 and stock_id=".StockData::getPrincipal()->id);

if(count($products)>0):
foreach($products as $sell):
if(date("Y-m",strtotime($sell->start_at))==date("Y-m",strtotime($_GET["start_at"]))):?>
            <div class="card" style="background-color: #333;">
              <div class="card-header">
                 
               <h3 class="dropdown-item-title">
                  <span class="float-right text-sm text-warning"><i class="far fa-clock mr-1"></i><?php echo date("d-m-Y h:i",strtotime($sell->start_at));?> al <?php echo date("d-m-Y h:i",strtotime($sell->end_at));?></span>
                  <br>
                <?php echo $sell->day;?> <?php 
switch (Core::$user->language){
  case 'ES': echo 'Dias'; break;
  case 'EN': echo 'Days'; break;
}
?>
                </h3>
               
         <span class="float-right text-sm text-green">  <?php echo $sell->getPerson()->phone;?> <i class="fas fa-phone"></i></span>
                <?php echo $sell->getPerson()->name;?> </h3>

              <p class="text-sm"><i class="fas fa-car"></i> <?php $brand = BrandData::getById($sell->getCars()->brand_id); echo $brand->name." ".$sell->getCars()->name." ".$sell->getCars()->year." [".$sell->getCars()->token."]"; ?> </p>
              
             
              
              </div>

            </div>
<?php endif; endforeach; ?>



  <?php else:?>
  <div>
         
    <h2><?php 
switch (Core::$user->language){
  case 'ES': echo 'No hay reservas'; break;
  case 'EN': echo 'There are no reservations'; break;
}
?></h2>
    <p><?php 
switch (Core::$user->language){
  case 'ES': echo 'No se ha realizado ninguna operacion.'; break;
  case 'EN': echo 'No operation has been performed.'; break;
}
?></p>
   
  </div>
  <?php endif;?>



<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="spends"):
///////////////////////////////////////// SPENDS ///////////////////////////////////////

$products = null;

// print_r(Core::$user);
if(isset($_SESSION["user_id"])){
if(Core::$user->kind==3 || Core::$user->kind==4){
$products = SpendData::getAllBySQL(" where user_id=".Core::$user->id." order by created_at desc");

}
else if(Core::$user->kind==2){
$products = SpendData::getAllBySQL(" where stock_to_id=".Core::$user->stock_id." order by created_at desc");
}
else{
//print_r($_GET);
$sql = "select SQL_BIG_RESULT * from spend ";
$whereparams = array();
$whereparams[] = " (kind=1) ";
if(isset($_GET["stock_id"]) && $_GET["stock_id"]!=""){
  $whereparams[] = " stock_to_id=$_GET[stock_id] ";
}
if(isset($_GET["start_at"]) && isset($_GET["finish_at"]) && $_GET["start_at"]!="" && $_GET["finish_at"]!=""){
  $whereparams[] = " ( date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]' ) ";
}

 $sql2 = $sql." where ".implode(" and ", $whereparams)." order by created_at desc";

$products = SpendData::getAllBySQL2($sql2);
$TicketMm = StockData::getPrincipal()->ticket_mm;

}
}

if(count($products)>0){?>
<br>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
<thead>
  <th>Accion</th>
    
      <th>Tipo de Gasto</th>
      <th>Proveedor</th>
      <th>No. Comp.</th>
      <th>No. Factura</th>
      <th>Pago</th>
      <th>ISR</th>
      <th>ITBIS Ret.</th>
      <th>Concepto</th>
      <th>Monto</th>
      <th>Fecha</th>
      <th>Accion</th>
  </thead>
 <?php
      foreach($products as $user):?>
        <tr>
         
        <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm">
                        <a href="./?view=finance&opt=edit&id=<?php echo $user->id;?>&spends=negocio" class="btn btn-success"><i class="fas fa-edit"></i></a>
                        <a href="<?php echo $TicketMm; ?>/ticket-spends.php?id=<?php echo $user->id; ?>" class="btn btn-info"><i class="fas fa-print"></i></a>
                      </div>
        </td>
          
           <td><?php $g= $user->getTG(); echo $g->name;?></td>
           <td><?php echo $user->person_id;?></td>
           <td><?php echo $user->voucher_code; ?></td>
           <td><?php echo $user->invoice_code; ?></td>
          <td><?php $p= $user->getF(); echo $p->name;?></td>
          <td><?php echo $user->imp_rent;?></td>
          <td><?php echo $user->itbis_ret;?></td>
         <td><?php echo $user->name; ?></td>
        
         <td><?php echo Core::$symbol; ?> <?php echo number_format($user->price,2,".",","); ?></td>
          <td><?php echo date("d-m-Y", strtotime($user->created_at)); ?></td>
     <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=finance&opt=delspend&id=<?php echo $user->id;?>&kind=1" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"></i></a>
                    
    <?php endif;?>
<?php endforeach; ?>
</td>

        </tr>

<?php endforeach; ?>


</table>
</div>
</div>



    <?php
}else{?><br>
  <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Gastos</h2>
    <p><?php 
switch (Core::$user->language){
  case 'ES': echo 'No se ha realizado ninguna operacion.'; break;
  case 'EN': echo 'No operation has been performed.'; break;
}
?></p>
  </div>
</div>
  <?php
}


?>

<script type="text/javascript">
      $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="Otros"):
///////////////////////////////////////// SPENDS ///////////////////////////////////////

$products = null;

// print_r(Core::$user);
if(isset($_SESSION["user_id"])){
if(Core::$user->kind==3 || Core::$user->kind==4){
$products = SpendData::getAllBySQL(" where user_id=".Core::$user->id." order by created_at desc");

}
else if(Core::$user->kind==2){
$products = SpendData::getAllBySQL(" where stock_to_id=".Core::$user->stock_id." order by created_at desc");
}
else{
//print_r($_GET);
$sql = "select SQL_BIG_RESULT * from spend ";
$whereparams = array();
$whereparams[] = " (kind=2) ";
if(isset($_GET["stock_id"]) && $_GET["stock_id"]!=""){
  $whereparams[] = " stock_to_id=$_GET[stock_id] ";
}
if(isset($_GET["start_at"]) && isset($_GET["finish_at"]) && $_GET["start_at"]!="" && $_GET["finish_at"]!=""){
  $whereparams[] = " ( date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]' ) ";
}

 $sql2 = $sql." where ".implode(" and ", $whereparams)." order by created_at desc";

$products = SpendData::getAllBySQL2($sql2);
$TicketMm = StockData::getPrincipal()->ticket_mm;

}
}

if(count($products)>0){?>
<br>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
<thead>
  <th>Accion</th>
      <th>Pago</th>
      <th>Concepto</th>
      <th>Monto</th>
      <th>Fecha</th>
      <th>Accion</th>
  </thead>
 <?php
      foreach($products as $user):?>
        <tr>
         
        <td class="text-right py-0 align-middle">
                      
                        <a href="./?view=finance&opt=edit&id=<?php echo $user->id;?>&spends=other" class="btn btn-block btn-success"><i class="fas fa-edit"></i></a>
                     
        </td>
         
          
          <td><?php $p= $user->getF(); echo $p->name;?></td>
          
         <td><?php echo $user->name; ?></td>
        
         <td><?php echo Core::$symbol; ?> <?php echo number_format($user->price,2,".",","); ?></td>
          <td><?php echo date("d-m-Y", strtotime($user->created_at)); ?></td>
     <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=finance&opt=delspend&id=<?php echo $user->id;?>&kind=2" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"></i></a>
                    
    <?php endif;?>
<?php endforeach; ?>
</td>

        </tr>

<?php endforeach; ?>


</table>
</div>
</div>



    <?php
}else{?><br>
  <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Gastos</h2>
    <p><?php 
switch (Core::$user->language){
  case 'ES': echo 'No se ha realizado ninguna operacion.'; break;
  case 'EN': echo 'No operation has been performed.'; break;
}
?></p>
  </div>
</div>
  <?php
}


?>

<script type="text/javascript">
      $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="maintenance"):
///////////////////////////////////////// SPENDS ///////////////////////////////////////

$products = null;

// print_r(Core::$user);
if(isset($_SESSION["user_id"])){
if(Core::$user->kind==3 || Core::$user->kind==4){
$products = MaintenanceData::getAllBySQL(" where user_id=".Core::$user->id." order by created_at desc");

}
else if(Core::$user->kind==2){
$products = MaintenanceData::getAllBySQL(" where stock_id=".Core::$user->stock_id." order by created_at desc");
}
else{
//print_r($_GET);
$sql = "select SQL_BIG_RESULT * from maintenance ";
$whereparams = array();
if(isset($_GET["stock_id"]) && $_GET["stock_id"]!=""){
  $whereparams[] = " stock_id=$_GET[stock_id] ";
}
if(isset($_GET["start_at"]) && isset($_GET["finish_at"]) && $_GET["start_at"]!="" && $_GET["finish_at"]!=""){
  $whereparams[] = " ( date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]' ) ";
}

 $sql2 = $sql." where ".implode(" and ", $whereparams)." order by created_at desc";

$products = MaintenanceData::getAllBySQL2($sql2);
$TicketMm = StockData::getPrincipal()->ticket_mm;

}
}

if(count($products)>0){?>
<br>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
<thead>
  <th>Accion</th>
      <th>Vehiculo</th>
      <th>Mantenimiento</th>
      <th>Usuario</th>
      <th>Monto</th>
      <th>Fecha</th>
      <th>Accion</th>
  </thead>
 <?php
      foreach($products as $user):?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=finance&opt=edit&id=<?php echo $user->id;?>&spends=maintenance" class="btn btn-success"><i class="fas fa-edit"></i></a>
                      </div>
        </td>
        <td><?php $brand = BrandData::getById($user->getCars()->brand_id); $cars = CarsData::getById($user->getCars()->id); echo $brand->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name." - ".$cars->chassis; ?></td>
        <td><?php echo $user->maintenance; ?></td>
        <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
        <td><?php echo number_format($user->total,2,".",","); ?></td>
        <td> <?php echo  date("d-m-Y",strtotime($user->created_at));?></td>
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=maintenance&opt=del&id=<?php echo $user->id;?>" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> Eliminar</i></a>
                    
    <?php endif;?>
<?php endforeach; ?>
</td>
    </tr>

<?php endforeach; ?>


</table>
</div>
</div>



    <?php
}else{?><br>
  <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Mantenimiento</h2>
    <p><?php 
switch (Core::$user->language){
  case 'ES': echo 'No se ha realizado ninguna operacion.'; break;
  case 'EN': echo 'No operation has been performed.'; break;
}
?></p>
  </div>
</div>
  <?php
}


?>

<script type="text/javascript">
      $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="oil"):
///////////////////////////////////////// SPENDS ///////////////////////////////////////

$products = null;

// print_r(Core::$user);
if(isset($_SESSION["user_id"])){
if(Core::$user->kind==3 || Core::$user->kind==4){
$products = OilData::getAllBySQL(" where user_id=".Core::$user->id." order by created_at desc");

}
else if(Core::$user->kind==2){
$products = OilData::getAllBySQL(" where stock_id=".Core::$user->stock_id." order by created_at desc");
}
else{
//print_r($_GET);
$sql = "select SQL_BIG_RESULT * from oil ";
$whereparams = array();
if(isset($_GET["stock_id"]) && $_GET["stock_id"]!=""){
  $whereparams[] = " stock_id=$_GET[stock_id] ";
}
if(isset($_GET["start_at"]) && isset($_GET["finish_at"]) && $_GET["start_at"]!="" && $_GET["finish_at"]!=""){
  $whereparams[] = " ( date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]' ) ";
}

 $sql2 = $sql." where ".implode(" and ", $whereparams)." order by created_at desc";

$products = OilData::getAllBySQL2($sql2);
$TicketMm = StockData::getPrincipal()->ticket_mm;

}
}

if(count($products)>0){?>
<br>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
<thead>
  <th>Accion</th>
      <th>Vehiculo</th>
      <th>Kms/Actual</th>
      <th>Usuario</th>
      <th>Monto</th>
      <th>Fecha</th>
      <th>Accion</th>
  </thead>
 <?php
      foreach($products as $user):?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=finance&opt=edit&id=<?php echo $user->id;?>&spends=oil" class="btn btn-success"><i class="fas fa-edit"></i></a>
                      </div>
        </td>
        <td><?php $brand = BrandData::getById($user->getCars()->brand_id); $cars = CarsData::getById($user->getCars()->id); echo $brand->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name." - ".$cars->chassis; ?></td>
        <td><?php echo $user->kms; ?></td>
        <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
        <td><?php echo number_format($user->total,2,".",","); ?></td>
        <td> <?php echo  date("d-m-Y",strtotime($user->created_at));?></td>
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=oil&opt=del&id=<?php echo $user->id;?>" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> Eliminar</i></a>
                    
    <?php endif;?>
<?php endforeach; ?>
</td>
    </tr>

<?php endforeach; ?>


</table>
</div>
</div>



    <?php
}else{?><br>
  <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Cambio de Aceite</h2>
    <p><?php 
switch (Core::$user->language){
  case 'ES': echo 'No se ha realizado ninguna operacion.'; break;
  case 'EN': echo 'No operation has been performed.'; break;
}
?></p>
  </div>
</div>
  <?php
}


?>

<script type="text/javascript">
      $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="toll"):
///////////////////////////////////////// SPENDS ///////////////////////////////////////

$products = null;

// print_r(Core::$user);
if(isset($_SESSION["user_id"])){
if(Core::$user->kind==3 || Core::$user->kind==4){
$products = TollData::getAllBySQL(" where user_id=".Core::$user->id." order by created_at desc");

}
else if(Core::$user->kind==2){
$products = TollData::getAllBySQL(" where stock_id=".Core::$user->stock_id." order by created_at desc");
}
else{
//print_r($_GET);
$sql = "select SQL_BIG_RESULT * from toll ";
$whereparams = array();
if(isset($_GET["stock_id"]) && $_GET["stock_id"]!=""){
  $whereparams[] = " stock_id=$_GET[stock_id] ";
}
if(isset($_GET["start_at"]) && isset($_GET["finish_at"]) && $_GET["start_at"]!="" && $_GET["finish_at"]!=""){
  $whereparams[] = " ( date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]' ) ";
}

 $sql2 = $sql." where ".implode(" and ", $whereparams)." order by created_at desc";

$products = TollData::getAllBySQL2($sql2);
$TicketMm = StockData::getPrincipal()->ticket_mm;

}
}

if(count($products)>0):?>
<br>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
<thead>
  <th>Accion</th>
      <th>Vehiculo</th>
      <th>Usuario</th>
      <th>Monto</th>
      <th>Fecha</th>
      <th>Accion</th>
  </thead>
 <?php
      foreach($products as $user):?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=finance&opt=edit&id=<?php echo $user->id;?>&spends=toll" class="btn btn-success"><i class="fas fa-edit"></i></a>
                      </div>
        </td>
        <td><?php $brand = BrandData::getById($user->getCars()->brand_id); $cars = CarsData::getById($user->getCars()->id); echo $brand->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name." - ".$cars->chassis; ?></td>
        <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
        <td><?php echo number_format($user->total,2,".",","); ?></td>
        <td> <?php echo  date("d-m-Y",strtotime($user->created_at));?></td>
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=toll&opt=del&id=<?php echo $user->id;?>" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> Eliminar</i></a>
                    
    <?php endif;?>
<?php endforeach; ?>
</td>
    </tr>

<?php endforeach; ?>


</table>
</div>
</div>



    <?php else:?><br>
  <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Peajes</h2>
    <p><?php 
switch (Core::$user->language){
  case 'ES': echo 'No se ha realizado ninguna operacion.'; break;
  case 'EN': echo 'No operation has been performed.'; break;
}
?></p>
  </div>
</div>
  <?php endif; ?>

<script type="text/javascript">
      $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="fuel"):
///////////////////////////////////////// SPENDS ///////////////////////////////////////

$products = null;

// print_r(Core::$user);
if(isset($_SESSION["user_id"])){
if(Core::$user->kind==3 || Core::$user->kind==4){
$products = FuelsData::getAllBySQL(" where user_id=".Core::$user->id." order by created_at desc");

}
else if(Core::$user->kind==2){
$products = FuelsData::getAllBySQL(" where stock_id=".Core::$user->stock_id." order by created_at desc");
}
else{
//print_r($_GET);
$sql = "select SQL_BIG_RESULT * from fuels ";
$whereparams = array();
if(isset($_GET["stock_id"]) && $_GET["stock_id"]!=""){
  $whereparams[] = " stock_id=$_GET[stock_id] ";
}
if(isset($_GET["start_at"]) && isset($_GET["finish_at"]) && $_GET["start_at"]!="" && $_GET["finish_at"]!=""){
  $whereparams[] = " ( date(created_at) between '$_GET[start_at]' and '$_GET[finish_at]' ) ";
}

 $sql2 = $sql." where ".implode(" and ", $whereparams)." order by created_at desc";

$products = FuelsData::getAllBySQL2($sql2);
$TicketMm = StockData::getPrincipal()->ticket_mm;

}
}

if(count($products)>0):?>
<br>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
<thead>
  <th>Accion</th>
      <th>Vehiculo</th>
      <th>Usuario</th>
      <th>Monto</th>
      <th>Fecha</th>
      <th>Accion</th>
  </thead>
 <?php
      foreach($products as $user):?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=finance&opt=edit&id=<?php echo $user->id;?>&spends=fuel" class="btn btn-success"><i class="fas fa-edit"></i></a>
                      </div>
        </td>
        <td><?php $brand = BrandData::getById($user->getCars()->brand_id); $cars = CarsData::getById($user->getCars()->id); echo $brand->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name." - ".$cars->chassis; ?></td>
        <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
        <td><?php echo number_format($user->total,2,".",","); ?></td>
        <td> <?php echo  date("d-m-Y",strtotime($user->created_at));?></td>
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=fuels&opt=del&id=<?php echo $user->id;?>" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> Eliminar</i></a>
                    
    <?php endif;?>
<?php endforeach; ?>
</td>
    </tr>

<?php endforeach; ?>


</table>
</div>
</div>



    <?php else:?><br>
  <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Combustible</h2>
    <p><?php 
switch (Core::$user->language){
  case 'ES': echo 'No se ha realizado ninguna operacion.'; break;
  case 'EN': echo 'No operation has been performed.'; break;
}
?></p>
  </div>
</div>
  <?php endif; ?>

<script type="text/javascript">
      $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
<?php endif; ?>

