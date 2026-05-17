
<section class="content">
<div class="row">
  <div class="col-md-12">
  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-history'></i> Registro de Sesión</h1>
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
<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):

$selstock = null;
if(isset($_GET["stock"])){ $selstock=$_GET["stock"]; }
else{ $selstock = StockData::getPrincipal()->id; }?>


<?php $users = SSData::getAllBySQL("where user_id>0 and stock_id=$selstock"); 
if(count($users)>0): ?>              
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
      <thead>
      <th>ID Usuario</th>
      <th>Nombre</th>
      <th>Entrada</th>
      <th>Salida</th>
      </thead>
      <?php foreach($users as $user):?>

<?php if($user->user_id!=""):$client = $user->getUser(); endif; ?>
       <tr>
        <td><?php echo $user->user_id;?></td>
        <td><?php echo $client->name." ".$client->lastname;?></td>
        <td><?php echo $user->created_in;?></td>
        <td><?php echo $user->created_out; ?></td>
        </tr>
        <?php endforeach; ?>
      </table>
      </div>
      </div>
    </div>
      <?php else:?>
      
         <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Registros</h2>
    <p>No se ha realizado ninguna operacion.</p>
    </div>
</div>
  
    <?php endif; ?>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="clients"):
$selstock = null;
if(isset($_GET["stock"])){ $selstock=$_GET["stock"]; }
else{ $selstock = StockData::getPrincipal()->id; }?>
     <div class="row my-2">
                <div class="col-md-6 col-6" >
                
                  <a href="./?view=session&opt=all" class="btn btn-danger btn-block btn-sm"><i class='fa fa-users'></i> Usuarios</a>
                </div>
                <div class="col-md-6 col-6">
                   <a href="./?view=session&opt=clients"  class="btn btn-danger btn-block btn-sm"><i class='fa fa-users'></i> Clientes</a>
                 
                </div>
              </div>

 <?php if (Core::$user->kind==1):?>
<div class="row my-2">
 <?php foreach(StockData::getAll() as $s):?>
                  <div class="col-md-3  col-6">
  <a href="./?view=session&opt=clients&stock=<?php echo $s->id; ?>" class="btn btn-success btn-block btn-sm"><i class='fa fa-street-view'></i> <?php echo $s->name; ?></a>
   </div>
<?php endforeach; ?>
</div>
<?php endif; $users = SSData::getAllBySQL("where client_id>0 and stock_id=$selstock"); 
if(count($users)>0): ?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
      <thead>
      <th>ID Usuario</th>
      <th>Nombre</th>
      <th>Entrada</th>
      <th>Salida</th>
      </thead>
      <?php foreach($users as $user):?>

<?php if($user->user_id!=""):$client = $user->getPerson(); endif; ?>
       <tr>
        <td><?php echo $user->client_id;?></td>
        <td><?php echo $client->name." ".$client->lastname;?></td>
        <td><?php echo $user->created_in;?></td>
        <td><?php echo $user->created_out; ?></td>
        </tr>
        <?php endforeach; ?>
      </table>
      </div>
      </div>
    </div>
      <?php else:?>
      
         <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Registros</h2>
    <p>No se ha realizado ninguna operacion.</p>
    </div>
</div>
  
    <?php endif; endif;?>

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
