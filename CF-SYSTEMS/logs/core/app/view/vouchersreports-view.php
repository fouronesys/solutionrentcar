
<section class="content">
<div class="row">
	<div class="col-md-12">
		 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
         <h1 class="m-0"><i class="fa fa-copy"></i> Reportes Comprobantes DGII</h1>
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
<input type="hidden" name="view" value="vouchersreports">
<div class="row">
<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
<select style="background-color: #333;" name="user_id" class="form-control select2">
	<option value="">-- EMPLEADO --</option>
	<?php if(Core::$user->kind==1):?>
	<?php foreach(UserData::getAll() as $p):?>
	<option value="<?php echo $p->id;?>"><?php echo $p->name;?></option>
	<?php endforeach; ?>
	<?php else:?>
	<?php foreach(UserData::getAll() as $p):?>
		<?php if($p->stock_id==Core::$user->stock_id):?>
	<option value="<?php echo $p->id;?>"><?php echo $p->name;?></option>
<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
</select>

</div>
<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
<select style="background-color: #333;" name="c_id" class="form-control select2">
	<option value="">-- COMPROBANTE --</option>
	<?php foreach($cp as $p):?>
	<option value="<?php echo $p->id;?>"><?php echo $p->name;?></option>
	<?php endforeach; ?>
</select>

</div>

<div class="col-6 col-sm-2 col-md-2 col-lg-2 col-xl-2">
<input style="background-color: #222;" type="date" name="sd" value="<?php if(isset($_GET["sd"])){ echo $_GET["sd"]; }else{ echo date('Y-m-d',(time() -(60*60*24*30) ));}?>" class="form-control">
</div>
<div class="col-6 col-sm-2 col-md-2 col-lg-2 col-xl-2">
<input style="background-color: #222;" type="date" name="ed" value="<?php if(isset($_GET["ed"])){ echo $_GET["ed"]; }else{ echo date("Y-m-d", strtotime('-6 hours'));}?>" class="form-control">
</div>

<div class="col-12 col-sm-2 col-md-2 col-lg-1 col-xl-1">
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

			if($_GET["c_id"]=="" && $_GET["user_id"]==""){
			$operations = BookingData::getAllByDateOp2($_GET["sd"],$_GET["ed"],2);
			}
			else if($_GET["c_id"]=="" && $_GET["user_id"]!=""){
			$operations = BookingData::getAllByDateOpByUserId2($_GET["user_id"],$_GET["sd"],$_GET["ed"],2);
			}
			else if($_GET["c_id"]!="" && $_GET["user_id"]==""){
			$operations = BookingData::getAllByDateBCOp2($_GET["c_id"],$_GET["sd"],$_GET["ed"],2);
			}else{
			$operations = BookingData::getAllByDateBCOpByUserId2($_GET["user_id"],$_GET["c_id"],$_GET["sd"],$_GET["ed"],2);
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
		<th>Total</th>
		<th>Cliente</th>
		<th>Vendedor</th>
		<th>Comprobante</th>
		<th>Fecha</th>
	</thead>
<?php foreach($operations as $operation):?>
	<tr>
		<td><?php echo $operation->id; ?></td>
		<td><?php echo Core::$symbol; ?> <?php echo number_format($operation->total-$operation->discount,2,'.',','); ?></td>
	<td> <?php if($operation->person_id!=null){$c= $operation->getPerson();echo $c->name." ".$c->lastname;}?> </td>

	<td> <?php if($operation->user_id!=null){$c= $operation->getUser();echo $c->name." ".$c->lastname;} ?> </td>


	<td><?php echo $operation->getC()->name;?>	</td>
		<td><?php echo date("d-m-Y h:i:s a", strtotime($operation->created_at)); ?></td>
	</tr>
<?php
$supertotal+= ($operation->total-$operation->discount);
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
