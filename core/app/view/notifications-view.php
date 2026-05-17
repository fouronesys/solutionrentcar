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

// Default: list
$rows = NotificationData::getForRecipient('user', $rid, 100, false);
?>
<section class="content">
  <div class="content-header"><div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h1 class="m-0" style="color:white;"><i class="fa fa-bell"></i> Notificaciones</h1>
      </div>
      <div class="col-sm-6 text-right">
        <a href="./?view=notifications&opt=preferences" class="btn btn-secondary btn-sm"><i class="fa fa-cog"></i> Preferencias</a>
        <button type="button" id="notifMarkAll" class="btn btn-warning btn-sm"><i class="fa fa-check-double"></i> Marcar todas como leídas</button>
      </div>
    </div>
  </div></div>

  <div class="card" style="background:#222;color:#fff;">
    <div class="card-body">
      <?php if(empty($rows)): ?>
        <p>No tienes notificaciones aún.</p>
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
            <p class="mb-1"><?php echo $n->body; ?></p>
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
      <?php endif; ?>
    </div>
  </div>
</section>
<script>
$(function(){
  $('.notif-mark').on('click', function(){
    var id = $(this).data('id');
    var btn = $(this);
    $.post('./?action=notification&opt=mark_read', {id: id}, function(r){
      if(r.ok){ btn.closest('.list-group-item').css('background', '#2a2a2a'); btn.remove(); }
    }, 'json');
  });
  $('.notif-go').on('click', function(){
    var id = $(this).data('id');
    $.post('./?action=notification&opt=mark_read', {id: id});
  });
  $('#notifMarkAll').on('click', function(){
    $.post('./?action=notification&opt=mark_all_read', {}, function(r){
      if(r.ok) location.reload();
    }, 'json');
  });
});
</script>
