<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="content">
<div class="row">
	<div class="col-md-12">
	<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-users'></i> <?php 
switch (Core::$user->language){
  case 'ES': echo 'Usuarios.'; break;
  case 'EN': echo 'Users'; break;
}
?></h1>
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
     
     
	
<?php 
$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT * from user where status=1  and username<>'krtavarez' and stock_id=".StockData::getPrincipal()->id;
$query = $con->query($sql);
    if(count($query)>0):?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example2">
    <thead> 
    <th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Accion'; break;
  case 'EN': echo 'Action'; break;
}
?></th>
			<th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Foto'; break;
  case 'EN': echo 'Photo'; break;
}
?></th>
			<th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Nombre'; break;
  case 'EN': echo 'Name'; break;
}
?></th>
			<th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Correo'; break;
  case 'EN': echo 'Email'; break;
}
?></th>
		
			<th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Activo'; break;
  case 'EN': echo 'Asset'; break;
}
?></th>
			<th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Tipo'; break;
  case 'EN': echo 'Type'; break;
}
?></th>
    <th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Accion'; break;
  case 'EN': echo 'Action'; break;
}
?></th>
    </thead>

    <tfoot>
      <tr> 
    <th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Accion'; break;
  case 'EN': echo 'Action'; break;
}
?></th>
			<th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Foto'; break;
  case 'EN': echo 'Photo'; break;
}
?></th>
			<th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Nombre'; break;
  case 'EN': echo 'Name'; break;
}
?></th>
			<th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Correo'; break;
  case 'EN': echo 'Email'; break;
}
?></th>
			<th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Activo'; break;
  case 'EN': echo 'Asset'; break;
}
?></th>
			<th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Tipo'; break;
  case 'EN': echo 'Type'; break;
}
?></th>
    <th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Accion'; break;
  case 'EN': echo 'Action'; break;
}
?></th>
      </tr>
    </tfoot>

      <?php while($r = $query->fetch_array()){?>
        <tr>
                       <td class="text-right py-0 align-middle">
                      <div class="btn-group btn-group-sm btn-block">
                        <a href="./?view=users&opt=edit&id=<?php echo $r['id'];?>" class="btn btn-success"><i class="fas fa-edit"></i></a>
                        <a href="./?view=users&opt=history&id=<?php echo $r['id'];?>" class="btn btn-info"><i class="fas fa-history"></i></a>
                        <a href="./?view=userpermissions&id=<?php echo $r['id'];?>" class="btn btn-warning"><i class="fas fa-user-plus"></i></a>
                      </div>
        </td>
<td width="5%">
					<?php if($r['image']!=""):
						$url = "CF-SYSTEMS/storage/profiles/".$r['image'];
						if(file_exists($url)){
							echo "<img src='$url' style='width:50%;'>";
						}
					endif;?>
				</td>
				<td><?php echo utf8_decode($r['name']." ".$r['lastname']); ?></td>
				<td><?php echo $r['email']; ?></td>
<?php if($r['status']==1):
switch (Core::$user->language){
  case 'ES': echo '<td> SI </td>'; break;
  case 'EN': echo '<td> YEAH </td>'; break;
} endif;
?>

<?php if($r['status']==0):
switch (Core::$user->language){
  case 'ES': echo '<td> NO </td>'; break;
  case 'EN': echo '<td> NOT </td>'; break;
} endif;
?>
			<?php	switch (Core::$user->language){
  case 'ES': echo '<td>'.KData::getById($r['kind'])->nombre.'</td>'; break;
  case 'EN': echo '<td>'.KData::getById($r['kind'])->name.'</td>'; break;
}
?>
				
        <td class="text-right py-0 align-middle">
            
<?php  
$sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
$qry = $con->query($sl); 

while($x = $qry->fetch_array()){
if ($x['permits_id']==4): ?>
   
                        <a href="./?action=users&opt=del&id=<?php echo $r['id'];?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete();" ><i class="fas fa-trash"> 	<?php	switch (Core::$user->language){
  case 'ES': echo 'Eliminar'; break;
  case 'EN': echo 'Delete'; break;
}
?></i></a>
                    
                    
                     <script>
function confirmDelete() {
    return confirm("¿Estás seguro de que deseas eliminar este registro?");
}
</script>
    <?php endif;?>
<?php }; ?>
</td>
    </tr>
    
    <?php }; ?>
      </table>
  </div><!-- /.box-body -->
</div>
</div><!-- /.box -->
      <?php else:?>
     
         <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Usuarios</h2>
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

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="new"):?>
<section class="content">
<div class="row">
	<div class="col-md-12">
	<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-users'></i> <?php 
switch (Core::$user->language){
  case 'ES': echo 'Nuevo Usuarios'; break;
  case 'EN': echo 'New Users'; break;
}
?></h1>
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
		<form class="form-horizontal" enctype="multipart/form-data"   method="post" id="changepasswd" role="form">
<div class="row">
 <div class="col-md-4 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Idioma'; break;
  case 'EN': echo 'Language'; break;
}
?></label>
      <select style="background-color:#333;" name="language" class="form-control select2" required>
      <option value="ES" <?php if($user->language=="ES"){ echo "selected"; }?>><?php 
switch (Core::$user->language){
  case 'ES': echo 'ESPAÑOL'; break;
  case 'EN': echo 'SPANISH'; break;
}
?></option>
      <option value="EN" <?php if($user->language=="EN"){ echo "selected"; }?>><?php 
switch (Core::$user->language){
  case 'ES': echo 'INGLES'; break;
  case 'EN': echo 'ENGLISH'; break;
}
?></option>
      </select>
    </div>
    
    
     <div  class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
     <div class="input-group">
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Cedula'; break;
  case 'EN': echo 'ID'; break;
}
?></label>
      <input style="background-color:#333;" type="text" name="no" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Cedula'; break;
  case 'EN': echo 'ID'; break;
}
?>" value="<?php echo $user->no;?>">
    </div>
</div>

<div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Genero'; break;
  case 'EN': echo 'Gender'; break;
}
?></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-spinner"></i></span>
    
    <select style="background-color:#333;" name="gender" class="form-control" required>
        <option value="1"<?php if($user->image=="man.png"){ echo "selected"; }?>><?php 
switch (Core::$user->language){
  case 'ES': echo 'Hombre'; break;
  case 'EN': echo 'Man'; break;
}
?></option>
      <option value="2"<?php if($user->image=="woman.png"){ echo "selected"; }?>><?php 
switch (Core::$user->language){
  case 'ES': echo 'Mujer'; break;
  case 'EN': echo 'Women'; break;
}
?></option>
      </select>
    </div>
  </div>

</div>



<div class="row">
     
     <div  class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Nombre'; break;
  case 'EN': echo 'Name'; break;
}
?> <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-user"></i></span>
      <input style="background-color:#333;" type="text" name="name"  autofocus class="form-control" id="name" required placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Nombre'; break;
  case 'EN': echo 'Name'; break;
}
?>">
    </div>
</div>

     <div  class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Apellido'; break;
  case 'EN': echo 'Last name'; break;
}
?></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-user"></i></span>
      <input style="background-color:#333;" type="text" name="lastname" class="form-control"  placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Apellido'; break;
  case 'EN': echo 'Last name'; break;
}
?>">
    </div>
</div>
     
     <div  class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Correo electrónico'; break;
  case 'EN': echo 'Email'; break;
}
?></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-comments"></i></span>
      <input style="background-color:#333;" type="email" name="email" class="form-control" id="email"  placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Correo electrónico'; break;
  case 'EN': echo 'Email'; break;
}
?>">
    </div>
</div>

     
     <div  class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
         <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Tipo'; break;
  case 'EN': echo 'Type'; break;
}
?>: <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-clone"></i></span>
    <select style="background-color:#333;" name="kind" required class="form-control">
    <?php foreach(KData::getAll() as $k):?>
      <option value="<?php echo $k->id;?>" <?php if($k->id==$user->kind){ echo "selected"; }?>><?php switch (Core::$user->language){
  case 'ES': echo $k->nombre; break;
  case 'EN': echo $k->name; break;
}?></option>
    <?php endforeach;?>
      </select>    
    </div>
  </div>

     <div  class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Sueldo'; break;
  case 'EN': echo 'Salary'; break;
}
?></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-plus"></i></span>
      <input style="background-color:#333;" type="text" name="comision" class="form-control" placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Sueldo'; break;
  case 'EN': echo 'Salary'; break;
}
?>">
    </div>
</div>
     
     <div  class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
    <label for="inputPassword1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Contraseña'; break;
  case 'EN': echo 'Password'; break;
}
?> <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-asterisk"></i></span>
    <input style="background-color:#333;" type="password" class="form-control"   id="password" name="password" placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Contraseña'; break;
  case 'EN': echo 'Password'; break;
}
?>">
    </div>
  </div>

     <div  class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
    <label for="inputPassword1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Confirmar Contraseña'; break;
  case 'EN': echo 'Confirm Password'; break;
}
?> <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-edit"></i></span>
      <input style="background-color:#333;" type="password" class="form-control" id="confirmnewpassword"  placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Confirmar Contraseña'; break;
  case 'EN': echo 'Confirm Password'; break;
}
?>">
    </div>
</div>
</div>
   <div class="col-md-6 col-12">
      <br>
    <label>
       <div class="form-group clearfix">
                      <div class="icheck-primary d-inline">
                        <input style="background-color:#333;" type="checkbox" id="radioPrimary" name="status" checked>
                        <label for="radioPrimary">
                          <?php 
switch (Core::$user->language){
  case 'ES': echo '¿Esta activo?'; break;
  case 'EN': echo 'Is it active?'; break;
}
?>
                        </label>
                      </div>
                    
                    </div>
    </label>
    

  <div class="contenedor">

		<div class="row">
			<div class="col-md-12">
		 		<canvas id="draw-canvas" >
		 			No tienes un buen navegador.
		 		</canvas>
		 	</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<input style="background-color:#333;" type="button" class="button" id="draw-submitBtn" value="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Guardar Firma'; break;
  case 'EN': echo 'Save Signature'; break;
}
?>"></input>
				<input style="background-color:#333;" type="button" class="button" id="draw-clearBtn" value="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Borrar Firma'; break;
  case 'EN': echo 'Delete Signature'; break;
}
?>"></input>

						<label hidden>Color</label>
						<input style="background-color:#333;" hidden type="color" id="color">
						<label hidden>Puntero</label>
						<input style="background-color:#333;" hidden type="range" id="puntero" min="1" default="1" max="5" width="10%">


			</div>

		</div>

	
		<div hidden class="row">
			<div class="col-md-12">
				<textarea required id="draw-dataUrl" class="form-control" name="base64" rows="5"></textarea>
			</div>
		</div>
		
	
	</div>   
    
<script>
/*
		El siguiente codigo en JS Contiene mucho codigo
		de las siguietes 3 fuentes:
		https://stipaltamar.github.io/dibujoCanvas/
		https://developer.mozilla.org/samples/domref/touchevents.html - https://developer.mozilla.org/es/docs/DOM/Touch_events
		http://bencentra.com/canvas/signature/signature.html - https://bencentra.com/code/2014/12/05/html5-canvas-touch-events.html
*/

(function() { // Comenzamos una funcion auto-ejecutable

	// Obtenenemos un intervalo regular(Tiempo) en la pamtalla
	window.requestAnimFrame = (function (callback) {
		return window.requestAnimationFrame ||
					window.webkitRequestAnimationFrame ||
					window.mozRequestAnimationFrame ||
					window.oRequestAnimationFrame ||
					window.msRequestAnimaitonFrame ||
					function (callback) {
					 	window.setTimeout(callback, 1000/60);
            // Retrasa la ejecucion de la funcion para mejorar la experiencia
					};
	})();

	// Traemos el canvas mediante el id del elemento html
	var canvas = document.getElementById("draw-canvas");
	var ctx = canvas.getContext("2d");


	// Mandamos llamar a los Elemetos interactivos de la Interfaz HTML
	var drawText = document.getElementById("draw-dataUrl");
	var drawImage = document.getElementById("draw-image");
	var clearBtn = document.getElementById("draw-clearBtn");
	var submitBtn = document.getElementById("draw-submitBtn");
	clearBtn.addEventListener("click", function (e) {
		// Definimos que pasa cuando el boton draw-clearBtn es pulsado
		clearCanvas();
		drawImage.setAttribute("src", "");
	}, false);
		// Definimos que pasa cuando el boton draw-submitBtn es pulsado
	submitBtn.addEventListener("click", function (e) {
	var dataUrl = canvas.toDataURL();
	drawText.innerHTML = dataUrl;
	drawImage.setAttribute("src", dataUrl);
	 }, false);

	// Activamos MouseEvent para nuestra pagina
	var drawing = false;
	var mousePos = { x:0, y:0 };
	var lastPos = mousePos;
	canvas.addEventListener("mousedown", function (e)
  {
    /*
      Mas alla de solo llamar a una funcion, usamos function (e){...}
      para mas versatilidad cuando ocurre un evento
    */
		var tint = document.getElementById("color");
		var punta = document.getElementById("puntero");
		console.log(e);
		drawing = true;
		lastPos = getMousePos(canvas, e);
	}, false);
	canvas.addEventListener("mouseup", function (e)
  {
		drawing = false;
	}, false);
	canvas.addEventListener("mousemove", function (e)
  {
		mousePos = getMousePos(canvas, e);
	}, false);

	// Activamos touchEvent para nuestra pagina
	canvas.addEventListener("touchstart", function (e) {
		mousePos = getTouchPos(canvas, e);
    console.log(mousePos);
    e.preventDefault(); // Prevent scrolling when touching the canvas
		var touch = e.touches[0];
		var mouseEvent = new MouseEvent("mousedown", {
			clientX: touch.clientX,
			clientY: touch.clientY
		});
		canvas.dispatchEvent(mouseEvent);
	}, false);
	canvas.addEventListener("touchend", function (e) {
    e.preventDefault(); // Prevent scrolling when touching the canvas
		var mouseEvent = new MouseEvent("mouseup", {});
		canvas.dispatchEvent(mouseEvent);
	}, false);
  canvas.addEventListener("touchleave", function (e) {
    // Realiza el mismo proceso que touchend en caso de que el dedo se deslice fuera del canvas
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var mouseEvent = new MouseEvent("mouseup", {});
    canvas.dispatchEvent(mouseEvent);
  }, false);
	canvas.addEventListener("touchmove", function (e) {
    e.preventDefault(); // Prevent scrolling when touching the canvas
		var touch = e.touches[0];
		var mouseEvent = new MouseEvent("mousemove", {
			clientX: touch.clientX,
			clientY: touch.clientY
		});
		canvas.dispatchEvent(mouseEvent);
	}, false);

	// Get the position of the mouse relative to the canvas
	function getMousePos(canvasDom, mouseEvent) {
		var rect = canvasDom.getBoundingClientRect();
    /*
      Devuelve el tamaño de un elemento y su posición relativa respecto
      a la ventana de visualización (viewport).
    */
		return {
			x: mouseEvent.clientX - rect.left,
			y: mouseEvent.clientY - rect.top
		};
	}

	// Get the position of a touch relative to the canvas
	function getTouchPos(canvasDom, touchEvent) {
		var rect = canvasDom.getBoundingClientRect();
    console.log(touchEvent);
    /*
      Devuelve el tamaño de un elemento y su posición relativa respecto
      a la ventana de visualización (viewport).
    */
		return {
			x: touchEvent.touches[0].clientX - rect.left, // Popiedad de todo evento Touch
			y: touchEvent.touches[0].clientY - rect.top
		};
	}

	// Draw to the canvas
	function renderCanvas() {
		if (drawing) {
      var tint = document.getElementById("color");
      var punta = document.getElementById("puntero");
      ctx.strokeStyle = tint.value;
      ctx.beginPath();
			ctx.moveTo(lastPos.x, lastPos.y);
			ctx.lineTo(mousePos.x, mousePos.y);
      console.log(punta.value);
    	ctx.lineWidth = punta.value;
			ctx.stroke();
      ctx.closePath();
			lastPos = mousePos;
		}
	}

	function clearCanvas() {
		canvas.width = canvas.width;
	}

	// Allow for animation
	(function drawLoop () {
		requestAnimFrame(drawLoop);
		renderCanvas();
	})();

})();    
</script>
<style>

#draw-canvas {
  border: 2px dotted #CCCCCC;
  border-radius: 5px;
  cursor: crosshair;
  background-image: url("CF-SYSTEMS/storage/configuration/fxrma.png");
  background-size: 100% 100%;
}

#draw-dataUrl {
  width: 100%;
}

section{
    flex:1;
}



.button {
    background: #3071a9;
    box-shadow: inset 0 -3px 0 rgba(0,0,0,.3);
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 5px;
    margin: 0 15px;
    text-decoration: none;
    color: white;
}

.button:active {
    transform: scale(0.9);
}

.contenedor {
    width: 100%
    margin: 5px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

input[type=range] {
  -webkit-appearance: none;
  margin: 18px 0;

}
input[type=range]:focus {
  outline: none;
}
input[type=range]::-webkit-slider-runnable-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-webkit-slider-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
  -webkit-appearance: none;
  margin-top: -14px;
}
input[type=range]:focus::-webkit-slider-runnable-track {
  background: #367ebd;
}
input[type=range]::-moz-range-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-moz-range-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]::-ms-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  background: transparent;
  border-color: transparent;
  border-width: 16px 0;
  color: transparent;
}
input[type=range]::-ms-fill-lower {
  background: #2a6495;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-fill-upper {
  background: #3071a9;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]:focus::-ms-fill-lower {
  background: #3071a9;
}
input[type=range]:focus::-ms-fill-upper {
  background: #367ebd;
}
</style>

</div>
    
  
<div class="row my-2" >
                <div class="col-md-6 col-6">
                  <a href="./?view=users&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> <?php 
switch (Core::$user->language){
  case 'ES': echo 'Cancelar'; break;
  case 'EN': echo 'Cancel'; break;
}
?></a>
                </div>
                <div class="col-md-6 col-6">
                  <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $user->id;?>">
                 
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
  case 'ES': echo 'Finalizar'; break;
  case 'EN': echo 'Finish'; break;
}
?></button>
                 
                </div>
              </div>
</form>
  </div>
</div>
</div>
</div>
</div>
   </div><!-- /.container-fluid -->
    </div>   

</section>
<script>
  $("#changepasswd").submit(function(e){
    if($("#password").val() == $("#confirmnewpassword").val()){

                e.preventDefault();
                var formData = jQuery(this).serialize();
               $.ajax({
                  type: "POST",
                  url: "./?action=users&opt=add",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Usuario Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=users&opt=all'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Actualizar' });
                  }
                  }
                });
                return false;
              

}else{
      e.preventDefault();
      $.jGrowl("La contraseña no coincide con la confirmacion", { header: 'Acceso permitido' });
    }
  
});
</script>
     <?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"):?>
<section class="content">
<?php $user = UserData::getById($_GET["id"]);?>

<div class="row">
	<div class="col-md-12">
	<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-users'></i> <?php 
switch (Core::$user->language){
  case 'ES': echo 'Editar Usuarios'; break;
  case 'EN': echo 'Edit Users'; break;
}
?></h1>
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
		<form class="form-horizontal" enctype="multipart/form-data"   method="post" id="changepasswd" role="form">
<div class="row">
 <div class="col-md-4 col-12">
<label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Idioma'; break;
  case 'EN': echo 'Language'; break;
}
?></label>
      <select style="background-color:#333;" name="language" class="form-control select2" required>
      <option value="ES" <?php if($user->language=="ES"){ echo "selected"; }?>><?php 
switch (Core::$user->language){
  case 'ES': echo 'ESPAÑOL'; break;
  case 'EN': echo 'SPANISH'; break;
}
?></option>
      <option value="EN" <?php if($user->language=="EN"){ echo "selected"; }?>><?php 
switch (Core::$user->language){
  case 'ES': echo 'INGLES'; break;
  case 'EN': echo 'ENGLISH'; break;
}
?></option>
      </select>
    </div>
    
    
     <div  class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
     <div class="input-group">
  <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Cedula'; break;
  case 'EN': echo 'ID'; break;
}
?></label>
      <input style="background-color:#333;" type="text" name="no" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Cedula'; break;
  case 'EN': echo 'ID'; break;
}
?>" value="<?php echo $user->no;?>">
    </div>
</div>

<div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Genero'; break;
  case 'EN': echo 'Gender'; break;
}
?></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-spinner"></i></span>
    
    <select style="background-color:#333;" name="gender" class="form-control" required>
        <option value="1"<?php if($user->image=="man.png"){ echo "selected"; }?>><?php 
switch (Core::$user->language){
  case 'ES': echo 'Hombre'; break;
  case 'EN': echo 'Man'; break;
}
?></option>
      <option value="2"<?php if($user->image=="woman.png"){ echo "selected"; }?>><?php 
switch (Core::$user->language){
  case 'ES': echo 'Mujer'; break;
  case 'EN': echo 'Women'; break;
}
?></option>
      </select>
    </div>
  </div>

</div>



<div class="row">
     
     <div  class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Nombre'; break;
  case 'EN': echo 'Name'; break;
}
?> <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-user"></i></span>
      <input style="background-color:#333;" type="text" name="name" value="<?php echo utf8_decode($user->name);?>" autofocus class="form-control" id="name" required placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Nombre'; break;
  case 'EN': echo 'Name'; break;
}
?>">
    </div>
</div>

     <div  class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Apellido'; break;
  case 'EN': echo 'Last name'; break;
}
?></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-user"></i></span>
      <input style="background-color:#333;" type="text" name="lastname" class="form-control"  value="<?php echo $user->lastname;?>" placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Apellido'; break;
  case 'EN': echo 'Last name'; break;
}
?>">
    </div>
</div>
     
     <div  class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Correo electrónico'; break;
  case 'EN': echo 'Email'; break;
}
?></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-comments"></i></span>
      <input style="background-color:#333;" type="email" name="email" class="form-control" id="email" value="<?php echo $user->email;?>" placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Correo electrónico'; break;
  case 'EN': echo 'Email'; break;
}
?>">
    </div>
</div>

     
     <div  class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
         <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Tipo'; break;
  case 'EN': echo 'Type'; break;
}
?>: <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-clone"></i></span>
    <select style="background-color:#333;" name="kind" required class="form-control">
    <?php foreach(KData::getAll() as $k):?>
      <option value="<?php echo $k->id;?>" <?php if($k->id==$user->kind){ echo "selected"; }?>><?php echo $k->name;?></option>
    <?php endforeach;?>
      </select>    
    </div>
  </div>

     <div  class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Sueldo'; break;
  case 'EN': echo 'Salary'; break;
}
?></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-plus"></i></span>
      <input style="background-color:#333;" type="text" name="comision" class="form-control" value="<?php echo $user->comision;?>"  placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Sueldo'; break;
  case 'EN': echo 'Salary'; break;
}
?>">
    </div>
</div>
     
     <div  class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
    <label for="inputPassword1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Contraseña'; break;
  case 'EN': echo 'Password'; break;
}
?> <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-asterisk"></i></span>
    <input style="background-color:#333;" type="password" class="form-control"   id="password" name="password" placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Contraseña'; break;
  case 'EN': echo 'Password'; break;
}
?>">
    </div>
  </div>

     <div  class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
    <label for="inputPassword1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Confirmar Contraseña'; break;
  case 'EN': echo 'Confirm Password'; break;
}
?> <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-edit"></i></span>
      <input style="background-color:#333;" type="password" class="form-control" id="confirmnewpassword"  placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Confirmar Contraseña'; break;
  case 'EN': echo 'Confirm Password'; break;
}
?>">
    </div>
</div>
</div>
 <p class="help-block"><?php 
switch (Core::$user->language){
  case 'ES': echo 'La contraseña solo se modificara si escribes algo, en caso contrario no se modifica.'; break;
  case 'EN': echo 'The password will only be modified if you type something, otherwise it is not modified.'; break;
}
?></p>
   <div class="col-md-6 col-12">
      <br>
    <label>
       <div class="form-group clearfix">
                      <div class="icheck-primary d-inline">
                        <input style="background-color:#333;" type="checkbox" id="radioPrimary" name="status" <?php if($user->status){ echo "checked";}?>>
                        <label for="radioPrimary">
                          <?php 
switch (Core::$user->language){
  case 'ES': echo '¿Esta activo?'; break;
  case 'EN': echo 'Is it active?'; break;
}
?>
                        </label>
                      </div>
                    
                    </div>
    </label>
    

  <div class="contenedor">

		<div class="row">
			<div class="col-md-12">
		 		<canvas id="draw-canvas" >
		 			No tienes un buen navegador.
		 		</canvas>
		 	</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<input style="background-color:#333;" type="button" class="button" id="draw-submitBtn" value="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Guardar Firma'; break;
  case 'EN': echo 'Save Signature'; break;
}
?>"></input>
				<input style="background-color:#333;" type="button" class="button" id="draw-clearBtn" value="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Borrar Firma'; break;
  case 'EN': echo 'Delete Signature'; break;
}
?>"></input>

						<label hidden>Color</label>
						<input style="background-color:#333;" hidden type="color" id="color">
						<label hidden>Puntero</label>
						<input style="background-color:#333;" hidden type="range" id="puntero" min="1" default="1" max="5" width="10%">


			</div>

		</div>

	
		<div hidden class="row">
			<div class="col-md-12">
				<textarea required id="draw-dataUrl" class="form-control" name="base64" rows="5"></textarea>
			</div>
		</div>
		
	
	</div>   
    
<script>
/*
		El siguiente codigo en JS Contiene mucho codigo
		de las siguietes 3 fuentes:
		https://stipaltamar.github.io/dibujoCanvas/
		https://developer.mozilla.org/samples/domref/touchevents.html - https://developer.mozilla.org/es/docs/DOM/Touch_events
		http://bencentra.com/canvas/signature/signature.html - https://bencentra.com/code/2014/12/05/html5-canvas-touch-events.html
*/

(function() { // Comenzamos una funcion auto-ejecutable

	// Obtenenemos un intervalo regular(Tiempo) en la pamtalla
	window.requestAnimFrame = (function (callback) {
		return window.requestAnimationFrame ||
					window.webkitRequestAnimationFrame ||
					window.mozRequestAnimationFrame ||
					window.oRequestAnimationFrame ||
					window.msRequestAnimaitonFrame ||
					function (callback) {
					 	window.setTimeout(callback, 1000/60);
            // Retrasa la ejecucion de la funcion para mejorar la experiencia
					};
	})();

	// Traemos el canvas mediante el id del elemento html
	var canvas = document.getElementById("draw-canvas");
	var ctx = canvas.getContext("2d");


	// Mandamos llamar a los Elemetos interactivos de la Interfaz HTML
	var drawText = document.getElementById("draw-dataUrl");
	var drawImage = document.getElementById("draw-image");
	var clearBtn = document.getElementById("draw-clearBtn");
	var submitBtn = document.getElementById("draw-submitBtn");
	clearBtn.addEventListener("click", function (e) {
		// Definimos que pasa cuando el boton draw-clearBtn es pulsado
		clearCanvas();
		drawImage.setAttribute("src", "");
	}, false);
		// Definimos que pasa cuando el boton draw-submitBtn es pulsado
	submitBtn.addEventListener("click", function (e) {
	var dataUrl = canvas.toDataURL();
	drawText.innerHTML = dataUrl;
	drawImage.setAttribute("src", dataUrl);
	 }, false);

	// Activamos MouseEvent para nuestra pagina
	var drawing = false;
	var mousePos = { x:0, y:0 };
	var lastPos = mousePos;
	canvas.addEventListener("mousedown", function (e)
  {
    /*
      Mas alla de solo llamar a una funcion, usamos function (e){...}
      para mas versatilidad cuando ocurre un evento
    */
		var tint = document.getElementById("color");
		var punta = document.getElementById("puntero");
		console.log(e);
		drawing = true;
		lastPos = getMousePos(canvas, e);
	}, false);
	canvas.addEventListener("mouseup", function (e)
  {
		drawing = false;
	}, false);
	canvas.addEventListener("mousemove", function (e)
  {
		mousePos = getMousePos(canvas, e);
	}, false);

	// Activamos touchEvent para nuestra pagina
	canvas.addEventListener("touchstart", function (e) {
		mousePos = getTouchPos(canvas, e);
    console.log(mousePos);
    e.preventDefault(); // Prevent scrolling when touching the canvas
		var touch = e.touches[0];
		var mouseEvent = new MouseEvent("mousedown", {
			clientX: touch.clientX,
			clientY: touch.clientY
		});
		canvas.dispatchEvent(mouseEvent);
	}, false);
	canvas.addEventListener("touchend", function (e) {
    e.preventDefault(); // Prevent scrolling when touching the canvas
		var mouseEvent = new MouseEvent("mouseup", {});
		canvas.dispatchEvent(mouseEvent);
	}, false);
  canvas.addEventListener("touchleave", function (e) {
    // Realiza el mismo proceso que touchend en caso de que el dedo se deslice fuera del canvas
    e.preventDefault(); // Prevent scrolling when touching the canvas
    var mouseEvent = new MouseEvent("mouseup", {});
    canvas.dispatchEvent(mouseEvent);
  }, false);
	canvas.addEventListener("touchmove", function (e) {
    e.preventDefault(); // Prevent scrolling when touching the canvas
		var touch = e.touches[0];
		var mouseEvent = new MouseEvent("mousemove", {
			clientX: touch.clientX,
			clientY: touch.clientY
		});
		canvas.dispatchEvent(mouseEvent);
	}, false);

	// Get the position of the mouse relative to the canvas
	function getMousePos(canvasDom, mouseEvent) {
		var rect = canvasDom.getBoundingClientRect();
    /*
      Devuelve el tamaño de un elemento y su posición relativa respecto
      a la ventana de visualización (viewport).
    */
		return {
			x: mouseEvent.clientX - rect.left,
			y: mouseEvent.clientY - rect.top
		};
	}

	// Get the position of a touch relative to the canvas
	function getTouchPos(canvasDom, touchEvent) {
		var rect = canvasDom.getBoundingClientRect();
    console.log(touchEvent);
    /*
      Devuelve el tamaño de un elemento y su posición relativa respecto
      a la ventana de visualización (viewport).
    */
		return {
			x: touchEvent.touches[0].clientX - rect.left, // Popiedad de todo evento Touch
			y: touchEvent.touches[0].clientY - rect.top
		};
	}

	// Draw to the canvas
	function renderCanvas() {
		if (drawing) {
      var tint = document.getElementById("color");
      var punta = document.getElementById("puntero");
      ctx.strokeStyle = tint.value;
      ctx.beginPath();
			ctx.moveTo(lastPos.x, lastPos.y);
			ctx.lineTo(mousePos.x, mousePos.y);
      console.log(punta.value);
    	ctx.lineWidth = punta.value;
			ctx.stroke();
      ctx.closePath();
			lastPos = mousePos;
		}
	}

	function clearCanvas() {
		canvas.width = canvas.width;
	}

	// Allow for animation
	(function drawLoop () {
		requestAnimFrame(drawLoop);
		renderCanvas();
	})();

})();    
</script>
<style>

#draw-canvas {
  border: 2px dotted #CCCCCC;
  border-radius: 5px;
  cursor: crosshair;
  background-image: url("CF-SYSTEMS/storage/configuration/fxrma.png");
  background-size: 100% 100%;
}

#draw-dataUrl {
  width: 100%;
}

section{
    flex:1;
}



.button {
    background: #3071a9;
    box-shadow: inset 0 -3px 0 rgba(0,0,0,.3);
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 5px;
    margin: 0 15px;
    text-decoration: none;
    color: white;
}

.button:active {
    transform: scale(0.9);
}

.contenedor {
    width: 100%
    margin: 5px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

input[type=range] {
  -webkit-appearance: none;
  margin: 18px 0;

}
input[type=range]:focus {
  outline: none;
}
input[type=range]::-webkit-slider-runnable-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-webkit-slider-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
  -webkit-appearance: none;
  margin-top: -14px;
}
input[type=range]:focus::-webkit-slider-runnable-track {
  background: #367ebd;
}
input[type=range]::-moz-range-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  background: #3071a9;
  border-radius: 1.3px;
  border: 0.2px solid #010101;
}
input[type=range]::-moz-range-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]::-ms-track {
  width: 100%;
  height: 8.4px;
  cursor: pointer;
  animate: 0.2s;
  background: transparent;
  border-color: transparent;
  border-width: 16px 0;
  color: transparent;
}
input[type=range]::-ms-fill-lower {
  background: #2a6495;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-fill-upper {
  background: #3071a9;
  border: 0.2px solid #010101;
  border-radius: 2.6px;
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
}
input[type=range]::-ms-thumb {
  box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
  border: 1px solid #000000;
  height: 36px;
  width: 16px;
  border-radius: 3px;
  background: #ffffff;
  cursor: pointer;
}
input[type=range]:focus::-ms-fill-lower {
  background: #3071a9;
}
input[type=range]:focus::-ms-fill-upper {
  background: #367ebd;
}
</style>

</div>
    
  
<div class="row my-2" >
                <div class="col-md-6 col-6">
                  <a href="./?view=users&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> <?php 
switch (Core::$user->language){
  case 'ES': echo 'Cancelar'; break;
  case 'EN': echo 'Cancel'; break;
}
?></a>
                </div>
                <div class="col-md-6 col-6">
                  <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $user->id;?>">
                 
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
  case 'ES': echo 'Finalizar'; break;
  case 'EN': echo 'Finish'; break;
}
?></button>
                 
                </div>
              </div>
</form>
  </div>
</div>
</div>
</div>
</div>
   </div><!-- /.container-fluid -->
    </div>   

</section>
<script>
  $("#changepasswd").submit(function(e){
    if($("#password").val() == $("#confirmnewpassword").val()){

                e.preventDefault();
                var formData = jQuery(this).serialize();
               $.ajax({
                  type: "POST",
                  url: "./?action=users&opt=upd",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Usuario Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=users&opt=all'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Actualizar' });
                  }
                  }
                });
                return false;
              

}else{
      e.preventDefault();
      $.jGrowl("La contraseña no coincide con la confirmacion", { header: 'Acceso permitido' });
    }
  
});
</script>
         
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edittype"):?>
<section class="content">
<?php $user = KData::getById($_GET["id"]);?>
<div class="row">
  <div class="col-md-12">
<div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-user-edit'></i> Editar Permiso</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cog'></i> Administracion</li>
              <li class="breadcrumb-item active"><i class='far fa-circle'></i> Usuarios</a></li>
              <li class="breadcrumb-item"><i class='fa fa-list-ol'></i> Tipos</a></li>

            </ol>
          </div><!-- /.col -->
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
    <form class="form-horizontal" enctype="multipart/form-data"   method="post" id="update" role="form">

    <div class="col-md-12 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Nombre'; break;
  case 'EN': echo 'Name'; break;
}
?> <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-user"></i></span>
      <input style="background-color:#333;" type="text" autocomplete="off" name="name" value="<?php echo utf8_decode($user->name);?>" autofocus class="form-control" id="name" required placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Nombre'; break;
  case 'EN': echo 'Name'; break;
}
?>">
    </div>
</div>

  
<div class="row my-2" >
                <div class="col-md-6 col-6">
                  <a href="./?view=users&opt=type" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> <?php 
switch (Core::$user->language){
  case 'ES': echo 'Cancelar'; break;
  case 'EN': echo 'Cancel'; break;
}
?></a>
                </div>
                <div class="col-md-6 col-6">
                  <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $user->id;?>">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
  case 'ES': echo 'Finalizar'; break;
  case 'EN': echo 'Finish'; break;
}
?></button>
                 
                </div>
              </div>
</form>
  </div>
</div>
</div>
</div>
</div>
   </div><!-- /.container-fluid -->
    </div>   

</section>
<script>
        jQuery(document).ready(function(){
            jQuery("#update").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=users&opt=updtype",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Tipo Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=users&opt=type'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Actualizar' });
                  }
                  }
                });
                return false;
              });
            });
            </script>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="newtype"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-list-ol'></i> Agregar Permiso</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cog'></i> Administracion</li>
              <li class="breadcrumb-item active"><i class='far fa-circle'></i> Usuarios</a></li>
              <li class="breadcrumb-item"><i class='fa fa-list-ol'></i> Tipos</a></li>

            </ol>
          </div><!-- /.col -->
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
    <form class="form-horizontal" enctype="multipart/form-data"   method="post" id="addtype" role="form">

    <div class="col-md-12 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
  case 'ES': echo 'Nombre'; break;
  case 'EN': echo 'Name'; break;
}
?> <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text" style="background-color:orange"><i class="fa fa-user"></i></span>
      <input style="background-color:#333;" type="text" autocomplete="off" name="name" value="<?php echo utf8_decode($user->name);?>" autofocus class="form-control" id="name" required placeholder="<?php 
switch (Core::$user->language){
  case 'ES': echo 'Nombre'; break;
  case 'EN': echo 'Name'; break;
}
?>">
    </div>
</div>

  
<div class="row my-2" >
                <div class="col-md-6 col-6">
                  <a href="./?view=users&opt=type" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                  <input style="background-color:#333;" type="hidden" name="user_id" value="<?php echo $user->id;?>">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> Finalizar</button>
                 
                </div>
              </div>
</form>
  </div>
</div>
</div>
</div>
</div>
   </div><!-- /.container-fluid -->
    </div>   

</section>
<script>
        jQuery(document).ready(function(){
            jQuery("#addtype").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=users&opt=addtype",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Tipo Exito!", { sticky: true });
                  $.jGrowl("Se Agrego a la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=users&opt=type'  }, delay); 
                     
                  }else{
                  $.jGrowl("Por favor verifique su datos", { header: 'Error al Actualizar' });
                  }
                  }
                });
                return false;
              });
            });
            </script>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="history"):
$user = UserData::getById($_GET["id"]);
$sells = ACData::getLike($_GET["id"]);
$symbol = StockData::getPrincipal()->currency;
?>
<section class="content">
<div class="row">
  <div class="col-md-12">
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <a href="./?view=users&opt=all" style="color:white;"><h1 class="m-0"><i class='fa fa-arrow-left'></i> Historial del Usuario</h1></a>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cog'></i> Administracion</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Usuarios</a></li>

            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
   
<?php  if(count($sells)>0):?>
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
        <thead>
      <th>Usuario</th>
      <th>Dato</th>
      <th>Fecha</th>
 
      </thead>
      <?php foreach($sells as $sell):?>
        <tr>
           
  <td><?php echo $sell->getUser()->name." ".$sell->getUser()->lastname ;?></td>
  <td><?php echo $sell->accion;?></td>
  <td><?php echo $sell->created_at ;?></td>
        
         
    </tr>
       <?php endforeach; ?>
      </table>
  </div><!-- /.box-body -->
</div><!-- /.box -->
     <?php else:?>
     <div>
         <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Historial</h2>
    <p>No se ha realizado ninguna operacion.</p>
    </div>
</div>
  </div>
   <?php endif; ?>
</div>
 
</div>
</div>
 
</div>

</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="type"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">
  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0"><i class='fa fa-list-ol'></i> Tipos de Usuarios</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cog'></i> Administracion</li>
              <li class="breadcrumb-item active"><i class='far fa-circle'></i> Usuarios</a></li>

            </ol>
          </div><!-- /.col -->
     
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
     
    <?php $users = KData::getAll();
    if(count($users)>0){
      // si hay usuarios
      ?>
      
<div class="card" style="background-color:#222;">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="example1">
      <thead>
      <th><?php 
switch (Core::$user->language){
  case 'ES': echo 'Nombre'; break;
  case 'EN': echo 'Name'; break;
}
?></th>
      </thead>
      <?php foreach($users as $user):?>
        <tr>
      
        <td><?php 
switch (Core::$user->language){
 case 'ES': echo utf8_decode($user->nombre); break;
 case 'EN': echo utf8_decode($user->name); break;
}
?></td>
        
     
       
        </tr>

          
        <?php

    endforeach;
 echo "</table></div></div>";


    }else{?>
       <div>
         <div class="card" style="background-color:#222;">
              <div class="card-header">
    <h2>No hay Tipos</h2>
    <p>No se ha realizado ninguna operacion.</p>
    </div>
</div>
  </div>
    <?php } ?>


  </div>
</div>
   </div><!-- /.row -->
      </div><!-- /.container-fluid -->
<script type="text/javascript">
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6 col-6:eq(0)');
</script>
</section>
<?php endif; ?>