<?php
class TaskData {
  public static $tablename = "tasks";

  public function __construct(){
    $this->created_at = "NOW()";
    $this->status = "PENDIENTE";
    $this->priority = "MEDIA";
    $this->source_type = "MANUAL";
  }

  public static function getById($id){
    $sql = "select * from ".self::$tablename." where id=$id";
    $query = Executor::doit($sql);
    return Model::one($query[0],new TaskData());
  }

  public static function getAll($stock_id){
    $sql = "select * from ".self::$tablename." where stock_id=$stock_id order by 
            FIELD(status,'PENDIENTE','EN_PROCESO','POSPUESTO','HECHO'),
            FIELD(priority,'ALTA','MEDIA','BAJA'),
            id desc";
    $query = Executor::doit($sql);
    return Model::many($query[0],new TaskData());
  }

  public static function existsAuto($stock_id,$source_key,$ref_table,$ref_id){
    $source_key = addslashes($source_key);
    $ref_table = addslashes($ref_table);
    $sql = "select id from ".self::$tablename." 
            where stock_id=$stock_id 
              and source_type='AUTO'
              and source_key='$source_key'
              and ref_table='$ref_table'
              and ref_id=".intval($ref_id)."
            limit 1";
    $q = Executor::doit($sql);
    $row = $q[0]->fetch_array();
    return $row ? intval($row[0]) : 0;
  }

  public function add(){
    $sql = "insert into ".self::$tablename." 
      (stock_id,user_id,source_type,source_key,ref_table,ref_id,title,description,priority,status,due_date,done_at,created_at)
      value
      (\"$this->stock_id\",\"$this->user_id\",
       \"$this->source_type\",\"$this->source_key\",\"$this->ref_table\",\"$this->ref_id\",
       \"$this->title\",\"$this->description\",
       \"$this->priority\",\"$this->status\",\"$this->due_date\",NULL,NOW())";
    Executor::doit($sql);
  }

  public function update(){
    $sql = "update ".self::$tablename." set
      user_id=\"$this->user_id\",
      title=\"$this->title\",
      description=\"$this->description\",
      priority=\"$this->priority\",
      status=\"$this->status\",
      due_date=\"$this->due_date\"
    where id=$this->id";
    Executor::doit($sql);
  }

  public function setStatus($st){
    $st = addslashes($st);
    $sql = "update ".self::$tablename." set status='$st' where id=$this->id";
    Executor::doit($sql);
  }

  public function done(){
    $sql = "update ".self::$tablename." set status='HECHO', done_at=NOW() where id=$this->id";
    Executor::doit($sql);
  }

  public function reopen(){
    $sql = "update ".self::$tablename." set status='PENDIENTE', done_at=NULL where id=$this->id";
    Executor::doit($sql);
  }

  public function postpone($days=3){
    $days = intval($days);
    $sql = "update ".self::$tablename."
            set status='POSPUESTO',
                due_date=DATE_ADD(CURDATE(), INTERVAL $days DAY)
            where id=$this->id";
    Executor::doit($sql);
  }

  public function del(){
    $sql = "delete from ".self::$tablename." where id=$this->id";
    Executor::doit($sql);
  }
}
?>
