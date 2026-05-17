<?php
// ======================================================
// 🧠 SCORE INTELIGENTE DE CLIENTES
// ======================================================

$db  = new Database();
$con = $db->connect();

$stock_id = intval(StockData::getPrincipal()->id);

// detectar columnas payment
$payment_has_person = false;
$payment_has_booking = false;

$rpay = $con->query("SHOW COLUMNS FROM payment");

if($rpay){
  while($c = $rpay->fetch_assoc()){

    if($c["Field"]=="person_id"){
      $payment_has_person = true;
    }

    if($c["Field"]=="booking_id"){
      $payment_has_booking = true;
    }

  }
}

// ======================================================
// CLIENTES
// ======================================================

$sql_clients = "
SELECT 
p.id,
p.name,
p.phone,
p.no,
p.license_date,
p.passport_date,
p.home_date,

(
  SELECT COUNT(*)
  FROM booking b
  WHERE b.person_id=p.id
  AND b.stock_id=$stock_id
) total_bookings,

(
  SELECT IFNULL(SUM(total),0)
  FROM booking b
  WHERE b.person_id=p.id
  AND b.stock_id=$stock_id
) total_reserved

FROM person p
WHERE p.stock_id=$stock_id
ORDER BY p.id DESC
LIMIT 20
";

$q_clients = $con->query($sql_clients);

?>

<style>
.rt-client-card{
  background:#16181d;
  border-radius:18px;
  border:1px solid rgba(255,255,255,.08);
  padding:18px;
  margin-bottom:15px;
  box-shadow:0 10px 28px rgba(0,0,0,.25);
}

.rt-client-name{
  color:#fff;
  font-size:20px;
  font-weight:900;
}

.rt-client-meta{
  color:#9aa0a6;
  font-size:13px;
  font-weight:800;
}

.rt-score{
  font-size:35px;
  font-weight:900;
}

.rt-score-good{
  color:#2ecc71;
}

.rt-score-mid{
  color:#f1c40f;
}

.rt-score-bad{
  color:#e74c3c;
}

.rt-mini{
  background:#0f1115;
  border-radius:14px;
  border:1px solid rgba(255,255,255,.06);
  padding:12px;
  margin-top:10px;
}

.rt-mini-title{
  color:#9aa0a6;
  font-size:11px;
  font-weight:900;
  text-transform:uppercase;
}

.rt-mini-value{
  color:#fff;
  font-size:17px;
  font-weight:900;
  margin-top:3px;
}

.rt-risk-box{
  background:#0b0d10;
  border-radius:12px;
  padding:12px;
  margin-top:12px;
  border:1px solid rgba(255,255,255,.05);
}

.rt-risk-title{
  color:#fff;
  font-weight:900;
}

.rt-risk-body{
  color:#cfcfcf;
  font-weight:700;
  margin-top:4px;
}

.rt-badge{
  padding:5px 10px;
  border-radius:999px;
  font-size:11px;
  font-weight:900;
}

.rt-green{
  background:#2ecc71;
  color:#fff;
}

.rt-yellow{
  background:#f1c40f;
  color:#111;
}

.rt-red{
  background:#e74c3c;
  color:#fff;
}
</style>

<div class="card" style="background:#16181d;border-radius:20px;">
<div class="card-header" style="border-bottom:1px solid rgba(255,255,255,.08);">

<h3 style="font-weight:900;color:#fff;margin:0;">
🧠 Inteligencia de Clientes
</h3>

<div style="color:#9aa0a6;font-weight:700;margin-top:5px;">
Evaluación automática de riesgo, pagos y comportamiento
</div>

</div>

<div class="card-body">

<?php

if($q_clients && $q_clients->num_rows>0):

while($c = $q_clients->fetch_assoc()):

$person_id = intval($c["id"]);

// ======================================================
// PAGOS
// ======================================================

$total_paid = 0;

if($payment_has_person){

  $sql_paid = "
  SELECT IFNULL(SUM(val),0) total
  FROM payment
  WHERE stock_id=$stock_id
  AND person_id=$person_id
  ";

}else if($payment_has_booking){

  $sql_paid = "
  SELECT IFNULL(SUM(p.val),0) total
  FROM payment p
  INNER JOIN booking b ON b.id=p.booking_id
  WHERE b.person_id=$person_id
  AND p.stock_id=$stock_id
  ";

}else{

  $sql_paid = null;

}

if($sql_paid){

  $q_paid = $con->query($sql_paid);

  if($q_paid){
    $total_paid = floatval($q_paid->fetch_assoc()["total"]);
  }

}

// ======================================================
// DEUDA
// ======================================================

$total_reserved = floatval($c["total_reserved"]);

$debt = $total_reserved - $total_paid;

if($debt < 0){
  $debt = 0;
}

// ======================================================
// SCORE
// ======================================================

$score = 100;

// deuda
if($debt > 0){

  if($debt >= 50000){
    $score -= 50;
  }
  elseif($debt >= 20000){
    $score -= 35;
  }
  elseif($debt >= 5000){
    $score -= 20;
  }
  else{
    $score -= 10;
  }

}

// reservas
$total_bookings = intval($c["total_bookings"]);

if($total_bookings >= 10){
  $score += 10;
}
elseif($total_bookings >= 5){
  $score += 5;
}

// documentos vencidos
$today = date("Y-m-d");

$expired = 0;

if(!empty($c["license_date"]) && $c["license_date"] < $today){
  $expired++;
}

if(!empty($c["passport_date"]) && $c["passport_date"] < $today){
  $expired++;
}

if(!empty($c["home_date"]) && $c["home_date"] < $today){
  $expired++;
}

$score -= ($expired * 10);

if($score > 100){
  $score = 100;
}

if($score < 0){
  $score = 0;
}

// ======================================================
// ESTADO
// ======================================================

if($score >= 80){

  $risk = "Cliente Confiable";
  $risk_class = "rt-green";
  $score_class = "rt-score-good";

  $recommendation = "Aprobación rápida recomendada.";

}
elseif($score >= 60){

  $risk = "Cliente Intermedio";
  $risk_class = "rt-yellow";
  $score_class = "rt-score-mid";

  $recommendation = "Revisar documentos y depósito.";

}
else{

  $risk = "Cliente Riesgoso";
  $risk_class = "rt-red";
  $score_class = "rt-score-bad";

  $recommendation = "Solicitar depósito alto o aprobación manual.";

}

?>

<div class="rt-client-card">

<div class="d-flex justify-content-between align-items-center flex-wrap">

<div>

<div class="rt-client-name">
<?php echo htmlspecialchars($c["name"]); ?>
</div>

<div class="rt-client-meta">
DOC: <?php echo htmlspecialchars($c["no"]); ?>
<?php if(!empty($c["phone"])): ?>
 · TEL: <?php echo htmlspecialchars($c["phone"]); ?>
<?php endif; ?>
</div>

</div>

<div style="text-align:right;">

<div class="rt-score <?php echo $score_class; ?>">
<?php echo $score; ?>
</div>

<div>
<span class="rt-badge <?php echo $risk_class; ?>">
<?php echo $risk; ?>
</span>
</div>

</div>

</div>

<div class="row">

<div class="col-md-3">

<div class="rt-mini">

<div class="rt-mini-title">
Reservas
</div>

<div class="rt-mini-value">
<?php echo $total_bookings; ?>
</div>

</div>

</div>

<div class="col-md-3">

<div class="rt-mini">

<div class="rt-mini-title">
Total Rentado
</div>

<div class="rt-mini-value">
<?php echo Core::$symbol." ".number_format($total_reserved,2); ?>
</div>

</div>

</div>

<div class="col-md-3">

<div class="rt-mini">

<div class="rt-mini-title">
Pagado
</div>

<div class="rt-mini-value">
<?php echo Core::$symbol." ".number_format($total_paid,2); ?>
</div>

</div>

</div>

<div class="col-md-3">

<div class="rt-mini">

<div class="rt-mini-title">
Deuda
</div>

<div class="rt-mini-value" style="color:<?php echo ($debt>0?'#e74c3c':'#2ecc71'); ?>">
<?php echo Core::$symbol." ".number_format($debt,2); ?>
</div>

</div>

</div>

</div>

<?php if($expired>0): ?>

<div class="rt-risk-box">

<div class="rt-risk-title">
⚠️ Documentos vencidos detectados
</div>

<div class="rt-risk-body">
Hay <?php echo $expired; ?> documento(s) vencido(s).
</div>

</div>

<?php endif; ?>

<div class="rt-risk-box">

<div class="rt-risk-title">
🧠 Recomendación Inteligente
</div>

<div class="rt-risk-body">
<?php echo $recommendation; ?>
</div>

</div>

</div>

<?php
endwhile;

else:
?>

<div style="color:#9aa0a6;font-weight:800;">
No hay clientes registrados para evaluar.
</div>

<?php endif; ?>

</div>
</div>