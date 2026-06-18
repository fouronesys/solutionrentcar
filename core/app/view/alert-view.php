<?php
// =======================================================
// 🔔 CENTRO DE ALERTAS INTELIGENTES - PHP 8.4
// =======================================================

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
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
$hoy      = date("Y-m-d");
$ahora    = date("d-m-Y h:i a");

if (!isset(Core::$symbol) || Core::$symbol == "") {
    Core::$symbol = "RD$";
}

// =======================================================
// HELPERS
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

function rt_sev_class($sev){
    if ($sev == "ALTO") return "badge badge-danger";
    if ($sev == "MEDIO") return "badge badge-warning";
    if ($sev == "LEVE") return "badge badge-info";
    return "badge badge-success";
}

function rt_sev_color($sev){
    if ($sev == "ALTO") return "#e74c3c";
    if ($sev == "MEDIO") return "#f1c40f";
    if ($sev == "LEVE") return "#17a2b8";
    return "#2ecc71";
}

function rt_icon($type){
    if ($type == "cash") return "fa fa-money-bill-wave";
    if ($type == "car") return "fa fa-car";
    if ($type == "tool") return "fa fa-tools";
    if ($type == "trend") return "fa fa-chart-line";
    if ($type == "warn") return "fa fa-exclamation-triangle";
    if ($type == "gps") return "fa fa-map-marker-alt";
    if ($type == "client") return "fa fa-user";
    if ($type == "time") return "fa fa-clock";
    return "fa fa-bell";
}

function rt_money_alert($amount){
    return Core::$symbol . " " . number_format(floatval($amount), 2, ".", ",");
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

function rt_days_from_date($date){
    if (empty($date)) {
        return 9999;
    }

    $ts = strtotime($date);

    if ($ts === false) {
        return 9999;
    }

    return (int) floor((time() - $ts) / 86400);
}

function rt_minutes_from_date($date){
    if (empty($date)) {
        return 999999;
    }

    $ts = strtotime($date);

    if ($ts === false) {
        return 999999;
    }

    return (int) floor((time() - $ts) / 60);
}

$alerts = [];

$income_today = 0;
$avg7 = 0;
$maint_open = 0;
$gasto_hoy = 0;

// =======================================================
// 0) Detectar link payment -> booking
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
// 1) 💰 COBRANZA: Reservas activas con deuda
// =======================================================

if (
    $payment_link_col &&
    rt_has_table($con, "booking") &&
    rt_has_table($con, "payment") &&
    rt_has_table($con, "cars") &&
    rt_has_table($con, "person")
) {
    $sql_deuda = "
        SELECT 
            b.id,
            b.person_id,
            b.car_id,
            IFNULL(b.total,0) total,
            IFNULL(p.pagado,0) pagado,
            (IFNULL(b.total,0) - IFNULL(p.pagado,0)) deuda,
            c.name AS car_name,
            c.plate,
            per.name AS client_name
        FROM booking b
        LEFT JOIN (
            SELECT {$payment_link_col} AS link_id, SUM(val) pagado
            FROM payment
            WHERE stock_id = $stock_id
            GROUP BY {$payment_link_col}
        ) p ON p.link_id = b.id
        LEFT JOIN cars c ON c.id = b.car_id
        LEFT JOIN person per ON per.id = b.person_id
        WHERE b.stock_id = $stock_id
          AND b.status IN (0,1)
          AND (IFNULL(b.total,0) - IFNULL(p.pagado,0)) > 0
        ORDER BY deuda DESC
        LIMIT 8
    ";

    $q_deuda = rt_query($con, $sql_deuda);

    if ($q_deuda && $q_deuda->num_rows > 0) {
        while ($r = $q_deuda->fetch_assoc()) {
            $deuda = floatval($r["deuda"] ?? 0);
            $sev   = ($deuda >= 15000 ? "ALTO" : ($deuda >= 5000 ? "MEDIO" : "LEVE"));

            $cliente = !empty($r["client_name"]) ? $r["client_name"] : "Cliente #" . ($r["person_id"] ?? "");
            $vehiculo = trim(($r["car_name"] ?? "") . " " . (!empty($r["plate"]) ? "(" . $r["plate"] . ")" : ""));

            $alerts[] = [
                "sev"    => $sev,
                "type"   => "cash",
                "title"  => "Cobranza pendiente",
                "desc"   => "La reserva <b>#" . rt_e($r["id"] ?? "") . "</b> tiene deuda de <b>" . rt_money_alert($deuda) . "</b>.",
                "meta"   => "Cliente: <b>" . rt_e($cliente) . "</b>" . ($vehiculo != "" ? " · Vehículo: <b>" . rt_e($vehiculo) . "</b>" : ""),
                "impact" => ($sev == "ALTO" ? "Impacto: riesgo fuerte de caja si no se cobra hoy." : ($sev == "MEDIO" ? "Impacto: puede afectar el flujo de caja." : "Impacto: mantener seguimiento para evitar acumulación.")),
                "action" => "Cobrar / revisar reserva",
                "link"   => "./?view=booking&opt=all"
            ];
        }
    } else {
        $alerts[] = [
            "sev"    => "OK",
            "type"   => "cash",
            "title"  => "Cobranza estable",
            "desc"   => "No se detectaron reservas activas con deuda pendiente.",
            "meta"   => "Flujo de caja sin deuda activa detectada.",
            "impact" => "Impacto: operación financiera más limpia.",
            "action" => "Ver finanzas",
            "link"   => "./?view=finance&opt=all"
        ];
    }
}

// =======================================================
// 2) 🚗 VEHÍCULOS PARADOS
// =======================================================

if (rt_has_table($con, "cars") && rt_has_table($con, "booking")) {
    $idle_days = 5;

    $sql_idle = "
        SELECT 
            c.id,
            c.name,
            c.plate,
            c.token,
            MAX(IFNULL(b.end_at, b.created_at)) AS last_move
        FROM cars c
        LEFT JOIN booking b
            ON b.stock_id = $stock_id
           AND b.car_id = c.id
        WHERE c.stock_id = $stock_id
        GROUP BY c.id
        ORDER BY last_move ASC
        LIMIT 10
    ";

    $q_idle = rt_query($con, $sql_idle);

    if ($q_idle) {
        while ($r = $q_idle->fetch_assoc()) {
            $last = $r["last_move"] ?? null;
            $days = rt_days_from_date($last);

            if ($days >= $idle_days) {
                $sev = ($days >= 15 ? "ALTO" : ($days >= 10 ? "MEDIO" : "LEVE"));
                $carLabel = trim(($r["name"] ?? "") . " " . (!empty($r["plate"]) ? "(" . $r["plate"] . ")" : ""));

                $alerts[] = [
                    "sev"    => $sev,
                    "type"   => "car",
                    "title"  => "Vehículo parado",
                    "desc"   => "El vehículo <b>" . rt_e($carLabel) . "</b> lleva <b>$days día(s)</b> sin movimiento.",
                    "meta"   => "Ficha: <b>" . rt_e($r["token"] ?? "") . "</b> · Última actividad: <b>" . ($last ? date("d-m-Y", strtotime($last)) : "Sin historial") . "</b>",
                    "impact" => ($sev == "ALTO" ? "Impacto: alta pérdida de ingresos por flota detenida." : ($sev == "MEDIO" ? "Impacto: baja rotación y menor ocupación." : "Impacto: oportunidad de rotación rápida.")),
                    "action" => "Promover / revisar tarifa",
                    "link"   => "./?view=cars&opt=all"
                ];
            }
        }
    }
}

// =======================================================
// 3) 🛠️ MANTENIMIENTO ABIERTO
// =======================================================

if (rt_has_table($con, "maintenance")) {
    $sql_maint = "SELECT COUNT(*) c FROM maintenance WHERE stock_id=$stock_id AND status=0";
    $q_maint = rt_query($con, $sql_maint);

    if ($q_maint) {
        $tmp = $q_maint->fetch_assoc();
        $maint_open = intval($tmp["c"] ?? 0);
    }

    if ($maint_open > 0) {
        $sev = ($maint_open >= 5 ? "ALTO" : ($maint_open >= 3 ? "MEDIO" : "LEVE"));

        $alerts[] = [
            "sev"    => $sev,
            "type"   => "tool",
            "title"  => "Mantenimientos abiertos",
            "desc"   => "Hay <b>$maint_open</b> mantenimiento(s) abierto(s).",
            "meta"   => "Recomendación: cerrar órdenes y revisar disponibilidad de la flota.",
            "impact" => ($sev == "ALTO" ? "Impacto: riesgo alto de flota fuera de servicio." : ($sev == "MEDIO" ? "Impacto: puede reducir disponibilidad." : "Impacto: mantener control preventivo.")),
            "action" => "Ver mantenimiento",
            "link"   => "./?view=finance&opt=vehicle"
        ];
    }
}

// =======================================================
// 4) 📉 INGRESOS BAJOS HOY
// =======================================================

if (rt_has_table($con, "payment")) {
    $sql_income_today = "
        SELECT IFNULL(SUM(val),0) t
        FROM payment
        WHERE stock_id=$stock_id AND DATE(created_at)='$hoy'
    ";

    $q_it = rt_query($con, $sql_income_today);

    if ($q_it) {
        $tmp = $q_it->fetch_assoc();
        $income_today = floatval($tmp["t"] ?? 0);
    }

    $sql_last7 = "
        SELECT DATE(created_at) d, IFNULL(SUM(val),0) t
        FROM payment
        WHERE stock_id=$stock_id
          AND DATE(created_at) >= DATE_SUB('$hoy', INTERVAL 7 DAY)
          AND DATE(created_at) <= DATE_SUB('$hoy', INTERVAL 1 DAY)
        GROUP BY d
    ";

    $q7 = rt_query($con, $sql_last7);

    if ($q7) {
        $sum = 0;
        $cnt = 0;

        while ($r = $q7->fetch_assoc()) {
            $sum += floatval($r["t"] ?? 0);
            $cnt++;
        }

        $avg7 = ($cnt > 0 ? ($sum / $cnt) : 0);
    }

    if ($avg7 > 0) {
        $ratio = ($income_today / $avg7) * 100;

        if ($ratio < 60) {
            $sev = ($ratio < 40 ? "ALTO" : "MEDIO");

            $alerts[] = [
                "sev"    => $sev,
                "type"   => "trend",
                "title"  => "Ingresos bajos hoy",
                "desc"   => "Hoy: <b>" . rt_money_alert($income_today) . "</b> vs promedio 7 días: <b>" . rt_money_alert($avg7) . "</b>.",
                "meta"   => "Rendimiento actual: <b>" . round($ratio) . "%</b> del promedio.",
                "impact" => ($sev == "ALTO" ? "Impacto: día crítico de caja si no se reacciona." : "Impacto: puede bajar el rendimiento del mes."),
                "action" => "Revisar cobranza",
                "link"   => "./?view=finance&opt=all"
            ];
        }
    }
}

// =======================================================
// 5) ⛽ GASTOS ALTOS HOY
// =======================================================

$umbral_gasto_hoy = 5000;

$spends_total = 0;
$maint_total  = 0;
$fuel_total   = 0;
$toll_total   = 0;

if (rt_has_table($con, "spends")) {
    $q = rt_query($con, "SELECT IFNULL(SUM(price),0) t FROM spends WHERE stock_id=$stock_id AND DATE(created_at)='$hoy'");
    if ($q) {
        $tmp = $q->fetch_assoc();
        $spends_total = floatval($tmp["t"] ?? 0);
    }
}

if (rt_has_table($con, "maintenance")) {
    $q = rt_query($con, "SELECT IFNULL(SUM(purchase_price),0) t FROM maintenance WHERE stock_id=$stock_id AND DATE(created_at)='$hoy'");
    if ($q) {
        $tmp = $q->fetch_assoc();
        $maint_total = floatval($tmp["t"] ?? 0);
    }
}

if (rt_has_table($con, "fuels")) {
    $q = rt_query($con, "SELECT IFNULL(SUM(price),0) t FROM fuels WHERE stock_id=$stock_id AND DATE(created_at)='$hoy'");
    if ($q) {
        $tmp = $q->fetch_assoc();
        $fuel_total = floatval($tmp["t"] ?? 0);
    }
}

if (rt_has_table($con, "toll")) {
    $q = rt_query($con, "SELECT IFNULL(SUM(price),0) t FROM toll WHERE stock_id=$stock_id AND DATE(created_at)='$hoy'");
    if ($q) {
        $tmp = $q->fetch_assoc();
        $toll_total = floatval($tmp["t"] ?? 0);
    }
}

$gasto_hoy = $spends_total + $maint_total + $fuel_total + $toll_total;

if ($gasto_hoy >= $umbral_gasto_hoy) {
    $sev = ($gasto_hoy >= ($umbral_gasto_hoy * 2) ? "ALTO" : "MEDIO");

    $alerts[] = [
        "sev"    => $sev,
        "type"   => "warn",
        "title"  => "Gastos altos hoy",
        "desc"   => "Gastos de hoy suman <b>" . rt_money_alert($gasto_hoy) . "</b>.",
        "meta"   => "Negocio: " . rt_money_alert($spends_total) . " · Mant.: " . rt_money_alert($maint_total) . " · Comb.: " . rt_money_alert($fuel_total) . " · Peajes: " . rt_money_alert($toll_total),
        "impact" => ($sev == "ALTO" ? "Impacto: puede dejar el día en pérdidas." : "Impacto: puede reducir ganancias del día."),
        "action" => "Validar gastos",
        "link"   => "./?view=finance&opt=all"
    ];
}

// =======================================================
// 6) 📍 GPS SIN SEÑAL
// =======================================================

if (
    rt_has_table($con, "gps_devices") &&
    rt_has_table($con, "gps_positions") &&
    rt_has_table($con, "cars") &&
    rt_has_column($con, "cars", "gps_id")
) {
    $sql_gps = "
        SELECT
            c.id,
            c.name,
            c.plate,
            c.token,
            g.imei,
            MAX(p.created_at) AS last_signal
        FROM cars c
        INNER JOIN gps_devices g ON g.id = c.gps_id
        LEFT JOIN gps_positions p ON p.gps_id = g.id
        WHERE c.stock_id=$stock_id
          AND c.gps_id IS NOT NULL
          AND c.gps_id > 0
        GROUP BY c.id
        ORDER BY last_signal ASC
        LIMIT 8
    ";

    $q_gps = rt_query($con, $sql_gps);

    if ($q_gps) {
        while ($r = $q_gps->fetch_assoc()) {
            $last = $r["last_signal"] ?? null;
            $mins = rt_minutes_from_date($last);

            if ($mins >= 30) {
                $sev = ($mins >= 180 ? "ALTO" : ($mins >= 60 ? "MEDIO" : "LEVE"));
                $carLabel = trim(($r["name"] ?? "") . " " . (!empty($r["plate"]) ? "(" . $r["plate"] . ")" : ""));

                $alerts[] = [
                    "sev"    => $sev,
                    "type"   => "gps",
                    "title"  => "GPS sin señal reciente",
                    "desc"   => "El vehículo <b>" . rt_e($carLabel) . "</b> tiene GPS sin señal reciente.",
                    "meta"   => "IMEI: <b>" . rt_e($r["imei"] ?? "") . "</b> · Última señal: <b>" . ($last ? date("d-m-Y h:i a", strtotime($last)) : "Nunca") . "</b>",
                    "impact" => ($sev == "ALTO" ? "Impacto: riesgo alto de perder visibilidad del vehículo." : "Impacto: verificar conexión, dispositivo o cobertura."),
                    "action" => "Abrir mapa GPS",
                    "link"   => "./?view=gps&opt=map"
                ];
            }
        }
    }
}

// =======================================================
// 7) ⏰ RESERVAS QUE VENCEN HOY / MAÑANA
// =======================================================

if (
    rt_has_table($con, "booking") &&
    rt_has_table($con, "cars") &&
    rt_has_table($con, "person") &&
    rt_has_column($con, "booking", "end_at")
) {
    $manana = date("Y-m-d", strtotime("+1 day"));

    $sql_due = "
        SELECT
            b.id,
            b.end_at,
            c.name AS car_name,
            c.plate,
            per.name AS client_name
        FROM booking b
        LEFT JOIN cars c ON c.id=b.car_id
        LEFT JOIN person per ON per.id=b.person_id
        WHERE b.stock_id=$stock_id
          AND b.status=1
          AND DATE(b.end_at) BETWEEN '$hoy' AND '$manana'
        ORDER BY b.end_at ASC
        LIMIT 8
    ";

    $q_due = rt_query($con, $sql_due);

    if ($q_due) {
        while ($r = $q_due->fetch_assoc()) {
            $end_at = $r["end_at"] ?? null;

            if (empty($end_at)) {
                continue;
            }

            $fecha = date("Y-m-d", strtotime($end_at));
            $sev = ($fecha == $hoy ? "MEDIO" : "LEVE");

            $carLabel = trim(($r["car_name"] ?? "") . " " . (!empty($r["plate"]) ? "(" . $r["plate"] . ")" : ""));
            $cliente = !empty($r["client_name"]) ? $r["client_name"] : "Cliente no especificado";

            $alerts[] = [
                "sev"    => $sev,
                "type"   => "time",
                "title"  => ($fecha == $hoy ? "Reserva vence hoy" : "Reserva vence mañana"),
                "desc"   => "La reserva <b>#" . rt_e($r["id"] ?? "") . "</b> está próxima a finalizar.",
                "meta"   => "Cliente: <b>" . rt_e($cliente) . "</b> · Vehículo: <b>" . rt_e($carLabel) . "</b> · Entrega: <b>" . date("d-m-Y h:i a", strtotime($end_at)) . "</b>",
                "impact" => "Impacto: preparar recepción, extensión o revisión del vehículo.",
                "action" => "Ver reservas",
                "link"   => "./?view=booking&opt=all"
            ];
        }
    }
}

// =======================================================
// CONTADORES
// =======================================================

$cnt_alto = 0;
$cnt_medio = 0;
$cnt_leve = 0;
$cnt_ok = 0;

foreach ($alerts as $a) {
    $sev = $a["sev"] ?? "OK";

    if ($sev == "ALTO") {
        $cnt_alto++;
    } elseif ($sev == "MEDIO") {
        $cnt_medio++;
    } elseif ($sev == "LEVE") {
        $cnt_leve++;
    } else {
        $cnt_ok++;
    }
}

$operational_status = "Operación estable";
$operational_color = "#2ecc71";

if ($cnt_alto > 0) {
    $operational_status = "Atención urgente";
    $operational_color = "#e74c3c";
} elseif ($cnt_medio > 0) {
    $operational_status = "Revisión recomendada";
    $operational_color = "#f1c40f";
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
.rt-alert-page{
    max-width:100%;
    overflow:hidden;
}
.rt-page-title{
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom:12px;
    flex-wrap:wrap;
}
.rt-page-title h3{
    color:#fff;
    font-weight:900;
    margin:0;
}
.rt-page-title span{
    color:#9aa0a6;
    font-weight:700;
}
.rt-alert-hero{
    background:#11141a;
    border-radius:22px;
    box-shadow:0 10px 28px rgba(0,0,0,.35);
    border:1px solid rgba(255,255,255,.06);
    padding:18px;
    margin-bottom:14px;
    overflow:hidden;
}
.rt-alert-hero h4{
    color:#fff;
    font-weight:900;
    margin:0;
}
.rt-alert-hero .status{
    display:inline-block;
    padding:7px 12px;
    border-radius:999px;
    color:#111;
    background:<?php echo rt_e($operational_color); ?>;
    font-weight:900;
}
.rt-alert-grid{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:12px;
    margin-top:14px;
}
.rt-alert-kpi{
    background:#0b0d10;
    border:1px solid rgba(255,255,255,.08);
    border-radius:16px;
    padding:14px;
    min-width:0;
    overflow:hidden;
}
.rt-alert-kpi .label{
    color:#bdbdbd;
    font-weight:900;
    font-size:12px;
    text-transform:uppercase;
}
.rt-alert-kpi .value{
    color:#fff;
    font-weight:900;
    font-size:24px;
    margin-top:4px;
}
.rt-block{
    background:#16181d;
    border-radius:20px;
    box-shadow:0 10px 28px rgba(0,0,0,.35);
    border:1px solid rgba(255,255,255,.06);
    padding:18px;
    max-width:100%;
    overflow:hidden;
}
.rt-cardx{
    background:#0f1115;
    border-radius:18px;
    padding:16px;
    border:1px solid rgba(255,255,255,.08);
    margin-bottom:14px;
    max-width:100%;
    overflow:hidden;
    word-break:break-word;
}
.rt-cardx .top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    flex-wrap:wrap;
}
.rt-cardx .title{
    color:#fff;
    font-weight:900;
    font-size:18px;
    margin:0;
}
.rt-cardx .desc{
    color:#cfcfcf;
    font-weight:800;
    margin:8px 0 0 0;
}
.rt-cardx .meta{
    color:#9aa0a6;
    font-weight:800;
    margin:8px 0 0 0;
    font-size:13px;
}
.rt-cardx .impact{
    color:#2ecc71;
    font-weight:900;
    margin:10px 0 0 0;
}
.rt-link{
    margin-top:12px;
}
.rt-link a{
    font-weight:900;
    text-decoration:none;
}
.rt-alert-left{
    display:flex;
    align-items:center;
    gap:10px;
    min-width:0;
}
.rt-alert-icon{
    width:42px;
    height:42px;
    min-width:42px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    background:rgba(255,255,255,.08);
    border:1px solid rgba(255,255,255,.10);
}
@media(max-width:991px){
    .rt-alert-grid{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }
}
@media(max-width:575px){
    .rt-alert-grid{
        grid-template-columns:1fr;
    }
}
</style>

<br>

<section class="content rt-alert-page">
    <div class="container-fluid">

        <div class="rt-page-title">
            <h3>
                <i class="fa fa-bell"></i> Alertas Inteligentes
            </h3>

            <span>Centro de control · <?php echo rt_e($ahora); ?></span>

            <span class="badge badge-dark" style="margin-left:auto;font-weight:900;">
                <?php echo count($alerts); ?> alerta(s)
            </span>
        </div>

        <div class="rt-alert-hero">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div>
                    <h4>🧠 Estado Operacional</h4>

                    <div style="color:#bdbdbd;font-weight:800;margin-top:6px;">
                        Sistema revisando cobranza, flota, mantenimiento, GPS, ingresos y vencimientos.
                    </div>
                </div>

                <span class="status">
                    <?php echo rt_e($operational_status); ?>
                </span>
            </div>

            <div class="rt-alert-grid">

                <div class="rt-alert-kpi">
                    <div class="label">Críticas</div>
                    <div class="value" style="color:#e74c3c;">
                        <?php echo intval($cnt_alto); ?>
                    </div>
                </div>

                <div class="rt-alert-kpi">
                    <div class="label">Medias</div>
                    <div class="value" style="color:#f1c40f;">
                        <?php echo intval($cnt_medio); ?>
                    </div>
                </div>

                <div class="rt-alert-kpi">
                    <div class="label">Leves</div>
                    <div class="value" style="color:#17a2b8;">
                        <?php echo intval($cnt_leve); ?>
                    </div>
                </div>

                <div class="rt-alert-kpi">
                    <div class="label">Estables</div>
                    <div class="value" style="color:#2ecc71;">
                        <?php echo intval($cnt_ok); ?>
                    </div>
                </div>

            </div>
        </div>

        <div class="rt-block">

            <?php if (empty($alerts)): ?>

                <div style="color:#9aa0a6;font-weight:800;">
                    No hay alertas críticas en este momento.
                </div>

            <?php else: ?>

                <?php foreach ($alerts as $a): ?>

                    <?php
                    $sev    = $a["sev"] ?? "OK";
                    $type   = $a["type"] ?? "bell";
                    $title  = $a["title"] ?? "";
                    $desc   = $a["desc"] ?? "";
                    $meta   = $a["meta"] ?? "";
                    $impact = $a["impact"] ?? "";
                    $link   = $a["link"] ?? "";
                    $action = $a["action"] ?? "Ver detalle";
                    $color  = rt_sev_color($sev);
                    ?>

                    <div class="rt-cardx" style="border-left:5px solid <?php echo rt_e($color); ?>;">

                        <div class="top">

                            <div class="rt-alert-left">

                                <div class="rt-alert-icon" style="background:<?php echo rt_e($color); ?>33; border-color:<?php echo rt_e($color); ?>55;">
                                    <i class="<?php echo rt_e(rt_icon($type)); ?>"></i>
                                </div>

                                <p class="title">
                                    <?php echo rt_e($title); ?>
                                </p>

                            </div>

                            <span class="<?php echo rt_e(rt_sev_class($sev)); ?>" style="font-weight:900;">
                                <?php echo rt_e($sev); ?>
                            </span>

                        </div>

                        <p class="desc">
                            <?php echo $desc; ?>
                        </p>

                        <?php if (!empty($meta)): ?>
                            <p class="meta">
                                <?php echo $meta; ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($impact)): ?>
                            <p class="impact">
                                <?php echo rt_e($impact); ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($link)): ?>
                            <div class="rt-link">
                                <a href="<?php echo rt_e($link); ?>" class="btn btn-sm btn-warning" style="font-weight:900;">
                                    <?php echo rt_e($action); ?>
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </div>
</section>