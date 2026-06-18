
<?php if(isset($_SESSION['user_id'])): ?>
<?php
/* ==========================================================
   ✅ TIMELINE / REGISTRO UNIFICADO (SIN TABLA EXTRA)
   - Une: booking, payment, spends, maintenance, fuels, toll, person, cars
   - Filtros: HOY / 7 DÍAS / 30 DÍAS
   - Diseño AdminLTE oscuro + “se siente vivo”
   - Ajustado a tu BD: payment (tabla) y val (monto)
   ========================================================== */

$stock_id = intval(StockData::getPrincipal()->id);

// =====================
// ✅ Filtro de rango
// =====================
$range = isset($_GET["range"]) ? $_GET["range"] : "today";

$end   = date("Y-m-d 23:59:59");
if($range=="7"){
  $start = date("Y-m-d 00:00:00", strtotime("-7 days"));
  $range_label = "Últimos 7 días";
}elseif($range=="30"){
  $start = date("Y-m-d 00:00:00", strtotime("-30 days"));
  $range_label = "Últimos 30 días";
}else{
  $start = date("Y-m-d 00:00:00");
  $range_label = "Hoy";
}

// =====================
// ✅ Conexión
// =====================
$db  = new Database();
$con = $db->connect();

// =====================
// ✅ Query unificado (UNION ALL)
// =====================
$sql = "
(
  SELECT 
    'booking' AS src,
    'Reserva' AS tipo,
    b.id AS ref_id,
    b.user_id AS user_id,
    b.stock_id AS stock_id,
    CONCAT('Reserva #', b.id, ' • Total: ', b.total) AS descripcion,
    b.created_at AS fecha,
    b.total AS monto
  FROM booking b
  WHERE b.stock_id = $stock_id
    AND b.created_at BETWEEN '$start' AND '$end'
)
UNION ALL
(
  SELECT 
    'payment' AS src,
    'Pago' AS tipo,
    p.id AS ref_id,
    p.user_id AS user_id,
    p.stock_id AS stock_id,
    CONCAT('Pago recibido • Monto: ', p.val) AS descripcion,
    p.created_at AS fecha,
    p.val AS monto
  FROM payment p
  WHERE p.stock_id = $stock_id
    AND p.created_at BETWEEN '$start' AND '$end'
)
UNION ALL
(
  SELECT 
    'spends' AS src,
    'Gasto' AS tipo,
    s.id AS ref_id,
    s.user_id AS user_id,
    s.stock_id AS stock_id,
    CONCAT('Gasto: ', s.name, ' • Monto: ', s.price) AS descripcion,
    s.created_at AS fecha,
    s.price AS monto
  FROM spend s
  WHERE s.stock_id = $stock_id
    AND s.created_at BETWEEN '$start' AND '$end'
)
UNION ALL
(
  SELECT 
    'maintenance' AS src,
    'Mantenimiento' AS tipo,
    m.id AS ref_id,
    m.user_id AS user_id,
    m.stock_id AS stock_id,
    CONCAT('Mantenimiento • Vehículo #', m.car_id, ' • Monto: ', m.total) AS descripcion,
    m.created_at AS fecha,
    m.total AS monto
  FROM maintenance m
  WHERE m.stock_id = $stock_id
    AND m.created_at BETWEEN '$start' AND '$end'
)
UNION ALL
(
  SELECT 
    'fuels' AS src,
    'Combustible' AS tipo,
    f.id AS ref_id,
    f.user_id AS user_id,
    f.stock_id AS stock_id,
    CONCAT('Combustible • Vehículo #', f.car_id, ' • Monto: ', f.total) AS descripcion,
    f.created_at AS fecha,
    f.total AS monto
  FROM fuels f
  WHERE f.stock_id = $stock_id
    AND f.created_at BETWEEN '$start' AND '$end'
)
UNION ALL
(
  SELECT 
    'toll' AS src,
    'Peaje' AS tipo,
    t.id AS ref_id,
    t.user_id AS user_id,
    t.stock_id AS stock_id,
    CONCAT('Peaje • Vehículo #', t.car_id, ' • Monto: ', t.total) AS descripcion,
    t.created_at AS fecha,
    t.total AS monto
  FROM toll t
  WHERE t.stock_id = $stock_id
    AND t.created_at BETWEEN '$start' AND '$end'
)
UNION ALL
(
  SELECT 
    'person' AS src,
    'Cliente' AS tipo,
    pe.id AS ref_id,
    pe.user_id AS user_id,
    pe.stock_id AS stock_id,
    CONCAT('Cliente nuevo: ', pe.name) AS descripcion,
    pe.created_at AS fecha,
    0 AS monto
  FROM person pe
  WHERE pe.stock_id = $stock_id
    AND pe.created_at BETWEEN '$start' AND '$end'
)
UNION ALL
(
  SELECT 
    'cars' AS src,
    'Vehículo' AS tipo,
    c.id AS ref_id,
    c.user_id AS user_id,
    c.stock_id AS stock_id,
    CONCAT('Vehículo agregado: ', c.name, ' • Placa: ', c.plate) AS descripcion,
    c.created_at AS fecha,
    0 AS monto
  FROM cars c
  WHERE c.stock_id = $stock_id
    AND c.created_at BETWEEN '$start' AND '$end'
)
ORDER BY fecha DESC
LIMIT 300
";

$q = $con->query($sql);

$activity = [];
$sql_error = "";
if($q){
  while($row = $q->fetch_assoc()){
    $activity[] = $row;
  }
}else{
  $sql_error = $con->error;
}

// =====================
// ✅ KPIs (se siente vivo)
// =====================
$counts = [
  "Reserva"=>0, "Pago"=>0, "Gasto"=>0, "Mantenimiento"=>0,
  "Combustible"=>0, "Peaje"=>0, "Cliente"=>0, "Vehículo"=>0
];
foreach($activity as $a){
  if(isset($counts[$a["tipo"]])) $counts[$a["tipo"]]++;
}

// =====================
// ✅ Meta por tipo (icono, badge, link)
// Ajusta links si tus vistas cambian.
// =====================
function act_meta($src){
  switch($src){
    case "booking":     return ["fa fa-calendar-check","badge-success","./?view=booking&opt=one&id="];
    case "payment":     return ["fa fa-cash-register","badge-primary","./?view=finance&opt=all&id="]; // AJUSTA si tienes view=payment
    case "spends":      return ["fa fa-receipt","badge-danger","./?view=finance&opt=all&spends=Negocio&id="];
    case "maintenance": return ["fa fa-tools","badge-warning","./?view=finance&opt=vehicle&id="];
    case "fuels":       return ["fa fa-gas-pump","badge-info","./?view=finance&opt=vehicle&id="];
    case "toll":        return ["fa fa-road","badge-secondary","./?view=finance&opt=vehicle&id="];
    case "person":      return ["fa fa-user-plus","badge-light","./?view=person&opt=edit&id="];
    case "cars":        return ["fa fa-car","badge-success","./?view=cars&opt=edit&id="];
    default:            return ["fa fa-bolt","badge-dark","#"];
  }
}
?>

<section class="content">
<br>
<style>
.rt-card{
  background:#16181d !important;
  border:1px solid rgba(255,255,255,.07) !important;
  border-radius:18px !important;
  box-shadow:0 10px 28px rgba(0,0,0,.35) !important;
  overflow:hidden;
}
.rt-card .card-header{
  border-bottom:1px solid rgba(255,255,255,.08) !important;
  background: linear-gradient(90deg, rgba(106,54,255,.20), rgba(0,0,0,0)) !important;
}
.rt-title{ color:#fff !important; font-weight:900 !important; margin:0; }
.rt-muted{ color:#bdbdbd !important; font-weight:800; }
.rt-pill{
  display:inline-flex; align-items:center; gap:8px;
  padding:6px 10px; border-radius:999px;
  border:1px solid rgba(255,255,255,.10);
  background:#0f1115; color:#eaeaea;
  font-weight:900; font-size:12px; white-space:nowrap;
}
.rt-dot{
  width:10px;height:10px;border-radius:50%;
  background:#2ecc71;
  box-shadow:0 0 0 4px rgba(46,204,113,.12);
  display:inline-block;
}
.rt-btn{
  border-radius:12px !important;
  font-weight:900 !important;
  border:1px solid rgba(255,255,255,.10) !important;
  background:#0f1115 !important;
  color:#fff !important;
  padding:9px 12px !important;
}
.rt-btn:hover{ opacity:.92; }

.rt-kpis{
  display:grid;
  grid-template-columns: repeat(4, minmax(0,1fr));
  gap:10px;
}
@media(max-width: 992px){
  .rt-kpis{ grid-template-columns: repeat(2, minmax(0,1fr)); }
}
.rt-kpi{
  background:#0f1115;
  border:1px solid rgba(255,255,255,.08);
  border-radius:16px;
  padding:12px;
}
.rt-kpi .n{ color:#fff; font-weight:900; font-size:18px; line-height:1.1; }
.rt-kpi .l{
  color:#bdbdbd; font-weight:900; font-size:12px;
  margin-top:6px; text-transform:uppercase; letter-spacing:.4px;
}

.rt-item{
  display:flex;
  gap:12px;
  padding:12px 0;
  border-bottom:1px solid rgba(255,255,255,.06);
}
.rt-icon{
  width:42px;height:42px;border-radius:14px;
  display:flex;align-items:center;justify-content:center;
  background:#0f1115;
  border:1px solid rgba(255,255,255,.10);
  color:#fff;
  flex:0 0 42px;
}
.rt-body{ flex:1; }
.rt-top{
  display:flex;justify-content:space-between;gap:10px;
  flex-wrap:wrap;align-items:center;
}
.rt-desc{ color:#fff; font-weight:900; }
.rt-date{ color:#bdbdbd; font-weight:800; font-size:12px; white-space:nowrap; }
.rt-chip{
  display:inline-block;
  padding:5px 10px;
  border-radius:999px;
  font-size:12px;
  font-weight:900;
  border:1px solid rgba(255,255,255,.10);
  background:#0f1115;
  color:#fff;
}
.rt-new{
  background: rgba(46,204,113,.05);
  box-shadow: inset 3px 0 0 rgba(46,204,113,.9);
}
</style>

<div class="row">
  <div class="col-md-12">

    <div class="card rt-card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-12 col-md-6">
            <h3 class="rt-title"><i class="fa fa-bolt"></i> Actividad del Sistema</h3>
            <div class="rt-muted">Registro unificado • <?php echo $range_label; ?></div>
          </div>

          <div class="col-12 col-md-6 text-right mt-3 mt-md-0">
            <span class="rt-pill"><span class="rt-dot"></span> Sistema activo</span>
            <span class="rt-pill ml-2"><i class="fa fa-clock"></i> <span id="reloj">--:--:--</span></span>

            <span class="ml-2">
              <a class="btn rt-btn btn-sm" href="./?view=activity&range=today">Hoy</a>
              <a class="btn rt-btn btn-sm" href="./?view=activity&range=7">7 días</a>
              <a class="btn rt-btn btn-sm" href="./?view=activity&range=30">30 días</a>
              <button type="button" class="btn rt-btn btn-sm" id="btnRefresh"><i class="fa fa-sync"></i></button>
            </span>
          </div>
        </div>
      </div>

      <div class="card-body">

        <?php if($sql_error!=""): ?>
          <div class="alert alert-danger">
            <b>Error SQL:</b> <?php echo htmlspecialchars($sql_error); ?>
          </div>
        <?php endif; ?>

        <div class="rt-kpis mb-3">
          <div class="rt-kpi"><div class="n"><?php echo intval($counts["Reserva"]); ?></div><div class="l">Reservas</div></div>
          <div class="rt-kpi"><div class="n"><?php echo intval($counts["Pago"]); ?></div><div class="l">Pagos</div></div>
          <div class="rt-kpi"><div class="n"><?php echo intval($counts["Gasto"]); ?></div><div class="l">Gastos</div></div>
          <div class="rt-kpi"><div class="n"><?php echo intval($counts["Cliente"]); ?></div><div class="l">Clientes nuevos</div></div>
        </div>

        <?php if(count($activity)>0): ?>

          <?php
          $ix=0;
          foreach($activity as $a):
            $ix++;
            $meta = act_meta($a["src"]);
            $icon = $meta[0];
            $badge = $meta[1];
            $base_link = $meta[2];
            $isNew = ($ix<=8) ? "rt-new" : "";

            // Nombre usuario (si existe)
            $uName = "Sistema";
            if(!empty($a["user_id"])){
              try{
                $u = UserData::getById($a["user_id"]);
                if($u){ $uName = $u->name." ".$u->lastname; }
              }catch(Exception $e){}
            }

            $link = $base_link . intval($a["ref_id"]);
          ?>
            <div class="rt-item <?php echo $isNew; ?>">
              <div class="rt-icon">
                <i class="<?php echo $icon; ?>"></i>
              </div>

              <div class="rt-body">
                <div class="rt-top">
                  <div class="rt-desc">
                    <span class="badge <?php echo $badge; ?>" style="border-radius:999px; font-weight:900; padding:6px 10px;">
                      <?php echo htmlspecialchars($a["tipo"]); ?>
                    </span>

                    <span style="margin-left:8px;">
                      <?php echo htmlspecialchars($a["descripcion"]); ?>
                    </span>
                  </div>

                  <div class="rt-date">
                    <i class="fa fa-user"></i> <?php echo htmlspecialchars($uName); ?>
                    &nbsp;•&nbsp;
                    <?php echo date("d-m-Y h:i:s a", strtotime($a["fecha"])); ?>
                  </div>
                </div>

                <div style="margin-top:8px; display:flex; gap:10px; flex-wrap:wrap;">
                  <span class="rt-chip"><i class="fa fa-hashtag"></i> ID <?php echo intval($a["ref_id"]); ?></span>

                  <?php if(floatval($a["monto"])>0): ?>
                    <span class="rt-chip"><i class="fa fa-money-bill"></i> <?php echo Core::$symbol." ".number_format($a["monto"],2,".",","); ?></span>
                  <?php endif; ?>

                  <a class="btn rt-btn btn-sm" href="<?php echo $link; ?>">
                    <i class="fa fa-eye"></i> Ver detalle
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>

        <?php else: ?>

          <div class="card rt-card" style="box-shadow:none !important;">
            <div class="card-body">
              <h4 style="color:#fff; font-weight:900; margin:0;">No hay actividad en este rango</h4>
              <p class="rt-muted" style="margin-top:8px;">Prueba con 7 días o 30 días.</p>
            </div>
          </div>

        <?php endif; ?>

      </div>
    </div>

  </div>
</div>

<script>
function actualizarReloj(){
  const ahora = new Date();
  const h = String(ahora.getHours()).padStart(2,'0');
  const m = String(ahora.getMinutes()).padStart(2,'0');
  const s = String(ahora.getSeconds()).padStart(2,'0');
  const el = document.getElementById("reloj");
  if(el) el.textContent = `${h}:${m}:${s}`;
}
setInterval(actualizarReloj, 1000);
actualizarReloj();

document.getElementById("btnRefresh")?.addEventListener("click", function(){
  window.location.reload();
});

// ✅ Auto-refresh opcional cada 30s (si quieres que se sienta “en vivo”)
// setInterval(()=>window.location.reload(), 30000);
</script>

</section>
<?php endif; ?>