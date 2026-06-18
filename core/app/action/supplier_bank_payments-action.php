<?php

if(isset($_GET["opt"]) && $_GET["opt"]=="add"){

    $pay = new SupplierBankPaymentData();
    $pay->person_id = isset($_POST["person_id"]) ? intval($_POST["person_id"]) : 0;
    $pay->account_id = isset($_POST["account_id"]) ? intval($_POST["account_id"]) : 0;
    $pay->invoice_ref = isset($_POST["invoice_ref"]) ? trim($_POST["invoice_ref"]) : "";
    $pay->currency = isset($_POST["currency"]) ? trim($_POST["currency"]) : "DOP";
    $pay->exchange_rate = isset($_POST["exchange_rate"]) && $_POST["exchange_rate"]!="" ? floatval($_POST["exchange_rate"]) : 1;
    $pay->amount = isset($_POST["amount"]) && $_POST["amount"]!="" ? floatval($_POST["amount"]) : 0;
    $pay->premium_percent = isset($_POST["premium_percent"]) && $_POST["premium_percent"]!="" ? floatval($_POST["premium_percent"]) : 0;
    $pay->fee_amount = isset($_POST["fee_amount"]) && $_POST["fee_amount"]!="" ? floatval($_POST["fee_amount"]) : 0;
    $pay->payment_date = isset($_POST["payment_date"]) && $_POST["payment_date"]!="" ? $_POST["payment_date"] : date("Y-m-d");
    $pay->reference_no = isset($_POST["reference_no"]) ? trim($_POST["reference_no"]) : "";
    $pay->notes = isset($_POST["notes"]) ? trim($_POST["notes"]) : "";
    $pay->created_by = $_SESSION["user_id"];

    $subtotal_local = $pay->amount * $pay->exchange_rate;
    $pay->premium_amount = $subtotal_local * ($pay->premium_percent / 100);
    $pay->total_local = $subtotal_local + $pay->premium_amount + $pay->fee_amount;

    $pay->add();

    // registrar también movimiento bancario
    $tx = new BankTransactionData();
    $tx->account_id = $pay->account_id;
    $tx->type = ($pay->currency=="DOP") ? "PAGO_SUPLIDOR_LOCAL" : "PAGO_SUPLIDOR_INTERNACIONAL";
    $tx->person_id = $pay->person_id;
    $tx->amount = $pay->amount;
    $tx->exchange_rate = $pay->exchange_rate;
    $tx->premium_percent = $pay->premium_percent;
    $tx->premium_amount = $pay->premium_amount;
    $tx->fee = $pay->fee_amount;
    $tx->total_local = $pay->total_local;
    $tx->direction = "SALIDA";
    $tx->description = "Pago suplidor Ref: ".$pay->invoice_ref." / ".$pay->reference_no;
    $tx->add();

    // actualizar balance de cuenta bancaria
    $acc = BankAccountData::getById($pay->account_id);
    $acc->balance = $acc->balance - $pay->total_local;
    $acc->updBalance();

    echo "true";

}else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){

    $pay = SupplierBankPaymentData::getById($_GET["id"]);

    // devolver balance a la cuenta
    $acc = BankAccountData::getById($pay->account_id);
    $acc->balance = $acc->balance + $pay->total_local;
    $acc->updBalance();

    $pay->del();

    header("location:./?view=supplier_bank_payments&opt=all");
}
?>