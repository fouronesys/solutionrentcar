<section class="content">
<div class="row">
  <div class="col-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-list-ol'></i> Comprobantes</h1>
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
          
<?php if($_GET['id']>0): $user = CData::getById($_GET["id"]);?>
<div class="card" style="background-color:#222;">
<div class="card-body">
    <form class="form-horizontal" method="post" id="upd" role="form">
 <div class="row">
      <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre</label>
      <input style="background-color:#333;" type="text" readonly value="<?php echo utf8_decode($user->name);?>"  class="form-control" >
    </div>
    
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Desde</label>
      <input style="background-color:#333;" type="numbers" name="de" autocomplete="off"  value="<?php echo $user->de;?>"  class="form-control">
    </div>
    
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Hasta</label>
      <input style="background-color:#333;" type="numbers" name="hasta" autocomplete="off"  value="<?php echo $user->hasta;?>"  class="form-control">
    </div>
    
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vencimiento</label>
      <input style="background-color:#333;" type="date" name="expiration" autocomplete="off"  value="<?php echo $user->expiration;?>" class="form-control">
    </div>

    
               
                <div class="col-md-12 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 control-label"></label>
                  <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $user->id;?>">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
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
                  url: "./?action=receipt&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Comprobante Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=receipt'  }, delay); 
                     
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
      
<?php endif;?>
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

<div class="card"  style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
      <thead>
      <th>Editar</th>
      <th>Serie</th>
      <th>Nombre</th>
      <th>Desde</th>
      <th>Hasta</th>   
      <th>Vencimiento</th>
      </thead>

      <tfoot>
      <th>Editar</th>
      <th>Serie</th>
      <th>Nombre</th>
      <th>Desde</th>
      <th>Hasta</th>   
      <th>Vencimiento</th>
      </tfoot>
      <?php foreach(CData::getAll() as $c):?>

       <tr>
       <td> <a href="./?view=receipt&id=<?php echo $c->id;?>" class="btn btn-block btn-warning"><i class="fas fa-edit"></i></a></td>
       <td><?php echo strtoupper($c->serie."".$c->indicator);?></td>
       <td><?php echo strtoupper($c->name);?></td>
       <td><?php echo $c->de;?></td>
       <td><?php echo $c->hasta;?></td>
       <td><?php echo $c->expiration; ?></td>
            
   
<!--///////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
        </tr>
        <?php endforeach;?>

      </table>
      </div>
      </div>
      
  </div>
</div>
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
<script type="text/javascript">
    $("#example2").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example2_wrapper .col-6:eq(0)');
</script>
</section>
