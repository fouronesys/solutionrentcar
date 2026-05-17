<section class="content">
<div class="row">
  <div class="col-12">

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-8">
        <h1 class="m-0"><i class="fa fa-balance-scale"></i> Balance (Renta - Gastos = Ganancia)</h1>
      </div>

      <div class="col-sm-4">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item active">
            <i class='fa fa-history'></i> 
            <span id="reloj"></span>
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>

<script>
function actualizarReloj() {
  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  document.getElementById("reloj").textContent = `${horas}:${minutos}:${segundos}`;
}
setInterval(actualizarReloj, 1000);
actualizarReloj();
</script>

<form>
<input type="hidden" name="view" value="balance">

<div class="row">
  <div class="col-3">
    <select style="background-color:#222;" class="form-control" name="stock">
      <?php foreach (StockData::getALLbySQL("where id=".StockData::getPrincipal()->id) as $stock):?>
      <option value="<?php echo $stock->id;?>"><?php echo $stock->name;?></option>
      <?php endforeach;?>
    </select>
  </div>

  <div class="col-3">
    <input style="background-color:#222;" type="date" name="sd"
    value="<?php echo isset($_GET["sd"]) ? $_GET["sd"] : date('Y-m-d',(time()-(60*60*24*30))); ?>"
    class="form-control">
  </div>

  <div class="col-3">
    <input style="background-color:#222;" type="date" name="ed"
    value="<?php echo isset($_GET["ed"]) ? $_GET["ed"] : date("Y-m-d"); ?>"
    class="form-control">
  </div>

  <div class="col-3">
    <input type="submit" class="btn btn-warning btn-block" value="Procesar">
  </div>
</div>
</form>

<br>

<?php if(isset($_GET["sd"]) && isset($_GET["ed"]) && isset($_GET["stock"])): ?>

<?php
$sd = strtotime($_GET["sd"]);
$ed = strtotime($_GET["ed"]);
$selstock = $_GET["stock"];
?>

<div class="card" style="background:#222;">
<div class="card-header">
<div id="line-chart"></div>
</div>
</div>

<script>
var total = [];
<?php
$c=0;
for($i=$sd;$i<=$ed;$i+=(60*60*24)){
  $op = BookingData::getGroupByDateOp(date("Y-m-d",$i),date("Y-m-d",$i),$selstock)[0]->t ?? 0;
  $sp = SpendData::getGroupByDateOp(date("Y-m-d",$i),date("Y-m-d",$i),1,$selstock)[0]->t ?? 0;
  $mt = MaintenanceData::getGroupByDateOp(date("Y-m-d",$i),date("Y-m-d",$i),$selstock)[0]->t ?? 0;
  echo "total.push({x:'".date("Y-m-d",$i)."',y:".($op-($sp+$mt))."});";
}
?>

new Morris.Line({
  element: 'line-chart',
  data: total,
  xkey: 'x',
  ykeys: ['y'],
  labels: ['Ganancia'],
  lineColors: ['green']
});
</script>

<div class="card" style="background:#222;">
<div class="card-body">
<div class="table-responsive">

<table class="table table-bordered" id="example1" style="color:white;">
<thead style="background:#111;">
<tr>
  <th>Fecha</th>
  <th>Rentas</th>
  <th>Mantenimiento</th>
  <th>Gastos</th>
  <th>Totales</th>
</tr>
</thead>

<tbody>
<?php
$bookingtotal=0;
$spendtotal=0;
$maintenancetotal=0;

for($i=$sd;$i<=$ed;$i+=(60*60*24)):
  $op = BookingData::getGroupByDateOp(date("Y-m-d",$i),date("Y-m-d",$i),$selstock)[0]->t ?? 0;
  $sp = SpendData::getGroupByDateOp(date("Y-m-d",$i),date("Y-m-d",$i),1,$selstock)[0]->t ?? 0;
  $mt = MaintenanceData::getGroupByDateOp(date("Y-m-d",$i),date("Y-m-d",$i),$selstock)[0]->t ?? 0;

  $bookingtotal += $op;
  $spendtotal += $sp;
  $maintenancetotal += $mt;
?>

<tr>
  <td><?php echo date("Y-m-d",$i); ?></td>
  <td><?php echo Core::$symbol." ".number_format($op,2,'.',','); ?></td>
  <td><?php echo Core::$symbol." ".number_format($mt,2,'.',','); ?></td>
  <td><?php echo Core::$symbol." ".number_format($sp,2,'.',','); ?></td>
  <td><?php echo Core::$symbol." ".number_format($op-($sp+$mt),2,'.',','); ?></td>
</tr>

<?php endfor; ?>
</tbody>

<tfoot>
<tr style="background:#444; color:white; font-weight:bold;">
  <th>Total</th>
  <th><?php echo Core::$symbol." ".number_format($bookingtotal,2,'.',','); ?></th>
  <th><?php echo Core::$symbol." ".number_format($maintenancetotal,2,'.',','); ?></th>
  <th><?php echo Core::$symbol." ".number_format($spendtotal,2,'.',','); ?></th>
  <th><?php echo Core::$symbol." ".number_format(($bookingtotal)-($spendtotal+$maintenancetotal),2,'.',','); ?></th>
</tr>
</tfoot>

</table>

</div>
</div>
</div>

<?php endif; ?>

</div>
</div>
</section>

<script>
$("#example1").DataTable();
</script>