<?php if(isset($_SESSION['user_id']) && isset($_GET["opt"]) && $_GET["opt"]=="all"): ?>

<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><i class="fa fa-university"></i> Bancos</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active"><i class="fa fa-home"></i> Inicio</li>
                <li class="breadcrumb-item active"><i class="fa fa-money-check-alt"></i> Finanzas</li>
                <li class="breadcrumb-item"><i class="far fa-circle"></i> Bancos</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <div id="accordion">
        <div class="card card-secondary card-outline" style="background-color:#222;">
          <a class="d-block w-100" data-toggle="collapse" href="#collapseBank" style="color:white;">
            <div class="card-header">
              <h4 class="card-title w-100">
                <i class="fa fa-plus"></i> CREAR NUEVO BANCO
              </h4>
            </div>
          </a>

          <div id="collapseBank" class="collapse" data-parent="#accordion">
            <div class="card-body">

              <form method="post" id="addbank" autocomplete="off">
                <div class="row">

                  <div class="col-md-8 col-12">
                    <label>Nombre del Banco</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-university"></i></span>
                      <input type="text" name="name" class="form-control" placeholder="Ej. Banreservas" required>
                    </div>
                  </div>

                  <div class="col-md-4 col-12">
                    <br>
                    <label>
                      <div class="form-group clearfix">
                        <div class="icheck-primary d-inline">
                          <input type="checkbox" id="status_add" name="status" checked>
                          <label for="status_add">¿Activo?</label>
                        </div>
                      </div>
                    </label>
                  </div>

                  <div class="card-body row">
                    <div class="col-6">
                      <a href="./?view=banks&opt=all" class="btn btn-warning btn-block btn-sm">
                        <i class="fa fa-times"></i> Cancelar
                      </a>
                    </div>
                    <div class="col-6">
                      <button type="submit" class="btn btn-primary btn-block btn-sm">
                        <i class="fa fa-check"></i> Guardar
                      </button>
                    </div>
                  </div>

                </div>
              </form>

            </div>
          </div>
        </div>
      </div>

      <div class="card mt-3" style="background-color:#222;">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="example1">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Banco</th>
                  <th>Estado</th>
                  <th>Fecha</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach(BankData::getAll() as $bank): ?>
                <tr>
                  <td><?php echo $bank->id; ?></td>
                  <td><?php echo $bank->name; ?></td>
                  <td>
                    <?php if($bank->status): ?>
                      <span class="badge badge-success">ACTIVO</span>
                    <?php else: ?>
                      <span class="badge badge-danger">INACTIVO</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo $bank->created_at; ?></td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button type="button"
                              class="btn btn-secondary btn-edit-bank"
                              data-id="<?php echo $bank->id; ?>"
                              data-name="<?php echo htmlspecialchars($bank->name, ENT_QUOTES); ?>"
                              data-status="<?php echo $bank->status; ?>"
                              data-toggle="modal"
                              data-target="#editBankModal">
                        <i class="fas fa-edit"></i>
                      </button>
                      <a href="./?action=banks&opt=del&id=<?php echo $bank->id; ?>"
                         class="btn btn-danger"
                         onclick="return confirm('¿Seguro que deseas eliminar este banco?');">
                        <i class="fas fa-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<div class="modal fade" id="editBankModal">
  <div class="modal-dialog">
    <div class="modal-content bg-secondary">
      <div class="modal-header" style="background-color:#222;">
        <h4 class="modal-title"><i class="fa fa-edit"></i> Editar Banco</h4>
        <button type="button" class="close" data-dismiss="modal" style="color:white;">
          <span>&times;</span>
        </button>
      </div>

      <form method="post" id="updbank" autocomplete="off">
        <div class="modal-body" style="background-color:#222;">
          <div class="row">
            <div class="col-md-12 col-12">
              <label>Nombre del Banco</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa fa-university"></i></span>
                <input type="text" name="name" id="edit_name" class="form-control" required>
              </div>
            </div>

            <div class="col-md-12 col-12 mt-3">
              <label>
                <div class="form-group clearfix">
                  <div class="icheck-primary d-inline">
                    <input type="checkbox" id="edit_status" name="status">
                    <label for="edit_status">¿Activo?</label>
                  </div>
                </div>
              </label>
            </div>
          </div>
        </div>

        <div class="modal-footer justify-content-between" style="background-color:#222;">
          <input type="hidden" name="id" id="edit_id">
          <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){

  $("#addbank").submit(function(e){
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "./?action=banks&opt=add",
      data: $(this).serialize(),
      success: function(res){
        if(res=="true"){
          $.jGrowl("Banco agregado correctamente", { header: "Éxito" });
          setTimeout(function(){ window.location='./?view=banks&opt=all'; }, 800);
        }else{
          $.jGrowl("Error al guardar", { header: "Error" });
        }
      }
    });
  });

  $(".btn-edit-bank").click(function(){
    $("#edit_id").val($(this).data("id"));
    $("#edit_name").val($(this).data("name"));
    $("#edit_status").prop("checked", $(this).data("status")==1);
  });

  $("#updbank").submit(function(e){
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "./?action=banks&opt=upd",
      data: $(this).serialize(),
      success: function(res){
        if(res=="true"){
          $.jGrowl("Banco actualizado correctamente", { header: "Éxito" });
          setTimeout(function(){ window.location='./?view=banks&opt=all'; }, 800);
        }else{
          $.jGrowl("Error al actualizar", { header: "Error" });
        }
      }
    });
  });

});
</script>

<?php endif; ?>