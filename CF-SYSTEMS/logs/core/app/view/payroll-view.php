
<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-file-excel'></i> Listado de Nomina</h1>
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
<?php if($_GET['id']>0): $user = PayrollData::getById($_GET["id"]);?>
    <form class="form-horizontal" method="post" id="upd" role="form">
 <div class="row">
     <div class="col-md-4 col-6">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Empleado</label>
    <select style="background-color:#333;" name="idemployee"  id="idemployee" class="form-control select2">
      <option disabled selected>--- ELEGIR ---</option>
        <?php foreach(UserData::getAll() as $employee):?>
       <option value="<?php echo $employee->id;?>"<?php if($employee->id==$user->idemployee){ echo "selected"; }?>><?php echo $employee->name." ".$employee->lastname;?></option>
    <?php endforeach;?>
    </select>
    </div>
    
      <div class="col-md-4 col-6">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Monto</label>
      <select style="background-color:#333;" name="amount" id="amount" class="form-control"></select>
    </div>
    
    <div class="col-md-4 col-6">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha</label>
      <input style="background-color:#333;" type="date" name="pay_day" autocomplete="off" value="<?php echo utf8_decode($user->pay_day);?>"  class="form-control">
    </div>

    
               
                <div class="col-md-12 col-6">

                  <input type="hidden" name="user_id" value="<?php echo $user->id;?>">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
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
                  url: "./?action=payroll&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Nomina Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=payroll&opt=all'  }, delay); 
                     
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
      <div class="col-md-4 col-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Empleado</label>
 <select style="background-color:#333;" name="idemployee" id="employee_id" required class="form-control select2">
      <option disabled selected value="0">--- ELEGIR ---</option>
        <?php foreach(UserData::getAll() as $employee):?>
      <option value="<?php echo $employee->id;?>"><?php echo strtoupper($employee->name." ".$employee->lastname);?></option>
    <?php endforeach;?>
    </select>
    </div>
    
      <div class="col-md-4 col-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Monto</label>
       <select style="background-color:#333;" name="amount" id="price" class="form-control"></select>
    </div>
    
    <div class="col-md-4 col-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha</label>
      <input style="background-color:#333;" type="date" name="pay_day" required autocomplete="off"  class="form-control">
    </div>
               
                <div class="col-md-12 col-12">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
</form>


<script type="text/javascript">

   $('#employee_id').change(function(){
      recargarLista();
    });
    
  
  function recargarLista(){
    $.ajax({
      type:"POST",
      url:"./?action=get&opt=employee",
      data:"employee_id=" + $('#employee_id').val(),
      success:function(r){
        $('#price').html(r);
      }
    });
  }

            jQuery(document).ready(function(){
            jQuery("#add").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=payroll&opt=add",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Nomina Exito!", { sticky: true });
                  $.jGrowl("Se Agrego la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = "./?view=payroll&opt=modal&id=" + $('#employee_id').val();  }, delay); 
                     
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

<?php $users = PayrollData::getAll();
    if(count($users)>0):?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      <th>Accion</th>
      <th>Empleado</th>
      <th>Monto</th>
      <th>Fecha</th>
      <th>Accion</th>
    </thead>

    <tfoot>
      <tr>
       <th>Accion</th>
      <th>Empleado</th>
      <th>Monto</th>
      <th>Fecha</th>
      <th>Accion</th>
      </tr>
    </tfoot>

      <?php foreach($users as $user): ?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=payroll&opt=all&id=<?php echo $user->id;?>" class="btn btn-success"><i class="fas fa-edit"></i></a>
                      </div>
        </td>

        <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
        <td><?php echo number_format($user->amount,2,".",","); ?></td>
        <td><?php echo date("d-m-Y",strtotime($user->pay_day)); ?></td>
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=payroll&opt=del&id=<?php echo $user->id;?>" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> Eliminar</i></a>
                    
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
    <h2>No hay Pagos</h2>
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
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="modal"):
////////////////////////////////////////////////////////////////////////// CLIENT_MODAL ///////////////////////////
?>

<section class="content">
<div class="row">
  <div class="col-12">
  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-book"></i> Pago de Nomina</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-briefcase'></i> Finanzas</li>
              <li class="breadcrumb-item active"><i class='far fa-circle'></i> Nomina Empleado</li>
            </ol>
          </div><!-- /.col -->
    </div>
<?php
$user = UserData::getById($_GET["id"]);
$TicketMm = StockData::getPrincipal()->ticket_mm;

print "<br><p class='alert alert-success'>Pago procesada exitosamente. <a  href='".$TicketMm."/ticket-payroll.php?id=".$user->id."' id='printx' class='btn-xs btn btn-info'><i class='fa fa-ticket'></i> Ver Ticket</a> </p>";

echo '<div class="row"><div class="col-12 col-offset-3">
<div class="embed-responsive embed-responsive-16by9">
  <iframe id="ticket1" name="ticket1" class="embed-responsive-item" src='.$TicketMm.'/ticket-payroll.php?id="'.$user->id.'" allowfullscreen></iframe>
</div>
</div></div>
';
?>
  </div>
</div>

</section>
<?php endif; ?>