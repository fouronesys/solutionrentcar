<?php if(isset($_GET["opt"]) && $_GET["opt"]=="map"):?>
<section class="content">
<div class="row">
  <div class="col-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><i class="fa fa-map-marker"></i> GPS Tracking</h1>
          </div>

          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active">
                <i class='fa fa-history'></i>
                <span id="reloj"></span>
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div>

<script>
function actualizarReloj() {
  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  const reloj = document.getElementById("reloj");
  if(reloj){ reloj.textContent = `${horas}:${minutos}:${segundos}`; }
}
setInterval(actualizarReloj, 1000);
actualizarReloj();
</script>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
  #map {
    height: 550px;
    width: 100%;
    border-radius:16px;
    overflow:hidden;
    border:1px solid rgba(255,255,255,.10);
  }
  .gps-top-card{
    background:#16181d;
    border:1px solid rgba(255,255,255,.08);
    border-radius:18px;
    box-shadow:0 10px 28px rgba(0,0,0,.35);
    padding:12px;
    margin-bottom:12px;
  }
  .gps-badge{
    display:inline-block;
    padding:6px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:900;
    background:#0f1115;
    border:1px solid rgba(255,255,255,.10);
    color:#fff;
    margin-right:6px;
    margin-bottom:6px;
  }
</style>

<div class="gps-top-card">
  <span class="gps-badge" id="gpsTotalFull">0 vehículos</span>
  <span class="gps-badge" id="gpsMovingFull">0 moviendo</span>
  <span class="gps-badge" id="gpsStoppedFull">0 detenidos</span>
  <span class="gps-badge" id="gpsNoSignalFull">0 sin señal</span>
</div>

<div class="row" hidden>
  <div class="col-6 col-sm-3 col-md-4 col-lg-4 col-xl-4">
    <select class="form-control" id="vehicleSelect"></select>
  </div>

  <div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
    <input class="form-control" type="date" id="from">
  </div>

  <div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
    <input class="form-control" type="date" id="to">
  </div>

  <div class="col-6 col-sm-2 col-md-2 col-lg-2 col-xl-2">
    <input onclick="cargarHistorialFiltrado()" type="submit" class="btn btn-warning btn-block" value="Cargar historial">
  </div>
</div>

<div class="my-2" id="map"></div>

<?php
$stock_state = StatesData::getById(StockData::getPrincipal()->location);
$lat = $stock_state && $stock_state->latitude ? $stock_state->latitude : 19.4517;
$lng = $stock_state && $stock_state->longitude ? $stock_state->longitude : -70.6970;
?>

<script>
var map = L.map('map').setView([<?php echo $lat; ?>, <?php echo $lng; ?>], 10);

var callejero = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; OpenStreetMap contributors',
  maxZoom: 19
});

var satelite = L.tileLayer(
  'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
  {
    attribution: 'Tiles &copy; Esri &mdash; Source: Esri, Earthstar Geographics',
    maxZoom: 20,
    maxNativeZoom: 19
  }
);

var noche = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
  attribution: '&copy; OpenStreetMap &copy; CARTO',
  subdomains: 'abcd',
  maxZoom: 20
});

callejero.addTo(map);

L.control.layers({
  "Callejero": callejero,
  "Satélite": satelite,
  "Noche": noche
}).addTo(map);

window.markers = {};
window.routes = [];
window.firstLoadGps = true;

function gpsDiffMinutes(createdAt){
  if(!createdAt){ return 999999; }
  var lastDate = new Date(String(createdAt).replace(" ", "T"));
  var now = new Date();
  return (now - lastDate) / 60000;
}

function gpsStatus(v){
  var diffMin = gpsDiffMinutes(v.created_at);

  if(diffMin > 30){
    return 'sin_senal';
  }

  if(parseFloat(v.speed) > 0){
    return 'moviendo';
  }

  return 'detenido';
}

function gpsColor(v){
  var st = gpsStatus(v);
  if(st === 'sin_senal') return '#e74c3c';
  if(st === 'moviendo') return '#2ecc71';
  return '#f1c40f';
}

function crearIconoGps(v){
  var color = gpsColor(v);
  var imageHtml = '';

  if(v.invoice_file){
    imageHtml = '<img src="CF-SYSTEMS/storage/invoice_files/' + v.invoice_file + '" style="width:100%;height:100%;object-fit:cover;">';
  }else{
    imageHtml = '<i class="fa fa-car" style="color:white;font-size:18px;"></i>';
  }

  return L.divIcon({
    className: '',
    html:
      '<div style="'+
      'width:52px;'+
      'height:52px;'+
      'border-radius:50%;'+
      'background:'+color+';'+
      'border:3px solid white;'+
      'box-shadow:0 4px 15px rgba(0,0,0,.55);'+
      'display:flex;'+
      'align-items:center;'+
      'justify-content:center;'+
      'overflow:hidden;'+
      '">'+imageHtml+'</div>',
    iconSize: [52,52],
    iconAnchor: [26,26],
    popupAnchor: [0,-22]
  });
}

function cargarVehiculos(){
  fetch("./?action=get_vehicles_locations&mode=last")
  .then(function(res){ return res.json(); })
  .then(function(data){
    const select = document.getElementById('vehicleSelect');
    if(select){ select.innerHTML = '<option value="">Todos</option>'; }

    let total = 0;
    let moving = 0;
    let stopped = 0;
    let noSignal = 0;
    let bounds = [];

    if(!data.vehicles){ return; }

    data.vehicles.forEach(function(v){
      if(select){
        let opt = document.createElement('option');
        opt.value = v.vehicle_id;
        opt.textContent = v.name + ' (' + v.plate + ')';
        select.appendChild(opt);
      }

      if(v.latitude && v.longitude){
        total++;

        let st = gpsStatus(v);
        if(st === 'moviendo') moving++;
        else if(st === 'sin_senal') noSignal++;
        else stopped++;

        let lat = parseFloat(v.latitude);
        let lng = parseFloat(v.longitude);
        let key = v.vehicle_id;

        let estadoTexto = 'Detenido';
        if(st === 'moviendo') estadoTexto = 'En movimiento';
        if(st === 'sin_senal') estadoTexto = 'Sin señal';

        let popup =
          '<b>' + v.name + ' (' + v.plate + ')</b><br>'+
          'Estado: <b>' + estadoTexto + '</b><br>'+
          'Velocidad: ' + v.speed + ' km/h<br>'+
          'Última señal: ' + v.created_at + '<br>'+
          '<button class="btn btn-warning btn-sm mt-2" onclick="verHistorial(' + v.vehicle_id + ')">Ver ubicación</button>';

        if(window.markers[key]){
          window.markers[key].setLatLng([lat,lng]);
          window.markers[key].setIcon(crearIconoGps(v));
          window.markers[key].setPopupContent(popup);
        }else{
          window.markers[key] = L.marker([lat,lng], {
            icon: crearIconoGps(v)
          }).addTo(map).bindPopup(popup);
        }

        bounds.push([lat,lng]);
      }
    });

    let gpsTotalFull = document.getElementById('gpsTotalFull');
    let gpsMovingFull = document.getElementById('gpsMovingFull');
    let gpsStoppedFull = document.getElementById('gpsStoppedFull');
    let gpsNoSignalFull = document.getElementById('gpsNoSignalFull');

    if(gpsTotalFull) gpsTotalFull.innerHTML = total + ' vehículos';
    if(gpsMovingFull) gpsMovingFull.innerHTML = moving + ' moviendo';
    if(gpsStoppedFull) gpsStoppedFull.innerHTML = stopped + ' detenidos';
    if(gpsNoSignalFull) gpsNoSignalFull.innerHTML = noSignal + ' sin señal';

    if(window.firstLoadGps && bounds.length > 0){
      map.fitBounds(bounds, {padding:[40,40]});
      window.firstLoadGps = false;
    }
  })
  .catch(function(err){
    console.error("Error al cargar vehículos:", err);
  });
}

function verHistorial(vehicle_id){
  fetch("./?action=get_vehicles_locations&mode=history&vehicle_id=" + vehicle_id)
  .then(function(res){ return res.json(); })
  .then(function(data){
    dibujarRuta(data.positions);
  })
  .catch(function(err){
    console.error("Error al cargar historial:", err);
  });
}

function cargarHistorialFiltrado(){
  const vehicle_id = document.getElementById('vehicleSelect').value;
  const from = document.getElementById('from').value;
  const to = document.getElementById('to').value;

  if(!vehicle_id){
    alert("Seleccione un vehículo");
    return;
  }

  fetch("./?action=get_vehicles_locations&mode=history&vehicle_id=" + vehicle_id + "&from=" + from + "&to=" + to)
  .then(function(res){ return res.json(); })
  .then(function(data){
    dibujarRuta(data.positions);
  })
  .catch(function(err){
    console.error("Error al cargar historial filtrado:", err);
  });
}

function dibujarRuta(positions){
  window.routes.forEach(function(r){ map.removeLayer(r); });
  window.routes = [];

  if(!positions || positions.length == 0){
    alert("No hay posiciones");
    return;
  }

  let latlngs = positions.map(function(p){
    return [parseFloat(p.latitude), parseFloat(p.longitude)];
  });

  let polyline = L.polyline(latlngs, {
    color:'blue',
    weight:4
  }).addTo(map);

  window.routes.push(polyline);

  let startMarker = L.marker(latlngs[0], {title:"Inicio"}).addTo(map).bindPopup("Inicio");
  let endMarker = L.marker(latlngs[latlngs.length-1], {title:"Fin"}).addTo(map).bindPopup("Fin");

  window.routes.push(startMarker);
  window.routes.push(endMarker);

  map.fitBounds(polyline.getBounds());
}

cargarVehiculos();
setInterval(cargarVehiculos, 10000);
</script>

  </div>
</div>
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
function actualizarReloj() {
  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  const reloj = document.getElementById("reloj");
  if(reloj){ reloj.textContent = `${horas}:${minutos}:${segundos}`; }
}
setInterval(actualizarReloj, 1000);
actualizarReloj();
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
        if(html=='true'){
          $.jGrowl("Marca Exito!", { sticky: true });
          $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
          setTimeout(function(){ window.location = './?view=brands&opt=all'  }, 1000);
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
        if(html=='true'){
          $.jGrowl("GPS Exito!", { sticky: true });
          $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
          setTimeout(function(){ window.location = './?view=gps&opt=assign'  }, 1000);
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
    </div>
  </div>
</div>
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
          </div>
          
       <div class="col-sm-6">
  <ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">
      <i class='fa fa-history'></i> 
      <span id="reloj"></span>
    </li>
  </ol>
</div>

<script>
function actualizarReloj() {
  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  const reloj = document.getElementById("reloj");
  if(reloj){ reloj.textContent = `${horas}:${minutos}:${segundos}`; }
}
setInterval(actualizarReloj, 1000);
actualizarReloj();
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
        if(html=='true'){
          $.jGrowl("GPS Exito!", { sticky: true });
          $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
          setTimeout(function(){ window.location = './?view=brands&opt=add'  }, 1000);
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
        if(html=='true'){
          $.jGrowl("GPS Exito!", { sticky: true });
          $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
          setTimeout(function(){ window.location = './?view=gps&opt=add'  }, 1000);
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
            <td><?php echo $user ? $user->latitude : ''; ?></td>
            <td><?php echo $user ? $user->longitude : ''; ?></td>
            <td><?php echo $user ? $user->speed : ''; ?></td>
          </tr>
        <?php } ?>
      </table>
    </div>
  </div>
</div>
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
          </div>
        </div>
    </div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<style>#map { height: 600px; width: 100%; border-radius:16px; }</style>

<form id="formHistorial">
  <div class="row">
    <div class="col-md-3 col-6">
      <label class="col-md-12 col-12 control-label">Vehiculos</label>
      <select id="vehicle_id" class="form-control select2" name="vehicle_id" required>
        <?php foreach(CarsData::getAll() as $cars):?>
          <option value="<?php echo $cars->id;?>"><?php echo $cars->getBrand()->name." ".$cars->name." ".$cars->year." F: [".$cars->token."]";?></option>
        <?php endforeach;?>
      </select>
    </div>
  
    <div class="col-md-3 col-6">
      <label class="col-md-12 col-12 control-label">&nbsp;</label>
      <button type="submit" class="btn btn-warning btn-block">Ver historial</button>
    </div>
  </div>
</form>
 
<div id="map"></div>

<script>
var map = L.map('map').setView([18.475, -69.890], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19
}).addTo(map);

window.historyMarkers = [];

function clearHistoryLayers(){
  if(window.routeLayer){
    map.removeLayer(window.routeLayer);
    window.routeLayer = null;
  }

  window.historyMarkers.forEach(function(m){
    map.removeLayer(m);
  });
  window.historyMarkers = [];
}

document.getElementById("formHistorial").addEventListener("submit", function(e){
  e.preventDefault();
  let vehicleId = document.getElementById("vehicle_id").value;
  if(!vehicleId) return alert("Seleccione un vehículo");

  fetch("./?action=get_vehicles_locations&mode=history&vehicle_id=" + vehicleId)
    .then(function(res){ return res.json(); })
    .then(function(data){
      if(data.positions && data.positions.length){
        clearHistoryLayers();

        let coords = data.positions.map(function(p){
          return [parseFloat(p.latitude), parseFloat(p.longitude)];
        });

        window.routeLayer = L.polyline(coords, {color: 'blue', weight:4}).addTo(map);
        map.fitBounds(window.routeLayer.getBounds());

        let first = coords[0];
        let last = coords[coords.length - 1];

        window.historyMarkers.push(L.marker(first).addTo(map).bindPopup("Inicio"));
        window.historyMarkers.push(L.marker(last).addTo(map).bindPopup("Última posición"));
      } else {
        alert("No hay historial para este vehículo");
      }
    })
    .catch(function(err){
      console.error("Error al cargar historial:", err);
    });
});
</script>

  </div>
</div>
  </div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="risks"):
// =======================================================
// 🛰️ PASO 6: CENTRO DE RIESGO GPS INTELIGENTE
// - Detecta GPS sin señal
// - Exceso de velocidad
// - Movimiento sospechoso en madrugada
// - Vehículos detenidos demasiado tiempo
// - Ranking de actividad GPS
// Usa: cars.gps_id, gps_devices, gps_positions
// =======================================================

$db  = new Database();
$con = $db->connect();
$stock_id = intval(StockData::getPrincipal()->id);

$now = date("Y-m-d H:i:s");
$today = date("Y-m-d");
$last24 = date("Y-m-d H:i:s", strtotime("-24 hours"));
$last7  = date("Y-m-d H:i:s", strtotime("-7 days"));

function gr_badge($level){
  if($level=="ALTO") return "badge badge-danger";
  if($level=="MEDIO") return "badge badge-warning";
  if($level=="LEVE") return "badge badge-info";
  return "badge badge-success";
}

function gr_color($level){
  if($level=="ALTO") return "#e74c3c";
  if($level=="MEDIO") return "#f1c40f";
  if($level=="LEVE") return "#17a2b8";
  return "#2ecc71";
}

function gr_time_ago($datetime){
  if(!$datetime) return "Nunca";
  $diff = time() - strtotime($datetime);
  if($diff < 60) return $diff." seg";
  if($diff < 3600) return floor($diff/60)." min";
  if($diff < 86400) return floor($diff/3600)." hora(s)";
  return floor($diff/86400)." día(s)";
}

$alerts = [];
$activity = [];
$cnt_alto = 0;
$cnt_medio = 0;
$cnt_leve = 0;
$cnt_ok = 0;

// =======================================================
// ÚLTIMA POSICIÓN POR VEHÍCULO
// =======================================================
$sql_last = "
SELECT
  c.id AS car_id,
  c.name,
  c.plate,
  c.token,
  c.invoice_file,
  g.id AS gps_id,
  g.imei,
  p.latitude,
  p.longitude,
  p.speed,
  p.created_at AS last_signal
FROM cars c
INNER JOIN gps_devices g ON g.id = c.gps_id
LEFT JOIN gps_positions p ON p.id = (
  SELECT p2.id
  FROM gps_positions p2
  WHERE p2.gps_id = g.id
  ORDER BY p2.created_at DESC
  LIMIT 1
)
WHERE c.stock_id=$stock_id
  AND c.gps_id IS NOT NULL
  AND c.gps_id > 0
ORDER BY p.created_at ASC
";

$q_last = $con->query($sql_last);

if($q_last){
  while($r = $q_last->fetch_assoc()){

    $carLabel = trim($r["name"]." ".(!empty($r["plate"]) ? "(".$r["plate"].")" : ""));
    $lastSignal = $r["last_signal"];
    $mins = 999999;

    if($lastSignal){
      $mins = floor((time() - strtotime($lastSignal)) / 60);
    }

    // GPS sin señal
    if($mins >= 30){
      $level = ($mins >= 180 ? "ALTO" : ($mins >= 60 ? "MEDIO" : "LEVE"));
      $alerts[] = [
        "level" => $level,
        "icon"  => "📡",
        "title" => "GPS sin señal reciente",
        "car"   => $carLabel,
        "desc"  => "El GPS tiene <b>".gr_time_ago($lastSignal)."</b> sin reportar ubicación.",
        "meta"  => "IMEI: <b>".htmlspecialchars($r["imei"])."</b> · Última señal: <b>".($lastSignal ? date("d-m-Y h:i a", strtotime($lastSignal)) : "Nunca")."</b>",
        "action"=> "Revisar dispositivo, cobertura, batería o posible desconexión.",
        "link"  => "./?view=gps&opt=map"
      ];
    }else{
      $cnt_ok++;
    }

    // Exceso de velocidad actual
    $speed = floatval($r["speed"]);
    if($speed >= 120){
      $level = ($speed >= 140 ? "ALTO" : "MEDIO");
      $alerts[] = [
        "level" => $level,
        "icon"  => "🚨",
        "title" => "Exceso de velocidad detectado",
        "car"   => $carLabel,
        "desc"  => "Velocidad actual o última registrada: <b>".number_format($speed,0)." km/h</b>.",
        "meta"  => "Última señal: <b>".($lastSignal ? date("d-m-Y h:i a", strtotime($lastSignal)) : "Nunca")."</b>",
        "action"=> "Contactar al conductor o revisar ruta del vehículo.",
        "link"  => "./?view=gps&opt=map"
      ];
    }
  }
}

// =======================================================
// EXCESOS DE VELOCIDAD EN ÚLTIMAS 24 HORAS
// =======================================================
$sql_speed = "
SELECT
  c.id AS car_id,
  c.name,
  c.plate,
  c.token,
  MAX(p.speed) max_speed,
  COUNT(*) total_events,
  MAX(p.created_at) last_event
FROM cars c
INNER JOIN gps_devices g ON g.id=c.gps_id
INNER JOIN gps_positions p ON p.gps_id=g.id
WHERE c.stock_id=$stock_id
  AND p.created_at >= '$last24'
  AND p.speed >= 120
GROUP BY c.id
ORDER BY max_speed DESC
LIMIT 8
";
$q_speed = $con->query($sql_speed);
if($q_speed){
  while($r = $q_speed->fetch_assoc()){
    $speed = floatval($r["max_speed"]);
    $level = ($speed >= 140 ? "ALTO" : "MEDIO");
    $carLabel = trim($r["name"]." ".(!empty($r["plate"]) ? "(".$r["plate"].")" : ""));

    $alerts[] = [
      "level" => $level,
      "icon"  => "🏎️",
      "title" => "Patrón de velocidad alta",
      "car"   => $carLabel,
      "desc"  => "Se detectaron <b>".intval($r["total_events"])." evento(s)</b> de velocidad alta en las últimas 24 horas.",
      "meta"  => "Máxima: <b>".number_format($speed,0)." km/h</b> · Último evento: <b>".date("d-m-Y h:i a", strtotime($r["last_event"]))."</b>",
      "action"=> "Revisar historial GPS y notificar al conductor.",
      "link"  => "./?view=gps&opt=map"
    ];
  }
}

// =======================================================
// MOVIMIENTO SOSPECHOSO MADRUGADA
// =======================================================
$sql_night = "
SELECT
  c.id AS car_id,
  c.name,
  c.plate,
  c.token,
  COUNT(*) total_events,
  MAX(p.created_at) last_event,
  MAX(p.speed) max_speed
FROM cars c
INNER JOIN gps_devices g ON g.id=c.gps_id
INNER JOIN gps_positions p ON p.gps_id=g.id
WHERE c.stock_id=$stock_id
  AND p.created_at >= '$last24'
  AND HOUR(p.created_at) BETWEEN 0 AND 5
  AND p.speed > 5
GROUP BY c.id
ORDER BY total_events DESC
LIMIT 8
";
$q_night = $con->query($sql_night);
if($q_night){
  while($r = $q_night->fetch_assoc()){
    $level = (intval($r["total_events"]) >= 10 ? "ALTO" : "MEDIO");
    $carLabel = trim($r["name"]." ".(!empty($r["plate"]) ? "(".$r["plate"].")" : ""));

    $alerts[] = [
      "level" => $level,
      "icon"  => "🌙",
      "title" => "Movimiento en madrugada",
      "car"   => $carLabel,
      "desc"  => "Se detectó movimiento entre 12:00 AM y 5:59 AM.",
      "meta"  => "Eventos: <b>".intval($r["total_events"])."</b> · Máx. velocidad: <b>".number_format($r["max_speed"],0)." km/h</b> · Último: <b>".date("d-m-Y h:i a", strtotime($r["last_event"]))."</b>",
      "action"=> "Confirmar si el vehículo estaba autorizado a moverse en ese horario.",
      "link"  => "./?view=gps&opt=map"
    ];
  }
}

// =======================================================
// DETENIDO DEMASIADO TIEMPO CON SEÑAL RECIENTE
// =======================================================
$sql_stopped = "
SELECT
  c.id AS car_id,
  c.name,
  c.plate,
  c.token,
  p.latitude,
  p.longitude,
  p.speed,
  p.created_at last_signal
FROM cars c
INNER JOIN gps_devices g ON g.id=c.gps_id
INNER JOIN gps_positions p ON p.id = (
  SELECT p2.id
  FROM gps_positions p2
  WHERE p2.gps_id=g.id
  ORDER BY p2.created_at DESC
  LIMIT 1
)
WHERE c.stock_id=$stock_id
  AND p.speed <= 2
  AND p.created_at >= DATE_SUB(NOW(), INTERVAL 2 HOUR)
LIMIT 10
";
$q_stopped = $con->query($sql_stopped);
if($q_stopped){
  while($r = $q_stopped->fetch_assoc()){
    $carLabel = trim($r["name"]." ".(!empty($r["plate"]) ? "(".$r["plate"].")" : ""));

    $alerts[] = [
      "level" => "LEVE",
      "icon"  => "🅿️",
      "title" => "Vehículo detenido",
      "car"   => $carLabel,
      "desc"  => "El vehículo aparece detenido con señal reciente.",
      "meta"  => "Velocidad: <b>".number_format($r["speed"],0)." km/h</b> · Última señal: <b>".date("d-m-Y h:i a", strtotime($r["last_signal"]))."</b>",
      "action"=> "Monitorear si permanece detenido por más tiempo.",
      "link"  => "./?view=gps&opt=map"
    ];
  }
}

// =======================================================
// RANKING ACTIVIDAD 7 DÍAS
// =======================================================
$sql_activity = "
SELECT
  c.id AS car_id,
  c.name,
  c.plate,
  c.token,
  COUNT(p.id) total_points,
  AVG(p.speed) avg_speed,
  MAX(p.speed) max_speed,
  MAX(p.created_at) last_signal
FROM cars c
INNER JOIN gps_devices g ON g.id=c.gps_id
LEFT JOIN gps_positions p ON p.gps_id=g.id AND p.created_at >= '$last7'
WHERE c.stock_id=$stock_id
GROUP BY c.id
ORDER BY total_points DESC
LIMIT 10
";
$q_activity = $con->query($sql_activity);
if($q_activity){
  while($r = $q_activity->fetch_assoc()){
    $activity[] = $r;
  }
}

foreach($alerts as $a){
  if($a["level"]=="ALTO") $cnt_alto++;
  elseif($a["level"]=="MEDIO") $cnt_medio++;
  elseif($a["level"]=="LEVE") $cnt_leve++;
}

$statusText = "GPS estable";
$statusColor = "#2ecc71";
if($cnt_alto > 0){
  $statusText = "Riesgo GPS alto";
  $statusColor = "#e74c3c";
}elseif($cnt_medio > 0){
  $statusText = "Revisión GPS recomendada";
  $statusColor = "#f1c40f";
}
?>

<style>
html,body{ overflow-x:hidden!important; }
.content-wrapper,.content,.container-fluid{ overflow-x:hidden!important; }

.gr-main{
  background:#16181d;
  border-radius:22px;
  border:1px solid rgba(255,255,255,.08);
  box-shadow:0 10px 28px rgba(0,0,0,.35);
  overflow:hidden;
  margin-bottom:20px;
}
.gr-header{
  padding:18px;
  border-bottom:1px solid rgba(255,255,255,.08);
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  flex-wrap:wrap;
}
.gr-header h3{
  color:#fff;
  font-weight:900;
  margin:0;
}
.gr-header span{
  color:#9aa0a6;
  font-weight:800;
}
.gr-status{
  padding:8px 13px;
  border-radius:999px;
  background:<?php echo $statusColor; ?>;
  color:#111;
  font-weight:900;
}
.gr-kpis{
  display:grid;
  grid-template-columns:repeat(4,minmax(0,1fr));
  gap:12px;
  padding:18px;
  padding-bottom:0;
}
.gr-kpi{
  background:#0f1115;
  border:1px solid rgba(255,255,255,.08);
  border-radius:16px;
  padding:14px;
  min-width:0;
}
.gr-kpi .label{
  color:#bdbdbd;
  font-size:12px;
  font-weight:900;
  text-transform:uppercase;
}
.gr-kpi .value{
  color:#fff;
  font-size:25px;
  font-weight:900;
  margin-top:4px;
}
.gr-body{
  padding:18px;
}
.gr-alert{
  background:#0f1115;
  border:1px solid rgba(255,255,255,.08);
  border-radius:18px;
  padding:16px;
  margin-bottom:14px;
  max-width:100%;
  overflow:hidden;
  word-break:break-word;
}
.gr-alert-top{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:12px;
  flex-wrap:wrap;
}
.gr-alert-left{
  display:flex;
  gap:12px;
  align-items:flex-start;
  min-width:0;
}
.gr-icon{
  width:52px;
  height:52px;
  border-radius:16px;
  display:flex;
  align-items:center;
  justify-content:center;
  background:#1f2937;
  font-size:24px;
  flex-shrink:0;
}
.gr-title{
  color:#fff;
  font-weight:900;
  margin:0;
  font-size:18px;
}
.gr-car{
  color:#9aa0a6;
  font-weight:900;
  font-size:13px;
  margin-top:3px;
}
.gr-desc{
  color:#d1d5db;
  font-weight:800;
  margin:10px 0 0 0;
}
.gr-meta{
  color:#9aa0a6;
  font-weight:800;
  font-size:13px;
  margin-top:8px;
}
.gr-action{
  background:rgba(46,204,113,.08);
  border:1px solid rgba(46,204,113,.15);
  padding:10px 12px;
  border-radius:12px;
  color:#2ecc71;
  font-weight:900;
  margin-top:12px;
}
.gr-box{
  background:#0f1115;
  border:1px solid rgba(255,255,255,.08);
  border-radius:18px;
  padding:16px;
  margin-top:14px;
}
.gr-box h5{
  color:#fff;
  font-weight:900;
  margin:0 0 12px 0;
}
.gr-table{
  color:#eaeaea;
  margin:0;
  width:100%;
}
.gr-table th{
  color:#bdbdbd;
  border-color:rgba(255,255,255,.08)!important;
  font-size:12px;
  text-transform:uppercase;
}
.gr-table td{
  border-color:rgba(255,255,255,.06)!important;
  vertical-align:middle!important;
  font-weight:800;
}
.table-responsive{ max-width:100%; overflow-x:auto; }
@media(max-width:991px){
  .gr-kpis{ grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media(max-width:575px){
  .gr-kpis{ grid-template-columns:1fr; }
  .gr-alert-left{ flex-direction:column; }
}
</style>

<section class="content">
<div class="container-fluid">
<br>

<div class="gr-main">

  <div class="gr-header">
    <div>
      <h3>🛰️ Centro de Riesgo GPS</h3>
      <span>Monitoreo inteligente de señal, velocidad, madrugada y actividad</span>
    </div>
    <div class="gr-status"><?php echo $statusText; ?></div>
  </div>

  <div class="gr-kpis">
    <div class="gr-kpi">
      <div class="label">Críticas</div>
      <div class="value" style="color:#e74c3c;"><?php echo $cnt_alto; ?></div>
    </div>
    <div class="gr-kpi">
      <div class="label">Medias</div>
      <div class="value" style="color:#f1c40f;"><?php echo $cnt_medio; ?></div>
    </div>
    <div class="gr-kpi">
      <div class="label">Leves</div>
      <div class="value" style="color:#17a2b8;"><?php echo $cnt_leve; ?></div>
    </div>
    <div class="gr-kpi">
      <div class="label">Estables</div>
      <div class="value" style="color:#2ecc71;"><?php echo $cnt_ok; ?></div>
    </div>
  </div>

  <div class="gr-body">

    <?php if(count($alerts)==0): ?>
      <div class="gr-alert" style="border-left:5px solid #2ecc71;">
        <div class="gr-alert-top">
          <div class="gr-alert-left">
            <div class="gr-icon" style="color:#2ecc71;">✅</div>
            <div>
              <p class="gr-title">GPS estable</p>
              <div class="gr-car">No hay riesgos importantes detectados.</div>
              <p class="gr-desc">La flota con GPS está reportando sin alertas críticas.</p>
              <div class="gr-action">Mantener monitoreo automático.</div>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <?php foreach($alerts as $a): ?>
        <div class="gr-alert" style="border-left:5px solid <?php echo gr_color($a["level"]); ?>;">
          <div class="gr-alert-top">
            <div class="gr-alert-left">
              <div class="gr-icon" style="color:<?php echo gr_color($a["level"]); ?>;">
                <?php echo $a["icon"]; ?>
              </div>
              <div>
                <p class="gr-title"><?php echo $a["title"]; ?></p>
                <div class="gr-car"><?php echo htmlspecialchars($a["car"]); ?></div>
                <p class="gr-desc"><?php echo $a["desc"]; ?></p>
                <div class="gr-meta"><?php echo $a["meta"]; ?></div>
                <div class="gr-action">⚡ <?php echo $a["action"]; ?></div>
                <?php if(!empty($a["link"])): ?>
                  <div style="margin-top:10px;">
                    <a href="<?php echo $a["link"]; ?>" class="btn btn-sm btn-warning" style="font-weight:900;">
                      Abrir GPS <i class="fa fa-arrow-right"></i>
                    </a>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <span class="<?php echo gr_badge($a["level"]); ?>" style="font-weight:900;">
              <?php echo $a["level"]; ?>
            </span>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <div class="gr-box">
      <h5>📊 Ranking de Actividad GPS — últimos 7 días</h5>

      <div class="table-responsive">
        <table class="table table-sm gr-table">
          <thead>
            <tr>
              <th>Vehículo</th>
              <th>Ficha</th>
              <th>Puntos GPS</th>
              <th>Vel. Prom.</th>
              <th>Vel. Máx.</th>
              <th>Última señal</th>
            </tr>
          </thead>
          <tbody>
            <?php if(count($activity)>0): ?>
              <?php foreach($activity as $r): ?>
                <tr>
                  <td><?php echo htmlspecialchars($r["name"]." ".(!empty($r["plate"]) ? "(".$r["plate"].")" : "")); ?></td>
                  <td><?php echo htmlspecialchars($r["token"]); ?></td>
                  <td><?php echo intval($r["total_points"]); ?></td>
                  <td><?php echo number_format(floatval($r["avg_speed"]),0); ?> km/h</td>
                  <td><?php echo number_format(floatval($r["max_speed"]),0); ?> km/h</td>
                  <td><?php echo $r["last_signal"] ? date("d-m-Y h:i a", strtotime($r["last_signal"])) : "Sin señal"; ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="color:#9aa0a6;">No hay actividad GPS registrada.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

</div>
</section>




<?php endif;?>
