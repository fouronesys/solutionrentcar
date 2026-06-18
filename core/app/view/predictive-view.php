<?php
// =======================================================
// 🛠️ PASO 4: MANTENIMIENTO PREDICTIVO
// - Usa cars.charge_kms, cars.kms, cars.kms_current
// - Usa maintenance.car_id, maintenance.purchase_price, maintenance.created_at
// - Detecta mantenimiento vencido, próximo, alto gasto y riesgo operativo
// =======================================================

$db  = new Database();
$con = $db->connect();
$stock_id = intval(StockData::getPrincipal()->id);
$today = date("Y-m-d");

function mp_money($amount){
  return Core::$symbol." ".number_format(floatval($amount),2,".",",");
}

function mp_badge($level){
  if($level=="ALTO") return "badge badge-danger";
  if($level=="MEDIO") return "badge badge-warning";
  if($level=="LEVE") return "badge badge-info";
  return "badge badge-success";
}

function mp_color($level){
  if($level=="ALTO") return "#e74c3c";
  if($level=="MEDIO") return "#f1c40f";
  if($level=="LEVE") return "#17a2b8";
  return "#2ecc71";
}

$predictive = [];
$cnt_alto = 0;
$cnt_medio = 0;
$cnt_leve = 0;
$cnt_ok = 0;

$sql = "
SELECT
  c.id,
  c.name,
  c.plate,
  c.token,
  c.year,
  c.charge_kms,
  c.kms,
  c.kms_current,
  IFNULL(mt.total_maint,0) AS total_maint,
  IFNULL(mt.maint_count,0) AS maint_count,
  mt.last_maint
FROM cars c
LEFT JOIN (
  SELECT
    car_id,
    SUM(purchase_price) total_maint,
    COUNT(*) maint_count,
    MAX(created_at) last_maint
  FROM maintenance
  WHERE stock_id=$stock_id
  GROUP BY car_id
) mt ON mt.car_id=c.id
WHERE c.stock_id=$stock_id
ORDER BY c.name ASC
";

$q = $con->query($sql);

if($q){
  while($r = $q->fetch_assoc()){

    $car_id = intval($r["id"]);
    $name = trim($r["name"]." ".(!empty($r["plate"]) ? "(".$r["plate"].")" : ""));
    $charge_kms = intval($r["charge_kms"]);
    $kms_base = intval($r["kms"]);
    $kms_current = intval($r["kms_current"]);
    $total_maint = floatval($r["total_maint"]);
    $maint_count = intval($r["maint_count"]);
    $last_maint = $r["last_maint"];

    if($charge_kms <= 0){
      $charge_kms = 5000;
    }

    $used_kms = max(0, $kms_current - $kms_base);
    $remaining_kms = $charge_kms - $used_kms;
    $progress = ($charge_kms > 0 ? ($used_kms / $charge_kms) * 100 : 0);
    $progress = max(0, min(150, $progress));

    $days_last = null;
    if(!empty($last_maint)){
      $days_last = floor((time() - strtotime($last_maint)) / 86400);
    }

    $level = "OK";
    $status = "Al día";
    $recommendation = "Mantener monitoreo normal.";

    if($remaining_kms <= 0){
      $level = "ALTO";
      $status = "Mantenimiento vencido";
      $recommendation = "Enviar el vehículo a mantenimiento antes de seguir rentándolo.";
    }elseif($remaining_kms <= 500){
      $level = "MEDIO";
      $status = "Próximo a mantenimiento";
      $recommendation = "Programar mantenimiento preventivo pronto.";
    }elseif($remaining_kms <= 1000){
      $level = "LEVE";
      $status = "En observación";
      $recommendation = "Preparar revisión preventiva.";
    }

    if($total_maint >= 20000){
      if($level=="OK" || $level=="LEVE"){
        $level = "MEDIO";
        $status = "Alto gasto acumulado";
      }
      $recommendation = "Revisar si el vehículo está consumiendo demasiado mantenimiento.";
    }

    if($maint_count >= 5){
      if($level!="ALTO"){
        $level = "MEDIO";
        $status = "Mantenimiento frecuente";
      }
      $recommendation = "Evaluar historial mecánico y rentabilidad del vehículo.";
    }

    if(empty($last_maint) && $kms_current > 0){
      if($level!="ALTO"){
        $level = "MEDIO";
        $status = "Sin historial de mantenimiento";
      }
      $recommendation = "Registrar mantenimiento inicial o revisión general.";
    }

    if($level=="ALTO") $cnt_alto++;
    elseif($level=="MEDIO") $cnt_medio++;
    elseif($level=="LEVE") $cnt_leve++;
    else $cnt_ok++;

    $predictive[] = [
      "id" => $car_id,
      "name" => $name,
      "token" => $r["token"],
      "year" => $r["year"],
      "charge_kms" => $charge_kms,
      "kms_base" => $kms_base,
      "kms_current" => $kms_current,
      "used_kms" => $used_kms,
      "remaining_kms" => $remaining_kms,
      "progress" => $progress,
      "total_maint" => $total_maint,
      "maint_count" => $maint_count,
      "last_maint" => $last_maint,
      "days_last" => $days_last,
      "level" => $level,
      "status" => $status,
      "recommendation" => $recommendation
    ];
  }
}
?>

<style>
html,body{ overflow-x:hidden!important; }
.content-wrapper,.content,.container-fluid{ overflow-x:hidden!important; }

.mp-main{
  background:#16181d;
  border-radius:22px;
  border:1px solid rgba(255,255,255,.08);
  box-shadow:0 10px 28px rgba(0,0,0,.35);
  overflow:hidden;
  margin-bottom:20px;
}
.mp-header{
  padding:18px;
  border-bottom:1px solid rgba(255,255,255,.08);
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:12px;
  flex-wrap:wrap;
}
.mp-header h3{
  color:#fff;
  font-weight:900;
  margin:0;
}
.mp-header span{
  color:#9aa0a6;
  font-weight:800;
}
.mp-kpis{
  display:grid;
  grid-template-columns:repeat(4,minmax(0,1fr));
  gap:12px;
  padding:18px;
  padding-bottom:0;
}
.mp-kpi{
  background:#0f1115;
  border:1px solid rgba(255,255,255,.08);
  border-radius:16px;
  padding:14px;
  min-width:0;
}
.mp-kpi .label{
  color:#bdbdbd;
  font-size:12px;
  font-weight:900;
  text-transform:uppercase;
}
.mp-kpi .value{
  color:#fff;
  font-size:25px;
  font-weight:900;
  margin-top:4px;
}
.mp-body{
  padding:18px;
}
.mp-card{
  background:#0f1115;
  border:1px solid rgba(255,255,255,.08);
  border-radius:18px;
  padding:16px;
  margin-bottom:14px;
  box-shadow:0 10px 25px rgba(0,0,0,.25);
  max-width:100%;
  overflow:hidden;
  word-break:break-word;
}
.mp-top{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:12px;
  flex-wrap:wrap;
}
.mp-title{
  color:#fff;
  font-size:18px;
  font-weight:900;
  margin:0;
}
.mp-meta{
  color:#9aa0a6;
  font-size:13px;
  font-weight:800;
  margin-top:4px;
}
.mp-grid{
  display:grid;
  grid-template-columns:repeat(4,minmax(0,1fr));
  gap:10px;
  margin-top:14px;
}
.mp-mini{
  background:#0b0d10;
  border:1px solid rgba(255,255,255,.06);
  border-radius:14px;
  padding:12px;
  min-width:0;
}
.mp-mini .label{
  color:#9aa0a6;
  font-size:11px;
  font-weight:900;
  text-transform:uppercase;
}
.mp-mini .value{
  color:#fff;
  font-size:16px;
  font-weight:900;
  margin-top:4px;
}
.mp-bar{
  height:14px;
  border-radius:999px;
  background:rgba(255,255,255,.08);
  margin-top:14px;
  overflow:hidden;
}
.mp-bar div{
  height:14px;
  border-radius:999px;
}
.mp-rec{
  background:rgba(46,204,113,.08);
  border:1px solid rgba(46,204,113,.15);
  padding:10px 12px;
  border-radius:12px;
  color:#2ecc71;
  font-weight:900;
  margin-top:12px;
}
@media(max-width:991px){
  .mp-kpis,.mp-grid{ grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media(max-width:575px){
  .mp-kpis,.mp-grid{ grid-template-columns:1fr; }
}
</style>

<section class="content">
<div class="container-fluid">
<br>

<div class="mp-main">

  <div class="mp-header">
    <div>
      <h3>🛠️ Mantenimiento Predictivo</h3>
      <span>Análisis automático por kilometraje, historial y gasto mecánico</span>
    </div>
    <span class="badge badge-dark" style="font-size:13px;font-weight:900;">
      <?php echo count($predictive); ?> vehículo(s) evaluado(s)
    </span>
  </div>

  <div class="mp-kpis">
    <div class="mp-kpi">
      <div class="label">Urgentes</div>
      <div class="value" style="color:#e74c3c;"><?php echo $cnt_alto; ?></div>
    </div>
    <div class="mp-kpi">
      <div class="label">Revisar pronto</div>
      <div class="value" style="color:#f1c40f;"><?php echo $cnt_medio; ?></div>
    </div>
    <div class="mp-kpi">
      <div class="label">Observación</div>
      <div class="value" style="color:#17a2b8;"><?php echo $cnt_leve; ?></div>
    </div>
    <div class="mp-kpi">
      <div class="label">Al día</div>
      <div class="value" style="color:#2ecc71;"><?php echo $cnt_ok; ?></div>
    </div>
  </div>

  <div class="mp-body">

    <?php if(count($predictive)==0): ?>
      <div class="mp-card">
        <div style="color:#9aa0a6;font-weight:800;">No hay vehículos para evaluar.</div>
      </div>
    <?php else: ?>

      <?php foreach($predictive as $v):
        $level = $v["level"];
        $bar_color = mp_color($level);
        $progress_width = min(100, $v["progress"]);
      ?>

      <div class="mp-card" style="border-left:5px solid <?php echo $bar_color; ?>;">

        <div class="mp-top">
          <div>
            <p class="mp-title">
              <?php echo htmlspecialchars($v["name"]); ?>
            </p>
            <div class="mp-meta">
              Ficha: <b><?php echo htmlspecialchars($v["token"]); ?></b>
              · Año: <b><?php echo htmlspecialchars($v["year"]); ?></b>
              · Último mant.: <b><?php echo $v["last_maint"] ? date("d-m-Y", strtotime($v["last_maint"])) : "Sin historial"; ?></b>
            </div>
          </div>

          <span class="<?php echo mp_badge($level); ?>" style="font-weight:900;">
            <?php echo $v["status"]; ?>
          </span>
        </div>

        <div class="mp-grid">
          <div class="mp-mini">
            <div class="label">KM base</div>
            <div class="value"><?php echo number_format($v["kms_base"]); ?></div>
          </div>
          <div class="mp-mini">
            <div class="label">KM actual</div>
            <div class="value"><?php echo number_format($v["kms_current"]); ?></div>
          </div>
          <div class="mp-mini">
            <div class="label">KM usados</div>
            <div class="value"><?php echo number_format($v["used_kms"]); ?></div>
          </div>
          <div class="mp-mini">
            <div class="label">Faltan</div>
            <div class="value" style="color:<?php echo ($v["remaining_kms"]<=0?'#e74c3c':'#fff'); ?>;">
              <?php echo number_format($v["remaining_kms"]); ?> km
            </div>
          </div>
        </div>

        <div class="mp-bar">
          <div style="width:<?php echo $progress_width; ?>%;background:<?php echo $bar_color; ?>;"></div>
        </div>

        <div class="mp-grid">
          <div class="mp-mini">
            <div class="label">Cada</div>
            <div class="value"><?php echo number_format($v["charge_kms"]); ?> km</div>
          </div>
          <div class="mp-mini">
            <div class="label">Mantenimientos</div>
            <div class="value"><?php echo intval($v["maint_count"]); ?></div>
          </div>
          <div class="mp-mini">
            <div class="label">Gasto acumulado</div>
            <div class="value"><?php echo mp_money($v["total_maint"]); ?></div>
          </div>
          <div class="mp-mini">
            <div class="label">Días último mant.</div>
            <div class="value"><?php echo $v["days_last"]!==null ? intval($v["days_last"])." días" : "N/A"; ?></div>
          </div>
        </div>

        <div class="mp-rec">
          ⚡ <?php echo $v["recommendation"]; ?>
        </div>

      </div>

      <?php endforeach; ?>

    <?php endif; ?>

  </div>
</div>

</div>
</section>
