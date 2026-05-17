<?php
// Genera tareas AUTO si aplican (no duplica)
if(isset($_SESSION["user_id"])){
  $db  = new Database();
  $con = $db->connect();
  $stock_id = intval(StockData::getPrincipal()->id);

  $hoy = date("Y-m-d");

  // Ajustables
  $idle_days = 5;
  $umbral_gasto_hoy = 5000;
  $ratio_income_low = 60; // % hoy vs promedio 7 días

  // Helper para insertar auto sin duplicar
  $addAuto = function($source_key,$ref_table,$ref_id,$title,$desc,$priority,$due_days=0) use ($stock_id){
    $exists = TaskData::existsAuto($stock_id,$source_key,$ref_table,$ref_id);
    if($exists) return;

    $t = new TaskData();
    $t->stock_id = $stock_id;
    $t->user_id = $_SESSION["user_id"];
    $t->source_type = "AUTO";
    $t->source_key  = $source_key;
    $t->ref_table   = $ref_table;
    $t->ref_id      = intval($ref_id);
    $t->title = addslashes($title);
    $t->description = addslashes($desc);
    $t->priority = $priority;
    $t->status = "PENDIENTE";
    $t->due_date = ($due_days>0 ? date("Y-m-d", strtotime("+$due_days day")) : NULL);
    $t->add();
  };

  // 1) Deudas (top 6)
  $sql_deuda = "
    SELECT 
      b.id,
      (IFNULL(b.total,0) - IFNULL(p.pagado,0)) deuda
    FROM booking b
    LEFT JOIN (
      SELECT booking_id, SUM(val) pagado
      FROM payment
      WHERE stock_id = $stock_id
      GROUP BY booking_id
    ) p ON p.booking_id = b.id
    WHERE b.stock_id = $stock_id
      AND b.status IN (0,1)
      AND (IFNULL(b.total,0) - IFNULL(p.pagado,0)) > 0
    ORDER BY deuda DESC
    LIMIT 6
  ";
  $q = $con->query($sql_deuda);
  if($q){
    while($r=$q->fetch_assoc()){
      $deuda = floatval($r["deuda"]);
      $prio = ($deuda>=15000 ? "ALTA" : ($deuda>=5000 ? "MEDIA" : "BAJA"));
      $addAuto(
        "DEUDA","booking",$r["id"],
        "Cobrar reserva #".$r["id"]." (deuda pendiente)",
        "La reserva #".$r["id"]." tiene deuda pendiente. Acción: cobrar / registrar pago / cerrar balance.",
        $prio, 1
      );
    }
  }

  // 2) Vehículos parados (cars)
  $sql_idle = "
    SELECT 
      c.id,
      c.name,
      c.plate,
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
  $qi = $con->query($sql_idle);
  if($qi){
    while($r=$qi->fetch_assoc()){
      $last = $r["last_move"];
      $days = 9999;
      if($last){ $days = (int) floor((time() - strtotime($last)) / 86400); }
      if($days >= $idle_days){
        $carLabel = trim(($r["name"] ?? "")." ".($r["plate"] ? "(".$r["plate"].")" : ""));
        $prio = ($days>=15 ? "ALTA" : ($days>=10 ? "MEDIA" : "BAJA"));
        $addAuto(
          "IDLE","cars",$r["id"],
          "Mover vehículo parado: ".$carLabel,
          "Este vehículo lleva ".$days." día(s) sin movimiento. Acción: promoción / ajustar tarifa / rotar disponibilidad.",
          $prio, 2
        );
      }
    }
  }

  // 3) Mantenimientos abiertos (count)
  $sql_m = "SELECT COUNT(*) c FROM maintenance WHERE stock_id=$stock_id AND status=0";
  $qm = $con->query($sql_m);
  $maint_open = ($qm ? intval($qm->fetch_assoc()["c"]) : 0);
  if($maint_open > 0){
    $prio = ($maint_open>=5 ? "ALTA" : ($maint_open>=3 ? "MEDIA" : "BAJA"));
    // ref_id 0 para "global"
    $addAuto(
      "MAINT","maintenance",0,
      "Cerrar mantenimientos abiertos (".$maint_open.")",
      "Hay ".$maint_open." mantenimiento(s) abierto(s). Acción: cerrar órdenes para liberar flota y reducir riesgo.",
      $prio, 1
    );
  }

  // 4) Ingresos bajos hoy vs promedio 7 días (payment)
  $income_today = 0; $avg7 = 0;
  $sql_it = "SELECT IFNULL(SUM(val),0) t FROM payment WHERE stock_id=$stock_id AND DATE(created_at)='$hoy'";
  $qit = $con->query($sql_it);
  $income_today = ($qit ? floatval($qit->fetch_assoc()["t"]) : 0);

  $sql_7 = "
    SELECT DATE(created_at) d, IFNULL(SUM(val),0) t
    FROM payment
    WHERE stock_id=$stock_id
      AND DATE(created_at) >= DATE_SUB('$hoy', INTERVAL 7 DAY)
      AND DATE(created_at) <= DATE_SUB('$hoy', INTERVAL 1 DAY)
    GROUP BY d
  ";
  $q7 = $con->query($sql_7);
  if($q7){
    $sum=0;$cnt=0;
    while($r=$q7->fetch_assoc()){ $sum += floatval($r["t"]); $cnt++; }
    $avg7 = ($cnt>0 ? ($sum/$cnt) : 0);
  }

  if($avg7 > 0){
    $ratio = ($income_today / $avg7) * 100;
    if($ratio < $ratio_income_low){
      $prio = ($ratio<40 ? "ALTA" : "MEDIA");
      $addAuto(
        "LOW_INCOME","payment",0,
        "Revisar ingresos bajos hoy (".round($ratio)."%)",
        "Hoy: ".Core::$symbol." ".number_format($income_today,2,".",",")." vs Promedio 7 días: ".Core::$symbol." ".number_format($avg7,2,".",",").". Acción: reforzar cobranza + ofertas.",
        $prio, 0
      );
    }
  }

  // 5) Gastos altos hoy (spends+maintenance+fuels+toll)
  $sql_g = "
    SELECT
      (SELECT IFNULL(SUM(price),0) FROM spends WHERE stock_id=$stock_id AND DATE(created_at)='$hoy') AS spends,
      (SELECT IFNULL(SUM(price),0) FROM maintenance WHERE stock_id=$stock_id AND DATE(created_at)='$hoy') AS maint,
      (SELECT IFNULL(SUM(price),0) FROM fuels WHERE stock_id=$stock_id AND DATE(created_at)='$hoy') AS fuel,
      (SELECT IFNULL(SUM(price),0) FROM toll WHERE stock_id=$stock_id AND DATE(created_at)='$hoy') AS tolls
  ";
  $qg = $con->query($sql_g);
  if($qg){
    $g = $qg->fetch_assoc();
    $gasto_hoy = floatval($g["spends"]) + floatval($g["maint"]) + floatval($g["fuel"]) + floatval($g["tolls"]);
    if($gasto_hoy >= $umbral_gasto_hoy){
      $prio = ($gasto_hoy >= ($umbral_gasto_hoy*2) ? "ALTA" : "MEDIA");
      $addAuto(
        "HIGH_EXP","spends",0,
        "Validar gastos altos hoy",
        "Gastos de hoy: ".Core::$symbol." ".number_format($gasto_hoy,2,".",",").". Acción: validar soportes / motivo / ajustar control.",
        $prio, 0
      );
    }
  }

  Core::redir("./?view=tasks&opt=all");
}
?>
