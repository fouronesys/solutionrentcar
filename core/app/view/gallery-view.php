
<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
              <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-square'></i> <?php  switch (Core::$user->language){  case 'ES': echo "Galeria"; break;  case 'EN': echo "Gallery"; break; } ?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Inicio"; break;
 case 'EN': echo "Home"; break;
}
?></li>
              <li class="breadcrumb-item active"><i class='fa fa-car'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculos"; break;
 case 'EN': echo "Vehicles"; break;
}
?></li>
           
            </ol>
          </div><!-- /.col -->
        </div>
          <div class="card"  style="background-color:#222;">
<div class="card-body">

<form class="form-horizontal" action="./?action=gallery&opt=add" method="post" role="form" enctype="multipart/form-data">
 <div class="row">
      <div class="col-md-8 col-8">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Foto"; break;
 case 'EN': echo "Photo"; break;
}
?></label>
      <input  style="background-color:#222;" type="file" name="invoice_file">
      <input  style="background-color:#222;" type="hidden" name="car_id" value="<?php echo $_GET["id"];?>">
    </div>
               
                <div class="col-md-4 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 my-3 control-label"></label>
                   <button class="btn btn-warning btn-block btn-sm" id="miBoton"><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Foto a Subir"; break;
 case 'EN': echo "Photo to Upload"; break;
}
?></button>
                 
                </div>
              </div>
</form>



</div>
</div>

<?php 
$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT * from galery where car_id=".$_GET["id"];
$query = $con->query($sql);
    if(count($query)>0):?>
<div class="card"  style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
   <thead>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Nombre"; break;
 case 'EN': echo "Name"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
    </thead>

    <tfoot>
      <tr>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Nombre"; break;
 case 'EN': echo "Name"; break;
}
?></th>
      <th><?php 
switch (Core::$user->language){
 case 'ES': echo "Accion"; break;
 case 'EN': echo "Action"; break;
}
?></th>
      </tr>
    </tfoot>

      <?php $contador = 0;  while($r = $query->fetch_array()): ?>
        <tr>
                 
        <td><?php echo $r['invoice_file']; ?></td>
        <td class="text-right py-0 align-middle">
            
<?php  
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()){
if ($x['permits_id']==4): ?>
   
                        <a href="./?action=gallery&opt=del&id=<?php echo $r['id'];?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();" ><i class="fas fa-trash"> <?php 
switch (Core::$user->language){
 case 'ES': echo "Eliminar"; break;
 case 'EN': echo "Delete"; break;
}
?></i></a>
                    
                    
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
    <?php endif;?>
<?php  }; ?>
  
</td>
    </tr>
    
    <?php $contador++; endwhile; ?>
    
    <script>
// Obtener el número de registros desde PHP
var totalRegistros = <?php echo $contador; ?>;

// Si hay 3 o más registros, desactivar el botón
if (totalRegistros >= 3) {
    document.getElementById("miBoton").disabled = true;
}
</script>
      </table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->
      <?php else:?>
     
        <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2><?php 
switch (Core::$user->language){
 case 'ES': echo "No hay fotos"; break;
 case 'EN': echo "There are no photos"; break;
}
?></h2>
    <p><?php 
switch (Core::$user->language){
 case 'ES': echo "No se ha realizado ninguna operacion"; break;
 case 'EN': echo "No operation has been performed"; break;
}
?></p>
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