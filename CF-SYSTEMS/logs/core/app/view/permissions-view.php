<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
<div class="row">
	<div class="col-md-12">
	<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-12 col-sm-5 col-md-5 col-lg-6 col-xl-6"><h1 class="m-0"><i class='fa fa-list-ol'></i> Permisos  Usuario</h1> 
          </div><!-- /.col -->
          <div class="col-12 col-sm-7 col-md-7 col-lg-6 col-xl-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cog'></i> Administracion</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Permisos Usuario</li>

            </ol>
          </div><!-- /.col -->
        
    </div>

<div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo y en en titulo del aparatado para crear nuevo.
            </div>

		<?php $users = PUData::getAll();
		if(count($users)>0){?>
			
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
			<thead>
			<th>Nombre</th>
			<th>Ubicacion</th>
			</thead>
			<?php
			foreach($users as $user){?>
				<tr>
				
				<td><?php echo utf8_decode($user->name); ?></td>
      	<td><?php echo $user->location; ?></td>

				</tr>
				<?php

			}
 echo "</table></div></div></div>";


		}else{?>
      
         <div class="card">
              <div class="card-header">
    <h2>No hay Permisos</h2>
    <p>No se ha realizado ninguna operacion.</p>
    </div>
</div>
  
    <?php } ?>


	</div>
</div>
</div><!-- /.row -->
      </div><!-- /.container-fluid -->
<script type="text/javascript">
    $("#example1").DataTable();
</script>
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="new"):?>

<section class="content">
    <?php $categories = PUData::getAll();?>
<div class="row">
  <div class="col-md-12">
  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-list-ol'></i> Nuevo Permiso</h1> 
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cog'></i> Administracion</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Permisos del Usuario</a></li>

            </ol>
          </div><!-- /.col -->
        
    </div>
      <div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Los Campos con (*) son requeridos los demas lo puedes dejar vacio en caso de no necesitar.
            </div>
 <div class="card">
<div class="card-body">
    <form class="form-horizontal" method="post" enctype="multipart/form-data" id="addpermission" role="form">

  <div class="form-group">
    <label for="inputEmail1" class="col-md-2 control-label">Nombre <span class="text-danger">*</span></label>
    <div class="col-md-12">
      <input type="text" autocomplete="off" name="name" required class="form-control" id="name" placeholder="Nombre del Permiso">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-md-2 control-label">Ubicacion</label>
    <div class="col-md-12">
    <select name="location" class="form-control">
    <option value="">-- NINGUNA --</option>
    <?php foreach($categories as $category):?>
      <option value="<?php echo $category->name;?>"><?php echo $category->location;?></option>
    <?php endforeach;?>
      </select>    </div>
  </div>

 <div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=permissions&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
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
<script>
            jQuery(document).ready(function(){
            jQuery("#addpermission").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=permissions&opt=add",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Permiso Exito!", { sticky: true });
                  $.jGrowl("Se Agrego a la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=permissions&opt=all'  }, delay); 
                     
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
<?php
$product = PUData::getById($_GET["id"]);

if($product!=null):?>
<div class="row">
	<div class="col-md-12">
  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-edit'></i> Editar Permiso</h1> 
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cog'></i> Administracion</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Permisos del Usuario</a></li>

            </ol>
          </div><!-- /.col -->
        
    </div>
      <div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Los Campos con (*) son requeridos los demas lo puedes dejar vacio en caso de no necesitar.
            </div>
 <div class="card">
<div class="card-body">
		<form class="form-horizontal" method="post" id="updpermission" enctype="multipart/form-data" role="form">

  
<div class="row">
   <div class="col-md-6">
    <div class="form-group">
    <label for="inputEmail1" class="col-md-12 control-label">Nombre <span class="text-danger">*</span></label>
      <input type="text" name="name" class="form-control" id="name" value="<?php echo $product->name; ?>" placeholder="Nombre">
    </div>
  </div>
  
   <div class="col-md-6">
    <div class="form-group">
    <label for="inputEmail1" class="col-md-12 control-label">Ubicacion</label>
  <input type="text" name="location" class="form-control"  value="<?php echo $product->location; ?>" placeholder="Ubicacion"> </div>
  </div>
  </div>
 
<div class="row my-2">
    <div class="col-md-4">
  <div class="form-group">
    <label for="inputEmail1" class="col-md-4 control-label" >Esta activo</label>
    <input type="checkbox" name="is_active" <?php if($product->is_active){ echo "checked";}?>>
    </div>
  </div>

                <div class="col-md-4 col-4">
                  <a href="./?view=permissions&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-4 col-4">
                      <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>

</form>
</div>
</div>
</div>
	</div>
</div>
<?php endif; ?>

<script>
            jQuery(document).ready(function(){
            jQuery("#updpermission").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=permissions&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Permiso Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=permissions&opt=all'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Actualizo' });
                  }
                  }
                });
                return false;
              });
            });
            </script>
</section>

<script type="text/javascript">
    $("#example1").DataTable();
</script>


<?php endif; ?>