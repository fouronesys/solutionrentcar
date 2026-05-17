
<section class="content">
<div class="row">
	<div class="col-12">
	<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-copy"></i> Reportes <small>[Clientes Populares]</small></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-copy'></i> Reportes</a></li>
              <li class="breadcrumb-item "><i class='far fa-circle'></i> Clientes Populares</a></li>
              
            </ol>
          </div><!-- /.col -->
    </div>
<form>
<input type="hidden" name="view" value="clientreports">
<div class="row">
<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
<?php if (Core::$user->kind==1):?>
	<select style="background-color: #333;" name="stock_id" class="form-control select2">
	<option value="">-- SUCURSAL --</option>
	<?php foreach(StockData::getAllBySQL("where id=".StockData::getPrincipal()->id) as $p):?>
	<option value="<?php echo $p->id;?>"><?php echo $p->name." ".$p->lastname;?></option>
	<?php endforeach; ?>
</select>
<?php else:?>
<select style="background-color: #333;" name="stock_id" class="form-control select2">
	<option value="<?php echo StockData::getPrincipal()->id;?>"><?php echo StockData::getPrincipal()->name;?></option>
</select>
<?php endif;?></div>

<div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2">
<input style="background-color: #222;" type="date" name="sd" value="<?php if(isset($_GET["sd"])){ echo $_GET["sd"]; }else{echo date('Y-m-d',(time() -(60*60*24*30) ));}?>" class="form-control">
</div>
<div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2">
<input style="background-color: #222;" type="date" name="ed" value="<?php if(isset($_GET["ed"])){ echo $_GET["ed"]; }else{echo date("Y-m-d", strtotime('-6 hours'));}?>" class="form-control">
</div>

<div class="col-6 col-sm-1 col-md-1 col-lg-1 col-xl-1">
<button type="submit" class="btn btn-warning btn-block"><i class="fa fa-search"></i></button>
</div>

</div>

</form>

<br><!--- -->
<div class="row">
	
	<div class="col-12">
		<?php if(isset($_GET["sd"]) && isset($_GET["ed"]) ):?>
<?php if($_GET["sd"]!="" && $_GET["ed"]!=""):?>
			<?php 
			$operations = array();
      if ($_GET["stock_id"]!="") {
      		$operations = BookingData::getSQL("select SQL_BIG_RESULT *,sum(total) as st from booking where date(created_at) >= \"$_GET[sd]\" and date(created_at) <= \"$_GET[ed]\" and status=3 and stock_id=\"$_GET[stock_id]\" group by person_id order by st desc");
      }else{
			$operations = BookingData::getSQL("select SQL_BIG_RESULT *,sum(total) as st from booking where date(created_at) >= \"$_GET[sd]\" and date(created_at) <= \"$_GET[ed]\" and status=3 and stock_id=\"$_GET[stock_id]\" group by person_id order by st desc");
		}?>

			 <?php if(count($operations)>0):?>
			 	<?php $supertotal = 0; ?>

 <div class="card" style="background-color: #222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
	<thead>
		<th>Id Cliente</th>
		<th>Total Renta</th>
		<th>Cliente</th>
	</thead>
<?php foreach($operations as $operation):?>
	<tr>
		<td><?php echo $operation->person_id; ?></td>
		<td><?php echo Core::$symbol; ?> <?php echo number_format($operation->st,2,'.',','); ?></td>
	<td> <?php if($operation->person_id!=null){$c= $operation->getPerson();echo $c->name." ".$c->lastname;} ?> </td>
	</tr>
<?php
//$supertotal+= ($operation->total-$operation->discount);
 endforeach; ?>

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

