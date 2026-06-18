<?php
  if(!isset($_SESSION['user_id'])){ echo '<p>Unauthorized</p>'; return; }
  $rid = intval($_SESSION['user_id']);
  $opt = isset($_GET['opt']) ? $_GET['opt'] : 'all';

  NotificationData::ensureSchema();

  if($opt === 'preferences'){
      $prefs = NotificationPreferenceData::getAllFor('user', $rid);
      $events = [
          NotificationService::EVENT_BOOKING_CREATED   => 'Reserva creada (admin)',
          NotificationService::EVENT_BOOKING_WEB       => 'Reserva creada (web)',
          NotificationService::EVENT_BOOKING_DELIVERED => 'Vehículo entregado',
          NotificationService::EVENT_BOOKING_CANCELED  => 'Reserva cancelada',
          NotificationService::EVENT_PAYMENT_RECEIVED  => 'Pago recibido',
          NotificationService::EVENT_REMINDER_RETURN   => 'Recordatorio de devolución',
          NotificationService::EVENT_REMINDER_PICKUP   => 'Recordatorio de entrega',
      ];
  ?>
  <section class="content">
    <div class="content-header"><div class="container-fluid">
      <h1 class="m-0" style="color:white;"><i class="fa fa-bell"></i> Preferencias de Notificación</h1>
    </div></div>
    <div class="card" style="background:#222;color:#fff;">
      <div class="card-body">
        <form id="notifPrefsForm">
          <table class="table table-bordered table-dark">
            <thead><tr><th>Evento</th><th class="text-center">En la app</th><th class="text-center">Email</th></tr></thead>
            <tbody>
            <?php foreach($events as $ev => $label):
                $inapp = isset($prefs[$ev]['inapp']) ? intval($prefs[$ev]['inapp']) : 1;
                $email = isset($prefs[$ev]['email']) ? intval($prefs[$ev]['email']) : 1; ?>
              <tr>
                <td><?php echo htmlspecialchars($label); ?></td>
                <td class="text-center"><input type="checkbox" name="events[<?php echo $ev; ?>][inapp]" value="1" <?php if($inapp) echo 'checked'; ?>></td>
                <td class="text-center"><input type="checkbox" name="events[<?php echo $ev; ?>][email]" value="1" <?php if($email) echo 'checked'; ?>></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar Preferencias</button>
          <a href="./?view=notifications&opt=all" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Volver</a>
          <span id="notifPrefsMsg" class="ml-3"></span>
        </form>
      </div>
    </div>
  </section>
  <script>
  $(function(){
    $('#notifPrefsForm').on('submit', function(e){
      e.preventDefault();
      $.post('./?action=notification&opt=save_preferences', $(this).serialize(), function(r){
        $('#notifPrefsMsg').text(r.ok ? '✓ Guardado' : 'Error').css('color', r.ok ? '#4ade80' : '#f87171');
      }, 'json');
    });
  });
  </script>
  <?php
      return;
  }

  if($opt === 'log'){
      $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
      $logs = NotificationData::getLogs($statusFilter, 200);
  ?>
  <section class="content">
    <div class="content-header"><div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h1 class="m-0" style="color:white;"><i class="fa fa-clipboard-list"></i> Registro de envíos</h1></div>
        <div class="col-sm-6 text-right">
          <a href="./?view=notifications&opt=all" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Volver</a>
        </div>
      </div>
    </div></div>
    <div class="card" style="background:#222;color:#fff;">
      <div class="card-body">
        <form method="get" class="form-inline mb-3">
          <input type="hidden" name="view" value="notifications">
          <input type="hidden" name="opt" value="log">
          <label class="mr-2">Estado:</label>
          <select name="status" class="form-control form-control-sm mr-2">
            <option value="">Todos</option>
            <option value="sent"   <?php if($statusFilter==='sent')   echo 'selected'; ?>>Enviados</option>
            <option value="failed" <?php if($statusFilter==='failed') echo 'selected'; ?>>Fallidos</option>
            <option value="skipped"<?php if($statusFilter==='skipped')echo 'selected'; ?>>Omitidos</option>
          </select>
          <button class="btn btn-primary btn-sm">Filtrar</button>
        </form>
        <table class="table table-sm table-dark table-striped">
          <thead><tr><th>#</th><th>Fecha</th><th>Notif #</th><th>Evento</th><th>Destinatario</th><th>Canal</th><th>Estado</th><th>Detalle</th></tr></thead>
          <tbody>
            <?php if(empty($logs)): ?>
              <tr><td colspan="8" class="text-center text-muted">No hay registros.</td></tr>
            <?php else: foreach($logs as $L): ?>
              <tr>
                <td><?php echo intval($L['id']); ?></td>
                <td><?php echo htmlspecialchars($L['created_at']); ?></td>
                <td><?php echo intval($L['notification_id']); ?></td>
                <td><?php echo htmlspecialchars((string)($L['event_type'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars(($L['recipient_type'] ?? '').' #'.intval($L['recipient_id'] ?? 0)); ?></td>
                <td><?php echo htmlspecialchars((string)$L['channel']); ?></td>
                <td><?php
                    $st = (string)$L['status'];
                    $cls = $st==='sent'?'success':($st==='failed'?'danger':'secondary');
                    echo '<span class="badge badge-'.$cls.'">'.htmlspecialchars($st).'</span>'; ?></td>
                <td style="max-width:340px;word-break:break-word;"><?php echo htmlspecialchars((string)($L['detail'] ?? '')); ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
  <?php
      return;
  }

  // Default: list with filters + pagination
  $filter    = isset($_GET['filter']) ? $_GET['filter'] : 'all';
  $eventType = isset($_GET['event'])  ? $_GET['event']  : '';
  $dateFrom  = isset($_GET['from'])   ? $_GET['from']   : '';
  $dateTo    = isset($_GET['to'])     ? $_GET['to']     : '';
  $page      = isset($_GET['page'])   ? intval($_GET['page']) : 1;
  $perPage   = 20;

  $result = NotificationData::getFiltered('user', $rid, $filter, $eventType, $dateFrom, $dateTo, $page, $perPage);
  $rows = $result['rows'];
  $total = $result['total'];
  $totalPages = max(1, (int)ceil($total / $perPage));

  $eventLabels = [
      NotificationService::EVENT_BOOKING_CREATED   => 'Reserva creada',
      NotificationService::EVENT_BOOKING_WEB       => 'Reserva web',
      NotificationService::EVENT_BOOKING_DELIVERED => 'Entregada',
      NotificationService::EVENT_BOOKING_CANCELED  => 'Cancelada',
      NotificationService::EVENT_PAYMENT_RECEIVED  => 'Pago recibido',
      NotificationService::EVENT_REMINDER_RETURN   => 'Recordatorio devolución',
      NotificationService::EVENT_REMINDER_PICKUP   => 'Recordatorio entrega',
  ];

  function notif_page_url($page, $filter, $eventType, $dateFrom, $dateTo){
      $params = ['view' => 'notifications', 'opt' => 'all'];
      if($filter !== 'all') $params['filter'] = $filter;
      if($eventType !== '') $params['event']  = $eventType;
      if($dateFrom !== '')  $params['from']   = $dateFrom;
      if($dateTo !== '')    $params['to']     = $dateTo;
      $params['page'] = max(1, intval($page));
      return './?' . http_build_query($params);
  }
  ?>
  <section class="content">
    <div class="content-header"><div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h1 class="m-0" style="color:white;"><i class="fa fa-bell"></i> Notificaciones</h1></div>
        <div class="col-sm-6 text-right">
          <a href="./?view=notifications&opt=log" class="btn btn-info btn-sm"><i class="fa fa-clipboard-list"></i> Registro</a>
          <a href="./?view=notifications&opt=preferences" class="btn btn-secondary btn-sm"><i class="fa fa-cog"></i> Preferencias</a>
          <button type="button" id="notifMarkAll" class="btn btn-warning btn-sm"><i class="fa fa-check-double"></i> Marcar todas como leídas</button>
        </div>
      </div>
    </div></div>

    <div class="card" style="background:#222;color:#fff;">
      <div class="card-body">
        <form method="get" class="form-inline mb-3">
          <input type="hidden" name="view" value="notifications">
          <input type="hidden" name="opt"  value="all">
          <label class="mr-2">Estado:</label>
          <select name="filter" class="form-control form-control-sm mr-2">
            <option value="all"    <?php if($filter==='all')    echo 'selected'; ?>>Todas</option>
            <option value="unread" <?php if($filter==='unread') echo 'selected'; ?>>No leídas</option>
            <option value="read"   <?php if($filter==='read')   echo 'selected'; ?>>Leídas</option>
          </select>
          <label class="mr-2">Tipo:</label>
          <select name="event" class="form-control form-control-sm mr-2">
            <option value="">Todos</option>
            <?php foreach($eventLabels as $ev => $lab): ?>
              <option value="<?php echo htmlspecialchars($ev); ?>" <?php if($eventType===$ev) echo 'selected'; ?>><?php echo htmlspecialchars($lab); ?></option>
            <?php endforeach; ?>
          </select>
          <label class="mr-2">Desde:</label>
          <input type="date" name="from" value="<?php echo htmlspecialchars($dateFrom); ?>" class="form-control form-control-sm mr-2">
          <label class="mr-2">Hasta:</label>
          <input type="date" name="to" value="<?php echo htmlspecialchars($dateTo); ?>" class="form-control form-control-sm mr-2">
          <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
          <a href="./?view=notifications&opt=all" class="btn btn-link btn-sm">Limpiar</a>
        </form>

        <?php if(empty($rows)): ?>
          <p>No hay notificaciones para los filtros seleccionados.</p>
        <?php else: ?>
          <div class="list-group">
          <?php foreach($rows as $n):
              $isRead = !empty($n->read_at);
              $bg = $isRead ? '#2a2a2a' : '#3a2f00'; ?>
            <div class="list-group-item" style="background:<?php echo $bg; ?>;color:#fff;border-color:#444;">
              <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1"><?php if(!$isRead): ?><span class="badge badge-warning">NUEVO</span> <?php endif; ?><?php echo htmlspecialchars($n->title); ?></h5>
                <small class="text-muted"><?php echo htmlspecialchars($n->created_at); ?></small>
              </div>
              <p class="mb-1"><?php echo nl2br(htmlspecialchars((string)$n->body, ENT_QUOTES, 'UTF-8')); ?></p>
              <div>
                <?php if(!empty($n->url)): ?>
                  <a href="<?php echo htmlspecialchars($n->url); ?>" class="btn btn-sm btn-info notif-go" data-id="<?php echo intval($n->id); ?>"><i class="fa fa-arrow-right"></i> Ver</a>
                <?php endif; ?>
                <?php if(!$isRead): ?>
                  <button type="button" class="btn btn-sm btn-success notif-mark" data-id="<?php echo intval($n->id); ?>"><i class="fa fa-check"></i> Marcar leída</button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
          </div>

          <nav class="mt-3"><ul class="pagination pagination-sm justify-content-center">
            <li class="page-item <?php if($page<=1) echo 'disabled'; ?>"><a class="page-link" href="<?php echo notif_page_url(max(1,$page-1),$filter,$eventType,$dateFrom,$dateTo); ?>">&laquo;</a></li>
            <?php
              $start = max(1, $page - 3);
              $end   = min($totalPages, $page + 3);
              for($i=$start; $i<=$end; $i++): ?>
              <li class="page-item <?php if($i===$page) echo 'active'; ?>"><a class="page-link" href="<?php echo notif_page_url($i,$filter,$eventType,$dateFrom,$dateTo); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?php if($page>=$totalPages) echo 'disabled'; ?>"><a class="page-link" href="<?php echo notif_page_url(min($totalPages,$page+1),$filter,$eventType,$dateFrom,$dateTo); ?>">&raquo;</a></li>
          </ul>
          <p class="text-center text-muted small"><?php echo intval($total); ?> resultado(s) — página <?php echo intval($page); ?> de <?php echo intval($totalPages); ?></p></nav>
        <?php endif; ?>
      </div>
    </div>
  </section>
  <script>
  $(function(){
    $('.notif-mark').on('click', function(){
      var id = $(this).data('id'); var btn = $(this);
      $.post('./?action=notification&opt=mark_read', {id: id}, function(r){
        if(r.ok){ btn.closest('.list-group-item').css('background', '#2a2a2a'); btn.remove(); }
      }, 'json');
    });
    $('.notif-go').on('click', function(){
      var id = $(this).data('id');
      $.post('./?action=notification&opt=mark_read', {id: id});
    });
    $('#notifMarkAll').on('click', function(){
      $.post('./?action=notification&opt=mark_all_read', {}, function(r){ if(r.ok) location.reload(); }, 'json');
    });
  });
  </script>
  