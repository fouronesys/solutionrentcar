<section class="content">
<div class="container-fluid">

<div class="row">
<div class="col-md-12">
<br>

<?php
$db  = new Database();
$con = $db->connect();

$stock_id = intval(StockData::getPrincipal()->id);
$hoy = date("Y-m-d");
$mes_actual = date("Y-m");
$dia_actual = intval(date("d"));
$dias_mes = intval(date("t"));

$decisions = [];

function cd_money($amount){
  return Core::$symbol." ".number_format(floatval($amount),2,".",",");
}

function cd_add_decision(&$decisions, $type, $level, $title, $msg, $impact, $link=""){
  $decisions[] = [
    "type"   => $type,
    "level"  => $level,
    "title"  => $title,
    "msg"    => $msg,
    "impact" => $impact,
    "link"   => $link
  ];
}

function cd_level_badge($level){
  if($level=="ALTO") return "badge badge-danger";
  if($level=="MEDIO") return "badge badge-warning";
  if($level=="LEVE") return "badge badge-info";
  return "badge badge-success";
}

function cd_level_color($level){
  if($level=="ALTO") return "#e74c3c";
  if($level=="MEDIO") return "#f1c40f";
  if($level=="LEVE") return "#17a2b8";
  return "#2ecc71";
}

function cd_icon($type){
  if($type=="income") return "📈";
  if($type=="car") return "🚗";
  if($type=="cash") return "💰";
  if($type=="risk") return "⚠️";
  if($type=="health") return "🧠";
  if($type=="price") return "🏷️";
  return "💡";
}

/* ======================================================
   1️⃣ Vehículos parados
====================================================== */
$sql_idle = "
SELECT 
  c.id,
  c.name,
  c.plate,
  c.token,
  MAX(COALESCE(b.end_at,b.created_at)) last_move
FROM cars c
LEFT JOIN booking b
  ON b.car_id=c.id 
 AND b.stock_id=$stock_id
WHERE c.stock_id=$stock_id
GROUP BY c.id
ORDER BY last_move ASC
LIMIT 10
";

$q_idle = $con->query($sql_idle);
if($q_idle){
  while($r=$q_idle->fetch_assoc()){
    if(empty($r["last_move"])){
      cd_add_decision(
        $decisions,
        "car",
        "MEDIO",
        "Vehículo sin historial de renta",
        "El vehículo <b>".htmlspecialchars($r["name"]." (".$r["plate"].")")."</b> no tiene movimiento registrado. Recomendación: verificar si está disponible, publicado o asignado correctamente.",
        "Puede estar ocupando espacio sin generar ingresos.",
        "./?view=cars&opt=all"
      );
      continue;
    }

    $days = floor((time()-strtotime($r["last_move"])) / 86400);
    if($days>=7){
      $level = ($days>=15 ? "ALTO" : ($days>=10 ? "MEDIO" : "LEVE"));
      cd_add_decision(
        $decisions,
        "car",
        $level,
        "Promoción recomendada",
        "El vehículo <b>".htmlspecialchars($r["name"]." (".$r["plate"].")")."</b> lleva <b>$days días</b> sin renta. Recomendación: aplicar descuento del 10%, destacarlo en redes o revisar la tarifa.",
        "Puede aumentar ocupación inmediata.",
        "./?view=cars&opt=all"
      );
    }
  }
}

/* ======================================================
   2️⃣ Deudas activas
====================================================== */
$sql_deuda="
SELECT 
  COUNT(*) c,
  IFNULL(SUM(deuda),0) total_deuda
FROM (
  SELECT 
    b.id,
    (IFNULL(b.total,0)-IFNULL(p.pagado,0)) deuda
  FROM booking b
  LEFT JOIN (
    SELECT booking_id,SUM(val) pagado
    FROM payment
    WHERE stock_id=$stock_id
    GROUP BY booking_id
  ) p ON p.booking_id=b.id
  WHERE b.stock_id=$stock_id
    AND b.status IN (0,1)
    AND (IFNULL(b.total,0)-IFNULL(p.pagado,0))>0
) x
";

$q_deuda=$con->query($sql_deuda);
$deudas = 0;
$total_deuda = 0;
if($q_deuda){
  $row_deuda = $q_deuda->fetch_assoc();
  $deudas = intval($row_deuda["c"]);
  $total_deuda = floatval($row_deuda["total_deuda"]);
}

if($deudas>=1){
  $level = ($total_deuda>=15000 ? "ALTO" : ($total_deuda>=5000 ? "MEDIO" : "LEVE"));
  cd_add_decision(
    $decisions,
    "cash",
    $level,
    "Acción de Cobranza",
    "Hay <b>$deudas reserva(s)</b> con pagos pendientes por un total aproximado de <b>".cd_money($total_deuda)."</b>. Recomendación: enviar recordatorio automático hoy.",
    "Mejora flujo de caja y reduce riesgo de pérdida.",
    "./?view=booking&opt=all"
  );
}

/* ======================================================
   3️⃣ Ingresos bajos hoy
====================================================== */
$sql_today="
SELECT IFNULL(SUM(val),0) t 
FROM payment 
WHERE stock_id=$stock_id 
AND DATE(created_at)='$hoy'
";
$q_today=$con->query($sql_today);
$today_income = $q_today ? floatval($q_today->fetch_assoc()["t"]) : 0;

if($today_income<3000){
  cd_add_decision(
    $decisions,
    "income",
    "MEDIO",
    "Impulsar Ventas Hoy",
    "Los ingresos de hoy están bajos: <b>".cd_money($today_income)."</b>. Recomendación: publicar oferta flash, contactar clientes frecuentes o revisar cobros pendientes.",
    "Puede aumentar ingresos diarios.",
    "./?view=finance&opt=all"
  );
}

/* ======================================================
   4️⃣ Predicción ingresos del mes
====================================================== */
$sql_month_income = "
SELECT IFNULL(SUM(val),0) total
FROM payment
WHERE stock_id=$stock_id
AND DATE_FORMAT(created_at,'%Y-%m') = '$mes_actual'
";

$q_month_income = $con->query($sql_month_income);
$month_income = $q_month_income ? floatval($q_month_income->fetch_assoc()["total"]) : 0;

$daily_avg = ($dia_actual > 0 ? ($month_income / $dia_actual) : 0);
$projection = $daily_avg * $dias_mes;

if($projection > 0){
  cd_add_decision(
    $decisions,
    "income",
    "OK",
    "Proyección mensual automática",
    "Si el ritmo actual continúa, el negocio puede cerrar este mes en aproximadamente <b>".cd_money($projection)."</b>.",
    "Promedio diario actual: ".cd_money($daily_avg)." · Ingresos acumulados: ".cd_money($month_income),
    "./?view=finance&opt=all"
  );
}

/* ======================================================
   5️⃣ Vehículos NO rentables
====================================================== */
$sql_bad_cars = "
SELECT
  c.id,
  c.name,
  c.plate,
  c.token,
  IFNULL(pay.total_income,0) income,
  IFNULL(mt.total_maint,0) maint
FROM cars c
LEFT JOIN (
  SELECT
    b.car_id,
    SUM(p.val) total_income
  FROM booking b
  INNER JOIN payment p ON p.booking_id=b.id
  WHERE b.stock_id=$stock_id
  GROUP BY b.car_id
) pay ON pay.car_id=c.id
LEFT JOIN (
  SELECT
    car_id,
    SUM(purchase_price) total_maint
  FROM maintenance
  WHERE stock_id=$stock_id
  GROUP BY car_id
) mt ON mt.car_id=c.id
WHERE c.stock_id=$stock_id
ORDER BY (IFNULL(mt.total_maint,0)-IFNULL(pay.total_income,0)) DESC
LIMIT 10
";

$q_bad_cars = $con->query($sql_bad_cars);
if($q_bad_cars){
  while($r = $q_bad_cars->fetch_assoc()){
    $income = floatval($r["income"]);
    $maint  = floatval($r["maint"]);

    if($maint > $income && $maint > 0){
      $loss = $maint - $income;
      $level = ($loss>=10000 ? "ALTO" : ($loss>=3000 ? "MEDIO" : "LEVE"));

      cd_add_decision(
        $decisions,
        "risk",
        $level,
        "Vehículo no rentable",
        "El vehículo <b>".htmlspecialchars($r["name"]." (".$r["plate"].")")."</b> ha consumido más mantenimiento del que ha producido. Diferencia negativa: <b>".cd_money($loss)."</b>.",
        "Recomendación: revisar tarifa, pausar uso, revisar mantenimiento o evaluar venta.",
        "./?view=finance&opt=vehicle"
      );
    }
  }
}

/* ======================================================
   6️⃣ Score de salud por vehículo
====================================================== */
$sql_health = "
SELECT
  c.id,
  c.name,
  c.plate,
  c.token,
  IFNULL(pay.total_income,0) income,
  IFNULL(mt.total_maint,0) maint,
  IFNULL(book.total_bookings,0) bookings
FROM cars c
LEFT JOIN (
  SELECT
    b.car_id,
    SUM(p.val) total_income
  FROM booking b
  INNER JOIN payment p ON p.booking_id=b.id
  WHERE b.stock_id=$stock_id
  GROUP BY b.car_id
) pay ON pay.car_id=c.id
LEFT JOIN (
  SELECT
    car_id,
    SUM(purchase_price) total_maint
  FROM maintenance
  WHERE stock_id=$stock_id
  GROUP BY car_id
) mt ON mt.car_id=c.id
LEFT JOIN (
  SELECT
    car_id,
    COUNT(*) total_bookings
  FROM booking
  WHERE stock_id=$stock_id
  GROUP BY car_id
) book ON book.car_id=c.id
WHERE c.stock_id=$stock_id
ORDER BY income DESC
LIMIT 8
";

$q_health = $con->query($sql_health);
if($q_health){
  while($r = $q_health->fetch_assoc()){
    $income   = floatval($r["income"]);
    $maint    = floatval($r["maint"]);
    $bookings = intval($r["bookings"]);

    $score_car = 50;

    if($income > 0){
      $profit_ratio = (($income - $maint) / $income) * 100;
      $score_car += ($profit_ratio * 0.30);
    }

    $score_car += min(20, ($bookings * 2));

    if($income <= 0 && $maint > 0){
      $score_car -= 30;
    }

    if($income > 0 && $maint > ($income * 0.50)){
      $score_car -= 25;
    }

    $score_car = round(max(0,min(100,$score_car)));

    if($score_car >= 80){
      $health_status = "Excelente";
      $level = "OK";
    }elseif($score_car >= 60){
      $health_status = "Estable";
      $level = "LEVE";
    }else{
      $health_status = "Riesgo";
      $level = "MEDIO";
    }

    cd_add_decision(
      $decisions,
      "health",
      $level,
      "Salud del vehículo: ".htmlspecialchars($r["name"]." (".$r["plate"].")"),
      "Nivel de salud calculado automáticamente: <b>$score_car%</b> — <b>$health_status</b>.",
      "Ingresos: ".cd_money($income)." · Mantenimiento: ".cd_money($maint)." · Reservas: ".$bookings,
      "./?view=cars&opt=all"
    );
  }
}

/* ======================================================
   7️⃣ Vehículo estrella / posible aumento de tarifa
====================================================== */
$sql_star = "
SELECT
  c.id,
  c.name,
  c.plate,
  c.token,
  IFNULL(SUM(p.val),0) income,
  COUNT(DISTINCT b.id) reservas
FROM cars c
INNER JOIN booking b ON b.car_id=c.id AND b.stock_id=$stock_id
INNER JOIN payment p ON p.booking_id=b.id AND p.stock_id=$stock_id
WHERE c.stock_id=$stock_id
AND DATE_FORMAT(p.created_at,'%Y-%m')='$mes_actual'
GROUP BY c.id
ORDER BY income DESC
LIMIT 1
";
$q_star = $con->query($sql_star);
if($q_star && $q_star->num_rows>0){
  $star = $q_star->fetch_assoc();
  if(floatval($star["income"]) > 0){
    cd_add_decision(
      $decisions,
      "price",
      "OK",
      "Vehículo estrella del mes",
      "El vehículo <b>".htmlspecialchars($star["name"]." (".$star["plate"].")")."</b> es el que más produce este mes con <b>".cd_money($star["income"])."</b>.",
      "Recomendación: priorizar disponibilidad, buen mantenimiento y evaluar aumento leve de tarifa.",
      "./?view=cars&opt=all"
    );
  }
}

// Ordenar: primero alertas más fuertes
usort($decisions, function($a,$b){
  $order = ["ALTO"=>1,"MEDIO"=>2,"LEVE"=>3,"OK"=>4];
  return ($order[$a["level"]] ?? 9) - ($order[$b["level"]] ?? 9);
});

$cnt_alto=0; $cnt_medio=0; $cnt_leve=0; $cnt_ok=0;
foreach($decisions as $d){
  if($d["level"]=="ALTO") $cnt_alto++;
  elseif($d["level"]=="MEDIO") $cnt_medio++;
  elseif($d["level"]=="LEVE") $cnt_leve++;
  else $cnt_ok++;
}
?>

<style>
html,body{ overflow-x:hidden!important; }
.content-wrapper,.content,.container-fluid{ overflow-x:hidden!important; }

.cd-card-main{
  background:#16181d;
  border-radius:20px;
  border:1px solid rgba(255,255,255,.08);
  box-shadow:0 10px 28px rgba(0,0,0,.35);
  overflow:hidden;
}
.cd-header{
  padding:18px;
  border-bottom:1px solid rgba(255,255,255,.08);
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  flex-wrap:wrap;
}
.cd-title-wrap h3{
  font-weight:900;
  color:#fff;
  margin:0;
}
.cd-title-wrap span{
  color:#9aa0a6;
  font-weight:800;
}
.cd-kpis{
  display:grid;
  grid-template-columns:repeat(4,minmax(0,1fr));
  gap:12px;
  padding:18px;
  padding-bottom:0;
}
.cd-kpi{
  background:#0f1115;
  border:1px solid rgba(255,255,255,.08);
  border-radius:16px;
  padding:14px;
  min-width:0;
}
.cd-kpi .label{
  color:#bdbdbd;
  font-size:12px;
  font-weight:900;
  text-transform:uppercase;
  letter-spacing:.4px;
}
.cd-kpi .value{
  color:#fff;
  font-size:24px;
  font-weight:900;
  margin-top:4px;
}
.cd-body{
  padding:18px;
}
.cd-decision{
  background:#0f1115;
  border-radius:18px;
  padding:18px;
  margin-bottom:14px;
  border:1px solid rgba(255,255,255,.08);
  box-shadow:0 10px 25px rgba(0,0,0,.25);
  position:relative;
  overflow:hidden;
  max-width:100%;
  word-break:break-word;
}
.cd-decision:before{
  content:"";
  position:absolute;
  right:-24px;
  top:-24px;
  width:100px;
  height:100px;
  border-radius:50%;
  background:rgba(255,255,255,.035);
}
.cd-row{
  display:flex;
  align-items:flex-start;
  gap:14px;
  position:relative;
  z-index:2;
}
.cd-icon{
  width:55px;
  height:55px;
  border-radius:16px;
  background:#1f2937;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:24px;
  flex-shrink:0;
}
.cd-content{ flex:1; min-width:0; }
.cd-content h5{
  color:#fff;
  font-weight:900;
  margin:0;
  font-size:18px;
}
.cd-msg{
  color:#d1d5db;
  font-weight:700;
  margin-top:10px;
  margin-bottom:10px;
  line-height:1.5;
}
.cd-impact{
  background:rgba(46,204,113,.08);
  border:1px solid rgba(46,204,113,.15);
  padding:10px 12px;
  border-radius:12px;
  color:#2ecc71;
  font-weight:900;
  font-size:13px;
}
.cd-action{
  margin-top:10px;
}
.cd-empty{
  color:#9aa0a6;
  font-weight:800;
  background:#0f1115;
  padding:16px;
  border-radius:16px;
  border:1px solid rgba(255,255,255,.08);
}
@media(max-width:991px){
  .cd-kpis{ grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media(max-width:575px){
  .cd-kpis{ grid-template-columns:1fr; }
  .cd-row{ flex-direction:column; }
}
</style>

<div class="cd-card-main">

  <div class="cd-header">
    <div class="cd-title-wrap">
      <h3>🧠 Centro de Decisiones</h3>
      <span>Recomendaciones estratégicas automáticas</span>
    </div>

    <span class="badge badge-dark" style="font-size:13px;font-weight:900;">
      <?php echo count($decisions); ?> recomendación(es)
    </span>
  </div>

  <div class="cd-kpis">
    <div class="cd-kpi">
      <div class="label">Críticas</div>
      <div class="value" style="color:#e74c3c;"><?php echo $cnt_alto; ?></div>
    </div>
    <div class="cd-kpi">
      <div class="label">Medias</div>
      <div class="value" style="color:#f1c40f;"><?php echo $cnt_medio; ?></div>
    </div>
    <div class="cd-kpi">
      <div class="label">Leves</div>
      <div class="value" style="color:#17a2b8;"><?php echo $cnt_leve; ?></div>
    </div>
    <div class="cd-kpi">
      <div class="label">Oportunidades</div>
      <div class="value" style="color:#2ecc71;"><?php echo $cnt_ok; ?></div>
    </div>
  </div>

  <div class="cd-body">

    <?php if(count($decisions)==0): ?>
      <div class="cd-empty">
        No hay decisiones críticas hoy. El negocio está estable.
      </div>
    <?php else: ?>
      <?php foreach($decisions as $d): ?>

        <div class="cd-decision" style="border-left:5px solid <?php echo cd_level_color($d["level"]); ?>;">
          <div class="cd-row">
            <div class="cd-icon" style="color:<?php echo cd_level_color($d["level"]); ?>;">
              <?php echo cd_icon($d["type"]); ?>
            </div>

            <div class="cd-content">
              <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                <h5><?php echo $d["title"]; ?></h5>
                <span class="<?php echo cd_level_badge($d["level"]); ?>" style="font-weight:900;">
                  <?php echo $d["level"]; ?>
                </span>
              </div>

              <p class="cd-msg"><?php echo $d["msg"]; ?></p>

              <div class="cd-impact">
                ⚡ <?php echo $d["impact"]; ?>
              </div>

              <?php if(!empty($d["link"])): ?>
                <div class="cd-action">
                  <a href="<?php echo $d["link"]; ?>" class="btn btn-sm btn-warning" style="font-weight:900;">
                    Ver detalle <i class="fa fa-arrow-right"></i>
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

      <?php endforeach; ?>
    <?php endif; ?>

  </div>
</div>

</div>
</div>

</div>
</section>
