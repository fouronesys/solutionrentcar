<?php if(isset($_SESSION['user_id']) && isset($_GET["opt"]) && $_GET["opt"]=="all"): ?>

<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><i class="fa fa-credit-card"></i> Cuentas Bancarias</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active"><i class="fa fa-home"></i> Inicio</li>
                <li class="breadcrumb-item active"><i class="fa fa-money-check-alt"></i> Finanzas</li>
                <li class="breadcrumb-item"><i class="far fa-circle"></i> Cuentas bancarias</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <div id="accordion">
        <div class="card card-secondary card-outline" style="background-color:#222;">
          <a class="d-block w-100" data-toggle="collapse" href="#collapseAccount" style="color:white;">
            <div class="card-header">
              <h4 class="card-title w-100">
                <i class="fa fa-plus"></i> CREAR NUEVA CUENTA BANCARIA
              </h4>
            </div>
          </a>

          <div id="collapseAccount" class="collapse" data-parent="#accordion">
            <div class="card-body">

              <form method="post" id="addbankaccount" autocomplete="off">
                <div class="row">

                  <div class="col-md-4 col-12">
                    <label>Banco</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-university"></i></span>
                      <select name="bank_id" class="form-control" required>
                        <option value="">SELECCIONAR</option>
                        <?php foreach(BankData::getAll() as $b): ?>
                        <option value="<?php echo $b->id; ?>"><?php echo $b->name; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4 col-12">
                    <label>Nombre de la Cuenta</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-file-alt"></i></span>
                      <input type="text" name="account_name" class="form-control" placeholder="Ej. Cuenta Corriente Principal" required>
                    </div>
                  </div>

                  <div class="col-md-4 col-12">
                    <label>Número de Cuenta</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-hashtag"></i></span>
                      <input type="text" name="account_number" class="form-control" placeholder="Número de cuenta" required>
                    </div>
                  </div>

                  <div class="col-md-6 col-12">
                    <label>Moneda</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                      <select name="currency" class="form-control" required>
                        <option value="DOP">PESO DOMINICANO (DOP)</option>
                        <option value="USD">DÓLAR (USD)</option>
                        <option value="EUR">EURO (EUR)</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6 col-12">
                    <label>Balance Inicial</label>
                    <div class="input-group">
                      <span class="input-group-text">RD$</span>
                      <input type="number" step="0.01" name="balance" class="form-control" placeholder="0.00" value="0">
                    </div>
                  </div>

                  <div class="card-body row">
                    <div class="col-6">
                      <a href="./?view=bank_accounts&opt=all" class="btn btn-warning btn-block btn-sm">
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
                  <th>Cuenta</th>
                  <th>Número</th>
                  <th>Moneda</th>
                  <th>Balance</th>
                  <th>Fecha</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach(BankAccountData::getAll() as $acc): 
                  $bank = $acc->getBank();
                ?>
                <tr>
                  <td><?php echo $acc->id; ?></td>
                  <td><?php echo $bank ? $bank->name : ""; ?></td>
                  <td><?php echo $acc->account_name; ?></td>
                  <td><?php echo $acc->account_number; ?></td>
                  <td><?php echo $acc->currency; ?></td>
                  <td><?php echo number_format($acc->balance,2,'.',','); ?></td>
                  <td><?php echo $acc->created_at; ?></td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button type="button"
                              class="btn btn-secondary btn-edit-account"
                              data-id="<?php echo $acc->id; ?>"
                              data-bank_id="<?php echo $acc->bank_id; ?>"
                              data-account_name="<?php echo htmlspecialchars($acc->account_name, ENT_QUOTES); ?>"
                              data-account_number="<?php echo htmlspecialchars($acc->account_number, ENT_QUOTES); ?>"
                              data-currency="<?php echo $acc->currency; ?>"
                              data-balance="<?php echo $acc->balance; ?>"
                              data-toggle="modal"
                              data-target="#editBankAccountModal">
                        <i class="fas fa-edit"></i>
                      </button>
                      <a href="./?action=bank_accounts&opt=del&id=<?php echo $acc->id; ?>"
                         class="btn btn-danger"
                         onclick="return confirm('¿Seguro que deseas eliminar esta cuenta?');">
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

<div class="modal fade" id="editBankAccountModal">
  <div class="modal-dialog">
    <div class="modal-content bg-secondary">
      <div class="modal-header" style="background-color:#222;">
        <h4 class="modal-title"><i class="fa fa-edit"></i> Editar Cuenta Bancaria</h4>
        <button type="button" class="close" data-dismiss="modal" style="color:white;">
          <span>&times;</span>
        </button>
      </div>

      <form method="post" id="updbankaccount" autocomplete="off">
        <div class="modal-body" style="background-color:#222;">
          <div class="row">

            <div class="col-md-12 col-12">
              <label>Banco</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa fa-university"></i></span>
                <select name="bank_id" id="edit_bank_id" class="form-control" required>
                  <?php foreach(BankData::getAll() as $b): ?>
                  <option value="<?php echo $b->id; ?>"><?php echo $b->name; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-md-12 col-12 mt-2">
              <label>Nombre de la Cuenta</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa fa-file-alt"></i></span>
                <input type="text" name="account_name" id="edit_account_name" class="form-control" required>
              </div>
            </div>

            <div class="col-md-12 col-12 mt-2">
              <label>Número de Cuenta</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa fa-hashtag"></i></span>
                <input type="text" name="account_number" id="edit_account_number" class="form-control" required>
              </div>
            </div>

            <div class="col-md-6 col-12 mt-2">
              <label>Moneda</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                <select name="currency" id="edit_currency" class="form-control" required>
                  <option value="DOP">PESO DOMINICANO (DOP)</option>
                  <option value="USD">DÓLAR (USD)</option>
                  <option value="EUR">EURO (EUR)</option>
                </select>
              </div>
            </div>

            <div class="col-md-6 col-12 mt-2">
              <label>Balance</label>
              <div class="input-group">
                <span class="input-group-text">RD$</span>
                <input type="number" step="0.01" name="balance" id="edit_balance" class="form-control" required>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer justify-content-between" style="background-color:#222;">
          <input type="hidden" name="id" id="edit_account_id">
          <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){

  $("#addbankaccount").submit(function(e){
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "./?action=bank_accounts&opt=add",
      data: $(this).serialize(),
      success: function(res){
        if(res=="true"){
          $.jGrowl("Cuenta bancaria agregada correctamente", { header: "Éxito" });
          setTimeout(function(){ window.location='./?view=bank_accounts&opt=all'; }, 800);
        }else{
          $.jGrowl("Error al guardar", { header: "Error" });
        }
      }
    });
  });

  $(".btn-edit-account").click(function(){
    $("#edit_account_id").val($(this).data("id"));
    $("#edit_bank_id").val($(this).data("bank_id"));
    $("#edit_account_name").val($(this).data("account_name"));
    $("#edit_account_number").val($(this).data("account_number"));
    $("#edit_currency").val($(this).data("currency"));
    $("#edit_balance").val($(this).data("balance"));
  });

  $("#updbankaccount").submit(function(e){
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "./?action=bank_accounts&opt=upd",
      data: $(this).serialize(),
      success: function(res){
        if(res=="true"){
          $.jGrowl("Cuenta bancaria actualizada correctamente", { header: "Éxito" });
          setTimeout(function(){ window.location='./?view=bank_accounts&opt=all'; }, 800);
        }else{
          $.jGrowl("Error al actualizar", { header: "Error" });
        }
      }
    });
  });

});
</script>

<?php endif; ?>