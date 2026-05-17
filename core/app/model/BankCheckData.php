<?php
class BankCheckData {

    public static $tablename = "bank_checks";

    public $id;
    public $account_id;
    public $check_number;
    public $pay_to;
    public $amount;
    public $issue_date;
    public $status;
    public $concept;
    public $created_by;
    public $created_at;

    public function getAccount(){
        return BankAccountData::getById($this->account_id);
    }

    public function add(){
        $sql = "insert into ".self::$tablename." 
        (account_id,check_number,pay_to,amount,issue_date,concept,status,created_by,created_at) ";
        $sql .= "value (
        \"$this->account_id\",
        \"$this->check_number\",
        \"$this->pay_to\",
        \"$this->amount\",
        \"$this->issue_date\",
        \"$this->concept\",
        \"$this->status\",
        \"$this->created_by\",
        NOW()
        )";
        Executor::doit($sql);
    }

    public function update(){
        $sql = "update ".self::$tablename." set 
        account_id=\"$this->account_id\",
        check_number=\"$this->check_number\",
        pay_to=\"$this->pay_to\",
        amount=\"$this->amount\",
        issue_date=\"$this->issue_date\",
        concept=\"$this->concept\",
        status=\"$this->status\"
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
        return Model::one($query[0], new BankCheckData());
    }

    public static function getAll(){
        $sql = "select * from ".self::$tablename." order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BankCheckData());
    }

    public static function getAllByAccount($account_id){
        $sql = "select * from ".self::$tablename." where account_id=\"$account_id\" order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BankCheckData());
    }

    public static function getAllByStatus($status){
        $sql = "select * from ".self::$tablename." where status=\"$status\" order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BankCheckData());
    }
}
?>