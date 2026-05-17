<?php

if(isset($_GET["opt"]) && $_GET["opt"]=="add"){

    $rec = new BankReconciliationData();
    $rec->account_id = isset($_POST["account_id"]) ? intval($_POST["account_id"]) : 0;
    $rec->balance_bank = isset($_POST["balance_bank"]) && $_POST["balance_bank"]!="" ? floatval($_POST["balance_bank"]) : 0;
    $rec->balance_system = isset($_POST["balance_system"]) && $_POST["balance_system"]!="" ? floatval($_POST["balance_system"]) : 0;
    $rec->difference = $rec->balance_bank - $rec->balance_system;
    $rec->add();

    echo "true";

}else if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){

    $rec = BankReconciliationData::getById($_POST["id"]);
    $rec->account_id = isset($_POST["account_id"]) ? intval($_POST["account_id"]) : 0;
    $rec->balance_bank = isset($_POST["balance_bank"]) && $_POST["balance_bank"]!="" ? floatval($_POST["balance_bank"]) : 0;
    $rec->balance_system = isset($_POST["balance_system"]) && $_POST["balance_system"]!="" ? floatval($_POST["balance_system"]) : 0;
    $rec->difference = $rec->balance_bank - $rec->balance_system;
    $rec->update();

    echo "true";

}else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){

    $rec = BankReconciliationData::getById($_GET["id"]);
    $rec->del();

    header("location:./?view=bank_reconciliations&opt=all");
}
?>