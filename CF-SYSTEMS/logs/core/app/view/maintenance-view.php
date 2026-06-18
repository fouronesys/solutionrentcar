<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):
////////////////////////////////////////////////////////// INCOME /////////////////////////////////
?>
<section class="content">
  <div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <a href="./?view=maintenance&opt=new" style="color: white;"> <h1 class="m-0"><i class="fa fa-plus"></i> Lista de Mantenimientos</a></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-briefcase'></i> Finanzas</a></li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Mantenimientos</a></li>

            </ol>
          </div><!-- /.col -->

    </div>


     <div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo y en en titulo del aparatado para crear nuevo.
            </div>

            
<?php $users = MaintenanceData::getAll();
    if(count($users)>0):?>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
    <thead>
      <th>Accion</th>
      <th>Vehiculo</th>
      <th>Mantenimiento</th>
      <th>Usuario</th>
      <th>Monto</th>
      <th>Fecha</th>
      <th>Accion</th>
    </thead>

    <tfoot>
      <tr>
      <th>Accion</th>
      <th>Vehiculo</th>
      <th>Mantenimiento</th>
      <th>Usuario</th>
      <th>Monto</th>
      <th>Fecha</th>
      <th>Accion</th>
      </tr>
    </tfoot>

      <?php foreach($users as $user): ?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=maintenance&opt=edit&id=<?php echo $user->id;?>" class="btn btn-success"><i class="fas fa-edit"></i></a>
                      </div>
        </td>
        <td><?php $brand = BrandData::getById($user->getCars()->brand_id); $cars = CarsData::getById($user->getCars()->id); echo $brand->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name." - ".$cars->chassis; ?></td>
        <td><?php echo $user->maintenance; ?></td>
        <td><?php echo $user->getUser()->name." ".$user->getUser()->lastname; ?></td>
        <td><?php echo number_format($user->total,2,".",","); ?></td>
        <td> <?php echo  date("d-m-Y",strtotime($user->created_at));?></td>
        <td class="text-right py-0 align-middle">
<?php $permissions = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);

foreach($permissions as $permission):
if ($permission->permits_id==4 and $permission->user_id==$_SESSION["user_id"]): ?>
   
                        <a href="./?action=maintenance&opt=del&id=<?php echo $user->id;?>" class="btn btn-danger btn-block btn-sm"><i class="fas fa-trash"> Eliminar</i></a>
                    
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
     
         <div class="card">
              <div class="card-header">
    <h2>No hay Mantenimiento</h2>
    <p>No se ha realizado ninguna operacion.</p>
    </div>
</div>
  
   <?php endif;?>



  </div>
</div>
    </div><!-- /.row -->
      </div><!-- /.container-fluid -->
<script type="text/javascript">
    $("#example1").DataTable();
</script>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="new"):
//////////////////////////////////////////////////////////// NEWINCOME ///////////////////////////
?>
        <section class="content">
<div class="row">
  <div class="col-md-12">
 
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-plus"></i> Nuevo Mantenimiento</a></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-briefcase'></i> Finanzas</a></li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Mantenimientos</a></li>

            </ol>
          </div><!-- /.col -->

    </div>

<div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Los Campos con (*) son requeridos los demas lo puedes dejar vacio en caso de no necesitar.
            </div>
    <div class="card">
<div class="card-body">
    <form class="form-horizontal" method="post" id="addmaintenance" role="form">

<div class="row">

    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vehiculo</label>
      <?php $clients = CarsData::getAll();?>
    <select name="car_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->getBrand()->name." ".$client->name." ".$client->year." ".$client->getExColor()->name." - ".$client->chassis;?></option>
    <?php endforeach;?>
      </select>
    </div>


    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Usuario</label>
      <?php $clients = UserData::getAll();?>
    <select name="user_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name." ".$client->lastname;?></option>
    <?php endforeach;?>
      </select>
    </div>


    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Monto Pagado</label>
    <input type="text" name="total" id="inputFormatoNumerico" class="form-control" placeholder="Monto Pagado">
    </div>
  
     <div class="col-md-12 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 control-label">Que se le Hizo?</label>
     <input id="placein" type="text" class="form-control" autocomplete="off"  name="maintenance" placeholder="Que se le Hizo?">
    </div>

</div>
 <div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=maintenance&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
  
</form>

<script type="text/javascript">
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
var placein = ["<?php foreach (TMData::getAll() as $client):?><?php echo $client->name; ?>","<?php endforeach; ?>"];

/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
autocomplete(document.getElementById("placein"), placein);
autocomplete(document.getElementById("placeout"), placeout);
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

  </div>
</div>
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
</div>
</div>
</div>
</div>
<script>
   Number.prototype.format = function(n, x, s, c) {
                let re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
                    num = this.toFixed(Math.max(0, ~~n));
                return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
            };
            // Restricts input for the given textbox to the given inputFilter.
            function setInputFilter(textbox, inputFilter) {
                ["input"].forEach(function(event) {
                    textbox.addEventListener(event, function() {
                        if (this.id === "inputFormatoNumerico") {
                            if (this.value !== "") {
                                let str = this.value;
                                let oldstr= str.substring(0, str.length - 1);
                                let millares = ",";
                                let decimales = ".";
                                str = str.split(millares).join("");
                                if (isNaN(str)) {
                                    this.value = oldstr;
                                } else {
                                    let numero = parseInt(str);
                                    this.value = numero.format(0, 3, millares, decimales);
                                }
                            }
                        }

                     
                    });
                });
            }
            setInputFilter(document.getElementById("inputFormatoNumerico"), function(value) {
                //declare an object RegExp
                let regex = new RegExp(/^-?\d*$/);
                //test the regexp
                return regex.test(value);
            });
            jQuery(document).ready(function(){
            jQuery("#addmaintenance").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=maintenance&opt=add",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Mantenimientos Exito!", { sticky: true });
                  $.jGrowl("Se Agrego a la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=maintenance&opt=all'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Agregar' });
                  }
                  }
                });
                return false;
              });
            });
            </script>

</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"):
/////////////////////////////////////////////////////////// EDITINCOME ///////////////////////////
$user = MaintenanceData::getById($_GET["id"]);?>

        <section class="content">
<div class="row">
  <div class="col-md-12">
   
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-edit"></i> Editar Mantenimiento</a></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-briefcase'></i> Finanzas</a></li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Mantenimientos</a></li>

            </ol>
          </div><!-- /.col -->

    </div>

<div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Este apartado ha sido mejorado para su impresión. Los Campos con (*) son requeridos los demas lo puedes dejar vacio en caso de no necesitar.
            </div>
    <div class="card">
<div class="card-body">
   <form class="form-horizontal" method="post" id="updmaintenance" role="form">

<div class="row">

    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vehiculo</label>
      <?php $clients = CarsData::getAll();?>
    <select name="car_id" class="form-control" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>" <?php if($user->car_id!=null&& $user->car_id==$client->id){ echo "selected";}?>><?php echo $client->getBrand()->name." ".$client->name." ".$client->year." ".$client->getExColor()->name." - ".$client->chassis;?></option>
    <?php endforeach;?>
      </select>
    </div>


    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Usuario</label>
      <?php $clients = UserData::getAll();?>
    <select name="user_id" class="form-control" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>" <?php if($user->user_id!=null&& $user->user_id==$client->id){ echo "selected";}?>><?php echo $client->name." ".$client->lastname;?></option>
    <?php endforeach;?>
      </select>
    </div>


    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Monto Pagado</label>
    <input type="text" name="total" value="<?php echo $user->total;?>" id="inputFormatoNumerico" class="form-control" placeholder="Monto Pagado">
    </div>
  
     <div class="col-md-12 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 control-label">Que se le Hizo?</label>
    <textarea name="maintenance" rows="2" class="form-control" required placeholder="Descripcion de lo que se le hizo al Vehiculo"><?php echo $user->maintenance;?></textarea>
    </div>

</div>
 <div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=maintenance&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                  <input type="hidden" name="id" value="<?php echo $user->id;?>">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
  
</form>
  </div>
</div>
</div>
</div>
</div>
</div>
<script>
   Number.prototype.format = function(n, x, s, c) {
                let re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
                    num = this.toFixed(Math.max(0, ~~n));
                return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
            };
            // Restricts input for the given textbox to the given inputFilter.
            function setInputFilter(textbox, inputFilter) {
                ["input"].forEach(function(event) {
                    textbox.addEventListener(event, function() {
                        if (this.id === "inputFormatoNumerico") {
                            if (this.value !== "") {
                                let str = this.value;
                                let oldstr= str.substring(0, str.length - 1);
                                let millares = ",";
                                let decimales = ".";
                                str = str.split(millares).join("");
                                if (isNaN(str)) {
                                    this.value = oldstr;
                                } else {
                                    let numero = parseInt(str);
                                    this.value = numero.format(0, 3, millares, decimales);
                                }
                            }
                        }

                     
                    });
                });
            }
            setInputFilter(document.getElementById("inputFormatoNumerico"), function(value) {
                //declare an object RegExp
                let regex = new RegExp(/^-?\d*$/);
                //test the regexp
                return regex.test(value);
            });
            jQuery(document).ready(function(){
            jQuery("#updmaintenance").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=maintenance&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Mantenimientos Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=maintenance&opt=all'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Actualizo' });
                  }
                  }
                });
                return false;
              });
            });
            </script>


</section>


<?php endif; ?>