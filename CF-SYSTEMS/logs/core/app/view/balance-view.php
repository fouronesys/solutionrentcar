
<section class="content">
<div class="row">
  <div class="col-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-8">
          <h1 class="m-0"><i class="fa fa-balance-scale"></i> Balance (Renta - Gastos = Ganancia)</h1>
          </div><!-- /.col -->
          
        <div class="col-sm-4">
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
<input type="hidden" name="view" value="balance">
<div class="row">
  <div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
<select style="background-color:#222;" class="form-control" name="stock">
  <?php foreach (StockData::getALLbySQL("where id=".StockData::getPrincipal()->id) as $stock):?>
<option value="<?php echo $stock->id;?>"><?php echo $stock->name;?></option>
  <?php endforeach;?>
</select>
</div>

<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
<input style="background-color:#222;" type="date" name="sd" value="<?php if(isset($_GET["sd"])){ echo $_GET["sd"]; }else{ echo date('Y-m-d',(time() -(60*60*24*30) ));}?>" required class="form-control">
</div>
<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
<input style="background-color:#222;" type="date" name="ed"  value="<?php if(isset($_GET["ed"])){ echo $_GET["ed"]; }else{ echo date("Y-m-d", strtotime('-6 hours'));}?>"  required class="form-control">
</div>

<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
<input type="submit" class="btn btn-warning btn-block" value="Procesar">
</div>

</div>

</form>

<br><!--- -->
<div class="row">

  <div class="col-12">
    <?php if(isset($_GET["sd"]) && isset($_GET["ed"]) && isset($_GET["stock"]) ):?>

<?php if($_GET["sd"]!=""&&$_GET["ed"]!="" && $_GET["stock"]!=""):
$sd = strtotime($_GET["sd"]);
$ed = strtotime($_GET["ed"]);
$selstock=$_GET["stock"];
?>
</div>
</div>
 <div class="card" style="background-color:#222;">
<div class="card-header">
<div id="line-chart" class="chart" data-animate="fadeInUp" ></div>
</div>
</div>
<script>

<?php 
echo "var c=0;";
echo "var dates=Array();";
echo "var data=Array();";
echo "var total=Array();";
for($i=$sd;$i<=$ed;$i+=(60*60*24)){
  $operations = BookingData::getGroupByDateOp(date("Y-m-d",$i),date("Y-m-d",$i),$selstock);
  $spends = SpendData::getGroupByDateOp(date("Y-m-d",$i),date("Y-m-d",$i),1,$selstock);
  $maintenance = MaintenanceData::getGroupByDateOp(date("Y-m-d",$i),date("Y-m-d",$i),$selstock);
//  echo $operations[0]->t;
  $op = $operations[0]->t!=null?$operations[0]->t:0;
  $sp = $spends[0]->t!=null?$spends[0]->t:0;
  $mt = $maintenance[0]->t!=null?$maintenance[0]->t:0;
  echo "dates[c]=\"".date("Y-m-d",$i)."\";";
  echo "data[c]=".($op-($sp+$mt)).";";
  echo "total[c]={x: dates[c],y: data[c]};";
  echo "c++;";
}
?>
// Use Morris.Area instead of Morris.Line
var line = new Morris.Line({
      element: 'line-chart',
      resize: true,
      data: total,
      xkey: 'x',
      ykeys: ['y'],
      labels: ['Y'],
      lineColors: ['green'],
      }).on('click', function(i, row){
  console.log(i, row);
});
</script>

<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
  <thead>
    <th>Fecha</th>
    <th>Rentas</th>
    <th>Mantenimiento</th>
    <th>Gastos</th>
    <th>Totales</th>
  </thead>
    <tfoot>
    <th>Fecha</th>
    <th>Rentas</th>
    <th>Mantenimiento</th>
    <th>Gastos</th>
    <th>Totales</th>     
    </tfoot>
<?php 
$bookingtotal = 0;
$spendtotal = 0;
$maintenancetotal =0;
for($i=$sd;$i<=$ed;$i+=(60*60*24)):
  $operations = BookingData::getGroupByDateOp(date("Y-m-d",$i),date("Y-m-d",$i),$selstock);
  $spends = SpendData::getGroupByDateOp(date("Y-m-d",$i),date("Y-m-d",$i),1,$selstock);
  $maintenance = MaintenanceData::getGroupByDateOp(date("Y-m-d",$i),date("Y-m-d",$i),$selstock);
      ?>

       <?php if(count($operations)>0):?>
<?php // foreach($operations as $operation):?>
  <tr>
    <td><?php echo date("Y-m-d",$i); ?></td>
    <td><?php echo Core::$symbol; ?> <?php echo number_format($operations[0]->t,2,'.',','); ?></td>
    <td><?php echo Core::$symbol; ?> <?php echo number_format($maintenance[0]->t,2,'.',','); ?></td>
    <td><?php echo Core::$symbol; ?> <?php echo number_format($spends[0]->t,2,'.',','); ?></td>
    <td><?php echo Core::$symbol; ?> <?php echo number_format($operations[0]->t-($spends[0]->t+$maintenance[0]->t),2,'.',','); ?></td>
  </tr>
<?php
$bookingtotal+= ($operations[0]->t);
$spendtotal+= ($spends[0]->t);
$maintenancetotal += ($maintenance[0]->t);
// endforeach; ?>
       <?php else:
       ?>
 <div>
 <div class="card">
 <div class="card-header">
  <h2>No hay operaciones</h2>
  <p>El rango de fechas seleccionado no proporciono ningun resultado de operaciones.</p>
</div>
</div>
</div>
       <?php endif; ?>
      <?php endfor;?>
  <tr style="background-color: gray; color: white;">
    <td>Total</td>
    <td><?php echo Core::$symbol; ?> <?php echo number_format($bookingtotal,2,'.',','); ?></td>
    <td><?php echo Core::$symbol; ?> <?php echo number_format($maintenancetotal,2,'.',','); ?></td>
    <td><?php echo Core::$symbol; ?> <?php echo number_format($spendtotal,2,'.',','); ?></td>
    <td><?php echo Core::$symbol; ?> <?php echo number_format(($bookingtotal)-($spendtotal+$maintenancetotal),2,'.',','); ?></td>
  </tr>
</table>
</div>
</div>
</div>
<?php else:?>

 <div class="card">
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