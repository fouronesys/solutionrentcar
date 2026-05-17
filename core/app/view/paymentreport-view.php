
<section class="content">
<div class="row">
	<div class="col-12">
  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
         <h1 class="m-0"><i class="fa fa-copy"></i> Reporte de Pagos de Clientes
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
						<input type="hidden" name="view" value="paymentreport">

<div class="row">
<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
<?php if (Core::$user->kind==1):?>
	<select style="background-color: #333;" name="stock_id" class="form-control select2">
	<option value="">--  SUCURSAL --</option>
	<?php foreach(StockData::getAllBySQL("where id=".StockData::getPrincipal()->id) as $p):?>
	<option value="<?php echo $p->id;?>"><?php echo $p->name." ".$p->lastname;?></option>
	<?php endforeach; ?>
</select>
<?php else:?>
<select style="background-color: #333;" name="stock_id" class="form-control select2">
	<option value="<?php echo StockData::getPrincipal()->id;?>"><?php echo StockData::getPrincipal()->name;?></option>
</select>
<?php endif;?></div>
<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
<select style="background-color: #333;" name="client_id" class="form-control select2">
	<option value="">--  TODOS --</option>
	<?php foreach(PersonData::getALLBySQL("where stock_id=".StockData::getPrincipal()->id) as $p):?>
	<option value="<?php echo $p->id;?>"><?php echo $p->name." ".$p->lastname;?></option>
	<?php endforeach; ?>
</select>

</div>
<div class="col-6 col-sm-2 col-md-2 col-lg-2 col-xl-2">
<input style="background-color: #222;" type="date" name="sd" value="<?php if(isset($_GET["sd"])){ echo $_GET["sd"]; }else{echo date("Y-m-d", strtotime('-6 hours'));}?>" class="form-control">
</div>
<div class="col-6 col-sm-2 col-md-2 col-lg-2 col-xl-2">
<input style="background-color: #222;" type="date" name="ed" value="<?php if(isset($_GET["ed"])){ echo $_GET["ed"]; }else{echo date("Y-m-d", strtotime('-6 hours'));}?>" class="form-control">
</div>

<div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2">
<button type="submit" class="btn btn-warning btn-block"><i class="fa fa-search"></i></button>
</div>

</div>
</form>


<br><!--- -->
<div class="row">
	
	<div class="col-12">
		<?php if(isset($_GET["sd"]) && isset($_GET["ed"]) ):?>
<?php if($_GET["sd"]!=""&&$_GET["ed"]!=""):?>
			<?php 
			$operations = array();
			if(isset($_GET["client_id"]) && $_GET["client_id"]!=""){
			$operations = PaymentData::getAllByDateAndClient($_GET["sd"],$_GET["ed"],$_GET["client_id"],StockData::getPrincipal()->id);

			}elseif(isset($_GET["stock_id"]) && $_GET["stock_id"]!=""){
			$operations = PaymentData::getAllByDateAndStock($_GET["sd"],$_GET["ed"],$_GET["stock_id"]);
      }else{
				$operations = PaymentData::getAllByDate($_GET["sd"],$_GET["ed"],StockData::getPrincipal()->id);

			}
			 ?>

			 <?php if(count($operations)>0):?>
<?php $t=0; foreach($operations as $operation){ $t+=$operation->val; }?>

 <div class="card" style="background-color: #222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
	<thead>
		<th>Cliente</th>
		<th>Valor</th>
		<th>Fecha</th>
	</thead>
<?php foreach($operations as $operation):?>
	<tr>
		<td><?php $c= $operation->getClient();echo $c->name." ".$c->lastname; ?></td>
		<td><?php echo Core::$symbol; ?> <?php echo number_format(abs($operation->val),2,".",","); ?></td>
		<td><?php echo $operation->created_at; ?></td>
	</tr>
<?php endforeach; ?>

</table>
</div>
<table class="table table-bordered">
	<thead>
		<th>Total:</th>
		<th><?php echo Core::$symbol." ".number_format(abs($t),2,".",","); ?></th>
	</thead>
</table>

</div>
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
</section>
<script type="text/javascript">
    $("#example1").DataTable();
</script>


