<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="addspend"){
    $user = new SpendData();
$user->person_id=$_POST["client_id"];
$user->voucher_code = $_POST["voucher_code"];
$user->created_date = $_POST["created_date"];
$user->invoice_code = $_POST["invoice_code"];
$user->created_att = $_POST["created_at"];
$user->p_id = $_POST["p_id"];
$user->expiry_spend = $_POST["expiry_spend"];
$user->type_g = $_POST["type_g"];
$user->type_sg = $_POST["type_sg"];
$user->imp_rent = $_POST["imp_rent"];
$user->itbis_ret = $_POST["itbis_ret"];
$user->f_id = $_POST["f_id"];
$user->name = $_POST["name"];
$valor = intval(str_replace(",", "", $_POST["price"]));
$user->price = $valor; 
$user->user_id = $_SESSION["user_id"];
$user->stock_id = StockData::getPrincipal()->id;
$s = $user->add();

 /// si es credito....
if($_POST["p_id"]==4){
$id_speds = SpendData::getAllByID();
$spends = $id_speds[0]->id!=null?$id_speds[0]->id:0;

                $payment = new PaymentData();
                $payment->sell_id = $spends;
                $payment->val = intval(str_replace(",", "", $_POST["price"]));
                $payment->person_id=$_POST["client_id"];
                $payment->user_id = $_SESSION["user_id"];
                $payment->stock_id = StockData::getPrincipal()->id;
                $payment->operation_type_id = 2;
                $payment->add_spends();
             }

          $user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Agrego el gasto " .$_POST["name"]."";
          $user->add();
          
echo 'true';
}

elseif(isset($_GET["opt"]) && $_GET["opt"]=="addother"){
$user = new SpendData();
$user->created_att = $_POST["created_at"];
$user->p_id = 1;
$user->f_id = $_POST["f_id"];
$user->name = $_POST["name"];
$valor = intval(str_replace(",", "", $_POST["price"]));
$user->price = $valor; 
$user->user_id = $_SESSION["user_id"];
$user->stock_id = StockData::getPrincipal()->id;
$user->add_ext();

          $user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Agrego el gasto " .$_POST["name"]."";
          $user->add();
          
echo 'true';
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="updspend"){
$user = SpendData::getById($_POST["user_id"]);
$user->person_id=$_POST["client_id"]!=""?$_POST["client_id"]:"NULL";
$user->voucher_code = $_POST["voucher_code"];
$user->created_date = $_POST["created_date"];
$user->invoice_code = $_POST["invoice_code"];
$user->created_att = $_POST["created_at"];
$user->p_id = $_POST["p_id"];
$user->expiry_spend = $_POST["expiry_spend"];
$user->type_g = $_POST["type_g"];
$user->type_sg = $_POST["type_sg"];
$user->imp_rent = $_POST["imp_rent"];
$user->itbis_ret = $_POST["itbis_ret"];
$user->f_id = $_POST["f_id"];
$user->name = $_POST["name"];
$valor = intval(str_replace(",", "", $_POST["price"]));
$user->price = $valor;
$user->user_id = $_SESSION["user_id"];
$user->update();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el gasto " .$_POST["name"]."";
          $user->add();

echo 'true';
}
elseif(isset($_GET["opt"]) && $_GET["opt"]=="updother"){
$user = SpendData::getById($_POST["user_id"]);
$user->created_att = $_POST["created_at"];
$user->f_id = $_POST["f_id"];
$user->name = $_POST["name"];
$valor = intval(str_replace(",", "", $_POST["price"]));
$user->price = $valor; 
$user->update_ext();

          $user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el gasto " .$_POST["name"]."";
          $user->add();
          
echo 'true';
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="delspend"){
$category = SpendData::getById($_GET["id"]);
$category->del();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el gasto " .$category->name."";
          $user->add();
if($_GET["kind"]==2):
header('location:./?view=finance&opt=all&spends=Otros');
else:
header('location:./?view=finance&opt=all&spends=Negocio');
endif;
    
}
?>