<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-search'></i> 
           <?php echo (Core::$user->language=='ES') ? "Data Credito" : "Credit Data"; ?> 
           <small>Global</small></h1>
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
              <div class="card"  style="background-color:#222;">
<div class="card-body">

 <form class="form-horizontal" method="post" id="add" role="form">
<div class="row">
      <div class="col-md-4 col-12">
    <label class="col-md-12 col-12 control-label"><?php echo (Core::$user->language=='ES') ? "Cedula" : "ID"; ?></label>
      <input style="background-color:#333;" type="text" name="no" autocomplete="off" required class="form-control" placeholder="<?php echo (Core::$user->language=='ES') ? "Cedula" : "ID"; ?>">
    </div>
    
      <div class="col-md-4 col-12">
    <label class="col-md-12 col-12 control-label"><?php echo (Core::$user->language=='ES') ? "Direccion" : "Address"; ?></label>
      <input style="background-color:#333;" type="text" name="address" autocomplete="off" required class="form-control" placeholder="<?php echo (Core::$user->language=='ES') ? "Direccion" : "Address"; ?>">
    </div>
    
     <div class="col-md-4 col-12">
    <label class="col-md-12 col-12 control-label"><?php echo (Core::$user->language=='ES') ? "Fecha Nacimiento" : "Date of Birth"; ?></label>
      <input style="background-color:#333;" type="date" name="nacimiento" autocomplete="off" required class="form-control">
    </div>
    
    <div class="col-md-4 col-12">
    <label class="col-md-12 col-12 control-label"><?php echo (Core::$user->language=='ES') ? "Nacionalidad" : "Nationality"; ?></label>
      <input style="background-color:#333;" type="text" name="nacionalidad" autocomplete="off" required class="form-control" placeholder="<?php echo (Core::$user->language=='ES') ? "Nacionalidad" : "Nationality"; ?>">
    </div>
    
    <div class="col-md-4 col-12">
    <label class="col-md-12 col-12 control-label"><?php echo (Core::$user->language=='ES') ? "Genero" : "Gender"; ?></label>
      <select style="background-color:#333;" name="gender" class="form-control">
      <option value="<?php echo (Core::$user->language=='ES') ? "Hombre" : "Man"; ?>"><?php echo (Core::$user->language=='ES') ? "Hombre" : "Man"; ?></option>
      <option value="<?php echo (Core::$user->language=='ES') ? "Mujer" : "Woman"; ?>"><?php echo (Core::$user->language=='ES') ? "Mujer" : "Woman"; ?></option>
      </select>
    </div>
   
   <div class="col-md-4 col-12">
    <label class="col-md-12 col-12 control-label"><?php echo (Core::$user->language=='ES') ? "Nombre Completo" : "Full Name"; ?></label>
      <input style="background-color:#333;" type="text" name="name" autocomplete="off" required class="form-control" placeholder="<?php echo (Core::$user->language=='ES') ? "Nombre Completo" : "Full Name"; ?>">
    </div>
    
    
       <div class="col-md-8 col-12">
    <label class="col-md-12 col-12 control-label"><?php echo (Core::$user->language=='ES') ? "Comentario" : "Comment"; ?></label>
      <input style="background-color:#333;" type="text" name="comment" autocomplete="off" required class="form-control" placeholder="<?php echo (Core::$user->language=='ES') ? "Comentario" : "Comment"; ?>">
      <input type="hidden" name="user_id" value="<?php echo UserData::getById($_SESSION['user_id'])->id;?>">
    </div>
               
                <div class="col-md-4 col-4">
    <label class="col-md-12 col-12 my-3 control-label"></label>
    <button class="btn btn-warning btn-block btn-sm"><i class="fa fa-check"></i> <?php echo (Core::$user->language=='ES') ? "Agregar" : "Add"; ?></button>
                </div>
              </div>
</form>


<script>
jQuery(document).ready(function(){
  jQuery("#add").submit(function(e){
    e.preventDefault();
    var formData = jQuery(this).serialize();
    $.ajax({
      type: "POST",
      url: "./?action=data&opt=add",
      data: formData,
      success: function(html){
        if(html=='true'){
          $.jGrowl("Agregado Exito!", { sticky: true });
          $.jGrowl("Se Agrego la Base Datos", { header: 'Acceso permitido' });
          setTimeout(function(){ window.location = './?view=data&opt=all' }, 1000); 
        }else{
          $.jGrowl("No se Puede duplicar datos", { header: 'Error al Agregar' });
        }
      }
    });
    return false;
  });
});
</script>

</div>
</div>

<?php 
$host = "localhost";
$usuario = "u144787244_datarentcar";
$password = "DataRentcar01";
$base_datos1 = "u144787244_datarentcar";

// Conexión
$conn = new mysqli($host, $usuario, $password, $base_datos1);
if ($conn->connect_errno) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT SQL_BIG_RESULT * FROM person";
$query = $conn->query($sql);

if ($query && $query->num_rows > 0): ?>
<div class="card"  style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
 <thead style="background-color: #333;">
  <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Cedula" : "ID"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Direccion" : "Address"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Nacimiento" : "Birth"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Nacionalidad" : "Nationality"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Genero" : "Gender"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Nombre" : "Name"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Comentario" : "Comment"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Empresa" : "Company"; ?></th>
  </tr>
 </thead>
 <tbody>
      <?php while($r = $query->fetch_assoc()): ?>
        <tr>
          <td><?php echo $r['no']; ?></td>
          <td><?php echo $r['address']; ?></td>
          <td><?php echo $r['nacimiento']; ?></td>
          <td><?php echo $r['nacionalidad']; ?></td>
          <td><?php echo $r['gender']; ?></td>
          <td><?php echo $r['name']; ?></td>
          <td><?php echo $r['comment']; ?></td>
          <td><?php echo $r['user_id']; ?></td>
        </tr>
      <?php endwhile; ?>
 </tbody>
</table>
</div>
</div>
</div>
<?php else: ?>
<div class="card">
  <div class="card-header">
    <h2>No hay datos</h2>
    <p>No se ha realizado ninguna operación.</p>
  </div>
</div>
<?php endif; ?>
</div>
</div>
</div>
</div>
</div>
</section>
<?php endif; ?>
