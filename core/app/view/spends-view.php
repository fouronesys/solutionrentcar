<?php if(isset($_GET["opt"]) && $_GET["opt"]=="vehicle"):
///////////////////////////////////////////////////////// NEWSPENDS ///////////////////////////
?>
<section class="content">
<div class="row">
  <div class="col-md-12">
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-minus-square"></i> Gastos de Vehiculos</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-briefcase'></i> Finanzas</a></li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Gastos</a></li>

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
         <?php $client = CarsData::getById($_GET["car_id"]);?>    
    <h4>VEHICULO: <?php echo strtoupper($client->getBrand()->name." ".$client->name." ".$client->year." ".$client->getExColor()->name." - ".$client->chassis);?></h4>        
            
    <div class="card" style="background-color:#222;">
<div class="card-body">
 
<form method="post" class="form-horizontal" id="maintenance" enctype="multipart/form-data">

<div class="row">
 <div class="col-md-12 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Factura del Gasto: &nbsp; &nbsp;   <input style="background-color:#333;" type="file" name="image"></label>
    </div>
    
    <div hidden class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vehiculo</label>
      <?php $clients = CarsData::getAll();?>
    <select style="background-color:#333;" name="car_id" class="form-control select2" required>
    
      <option value="<?php echo $client->id;?>"><?php echo $client->getBrand()->name." ".$client->name." ".$client->year." ".$client->getExColor()->name." - ".$client->chassis;?></option>
 
      </select>
    </div>
    
     <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Tipo de Gasto</label>
    <select style="background-color:#333;" name="type_id" class="form-control select2" id="type_id"  onchange="showInp()">
      <option value="">-- ELEGIR --</option>
    <?php foreach(MData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

   
    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Usuario</label>
    <select style="background-color:#333;" name="user_id" class="form-control select2" required>
        <option value="">-- ELEGIR --</option>
    <?php foreach(UserData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->user_id!=null&& $user->user_id==$client->id){ echo "selected";}?>><?php echo $client->name." ".$client->lastname;?></option>
    <?php endforeach;?>
      </select>
    </div>


   <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Forma</label>
    <select style="background-color:#333;" name="f_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach(FData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name." ".$client->lastname;?></option>
    <?php endforeach;?>
      </select>
    </div>
  
    
     

 <div class="col-md-2 col-12" id="custom_id">

    <label for="inputEmail1" class="col-md-12 col-12 control-label">Kms/Actual</label>
     <input style="background-color:#333;" type="number" class="form-control" autocomplete="off"   name="kms" placeholder="Kms/Actual">
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

  <!-- Tasa -->
  <div class="col-md-3 col-12">
    <label for="tasa_dolar" class="col-md-12 col-12 control-label">
      Tasa Actual del Dólar
    </label>
    <div class="input-group">
      <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
      <input type="text" name="cup_dolar" id="tasa_dolar" class="form-control" readonly 
             value="<?php echo $tasa ? number_format($tasa, 2) . ' DOP/USD' : 'Buscando...'; ?>">
    </div>
    
  </div>

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
      <input type="text" id="usd_price" name="total" class="form-control" readonly placeholder="0.00 USD" value="<?php echo $q->usd_price; ?>">
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
    
   
  
     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha</label>
     <input style="background-color:#333;" type="datetime-local" name="created_at"  required class="form-control">
    </div>
    <p id="ultima_actualizacion" style="color:#aaa; font-size:12px; margin-top:5px;">
      Última actualización: -
    </p>
     <div class="col-md-12 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Descripcion del Gasto</label>
     <textarea style="background-color:#333;"  type="text" class="form-control" autocomplete="off"  name="maintenance" placeholder="Descripcion del Gasto"></textarea>
    </div>
</div>
 <div class="row my-2">
                <div class="col-md-6 col-6">
                   <a href="./?view=finance&opt=vehicle" class="btn btn-warning btn-block btn-sm">Regresar</a>
                </div>
                <div class="col-md-6 col-6">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
              </form>
     

  </div>
</div>

<div id="allproducts"></div>


</div>
</div>
</div>
</div>
</section>
<script>

  

  $.get("./?action=get&opt=vehicule","",function(data2){
        $("#allproducts").html(data2);
   });
        
     $("#maintenance").submit(function(e){
            e.preventDefault();
            $.post("./?action=maintenance&opt=add",$("#maintenance").serialize(),function(data){
                
            $.jGrowl("Gasto Exito!", { header: 'Acceso permitido' });
            
            $.get("./?action=get&opt=vehicule","",function(data2){
                $("#allproducts").html(data2);
            });

            });


        });
        
document.getElementById("custom_id").style.display = "none";

function showInp(){
  var getSelectValue = document.getElementById("type_id").value;
 
  if(getSelectValue=="2"){
document.getElementById("custom_id").style.display = "inline-block";

      
  }else{
document.getElementById("custom_id").style.display = "none";
  }
  
}


  
   Number.prototype.format = function(n, x, s, c) {
                let re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
                    num = this.toFixed(Math.max(0, ~~n));
                return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
            };
            // Restricts input for the given textbox to the given inputFilter.
            function setInputFilter(textbox, inputFilter) {
                ["input"].forEach(function(event) {
                    textbox.addEventListener(event, function() {
                        if (this.id === "inputFormatoNumerico") {
                            if (this.value !== "") {
                                let str = this.value;
                                let oldstr= str.substring(0, str.length - 1);
                                let millares = ",";
                                let decimales = ".";
                                str = str.split(millares).join("");
                                if (isNaN(str)) {
                                    this.value = oldstr;
                                } else {
                                    let numero = parseInt(str);
                                    this.value = numero.format(0, 3, millares, decimales);
                                }
                            }
                        }

                     
                    });
                });
            }
            setInputFilter(document.getElementById("inputFormatoNumerico"), function(value) {
                //declare an object RegExp
                let regex = new RegExp(/^-?\d*$/);
                //test the regexp
                return regex.test(value);
            });

</script>


</section>

<?php endif; ?>