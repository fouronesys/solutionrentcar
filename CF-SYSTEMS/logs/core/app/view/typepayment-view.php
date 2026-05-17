<?php if(isset($_GET["opt"]) && $_GET["opt"]=="payment"):?>

<section class="content">
<div class="row">
  <div class="col-12">
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
         <h1 class="m-0"><i class='fa fa-list-ol'></i> Tipo de Pago</a>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cubes'></i> Inventario</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Pago/Forma</a></li>

            </ol>
          </div><!-- /.col -->
   
    </div>
<div class="row my-2">
                <div class="col-6">
                
                  <a href="./?view=typepayment&opt=payment"  class="btn btn-danger btn-block btn-sm"><i class='fa fa-list-ol'></i> Tipos</a>
                </div>
                <div class="col-6">
                   <a href="./?view=typepayment&opt=shape"  class="btn btn-danger btn-block btn-sm"><i class='fa fa-list-ol'></i> Formas</a>
                 
                </div>
              </div>

<div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo y en en titulo del aparatado para crear nuevo.
            </div>

<?php $users = PData::getAll();
    if(count($users)>0){?>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
        <thead>
      <th>Nombre</th>
      </thead>
      <tfoot>
   <tr>
    <th>Nombre</th>
  </tr>
  </tfoot>
      <?php
      foreach($users as $user){
        ?>
        <tr>
             <td><?php echo utf8_decode($user->name); ?></td>
        </tr>
        <?php

      }

?>
      </table>
  </div><!-- /.box-body -->
</div><!-- /.box -->
      
   <?php }else{?>
      
         <div class="card">
              <div class="card-header">
    <h2>No hay Tipo de Pago</h2>
    <p>No se ha realizado ninguna operacion.</p>
    </div>
</div>
  
    <?php } ?>


  </div>
</div>
     </div><!-- /.row -->
      </div><!-- /.container-fluid -->
<script type="text/javascript">
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-6:eq(0)');
</script>
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="newpayment"):
 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
<section class="content">
<div class="row">
  <div class="col-12">
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0"><i class="fa fa-list-ol"></i> Nuevo Tipo de Pago</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cubes'></i> Inventario</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Pago/Forma</a></li>

            </ol>
          </div><!-- /.col -->
</div>
<div class="card">
<div class="card-body">
    <form class="form-horizontal" method="post" id="addpayment" role="form">
  <div class="form-group">
    <label for="inputEmail1" class="col-2  col-2 control-label">Nombre*</label>
    <div class="col-12">
      <input type="text" autocomplete="off" autofocus name="name" required class="form-control" id="name" placeholder="Nombre">
    </div>
  </div>

 <div class="row my-2">
                <div class="col-6">
                  <a href="./?view=typepayment&opt=payment" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-6">
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
            jQuery("#addpayment").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=typepayment&opt=addpayment",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Tipo de Pago Exito!", { sticky: true });
                  $.jGrowl("Se Agrego a la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=typepayment&opt=payment'  }, delay); 
                     
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
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="editpayment"):?>
<section class="content">
<?php $user = PData::getById($_GET["id"]);?>
<div class="row">
  <div class="col-12">
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0"><i class="fa fa-edit"></i> Editar Tipo de Pago</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cubes'></i> Inventario</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Pago/Forma</a></li>

            </ol>
          </div><!-- /.col -->
</div>
<div class="card">
<div class="card-body">
    <form class="form-horizontal" method="post" id="updpayment" role="form">
  <div class="form-group">
    <label for="inputEmail1" class="col-2  col-2 control-label">Nombre*</label>
    <div class="col-12">
      <input type="text" autocomplete="off" autofocus name="name" required class="form-control" value="<?php echo utf8_decode($user->name);?>" placeholder="Nombre">
    </div>
  </div>

 <div class="row my-2">
                <div class="col-6">
                  <a href="./?view=typepayment&opt=payment" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
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
            jQuery("#updpayment").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=typepayment&opt=updpayment",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Forma de Pago Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=typepayment&opt=payment'  }, delay); 
                     
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

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="shape"):?>

<section class="content">
<div class="row">
  <div class="col-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <a href="./?view=typepayment&opt=newshape" style="color: white;"> <h1 class="m-0"><i class='fa fa-list-ol'></i> Forma de Pago</a>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cubes'></i> Inventario</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Pago/Forma</a></li>

            </ol>
          </div><!-- /.col -->
   
    </div>
<div class="row my-2">
                <div class="col-6">
                
                  <a href="./?view=typepayment&opt=payment"  class="btn btn-danger btn-block btn-sm"><i class='fa fa-list-ol'></i> Tipos</a>
                </div>
                <div class="col-6">
                   <a href="./?view=typepayment&opt=shape"  class="btn btn-danger btn-block btn-sm"><i class='fa fa-list-ol'></i> Formas</a>
                 
                </div>
              </div>

<div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo y en en titulo del aparatado para crear nuevo.
            </div>
<?php

      $users = FData::getAll();
    if(count($users)>0){
      // si hay usuarios
      ?>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
      <thead>
      <th width="10%">Accion</th>
      <th>Nombre</th>
      <th width="20%">Accion</th>
    </thead>

    <tfoot>
      <tr>
      <th>Acccion</th>
      <th>Nombre</th>
      <th>Accion</th>
      </tr>
    </tfoot>

      <?php foreach($users as $user): ?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=typepayment&opt=editshape&id=<?php echo $user->id;?>" class="btn btn-success"><i class="fas fa-edit"></i></a>
                      </div>
        </td>
        <td><?php echo $user->name; ?></td>
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=typepayment&opt=delshape&id=<?php echo $user->id;?>" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> Eliminar</i></a>
                    
    <?php endif;?>
<?php endforeach; ?>
</td>
    </tr>
      <?php endforeach; ?>
      </table>
  </div><!-- /.box-body -->
</div><!-- /.box -->
      
      <?php


    }else{
      echo "<p class='alert alert-info'>No hay Forma de Pago</p>";
    }


    ?>


  </div>
</div>
     </div><!-- /.row -->
      </div><!-- /.container-fluid -->
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="newshape"):?>
<section class="content">
<div class="row">
  <div class="col-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-list-ol'></i> Nueva Forma de Pago
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cubes'></i> Inventario</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Pago/Forma</a></li>

            </ol>
          </div><!-- /.col -->
</div>
<div class="card">
<div class="card-body">
 <form class="form-horizontal" method="post" id="addshape" role="form">
  <div class="form-group">
    <label for="inputEmail1" class="col-2  col-2 control-label">Nombre*</label>
    <div class="col-12">
      <input type="text" autocomplete="off" name="name" required class="form-control" autofocus placeholder="Nombre">
    </div>
  </div>
<div class="row my-2" >
                <div class="col-6">
                  <a href="./?view=typepayment&opt=shape" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-6">
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
            jQuery("#addshape").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=typepayment&opt=addshape",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Forma de Pago Exito!", { sticky: true });
                  $.jGrowl("Se Agrego a la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=typepayment&opt=shape'  }, delay); 
                     
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
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="editshape"):?>
<section class="content">
<?php $user = FData::getById($_GET["id"]);?>
<div class="row">
  <div class="col-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-edit'></i> Editar Forma de Pago
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cubes'></i> Inventario</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Pago/Forma</a></li>

            </ol>
          </div><!-- /.col -->
</div>
<div class="card">
<div class="card-body">
    <form class="form-horizontal" method="post" id="updshape" role="form">

  <div class="form-group">
    <label for="inputEmail1" class="col-2 col-2 control-label">Nombre*</label>
    <div class="col-12">
      <input type="text" name="name" value="<?php echo utf8_decode($user->name);?>" class="form-control" id="name" placeholder="Nombre">
    </div>
  </div>

<div class="row my-2">
                <div class="col-6">
                  <a href="./?view=typepayment&opt=shape" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
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
            jQuery("#updshape").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=typepayment&opt=updshape",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Forma de Pago Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=typepayment&opt=shape'  }, delay); 
                     
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