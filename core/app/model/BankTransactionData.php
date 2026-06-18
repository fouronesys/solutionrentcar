<?php
class BankTransactionData {

    public static $tablename = "bank_transactions";

    public $id;
    public $account_id;
    public $type;
    public $person_id;
    public $amount;
    public $exchange_rate;
    public $premium_percent;
    public $premium_amount;
    public $fee;
    public $total_local;
    public $direction;
    public $description;
    public $created_at;

    public function getAccount(){
        return BankAccountData::getById($this->account_id);
    }

    public function getPerson(){
        return PersonData::getById($this->person_id);
    }

    public function add(){
        $sql = "insert into ".self::$tablename." 
        (account_id,type,person_id,amount,exchange_rate,premium_percent,premium_amount,fee,total_local,direction,description,created_at) ";
        $sql .= "value (
        \"$this->account_id\",
        \"$this->type\",
        \"$this->person_id\",
        \"$this->amount\",
        \"$this->exchange_rate\",
        \"$this->premium_percent\",
        \"$this->premium_amount\",
        \"$this->fee\",
        \"$this->total_local\",
        \"$this->direction\",
        \"$this->description\",
        NOW()
        )";
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
        return Model::one($query[0], new BankTransactionData());
    }

    public static function getAll(){
        $sql = "select * from ".self::$tablename." order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BankTransactionData());
    }

    public static function getAllByAccount($account_id){
        $sql = "select * from ".self::$tablename." where account_id=\"$account_id\" order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BankTransactionData());
    }

    public static function getAllByType($type){
        $sql = "select * from ".self::$tablename." where type=\"$type\" order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BankTransactionData());
    }

    public static function getAllBySQL($sqlExtra){
        $sql = "select * from ".self::$tablename." $sqlExtra";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BankTransactionData());
    }
}
?>