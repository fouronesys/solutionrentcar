
<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-car-crash'></i> Choques o Multas</h1>
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

<form class="form-horizontal" method="post" id="add" role="form">
  <div class="row">

    <div class="col-md-5 col-5">
     <select style="background-color:#333;" name="car_id" class="form-control select2">
    <?php foreach(CarsData::getAll() as $client):?>
     <option value="<?php echo $client->id;?>"><?php echo $client->getBrand()->name." ".$client->name." ".$client->year." ".$client->getExColor()->name." - ".$client->chassis;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
      <div class="col-md-3 col-3">
     <select style="background-color:#333;" name="person_id" class="form-control select2">
    <?php foreach(PersonData::getAll() as $users):?>
     <option value="<?php echo $users->id;?>"><?php echo $users->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
     <div class="col-md-2 col-2">
     <select style="background-color:#333;" name="type_id" class="form-control">
     <option value="Multa">Multa</option>
     <option value="Choque">Choque</option>
      </select>
    </div>
    
      <div class="col-md-2 col-2">
      <input  style="background-color:#333;" type="text" name="price" autocomplete="off"  class="form-control" placeholder="Precio" required>
    </div>


    <div class="col-md-12 col-12">
      <label class="col-md-12 col-12 control-label"></label>
      <button class="btn btn-warning btn-block btn-sm" id="btn-add-place"><i class="fa fa-check"></i> Finalizar</button>
    </div>
    
  </div>
</form>

<script>
function guardarkayOffline(kay) {
  let pendientes = JSON.parse(localStorage.getItem("crashes_pendientes")) || [];
  pendientes.push(kay);
  localStorage.setItem("crashes_pendientes", JSON.stringify(pendientes));
  toastr.info('Guardado localmente. Se enviará cuando vuelva el internet.');
}

function sincronizarcrashes() {
  let pendientes = JSON.parse(localStorage.getItem("crashes_pendientes")) || [];
  if (pendientes.length > 0 && navigator.onLine) {
    pendientes.forEach((kay, i) => {
     fetch("./?action=crashes&opt=add_offline",  {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(kay)
      })
      .then(res => res.text())
      .then(resp => {
        if (resp === "OK") {
          pendientes.splice(i, 1);
          localStorage.setItem("crashes_pendientes", JSON.stringify(pendientes));
        } else {
        alert("No se puede duplicar. Ya existe.");
      }
      });
    });
  }
}

document.getElementById("add").addEventListener("submit", function(e) {
  e.preventDefault();

  const car_id  = this.car_id.value;
  const person_id = this.person_id.value;
  const type_id = this.type_id.value;

  if (!car_id || !person_id || !type_id) {
    toastr.error("Todos los campos son obligatorios");
    return;
  }

  // Construir objeto para guardar offline
  const kay = { car_id: car_id, person_id: person_id, type_id: type_id };

  if (navigator.onLine) {
    const formData = new FormData(this);
    fetch("./?action=crashes&opt=add", {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(resp => {
      if (resp.trim() === "OK" || resp.trim() === "UPDATED") {
        toastr.success('Registro procesado correctamente.');
        location.href = "./?view=crashes&opt=all";
      } else {
        toastr.warning('Ya existe ese registro. Guardado offline.');
        guardarkayOffline(kay);
      }
    })
    .catch(() => guardarkayOffline(kay));
  } else {
    guardarkayOffline(kay);
  }
});


setInterval(() => {
  if (navigator.onLine) sincronizarcrashes();
}, 5000);
</script>
</div>
</div>


<?php 
$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT * from crashes";
$query = $con->query($sql);
    if(count($query)>0):?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      <th>Vehiculo</th>
      <th>Empleado</th>
      <th>Causa</th>
      <th>Precio</th>
    </thead>

    <tfoot>
      <tr>
      <th>Vehiculo</th>
      <th>Empleado</th>
      <th>Causa</th>
      <th>Precio</th>
      </tr>
    </tfoot>

       <?php while($r = $query->fetch_array()): 
       $client = CarsData::getById($r["car_id"]);
       $users = PersonData::getById($r["person_id"]);
       ?>
        <tr>
                 
        <td><?php echo strtoupper($client->getBrand()->name." ".$client->name." ".$client->year." ".$client->getExColor()->name." - ".$client->chassis); ?></td>
        <td><?php echo strtoupper($users->name); ?></td>
        <td><?php echo strtoupper($r['type_id']); ?></td>
        <td><?php echo number_format($r['price'],0,".",","); ?></td>
      
    </tr>
    
    <?php endwhile; ?>
      </table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->
      <?php else:?>
     
         <div class="card">
              <div class="card-header">
    <h2>No hay kay</h2>
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
<?php endif; ?>