<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
  <div class="row">
    <div class="col-md-12">

      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><i class='fa fa-key'></i> Control Llaves</h1>
            </div>

            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active">
                  <i class='fa fa-history'></i> 
                  <span id="reloj"></span>
                </li>
              </ol>
            </div>
          </div>

          <script>
          function actualizarReloj() {
            const reloj = document.getElementById("reloj");
            if (!reloj) return;

            const ahora = new Date();
            const horas = String(ahora.getHours()).padStart(2, '0');
            const minutos = String(ahora.getMinutes()).padStart(2, '0');
            const segundos = String(ahora.getSeconds()).padStart(2, '0');
            reloj.textContent = `${horas}:${minutos}:${segundos}`;
          }

          document.addEventListener("DOMContentLoaded", function () {
            actualizarReloj();
            setInterval(actualizarReloj, 1000);
          });
          </script>

          <div class="card" style="background-color:#222;">
            <div class="card-body">

              <form class="form-horizontal" method="post" id="add" role="form">
                <div class="row">

                  <div class="col-md-6 col-6">
                    <select style="background-color:#333;" name="car_id" class="form-control select2" required>
                      <option value="">Seleccionar vehículo</option>
                      <?php foreach(CarsData::getAll() as $client):?>
                        <option value="<?php echo (int)$client->id;?>">
                          <?php
                            echo htmlspecialchars(
                              $client->getBrand()->name." ".
                              $client->name." ".
                              $client->year." ".
                              $client->getExColor()->name." - ".
                              $client->chassis,
                              ENT_QUOTES,
                              'UTF-8'
                            );
                          ?>
                        </option>
                      <?php endforeach;?>
                    </select>
                  </div>
                  
                  <div class="col-md-3 col-3">
                    <select style="background-color:#333;" name="user_id" class="form-control select2" required>
                      <option value="">Seleccionar empleado</option>
                      <?php foreach(UserData::getAll() as $users):?>
                        <option value="<?php echo (int)$users->id;?>">
                          <?php echo htmlspecialchars($users->name, ENT_QUOTES, 'UTF-8');?>
                        </option>
                      <?php endforeach;?>
                    </select>
                  </div>
                  
                  <div class="col-md-3 col-3">
                    <select style="background-color:#333;" name="type_id" class="form-control" required>
                      <option value="Entregado">Entregado</option>
                      <option value="Recibido">Recibido</option>
                    </select>
                  </div>

                  <div class="col-md-12 col-12">
                    <label class="col-md-12 col-12 control-label"></label>
                    <button class="btn btn-warning btn-block btn-sm" id="btn-add-place" type="submit">
                      <i class="fa fa-check"></i> Finalizar
                    </button>
                  </div>
                  
                </div>
              </form>

              <script>
              function guardarkayOffline(kay) {
                let pendientes = JSON.parse(localStorage.getItem("kayes_pendientes")) || [];
                pendientes.push(kay);
                localStorage.setItem("kayes_pendientes", JSON.stringify(pendientes));
                if (typeof toastr !== "undefined") {
                  toastr.info('Guardado localmente. Se enviará cuando vuelva el internet.');
                }
              }

              function sincronizarkayes() {
                let pendientes = JSON.parse(localStorage.getItem("kayes_pendientes")) || [];
                if (pendientes.length > 0 && navigator.onLine) {
                  let nuevosPendientes = [...pendientes];

                  pendientes.forEach((kay, i) => {
                    fetch("./?action=key&opt=add_offline", {
                      method: "POST",
                      headers: { "Content-Type": "application/json" },
                      body: JSON.stringify(kay)
                    })
                    .then(res => res.text())
                    .then(resp => {
                      resp = resp.trim();

                      if (resp === "OK" || resp === "UPDATED") {
                        nuevosPendientes.splice(i, 1);
                        localStorage.setItem("kayes_pendientes", JSON.stringify(nuevosPendientes));
                      } else {
                        console.log("No se puede duplicar. Ya existe.");
                      }
                    })
                    .catch(err => {
                      console.log("Error sincronizando:", err);
                    });
                  });
                }
              }

              document.addEventListener("DOMContentLoaded", function () {
                const form = document.getElementById("add");
                if (!form) return;

                form.addEventListener("submit", function(e) {
                  e.preventDefault();

                  const car_id  = this.car_id.value.trim();
                  const user_id = this.user_id.value.trim();
                  const type_id = this.type_id.value.trim();

                  if (!car_id || !user_id || !type_id) {
                    if (typeof toastr !== "undefined") {
                      toastr.error("Todos los campos son obligatorios");
                    } else {
                      alert("Todos los campos son obligatorios");
                    }
                    return;
                  }

                  const kay = {
                    car_id: car_id,
                    user_id: user_id,
                    type_id: type_id
                  };

                  if (navigator.onLine) {
                    const formData = new FormData(this);

                    fetch("./?action=key&opt=add", {
                      method: "POST",
                      body: formData
                    })
                    .then(res => res.text())
                    .then(resp => {
                      resp = resp.trim();

                      if (resp === "OK" || resp === "UPDATED") {
                        if (typeof toastr !== "undefined") {
                          toastr.success('Registro procesado correctamente.');
                        }
                        window.location.href = "./?view=key&opt=all";
                      } else {
                        if (typeof toastr !== "undefined") {
                          toastr.warning('Ya existe ese registro. Guardado offline.');
                        }
                        guardarkayOffline(kay);
                      }
                    })
                    .catch(() => {
                      guardarkayOffline(kay);
                    });
                  } else {
                    guardarkayOffline(kay);
                  }
                });

                setInterval(() => {
                  if (navigator.onLine) {
                    sincronizarkayes();
                  }
                }, 5000);
              });
              </script>

            </div>
          </div>

          <?php 
          $base = new Database();
          $con = $base->connect();
          $sql = "SELECT SQL_BIG_RESULT * FROM kay";
          $query = $con->query($sql);
          ?>

          <?php if($query && $query->num_rows > 0):?>
            <div class="card" style="background-color:#222;">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered" id="example1">
                    <thead>
                      <tr>
                        <th>Vehiculo</th>
                        <th>Empleado</th>
                        <th>Estatus</th>
                      </tr>
                    </thead>

                    <tfoot>
                      <tr>
                        <th>Vehiculo</th>
                        <th>Empleado</th>
                        <th>Estatus</th>
                      </tr>
                    </tfoot>

                    <tbody>
                      <?php while($r = $query->fetch_assoc()): ?>
                        <?php
                        $client = CarsData::getById($r["car_id"]);
                        $users  = UserData::getById($r["user_id"]);

                        $vehiculo = "VEHICULO NO ENCONTRADO";
                        $empleado = "USUARIO NO ENCONTRADO";

                        if($client){
                          $brandName = ($client->getBrand() ? $client->getBrand()->name : '');
                          $colorName = ($client->getExColor() ? $client->getExColor()->name : '');

                          $vehiculo = strtoupper(trim(
                            $brandName." ".
                            $client->name." ".
                            $client->year." ".
                            $colorName." - ".
                            $client->chassis
                          ));
                        }

                        if($users){
                          $empleado = strtoupper($users->name);
                        }
                        ?>
                        <tr>
                          <td><?php echo htmlspecialchars($vehiculo, ENT_QUOTES, 'UTF-8'); ?></td>
                          <td><?php echo htmlspecialchars($empleado, ENT_QUOTES, 'UTF-8'); ?></td>
                          <td><?php echo htmlspecialchars(strtoupper($r['type_id']), ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          <?php else:?>
            <div class="card">
              <div class="card-header">
                <h2>No hay kay</h2>
                <p>No se ha realizado ninguna operacion.</p>
              </div>
            </div>
          <?php endif;?>

        </div>
      </div>

    </div>
  </div>
</section>
<?php endif; ?>