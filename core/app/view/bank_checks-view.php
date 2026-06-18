<?php if(isset($_SESSION['user_id']) && isset($_GET["opt"]) && $_GET["opt"]=="all"): ?>

<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><i class="fa fa-money-check"></i> Cheques</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active"><i class="fa fa-home"></i> Inicio</li>
                <li class="breadcrumb-item active"><i class="fa fa-money-check-alt"></i> Finanzas</li>
                <li class="breadcrumb-item"><i class="far fa-circle"></i> Cheques</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <div id="accordion">
        <div class="card card-secondary card-outline" style="background-color:#222;">
          <a class="d-block w-100" data-toggle="collapse" href="#collapseCheck" style="color:white;">
            <div class="card-header">
              <h4 class="card-title w-100">
                <i class="fa fa-plus"></i> REGISTRAR NUEVO CHEQUE
              </h4>
            </div>
          </a>

          <div id="collapseCheck" class="collapse" data-parent="#accordion">
            <div class="card-body">
              <form method="post" id="addbankcheck" autocomplete="off">
                <div class="row">

                  <div class="col-md-4 col-12">
                    <label>Cuenta Bancaria</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                      <select name="account_id" class="form-control" required>
                        <option value="">SELECCIONAR</option>
                        <?php foreach(BankAccountData::getAll() as $acc): 
                          $bank = $acc->getBank();
                        ?>
                        <option value="<?php echo $acc->id; ?>">
                          <?php echo ($bank ? $bank->name : "")." - ".$acc->account_name." - ".$acc->account_number; ?>
                        </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4 col-12">
                    <label>Número de Cheque</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-hashtag"></i></span>
                      <input type="text" name="check_number" class="form-control" placeholder="No. cheque" required>
                    </div>
                  </div>

                  <div class="col-md-4 col-12">
                    <label>Fecha</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                      <input type="date" name="issue_date" class="form-control" value="<?php echo date("Y-m-d"); ?>" required>
                    </div>
                  </div>

                  <div class="col-md-6 col-12">
                    <label>Beneficiario</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-user"></i></span>
                      <input type="text" name="pay_to" class="form-control" placeholder="Nombre del beneficiario" required>
                    </div>
                  </div>

                  <div class="col-md-3 col-12">
                    <label>Monto</label>
                    <div class="input-group">
                      <span class="input-group-text">RD$</span>
                      <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                    </div>
                  </div>

                  <div class="col-md-3 col-12">
                    <label>Estado</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-flag"></i></span>
                      <select name="status" class="form-control" required>
                        <option value="EMITIDO">EMITIDO</option>
                        <option value="COBRADO">COBRADO</option>
                        <option value="ANULADO">ANULADO</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-12 col-12">
                    <label>Concepto</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-align-left"></i></span>
                      <textarea name="concept" class="form-control" rows="2" placeholder="Concepto del cheque"></textarea>
                    </div>
                  </div>

                  <div class="card-body row">
                    <div class="col-6">
                      <a href="./?view=bank_checks&opt=all" class="btn btn-warning btn-block btn-sm">
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
                  <th>Cuenta</th>
                  <th>No. Cheque</th>
                  <th>Beneficiario</th>
                  <th>Monto</th>
                  <th>Fecha</th>
                  <th>Estado</th>
                  <th>Concepto</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach(BankCheckData::getAll() as $check): 
                  $acc = $check->getAccount();
                ?>
                <tr>
                  <td><?php echo $check->id; ?></td>
                  <td><?php echo $acc ? $acc->account_name : ""; ?></td>
                  <td><?php echo $check->check_number; ?></td>
                  <td><?php echo $check->pay_to; ?></td>
                  <td><?php echo number_format($check->amount,2,'.',','); ?></td>
                  <td><?php echo $check->issue_date; ?></td>
                  <td>
                    <?php if($check->status=="EMITIDO"): ?>
                      <span class="badge badge-warning">EMITIDO</span>
                    <?php elseif($check->status=="COBRADO"): ?>
                      <span class="badge badge-success">COBRADO</span>
                    <?php else: ?>
                      <span class="badge badge-danger">ANULADO</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo $check->concept; ?></td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button type="button"
                              class="btn btn-secondary btn-edit-check"
                              data-id="<?php echo $check->id; ?>"
                              data-account_id="<?php echo $check->account_id; ?>"
                              data-check_number="<?php echo htmlspecialchars($check->check_number, ENT_QUOTES); ?>"
                              data-pay_to="<?php echo htmlspecialchars($check->pay_to, ENT_QUOTES); ?>"
                              data-amount="<?php echo $check->amount; ?>"
                              data-issue_date="<?php echo $check->issue_date; ?>"
                              data-status="<?php echo $check->status; ?>"
                              data-concept="<?php echo htmlspecialchars($check->concept, ENT_QUOTES); ?>"
                              data-toggle="modal"
                              data-target="#editCheckModal">
                        <i class="fas fa-edit"></i>
                      </button>
                      <a href="./?action=bank_checks&opt=del&id=<?php echo $check->id; ?>"
                         class="btn btn-danger"
                         onclick="return confirm('¿Seguro que deseas eliminar este cheque?');">
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

<div class="modal fade" id="editCheckModal">
  <div class="modal-dialog">
    <div class="modal-content bg-secondary">
      <div class="modal-header" style="background-color:#222;">
        <h4 class="modal-title"><i class="fa fa-edit"></i> Editar Cheque</h4>
        <button type="button" class="close" data-dismiss="modal" style="color:white;">
          <span>&times;</span>
        </button>
      </div>

      <form method="post" id="updbankcheck" autocomplete="off">
        <div class="modal-body" style="background-color:#222;">
          <div class="row">

            <div class="col-md-12 col-12">
              <label>Cuenta Bancaria</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                <select name="account_id" id="edit_account_id" class="form-control" required>
                  <?php foreach(BankAccountData::getAll() as $acc): 
                    $bank = $acc->getBank();
                  ?>
                  <option value="<?php echo $acc->id; ?>">
                    <?php echo ($bank ? $bank->name : "")." - ".$acc->account_name." - ".$acc->account_number; ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-md-6 col-12 mt-2">
              <label>No. Cheque</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa fa-hashtag"></i></span>
                <input type="text" name="check_number" id="edit_check_number" class="form-control" required>
              </div>
            </div>

            <div class="col-md-6 col-12 mt-2">
              <label>Fecha</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                <input type="date" name="issue_date" id="edit_issue_date" class="form-control" required>
              </div>
            </div>

            <div class="col-md-12 col-12 mt-2">
              <label>Beneficiario</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa fa-user"></i></span>
                <input type="text" name="pay_to" id="edit_pay_to" class="form-control" required>
              </div>
            </div>

            <div class="col-md-6 col-12 mt-2">
              <label>Monto</label>
              <div class="input-group">
                <span class="input-group-text">RD$</span>
                <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control" required>
              </div>
            </div>

            <div class="col-md-6 col-12 mt-2">
              <label>Estado</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa fa-flag"></i></span>
                <select name="status" id="edit_status" class="form-control" required>
                  <option value="EMITIDO">EMITIDO</option>
                  <option value="COBRADO">COBRADO</option>
                  <option value="ANULADO">ANULADO</option>
                </select>
              </div>
            </div>

            <div class="col-md-12 col-12 mt-2">
              <label>Concepto</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa fa-align-left"></i></span>
                <textarea name="concept" id="edit_concept" class="form-control" rows="2"></textarea>
              </div>
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

  $("#addbankcheck").submit(function(e){
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "./?action=bank_checks&opt=add",
      data: $(this).serialize(),
      success: function(res){
        if(res=="true"){
          $.jGrowl("Cheque registrado correctamente", { header: "Éxito" });
          setTimeout(function(){ window.location='./?view=bank_checks&opt=all'; }, 800);
        }else{
          $.jGrowl("Error al guardar", { header: "Error" });
        }
      }
    });
  });

  $(".btn-edit-check").click(function(){
    $("#edit_id").val($(this).data("id"));
    $("#edit_account_id").val($(this).data("account_id"));
    $("#edit_check_number").val($(this).data("check_number"));
    $("#edit_pay_to").val($(this).data("pay_to"));
    $("#edit_amount").val($(this).data("amount"));
    $("#edit_issue_date").val($(this).data("issue_date"));
    $("#edit_status").val($(this).data("status"));
    $("#edit_concept").val($(this).data("concept"));
  });

  $("#updbankcheck").submit(function(e){
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "./?action=bank_checks&opt=upd",
      data: $(this).serialize(),
      success: function(res){
        if(res=="true"){
          $.jGrowl("Cheque actualizado correctamente", { header: "Éxito" });
          setTimeout(function(){ window.location='./?view=bank_checks&opt=all'; }, 800);
        }else{
          $.jGrowl("Error al actualizar", { header: "Error" });
        }
      }
    });
  });

});
</script>

<?php endif; ?>