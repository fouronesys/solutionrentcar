<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){

$user = $_POST['name'];
$pass = $_POST['password'];
$base = new Database();
$con = $base->connect();
$sql = "select name from user where username=\"".$user."\" and password=\"".$pass."\"";
//print $sql;
$query = $con->query($sql);
$found = false;
while($r = $query->fetch_array()){
	$found = true ;
}

if($found==false) {
	$img = $_POST["base64"];
$img = str_replace('data:image/png;base64,', '', $img);
$fileData = base64_decode($img);
$fileName = "firmas/".uniqid().'.png';

file_put_contents($fileName, $fileData);
    
	$user = new UserData();
	$user->firma = $fileName;
	$user->kind = $_POST["kind"];
	$user->stock_id = StockData::getPrincipal()->id;
	$user->language = $_POST["language"];
    $user->email = $_POST["email"];
	$user->comision = $_POST["comision"];
	$user->name = $_POST["name"];
	$user->lastname = $_POST["lastname"];
	$user->phone = $_POST["phone"];
	$user->status = isset($_POST["status"])?1:0;
	$user->password = sha1(md5($_POST["password"]));
	if ($_POST["gender"]==1) {$user->image="man.png";}else{$user->image="woman.png";}
	$user->add();

if ($_POST["kind"]==1) {
  $id_user = UserData::getAllByID();
  $user_id = $id_user[0]->id!=null?$id_user[0]->id:0;
	for ($i=1; $i <23; $i++) { 
	$user = new UserPermissionsData();
	$user->user_id = $user_id;
	$user->permits_id = $i;
	$user->add();
	}
	}elseif($_POST["kind"]==3){
	$kind_3= array('9', '13', '20');
  $kind_num3 = count($kind_3);

  $id_user = UserData::getAllByID();
  $user_id = $id_user[0]->id!=null?$id_user[0]->id:0;

  for ($i = 0; $i < $array_num3; ++$i){
  $user = new UserPermissionsData();
	$user->user_id = $user_id;
	$user->permits_id = $kind_3[$i];
	$user->add();
  }


	}elseif($_POST["kind"]==4){
	$id_user = UserData::getAllByID();
  $user_id = $id_user[0]->id!=null?$id_user[0]->id:0;
	for ($i=1; $i <23; $i++) { 
	$user = new UserPermissionsData();
	$user->user_id = $user_id;
	$user->permits_id = $i;
	$user->add();
	}
	}elseif($_POST["kind"]==5){
  $kind_5= array('5','12');
  $kind_num5 = count($kind_5);

  $id_user = UserData::getAllByID();
  $user_id = $id_user[0]->id!=null?$id_user[0]->id:0;

  for ($i = 0; $i < $array_num5; ++$i){
  $user = new UserPermissionsData();
	$user->user_id = $user_id;
	$user->permits_id = $kind_5[$i];
	$user->add();
  }
	}

	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Agrego el usuario " .$_POST["name"]."";
          $user->add();
          
echo 'true';
}
	
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
	$user = UserData::getById($_POST["user_id"]);

	$user->stock_id = isset($_POST["stock_id"])?$_POST["stock_id"]:"NULL";
	$user->comision = isset($_POST["comision"])&&$_POST["comision"]!=""?$_POST["comision"]:"NULL";
    
    $img = $_POST["base64"];
$img = str_replace('data:image/png;base64,', '', $img);
$fileData = base64_decode($img);
$fileName = "firmas/".uniqid().'.png';

file_put_contents($fileName, $fileData);
    $user->firma = $fileName;
	$user->name = $_POST["name"];
	$user->lastname = $_POST["lastname"];
	$user->phone = $_POST["phone"];
	$user->language = $_POST["language"];
    $user->no = $_POST["no"];
	$user->comision = $_POST["comision"];
	$user->email = $_POST["email"];
	$user->status = isset($_POST["status"])?1:0;
	if ($_POST["gender"]==1) {
	$user->image="man.png";
	}else{
	$user->image="woman.png";
	}
	$user->update();

	if($_POST["password"]!=""){
		$user->password = sha1(md5($_POST["password"]));
		  $user->update_passwd();
}

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el usuario " .$_POST["name"]."";
          $user->add();
          
echo 'true';
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="updprofile"){
    
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
$uploadPath = "CF-SYSTEMS/storage/profiles/"; 
 
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
 

	$user = UserData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->lastname = $_POST["lastname"];
    $user->image = $fileName;
    $user->profile();

header('location:./?view=home');

}else if(isset($_GET["opt"]) && $_GET["opt"]=="updtype"){
	$user = KData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->update();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el tipo de usuario " .$_POST["name"]."";
          $user->add();

echo 'true';
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){

$user = UserData::getById($_SESSION["user_id"]);

$permits = UserPermissionsData::getAllBySQL("where user_id=".$_GET["id"]);

foreach ($permits as $permit) {
	$permit->del();
}

if($user->kind==1){
	$userx  = UserData::getById($_GET["id"]);

	if($user->id!=$userx->id){
		$userx->del();

	}else{
		Core::alert("Error. No te puedes eliminar a ti mismo!");
	}

}else{
	Core::alert("Error. No tienes permisos!");
}

header('location:./?view=users&opt=all');
}

else if(isset($_GET["opt"]) && $_GET["opt"]=="deltype"){

$user = KData::getById($_GET["id"]);
$user->del();

header('location:./?view=users&opt=all');
}


?>