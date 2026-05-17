<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <a href="./?view=spendtype&opt=new" style="color: white;"> <h1 class="m-0"><i class='fa fa-th-list'></i> Tipos de Gastos</a></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
               <li class="breadcrumb-item active"><i class='fa fa-briefcase'></i> Finanzas</a></li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Tipos de Gastos</a></li>

            </ol>
          </div><!-- /.col -->
  
    </div>
<div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo y en en titulo del aparatado para crear nuevo.
            </div>
<?php $users = TGData::getAll();
    if(count($users)>0){?>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
      <thead>
      <th>Nombre</th>
      </thead>

      <tfoot>
      <th>Nombre</th>
      </tfoot>
      <?php foreach($users as $user){?>
        <tr>
        <td><a  style="color: white;" data-toggle="modal" data-target="#myModal<?php echo $user->id; ?>"><?php echo utf8_decode($user->name); ?></a></td>
        
     <div class="modal fade" id="myModal<?php echo $user->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Actualizar / Eliminar</h4>
      </div>
      <div class="modal-body">
 <p style="color: white;">¿Que desea hacer a este tipo de gasto?</p>
<h4>Nombre: <?php echo utf8_decode($user->name); ?></h4>
<div class="row">
               <div class="col-md-6 col-6">
                  <a  href="./?view=spendtype&opt=edit&id=<?php echo $user->id;?>" class="btn btn-success btn-block btn-sm"><i class='fa fa-edit'></i> Actualizar</a>
                </div>
                <div class="col-md-6 col-6">
                  <a href="./?action=spendtype&opt=del&id=<?php echo $user->id;?>" class="btn btn-warning btn-block btn-sm"><i class='fa fa-trash'></i> Eliminar</a>
                </div>
</div>             

      </div>
    </div>
  </div>

</div>
        
      
    </tr>
    
        <?php

      }

?>
      </table>
  </div><!-- /.box-body -->
</div><!-- /.box -->
      
      <?php


    }else{?>
         <div>
         <div class="card">
              <div class="card-header">
    <h2>No hay Tipo de Gasto</h2>
    <p>No se ha realizado ninguna operacion.</p>
    </div>
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
    }).buttons().container().appendTo('#example1_wrapper .col-md-6 col-6:eq(0)');
</script>
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="new"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-th-list'></i> Nuevo Tipos de Gastos</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
               <li class="breadcrumb-item active"><i class='fa fa-briefcase'></i> Finanzas</a></li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Tipos de Gastos</a></li>

            </ol>
          </div><!-- /.col -->
  
    </div>
    <div class="card">
<div class="card-body">
    <form class="form-horizontal" method="post" id="addspends" role="form">
  <div class="form-group">
    <label for="inputEmail1" class="col-md-2 col-6 control-label">Nombre*</label>
    <div class="col-md-12">
      <input type="text" name="name" required class="form-control" id="name" placeholder="Nombre">
    </div>
  </div>

<div class="row my-2" >
                <div class="col-md-6 col-6">
                  <a href="./?view=spendtype&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
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
            jQuery("#addspends").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=spendtype&opt=add",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Tipo de Gastos Exito!", { sticky: true });
                  $.jGrowl("Se Agrego a la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=spendtype&opt=all'  }, delay); 
                     
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
<?php $user = TGData::getById($_GET["id"]);?>
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-edit'></i> Editar Tipos de Gastos</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
               <li class="breadcrumb-item active"><i class='fa fa-briefcase'></i> Finanzas</a></li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Tipos de Gastos</a></li>

            </ol>
          </div><!-- /.col -->
  
    </div>
    <div class="card">
<div class="card-body">
    <form class="form-horizontal" method="post" id="updspends"  role="form">
  <div class="form-group">
    <label for="inputEmail1" class="col-md-2 col-2 control-label">Nombre*</label>
    <div class="col-md-12">
      <input type="text" name="name" value="<?php echo utf8_decode($user->name);?>" class="form-control" id="name" placeholder="Nombre">
    </div>
  </div>

 <div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=spendtype&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
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
            jQuery("#updspends").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=spendtype&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Tipo de Gastos Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=spendtype&opt=all'  }, delay); 
                     
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