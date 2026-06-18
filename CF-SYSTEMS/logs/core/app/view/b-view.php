<?php 
$TicketMm = StockData::getPrincipal()->ticket_mm;
?>
<section class="content">
<div class="row">
	<div class="col-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><a href="./?view=box&opt=history" style="color: white;" title=" Click para Historial"><i class='fa fa-arrow-left'></i></a>&nbsp;<i class='fa fa-history'></i> <a style="color: white;"  href="<?php echo $TicketMm; ?>/ticket-b.php?id=<?php echo $_GET["id"];?>">Corte de Caja #<?php echo $_GET["id"]; ?></a></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-briefcase'></i> Finanzas</a></li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Corte Caja</a></li>

            </ol>
          </div><!-- /.col -->
      
    </div>

 <div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para la impresión en la parte superior del apartado para probarlo.
            </div>


<?php
$products = BookingData::getByBoxId($_GET["id"]);
if(count($products)>0):
$total_total = 0;
?>

<div class="card">
 <div class="card-header">
                <h2 class="card-title"><i class="fa fa-shopping-cart"></i> Ventas:</h2>
              </div>
<div class="card-body">
<table class="table table-bordered">
	<thead>
	 	<th>Vehiculo</th>
		<th>Total</th>
		<th>Fecha</th>
	</thead>
	<?php foreach($products as $sell):?>

	<tr>
	
	<td><?php $brand = BrandData::getById($sell->getCars()->brand_id); echo $brand->name." ".$sell->getCars()->name." ".$sell->getCars()->year." - ".$sell->getCars()->chassis; ?></td>
		
		<td>

<?php
		$total_total += $sell->total;
		echo "<b>".Core::$symbol." ".number_format($sell->total,2,".",",")."</b>";

?>			

		</td>
		<td><?php echo date("d-m-Y h:i:s a", strtotime($sell->created_at)); ?></td>
	</tr>

<?php endforeach; ?>

</table>

<div class='box-body'><h3>Total: <?php echo Core::$symbol." ".number_format($total_total,2,".",","); ?></h3></div>
</div>
</div>
<?php endif; ?>

		<?php
		$users = SpendData::getSpendsByBoxId($_GET["id"]);
		if(count($users)>0):
			// si hay usuarios
			$total = 0;
			?>
<div class="card">
 <div class="card-header">
                <h2 class="card-title"><i class='fa fa-minus-square'></i> Gastos:</h2>
              </div>
<div class="card-body">
<table class="table table-bordered" >
	<thead>
		<th>Tipo</th>
		<th>Concepto</th>
			<th>Costo</th>
			<th>Fecha</th>
	</thead>
	<?php foreach($users as $user):?>

	<tr>
<td><?php 
if($user->kind==1){ echo "<span class='label label-success'>Gasto</span>"; } 
?></td>
<td><?php echo utf8_decode($user->name); ?></td>
<td><?php echo Core::$symbol; ?> <?php echo number_format($user->price,2,".",","); ?></td>
<td><?php echo $user->created_at; $total+=$user->price; ?></td>
	</tr>

<?php endforeach; ?>
</table>
<div class='box-body'><h3>Total: <?php echo Core::$symbol." ".number_format($total,2,".",","); ?></h3></div>
</div>
</div>
<?php endif;?>


		<?php $users = MaintenanceData::getByBoxId($_GET["id"]);
		if(count($users)>0):
			// si hay usuarios
			$total = 0;?>
              <div class="card">
 <div class="card-header">
                <h2 class="card-title"><i class='fa fa-cog'></i> Mantenimiento:</h2>
              </div>
<div class="card-body">
<table class="table table-bordered" >
	<thead>
		<th>Vehiculo</th>
		<th>Concepto</th>
			<th>Costo</th>
			<th>Fecha</th>
	</thead>
	<?php foreach($users as $user):?>

	<tr>
<?php $total_income += $user->total;?>

	<td><?php $brand = BrandData::getById($user->getCars()->brand_id); echo $brand->name." ".$user->getCars()->name." ".$user->getCars()->year." - ".$user->getCars()->chassis; ?></td>

<td><?php echo utf8_decode($user->maintenance); ?></td>
<td><?php echo Core::$symbol; ?> <?php echo number_format($user->total,2,".",","); ?></td>
<td><?php echo $user->created_at; ?></td>
	</tr>

<?php endforeach; ?>
</table>
<div class='box-body'><h3>Total: <?php echo Core::$symbol." ".number_format($total_income,2,".",","); ?></h3></div>
</div>
</div>
<?php endif;?>


	</div>
</div>
</div>
</div>

</section>

