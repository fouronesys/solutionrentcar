<?php
class IncidentData {
  public static $tablename = "incidents";

  public function __construct(){
    $this->created_at = "NOW()";
    $this->status = "ABIERTO";
    $this->severity = "LEVE";
    $this->cost = 0;
  }

  public function getUser(){ return UserData::getById($this->user_id); }
  public function getPerson(){ return PersonData::getById($this->person_id); }
  public function getCar(){ return CarsData::getById($this->car_id); }
  public function getBooking(){ return BookingData::getById($this->booking_id); }
  public function getMaintenance(){ return MaintenanceData::getById($this->maintenance_id); }

  public static function getById($id){
    $sql = "select * from ".self::$tablename." where id=$id";
    $query = Executor::doit($sql);
    return Model::one($query[0],new IncidentData());
  }

  public static function getAllBySQL($where){
    $sql = "select * from ".self::$tablename." ".$where;
    $query = Executor::doit($sql);
    return Model::many($query[0],new IncidentData());
  }

  public static function getAll($stock_id){
    $sql = "select * from ".self::$tablename." where stock_id=$stock_id order by id desc";
    $query = Executor::doit($sql);
    return Model::many($query[0],new IncidentData());
  }

  public function add(){
    $sql = "insert into ".self::$tablename." 
    (stock_id,user_id,person_id,car_id,booking_id,maintenance_id,code,title,description,category,severity,status,cost,due_date,resolved_at,created_at)
    value 
    (\"$this->stock_id\",\"$this->user_id\",\"$this->person_id\",\"$this->car_id\",\"$this->booking_id\",\"$this->maintenance_id\",
     \"$this->code\",\"$this->title\",\"$this->description\",\"$this->category\",\"$this->severity\",\"$this->status\",
     \"$this->cost\",\"$this->due_date\",NULL,NOW())";
    Executor::doit($sql);
  }

  public function update(){
    $sql = "update ".self::$tablename." set 
      user_id=\"$this->user_id\",
      person_id=\"$this->person_id\",
      car_id=\"$this->car_id\",
      booking_id=\"$this->booking_id\",
      maintenance_id=\"$this->maintenance_id\",
      code=\"$this->code\",
      title=\"$this->title\",
      description=\"$this->description\",
      category=\"$this->category\",
      severity=\"$this->severity\",
      status=\"$this->status\",
      cost=\"$this->cost\",
      due_date=\"$this->due_date\"
    where id=$this->id";
    Executor::doit($sql);
  }

  public function close(){
    $sql = "update ".self::$tablename." set status='CERRADO', resolved_at=NOW() where id=$this->id";
    Executor::doit($sql);
  }

  public function del(){
    $sql = "delete from ".self::$tablename." where id=$this->id";
    Executor::doit($sql);
  }
}
?>
