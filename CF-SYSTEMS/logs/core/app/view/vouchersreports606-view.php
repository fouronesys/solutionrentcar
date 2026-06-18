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
         <h1 class="m-0"><i class="fa fa-copy"></i> Reportes Comprobantes DGII 606 </h1>
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
						<input type="hidden" name="view" value="vouchersreports606">
<div class="row">
<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">

<select name="c_id" class="form-control select2">
	<option value="">-- COMPROBANTE --</option>
	<?php foreach(CData::getAllBySQL("where location=606") as $p):?>
	<option value="<?php echo $p->id;?>"><?php echo $p->name;?></option>
	<?php endforeach; ?>
</select>

</div>

<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">

<input style="background-color: #222;" type="date" name="sd" value="<?php if(isset($_GET["sd"])){ echo $_GET["sd"]; }else{ echo date('Y-m-d',(time() -(60*60*24*30) ));}?>" class="form-control">
</div>
<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">

<input style="background-color: #222;" type="date" name="ed" value="<?php if(isset($_GET["ed"])){ echo $_GET["ed"]; }else{ echo date("Y-m-d", strtotime('-6 hours'));}?>" class="form-control">
</div>

<div class="col-6 col-sm-3 col-md-3 col-lg-1 col-xl-1">
<button type="submit" class="btn btn-warning btn-block"><i class="fa fa-search"></i></button>
</div>

</div>

</form>

<br><!--- -->
<div class="row">
	
	<div class="col-md-12">
<?php if(isset($_GET["sd"]) && isset($_GET["ed"]) ):
if($_GET["sd"]!=""&&$_GET["ed"]!=""):

			$operations = array();

			if($_GET["c_id"]==""){
			$operations = SpendData::getAllByDateOp2($_GET["sd"],$_GET["ed"],StockData::getPrincipal()->id);
			}
			else if($_GET["c_id"]<>""){
			$operations = SpendData::getAllByDateBCOp2($_GET["c_id"],$_GET["sd"],$_GET["ed"],StockData::getPrincipal()->id);
			}


 if(count($operations)>0 || count($products)>0):
 $supertotal = 0; ?>
			 
 <div class="card" style="background-color: #222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
	<thead>
		<th>Id</th>
		<th style="width:100%">RNC o Cedula</th>
		<th style="width:100%">Tipo Id</th>
		<th style="width:100%">Tipo Bienes y Servicios Comprados</th>
		<th>NCF</th>
		<th style="width:100%">NCF o Documento Modficado</th>
		<th style="width:100%">Fecha Comprobante</th>
		<th style="width:100%">Fecha Pago</th>
		<th style="width:100%">Monto Facturado en Servicio</th>
		<th style="width:100%">Monto Facturado en Bienes</th>
		<th style="width:100%">Total Monto Facturado</th>
		<th style="width:100%"><?php echo $iva_name;?> Facturado</th>
		<th style="width:100%"><?php echo $iva_name;?> Retenido</th>
		<th style="width:100%"><?php echo $iva_name;?> Sujeto a Proporcionalidad (Art.349)</th>
		<th style="width:100%"><?php echo $iva_name;?> llevado al costo</th>
		<th style="width:100%"><?php echo $iva_name;?> por Adelantar</th>
		<th style="width:100%"><?php echo $iva_name;?> Percibido en Compras</th>
		<th style="width:100%">Tipo de Retencion en ISR</th>
		<th style="width:100%">Monto Retenido Renta</th>
		<th style="width:100%">ISR Percibido en Compras</th>
		<th style="width:100%">Impuesto Selectivo al Consumo</th>
		<th style="width:100%">Otros Impuesto/Tasas</th>
		<th style="width:100%">Monto Propina Legal</th>
		<th style="width:100%">Forma de Pago</th>
	</thead>
<?php $i=1; foreach($products as $product):?>
	<tr>
		<td><?php echo $i; ?></td>
		<td><?php if($product->person_id!=null){$c= $product->getPerson();echo $c->no; $type=$c->is_type;} ?> </td>
		<td><?php if($product->person_id!=null){$c= $product->getPerson();echo $c->is_id; $isid=$c->is_id;} ?> </td>
		<td><?php echo $product->getTG()->name;?>	</td>
		<td><?php echo "B".$product->voucher; ?></td>
		<td></td>
		<td><?php echo  date("Ymd", strtotime($product->created_voucher)); ?> </td>
		<td><?php echo  date("Ymd", strtotime($product->created_at)); ?> </td>
		<td></td>
		<td><?php echo $symbol; ?> <?php echo number_format($product->total/1.18,2,'.',','); ?></td>
	    <td><?php echo $symbol; ?> <?php echo number_format($product->total/1.18,2,'.',','); ?></td>
	    <td><?php echo Core::$symbol; ?> <?php echo number_format((($product->total/1.18)*$iva_val/100),2,'.',','); ?></td>
	    <td></td>
	    <td></td>
	    <td><?php echo Core::$symbol; ?> <?php echo number_format((($product->total/1.18)*$iva_val/100),2,'.',','); ?></td>
	    <td></td>
	    <td></td>
	   <td></td>
	    <td></td>
	    <td></td>
	    <td></td>
	    <td></td>
	    <td></td>
	    <td><?php echo $product->getP()->name;?>	</td>
	</tr>
<?php $i++; $supertotal2+= $product->total; endforeach; ?>

<!-- ------------------------------------------------------------------------------------------- -->

<?php $i=1; foreach($operations as $operation):?>
	<tr>
		<td><?php echo $i; ?></td>
		<td><?php if($operation->person_id!=null){$c= $operation->getPerson();echo $c->no; $type=$c->is_type;} ?> </td>
		<td><?php if($operation->person_id!=null){$c= $operation->getPerson();echo $c->is_id; $isid=$c->is_id;} ?> </td>
		<td><?php echo $operation->getTG()->name;?>	</td>
		<td><?php echo $operation->voucher_code; ?></td>
		<td></td>

		<td><?php echo date("Ymd", strtotime($operation->created_date)); ?></td>
		<td><?php if ($operation->created_pg!=null) {echo date("Ym", strtotime($operation->created_pg));}  ?></td>

		<td><?php if($type==1){echo number_format($operation->price/1.18,2,'.',','); $tcs=$operation->price;} ?></td>

		<td><?php if($type==2){echo number_format($operation->price/1.18,2,'.',','); $tcn=$operation->price;} ?></td>

		<?php $tpv=($tcs+$tcn);?>

	  <td><?php echo $symbol; ?> <?php echo number_format($tpv/1.18,2,'.',','); ?></td>
	    <td><?php echo Core::$symbol; ?> <?php echo number_format((($tpv/1.18)*$iva_val/100),2,'.',','); ?></td>
	    <td><?php echo $operation->itbis_ret; ?></td>
	    <td></td>
	    <td><?php echo Core::$symbol; ?> <?php echo number_format((($tpv/1.18)*$iva_val/100),2,'.',','); ?></td>
	    <td></td>
	    <td></td>
	    <td><?php echo $operation->getSG()->name;?>	</td>
	    <td><?php echo $operation->imp_rent; ?></td>
	    <td></td>
	    <td></td>
	    <td></td>
	    <td></td>
	     <td><?php echo $operation->getP()->name;?>	</td>
	</tr>
<?php $i++; $supertotal+= $tpv; endforeach; ?>
</table>
</div>
<div class="card-body">
<table class="table table-bordered">
	<thead>
		<th>Total:</th>
		<th><?php echo Core::$symbol." ".number_format($supertotal+$supertotal2,2,".",","); ?></th>
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