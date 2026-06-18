<section class="content">
<?php $user = UserData::getById($_SESSION["user_id"]);?>
<div class="row">
  <div class="col-md-12">
<div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-user-edit'></i> Editar Usuarios</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Inicio</li>
              <li class="breadcrumb-item active"><i class='fa fa-cog'></i> Administracion</li>
              <li class="breadcrumb-item"><i class='far fa-circle'></i> Usuarios</a></li>

            </ol>
          </div><!-- /.col -->
        </div>
  <div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Nota:</h5>
              Favor actualizar la Contrase&ntilde;a o el Usuario (admin) a un Usuario o Contrase&ntilde;a mas segura. 
            </div>
  <div class="card">
<div class="card-body">
    <form class="form-horizontal" enctype="multipart/form-data"   method="post" id="changepasswd" role="form">


<div class="row">
     
    <div class="col-md-6 col-6">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text"><i class="fa fa-user"></i></span>
      <input type="text" name="name" value="<?php echo utf8_decode($user->name);?>" autofocus class="form-control" id="name" required placeholder="Nombre">
    </div>
</div>
<div class="col-md-6 col-6">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Apellido</label>
     <div class="input-group">
  <span class="input-group-text"><i class="fa fa-user"></i></span>
      <input type="text" name="lastname" class="form-control" id="lastname" value="<?php echo $user->lastname;?>" placeholder="Apellido">
    </div>
</div>
</div>

<div class="row">
     
    <div class="col-md-6 col-6">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre de usuario <span class="text-danger">*</span></label>
 <div class="input-group">
  <span class="input-group-text"><i class="fa fa-spinner"></i></span>
      <input type="text" name="username" class="form-control" value="<?php echo $user->username;?>" required id="username" placeholder="Nombre de usuario">
    </div>
</div>

    <div class="col-md-6 col-6">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Email</label>
     <div class="input-group">
  <span class="input-group-text"><i class="fa fa-comments"></i></span>
      <input type="text" name="email" class="form-control" id="email" value="<?php echo $user->email1;?>" placeholder="Email">
    </div>
</div>
</div>

<div class="row">
     
 <div class="col-md-6 col-6">
    <label for="inputPassword1" class="col-md-12 col-12 control-label">Contrase&ntilde;a <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text"><a type="button"  data-toggle="modal" data-target="#myModal"><i class="fa fa-asterisk"></i></a></span>
    <input type="password" class="form-control"   id="password" name="password" placeholder="Contraseña">
    </div>
    <p class="help-block">La contrase&ntilde;a solo se modificara si escribes algo, en caso contrario no se modifica.</p>
  </div>

      <div class="col-md-6 col-6">
    <label for="inputPassword1" class="col-md-6 col-6 control-label">Confirmar Contrase&ntilde;a <span class="text-danger">*</span></label>
     <div class="input-group">
  <span class="input-group-text"><i class="fa fa-edit"></i></span>
      <input type="password" class="form-control" id="confirmnewpassword"  placeholder="Confirmar Contraseña">
    </div>
</div>
</div>

  
<div class="row my-2" >
                <div class="col-md-6 col-6">
                  <a href="./?view=users&opt=all" class="btn btn-warning btn-block btn-sm"><i class='fa fa-times'></i> Cancelar</a>
                </div>
                <div class="col-md-6 col-6">
                  <input type="hidden" name="user_id" value="<?php echo $user->id;?>">
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
  $("#changepasswd").submit(function(e){

if($("#password").val() == "admin" || $("#password").val() == "Admin"){
e.preventDefault();
$.jGrowl("La Contraseña no puede ser Admin", { header: 'Acceso permitido' });

}else if($("#username").val() == "admin" || $("#username").val() == "Admin"){
e.preventDefault();
$.jGrowl("El Usuario no puede ser Admin", { header: 'Acceso permitido' });

}else{

    if($("#password").val() == $("#confirmnewpassword").val()){

                e.preventDefault();
                var formData = jQuery(this).serialize();
               $.ajax({
                  type: "POST",
                  url: "./?action=users&opt=updprofile",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Usuario Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=home'  }, delay); 
                     
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
}  
});
            </script>

