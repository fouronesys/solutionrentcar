<?php
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include_once __DIR__ . "/../core/controller/Core.php";
include_once __DIR__ . "/../core/controller/Database.php";
include_once __DIR__ . "/../core/controller/Executor.php";
include_once __DIR__ . "/../core/controller/Model.php";
include_once __DIR__ . "/../core/controller/class.phpmailer.php";
include_once __DIR__ . "/../core/controller/class.smtp.php";
include_once __DIR__ . "/../core/app/model/NotificationData.php";
include_once __DIR__ . "/../core/app/model/NotificationPreferenceData.php";
include_once __DIR__ . "/../core/controller/NotificationService.php";

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function wnotif_json($ok, $extra = []){
    echo json_encode(array_merge(['ok' => (bool)$ok], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

if(!isset($_SESSION['client_id']) || intval($_SESSION['client_id']) <= 0){
    wnotif_json(false, ['message' => 'unauthorized']);
}
$rtype = 'client';
$rid = intval($_SESSION['client_id']);
NotificationData::ensureSchema();

$opt = $_GET['opt'] ?? '';

if($opt === 'count'){
    wnotif_json(true, ['unread' => NotificationData::countUnread($rtype, $rid)]);
}
if($opt === 'list'){
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    if($limit < 1) $limit = 10; if($limit > 50) $limit = 50;
    $rows = NotificationData::getForRecipient($rtype, $rid, $limit, false);
    $out = [];
    foreach($rows as $n){
        $out[] = [
            'id' => intval($n->id), 'type' => $n->type, 'title' => $n->title,
            'body' => $n->body, 'url' => $n->url,
            'read' => !empty($n->read_at), 'created' => $n->created_at,
        ];
    }
    wnotif_json(true, ['unread' => NotificationData::countUnread($rtype, $rid), 'items' => $out]);
}
if($opt === 'mark_read'){
    $id = intval($_POST['id'] ?? ($_GET['id'] ?? 0));
    if($id > 0){ NotificationData::markRead($id, $rtype, $rid); wnotif_json(true); }
    wnotif_json(false, ['message' => 'missing id']);
}
if($opt === 'mark_all_read'){
    NotificationData::markAllRead($rtype, $rid);
    wnotif_json(true);
}
if($opt === 'save_preferences'){
    $events = isset($_POST['events']) && is_array($_POST['events']) ? $_POST['events'] : [];
    $allEvents = [
        NotificationService::EVENT_BOOKING_CREATED,
        NotificationService::EVENT_BOOKING_WEB,
        NotificationService::EVENT_BOOKING_DELIVERED,
        NotificationService::EVENT_BOOKING_CANCELED,
        NotificationService::EVENT_PAYMENT_RECEIVED,
        NotificationService::EVENT_REMINDER_RETURN,
        NotificationService::EVENT_REMINDER_PICKUP,
    ];
    foreach($allEvents as $ev){
        foreach(['inapp','email'] as $ch){
            $en = !empty($events[$ev][$ch]) ? 1 : 0;
            NotificationPreferenceData::setPreference($rtype, $rid, $ev, $ch, $en);
        }
    }
    wnotif_json(true);
}
wnotif_json(false, ['message' => 'unknown opt']);
?>
