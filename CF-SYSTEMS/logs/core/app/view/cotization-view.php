<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):
$TicketMm = StockData::getPrincipal()->ticket_mm;?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-th-list'></i> Listado de Cotizaciones</h1>
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
    
                
          <div class="col-12 col-sm-4 col-md-4 my-2">
          <a  href="./?view=cotization&opt=new" class="floating-btn message-btn" style="background-color: #4DBE04;">
     <div class="icon-container">
    <i class="fa fa-suitcase-rolling"></i>
    </div>
    <span class="message-text"> CREAR NUEVA</span>
  </a>
            <!-- /.info-box -->
          </div>
          
         
</div>
<?php endif;?>
    
<?php $users = CotizationData::getAllbySQL("where stock_id=".StockData::getPrincipal()->id);
    if(count($users)>0):?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      <th>Accion</th>
      <th>Nombre</th>
      <th>Total</th>
      <th>Accion</th>
    </thead>

    <tfoot>
      <tr>
      <th>Accion</th>
      <th>Nombre</th>
      <th>Total</th>
      <th>Accion</th>
      </tr>
    </tfoot>

      <?php foreach($users as $user): ?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
 
                <!-- Botón visible en todos los dispositivos -->
<a href="<?php echo $TicketMm; ?>/ticket-cotiz.php?id=<?php echo $user->id; ?>"
   class="btn btn-info btn-sm"
   onclick="return manejarVisualizacionPDF(this.href, event)"> <i class="fa fa-eye"></i>
</a>

<!-- Modal para PC -->
<div id="modalPDF" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:#000a; z-index:9999;">
  <div style="position:relative; width:90%; height:90%; margin:5% auto; background:#1e1e1e; border-radius:10px; box-shadow: 0 0 20px rgba(0,0,0,0.7); overflow:hidden; padding-top:80px;">

    <!-- Botones flotantes -->
    <div style="position:absolute; top:20px; right:20px; display:flex; flex-direction:column; gap:15px; z-index:1000;">
      <button onclick="imprimirPDF()" style="background:#28a745; color:#fff; border:none; padding:12px 20px; border-radius:40px; font-weight:bold; font-size:16px; display:flex; align-items:center; gap:8px;">
        <i class="fa fa-print"></i> IMPRIMIR
      </button>
      <a id="btnDescargar" href="#" download style="background:#007bff; color:#fff; border:none; padding:12px 20px; border-radius:40px; font-weight:bold; font-size:16px; display:flex; align-items:center; gap:8px; text-decoration:none;">
        <i class="fa fa-download"></i> DESCARGAR
      </a>
      <button onclick="cerrarPDF()" style="background:#c40030; color:#fff; border:none; padding:12px 20px; border-radius:40px; font-weight:bold; font-size:16px; display:flex; align-items:center; gap:8px;">
        <i class="fa fa-times"></i> CERRAR
      </button>
    </div>

    <!-- Iframe del PDF -->
    <iframe id="iframePDF" src="" style="position:absolute; top:0; left:0; width:100%; height:100%; border:none; z-index:1;"></iframe>
  </div>
</div>

<script>

function manejarVisualizacionPDF(url, event) {
  const esPC = window.innerWidth >= 1024;

  if (esPC) {
    event.preventDefault(); // evita que abra en otra pestaña
    document.getElementById('iframePDF').src = url;
    document.getElementById('btnDescargar').href = url;
    document.getElementById('modalPDF').style.display = 'block';
    return false;
  }

  // en tablet o móvil: permite comportamiento normal (abrir enlace)
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
                          <a href="./?view=cotization&opt=process&id=<?php echo $user->id; ?>" class="btn btn-success"><i class="fas fa-check"></i></a>
                      </div>
        </td>

        <td><?php echo $user->getPerson()->name; ?></td>
        <td><?php echo $user->total; ?></td>
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=cotization&opt=del&id=<?php echo $user->id;?>" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> Eliminar</i></a>
                    
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
     
         <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Cotizacion</h2>
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

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="process"):
$TicketMm = StockData::getPrincipal()->ticket_mm;?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-check'></i> Procesar Cotizacion</h1>
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
        
       
<?php $cotiz = CotizationData::getbyId($_GET['id']);  $users = OperationData::getAllBySQL("where cotization_id=".$_GET['id']);
    if(count($users)>0):?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      <th>Accion</th>
      <th>Vehiculo</th>
      <th>Dia</th>
      <th>Precio</th>
      <th>Accion</th>
    </thead>

    <tfoot>
      <tr>
      <th>Accion</th>
      <th>Vehiculo</th>
      <th>Dia</th>
      <th>Precio</th>
      <th>Accion</th>
      </tr>
    </tfoot>

      <?php foreach($users as $user): $cars = CarsData::getbyId($user->car_id);?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                          <a data-toggle="modal" data-target="#myModal<?php echo $user->id;?>" class="btn btn-sm btn-block btn-primary"><i class="fas fa-history"> Reservar</i></a>
                      </div>
        </td>

        <td><?php echo $cars->getBrand()->name." ".$cars->name." [".$cars->token."]"; ?></td>
        <td><?php echo $user->day; ?></td>
        <td><?php echo $user->price; ?></td>
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=cotization&opt=delprocess&id=<?php echo $user->id;?>&cotization_id=<?php echo $user->cotization_id;?>" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> Eliminar</i></a>
                    
    <?php endif;?>
<?php endforeach; ?>
</td>
    </tr>
<!-- Modal -->
<div class="modal fade" id="myModal<?php echo $user->id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog modal-xs" role="document">
<div class="modal-content">
<div class="modal-body">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title" id="myModalLabel"><i class="fa fa-calendar"></i> Fecha Inicio</h4>
</div>
<div class="modal-body">
<form method="post" class="form-horizontal" action="./?action=cotization&opt=addbook" enctype="multipart/form-data">
<div class="row">

    <div class="col-md-12 col-12">
      <input type="datetime-local"  name="start_at" autocomplete="off"  class="form-control">
       <input type="hidden"  name="cotiz_id" value="<?php echo $user->id;?>"  class="form-control">
       <input type="hidden"  name="person_id" value="<?php echo $cotiz->person_id;?>"  class="form-control">
       <input type="hidden"  name="car_id" value="<?php echo $user->car_id;?>"  class="form-control">
        <input type="hidden"  name="day" value="<?php echo $user->day;?>"  class="form-control">
       <input type="hidden"  name="price" value="<?php echo $user->price;?>"  class="form-control">
    </div>


                <div class="col-md-12 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"></label>
                   <button class="btn btn-success btn-block btn-sm"><i class="fa fa-check"></i> Comenzar</button>
                 
                </div>
              </div>
</form>

</div>

</div>
</div>
</div>  
    <?php endforeach; ?>
      </table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->
      <?php else:?>
     
         <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Vehiculos</h2>
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
          <h1 class="m-0"><i class='fa fa-th-list'></i> Nueva Cotizacion</h1>
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
    
                
          <div class="col-12 col-sm-4 col-md-4 my-2">
            <a href="./?view=cotization&opt=all" class="floating-btn message-btn"  style="background-color: #4DBE04;">
  <div class="icon-container">
   <i class="fa fa-th-list"></i>
  </div>
  <span class="message-text">LISTA DE CREADAS</span>
</a>
          </div>
          <!-- /.col -->

          
         
</div>
<?php endif;?>
<div class="row">

  <div class="col-12 col-sm-12 col-md-7 col-lg-7 col-xl-7">

<div style="background-color:#222;">      
<div class="card-body">
      

    <div class="row">
      <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
    
       <form id="ssearch">
     <div class="input-group">
           <span class="input-group-text" style="background-color:orange;"><i class="fa fa-search"></i></span>
<input type="hidden" name="view" value="cotization">
        <input style="background-color:#333;"  type="search"  aria-label="Search" autocomplete="off" id="product_name" name="product_name" class="form-control" placeholder="Buscar por Modelo o Año">
      </form>
    </div>
  </div>
    
    </div>
<div class="table-responsive">

<script type="text/javascript">
$(document).ready(function(){
  $("#ssearchp").on("submit",function(e){
    e.preventDefault();
    name = $("#product_name").val();
    if(name!=""){
    $.get("./?action=get&opt=allnocat",$("#ssearchp").serialize()+"&go=name",function(data){
      $("#allproducts").html(data);
    });
    $("#product_name").val("");
    }
   else {
        $("#getallnocat").click(function(){
    $.get("./?action=get&opt=allnocat","",function(data2){
        $("#allproducts").html(data2);
      });

  }); 
    }

  });
  });
//jQuery.noConflict();

$(document).ready(function(){

  $("#product_name").keyup(function(){
//    $("#searchp").submit();
searchx();
  });

//  $("#searchp").on("submit",function(e){
  //  e.preventDefault();
function searchx(){
    name = $("#product_name").val();
    console.log(name);
    if(name!=""){
    $.get("./?action=get&opt=allnocat&id=<?php echo $_GET["id"];?>","product_name="+name+"&go=name",function(data){
      $("#allproducts").html(data);
    });
    
    }else{
        $("#getallnocat").click(function(){
    $.get("./?action=get&opt=allnocat","",function(data2){
        $("#allproducts").html(data2);
      });

  }); 
    }
}
//  });
  });

    $("#getallnocat").click(function(){
    $.get("./?action=get&opt=allnocat","",function(data2){
        $("#allproducts").html(data2);
      });

  }); 
  
</script></td>



</div>
</div>
</div>


<div id="allproducts"></div>
</div>

<div class="col-12 col-sm-12 col-md-5 col-lg-5 col-xl-5">
<div id="cartoxsell"></div>
  
</div>

</div>

<script type="text/javascript">
        $.get("./?action=get&opt=allnocat","",function(data2){
        $("#allproducts").html(data2);
        console.log(data2);
      });

<?php if(isset($_SESSION["cotization"])):?>
  $.get("./?action=get&opt=cart", "", function(data2){
    $("#cartoxsell").html(data2);

    // Re-inicializar select2 en los selects cargados dinámicamente
    $("#cartoxsell select").select2({
      width: '100%'
    });
  });
<?php endif; ?>

          
     </script>


</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="booking"): $cars = CarsData::getById($_GET["car_id"]); $cli = PersonData::getById($_GET["person_id"]); $stock = StockData::getById($cars->stock_id); ?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-history'></i> Agregar Reserva</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-history'></i> Reservas de coche</li>
               
            </ol>
          </div><!-- /.col -->
        </div>
          <div class="card" style="background-color:#222;">
<div  class="card-header">
<i class="fa fa-user-plus"></i>  Datos del Cliente: 
</div>
<div class="card-body">

   <form class="form-horizontal" method="post" id="addbooking" role="form"> 
   
   <input type="hidden" name="cotiz_id" value="<?php echo $_GET["cotiz_id"];?>"  class="form-control">
      <div class="row">
         <div class="col-md-3 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Conductor #1 </label>
    <select name="person_id" class="form-control select2" required>
      <option value="<?php echo $cli->id;?>"><?php echo $cli->name;?></option>
      </select>
        </div>
    </div>

        <div class="col-md-3 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Conductor #2</label>
      <?php $clients = PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id);?>
    <select name="person2_id" class="form-control select2">
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
        </div>
    </div>
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha a Entregar</label>
      <input type="datetime-local" required name="start_at" id="start_at" value="<?php echo $_GET["start_at"];?>" class="form-control " >
    </div>


     <div class="col-md-3 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha a Recibir</label>
      <input type="datetime-local" required name="end_at" id="end_at" value="<?php echo date('Y-m-d h:i', strtotime($_GET["start_at"].'+'.$_GET["day"].' day'));?>" class="form-control " >
        </div>
     

  <div class="col-md-3 col-12"> 
  <label for="inputEmail1" class="col-md-12 col-12 control-label">Tipo de Seguro</label>
    
      <select class="form-control" name="type_sure">
      <option value="">--- ELEGIR ---</option>
      <?php foreach (SureData::getALL() as $sure): ?>
      <option value="<?php echo $sure->id;?>"><?php echo $sure->name;?></option>
      <?php endforeach ?>
      </select>
</div>
<div class="col-md-3 col-12 "> 
  <label for="inputEmail1" class="col-md-12 col-12 control-label">Deducible</label>
    
          <input type="text" name="sure" class="form-control" value="0"   placeholder="Deducible" autocomplete="off" required>
</div>
 <div hidden class="col-md-3 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Deposito</label>
     <span class="input-group-text autocomplete"><i class="fa fa-asterisk"></i></span>
      <input type="text" name="deposit" value="0" class="form-control" placeholder="Deposito" >
    </div>
  </div>

<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Forma de Pago</label>
    <select name="f_id" required class="form-control select2">
    <?php foreach(FData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
      <div class="col-md-3 col-12">
    <div class="input-group" >
  <label for="inputEmail1" class="col-md-12 col-12 control-label">Combustible</label>
    <span class="input-group-text autocomplete"><i class="fa fa-tint"></i></span>
     <select name="fuel"  class="form-control">
      <option value="R">En Reserva</option>
      <option value="1/4">1/4</option>
      <option value="1/2">Medio</option>
      <option value="3/4">3/4</option>
      <option value="F">Full</option>
     </select>
    </div>
  </div>

    <div class="col-md-6 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Lugar a Entregar</label>
     <span class="input-group-text autocomplete"><i class="fa fa-street-view"></i></span>
       <input id="placein" type="text" class="form-control" autocomplete="off"  name="place_start" placeholder="Lugar a Entregar">
    </div>
  </div>

    <div class="col-md-6 col-12">
       <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Lugar a Recibir</label>
     <span class="input-group-text autocomplete"><i class="fa fa-street-view"></i></span>
      <input type="text" id="placeout" name="place_end" class="form-control" placeholder="Lugar a Recibir" >
    </div>
  </div>


<div  class="card-header col-md-12 col-12 my-2">
<i class="fa fa-clone"></i>  Datos Extras:
</div>

<div class="col-md-2 col-6">
       <div class="input-group">
     <span class="input-group-text autocomplete"><i class="fa fa-baby"></i> CARSEAT</span>
     <input id="carseat1"  type="number" value="0" class="form-control" autocomplete="off"  name="unit_carseat"  placeholder="Unidad"  min="0">
    </div>
  </div>
  

<div class="col-md-2 col-6">
       <div class="input-group">
       <input type="number" class="form-control" required value="0"  autocomplete="off" id="carseat2"  name="price_carseat" placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>
  
  
  <div class="col-md-2 col-6">
       <div class="input-group">
     <span class="input-group-text autocomplete"><i class="fa fa-wifi"></i> INTERNET</span>
     
       <input  type="number" class="form-control" value="0" autocomplete="off" id="wifi1"  name="unit_wifi" placeholder="Unidad"  min="0" >
    </div>
  </div>
  

<div class="col-md-2 col-6">
       <div class="input-group">
       <input  type="number" class="form-control" required value="0" autocomplete="off"  id="wifi2" name="price_wifi" placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>
  
  
  <div class="col-md-2 col-6">
       <div class="input-group">
     <span class="input-group-text autocomplete"><i class="fa fa-sitemap"></i> TRAILER</span>
      <input type="number" value="0" class="form-control" autocomplete="off" id="trailer1"  name="unit_trailer" placeholder="Unidad"  min="0" >
    </div>
  </div>
  
  
<div class="col-md-2 col-6">
       <div class="input-group">
       <input  type="number" class="form-control" value="0"  autocomplete="off" id="trailer2" required  name="price_trailer" placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>
  
<div  class="card-header col-md-12 col-12 my-2">
<i class="fa fa-car"></i>  Datos del Vehiculo:
</div>

    <div hidden class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Ubicacion</label>
      <?php $clients = LocationData::getAll();?>
    <select name="location" required class="form-control select2" id="location" >
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
  
    <div class="col-md-3 col-12">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Rent Car</label>
    <span class="input-group-text autocomplete"><i class="fa fa-street-view"></i></span>
    <select name="stock_id" required   class="form-control"  onchange="showInp()"> <option value="<?php echo $stock->id;?>"><?php echo $stock->name;?></option></select>
    </div>
  </div>

 <div class="col-md-2 col-12" id="rpayment" style="display: none"/>
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Precio de Rent Car</label>
      <input type="number" value="0" required name="rpayment"  class="form-control" placeholder="Precio de Rent Car"  min="0" step="0.01"> 
    </div>
    
<div class="col-md-3 col-12">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vehiculo/Original</label>
    <select name="car_id" required  class="form-control select2">  <option value="<?php echo $cars->id;?>"><?php echo $cars->getBrand()->name." ".$cars->name." [".$cars->token."]";?></option></select>
    </div>
  </div>

  <div hidden class="col-md-3 col-12">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vehiculo/Reemplazo</label>
    <?php $clients = CarsData::getAllbySQL("where stock_id=".StockData::getPrincipal()->id);?>
    <select name="car2_id" class="form-control select2">
    <option value="0">--ELEGIR--</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->getBrand()->name." ".$client->name." ".$client->year." ".$client->plate." [".$client->token."]";?></option>
    <?php endforeach;?>
      </select></div>
  </div>

<?php $divisa = StockData::getPrincipal()->divisa;?> 
 <div class="col-md-2 col-12">
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Divisa</label>
    <select name="divisa_id" id="divisa_id" class="form-control">
    <option value="1">DOLAR</option>
    <option value="<?php echo $divisa ;?>">PESOS</option>
      </select></div>
  </div>
  

    <div class="col-md-2 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Precio X Dia</label>
    <input type="number" required name="price2" id="tariff2"  class="form-control" placeholder="Escribir Precio" value="<?php echo $_GET["price"];?>"  min="0" step="0.01">
    </div>
    </div>


    <div class="col-md-2 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Dias de Renta</label>
    <select name="day" id="dias"  class="form-control"></select>
    </div>
</div>
    <div class="col-md-2 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Reserva</label>
    <span class="input-group-text autocomplete"><i class="fa fa-asterisk"></i></span>
     <select name="total" id="amount"  class="form-control"></select>
    </div>
  </div>
  
   <div class="col-md-2 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Total Extra</label>
    <span class="input-group-text autocomplete"><i class="fa fa-asterisk"></i></span>
     <select name="xtotal" id="xmount"  class="form-control"></select>
    </div>
  </div>


    <div class="col-md-2 col-12">   
    <div class="input-group">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Abono/Total</label>
    <span class="input-group-text autocomplete"><i class="fa fa-asterisk"></i></span>
      <input type="number" value="0" required name="payment" id="payment" class="form-control" placeholder="Abono/Total"  min="0" step="0.01">
    </div>
  </div>

    <div class="col-md-2 col-12">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Monto Restante</label>
    <span class="input-group-text autocomplete"><i class="fa fa-minus-square"></i></span>
     <input readonly id="remaining" name="remaining"  class="form-control">
    </div>
  </div>
  
   <div class="col-md-1 col-6">
    <div class="input-group" >
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><i class="fa fa-plane"></i></label>
    <input text="number" required value="0"  name="plane"  class="form-control"  min="0" step="0.01">
    </div>
  </div>
                      
    <div class="col-md-1 col-6">
    <div class="input-group" >
                      
                      <label for="inputEmail1" class="col-md-12 col-12 control-label">ITBIS</label>
    <div class="icheck-primary d-inline">
                        <input type="checkbox" name="iva" id="checkbox2">
                        <label for="checkbox2">
                          18%
                        </label>
                      </div>
    </div>
  </div>
<script type="text/javascript">

  $(document).ready(function(){
    $('#xmount').val();
    recargarxLista();

  })
  
  function recargarxLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
       data: {carseat1: $('#carseat1').val(), carseat2:  $('#carseat2').val(), wifi1: $('#wifi1').val(), wifi2:  $('#wifi2').val(), trailer1: $('#trailer1').val(), trailer2:  $('#trailer2').val()},
      success:function(r){
        $('#xmount').html(r);
      }
    });
  }



 carseat2.addEventListener("keyup", function()
    {
        
     $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
       data: {carseat1: $('#carseat1').val(), carseat2:  $('#carseat2').val(), wifi1: $('#wifi1').val(), wifi2:  $('#wifi2').val(), trailer1: $('#trailer1').val(), trailer2:  $('#trailer2').val()},
      success:function(r){
        $('#xmount').html(r);
        
        function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 

$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
      }
    });
        
    });
    
    
    wifi2.addEventListener("keyup", function()
    {
        
     $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
       data: {carseat1: $('#carseat1').val(), carseat2:  $('#carseat2').val(),wifi1: $('#wifi1').val(), wifi2:  $('#wifi2').val(),trailer1: $('#trailer1').val(), trailer2:  $('#trailer2').val()},
      success:function(r){
        $('#xmount').html(r);
        
        function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 

$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
      }
    });
        
    });
    
    
     trailer2.addEventListener("keyup", function()
    {
        
     $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
       data: {carseat1: $('#carseat1').val(), carseat2:  $('#carseat2').val(),wifi1: $('#wifi1').val(), wifi2:  $('#wifi2').val(),trailer1: $('#trailer1').val(), trailer2:  $('#trailer2').val()},
      success:function(r){
        $('#xmount').html(r);
        
        function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 

$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
      }
    });
        
    });
    
    

function showInp(){
  var getSelectValue = document.getElementById("select2lista").value;

  if(getSelectValue==<?php echo StockData::getPrincipal()->id;?>){
    document.getElementById("rpayment").style.display = "none";
  }else{
   document.getElementById("rpayment").style.display = "inline-block";  
  }
 
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
      url:"./?action=get&opt=reserve",
      data:"location=" + $('#location').val(),
      success:function(r){
        $('#select2lista').html(r);
      }
    });
  }


  $(document).ready(function(){
    $('#select2lista').val();
    recargar2Lista();

    $('#select2lista').change(function(){
      recargar2Lista();
    });
  })

  function recargar2Lista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=cars2",
      data:"stock_id=" + $('#select2lista').val(),
      success:function(r){
        $('#cars').html(r);
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

 

$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
 });

function Lista(){
function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
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
       data: {paycar: $('#payment').val(),tcars: $('#tariff').val(),dvs: $('#divisa_id').val(),cars: $('#cars').val(), price: vprice},
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
$("#remaining").val(($('#tariff').val()*vprice)*$('#divisa_id').val());
}


   


    tariff2.addEventListener("keyup", function()
    {   

    function calcularDiasAusencia(fechaIni, fechaFin) {
  var diaEnMils = 1000 * 60 * 60 * 24,
      desde = new Date(fechaIni.substr(0, 10)),
      hasta = new Date(fechaFin.substr(0, 10)),
      diff = hasta.getTime() - desde.getTime();// +1 incluir el dia de ini
  return diff / diaEnMils;
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

 
$("#remaining").val( agregarSeparadorMiles((($('#tariff2').val()*vprice)*$('#divisa_id').val())+parseFloat($("#xmount").val())));
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

<?php if ($method=="AMERICANO"):?>
$('#category_id').on('change', () => {
    var value = $('#category_id').val();
    
    if(value) {
       $('.warning').hide();
       $('#submit').prop('disabled', false);
    }
    

    
});

<?php endif;?>
</script>
 <div hidden id="day"></div>

                <div class="col-md-6 col-6 my-2">
                  <a href="./?view=cotization&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6 my-2">

                   <button  class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
</form>
</div>
</div>

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
<script type="text/javascript">

            jQuery(document).ready(function(){
            jQuery("#addbooking").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=booking&opt=addcotiz",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Reserva Exito!", { sticky: true });
                  $.jGrowl("Se Agrego la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=booking&opt=all'  }, delay); 
                     
                  }else{
                  $.jGrowl("Hay otra reserva con esa fecha", { header: 'Error al Agregar' });
                  }
                  }
                });
                return false;
              });
            });

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
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
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

</section>
<?php endif; ?>