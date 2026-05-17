<?php
$inicio = $_POST["inicio"]; // formato YYYY-MM-DD
$fin    = $_POST["fin"];    // formato YYYY-MM-DD

$base = new Database();
$con = $base->connect();

$sql = "SELECT c.id, c.plate, c.name, c.year
        FROM cars c
        WHERE c.id NOT IN (
            SELECT b.car_id
            FROM booking b
            WHERE 
                (DATE(b.start_at) <= '$fin'
                 AND DATE(b.end_at) >= '$inicio')
                AND b.status IN (1,2)
        )";

$query = $con->query($sql);
$resultado = "";

while($row = $query->fetch_array()){
  $resultado .= "🚗 ".$row['plate']." - ".$row['name']." (".$row['year'].")\n";
}

echo $resultado;
?>
