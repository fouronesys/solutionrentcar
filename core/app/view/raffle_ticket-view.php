<?php
if(!isset($_SESSION)){
    session_start();
}

$base = new Database();
$con = $base->connect();

$stock_id = isset($_SESSION["stock_id"]) ? intval($_SESSION["stock_id"]) : 1;

$raffle_id = isset($_GET["raffle_id"]) ? intval($_GET["raffle_id"]) : 0;

$where = "WHERE rt.stock_id = '$stock_id'";

if($raffle_id > 0){
    $where .= " AND rt.raffle_id = '$raffle_id'";
}

$sql_raffles = "
SELECT id, title 
FROM raffles 
WHERE stock_id = '$stock_id'
ORDER BY id DESC
";

$query_raffles = $con->query($sql_raffles);

$sql = "
SELECT 
    rt.*,
    r.title AS raffle_title,
    r.start_date,
    r.end_date,
    r.min_rental_days,
    r.winners_limit,
    p.name AS client_name,
    p.phone AS client_phone,
    c.name AS car_name,
    c.plate AS car_plate
FROM raffle_tickets rt
LEFT JOIN raffles r ON r.id = rt.raffle_id
LEFT JOIN person p ON p.id = rt.person_id
LEFT JOIN cars c ON c.id = rt.car_id
$where
ORDER BY rt.is_winner DESC, rt.winner_position ASC, rt.rental_days DESC, rt.id ASC
";

$query = $con->query($sql);
?>

<section class="content">
<div class="container-fluid">

<div class="row mb-3">
  <div class="col-md-12">
    <h1 style="color:white;font-weight:900;">
      <i class="fa fa-users"></i> Ganadores de Sorteos
    </h1>
  </div>

</div>

<div class="card mb-3" style="background:#111827;color:white;border-radius:18px;">
<div class="card-body">

<form method="get">
  <input type="hidden" name="view" value="raffle_ticket">

  <div class="row">

    <div class="col-md-8 mb-2">
      <label>Campaña / Sorteo</label>

      <select name="raffle_id" class="form-control">
        <option value="0">Todas las campañas</option>

        <?php if($query_raffles && $query_raffles->num_rows > 0): ?>
          <?php while($rf = $query_raffles->fetch_assoc()): ?>
            <option value="<?php echo $rf["id"]; ?>" <?php if($raffle_id == $rf["id"]) echo "selected"; ?>>
              <?php echo strtoupper($rf["title"]); ?>
            </option>
          <?php endwhile; ?>
        <?php endif; ?>
      </select>
    </div>

    <div class="col-md-4 mb-2">
      <label>&nbsp;</label>
      <button class="btn btn-primary btn-block">
        <i class="fa fa-search"></i> Filtrar
      </button>
    </div>

  </div>
</form>

</div>
</div>

<?php if($raffle_id > 0): ?>

<div class="card mb-3" style="background:#0f172a;color:white;border-radius:18px;">
<div class="card-body text-center">

  <h4 style="font-weight:900;">
    Generar participantes automáticamente desde las rentas
  </h4>

  <p style="color:#94a3b8;">
    El sistema buscará las rentas que cumplan con el rango de fecha y días mínimos.
  </p>

  <a href="./?action=raffle_ticket&opt=generate&raffle_id=<?php echo $raffle_id; ?>" 
     class="btn btn-success btn-lg"
     onclick="return confirm('¿Deseas generar los participantes y ganadores de esta campaña?');"
     style="border-radius:14px;font-weight:900;">
    <i class="fa fa-random"></i> GENERAR PARTICIPANTES / GANADORES
  </a>

</div>
</div>

<?php endif; ?>

<div class="card" style="background:#111827;color:white;border-radius:18px;">
<div class="card-body table-responsive">

<?php if($query && $query->num_rows > 0): ?>

<table class="table table-bordered table-hover" style="color:white;">
<thead style="background:#020617;">
<tr>
  <th>#</th>
  <th>Campaña</th>
  <th>Cliente</th>
  <th>Teléfono</th>
  <th>Vehículo</th>
  <th>Renta</th>
  <th>Días</th>
  <th>Estado</th>
  <th>Premio</th>
  <th>Fecha</th>
</tr>
</thead>

<tbody>

<?php while($t = $query->fetch_assoc()): ?>

<tr>

  <td><?php echo $t["id"]; ?></td>

  <td>
    <strong><?php echo strtoupper($t["raffle_title"]); ?></strong><br>
    <small style="color:#94a3b8;">
      <?php echo $t["start_date"]; ?> al <?php echo $t["end_date"]; ?>
    </small>
  </td>

  <td>
    <?php echo strtoupper($t["client_name"] ?? $t["name"] ?? ""); ?>
  </td>

  <td>
    <?php echo $t["client_phone"] ?? $t["phone"] ?? ""; ?>
  </td>

  <td>
    <?php echo strtoupper($t["car_name"] ?? ""); ?><br>
    <small style="color:#94a3b8;">
      <?php echo $t["car_plate"] ?? ""; ?>
    </small>
  </td>

  <td>
    #<?php echo intval($t["booking_id"]); ?>
  </td>

  <td>
    <span class="badge badge-info">
      <?php echo intval($t["rental_days"]); ?> días
    </span>
  </td>

  <td>
    <?php if(intval($t["is_winner"]) == 1): ?>
      <span class="badge badge-success">
        Ganador #<?php echo intval($t["winner_position"]); ?>
      </span>
    <?php else: ?>
      <span class="badge badge-secondary">
        Participante
      </span>
    <?php endif; ?>
  </td>

  <td>
    <?php echo !empty($t["prize_awarded"]) ? strtoupper($t["prize_awarded"]) : "-"; ?>
  </td>

  <td>
    <?php echo $t["created_at"]; ?>
  </td>

</tr>

<?php endwhile; ?>

</tbody>
</table>

<?php else: ?>

<div class="text-center" style="padding:60px;">
  <i class="fa fa-users" style="font-size:85px;color:#334155;"></i>

  <h3 style="font-weight:900;margin-top:20px;">
    No hay participantes generados
  </h3>

  <p style="color:#94a3b8;">
    Selecciona una campaña y presiona “Generar participantes / ganadores”.
  </p>
</div>

<?php endif; ?>

</div>
</div>

</div>
</section>