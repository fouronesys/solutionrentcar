<?php
class BankAccountData {

    public static $tablename = "bank_accounts";

    public $id;
    public $bank_id;
    public $account_name;
    public $account_number;
    public $currency;
    public $balance;
    public $created_at;

    public function getBank(){
        return BankData::getById($this->bank_id);
    }

    public function add(){
        $sql = "insert into ".self::$tablename." (bank_id,account_name,account_number,currency,balance,created_at) ";
        $sql .= "value (\"$this->bank_id\",\"$this->account_name\",\"$this->account_number\",\"$this->currency\",\"$this->balance\",NOW())";
        Executor::doit($sql);
    }

    public function update(){
        $sql = "update ".self::$tablename." set 
        bank_id=\"$this->bank_id\",
        account_name=\"$this->account_name\",
        account_number=\"$this->account_number\",
        currency=\"$this->currency\",
        balance=\"$this->balance\"
        where id=$this->id";
        Executor::doit($sql);
    }

    public function updBalance(){
        $sql = "update ".self::$tablename." set balance=\"$this->balance\" where id=$this->id";
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
        return Model::one($query[0], new BankAccountData());
    }

    public static function getAll(){
        $sql = "select * from ".self::$tablename." order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BankAccountData());
    }

    public static function getAllByBank($bank_id){
        $sql = "select * from ".self::$tablename." where bank_id=\"$bank_id\" order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BankAccountData());
    }

    public static function getLike($q){
        $sql = "select * from ".self::$tablename." 
                where account_name like '%$q%' 
                or account_number like '%$q%' 
                or currency like '%$q%'
                order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BankAccountData());
    }
}
?>