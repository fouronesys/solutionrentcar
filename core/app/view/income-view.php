<?php if(isset($_GET["opt"]) && $_GET["opt"]=="daily"): ?>

<section class="content">
<div class="container-fluid">

  <div class="row mb-2">
    <div class="col-sm-8">
      <h1 class="m-0"><i class="fa fa-calendar-day"></i> Ingresos Diarios</h1>
    </div>
  
  </div>

  <form>
    <input type="hidden" name="view" value="income">
    <input type="hidden" name="opt" value="daily">

    <div class="row">
      <div class="col-md-3">
        <select style="background-color:#222; color:white;" class="form-control" name="stock">
          <?php foreach (StockData::getALLbySQL("where id=".StockData::getPrincipal()->id) as $stock):?>
            <option value="<?php echo $stock->id;?>" <?php if(isset($_GET["stock"]) && $_GET["stock"]==$stock->id){ echo "selected"; } ?>>
              <?php echo $stock->name;?>
            </option>
          <?php endforeach;?>
        </select>
      </div>

      <div class="col-md-3">
        <input style="background-color:#222; color:white;" type="date" name="sd"
        value="<?php echo isset($_GET["sd"]) ? $_GET["sd"] : date('Y-m-01'); ?>"
        required class="form-control">
      </div>

      <div class="col-md-3">
        <input style="background-color:#222; color:white;" type="date" name="ed"
        value="<?php echo isset($_GET["ed"]) ? $_GET["ed"] : date('Y-m-d'); ?>"
        required class="form-control">
      </div>

      <div class="col-md-3">
        <input type="submit" class="btn btn-warning btn-block" value="Procesar">
      </div>
    </div>
  </form>

  <br>

  <?php if(isset($_GET["sd"]) && isset($_GET["ed"]) && isset($_GET["stock"]) && $_GET["sd"]!="" && $_GET["ed"]!="" && $_GET["stock"]!=""): ?>
  <?php
    $sd = strtotime($_GET["sd"]);
    $ed = strtotime($_GET["ed"]);
    $selstock = intval($_GET["stock"]);

    $total_rentas = 0;
    $total_gastos = 0;
    $total_mantenimiento = 0;
    $total_neto = 0;
  ?>

  <div class="card" style="background:#222; color:white;">
    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover" id="example1" style="color:white;">
        <thead style="background:#111;">
          <tr>
            <th>Fecha</th>
            <th>Rentas</th>
          </tr>
        </thead>
        <tbody>
          <?php for($i=$sd; $i<=$ed; $i+=(60*60*24)): ?>
          <?php
            $fecha = date("Y-m-d",$i);

            $operations  = BookingData::getGroupByDateIncomeOp($fecha,$fecha,$selstock);
            $spends      = SpendData::getGroupByDateOp($fecha,$fecha,1,$selstock);
            $maintenance = MaintenanceData::getGroupByDateOp($fecha,$fecha,$selstock);

            $rentas = (isset($operations[0]->t) && $operations[0]->t!=null) ? $operations[0]->t : 0;
            $gastos = (isset($spends[0]->t) && $spends[0]->t!=null) ? $spends[0]->t : 0;
            $mantenimiento_val = (isset($maintenance[0]->t) && $maintenance[0]->t!=null) ? $maintenance[0]->t : 0;
            $neto = $rentas - ($gastos + $mantenimiento_val);

            $total_rentas += $rentas;
            $total_gastos += $gastos;
            $total_mantenimiento += $mantenimiento_val;
            $total_neto += $neto;
          ?>
          <tr>
            <td data-order="<?php echo $m; ?>">
  <?php echo date("F", strtotime($inicio_mes)); ?>
</td>
            <td><?php echo Core::$symbol." ".number_format($rentas,2,'.',','); ?></td>
          </tr>
          <?php endfor; ?>
        </tbody>
        <tfoot>
          <tr style="background:#444; color:white; font-weight:bold;">
            <th>Total</th>
            <th><?php echo Core::$symbol." ".number_format($total_rentas,2,'.',','); ?></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <?php endif; ?>

</div>
</section>

<script>
$("#example1").DataTable({
  "order": [[0, "desc"]]
});
</script>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="monthly"): ?>

<section class="content">
<div class="container-fluid">

  <div class="row mb-2">
    <div class="col-sm-8">
      <h1 class="m-0"><i class="fa fa-calendar"></i> Ingresos Mensuales</h1>
    </div>
    <div class="col-sm-4">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item active">
          <i class="fa fa-bar-chart"></i> Resumen mensual
        </li>
      </ol>
    </div>
  </div>

  <form>
    <input type="hidden" name="view" value="income">
    <input type="hidden" name="opt" value="monthly">

    <div class="row">
      <div class="col-md-4">
        <select style="background-color:#222; color:white;" class="form-control" name="stock">
          <?php foreach (StockData::getALLbySQL("where id=".StockData::getPrincipal()->id) as $stock):?>
            <option value="<?php echo $stock->id;?>" <?php if(isset($_GET["stock"]) && $_GET["stock"]==$stock->id){ echo "selected"; } ?>>
              <?php echo $stock->name;?>
            </option>
          <?php endforeach;?>
        </select>
      </div>

      <div class="col-md-3">
        <input style="background-color:#222; color:white;" type="number" name="year"
        value="<?php echo isset($_GET["year"]) ? intval($_GET["year"]) : date("Y"); ?>"
        class="form-control" min="2020" max="2100" required>
      </div>

      <div class="col-md-5">
        <input type="submit" class="btn btn-warning btn-block" value="Procesar">
      </div>
    </div>
  </form>

  <br>

  <?php if(isset($_GET["year"]) && isset($_GET["stock"]) && $_GET["year"]!="" && $_GET["stock"]!=""): ?>
  <?php
    $year = intval($_GET["year"]);
    $selstock = intval($_GET["stock"]);

    $total_rentas_anual = 0;
    $total_gastos_anual = 0;
    $total_mantenimiento_anual = 0;
    $total_neto_anual = 0;
  ?>

  <div class="card" style="background:#222; color:white;">
    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover" id="example2" style="color:white;">
        <thead style="background:#111;">
          <tr>
            <th>Mes</th>
            <th>Rentas</th>
          </tr>
        </thead>
        <tbody>
          <?php for($m=1; $m<=12; $m++): ?>
          <?php
            $inicio_mes = date("Y-m-d", strtotime($year."-".str_pad($m,2,"0",STR_PAD_LEFT)."-01"));
            $fin_mes = date("Y-m-t", strtotime($inicio_mes));

            $rentas_mes = 0;
            $gastos_mes = 0;
            $mantenimiento_mes = 0;

            for($i=strtotime($inicio_mes); $i<=strtotime($fin_mes); $i+=(60*60*24)){
              $fecha = date("Y-m-d",$i);

              $operations  = BookingData::getGroupByDateIncomeOp($fecha,$fecha,$selstock);
              $spends      = SpendData::getGroupByDateOp($fecha,$fecha,1,$selstock);
              $maintenance = MaintenanceData::getGroupByDateOp($fecha,$fecha,$selstock);

              $rentas_mes += (isset($operations[0]->t) && $operations[0]->t!=null) ? $operations[0]->t : 0;
              $gastos_mes += (isset($spends[0]->t) && $spends[0]->t!=null) ? $spends[0]->t : 0;
              $mantenimiento_mes += (isset($maintenance[0]->t) && $maintenance[0]->t!=null) ? $maintenance[0]->t : 0;
            }

            $neto_mes = $rentas_mes - ($gastos_mes + $mantenimiento_mes);

            $total_rentas_anual += $rentas_mes;
            $total_gastos_anual += $gastos_mes;
            $total_mantenimiento_anual += $mantenimiento_mes;
            $total_neto_anual += $neto_mes;
          ?>
          <tr>
            <td data-order="<?php echo $m; ?>">
  <?php echo date("F", strtotime($inicio_mes)); ?>
</td>
            <td><?php echo Core::$symbol." ".number_format($rentas_mes,2,'.',','); ?></td>
          </tr>
          <?php endfor; ?>
        </tbody>
        <tfoot>
          <tr style="background:#444; color:white; font-weight:bold;">
            <th>Total anual</th>
            <th><?php echo Core::$symbol." ".number_format($total_rentas_anual,2,'.',','); ?></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <?php endif; ?>

</div>
</section>

<script>
$("#example2").DataTable({
  "paging": false,
  "searching": false,
  "info": false,
  "order": [[0, "asc"]]
});
</script>

<?php endif; ?>