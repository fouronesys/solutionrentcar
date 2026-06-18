<?php

#[AllowDynamicProperties]
class NotificationPreferenceData {

    public static $tablename = "notification_preference";

    /** Default: every event enabled for both inapp + email */
    public static function isEnabled($recipientType, $recipientId, $eventType, $channel){
        NotificationData::ensureSchema();
        $con = Database::getCon();
        $rt = $con->real_escape_string((string)$recipientType);
        $rid = intval($recipientId);
        $et = $con->real_escape_string((string)$eventType);
        $ch = $con->real_escape_string((string)$channel);

        $sql = "SELECT enabled FROM ".self::$tablename."
                WHERE recipient_type='$rt' AND recipient_id=$rid
                AND event_type='$et' AND channel='$ch' LIMIT 1";
        $r = Executor::doit($sql);
        if($r[0] && $row = $r[0]->fetch_assoc()){
            return intval($row['enabled']) == 1;
        }
        return true; // default ON
    }

    public static function setPreference($recipientType, $recipientId, $eventType, $channel, $enabled){
        NotificationData::ensureSchema();
        $con = Database::getCon();
        $rt = $con->real_escape_string((string)$recipientType);
        $rid = intval($recipientId);
        $et = $con->real_escape_string((string)$eventType);
        $ch = $con->real_escape_string((string)$channel);
        $en = intval($enabled) ? 1 : 0;

        $sql = "INSERT INTO ".self::$tablename."
                (recipient_type,recipient_id,event_type,channel,enabled,updated_at)
                VALUES ('$rt',$rid,'$et','$ch',$en,NOW())
                ON DUPLICATE KEY UPDATE enabled=$en, updated_at=NOW()";
        Executor::doit($sql);
    }

    public static function getAllFor($recipientType, $recipientId){
        NotificationData::ensureSchema();
        $con = Database::getCon();
        $rt = $con->real_escape_string((string)$recipientType);
        $rid = intval($recipientId);
        $sql = "SELECT event_type, channel, enabled FROM ".self::$tablename."
                WHERE recipient_type='$rt' AND recipient_id=$rid";
        $r = Executor::doit($sql);
        $out = [];
        if($r[0]){
            while($row = $r[0]->fetch_assoc()){
                $out[$row['event_type']][$row['channel']] = intval($row['enabled']);
            }
        }
        return $out;
    }
}
?>
