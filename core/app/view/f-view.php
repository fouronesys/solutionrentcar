<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>

<section class="content">
<div class="row">
  <div class="col-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <a href="./?view=f&opt=new" style="color: white;"> <h1 class="m-0"><i class='fa fa-list-ol'></i> Forma de Pago</a>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cubes'></i> Extras</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Forma de Pago</a></li>

            </ol>
          </div><!-- /.col -->
   
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
$sql = "select SQL_BIG_RESULT * from f";
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
                        <a href="./?view=f&opt=all&id=<?php echo $r['id'];?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                      </div>
        </td>

        <td><?php echo $r['name']; ?></td>
        <td class="text-right py-0 align-middle">
            
<?php  
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()){
if ($x['permits_id']==4): ?>
   
                        <a href="./?action=f&opt=del&id=<?php echo $r['id'];?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();" ><i class="fas fa-trash"> Eliminar</i></a>
                    
                    
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
    <h2>No hay Forma de Pago</h2>
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
           <h1 class="m-0"><i class='fa fa-list-ol'></i> Nueva Forma de Pago
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cubes'></i> Extras</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Forma de Pago</a></li>

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
                  <a href="./?view=f&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
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
                  url: "./?action=f&opt=add",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Forma de Pago Exito!", { sticky: true });
                  $.jGrowl("Se Agrego a la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=f&opt=all'  }, delay); 
                     
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
                  <a href="./?view=f&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
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
                  url: "./?action=f&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Forma de Pago Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=f&opt=all'  }, delay); 
                     
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