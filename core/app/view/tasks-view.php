<?php if(isset($_SESSION["user_id"])): ?>
<?php
$stock_id = StockData::getPrincipal()->id;
$tasks = TaskData::getAll($stock_id);

function rt_task_badge($st){
  if($st=="PENDIENTE") return "badge badge-danger";
  if($st=="EN_PROCESO") return "badge badge-warning";
  if($st=="POSPUESTO") return "badge badge-info";
  return "badge badge-success";
}
function rt_task_prio($p){
  if($p=="ALTA") return "badge badge-danger";
  if($p=="MEDIA") return "badge badge-warning";
  return "badge badge-secondary";
}
?>

<section class="content">
  <div class="container-fluid">
    <div class="content-header">
      <div class="row mb-2">
        <div class="col-sm-7">
          <h1 class="m-0"><i class="fa fa-check-square"></i> Tareas Automáticas</h1>
          <small style="color:#bdbdbd;font-weight:800;">Módulo nuevo · To-Do Inteligente (acciones sugeridas)</small>
        </div>
        <div class="col-sm-5">
          <div class="float-sm-right">
            <a href="./?action=gentasks" class="btn btn-warning" style="border-radius:14px;font-weight:900;">
              <i class="fa fa-magic"></i> Generar sugeridas
            </a>
            <button class="btn btn-info" data-toggle="modal" data-target="#modalAddTask" style="border-radius:14px;font-weight:900;">
              <i class="fa fa-plus"></i> Crear manual
            </button>
          </div>
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
      .rt-item{
        background:#0f1115;
        border:1px solid rgba(255,255,255,.08);
        border-radius:16px;
        padding:12px 14px;
        margin-bottom:10px;
      }
      .rt-title{ color:#fff; font-weight:900; margin:0; }
      .rt-desc{ color:#cfcfcf; font-weight:800; margin:6px 0 0 0; line-height:1.35; }
      .rt-meta{ color:#9aa0a6; font-weight:800; font-size:12px; }
      .rt-btn{ border-radius:14px !important; font-weight:900 !important; }
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
        <h3 class="card-title"><i class="fa fa-list"></i> Lista de tareas</h3>
        <div class="card-tools">
          <span class="badge badge-dark" style="font-weight:900;"><?php echo count($tasks); ?> tarea(s)</span>
        </div>
      </div>

      <div class="card-body">
        <?php if(count($tasks)==0): ?>
          <div class="rt-item">
            <p class="rt-title"><i class="fa fa-check"></i> Todo está al día</p>
            <p class="rt-desc">No hay tareas pendientes. Si quieres, pulsa “Generar sugeridas”.</p>
          </div>
        <?php endif; ?>

        <?php foreach($tasks as $t): ?>
          <div class="rt-item">
            <div class="d-flex align-items-center justify-content-between">
              <p class="rt-title">
                <?php echo htmlspecialchars($t->title); ?>
                <?php if($t->source_type=="AUTO"): ?>
                  <span class="badge badge-success" style="font-weight:900;margin-left:6px;">AUTO</span>
                <?php endif; ?>
              </p>
              <div>
                <span class="<?php echo rt_task_prio($t->priority); ?>" style="font-weight:900;"><?php echo $t->priority; ?></span>
                <span class="<?php echo rt_task_badge($t->status); ?>" style="font-weight:900;"><?php echo $t->status; ?></span>
              </div>
            </div>

            <?php if(trim($t->description)!=""): ?>
              <p class="rt-desc"><?php echo $t->description; ?></p>
            <?php endif; ?>

            <div class="rt-meta mt-2">
              Creada: <?php echo date("d-m-Y h:i a", strtotime($t->created_at)); ?>
              <?php if($t->due_date): ?> · Vence: <b style="color:#fff;"><?php echo date("d-m-Y", strtotime($t->due_date)); ?></b><?php endif; ?>
              <?php if($t->done_at): ?> · Hecho: <b style="color:#fff;"><?php echo date("d-m-Y h:i a", strtotime($t->done_at)); ?></b><?php endif; ?>
            </div>

            <div class="mt-2">
              <?php if($t->status!="HECHO"): ?>
                <a class="btn btn-success btn-sm rt-btn" href="./?action=taskdone&id=<?php echo $t->id; ?>"><i class="fa fa-check"></i> Hecho</a>
                <a class="btn btn-info btn-sm rt-btn" href="./?action=taskpostpone&id=<?php echo $t->id; ?>&days=3"><i class="fa fa-clock"></i> Posponer 3d</a>
              <?php else: ?>
                <a class="btn btn-warning btn-sm rt-btn" href="./?action=taskreopen&id=<?php echo $t->id; ?>"><i class="fa fa-undo"></i> Reabrir</a>
              <?php endif; ?>

              <button class="btn btn-primary btn-sm rt-btn" data-toggle="modal" data-target="#modalEditTask"
                onclick="fillTask(
                  '<?php echo $t->id; ?>',
                  '<?php echo htmlspecialchars($t->title,ENT_QUOTES); ?>',
                  '<?php echo htmlspecialchars($t->description ?? "",ENT_QUOTES); ?>',
                  '<?php echo $t->priority; ?>',
                  '<?php echo $t->status; ?>',
                  '<?php echo $t->due_date; ?>'
                )">
                <i class="fa fa-edit"></i> Editar
              </button>

              <a class="btn btn-danger btn-sm rt-btn" href="./?action=deltask&id=<?php echo $t->id; ?>"
                 onclick="return confirm('¿Eliminar esta tarea?');">
                 <i class="fa fa-trash"></i>
              </a>

              <?php if($t->ref_table && $t->ref_id): ?>
                <span class="badge badge-dark" style="font-weight:900;">
                  Ref: <?php echo htmlspecialchars($t->ref_table); ?> #<?php echo intval($t->ref_id); ?>
                </span>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>


    <!-- MODAL ADD -->
    <div class="modal fade" id="modalAddTask">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="post" action="./?action=addtask">
            <div class="modal-header">
              <h4 class="modal-title"><i class="fa fa-plus"></i> Crear tarea manual</h4>
              <button type="button" class="close text-white" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-8 mb-2">
                  <label>Título</label>
                  <input type="text" name="title" class="form-control" required>
                </div>
                <div class="col-md-2 mb-2">
                  <label>Prioridad</label>
                  <select name="priority" class="form-control">
                    <option value="BAJA">BAJA</option>
                    <option value="MEDIA" selected>MEDIA</option>
                    <option value="ALTA">ALTA</option>
                  </select>
                </div>
                <div class="col-md-2 mb-2">
                  <label>Estado</label>
                  <select name="status" class="form-control">
                    <option value="PENDIENTE" selected>PENDIENTE</option>
                    <option value="EN_PROCESO">EN_PROCESO</option>
                    <option value="POSPUESTO">POSPUESTO</option>
                    <option value="HECHO">HECHO</option>
                  </select>
                </div>

                <div class="col-md-12 mb-2">
                  <label>Descripción</label>
                  <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="col-md-4 mb-2">
                  <label>Vence</label>
                  <input type="date" name="due_date" class="form-control">
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

    <!-- MODAL EDIT -->
    <div class="modal fade" id="modalEditTask">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="post" action="./?action=updatetask">
            <input type="hidden" name="id" id="et_id">
            <div class="modal-header">
              <h4 class="modal-title"><i class="fa fa-edit"></i> Editar tarea</h4>
              <button type="button" class="close text-white" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-8 mb-2">
                  <label>Título</label>
                  <input type="text" name="title" id="et_title" class="form-control" required>
                </div>
                <div class="col-md-2 mb-2">
                  <label>Prioridad</label>
                  <select name="priority" id="et_priority" class="form-control">
                    <option value="BAJA">BAJA</option>
                    <option value="MEDIA">MEDIA</option>
                    <option value="ALTA">ALTA</option>
                  </select>
                </div>
                <div class="col-md-2 mb-2">
                  <label>Estado</label>
                  <select name="status" id="et_status" class="form-control">
                    <option value="PENDIENTE">PENDIENTE</option>
                    <option value="EN_PROCESO">EN_PROCESO</option>
                    <option value="POSPUESTO">POSPUESTO</option>
                    <option value="HECHO">HECHO</option>
                  </select>
                </div>

                <div class="col-md-12 mb-2">
                  <label>Descripción</label>
                  <textarea name="description" id="et_description" class="form-control" rows="3"></textarea>
                </div>

                <div class="col-md-4 mb-2">
                  <label>Vence</label>
                  <input type="date" name="due_date" id="et_due_date" class="form-control">
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
function fillTask(id,title,desc,priority,status,due){
  document.getElementById("et_id").value = id;
  document.getElementById("et_title").value = title || '';
  document.getElementById("et_description").value = desc || '';
  document.getElementById("et_priority").value = priority || 'MEDIA';
  document.getElementById("et_status").value = status || 'PENDIENTE';
  document.getElementById("et_due_date").value = due || '';
}
</script>

<?php endif; ?>

