<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
<div class="row">
	<div class="col-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <a href="./?view=box&opt=history" style="color: white;" title=" Click para Historial"> <h1 class="m-0"><i class='fa fa-shopping-cart'></i> Corte de Caja</h1></a>
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

		
<p>Al procesar ventas se generara un corte de caja para todas las ventas del Sucursal: <b><?php echo StockData::getPrincipal()->name;?></b></p> 

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
$products = BookingData::getSellsUnBoxed(StockData::getPrincipal()->id);
$prospends = SpendData::getAllUnBoxed(StockData::getPrincipal()->id);
$promaint = MaintenanceData::getAllUnBoxed();
if(count($products)>0  || count($prospends)>0 || count($promaint)>0):?>
 
   <a href="javascript:void()" id="process" class="btn btn-warning btn-block btn-sm ">Crear Corte de Caja <i class="fa fa-check"></i></a>
   <br>
   <script type="text/javascript">
	$("#process").click(function(){
		x = confirm("Estas seguro que deseas continuar?")
		if(x){
			window.location = "./index.php?action=process&opt=box";
		}
	});
</script>
<?php endif;  $total_total = 0;?>
<div class="card" style="background-color:#222;">
 <div class="card-header">
                <h2 class="card-title"><i class="fa fa-car"></i> Rentas:</h2>
              </div>
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" >
	<thead>
		<th>Vehiculo</th>
		<th>Total</th>
		<th>Vendedor</th>
		<th>Sucursal</th>
		<th>Fecha</th>
	</thead>
	<tfoot>
		<th>Vehiculo</th>
		<th>Total</th>
		<th>Vendedor</th>
		<th>Sucursal</th>
		<th>Fecha</th>
	</tfoot>
	<?php foreach($products as $sell):?>

	<tr>
	<td><?php $brand = BrandData::getById($sell->getCars()->brand_id); echo $brand->name." ".$sell->getCars()->name." ".$sell->getCars()->year." - ".$sell->getCars()->chassis; ?></td>
		
		<td>

<?php $total_total += $sell->total;
echo "<b>".Core::$symbol." ".number_format($sell->total,2,".",",")."</b>";?>			

		</td>
		<td>
			<?php
			$u = UserData::getById($sell->user_id);
			echo $u->name." ".$u->lastname;
			?>
		</td>
		<td><?php echo $sell->getStock()->name; ?></td>
		<td><?php echo date("d-m-Y h:i:s a", strtotime($sell->created_at)); ?></td>
	</tr>

<?php endforeach; ?>

</table>

<table class="table table-bordered">
	<thead>
		<th>Total:</th>
		<th><?php echo Core::$symbol." ".number_format($total_total,2,".",","); ?></th>
	</thead>
</table>
</div>
</div>
</div>

<?php $users = SpendData::getAllUnBoxed(StockData::getPrincipal()->id);
		if(count($users)>0):
			$total = 0;?>
<div class="card" style="background-color:#222;">
 <div class="card-header">
                <h2 class="card-title"><i class='fa fa-minus-square'></i> Gastos:</h2>
              </div>
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" >
			<thead>
			<th>Tipo</th>
			<th>Concepto</th>
			<th>Costo</th>
			<th>Fecha</th>
			</thead>
			<tfoot>
			<th>Tipo</th>
			<th>Concepto</th>
			<th>Costo</th>
			<th>Fecha</th>
			</tfoot>
			<?php
			foreach($users as $user){
				?>
				<tr>
					<td><?php 
					if($user->kind==1){ echo "<span class='label label-success'>Gasto</span>"; } 

					?></td>
				<td><?php echo utf8_decode($user->name); ?></td>
				<td><?php echo Core::$symbol; ?> <?php echo number_format($user->price,2,".",","); ?></td>
				<td><?php echo $user->created_at; ?></td>
				</tr>
				<?php
				$total+=$user->price;

			}?>

</table>
<table class="table table-bordered" >
	<thead>
		<th>Total:</th>
		<th><?php echo Core::$symbol." ".number_format($total,2,".",","); ?></th>
	</thead>
</table>
</div>
</div>
</div>
<?php endif; ?>
<?php $users = MaintenanceData::getAllUnBoxed();
if(count($users)>0):
$total = 0;?>

              <div class="card" style="background-color:#222;">
 <div class="card-header">
                <h2 class="card-title"><i class='fa fa-cog'></i> Mantenimiento:</h2>
              </div>
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" >
			<thead>
			<th>Concepto</th>
			<th>Costo</th>
			<th>Fecha</th>
			</thead>
			<?php
			foreach($users as $user){
				?>
				<tr>
				<td><?php echo utf8_decode($user->maintenance); ?></td>
				<td><?php echo Core::$symbol; ?> <?php echo number_format($user->total,2,".",","); ?></td>
				<td><?php echo $user->created_at; ?></td>
				</tr>
				<?php
				$total+=$user->price;

			}?>

</table>
</div>

<table class="table table-bordered" >
	<thead>
		<th>Total:</th>
		<th><?php echo Core::$symbol." ".number_format($total,2,".",","); ?></th>
	</thead>
</table>
</div>
</div>
</div>
<?php  endif; ?>

</div>
</div>
  </div><!-- /.row -->
      </div><!-- /.container-fluid -->
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="history"):?>

<section class="content">
<div class="row">
	<div class="col-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><a href="./?view=box&opt=all" style="color: white;" title=" Click para Historial"><i class='fa fa-arrow-left'></i></a>&nbsp;<i class='fa fa-history'></i> Historial de Caja
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



<form id="filterboxhistory">
	<input type="hidden" name="view" value="boxhistory">
<div class="row">

	<div class="col-md-3 col-4">
		<label>Fecha inicio:</label>
		<input type="date" name="start_at" value="<?php echo date('Y-m-d',(time() -(60*60*24*7))); ?>" required class="form-control">
	</div>
	<div class="col-md-3 col-4">
		<label>Fecha fin:</label>
		<input type="date" name="finish_at" value="<?php echo date("Y-m-d", strtotime('-6 hours')); ?>" required class="form-control">
	</div>
	<div class="col-md-3 col-4">
		<label>Aplicar Filtro:</label><br>
		<input type="submit" value="Aplicar Filtro" class="btn btn-primary">
	</div>

</div>
</form>

<br>
<div class="allfilterboxhistory"></div>

<script type="text/javascript">
	$(document).ready(function(){
		$.get("./?action=filter&opt=boxhistory",$("#filterboxhistory").serialize(),function(data){
			$(".allfilterboxhistory").html(data);
		});

		$("#filterboxhistory").submit(function(e){
			e.preventDefault();
		$.get("./?action=filter&opt=boxhistory",$("#filterboxhistory").serialize(),function(data){
			$(".allfilterboxhistory").html(data);
		});

		})
	});
</script>
	</div>
</div>
	</div>
</div>
</section>

<?php endif; ?>

