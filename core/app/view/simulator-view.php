<?php
// =======================================================
// 💸 PASO 5: SIMULADOR FINANCIERO INTELIGENTE - PHP 8.4
// =======================================================

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

$db  = new Database();
$con = $db->connect();

if (!$con) {
  die("Error de conexión");
}

$con->set_charset("utf8mb4");

$principal = StockData::getPrincipal();

if (!$principal || empty($principal->id)) {
  die("No se encontró stock principal");
}

$stock_id = intval($principal->id);

$mes_actual = date("Y-m");
$dia_actual = intval(date("d"));
$dias_mes   = intval(date("t"));

if (!isset(Core::$symbol) || Core::$symbol == "") {
  Core::$symbol = "RD$";
}

function sf_e($value){
  return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function sf_query($con, $sql){
  try {
    return $con->query($sql);
  } catch (Throwable $e) {
    return false;
  }
}

function sf_has_table($con, $table){
  try {
    $table = $con->real_escape_string($table);
    $q = $con->query("SHOW TABLES LIKE '$table'");
    return ($q && $q->num_rows > 0);
  } catch (Throwable $e) {
    return false;
  }
}

function sf_money($amount){
  return Core::$symbol . " " . number_format(floatval($amount), 2, ".", ",");
}

function sf_num($value, $default = 0){
  return isset($value) && $value !== "" ? floatval($value) : $default;
}

function sf_one_value($con, $sql, $field = "total"){
  $q = sf_query($con, $sql);

  if ($q) {
    $row = $q->fetch_assoc();
    return floatval($row[$field] ?? 0);
  }

  return 0;
}

// =======================================================
// DATOS BASE DEL MES
// =======================================================

$income_month = 0;
$total_bookings = 0;
$total_days = 0;
$booked_total = 0;
$expenses_month = 0;
$total_cars = 0;

if (sf_has_table($con, "payment")) {
  $income_month = sf_one_value($con, "
    SELECT IFNULL(SUM(val),0) total
    FROM payment
    WHERE stock_id=$stock_id
    AND DATE_FORMAT(created_at,'%Y-%m')='$mes_actual'
  ", "total");
}

if (sf_has_table($con, "booking")) {
  $q_booking = sf_query($con, "
    SELECT
      COUNT(*) total_bookings,
      IFNULL(SUM(day),0) total_days,
      IFNULL(SUM(total),0) booked_total
    FROM booking
    WHERE stock_id=$stock_id
    AND DATE_FORMAT(created_at,'%Y-%m')='$mes_actual'
  ");

  if ($q_booking) {
    $bdata = $q_booking->fetch_assoc();

    $total_bookings = intval($bdata["total_bookings"] ?? 0);
    $total_days     = floatval($bdata["total_days"] ?? 0);
    $booked_total   = floatval($bdata["booked_total"] ?? 0);
  }
}

$spends_total = 0;
$maintenance_total = 0;
$fuel_total = 0;
$toll_total = 0;

if (sf_has_table($con, "spends")) {
  $spends_total = sf_one_value($con, "
    SELECT IFNULL(SUM(price),0) total
    FROM spends
    WHERE stock_id=$stock_id
    AND DATE_FORMAT(created_at,'%Y-%m')='$mes_actual'
  ");
}

if (sf_has_table($con, "maintenance")) {
  $maintenance_total = sf_one_value($con, "
    SELECT IFNULL(SUM(purchase_price),0) total
    FROM maintenance
    WHERE stock_id=$stock_id
    AND DATE_FORMAT(created_at,'%Y-%m')='$mes_actual'
  ");
}

if (sf_has_table($con, "fuels")) {
  $fuel_total = sf_one_value($con, "
    SELECT IFNULL(SUM(price),0) total
    FROM fuels
    WHERE stock_id=$stock_id
    AND DATE_FORMAT(created_at,'%Y-%m')='$mes_actual'
  ");
}

if (sf_has_table($con, "toll")) {
  $toll_total = sf_one_value($con, "
    SELECT IFNULL(SUM(price),0) total
    FROM toll
    WHERE stock_id=$stock_id
    AND DATE_FORMAT(created_at,'%Y-%m')='$mes_actual'
  ");
}

$expenses_month = $spends_total + $maintenance_total + $fuel_total + $toll_total;

if (sf_has_table($con, "cars")) {
  $total_cars = intval(sf_one_value($con, "
    SELECT COUNT(*) total
    FROM cars
    WHERE stock_id=$stock_id
  "));
}

$daily_income_avg = ($dia_actual > 0 ? ($income_month / $dia_actual) : 0);
$projected_income_current = $daily_income_avg * $dias_mes;
$projected_profit_current = $projected_income_current - $expenses_month;

$avg_booking_value = ($total_bookings > 0 ? ($booked_total / $total_bookings) : 0);
$avg_daily_rate = ($total_days > 0 ? ($booked_total / $total_days) : 0);
$avg_days_per_booking = ($total_bookings > 0 ? ($total_days / $total_bookings) : 0);

$occupancy_days_possible = $total_cars * $dias_mes;
$occupancy_rate = ($occupancy_days_possible > 0 ? ($total_days / $occupancy_days_possible) * 100 : 0);

// =======================================================
// INPUTS DEL SIMULADOR
// =======================================================

$increase_rate       = sf_num($_POST["increase_rate"] ?? 300, 300);
$increase_occupancy  = sf_num($_POST["increase_occupancy"] ?? 10, 10);
$reduce_expenses     = sf_num($_POST["reduce_expenses"] ?? 10, 10);
$new_car_price       = sf_num($_POST["new_car_price"] ?? 0, 0);
$new_car_daily_rate  = sf_num($_POST["new_car_daily_rate"] ?? $avg_daily_rate, $avg_daily_rate);
$new_car_days_month  = sf_num($_POST["new_car_days_month"] ?? 15, 15);

// =======================================================
// CÁLCULOS
// =======================================================

$extra_by_rate = $total_days * $increase_rate;

$extra_days_by_occupancy = $occupancy_days_possible * ($increase_occupancy / 100);
$extra_by_occupancy = $extra_days_by_occupancy * $avg_daily_rate;

$savings_by_expenses = $expenses_month * ($reduce_expenses / 100);

$new_car_month_income = $new_car_daily_rate * $new_car_days_month;

$new_car_roi_months = 0;

if ($new_car_price > 0 && $new_car_month_income > 0) {
  $new_car_roi_months = $new_car_price / $new_car_month_income;
}

$scenario_income = $projected_income_current + $extra_by_rate + $extra_by_occupancy + $new_car_month_income;
$scenario_expenses = max(0, $expenses_month - $savings_by_expenses);
$scenario_profit = $scenario_income - $scenario_expenses;
$profit_difference = $scenario_profit - $projected_profit_current;

$health_msg = "Escenario conservador";
$health_color = "#f1c40f";

if ($profit_difference > 0) {
  $health_msg = "Escenario favorable";
  $health_color = "#2ecc71";
} elseif ($profit_difference < 0) {
  $health_msg = "Escenario de riesgo";
  $health_color = "#e74c3c";
}
?>

<style>
html,body{ overflow-x:hidden!important; }
.content-wrapper,.content,.container-fluid{ overflow-x:hidden!important; }

.sf-main{
  background:#16181d;
  border-radius:22px;
  border:1px solid rgba(255,255,255,.08);
  box-shadow:0 10px 28px rgba(0,0,0,.35);
  overflow:hidden;
  margin-bottom:20px;
}
.sf-header{
  padding:18px;
  border-bottom:1px solid rgba(255,255,255,.08);
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:12px;
  flex-wrap:wrap;
}
.sf-header h3{
  color:#fff;
  font-weight:900;
  margin:0;
}
.sf-header span{
  color:#9aa0a6;
  font-weight:800;
}
.sf-status{
  display:inline-block;
  padding:8px 13px;
  border-radius:999px;
  background:<?php echo sf_e($health_color); ?>;
  color:#111;
  font-weight:900;
}
.sf-kpis{
  display:grid;
  grid-template-columns:repeat(4,minmax(0,1fr));
  gap:12px;
  padding:18px;
  padding-bottom:0;
}
.sf-kpi{
  background:#0f1115;
  border:1px solid rgba(255,255,255,.08);
  border-radius:16px;
  padding:14px;
  min-width:0;
  overflow:hidden;
}
.sf-kpi .label{
  color:#bdbdbd;
  font-size:12px;
  font-weight:900;
  text-transform:uppercase;
}
.sf-kpi .value{
  color:#fff;
  font-size:23px;
  font-weight:900;
  margin-top:5px;
}
.sf-kpi .sub{
  color:#9aa0a6;
  font-size:12px;
  font-weight:800;
  margin-top:4px;
}
.sf-body{
  padding:18px;
}
.sf-box{
  background:#0f1115;
  border:1px solid rgba(255,255,255,.08);
  border-radius:18px;
  padding:16px;
  margin-bottom:14px;
  max-width:100%;
  overflow:hidden;
}
.sf-box h5{
  color:#fff;
  font-weight:900;
  margin:0 0 10px 0;
}
.sf-form label{
  color:#bdbdbd;
  font-weight:900;
  font-size:12px;
  text-transform:uppercase;
}
.sf-form input{
  background:#0b0d10!important;
  border:1px solid rgba(255,255,255,.12)!important;
  color:#fff!important;
  border-radius:12px!important;
  font-weight:800;
}
.sf-result-grid{
  display:grid;
  grid-template-columns:repeat(3,minmax(0,1fr));
  gap:12px;
}
.sf-result{
  background:#0b0d10;
  border:1px solid rgba(255,255,255,.08);
  border-radius:16px;
  padding:14px;
}
.sf-result .label{
  color:#9aa0a6;
  font-size:12px;
  font-weight:900;
  text-transform:uppercase;
}
.sf-result .value{
  color:#fff;
  font-size:20px;
  font-weight:900;
  margin-top:5px;
}
.sf-advice{
  background:rgba(46,204,113,.08);
  border:1px solid rgba(46,204,113,.15);
  padding:12px;
  border-radius:14px;
  color:#2ecc71;
  font-weight:900;
  margin-top:12px;
}
@media(max-width:991px){
  .sf-kpis{ grid-template-columns:repeat(2,minmax(0,1fr)); }
  .sf-result-grid{ grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media(max-width:575px){
  .sf-kpis,.sf-result-grid{ grid-template-columns:1fr; }
}
</style>

<section class="content">
<div class="container-fluid">
<br>

<div class="sf-main">

  <div class="sf-header">
    <div>
      <h3>💸 Simulador Financiero Inteligente</h3>
      <span>Calcula escenarios: tarifa, ocupación, gastos y compra de vehículo</span>
    </div>

    <span class="sf-status">
      <?php echo sf_e($health_msg); ?>
    </span>
  </div>

  <div class="sf-kpis">

    <div class="sf-kpi">
      <div class="label">Ingreso proyectado actual</div>
      <div class="value"><?php echo sf_money($projected_income_current); ?></div>
      <div class="sub">Basado en promedio diario</div>
    </div>

    <div class="sf-kpi">
      <div class="label">Gastos del mes</div>
      <div class="value"><?php echo sf_money($expenses_month); ?></div>
      <div class="sub">Negocio + mantenimiento + combustible + peajes</div>
    </div>

    <div class="sf-kpi">
      <div class="label">Tarifa promedio diaria</div>
      <div class="value"><?php echo sf_money($avg_daily_rate); ?></div>
      <div class="sub">Calculada por días rentados</div>
    </div>

    <div class="sf-kpi">
      <div class="label">Ocupación estimada</div>
      <div class="value"><?php echo round($occupancy_rate); ?>%</div>
      <div class="sub"><?php echo round($total_days); ?> días rentados / <?php echo intval($occupancy_days_possible); ?></div>
    </div>

  </div>

  <div class="sf-body">

    <div class="sf-box">
      <h5>🧮 Probar escenario</h5>

      <form method="POST" class="sf-form">
        <div class="row">

          <div class="col-md-4 mb-3">
            <label>Subir tarifa diaria</label>
            <input type="number" step="0.01" name="increase_rate" class="form-control" value="<?php echo sf_e($increase_rate); ?>">
          </div>

          <div class="col-md-4 mb-3">
            <label>Aumentar ocupación (%)</label>
            <input type="number" step="0.01" name="increase_occupancy" class="form-control" value="<?php echo sf_e($increase_occupancy); ?>">
          </div>

          <div class="col-md-4 mb-3">
            <label>Reducir gastos (%)</label>
            <input type="number" step="0.01" name="reduce_expenses" class="form-control" value="<?php echo sf_e($reduce_expenses); ?>">
          </div>

          <div class="col-md-4 mb-3">
            <label>Costo nuevo vehículo</label>
            <input type="number" step="0.01" name="new_car_price" class="form-control" value="<?php echo sf_e($new_car_price); ?>">
          </div>

          <div class="col-md-4 mb-3">
            <label>Tarifa diaria nuevo vehículo</label>
            <input type="number" step="0.01" name="new_car_daily_rate" class="form-control" value="<?php echo sf_e($new_car_daily_rate); ?>">
          </div>

          <div class="col-md-4 mb-3">
            <label>Días rentados nuevo vehículo</label>
            <input type="number" step="0.01" name="new_car_days_month" class="form-control" value="<?php echo sf_e($new_car_days_month); ?>">
          </div>

          <div class="col-md-12">
            <button type="submit" class="btn btn-warning btn-block" style="font-weight:900;">
              Calcular escenario
            </button>
          </div>

        </div>
      </form>
    </div>

    <div class="sf-box">
      <h5>📊 Resultado del escenario</h5>

      <div class="sf-result-grid">

        <div class="sf-result">
          <div class="label">Extra por tarifa</div>
          <div class="value" style="color:#2ecc71;"><?php echo sf_money($extra_by_rate); ?></div>
        </div>

        <div class="sf-result">
          <div class="label">Extra por ocupación</div>
          <div class="value" style="color:#2ecc71;"><?php echo sf_money($extra_by_occupancy); ?></div>
        </div>

        <div class="sf-result">
          <div class="label">Ahorro por gastos</div>
          <div class="value" style="color:#2ecc71;"><?php echo sf_money($savings_by_expenses); ?></div>
        </div>

        <div class="sf-result">
          <div class="label">Ingreso nuevo vehículo</div>
          <div class="value"><?php echo sf_money($new_car_month_income); ?></div>
        </div>

        <div class="sf-result">
          <div class="label">Ganancia proyectada</div>
          <div class="value" style="color:<?php echo ($scenario_profit >= 0 ? '#2ecc71' : '#e74c3c'); ?>;">
            <?php echo sf_money($scenario_profit); ?>
          </div>
        </div>

        <div class="sf-result">
          <div class="label">Diferencia vs actual</div>
          <div class="value" style="color:<?php echo ($profit_difference >= 0 ? '#2ecc71' : '#e74c3c'); ?>;">
            <?php echo sf_money($profit_difference); ?>
          </div>
        </div>

      </div>

      <?php if ($new_car_price > 0 && $new_car_roi_months > 0): ?>
        <div class="sf-advice">
          🚗 Recuperación estimada del nuevo vehículo: <?php echo number_format($new_car_roi_months, 1); ?> meses.
        </div>
      <?php endif; ?>

      <?php if ($profit_difference > 0): ?>
        <div class="sf-advice">
          ⚡ El escenario mejora la ganancia aproximada en <?php echo sf_money($profit_difference); ?>. Es una opción favorable si el mercado soporta esa tarifa u ocupación.
        </div>
      <?php elseif ($profit_difference < 0): ?>
        <div class="sf-advice" style="color:#e74c3c;background:rgba(231,76,60,.08);border-color:rgba(231,76,60,.15);">
          ⚠️ El escenario reduce la ganancia proyectada. Revisa costo del vehículo, días estimados o gastos.
        </div>
      <?php else: ?>
        <div class="sf-advice">
          ⚡ El escenario queda similar al actual. Ajusta tarifa, ocupación o gastos para ver una diferencia mayor.
        </div>
      <?php endif; ?>

    </div>

  </div>
</div>

</div>
</section>