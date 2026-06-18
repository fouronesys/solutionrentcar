<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>

<section class="content">
<div class="row">
  <div class="col-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><i class='fa fa-list-ol'></i> Tipo de  Mantenimiento</h1>
          </div><!-- /.col -->
          
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
    
            <div class="card" style="background-color:#222;">
<div class="card-body">
<?php if(isset($_GET['id'])): $user = MData::getById($_GET["id"]);?>
    <form class="form-horizontal" method="post" id="upd" role="form">
 <div class="row">
      <div class="col-md-8 col-8">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre</label>
      <input style="background-color:#333;" type="text" name="name" autocomplete="off"  value="<?php echo utf8_decode($user->name);?>"  class="form-control" placeholder="Tipo del Mantenimiento">
    </div>

    
               
                <div class="col-md-4 col-4">

    <label for="inputEmail1" class="col-md-12 col-12 my-3 control-label"></label>
                  <input type="hidden" name="user_id" value="<?php echo $user->id;?>">
                   <button class="btn btn-warning btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
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
                  url: "./?action=m&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Tipo Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=m&opt=all'  }, delay); 
                     
                  }else{
                  $.jGrowl("No se Puede duplicar datos", { header: 'Error al Agregar' });
                  }
                  }
                });
                return false;
              });
            });
            </script>
<?php else:?>
<form class="form-horizontal" method="post" id="add" role="form">
  <div class="row">
    <div class="col-md-8 col-8">
      <label class="col-md-12 col-12 control-label">Nombre</label>
      <input style="background-color:#333;" type="text" name="name" autocomplete="off" class="form-control" placeholder="Tipo del Mantenimiento" required>
    </div>
    <div class="col-md-4 col-4">
      <label class="col-md-12 col-12 my-3 control-label"></label>
      <button class="btn btn-warning btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
    </div>
  </div>
</form>

<script>
// Guardar localmente si no hay internet
function guardarMantenimientoOffline(tipo) {
  let lista = JSON.parse(localStorage.getItem("mantenimientos_pendientes")) || [];
  lista.push(tipo);
  localStorage.setItem("mantenimientos_pendientes", JSON.stringify(lista));
  toastr.info('Guardado localmente. Se enviará cuando vuelva el internet.');
}

// Sincronizar cuando se recupere la conexión
function sincronizarMantenimientos() {
  let lista = JSON.parse(localStorage.getItem("mantenimientos_pendientes")) || [];
  if (lista.length > 0 && navigator.onLine) {
    lista.forEach((item, i) => {
      fetch("./?action=m&opt=add_offline", {
     method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(item)
})
      .then(res => res.text())
      .then(resp => {
        if (resp.trim() === "OK" || resp.trim() === "true") {
          lista.splice(i, 1);
          localStorage.setItem("mantenimientos_pendientes", JSON.stringify(lista));
        }
      });
    });
  }
}

document.getElementById("add").addEventListener("submit", function(e){
  e.preventDefault();
  const form = this;
  const tipo = { name: form.name.value };

  if (navigator.onLine) {
    fetch("./?action=m&opt=add", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams(tipo).toString()
    })
    .then(res => res.text())
    .then(resp => {
      if (resp.trim() === "OK" || resp.trim() === "true") {
       toastr.success('Registro agregado correctamente.');
        window.location = './?view=m&opt=all';
      } else {
       toastr.error('Ya existe ese registro.');
      }
    })
    .catch(() => guardarMantenimientoOffline(tipo));
  } else {
    guardarMantenimientoOffline(tipo);
  }
});

// Sincronizar cada 5 segundos
setInterval(() => {
  if (navigator.onLine) sincronizarMantenimientos();
}, 5000);
</script>

<?php endif;?>
</div>
</div>

<?php 
$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT * from maintenance_type";
$query = $con->query($sql);
    if(count($query)>0):?>
  <div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      <th>Accion</th>
      <th>Nombre</th>
      <th>Accion</th>
    </thead>

    <tfoot>
      <tr>
      <th>Accion</th>
      <th>Nombre</th>
      <th>Accion</th>
      </tr>
    </tfoot>

      <?php while($r = $query->fetch_array()){?>
        <tr>
                  <td class="text-right py-0 align-middle"><?php if($r['id']==2 || $r['id']==5 || $r['id']==7): 
    
else: ?>
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=m&opt=all&id=<?php echo $r['id'];?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                      </div>
<?php endif;?>    
        </td>

        <td><?php echo $r['name']; ?></td>
        <td class="text-right py-0 align-middle">
            
<?php if($r['id']==2 || $r['id']==5 || $r['id']==7): 
    
else:  

$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()){
if ($x['permits_id']==4): ?>
   
                        <a href="./?action=m&opt=del&id=<?php echo $r['id'];?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();" ><i class="fas fa-trash"> Eliminar</i></a>
                    
                    
                     <script>
function confirmDelete() {
    return confirm("¿Estás seguro de que deseas eliminar este registro?");
}
</script>
    <?php endif;?>
<?php } 
endif; ?>
</td>
    </tr>
    
    <?php }; ?>
                </table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->
      <?php else:?>
     
           <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Mantenimiento</h2>
    <p>No se ha realizado ninguna operacion.</p>
    </div>
</div>
  <?php endif;?>
</div>
</div>
</div>
</div>
</div>
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="new"):?>
<section class="content">
<div class="row">
  <div class="col-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-list-ol'></i> Nuevo Tipo
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cubes'></i> Extras</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Mantenimiento</a></li>

            </ol>
          </div><!-- /.col -->
</div>
  <div class="card" style="background-color:#222;">
<div class="card-body">
 <form class="form-horizontal" method="post" id="add" role="form">
  <div class="form-group">
    <label for="inputEmail1" class="col-12  col-12 control-label">Nombre*</label>
    <div class="col-12">
      <input type="text" autocomplete="off" name="name" required class="form-control" autofocus placeholder="Nombre">
    </div>
  </div>
<div class="row my-2" >
                <div class="col-6 col-md-6">
                  <a href="./?view=m&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-6 col-md-6">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
</form>
</div>
  </div>
</div>
  </div>
</div>
  </div>
</div>
<script>
            jQuery(document).ready(function(){
            jQuery("#add").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=m&opt=add",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Mantenimiento Exito!", { sticky: true });
                  $.jGrowl("Se Agrego a la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=m&opt=all'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Agregar' });
                  }
                  }
                });
                return false;
              });
            });
            </script>
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"):?>
<section class="content">
<?php $user = FData::getById($_GET["id"]);?>
<div class="row">
  <div class="col-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-edit'></i> Editar Mantenimiento
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cubes'></i> Inventario</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Pago/Forma</a></li>

            </ol>
          </div><!-- /.col -->
</div>
  <div class="card" style="background-color:#222;">
<div class="card-body">
    <form class="form-horizontal" method="post" id="upd" role="form">

  <div class="form-group">
    <label for="inputEmail1" class="col-2 col-2 control-label">Nombre*</label>
    <div class="col-12">
      <input type="text" name="name" value="<?php echo utf8_decode($user->name);?>" class="form-control" id="name" placeholder="Nombre">
    </div>
  </div>

<div class="row my-2">
                <div class="col-6">
                  <a href="./?view=m&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-6">
                   <input type="hidden" name="user_id" value="<?php echo $user->id;?>">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>

</form>
</div>
  </div>
</div>
  </div>
</div>
  </div>
</div>
<script>
            jQuery(document).ready(function(){
            jQuery("#upd").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=m&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Mantenimiento Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=m&opt=all'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Actualizar' });
                  }
                  }
                });
                return false;
              });
            });
            </script>
</section>
<?php endif; ?>