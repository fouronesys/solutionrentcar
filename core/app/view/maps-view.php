<section class="content">
<div class="row">
  <div class="col-md-12">
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-map'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Mapa del Cliente"; break;
 case 'EN': echo "Customer Map"; break;
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


  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 550px;
            width: 100%;
        }
    </style>

<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // Inicializar el mapa
    var map = L.map('map').setView([18.47186, -69.89232], 7); // Coordenadas centradas en España

    // Capa base del mapa
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
    }).addTo(map);

    // Datos de las provincias (puedes agregar más provincias)
    var provincias = [
        <?php foreach(PersonData::getAllBySQL("where latitud>0 and stock_id=".StockData::getPrincipal()->id) as $cli):?>
        { nombre: "<?php echo $cli->name;?>", lat: <?php echo $cli->latitud;?>, lng: <?php echo $cli->longitud;?>},
        <?php endforeach;?>
        // Agrega más provincias aquí
    ];

    // Agregar marcadores para cada provincia
    provincias.forEach(function(provincia) {
        L.marker([provincia.lat, provincia.lng]).addTo(map)
            .bindPopup(provincia.nombre);
    });
</script>



  </div>
</div>
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->

</section>

