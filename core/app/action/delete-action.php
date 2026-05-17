<?php if(isset($_GET["opt"]) && $_GET["opt"]=="permissions"){

$product = UserPermissionsData::getById($_GET["id"]);
$product->del();
header('location:./?view=userpermissions&id='.$_GET["user_id"]);
//////////////////////////////////////////////////////////// PRINT ///////////////////////////////////////////////////////

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="wait"){

$product = WaitData::getById($_GET["id"]);
$product->del();
header('location:./?view=booking&opt=earring');
//////////////////////////////////////////////////////////// PRINT ///////////////////////////////////////////////////////

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="receipt"){

/// si es Consumidor Final....
if($_GET["c_id"]==1){
$cf = CFData::getById($_GET["id"]);
$cf->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el comprobante " .$cf->name;
          $user->add();

header('location:./?view=receipt&opt=all');

}
/// si es Credito Fiscal....
elseif($_GET["c_id"]==2){
$cfs = CFSData::getById($_GET["id"]);
$cfs->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el comprobante " .$cfs->name;
          $user->add();

header('location:./?view=receipt&opt=all');

}
/// si es Gubernamental....
elseif($_GET["c_id"]==3){
$cg = CGData::getById($_GET["id"]);
$cg->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el comprobante " .$cg->name;
          $user->add();

header('location:./?view=receipt&opt=all');

}
/// si es Nota de Credito....
elseif($_GET["c_id"]==4){
$cnc = CNCData::getById($_GET["id"]);
$cnc->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el comprobante " .$cnc->name;
          $user->add();

header('location:./?view=receipt&opt=all');

}
/// si es Nota de Debito....
elseif($_GET["c_id"]==8){
$cnd = CNDData::getById($_GET["id"]);
$cnd->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el comprobante " .$cnd->name;
          $user->add();

header('location:./?view=receipt&opt=all');

}
/// si es Compras....
elseif($_GET["c_id"]==9){
$ccp = CCPData::getById($_GET["id"]);
$ccp->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el comprobante " .$ccp->name;
          $user->add();

header('location:./?view=receipt&opt=all');

}
/// si es Gastos....
elseif($_GET["c_id"]==10){
$cgt = CGTData::getById($_GET["id"]);
$cgt->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el comprobante " .$cgt->name;
          $user->add();

header('location:./?view=receipt&opt=all');

}
/// si es Regimen Special....
elseif($_GET["c_id"]==11){
$csr = CRSData::getById($_GET["id"]);
$csr->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el comprobante " .$csr->name;
          $user->add();

header('location:./?view=receipt&opt=all');

}
////////////////////////////////////////////////////////// TRASPASOS //////////////////////////////////////////////////////

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="payment"){
$sell = PaymentData::getById($_GET["id"]);
$sell->del();

header('location:./?view=make&opt=history&id='.$_GET["person_id"]);
///////////////////////////////////////////////////////////// PAYMENT /////////////////////////////////////////////////

}
?>