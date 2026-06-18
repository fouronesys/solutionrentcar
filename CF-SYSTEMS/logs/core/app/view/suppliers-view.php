
<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-street-view'></i> Listado de Suplidores</h1>
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
<?php if(isset($_GET['id'])): $user = SuppliersData::getById($_GET["id"]);?>
    <form class="form-horizontal" method="post" id="upd" role="form">
 <div class="row">
      <div class="col-md-8 col-8">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre</label>
      <input style="background-color:#333;" type="text" name="name" autocomplete="off"  value="<?php echo utf8_decode($user->name);?>"  class="form-control" placeholder="Nombre del Suplidor">
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
                  url: "./?action=suppliers&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Suplidor Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=suppliers&opt=all'  }, delay); 
                     
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
      <input style="background-color:#333;" type="text" name="name" required autocomplete="off" class="form-control" placeholder="Nombre del Suplidor">
    </div>
    <div class="col-md-4 col-4">
      <label class="col-md-12 col-12 my-3 control-label"></label>
      <button class="btn btn-warning btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
    </div>
  </div>
</form>

<script>
function guardarSuplidorOffline(data) {
  let pendientes = JSON.parse(localStorage.getItem("suplidores_pendientes") || "[]");
  pendientes.push(data);
  localStorage.setItem("suplidores_pendientes", JSON.stringify(pendientes));
  toastr.info('Guardado localmente. Se enviará cuando vuelva el internet.');
}

function sincronizarSuplidores() {
  let pendientes = JSON.parse(localStorage.getItem("suplidores_pendientes") || "[]");
  if (pendientes.length > 0 && navigator.onLine) {
    pendientes.forEach((suplidor, i) => {
      fetch("./?action=suppliers&opt=add_offline", {
         method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(suplidor)
})
      .then(res => res.text())
      .then(resp => {
        if (resp.trim() === "OK") {
          pendientes.splice(i, 1);
          localStorage.setItem("suplidores_pendientes", JSON.stringify(pendientes));
        }
      });
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
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams(data)
    })
    .then(res => res.text())
    .then(resp => {
      if (resp.trim() === "OK") {
         toastr.success('Registro agregado correctamente.');
        window.location.href = "./?view=suppliers&opt=all";
      } else {
         toastr.error('Ya existe ese registro.');
      }
    })
    .catch(() => guardarSuplidorOffline(data));
  } else {
    guardarSuplidorOffline(data);
  }
});

// Reintenta sincronizar cada 5 segundos si vuelve el internet
setInterval(() => {
  if (navigator.onLine) sincronizarSuplidores();
}, 5000);
</script>

<?php endif;?>
</div>
</div>

<?php 
$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT * from stock where is_ext=1";
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
                        <a href="./?view=suppliers&opt=all&id=<?php echo $r['id'];?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                      </div>
        </td>

        <td><?php echo $r['name']; ?></td>
        <td class="text-right py-0 align-middle">
            
<?php  
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()){
if ($x['permits_id']==4): ?>
   
                        <a href="./?action=suppliers&opt=del&id=<?php echo $r['id'];?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();" ><i class="fas fa-trash"> Eliminar</i></a>
                    
                    
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
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->
      <?php else:?>
     
          <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Suplidor</h2>
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