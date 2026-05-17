<section class="content">
<div class="row">
  <div class="col-md-12">
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-history'></i> Registro de Actividad</h1>
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
<?php $users = ACData::getAllBySQL("where user_id!=6 and stock_id=".StockData::getPrincipal()->id);
    if(count($users)>0){?>
      
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
      <thead>
      <th>ID Usuario</th>
      <th>Nombre</th>
      <th>Accion</th>
      <th>Fecha</th>
      </thead>
      <?php foreach($users as $user){?>

<?php if($user->user_id!=""){
$client = $user->getUser();
}?>
       <tr>
        <td><?php echo $user->user_id;?></td>
        <td><?php echo $client->name." ".$client->lastname;?></td>
        <td><?php echo $user->accion;?></td>
        <td><?php echo date("d-m-Y h:i:s a", strtotime($user->created_at)); ?></td>


        </tr>
        <?php

      }?>
      </table>
      </div>
      </div>
    </div>
      <?php }else{?>
      
         <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Actividad</h2>
    <p>No se ha realizado ninguna operacion.</p>
    </div>
</div>
  
    <?php } ?>


  </div>
</div>
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
<script type="text/javascript">
     $("#example2").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example2_wrapper .col-md-6 col-6:eq(0)');
</script>
</section>

