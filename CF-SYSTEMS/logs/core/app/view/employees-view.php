<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
<div class="row">
	<div class="col-md-12">
	<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-users'></i> Empleados</h1>
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
<?php if(isset($_GET['id'])): $user = BrandData::getById($_GET["id"]);?>
  	<form class="form-horizontal" enctype="multipart/form-data"   method="post" id="changepasswd" role="form">

<div class="row">
     
     <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Genero</label>
     <div class="input-group">
  <span class="input-group-text"><i class="fa fa-spinner"></i></span>
    
    <select style="background-color:#333;" name="gender" class="form-control" required>
      <option value="1"<?php if($user->image=="man.png"){ echo "selected"; }?>>Hombre</option>
      <option value="2"<?php if($user->image=="woman.png"){ echo "selected"; }?>>Mujer</option>
      </select>
    </div>
  </div>

 
     
    <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text"><i class="fa fa-user"></i></span>
      <input style="background-color:#333;" type="text" name="name" value="<?php echo utf8_decode($user->name);?>" autofocus class="form-control" id="name" required placeholder="Nombre">
    </div>
</div>

<div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Apellido</label>
     <div class="input-group">
  <span class="input-group-text"><i class="fa fa-user"></i></span>
      <input style="background-color:#333;" type="text" name="lastname" class="form-control" id="lastname" value="<?php echo $user->lastname;?>" placeholder="Apellido">
    </div>
</div>
  
    <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Email</label>
     <div class="input-group">
  <span class="input-group-text"><i class="fa fa-comments"></i></span>
      <input style="background-color:#333;" type="text" name="email" class="form-control" id="email" value="<?php echo $user->email;?>" placeholder="Email">
    </div>
</div>


<div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Cedula</label>
     <div class="input-group">
  <span class="input-group-text"><i class="fa fa-user"></i></span>
      <input style="background-color:#333;" type="text" name="no" class="form-control" id="lastname" value="<?php echo $user->no;?>" placeholder="Apellido">
    </div>
</div>
  
</div>

  
<div class="row my-2" >
                <div class="col-md-6 col-6">
                  <a href="./?view=employees&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                  <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $user->id;?>">
                 
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
                  url: "./?action=brands&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("empleado Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=brands&opt=all'  }, delay); 
                     
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
     
 <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Genero</label>
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
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text"><i class="fa fa-user"></i></span>
      <input style="background-color:#333;" type="text" name="name" autocomplete="off" autofocus class="form-control" id="name" required placeholder="Nombre">
    </div>
</div>
 <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Apellido</label>
     <div class="input-group">
  <span class="input-group-text"><i class="fa fa-user"></i></span>
      <input style="background-color:#333;" type="text" name="lastname" autocomplete="off" class="form-control" id="lastname" placeholder="Apellido">
    </div>
</div>
     
  

    <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Email</label>
     <div class="input-group">
  <span class="input-group-text"><i class="fa fa-comments"></i></span>
      <input style="background-color:#333;" type="text" name="email" autocomplete="off" class="form-control" id="email"  placeholder="Email">
    </div>
</div>
     

   <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Cedula</label>
     <div class="input-group">
  <span class="input-group-text"><i class="fa fa-user"></i></span>
      <input style="background-color:#333;" type="text" name="no" class="form-control" id="lastname" placeholder="Cedula">
    </div>
</div>

    <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
      <label class="col-md-12 col-12 my-3 control-label"></label>
      <button class="btn btn-warning btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
    </div>
  </div>
</form>

<script>
// Guardar empleados localmente si no hay internet
function guardarempleadoOffline(empleado) {
  let empleados = JSON.parse(localStorage.getItem("empleados_pendientes")) || [];
  empleados.push(empleado);
  localStorage.setItem("empleados_pendientes", JSON.stringify(empleados));
  toastr.info('Guardado localmente. Se enviará cuando vuelva el internet.');
}

// Sincronizar empleados cuando vuelva la conexión
function sincronizarempleados() {
  let empleados = JSON.parse(localStorage.getItem("empleados_pendientes")) || [];
  if (empleados.length > 0 && navigator.onLine) {
    empleados.forEach((empleado, i) => {
      fetch("./?action=employees&opt=add_offline", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(empleado)
})

      .then(res => res.text())
      .then(resp => {
        if (resp.trim() === "OK") {
          empleados.splice(i, 1);
          localStorage.setItem("empleados_pendientes", JSON.stringify(empleados));
        }
      });
    });
  }
}

document.getElementById("add").addEventListener("submit", function(e) {
  e.preventDefault();
  const form = this;
  const empleado = { name: form.name.value };

  if (navigator.onLine) {
    fetch("./?action=employees&opt=add", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams(empleado).toString()
    })
    .then(res => res.text())
    .then(resp => {
      if (resp.trim() === "OK") {
       toastr.success('Registro agregado correctamente.');
        window.location = './?view=employees&opt=all';
      } else {
         toastr.error('Ya existe ese registro.');
      }
    })
    .catch(() => guardarempleadoOffline(empleado));
  } else {
    guardarempleadoOffline(empleado);
  }
});

// Intentar sincronizar cada 5 segundos
setInterval(() => {
  if (navigator.onLine) sincronizarempleados();
}, 5000);
</script>

<?php endif;?>
</div>
</div>
   
	
<?php 
$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT * from user where status=1 and username='' and stock_id=".StockData::getPrincipal()->id;
$query = $con->query($sql);
    if(count($query)>0):?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
    <thead>
      <th>Accion</th>
			<th>Foto</th>
			<th>Nombre</th>
			<th>Sucursal</th>
			<th>Activo</th>
			<th>Tipo</th>
      <th>Accion</th>
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

      <?php while($r = $query->fetch_array()){?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">   
                      <a href="./?view=employees&opt=edit&id=<?php echo $r['id'];?>" class="btn btn-success"><i class="fas fa-edit"></i></a>
                        <a href="./?view=employees&opt=history&id=<?php echo $r['name'];?>" class="btn btn-info"><i class="fas fa-history"></i></a>
                      </div>
        </td>
<td width="5%">
					<?php if($r['image']!=""):
						$url = "CF-SYSTEMS/storage/profiles/".$r['image'];
						if(file_exists($url)){
							echo "<img src='$url' style='width:50%;'>";
						}
					endif;?>
				</td>
				<td><?php echo utf8_decode($r['name']." ".$r['lastname']); ?></td>
				<td><?php if($r['stock_id']<>null): echo StockData::getById($r['stock_id'])->name; endif;?></td>
				<td><?php if($r['status']==1): echo "SI"; else: echo "NO"; endif;?></td>
				<td>
			<?php 
switch (Core::$user->language){
 case 'ES': echo KData::getById($r['kind'])->nombre; break;
 case 'EN': echo KData::getById($r['kind'])->name; break;
}
?>
</td>
        <td class="text-right py-0 align-middle">
            
<?php  
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()){
if ($x['permits_id']==4): ?>
   
                        <a href="./?action=employees&opt=del&id=<?php echo $r['id'];?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();" ><i class="fas fa-trash"> Eliminar</i></a>
                    
                    
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
    <h2>No hay Empleados</h2>
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