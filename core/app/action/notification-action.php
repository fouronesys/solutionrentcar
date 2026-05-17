<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
}

function notif_json($ok, $extra = []){
    echo json_encode(array_merge(['ok' => (bool)$ok], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

function notif_recipient(){
    if(isset($_SESSION['user_id']) && intval($_SESSION['user_id']) > 0){
        return ['user', intval($_SESSION['user_id'])];
    }
    if(isset($_SESSION['client_id']) && intval($_SESSION['client_id']) > 0){
        return ['client', intval($_SESSION['client_id'])];
    }
    return [null, 0];
}

list($rtype, $rid) = notif_recipient();
if(!$rtype){ notif_json(false, ['message' => 'unauthorized']); }

NotificationData::ensureSchema();

$opt = isset($_GET['opt']) ? $_GET['opt'] : '';

if($opt === 'count'){
    notif_json(true, ['unread' => NotificationData::countUnread($rtype, $rid)]);
}

if($opt === 'list'){
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    if($limit < 1) $limit = 10;
    if($limit > 50) $limit = 50;
    $rows = NotificationData::getForRecipient($rtype, $rid, $limit, false);
    $out = [];
    foreach($rows as $n){
        $out[] = [
            'id'       => intval($n->id),
            'type'     => $n->type,
            'title'    => $n->title,
            'body'     => $n->body,
            'url'      => $n->url,
            'read'     => !empty($n->read_at),
            'created'  => $n->created_at,
        ];
    }
    notif_json(true, [
        'unread' => NotificationData::countUnread($rtype, $rid),
        'items'  => $out,
    ]);
}

if($opt === 'mark_read'){
    $id = intval($_POST['id'] ?? ($_GET['id'] ?? 0));
    if($id > 0){
        NotificationData::markRead($id, $rtype, $rid);
        notif_json(true);
    }
    notif_json(false, ['message' => 'missing id']);
}

if($opt === 'mark_all_read'){
    NotificationData::markAllRead($rtype, $rid);
    notif_json(true);
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
    $channels = ['inapp', 'email'];
    foreach($allEvents as $ev){
        foreach($channels as $ch){
            $enabled = !empty($events[$ev][$ch]) ? 1 : 0;
            NotificationPreferenceData::setPreference($rtype, $rid, $ev, $ch, $enabled);
        }
    }
    notif_json(true);
}

notif_json(false, ['message' => 'unknown opt']);
?>
