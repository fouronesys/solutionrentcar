<?php

if(isset($_GET["opt"]) && $_GET["opt"]=="add"){

    $tx = new BankTransactionData();
    $tx->account_id = isset($_POST["account_id"]) ? intval($_POST["account_id"]) : 0;
    $tx->type = isset($_POST["type"]) ? trim($_POST["type"]) : "";
    $tx->person_id = isset($_POST["person_id"]) && $_POST["person_id"]!="" ? intval($_POST["person_id"]) : 0;
    $tx->amount = isset($_POST["amount"]) && $_POST["amount"]!="" ? floatval($_POST["amount"]) : 0;
    $tx->exchange_rate = isset($_POST["exchange_rate"]) && $_POST["exchange_rate"]!="" ? floatval($_POST["exchange_rate"]) : 1;
    $tx->premium_percent = isset($_POST["premium_percent"]) && $_POST["premium_percent"]!="" ? floatval($_POST["premium_percent"]) : 0;
    $tx->fee = isset($_POST["fee"]) && $_POST["fee"]!="" ? floatval($_POST["fee"]) : 0;
    $tx->direction = isset($_POST["direction"]) ? trim($_POST["direction"]) : "SALIDA";
    $tx->description = isset($_POST["description"]) ? trim($_POST["description"]) : "";

    // cálculo financiero
    $subtotal_local = $tx->amount * $tx->exchange_rate;
    $tx->premium_amount = $subtotal_local * ($tx->premium_percent / 100);
    $tx->total_local = $subtotal_local + $tx->premium_amount + $tx->fee;

    $tx->add();

    // actualizar balance de la cuenta
    $acc = BankAccountData::getById($tx->account_id);
    if($tx->direction=="ENTRADA"){
        $acc->balance = $acc->balance + $tx->total_local;
    }else{
        $acc->balance = $acc->balance - $tx->total_local;
    }
    $acc->updBalance();

    echo "true";

}else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){

    $tx = BankTransactionData::getById($_GET["id"]);

    // revertir balance antes de borrar
    $acc = BankAccountData::getById($tx->account_id);
    if($tx->direction=="ENTRADA"){
        $acc->balance = $acc->balance - $tx->total_local;
    }else{
        $acc->balance = $acc->balance + $tx->total_local;
    }
    $acc->updBalance();

    $tx->del();

    header("location:./?view=bank_transactions&opt=all");
}
?>