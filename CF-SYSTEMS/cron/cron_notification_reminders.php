<?php
/**
 * Notification reminder cron.
 * Sends:
 *   - reminder_pickup  to customers whose booking start_at is tomorrow (status=0)
 *   - reminder_return  to customers whose booking end_at is tomorrow (status=1)
 * Idempotent: skips if a notification of the same type was already created
 * for the same booking within the last 20 hours.
 *
 * Run via: php CF-SYSTEMS/cron/cron_notification_reminders.php
 *   (recommended hourly; effective once per day)
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

chdir(dirname(__DIR__, 2));

require_once "core/controller/Core.php";
require_once "core/controller/View.php";
require_once "core/controller/Module.php";
require_once "core/controller/Database.php";
require_once "core/controller/Executor.php";
require_once "core/controller/Lb.php";
require_once "core/controller/Model.php";
require_once "core/controller/Bootload.php";
require_once "core/controller/Action.php";
require_once "core/controller/class.phpmailer.php";
require_once "core/controller/class.smtp.php";
require_once "core/controller/class.pop3.php";
require_once "core/autoload.php";
require_once "core/app/autoload.php";
require_once "core/controller/NotificationService.php";

NotificationData::ensureSchema();

$con = Database::getCon();
$tomorrow = date('Y-m-d', strtotime('+1 day'));

function recently_notified($con, $bookingId, $eventType){
    $bookingId = intval($bookingId);
    $et = $con->real_escape_string($eventType);
    $sql = "SELECT id FROM notification
            WHERE type='$et'
              AND data_json REGEXP '\"booking_id\":$bookingId([,}])'
              AND created_at > DATE_SUB(NOW(), INTERVAL 20 HOUR) LIMIT 1";
    $r = $con->query($sql);
    return $r && $r->num_rows > 0;
}

$processed = 0;

/* ---------- Pickup reminders (status=0, start_at tomorrow) ---------- */
$sql = "SELECT b.id, b.person_id, b.stock_id, b.start_at, b.end_at,
               p.name AS pname, p.email AS pemail,
               c.name AS cname
        FROM booking b
        LEFT JOIN person p ON p.id = b.person_id
        LEFT JOIN cars c   ON c.id = b.car_id
        WHERE b.status = 0
        AND DATE(b.start_at) = '$tomorrow'";
$r = $con->query($sql);
if($r){
    while($row = $r->fetch_assoc()){
        if(recently_notified($con, $row['id'], NotificationService::EVENT_REMINDER_PICKUP)) continue;
        $title = "Recordatorio: Entrega de vehículo mañana";
        $body  = "Hola ".htmlspecialchars($row['pname']).", recuerda que tu reservación del vehículo "
               . htmlspecialchars((string)$row['cname'])
               . " inicia mañana (".$row['start_at'].").";
        NotificationService::notify('client', intval($row['person_id']),
            NotificationService::EVENT_REMINDER_PICKUP, $title, $body,
            ['stock_id' => intval($row['stock_id']), 'booking_id' => intval($row['id'])]);
        NotificationService::notifyStockUsers(intval($row['stock_id']),
            NotificationService::EVENT_REMINDER_PICKUP,
            "Entrega mañana: ".htmlspecialchars($row['pname']),
            "El cliente ".htmlspecialchars($row['pname'])." debe recibir el vehículo mañana (".$row['start_at'].").",
            ['booking_id' => intval($row['id'])]);
        $processed++;
    }
}

/* ---------- Return reminders (status=1, end_at tomorrow) ---------- */
$sql = "SELECT b.id, b.person_id, b.stock_id, b.start_at, b.end_at,
               p.name AS pname, p.email AS pemail,
               c.name AS cname
        FROM booking b
        LEFT JOIN person p ON p.id = b.person_id
        LEFT JOIN cars c   ON c.id = b.car_id
        WHERE b.status = 1
        AND DATE(b.end_at) = '$tomorrow'";
$r = $con->query($sql);
if($r){
    while($row = $r->fetch_assoc()){
        if(recently_notified($con, $row['id'], NotificationService::EVENT_REMINDER_RETURN)) continue;
        $title = "Recordatorio: Devolución de vehículo mañana";
        $body  = "Hola ".htmlspecialchars($row['pname']).", recuerda devolver el vehículo "
               . htmlspecialchars((string)$row['cname'])
               . " mañana (".$row['end_at'].").";
        NotificationService::notify('client', intval($row['person_id']),
            NotificationService::EVENT_REMINDER_RETURN, $title, $body,
            ['stock_id' => intval($row['stock_id']), 'booking_id' => intval($row['id'])]);
        NotificationService::notifyStockUsers(intval($row['stock_id']),
            NotificationService::EVENT_REMINDER_RETURN,
            "Devolución mañana: ".htmlspecialchars($row['pname']),
            "El cliente ".htmlspecialchars($row['pname'])." debe devolver el vehículo mañana (".$row['end_at'].").",
            ['booking_id' => intval($row['id'])]);
        $processed++;
    }
}

echo "[".date('Y-m-d H:i:s')."] cron_notification_reminders done. Processed: $processed\n";
?>
