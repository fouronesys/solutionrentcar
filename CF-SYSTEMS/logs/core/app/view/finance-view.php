<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):
////////////////////////////////////////////////////////// SPENDS /////////////////////////////
  ?>
<section class="content">
  <div class="row">
  <div class="col-md-12">
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
        <?php if ($_GET["spends"]=="Negocio"):?>
           <h1 class="m-0"><i class="fa fa-minus-square"></i> Gastos del Negocio</h1>
        <?php elseif ($_GET["spends"]=="Peajes"):?>
           <h1 class="m-0"><i class="fa fa-minus-square"></i> Gastos del Peajes</h1>
        <?php elseif ($_GET["spends"]=="Combustible"):?>
           <h1 class="m-0"><i class="fa fa-minus-square"></i> Gastos del Combustible</h1>
        <?php elseif ($_GET["spends"]=="Mantenimiento"):?>
           <h1 class="m-0"><i class="fa fa-minus-square"></i> Gastos del Mantenimiento</h1> 
         <?php elseif ($_GET["spends"]=="Oil"):?>
          <h1 class="m-0"><i class="fa fa-minus-square"></i> Gastos del Cambio de Aceite</h1> 
            <?php elseif ($_GET["spends"]=="Otros"):?>
           <h1 class="m-0"><i class="fa fa-minus-square"></i> Otros Gastos</h1> 
        <?php endif;?>
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

        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <div class="col-md-12">
            <!-- MAP & BOX PANE -->
       <div class="callout callout-warning" style="background-color:#222;">
              <h5><i class="fas fa-info"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Nota"; break;
 case 'EN': echo "Note"; break;
}
?>:</h5>
             <?php 
switch (Core::$user->language){
 case 'ES': echo " Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo."; break;
 case 'EN': echo "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; break;
}
?>
            </div>
            <div class="card" style="background-color:#222;">
              <div class="card-header">
                <h3 class="card-title">Gastos del  <?php if ($_GET["spends"]=="Negocio"):?>Negocio<?php elseif ($_GET["spends"]=="Peajes"):?>Peaje<?php elseif ($_GET["spends"]=="Combustible"):?>Combustible<?php elseif ($_GET["spends"]=="Mantenimiento"):?>Mantenimiento<?php elseif ($_GET["spends"]=="Oil"):?>Cambio de Aceite<?php endif;?></h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div class="d-md-flex">
                  <div class="p-1 flex-fill" style="overflow: hidden">
                    <!-- Map will be created here -->
                    <div id="world-map-markers" style="overflow: hidden">
                      <div class="col-md-12"><form id="filterspends">
  <input style="background-color:#222;" type="hidden" name="view" value="filterspends">
<div class="row">

  <div class="col-6 col-sm-3 col-md-3 col-lg-3 col-xl-3">
 
    <label>Sucursal:</label>
    <select style="background-color:#333;" name="stock_id" class="form-control">
      <option value="">-- Sucursal--</option>
      <?php foreach(StockData::getAllbySQL("where id=".StockData::getPrincipal()->id) as $stock):?>
        <option value="<?php echo $stock->id; ?>"><?php echo $stock->name; ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
    
    <label>Fecha inicio:</label>
    <input style="background-color:#222;" type="date" name="start_at" value="<?php echo date("Y-m-d", strtotime('-6 hours')); ?>" required class="form-control">
  </div>
  <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
 
    <label>Fecha fin:</label>
    <input style="background-color:#222;" type="date" name="finish_at" value="<?php echo date("Y-m-d", strtotime('-6 hours')); ?>" required class="form-control">
  </div>
  <div class="col-1 col-sm-1 col-md-1 col-lg-1 col-xl-1">
 
   <label>Buscar</label>
    <button type="submit" class="btn btn-warning"> <i class="fa fa-search"></i></button>
  </div>

</div>
</form>

<div class="allfilterspends"></div>

</div>
                    </div>
                  </div>
                 
                </div><!-- /.d-md-flex -->
              </div>
              <!-- /.card-body -->
            </div>
           

          </div>
          <!-- /.col -->

          <!-- /.col -->
        </div>




  </div>
</div>
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
</section>
 <?php if ($_GET["spends"]=="Negocio"):?>
<script type="text/javascript">
  $(document).ready(function(){
    $.get("./?action=filter&opt=spends",$("#filterspends").serialize(),function(data){
      $(".allfilterspends").html(data);
    });

    $("#filterspends").submit(function(e){
      e.preventDefault();
    $.get("./?action=filter&opt=spends",$("#filterspends").serialize(),function(data){
      $(".allfilterspends").html(data);
    });

    })
  });
</script>
<?php elseif ($_GET["spends"]=="Otros"):?>
<script type="text/javascript">
  $(document).ready(function(){
    $.get("./?action=filter&opt=Otros",$("#filterspends").serialize(),function(data){
      $(".allfilterspends").html(data);
    });

    $("#filterspends").submit(function(e){
      e.preventDefault();
    $.get("./?action=filter&opt=Otros",$("#filterspends").serialize(),function(data){
      $(".allfilterspends").html(data);
    });

    })
  });
</script>
<?php endif; elseif(isset($_GET["opt"]) && $_GET["opt"]=="new"):
///////////////////////////////////////////////////////// NEWSPENDS ///////////////////////////
?>
<section class="content">
<div class="row">
  <div class="col-md-12">
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-minus-square"></i> Nuevo <?php if ($_GET["spends"]=="negocio"):?>Negocio<?php elseif ($_GET["spends"]=="other"):?>Otros Gastos<?php endif;?></a></h1>
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
<div class="callout callout-warning" style="background-color:#222;">
              <h5><i class="fas fa-info"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Nota"; break;
 case 'EN': echo "Note"; break;
}
?>:</h5>
             <?php 
switch (Core::$user->language){
 case 'ES': echo " Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo."; break;
 case 'EN': echo "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; break;
}
?>
            </div>
    <div class="card" style="background-color:#222;">
<div class="card-body">
    <form class="form-horizontal" method="post" id="addfspends" role="form">
<?php if ($_GET["spends"]=="negocio"):?>
 <div class="col-md-12">

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Proveedor <span class="text-danger">*</span></label>
    <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-user"></i></span>
   <input style="background-color:#222;" type="text" name="client_id" autocomplete="off" class="form-control"  placeholder="Proveedor"> </div>
   </div>

    <div class="col-md-6 col-12">
 <label for="inputEmail1" class="col-md-12 col-12 control-label">No. Comprobante</label>
     <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-barcode"></i></span>
      <input style="background-color:#222;" type="text" name="voucher_code" autocomplete="off" class="form-control"  placeholder="No. Comprobante" data-inputmask='"mask": "9999999999"' data-mask>
    </div>
 </div>
  </div>
  
<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vencimiento comprobante</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-calendar"></i></span>
      <input style="background-color:#222;" type="date" name="created_date" value="<?php echo date("Y-m-d", strtotime('-6 hours')); ?>"  class="form-control">
    </div>
</div>
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">No. Factura</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-print"></i></span>
      <input style="background-color:#222;" type="number" name="invoice_code" autocomplete="off"  class="form-control"  placeholder="No. Factura">
    </div>
  </div>
</div>
<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha del Gasto</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-calendar"></i></span>
      <input style="background-color:#222;" type="date" name="created_at"  value="<?php echo date("Y-m-d", strtotime('-6 hours')); ?>" required class="form-control" >
    </div>
</div>
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Forma Pago</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-th-list"></i></span>
    <select style="background-color:#333;" required name="f_id" class="form-control">
    <?php foreach(FData::getAll() as $f):?>
      <option value="<?php echo $f->id;?>"><?php echo $f->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
 </div>
</div>

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vencimiento de Pago</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-calendar"></i></span>
    <input style="background-color:#222;" type="date" name="expiry_spend" autocomplete="off" value="<?php echo date("Y-m-d", strtotime('-6 hours')); ?>"  class="form-control" id="name">
    </div>
</div>

    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Tipo de Gasto</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-th-list"></i></span>
    <select style="background-color:#333;" name="type_g" class="form-control">
    <?php foreach(TGData::getAll() as $t):?>
      <option value="<?php echo $t->id;?>"><?php echo $t->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
  </div>
 </div>

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Impuesto Sobre la Renta</label>
     <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-download"></i></span>
      <input style="background-color:#222;" type="tel" name="imp_rent" autocomplete="off" value="0" class="form-control" placeholder="Impuesto Sobre la Renta">
    </div>
</div>

     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12  control-label">ITBIS Retenido</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-spinner"></i></span>
      <input style="background-color:#222;" type="tel" name="itbis_ret" autocomplete="off" value="0" class="form-control" placeholder="ITBIS Retenido">
    </div>
  </div>
</div>

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Tipo de Retencion ISR</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-th-list"></i></span>
    <select style="background-color:#333;" name="type_sg" class="form-control">
    <option value="">Ninguno</option>
    <?php foreach(SGData::getAll() as $s):?>
      <option value="<?php echo $s->id;?>"><?php echo $s->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
</div>

 
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Concepto <span class="text-danger">*</span></label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-clone"></i></span>
      <input style="background-color:#222;" type="text" name="name" autocomplete="off" required class="form-control" id="name" placeholder="Concepto">
    </div>
  </div>
</div>

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Monto Pagado <span class="text-danger">*</span></label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
      <input style="background-color:#222;" type="tel" autocomplete="off" name="price" required class="form-control"  placeholder="Monto Pagado" id="inputFormatoNumerico">
    </div>
   </div>

    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Pago</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-th-list"></i></span>
    <select style="background-color:#333;" required name="p_id" class="form-control">
    <?php foreach(PData::getAll() as $p):?>
      <option value="<?php echo $p->id;?>"><?php echo $p->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
  </div>
</div>

  <div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=finance&opt=all&spends=Negocio" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
<?php elseif ($_GET["spends"]=="other"):?>
 <div class="col-md-12">

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha del Gasto</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-calendar"></i></span>
      <input style="background-color:#222;" type="date" name="created_at"  value="<?php echo date("Y-m-d"); ?>" required class="form-control" >
    </div>
</div>
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Forma Pago</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-th-list"></i></span>
    <select style="background-color:#333;" required name="f_id" class="form-control">
    <?php foreach(FData::getAll() as $f):?>
      <option value="<?php echo $f->id;?>"><?php echo $f->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
 </div>

 
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Concepto <span class="text-danger">*</span></label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-clone"></i></span>
      <input style="background-color:#222;" type="text" name="name" autofocus autocomplete="off" required class="form-control" id="name" placeholder="Concepto">
    </div>
  </div>

     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Monto Pagado <span class="text-danger">*</span></label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
      <input style="background-color:#222;" type="tel" autocomplete="off" name="price" required class="form-control"  placeholder="Monto Pagado" id="inputFormatoNumerico">
    </div>
   </div>

    
</div>

  <div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=finance&opt=all&spends=Otros" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>

<?php endif;?>
</form>
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
</div>
</section>

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
</script>
<?php if ($_GET["spends"]=="negocio"):?>
<script>
            jQuery(document).ready(function(){
            jQuery("#addfspends").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=finance&opt=addspend",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Gastos Exito!", { sticky: true });
                  $.jGrowl("Se Agrego a la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=finance&opt=all&spends=Negocio'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Agregar' });
                  }
                  }
                });
                return false;
              });
            });
</script>
<?php elseif ($_GET["spends"]=="other"):?>
<script>
            jQuery(document).ready(function(){
            jQuery("#addfspends").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=finance&opt=addother",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Gastos Exito!", { sticky: true });
                  $.jGrowl("Se Agrego a la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=finance&opt=all&spends=Otros'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Agregar' });
                  }
                  }
                });
                return false;
              });
            });
</script>

<?php endif; elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"):
/////////////////////////////////////////////////////////// EDITSPENDS ///////////////////////////
?>
<section class="content">
<div class="row">
  <div class="col-md-12">
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-edit"></i> Editar 
<?php if ($_GET["spends"]=="negocio"): echo "Negocio"; $user = SpendData::getById($_GET["id"]); 
elseif($_GET["spends"]=="toll"): echo "Peajes"; $user = TollData::getById($_GET["id"]); 
elseif($_GET["spends"]=="fuel"): echo "Combustible"; $user = FuelsData::getById($_GET["id"]); 
elseif($_GET["spends"]=="maintenance"): echo "Mantenimiento"; $user = MaintenanceData::getById($_GET["id"]); 
elseif ($_GET["spends"]=="other"): echo "Otros Gastos"; $user = SpendData::getById($_GET["id"]); 
endif;?></a></h1>
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
<div class="callout callout-warning" style="background-color:#222;">
              <h5><i class="fas fa-info"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Nota"; break;
 case 'EN': echo "Note"; break;
}
?>:</h5>
             <?php 
switch (Core::$user->language){
 case 'ES': echo " Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo."; break;
 case 'EN': echo "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; break;
}
?>
            </div>
    <div class="card" style="background-color:#222;">
<div class="card-body">
    <form class="form-horizontal" method="post" id="updfspends" role="form">
<?php if ($_GET["spends"]=="negocio"):?>
 <div class="col-md-12">

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Proveedor <span class="text-danger">*</span></label>
    <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-user"></i></span>
    <select style="background-color:#333;" name="client_id" required class="form-control">
    <?php foreach(PersonData::getProviders() as $client):?>
       <option value="<?php echo $client->id;?>" <?php if($user->person_id!=null&& $user->person_id==$client->id){ echo "selected";}?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
   </div>

    <div class="col-md-6 col-12">
 <label for="inputEmail1" class="col-md-12 col-12 control-label">No. Comprobante</label>
     <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-barcode"></i></span>
      <input style="background-color:#222;" type="text" name="voucher_code" autocomplete="off" value="<?php echo $user->voucher_code;?>"  class="form-control"  placeholder="No. Comprobante" data-inputmask='"mask": "9999999999"' data-mask>
    </div>
 </div>
  </div>
  
<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vencimiento comprobante</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-calendar"></i></span>
      <input style="background-color:#222;" type="date" name="created_date" value="<?php echo $user->created_date; ?>"  class="form-control">
    </div>
</div>
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">No. Factura</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-print"></i></span>
      <input style="background-color:#222;" type="number" name="invoice_code" autocomplete="off"   value="<?php echo $user->invoice_code;?>" class="form-control"  placeholder="No. Factura">
    </div>
  </div>
</div>
<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha del Gasto</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-calendar"></i></span>
      <input style="background-color:#222;" type="date" name="created_at" value="<?php echo $user->created_at; ?>" required class="form-control" >
    </div>
</div>
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Forma Pago</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-th-list"></i></span>
    <select style="background-color:#333;" required name="f_id" class="form-control">
    <?php foreach(FData::getAll() as $f):?>
     <option value="<?php echo $f->id;?>" <?php if($user->f_id!=null&& $user->f_id==$f->id){ echo "selected";}?>><?php echo $f->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
 </div>
</div>

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vencimiento de Pago</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-calendar"></i></span>
    <input style="background-color:#222;" type="date" name="expiry_spend" autocomplete="off" value="<?php echo $user->expiry_spend; ?>"  class="form-control" id="name">
    </div>
</div>

    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Tipo de Gasto</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-th-list"></i></span>
    <select style="background-color:#333;" name="type_g" class="form-control">
    <?php foreach(TGData::getAll() as $t):?>
     <option value="<?php echo $t->id;?>" <?php if($user->type_g!=null&& $user->type_g==$t->id){ echo "selected";}?>><?php echo $t->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
  </div>
 </div>

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Impuesto Sobre la Renta</label>
     <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-download"></i></span>
      <input style="background-color:#222;" type="text" name="imp_rent" autocomplete="off" value="<?php echo $user->imp_rent;?>" class="form-control" placeholder="Impuesto Sobre la Renta">
    </div>
</div>

     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">ITBIS Retenido</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-spinner"></i></span>
      <input style="background-color:#222;" type="text" name="itbis_ret" autocomplete="off" value="<?php echo $user->itbis_ret;?>" class="form-control" placeholder="ITBIS Retenido">
    </div>
  </div>
</div>

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Tipo de Retencion ISR</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-th-list"></i></span>
    <select style="background-color:#333;" name="type_sg" class="form-control">
    <option value="">Ninguno</option>
    <?php foreach(SGData::getAll() as $s):?>
      <option value="<?php echo $s->id;?>" <?php if($user->type_sg!=null&& $user->type_sg==$s->id){ echo "selected";}?>><?php echo $s->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
</div>

 
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Concepto <span class="text-danger">*</span></label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-clone"></i></span>
      <input style="background-color:#222;" type="text" name="name" autocomplete="off" required class="form-control" value="<?php echo utf8_decode($user->name);?>" placeholder="Concepto">
    </div>
  </div>
</div>

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Monto Pagado <span class="text-danger">*</span></label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
      <input style="background-color:#222;" type="text" autocomplete="off" name="price" required class="form-control" value="<?php echo $user->price;?>"  placeholder="Monto Pagado" id="inputFormatoNumerico">
    </div>
   </div>

    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Pago</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-th-list"></i></span>
    <select style="background-color:#333;" required name="p_id" class="form-control">
    <?php foreach(PData::getAll() as $p):?>
      <option value="<?php echo $p->id;?>" <?php if($user->p_id!=null&& $user->p_id==$p->id){ echo "selected";}?>><?php echo $p->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
  </div>
</div>

  <div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=finance&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                  <input style="background-color:#222;" type="hidden" name="user_id" value="<?php echo $user->id;?>">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
              
<?php elseif ($_GET["spends"]=="other"):?>
 <div class="col-md-12">

<div class="row">
     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha del Gasto</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-calendar"></i></span>
      <input style="background-color:#222;" type="date" name="created_at"  value="<?php echo $user->created_at; ?>" required class="form-control" >
    </div>
</div>
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Forma Pago</label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-th-list"></i></span>
    <select style="background-color:#333;" required name="f_id" class="form-control">
    <?php foreach(FData::getAll() as $f):?>
       <option value="<?php echo $f->id;?>" <?php if($user->f_id!=null&& $user->f_id==$f->id){ echo "selected";}?>><?php echo $f->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
 </div>

 
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Concepto <span class="text-danger">*</span></label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-clone"></i></span>
      <input style="background-color:#222;" type="text" name="name"  autocomplete="off" value="<?php echo $user->name; ?>" required class="form-control" id="name" placeholder="Concepto">
    </div>
  </div>

     
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Monto Pagado <span class="text-danger">*</span></label>
         <div class="input-group">
<span class="input-group-text" style="background-color:orange;"><i class="fa fa-asterisk"></i></span>
      <input style="background-color:#222;" type="tel" autocomplete="off" name="price" value="<?php echo $user->price; ?>" required class="form-control"  placeholder="Monto Pagado" id="inputFormatoNumerico">
    </div>
   </div>

    
</div>

  <div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=finance&opt=all&spends=Otros" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                       <input style="background-color:#222;" type="hidden" name="user_id" value="<?php echo $user->id;?>">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>

<?php elseif($_GET["spends"]=="toll"):?>

<div class="row">

    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vehiculo</label>
      <?php $clients = CarsData::getAll();?>
    <select style="background-color:#333;" name="car_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->car_id!=null&& $user->car_id==$client->id){ echo "selected";}?>><?php echo $client->getBrand()->name." ".$client->name." ".$client->year." ".$client->getExColor()->name." - ".$client->chassis;?></option>
    <?php endforeach;?>
      </select>
    </div>

    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Usuario</label>
      <?php $clients = UserData::getAll();?>
    <select style="background-color:#333;" name="user_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->user_id!=null&& $user->user_id==$client->id){ echo "selected";}?>><?php echo $client->name." ".$client->lastname;?></option>
    <?php endforeach;?>
      </select>
    </div>

     <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Forma</label>
      <?php $clients = FData::getAll();?>
    <select style="background-color:#333;" name="f_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->f_id!=null&& $user->f_id==$client->id){ echo "selected";}?>><?php echo $client->name." ".$client->lastname;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
     <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Tipo</label>
      <?php $clients = PData::getAll();?>
    <select style="background-color:#333;" name="p_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->p_id!=null&& $user->p_id==$client->id){ echo "selected";}?>><?php echo $client->name." ".$client->lastname;?></option>
    <?php endforeach;?>
      </select>
    </div>

    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Monto Pagado</label>
    <input style="background-color:#222;" type="text" name="total" id="inputFormatoNumerico" value="<?php echo $user->total;?>" class="form-control" placeholder="Monto Pagado">
    </div>
  
    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha Realizado</label>
    <input style="background-color:#222;" type="date" class="form-control" value="<?php echo $user->created_date;?>"  autocomplete="off"  name="created_date">
    </div>

</div>
 <div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=finance&opt=all&spends=Negocio" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                  <input style="background-color:#222;" type="hidden" name="id" value="<?php echo $user->id;?>">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
<?php elseif($_GET["spends"]=="fuel"):?>

<div class="row">

    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vehiculo</label>
      <?php $clients = CarsData::getAll();?>
    <select style="background-color:#333;" name="car_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->car_id!=null&& $user->car_id==$client->id){ echo "selected";}?>><?php echo $client->getBrand()->name." ".$client->name." ".$client->year." ".$client->getExColor()->name." - ".$client->chassis;?></option>
    <?php endforeach;?>
      </select>
    </div>

    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Usuario</label>
      <?php $clients = UserData::getAll();?>
    <select style="background-color:#333;" name="user_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->user_id!=null&& $user->user_id==$client->id){ echo "selected";}?>><?php echo $client->name." ".$client->lastname;?></option>
    <?php endforeach;?>
      </select>
    </div>

     <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Forma</label>
      <?php $clients = FData::getAll();?>
    <select style="background-color:#333;" name="f_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->f_id!=null&& $user->f_id==$client->id){ echo "selected";}?>><?php echo $client->name." ".$client->lastname;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
     <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Tipo</label>
      <?php $clients = PData::getAll();?>
    <select style="background-color:#333;" name="p_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->p_id!=null&& $user->p_id==$client->id){ echo "selected";}?>><?php echo $client->name." ".$client->lastname;?></option>
    <?php endforeach;?>
      </select>
    </div>

    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Monto Pagado</label>
    <input style="background-color:#222;" type="text" name="total" id="inputFormatoNumerico" value="<?php echo $user->total;?>" class="form-control" placeholder="Monto Pagado">
    </div>
  
    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha Realizado</label>
    <input style="background-color:#222;" type="date" class="form-control" value="<?php echo $user->created_date;?>"  autocomplete="off"  name="created_date">
    </div>

</div>
 <div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=finance&opt=all&spends=Negocio" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                  <input style="background-color:#222;" type="hidden" name="id" value="<?php echo $user->id;?>">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
<?php elseif($_GET["spends"]=="maintenance"):?>

<div class="row">

    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Vehiculo</label>
      <?php $clients = CarsData::getAll();?>
    <select style="background-color:#333;" name="car_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->car_id!=null&& $user->car_id==$client->id){ echo "selected";}?>><?php echo $client->getBrand()->name." ".$client->name." ".$client->year." ".$client->getExColor()->name." - ".$client->chassis;?></option>
    <?php endforeach;?>
      </select>
    </div>

    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Usuario</label>
      <?php $clients = UserData::getAll();?>
    <select style="background-color:#333;" name="user_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->user_id!=null&& $user->user_id==$client->id){ echo "selected";}?>><?php echo $client->name." ".$client->lastname;?></option>
    <?php endforeach;?>
      </select>
    </div>

     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Forma</label>
      <?php $clients = FData::getAll();?>
    <select style="background-color:#333;" name="f_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->f_id!=null&& $user->f_id==$client->id){ echo "selected";}?>><?php echo $client->name." ".$client->lastname;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
     <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Tipo</label>
      <?php $clients = PData::getAll();?>
    <select style="background-color:#333;" name="p_id" class="form-control select2" required>
      <option value="">-- ELEGIR --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"<?php if($user->p_id!=null&& $user->p_id==$client->id){ echo "selected";}?>><?php echo $client->name." ".$client->lastname;?></option>
    <?php endforeach;?>
      </select>
    </div>

    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Monto Pagado</label>
    <input style="background-color:#222;" type="text" name="total" id="inputFormatoNumerico" value="<?php echo $user->total;?>" class="form-control" placeholder="Monto Pagado">
    </div>
  
  
     <div class="col-md-4 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 control-label">Que se le Hizo?</label>
     <input style="background-color:#222;" id="placein" type="text" class="form-control" autocomplete="off" value="<?php echo $user->maintenance;?>"  name="maintenance" placeholder="Que se le Hizo?">
    </div>


     <div class="col-md-4 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 control-label">Fecha</label>
     <input style="background-color:#222;" type="datetime-local" class="form-control" value="<?php echo $user->created_at;?>"  name="created_at">
    </div>

</div>
 <div class="row my-2">
                <div class="col-md-6 col-6">
                  <a href="./?view=finance&opt=all&spends=Negocio" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                  <input style="background-color:#222;" type="hidden" name="id" value="<?php echo $user->id;?>">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>

<?php endif; ?>
  
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
 
</script>
<?php if ($_GET["spends"]=="negocio"):?>
<script>
                  jQuery(document).ready(function(){
            jQuery("#updfspends").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=finance&opt=updspend",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Gastos Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=finance&opt=all&spends=Negocio'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Actualizo' });
                  }
                  }
                });
                return false;
              });
            });
          
</script>
<?php elseif ($_GET["spends"]=="other"):?>
<script>
                  jQuery(document).ready(function(){
            jQuery("#updfspends").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=finance&opt=updother",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Gastos Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=finance&opt=all&spends=Otros'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Actualizo' });
                  }
                  }
                });
                return false;
              });
            });
          
</script>
<?php elseif ($_GET["spends"]=="toll"):?>
<script>
                    jQuery(document).ready(function(){
            jQuery("#updfspends").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=toll&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Peaje Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=finance&opt=all&spends=Negocio'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Actualizo' });
                  }
                  }
                });
                return false;
              });
            });
          
</script>
<?php elseif ($_GET["spends"]=="fuel"):?>
<script>
            jQuery(document).ready(function(){
            jQuery("#updfspends").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=fuels&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Combustible Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=finance&opt=all&spends=Negocio'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Actualizo' });
                  }
                  }
                });
                return false;
              });
            });
          
</script>
<?php elseif ($_GET["spends"]=="maintenance"):?>
<script>
                     jQuery(document).ready(function(){
            jQuery("#updfspends").submit(function(e){
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
                    setTimeout(function(){ window.location = './?view=finance&opt=all&spends=Negocio'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Actualizo' });
                  }
                  }
                });
                return false;
              });
            });

</script>

<?php endif;?>

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

</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="vehicle"):
///////////////////////////////////////////////////////// NEWSPENDS ///////////////////////////
?>

<section class="content">
<div class="row">
  <div class="col-md-12">
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class="fa fa-minus-square"></i> Gastos de Vehiculos</h1>
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
<div class="callout callout-warning" style="background-color:#222;">
              <h5><i class="fas fa-info"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Nota"; break;
 case 'EN': echo "Note"; break;
}
?>:</h5>
             <?php 
switch (Core::$user->language){
 case 'ES': echo " Este apartado ha sido mejorado para su impresión. Haga clic en el nombre para las opciones que ofrece el registro en la parte inferior del apartado para probarlo."; break;
 case 'EN': echo "This section has been improved for printing. Click on the name for the registration options at the bottom of the section to try it out."; break;
}
?>
            </div>
  

<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
<thead>
  <th>Accion</th>
      <th>Categoria</th>
      <th>Vehiculo</th>
      <th>Año</th>
      <th>Placa</th>
      <th>Color</th>
  </thead>
 <?php
      foreach(CarsData::getAll() as $sells):?>
        <tr>
                  <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=spends&opt=vehicle&car_id=<?php echo $sells->id;?>" class="btn btn-success"><i class="fas fa-eye"></i></a>
                      </div>
        </td>
        <td><?php echo  strtoupper($sells->getCategory()->name); ?></td>
        <td><?php echo  $sells->getBrand()->name." ".$sells->name;?></td>
        <td><?php echo strtoupper($sells->year); ?></td>
        <td><?php echo strtoupper($sells->plate); ?></td>
        <td> <?php echo strtoupper($sells->getExColor()->name);?></td>
    </tr>

<?php endforeach; ?>

</table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->



</div>
</div>
</div>
</div>
</section>

 <script type="text/javascript">
      $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

      
    </script>

<?php endif; ?>