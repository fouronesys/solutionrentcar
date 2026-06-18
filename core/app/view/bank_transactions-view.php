<?php if(isset($_SESSION['user_id']) && isset($_GET["opt"]) && $_GET["opt"]=="all"): ?>

<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><i class="fa fa-exchange-alt"></i> Movimientos Bancarios</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active"><i class="fa fa-home"></i> Inicio</li>
                <li class="breadcrumb-item active"><i class="fa fa-money-check-alt"></i> Finanzas</li>
                <li class="breadcrumb-item"><i class="far fa-circle"></i> Movimientos bancarios</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <div id="accordion">
        <div class="card card-secondary card-outline" style="background-color:#222;">
          <a class="d-block w-100" data-toggle="collapse" href="#collapseTransaction" style="color:white;">
            <div class="card-header">
              <h4 class="card-title w-100">
                <i class="fa fa-plus"></i> CREAR NUEVO MOVIMIENTO
              </h4>
            </div>
          </a>

          <div id="collapseTransaction" class="collapse" data-parent="#accordion">
            <div class="card-body">

              <form method="post" id="addbanktransaction" autocomplete="off">
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
                          <?php echo ($bank?$bank->name:"")." - ".$acc->account_name." - ".$acc->account_number; ?>
                        </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4 col-12">
                    <label>Tipo de Transacción</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-random"></i></span>
                      <select name="type" class="form-control" required>
                        <option value="DEPOSITO">DEPÓSITO</option>
                        <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                        <option value="PAGO_SUPLIDOR_LOCAL">PAGO SUPLIDOR LOCAL</option>
                        <option value="PAGO_SUPLIDOR_INTERNACIONAL">PAGO SUPLIDOR INTERNACIONAL</option>
                        <option value="COMISION_BANCARIA">COMISIÓN BANCARIA</option>
                        <option value="NOTA_DEBITO">NOTA DÉBITO</option>
                        <option value="NOTA_CREDITO">NOTA CRÉDITO</option>
                        <option value="AJUSTE">AJUSTE</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4 col-12">
                    <label>Dirección</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-arrow-right"></i></span>
                      <select name="direction" class="form-control" required>
                        <option value="ENTRADA">ENTRADA</option>
                        <option value="SALIDA">SALIDA</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6 col-12">
                    <label>Beneficiario / Suplidor</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-user"></i></span>
                      <select name="person_id" class="form-control select2">
                        <option value="">NINGUNO</option>
                        <?php foreach(PersonData::getAll() as $p): ?>
                        <option value="<?php echo $p->id; ?>">
                          <?php echo isset($p->customer_name) && $p->customer_name!="" ? $p->customer_name : (isset($p->name)?$p->name:""); ?>
                        </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6 col-12">
                    <label>Monto en Divisa</label>
                    <div class="input-group">
                      <span class="input-group-text">Monto</span>
                      <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="0" required>
                    </div>
                  </div>

                  <div class="col-md-4 col-12">
                    <label>Tasa</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                      <input type="number" step="0.0001" name="exchange_rate" id="exchange_rate" class="form-control" value="1">
                    </div>
                  </div>

                  <div class="col-md-4 col-12">
                    <label>% Prima</label>
                    <div class="input-group">
                      <span class="input-group-text">%</span>
                      <input type="number" step="0.01" name="premium_percent" id="premium_percent" class="form-control" value="0">
                    </div>
                  </div>

                  <div class="col-md-4 col-12">
                    <label>Comisión</label>
                    <div class="input-group">
                      <span class="input-group-text">RD$</span>
                      <input type="number" step="0.01" name="fee" id="fee" class="form-control" value="0">
                    </div>
                  </div>

                  <div class="col-md-6 col-12">
                    <label>Prima Calculada</label>
                    <div class="input-group">
                      <span class="input-group-text">RD$</span>
                      <input type="text" id="premium_amount_preview" class="form-control" readonly value="0.00">
                    </div>
                  </div>

                  <div class="col-md-6 col-12">
                    <label>Total Local</label>
                    <div class="input-group">
                      <span class="input-group-text">RD$</span>
                      <input type="text" id="total_local_preview" class="form-control" readonly value="0.00">
                    </div>
                  </div>

                  <div class="col-md-12 col-12">
                    <label>Descripción</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-align-left"></i></span>
                      <textarea name="description" class="form-control" rows="2" placeholder="Descripción del movimiento"></textarea>
                    </div>
                  </div>

                  <div class="card-body row">
                    <div class="col-6">
                      <a href="./?view=bank_transactions&opt=all" class="btn btn-warning btn-block btn-sm">
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
                  <th>Tipo</th>
                  <th>Dirección</th>
                  <th>Monto</th>
                  <th>Prima</th>
                  <th>Comisión</th>
                  <th>Total Local</th>
                  <th>Fecha</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach(BankTransactionData::getAll() as $tx): 
                  $acc = $tx->getAccount();
                ?>
                <tr>
                  <td><?php echo $tx->id; ?></td>
                  <td><?php echo $acc ? $acc->account_name : ""; ?></td>
                  <td><?php echo $tx->type; ?></td>
                  <td>
                    <?php if($tx->direction=="ENTRADA"): ?>
                      <span class="badge badge-success">ENTRADA</span>
                    <?php else: ?>
                      <span class="badge badge-danger">SALIDA</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo number_format($tx->amount,2,'.',','); ?></td>
                  <td><?php echo number_format($tx->premium_amount,2,'.',','); ?></td>
                  <td><?php echo number_format($tx->fee,2,'.',','); ?></td>
                  <td><?php echo number_format($tx->total_local,2,'.',','); ?></td>
                  <td><?php echo $tx->created_at; ?></td>
                  <td>
                    <a href="./?action=bank_transactions&opt=del&id=<?php echo $tx->id; ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Seguro que deseas eliminar este movimiento?');">
                      <i class="fas fa-trash"></i>
                    </a>
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

<script>
$(document).ready(function(){

  function recalcular(){
    let amount = parseFloat($("#amount").val()) || 0;
    let rate = parseFloat($("#exchange_rate").val()) || 1;
    let percent = parseFloat($("#premium_percent").val()) || 0;
    let fee = parseFloat($("#fee").val()) || 0;

    let subtotal = amount * rate;
    let premium = subtotal * (percent / 100);
    let total = subtotal + premium + fee;

    $("#premium_amount_preview").val(premium.toFixed(2));
    $("#total_local_preview").val(total.toFixed(2));
  }

  $("#amount, #exchange_rate, #premium_percent, #fee").on("input", recalcular);
  recalcular();

  $("#addbanktransaction").submit(function(e){
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "./?action=bank_transactions&opt=add",
      data: $(this).serialize(),
      success: function(res){
        if(res=="true"){
          $.jGrowl("Movimiento bancario agregado correctamente", { header: "Éxito" });
          setTimeout(function(){ window.location='./?view=bank_transactions&opt=all'; }, 800);
        }else{
          $.jGrowl("Error al guardar", { header: "Error" });
        }
      }
    });
  });

});
</script>

<?php endif; ?>