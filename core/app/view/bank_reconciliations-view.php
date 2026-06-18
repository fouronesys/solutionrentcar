<?php if(isset($_SESSION['user_id']) && isset($_GET["opt"]) && $_GET["opt"]=="all"): ?>

<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><i class="fa fa-balance-scale"></i> Conciliaciones Bancarias</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active"><i class="fa fa-home"></i> Inicio</li>
                <li class="breadcrumb-item active"><i class="fa fa-money-check-alt"></i> Finanzas</li>
                <li class="breadcrumb-item"><i class="far fa-circle"></i> Conciliaciones</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <div id="accordion">
        <div class="card card-secondary card-outline" style="background-color:#222;">
          <a class="d-block w-100" data-toggle="collapse" href="#collapseRec" style="color:white;">
            <div class="card-header">
              <h4 class="card-title w-100">
                <i class="fa fa-plus"></i> REGISTRAR NUEVA CONCILIACIÓN
              </h4>
            </div>
          </a>

          <div id="collapseRec" class="collapse" data-parent="#accordion">
            <div class="card-body">
              <form method="post" id="addbankreconciliation" autocomplete="off">
                <div class="row">

                  <div class="col-md-4 col-12">
                    <label>Cuenta Bancaria</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                      <select name="account_id" id="account_id" class="form-control" required>
                        <option value="">SELECCIONAR</option>
                        <?php foreach(BankAccountData::getAll() as $acc): 
                          $bank = $acc->getBank();
                        ?>
                        <option value="<?php echo $acc->id; ?>" data-balance="<?php echo $acc->balance; ?>">
                          <?php echo ($bank ? $bank->name : "")." - ".$acc->account_name." - ".$acc->account_number; ?>
                        </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4 col-12">
                    <label>Balance Banco</label>
                    <div class="input-group">
                      <span class="input-group-text">RD$</span>
                      <input type="number" step="0.01" name="balance_bank" id="balance_bank" class="form-control" value="0" required>
                    </div>
                  </div>

                  <div class="col-md-4 col-12">
                    <label>Balance Sistema</label>
                    <div class="input-group">
                      <span class="input-group-text">RD$</span>
                      <input type="number" step="0.01" name="balance_system" id="balance_system" class="form-control" value="0" required>
                    </div>
                  </div>

                  <div class="col-md-12 col-12">
                    <label>Diferencia</label>
                    <div class="input-group">
                      <span class="input-group-text">RD$</span>
                      <input type="text" id="difference_preview" class="form-control" readonly value="0.00">
                    </div>
                  </div>

                  <div class="card-body row">
                    <div class="col-6">
                      <a href="./?view=bank_reconciliations&opt=all" class="btn btn-warning btn-block btn-sm">
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
                  <th>Balance Banco</th>
                  <th>Balance Sistema</th>
                  <th>Diferencia</th>
                  <th>Estado</th>
                  <th>Fecha</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach(BankReconciliationData::getAll() as $rec): 
                  $acc = $rec->getAccount();
                ?>
                <tr>
                  <td><?php echo $rec->id; ?></td>
                  <td><?php echo $acc ? $acc->account_name : ""; ?></td>
                  <td><?php echo number_format($rec->balance_bank,2,'.',','); ?></td>
                  <td><?php echo number_format($rec->balance_system,2,'.',','); ?></td>
                  <td><?php echo number_format($rec->difference,2,'.',','); ?></td>
                  <td>
                    <?php if(abs($rec->difference) < 0.01): ?>
                      <span class="badge badge-success">CUADRADO</span>
                    <?php else: ?>
                      <span class="badge badge-danger">DIFERENCIA</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo $rec->created_at; ?></td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button type="button"
                              class="btn btn-secondary btn-edit-rec"
                              data-id="<?php echo $rec->id; ?>"
                              data-account_id="<?php echo $rec->account_id; ?>"
                              data-balance_bank="<?php echo $rec->balance_bank; ?>"
                              data-balance_system="<?php echo $rec->balance_system; ?>"
                              data-toggle="modal"
                              data-target="#editRecModal">
                        <i class="fas fa-edit"></i>
                      </button>
                      <a href="./?action=bank_reconciliations&opt=del&id=<?php echo $rec->id; ?>"
                         class="btn btn-danger"
                         onclick="return confirm('¿Seguro que deseas eliminar esta conciliación?');">
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

<div class="modal fade" id="editRecModal">
  <div class="modal-dialog">
    <div class="modal-content bg-secondary">
      <div class="modal-header" style="background-color:#222;">
        <h4 class="modal-title"><i class="fa fa-edit"></i> Editar Conciliación</h4>
        <button type="button" class="close" data-dismiss="modal" style="color:white;">
          <span>&times;</span>
        </button>
      </div>

      <form method="post" id="updbankreconciliation" autocomplete="off">
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
              <label>Balance Banco</label>
              <div class="input-group">
                <span class="input-group-text">RD$</span>
                <input type="number" step="0.01" name="balance_bank" id="edit_balance_bank" class="form-control" required>
              </div>
            </div>

            <div class="col-md-6 col-12 mt-2">
              <label>Balance Sistema</label>
              <div class="input-group">
                <span class="input-group-text">RD$</span>
                <input type="number" step="0.01" name="balance_system" id="edit_balance_system" class="form-control" required>
              </div>
            </div>

            <div class="col-md-12 col-12 mt-2">
              <label>Diferencia</label>
              <div class="input-group">
                <span class="input-group-text">RD$</span>
                <input type="text" id="edit_difference_preview" class="form-control" readonly value="0.00">
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

  function recalcular(){
    let banco = parseFloat($("#balance_bank").val()) || 0;
    let sistema = parseFloat($("#balance_system").val()) || 0;
    let diff = banco - sistema;
    $("#difference_preview").val(diff.toFixed(2));
  }

  function recalcularEdit(){
    let banco = parseFloat($("#edit_balance_bank").val()) || 0;
    let sistema = parseFloat($("#edit_balance_system").val()) || 0;
    let diff = banco - sistema;
    $("#edit_difference_preview").val(diff.toFixed(2));
  }

  $("#account_id").change(function(){
    let balance = $('option:selected', this).data('balance') || 0;
    $("#balance_system").val(balance);
    recalcular();
  });

  $("#balance_bank, #balance_system").on("input", recalcular);
  $("#edit_balance_bank, #edit_balance_system").on("input", recalcularEdit);

  $("#addbankreconciliation").submit(function(e){
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "./?action=bank_reconciliations&opt=add",
      data: $(this).serialize(),
      success: function(res){
        if(res=="true"){
          $.jGrowl("Conciliación registrada correctamente", { header: "Éxito" });
          setTimeout(function(){ window.location='./?view=bank_reconciliations&opt=all'; }, 800);
        }else{
          $.jGrowl("Error al guardar", { header: "Error" });
        }
      }
    });
  });

  $(".btn-edit-rec").click(function(){
    $("#edit_id").val($(this).data("id"));
    $("#edit_account_id").val($(this).data("account_id"));
    $("#edit_balance_bank").val($(this).data("balance_bank"));
    $("#edit_balance_system").val($(this).data("balance_system"));
    recalcularEdit();
  });

  $("#updbankreconciliation").submit(function(e){
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "./?action=bank_reconciliations&opt=upd",
      data: $(this).serialize(),
      success: function(res){
        if(res=="true"){
          $.jGrowl("Conciliación actualizada correctamente", { header: "Éxito" });
          setTimeout(function(){ window.location='./?view=bank_reconciliations&opt=all'; }, 800);
        }else{
          $.jGrowl("Error al actualizar", { header: "Error" });
        }
      }
    });
  });

});
</script>

<?php endif; ?>