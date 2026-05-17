<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
<div class="row">
	<div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-building'></i> Rent Car</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-building'></i> Rent Car</li>

            </ol>
          </div><!-- /.col -->
       
    </div>

<div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo y en en titulo del aparatado para crear nuevo.
            </div>
		<?php $users = StockData::getAll();
		if(count($users)>0): ?>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
			<thead>
			<th>Accion</th>
			<th>Nombre</th>
			<th>Direccion</th>
			<th>Telefono</th>
			<th>Celular</th>
			<th>WEB</th>
			<th>Redes Soc.</th>
			<th>Email</th>
			<th>Principal</th>
			</thead>

      <tfoot>
      <tr>
      <th>Accion</th>
      <th>Nombre</th>
      <th>Direccion</th>
      <th>Telefono</th>
      <th>Celular</th>
      <th>WEB</th>
      <th>Redes Soc.</th>
      <th>Email</th>
      <th>Principal</th>
      </tr>
      </tfoot>

			<?php foreach($users as $user):?>
				<tr>
				<td><a href="./?view=stocks&opt=edit&id=<?php echo $user->id;?>" class="btn btn-success btn-block"><i class="fa fa-edit"></i></a></td>

				<td><?php echo utf8_decode($user->name." ".$user->lastname); ?></td>
				<td><?php echo $user->address;?></td>
        <td><?php echo $user->phone; ?></td>
        <td><?php echo $user->phone2; ?></td>
        <td><?php echo $user->field1; ?></td>
        <td><?php echo $user->field2; ?></td>
        <td><?php echo $user->email; ?></td>
        <td><?php if($user->is_principal):?>
          <i class="fa fa-check"></i>
        <?php else:?>        
          <?php endif;?></td>
				</tr>
				<?php endforeach;?>
			</table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->
			
			   <?php else:?>
      
         <div class="card">
              <div class="card-header">
    <h2>No hay Stock</h2>
    <p>No se ha realizado ninguna operacion.</p>
    </div>
</div>
  
    <?php endif; ?>


	</div>
</div>
 </div><!-- /.row -->
      </div><!-- /.container-fluid -->
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="new"):?>

<section class="content">
<div class="row">
	<div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-building"></i> Nueva Rent Car
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-building'></i> Rent Car</li>

            </ol>
          </div><!-- /.col -->
       
 
</div>
 <p class="alert alert-info">* Campos obligatorios</p>
 <div class="card">
<div class="card-body">
		<form class="form-horizontal" method="post" id="addstock" role="form">

<div class="row">
     
  <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Ubicacion</label>
      <?php $clients = LocationData::getAll();?>
    <select name="location" required class="form-control" id="location" >
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

    <div class="col-md-6  col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre <span class="text-danger">*</span></label>
      <div class="input-group">
<span class="input-group-text"><i class="fa fa-user"></i></span>
    <input type="text" name="name" required class="form-control" id="name" placeholder="Nombre">
    </div>
  </div>

</div>

<div class="row">
     
    <div class="col-md-12 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Direccion <span class="text-danger">*</span></label>
    <div class="input-group">
<span class="input-group-text"><i class="fa fa-street-view"></i></span>
     <input type="text" name="address"  required class="form-control" id="name" placeholder="Direccion">
    </div>
  </div>

</div>

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Whatsapp <span class="text-danger">*</span></label>
    <div class="input-group">
<span class="input-group-text"><i class="fa fa-comment"></i></span>
     <input type="text" name="phone"  class="form-control" placeholder="Whatsapp" data-inputmask='"mask": "(999) 999-9999"' data-mask>
    </div>
  </div>

    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Celular </label>
      <div class="input-group">
<span class="input-group-text"><i class="fa fa-phone"></i></span>
   <input type="text" name="phone2"  class="form-control"  placeholder="Telefono" data-inputmask='"mask": "(999) 999-9999"' data-mask>
    </div>
  </div>

</div>
  
  <div class="row">

    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">WEB </label>
      <div class="input-group">
<span class="input-group-text"><i class="fa fa-globe"></i></span>
  <input type="text" name="field1"  class="form-control" id="name" placeholder="Pagina Web">
    </div>
  </div>

     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Redes Social</label>
    <div class="input-group">
<span class="input-group-text"><i class="fa fa-comment"></i></span>
    <input type="text" name="field2"  class="form-control"  placeholder="Instagram">
    </div>
  </div>


</div>

 <div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=stocks&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
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
            jQuery("#addstock").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=stocks&opt=add",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Rent Car Exito!", { sticky: true });
                  $.jGrowl("Se Agrego a la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=stocks&opt=all'  }, delay); 
                     
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

<?php $stock = StockData::getById($_GET["id"]);?>
<section class="content">
<div class="row">
	<div class="col-md-12">
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-building"></i> Editar Rent Car
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cubes'></i> Inventario</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Rent Car</a></li>

            </ol>
          </div><!-- /.col -->
       
 
</div>
 <p class="alert alert-info">* Campos obligatorios</p>
 <div class="card">
<div class="card-body">
<form class="form-horizontal" method="post" id="updstock" role="form">

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Ubicacion</label>
      <?php $clients = LocationData::getAll();?>
    <select name="location" required class="form-control" id="location" >
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($client->id==$stock->location){ echo "selected"; }?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre <span class="text-danger">*</span></label>
      <div class="input-group">
<span class="input-group-text"><i class="fa fa-user"></i></span>
    <input type="text" name="name" required class="form-control" value="<?php echo $stock->name; ?>" placeholder="Nombre">
    </div>
  </div>

</div>

<div class="row">
     
    <div class="col-md-12 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Direccion <span class="text-danger">*</span></label>
    <div class="input-group">
<span class="input-group-text"><i class="fa fa-street-view"></i></span>
     <input type="text" name="address"  required class="form-control" value="<?php echo $stock->address; ?>" placeholder="Direccion">
    </div>
  </div>

</div>

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Whatsapp <span class="text-danger">*</span></label>
    <div class="input-group">
<span class="input-group-text"><i class="fa fa-comment"></i></span>
     <input type="text" name="phone"  class="form-control" placeholder="Whatsapp" value="<?php echo $stock->phone; ?>" data-inputmask='"mask": "(999) 999-9999"' data-mask>
    </div>
  </div>

    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Celular </label>
      <div class="input-group">
<span class="input-group-text"><i class="fa fa-phone"></i></span>
   <input type="text" name="phone2"  class="form-control" value="<?php echo $stock->phone2; ?>"   placeholder="Telefono" data-inputmask='"mask": "(999) 999-9999"' data-mask>
    </div>
  </div>

</div>
  
  <div class="row">
     

    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">WEB </label>
      <div class="input-group">
<span class="input-group-text"><i class="fa fa-globe"></i></span>
  <input type="text" name="field1"  class="form-control" value="<?php echo $stock->field1; ?>" placeholder="Pagina Web">
    </div>
  </div>

    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Redes Social</label>
    <div class="input-group">
<span class="input-group-text"><i class="fa fa-comment"></i></span>
    <input type="text" name="field2"  class="form-control" value="<?php echo $stock->field2; ?>"  placeholder="Instagram">
    </div>
  </div>
</div>

 <div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=stocks&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                  <input type="hidden" name="id" value="<?php echo $stock->id;?>">
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
            jQuery("#updstock").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=stocks&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Rent Car Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=stocks&opt=all'  }, delay); 
                     
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