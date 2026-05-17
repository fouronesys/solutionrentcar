<?php
class BankData {

    public static $tablename = "banks";

    public $id;
    public $name;
    public $status;
    public $created_at;

    public function add(){
        $sql = "insert into ".self::$tablename." (name,status,created_at) ";
        $sql .= "value (\"$this->name\",\"$this->status\",NOW())";
        Executor::doit($sql);
    }

    public function update(){
        $sql = "update ".self::$tablename." set name=\"$this->name\", status=\"$this->status\" where id=$this->id";
        Executor::doit($sql);
    }

    public function del(){
        $sql = "delete from ".self::$tablename." where id=$this->id";
        Executor::doit($sql);
    }

    public static function delById($id){
        $sql = "delete from ".self::$tablename." where id=$id";
        Executor::doit($sql);
    }

    public static function getById($id){
        $sql = "select * from ".self::$tablename." where id=\"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new BankData());
    }

    public static function getAll(){
        $sql = "select * from ".self::$tablename." order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BankData());
    }

    public static function getLike($q){
        $sql = "select * from ".self::$tablename." where name like '%$q%' order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BankData());
    }
}
?>