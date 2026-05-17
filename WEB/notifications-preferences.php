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
$prefs = NotificationPreferenceData::getAllFor('client', $rid);

$events = [
    NotificationService::EVENT_BOOKING_WEB       => __('notif_ev_booking_web'),
    NotificationService::EVENT_BOOKING_CREATED   => __('notif_ev_booking_created'),
    NotificationService::EVENT_BOOKING_DELIVERED => __('notif_ev_booking_delivered'),
    NotificationService::EVENT_BOOKING_CANCELED  => __('notif_ev_booking_canceled'),
    NotificationService::EVENT_PAYMENT_RECEIVED  => __('notif_ev_payment_received'),
    NotificationService::EVENT_REMINDER_RETURN   => __('notif_ev_reminder_return'),
    NotificationService::EVENT_REMINDER_PICKUP   => __('notif_ev_reminder_pickup'),
];
?>
<section class="ftco-section" style="padding:80px 0;">
  <div class="container">
    <h2 class="mb-4"><?php echo __('notif_preferences'); ?></h2>
    <form id="cliPrefsForm">
      <table class="table table-bordered">
        <thead><tr><th><?php echo __('notif_event'); ?></th><th class="text-center"><?php echo __('notif_inapp'); ?></th><th class="text-center"><?php echo __('notif_email'); ?></th></tr></thead>
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
      <button type="submit" class="btn btn-primary"><?php echo __('notif_save'); ?></button>
      <a href="<?php echo $base_url_safe; ?>notifications.php" class="btn btn-secondary"><?php echo __('notif_back'); ?></a>
      <span id="cliPrefsMsg" class="ml-3"></span>
    </form>
  </div>
</section>
<script src="<?php echo $base_url_safe; ?>js/jquery-3.2.1.min.js"></script>
<script>
$(function(){
  $('#cliPrefsForm').on('submit', function(e){
    e.preventDefault();
    $.post('<?php echo $base_url_safe; ?>notification-action.php?opt=save_preferences', $(this).serialize(), function(r){
      $('#cliPrefsMsg').text(r.ok ? '✓' : 'Error').css('color', r.ok ? 'green' : 'red');
    }, 'json');
  });
});
</script>
<?php include "layout/footer.php"; ?>
