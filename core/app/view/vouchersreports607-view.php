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
         <h1 class="m-0"><i class="fa fa-copy"></i> Reportes Comprobantes DGII 607</h1>
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
						<input type="hidden" name="view" value="vouchersreports607">
<div class="row">
<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
<select style="background-color: #333;" name="c_id" class="form-control select2">
	<option value="">-- COMPROBANTE --</option>
	<?php foreach(CData::getAllBySQL("where location=607") as $p):?>
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
		<?php if(isset($_GET["sd"]) && isset($_GET["ed"]) ):?>
<?php if($_GET["sd"]!=""&&$_GET["ed"]!=""):?>
			<?php 
			
			$operations = array();

		  if($_GET["c_id"]==""){
			$operations = BookingData::getAllByDateOp2($_GET["sd"],$_GET["ed"],StockData::getPrincipal()->id);
			}
			else if($_GET["c_id"]<>""){
			$operations = BookingData::getAllByDateIva($_GET["sd"],$_GET["ed"],StockData::getPrincipal()->id,$_GET["c_id"]);
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
		<th style="width:100%">RNC/Cedula o Pasaporte</th>
		<th style="width:100%">Tipo Identificacion</th>
		<th>Numero de Comprobante Fiscal</th>
		<th style="width:100%">Numero de Comprobante Fiscal Modficado</th>
		<th style="width:100%">Tipo de Ingreso</th>
		<th style="width:100%">Fecha Comprobante</th>
		<th style="width:100%">Fecha de Retencion</th>
		<th style="width:100%">Monto Facturado</th>
		<th style="width:100%"><?php echo $iva_name;?> Facturado</th>
		<th style="width:100%"><?php echo $iva_name;?> Percibido</th>
		<th style="width:100%">Retencion Renta por Terceros</th>
		<th style="width:100%">ISR Percibido</th>
		<th style="width:100%">Impuesto Selectivo al Consumo</th>
	    <th style="width:100%">Otros Impuesto/Tasas</th>
		<th style="width:100%">Monto Propina Legal</th>
		<th style="width:100%">Efectivo</th>
		<th style="width:100%">Cheque/Transferencia/Deposito</th>
		<th style="width:100%">Tarjeta de Debito/Credito</th>
		<th style="width:100%">Ventas a Credito</th>
		<th style="width:100%">Bonos o Certificados de Regalo</th>
		<th style="width:100%">Permuta</th>
	</thead>
<?php $i=1; foreach($operations as $product):?>
	<tr>
		<td><?php echo $i; ?></td>
<?php 
$persona = $product->getPerson();

switch (true) {
    case (!empty($persona->no)):
        $id_value = $persona->no;
        $id_type  = "Cédula";
        break;

    case (!empty($persona->rnc)):
        $id_value = $persona->rnc;
        $id_type  = "RNC";
        break;

    case (!empty($persona->passport)):
        $id_value = $persona->passport;
        $id_type  = "Pasaporte";
        break;

    default:
        $id_value = "-";
        $id_type  = "-";
        break;
}
?>

<td><?php echo $id_value; ?></td>
<td><?php echo $id_type; ?></td>


        
		<td> <?php echo $product->number_iva;?></td>
        <td></td>
		<td>Servicios</td>
		<td><?php echo date("Ymd ", strtotime($product->created_at)); ?></td>
		<td></td>
		<td><?php echo Core::$symbol." ".number_format($product->total/1.18,2,'.',','); ?></td>
		<td><?php echo Core::$symbol." ".number_format($product->total-($product->total/1.18),2,'.',','); ?></td>
	    <td></td>
	    <td></td>
	    <td></td>
	    <td></td>
	    <td></td>
	    <td></td>
	    <td><?php if($product->f_id==1){echo Core::$symbol." ". number_format($product->total,2,'.',',');} ?></td>
	    <td><?php if($product->f_id==2 || $product->f_id==3 || $product->f_id==4){echo number_format($product->total,2,'.',',');} ?></td>
	    <td><?php if($product->f_id==5){echo Core::$symbol." ". number_format($product->total,2,'.',',');} ?></td>
	    <td><?php if($product->p_id==4){echo Core::$symbol." ". number_format($product->total,2,'.',',');} ?></td>
	    <td></td>
	    <td><?php if($product->f_id==6){echo Core::$symbol." ". number_format($product->total,2,'.',',');} ?></td>
	    	</tr>
<?php $i++; $supertotal+= $product->total; endforeach; ?>
</table>
</div>
<div class="card-body">
<table class="table table-bordered">
	<thead>
		<th>Total:</th>
		<th><?php echo Core::$symbol." ".number_format($supertotal,2,".",","); ?></th>
	</thead>
</table>
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