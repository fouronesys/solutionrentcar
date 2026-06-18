<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<!-- Main content -->
<section class="content">
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fa fa-search"></i> Buscar Disponibilidad</h1>
      </div>
      
     <div class="col-sm-6">
  <ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">
      <i class='fa fa-history'></i> 
      <span id="reloj"></span>
    </li>
  </ol>
</div><!-- /.col -->

<script>
// Función para actualizar la hora en tiempo real
function actualizarReloj() {
  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  document.getElementById("reloj").textContent = `${horas}:${minutos}:${segundos}`;
}

// Actualiza cada segundo
setInterval(actualizarReloj, 1000);
actualizarReloj(); // Llamada inicial
</script>

    </div>

   <!-- Filtros -->
   <form method="get" action="">
     <input type="hidden" name="view" value="available">
     <input type="hidden" name="opt" value="all">

     <div class="row mb-3">
       <div class="col-md-3">
         <label>Fecha Inicio</label>
         <input type="date" class="form-control" name="fecha_inicio"
                value="<?php echo isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d'); ?>">
       </div>
       <div class="col-md-3">
         <label>Fecha Fin</label>
         <input type="date" class="form-control" name="fecha_fin"
                value="<?php echo isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d', strtotime('+1 day')); ?>">
       </div>
       <div class="col-md-3">
         <label>Buscar Vehículo</label>
         <input type="text" class="form-control" name="buscar"
                value="<?php echo isset($_GET['buscar']) ? $_GET['buscar'] : ''; ?>"
                placeholder="Año, marca o modelo">
       </div>
       <div class="col-md-3 d-flex align-items-end">
         <button type="submit" class="btn btn-warning btn-block">Buscar</button>
       </div>
     </div>
   </form>

    <?php
    $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d');
    $fecha_fin    = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d', strtotime('+1 day'));
    $buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

    $base = new Database();
    $con = $base->connect();
    $stock_actual = StockData::getPrincipal()->id;
    ?>

    <div class="row">
      <!-- Global -->
      <div class="col-md-6">
        <div class="card" style="background-color:#222; color:#fff;">
          <div class="card-header">Disponibilidad Global (Todas las Rent Car)</div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-sm">
                <thead>
                  <tr>
                    <th>Vehículo</th>
                    <th>Sucursal</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $base_path = dirname(__DIR__, 4);
                $carpetas = array_filter(glob($base_path . '/*'), function ($dir) {
                    $excluir = ['CF-SYSTEMS','logs'];
                    return is_dir($dir) && !in_array(basename($dir), $excluir);
                });

                $total_autos = 0;
                $total_clientes = 0;

                foreach ($carpetas as $carpeta) {
                    $config_path = $carpeta . '/core/controller/Database.php';
                    if (!file_exists($config_path)) continue;

                    $contenido = file_get_contents($config_path);
                    preg_match('/\$this->host\s*=\s*[\'"](.*?)[\'"]/', $contenido, $host);
                    preg_match('/\$this->user\s*=\s*[\'"](.*?)[\'"]/', $contenido, $user);
                    preg_match('/\$this->pass\s*=\s*[\'"](.*?)[\'"]/', $contenido, $pass);
                    preg_match('/\$this->ddbb\s*=\s*[\'"](.*?)[\'"]/', $contenido, $db);

                    try {
                        $pdo = new PDO("mysql:host={$host[1]};dbname={$db[1]}", $user[1], $pass[1]);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql_global = "
                          SELECT car.plate, car.name AS car_name, car.year,
                                 b.name AS brand_name, s.name AS sucursal, s.phone
                          FROM cars car
                          INNER JOIN brand b ON car.brand_id = b.id
                          INNER JOIN stock s ON car.stock_id = s.id
                          WHERE car.id NOT IN (
                            SELECT bk.car_id
                            FROM booking bk
                            WHERE (DATE(bk.start_at) <= '$fecha_fin'
                              AND DATE(bk.end_at) >= '$fecha_inicio')
                              AND bk.status<>3
                          )
                        ";
                        if($buscar != ''){
                          $sql_global .= " AND (
                            car.year LIKE '%$buscar%' OR
                            car.name LIKE '%$buscar%' OR
                            b.name LIKE '%$buscar%'
                          )";
                        }
                        $sql_global .= " ORDER BY s.name ASC, b.name ASC, car.name ASC";

                        $stmt = $pdo->query($sql_global);
                        $agrupado = [];
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $total_autos++;
                            $agrupado[$row['sucursal']][] = $row;
                        }

                        foreach ($agrupado as $sucursal => $vehiculos) {
                            $total_clientes++;
                            $telefono = preg_replace('/\D/', '', $vehiculos[0]['phone']); // limpiar

                            $mensaje = "Sucursal: $sucursal%0AFechas: $fecha_inicio al $fecha_fin%0A";
                            foreach ($vehiculos as $row) {
                                $mensaje .= "- ".strtoupper($row['brand_name'].' '.$row['car_name'])." (".$row['year'].")";
                            }
                            $wa_link = "https://wa.me/$telefono?text=$mensaje";

                            echo "<tr style='background:#333; color:#FFA500; font-weight:bold'>
                                    <td colspan='2'>
                                      Sucursal: $sucursal (".count($vehiculos)." vehículos)
                                      <span style='float:right;'>
                                        <a href='$wa_link' target='_blank' class='btn btn-success btn-sm'><i class='fab fa-whatsapp'></i> WhatsApp</a>
                                      </span>
                                    </td>
                                  </tr>";
                            foreach ($vehiculos as $row) {
                                echo "<tr>
                                        <td>".strtoupper($row['brand_name'].' '.$row['car_name'])." (".$row['year'].")</td>
                                        <td>".$sucursal."</td>
                                      </tr>";
                            }
                        }

                    } catch (Exception $e) {
                        echo "<tr><td colspan='2'>Error en ".basename($carpeta).": ".$e->getMessage()."</td></tr>";
                    }
                }

                if ($total_autos == 0) {
                    echo "<tr><td colspan='2' class='text-center'>No hay vehículos disponibles</td></tr>";
                }
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Actual -->
      <div class="col-md-6">
        <div class="card" style="background-color:#222; color:#fff;">
          <div class="card-header d-flex justify-content-between">
            <span>Disponibilidad Rent Car Actual</span>
            <?php
            // obtener teléfono de la sucursal actual
            $telefono_actual = '';
            $tel = $con->query("SELECT phone, name FROM stock WHERE id=$stock_actual LIMIT 1");
            if($tel && $rowTel=$tel->fetch_assoc()){
              $telefono_actual = preg_replace('/\D/', '', $rowTel['phone']);
              $nombre_sucursal = $rowTel['name'];
            } else {
              $nombre_sucursal = "Sucursal Actual";
            }
            ?>
            <a href="#" id="wa_actual" target="_blank" class="btn btn-success btn-sm" style="display:none;">
              <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-sm">
                <thead>
                  <tr><th>Vehículo</th></tr>
                </thead>
                <tbody>
                <?php
                $listado_actual = "Sucursal: $nombre_sucursal%0ADisponibilidad del $fecha_inicio al $fecha_fin%0A";
                $sql_actual = "
                  SELECT car.plate, car.name AS car_name, car.year, b.name AS brand_name
                  FROM cars car
                  INNER JOIN brand b ON car.brand_id = b.id
                  WHERE car.stock_id = $stock_actual
                    AND car.id NOT IN (
                      SELECT bk.car_id
                      FROM booking bk
                      WHERE (DATE(bk.start_at) <= '$fecha_fin'
                        AND DATE(bk.end_at) >= '$fecha_inicio')
                        AND bk.status<>3
                    )
                  ORDER BY b.name ASC, car.name ASC
                ";
                $hay_actual = false;
                $query_actual = $con->query($sql_actual);
                if($query_actual && $query_actual->num_rows > 0){
                  while($row = $query_actual->fetch_assoc()){
                    $linea = strtoupper($row['brand_name'].' '.$row['car_name'])." (".$row['year'].")";
                    $listado_actual .= "- $linea%0A";
                    echo "<tr><td>$linea</td></tr>";
                    $hay_actual = true;
                  }
                } else {
                  echo "<tr><td class='text-center'>No hay vehículos disponibles</td></tr>";
                  $listado_actual .= "No hay vehículos disponibles%0A";
                }

                if($hay_actual){
                  echo "<script>
                    document.getElementById('wa_actual').style.display='inline-block';
                    document.getElementById('wa_actual').href='https://wa.me/?text=$listado_actual';
                    </script>";
                }
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /.container-fluid -->
</div>
</section>
<?php endif;?>
