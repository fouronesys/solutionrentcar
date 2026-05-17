<?php
$db  = new Database();
$con = $db->connect();
$stock_id = intval(StockData::getPrincipal()->id);
$mes = date("Y-m");

// ======================================================
// INGRESOS POR VEHÍCULO (basado en pagos reales)
// ======================================================
$sql_income = "
SELECT 
    c.id,
    c.name,
    c.plate,
    IFNULL(SUM(p.val),0) ingresos
FROM cars c
LEFT JOIN booking b ON b.car_id=c.id AND b.stock_id=$stock_id
LEFT JOIN payment p ON p.booking_id=b.id 
    AND p.stock_id=$stock_id
    AND DATE_FORMAT(p.created_at,'%Y-%m')='$mes'
WHERE c.stock_id=$stock_id
GROUP BY c.id
";

$q_income = $con->query($sql_income);

$vehicles = [];

while($r = $q_income->fetch_assoc()){
    $vehicles[] = $r;
}

// Ordenar por ingresos DESC
usort($vehicles,function($a,$b){
    return $b["ingresos"] <=> $a["ingresos"];
});

// Top y Bottom
$top = array_slice($vehicles,0,5);
$bottom = array_slice(array_reverse($vehicles),0,5);

?>

<style>
.rt-block{
  background:#16181d;
  border-radius:20px;
  box-shadow:0 10px 28px rgba(0,0,0,.35);
  border:1px solid rgba(255,255,255,.06);
  padding:20px;
  margin-bottom:20px;
}
.rt-cardx{
  background:#0f1115;
  border-radius:16px;
  padding:15px;
  border:1px solid rgba(255,255,255,.08);
  margin-bottom:12px;
}
.rt-title{
  color:#fff;
  font-weight:900;
  font-size:18px;
}
.rt-money{
  font-weight:900;
  color:#2ecc71;
}
</style>

<section class="content">
<div class="container-fluid">
<br><br>
<h3 style="color:#fff;font-weight:900;">
🏆 Rendimiento de Flotilla (Mes Actual)
</h3>

<!-- ================== TOP VEHÍCULOS ================== -->
<div class="rt-block">
<h4 style="color:#fff;font-weight:900;">🥇 Top 5 Más Rentables</h4>

<?php foreach($top as $v): ?>
<div class="rt-cardx">
  <div class="rt-title">
    <?php echo $v["name"]." (".$v["plate"].")"; ?>
  </div>
  <div class="rt-money">
    <?php echo Core::$symbol." ".number_format($v["ingresos"],2,".",","); ?>
  </div>
</div>
<?php endforeach; ?>

</div>

<!-- ================== BOTTOM VEHÍCULOS ================== -->
<div class="rt-block">
<h4 style="color:#fff;font-weight:900;">📉 Bottom 5 Menor Rendimiento</h4>

<?php foreach($bottom as $v): ?>
<div class="rt-cardx">
  <div class="rt-title">
    <?php echo $v["name"]." (".$v["plate"].")"; ?>
  </div>
  <div style="color:#e74c3c;font-weight:900;">
    <?php echo Core::$symbol." ".number_format($v["ingresos"],2,".",","); ?>
  </div>
</div>
<?php endforeach; ?>

</div>

</div>
</section>