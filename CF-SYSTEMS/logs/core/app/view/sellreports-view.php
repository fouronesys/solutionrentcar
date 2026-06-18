
<?php
$iva_name = StockData::getPrincipal()->imp_name;
$iva_val = StockData::getPrincipal()->imp_val;
?>
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-copy"></i> Reportes de Renta
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

            <form>
            <input type="hidden" name="view" value="sellreports">
<div class="row">

<div class="col-md-2 col-6">

<select  style="background-color: #333;" name="user_id" class="form-control select2">
  <option value="">-- USUARIO --</option>
  <?php if(Core::$user->kind==1):?>
  <?php foreach(UserData::getAllbySQL("where stock_id=".StockData::getPrincipal()->id." and username!='krtavarez'") as $p):?>
  <option value="<?php echo $p->id;?>"><?php echo $p->name." ".$p->lastname;?></option>
  <?php endforeach; 
else: 
foreach(UserData::getAllbySQL("where stock_id=".StockData::getPrincipal()->id) as $p):
if($p->stock_id==Core::$user->stock_id):?>
<option value="<?php echo $p->id;?>"><?php echo $p->name." ".$p->lastname;?></option>
<?php endif; endforeach; endif; ?>
</select>

</div>
<div class="col-md-3 col-6">

<select style="background-color: #333;" name="client_id" class="form-control select2">
  <option value="">-- VEHICULO --</option>
  <?php foreach(CarsData::getAllbySQL("where stock_id=".StockData::getPrincipal()->id) as $p):?>
  <option value="<?php echo $p->id;?>"><?php $brand= BrandData::getById($p->brand_id);
  echo $brand->name." ".$p->name." ".$p->year." ".$p->getExColor()->name." - ".$p->chassis;?>
  </option>
  <?php endforeach; ?>
</select>

</div>

<div class="col-md-3 col-6">

<input style="background-color: #222;" type="date" name="sd" value="<?php if(isset($_GET["sd"])){ echo $_GET["sd"]; }else{ echo date('Y-m-d',(time() -(60*60*24*30) ));}?>" class="form-control">
</div>
<div class="col-md-3 col-6">

<input style="background-color: #222;" type="date" name="ed" value="<?php if(isset($_GET["ed"])){ echo $_GET["ed"]; }else{ echo date("Y-m-d", strtotime('-6 hours'));}?>" class="form-control">
</div>

<div class="col-md-1">

<button type="submit" class="btn btn-warning btn-block"><i class="fa fa-search"></i></button>
</div>

</div>

</form>

<br><!--- -->
<div class="row">
  
  <div class="col-md-12">
    <?php if(isset($_GET["sd"]) && isset($_GET["ed"]) ):?>
<?php if($_GET["sd"]!=""&&$_GET["ed"]!=""):?>
      <?php 
      $operations = array();

      if($_GET["client_id"]=="" && $_GET["user_id"]==""){
      $operations = BookingData::getAllByDateOp($_GET["sd"],$_GET["ed"],StockData::getPrincipal()->id);
      }
      else if($_GET["client_id"]=="" && $_GET["user_id"]!=""){
      $operations = BookingData::getAllByDateOpByUserId($_GET["user_id"],$_GET["sd"],$_GET["ed"],StockData::getPrincipal()->id);
      }
      else if($_GET["client_id"]!="" && $_GET["user_id"]==""){
      $operations = BookingData::getAllByDateBCOp($_GET["client_id"],$_GET["sd"],$_GET["ed"],StockData::getPrincipal()->id);
      }else{
      $operations = BookingData::getAllByDateBCOpByUserId($_GET["user_id"],$_GET["client_id"],$_GET["sd"],$_GET["ed"],StockData::getPrincipal()->id);
      } 


       ?>

       <?php if(count($operations)>0):?>
        <?php $supertotal = 0; ?>
  
 <div class="card" style="background-color: #222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
  <thead>
    <th>Id</th>
    <th>Vehiculo</th>
    <th>Clientes</th>
    <th>Vendedor</th>
    <th>Total Neto</th>
    <th>ITBIS</th>
    <th>Total</th>
    <th>Fecha</th>
  </thead>
  <tfoot>
    <th>Id</th>
    <th>Vehiculo</th>
    <th>Clientes</th>
    <th>Vendedor</th>
    <th>Total Neto</th>
    <th>ITBIS</th>
    <th>Total</th>
    <th>Fecha</th>  
  </tfoot>
<?php foreach($operations as $operation):?>
  <tr>
    <td><?php echo $operation->id; ?></td>
  <td> <?php if($operation->car_id!=null){$brand= BrandData::getById($operation->getCars()->brand_id);$c= $operation->getCars();echo $brand->name." ".$c->name." ".$c->year." ".$c->getExColor()->name." - ".$c->chassis;} ?> </td>
  <td> <?php if($operation->person_id!=null){$c= $operation->getPerson();echo $c->name;} ?> </td>
  <td> <?php if($operation->user_id!=null){$c= $operation->getUser();echo $c->name." ".$c->lastname;} ?> </td>
    <td><?php echo Core::$symbol; ?> <?php echo number_format($operation->total/(1 + ($iva_val/100) ),2,'.',','); ?></td>
    <td><?php echo Core::$symbol; ?> <?php echo number_format(($operation->total/1.18)*$iva_val/100,2,'.',','); ?></td>
    <td><?php echo Core::$symbol; ?> <?php echo number_format($operation->total,2,'.',','); ?></td>
    <td><?php echo date("d-m-Y h:i:s a", strtotime($operation->created_at)); ?></td>
  </tr>
<?php
$supertotal+= ($operation->total);
 endforeach; ?>

</table>
</div>
<table class="table table-bordered">
  <thead>
    <th>Total:</th>
    <th><?php echo Core::$symbol." ".number_format($supertotal,2,".",","); ?></th>
  </thead>
</table>
</div>
</div>
</div>

<?php else:
// si no hay operaciones
?>
  <div class="card" style="background-color: #222;">
              <div class="card-header">
  <h2>No hay operaciones</h2>
  <p>El rango de fechas seleccionado no proporciono ningun resultado de operaciones.</p>
</div>
</div>
       <?php endif; ?>
<?php else:?>

  <div class="card" style="background-color: #222;">
              <div class="card-header">
  <h2>Fecha Incorrectas</h2>
  <p>Puede ser que no selecciono un rango de fechas, o el rango seleccionado es incorrecto.</p>
</div>
</div>
<?php endif;?>

    <?php endif; ?>
  </div>
</div>
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
<script type="text/javascript">
    $("#example1").DataTable();
</script>
</section>
