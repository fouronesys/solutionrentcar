<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"): ?>
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><i class='fa fa-users'></i> <?php 
            switch (Core::$user->language){
              case 'ES': echo 'Usuarios.'; break;
              case 'EN': echo 'Users'; break;
            }
            ?></h1>
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

    <div class="callout callout-warning" style="background-color:#222;">
      <h5><i class="fas fa-info"></i> <?php 
      switch (Core::$user->language){
        case 'ES': echo "Nota"; break;
        case 'EN': echo "Note"; break;
      }
      ?>:</h5>
      <?php 
      switch (Core::$user->language){
        case 'ES': echo " Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo."; break;
        case 'EN': echo "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; break;
      }
      ?>
    </div>

<?php 
$base = new Database();
$con = $base->connect();
$stock_id = (int)(StockData::getPrincipal()->id ?? 0);
$sql = "SELECT SQL_BIG_RESULT * FROM user WHERE username<>'krtavarez' AND stock_id=".$stock_id;
$query = $con->query($sql);
if($query && $query->num_rows > 0): ?>
<div class="card" style="background-color:#222;">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="example2">
        <thead> 
          <tr>
            <th><?php switch (Core::$user->language){ case 'ES': echo 'Accion'; break; case 'EN': echo 'Action'; break; } ?></th>
            <th><?php switch (Core::$user->language){ case 'ES': echo 'Foto'; break; case 'EN': echo 'Photo'; break; } ?></th>
            <th><?php switch (Core::$user->language){ case 'ES': echo 'Nombre'; break; case 'EN': echo 'Name'; break; } ?></th>
            <th>Whatsapp</th>
            <th><?php switch (Core::$user->language){ case 'ES': echo 'Correo'; break; case 'EN': echo 'Email'; break; } ?></th>
            <th><?php switch (Core::$user->language){ case 'ES': echo 'Activo'; break; case 'EN': echo 'Asset'; break; } ?></th>
            <th><?php switch (Core::$user->language){ case 'ES': echo 'Tipo'; break; case 'EN': echo 'Type'; break; } ?></th>
            <th><?php switch (Core::$user->language){ case 'ES': echo 'Accion'; break; case 'EN': echo 'Action'; break; } ?></th>
          </tr>
        </thead>

        <tfoot>
          <tr>
            <th><?php switch (Core::$user->language){ case 'ES': echo 'Accion'; break; case 'EN': echo 'Action'; break; } ?></th>
            <th><?php switch (Core::$user->language){ case 'ES': echo 'Foto'; break; case 'EN': echo 'Photo'; break; } ?></th>
            <th><?php switch (Core::$user->language){ case 'ES': echo 'Nombre'; break; case 'EN': echo 'Name'; break; } ?></th>
            <th>Whatsapp</th>
            <th><?php switch (Core::$user->language){ case 'ES': echo 'Correo'; break; case 'EN': echo 'Email'; break; } ?></th>
            <th><?php switch (Core::$user->language){ case 'ES': echo 'Activo'; break; case 'EN': echo 'Asset'; break; } ?></th>
            <th><?php switch (Core::$user->language){ case 'ES': echo 'Tipo'; break; case 'EN': echo 'Type'; break; } ?></th>
            <th><?php switch (Core::$user->language){ case 'ES': echo 'Accion'; break; case 'EN': echo 'Action'; break; } ?></th>
          </tr>
        </tfoot>

        <tbody>
<?php while($r = $query->fetch_assoc()): ?>
          <tr>
            <td class="text-right py-0 align-middle">
              <div class="btn-group btn-group-sm btn-block">
                <a href="./?view=users&opt=edit&id=<?php echo (int)$r['id']; ?>" class="btn btn-success"><i class="fas fa-edit"></i></a>
                <a href="./?view=users&opt=history&id=<?php echo (int)$r['id']; ?>" class="btn btn-info"><i class="fas fa-history"></i></a>
                <a href="./?view=userpermissions&id=<?php echo (int)$r['id']; ?>" class="btn btn-warning"><i class="fas fa-user-plus"></i></a>
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
            <td><?php echo htmlspecialchars((string)($r['phone'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars((string)($r['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>

            <?php if((int)($r['status'] ?? 0) === 1): ?>
              <?php switch (Core::$user->language){ case 'ES': echo '<td> SI </td>'; break; case 'EN': echo '<td> YEAH </td>'; break; } ?>
            <?php else: ?>
              <?php switch (Core::$user->language){ case 'ES': echo '<td> NO </td>'; break; case 'EN': echo '<td> NOT </td>'; break; } ?>
            <?php endif; ?>

            <?php
            $kindObj = null;
            if(isset($r['kind'])){ $kindObj = KData::getById((int)$r['kind']); }
            switch (Core::$user->language){
              case 'ES': echo '<td>'.htmlspecialchars((string)($kindObj->nombre ?? ''), ENT_QUOTES, 'UTF-8').'</td>'; break;
              case 'EN': echo '<td>'.htmlspecialchars((string)($kindObj->name ?? ''), ENT_QUOTES, 'UTF-8').'</td>'; break;
            }
            ?>

            <td class="text-right py-0 align-middle">
<?php  
$sl = "SELECT SQL_BIG_RESULT * FROM permits_user WHERE user_id=".(int)($_SESSION["user_id"] ?? 0);
$qry = $con->query($sl); 
$canDelete = false;
if($qry){
  while($x = $qry->fetch_assoc()){
    if((int)$x['permits_id'] === 4){
      $canDelete = true;
      break;
    }
  }
}
if($canDelete): ?>
              <a href="./?action=users&opt=del&id=<?php echo (int)$r['id']; ?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();">
                <i class="fas fa-trash"> <?php switch (Core::$user->language){ case 'ES': echo 'Eliminar'; break; case 'EN': echo 'Delete'; break; } ?></i>
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
    <h2>No hay Usuarios</h2>
    <p>No se ha realizado ninguna operacion.</p>
  </div>
</div>
<?php endif; ?>
  </div>
</div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="new"): ?>
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><i class='fa fa-users'></i> <?php 
            switch (Core::$user->language){
              case 'ES': echo 'Nuevo Usuarios'; break;
              case 'EN': echo 'New Users'; break;
            }
            ?></h1>
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

    <div class="callout callout-warning" style="background-color:#222;">
      <h5><i class="fas fa-info"></i> <?php switch (Core::$user->language){ case 'ES': echo "Nota"; break; case 'EN': echo "Note"; break; } ?>:</h5>
      <?php switch (Core::$user->language){
        case 'ES': echo " Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo."; break;
        case 'EN': echo "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; break;
      } ?>
    </div>

    <div class="card" style="background-color:#222;">
      <div class="card-body">
        <form class="form-horizontal" enctype="multipart/form-data" method="post" id="changepasswd" role="form">
          <div class="row">
            <div class="col-md-3 col-12">
              <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Idioma'; break; case 'EN': echo 'Language'; break; } ?></label>
              <select style="background-color:#333;" name="language" class="form-control select2" required>
                <option value="ES"><?php switch (Core::$user->language){ case 'ES': echo 'ESPAÑOL'; break; case 'EN': echo 'SPANISH'; break; } ?></option>
                <option value="EN"><?php switch (Core::$user->language){ case 'ES': echo 'INGLES'; break; case 'EN': echo 'ENGLISH'; break; } ?></option>
              </select>
            </div>

            <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
              <div class="input-group">
                <label class="col-md-12 col-12 control-label">Whatsapp</label>
                <input style="background-color:#333;" type="text" name="phone" autocomplete="off" class="form-control" placeholder="Numero de Whatsapp">
              </div>
            </div>

            <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
              <div class="input-group">
                <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Cedula'; break; case 'EN': echo 'ID'; break; } ?></label>
                <input style="background-color:#333;" type="text" name="no" autocomplete="off" class="form-control" placeholder="<?php switch (Core::$user->language){ case 'ES': echo 'Cedula'; break; case 'EN': echo 'ID'; break; } ?>">
              </div>
            </div>

            <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
              <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Genero'; break; case 'EN': echo 'Gender'; break; } ?></label>
              <div class="input-group">
                <span class="input-group-text" style="background-color:orange"><i class="fa fa-spinner"></i></span>
                <select style="background-color:#333;" name="gender" class="form-control" required>
                  <option value="1"><?php switch (Core::$user->language){ case 'ES': echo 'Hombre'; break; case 'EN': echo 'Man'; break; } ?></option>
                  <option value="2"><?php switch (Core::$user->language){ case 'ES': echo 'Mujer'; break; case 'EN': echo 'Women'; break; } ?></option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Nombre'; break; case 'EN': echo 'Name'; break; } ?> <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text" style="background-color:orange"><i class="fa fa-user"></i></span>
                <input style="background-color:#333;" type="text" name="name" autofocus class="form-control" id="name" required placeholder="<?php switch (Core::$user->language){ case 'ES': echo 'Nombre'; break; case 'EN': echo 'Name'; break; } ?>">
              </div>
            </div>

            <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Apellido'; break; case 'EN': echo 'Last name'; break; } ?></label>
              <div class="input-group">
                <span class="input-group-text" style="background-color:orange"><i class="fa fa-user"></i></span>
                <input style="background-color:#333;" type="text" name="lastname" class="form-control" placeholder="<?php switch (Core::$user->language){ case 'ES': echo 'Apellido'; break; case 'EN': echo 'Last name'; break; } ?>">
              </div>
            </div>

            <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Correo electrónico'; break; case 'EN': echo 'Email'; break; } ?></label>
              <div class="input-group">
                <span class="input-group-text" style="background-color:orange"><i class="fa fa-comments"></i></span>
                <input style="background-color:#333;" type="email" name="email" class="form-control" id="email" placeholder="<?php switch (Core::$user->language){ case 'ES': echo 'Correo electrónico'; break; case 'EN': echo 'Email'; break; } ?>">
              </div>
            </div>

            <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
              <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Tipo'; break; case 'EN': echo 'Type'; break; } ?>: <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text" style="background-color:orange"><i class="fa fa-clone"></i></span>
                <select style="background-color:#333;" name="kind" required class="form-control">
                  <?php foreach(KData::getAll() as $k): ?>
                    <option value="<?php echo (int)$k->id; ?>">
                      <?php switch (Core::$user->language){ case 'ES': echo htmlspecialchars((string)$k->nombre, ENT_QUOTES, 'UTF-8'); break; case 'EN': echo htmlspecialchars((string)$k->name, ENT_QUOTES, 'UTF-8'); break; } ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
              <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Sueldo'; break; case 'EN': echo 'Salary'; break; } ?></label>
              <div class="input-group">
                <span class="input-group-text" style="background-color:orange"><i class="fa fa-plus"></i></span>
                <input style="background-color:#333;" type="text" name="comision" class="form-control" placeholder="<?php switch (Core::$user->language){ case 'ES': echo 'Sueldo'; break; case 'EN': echo 'Salary'; break; } ?>">
              </div>
            </div>

            <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
              <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Contraseña'; break; case 'EN': echo 'Password'; break; } ?> <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text" style="background-color:orange"><i class="fa fa-asterisk"></i></span>
                <input style="background-color:#333;" type="password" class="form-control" id="password" name="password" placeholder="<?php switch (Core::$user->language){ case 'ES': echo 'Contraseña'; break; case 'EN': echo 'Password'; break; } ?>">
              </div>
            </div>

            <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
              <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Confirmar Contraseña'; break; case 'EN': echo 'Confirm Password'; break; } ?> <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text" style="background-color:orange"><i class="fa fa-edit"></i></span>
                <input style="background-color:#333;" type="password" class="form-control" id="confirmnewpassword" placeholder="<?php switch (Core::$user->language){ case 'ES': echo 'Confirmar Contraseña'; break; case 'EN': echo 'Confirm Password'; break; } ?>">
              </div>
            </div>
          </div>

          <div class="col-md-6 col-12">
            <br>
            <label>
              <div class="form-group clearfix">
                <div class="icheck-primary d-inline">
                  <input style="background-color:#333;" type="checkbox" id="radioPrimary" name="status" checked>
                  <label for="radioPrimary"><?php switch (Core::$user->language){ case 'ES': echo '¿Esta activo?'; break; case 'EN': echo 'Is it active?'; break; } ?></label>
                </div>
              </div>
            </label>
          </div>

          <div class="row my-2">
            <div class="col-md-6 col-6">
              <a href="./?view=users&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> <?php switch (Core::$user->language){ case 'ES': echo 'Cancelar'; break; case 'EN': echo 'Cancel'; break; } ?></a>
            </div>
            <div class="col-md-6 col-6">
              <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> <?php switch (Core::$user->language){ case 'ES': echo 'Finalizar'; break; case 'EN': echo 'Finish'; break; } ?></button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</section>

<script>
$("#changepasswd").submit(function(e){
  if($("#password").val() == $("#confirmnewpassword").val()){
    e.preventDefault();
    var formData = jQuery(this).serialize();
    $.ajax({
      type: "POST",
      url: "./?action=users&opt=add",
      data: formData,
      success: function(html){
        if(html.trim() === 'true' || html.trim() === 'OK'){
          $.jGrowl("Usuario Exito!", { sticky: true });
          $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
          var delay = 1000;
          setTimeout(function(){ window.location = './?view=users&opt=all'; }, delay);
        }else{
          $.jGrowl("Por favor verifique su datos", { header: 'Error al Actualizar' });
        }
      }
    });
    return false;
  }else{
    e.preventDefault();
    $.jGrowl("La contraseña no coincide con la confirmacion", { header: 'Acceso permitido' });
  }
});
</script>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"): ?>
<section class="content">
<?php
$user = null;
if(isset($_GET["id"]) && is_numeric($_GET["id"])){
  $user = UserData::getById((int)$_GET["id"]);
}
if(!$user){
  echo '<div class="alert alert-danger">Usuario no encontrado.</div>';
}else{
?>
<div class="row">
  <div class="col-md-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><i class=\'fa fa-users\'></i> <?php switch (Core::$user->language){ case 'ES': echo 'Editar Usuarios'; break; case 'EN': echo 'Edit Users'; break; } ?></h1>
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
      </div>
    </div>

    <div class="card" style="background-color:#222;">
      <div class="card-body">
        <form class="form-horizontal" enctype="multipart/form-data" method="post" id="changepasswd" role="form">
          <div class="row">
            <div class="col-md-3 col-12">
              <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Idioma'; break; case 'EN': echo 'Language'; break; } ?></label>
              <select style="background-color:#333;" name="language" class="form-control select2" required>
                <option value="ES" <?php echo (($user->language ?? '')=="ES" ? "selected" : ""); ?>><?php switch (Core::$user->language){ case 'ES': echo 'ESPAÑOL'; break; case 'EN': echo 'SPANISH'; break; } ?></option>
                <option value="EN" <?php echo (($user->language ?? '')=="EN" ? "selected" : ""); ?>><?php switch (Core::$user->language){ case 'ES': echo 'INGLES'; break; case 'EN': echo 'ENGLISH'; break; } ?></option>
              </select>
            </div>

            <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
              <div class="input-group">
                <label class="col-md-12 col-12 control-label">Whatsapp</label>
                <input style="background-color:#333;" type="text" name="phone" autocomplete="off" class="form-control" placeholder="Numero de Whatsapp" value="<?php echo htmlspecialchars((string)($user->phone ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
              </div>
            </div>

            <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
              <div class="input-group">
                <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Cedula'; break; case 'EN': echo 'ID'; break; } ?></label>
                <input style="background-color:#333;" type="text" name="no" autocomplete="off" class="form-control" placeholder="<?php switch (Core::$user->language){ case 'ES': echo 'Cedula'; break; case 'EN': echo 'ID'; break; } ?>" value="<?php echo htmlspecialchars((string)($user->no ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
              </div>
            </div>

            <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
              <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Genero'; break; case 'EN': echo 'Gender'; break; } ?></label>
              <div class="input-group">
                <span class="input-group-text" style="background-color:orange"><i class="fa fa-spinner"></i></span>
                <select style="background-color:#333;" name="gender" class="form-control" required>
                  <option value="1" <?php echo (($user->image ?? '')=="man.png" ? "selected" : ""); ?>><?php switch (Core::$user->language){ case 'ES': echo 'Hombre'; break; case 'EN': echo 'Man'; break; } ?></option>
                  <option value="2" <?php echo (($user->image ?? '')=="woman.png" ? "selected" : ""); ?>><?php switch (Core::$user->language){ case 'ES': echo 'Mujer'; break; case 'EN': echo 'Women'; break; } ?></option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Nombre'; break; case 'EN': echo 'Name'; break; } ?> <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text" style="background-color:orange"><i class="fa fa-user"></i></span>
                <input style="background-color:#333;" type="text" name="name" value="<?php echo htmlspecialchars((string)($user->name ?? ''), ENT_QUOTES, 'UTF-8'); ?>" autofocus class="form-control" id="name" required placeholder="<?php switch (Core::$user->language){ case 'ES': echo 'Nombre'; break; case 'EN': echo 'Name'; break; } ?>">
              </div>
            </div>

            <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Apellido'; break; case 'EN': echo 'Last name'; break; } ?></label>
              <div class="input-group">
                <span class="input-group-text" style="background-color:orange"><i class="fa fa-user"></i></span>
                <input style="background-color:#333;" type="text" name="lastname" class="form-control" value="<?php echo htmlspecialchars((string)($user->lastname ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?php switch (Core::$user->language){ case 'ES': echo 'Apellido'; break; case 'EN': echo 'Last name'; break; } ?>">
              </div>
            </div>

            <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
              <label class="col-md-12 col-12 control-label"><?php switch (Core::$user->language){ case 'ES': echo 'Correo electrónico'; break; case 'EN': echo 'Email'; break; } ?></label>
              <div class="input-group">
                <span class="input-group-text" style="background-color:orange"><i class="fa fa-comments"></i></span>
                <input style="background-color:#333;" type="email" name="email" class="form-control" id="email" value="<?php echo htmlspecialchars((string)($user->email ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?php switch (Core::$user->language){ case 'ES': echo 'Correo electrónico'; break; case 'EN': echo 'Email'; break; } ?>">
              </div>
            </div>
          </div>
<?php } ?>
</section>
<?php endif; ?>