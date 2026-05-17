<?php

if(isset($_GET["opt"]) && $_GET["opt"]=="add"){

    $check = new BankCheckData();
    $check->account_id = isset($_POST["account_id"]) ? intval($_POST["account_id"]) : 0;
    $check->check_number = isset($_POST["check_number"]) ? trim($_POST["check_number"]) : "";
    $check->pay_to = isset($_POST["pay_to"]) ? trim($_POST["pay_to"]) : "";
    $check->amount = isset($_POST["amount"]) && $_POST["amount"]!="" ? floatval($_POST["amount"]) : 0;
    $check->issue_date = isset($_POST["issue_date"]) && $_POST["issue_date"]!="" ? $_POST["issue_date"] : date("Y-m-d");
    $check->concept = isset($_POST["concept"]) ? trim($_POST["concept"]) : "";
    $check->status = isset($_POST["status"]) ? trim($_POST["status"]) : "EMITIDO";
    $check->created_by = $_SESSION["user_id"];
    $check->add();

    // movimiento bancario automático
    $tx = new BankTransactionData();
    $tx->account_id = $check->account_id;
    $tx->type = "CHEQUE";
    $tx->person_id = 0;
    $tx->amount = $check->amount;
    $tx->exchange_rate = 1;
    $tx->premium_percent = 0;
    $tx->premium_amount = 0;
    $tx->fee = 0;
    $tx->total_local = $check->amount;
    $tx->direction = "SALIDA";
    $tx->description = "Cheque No. ".$check->check_number." / ".$check->pay_to." / ".$check->concept;
    $tx->add();

    // descontar cuenta bancaria
    $acc = BankAccountData::getById($check->account_id);
    $acc->balance = $acc->balance - $check->amount;
    $acc->updBalance();

    echo "true";

}else if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){

    $old = BankCheckData::getById($_POST["id"]);

    // revertir balance anterior
    $oldAcc = BankAccountData::getById($old->account_id);
    $oldAcc->balance = $oldAcc->balance + $old->amount;
    $oldAcc->updBalance();

    $check = BankCheckData::getById($_POST["id"]);
    $check->account_id = isset($_POST["account_id"]) ? intval($_POST["account_id"]) : 0;
    $check->check_number = isset($_POST["check_number"]) ? trim($_POST["check_number"]) : "";
    $check->pay_to = isset($_POST["pay_to"]) ? trim($_POST["pay_to"]) : "";
    $check->amount = isset($_POST["amount"]) && $_POST["amount"]!="" ? floatval($_POST["amount"]) : 0;
    $check->issue_date = isset($_POST["issue_date"]) && $_POST["issue_date"]!="" ? $_POST["issue_date"] : date("Y-m-d");
    $check->concept = isset($_POST["concept"]) ? trim($_POST["concept"]) : "";
    $check->status = isset($_POST["status"]) ? trim($_POST["status"]) : "EMITIDO";
    $check->update();

    // aplicar balance nuevo
    $newAcc = BankAccountData::getById($check->account_id);
    $newAcc->balance = $newAcc->balance - $check->amount;
    $newAcc->updBalance();

    echo "true";

}else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){

    $check = BankCheckData::getById($_GET["id"]);

    // devolver balance
    $acc = BankAccountData::getById($check->account_id);
    $acc->balance = $acc->balance + $check->amount;
    $acc->updBalance();

    $check->del();

    header("location:./?view=bank_checks&opt=all");
}
?>