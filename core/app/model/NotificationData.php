<?php

#[AllowDynamicProperties]
class NotificationData {

    public static $tablename = "notification";
    private static $schemaChecked = false;

    public $id;
    public $recipient_type;
    public $recipient_id;
    public $stock_id;
    public $type;
    public $title;
    public $body;
    public $data_json;
    public $url;
    public $read_at;
    public $created_at;

    public function __construct(){
        $this->created_at = "NOW()";
        $this->read_at = null;
    }

    /**
     * Idempotent schema bootstrap. Runs once per request.
     */
    public static function ensureSchema(){
        if(self::$schemaChecked) return;
        self::$schemaChecked = true;

        $con = Database::getCon();
        if(!$con) return;

        $sqls = [
            "CREATE TABLE IF NOT EXISTS notification (
                id INT(11) NOT NULL AUTO_INCREMENT,
                recipient_type VARCHAR(20) NOT NULL DEFAULT 'user',
                recipient_id INT(11) NOT NULL DEFAULT 0,
                stock_id INT(11) NOT NULL DEFAULT 0,
                type VARCHAR(60) NOT NULL DEFAULT '',
                title VARCHAR(255) NOT NULL DEFAULT '',
                body TEXT NULL,
                data_json TEXT NULL,
                url VARCHAR(500) NULL,
                read_at DATETIME NULL DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_recipient (recipient_type, recipient_id, read_at),
                KEY idx_stock (stock_id, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE IF NOT EXISTS notification_preference (
                id INT(11) NOT NULL AUTO_INCREMENT,
                recipient_type VARCHAR(20) NOT NULL DEFAULT 'user',
                recipient_id INT(11) NOT NULL DEFAULT 0,
                event_type VARCHAR(60) NOT NULL DEFAULT '',
                channel VARCHAR(20) NOT NULL DEFAULT 'inapp',
                enabled TINYINT(1) NOT NULL DEFAULT 1,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_pref (recipient_type, recipient_id, event_type, channel)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE IF NOT EXISTS notification_log (
                id INT(11) NOT NULL AUTO_INCREMENT,
                notification_id INT(11) NOT NULL DEFAULT 0,
                channel VARCHAR(20) NOT NULL DEFAULT '',
                status VARCHAR(20) NOT NULL DEFAULT '',
                detail TEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_notif (notification_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ];

        foreach($sqls as $sql){
            @$con->query($sql);
        }
    }

    public function add(){
        self::ensureSchema();
        $con = Database::getCon();
        $rt = $con->real_escape_string((string)$this->recipient_type);
        $rid = intval($this->recipient_id);
        $sid = intval($this->stock_id);
        $type = $con->real_escape_string((string)$this->type);
        $title = $con->real_escape_string((string)$this->title);
        $body = $con->real_escape_string((string)$this->body);
        $data = $con->real_escape_string((string)$this->data_json);
        $url = $con->real_escape_string((string)$this->url);

        $sql = "INSERT INTO ".self::$tablename."
            (recipient_type,recipient_id,stock_id,type,title,body,data_json,url,created_at)
            VALUES ('$rt',$rid,$sid,'$type','$title','$body','$data','$url',NOW())";
        $r = Executor::doit($sql);
        $this->id = $r[1];
        return $this->id;
    }

    public static function markRead($id, $recipientType, $recipientId){
        self::ensureSchema();
        $con = Database::getCon();
        $id = intval($id);
        $rt = $con->real_escape_string((string)$recipientType);
        $rid = intval($recipientId);
        $sql = "UPDATE ".self::$tablename." SET read_at=NOW()
                WHERE id=$id AND recipient_type='$rt' AND recipient_id=$rid AND read_at IS NULL";
        Executor::doit($sql);
    }

    public static function markAllRead($recipientType, $recipientId){
        self::ensureSchema();
        $con = Database::getCon();
        $rt = $con->real_escape_string((string)$recipientType);
        $rid = intval($recipientId);
        $sql = "UPDATE ".self::$tablename." SET read_at=NOW()
                WHERE recipient_type='$rt' AND recipient_id=$rid AND read_at IS NULL";
        Executor::doit($sql);
    }

    public static function countUnread($recipientType, $recipientId){
        self::ensureSchema();
        $con = Database::getCon();
        $rt = $con->real_escape_string((string)$recipientType);
        $rid = intval($recipientId);
        $sql = "SELECT COUNT(*) AS c FROM ".self::$tablename."
                WHERE recipient_type='$rt' AND recipient_id=$rid AND read_at IS NULL";
        $r = Executor::doit($sql);
        if($r[0]){
            $row = $r[0]->fetch_assoc();
            return intval($row['c'] ?? 0);
        }
        return 0;
    }

    public static function getForRecipient($recipientType, $recipientId, $limit = 20, $unreadOnly = false){
        self::ensureSchema();
        $con = Database::getCon();
        $rt = $con->real_escape_string((string)$recipientType);
        $rid = intval($recipientId);
        $limit = intval($limit);
        $extra = $unreadOnly ? " AND read_at IS NULL" : "";
        $sql = "SELECT * FROM ".self::$tablename."
                WHERE recipient_type='$rt' AND recipient_id=$rid $extra
                ORDER BY id DESC LIMIT $limit";
        $r = Executor::doit($sql);
        return Model::many($r[0], new NotificationData());
    }

    public static function getById($id){
        self::ensureSchema();
        $id = intval($id);
        $r = Executor::doit("SELECT * FROM ".self::$tablename." WHERE id=$id");
        return Model::one($r[0], new NotificationData());
    }

    public static function logDelivery($notificationId, $channel, $status, $detail = ''){
        $con = Database::getCon();
        $nid = intval($notificationId);
        $ch = $con->real_escape_string((string)$channel);
        $st = $con->real_escape_string((string)$status);
        $dt = $con->real_escape_string((string)$detail);
        $sql = "INSERT INTO notification_log (notification_id,channel,status,detail,created_at)
                VALUES ($nid,'$ch','$st','$dt',NOW())";
        @Executor::doit($sql);
    }
      public static function getFiltered($recipientType, $recipientId, $filter = 'all', $eventType = '', $dateFrom = '', $dateTo = '', $page = 1, $perPage = 20){
          self::ensureSchema();
          $con = Database::getCon();
          $rt = $con->real_escape_string((string)$recipientType);
          $rid = intval($recipientId);
          $where = "recipient_type='$rt' AND recipient_id=$rid";
          if($filter === 'unread') $where .= " AND read_at IS NULL";
          if($filter === 'read') $where .= " AND read_at IS NOT NULL";
          if($eventType !== ''){
              $et = $con->real_escape_string((string)$eventType);
              $where .= " AND type='$et'";
          }
          if($dateFrom !== ''){
              $df = $con->real_escape_string((string)$dateFrom);
              $where .= " AND created_at >= '$df 00:00:00'";
          }
          if($dateTo !== ''){
              $dt = $con->real_escape_string((string)$dateTo);
              $where .= " AND created_at <= '$dt 23:59:59'";
          }
          $page = max(1, intval($page));
          $perPage = max(1, min(100, intval($perPage)));
          $offset = ($page - 1) * $perPage;
          $countR = Executor::doit("SELECT COUNT(*) AS c FROM ".self::$tablename." WHERE $where");
          $total = 0;
          if($countR[0]){ $row = $countR[0]->fetch_assoc(); $total = intval($row['c'] ?? 0); }
          $r = Executor::doit("SELECT * FROM ".self::$tablename." WHERE $where ORDER BY id DESC LIMIT $offset,$perPage");
          return ['total' => $total, 'page' => $page, 'perPage' => $perPage, 'rows' => Model::many($r[0], new NotificationData())];
      }

      public static function getLogs($status = '', $limit = 100){
          self::ensureSchema();
          $con = Database::getCon();
          $where = '1=1';
          if($status !== ''){
              $st = $con->real_escape_string((string)$status);
              $where .= " AND l.status='$st'";
          }
          $limit = max(1, min(500, intval($limit)));
          $sql = "SELECT l.id, l.notification_id, l.channel, l.status, l.detail, l.created_at,
                         n.recipient_type, n.recipient_id, n.type AS event_type, n.title
                  FROM notification_log l
                  LEFT JOIN notification n ON n.id = l.notification_id
                  WHERE $where
                  ORDER BY l.id DESC LIMIT $limit";
          $r = Executor::doit($sql);
          $rows = [];
          if($r[0]){ while($row = $r[0]->fetch_assoc()){ $rows[] = $row; } }
          return $rows;
      }
  }
  ?>
  