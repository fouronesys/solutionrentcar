<?php

class NotificationService {

    const EVENT_BOOKING_CREATED   = 'booking_created';
    const EVENT_BOOKING_WEB       = 'booking_web';
    const EVENT_BOOKING_DELIVERED = 'booking_delivered';
    const EVENT_BOOKING_CANCELED  = 'booking_canceled';
    const EVENT_PAYMENT_RECEIVED  = 'payment_received';
    const EVENT_REMINDER_RETURN   = 'reminder_return';
    const EVENT_REMINDER_PICKUP   = 'reminder_pickup';

    /**
     * Main entry. Creates in-app notification row and dispatches email channel
     * (if preference enabled and recipient has an email).
     *
     * @param string $recipientType 'user'|'client'
     * @param int    $recipientId
     * @param string $eventType
     * @param string $title
     * @param string $body         plain text or basic HTML
     * @param array  $data         optional payload (stock_id, url, booking_id, email_to override)
     */
    public static function notify($recipientType, $recipientId, $eventType, $title, $body, $data = []){
        $recipientId = intval($recipientId);
        if($recipientId <= 0) return false;

        $stockId = intval($data['stock_id'] ?? 0);
        $url     = (string)($data['url'] ?? '');

        // In-app channel
        if(NotificationPreferenceData::isEnabled($recipientType, $recipientId, $eventType, 'inapp')){
            $n = new NotificationData();
            $n->recipient_type = $recipientType;
            $n->recipient_id   = $recipientId;
            $n->stock_id       = $stockId;
            $n->type           = $eventType;
            $n->title          = $title;
            $n->body           = $body;
            $n->url            = $url;
            $n->data_json      = json_encode($data, JSON_UNESCAPED_UNICODE);
            $nid = $n->add();
            NotificationData::logDelivery($nid, 'inapp', 'sent', '');
        }

        // Email channel
        if(NotificationPreferenceData::isEnabled($recipientType, $recipientId, $eventType, 'email')){
            $emailTo = self::resolveEmail($recipientType, $recipientId, $data);
            if($emailTo){
                $ok = self::sendEmail($emailTo, $title, $body, $data);
                NotificationData::logDelivery($nid ?? 0, 'email', $ok ? 'sent' : 'failed', $ok ? $emailTo : ('to='.$emailTo));
            }
        }

        // Push channel (Expo) — defaults ON when preference row not set
        if(NotificationPreferenceData::isEnabled($recipientType, $recipientId, $eventType, 'push')){
            self::sendPush($recipientType, $recipientId, $eventType, $title, $body, $data, $nid ?? 0);
        }

        return true;
    }

    /**
     * Dispatches the notification to every Expo device token registered for the
     * recipient. Cleans up tokens reported as DeviceNotRegistered.
     */
    private static function sendPush($recipientType, $recipientId, $eventType, $title, $body, $data, $notificationId){
        try {
            self::sendPushInner($recipientType, $recipientId, $eventType, $title, $body, $data, $notificationId);
        } catch(\Throwable $e){
            error_log("[NotificationService] push error: ".$e->getMessage());
            @NotificationData::logDelivery($notificationId, 'push', 'failed', 'exception: '.$e->getMessage());
        }
    }

    private static function sendPushInner($recipientType, $recipientId, $eventType, $title, $body, $data, $notificationId){
        if(!function_exists('curl_init')) return; // cURL not available; skip silently
        $sub = function_exists('mb_substr') ? 'mb_substr' : 'substr';
        $con = Database::getCon();
        if(!$con) return;
        $rt = $con->real_escape_string((string)$recipientType);
        $rid = intval($recipientId);

        // Bail quietly if device_token table doesn't exist yet
        $check = @$con->query("SHOW TABLES LIKE 'device_token'");
        if(!$check || $check->num_rows === 0) return;

        $r = @$con->query("SELECT id, token FROM device_token WHERE recipient_type='$rt' AND recipient_id=$rid");
        if(!$r || $r->num_rows === 0) return;

        $tokens = [];
        while($row = $r->fetch_assoc()){
            $tokens[intval($row['id'])] = (string)$row['token'];
        }
        if(!$tokens) return;

        // Expo accepts batches of up to 100 messages per request.
        $tokenIds = array_keys($tokens);
        $tokenValues = array_values($tokens);
        $okTotal = 0; $failTotal = 0; $lastHttp = 0; $lastErr = '';
        $chunks = array_chunk($tokenValues, 100, true);
        $idChunks = array_chunk($tokenIds, 100, true);

        foreach($chunks as $ci => $chunk){
            $messages = [];
            foreach($chunk as $tok){
                $messages[] = [
                    'to'    => $tok,
                    'sound' => 'default',
                    'title' => $sub((string)$title, 0, 100),
                    'body'  => $sub(strip_tags((string)$body), 0, 200),
                    'data'  => array_merge(['event_type' => $eventType], is_array($data) ? $data : []),
                ];
            }

            $payload = json_encode($messages, JSON_UNESCAPED_UNICODE);
            $ch = curl_init('https://exp.host/--/api/v2/push/send');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Accept-Encoding: gzip, deflate',
                ],
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_TIMEOUT        => 8,
            ]);
            $resp = curl_exec($ch);
            $lastHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $lastErr  = curl_error($ch);
            curl_close($ch);

            if($resp === false){
                $failTotal += count($chunk);
                continue;
            }
            $decoded = json_decode($resp, true);
            if(!isset($decoded['data']) || !is_array($decoded['data'])){
                $failTotal += count($chunk);
                continue;
            }
            $batchIds = array_values($idChunks[$ci]);
            foreach($decoded['data'] as $i => $ticket){
                if(($ticket['status'] ?? '') === 'ok'){
                    $okTotal++;
                } else {
                    $failTotal++;
                    $errType = $ticket['details']['error'] ?? '';
                    if($errType === 'DeviceNotRegistered' && isset($batchIds[$i])){
                        $delId = intval($batchIds[$i]);
                        @$con->query("DELETE FROM device_token WHERE id=$delId");
                    }
                }
            }
        }

        if($okTotal === 0 && $failTotal > 0){
            NotificationData::logDelivery($notificationId, 'push', 'failed',
                "sent=0 failed=$failTotal http=$lastHttp err=".substr($lastErr, 0, 120));
        } else {
            NotificationData::logDelivery($notificationId, 'push',
                $failTotal === 0 ? 'sent' : 'partial',
                "sent=$okTotal failed=$failTotal");
        }
    }

    public static function notifyMany($recipientType, $recipientIds, $eventType, $title, $body, $data = []){
        if(!is_array($recipientIds)) return;
        foreach($recipientIds as $rid){
            self::notify($recipientType, $rid, $eventType, $title, $body, $data);
        }
    }

    /**
     * Notify all active users in a stock (admins + employees).
     */
    public static function notifyStockUsers($stockId, $eventType, $title, $body, $data = []){
        $stockId = intval($stockId);
        if($stockId <= 0) return;
        $con = Database::getCon();
        $r = $con->query("SELECT id FROM user WHERE stock_id=$stockId AND status=1");
        if($r){
            while($row = $r->fetch_assoc()){
                $d = $data; $d['stock_id'] = $stockId;
                self::notify('user', intval($row['id']), $eventType, $title, $body, $d);
            }
        }
    }

    private static function resolveEmail($recipientType, $recipientId, $data){
        if(!empty($data['email_to'])) return $data['email_to'];
        $con = Database::getCon();
        $recipientId = intval($recipientId);
        if($recipientType === 'user'){
            $r = $con->query("SELECT email FROM user WHERE id=$recipientId LIMIT 1");
        } else {
            $r = $con->query("SELECT email FROM person WHERE id=$recipientId LIMIT 1");
        }
        if($r && $row = $r->fetch_assoc()){
            $email = trim((string)($row['email'] ?? ''));
            if($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) return $email;
        }
        return null;
    }

    private static function sendEmail($to, $subject, $body, $data = []){
        try {
            if(!class_exists('PHPMailer')) return false;

            $emailUser = Core::$email_user;
            $emailPass = Core::$email_password;

            // Try fall back to configuration table if Core not set
            if(empty($emailUser) && class_exists('ConfigurationData')){
                $cu = @ConfigurationData::getByPreffix('email_user');
                $cp = @ConfigurationData::getByPreffix('email_password');
                if($cu && !empty($cu->val)) $emailUser = $cu->val;
                if($cp && !empty($cp->val)) $emailPass = $cp->val;
            }

            // Allow env override
            $envUser = getenv('SMTP_USER');
            $envPass = getenv('SMTP_PASSWORD');
            $envHost = getenv('SMTP_HOST');
            $envPort = getenv('SMTP_PORT');
            if($envUser) $emailUser = $envUser;
            if($envPass) $emailPass = $envPass;

            if(empty($emailUser) || empty($emailPass)){
                error_log("[NotificationService] SMTP credentials missing; skipping email to $to");
                return false;
            }

            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPAuth   = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Host       = $envHost ?: 'smtp.gmail.com';
            $mail->Port       = $envPort ? intval($envPort) : 465;
            $mail->Username   = $emailUser;
            $mail->Password   = $emailPass;
            $mail->From       = $emailUser;
            $mail->FromName   = !empty($data['from_name']) ? $data['from_name'] : 'Notificaciones';
            $mail->CharSet    = 'UTF-8';
            $mail->IsHTML(true);
            $mail->Subject    = $subject;
            $mail->Body       = self::buildHtmlTemplate($subject, $body, $data);
            $mail->AltBody    = strip_tags($body);
            $mail->AddAddress($to);

            $ok = $mail->Send();
            return (bool)$ok;
        } catch(Exception $e){
            error_log("[NotificationService] Email error: ".$e->getMessage());
            return false;
        }
    }

    private static function buildHtmlTemplate($subject, $body, $data = []){
        $brand = htmlspecialchars($data['brand'] ?? 'Solutions Rent Car', ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
        $url   = !empty($data['url']) ? htmlspecialchars($data['url'], ENT_QUOTES, 'UTF-8') : '';
        $btn   = '';
        if($url){
            $btn = '<p style="margin:24px 0;text-align:center;"><a href="'.$url.'" style="background:#222;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;font-weight:bold;">Ver detalle</a></p>';
        }
        $year = date('Y');
        return '<!DOCTYPE html><html><body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;color:#222;">'
            .'<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:24px 0;"><tr><td align="center">'
            .'<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;overflow:hidden;max-width:600px;">'
            .'<tr><td style="background:#111;color:#fff;padding:18px 24px;font-size:18px;font-weight:bold;">'.$brand.'</td></tr>'
            .'<tr><td style="padding:24px;">'
            .'<h2 style="margin:0 0 12px 0;font-size:20px;color:#111;">'.$title.'</h2>'
            .'<div style="font-size:14px;line-height:1.6;color:#333;">'.$body.'</div>'
            .$btn
            .'</td></tr>'
            .'<tr><td style="background:#fafafa;color:#888;padding:14px 24px;font-size:12px;text-align:center;">© '.$year.' '.$brand.'</td></tr>'
            .'</table></td></tr></table></body></html>';
    }
}
?>
