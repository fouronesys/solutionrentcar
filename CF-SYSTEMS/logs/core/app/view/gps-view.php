<?php if(isset($_GET["opt"]) && $_GET["opt"]=="map"):?>
<section class="content">
<div class="row">
  <div class="col-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0"><i class="fa fa-map-marker"></i> GPS Tracking</h1>
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

         
          </div><!-- /.col -->
      

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<style>#map { height: 550px; width: 100%; }</style>

<div class="row" hidden>
    
  <div class="col-6 col-sm-3 col-md-4 col-lg-4 col-xl-4">
<select class="form-control" id="vehicleSelect"></select>
</div>

<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
<input class="form-control"  type="date" id="from">
</div>

<div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
<input class="form-control"  type="date" id="to">
</div>

<div class="col-6 col-sm-2 col-md-2 col-lg-2 col-xl-2">
<input onclick="cargarHistorialFiltrado()" type="submit" class="btn btn-warning btn-block" value="Cargar historial">
</div>

</div>


<div class="my-2" id="map"></div>
<?php 

$stock_state = StatesData::getById(StockData::getPrincipal()->location); 
// Supongamos que en la tabla tienes latitud y longitud
$lat = $stock_state->latitude;
$lng = $stock_state->longitude;
?>
<script>
var map = L.map('map').setView([<?php echo $lat; ?>, <?php echo $lng; ?>], 10);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);


// === Capas base ===
var callejero = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; OpenStreetMap contributors',
  maxZoom: 19
});

var satelite = L.tileLayer(
  'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', 
  {
    attribution: 'Tiles &copy; Esri &mdash; Source: Esri, Earthstar Geographics',
    maxZoom: 20,         // Leaflet permite hasta 20
    maxNativeZoom: 19    // Esri solo tiene datos hasta 17 -> evita "map data not available"
  }
);

var noche = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
  attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/">CARTO</a>',
  subdomains: 'abcd',
  maxZoom: 20
});

// Activar callejero por defecto
callejero.addTo(map);

// Control para cambiar capas
L.control.layers({
  "Callejero": callejero,
  "Satélite": satelite,
  "Noche": noche
}).addTo(map);

window.markers = [];
window.routes = [];

// Cargar vehículos para el select y mapa
function cargarVehiculos(){
    fetch("./?action=get_vehicles_locations&mode=last")
    .then(res=>res.json())
    .then(data=>{
        const select = document.getElementById('vehicleSelect');
        select.innerHTML = '<option value="">Todos</option>';
        window.markers.forEach(m=>map.removeLayer(m));
        window.markers = [];
        window.routes.forEach(r=>map.removeLayer(r));
        window.routes = [];

        data.vehicles.forEach(v=>{
            // llenar select
            let opt = document.createElement('option');
            opt.value = v.vehicle_id;
            opt.textContent = `${v.name} (${v.plate})`;
            select.appendChild(opt);

           if (v.latitude && v.longitude) {
    let icon = L.icon({
        iconUrl: v.invoice_file 
            ? `CF-SYSTEMS/storage/invoice_files/${v.invoice_file}` 
            : `CF-SYSTEMS/img/veh2.png`, // imagen por defecto
        iconSize: [50, 50]
    });


                let marker = L.marker([v.latitude,v.longitude],{icon:icon})
                    .addTo(map)
                    .bindPopup(`<b>${v.name} (${v.plate})</b><br>
                                Velocidad: ${v.speed} km/h<br>
                                Última vez: ${v.created_at}<br>
                                <button class="btn btn-warning" onclick="verHistorial(${v.vehicle_id})">Ver Ubicacion</button>`);
                window.markers.push(marker);
            }
        });
    })
    .catch(err=>console.error("Error al cargar vehículos:",err));
}

// Cargar historial de un vehículo
function verHistorial(vehicle_id){
    fetch(`./?action=get_vehicles_locations&mode=history&vehicle_id=${vehicle_id}`)
    .then(res=>res.json())
    .then(data=>{
        dibujarRuta(data.positions);
    })
    .catch(err=>console.error("Error al cargar historial:",err));
}

// Cargar historial filtrado por fechas
function cargarHistorialFiltrado(){
    const vehicle_id = document.getElementById('vehicleSelect').value;
    const from = document.getElementById('from').value;
    const to = document.getElementById('to').value;

    if(!vehicle_id){ alert("Seleccione un vehículo"); return; }

    fetch(`./?action=get_vehicles_locations&mode=history&vehicle_id=${vehicle_id}&from=${from}&to=${to}`)
    .then(res=>res.json())
    .then(data=>{
        dibujarRuta(data.positions);
    })
    .catch(err=>console.error("Error al cargar historial filtrado:",err));
}

// Dibujar ruta con color según velocidad
function dibujarRuta(positions){
    // borrar ruta anterior
    window.routes.forEach(r=>map.removeLayer(r));
    window.routes = [];

    if(positions.length==0){ alert("No hay posiciones"); return; }

    let latlngs = positions.map(p=>[p.latitude,p.longitude]);
    let polyline = L.polyline(latlngs,{
        color:'blue',
        weight:4
    }).addTo(map);
    window.routes.push(polyline);

    // marcador inicial
    let startMarker = L.marker(latlngs[0],{title:"Inicio"}).addTo(map);
    window.routes.push(startMarker);

    // marcador final
    let endMarker = L.marker(latlngs[latlngs.length-1],{title:"Fin"}).addTo(map);
    window.routes.push(endMarker);

    map.fitBounds(polyline.getBounds());
}

// Cargar vehículos al inicio
cargarVehiculos();
setInterval(cargarVehiculos,1000);
</script>

  </div>
</div>
  </div><!-- /.row -->
</section>


<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="assign"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-street-view'></i> Asignar Dispositivo</h1>
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
          <div class="card"  style="background-color:#222;">
<div class="card-body">
<?php if(isset($_GET['id'])): $user = BrandData::getById($_GET["id"]);?>
    <form class="form-horizontal" method="post" id="upd" role="form">
 <div class="row">
      <div class="col-md-8 col-8">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre</label>
      <input  style="background-color:#333;" type="text" name="name" autocomplete="off"  value="<?php echo utf8_decode($user->name);?>"  class="form-control" placeholder="Nombre del Marca">
    </div>

    
               
                <div class="col-md-4 col-4">

    <label for="inputEmail1" class="col-md-12 col-12 my-3 control-label"></label>
                  <input type="hidden" name="user_id" value="<?php echo $user->id;?>">
                   <button class="btn btn-warning btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
</form>


<script>
            jQuery(document).ready(function(){
            jQuery("#upd").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=brands&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Marca Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=brands&opt=all'  }, delay); 
                     
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
<form class="form-horizontal" method="post" id="add" role="form">
  <div class="row">
    <div class="col-md-3 col-6">
      <label class="col-md-12 col-12 control-label">Vehiculos</label>
      <select style="background-color: #333;" class="form-control select2" name="vehicle_id">
      <?php foreach (CarsData::getALL() as $cars): ?>
      <option value="<?php echo $cars->id;?>"><?php echo $cars->getBrand()->name." ".$cars->name." ".$cars->year." F: [".$cars->token."]";?></option>
      <?php endforeach ?>
      </select>

    </div>
    
    <div class="col-md-3 col-6">
      <label class="col-md-12 col-12 control-label">GPS</label>
      <select style="background-color: #333;" class="form-control select2" name="gps_id">
      <?php foreach (DeviceData::getALL() as $device): ?>
      <option value="<?php echo $device->id;?>"><?php echo $device->imei;?></option>
      <?php endforeach ?>
      </select>

    </div>
    <div class="col-md-3 col-12">
      <label class="col-md-12 col-12 control-label"></label>
      <button class="btn btn-warning btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
    </div>
  </div>
</form>

<script>
            jQuery(document).ready(function(){
            jQuery("#add").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=gps&opt=add",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("GPS Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=gps&opt=assign'  }, delay); 
                     
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

<?php 
$base = new Database();
$con = $base->connect();

// Hacemos join entre cars y gps_devices
$sql = "SELECT c.name AS name, c.brand_id, c.year, c.token, g.imei 
        FROM cars c
        LEFT JOIN gps_devices g ON g.id = c.gps_id";
$query = $con->query($sql);

if($query->num_rows > 0): ?>
<div class="card" style="background-color:#222;">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="example1">
        <thead>
          <th>Nombre</th>
          <th>IMEI</th>
        </thead>

        <tfoot>
          <tr>
            <th>Nombre</th>
            <th>IMEI</th>
          </tr>
        </tfoot>

        <?php while($r = $query->fetch_assoc()){ ?>
          <tr>
            <td><?php echo BrandData::getById($r['brand_id'])->name." ".$r['name']." ".$r['year']." F: [".$r['token']."]";?></td>
            <td><?php echo $r['imei'] ? $r['imei'] : 'No asignado'; ?></td>
          </tr>
        <?php } ?>
      </table>
    </div><!-- /.table-responsive -->
  </div><!-- /.card-body -->
</div><!-- /.card -->
<?php else: ?>
<div class="card">
  <div class="card-header">
    <h2>No hay Carros</h2>
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

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="add"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-street-view'></i> Agregar GPS</h1>
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
          <div class="card"  style="background-color:#222;">
<div class="card-body">
<?php if(isset($_GET['id'])): $user = BrandData::getById($_GET["id"]);?>
    <form class="form-horizontal" method="post" id="upd" role="form">
 <div class="row">
    <div class="col-md-3 col-6">
      <label class="col-md-12 col-12 control-label">Nombre</label>
      <input style="background-color:#333;" type="text" name="name" autocomplete="off" class="form-control" placeholder="Nombre" required>
    </div>
    
    <div class="col-md-3 col-6">
      <label class="col-md-12 col-12 control-label">IMEI</label>
      <input style="background-color:#333;" type="text" name="imei" autocomplete="off" class="form-control" placeholder="IMEI" required>
    </div>
    
    
      <div class="col-md-3 col-6">
      <label class="col-md-12 col-12 control-label">latitude</label>
      <input style="background-color:#333;" type="text" name="latitude" autocomplete="off" class="form-control" placeholder="latitude" required>
    </div>
    
      <div class="col-md-3 col-6">
      <label class="col-md-12 col-12 control-label">longitude</label>
      <input style="background-color:#333;" type="text" name="longitude" autocomplete="off" class="form-control" placeholder="longitude" required>
    </div>
    
    <div class="col-md-12 col-12">
      <label class="col-md-12 col-12 control-label">&nbsp;</label>
    <input type="hidden" name="user_id" value="<?php echo $user->id;?>">
      <button class="btn btn-warning btn-block "><i class="fa fa-check"></i> Finalizar</button>
    </div>
  </div>
</form>


<script>
            jQuery(document).ready(function(){
            jQuery("#upd").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=gps&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("GPS Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=brands&opt=add'  }, delay); 
                     
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
<form class="form-horizontal" method="post" id="add" role="form">
  <div class="row">
  <div class="col-md-3 col-6">
      <label class="col-md-12 col-12 control-label">Nombre</label>
      <input style="background-color:#333;" type="text" name="name" autocomplete="off" class="form-control" placeholder="Nombre" required>
    </div>
    
    <div class="col-md-3 col-6">
      <label class="col-md-12 col-12 control-label">IMEI</label>
      <input style="background-color:#333;" type="text" name="imei" autocomplete="off" class="form-control" placeholder="IMEI" required>
    </div>
    
    
      <div class="col-md-3 col-6">
      <label class="col-md-12 col-12 control-label">latitude</label>
      <input style="background-color:#333;" type="text" name="latitude" autocomplete="off" class="form-control" placeholder="latitude" required>
    </div>
    
      <div class="col-md-3 col-6">
      <label class="col-md-12 col-12 control-label">longitude</label>
      <input style="background-color:#333;" type="text" name="longitude" autocomplete="off" class="form-control" placeholder="longitude" required>
    </div>
    
    
    <div class="col-md-12 col-12">
      <label class="col-md-12 col-12 control-label">&nbsp;</label>
      <button class="btn btn-warning btn-block "><i class="fa fa-check"></i> Finalizar</button>
    </div>
  </div>
</form>

<script>
            jQuery(document).ready(function(){
            jQuery("#add").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=gps&opt=addgps",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("GPS Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=gps&opt=add'  }, delay); 
                     
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

<?php 
$base = new Database();
$con = $base->connect();

// Hacemos join entre cars y gps_devices
$sql = "SELECT * FROM gps_devices";
$query = $con->query($sql);

if($query->num_rows > 0): ?>
<div class="card" style="background-color:#222;">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="example1">
        <thead>
          <th>Nombre</th>
          <th>IMEI</th>
          <th>Latitude</th>
          <th>Longitude</th>
          <th>Velocidad</th>
        </thead>

        <tfoot>
          <tr>
          <th>Nombre</th>
          <th>IMEI</th>
          <th>Latitude</th>
          <th>Longitude</th>
          <th>Velocidad</th>
          </tr>
        </tfoot>

        <?php while($r = $query->fetch_assoc()){ $user = DeviceData::getByStoreId($r['id']); ?>
          <tr>
            <td><?php echo $r['name'];?></td>
            <td><?php echo $r['imei'] ? $r['imei'] : 'No asignado'; ?></td>
            <td><?php echo $user->latitude; ?></td>
            <td><?php echo $user->longitude; ?></td>
            <td><?php echo $user->speed; ?></td>
          </tr>
        <?php } ?>
      </table>
    </div><!-- /.table-responsive -->
  </div><!-- /.card-body -->
</div><!-- /.card -->
<?php else: ?>
<div class="card">
  <div class="card-header">
    <h2>No hay Carros</h2>
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

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>

<section class="content">
<div class="row">
  <div class="col-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0"><i class="fa fa-balance-scale"></i> GPS Tracking </h1>
          </div><!-- /.col -->
         
          </div><!-- /.col -->
      
    </div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<style>#map { height: 600px; width: 100%; }</style>

<form id="formHistorial">
  <div class="row">
      
    <div class="col-md-3 col-6">
      <label class="col-md-12 col-12 control-label">Vehiculos</label>
  <select id="vehicle_id" class="form-control select2" name="vehicle_id" required>
    <?php foreach(CarsData::getAll() as $cars):?>
     <option value="<?php echo $cars->id;?>" <?php if($cars->id==$user->car_id){echo 'selected';}?>><?php echo $cars->getBrand()->name." ".$cars->name." ".$cars->year." F: [".$cars->token."]";?></option> 
    <?php endforeach;?>   
  </select>
   </div>
  
    <div class="col-md-3 col-6">
         <label class="col-md-12 col-12 control-label">&nbsp;</label>
    <button type="submit"  class="btn btn-warning btn-block">Ver historial</button>
    </div>
</form>
 
<div id="map"></div>

<script>
var map = L.map('map').setView([18.475, -69.890], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19
}).addTo(map);

document.getElementById("formHistorial").addEventListener("submit", function(e){
  e.preventDefault();
  let vehicleId = document.getElementById("vehicle_id").value;
  if(!vehicleId) return alert("Seleccione un vehículo");

  fetch(`./?action=get_vehicles_locations&mode=history&vehicle_id=${vehicleId}`)
    .then(res => res.json())
    .then(data => {
      if(data.positions && data.positions.length){
        // Limpiar capas anteriores
        if(window.routeLayer){
          map.removeLayer(window.routeLayer);
        }

        let coords = data.positions.map(p => [p.latitude, p.longitude]);
        window.routeLayer = L.polyline(coords, {color: 'blue'}).addTo(map);
        map.fitBounds(window.routeLayer.getBounds());

        // Marcadores
        let first = coords[coords.length-1]; // más viejo
        let last = coords[0]; // más reciente
        L.marker(first).addTo(map).bindPopup("Inicio");
        L.marker(last).addTo(map).bindPopup("Última posición");
      } else {
        alert("No hay historial para este vehículo");
      }
    })
    .catch(err => console.error("Error al cargar historial:", err));
});
</script>


  </div>
</div>
  </div><!-- /.row -->
</section>
<?php endif;?>