
<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-clone'></i> Listado de Combustible</h1>
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
<?php if($_GET['id']>0): $user = FuelData::getById($_GET["id"]);?>
    <form class="form-horizontal" method="post" id="upd" role="form">
 <div class="row">
      <div class="col-md-8 col-8">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre</label>
      <input type="text" name="name" autocomplete="off"  value="<?php echo utf8_decode($user->name);?>"  class="form-control" placeholder="Nombre del Combustible">
    </div>

    
               
                <div class="col-md-4 col-4">

    <label for="inputEmail1" class="col-md-12 col-12 my-3 control-label"></label>
                  <input type="hidden" name="user_id" value="<?php echo $user->id;?>">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
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
                  url: "./?action=fuel&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Combustible Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=fuel&opt=all'  }, delay); 
                     
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
      <input type="text" name="name" autocomplete="off" class="form-control" placeholder="Nombre del Combustible" required>
    </div>
    <div class="col-md-4 col-4">
      <label class="col-md-12 col-12 my-3 control-label"></label>
      <button class="btn btn-warning btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
    </div>
  </div>
</form>

<script>
// Guardar offline
function guardarFuelOffline(data) {
  let lista = JSON.parse(localStorage.getItem("fuel_pendiente")) || [];
  lista.push(data);
  localStorage.setItem("fuel_pendiente", JSON.stringify(lista));
  toastr.info('Guardado localmente. Se enviará cuando vuelva el internet.');
}

// Sincronizar cuando regrese el internet
function sincronizarFuel() {
  let lista = JSON.parse(localStorage.getItem("fuel_pendiente")) || [];
  if (lista.length > 0 && navigator.onLine) {
    lista.forEach((item, i) => {
      fetch("./?action=fuel&opt=add_offline", {
         method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(item)
})
      .then(res => res.text())
      .then(resp => {
        if (resp.trim() === "OK") {
          lista.splice(i, 1);
          localStorage.setItem("fuel_pendiente", JSON.stringify(lista));
        }
      });
    });
  }
}

document.getElementById("add").addEventListener("submit", function(e){
  e.preventDefault();
  const form = this;
  const data = { name: form.name.value };

  if (navigator.onLine) {
    fetch("./?action=fuel&opt=add", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams(data).toString()
    })
    .then(res => res.text())
    .then(resp => {
      if (resp.trim() === "OK" || resp.trim() === "true") {
         toastr.success('Registro agregado correctamente.');
        window.location = './?view=fuel&opt=all';
      } else {
         toastr.error('Ya existe ese registro.');
      }
    })
    .catch(() => guardarFuelOffline(data));
  } else {
    guardarFuelOffline(data);
  }
});

// Intentar sincronizar cada 5 segundos
setInterval(() => {
  if (navigator.onLine) sincronizarFuel();
}, 5000);
</script>

<?php endif;?>
</div>
</div>

<?php $users = FuelData::getAll();
    if(count($users)>0):?>
<div class="card"  style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
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

      <?php foreach($users as $user): ?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=fuel&opt=all&id=<?php echo $user->id;?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                      </div>
        </td>

        <td><?php echo $user->name; ?></td>
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=fuel&opt=del&id=<?php echo $user->id;?>" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> Eliminar</i></a>
                    
    <?php endif;?>
<?php endforeach; ?>
</td>
    </tr>
    
    <?php endforeach; ?>
      </table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->
      <?php else:?>
     
         <div class="card">
              <div class="card-header">
    <h2>No hay Combustible</h2>
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
<?php endif; ?>