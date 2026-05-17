<?php
class SupplierBankPaymentData {

    public static $tablename = "supplier_payments_bank";

    public $id;
    public $person_id;
    public $account_id;
    public $invoice_ref;
    public $currency;
    public $exchange_rate;
    public $amount;
    public $premium_percent;
    public $premium_amount;
    public $fee_amount;
    public $total_local;
    public $payment_date;
    public $reference_no;
    public $notes;
    public $created_by;
    public $created_at;

    public function getSupplier(){
        return PersonData::getById($this->person_id);
    }

    public function getAccount(){
        return BankAccountData::getById($this->account_id);
    }

    public function add(){
        $sql = "insert into ".self::$tablename." 
        (person_id,account_id,invoice_ref,currency,exchange_rate,amount,premium_percent,premium_amount,fee_amount,total_local,payment_date,reference_no,notes,created_by,created_at) ";
        $sql .= "value (
        \"$this->person_id\",
        \"$this->account_id\",
        \"$this->invoice_ref\",
        \"$this->currency\",
        \"$this->exchange_rate\",
        \"$this->amount\",
        \"$this->premium_percent\",
        \"$this->premium_amount\",
        \"$this->fee_amount\",
        \"$this->total_local\",
        \"$this->payment_date\",
        \"$this->reference_no\",
        \"$this->notes\",
        \"$this->created_by\",
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
        return Model::one($query[0], new SupplierBankPaymentData());
    }

    public static function getAll(){
        $sql = "select * from ".self::$tablename." order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new SupplierBankPaymentData());
    }

    public static function getAllBySupplier($person_id){
        $sql = "select * from ".self::$tablename." where person_id=\"$person_id\" order by id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new SupplierBankPaymentData());
    }

    public static function getAllBySQL($sqlExtra){
        $sql = "select * from ".self::$tablename." $sqlExtra";
        $query = Executor::doit($sql);
        return Model::many($query[0], new SupplierBankPaymentData());
    }
}
?>