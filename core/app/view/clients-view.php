<?php if(isset($_GET["opt"]) && $_GET["opt"]=="programmed"): 
$clistock= PersonData::getById($_SESSION["client_id"]);?>

<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-history'></i> 
<?php 
switch ($clistock->language){
 case 'ES': echo "Listado de Reservas"; break;
 case 'EN': echo "Reservation List"; break;
}
?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> 
<?php 
switch ($clistock->language){
 case 'ES': echo "Tablero"; break;
 case 'EN': echo "Board"; break;
}
?></li>
              <li class="breadcrumb-item active"><i class='fa fa-history'></i> 
<?php 
switch ($clistock->language){
 case 'ES': echo "Reservas"; break;
 case 'EN': echo "Reservation"; break;
}
?></li>
           
            </ol>
          </div><!-- /.col -->
    
    </div>
<div class="callout callout-info">
              <h5><i class="fas fa-info"></i> 
<?php 
switch ($clistock->language){
 case 'ES': echo "Nota"; break;
 case 'EN': echo "Note"; break;
}
?>:</h5>
              <?php 
switch ($clistock->language){
 case 'ES': echo "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo."; break;
 case 'EN': echo "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; break;
}
?>
            </div>
            
<?php

$clistock= PersonData::getById($_SESSION["client_id"]);
$users = BookingData::getAllBySQL("where status=0 and person_id=".$clistock->id." order by id desc");
$TicketMm = StockData::getFPrincipal($clistock->stock_id)->ticket_mm;
$method = StockData::getFPrincipal($clistock->stock_id)->method;
    if(count($users)>0):?>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      <th>
<?php 
switch ($clistock->language){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
       <th><?php 
switch ($clistock->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Cliente"; break;
 case 'EN': echo "Customer"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Precio Por Dia"; break;
 case 'EN': echo "Price per day"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Dia"; break;
 case 'EN': echo "Day"; break;
}
?></th>
      <th>Extra</th>
      <th>ITBIS (18%)</th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Otros Cobros"; break;
 case 'EN': echo "Other Charges"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Tarjeta (".StockData::getbyId($clistock->stock_id)->card."%)"; break;
 case 'EN': echo "Card (".StockData::getbyId($clistock->stock_id)->card."%)"; break;
}
?></th>
      <th>Total</th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Abonado"; break;
 case 'EN': echo "Subscriber"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Restante"; break;
 case 'EN': echo "Remaining"; break;
}
?></th>
      <th>Rent A Car</th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Entrega"; break;
 case 'EN': echo "Delivery"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Recibir"; break;
 case 'EN': echo "Receive"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Creado Por"; break;
 case 'EN': echo "Created By"; break;
}
?></th>
    </thead>

    <tfoot>
      <tr>
    
      <th>
<?php 
switch ($clistock->language){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
       <th><?php 
switch ($clistock->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Cliente"; break;
 case 'EN': echo "Customer"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Precio Por Dia"; break;
 case 'EN': echo "Price per day"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Dia"; break;
 case 'EN': echo "Day"; break;
}
?></th>
      <th>Extra</th>
      <th>ITBIS (18%)</th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Otros Cobros"; break;
 case 'EN': echo "Other Charges"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Tarjeta (".StockData::getbyId($clistock->stock_id)->card."%)"; break;
 case 'EN': echo "Card (".StockData::getbyId($clistock->stock_id)->card."%)"; break;
}
?></th>
      <th>Total</th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Abonado"; break;
 case 'EN': echo "Subscriber"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Restante"; break;
 case 'EN': echo "Remaining"; break;
}
?></th>
      <th>Rent A Car</th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Entrega"; break;
 case 'EN': echo "Delivery"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Recibir"; break;
 case 'EN': echo "Receive"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Creado Por"; break;
 case 'EN': echo "Created By"; break;
}
?></th>
      </tr>
    </tfoot>

      <?php foreach($users as $user):  
$totpayments = 0;
$payments = PaymentData::getByPayment($user->id);
$totpayments = $payments[0]->t!=null?$payments[0]->t:0;?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">

 <a href="<?php echo $TicketMm; ?>/ticket-reserve-client.php?id=<?php echo $user->id; ?>" class="btn btn-info"><i class="fas fa-print"></i></a>
                   </div>
        </td>
<td><?php  if ($totpayments==0): echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; elseif ($totpayments>0 and $totpayments<$user->total): echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; elseif ($user->total==$totpayments): echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; endif;  $brand = BrandData::getById($user->getCars()->brand_id); echo $brand->name." ".$user->getCars()->name." ".$user->getCars()->year." [".$user->getCars()->token."]"; ?></td>
        <td><?php echo $user->getPerson()->name; ?></td>
        <td><?php echo number_format($user->price,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo $user->day; ?></td>
        <td><?php echo number_format($user->xtotal,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format((($user->price*$user->day)+$user->xtotal)*($user->iva/100),2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format($user->plane,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format($user->total*($user->card/100),2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format($user->total,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format($totpayments,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format(($user->total-$totpayments),2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo $user->getStock()->name; ?></td>
        <td><?php echo $user->start_at; ?></td>
        <td><?php echo $user->end_at; ?></td>
         <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
        
    </tr>
    
    <?php endforeach; ?>
      </table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->

      <?php else:?>
     
         <div class="card">
              <div class="card-header">
    <h2>No hay Reservas</h2>
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
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="filled"): 
$clistock= PersonData::getById($_SESSION["client_id"]);?>

<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-history'></i> <?php 
switch ($clistock->language){
 case 'ES': echo "Listado de Reservas"; break;
 case 'EN': echo "Reservation List"; break;
}
?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> <?php 
switch ($clistock->language){
 case 'ES': echo "Tablero"; break;
 case 'EN': echo "Board"; break;
}
?></li>
              <li class="breadcrumb-item active"><i class='fa fa-history'></i> 
<?php 
switch ($clistock->language){
 case 'ES': echo "Reservas"; break;
 case 'EN': echo "Reservation"; break;
}
?>
           
            </ol>
          </div><!-- /.col -->
    
    </div>
<div class="callout callout-info">
              <h5><i class="fas fa-info"></i> <?php 
switch ($clistock->language){
 case 'ES': echo "Nota"; break;
 case 'EN': echo "Note"; break;
}
?>:</h5>
             <?php 
switch ($clistock->language){
 case 'ES': echo "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo."; break;
 case 'EN': echo "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; break;
}
?>
            </div>
            
<?php
$users = BookingData::getAllBySQL("where status=3 and person_id=".$clistock->id." order by id desc");
$TicketMm = StockData::getFPrincipal($clistock->stock_id)->ticket_mm;
$method = StockData::getFPrincipal($clistock->stock_id)->method;
    if(count($users)>0):?>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      <th>Accion</th>
       <th>Vehiculo</th>
      <th>Cliente</th>
      <th>Tarifa</th>
      <th>Dia(s)</th>
      <th>Extra</th>
      <th>ITBIS (18%)</th>
      <th>Aeropuerto</th>
      <th>Tarjeta (3%)</th>
      <th>Total</th>
      <th>Abonado</th>
      <th>Restante</th>
      <th>RentCar</th>
      <th>Entrega</th>
      <th>Recibir</th>
       <th>Creado Por</th>
    </thead>

    <tfoot>
      <tr>
      <th>Accion</th>
      <th>Categoria</th>
      <th>Cliente</th>
      <th>Tarifa</th>
      <th>Dia(s)</th>
      <th>Extra</th>
      <th>ITBIS (18%)</th>
      <th>Aeropuerto</th>
      <th>Tarjeta (3%)</th>
      <th>Total</th>
      <th>Abonado</th>
      <th>Restante</th>
      <th>RentCar</th>
      <th>Entrega</th>
      <th>Recibir</th>
      <th>Creado Por</th>
      </tr>
    </tfoot>

      <?php foreach($users as $user):  
$totpayments = 0;
$payments = PaymentData::getByPayment($user->id);
$totpayments = $payments[0]->t!=null?$payments[0]->t:0;?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">

 <a href="<?php echo $TicketMm; ?>/ticket-client.php?id=<?php echo $user->id; ?>" class="btn btn-info"><i class="fas fa-print"></i></a>
                   </div>
        </td>

     <td><?php  if ($totpayments==0): echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; elseif ($totpayments>0 and $totpayments<$user->total): echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; elseif ($user->total==$totpayments): echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; endif;  $brand = BrandData::getById($user->getCars()->brand_id); echo $brand->name." ".$user->getCars()->name." ".$user->getCars()->year." [".$user->getCars()->token."]"; ?></td>
        <td><?php echo $user->getPerson()->name; ?></td>
        <td><?php echo number_format($user->price,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo $user->day; ?></td>
        <td><?php echo number_format($user->xtotal,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format((($user->price*$user->day)+$user->xtotal)*($user->iva/100),2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format($user->plane,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format($user->total*($user->card/100),2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format($user->total,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format($totpayments,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format(($user->total-$totpayments),2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo $user->getStock()->name; ?></td>
        <td><?php echo $user->start_at; ?></td>
        <td><?php echo $user->end_at; ?></td>
         <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
    </tr>
    
    <?php endforeach; ?>
      </table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->

      <?php else:?>
     
         <div class="card">
              <div class="card-header">
    <h2>No hay Reservas</h2>
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

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="rent"): 
$clistock= PersonData::getById($_SESSION["client_id"]);?>

<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-history'></i> <?php 
switch ($clistock->language){
 case 'ES': echo "Listado de Reservas"; break;
 case 'EN': echo "Reservation List"; break;
}
?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> <?php 
switch ($clistock->language){
 case 'ES': echo "Tablero"; break;
 case 'EN': echo "Board"; break;
}
?></li>
              <li class="breadcrumb-item active"><i class='fa fa-car'></i>  <?php 
switch ($clistock->language){
 case 'ES': echo "Rentas Activas"; break;
 case 'EN': echo "Active Income"; break;
}
?></li>
           
            </ol>
          </div><!-- /.col -->
    
    </div>
<div class="callout callout-info">
              <h5><i class="fas fa-info"></i> <?php 
switch ($clistock->language){
 case 'ES': echo "Nota"; break;
 case 'EN': echo "Note"; break;
}
?>:</h5>
              <?php 
switch ($clistock->language){
 case 'ES': echo "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo."; break;
 case 'EN': echo "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; break;
}
?>
            </div>
            
<?php
$users = BookingData::getAllBySQL("where type_id=1 and status=1 and person_id=".$clistock->id." order by id desc");
$TicketMm = StockData::getFPrincipal($clistock->stock_id)->ticket_mm;
$method = StockData::getFPrincipal($clistock->stock_id)->method;
    if(count($users)>0):?>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      
      <th>
<?php 
switch ($clistock->language){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
       <th><?php 
switch ($clistock->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Cliente"; break;
 case 'EN': echo "Customer"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Precio Por Dia"; break;
 case 'EN': echo "Price per day"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Dia"; break;
 case 'EN': echo "Day"; break;
}
?></th>
      <th>Extra</th>
      <th>ITBIS (18%)</th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Otros Cobros"; break;
 case 'EN': echo "Other Charges"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Tarjeta (".StockData::getbyId($clistock->stock_id)->card."%)"; break;
 case 'EN': echo "Card (".StockData::getbyId($clistock->stock_id)->card."%)"; break;
}
?></th>
      <th>Total</th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Abonado"; break;
 case 'EN': echo "Subscriber"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Restante"; break;
 case 'EN': echo "Remaining"; break;
}
?></th>
      <th>Rent A Car</th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Entrega"; break;
 case 'EN': echo "Delivery"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Recibir"; break;
 case 'EN': echo "Receive"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Creado Por"; break;
 case 'EN': echo "Created By"; break;
}
?></th>
    </thead>

    <tfoot>
      <tr>
     
      <th>
<?php 
switch ($clistock->language){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
       <th><?php 
switch ($clistock->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Cliente"; break;
 case 'EN': echo "Customer"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Precio Por Dia"; break;
 case 'EN': echo "Price per day"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Dia"; break;
 case 'EN': echo "Day"; break;
}
?></th>
      <th>Extra</th>
      <th>ITBIS (18%)</th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Otros Cobros"; break;
 case 'EN': echo "Other Charges"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Tarjeta (".StockData::getbyId($clistock->stock_id)->card."%)"; break;
 case 'EN': echo "Card (".StockData::getbyId($clistock->stock_id)->card."%)"; break;
}
?></th>
      <th>Total</th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Abonado"; break;
 case 'EN': echo "Subscriber"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Restante"; break;
 case 'EN': echo "Remaining"; break;
}
?></th>
      <th>Rent A Car</th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Entrega"; break;
 case 'EN': echo "Delivery"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Recibir"; break;
 case 'EN': echo "Receive"; break;
}
?></th>
      <th><?php 
switch ($clistock->language){
 case 'ES': echo "Creado Por"; break;
 case 'EN': echo "Created By"; break;
}
?></th>
      </tr>
    </tfoot>

      <?php foreach($users as $user):  
$totpayments = 0;
$payments = PaymentData::getByPayment($user->id);
$totpayments = $payments[0]->t!=null?$payments[0]->t:0;?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">

 <a href="<?php echo $TicketMm; ?>/ticket-client.php?id=<?php echo $user->id; ?>" class="btn btn-info"><i class="fas fa-print"></i></a>
                   </div>
        </td>

<td><?php  if ($totpayments==0): echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; elseif ($totpayments>0 and $totpayments<$user->total): echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; elseif ($user->total==$totpayments): echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; endif;  $brand = BrandData::getById($user->getCars()->brand_id); echo $brand->name." ".$user->getCars()->name." ".$user->getCars()->year." [".$user->getCars()->token."]"; ?></td>
        <td><?php echo $user->getPerson()->name; ?></td>
        <td><?php echo number_format($user->price,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo $user->day; ?></td>
        <td><?php echo number_format($user->xtotal,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format((($user->price*$user->day)+$user->xtotal)*($user->iva/100),2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format($user->plane,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format($user->total*($user->card/100),2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format($user->total,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format($totpayments,2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo number_format(($user->total-$totpayments),2,".",",")." ".StockData::getbyId($clistock->stock_id)->currency; ?></td>
        <td><?php echo $user->getStock()->name; ?></td>
        <td><?php echo $user->start_at; ?></td>
        <td><?php echo $user->end_at; ?></td>
         <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
    </tr>
    
    <?php endforeach; ?>
      </table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->

      <?php else:?>
     
         <div class="card">
              <div class="card-header">
    <h2>No hay Rentas Activas</h2>
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

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="credit"):
$clistock= PersonData::getById($_SESSION["client_id"]);
$type = StockData::getFPrincipal($clistock->stock_id)->type;
?>
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
              <li class="breadcrumb-item active"><i class='fa fa-copy'></i> DEUDA PENDIENTE</a></li>

            </ol>
          </div><!-- /.col -->

    </div>

 

<?php  $sells = BookingData::getCreditByClientId($clistock->id,$clistock->stock_id,2);

foreach ($sells as $sell) {
$tx = PaymentData::sumBySellId2($sell->id,$clistock->stock_id)->total;
$selltx+=$tx;
}

    if($selltx>0):?>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      <th>Nombre completo</th>
      <th>Direccion</th>
      <th>Telefono</th>
      <th>Saldo Pendiente</th>
    </thead>
    <tfoot>
      <th>Nombre completo</th>
      <th>Direccion</th>
      <th>Telefono</th>
      <th>Saldo Pendiente</th>
    </tfoot>

<?php 
$total=0;
foreach ($sells as $sell):
$tx = PaymentData::sumBySellId2($sell->id,$clistock->stock_id)->total;
if($tx>0){
$total+=$tx;
}
  if ($total>0):?>
        <tr>

      
        <td><?php echo utf8_decode($clistock->name." ".$clistock->lastname);?></td>
        <td><?php echo $clistock->address1; ?></td>
        <td><?php echo $clistock->phone1; ?></td>
        <td><?php echo number_format($total)." ".StockData::getbyId($clistock->stock_id)->currency;?></td>
        
        </tr>
        <?php endif; endforeach; ?>
      </table>
      </div>
      </div>
    </div>
      <?php else:?>
     
         <div class='card'>
              <div class='card-header'>
        <h2>No hay clientes con creditos</h2>
        <p>No se ha realizado ninguna operacion.</p>
    </div>
</div>

  <?php  endif; ?>


  </div>
</div>
</section>
<script type="text/javascript">
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-6:eq(0)');
</script>

<?php endif; ?>