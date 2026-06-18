<?php if(isset($_SESSION['user_id'])):

$principal = StockData::getPrincipal();

$default_chart = $principal->default_chart ?? '';
$iva_val       = $principal->imp_val ?? 0;

$selstock = null;
if(isset($_GET["stock"])) { 
  $selstock = $_GET["stock"]; 
} else { 
  $selstock = $principal->id ?? 0; 
}

date_default_timezone_set('America/Santo_Domingo');
$today = date('Y-m-d');

$sd = $today;
$ed = $today;

$ntot = 0;
$nsells = 0;
$totsells = 0;
$totmaintenance = 0;
$totfuel = 0;
$totoll = 0;
$totdeposit = 0;
$totspend = 0;
$totpayments = 0;

$operations  = BookingData::getGroupByDateOp($today, $today, $selstock);
$deposit     = BookingData::getGroupByDateDp($today, $today, $selstock);
$spends      = SpendData::getGroupByDateOp($today, $today, 1, $selstock);
$maintenance = MaintenanceData::getGroupByDateOp($today, $today, $selstock);
$fuel        = FuelsData::getGroupByDateOp($today, $today, $selstock);
$toll        = TollData::getGroupByDateOp($today, $today, $selstock);
$payments    = PaymentData::getGroupByDateOp($today, $today, $selstock);

if(!function_exists('safeFirstObject')){
  function safeFirstObject($arr){
    return (is_array($arr) && isset($arr[0]) && is_object($arr[0])) ? $arr[0] : null;
  }
}

$op0 = safeFirstObject($operations);
$py0 = safeFirstObject($payments);
$sp0 = safeFirstObject($spends);
$dp0 = safeFirstObject($deposit);
$tl0 = safeFirstObject($toll);
$fl0 = safeFirstObject($fuel);
$mt0 = safeFirstObject($maintenance);

$sl        = (float)($op0->t ?? 0);
$paymentsx = (float)($py0->t ?? 0);
$sp        = (float)($sp0->t ?? 0);
$dp        = (float)($dp0->t ?? 0);
$tl        = (float)($tl0->t ?? 0);
$fl        = (float)($fl0->t ?? 0);
$mt        = (float)($mt0->t ?? 0);

$ntot += (($sl + $dp) - ($mt + $sp + $tl + $fl));

$nsells += (int)($op0->c ?? 0);
$totsells += $sl;
$totspend += $sp;
$totpayments += $paymentsx;
$totmaintenance += $mt;
$totoll += $tl;
$totfuel += $fl;
$totdeposit += $dp;
?>

<section class="content">
  <div class="container-fluid">

    <!-- =========================
         ✅ ESTILO ENTERPRISE (SOLO DASHBOARD)
    ========================= -->
    <style>
      .rt-card{
        background:#16181d !important;
        border:1px solid rgba(255,255,255,.07) !important;
        border-radius:18px !important;
        box-shadow:0 10px 28px rgba(0,0,0,.35) !important;
      }
      .rt-card .card-header{
        border-bottom:1px solid rgba(255,255,255,.08) !important;
        background: linear-gradient(90deg, rgba(106,54,255,.20), rgba(0,0,0,0)) !important;
      }
      .rt-card .card-title{ color:#fff !important; font-weight:900 !important; }
      .rt-muted{ color:#bdbdbd !important; }
      .rt-small{ font-size:12px; font-weight:800; letter-spacing:.4px; text-transform:uppercase; }
      .kpi-wrap .kpi-card{
        background:#16181d;
        border:1px solid rgba(255,255,255,.07);
        border-radius:18px;
        padding:14px 14px;
        box-shadow:0 10px 28px rgba(0,0,0,.35);
        overflow:hidden;
        position:relative;
      }
      .kpi-wrap .kpi-title{
        color:#bdbdbd;
        font-weight:800;
        font-size:12px;
        letter-spacing:.4px;
        text-transform:uppercase;
        margin:0;
      }
      .kpi-wrap .kpi-value{
        color:#fff;
        font-weight:900;
        font-size:24px;
        margin:4px 0 0 0;
        line-height:1.1;
      }
      .kpi-wrap .kpi-sub{
        color:#9aa0a6;
        font-size:12px;
        margin:6px 0 0 0;
        font-weight:700;
      }
      .kpi-wrap .kpi-ico{
        position:absolute;
        right:14px;
        top:14px;
        opacity:.18;
        font-size:40px;
      }
      .kpi-wrap .kpi-badge{
        display:inline-block;
        margin-top:8px;
        padding:5px 10px;
        border-radius:999px;
        font-size:12px;
        font-weight:900;
        border:1px solid rgba(255,255,255,.10);
      }

      /* info-box uniformes */
      .info-box.rt-infobox{
        background:#16181d !important;
        border:1px solid rgba(255,255,255,.07) !important;
        border-radius:18px !important;
        box-shadow:0 10px 28px rgba(0,0,0,.35) !important;
      }
      .info-box.rt-infobox .info-box-content .info-box-text{ color:#cfcfcf !important; font-weight:800; }
      .info-box.rt-infobox .info-box-number{ color:#fff !important; font-weight:900; }
      .rt-eye{
        position:absolute;
        right:12px;
        top:10px;
        z-index:10;
        opacity:.85;
      }
      .rt-eye a{ color:#fff; }

      /* =========================
         ✅ CENTRO DE CONTROL + ACTIVIDAD
      ========================= */
      .rt-control .ctrl-btn{
        display:flex;
        align-items:center;
        justify-content:flex-start;
        gap:10px;
        padding:12px 14px;
        border-radius:16px;
        font-weight:900;
        color:#fff !important;
        border:1px solid rgba(255,255,255,.08);
        background:#0f1115;
        box-shadow:0 10px 28px rgba(0,0,0,.35);
        transition: all .18s ease;
        text-decoration:none !important;
        width:100%;
        margin-bottom:10px;
      }
      .rt-control .ctrl-btn:hover{
        transform: translateY(-1px);
        border-color: rgba(255,255,255,.14);
        filter: brightness(1.05);
      }
      .rt-control .ctrl-ico{
        width:40px;
        height:40px;
        border-radius:14px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:18px;
        font-weight:900;
        background:rgba(106,54,255,.18);
        border:1px solid rgba(106,54,255,.25);
      }
      .rt-control .ctrl-txt small{
        display:block;
        font-weight:800;
        color:#bdbdbd;
        margin-top:2px;
      }
      .rt-control .live-item{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:10px;
        padding:10px 12px;
        border-bottom:1px solid rgba(255,255,255,.06);
      }
      .rt-control .live-item:last-child{ border-bottom:none; }
      .rt-control .live-left{
        display:flex;
        flex-direction:column;
        gap:4px;
        min-width:0;
      }
      .rt-control .live-title{
        color:#fff;
        font-weight:900;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
      }
      .rt-control .live-sub{
        color:#bdbdbd;
        font-weight:800;
        font-size:12px;
      }
      .rt-control .live-time{
        color:#9aa0a6;
        font-weight:900;
        font-size:12px;
        white-space:nowrap;
      }

      /* =========================
         ✅ BUSCADOR DISPONIBILIDAD (CARD PREMIUM)
      ========================= */
      .av-rt label{ color:#bdbdbd; font-weight:800; font-size:12px; }
      .av-rt input[type="date"]{
        background:#0f1115 !important;
        border:1px solid rgba(255,255,255,.12) !important;
        color:#fff !important;
        border-radius: 12px !important;
        padding: 10px 12px !important;
      }
      .av-rt .btn-av{
        background: linear-gradient(90deg,#2ecc71,#27ae60) !important;
        border:none !important;
        color:#fff !important;
        font-weight:900 !important;
        border-radius: 14px !important;
      }
      .av-rt .av-result{
        margin-top:12px;
        background:#0f1115;
        border:1px solid rgba(255,255,255,.08);
        border-radius:14px;
        overflow:hidden;
      }
      .av-rt .av-result-head{
        padding:10px 12px;
        border-bottom:1px solid rgba(255,255,255,.08);
        font-weight:900;
        color:#2ecc71;
        font-size:13px;
      }
      .av-rt .table{ margin:0 !important; color:#eaeaea; }
      .av-rt .table th{
        border-top:0 !important;
        color:#cfcfcf !important;
        font-size:12px;
      }
      .av-rt .table td{ border-color: rgba(255,255,255,.06) !important; vertical-align: middle; }
      .av-rt .av-err{
        background:#2b1b1b;
        border:1px solid rgba(231,76,60,.35);
        color:#ffd7d7;
        padding:10px 12px;
        border-radius:14px;
        font-weight:800;
      }
      .av-rt .av-result .table th:first-child,
      .av-rt .av-result .table td:first-child{
        padding-left: 14px !important;
      }
    </style>

    <!-- =========================
         ✅ KPI ENTERPRISE (FINANZAS HOY)
    ========================= -->
    <?php
      $ingresos_hoy = floatval($totsells);
      $pagos_hoy    = floatval($totpayments);
      $gastos_hoy   = floatval($totspend + $totmaintenance + $totfuel + $totoll);
      $neto_hoy     = floatval($ntot);
      $neto_ok = $neto_hoy >= 0;
    ?>
    
    
    <!-- =========================
         ✅ BUSCADOR DISPONIBILIDAD (CARD PREMIUM)
    ========================= -->
    <?php
    $av_from = isset($_POST["av_from"]) ? trim($_POST["av_from"]) : "";
    $av_to   = isset($_POST["av_to"])   ? trim($_POST["av_to"])   : "";
    $av_open = false;
    $av_error = "";
    $av_cars  = [];

    if(isset($_POST["av_action"]) && $_POST["av_action"] === "search"){
      $av_open = true;

      if($av_from=="" || $av_to==""){
        $av_error = "Seleccione Fecha 1 y Fecha 2.";
      } elseif(strtotime($av_from) === false || strtotime($av_to) === false){
        $av_error = "Fechas inválidas.";
      } elseif(strtotime($av_from) > strtotime($av_to)){
        $av_error = "La Fecha 1 no puede ser mayor que la Fecha 2.";
      } else {

        $fromSQL = date("Y-m-d 00:00:00", strtotime($av_from));
        $toSQL   = date("Y-m-d 23:59:59", strtotime($av_to));

        $stock_id = intval(StockData::getPrincipal()->id);

        $db  = new Database();
        $con = $db->connect();

        $sql = "
          SELECT c.id, c.name, c.plate, c.token, c.year, c.invoice_file
          FROM cars c
          WHERE c.stock_id = $stock_id
            AND NOT EXISTS (
              SELECT 1
              FROM booking b
              WHERE b.car_id = c.id
                AND b.stock_id = $stock_id
                AND b.status IN (0,1)
                AND (b.start_at <= '$toSQL' AND b.end_at >= '$fromSQL')
            )
          ORDER BY c.name ASC
        ";

        $q = $con->query($sql);
        if(!$q){
          $av_error = $con->error;
        } else {
          while($r = $q->fetch_assoc()){
            $av_cars[] = $r;
          }
        }
      }
    }
    ?>
    
<div class="row mb-3 av-rt">
  <div class="col-12">
    <div class="card rt-card my-3" id="avCardBox">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h3 class="card-title mb-0">
            <i class="fa fa-search"></i>
            <?php echo (Core::$user->language=="EN"?"Availability Finder":"Buscador de Disponibilidad"); ?>
          </h3>
          <span class="rt-muted" style="font-weight:800;">
            — <?php echo (Core::$user->language=="EN"?"Check free cars by date":"Verifica vehículos disponibles por fecha"); ?>
          </span>
        </div>

     
      </div>

      <div class="card-body">

        <form method="POST">
          <input type="hidden" name="av_action" value="search">

          <div class="row">
            <div class="col-12 col-md-5 mb-3">
              <label><?php echo (Core::$user->language=="EN"?"From":"Fecha desde"); ?>:</label>
              <input type="date" name="av_from" class="form-control" value="<?php echo htmlspecialchars($av_from); ?>">
            </div>
            <div class="col-12 col-md-5 mb-3">
              <label><?php echo (Core::$user->language=="EN"?"To":"Fecha hasta"); ?>:</label>
              <input type="date" name="av_to" class="form-control" value="<?php echo htmlspecialchars($av_to); ?>">
            </div>
            <div class="col-12 col-md-2 mb-3">
              <label><?php echo (Core::$user->language=="EN"?"Search":"Buscar"); ?>:</label>
              <button type="submit" class="btn-av btn btn-block">
                <i class="fa fa-search"></i>
              </button>
            </div>
          </div>
        </form>

        <div id="avResult" style="<?php echo $av_open ? '' : 'display:none;'; ?>">
          <?php if($av_open && $av_error!=""): ?>
            <div class="av-err">⚠️ <?php echo htmlspecialchars($av_error); ?></div>
          <?php elseif($av_open): ?>
            <div class="av-result">
              <div class="av-result-head d-flex justify-content-between align-items-center flex-wrap">
                <div>
                  ✅ <?php echo (Core::$user->language=="EN"?"Available":"Disponibles"); ?>: <?php echo count($av_cars); ?>
                  <span style="color:#b8b8b8;">
                    (<?php echo htmlspecialchars($av_from); ?> → <?php echo htmlspecialchars($av_to); ?>)
                  </span>
                </div>

                <?php if(count($av_cars)>0): 
                
                $TicketMm = StockData::getPrincipal()->ticket_mm;
                $pdfUrl = $TicketMm . "/ticket-cars.php?av_from=" . urlencode($av_from) . "&av_to=" . urlencode($av_to);?>
                
                <a href="<?php echo $pdfUrl; ?>"
                 class="btn-danger btn-sm" style="font-weight:700;"
                 onclick="return abrirPDF(this.href, event)">
                 <?php echo (Core::$user->language=="EN"?"Generate PDF":"Generar PDF"); ?>
              </a>
              
<!-- MODAL CONTRATO (UNA SOLA VEZ, FUERA DEL FOREACH) -->
<div id="modalPDF" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:#000a; z-index:9999;">
  <div style="position:relative; width:90%; height:90%; margin:5% auto; background:#1e1e1e; border-radius:10px; overflow:hidden; padding-top:80px;">
    <div style="position:absolute; top:20px; right:20px; display:flex; flex-direction:column; gap:10px;">
      <button type="button" onclick="imprimirPDF()" style="background:#28a745; color:#fff; padding:10px 16px; border-radius:40px; font-weight:bold;"><i class="fa fa-print"></i> IMPRIMIR</button>
      <a id="btnDescargar" href="#" download style="background:#007bff; color:#fff; padding:10px 16px; border-radius:40px; font-weight:bold; text-decoration:none;"><i class="fa fa-download"></i> DESCARGAR</a>
      <button type="button" onclick="cerrarPDF()" style="background:#c40030; color:#fff; padding:10px 16px; border-radius:40px; font-weight:bold;"><i class="fa fa-times"></i> CERRAR</button>
    </div>
    <iframe id="iframePDF" src="" style="width:100%; height:100%; border:none;"></iframe>
  </div>
</div>

<script>
function abrirPDF(url, event) {
  if (window.innerWidth >= 1024) {
    event.preventDefault();
    document.getElementById('iframePDF').src = url;
    document.getElementById('btnDescargar').href = url;
    document.getElementById('modalPDF').style.display = 'block';
    return false;
  }
  return true;
}
function cerrarPDF() {
  document.getElementById('modalPDF').style.display = 'none';
  document.getElementById('iframePDF').src = '';
  document.getElementById('btnDescargar').href = '#';
}
function imprimirPDF() {
  const iframe = document.getElementById('iframePDF');
  if(iframe && iframe.contentWindow) iframe.contentWindow.print();
}
</script>
<?php endif; ?>
              </div>

              <div style="overflow:auto;">
                <table class="table table-borderless table-sm">
                  <thead>
                    <tr>
                      <th style="width:120px;"><?php echo (Core::$user->language=="EN"?"PHOTO":"FOTO"); ?></th>
                      <th style="width:260px;"><?php echo (Core::$user->language=="EN"?"VEHICLE":"VEHÍCULO"); ?></th>
                      <th style="width:120px;"><?php echo (Core::$user->language=="EN"?"ID":"FICHA"); ?></th>
                      <th style="width:90px;"><?php echo (Core::$user->language=="EN"?"YEAR":"AÑO"); ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($av_cars as $c): ?>
                      <tr style="border-bottom:1px solid #333;">
                        <td>
<?php if(!empty($c["invoice_file"])): ?>
  <img class="rc-carimg"
       src="CF-SYSTEMS/storage/invoice_files/<?php echo $c["invoice_file"]; ?>"
       alt="car"
       loading="lazy"
       decoding="async"
       width="90"
       height="65"
       style="object-fit:cover;border-radius:8px;border:1px solid #444;">
<?php else: ?>
  <img class="rc-carimg"
       src="https://via.placeholder.com/90x65?text=NO+IMAGE"
       alt="car"
       loading="lazy"
       decoding="async"
       width="90"
       height="65"
       style="object-fit:cover;border-radius:8px;border:1px solid #444;">
<?php endif; ?>
                        </td>
                        <td><b><?php echo htmlspecialchars($c["name"]); ?></b></td>
                        <td><?php echo htmlspecialchars($c["token"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($c["year"] ?? ""); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

            </div>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </div>
</div>

    <div class="row kpi-wrap mb-3">
      <div class="col-12 col-md-3 mb-3">
        <div class="kpi-card">
          <div class="kpi-ico"><i class="fas fa-chart-line"></i></div>
          <p class="kpi-title"><?php echo (Core::$user->language=="EN"?"Income today":"Ingresos hoy"); ?></p>
          <div class="kpi-value"><?php echo Core::$symbol." ".number_format($ingresos_hoy,2,".",","); ?></div>
          <div class="kpi-sub"><?php echo (Core::$user->language=="EN"?"Rents / operations":"Rentas / operaciones"); ?></div>
          <span class="kpi-badge">📌 <?php echo (Core::$user->language=="EN"?"Real-time":"En tiempo real"); ?></span>
        </div>
      </div>

      <div class="col-12 col-md-3 mb-3">
        <div class="kpi-card">
          <div class="kpi-ico"><i class="fas fa-cash-register"></i></div>
          <p class="kpi-title"><?php echo (Core::$user->language=="EN"?"Payments today":"Pagos hoy"); ?></p>
          <div class="kpi-value"><?php echo Core::$symbol." ".number_format($pagos_hoy,2,".",","); ?></div>
          <div class="kpi-sub"><?php echo (Core::$user->language=="EN"?"Collected":"Cobros recibidos"); ?></div>
          <span class="kpi-badge">✅ <?php echo (Core::$user->language=="EN"?"Cash control":"Control de caja"); ?></span>
        </div>
      </div>

      <div class="col-12 col-md-3 mb-3">
        <div class="kpi-card">
          <div class="kpi-ico"><i class="fas fa-receipt"></i></div>
          <p class="kpi-title"><?php echo (Core::$user->language=="EN"?"Expenses today":"Gastos hoy"); ?></p>
          <div class="kpi-value"><?php echo Core::$symbol." ".number_format($gastos_hoy,2,".",","); ?></div>
          <div class="kpi-sub"><?php echo (Core::$user->language=="EN"?"Bills + Maint. + Fuel + Tolls":"Gastos + Mant. + Comb. + Peajes"); ?></div>
          <span class="kpi-badge">⚠️ <?php echo (Core::$user->language=="EN"?"Outflow":"Salidas"); ?></span>
        </div>
      </div>

      <div class="col-12 col-md-3 mb-3">
        <div class="kpi-card" style="<?php echo $neto_ok ? '' : 'border-color: rgba(231,76,60,.35);'; ?>">
          <div class="kpi-ico"><i class="fas fa-balance-scale"></i></div>
          <p class="kpi-title"><?php echo (Core::$user->language=="EN"?"Net balance today":"Balance neto hoy"); ?></p>
          <div class="kpi-value" style="<?php echo $neto_ok ? 'color:#2ecc71;' : 'color:#e74c3c;'; ?>">
            <?php echo Core::$symbol." ".number_format($neto_hoy,2,".",","); ?>
          </div>
          <div class="kpi-sub"><?php echo (Core::$user->language=="EN"?"Income + Deposit − Expenses":"Ingresos + Depósito − Gastos"); ?></div>
          <span class="kpi-badge"><?php echo $neto_ok ? "🟢 ".(Core::$user->language=="EN"?"Positive":"Positivo") : "🔴 ".(Core::$user->language=="EN"?"Negative":"Negativo"); ?></span>
        </div>
      </div>
    </div>
    
    
<!-- =========================
     MAPA EN VIVO DASHBOARD
========================= -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
  .live-map-card{
    background:#16181d;
    border:1px solid rgba(255,255,255,.08);
    border-radius:18px;
    box-shadow:0 10px 28px rgba(0,0,0,.35);
    overflow:hidden;
    margin-bottom:20px;
  }
  .live-map-head{
    padding:14px 16px;
    border-bottom:1px solid rgba(255,255,255,.08);
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
  }
  .live-map-head h3{
    color:white;
    font-weight:900;
    margin:0;
    font-size:18px;
  }
  .live-map-status span{
    display:inline-block;
    padding:5px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:900;
    margin-left:5px;
    background:#0f1115;
    border:1px solid rgba(255,255,255,.10);
    color:#fff;
  }
  #dashboardLiveMap{
    height:360px;
    width:100%;
  }
</style>

<div class="row">
  <div class="col-12">
    <div class="live-map-card">
      <div class="live-map-head">
        <h3>
          <i class="fas fa-satellite-dish"></i>
          Monitoreo GPS en Vivo
        </h3>

        <div class="live-map-status">
          <span id="gpsTotal">0 vehículos</span>
          <span id="gpsMoving">0 moviendo</span>
          <span id="gpsStopped">0 detenidos</span>

          <a href="./?view=gps&opt=map"
             class="btn btn-sm btn-warning"
             style="font-weight:900;margin-left:8px;">
             Ver mapa completo
          </a>
        </div>
      </div>

      <div id="dashboardLiveMap"></div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){

  var dashMap = L.map('dashboardLiveMap').setView([19.4517, -70.6970], 10);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
  }).addTo(dashMap);

  var dashMarkers = {};
  var firstLoad = true;

  function getStatusColor(speed, createdAt){

    var lastDate = new Date(createdAt.replace(" ", "T"));
    var now = new Date();
    var diffMin = (now - lastDate) / 60000;

    if(diffMin > 30){
      return '#e74c3c';
    }

    if(parseFloat(speed) > 5){
      return '#2ecc71';
    }

    return '#f1c40f';
  }

  function crearIcono(v){

    var color = getStatusColor(v.speed, v.created_at);

    var imageHtml = '';

    if(v.invoice_file){
      imageHtml = '<img src="CF-SYSTEMS/storage/invoice_files/' + v.invoice_file + '" style="width:100%;height:100%;object-fit:cover;">';
    }else{
      imageHtml = '<i class="fa fa-car" style="color:white;font-size:18px;"></i>';
    }

    return L.divIcon({
      className: '',
      html:
      '<div style="'+
      'width:46px;'+
      'height:46px;'+
      'border-radius:50%;'+
      'background:'+color+';'+
      'border:3px solid white;'+
      'box-shadow:0 4px 15px rgba(0,0,0,.55);'+
      'display:flex;'+
      'align-items:center;'+
      'justify-content:center;'+
      'overflow:hidden;'+
      '">'+
      imageHtml+
      '</div>',
      iconSize: [46,46],
      iconAnchor: [23,23],
      popupAnchor: [0,-20]
    });
  }

  function cargarMapaDashboard(){

    fetch('./?action=get_vehicles_locations&mode=last')
    .then(function(res){
      return res.json();
    })
    .then(function(data){

      var total = 0;
      var moving = 0;
      var stopped = 0;
      var bounds = [];

      if(!data.vehicles){
        return;
      }

      data.vehicles.forEach(function(v){

        if(!v.latitude || !v.longitude){
          return;
        }

        total++;

        if(parseFloat(v.speed) > 5){
          moving++;
        }else{
          stopped++;
        }

        var lat = parseFloat(v.latitude);
        var lng = parseFloat(v.longitude);
        var key = v.vehicle_id;

        var popup =
          '<b>'+v.name+' ('+v.plate+')</b><br>'+
          'Velocidad: '+v.speed+' km/h<br>'+
          'Última señal: '+v.created_at+'<br>'+
          '<a href="./?view=gps&opt=map" class="btn btn-warning btn-sm mt-2" style="font-weight:900;">Abrir GPS</a>';

        if(dashMarkers[key]){

          dashMarkers[key].setLatLng([lat,lng]);
          dashMarkers[key].setIcon(crearIcono(v));
          dashMarkers[key].setPopupContent(popup);

        }else{

          dashMarkers[key] = L.marker([lat,lng], {
            icon: crearIcono(v)
          }).addTo(dashMap).bindPopup(popup);
        }

        bounds.push([lat,lng]);
      });

      document.getElementById('gpsTotal').innerHTML = total + ' vehículos';
      document.getElementById('gpsMoving').innerHTML = moving + ' moviendo';
      document.getElementById('gpsStopped').innerHTML = stopped + ' detenidos';

      if(firstLoad && bounds.length > 0){
        dashMap.fitBounds(bounds, {
          padding:[40,40]
        });

        firstLoad = false;
      }
    })
    .catch(function(err){
      console.log('Error GPS Dashboard:', err);
    });
  }

  cargarMapaDashboard();

  setInterval(function(){
    cargarMapaDashboard();
  }, 10000);

});
</script>


    <!-- =================================================================================
         ✅ CENTRO DE CONTROL + ACTIVIDAD EN VIVO (DEBAJO DE KPI)
         - Centro de Control (acciones rápidas)
         - Actividad en vivo (hoy) desde varias tablas (booking, payment, spends, etc.)
    ================================================================================== -->
    <div class="row rt-control mb-3">

      <!-- CENTRO DE CONTROL -->
      <div class="col-12 col-lg-8 mb-3">
        <div class="card rt-card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-sliders-h"></i>
              <?php echo (Core::$user->language=="EN" ? "Control Center" : "Centro de Control"); ?>
            </h3>
            <span class="rt-muted" style="font-weight:800;">
              — <?php echo (Core::$user->language=="EN" ? "Quick actions to work faster" : "Acciones rápidas para trabajar más rápido"); ?>
            </span>
          </div>

          <div class="card-body">
            <div class="row">

              <div class="col-12 col-md-6">
                <a class="ctrl-btn" href="./?view=booking&opt=new">
                  <div class="ctrl-ico"><i class="fas fa-plus"></i></div>
                  <div class="ctrl-txt">
                    <?php echo (Core::$user->language=="EN" ? "New reservation" : "Nueva reserva"); ?>
                    <small><?php echo (Core::$user->language=="EN" ? "Create / manage bookings" : "Crear / gestionar reservas"); ?></small>
                  </div>
                </a>

                <a class="ctrl-btn" href="?view=credit&opt=pay">
                  <div class="ctrl-ico" style="background:rgba(46,204,113,.16);border-color:rgba(46,204,113,.25);">
                    <i class="fas fa-cash-register"></i>
                  </div>
                  <div class="ctrl-txt">
                    <?php echo (Core::$user->language=="EN" ? "Register payment" : "Registrar pago"); ?>
                    <small><?php echo (Core::$user->language=="EN" ? "Cash control / collections" : "Control de caja / cobros"); ?></small>
                  </div>
                </a>

                <a class="ctrl-btn" href="./?view=finance&opt=new&spends=negocio">
                  <div class="ctrl-ico" style="background:rgba(231,76,60,.16);border-color:rgba(231,76,60,.25);">
                    <i class="fas fa-minus-circle"></i>
                  </div>
                  <div class="ctrl-txt">
                    <?php echo (Core::$user->language=="EN" ? "Register expense" : "Registrar gasto"); ?>
                    <small><?php echo (Core::$user->language=="EN" ? "Bills / outflows" : "Salidas / gastos"); ?></small>
                  </div>
                </a>
              </div>

              <div class="col-12 col-md-6">
                <a class="ctrl-btn" href="./?view=finance&opt=vehicle">
                  <div class="ctrl-ico" style="background:rgba(52,152,219,.16);border-color:rgba(52,152,219,.25);">
                    <i class="fas fa-tools"></i>
                  </div>
                  <div class="ctrl-txt">
                    <?php echo (Core::$user->language=="EN" ? "Maintenance" : "Mantenimiento"); ?>
                    <small><?php echo (Core::$user->language=="EN" ? "Vehicle costs tracking" : "Control de costos de vehículos"); ?></small>
                  </div>
                </a>

                <a class="ctrl-btn" href="./?view=contract&opt=running">
                  <div class="ctrl-ico" style="background:rgba(241,196,15,.16);border-color:rgba(241,196,15,.25);">
                    <i class="fas fa-file-signature"></i>
                  </div>
                  <div class="ctrl-txt">
                    <?php echo (Core::$user->language=="EN" ? "Contracts" : "Contratos"); ?>
                    <small><?php echo (Core::$user->language=="EN" ? "View / manage active contracts" : "Ver / gestionar contratos activos"); ?></small>
                  </div>
                </a>

                <a class="ctrl-btn" href="./?view=gps&opt=map">
                  <div class="ctrl-ico" style="background:rgba(155,89,182,.16);border-color:rgba(155,89,182,.25);">
                    <i class="fas fa-map-marker-alt"></i>
                  </div>
                  <div class="ctrl-txt">
                    <?php echo (Core::$user->language=="EN" ? "GPS / Tracking" : "GPS / Rastreo"); ?>
                    <small><?php echo (Core::$user->language=="EN" ? "Vehicles in real time" : "Vehículos en tiempo real"); ?></small>
                  </div>
                </a>
              </div>

            </div><!-- row -->
          </div><!-- body -->
        </div>
      </div>

      <!-- ACTIVIDAD EN VIVO -->
      <div class="col-12 col-lg-4 mb-3">
  <div class="card rt-card">
    <div class="card-header">
      <h3 class="card-title">
        <i class="fas fa-bolt"></i>
        <?php echo (Core::$user->language=="EN" ? "Live activity (today)" : "Actividad en vivo (hoy)"); ?>
      </h3>
    </div>

    <div class="card-body p-0 live-scroll-box">
      <?php
        $db  = new Database();
        $con = $db->connect();
        $stock_id = intval(StockData::getPrincipal()->id);
        $today = date("Y-m-d");

        $sql_live = "
          (SELECT created_at AS atime, 'Reserva' AS tipo, CONCAT('Booking #', id) AS detalle
           FROM booking
           WHERE stock_id=$stock_id AND DATE(created_at)='$today')
          UNION ALL
          (SELECT created_at AS atime, 'Pago' AS tipo, CONCAT('Pago RD$', val, ' (Booking ', booking_id, ')') AS detalle
           FROM payment
           WHERE stock_id=$stock_id AND DATE(created_at)='$today')
          UNION ALL
          (SELECT created_at AS atime, 'Gasto' AS tipo, CONCAT(name, ' RD$', price) AS detalle
           FROM spend
           WHERE stock_id=$stock_id AND DATE(created_at)='$today')
          UNION ALL
          (SELECT created_at AS atime, 'Mantenimiento' AS tipo, CONCAT('Mantenimiento #', id) AS detalle
           FROM maintenance
           WHERE stock_id=$stock_id AND DATE(created_at)='$today')
          UNION ALL
          (SELECT created_at AS atime, 'Combustible' AS tipo, CONCAT('Fuel #', id) AS detalle
           FROM fuels
           WHERE stock_id=$stock_id AND DATE(created_at)='$today')
          UNION ALL
          (SELECT created_at AS atime, 'Peaje' AS tipo, CONCAT('Toll #', id) AS detalle
           FROM toll
           WHERE stock_id=$stock_id AND DATE(created_at)='$today')
          ORDER BY atime DESC
          LIMIT 10
        ";

        $live = [];
        $q_live = @$con->query($sql_live);
        if($q_live){
          while($r = $q_live->fetch_assoc()){
            $live[] = $r;
          }
        }
      ?>

      <?php if(count($live)==0): ?>
        <div style="padding:12px;">
          <div class="av-err" style="margin:0;">
            <?php echo (Core::$user->language=="EN" ? "No activity registered today." : "No hay actividad registrada hoy."); ?>
          </div>
        </div>
      <?php else: ?>
        <?php foreach($live as $it): ?>
          <div class="live-item">
            <div class="live-left">
              <div class="live-title">
                <?php echo htmlspecialchars($it["tipo"]); ?>
                <span class="badge badge-secondary" style="margin-left:6px;">
                  <?php echo date("h:i a", strtotime($it["atime"])); ?>
                </span>
              </div>
              <div class="live-sub">
                <?php echo htmlspecialchars($it["detalle"]); ?>
              </div>
            </div>
            <div class="live-time">
              <?php echo date("d-m-Y", strtotime($it["atime"])); ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </div><!-- body -->
  </div>
</div>

<style>
.live-scroll-box{
  max-height: 287px; /* ajusta según el alto aproximado de 4 registros */
  overflow-y: auto;
  overflow-x: hidden;
}

.live-scroll-box::-webkit-scrollbar{
  width: 8px;
}

.live-scroll-box::-webkit-scrollbar-thumb{
  background: rgba(255,255,255,0.20);
  border-radius: 10px;
}

.live-scroll-box::-webkit-scrollbar-track{
  background: transparent;
}
</style>

    </div>


      <div class="row">
        <div class="col-md-8">

          <div class="row">

            <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <div class="info-box rt-infobox mb-3 position-relative">
                <span class="rt-eye"><b><a href="./?view=contract&opt=running"><i class="fa fa-eye fa-2x"></i></a></b></span>
                <span class="info-box-icon bg-info elevation-1"><i class="fa fa-list-ul"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"><?php echo (Core::$user->language=="EN"?"Vehicles":"Vehiculos"); ?></span>
                  <span class="info-box-text"><?php echo (Core::$user->language=="EN"?"Rented":"Rentados"); ?></span>
                  <span class="info-box-number">
                    <?php echo count(BookingData::getAllBySQL("where type_id=1 and status=1 and stock_id=".StockData::getPrincipal()->id)); ?>
                  </span>
                </div>
              </div>
            </div>

            <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <div class="info-box rt-infobox mb-3 position-relative">
                <span class="rt-eye"><b><a href="./?view=cars&opt=all"><i class="fa fa-eye fa-2x"></i></a></b></span>
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-car"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"><?php echo (Core::$user->language=="EN"?"Total":"Total"); ?></span>
                  <span class="info-box-text"><?php echo (Core::$user->language=="EN"?"Vehicles":"Vehiculos"); ?></span>
                  <span class="info-box-number"><?php echo count(CarsData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id)); ?></span>
                </div>
              </div>
            </div>

            <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <div class="info-box rt-infobox mb-3 position-relative">
                <span class="rt-eye"><b><a href="./?view=person&opt=all"><i class="fa fa-eye fa-2x"></i></a></b></span>
                <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"><?php echo (Core::$user->language=="EN"?"Customers":"Clientes"); ?></span>
                  <span class="info-box-text"><?php echo (Core::$user->language=="EN"?"Registered":"Registrado"); ?></span>
                  <span class="info-box-number"><?php echo count(PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id)); ?></span>
                </div>
              </div>
            </div>

            <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <div class="info-box rt-infobox mb-3">
                <span class="info-box-icon elevation-1" style="background-color: gray;"><i class="fa fa-asterisk"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"><?php echo (Core::$user->language=="EN"?"Deductible":"Deducible"); ?></span>
                  <span class="info-box-text"><?php echo Core::$symbol; ?></span>
                  <span class="info-box-number"><?php echo number_format($totdeposit,2,".",",");?></span>
                </div>
              </div>
            </div>

            <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <div class="info-box rt-infobox mb-3">
                <span class="info-box-icon elevation-1" style="background-color: gray;"><i class="fas fa-check"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"><?php echo (Core::$user->language=="EN"?"Payments":"Pagos"); ?></span>
                  <span class="info-box-text"><?php echo Core::$symbol; ?></span>
                  <span class="info-box-number"><?php echo number_format($totpayments,2,".",",");?></span>
                </div>
              </div>
            </div>

            <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <div class="info-box rt-infobox mb-3">
                <span class="info-box-icon elevation-1" style="background-color: gray;"><i class="fa fa-balance-scale"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"><?php echo (Core::$user->language=="EN"?"Earnings":"Ganancias"); ?></span>
                  <span class="info-box-text"><?php echo Core::$symbol; ?></span>
                  <span class="info-box-number"><?php echo number_format($totpayments-($totmaintenance+$totspend),2,".",",");?></span>
                </div>
              </div>
            </div>

            <?php if(StockData::getPrincipal()->method=="1"):?>
            <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <div class="info-box rt-infobox mb-3 position-relative">
                <span class="rt-eye"><b><a href="./?view=booking&opt=all"><i class="fa fa-eye fa-2x"></i></a></b></span>
                <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-edit"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"><?php echo (Core::$user->language=="EN"?"Reservations":"Reservas"); ?></span>
                  <span class="info-box-text"><?php echo (Core::$user->language=="EN"?"Total":"Total"); ?></span>
                  <span class="info-box-number"><?php echo count(BookingData::getAllBySQL("where status=0 and stock_id=".StockData::getPrincipal()->id)); ?></span>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <div class="info-box rt-infobox mb-3 position-relative">
                <span class="rt-eye"><b><a href="./?view=finance&opt=all&spends=Negocio"><i class="fa fa-eye fa-2x"></i></a></b></span>
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-minus-square"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"><?php echo (Core::$user->language=="EN"?"Bills":"Gastos"); ?></span>
                  <span class="info-box-text"><?php echo Core::$symbol; ?></span>
                  <span class="info-box-number"><?php echo number_format($totspend,2,".",",");?></span>
                </div>
              </div>
            </div>

            <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <div class="info-box rt-infobox mb-3 position-relative">
                <span class="rt-eye"><b><a href="./?view=finance&opt=vehicle"><i class="fa fa-eye fa-2x"></i></a></b></span>
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cogs"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"><?php echo (Core::$user->language=="EN"?"Maintenance":"Mantenimiento"); ?></span>
                  <span class="info-box-text"><?php echo Core::$symbol; ?></span>
                  <span class="info-box-number"><?php echo number_format($totmaintenance,2,".",",");?></span>
                </div>
              </div>
            </div>

          </div><!-- /.row (KPIs) -->

        </div><!-- /.col-md-8 -->

      <!-- =========================
     ✅ FIDELIZACIÓN (DERECHA)
========================= -->
<div class="col-md-4">
  <?php
    $base = new Database();
    $con  = $base->connect();
    mysqli_set_charset($con, "utf8");

    $clientes_top = [];
    $total_puntos = 0;

    $sql = "SELECT 
              p.id, 
              p.name, 
              FLOOR(SUM(COALESCE(b.total,0)) / 100) AS puntos
            FROM person p
            INNER JOIN booking b ON p.id = b.person_id
            GROUP BY p.id, p.name
            ORDER BY puntos DESC";

    $res = $con->query($sql);

    if ($res instanceof mysqli_result) {
      while ($row = $res->fetch_assoc()) {
        $row["id"]     = (int)($row["id"] ?? 0);
        $row["name"]   = (string)($row["name"] ?? "");
        $row["puntos"] = (int)($row["puntos"] ?? 0);

        $clientes_top[] = $row;
        $total_puntos  += $row["puntos"];
      }
      $res->free();
    }
  ?>

  <div class="card rt-card">
    <div class="card-header border-transparent">
      <h3 class="card-title">
        <i class="fa fa-star"></i>
        <?php echo (Core::$user->language == "EN" ? "Loyalty - Frequent customers" : "Fidelización - Clientes Frecuentes"); ?>
      </h3>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive" style="max-height:305px; overflow-y:auto;">
        <table class="table m-0">
          <thead>
            <tr>
              <th class="rt-muted"><?php echo (Core::$user->language == "EN" ? "Customer" : "Cliente"); ?></th>
              <th class="rt-muted"><?php echo (Core::$user->language == "EN" ? "Points" : "Puntos"); ?></th>
              <th class="rt-muted"><?php echo (Core::$user->language == "EN" ? "Level" : "Nivel"); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($clientes_top)): ?>
              <?php foreach ($clientes_top as $c): ?>
                <?php
                  $puntos = (int)($c["puntos"] ?? 0);

                  if ($puntos >= 100) {
                    $nivel = "<span class='badge badge-warning'>Oro</span>";
                  } elseif ($puntos >= 50) {
                    $nivel = "<span class='badge badge-secondary'>Plata</span>";
                  } else {
                    $nivel = "<span class='badge badge-info'>Bronce</span>";
                  }
                ?>
                <tr>
                  <td style="color:#fff; font-weight:900;">
                    <?php echo htmlspecialchars((string)($c["name"] ?? ""), ENT_QUOTES, "UTF-8"); ?>
                  </td>
                  <td>
                    <span class="badge badge-primary">
                      <?php echo $puntos; ?> pts
                    </span>
                  </td>
                  <td><?php echo $nivel; ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="text-center rt-muted" style="padding:15px;">
                  <?php echo (Core::$user->language == "EN" ? "No loyalty data available." : "No hay datos de fidelización disponibles."); ?>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div><!-- /.col-md-4 -->
</div><!-- /.row principal -->

      <!-- =========================
           ✅ ANÁLISIS RESERVAS (ENTERPRISE)
      ========================= -->
      <?php
      $base = new Database();
      $con = $base->connect();

      $anio = date("Y");
      $mes  = date("m");

      $sql = "SELECT MONTH(start_at) AS mes, COUNT(*) AS total
              FROM booking
              WHERE status = 1 AND YEAR(start_at) = $anio
              GROUP BY mes ORDER BY mes";

      $reservas = array_fill(1, 12, 0);
      if ($res = $con->query($sql)) {
        while ($row = $res->fetch_assoc()) {
          $reservas[intval($row["mes"])] = intval($row["total"]);
        }
      }

      $labels = json_encode(["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"]);
      $data   = json_encode(array_values($reservas));

      $sql2 = "SELECT
                  c.id,
                  c.name,
                  c.plate,
                  COUNT(b.id) AS total_reservas
              FROM cars c
              LEFT JOIN booking b
                     ON c.id = b.car_id
                    AND YEAR(b.start_at) = $anio
                    AND MONTH(b.start_at) = $mes
                    AND b.status = 1
              GROUP BY c.id
              ORDER BY total_reservas ASC
              LIMIT 5";

      $vehiculos = [];
      if ($res2 = $con->query($sql2)) {
        while ($row = $res2->fetch_assoc()) {
          $vehiculos[] = $row;
        }
      }
      ?>

      <div class="row">
        <div class="col-md-12">
          <div class="card rt-card">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fa fa-chart-bar"></i>
                <?php echo (Core::$user->language=="EN"?"Bookings analysis":"ANALISIS DE RESERVAS"); ?> <?php echo $anio; ?>
              </h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-8">
                  <p class="text-center" style="color:#fff; font-weight:900;">
                    <?php echo (Core::$user->language=="EN"?"BOOKINGS PER MONTH":"RESERVAS POR MES"); ?>
                  </p>
                  <canvas id="bookingsChart" height="180" style="height:180px;"></canvas>
                </div>

                <div class="col-md-4">
                  <p class="text-center" style="color:#fff; font-weight:900;">
                    <?php echo (Core::$user->language=="EN"?"LEAST BOOKED VEHICLES (CURRENT MONTH)":"VEHÍCULOS MENOS ALQUILADOS (MES ACTUAL)"); ?>
                  </p>

                  <?php foreach ($vehiculos as $v):
                    $meta = 20;
                    $percent = ($meta > 0) ? ($v["total_reservas"] / $meta) * 100 : 0;
                    $color = "bg-danger";
                    if ($percent >= 75) $color = "bg-success";
                    elseif ($percent >= 50) $color = "bg-warning";
                    elseif ($percent >= 25) $color = "bg-primary";
                  ?>
                    <div class="progress-group" style="color:#eaeaea; font-weight:800;">
                      <?php echo strtoupper($v["name"] . " (" . $v["plate"] . ")"); ?>
                      <span class="float-right"><b><?php echo $v["total_reservas"]; ?></b>/<?php echo $meta; ?></span>
                      <div class="progress progress-sm">
                        <div class="progress-bar <?php echo $color; ?>" style="width: <?php echo $percent; ?>%"></div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <div class="card-footer" style="background:#0f1115; border-top:1px solid rgba(255,255,255,.08);">
              <div class="row">
                <div class="col-sm-3 col-6">
                  <div class="description-block border-right">
                    <h5 class="description-header" style="color:#fff; font-weight:900;"><?php echo array_sum($reservas); ?></h5>
                    <span class="description-text" style="color:#bdbdbd; font-weight:800;"><?php echo (Core::$user->language=="EN"?"TOTAL BOOKINGS":"TOTAL RESERVAS"); ?></span>
                  </div>
                </div>
                <div class="col-sm-3 col-6">
                  <div class="description-block border-right">
                    <h5 class="description-header" style="color:#fff; font-weight:900;"><?php echo $reservas[intval($mes)]; ?></h5>
                    <span class="description-text" style="color:#bdbdbd; font-weight:800;"><?php echo (Core::$user->language=="EN"?"THIS MONTH":"ESTE MES"); ?></span>
                  </div>
                </div>
                <div class="col-sm-3 col-6">
                  <div class="description-block border-right">
                    <h5 class="description-header" style="color:#fff; font-weight:900;"><?php echo count($vehiculos); ?></h5>
                    <span class="description-text" style="color:#bdbdbd; font-weight:800;"><?php echo (Core::$user->language=="EN"?"VEHICLES EVALUATED":"VEHÍCULOS EVALUADOS"); ?></span>
                  </div>
                </div>
                <div class="col-sm-3 col-6">
                  <div class="description-block">
                    <h5 class="description-header" style="color:#fff; font-weight:900;"><?php echo $anio; ?></h5>
                    <span class="description-text" style="color:#bdbdbd; font-weight:800;"><?php echo (Core::$user->language=="EN"?"CURRENT YEAR":"AÑO ACTUAL"); ?></span>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <style>
        #bookingsChart{
          height: 250px !important;
          max-height: 250px !important;
          min-height: 250px !important;
        }
      </style>

      <?php
      $data = json_encode(array_values($reservas), JSON_NUMERIC_CHECK);
      $maxY = ceil(max($reservas) / 10) * 10;
      if ($maxY == 0) $maxY = 10;
      ?>

      <script>
      document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('bookingsChart').getContext('2d');
        new Chart(ctx, {
          type: 'line',
          data: {
            labels: <?php echo $labels; ?>,
            datasets: [{
              label: '<?php echo (Core::$user->language=="EN"?"Bookings":"Reservas"); ?>',
              backgroundColor: 'rgba(0,123,255,0.3)',
              borderColor: '#007bff',
              borderWidth: 2,
              pointRadius: 4,
              fill: true,
              data: <?php echo $data; ?>
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2.5,
            plugins: {
              legend: { labels: { color: 'white' } }
            },
            scales: {
              x: { ticks: { color: 'white' } },
              y: {
                beginAtZero: true,
                suggestedMax: <?php echo $maxY; ?>,
                ticks: { color: 'white' }
              }
            }
          }
        });
      });
      </script>


  </div><!-- /.container-fluid -->
</section>

 
<?php endif;?>
