<?php 
$operations = PUData::getAll();
$users = UserData::getById($_GET["id"]);

?>
<section class="content">
   <div class="row">
  <div class="col-md-12">
  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><a href="./?view=users&opt=all" style="color: white;"><i class='fa fa-arrow-left'></i></a>&nbsp;<i class='fa fa-user'></i> Permisos del Usuario <small><?php echo $users->name." ".$users->lastname;?></small></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cog'></i> Administracion</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Usuarios</a></li>

            </ol>
          </div><!-- /.col -->
     
    </div>


<?php if(isset($_GET["id"]) && $_GET["id"]!=""):?>


<div class="card" style="background-color: #222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
  <thead>
    <th>Permisos</th>
    <th></th>

  </thead>
<?php
  foreach($operations as $operation): ?>
<tr>
  <td><?php echo $operation->ubicacion ;?></td>


     <td width="15%">
<?php $operations = UserPermissionsData::getGroupByOp($_GET["id"],$operation->id);  $sl = $operations[0]->c!=null?$operations[0]->c:0;   
if($sl>0):?>
<a class="btn btn-block btn-warning" href="./?action=delete&opt=permissions&user_id=<?php echo $_GET["id"];?>&id=<?php echo $operations[0]->id; ?>">Desactivar</a>
<?php endif;  if($sl==0):?>
<a class="btn btn-block btn-danger" href="./?action=add&opt=userpermissions&user_id=<?php echo $_GET["id"];?>&id=<?php echo $operation->id; ?>">Activar</a>
<?php endif; ?>        
        </td>

  </td>
</tr>
<?php endforeach;?>
</table>
</div>
</div>
</div>

</div>
</div>
</div>
</div>
</section>

<?php endif; ?>
<script type="text/javascript">
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6 col-6:eq(0)');
</script>
