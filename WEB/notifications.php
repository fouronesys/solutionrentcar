<?php
ob_start();
include "layout/header.php";

include_once __DIR__ . "/../core/app/model/NotificationData.php";
include_once __DIR__ . "/../core/app/model/NotificationPreferenceData.php";
include_once __DIR__ . "/../core/controller/NotificationService.php";

if(!isset($_SESSION['client_id']) || intval($_SESSION['client_id']) <= 0){
    header("Location: " . ($base_url_safe ?? './'));
    exit;
}
$rid = intval($_SESSION['client_id']);
NotificationData::ensureSchema();
$rows = NotificationData::getForRecipient('client', $rid, 100, false);
?>
<section class="ftco-section" style="padding:80px 0;">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2><?php echo __('notif_title'); ?></h2>
      <div>
        <a href="<?php echo $base_url_safe; ?>notifications-preferences.php" class="btn btn-outline-dark btn-sm"><i class="fa fa-cog"></i> <?php echo __('notif_preferences'); ?></a>
        <button type="button" id="cliNotifMarkAll" class="btn btn-warning btn-sm"><?php echo __('notif_mark_all'); ?></button>
      </div>
    </div>

    <?php if(empty($rows)): ?>
      <div class="alert alert-info"><?php echo __('notif_empty'); ?></div>
    <?php else: ?>
      <div class="list-group">
      <?php foreach($rows as $n): $isRead = !empty($n->read_at); ?>
        <div class="list-group-item" style="border-left:4px solid <?php echo $isRead ? '#ccc' : '#f59e0b'; ?>;">
          <div class="d-flex justify-content-between">
            <h5 class="mb-1"><?php if(!$isRead): ?><span class="badge badge-warning"><?php echo __('notif_new'); ?></span> <?php endif; ?><?php echo htmlspecialchars($n->title); ?></h5>
            <small class="text-muted"><?php echo htmlspecialchars($n->created_at); ?></small>
          </div>
          <p class="mb-1"><?php echo $n->body; ?></p>
          <?php if(!$isRead): ?>
            <button type="button" class="btn btn-sm btn-success cli-notif-mark" data-id="<?php echo intval($n->id); ?>"><?php echo __('notif_mark_read'); ?></button>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<script src="<?php echo $base_url_safe; ?>js/jquery-3.2.1.min.js"></script>
<script>
$(function(){
  $('.cli-notif-mark').on('click', function(){
    var id = $(this).data('id'); var btn = $(this);
    $.post('<?php echo $base_url_safe; ?>notification-action.php?opt=mark_read', {id:id}, function(r){
      if(r.ok){ btn.closest('.list-group-item').css('border-left-color','#ccc'); btn.remove(); }
    }, 'json');
  });
  $('#cliNotifMarkAll').on('click', function(){
    $.post('<?php echo $base_url_safe; ?>notification-action.php?opt=mark_all_read', {}, function(r){
      if(r.ok) location.reload();
    }, 'json');
  });
});
</script>
<?php include "layout/footer.php"; ?>
