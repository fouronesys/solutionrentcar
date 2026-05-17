<?php 
if(isset($_GET["opt"]) && $_GET["opt"] == "all"):

    if(isset($_SESSION["user_id"])){

        $language_user = Core::$user->language;
        // echo $language_user; // (opcional) quítalo si no quieres imprimirlo arriba
        
        $stock_user = StockData::getPrincipal();

    } elseif(isset($_SESSION["client_id"])){

        $clistock = PersonData::getById($_SESSION["client_id"]);
        $language_user = $clistock->language;
        // echo $language_user; // (opcional)
        
        $stock_user = StockData::getById($clistock->stock_id);

    }

?>
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0" style="color: white;"><i class='fa fa-history'></i> 
<?php 
switch ($language_user){
 case 'ES': echo "Listado de Reservas"; break;
 case 'EN': echo "Reservations List"; break;
}
?></h1>
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
  const el = document.getElementById("reloj");
  if(el) el.textContent = `${horas}:${minutos}:${segundos}`;
}

// Actualiza cada segundo
setInterval(actualizarReloj, 1000);
actualizarReloj(); // Llamada inicial
</script>

    </div>

<div class="callout callout-warning" style="background-color:#222; color: white;">
  <h5><i class="fas fa-info"></i> <?php echo ($language_user=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo ($language_user=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>

<?php 

if(isset($_SESSION["user_id"])):
$users = BookingData::getAllBySQL("where type=1 and status=0 and stock_id=".$stock_user->id." order by id desc");
elseif(isset($_SESSION["client_id"])):
$users = BookingData::getAllBySQL("where type=1 and status=0 and person_id=".$clistock->id." order by id desc");
endif;
$TicketMm = $stock_user->ticket_mm;
$method = $stock_user->method;

if(count($users)>0):?>

<!-- MODAL CONTRATO (UNA SOLA VEZ, FUERA DEL FOREACH) -->
<div id="modalPDF" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:#000a; z-index:9999;">
  <div style="position:relative; width:90%; height:90%; margin:5% auto; background:#1e1e1e; border-radius:10px; overflow:hidden; padding-top:80px;">
    <div style="position:absolute; top:20px; right:20px; display:flex; flex-direction:column; gap:10px;">
      <button type="button" onclick="imprimirPDF()" style="background:#28a745; color:#fff; padding:10px 16px; border-radius:40px; font-weight:bold;"><i class="fa fa-print"></i> IMPRIMIR</button>
      <a id="btnDescargar" href="#" download style="background:#007bff; color:#fff; padding:10px 16px; border-radius:40px; font-weight:bold; text-decoration:none;"><i class="fa fa-download"></i> DESCARGAR</a>
      <button type="button" onclick="cerrarPDF()" style="background:#c40030; color:#fff; padding:10px 16px; border-radius:40px; font-weight:bold;"><i class="fa fa-times"></i> CERRAR</button>
    </div>
    <iframe id="iframePDF" src="" style="width:100%; height:100%; border:none;"></iframe>
  </div>
</div>

<script>
function abrirPDF(url, event) {
  if (window.innerWidth >= 1024) {
    event.preventDefault();
    document.getElementById('iframePDF').src = url;
    document.getElementById('btnDescargar').href = url;
    document.getElementById('modalPDF').style.display = 'block';
    return false;
  }
  return true;
}
function cerrarPDF() {
  document.getElementById('modalPDF').style.display = 'none';
  document.getElementById('iframePDF').src = '';
  document.getElementById('btnDescargar').href = '#';
}
function imprimirPDF() {
  const iframe = document.getElementById('iframePDF');
  if(iframe && iframe.contentWindow) iframe.contentWindow.print();
}
</script>

<div class="card" style="background-color:#222; color: white;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
    <thead style="background-color: #333; color: white;">
      <th><?php 
switch ($language_user){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></th>

<?php if(isset($_SESSION["user_id"])):?>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Cliente"; break;
 case 'EN': echo "Customer"; break;
}
?></th>
<?php endif;?>

      <th><?php 
switch ($language_user){
case 'ES': echo "Precio/Dia"; break;
 case 'EN': echo "Price/Day"; break;
}
?></th>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Dia"; break;
 case 'EN': echo "Day"; break;
}
?></th>
      <th>Extra</th>
      <th><?php echo $stock_user->imp_name; ?> (<?php echo $stock_user->imp_val;?>%)</th>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Otros"; break;
 case 'EN': echo "Other"; break;
}
?></th>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Tarjeta"; break;
 case 'EN': echo "Card"; break;
}
?> (<?php echo $stock_user->card."%";?>)</th>
      <th>Total</th>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Abonado"; break;
 case 'EN': echo "Subscriber"; break;
}
?></th>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Restante"; break;
 case 'EN': echo "Remaining"; break;
}
?></th>

<?php if(isset($_SESSION["user_id"])):?>
      <th>Rentcar</th>
<?php endif;?>

      <th><?php 
switch ($language_user){
 case 'ES': echo "Entrega"; break;
 case 'EN': echo "Delivery"; break;
}
?></th>

      <th><?php 
switch ($language_user){
 case 'ES': echo "Recibir"; break;
 case 'EN': echo "Receive"; break;
}
?></th>

<?php if(isset($_SESSION["user_id"])):?>
       <th><?php 
switch ($language_user){
 case 'ES': echo "Creado Por"; break;
 case 'EN': echo "Created By"; break;
}
?></th>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
<?php endif;?>

    </thead>

    <tfoot style="background-color: #333; color: white;">
      <tr>
     <th><?php 
switch ($language_user){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></th>

<?php if(isset($_SESSION["user_id"])):?>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Cliente"; break;
 case 'EN': echo "Customer"; break;
}
?></th>
<?php endif;?>

      <th><?php 
switch ($language_user){
case 'ES': echo "Precio/Dia"; break;
 case 'EN': echo "Price/Day"; break;
}
?></th>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Dia"; break;
 case 'EN': echo "Day"; break;
}
?></th>
      <th>Extra</th>
      <th><?php echo $stock_user->imp_name; ?> (<?php echo $stock_user->imp_val;?>%)</th>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Otros"; break;
 case 'EN': echo "Other"; break;
}
?></th>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Tarjeta"; break;
 case 'EN': echo "Card"; break;
}
?> (<?php echo $stock_user->card."%";?>)</th>
      <th>Total</th>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Abonado"; break;
 case 'EN': echo "Subscriber"; break;
}
?></th>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Restante"; break;
 case 'EN': echo "Remaining"; break;
}
?></th>

<?php if(isset($_SESSION["user_id"])):?>
      <th>Rentcar</th>
<?php endif;?>

      <th><?php 
switch ($language_user){
 case 'ES': echo "Entrega"; break;
 case 'EN': echo "Delivery"; break;
}
?></th>

      <th><?php 
switch ($language_user){
 case 'ES': echo "Recibir"; break;
 case 'EN': echo "Receive"; break;
}
?></th>

<?php if(isset($_SESSION["user_id"])):?>
       <th><?php 
switch ($language_user){
 case 'ES': echo "Creado Por"; break;
 case 'EN': echo "Created By"; break;
}
?></th>
      <th><?php 
switch ($language_user){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
<?php endif;?>

      </tr>
    </tfoot>

<?php foreach($users as $user):  

  $totpayments = 0;
  $payments = PaymentData::getByPayment($user->id);
  if(is_array($payments) && count($payments) > 0){
    $totpayments = ($payments[0]->t!=null) ? $payments[0]->t : 0;
  }

  $pdfUrl = $TicketMm . "/ticket-reserve.php?id=" . $user->id;
?>
        <tr>
          <td class="text-right py-0 align-middle">
            <div class="btn-group btn-group btn-block">

              <a href="<?php echo $pdfUrl; ?>"
                 class="btn btn-info btn-sm"
                 onclick="return abrirPDF(this.href, event)">
                 <i class="fa fa-eye"></i>
              </a>

<?php if(isset($_SESSION["user_id"])): if(PersonData::getById($user->person_id)->is_rental==0):?>
              <a href="./?view=booking&opt=delivery&cars=<?php echo $user->getCars()->id;?>&id=<?php echo $user->id;?>" class="btn btn-warning"><i class="fas fa-car"></i></a> 
<?php endif;?> 
              <a href="./?view=booking&opt=edit&id=<?php echo $user->id;?>" class="btn btn-success"><i class="fas fa-edit"></i></a> 
            </div>
<?php endif;?>                   
          </td>

          <td>
            <?php  
              if ($totpayments==0){
                echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>';
              } elseif ($totpayments>0 && $totpayments<$user->total){
                echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>';
              } elseif ($user->total==$totpayments){
                echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>';
              }

              $brand = BrandData::getById($user->getCars()->brand_id);
              $color = ColorData::getById($user->getCars()->exterior_id);
              echo $brand->name." ".$user->getCars()->name." ".$user->getCars()->year." ".$color->name." [".$user->getCars()->token."]"; 
            ?>
          </td>

<?php if(isset($_SESSION["user_id"])):?>
          <td><?php echo $user->getPerson()->name; ?></td>
<?php endif;?>

          <td><?php echo number_format($user->price,2,".",","); ?></td>
          <td><?php echo $user->day; ?></td>
          <td><?php echo number_format($user->xtotal,2,".",","); ?></td>
          <td><?php echo number_format((($user->price*$user->day)+$user->xtotal)*($user->iva/100),2,".",","); ?></td>
          <td><?php echo number_format($user->plane,2,".",","); ?></td>
          <td><?php echo number_format($user->total*($user->card/100),2,".",","); ?></td>
          <td><?php echo number_format($user->total,2,".",","); ?></td>
          <td><?php echo number_format($totpayments,2,".",","); ?></td>
          <td><?php echo number_format(($user->total-$totpayments),2,".",","); ?></td>

<?php if(isset($_SESSION["user_id"])):?>
          <td><?php echo $user->getStock()->name; ?></td>
<?php endif;?>

          <td><?php echo $user->start_at; ?></td>
          <td><?php echo $user->end_at; ?></td>

<?php if(isset($_SESSION["user_id"])):?>
          <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
          <td class="text-right py-0 align-middle">
            <?php if(PersonData::getById($user->person_id)->is_rental==0):?>
              <a href="./?action=booking&opt=del&id=<?php echo $user->id; ?>" class="btn btn-danger btn-block btn-sm"><i class="fa fa-trash"></i> Eliminar</a>
            <?php endif;?>
          </td>
<?php endif;?>

        </tr>
<?php endforeach; ?>

</table>
</div><!-- /.box-body -->
</div>
</div><!-- /.box -->

<?php else:?>

<div class="card" style="background-color:#222;">
  <div class="card-header">
    <h2><?php 
switch ($language_user){
 case 'ES': echo "No hay Reservas"; break;
 case 'EN': echo "There are no reservations"; break;
}
?></h2>
    <p><?php 
switch ($language_user){
 case 'ES': echo "No se ha realizado ninguna operacion."; break;
 case 'EN': echo "No operation has been performed."; break;
}
?></p>
  </div>
</div>

<?php endif;?>

  </div>
</div>
    </div><!-- /.row -->
      </div><!-- /.container-fluid -->

<script type="text/javascript">
$(function(){
  $("#example2").DataTable();
});
</script>

</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="confirmation"):?>

<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-history'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Reservas Por Confirmar"; break;
 case 'EN': echo "Reservations to be confirmed"; break;
}
?></h1>
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
    
<?php if(StockData::getPrincipal()->update=="1"):?>    
<div class="row">
    
                
          <div class="col-12 col-sm-4 col-md-4 my-2">
          <a  href="./?view=booking&opt=new" class="floating-btn message-btn" style="background-color: #4DBE04;">
     <div class="icon-container">
    <i class="fa fa-suitcase-rolling"></i>
    </div>
    <span class="message-text"> CREAR RESERVA</span>
  </a>
            <!-- /.info-box -->
          </div>
          
          <!-- /.col -->
          <div class="col-12 col-sm-4 col-md-4 my-2">
            <a href="./?view=booking&opt=cluster" class="floating-btn message-btn" style="background-color: orange;">
  <div class="icon-container">
   <i class="fa fa-edit"></i>
    
   <?php if($total_registros > 0):?>
    <span class="notification-badge badge-danger"><?php echo $total_registros;?></span>
    <?php endif;?>
  </div>
  <span class="message-text">RESERVAS GRUPALES</span>
</a>
          </div>
          <!-- /.col -->

          
          <div class="col-12 col-sm-4 col-md-4 my-2">
             <a href="./?view=booking&opt=earring" class="floating-btn message-btn" style="background-color: #C70039;">
      <div class="icon-container">
    <i class="fa fa-globe"> </i>
    
    <?php if($wt_tot > 0):?>
    <span class="notification-badge badge-danger"><?php echo $wt_tot;?></span>
    <?php endif;?>
    </div>
    <span class="message-text"> POR LA PAGINA WEB</span> 
  </a>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
         
</div>
<?php else:?>
<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>
<?php endif;?>

<?php $users = BookingData::getAllBySQL("where type=1 and status=0 and firma='' and stock_id=".StockData::getPrincipal()->id." order by id desc");
$TicketMm = StockData::getPrincipal()->ticket_mm;
$method = StockData::getPrincipal()->method;
    if(count($users)>0):?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
    <thead style="background-color: #333; color: white;">
       <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Cliente"; break;
 case 'EN': echo "Customer"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Dia"; break;
 case 'EN': echo "Price/Day"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Dia"; break;
 case 'EN': echo "Day"; break;
}
?></th>
      <th>Extra</th>
      <th><?php echo StockData::getPrincipal()->imp_name; ?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Otros"; break;
 case 'EN': echo "Other"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Tarjeta"; break;
 case 'EN': echo "Card"; break;
}
?> (<?php echo StockData::getPrincipal()->card."%";?>)</th>
      <th>Total</th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Abonado"; break;
 case 'EN': echo "Subscriber"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Restante"; break;
 case 'EN': echo "Remaining"; break;
}
?></th>
      <th>Rentcar</th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Entrega"; break;
 case 'EN': echo "Delivery"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Recibir"; break;
 case 'EN': echo "Receive"; break;
}
?></th>
       <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Creado Por"; break;
 case 'EN': echo "Created By"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
    </thead>

    <tfoot style="background-color: #333; color: white;">
      <tr>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Cliente"; break;
 case 'EN': echo "Customer"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
case 'ES': echo "Precio/Dia"; break;
 case 'EN': echo "Price/Day"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Dia"; break;
 case 'EN': echo "Day"; break;
}
?></th>
      <th>Extra</th>
      <th><?php echo StockData::getPrincipal()->imp_name; ?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Otros"; break;
 case 'EN': echo "Other"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Tarjeta"; break;
 case 'EN': echo "Card"; break;
}
?> (<?php echo StockData::getPrincipal()->card."%";?>)</th>
      <th>Total</th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Abonado"; break;
 case 'EN': echo "Subscriber"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Restante"; break;
 case 'EN': echo "Remaining"; break;
}
?></th>
      <th>Rentcar</th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Entrega"; break;
 case 'EN': echo "Delivery"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Recibir"; break;
 case 'EN': echo "Receive"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Creado Por"; break;
 case 'EN': echo "Created By"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
      </tr>
    </tfoot>

      <?php foreach($users as $user):  
$totpayments = 0;
$payments = PaymentData::getByPayment($user->id);
$totpayments = $payments[0]->t!=null?$payments[0]->t:0;?>
        <tr>
            <td><?php  if ($totpayments==0): echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; elseif ($totpayments>0 and $totpayments<$user->total): echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; elseif ($user->total==$totpayments): echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; endif;  $brand = BrandData::getById($user->getCars()->brand_id); echo $brand->name." ".$user->getCars()->name." ".$user->getCars()->year." [".$user->getCars()->token."]"; ?></td>
        <td><?php echo $user->getPerson()->name; ?></td>
        <td><?php echo number_format($user->price,2,".",","); ?></td>
        <td><?php echo $user->day; ?></td>
        <td><?php echo number_format($user->xtotal,2,".",","); ?></td>
        <td><?php echo number_format((($user->price*$user->day)+$user->xtotal)*($user->iva/100),2,".",","); ?></td>
        <td><?php echo number_format($user->plane,2,".",","); ?></td>
        <td><?php echo number_format($user->total*($user->card/100),2,".",","); ?></td>
        <td><?php echo number_format($user->total,2,".",","); ?></td>
        <td><?php echo number_format($totpayments,2,".",","); ?></td>
        <td><?php echo number_format(($user->total-$totpayments),2,".",","); ?></td>
        <td><?php echo $user->getStock()->name; ?></td>
        <td><?php echo $user->start_at; ?></td>
        <td><?php echo $user->end_at; ?></td>
         <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
        <td class="text-right py-0 align-middle">
            <?php if(PersonData::getById($user->person_id)->is_rental==0):?>
                        <a href="./?action=booking&opt=del&id=<?php echo $user->id; ?>" class="btn btn-danger btn-block btn-sm"><i class="fa fa-trash"></i> Eliminar</a>
                        <?php endif;?>
</td>
    </tr>
    
    <?php endforeach; ?>
      </table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->

      <?php else:?>
     
         <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2><?php 
switch (Core::$user->language){
 case 'ES': echo "No hay Reservas"; break;
 case 'EN': echo "There are no reservations"; break;
}
?></h2>
    <p><?php 
switch (Core::$user->language){
 case 'ES': echo "No se ha realizado ninguna operacion."; break;
 case 'EN': echo "No operation has been performed."; break;
}
?></p>
    </div>
</div>
  
   <?php endif;?>



  </div>
</div>
    </div><!-- /.row -->
      </div><!-- /.container-fluid -->
<script type="text/javascript">
    $("#example2").DataTable();
</script>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="newearring"): 

if(!isset($_SESSION["user_id"])){
    Core::redir("./");
    exit;
}

if(!isset($_GET["id"]) || intval($_GET["id"]) <= 0){
    Core::redir("./?view=web&opt=all");
    exit;
}

$id = intval($_GET["id"]);

$user = BookingData::getById($id);

if(!$user){
    Core::redir("./?view=web&opt=all");
    exit;
}

if($user->type != "2"){
    Core::redir("./?view=web&opt=all");
    exit;
}

$TicketMm = StockData::getPrincipal()->ticket_mm;
$method   = StockData::getPrincipal()->method;
?>

<section class="content">
<div class="row">
<div class="col-md-12">

<div class="content-header">
<div class="container-fluid">
<div class="row mb-2">

<div class="col-sm-6">
<h1 class="m-0">
<i class='fa fa-history'></i>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Procesar Reserva Web"; break;
 case 'EN': echo "Process Web Reservation"; break;
}
?>
</h1>
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

<div class="card" style="background-color:#222;">
<div class="card-body">

<form class="form-horizontal" action="./?action=booking&opt=earring" method="post" role="form" enctype="multipart/form-data">

<input type="hidden" name="user_id" value="<?php echo $user->id; ?>">
<input type="hidden" name="type" value="2">

<div class="row">

<div class="col-md-3 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">
<?php 
switch (Core::$user->language){
 case 'ES': echo "CLIENTE/ EMPRESA"; break;
 case 'EN': echo "CUSTOMER/ COMPANY"; break;
}
?>
</label>

<select style="background-color:#333; color:white;" name="person_id" class="form-control select2" required>
<option value="">-- ELEGIR --</option>
<?php foreach(PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id) as $client): ?>
<option value="<?php echo $client->id; ?>" <?php if($client->id==$user->person_id){ echo "selected"; } ?>>
<?php echo $client->name; ?>
</option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="col-md-3 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">CONDUCTOR OPCIONAL</label>
<select style="background-color:#333; color:white;" name="person2_id" class="form-control select2">
<option value="">-- ELEGIR --</option>
<?php foreach(PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id) as $client): ?>
<option value="<?php echo $client->id; ?>" <?php if(isset($user->person2_id) && $client->id==$user->person2_id){ echo "selected"; } ?>>
<?php echo $client->name; ?>
</option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="col-md-3 col-12">
<label class="col-md-12 col-12 control-label">Fecha a Entregar</label>
<input style="background-color:#333; color:white;" type="datetime-local" value="<?php echo $user->start_at; ?>" required name="start_at" id="start_at" class="form-control">
</div>

<div class="col-md-3 col-12">
<label class="col-md-12 col-12 control-label">Fecha a Recibir</label>
<input style="background-color:#333; color:white;" type="datetime-local" value="<?php echo $user->end_at; ?>" required name="end_at" id="end_at" class="form-control">
</div>

<div class="col-md-3 col-12">
<label class="col-md-12 col-12 control-label">Tipo de Seguro</label>
<select style="background-color:#333; color:white;" class="form-control" name="type_sure">
<?php foreach (SureData::getALL() as $sure): ?>
<option value="<?php echo $sure->id; ?>" <?php if(isset($user->type_sure) && $sure->id==$user->type_sure){ echo "selected"; } ?>>
<?php echo $sure->name; ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-3 col-12">
<label class="col-md-12 col-12 control-label">Deducible</label>
<input style="background-color:#333; color:white;" type="text" name="sure" class="form-control" value="<?php echo isset($user->sure)?$user->sure:0; ?>" autocomplete="off" required>
</div>

<div hidden class="col-md-3 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">Deposito</label>
<span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
<input style="background-color:#333; color:white;" type="text" name="deposit" value="<?php echo isset($user->deposit)?$user->deposit:0; ?>" class="form-control">
</div>
</div>

<div class="col-md-3 col-12">
<label class="col-md-12 col-12 control-label">Forma de Pago</label>
<select style="background-color:#333; color:white;" name="f_id" required class="form-control select2">
<?php foreach(FData::getAll() as $client): ?>
<option value="<?php echo $client->id; ?>" <?php if(isset($user->f_id) && $client->id==$user->f_id){ echo "selected"; } ?>>
<?php echo $client->name; ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-3 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">Combustible</label>
<span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-tint"></i></span>
<select style="background-color:#333; color:white;" name="fuel" class="form-control">
<option value="R" <?php if(isset($user->fuel) && $user->fuel=="R"){ echo "selected"; } ?>>Reserva</option>
<option value="1/4" <?php if(isset($user->fuel) && $user->fuel=="1/4"){ echo "selected"; } ?>>1/4</option>
<option value="1/2" <?php if(isset($user->fuel) && $user->fuel=="1/2"){ echo "selected"; } ?>>Medio</option>
<option value="3/4" <?php if(isset($user->fuel) && $user->fuel=="3/4"){ echo "selected"; } ?>>3/4</option>
<option value="F" <?php if(isset($user->fuel) && $user->fuel=="F"){ echo "selected"; } ?>>Full</option>
</select>
</div>
</div>

<div class="col-md-4 col-12">
<label class="col-md-12 col-12 control-label">Lugar a Entregar</label>
<div class="input-group">
<select id="place_start" class="form-control select2" name="place_start" style="background-color:#333; color:white;">
<option value="" disabled>--- ELEGIR ---</option>
<?php foreach(PlaceData::getAll() as $place): ?>
<option value="<?php echo $place->name; ?>" <?php if(isset($user->place_start) && $user->place_start==$place->name){ echo "selected"; } ?>>
<?php echo $place->name; ?>
</option>
<?php endforeach; ?>
</select>
<input type="text" id="place_start2" name="place_start2" class="form-control" placeholder="Escribe aquí" style="background-color:#333; color:white; display:none;">
</div>
<small id="toggleplace_start" style="color:orange; font-size:13px; margin-top:5px; display:block; cursor:pointer;">
CAMBIAR ENTRE ESCRIBIR Y SELECCIONAR
</small>
</div>

<div class="col-md-4 col-12">
<label class="col-md-12 col-12 control-label">Lugar a Recibir</label>
<div class="input-group">
<select id="place_end" class="form-control select2" name="place_end" style="background-color:#333; color:white;">
<option value="" disabled>--- ELEGIR ---</option>
<?php foreach(PlaceData::getAll() as $place): ?>
<option value="<?php echo $place->name; ?>" <?php if(isset($user->place_end) && $user->place_end==$place->name){ echo "selected"; } ?>>
<?php echo $place->name; ?>
</option>
<?php endforeach; ?>
</select>
<input type="text" id="place_end2" name="place_end2" class="form-control" placeholder="Escribe aquí" style="background-color:#333; color:white; display:none;">
</div>
<small id="toggleplace_end" style="color:orange; font-size:13px; margin-top:5px; display:block; cursor:pointer;">
CAMBIAR ENTRE ESCRIBIR Y SELECCIONAR
</small>
</div>

<div class="card-header col-md-12 col-12 my-2">
<i class="fa fa-clone"></i> Datos Extras:
</div>

<div class="col-md-4 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">CARSEAT - [UND|PRECIO]</label>
<span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-baby"></i></span>
<input style="background-color:#333; color:white;" id="carseat1" type="number" value="0" class="form-control" name="unit_carseat" min="0">
<input style="background-color:#333; color:white;" type="number" class="form-control" required value="0" id="carseat2" name="price_carseat" min="0" step="0.01">
</div>
</div>

<div class="col-md-4 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">INTERNET - [UND|PRECIO]</label>
<span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-wifi"></i></span>
<input style="background-color:#333; color:white;" type="number" class="form-control" value="0" id="wifi1" name="unit_wifi" min="0">
<input style="background-color:#333; color:white;" type="number" class="form-control" required value="0" id="wifi2" name="price_wifi" min="0" step="0.01">
</div>
</div>

<div class="col-md-4 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">TRAILER - [UND|PRECIO]</label>
<span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-sitemap"></i></span>
<input style="background-color:#333; color:white;" type="number" value="0" class="form-control" id="trailer1" name="unit_trailer" min="0">
<input style="background-color:#333; color:white;" type="number" class="form-control" value="0" id="trailer2" required name="price_trailer" min="0" step="0.01">
</div>
</div>

<div class="card-header col-md-12 col-12 my-2">
<i class="fa fa-car"></i> Datos del Vehiculo:
</div>

<div hidden class="col-md-4 col-12">
<label class="col-md-12 col-12 control-label">Ubicacion</label>
<select style="background-color:#333; color:white;" name="location" required class="form-control select2" id="location">
<?php foreach(LocationData::getAll() as $state): ?>
<option value="<?php echo $state->id; ?>"><?php echo $state->name; ?></option>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-3 col-12" hidden>
<div class="input-group">
<label class="col-md-12 col-12 control-label">Rent A Car</label>
<span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-street-view"></i></span>
<select style="background-color:#333; color:white;" name="stock_id" required class="form-control">
<?php foreach(StockData::getAll() as $stock): ?>
<option value="<?php echo $stock->id; ?>" <?php if($stock->id==$user->stock_id){ echo 'selected'; } ?>>
<?php echo $stock->name; ?>
</option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="col-md-3 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">Vehiculo</label>
<select style="background-color:#333; color:white;" name="car_id" required class="form-control select2">
<?php foreach(CarsData::getAll() as $cars): ?>
<option value="<?php echo $cars->id; ?>" <?php if($cars->id==$user->car_id){ echo 'selected'; } ?>>
<?php echo $cars->getBrand()->name." ".$cars->name." ".$cars->year." F: [".$cars->token."]"; ?>
</option>
<?php endforeach; ?>
</select>
</div>
</div>

<?php $divisa = StockData::getPrincipal()->divisa; ?>

<div hidden class="col-md-2 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">Divisa</label>
<select style="background-color:#333; color:white;" name="divisa_id" id="divisa_id" class="form-control">
<option value="1">DOLAR</option>
<option value="<?php echo $divisa; ?>">PESOS</option>
</select>
</div>
</div>

<div class="col-md-2 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">Dias de Renta</label>
<input style="background-color:#333; color:white;" id="dias" name="day" value="<?php echo $user->day; ?>" class="form-control">
</div>
</div>

<div class="col-md-2 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">Precio por Dia</label>
<input style="background-color:#333; color:white;" type="number" value="<?php echo $user->price; ?>" required name="price2" id="tariff2" class="form-control" min="0" step="0.01">
</div>
</div>

<div class="col-md-2 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">Total Reserva</label>
<span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
<input style="background-color:#333; color:white;" type="number" value="<?php echo $user->price*$user->day; ?>" required name="total" id="amount" class="form-control" min="0" step="0.01">
</div>
</div>

<div class="col-md-2 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">Total Extra</label>
<span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
<select style="background-color:#333; color:white;" name="xtotal" id="xmount" class="form-control"></select>
</div>
</div>

<div class="col-md-2 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">Abono o Total</label>
<span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
<input style="background-color:#333; color:white;" type="number" value="0" required name="payment" id="payment" class="form-control" min="0" step="0.01">
</div>
</div>

<div class="col-md-2 col-12">
<div class="input-group">
<label class="col-md-12 col-12 control-label">Monto Restante</label>
<span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-minus-square"></i></span>
<input style="background-color:#333; color:white;" readonly id="remaining" name="remaining" class="form-control">
</div>
</div>

<div class="col-md-2 col-6">
<div class="input-group">
<label class="col-md-12 col-12 control-label">Otros Cobros</label>
<input style="background-color:#333; color:white;" type="number" required value="0" name="plane" class="form-control" min="0" step="0.01">
</div>
</div>

<div class="col-md-1 col-6">
<div class="input-group">
<label class="col-md-12 col-12 control-label"><?php echo StockData::getPrincipal()->imp_name; ?></label>
<div class="icheck-primary d-inline">
<input style="background-color:#333; color:white;" type="checkbox" name="iva" id="checkbox2">
<label for="checkbox2"><?php echo StockData::getPrincipal()->imp_val; ?>%</label>
</div>
</div>
</div>

<div hidden id="day"></div>

<div class="col-md-12 col-12 my-2">
<button id="submit" class="btn btn-primary btn-block btn-sm">
<i class="fa fa-check"></i>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Finalizar"; break;
 case 'EN': echo "Finish"; break;
}
?>
</button>
</div>

</div>
</form>

</div>
</div>

</div>
</div>
</div>
</div>
</div>
</section>

<script>
$(document).ready(function(){

    let modoManual = false;
    $('#toggleplace_start').click(function () {
        if (!modoManual) {
            $('#place_start').select2('destroy').hide();
            $('#place_start2').show();
            $('#place_start').val('');
            modoManual = true;
        } else {
            $('#place_start2').hide().val('');
            $('#place_start').show().select2();
            modoManual = false;
        }
    });

    let modoManual2 = false;
    $('#toggleplace_end').click(function () {
        if (!modoManual2) {
            $('#place_end').select2('destroy').hide();
            $('#place_end2').show();
            $('#place_end').val('');
            modoManual2 = true;
        } else {
            $('#place_end2').hide().val('');
            $('#place_end').show().select2();
            modoManual2 = false;
        }
    });

    recargarxLista();
    calcularRestante();

    $('#carseat1,#carseat2,#wifi1,#wifi2,#trailer1,#trailer2').on('keyup change', function(){
        recargarxLista();
    });

    $('#payment,#amount,#xmount,#tariff2,#dias').on('keyup change', function(){
        calcularTotalReserva();
        calcularRestante();
    });

});

function recargarxLista(){
    $.ajax({
        type:"POST",
        url:"./?action=get&opt=carseat",
        data:{
            carseat1: $('#carseat1').val(),
            carseat2: $('#carseat2').val(),
            wifi1: $('#wifi1').val(),
            wifi2: $('#wifi2').val(),
            trailer1: $('#trailer1').val(),
            trailer2: $('#trailer2').val()
        },
        success:function(r){
            $('#xmount').html(r);
            calcularRestante();
        }
    });
}

function calcularTotalReserva(){
    let dias = parseFloat($('#dias').val()) || 0;
    let precio = parseFloat($('#tariff2').val()) || 0;
    $('#amount').val((dias * precio).toFixed(2));
}

function calcularRestante(){
    let amount = parseFloat($('#amount').val()) || 0;
    let extra = parseFloat($('#xmount').val()) || 0;
    let payment = parseFloat($('#payment').val()) || 0;

    let restante = (amount + extra) - payment;

    $('#remaining').val(restante.toFixed(2));
}
</script>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="delivery"): 
$user = BookingData::getById($_GET["id"]);
$method = StockData::getPrincipal()->method;
$totpayments = 0;
$payments = PaymentData::getByPayment($user->id);
$totpayments = $payments[0]->t!=null?$payments[0]->t:0;?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0"><i class='fa fa-car'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo a Entregar"; break;
 case 'EN': echo "Vehicle to Deliver"; break;
}
?></h1>
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

<?php $cars = CarsData::getById($_GET["cars"]);?>
     

                  <div class="card" style="background-color:#222;">
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <div class="card-body">
                        <label>Datos del Vehiculo: </label>


     <div class="row">
         
    <div class="col-md-6 col-12">
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <div class="card-body">
                        <label>Rent a Car: </label>
                        <?php echo $cars->getStock()->name;?>
                        <br>
                        <label>Nombre del Vehiculo: </label>
                        <?php echo $cars->getBrand()->name;?><br>
                        <label>Modelo: </label>
                        <?php echo $cars->name;?>
                        <br>
                        <label>Año del Modelo: </label>
                        <?php echo $cars->year;?>
                        <br>
                        <label>Categoria: </label>
                        <?php echo $cars->getCategory()->name;?>
                        <br>
                        <label>Color Interior: </label>
                        <?php echo $cars->getInColor()->name;?> 
                        <br>
                        <label>Color Exterior: </label>
                        <?php echo $cars->getExColor()->name;?>
                        <br>
                        <label>Ficha: </label>
                        <?php echo $cars->token;?>
                        <br>
                        <label>Seguro de Ley: </label>
                        <?php echo $cars->insurance_id;?>
                        <br>
                        <label>Vencimiento del Seguro [LEY]: </label>
                        <?php echo  date("d-m-Y",strtotime($cars->date_insurance));?>
                        <br>
                        <label>Seguro Full: </label>
                        <?php echo $cars->insurance2_id;?>
                        <br>
                        <label>Vencimiento del Seguro [FULL]: </label>
                        <?php echo  date("d-m-Y",strtotime($cars->date2_insurance));?>
                       </div>
                    </div>

  
      
      <div class="card-header">
                      
                        <label>Foto del Vehiculo: </label>
                         
                        <?php if ($cars->invoice_file>0):?>
  <a href="./../CF-SYSTEMS/storage/invoice_files/<?php echo $cars->invoice_file;?>"  class="btn btn-default"><i class="fa fa-file"></i> Foto del Seguro (<?php echo $cars->invoice_file; ?>)</a>
                         <?php endif;?>
                      
                   </div>     
                    <div class="card-header">
                      
                        <label>Seguro de Ley: </label>
                         
                        <?php if ($cars->insurance_file>0):?>
  <a href="./../CF-SYSTEMS/storage/invoice_files/<?php echo $cars->insurance_file;?>"  class="btn btn-default"><i class="fa fa-file"></i> Foto del Seguro (<?php echo $cars->insurance_file; ?>)</a>
                         <?php endif;?>
                   
                   </div>


             <div class="card-header">
                    
                        <label>Seguro de Full: </label>
                         
                        <?php if ($cars->insurance2_file>0):?>
  <a href="./../CF-SYSTEMS/storage/invoice_files/<?php echo $cars->insurance2_file;?>"  class="btn btn-default"><i class="fa fa-file"></i> Foto del Seguro (<?php echo $cars->insurance2_file; ?>)</a>
                         <?php endif;?>
                      
                   </div>  
 
</div>

    <div class="col-md-6 col-12">
                   
<div  class="card-header">
<i class="fa fa-history"></i>  Datos de la Reserva:
</div>

  <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <div class="card-body">
                        <label>Lugar de Entrega: </label>
                        <?php echo $user->place_start;?>
                        <br>
                        <label>Fecha / Hora: </label>
                        <?php echo  date("d-m-Y h:i:s a",strtotime($user->start_at));?>
                        <br>
                        <label>Lugar de Recibir: </label>
                        <?php echo $user->place_end;?>
                        <br>
                       <label>Fecha / Hora: </label>
                        <?php echo  date("d-m-Y h:i:s a",strtotime($user->end_at));?>
                        <br>
                        <label><?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Dia"; break;
 case 'EN': echo "Price/day"; break;
}
?>: </label>
                        <?php echo $user->price;?>
                        <br>
                        <label><?php 
switch (Core::$user->language){
 case 'ES': echo "Dia"; break;
 case 'EN': echo "Day"; break;
}
?>: </label>
                        <?php echo $user->day;?>
                        <br>
                        <label><?php 
switch (Core::$user->language){
 case 'ES': echo "Conductor"; break;
 case 'EN': echo "Driver"; break;
}
?> #1: </label>
                        <?php echo $user->getPerson()->name; ?>
                        <br>
                        <label><?php 
switch (Core::$user->language){
 case 'ES': echo "CONDUCTOR OPCIONAL"; break;
 case 'EN': echo "OPTIONAL DRIVER"; break;
}
?> : </label>
                        <?php echo $user->getPerson2()->name; ?>
                        <br>
                        <label>Total: </label>
                        <?php echo number_format($user->total,2,".",","); ?>
                        <br>
                        <label><?php 
switch (Core::$user->language){
 case 'ES': echo "Abonado"; break;
 case 'EN': echo "Subscriber"; break;
}
?>: </label>
                        <?php echo number_format($totpayments,2,".",","); ?>
                        <br>
                        <label><?php 
switch (Core::$user->language){
 case 'ES': echo "Restante"; break;
 case 'EN': echo "Remaining"; break;
}
?>: </label>
                        <?php echo number_format(($user->total-$totpayments),2,".",","); ?>
                        
                      </div>
                    </div>
         
  


<style>
/* Contenedor para las imágenes */
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  top: 2%;
  width: 100%; /* Establece el ancho y alto según las dimensiones de la imagen de fondo */
  height: 20%;
}

/* Estilo para la imagen de fondo */
.background-image {
  position: absolute;
  z-index: 1;
  width: 100%;
  height: 100%;
}

/* Estilo para la imagen superior */
.overlay-image {
  z-index: 2;
  width: 100%;
  height: 100%;
}
</style>      


                      <center>
                    
<?php 
$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT danger from delivery where random=0 and method=2 and booking_id=".$_GET["id"];
$query = $con->query($sql);

while ($row = $query->fetch_array()) {
    if (!empty($row['danger'])) {
        $imagenes = explode("|", $row['danger']); // Convertir la cadena en array
$i = 1;
foreach ($imagenes as $img) {
            $imgPath = trim($img); 
   echo "   <label>Daños del Vehiculo: #$i</label><br>
   <a href='danger/$imgPath'  class='btn btn-default'><i class='fa fa-image'></i> Visualizar  Foto</a><br>" ; 
   $i++;
}
}
}
?>
                      </center>
      
    </div>

   




      

             
  
                  <div class="card" style="background-color:#222;">
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <div class="card-body">
                        <label>Revision del Vehiculo: </label>


    <form class="form-horizontal delivery" method="post" id="delivery" action="./?action=booking&opt=delivery" role="form" enctype="multipart/form-data">
    
    
		
     <div class="row">
    <div class="col-md-6 col-6">
<div class="icheck-primary d-inline">
<input style="background-color: #333; color: white;" type="checkbox" name="cat" id="checkboxPrimary1" checked>
<label for="checkboxPrimary1">
GATO
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-primary d-inline">
                        <input style="background-color: #333; color: white;" type="checkbox" name="radio" id="checkboxPrimary2" checked>
                        <label for="checkboxPrimary2">
                          RADIO
                        </label>
                      </div>
    </div>

    <div class="col-md-6 col-6">
<div class="icheck-primary d-inline">
<input style="background-color: #333; color: white;" type="checkbox"  name="replacement" id="checkboxPrimary3" checked>
<label for="checkboxPrimary3">
REPUESTO
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-primary d-inline">
                        <input style="background-color: #333; color: white;" type="checkbox" name="antenna" id="checkboxPrimary4" checked>
                        <label for="checkboxPrimary4">
                          ANTENA
                        </label>
                      </div>
    </div>


    <div class="col-md-6 col-6">
<div class="icheck-primary d-inline">
<input style="background-color: #333; color: white;" type="checkbox" name="keyring" id="checkboxPrimary5" checked>
<label for="checkboxPrimary5">
LLAVERO
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-primary d-inline">
                        <input style="background-color: #333; color: white;" type="checkbox" name="carpets" id="checkboxPrimary6" checked>
                        <label for="checkboxPrimary6">
                          ALFOMBRAS
                        </label>
                      </div>
    </div>

   <div class="col-md-6 col-6">
<div class="icheck-primary d-inline">
<input style="background-color: #333; color: white;" type="checkbox" name="belts" id="checkboxPrimary7" checked>
<label for="checkboxPrimary7">
CINTURONES
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-primary d-inline">
                        <input style="background-color: #333; color: white;" type="checkbox" name="roof_lining" id="checkboxPrimary8" checked>
                        <label for="checkboxPrimary8">
                         FORRO TECHO
                        </label>
                      </div>
    </div>    

   <div class="col-md-6 col-6">
<div class="icheck-primary d-inline">
<input style="background-color: #333; color: white;" type="checkbox" name="mirrors" id="checkboxPrimary9" checked>
<label for="checkboxPrimary9">
ESPEJOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-primary d-inline">
                        <input style="background-color: #333; color: white;" type="checkbox" name="board" id="checkboxPrimary10" checked>
                        <label for="checkboxPrimary10">
                         TABLERO
                        </label>
                      </div>
    </div>  

       <div class="col-md-6 col-6">
<div class="icheck-primary d-inline">
<input style="background-color: #333; color: white;" type="checkbox" name="document" id="checkboxPrimary11" checked>
<label for="checkboxPrimary11">
DOCUMENTOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-primary d-inline">
                        <input style="background-color: #333; color: white;" type="checkbox" name="watches" id="checkboxPrimary12" checked>
                        <label for="checkboxPrimary12">
                         RELOJES
                        </label>
                      </div>
    </div>  


       <div class="col-md-6 col-6">
<div class="icheck-primary d-inline">
<input style="background-color: #333; color: white;" type="checkbox" name="rearview" id="checkboxPrimary13" checked>
<label for="checkboxPrimary13">
RETREVISOR
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-primary d-inline">
                        <input style="background-color: #333; color: white;" type="checkbox" name="lighter" id="checkboxPrimary14" checked>
                        <label for="checkboxPrimary14">
                         ENCENDEDOR
                        </label>
                      </div>
    </div>  

           <div class="col-md-6 col-6">
<div class="icheck-primary d-inline">
<input style="background-color: #333; color: white;" type="checkbox" name="crystals" id="checkboxPrimary15" checked>
<label for="checkboxPrimary15">
CRISTALES
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-primary d-inline">
                        <input style="background-color: #333; color: white;" type="checkbox" name="cd" id="checkboxPrimary16" checked>
                        <label for="checkboxPrimary16">
                         CD CHANGER
                        </label>
                      </div>
    </div>  


           <div class="col-md-6 col-6">
<div class="icheck-primary d-inline">
<input style="background-color: #333; color: white;" type="checkbox" name="bumper" id="checkboxPrimary17" checked>
<label for="checkboxPrimary17">
TAPA COV. BUMPER
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-primary d-inline">
                        <input style="background-color: #333; color: white;" type="checkbox" name="equalizer" id="checkboxPrimary18" checked>
                        <label for="checkboxPrimary18">
                         ECUALIZADOR
                        </label>
                      </div>
    </div>  

               <div class="col-md-6 col-6">
<div class="icheck-primary d-inline">
<input style="background-color: #333; color: white;" type="checkbox" name="cup_holder" id="checkboxPrimary19" checked>
<label for="checkboxPrimary19">
PORTA VASOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-primary d-inline">
                        <input style="background-color: #333; color: white;" type="checkbox" name="plate" id="checkboxPrimary20" checked>
                        <label for="checkboxPrimary20">
                         PLACA
                        </label>
                      </div>
    </div>  

 

    <div class="col-md-6 col-6">
                      <div class="icheck-primary d-inline">
                        <input style="background-color: #333; color: white;" type="checkbox" name="seats" id="checkboxPrimary21" checked>
                        <label for="checkboxPrimary21">
                         ASIENTOS
                        </label>
                      </div>
    </div>  

               <div class="col-md-6 col-6">
<div class="icheck-primary d-inline">
<input style="background-color: #333; color: white;" type="checkbox" name="logo" id="checkboxPrimary22" checked>
<label for="checkboxPrimary22">
LOGOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-primary d-inline">
                        <input style="background-color: #333; color: white;" type="checkbox" name="batery" id="checkboxPrimary23" checked>
                        <label for="checkboxPrimary23">
                        BATERIA
                        </label>
                      </div>
    </div> 



    <div class="col-md-6 col-6">
                      <div class="icheck-primary d-inline">
                        <input style="background-color: #333; color: white;" type="checkbox" name="top" id="checkboxPrimary24" checked>
                        <label for="checkboxPrimary24">
TAPA COMBUSTIBLE
                   
                        </label>
                      </div>
    </div> 
    
    

 <div  class="card-header col-md-12 col-12 my-2" style="background-color:gray;">
<i class="fa fa-car"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Daños del Vehiculo"; break;
 case 'EN': echo "Vehicle Damage"; break;
}
?>:
</div> 
<style>
    
#vert-tabs-frontal-tab.nav-link{
    color: orange !important;
}
#vert-tabs-lateral-izquierdo-tab.nav-link{
    color: orange !important;
}
#vert-tabs-lateral-derecho-tab.nav-link{
    color: orange !important;
}
#vert-tabs-trasera-tab.nav-link{
    color: orange !important;
}
#vert-tabs-superior-tab.nav-link{
    color: orange !important;
}
#vert-tabs-inferior-tab.nav-link{
    color: orange !important;
}
</style>


        <div class="card card card-outline"  style="background-color:#222;">
         
          <div class="card-body">
         <div class="nav-wrapper">
  <ul class="nav nav-tabs d-flex flex-nowrap" id="custom-content-above-tab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="vert-tabs-frontal-tab" data-toggle="pill" href="#vert-tabs-frontal" role="tab" aria-controls="vert-tabs-frontal" aria-selected="true">FRONTAL</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-lateral-izquierdo-tab" data-toggle="pill" href="#vert-tabs-lateral-izquierdo" role="tab" aria-controls="vert-tabs-lateral-izquierdo" aria-selected="false">LATERAL IZQUIERDO</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-lateral-derecho-tab" data-toggle="pill" href="#vert-tabs-lateral-derecho" role="tab" aria-controls="vert-tabs-lateral-derecho" aria-selected="false">LATERAL DERECHO</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-trasera-tab" data-toggle="pill" href="#vert-tabs-trasera" role="tab" aria-controls="vert-tabs-trasera" aria-selected="false">TRASERA</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-superior-tab" data-toggle="pill" href="#vert-tabs-superior" role="tab" aria-controls="vert-tabs-superior" aria-selected="false">SUPERIOR</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-inferior-tab" data-toggle="pill" href="#vert-tabs-inferior" role="tab" aria-controls="vert-tabs-inferior" aria-selected="false">INFERIOR</a>
    </li>
  </ul>
</div>

<style>
  .nav-wrapper {
    overflow-x: auto;  /* Permite el scroll horizontal */
    overflow-y: hidden; /* Evita el scroll vertical */
    white-space: nowrap;
  }
  .nav-tabs {
    flex-wrap: nowrap;
  }
</style>

            <div class="tab-custom-content">
              <p class="lead mb-0"> SECCIONES</p>
            </div>
            <div class="row">
          
              <div class="col-12 col-md-12">
                <div class="tab-content" id="vert-tabs-tabContent">
                  <div class="tab-pane text-left fade show active" id="vert-tabs-frontal" role="tabpanel" aria-labelledby="vert-tabs-frontal-tab">
                     
            <!-- TO DO List -->
            <div  style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck1">
                      <label for="todoCheck1"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text1">Capó</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image1" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image1" type="file" style="display: none;" accept="image/*"  name="image1">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck1').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image1').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck1').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text1').style.textDecoration = 'line-through';
    }
  });
</script>
                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck2">
                      <label for="todoCheck2"></label>
                    </div>
                     <span class="text" id="text2">Parachoques</span>
                    <div class="tools">
                      <label for="image2" class="custom-file-upload"><i class="fa fa-upload"></i></label> 
                      <input id="image2" type="file" style="display: none;" accept="image/*"  name="image2">
                    </div>
                  </li>
                 
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck2').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image2').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck2').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text2').style.textDecoration = 'line-through';
    }
  });
</script>
                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck3">
                      <label for="todoCheck3"></label>
                    </div>
                     <span class="text" id="text3">Faros</span>
                    <div class="tools">
                     <label for="image3" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image3" type="file" style="display: none;" accept="image/*"  name="image3">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck3').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image3').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck3').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text3').style.textDecoration = 'line-through';
    }
  });
</script>
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck4">
                      <label for="todoCheck4"></label>
                    </div>
                     <span class="text" id="text4">Parrilla</span>
                    <div class="tools">
                     <label for="image4" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image4" type="file" style="display: none;" accept="image/*"  name="image4">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck4').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image4').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck4').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text4').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck5">
                      <label for="todoCheck5"></label>
                    </div>
                     <span class="text" id="text5">Parabrisas</span>
                    <div class="tools">
                     <label for="image5" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image5" type="file" style="display: none;" accept="image/*"  name="image5">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck3').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image3').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck3').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text3').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck6">
                      <label for="todoCheck6"></label>
                    </div>
                     <span class="text" id="text6">Forlay</span>
                    <div class="tools">
                     <label for="image6" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image6" type="file" style="display: none;" accept="image/*"  name="image6">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck6').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image6').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck6').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text6').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                 <input style="background-color:#222;" autocomplete="off" name="comment1"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-lateral-izquierdo" role="tabpanel" aria-labelledby="vert-tabs-lateral-izquierdo-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
            
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck7">
                      <label for="todoCheck7"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text7">Puertas</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image7" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image7" type="file" style="display: none;" accept="image/*"  name="image7">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck7').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image7').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck7').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text7').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck8">
                      <label for="todoCheck8"></label>
                    </div>
                     <span class="text" id="text8">Guardafangos</span>
                    <div class="tools">
                     <label for="image8" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image8" type="file" style="display: none;" accept="image/*"  name="image8">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck8').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image8').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck8').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text8').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck9">
                      <label for="todoCheck9"></label>
                    </div>
                     <span class="text" id="text9">Espejos retrovisores</span>
                    <div class="tools">
                     <label for="image9" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image9" type="file" style="display: none;" accept="image/*"  name="image9">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck9').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image9').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck9').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text9').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck10">
                      <label for="todoCheck10"></label>
                    </div>
                     <span class="text" id="text10">Ventanas laterales</span>
                    <div class="tools">
                     <label for="image10" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image10" type="file" style="display: none;" accept="image/*"  name="image10">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck10').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image10').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck10').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text10').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck11">
                      <label for="todoCheck11"></label>
                    </div>
                     <span class="text" id="text11">Manijas de las puertas</span>
                    <div class="tools">
                     <label for="image11" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image11" type="file" style="display: none;" accept="image/*"  name="image11">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck11').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image11').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck11').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text11').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck12">
                      <label for="todoCheck12"></label>
                    </div>
                     <span class="text" id="text12">Llantas y rines</span>
                    <div class="tools">
                     <label for="image12" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image12" type="file" style="display: none;" accept="image/*"  name="image12">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck12').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image12').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck12').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text12').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment2"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-lateral-derecho" role="tabpanel" aria-labelledby="vert-tabs-lateral-derecho-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
              
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
             
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck13">
                      <label for="todoCheck13"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text13">Puertas</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image13" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image13" type="file" style="display: none;" accept="image/*"  name="image13">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck13').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image13').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck13').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text13').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck14">
                      <label for="todoCheck14"></label>
                    </div>
                     <span class="text" id="text14">Guardafangos </span>
                    <div class="tools">
                   <label for="image14" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                   <input id="image14" type="file" style="display: none;" accept="image/*"  name="image14">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck14').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image14').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck14').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text14').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck15">
                      <label for="todoCheck15"></label>
                    </div>
                     <span class="text" id="text15">Espejos retrovisores</span>
                    <div class="tools">
                     <label for="image15" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image15" type="file" style="display: none;" accept="image/*"  name="image15">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck15').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image15').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck15').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text15').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck16">
                      <label for="todoCheck16"></label>
                    </div>
                     <span class="text" id="text16">Ventanas laterales</span>
                    <div class="tools">
                    <label for="image16" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                    <input id="image16" type="file" style="display: none;" accept="image/*"  name="image16">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck16').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image16').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck16').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text16').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck17">
                      <label for="todoCheck17"></label>
                    </div>
                     <span class="text" id="text17">Manijas de las puertas</span>
                    <div class="tools">
                     <label for="image17" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image17" type="file" style="display: none;" accept="image/*"  name="image17">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck17').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image17').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck17').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text17').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck18">
                      <label for="todoCheck18"></label>
                    </div>
                     <span class="text" id="text18">Llantas y rines</span>
                    <div class="tools">
                    <label for="image18" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image18" type="file" style="display: none;" accept="image/*"  name="image18">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck18').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image18').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck18').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text18').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment3"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>     
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-trasera" role="tabpanel" aria-labelledby="vert-tabs-trasera-tab">
                        
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
              
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck19">
                      <label for="todoCheck19"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text19">Parachoques</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                    <label for="image19" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image19" type="file" style="display: none;" accept="image/*"  name="image19">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck19').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image19').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck19').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text19').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck20">
                      <label for="todoCheck20"></label>
                    </div>
                     <span class="text" id="text20">Compuerta</span>
                    <div class="tools">
                     <label for="image20" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image20" type="file" style="display: none;" accept="image/*"  name="image20">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck20').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image20').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck20').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text20').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck21">
                      <label for="todoCheck21"></label>
                    </div>
                     <span class="text" id="text21">Faros</span>
                    <div class="tools">
                     <label for="image21" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image21" type="file" style="display: none;" accept="image/*"  name="image21">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck21').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image21').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck21').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text21').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck22">
                      <label for="todoCheck22"></label>
                    </div>
                     <span class="text" id="text22">Escape</span>
                    <div class="tools">
                    <label for="image22" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                    <input id="image22" type="file" style="display: none;" accept="image/*"  name="image22">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck22').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image22').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck22').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text22').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck23">
                      <label for="todoCheck23"></label>
                    </div>
                     <span class="text" id="text23">Vidrio trasero</span>
                    <div class="tools">
                   <label for="image23" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                   <input id="image23" type="file" style="display: none;" accept="image/*"  name="image23">
                    </div>
                  </li>                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck23').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image23').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck23').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text23').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment4"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                    <div class="tab-pane fade" id="vert-tabs-superior" role="tabpanel" aria-labelledby="vert-tabs-superior-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck24">
                      <label for="todoCheck24"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text24">Techo</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                    <label for="image24" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image24" type="file" style="display: none;" accept="image/*"  name="image24">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck24').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image24').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck24').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text24').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck25">
                      <label for="todoCheck25"></label>
                    </div>
                     <span class="text" id="text25">Antena</span>
                    <div class="tools">
                   <label for="image25" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                   <input id="image25" type="file" style="display: none;" accept="image/*"  name="image25">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck25').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image25').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck25').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text25').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment5"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->      
                    <div class="tab-pane fade" id="vert-tabs-inferior" role="tabpanel" aria-labelledby="vert-tabs-inferior-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck26">
                      <label for="todoCheck26"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text26">Chasis</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                  <label for="image26" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                  <input id="image26" type="file" style="display: none;" accept="image/*"  name="image26">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck26').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image26').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck26').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text26').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck27">
                      <label for="todoCheck27"></label>
                    </div>
                     <span class="text" id="text27">Suspensión</span>
                    <div class="tools">
                    <label for="image27" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image27" type="file" style="display: none;" accept="image/*"  name="image27">
                    </div>
                  </li>                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck27').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image27').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck27').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text27').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck28">
                      <label for="todoCheck28"></label>
                    </div>
                     <span class="text" id="text28">Amortiguador</span>
                    <div class="tools">
                    <label for="image28" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image28" type="file" style="display: none;" accept="image/*"  name="image28">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck28').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image28').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck28').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text28').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                  <div class="icheck-primary d-inline ml-2">
                  <input type="checkbox" id="todoCheck29"> <!-- Checkbox habilitado para envío -->
                  <label for="todoCheck29"></label>
                  </div>
                  <span class="text" id="text29">Otros</span>
                  <div class="tools">
                  <label for="image29" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                  <input id="image29" type="file" style="display: none;" accept="image/*"  name="image29">
                  </div>
                  </li>

<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck29').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image29').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck29').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text29').style.textDecoration = 'line-through';
    }
  });
</script>
                  
                  
                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment6"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /.card -->
  
 </div> 
  <div class="col-md-12 col-12">
<?php if (is_null($user->firma) || $user->firma === ''):?>

   <div  class="card-header col-md-12 col-12 my-2" style="background-color:gray;">
<i class="fa fa-edit"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Firma del Cliente"; break;
 case 'EN': echo "Client Signature"; break;
}
?>:
</div> 
    <div class="contenedor">

    <div class="row">
      <div class="col-md-12">
        <canvas id="draw-canvas" width="340" height="200">
          No tienes un buen navegador.
        </canvas>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
          
       
        
        <input  type="button" class="button btn-danger" id="draw-clearBtn" value="<?php 
switch (Core::$user->language){
 case 'ES': echo "Borrar Firma"; break;
 case 'EN': echo "Delete Signature"; break;
}
?>"></input>
     


            <label>Color</label>
            <input style="background-color:#333;" type="color" id="color">
            <input style="background-color:#333;" hidden type="range" id="puntero" min="1" default="1" max="5" width="10%">


      </div>

    </div>

  
    <div hidden class="row">
      <div class="col-md-12">
        <textarea style="background-color:#333;"  id="draw-dataUrl" class="form-control" name="base64" rows="5"></textarea>
      </div>
    </div>
  
  
  </div>
  
 <?php else:?>
 Firma registrada via WhatsApp
      
 <?php endif;?>
                       <input style="background-color: #333; color: white;" type="hidden" name="user_id" value="<?php echo $_GET['id'];?>">
                    <div class="row">
                   
                <div class="col-md-12 col-12 my-2">

                   <button type="submit" id="draw-submitBtn" class="btn btn-success btn-block btn-sm "><i class="fa fa-check"></i> Entregar</button>
                 
                </div>
                </div>
    </div>    
  </div>

                      </div>
                    </div>
                  </div>

              </div>
              
              
              
<style>

section{
    flex:1;
}



.button {
    background: #3071a9;
    box-shadow: inset 0 -3px 0 rgba(0,0,0,.3);
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 5px;
    margin: 0 15px;
    text-decoration: none;
    color: white;
}

.button:active {
    transform: scale(0.9);
}

.contenedor {
    width: 100%
    margin: 5px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

input[type=range] {
  -webkit-appearance: none;
  margin: 18px 0;

}
input[type=range]:focus {
  outline: none;
}
input[type=range]::-webkit-slider-runnable-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-webkit-slider-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
  -webkit-appearance: none;
  margin-top: -14px;
}
input[type=range]:focus::-webkit-slider-runnable-track {
  background: #367ebd;
}
input[type=range]::-moz-range-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-moz-range-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]::-ms-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  background: transparent;
  border-color: transparent;
  border-width: 16px 0;
  color: transparent;
}
input[type=range]::-ms-fill-lower {
  background: #2a6495;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-fill-upper {
  background: #3071a9;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]:focus::-ms-fill-lower {
  background: #3071a9;
}
input[type=range]:focus::-ms-fill-upper {
  background: #367ebd;
}
</style>
<script>
/*
    El siguiente codigo en JS Contiene mucho codigo
    de las siguietes 3 fuentes:
    https://stipaltamar.github.io/dibujoCanvas/
    https://developer.mozilla.org/samples/domref/touchevents.html - https://developer.mozilla.org/es/docs/DOM/Touch_events
    http://bencentra.com/canvas/signature/signature.html - https://bencentra.com/code/2014/12/05/html5-canvas-touch-events.html
*/

(function() { // Comenzamos una funcion auto-ejecutable

  // Obtenenemos un intervalo regular(Tiempo) en la pamtalla
  window.requestAnimFrame = (function (callback) {
    return window.requestAnimationFrame ||
          window.webkitRequestAnimationFrame ||
          window.mozRequestAnimationFrame ||
          window.oRequestAnimationFrame ||
          window.msRequestAnimaitonFrame ||
          function (callback) {
            window.setTimeout(callback, 1000/60);
            // Retrasa la ejecucion de la funcion para mejorar la experiencia
          };
  })();

  // Traemos el canvas mediante el id del elemento html
  var canvas = document.getElementById("draw-canvas");
  var ctx = canvas.getContext("2d");


  // Mandamos llamar a los Elemetos interactivos de la Interfaz HTML
  var drawText = document.getElementById("draw-dataUrl");
  var drawImage = document.getElementById("draw-image");
  var clearBtn = document.getElementById("draw-clearBtn");
  var submitBtn = document.getElementById("draw-submitBtn");
  clearBtn.addEventListener("click", function (e) {
    // Definimos que pasa cuando el boton draw-clearBtn es pulsado
    clearCanvas();
    drawImage.setAttribute("src", "");
  }, false);
    // Definimos que pasa cuando el boton draw-submitBtn es pulsado
  submitBtn.addEventListener("click", function (e) {
  var dataUrl = canvas.toDataURL();
  drawText.innerHTML = dataUrl;
  drawImage.setAttribute("src", dataUrl);
   }, false);

  // Activamos MouseEvent para nuestra pagina
  var drawing = false;
  var mousePos = { x:0, y:0 };
  var lastPos = mousePos;
  canvas.addEventListener("mousedown", function (e)
  {
    /*
      Mas alla de solo llamar a una funcion, usamos function (e){...}
      para mas versatilidad cuando ocurre un evento
    */
    var tint = document.getElementById("color");
    var punta = document.getElementById("puntero");
    console.log(e);
    drawing = true;
    lastPos = getMousePos(canvas, e);
  }, false);
  canvas.addEventListener("mouseup", function (e)
  {
    drawing = false;
  }, false);
  canvas.addEventListener("mousemove", function (e)
  {
    mousePos = getMousePos(canvas, e);
  }, false);

  // Activamos touchEvent para nuestra pagina
  canvas.addEventListener("touchstart", function (e) {
    mousePos = getTouchPos(canvas, e);
    console.log(mousePos);
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var touch = e.touches[0];
    var mouseEvent = new MouseEvent("mousedown", {
      clientX: touch.clientX,
      clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchend", function (e) {
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var mouseEvent = new MouseEvent("mouseup", {});
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchleave", function (e) {
    // Realiza el mismo proceso que touchend en caso de que el dedo se deslice fuera del canvas
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var mouseEvent = new MouseEvent("mouseup", {});
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchmove", function (e) {
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var touch = e.touches[0];
    var mouseEvent = new MouseEvent("mousemove", {
      clientX: touch.clientX,
      clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
  }, false);

  // Get the position of the mouse relative to the canvas
  function getMousePos(canvasDom, mouseEvent) {
    var rect = canvasDom.getBoundingClientRect();
    /*
      Devuelve el tamaño de un elemento y su posición relativa respecto
      a la ventana de visualización (viewport).
    */
    return {
      x: mouseEvent.clientX - rect.left,
      y: mouseEvent.clientY - rect.top
    };
  }

  // Get the position of a touch relative to the canvas
  function getTouchPos(canvasDom, touchEvent) {
    var rect = canvasDom.getBoundingClientRect();
    console.log(touchEvent);
    /*
      Devuelve el tamaño de un elemento y su posición relativa respecto
      a la ventana de visualización (viewport).
    */
    return {
      x: touchEvent.touches[0].clientX - rect.left, // Popiedad de todo evento Touch
      y: touchEvent.touches[0].clientY - rect.top
    };
  }

  // Draw to the canvas
  function renderCanvas() {
    if (drawing) {
      var tint = document.getElementById("color");
      var punta = document.getElementById("puntero");
      ctx.strokeStyle = tint.value;
      ctx.beginPath();
      ctx.moveTo(lastPos.x, lastPos.y);
      ctx.lineTo(mousePos.x, mousePos.y);
      console.log(punta.value);
      ctx.lineWidth = punta.value;
      ctx.stroke();
      ctx.closePath();
      lastPos = mousePos;
    }
  }

  function clearCanvas() {
    canvas.width = canvas.width;
  }

  // Allow for animation
  (function drawLoop () {
    requestAnimFrame(drawLoop);
    renderCanvas();
  })();

})();    
</script>

<?php if(StockData::getPrincipal()->method=="1"): ?>
<style>

#draw-canvas {
  border: 2px dotted #CCCCCC;
  border-radius: 5px;
  cursor: crosshair;
  background-image: url("CF-SYSTEMS/storage/configuration/fxrma.png");
  background-size: 100% 100%;
}

#draw-dataUrl {
  width: 100%;
}

</style>

<?php elseif(StockData::getPrincipal()->method=="2"): ?>
<style>

#draw-canvas {
  border: 2px dotted #CCCCCC;
  border-radius: 5px;
  cursor: crosshair;
  background-image: url("CF-SYSTEMS/storage/configuration/furma.png");
  background-size: 100% 100%;
}

#draw-dataUrl {
  width: 100%;
}

</style>

<?php endif;?>

            </div>

            </div>
            </div>
</div>

</div>
</div>
</div>
  </div>
</div>

  
  </div>
                      </div>
                    </div>
                 

</form>
             



            </div>

            </div>

            </div>
            </div>
</div>

</div>
</div>
</div>
  </div>
</div>

</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="new"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fas fa-map-marked-alt'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Nueva Reserva"; break;
 case 'EN': echo "New Reservation"; break;
}
?></h1>
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
        
 <?php if(StockData::getPrincipal()->update=="1"):?>
 <div class="row">
            
          <div class="col-12 col-sm-4 col-md-4 my-2">
          <a  href="./?view=booking&opt=cluster" class="floating-btn message-btn" style="background-color: #4DBE04;">
     <div class="icon-container">
    <i class="fa fa-tasks"></i>
    
    <?php if($total_registros > 0):?>
    <span class="notification-badge badge-danger"><?php echo $total_registros;?></span>
    <?php endif;?>
    </div>
    <span class="message-text"> RESERVAS GRUPALES</span>
  </a>
            <!-- /.info-box -->
          </div>
          
          <!-- /.col -->
          <div class="col-12 col-sm-4 col-md-4 my-2">
            <a href="./?view=booking&opt=confirmation" class="floating-btn message-btn" style="background-color: orange;">
  <div class="icon-container">
   <i class="fa fa-edit"></i>
    
    <?php if($conf_tot > 0):?>
    <span class="notification-badge badge-danger"><?php echo $conf_tot;?></span>
    <?php endif;?>
  </div>
  <span class="message-text">SIN FIRMAR</span>
</a>
          </div>
          <!-- /.col -->

          
          <div class="col-12 col-sm-4 col-md-4 my-2">
             <a href="./?view=booking&opt=earring" class="floating-btn message-btn" style="background-color: #C70039;">
      <div class="icon-container">
    <i class="fa fa-globe"> </i>
    
    <?php if($wt_tot > 0):?>
    <span class="notification-badge badge-danger"><?php echo $wt_tot;?></span>
    <?php endif;?>
    </div>
    <span class="message-text"> POR LA PAGINA WEB</span> 
  </a>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
<?php else:?>
<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>
<?php endif;?>         
        
         
        

            <div class="col-md-12">
                
                <!-- Profile Image -->
            <div class="card card-secondary card-outline" style="background-color: #222;">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="CF-SYSTEMS/storage/profiles/user.png"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?php 
switch (Core::$user->language){
 case 'ES': echo "Datos de la Reserva"; break;
 case 'EN': echo "Reservation Data"; break;
}
?></h3>
  <div class="card-header p-0 pt-1">
  <ul class="nav nav-tabs row" id="stepTabs" role="tablist">
    <li class="nav-item col-6 col-md-3">
      <a class="nav-link active disabled-tab text-center" href="#step1" role="tab">Datos del Cliente</a>
    </li>
    <li class="nav-item col-6 col-md-3">
      <a class="nav-link disabled-tab text-center" href="#step2" role="tab">Fechas & Lugar</a>
    </li>
    <li class="nav-item col-6 col-md-3">
      <a class="nav-link disabled-tab text-center" href="#step3" role="tab">Elegir Vehículo</a>
    </li>
    <li class="nav-item col-6 col-md-3">
      <a class="nav-link disabled-tab text-center" href="#step4" role="tab">Facturación</a>
    </li>
  </ul>
</div>

<style>
  .disabled-tab {
    pointer-events: none;
    cursor: default;
  }
</style>

  
<form action="./?action=booking&opt=add" method="post" id="delivery" role="form" enctype="multipart/form-data">
                
  <div class="card-body">
    <div class="tab-content" id="stepTabContent">
    
    <div class="tab-pane fade show active" id="step1" role="tabpanel">
  <input type="hidden" name="nuevo_cliente_activo" id="nuevo_cliente_activo" value="0">

  <div class="row">
    <div class="col-md-4 col-12">
      <label class="col-md-12 col-12 control-label">
        <?php echo Core::$user->language == 'EN' ? 'CUSTOMER/ COMPANY' : 'CLIENTE/ EMPRESA'; ?>
      </label>
      <select style="background-color: #333; color: white;" name="person_id" id="person_id" class="form-control select2" required>
        <option value="">-- <?php echo Core::$user->language == 'EN' ? 'CHOOSE' : 'ELEGIR'; ?> --</option>
        <?php foreach(PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id) as $client): ?>
          <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-4 col-12">
      <label class="col-md-12 col-12 control-label">
        <?php echo Core::$user->language == 'EN' ? 'OPTIONAL DRIVER' : 'CONDUCTOR OPCIONAL'; ?>
      </label>
      <select style="background-color: #333; color: white;" name="person2_id" id="person2_id" class="form-control select2">
        <option value="">-- <?php echo Core::$user->language == 'EN' ? 'CHOOSE' : 'ELEGIR'; ?> --</option>
        <?php foreach(PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id) as $client): ?>
          <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-4 col-12">
      <label class="col-md-12 col-12 control-label">&nbsp;</label>
      <button type="button" id="btn_toggle_cliente" class="btn btn-success btn-block">
        CREAR NUEVO
      </button>
    </div>
  </div>

  <!-- Formulario Nuevo Cliente -->
  <div id="form_nuevo_cliente" class="mt-4" style="display:none;">
    <div class="row">
    
        <div class="col-md-4 col-12">
<?php if(StockData::getPrincipal()->method==1):?>
      
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Provincia/Estado"; break;
 case 'EN': echo "Province/State"; break;
}
?></label>

<?php endif; if(StockData::getPrincipal()->method==2):?>

        <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Estado"; break;
 case 'EN': echo "State"; break;
}
?></label>
        
        <?php endif; ?>

      <select style="background-color:#333;"  name="location"  class="form-control select2">
      <option selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>

<?php if(StockData::getPrincipal()->update==1):

foreach(StatesData::getAllWithCountry() as $state): ?>
        <option value="<?php echo $state->id; ?>">
            <?php echo $state->state_name . ' (' . $state->country_name . ')'; ?>
        </option>
        
<?php endforeach; else:

foreach(LocationData::getAll() as $loc):?>
      <option value="<?php echo $loc->id;?>"><?php echo $loc->name." (".$loc->timezone.")";?></option>
      
<?php endforeach; endif;?>

      </select>
    </div>
     
      
    <div class="col-md-4 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Tipo"; break;
 case 'EN': echo "Type"; break;
}
?></label>
      <select style="background-color:#333;" name="type" class="form-control select2" id="type_person">
      <option value="0"><?php 
switch (Core::$user->language){
 case 'ES': echo "PERSONA FISICA"; break;
 case 'EN': echo "NATURAL PERSON"; break;
}
?></option>
      <option value="1"><?php 
switch (Core::$user->language){
 case 'ES': echo "EMPRESA"; break;
 case 'EN': echo "COMPANY"; break;
}
?></option>
      </select>
    </div>
   
    
    <div class="col-md-4 col-12" id="rnc_id">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "RNC"; break;
 case 'EN': echo "NIE"; break;
}
?></label>
      <input style="background-color:#333;" type="text" name="rnc" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "RNC Empresa"; break;
 case 'EN': echo "NIE Company"; break;
}
?>">
    </div>

  

    <div class="col-md-4 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Nombre Completo"; break;
 case 'EN': echo "Full Name"; break;
}
?></label>
      <input style="background-color:#333;" type="text" autofocus name="name" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Nombre Completo"; break;
 case 'EN': echo "Full Name"; break;
}
?>">
    </div>
    
<div class="col-md-3 col-12">
  <label for="inputEmail1" class="col-md-12 col-12 control-label">
    <?php echo Core::$user->language == 'ES' ? 'Genero' : 'Gender'; ?>
  </label>
  <select style="background-color:#333;" name="gender"  class="form-control">
   
      --- <?php echo Core::$user->language == 'ES' ? 'ELEGIR' : 'CHOOSE'; ?> ---
    </option>
    <?php
    $lang = Core::$user->language;
    $current = $user->gender ?? '';
    $options = [
      'M' => $lang == 'ES' ? 'Hombre' : 'Man',
      'F' => $lang == 'ES' ? 'Mujer' : 'Woman'
    ];
    foreach ($options as $val => $label) {
      echo "<option value=\"$val\">$label</option>";
    }
    ?>
  </select>
</div>

    
   <div class="col-md-3 col-12">
  <label for="inputEmail1" class="col-md-12 col-12 control-label">
    <?php echo Core::$user->language == 'ES' ? 'Idioma' : 'Language'; ?>
  </label>
 <select style="background-color:#333;" name="language"  class="form-control">
 
  <?php
  $lang = Core::$user->language;
  $options = [
    'ES' => ['ES' => 'Español', 'EN' => 'Inglés'],
    'EN' => ['EN' => 'English', 'ES' => 'Spanish']
  ];
  foreach ($options[$lang] as $val => $label) {
    echo "<option value=\"$val\">$label</option>";
  }
  ?>
</select>


</div>

    

  <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Numero de Cedula"; break;
 case 'EN': echo "ID Number"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="no" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Numero de Cedula"; break;
 case 'EN': echo "ID Number"; break;
}
?>">
    </div>

    <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Licencia de conducir"; break;
 case 'EN': echo "Driver license"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="license" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Licencia de conducir"; break;
 case 'EN': echo "Driver license"; break;
}
?>">
    </div>


    <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Pasaporte"; break;
 case 'EN': echo "Passport"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="passport" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Pasaporte"; break;
 case 'EN': echo "Passport"; break;
}
?>">
    </div>


    <div class="col-md-4 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Nacionalidad"; break;
 case 'EN': echo "Nationality"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="nationality" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Nacionalidad"; break;
 case 'EN': echo "Nationality"; break;
}
?>">
    </div>


<div class="col-md-2 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Estado Civil"; break;
 case 'EN': echo "Marital status"; break;
}
?></label>
      <select style="background-color:#333;"  name="estado"   class="form-control">
      <option value="<?php 
switch (Core::$user->language){
 case 'ES': echo "Soltero"; break;
 case 'EN': echo "Single"; break;
}
?>"><?php 
switch (Core::$user->language){
 case 'ES': echo "Soltero"; break;
 case 'EN': echo "Single"; break;
}
?></option>
      <option value="<?php 
switch (Core::$user->language){
 case 'ES': echo "Casado"; break;
 case 'EN': echo "Married"; break;
}
?>"><?php 
switch (Core::$user->language){
 case 'ES': echo "Casado"; break;
 case 'EN': echo "Married"; break;
}
?></option>
      <option value="<?php 
switch (Core::$user->language){
 case 'ES': echo "Union Libre"; break;
 case 'EN': echo "Free Union"; break;
}
?>"><?php 
switch (Core::$user->language){
 case 'ES': echo "Union Libre"; break;
 case 'EN': echo "Free Union"; break;
}
?></option>
      <option value="Viudo"><?php 
switch (Core::$user->language){
 case 'ES': echo "Viudo"; break;
 case 'EN': echo "Widower"; break;
}
?></option>
      </select>
    </div>
    
         
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Cumpleaño"; break;
 case 'EN': echo "Birthday"; break;
}
?></label>
<input type="date" style="background-color:#333;"  class="form-control"  name="birthday">
    </div>
    
<div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion Estadia"; break;
 case 'EN': echo "Address Stay"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="address" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion Estadia"; break;
 case 'EN': echo "Address Stay"; break;
}
?>">
    </div>
    
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Telefono Estadia"; break;
 case 'EN': echo "Stay Telephone"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="phone"  autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Telefono Estadia"; break;
 case 'EN': echo "Stay Telephone"; break;
}
?>">
    </div>
    
        <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Correo"; break;
 case 'EN': echo "Email"; break;
}
?></label>
      <input style="background-color:#333;" type="email"  name="email" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Correo"; break;
 case 'EN': echo "Email"; break;
}
?>">
    </div>
    

<div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion Extranjera"; break;
 case 'EN': echo "Foreign Address"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="address2" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion Extranjera"; break;
 case 'EN': echo "Foreign Address"; break;
}
?>">
    </div>
    
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Telefono Extranjera"; break;
 case 'EN': echo "Foreign Telephone"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="phone2" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Telefono Extranjera"; break;
 case 'EN': echo "Foreign Telephone"; break;
}
?>">
    </div>
    
    
<div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Referencia (Conocido)"; break;
 case 'EN': echo "Reference (Known)"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="reference" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Referencia (Conocido)"; break;
 case 'EN': echo "Reference (Known)"; break;
}
?>">
    </div>


    <div class="col-md-6 col-12 my-3">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Cedula"; break;
 case 'EN': echo "Photo ID"; break;
}
?></label>
<input style="background-color:#333;" type="file"  name="invoice_file">
    </div>
    

 <div class="col-md-3 col-12 my-3">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Pasaporte"; break;
 case 'EN': echo "Passport Photo"; break;
}
?></label>
<input style="background-color:#333;" type="file"  name="passport_file">
    </div>
    
 <div class="col-md-3 col-12 my-3">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
<input style="background-color:#333;" type="date" class="form-control"  name="passport_date">
    </div>
    
     <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Licencia"; break;
 case 'EN': echo "Photo License"; break;
}
?></label>
<input style="background-color:#333;" type="file"  name="license_file">
    </div>
    
     
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
<input style="background-color:#333;" type="date" class="form-control"  name="license_date">
    </div>
    
     <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Residencia"; break;
 case 'EN': echo "Photo Residence"; break;
}
?></label>
    <input style="background-color:#333;" type="file"  name="home_file">
    </div>

 
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
<input style="background-color:#333;" type="date" class="form-control"  name="home_date">
    </div>
    
    
<script>

document.getElementById("rnc_id").style.display = "none";

    
$('#type_person').on('change', () => {
    var getSelectValue = $('#type_person').val();
  
   if(getSelectValue=="1") {
document.getElementById("rnc_id").style.display = "inline-block";
   }
   else if(getSelectValue=="0") {
document.getElementById("rnc_id").style.display = "none";
   }
  
});


</script>

    </div>
  </div>

  <!-- Botón Continuar -->

</div>

<script>
$(document).ready(function() {
  const $nuevoClienteForm = $('#form_nuevo_cliente');
  const $btnCrear = $('#btn_toggle_cliente');
  const $clienteSelect = $('#person_id');
  const $continuarWrap = $('#btn_continuar_wrap');
  const $nuevoClienteActivo = $('#nuevo_cliente_activo');

  function validarContinuar() {
    const activo = $nuevoClienteActivo.val() === '1';

    if (activo) {
      // Crear nuevo cliente: se oculta el selector
      $clienteSelect.prop('disabled', true).prop('required', false);

      // NO marcar inputs como required
      let hayAlgoLleno = false;
      $nuevoClienteForm.find('input').each(function() {
        if ($(this).val().trim() !== '') {
          hayAlgoLleno = true;
        }
      });

      // Mostrar continuar solo si hay al menos un campo lleno
      $continuarWrap.toggle(hayAlgoLleno);
    } else {
      // Usar selector: se activa
      $clienteSelect.prop('disabled', false).prop('required', true);

      // Nunca required en los inputs del formulario
      $nuevoClienteForm.find('input').removeAttr('required');

      // Mostrar continuar si hay cliente seleccionado
      $continuarWrap.toggle($clienteSelect.val() !== '');
    }
  }

  $btnCrear.click(function() {
    $nuevoClienteForm.slideToggle(300, function() {
      const visible = $nuevoClienteForm.is(':visible');
      $nuevoClienteActivo.val(visible ? '1' : '0');
      validarContinuar();
    });

    if ($btnCrear.text().toUpperCase().includes('CREAR')) {
      $btnCrear.html('<i class="fa fa-times"></i> Cancelar');
    } else {
      $btnCrear.html('<i class="fa fa-plus"></i> Crear nuevo');
    }
  });

  $clienteSelect.on('change', validarContinuar);
  $nuevoClienteForm.on('input change', 'input', validarContinuar);
});
</script>


<!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
      <div class="tab-pane fade" id="step2" role="tabpanel">
          <div class="row">
                        
                   <div hidden class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Tipo Contrato</label>
    <select style="background-color: #333; color: white;" name="type_id"  class="form-control select2" id="type_id" onchange="showInp2()">
      <option value="1">ENTRE FECHAS</option>
       <option value="2">ABIERTO</option>
      </select>
    </div>
 <div  class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Fecha a Entregar"; break;
 case 'EN': echo "Date to be delivered"; break;
}
?></label>
      <input style="background-color: #333; color: white;" type="datetime-local"  required name="start_at" id="start_at" class="form-control " >
    </div>

     <div id="end_at1" class="col-md-4 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Fecha a Recibir"; break;
 case 'EN': echo "Date to Receive"; break;
}
?></label>
      <input style="background-color: #333; color: white;" type="datetime-local" required  name="end_at" id="end_at"  class="form-control"> 
        </div>
        
         <div id="end_at2" class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Dia de Pago</label>
      <select style="background-color: #333; color: white;" name="payment_day" id="payment_day"  class="form-control select2">
      <option value="1">DIARIO</option>
      <option value="7">SEMANAL</option>
      <option value="15">QUINCENAL</option>
      <option value="30">MENSUAL</option>
      </select>
      <select style="background-color: #333; color: white;" hidden name="selectdate" id="selectdate"  class="form-control"></select>
        </div>

  
  <div class="col-md-6 col-12">
  <label class="col-md-12 col-12 control-label">
    <?php 
      switch (Core::$user->language){
        case 'ES': echo "Lugar a Entregar"; break;
        case 'EN': echo "Place to Deliver"; break;
      }
    ?>
  </label>

  <div class="input-group">
    <!-- SELECT2 visible al inicio -->
    <select id="place_start" class="form-control select2" name="place_start" style="background-color:#333;">
      <option value="" selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>
      <?php foreach(PlaceData::getAll() as $place): ?>
        <option value="<?php echo $place->name; ?>"><?php echo $place->name; ?></option>
      <?php endforeach; ?>
    </select>

    <!-- INPUT oculto al inicio -->
    <input type="text" id="place_start2" name="place_start2" class="form-control" placeholder="Escribe aquí" style="background-color:#333; display:none;">
  </div>

  <!-- BOTÓN DE CAMBIO -->
  <small id="toggleplace_start" style="color:orange; font-size:13px; margin-top:5px; display:block; cursor:pointer;">
    <?php 
switch (Core::$user->language){
 case 'ES': echo "CAMBIAR ENTRE ESCRIBIR Y SELECCIONAR"; break;
 case 'EN': echo "SWITCH BETWEEN WRITE AND SELECT"; break;
}
?>
  </small>
</div>

<script>
  $(document).ready(function () {
    let modoManual = false;

    $('#toggleplace_start').click(function () {
      if (!modoManual) {
        // Ocultar SELECT2 y mostrar INPUT
        $('#place_start').select2('destroy').hide();
        $('#place_start2').show();
        $('#place_start').val('');
        modoManual = true;
      } else {
        // Volver a mostrar SELECT2 y ocultar INPUT
        $('#place_start2').hide();
        $('#place_start2').val('');
        $('#place_start').show().select2();
        modoManual = false;
      }
    });
  });
</script>



    <div class="col-md-6 col-12">
  <label class="col-md-12 col-12 control-label">
    <?php 
      switch (Core::$user->language){
        case 'ES': echo "Lugar a Recibir"; break;
        case 'EN': echo "Place to Receive"; break;
      }
    ?>
  </label>

  <div class="input-group">
    <!-- SELECT2 visible al inicio -->
    <select id="place_end" class="form-control select2" name="place_end" style="background-color:#333;">
      <option value="" selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>
      <?php foreach(PlaceData::getAll() as $place): ?>
        <option value="<?php echo $place->name; ?>"><?php echo $place->name; ?></option>
      <?php endforeach; ?>
    </select>

    <!-- INPUT oculto al inicio -->
    <input type="text" id="place_end2" name="place_end2" class="form-control" placeholder="Escribe aquí" style="background-color:#333; display:none;">
  </div>

  <!-- BOTÓN DE CAMBIO -->
  <small id="toggleplace_end" style="color:orange; font-size:13px; margin-top:5px; display:block; cursor:pointer;">
    <?php 
switch (Core::$user->language){
 case 'ES': echo "CAMBIAR ENTRE ESCRIBIR Y SELECCIONAR"; break;
 case 'EN': echo "SWITCH BETWEEN WRITE AND SELECT"; break;
}
?>
  </small>
</div>


<script>
  $(document).ready(function () {
    let modoManual2 = false;

    $('#toggleplace_end').click(function () {
      if (!modoManual2) {
        // Ocultar SELECT2 y mostrar INPUT
        $('#place_end').select2('destroy').hide();
        $('#place_end2').show();
        $('#place_end').val('');
        modoManual2 = true;
      } else {
        // Volver a mostrar SELECT2 y ocultar INPUT
        $('#place_end2').hide();
        $('#place_end2').val('');
        $('#place_end').show().select2();
        modoManual2 = false;
      }
    });
  });
</script>

      </div>
      </div>
      
<!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
      <div class="tab-pane fade" id="step3" role="tabpanel">
          <div class="row">
       
       <div class="col-md-2 col-12" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Modo"; break;
 case 'EN': echo "Mode"; break;
}
?></label>
    <select style="background-color: #333; color: white;" name="method" class="form-control" id="method" onchange="showMethod()">
      <option value="1"><?php 
switch (Core::$user->language){
 case 'ES': echo "Disponible"; break;
 case 'EN': echo "Available"; break;
}
?></option>
      <option value="2"><?php 
switch (Core::$user->language){
 case 'ES': echo "Rejuego"; break;
 case 'EN': echo "Replay"; break;
}
?></option>


<?php if(StockData::getPrincipal()->update=="1"):?>
<option value="3"><?php 
switch (Core::$user->language){
 case 'ES': echo "Externo"; break;
 case 'EN': echo "external"; break;
}
?></option>
<?php endif;?>
      </select>
    </div>

    <div class="col-md-3 col-12" hidden>
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Ubicacion</label>
    
    <select style="background-color: #333; color: white;" name="location" class="form-control" id="location">
    <?php foreach(LocationData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<select hidden style="background-color: #333; color: white;" name="stock_id" id="select2lista"  class="form-control" onchange="showInp()"></select>
  
  
  <div class="col-md-3 col-12" id="stock_id2" style="display: none">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Suplidor"; break;
 case 'EN': echo "Supplier"; break;
}
?></label>
      <input style="background-color: #333; color: white;" type="text" name="stock_id2"  class="form-control" placeholder="Nombre del Rent A Car"> 
    </div>
  
  
  <div class="col-md-2 col-12" id="rpayment" style="display: none">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Precio/Dia</label>
      <input style="background-color: #333; color: white;" type="number" value="0" name="rpayment"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" min="0" step="0.01"> 
    </div>
    
    
    <div class="col-md-5 col-12"  id="cars1">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label" id="type_cars1"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></label>
    <label for="inputEmail1" class="col-md-12 col-12 control-label" id="type_cars3"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo (Rejuego)"; break;
 case 'EN': echo "Vehicle (Replay)"; break;
}
?></label>
    <select style="background-color: #333; color: white;" name="car_id"  id="cars" class="form-control select2"></select>
    </div>
  </div>
  
    <script>
        $(document).ready(function () {
            $('#cars').select2({
                templateResult: function (data) {
                    if (!data.id) {
                        return data.text;
                    }
                    
                    // Recuperar la descripción desde el atributo `data-description`
                    const description = $(data.element).data('description');

                    const $template = $(
                        `<div>
                            <strong>${data.text}</strong>
                            <div style="font-size: 12px; color: orange;">${description || ''}</div>
                        </div>`
                    );
                    return $template;
                },
                templateSelection: function (data) {
                    return data.text; // Mostrar solo el texto seleccionado
                }
            });
        });
    </script>
    
   <div class="col-md-5 col-12" id="cars3">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo (Solicitado)"; break;
 case 'EN': echo "Vehicle (Requested)"; break;
}
?></label>
    <select style="background-color: #333; color: white;" name="car2_id" id="cars2" class="form-control select2">
    <option value="0">--<?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?>--</option>
    <?php foreach(CarsData::getAllBySQL("where status<>4") as $cars): $provider = SuppliersData::getById($cars->provider_id);?>
      <option value="<?php echo $cars->id;?>" data-description="<?php echo strtoupper($cars->getStock()->name);?>"><?php echo $cars->getBrand()->name." ".$cars->name." ".$cars->year." F: [".$cars->token."].";?></option>
    <?php endforeach;?>
      </select>
    </div>
  </div>
 
 
 

    <script>
        $(document).ready(function () {
            $('#cars2').select2({
                templateResult: function (data) {
                    if (!data.id) {
                        return data.text;
                    }
                    
                    // Recuperar la descripción desde el atributo `data-description`
                    const description = $(data.element).data('description');

                    const $template = $(
                        `<div>
                            <strong>${data.text}</strong>
                            <div style="font-size: 12px; color: orange;">${description || ''}</div>
                        </div>`
                    );
                    return $template;
                },
                templateSelection: function (data) {
                    return data.text; // Mostrar solo el texto seleccionado
                }
            });
        });
    </script>
  
 
 
    <select hidden style="background-color: #333; color: white;" name="divisa_id" id="divisa_id" class="form-control">
    <option value="1">DOLAR</option>
    </select>
    
    <div class="col-md-3 col-12" id="cars2_brand">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Marca"; break;
 case 'EN': echo "Brand"; break;
}
?></label>
    <select style="background-color: #333; color: white;"  name="cars2_brand" class="form-control select2" >
      <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
    <?php foreach(BrandData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

     <div class="col-md-2 col-12" id="cars2_name">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Modelo"; break;
 case 'EN': echo "Model"; break;
}
?></label>
      <input style="background-color: #333; color: white;" type="text" name="cars2_name" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>

 
  <div class="col-md-3 col-12"  id="cars2_category">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Categoria"; break;
 case 'EN': echo "Category"; break;
}
?></label>
    <select style="background-color: #333; color: white;" name="cars2_category" class="form-control select2"  >
      <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
    <?php foreach(CategoryData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

     <div class="col-md-1 col-12" id="cars2_year">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Año"; break;
 case 'EN': echo "Year"; break;
}
?> </label>
      <input style="background-color: #333; color: white;" type="text" value="<?php echo date("Y");?>" name="cars2_year" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>

<div class="col-md-3 col-12" id="cars2_plate">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Placa"; break;
 case 'EN': echo "Plate No."; break;
}
?></label>
      <input style="background-color: #333; color: white;" type="text"  name="cars2_plate" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>
  
 <div class="col-md-5 col-12" id="cars2_chassis">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Chasis"; break;
 case 'EN': echo "Chassis No."; break;
}
?></label>
      <input style="background-color: #333; color: white;" type="text"  name="cars2_chassis" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>


<div id="extra" class="col-md-12 col-12"></div>

 <input style="background-color: #333; color: white;" type="hidden" id="unitx1" name="unit_extra1"   class="form-control"> 
  <input style="background-color: #333; color: white;" type="hidden" id="pricex1" name="price_extra1"  class="form-control"> 
  
  
 <input style="background-color: #333; color: white;" type="hidden" id="unitx2" name="unit_extra2"  class="form-control"> 
  <input style="background-color: #333; color: white;" type="hidden" id="pricex2" name="price_extra2"  class="form-control"> 
  
  
  
 <input style="background-color: #333; color: white;" type="hidden" id="unitx3" name="unit_extra3"  class="form-control"> 
  <input style="background-color: #333; color: white;" type="hidden" id="pricex3" name="price_extra3" class="form-control"> 
  
  
 <input style="background-color: #333; color: white;" type="hidden" id="unitx4" name="unit_extra4"  class="form-control"> 
  <input style="background-color: #333; color: white;" type="hidden" id="pricex4" name="price_extra4"  class="form-control"> 
  
      </div>
      </div>
      
<!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
      <div class="tab-pane fade" id="step4" role="tabpanel">
          <div class="row">
     
<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Forma de Pago"; break;
 case 'EN': echo "Method of payment"; break;
}
?></label>
    <select style="background-color: #333; color: white;" name="f_id" required class="form-control select2">
    <?php foreach(FData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

  
   <div class="col-md-3 col-12"> 
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Tipo de Seguro"; break;
 case 'EN': echo "Insurance Type"; break;
}
?></label>
    
      <select style="background-color: #333; color: white;" class="form-control" name="type_sure">
      <?php foreach (SureData::getALL() as $sure): ?>
      <option value="<?php echo $sure->id;?>"><?php echo $sure->name;?></option>
      <?php endforeach ?>
      </select>
</div>
   <div class="col-md-3 col-12">
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Deducible"; break;
 case 'EN': echo "Deductible"; break;
}
?></label>
    
          <input style="background-color: #333; color: white;" type="text" name="sure" class="form-control" value="0"   placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" autocomplete="off" >
</div>
 <div hidden class="col-md-4 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Deposito</label>
     <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
      <input style="background-color: #333; color: white;" type="text" name="deposit" value="0" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" >
    </div>
  </div>

   <div class="col-md-3 col-12">
    <div class="input-group" >
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Combustible"; break;
 case 'EN': echo "Fuel"; break;
}
?></label>

    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-tint"></i></span>
     <select style="background-color:#333;" name="fuel"  class="form-control">
      <option value="R"><?php 
      switch (Core::$user->language){
        case 'ES': echo "Reserva"; break;
        case 'EN': echo "Booking"; break;
      }
    ?></option>
      <option value="1/4">1/4</option>
      <option value="1/2"><?php 
      switch (Core::$user->language){
        case 'ES': echo "Medio"; break;
        case 'EN': echo "Half"; break;
      }
    ?></option>
      <option value="3/4">3/4</option>
      <option value="F">Full</option>
     </select>
    </div>
  </div>
  
  
        <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Dias de Renta"; break;
 case 'EN': echo "Income Days"; break;
}
?></label>
    <input style="background-color: #333; color: white;" id="dias" name="day"  class="form-control">
    </div>
</div>


  <?php
// ===============================
// Consultamos la tasa de USD->DOP
// ===============================
$tasa = null;
$url  = "https://open.er-api.com/v6/latest/USD"; // API pública y estable

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode == 200 && $response !== false) {
    $data = json_decode($response, true);
    if (isset($data["rates"]["DOP"])) {
        $tasa = $data["rates"]["DOP"];
    }
}
?>


  <!-- Pesos -->
  <div class="col-md-3 col-12">
    <label for="purchase_price" class="col-md-12 col-12 control-label">
      <?php 
switch (Core::$user->language){
 case 'ES': echo "Precio por Dia (USD<i class='fa fa-dollar-sign'></i>)"; break;
 case 'EN': echo "Price per day (USD<i class='fa fa-dollar-sign'></i>)"; break;
}
?> <span class="text-danger">*</span>
    </label>
    <div class="input-group">
      <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
    <input style="background-color: #333;" type="number"  name="price2" id="tariff2"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>"  min="0" step="0.01">
    </div>
  </div>
  


  <!-- USD -->
  <div class="col-md-3 col-12">
    <label for="usd_price" class="col-md-12 col-12 control-label">
     <?php 
switch (Core::$user->language){
 case 'ES': echo "Equivalente en (RD<i class='fa fa-dollar-sign'></i>)"; break;
 case 'EN': echo "Equivalent in (RD<i class='fa fa-dollar-sign'></i>)"; break;
}
?>
    </label>
    <div class="input-group">
      <span class="input-group-text">RD <i class='fa fa-dollar-sign'></i></span>
      <input type="text" id="usd_price" name="usd_price" class="form-control" readonly placeholder="0.00 USD">
    </div>
  </div>

  <!-- Tasa -->
  <div class="col-md-3 col-12">
    <label for="tasa_dolar" class="col-md-12 col-12 control-label">
      <?php 
switch (Core::$user->language){
 case 'ES': echo "Tasa Actual del Dólar"; break;
 case 'EN': echo "Current Dollar Rate"; break;
}
?>
    </label>
    <div class="input-group">
      <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
      <input type="text" name="tasa_dolar" id="tasa_dolar" class="form-control" readonly 
             value="<?php echo $tasa ? number_format($tasa, 2) . ' DOP/USD' : 'Buscando...'; ?>">
    </div>
    
  </div>


<script>
async function obtenerTasa() {
  try {
    let res = await fetch("https://open.er-api.com/v6/latest/USD");
    let data = await res.json();
    if (data && data.rates && data.rates.DOP) {
      return data.rates.DOP;
    }
  } catch (e) {
    console.error("Error obteniendo tasa:", e);
  }
  return null;
}

async function actualizarTasa() {
  const inputPesos = document.getElementById("tariff2");
  const inputUSD   = document.getElementById("usd_price");
  const inputTasa  = document.getElementById("tasa_dolar");
  const infoFecha  = document.getElementById("ultima_actualizacion");

  let tasa = await obtenerTasa();

  if (tasa) {
    inputTasa.value = tasa.toFixed(2);

    // Mostrar la hora de la última actualización
    infoFecha.innerText = "Última actualización en (Forex): " + new Date().toLocaleString();

    inputPesos.addEventListener("input", () => {
      let pesos = parseFloat(inputPesos.value) || 0;
      let dolares = pesos * tasa;
      inputUSD.value = dolares.toFixed(2);
    });
  } else {
    inputTasa.value = "Buscando...";
    inputUSD.value  = "Esperando tasa...";
    infoFecha.innerText = "Última actualización: fallo en la conexión";
  }
}

// primera carga
document.addEventListener("DOMContentLoaded", actualizarTasa);

// reintento cada 60 segundos
setInterval(actualizarTasa, 60000);
</script>


    
    
    <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php echo StockData::getPrincipal()->imp_name; ?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-list-ol"></i></span>
     <select style="background-color: #333; color: white;" name="iva" id="iva"  class="form-control" onchange="showIva()">
         <option value="0"><?php 
switch (Core::$user->language){
 case 'ES': echo "NO"; break;
 case 'EN': echo "NOT"; break;
}
?></option>
         <option value="<?php echo StockData::getPrincipal()->imp_val;?>"><?php 
switch (Core::$user->language){
 case 'ES': echo "SI"; break;
 case 'EN': echo "YES"; break;
}
?></option>
     </select>
    </div>
  </div>


   <div class="col-md-3 col-12" id="type_iva">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Comprobante"; break;
 case 'EN': echo "Voucher"; break;
}
?></label>
    <select style="background-color: #333; color: white;" name="type_iva" class="form-control select2"  >
     <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
     <?php foreach(CData::getAllBySQL("where de>0 and hasta>0") as $c):?>
      <option value="<?php echo $c->id."-".$c->serie."-".$c->de;?>"><?php echo $c->name;?></option>
    <?php endforeach;?>
      </select>
    </div>  
    
     


     <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Total Reserva"; break;
 case 'EN': echo "Total Reserve"; break;
}
?></label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <select style="background-color: #333; color: white;" name="total" id="amount"  class="form-control"></select>
    </div>
  </div>
  
   <div class="col-md-3 col-12" id="iva_value">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Valor"; break;
 case 'EN': echo "Worth"; break;
}
?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</label>
    <input style="background-color: #333; color: white;" id="value_iva" name="value_iva"  class="form-control" readonly>
    </div>   
  
   <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Extra</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <select style="background-color: #333; color: white;" name="xtotal" id="xmount"  class="form-control"></select>
    </div>
  </div>



    <div class="col-md-3 col-12">   
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Abono o Total"; break;
 case 'EN': echo "Subscription or Total"; break;
}
?></label>
   
      <input style="background-color: #333; color: white;" type="number" value="0"  name="payment" id="payment" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" min="0" step="0.01">
    </div>
  </div>

    <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Monto Restante"; break;
 case 'EN': echo "Remaining Amount"; break;
}
?></label>
    
     <input style="background-color: #333; color: white;" readonly id="remaining" name="remaining"  class="form-control">
    </div>
  </div>

 <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Otros Cobros"; break;
 case 'EN': echo "Other Charges"; break;
}
?></label>
     <input style="background-color: #333; color: white;" type="number" name="plane" value="0"  class="form-control" min="0" step="0.01">
    </div>
  </div>
  
  <div class="col-md-12 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Nota de Reservacion"; break;
 case 'EN': echo "Reservation Note"; break;
}
?></label>
     <textarea style="background-color: #333; color: white;" row="2" name="comment" placeholder="Nota de Reservacion"  class="form-control"></textarea>
    </div>
  </div>
  
  </div>
  
  <p id="ultima_actualizacion" style="color:#aaa; font-size:12px; margin-top:5px;">
      Última actualización: -
    </p>
  
                     <input style="background-color: #333; color: white;" type="hidden" name="user_id" value="<?php echo $_GET['id'];?>">
                    <div class="row">
                  
                <div class="col-md-12 col-12 my-2">

                   <button type="submit"  class="btn btn-success btn-block btn-sm" ><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Finalizar"; break;
 case 'EN': echo "Finish"; break;
}
?></button>
                   
              
      </div>
    </div>
    
    
 <div hidden id="day"></div>
  

    </div>

    <!-- Botones -->
    <div class="d-flex justify-content-between mt-3">
      <div class="btn btn-secondary" id="prevBtn">Regresar</div>
      <div class="btn btn-warning" id="nextBtn">Siguiente</div>
    </div>
 
</div>
</div>
 </form> 
<script>
  const $tabs = $('#stepTabs .nav-link');
  const $panes = $('.tab-pane');
  let current = 0;

  function updateStep() {
    $tabs.removeClass('active').eq(current).addClass('active');
    $panes.removeClass('show active').eq(current).addClass('show active');

    $('#prevBtn').toggle(current > 0);
    $('#nextBtn').toggle(current < $tabs.length - 1);
  }

  $('#nextBtn').click(function () {
    if (current < $tabs.length - 1) {
      current++;
      updateStep();
    }
  });

  $('#prevBtn').click(function () {
    if (current > 0) {
      current--;
      updateStep();
    }
  });

  // Inicialización
  updateStep();
    
    document.getElementById("cars3").style.display = "none";
    document.getElementById("type_cars1").style.display = "inline-block";
    document.getElementById("type_cars3").style.display = "none";
   
    document.getElementById("type_iva").style.display = "none";
    document.getElementById("iva_value").style.display = "none"; 
 
 function showIva(){
  var getSelectValue = document.getElementById("iva").value;

  if(getSelectValue==0){
      
    document.getElementById("type_iva").style.display = "none";
    document.getElementById("iva_value").style.display = "none";
     
    function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
    
   
}

$("#remaining").val(agregarSeparadorMiles(parseFloat($("#amount").val())-parseFloat($("#payment").val())));
    
  }else if(getSelectValue==<?php echo StockData::getPrincipal()->imp_val;?>){

    document.getElementById("type_iva").style.display = "inline-block"; 
    document.getElementById("iva_value").style.display = "inline-block"; 
    function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
    
   
}

$("#remaining").val(agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#amount").val()*0.<?php echo StockData::getPrincipal()->imp_val;?>))-parseFloat($("#payment").val())));

$("#value_iva").val(agregarSeparadorMiles(+parseFloat($("#amount").val()*0.<?php echo StockData::getPrincipal()->imp_val;?>)));

}

 }
   
    document.getElementById("cars2_name").style.display = "none";
    document.getElementById("cars2_plate").style.display = "none";
    document.getElementById("cars2_chassis").style.display = "none";
    document.getElementById("cars2_category").style.display = "none";
    document.getElementById("cars2_brand").style.display = "none";
    document.getElementById("cars2_year").style.display = "none"; 
    
  $(document).ready(function(){
    $('#xmount').val();
    recargarxLista();

  })
  
  function recargarxLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
       data: {unit1: $('#unit1').val(), price1:  $('#price1').val(), uni2: $('#unit2').val(), price2:  $('#price2').val(), unit3: $('#unit3').val(), price3:  $('#price3').val(), unit4: $('#unit4').val(), price4:  $('#price4').val()},
      success:function(r){
        $('#xmount').html(r);
      }
    });
  }




function showInp(){
  var getSelectValue = document.getElementById("select2lista").value;

  if(getSelectValue==<?php echo StockData::getPrincipal()->id;?>){
    document.getElementById("rpayment").style.display = "none";
  }else{
   document.getElementById("rpayment").style.display = "inline-block";  
  }
 
}

function showInp2(){
  var getSelectValue = document.getElementById("type_id").value;

  if(getSelectValue=="1"){
    document.getElementById("end_at1").style.display = "inline-block";
    document.getElementById("end_at2").style.display = "none";
  }
   if(getSelectValue=="2"){
    document.getElementById("end_at2").style.display = "inline-block";
    document.getElementById("end_at1").style.display = "none";
  }
  
}

document.getElementById("end_at2").style.display = "none";

 $(document).ready(function(){
    $('#location').val();
    recargarLista();

    $('#location').change(function(){
      recargarLista();
    });
  })

  function recargarLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=all",
      data:"location=" + $('#location').val(),
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }
  

  $(document).ready(function(){
    $('#select2lista').val();
    recargar2Lista();

    $('#select2lista').change(function(){
      recargar2Lista();
    });
  })
  
   $('#cars').change(function(){
 
 
     $('.warning').hide();
       $('#draw-submitBtn').prop('disabled', false);
    
 
  recargarDanger();
  recargarExtras();
  
$("#unitx1").val(0);
$("#pricex1").val(0);

$("#unitx2").val(0);
$("#pricex2").val(0);

$("#unitx3").val(0);
$("#pricex3").val(0);

$("#unitx4").val(0);
$("#pricex4").val(0);


$("#xmount").val(0);

   function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
    
   
}

$("#remaining").val(agregarSeparadorMiles(parseFloat($("#amount").val())-parseFloat($("#payment").val())));

    });

  function recargarDanger(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=danger",
      data: {car_id: $('#cars').val()},
      success:function(r){
        $('#danger').html(r);
      }
    });
  }
  
  function recargarExtras(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=extra",
      data: {car_id: $('#cars').val()},
      success:function(r){
        $('#extra').html(r);
      }
    });
  }
  
   function recargar2Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
      data: {stock_id: $('#select2lista').val(),start_at: $('#start_at').val(),end_at: $('#end_at').val()},
      success:function(r){
        $('#cars').html(r);
      }
    });
  }
  
  
   var inival = $("#end_at").val();
  $("#end_at").change(function(){
  if ( $("#end_at").val() != inival ) {
      
      
      
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
     data: {stock_id: $('#select2lista').val(),start_at: $('#start_at').val(),end_at: $('#end_at').val()},
      success:function(r){
        $('#cars').html(r);
      }
    });
    
     $('.warning').hide();
       $('#submit').prop('disabled', true);
    
    function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni),
      hasta = new Date(fechaFin),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return  Math.round((diff / diaEnMils));
}


 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val())+parseFloat($("#xmount").val())));
    
  
  
  }
});



function showMethod(){
    
  var getSelectValue = document.getElementById("method").value;
  var getSelectValue2 = document.getElementById("select2lista").value;

  if(getSelectValue==1){

  document.getElementById("extra").style.display = "none";

      
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
     data: {stock_id: $('#select2lista').val(),start_at: $('#start_at').val(),end_at: $('#end_at').val()},
      success:function(r){
        $('#cars').html(r);
      }
    });
    
    function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni),
      hasta = new Date(fechaFin),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return  Math.round((diff / diaEnMils));
}


 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val())+parseFloat($("#xmount").val())));
    
    
    document.getElementById("rpayment").style.display = "none";
    document.getElementById("stock_id2").style.display = "none"; 
    
    document.getElementById("cars2_name").style.display = "none";
    document.getElementById("cars2_plate").style.display = "none";
    document.getElementById("cars2_chassis").style.display = "none";
    document.getElementById("cars2_category").style.display = "none";
    document.getElementById("cars2_brand").style.display = "none";
    document.getElementById("cars2_year").style.display = "none";
    document.getElementById("cars1").style.display = "inline-block";
    document.getElementById("cars3").style.display = "none";
    document.getElementById("type_cars1").style.display = "inline-block";
    document.getElementById("type_cars3").style.display = "none";
    
  }else if(getSelectValue==2){
      
      
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
     data: {stock_id: $('#select2lista').val(),start_at: $('#start_at').val(),end_at: $('#end_at').val()},
      success:function(r){
        $('#cars').html(r);
      }
    });
    
    function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni),
      hasta = new Date(fechaFin),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return  Math.round((diff / diaEnMils));
}


 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val())+parseFloat($("#xmount").val())));
    
  
      
  
    document.getElementById("rpayment").style.display = "none";
    document.getElementById("stock_id2").style.display = "none";
    
    document.getElementById("cars1").style.display = "inline-block";
    document.getElementById("cars3").style.display = "inline-block";
    document.getElementById("type_cars1").style.display = "none";
    document.getElementById("type_cars3").style.display = "inline-block";
    document.getElementById("cars2_name").style.display = "none";
    document.getElementById("cars2_plate").style.display = "none";
    document.getElementById("cars2_chassis").style.display = "none";
    document.getElementById("cars2_category").style.display = "none";
    document.getElementById("cars2_brand").style.display = "none";
    document.getElementById("cars2_year").style.display = "none";
  }else if(getSelectValue==3){
      

  recargarLista();
  recargarExtras();
  document.getElementById("extra").style.display = "none";
    

  function recargarLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=all",
      data:"location=" + getSelectValue,
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }
  
 
      recargar2Lista();
    
  function recargar2Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
      data:"stock_id=" + getSelectValue2,
      success:function(r){
        $('#cars').html(r);
      }
    });
  }
  

      
    document.getElementById("stock_id2").style.display = "inline-block";
    
    document.getElementById("cars1").style.display = "none";
    document.getElementById("cars3").style.display = "none";
    document.getElementById("type_cars1").style.display = "none";
    document.getElementById("type_cars3").style.display = "none";
    document.getElementById("cars2_name").style.display =  "inline-block";
    document.getElementById("cars2_plate").style.display =  "inline-block";
    document.getElementById("cars2_chassis").style.display = "inline-block";
    document.getElementById("cars2_category").style.display =  "inline-block";
    document.getElementById("cars2_brand").style.display =  "inline-block";
    document.getElementById("cars2_year").style.display =  "inline-block";
    
   document.getElementById("rpayment").style.display = "inline-block";  
  }
 
}

    $('#cars').change(function(){
      recargar3Lista();
      
  document.getElementById("extra").style.display = "inline-block";
    });
 

  function recargar3Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
      data:"cars=" + $('#cars').val(),
      success:function(r){
        $('#tariff').html(r);
      }
    });
  }

    $('#tariff').change(function(){
      Lista();

    });

$("#payment").keyup(function(){
function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
 });

function Lista(){
    

  var getSelectValue = document.getElementById("type_id").value;

  if(getSelectValue=="1"){
    
function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
}

   
 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=amount",
       data: {paycar: $('#payment').val(),tcars: $('#tariff').val(),dvs: $('#divisa_id').val(),cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price",
       data: {cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });
$("#remaining").val(($('#tariff').val()*$('#dias').val())*$('#divisa_id').val());

}

 if(getSelectValue=="2"){
    
function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
}

   
 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=free",
      data:"freedate=" + $('#payment_day').val(),
      success:function(r){
        $('#selectdate').html(r);
      }
    });
  
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=amount",
       data: {paycar: $('#payment').val(),tcars: $('#tariff').val(),dvs: $('#divisa_id').val(),cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price",
       data: {cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });
$("#remaining").val(($('#tariff').val()*$('#dias').val())*$('#divisa_id').val());

}
}



    tariff2.addEventListener("keyup", function()
    {   


  var getSelectValue = document.getElementById("type_id").value;

  if(getSelectValue=="1"){
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff2",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val())+parseFloat($("#xmount").val())));
    
        
    }
    
    
    if(getSelectValue=="2"){

    function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
}

   
 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=free",
      data:"freedate=" + $('#payment_day').val(),
      success:function(r){
        $('#selectdate').html(r);
      }
    });
  


    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff2",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles(($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val()));
    
        
    }
    }, false);
 

</script>
    


  
            </div>

            </div>
            </div>
</div>

</div>
</div>
</div>
  </div>
</div>

  
  </div>
                      </div>
                    </div>

             



            </div>

            </div>

            </div>
            </div>
</div>

</div>
</div>
</div>
  </div>
</div>


</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"):
$user = BookingData::getById($_GET["id"]);?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fas fa-edit'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Editar Reserva"; break;
 case 'EN': echo "Edit Reservation"; break;
}
?></h1>
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
                
                <!-- Profile Image -->
            <div class="card card-secondary card-outline" style="background-color: #222;">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="CF-SYSTEMS/storage/profiles/user.png"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?php 
switch (Core::$user->language){
 case 'ES': echo "Datos del Cliente"; break;
 case 'EN': echo "Customer Data"; break;
}
?></h3>

<form class="form-horizontal" action="./?action=booking&opt=updrandom" method="post" id="delivery" role="form" enctype="multipart/form-data">
                
                   <div class="row">
                      <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "CLIENTE/ EMPRESA"; break;
 case 'EN': echo "CUSTOMER/ COMPANY"; break;
}
?> </label>
    <select style="background-color: #333; color: white;" name="person_id" id="person_id"  class="form-control select2" required>
      <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
    <?php foreach(PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id) as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($client->id==$user->person_id){ echo "selected"; }?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
        <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "CONDUCTOR OPCIONAL"; break;
 case 'EN': echo "OPTIONAL DRIVER"; break;
}
?> </label>
   <select style="background-color: #333; color: white;" name="person2_id" id="person2_id" class="form-control select2" >
      <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
    <?php foreach(PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id) as $client):?>
     <option value="<?php echo $client->id;?>"<?php if($client->id==$user->person2_id){ echo "selected"; }?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
    </div>
               
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

      
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card" style="background-color: #222;">
             
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                    
                    <div class="row">
                        
                   <div hidden class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Tipo Contrato</label>
    <select style="background-color: #333; color: white;" name="type_id"  class="form-control select2" id="type_id" onchange="showInp2()">
      <option value="1">ENTRE FECHAS</option>
       <option value="2">ABIERTO</option>
      </select>
    </div>
 <div  class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Fecha a Entregar"; break;
 case 'EN': echo "Date to be delivered"; break;
}
?></label>
      <input style="background-color: #333; color: white;" value="<?php echo $user->start_at;?>"  type="datetime-local"  required name="start_at" id="start_at" class="form-control " >
    </div>

     <div id="end_at1" class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Fecha a Recibir"; break;
 case 'EN': echo "Date to Receive"; break;
}
?></label>
      <input style="background-color: #333; color: white;" value="<?php echo $user->end_at;?>"  type="datetime-local"  name="end_at" id="end_at"  class="form-control"> 
        </div>
        
         <div id="end_at2" class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Dia de Pago</label>
      <select style="background-color: #333; color: white;" name="payment_day" id="payment_day"  class="form-control select2">
      <option value="1">DIARIO</option>
      <option value="7">SEMANAL</option>
      <option value="15">QUINCENAL</option>
      <option value="30">MENSUAL</option>
      </select>
      <select style="background-color: #333; color: white;" hidden name="selectdate" id="selectdate"  class="form-control"></select>
        </div>


<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Forma de Pago"; break;
 case 'EN': echo "Method of payment"; break;
}
?></label>
    <select style="background-color: #333; color: white;" name="f_id" required class="form-control select2">
    <?php foreach(FData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>" <?php if($client->id==$user->f_id){ echo "selected"; }?>>
        <?php echo $client->name;?>
      </option>
    <?php endforeach;?>
      </select>
    </div>

  
   <div class="col-md-3 col-12"> 
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Tipo de Seguro"; break;
 case 'EN': echo "Insurance Type"; break;
}
?></label>
    
      <select style="background-color: #333; color: white;" class="form-control" name="type_sure">
      <?php foreach (SureData::getALL() as $sure): ?>
     <option value="<?php echo $sure->id;?>"<?php if($sure->id==$user->type_sure){ echo "selected"; }?>><?php echo $sure->name;?></option>
      <?php endforeach ?>
      </select>
</div>
<div class="col-md-2 col-12 "> 
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Deducible"; break;
 case 'EN': echo "Deductible"; break;
}
?></label>
    
          <input style="background-color: #333; color: white;" value="<?php echo $user->sure;?>" type="text" name="sure" class="form-control"   placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" autocomplete="off" >
</div>
 <div hidden class="col-md-4 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Deposito</label>
     <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
      <input style="background-color: #333; color: white;" type="text" name="deposit" value="<?php echo $user->deposit;?>" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" >
    </div>
  </div>

      <div class="col-md-2 col-12">
    <div class="input-group" >
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Combustible"; break;
 case 'EN': echo "Fuel"; break;
}
?></label>

    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-tint"></i></span>
     <select style="background-color: #333; color: white;" name="fuel"  class="form-control">
      <option value="R"<?php if($user->fuel=="R"){ echo "selected"; }?>>Reserva</option>
      <option value="1/4"<?php if($user->fuel=="1/4"){ echo "selected"; }?>>1/4</option>
      <option value="1/2"<?php if($user->fuel=="1/2"){ echo "selected"; }?>>Medio</option>
      <option value="3/4"<?php if($user->fuel=="3/4"){ echo "selected"; }?>>3/4</option>
      <option value="F"<?php if($user->fuel=="F"){ echo "selected"; }?>>Full</option>
     </select>
    </div>
  </div>

    <div class="col-md-4 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Lugar a Entregar"; break;
 case 'EN': echo "Place to Deliver"; break;
}
?></label>
   
  <style>
        #place_start2 { display: none; }
    </style>
    
    <span id="toggleplace_start" class="input-group-text btn-danger"><i class="fa fa-retweet"></i></span>
           <select id="place_start" style="background-color: #333; color: white;" class="form-control"  name="place_start">
               <option value="" selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>
         <?php foreach(PlaceData::getAll() as $place):?>
        <option value="<?php echo $place->name;?>"<?php if($place->name==$user->place_start){ echo "selected"; }?>><?php echo $place->name;?></option>
         <?php endforeach;?>
     </select>
      <input  name="place_start2" style="background-color: #333; color: white;" class="form-control" type="text" id="place_start2" value="<?php echo $user->place_start;?>" placeholder="Escribe aquí">
    </div>
  </div>

    <script>
        $(document).ready(function () {
            $("#toggleplace_start").click(function () {
                $("#place_start").toggle();
                $("#place_start2").toggle();
            });
        });
    </script>


    <div class="col-md-4 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Lugar a Recibir"; break;
 case 'EN': echo "Place to Receive"; break;
}
?></label>

  <style>
        #place_end2 { display: none; }
    </style>

     <span id="toggleplace_end" class="input-group-text btn-danger"><i class="fa fa-retweet"></i></span>
           <select id="place_end" style="background-color: #333; color: white;" class="form-control"  name="place_end">
               <option value="" selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>
         <?php foreach(PlaceData::getAll() as $place):?>
         <option value="<?php echo $place->name;?>"<?php if($place->name==$user->place_end){ echo "selected"; }?>><?php echo $place->name;?></option>
         <?php endforeach;?>
     </select>
      <input  name="place_end2" style="background-color: #333; color: white;" class="form-control" type="text" id="place_end2" value="<?php echo $user->place_end;?>" placeholder="Escribe aquí">
    </div>
  </div>

  
   <script>
        $(document).ready(function () {
            $("#toggleplace_end").click(function () {
                $("#place_end").toggle();
                $("#place_end2").toggle();
            });
        });
    </script>

         
 <div  class="card-header col-md-12 col-12 my-2" style="background-color:gray;">
<i class="fa fa-car"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?>:
</div>
 <div class="col-md-2 col-12" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Modo"; break;
 case 'EN': echo "Mode"; break;
}
?></label>
    <select style="background-color: #333; color: white;" name="method" class="form-control" id="method" onchange="showMethod()">
      <option value="1"><?php 
switch (Core::$user->language){
 case 'ES': echo "Disponible"; break;
 case 'EN': echo "Available"; break;
}
?></option>
      <option value="2"><?php 
switch (Core::$user->language){
 case 'ES': echo "Rejuego"; break;
 case 'EN': echo "Replay"; break;
}
?></option>
       </select>
    </div>

    <div class="col-md-3 col-12" hidden>
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Ubicacion</label>
    
    <select style="background-color: #333; color: white;" name="location" class="form-control" id="location">
    <?php foreach(LocationData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($client->id==$user->location){ echo "selected"; }?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<div class="col-md-3 col-12" hidden>
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Rent A Car</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-street-view"></i></span>
    <select style="background-color: #333; color: white;" name="stock_id" id="select2lista"  class="form-control" onchange="showInp()"></select>
    </div>
  </div>
  
  <div class="col-md-3 col-12" id="stock_id2" style="display: none">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Suplidor"; break;
 case 'EN': echo "Supplier"; break;
}
?></label>
      <input style="background-color: #333; color: white;" type="text" name="stock_id2"  class="form-control" placeholder="Nombre del Rent A Car"> 
    </div>
  
  
  <div class="col-md-2 col-12" id="rpayment" style="display: none">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Precio/Dia</label>
      <input style="background-color: #333; color: white;" type="number" value="<?php echo $user->rpayment;?>" name="rpayment"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" min="0" step="0.01"> 
    </div>
    
    
    <div class="col-md-5 col-12" id="cars1">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label" id="type_cars1"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></label>
    <label for="inputEmail1" class="col-md-12 col-12 control-label" id="type_cars3"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo (Rejuego)"; break;
 case 'EN': echo "Vehicle (Replay)"; break;
}
?></label>
    <select style="background-color: #333; color: white;" name="car_id"  id="cars" class="form-control select2"></select>
    </div>
  </div>
  
    <script>
        $(document).ready(function () {
            $('#cars').select2({
                templateResult: function (data) {
                    if (!data.id) {
                        return data.text;
                    }
                    const description = $(data.element).data('description');
                    const $template = $(
                        `<div>
                            <strong>${data.text}</strong>
                            <div style="font-size: 12px; color: orange;">${description || ''}</div>
                        </div>`
                    );
                    return $template;
                },
                templateSelection: function (data) {
                    return data.text;
                }
            });
        });
    </script>
    
   <div class="col-md-5 col-12" id="cars3">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo (Solicitado)"; break;
 case 'EN': echo "Vehicle (Requested)"; break;
}
?></label>
    <select style="background-color: #333; color: white;" name="car2_id" id="cars2" class="form-control select2">
    <option value="0">--<?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?>--</option>
    <?php foreach(CarsData::getAllBySQL("where status<>4") as $cars): $provider = SuppliersData::getById($cars->provider_id);?>
      <option value="<?php echo $cars->id;?>" data-description="<?php echo strtoupper($cars->getStock()->name);?>" <?php if($cars->id==$user->car2_id){ echo "selected"; }?>>
        <?php echo $cars->getBrand()->name." ".$cars->name." ".$cars->year." F: [".$cars->token."].";?>
      </option>
    <?php endforeach;?>
      </select>
    </div>
  </div>
 
    <script>
        $(document).ready(function () {
            $('#cars2').select2({
                templateResult: function (data) {
                    if (!data.id) {
                        return data.text;
                    }
                    const description = $(data.element).data('description');
                    const $template = $(
                        `<div>
                            <strong>${data.text}</strong>
                            <div style="font-size: 12px; color: orange;">${description || ''}</div>
                        </div>`
                    );
                    return $template;
                },
                templateSelection: function (data) {
                    return data.text;
                }
            });
        });
    </script>
  
 
 <div hidden class="col-md-2 col-12">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Divisa</label>
    <select style="background-color: #333; color: white;" name="divisa_id" id="divisa_id" class="form-control">
    <option value="1">DOLAR</option>
      </select></div>
  </div>
    
     <div class="col-md-4 col-12" id="cars2_brand">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Marca"; break;
 case 'EN': echo "Brand"; break;
}
?></label>
      <?php $clients = BrandData::getAll();?>
    <select style="background-color: #333; color: white;"  name="cars2_brand" class="form-control select2" >
      <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

     <div class="col-md-2 col-12" id="cars2_name">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Modelo"; break;
 case 'EN': echo "Model"; break;
}
?></label>
      <input style="background-color: #333; color: white;" type="text" name="cars2_name" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>

 
  <div class="col-md-3 col-12" id="cars2_category">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Categoria"; break;
 case 'EN': echo "Category"; break;
}
?></label>
    <select style="background-color: #333; color: white;" name="cars2_category" class="form-control select2"  >
      <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
    <?php foreach(CategoryData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

    <div class="col-md-1 col-12" id="cars2_year">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Año"; break;
 case 'EN': echo "Year"; break;
}
?> </label>
      <input style="background-color: #333; color: white;" type="text" value="<?php echo date("Y");?>" name="cars2_year" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>

<div class="col-md-2 col-12" id="cars2_plate">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Placa"; break;
 case 'EN': echo "Plate No."; break;
}
?></label>
      <input style="background-color: #333; color: white;" type="text"  name="cars2_plate" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>
  


<div id="extra" class="col-md-12 col-12"></div>

 <input style="background-color: #333; color: white;" type="hidden" id="unitx1" name="unit_extra1"   class="form-control"> 
  <input style="background-color: #333; color: white;" type="hidden" id="pricex1" name="price_extra1"  class="form-control"> 
  
 <input style="background-color: #333; color: white;" type="hidden" id="unitx2" name="unit_extra2"  class="form-control"> 
  <input style="background-color: #333; color: white;" type="hidden" id="pricex2" name="price_extra2"  class="form-control"> 
  
 <input style="background-color: #333; color: white;" type="hidden" id="unitx3" name="unit_extra3"  class="form-control"> 
  <input style="background-color: #333; color: white;" type="hidden" id="pricex3" name="price_extra3" class="form-control"> 
  
 <input style="background-color: #333; color: white;" type="hidden" id="unitx4" name="unit_extra4"  class="form-control"> 
  <input style="background-color: #333; color: white;" type="hidden" id="pricex4" name="price_extra4"  class="form-control"> 


   <div  class="card-header col-md-12 col-12 my-2" style="background-color:gray;">
<i class="fa fa-money-bill-alt"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Facturacion"; break;
 case 'EN': echo "Billing"; break;
}
?>:
</div> 

<div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Dias de Renta"; break;
 case 'EN': echo "Income Days"; break;
}
?></label>
    <input style="background-color: #333; color: white;" id="dias" name="day"  value="<?php echo $user->day;?>" class="form-control">
    </div>
</div>


  <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Precio por Dia"; break;
 case 'EN': echo "Price per day"; break;
}
?></label>
    <input style="background-color: #333; color: white;" type="number" value="<?php echo $user->price;?>"   name="price2" id="tariff2"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>"  min="0" step="0.01">
    </div>
   
    </div>
    
    <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php echo StockData::getPrincipal()->imp_name; ?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-list-ol"></i></span>
     <select style="background-color: #333; color: white;" name="iva" id="iva"  class="form-control" onchange="showIva()">
         <option value="0" <?php if($user->iva==0){ echo "selected"; } ?>>NO</option>
         <option value="<?php echo StockData::getPrincipal()->imp_val;?>" <?php if($user->iva==StockData::getPrincipal()->imp_val){ echo "selected"; } ?>>SI</option>
     </select>
    </div>
  </div>


   <div class="col-md-3 col-12" id="type_iva">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Comprobante"; break;
 case 'EN': echo "Voucher"; break;
}
?></label>
    <select style="background-color: #333; color: white;" name="type_iva" class="form-control select2"  >
     <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
     <?php foreach(CData::getAllBySQL("where de>0 and hasta>0") as $c):?>
      <option value="<?php echo $c->id."-".$c->serie."-".$c->de;?>"><?php echo $c->name;?></option>
    <?php endforeach;?>
      </select>
    </div>  
    

     <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Total Reserva"; break;
 case 'EN': echo "Total Reserve"; break;
}
?></label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <!-- CAMBIO: INPUT READONLY (ANTES ERA SELECT VACIO) -->
     <input style="background-color:#333;" type="number" step="0.01"
            name="total" id="amount" class="form-control"
            value="<?php echo $user->total; ?>" readonly>
    </div>
  </div>
  
   <div class="col-md-3 col-12" id="iva_value">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Valor"; break;
 case 'EN': echo "Worth"; break;
}
?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</label>
    <input style="background-color: #333; color: white;" id="value_iva" name="value_iva"  class="form-control" readonly>
    </div>   
  
   <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Extra</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <!-- CAMBIO: INPUT READONLY (ANTES ERA SELECT VACIO) -->
     <input style="background-color:#333;" type="number" step="0.01"
            name="xtotal" id="xmount" class="form-control"
            value="<?php echo $user->xtotal; ?>" readonly>
    </div>
  </div>

    <div class="col-md-3 col-12">   
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Abono o Total"; break;
 case 'EN': echo "Subscription or Total"; break;
}
?></label>
   
      <input style="background-color: #333; color: white;" type="number" value="<?php echo $user->payment;?>"  name="payment" id="payment" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" min="0" step="0.01">
    </div>
  </div>

    <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Monto Restante"; break;
 case 'EN': echo "Remaining Amount"; break;
}
?></label>
    
     <input style="background-color: #333; color: white;" readonly id="remaining" name="remaining"  class="form-control">
    </div>
  </div>

 <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Otros Cobros"; break;
 case 'EN': echo "Other Charges"; break;
}
?></label>
     <input style="background-color: #333; color: white;" type="number" value="<?php echo $user->plane;?>" name="plane" id="plane" class="form-control" min="0" step="0.01">
    </div>
  </div>
  
                     

  </div>
  
                     <input style="background-color: #333; color: white;" type="hidden" name="user_id" value="<?php echo $_GET['id'];?>">
                    <div class="row">
                  
                <div class="col-md-12 col-12 my-2">

                   <button type="submit" id="draw-submitBtn" class="btn btn-success btn-block btn-sm" disabled><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Finalizar"; break;
 case 'EN': echo "Finish"; break;
}
?></button>
                   
                 <script>
document.getElementById('delivery').addEventListener('submit', function(event) {
    document.getElementById('draw-submitBtn').disabled = true;

    // ✅ FIX: si el usuario está usando el input, copiamos al select; y viceversa
    if ($("#place_start").is(":visible")) {
      $("#place_start2").val($("#place_start").val());
    } else {
      $("#place_start").val($("#place_start2").val());
    }

    if ($("#place_end").is(":visible")) {
      $("#place_end2").val($("#place_end").val());
    } else {
      $("#place_end").val($("#place_end2").val());
    }
});
</script>
                </div>
                </div>
    </div>        
  
  </div>


              </div>
           
     
  
    <script type="text/javascript">
    
    document.getElementById("cars3").style.display = "none";
    document.getElementById("type_cars1").style.display = "inline-block";
    document.getElementById("type_cars3").style.display = "none";
   
    document.getElementById("type_iva").style.display = "none";
    document.getElementById("iva_value").style.display = "none"; 
 

function agregarSeparadorMiles(numero) {
  let partesNumero = numero.toString().split(',');
  partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  return partesNumero.join('.');
}

function calcRemainingSimple(){
  let total = parseFloat($("#amount").val() || 0);
  let xt    = parseFloat($("#xmount").val() || 0);
  let pago  = parseFloat($("#payment").val() || 0);
  $("#remaining").val(agregarSeparadorMiles((total + xt) - pago));
}

function showIva(){
  var getSelectValue = document.getElementById("iva").value;

  if(getSelectValue==0){
    document.getElementById("type_iva").style.display = "none";
    document.getElementById("iva_value").style.display = "none";
    calcRemainingSimple();
  }else if(getSelectValue==<?php echo StockData::getPrincipal()->imp_val;?>){
    document.getElementById("type_iva").style.display = "inline-block"; 
    document.getElementById("iva_value").style.display = "inline-block"; 

    // Nota: aquí tú estabas sumando iva con 0.xx; lo dejo igual a tu lógica original:
    let base = parseFloat($("#amount").val() || 0);
    let pago = parseFloat($("#payment").val() || 0);
    $("#remaining").val(agregarSeparadorMiles((base + (base*0.<?php echo StockData::getPrincipal()->imp_val;?>)) - pago));
    $("#value_iva").val(agregarSeparadorMiles(+parseFloat(base*0.<?php echo StockData::getPrincipal()->imp_val;?>)));
  }
}

document.getElementById("cars2_name").style.display = "none";
document.getElementById("cars2_plate").style.display = "none";
document.getElementById("cars2_category").style.display = "none";
document.getElementById("cars2_brand").style.display = "none";
document.getElementById("cars2_year").style.display = "none"; 

$(document).ready(function(){
  // ✅ Inicializa IVA/remaining al entrar
  showIva();
  calcRemainingSimple();
})

function showInp(){
  var getSelectValue = document.getElementById("select2lista").value;

  if(getSelectValue==<?php echo StockData::getPrincipal()->id;?>){
    document.getElementById("rpayment").style.display = "none";
  }else{
   document.getElementById("rpayment").style.display = "inline-block";  
  }
}

function showInp2(){
  var getSelectValue = document.getElementById("type_id").value;

  if(getSelectValue=="1"){
    document.getElementById("end_at1").style.display = "inline-block";
    document.getElementById("end_at2").style.display = "none";
  }
  if(getSelectValue=="2"){
    document.getElementById("end_at2").style.display = "inline-block";
    document.getElementById("end_at1").style.display = "none";
  }
}

document.getElementById("end_at2").style.display = "none";

// CARGA LISTA STOCK
$(document).ready(function(){
  $('#location').val();
  recargarLista();
  $('#location').change(function(){
    recargarLista();
  });
})

function recargarLista(){
  $.ajax({
    type:"POST",
    url:"./?action=get&opt=all",
    data:"location=" + $('#location').val(),
    success:function(r){
      $('#select2lista').html(r);
    }
  });
}

// CARGA VEHICULOS EDIT (seleccionado)
$(document).ready(function(){
  $('#select2lista').val();
  recargar2Lista();
  $('#select2lista').change(function(){
    recargar2Lista();
  });
})

function recargar2Lista(){
  $.ajax({
    type:"POST",
    url:"./?action=get&opt=editcars",
    data:"car=" + <?php echo $user->car_id;?>,
    success:function(r){
      $('#cars').html(r);
    }
  });
}

// EXTRAS
$('#draw-submitBtn').prop('disabled', false);
recargarExtras();

function recargarExtras(){
  $.ajax({
    type:"POST",
    url:"./?action=get&opt=extra",
    data: {car_id: $('#cars').val()},
    success:function(r){
      $('#extra').html(r);
    }
  });
}

$('#cars').change(function(){
  $("#unitx1").val(0); $("#pricex1").val(0);
  $("#unitx2").val(0); $("#pricex2").val(0);
  $("#unitx3").val(0); $("#pricex3").val(0);
  $("#unitx4").val(0); $("#pricex4").val(0);
  $("#xmount").val(0);

  calcRemainingSimple();
});

// METHOD
function showMethod(){
  var getSelectValue = document.getElementById("method").value;
  var getSelectValue2 = document.getElementById("select2lista").value;

  if(getSelectValue==1){

    document.getElementById("extra").style.display = "none";

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
      data: {stock_id: $('#select2lista').val(),start_at: $('#start_at').val(),end_at: $('#end_at').val()},
      success:function(r){
        $('#cars').html(r);
      }
    });

    function calcularDiasAusencia(fechaIni, fechaFin) {
      var diaEnMils = 1000 * 60 * 60 * 24,
          desde = new Date(fechaIni),
          hasta = new Date(fechaFin),
          diff = hasta.getTime() - desde.getTime();
      return  Math.round((diff / diaEnMils));
    }

    $("#dias").val(calcularDiasAusencia(
      document.getElementById('start_at').value,
      document.getElementById('end_at').value
    ));

    // AQUÍ TU SISTEMA RECALCULA amount por ajax, PERO YA amount ES INPUT
    // (si lo quieres recalcular por ajax, debes asignar el valor en success con $("#amount").val(r); )
    // Por ahora, dejamos remaining estable:
    calcRemainingSimple();

    document.getElementById("rpayment").style.display = "none";
    document.getElementById("stock_id2").style.display = "none"; 
    
    document.getElementById("cars2_name").style.display = "none";
    document.getElementById("cars2_plate").style.display = "none";
    document.getElementById("cars2_category").style.display = "none";
    document.getElementById("cars2_brand").style.display = "none";
    document.getElementById("cars2_year").style.display = "none";
    document.getElementById("cars1").style.display = "inline-block";
    document.getElementById("cars3").style.display = "none";
    document.getElementById("type_cars1").style.display = "inline-block";
    document.getElementById("type_cars3").style.display = "none";
    
  }else if(getSelectValue==2){

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
      data: {stock_id: $('#select2lista').val(),start_at: $('#start_at').val(),end_at: $('#end_at').val()},
      success:function(r){
        $('#cars').html(r);
      }
    });

    calcRemainingSimple();

    document.getElementById("rpayment").style.display = "none";
    document.getElementById("stock_id2").style.display = "none";
    
    document.getElementById("cars1").style.display = "inline-block";
    document.getElementById("cars3").style.display = "inline-block";
    document.getElementById("type_cars1").style.display = "none";
    document.getElementById("type_cars3").style.display = "inline-block";
    document.getElementById("cars2_name").style.display = "none";
    document.getElementById("cars2_plate").style.display = "none";
    document.getElementById("cars2_category").style.display = "none";
    document.getElementById("cars2_brand").style.display = "none";
    document.getElementById("cars2_year").style.display = "none";
  }
}

$("#payment").keyup(function(){
  showIva();
});

// Si cambian otros cobros, recalcula remaining
$("#plane").on("keyup change", function(){
  calcRemainingSimple();
});

</script>

 <div hidden id="day"></div>
   
       
       <script>
/* =========================================
   RECALCULO TOTAL EN EDIT (SIN AJAX)
   - cambia fechas => recalcula dias y total
   - cambia precio/dia => recalcula total
   - cambia iva => recalcula restante e iva
   - cambia abono => recalcula restante
   - cambia extra/otros => recalcula restante
========================================= */

function toNum(v){
  v = (v ?? "").toString().trim();
  if(v==="") return 0;
  // quita separadores de miles (como tu sistema)
  v = v.replace(/,/g,'');
  return parseFloat(v) || 0;
}

function calcularDias(fechaIni, fechaFin){
  if(!fechaIni || !fechaFin) return 0;
  // datetime-local -> Date
  let desde = new Date(fechaIni);
  let hasta = new Date(fechaFin);
  let diff = hasta.getTime() - desde.getTime();
  let diaEnMils = 1000 * 60 * 60 * 24;
  let d = Math.round(diff / diaEnMils);
  if(isNaN(d) || d < 0) d = 0;
  return d;
}

function recalcularTotal(){
  // 1) dias
  let d = calcularDias($('#start_at').val(), $('#end_at').val());
  $('#dias').val(d);

  // 2) total base = precio/dia * dias
  let priceDay = toNum($('#tariff2').val());
  let totalBase = (priceDay * d);

  // 3) extra + otros
  let xtotal = toNum($('#xmount').val());
  let plane  = toNum($('#plane').val());

  // 4) IVA
  let ivaPercent = toNum($('#iva').val()); // 0 o 18 (o el imp_val)
  let ivaAmount  = ((totalBase + xtotal) * (ivaPercent/100));

  // 5) Total Reserva (lo que tú guardas como "total" base)
  // Si quieres que "amount" sea SOLO reserva sin extras, deja totalBase.
  // Pero tú normalmente lo usas como total reserva base: lo dejo así:
  $('#amount').val( (totalBase).toFixed(2) );

  // 6) mostrar valor iva si aplica
  if(ivaPercent > 0){
    $('#type_iva').show();
    $('#iva_value').show();
    $('#value_iva').val(ivaAmount.toFixed(2));
  }else{
    $('#type_iva').hide();
    $('#iva_value').hide();
    $('#value_iva').val('');
  }

  // 7) Remaining = (totalBase + xtotal + plane + iva) - payment
  let payment = toNum($('#payment').val());
  let totalFinal = (totalBase + xtotal + plane + ivaAmount);
  let remaining = totalFinal - payment;

  $('#remaining').val( remaining.toFixed(2) );
}

// ✅ Hooks (eventos)
$(document).ready(function(){
  // inicial
  recalcularTotal();

  // fechas
  $('#start_at, #end_at').on('change keyup', function(){
    recalcularTotal();
  });

  // precio/dia
  $('#tariff2').on('keyup change', function(){
    recalcularTotal();
  });

  // iva
  $('#iva').on('change', function(){
    recalcularTotal();
  });

  // abono
  $('#payment').on('keyup change', function(){
    recalcularTotal();
  });

  // otros cobros
  $('#plane').on('keyup change', function(){
    recalcularTotal();
  });

  // total extra (si lo llenas con ajax y le haces .val())
  $('#xmount').on('keyup change', function(){
    recalcularTotal();
  });
});
</script>
       
<style>

section{
    flex:1;
}

.button {
    background: #3071a9;
    box-shadow: inset 0 -3px 0 rgba(0,0,0,.3);
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 5px;
    margin: 0 15px;
    text-decoration: none;
    color: white;
}

.button:active { transform: scale(0.9); }

.contenedor {
    width: 100%
    margin: 5px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

input[type=range] { -webkit-appearance: none; margin: 18px 0; }
input[type=range]:focus { outline: none; }
input[type=range]::-webkit-slider-runnable-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-webkit-slider-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
  -webkit-appearance: none;
  margin-top: -14px;
}
input[type=range]:focus::-webkit-slider-runnable-track { background: #367ebd; }
input[type=range]::-moz-range-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-moz-range-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]::-ms-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  background: transparent;
  border-color: transparent;
  border-width: 16px 0;
  color: transparent;
}
input[type=range]::-ms-fill-lower {
  background: #2a6495;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-fill-upper {
  background: #3071a9;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]:focus::-ms-fill-lower { background: #3071a9; }
input[type=range]:focus::-ms-fill-upper { background: #367ebd; }
</style>

<?php if(StockData::getPrincipal()->method=="1"): ?>
<style>
#draw-canvas {
  border: 2px dotted #CCCCCC;
  border-radius: 5px;
  cursor: crosshair;
  background-image: url("CF-SYSTEMS/storage/configuration/fxrma.png");
  background-size: 100% 100%;
}
#draw-dataUrl { width: 100%; }
</style>

<?php elseif(StockData::getPrincipal()->method=="2"): ?>
<style>
#draw-canvas {
  border: 2px dotted #CCCCCC;
  border-radius: 5px;
  cursor: crosshair;
  background-image: url("CF-SYSTEMS/storage/configuration/furma.png");
  background-size: 100% 100%;
}
#draw-dataUrl { width: 100%; }
</style>
<?php endif;?>

<!-- ✅ IMPORTANTE: UN SOLO CIERRE DE FORM -->
</form>
  
            </div>
            </div>
            </div>
</div>

</div>
</div>
</div>
  </div>
</div>

  
  </div>
                      </div>
                    </div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="modal"):?>

<section class="content">
<div class="row">
  <div class="col-12">
  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-edit"></i> Reservacion de Vehiculo</h1>
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
$user = BookingData::getById($_GET["id"]);
$TicketMm = StockData::getPrincipal()->ticket_mm;
$url = $TicketMm . "/ticket-reserve.php?id=" . $user->id;
?>

<!-- Contenedor principal -->
<div style="position:relative; width:100%; height:100vh; background:#1e1e1e;">

  <!-- Botones flotantes dentro del contenedor -->
  <div style="position:absolute; top:20px; right:20px; background:#111c; padding:10px; border-radius:12px; z-index:10; box-shadow: 0 0 10px rgba(0,0,0,0.5); display:flex; flex-direction:column; gap:10px;">
    <button onclick="imprimirPDF()" style="background:#c40030; color:#fff; border:none; padding:10px 16px; border-radius:40px; font-weight:bold; font-size:15px; display:flex; align-items:center; gap:8px;">
      <i class="fa fa-print"></i> IMPRIMIR
    </button>
    <a id="btnDescargar" href="<?php echo $url; ?>" download style="background:#007bff; color:#fff; border:none; padding:10px 16px; border-radius:40px; font-weight:bold; font-size:15px; display:flex; align-items:center; gap:8px; text-decoration:none;">
      <i class="fa fa-download"></i> DESCARGAR
    </a>
    
    <a  href="./?action=booking&opt=what&id=<?php echo $user->id; ?>&person_id=<?php echo $user->person_id; ?>&car_id=<?php echo $user->car_id; ?>" style="background:#28a745; color:#fff; border:none; padding:10px 16px; border-radius:40px; font-weight:bold; font-size:15px; display:flex; align-items:center; gap:8px;">
      <i class="fab fa-whatsapp"></i> COMPARTIR
    </a>
   
  </div>

  <!-- Iframe del PDF -->
  <iframe id="iframePDF" src="<?php echo $url; ?>" 
    style="width:100%; height:100%; border:none; position:absolute; top:0; left:0; z-index:1;">
  </iframe>

</div>

<script>
function imprimirPDF() {
  const iframe = document.getElementById('iframePDF');
  iframe.focus();
  iframe.contentWindow.print();
}
</script>


  </div>
</div>

</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="signature"): $sell = BookingData::getById($_GET["id"]); 
$clistock= PersonData::getById($_SESSION["client_id"]);?>
   <section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
         <h1 class="m-0" style="color:white;"><i class='fa fa-user-plus'></i> Confirmar Reservacion</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> <?php 
switch ($clistock->language){
 case 'ES': echo "Tablero"; break;
 case 'EN': echo "Board"; break;
}
?></li>
              <li class="breadcrumb-item active"><i class='fa fa-edit'></i> Reserva</li>

            </ol>
          </div><!-- /.col -->
        </div>

        <div class="row">

            <div class="col-md-12">
                
                <!-- Profile Image -->
            <div class="card card-secondary card-outline" style="background-color:#222; color:white;">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="CF-SYSTEMS/storage/profiles/man.png"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?php 
switch ($clistock->language){
 case 'ES': echo "Datos del Cliente"; break;
 case 'EN': echo "Customer Data"; break;
}
?></h3>

<form class="form-horizontal" action="./?action=booking&opt=signature" method="post" id="delivery" role="form" enctype="multipart/form-data">
                
                  
                   <div class="row">
                  
                      <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "CLIENTE/ EMPRESA"; break;
 case 'EN': echo "CUSTOMER/ COMPANY"; break;
}
?></label>
    <select style="background-color: #333; color: white;" class="form-control" required>
    <?php foreach(PersonData::getAllBySQL("where id=".$sell->person_id) as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
        <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "CONDUCTOR OPCIONAL"; break;
 case 'EN': echo "OPTIONAL DRIVER"; break;
}
?> </label>
   <select style="background-color: #333; color: white;" class="form-control" >
    <option value="">-- <?php 
switch ($clistock->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
    <?php foreach(PersonData::getAllBySQL("where id=".$sell->person2_id) as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
    </div>
               
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

      
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card" style="background-color:#222; color:white;">
             
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                    
                    <div class="row">
                        
                   <div hidden class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Tipo Contrato</label>
    <select style="background-color: #333; color: white;" name="type_id"  class="form-control select2" id="type_id" onchange="showInp2()">
      <option value="1">ENTRE FECHAS</option>
       <option value="2">ABIERTO</option>
      </select>
    </div>
 <div hidden class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Fecha a Entregar"; break;
 case 'EN': echo "Date to be delivered"; break;
}
?></label>
      <input style="background-color: #333; color: white;" type="datetime-local" value="<?php echo $sell->start_at;?>" readonly class="form-control " >
    </div>

     <div id="end_at1" class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Fecha a Recibir"; break;
 case 'EN': echo "Date to Receive"; break;
}
?></label>
      <input style="background-color: #333; color: white;" type="datetime-local" value="<?php echo $sell->end_at;?>" readonly class="form-control"> 
        </div>
        
         <div id="end_at2" class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Dia de Pago</label>
      <select style="background-color: #333; color: white;" name="payment_day" id="payment_day"  class="form-control select2">
      <option value="1">DIARIO</option>
      <option value="7">SEMANAL</option>
      <option value="15">QUINCENAL</option>
      <option value="30">MENSUAL</option>
      </select>
      <select style="background-color: #333; color: white;" hidden name="selectdate" id="selectdate"  class="form-control"></select>
        </div>


<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Forma de Pago"; break;
 case 'EN': echo "Method of payment"; break;
}
?></label>
    <select style="background-color: #333; color: white;" name="f_id" required class="form-control">
    <?php foreach(FData::getAllBySQL("where id=".$sell->f_id) as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

  
   <div class="col-md-3 col-12"> 
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Tipo de Seguro"; break;
 case 'EN': echo "Insurance Type"; break;
}
?></label>
    
      <select style="background-color: #333; color: white;" class="form-control" name="type_sure">
      <?php foreach (SureData::getAllBySQL("where id=".$sell->type_sure) as $sure): ?>
      <option value="<?php echo $sure->id;?>"><?php echo $sure->name;?></option>
      <?php endforeach ?>
      </select>
</div>
<div class="col-md-3 col-12 "> 
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Deducible"; break;
 case 'EN': echo "Deductible"; break;
}
?></label>
    
          <input style="background-color: #333; color: white;" type="text" readonly class="form-control" value="<?php echo $sell->sure;?>"   placeholder="<?php 
switch ($clistock->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" autocomplete="off" >
</div>
 <div hidden class="col-md-4 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Deposito</label>
     <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
      <input style="background-color: #333; color: white;" type="text" name="deposit" value="0" class="form-control" placeholder="<?php 
switch ($clistock->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" >
    </div>
  </div>

      <div class="col-md-2 col-12">
    <div class="input-group" >
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Combustible"; break;
 case 'EN': echo "Fuel"; break;
}
?></label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-tint"></i></span>
     <select style="background-color: #333; color: white;" class="form-control">
      <option value="R" <?php if($sell->fuel=="R"){ echo "selected";};?>>Reserva</option>
      <option value="1/4"  <?php if($sell->fuel=="1/4"){ echo "selected";};?>>1/4</option>
      <option value="1/2"  <?php if($sell->fuel=="1/2"){ echo "selected";};?>>Medio</option>
      <option value="3/4"  <?php if($sell->fuel=="3/4"){ echo "selected";};?>>3/4</option>
      <option value="F"  <?php if($sell->fuel=="F"){ echo "selected";};?>>Full</option>
     </select>
    </div>
  </div>

    <div class="col-md-5 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Lugar a Entregar"; break;
 case 'EN': echo "Place to Deliver"; break;
}
?></label>
     <input type="text" style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->place_start;?>">
    </div>
  </div>

     <div class="col-md-4 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Lugar a Recibir"; break;
 case 'EN': echo "Place to Receive"; break;
}
?></label>
     <input type="text" style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->place_end;?>">
    </div>
  </div>
         
 <div  class="card-header col-md-12 col-12 my-2" style="background-color:gray;">
<i class="fa fa-car"></i> <?php 
switch ($clistock->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?>:
</div>

    <div class="col-md-3 col-12" hidden>
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Ubicacion</label>
    
    <select style="background-color: #333; color: white;" class="form-control" id="location">
    <?php foreach(LocationData::getAllBySQL("where id=".$sell->location) as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<div class="col-md-3 col-12" hidden>
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Rent A Car</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-street-view"></i></span>
    <select style="background-color: #333; color: white;" id="select2lista"  class="form-control"></select>
    </div>
  </div>
  
  <div class="col-md-3 col-12" id="stock_id2" style="display: none"/>
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Suplidor</label>
      <input style="background-color: #333; color: white;" type="text" name="stock_id2"  class="form-control" placeholder="Nombre del Rent A Car"> 
    </div>
  
  
  <div class="col-md-2 col-12" id="rpayment" style="display: none"/>
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Precio/Rent A Car</label>
      <input style="background-color: #333; color: white;" type="number" value="0" name="rpayment"  class="form-control" placeholder="<?php 
switch ($clistock->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" min="0" step="0.01"> 
    </div>
    
    
    <div class="col-md-5 col-12" id="cars1">
    <div class="input-group">
<?php if($sell->car2_id==0):?>
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></label>
<?php elseif($sell->car2_id<>0):?>
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?> (Rejuego)</label>
<?php endif;?>
     <select style="background-color: #333; color: white;" class="form-control">
    <?php foreach(CarsData::getAllBySQL("where id=".$sell->car_id) as $cars):?>
      <option value="<?php echo $cars->id;?>"><?php echo $cars->getBrand()->name." ".$cars->name." ".$cars->year." F: [".$cars->token."].";?></option>
    <?php endforeach;?>
      </select>
    </div>
  
  </div>
  
   
  
  <?php if($sell->car2_id<>0):?>
   <div class="col-md-5 col-12">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?> (Solicitado)</label>
    <select style="background-color: #333; color: white;"  id="cars2" class="form-control select2">
    <?php foreach(CarsData::getAllBySQL("where id=".$sell->car2_id) as $cars): $provider = SuppliersData::getById($cars->provider_id);?>
      <option value="<?php echo $cars->id;?>"><?php echo $cars->getBrand()->name." ".$cars->name." ".$cars->year." F: [".$cars->token."].";?></option>
    <?php endforeach;?>
      </select>
    </div>
  </div>
 <?php endif;?>
 

 
 
 
 <div hidden class="col-md-2 col-12">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Divisa</label>
    <select style="background-color: #333; color: white;" name="divisa_id" id="divisa_id" class="form-control">
    <option value="1">DOLAR</option>
      </select></div>
  </div>
    
     <div class="col-md-4 col-12" id="cars2_brand">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Marca</label>
      <?php $clients = BrandData::getAll();?>
    <select style="background-color: #333; color: white;"  name="cars2_brand" class="form-control select2" >
     <option value="">-- <?php 
switch ($clistock->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

     <div class="col-md-2 col-12" id="cars2_name">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Modelo</label>
      <input style="background-color: #333; color: white;" type="text" name="cars2_name" autocomplete="off"  class="form-control" placeholder="<?php 
switch ($clistock->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>

 
  <div class="col-md-3 col-12" id="cars2_category">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Categoria</label>
    <select style="background-color: #333; color: white;" name="cars2_category" class="form-control select2"  >
      <option value="">-- <?php 
switch ($clistock->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
    <?php foreach(CategoryData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

    <div class="col-md-1 col-12" id="cars2_year">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Año </label>
      <input style="background-color: #333; color: white;" type="text" value="<?php echo date("Y");?>" name="cars2_year" autocomplete="off"  class="form-control" placeholder="<?php 
switch ($clistock->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>

<div class="col-md-2 col-12" id="cars2_plate">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">No. de Placa</label>
      <input style="background-color: #333; color: white;" type="text" data-inputmask='"mask": "A 999999"' data-mask name="cars2_plate" autocomplete="off"  class="form-control" placeholder="<?php 
switch ($clistock->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>
  

<div  class="card-header col-md-12 col-12 my-2" style="background-color:gray;">
<i class="fa fa-clone"></i>  Extras:
</div>
    
    <?php  $cars = CarsData::getbyId($sell->car_id); if($cars->getCategory()->name=="Ambulancia"):?>
<div class="row">
 <div class="col-md-3 col-12">
     
     <input style="background-color: #333; color: white;" type="text" value="CAMILLA" class="form-control" autocomplete="off"  readonly>
       <div class="input-group">
     <span class="input-group-text autocomplete" style="background-color:orange;">UND</span>
     <input style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->unit_extra1;?>"  autocomplete="off" id="unit1"  placeholder="Unidad"  min="0">
       <input style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->price_extra1;?>"   autocomplete="off" id="price1"   placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>

 <div class="col-md-3 col-12">
     
     <input style="background-color: #333; color: white;" type="text" value="OXIGENO" class="form-control" autocomplete="off"  readonly>
       <div class="input-group">
     <span class="input-group-text autocomplete" style="background-color:orange;">UND</span>
        <input style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->unit_extra2;?>"  autocomplete="off" id="unit2"   placeholder="Unidad"  min="0" >
       <input style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->price_extra2;?>"  placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>
  

 <div class="col-md-3 col-12">
     
     <input style="background-color: #333; color: white;" type="text" value="DESFIBRILADOR" class="form-control" autocomplete="off" readonly>
       <div class="input-group">
     <span class="input-group-text autocomplete" style="background-color:orange;">UND</span>
      <input style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->unit_extra3;?>"   autocomplete="off" id="unit3"   placeholder="Unidad"  min="0" >
       <input style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->price_extra3;?>"   autocomplete="off" id="price3"    placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>
 
 <div class="col-md-3 col-12">
     
     <input style="background-color: #333; color: white;" type="text" value="SILLA DE RUEDA" class="form-control" autocomplete="off"  readonly>
       <div class="input-group">
     <span class="input-group-text autocomplete" style="background-color:orange;">UND</span>
       <input style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->unit_extra4;?>"   class="form-control" autocomplete="off" id="unit4" placeholder="Unidad"  min="0" >
        <input style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->unit_extra4;?>"   autocomplete="off" id="price4"  placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>
  </div>

<?php else:?>
<div class="row">
 <div class="col-md-4 col-12">
     
     <input style="background-color: #333; color: white;" type="text" value="CARSEAT" class="form-control" autocomplete="off"  readonly>
       <div class="input-group">
     <span class="input-group-text autocomplete" style="background-color:orange;">UND</span>
      <input style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->unit_extra1;?>"   class="form-control" autocomplete="off" id="unit1"   placeholder="Unidad"  min="0">
       <input style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->unit_extra1;?>"   class="form-control"   autocomplete="off" id="price1" placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>

 <div class="col-md-4 col-12">
     
     <input style="background-color: #333; color: white;" type="text" value="INTERNET" class="form-control" autocomplete="off"  readonly>
       <div class="input-group">
     <span class="input-group-text autocomplete" style="background-color:orange;">UND</span>
     <input style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->unit_extra2;?>"   autocomplete="off" id="unit2"  placeholder="Unidad"  min="0" >
       <input style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->price_extra2;?>"  autocomplete="off" id="price2" placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>
  

 <div class="col-md-4 col-12">
     
     <input style="background-color: #333; color: white;" type="text" value="TRAILER" class="form-control" autocomplete="off"  readonly>
       <div class="input-group">
     <span class="input-group-text autocomplete" style="background-color:orange;">UND</span>
       <input style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->unit_extra3;?>" class="form-control" autocomplete="off" id="unit3"  placeholder="Unidad"  min="0" >
       <input style="background-color: #333; color: white;" class="form-control" readonly value="<?php echo $sell->price_extra3;?>"   autocomplete="off" id="price3"  placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>
 
  </div>
<?php endif;?>
  

   <div  class="card-header col-md-12 col-12 my-2" style="background-color:gray;">
<i class="fa fa-money-bill-alt"></i> <?php 
switch ($clistock->language){
 case 'ES': echo "Facturacion"; break;
 case 'EN': echo "Billing"; break;
}
?>:
</div> 

<div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Dias de Renta"; break;
 case 'EN': echo "Income Days"; break;
}
?></label>
    <input style="background-color: #333; color: white;" value="<?php echo $sell->day;?>" readonly  class="form-control">
    </div>
</div>


  <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Precio por Dia"; break;
 case 'EN': echo "Price per day"; break;
}
?></label>
    <input style="background-color: #333; color: white;" value="<?php echo $sell->price;?>"  class="form-control" readonly>
    </div>
   
    </div>
    
    <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">ITBIS (18%)</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-list-ol"></i></span>
     <select style="background-color: #333; color: white;" id="iva"  class="form-control" onchange="showIva()">
         <option value="0" <?php if($sell->iva=="0"){ echo "selected";};?>>NO</option>
         <option value="18" <?php if($sell->iva=="18"){ echo "selected";};?>>SI</option>
     </select>
    </div>
  </div>


   <div class="col-md-3 col-12" id="type_iva">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Comprobante</label>
    <select style="background-color: #333; color: white;" class="form-control select2">
     <?php foreach(CData::getAllBySQL("where id=".$sell->type_iva) as $c):?>
      <option value="<?php echo $c->id."-".$c->serie."-".$c->de;?>"><?php echo $c->name;?></option>
    <?php endforeach;?>
      </select>
    </div>  
    
     


     <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Total Reserva"; break;
 case 'EN': echo "Total Reserve"; break;
}
?></label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <input style="background-color: #333; color: white;" value="<?php echo $sell->total;?>"  class="form-control" readonly>
    </div>
  </div>
  
   <div class="col-md-3 col-12" id="iva_value">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Valor (%18)</label>
  <input style="background-color: #333; color: white;" value="<?php echo $sell->value_iva;?>"  class="form-control" readonly>
    </div>   
  
   <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Extra</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
   <input style="background-color: #333; color: white;" value="<?php echo $sell->xtotal;?>"  class="form-control" readonly>
    </div>
  </div>



    <div class="col-md-3 col-12">   
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Abono o Total"; break;
 case 'EN': echo "Subscription or Total"; break;
}
?></label>
   <?php $sells = BookingData::getCreditByClientId($sell->person_id,$sell->stock_id,1);

foreach ($sells as $z) {
$tz += PaymentData::getByPayment($z->id)->total;
}
?>
   <input style="background-color: #333; color: white;" value="<?php echo $tz;?>"  class="form-control" readonly>
    </div>
  </div>

    <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Monto Restante"; break;
 case 'EN': echo "Remaining Amount"; break;
}
?></label>
   <?php $sells = BookingData::getCreditByClientId($sell->person_id,$sell->stock_id);

foreach ($sells as $x) {
$tx += PaymentData::sumBySellId2($x->id,$x->stock_id)->total;
}
?>
   <input style="background-color: #333; color: white;" value="<?php echo $tx;?>"  class="form-control" readonly>
    </div>
  </div>

 <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch ($clistock->language){
 case 'ES': echo "Otros Cobros"; break;
 case 'EN': echo "Other Charges"; break;
}
?></label>
     <input style="background-color: #333; color: white;" value="<?php echo $sell->plane;?>"  class="form-control" readonly>
    </div>
  </div>
  </div>
  
   <div  class="card-header col-md-12 col-12 my-2" style="background-color:gray;">
<i class="fa fa-edit"></i> Firma del Cliente:
</div> 
    <div class="contenedor">

    <div class="row">
      <div class="col-md-12">
        <canvas id="draw-canvas" width="380" height="300">
          No tienes un buen navegador.
        </canvas>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
          
       
        
        <input  type="button" class="button btn-danger" id="draw-clearBtn" value="Borrar Firma"></input>


         
            <input style="background-color: #333; color: white;" type="color" id="color" hidden>
            <input style="background-color: #333; color: white;" hidden type="range" id="puntero" min="1" default="1" max="5" width="10%">


      </div>

    </div>

  
    <div hidden class="row">
      <div class="col-md-12">
        <textarea required id="draw-dataUrl" class="form-control" name="base64" rows="5"></textarea>
      </div>
    </div>
  
  
  </div>
  
                    <div class="row">
                   <div class="col-md-6 col-6 my-2">
                  <a href="./?view=booking&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> <?php 
switch ($clistock->language){
 case 'ES': echo "Cancelar"; break;
 case 'EN': echo "Cancel"; break;
}
?></a>
                </div>
                <div class="col-md-6 col-6 my-2">

                   <button type="submit" id="draw-submitBtn" class="btn btn-success btn-block btn-sm "><i class="fa fa-check"></i> <?php 
switch ($clistock->language){
 case 'ES': echo "Finalizar"; break;
 case 'EN': echo "Finish"; break;
}
?></button>
                   
                 <script>
document.getElementById('delivery').addEventListener('submit', function(event) {
    document.getElementById('draw-submitBtn').disabled = true;
});
</script>
                </div>
                </div>
    </div>        
  
  </div>


              </div>
           
              <input style="background-color: #333; color: white;" type="hidden" name="user_id" value="<?php echo $_GET['id'];?>">
     
  
    <script type="text/javascript">
    
    
    document.getElementById("type_iva").style.display = "none";
    document.getElementById("iva_value").style.display = "none"; 
 
 function showIva(){
  var getSelectValue = document.getElementById("iva").value;

  if(getSelectValue==0){
      
    document.getElementById("type_iva").style.display = "none";
    document.getElementById("iva_value").style.display = "none";
     
    function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
    
   
}

$("#remaining").val(agregarSeparadorMiles(parseFloat($("#amount").val())-parseFloat($("#payment").val())));
    
  }else if(getSelectValue==18){

    document.getElementById("type_iva").style.display = "inline-block"; 
    document.getElementById("iva_value").style.display = "inline-block"; 
    function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
    
   
}

$("#remaining").val(agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#amount").val()*0.18))-parseFloat($("#payment").val())));

$("#value_iva").val(agregarSeparadorMiles(+parseFloat($("#amount").val()*0.18)));

}

 }
   
    document.getElementById("cars2_name").style.display = "none";
    document.getElementById("cars2_plate").style.display = "none";
    document.getElementById("cars2_category").style.display = "none";
    document.getElementById("cars2_brand").style.display = "none";
    document.getElementById("cars2_year").style.display = "none"; 
    
  $(document).ready(function(){
    $('#xmount').val();
    recargarxLista();

  })
  
  function recargarxLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
       data: {unit1: $('#unit1').val(), price1:  $('#price1').val(), uni2: $('#unit2').val(), price2:  $('#price2').val(), unit3: $('#unit3').val(), price3:  $('#price3').val(), unit4: $('#unit4').val(), price4:  $('#price4').val()},
      success:function(r){
        $('#xmount').html(r);
      }
    });
  }



function showInp2(){
  var getSelectValue = document.getElementById("type_id").value;

  if(getSelectValue=="1"){
    document.getElementById("end_at1").style.display = "inline-block";
    document.getElementById("end_at2").style.display = "none";
  }
   if(getSelectValue=="2"){
    document.getElementById("end_at2").style.display = "inline-block";
    document.getElementById("end_at1").style.display = "none";
  }
  
}

document.getElementById("end_at2").style.display = "none";

 $(document).ready(function(){
    $('#location').val();
    recargarLista();

    $('#location').change(function(){
      recargarLista();
    });
  })

  function recargarLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=all",
      data:"location=" + $('#location').val(),
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }
  

  $(document).ready(function(){
    $('#select2lista').val();
    recargar2Lista();
  })
  
   $('#cars').change(function(){
 
  recargarDanger();
  recargarExtras();
  
$("#unitx1").val(0);
$("#pricex1").val(0);

$("#unitx2").val(0);
$("#pricex2").val(0);

$("#unitx3").val(0);
$("#pricex3").val(0);

$("#unitx4").val(0);
$("#pricex4").val(0);


$("#xmount").val(0);

   function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
    
   
}

$("#remaining").val(agregarSeparadorMiles(parseFloat($("#amount").val())-parseFloat($("#payment").val())));

    });

  function recargarDanger(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=danger",
      data: {car_id: $('#cars').val()},
      success:function(r){
        $('#danger').html(r);
      }
    });
  }
  
  function recargarExtras(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=extra",
      data: {car_id: $('#cars').val()},
      success:function(r){
        $('#extra').html(r);
      }
    });
  }
  
   function recargar2Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
      data: {stock_id: $('#select2lista').val(),start_at: $('#start_at').val(),end_at: $('#end_at').val()},
      success:function(r){
        $('#cars').html(r);
      }
    });
  }
  
  
 $('#dias').prop('disabled', true);
  
   var inival = $("#end_at").val();
  $("#end_at").change(function(){
  if ( $("#end_at").val() != inival ) {
      
      
 $('#dias').prop('disabled', false);
      
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
     data: {stock_id: $('#select2lista').val(),start_at: $('#start_at').val(),end_at: $('#end_at').val()},
      success:function(r){
        $('#cars').html(r);
      }
    });
    
     $('.warning').hide();
       $('#submit').prop('disabled', true);
    
    function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni),
      hasta = new Date(fechaFin),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return  Math.round((diff / diaEnMils));
}


 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val())+parseFloat($("#xmount").val())));
    
  
  
  }
});



function showMethod(){
    
  var getSelectValue = document.getElementById("method").value;
  var getSelectValue2 = document.getElementById("select2lista").value;

  if(getSelectValue==1){

  recargarLista();
  recargarExtras();
  document.getElementById("extra").style.display = "none";

  function recargarLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=all",
      data:"location=" + getSelectValue,
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }
  
  recargar2Lista();
    
  function recargar2Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
      data:"stock_id=" + getSelectValue2,
      success:function(r){
        $('#cars').html(r);
      }
    });
  }

    
    document.getElementById("rpayment").style.display = "none";
    document.getElementById("stock_id2").style.display = "none"; 
    
    document.getElementById("cars2_name").style.display = "none";
    document.getElementById("cars2_plate").style.display = "none";
    document.getElementById("cars2_category").style.display = "none";
    document.getElementById("cars2_brand").style.display = "none";
    document.getElementById("cars2_year").style.display = "none";
    document.getElementById("cars1").style.display = "inline-block";
  }else if(getSelectValue==2){
      
      
  recargarExtras();
      
  
    document.getElementById("rpayment").style.display = "none";
    document.getElementById("stock_id2").style.display = "none";
    
    document.getElementById("cars1").style.display = "inline-block";
    document.getElementById("cars2_name").style.display = "none";
    document.getElementById("cars2_plate").style.display = "none";
    document.getElementById("cars2_category").style.display = "none";
    document.getElementById("cars2_brand").style.display = "none";
    document.getElementById("cars2_year").style.display = "none";
  }else if(getSelectValue==3){
      

  recargarLista();
  recargarExtras();
  document.getElementById("extra").style.display = "none";
    

  function recargarLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=all",
      data:"location=" + getSelectValue,
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }
  
 
      recargar2Lista();
    
  function recargar2Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
      data:"stock_id=" + getSelectValue2,
      success:function(r){
        $('#cars').html(r);
      }
    });
  }
  

      
    document.getElementById("stock_id2").style.display = "inline-block";
    
    document.getElementById("cars1").style.display = "none";
    document.getElementById("cars2_name").style.display =  "inline-block";
    document.getElementById("cars2_plate").style.display =  "inline-block";
    document.getElementById("cars2_category").style.display =  "inline-block";
    document.getElementById("cars2_brand").style.display =  "inline-block";
    document.getElementById("cars2_year").style.display =  "inline-block";
    
   document.getElementById("rpayment").style.display = "inline-block";  
  }
 
}

    $('#cars').change(function(){
      recargar3Lista();
      
  document.getElementById("extra").style.display = "inline-block";
    });
 

  function recargar3Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
      data:"cars=" + $('#cars').val(),
      success:function(r){
        $('#tariff').html(r);
      }
    });
  }

    $('#tariff').change(function(){
      Lista();

    });

$("#payment").keyup(function(){
function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
 });

function Lista(){
    

  var getSelectValue = document.getElementById("type_id").value;

  if(getSelectValue=="1"){
    
function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
}

   
 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=amount",
       data: {paycar: $('#payment').val(),tcars: $('#tariff').val(),dvs: $('#divisa_id').val(),cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price",
       data: {cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });
$("#remaining").val(($('#tariff').val()*$('#dias').val())*$('#divisa_id').val());

}

 if(getSelectValue=="2"){
    
function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
}

   
 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=free",
      data:"freedate=" + $('#payment_day').val(),
      success:function(r){
        $('#selectdate').html(r);
      }
    });
  
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=amount",
       data: {paycar: $('#payment').val(),tcars: $('#tariff').val(),dvs: $('#divisa_id').val(),cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price",
       data: {cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });
$("#remaining").val(($('#tariff').val()*$('#dias').val())*$('#divisa_id').val());

}
}



    tariff2.addEventListener("keyup", function()
    {   


  var getSelectValue = document.getElementById("type_id").value;

  if(getSelectValue=="1"){
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff2",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val())+parseFloat($("#xmount").val())));
    
        
    }
    
    
    if(getSelectValue=="2"){

    function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
}

   
 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=free",
      data:"freedate=" + $('#payment_day').val(),
      success:function(r){
        $('#selectdate').html(r);
      }
    });
  


    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff2",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles(($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val()));
    
        
    }
    }, false);
 


   
$('#cars').on('change', () => {
    var value = $('#cars').val();
    
    if(value) {
       $('.warning').hide();
       $('#submit').prop('disabled', false);
    }
    

    
});

</script>

 <div hidden id="day"></div>
   
              
<style>

section{
    flex:1;
}



.button {
    background: #3071a9;
    box-shadow: inset 0 -3px 0 rgba(0,0,0,.3);
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 5px;
    margin: 0 15px;
    text-decoration: none;
    color: white;
}

.button:active {
    transform: scale(0.9);
}

.contenedor {
    width: 100%
    margin: 5px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

input[type=range] {
  -webkit-appearance: none;
  margin: 18px 0;

}
input[type=range]:focus {
  outline: none;
}
input[type=range]::-webkit-slider-runnable-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-webkit-slider-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
  -webkit-appearance: none;
  margin-top: -14px;
}
input[type=range]:focus::-webkit-slider-runnable-track {
  background: #367ebd;
}
input[type=range]::-moz-range-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-moz-range-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]::-ms-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  background: transparent;
  border-color: transparent;
  border-width: 16px 0;
  color: transparent;
}
input[type=range]::-ms-fill-lower {
  background: #2a6495;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-fill-upper {
  background: #3071a9;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]:focus::-ms-fill-lower {
  background: #3071a9;
}
input[type=range]:focus::-ms-fill-upper {
  background: #367ebd;
}
</style>
<script>
/*
    El siguiente codigo en JS Contiene mucho codigo
    de las siguietes 3 fuentes:
    https://stipaltamar.github.io/dibujoCanvas/
    https://developer.mozilla.org/samples/domref/touchevents.html - https://developer.mozilla.org/es/docs/DOM/Touch_events
    http://bencentra.com/canvas/signature/signature.html - https://bencentra.com/code/2014/12/05/html5-canvas-touch-events.html
*/

(function() { // Comenzamos una funcion auto-ejecutable

  // Obtenenemos un intervalo regular(Tiempo) en la pamtalla
  window.requestAnimFrame = (function (callback) {
    return window.requestAnimationFrame ||
          window.webkitRequestAnimationFrame ||
          window.mozRequestAnimationFrame ||
          window.oRequestAnimationFrame ||
          window.msRequestAnimaitonFrame ||
          function (callback) {
            window.setTimeout(callback, 1000/60);
            // Retrasa la ejecucion de la funcion para mejorar la experiencia
          };
  })();

  // Traemos el canvas mediante el id del elemento html
  var canvas = document.getElementById("draw-canvas");
  var ctx = canvas.getContext("2d");


  // Mandamos llamar a los Elemetos interactivos de la Interfaz HTML
  var drawText = document.getElementById("draw-dataUrl");
  var drawImage = document.getElementById("draw-image");
  var clearBtn = document.getElementById("draw-clearBtn");
  var submitBtn = document.getElementById("draw-submitBtn");
  clearBtn.addEventListener("click", function (e) {
    // Definimos que pasa cuando el boton draw-clearBtn es pulsado
    clearCanvas();
    drawImage.setAttribute("src", "");
  }, false);
    // Definimos que pasa cuando el boton draw-submitBtn es pulsado
  submitBtn.addEventListener("click", function (e) {
  var dataUrl = canvas.toDataURL();
  drawText.innerHTML = dataUrl;
  drawImage.setAttribute("src", dataUrl);
   }, false);

  // Activamos MouseEvent para nuestra pagina
  var drawing = false;
  var mousePos = { x:0, y:0 };
  var lastPos = mousePos;
  canvas.addEventListener("mousedown", function (e)
  {
    /*
      Mas alla de solo llamar a una funcion, usamos function (e){...}
      para mas versatilidad cuando ocurre un evento
    */
    var tint = document.getElementById("color");
    var punta = document.getElementById("puntero");
    console.log(e);
    drawing = true;
    lastPos = getMousePos(canvas, e);
  }, false);
  canvas.addEventListener("mouseup", function (e)
  {
    drawing = false;
  }, false);
  canvas.addEventListener("mousemove", function (e)
  {
    mousePos = getMousePos(canvas, e);
  }, false);

  // Activamos touchEvent para nuestra pagina
  canvas.addEventListener("touchstart", function (e) {
    mousePos = getTouchPos(canvas, e);
    console.log(mousePos);
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var touch = e.touches[0];
    var mouseEvent = new MouseEvent("mousedown", {
      clientX: touch.clientX,
      clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchend", function (e) {
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var mouseEvent = new MouseEvent("mouseup", {});
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchleave", function (e) {
    // Realiza el mismo proceso que touchend en caso de que el dedo se deslice fuera del canvas
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var mouseEvent = new MouseEvent("mouseup", {});
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchmove", function (e) {
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var touch = e.touches[0];
    var mouseEvent = new MouseEvent("mousemove", {
      clientX: touch.clientX,
      clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
  }, false);

  // Get the position of the mouse relative to the canvas
  function getMousePos(canvasDom, mouseEvent) {
    var rect = canvasDom.getBoundingClientRect();
    /*
      Devuelve el tamaño de un elemento y su posición relativa respecto
      a la ventana de visualización (viewport).
    */
    return {
      x: mouseEvent.clientX - rect.left,
      y: mouseEvent.clientY - rect.top
    };
  }

  // Get the position of a touch relative to the canvas
  function getTouchPos(canvasDom, touchEvent) {
    var rect = canvasDom.getBoundingClientRect();
    console.log(touchEvent);
    /*
      Devuelve el tamaño de un elemento y su posición relativa respecto
      a la ventana de visualización (viewport).
    */
    return {
      x: touchEvent.touches[0].clientX - rect.left, // Popiedad de todo evento Touch
      y: touchEvent.touches[0].clientY - rect.top
    };
  }

  // Draw to the canvas
  function renderCanvas() {
    if (drawing) {
      var tint = document.getElementById("color");
      var punta = document.getElementById("puntero");
      ctx.strokeStyle = tint.value;
      ctx.beginPath();
      ctx.moveTo(lastPos.x, lastPos.y);
      ctx.lineTo(mousePos.x, mousePos.y);
      console.log(punta.value);
      ctx.lineWidth = punta.value;
      ctx.stroke();
      ctx.closePath();
      lastPos = mousePos;
    }
  }

  function clearCanvas() {
    canvas.width = canvas.width;
  }

  // Allow for animation
  (function drawLoop () {
    requestAnimFrame(drawLoop);
    renderCanvas();
  })();

})();    
</script>
<style>

#draw-canvas {
  border: 2px dotted #CCCCCC;
  border-radius: 5px;
  cursor: crosshair;
  background-image: url("CF-SYSTEMS/storage/configuration/fxrma.png");
  background-size: 100% 100%;
}

#draw-dataUrl {
  width: 100%;
}

</style>
</form>
  
            </div>

            </div>
            </div>
</div>

</div>

</section>
<?php endif; ?>