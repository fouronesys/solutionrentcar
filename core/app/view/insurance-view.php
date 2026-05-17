
<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-clone'></i> Listado de Seguro</h1>
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
           <div class="card"  style="background-color:#222;">
<div class="card-body">
<?php if(isset($_GET['id'])): $user = InsuranceData::getById($_GET["id"]);?>
    <form class="form-horizontal" method="post" id="upd" role="form">
 <div class="row">
      <div class="col-md-8 col-8">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre</label>
      <input style="background-color:#333;" type="text" name="name" autocomplete="off"  value="<?php echo utf8_decode($user->name);?>"  class="form-control" placeholder="Nombre del Seguro">
    </div>

    
               
                <div class="col-md-4 col-4">

    <label for="inputEmail1" class="col-md-12 col-12 my-3 control-label"></label>
                  <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $user->id;?>">
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
                  url: "./?action=insurance&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Seguro Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=insurance&opt=all'  }, delay); 
                     
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
      <input style="background-color:#333;" type="text" name="name" autocomplete="off" class="form-control" placeholder="Nombre del Seguro" required>
    </div>
    <div class="col-md-4 col-4">
      <label class="col-md-12 col-12 my-3 control-label"></label>
      <button class="btn btn-warning btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
    </div>
  </div>
</form>

<script>
// Guardar localmente si no hay conexión
function guardarSeguroOffline(seguro) {
  let lista = JSON.parse(localStorage.getItem("seguros_pendientes")) || [];
  lista.push(seguro);
  localStorage.setItem("seguros_pendientes", JSON.stringify(lista));
   toastr.info('Guardado localmente. Se enviará cuando vuelva el internet.');
}

// Sincronizar cuando haya conexión
function sincronizarSeguros() {
  let lista = JSON.parse(localStorage.getItem("seguros_pendientes")) || [];
  if (lista.length > 0 && navigator.onLine) {
    lista.forEach((item, i) => {
      fetch("./?action=insurance&opt=add_offline", {
        method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(item)
})
      .then(res => res.text())
      .then(resp => {
        if (resp.trim() === "OK") {
          lista.splice(i, 1);
          localStorage.setItem("seguros_pendientes", JSON.stringify(lista));
        }
      });
    });
  }
}

document.getElementById("add").addEventListener("submit", function(e){
  e.preventDefault();
  const form = this;
  const seguro = { name: form.name.value };

  if (navigator.onLine) {
    fetch("./?action=insurance&opt=add", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams(seguro).toString()
    })
    .then(res => res.text())
    .then(resp => {
      if (resp.trim() === "OK" || resp.trim() === "true") {
        toastr.success('Registro agregado correctamente.');
        window.location = './?view=insurance&opt=all';
      } else {
       toastr.error('Ya existe ese registro.');
      }
    })
    .catch(() => guardarSeguroOffline(seguro));
  } else {
    guardarSeguroOffline(seguro);
  }
});

// Intentar sincronizar cada 5 segundos
setInterval(() => {
  if (navigator.onLine) sincronizarSeguros();
}, 5000);
</script>

<?php endif;?>
</div>
</div>

<?php 
$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT * from insurance";
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
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=insurance&opt=all&id=<?php echo $r['id'];?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                      </div>
        </td>

        <td><?php echo $r['name']; ?></td>
        <td class="text-right py-0 align-middle">
            
<?php  
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()){
if ($x['permits_id']==4): ?>
   
                        <a href="./?action=insurance&opt=del&id=<?php echo $r['id'];?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();" ><i class="fas fa-trash"> Eliminar</i></a>
                    
                    
                     <script>
function confirmDelete() {
    return confirm("¿Estás seguro de que deseas eliminar este registro?");
}
</script>
    <?php endif;?>
<?php }; ?>
</td>
    </tr>
    
    <?php }; ?>
      </table>
 </table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->
      <?php else:?>
     
          <div class="card"  style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Seguro</h2>
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