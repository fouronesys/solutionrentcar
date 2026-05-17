<?php if(isset($_GET["opt"]) && $_GET["opt"]=="daily"): ?>

<section class="content">
<div class="container-fluid">

  <div class="row mb-2">
    <div class="col-sm-8">
      <h1 class="m-0"><i class="fa fa-calendar-day"></i> Gastos Diarios</h1>
    </div>
  </div>

  <form>
    <input type="hidden" name="view" value="bills">
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

    $total_gastos = 0;
  ?>

  <div class="card" style="background:#222; color:white;">
    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover" id="example1" style="color:white;">
        <thead style="background:#111;">
          <tr>
            <th>Fecha</th>
            <th>Gastos</th>
          </tr>
        </thead>
        <tbody>
          <?php for($i=$sd; $i<=$ed; $i+=(60*60*24)): ?>
          <?php
            $fecha = date("Y-m-d",$i);

            $spends = SpendData::getGroupByDateOp($fecha,$fecha,1,$selstock);
            $gastos = (isset($spends[0]->t) && $spends[0]->t!=null) ? $spends[0]->t : 0;

            $total_gastos += $gastos;
          ?>
          <tr>
            <td><?php echo $fecha; ?></td>
            <td style="color:#ff6b6b; font-weight:bold;">
              <?php echo Core::$symbol." ".number_format($gastos,2,'.',','); ?>
            </td>
          </tr>
          <?php endfor; ?>
        </tbody>
        <tfoot>
          <tr style="background:#444; color:white; font-weight:bold;">
            <th>Total</th>
            <th style="color:#ff6b6b;">
              <?php echo Core::$symbol." ".number_format($total_gastos,2,'.',','); ?>
            </th>
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
      <h1 class="m-0"><i class="fa fa-calendar"></i> Gastos Mensuales</h1>
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
    <input type="hidden" name="view" value="bills">
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

    $total_gastos_anual = 0;
  ?>

  <div class="card" style="background:#222; color:white;">
    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover" id="example2" style="color:white;">
        <thead style="background:#111;">
          <tr>
            <th>Mes</th>
            <th>Gastos</th>
          </tr>
        </thead>
        <tbody>
          <?php for($m=1; $m<=12; $m++): ?>
          <?php
            $inicio_mes = date("Y-m-d", strtotime($year."-".str_pad($m,2,"0",STR_PAD_LEFT)."-01"));
            $fin_mes = date("Y-m-t", strtotime($inicio_mes));

            $gastos_mes = 0;

            for($i=strtotime($inicio_mes); $i<=strtotime($fin_mes); $i+=(60*60*24)){
              $fecha = date("Y-m-d",$i);

              $spends = SpendData::getGroupByDateOp($fecha,$fecha,1,$selstock);
              $gastos_mes += (isset($spends[0]->t) && $spends[0]->t!=null) ? $spends[0]->t : 0;
            }

            $total_gastos_anual += $gastos_mes;
          ?>
          <tr>
            <td data-order="<?php echo $m; ?>">
              <?php echo date("F", strtotime($inicio_mes)); ?>
            </td>
            <td style="color:#ff6b6b; font-weight:bold;">
              <?php echo Core::$symbol." ".number_format($gastos_mes,2,'.',','); ?>
            </td>
          </tr>
          <?php endfor; ?>
        </tbody>
        <tfoot>
          <tr style="background:#444; color:white; font-weight:bold;">
            <th>Total anual</th>
            <th style="color:#ff6b6b;">
              <?php echo Core::$symbol." ".number_format($total_gastos_anual,2,'.',','); ?>
            </th>
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