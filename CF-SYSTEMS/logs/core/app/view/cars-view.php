<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>

<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-car'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Listado de Vehiculos"; break;
 case 'EN': echo "List of Vehicles"; break;
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
    
<!-- Barra de búsqueda -->
      <div class="row mb-2 mt-3">
        <div class="col-md-12">
          <input type="text" id="buscador" class="form-control" placeholder="🔍 Busque por nombre, placa, marca o modelo">
        </div>
      </div>
  
 <style>
  /* Contenedor de botones */
.btn-circles {
  position: absolute;
  top: 50%;
  display: flex;
  flex-direction: column;
  gap: 10px;
  transform: translateY(-50%);
}

.btn-circles.right { right: 10px; }
.btn-circles.left  { left: 10px; }

.circle-btn {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: none;
  background-color: #444;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: 0.3s;
}

.circle-btn:hover {
  background-color: #666;
}




 </style>    
            <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <div class="col-md-8 my-3">
            <!-- MAP & BOX PANE -->
   
    
 <!-- Grid de productos -->
<div class="row" id="grid-vehiculos">
  <?php foreach(CarsData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id) as $sells): ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-4">
      <div class="card h-100 shadow-sm text-center" style="background-color:#222;">
        <!-- Imagen -->
        <?php if(!empty($sells->invoice_file)):?>
          <img src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>" class="card-img-top">
        <?php else: $ticket_image = StockData::getPrincipal()->ticket_image; ?>
          <img src="CF-SYSTEMS/storage/configuration/<?php echo $ticket_image; ?>" class="card-img-top">
        <?php endif; ?>
        
        
           <!-- 🔵 Botones circulares siempre visibles -->
   <div class="btn-circles right">
      <a  style="background-color: #27BEF5;" href="./?view=cars&opt=description&id=<?php echo $sells->id;?>" class="circle-btn" title="Info"><i class="fa fa-info"></i></a>
      <a  href="./?view=gallery&opt=all&id=<?php echo $sells->id;?>"  class="circle-btn" title="Galeria"><i class="fa fa-image"></i></a>
       <a  style="background-color: #4DBE04;" href="./?view=cars&opt=edit&id=<?php echo $sells->id;?>"  class="circle-btn" title="Editar"><i class="fa fa-edit"></i></a>
  
<?php if ($sells->status==0):

$base = new Database();
$con = $base->connect();
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()):
if ($x['permits_id']==4): ?>
   
      <a style="background-color: #C70039;" href="./?action=cars&opt=del&id=<?php echo $sells->id;?>"  class="circle-btn" title="Eliminar"><i class="fa fa-trash"></i></a>
      
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
    <?php endif; endwhile; endif;?>
    </div>
    
      <!-- 🔵 Botones circulares IZQUIERDA -->
    <div class="btn-circles left">
      <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=5" style="background-color: orange;" class="circle-btn" title="Favorito"><i class="fas fa-usd"></i></a>
      <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=4" class="circle-btn" title="Compartir"><i class="fa fa-cogs"></i></a>
    </div>


        <!-- Información -->
        <div class="card-body d-flex flex-column">
          <h6 class="fw-bold mb-1" style="font-size:13px;">
            <?php echo strtoupper($sells->getBrand()->name." ".$sells->name." ".$sells->year); ?>
          </h6>
          <p class="text-muted mb-1" style="font-size:14px;">
            <?php echo $sells->plate ?: "Sin Placa"; ?>
          </p>
          <p class="fw-bold mb-1" style="font-size:16px; color:white;">
            <?php echo Core::$symbol." ".number_format($sells->price,2); ?>
          </p>
          <p class="text-muted mb-3" style="font-size:14px;">
            <?php echo strtoupper($sells->getExColor()->name); ?>
          </p>

          <!-- Estado -->
          <?php if ($sells->status==0):?>
            <a href="javascript:void(0)" class="floating-btn message-btn mt-auto" style="background-color:gray; pointer-events:none;">
              <div class="icon-container"><i class="fa fa-car"></i></div>
              <span class="message-text"> DISPONIBLE</span>
            </a>
          <?php elseif ($sells->status==1):?>
            <a href="javascript:void(0)" class="floating-btn message-btn mt-auto disabled-link" style="background-color:orange; pointer-events:none;">
              <div class="icon-container"><i class="fa fa-car"></i></div>
              <span class="message-text"> RESERVADO</span>
            </a>
          <?php elseif ($sells->status==2):?>
            <a href="javascript:void(0)" class="floating-btn message-btn mt-auto disabled-link" style="background-color:#C70039; pointer-events:none;">
              <div class="icon-container"><i class="fa fa-car"></i></div>
              <span class="message-text"> RENTADO</span>
            </a>
          <?php endif; ?>
          
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Mensaje cuando no hay coincidencias -->
<div id="mensaje-vacio" class="text-center mt-4" style="display:none; color:gray; font-size:18px;">
  🚘 No se encontraron vehículos
</div>

<script>
const buscador = document.getElementById("buscador");
const mensajeVacio = document.getElementById("mensaje-vacio");

buscador.addEventListener("keyup", () => {
  let texto = buscador.value.toLowerCase().trim();
  let encontrados = 0;

  // recorrer cada columna del grid
  document.querySelectorAll("#grid-vehiculos > div").forEach(col => {
    const contenido = col.textContent.toLowerCase();

    if (texto === "" || contenido.includes(texto)) {
      col.style.display = "";   // mostrar tarjeta completa
      encontrados++;
    } else {
      col.style.display = "none"; // ocultar tarjeta
    }
  });

  // mostrar/ocultar mensaje vacío
  mensajeVacio.style.display = (encontrados === 0) ? "block" : "none";
});
</script>
    </div>
    
 
          <div class="col-md-4 my-3">
              
            <!-- Info Boxes Style 2 -->
            <div class="info-box mb-3" style="background-color: #4DBE04;">
              <span class="info-box-icon"><i class="fa fa-edit"></i></span>
                <a  href="./?view=cars&opt=new" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">CREAR NUEVO</span>
                <span class="info-box-number">VEHICULO</span>
              </div>
                </a>
              <!-- /.info-box-content -->
            </div>
            
             <div class="info-box mb-3" style="background-color: gray;">
              <span class="info-box-icon"><i class="fa fa-car"></i></span>
             <a  href="./?view=cars&opt=available" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">VEHICULO</span>
                <span class="info-box-number">DISPONIBLE</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            
            <!-- /.info-box -->
            <div class="info-box mb-3" style="background-color: orange;">
              <span class="info-box-icon"><i class="fa fa-suitcase-rolling"></i></span>
             <a  href="./?view=cars&opt=reserved" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">VEHICULO</span>
                <span class="info-box-number">RESERVADOS</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
            <div class="info-box mb-3" style="background-color: #C70039;">
              <span class="info-box-icon"><i class="fa fa-road"> </i></span>
             <a  href="./?view=cars&opt=rented" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">VEHICULO</span>
                <span class="info-box-number">RENTADO</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
            <div class="info-box mb-3" style="background-color: #27BEF5;">
              <span class="info-box-icon"><i class="fa fa-globe"></i></span>
               <a  href="./?view=cars&opt=all" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">MOSTRAR TODOS</span>
                <span class="info-box-number">VEHICULO</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
   </div>
 <?php else:?>

 <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
    
       <form id="ssearch">
     <div class="input-group">
           <span class="input-group-text"><i class="fa fa-search"></i></span>
<input style="background-color:#222;" type="hidden" name="view" value="sell">
        <input style="background-color:#222;" type="search"  aria-label="Search" autocomplete="off" id="product_name" name="product_name" class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Buscar Modelo o Año"; break;  case 'EN': echo "Search Model or Year"; break; } ?>">
    </div>
     </form>
  </div>
 
    

      <script type="text/javascript">
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
    $.get("./?action=get&opt=carsall&stock=<?php echo StockData::getPrincipal()->id;?>&id=<?php echo $_GET["id"];?>","product_name="+name+"&go=name",function(data){
      $("#allproducts").html(data);
    });
    
    }else{
    $.get("./?action=get&opt=carsall&stock=<?php echo StockData::getPrincipal()->id;?>","",function(data2){
        $("#allproducts").html(data2);
      

  }); 
    }
}
      $.get("./?action=get&opt=carsall&stock=<?php echo StockData::getPrincipal()->id;?>","",function(data2){
        $("#allproducts").html(data2);
        console.log(data2);
      });

   $("#mesero").click(function(){
      $.get("./?action=get&opt=products","",function(data){
        $(".steps").html(data);       
      });
    });

  </script>
    </div> 
  
<div id="allproducts"></div>
            <!-- /.card -->
        
<?php endif;?>
  
  
</div>

  </div>
    </div><!-- /.row -->
      </div><!-- /.container-fluid -->
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="available"):?>

<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-car'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Listado de Vehiculos"; break;
 case 'EN': echo "List of Vehicles"; break;
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
    
<!-- Barra de búsqueda -->
      <div class="row mb-2 mt-3">
        <div class="col-md-12">
          <input type="text" id="buscador" class="form-control" placeholder="🔍 Busque por nombre, placa, marca o modelo">
        </div>
      </div>
  
 <style>
  /* Contenedor de botones */
.btn-circles {
  position: absolute;
  top: 50%;
  display: flex;
  flex-direction: column;
  gap: 10px;
  transform: translateY(-50%);
}

.btn-circles.right { right: 10px; }
.btn-circles.left  { left: 10px; }

.circle-btn {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: none;
  background-color: #444;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: 0.3s;
}

.circle-btn:hover {
  background-color: #666;
}




 </style>    
            <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <div class="col-md-8 my-3">
            <!-- MAP & BOX PANE -->
   
    
 <!-- Grid de productos -->
<div class="row" id="grid-vehiculos">
  <?php foreach(CarsData::getAllBySQL("where status=0 and stock_id=".StockData::getPrincipal()->id) as $sells): ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-4">
      <div class="card h-100 shadow-sm text-center" style="background-color:#222;">
        <!-- Imagen -->
        <?php if(!empty($sells->invoice_file)):?>
          <img src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>" class="card-img-top">
        <?php else: $ticket_image = StockData::getPrincipal()->ticket_image; ?>
          <img src="CF-SYSTEMS/storage/configuration/<?php echo $ticket_image; ?>" class="card-img-top">
        <?php endif; ?>
        
        
           <!-- 🔵 Botones circulares siempre visibles -->
   <div class="btn-circles right">
      <a  style="background-color: #27BEF5;" href="./?view=cars&opt=description&id=<?php echo $sells->id;?>" class="circle-btn" title="Info"><i class="fa fa-info"></i></a>
      <a  href="./?view=gallery&opt=all&id=<?php echo $sells->id;?>"  class="circle-btn" title="Galeria"><i class="fa fa-image"></i></a>
       <a  style="background-color: #4DBE04;" href="./?view=cars&opt=edit&id=<?php echo $sells->id;?>"  class="circle-btn" title="Editar"><i class="fa fa-edit"></i></a>
  
<?php if ($sells->status==0):

$base = new Database();
$con = $base->connect();
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()):
if ($x['permits_id']==4): ?>
   
      <a style="background-color: #C70039;" href="./?action=cars&opt=del&id=<?php echo $sells->id;?>"  class="circle-btn" title="Eliminar"><i class="fa fa-trash"></i></a>
      
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
    <?php endif; endwhile; endif;?>
    </div>
    
      <!-- 🔵 Botones circulares IZQUIERDA -->
    <div class="btn-circles left">
      <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=5" style="background-color: orange;" class="circle-btn" title="Favorito"><i class="fas fa-usd"></i></a>
      <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=4" class="circle-btn" title="Compartir"><i class="fa fa-cogs"></i></a>
    </div>


        <!-- Información -->
        <div class="card-body d-flex flex-column">
          <h6 class="fw-bold mb-1" style="font-size:13px;">
            <?php echo strtoupper($sells->getBrand()->name." ".$sells->name." ".$sells->year); ?>
          </h6>
          <p class="text-muted mb-1" style="font-size:14px;">
            <?php echo $sells->plate ?: "Sin Placa"; ?>
          </p>
          <p class="fw-bold mb-1" style="font-size:16px; color:white;">
            <?php echo Core::$symbol." ".number_format($sells->price,2); ?>
          </p>
          <p class="text-muted mb-3" style="font-size:14px;">
            <?php echo strtoupper($sells->getExColor()->name); ?>
          </p>

          <!-- Estado -->
          <?php if ($sells->status==0):?>
            <a href="javascript:void(0)" class="floating-btn message-btn mt-auto" style="background-color:gray; pointer-events:none;">
              <div class="icon-container"><i class="fa fa-car"></i></div>
              <span class="message-text"> DISPONIBLE</span>
            </a>
          <?php elseif ($sells->status==1):?>
            <a href="javascript:void(0)" class="floating-btn message-btn mt-auto disabled-link" style="background-color:orange; pointer-events:none;">
              <div class="icon-container"><i class="fa fa-car"></i></div>
              <span class="message-text"> RESERVADO</span>
            </a>
          <?php elseif ($sells->status==2):?>
            <a href="javascript:void(0)" class="floating-btn message-btn mt-auto disabled-link" style="background-color:#C70039; pointer-events:none;">
              <div class="icon-container"><i class="fa fa-car"></i></div>
              <span class="message-text"> RENTADO</span>
            </a>
          <?php endif; ?>
          
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Mensaje cuando no hay coincidencias -->
<div id="mensaje-vacio" class="text-center mt-4" style="display:none; color:gray; font-size:18px;">
  🚘 No se encontraron vehículos
</div>

<script>
const buscador = document.getElementById("buscador");
const mensajeVacio = document.getElementById("mensaje-vacio");

buscador.addEventListener("keyup", () => {
  let texto = buscador.value.toLowerCase().trim();
  let encontrados = 0;

  // recorrer cada columna del grid
  document.querySelectorAll("#grid-vehiculos > div").forEach(col => {
    const contenido = col.textContent.toLowerCase();

    if (texto === "" || contenido.includes(texto)) {
      col.style.display = "";   // mostrar tarjeta completa
      encontrados++;
    } else {
      col.style.display = "none"; // ocultar tarjeta
    }
  });

  // mostrar/ocultar mensaje vacío
  mensajeVacio.style.display = (encontrados === 0) ? "block" : "none";
});
</script>
    </div>
    
 
          <div class="col-md-4 my-3">
              
            <!-- Info Boxes Style 2 -->
            <div class="info-box mb-3" style="background-color: #4DBE04;">
              <span class="info-box-icon"><i class="fa fa-edit"></i></span>
                <a  href="./?view=cars&opt=new" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">CREAR NUEVO</span>
                <span class="info-box-number">VEHICULO</span>
              </div>
                </a>
              <!-- /.info-box-content -->
            </div>
            
             <div class="info-box mb-3" style="background-color: gray;">
              <span class="info-box-icon"><i class="fa fa-car"></i></span>
             <a  href="./?view=cars&opt=available" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">VEHICULO</span>
                <span class="info-box-number">DISPONIBLE</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            
            <!-- /.info-box -->
            <div class="info-box mb-3" style="background-color: orange;">
              <span class="info-box-icon"><i class="fa fa-suitcase-rolling"></i></span>
             <a  href="./?view=cars&opt=reserved" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">VEHICULO</span>
                <span class="info-box-number">RESERVADOS</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
            <div class="info-box mb-3" style="background-color: #C70039;">
              <span class="info-box-icon"><i class="fa fa-road"> </i></span>
             <a  href="./?view=cars&opt=rented" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">VEHICULO</span>
                <span class="info-box-number">RENTADO</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
            <div class="info-box mb-3" style="background-color: #27BEF5;">
              <span class="info-box-icon"><i class="fa fa-globe"></i></span>
               <a  href="./?view=cars&opt=all" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">MOSTRAR TODOS</span>
                <span class="info-box-number">VEHICULO</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
   </div>
 <?php else:?>

 <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
    
       <form id="ssearch">
     <div class="input-group">
           <span class="input-group-text"><i class="fa fa-search"></i></span>
<input style="background-color:#222;" type="hidden" name="view" value="sell">
        <input style="background-color:#222;" type="search"  aria-label="Search" autocomplete="off" id="product_name" name="product_name" class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Buscar Modelo o Año"; break;  case 'EN': echo "Search Model or Year"; break; } ?>">
    </div>
     </form>
  </div>
 
    

      <script type="text/javascript">
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
    $.get("./?action=get&opt=available&stock=<?php echo StockData::getPrincipal()->id;?>&id=<?php echo $_GET["id"];?>","product_name="+name+"&go=name",function(data){
      $("#allproducts").html(data);
    });
    
    }else{
    $.get("./?action=get&opt=available&stock=<?php echo StockData::getPrincipal()->id;?>","",function(data2){
        $("#allproducts").html(data2);
      

  }); 
    }
}
      $.get("./?action=get&opt=available&stock=<?php echo StockData::getPrincipal()->id;?>","",function(data2){
        $("#allproducts").html(data2);
        console.log(data2);
      });

   $("#mesero").click(function(){
      $.get("./?action=get&opt=products","",function(data){
        $(".steps").html(data);       
      });
    });

  </script>
    </div> 
  
<div id="allproducts"></div>
            <!-- /.card -->
        
<?php endif;?>
  
  
</div>

  </div>
    </div><!-- /.row -->
      </div><!-- /.container-fluid -->
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="reserved"):?>

<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-car'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Listado de Vehiculos"; break;
 case 'EN': echo "List of Vehicles"; break;
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
    
<!-- Barra de búsqueda -->
      <div class="row mb-2 mt-3">
        <div class="col-md-12">
          <input type="text" id="buscador" class="form-control" placeholder="🔍 Busque por nombre, placa, marca o modelo">
        </div>
      </div>
  
 <style>
  /* Contenedor de botones */
.btn-circles {
  position: absolute;
  top: 50%;
  display: flex;
  flex-direction: column;
  gap: 10px;
  transform: translateY(-50%);
}

.btn-circles.right { right: 10px; }
.btn-circles.left  { left: 10px; }

.circle-btn {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: none;
  background-color: #444;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: 0.3s;
}

.circle-btn:hover {
  background-color: #666;
}




 </style>    
            <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <div class="col-md-8 my-3">
            <!-- MAP & BOX PANE -->
   
    
 <!-- Grid de productos -->
<div class="row" id="grid-vehiculos">
  <?php foreach(CarsData::getAllBySQL("where status=1 and stock_id=".StockData::getPrincipal()->id) as $sells): ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-4">
      <div class="card h-100 shadow-sm text-center" style="background-color:#222;">
        <!-- Imagen -->
        <?php if(!empty($sells->invoice_file)):?>
          <img src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>" class="card-img-top">
        <?php else: $ticket_image = StockData::getPrincipal()->ticket_image; ?>
          <img src="CF-SYSTEMS/storage/configuration/<?php echo $ticket_image; ?>" class="card-img-top">
        <?php endif; ?>
        
        
           <!-- 🔵 Botones circulares siempre visibles -->
   <div class="btn-circles right">
      <a  style="background-color: #27BEF5;" href="./?view=cars&opt=description&id=<?php echo $sells->id;?>" class="circle-btn" title="Info"><i class="fa fa-info"></i></a>
      <a  href="./?view=gallery&opt=all&id=<?php echo $sells->id;?>"  class="circle-btn" title="Galeria"><i class="fa fa-image"></i></a>
       <a  style="background-color: #4DBE04;" href="./?view=cars&opt=edit&id=<?php echo $sells->id;?>"  class="circle-btn" title="Editar"><i class="fa fa-edit"></i></a>
  
<?php if ($sells->status==0):

$base = new Database();
$con = $base->connect();
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()):
if ($x['permits_id']==4): ?>
   
      <a style="background-color: #C70039;" href="./?action=cars&opt=del&id=<?php echo $sells->id;?>"  class="circle-btn" title="Eliminar"><i class="fa fa-trash"></i></a>
      
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
    <?php endif; endwhile; endif;?>
    </div>
    
      <!-- 🔵 Botones circulares IZQUIERDA -->
    <div class="btn-circles left">
      <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=5" style="background-color: orange;" class="circle-btn" title="Favorito"><i class="fas fa-usd"></i></a>
      <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=4" class="circle-btn" title="Compartir"><i class="fa fa-cogs"></i></a>
    </div>


        <!-- Información -->
        <div class="card-body d-flex flex-column">
          <h6 class="fw-bold mb-1" style="font-size:13px;">
            <?php echo strtoupper($sells->getBrand()->name." ".$sells->name." ".$sells->year); ?>
          </h6>
          <p class="text-muted mb-1" style="font-size:14px;">
            <?php echo $sells->plate ?: "Sin Placa"; ?>
          </p>
          <p class="fw-bold mb-1" style="font-size:16px; color:white;">
            <?php echo Core::$symbol." ".number_format($sells->price,2); ?>
          </p>
          <p class="text-muted mb-3" style="font-size:14px;">
            <?php echo strtoupper($sells->getExColor()->name); ?>
          </p>

          <!-- Estado -->
          <?php if ($sells->status==0):?>
            <a href="javascript:void(0)" class="floating-btn message-btn mt-auto" style="background-color:gray; pointer-events:none;">
              <div class="icon-container"><i class="fa fa-car"></i></div>
              <span class="message-text"> DISPONIBLE</span>
            </a>
          <?php elseif ($sells->status==1):?>
            <a href="javascript:void(0)" class="floating-btn message-btn mt-auto disabled-link" style="background-color:orange; pointer-events:none;">
              <div class="icon-container"><i class="fa fa-car"></i></div>
              <span class="message-text"> RESERVADO</span>
            </a>
          <?php elseif ($sells->status==2):?>
            <a href="javascript:void(0)" class="floating-btn message-btn mt-auto disabled-link" style="background-color:#C70039; pointer-events:none;">
              <div class="icon-container"><i class="fa fa-car"></i></div>
              <span class="message-text"> RENTADO</span>
            </a>
          <?php endif; ?>
          
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Mensaje cuando no hay coincidencias -->
<div id="mensaje-vacio" class="text-center mt-4" style="display:none; color:gray; font-size:18px;">
  🚘 No se encontraron vehículos
</div>

<script>
const buscador = document.getElementById("buscador");
const mensajeVacio = document.getElementById("mensaje-vacio");

buscador.addEventListener("keyup", () => {
  let texto = buscador.value.toLowerCase().trim();
  let encontrados = 0;

  // recorrer cada columna del grid
  document.querySelectorAll("#grid-vehiculos > div").forEach(col => {
    const contenido = col.textContent.toLowerCase();

    if (texto === "" || contenido.includes(texto)) {
      col.style.display = "";   // mostrar tarjeta completa
      encontrados++;
    } else {
      col.style.display = "none"; // ocultar tarjeta
    }
  });

  // mostrar/ocultar mensaje vacío
  mensajeVacio.style.display = (encontrados === 0) ? "block" : "none";
});
</script>
    </div>
    
 
          <div class="col-md-4 my-3">
              
            <!-- Info Boxes Style 2 -->
            <div class="info-box mb-3" style="background-color: #4DBE04;">
              <span class="info-box-icon"><i class="fa fa-edit"></i></span>
                <a  href="./?view=cars&opt=new" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">CREAR NUEVO</span>
                <span class="info-box-number">VEHICULO</span>
              </div>
                </a>
              <!-- /.info-box-content -->
            </div>
            
             <div class="info-box mb-3" style="background-color: gray;">
              <span class="info-box-icon"><i class="fa fa-car"></i></span>
             <a  href="./?view=cars&opt=available" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">VEHICULO</span>
                <span class="info-box-number">DISPONIBLE</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            
            <!-- /.info-box -->
            <div class="info-box mb-3" style="background-color: orange;">
              <span class="info-box-icon"><i class="fa fa-suitcase-rolling"></i></span>
             <a  href="./?view=cars&opt=reserved" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">VEHICULO</span>
                <span class="info-box-number">RESERVADOS</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
            <div class="info-box mb-3" style="background-color: #C70039;">
              <span class="info-box-icon"><i class="fa fa-road"> </i></span>
             <a  href="./?view=cars&opt=rented" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">VEHICULO</span>
                <span class="info-box-number">RENTADO</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
            <div class="info-box mb-3" style="background-color: #27BEF5;">
              <span class="info-box-icon"><i class="fa fa-globe"></i></span>
               <a  href="./?view=cars&opt=all" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">MOSTRAR TODOS</span>
                <span class="info-box-number">VEHICULO</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
   </div>
 <?php else:?>

 <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
    
       <form id="ssearch">
     <div class="input-group">
           <span class="input-group-text"><i class="fa fa-search"></i></span>
<input style="background-color:#222;" type="hidden" name="view" value="sell">
        <input style="background-color:#222;" type="search"  aria-label="Search" autocomplete="off" id="product_name" name="product_name" class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Buscar Modelo o Año"; break;  case 'EN': echo "Search Model or Year"; break; } ?>">
    </div>
     </form>
  </div>
 
    

      <script type="text/javascript">
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
    $.get("./?action=get&opt=reserved&stock=<?php echo StockData::getPrincipal()->id;?>&id=<?php echo $_GET["id"];?>","product_name="+name+"&go=name",function(data){
      $("#allproducts").html(data);
    });
    
    }else{
    $.get("./?action=get&opt=reserved&stock=<?php echo StockData::getPrincipal()->id;?>","",function(data2){
        $("#allproducts").html(data2);
      

  }); 
    }
}
      $.get("./?action=get&opt=reserved&stock=<?php echo StockData::getPrincipal()->id;?>","",function(data2){
        $("#allproducts").html(data2);
        console.log(data2);
      });

   $("#mesero").click(function(){
      $.get("./?action=get&opt=products","",function(data){
        $(".steps").html(data);       
      });
    });

  </script>
    </div> 
  
<div id="allproducts"></div>
            <!-- /.card -->
        
<?php endif;?>
  
  
</div>

  </div>
    </div><!-- /.row -->
      </div><!-- /.container-fluid -->
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="rented"):?>

<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-car'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Listado de Vehiculos"; break;
 case 'EN': echo "List of Vehicles"; break;
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
    
<!-- Barra de búsqueda -->
      <div class="row mb-2 mt-3">
        <div class="col-md-12">
          <input type="text" id="buscador" class="form-control" placeholder="🔍 Busque por nombre, placa, marca o modelo">
        </div>
      </div>
  
 <style>
  /* Contenedor de botones */
.btn-circles {
  position: absolute;
  top: 50%;
  display: flex;
  flex-direction: column;
  gap: 10px;
  transform: translateY(-50%);
}

.btn-circles.right { right: 10px; }
.btn-circles.left  { left: 10px; }

.circle-btn {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: none;
  background-color: #444;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: 0.3s;
}

.circle-btn:hover {
  background-color: #666;
}




 </style>    
            <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <div class="col-md-8 my-3">
            <!-- MAP & BOX PANE -->
   
    
 <!-- Grid de productos -->
<div class="row" id="grid-vehiculos">
  <?php foreach(CarsData::getAllBySQL("where status=2  and stock_id=".StockData::getPrincipal()->id) as $sells): ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-4">
      <div class="card h-100 shadow-sm text-center" style="background-color:#222;">
        <!-- Imagen -->
        <?php if(!empty($sells->invoice_file)):?>
          <img src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>" class="card-img-top">
        <?php else: $ticket_image = StockData::getPrincipal()->ticket_image; ?>
          <img src="CF-SYSTEMS/storage/configuration/<?php echo $ticket_image; ?>" class="card-img-top">
        <?php endif; ?>
        
        
           <!-- 🔵 Botones circulares siempre visibles -->
   <div class="btn-circles right">
      <a  style="background-color: #27BEF5;" href="./?view=cars&opt=description&id=<?php echo $sells->id;?>" class="circle-btn" title="Info"><i class="fa fa-info"></i></a>
      <a  href="./?view=gallery&opt=all&id=<?php echo $sells->id;?>"  class="circle-btn" title="Galeria"><i class="fa fa-image"></i></a>
       <a  style="background-color: #4DBE04;" href="./?view=cars&opt=edit&id=<?php echo $sells->id;?>"  class="circle-btn" title="Editar"><i class="fa fa-edit"></i></a>
  
<?php if ($sells->status==0):

$base = new Database();
$con = $base->connect();
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()):
if ($x['permits_id']==4): ?>
   
      <a style="background-color: #C70039;" href="./?action=cars&opt=del&id=<?php echo $sells->id;?>"  class="circle-btn" title="Eliminar"><i class="fa fa-trash"></i></a>
      
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
    <?php endif; endwhile; endif;?>
    </div>
    
      <!-- 🔵 Botones circulares IZQUIERDA -->
    <div class="btn-circles left">
      <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=5" style="background-color: orange;" class="circle-btn" title="Favorito"><i class="fas fa-usd"></i></a>
      <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=4" class="circle-btn" title="Compartir"><i class="fa fa-cogs"></i></a>
    </div>


        <!-- Información -->
        <div class="card-body d-flex flex-column">
          <h6 class="fw-bold mb-1" style="font-size:13px;">
            <?php echo strtoupper($sells->getBrand()->name." ".$sells->name." ".$sells->year); ?>
          </h6>
          <p class="text-muted mb-1" style="font-size:14px;">
            <?php echo $sells->plate ?: "Sin Placa"; ?>
          </p>
          <p class="fw-bold mb-1" style="font-size:16px; color:white;">
            <?php echo Core::$symbol." ".number_format($sells->price,2); ?>
          </p>
          <p class="text-muted mb-3" style="font-size:14px;">
            <?php echo strtoupper($sells->getExColor()->name); ?>
          </p>

          <!-- Estado -->
          <?php if ($sells->status==0):?>
            <a href="javascript:void(0)" class="floating-btn message-btn mt-auto" style="background-color:gray; pointer-events:none;">
              <div class="icon-container"><i class="fa fa-car"></i></div>
              <span class="message-text"> DISPONIBLE</span>
            </a>
          <?php elseif ($sells->status==1):?>
            <a href="javascript:void(0)" class="floating-btn message-btn mt-auto disabled-link" style="background-color:orange; pointer-events:none;">
              <div class="icon-container"><i class="fa fa-car"></i></div>
              <span class="message-text"> RESERVADO</span>
            </a>
          <?php elseif ($sells->status==2):?>
            <a href="javascript:void(0)" class="floating-btn message-btn mt-auto disabled-link" style="background-color:#C70039; pointer-events:none;">
              <div class="icon-container"><i class="fa fa-car"></i></div>
              <span class="message-text"> RENTADO</span>
            </a>
          <?php endif; ?>
          
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Mensaje cuando no hay coincidencias -->
<div id="mensaje-vacio" class="text-center mt-4" style="display:none; color:gray; font-size:18px;">
  🚘 No se encontraron vehículos
</div>

<script>
const buscador = document.getElementById("buscador");
const mensajeVacio = document.getElementById("mensaje-vacio");

buscador.addEventListener("keyup", () => {
  let texto = buscador.value.toLowerCase().trim();
  let encontrados = 0;

  // recorrer cada columna del grid
  document.querySelectorAll("#grid-vehiculos > div").forEach(col => {
    const contenido = col.textContent.toLowerCase();

    if (texto === "" || contenido.includes(texto)) {
      col.style.display = "";   // mostrar tarjeta completa
      encontrados++;
    } else {
      col.style.display = "none"; // ocultar tarjeta
    }
  });

  // mostrar/ocultar mensaje vacío
  mensajeVacio.style.display = (encontrados === 0) ? "block" : "none";
});
</script>
    </div>
    
 
          <div class="col-md-4 my-3">
              
            <!-- Info Boxes Style 2 -->
            <div class="info-box mb-3" style="background-color: #4DBE04;">
              <span class="info-box-icon"><i class="fa fa-edit"></i></span>
                <a  href="./?view=cars&opt=new" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">CREAR NUEVO</span>
                <span class="info-box-number">VEHICULO</span>
              </div>
                </a>
              <!-- /.info-box-content -->
            </div>
            
             <div class="info-box mb-3" style="background-color: gray;">
              <span class="info-box-icon"><i class="fa fa-car"></i></span>
             <a  href="./?view=cars&opt=available" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">VEHICULO</span>
                <span class="info-box-number">DISPONIBLE</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            
            <!-- /.info-box -->
            <div class="info-box mb-3" style="background-color: orange;">
              <span class="info-box-icon"><i class="fa fa-suitcase-rolling"></i></span>
             <a  href="./?view=cars&opt=reserved" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">VEHICULO</span>
                <span class="info-box-number">RESERVADOS</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
            <div class="info-box mb-3" style="background-color: #C70039;">
              <span class="info-box-icon"><i class="fa fa-road"> </i></span>
             <a  href="./?view=cars&opt=rented" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">VEHICULO</span>
                <span class="info-box-number">RENTADO</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
            <div class="info-box mb-3" style="background-color: #27BEF5;">
              <span class="info-box-icon"><i class="fa fa-globe"></i></span>
               <a  href="./?view=cars&opt=all" style="color:white;">
              <div class="info-box-content">
                <span class="info-box-text">MOSTRAR TODOS</span>
                <span class="info-box-number">VEHICULO</span>
              </div>
               </a>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
   </div>
 <?php else:?>

 <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
    
       <form id="ssearch">
     <div class="input-group">
           <span class="input-group-text"><i class="fa fa-search"></i></span>
<input style="background-color:#222;" type="hidden" name="view" value="sell">
        <input style="background-color:#222;" type="search"  aria-label="Search" autocomplete="off" id="product_name" name="product_name" class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Buscar Modelo o Año"; break;  case 'EN': echo "Search Model or Year"; break; } ?>">
    </div>
     </form>
  </div>
 
    

      <script type="text/javascript">
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
    $.get("./?action=get&opt=rented&stock=<?php echo StockData::getPrincipal()->id;?>&id=<?php echo $_GET["id"];?>","product_name="+name+"&go=name",function(data){
      $("#allproducts").html(data);
    });
    
    }else{
    $.get("./?action=get&opt=rented&stock=<?php echo StockData::getPrincipal()->id;?>","",function(data2){
        $("#allproducts").html(data2);
      

  }); 
    }
}
      $.get("./?action=get&opt=rented&stock=<?php echo StockData::getPrincipal()->id;?>","",function(data2){
        $("#allproducts").html(data2);
        console.log(data2);
      });

   $("#mesero").click(function(){
      $.get("./?action=get&opt=products","",function(data){
        $(".steps").html(data);       
      });
    });

  </script>
    </div> 
  
<div id="allproducts"></div>
            <!-- /.card -->
        
<?php endif;?>
  
  
</div>

  </div>
    </div><!-- /.row -->
      </div><!-- /.container-fluid -->
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="cogs"):?>

<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><i class='fa fa-car'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Listado de Vehiculos"; break;
 case 'EN': echo "List of Vehicles"; break;
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
            
 <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
    
       <form id="ssearch">
     <div class="input-group">
           <span class="input-group-text"><i class="fa fa-search"></i></span>
<input style="background-color:#222;" type="hidden" name="view" value="sell">
        <input style="background-color:#222;" type="search"  aria-label="Search" autocomplete="off" id="product_name" name="product_name" class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Buscar Modelo o Año"; break;  case 'EN': echo "Search Model or Year"; break; } ?>">
      </form>
    </div>
  </div>
 
    

      <script type="text/javascript">
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
    $.get("./?action=get&opt=cogs&stock=<?php echo StockData::getPrincipal()->id;?>&id=<?php echo $_GET["id"];?>","product_name="+name+"&go=name",function(data){
      $("#allproducts").html(data);
    });
    
    }else{
    $.get("./?action=get&opt=cogs&stock=<?php echo StockData::getPrincipal()->id;?>","",function(data2){
        $("#allproducts").html(data2);
      

  }); 
    }
}
      $.get("./?action=get&opt=cogs&stock=<?php echo StockData::getPrincipal()->id;?>","",function(data2){
        $("#allproducts").html(data2);
        console.log(data2);
      });

   $("#mesero").click(function(){
      $.get("./?action=get&opt=products","",function(data){
        $(".steps").html(data);       
      });
    });

  </script>
    </div>
    <br>

<div id="allproducts"></div>
  
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
           <h1 class="m-0"><i class='fa fa-car'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Agregar Vehiculo"; break;
 case 'EN': echo "Add Vehicle"; break;
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
        
      
          <div class="card" style="background-color:#222;">
<div class="card-body">
<form method="post" id="form-carro" class="form-horizontal" enctype="multipart/form-data">
 <div class="row">

    <div class="col-md-12 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 control-label"> <?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Vehicle"; break;
 case 'EN': echo "Vehicle photo"; break;
}
?></label>
    <input style="background-color:#222;" type="file" name="image">
    </div>



     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Suplidor"; break;
 case 'EN': echo "Supplier"; break;
}
?></label>
       <select style="background-color:#222;" required name="provider_id" id="provider_id" class="form-control select2" >
    <?php foreach(SuppliersData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
     <div class="col-md-2 col-12" id="provider_price">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Suplidor"; break;
 case 'EN': echo "Price/Supplier"; break;
}
?></label>
      <input style="background-color:#222;" type="number" name="provider_price" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Suplidor"; break;
 case 'EN': echo "Price/Supplier"; break;
}
?>">
    </div>
    
    <script>
    
    document.getElementById("provider_price").style.display = "none";
    
    $('#provider_id').change(function(){
    
    var getSelectValue = document.getElementById("provider_id").value;
    
    if(getSelectValue==<?php echo StockData::getPrincipal()->id;?>){
    document.getElementById("provider_price").style.display = "none";
    }else{
    document.getElementById("provider_price").style.display = "inline-block";   
    }
    
    });
    </script>
    
    
    

     <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "(MI/KMS) Actual"; break;
 case 'EN': echo "(MI/KMS) Current"; break;
}
?></label>
      <input style="background-color:#222;" type="number" name="kms_current" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "(MI/KMS) Actual"; break;
 case 'EN': echo "(MI/KMS) Current"; break;
}
?>">
    </div>
    
    <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Cambio de Aceite"; break;
 case 'EN': echo "Oil Change"; break;
}
?></label>
      <input style="background-color:#222;" type="number" name="charge_kms" autocomplete="off"  class="form-control" placeholder="(MI/KMS)?">
    </div>


     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Ficha"; break;
 case 'EN': echo "File"; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="token" autocomplete="off"  class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Del Vehiculo"; break;  case 'EN': echo "From the Vehicle"; break; } ?>">
    </div>
 </div>


<div class="row">

     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Marca"; break;
 case 'EN': echo "Brand"; break;
}
?></label>
      <?php $clients = BrandData::getAll();?>
    <select style="background-color:#222;" required name="brand_id" class="form-control select2">
      <option value="">--- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> ---</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

     <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Modelo"; break;
 case 'EN': echo "Model"; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="name" autocomplete="off"  class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Del Vehiculo"; break;  case 'EN': echo "From the Vehicle"; break; } ?>">
    </div>

    <div class="col-md-1 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Año"; break;
 case 'EN': echo "Year"; break;
}
?> </label>
      <input style="background-color:#222;" type="text" value="<?php echo date("Y");?>" name="year" autocomplete="off"  class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Del Vehiculo"; break;  case 'EN': echo "From the Vehicle"; break; } ?>">
    </div>


<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Categoria"; break;
 case 'EN': echo "Category"; break;
}
?></label>
      <?php $clients = CategoryData::getAll();?>
    <select style="background-color:#222;" name="category_id" class="form-control select2" required>
      <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Color interior"; break;
 case 'EN': echo "Interior color"; break;
}
?></label>
      <?php $clients = ColorData::getAll();?>
    <select style="background-color:#222;" name="interior_id" class="form-control select2" required>
     <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Color exterior"; break;
 case 'EN': echo "Exterior color"; break;
}
?></label>
      <?php $clients = ColorData::getAll();?>
    <select style="background-color:#222;" name="exterior_id" class="form-control select2" required>
    <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>


<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Placa"; break;
 case 'EN': echo "Plate No."; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="plate" autocomplete="off" required class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Placa"; break;
 case 'EN': echo "Plate No."; break;
}
?>">
    </div>
    
         
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Transmisión"; break;
 case 'EN': echo "Transmission"; break;
}
?></label>
      <?php $clients = TransmissionData::getAll();?>
    <select style="background-color:#222;" required name="transmission_id" class="form-control select2">
    <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Combustible"; break;
 case 'EN': echo "Fuel"; break;
}
?></label>
      <?php $clients = FuelData::getAll();?>
    <select style="background-color:#222;" required name="fuel_id" class="form-control select2">
    <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
    
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Matricula"; break;
 case 'EN': echo "Registration No."; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="tuition" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Matricula"; break;
 case 'EN': echo "Registration No."; break;
}
?>">
    </div>
    <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Asientos"; break;
 case 'EN': echo "Number of Seats"; break;
}
?></label>
      <input style="background-color:#222;" type="number" required  name="seat" value="5" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Asientos"; break;
 case 'EN': echo "Number of Seats"; break;
}
?>">
    </div>
    
    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Chasis"; break;
 case 'EN': echo "Chassis No."; break;
}
?></label>
      <input style="background-color:#222;" type="text"  name="chassis" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Chasis"; break;
 case 'EN': echo "Chassis No."; break;
}
?>">
    </div>
    
    
     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Pagina Web"; break;
 case 'EN': echo "Price/Website"; break;
}
?></label>
      <input style="background-color:#222;" type="number" required  name="price" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Pagina Web"; break;
 case 'EN': echo "Price/Website"; break;
}
?>">
    </div>

<div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Seguro de Ley"; break;
 case 'EN': echo "Legal Insurance"; break;
}
?></label>
    <select style="background-color:#222;" name="insurance_id" class="form-control select2">
    <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
        <?php foreach(InsuranceData::getAll() as $ins):?>
        <option value="<?php echo $ins->name;?>"><?php echo $ins->name;?></option>
        <?php endforeach;?>
    </select>
    </div>

  <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
      <input style="background-color:#222;" type="date" name="date_insurance" autocomplete="off" class="form-control">
    </div>

 <div class="col-md-4 col-12">
    
    <input style="background-color:#222;" type="file" class="my-3" name="insurance_file">
</div>

<div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Seguro Full"; break;
 case 'EN': echo "Full Insurance"; break;
}
?></label>
       <select style="background-color:#222;" name="insurance2_id" class="form-control select2">
      <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
        <?php foreach(InsuranceData::getAll() as $ins):?>
        <option value="<?php echo $ins->name;?>"><?php echo $ins->name;?></option>
        <?php endforeach;?>
    </select>
    </div>

  <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
      <input style="background-color:#222;" type="date" name="date2_insurance" autocomplete="off" class="form-control">
    </div>

 <div class="col-md-4 col-12">
    
    <input style="background-color:#222;" type="file" class="my-3" name="insurance2_file">
</div>


</div>
<div class="row my-2">
               
                
                <div class="col-md-12 col-12">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Agregar"; break;
 case 'EN': echo "Add"; break;
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
    if (!json[key]) {
      json[key] = value;
    }
  });
  return json;
}


// Guardar carros localmente si no hay internet
function guardarOffline(carro) {
  let carros = JSON.parse(localStorage.getItem("carros_pendientes")) || [];
  carros.push(carro);
  localStorage.setItem("carros_pendientes", JSON.stringify(carros));
  toastr.info('Guardado localmente. Se enviará cuando vuelva el internet.');
}

// Sincronizar carros cuando vuelva la conexión
function sincronizarcarros() {
  let carros = JSON.parse(localStorage.getItem("carros_pendientes")) || [];
  if (carros.length > 0 && navigator.onLine) {
    carros.forEach((carro, i) => {
      fetch("./?action=cars&opt=add_offline", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(carro)
})

      .then(res => res.text())
      .then(resp => {
        if (resp.trim() === "OK") {
          carros.splice(i, 1);
          localStorage.setItem("carros_pendientes", JSON.stringify(carros));
        }
      });
    });
  }
}

document.getElementById("form-carro").addEventListener("submit", function(e) {
  e.preventDefault();

  const form = document.getElementById("form-carro");
  const carroJSON = formToJSON(form); // Se define aquí

  if (navigator.onLine) {
    const formData = new FormData(form);

    fetch("./?action=cars&opt=add", {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(resp => {
      if (resp.trim() === "OK") {
        toastr.success('Registro agregado correctamente.');
          var delay = 1000;
         setTimeout(function(){ window.location = './?view=cars&opt=all'  }, delay); 
      } else {
        toastr.error('Ya existe ese registro.');
      }
    })
    .catch(() => guardarOffline(carroJSON));
  } else {
    guardarOffline(carroJSON);
  }
});


// Intentar sincronizar cada 5 segundos
setInterval(() => {
  if (navigator.onLine) sincronizarcarros();
}, 5000);

</script>

</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"):
$user = CarsData::getById($_GET["id"]);?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0"><i class='fa fa-edit'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Editar Vehiculo"; break;
 case 'EN': echo "Edit Vehicle"; break;
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
          <div class="card" style="background-color:#222;">
<div class="card-body">
<form method="post" id="form-carro" class="form-horizontal" enctype="multipart/form-data">
 <div class="row">

  <div class="col-md-12 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"> <?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Vehicle"; break;
 case 'EN': echo "Vehicle photo"; break;
}
?></label>
    <?php if($user->invoice_file!=""):?>
    <a href="./../CF-SYSTEMS/storage/invoice_files/<?php echo $user->invoice_file;?>"  class="btn btn-default"><i class="fa fa-file"></i> Archivo carro (<?php echo $user->invoice_file; ?>)</a>
    <?php endif; ?>
    <input style="background-color:#222;" type="file" class="my-2"  name="image">
    </div>
    

     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Suplidor"; break;
 case 'EN': echo "Supplier"; break;
}
?></label>
       <select style="background-color:#222;" required name="provider_id" id="provider_id" class="form-control select2" >
    <?php foreach(SuppliersData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
     <div class="col-md-2 col-12" id="provider_price">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Suplidor"; break;
 case 'EN': echo "Price/Supplier"; break;
}
?></label>
      <input style="background-color:#222;" type="number" name="provider_price" value="<?php echo utf8_decode($user->provider_price);?>"  autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Suplidor"; break;
 case 'EN': echo "Price/Supplier"; break;
}
?>">
    </div>
    
    <script>
    
    document.getElementById("provider_price").style.display = "none";
    
    $('#provider_id').change(function(){
    
    var getSelectValue = document.getElementById("provider_id").value;
    
    if(getSelectValue==<?php echo StockData::getPrincipal()->id;?>){
    document.getElementById("provider_price").style.display = "none";
    }else{
    document.getElementById("provider_price").style.display = "inline-block";   
    }
    
    });
    </script>
    
    
    

     <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "(MI/KMS) Actual"; break;
 case 'EN': echo "(MI/KMS) Current"; break;
}
?></label>
      <input style="background-color:#222;" type="number" name="kms_current" autocomplete="off" value="<?php echo utf8_decode($user->kms_current);?>"   class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "(MI/KMS) Actual"; break;
 case 'EN': echo "(MI/KMS) Current"; break;
}
?>">
    </div>
    
    <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Cambio de Aceite"; break;
 case 'EN': echo "Oil Change"; break;
}
?></label>
      <input style="background-color:#222;" type="number" name="charge_kms" autocomplete="off" value="<?php echo utf8_decode($user->charge_kms);?>"  class="form-control" placeholder="(MI/KMS)?">
    </div>


     <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Ficha"; break;
 case 'EN': echo "File"; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="token" autocomplete="off" value="<?php echo utf8_decode($user->token);?>"  class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Del Vehiculo"; break;  case 'EN': echo "From the Vehicle"; break; } ?>">
    </div>

     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Marca"; break;
 case 'EN': echo "Brand"; break;
}
?></label>
      <?php $clients = BrandData::getAll();?>
    <select style="background-color:#222;" required name="brand_id" class="form-control select2">
      <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach($clients as $client):?>
     <option value="<?php echo $client->id;?>"<?php if($user->brand_id!=null&& $user->brand_id==$client->id){ echo "selected";}?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

     <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Modelo"; break;
 case 'EN': echo "Model"; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="name" value="<?php echo utf8_decode($user->name);?>" autocomplete="off"  class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Del Vehiculo"; break;  case 'EN': echo "From the Vehicle"; break; } ?>">
    </div>

    <div class="col-md-1 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Año"; break;
 case 'EN': echo "Year"; break;
}
?> </label>
      <input style="background-color:#222;" type="text"  value="<?php echo utf8_decode($user->year);?>" name="year" autocomplete="off"  class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Del Vehiculo"; break;  case 'EN': echo "From the Vehicle"; break; } ?>">
    </div>


<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Categoria"; break;
 case 'EN': echo "Category"; break;
}
?></label>
    <select style="background-color:#222;" name="category_id" class="form-control select2" required>
    <?php foreach(CategoryData::getAll() as $client):?>
     <option value="<?php echo $client->id;?>"<?php if($user->category_id!=null&& $user->category_id==$client->id){ echo "selected";}?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Color interior"; break;
 case 'EN': echo "Interior color"; break;
}
?></label>
    <select style="background-color:#222;" name="interior_id" class="form-control select2" required>
    <?php foreach(ColorData::getAll() as $client):?>
     <option value="<?php echo $client->id;?>"<?php if($user->interior_id!=null&& $user->interior_id==$client->id){ echo "selected";}?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Color exterior"; break;
 case 'EN': echo "Exterior color"; break;
}
?></label>
    <select style="background-color:#222;" name="exterior_id" class="form-control select2" required>
    <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach(ColorData::getAll() as $client):?>
     <option value="<?php echo $client->id;?>"<?php if($user->exterior_id!=null&& $user->exterior_id==$client->id){ echo "selected";}?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>


<div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Placa"; break;
 case 'EN': echo "Plate No."; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="plate"  value="<?php echo utf8_decode($user->plate);?>" autocomplete="off" required class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Placa"; break;
 case 'EN': echo "Plate No."; break;
}
?>">
    </div>
    
    
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Matricula"; break;
 case 'EN': echo "Registration No."; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="tuition" value="<?php echo utf8_decode($user->tuition);?>" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Matricula"; break;
 case 'EN': echo "Registration No."; break;
}
?>">
    </div>
    <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Asientos"; break;
 case 'EN': echo "Number of Seats"; break;
}
?></label>
      <input style="background-color:#222;" type="number" required  name="seat"  value="<?php echo utf8_decode($user->seat);?>"  autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Asientos"; break;
 case 'EN': echo "Number of Seats"; break;
}
?>">
    </div>
    
    
    <div class="col-md-5 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Chasis"; break;
 case 'EN': echo "Chassis No."; break;
}
?></label>
      <input style="background-color:#222;" type="text"  name="chassis" autocomplete="off" value="<?php echo utf8_decode($user->chassis);?>"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Chasis"; break;
 case 'EN': echo "Chassis No."; break;
}
?>">
    </div>
    
     
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Transmisión"; break;
 case 'EN': echo "Transmission"; break;
}
?></label>
    <select style="background-color:#222;" required name="transmission_id" class="form-control select2">
    <?php foreach(TransmissionData::getAll() as $client):?>
     <option value="<?php echo $client->id;?>"<?php if($user->transmission_id!=null&& $user->transmission_id==$client->id){ echo "selected";}?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Combustible"; break;
 case 'EN': echo "Fuel"; break;
}
?></label>
    <select style="background-color:#222;" required name="fuel_id" class="form-control select2">
    <?php foreach(FuelData::getAll() as $client):?>
    <option value="<?php echo $client->id;?>"<?php if($user->fuel_id!=null&& $user->fuel_id==$client->id){ echo "selected";}?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. Bateria"; break;
 case 'EN': echo "No. Battery"; break;
}
?></label>
      <input style="background-color:#222;" type="number"  name="no_batery" value="<?php echo utf8_decode($user->no_batery);?>" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. Bateria"; break;
 case 'EN': echo "No. Battery"; break;
}
?>">
    </div>
    
     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Pagina Web"; break;
 case 'EN': echo "Price/Website"; break;
}
?></label>
      <input style="background-color:#222;" type="number" required  name="price" value="<?php echo utf8_decode($user->price);?>" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Pagina Web"; break;
 case 'EN': echo "Price/Website"; break;
}
?>">
    </div>

<div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Seguro de Ley"; break;
 case 'EN': echo "Legal Insurance"; break;
}
?></label>
    <select style="background-color:#222;" name="insurance_id" class="form-control select2">
        <?php foreach(InsuranceData::getAll() as $ins):?>
        <option value="<?php echo $ins->name;?>"><?php echo $ins->name;?></option>
        <?php endforeach;?>
    </select>
    </div>

  <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
      <input style="background-color:#222;" type="date" value="<?php echo utf8_decode($user->date_insurance);?>" name="date_insurance" autocomplete="off" class="form-control">
    </div>

 <div class="col-md-4 col-12">
    
    <input style="background-color:#222;" type="file" class="my-3" name="insurance_file">
</div>

<div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Seguro Full"; break;
 case 'EN': echo "Full Insurance"; break;
}
?></label>
       <select style="background-color:#222;" name="insurance2_id" class="form-control select2">
      <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
        <?php foreach(InsuranceData::getAll() as $ins):?>
        <option value="<?php echo $ins->name;?>"><?php echo $ins->name;?></option>
        <?php endforeach;?>
    </select>
    </div>

  <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
      <input style="background-color:#222;" type="date" value="<?php echo utf8_decode($user->date2_insurance);?>" name="date2_insurance" autocomplete="off" class="form-control">
    </div>

 <div class="col-md-4 col-12">
    
    <input style="background-color:#222;" type="file" class="my-3" name="insurance2_file">
</div>


</div>
<div class="row my-2">
              
                <input style="background-color:#222;" type="hidden" name="user_id" value="<?php echo $user->id;?>">
                <div class="col-md-12 col-12">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Actualizar"; break;
 case 'EN': echo "Update"; break;
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
    if (!json[key]) {
      json[key] = value;
    }
  });
  return json;
}


// Guardar carros localmente si no hay internet
function guardarOffline(carro) {
  let carros = JSON.parse(localStorage.getItem("carros_pendientes")) || [];
  carros.push(carro);
  localStorage.setItem("carros_pendientes", JSON.stringify(carros));
  toastr.info('Guardado localmente. Se enviará cuando vuelva el internet.');
}

// Sincronizar carros cuando vuelva la conexión
function sincronizarcarros() {
  let carros = JSON.parse(localStorage.getItem("carros_pendientes")) || [];
  if (carros.length > 0 && navigator.onLine) {
    carros.forEach((carro, i) => {
      fetch("./?action=cars&opt=upd_offline", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(carro)
})

      .then(res => res.text())
      .then(resp => {
        if (resp.trim() === "OK") {
          carros.splice(i, 1);
          localStorage.setItem("carros_pendientes", JSON.stringify(carros));
        }
      });
    });
  }
}

document.getElementById("form-carro").addEventListener("submit", function(e) {
  e.preventDefault();

  const form = document.getElementById("form-carro");
  const carroJSON = formToJSON(form); // Se define aquí

  if (navigator.onLine) {
    const formData = new FormData(form);

    fetch("./?action=cars&opt=upd", {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(resp => {
      if (resp.trim() === "OK") {
        toastr.success('Registro actualizado correctamente.');
         var delay = 1000;
         setTimeout(function(){ window.location = './?view=cars&opt=all'  }, delay); 
      } else {
        toastr.error('Ya existe ese registro.');
      }
    })
    .catch(() => guardarOffline(carroJSON));
  } else {
    guardarOffline(carroJSON);
  }
});


// Intentar sincronizar cada 5 segundos
setInterval(() => {
  if (navigator.onLine) sincronizarcarros();
}, 5000);

</script>


 <div class="floating-buttons">
    <a onclick="history.back()"  class="floating-1"><i class="fa fa-arrow-left"></i></a>
  </div>
  
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="description"):
$user = CarsData::getById($_GET["id"]);?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0"><i class='fa fa-car'></i>  <?php  switch (Core::$user->language){  case 'ES': echo "Informacion Del Vehiculo"; break;  case 'EN': echo "Vehicle Information"; break; } ?></h1>
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

        <div class="row">
            <div class="col-md-4">
            
                  <div class="card" style="background-color:#222;">
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <div class="card-body">
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Nombre del Rent Car"; break;  case 'EN': echo "Rent Car Name"; break; } ?>: </label>
                        <?php echo $user->getStock()->name;?>
                        <br>
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Vehiculo"; break;  case 'EN': echo "Vehicle"; break; } ?>: </label>
                        <?php echo $user->getBrand()->name;?><br>
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Modelo"; break;  case 'EN': echo "Model"; break; } ?>: </label>
                        <?php echo $user->name;?>
                        <br>
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Año"; break;  case 'EN': echo "Year"; break; } ?>: </label>
                        <?php echo $user->year;?>
                        <br>
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Categoria"; break;  case 'EN': echo "Category"; break; } ?>: </label>
                        <?php echo $user->getCategory()->name;?>
                        <br>
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Color Interior"; break;  case 'EN': echo "Interior color"; break; } ?>: </label>
                        <?php echo $user->getInColor()->name;?> 
                        <br>
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Color Exterior"; break;  case 'EN': echo "Exterior color"; break; } ?>: </label>
                        <?php echo $user->getExColor()->name;?>
                       
                      </div>
                    </div>
                  </div>
            </div>


            <div class="col-md-4">
            
                  <div class="card" style="background-color:#222;">
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <div class="card-body">
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Aseguradora"; break;  case 'EN': echo "Insurance"; break; } ?>: </label>
                        <?php echo $user->insurance_id;?>
                        <br>
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Vencimiento"; break;  case 'EN': echo "Maturity"; break; } ?>: </label>
                        <?php echo  date("d-m-Y",strtotime($user->date_insurance));?>
                        
                        <br>
                        
                    </div>
                  </div>


            </div>
             <div class="card" style="background-color:#222;">
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <center>
                        <label class="my-2"><?php  switch (Core::$user->language){  case 'ES': echo "Foto del Seguro"; break;  case 'EN': echo "Insurance Photo"; break; } ?>: </label>
                          <div class="card-body">
                        <?php if ($user->insurance_file!=""):?>
  <img src="../CF-SYSTEMS/storage/invoice_files/<?php echo $user->insurance_file;?>" class="product-image" style="width: 50%;">
                         <?php endif;?>
                      </div>
                      </center>
                    </div>
                  </div>
            </div>

            <div class="col-md-4">
            
                  <div class="card" style="background-color:#222;">
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <center>
                        <label class="my-2"><?php  switch (Core::$user->language){  case 'ES': echo "Foto Del Vehiculo"; break;  case 'EN': echo "Vehicle Photo"; break; } ?>: </label>
                      <div class="card-body">
                        <?php if ($user->invoice_file!=""):?>
  <img src="../CF-SYSTEMS/storage/invoice_files/<?php echo $user->invoice_file;?>" class="product-image" style="width: 50%;">
                         <?php endif;?>
                      </div>
                    </center>
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


 <div class="floating-buttons">
    <a onclick="history.back()"  class="floating-1"><i class="fa fa-arrow-left"></i></a>
  </div>
</section>


<?php endif; ?>