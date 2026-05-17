<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"): ?>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><i class='fa fa-users'></i> Empleados</h1>
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
<?php $user = UserData::getById((int)$_GET["id"]); ?>

<?php if($user): ?>
<form class="form-horizontal" enctype="multipart/form-data" method="post" id="upd" role="form">
  <div class="row">

    <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
      <label class="col-md-12 col-12 control-label">Genero</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa fa-spinner"></i></span>
        <select style="background-color:#333;" name="gender" class="form-control" required>
          <option value="1" <?php echo (($user->image ?? '')=="man.png" ? "selected" : ""); ?>>Hombre</option>
          <option value="2" <?php echo (($user->image ?? '')=="woman.png" ? "selected" : ""); ?>>Mujer</option>
        </select>
      </div>
    </div>

    <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
      <label class="col-md-12 col-12 control-label">Nombre <span class="text-danger">*</span></label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa fa-user"></i></span>
        <input style="background-color:#333;" type="text" name="name" value="<?php echo htmlspecialchars((string)($user->name ?? ''), ENT_QUOTES, 'UTF-8'); ?>" autofocus class="form-control" id="name" required placeholder="Nombre">
      </div>
    </div>

    <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
      <label class="col-md-12 col-12 control-label">Apellido</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa fa-user"></i></span>
        <input style="background-color:#333;" type="text" name="lastname" class="form-control" id="lastname" value="<?php echo htmlspecialchars((string)($user->lastname ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Apellido">
      </div>
    </div>

    <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
      <label class="col-md-12 col-12 control-label">Email</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa fa-comments"></i></span>
        <input style="background-color:#333;" type="text" name="email" class="form-control" id="email" value="<?php echo htmlspecialchars((string)($user->email ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Email">
      </div>
    </div>

    <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
      <label class="col-md-12 col-12 control-label">Cedula</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa fa-user"></i></span>
        <input style="background-color:#333;" type="text" name="no" class="form-control" id="no" value="<?php echo htmlspecialchars((string)($user->no ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Cedula">
      </div>
    </div>

  </div>

  <div class="row my-2">
    <div class="col-md-6 col-6">
      <a href="./?view=employees&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
    </div>
    <div class="col-md-6 col-6">
      <input type="hidden" name="user_id" value="<?php echo (int)$user->id; ?>">
      <button type="submit" class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
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
      url: "./?action=employees&opt=upd",
      data: formData,
      success: function(html){
        if(html.trim() === 'true' || html.trim() === 'OK'){
          $.jGrowl("Empleado Exito!", { sticky: true });
          $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
          var delay = 1000;
          setTimeout(function(){
            window.location = './?view=employees&opt=all';
          }, delay);
        }else{
          $.jGrowl("No se Puede duplicar datos", { header: 'Error al Agregar' });
        }
      },
      error: function(){
        $.jGrowl("Error al procesar la solicitud", { header: 'Error' });
      }
    });

    return false;
  });
});
</script>
<?php else: ?>
  <div class="alert alert-danger">Empleado no encontrado.</div>
<?php endif; ?>

<?php else: ?>
<form class="form-horizontal" method="post" id="add" role="form">
  <div class="row">

    <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
      <label class="col-md-12 col-12 control-label">Genero</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa fa-spinner"></i></span>
        <select style="background-color:#333;" name="gender" class="form-control" required>
          <option value="">-- ELEGIR --</option>
          <option value="1">Hombre</option>
          <option value="2">Mujer</option>
        </select>
      </div>
    </div>

    <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
      <label class="col-md-12 col-12 control-label">Nombre <span class="text-danger">*</span></label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa fa-user"></i></span>
        <input style="background-color:#333;" type="text" name="name" autocomplete="off" autofocus class="form-control" id="name" required placeholder="Nombre">
      </div>
    </div>

    <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
      <label class="col-md-12 col-12 control-label">Apellido</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa fa-user"></i></span>
        <input style="background-color:#333;" type="text" name="lastname" autocomplete="off" class="form-control" id="lastname" placeholder="Apellido">
      </div>
    </div>

    <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
      <label class="col-md-12 col-12 control-label">Email</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa fa-comments"></i></span>
        <input style="background-color:#333;" type="text" name="email" autocomplete="off" class="form-control" id="email" placeholder="Email">
      </div>
    </div>

    <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
      <label class="col-md-12 col-12 control-label">Cedula</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa fa-user"></i></span>
        <input style="background-color:#333;" type="text" name="no" class="form-control" id="no" placeholder="Cedula">
      </div>
    </div>

    <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
      <label class="col-md-12 col-12 my-3 control-label"></label>
      <button type="submit" class="btn btn-warning btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
    </div>

  </div>
</form>

<script>
function guardarempleadoOffline(empleado) {
  let empleados = JSON.parse(localStorage.getItem("empleados_pendientes") || "[]");
  empleados.push(empleado);
  localStorage.setItem("empleados_pendientes", JSON.stringify(empleados));

  if (typeof toastr !== "undefined") {
    toastr.info('Guardado localmente. Se enviará cuando vuelva el internet.');
  }
}

function sincronizarempleados() {
  let empleados = JSON.parse(localStorage.getItem("empleados_pendientes") || "[]");

  if (empleados.length > 0 && navigator.onLine) {
    let pendientesActualizados = [...empleados];

    empleados.forEach((empleado) => {
      fetch("./?action=employees&opt=add_offline", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(empleado)
      })
      .then(res => res.text())
      .then(resp => {
        if (resp.trim() === "OK" || resp.trim() === "true") {
          pendientesActualizados = pendientesActualizados.filter(item =>
            JSON.stringify(item) !== JSON.stringify(empleado)
          );
          localStorage.setItem("empleados_pendientes", JSON.stringify(pendientesActualizados));
        }
      })
      .catch(() => {});
    });
  }
}

document.getElementById("add").addEventListener("submit", function(e) {
  e.preventDefault();
  const form = this;

  const empleado = {
    gender: form.gender.value,
    name: form.name.value,
    lastname: form.lastname.value,
    email: form.email.value,
    no: form.no.value
  };

  if (navigator.onLine) {
    fetch("./?action=employees&opt=add", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
      body: new URLSearchParams(empleado).toString()
    })
    .then(res => res.text())
    .then(resp => {
      if (resp.trim() === "OK" || resp.trim() === "true") {
        if (typeof toastr !== "undefined") {
          toastr.success('Registro agregado correctamente.');
        }
        window.location = './?view=employees&opt=all';
      } else {
        if (typeof toastr !== "undefined") {
          toastr.error('Ya existe ese registro.');
        }
      }
    })
    .catch(() => guardarempleadoOffline(empleado));
  } else {
    guardarempleadoOffline(empleado);
  }
});

setInterval(() => {
  if (navigator.onLine) sincronizarempleados();
}, 5000);
</script>
<?php endif; ?>

        </div>
      </div>

<?php
$base = new Database();
$con = $base->connect();
$stock_id = (int)(StockData::getPrincipal()->id ?? 0);
$sql = "SELECT SQL_BIG_RESULT * FROM user WHERE status=1 AND username='' AND stock_id=".$stock_id;
$query = $con->query($sql);
?>

<?php if($query && $query->num_rows > 0): ?>
<div class="card" style="background-color:#222;">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="example2">
        <thead>
          <tr>
            <th>Accion</th>
            <th>Foto</th>
            <th>Nombre</th>
            <th>Sucursal</th>
            <th>Activo</th>
            <th>Tipo</th>
            <th>Accion</th>
          </tr>
        </thead>

        <tfoot>
          <tr>
            <th>Accion</th>
            <th>Foto</th>
            <th>Nombre</th>
            <th>Sucursal</th>
            <th>Activo</th>
            <th>Tipo</th>
            <th>Accion</th>
          </tr>
        </tfoot>

        <tbody>
<?php while($r = $query->fetch_assoc()): ?>
          <tr>
            <td class="text-right py-0 align-middle">
              <div class="btn-group btn-group-sm btn-block">
                <a href="./?view=employees&opt=edit&id=<?php echo (int)$r['id']; ?>" class="btn btn-success"><i class="fas fa-edit"></i></a>
                <a href="./?view=employees&opt=history&id=<?php echo urlencode((string)$r['name']); ?>" class="btn btn-info"><i class="fas fa-history"></i></a>
              </div>
            </td>

            <td width="5%">
              <?php
              if(!empty($r['image'])){
                $url = "CF-SYSTEMS/storage/profiles/".$r['image'];
                if(file_exists($url)){
                  echo '<img src="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'" style="width:50%;">';
                }
              }
              ?>
            </td>

            <td><?php echo htmlspecialchars(trim(($r['name'] ?? '').' '.($r['lastname'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>

            <td>
              <?php
              if(!empty($r['stock_id'])){
                $stockObj = StockData::getById((int)$r['stock_id']);
                echo htmlspecialchars((string)($stockObj->name ?? ''), ENT_QUOTES, 'UTF-8');
              }
              ?>
            </td>

            <td><?php echo ((int)$r['status'] === 1 ? "SI" : "NO"); ?></td>

            <td>
              <?php
              $kindObj = null;
              if(isset($r['kind'])) {
                $kindObj = KData::getById((int)$r['kind']);
              }

              switch (Core::$user->language ?? 'ES'){
                case 'EN':
                  echo htmlspecialchars((string)($kindObj->name ?? ''), ENT_QUOTES, 'UTF-8');
                  break;
                case 'ES':
                default:
                  echo htmlspecialchars((string)($kindObj->nombre ?? ''), ENT_QUOTES, 'UTF-8');
                  break;
              }
              ?>
            </td>

            <td class="text-right py-0 align-middle">
              <?php
              $permisoEliminar = false;
              $user_session_id = isset($_SESSION["user_id"]) ? (int)$_SESSION["user_id"] : 0;
              $sl = "SELECT SQL_BIG_RESULT * FROM permits_user WHERE user_id=".$user_session_id;
              $qry = $con->query($sl);

              if($qry){
                while($x = $qry->fetch_assoc()){
                  if((int)$x['permits_id'] === 4){
                    $permisoEliminar = true;
                    break;
                  }
                }
              }

              if($permisoEliminar):
              ?>
                <a href="./?action=employees&opt=del&id=<?php echo (int)$r['id']; ?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();"><i class="fas fa-trash"></i> Eliminar</a>
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
    <h2>No hay Empleados</h2>
    <p>No se ha realizado ninguna operacion.</p>
  </div>
</div>
<?php endif; ?>

    </div>
  </div>
</section>
<?php endif; ?>