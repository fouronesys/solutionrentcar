<?php

if(isset($_GET["opt"]) && $_GET["opt"]=="add"){

    $bank = new BankData();
    $bank->name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $bank->status = isset($_POST["status"]) ? 1 : 0;
    $bank->add();

    echo "true";

}else if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){

    $bank = BankData::getById($_POST["id"]);
    $bank->name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $bank->status = isset($_POST["status"]) ? 1 : 0;
    $bank->update();

    echo "true";

}else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){

    $bank = BankData::getById($_GET["id"]);
    $bank->del();

    header("location:./?view=banks&opt=all");
}
?>