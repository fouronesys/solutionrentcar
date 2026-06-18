<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($_POST['location']==1):
 $u = StockData::getAll();
  foreach ($u as $value)
    $html.="<option value='".$value->id."'>".strtoupper($value->name)."</option>";
  echo $html; 
endif;

elseif(isset($_GET["opt"]) && $_GET["opt"]=="employee"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(isset($_POST['employee_id'])):
$value = UserData::getById($_POST['employee_id']);
    $html="<option value='".$value->comision."'>".number_format($value->comision,0,".",",")."</option>";
  echo $html; 
endif; 

if($_POST['location']==2):
$u = StockData::getAllBySQL("where id!='".StockData::getPrincipal()->id."' and is_ext=2");
$label = "";
switch (Core::$user->language) {
  case 'ES': $label = '--- ELEGIR ---'; break;
  case 'EN': $label = '--- CHOOSE ---'; break;
}

$html = "<option value='".StockData::getPrincipal()->id."' disabled selected>$label</option>";

  foreach ($u as $value)
    $html.="<option value='".$value->id."'>".strtoupper($value->name)."</option>";
  echo $html; 
endif;


elseif(isset($_GET["opt"]) && $_GET["opt"]=="vehicule"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="exxmple">
<thead>
  <th>Accion</th>
      <th>Tipo</th>
      <th>Vehiculo</th>
      <th>Descripcion</th>
      <th>Usuario</th>
      <th>Valor RD$</th>
      <th>Valor USD$</th>
      <th>Taza USD$</th>
      <th>Fecha</th>
      <th>Accion</th>
  </thead>
 <?php
      foreach(MaintenanceData::getAll() as $user):?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=finance&opt=vehicle&id=<?php echo $user->id;?>&spends=MANTENIMIENTO" class="btn btn-success"><i class="fas fa-edit"></i></a>
                      </div>
        </td>
        <td>MANTENIMIENTO</td>
        <td><?php $brand = BrandData::getById($user->getCars()->brand_id); $cars = CarsData::getById($user->getCars()->id); echo $brand->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name." - ".$cars->chassis; ?></td>
        <td><?php echo $user->maintenance; ?></td>
        <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
        <td><?php echo number_format($user->purchase_price,2,".",","); ?></td>
        <td><?php echo number_format($user->total,2,".",","); ?></td>
        <td><?php echo number_format($user->cup_dolar,2,".",","); ?></td>
        <td> <?php echo  date("d-m-Y",strtotime($user->created_at));?></td>
         <td class="text-right py-0 align-middle">
<?php  
$base = new Database();
$con = $base->connect();
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()){
if ($x['permits_id']==4): ?>
   
                        <a href="./?action=maintenance&opt=del&id=<?php echo $user->id;?>&car_id=<?php echo $cars->id;?>&spends=MANTENIMIENTO" class="btn btn-danger btn-sm" onclick="return confirmDelete();" ><i class="fas fa-trash"> <?php 
switch (Core::$user->language){
 case 'ES': echo "ELIMINAR"; break;
 case 'EN': echo "DELETE"; break;
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
<?php }; ?>
</td>
    </tr>

<?php endforeach; ?>

 <?php
      foreach(OilData::getAll() as $user):?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=finance&opt=vehicle&id=<?php echo $user->id;?>&spends=ACEITE" class="btn btn-success"><i class="fas fa-edit"></i></a>
                      </div>
        </td>
        <td>CAMBIO DE ACEITE</td>
       <td><?php $brand = BrandData::getById($user->getCars()->brand_id); $cars = CarsData::getById($user->getCars()->id); echo $brand->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name." - ".$cars->chassis; ?></td>
        <td><?php echo number_format($user->kms,0,".",",")." MI/KMS"; ?></td>
        <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
        <td><?php echo number_format($user->total,2,".",","); ?></td>
        <td> <?php echo  date("d-m-Y",strtotime($user->created_at));?></td>
       
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=maintenance&opt=del&id=<?php echo $user->id;?>&spends=ACEITE" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> <?php 
switch (Core::$user->language){
 case 'ES': echo "ELIMINAR"; break;
 case 'EN': echo "DELETE"; break;
}
?></i></a>
                    
    <?php endif;?>
<?php endforeach; ?>
</td>
    </tr>

<?php endforeach; ?>


 <?php
      foreach(TollData::getAll() as $user):?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=finance&opt=vehicle&id=<?php echo $user->id;?>&spends=PEAJE" class="btn btn-success"><i class="fas fa-edit"></i></a>
                      </div>
        </td>
        <td>PEAJE</td>
        <td><?php $brand = BrandData::getById($user->getCars()->brand_id); $cars = CarsData::getById($user->getCars()->id); echo $brand->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name." - ".$cars->chassis; ?></td>
        <td>-- --</td>
        <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
        <td><?php echo number_format($user->total,2,".",","); ?></td>
        <td> <?php echo  date("d-m-Y",strtotime($user->created_at));?></td>
       
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=maintenance&opt=del&id=<?php echo $user->id;?>&spends=PEAJE" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> <?php 
switch (Core::$user->language){
 case 'ES': echo "ELIMINAR"; break;
 case 'EN': echo "DELETE"; break;
}
?></i></a>
                    
    <?php endif;?>
<?php endforeach; ?>
</td>
    </tr>

<?php endforeach; ?>


 <?php
      foreach(FuelsData::getAll() as $user):?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=finance&opt=vehicle&id=<?php echo $user->id;?>&spends=COMBUSTIBLE" class="btn btn-success"><i class="fas fa-edit"></i></a>
                      </div>
        </td>
        <td>COMBUSTIBLE</td>
      <td><?php $brand = BrandData::getById($user->getCars()->brand_id); $cars = CarsData::getById($user->getCars()->id); echo $brand->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name." - ".$cars->chassis; ?></td>
        <td>-- --</td>
        <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
        <td><?php echo number_format($user->total,2,".",","); ?></td>
        <td> <?php echo  date("d-m-Y",strtotime($user->created_at));?></td>
       
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=maintenance&opt=del&id=<?php echo $user->id;?>&spends=COMBUSTIBLE" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> <?php 
switch (Core::$user->language){
 case 'ES': echo "ELIMINAR"; break;
 case 'EN': echo "DELETE"; break;
}
?></i></a>
                    
    <?php endif;?>
<?php endforeach; ?>
</td>
    </tr>

<?php endforeach; ?>

</table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->


    <script type="text/javascript">
      $("#exxmple").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#exxmple_wrapper .col-md-6:eq(0)');

      
    </script>


<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="free"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$u = date("Y-m-d H:i",strtotime("+".$_POST['freedate']."day"));
$html="<option value='".$u."'>$u</option>";
echo $html; 


elseif(isset($_GET["opt"]) && $_GET["opt"]=="carseat"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $html.="<option value='".(($_POST['unit1']*$_POST['price1'])+($_POST['unit2']*$_POST['price2'])+($_POST['unit3']*$_POST['price3'])+($_POST['unit4']*$_POST['price4']))."'>".(($_POST['unit1']*$_POST['price1'])+($_POST['unit2']*$_POST['price2'])+($_POST['unit3']*$_POST['price3'])+($_POST['unit4']*$_POST['price4']))."</option>";
  echo $html; 



elseif(isset($_GET["opt"]) && $_GET["opt"]=="reserve"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(isset($_POST['location'])):
$u = StockData::getAll();
$label = "";
switch (Core::$user->language) {
  case 'ES': $label = '--- ELEGIR ---'; break;
  case 'EN': $label = '--- CHOOSE ---'; break;
}

$html = "<option disabled selected>$label</option>";

  foreach ($u as $value)
    $html.="<option value='".$value->id."'>".strtoupper($value->name)."</option>";
  echo $html; 
endif;

elseif(isset($_GET["opt"]) && $_GET["opt"]=="cars"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if(!empty($_POST['end_at'])):

$selstock = $_POST['stock_id'];
$start = date("Y-m-d", strtotime($_POST['start_at']));
$end = date("Y-m-d", strtotime($_POST['end_at']));


$base = new Database();
$con = $base->connect();
$sql = "SELECT *
FROM cars
WHERE NOT EXISTS (
    SELECT car_id
    FROM booking
    WHERE cars.id = booking.car_id
    AND booking.status <> 3
    AND (
        booking.start_at <= '$end'
        AND booking.end_at >= '$start'
    )
)";

$label = "";
switch (Core::$user->language) {
  case 'ES': $label = '--- ELEGIR ---'; break;
  case 'EN': $label = '--- CHOOSE ---'; break;
}

$html = "<option value='0' disabled selected>$label</option>";

$query = $con->query($sql);
while($r = $query->fetch_array()){ $cars = CarsData::getById($r["id"]); $provider = SuppliersData::getById($cars->provider_id);
  $html.="<option value='".$cars->id."' data-description='".strtoupper($cars->getStock()->name)."'>".$cars->getBrand()->name." ".$cars->name." ".$cars->year." F: [".$cars->token."]</option>";
  }
  echo $html; 
endif; 



elseif(isset($_GET["opt"]) && $_GET["opt"]=="extra"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(isset($_POST['car_id'])): $cars = CarsData::getbyId($_POST['car_id']);?>
<div  class="card-header">
<i class="fa fa-clone"></i>  Extras:
</div>
<?php if($cars->getCategory()->name=="Ambulancia"):?>
<div class="row">
 <div class="col-md-3 col-12">
     
     <input type="text" value="CAMILLA" class="form-control" autocomplete="off"  readonly  placeholder="CAMILLA">
       <div class="input-group">
     <span class="input-group-text autocomplete">UND</span>
     <input  type="number" value="0" class="form-control" autocomplete="off" id="unit1"  placeholder="Unidad"  min="0">
       <input type="number" class="form-control"  value="0"  autocomplete="off" id="price1"   placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>

 <div class="col-md-3 col-12">
     
     <input type="text" value="OXIGENO" class="form-control" autocomplete="off"  readonly  placeholder="OXIGENO">
       <div class="input-group">
     <span class="input-group-text autocomplete">UND</span>
       <input  type="number" class="form-control" value="0" autocomplete="off" id="unit2"   placeholder="Unidad"  min="0" >
       <input  type="number" class="form-control"  value="0" autocomplete="off"  id="price2"  placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>
  

 <div class="col-md-3 col-12">
     
     <input type="text" value="DESFIBRILADOR" class="form-control" autocomplete="off"  readonly  placeholder="DESFIBRILADOR">
       <div class="input-group">
     <span class="input-group-text autocomplete">UND</span>
      <input type="number" value="0" class="form-control" autocomplete="off" id="unit3"   placeholder="Unidad"  min="0" >
       <input  type="number" class="form-control" value="0"  autocomplete="off" id="price3"    placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>
 
 <div class="col-md-3 col-12">
     
     <input type="text" value="SILLA DE RUEDA" class="form-control" autocomplete="off"  readonly  placeholder="SILLA DE RUEDA">
       <div class="input-group">
     <span class="input-group-text autocomplete">UND</span>
      <input type="number" value="0" class="form-control" autocomplete="off" id="unit4" placeholder="Unidad"  min="0" >
       <input  type="number" class="form-control" value="0"  autocomplete="off" id="price4"  placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>
  </div>
  <script>
    
 price1.addEventListener("keyup", function()
    {
        
     $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
        data: {unit1: $('#unit1').val(), price1:  $('#price1').val(), unit2: $('#unit2').val(), price2:  $('#price2').val(), unit3: $('#unit3').val(), price3:  $('#price3').val(), unit4: $('#unit4').val(), price4:  $('#price4').val()},
      success:function(r){
        $('#xmount').html(r);
        
        function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
     // Obtener los valores de los inputs dentro del div
    const unit1 = document.getElementById("unit1").value;
    const price1 = document.getElementById("price1").value;
    
$("#unitx1").val(unit1);
$("#pricex1").val(price1);

    const unit2 = document.getElementById("unit2").value;
    const price2 = document.getElementById("price2").value;
    
$("#unitx2").val(unit2);
$("#pricex2").val(price2);

    const unit3 = document.getElementById("unit3").value;
    const price3 = document.getElementById("price3").value;
    
$("#unitx3").val(unit3);
$("#pricex3").val(price3);

    const unit4 = document.getElementById("unit4").value;
    const price4 = document.getElementById("price4").value;

$("#unitx4").val(unit4);
$("#pricex4").val(price4);

$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
      }
    });
        
    });
    
    
    price2.addEventListener("keyup", function()
    {
        
     $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
        data: {unit1: $('#unit1').val(), price1:  $('#price1').val(), unit2: $('#unit2').val(), price2:  $('#price2').val(), unit3: $('#unit3').val(), price3:  $('#price3').val(), unit4: $('#unit4').val(), price4:  $('#price4').val()},
      success:function(r){
        $('#xmount').html(r);
        
        function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
    
   
}
// Obtener los valores de los inputs dentro del div
    const unit1 = document.getElementById("unit1").value;
    const price1 = document.getElementById("price1").value;
    
$("#unitx1").val(unit1);
$("#pricex1").val(price1);

    const unit2 = document.getElementById("unit2").value;
    const price2 = document.getElementById("price2").value;
    
$("#unitx2").val(unit2);
$("#pricex2").val(price2);

    const unit3 = document.getElementById("unit3").value;
    const price3 = document.getElementById("price3").value;
    
$("#unitx3").val(unit3);
$("#pricex3").val(price3);

    const unit4 = document.getElementById("unit4").value;
    const price4 = document.getElementById("price4").value;

$("#unitx4").val(unit4);
$("#pricex4").val(price4);
 

$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
      }
    });
        
    });
    
    
     price3.addEventListener("keyup", function()
    {
        
     $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
        data: {unit1: $('#unit1').val(), price1:  $('#price1').val(), unit2: $('#unit2').val(), price2:  $('#price2').val(), unit3: $('#unit3').val(), price3:  $('#price3').val(), unit4: $('#unit4').val(), price4:  $('#price4').val()},
      success:function(r){
        $('#xmount').html(r);
        
        function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 // Obtener los valores de los inputs dentro del div
    const unit1 = document.getElementById("unit1").value;
    const price1 = document.getElementById("price1").value;
    
$("#unitx1").val(unit1);
$("#pricex1").val(price1);

    const unit2 = document.getElementById("unit2").value;
    const price2 = document.getElementById("price2").value;
    
$("#unitx2").val(unit2);
$("#pricex2").val(price2);

    const unit3 = document.getElementById("unit3").value;
    const price3 = document.getElementById("price3").value;
    
$("#unitx3").val(unit3);
$("#pricex3").val(price3);

    const unit4 = document.getElementById("unit4").value;
    const price4 = document.getElementById("price4").value;

$("#unitx4").val(unit4);
$("#pricex4").val(price4);

$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
      }
    });
        
    });
    
    
    price4.addEventListener("keyup", function()
    {
        
     $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
        data: {unit1: $('#unit1').val(), price1:  $('#price1').val(), unit2: $('#unit2').val(), price2:  $('#price2').val(), unit3: $('#unit3').val(), price3:  $('#price3').val(), unit4: $('#unit4').val(), price4:  $('#price4').val()},
      success:function(r){
        $('#xmount').html(r);
        
        function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 // Obtener los valores de los inputs dentro del div
    const unit1 = document.getElementById("unit1").value;
    const price1 = document.getElementById("price1").value;
    
$("#unitx1").val(unit1);
$("#pricex1").val(price1);

    const unit2 = document.getElementById("unit2").value;
    const price2 = document.getElementById("price2").value;
    
$("#unitx2").val(unit2);
$("#pricex2").val(price2);

    const unit3 = document.getElementById("unit3").value;
    const price3 = document.getElementById("price3").value;
    
$("#unitx3").val(unit3);
$("#pricex3").val(price3);

    const unit4 = document.getElementById("unit4").value;
    const price4 = document.getElementById("price4").value;

$("#unitx4").val(unit4);
$("#pricex4").val(price4);

$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
      }
    });
        
    });
    
    
</script>
<?php else:?>
<div class="row">
 <div class="col-md-4 col-12">
     
     <input type="text" value="CARSEAT" class="form-control" autocomplete="off"  readonly  placeholder="CARSEAT">
       <div class="input-group">
     <span class="input-group-text autocomplete">UND</span>
     <input type="number" value="0" class="form-control" autocomplete="off" id="unit1"   placeholder="Unidad"  min="0">
       <input type="number" class="form-control"  value="0"  autocomplete="off" id="price1" placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>

 <div class="col-md-4 col-12">
     
     <input type="text" value="INTERNET" class="form-control" autocomplete="off"  readonly  placeholder="INTERNET">
       <div class="input-group">
     <span class="input-group-text autocomplete">UND</span>
       <input  type="number" class="form-control" value="0" autocomplete="off" id="unit2"  placeholder="Unidad"  min="0" >
       <input  type="number" class="form-control"  value="0" autocomplete="off" id="price2" placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>
  

 <div class="col-md-4 col-12">
     
     <input type="text" value="TRAILER" class="form-control" autocomplete="off"  readonly  placeholder="TRAILER">
       <div class="input-group">
     <span class="input-group-text autocomplete">UND</span>
      <input type="number" value="0" class="form-control" autocomplete="off" id="unit3"  placeholder="Unidad"  min="0" >
       <input  type="number" class="form-control" value="0"  autocomplete="off" id="price3"  placeholder="Precio"  min="0" step="0.01">
    </div>
  </div>
 
  </div>
  
  <script>

 price1.addEventListener("keyup", function()
    {
        
     $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
        data: {unit1: $('#unit1').val(), price1:  $('#price1').val(), unit2: $('#unit2').val(), price2:  $('#price2').val(), unit3: $('#unit3').val(), price3:  $('#price3').val(), unit4: $('#unit4').val(), price4:  $('#price4').val()},
      success:function(r){
        $('#xmount').html(r);
        
        function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 
     // Obtener los valores de los inputs dentro del div
    const unit1 = document.getElementById("unit1").value;
    const price1 = document.getElementById("price1").value;
    
$("#unitx1").val(unit1);
$("#pricex1").val(price1);

    const unit2 = document.getElementById("unit2").value;
    const price2 = document.getElementById("price2").value;
    
$("#unitx2").val(unit2);
$("#pricex2").val(price2);

    const unit3 = document.getElementById("unit3").value;
    const price3 = document.getElementById("price3").value;
    
$("#unitx3").val(unit3);
$("#pricex3").val(price3);

$("#unitx4").val(0);
$("#pricex4").val(0);

$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
      }
    });
        
    });
    
    
    price2.addEventListener("keyup", function()
    {
        
     $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
        data: {unit1: $('#unit1').val(), price1:  $('#price1').val(), unit2: $('#unit2').val(), price2:  $('#price2').val(), unit3: $('#unit3').val(), price3:  $('#price3').val(), unit4: $('#unit4').val(), price4:  $('#price4').val()},
      success:function(r){
        $('#xmount').html(r);
        
        function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
    
   
}
// Obtener los valores de los inputs dentro del div
    const unit1 = document.getElementById("unit1").value;
    const price1 = document.getElementById("price1").value;
    
$("#unitx1").val(unit1);
$("#pricex1").val(price1);

    const unit2 = document.getElementById("unit2").value;
    const price2 = document.getElementById("price2").value;
    
$("#unitx2").val(unit2);
$("#pricex2").val(price2);

    const unit3 = document.getElementById("unit3").value;
    const price3 = document.getElementById("price3").value;
    
$("#unitx3").val(unit3);
$("#pricex3").val(price3);

$("#unitx4").val(0);
$("#pricex4").val(0);

$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
      }
    });
        
    });
    
    
     price3.addEventListener("keyup", function()
    {
        
     $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
        data: {unit1: $('#unit1').val(), price1:  $('#price1').val(), unit2: $('#unit2').val(), price2:  $('#price2').val(), unit3: $('#unit3').val(), price3:  $('#price3').val(), unit4: $('#unit4').val(), price4:  $('#price4').val()},
      success:function(r){
        $('#xmount').html(r);
        
        function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 // Obtener los valores de los inputs dentro del div
    const unit1 = document.getElementById("unit1").value;
    const price1 = document.getElementById("price1").value;
    
$("#unitx1").val(unit1);
$("#pricex1").val(price1);

    const unit2 = document.getElementById("unit2").value;
    const price2 = document.getElementById("price2").value;
    
$("#unitx2").val(unit2);
$("#pricex2").val(price2);

    const unit3 = document.getElementById("unit3").value;
    const price3 = document.getElementById("price3").value;
    
$("#unitx3").val(unit3);
$("#pricex3").val(price3);

$("#unitx4").val(0);
$("#pricex4").val(0);

$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
      }
    });
        
    });
    
    
    price4.addEventListener("keyup", function()
    {
        
     $.ajax({
      type:"POST",
      url:"./?action=get&opt=carseat",
        data: {unit1: $('#unit1').val(), price1:  $('#price1').val(), unit2: $('#unit2').val(), price2:  $('#price2').val(), unit3: $('#unit3').val(), price3:  $('#price3').val(), unit4: $('#unit4').val(), price4:  $('#price4').val()},
      success:function(r){
        $('#xmount').html(r);
        
        function agregarSeparadorMiles(numero) {
    let partesNumero = numero.toString().split(',');

    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return partesNumero.join('.');
}

 // Obtener los valores de los inputs dentro del div
    const unit1 = document.getElementById("unit1").value;
    const price1 = document.getElementById("price1").value;
    
$("#unitx1").val(unit1);
$("#pricex1").val(price1);

    const unit2 = document.getElementById("unit2").value;
    const price2 = document.getElementById("price2").value;
    
$("#unitx2").val(unit2);
$("#pricex2").val(price2);

    const unit3 = document.getElementById("unit3").value;
    const price3 = document.getElementById("price3").value;
    
$("#unitx3").val(unit3);
$("#pricex3").val(price3);

$("#unitx4").val(0);
$("#pricex4").val(0);

$("#remaining").val( agregarSeparadorMiles((parseFloat($("#amount").val())+parseFloat($("#xmount").val()))-parseFloat($("#payment").val())));
      }
    });
        
    });
    
    
</script>
<?php endif; endif;?>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="tariff"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(isset($_POST['cars'])):
$u = TariffData::getAllBySQL("where brand_id='".$_POST['cars']."'");
$label = "";
switch (Core::$user->language) {
  case 'ES': $label = '--- ELEGIR ---'; break;
  case 'EN': $label = '--- CHOOSE ---'; break;
}

$html = "<option disabled selected>$label</option>";

  foreach ($u as $value)
    $html.="<option value='".$value->price."'>".$value->description.": ".$value->price."</option>";
  echo $html; 
endif;

elseif(isset($_GET["opt"]) && $_GET["opt"]=="tariff2"):
  if(isset($_POST['price'])):
    $html.="<option value='".($_POST['tcars']*$_POST['price'])*$_POST['dvs']."'>".number_format(($_POST['tcars']*$_POST['price'])*$_POST['dvs'],0,".",",")."</option>";
  echo $html; 
endif; 

elseif(isset($_GET["opt"]) && $_GET["opt"]=="price2"):
if(isset($_POST['price'])):
    $html.="<option value='".$_POST['price']."'>".$_POST['price']."</option>";
  echo $html; 
endif; 

elseif(isset($_GET["opt"]) && $_GET["opt"]=="amount"):
if(isset($_POST['cars'])):
 $u = CarsData::getAllBySQL("where id='".$_POST['cars']."' and status!=4");
  foreach ($u as $value)
    $html.="<option value='".($_POST['tcars']*$_POST['price'])*$_POST['dvs']."'>".number_format(($_POST['tcars']*$_POST['price'])*$_POST['dvs'],0,".",",")."</option>";
  echo $html; 
endif; 

elseif(isset($_GET["opt"]) && $_GET["opt"]=="price"):
if(isset($_POST['cars'])):
 $u = CarsData::getAllBySQL("where id='".$_POST['cars']."' and status!=4");
  foreach ($u as $value)
    $html.="<option value='".$_POST['price']."'>".$_POST['price']."</option>";
  echo $html; 
endif; 

elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"):
if(isset($_POST['location'])):
    
 $x = CarsData::getById($_POST['location']);
 $u = StockData::getAllBySQL("where id='".$x->stock_id."'");
$html="";
  foreach ($u as $value)
    $html.="<option value='".$value->id."'>".$value->name."</option>";
  echo $html; 
endif; 

elseif(isset($_GET["opt"]) && $_GET["opt"]=="editcars"):
if(isset($_POST['car'])):
 $u = CarsData::getAllBySQL("where id='".$_POST['car']."' and status!=4");
$html="";
  foreach ($u as $value)
    $html.="<option value='".$value->id."'>".$value->getBrand()->name." ".$value->name." ".$value->year." ".$value->token."</option>";
  echo $html; 
endif; 

elseif(isset($_GET["opt"]) && $_GET["opt"]=="editcar2s"):
if(isset($_POST['id'])):
$z = BookingData::getById($_POST['id']);

if($z->car2_id==0):
$u = CarsData::getAllBySQL("where id<>'".$z->car_id."' and status!=4");
$label = "";
switch (Core::$user->language) {
  case 'ES': $label = '--- ELEGIR ---'; break;
  case 'EN': $label = '--- CHOOSE ---'; break;
}

$html = "<option value='0' disabled selected>$label</option>";

  foreach ($u as $value)
    $html.="<option value='".$value->id."'>".$value->getBrand()->name." ".$value->name." ".$value->year." ".$value->token."</option>";
  echo $html; 
endif; 

if($z->car2_id<>0):
$u = CarsData::getAllBySQL("where id='".$z->car2_id."' and status!=4");
  foreach ($u as $value)
    $html.="<option value='".$value->id."'>".$value->getBrand()->name." ".$value->name." ".$value->year." ".$value->token."</option>";
  echo $html;    
endif; 

endif; 

elseif(isset($_GET["opt"]) && $_GET["opt"]=="editariff"):
if(isset($_POST['price'])):
 $u = BookingData::getAllBySQL("where id='".$_POST['price']."'");
$html="";
  foreach ($u as $value)
    $html.="<option value='".$value->price."'>".$value->price."</option>";
  echo $html; 
endif;


elseif(isset($_GET["opt"]) && $_GET["opt"]=="editday"):
if(isset($_POST['price'])):
 $u = BookingData::getAllBySQL("where id='".$_POST['price']."'");
$html="";
  foreach ($u as $value)
    $html.="<option value='".$value->day."'>".$value->day."</option>";
  echo $html; 
endif;

elseif(isset($_GET["opt"]) && $_GET["opt"]=="editamount"):
if(isset($_POST['cars'])):
 $u = CarsData::getAllBySQL("where id='".$_POST['cars']."' abd status!=4");
 $html="";
  foreach ($u as $value)
    $html.="<option value='".($_POST['tcars']*$_POST['price'])*$_POST['dvs']."'>".number_format(($_POST['tcars']*$_POST['price'])*$_POST['dvs'],0,".",",")."</option>";
  echo $html; 
endif; 

elseif(isset($_GET["opt"]) && $_GET["opt"]=="available"):$go = $_GET["go"];
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$search  ="";
if($go=="name"):
$search=$_GET["product_name"]; 
$products = CarsData::getLike($search,StockData::getPrincipal()->id);
else:
$products = CarsData::getAllBySQL("where status=0 || status=3 and stock_id=".StockData::getPrincipal()->id);
endif;?>


      <!-- Default box -->
      <div class="card card-solid" style="background-color:#222;">
        <div class="card-body pb-0">
          <div class="row">
            <?php foreach($products as $sells):?>
           
          <!-- /.col -->
          <div class="col-md-6">
            <!-- Widget: user widget style 1 -->
            <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              
              <div class="widget-user-header bg-warning text-white">
                <h3 class="widget-user-username"><?php echo strtoupper($sells->getBrand()->name." ".$sells->name); ?></h3>
              </div>
              
        
              <div class="card-footer" style="margin-top:-15%;">
                <div class="row">
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                      <h5 class="description-header">
                   <?php echo strtoupper($sells->getExColor()->name); ?> </h5>
                   </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                    </div>
                    <!-- /.description-block -->
                  </div>    <!-- /.col -->
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                      <h5 class="description-header">
                   <?php echo strtoupper($sells->year); ?> </h5>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
                
                <div class="widget-user-image">
                  <?php if(!empty($sells->invoice_file)):?>
                <img class="img-circle elevation-2" src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>" style="width:100px; height: 100px; ">
                <?php else:?>
                <img class="img-circle elevation-2"  style="width:100px; height: 100px; background-color:white;">
                <?php endif;?>
              </div>
           
             <div class="card-footer" >
                 <div class="input-group" style="margin-top:-1%;">
                  <div class=" col-md-12">

                   <div class="row">
                  <div class="col-md-4">
                    <a href="./?view=cars&opt=description&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn btn-info"><i class="fa fa-eye"></i> INFO 
                      
                    </a>
                     </div>
                     
                  <div class="col-md-4">
                    <a href="./?view=gallery&opt=all&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn-secondary">  <i class="fa fa-image"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "GALERIA"; break;
 case 'EN': echo "GALLERY"; break;
}
?>  </a>
  </div>
                <div class="col-md-4">
                    <a href="./?view=cars&opt=edit&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn-warning">  <i class="fa fa-edit"></i><?php 
switch (Core::$user->language){
 case 'ES': echo "EDITAR"; break;
 case 'EN': echo "EDIT"; break;
}
?></a>
                    
                     </div>
                   </div>
                    
                  </div>
                  </div>
                  
                </div>
                
              
              
                <div class="row">
                    
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header"><?php 
switch (Core::$user->language){
 case 'ES': echo "PLACA"; break;
 case 'EN': echo "PLATE"; break;
}
?> </h5>
                      <span class="description-text"><?php echo $sells->plate; ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">CTG.</h5>
                      <span class="description-text"><?php echo  strtoupper($sells->getCategory()->name); ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>    <!-- /.col -->
                  <div class="col-sm-4">
                    <div class="description-block">
                      <h5 class="description-header"><?php 
switch (Core::$user->language){
 case 'ES': echo "FICHA"; break;
 case 'EN': echo "FILE"; break;
}
?></h5>
                      <span class="description-text"><?php echo $sells->token; ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              
              
                <div class="card-footer" >
                 <div class="input-group" style="margin-top:-10%;">
                  <div class=" col-md-12">

                   <div class="row">
                 
                  <div class="col-md-6">
                    <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=5" class="btn btn-sm btn-block btn-warning"><i class="fa fa-dollar-sign"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "VENDIDO"; break;
 case 'EN': echo "SOLD"; break;
}
?>
                      
                    </a>
                    
                     </div>
                  <div class="col-md-6">
                    <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=4" class="btn btn-sm btn-block btn-success"><i class="fa fa-cog"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "TALLER"; break;
 case 'EN': echo "WORKSHOP"; break;
}

?>
                     
                    </a>
                    
                     </div>
                   </div>
                    
                  </div>
                  </div>
                  
                </div>
                
                <?php  
$base = new Database();
$con = $base->connect();
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()){
if ($x['permits_id']==4): ?>
   
                        <a href="./?action=cars&opt=del&id=<?php echo $sells->id;?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();" ><i class="fas fa-trash"> <?php 
switch (Core::$user->language){
 case 'ES': echo "ELIMINAR"; break;
 case 'EN': echo "DELETE"; break;
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
<?php }; ?>
            </div>
            <!-- /.widget-user -->
          </div> 
          <?php endforeach;?>
          </div>
        </div>
      
        <!-- /.card-footer -->
      </div>
      <!-- /.card -->


<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="carsall"):$go = $_GET["go"];
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$search  ="";
if($go=="name"):
$search=$_GET["product_name"]; 
$products = CarsData::getLike($search,StockData::getPrincipal()->id);
else:
$products = CarsData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id);
endif;?>


      
      <!-- Default box -->
      <div class="card card-solid" style="background-color:#222;">
        <div class="card-body pb-0">
          <div class="row">
            <?php foreach($products as $sells):?>
           
          <!-- /.col -->
          <div class="col-md-6">
            <!-- Widget: user widget style 1 -->
            <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              
              <div class="widget-user-header bg-warning text-white">
                <h3 class="widget-user-username"><?php echo strtoupper($sells->getBrand()->name." ".$sells->name); ?></h3>
              </div>
              
        
              <div class="card-footer" style="margin-top:-15%;">
                <div class="row">
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                      <h5 class="description-header">
                   <?php echo strtoupper($sells->getExColor()->name); ?> </h5>
                   </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                    </div>
                    <!-- /.description-block -->
                  </div>    <!-- /.col -->
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                      <h5 class="description-header">
                   <?php echo strtoupper($sells->year); ?> </h5>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
                
                <div class="widget-user-image">
                  <?php if(!empty($sells->invoice_file)):?>
                <img class="img-circle elevation-2" src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>" style="width:100px; height: 100px; ">
                <?php else:?>
                <img class="img-circle elevation-2"  style="width:100px; height: 100px; background-color:white;">
                <?php endif;?>
              </div>
           
             <div class="card-footer" >
                 <div class="input-group" style="margin-top:-1%;">
                  <div class=" col-md-12">

                   <div class="row">
                  <div class="col-md-4">
                    <a href="./?view=cars&opt=description&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn btn-info"><i class="fa fa-eye"></i> INFO 
                      
                    </a>
                     </div>
                     
                  <div class="col-md-4">
                    <a href="./?view=gallery&opt=all&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn-secondary">  <i class="fa fa-image"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "GALERIA"; break;
 case 'EN': echo "GALLERY"; break;
}
?>  </a>
  </div>
                <div class="col-md-4">
                    <a href="./?view=cars&opt=edit&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn-warning">  <i class="fa fa-edit"></i><?php 
switch (Core::$user->language){
 case 'ES': echo "EDITAR"; break;
 case 'EN': echo "EDIT"; break;
}
?></a>
                    
                     </div>
                   </div>
                    
                  </div>
                  </div>
                  
                </div>
                
              
              
                <div class="row">
                    
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header"><?php 
switch (Core::$user->language){
 case 'ES': echo "PLACA"; break;
 case 'EN': echo "PLATE"; break;
}
?> </h5>
                      <span class="description-text"><?php echo $sells->plate; ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">CTG.</h5>
                      <span class="description-text"><?php echo  strtoupper($sells->getCategory()->name); ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>    <!-- /.col -->
                  <div class="col-sm-4">
                    <div class="description-block">
                      <h5 class="description-header"><?php 
switch (Core::$user->language){
 case 'ES': echo "FICHA"; break;
 case 'EN': echo "FILE"; break;
}
?></h5>
                      <span class="description-text"><?php echo $sells->token; ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              
              
                <div class="card-footer" >
                 <div class="input-group" style="margin-top:-10%;">
                  <div class=" col-md-12">

                   <div class="row">
                 
                  <div class="col-md-6">
                    <a class="btn btn-sm btn-block btn-warning"><i class="fa fa-dollar-sign"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "VENDIDO"; break;
 case 'EN': echo "SOLD"; break;
}
?>
                      
                    </a>
                    
                     </div>
                  <div class="col-md-6">
                    
                    <a class="btn btn-sm btn-block btn-success"><i class="fa fa-cog"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "TALLER"; break;
 case 'EN': echo "WORKSHOP"; break;
}
?>
                     
                    </a>
                    
                     </div>
                   </div>
                    
                  </div>
                  </div>
                  
                </div>
                
            
                        <a class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();" ><i class="fas fa-trash"> <?php 
switch (Core::$user->language){
 case 'ES': echo "ELIMINAR"; break;
 case 'EN': echo "DELETE"; break;
}
?></i></a>
                    
       
            </div>
            <!-- /.widget-user -->
          </div> 
          <?php endforeach;?>
          </div>
        </div>
      
        <!-- /.card-footer -->
      </div>
      <!-- /.card -->

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="reserved"):$go = $_GET["go"];
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$search  ="";
if($go=="name"):
$search=$_GET["product_name"]; 
$products = CarsData::getLike($search,StockData::getPrincipal()->id);
else:
$products = CarsData::getAllBySQL("where status=1 and stock_id=".StockData::getPrincipal()->id);
endif;?>


     
      <!-- Default box -->
      <div class="card card-solid" style="background-color:#222;">
        <div class="card-body pb-0">
          <div class="row">
            <?php foreach($products as $sells):?>
           
          <!-- /.col -->
          <div class="col-md-6">
            <!-- Widget: user widget style 1 -->
            <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              
              <div class="widget-user-header bg-warning text-white">
                <h3 class="widget-user-username"><?php echo strtoupper($sells->getBrand()->name." ".$sells->name); ?></h3>
              </div>
              
        
              <div class="card-footer" style="margin-top:-15%;">
                <div class="row">
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                      <h5 class="description-header">
                   <?php echo strtoupper($sells->getExColor()->name); ?> </h5>
                   </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                    </div>
                    <!-- /.description-block -->
                  </div>    <!-- /.col -->
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                      <h5 class="description-header">
                   <?php echo strtoupper($sells->year); ?> </h5>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
                
                <div class="widget-user-image">
                  <?php if(!empty($sells->invoice_file)):?>
                <img class="img-circle elevation-2" src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>" style="width:100px; height: 100px; ">
                <?php else:?>
                <img class="img-circle elevation-2"  style="width:100px; height: 100px; background-color:white;">
                <?php endif;?>
              </div>
           
             <div class="card-footer" >
                 <div class="input-group" style="margin-top:-1%;">
                  <div class=" col-md-12">

                   <div class="row">
                  <div class="col-md-4">
                    <a href="./?view=cars&opt=description&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn btn-info"><i class="fa fa-eye"></i> INFO 
                      
                    </a>
                     </div>
                     
                  <div class="col-md-4">
                    <a href="./?view=gallery&opt=all&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn-secondary">  <i class="fa fa-image"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "GALERIA"; break;
 case 'EN': echo "GALLERY"; break;
}
?>  </a>
  </div>
                <div class="col-md-4">
                    <a href="./?view=cars&opt=edit&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn-warning">  <i class="fa fa-edit"></i><?php 
switch (Core::$user->language){
 case 'ES': echo "EDITAR"; break;
 case 'EN': echo "EDIT"; break;
}
?></a>
                    
                     </div>
                   </div>
                    
                  </div>
                  </div>
                  
                </div>
                
              
              
                <div class="row">
                    
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header"><?php 
switch (Core::$user->language){
 case 'ES': echo "PLACA"; break;
 case 'EN': echo "PLATE"; break;
}
?> </h5>
                      <span class="description-text"><?php echo $sells->plate; ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">CTG.</h5>
                      <span class="description-text"><?php echo  strtoupper($sells->getCategory()->name); ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>    <!-- /.col -->
                  <div class="col-sm-4">
                    <div class="description-block">
                      <h5 class="description-header"><?php 
switch (Core::$user->language){
 case 'ES': echo "FICHA"; break;
 case 'EN': echo "FILE"; break;
}
?></h5>
                      <span class="description-text"><?php echo $sells->token; ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              
              
                <div class="card-footer" >
                 <div class="input-group" style="margin-top:-10%;">
                  <div class=" col-md-12">

                   <div class="row">
                 
                  <div class="col-md-6">
                    <a  class="btn btn-sm btn-block btn-warning"><i class="fa fa-dollar-sign"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "VENDIDO"; break;
 case 'EN': echo "SOLD"; break;
}
?>
                      
                    </a>
                    
                     </div>
                  <div class="col-md-6">
                   
                    <a class="btn btn-sm btn-block btn-success"><i class="fa fa-cog"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "TALLER"; break;
 case 'EN': echo "WORKSHOP"; break;
}
?>

                     
                    </a>
                    
                           </div>
                   </div>
                    
                  </div>
                  </div>
                  
                </div>
                
            
                        <a class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();" ><i class="fas fa-trash"> <?php 
switch (Core::$user->language){
 case 'ES': echo "ELIMINAR"; break;
 case 'EN': echo "DELETE"; break;
}
?></i></a>
                    
       
            </div>
            <!-- /.widget-user -->
          </div> 
          <?php endforeach;?>
          </div>
        </div>
      
        <!-- /.card-footer -->
      </div>
      <!-- /.card -->

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="rented"):$go = $_GET["go"];
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$search  ="";
if($go=="name"):
$search=$_GET["product_name"]; 
$products = CarsData::getLike($search,StockData::getPrincipal()->id);
else:
$products = CarsData::getAllBySQL("where status=2 and stock_id=".StockData::getPrincipal()->id);
endif;?>


      
      <!-- Default box -->
      <div class="card card-solid" style="background-color:#222;">
        <div class="card-body pb-0">
          <div class="row">
            <?php foreach($products as $sells):?>
           
          <!-- /.col -->
          <div class="col-md-6">
            <!-- Widget: user widget style 1 -->
            <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              
              <div class="widget-user-header bg-warning text-white">
                <h3 class="widget-user-username"><?php echo strtoupper($sells->getBrand()->name." ".$sells->name); ?></h3>
              </div>
              
        
              <div class="card-footer" style="margin-top:-15%;">
                <div class="row">
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                      <h5 class="description-header">
                   <?php echo strtoupper($sells->getExColor()->name); ?> </h5>
                   </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                    </div>
                    <!-- /.description-block -->
                  </div>    <!-- /.col -->
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                      <h5 class="description-header">
                   <?php echo strtoupper($sells->year); ?> </h5>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
                
                <div class="widget-user-image">
                  <?php if(!empty($sells->invoice_file)):?>
                <img class="img-circle elevation-2" src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>" style="width:100px; height: 100px; ">
                <?php else:?>
                <img class="img-circle elevation-2"  style="width:100px; height: 100px; background-color:white;">
                <?php endif;?>
              </div>
           
             <div class="card-footer" >
                 <div class="input-group" style="margin-top:-1%;">
                  <div class=" col-md-12">

                   <div class="row">
                  <div class="col-md-4">
                    <a href="./?view=cars&opt=description&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn btn-info"><i class="fa fa-eye"></i> INFO 
                      
                    </a>
                     </div>
                     
                  <div class="col-md-4">
                    <a href="./?view=gallery&opt=all&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn-secondary">  <i class="fa fa-image"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "GALERIA"; break;
 case 'EN': echo "GALLERY"; break;
}
?>  </a>
  </div>
                <div class="col-md-4">
                    <a href="./?view=cars&opt=edit&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn-warning">  <i class="fa fa-edit"></i><?php 
switch (Core::$user->language){
 case 'ES': echo "EDITAR"; break;
 case 'EN': echo "EDIT"; break;
}
?></a>
                    
                     </div>
                   </div>
                    
                  </div>
                  </div>
                  
                </div>
                
              
              
                <div class="row">
                    
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header"><?php 
switch (Core::$user->language){
 case 'ES': echo "PLACA"; break;
 case 'EN': echo "PLATE"; break;
}
?> </h5>
                      <span class="description-text"><?php echo $sells->plate; ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">CTG.</h5>
                      <span class="description-text"><?php echo  strtoupper($sells->getCategory()->name); ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>    <!-- /.col -->
                  <div class="col-sm-4">
                    <div class="description-block">
                      <h5 class="description-header"><?php 
switch (Core::$user->language){
 case 'ES': echo "FICHA"; break;
 case 'EN': echo "FILE"; break;
}
?></h5>
                      <span class="description-text"><?php echo $sells->token; ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              
              
                <div class="card-footer" >
                 <div class="input-group" style="margin-top:-10%;">
                  <div class=" col-md-12">

                   <div class="row">
                 
                  <div class="col-md-6">
                    <a  class="btn btn-sm btn-block btn-warning"><i class="fa fa-dollar-sign"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "VENDIDO"; break;
 case 'EN': echo "SOLD"; break;
}
?>
                      
                    </a>
                    
                     </div>
                  <div class="col-md-6">
                   
                    <a class="btn btn-sm btn-block btn-success"><i class="fa fa-cog"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "TALLER"; break;
 case 'EN': echo "WORKSHOP"; break;
}
?>

                     
                    </a>
                    
                     </div>
                   </div>
                    
                  </div>
                  </div>
                  
                </div>
                
             
   
                        <a  class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();" ><i class="fas fa-trash"> <?php 
switch (Core::$user->language){
 case 'ES': echo "ELIMINAR"; break;
 case 'EN': echo "DELETE"; break;
}
?></i></a>
                    
      
            </div>
            <!-- /.widget-user -->
          </div> 
          <?php endforeach;?>
          </div>
        </div>
      
        <!-- /.card-footer -->
      </div>
      <!-- /.card -->

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="cogs"):$go = $_GET["go"];
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$search  ="";
if($go=="name"):
$search=$_GET["product_name"]; 
$products = CarsData::getLike($search,StockData::getPrincipal()->id);
else:
$products = CarsData::getAllBySQL("where status=4 and stock_id=".StockData::getPrincipal()->id);
endif;?>


      <!-- Default box -->
      <div class="card card-solid"  style="background-color:#222;">
        <div class="card-body pb-0">
          <div class="row">
            <?php foreach($products as $sells):?>
     
          <!-- /.col -->
          <div class="col-md-4">
            <!-- Widget: user widget style 1 -->
            <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              
                <div class="widget-user-header bg-warning text-white">
                <h3 class="widget-user-username"><?php echo strtoupper($sells->getBrand()->name." ".$sells->name); ?></h3>
              </div>
              
        
              <div class="card-footer" style="margin-top:-15%;">
                <div class="row">
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                      <h5 class="description-header">
                   <?php echo strtoupper($sells->getExColor()->name); ?> </h5>
                   </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                    </div>
                    <!-- /.description-block -->
                  </div>    <!-- /.col -->
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                      <h5 class="description-header">
                   <?php echo strtoupper($sells->year); ?> </h5>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
                
                 <div class="card-footer" >
                 <div class="input-group" style="margin-top:-15%;">
                  <div class=" col-md-12">

                  
                    <a href="./?view=gallery&opt=all&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn-secondary">  <i class="fa fa-image"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "GALERIA"; break;
 case 'EN': echo "GALLERY"; break;
}
?>  
                    
                    </a>
                    
                    
                  </div>
                  </div>
                  
                </div>
                
              <div class="widget-user-image">
                  <?php if(!empty($sells->invoice_file)):?>
                <img class="img-circle elevation-2" src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>" style="width:100px; height: 100px; ">
                <?php else:?>
                <img class="img-circle elevation-2"  style="width:100px; height: 100px; background-color:white;">
                <?php endif;?>
              </div>
              
              <div class="card-footer" style="margin-top:-5%;">
                <div class="row">
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header"><?php 
switch (Core::$user->language){
 case 'ES': echo "PLACA"; break;
 case 'EN': echo "PLATE"; break;
}
?></h5>
                      <span class="description-text"><?php echo $sells->plate; ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">CTG.</h5>
                      <span class="description-text"><?php echo  strtoupper($sells->getCategory()->name); ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>    <!-- /.col -->
                  <div class="col-sm-4">
                    <div class="description-block">
                      <h5 class="description-header"><?php 
switch (Core::$user->language){
 case 'ES': echo "FICHA"; break;
 case 'EN': echo "FILE"; break;
}
?></h5>
                      <span class="description-text"><?php echo $sells->token; ?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
              
                <div class="card-footer" >
                 <div class="input-group" style="margin-top:-15%;">
                  <div class=" col-md-12">

                  
                   
                   <div class="row">
                  <div class="col-md-4  col-4">
                    <a href="./?view=cars&opt=description&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn btn-info"> INFO 
                      <i class="fa fa-eye"></i>
                    </a>
                     </div>
                     
                  <div class="col-md-4 col-4">
                    <a href="./?view=cars&opt=edit&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn-warning"> <?php 
switch (Core::$user->language){
 case 'ES': echo "EDITAR"; break;
 case 'EN': echo "EDIT"; break;
}
?>
                      
                    </a>
                    
                     </div>
                  <div class="col-md-4 col-4">
                    <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=4" class="btn btn-sm btn-block btn-success"><?php 
switch (Core::$user->language){
 case 'ES': echo "TALLER"; break;
 case 'EN': echo "WORKSHOP"; break;
}
?>
                     
                    </a>
                   
                     </div>
                   </div>
                    
                    
                  </div>
                  </div>
                  
                </div>
            </div>
            <!-- /.widget-user -->
          </div> 
          <?php endforeach;?>
          </div>
        </div>
      
        <!-- /.card-footer -->
      </div>
      <!-- /.card -->
 <?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="allnocat"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$go = $_GET["go"];
$search  ="";
if($go=="name"):
$search=$_GET["product_name"]; 
$products = CarsData::getLike($search,StockData::getPrincipal()->id);
else:
$products = CarsData::getAllnoCat();
endif;
$ticket_image = StockData::getPrincipal()->ticket_image;

?>
   <?php if(count($products)>0):?>
  
      <!-- Default box -->
      <div class="card card-solid"  style="background-color:#222;">
        <div class="card-body pb-0">
          <div class="row">
            <?php foreach($products as $sells):?>
           
          <!-- /.col -->
          <div class="col-md-6">
            <!-- Widget: user widget style 1 -->
            <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              
                <div class="widget-user-header bg-warning text-white">
                <h3 class="widget-user-username"><?php echo strtoupper($sells->getBrand()->name." ".$sells->name); ?></h3>
                
               
             <h6 class="description-header"><?php echo $sells->chassis; ?></h6>
              </div>
              
        
              <div class="card-footer" style="margin-top:-15%;">
                <div class="row">
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                      <h5 class="description-header"><?php echo strtoupper($sells->getExColor()->name); ?> </h5>
                   
                   </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                    </div>
                    <!-- /.description-block -->
                  </div>    <!-- /.col -->
                  <div class="col-sm-4 col-4">
                    <div class="description-block">
                      <h5 class="description-header">
                   <?php echo strtoupper($sells->year); ?> </h5>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col --> 
                </div>
                <!-- /.row -->   
              </div>
              
               <div class="card-footer" >
                 <div class="input-group" style="margin-top:-20%;">
                  <div class=" col-md-12">

                  
                         <form id="form-<?php echo $sells->id; ?>">
            
         <input type="hidden" name="q" value="1" class="form-control" required>
       <input type="hidden" name="product_id" value="<?php echo $sells->id; ?>">
       
       
                 <div class="input-group" >
                  

       <input type="number" name="price" autocomplete="off" class="form-control" placeholder="Precio x Dia" value="<?php echo $sells->price;?>" required style="border-color:white;">
                    
                    
                
                  <button type="submit" id="mesero-<?php echo $sells->id; ?>" class="btn btn-success"><i class="fa fa-plus"></i> </button>
       </form>    
                  <script type="text/javascript">
   $("#form-<?php echo $sells->id; ?>").submit(function(e){
            e.preventDefault();
            $.post("./?action=add&opt=tocart",$("#form-<?php echo $sells->id; ?>").serialize(),function(data){
            $.get("./?action=get&opt=cart","",function(data2){
                $("#cartoxsell").html(data2);
            });

            });

        });
    </script>
                    
                    
                  </div>
                  </div>
                  
                </div>
                
                
              <div class="widget-user-image">
                  <?php if(!empty($sells->invoice_file)):?>
                <img class="img-circle elevation-2" src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>" style="width:100px; height: 100px; ">
                <?php else:?>
                <img class="img-circle elevation-2"  style="width:100px; height: 100px; background-color:white;">
                <?php endif;?>
              </div>
              
                <div class="row">
                  <div class="col-sm-12">
                    <div class="description-block">
                       
                       
                        <b><?php 
switch (Core::$user->language){
 case 'ES': echo "PLACA"; break;
 case 'EN': echo "PLATE"; break;
}
?>: <?php echo $sells->plate; ?></b><br/>
                        <b><?php 
switch (Core::$user->language){
 case 'ES': echo "FICHA"; break;
 case 'EN': echo "FILE"; break;
}
?>: <?php echo $sells->token; ?></b><br/>
                        <b><?php 
switch (Core::$user->language){
 case 'ES': echo "CATEGORIA"; break;
 case 'EN': echo "CATEGORY"; break;
}
?>: <?php echo  strtoupper($sells->getCategory()->name); ?></b></p>
                    </div>
                    <!-- /.description-block -->
                    <a href="./?view=gallery&opt=all&id=<?php echo $sells->id;?>" class="btn btn-sm btn-block btn-secondary">  <i class="fa fa-image"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "GALERIA"; break;
 case 'EN': echo "GALLERY"; break;
}
?>  
                    
                    </a>
                  </div>
                  <!-- /.col -->
                 
                  
                  <!-- /.col -->
                </div>
                <!-- /.row -->
           
              
               
                  
                </div>
            </div>
            <!-- /.widget-user -->
          </div> 
          <?php endforeach;?>
          </div>
        </div>
      
        <!-- /.card-footer -->
      </div>
      <!-- /.card -->

  <?php else:?>
<div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2><?php 
switch (Core::$user->language){
 case 'ES': echo "No hay vehiculo en seleccionado"; break;
 case 'EN': echo "There is no vehicle in selected"; break;
}
?></h2>
    <p><?php 
switch (Core::$user->language){
 case 'ES': echo "No se ha realizado ninguna operacion."; break;
 case 'EN': echo "No operation has been performed."; break;
}
?></p>
    </div>
</div>
  <?php endif; ?>



<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="allbycat"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$ticket_image = StockData::getPrincipal()->ticket_image;
$products = CarsData::getAllByCategoryId2($_GET["id"]);?>

<?php if(count($products)>0):?>
      <!-- Default box -->
      <div class="card card-solid"  style="background-color:#222;">
        <div class="card-body pb-0">
          <div class="row">
            <?php foreach($products as $sells):?>
            <div class="col-12 col-sm-6 col-md-6 d-flex align-items-stretch flex-column">
              <div class="card bg-light d-flex flex-fill">
              <div class="card-header text-muted border-bottom-0">
                  
                </div>

        <form id="form-<?php echo $sells->id; ?>">
                <div class="card-body pt-0">
                  <div class="row">
                    <div class="col-7">
                      <h2 class="lead"><b><?php echo strtoupper($sells->getBrand()->name." ".$sells->name); ?></b></h2>
                      <p class="text-muted text-sm"><b>COLOR: <?php echo strtoupper($sells->getExColor()->name); ?></b><br/>
                        <b><?php 
switch (Core::$user->language){
 case 'ES': echo "PLACA"; break;
 case 'EN': echo "PLATE"; break;
}
?>: <?php echo $sells->plate; ?></b><br/>
                        <b>AÑO: <?php echo $sells->year; ?></b><br/>
                        <b>FICHA: <?php echo $sells->token; ?></b><br/>
                        <b><?php 
switch (Core::$user->language){
 case 'ES': echo "CATEGORIA"; break;
 case 'EN': echo "CATEGORY"; break;
}
?>: <?php echo  strtoupper($sells->getCategory()->name); ?></b></p>
                     
                    </div>
                    <div class="col-5 text-center">
                      <img src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>" style="width:50px; height: 50px; "  class="img-circle img-fluid">
                    </div>
                  </div>
                </div>
          
                <div class="input-group card-header">
        <input type="number" name="price" autocomplete="off" class="form-control" placeholder="Precio x Dia" required>
        <input type="number" name="q" value="1" class="form-control" required>
       <input type="hidden" name="product_id" value="<?php echo $sells->id; ?>">
      </div>
                  <button type="submit" id="mesero-<?php echo $sells->id; ?>" class="btn btn-success btn-block"><i class="fa fa-plus"></i> Carrito  </button>
        </form>

              </div>
            </div>
    <script type="text/javascript">
   $("#form-<?php echo $sells->id; ?>").submit(function(e){
            e.preventDefault();
            $.post("./?action=add&opt=tocart",$("#form-<?php echo $sells->id; ?>").serialize(),function(data){
            $.get("./?action=get&opt=cart","",function(data2){
                $("#cartofsell").html(data2);
            });

            });

        });
    </script>
            <?php endforeach;?>
          </div>
        </div>
      
        <!-- /.card-footer -->
      </div><?php else:?>
  <div class="col-md-12 col-12">
<div class="card" style="background-color:#222;">
              <div class="card-header">
     <h2><?php 
switch (Core::$user->language){
 case 'ES': echo "No hay vehiculo en seleccionado ID#".$_GET["id"]; break;
 case 'EN': echo "There is no vehicle in selected ID#".$_GET["id"]; break;
}
?></h2>
    <p><?php 
switch (Core::$user->language){
 case 'ES': echo "No se ha realizado ninguna operacion."; break;
 case 'EN': echo "No operation has been performed."; break;
}
?></p>
    </div>
</div>
  </div>
  <?php endif; ?>


<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="cart"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$symbol = StockData::getPrincipal()->currency;
$iva_name = StockData::getPrincipal()->imp_name;
$iva_val = StockData::getPrincipal()->imp_val;
$total=0;
?>


<?php foreach($_SESSION["cotization"] as $p):
$product = CarsData::getById($p["product_id"]);
$pt = $p["price"]*$p["q"]; $total +=$pt; ?></b></td>

<?php endforeach;?>
        <!-- ./row -->
        
        <div class="row">
<div class="card col-6 col-sm-6 col-md-7">

<div style="margin-left:3%;">
Total: <?php echo number_format($total,2,'.',','); ?>
</div>
 </div>
 
 <div class="col-6 col-sm-6 col-md-5">
    <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio / <i class='fa fa-th-list'></i> Cotizacion</li>
             
           </ol>
       </div>
</div>


<div class="card" style="background-color:#222;">      
<div class="card-body">
      

    <div class="row">
<form method="post" class="form-horizontal" id="cotizadd" enctype="multipart/form-data">

  <div class="row">
       <div class="col-12 col-sm-12 col-md-9 col-lg-9 col-xl-9">
   
     <label class="control-label">Cliente</label>
       <div class="input-group">
   <select name="person_id" class="form-control select2" required>
      <option value="">-- <?php 
switch (Core::$user->language){
 case 'ES': echo "ELEGIR"; break;
 case 'EN': echo "CHOOSE"; break;
}
?>  --</option>
    <?php foreach(PersonData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id) as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
  <input type="hidden" value="<?php echo $total;?>" class="form-control" autocomplete="off"  name="total">
    </div>
</div>


 <div class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
   
    <div class="input-group" >
                      
                      <label for="inputEmail1" class="col-md-12 col-12 control-label">ITBIS</label>
    <div class="icheck-primary d-inline">
                        <input type="checkbox" name="iva" value="18" id="checkbox2">
                        <label for="checkbox2">
                          18%
                        </label>
                      </div>
    </div>
</div>
  

      <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
    <br/>
     <div class="input-group">
     <a href="./?action=clearcart&opt=all" class="btn btn-warning btn-block "><i class='fa fa-times'></i> Cancelar</a>
      </div>
    </div>

    <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
  <br/>
     <div class="input-group">
     <button class="btn btn-primary btn-block "><i class="fa fa-check"></i> Finalizar</button>
      </div>
    </div>
  </div>
 
</form>


<style type="text/css"> 
.select2.select2-container {
  width: 100% !important;
}

.select2.select2-container .select2-selection {
  border: 1px solid #ccc;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 5px;
  height: 37px;
  margin-bottom: 15px;
  outline: none !important;
  transition: all .15s ease-in-out;
  background: #343a40;
}

.select2.select2-container .select2-selection .select2-selection__rendered {
  color: white;
  line-height: 32px;
  padding-right: 33px;
}

.select2.select2-container .select2-selection .select2-selection__arrow {
  background: #343a40;
  border-left: 1px solid #ccc;
  -webkit-border-radius: 0 3px 3px 0;
  -moz-border-radius: 0 3px 3px 0;
  border-radius: 0 3px 3px 0;
  height: 32px;
  width: 33px;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--single {
  background: #343a40;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--single .select2-selection__arrow {
  -webkit-border-radius: 0 3px 0 0;
  -moz-border-radius: 0 3px 0 0;
  border-radius: 0 3px 0 0;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--multiple {
  border: 1px solid #34495e;
}

.select2.select2-container .select2-selection--multiple {
  height: auto;
  min-height: 34px;
}

.select2.select2-container .select2-selection--multiple .select2-search--inline .select2-search__field {
  margin-top: 0;
  height: 32px;
}

.select2.select2-container .select2-selection--multiple .select2-selection__rendered {
  display: block;
  padding: 0 4px;
  line-height: 29px;
}

.select2.select2-container .select2-selection--multiple .select2-selection__choice {
  background-color: #f8f8f8;
  border: 1px solid #ccc;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  margin: 4px 4px 0 0;
  padding: 0 6px 0 22px;
  height: 24px;
  line-height: 24px;
  font-size: 12px;
  position: relative;
}

.select2.select2-container .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove {
  position: absolute;
  top: 0;
  left: 0;
  height: 22px;
  width: 22px;
  margin: 0;
  text-align: center;
  color: #e74c3c;
  font-weight: bold;
  font-size: 16px;
}

.select2-container .select2-dropdown {
  background: transparent;
  border: none;
  margin-top: -5px;
}

.select2-container .select2-dropdown .select2-search {
  padding: 0;
}

.select2-container .select2-dropdown .select2-search input {
  outline: none !important;
  border: 1px solid #34495e !important;
  border-bottom: none !important;
  padding: 4px 6px !important;
}

.select2-container .select2-dropdown .select2-results {
  padding: 0;
}

.select2-container .select2-dropdown .select2-results ul {
  background: #343a40;
  border: 1px solid #34495e;
}

.select2-container .select2-dropdown .select2-results ul .select2-results__option--highlighted[aria-selected] {
  background-color: #3498db;
}
</style>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-body">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title" id="myModalLabel"><i class="fa fa-user-plus"></i> Crear Cliente</h4>
</div>
<div class="modal-body">
<form method="post" class="form-horizontal" action="./?action=person&opt=addcotiz" enctype="multipart/form-data">
<div class="row">


   
    <div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Foto Cedula</label>
<input type="file"  name="invoice_file">
    </div>

 <div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Foto Pasaporte</label>
<input type="file"  name="passport_file">
    </div>
    
     <div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Foto Licencia</label>
<input type="file"  name="license_file">
    </div>
    
     <div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Foto Residencia</label>
    <input type="file"  name="home_file">
    </div>
    
    
    <div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre Completo</label>
      <input type="text" autofocus name="name" autocomplete="off"  class="form-control" placeholder="Nombre Completo">
    </div>

  <div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Numero de Cedula</label>
      <input type="text"  name="no" autocomplete="off"  class="form-control" placeholder="Numero de Cedula">
    </div>

    <div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Licencia de conducir</label>
      <input type="text"  name="license" autocomplete="off"  class="form-control" placeholder="Licencia de conducir">
    </div>

    <div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Expira</label>
      <input type="date"  name="expirelicense" autocomplete="off"  class="form-control">
    </div>

<div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Emitida</label>
      <input type="text"  name="issuedlicense" autocomplete="off"  class="form-control" placeholder="Donde se emitio">
    </div>


    <div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Pasaporte</label>
      <input type="text"  name="passport" autocomplete="off"  class="form-control" placeholder="Pasaporte">
    </div>


    <div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Nacionalidad</label>
      <input type="text"  name="nationality" autocomplete="off"  class="form-control" placeholder="Nacionalidad">
    </div>


 <div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Correo</label>
      <input type="email"  name="email" autocomplete="off"  class="form-control" placeholder="Correo Electronico">
    </div>

 <div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Telefono (USD)</label>
      <input type="text"  name="phone" autocomplete="off"  class="form-control" placeholder="Telefono (USD)">
    </div>
 <div class="col-md-6 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label">Telefono (DOM)</label>
      <input type="text"  name="phone2" autocomplete="off" class="form-control" placeholder="Telefono (DOM)">
    </div>

                <div class="col-md-12 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"></label>
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
</form>

</div>

</div>
</div>
</div>
<div class="table-responsive">
    <br>
<table class="table table-bordered" id="example2">
<thead>
  <th>Dia</th>
  <th>Vehiculo</th>
  <th>Precio</th>
  <th>Total</th>
  <th ></th>
</thead>
<?php foreach($_SESSION["cotization"] as $p):
$product = CarsData::getById($p["product_id"]);
?>
<tr >
  <td ><?php echo $p["q"]; ?></td>
  <td><?php echo strtoupper($product->getBrand()->name." ".$product->name." ".$product->getExColor()->name." [".$product->token."] ".$product->year); ?></td>
  <td><b><?php echo number_format($p["price"],2,".",","); ?></b></td>
  <td><b><?php  echo $p["price"]*$p["q"]; ?></b></td>
  <td>

      <a href="./?action=clearcart&opt=all&product_id=<?php echo $product->id; ?>" class="btn btn-danger"><i class="fa fa-trash"></i></a>
  </td>
</tr>

<?php endforeach; ?>
</table>
</div>
          
            </div>
          </div>
        </div>
 </div>
</div>
    </div>     


<script>

    $("#cotizadd").submit(function(e){
    client = $("#client_id").val();
    if(client!=""){   
      e.preventDefault();
        $.post("./?action=cotization&opt=add",$("#cotizadd").serialize(),function(data){
          $.get("./?view=cartof&opt=sell",null,function(data2){
            $("#cartoxsell").html(data);
            $("#show_search_results").html("");
          });
         $.jGrowl("Cotizacion procesada exitosamente!", { header: 'Acceso permitido' });
      e.preventDefault();
        });

  }else{
    $.jGrowl("Campo de cliente vacio", { header: 'Acceso denegado' });
    e.preventDefault();
  }
  });
            </script>




<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="chat"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

                <div class="card direct-chat direct-chat-info" style="background-color: #333;">
                  <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-friends "></i> ASISTENTE</h3>
 <a onclick="enfocarInput()" data-widget="control-sidebar" class="float-right text-white" data-slide="true" href="#" role="button"><i class="fas fa-times"></i></a>
                  </div>
                  <div class="card-footer">
                    <form id="form-chat" method="post">
                      <div class="input-group">
                        <input id="myInput" type="text" name="name" placeholder="Que Necesitas?" required class="form-control">
                        <input type="hidden" name="created_at" value="<?php echo date("d-m-Y h:i:s a");?>" class="form-control">
                        <button type="submit" class=" form-control col-md-2" style="background-color:orange;"><i class="fa fa-search"></i></button>
                      </div>
                    </form>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body" >
                    <!-- Conversations are loaded here -->
                    <div class="direct-chat-messages"  style="height:375px;">
                      <!-- Message. Default to the left -->
                      
<?php foreach(array_reverse($_SESSION["chat"]) as $p): ?>
                      <div class="direct-chat-msg">
                        <div class="direct-chat-infos clearfix">
                          <span class="direct-chat-name float-left"><?php echo $users->name." ".$users->lastname;?></span>
                          <span class="direct-chat-timestamp float-right"><?php echo $p["created_at"];?></span>
                        </div>
                        <!-- /.direct-chat-infos -->
                        <img class="direct-chat-img" src="CF-SYSTEMS/storage/profiles/man.png">
                        <!-- /.direct-chat-img -->
                        <div class="direct-chat-text">
                        ¿<?php echo $p["name"];?>
                        </div>
                        <!-- /.direct-chat-text -->
                      </div>
                      <!-- /.direct-chat-msg -->

                      <!-- Message to the right -->
                      <div class="direct-chat-msg right">
                        <div class="direct-chat-infos clearfix">
                          <span class="direct-chat-name float-right">Assanpos</span>
                          <span class="direct-chat-timestamp float-left"><?php echo $p["created_at"];?></span>
                        </div>
                        <!-- /.direct-chat-infos -->
                        <img class="direct-chat-img" src="CF-SYSTEMS/storage/configuration/assanpos.png" alt="message user image">
                        <!-- /.direct-chat-img -->
                        <div class="direct-chat-text" style="background-color:orange; border-color:white; ">
                       <?php echo nl2br(htmlspecialchars($p["lastname"]));?>
                        </div>
                        <?php if(!empty($p["url"])):?>
                        <div class="direct-chat-text" style="background-color:white;">
                         <a  href="./<?php echo $p["url"];?>"><i class="fa fa-eye"></i> VER MODULO</a>
                        </div><!-- /.direct-chat-text -->
                      </div>
                      <?php endif;?>
                      <!-- /.direct-chat-msg -->
                    
<?php endforeach;  if(!isset($_SESSION["chat"])):?> 
<!-- Message to the right -->
                      <div class="direct-chat-msg right">
                        <div class="direct-chat-infos clearfix">
                          <span class="direct-chat-name float-right">Assanpos</span>
                          <span class="direct-chat-timestamp float-left"><?php echo $p["created_at"];?></span>
                        </div>
                        <!-- /.direct-chat-infos -->
                        <img class="direct-chat-img" src="CF-SYSTEMS/storage/configuration/rentals.png" alt="message user image">
                        <!-- /.direct-chat-img -->
                        <div class="direct-chat-text"  style="background-color:orange; border-color:white; ">
                     
                     <button class="btn-info" style="border-color:transparent; background-color:orange;">¿Como podemos ayudarte?</button>
                        <input type="hidden" name="name" value="<?php echo $xchat->ask;?>" class="form-control">
                        <input type="hidden" name="created_at" value="<?php echo date("d-m-Y h:i:s a");?>" class="form-control">
                    
                   
                        </div>
                        <!-- /.direct-chat-text -->
                      </div>
  
        
<?php endif; ?>

                    <!-- /.direct-chat-pane -->
                  </div>
                  <!-- /.card-body -->
                  </div>
                <!--/.direct-chat -->
                

<script type="text/javascript">
               $("#form-chat").submit(function(e){
            e.preventDefault();
            $.post("./?action=add&opt=tochat",$("#form-chat").serialize(),function(data){
            $.get("./?action=get&opt=chat","",function(data2){
                $("#cartofsell").html(data2);
            });

            });


        });



function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
      closeAllLists(e.target);
  });
}

/*An array containing all the country names in the world:*/
var countries = ["<?php foreach (ChatData::getAll() as $mk):?><?php echo $mk->ask; ?>","<?php endforeach; ?>"];

/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
autocomplete(document.getElementById("myInput"), countries);
</script>

<style type="text/css">


/*the container must be positioned relative:*/
.autocomplete {
  position: relative;
  display: inline-block;
}

input {
  border: 1px solid transparent;
  background-color: #343a40;
  padding: 10px;
  font-size: 16px;
}

input[type=text] {
  background-color: #343a40;
  width: 100%;
}

input[type=submit] {
  background-color: DodgerBlue;
  color: #fff;
  cursor: pointer;
}

.autocomplete-items {
  position: absolute;
  border: 1px solid #343a40;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #343a40; 
  border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #383f45; 
}

/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}
</style>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="products"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$go = $_GET["go"];
$search=$_GET["product_name"]; ?>

<?php $base = new Database();
$con = $base->connect();
if(!empty($search)):
$sql = "select SQL_BIG_RESULT * from products where (p_code like '%$search%' or hsn_code like '%$search%' or p_name like '%$search%')";
else:
$sql = "select SQL_BIG_RESULT * from products ";   
endif;
$query = $con->query($sql);
if(count($query)>0):?>


<div class="card"  style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
  <thead>
    <th>Accion</th>
    <th>Codigo</th>
    <th>Nombre</th>
    <th>Marca</th>
    <th>Compra RD$</th>
    <th>Compra USD$</th>
    <th>Tasa USD$</th>
    <th>Disponible</th>
    <th>Accion</th>
  </thead>

  <tfoot>
    <tr>
    <th>Accion</th>
    <th>Codigo</th>
    <th>Nombre</th>
    <th>Marca</th>
    <th>Compra RD$</th>
    <th>Compra USD$</th>
    <th>Tasa USD$</th>
    <th>Disponible</th>
    <th>Accion</th>
  </tr>
</tfoot>

<?php while($r = $query->fetch_array()): 
$q = ProductData::getByStoreId($r['p_id']); 

if($q->store_id == $_GET["stock"]):?>

  <tr>

        <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm">
                      
                        <a href="./?view=inventory&opt=all&id=<?php echo $r['p_id'];?>" class="btn btn-warning"><i class="fas fa-edit"></i> EDITAR </a>
                        
                      </div>
        </td>
    <td><?php echo $r['p_code']; ?></td>
  <td><?php echo $r['p_name']; ?></td>
    <td><?php if($r['brand_id']!=null){echo BrandData::getById($q->brand_id)->name;}  ?></td>
    <td><?php echo $currency; ?> <?php echo number_format($q->purchase_price,0,'.',','); ?></td>
    <td><?php echo $currency; ?> <?php echo number_format($q->usd_price,2,'.',','); ?></td>
    <td><?php echo $currency; ?> <?php echo number_format($q->tasa_dolar,2,'.',','); ?></td>

    <td><?php echo number_format($q->quantity_in_stock,0,'.',','); ?></td>
   
<td class="text-right py-0 align-middle">
            
<?php  
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()):
if ($x['permits_id']==4): ?>
   
                        <a href="./?action=products&opt=del&id=<?php echo $r['p_id'];?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();" ><i class="fas fa-trash"> Eliminar</i></a>
                    
                    
                     <script>
function confirmDelete() {
    return confirm("¿Estás seguro de que deseas eliminar este registro?");
}
</script>
    <?php endif; endwhile; ?>
</td>
    </tr>
    
    <?php endif; endwhile; ?>
      </table>
  </div><!-- /.box-body -->
</div>
</div> 
</div>
</div>  

<script type="text/javascript">
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6 col-6:eq(0)');
</script>

  <?php else:?><br>
  <div class="card"  style="background-color:#222;">
              <div class="card-header">
    <h2>No hay productos</h2>
    <p>No se han agregado productos a la base de datos, puedes agregar uno dando click en el boton <b>"Agregar Producto"</b>.</p>
  </div>
</div>
  <?php endif;?>
<?php endif;?>