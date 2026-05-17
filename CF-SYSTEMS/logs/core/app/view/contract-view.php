<?php if(isset($_GET["opt"]) && $_GET["opt"]=="new"):?>

   <section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
         <h1 class="m-0"><i class='fa fa-user-plus'></i> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Rango de Fecha"; break;
 case 'EN': echo "Date Range"; break;
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

       
            
<?php if(StockData::getPrincipal()->update=="1"):?>
 <div class="row">
 
          <!-- /.col -->
          <div class="col-12 col-sm-4 col-md-4 my-2">
            <a href="./?view=contract&opt=newfree" class="floating-btn message-btn" style="background-color: orange;">
  <div class="icon-container">
   <i class="fa fa-road"></i>
  </div>
  <span class="message-text"> ABIERTO</span>
</a>
          </div>
          <!-- /.col -->

          
          <div class="col-12 col-sm-4 col-md-4 my-2">
             <a href="./?view=contract&opt=newhours" class="floating-btn message-btn" style="background-color: #C70039;">
      <div class="icon-container">
    <i class="fa fa-history"> </i>
    </div>
    <span class="message-text"> POR HORA</span> 
  </a>
            <!-- /.info-box -->
          </div>
  </div>          <!-- /.col -->
<?php else:?>
<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>
<?php endif;?>         
 
        
 <div class="row">
            <div class="col-md-12">
                
                <!-- Profile Image -->
            <div class="card card-secondary card-outline" style="background-color:#222;">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="CF-SYSTEMS/storage/profiles/user.png"
                       alt="User profile picture">
                </div>

             <h3 class="profile-username text-center"><?php 
switch (Core::$user->language){
 case 'ES': echo "Datos de la Renta"; break;
 case 'EN': echo "Rent Data"; break;
}
?></h3>
 <div class="card-header p-0 pt-1">
  <div style="overflow-x: auto; white-space: nowrap;">
    <ul class="nav nav-tabs flex-nowrap" id="stepTabs" role="tablist" style="min-width: max-content;">
      <li class="nav-item" style="display: inline-block; min-width: 150px;">
        <a class="nav-link active disabled-tab text-center" href="#step1" role="tab">Datos del Cliente</a>
      </li>
      <li class="nav-item" style="display: inline-block; min-width: 150px;">
        <a class="nav-link disabled-tab text-center" href="#step2" role="tab">Fechas & Lugar</a>
      </li>
      <li class="nav-item" style="display: inline-block; min-width: 150px;">
        <a class="nav-link disabled-tab text-center" href="#step3" role="tab">Elegir Vehículo</a>
      </li>
      <li class="nav-item" style="display: inline-block; min-width: 150px;">
        <a class="nav-link disabled-tab text-center" href="#step4" role="tab">Facturación</a>
      </li>
      <li class="nav-item" style="display: inline-block; min-width: 150px;">
        <a class="nav-link disabled-tab text-center" href="#step5" role="tab">Revisión</a>
      </li>
      <li class="nav-item" style="display: inline-block; min-width: 150px;">
        <a class="nav-link disabled-tab text-center" href="#step6" role="tab">Marcar Daño</a>
      </li>
      <li class="nav-item" style="display: inline-block; min-width: 150px;">
        <a class="nav-link disabled-tab text-center" href="#step7" role="tab">Firma</a>
      </li>
    </ul>
  </div>
</div>


<style>
  .disabled-tab {
    pointer-events: none;
    cursor: default;
  }
  
  /* Después: reemplaza por */
.damage-pane {
  display: none;
}
.damage-pane.active.show {
  display: block;
}
</style>

<form action="./?action=contract&opt=add" id="delivery" method="POST" enctype="multipart/form-data">
  
                
  <div class="card-body">
    <div class="tab-content" id="stepTabContent">
    
    <div class="damage-pane fade show active" id="step1" role="tabpanel">
  <input type="hidden" name="nuevo_cliente_activo" id="nuevo_cliente_activo" value="0">

  <div class="row">
    <div class="col-md-4 col-12">
      <label class="col-md-12 col-12 control-label">
        <?php echo Core::$user->language == 'EN' ? 'CUSTOMER/ COMPANY' : 'CLIENTE/ EMPRESA'; ?>
      </label>
      <select style="background-color: #333;" name="person_id" id="person_id" class="form-control select2" required>
        <option value="">-- <?php echo Core::$user->language == 'EN' ? 'CHOOSE' : 'ELEGIR'; ?> --</option>
        <?php foreach(PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id) as $client): ?>
          <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-4 col-12">
      <label class="col-md-12 col-12 control-label">
        <?php echo Core::$user->language == 'EN' ? 'OPTIONAL DRIVER' : 'CONDUCTOR OPCIONAL'; ?>
      </label>
      <select style="background-color: #333;" name="person2_id" id="person2_id" class="form-control select2">
        <option value="">-- <?php echo Core::$user->language == 'EN' ? 'CHOOSE' : 'ELEGIR'; ?> --</option>
        <?php foreach(PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id) as $client): ?>
          <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-4 col-12">
      <label class="col-md-12 col-12 control-label">&nbsp;</label>
      <button type="button" id="btn_toggle_cliente" class="btn btn-success btn-block">
        CREAR NUEVO
      </button>
    </div>
  </div>

  <!-- Formulario Nuevo Cliente -->
  <div id="form_nuevo_cliente" class="mt-4" style="display:none;">
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

       <select style="background-color:#333;"  name="location"  class="form-control select2">
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
      <select style="background-color:#333;" name="type" class="form-control select2" id="type_person">
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
      <input style="background-color:#333;" type="text" autofocus name="name" autocomplete="off"  class="form-control" placeholder="<?php 
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
  <select style="background-color:#333;" name="gender"  class="form-control">
   
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
      echo "<option value=\"$val\">$label</option>";
    }
    ?>
  </select>
</div>

    
   <div class="col-md-3 col-12">
  <label for="inputEmail1" class="col-md-12 col-12 control-label">
    <?php echo Core::$user->language == 'ES' ? 'Idioma' : 'Language'; ?>
  </label>
 <select style="background-color:#333;" name="language"  class="form-control">
 
  <?php
  $lang = Core::$user->language;
  $options = [
    'ES' => ['ES' => 'Español', 'EN' => 'Inglés'],
    'EN' => ['EN' => 'English', 'ES' => 'Spanish']
  ];
  foreach ($options[$lang] as $val => $label) {
    echo "<option value=\"$val\">$label</option>";
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
      <select style="background-color:#333;"  name="estado"   class="form-control">
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

document.getElementById("rnc_id").style.display = "none";

    
$('#type_person').on('change', () => {
    var getSelectValue = $('#type_person').val();
  
   if(getSelectValue=="1") {
document.getElementById("rnc_id").style.display = "inline-block";
   }
   else if(getSelectValue=="0") {
document.getElementById("rnc_id").style.display = "none";
   }
  
});


</script>

    </div>
  </div>

  <!-- Botón Continuar -->

</div>

<script>
$(document).ready(function() {
  const $nuevoClienteForm = $('#form_nuevo_cliente');
  const $btnCrear = $('#btn_toggle_cliente');
  const $clienteSelect = $('#person_id');
  const $continuarWrap = $('#btn_continuar_wrap');
  const $nuevoClienteActivo = $('#nuevo_cliente_activo');

  function validarContinuar() {
    const activo = $nuevoClienteActivo.val() === '1';

    if (activo) {
      // Crear nuevo cliente: se oculta el selector
      $clienteSelect.prop('disabled', true).prop('required', false);

      // NO marcar inputs como required
      let hayAlgoLleno = false;
      $nuevoClienteForm.find('input').each(function() {
        if ($(this).val().trim() !== '') {
          hayAlgoLleno = true;
        }
      });

      // Mostrar continuar solo si hay al menos un campo lleno
      $continuarWrap.toggle(hayAlgoLleno);
    } else {
      // Usar selector: se activa
      $clienteSelect.prop('disabled', false).prop('required', true);

      // Nunca required en los inputs del formulario
      $nuevoClienteForm.find('input').removeAttr('required');

      // Mostrar continuar si hay cliente seleccionado
      $continuarWrap.toggle($clienteSelect.val() !== '');
    }
  }

  $btnCrear.click(function() {
    $nuevoClienteForm.slideToggle(300, function() {
      const visible = $nuevoClienteForm.is(':visible');
      $nuevoClienteActivo.val(visible ? '1' : '0');
      validarContinuar();
    });

    if ($btnCrear.text().toUpperCase().includes('CREAR')) {
      $btnCrear.html('<i class="fa fa-times"></i> Cancelar');
    } else {
      $btnCrear.html('<i class="fa fa-plus"></i> Crear nuevo');
    }
  });

  $clienteSelect.on('change', validarContinuar);
  $nuevoClienteForm.on('input change', 'input', validarContinuar);
});
</script>


<!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
      <div class="damage-pane fade" id="step2" role="tabpanel">
          <div class="row">
                        
                   <div hidden class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Tipo Contrato</label>
    <select style="background-color: #333;" name="type_id"  class="form-control select2" id="type_id" onchange="showInp2()">
      <option value="1">ENTRE FECHAS</option>
      </select>
    </div>

     <div id="end_at1" class="col-md-4 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Fecha de Devolucion"; break;
 case 'EN': echo "Date to Receive"; break;
}
?></label>
      <input style="background-color: #333;" type="datetime-local" required  name="end_at" id="end_at"  class="form-control"> 
        </div>
        
            
 <div class="col-md-3 col-12">
    <label  hidden for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Fecha a Entregar"; break;
 case 'EN': echo "Date to be delivered"; break;
}
?></label>
      <input  hidden style="background-color:#333;" type="datetime-local" value="<?php echo date("Y-m-d H:i");?>" required name="start_at" id="start_at" class="form-control " >
    </div>

         <div id="end_at2" class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Dia de Pago</label>
      <select style="background-color: #333;" name="payment_day" id="payment_day"  class="form-control select2">
      <option value="1">DIARIO</option>
      <option value="7">SEMANAL</option>
      <option value="15">QUINCENAL</option>
      <option value="30">MENSUAL</option>
      </select>
      <select style="background-color: #333;" hidden name="selectdate" id="selectdate"  class="form-control"></select>
        </div>

  
  <div class="col-md-6 col-12">
  <label class="col-md-12 col-12 control-label">
    <?php 
      switch (Core::$user->language){
        case 'ES': echo "Lugar a Entregar"; break;
        case 'EN': echo "Place to Deliver"; break;
      }
    ?>
  </label>

  <div class="input-group">
    <!-- SELECT2 visible al inicio -->
    <select id="place_start" class="form-control select2" name="place_start" style="background-color:#333;">
      <option value="" selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>
      <?php foreach(PlaceData::getAll() as $place): ?>
        <option value="<?php echo $place->name; ?>"><?php echo $place->name; ?></option>
      <?php endforeach; ?>
    </select>

    <!-- INPUT oculto al inicio -->
    <input type="text" id="place_start2" name="place_start2" class="form-control" placeholder="Escribe aquí" style="background-color:#333; display:none;">
  </div>

  <!-- BOTÓN DE CAMBIO -->
  <small id="toggleplace_start" style="color:orange; font-size:13px; margin-top:5px; display:block; cursor:pointer;">
    <?php 
switch (Core::$user->language){
 case 'ES': echo "CAMBIAR ENTRE ESCRIBIR Y SELECCIONAR"; break;
 case 'EN': echo "SWITCH BETWEEN WRITE AND SELECT"; break;
}
?>
  </small>
</div>

<script>
  $(document).ready(function () {
    let modoManual = false;

    $('#toggleplace_start').click(function () {
      if (!modoManual) {
        // Ocultar SELECT2 y mostrar INPUT
        $('#place_start').select2('destroy').hide();
        $('#place_start2').show();
        $('#place_start').val('');
        modoManual = true;
      } else {
        // Volver a mostrar SELECT2 y ocultar INPUT
        $('#place_start2').hide();
        $('#place_start2').val('');
        $('#place_start').show().select2();
        modoManual = false;
      }
    });
  });
</script>



    <div class="col-md-6 col-12">
  <label class="col-md-12 col-12 control-label">
    <?php 
      switch (Core::$user->language){
        case 'ES': echo "Lugar de Devolucion"; break;
        case 'EN': echo "Place to Receive"; break;
      }
    ?>
  </label>

  <div class="input-group">
    <!-- SELECT2 visible al inicio -->
    <select id="place_end" class="form-control select2" name="place_end" style="background-color:#333;">
      <option value="" selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>
      <?php foreach(PlaceData::getAll() as $place): ?>
        <option value="<?php echo $place->name; ?>"><?php echo $place->name; ?></option>
      <?php endforeach; ?>
    </select>

    <!-- INPUT oculto al inicio -->
    <input type="text" id="place_end2" name="place_end2" class="form-control" placeholder="Escribe aquí" style="background-color:#333; display:none;">
  </div>

  <!-- BOTÓN DE CAMBIO -->
  <small id="toggleplace_end" style="color:orange; font-size:13px; margin-top:5px; display:block; cursor:pointer;">
    <?php 
switch (Core::$user->language){
 case 'ES': echo "CAMBIAR ENTRE ESCRIBIR Y SELECCIONAR"; break;
 case 'EN': echo "SWITCH BETWEEN WRITE AND SELECT"; break;
}
?>
  </small>
</div>


<script>
  $(document).ready(function () {
    let modoManual2 = false;

    $('#toggleplace_end').click(function () {
      if (!modoManual2) {
        // Ocultar SELECT2 y mostrar INPUT
        $('#place_end').select2('destroy').hide();
        $('#place_end2').show();
        $('#place_end').val('');
        modoManual2 = true;
      } else {
        // Volver a mostrar SELECT2 y ocultar INPUT
        $('#place_end2').hide();
        $('#place_end2').val('');
        $('#place_end').show().select2();
        modoManual2 = false;
      }
    });
  });
</script>

      </div>
      </div>
      
<!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
      <div class="damage-pane fade" id="step3" role="tabpanel">
          <div class="row">
       
       <div class="col-md-2 col-12" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Modo"; break;
 case 'EN': echo "Mode"; break;
}
?></label>
    <select style="background-color: #333;" name="method" class="form-control" id="method" onchange="showMethod()">
      <option value="1"><?php 
switch (Core::$user->language){
 case 'ES': echo "Disponible"; break;
 case 'EN': echo "Available"; break;
}
?></option>
      <option value="2"><?php 
switch (Core::$user->language){
 case 'ES': echo "Rejuego"; break;
 case 'EN': echo "Replay"; break;
}
?></option>

<?php if(StockData::getPrincipal()->update=="1"):?>
<option value="3"><?php 
switch (Core::$user->language){
 case 'ES': echo "Externo"; break;
 case 'EN': echo "external"; break;
}
?></option>
<?php endif;?>
    
      </select>
    </div>

    <div class="col-md-3 col-12" hidden>
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Ubicacion</label>
    
    <select style="background-color: #333;" name="location" class="form-control" id="location">
    <?php foreach(LocationData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<select hidden style="background-color: #333;" name="stock_id" id="select2lista"  class="form-control" onchange="showInp()"></select>
  
  
  <div class="col-md-3 col-12" id="stock_id2" style="display: none">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Suplidor"; break;
 case 'EN': echo "Supplier"; break;
}
?></label>
      <input style="background-color: #333;" type="text" name="stock_id2"  class="form-control" placeholder="Nombre del Rent A Car"> 
    </div>
  
  
  <div class="col-md-2 col-12" id="rpayment" style="display: none">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Precio/Dia</label>
      <input style="background-color: #333;" type="number" value="0" name="rpayment"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" min="0" step="0.01"> 
    </div>
    
    
    <div class="col-md-5 col-12" id="cars1">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label" id="type_cars1"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></label>
    <label for="inputEmail1" class="col-md-12 col-12 control-label" id="type_cars3"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo (Rejuego)"; break;
 case 'EN': echo "Vehicle (Replay)"; break;
}
?></label>
    <select style="background-color: #333;" name="car_id"  id="cars" class="form-control select2"></select>
    </div>
  </div>
  
    <script>
        $(document).ready(function () {
            $('#cars').select2({
                templateResult: function (data) {
                    if (!data.id) {
                        return data.text;
                    }
                    
                    // Recuperar la descripción desde el atributo `data-description`
                    const description = $(data.element).data('description');

                    const $template = $(
                        `<div>
                            <strong>${data.text}</strong>
                            <div style="font-size: 12px; color: orange;">${description || ''}</div>
                        </div>`
                    );
                    return $template;
                },
                templateSelection: function (data) {
                    return data.text; // Mostrar solo el texto seleccionado
                }
            });
        });
    </script>
    
   <div class="col-md-5 col-12" id="cars3">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo (Solicitado)"; break;
 case 'EN': echo "Vehicle (Requested)"; break;
}
?></label>
    <select style="background-color: #333;" name="car2_id" id="cars2" class="form-control select2">
    <option value="0">--<?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?>--</option>
    <?php foreach(CarsData::getAllBySQL("where status<>4") as $cars): $provider = SuppliersData::getById($cars->provider_id);?>
      <option value="<?php echo $cars->id;?>" data-description="<?php echo strtoupper($cars->getStock()->name);?>"><?php echo $cars->getBrand()->name." ".$cars->name." ".$cars->year." F: [".$cars->token."].";?></option>
    <?php endforeach;?>
      </select>
    </div>
  </div>
 
 
 

    <script>
        $(document).ready(function () {
            $('#cars2').select2({
                templateResult: function (data) {
                    if (!data.id) {
                        return data.text;
                    }
                    
                    // Recuperar la descripción desde el atributo `data-description`
                    const description = $(data.element).data('description');

                    const $template = $(
                        `<div>
                            <strong>${data.text}</strong>
                            <div style="font-size: 12px; color: orange;">${description || ''}</div>
                        </div>`
                    );
                    return $template;
                },
                templateSelection: function (data) {
                    return data.text; // Mostrar solo el texto seleccionado
                }
            });
        });
    </script>
  
 
 
    <select hidden style="background-color: #333;" name="divisa_id" id="divisa_id" class="form-control">
    <option value="1">DOLAR</option>
    </select>
    
     <div class="col-md-3 col-12" id="cars2_brand">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Marca"; break;
 case 'EN': echo "Brand"; break;
}
?></label>
    <select style="background-color: #333;"  name="cars2_brand" class="form-control select2" >
      <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
    <?php foreach(BrandData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

     <div class="col-md-2 col-12" id="cars2_name">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Modelo"; break;
 case 'EN': echo "Model"; break;
}
?></label>
      <input style="background-color: #333;" type="text" name="cars2_name" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>

 
  <div class="col-md-3 col-12" id="cars2_category">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Categoria"; break;
 case 'EN': echo "Category"; break;
}
?></label>
    <select style="background-color: #333;" name="cars2_category" class="form-control select2"  >
      <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
    <?php foreach(CategoryData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

    <div class="col-md-1 col-12" id="cars2_year">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Año"; break;
 case 'EN': echo "Year"; break;
}
?> </label>
      <input style="background-color: #333;" type="text" value="<?php echo date("Y");?>" name="cars2_year" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>

<div class="col-md-3 col-12" id="cars2_plate">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Placa"; break;
 case 'EN': echo "Plate No."; break;
}
?></label>
      <input style="background-color: #333;" type="text"  name="cars2_plate" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>
    
    
    
    <div class="col-md-5 col-12" id="cars2_chassis">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Chasis"; break;
 case 'EN': echo "Chassis No."; break;
}
?></label>
      <input style="background-color: #333;" type="text"  name="cars2_chassis" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>
  



<div id="extra" class="col-md-12 col-12"></div>

 <input style="background-color: #333;" type="hidden" id="unitx1" name="unit_extra1"   class="form-control"> 
  <input style="background-color: #333;" type="hidden" id="pricex1" name="price_extra1"  class="form-control"> 
  
  
 <input style="background-color: #333;" type="hidden" id="unitx2" name="unit_extra2"  class="form-control"> 
  <input style="background-color: #333;" type="hidden" id="pricex2" name="price_extra2"  class="form-control"> 
  
  
  
 <input style="background-color: #333;" type="hidden" id="unitx3" name="unit_extra3"  class="form-control"> 
  <input style="background-color: #333;" type="hidden" id="pricex3" name="price_extra3" class="form-control"> 
  
  
 <input style="background-color: #333;" type="hidden" id="unitx4" name="unit_extra4"  class="form-control"> 
  <input style="background-color: #333;" type="hidden" id="pricex4" name="price_extra4"  class="form-control"> 
  
      </div>
      </div>
      
<!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
      <div class="damage-pane fade" id="step4" role="tabpanel">

<?php if(StockData::getPrincipal()->update==1):
///////////////////??///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

  <div class="row">
     
<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Forma de Pago"; break;
 case 'EN': echo "Method of payment"; break;
}
?></label>
    <select style="background-color: #333;" name="f_id" required class="form-control select2">
    <?php foreach(FData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

  
   <div class="col-md-2 col-12"> 
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Tipo de Seguro"; break;
 case 'EN': echo "Insurance Type"; break;
}
?></label>
    
      <select style="background-color: #333;" class="form-control" name="type_sure">
      <?php foreach (SureData::getALL() as $sure): ?>
      <option value="<?php echo $sure->id;?>"><?php echo $sure->name;?></option>
      <?php endforeach ?>
      </select>
</div>
   <div class="col-md-2 col-12">
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Deducible"; break;
 case 'EN': echo "Deductible"; break;
}
?></label>
    
          <input style="background-color: #333;" type="text" name="sure" class="form-control" value="0"   placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" autocomplete="off" >
</div>
 <div hidden class="col-md-2 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Deposito</label>
     <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
      <input style="background-color: #333;" type="text" name="deposit" value="0" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" >
    </div>
  </div>

   <div class="col-md-3 col-12">
    <div class="input-group" >
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Combustible"; break;
 case 'EN': echo "Fuel"; break;
}
?></label>

    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-tint"></i></span>
     <select style="background-color:#333;" name="fuel"  class="form-control">
      <option value="R"><?php 
      switch (Core::$user->language){
        case 'ES': echo "Reserva"; break;
        case 'EN': echo "Booking"; break;
      }
    ?></option>
      <option value="1/4">1/4</option>
      <option value="1/2"><?php 
      switch (Core::$user->language){
        case 'ES': echo "Medio"; break;
        case 'EN': echo "Half"; break;
      }
    ?></option>
      <option value="3/4">3/4</option>
      <option value="F">Full</option>
     </select>
    </div>
  </div>
  
  
        <div class="col-md-2 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Dias de Renta"; break;
 case 'EN': echo "Income Days"; break;
}
?></label>
    <input style="background-color: #333;" id="dias" name="day"  class="form-control">
    </div>
</div>

    
    <?php
// ===============================
// Consultamos la tasa de USD->DOP
// ===============================
$tasa = null;
$url  = "https://open.er-api.com/v6/latest/USD"; // API pública y estable

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode == 200 && $response !== false) {
    $data = json_decode($response, true);
    if (isset($data["rates"]["DOP"])) {
        $tasa = $data["rates"]["DOP"];
    }
}
?>


  <!-- Pesos -->
  <div class="col-md-3 col-12">
    <label for="purchase_price" class="col-md-12 col-12 control-label">
      <?php 
switch (Core::$user->language){
 case 'ES': echo "Precio por Dia (USD<i class='fa fa-dollar-sign'></i>)"; break;
 case 'EN': echo "Price per day (USD<i class='fa fa-dollar-sign'></i>)"; break;
}
?> <span class="text-danger">*</span>
    </label>
    <div class="input-group">
      <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
    <input style="background-color: #333;" type="number"  name="price2" id="tariff2"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>"  min="0" step="0.01">
    </div>
  </div>
  


  <!-- USD -->
  <div class="col-md-3 col-12">
    <label for="usd_price" class="col-md-12 col-12 control-label">
     <?php 
switch (Core::$user->language){
 case 'ES': echo "Equivalente en (RD<i class='fa fa-dollar-sign'></i>)"; break;
 case 'EN': echo "Equivalent in (RD<i class='fa fa-dollar-sign'></i>)"; break;
}
?>
    </label>
    <div class="input-group">
      <span class="input-group-text">RD <i class='fa fa-dollar-sign'></i></span>
      <input type="text" id="usd_price" name="usd_price" class="form-control" readonly placeholder="0.00 USD">
    </div>
  </div>

  <!-- Tasa -->
  <div class="col-md-3 col-12">
    <label for="tasa_dolar" class="col-md-12 col-12 control-label">
      <?php 
switch (Core::$user->language){
 case 'ES': echo "Tasa Actual del Dólar"; break;
 case 'EN': echo "Current Dollar Rate"; break;
}
?>
    </label>
    <div class="input-group">
      <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
      <input type="text" name="tasa_dolar" id="tasa_dolar" class="form-control" readonly 
             value="<?php echo $tasa ? number_format($tasa, 2) . ' DOP/USD' : 'Buscando...'; ?>">
    </div>
    
  </div>


<script>
async function obtenerTasa() {
  try {
    let res = await fetch("https://open.er-api.com/v6/latest/USD");
    let data = await res.json();
    if (data && data.rates && data.rates.DOP) {
      return data.rates.DOP;
    }
  } catch (e) {
    console.error("Error obteniendo tasa:", e);
  }
  return null;
}

async function actualizarTasa() {
  const inputPesos = document.getElementById("tariff2");
  const inputUSD   = document.getElementById("usd_price");
  const inputTasa  = document.getElementById("tasa_dolar");
  const infoFecha  = document.getElementById("ultima_actualizacion");

  let tasa = await obtenerTasa();

  if (tasa) {
    inputTasa.value = tasa.toFixed(2);

    // Mostrar la hora de la última actualización
    infoFecha.innerText = "Última actualización en (Forex): " + new Date().toLocaleString();

    inputPesos.addEventListener("input", () => {
      let pesos = parseFloat(inputPesos.value) || 0;
      let dolares = pesos * tasa;
      inputUSD.value = dolares.toFixed(2);
    });
  } else {
    inputTasa.value = "Buscando...";
    inputUSD.value  = "Esperando tasa...";
    infoFecha.innerText = "Última actualización: fallo en la conexión";
  }
}

// primera carga
document.addEventListener("DOMContentLoaded", actualizarTasa);

// reintento cada 60 segundos
setInterval(actualizarTasa, 60000);
</script>
    
    <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php echo StockData::getPrincipal()->imp_name; ?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-list-ol"></i></span>
     <select style="background-color: #333;" name="iva" id="iva"  class="form-control" onchange="showIva()">
         <option value="0"><?php 
switch (Core::$user->language){
 case 'ES': echo "NO"; break;
 case 'EN': echo "NOT"; break;
}
?></option>
         <option value="<?php echo StockData::getPrincipal()->imp_val;?>"><?php 
switch (Core::$user->language){
 case 'ES': echo "SI"; break;
 case 'EN': echo "YES"; break;
}
?></option>
     </select>
    </div>
  </div>


   <div class="col-md-3 col-12" id="type_iva">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Comprobante"; break;
 case 'EN': echo "Voucher"; break;
}
?></label>
    <select style="background-color: #333;" name="type_iva" class="form-control select2"  >
     <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
     <?php foreach(CData::getAllBySQL("where de>0 and hasta>0") as $c):?>
      <option value="<?php echo $c->id."-".$c->serie."-".$c->de;?>"><?php echo $c->name;?></option>
    <?php endforeach;?>
      </select>
    </div>  
    
     


     <div class="col-md-2 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Total Reserva"; break;
 case 'EN': echo "Total Reserve"; break;
}
?></label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <select style="background-color: #333;" name="total" id="amount"  class="form-control"></select>
    </div>
  </div>
  
   <div class="col-md-2 col-12" id="iva_value">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Valor"; break;
 case 'EN': echo "Worth"; break;
}
?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</label>
    <input style="background-color: #333;" id="value_iva" name="value_iva"  class="form-control" readonly>
    </div>   
  
   <div class="col-md-2 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Extra</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <select style="background-color: #333;" name="xtotal" id="xmount"  class="form-control"></select>
    </div>
  </div>



    <div class="col-md-2 col-12">   
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Abono o Total"; break;
 case 'EN': echo "Subscription or Total"; break;
}
?></label>
   
      <input style="background-color: #333;" type="number" value="0"  name="payment" id="payment" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" min="0" step="0.01">
    </div>
  </div>

    <div class="col-md-2 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Monto Restante"; break;
 case 'EN': echo "Remaining Amount"; break;
}
?></label>
    
     <input style="background-color: #333;" readonly id="remaining" name="remaining"  class="form-control">
    </div>
  </div>

 <div class="col-md-2 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Otros Cobros"; break;
 case 'EN': echo "Other Charges"; break;
}
?></label>
     <input style="background-color: #333;" type="number" name="plane" value="0"  class="form-control" min="0" step="0.01">
    </div>
  </div>
  
  <p id="ultima_actualizacion" style="color:#aaa; font-size:12px; margin-top:5px;">
      Última actualización: -
    </p>
  </div>

<?php else:
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

          <div class="row">
     
<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Forma de Pago"; break;
 case 'EN': echo "Method of payment"; break;
}
?></label>
    <select style="background-color: #333;" name="f_id" required class="form-control select2">
    <?php foreach(FData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

  
   <div class="col-md-3 col-12"> 
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Tipo de Seguro"; break;
 case 'EN': echo "Insurance Type"; break;
}
?></label>
    
      <select style="background-color: #333;" class="form-control" name="type_sure">
      <?php foreach (SureData::getALL() as $sure): ?>
      <option value="<?php echo $sure->id;?>"><?php echo $sure->name;?></option>
      <?php endforeach ?>
      </select>
</div>
   <div class="col-md-3 col-12">
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Deducible"; break;
 case 'EN': echo "Deductible"; break;
}
?></label>
    
          <input style="background-color: #333;" type="text" name="sure" class="form-control" value="0"   placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" autocomplete="off" >
</div>
 <div hidden class="col-md-4 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Deposito</label>
     <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
      <input style="background-color: #333;" type="text" name="deposit" value="0" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" >
    </div>
  </div>

   <div class="col-md-3 col-12">
    <div class="input-group" >
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Combustible"; break;
 case 'EN': echo "Fuel"; break;
}
?></label>

    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-tint"></i></span>
     <select style="background-color:#333;" name="fuel"  class="form-control">
      <option value="R"><?php 
      switch (Core::$user->language){
        case 'ES': echo "Reserva"; break;
        case 'EN': echo "Booking"; break;
      }
    ?></option>
      <option value="1/4">1/4</option>
      <option value="1/2"><?php 
      switch (Core::$user->language){
        case 'ES': echo "Medio"; break;
        case 'EN': echo "Half"; break;
      }
    ?></option>
      <option value="3/4">3/4</option>
      <option value="F">Full</option>
     </select>
    </div>
  </div>
  
  
        <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Dias de Renta"; break;
 case 'EN': echo "Income Days"; break;
}
?></label>
    <input style="background-color: #333;" id="dias" name="day"  class="form-control">
    </div>
</div>


  <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Precio por Dia"; break;
 case 'EN': echo "Price per day"; break;
}
?></label>
    <input style="background-color: #333;" type="number"  name="price2" id="tariff2"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>"  min="0" step="0.01">
    </div>
   
    </div>
    
     <input type="hidden" name="tasa_dolar" value="0">
     <input type="hidden" id="usd_price" name="usd_price" value="0">
    
    <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php echo StockData::getPrincipal()->imp_name; ?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-list-ol"></i></span>
     <select style="background-color: #333;" name="iva" id="iva"  class="form-control" onchange="showIva()">
         <option value="0"><?php 
switch (Core::$user->language){
 case 'ES': echo "NO"; break;
 case 'EN': echo "NOT"; break;
}
?></option>
         <option value="<?php echo StockData::getPrincipal()->imp_val;?>"><?php 
switch (Core::$user->language){
 case 'ES': echo "SI"; break;
 case 'EN': echo "YES"; break;
}
?></option>
     </select>
    </div>
  </div>


   <div class="col-md-3 col-12" id="type_iva">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Comprobante"; break;
 case 'EN': echo "Voucher"; break;
}
?></label>
    <select style="background-color: #333;" name="type_iva" class="form-control select2"  >
     <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
     <?php foreach(CData::getAllBySQL("where de>0 and hasta>0") as $c):?>
      <option value="<?php echo $c->id."-".$c->serie."-".$c->de;?>"><?php echo $c->name;?></option>
    <?php endforeach;?>
      </select>
    </div>  
    
     


     <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Total Reserva"; break;
 case 'EN': echo "Total Reserve"; break;
}
?></label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <select style="background-color: #333;" name="total" id="amount"  class="form-control"></select>
    </div>
  </div>
  
   <div class="col-md-3 col-12" id="iva_value">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Valor"; break;
 case 'EN': echo "Worth"; break;
}
?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</label>
    <input style="background-color: #333;" id="value_iva" name="value_iva"  class="form-control" readonly>
    </div>   
  
   <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Extra</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <select style="background-color: #333;" name="xtotal" id="xmount"  class="form-control"></select>
    </div>
  </div>



    <div class="col-md-3 col-12">   
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Abono o Total"; break;
 case 'EN': echo "Subscription or Total"; break;
}
?></label>
   
      <input style="background-color: #333;" type="number" value="0"  name="payment" id="payment" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" min="0" step="0.01">
    </div>
  </div>

    <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Monto Restante"; break;
 case 'EN': echo "Remaining Amount"; break;
}
?></label>
    
     <input style="background-color: #333;" readonly id="remaining" name="remaining"  class="form-control">
    </div>
  </div>

 <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Otros Cobros"; break;
 case 'EN': echo "Other Charges"; break;
}
?></label>
     <input style="background-color: #333;" type="number" name="plane" value="0"  class="form-control" min="0" step="0.01">
    </div>
  </div>
  
  </div>
  
<?php endif;?>
                     <input style="background-color: #333;" type="hidden" name="user_id" value="<?php echo $_GET['id'];?>">
                    
    
    
 <div hidden id="day"></div>
  

    </div>
    
    <!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
      <div class="damage-pane fade" id="step5" role="tabpanel">
          
          <div class="row">
         
           <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="cat" id="checkboxPrimary1" checked>
<label for="checkboxPrimary1">
<?php 
switch (Core::$user->language){
 case 'ES': echo "GATO"; break;
 case 'EN': echo "CAT"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="radio" id="checkboxPrimary2" checked>
                        <label for="checkboxPrimary2">
                          RADIO
                        </label>
                      </div>
    </div>

    <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox"  name="replacement" id="checkboxPrimary3" checked>
<label for="checkboxPrimary3">
<?php 
switch (Core::$user->language){
 case 'ES': echo "REPUESTO"; break;
 case 'EN': echo "REPLACEMENT"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="antenna" id="checkboxPrimary4" checked>
                        <label for="checkboxPrimary4">
                          <?php 
switch (Core::$user->language){
 case 'ES': echo "ANTENA"; break;
 case 'EN': echo "ANTENNA"; break;
}
?>
                        </label>
                      </div>
    </div>


    <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="keyring" id="checkboxPrimary5" checked>
<label for="checkboxPrimary5">
<?php 
switch (Core::$user->language){
 case 'ES': echo "LLAVERO"; break;
 case 'EN': echo "KEY RING"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="carpets" id="checkboxPrimary6" checked>
                        <label for="checkboxPrimary6">
                          <?php 
switch (Core::$user->language){
 case 'ES': echo "ALFOMBRAS"; break;
 case 'EN': echo "CARPETS"; break;
}
?>
                        </label>
                      </div>
    </div>

   <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="belts" id="checkboxPrimary7" checked>
<label for="checkboxPrimary7">
<?php 
switch (Core::$user->language){
 case 'ES': echo "CINTURONES"; break;
 case 'EN': echo "BELTS"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="roof_lining" id="checkboxPrimary8" checked>
                        <label for="checkboxPrimary8">
                         <?php 
switch (Core::$user->language){
 case 'ES': echo "FORRO TECHO"; break;
 case 'EN': echo "ROOF LINING"; break;
}
?>
                        </label>
                      </div>
    </div>    

   <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="mirrors" id="checkboxPrimary9" checked>
<label for="checkboxPrimary9">
<?php 
switch (Core::$user->language){
 case 'ES': echo "ESPEJOS"; break;
 case 'EN': echo "MIRRORS"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="board" id="checkboxPrimary10" checked>
                        <label for="checkboxPrimary10">
                         <?php 
switch (Core::$user->language){
 case 'ES': echo "TABLERO"; break;
 case 'EN': echo "BOARD"; break;
}
?>
                        </label>
                      </div>
    </div>  

       <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="document" id="checkboxPrimary11" checked>
<label for="checkboxPrimary11">
<?php 
switch (Core::$user->language){
 case 'ES': echo "DOCUMENTOS"; break;
 case 'EN': echo "DOCUMENTS"; break;
}
?>
 
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="watches" id="checkboxPrimary12" checked>
                        <label for="checkboxPrimary12">
                         <?php 
switch (Core::$user->language){
 case 'ES': echo "RELOJES"; break;
 case 'EN': echo "WATCHES"; break;
}
?>
 
                        </label>
                      </div>
    </div>  


       <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="rearview" id="checkboxPrimary13" checked>
<label for="checkboxPrimary13">
<?php 
switch (Core::$user->language){
 case 'ES': echo "RETREVISOR"; break;
 case 'EN': echo "REVIEWER"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="lighter" id="checkboxPrimary14" checked>
                        <label for="checkboxPrimary14">
                         <?php 
switch (Core::$user->language){
 case 'ES': echo "ENCENDEDOR"; break;
 case 'EN': echo "LIGHTER"; break;
}
?>
                        </label>
                      </div>
    </div>  

           <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="crystals" id="checkboxPrimary15" checked>
<label for="checkboxPrimary15">
<?php 
switch (Core::$user->language){
 case 'ES': echo "CRISTALES"; break;
 case 'EN': echo "CRYSTALS"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="cd" id="checkboxPrimary16" checked>
                        <label for="checkboxPrimary16">
                        <?php 
switch (Core::$user->language){
 case 'ES': echo "PORTA CD"; break;
 case 'EN': echo "CD HOLDER"; break;
}
?>
                        </label>
                      </div>
    </div>  


           <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="bumper" id="checkboxPrimary17" checked>
<label for="checkboxPrimary17">
<?php 
switch (Core::$user->language){
 case 'ES': echo "TAPA COV. BUMPER"; break;
 case 'EN': echo "VOC COVER BUMPER"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="equalizer" id="checkboxPrimary18" checked>
                        <label for="checkboxPrimary18">
                         <?php 
switch (Core::$user->language){
 case 'ES': echo "ECUALIZADOR"; break;
 case 'EN': echo "EQUALIZER"; break;
}
?>
                        </label>
                      </div>
    </div>  

               <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="cup_holder" id="checkboxPrimary19" checked>
<label for="checkboxPrimary19">
<?php 
switch (Core::$user->language){
 case 'ES': echo "PORTA VASOS"; break;
 case 'EN': echo "CUP HOLDER"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="plate" id="checkboxPrimary20" checked>
                        <label for="checkboxPrimary20">
                         <?php 
switch (Core::$user->language){
 case 'ES': echo "PLACA"; break;
 case 'EN': echo "PLATE"; break;
}
?>
                        </label>
                      </div>
    </div>  

 

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="seats" id="checkboxPrimary21" checked>
                        <label for="checkboxPrimary21">
                         <?php 
switch (Core::$user->language){
 case 'ES': echo "ASIENTOS"; break;
 case 'EN': echo "SEATING"; break;
}
?>
                        </label>
                      </div>
    </div>  

               <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="logo" id="checkboxPrimary22" checked>
<label for="checkboxPrimary22">
LOGOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="batery" id="checkboxPrimary23" checked>
                        <label for="checkboxPrimary23">
                        <?php 
switch (Core::$user->language){
 case 'ES': echo "BATERIA"; break;
 case 'EN': echo "BATTERY"; break;
}
?>
                        </label>
                      </div>
    </div> 



    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="top" id="checkboxPrimary24" checked>
                        <label for="checkboxPrimary24">
<?php 
switch (Core::$user->language){
 case 'ES': echo "TAPA COMBUSTIBLE"; break;
 case 'EN': echo "FUEL CAP"; break;
}
?>
                   
                        </label>
                      </div>
    </div> 
         
              
          </div>
         
    </div>
    
     <!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
      <div class="damage-pane fade" id="step6" role="tabpanel">

 <div class="card card card-outline"  style="background-color:#222;">
         
          <div class="card-body">
              
         <div class="nav-wrapper">
  <ul class="nav nav-tabs d-flex flex-nowrap" id="custom-content-above-tab" role="tablist">
      
    <li class="nav-item">
      <a class="nav-link active" id="vert-tabs-frontal-tab" data-toggle="pill" href="#vert-tabs-frontal" role="tab" aria-controls="vert-tabs-frontal" aria-selected="true">FRONTAL</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-lateral-izquierdo-tab" data-toggle="pill" href="#vert-tabs-lateral-izquierdo" role="tab" aria-controls="vert-tabs-lateral-izquierdo" aria-selected="false"><?php 
switch (Core::$user->language){
 case 'ES': echo "LATERAL IZQUIERDO"; break;
 case 'EN': echo "LEFT SIDE"; break;
}
?></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-lateral-derecho-tab" data-toggle="pill" href="#vert-tabs-lateral-derecho" role="tab" aria-controls="vert-tabs-lateral-derecho" aria-selected="false"><?php 
switch (Core::$user->language){
 case 'ES': echo "LATERAL DERECHO"; break;
 case 'EN': echo "RIGHT BACK"; break;
}
?></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-trasera-tab" data-toggle="pill" href="#vert-tabs-trasera" role="tab" aria-controls="vert-tabs-trasera" aria-selected="false"><?php 
switch (Core::$user->language){
 case 'ES': echo "TRASERA"; break;
 case 'EN': echo "REAR"; break;
}
?></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-superior-tab" data-toggle="pill" href="#vert-tabs-superior" role="tab" aria-controls="vert-tabs-superior" aria-selected="false">SUPERIOR</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-inferior-tab" data-toggle="pill" href="#vert-tabs-inferior" role="tab" aria-controls="vert-tabs-inferior" aria-selected="false"><?php 
switch (Core::$user->language){
 case 'ES': echo "INFERIOR"; break;
 case 'EN': echo "LOWER"; break;
}
?></a>
    </li>
  </ul>
</div>

<style>
  .nav-wrapper {
    overflow-x: auto;  /* Permite el scroll horizontal */
    overflow-y: hidden; /* Evita el scroll vertical */
    white-space: nowrap;
  }
  .nav-tabs {
    flex-wrap: nowrap;
  }

    
#vert-tabs-frontal-tab.nav-link{
    color: orange !important;
}
#vert-tabs-lateral-izquierdo-tab.nav-link{
    color: orange !important;
}
#vert-tabs-lateral-derecho-tab.nav-link{
    color: orange !important;
}
#vert-tabs-trasera-tab.nav-link{
    color: orange !important;
}
#vert-tabs-superior-tab.nav-link{
    color: orange !important;
}
#vert-tabs-inferior-tab.nav-link{
    color: orange !important;
}
</style>




<div class="row">
          
              <div class="col-12 col-md-12">
                <div class="tab-content" id="vert-tabs-tabContent">
                  
                  <div class="tab-pane text-left fade show active" id="vert-tabs-frontal" role="tabpanel" aria-labelledby="vert-tabs-frontal-tab">
                     
            <!-- TO DO List -->
            <div  style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck1">
                      <label for="todoCheck1"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text1"><?php 
switch (Core::$user->language){
 case 'ES': echo "Capó"; break;
 case 'EN': echo "Hood"; break;
}
?></span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image1" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image1" type="file" style="display: none;" accept="image/*"  name="image1">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck1').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image1').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck1').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text1').style.textDecoration = 'line-through';
    }
  });
</script>
                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck2">
                      <label for="todoCheck2"></label>
                    </div>
                     <span class="text" id="text2"><?php 
switch (Core::$user->language){
 case 'ES': echo "Parachoques"; break;
 case 'EN': echo "Bumper"; break;
}
?></span>
                    <div class="tools">
                      <label for="image2" class="custom-file-upload"><i class="fa fa-upload"></i></label> 
                      <input id="image2" type="file" style="display: none;" accept="image/*"  name="image2">
                    </div>
                  </li>
                 
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck2').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image2').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck2').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text2').style.textDecoration = 'line-through';
    }
  });
</script>
                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck3">
                      <label for="todoCheck3"></label>
                    </div>
                     <span class="text" id="text3"><?php 
switch (Core::$user->language){
 case 'ES': echo "Faros"; break;
 case 'EN': echo "Headlights"; break;
}
?></span>
                    <div class="tools">
                     <label for="image3" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image3" type="file" style="display: none;" accept="image/*"  name="image3">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck3').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image3').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck3').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text3').style.textDecoration = 'line-through';
    }
  });
</script>
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck4">
                      <label for="todoCheck4"></label>
                    </div>
                     <span class="text" id="text4"><?php 
switch (Core::$user->language){
 case 'ES': echo "Parrilla"; break;
 case 'EN': echo "Grill"; break;
}
?></span>
                    <div class="tools">
                     <label for="image4" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image4" type="file" style="display: none;" accept="image/*"  name="image4">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck4').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image4').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck4').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text4').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck5">
                      <label for="todoCheck5"></label>
                    </div>
                     <span class="text" id="text5"><?php 
switch (Core::$user->language){
 case 'ES': echo "Parabrisas"; break;
 case 'EN': echo "Windshield"; break;
}
?></span>
                    <div class="tools">
                     <label for="image5" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image5" type="file" style="display: none;" accept="image/*"  name="image5">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck3').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image3').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck3').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text3').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck6">
                      <label for="todoCheck6"></label>
                    </div>
                     <span class="text" id="text6">Forlay</span>
                    <div class="tools">
                     <label for="image6" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image6" type="file" style="display: none;" accept="image/*"  name="image6">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck6').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image6').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck6').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text6').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                 <input style="background-color:#222;" autocomplete="off" name="comment1"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-lateral-izquierdo" role="tabpanel" aria-labelledby="vert-tabs-lateral-izquierdo-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
            
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck7">
                      <label for="todoCheck7"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text7"><?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?></span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image7" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image7" type="file" style="display: none;" accept="image/*"  name="image7">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck7').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image7').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck7').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text7').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck8">
                      <label for="todoCheck8"></label>
                    </div>
                     <span class="text" id="text8"><?php 
switch (Core::$user->language){
 case 'ES': echo "Guardafangos"; break;
 case 'EN': echo "Fenders"; break;
}
?></span>
                    <div class="tools">
                     <label for="image8" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image8" type="file" style="display: none;" accept="image/*"  name="image8">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck8').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image8').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck8').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text8').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck9">
                      <label for="todoCheck9"></label>
                    </div>
                     <span class="text" id="text9"><?php 
switch (Core::$user->language){
 case 'ES': echo "Espejos retrovisores"; break;
 case 'EN': echo "Rear view mirrors"; break;
}
?></span>
                    <div class="tools">
                     <label for="image9" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image9" type="file" style="display: none;" accept="image/*"  name="image9">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck9').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image9').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck9').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text9').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck10">
                      <label for="todoCheck10"></label>
                    </div>
                     <span class="text" id="text10"><?php 
switch (Core::$user->language){
 case 'ES': echo "Ventanas laterales"; break;
 case 'EN': echo "Side windows"; break;
}
?></span>
                    <div class="tools">
                     <label for="image10" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image10" type="file" style="display: none;" accept="image/*"  name="image10">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck10').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image10').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck10').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text10').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck11">
                      <label for="todoCheck11"></label>
                    </div>
                     <span class="text" id="text11"><?php 
switch (Core::$user->language){
 case 'ES': echo "Manijas de las puertas"; break;
 case 'EN': echo "Door handles"; break;
}
?></span>
                    <div class="tools">
                     <label for="image11" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image11" type="file" style="display: none;" accept="image/*"  name="image11">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck11').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image11').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck11').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text11').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck12">
                      <label for="todoCheck12"></label>
                    </div>
                     <span class="text" id="text12"><?php 
switch (Core::$user->language){
 case 'ES': echo "Llantas y rines"; break;
 case 'EN': echo "Tires and rims"; break;
}
?></span>
                    <div class="tools">
                     <label for="image12" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image12" type="file" style="display: none;" accept="image/*"  name="image12">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck12').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image12').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck12').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text12').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment2"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-lateral-derecho" role="tabpanel" aria-labelledby="vert-tabs-lateral-derecho-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
              
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
             
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck13">
                      <label for="todoCheck13"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text13"><?php 
switch (Core::$user->language){
 case 'ES': echo "Puertas"; break;
 case 'EN': echo "Doors"; break;
}
?></span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image13" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image13" type="file" style="display: none;" accept="image/*"  name="image13">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck13').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image13').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck13').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text13').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck14">
                      <label for="todoCheck14"></label>
                    </div>
                     <span class="text" id="text14"><?php 
switch (Core::$user->language){
 case 'ES': echo "Guardafangos"; break;
 case 'EN': echo "Fenders"; break;
}
?> </span>
                    <div class="tools">
                   <label for="image14" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                   <input id="image14" type="file" style="display: none;" accept="image/*"  name="image14">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck14').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image14').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck14').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text14').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck15">
                      <label for="todoCheck15"></label>
                    </div>
                     <span class="text" id="text15"><?php 
switch (Core::$user->language){
 case 'ES': echo "Espejos retrovisores"; break;
 case 'EN': echo "Rear view mirrors"; break;
}
?> </span>
                    <div class="tools">
                     <label for="image15" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image15" type="file" style="display: none;" accept="image/*"  name="image15">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck15').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image15').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck15').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text15').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck16">
                      <label for="todoCheck16"></label>
                    </div>
                     <span class="text" id="text16"><?php 
switch (Core::$user->language){
 case 'ES': echo "Ventanas laterales"; break;
 case 'EN': echo "Side windows"; break;
}
?></span>
                    <div class="tools">
                    <label for="image16" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                    <input id="image16" type="file" style="display: none;" accept="image/*"  name="image16">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck16').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image16').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck16').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text16').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck17">
                      <label for="todoCheck17"></label>
                    </div>
                     <span class="text" id="text17"><?php 
switch (Core::$user->language){
 case 'ES': echo "Manijas de las puertas"; break;
 case 'EN': echo "Door handles"; break;
}
?></span>
                    <div class="tools">
                     <label for="image17" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image17" type="file" style="display: none;" accept="image/*"  name="image17">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck17').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image17').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck17').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text17').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck18">
                      <label for="todoCheck18"></label>
                    </div>
                     <span class="text" id="text18"><?php 
switch (Core::$user->language){
 case 'ES': echo "Llantas y rines"; break;
 case 'EN': echo "Tires and rims"; break;
}
?></span>
                    <div class="tools">
                    <label for="image18" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image18" type="file" style="display: none;" accept="image/*"  name="image18">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck18').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image18').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck18').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text18').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment3"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>     
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-trasera" role="tabpanel" aria-labelledby="vert-tabs-trasera-tab">
                        
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
              
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck19">
                      <label for="todoCheck19"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text19"><?php 
switch (Core::$user->language){
 case 'ES': echo "Parachoques"; break;
 case 'EN': echo "Bumper"; break;
}
?></span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                    <label for="image19" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image19" type="file" style="display: none;" accept="image/*"  name="image19">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck19').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image19').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck19').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text19').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck20">
                      <label for="todoCheck20"></label>
                    </div>
                     <span class="text" id="text20"><?php 
switch (Core::$user->language){
 case 'ES': echo "Compuerta"; break;
 case 'EN': echo "Gate"; break;
}
?></span>
                    <div class="tools">
                     <label for="image20" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image20" type="file" style="display: none;" accept="image/*"  name="image20">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck20').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image20').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck20').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text20').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck21">
                      <label for="todoCheck21"></label>
                    </div>
                     <span class="text" id="text21"><?php 
switch (Core::$user->language){
 case 'ES': echo "Faros"; break;
 case 'EN': echo "Headlights"; break;
}
?></span>
                    <div class="tools">
                     <label for="image21" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image21" type="file" style="display: none;" accept="image/*"  name="image21">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck21').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image21').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck21').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text21').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck22">
                      <label for="todoCheck22"></label>
                    </div>
                     <span class="text" id="text22"><?php 
switch (Core::$user->language){
 case 'ES': echo "Escape"; break;
 case 'EN': echo "Exhaust"; break;
}
?></span>
                    <div class="tools">
                    <label for="image22" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                    <input id="image22" type="file" style="display: none;" accept="image/*"  name="image22">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck22').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image22').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck22').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text22').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck23">
                      <label for="todoCheck23"></label>
                    </div>
                     <span class="text" id="text23"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vidrio trasero"; break;
 case 'EN': echo "Rear glass"; break;
}
?></span>
                    <div class="tools">
                   <label for="image23" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                   <input id="image23" type="file" style="display: none;" accept="image/*"  name="image23">
                    </div>
                  </li>                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck23').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image23').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck23').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text23').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment4"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                    <div class="tab-pane fade" id="vert-tabs-superior" role="tabpanel" aria-labelledby="vert-tabs-superior-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck24">
                      <label for="todoCheck24"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text24"><?php 
switch (Core::$user->language){
 case 'ES': echo "Techo"; break;
 case 'EN': echo "Ceiling"; break;
}
?></span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                    <label for="image24" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image24" type="file" style="display: none;" accept="image/*"  name="image24">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck24').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image24').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck24').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text24').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck25">
                      <label for="todoCheck25"></label>
                    </div>
                     <span class="text" id="text25"><?php 
switch (Core::$user->language){
 case 'ES': echo "Antena"; break;
 case 'EN': echo "Antenna"; break;
}
?></span>
                    <div class="tools">
                   <label for="image25" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                   <input id="image25" type="file" style="display: none;" accept="image/*"  name="image25">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck25').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image25').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck25').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text25').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment5"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->      
                    <div class="tab-pane fade" id="vert-tabs-inferior" role="tabpanel" aria-labelledby="vert-tabs-inferior-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck26">
                      <label for="todoCheck26"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text26"><?php 
switch (Core::$user->language){
 case 'ES': echo "Chasis"; break;
 case 'EN': echo "Chassis"; break;
}
?></span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                  <label for="image26" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                  <input id="image26" type="file" style="display: none;" accept="image/*"  name="image26">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck26').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image26').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck26').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text26').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck27">
                      <label for="todoCheck27"></label>
                    </div>
                     <span class="text" id="text27"><?php 
switch (Core::$user->language){
 case 'ES': echo "Suspensión"; break;
 case 'EN': echo "Suspension"; break;
}
?></span>
                    <div class="tools">
                    <label for="image27" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image27" type="file" style="display: none;" accept="image/*"  name="image27">
                    </div>
                  </li>                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck27').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image27').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck27').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text27').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck28">
                      <label for="todoCheck28"></label>
                    </div>
                     <span class="text" id="text28"><?php 
switch (Core::$user->language){
 case 'ES': echo "Amortiguador"; break;
 case 'EN': echo "Shock absorber"; break;
}
?></span>
                    <div class="tools">
                    <label for="image28" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image28" type="file" style="display: none;" accept="image/*"  name="image28">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck28').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image28').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck28').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text28').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                  <div class="icheck-primary d-inline ml-2">
                  <input type="checkbox" id="todoCheck29"> <!-- Checkbox habilitado para envío -->
                  <label for="todoCheck29"></label>
                  </div>
                  <span class="text" id="text29"><?php 
switch (Core::$user->language){
 case 'ES': echo "Otros"; break;
 case 'EN': echo "Others"; break;
}
?></span>
                  <div class="tools">
                  <label for="image29" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                  <input id="image29" type="file" style="display: none;" accept="image/*"  name="image29">
                  </div>
                  </li>

<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck29').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image29').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck29').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text29').style.textDecoration = 'line-through';
    }
  });
</script>
                  
                  
                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment6"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>



                </div>
              </div>

                </div>

    </div>
    </div>
    
    </div>
     <!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
    <div class="damage-pane fade" id="step7" role="tabpanel">
      <div class="contenedor">

    <div class="row">
      <div class="col-md-12">
        <canvas id="draw-canvas" width="340" height="200">
          No tienes un buen navegador.
        </canvas>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
          
       
        
        <input  type="button" class="button btn-danger" id="draw-clearBtn" value="<?php 
switch (Core::$user->language){
 case 'ES': echo "Borrar Firma"; break;
 case 'EN': echo "Delete Signature"; break;
}
?>"></input>
     


            <label>Color</label>
            <input style="background-color:#333;" type="color" id="color">
            <input style="background-color:#333;" hidden type="range" id="puntero" min="1" default="1" max="5" width="10%">


      </div>

    </div>

  
    <div hidden class="row">
      <div class="col-md-12">
        <textarea style="background-color:#333;"  id="draw-dataUrl" class="form-control" name="base64" rows="5"></textarea>
      </div>
    </div>
  
  
  </div>
   
                     <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $_GET['id'];?>">
                   
                <div class="col-md-12 col-12 my-2">

                   <button type="submit" id="draw-submitBtn"  class="btn btn-success btn-block btn-sm "><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Entregar"; break;
 case 'EN': echo "Deliver"; break;
}
?></button>
                   
                
                </div>
  
       

  </div>

    <!-- Botones -->
    <div class="d-flex justify-content-between mt-3">
      <div class="btn btn-secondary" id="prevBtn">Regresar</div>
      <div class="btn btn-warning" id="nextBtn">Siguiente</div>
    </div>
 
</div>
</div>
 </form> 
<script>
  const $tabs = $('#stepTabs .nav-link');
  const $panes = $('.damage-pane');
  let current = 0;

  function updateStep() {
    $tabs.removeClass('active').eq(current).addClass('active');
    $panes.removeClass('show active').eq(current).addClass('show active');

    $('#prevBtn').toggle(current > 0);
    $('#nextBtn').toggle(current < $tabs.length - 1);
    
    
      // 👇 Activar tab "FRONTAL" automáticamente si estás en "Marcar Daño" (índice 5 = #step6)
  if ($tabs.eq(current).attr('href') === '#step6') {
    $('#custom-content-above-tab .nav-link').removeClass('active');
    $('#vert-tabs-tabContent .damage-pane').removeClass('show active');
    $('#vert-tabs-frontal-tab').addClass('active');
    $('#vert-tabs-frontal').addClass('show active');
  }
  
  }

  $('#nextBtn').click(function () {
    if (current < $tabs.length - 1) {
      current++;
      updateStep();
    }
  });

  $('#prevBtn').click(function () {
    if (current > 0) {
      current--;
      updateStep();
    }
  });

  // Inicialización
  updateStep();
  
 
    
    document.getElementById("cars3").style.display = "none";
    document.getElementById("type_cars1").style.display = "inline-block";
    document.getElementById("type_cars3").style.display = "none";
   
    document.getElementById("type_iva").style.display = "none";
    document.getElementById("iva_value").style.display = "none"; 
 
  
 function showIva(){
  var getSelectValue = document.getElementById("iva").value;

  if(getSelectValue==0){
      
    document.getElementById("type_iva").style.display = "none";
    document.getElementById("iva_value").style.display = "none";
     
    function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
    
   
}

$("#remaining").val(agregarSeparadorMiles(parseFloat($("#amount").val())-parseFloat($("#payment").val())));
    
  }else if(getSelectValue==<?php echo StockData::getPrincipal()->imp_val;?>){

    document.getElementById("type_iva").style.display = "inline-block"; 
    document.getElementById("iva_value").style.display = "inline-block"; 
    function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
    
   
}

$("#remaining").val(agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#amount").val()*0.<?php echo StockData::getPrincipal()->imp_val;?>))-parseFloat($("#payment").val())));

$("#value_iva").val(agregarSeparadorMiles(+parseFloat($("#amount").val()*0.<?php echo StockData::getPrincipal()->imp_val;?>)));

}

 }
   
    document.getElementById("cars2_name").style.display = "none";
    document.getElementById("cars2_plate").style.display = "none";
    document.getElementById("cars2_chassis").style.display = "none";
    document.getElementById("cars2_category").style.display = "none";
    document.getElementById("cars2_brand").style.display = "none";
    document.getElementById("cars2_year").style.display = "none"; 
    
    
      $(document).ready(function(){
    $('#xmount').val();
    recargarxLista();

  });
  
  
  function recargarxLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
       data: {unit1: $('#unit1').val(), price1:  $('#price1').val(), uni2: $('#unit2').val(), price2:  $('#price2').val(), unit3: $('#unit3').val(), price3:  $('#price3').val(), unit4: $('#unit4').val(), price4:  $('#price4').val()},
      success:function(r){
        $('#xmount').html(r);
      }
    });
  }

  
  
function showInp2(){
  var getSelectValue = document.getElementById("type_id").value;

  if(getSelectValue=="1"){
    document.getElementById("end_at1").style.display = "inline-block";
    document.getElementById("end_at2").style.display = "none";
  }
   if(getSelectValue=="2"){
    document.getElementById("end_at2").style.display = "inline-block";
    document.getElementById("end_at1").style.display = "none";
  }
  
}

document.getElementById("end_at2").style.display = "none";



 $(document).ready(function(){
    $('#location').val();
    recargarLista();

    $('#location').change(function(){
      recargarLista();
    });
  });
  
    function recargarLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=all",
      data:"location=" + $('#location').val(),
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }
  

  $(document).ready(function(){
    $('#select2lista').val();
    recargar2Lista();
  });
  
   $('#cars').change(function(){
 
  recargarExtras();
  
  
$("#unitx1").val(0);
$("#pricex1").val(0);

$("#unitx2").val(0);
$("#pricex2").val(0);

$("#unitx3").val(0);
$("#pricex3").val(0);

$("#unitx4").val(0);
$("#pricex4").val(0);


$("#xmount").val(0);

   function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
    
   
}

$("#remaining").val(agregarSeparadorMiles(parseFloat($("#amount").val())-parseFloat($("#payment").val())));

    });
    
    
   
  
  function recargarExtras(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=extra",
      data: {car_id: $('#cars').val()},
      success:function(r){
        $('#extra').html(r);
      }
    });
  }
  
   function recargar2Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
      data: {stock_id: $('#select2lista').val(),start_at: $('#start_at').val(),end_at: $('#end_at').val()},
      success:function(r){
        $('#cars').html(r);
      }
    });
  }
  
  
 $('#dias').prop('disabled', true);
  
   var inival = $("#end_at").val();
  $("#end_at").change(function(){
  if ( $("#end_at").val() != inival ) {
      
      
 $('#dias').prop('disabled', false);
      
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
     data: {stock_id: $('#select2lista').val(),start_at: $('#start_at').val(),end_at: $('#end_at').val()},
      success:function(r){
        $('#cars').html(r);
      }
    });
    
    
    function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni),
      hasta = new Date(fechaFin),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return  Math.round((diff / diaEnMils));
}


 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val())+parseFloat($("#xmount").val())));
    
  
  
  }
});





function showMethod(){
    
  var getSelectValue = document.getElementById("method").value;
  var getSelectValue2 = document.getElementById("select2lista").value;

  if(getSelectValue==1){
      
    $('.warning').hide();
       $('#draw-submitBtn').prop('disabled', true);
  
    
    $('#cars').on('change', () => {
    var value = $('#cars').val();
    
    if(value) {
       $('.warning').hide();
       $('#draw-submitBtn').prop('disabled', false);
    }
    

    
});

  document.getElementById("extra").style.display = "none";


 $('#dias').prop('disabled', false);
      
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
     data: {stock_id: $('#select2lista').val(),start_at: $('#start_at').val(),end_at: $('#end_at').val()},
      success:function(r){
        $('#cars').html(r);
      }
    });
    
    function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni),
      hasta = new Date(fechaFin),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return  Math.round((diff / diaEnMils));
}


 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val())+parseFloat($("#xmount").val())));
    
  
  
    
    document.getElementById("rpayment").style.display = "none";
    document.getElementById("stock_id2").style.display = "none"; 
    
    document.getElementById("cars2_name").style.display = "none";
    document.getElementById("cars2_plate").style.display = "none";
    document.getElementById("cars2_chassis").style.display = "none";
    document.getElementById("cars2_category").style.display = "none";
    document.getElementById("cars2_brand").style.display = "none";
    document.getElementById("cars2_year").style.display = "none";
    document.getElementById("cars1").style.display = "inline-block";
    document.getElementById("cars3").style.display = "none";
    document.getElementById("type_cars1").style.display = "inline-block";
    document.getElementById("type_cars3").style.display = "none";
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
  }else if(getSelectValue==2){
      
   $('.warning').hide();
       $('#draw-submitBtn').prop('disabled', true);
  
    
    $('#cars').on('change', () => {
    var value = $('#cars').val();
    
    if(value) {
       $('.warning').hide();
       $('#draw-submitBtn').prop('disabled', false);
    }
    

    
});
      
 $('#dias').prop('disabled', false);
      
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
     data: {stock_id: $('#select2lista').val(),start_at: $('#start_at').val(),end_at: $('#end_at').val()},
      success:function(r){
        $('#cars').html(r);
      }
    });
    
    function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni),
      hasta = new Date(fechaFin),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return  Math.round((diff / diaEnMils));
}


 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val())+parseFloat($("#xmount").val())));
    
  
      
  
    document.getElementById("rpayment").style.display = "none";
    document.getElementById("stock_id2").style.display = "none";
    
    document.getElementById("cars1").style.display = "inline-block";
    document.getElementById("cars3").style.display = "inline-block";
    document.getElementById("type_cars1").style.display = "none";
    document.getElementById("type_cars3").style.display = "inline-block";
    document.getElementById("cars2_name").style.display = "none";
    document.getElementById("cars2_plate").style.display = "none";
    document.getElementById("cars2_chassis").style.display = "none";
    document.getElementById("cars2_category").style.display = "none";
    document.getElementById("cars2_brand").style.display = "none";
    document.getElementById("cars2_year").style.display = "none";


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  }else if(getSelectValue==3){
      

    $('.warning').hide();
       $('#draw-submitBtn').prop('disabled', false);
  

  recargarLista();
  recargarExtras();
  document.getElementById("extra").style.display = "none";
    

  function recargarLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=all",
      data:"location=" + getSelectValue,
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }
  
 
      recargar2Lista();
    
  function recargar2Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
      data:"stock_id=" + getSelectValue2,
      success:function(r){
        $('#cars').html(r);
      }
    });
  }
  

      
    document.getElementById("stock_id2").style.display = "inline-block";
    
    document.getElementById("cars1").style.display = "none";
    document.getElementById("cars3").style.display = "none";
    document.getElementById("type_cars1").style.display = "none";
    document.getElementById("type_cars3").style.display = "none";
    document.getElementById("cars2_name").style.display =  "inline-block";
    document.getElementById("cars2_plate").style.display =  "inline-block";
    document.getElementById("cars2_chassis").style.display = "inline-block";
    document.getElementById("cars2_category").style.display =  "inline-block";
    document.getElementById("cars2_brand").style.display =  "inline-block";
    document.getElementById("cars2_year").style.display =  "inline-block";
    
   document.getElementById("rpayment").style.display = "inline-block";  
  }
 
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#cars').change(function(){
      recargar3Lista();
      
  document.getElementById("extra").style.display = "inline-block";
    });
 
 
 
  function recargar3Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
      data:"cars=" + $('#cars').val(),
      success:function(r){
        $('#tariff').html(r);
      }
    });
  }

    $('#tariff').change(function(){
      Lista();

    });
    
    
    

$("#payment").keyup(function(){
function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
 });
 
 



function Lista(){
    

  var getSelectValue = document.getElementById("type_id").value;

  if(getSelectValue=="1"){
    
function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
}

   
 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=amount",
       data: {paycar: $('#payment').val(),tcars: $('#tariff').val(),dvs: $('#divisa_id').val(),cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price",
       data: {cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });
$("#remaining").val(($('#tariff').val()*$('#dias').val())*$('#divisa_id').val());

}

 if(getSelectValue=="2"){
    
function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
}

   
 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=free",
      data:"freedate=" + $('#payment_day').val(),
      success:function(r){
        $('#selectdate').html(r);
      }
    });
  
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=amount",
       data: {paycar: $('#payment').val(),tcars: $('#tariff').val(),dvs: $('#divisa_id').val(),cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price",
       data: {cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });
$("#remaining").val(($('#tariff').val()*$('#dias').val())*$('#divisa_id').val());

}
}




    tariff2.addEventListener("keyup", function()
    {   


  var getSelectValue = document.getElementById("type_id").value;

  if(getSelectValue=="1"){
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff2",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val())+parseFloat($("#xmount").val())));
    
        
    }
    
    
    if(getSelectValue=="2"){

    function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
}

   
 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=free",
      data:"freedate=" + $('#payment_day').val(),
      success:function(r){
        $('#selectdate').html(r);
      }
    });
  


    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff2",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles(($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val()));
    
        
    }
    }, false);
    
</script>

             
<style>

section{
    flex:1;
}



.button {
    background: #3071a9;
    box-shadow: inset 0 -3px 0 rgba(0,0,0,.3);
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 5px;
    margin: 0 15px;
    text-decoration: none;
    color: white;
}

.button:active {
    transform: scale(0.9);
}

.contenedor {
    width: 100%
    margin: 5px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

input[type=range] {
  -webkit-appearance: none;
  margin: 18px 0;

}
input[type=range]:focus {
  outline: none;
}
input[type=range]::-webkit-slider-runnable-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-webkit-slider-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
  -webkit-appearance: none;
  margin-top: -14px;
}
input[type=range]:focus::-webkit-slider-runnable-track {
  background: #367ebd;
}
input[type=range]::-moz-range-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-moz-range-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]::-ms-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  background: transparent;
  border-color: transparent;
  border-width: 16px 0;
  color: transparent;
}
input[type=range]::-ms-fill-lower {
  background: #2a6495;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-fill-upper {
  background: #3071a9;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]:focus::-ms-fill-lower {
  background: #3071a9;
}
input[type=range]:focus::-ms-fill-upper {
  background: #367ebd;
}
</style>
<script>
/*
    El siguiente codigo en JS Contiene mucho codigo
    de las siguietes 3 fuentes:
    https://stipaltamar.github.io/dibujoCanvas/
    https://developer.mozilla.org/samples/domref/touchevents.html - https://developer.mozilla.org/es/docs/DOM/Touch_events
    http://bencentra.com/canvas/signature/signature.html - https://bencentra.com/code/2014/12/05/html5-canvas-touch-events.html
*/

(function() { // Comenzamos una funcion auto-ejecutable

  // Obtenenemos un intervalo regular(Tiempo) en la pamtalla
  window.requestAnimFrame = (function (callback) {
    return window.requestAnimationFrame ||
          window.webkitRequestAnimationFrame ||
          window.mozRequestAnimationFrame ||
          window.oRequestAnimationFrame ||
          window.msRequestAnimaitonFrame ||
          function (callback) {
            window.setTimeout(callback, 1000/60);
            // Retrasa la ejecucion de la funcion para mejorar la experiencia
          };
  })();

  // Traemos el canvas mediante el id del elemento html
  var canvas = document.getElementById("draw-canvas");
  var ctx = canvas.getContext("2d");


  // Mandamos llamar a los Elemetos interactivos de la Interfaz HTML
  var drawText = document.getElementById("draw-dataUrl");
  var drawImage = document.getElementById("draw-image");
  var clearBtn = document.getElementById("draw-clearBtn");
  var submitBtn = document.getElementById("draw-submitBtn");
  clearBtn.addEventListener("click", function (e) {
    // Definimos que pasa cuando el boton draw-clearBtn es pulsado
    clearCanvas();
    drawImage.setAttribute("src", "");
  }, false);
    // Definimos que pasa cuando el boton draw-submitBtn es pulsado
  submitBtn.addEventListener("click", function (e) {
  var dataUrl = canvas.toDataURL();
  drawText.innerHTML = dataUrl;
  drawImage.setAttribute("src", dataUrl);
   }, false);

  // Activamos MouseEvent para nuestra pagina
  var drawing = false;
  var mousePos = { x:0, y:0 };
  var lastPos = mousePos;
  canvas.addEventListener("mousedown", function (e)
  {
    /*
      Mas alla de solo llamar a una funcion, usamos function (e){...}
      para mas versatilidad cuando ocurre un evento
    */
    var tint = document.getElementById("color");
    var punta = document.getElementById("puntero");
    console.log(e);
    drawing = true;
    lastPos = getMousePos(canvas, e);
  }, false);
  canvas.addEventListener("mouseup", function (e)
  {
    drawing = false;
  }, false);
  canvas.addEventListener("mousemove", function (e)
  {
    mousePos = getMousePos(canvas, e);
  }, false);

  // Activamos touchEvent para nuestra pagina
  canvas.addEventListener("touchstart", function (e) {
    mousePos = getTouchPos(canvas, e);
    console.log(mousePos);
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var touch = e.touches[0];
    var mouseEvent = new MouseEvent("mousedown", {
      clientX: touch.clientX,
      clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchend", function (e) {
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var mouseEvent = new MouseEvent("mouseup", {});
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchleave", function (e) {
    // Realiza el mismo proceso que touchend en caso de que el dedo se deslice fuera del canvas
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var mouseEvent = new MouseEvent("mouseup", {});
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchmove", function (e) {
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var touch = e.touches[0];
    var mouseEvent = new MouseEvent("mousemove", {
      clientX: touch.clientX,
      clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
  }, false);

  // Get the position of the mouse relative to the canvas
  function getMousePos(canvasDom, mouseEvent) {
    var rect = canvasDom.getBoundingClientRect();
    /*
      Devuelve el tamaño de un elemento y su posición relativa respecto
      a la ventana de visualización (viewport).
    */
    return {
      x: mouseEvent.clientX - rect.left,
      y: mouseEvent.clientY - rect.top
    };
  }

  // Get the position of a touch relative to the canvas
  function getTouchPos(canvasDom, touchEvent) {
    var rect = canvasDom.getBoundingClientRect();
    console.log(touchEvent);
    /*
      Devuelve el tamaño de un elemento y su posición relativa respecto
      a la ventana de visualización (viewport).
    */
    return {
      x: touchEvent.touches[0].clientX - rect.left, // Popiedad de todo evento Touch
      y: touchEvent.touches[0].clientY - rect.top
    };
  }

  // Draw to the canvas
  function renderCanvas() {
    if (drawing) {
      var tint = document.getElementById("color");
      var punta = document.getElementById("puntero");
      ctx.strokeStyle = tint.value;
      ctx.beginPath();
      ctx.moveTo(lastPos.x, lastPos.y);
      ctx.lineTo(mousePos.x, mousePos.y);
      console.log(punta.value);
      ctx.lineWidth = punta.value;
      ctx.stroke();
      ctx.closePath();
      lastPos = mousePos;
    }
  }

  function clearCanvas() {
    canvas.width = canvas.width;
  }

  // Allow for animation
  (function drawLoop () {
    requestAnimFrame(drawLoop);
    renderCanvas();
  })();

})();    
</script>

<?php if(StockData::getPrincipal()->method=="1"): ?>
<style>

#draw-canvas {
  border: 2px dotted #CCCCCC;
  border-radius: 5px;
  cursor: crosshair;
  background-image: url("CF-SYSTEMS/storage/configuration/fxrma.png");
  background-size: 100% 100%;
}

#draw-dataUrl {
  width: 100%;
}

</style>

<?php elseif(StockData::getPrincipal()->method=="2"): ?>
<style>

#draw-canvas {
  border: 2px dotted #CCCCCC;
  border-radius: 5px;
  cursor: crosshair;
  background-image: url("CF-SYSTEMS/storage/configuration/furma.png");
  background-size: 100% 100%;
}

#draw-dataUrl {
  width: 100%;
}

</style>

<?php endif;?>


            </div>

            </div>
            </div>
</div>

</div>
</div>
</div>
  </div>
</div>

  
  </div>
                      </div>
                    </div>
                 

            </div>

            </div>

            </div>
            </div>
</div>

</div>
</div>
</div>
  </div>
</div>
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="newfree"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
         <h1 class="m-0"><i class='fa fa-user-plus'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Abierto"; break;
 case 'EN': echo "Open"; break;
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

<?php if(StockData::getPrincipal()->update=="1"):?>
 <div class="row">
 
<?php if(StockData::getPrincipal()->method=="1"):?>                
          <div class="col-12 col-sm-4 col-md-4 my-2">
          <a  href="./?view=contract&opt=new" class="floating-btn message-btn" style="background-color: #4DBE04;">
     <div class="icon-container">
    <i class="fa fa-calendar"></i>
    </div>
    <span class="message-text"> RANGO DE FECHA </span>
  </a>
            <!-- /.info-box -->
          </div>
<?php endif;?>          
          
          <div class="col-12 col-sm-4 col-md-4 my-2">
             <a href="./?view=contract&opt=newhours" class="floating-btn message-btn" style="background-color: #C70039;">
      <div class="icon-container">
    <i class="fa fa-history"> </i>
    </div>
    <span class="message-text"> POR HORA</span> 
  </a>
            <!-- /.info-box -->
          </div>
  </div>          <!-- /.col -->
<?php else:?>
<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>
<?php endif;?>         
 
        <div class="row">

            <div class="col-md-12">
                
                <!-- Profile Image -->
            <div class="card card-secondary card-outline" style="background-color:#222;">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="CF-SYSTEMS/storage/profiles/user.png"
                       alt="User profile picture">
                </div>

             <h3 class="profile-username text-center"><?php 
switch (Core::$user->language){
 case 'ES': echo "Datos de la Renta"; break;
 case 'EN': echo "Rent Data"; break;
}
?></h3>
 <div class="card-header p-0 pt-1">
  <div style="overflow-x: auto; white-space: nowrap;">
    <ul class="nav nav-tabs flex-nowrap" id="stepTabs" role="tablist" style="min-width: max-content;">
      <li class="nav-item" style="display: inline-block; min-width: 150px;">
        <a class="nav-link active disabled-tab text-center" href="#step1" role="tab">Datos del Cliente</a>
      </li>
      <li class="nav-item" style="display: inline-block; min-width: 150px;">
        <a class="nav-link disabled-tab text-center" href="#step2" role="tab">Dia & Lugar</a>
      </li>
      <li class="nav-item" style="display: inline-block; min-width: 150px;">
        <a class="nav-link disabled-tab text-center" href="#step3" role="tab">Elegir Vehículo</a>
      </li>
      <li class="nav-item" style="display: inline-block; min-width: 150px;">
        <a class="nav-link disabled-tab text-center" href="#step4" role="tab">Facturación</a>
      </li>
      <li class="nav-item" style="display: inline-block; min-width: 150px;">
        <a class="nav-link disabled-tab text-center" href="#step5" role="tab">Revisión</a>
      </li>
      <li class="nav-item" style="display: inline-block; min-width: 150px;">
        <a class="nav-link disabled-tab text-center" href="#step6" role="tab">Marcar Daño</a>
      </li>
      <li class="nav-item" style="display: inline-block; min-width: 150px;">
        <a class="nav-link disabled-tab text-center" href="#step7" role="tab">Firma</a>
      </li>
    </ul>
  </div>
</div>


<style>
  .disabled-tab {
    pointer-events: none;
    cursor: default;
  }
  
  /* Después: reemplaza por */
.damage-pane {
  display: none;
}
.damage-pane.active.show {
  display: block;
}
</style>

<form action="./?action=contract&opt=add" id="delivery" method="POST" enctype="multipart/form-data">
  
                
  <div class="card-body">
    <div class="tab-content" id="stepTabContent">
    
    <div class="damage-pane fade show active" id="step1" role="tabpanel">
  <input type="hidden" name="nuevo_cliente_activo" id="nuevo_cliente_activo" value="0">

  <div class="row">
    <div class="col-md-4 col-12">
      <label class="col-md-12 col-12 control-label">
        <?php echo Core::$user->language == 'EN' ? 'CUSTOMER/ COMPANY' : 'CLIENTE/ EMPRESA'; ?>
      </label>
      <select style="background-color: #333;" name="person_id" id="person_id" class="form-control select2" required>
        <option value="">-- <?php echo Core::$user->language == 'EN' ? 'CHOOSE' : 'ELEGIR'; ?> --</option>
        <?php foreach(PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id) as $client): ?>
          <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-4 col-12">
      <label class="col-md-12 col-12 control-label">
        <?php echo Core::$user->language == 'EN' ? 'OPTIONAL DRIVER' : 'CONDUCTOR OPCIONAL'; ?>
      </label>
      <select style="background-color: #333;" name="person2_id" id="person2_id" class="form-control select2">
        <option value="">-- <?php echo Core::$user->language == 'EN' ? 'CHOOSE' : 'ELEGIR'; ?> --</option>
        <?php foreach(PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id) as $client): ?>
          <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-4 col-12">
      <label class="col-md-12 col-12 control-label">&nbsp;</label>
      <button type="button" id="btn_toggle_cliente" class="btn btn-success btn-block">
        CREAR NUEVO
      </button>
    </div>
  </div>

  <!-- Formulario Nuevo Cliente -->
  <div id="form_nuevo_cliente" class="mt-4" style="display:none;">
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

      <select style="background-color:#333;"  name="location"  class="form-control select2">
      <option selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>
      <?php foreach(LocationData::getAll() as $loc):?>
      <option value="<?php echo $loc->id;?>"><?php echo $loc->name;?></option>
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
      <select style="background-color:#333;" name="type" class="form-control select2" id="type_person">
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
      <input style="background-color:#333;" type="text" autofocus name="name" autocomplete="off"  class="form-control" placeholder="<?php 
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
  <select style="background-color:#333;" name="gender"  class="form-control">
   
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
      echo "<option value=\"$val\">$label</option>";
    }
    ?>
  </select>
</div>

    
   <div class="col-md-3 col-12">
  <label for="inputEmail1" class="col-md-12 col-12 control-label">
    <?php echo Core::$user->language == 'ES' ? 'Idioma' : 'Language'; ?>
  </label>
 <select style="background-color:#333;" name="language"  class="form-control">
 
  <?php
  $lang = Core::$user->language;
  $options = [
    'ES' => ['ES' => 'Español', 'EN' => 'Inglés'],
    'EN' => ['EN' => 'English', 'ES' => 'Spanish']
  ];
  foreach ($options[$lang] as $val => $label) {
    echo "<option value=\"$val\">$label</option>";
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
      <select style="background-color:#333;"  name="estado"   class="form-control">
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

document.getElementById("rnc_id").style.display = "none";

    
$('#type_person').on('change', () => {
    var getSelectValue = $('#type_person').val();
  
   if(getSelectValue=="1") {
document.getElementById("rnc_id").style.display = "inline-block";
   }
   else if(getSelectValue=="0") {
document.getElementById("rnc_id").style.display = "none";
   }
  
});


</script>

    </div>
  </div>

  <!-- Botón Continuar -->

</div>

<script>
$(document).ready(function() {
  const $nuevoClienteForm = $('#form_nuevo_cliente');
  const $btnCrear = $('#btn_toggle_cliente');
  const $clienteSelect = $('#person_id');
  const $continuarWrap = $('#btn_continuar_wrap');
  const $nuevoClienteActivo = $('#nuevo_cliente_activo');

  function validarContinuar() {
    const activo = $nuevoClienteActivo.val() === '1';

    if (activo) {
      // Crear nuevo cliente: se oculta el selector
      $clienteSelect.prop('disabled', true).prop('required', false);

      // NO marcar inputs como required
      let hayAlgoLleno = false;
      $nuevoClienteForm.find('input').each(function() {
        if ($(this).val().trim() !== '') {
          hayAlgoLleno = true;
        }
      });

      // Mostrar continuar solo si hay al menos un campo lleno
      $continuarWrap.toggle(hayAlgoLleno);
    } else {
      // Usar selector: se activa
      $clienteSelect.prop('disabled', false).prop('required', true);

      // Nunca required en los inputs del formulario
      $nuevoClienteForm.find('input').removeAttr('required');

      // Mostrar continuar si hay cliente seleccionado
      $continuarWrap.toggle($clienteSelect.val() !== '');
    }
  }

  $btnCrear.click(function() {
    $nuevoClienteForm.slideToggle(300, function() {
      const visible = $nuevoClienteForm.is(':visible');
      $nuevoClienteActivo.val(visible ? '1' : '0');
      validarContinuar();
    });

    if ($btnCrear.text().toUpperCase().includes('CREAR')) {
      $btnCrear.html('<i class="fa fa-times"></i> Cancelar');
    } else {
      $btnCrear.html('<i class="fa fa-plus"></i> Crear nuevo');
    }
  });

  $clienteSelect.on('change', validarContinuar);
  $nuevoClienteForm.on('input change', 'input', validarContinuar);
});
</script>


<!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
      <div class="damage-pane fade" id="step2" role="tabpanel">
          <div class="row">
                        
                    <div hidden class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"></label>
    <select style="background-color:#333;" name="type_id"  class="form-control select2" id="type_id">
       <option value="2">ABIERTO</option>
      </select>
    </div>

 
        
    <div hidden class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"></label>
      <input style="background-color:#333;" type="datetime-local" value="<?php echo date("Y-m-d H:i");?>" required name="start_at" id="start_at" class="form-control " >
    </div>

     <div hidden class="col-md-3 col-12">
     <label for="inputEmail1" class="col-md-12 col-12 control-label"></label>
     <input style="background-color:#333;" type="datetime-local"  name="end_at" id="end_at" value="<?php echo date("Y-m-d H:i", strtotime('+1 day'));?>"  class="form-control"> 
     </div>
            

       <div id="end_at2" class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "DIA DE PAGO"; break;
 case 'EN': echo "PAYDAY"; break;
}
?></label>
      <select style="background-color:#333;" name="payment_day" id="payment_day"  class="form-control select2">
      <option value="1"><?php 
switch (Core::$user->language){
 case 'ES': echo "DIARIO"; break;
 case 'EN': echo "DIARY"; break;
}
?></option>
      <option value="7"><?php 
switch (Core::$user->language){
 case 'ES': echo "SEMANAL"; break;
 case 'EN': echo "WEEKLY"; break;
}
?></option>
      <option value="15"><?php 
switch (Core::$user->language){
 case 'ES': echo "QUINCENAL"; break;
 case 'EN': echo "FORTNIGHTLY"; break;
}
?></option>
      <option value="30"><?php 
switch (Core::$user->language){
 case 'ES': echo "MENSUAL"; break;
 case 'EN': echo "MONTHLY"; break;
}
?></option>
      </select>
      <select style="background-color:#333;" hidden name="selectdate" id="selectdate"  class="form-control"></select>
        </div>

  <div class="col-md-8 col-12"></div>
  
  
  <div class="col-md-6 col-12">
  <label class="col-md-12 col-12 control-label">
    <?php 
      switch (Core::$user->language){
        case 'ES': echo "Lugar a Entregar"; break;
        case 'EN': echo "Place to Deliver"; break;
      }
    ?>
  </label>

  <div class="input-group">
    <!-- SELECT2 visible al inicio -->
    <select id="place_start" class="form-control select2" name="place_start" style="background-color:#333;">
      <option value="" selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>
      <?php foreach(PlaceData::getAll() as $place): ?>
        <option value="<?php echo $place->name; ?>"><?php echo $place->name; ?></option>
      <?php endforeach; ?>
    </select>

    <!-- INPUT oculto al inicio -->
    <input type="text" id="place_start2" name="place_start2" class="form-control" placeholder="Escribe aquí" style="background-color:#333; display:none;">
  </div>

  <!-- BOTÓN DE CAMBIO -->
  <small id="toggleplace_start" style="color:orange; font-size:13px; margin-top:5px; display:block; cursor:pointer;">
    <?php 
switch (Core::$user->language){
 case 'ES': echo "CAMBIAR ENTRE ESCRIBIR Y SELECCIONAR"; break;
 case 'EN': echo "SWITCH BETWEEN WRITE AND SELECT"; break;
}
?>
  </small>
</div>

<script>
  $(document).ready(function () {
    let modoManual = false;

    $('#toggleplace_start').click(function () {
      if (!modoManual) {
        // Ocultar SELECT2 y mostrar INPUT
        $('#place_start').select2('destroy').hide();
        $('#place_start2').show();
        $('#place_start').val('');
        modoManual = true;
      } else {
        // Volver a mostrar SELECT2 y ocultar INPUT
        $('#place_start2').hide();
        $('#place_start2').val('');
        $('#place_start').show().select2();
        modoManual = false;
      }
    });
  });
</script>



    <div class="col-md-6 col-12">
  <label class="col-md-12 col-12 control-label">
    <?php 
      switch (Core::$user->language){
        case 'ES': echo "Lugar de Devolucion"; break;
        case 'EN': echo "Place to Receive"; break;
      }
    ?>
  </label>

  <div class="input-group">
    <!-- SELECT2 visible al inicio -->
    <select id="place_end" class="form-control select2" name="place_end" style="background-color:#333;">
      <option value="" selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>
      <?php foreach(PlaceData::getAll() as $place): ?>
        <option value="<?php echo $place->name; ?>"><?php echo $place->name; ?></option>
      <?php endforeach; ?>
    </select>

    <!-- INPUT oculto al inicio -->
    <input type="text" id="place_end2" name="place_end2" class="form-control" placeholder="Escribe aquí" style="background-color:#333; display:none;">
  </div>

  <!-- BOTÓN DE CAMBIO -->
  <small id="toggleplace_end" style="color:orange; font-size:13px; margin-top:5px; display:block; cursor:pointer;">
    <?php 
switch (Core::$user->language){
 case 'ES': echo "CAMBIAR ENTRE ESCRIBIR Y SELECCIONAR"; break;
 case 'EN': echo "SWITCH BETWEEN WRITE AND SELECT"; break;
}
?>
  </small>
</div>


<script>
  $(document).ready(function () {
    let modoManual2 = false;

    $('#toggleplace_end').click(function () {
      if (!modoManual2) {
        // Ocultar SELECT2 y mostrar INPUT
        $('#place_end').select2('destroy').hide();
        $('#place_end2').show();
        $('#place_end').val('');
        modoManual2 = true;
      } else {
        // Volver a mostrar SELECT2 y ocultar INPUT
        $('#place_end2').hide();
        $('#place_end2').val('');
        $('#place_end').show().select2();
        modoManual2 = false;
      }
    });
  });
</script>

      </div>
      </div>
      
<!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
      <div class="damage-pane fade" id="step3" role="tabpanel">
          <div class="row">
       
       <div class="col-md-2 col-12" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Modo"; break;
 case 'EN': echo "Mode"; break;
}
?></label>
    <select style="background-color: #333;" name="method" class="form-control" id="method" onchange="showMethod()">
      <option value="1"><?php 
switch (Core::$user->language){
 case 'ES': echo "Disponible"; break;
 case 'EN': echo "Available"; break;
}
?></option>
      <option value="2"><?php 
switch (Core::$user->language){
 case 'ES': echo "Rejuego"; break;
 case 'EN': echo "Replay"; break;
}
?></option>
      
      </select>
    </div>

    <div class="col-md-3 col-12" hidden>
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Ubicacion</label>
    
    <select style="background-color: #333;" name="location" class="form-control" id="location">
    <?php foreach(LocationData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<select hidden style="background-color: #333;" name="stock_id" id="select2lista"  class="form-control" onchange="showInp()"></select>
  
  
  <div class="col-md-3 col-12" id="stock_id2" style="display: none">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Suplidor"; break;
 case 'EN': echo "Supplier"; break;
}
?></label>
      <input style="background-color: #333;" type="text" name="stock_id2"  class="form-control" placeholder="Nombre del Rent A Car"> 
    </div>
  
  
  <div class="col-md-2 col-12" id="rpayment" style="display: none">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Precio/Rent A Car</label>
      <input style="background-color: #333;" type="number" value="0" name="rpayment"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" min="0" step="0.01"> 
    </div>
    
    
    <div class="col-md-5 col-12" id="cars1">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label" id="type_cars1"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></label>
    <label for="inputEmail1" class="col-md-12 col-12 control-label" id="type_cars3"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo (Rejuego)"; break;
 case 'EN': echo "Vehicle (Replay)"; break;
}
?></label>
    <select style="background-color: #333;" name="car_id"  id="cars" class="form-control select2"></select>
    </div>
  </div>
  
    <script>
        $(document).ready(function () {
            $('#cars').select2({
                templateResult: function (data) {
                    if (!data.id) {
                        return data.text;
                    }
                    
                    // Recuperar la descripción desde el atributo `data-description`
                    const description = $(data.element).data('description');

                    const $template = $(
                        `<div>
                            <strong>${data.text}</strong>
                            <div style="font-size: 12px; color: orange;">${description || ''}</div>
                        </div>`
                    );
                    return $template;
                },
                templateSelection: function (data) {
                    return data.text; // Mostrar solo el texto seleccionado
                }
            });
        });
    </script>
    
   <div class="col-md-5 col-12" id="cars3">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo (Solicitado)"; break;
 case 'EN': echo "Vehicle (Requested)"; break;
}
?></label>
    <select style="background-color: #333;" name="car2_id" id="cars2" class="form-control select2">
    <option value="0">--<?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?>--</option>
    <?php foreach(CarsData::getAllBySQL("where status<>4") as $cars): $provider = SuppliersData::getById($cars->provider_id);?>
      <option value="<?php echo $cars->id;?>" data-description="<?php echo strtoupper($cars->getStock()->name);?>"><?php echo $cars->getBrand()->name." ".$cars->name." ".$cars->year." F: [".$cars->token."].";?></option>
    <?php endforeach;?>
      </select>
    </div>
  </div>
 
 
 

    <script>
        $(document).ready(function () {
            $('#cars2').select2({
                templateResult: function (data) {
                    if (!data.id) {
                        return data.text;
                    }
                    
                    // Recuperar la descripción desde el atributo `data-description`
                    const description = $(data.element).data('description');

                    const $template = $(
                        `<div>
                            <strong>${data.text}</strong>
                            <div style="font-size: 12px; color: orange;">${description || ''}</div>
                        </div>`
                    );
                    return $template;
                },
                templateSelection: function (data) {
                    return data.text; // Mostrar solo el texto seleccionado
                }
            });
        });
    </script>
  
 
 
    <select hidden style="background-color: #333;" name="divisa_id" id="divisa_id" class="form-control">
    <option value="1">DOLAR</option>
    </select>
    
     <div class="col-md-4 col-12" id="cars2_brand">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Marca"; break;
 case 'EN': echo "Brand"; break;
}
?></label>
    <select style="background-color: #333;"  name="cars2_brand" class="form-control select2" >
      <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
    <?php foreach(BrandData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

     <div class="col-md-2 col-12" id="cars2_name">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Modelo"; break;
 case 'EN': echo "Model"; break;
}
?></label>
      <input style="background-color: #333;" type="text" name="cars2_name" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>

 
  <div class="col-md-3 col-12" id="cars2_category">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Categoria"; break;
 case 'EN': echo "Category"; break;
}
?></label>
    <select style="background-color: #333;" name="cars2_category" class="form-control select2"  >
      <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
    <?php foreach(CategoryData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

    <div class="col-md-1 col-12" id="cars2_year">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Año"; break;
 case 'EN': echo "Year"; break;
}
?> </label>
      <input style="background-color: #333;" type="text" value="<?php echo date("Y");?>" name="cars2_year" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>

<div class="col-md-2 col-12" id="cars2_plate">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Placa"; break;
 case 'EN': echo "Plate No."; break;
}
?></label>
      <input style="background-color: #333;" type="text"  name="cars2_plate" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>">
    </div>
  



<div id="extra" class="col-md-12 col-12"></div>

 <input style="background-color: #333;" type="hidden" id="unitx1" name="unit_extra1"   class="form-control"> 
  <input style="background-color: #333;" type="hidden" id="pricex1" name="price_extra1"  class="form-control"> 
  
  
 <input style="background-color: #333;" type="hidden" id="unitx2" name="unit_extra2"  class="form-control"> 
  <input style="background-color: #333;" type="hidden" id="pricex2" name="price_extra2"  class="form-control"> 
  
  
  
 <input style="background-color: #333;" type="hidden" id="unitx3" name="unit_extra3"  class="form-control"> 
  <input style="background-color: #333;" type="hidden" id="pricex3" name="price_extra3" class="form-control"> 
  
  
 <input style="background-color: #333;" type="hidden" id="unitx4" name="unit_extra4"  class="form-control"> 
  <input style="background-color: #333;" type="hidden" id="pricex4" name="price_extra4"  class="form-control"> 
  
      </div>
      </div>
      
<!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
      <div class="damage-pane fade" id="step4" role="tabpanel">
          <div class="row">
     
<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Forma de Pago"; break;
 case 'EN': echo "Method of payment"; break;
}
?></label>
    <select style="background-color: #333;" name="f_id" required class="form-control select2">
    <?php foreach(FData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

  
   <div class="col-md-3 col-12"> 
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Tipo de Seguro"; break;
 case 'EN': echo "Insurance Type"; break;
}
?></label>
    
      <select style="background-color: #333;" class="form-control" name="type_sure">
      <?php foreach (SureData::getALL() as $sure): ?>
      <option value="<?php echo $sure->id;?>"><?php echo $sure->name;?></option>
      <?php endforeach ?>
      </select>
</div>
   <div class="col-md-3 col-12">
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Deducible"; break;
 case 'EN': echo "Deductible"; break;
}
?></label>
    
          <input style="background-color: #333;" type="text" name="sure" class="form-control" value="0"   placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" autocomplete="off" >
</div>
 <div hidden class="col-md-4 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Deposito</label>
     <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
      <input style="background-color: #333;" type="text" name="deposit" value="0" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" >
    </div>
  </div>

   <div class="col-md-3 col-12">
    <div class="input-group" >
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Combustible"; break;
 case 'EN': echo "Fuel"; break;
}
?></label>

    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-tint"></i></span>
     <select style="background-color:#333;" name="fuel"  class="form-control">
      <option value="R"><?php 
      switch (Core::$user->language){
        case 'ES': echo "Reserva"; break;
        case 'EN': echo "Booking"; break;
      }
    ?></option>
      <option value="1/4">1/4</option>
      <option value="1/2"><?php 
      switch (Core::$user->language){
        case 'ES': echo "Medio"; break;
        case 'EN': echo "Half"; break;
      }
    ?></option>
      <option value="3/4">3/4</option>
      <option value="F">Full</option>
     </select>
    </div>
  </div>
  
  
        <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Dias de Renta"; break;
 case 'EN': echo "Income Days"; break;
}
?></label>
    <input style="background-color: #333;" id="dias" name="day"  class="form-control">
    </div>
</div>


  <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Precio por Dia"; break;
 case 'EN': echo "Price per day"; break;
}
?></label>
    <input style="background-color: #333;" type="number"  name="price2" id="tariff2"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>"  min="0" step="0.01">
    </div>
   
    </div>
    
    <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php echo StockData::getPrincipal()->imp_name; ?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-list-ol"></i></span>
     <select style="background-color: #333;" name="iva" id="iva"  class="form-control" onchange="showIva()">
         <option value="0"><?php 
switch (Core::$user->language){
 case 'ES': echo "NO"; break;
 case 'EN': echo "NOT"; break;
}
?></option>
         <option value="<?php echo StockData::getPrincipal()->imp_val;?>"><?php 
switch (Core::$user->language){
 case 'ES': echo "SI"; break;
 case 'EN': echo "YES"; break;
}
?></option>
     </select>
    </div>
  </div>


   <div class="col-md-3 col-12" id="type_iva">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Comprobante"; break;
 case 'EN': echo "Voucher"; break;
}
?></label>
    <select style="background-color: #333;" name="type_iva" class="form-control select2"  >
     <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> --</option>
     <?php foreach(CData::getAllBySQL("where de>0 and hasta>0") as $c):?>
      <option value="<?php echo $c->id."-".$c->serie."-".$c->de;?>"><?php echo $c->name;?></option>
    <?php endforeach;?>
      </select>
    </div>  
    
     


     <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Total Reserva"; break;
 case 'EN': echo "Total Reserve"; break;
}
?></label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <select style="background-color: #333;" name="total" id="amount"  class="form-control"></select>
    </div>
  </div>
  
   <div class="col-md-3 col-12" id="iva_value">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Valor"; break;
 case 'EN': echo "Worth"; break;
}
?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</label>
    <input style="background-color: #333;" id="value_iva" name="value_iva"  class="form-control" readonly>
    </div>   
  
   <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Extra</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <select style="background-color: #333;" name="xtotal" id="xmount"  class="form-control"></select>
    </div>
  </div>



    <div class="col-md-3 col-12">   
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Abono o Total"; break;
 case 'EN': echo "Subscription or Total"; break;
}
?></label>
   
      <input style="background-color: #333;" type="number" value="0"  name="payment" id="payment" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" min="0" step="0.01">
    </div>
  </div>

    <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Monto Restante"; break;
 case 'EN': echo "Remaining Amount"; break;
}
?></label>
    
     <input style="background-color: #333;" readonly id="remaining" name="remaining"  class="form-control">
    </div>
  </div>

 <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Otros Cobros"; break;
 case 'EN': echo "Other Charges"; break;
}
?></label>
     <input style="background-color: #333;" type="number" name="plane" value="0"  class="form-control" min="0" step="0.01">
    </div>
  </div>
  
  </div>
  
                     <input style="background-color: #333;" type="hidden" name="user_id" value="<?php echo $_GET['id'];?>">
                    
    
    
 <div hidden id="day"></div>
  

    </div>
    
    <!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
      <div class="damage-pane fade" id="step5" role="tabpanel">
          
          <div class="row">
         
           <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="cat" id="checkboxPrimary1" checked>
<label for="checkboxPrimary1">
<?php 
switch (Core::$user->language){
 case 'ES': echo "GATO"; break;
 case 'EN': echo "CAT"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="radio" id="checkboxPrimary2" checked>
                        <label for="checkboxPrimary2">
                          RADIO
                        </label>
                      </div>
    </div>

    <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox"  name="replacement" id="checkboxPrimary3" checked>
<label for="checkboxPrimary3">
<?php 
switch (Core::$user->language){
 case 'ES': echo "REPUESTO"; break;
 case 'EN': echo "REPLACEMENT"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="antenna" id="checkboxPrimary4" checked>
                        <label for="checkboxPrimary4">
                          <?php 
switch (Core::$user->language){
 case 'ES': echo "ANTENA"; break;
 case 'EN': echo "ANTENNA"; break;
}
?>
                        </label>
                      </div>
    </div>


    <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="keyring" id="checkboxPrimary5" checked>
<label for="checkboxPrimary5">
<?php 
switch (Core::$user->language){
 case 'ES': echo "LLAVERO"; break;
 case 'EN': echo "KEY RING"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="carpets" id="checkboxPrimary6" checked>
                        <label for="checkboxPrimary6">
                          <?php 
switch (Core::$user->language){
 case 'ES': echo "ALFOMBRAS"; break;
 case 'EN': echo "CARPETS"; break;
}
?>
                        </label>
                      </div>
    </div>

   <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="belts" id="checkboxPrimary7" checked>
<label for="checkboxPrimary7">
<?php 
switch (Core::$user->language){
 case 'ES': echo "CINTURONES"; break;
 case 'EN': echo "BELTS"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="roof_lining" id="checkboxPrimary8" checked>
                        <label for="checkboxPrimary8">
                         <?php 
switch (Core::$user->language){
 case 'ES': echo "FORRO TECHO"; break;
 case 'EN': echo "ROOF LINING"; break;
}
?>
                        </label>
                      </div>
    </div>    

   <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="mirrors" id="checkboxPrimary9" checked>
<label for="checkboxPrimary9">
<?php 
switch (Core::$user->language){
 case 'ES': echo "ESPEJOS"; break;
 case 'EN': echo "MIRRORS"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="board" id="checkboxPrimary10" checked>
                        <label for="checkboxPrimary10">
                         <?php 
switch (Core::$user->language){
 case 'ES': echo "TABLERO"; break;
 case 'EN': echo "BOARD"; break;
}
?>
                        </label>
                      </div>
    </div>  

       <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="document" id="checkboxPrimary11" checked>
<label for="checkboxPrimary11">
<?php 
switch (Core::$user->language){
 case 'ES': echo "DOCUMENTOS"; break;
 case 'EN': echo "DOCUMENTS"; break;
}
?>
 
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="watches" id="checkboxPrimary12" checked>
                        <label for="checkboxPrimary12">
                         <?php 
switch (Core::$user->language){
 case 'ES': echo "RELOJES"; break;
 case 'EN': echo "WATCHES"; break;
}
?>
 
                        </label>
                      </div>
    </div>  


       <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="rearview" id="checkboxPrimary13" checked>
<label for="checkboxPrimary13">
<?php 
switch (Core::$user->language){
 case 'ES': echo "RETREVISOR"; break;
 case 'EN': echo "REVIEWER"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="lighter" id="checkboxPrimary14" checked>
                        <label for="checkboxPrimary14">
                         <?php 
switch (Core::$user->language){
 case 'ES': echo "ENCENDEDOR"; break;
 case 'EN': echo "LIGHTER"; break;
}
?>
                        </label>
                      </div>
    </div>  

           <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="crystals" id="checkboxPrimary15" checked>
<label for="checkboxPrimary15">
<?php 
switch (Core::$user->language){
 case 'ES': echo "CRISTALES"; break;
 case 'EN': echo "CRYSTALS"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="cd" id="checkboxPrimary16" checked>
                        <label for="checkboxPrimary16">
                        <?php 
switch (Core::$user->language){
 case 'ES': echo "PORTA CD"; break;
 case 'EN': echo "CD HOLDER"; break;
}
?>
                        </label>
                      </div>
    </div>  


           <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="bumper" id="checkboxPrimary17" checked>
<label for="checkboxPrimary17">
<?php 
switch (Core::$user->language){
 case 'ES': echo "TAPA COV. BUMPER"; break;
 case 'EN': echo "VOC COVER BUMPER"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="equalizer" id="checkboxPrimary18" checked>
                        <label for="checkboxPrimary18">
                         <?php 
switch (Core::$user->language){
 case 'ES': echo "ECUALIZADOR"; break;
 case 'EN': echo "EQUALIZER"; break;
}
?>
                        </label>
                      </div>
    </div>  

               <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="cup_holder" id="checkboxPrimary19" checked>
<label for="checkboxPrimary19">
<?php 
switch (Core::$user->language){
 case 'ES': echo "PORTA VASOS"; break;
 case 'EN': echo "CUP HOLDER"; break;
}
?>
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="plate" id="checkboxPrimary20" checked>
                        <label for="checkboxPrimary20">
                         <?php 
switch (Core::$user->language){
 case 'ES': echo "PLACA"; break;
 case 'EN': echo "PLATE"; break;
}
?>
                        </label>
                      </div>
    </div>  

 

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="seats" id="checkboxPrimary21" checked>
                        <label for="checkboxPrimary21">
                         <?php 
switch (Core::$user->language){
 case 'ES': echo "ASIENTOS"; break;
 case 'EN': echo "SEATING"; break;
}
?>
                        </label>
                      </div>
    </div>  

               <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="logo" id="checkboxPrimary22" checked>
<label for="checkboxPrimary22">
LOGOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="batery" id="checkboxPrimary23" checked>
                        <label for="checkboxPrimary23">
                        <?php 
switch (Core::$user->language){
 case 'ES': echo "BATERIA"; break;
 case 'EN': echo "BATTERY"; break;
}
?>
                        </label>
                      </div>
    </div> 



    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="top" id="checkboxPrimary24" checked>
                        <label for="checkboxPrimary24">
<?php 
switch (Core::$user->language){
 case 'ES': echo "TAPA COMBUSTIBLE"; break;
 case 'EN': echo "FUEL CAP"; break;
}
?>
                   
                        </label>
                      </div>
    </div> 
         
              
          </div>
         
    </div>
    
     <!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
      <div class="damage-pane fade" id="step6" role="tabpanel">

 <div class="card card card-outline"  style="background-color:#222;">
         
          <div class="card-body">
              
         <div class="nav-wrapper">
  <ul class="nav nav-tabs d-flex flex-nowrap" id="custom-content-above-tab" role="tablist">
      
    <li class="nav-item">
      <a class="nav-link active" id="vert-tabs-frontal-tab" data-toggle="pill" href="#vert-tabs-frontal" role="tab" aria-controls="vert-tabs-frontal" aria-selected="true">FRONTAL</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-lateral-izquierdo-tab" data-toggle="pill" href="#vert-tabs-lateral-izquierdo" role="tab" aria-controls="vert-tabs-lateral-izquierdo" aria-selected="false"><?php 
switch (Core::$user->language){
 case 'ES': echo "LATERAL IZQUIERDO"; break;
 case 'EN': echo "LEFT SIDE"; break;
}
?></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-lateral-derecho-tab" data-toggle="pill" href="#vert-tabs-lateral-derecho" role="tab" aria-controls="vert-tabs-lateral-derecho" aria-selected="false"><?php 
switch (Core::$user->language){
 case 'ES': echo "LATERAL DERECHO"; break;
 case 'EN': echo "RIGHT BACK"; break;
}
?></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-trasera-tab" data-toggle="pill" href="#vert-tabs-trasera" role="tab" aria-controls="vert-tabs-trasera" aria-selected="false"><?php 
switch (Core::$user->language){
 case 'ES': echo "TRASERA"; break;
 case 'EN': echo "REAR"; break;
}
?></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-superior-tab" data-toggle="pill" href="#vert-tabs-superior" role="tab" aria-controls="vert-tabs-superior" aria-selected="false">SUPERIOR</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-inferior-tab" data-toggle="pill" href="#vert-tabs-inferior" role="tab" aria-controls="vert-tabs-inferior" aria-selected="false"><?php 
switch (Core::$user->language){
 case 'ES': echo "INFERIOR"; break;
 case 'EN': echo "LOWER"; break;
}
?></a>
    </li>
  </ul>
</div>

<style>
  .nav-wrapper {
    overflow-x: auto;  /* Permite el scroll horizontal */
    overflow-y: hidden; /* Evita el scroll vertical */
    white-space: nowrap;
  }
  .nav-tabs {
    flex-wrap: nowrap;
  }

    
#vert-tabs-frontal-tab.nav-link{
    color: orange !important;
}
#vert-tabs-lateral-izquierdo-tab.nav-link{
    color: orange !important;
}
#vert-tabs-lateral-derecho-tab.nav-link{
    color: orange !important;
}
#vert-tabs-trasera-tab.nav-link{
    color: orange !important;
}
#vert-tabs-superior-tab.nav-link{
    color: orange !important;
}
#vert-tabs-inferior-tab.nav-link{
    color: orange !important;
}
</style>




<div class="row">
          
              <div class="col-12 col-md-12">
                <div class="tab-content" id="vert-tabs-tabContent">
                  
                  <div class="tab-pane text-left fade show active" id="vert-tabs-frontal" role="tabpanel" aria-labelledby="vert-tabs-frontal-tab">
                     
            <!-- TO DO List -->
            <div  style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck1">
                      <label for="todoCheck1"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text1"><?php 
switch (Core::$user->language){
 case 'ES': echo "Capó"; break;
 case 'EN': echo "Hood"; break;
}
?></span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image1" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image1" type="file" style="display: none;" accept="image/*"  name="image1">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck1').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image1').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck1').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text1').style.textDecoration = 'line-through';
    }
  });
</script>
                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck2">
                      <label for="todoCheck2"></label>
                    </div>
                     <span class="text" id="text2"><?php 
switch (Core::$user->language){
 case 'ES': echo "Parachoques"; break;
 case 'EN': echo "Bumper"; break;
}
?></span>
                    <div class="tools">
                      <label for="image2" class="custom-file-upload"><i class="fa fa-upload"></i></label> 
                      <input id="image2" type="file" style="display: none;" accept="image/*"  name="image2">
                    </div>
                  </li>
                 
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck2').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image2').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck2').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text2').style.textDecoration = 'line-through';
    }
  });
</script>
                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck3">
                      <label for="todoCheck3"></label>
                    </div>
                     <span class="text" id="text3"><?php 
switch (Core::$user->language){
 case 'ES': echo "Faros"; break;
 case 'EN': echo "Headlights"; break;
}
?></span>
                    <div class="tools">
                     <label for="image3" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image3" type="file" style="display: none;" accept="image/*"  name="image3">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck3').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image3').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck3').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text3').style.textDecoration = 'line-through';
    }
  });
</script>
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck4">
                      <label for="todoCheck4"></label>
                    </div>
                     <span class="text" id="text4"><?php 
switch (Core::$user->language){
 case 'ES': echo "Parrilla"; break;
 case 'EN': echo "Grill"; break;
}
?></span>
                    <div class="tools">
                     <label for="image4" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image4" type="file" style="display: none;" accept="image/*"  name="image4">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck4').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image4').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck4').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text4').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck5">
                      <label for="todoCheck5"></label>
                    </div>
                     <span class="text" id="text5"><?php 
switch (Core::$user->language){
 case 'ES': echo "Parabrisas"; break;
 case 'EN': echo "Windshield"; break;
}
?></span>
                    <div class="tools">
                     <label for="image5" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image5" type="file" style="display: none;" accept="image/*"  name="image5">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck3').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image3').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck3').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text3').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck6">
                      <label for="todoCheck6"></label>
                    </div>
                     <span class="text" id="text6">Forlay</span>
                    <div class="tools">
                     <label for="image6" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image6" type="file" style="display: none;" accept="image/*"  name="image6">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck6').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image6').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck6').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text6').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                 <input style="background-color:#222;" autocomplete="off" name="comment1"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-lateral-izquierdo" role="tabpanel" aria-labelledby="vert-tabs-lateral-izquierdo-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
            
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck7">
                      <label for="todoCheck7"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text7"><?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?></span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image7" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image7" type="file" style="display: none;" accept="image/*"  name="image7">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck7').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image7').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck7').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text7').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck8">
                      <label for="todoCheck8"></label>
                    </div>
                     <span class="text" id="text8"><?php 
switch (Core::$user->language){
 case 'ES': echo "Guardafangos"; break;
 case 'EN': echo "Fenders"; break;
}
?></span>
                    <div class="tools">
                     <label for="image8" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image8" type="file" style="display: none;" accept="image/*"  name="image8">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck8').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image8').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck8').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text8').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck9">
                      <label for="todoCheck9"></label>
                    </div>
                     <span class="text" id="text9"><?php 
switch (Core::$user->language){
 case 'ES': echo "Espejos retrovisores"; break;
 case 'EN': echo "Rear view mirrors"; break;
}
?></span>
                    <div class="tools">
                     <label for="image9" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image9" type="file" style="display: none;" accept="image/*"  name="image9">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck9').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image9').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck9').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text9').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck10">
                      <label for="todoCheck10"></label>
                    </div>
                     <span class="text" id="text10"><?php 
switch (Core::$user->language){
 case 'ES': echo "Ventanas laterales"; break;
 case 'EN': echo "Side windows"; break;
}
?></span>
                    <div class="tools">
                     <label for="image10" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image10" type="file" style="display: none;" accept="image/*"  name="image10">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck10').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image10').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck10').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text10').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck11">
                      <label for="todoCheck11"></label>
                    </div>
                     <span class="text" id="text11"><?php 
switch (Core::$user->language){
 case 'ES': echo "Manijas de las puertas"; break;
 case 'EN': echo "Door handles"; break;
}
?></span>
                    <div class="tools">
                     <label for="image11" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image11" type="file" style="display: none;" accept="image/*"  name="image11">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck11').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image11').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck11').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text11').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck12">
                      <label for="todoCheck12"></label>
                    </div>
                     <span class="text" id="text12"><?php 
switch (Core::$user->language){
 case 'ES': echo "Llantas y rines"; break;
 case 'EN': echo "Tires and rims"; break;
}
?></span>
                    <div class="tools">
                     <label for="image12" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image12" type="file" style="display: none;" accept="image/*"  name="image12">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck12').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image12').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck12').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text12').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment2"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-lateral-derecho" role="tabpanel" aria-labelledby="vert-tabs-lateral-derecho-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
              
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
             
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck13">
                      <label for="todoCheck13"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text13"><?php 
switch (Core::$user->language){
 case 'ES': echo "Puertas"; break;
 case 'EN': echo "Doors"; break;
}
?></span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image13" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image13" type="file" style="display: none;" accept="image/*"  name="image13">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck13').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image13').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck13').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text13').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck14">
                      <label for="todoCheck14"></label>
                    </div>
                     <span class="text" id="text14"><?php 
switch (Core::$user->language){
 case 'ES': echo "Guardafangos"; break;
 case 'EN': echo "Fenders"; break;
}
?> </span>
                    <div class="tools">
                   <label for="image14" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                   <input id="image14" type="file" style="display: none;" accept="image/*"  name="image14">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck14').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image14').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck14').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text14').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck15">
                      <label for="todoCheck15"></label>
                    </div>
                     <span class="text" id="text15"><?php 
switch (Core::$user->language){
 case 'ES': echo "Espejos retrovisores"; break;
 case 'EN': echo "Rear view mirrors"; break;
}
?> </span>
                    <div class="tools">
                     <label for="image15" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image15" type="file" style="display: none;" accept="image/*"  name="image15">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck15').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image15').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck15').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text15').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck16">
                      <label for="todoCheck16"></label>
                    </div>
                     <span class="text" id="text16"><?php 
switch (Core::$user->language){
 case 'ES': echo "Ventanas laterales"; break;
 case 'EN': echo "Side windows"; break;
}
?></span>
                    <div class="tools">
                    <label for="image16" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                    <input id="image16" type="file" style="display: none;" accept="image/*"  name="image16">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck16').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image16').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck16').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text16').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck17">
                      <label for="todoCheck17"></label>
                    </div>
                     <span class="text" id="text17"><?php 
switch (Core::$user->language){
 case 'ES': echo "Manijas de las puertas"; break;
 case 'EN': echo "Door handles"; break;
}
?></span>
                    <div class="tools">
                     <label for="image17" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image17" type="file" style="display: none;" accept="image/*"  name="image17">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck17').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image17').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck17').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text17').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck18">
                      <label for="todoCheck18"></label>
                    </div>
                     <span class="text" id="text18"><?php 
switch (Core::$user->language){
 case 'ES': echo "Llantas y rines"; break;
 case 'EN': echo "Tires and rims"; break;
}
?></span>
                    <div class="tools">
                    <label for="image18" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image18" type="file" style="display: none;" accept="image/*"  name="image18">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck18').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image18').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck18').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text18').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment3"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>     
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-trasera" role="tabpanel" aria-labelledby="vert-tabs-trasera-tab">
                        
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
              
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck19">
                      <label for="todoCheck19"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text19"><?php 
switch (Core::$user->language){
 case 'ES': echo "Parachoques"; break;
 case 'EN': echo "Bumper"; break;
}
?></span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                    <label for="image19" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image19" type="file" style="display: none;" accept="image/*"  name="image19">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck19').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image19').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck19').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text19').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck20">
                      <label for="todoCheck20"></label>
                    </div>
                     <span class="text" id="text20"><?php 
switch (Core::$user->language){
 case 'ES': echo "Compuerta"; break;
 case 'EN': echo "Gate"; break;
}
?></span>
                    <div class="tools">
                     <label for="image20" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image20" type="file" style="display: none;" accept="image/*"  name="image20">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck20').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image20').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck20').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text20').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck21">
                      <label for="todoCheck21"></label>
                    </div>
                     <span class="text" id="text21"><?php 
switch (Core::$user->language){
 case 'ES': echo "Faros"; break;
 case 'EN': echo "Headlights"; break;
}
?></span>
                    <div class="tools">
                     <label for="image21" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image21" type="file" style="display: none;" accept="image/*"  name="image21">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck21').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image21').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck21').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text21').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck22">
                      <label for="todoCheck22"></label>
                    </div>
                     <span class="text" id="text22"><?php 
switch (Core::$user->language){
 case 'ES': echo "Escape"; break;
 case 'EN': echo "Exhaust"; break;
}
?></span>
                    <div class="tools">
                    <label for="image22" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                    <input id="image22" type="file" style="display: none;" accept="image/*"  name="image22">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck22').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image22').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck22').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text22').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck23">
                      <label for="todoCheck23"></label>
                    </div>
                     <span class="text" id="text23"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vidrio trasero"; break;
 case 'EN': echo "Rear glass"; break;
}
?></span>
                    <div class="tools">
                   <label for="image23" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                   <input id="image23" type="file" style="display: none;" accept="image/*"  name="image23">
                    </div>
                  </li>                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck23').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image23').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck23').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text23').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment4"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                    <div class="tab-pane fade" id="vert-tabs-superior" role="tabpanel" aria-labelledby="vert-tabs-superior-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck24">
                      <label for="todoCheck24"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text24"><?php 
switch (Core::$user->language){
 case 'ES': echo "Techo"; break;
 case 'EN': echo "Ceiling"; break;
}
?></span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                    <label for="image24" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image24" type="file" style="display: none;" accept="image/*"  name="image24">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck24').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image24').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck24').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text24').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck25">
                      <label for="todoCheck25"></label>
                    </div>
                     <span class="text" id="text25"><?php 
switch (Core::$user->language){
 case 'ES': echo "Antena"; break;
 case 'EN': echo "Antenna"; break;
}
?></span>
                    <div class="tools">
                   <label for="image25" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                   <input id="image25" type="file" style="display: none;" accept="image/*"  name="image25">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck25').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image25').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck25').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text25').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment5"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->      
                    <div class="tab-pane fade" id="vert-tabs-inferior" role="tabpanel" aria-labelledby="vert-tabs-inferior-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck26">
                      <label for="todoCheck26"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text26"><?php 
switch (Core::$user->language){
 case 'ES': echo "Chasis"; break;
 case 'EN': echo "Chassis"; break;
}
?></span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                  <label for="image26" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                  <input id="image26" type="file" style="display: none;" accept="image/*"  name="image26">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck26').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image26').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck26').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text26').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck27">
                      <label for="todoCheck27"></label>
                    </div>
                     <span class="text" id="text27"><?php 
switch (Core::$user->language){
 case 'ES': echo "Suspensión"; break;
 case 'EN': echo "Suspension"; break;
}
?></span>
                    <div class="tools">
                    <label for="image27" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image27" type="file" style="display: none;" accept="image/*"  name="image27">
                    </div>
                  </li>                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck27').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image27').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck27').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text27').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck28">
                      <label for="todoCheck28"></label>
                    </div>
                     <span class="text" id="text28"><?php 
switch (Core::$user->language){
 case 'ES': echo "Amortiguador"; break;
 case 'EN': echo "Shock absorber"; break;
}
?></span>
                    <div class="tools">
                    <label for="image28" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image28" type="file" style="display: none;" accept="image/*"  name="image28">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck28').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image28').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck28').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text28').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                  <div class="icheck-primary d-inline ml-2">
                  <input type="checkbox" id="todoCheck29"> <!-- Checkbox habilitado para envío -->
                  <label for="todoCheck29"></label>
                  </div>
                  <span class="text" id="text29"><?php 
switch (Core::$user->language){
 case 'ES': echo "Otros"; break;
 case 'EN': echo "Others"; break;
}
?></span>
                  <div class="tools">
                  <label for="image29" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                  <input id="image29" type="file" style="display: none;" accept="image/*"  name="image29">
                  </div>
                  </li>

<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck29').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image29').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck29').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text29').style.textDecoration = 'line-through';
    }
  });
</script>
                  
                  
                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment6"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>



                </div>
              </div>

                </div>

    </div>
    </div>
    
    </div>
     <!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.  -->
    <div class="damage-pane fade" id="step7" role="tabpanel">
      <div class="contenedor">

    <div class="row">
      <div class="col-md-12">
        <canvas id="draw-canvas" width="340" height="200">
          No tienes un buen navegador.
        </canvas>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
          
       
        
        <input  type="button" class="button btn-danger" id="draw-clearBtn" value="<?php 
switch (Core::$user->language){
 case 'ES': echo "Borrar Firma"; break;
 case 'EN': echo "Delete Signature"; break;
}
?>"></input>
     


            <label>Color</label>
            <input style="background-color:#333;" type="color" id="color">
            <input style="background-color:#333;" hidden type="range" id="puntero" min="1" default="1" max="5" width="10%">


      </div>

    </div>

  
    <div hidden class="row">
      <div class="col-md-12">
        <textarea style="background-color:#333;"  id="draw-dataUrl" class="form-control" name="base64" rows="5"></textarea>
      </div>
    </div>
  
  
  </div>
   
                     <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $_GET['id'];?>">
                   
                <div class="col-md-12 col-12 my-2">

                   <button type="submit" id="draw-submitBtn"  class="btn btn-success btn-block btn-sm "><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Entregar"; break;
 case 'EN': echo "Deliver"; break;
}
?></button>
                   
                
                </div>
  
       

  </div>

    <!-- Botones -->
    <div class="d-flex justify-content-between mt-3">
      <div class="btn btn-secondary" id="prevBtn">Regresar</div>
      <div class="btn btn-warning" id="nextBtn">Siguiente</div>
    </div>
 
</div>
</div>
 </form> 
<script>
  const $tabs = $('#stepTabs .nav-link');
  const $panes = $('.damage-pane');
  let current = 0;

  function updateStep() {
    $tabs.removeClass('active').eq(current).addClass('active');
    $panes.removeClass('show active').eq(current).addClass('show active');

    $('#prevBtn').toggle(current > 0);
    $('#nextBtn').toggle(current < $tabs.length - 1);
    
    
      // 👇 Activar tab "FRONTAL" automáticamente si estás en "Marcar Daño" (índice 5 = #step6)
  if ($tabs.eq(current).attr('href') === '#step6') {
    $('#custom-content-above-tab .nav-link').removeClass('active');
    $('#vert-tabs-tabContent .damage-pane').removeClass('show active');
    $('#vert-tabs-frontal-tab').addClass('active');
    $('#vert-tabs-frontal').addClass('show active');
  }
  
  }

  $('#nextBtn').click(function () {
    if (current < $tabs.length - 1) {
      current++;
      updateStep();
    }
  });

  $('#prevBtn').click(function () {
    if (current > 0) {
      current--;
      updateStep();
    }
  });

  // Inicialización
  updateStep();
  
 
    document.getElementById("cars3").style.display = "none";
    document.getElementById("type_cars1").style.display = "inline-block";
    document.getElementById("type_cars3").style.display = "none";
   
    document.getElementById("type_iva").style.display = "none";
    document.getElementById("iva_value").style.display = "none"; 
 
 function showIva(){
  var getSelectValue = document.getElementById("iva").value;

  if(getSelectValue==0){
      
    document.getElementById("type_iva").style.display = "none";
    document.getElementById("iva_value").style.display = "none";
     
    function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
    
   
}

$("#remaining").val(agregarSeparadorMiles(parseFloat($("#amount").val())-parseFloat($("#payment").val())));
    
  }else if(getSelectValue==<?php echo StockData::getPrincipal()->imp_val;?>){

    document.getElementById("type_iva").style.display = "inline-block"; 
    document.getElementById("iva_value").style.display = "inline-block"; 
    function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
    
   
}

$("#remaining").val(agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#amount").val()*0.<?php echo StockData::getPrincipal()->imp_val;?>))-parseFloat($("#payment").val())));

$("#value_iva").val(agregarSeparadorMiles(+parseFloat($("#amount").val()*0.<?php echo StockData::getPrincipal()->imp_val;?>)));

}

 }
   
    document.getElementById("cars2_name").style.display = "none";
    document.getElementById("cars2_plate").style.display = "none";
    document.getElementById("cars2_category").style.display = "none";
    document.getElementById("cars2_brand").style.display = "none";
    document.getElementById("cars2_year").style.display = "none"; 
    
  $(document).ready(function(){
    $('#xmount').val();
    recargarxLista();

  })
  
  function recargarxLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
       data: {unit1: $('#unit1').val(), price1:  $('#price1').val(), uni2: $('#unit2').val(), price2:  $('#price2').val(), unit3: $('#unit3').val(), price3:  $('#price3').val(), unit4: $('#unit4').val(), price4:  $('#price4').val()},
      success:function(r){
        $('#xmount').html(r);
      }
    });
  }




 $(document).ready(function(){
    $('#location').val();
    recargarLista();

    $('#location').change(function(){
      recargarLista();
    });
  })

  function recargarLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=all",
      data:"location=" + $('#location').val(),
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }
  

  $(document).ready(function(){
    $('#select2lista').val();
    recargar2Lista();
  })
  
   $('#cars').change(function(){
 
  recargarExtras();
  
$("#unitx1").val(0);
$("#pricex1").val(0);

$("#unitx2").val(0);
$("#pricex2").val(0);

$("#unitx3").val(0);
$("#pricex3").val(0);

$("#unitx4").val(0);
$("#pricex4").val(0);


$("#xmount").val(0);

   function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
    
   
}

$("#remaining").val(agregarSeparadorMiles(parseFloat($("#amount").val())-parseFloat($("#payment").val())));

    });

  
  function recargarExtras(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=extra",
      data: {car_id: $('#cars').val()},
      success:function(r){
        $('#extra').html(r);
      }
    });
  }
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
     data: {stock_id: <?php echo StockData::getPrincipal()->id;?>,start_at: $('#start_at').val(),end_at: $('#end_at').val()},
      success:function(r){
        $('#cars').html(r);
      }
    });
    
     $('.warning').hide();
       $('#draw-submitBtn').prop('disabled', true);
    
    function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni),
      hasta = new Date(fechaFin),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return  Math.round((diff / diaEnMils));
}


 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val())+parseFloat($("#xmount").val())));
    
  
  
function showMethod(){
    
  var getSelectValue = document.getElementById("method").value;
  var getSelectValue2 = document.getElementById("select2lista").value;

  if(getSelectValue==1){

  recargarLista();
  recargarExtras();
  document.getElementById("extra").style.display = "none";

  function recargarLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=all",
      data:"location=" + getSelectValue,
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }
  
  recargar2Lista();
    
  function recargar2Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
      data:"stock_id=" + getSelectValue2,
      success:function(r){
        $('#cars').html(r);
      }
    });
  }

    
    document.getElementById("rpayment").style.display = "none";
    document.getElementById("stock_id2").style.display = "none"; 
    
    document.getElementById("cars2_name").style.display = "none";
    document.getElementById("cars2_plate").style.display = "none";
    document.getElementById("cars2_category").style.display = "none";
    document.getElementById("cars2_brand").style.display = "none";
    document.getElementById("cars2_year").style.display = "none";
    document.getElementById("cars1").style.display = "inline-block";
    document.getElementById("cars3").style.display = "none";
    document.getElementById("type_cars1").style.display = "inline-block";
    document.getElementById("type_cars3").style.display = "none";
    
  }else if(getSelectValue==2){
      
      
  recargarExtras();
      
  
    document.getElementById("rpayment").style.display = "none";
    document.getElementById("stock_id2").style.display = "none";
    
    document.getElementById("cars1").style.display = "inline-block";
    document.getElementById("cars3").style.display = "inline-block";
    document.getElementById("type_cars1").style.display = "none";
    document.getElementById("type_cars3").style.display = "inline-block";
    document.getElementById("cars2_name").style.display = "none";
    document.getElementById("cars2_plate").style.display = "none";
    document.getElementById("cars2_category").style.display = "none";
    document.getElementById("cars2_brand").style.display = "none";
    document.getElementById("cars2_year").style.display = "none";
  }else if(getSelectValue==3){
      

  recargarLista();
  recargarExtras();
  document.getElementById("extra").style.display = "none";
    

  function recargarLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=all",
      data:"location=" + getSelectValue,
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }
  
 
      recargar2Lista();
    
  function recargar2Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
      data:"stock_id=" + getSelectValue2,
      success:function(r){
        $('#cars').html(r);
      }
    });
  }
  

      
    document.getElementById("stock_id2").style.display = "inline-block";
    
    document.getElementById("cars1").style.display = "none";
    document.getElementById("cars3").style.display = "none";
    document.getElementById("type_cars1").style.display = "none";
    document.getElementById("type_cars3").style.display = "none";
    document.getElementById("cars2_name").style.display =  "inline-block";
    document.getElementById("cars2_plate").style.display =  "inline-block";
    document.getElementById("cars2_category").style.display =  "inline-block";
    document.getElementById("cars2_brand").style.display =  "inline-block";
    document.getElementById("cars2_year").style.display =  "inline-block";
    
   document.getElementById("rpayment").style.display = "inline-block";  
  }
 
}

    $('#cars').change(function(){
      recargar3Lista();
      
  document.getElementById("extra").style.display = "inline-block";
    });
 

  function recargar3Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
      data:"cars=" + $('#cars').val(),
      success:function(r){
        $('#tariff').html(r);
      }
    });
  }

    $('#tariff').change(function(){
      Lista();

    });

$("#payment").keyup(function(){
function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
 });

function Lista(){
    

  var getSelectValue = document.getElementById("type_id").value;

  if(getSelectValue=="1"){
    
function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
}

   
 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=amount",
       data: {paycar: $('#payment').val(),tcars: $('#tariff').val(),dvs: $('#divisa_id').val(),cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price",
       data: {cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });
$("#remaining").val(($('#tariff').val()*$('#dias').val())*$('#divisa_id').val());

}

 if(getSelectValue=="2"){
    
function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
}

   
 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=free",
      data:"freedate=" + $('#payment_day').val(),
      success:function(r){
        $('#selectdate').html(r);
      }
    });
  
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=amount",
       data: {paycar: $('#payment').val(),tcars: $('#tariff').val(),dvs: $('#divisa_id').val(),cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price",
       data: {cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });
$("#remaining").val(($('#tariff').val()*$('#dias').val())*$('#divisa_id').val());

}
}



    tariff2.addEventListener("keyup", function()
    {   


  var getSelectValue = document.getElementById("type_id").value;

  if(getSelectValue=="1"){
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff2",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles((($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val())+parseFloat($("#xmount").val())));
    
        
    }
    
    
    if(getSelectValue=="2"){

    function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
}

   
 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=free",
      data:"freedate=" + $('#payment_day').val(),
      success:function(r){
        $('#selectdate').html(r);
      }
    });
  


    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff2",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val( agregarSeparadorMiles(($('#tariff2').val()*$('#dias').val())*$('#divisa_id').val()));
    
        
    }
    }, false);
 


   
$('#cars').on('change', () => {
    var value = $('#cars').val();
    
    if(value) {
       $('.warning').hide();
       $('#draw-submitBtn').prop('disabled', false);
    }
    

    
});

</script>

 <div hidden id="day"></div>
   
           
<style>

section{
    flex:1;
}



.button {
    background: #3071a9;
    box-shadow: inset 0 -3px 0 rgba(0,0,0,.3);
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 5px;
    margin: 0 15px;
    text-decoration: none;
    color: white;
}

.button:active {
    transform: scale(0.9);
}

.contenedor {
    width: 100%
    margin: 5px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

input[type=range] {
  -webkit-appearance: none;
  margin: 18px 0;

}
input[type=range]:focus {
  outline: none;
}
input[type=range]::-webkit-slider-runnable-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-webkit-slider-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
  -webkit-appearance: none;
  margin-top: -14px;
}
input[type=range]:focus::-webkit-slider-runnable-track {
  background: #367ebd;
}
input[type=range]::-moz-range-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-moz-range-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]::-ms-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  background: transparent;
  border-color: transparent;
  border-width: 16px 0;
  color: transparent;
}
input[type=range]::-ms-fill-lower {
  background: #2a6495;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-fill-upper {
  background: #3071a9;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]:focus::-ms-fill-lower {
  background: #3071a9;
}
input[type=range]:focus::-ms-fill-upper {
  background: #367ebd;
}
</style>
<script>
/*
    El siguiente codigo en JS Contiene mucho codigo
    de las siguietes 3 fuentes:
    https://stipaltamar.github.io/dibujoCanvas/
    https://developer.mozilla.org/samples/domref/touchevents.html - https://developer.mozilla.org/es/docs/DOM/Touch_events
    http://bencentra.com/canvas/signature/signature.html - https://bencentra.com/code/2014/12/05/html5-canvas-touch-events.html
*/

(function() { // Comenzamos una funcion auto-ejecutable

  // Obtenenemos un intervalo regular(Tiempo) en la pamtalla
  window.requestAnimFrame = (function (callback) {
    return window.requestAnimationFrame ||
          window.webkitRequestAnimationFrame ||
          window.mozRequestAnimationFrame ||
          window.oRequestAnimationFrame ||
          window.msRequestAnimaitonFrame ||
          function (callback) {
            window.setTimeout(callback, 1000/60);
            // Retrasa la ejecucion de la funcion para mejorar la experiencia
          };
  })();

  // Traemos el canvas mediante el id del elemento html
  var canvas = document.getElementById("draw-canvas");
  var ctx = canvas.getContext("2d");


  // Mandamos llamar a los Elemetos interactivos de la Interfaz HTML
  var drawText = document.getElementById("draw-dataUrl");
  var drawImage = document.getElementById("draw-image");
  var clearBtn = document.getElementById("draw-clearBtn");
  var submitBtn = document.getElementById("draw-submitBtn");
  clearBtn.addEventListener("click", function (e) {
    // Definimos que pasa cuando el boton draw-clearBtn es pulsado
    clearCanvas();
    drawImage.setAttribute("src", "");
  }, false);
    // Definimos que pasa cuando el boton draw-submitBtn es pulsado
  submitBtn.addEventListener("click", function (e) {
  var dataUrl = canvas.toDataURL();
  drawText.innerHTML = dataUrl;
  drawImage.setAttribute("src", dataUrl);
   }, false);

  // Activamos MouseEvent para nuestra pagina
  var drawing = false;
  var mousePos = { x:0, y:0 };
  var lastPos = mousePos;
  canvas.addEventListener("mousedown", function (e)
  {
    /*
      Mas alla de solo llamar a una funcion, usamos function (e){...}
      para mas versatilidad cuando ocurre un evento
    */
    var tint = document.getElementById("color");
    var punta = document.getElementById("puntero");
    console.log(e);
    drawing = true;
    lastPos = getMousePos(canvas, e);
  }, false);
  canvas.addEventListener("mouseup", function (e)
  {
    drawing = false;
  }, false);
  canvas.addEventListener("mousemove", function (e)
  {
    mousePos = getMousePos(canvas, e);
  }, false);

  // Activamos touchEvent para nuestra pagina
  canvas.addEventListener("touchstart", function (e) {
    mousePos = getTouchPos(canvas, e);
    console.log(mousePos);
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var touch = e.touches[0];
    var mouseEvent = new MouseEvent("mousedown", {
      clientX: touch.clientX,
      clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchend", function (e) {
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var mouseEvent = new MouseEvent("mouseup", {});
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchleave", function (e) {
    // Realiza el mismo proceso que touchend en caso de que el dedo se deslice fuera del canvas
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var mouseEvent = new MouseEvent("mouseup", {});
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchmove", function (e) {
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var touch = e.touches[0];
    var mouseEvent = new MouseEvent("mousemove", {
      clientX: touch.clientX,
      clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
  }, false);

  // Get the position of the mouse relative to the canvas
  function getMousePos(canvasDom, mouseEvent) {
    var rect = canvasDom.getBoundingClientRect();
    /*
      Devuelve el tamaño de un elemento y su posición relativa respecto
      a la ventana de visualización (viewport).
    */
    return {
      x: mouseEvent.clientX - rect.left,
      y: mouseEvent.clientY - rect.top
    };
  }

  // Get the position of a touch relative to the canvas
  function getTouchPos(canvasDom, touchEvent) {
    var rect = canvasDom.getBoundingClientRect();
    console.log(touchEvent);
    /*
      Devuelve el tamaño de un elemento y su posición relativa respecto
      a la ventana de visualización (viewport).
    */
    return {
      x: touchEvent.touches[0].clientX - rect.left, // Popiedad de todo evento Touch
      y: touchEvent.touches[0].clientY - rect.top
    };
  }

  // Draw to the canvas
  function renderCanvas() {
    if (drawing) {
      var tint = document.getElementById("color");
      var punta = document.getElementById("puntero");
      ctx.strokeStyle = tint.value;
      ctx.beginPath();
      ctx.moveTo(lastPos.x, lastPos.y);
      ctx.lineTo(mousePos.x, mousePos.y);
      console.log(punta.value);
      ctx.lineWidth = punta.value;
      ctx.stroke();
      ctx.closePath();
      lastPos = mousePos;
    }
  }

  function clearCanvas() {
    canvas.width = canvas.width;
  }

  // Allow for animation
  (function drawLoop () {
    requestAnimFrame(drawLoop);
    renderCanvas();
  })();

})();    
</script>

<?php if(StockData::getPrincipal()->method=="1"): ?>
<style>

#draw-canvas {
  border: 2px dotted #CCCCCC;
  border-radius: 5px;
  cursor: crosshair;
  background-image: url("CF-SYSTEMS/storage/configuration/fxrma.png");
  background-size: 100% 100%;
}

#draw-dataUrl {
  width: 100%;
}

</style>

<?php elseif(StockData::getPrincipal()->method=="2"): ?>
<style>

#draw-canvas {
  border: 2px dotted #CCCCCC;
  border-radius: 5px;
  cursor: crosshair;
  background-image: url("CF-SYSTEMS/storage/configuration/furma.png");
  background-size: 100% 100%;
}

#draw-dataUrl {
  width: 100%;
}

</style>

<?php endif;?>


            </div>

            </div>
            </div>
</div>

</div>
</div>
</div>
  </div>
</div>

  
  </div>
                      </div>
                    </div>
                 

            </div>

            </div>

            </div>
            </div>
</div>

</div>
</div>
</div>
  </div>
</div>
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"):
$user = BookingData::getById($_GET["id"]);?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0"><i class='fa fa-edit'></i> Editar Contrato</h1>
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
         <div class="card" style="background-color:#222;">

<div  class="card-header">
<i class="fa fa-user-plus"></i>  Datos del Cliente:
</div>
<div class="card-body">
    <form class="form-horizontal" method="post" id="upd" role="form" enctype="multipart/form-data">
      <div class="row">
 <div class="col-md-3 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Conductor #1</label>
      <?php $clients = PersonData::getAll();?>
    <select style="background-color:#333;" name="person_id" class="form-control select2" required>
      <option value="0">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($client->id==$user->person_id){ echo "selected"; }?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
        
        </div>
    </div>

        <div class="col-md-3 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Conductor #2</label>
      <?php $clients = PersonData::getAll();?>
    <select style="background-color:#333;" name="person2_id" class="form-control select2">
      <option value="0">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($client->id==$user->person2_id){ echo "selected"; }?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
       
        </div>
    </div>
   
   <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha a Entregar</label>
      <input style="background-color:#333;" type="datetime-local" value="<?php echo $user->start_at;?>" required name="start_at" id="start_at" class="form-control " >
    </div>


     <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha de Devolucion</label>
      <input style="background-color:#333;" type="datetime-local" value="<?php echo $user->end_at;?>" required name="end_at" id="end_at" class="form-control " >
        </div>

    
   
  <div class="col-md-4 col-12">
  <label class="col-md-12 col-12 control-label">
    <?php 
      switch (Core::$user->language){
        case 'ES': echo "Lugar a Entregar"; break;
        case 'EN': echo "Place to Deliver"; break;
      }
    ?>
  </label>

  <div class="input-group">
    <!-- SELECT2 visible al inicio -->
    <select id="place_start" class="form-control select2" name="place_start" style="background-color:#333;">
      <option value="" selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>
      <?php foreach(PlaceData::getAll() as $place): ?>
        <option value="<?php echo $place->name; ?>"><?php echo $place->name; ?></option>
      <?php endforeach; ?>
    </select>

    <!-- INPUT oculto al inicio -->
    <input type="text" id="place_start2" name="place_start2" class="form-control" placeholder="Escribe aquí" style="background-color:#333; display:none;">
  </div>

  <!-- BOTÓN DE CAMBIO -->
  <small id="toggleplace_start" style="color:orange; font-size:13px; margin-top:5px; display:block; cursor:pointer;">
    <?php 
switch (Core::$user->language){
 case 'ES': echo "CAMBIAR ENTRE ESCRIBIR Y SELECCIONAR"; break;
 case 'EN': echo "SWITCH BETWEEN WRITE AND SELECT"; break;
}
?>
  </small>
</div>

<script>
  $(document).ready(function () {
    let modoManual = false;

    $('#toggleplace_start').click(function () {
      if (!modoManual) {
        // Ocultar SELECT2 y mostrar INPUT
        $('#place_start').select2('destroy').hide();
        $('#place_start2').show();
        $('#place_start').val('');
        modoManual = true;
      } else {
        // Volver a mostrar SELECT2 y ocultar INPUT
        $('#place_start2').hide();
        $('#place_start2').val('');
        $('#place_start').show().select2();
        modoManual = false;
      }
    });
  });
</script>



    <div class="col-md-4 col-12">
  <label class="col-md-12 col-12 control-label">
    <?php 
      switch (Core::$user->language){
        case 'ES': echo "Lugar de Devolucion"; break;
        case 'EN': echo "Place to Receive"; break;
      }
    ?>
  </label>

  <div class="input-group">
    <!-- SELECT2 visible al inicio -->
    <select id="place_end" class="form-control select2" name="place_end" style="background-color:#333;">
      <option value="" selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>
      <?php foreach(PlaceData::getAll() as $place): ?>
        <option value="<?php echo $place->name; ?>"><?php echo $place->name; ?></option>
      <?php endforeach; ?>
    </select>

    <!-- INPUT oculto al inicio -->
    <input type="text" id="place_end2" name="place_end2" class="form-control" placeholder="Escribe aquí" style="background-color:#333; display:none;">
  </div>

  <!-- BOTÓN DE CAMBIO -->
  <small id="toggleplace_end" style="color:orange; font-size:13px; margin-top:5px; display:block; cursor:pointer;">
    <?php 
switch (Core::$user->language){
 case 'ES': echo "CAMBIAR ENTRE ESCRIBIR Y SELECCIONAR"; break;
 case 'EN': echo "SWITCH BETWEEN WRITE AND SELECT"; break;
}
?>
  </small>
</div>


<script>
  $(document).ready(function () {
    let modoManual2 = false;

    $('#toggleplace_end').click(function () {
      if (!modoManual2) {
        // Ocultar SELECT2 y mostrar INPUT
        $('#place_end').select2('destroy').hide();
        $('#place_end2').show();
        $('#place_end').val('');
        modoManual2 = true;
      } else {
        // Volver a mostrar SELECT2 y ocultar INPUT
        $('#place_end2').hide();
        $('#place_end2').val('');
        $('#place_end').show().select2();
        modoManual2 = false;
      }
    });
  });
</script>
   
<div  class="card-header col-md-12 col-12 my-2">
<i class="fa fa-car"></i>  Datos del Vehiculo:
</div>


    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Ubicacion</label>
      <?php $clients = LocationData::getAll();?>
    <select style="background-color:#333;" name="location" required class="form-control select2" id="location" >
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
  
    <div class="col-md-3 col-12" hidden>
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Rent Car</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-street-view"></i></span>
    <select style="background-color:#333;" name="stock_id" required id="select2lista"  class="form-control"></select>
    </div>
  </div>

        <div class="col-md-3 col-12">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vehiculo/Original</label>
    <select style="background-color:#333;" name="car_id" id="cars"  class="form-control select2"></select>
    </div>
  </div>

       <div class="col-md-3 col-12">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vehiculo/Reemplazo</label>
    <?php $clients = CarsData::getAll();?>
    <select style="background-color:#333;" name="car2_id" required class="form-control select2">
    <option value="0">--<?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?>--</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->getBrand()->name." ".$client->name." ".$client->year." ".$client->plate;?></option>
    <?php endforeach;?>
      </select></div>
  </div>

 <div hidden class="col-md-2 col-12">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Divisa</label>
    <select style="background-color:#333;" name="divisa_id" id="divisa_id" class="form-control">
    <option value="1">DOLAR</option>
      </select></div>
  </div>
   <div class="col-md-2 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Dias de Renta</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-list-ol"></i></span>
     <input style="background-color:#333;" id="dias" name="day"  class="form-control" disabled>
    </div>
</div>
  
    <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Precio X Dia:</label>
    <input style="background-color:#333;" type="number" name="price" value="<?php echo $user->price;?>"  id="tariff2"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" min="0" step="0.01">
    </div>
    </div>


   
         <div class="col-md-2 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Reserva</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <select style="background-color:#333;" name="total" id="amount"  class="form-control"></select>
    </div>
  </div>
  
   <div class="col-md-2 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Extra</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <select style="background-color:#333;" name="xtotal" id="xmount"  class="form-control"></select>
    </div>
  </div>


 <div hidden id="day"></div>
    <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Combustible</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-tint"></i></span>
     <select style="background-color:#333;" name="fuel"  class="form-control">
      <option value="E" <?php if($user->fuel=="E"){ echo "selected"; }?>>En Contrato</option>
      <option value="1/4" <?php if($user->fuel=="1/4"){ echo "selected"; }?>>1/4</option>
      <option value="1/2" <?php if($user->fuel=="1/2"){ echo "selected"; }?>>Medio</option>
      <option value="3/4" <?php if($user->fuel=="3/4"){ echo "selected"; }?>>3/4</option>
      <option value="F" <?php if($user->fuel=="F"){ echo "selected"; }?>>Full</option>
     </select>
    </div>
  </div>

<script type="text/javascript">

  $(document).ready(function(){
      $('#location').val(<?php echo $user->location;?>);
    recargarLista2();

    $('#location').change(function(){
      recargarLista();
    });
  })

  function recargarLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=all",
      data:"location=" + $('#location').val(),
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }

  function recargarLista2(){
     $.ajax({
      type:"POST",
      url:"./?action=get&opt=edit",
      data:"location=" + <?php echo $user->stock_id;?>,
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }


  $(document).ready(function(){
    $('#select2lista').val(<?php echo $user->car_id;?>);
    recargarLista3();
  })

  function recargar2Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars",
      data:"stock_id=" + $('#select2lista').val(),
      success:function(r){
        $('#cars').html(r);
      }
    });
  }

  function recargarLista3(){
  
     $.ajax({
      type:"POST",
      url:"./?action=get&opt=editcars",
      data:"car=" + <?php echo $user->car_id;?>,
      success:function(r){
        $('#cars').html(r);
      }
    });
  }

  $(document).ready(function(){
    $('#cars').val(<?php echo $user->price;?>);
    recargarLista4();

    $('#cars').change(function(){
      recargar3Lista();
    });
  })

  function recargar3Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
      data:"cars=" + $('#cars').val(),
      success:function(r){
        $('#tariff').html(r);
      }
    });
  }

   function recargarLista4(){
  
     $.ajax({
      type:"POST",
      url:"./?action=get&opt=editariff",
      data:"price=" + <?php echo $user->id;?>,
      success:function(r){
        $('#tariff').html(r);
      }
    });

      $.ajax({
      type:"POST",
      url:"./?action=get&opt=editday",
      data:"price=" + <?php echo $user->id;?>,
      success:function(r){
        $('#dias').html(r);
      }
    });

          $.ajax({
      type:"POST",
      url:"./?action=get&opt=editamount",
     data: {paycar: $('#payment').val(),tcars: $('#tariff').val(),cars: $('#cars').val(), price: <?php echo $user->day;?>},
      success:function(r){
        $('#amount').html(r);
      }
    });
  }

    $('#tariff').change(function(){
      Lista();

    });

function Lista(){
function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
}

    
 $("#dias").val(calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  ));
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=editamount",
       data: {paycar: $('#payment').val(),tcars: $('#tariff').val(),cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price",
       data: {cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });
$("#remaining").val(($('#tariff').val()*$('#dias').val())-$("#payment").val());
}
    tariff2.addEventListener("keyup", function()
    {   

  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff2",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(),cars: $('#cars').val(), price: $('#dias').val()},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: $('#dias').val()},
      success:function(r){
        $('#dias').html(r);
      }
    });

 
$("#remaining").val(($('#tariff2').val()*$('#dias').val())-$("#payment").val());
    }, false);
 


   
  function mostrar() {

      document.getElementById('tariff').style.display = 'none';
      document.getElementById('taff1').style.display = 'none';
      document.getElementById('taff2').style.display = 'block';
      document.getElementById('tariff2').style.display = 'block';
    }

    function ocultar() {

      document.getElementById('tariff').style.display = 'block';
      document.getElementById('taff1').style.display = 'block';
      document.getElementById('taff2').style.display = 'none';
      document.getElementById('tariff2').style.display = 'none';
    }
      
      document.getElementById('tariff').style.display = 'none';
      document.getElementById('taff1').style.display = 'none';

      document.getElementById('tariff2').style.display = 'block';
      document.getElementById('taff2').style.display = 'block';

$('#end_at').on('change', () => {
    var value = $('#end_at').val();
    
    if(value) {
       $('.warning').hide();
       $('#submit').prop('disabled', false);
    }
    

    
});
    
</script>
 <div hidden id="day"></div>

            
                <div class="col-md-12 col-12 my-2">
               
                     <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $_GET['id'];?>">
                   <button id="submit"  class="btn btn-primary btn-block btn-sm" disabled><i class="fa fa-check"></i> Finalizar</button>
                 
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
                  url: "./?action=contract&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Contrato Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=contract&opt=all'  }, delay); 
                     
                  }else{
                  $.jGrowl("No se Puede duplicar datos", { header: 'Error al Agregar' });
                  }
                  }
                });
                return false;
              });
            });
            </script>
            <script type="text/javascript">
function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input style="background-color:#333;" type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
      closeAllLists(e.target);
  });
}

/*An array containing all the country names in the world:*/
var placein = ["<?php foreach (PlaceData::getAll() as $client):?><?php echo $client->name; ?>","<?php endforeach; ?>"];

/*An array containing all the country names in the world:*/
var placeout = ["<?php foreach (PlaceData::getAll() as $client):?><?php echo $client->name; ?>","<?php endforeach; ?>"];

/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
autocomplete(document.getElementById("placein"), placein);
autocomplete(document.getElementById("placeout"), placeout);
</script>

<style type="text/css">


/*the container must be positioned relative:*/
.autocomplete {
  position: relative;
  display: inline-block;
}

input {
  border: 1px solid transparent;
  background-color: #343a40;
  padding: 10px;
  font-size: 16px;
}

input[type=text] {
  background-color: #343a40;
  width: 100%;
}

input[type=submit] {
  background-color: DodgerBlue;
  color: #fff;
  cursor: pointer;
}

.autocomplete-items {
  position: absolute;
  border: 1px solid #343a40;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #343a40; 
  border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #383f45; 
}

/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}
</style>


<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="random"): 
$user = BookingData::getById($_GET["id"]);?>

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
           <div class="col-sm-6">
          <h1 class="m-0"><i class='fa fa-random'></i> <?php  switch (Core::$user->language){  case 'ES': echo "Reemplazar Vehiculo"; break;  case 'EN': echo "Replace Vehicle"; break; } ?></h1>
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
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
 <div class="card card-warning card-outline" style="background-color:#222;">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="CF-SYSTEMS/storage/profiles/user.png"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?php echo $user->getPerson()->name." ".$user->getPerson->lastname;?></h3>

                <p class="text-muted text-center"><?php echo strtoupper($user->getPerson()->address);?></p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item" style="background-color:#222;">
                    <b>CEDULA:</b> <a class="float-right"><?php echo $user->getPerson()->no;?></a>
                  </li>
                  <li class="list-group-item" style="background-color:#222;">
                    <b>PASAPORTE:</b> <a class="float-right"><?php echo $user->getPerson()->passport;?></a>
                  </li>
                  <li class="list-group-item" style="background-color:#222;">
                     <b>TEL:</b> <a class="float-right"><?php echo $user->getPerson()->phone;?></a>
                  </li>
                </ul>

               
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

      
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card" style="background-color:#222;">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#timeline" data-toggle="tab">Cambiar Vehiculo</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane" id="activity">
    
    
    <script type="text/javascript">

  $(document).ready(function(){
    $('#location').val();
    recargarLista();

    $('#location').change(function(){
      recargarLista();
    });
  })

  function recargarLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=edit",
      data:"location=" + <?php echo $user->car_id;?>,
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }


  $(document).ready(function(){
    $('#select2lista').val();
    recargar2Lista();
     recargar4Lista();

    $('#select2lista').change(function(){
      recargar2Lista();
       recargar4Lista();
    });
  })


  /* ACTUAL */
  function recargar2Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=editcars",
      data:"car=" + <?php echo $user->car_id;?>,
      success:function(r){
        $('#cars').html(r);
      }
    });
  }
  
  /* REEMPLAZO */
  function recargar4Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=editcar2s",
      data:"id=" + <?php echo $user->id?>,
      success:function(r){
        $('#car2s').html(r);
      }
    });
  }


  $(document).ready(function(){
    $('#cars').val();
    recargar3Lista();

    $('#cars').change(function(){
      recargar3Lista();
    });
  })

  function recargar3Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
      data:"cars=" + $('#cars').val(),
      success:function(r){
        $('#tariff').html(r);
      }
    });
  }

    $('#tariff').change(function(){
      Lista();

    });

$("#payment").keyup(function(){
function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 

$("#remaining").val( agregarSeparadorMiles(parseFloat($("#amount").val())-parseFloat($("#payment").val())));
 });

function Lista(){
function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni),
      hasta = new Date(fechaFin),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return  Math.round((diff / diaEnMils));
}

 document.getElementById('dias').innerText = calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  );

 var vprice = document.getElementById('day').innerText = calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  );
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=amount",
       data: {paycar: $('#payment').val(),tcars: $('#tariff').val(),dvs: $('#divisa_id').val(),cars: $('#cars').val(), price:vprice},
      success:function(r){
        $('#amount').html(r);
      }
    });

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price",
       data: {cars: $('#cars').val(), price: vprice},
      success:function(r){
        $('#dias').html(r);
      }
    });
$("#remaining").val((($('#tariff').val()*vprice)*$('#divisa_id').val())-$('#payment').val());}



    tariff2.addEventListener("keyup", function()
    {
        function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni),
      hasta = new Date(fechaFin),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return  Math.round((diff / diaEnMils));
}

 document.getElementById('dias').innerText = calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  );

 var vprice = document.getElementById('day').innerText = calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  );


    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff2",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: vprice},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: vprice},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val((($('#tariff2').val()*vprice)*$('#divisa_id').val())-$('#payment').val());
    }, false);

</script>
          
                  </div>
                  <!-- /.tab-pane -->
                  <div class="active tab-pane" id="timeline">
                    <!-- The timeline -->
  <form class="form-horizontal" action="./?action=contract&opt=updrandom2" method="post" id="delivery" role="form" enctype="multipart/form-data">                  
       <div class="row">
           
       
  
  <div class="col-md-6 col-12">
  <label class="col-md-12 col-12 control-label">
    <?php 
      switch (Core::$user->language){
        case 'ES': echo "Lugar a Entregar"; break;
        case 'EN': echo "Place to Deliver"; break;
      }
    ?>
  </label>

  <div class="input-group">
    <!-- SELECT2 visible al inicio -->
    <select id="place_start" class="form-control select2" name="place_start" style="background-color:#333;">
      <option value="" selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>
      <?php foreach(PlaceData::getAll() as $place): ?>
        <option value="<?php echo $place->name; ?>"><?php echo $place->name; ?></option>
      <?php endforeach; ?>
    </select>

    <!-- INPUT oculto al inicio -->
    <input type="text" id="place_start2" name="place_start2" class="form-control" placeholder="Escribe aquí" style="background-color:#333; display:none;">
  </div>

  <!-- BOTÓN DE CAMBIO -->
  <small id="toggleplace_start" style="color:orange; font-size:13px; margin-top:5px; display:block; cursor:pointer;">
    <?php 
switch (Core::$user->language){
 case 'ES': echo "CAMBIAR ENTRE ESCRIBIR Y SELECCIONAR"; break;
 case 'EN': echo "SWITCH BETWEEN WRITE AND SELECT"; break;
}
?>
  </small>
</div>

<script>
  $(document).ready(function () {
    let modoManual = false;

    $('#toggleplace_start').click(function () {
      if (!modoManual) {
        // Ocultar SELECT2 y mostrar INPUT
        $('#place_start').select2('destroy').hide();
        $('#place_start2').show();
        $('#place_start').val('');
        modoManual = true;
      } else {
        // Volver a mostrar SELECT2 y ocultar INPUT
        $('#place_start2').hide();
        $('#place_start2').val('');
        $('#place_start').show().select2();
        modoManual = false;
      }
    });
  });
</script>



    <div class="col-md-6 col-12">
  <label class="col-md-12 col-12 control-label">
    <?php 
      switch (Core::$user->language){
        case 'ES': echo "Lugar de Devolucion"; break;
        case 'EN': echo "Place to Receive"; break;
      }
    ?>
  </label>

  <div class="input-group">
    <!-- SELECT2 visible al inicio -->
    <select id="place_end" class="form-control select2" name="place_end" style="background-color:#333;">
      <option value="" selected disabled>--- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?> ---</option>
      <?php foreach(PlaceData::getAll() as $place): ?>
        <option value="<?php echo $place->name; ?>"><?php echo $place->name; ?></option>
      <?php endforeach; ?>
    </select>

    <!-- INPUT oculto al inicio -->
    <input type="text" id="place_end2" name="place_end2" class="form-control" placeholder="Escribe aquí" style="background-color:#333; display:none;">
  </div>

  <!-- BOTÓN DE CAMBIO -->
  <small id="toggleplace_end" style="color:orange; font-size:13px; margin-top:5px; display:block; cursor:pointer;">
    <?php 
switch (Core::$user->language){
 case 'ES': echo "CAMBIAR ENTRE ESCRIBIR Y SELECCIONAR"; break;
 case 'EN': echo "SWITCH BETWEEN WRITE AND SELECT"; break;
}
?>
  </small>
</div>


<script>
  $(document).ready(function () {
    let modoManual2 = false;

    $('#toggleplace_end').click(function () {
      if (!modoManual2) {
        // Ocultar SELECT2 y mostrar INPUT
        $('#place_end').select2('destroy').hide();
        $('#place_end2').show();
        $('#place_end').val('');
        modoManual2 = true;
      } else {
        // Volver a mostrar SELECT2 y ocultar INPUT
        $('#place_end2').hide();
        $('#place_end2').val('');
        $('#place_end').show().select2();
        modoManual2 = false;
      }
    });
  });
</script>
  
        <div class="col-md-6 col-12">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vehiculo/Actual</label>
    <select style="background-color:#333;" name="car_id" id="cars" required  class="form-control select2"></select>
    </div>
  </div>


    <div class="col-md-3 col-12"> 
    <label for="inputEmail1" class="col-md-12 col-12 control-label">KM Actual</label>
      <input style="background-color:#333;" type="number" required name="kms" class="form-control"  placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" autocomplete="off" min="0">
</div>
<div class="col-md-3 col-12">
    <div class="input-group" >
  <label for="inputEmail1" class="col-md-12 col-12 control-label">Combustible</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-tint"></i></span>
     <select style="background-color:#333;" name="fuel"  class="form-control">
       <option value="R" <?php if($user->fuel=="R"): echo "selected"; endif;?>>Reserva</option>
      <option value="1/4" <?php if($user->fuel=="1/4"): echo "selected"; endif;?>>1/4</option>
      <option value="1/2" <?php if($user->fuel=="1/2"): echo "selected"; endif;?>>Medio</option>
      <option value="3/4" <?php if($user->fuel=="3/4"): echo "selected"; endif;?>>3/4</option>
      <option value="F" <?php if($user->fuel=="F"): echo "selected"; endif;?>>Full</option>
     </select>
    </div>
  </div>
  
<?php $cars = CarsData::getById($user->car_id);?>


  
            <div class="col-md-12 my-2">

                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <div class="card-body">
                        <label>Revision del Vehiculo/Actual: </label>

<div class="row">
    <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="cat" id="checkboxPrimary1" checked>
<label for="checkboxPrimary1">
GATO
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="radio" id="checkboxPrimary2" checked>
                        <label for="checkboxPrimary2">
                          RADIO
                        </label>
                      </div>
    </div>

    <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox"  name="replacement" id="checkboxPrimary3" checked>
<label for="checkboxPrimary3">
REPUESTO
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="antenna" id="checkboxPrimary4" checked>
                        <label for="checkboxPrimary4">
                          ANTENA
                        </label>
                      </div>
    </div>


    <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="keyring" id="checkboxPrimary5" checked>
<label for="checkboxPrimary5">
LLAVERO
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="carpets" id="checkboxPrimary6" checked>
                        <label for="checkboxPrimary6">
                          ALFOMBRAS
                        </label>
                      </div>
    </div>

   <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="belts" id="checkboxPrimary7" checked>
<label for="checkboxPrimary7">
CINTURONES
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="roof_lining" id="checkboxPrimary8" checked>
                        <label for="checkboxPrimary8">
                         FORRO TECHO
                        </label>
                      </div>
    </div>    


   <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="mirrors" id="checkboxPrimary9" checked>
<label for="checkboxPrimary9">
ESPEJOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="board" id="checkboxPrimary10" checked>
                        <label for="checkboxPrimary10">
                         TABLERO
                        </label>
                      </div>
    </div>  

       <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="document" id="checkboxPrimary11" checked>
<label for="checkboxPrimary11">
DOCUMENTOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="watches" id="checkboxPrimary12" checked>
                        <label for="checkboxPrimary12">
                         RELOJES
                        </label>
                      </div>
    </div>  


       <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="rearview" id="checkboxPrimary13" checked>
<label for="checkboxPrimary13">
RETREVISOR
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="lighter" id="checkboxPrimary14" checked>
                        <label for="checkboxPrimary14">
                         ENCENDEDOR
                        </label>
                      </div>
    </div>  

           <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="crystals" id="checkboxPrimary15" checked>
<label for="checkboxPrimary15">
CRISTALES
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="cd" id="checkboxPrimary16" checked>
                        <label for="checkboxPrimary16">
                         CD CHANGER
                        </label>
                      </div>
    </div>  


           <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="bumper" id="checkboxPrimary17" checked>
<label for="checkboxPrimary17">
TAPA COV. BUMPER
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="equalizer" id="checkboxPrimary18" checked>
                        <label for="checkboxPrimary18">
                         ECUALIZADOR
                        </label>
                      </div>
    </div>  

               <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="cup_holder" id="checkboxPrimary19" checked>
<label for="checkboxPrimary19">
PORTA VASOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="plate" id="checkboxPrimary20" checked>
                        <label for="checkboxPrimary20">
                         PLACA
                        </label>
                      </div>
    </div>  

 

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="seats" id="checkboxPrimary21" checked>
                        <label for="checkboxPrimary21">
                         ASIENTOS
                        </label>
                      </div>
    </div>  

               <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="logo" id="checkboxPrimary22" checked>
<label for="checkboxPrimary22">
LOGOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="batery" id="checkboxPrimary23" checked>
                        <label for="checkboxPrimary23">
                        BATERIA
                        </label>
                      </div>
    </div> 



    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="top" id="checkboxPrimary24" checked>
                        <label for="checkboxPrimary24">
TAPA COMBUSTIBLE
                   
                        </label>
                      </div>
    </div> 
 
  </div>

                      </div>
                    </div>

              </div>
              
             
<style>
    
#vert-tabs-frontal-tab.nav-link{
    color: orange !important;
}
#vert-tabs-lateral-izquierdo-tab.nav-link{
    color: orange !important;
}
#vert-tabs-lateral-derecho-tab.nav-link{
    color: orange !important;
}
#vert-tabs-trasera-tab.nav-link{
    color: orange !important;
}
#vert-tabs-superior-tab.nav-link{
    color: orange !important;
}
#vert-tabs-inferior-tab.nav-link{
    color: orange !important;
}
</style>

        <div class="card card card-outline"  style="background-color:#222;">
         
          <div class="card-body">
         <div class="nav-wrapper">
  <ul class="nav nav-tabs d-flex flex-nowrap" id="custom-content-above-tab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="vert-tabs-frontal-tab" data-toggle="pill" href="#vert-tabs-frontal" role="tab" aria-controls="vert-tabs-frontal" aria-selected="true">FRONTAL</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-lateral-izquierdo-tab" data-toggle="pill" href="#vert-tabs-lateral-izquierdo" role="tab" aria-controls="vert-tabs-lateral-izquierdo" aria-selected="false">LATERAL IZQUIERDO</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-lateral-derecho-tab" data-toggle="pill" href="#vert-tabs-lateral-derecho" role="tab" aria-controls="vert-tabs-lateral-derecho" aria-selected="false">LATERAL DERECHO</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-trasera-tab" data-toggle="pill" href="#vert-tabs-trasera" role="tab" aria-controls="vert-tabs-trasera" aria-selected="false">TRASERA</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-superior-tab" data-toggle="pill" href="#vert-tabs-superior" role="tab" aria-controls="vert-tabs-superior" aria-selected="false">SUPERIOR</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-inferior-tab" data-toggle="pill" href="#vert-tabs-inferior" role="tab" aria-controls="vert-tabs-inferior" aria-selected="false">INFERIOR</a>
    </li>
  </ul>
</div>

<style>
  .nav-wrapper {
    overflow-x: auto;  /* Permite el scroll horizontal */
    overflow-y: hidden; /* Evita el scroll vertical */
    white-space: nowrap;
  }
  .nav-tabs {
    flex-wrap: nowrap;
  }
</style>

            <div class="tab-custom-content">
              <p class="lead mb-0">  <?php 
switch (Core::$user->language){
 case 'ES': echo "SECCIONES"; break;
 case 'EN': echo "SECTIONS"; break;
}
?></p>
            </div>
            <div class="row">
          
              <div class="col-12 col-md-12">
                <div class="tab-content" id="vert-tabs-tabContent">
                  <div class="tab-pane text-left fade show active" id="vert-tabs-frontal" role="tabpanel" aria-labelledby="vert-tabs-frontal-tab">
                     
            <!-- TO DO List -->
            <div  style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck1">
                      <label for="todoCheck1"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text1">Capó</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image1" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image1" type="file" style="display: none;" accept="image/*"  name="image1">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck1').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image1').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck1').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text1').style.textDecoration = 'line-through';
    }
  });
</script>
                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck2">
                      <label for="todoCheck2"></label>
                    </div>
                     <span class="text" id="text2">Parachoques</span>
                    <div class="tools">
                      <label for="image2" class="custom-file-upload"><i class="fa fa-upload"></i></label> 
                      <input id="image2" type="file" style="display: none;" accept="image/*"  name="image2">
                    </div>
                  </li>
                 
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck2').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image2').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck2').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text2').style.textDecoration = 'line-through';
    }
  });
</script>
                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck3">
                      <label for="todoCheck3"></label>
                    </div>
                     <span class="text" id="text3">Faros</span>
                    <div class="tools">
                     <label for="image3" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image3" type="file" style="display: none;" accept="image/*"  name="image3">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck3').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image3').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck3').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text3').style.textDecoration = 'line-through';
    }
  });
</script>
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck4">
                      <label for="todoCheck4"></label>
                    </div>
                     <span class="text" id="text4">Parrilla</span>
                    <div class="tools">
                     <label for="image4" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image4" type="file" style="display: none;" accept="image/*"  name="image4">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck4').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image4').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck4').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text4').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck5">
                      <label for="todoCheck5"></label>
                    </div>
                     <span class="text" id="text5">Parabrisas</span>
                    <div class="tools">
                     <label for="image5" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image5" type="file" style="display: none;" accept="image/*"  name="image5">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck3').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image3').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck3').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text3').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck6">
                      <label for="todoCheck6"></label>
                    </div>
                     <span class="text" id="text6">Forlay</span>
                    <div class="tools">
                     <label for="image6" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image6" type="file" style="display: none;" accept="image/*"  name="image6">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck6').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image6').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck6').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text6').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                 <input style="background-color:#222;" autocomplete="off" name="comment1"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-lateral-izquierdo" role="tabpanel" aria-labelledby="vert-tabs-lateral-izquierdo-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
            
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck7">
                      <label for="todoCheck7"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text7">Puertas</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image7" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image7" type="file" style="display: none;" accept="image/*"  name="image7">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck7').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image7').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck7').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text7').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck8">
                      <label for="todoCheck8"></label>
                    </div>
                     <span class="text" id="text8">Guardafangos</span>
                    <div class="tools">
                     <label for="image8" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image8" type="file" style="display: none;" accept="image/*"  name="image8">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck8').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image8').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck8').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text8').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck9">
                      <label for="todoCheck9"></label>
                    </div>
                     <span class="text" id="text9">Espejos retrovisores</span>
                    <div class="tools">
                     <label for="image9" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image9" type="file" style="display: none;" accept="image/*"  name="image9">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck9').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image9').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck9').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text9').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck10">
                      <label for="todoCheck10"></label>
                    </div>
                     <span class="text" id="text10">Ventanas laterales</span>
                    <div class="tools">
                     <label for="image10" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image10" type="file" style="display: none;" accept="image/*"  name="image10">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck10').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image10').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck10').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text10').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck11">
                      <label for="todoCheck11"></label>
                    </div>
                     <span class="text" id="text11">Manijas de las puertas</span>
                    <div class="tools">
                     <label for="image11" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image11" type="file" style="display: none;" accept="image/*"  name="image11">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck11').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image11').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck11').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text11').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck12">
                      <label for="todoCheck12"></label>
                    </div>
                     <span class="text" id="text12">Llantas y rines</span>
                    <div class="tools">
                     <label for="image12" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image12" type="file" style="display: none;" accept="image/*"  name="image12">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck12').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image12').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck12').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text12').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment2"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-lateral-derecho" role="tabpanel" aria-labelledby="vert-tabs-lateral-derecho-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
              
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
             
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck13">
                      <label for="todoCheck13"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text13">Puertas</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image13" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image13" type="file" style="display: none;" accept="image/*"  name="image13">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck13').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image13').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck13').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text13').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck14">
                      <label for="todoCheck14"></label>
                    </div>
                     <span class="text" id="text14">Guardafangos </span>
                    <div class="tools">
                   <label for="image14" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                   <input id="image14" type="file" style="display: none;" accept="image/*"  name="image14">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck14').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image14').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck14').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text14').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck15">
                      <label for="todoCheck15"></label>
                    </div>
                     <span class="text" id="text15">Espejos retrovisores</span>
                    <div class="tools">
                     <label for="image15" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image15" type="file" style="display: none;" accept="image/*"  name="image15">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck15').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image15').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck15').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text15').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck16">
                      <label for="todoCheck16"></label>
                    </div>
                     <span class="text" id="text16">Ventanas laterales</span>
                    <div class="tools">
                    <label for="image16" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                    <input id="image16" type="file" style="display: none;" accept="image/*"  name="image16">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck16').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image16').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck16').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text16').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck17">
                      <label for="todoCheck17"></label>
                    </div>
                     <span class="text" id="text17">Manijas de las puertas</span>
                    <div class="tools">
                     <label for="image17" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image17" type="file" style="display: none;" accept="image/*"  name="image17">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck17').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image17').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck17').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text17').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck18">
                      <label for="todoCheck18"></label>
                    </div>
                     <span class="text" id="text18">Llantas y rines</span>
                    <div class="tools">
                    <label for="image18" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image18" type="file" style="display: none;" accept="image/*"  name="image18">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck18').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image18').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck18').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text18').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment3"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>     
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-trasera" role="tabpanel" aria-labelledby="vert-tabs-trasera-tab">
                        
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
              
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck19">
                      <label for="todoCheck19"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text19">Parachoques</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                    <label for="image19" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image19" type="file" style="display: none;" accept="image/*"  name="image19">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck19').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image19').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck19').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text19').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck20">
                      <label for="todoCheck20"></label>
                    </div>
                     <span class="text" id="text20">Compuerta</span>
                    <div class="tools">
                     <label for="image20" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image20" type="file" style="display: none;" accept="image/*"  name="image20">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck20').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image20').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck20').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text20').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck21">
                      <label for="todoCheck21"></label>
                    </div>
                     <span class="text" id="text21">Faros</span>
                    <div class="tools">
                     <label for="image21" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image21" type="file" style="display: none;" accept="image/*"  name="image21">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck21').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image21').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck21').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text21').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck22">
                      <label for="todoCheck22"></label>
                    </div>
                     <span class="text" id="text22">Escape</span>
                    <div class="tools">
                    <label for="image22" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                    <input id="image22" type="file" style="display: none;" accept="image/*"  name="image22">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck22').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image22').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck22').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text22').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck23">
                      <label for="todoCheck23"></label>
                    </div>
                     <span class="text" id="text23">Vidrio trasero</span>
                    <div class="tools">
                   <label for="image23" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                   <input id="image23" type="file" style="display: none;" accept="image/*"  name="image23">
                    </div>
                  </li>                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck23').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image23').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck23').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text23').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment4"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                    <div class="tab-pane fade" id="vert-tabs-superior" role="tabpanel" aria-labelledby="vert-tabs-superior-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck24">
                      <label for="todoCheck24"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text24">Techo</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                    <label for="image24" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image24" type="file" style="display: none;" accept="image/*"  name="image24">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck24').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image24').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck24').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text24').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck25">
                      <label for="todoCheck25"></label>
                    </div>
                     <span class="text" id="text25">Antena</span>
                    <div class="tools">
                   <label for="image25" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                   <input id="image25" type="file" style="display: none;" accept="image/*"  name="image25">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck25').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image25').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck25').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text25').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment5"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->      
                    <div class="tab-pane fade" id="vert-tabs-inferior" role="tabpanel" aria-labelledby="vert-tabs-inferior-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck26">
                      <label for="todoCheck26"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text26">Chasis</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                  <label for="image26" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                  <input id="image26" type="file" style="display: none;" accept="image/*"  name="image26">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck26').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image26').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck26').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text26').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck27">
                      <label for="todoCheck27"></label>
                    </div>
                     <span class="text" id="text27">Suspensión</span>
                    <div class="tools">
                    <label for="image27" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image27" type="file" style="display: none;" accept="image/*"  name="image27">
                    </div>
                  </li>                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck27').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image27').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck27').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text27').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck28">
                      <label for="todoCheck28"></label>
                    </div>
                     <span class="text" id="text28">Amortiguador</span>
                    <div class="tools">
                    <label for="image28" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image28" type="file" style="display: none;" accept="image/*"  name="image28">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck28').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image28').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck28').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text28').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                  <div class="icheck-primary d-inline ml-2">
                  <input type="checkbox" id="todoCheck29"> <!-- Checkbox habilitado para envío -->
                  <label for="todoCheck29"></label>
                  </div>
                  <span class="text" id="text29">Otros</span>
                  <div class="tools">
                  <label for="image29" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                  <input id="image29" type="file" style="display: none;" accept="image/*"  name="image29">
                  </div>
                  </li>

<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck29').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image29').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck29').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text29').style.textDecoration = 'line-through';
    }
  });
</script>
                  
                  
                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment6"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /.card -->
          
  
 
        </div>
        
          <div  class="card-header col-md-12 col-12 my-3" style="background-color:gray;">
<i class="fa fa-car"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo de Reemplazo"; break;
 case 'EN': echo "Replacement Vehicle"; break;
}
?>:
</div> 
   
  <div class="col-md-6 col-12">
      
        <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vehiculo/Reemplazo</label>
    <select style="background-color:#333;" name="car2_id" id="car2s" required  class="form-control select2"></select>
    </div>
</div>

   <div class="col-md-3 col-12"> 
    <label for="inputEmail1" class="col-md-12 col-12 control-label">KM Actual</label>
      <input style="background-color:#333;" type="number" required  name="kms2" class="form-control"  placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" autocomplete="off" min="0">
</div>

  <div class="col-md-3 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Combustible</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-tint"></i></span>
     <select style="background-color:#333;" name="fuel2"  class="form-control">
      <option value="R">Reserva</option>
      <option value="1/4">1/4</option>
      <option value="1/2">Medio</option>
      <option value="3/4">3/4</option>
      <option value="F">Full</option>
     </select>
    </div>
  </div>
  
   <div class="col-md-12 my-2">

                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <div class="card-body">
                        <label>Revision del Vehiculo/Reemplazo: </label>

<div class="row">
    <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="cat2" id="checkboxRPrimary1" checked>
<label for="checkboxRPrimary1">
GATO
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="radio2" id="checkboxRPrimary2" checked>
                        <label for="checkboxRPrimary2">
                          RADIO
                        </label>
                      </div>
    </div>

    <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox"  name="replacement2" id="checkboxRPrimary3" checked>
<label for="checkboxRPrimary3">
REPUESTO
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="antenna2" id="checkboxRPrimary4" checked>
                        <label for="checkboxRPrimary4">
                          ANTENA
                        </label>
                      </div>
    </div>


    <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="keyring2" id="checkboxRPrimary5" checked>
<label for="checkboxRPrimary5">
LLAVERO
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="carpets2" id="checkboxRPrimary6" checked>
                        <label for="checkboxRPrimary6">
                          ALFOMBRAS
                        </label>
                      </div>
    </div>

   <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="belts2" id="checkboxRPrimary7" checked>
<label for="checkboxRPrimary7">
CINTURONES
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="roof_lining2" id="checkboxRPrimary8" checked>
                        <label for="checkboxRPrimary8">
                         FORRO TECHO
                        </label>
                      </div>
    </div>    


   <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="mirrors2" id="checkboxRPrimary9" checked>
<label for="checkboxRPrimary9">
ESPEJOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="board2" id="checkboxRPrimary10" checked>
                        <label for="checkboxRPrimary10">
                         TABLERO
                        </label>
                      </div>
    </div>  

       <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="document2" id="checkboxRPrimary11" checked>
<label for="checkboxRPrimary11">
DOCUMENTOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                      <input style="background-color:#333;" type="checkbox" name="watches2" id="checkboxRPrimary12" checked>
                        <label for="checkboxRPrimary12">
                         RELOJES
                        </label>
                      </div>
    </div>  


       <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="rearview2" id="checkboxRPrimary13" checked>
<label for="checkboxRPrimary13">
RETREVISOR
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                      <input style="background-color:#333;" type="checkbox" name="lighter2" id="checkboxRPrimary14" checked>
                        <label for="checkboxRPrimary14">
                         ENCENDEDOR
                        </label>
                      </div>
    </div>  

           <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="crystals2" id="checkboxRPrimary15" checked>
<label for="checkboxRPrimary15">
CRISTALES
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="cd2" id="checkboxRPrimary16" checked>
                        <label for="checkboxRPrimary16">
                         CD CHANGER
                        </label>
                      </div>
    </div>  


           <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="bumper2" id="checkboxRPrimary17" checked>
<label for="checkboxRPrimary17">
TAPA COV. BUMPER
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                    <input style="background-color:#333;" type="checkbox" name="equalizer2" id="checkboxRPrimary18" checked>
                        <label for="checkboxRPrimary18">
                         ECUALIZADOR
                        </label>
                      </div>
    </div>  

               <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="cup_holder2" id="checkboxRPrimary19" checked>
<label for="checkboxRPrimary19">
PORTA VASOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="plate2" id="checkboxRPrimary20" checked>
                        <label for="checkboxRPrimary20">
                         PLACA
                        </label>
                      </div>
    </div>  

 

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="seats2" id="checkboxRPrimary21" checked>
                        <label for="checkboxRPrimary21">
                         ASIENTOS
                        </label>
                      </div>
    </div>  

               <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="logo2" id="checkboxRPrimary22" checked>
<label for="checkboxRPrimary22">
LOGOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="batery2" id="checkboxRPrimary23" checked>
                        <label for="checkboxRPrimary23">
                        BATERIA
                        </label>
                      </div>
    </div> 



    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="top2" id="checkboxRPrimary24" checked>
                        <label for="checkboxRPrimary24">
TAPA COMBUSTIBLE
                   
                        </label>
                      </div>
    </div> 
    



  </div>

                      </div>
                    </div>

              </div>

<style>
    
#vert-tabs-frontal-tab.nav-link{
    color: orange !important;
}
#vert-tabs-lateral-izquierdo-tab.nav-link{
    color: orange !important;
}
#vert-tabs-lateral-derecho-tab.nav-link{
    color: orange !important;
}
#vert-tabs-trasera-tab.nav-link{
    color: orange !important;
}
#vert-tabs-superior-tab.nav-link{
    color: orange !important;
}
#vert-tabs-inferior-tab.nav-link{
    color: orange !important;
}

   
#vert-tabs-frontal-tab2.nav-link{
    color: orange !important;
}
#vert-tabs-lateral-izquierdo-tab2.nav-link{
    color: orange !important;
}
#vert-tabs-lateral-derecho-tab2.nav-link{
    color: orange !important;
}
#vert-tabs-trasera-tab2.nav-link{
    color: orange !important;
}
#vert-tabs-superior-tab2.nav-link{
    color: orange !important;
}
#vert-tabs-inferior-tab2.nav-link{
    color: orange !important;
}
</style>

        <div class="card card card-outline"  style="background-color:#222;">
         
          <div class="card-body">
         <div class="nav-wrapper">
  <ul class="nav nav-tabs d-flex flex-nowrap" id="custom-content-above-tab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="vert-tabs-frontal-tab2" data-toggle="pill" href="#vert-tabs-frontal2" role="tab" aria-controls="vert-tabs-frontal2" aria-selected="true">FRONTAL</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-lateral-izquierdo-tab2" data-toggle="pill" href="#vert-tabs-lateral-izquierdo2" role="tab" aria-controls="vert-tabs-lateral-izquierdo2" aria-selected="false">LATERAL IZQUIERDO</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-lateral-derecho-tab2" data-toggle="pill" href="#vert-tabs-lateral-derecho2" role="tab" aria-controls="vert-tabs-lateral-derecho2" aria-selected="false">LATERAL DERECHO</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-trasera-tab2" data-toggle="pill" href="#vert-tabs-trasera2" role="tab" aria-controls="vert-tabs-trasera2" aria-selected="false">TRASERA</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-superior-tab2" data-toggle="pill" href="#vert-tabs-superior2" role="tab" aria-controls="vert-tabs-superior2" aria-selected="false">SUPERIOR</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-inferior-tab2" data-toggle="pill" href="#vert-tabs-inferior2" role="tab" aria-controls="vert-tabs-inferior2" aria-selected="false">INFERIOR</a>
    </li>
  </ul>
</div>

<style>
  .nav-wrapper {
    overflow-x: auto;  /* Permite el scroll horizontal */
    overflow-y: hidden; /* Evita el scroll vertical */
    white-space: nowrap;
  }
  .nav-tabs {
    flex-wrap: nowrap;
  }
</style>

            <div class="tab-custom-content">
              <p class="lead mb-0">  <?php 
switch (Core::$user->language){
 case 'ES': echo "SECCIONES"; break;
 case 'EN': echo "SECTIONS"; break;
}
?></p>
            </div>
            <div class="row">
          
              <div class="col-12 col-md-12">
                <div class="tab-content" id="vert-tabs-tabContent">
                  <div class="tab-pane text-left fade show active" id="vert-tabs-frontal2" role="tabpanel" aria-labelledby="vert-tabs-frontal-tab2">
                     
            <!-- TO DO List -->
            <div  style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck1">
                      <label for="secundary_todoCheck1"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="secundary_text1">Capó</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="secundary_image1" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image1" type="file" style="display: none;" accept="image/*"  name="secundary_image1">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck1').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image1').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck1').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text1').style.textDecoration = 'line-through';
    }
  });
</script>
                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck2">
                      <label for="secundary_todoCheck2"></label>
                    </div>
                     <span class="text" id="secundary_text2">Parachoques</span>
                    <div class="tools">
                      <label for="secundary_image2" class="custom-file-upload"><i class="fa fa-upload"></i></label> 
                      <input id="secundary_image2" type="file" style="display: none;" accept="image/*"  name="secundary_image2">
                    </div>
                  </li>
                 
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck2').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image2').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck2').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text2').style.textDecoration = 'line-through';
    }
  });
</script>
                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck3">
                      <label for="secundary_todoCheck3"></label>
                    </div>
                     <span class="text" id="secundary_text3">Faros</span>
                    <div class="tools">
                     <label for="secundary_image3" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image3" type="file" style="display: none;" accept="image/*"  name="secundary_image3">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck3').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image3').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck3').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text3').style.textDecoration = 'line-through';
    }
  });
</script>
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="secundary_todoCheck4">
                      <label for="secundary_todoCheck4"></label>
                    </div>
                     <span class="text" id="secundary_text4">Parrilla</span>
                    <div class="tools">
                     <label for="secundary_image4" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image4" type="file" style="display: none;" accept="image/*"  name="secundary_image4">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck4').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image4').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck4').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text4').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck5">
                      <label for="todoCheck5"></label>
                    </div>
                     <span class="text" id="secundary_text5">Parabrisas</span>
                    <div class="tools">
                     <label for="secundary_image5" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image5" type="file" style="display: none;" accept="image/*"  name="secundary_image5">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck3').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image3').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck3').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text3').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck6">
                      <label for="secundary_todoCheck6"></label>
                    </div>
                     <span class="text" id="secundary_text6">Forlay</span>
                    <div class="tools">
                     <label for="secundary_image6" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image6" type="file" style="display: none;" accept="image/*"  name="secundary_image6">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck6').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image6').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck6').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text6').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                 <input style="background-color:#222;" autocomplete="off" name="secundary_comment1"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-lateral-izquierdo2" role="tabpanel" aria-labelledby="vert-tabs-lateral-izquierdo-tab2">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
            
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck7">
                      <label for="secundary_todoCheck7"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="secundary_text7">Puertas</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="secundary_image7" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image7" type="file" style="display: none;" accept="image/*"  name="secundary_image7">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck7').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image7').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck7').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text7').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="secundary_todoCheck8">
                      <label for="secundary_todoCheck8"></label>
                    </div>
                     <span class="text" id="secundary_text8">Guardafangos</span>
                    <div class="tools">
                     <label for="secundary_image8" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image8" type="file" style="display: none;" accept="image/*"  name="secundary_image8">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck8').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image8').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck8').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text8').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="secundary_todoCheck9">
                      <label for="secundary_todoCheck9"></label>
                    </div>
                     <span class="text" id="secundary_text9">Espejos retrovisores</span>
                    <div class="tools">
                     <label for="secundary_image9" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image9" type="file" style="display: none;" accept="image/*"  name="secundary_image9">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck9').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image9').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck9').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text9').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck10">
                      <label for="secundary_todoCheck10"></label>
                    </div>
                     <span class="text" id="secundary_text10">Ventanas laterales</span>
                    <div class="tools">
                     <label for="secundary_image10" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image10" type="file" style="display: none;" accept="image/*"  name="secundary_image10">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck10').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image10').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck10').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text10').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck11">
                      <label for="secundary_todoCheck11"></label>
                    </div>
                     <span class="text" id="secundary_text11">Manijas de las puertas</span>
                    <div class="tools">
                     <label for="secundary_image11" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image11" type="file" style="display: none;" accept="image/*"  name="secundary_image11">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck11').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image11').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck11').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text11').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck12">
                      <label for="secundary_todoCheck12"></label>
                    </div>
                     <span class="text" id="secundary_text12">Llantas y rines</span>
                    <div class="tools">
                     <label for="secundary_image12" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image12" type="file" style="display: none;" accept="image/*"  name="secundary_image12">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck12').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image12').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck12').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text12').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="secundary_comment2"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-lateral-derecho2" role="tabpanel" aria-labelledby="vert-tabs-lateral-derecho-tab2">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
              
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
             
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck13">
                      <label for="secundary_todoCheck13"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="secundary_text13">Puertas</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for=secundary_"image13" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image13" type="file" style="display: none;" accept="image/*"  name="secundary_image13">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck13').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image13').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck13').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text13').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck14">
                      <label for="secundary_todoCheck14"></label>
                    </div>
                     <span class="text" id="secundary_text14">Guardafangos </span>
                    <div class="tools">
                   <label for="secundary_image14" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                   <input id="secundary_image14" type="file" style="display: none;" accept="image/*"  name="secundary_image14">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck14').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image14').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck14').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text14').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck15">
                      <label for="secundary_todoCheck15"></label>
                    </div>
                     <span class="text" id="secundary_text15">Espejos retrovisores</span>
                    <div class="tools">
                     <label for="secundary_image15" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image15" type="file" style="display: none;" accept="image/*"  name="secundary_image15">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck15').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image15').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck15').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text15').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck16">
                      <label for="secundary_todoCheck16"></label>
                    </div>
                     <span class="text" id="secundary_text16">Ventanas laterales</span>
                    <div class="tools">
                    <label for="secundary_image16" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                    <input id="secundary_image16" type="file" style="display: none;" accept="image/*"  name="secundary_image16">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck16').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image16').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck16').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text16').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck17">
                      <label for="secundary_todoCheck17"></label>
                    </div>
                     <span class="text" id="secundary_text17">Manijas de las puertas</span>
                    <div class="tools">
                     <label for="secundary_image17" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image17" type="file" style="display: none;" accept="image/*"  name="secundary_image17">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck17').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image17').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck17').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text17').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck18">
                      <label for="secundary_todoCheck18"></label>
                    </div>
                     <span class="text" id="secundary_text18">Llantas y rines</span>
                    <div class="tools">
                    <label for="secundary_image18" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="secundary_image18" type="file" style="display: none;" accept="image/*"  name="secundary_image18">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck18').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image18').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck18').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text18').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="secundary_comment3"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>     
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-trasera2" role="tabpanel" aria-labelledby="vert-tabs-trasera-tab2">
                        
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
              
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck19">
                      <label for="secundary_todoCheck19"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="secundary_text19">Parachoques</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                    <label for="secundary_image19" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="secundary_image19" type="file" style="display: none;" accept="image/*"  name="secundary_image19">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck19').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image19').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck19').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text19').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck20">
                      <label for="secundary_todoCheck20"></label>
                    </div>
                     <span class="text" id="secundary_text20">Compuerta</span>
                    <div class="tools">
                     <label for="secundary_image20" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image20" type="file" style="display: none;" accept="image/*"  name="secundary_image20">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck20').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image20').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck20').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text20').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck21">
                      <label for="secundary_todoCheck21"></label>
                    </div>
                     <span class="text" id="secundary_text21">Faros</span>
                    <div class="tools">
                     <label for="secundary_image21" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="secundary_image21" type="file" style="display: none;" accept="image/*"  name="secundary_image21">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck21').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image21').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck21').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text21').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck22">
                      <label for="secundary_todoCheck22"></label>
                    </div>
                     <span class="text" id="secundary_text22">Escape</span>
                    <div class="tools">
                    <label for="secundary_image22" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                    <input id="secundary_image22" type="file" style="display: none;" accept="image/*"  name="secundary_image22">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck22').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image22').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck22').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text22').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="secundary_todoCheck23">
                      <label for="secundary_todoCheck23"></label>
                    </div>
                     <span class="text" id="secundary_text23">Vidrio trasero</span>
                    <div class="tools">
                   <label for="secundary_image23" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                   <input id="secundary_image23" type="file" style="display: none;" accept="image/*"  name="secundary_image23">
                    </div>
                  </li>                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck23').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image23').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck23').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text23').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="secundary_comment4"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                    <div class="tab-pane fade" id="vert-tabs-superior2" role="tabpanel" aria-labelledby="vert-tabs-superior-tab2">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="secundary_todoCheck24">
                      <label for="secundary_todoCheck24"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="secundary_text24">Techo</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                    <label for="secundary_image24" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="secundary_image24" type="file" style="display: none;" accept="image/*"  name="secundary_image24">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck24').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image24').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck24').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text24').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="secundary_todoCheck25">
                      <label for="secundary_todoCheck25"></label>
                    </div>
                     <span class="text" id="secundary_text25">Antena</span>
                    <div class="tools">
                   <label for="secundary_image25" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                   <input id="secundary_image25" type="file" style="display: none;" accept="image/*"  name="secundary_image25">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck25').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image25').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck25').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text25').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="secundary_comment5"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->      
                    <div class="tab-pane fade" id="vert-tabs-inferior2" role="tabpanel" aria-labelledby="vert-tabs-inferior-tab2">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="secundary_todoCheck26">
                      <label for="secundary_todoCheck26"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="secundary_text26">Chasis</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                  <label for="secundary_image26" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                  <input id="secundary_image26" type="file" style="display: none;" accept="image/*"  name="secundary_image26">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck26').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image26').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck26').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text26').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="secundary_todoCheck27">
                      <label for="secundary_todoCheck27"></label>
                    </div>
                     <span class="text" id="secundary_text27">Suspensión</span>
                    <div class="tools">
                    <label for="secundary_image27" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="secundary_image27" type="file" style="display: none;" accept="image/*"  name="secundary_image27">
                    </div>
                  </li>                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck27').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image27').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck27').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text27').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="secundary_todoCheck28">
                      <label for="secundary_todoCheck28"></label>
                    </div>
                     <span class="text" id="secundary_text28">Amortiguador</span>
                    <div class="tools">
                    <label for="secundary_image28" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="secundary_image28" type="file" style="display: none;" accept="image/*"  name="secundary_image28">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck28').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image28').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck28').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text28').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                  <div class="icheck-primary d-inline ml-2">
                  <input type="checkbox" id="secundary_todoCheck29"> <!-- Checkbox habilitado para envío -->
                  <label for="secundary_todoCheck29"></label>
                  </div>
                  <span class="text" id="secundary_text29">Otros</span>
                  <div class="tools">
                  <label for="secundary_image29" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                  <input id="secundary_image29" type="file" style="display: none;" accept="image/*"  name="secundary_image29">
                  </div>
                  </li>

<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('secundary_todoCheck29').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('secundary_image29').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('secundary_todoCheck29').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('secundary_text29').style.textDecoration = 'line-through';
    }
  });
</script>
                  
                  
                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="secundary_comment6"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /.card -->
        </div>
  
 </div> 
                     
                     
                     <div class="row">
                 
                <div class="col-md-12 col-12 my-2">
               
                     <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $_GET['id'];?>">
                     <input style="background-color:#333;" type="hidden" name="firma" value="<?php echo $delivery->firma;?>">
                   <button id="draw-submitBtn" class="btn btn-success btn-block btn-sm "><i class="fa fa-check"></i> Entregar</button>
                 
                </div>
                </div>
          </div> 
  
  </div>

                      </div>
                    </div>
                  </div>

              </div>               
</form>
<script type="text/javascript">
  $(document).ready(function(){
  //Validación del formulario al ser llenado
  $("input[type=number], select").change(function(){
    $("button[type=submit]").prop('disabled', false).attr('title',false);
  });
});
</script>

  
                     
                    </div>
                  </div>
                  <!-- /.tab-pane -->

                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="extenddate"): 

$user = BookingData::getById($_GET["id"]);?>

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
           <div class="col-sm-6">
         <h1 class="m-0"><i class='fa fa-random'></i> <?php  switch (Core::$user->language){  case 'ES': echo "Extender Fecha"; break;  case 'EN': echo "Extend Date"; break; } ?></h1>
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
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
           <div class="card card-warning card-outline" style="background-color:#222;">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="CF-SYSTEMS/storage/profiles/user.png"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?php echo $user->getPerson()->name." ".$user->getPerson->lastname;?></h3>

                <p class="text-muted text-center"><?php echo strtoupper($user->getPerson()->address);?></p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item" style="background-color:#222;">
                    <b>CEDULA:</b> <a class="float-right"><?php echo $user->getPerson()->no;?></a>
                  </li>
                  <li class="list-group-item" style="background-color:#222;">
                    <b>PASAPORTE:</b> <a class="float-right"><?php echo $user->getPerson()->passport;?></a>
                  </li>
                  <li class="list-group-item" style="background-color:#222;">
                     <b>TEL:</b> <a class="float-right"><?php echo $user->getPerson()->phone;?></a>
                  </li>
                </ul>

               
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

      
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card" style="background-color:#222;">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Cambiar Fecha</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                      
   <form class="form-horizontal" method="post" id="updrandom" role="form" enctype="multipart/form-data">                  
                    <div class="row">
                        
                   <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha a Entregar</label>
      <input style="background-color:#333;" readonly type="datetime-local" value="<?php echo $user->start_at;?>" required name="start_at" id="start_at" class="form-control " >
    </div>


     <div class="col-md-4 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha de Devolucion</label>
      <input style="background-color:#333;" type="datetime-local" value="<?php echo $user->end_at;?>" required name="end_at" id="end_at" class="form-control " >
        </div>

    <div class="col-md-4 col-12" hidden>
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Ubicacion</label>
      <?php $clients = LocationData::getAll();?>
    <select style="background-color:#333;" name="location" required class="form-control" id="location" >
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
  
    <div class="col-md-4 col-12" hidden>
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Rent Car</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-street-view"></i></span>
    <select style="background-color:#333;" name="stock_id" required id="select2lista"  class="form-control"></select>
    </div>
  </div>
 
 <div hidden class="col-md-4 col-12">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Divisa</label>
    <select style="background-color:#333;" name="divisa_id" id="divisa_id" class="form-control">
     <option value="1">DOLAR</option>
     </select></div>
  </div>
  
     <div class="col-md-4 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Dias de Renta</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-list-ol"></i></span>
     <select style="background-color:#333;" name="day" id="dias"  class="form-control"></select>
    </div>
</div>

<div class="col-md-4 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Precio X Dia:</label>
    <input style="background-color:#333;" type="number" name="price" value="<?php echo $user->price;?>"  id="tariff2"  class="form-control" placeholder="Precio en Dolar" min="0" step="0.01">
    </div>
    </div>
    
      <div class="col-md-4 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Reserva</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <select style="background-color:#333;" name="total" id="amount"  class="form-control"></select>
    </div>
  </div>
  
   <div hidden class="col-md-2 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Extra</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
     <select style="background-color:#333;" name="xtotal" id="xmount"  class="form-control"></select>
    </div>
  </div>

  
   <div class="col-md-6 col-12">   
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Anterior</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
      <input style="background-color:#333;" type="number" required value="<?php echo $user->total;?>" required name="payment" id="payment" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Aqui"; break;
 case 'EN': echo "Write Here"; break;
}
?>" min="0" step="0.01">
    </div>
  </div>

  <div class="col-md-6 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Monto Agregar</label>
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-minus-square"></i></span>
     <input style="background-color:#333;" readonly id="remaining" name="remaining"  class="form-control">
    </div>
  </div>
    
    
    <script type="text/javascript">

  $(document).ready(function(){
    $('#location').val();
    recargarLista();

    $('#location').change(function(){
      recargarLista();
    });
  })

  function recargarLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=edit",
      data:"location=" + <?php echo $user->car_id;?>,
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }


  $(document).ready(function(){
    $('#select2lista').val();
    recargar2Lista();
     recargar4Lista();

    $('#select2lista').change(function(){
      recargar2Lista();
       recargar4Lista();
    });
  })

  function recargar2Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=editcars",
      data:"car=" + <?php if($user->car2_id>0): echo $user->car2_id; else: echo $user->car_id; endif;?>,
      success:function(r){
        $('#cars').html(r);
      }
    });
  }
  
  function recargar4Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=editcar2s",
      data:"car=" + <?php if($user->car2_id>0): echo $user->car2_id; else: echo $user->car_id; endif;?>,
      success:function(r){
        $('#car2s').html(r);
      }
    });
  }


  $(document).ready(function(){
    $('#cars').val();
    recargar3Lista();

    $('#cars').change(function(){
      recargar3Lista();
    });
  })

  function recargar3Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff",
      data:"cars=" + $('#cars').val(),
      success:function(r){
        $('#tariff').html(r);
      }
    });
  }

    $('#tariff').change(function(){
      Lista();

    });

$("#payment").keyup(function(){
function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 

$("#remaining").val( agregarSeparadorMiles(parseFloat($("#amount").val())-parseFloat($("#payment").val())));
 });

function Lista(){
function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni),
      hasta = new Date(fechaFin),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return  Math.round((diff / diaEnMils));
}

 document.getElementById('dias').innerText = calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  );

 var vprice = document.getElementById('day').innerText = calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  );
  
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=amount",
       data: {paycar: $('#payment').val(),tcars: $('#tariff').val(),dvs: $('#divisa_id').val(),cars: $('#cars').val(), price:vprice},
      success:function(r){
        $('#amount').html(r);
      }
    });

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price",
       data: {cars: $('#cars').val(), price: vprice},
      success:function(r){
        $('#dias').html(r);
      }
    });
$("#remaining").val((($('#tariff').val()*vprice)*$('#divisa_id').val())-$('#payment').val());}



    tariff2.addEventListener("keyup", function()
    {
        function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni),
      hasta = new Date(fechaFin),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return  Math.round((diff / diaEnMils));
}

 document.getElementById('dias').innerText = calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  );

 var vprice = document.getElementById('day').innerText = calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  );


    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff2",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: vprice},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: vprice},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val((($('#tariff2').val()*vprice)*$('#divisa_id').val())-$('#payment').val());
    }, false);
    

end_at.addEventListener("change", function()
    {
        function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni),
      hasta = new Date(fechaFin),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return  Math.round((diff / diaEnMils));
}

 document.getElementById('dias').innerText = calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  );

 var vprice = document.getElementById('day').innerText = calcularDiasAusencia(
    document.getElementById('start_at').value,
    document.getElementById('end_at').value
  );


    $.ajax({
      type:"POST",
      url:"./?action=get&opt=tariff2",
       data: {tcars: $('#tariff2').val(),dvs: $('#divisa_id').val(), price: vprice},
      success:function(r){
        $('#amount').html(r);
      }
    });
    

    $.ajax({
      type:"POST",
      url:"./?action=get&opt=price2",
       data: {price: vprice},
      success:function(r){
        $('#dias').html(r);
      }
    });

function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
$("#remaining").val((($('#tariff2').val()*vprice)*$('#divisa_id').val())-$('#payment').val());
    }, false);

</script>
 <div hidden id="day"></div>

    
 
                <div class="col-md-12 col-12 my-2">
               
                     <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $_GET['id'];?>">
                   <button type="submit" id="btnsubmit" disabled  class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 </div> 
                </div>
</form>
 
 <script>

$('input[id=end_at]').on('change', () => {
  //Obtenemos el valor
  var input2 = $('#end_at').val();
  //Validamos que el valor sea mayor a 7 caracteres
  if (input2.length >= 2) {
    //Habiiltamos el botón quitando la clase 'disabled' y la propiedad 'disabled'
    $('#btnsubmit').removeClass('disabled').removeAttr('disabled');
  } else {
    //Volvemos a deshabilitar el botón, adicionando nuevamente la clase y propiedad 'disabled'
    $('#btnsubmit').addClass('disabled').prop('disabled', true);
  }
});

			$('#btnsubmit').on('click', function(e){
  e.preventDefault(); // Para evitar se dispare el submit


  $(this).prop('disabled', true); // Desactivas el botón
  $(this).submit(); // Disparas el submit una vez desactivado el botón
});


// Esto solo para mostrar que el submit no se envía mas de una vez
$('form').on('submit', function(){
  console.log('send'); 
});


 
            jQuery(document).ready(function(){
            jQuery("#updrandom").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
            
                $.ajax({
                  type: "POST",
                  url: "./?action=contract&opt=updrandom",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Fecha Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=contract&opt=extend'  }, delay); 
                     
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
                  <!-- /.tab-pane aqui -->

                     
                    </div>
                  </div>
                  <!-- /.tab-pane -->

                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="received"):
$user = CarsData::getById($_GET["cars"]); $book = BookingData::getById($_GET["id"]); $delivery = DeliveryData::getBySell(0,2,$_GET["id"]);?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0"><i class='fa fa-car'></i> Vehiculo de Devolucion</h1>
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

      
         
                  <div class="card" style="background-color:#222;">
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <div class="card-body">
                        <label>Datos del Vehiculo: </label>


     <div class="row">
         
    <div class="col-md-6 col-12">
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <div class="card-body">
                        <label>Rent a Car: </label>
                        <?php echo $user->getStock()->name;?>
                        <br>
                        <label>Nombre del Vehiculo: </label>
                        <?php echo $user->getBrand()->name;?><br>
                        <label>Modelo: </label>
                        <?php echo $user->name;?>
                        <br>
                        <label>Año del Modelo: </label>
                        <?php echo $user->year;?>
                        <br>
                        <label>Categoria: </label>
                        <?php echo $user->getCategory()->name;?>
                        <br>
                        <label>Color Interior: </label>
                        <?php echo $user->getInColor()->name;?> 
                        <br>
                        <label>Color Exterior: </label>
                        <?php echo $user->getExColor()->name;?>
                        <br>
                        <label>Ficha: </label>
                        <?php echo $user->token;?>
                        <br>
                        <label>Seguro de Ley: </label>
                        <?php echo $user->insurance_id;?>
                        <br>
                        <label>Vencimiento del Seguro [LEY]: </label>
                        <?php echo  date("d-m-Y",strtotime($user->date_insurance));?>
                        <br>
                        <label>Seguro Full: </label>
                        <?php echo $user->insurance2_id;?>
                        <br>
                        <label>Vencimiento del Seguro [FULL]: </label>
                        <?php echo  date("d-m-Y",strtotime($user->date2_insurance));?>
                        <br>
                        <label>Fecha Entrega: </label>
                         <?php echo  date("d-m-Y h:i:s a",strtotime($book->start_at));?>
                        <br>
                        <label>Fechde Devolucion: </label>
                        <?php echo  date("d-m-Y h:i:s a",strtotime($book->end_at));?>
                      </div>
                    </div>

</div>

    <div class="col-md-6 col-12">
   
      
      <div class="card-header">
                      <center>
                        <label>Foto del Vehiculo: </label>
                         
                        <?php if ($user->invoice_file!=""):?>
  <a href="./../CF-SYSTEMS/storage/invoice_files/<?php echo $user->invoice_file;?>"  class="btn btn-default"><i class="fa fa-file"></i> Foto del Seguro (<?php echo $user->invoice_file; ?>)</a>
                         <?php endif;?>
                      </center>
                   </div>     
                    <div class="card-header">
                      <center>
                        <label>Seguro de Ley: </label>
                         
                        <?php if ($user->insurance_file!=""):?>
  <a href="./../CF-SYSTEMS/storage/invoice_files/<?php echo $user->insurance_file;?>"  class="btn btn-default"><i class="fa fa-file"></i> Foto del Seguro (<?php echo $user->insurance_file; ?>)</a>
                         <?php endif;?>
                      </center>
                   </div>


             <div class="card-header">
                      <center>
                        <label>Seguro de Full: </label>
                         
                        <?php if ($user->insurance2_file!=""):?>
  <a href="./../CF-SYSTEMS/storage/invoice_files/<?php echo $user->insurance2_file;?>"  class="btn btn-default"><i class="fa fa-file"></i> Foto del Seguro (<?php echo $user->insurance2_file; ?>)</a>
                         <?php endif;?>
                      </center>
                   </div>  
                   


<style>
/* Contenedor para las imágenes */
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  top: 2%;
  width: 100%; /* Establece el ancho y alto según las dimensiones de la imagen de fondo */
  height: 20%;
}

/* Estilo para la imagen de fondo */
.background-image {
  position: absolute;
  z-index: 1;
  width: 100%;
  height: 100%;
}

/* Estilo para la imagen superior */
.overlay-image {
  z-index: 2;
  width: 100%;
  height: 100%;
}
</style>      


                      <center>
                    
<?php 
$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT danger from delivery where random=0 and method=2 and booking_id=".$_GET["id"];
$query = $con->query($sql);

while ($row = $query->fetch_array()) {
    if (!empty($row['danger'])) {
        $imagenes = explode("|", $row['danger']); // Convertir la cadena en array
$i = 1;
foreach ($imagenes as $img) {
            $imgPath = trim($img); 
   echo "   <label>Daños del Vehiculo: #$i</label><br>
   <a href='danger/$imgPath'  class='btn btn-default'><i class='fa fa-image'></i> Visualizar  Foto</a><br>" ; 
   $i++;
}
}
}
?>
                      </center>
      
    </div>

 
         
                  <div class="card" style="background-color:#222;">
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <div class="card-body">
                        <label>Revision del Vehiculo: </label>


    <form class="form-horizontal" method="post" id="received" action="./?action=contract&opt=received" role="form" enctype="multipart/form-data"> 
     <div class="row">
    <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="cat" id="checkboxPrimary1" checked>
<label for="checkboxPrimary1">
GATO
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="radio" id="checkboxPrimary2"  checked>
                        <label for="checkboxPrimary2">
                          RADIO
                        </label>
                      </div>
    </div>

    <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox"  name="replacement" id="checkboxPrimary3"  checked>
<label for="checkboxPrimary3">
REPUESTO
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="antenna" id="checkboxPrimary4"  checked>
                        <label for="checkboxPrimary4">
                          ANTENA
                        </label>
                      </div>
    </div>


    <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="keyring" id="checkboxPrimary5"  checked>
<label for="checkboxPrimary5">
LLAVERO
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="carpets" id="checkboxPrimary6"  checked>
                        <label for="checkboxPrimary6">
                          ALFOMBRAS
                        </label>
                      </div>
    </div>

   <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="belts" id="checkboxPrimary7"  checked>
<label for="checkboxPrimary7">
CINTURONES
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="roof_lining" id="checkboxPrimary8"  checked>
                        <label for="checkboxPrimary8">
                         FORRO TECHO
                        </label>
                      </div>
    </div>    


   <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="mirrors" id="checkboxPrimary9"  checked>
<label for="checkboxPrimary9">
ESPEJOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="board" id="checkboxPrimary10"  checked>
                        <label for="checkboxPrimary10">
                         TABLERO
                        </label>
                      </div>
    </div>  

       <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="document" id="checkboxPrimary11"  checked>
<label for="checkboxPrimary11">
DOCUMENTOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="watches" id="checkboxPrimary12"  checked>
                        <label for="checkboxPrimary12">
                         RELOJES
                        </label>
                      </div>
    </div>  


       <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="rearview" id="checkboxPrimary13"  checked>
<label for="checkboxPrimary13">
RETREVISOR
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="lighter" id="checkboxPrimary14" checked>
                        <label for="checkboxPrimary14">
                         ENCENDEDOR
                        </label>
                      </div>
    </div>  

           <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="crystals" id="checkboxPrimary15" checked>
<label for="checkboxPrimary15">
CRISTALES
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="cd" id="checkboxPrimary16" checked>
                        <label for="checkboxPrimary16">
                         CD CHANGER
                        </label>
                      </div>
    </div>  


           <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="bumper" id="checkboxPrimary17" checked>
<label for="checkboxPrimary17">
TAPA COV. BUMPER
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="equalizer" id="checkboxPrimary18" checked>
                        <label for="checkboxPrimary18">
                         ECUALIZADOR
                        </label>
                      </div>
    </div>  

               <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="cup_holder" id="checkboxPrimary19" checked>
<label for="checkboxPrimary19">
PORTA VASOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="plate" id="checkboxPrimary20" checked>
                        <label for="checkboxPrimary20">
                         PLACA
                        </label>
                      </div>
    </div>  

 

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="seats" id="checkboxPrimary21" checked>
                        <label for="checkboxPrimary21">
                         ASIENTOS
                        </label>
                      </div>
    </div>  

               <div class="col-md-6 col-6">
<div class="icheck-warning d-inline">
<input style="background-color:#333;" type="checkbox" name="logo" id="checkboxPrimary22" checked>
<label for="checkboxPrimary22">
LOGOS
</label>
</div>
</div>

    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="batery" id="checkboxPrimary23" checked>
                        <label for="checkboxPrimary23">
                        BATERIA
                        </label>
                      </div>
    </div> 



    <div class="col-md-6 col-6">
                      <div class="icheck-warning d-inline">
                        <input style="background-color:#333;" type="checkbox" name="top" id="checkboxPrimary24" checked>
                        <label for="checkboxPrimary24">
TAPA COMBUSTIBLE
                   
                        </label>
                      </div>
    </div> 

     
    <div class="col-md-12 col-12 my-2"> 
    <div class="row">

    <div class="col-md-6 col-12 my-2"> 
      <input style="background-color:#333;" type="number" required name="kms" class="form-control"  placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir kilometraje"; break;
 case 'EN': echo "Write mileage"; break;
}
?>" autocomplete="off" required>
</div>
<div class="col-md-6 col-12 my-2">
    <div class="input-group" >
    <span class="input-group-text autocomplete" style="background-color:orange;"><i class="fa fa-tint"></i></span>
     <select style="background-color:#333;" name="fuel"  class="form-control">
      <option value="R">Reserva</option>
      <option value="1/4">1/4</option>
      <option value="1/2">Medio</option>
      <option value="3/4">3/4</option>
      <option value="F">Full</option>
     </select>
    </div>
  </div>
</div>
</div>


 <div  class="card-header col-md-12 col-12 my-2" style="background-color:gray;">
<i class="fa fa-car"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Daños del Vehiculo"; break;
 case 'EN': echo "Vehicle Damage"; break;
}
?>:
</div> 
<style>
    
#vert-tabs-frontal-tab.nav-link{
    color: orange !important;
}
#vert-tabs-lateral-izquierdo-tab.nav-link{
    color: orange !important;
}
#vert-tabs-lateral-derecho-tab.nav-link{
    color: orange !important;
}
#vert-tabs-trasera-tab.nav-link{
    color: orange !important;
}
#vert-tabs-superior-tab.nav-link{
    color: orange !important;
}
#vert-tabs-inferior-tab.nav-link{
    color: orange !important;
}
</style>

        <div class="card card card-outline"  style="background-color:#222;">
         
          <div class="card-body">
         <div class="nav-wrapper">
  <ul class="nav nav-tabs d-flex flex-nowrap" id="custom-content-above-tab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="vert-tabs-frontal-tab" data-toggle="pill" href="#vert-tabs-frontal" role="tab" aria-controls="vert-tabs-frontal" aria-selected="true">FRONTAL</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-lateral-izquierdo-tab" data-toggle="pill" href="#vert-tabs-lateral-izquierdo" role="tab" aria-controls="vert-tabs-lateral-izquierdo" aria-selected="false">LATERAL IZQUIERDO</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-lateral-derecho-tab" data-toggle="pill" href="#vert-tabs-lateral-derecho" role="tab" aria-controls="vert-tabs-lateral-derecho" aria-selected="false">LATERAL DERECHO</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-trasera-tab" data-toggle="pill" href="#vert-tabs-trasera" role="tab" aria-controls="vert-tabs-trasera" aria-selected="false">TRASERA</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-superior-tab" data-toggle="pill" href="#vert-tabs-superior" role="tab" aria-controls="vert-tabs-superior" aria-selected="false">SUPERIOR</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="vert-tabs-inferior-tab" data-toggle="pill" href="#vert-tabs-inferior" role="tab" aria-controls="vert-tabs-inferior" aria-selected="false">INFERIOR</a>
    </li>
  </ul>
</div>

<style>
  .nav-wrapper {
    overflow-x: auto;  /* Permite el scroll horizontal */
    overflow-y: hidden; /* Evita el scroll vertical */
    white-space: nowrap;
  }
  .nav-tabs {
    flex-wrap: nowrap;
  }
</style>

            <div class="tab-custom-content">
              <p class="lead mb-0">  <?php 
switch (Core::$user->language){
 case 'ES': echo "SECCIONES"; break;
 case 'EN': echo "SECTIONS"; break;
}
?></p>
            </div>
            <div class="row">
          
              <div class="col-12 col-md-12">
                <div class="tab-content" id="vert-tabs-tabContent">
                  <div class="tab-pane text-left fade show active" id="vert-tabs-frontal" role="tabpanel" aria-labelledby="vert-tabs-frontal-tab">
                     
            <!-- TO DO List -->
            <div  style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck1">
                      <label for="todoCheck1"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text1">Capó</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image1" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image1" type="file" style="display: none;" accept="image/*"  name="image1">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck1').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image1').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck1').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text1').style.textDecoration = 'line-through';
    }
  });
</script>
                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck2">
                      <label for="todoCheck2"></label>
                    </div>
                     <span class="text" id="text2">Parachoques</span>
                    <div class="tools">
                      <label for="image2" class="custom-file-upload"><i class="fa fa-upload"></i></label> 
                      <input id="image2" type="file" style="display: none;" accept="image/*"  name="image2">
                    </div>
                  </li>
                 
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck2').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image2').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck2').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text2').style.textDecoration = 'line-through';
    }
  });
</script>
                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck3">
                      <label for="todoCheck3"></label>
                    </div>
                     <span class="text" id="text3">Faros</span>
                    <div class="tools">
                     <label for="image3" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image3" type="file" style="display: none;" accept="image/*"  name="image3">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck3').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image3').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck3').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text3').style.textDecoration = 'line-through';
    }
  });
</script>
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck4">
                      <label for="todoCheck4"></label>
                    </div>
                     <span class="text" id="text4">Parrilla</span>
                    <div class="tools">
                     <label for="image4" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image4" type="file" style="display: none;" accept="image/*"  name="image4">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck4').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image4').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck4').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text4').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck5">
                      <label for="todoCheck5"></label>
                    </div>
                     <span class="text" id="text5">Parabrisas</span>
                    <div class="tools">
                     <label for="image5" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image5" type="file" style="display: none;" accept="image/*"  name="image5">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck3').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image3').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck3').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text3').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck6">
                      <label for="todoCheck6"></label>
                    </div>
                     <span class="text" id="text6">Forlay</span>
                    <div class="tools">
                     <label for="image6" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image6" type="file" style="display: none;" accept="image/*"  name="image6">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck6').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image6').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck6').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text6').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                 <input style="background-color:#222;" autocomplete="off" name="comment1"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-lateral-izquierdo" role="tabpanel" aria-labelledby="vert-tabs-lateral-izquierdo-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
            
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck7">
                      <label for="todoCheck7"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text7">Puertas</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image7" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image7" type="file" style="display: none;" accept="image/*"  name="image7">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck7').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image7').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck7').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text7').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck8">
                      <label for="todoCheck8"></label>
                    </div>
                     <span class="text" id="text8">Guardafangos</span>
                    <div class="tools">
                     <label for="image8" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image8" type="file" style="display: none;" accept="image/*"  name="image8">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck8').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image8').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck8').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text8').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck9">
                      <label for="todoCheck9"></label>
                    </div>
                     <span class="text" id="text9">Espejos retrovisores</span>
                    <div class="tools">
                     <label for="image9" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image9" type="file" style="display: none;" accept="image/*"  name="image9">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck9').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image9').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck9').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text9').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck10">
                      <label for="todoCheck10"></label>
                    </div>
                     <span class="text" id="text10">Ventanas laterales</span>
                    <div class="tools">
                     <label for="image10" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image10" type="file" style="display: none;" accept="image/*"  name="image10">
                    </div>
                  </li>
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck10').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image10').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck10').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text10').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck11">
                      <label for="todoCheck11"></label>
                    </div>
                     <span class="text" id="text11">Manijas de las puertas</span>
                    <div class="tools">
                     <label for="image11" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image11" type="file" style="display: none;" accept="image/*"  name="image11">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck11').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image11').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck11').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text11').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck12">
                      <label for="todoCheck12"></label>
                    </div>
                     <span class="text" id="text12">Llantas y rines</span>
                    <div class="tools">
                     <label for="image12" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image12" type="file" style="display: none;" accept="image/*"  name="image12">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck12').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image12').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck12').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text12').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment2"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-lateral-derecho" role="tabpanel" aria-labelledby="vert-tabs-lateral-derecho-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
              
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
             
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck13">
                      <label for="todoCheck13"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text13">Puertas</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                     <label for="image13" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image13" type="file" style="display: none;" accept="image/*"  name="image13">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck13').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image13').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck13').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text13').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck14">
                      <label for="todoCheck14"></label>
                    </div>
                     <span class="text" id="text14">Guardafangos </span>
                    <div class="tools">
                   <label for="image14" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                   <input id="image14" type="file" style="display: none;" accept="image/*"  name="image14">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck14').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image14').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck14').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text14').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck15">
                      <label for="todoCheck15"></label>
                    </div>
                     <span class="text" id="text15">Espejos retrovisores</span>
                    <div class="tools">
                     <label for="image15" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image15" type="file" style="display: none;" accept="image/*"  name="image15">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck15').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image15').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck15').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text15').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck16">
                      <label for="todoCheck16"></label>
                    </div>
                     <span class="text" id="text16">Ventanas laterales</span>
                    <div class="tools">
                    <label for="image16" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                    <input id="image16" type="file" style="display: none;" accept="image/*"  name="image16">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck16').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image16').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck16').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text16').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck17">
                      <label for="todoCheck17"></label>
                    </div>
                     <span class="text" id="text17">Manijas de las puertas</span>
                    <div class="tools">
                     <label for="image17" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image17" type="file" style="display: none;" accept="image/*"  name="image17">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck17').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image17').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck17').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text17').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck18">
                      <label for="todoCheck18"></label>
                    </div>
                     <span class="text" id="text18">Llantas y rines</span>
                    <div class="tools">
                    <label for="image18" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image18" type="file" style="display: none;" accept="image/*"  name="image18">
                    </div>
                  </li>
                  
                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck18').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image18').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck18').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text18').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment3"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>     
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                  <div class="tab-pane fade" id="vert-tabs-trasera" role="tabpanel" aria-labelledby="vert-tabs-trasera-tab">
                        
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
              
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck19">
                      <label for="todoCheck19"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text19">Parachoques</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                    <label for="image19" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image19" type="file" style="display: none;" accept="image/*"  name="image19">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck19').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image19').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck19').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text19').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck20">
                      <label for="todoCheck20"></label>
                    </div>
                     <span class="text" id="text20">Compuerta</span>
                    <div class="tools">
                     <label for="image20" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image20" type="file" style="display: none;" accept="image/*"  name="image20">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck20').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image20').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck20').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text20').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck21">
                      <label for="todoCheck21"></label>
                    </div>
                     <span class="text" id="text21">Faros</span>
                    <div class="tools">
                     <label for="image21" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                     <input id="image21" type="file" style="display: none;" accept="image/*"  name="image21">
                    </div>
                  </li>

                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck21').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image21').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck21').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text21').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck22">
                      <label for="todoCheck22"></label>
                    </div>
                     <span class="text" id="text22">Escape</span>
                    <div class="tools">
                    <label for="image22" class="custom-file-upload"><i class="fa fa-upload"></i></label>                       
                    <input id="image22" type="file" style="display: none;" accept="image/*"  name="image22">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck22').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image22').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck22').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text22').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                  
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck23">
                      <label for="todoCheck23"></label>
                    </div>
                     <span class="text" id="text23">Vidrio trasero</span>
                    <div class="tools">
                   <label for="image23" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                   <input id="image23" type="file" style="display: none;" accept="image/*"  name="image23">
                    </div>
                  </li>                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck23').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image23').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck23').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text23').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment4"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                    <div class="tab-pane fade" id="vert-tabs-superior" role="tabpanel" aria-labelledby="vert-tabs-superior-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck24">
                      <label for="todoCheck24"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text24">Techo</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                    <label for="image24" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image24" type="file" style="display: none;" accept="image/*"  name="image24">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck24').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image24').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck24').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text24').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck25">
                      <label for="todoCheck25"></label>
                    </div>
                     <span class="text" id="text25">Antena</span>
                    <div class="tools">
                   <label for="image25" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                   <input id="image25" type="file" style="display: none;" accept="image/*"  name="image25">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck25').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image25').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck25').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text25').style.textDecoration = 'line-through';
    }
  });
</script>

                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment5"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                  
            <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->      
                    <div class="tab-pane fade" id="vert-tabs-inferior" role="tabpanel" aria-labelledby="vert-tabs-inferior-tab">
                      
            <!-- TO DO List -->
            <div class="card" style="background-color:#222;">
             
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list" >
                  <li style="background-color:#222;">
                    <!-- drag handle -->
                  
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck26">
                      <label for="todoCheck26"></label>
                    </div>
                    <!-- todo text -->
                     <span class="text" id="text26">Chasis</span>
                    <!-- Emphasis label -->
                   
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                  <label for="image26" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                  <input id="image26" type="file" style="display: none;" accept="image/*"  name="image26">
                    </div>
                  </li>
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck26').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image26').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck26').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text26').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                   
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox"  id="todoCheck27">
                      <label for="todoCheck27"></label>
                    </div>
                     <span class="text" id="text27">Suspensión</span>
                    <div class="tools">
                    <label for="image27" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image27" type="file" style="display: none;" accept="image/*"  name="image27">
                    </div>
                  </li>                  
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck27').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image27').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck27').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text27').style.textDecoration = 'line-through';
    }
  });
</script>

                  <li style="background-color:#222;">
                    
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck28">
                      <label for="todoCheck28"></label>
                    </div>
                     <span class="text" id="text28">Amortiguador</span>
                    <div class="tools">
                    <label for="image28" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                    <input id="image28" type="file" style="display: none;" accept="image/*"  name="image28">
                    </div>
                  </li>
                  
                                    
                  
<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck28').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image28').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck28').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text28').style.textDecoration = 'line-through';
    }
  });
</script>

                  
                  <li style="background-color:#222;">
                  <div class="icheck-primary d-inline ml-2">
                  <input type="checkbox" id="todoCheck29"> <!-- Checkbox habilitado para envío -->
                  <label for="todoCheck29"></label>
                  </div>
                  <span class="text" id="text29">Otros</span>
                  <div class="tools">
                  <label for="image29" class="custom-file-upload"><i class="fa fa-upload"></i></label>                        
                  <input id="image29" type="file" style="display: none;" accept="image/*"  name="image29">
                  </div>
                  </li>

<script>
  // Evitar que el usuario haga clic directamente en el checkbox
  document.getElementById('todoCheck29').addEventListener('click', function(e) {
    e.preventDefault();  // Desactivar la acción del clic
  });

  // Activar el checkbox solo cuando se sube una imagen
  document.getElementById('image29').addEventListener('change', function() {
    // Verificar si el archivo fue seleccionado
    if (this.files && this.files[0]) {
      // Activar el checkbox
      document.getElementById('todoCheck29').checked = true;

      // Agregar la raya al texto "Otros"
      document.getElementById('text29').style.textDecoration = 'line-through';
    }
  });
</script>
                  
                  
                </ul>
                    <input style="background-color:#222;" autocomplete="off" name="comment6"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Observaciones del Vehiculo"; break;
 case 'EN': echo "Vehicle Observations"; break;
}
?>">
              </div>
            </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /.card -->
        </div>
  
 </div> 
  
   <div  class="card-header col-md-12 col-12 my-2" style="background-color:gray;">
<i class="fa fa-edit"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Firma del Cliente"; break;
 case 'EN': echo "Client Signature"; break;
}
?>:
</div> 
    <div class="contenedor">

    <div class="row">
      <div class="col-md-12">
        <canvas id="draw-canvas" width="340" height="200">
          No tienes un buen navegador.
        </canvas>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
          
      
        <input  type="button" class="button btn-danger" id="draw-clearBtn" value="<?php 
switch (Core::$user->language){
 case 'ES': echo "Borrar Firma"; break;
 case 'EN': echo "Delete Signature"; break;
}
?>"></input>
     


            <label>Color</label>
            <input style="background-color:#333;" type="color" id="color">
            <input style="background-color:#333;" hidden type="range" id="puntero" min="1" default="1" max="5" width="10%">


   
  
    <div hidden class="row">
      <div class="col-md-12">
        <textarea style="background-color:#333;"  id="draw-dataUrl" class="form-control" name="base64" rows="5"></textarea>
      </div>
    </div>
  
  
  </div>
   
                     <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $_GET['id'];?>">
                 
                 
             

 
                
                </div>
                   <div class="col-md-12 col-12 my-2">

                   <button type="submit" id="draw-submitBtn"  class="btn btn-success btn-block btn-sm "><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Recibir"; break;
 case 'EN': echo "Receive"; break;
}
?></button>
                   
            </div>
    </div>        
  
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
  </div>
</div>
         
<style>

section{
    flex:1;
}



.button {
    background: #3071a9;
    box-shadow: inset 0 -3px 0 rgba(0,0,0,.3);
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 5px;
    margin: 0 15px;
    text-decoration: none;
    color: white;
}

.button:active {
    transform: scale(0.9);
}

.contenedor {
    width: 100%
    margin: 5px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

input[type=range] {
  -webkit-appearance: none;
  margin: 18px 0;

}
input[type=range]:focus {
  outline: none;
}
input[type=range]::-webkit-slider-runnable-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-webkit-slider-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
  -webkit-appearance: none;
  margin-top: -14px;
}
input[type=range]:focus::-webkit-slider-runnable-track {
  background: #367ebd;
}
input[type=range]::-moz-range-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-moz-range-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]::-ms-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  background: transparent;
  border-color: transparent;
  border-width: 16px 0;
  color: transparent;
}
input[type=range]::-ms-fill-lower {
  background: #2a6495;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-fill-upper {
  background: #3071a9;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]:focus::-ms-fill-lower {
  background: #3071a9;
}
input[type=range]:focus::-ms-fill-upper {
  background: #367ebd;
}
</style>
<script>
/*
    El siguiente codigo en JS Contiene mucho codigo
    de las siguietes 3 fuentes:
    https://stipaltamar.github.io/dibujoCanvas/
    https://developer.mozilla.org/samples/domref/touchevents.html - https://developer.mozilla.org/es/docs/DOM/Touch_events
    http://bencentra.com/canvas/signature/signature.html - https://bencentra.com/code/2014/12/05/html5-canvas-touch-events.html
*/

(function() { // Comenzamos una funcion auto-ejecutable

  // Obtenenemos un intervalo regular(Tiempo) en la pamtalla
  window.requestAnimFrame = (function (callback) {
    return window.requestAnimationFrame ||
          window.webkitRequestAnimationFrame ||
          window.mozRequestAnimationFrame ||
          window.oRequestAnimationFrame ||
          window.msRequestAnimaitonFrame ||
          function (callback) {
            window.setTimeout(callback, 1000/60);
            // Retrasa la ejecucion de la funcion para mejorar la experiencia
          };
  })();

  // Traemos el canvas mediante el id del elemento html
  var canvas = document.getElementById("draw-canvas");
  var ctx = canvas.getContext("2d");


  // Mandamos llamar a los Elemetos interactivos de la Interfaz HTML
  var drawText = document.getElementById("draw-dataUrl");
  var drawImage = document.getElementById("draw-image");
  var clearBtn = document.getElementById("draw-clearBtn");
  var submitBtn = document.getElementById("draw-submitBtn");
  clearBtn.addEventListener("click", function (e) {
    // Definimos que pasa cuando el boton draw-clearBtn es pulsado
    clearCanvas();
    drawImage.setAttribute("src", "");
  }, false);
    // Definimos que pasa cuando el boton draw-submitBtn es pulsado
  submitBtn.addEventListener("click", function (e) {
  var dataUrl = canvas.toDataURL();
  drawText.innerHTML = dataUrl;
  drawImage.setAttribute("src", dataUrl);
   }, false);

  // Activamos MouseEvent para nuestra pagina
  var drawing = false;
  var mousePos = { x:0, y:0 };
  var lastPos = mousePos;
  canvas.addEventListener("mousedown", function (e)
  {
    /*
      Mas alla de solo llamar a una funcion, usamos function (e){...}
      para mas versatilidad cuando ocurre un evento
    */
    var tint = document.getElementById("color");
    var punta = document.getElementById("puntero");
    console.log(e);
    drawing = true;
    lastPos = getMousePos(canvas, e);
  }, false);
  canvas.addEventListener("mouseup", function (e)
  {
    drawing = false;
  }, false);
  canvas.addEventListener("mousemove", function (e)
  {
    mousePos = getMousePos(canvas, e);
  }, false);

  // Activamos touchEvent para nuestra pagina
  canvas.addEventListener("touchstart", function (e) {
    mousePos = getTouchPos(canvas, e);
    console.log(mousePos);
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var touch = e.touches[0];
    var mouseEvent = new MouseEvent("mousedown", {
      clientX: touch.clientX,
      clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchend", function (e) {
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var mouseEvent = new MouseEvent("mouseup", {});
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchleave", function (e) {
    // Realiza el mismo proceso que touchend en caso de que el dedo se deslice fuera del canvas
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var mouseEvent = new MouseEvent("mouseup", {});
    canvas.dispatchEvent(mouseEvent);
  }, false);
  canvas.addEventListener("touchmove", function (e) {
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var touch = e.touches[0];
    var mouseEvent = new MouseEvent("mousemove", {
      clientX: touch.clientX,
      clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
  }, false);

  // Get the position of the mouse relative to the canvas
  function getMousePos(canvasDom, mouseEvent) {
    var rect = canvasDom.getBoundingClientRect();
    /*
      Devuelve el tamaño de un elemento y su posición relativa respecto
      a la ventana de visualización (viewport).
    */
    return {
      x: mouseEvent.clientX - rect.left,
      y: mouseEvent.clientY - rect.top
    };
  }

  // Get the position of a touch relative to the canvas
  function getTouchPos(canvasDom, touchEvent) {
    var rect = canvasDom.getBoundingClientRect();
    console.log(touchEvent);
    /*
      Devuelve el tamaño de un elemento y su posición relativa respecto
      a la ventana de visualización (viewport).
    */
    return {
      x: touchEvent.touches[0].clientX - rect.left, // Popiedad de todo evento Touch
      y: touchEvent.touches[0].clientY - rect.top
    };
  }

  // Draw to the canvas
  function renderCanvas() {
    if (drawing) {
      var tint = document.getElementById("color");
      var punta = document.getElementById("puntero");
      ctx.strokeStyle = tint.value;
      ctx.beginPath();
      ctx.moveTo(lastPos.x, lastPos.y);
      ctx.lineTo(mousePos.x, mousePos.y);
      console.log(punta.value);
      ctx.lineWidth = punta.value;
      ctx.stroke();
      ctx.closePath();
      lastPos = mousePos;
    }
  }

  function clearCanvas() {
    canvas.width = canvas.width;
  }

  // Allow for animation
  (function drawLoop () {
    requestAnimFrame(drawLoop);
    renderCanvas();
  })();

})();    
</script>

<?php if(StockData::getPrincipal()->method=="1"): ?>
<style>

#draw-canvas {
  border: 2px dotted #CCCCCC;
  border-radius: 5px;
  cursor: crosshair;
  background-image: url("CF-SYSTEMS/storage/configuration/fxrma.png");
  background-size: 100% 100%;
}

#draw-dataUrl {
  width: 100%;
}

</style>

<?php elseif(StockData::getPrincipal()->method=="2"): ?>
<style>

#draw-canvas {
  border: 2px dotted #CCCCCC;
  border-radius: 5px;
  cursor: crosshair;
  background-image: url("CF-SYSTEMS/storage/configuration/furma.png");
  background-size: 100% 100%;
}

#draw-dataUrl {
  width: 100%;
}

</style>

<?php endif;?>
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="running"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-random'></i> 
             <?php echo (Core::$user->language=='ES') ? "Listado de Contratos" : "List of Contracts"; ?>
           </h1>
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

            
<?php if(StockData::getPrincipal()->update=="1"):?>
 <div class="row">
 
          <!-- /.col -->
          <div class="col-12 col-sm-4 col-md-4 my-2">
            <a href="./?view=contract&opt=free" class="floating-btn message-btn" style="background-color: orange;">
  <div class="icon-container">
   <i class="fa fa-road"></i>
  </div>
  <span class="message-text"> ABIERTO</span>
</a>
          </div>
          <!-- /.col -->

          
          <div class="col-12 col-sm-4 col-md-4 my-2">
             <a href="./?view=contract&opt=hours" class="floating-btn message-btn" style="background-color: #C70039;">
      <div class="icon-container">
    <i class="fa fa-history"> </i>
    </div>
    <span class="message-text"> POR HORA</span> 
  </a>
            <!-- /.info-box -->
          </div>
    </div>        <!-- /.col -->
<?php else:?>
<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>
<?php endif;?>         

<?php 
$base = new Database();
$con = $base->connect();
$sql = "SELECT SQL_BIG_RESULT * FROM booking WHERE type_id=1 AND status=1 AND stock_id=".StockData::getPrincipal()->id;
$query = $con->query($sql);

if ($query && $query->num_rows > 0): 
  $TicketMm = StockData::getPrincipal()->ticket_mm;
?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
<thead style="background-color: #333;">
  <tr>
    <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Dia" : "Day"; ?></th>
    <th>SubTotal</th>
    <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
    <th>Total</th>
    <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
    <th>Extra</th>
    <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%"; ?>)</th>
    <th>RentCar</th>
    <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Recibir" : "Receive"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
  </tr>
</thead>

<tbody>
<?php 
while($r = $query->fetch_assoc()): 
  $totpayments = 0;
  $payments = PaymentData::getByPayment($r['id']);
  $totpayments = ($payments[0]->t != null) ? $payments[0]->t : 0;

  $cars1 = CarsData::getById($r['car_id']);
  $cars2 = CarsData::getById($r['car2_id']);
?>
  <tr>
    <td width="15%">
      <a href="./?view=contract&opt=received&cars=<?php echo $cars1->id;?>&id=<?php echo $r['id'];?>" 
         class="btn btn-warning btn-block btn-sm"><i class="fas fa-car"></i></a>
    </td>

    <td>
      <?php  
      // Iconos según estado de pago
      if ($totpayments==0): 
        echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; 
      elseif ($totpayments>0 && $totpayments<$r['total']): 
        echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; 
      elseif ($r['total']==$totpayments): 
        echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; 
      endif;

      // Vehículo principal o secundario
      if ($r['car2_id']==0): 
        echo BrandData::getById($cars1->brand_id)->name." ".$cars1->name." ".$cars1->year." ".ColorData::getById($cars1->exterior_id)->name." [".$cars1->token."]"; 
      else:  
        echo BrandData::getById($cars2->brand_id)->name." ".$cars2->name." ".$cars2->year." ".ColorData::getById($cars2->exterior_id)->name." [".$cars2->token."]"; 
      endif; 
      ?>
    </td>

    <td><?php echo PersonData::getById($r['person_id'])->name; ?></td>
    <td><?php echo number_format($r['price'],2,".",","); ?></td>
    <td><?php echo $r['day']; ?></td>
    <td><?php echo number_format($r['total']-$r['value_iva'],2,".",","); ?></td>
    <td><?php echo number_format($r['value_iva'],2,".",","); ?></td>
    <td><?php echo number_format($r['total'],2,".",","); ?></td>
    <td><?php echo number_format($totpayments,2,".",","); ?></td>
    <td><?php echo number_format(($r['total']-$totpayments),2,".",","); ?></td>
    <td><?php echo number_format($r['xtotal'],2,".",","); ?></td>
    <td><?php echo number_format($r['plane'],2,".",","); ?></td>
    <td><?php echo number_format($r['total']*($r['card']/100),2,".",","); ?></td>
    <td><?php echo StockData::getById($r['stock_id'])->name; ?></td>
    <td><?php echo $r['start_at']; ?></td>
    <td><?php echo $r['end_at']; ?></td>
    <td><?php $u = UserData::getById($r['user_id']); echo $u->name." ".$u->lastname; ?></td>
  </tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>
</div>
<?php else: ?>
<div class="card" style="background-color:#222;">
  <div class="card-header">
    <h2>No hay Contratos</h2>
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

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="replace"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-random'></i> 
            <?php echo (Core::$user->language=='ES') ? "Reemplazar Vehiculo" : "Replace Vehicle"; ?>
           </h1>
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

<?php if(StockData::getPrincipal()->update=="1"):?>
 <div class="row">
 
          <!-- /.col -->
          <div class="col-12 col-sm-4 col-md-4 my-2">
            <a href="./?view=contract&opt=replacefree" class="floating-btn message-btn" style="background-color: orange;">
  <div class="icon-container">
   <i class="fa fa-road"></i>
  </div>
  <span class="message-text"> ABIERTO</span>
</a>
          </div>
          <!-- /.col -->

          
          <div class="col-12 col-sm-4 col-md-4 my-2">
             <a href="./?view=contract&opt=replacehours" class="floating-btn message-btn" style="background-color: #C70039;">
      <div class="icon-container">
    <i class="fa fa-history"> </i>
    </div>
    <span class="message-text"> POR HORA</span> 
  </a>
            <!-- /.info-box -->
          </div>
</div>          <!-- /.col -->
<?php else:?>
<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>
<?php endif;?>         
            
<?php 
$base = new Database();
$con = $base->connect();
$sql = "SELECT SQL_BIG_RESULT * FROM booking WHERE type_id=1 AND status=1 AND stock_id=".StockData::getPrincipal()->id;
$query = $con->query($sql);

if ($query && $query->num_rows > 0): 
  $TicketMm = StockData::getPrincipal()->ticket_mm;
?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
  <thead style="background-color: #333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Dia" : "Day"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%"; ?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Recibir" : "Receive"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </thead>
  <tfoot style="background-color: #333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Dia" : "Day"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%"; ?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Recibir" : "Receive"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </tfoot>

<?php 
while($r = $query->fetch_assoc()):  
  $totpayments = 0;
  $payments = PaymentData::getByPayment($r['id']);
  $totpayments = ($payments[0]->t != null) ? $payments[0]->t : 0;

  $cars1 = CarsData::getById($r['car_id']);
  $cars2 = CarsData::getById($r['car2_id']);
?>
<tr>
  <td width="20%">
    <a href="./?view=contract&opt=random&id=<?php echo $r['id'];?>" class="btn btn-warning btn-block btn-sm">
      <i class="fas fa-random"></i>
    </a>
  </td>

  <td>
    <?php  
    if ($totpayments==0) echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; 
    elseif ($totpayments>0 && $totpayments<$r['total']) echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; 
    elseif ($r['total']==$totpayments) echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; 

    if ($r['car2_id']==0): 
      echo BrandData::getById($cars1->brand_id)->name." ".$cars1->name." ".$cars1->year." ".ColorData::getById($cars1->exterior_id)->name." [".$cars1->token."]"; 
    else:  
      echo BrandData::getById($cars2->brand_id)->name." ".$cars2->name." ".$cars2->year." ".ColorData::getById($cars2->exterior_id)->name." [".$cars2->token."]"; 
    endif; 
    ?>
  </td>

  <td><?php echo PersonData::getById($r['person_id'])->name; ?></td>
  <td><?php echo number_format($r['price'],2,".",","); ?></td>
  <td><?php echo $r['day']; ?></td>
  <td><?php echo number_format($r['total']-$r['value_iva'],2,".",","); ?></td>
  <td><?php echo number_format($r['value_iva'],2,".",","); ?></td>
  <td><?php echo number_format($r['total'],2,".",","); ?></td>
  <td><?php echo number_format($totpayments,2,".",","); ?></td>
  <td><?php echo number_format(($r['total']-$totpayments),2,".",","); ?></td>
  <td><?php echo number_format($r['xtotal'],2,".",","); ?></td>
  <td><?php echo number_format($r['plane'],2,".",","); ?></td>
  <td><?php echo number_format($r['total']*($r['card']/100),2,".",","); ?></td>
  <td><?php echo StockData::getById($r['stock_id'])->name; ?></td>
  <td><?php echo $r['start_at']; ?></td>
  <td><?php echo $r['end_at']; ?></td>
  <td><?php $u = UserData::getById($r['user_id']); echo $u->name." ".$u->lastname; ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</div>
</div>
<?php else: ?>
<div class="card" style="background-color:#222;">
  <div class="card-header">
    <h2>No hay Contratos</h2>
    <p>No se ha realizado ninguna operacion.</p>
  </div>
</div>
<?php endif; ?>
</div>
</div>
</div>
</div>
</div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="replacefree"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
           <h1 class="m-0"><i class='fa fa-random'></i> 
            <?php echo (Core::$user->language=='ES') ? "Reemplazar Vehiculo" : "Replace Vehicle"; ?>
           </h1>
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

<?php if(StockData::getPrincipal()->update=="1"):?>
 <div class="row">
            
          <div class="col-12 col-sm-4 col-md-4 my-2">
          <a  href="./?view=contract&opt=replace" class="floating-btn message-btn" style="background-color: #4DBE04;">
     <div class="icon-container">
    <i class="fa fa-calendar"></i>
    </div>
    <span class="message-text"> RANGO DE FECHA </span>
  </a>
            <!-- /.info-box -->
          </div>
          
          
          <div class="col-12 col-sm-4 col-md-4 my-2">
             <a href="./?view=contract&opt=replacefreehours" class="floating-btn message-btn" style="background-color: #C70039;">
      <div class="icon-container">
    <i class="fa fa-history"> </i>
    </div>
    <span class="message-text"> POR HORA</span> 
  </a>
            <!-- /.info-box -->
          </div>
  </div>        <!-- /.col -->
<?php else:?>
<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>
<?php endif;?>         
            
<?php 
$base = new Database();
$con = $base->connect();
$sql = "SELECT SQL_BIG_RESULT * FROM booking WHERE type_id=2 AND status=1 AND stock_id=".StockData::getPrincipal()->id;
$query = $con->query($sql);

if ($query && $query->num_rows > 0): 
  $TicketMm = StockData::getPrincipal()->ticket_mm;
?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
  <thead style="background-color: #333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Dia" : "Day"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%"; ?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Recibir" : "Receive"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </thead>
  <tfoot style="background-color: #333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Dia" : "Day"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%"; ?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Recibir" : "Receive"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </tfoot>

<?php 
while($r = $query->fetch_assoc()):  
  $totpayments = 0;
  $payments = PaymentData::getByPayment($r['id']);
  $totpayments = ($payments[0]->t != null) ? $payments[0]->t : 0;

  $cars1 = CarsData::getById($r['car_id']);
  $cars2 = CarsData::getById($r['car2_id']);
?>
<tr>
  <td width="20%">
    <a href="./?view=contract&opt=random&id=<?php echo $r['id'];?>"  class="btn btn-warning btn-block btn-sm">
      <i class="fas fa-random"></i>
    </a>
  </td>

  <td>
    <?php  
    if ($totpayments==0) echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; 
    elseif ($totpayments>0 && $totpayments<$r['total']) echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; 
    elseif ($r['total']==$totpayments) echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; 

    if ($r['car2_id']==0): 
      echo BrandData::getById($cars1->brand_id)->name." ".$cars1->name." ".$cars1->year." ".ColorData::getById($cars1->exterior_id)->name." [".$cars1->token."]"; 
    else:  
      echo BrandData::getById($cars2->brand_id)->name." ".$cars2->name." ".$cars2->year." ".ColorData::getById($cars2->exterior_id)->name." [".$cars2->token."]"; 
    endif; 
    ?>
  </td>

  <td><?php echo PersonData::getById($r['person_id'])->name; ?></td>
  <td><?php echo number_format($r['price'],2,".",","); ?></td>
  <td><?php echo $r['day']; ?></td>
  <td><?php echo number_format($r['total']-$r['value_iva'],2,".",","); ?></td>
  <td><?php echo number_format($r['value_iva'],2,".",","); ?></td>
  <td><?php echo number_format($r['total'],2,".",","); ?></td>
  <td><?php echo number_format($totpayments,2,".",","); ?></td>
  <td><?php echo number_format(($r['total']-$totpayments),2,".",","); ?></td>
  <td><?php echo number_format($r['xtotal'],2,".",","); ?></td>
  <td><?php echo number_format($r['plane'],2,".",","); ?></td>
  <td><?php echo number_format($r['total']*($r['card']/100),2,".",","); ?></td>
  <td><?php echo StockData::getById($r['stock_id'])->name; ?></td>
  <td><?php echo $r['start_at']; ?></td>
  <td><?php echo $r['end_at']; ?></td>
  <td><?php $u = UserData::getById($r['user_id']); echo $u->name." ".$u->lastname; ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</div>
</div>
<?php else: ?>
<div class="card" style="background-color:#222;">
  <div class="card-header">
    <h2>No hay Contratos</h2>
    <p>No se ha realizado ninguna operacion.</p>
  </div>
</div>
<?php endif; ?>
</div>
</div>
</div>
</div>
</div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="extend"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><i class='fa fa-random'></i> 
              <?php echo (Core::$user->language=='ES') ? "Extender Fecha" : "Extend Date"; ?>
            </h1>
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

 <?php if(StockData::getPrincipal()->update=="1"):?>
 <div class="row">
 
          <!-- /.col -->
          <div class="col-12 col-sm-4 col-md-4 my-2">
            <a href="./?view=contract&opt=extendfree" class="floating-btn message-btn" style="background-color: orange;">
  <div class="icon-container">
   <i class="fa fa-road"></i>
  </div>
  <span class="message-text"> ABIERTO</span>
</a>
          </div>
          <!-- /.col -->

 
</div>          <!-- /.col -->
<?php else:?>
<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>
<?php endif;?>         
            
<?php 
$base = new Database();
$con = $base->connect();
$sql = "SELECT SQL_BIG_RESULT * FROM booking WHERE type_id=1 AND status=1 AND stock_id=".StockData::getPrincipal()->id;
$query = $con->query($sql);

if ($query && $query->num_rows > 0): 
  $TicketMm = StockData::getPrincipal()->ticket_mm;
?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
  <thead style="background-color: #333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Dia" : "Day"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%"; ?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Recibir" : "Receive"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </thead>
  <tfoot style="background-color: #333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Dia" : "Day"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%"; ?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Recibir" : "Receive"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </tfoot>

<?php 
while($r = $query->fetch_assoc()):  
  $totpayments = 0;
  $payments = PaymentData::getByPayment($r['id']);
  $totpayments = ($payments[0]->t != null) ? $payments[0]->t : 0;

  $cars1 = CarsData::getById($r['car_id']);
  $cars2 = CarsData::getById($r['car2_id']);
?>
<tr>
  <td width="20%">
    <a href="./?view=contract&opt=extenddate&id=<?php echo $r['id'];?>" class="btn btn-warning btn-block btn-sm">
      <i class="fas fa-calendar"></i>
    </a>
  </td>

  <td>
    <?php  
    if ($totpayments==0) echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; 
    elseif ($totpayments>0 && $totpayments<$r['total']) echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; 
    elseif ($r['total']==$totpayments) echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; 

    if ($r['car2_id']==0): 
      echo BrandData::getById($cars1->brand_id)->name." ".$cars1->name." ".$cars1->year." ".ColorData::getById($cars1->exterior_id)->name." [".$cars1->token."]"; 
    else:  
      echo BrandData::getById($cars2->brand_id)->name." ".$cars2->name." ".$cars2->year." ".ColorData::getById($cars2->exterior_id)->name." [".$cars2->token."]"; 
    endif; 
    ?>
  </td>

  <td><?php echo PersonData::getById($r['person_id'])->name; ?></td>
  <td><?php echo number_format($r['price'],2,".",","); ?></td>
  <td><?php echo $r['day']; ?></td>
  <td><?php echo number_format($r['total']-$r['value_iva'],2,".",","); ?></td>
  <td><?php echo number_format($r['value_iva'],2,".",","); ?></td>
  <td><?php echo number_format($r['total'],2,".",","); ?></td>
  <td><?php echo number_format($totpayments,2,".",","); ?></td>
  <td><?php echo number_format(($r['total']-$totpayments),2,".",","); ?></td>
  <td><?php echo number_format($r['xtotal'],2,".",","); ?></td>
  <td><?php echo number_format($r['plane'],2,".",","); ?></td>
  <td><?php echo number_format($r['total']*($r['card']/100),2,".",","); ?></td>
  <td><?php echo StockData::getById($r['stock_id'])->name; ?></td>
  <td><?php echo $r['start_at']; ?></td>
  <td><?php echo $r['end_at']; ?></td>
  <td><?php $u = UserData::getById($r['user_id']); echo $u->name." ".$u->lastname; ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</div>
</div>
<?php else: ?>
<div class="card" style="background-color:#222;">
  <div class="card-header">
    <h2>No hay Contratos</h2>
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

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="pay"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
           <h1 class="m-0"><i class='fa fa-asterisk'></i> 
             <?php echo (Core::$user->language=='ES') ? "Pago / Credito" : "Payment / Credit"; ?>
           </h1>
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

<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>
            
<?php 
$base = new Database();
$con = $base->connect();
$sql = "SELECT SQL_BIG_RESULT * FROM booking WHERE type_id=1 AND status=1 AND stock_id=".StockData::getPrincipal()->id;
$query = $con->query($sql);

if ($query && $query->num_rows > 0): 
  $TicketMm = StockData::getPrincipal()->ticket_mm;
?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
  <thead style="background-color: #333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Dia" : "Day"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%"; ?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Recibir" : "Receive"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </thead>
  <tfoot style="background-color: #333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Dia" : "Day"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%"; ?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Recibir" : "Receive"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </tfoot>

<?php 
while($r = $query->fetch_assoc()):  
  $totpayments = 0;
  $payments = PaymentData::getByPayment($r['id']);
  $totpayments = ($payments[0]->t != null) ? $payments[0]->t : 0;

  $cars1 = CarsData::getById($r['car_id']);
  $cars2 = CarsData::getById($r['car2_id']);
?>
<tr>
  <td width="20%">
    <a href="./?view=contract&opt=payment&id=<?php echo $r['person_id'];?>" class="btn btn-warning btn-block btn-sm">
      <i class="fas fa-money-bill-alt"></i>
    </a>
  </td>

  <td>
    <?php  
    if ($totpayments==0) echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; 
    elseif ($totpayments>0 && $totpayments<$r['total']) echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; 
    elseif ($r['total']==$totpayments) echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; 

    if ($r['car2_id']==0): 
      echo BrandData::getById($cars1->brand_id)->name." ".$cars1->name." ".$cars1->year." ".ColorData::getById($cars1->exterior_id)->name." [".$cars1->token."]"; 
    else:  
      echo BrandData::getById($cars2->brand_id)->name." ".$cars2->name." ".$cars2->year." ".ColorData::getById($cars2->exterior_id)->name." [".$cars2->token."]"; 
    endif; 
    ?>
  </td>

  <td><?php echo PersonData::getById($r['person_id'])->name; ?></td>
  <td><?php echo number_format($r['price'],2,".",","); ?></td>
  <td><?php echo $r['day']; ?></td>
  <td><?php echo number_format($r['total']-$r['value_iva'],2,".",","); ?></td>
  <td><?php echo number_format($r['value_iva'],2,".",","); ?></td>
  <td><?php echo number_format($r['total'],2,".",","); ?></td>
  <td><?php echo number_format($totpayments,2,".",","); ?></td>
  <td><?php echo number_format(($r['total']-$totpayments),2,".",","); ?></td>
  <td><?php echo number_format($r['xtotal'],2,".",","); ?></td>
  <td><?php echo number_format($r['plane'],2,".",","); ?></td>
  <td><?php echo number_format($r['total']*($r['card']/100),2,".",","); ?></td>
  <td><?php echo StockData::getById($r['stock_id'])->name; ?></td>
  <td><?php echo $r['start_at']; ?></td>
  <td><?php echo $r['end_at']; ?></td>
  <td><?php $u = UserData::getById($r['user_id']); echo $u->name." ".$u->lastname; ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</div>
</div>
<?php else: ?>
<div class="card" style="background-color:#222;">
  <div class="card-header">
    <h2>No hay Contratos</h2>
    <p>No se ha realizado ninguna operacion.</p>
  </div>
</div>
<?php endif; ?>
</div>
</div>
</div>
</div>
</div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="bill"):?>

<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-copy'></i> <?php  switch (Core::$user->language){  case 'ES': echo "Factura / Contrato"; break;  case 'EN': echo "Invoice / Contract"; break; } ?></h1>
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
    
<?php if(StockData::getPrincipal()->update=="1"):?>
 <div class="row">
 
          <!-- /.col -->
          <div class="col-12 col-sm-4 col-md-4 my-2">
            <a href="./?view=contract&opt=billfree" class="floating-btn message-btn" style="background-color: orange;">
  <div class="icon-container">
   <i class="fa fa-road"></i>
  </div>
  <span class="message-text"> ABIERTO</span>
</a>
          </div>
          <!-- /.col -->

          
          <div class="col-12 col-sm-4 col-md-4 my-2">
             <a href="./?view=contract&opt=billhours" class="floating-btn message-btn" style="background-color: #C70039;">
      <div class="icon-container">
    <i class="fa fa-history"> </i>
    </div>
    <span class="message-text"> POR HORA</span> 
  </a>
            <!-- /.info-box -->
          </div>
</div>          <!-- /.col -->
<?php else:?>
<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>
<?php endif;?>     
            
<?php 
$base = new Database();
$con = $base->connect();
$sql = "SELECT SQL_BIG_RESULT * FROM booking WHERE type_id=1 AND status=1 AND stock_id=".StockData::getPrincipal()->id;
$query = $con->query($sql);

if ($query && $query->num_rows > 0): 
    $TicketMm = StockData::getPrincipal()->ticket_mm;
?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
<thead  style="background-color: #333;">
  <tr>
    <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Dia" : "Day"; ?></th>
    <th>SubTotal</th>
    <th><?php echo StockData::getPrincipal()->imp_name; ?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</th>
    <th>Total</th>
    <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
    <th>Extra</th>
    <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%";?>)</th>
    <th>RentCar</th>
    <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Recibir" : "Receive"; ?></th>
    <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
  </tr>
</thead>

<?php while($r = $query->fetch_assoc()): 
$totpayments = 0;
$payments = PaymentData::getByPayment($r['id']);
$totpayments = $payments[0]->t!=null ? $payments[0]->t : 0;
$cars1 = CarsData::getById($r['car_id']);
$cars2 = CarsData::getById($r['car2_id']);
?>
<tr>
  <td>
    <div class="btn-group btn-group-sm btn-block">
    
      <!-- Botón Contrato -->
      <a href="<?php echo $TicketMm; ?>/ticket.php?id=<?php echo $r['id']; ?>"
         class="btn btn-info btn-sm"
         onclick="return manejarVisualizacionPDF(this.href, event)">
         <i class="fa fa-eye"></i>
      </a>
    </div>
  </td>

  <td>
    <?php  
      if ($totpayments==0) echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; 
      elseif ($totpayments>0 && $totpayments<$r['total']) echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; 
      elseif ($r['total']==$totpayments) echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; 

      if ($r['car2_id']==0): 
        echo BrandData::getById($cars1->brand_id)->name." ".$cars1->name." ".$cars1->year." ".ColorData::getById($cars1->exterior_id)->name." [".$cars1->token."]"; 
      else:  
        echo BrandData::getById($cars2->brand_id)->name." ".$cars2->name." ".$cars2->year." ".ColorData::getById($cars2->exterior_id)->name." [".$cars2->token."]"; 
      endif;
    ?>
  </td>
  <td><?php echo PersonData::getById($r['person_id'])->name; ?></td>
  <td><?php echo number_format($r['price'],2,".",","); ?></td>
  <td><?php echo $r['day']; ?></td>
  <td><?php echo number_format($r['total']-$r['value_iva'],2,".",","); ?></td>
  <td><?php echo number_format($r['value_iva'],2,".",","); ?></td>
  <td><?php echo number_format($r['total'],2,".",","); ?></td>
  <td><?php echo number_format($totpayments,2,".",","); ?></td>
  <td><?php echo number_format(($r['total']-$totpayments),2,".",","); ?></td>
  <td><?php echo number_format($r['xtotal'],2,".",","); ?></td>
  <td><?php echo number_format($r['plane'],2,".",","); ?></td>
  <td><?php echo number_format($r['total']*($r['card']/100),2,".",","); ?></td>
  <td><?php echo StockData::getById($r['stock_id'])->name; ?></td>
  <td><?php echo $r['start_at']; ?></td>
  <td><?php echo $r['end_at']; ?></td>
  <td><?php $u = UserData::getById($r['user_id']); echo $u->name." ".$u->lastname; ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</div>
</div>
<?php else: ?>
<div class="card" style="background-color:#222;">
  <div class="card-header">
    <h2>No hay Contratos</h2>
    <p>No se ha realizado ninguna operacion.</p>
  </div>
</div>
<?php endif; ?>
</div>
</div>
</div>
</div>
</div>
</section>

<!-- MODAL RECIBO -->
<div id="modalPDF2" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:#000a; z-index:9999;">
  <div style="position:relative; width:90%; height:90%; margin:5% auto; background:#1e1e1e; border-radius:10px; overflow:hidden; padding-top:80px;">
    <div style="position:absolute; top:20px; right:20px; display:flex; flex-direction:column; gap:10px;">
      <button onclick="imprimirPDF2()" style="background:#28a745; color:#fff; padding:10px 16px; border-radius:40px; font-weight:bold;"><i class="fa fa-print"></i> IMPRIMIR</button>
      <a id="btnDescargar2" href="#" download style="background:#007bff; color:#fff; padding:10px 16px; border-radius:40px; font-weight:bold; text-decoration:none;"><i class="fa fa-download"></i> DESCARGAR</a>
      <button onclick="cerrarPDF2()" style="background:#c40030; color:#fff; padding:10px 16px; border-radius:40px; font-weight:bold;"><i class="fa fa-times"></i> CERRAR</button>
    </div>
    <iframe id="iframePDF2" src="" style="width:100%; height:100%; border:none;"></iframe>
  </div>
</div>

<!-- MODAL CONTRATO -->
<div id="modalPDF" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:#000a; z-index:9999;">
  <div style="position:relative; width:90%; height:90%; margin:5% auto; background:#1e1e1e; border-radius:10px; overflow:hidden; padding-top:80px;">
    <div style="position:absolute; top:20px; right:20px; display:flex; flex-direction:column; gap:10px;">
      <button onclick="imprimirPDF()" style="background:#28a745; color:#fff; padding:10px 16px; border-radius:40px; font-weight:bold;"><i class="fa fa-print"></i> IMPRIMIR</button>
      <a id="btnDescargar" href="#" download style="background:#007bff; color:#fff; padding:10px 16px; border-radius:40px; font-weight:bold; text-decoration:none;"><i class="fa fa-download"></i> DESCARGAR</a>
      <button onclick="cerrarPDF()" style="background:#c40030; color:#fff; padding:10px 16px; border-radius:40px; font-weight:bold;"><i class="fa fa-times"></i> CERRAR</button>
    </div>
    <iframe id="iframePDF" src="" style="width:100%; height:100%; border:none;"></iframe>
  </div>
</div>

<script>
function manejarVisualizacionPDF2(url, event) {
  if (window.innerWidth >= 1024) {
    event.preventDefault();
    document.getElementById('iframePDF2').src = url;
    document.getElementById('btnDescargar2').href = url;
    document.getElementById('modalPDF2').style.display = 'block';
    return false;
  }
  return true;
}
function cerrarPDF2() {
  document.getElementById('modalPDF2').style.display = 'none';
  document.getElementById('iframePDF2').src = '';
  document.getElementById('btnDescargar2').href = '#';
}
function imprimirPDF2() {
  const iframe = document.getElementById('iframePDF2');
  iframe.contentWindow.print();
}

function manejarVisualizacionPDF(url, event) {
  if (window.innerWidth >= 1024) {
    event.preventDefault();
    document.getElementById('iframePDF').src = url;
    document.getElementById('btnDescargar').href = url;
    document.getElementById('modalPDF').style.display = 'block';
    return false;
  }
  return true;
}
function cerrarPDF() {
  document.getElementById('modalPDF').style.display = 'none';
  document.getElementById('iframePDF').src = '';
  document.getElementById('btnDescargar').href = '#';
}
function imprimirPDF() {
  const iframe = document.getElementById('iframePDF');
  iframe.contentWindow.print();
}
</script>


<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="free"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-edit'></i> 
            <?php echo (Core::$user->language=='ES') ? "Listado de Contratos" : "List of Contracts"; ?>
           </h1>
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

<?php if(StockData::getPrincipal()->update=="1"):?>
 <div class="row">
 
<?php if(StockData::getPrincipal()->method=="1"):?>                
          <div class="col-12 col-sm-4 col-md-4 my-2">
          <a  href="./?view=contract&opt=running" class="floating-btn message-btn" style="background-color: #4DBE04;">
     <div class="icon-container">
    <i class="fa fa-calendar"></i>
    </div>
    <span class="message-text"> RANGO DE FECHA </span>
  </a>
            <!-- /.info-box -->
          </div>
 <?php endif;?>         
          
          <div class="col-12 col-sm-4 col-md-4 my-2">
             <a href="./?view=contract&opt=hours" class="floating-btn message-btn" style="background-color: #C70039;">
      <div class="icon-container">
    <i class="fa fa-history"> </i>
    </div>
    <span class="message-text"> POR HORA</span> 
  </a>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
</div>
<?php else:?>
<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>
<?php endif;?>         
            
<?php 
$base = new Database();
$con = $base->connect();
$sql = "SELECT SQL_BIG_RESULT * FROM booking WHERE type_id=2 AND status=1 AND stock_id=".StockData::getPrincipal()->id;
$query = $con->query($sql);

if ($query && $query->num_rows > 0): 
  $TicketMm = StockData::getPrincipal()->ticket_mm;
?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
  <thead style="background-color: #333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Dia" : "Day"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%"; ?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </thead>
<tbody>
<?php 
while($r = $query->fetch_assoc()):  
  $totpayments = 0;
  $payments = PaymentData::getByPayment($r['id']);
  $totpayments = ($payments[0]->t != null) ? $payments[0]->t : 0;

  $fechaActual = date("Y-m-d"); 
  $fechaRegistro = date("Y-m-d", strtotime($r['start_at'])); 
  $diasTranscurridos = (strtotime($fechaActual) - strtotime($fechaRegistro)) / 86400;

  $cars1 = CarsData::getById($r['car_id']);
  $cars2 = CarsData::getById($r['car2_id']);
?>
<tr>
  <td width="15%">
    <a href="./?view=contract&opt=received&cars=<?php echo $cars1->id;?>&id=<?php echo $r['id'];?>" class="btn btn-warning btn-block btn-sm"><i class="fas fa-car"></i></a>
  </td>

  <td>
    <?php  
    if ($totpayments==0): 
      echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; 
    elseif ($totpayments>0 && $totpayments<$r['total']): 
      echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; 
    elseif ($r['total']==$totpayments): 
      echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; 
    endif;

    if ($r['car2_id']==0): 
      $brand = BrandData::getById($cars1->brand_id); 
      echo $brand->name." ".$cars1->name." ".$cars1->year." ".ColorData::getById($cars1->exterior_id)->name." [".$cars1->token."]"; 
    else: 
      $brand = BrandData::getById($cars2->brand_id); 
      echo $brand->name." ".$cars2->name." ".$cars2->year." ".ColorData::getById($cars2->exterior_id)->name." [".$cars2->token."]"; 
    endif; 
    ?>
  </td>

  <td><?php echo PersonData::getById($r['person_id'])->name; ?></td>

  

  <td><?php echo number_format($r['price'],2,".",","); ?></td>
  <td><?php echo $diasTranscurridos; ?></td>
  <td><?php echo number_format($r['price']*$diasTranscurridos,2,".",",");?></td>
  <td><?php echo number_format((($r['price']*$diasTranscurridos)+$r['xtotal'])*($r['iva']/100),2,".",","); ?></td>
  <td><?php echo number_format((($r['price']*$diasTranscurridos)+$r['xtotal']),2,".",","); ?></td>
  <td><?php echo number_format($totpayments,2,".",","); ?></td>
  <td><?php echo number_format((($r['price']*$diasTranscurridos)+$r['xtotal'])-$totpayments,2,".",","); ?></td>
  <td><?php echo number_format($r['xtotal'],2,".",","); ?></td>
  <td><?php echo number_format($r['plane'],2,".",","); ?></td>
  <td><?php echo number_format($r['total']*($r['card']/100),2,".",","); ?></td>
  <td><?php echo StockData::getById($r['stock_id'])->name; ?></td>
  <td><?php echo $r['start_at']; ?></td>
  <td><?php $u = UserData::getById($r['user_id']); echo $u->name." ".$u->lastname; ?></td> 
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>
</div>
<?php else: ?>
<div class="card" style="background-color:#222;">
  <div class="card-header">
    <h2>No hay Contratos</h2>
    <p>No se ha realizado ninguna operacion.</p>
  </div>
</div>
<?php endif; ?>
</div>
</div>
</div>
</div>
</div>
</section>



<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="extendfree"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-retweet'></i> 
             <?php echo (Core::$user->language=='ES') ? "Renovar Contrato" : "Renew Contract"; ?>
           </h1>
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
      

   <?php if(StockData::getPrincipal()->update=="1"):?>
 <div class="row">
            
          <div class="col-12 col-sm-4 col-md-4 my-2">
          <a  href="./?view=contract&opt=extend" class="floating-btn message-btn" style="background-color: #4DBE04;">
     <div class="icon-container">
    <i class="fa fa-calendar"></i>
    </div>
    <span class="message-text"> RANGO DE FECHA </span>
  </a>
            <!-- /.info-box -->
          </div>
          
  
</div>          <!-- /.col -->
<?php else:?>
<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>
<?php endif;?>         
            
<?php 
$base = new Database();
$con = $base->connect();
$sql = "SELECT SQL_BIG_RESULT * FROM booking WHERE type_id=2 AND status=1 AND stock_id=".StockData::getPrincipal()->id;
$query = $con->query($sql);

if ($query && $query->num_rows > 0): 
    $TicketMm = StockData::getPrincipal()->ticket_mm;
?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
  <thead style="background-color: #333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Pago" : "Payment"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Días Transcurridos" : "Days Passed"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%";?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </thead>

  <tfoot style="background-color: #333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Pago" : "Payment"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Días Transcurridos" : "Days Passed"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%";?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </tfoot>

<?php 
while($r = $query->fetch_assoc()){  
  $totpayments = 0;
  $payments = PaymentData::getByPayment($r['id']);
  $totpayments = $payments[0]->t ?? 0;

  $fechaActual = date("Y-m-d"); 
  $fechaRegistro = date("Y-m-d", strtotime($r['start_at'])); 
  $segundosTranscurridos = strtotime($fechaActual) - strtotime($fechaRegistro);
  $diasTranscurridos = $segundosTranscurridos / 86400;

  $cars1 = CarsData::getById($r['car_id']);
  $cars2 = CarsData::getById($r['car2_id']);
?>
<tr>
  <td width="15%">
    <a href="./?view=contract&opt=renew&id=<?php echo $r['id'];?>"  
       class="btn btn-warning btn-block btn-sm">
       <i class="fas fa-history"></i>
    </a>
  </td>

  <td>
    <?php  
      if ($totpayments==0) echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; 
      elseif ($totpayments>0 && $totpayments<$r['total']) echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; 
      elseif ($r['total']==$totpayments) echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; 

      if ($r['car2_id']==0): 
        $brand = BrandData::getById($cars1->brand_id); 
        echo $brand->name." ".$cars1->name." ".$cars1->year." ".ColorData::getById($cars1->exterior_id)->name." [".$cars1->token."]"; 
      else: 
        $brand = BrandData::getById($cars2->brand_id); 
        echo $brand->name." ".$cars2->name." ".$cars2->year." ".ColorData::getById($cars2->exterior_id)->name." [".$cars2->token."]"; 
      endif;
    ?>
  </td>

  <td><?php echo PersonData::getById($r['person_id'])->name; ?></td>
  <td>
    <?php 
      if ($r['payment_day']==1) echo "DIARIO"; 
      elseif ($r['payment_day']==7) echo "SEMANAL"; 
      elseif ($r['payment_day']==15) echo "QUINCENAL"; 
      elseif ($r['payment_day']==30) echo "MENSUAL"; 
    ?>
  </td>
  <td><?php echo number_format($r['price'],2,".",","); ?></td>
  <td><?php echo $diasTranscurridos; ?></td>
  <td><?php echo number_format($r['total']*$diasTranscurridos,2,".",","); ?></td>
  <td><?php echo number_format($totpayments,2,".",","); ?></td>
  <td><?php echo number_format($r['xtotal'],2,".",","); ?></td>
  <td><?php echo number_format((($r['price']*$diasTranscurridos)+$r['xtotal'])*($r['iva']/100),2,".",","); ?></td>
  <td><?php echo number_format($r['plane'],2,".",","); ?></td>
  <td><?php echo number_format($r['total']*($r['card']/100),2,".",","); ?></td>
  <td><?php echo StockData::getById($r['stock_id'])->name; ?></td>
  <td><?php echo $r['start_at']; ?></td>
  <td><?php $u = UserData::getById($r['user_id']); echo $u->name." ".$u->lastname; ?></td> 
</tr>
<?php }; ?>
</table>
</div>
</div>
</div>
<?php else: ?>
<div class="card" style="background-color:#222;">
  <div class="card-header">
    <h2>No hay Contratos</h2>
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

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="billfree"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><i class='fa fa-copy'></i> 
              <?php echo (Core::$user->language=='ES') ? "Factura / Contrato" : "Invoice / Contract"; ?>
            </h1>
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

<?php if(StockData::getPrincipal()->update=="1"):?>
 <div class="row">
            
          <div class="col-12 col-sm-4 col-md-4 my-2">
          <a  href="./?view=contract&opt=bill" class="floating-btn message-btn" style="background-color: #4DBE04;">
     <div class="icon-container">
    <i class="fa fa-calendar"></i>
    </div>
    <span class="message-text"> RANGO DE FECHA </span>
  </a>
            <!-- /.info-box -->
          </div>
          
          
          <div class="col-12 col-sm-4 col-md-4 my-2">
             <a href="./?view=contract&opt=billhours" class="floating-btn message-btn" style="background-color: #C70039;">
      <div class="icon-container">
    <i class="fa fa-history"> </i>
    </div>
    <span class="message-text"> POR HORA</span> 
  </a>
            <!-- /.info-box -->
          </div>
</div>          <!-- /.col -->
<?php else:?>
<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>
<?php endif;?>         
            
<?php 
$base = new Database();
$con = $base->connect();
$sql = "SELECT SQL_BIG_RESULT * FROM booking WHERE type_id=2 AND status=1 AND stock_id=".StockData::getPrincipal()->id;
$query = $con->query($sql);

if ($query && $query->num_rows > 0): 
    $TicketMm = StockData::getPrincipal()->ticket_mm;
?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
  <thead style="background-color: #333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Días" : "Days"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%"; ?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </thead>

  <tfoot style="background-color: #333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Accion" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehiculo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Dia" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Días" : "Days"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%"; ?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </tfoot>

<?php 
while($r = $query->fetch_assoc()){  
  $totpayments = 0;
  $payments = PaymentData::getByPayment($r['id']);
  $totpayments = $payments[0]->t ?? 0;

  $diasTranscurridos = (strtotime(date("Y-m-d")) - strtotime($r['start_at'])) / 86400;

  $cars1 = CarsData::getById($r['car_id']);
  $cars2 = CarsData::getById($r['car2_id']);

  $subtotal = ($r['price'] * $diasTranscurridos) + $r['xtotal'];
  $iva = $subtotal * ($r['iva']/100);
  $total = $subtotal + $iva;
  $restante = $total - $totpayments;
?>
<tr>  
  <td>
    <div class="btn-group btn-group-sm btn-block">
      <?php if(PersonData::getById($r['person_id'])->is_rental==0):?>
        <a href="<?php echo $TicketMm; ?>/ticket-receipt.php?id=<?php echo $r['id']; ?>"  
           class="btn btn-warning"><i class="fas fa-money-check-alt"></i></a>
      <?php endif;?>
      <a href="<?php echo $TicketMm; ?>/ticket.php?id=<?php echo $r['id']; ?>" 
         class="btn btn-success"><i class="fas fa-copy"></i></a>
    </div>
  </td>

  <td>
    <?php  
    if ($totpayments==0) echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; 
    elseif ($totpayments>0 && $totpayments<$total) echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; 
    elseif ($total==$totpayments) echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; 

    if ($r['car2_id']==0): 
      $brand = BrandData::getById($cars1->brand_id); 
      echo $brand->name." ".$cars1->name." ".$cars1->year." ".ColorData::getById($cars1->exterior_id)->name." [".$cars1->token."]"; 
    else: 
      $brand = BrandData::getById($cars2->brand_id); 
      echo $brand->name." ".$cars2->name." ".$cars2->year." ".ColorData::getById($cars2->exterior_id)->name." [".$cars2->token."]"; 
    endif;
    ?>
  </td>

  <td><?php echo PersonData::getById($r['person_id'])->name; ?></td>
  <td><?php echo number_format($r['price'],2,".",","); ?></td>
  <td><?php echo $diasTranscurridos; ?></td>
  <td><?php echo number_format($subtotal,2,".",","); ?></td>
  <td><?php echo number_format($iva,2,".",","); ?></td>
  <td><?php echo number_format($total,2,".",","); ?></td>
  <td><?php echo number_format($totpayments,2,".",","); ?></td>
  <td><?php echo number_format($restante,2,".",","); ?></td>
  <td><?php echo number_format($r['xtotal'],2,".",","); ?></td>
  <td><?php echo number_format($r['plane'],2,".",","); ?></td>
  <td><?php echo number_format($total*($r['card']/100),2,".",","); ?></td>
  <td><?php echo StockData::getById($r['stock_id'])->name; ?></td>
  <td><?php echo $r['start_at']; ?></td>
  <td><?php $u = UserData::getById($r['user_id']); echo $u->name." ".$u->lastname; ?></td> 
</tr>
<?php }; ?>
</table>
</div>
</div>
</div>
<?php else: ?>
<div class="card" style="background-color:#222;">
  <div class="card-header">
    <h2>No hay Contratos</h2>
    <p>No se ha realizado ninguna operacion.</p>
  </div>
</div>
<?php endif; ?>
</div>
</div>
</div>
</div>
</div>
</section>


<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="payfree"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-asterisk'></i> 
            <?php echo (Core::$user->language=='ES') ? "Pago / Crédito" : "Payment / Credit"; ?>
           </h1>
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

<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>
            
<?php 
$base = new Database();
$con = $base->connect();
$sql = "SELECT SQL_BIG_RESULT * FROM booking WHERE type_id=2 AND status=1 AND stock_id=".StockData::getPrincipal()->id;
$query = $con->query($sql);

if ($query && $query->num_rows > 0): 
    $TicketMm = StockData::getPrincipal()->ticket_mm;
?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
  <thead style="background-color:#333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Acción" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehículo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Pago" : "Payment"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Día" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Días" : "Days"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%"; ?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Recibir" : "Receive"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </thead>

  <tfoot style="background-color:#333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Acción" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehículo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Pago" : "Payment"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Día" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Días" : "Days"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name." (".StockData::getPrincipal()->imp_val."%)"; ?></th>
      <th>Total</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%"; ?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Recibir" : "Receive"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
    </tr>
  </tfoot>

<?php 
while($r = $query->fetch_assoc()){  
  $totpayments = 0;
  $payments = PaymentData::getByPayment($r['id']);
  $totpayments = $payments[0]->t ?? 0;

  $fechaActual = date("Y-m-d"); 
  $diasTranscurridos = (strtotime($fechaActual) - strtotime($r['start_at'])) / 86400;

  $cars1 = CarsData::getById($r['car_id']);
  $cars2 = CarsData::getById($r['car2_id']);

  $subtotal = ($r['price'] * $diasTranscurridos) + $r['xtotal'];
  $iva = $subtotal * ($r['iva']/100);
  $total = $subtotal + $iva;
  $restante = $total - $totpayments;
?>
<tr>
  <td width="20%">
    <a href="./?view=contract&opt=renewpayment&id=<?php echo $r['person_id'];?>&pay=<?php echo $total;?>" 
       class="btn btn-warning btn-block btn-sm"><i class="fas fa-money-bill-alt"></i></a>
  </td>
  <td>
    <?php  
    if ($totpayments==0) echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; 
    elseif ($totpayments>0 && $totpayments<$total) echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; 
    elseif ($total==$totpayments) echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; 

    if ($r['car2_id']==0): 
      $brand = BrandData::getById($cars1->brand_id); 
      echo $brand->name." ".$cars1->name." ".$cars1->year." ".ColorData::getById($cars1->exterior_id)->name." [".$cars1->token."]"; 
    else: 
      $brand = BrandData::getById($cars2->brand_id); 
      echo $brand->name." ".$cars2->name." ".$cars2->year." ".ColorData::getById($cars2->exterior_id)->name." [".$cars2->token."]"; 
    endif;
    ?>
  </td>
  <td><?php echo PersonData::getById($r['person_id'])->name; ?></td>
  <td>
    <?php 
      if ($r['payment_day']==1) echo "DIARIO"; 
      elseif ($r['payment_day']==7) echo "SEMANAL"; 
      elseif ($r['payment_day']==15) echo "QUINCENAL"; 
      elseif ($r['payment_day']==30) echo "MENSUAL"; 
    ?>
  </td>
  <td><?php echo number_format($r['price'],2,".",","); ?></td>
  <td><?php echo $diasTranscurridos; ?></td>
  <td><?php echo number_format($subtotal,2,".",",");?></td>
  <td><?php echo number_format($iva,2,".",","); ?></td>
  <td><?php echo number_format($total,2,".",","); ?></td>
  <td><?php echo number_format($totpayments,2,".",","); ?></td>
  <td><?php echo number_format($restante,2,".",","); ?></td>
  <td><?php echo number_format($r['xtotal'],2,".",","); ?></td>
  <td><?php echo number_format($r['plane'],2,".",","); ?></td>
  <td><?php echo number_format($total*($r['card']/100),2,".",","); ?></td>
  <td><?php echo StockData::getById($r['stock_id'])->name; ?></td>
  <td><?php echo $r['start_at']; ?></td>
  <td><?php echo $r['end_at']; ?></td>
  <td><?php $u = UserData::getById($r['user_id']); echo $u->name." ".$u->lastname; ?></td> 
</tr>
<?php }; ?>
</table>
</div>
</div>
</div>
<?php else: ?>
<div class="card" style="background-color:#222;">
  <div class="card-header">
    <h2>No hay Contratos</h2>
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

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="payment"):
$client = PersonData::getById($_GET["id"]);
$sells = BookingData::getAllBySQL("WHERE type_id=1 AND person_id=".$client->id." AND stock_id=".StockData::getPrincipal()->id." ORDER BY id DESC");

$total = 0;
$credit_array = [];

foreach ($sells as $sell) {
    $tx = PaymentData::sumBySellId2($sell->id, StockData::getPrincipal()->id)->total;
    $cars = CarsData::getById($sell->car_id);

    if ($tx >= 0) {
        $credit_array[] = [
            "brand"   => $cars->getBrand()->name,
            "model"   => $cars->name,
            "token"   => $sell->getCars()->token,
            "sell_id" => $sell->id,
            "total"   => $tx,
            "txtal"   => $sell->total
        ];
        $total += $tx;
    }
}
?>

<section class="content">
<div class="row">
  <div class="col-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-asterisk"></i> Realizar Pago</h1>
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

    <div class="callout callout-warning" style="background-color:#222;">
      <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
      <?php echo (Core::$user->language=='ES') 
        ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
        : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
    </div>

    <p><b>NOMBRE:</b> <?php echo strtoupper($client->name." ".$client->lastname);?></p>
    <h3>Deuda total: <?php echo number_format($total,2,".",","); ?></h3>

    <?php if(count($credit_array)>0): ?>
      <?php foreach($credit_array as $ca): if($ca['total']>0): ?>
        <div class="card" style="background-color:#222;">
          <div class="card-body">
            <form class="form-horizontal" method="post" enctype="multipart/form-data" id="addpayment<?php echo $ca['sell_id']; ?>" action="./?action=add&opt=contractpayment" role="form">
              <input type="hidden" name="sell_id" value="<?php echo $ca['sell_id'];?>">
              <input type="hidden" name="client_id" value="<?php echo $client->id; ?>">

              <div class="row">
                <div class="col-md-6 col-12">
                  <label>Total de Renta: <?php echo $ca['brand']." ".$ca['model']." [".$ca['token']."]"; ?></label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                    <input style="background-color:#333;" type="text" id="txtal<?php echo $ca['sell_id']; ?>" class="form-control" value="<?php echo $ca['txtal'] ?>" readonly>
                  </div>
                </div>

                <div class="col-md-6 col-12">
                  <label>Forma de Pago</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-list-ol"></i></span>
                    <select style="background-color:#333;" name="f_id" class="form-control">
                      <?php foreach(FData::getAll() as $f): ?>
                        <option value="<?php echo $f->id;?>"><?php echo $f->name;?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row mt-2">
                <div class="col-md-6 col-12">
                  <label>Total Deuda</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
                    <input style="background-color:#333;" type="text" class="form-control" value="$ <?php echo round($ca['total']); ?>" readonly>
                    <input type="hidden" name="total" id="total<?php echo $ca['sell_id']; ?>" value="<?php echo round($ca['total']); ?>">
                  </div>
                </div>

                <div class="col-md-6 col-12">
                  <label>Pago a Realizar <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
                    <input style="background-color:#333;" type="number" min="1" step="0.01" name="val" required id="val<?php echo $ca['sell_id']; ?>" class="form-control" placeholder="<?php echo (Core::$user->language=='ES') ? "Escribir Aquí" : "Write Here"; ?>">
                  </div>
                </div>
              </div>

              <div class="row my-3">
              
                <div class="col-md-12 col-12 my-2">
                  <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                </div>
              </div>
            </form>
          </div>
        </div>

<script>
$(document).ready(function(){
  $("#addpayment<?php echo $ca['sell_id']; ?>").submit(function(e){
    let total = parseFloat($("#total<?php echo $ca['sell_id']; ?>").val());
    let txtal  = parseFloat($("#txtal<?php echo $ca['sell_id']; ?>").val());
    let val    = parseFloat($("#val<?php echo $ca['sell_id']; ?>").val());

    if(!isNaN(val) && val > 0){
      if(val <= txtal){
        let go = confirm("¿Está seguro que desea continuar?");
        if(!go){ e.preventDefault(); }
      } else {
        alert("No es posible ingresar un pago mayor a la deuda total.");
        e.preventDefault();
      }
    } else {
      alert("Debes ingresar un valor mayor que 0.");
      e.preventDefault();
    }
  });
});
</script>

      <?php endif; endforeach; ?>
    <?php endif; ?>

  </div>
</div>
</section>


<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="renewpayment"):
$client = PersonData::getById($_GET["id"]);
$sells = BookingData::getAllBySQL("WHERE type_id=2 AND person_id=".$client->id." AND stock_id=".StockData::getPrincipal()->id." ORDER BY id DESC");

$total=0;
$credit_array = [];

foreach ($sells as $sell) {
    $tx   = PaymentData::sumBySellId2($sell->id,StockData::getPrincipal()->id)->total;
    $cars = CarsData::getById($sell->car_id);

    if($tx >= 0){
        $credit_array[] = [
            "brand"   => $cars->getBrand()->name,
            "model"   => $cars->name,
            "token"   => $sell->getCars()->token,
            "sell_id" => $sell->id,
            "total"   => $tx,
            "txtal"   => $_GET["pay"]
        ];
        $total += $tx;
    }
}
?>

<section class="content">
<div class="row">
  <div class="col-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-asterisk"></i> Realizar Pago</h1>
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

    <div class="callout callout-warning" style="background-color:#222;">
      <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
      <?php echo (Core::$user->language=='ES') 
        ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
        : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
    </div>

    <p><b>NOMBRE:</b> <?php echo strtoupper($client->name." ".$client->lastname);?></p>
    <h3>Deuda total: <?php echo number_format($_GET["pay"],2,".",","); ?></h3>

    <?php if(count($credit_array)>0): ?>
      <?php foreach($credit_array as $ca): if($ca['total']>0): ?>
        <div class="card" style="background-color:#222;">
          <div class="card-body">
            <form method="post" id="addpayment<?php echo $ca['sell_id']; ?>" action="./?action=add&opt=renewpayment">
              <input type="hidden" name="sell_id" value="<?php echo $ca['sell_id'];?>">
              <input type="hidden" name="client_id" value="<?php echo $client->id; ?>">
              <input type="hidden" name="total" id="total<?php echo $ca['sell_id']; ?>" value="<?php echo $ca['total']; ?>">
              <input type="hidden" name="txtal" id="txtal<?php echo $ca['sell_id']; ?>" value="<?php echo $ca['txtal']; ?>">

              <div class="row">
                <div class="col-md-6 col-12">
                  <label>Total de Renta: <?php echo $ca['brand']." ".$ca['model']." [".$ca['token']."]"; ?></label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                    <input style="background-color:#333;" type="text" class="form-control" value="<?php echo $ca['txtal'] ?>" readonly>
                  </div>
                </div>

                <div class="col-md-6 col-12">
                  <label>Forma de Pago</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-list-ol"></i></span>
                    <select style="background-color:#333;" name="f_id" class="form-control">
                      <?php foreach(FData::getAll() as $f): ?>
                        <option value="<?php echo $f->id;?>"><?php echo $f->name;?></option>
                      <?php endforeach;?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row mt-2">
                <div class="col-md-6 col-12">
                  <label>Total Deuda</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
                    <input style="background-color:#333;" type="text" class="form-control" value="<?php echo round($_GET["pay"]); ?>" readonly>
                  </div>
                </div>

                <div class="col-md-6 col-12">
                  <label>Pago a Realizar <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
                    <input style="background-color:#333;" type="number" min="1" step="0.01" name="val" required id="val<?php echo $ca['sell_id']; ?>" class="form-control" placeholder="<?php echo (Core::$user->language=='ES') ? "Escribir Aquí" : "Write Here"; ?>">
                  </div>
                </div>
              </div>

              <div class="row my-2">
                
                <div class="col-md-12 col-12 my-2">
                  <?php if($_GET["pay"]>0): ?>
                    <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                  <?php else: ?>
                    <button disabled class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                  <?php endif; ?>
                </div>
              </div>
            </form>
          </div>
        </div>

<script>
$(document).ready(function(){
  $("#addpayment<?php echo $ca['sell_id']; ?>").submit(function(e){
    let total = parseFloat($("#total<?php echo $ca['sell_id']; ?>").val());
    let txtal = parseFloat($("#txtal<?php echo $ca['sell_id']; ?>").val());
    let val   = parseFloat($("#val<?php echo $ca['sell_id']; ?>").val());

    if(!isNaN(val) && val > 0){
      if(val <= txtal){
        let go = confirm("¿Está seguro que desea continuar?");
        if(!go){ e.preventDefault(); }
      } else {
        alert("No es posible ingresar un pago mayor a la deuda total.");
        e.preventDefault();
      }
    } else {
      alert("Debes ingresar un valor mayor que 0.");
      e.preventDefault();
    }
  });
});
</script>

      <?php endif; endforeach; ?>
    <?php endif; ?>
  </div>
</div>
</section>


<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="renew"):
$client = PersonData::getById($_GET["id"]);
$sells = BookingData::getAllBySQL("WHERE type_id=2 AND person_id=".$client->id." AND stock_id=".StockData::getPrincipal()->id." ORDER BY id DESC");

$total=0;
$credit_array = [];

foreach ($sells as $sell) {
    $tx   = PaymentData::sumBySellId2($sell->id,StockData::getPrincipal()->id)->total;
    $cars = CarsData::getById($sell->car_id);

    if($tx >= 0){
        $credit_array[] = [
            "brand"   => $cars->getBrand()->name,
            "model"   => $cars->name,
            "token"   => $sell->getCars()->token,
            "sell_id" => $sell->id,
            "total"   => $tx,
            "txtal"   => $sell->total
        ];
        $total += $tx;
    }
}
?>

<section class="content">
<div class="row">
  <div class="col-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-asterisk"></i> Realizar Pago</h1>
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

    <div class="callout callout-warning" style="background-color:#222;">
      <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
      <?php echo (Core::$user->language=='ES') 
        ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
        : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
    </div>

    <p><b>NOMBRE:</b> <?php echo strtoupper($client->name." ".$client->lastname);?></p>
    <h3>Deuda total: <?php echo number_format($total,2,".",","); ?></h3>

    <?php if(count($credit_array)>0): ?>
      <?php foreach($credit_array as $ca): if($ca['total']>0): ?>
        <div class="card" style="background-color:#222;">
          <div class="card-body">
            <form method="post" id="addpayment<?php echo $ca['sell_id']; ?>" action="./?action=add&opt=contractrenew">
              <input type="hidden" name="sell_id" value="<?php echo $ca['sell_id'];?>">
              <input type="hidden" name="client_id" value="<?php echo $client->id; ?>">
              <input type="hidden" name="total" id="total<?php echo $ca['sell_id']; ?>" value="<?php echo round($ca['total']); ?>">
              <input type="hidden" name="txtal" id="txtal<?php echo $ca['sell_id']; ?>" value="<?php echo $ca['txtal']; ?>">

              <div class="row">
                <div class="col-md-6 col-12">
                  <label>Forma de Pago</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-list-ol"></i></span>
                    <select style="background-color:#333;" name="f_id" class="form-control">
                      <?php foreach(FData::getAll() as $f): ?>
                        <option value="<?php echo $f->id;?>"><?php echo $f->name;?></option>
                      <?php endforeach;?>
                    </select>
                  </div>
                </div>

                <div class="col-md-6 col-12">
                  <label>Pago a Realizar <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
                    <input style="background-color:#333;" type="text" class="form-control" 
                           name="val" id="val<?php echo $ca['sell_id']; ?>" 
                           value="<?php echo $ca['txtal'] ?>" readonly>
                  </div>
                </div>
              </div>

              <div class="row my-2">
              
                <div class="col-md-12 col-12 my-2">
                   <button class="btn btn-primary btn-block btn-sm">
                     <i class="fa fa-check"></i> Finalizar
                   </button>
                </div>
              </div>
            </form>
          </div>
        </div>

<script>
$(document).ready(function(){
  $("#addpayment<?php echo $ca['sell_id']; ?>").submit(function(e){
    let val = parseFloat($("#val<?php echo $ca['sell_id']; ?>").val());
    if(isNaN(val) || val <= 0){
      alert("Debes ingresar un valor válido.");
      e.preventDefault();
    } else {
      let go = confirm("¿Está seguro que desea continuar?");
      if(!go){ e.preventDefault(); }
    }
  });
});
</script>

      <?php endif; endforeach; ?>
    <?php endif; ?>
  </div>
</div>
</section>



<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="paymentstock"):

$client = PersonData::getById($_GET["id"]);
$sells = BookingData::getCreditByClientId($client->id,$_GET["stock"]);
$total=0;
$credit_array = array();
foreach ($sells as $sell) {
  $tx = PaymentData::sumBySellId2($sell->id,$_GET["stock"])->total;
  $cars= CarsData::getById($sell->car_id);
  if($tx>=0){
    $credit_array[] = array(
      "brand"=>$cars->getBrand()->name,
      "model"=>$cars->name,
      "token"=>$sell->getCars()->token,
      "sell_id"=>$sell->id,
      "total"=>$tx,
      "txtal"=>$sell->total
    );
    $total+=$tx;
  }
}

?>

<section class="content">
<div class="row">
  <div class="col-12">
  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-asterisk"></i> Realizar Pago</h1>
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
 <div class="callout callout-warning" style="background-color:#222;">
              <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
             <?php echo (Core::$user->language=='ES') 
                ? " Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
                : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
            </div>
 
 <p>NOMBRE: <?php echo strtoupper($client->name." ".$client->lastname);?></p>

  <?php if(count($credit_array)>0):?>
    <?php foreach($credit_array as $ca):?>
 <div class="card" style="background-color:#222;">
<div class="card-body">
    <form class="form-horizontal" method="post" enctype="multipart/form-data" id="addpayment<?php echo $ca['sell_id']; ?>" action="./?action=add&opt=paymentstock" role="form">
<input style="background-color:#333;" type="hidden" name="sell_id" value="<?php echo $ca['sell_id'];?>">
<input style="background-color:#333;" type="hidden" name="client_id" class="form-control"  value="<?php echo $client->id; ?>">

<div class="row">
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total de Renta: <?php echo $ca['brand']." ".$ca['model']." [".$ca['token']."]"; ?></label>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
          <input style="background-color:#333;" type="text" autocomplete="off" id="txtal<?php echo $ca['sell_id']; ?>"  class="form-control"  value="<?php echo $ca['txtal'] ?>" readonly>
        </div>
    </div>

   <div class="col-md-6 col-12">
      <label for="inputEmail1" class="col-12 col-md-12 control-label">Foma de Pago</label>
           <div class="input-group">
              <span class="input-group-text"><i class="fa fa-list-ol"></i></span>
              <select style="background-color:#333;" name="f_id" class="form-control">
                <?php foreach(FData::getAll() as $f):?>
                  <option value="<?php echo $f->id;?>"><?php echo $f->name;?></option>
                <?php endforeach;?>
              </select>
           </div>
    </div>
  </div>
  
<div class="row">
    <div class="col-md-6 col-12">
      <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Deuda</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
        <input style="background-color:#333;" type="text" class="form-control" value="$ <?php echo round($ca['total']); ?>" readonly>
        <input style="background-color:#333;" type="hidden" name="total" id="total<?php echo $ca['sell_id']; ?>" value="<?php echo round($ca['total']); ?>">
      </div>
    </div>

    <div class="col-md-6 col-12">
      <label for="inputEmail1" class="col-12 col-md-12 control-label">Pago a Realizar <span class="text-danger">*</span></label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
        <input style="background-color:#333;" type="text" autocomplete="off" autofocus name="val" required id="val<?php echo $ca['sell_id']; ?>" class="form-control" placeholder="<?php echo (Core::$user->language=='ES') ? "Escribir Aquí" : "Write Here"; ?>">
      </div>
    </div>
</div>

<div class="row my-2">
  
                <div class="col-md-12 col-12 my-2">
    <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
  </div>
</div>
</form>
</div>
</div>
<script>
  $(document).ready(function(){
    $("#addpayment<?php echo $ca['sell_id']; ?>").submit(function(e){
      let total = $("#total<?php echo $ca['sell_id']; ?>").val();
      let txtal = $("#txtal<?php echo $ca['sell_id']; ?>").val();
      let val = $("#val<?php echo $ca['sell_id']; ?>").val();
      if( val!="" && val>0 ){
        if(parseFloat(val) == parseFloat(txtal)){
          let go = confirm("¿Está seguro que desea continuar?");
          if(!go){ e.preventDefault(); }
        }else{
          alert("Solo se permite cancelar el total de la deuda.");
          e.preventDefault();          
        }
      }else{
        alert("Debes ingresar un valor mayor que 0.");
        e.preventDefault();
      }
    });
  });
</script>
<?php endforeach; ?>
<?php endif; ?>

  </div>
</div>

</section>


<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="finished"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-edit'></i> <?php echo (Core::$user->language=='ES') ? "Listado de Contratos" : "List of Contracts"; ?></h1>
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

<div class="callout callout-warning" style="background-color:#222;">
  <h5><i class="fas fa-info"></i> <?php echo (Core::$user->language=='ES') ? "Nota" : "Note"; ?>:</h5>
  <?php echo (Core::$user->language=='ES') 
      ? "Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo." 
      : "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; ?>
</div>

<?php 
$base = new Database();
$con = $base->connect();
$sql = "SELECT SQL_BIG_RESULT * FROM booking WHERE status=3 AND stock_id=".StockData::getPrincipal()->id;
$query = $con->query($sql);
if($query && $query->num_rows > 0): 
  $TicketMm = StockData::getPrincipal()->ticket_mm;
?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
  <thead style="background-color: #333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Acción" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehículo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Día" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Día" : "Day"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name; ?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%";?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Recibir" : "Receive"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Acción" : "Action"; ?></th>
    </tr>
  </thead>

  <tfoot style="background-color:#333;">
    <tr>
      <th><?php echo (Core::$user->language=='ES') ? "Acción" : "Action"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Vehículo" : "Vehicle"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Cliente" : "Customer"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Precio/Día" : "Price/Day"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Día" : "Day"; ?></th>
      <th>SubTotal</th>
      <th><?php echo StockData::getPrincipal()->imp_name; ?> (<?php echo StockData::getPrincipal()->imp_val;?>%)</th>
      <th><?php echo (Core::$user->language=='ES') ? "Abonado" : "Subscriber"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Restante" : "Remaining"; ?></th>
      <th>Extra</th>
      <th><?php echo (Core::$user->language=='ES') ? "Otros" : "Other"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Tarjeta" : "Card"; ?> (<?php echo StockData::getPrincipal()->card."%";?>)</th>
      <th>RentCar</th>
      <th><?php echo (Core::$user->language=='ES') ? "Entrega" : "Delivery"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Recibir" : "Receive"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Creado Por" : "Created By"; ?></th>
      <th><?php echo (Core::$user->language=='ES') ? "Acción" : "Action"; ?></th>
    </tr>
  </tfoot>

<?php while($r = $query->fetch_array()){
  $totpayments = 0;
  $payments = PaymentData::getByPayment($r['id']);
  $totpayments = $payments[0]->t ?? 0;
  $cars1 = CarsData::getById($r['car_id']);
  $cars2 = CarsData::getById($r['car2_id']);
?>
<tr>
  <td class="text-right py-0 align-middle">
    <div class="btn-group btn-group-sm btn-block">
      <!-- Botón visible en todos los dispositivos -->
      <a href="<?php echo $TicketMm; ?>/ticket.php?id=<?php echo $r['id']; ?>"
         class="btn btn-info btn-sm"
         onclick="return manejarVisualizacionPDF(this.href, event)">
         <i class="fa fa-eye"></i>
      </a>
    </div>
  </td>

  <td>
    <?php  
      if ($totpayments==0) echo '<span class="description-percentage text-danger"><i class="fas fa-caret-right"></i></span>'; 
      elseif ($totpayments>0 && $totpayments<$r['total']) echo '<span class="description-percentage text-warning"><i class="fas fa-caret-right"></i></span>'; 
      elseif ($r['total']==$totpayments) echo '<span class="description-percentage text-success"><i class="fas fa-caret-right"></i></span>'; 

      if ($r['car2_id']==0): 
        echo BrandData::getById($cars1->brand_id)->name." ".$cars1->name." ".$cars1->year." ".ColorData::getById($cars1->exterior_id)->name." [".$cars1->token."]"; 
      else:  
        echo BrandData::getById($cars2->brand_id)->name." ".$cars2->name." ".$cars2->year." ".ColorData::getById($cars2->exterior_id)->name." [".$cars2->token."]"; 
      endif;
    ?>
  </td>

  <td><?php echo PersonData::getById($r['person_id'])->name; ?></td>
  <td><?php echo number_format($r['price'],2,".",","); ?></td>
  <td><?php echo $r['day']; ?></td>
  <td><?php echo number_format($r['total'],2,".",","); ?></td>
  <td><?php echo number_format((($r['price']*$r['day'])+$r['xtotal'])*($r['iva']/100),2,".",","); ?></td>
  <td><?php echo number_format($totpayments,2,".",","); ?></td>
  <td><?php echo number_format(($r['total']-$totpayments),2,".",","); ?></td>
  <td><?php echo number_format($r['xtotal'],2,".",","); ?></td>
  <td><?php echo number_format($r['plane'],2,".",","); ?></td>
  <td><?php echo number_format($r['total']*($r['card']/100),2,".",","); ?></td>
  <td><?php echo StockData::getById($r['stock_id'])->name; ?></td>
  <td><?php echo $r['start_at']; ?></td>
  <td><?php echo $r['end_at']; ?></td>
  <td><?php echo UserData::getById($r['user_id'])->name." ".UserData::getById($r['user_id'])->lastname; ?></td>
  <td class="text-right py-0 align-middle">
    <?php  
    $sl = "SELECT SQL_BIG_RESULT * FROM permits_user WHERE user_id=".$_SESSION["user_id"];
    $qry = $con->query($sl); 
    while($x = $qry->fetch_array()){
      if ($x['permits_id']==4): ?>
        <a href="./?action=contract&opt=del&id=<?php echo $r['id'];?>&cars=<?php echo $r['car_id'];?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();"><i class="fas fa-trash"> Eliminar</i></a>
        <script>
          function confirmDelete() {
              return confirm("¿Estás seguro de que deseas eliminar este registro?");
          }
        </script>
    <?php endif; } ?>
  </td>
</tr>
<?php }; ?>
</table>
</div>
</div>
</div>

<!-- Modal PDF -->
<div id="modalPDF" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:#000a; z-index:9999;">
  <div style="position:relative; width:90%; height:90%; margin:5% auto; background:#1e1e1e; border-radius:10px; overflow:hidden; padding-top:80px;">
    <div style="position:absolute; top:20px; right:20px; display:flex; flex-direction:column; gap:15px; z-index:1000;">
      <button onclick="imprimirPDF()" style="background:#28a745; color:#fff; border:none; padding:12px 20px; border-radius:40px; font-weight:bold; font-size:16px;"><i class="fa fa-print"></i> IMPRIMIR</button>
      <a id="btnDescargar" href="#" download style="background:#007bff; color:#fff; padding:12px 20px; border-radius:40px; font-weight:bold; font-size:16px; text-decoration:none;"><i class="fa fa-download"></i> DESCARGAR</a>
      <button onclick="cerrarPDF()" style="background:#c40030; color:#fff; border:none; padding:12px 20px; border-radius:40px; font-weight:bold; font-size:16px;"><i class="fa fa-times"></i> CERRAR</button>
    </div>
    <iframe id="iframePDF" src="" style="position:absolute; top:0; left:0; width:100%; height:100%; border:none;"></iframe>
  </div>
</div>

<script>
function manejarVisualizacionPDF(url, event) {
  const esPC = window.innerWidth >= 1024;
  if (esPC) {
    event.preventDefault();
    document.getElementById('iframePDF').src = url;
    document.getElementById('btnDescargar').href = url;
    document.getElementById('modalPDF').style.display = 'block';
    return false;
  }
  return true;
}
function cerrarPDF() {
  document.getElementById('modalPDF').style.display = 'none';
  document.getElementById('iframePDF').src = '';
  document.getElementById('btnDescargar').href = '#';
}
function imprimirPDF() {
  const iframe = document.getElementById('iframePDF');
  iframe.focus();
  iframe.contentWindow.print();
}
</script>

<?php else: ?>
<div class="card" style="background-color:#222;">
  <div class="card-header">
    <h2>No hay Contratos</h2>
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


<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="modal"):?>

<section class="content">
<div class="row">
  <div class="col-12">
  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-edit"></i> Contrato de Vehiculo</h1>
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
<?php
$user = BookingData::getById($_GET["id"]);
$TicketMm = StockData::getPrincipal()->ticket_mm;
$url = $TicketMm . "/ticket.php?id=" . $user->id;?>
<!-- Contenedor principal -->
<div style="position:relative; width:100%; height:100vh; background:#1e1e1e;">

  <!-- Botones flotantes dentro del contenedor -->
  <div style="position:absolute; top:20px; right:20px; background:#111c; padding:10px; border-radius:12px; z-index:10; box-shadow: 0 0 10px rgba(0,0,0,0.5); display:flex; flex-direction:column; gap:10px;">
    <button onclick="imprimirPDF()" style="background:#c40030; color:#fff; border:none; padding:10px 16px; border-radius:40px; font-weight:bold; font-size:15px; display:flex; align-items:center; gap:8px;">
      <i class="fa fa-print"></i> IMPRIMIR
    </button>
    <a id="btnDescargar" href="<?php echo $url; ?>" download style="background:#007bff; color:#fff; border:none; padding:10px 16px; border-radius:40px; font-weight:bold; font-size:15px; display:flex; align-items:center; gap:8px; text-decoration:none;">
      <i class="fa fa-download"></i> DESCARGAR
    </a>
    
    <a  href="./?action=booking&opt=what&id=<?php echo $user->id; ?>&person_id=<?php echo $user->person_id; ?>&car_id=<?php echo $user->car_id; ?>" style="background:#28a745; color:#fff; border:none; padding:10px 16px; border-radius:40px; font-weight:bold; font-size:15px; display:flex; align-items:center; gap:8px;">
      <i class="fab fa-whatsapp"></i> COMPARTIR
    </a>
   
  </div>

  <!-- Iframe del PDF -->
  <iframe id="iframePDF" src="<?php echo $url; ?>" 
    style="width:100%; height:100%; border:none; position:absolute; top:0; left:0; z-index:1;">
  </iframe>

</div>

<script>
function imprimirPDF() {
  const iframe = document.getElementById('iframePDF');
  iframe.focus();
  iframe.contentWindow.print();
}
</script>


  </div>
</div>

</section>



<?php endif; ?>