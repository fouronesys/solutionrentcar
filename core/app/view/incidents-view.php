<?php if(isset($_SESSION["user_id"])): ?>
<?php
$stock_id = StockData::getPrincipal()->id;
$incs = IncidentData::getAll($stock_id);
$persons = PersonData::getAllBySQL("where stock_id=$stock_id order by name asc");
$cars = CarsData::getAllBySQL("where stock_id=$stock_id order by name asc");
?>

<section class="content">
  <div class="container-fluid">
    <div class="content-header">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><i class="fa fa-exclamation-triangle"></i> Incidencias</h1>
          <small style="color:#bdbdbd;font-weight:800;">Módulo de control · Reportes y seguimiento</small>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item active"><i class="fa fa-bell"></i> Centro de control</li>
          </ol>
        </div>
      </div>
    </div>

    <style>
      .rt-card{
        background:#16181d !important;
        border:1px solid rgba(255,255,255,.07) !important;
        border-radius:18px !important;
        box-shadow:0 10px 28px rgba(0,0,0,.35) !important;
      }
      .rt-card .card-header{
        border-bottom:1px solid rgba(255,255,255,.08) !important;
        background: linear-gradient(90deg, rgba(255, 193, 7, .18), rgba(0,0,0,0)) !important;
      }
      .rt-card .card-title{ color:#fff !important; font-weight:900 !important; }
      .rt-table th{ color:#cfcfcf !important; font-size:12px; }
      .rt-table td{ color:#eaeaea !important; font-weight:800; vertical-align:middle; }
      .rt-badge{ font-weight:900; }
      .rt-btn{
        border-radius:14px !important;
        font-weight:900 !important;
      }
      .modal-content{ background:#16181d !important; color:#fff !important; border-radius:18px !important; border:1px solid rgba(255,255,255,.08) !important; }
      .modal-header{ border-bottom:1px solid rgba(255,255,255,.08) !important; }
      .form-control, select{
        background:#0f1115 !important;
        border:1px solid rgba(255,255,255,.12) !important;
        color:#fff !important;
        border-radius:12px !important;
      }
      label{ color:#bdbdbd; font-weight:800; font-size:12px; }
    </style>

    <div class="card rt-card">
      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-list"></i> Listado</h3>
        <div class="card-tools">
          <button class="btn btn-warning rt-btn" data-toggle="modal" data-target="#modalAddInc">
            <i class="fa fa-plus"></i> Nueva incidencia
          </button>
        </div>
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table id="tbInc" class="table table-bordered rt-table">
            <thead>
              <tr>
                <th style="width:90px;">ID</th>
                <th style="width:140px;">Estado</th>
                <th style="width:120px;">Severidad</th>
                <th>Título</th>
                <th style="width:170px;">Vehículo</th>
                <th style="width:170px;">Cliente</th>
                <th style="width:150px;">Fecha</th>
                <th style="width:180px;">Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($incs as $i): 
                $car = $i->car_id ? $i->getCar() : null;
                $per = $i->person_id ? $i->getPerson() : null;

                $st = $i->status;
                $sv = $i->severity;

                $stClass = ($st=="ABIERTO" ? "badge badge-danger" : ($st=="EN_PROCESO" ? "badge badge-warning" : ($st=="RESUELTO" ? "badge badge-info" : "badge badge-success")));
                $svClass = ($sv=="ALTO" ? "badge badge-danger" : ($sv=="MEDIO" ? "badge badge-warning" : "badge badge-info"));
              ?>
              <tr>
                <td>#<?php echo $i->id; ?></td>
                <td><span class="<?php echo $stClass; ?> rt-badge"><?php echo $st; ?></span></td>
                <td><span class="<?php echo $svClass; ?> rt-badge"><?php echo $sv; ?></span></td>
                <td>
                  <b><?php echo htmlspecialchars($i->title); ?></b><br>
                  <small style="color:#9aa0a6;font-weight:800;"><?php echo htmlspecialchars(substr($i->description,0,80)); ?></small>
                </td>
                <td><?php echo $car ? htmlspecialchars($car->name." (".$car->plate.")") : "-"; ?></td>
                <td><?php echo $per ? htmlspecialchars($per->name) : "-"; ?></td>
                <td><?php echo date("d-m-Y h:i a", strtotime($i->created_at)); ?></td>
                <td>
                  <button class="btn btn-sm btn-info rt-btn"
                    onclick="fillEdit(
                      '<?php echo $i->id; ?>',
                      '<?php echo htmlspecialchars($i->code ?? '',ENT_QUOTES); ?>',
                      '<?php echo htmlspecialchars($i->title,ENT_QUOTES); ?>',
                      '<?php echo htmlspecialchars($i->description ?? '',ENT_QUOTES); ?>',
                      '<?php echo htmlspecialchars($i->category ?? '',ENT_QUOTES); ?>',
                      '<?php echo $i->severity; ?>',
                      '<?php echo $i->status; ?>',
                      '<?php echo $i->cost; ?>',
                      '<?php echo $i->due_date; ?>',
                      '<?php echo $i->person_id; ?>',
                      '<?php echo $i->car_id; ?>',
                      '<?php echo $i->booking_id; ?>',
                      '<?php echo $i->maintenance_id; ?>'
                    )"
                    data-toggle="modal" data-target="#modalEditInc">
                    <i class="fa fa-edit"></i>
                  </button>

                  <?php if($i->status!="CERRADO"): ?>
                    <a class="btn btn-sm btn-success rt-btn" href="./?action=closeincident&id=<?php echo $i->id; ?>">
                      <i class="fa fa-check"></i>
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- =========================
         MODAL ADD
    ========================= -->
    <div class="modal fade" id="modalAddInc">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="post" action="./?action=addincident">
            <div class="modal-header">
              <h4 class="modal-title"><i class="fa fa-plus"></i> Nueva incidencia</h4>
              <button type="button" class="close text-white" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
              <div class="row">
                <div class="col-md-3 mb-2">
                  <label>Código</label>
                  <input type="text" name="code" class="form-control" placeholder="INC-001">
                </div>
                <div class="col-md-5 mb-2">
                  <label>Categoría</label>
                  <input type="text" name="category" class="form-control" placeholder="Daño / Multa / Reclamo / Pago...">
                </div>
                <div class="col-md-2 mb-2">
                  <label>Severidad</label>
                  <select name="severity" class="form-control">
                    <option value="LEVE">LEVE</option>
                    <option value="MEDIO">MEDIO</option>
                    <option value="ALTO">ALTO</option>
                  </select>
                </div>
                <div class="col-md-2 mb-2">
                  <label>Estado</label>
                  <select name="status" class="form-control">
                    <option value="ABIERTO">ABIERTO</option>
                    <option value="EN_PROCESO">EN_PROCESO</option>
                    <option value="RESUELTO">RESUELTO</option>
                    <option value="CERRADO">CERRADO</option>
                  </select>
                </div>

                <div class="col-md-12 mb-2">
                  <label>Título</label>
                  <input type="text" name="title" class="form-control" required>
                </div>

                <div class="col-md-12 mb-2">
                  <label>Descripción</label>
                  <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="col-md-4 mb-2">
                  <label>Cliente</label>
                  <select name="person_id" class="form-control select2">
                    <option value="">-- Seleccione --</option>
                    <?php foreach($persons as $p): ?>
                      <option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-4 mb-2">
                  <label>Vehículo</label>
                  <select name="car_id" class="form-control select2">
                    <option value="">-- Seleccione --</option>
                    <?php foreach($cars as $c): ?>
                      <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name." (".$c->plate.")"); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-2 mb-2">
                  <label>Costo</label>
                  <input type="number" step="0.01" name="cost" class="form-control" value="0">
                </div>

                <div class="col-md-2 mb-2">
                  <label>Vence</label>
                  <input type="date" name="due_date" class="form-control">
                </div>

                <div class="col-md-4 mb-2">
                  <label>Numero de factura (opcional)</label>
                  <input type="number" name="booking_id" class="form-control">
                </div>

                <div class="col-md-4 mb-2">
                  <label>Numero de mantenimiento (opcional)</label>
                  <input type="number" name="maintenance_id" class="form-control">
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary rt-btn" data-dismiss="modal">Cancelar</button>
              <button class="btn btn-warning rt-btn"><i class="fa fa-save"></i> Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>


    <!-- =========================
         MODAL EDIT
    ========================= -->
    <div class="modal fade" id="modalEditInc">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="post" action="./?action=updateincident">
            <input type="hidden" name="id" id="ei_id">
            <div class="modal-header">
              <h4 class="modal-title"><i class="fa fa-edit"></i> Editar incidencia</h4>
              <button type="button" class="close text-white" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
              <div class="row">
                <div class="col-md-3 mb-2">
                  <label>Código</label>
                  <input type="text" name="code" id="ei_code" class="form-control">
                </div>
                <div class="col-md-5 mb-2">
                  <label>Categoría</label>
                  <input type="text" name="category" id="ei_category" class="form-control">
                </div>
                <div class="col-md-2 mb-2">
                  <label>Severidad</label>
                  <select name="severity" id="ei_severity" class="form-control">
                    <option value="LEVE">LEVE</option>
                    <option value="MEDIO">MEDIO</option>
                    <option value="ALTO">ALTO</option>
                  </select>
                </div>
                <div class="col-md-2 mb-2">
                  <label>Estado</label>
                  <select name="status" id="ei_status" class="form-control">
                    <option value="ABIERTO">ABIERTO</option>
                    <option value="EN_PROCESO">EN_PROCESO</option>
                    <option value="RESUELTO">RESUELTO</option>
                    <option value="CERRADO">CERRADO</option>
                  </select>
                </div>

                <div class="col-md-12 mb-2">
                  <label>Título</label>
                  <input type="text" name="title" id="ei_title" class="form-control" required>
                </div>

                <div class="col-md-12 mb-2">
                  <label>Descripción</label>
                  <textarea name="description" id="ei_description" class="form-control" rows="3"></textarea>
                </div>

                <div class="col-md-4 mb-2">
                  <label>Cliente</label>
                  <select name="person_id" id="ei_person_id" class="form-control">
                    <option value="">-- Seleccione --</option>
                    <?php foreach($persons as $p): ?>
                      <option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-4 mb-2">
                  <label>Vehículo</label>
                  <select name="car_id" id="ei_car_id" class="form-control">
                    <option value="">-- Seleccione --</option>
                    <?php foreach($cars as $c): ?>
                      <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name." (".$c->plate.")"); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-2 mb-2">
                  <label>Costo</label>
                  <input type="number" step="0.01" name="cost" id="ei_cost" class="form-control">
                </div>

                <div class="col-md-2 mb-2">
                  <label>Vence</label>
                  <input type="date" name="due_date" id="ei_due_date" class="form-control">
                </div>

                <div class="col-md-4 mb-2">
                  <label>booking_id</label>
                  <input type="number" name="booking_id" id="ei_booking_id" class="form-control">
                </div>

                <div class="col-md-4 mb-2">
                  <label>maintenance_id</label>
                  <input type="number" name="maintenance_id" id="ei_maintenance_id" class="form-control">
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary rt-btn" data-dismiss="modal">Cancelar</button>
              <button class="btn btn-warning rt-btn"><i class="fa fa-save"></i> Guardar cambios</button>
            </div>
          </form>
        </div>
      </div>
    </div>


  </div>
</section>

<script>
function fillEdit(id,code,title,desc,cat,sev,st,cost,due,person_id,car_id,booking_id,maintenance_id){
  document.getElementById("ei_id").value = id;
  document.getElementById("ei_code").value = code || '';
  document.getElementById("ei_title").value = title || '';
  document.getElementById("ei_description").value = desc || '';
  document.getElementById("ei_category").value = cat || '';
  document.getElementById("ei_severity").value = sev || 'LEVE';
  document.getElementById("ei_status").value = st || 'ABIERTO';
  document.getElementById("ei_cost").value = cost || 0;
  document.getElementById("ei_due_date").value = due || '';
  document.getElementById("ei_person_id").value = person_id || '';
  document.getElementById("ei_car_id").value = car_id || '';
  document.getElementById("ei_booking_id").value = booking_id || '';
  document.getElementById("ei_maintenance_id").value = maintenance_id || '';
}

$(function () {
  $('#tbInc').DataTable({
    "responsive": true,
    "lengthChange": false,
    "autoWidth": false
  });
});
</script>

<?php endif; ?>
