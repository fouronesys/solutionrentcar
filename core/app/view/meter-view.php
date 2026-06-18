<?php
// =======================================================
// ✅ METER VIEW / SALUD DEL NEGOCIO + CENTRO INTELIGENTE
// PHP 8.4
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

if (!isset(Core::$symbol) || Core::$symbol == "") {
    Core::$symbol = "RD$";
}

$todayYM     = date("Y-m");
$lastYM      = date("Y-m", strtotime("-1 month"));
$todayYMD    = date("Y-m-d");
$monthStart  = date("Y-m-01 00:00:00");
$monthEnd    = date("Y-m-t 23:59:59");
$last30Date  = date("Y-m-d 00:00:00", strtotime("-30 days"));

// =======================================================
// HELPERS PHP 8.4
// =======================================================

function rt_e($value){
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function rt_query($con, $sql){
    try {
        return $con->query($sql);
    } catch (Throwable $e) {
        return false;
    }
}

function rt_has_table($con, $table){
    try {
        $table = $con->real_escape_string($table);
        $q = $con->query("SHOW TABLES LIKE '$table'");
        return ($q && $q->num_rows > 0);
    } catch (Throwable $e) {
        return false;
    }
}

function rt_has_column($con, $table, $column){
    try {
        $table  = $con->real_escape_string($table);
        $column = $con->real_escape_string($column);
        $q = $con->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return ($q && $q->num_rows > 0);
    } catch (Throwable $e) {
        return false;
    }
}

function rt_one_value($con, $sql, $field = "total"){
    $q = rt_query($con, $sql);

    if ($q) {
        $row = $q->fetch_assoc();
        return floatval($row[$field] ?? 0);
    }

    return 0;
}

function rt_sev_badge($sev){
    if ($sev == "ALTO") return "badge badge-danger";
    if ($sev == "MEDIO") return "badge badge-warning";
    return "badge badge-info";
}

function rt_money($amount){
    return Core::$symbol . " " . number_format(floatval($amount), 2, ".", ",");
}

// =======================================================
// Detectar link de payment -> booking
// =======================================================

$payment_link_col = null;

if (rt_has_table($con, "payment")) {
    if (rt_has_column($con, "payment", "booking_id")) {
        $payment_link_col = "booking_id";
    } elseif (rt_has_column($con, "payment", "sell_id")) {
        $payment_link_col = "sell_id";
    }
}

// =======================================================
// VARIABLES BASE
// =======================================================

$income = 0;
$expenses = 0;
$last_income = 0;
$pending_cnt = 0;
$pending_amt = 0;
$totalCars = 0;
$rentedCars = 0;
$occupancy = 0;
$idle = 0;
$maint_open = 0;
$paid_today = 0;
$billed_today = 0;
$ratio_collect = 0;
$growth = 0;

$top_vehicle = null;
$worst_maint_vehicle = null;
$vehicle_rank = [];
$idle_vehicles = [];
$risk_vehicles = [];
$maint_month_total = 0;
$vehicle_income_total = 0;

// =======================================================
// 1) INGRESOS MES
// =======================================================

if (rt_has_table($con, "payment")) {
    $income = rt_one_value($con, "
        SELECT IFNULL(SUM(val),0) total
        FROM payment
        WHERE stock_id=$stock_id
        AND DATE_FORMAT(created_at,'%Y-%m')='$todayYM'
    ");

    $last_income = rt_one_value($con, "
        SELECT IFNULL(SUM(val),0) total
        FROM payment
        WHERE stock_id=$stock_id
        AND DATE_FORMAT(created_at,'%Y-%m')='$lastYM'
    ");

    $paid_today = rt_one_value($con, "
        SELECT IFNULL(SUM(val),0) total
        FROM payment
        WHERE stock_id=$stock_id
        AND DATE(created_at)='$todayYMD'
    ");
}

// =======================================================
// 2) GASTOS MES GENERALES
// =======================================================

if (rt_has_table($con, "spends")) {
    $expenses = rt_one_value($con, "
        SELECT IFNULL(SUM(price),0) total
        FROM spends
        WHERE stock_id=$stock_id
        AND DATE_FORMAT(created_at,'%Y-%m')='$todayYM'
    ");
}

// =======================================================
// 3) PENDIENTES
// =======================================================

if (
    $payment_link_col &&
    rt_has_table($con, "booking") &&
    rt_has_table($con, "payment")
) {
    $sql_pending = "
        SELECT
            COUNT(*) AS c,
            SUM((IFNULL(b.total,0) - IFNULL(p.pagado,0))) AS t
        FROM booking b
        LEFT JOIN (
            SELECT {$payment_link_col} AS link_id, SUM(val) pagado
            FROM payment
            WHERE stock_id=$stock_id
            GROUP BY {$payment_link_col}
        ) p ON p.link_id = b.id
        WHERE b.stock_id=$stock_id
        AND b.status IN (0,1)
        AND (IFNULL(b.total,0) - IFNULL(p.pagado,0)) > 0
    ";

    $q_pending = rt_query($con, $sql_pending);

    if ($q_pending) {
        $row = $q_pending->fetch_assoc();
        $pending_cnt = intval($row["c"] ?? 0);
        $pending_amt = floatval($row["t"] ?? 0);
    }
}

// =======================================================
// 4) OCUPACIÓN
// =======================================================

if (rt_has_table($con, "cars")) {
    $totalCars = intval(rt_one_value($con, "
        SELECT COUNT(*) total
        FROM cars
        WHERE stock_id=$stock_id
    "));
}

if (rt_has_table($con, "booking")) {
    $rentedCars = intval(rt_one_value($con, "
        SELECT COUNT(DISTINCT car_id) total
        FROM booking
        WHERE stock_id=$stock_id
        AND status=1
    "));

    $billed_today = rt_one_value($con, "
        SELECT IFNULL(SUM(total),0) total
        FROM booking
        WHERE stock_id=$stock_id
        AND DATE(created_at)='$todayYMD'
    ");
}

$occupancy = $totalCars > 0 ? ($rentedCars / $totalCars) * 100 : 0;
$idle = max(0, ($totalCars - $rentedCars));

if ($billed_today > 0) {
    $ratio_collect = ($paid_today / $billed_today) * 100;
}

// =======================================================
// 5) MANTENIMIENTO ABIERTO
// =======================================================

if (rt_has_table($con, "maintenance")) {
    $maint_open = intval(rt_one_value($con, "
        SELECT COUNT(*) total
        FROM maintenance
        WHERE stock_id=$stock_id
        AND status=0
    "));
}

// =======================================================
// SCORE SALUD
// =======================================================

$score = 0;

if ($income > 0) {
    $profit_ratio = (($income - $expenses) / $income) * 100;
    $profit_ratio = max(0, min(100, $profit_ratio));
    $score += $profit_ratio * 0.40;
} else {
    $score += 5;
}

$pending_penalty = ($pending_cnt * 5) + (($pending_amt / 5000) * 5);
$pending_score = max(0, 100 - $pending_penalty);
$score += $pending_score * 0.20;

$score += max(0, min(100, $occupancy)) * 0.20;
$score += max(0, 100 - ($maint_open * 10)) * 0.10;

if ($last_income > 0) {
    $growth = (($income - $last_income) / $last_income) * 100;
    $growth_for_score = max(0, min(100, $growth));
    $score += $growth_for_score * 0.10;
}

$score = round(max(0, min(100, $score)));
$riesgo = 100 - $score;

if ($score >= 80) {
    $color = "#2ecc71";
    $status = "Excelente";
} elseif ($score >= 60) {
    $color = "#f1c40f";
    $status = "Estable";
} else {
    $color = "#e74c3c";
    $status = "Riesgo";
}

// =======================================================
// DIAGNÓSTICO
// =======================================================

$dx = [];
$hallazgos = [];
$acciones = [];

if ($pending_cnt > 0 || $pending_amt > 0) {
    $sev = ($pending_amt >= 15000 ? "ALTO" : ($pending_amt >= 5000 ? "MEDIO" : "LEVE"));

    $dx[] = [
        "title" => "Riesgo de Cobranza",
        "sev"   => $sev,
        "body"  => "Existen reservas activas con deuda pendiente. Esto puede afectar caja y aumentar presión de cobros."
    ];

    $hallazgos[] = "Deuda activa: " . rt_money($pending_amt) . " (" . $pending_cnt . " reserva(s)).";
    $acciones[] = "Priorizar cobros pendientes antes de crear nuevos gastos operativos.";
} else {
    $hallazgos[] = "Cobranza: sin deudas activas detectadas.";
}

if ($idle > 0) {
    $sev = ($idle >= 5 ? "MEDIO" : "LEVE");

    $dx[] = [
        "title" => "Ocupación de Flota",
        "sev"   => $sev,
        "body"  => "Se detectan vehículos sin rentas activas. Esto reduce rotación y limita el ingreso diario."
    ];

    $hallazgos[] = "Vehículos parados: " . $idle . " (de " . $totalCars . ").";
    $acciones[] = "Crear promoción rápida para vehículos parados o revisar precio de renta.";
} else {
    $hallazgos[] = "Ocupación: flota sin vehículos parados según rentas activas.";
}

if ($billed_today > 0) {
    $sev = ($ratio_collect < 60 ? "ALTO" : ($ratio_collect < 80 ? "MEDIO" : "LEVE"));

    if ($ratio_collect < 95) {
        $dx[] = [
            "title" => "Cobro por debajo de lo facturado (HOY)",
            "sev"   => $sev,
            "body"  => "Los pagos registrados hoy no alcanzan lo facturado hoy. Puede indicar cobros pendientes o registros incompletos."
        ];

        $acciones[] = "Revisar reservas creadas hoy y confirmar si el pago fue registrado correctamente.";
    } else {
        $dx[] = [
            "title" => "Cobranza del día (HOY)",
            "sev"   => "LEVE",
            "body"  => "Los pagos del día están alineados con lo facturado. Mantener monitoreo."
        ];
    }

    $hallazgos[] = "Cobrado/Facturado hoy: " . round($ratio_collect) . "% (Cobrado " . rt_money($paid_today) . " / Facturado " . rt_money($billed_today) . ").";
} else {
    $dx[] = [
        "title" => "Actividad del día",
        "sev"   => "LEVE",
        "body"  => "Hoy no se detectó facturación. No se evalúa ratio de cobro."
    ];

    $hallazgos[] = "Facturado hoy: 0. No se evalúa ratio.";
}

// =======================================================
// CENTRO INTELIGENTE POR VEHÍCULO
// =======================================================

if (
    $payment_link_col &&
    rt_has_table($con, "cars") &&
    rt_has_table($con, "booking") &&
    rt_has_table($con, "payment")
) {
    $sql_top_vehicle = "
        SELECT
            c.id,
            c.name,
            c.plate,
            c.token,
            IFNULL(SUM(p.val),0) AS income,
            COUNT(DISTINCT b.id) AS bookings
        FROM cars c
        LEFT JOIN booking b ON b.car_id = c.id AND b.stock_id=$stock_id
        LEFT JOIN payment p ON p.{$payment_link_col}=b.id 
            AND p.stock_id=$stock_id 
            AND p.created_at BETWEEN '$monthStart' AND '$monthEnd'
        WHERE c.stock_id=$stock_id
        GROUP BY c.id
        ORDER BY income DESC
        LIMIT 1
    ";

    $q_top_vehicle = rt_query($con, $sql_top_vehicle);

    if ($q_top_vehicle && $q_top_vehicle->num_rows > 0) {
        $top_vehicle = $q_top_vehicle->fetch_assoc();
    }

    if (rt_has_table($con, "maintenance")) {
        $sql_rank = "
            SELECT
                c.id,
                c.name,
                c.plate,
                c.token,
                IFNULL(pay.income,0) AS income,
                IFNULL(mt.maint_cost,0) AS maint_cost,
                (IFNULL(pay.income,0) - IFNULL(mt.maint_cost,0)) AS profit,
                IFNULL(pay.bookings,0) AS bookings,
                IFNULL(mt.maint_count,0) AS maint_count
            FROM cars c
            LEFT JOIN (
                SELECT b.car_id, SUM(p.val) income, COUNT(DISTINCT b.id) bookings
                FROM booking b
                INNER JOIN payment p ON p.{$payment_link_col}=b.id
                WHERE b.stock_id=$stock_id
                AND p.stock_id=$stock_id
                AND p.created_at BETWEEN '$monthStart' AND '$monthEnd'
                GROUP BY b.car_id
            ) pay ON pay.car_id = c.id
            LEFT JOIN (
                SELECT car_id, SUM(purchase_price) maint_cost, COUNT(*) maint_count
                FROM maintenance
                WHERE stock_id=$stock_id
                AND created_at BETWEEN '$monthStart' AND '$monthEnd'
                GROUP BY car_id
            ) mt ON mt.car_id = c.id
            WHERE c.stock_id=$stock_id
            ORDER BY profit DESC
            LIMIT 10
        ";

        $q_rank = rt_query($con, $sql_rank);

        if ($q_rank) {
            while ($r = $q_rank->fetch_assoc()) {
                $vehicle_rank[] = $r;
                $vehicle_income_total += floatval($r["income"] ?? 0);
                $maint_month_total += floatval($r["maint_cost"] ?? 0);
            }
        }

        $sql_worst_maint = "
            SELECT
                c.id,
                c.name,
                c.plate,
                c.token,
                SUM(m.purchase_price) AS maint_cost,
                COUNT(*) AS total_maint
            FROM maintenance m
            INNER JOIN cars c ON c.id=m.car_id
            WHERE m.stock_id=$stock_id
            AND c.stock_id=$stock_id
            AND m.created_at BETWEEN '$monthStart' AND '$monthEnd'
            GROUP BY m.car_id
            ORDER BY maint_cost DESC
            LIMIT 1
        ";

        $q_worst_maint = rt_query($con, $sql_worst_maint);

        if ($q_worst_maint && $q_worst_maint->num_rows > 0) {
            $worst_maint_vehicle = $q_worst_maint->fetch_assoc();
        }
    }

    $sql_idle_vehicles = "
        SELECT
            c.id,
            c.name,
            c.plate,
            c.token,
            MAX(b.end_at) AS last_rent
        FROM cars c
        LEFT JOIN booking b ON b.car_id=c.id AND b.stock_id=$stock_id AND b.status IN (1,3)
        WHERE c.stock_id=$stock_id
        AND c.id NOT IN (
            SELECT DISTINCT b2.car_id
            FROM booking b2
            INNER JOIN payment p2 ON p2.{$payment_link_col}=b2.id
            WHERE b2.stock_id=$stock_id
            AND p2.stock_id=$stock_id
            AND p2.created_at >= '$last30Date'
        )
        GROUP BY c.id
        ORDER BY last_rent ASC
        LIMIT 8
    ";

    $q_idle_vehicles = rt_query($con, $sql_idle_vehicles);

    if ($q_idle_vehicles) {
        while ($r = $q_idle_vehicles->fetch_assoc()) {
            $idle_vehicles[] = $r;
        }
    }

    foreach ($vehicle_rank as $vr) {
        $income_v = floatval($vr["income"] ?? 0);
        $maint_v  = floatval($vr["maint_cost"] ?? 0);
        $profit_v = floatval($vr["profit"] ?? 0);

        if ($maint_v > 0 && ($income_v <= 0 || $maint_v >= ($income_v * 0.50) || $profit_v < 0)) {
            $risk_vehicles[] = $vr;
        }
    }
}

// =======================================================
// RECOMENDACIONES INTELIGENTES
// =======================================================

$smart = [];

if ($score < 60) {
    $smart[] = [
        "sev" => "ALTO",
        "title" => "Plan de choque financiero",
        "body" => "La salud está por debajo de 60%. Revisa cobros pendientes, vehículos parados y gastos antes de aumentar operaciones."
    ];
} elseif ($score < 80) {
    $smart[] = [
        "sev" => "MEDIO",
        "title" => "Optimización operativa",
        "body" => "La salud está estable, pero hay espacio para mejorar ocupación, cobranza y mantenimiento."
    ];
} else {
    $smart[] = [
        "sev" => "LEVE",
        "title" => "Negocio saludable",
        "body" => "La operación se mantiene fuerte. Puedes enfocarte en crecimiento, fidelización y aumento de tarifa en vehículos de alta demanda."
    ];
}

if ($pending_amt > 0) {
    $smart[] = [
        "sev" => ($pending_amt >= 15000 ? "ALTO" : "MEDIO"),
        "title" => "Cobranza prioritaria",
        "body" => "Hay " . rt_money($pending_amt) . " pendientes. Este dinero debe revisarse antes de considerar utilidad real."
    ];
}

if ($idle >= 3) {
    $smart[] = [
        "sev" => "MEDIO",
        "title" => "Flota detenida",
        "body" => "Tienes " . $idle . " vehículos sin renta activa. Considera promoción, revisión de precio o publicación destacada."
    ];
}

if ($worst_maint_vehicle && floatval($worst_maint_vehicle["maint_cost"] ?? 0) > 0) {
    $smart[] = [
        "sev" => "MEDIO",
        "title" => "Mantenimiento elevado",
        "body" => "El vehículo " . ($worst_maint_vehicle["name"] ?? "") . " (" . ($worst_maint_vehicle["plate"] ?? "") . ") es el que más ha consumido mantenimiento este mes."
    ];
}

if ($top_vehicle && floatval($top_vehicle["income"] ?? 0) > 0) {
    $smart[] = [
        "sev" => "LEVE",
        "title" => "Vehículo estrella",
        "body" => "El vehículo " . ($top_vehicle["name"] ?? "") . " (" . ($top_vehicle["plate"] ?? "") . ") es el que más dinero produce este mes. Evalúa subir tarifa o priorizar disponibilidad."
    ];
}

if (count($risk_vehicles) > 0) {
    $smart[] = [
        "sev" => "ALTO",
        "title" => "Vehículos con baja rentabilidad",
        "body" => "Hay " . count($risk_vehicles) . " vehículo(s) donde el mantenimiento está afectando la ganancia. Revisa si conviene reparar, pausar o vender."
    ];
}

foreach ($acciones as $a) {
    $smart[] = [
        "sev" => "LEVE",
        "title" => "Acción sugerida",
        "body" => $a
    ];
}
?>

<style>
html, body{
  overflow-x:hidden !important;
}
.content-wrapper,
.content,
.container-fluid{
  overflow-x:hidden !important;
}

.rt-health{
  background:#16181d;
  border-radius:18px;
  padding:20px;
  box-shadow:0 10px 28px rgba(0,0,0,.35);
  margin-bottom:20px;
  border:1px solid rgba(255,255,255,.07);
}
.rt-health-bar{
  height:18px;
  border-radius:999px;
  background:rgba(255,255,255,.08);
  overflow:hidden;
}
.rt-health-bar div{ height:18px; }
.rt-health-meta{
  margin-top:8px;
  color:#bdbdbd;
  font-weight:800;
  font-size:12px;
}
.rt-med,
.rt-ai-card{
  max-width:100%;
  overflow:hidden;
  word-break:break-word;
  background:#0f1115;
  border:1px solid rgba(255,255,255,.08);
  border-radius:16px;
  padding:14px;
  margin-top:12px;
}
.rt-med h5,
.rt-ai-card h5{ color:#fff; font-weight:900; margin:0 0 10px 0; }
.rt-med .meta,
.rt-ai-card .meta{ color:#bdbdbd; font-weight:800; font-size:12px; }
.rt-med .dx,
.rt-ai-card .dx{
  background:#0b0d10;
  border:1px solid rgba(255,255,255,.08);
  border-radius:14px;
  padding:12px;
  margin-top:10px;
}
.rt-med .dx-title,
.rt-ai-card .dx-title{ color:#fff; font-weight:900; margin:0; }
.rt-med .dx-body,
.rt-ai-card .dx-body{ color:#cfcfcf; font-weight:800; margin:6px 0 0 0; }
.rt-med ul{
  margin:10px 0 0 0;
  padding-left:18px;
  color:#bdbdbd;
  font-weight:800;
}
.rt-kpi-grid{
  display:grid;
  grid-template-columns:repeat(4,minmax(0,1fr));
  max-width:100%;
  overflow:hidden;
  gap:12px;
  margin-bottom:14px;
}
.rt-kpi-mini{
  min-width:0;
  max-width:100%;
  overflow:hidden;
  background:#0f1115;
  border:1px solid rgba(255,255,255,.08);
  border-radius:16px;
  padding:14px;
  box-shadow:0 10px 28px rgba(0,0,0,.25);
}
.rt-kpi-mini .label{
  color:#bdbdbd;
  font-weight:900;
  font-size:12px;
  text-transform:uppercase;
  letter-spacing:.4px;
}
.rt-kpi-mini .value{
  color:#fff;
  font-weight:900;
  font-size:22px;
  margin-top:5px;
}
.rt-kpi-mini .sub{
  color:#9aa0a6;
  font-weight:800;
  font-size:12px;
  margin-top:4px;
}
.rt-table{
  color:#eaeaea;
  margin:0;
  width:100%;
}
.table-responsive{
  max-width:100%;
  overflow-x:auto;
}
.rt-table th{
  color:#bdbdbd;
  border-color:rgba(255,255,255,.08)!important;
  font-size:12px;
  text-transform:uppercase;
}
.rt-table td{
  border-color:rgba(255,255,255,.06)!important;
  vertical-align:middle!important;
  font-weight:800;
}
.rt-profit-good{ color:#2ecc71!important; font-weight:900; }
.rt-profit-bad{ color:#e74c3c!important; font-weight:900; }
@media(max-width:991px){
  .rt-kpi-grid{ grid-template-columns:repeat(2,1fr); }
}
@media(max-width:575px){
  .rt-kpi-grid{ grid-template-columns:1fr; }
}
</style>

<br>

<div class="rt-health">
  <div class="d-flex align-items-center justify-content-between flex-wrap">
    <h4 style="color:#fff;font-weight:900;margin:0;">🧠 Salud del Negocio</h4>
    <span class="badge badge-<?php echo ($riesgo >= 60 ? 'danger' : ($riesgo >= 30 ? 'warning' : 'success')); ?>" style="font-size:14px;font-weight:900;">
      <?php echo intval($riesgo); ?>% Riesgo
    </span>
  </div>

  <div class="rt-health-bar mt-3">
    <div style="width:<?php echo intval($score); ?>%; background:<?php echo rt_e($color); ?>;"></div>
  </div>

  <div style="margin-top:10px;color:#fff;font-weight:900;font-size:18px;">
    Salud: <?php echo intval($score); ?>% — <?php echo rt_e($status); ?>
  </div>

  <div class="rt-health-meta">
    Mes: <b><?php echo rt_e($todayYM); ?></b> · 
    Ingresos: <b><?php echo rt_money($income); ?></b> ·
    Gastos negocio: <b><?php echo rt_money($expenses); ?></b> ·
    Pendientes: <b><?php echo intval($pending_cnt); ?></b> ·
    Ocupación: <b><?php echo round($occupancy); ?>%</b> ·
    Mantenimiento abierto: <b><?php echo intval($maint_open); ?></b>
  </div>
</div>

<div class="rt-kpi-grid">
  <div class="rt-kpi-mini">
    <div class="label">Ingresos mes</div>
    <div class="value"><?php echo rt_money($income); ?></div>
    <div class="sub">Pagos registrados</div>
  </div>

  <div class="rt-kpi-mini">
    <div class="label">Gastos negocio</div>
    <div class="value"><?php echo rt_money($expenses); ?></div>
    <div class="sub">No incluye mantenimiento por vehículo</div>
  </div>

  <div class="rt-kpi-mini">
    <div class="label">Ocupación</div>
    <div class="value"><?php echo round($occupancy); ?>%</div>
    <div class="sub"><?php echo intval($rentedCars); ?> rentados / <?php echo intval($totalCars); ?> total</div>
  </div>

  <div class="rt-kpi-mini">
    <div class="label">Crecimiento</div>
    <div class="value" style="color:<?php echo ($growth >= 0 ? '#2ecc71' : '#e74c3c'); ?>;">
      <?php echo round($growth); ?>%
    </div>
    <div class="sub">Comparado con mes anterior</div>
  </div>
</div>

<div class="rt-ai-card">
  <div class="d-flex align-items-center justify-content-between flex-wrap">
    <h5>🚀 Centro Inteligente Empresarial</h5>
    <span class="badge badge-primary" style="font-size:13px;font-weight:900;">IA Operacional</span>
  </div>

  <div class="meta">Evaluación automática basada en reservas, pagos, mantenimiento y flota.</div>

  <div class="row mt-2">
    <div class="col-md-6">
      <div class="dx">
        <p class="dx-title">🔥 Vehículo que más produce</p>

        <?php if ($top_vehicle && floatval($top_vehicle["income"] ?? 0) > 0): ?>
          <p class="dx-body">
            <?php echo rt_e(($top_vehicle["name"] ?? "") . " (" . ($top_vehicle["plate"] ?? "") . ")"); ?><br>
            Ingresos del mes: <b class="rt-profit-good"><?php echo rt_money($top_vehicle["income"] ?? 0); ?></b><br>
            Reservas relacionadas: <b><?php echo intval($top_vehicle["bookings"] ?? 0); ?></b>
          </p>
        <?php else: ?>
          <p class="dx-body">No hay ingresos por vehículo registrados este mes.</p>
        <?php endif; ?>

      </div>
    </div>

    <div class="col-md-6">
      <div class="dx">
        <p class="dx-title">💸 Vehículo con más mantenimiento</p>

        <?php if ($worst_maint_vehicle && floatval($worst_maint_vehicle["maint_cost"] ?? 0) > 0): ?>
          <p class="dx-body">
            <?php echo rt_e(($worst_maint_vehicle["name"] ?? "") . " (" . ($worst_maint_vehicle["plate"] ?? "") . ")"); ?><br>
            Mantenimiento del mes: <b class="rt-profit-bad"><?php echo rt_money($worst_maint_vehicle["maint_cost"] ?? 0); ?></b><br>
            Registros: <b><?php echo intval($worst_maint_vehicle["total_maint"] ?? 0); ?></b>
          </p>
        <?php else: ?>
          <p class="dx-body">No hay mantenimiento registrado este mes.</p>
        <?php endif; ?>

      </div>
    </div>
  </div>

  <div class="dx">
    <p class="dx-title">🧠 Recomendaciones inteligentes</p>

    <?php foreach ($smart as $s): ?>
      <div style="padding:10px 0;border-top:1px solid rgba(255,255,255,.06);">
        <span class="<?php echo rt_e(rt_sev_badge($s["sev"] ?? "LEVE")); ?>" style="font-weight:900;">
          <?php echo rt_e($s["sev"] ?? "LEVE"); ?>
        </span>

        <b style="color:#fff;margin-left:6px;">
          <?php echo rt_e($s["title"] ?? ""); ?>
        </b>

        <div style="color:#cfcfcf;font-weight:800;margin-top:4px;">
          <?php echo rt_e($s["body"] ?? ""); ?>
        </div>
      </div>
    <?php endforeach; ?>

  </div>
</div>

<div class="rt-ai-card">
  <h5>🏆 Ranking de Rentabilidad por Vehículo</h5>
  <div class="meta">Ingresos por pagos vinculados a reservas menos mantenimiento del mes.</div>

  <div class="table-responsive mt-2">
    <table class="table rt-table table-sm">
      <thead>
        <tr>
          <th>Vehículo</th>
          <th>Ficha</th>
          <th>Ingresos</th>
          <th>Mantenimiento</th>
          <th>Ganancia aprox.</th>
          <th>Estado</th>
        </tr>
      </thead>

      <tbody>
        <?php if (count($vehicle_rank) > 0): ?>

          <?php foreach ($vehicle_rank as $v): ?>
            <?php
              $profit = floatval($v["profit"] ?? 0);
              $income_v = floatval($v["income"] ?? 0);
              $maint_v = floatval($v["maint_cost"] ?? 0);

              if ($income_v <= 0 && $maint_v <= 0) {
                  $estado = "Sin movimiento";
                  $badge = "badge badge-secondary";
              } elseif ($profit < 0) {
                  $estado = "Riesgo";
                  $badge = "badge badge-danger";
              } elseif ($maint_v > 0 && $maint_v >= ($income_v * 0.50)) {
                  $estado = "Revisar";
                  $badge = "badge badge-warning";
              } else {
                  $estado = "Rentable";
                  $badge = "badge badge-success";
              }
            ?>

            <tr>
              <td><?php echo rt_e(($v["name"] ?? "") . " (" . ($v["plate"] ?? "") . ")"); ?></td>
              <td><?php echo rt_e($v["token"] ?? ""); ?></td>
              <td><?php echo rt_money($income_v); ?></td>
              <td><?php echo rt_money($maint_v); ?></td>
              <td class="<?php echo ($profit >= 0 ? 'rt-profit-good' : 'rt-profit-bad'); ?>">
                <?php echo rt_money($profit); ?>
              </td>
              <td>
                <span class="<?php echo rt_e($badge); ?>" style="font-weight:900;">
                  <?php echo rt_e($estado); ?>
                </span>
              </td>
            </tr>

          <?php endforeach; ?>

        <?php else: ?>
          <tr>
            <td colspan="6" style="color:#bdbdbd;">No hay datos suficientes para generar ranking.</td>
          </tr>
        <?php endif; ?>
      </tbody>

    </table>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="rt-ai-card">
      <h5>🚗 Vehículos sin producir en 30 días</h5>
      <div class="meta">Carros sin pagos vinculados a reservas en los últimos 30 días.</div>

      <?php if (count($idle_vehicles) > 0): ?>
        <?php foreach ($idle_vehicles as $v): ?>
          <div class="dx">
            <p class="dx-title">
              <?php echo rt_e(($v["name"] ?? "") . " (" . ($v["plate"] ?? "") . ")"); ?>
            </p>
            <p class="dx-body">
              Ficha: <b><?php echo rt_e($v["token"] ?? ""); ?></b><br>
              Última renta registrada:
              <b>
                <?php
                  $last_rent = $v["last_rent"] ?? null;
                  echo $last_rent ? date("d-m-Y", strtotime($last_rent)) : "Sin historial";
                ?>
              </b>
            </p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="dx">
          <p class="dx-body">No se detectaron vehículos improductivos en los últimos 30 días.</p>
        </div>
      <?php endif; ?>

    </div>
  </div>

  <div class="col-md-6">
    <div class="rt-ai-card">
      <h5>⚠️ Vehículos en riesgo operativo</h5>
      <div class="meta">Mantenimiento alto o ganancia negativa en el mes.</div>

      <?php if (count($risk_vehicles) > 0): ?>
        <?php foreach ($risk_vehicles as $v): ?>
          <div class="dx">
            <p class="dx-title">
              <?php echo rt_e(($v["name"] ?? "") . " (" . ($v["plate"] ?? "") . ")"); ?>
            </p>
            <p class="dx-body">
              Ingresos: <b><?php echo rt_money($v["income"] ?? 0); ?></b><br>
              Mantenimiento: <b class="rt-profit-bad"><?php echo rt_money($v["maint_cost"] ?? 0); ?></b><br>
              Resultado:
              <b class="<?php echo (floatval($v["profit"] ?? 0) >= 0 ? 'rt-profit-good' : 'rt-profit-bad'); ?>">
                <?php echo rt_money($v["profit"] ?? 0); ?>
              </b>
            </p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="dx">
          <p class="dx-body">No se detectaron vehículos con riesgo operativo fuerte este mes.</p>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<div class="rt-med">
  <div class="d-flex align-items-center justify-content-between flex-wrap">
    <h5>🩺 Historial del Negocio (Explicación del Riesgo)</h5>

    <span class="badge badge-<?php echo ($riesgo >= 60 ? 'danger' : ($riesgo >= 30 ? 'warning' : 'success')); ?>" style="font-size:14px;font-weight:900;">
      <?php echo intval($riesgo); ?>% Riesgo
    </span>
  </div>

  <div class="meta">
    Evaluación: <?php echo date("d-m-Y h:i a"); ?> · Fuente: Reservas / Pagos / Flota / Mantenimiento
  </div>

  <?php foreach ($dx as $i => $d): ?>
    <div class="dx">
      <div class="d-flex align-items-center justify-content-between flex-wrap">
        <p class="dx-title">
          <?php echo intval($i + 1) . ". " . rt_e($d["title"] ?? ""); ?>
        </p>

        <span class="<?php echo rt_e(rt_sev_badge($d["sev"] ?? "LEVE")); ?>" style="font-weight:900;">
          <?php echo rt_e($d["sev"] ?? "LEVE"); ?>
        </span>
      </div>

      <p class="dx-body">
        <?php echo rt_e($d["body"] ?? ""); ?>
      </p>
    </div>
  <?php endforeach; ?>

  <div class="dx">
    <p class="dx-title">🧾 Hallazgos relevantes</p>
    <ul>
      <?php foreach ($hallazgos as $h): ?>
        <li><?php echo rt_e($h); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>