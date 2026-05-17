
<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-key'></i> Control Llaves</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cubes'></i> Extras</li>
              <li class="breadcrumb-item active"><i class='far fa-circle'></i> kay</li>
               
            </ol>
          </div><!-- /.col -->
        </div>
          <div class="card" style="background-color:#222;">
<div class="card-body">
<?php if(isset($_GET['id'])): $user = KeyData::getById($_GET["id"]);?>
    <form class="form-horizontal" method="post" id="upd" role="form">
 <div class="row">
     
      <div class="col-md-6 col-6">
     <select style="background-color:#333;" name="car_id" class="form-control select2">
    <?php foreach(CarsData::getAll() as $client):?>
     <option value="<?php echo $client->id;?>"><?php echo $client->getBrand()->name." ".$client->name." ".$client->year." ".$client->getExColor()->name." - ".$client->chassis;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
      <div class="col-md-3 col-3">
     <select style="background-color:#333;" name="user_id" class="form-control select2">
    <?php foreach(UserData::getAll() as $users):?>
     <option value="<?php echo $users->id;?>"><?php echo $users->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
     <div class="col-md-3 col-3">
     <select style="background-color:#333;" name="type_id" class="form-control">
     <option value="Entregado">Entregado</option>
     <option value="Recibido">Recibido</option>
      </select>
    </div>

    <div class="col-md-12 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 my-3 control-label"></label>
                  <input type="hidden" name="employes_id" value="<?php echo $user->id;?>">
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
                  url: "./?action=key&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Llave Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.place = './?view=key&opt=all'  }, delay); 
                     
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

    <div class="col-md-6 col-6">
     <select style="background-color:#333;" name="car_id" class="form-control select2">
    <?php foreach(CarsData::getAll() as $client):?>
     <option value="<?php echo $client->id;?>"><?php echo $client->getBrand()->name." ".$client->name." ".$client->year." ".$client->getExColor()->name." - ".$client->chassis;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
      <div class="col-md-3 col-3">
     <select style="background-color:#333;" name="user_id" class="form-control select2">
    <?php foreach(UserData::getAll() as $users):?>
     <option value="<?php echo $users->id;?>"><?php echo $users->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
     <div class="col-md-3 col-3">
     <select style="background-color:#333;" name="type_id" class="form-control">
     <option value="Entregado">Entregado</option>
     <option value="Recibido">Recibido</option>
      </select>
    </div>

    <div class="col-md-12 col-12">
      <label class="col-md-12 col-12 control-label"></label>
      <button class="btn btn-warning btn-block btn-sm" id="btn-add-place"><i class="fa fa-check"></i> Finalizar</button>
    </div>
    
  </div>
</form>

<script>
function guardarkayOffline(kay) {
  let pendientes = JSON.parse(localStorage.getItem("kayes_pendientes")) || [];
  pendientes.push(kay);
  localStorage.setItem("kayes_pendientes", JSON.stringify(pendientes));
  toastr.info('Guardado localmente. Se enviará cuando vuelva el internet.');
}

function sincronizarkayes() {
  let pendientes = JSON.parse(localStorage.getItem("kayes_pendientes")) || [];
  if (pendientes.length > 0 && navigator.onLine) {
    pendientes.forEach((kay, i) => {
     fetch("./?action=key&opt=add_offline",  {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(kay)
      })
      .then(res => res.text())
      .then(resp => {
        if (resp === "OK") {
          pendientes.splice(i, 1);
          localStorage.setItem("kayes_pendientes", JSON.stringify(pendientes));
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
  const user_id = this.user_id.value;
  const type_id = this.type_id.value;

  if (!car_id || !user_id || !type_id) {
    toastr.error("Todos los campos son obligatorios");
    return;
  }

  // Construir objeto para guardar offline
  const kay = { car_id: car_id, user_id: user_id, type_id: type_id };

  if (navigator.onLine) {
    const formData = new FormData(this);
    fetch("./?action=key&opt=add", {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(resp => {
      if (resp.trim() === "OK" || resp.trim() === "UPDATED") {
        toastr.success('Registro procesado correctamente.');
        location.href = "./?view=key&opt=all";
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
  if (navigator.onLine) sincronizarkayes();
}, 5000);
</script>

<?php endif;?>
</div>
</div>


<?php 
$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT * from kay";
$query = $con->query($sql);
    if(count($query)>0):?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      <th>Vehiculo</th>
      <th>Empleado</th>
      <th>Estatus</th>
    </thead>

    <tfoot>
      <tr>
      <th>Vehiculo</th>
      <th>Empleado</th>
      <th>Estatus</th>
      </tr>
    </tfoot>

       <?php while($r = $query->fetch_array()): 
       $client = CarsData::getById($r["car_id"]);
       $users = UserData::getById($r["user_id"]);
       ?>
        <tr>
                 
        <td><?php echo strtoupper($client->getBrand()->name." ".$client->name." ".$client->year." ".$client->getExColor()->name." - ".$client->chassis); ?></td>
        <td><?php echo strtoupper($users->name); ?></td>
        <td><?php echo strtoupper($r['type_id']); ?></td>
      
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