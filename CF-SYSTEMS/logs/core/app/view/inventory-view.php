
<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):
//////////////////////////////////////////// ALL //////////////////////////////////

$currency = "RD$";
$selstock = null;
if(isset($_GET["stock"])){ $selstock=$_GET["stock"]; }
else{ $selstock = StockData::getPrincipal()->id; }?>

<section class="content">
<div class="row">
  <div class="col-12">
     <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
           <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-archive'></i> Productos</h1>
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

        </div><!-- /.row -->

    
<div class="callout callout-purple" style="background-color:#222;" >
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo y en en titulo del aparatado para crear nuevo.
            </div>


<?php if(isset($_GET['id'])): $product = ProductData::getById($_GET["id"]); $q = ProductData::getByStoreId($_GET["id"]);?>
<div class="card"  style="background-color:#222;">
<div class="card-body">
<form class="form-horizontal" method="post" id="updproduct" enctype="multipart/form-data" role="form">



<div class="row">
     
    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Codigo Interno</label>
    <div class="input-group">
<span class="input-group-text"><i class="fa fa-barcode"></i></span>
      <input type="text" name="p_code" class="form-control" autocomplete="off"  placeholder="Codigo Interno" value="<?php echo $product->p_code; ?>" >
    </div>
  </div>
  
   <div class="col-md-5 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre <span class="text-danger">*</span></label>
      <div class="input-group">
<span class="input-group-text"><i class="fa fa-user"></i></span>
      <input type="text" name="p_name" autocomplete="off" required class="form-control"  placeholder="Nombre del Producto" value="<?php echo $product->p_name; ?>">
    </div>
  </div>


 
  <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vencimiento <span class="text-danger">*</span></label>
      <div class="input-group">
<span class="input-group-text"><i class="fa fa-credit-card"></i></span>
      <input type="date" name="p_date" autocomplete="off"  class="form-control" value="<?php echo $product->p_date; ?>">
    </div>
  </div>
 
   

<div class="col-md-3 col-12">
     <label for="inputEmail1" class="col-md-12 col-12 control-label">Suplidor</label>
       <div class="input-group">
    <select name="sup_id" class="form-control select2">
    <option value="">-- NINGUNO --</option>
     <?php foreach(PersonData::getProviders() as $providers):?>
      <option value="<?php echo $providers->id;?>" <?php if($q->sup_id!=null&& $q->sup_id==$providers->id){ echo "selected";}?>><?php echo $providers->name." ".$providers->lastname;?></option>
    <?php endforeach;?>
      </select> 
    </div>

  </div>


<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Marca</label>
      <div class="input-group">
    <select name="brand_id" class="form-control select2">
    <option value="">-- NINGUNA --</option>
    <?php foreach(BrandData::getAll() as $brand):?>
      <option value="<?php echo $brand->brand_id;?>" <?php if($q->brand_id!=null&& $q->brand_id==$brand->brand_id){ echo "selected";}?>><?php echo $brand->brand_name;?></option>
    <?php endforeach;?>
      </select>    
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
      Precio en Pesos (DOP) <span class="text-danger">*</span>
    </label>
    <div class="input-group">
      <span class="input-group-text">RD$</span>
      <input type="number" step="any" id="purchase_price" name="purchase_price"
             class="form-control" autocomplete="off" placeholder="Ej: 600" required value="<?php echo $q->purchase_price; ?>">
    </div>
  </div>

  <!-- USD -->
  <div class="col-md-3 col-12">
    <label for="usd_price" class="col-md-12 col-12 control-label">
      Equivalente en USD
    </label>
    <div class="input-group">
      <span class="input-group-text">$</span>
      <input type="text" id="usd_price" name="usd_price" class="form-control" readonly placeholder="0.00 USD" value="<?php echo $q->usd_price; ?>">
    </div>
  </div>

  <!-- Tasa -->
  <div class="col-md-3 col-12">
    <label for="tasa_dolar" class="col-md-12 col-12 control-label">
      Tasa Actual del Dólar
    </label>
    <div class="input-group">
      <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
      <input type="text" name="tasa_dolar" id="tasa_dolar" class="form-control" readonly 
             value="<?php echo $tasa ? number_format($tasa, 2) . ' DOP/USD' : 'Buscando...'; ?>">
    </div>
    <p id="ultima_actualizacion" style="color:#aaa; font-size:12px; margin-top:5px;">
      Última actualización: -
    </p>
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
  const inputPesos = document.getElementById("purchase_price");
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
      let dolares = pesos / tasa;
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


  <div class="col-md-3 col-12" >
  <label for="inputEmail1" class="col-md-12 col-12 control-label">Referencia:</label>
  <div class="input-group">
<span class="input-group-text"><i class="fa fa-clone"></i></span>
       <input type="text" name="p_model" autocomplete="off"  class="form-control" required  placeholder="CRV 2017-2022" value="<?php echo $product->p_model; ?>">
    </div>
  </div>



 <div class="col-md-3 col-12" id="q">
     <label for="inputEmail1" class="col-md-12 col-12 control-label">Disponiblidad:</label>
     <div class="input-group">
<span class="input-group-text"><i class="fa fa-spinner"></i></span>
      <input type="number" name="quantity_in_stock" required="" class="form-control"value="1" autocomplete="off" id="inputEmail1" placeholder="Inventario inicial" value="<?php echo $product->quantity_in_stock; ?>">
    </div>
  </div>

 <div class="col-md-3 col-12" id="minima">
       <label for="inputEmail1" class="col-md-12 col-12 control-label">Alerta de inventario:</label>
       <div class="input-group">
<span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
      <input type="number" name="alert_quantity" autocomplete="off" value="0" class="form-control" id="inputEmail1" placeholder="Alerta de Inventario (Default 10)" value="<?php echo $product->alert_quantity; ?>">
    </div>
   
  </div>
 
   
     <div class="col-md-3 col-12" >
      <br>
    <label>
       <div class="form-group clearfix">
                      <div class="icheck-primary d-inline">
                         <input type="checkbox" id="radioPrimary1" name="status" <?php if($q->status){ echo "checked";}?>>
                        <label for="radioPrimary1">
                        ¿Esta Activo?
                        </label>
                      </div>
                    
                    </div>
    </label>
    </div>
   
 <div class="card-body row">
                <div class="col-6">
                  <a href="./?view=products&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-6">
                     <input type="hidden" name="product_id" value="<?php echo $_GET["id"]; ?>">
                   <button type="submit" class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>

  </div>
</form>
                </div>
              </div>

<script>

            jQuery(document).ready(function(){
            jQuery("#updproduct").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=products&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Producto Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo a la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=inventory&opt=all'  }, delay); 
                     
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
 <div id="accordion">
   <div class="card card-secondary card-outline" style="background-color:#222;">
                    <a class="d-block w-100" data-toggle="collapse" href="#collapseThree" style="color:white;">
                        <div class="card-header">
                            <h4 class="card-title w-100">
                            <i class="fa fa-plus"></i>   CREAR NUEVO PRODUCTO
                            </h4>
                        </div>
                    </a>
                    <div id="collapseThree" class="collapse" data-parent="#accordion">
 
                        <div class="card-body">
    <form class="form-horizontal" method="post" enctype="multipart/form-data" id="addproduct" role="form">


<div class="row">
     
    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Codigo Interno</label>
    <div class="input-group">
<span class="input-group-text"><i class="fa fa-barcode"></i></span>
      <input type="text" name="p_code" class="form-control" autocomplete="off"  placeholder="Codigo Interno" >
    </div>
  </div>
  
   <div class="col-md-5 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre <span class="text-danger">*</span></label>
      <div class="input-group">
<span class="input-group-text"><i class="fa fa-user"></i></span>
      <input type="text" name="p_name" autocomplete="off" required class="form-control"  placeholder="Nombre del Producto">
    </div>
  </div>


 
  <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vencimiento <span class="text-danger">*</span></label>
      <div class="input-group">
<span class="input-group-text"><i class="fa fa-credit-card"></i></span>
      <input type="date" name="p_date" autocomplete="off"  class="form-control" >
    </div>
  </div>
 
   

<div class="col-md-3 col-12">
     <label for="inputEmail1" class="col-md-12 col-12 control-label">Suplidor</label>
       <div class="input-group">
    <select name="sup_id" class="form-control select2">
    <option value="">-- NINGUNO --</option>
     <?php foreach(PersonData::getProviders() as $providers):?>
      <option value="<?php echo $providers->id;?>"><?php echo $providers->name." ".$providers->lastname;?></option>
    <?php endforeach;?>
      </select> 
    </div>

  </div>


<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Marca</label>
      <div class="input-group">
    <select name="brand_id" class="form-control select2">
    <option value="">-- NINGUNA --</option>
    <?php foreach(BrandData::getAll() as $brand):?>
      <option value="<?php echo $brand->brand_id;?>"><?php echo $brand->name;?></option>
    <?php endforeach;?>
      </select>    
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
      Precio en Pesos (DOP) <span class="text-danger">*</span>
    </label>
    <div class="input-group">
      <span class="input-group-text">RD$</span>
      <input type="number" step="any" id="purchase_price" name="purchase_price"
             class="form-control" autocomplete="off" placeholder="Ej: 600" required>
    </div>
  </div>

  <!-- USD -->
  <div class="col-md-3 col-12">
    <label for="usd_price" class="col-md-12 col-12 control-label">
      Equivalente en USD
    </label>
    <div class="input-group">
      <span class="input-group-text">$</span>
      <input type="text" id="usd_price" name="usd_price" class="form-control" readonly placeholder="0.00 USD">
    </div>
  </div>

  <!-- Tasa -->
  <div class="col-md-3 col-12">
    <label for="tasa_dolar" class="col-md-12 col-12 control-label">
      Tasa Actual del Dólar
    </label>
    <div class="input-group">
      <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
      <input type="text" name="tasa_dolar" id="tasa_dolar" class="form-control" readonly 
             value="<?php echo $tasa ? number_format($tasa, 2) . ' DOP/USD' : 'Buscando...'; ?>">
    </div>
    <p id="ultima_actualizacion" style="color:#aaa; font-size:12px; margin-top:5px;">
      Última actualización: -
    </p>
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
  const inputPesos = document.getElementById("purchase_price");
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
      let dolares = pesos / tasa;
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


  <div class="col-md-3 col-12" >
  <label for="inputEmail1" class="col-md-12 col-12 control-label">Referencia:</label>
  <div class="input-group">
<span class="input-group-text"><i class="fa fa-clone"></i></span>
       <input type="text" name="p_model" autocomplete="off"  class="form-control" required  placeholder="CRV 2017-2022">
    </div>
  </div>



 <div class="col-md-3 col-12" id="q">
     <label for="inputEmail1" class="col-md-12 col-12 control-label">Disponiblidad:</label>
     <div class="input-group">
<span class="input-group-text"><i class="fa fa-spinner"></i></span>
      <input type="number" name="quantity_in_stock" required="" class="form-control"value="1" autocomplete="off" id="inputEmail1" placeholder="Inventario inicial">
    </div>
  </div>

 <div class="col-md-3 col-12" id="minima">
       <label for="inputEmail1" class="col-md-12 col-12 control-label">Alerta de inventario:</label>
       <div class="input-group">
<span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
      <input type="number" name="alert_quantity" autocomplete="off" value="0" class="form-control" id="inputEmail1" placeholder="Alerta de Inventario (Default 10)">
    </div>
   
  </div>
 
   
 <div class="card-body row">
                <div class="col-6">
                  <a href="./?view=products&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-6">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>

  </div>
</form>

<script>

            jQuery(document).ready(function(){
            jQuery("#addproduct").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=products&opt=add",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Producto Exito!", { sticky: true });
                  $.jGrowl("Se Agrego a la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=inventory&opt=all'  }, delay); 
                     
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


<?php endif;?>

<div class="card"  style="background-color:#222;">
<div class="card-body">
<div class="col-md-12">
<div class="row">
    
<div class="col-md-3">
  <button type="button" class="btn btn-warning btn-block my-2" data-toggle="modal" data-target="#modal-secondary01"><i class="fas fa-edit"></i> REGISTRAR SALIDA</button>
</div>  



      <div class="modal fade" id="modal-secondary01">
        <div class="modal-dialog">
          <div class="modal-content bg-secondary">
            <div class="modal-header" style="background-color: #222;">
              <h4 class="modal-title"><i class="fa fa-edit"></i>  Registrar Salida de Producto</h4>
            </div>
            
    <form method="post" id="exitproduct" enctype="multipart/form-data">     
    <div class="modal-body" style="background-color: #222;">
    
    <div class="row">
    <div class="col-md-12 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Seleccionar Producto</label>
    <div class="input-group">
  <select name="product_id" class="form-control select2">
  <option value="" selected disabled>ELEGIR PRODUCTO</option>
<?php 
$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT p_id, p_name from products";
$query = $con->query($sql);
while($s = $query->fetch_array()):?>
  <option value="<?php echo $s["p_id"];?>"><?php echo $s["p_name"]?></option>
  <?php endwhile; ?>
</select>
</div>
    </div>
    
    
    <div class="col-md-8 col-8">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Motivo</label>
    <div class="input-group">
  <select name="type" class="form-control select2" required>
  <option value="" selected disabled>ELEGIR MOTIVO</option>
  <option value="POR AJUSTE DE INVENTARIO">POR AJUSTE DE INVENTARIO</option>
  <option value="POR AVERIA DE PRODUCTO">POR AVERIA DE PRODUCTO</option>
  <option value="POR CANJE DE SUPLIDOR">POR CANJE DE SUPLIDOR</option>
</select>
</div>
    </div>
    
    
     <div class="col-md-4 col-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Cantidad</label>
    <div class="input-group">
  <input type="number" required autocomplete="off" name="quantity_in_stock" class="form-control" style="width:50px;">
</div>
    </div>
    
    </div>
            </div>
            <div class="modal-footer justify-content-between"  style="background-color: #222;">
              <button  class="btn btn-outline-light" data-dismiss="modal" style="background-color: #cf89ff; border: solid #cf89ff;">Cerrar</button>
              <button type="submit" class="btn btn-outline-light" style="background-color: #cf89ff; border: solid #cf89ff;"><i class="fa fa-check"></i> Actualizar</button>
            </div>
            </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->

<script>
            jQuery(document).ready(function(){
            jQuery("#exitproduct").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=products&opt=exit",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Salida Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=inventory&opt=all'  }, delay); 
                     
                  }else{
                  $.jGrowl("No se Puede duplicar datos", { header: 'Error al Agregar' });
                  }
                  }
                });
                return false;
              });
            });
            </script>


<div class="col-md-3">
  <button type="button" class="btn btn-success btn-block my-2" data-toggle="modal" data-target="#modal-secondary02"><i class="fas fa-edit"></i> REGISTRAR ENTRADA</button>
</div>  





      <div class="modal fade" id="modal-secondary02">
        <div class="modal-dialog">
          <div class="modal-content bg-secondary">
            <div class="modal-header" style="background-color: #222;">
              <h4 class="modal-title"><i class="fa fa-edit"></i>  Registrar Entrada de Producto</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            
    <form method="post" id="entranceproduct" enctype="multipart/form-data">     
    <div class="modal-body" style="background-color: #222;">
    
    <div class="row">
    <div class="col-md-12 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Seleccionar Producto</label>
    <div class="input-group">
  <select name="product_id" class="form-control select2">
  <option value="" selected disabled>ELEGIR PRODUCTO</option>
<?php 
$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT p_id, p_name from products";
$query = $con->query($sql);
while($e = $query->fetch_array()):?>
  <option value="<?php echo $e["p_id"];?>"><?php echo $e["p_name"]?></option>
  <?php endwhile; ?>
</select>
</div>
    </div>
    
    
    <div class="col-md-8 col-8">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Motivo</label>
    <div class="input-group">
  <select name="type" class="form-control select2" required>
  <option value="" selected disabled>ELEGIR MOTIVO</option>
  <option value="POR INVENTARIO INICIAL">POR INVENTARIO INICIAL</option>
  <option value="POR AJUSTE DE INVENTARIO">POR AJUSTE DE INVENTARIO</option>
  <option value="POR PROMOCIONAL DE SUPLIDOR">POR PROMOCIONAL DE SUPLIDOR</option>
  <option value="POR CANJE DE SUPLIDOR">POR CANJE DE SUPLIDOR</option>
</select>
</div>
    </div>
    
    
     <div class="col-md-4 col-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Cantidad</label>
    <div class="input-group">
  <input type="number" required autocomplete="off" name="quantity_in_stock" class="form-control" style="width:50px;">
</div>
    </div>
    
    </div>
            </div>
            <div class="modal-footer justify-content-between"  style="background-color: #222;">
              <button  class="btn btn-success" data-dismiss="modal" >Cerrar</button>
              <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Actualizar</button>
            </div>
            </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->

<script>
            jQuery(document).ready(function(){
            jQuery("#entranceproduct").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=products&opt=entrance",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Entrada Exito!", { sticky: true });
                  $.jGrowl("Se Agrego la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=inventory&opt=all'  }, delay); 
                     
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

<script>
      $.get("./?action=get&opt=products&stock=<?php echo $selstock; ?>","",function(data2){
        $("#allproducts").html(data2);
        console.log(data2);
      });
</script>

<div id="allproducts"></div>


</div><!-- /.container-fluid -->
    </div>
</div><!-- /.container-fluid -->
    </div>
</section>
<?php endif; ?>

