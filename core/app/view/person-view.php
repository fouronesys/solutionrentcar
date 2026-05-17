<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-users'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Clientes"; break;
 case 'EN': echo "Customers"; break;
}
?> </h1>
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
function actualizarReloj() {
  const reloj = document.getElementById("reloj");
  if (!reloj) return;

  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  reloj.textContent = `${horas}:${minutos}:${segundos}`;
}
document.addEventListener("DOMContentLoaded", function() {
  actualizarReloj();
  setInterval(actualizarReloj, 1000);
});
</script>
    
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
$sql = "select SQL_BIG_RESULT * from person where stock_id=".StockData::getPrincipal()->id;
$query = $con->query($sql);
if($query && $query->num_rows > 0):?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Nombre"; break;
 case 'EN': echo "Name"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Cedula"; break;
 case 'EN': echo "ID"; break;
}
?></th>
<?php if(StockData::getPrincipal()->method==1):?>
      
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion"; break;
 case 'EN': echo "Address"; break;
}
?> (DOM)</th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion"; break;
 case 'EN': echo "Address"; break;
}
?> (USD)</th>

<?php endif; if(StockData::getPrincipal()->method==2):?>
     
<th><?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion"; break;
 case 'EN': echo "Address"; break;
}
?></th>

<?php endif; ?>

      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Licencia"; break;
 case 'EN': echo "License"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Pasaporte"; break;
 case 'EN': echo "Passport"; break;
}
?></th>
      <th>TEL: (USD)</th>
      <th>TEL: (DOM)</th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Referencia"; break;
 case 'EN': echo "Referencia"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
    </thead>

    <tfoot>
      <tr>
        <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Nombre"; break;
 case 'EN': echo "Name"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Cedula"; break;
 case 'EN': echo "ID"; break;
}
?></th>
 
 <?php if(StockData::getPrincipal()->method==1):?>
      
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion"; break;
 case 'EN': echo "Address"; break;
}
?> (DOM)</th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion"; break;
 case 'EN': echo "Address"; break;
}
?> (USD)</th>

<?php endif; if(StockData::getPrincipal()->method==2):?>
     
<th><?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion"; break;
 case 'EN': echo "Address"; break;
}
?></th>

<?php endif; ?>
 
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Licencia"; break;
 case 'EN': echo "License"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Pasaporte"; break;
 case 'EN': echo "Passport"; break;
}
?></th>
      <th>TEL: (USD)</th>
      <th>TEL: (DOM)</th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Referencia"; break;
 case 'EN': echo "Referencia"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
      </tr>
    </tfoot>

      
      <?php while($r = $query->fetch_array()){?>
          <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=person&opt=edit&id=<?php echo $r['id'];?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                        <?php if(!empty($r['phone'])):?>
                        <a href="https://<?php echo StockData::getPrincipal()->web_url;?>?login=1&username=<?php echo $r['phone'];?>&password=<?php echo $r['phone'];?>" target="_blank" class="btn btn-secondary"><i class="fas fa-eye"></i></a>
                        <?php else:?>
                        <a href="https://<?php echo StockData::getPrincipal()->web_url;?>/?login=1&username=<?php echo $r['phone2'];?>&password=<?php echo $r['phone2'];?>" target="_blank" class="btn btn-secondary"><i class="fas fa-eye"></i></a>
                        <?php endif;?>
                      </div>
        </td>
        <td><?php echo $r['name']; ?></td>
        <td><?php echo $r['no']; ?></td>
        
        <?php if(StockData::getPrincipal()->method==1):?>
      
        <td><?php echo $r['address']; ?></td>
        <td><?php echo $r['address2']; ?></td>

        <?php endif; if(StockData::getPrincipal()->method==2):?>

        <td><?php echo $r['address']; ?></td>
        
        <?php endif; ?>
        
        <td><?php echo $r['license']; ?></td>
        <td><?php echo $r['passport']; ?></td>
        <td><?php echo $r['phone']; ?></td>
        <td><?php echo $r['phone2']; ?></td>
        <td><?php echo $r['reference']; ?></td>
    <td class="text-right py-0 align-middle">
            
<?php  
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()){
if ($x['permits_id']==4): ?>
   
                        <a href="./?action=person&opt=del&id=<?php echo $r['id'];?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();" ><i class="fas fa-trash"> <?php 
switch (Core::$user->language){
 case 'ES': echo "Eliminar"; break;
 case 'EN': echo "Delete"; break;
}
?></i></a>
                    
                    
                     <script>
function confirmDelete() {
    return confirm("<?php 
switch (Core::$user->language){
 case 'ES': echo "¿Estás seguro de que deseas eliminar este registro?"; break;
 case 'EN': echo "Are you sure you want to delete this record?"; break;
}
?>");
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
    <h2><?php 
switch (Core::$user->language){
 case 'ES': echo "No hay Clientes"; break;
 case 'EN': echo "There are no Clients"; break;
}
?></h2>
    <p><?php 
switch (Core::$user->language){
 case 'ES': echo "No se ha realizado ninguna operacion."; break;
 case 'EN': echo "No operation has been performed."; break;
}
?></p>
    </div>
</div>
  
   <?php endif;?>



  </div>
</div>
    </div><!-- /.row -->
      </div><!-- /.container-fluid -->
<script type="text/javascript">
$(document).ready(function(){
  if ($("#example1").length) {
    $("#example1").DataTable();
  }
});
</script>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="new"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-plus'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Nuevo Cliente"; break;
 case 'EN': echo "New Client"; break;
}
?></h1>
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
function actualizarReloj() {
  const reloj = document.getElementById("reloj");
  if (!reloj) return;

  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  reloj.textContent = `${horas}:${minutos}:${segundos}`;
}
document.addEventListener("DOMContentLoaded", function() {
  actualizarReloj();
  setInterval(actualizarReloj, 1000);
});
</script>
        </div>
          <div class="card" style="background-color:#222;">
<div class="card-body">
<form method="post" id="form-cliente" class="form-horizontal" enctype="multipart/form-data">
<div class="row">
    
        <div class="col-md-4 col-12">
<?php if(StockData::getPrincipal()->method==1):?>
      
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Provincia/Estado"; break;
 case 'EN': echo "Province/State"; break;
}
?></label>

<?php endif; if(StockData::getPrincipal()->method==2):?>

        <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Estado"; break;
 case 'EN': echo "State"; break;
}
?></label>
        
        <?php endif; ?>

       <select style="background-color:#333;"  name="location" id="location"  class="form-control select2">
      <option selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>

<?php if(StockData::getPrincipal()->update==1):

foreach(StatesData::getAllWithCountry() as $state): ?>
        <option value="<?php echo $state->id; ?>">
            <?php echo $state->state_name . ' (' . $state->country_name . ')'; ?>
        </option>
        
<?php endforeach; else:

foreach(LocationData::getAll() as $loc):?>
      <option value="<?php echo $loc->id;?>"><?php echo $loc->name." (".$loc->timezone.")";?></option>
      
<?php endforeach; endif;?>

      </select>
    </div>
     
      
    <div class="col-md-4 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Tipo"; break;
 case 'EN': echo "Type"; break;
}
?></label>
      <select style="background-color:#333;" name="type" class="form-control select2" id="type_id">
      <option value="0"><?php 
switch (Core::$user->language){
 case 'ES': echo "PERSONA FISICA"; break;
 case 'EN': echo "NATURAL PERSON"; break;
}
?></option>
      <option value="1"><?php 
switch (Core::$user->language){
 case 'ES': echo "EMPRESA"; break;
 case 'EN': echo "COMPANY"; break;
}
?></option>
      </select>
    </div>
   
    
    <div class="col-md-4 col-12" id="rnc_id">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "RNC"; break;
 case 'EN': echo "NIE"; break;
}
?></label>
      <input style="background-color:#333;" type="text" name="rnc" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "RNC Empresa"; break;
 case 'EN': echo "NIE Company"; break;
}
?>">
    </div>

  

    <div class="col-md-4 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Nombre Completo"; break;
 case 'EN': echo "Full Name"; break;
}
?></label>
      <input style="background-color:#333;" type="text" autofocus name="name" autocomplete="off" required class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Nombre Completo"; break;
 case 'EN': echo "Full Name"; break;
}
?>">
    </div>
    
<div class="col-md-3 col-12">
  <label for="inputEmail1" class="col-md-12 col-12 control-label">
    <?php echo Core::$user->language == 'ES' ? 'Genero' : 'Gender'; ?>
  </label>
  <select style="background-color:#333;" name="gender" required class="form-control">
    <option value="" selected disabled>
      --- <?php echo Core::$user->language == 'ES' ? 'ELEGIR' : 'CHOOSE'; ?> ---
    </option>
    <?php
    $lang = Core::$user->language;
    $options = [
      'M' => $lang == 'ES' ? 'Hombre' : 'Man',
      'F' => $lang == 'ES' ? 'Mujer' : 'Woman'
    ];
    foreach ($options as $val => $label) {
      echo '<option value="'.$val.'">'.$label.'</option>';
    }
    ?>
  </select>
</div>

    
   <div class="col-md-3 col-12">
  <label for="inputEmail1" class="col-md-12 col-12 control-label">
    <?php echo Core::$user->language == 'ES' ? 'Idioma' : 'Language'; ?>
  </label>
 <select style="background-color:#333;" name="language" required class="form-control">
  <option value="" selected disabled>
    --- <?php echo Core::$user->language == 'ES' ? 'ELEGIR' : 'CHOOSE'; ?> ---
  </option>
  <?php
  $lang = Core::$user->language;
  $options = [
    'ES' => ['EN' => 'Inglés', 'ES' => 'Español'],
    'EN' => ['EN' => 'English', 'ES' => 'Spanish']
  ];
  foreach ($options[$lang] as $val => $label) {
    echo '<option value="'.$val.'">'.$label.'</option>';
  }
  ?>
</select>


</div>

    

  <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Numero de Cedula"; break;
 case 'EN': echo "ID Number"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="no" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Numero de Cedula"; break;
 case 'EN': echo "ID Number"; break;
}
?>">
    </div>

    <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Licencia de conducir"; break;
 case 'EN': echo "Driver license"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="license" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Licencia de conducir"; break;
 case 'EN': echo "Driver license"; break;
}
?>">
    </div>


    <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Pasaporte"; break;
 case 'EN': echo "Passport"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="passport" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Pasaporte"; break;
 case 'EN': echo "Passport"; break;
}
?>">
    </div>


    <div class="col-md-4 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Nacionalidad"; break;
 case 'EN': echo "Nationality"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="nationality" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Nacionalidad"; break;
 case 'EN': echo "Nationality"; break;
}
?>">
    </div>


<div class="col-md-2 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Estado Civil"; break;
 case 'EN': echo "Marital status"; break;
}
?></label>
      <select style="background-color:#333;"  name="estado"  required class="form-control">
      <option value="<?php 
switch (Core::$user->language){
 case 'ES': echo "Soltero"; break;
 case 'EN': echo "Single"; break;
}
?>"><?php 
switch (Core::$user->language){
 case 'ES': echo "Soltero"; break;
 case 'EN': echo "Single"; break;
}
?></option>
      <option value="<?php 
switch (Core::$user->language){
 case 'ES': echo "Casado"; break;
 case 'EN': echo "Married"; break;
}
?>"><?php 
switch (Core::$user->language){
 case 'ES': echo "Casado"; break;
 case 'EN': echo "Married"; break;
}
?></option>
      <option value="<?php 
switch (Core::$user->language){
 case 'ES': echo "Union Libre"; break;
 case 'EN': echo "Free Union"; break;
}
?>"><?php 
switch (Core::$user->language){
 case 'ES': echo "Union Libre"; break;
 case 'EN': echo "Free Union"; break;
}
?></option>
      <option value="Viudo"><?php 
switch (Core::$user->language){
 case 'ES': echo "Viudo"; break;
 case 'EN': echo "Widower"; break;
}
?></option>
      </select>
    </div>
    
         
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Cumpleaño"; break;
 case 'EN': echo "Birthday"; break;
}
?></label>
<input type="date" style="background-color:#333;"  class="form-control"  name="birthday">
    </div>
    
<div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion Estadia"; break;
 case 'EN': echo "Address Stay"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="address" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion Estadia"; break;
 case 'EN': echo "Address Stay"; break;
}
?>">
    </div>
    
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Telefono Estadia"; break;
 case 'EN': echo "Stay Telephone"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="phone"  autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Telefono Estadia"; break;
 case 'EN': echo "Stay Telephone"; break;
}
?>">
    </div>
    
        <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Correo"; break;
 case 'EN': echo "Email"; break;
}
?></label>
      <input style="background-color:#333;" type="email"  name="email" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Correo"; break;
 case 'EN': echo "Email"; break;
}
?>">
    </div>
    

<div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion Extranjera"; break;
 case 'EN': echo "Foreign Address"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="address2" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion Extranjera"; break;
 case 'EN': echo "Foreign Address"; break;
}
?>">
    </div>
    
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Telefono Extranjera"; break;
 case 'EN': echo "Foreign Telephone"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="phone2" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Telefono Extranjera"; break;
 case 'EN': echo "Foreign Telephone"; break;
}
?>">
    </div>
    
    
<div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Referencia (Conocido)"; break;
 case 'EN': echo "Reference (Known)"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="reference" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Referencia (Conocido)"; break;
 case 'EN': echo "Reference (Known)"; break;
}
?>">
    </div>


    <div class="col-md-6 col-12 my-3">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Cedula"; break;
 case 'EN': echo "Photo ID"; break;
}
?></label>
<input style="background-color:#333;" type="file"  name="invoice_file">
    </div>
    

 <div class="col-md-3 col-12 my-3">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Pasaporte"; break;
 case 'EN': echo "Passport Photo"; break;
}
?></label>
<input style="background-color:#333;" type="file"  name="passport_file">
    </div>
    
 <div class="col-md-3 col-12 my-3">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
<input style="background-color:#333;" type="date" class="form-control"  name="passport_date">
    </div>
    
     <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Licencia"; break;
 case 'EN': echo "Photo License"; break;
}
?></label>
<input style="background-color:#333;" type="file"  name="license_file">
    </div>
    
     
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
<input style="background-color:#333;" type="date" class="form-control"  name="license_date">
    </div>
    
     <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Residencia"; break;
 case 'EN': echo "Photo Residence"; break;
}
?></label>
    <input style="background-color:#333;" type="file"  name="home_file">
    </div>

 
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
<input style="background-color:#333;" type="date" class="form-control"  name="home_date">
    </div>
    
    
<script>
document.addEventListener("DOMContentLoaded", function() {
  const rncDiv = document.getElementById("rnc_id");
  if (rncDiv) rncDiv.style.display = "none";

  $('#type_id').on('change', function() {
    var getSelectValue = $('#type_id').val();
    if(getSelectValue=="1") {
      document.getElementById("rnc_id").style.display = "inline-block";
    } else {
      document.getElementById("rnc_id").style.display = "none";
    }
  });

  $('#location').on('change', function() {
    var value = $('#location').val();
    if(value) {
       $('.warning').hide();
       $('#submit').prop('disabled', false);
    }
  });
});
</script>

                <div class="col-md-6 col-6">
<label for="inputEmail1" class="col-md-12 col-12 control-label"></label>
                  <a href="./?view=person&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Cancelar"; break;
 case 'EN': echo "Cancel"; break;
}
?></a>
                </div>
                <div class="col-md-6 col-6">
<label for="inputEmail1" class="col-md-12 col-12 control-label"></label>
                   <button id="submit" class="btn btn-primary btn-block btn-sm" disabled><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Finalizar"; break;
 case 'EN': echo "Finish"; break;
}
?></button>
                 
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
function formToJSON(form) {
  const data = new FormData(form);
  const json = {};
  data.forEach((value, key) => {
    if (!(key in json) && !(value instanceof File)) {
      json[key] = value;
    }
  });
  return json;
}

function guardarOffline(cliente) {
  let pendientes = JSON.parse(localStorage.getItem("clientes_pendientes")) || [];
  pendientes.push(cliente);
  localStorage.setItem("clientes_pendientes", JSON.stringify(pendientes));
  if (typeof toastr !== "undefined") {
    toastr.info('Guardado localmente sin archivo. Se enviará cuando vuelva el internet.');
  }
}

function sincronizarClientes() {
  let pendientes = JSON.parse(localStorage.getItem("clientes_pendientes")) || [];
  if (pendientes.length > 0 && navigator.onLine) {
    let nuevosPendientes = [...pendientes];

    pendientes.forEach((cliente, i) => {
      fetch("./?action=person&opt=add_offline",  {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(cliente)
      })
      .then(res => res.text())
      .then(resp => {
        resp = resp.trim();
        if (resp === "OK" || resp === "UPDATED") {
          nuevosPendientes.splice(i, 1);
          localStorage.setItem("clientes_pendientes", JSON.stringify(nuevosPendientes));
        }
      })
      .catch(err => {
        console.log("Error sincronizando cliente:", err);
      });
    });
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("form-cliente");
  if (!form) return;

  form.addEventListener("submit", function(e) {
    e.preventDefault();

    const cliente = formToJSON(this);

    if (navigator.onLine) {
      const formData = new FormData(this);

      fetch("./?action=person&opt=add", {
        method: "POST",
        body: formData
      })
      .then(res => res.text())
      .then(resp => {
        resp = resp.trim();
        if (resp === "OK" || resp === "UPDATED") {
          if (typeof toastr !== "undefined") {
            toastr.success('Registro agregado correctamente.');
          }
          window.location = './?view=person&opt=all';
        } else {
          if (typeof toastr !== "undefined") {
            toastr.error('Ya existe ese registro.');
          }
        }
      })
      .catch(() => guardarOffline(cliente));
    } else {
      guardarOffline(cliente);
    }
  });

  setInterval(() => {
    if (navigator.onLine) sincronizarClientes();
  }, 5000);
});
</script>

</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"): $user = PersonData::getById($_GET["id"]);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0"><i class='fa fa-edit'></i>Editar Cliente</h1>
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
function actualizarReloj() {
  const reloj = document.getElementById("reloj");
  if (!reloj) return;

  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  reloj.textContent = `${horas}:${minutos}:${segundos}`;
}
document.addEventListener("DOMContentLoaded", function() {
  actualizarReloj();
  setInterval(actualizarReloj, 1000);
});
</script>
        </div>
          <div class="card" style="background-color:#222;">
<div class="card-body">
<form method="post" class="form-horizontal" action="./?action=person&opt=upd" enctype="multipart/form-data">
<div class="row">
    
        <div class="col-md-4 col-12">
<?php if(StockData::getPrincipal()->method==1):?>
      
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Provincia/Pais"; break;
 case 'EN': echo "Province/Country"; break;
}
?></label>

<?php endif; if(StockData::getPrincipal()->method==2):?>

        <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Estado"; break;
 case 'EN': echo "State"; break;
}
?></label>
        
        <?php endif; ?>

      <select style="background-color:#333;"  name="location" id="location" required class="form-control select2">
      <option selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>
      <?php foreach(LocationData::getAll() as $loc):?>
      <option value="<?php echo $loc->id;?>" <?php if ($user->location==$loc->id){echo "selected";}?>><?php echo $loc->name;?></option>
      <?php endforeach;?>
      </select>
    </div>
     
      
    <div class="col-md-4 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Tipo"; break;
 case 'EN': echo "Type"; break;
}
?></label>
      <select style="background-color:#333;" name="type" class="form-control select2" id="type_id">
      <option value="0" <?php if ($user->type==0){echo "selected";}?>><?php 
switch (Core::$user->language){
 case 'ES': echo "PERSONA FISICA"; break;
 case 'EN': echo "NATURAL PERSON"; break;
}
?></option>
      <option value="1" <?php if ($user->type==1){echo "selected";}?>><?php 
switch (Core::$user->language){
 case 'ES': echo "EMPRESA"; break;
 case 'EN': echo "COMPANY"; break;
}
?></option>
      </select>
    </div>
   
    
    <div class="col-md-4 col-12" id="rnc_id">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "RNC"; break;
 case 'EN': echo "NIE"; break;
}
?></label>
      <input style="background-color:#333;" type="text" name="rnc" value="<?php echo $user->rnc;?>"  autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "RNC Empresa"; break;
 case 'EN': echo "NIE Company"; break;
}
?>">
    </div>

  

    <div class="col-md-4 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Nombre Completo"; break;
 case 'EN': echo "Full Name"; break;
}
?></label>
      <input style="background-color:#333;" type="text" autofocus name="name" value="<?php echo $user->name;?>" autocomplete="off" required class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Nombre Completo"; break;
 case 'EN': echo "Full Name"; break;
}
?>">
    </div>
    
<div class="col-md-3 col-12">
  <label for="inputEmail1" class="col-md-12 col-12 control-label">
    <?php echo Core::$user->language == 'ES' ? 'Género' : 'Gender'; ?>
  </label>
  <select style="background-color:#333;" name="gender" required class="form-control">
    <option value="" disabled <?php echo empty($user->gender) ? 'selected' : ''; ?>>
      --- <?php echo Core::$user->language == 'ES' ? 'ELEGIR' : 'CHOOSE'; ?> ---
    </option>
    <?php
    $lang = Core::$user->language;
    $current = $user->gender ?? '';
    $options = [
      'M' => $lang == 'ES' ? 'Hombre' : 'Man',
      'F' => $lang == 'ES' ? 'Mujer' : 'Woman'
    ];
    foreach ($options as $val => $label) {
      $selected = ($current == $val) ? 'selected' : '';
      echo "<option value=\"$val\" $selected>$label</option>";
    }
    ?>
  </select>
</div>

    
  <div class="col-md-3 col-12">
  <label for="inputEmail1" class="col-md-12 col-12 control-label">
    <?php echo Core::$user->language == 'ES' ? 'Idioma' : 'Language'; ?>
  </label>
  <select style="background-color:#333;" name="language" required class="form-control">
    <option value="" selected disabled>
      --- <?php echo Core::$user->language == 'ES' ? 'ELEGIR' : 'CHOOSE'; ?> ---
    </option>
    <?php
    $lang = Core::$user->language;
    $userLang = $user->language;
    $options = [
      'ES' => ['EN' => 'Inglés', 'ES' => 'Español'],
      'EN' => ['EN' => 'English', 'ES' => 'Spanish']
    ];
    foreach ($options[$lang] as $val => $label) {
      $selected = ($userLang == $val) ? 'selected' : '';
      echo "<option value=\"$val\" $selected>$label</option>";
    }
    ?>
  </select>
</div>


    

  <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Numero de Cedula"; break;
 case 'EN': echo "ID Number"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="no" value="<?php echo $user->no;?>" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Numero de Cedula"; break;
 case 'EN': echo "ID Number"; break;
}
?>">
    </div>

    <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Licencia de conducir"; break;
 case 'EN': echo "Driver license"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="license" value="<?php echo $user->license;?>" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Licencia de conducir"; break;
 case 'EN': echo "Driver license"; break;
}
?>">
    </div>


    <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Pasaporte"; break;
 case 'EN': echo "Passport"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="passport" value="<?php echo $user->passport;?>" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Pasaporte"; break;
 case 'EN': echo "Passport"; break;
}
?>">
    </div>


    <div class="col-md-4 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Nacionalidad"; break;
 case 'EN': echo "Nationality"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="nationality" autocomplete="off" value="<?php echo $user->nationality;?>"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Nacionalidad"; break;
 case 'EN': echo "Nationality"; break;
}
?>">
    </div>


<div class="col-md-2 col-12">
  <label for="inputEmail1" class="col-md-12 col-12 control-label">
    <?php echo Core::$user->language == 'ES' ? 'Estado Civil' : 'Marital Status'; ?>
  </label>
  <select style="background-color:#333;" name="estado" required class="form-control">
    <option value="" disabled <?php echo empty($user->estado) ? 'selected' : ''; ?>>
      --- <?php echo Core::$user->language == 'ES' ? 'ELEGIR' : 'CHOOSE'; ?> ---
    </option>
    <?php
    $lang = Core::$user->language;
    $current = $user->estado ?? '';
    $options = [
      'Soltero' => $lang == 'ES' ? 'Soltero' : 'Single',
      'Casado' => $lang == 'ES' ? 'Casado' : 'Married',
      'Unión Libre' => $lang == 'ES' ? 'Unión Libre' : 'Free Union',
      'Viudo' => $lang == 'ES' ? 'Viudo' : 'Widower'
    ];
    foreach ($options as $val => $label) {
      $selected = ($current == $val) ? 'selected' : '';
      echo "<option value=\"$val\" $selected>$label</option>";
    }
    ?>
  </select>
</div>

         
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Cumpleaño"; break;
 case 'EN': echo "Birthday"; break;
}
?></label>
<input type="date" style="background-color:#333;"  class="form-control"  value="<?php echo $user->birthday;?>" name="birthday">
    </div>
    
<div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion Estadia"; break;
 case 'EN': echo "Address Stay"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="address" autocomplete="off" value="<?php echo $user->address;?>"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion Estadia"; break;
 case 'EN': echo "Address Stay"; break;
}
?>">
    </div>
    
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Telefono Estadia"; break;
 case 'EN': echo "Stay Telephone"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="phone"  autocomplete="off" value="<?php echo $user->phone;?>"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Telefono Estadia"; break;
 case 'EN': echo "Stay Telephone"; break;
}
?>">
    </div>
    
        <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Correo"; break;
 case 'EN': echo "Email"; break;
}
?></label>
      <input style="background-color:#333;" type="email"  name="email" autocomplete="off" value="<?php echo $user->email;?>"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Correo"; break;
 case 'EN': echo "Email"; break;
}
?>">
    </div>
    

<div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion Extranjera"; break;
 case 'EN': echo "Foreign Address"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="address2" autocomplete="off" value="<?php echo $user->address2;?>"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Direccion Extranjera"; break;
 case 'EN': echo "Foreign Address"; break;
}
?>">
    </div>
    
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Telefono Extranjera"; break;
 case 'EN': echo "Foreign Telephone"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="phone2" autocomplete="off" value="<?php echo $user->phone2;?>"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Telefono Extranjera"; break;
 case 'EN': echo "Foreign Telephone"; break;
}
?>">
    </div>
    
    
<div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Referencia (Conocido)"; break;
 case 'EN': echo "Reference (Known)"; break;
}
?></label>
      <input style="background-color:#333;" type="text"  name="reference" autocomplete="off" value="<?php echo $user->reference;?>"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Referencia (Conocido)"; break;
 case 'EN': echo "Reference (Known)"; break;
}
?>">
    </div>


    <div class="col-md-6 col-12 my-3">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Cedula"; break;
 case 'EN': echo "Photo ID"; break;
}
?></label>
<?php if($user->invoice_file!=""):?>
<a href="CF-SYSTEMS/storage/invoice_files/<?php echo $user->invoice_file;?>"  class="btn btn-default"><i class="fa fa-file"></i> Archivo Cliente (<?php echo $user->invoice_file; ?>)</a>
      
    <?php endif; ?>
<input style="background-color:#333;" type="file"  name="invoice_file">
    </div>
    

 <div class="col-md-3 col-12 my-3">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Pasaporte"; break;
 case 'EN': echo "Passport Photo"; break;
}
?></label>
<?php if($user->passport_file!=""):?>
<a href="CF-SYSTEMS/storage/invoice_files/<?php echo $user->passport_file;?>"  class="btn btn-default"><i class="fa fa-file"></i> Archivo Cliente (<?php echo $user->passport_file; ?>)</a>
      
    <?php endif; ?>
<input style="background-color:#333;" type="file"  name="passport_file">
    </div>
    
 <div class="col-md-3 col-12 my-3">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
<input style="background-color:#333;" type="date" value="<?php echo $user->passport_date;?>" class="form-control"  name="passport_date">
    </div>
    
     <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Licencia"; break;
 case 'EN': echo "Photo License"; break;
}
?></label>

<?php if($user->license_file!=""):?>
<a href="CF-SYSTEMS/storage/invoice_files/<?php echo $user->license_file;?>"  class="btn btn-default"><i class="fa fa-file"></i> Archivo Cliente (<?php echo $user->license_file; ?>)</a>
      
    <?php endif; ?>
<input style="background-color:#333;" type="file"  name="license_file">
    </div>
    
     
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
<input style="background-color:#333;" type="date" value="<?php echo $user->license_date;?>"  class="form-control"  name="license_date">
    </div>
    
     <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Residencia"; break;
 case 'EN': echo "Photo Residence"; break;
}
?></label>

<?php if($user->home_file!=""):?>
<a href="CF-SYSTEMS/storage/invoice_files/<?php echo $user->home_file;?>"  class="btn btn-default"><i class="fa fa-file"></i> Archivo Cliente (<?php echo $user->home_file; ?>)</a>
      
    <?php endif; ?>
    <input style="background-color:#333;" type="file"  name="home_file">
    </div>

 
 <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
<input style="background-color:#333;" type="date" value="<?php echo $user->home_date;?>" class="form-control"  name="home_date">
    </div>
    
    
<script>
document.addEventListener("DOMContentLoaded", function() {
  const rncDiv = document.getElementById("rnc_id");
  if (rncDiv) {
    if ($('#type_id').val() == "1") {
      rncDiv.style.display = "inline-block";
    } else {
      rncDiv.style.display = "none";
    }
  }

  $('#type_id').on('change', function() {
    var getSelectValue = $('#type_id').val();
    if(getSelectValue=="1") {
      document.getElementById("rnc_id").style.display = "inline-block";
    } else {
      document.getElementById("rnc_id").style.display = "none";
    }
  });

  $('#location').on('change', function() {
    var value = $('#location').val();
    if(value) {
       $('.warning').hide();
       $('#submit').prop('disabled', false);
    }
  });

  if ($('#location').val()) {
    $('#submit').prop('disabled', false);
  }
});
</script>

                <div class="col-md-6 col-6">
<label for="inputEmail1" class="col-md-12 col-12 control-label"></label>
                  <a href="./?view=person&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Cancelar"; break;
 case 'EN': echo "Cancel"; break;
}
?></a>
                </div>
                <div class="col-md-6 col-6">
<label for="inputEmail1" class="col-md-12 col-12 control-label"></label>
                   <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $user->id;?>">
                   <button id="submit" class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Finalizar"; break;
 case 'EN': echo "Finish"; break;
}
?></button>
                 
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

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="providers"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?> 
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
        <h1 class="m-0"><i class='fa fa-users'></i> Suplidores</h1>
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
function actualizarReloj() {
  const reloj = document.getElementById("reloj");
  if (!reloj) return;

  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  reloj.textContent = `${horas}:${minutos}:${segundos}`;
}
document.addEventListener("DOMContentLoaded", function() {
  actualizarReloj();
  setInterval(actualizarReloj, 1000);
});
</script>

    </div>
    
  <div class="callout callout-secondary" style="background-color: #222">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo y en en titulo del aparatado para crear nuevo.
            </div>


            
 <div id="accordion">
   <div class="card card-secondary card-outline" style="background-color:#222;">
                    <a class="d-block w-100" data-toggle="collapse" href="#collapseThree" style="color:white;">
                        <div class="card-header">
                            <h4 class="card-title w-100">
                            <i class="fa fa-plus"></i>   CREAR NUEVO SUPLIDOR
                            </h4>
                        </div>
                    </a>
                    <div id="collapseThree" class="collapse" data-parent="#accordion">
 
                        <div class="card-body">
  <form class="form-horizontal" method="post" id="addprovider" role="form">


<div class="row">
     
    <div class="col-md-6 col-6">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">No Documento</label>
      <div class="input-group">
  <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
      <input type="text" autocomplete="off" name="no" class="form-control" id="no" placeholder="No Documento">
    </div>
    </div>
    
     <div class="col-md-6 col-6" id="stype">
  <label class="control-label">Tipo</label>
      <div class="input-group">
     <select name="is_id" class="form-control">
      <option value="1">RNC</option>
      <option value="2">CEDULA</option>
      <option value="3">PASAPORTE</option>
      </select>
      </div>
      </div>

     
    <div class="col-md-6 col-6">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre</label>
      <div class="input-group">
  <span class="input-group-text"><i class="fa fa-user"></i></span>
       <input type="text" autocomplete="off" name="name" class="form-control" id="name" placeholder="Nombre" required>
    </div>
    </div>

    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Direccion</label>
      <div class="input-group">
  <span class="input-group-text"><i class="fa fa-street-view"></i></span>
      <input type="text" autocomplete="off" name="address1" class="form-control"  id="address1" placeholder="Direccion">
    </div>
    </div>
 
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Email</label>
      <div class="input-group">
  <span class="input-group-text"><i class="fa fa-comments"></i></span>
      <input type="email" autocomplete="off" name="email1" class="form-control" id="email1" placeholder="Email">
    </div>
    </div>
  
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Telefono</label>
      <div class="input-group">
  <span class="input-group-text"><i class="fa fa-phone"></i></span>
      <input type="text" autocomplete="off" name="phone1" class="form-control"  data-inputmask='"mask": "(999) 999-9999"' data-mask placeholder="Telefono">
    </div>
    </div>
  </div>

 <div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=person&opt=providers" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
</form>
<script>
jQuery(document).ready(function(){
  jQuery("#addprovider").submit(function(e){
    e.preventDefault();
    var formData = jQuery(this).serialize();
    $.ajax({
      type: "POST",
      url: "./?action=person&opt=addproviders",
      data: formData,
      success: function(html){
        html = $.trim(html);
        if(html=='true' || html=='OK')
        {
          $.jGrowl("Proveedor Exito!", { sticky: true });
          $.jGrowl("Se Agrego a la Base Datos", { header: 'Acceso permitido' });
          var delay = 1000;
          setTimeout(function(){ window.location = './?view=person&opt=providers'  }, delay); 
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
                </div>
                 </div>


<?php $selstock = null;
if(isset($_GET["stock"])){ $selstock=$_GET["stock"]; }
else{ $selstock = StockData::getPrincipal()->id; }

$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT * from supplier";
$query = $con->query($sql);
if($query && $query->num_rows > 0):?>
<div class="card" style="background-color: #222">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
      <thead>
      <th>Accion</th>
      <th>RNC</th>
      <th>Nombre completo</th>
      <th>Direccion</th>
      <th>Email</th>
      <th>Telefono</th>
      <th>Accion</th>
      </thead>
      <tfoot>
      <th>Accion</th>
      <th>RNC</th>
      <th>Nombre completo</th>
      <th>Direccion</th>
      <th>Email</th>
      <th>Telefono</th>
      <th>Accion</th>
      </tfoot>
      <?php while($r = $query->fetch_array()):?>
        <tr>

                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=person&opt=providers&id=<?php echo $r['sup_id'];?>" class="btn btn-success"><i class="fas fa-edit"></i></a>
                        <a href="./?view=person&opt=history_clients&id=<?php echo $r['sup_id'];?>" class="btn btn-info"><i class="fas fa-history"></i></a>
                      </div>
        </td>

      <td><?php echo $r['code_name']; ?></td>
       <td><?php echo utf8_decode($r['sup_name']); ?></td>
       <td><?php echo $r['sup_address'];?></td>
        <td><?php echo $r['sup_email'];?></td>
        <td><?php echo $r['sup_mobile'];?></td>
        
       <td class="text-right py-0 align-middle">
            
<?php  
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()):
if ($x['permits_id']==4): ?>
   
                       <a href="./?action=person&opt=delproviders&id=<?php echo $r['sup_id'];?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDeleteProvider();" ><i class="fas fa-trash"> Eliminar</i></a>
                    
                    
                     <script>
function confirmDeleteProvider() {
    return confirm("¿Estás seguro de que deseas eliminar este registro?");
}
</script>
    <?php endif; endwhile; ?>
</td>
    </tr>
    
    <?php endwhile; ?>
      </table>
  </div><!-- /.box-body -->
</div>
</div>  

      <?php else:?>
     <div>
         <div class="card">
              <div class="card-header">
    <h2>No hay Provedores</h2>
    <p>No se ha realizado ninguna operacion.</p>
    </div>
</div>
</div>
  
   <?php endif;?>



  </div>
</div>
    </div><!-- /.row -->
      </div><!-- /.container-fluid -->
<script type="text/javascript">
$(document).ready(function(){
  if ($("#example1").length) {
    $("#example1").DataTable();
  }
});
</script>
</section>
<?php endif; ?>