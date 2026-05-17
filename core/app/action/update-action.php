<?php if(isset($_GET["opt"]) && $_GET["opt"]=="profile"){

if(count($_POST)>0){
	$user = UserData::getById($_POST["user_id"]);

  if(isset($_FILES["image"])){
    $image = new Upload($_FILES["image"]);
    if($image->uploaded){
      $image->Process("../CF-SYSTEMS/storage/profiles/");
      if($image->processed){
        $user->image = $image->file_dst_name;
      }
    }
  }
	$user->name = $_POST["name"];
	$user->lastname = $_POST["lastname"];
	$user->username = $_POST["username"];
	$user->email = $_POST["email"];
	$user->status = Core::$user->status;
	$user->comision = Core::$user->comision!=""?Core::$user->comision:"NULL";
	$user->stock_id = Core::$user->stock_id!=""?Core::$user->stock_id:"NULL";
	$user->update();

	if($_POST["password"]!=""){
		$user->password = sha1(md5($_POST["password"]));
		$user->update_passwd();
print "<script>alert('Se ha actualizado el password');</script>";

	}
header('location:./?view=profile');


}

////////////////////////////////////////////////////////////////////// SETTINGS //////////////////////////////////////////
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="settings"){

/* 
 * Función personalizada para comprimir y 
 * subir una imagen mediante PHP
 */ 
function compressImage($source, $destination, $quality) { 
    // Obtenemos la información de la imagen
    $imgInfo = getimagesize($source); 
    $mime = $imgInfo['mime']; 
     
    // Creamos una imagen
    switch($mime){ 
        case 'image/jpeg': 
            $image = imagecreatefromjpeg($source); 
            break; 
        case 'image/png': 
            $image = imagecreatefrompng($source); 
            break; 
        case 'image/gif': 
            $image = imagecreatefromgif($source); 
            break; 
        default: 
            $image = imagecreatefromjpeg($source); 
    } 
     
    // Guardamos la imagen
    imagejpeg($image, $destination, $quality); 
     
    // Devolvemos la imagen comprimida
    return $destination; 
} 
 
 
// Ruta subida
$uploadPath = "CF-SYSTEMS/storage/configuration/"; 

// Ruta subida
$uploadWebPath = "WEB/img/"; 

    $user = StockData::getById($_POST["user_id"]);
	$user->location = $_POST["location"];
	$user->name = $_POST["name"];
	$user->address = $_POST["address"];
	$user->rnc = $_POST["rnc"];
	$user->color = $_POST["color"];
	$user->phone = $_POST["phone"];
	$user->phone2 = $_POST["phone2"];
	$user->field1 = $_POST["field1"];
	$user->field2 = $_POST["field2"];
	$user->notario = $_POST["notario"];
	$user->no_notario = $_POST["no_notario"];
	$user->imp_name = $_POST["imp_name"];
	$user->imp_val = $_POST["imp_val"];
	$user->frame = $_POST["frame"];
	$user->method = $_POST["method"];
	$user->card = $_POST["card"];
	$user->email = $_POST["email"];
	$user->currency = $_POST["currency"];
	$user->witness1 = $_POST["witness1"];
	$user->no_witness1 = $_POST["no_witness1"];
	$user->witness2 = $_POST["witness2"];
	$user->no_witness2 = $_POST["no_witness2"];

// Si el fichero se ha enviado
$status = $statusMsg = ''; 
if(isset($_FILES["image"])){ 
    $status = 'error'; 
    if(!empty($_FILES["image"]["name"])) { 
        // File info 
        $fileName = basename($_FILES["image"]["name"]); 
        $imageUploadPath = $uploadPath . $fileName; 
        $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION); 
         
        // Permitimos solo unas extensiones
        $allowTypes = array('jpg','png','jpeg','gif'); 
        if(in_array($fileType, $allowTypes)){ 
            // Image temp source 
            $imageTemp = $_FILES["image"]["tmp_name"]; 
             
            // Comprimos el fichero
            $compressedImage = compressImage($imageTemp, $imageUploadPath, 75); 
             
            if($compressedImage){ 
                $status = 'success'; 
                $statusMsg = "La imagen se ha subido satisfactoriamente.";
                 $user->ticket_image = $fileName;
            }else{ 
                $statusMsg = "La compresion de la imagen ha fallado"; 
            } 
        }else{ 
            $statusMsg = 'Lo sentimos, solo se permiten imágenes con estas extensiones: JPG, JPEG, PNG, & GIF.'; 
        } 
    }else{ 
        $statusMsg = 'Por favor, selecciona una imagen.'; 
    } 
} 

// Si el fichero se ha enviado
$status2 = $statusMsg2 = ''; 
if(isset($_FILES["type_img"])){ 
    $status2 = 'error'; 
    if(!empty($_FILES["type_img"]["name"])) { 
        // File info 
        $fileName2 = basename($_FILES["type_img"]["name"]); 
        $imageUploadPath2 = $uploadPath . $fileName2; 
        $fileType2 = pathinfo($imageUploadPath2, PATHINFO_EXTENSION); 
         
        // Permitimos solo unas extensiones
        $allowTypes2 = array('jpg','png','jpeg','gif'); 
        if(in_array($fileType2, $allowTypes2)){ 
            // Image temp source 
            $imageTemp2 = $_FILES["type_img"]["tmp_name"]; 
             
            // Comprimos el fichero
            $compressedImage2 = compressImage($imageTemp2, $imageUploadPath2, 75); 
             
            if($compressedImage2){ 
                $status2 = 'success'; 
                $user->type_img = $fileName2;
                $statusMsg2 = "La imagen se ha subido satisfactoriamente."; 
            }else{ 
                $statusMsg2 = "La compresion de la imagen ha fallado"; 
            } 
        }else{ 
            $statusMsg2 = 'Lo sentimos, solo se permiten imágenes con estas extensiones: JPG, JPEG, PNG, & GIF.'; 
        } 
    }else{ 
        $statusMsg2 = 'Por favor, selecciona una imagen.'; 
    } 
} 
 
 
// Si el fichero se ha enviado
$status3 = $statusMsg3 = ''; 
if(isset($_FILES["web_img"])){ 
    $status2 = 'error'; 
    if(!empty($_FILES["web_img"]["name"])) { 
        // File info 
        $fileName3 = basename($_FILES["web_img"]["name"]); 
        $imageUploadPath3 = $uploadWebPath . $fileName3; 
        $fileType3 = pathinfo($imageUploadPath3, PATHINFO_EXTENSION); 
         
        // Permitimos solo unas extensiones
        $allowTypes3 = array('jpg','png','jpeg','gif'); 
        if(in_array($fileType3, $allowTypes3)){ 
            // Image temp source 
            $imageTemp3 = $_FILES["web_img"]["tmp_name"]; 
             
            // Comprimos el fichero
            $compressedImage3 = compressImage($imageTemp3, $imageUploadPath3, 75); 
             
            if($compressedImage3){ 
                $status3 = 'success'; 
                $statusMsg3 = "La imagen se ha subido satisfactoriamente."; 
                $user->web_img = $fileName3;
 
            }else{ 
                $statusMsg3 = "La compresion de la imagen ha fallado"; 
            } 
        }else{ 
            $statusMsg3 = 'Lo sentimos, solo se permiten imágenes con estas extensiones: JPG, JPEG, PNG, & GIF.'; 
        } 
    }else{ 
        $statusMsg3 = 'Por favor, selecciona una imagen.'; 
    } 
}
 
	$user->update();

header('location:./?view=settings');
////////////////////////////////////////////////////////////////////// PERMISSIONS ///////////////////////////////////////
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="permissions"){

if(count($_POST)>0){
	$product = PUData::getById($_POST["product_id"]);

  $product->name = $_POST["name"];
  $product->location = $_POST["location"];
  $product->is_active = isset($_POST["is_active"])?1:0;
  $product->update();

	
	setcookie("prdupd","true");
header('location:./?view=permissions');

}

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="receipt"){

/// si es Consumidor Final....
if($_POST["c_id"]==1){
    
if(count($_POST)>0){
$cf = CFData::getById($_POST["user_id"]);
$cf->name_in = $_POST["name_in"];
$cf->update2();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el comprobante " .$_POST["name"]."";
          $user->add();

header('location:./?view=receipt&opt=all');

}
}
/// si es Credito Fiscal....
elseif($_POST["c_id"]==2){

if(count($_POST)>0){
$cfs = CFSData::getById($_POST["user_id"]);
$cfs->name_in = $_POST["name_in"];
$cfs->name_out = $_POST["name_out"];
$cfs->created_at = $_POST["created_at"];
$cfs->update2();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el comprobante " .$_POST["name"]."";
          $user->add();

header('location:./?view=receipt&opt=all');


}
}
/// si es Gubernamental....
elseif($_POST["c_id"]==3){

if(count($_POST)>0){
$cg = CGData::getById($_POST["user_id"]);
$cg->name_in = $_POST["name_in"];
$cg->name_out = $_POST["name_out"];
$cg->created_at = $_POST["created_at"];
$cg->update2();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el comprobante " .$_POST["name"]."";
          $user->add();

header('location:./?view=receipt&opt=all');


}
}
/// si es Nota de Credito....
elseif($_POST["c_id"]==4){

if(count($_POST)>0){
$cnc = CNCData::getById($_POST["user_id"]);
$cnc->name_in = $_POST["name_in"];
$cnc->name_out = $_POST["name_out"];
$cnc->created_at = $_POST["created_at"];
$cnc->update2();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el comprobante " .$_POST["name"]."";
          $user->add();

header('location:./?view=receipt&opt=all');


}
}
/// si es Nota de Debito....
elseif($_POST["c_id"]==8){

if(count($_POST)>0){
$cnd = CNDData::getById($_POST["user_id"]);
$cnd->name_in = $_POST["name_in"];
$cnd->name_out = $_POST["name_out"];
$cnd->created_at = $_POST["created_at"];
$cnd->update2();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el comprobante " .$_POST["name"]."";
          $user->add();

header('location:./?view=receipt&opt=all');


}
}
/// si es Compras....
elseif($_POST["c_id"]==9){

if(count($_POST)>0){
$ccp = CCPData::getById($_POST["user_id"]);
$ccp->name_in = $_POST["name_in"];
$ccp->name_out = $_POST["name_out"];
$ccp->created_at = $_POST["created_at"];
$ccp->update2();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el comprobante " .$_POST["name"]."";
          $user->add();

header('location:./?view=receipt&opt=all');


}
}
/// si es Gastos....
elseif($_POST["c_id"]==10){

if(count($_POST)>0){
$cgt = CGTData::getById($_POST["user_id"]);
$cgt->name_in = $_POST["name_in"];
$cgt->name_out = $_POST["name_out"];
$cgt->created_at = $_POST["created_at"];
$cgt->update2();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el comprobante " .$_POST["name"]."";
          $user->add();

header('location:./?view=receipt&opt=all');


}
}
/// si es Regimen Special....
elseif($_POST["c_id"]==11){

if(count($_POST)>0){
$csr = CRSData::getById($_POST["user_id"]);
$csr->name_in = $_POST["name_in"];
$csr->name_out = $_POST["name_out"];
$csr->created_at = $_POST["created_at"];
$csr->update2();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el comprobante " .$_POST["name"]."";
          $user->add();

header('location:./?view=receipt&opt=all');


}
}
}

?>