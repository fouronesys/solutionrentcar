
<?php
$clients = PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id);
$users = UserData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id." and username!='krtavarez'");
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
           <h1 class="m-0"><i class="fa fa-copy"></i> Reportes de Gastos
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-copy'></i> Reportes</a></li>
              <li class="breadcrumb-item "><i class='far fa-circle'></i> Gastos</a></li>
              
            </ol>
          </div><!-- /.col -->

    </div>



						<form>
						<input type="hidden" name="view" value="spendsreports">
<div class="row">

<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">

<select style="background-color: #333;" name="user_id" class="form-control select2">
	<option value="">-- USUARIO --</option>
	<?php if(Core::$user->kind==1):?>
	<?php foreach($users as $p):?>
	<option value="<?php echo $p->id;?>"><?php echo $p->name." ".$p->lastname;?></option>
	<?php endforeach; ?>
	<?php else:?>
	<?php foreach($users as $p):?>
		<?php if($p->stock_id==Core::$user->stock_id):?>
	<option value="<?php echo $p->id;?>"><?php echo $p->name." ".$p->lastname;?></option>
<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
</select>

</div>


<div class="col-6 col-sm-2 col-md-2 col-lg-2 col-xl-2">

<input style="background-color: #222;" type="date" name="sd" value="<?php if(isset($_GET["sd"])){ echo $_GET["sd"]; }else{ echo date('Y-m-d',(time() -(60*60*24*30) ));}?>" class="form-control">
</div>
<div class="col-6 col-sm-2 col-md-2 col-lg-2 col-xl-2">

<input style="background-color: #222;" type="date" name="ed" value="<?php if(isset($_GET["ed"])){ echo $_GET["ed"]; }else{ echo date("Y-m-d", strtotime('-6 hours'));}?>" class="form-control">
</div>

<div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2">

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

			if($_GET["user_id"]==""){
			$operations = SpendData::getAllByDateOp($_GET["sd"],$_GET["ed"],StockData::getPrincipal()->id);
			}
			else if($_GET["client_id"]=="" && $_GET["user_id"]!=""){
			$operations = SpendData::getAllByDateOpByUserId($_GET["user_id"],$_GET["sd"],$_GET["ed"],StockData::getPrincipal()->id);
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
      <th>Proveedor</th>
      <th>Pago</th>
      <th>Concepto</th>
      <th>Monto</th>
      <th>Fecha</th>
	</thead>
	<tfoot>
		<th>Id</th>
      <th>Proveedor</th>
      <th>Pago</th>
      <th>Concepto</th>
      <th>Monto</th>
      <th>Fecha</th>
	</tfoot>
<?php foreach($operations as $operation):?>
	<tr>
		<td><?php echo $operation->id; ?></td>
         <td><?php echo $operation->person_id;?></td>
          <td><?php $p= $operation->getF(); echo $p->name;?></td>
         <td><?php echo $operation->name; ?></td>
          <td><?php echo Core::$symbol; ?> <?php echo number_format($operation->price,2,".",","); ?></td>
          <td><?php echo date("d-m-Y", strtotime($operation->created_at)); ?></td>
    
	</tr>
<?php
$supertotal+= ($operation->price);
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