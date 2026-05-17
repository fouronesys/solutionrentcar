<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"): ?>
<section class="content">
  <div class="row">
    <div class="col-md-12">

      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><i class='fa fa-street-view'></i> Listado de Suplidores</h1>
            </div>

            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active">
                  <i class='fa fa-history'></i>
                  <span id="reloj"></span>
                </li>
              </ol>
            </div>

            <script>
              function actualizarReloj() {
                const ahora = new Date();
                const horas = String(ahora.getHours()).padStart(2, '0');
                const minutos = String(ahora.getMinutes()).padStart(2, '0');
                const segundos = String(ahora.getSeconds()).padStart(2, '0');
                const reloj = document.getElementById("reloj");
                if (reloj) {
                  reloj.textContent = `${horas}:${minutos}:${segundos}`;
                }
              }

              setInterval(actualizarReloj, 1000);
              actualizarReloj();
            </script>
          </div>
        </div>
      </div>

      <div class="card" style="background-color:#222;">
        <div class="card-body">

          <?php if(isset($_GET['id']) && is_numeric($_GET["id"])): ?>
            <?php $user = SuppliersData::getById((int)$_GET["id"]); ?>

            <?php if($user): ?>
              <form class="form-horizontal" method="post" id="upd" role="form">
                <div class="row">
                  <div class="col-md-8 col-8">
                    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre</label>
                    <input 
                      style="background-color:#333;" 
                      type="text" 
                      name="name" 
                      autocomplete="off"  
                      value="<?php echo htmlspecialchars((string)$user->name, ENT_QUOTES, 'UTF-8'); ?>"  
                      class="form-control" 
                      placeholder="Nombre del Suplidor"
                      required
                    >
                  </div>

                  <div class="col-md-4 col-4">
                    <label for="inputEmail1" class="col-md-12 col-12 my-3 control-label"></label>
                    <input type="hidden" name="user_id" value="<?php echo (int)$user->id; ?>">
                    <button type="submit" class="btn btn-warning btn-block btn-sm">
                      <i class="fa fa-check"></i> Finalizar
                    </button>
                  </div>
                </div>
              </form>

              <script>
                jQuery(document).ready(function(){
                  jQuery("#upd").submit(function(e){
                    e.preventDefault();
                    var formData = jQuery(this).serialize();

                    $.ajax({
                      type: "POST",
                      url: "./?action=suppliers&opt=upd",
                      data: formData,
                      success: function(html){
                        if(html.trim() === 'true' || html.trim() === 'OK'){
                          $.jGrowl("Suplidor Exito!", { sticky: true });
                          $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                          var delay = 1000;
                          setTimeout(function(){ 
                            window.location = './?view=suppliers&opt=all';
                          }, delay);
                        }else{
                          $.jGrowl("No se Puede duplicar datos", { header: 'Error al Agregar' });
                        }
                      },
                      error: function(){
                        $.jGrowl("Ocurrió un error al actualizar.", { header: 'Error' });
                      }
                    });

                    return false;
                  });
                });
              </script>
            <?php else: ?>
              <div class="alert alert-danger">El suplidor no existe o fue eliminado.</div>
            <?php endif; ?>

          <?php else: ?>
            <form class="form-horizontal" method="post" id="add" role="form">
              <div class="row">
                <div class="col-md-8 col-8">
                  <label class="col-md-12 col-12 control-label">Nombre</label>
                  <input 
                    style="background-color:#333;" 
                    type="text" 
                    name="name" 
                    required 
                    autocomplete="off" 
                    class="form-control" 
                    placeholder="Nombre del Suplidor"
                  >
                </div>
                <div class="col-md-4 col-4">
                  <label class="col-md-12 col-12 my-3 control-label"></label>
                  <button type="submit" class="btn btn-warning btn-block btn-sm">
                    <i class="fa fa-check"></i> Finalizar
                  </button>
                </div>
              </div>
            </form>

            <script>
              function guardarSuplidorOffline(data) {
                let pendientes = JSON.parse(localStorage.getItem("suplidores_pendientes") || "[]");
                pendientes.push(data);
                localStorage.setItem("suplidores_pendientes", JSON.stringify(pendientes));

                if (typeof toastr !== "undefined") {
                  toastr.info('Guardado localmente. Se enviará cuando vuelva el internet.');
                } else {
                  alert('Guardado localmente. Se enviará cuando vuelva el internet.');
                }
              }

              function sincronizarSuplidores() {
                let pendientes = JSON.parse(localStorage.getItem("suplidores_pendientes") || "[]");

                if (pendientes.length > 0 && navigator.onLine) {
                  let nuevosPendientes = [...pendientes];

                  pendientes.forEach((suplidor) => {
                    fetch("./?action=suppliers&opt=add_offline", {
                      method: "POST",
                      headers: { "Content-Type": "application/json" },
                      body: JSON.stringify(suplidor)
                    })
                    .then(res => res.text())
                    .then(resp => {
                      if (resp.trim() === "OK" || resp.trim() === "true") {
                        nuevosPendientes = nuevosPendientes.filter(item => item.name !== suplidor.name);
                        localStorage.setItem("suplidores_pendientes", JSON.stringify(nuevosPendientes));
                      }
                    })
                    .catch(() => {});
                  });
                }
              }

              document.getElementById("add").addEventListener("submit", function(e) {
                e.preventDefault();

                const form = this;
                const data = { name: form.name.value };

                if (navigator.onLine) {
                  fetch("./?action=suppliers&opt=add", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
                    body: new URLSearchParams(data).toString()
                  })
                  .then(res => res.text())
                  .then(resp => {
                    if (resp.trim() === "OK" || resp.trim() === "true") {
                      if (typeof toastr !== "undefined") {
                        toastr.success('Registro agregado correctamente.');
                      }
                      window.location.href = "./?view=suppliers&opt=all";
                    } else {
                      if (typeof toastr !== "undefined") {
                        toastr.error('Ya existe ese registro.');
                      } else {
                        alert('Ya existe ese registro.');
                      }
                    }
                  })
                  .catch(() => guardarSuplidorOffline(data));
                } else {
                  guardarSuplidorOffline(data);
                }
              });

              setInterval(() => {
                if (navigator.onLine) {
                  sincronizarSuplidores();
                }
              }, 5000);

              window.addEventListener("online", sincronizarSuplidores);
            </script>
          <?php endif; ?>

        </div>
      </div>

      <?php
        $base = new Database();
        $con = $base->connect();

        $sql = "SELECT SQL_BIG_RESULT * FROM stock WHERE is_ext=1";
        $query = $con->query($sql);
      ?>

      <?php if($query && $query->num_rows > 0): ?>
        <div class="card" style="background-color:#222;">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" id="example1">
                <thead>
                  <tr>
                    <th>Accion</th>
                    <th>Nombre</th>
                    <th>Accion</th>
                  </tr>
                </thead>

                <tfoot>
                  <tr>
                    <th>Accion</th>
                    <th>Nombre</th>
                    <th>Accion</th>
                  </tr>
                </tfoot>

                <tbody>
                  <?php while($r = $query->fetch_assoc()): ?>
                    <tr>
                      <td class="text-right py-0 align-middle">
                        <div class="btn-group btn-group-sm btn-block">
                          <a href="./?view=suppliers&opt=all&id=<?php echo (int)$r['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i>
                          </a>
                        </div>
                      </td>

                      <td><?php echo htmlspecialchars((string)$r['name'], ENT_QUOTES, 'UTF-8'); ?></td>

                      <td class="text-right py-0 align-middle">
                        <?php
                          $permisoEliminar = false;
                          if(isset($_SESSION["user_id"]) && is_numeric($_SESSION["user_id"])) {
                            $user_id = (int)$_SESSION["user_id"];
                            $sl = "SELECT SQL_BIG_RESULT * FROM permits_user WHERE user_id=".$user_id;
                            $qry = $con->query($sl);

                            if($qry && $qry->num_rows > 0) {
                              while($x = $qry->fetch_assoc()) {
                                if(isset($x['permits_id']) && (int)$x['permits_id'] === 4) {
                                  $permisoEliminar = true;
                                  break;
                                }
                              }
                            }
                          }
                        ?>

                        <?php if($permisoEliminar): ?>
                          <a 
                            href="./?action=suppliers&opt=del&id=<?php echo (int)$r['id']; ?>" 
                            class="btn btn-danger btn-block btn-sm" 
                            onclick="return confirmDelete();"
                          >
                            <i class="fas fa-trash"></i> Eliminar
                          </a>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <script>
          function confirmDelete() {
            return confirm("¿Estás seguro de que deseas eliminar este registro?");
          }
        </script>

      <?php else: ?>
        <div class="card" style="background-color:#222;">
          <div class="card-header">
            <h2>No hay Suplidor</h2>
            <p>No se ha realizado ninguna operacion.</p>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</section>
<?php endif; ?>