<?php if(isset($_SESSION['user_id']) && isset($_GET["opt"]) && $_GET["opt"]=="all"): ?>

<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><i class="fa fa-hand-holding-usd"></i> Pagos a Suplidores</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active"><i class="fa fa-home"></i> Inicio</li>
                <li class="breadcrumb-item active"><i class="fa fa-money-check-alt"></i> Finanzas</li>
                <li class="breadcrumb-item"><i class="far fa-circle"></i> Pagos a suplidores</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <div id="accordion">
        <div class="card card-secondary card-outline" style="background-color:#222;">
          <a class="d-block w-100" data-toggle="collapse" href="#collapseSupplierBankPayment" style="color:white;">
            <div class="card-header">
              <h4 class="card-title w-100">
                <i class="fa fa-plus"></i> REGISTRAR PAGO A SUPLIDOR
              </h4>
            </div>
          </a>

          <div id="collapseSupplierBankPayment" class="collapse" data-parent="#accordion">
            <div class="card-body">

              <form method="post" id="addsupplierbankpayment" autocomplete="off">
                <div class="row">

                  <div class="col-md-4 col-12">
                    <label>Suplidor</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-user"></i></span>
                      <select name="person_id" class="form-control select2" required>
                        <option value="">SELECCIONAR</option>
                        <?php foreach(PersonData::getProviders() as $p): ?>
                        <option value="<?php echo $p->id; ?>">
                          <?php echo $p->name." ".$p->lastname; ?>
                        </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

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
                          <?php echo ($bank?$bank->name:"")." - ".$acc->account_name; ?>
                        </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4 col-12">
                    <label>Factura / Referencia Compra</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-file-invoice"></i></span>
                      <input type="text" name="invoice_ref" class="form-control" placeholder="Ref factura suplidor">
                    </div>
                  </div>

                  <div class="col-md-3 col-12">
                    <label>Moneda</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                      <select name="currency" id="currency" class="form-control" required>
                        <option value="DOP">DOP</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-3 col-12">
                    <label>Tasa</label>
                    <div class="input-group">
                      <span class="input-group-text">T</span>
                      <input type="number" step="0.0001" name="exchange_rate" id="exchange_rate" class="form-control" value="1">
                    </div>
                  </div>

                  <div class="col-md-3 col-12">
                    <label>Monto</label>
                    <div class="input-group">
                      <span class="input-group-text">M</span>
                      <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="0" required>
                    </div>
                  </div>

                  <div class="col-md-3 col-12">
                    <label>Fecha Pago</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                      <input type="date" name="payment_date" class="form-control" value="<?php echo date("Y-m-d"); ?>">
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
                      <input type="number" step="0.01" name="fee_amount" id="fee_amount" class="form-control" value="0">
                    </div>
                  </div>

                  <div class="col-md-4 col-12">
                    <label>Referencia Bancaria</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-hashtag"></i></span>
                      <input type="text" name="reference_no" class="form-control" placeholder="No. transferencia / depósito">
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
                    <label>Notas</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-align-left"></i></span>
                      <textarea name="notes" class="form-control" rows="2" placeholder="Observaciones"></textarea>
                    </div>
                  </div>

                  <div class="card-body row">
                    <div class="col-6">
                      <a href="./?view=supplier_bank_payments&opt=all" class="btn btn-warning btn-block btn-sm">
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
                  <th>Suplidor</th>
                  <th>Cuenta</th>
                  <th>Factura</th>
                  <th>Moneda</th>
                  <th>Monto</th>
                  <th>Prima</th>
                  <th>Comisión</th>
                  <th>Total Local</th>
                  <th>Fecha</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach(SupplierBankPaymentData::getAll() as $pay): 
                  $sup = $pay->getSupplier();
                  $acc = $pay->getAccount();
                ?>
                <tr>
                  <td><?php echo $pay->id; ?></td>
                  <td><?php echo $sup ? $sup->name." ".$sup->lastname : ""; ?></td>
                  <td><?php echo $acc ? $acc->account_name : ""; ?></td>
                  <td><?php echo $pay->invoice_ref; ?></td>
                  <td><?php echo $pay->currency; ?></td>
                  <td><?php echo number_format($pay->amount,2,'.',','); ?></td>
                  <td><?php echo number_format($pay->premium_amount,2,'.',','); ?></td>
                  <td><?php echo number_format($pay->fee_amount,2,'.',','); ?></td>
                  <td><?php echo number_format($pay->total_local,2,'.',','); ?></td>
                  <td><?php echo $pay->payment_date; ?></td>
                  <td>
                    <a href="./?action=supplier_bank_payments&opt=del&id=<?php echo $pay->id; ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Seguro que deseas eliminar este pago?');">
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
    let fee = parseFloat($("#fee_amount").val()) || 0;

    let subtotal = amount * rate;
    let premium = subtotal * (percent / 100);
    let total = subtotal + premium + fee;

    $("#premium_amount_preview").val(premium.toFixed(2));
    $("#total_local_preview").val(total.toFixed(2));
  }

  $("#amount, #exchange_rate, #premium_percent, #fee_amount").on("input", recalcular);
  recalcular();

  $("#addsupplierbankpayment").submit(function(e){
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "./?action=supplier_bank_payments&opt=add",
      data: $(this).serialize(),
      success: function(res){
        if(res=="true"){
          $.jGrowl("Pago a suplidor registrado correctamente", { header: "Éxito" });
          setTimeout(function(){ window.location='./?view=supplier_bank_payments&opt=all'; }, 800);
        }else{
          $.jGrowl("Error al guardar", { header: "Error" });
        }
      }
    });
  });

});
</script>

<?php endif; ?>