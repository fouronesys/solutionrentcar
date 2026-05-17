<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>

<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-edit'></i> Listado de Ofertas</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-edit'></i> Ofertas</li>
           
            </ol>
          </div><!-- /.col -->
    
    </div>
<div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo.
            </div>
            
<?php $users = TariffData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id." group by brand_id");
    if(count($users)>0):?>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      <th>Accion</th>
      <th>Paquete</th>
      <th>Vehiculo</th>
      <th>Accion</th>
    </thead>

    <tfoot>
      <tr>
      <th>Accion</th>
      <th>Paquete</th>
      <th>Vehiculo</th>
      <th>Accion</th>
      </tr>
    </tfoot>

      <?php foreach($users as $user): ?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=tariff&opt=edit&id=<?php echo $user->id;?>&cars=<?php echo $user->getCars()->id;?>" class="btn btn-success"><i class="fas fa-edit"></i></a>
                      </div>
        </td>

        <td><?php echo $user->getPackage()->name; ?></td>
        <td><?php $brand = BrandData::getById($user->getCars()->brand_id); echo $brand->name." ".$user->getCars()->name." ".$user->getCars()->year." - ".$user->getCars()->chassis; ?></td>
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=tariff&opt=del&id=<?php echo $user->id;?>" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> Eliminar</i></a>
                    
    <?php endif;?>
<?php endforeach; ?>
</td>
    </tr>
    
    <?php endforeach; ?>
      </table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->
      <?php else:?>
     
         <div class="card">
              <div class="card-header">
    <h2>No hay Ofertas</h2>
    <p>No se ha realizado ninguna operacion.</p>
    </div>
</div>
  
   <?php endif;?>



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
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-edit'></i> Agregar Oferta</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-edit'></i> Ofertas</li>
               
            </ol>
          </div><!-- /.col -->
        </div>
          <div class="card">
<div class="card-body">
    <form class="form-horizontal" method="post" id="addtariff" role="form">
 <div class="row">
    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Paquete</label>
      <?php $clients = PackageData::getAll();?>
    <select name="package_id" class="form-control" required >
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vehiculo</label>
      <?php $clients = CarsData::getAllbySQL("where stock_id=".StockData::getPrincipal()->id);?>
    <select name="brand_id" class="form-control select2" required >
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->getBrand()->name." ".$client->name." ".$client->year." ".$client->getExColor()->name." - ".$client->chassis;?></option>
    <?php endforeach;?>
      </select>
    </div>

      <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Precio Normal</label>
      <input type="number" name="price_normal" autocomplete="off"  class="form-control" placeholder="Precio Normal">
    </div>

    
<div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Precio (Sabado-Domingo)</label>
      <input type="number" name="price_weekend" autocomplete="off"  class="form-control" placeholder="Precio (Sabado-Domingo)">
    </div>


<div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Precio Pico</label>
      <input type="number" name="price_peak" autocomplete="off"  class="form-control" placeholder="Precio Pico">
    </div>
                <div class="col-md-2 col-6">

    <label for="inputEmail1" class="col-md-12 col-12 my-3 control-label"></label>
                  <a href="./?view=tariff&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-2 col-6">

    <label for="inputEmail1" class="col-md-12 col-12 my-3 control-label"></label>
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
</form>

<style type="text/css"> 
.select2.select2-container {
  width: 100% !important;
}

.select2.select2-container .select2-selection {
  border: 1px solid #ccc;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 5px;
  height: 37px;
  margin-bottom: 15px;
  outline: none !important;
  transition: all .15s ease-in-out;
  background: #343a40;
}

.select2.select2-container .select2-selection .select2-selection__rendered {
  color: white;
  line-height: 32px;
  padding-right: 33px;
}

.select2.select2-container .select2-selection .select2-selection__arrow {
  background: #343a40;
  border-left: 1px solid #ccc;
  -webkit-border-radius: 0 3px 3px 0;
  -moz-border-radius: 0 3px 3px 0;
  border-radius: 0 3px 3px 0;
  height: 32px;
  width: 33px;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--single {
  background: #343a40;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--single .select2-selection__arrow {
  -webkit-border-radius: 0 3px 0 0;
  -moz-border-radius: 0 3px 0 0;
  border-radius: 0 3px 0 0;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--multiple {
  border: 1px solid #34495e;
}

.select2.select2-container .select2-selection--multiple {
  height: auto;
  min-height: 34px;
}

.select2.select2-container .select2-selection--multiple .select2-search--inline .select2-search__field {
  margin-top: 0;
  height: 32px;
}

.select2.select2-container .select2-selection--multiple .select2-selection__rendered {
  display: block;
  padding: 0 4px;
  line-height: 29px;
}

.select2.select2-container .select2-selection--multiple .select2-selection__choice {
  background-color: #f8f8f8;
  border: 1px solid #ccc;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  margin: 4px 4px 0 0;
  padding: 0 6px 0 22px;
  height: 24px;
  line-height: 24px;
  font-size: 12px;
  position: relative;
}

.select2.select2-container .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove {
  position: absolute;
  top: 0;
  left: 0;
  height: 22px;
  width: 22px;
  margin: 0;
  text-align: center;
  color: #e74c3c;
  font-weight: bold;
  font-size: 16px;
}

.select2-container .select2-dropdown {
  background: transparent;
  border: none;
  margin-top: -5px;
}

.select2-container .select2-dropdown .select2-search {
  padding: 0;
}

.select2-container .select2-dropdown .select2-search input {
  outline: none !important;
  border: 1px solid #34495e !important;
  border-bottom: none !important;
  padding: 4px 6px !important;
}

.select2-container .select2-dropdown .select2-results {
  padding: 0;
}

.select2-container .select2-dropdown .select2-results ul {
  background: #343a40;
  border: 1px solid #34495e;
}

.select2-container .select2-dropdown .select2-results ul .select2-results__option--highlighted[aria-selected] {
  background-color: #3498db;
}
</style>

</div>
</div>

</div>
</div>
</div>
  </div>
</div>

<script>
            jQuery(document).ready(function(){
            jQuery("#addtariff").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=tariff&opt=add",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Oferta Exito!", { sticky: true });
                  $.jGrowl("Se Agrego la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=tariff&opt=all'  }, delay); 
                     
                  }else{
                  $.jGrowl("No se Puede duplicar datos", { header: 'Error al Agregar' });
                  }
                  }
                });
                return false;
              });
            });
            </script>

</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"):?>
<?php $user = TariffData::getById($_GET["id"]);?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0"><i class='fa fa-edit'></i>Editar Oferta</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
               <li class="breadcrumb-item"><i class='fa fa-edit'></i> Ofertas</li>

            </ol>
          </div><!-- /.col -->
        </div>
          <div class="card">
<div class="card-body">
   <form class="form-horizontal" method="post" id="updtariff" role="form">
 <div class="row">
    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Paquete</label>
      <?php $clients = PackageData::getAll();?>
    <select name="package_id" class="form-control" required >
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->package_id!=null&& $user->package_id==$client->id){ echo "selected";}?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vehiculo</label>
      <?php $clients = CarsData::getAll();?>
    <select name="brand_id" class="form-control" required >
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->brand_id!=null&& $user->brand_id==$client->id){ echo "selected";}?>><?php echo $client->getBrand()->name." ".$client->name." ".$client->year." ".$client->getExColor()->name." - ".$client->chassis;?></option>
    <?php endforeach;?>
      </select>
    </div>
<?php foreach(TariffData::getAllBySQL("where brand_id=".$user->brand_id) as $tariff):?>
      <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Precio <?php echo $tariff->description;?></label>
      <input type="number" name="price[]" autocomplete="off" value="<?php echo $tariff->price;?>"  class="form-control" placeholder="Precio <?php echo $tariff->description;?>">
    </div>
    
  <input type="hidden" name="user_id[]" value="<?php echo $tariff->id;?>">
<?php endforeach;?>
  
                <div class="col-md-2 col-6">

    <label for="inputEmail1" class="col-md-12 col-12 my-3 control-label"></label>
                  <a href="./?view=tariff&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-2 col-6">

    <label for="inputEmail1" class="col-md-12 col-12 my-3 control-label"></label>

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
            jQuery("#updtariff").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=tariff&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Oferta Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=tariff&opt=all'  }, delay); 
                     
                  }else{
                  $.jGrowl("No se Puede duplicar datos", { header: 'Error al Agregar' });
                  }
                  }
                });
                return false;
              });
            });
            </script>

</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="package"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-edit'></i> Listado de Paquete</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-edit'></i> Ofertas</li>
               
            </ol>
          </div><!-- /.col -->
        </div>
          <div class="card">
<div class="card-body">
<?php if($_GET['id']>0): $user = PackageData::getById($_GET["id"]);?>
    <form class="form-horizontal" method="post" id="updpackage" role="form">
 <div class="row">
      <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre</label>
      <input type="text" name="name" autocomplete="off"  value="<?php echo utf8_decode($user->name);?>"  class="form-control" placeholder="Nombre del Paquete">
    </div>

    
<div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Km Gratis</label>
      <input type="number" name="free" autocomplete="off"  value="<?php echo $user->free;?>"  class="form-control" placeholder="Km Gratis">
    </div>

               
                <div class="col-md-4 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 my-3 control-label"></label>
                  <input type="hidden" name="user_id" value="<?php echo $user->id;?>">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
</form>


<script>
            jQuery(document).ready(function(){
            jQuery("#updpackage").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=tariff&opt=updpackage",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Paquete Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=tariff&opt=package'  }, delay); 
                     
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
<form class="form-horizontal" method="post" id="addpackage" role="form">
 <div class="row">
      <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre</label>
      <input type="text" name="name" autocomplete="off"  class="form-control" placeholder="Nombre del Paquete">
    </div>

    
<div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Km Gratis</label>
      <input type="number" name="free" autocomplete="off"  class="form-control" placeholder="Km Gratis">
    </div>

               
                <div class="col-md-4 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 my-3 control-label"></label>
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
</form>


<script>
            jQuery(document).ready(function(){
            jQuery("#addpackage").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=tariff&opt=addpackage",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Paquete Exito!", { sticky: true });
                  $.jGrowl("Se Agrego la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=tariff&opt=package'  }, delay); 
                     
                  }else{
                  $.jGrowl("No se Puede duplicar datos", { header: 'Error al Agregar' });
                  }
                  }
                });
                return false;
              });
            });
            </script>

<?php endif;?>
</div>
</div>

<?php $users = PackageData::getAll();
    if(count($users)>0):?>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      <th>Accion</th>
      <th>Nombre</th>
      <th>Km Gratis</th>
      <th>Accion</th>
    </thead>

    <tfoot>
      <tr>
      <th>Accion</th>
      <th>Nombre</th>
      <th>Km Gratis</th>
      <th>Accion</th>
      </tr>
    </tfoot>

      <?php foreach($users as $user): ?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=tariff&opt=package&id=<?php echo $user->id;?>" class="btn btn-success"><i class="fas fa-edit"></i></a>
                      </div>
        </td>

        <td><?php echo $user->name; ?></td>
        <td><?php echo $user->free; ?></td>
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=tariff&opt=delpackage&id=<?php echo $user->id;?>" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> Eliminar</i></a>
                    
    <?php endif;?>
<?php endforeach; ?>
</td>
    </tr>
    
    <?php endforeach; ?>
      </table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->
      <?php else:?>
     
         <div class="card">
              <div class="card-header">
    <h2>No hay Ofertas</h2>
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