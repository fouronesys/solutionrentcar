
<?php $stock = StockData::getPrincipal();?>
<section class="content">
<div class="row">
	<div class="col-md-12">
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-wrench'></i> Ajustes Generales
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
<div class="card-body">
<form class="form-horizontal" method="post" action="./?action=update&opt=settings" role="form" enctype="multipart/form-data">
    
 <div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Logo</label>
      <input style="background-color:#333;" type="file" name="image" >
    </div>

    <div class="col-md-6 col-12">
   <?php if(isset($stock->ticket_image)):?>
  <img src="CF-SYSTEMS/storage/configuration/<?php echo $stock->ticket_image;?>" style="width:20%;">
       <?php endif;?>
  </div>
 
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Ubicacion</label>
    <select style="background-color:#333;" name="location" required class="form-control select2" id="location" >
    <?php if(StockData::getPrincipal()->update=="1"): 
    
    foreach(StatesData::getAllWithCountry() as $state): ?>
        <option value="<?php echo $state->id; ?>" <?php if($state->id==$stock->location){ echo "selected"; }?>>
            <?php echo $state->state_name . ' (' . $state->country_name . ')'; ?>
        </option>
    <?php endforeach; 
    
    else:
        
    foreach(LocationData::getAll() as $state): ?>
        <option value="<?php echo $state->id; ?>" <?php if($state->id==$stock->location){ echo "selected"; }?>>
            <?php echo $state->name; ?>
        </option>
    <?php endforeach; 
    
    endif;?>
     
      </select>
    </div>

    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre <span class="text-danger">*</span></label>
      <div class="input-group">
<span class="input-group-text"  style="background-color:orange;"><i class="fa fa-user"></i></span>
    <input style="background-color:#333;" type="text" name="name" required class="form-control" value="<?php echo $stock->name; ?>" placeholder="Nombre">
    </div>
  </div>


    <div class="col-md-12 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Direccion <span class="text-danger">*</span></label>
    <div class="input-group">
<span class="input-group-text"  style="background-color:orange;"><i class="fa fa-street-view"></i></span>
     <input style="background-color:#333;" type="text" name="address"  required class="form-control" value="<?php echo $stock->address; ?>" placeholder="Direccion">
    </div>
  </div>
  
  <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">RNC/NIE</span></label>
    <div class="input-group">
<span class="input-group-text"  style="background-color:orange;"><i class="fa fa-recycle"></i></span>
     <input style="background-color:#333;" type="text" name="rnc"  class="form-control" value="<?php echo $stock->rnc; ?>" placeholder="RNC/NIE">
    </div>
  </div>
  
  <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Color</span></label>
    <div class="input-group">
<span class="input-group-text"  style="background-color:orange;"><i class="fa fa-tint"></i></span>
     <input style="background-color:#333;" type="text" name="color"  class="form-control" value="<?php echo $stock->color; ?>" data-inputmask='"mask": "999,999,999"' data-mask>
    </div>
  </div>


     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Whatsapp <span class="text-danger">*</span></label>
    <div class="input-group">
<span class="input-group-text"  style="background-color:orange;"><i class="fa fa-comment"></i></span>
     <input style="background-color:#333;" type="text" name="phone"  class="form-control" placeholder="Whatsapp" value="<?php echo $stock->phone; ?>" data-inputmask='"mask": "(999) 999-9999"' data-mask>
    </div>
  </div>

    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Celular </label>
      <div class="input-group">
<span class="input-group-text"  style="background-color:orange;"><i class="fa fa-phone"></i></span>
   <input style="background-color:#333;" type="text" name="phone2"  class="form-control" value="<?php echo $stock->phone2; ?>"   placeholder="Telefono" data-inputmask='"mask": "(999) 999-9999"' data-mask>
    </div>
  </div>



    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">WEB </label>
      <div class="input-group">
<span class="input-group-text"  style="background-color:orange;"><i class="fa fa-globe"></i></span>
  <input style="background-color:#333;" type="text" name="field1"  class="form-control" value="<?php echo $stock->field1; ?>" placeholder="Pagina Web">
    </div>
  </div>

    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Instagram</label>
    <div class="input-group">
<span class="input-group-text"  style="background-color:orange;"><i class="fa fa-comment"></i></span>
    <input style="background-color:#333;" type="text" name="field2"  class="form-control" value="<?php echo $stock->field2; ?>"  placeholder="Instagram">
    </div>
  </div>


    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre Impuesto </label>
      <div class="input-group">
<span class="input-group-text"  style="background-color:orange;"><i class="fa fa-edit"></i></span>
  <input style="background-color:#333;" type="text" name="imp_name"  class="form-control" value="<?php echo $stock->imp_name; ?>" placeholder="Nombre Impuesto">
    </div>
  </div>

    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Valor Impuesto</label>
    <div class="input-group">
<span class="input-group-text"  style="background-color:orange;"><i class="fa fa-ellipsis-h "></i></span>
    <input style="background-color:#333;" type="text" name="imp_val"  class="form-control" value="<?php echo $stock->imp_val; ?>"  placeholder="Instagram">
    </div>
  </div>
  
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Metodo</label>
    <div class="input-group">
<span class="input-group-text"  style="background-color:orange;"><i class="fa fa-copy "></i></span>
     <select style="background-color:#333;" name="method" required class="form-control">
    <option value="1"<?php if($stock->method=="1"){ echo "selected"; }?>>RENTALS</option>
    <option value="2"<?php if($stock->method=="2"){ echo "selected"; }?>>UBER / INDRIVE</option>
      </select>
    </div>
  </div>
  
      <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Moneda</label>
     <div class="input-group">
    <span class="input-group-text"  style="background-color:orange;"><i class="fa fa-money-bill-alt"></i></span>
    <select style="background-color:#333;" name="currency" required class="form-control">
    <option value="USD"<?php if($stock->currency=="USD"){ echo "selected"; }?>>DOLAR</option>
    <option value="RD"<?php if($stock->currency=="RD"){ echo "selected"; }?>>PESOS</option>
      </select>
    </div>
     </div>

    <div class="col-md-12 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Frame Ubicacion </label>
      <div class="input-group">
<span class="input-group-text"  style="background-color:orange;"><i class="fa fa-random"></i></span>
  <textarea style="background-color:#333;" type="text" name="frame"  class="form-control" placeholder="Frame Ubicacion"><?php echo $stock->frame; ?></textarea>
    </div>
  </div>
  
   <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Correo Electronico</label>
  <input style="background-color:#333;" type="text" name="email" required  class="form-control" placeholder="(%) de Tarjeta" value="<?php echo $stock->email; ?>">
    </div>


  <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">(%) de Tarjeta</label>
      <div class="input-group">
<span class="input-group-text"  style="background-color:orange;"><i class="fa fa-money-check-alt"></i></span>
  <input style="background-color:#333;" type="text" name="card" required  class="form-control" placeholder="(%) de Tarjeta" value="<?php echo $stock->card; ?>">
    </div>
  </div>
  
  
   <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre Abogado</label>
      <div class="input-group">
<span class="input-group-text"  style="background-color:orange;"><i class="fa fa-user"></i></span>
  <input style="background-color:#333;" type="text" name="notario"  class="form-control" placeholder="Nombre Abogado" value="<?php echo $stock->notario; ?>">
    </div>
  </div>



</div>
 <div class="row my-2">
               
                <div class="col-md-12 col-12">
                  <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $stock->id;?>">
                   <button class="btn btn-warning btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
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

</section>