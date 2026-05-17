<?php
class BankReconciliationData {

    public static $tablename = "bank_reconciliations";

    public $id;
    public $account_id;
    public $balance_bank;
    public $balance_system;
    public $difference;
    public $created_at;

    public function getAccount(){
        return BankAccountData::getById($this->account_id);
    }

    public function add(){
        $sql = "insert into ".self::$tablename." (account_id,balance_bank,balance_system,difference,created_at) ";
        $sql .= "value (
        \"$this->account_id\",
        \"$this->balance_bank\",
        \"$this->balance_system\",
        \"$this->difference\",
        NOW()
        )";
        Executor::doit($sql);
    }

    public function update(){
        $sql = "update ".self::$tablename." set
        account_id=\"$this->account_id\",
        balance_bank=\"$this->balance_bank\",
        balance_system=\"$this->balance_system\",
        difference=\"$this->difference\"
        where id=$this->id";
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
        return Model::one($query[0], new BankReconciliationData());
    }

    public static function getAll(){
        $sql = "select * from ".self::$tablename." order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BankReconciliationData());
    }

    public static function getAllByAccount($account_id){
        $sql = "select * from ".self::$tablename." where account_id=\"$account_id\" order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BankReconciliationData());
    }
}
?>