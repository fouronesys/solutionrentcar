<?php

if(isset($_GET["opt"]) && $_GET["opt"]=="add"){

    $acc = new BankAccountData();
    $acc->bank_id = isset($_POST["bank_id"]) ? intval($_POST["bank_id"]) : 0;
    $acc->account_name = isset($_POST["account_name"]) ? trim($_POST["account_name"]) : "";
    $acc->account_number = isset($_POST["account_number"]) ? trim($_POST["account_number"]) : "";
    $acc->currency = isset($_POST["currency"]) ? trim($_POST["currency"]) : "DOP";
    $acc->balance = isset($_POST["balance"]) && $_POST["balance"]!="" ? floatval($_POST["balance"]) : 0;
    $acc->add();

    echo "true";

}else if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){

    $acc = BankAccountData::getById($_POST["id"]);
    $acc->bank_id = isset($_POST["bank_id"]) ? intval($_POST["bank_id"]) : 0;
    $acc->account_name = isset($_POST["account_name"]) ? trim($_POST["account_name"]) : "";
    $acc->account_number = isset($_POST["account_number"]) ? trim($_POST["account_number"]) : "";
    $acc->currency = isset($_POST["currency"]) ? trim($_POST["currency"]) : "DOP";
    $acc->balance = isset($_POST["balance"]) && $_POST["balance"]!="" ? floatval($_POST["balance"]) : 0;
    $acc->update();

    echo "true";

}else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){

    $acc = BankAccountData::getById($_GET["id"]);
    $acc->del();

    header("location:./?view=bank_accounts&opt=all");
}
?>