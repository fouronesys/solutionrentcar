<?php
if(!isset($_SESSION)){ session_start(); }

$base = new Database();
$con = $base->connect();

$stock_id = isset($_SESSION["stock_id"]) ? intval($_SESSION["stock_id"]) : 1;
$opt = isset($_GET["opt"]) ? $_GET["opt"] : "all";
?>

<section class="content">
<div class="container-fluid">

<?php if($opt=="all"): ?>

<?php
$sql = "
SELECT *
FROM raffles
WHERE stock_id='$stock_id'
ORDER BY id DESC
";
$query = $con->query($sql);
?>

<div class="row mb-3">
  <div class="col-md-12">
    <h1 style="color:white;font-weight:900;">
      <i class="fa fa-bullhorn"></i> Sorteos / Campañas de Rentas
    </h1>
  </div>

</div>

<div class="card" style="background:#111827;color:white;border-radius:18px;">
<div class="card-body table-responsive">

<?php if($query && $query->num_rows > 0): ?>

<table class="table table-bordered table-hover" style="color:white;">
<thead style="background:#020617;">
<tr>
  <th>#</th>
  <th>Campaña</th>
  <th>Rango</th>
  <th>Días mínimos</th>
  <th>Ganadores</th>
  <th>Estado</th>
  <th>Acción</th>
</tr>
</thead>

<tbody>
<?php while($r = $query->fetch_assoc()): ?>
<tr>
  <td><?php echo $r["id"]; ?></td>

  <td>
    <strong><?php echo strtoupper($r["title"]); ?></strong><br>
    <small style="color:#94a3b8;">
      <?php echo $r["rule_description"]; ?>
    </small>
  </td>

  <td>
    <?php echo $r["start_date"]; ?> al <?php echo $r["end_date"]; ?>
  </td>

  <td><?php echo intval($r["min_rental_days"]); ?> días</td>

  <td><?php echo intval($r["winners_limit"]); ?></td>

  <td>
    <?php if($r["status"]=="active" || $r["status"]==1): ?>
      <span class="badge badge-success">Activa</span>
    <?php else: ?>
      <span class="badge badge-danger">Inactiva</span>
    <?php endif; ?>
  </td>

  <td>
    <a href="./?view=raffle_ticket&raffle_id=<?php echo $r["id"]; ?>" class="btn btn-info btn-sm">
      <i class="fa fa-trophy"></i>
    </a>

    <a href="./?view=raffle&opt=flyer&id=<?php echo $r["id"]; ?>" class="btn btn-primary btn-sm">
      <i class="fa fa-image"></i> Flyer
    </a>

    <a href="./?view=raffle&opt=edit&id=<?php echo $r["id"]; ?>" class="btn btn-warning btn-sm">
      <i class="fa fa-edit"></i>
    </a>
  </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php else: ?>

<div class="text-center" style="padding:60px;">
  <i class="fa fa-bullhorn" style="font-size:85px;color:#334155;"></i>
  <h3 style="font-weight:900;margin-top:20px;">No tienes campañas creadas</h3>
  <p style="color:#94a3b8;">Crea una campaña para motivar rentas en temporada baja.</p>

  <a href="./?view=raffle&opt=new" class="btn btn-primary">
    Crear Campaña
  </a>
</div>

<?php endif; ?>

</div>
</div>

<?php elseif($opt=="new"): ?>

<div class="row mb-3">
  <div class="col-md-12">
    <h1 style="color:white;font-weight:900;">
      <i class="fa fa-plus"></i> Nueva Campaña
    </h1>
  </div>

</div>

<div class="card" style="background:#111827;color:white;border-radius:18px;">
<div class="card-body">

<form method="post" action="./?action=raffle&opt=add" id="campaignForm">

<div class="row">

  <div class="col-md-12 mb-3">  
    <label>Título de la campaña *</label>
    <input type="text" name="title" class="form-control" required
           value="Renta y gana tanque full">
  </div>

  <div class="col-md-6 mb-3">
    <label>Fecha inicio *</label>
    <input type="date" name="start_date" class="form-control" required>
  </div>

  <div class="col-md-6 mb-3">
    <label>Fecha final *</label>
    <input type="date" name="end_date" class="form-control" required>
  </div>

  <div class="col-md-6 mb-3">
    <label>Días mínimos de renta *</label>
    <input type="number" name="min_rental_days" class="form-control" value="3" min="1" required>
  </div>

  <div class="col-md-6 mb-3">
    <label>Cantidad de ganadores *</label>
    <input type="number" name="winners_limit" class="form-control" value="3" min="1" required>
  </div>

  <div class="col-md-12 mb-3">
    <label>Premio principal *</label>
    <input type="text" name="prize_description" class="form-control" required
           placeholder="Ej: Tanque lleno / Fin de semana gratis / 7 días gratis">
  </div>

  <div class="col-md-12 mb-3">
    <label>Reglas de la campaña *</label>
    <textarea name="rule_description" class="form-control" rows="4" required
    placeholder="Ej: Quienes renten 3 días o más entre estas fechas participan automáticamente.">Quienes renten 3 días o más entre estas fechas participan automáticamente.</textarea>
  </div>

  <div class="col-md-12 mb-3">
    <label>Descripción para el flyer</label>
    <textarea name="description" class="form-control" rows="4"
    placeholder="Ej: Reserva ahora y participa automáticamente.">Reserva ahora y participa automáticamente.</textarea>
  </div>

</div>

<div class="text-center mt-4">
  <button type="submit" class="btn btn-success btn-lg" id="saveBtn" style="border-radius:14px;font-weight:900;">
    GUARDAR CAMPAÑA
  </button>
</div>

</form>

</div>
</div>

<script>
document.getElementById("campaignForm").addEventListener("submit", function(e){
  var btn = document.getElementById("saveBtn");

  if(btn.dataset.loading == "1"){
    e.preventDefault();
    return false;
  }

  btn.dataset.loading = "1";
  btn.disabled = true;
  btn.innerHTML = "GUARDANDO...";
});
</script>


<?php elseif($opt=="flyer"): ?>

<?php
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$sql = "
SELECT *
FROM raffles
WHERE id='$id'
AND stock_id='$stock_id'
LIMIT 1
";
$query = $con->query($sql);
$raffle = ($query && $query->num_rows > 0) ? $query->fetch_assoc() : null;

$stock = StockData::getPrincipal();

$company_name      = $stock->name;
$company_phone     = $stock->phone;
$company_phone2    = $stock->phone2;
$company_address   = $stock->address;
$company_instagram = $stock->field2;
$company_rnc       = $stock->rnc;
$company_logo      = $stock->ticket_image;


$color = explode(",", $stock->color);

$primary   = isset($color[0]) && trim($color[0]) != "" ? trim($color[0]) : "#0d47a1";
$secondary = isset($color[1]) && trim($color[1]) != "" ? trim($color[1]) : "#1d4ed8";
$accent    = isset($color[2]) && trim($color[2]) != "" ? trim($color[2]) : "#f59e0b";

$logo_path = "";

if(!empty($company_logo)){

    $logo_file = basename($company_logo);

    $possible_logos = array(
        "CF-SYSTEMS/storage/configuration/".$logo_file,
        "../CF-SYSTEMS/storage/configuration/".$logo_file,
        "../../CF-SYSTEMS/storage/configuration/".$logo_file,
        "../../../CF-SYSTEMS/storage/configuration/".$logo_file
    );

    foreach($possible_logos as $p){

        if(file_exists($p)){

            /*
            |--------------------------------------------------------------------------
            | URL PARA EL HTML
            |--------------------------------------------------------------------------
            */
            $logo_path = "./CF-SYSTEMS/storage/configuration/".$logo_file;

            break;
        }
    }
}

$prize = "";

$qp = $con->query("
    SELECT prize_description 
    FROM raffle_prizes 
    WHERE raffle_id = '$id'
    ORDER BY prize_order ASC
    LIMIT 1
");

if($qp && $qp->num_rows > 0){
    $rp = $qp->fetch_assoc();
    $prize = $rp["prize_description"];
}

if($prize == ""){
    $prize = $raffle["description"];
}
?>

<?php if($raffle): ?>

<style>

.flyer-print-box{
  width:1080px;
  height:1080px;
  max-width:100%;
  margin:auto;
  position:relative;
  overflow:hidden;
  color:white;
  font-family:Arial, Helvetica, sans-serif;
}

.flyer-print-box:before{
  content:"";
  position:absolute;
  width:560px;
  height:560px;
  background:gray;
  border-radius:50%;
  top:-180px;
  left:-160px;
}

.flyer-print-box:after{
  content:"";
  position:absolute;
  width:650px;
  height:650px;
  background:gray;
  border-radius:50%;
  right:-250px;
  bottom:-250px;
}

.flyer-card{
  position:absolute;
  inset:65px;
  background:#111827;
  border:2px solid background:rgba(<?php echo $color[0]; ?>,<?php echo $color[1]; ?>,<?php echo $color[2]; ?>,.45);
  box-shadow:0 20px 60px rgba(0,0,0,.55);
  z-index:2;
}

.flyer-header{
  padding:32px 35px 22px 35px;
  border-bottom:2px solid rgba(<?php echo $color[0]; ?>,<?php echo $color[1]; ?>,<?php echo $color[2]; ?>,.45);
  display:flex;
  align-items:center;
  gap:22px;
}

.flyer-logo{
  width:105px;
  height:105px;
  object-fit:contain;
  background:white;
  border-radius:16px;
  padding:8px;
}

.flyer-company{
  font-size:34px;
  font-weight:900;
  text-transform:uppercase;
  line-height:1.1;
}

.flyer-rnc{
  font-size:17px;
  color:#cbd5e1;
  margin-top:8px;
}

.flyer-body{
  padding:28px 35px;
}

.flyer-small-title{
  color:#f59e0b;
  font-size:42px;
  font-weight:900;
  text-transform:uppercase;
  margin-bottom:18px;
}

.flyer-main-title{
  color:#f59e0b;
  font-size:58px;
  font-weight:900;
  text-transform:uppercase;
  line-height:1.05;
  text-shadow:0 4px 8px rgba(0,0,0,.45);
}

.flyer-dates{
  margin-top:25px;
  font-size:30px;
  font-weight:800;
  line-height:1.45;
}

.flyer-line{
  height:2px;
  background:rgba(<?php echo $color[0]; ?>,<?php echo $color[1]; ?>,<?php echo $color[2]; ?>,.55);
  margin:25px 0;
}

.flyer-section-title{
  color:#22c55e;
  font-size:32px;
  font-weight:900;
  text-transform:uppercase;
  margin-bottom:15px;
}

.flyer-rule{
  font-size:25px;
  line-height:1.35;
  font-weight:600;
}

.flyer-rule ul{
  margin:10px 0 0 28px;
  padding:0;
}

.flyer-prize{
  margin-top:28px;
}

.flyer-prize-text{
  color:#f59e0b;
  font-size:34px;
  font-weight:900;
  text-transform:uppercase;
  line-height:1.15;
}

.flyer-info{
  margin-top:22px;
  color:#e5e7eb;
  font-size:19px;
  line-height:1.6;
  text-transform:uppercase;
}

.flyer-footer{
  position:absolute;
  left:0;
  right:0;
  bottom:0;
  background:rgba(<?php echo $color[0]; ?>,<?php echo $color[1]; ?>,<?php echo $color[2]; ?>,1);
  padding:25px 35px;
  z-index:3;
}

.flyer-footer-title{
  font-size:30px;
  font-weight:900;
}

.flyer-footer-phone{
  font-size:32px;
  font-weight:900;
  margin-top:6px;
}

.flyer-footer-address{
  font-size:17px;
  color:#dbeafe;
  margin-top:6px;
}

.flyer-actions{
  text-align:center;
  margin-top:25px;
}

@media print{
  body *{
    visibility:hidden!important;
  }

  #printFlyer, #printFlyer *{
    visibility:visible!important;
  }

  #printFlyer{
    position:absolute!important;
    left:0!important;
    top:0!important;
    width:100%!important;
    height:auto!important;
  }

  .flyer-preview-wrap{
    padding:0!important;
    background:white!important;
    box-shadow:none!important;
  }

  .flyer-print-box{
    width:1080px!important;
    height:1080px!important;
    max-width:none!important;
  }

  .flyer-actions,
  .no-print{
    display:none!important;
  }

  @page{
    size:1080px 1080px;
    margin:0;
  }
}
</style>

<div class="row mb-3 no-print">
  <div class="col-md-12">
    <h1 style="color:white;font-weight:900;">
      <i class="fa fa-image"></i> Flyer de Campaña
    </h1>
  </div>

</div>


<div class="flyer-actions no-print">

  <button onclick="downloadFlyer('instagram')"
          class="btn btn-primary btn-lg"
          style="font-weight:900;border-radius:14px;">
      Descargar Instagram
  </button>

  <button onclick="downloadFlyer('whatsapp')"
          class="btn btn-success btn-lg"
          style="font-weight:900;border-radius:14px;">
      Descargar WhatsApp
  </button>

</div>

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
function downloadFlyer(type){

    let flyer = document.querySelector(".flyer-print-box");

    let scale = 2;

    if(type === "whatsapp"){
        flyer.style.width = "1080px";
        flyer.style.height = "1920px";
    }else{
        flyer.style.width = "1080px";
        flyer.style.height = "1080px";
    }

    html2canvas(flyer,{
        scale: scale,
        useCORS:true,
        backgroundColor:null
    }).then(canvas => {

        let link = document.createElement("a");

        link.download = "flyer_"+type+".png";

        link.href = canvas.toDataURL("image/png");

        link.click();

    });
}
</script>
<br>

<div class="flyer-preview-wrap" id="printFlyer">

  <div class="flyer-print-box">

    <div class="flyer-card">

      <div class="flyer-header">

        <?php if(!empty($logo_path)): ?>
          <img src="<?php echo $logo_path; ?>" class="flyer-logo">
        <?php endif; ?>

        <div>
          <div class="flyer-company">
            <?php echo strtoupper($company_name); ?>
          </div>

          <?php if(!empty($company_rnc)): ?>
            <div class="flyer-rnc">RNC: <?php echo $company_rnc; ?></div>
          <?php endif; ?>
        </div>

      </div>

      <div class="flyer-body">

        <div class="flyer-small-title">
          Renta y gana
        </div>

        <div class="flyer-main-title">
          <?php echo strtoupper($prize); ?>
        </div>

        <div class="flyer-dates">
          DESDE: <?php echo date("d", strtotime($raffle["start_date"])); ?> de <?php echo strftime("%B", strtotime($raffle["start_date"])); ?> de <?php echo date("Y", strtotime($raffle["start_date"])); ?><br>
          HASTA: <?php echo date("d", strtotime($raffle["end_date"])); ?> de <?php echo strftime("%B", strtotime($raffle["end_date"])); ?> de <?php echo date("Y", strtotime($raffle["end_date"])); ?>
        </div>

        <div class="flyer-line"></div>

        <div class="flyer-section-title">
          Regla del sorteo:
        </div>

        <div class="flyer-rule">
          ¡Es muy sencillo! Participas automáticamente si cumples con lo siguiente:
          <ul>
            <li><b>Renta mínima:</b> <?php echo intval($raffle["min_rental_days"]); ?> días o más.</li>
            <li><?php echo $raffle["rule_description"]; ?></li>
          </ul>
        </div>

        <div class="flyer-prize">
          <div class="flyer-section-title">
            Premio:
          </div>

          <div class="flyer-prize-text">
            <?php echo intval($raffle["winners_limit"]); ?> ganadores de <?php echo strtoupper($prize); ?>
          </div>
        </div>

        <div class="flyer-info">
          Días mínimos de renta: <?php echo intval($raffle["min_rental_days"]); ?><br>
          Ganadores: <?php echo intval($raffle["winners_limit"]); ?>
        </div>

      </div>

      <div class="flyer-footer">
        <div class="flyer-footer-title">RESERVA AHORA</div>

        <div class="flyer-footer-phone">
          <?php echo trim($company_phone." ".$company_phone2); ?>
        </div>

        <?php if(!empty($company_address)): ?>
          <div class="flyer-footer-address">
            <?php echo strtoupper($company_address); ?>
          </div>
        <?php endif; ?>

        <?php if(!empty($company_instagram)): ?>
          <div class="flyer-footer-address">
            Instagram: <?php echo $company_instagram; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>

  </div>

</div>


<?php else: ?>

<div class="alert alert-danger">Campaña no encontrada.</div>

<?php endif; ?>

<?php endif; ?>

</div>
</section>