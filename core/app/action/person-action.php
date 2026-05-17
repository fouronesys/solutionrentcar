<?php if(isset($_GET["opt"]) && $_GET["opt"]=="add"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
$uploadPath = "CF-SYSTEMS/storage/invoice_files/"; 


$location = LocationData::getById($_POST["location"]);
  

function generarMatricula($nombreCliente, $telefonoRaw) {
    // 1. Generar prefijo desde el nombre del cliente
    $prefijo = generarPrefijo($nombreCliente);

    // 2. Limpiar el número de teléfono
    $telefono = limpiarTelefono($telefonoRaw);

    // 3. Combinar prefijo + teléfono
    $matricula = $prefijo . $telefono;

    return $matricula;
}

function generarPrefijo($nombre) {
    // Limpiar y convertir a ASCII
    $nombre_limpio = strtoupper(preg_replace('/[^A-Z]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $nombre)));

    // Tomar primeras letras de las primeras dos palabras
    $palabras = explode(" ", $nombre_limpio);
    $prefijo = "";

    foreach ($palabras as $p) {
        if (strlen($p) > 0 && strlen($prefijo) < 3) {
            $prefijo .= $p[0];
        }
    }

    // Asegurar al menos dos letras
    if (strlen($prefijo) < 2) {
        $prefijo .= substr($nombre_limpio, 0, 3 - strlen($prefijo));
    }

    return strtoupper($prefijo) . "-";
}

function limpiarTelefono($telefono) {
    // Quitar todo lo que no sea número
    return preg_replace('/\D/', '', $telefono);
}


$nombreCliente = StockData::getPrincipal()->name;
$telefonoIngresado = $_POST["phone"]; // O viene de $_POST["phone"]


// Generar una matrícula
$matriculaGenerada = generarMatricula($nombreCliente, $telefonoIngresado);
  
 
$user = new PersonData();
  $user->name = $_POST["name"];
  $user->no = $_POST["no"];
  $user->rnc = $_POST["rnc"];
  $user->language = $_POST["language"];
  $user->birthday = $_POST["birthday"];
  $user->gender = $_POST["gender"];
  $user->username = $matriculaGenerada;
  $user->password =  $user->password = sha1(md5($matriculaGenerada));
  $user->reference = $_POST["reference"];
  $user->location = $_POST["location"];
  $user->longitud = $location->longitud;
  $user->latitud = $location->latitud;
  $user->license = $_POST["license"];
  $user->email = $_POST["email"];
  $user->expirelicense = $_POST["expirelicense"];
  $user->issuedlicense = $_POST["issuedlicense"];
  $user->phone = $_POST["phone"];
  $user->phone2 = $_POST["phone2"];
  $user->passport = $_POST["passport"];
  $user->nationality = $_POST["nationality"];
  $user->address = $_POST["address"];
  $user->address2 = $_POST["address2"];
  $user->user_id = $_SESSION["user_id"];
  $user->invoice_date = $_POST["invoice_date"];
  $user->passport_date = $_POST["passport_date"];
  $user->license_date = $_POST["license_date"];
  $user->home_date = $_POST["home_date"];
  
// Archivos a subir con sus atributos en el modelo
$imagenes = [
    "invoice_file"  => "invoice_file",
    "passport_file" => "passport_file",
    "license_file"  => "license_file",
    "home_file"     => "home_file"
];

$allowTypes = ['jpg', 'jpeg', 'png', 'gif'];
$uploadPath = "CF-SYSTEMS/storage/invoice_files/";

$prefixes = [
    "invoice_file"  => "cedula",
    "passport_file" => "pasaporte",
    "license_file"  => "licencia",
    "home_file"     => "residencia"
];

foreach ($imagenes as $campo => $atributo) {
    if (isset($_FILES[$campo]) && !empty($_FILES[$campo]["name"])) {
        $prefix = isset($prefixes[$campo]) ? $prefixes[$campo] : "archivo";
        $fileName = $prefix . '_' . time() . '.' . pathinfo($_FILES[$campo]["name"], PATHINFO_EXTENSION);
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
        $imageTemp = $_FILES[$campo]["tmp_name"];
        $imageUploadPath = $uploadPath . $fileName;

        // Validar tipo de archivo
        if (in_array(strtolower($fileType), $allowTypes)) {
            // Validar tamaño máximo (2MB)
            if ($_FILES[$campo]["size"] <= 2 * 1024 * 1024) {
                $compressedImage = compressImage($imageTemp, $imageUploadPath, 75);
                if ($compressedImage) {
                    $user->$atributo = $fileName;
                } else {
                    error_log("❌ Falló la compresión de la imagen: $campo");
                }
            } else {
                error_log("⚠️ Archivo muy grande ($campo): supera los 2MB.");
            }
        } else {
            error_log("❌ Tipo de archivo no permitido para $campo: $fileType");
        }
    }
}


  $user->stock_id = StockData::getPrincipal()->id;
  $user->add();

  
  $user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Agrego el cliente " .$_POST["name"]."";
          $user->add();
          

echo "OK";
exit;
elseif(isset($_GET["opt"]) && $_GET["opt"]=="add_offline"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
session_start();
include "../../autoload.php"; // Ajusta esta ruta según tu estructura real

header("Content-Type: application/json");

// Leer JSON
$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data["name"])) {
  echo "ERROR";
  exit;
}

$location = LocationData::getById($data["location"]);
$nombreCliente = StockData::getPrincipal()->name;
$telefonoIngresado = $data["phone"] ?? "";

function generarMatricula($nombreCliente, $telefonoRaw) {
    $prefijo = generarPrefijo($nombreCliente);
    $telefono = limpiarTelefono($telefonoRaw);
    return $prefijo . $telefono;
}
function generarPrefijo($nombre) {
    $nombre_limpio = strtoupper(preg_replace('/[^A-Z]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $nombre)));
    $palabras = explode(" ", $nombre_limpio);
    $prefijo = "";
    foreach ($palabras as $p) {
        if (strlen($p) > 0 && strlen($prefijo) < 3) {
            $prefijo .= $p[0];
        }
    }
    if (strlen($prefijo) < 2) {
        $prefijo .= substr($nombre_limpio, 0, 3 - strlen($prefijo));
    }
    return strtoupper($prefijo) . "-";
}
function limpiarTelefono($telefono) {
    return preg_replace('/\D/', '', $telefono);
}

$matriculaGenerada = generarMatricula($nombreCliente, $telefonoIngresado);

// Crear cliente
$user = new PersonData();
$user->name = $data["name"];
$user->no = $data["no"] ?? "";
$user->rnc = $data["rnc"] ?? "";
$user->language = $_POST["language"];
$user->birthday = $_POST["birthday"];
$user->gender = $_POST["gender"];
$user->username = $matriculaGenerada;
$user->password = sha1(md5($matriculaGenerada));
$user->reference = $data["reference"] ?? "";
$user->location = $data["location"];
$user->longitud = $location->longitud;
$user->latitud = $location->latitud;
$user->license = $data["license"] ?? "";
$user->email = $data["email"] ?? "";
$user->expirelicense = $data["expirelicense"] ?? null;
$user->issuedlicense = $data["issuedlicense"] ?? null;
$user->phone = $data["phone"] ?? "";
$user->phone2 = $data["phone2"] ?? "";
$user->passport = $data["passport"] ?? "";
$user->nationality = $data["nationality"] ?? "";
$user->address = $data["address"] ?? "";
$user->address2 = $data["address2"] ?? "";
$user->invoice_date = $data["invoice_date"] ?? null;
$user->passport_date = $data["passport_date"] ?? null;
$user->license_date = $data["license_date"] ?? null;
$user->home_date = $data["home_date"] ?? null;
$user->stock_id = StockData::getPrincipal()->id;
$user->user_id = $_SESSION["user_id"] ?? 1; // Por si no hay sesión activa
$user->add();

// Registrar en historial
$log = new ACData();
$log->user_id = $_SESSION["user_id"] ?? 1;
$log->accion = "Agregó el cliente (offline): " . $data["name"];
$log->add();

echo "OK";
exit;

elseif(isset($_GET["opt"]) && $_GET["opt"]=="upd"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
$uploadPath = "CF-SYSTEMS/storage/invoice_files/"; 
 

  $location = LocationData::getById($_POST["location"]);
  
  $user = PersonData::getById($_POST["user_id"]);
  $user->name = $_POST["name"];
  $user->language = $_POST["language"];
  $user->rnc = $_POST["rnc"];
  $user->reference = $_POST["reference"];
  $user->no = $_POST["no"];
  $user->location = $_POST["location"];
  $user->longitud = $location->longitud;
  $user->latitud = $location->latitud;
  $user->license = $_POST["license"];
  $user->email = $_POST["email"];
  $user->phone = $_POST["phone"];
  $user->phone2 = $_POST["phone2"];
  $user->passport = $_POST["passport"];
  $user->nationality = $_POST["nationality"];
  $user->address = $_POST["address"];
  $user->user_id = $_SESSION["user_id"];
  $user->invoice_date = $_POST["invoice_date"];
  $user->passport_date = $_POST["passport_date"];
  $user->license_date = $_POST["license_date"];
  $user->home_date = $_POST["home_date"];
  $user->stock_id = StockData::getPrincipal()->id;
 
// Si el fichero se ha enviado
$status = $statusMsg = ''; 
if(isset($_FILES["invoice_file"])){ 
    $status = 'error'; 
    if(!empty($_FILES["invoice_file"]["name"])) { 
        // File info 
        $fileName = basename($_FILES["invoice_file"]["name"]); 
        $imageUploadPath = $uploadPath . $fileName; 
        $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION); 
         
        // Permitimos solo unas extensiones
        $allowTypes = array('jpg','png','jpeg','gif'); 
        if(in_array($fileType, $allowTypes)){ 
            // Image temp source 
            $imageTemp = $_FILES["invoice_file"]["tmp_name"]; 
             
            // Comprimos el fichero
            $compressedImage = compressImage($imageTemp, $imageUploadPath, 75); 
             
            if($compressedImage){ 
                $status = 'success'; 
                $statusMsg = "La imagen se ha subido satisfactoriamente."; 
  $user->invoice_file = $fileName;
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


$status2 = $statusMsg2 = ''; 
if(isset($_FILES["passport_file"])){ 
    $status2 = 'error'; 
    if(!empty($_FILES["passport_file"]["name"])) { 
        // File info 
        $fileName2 = basename($_FILES["passport_file"]["name"]); 
        $imageUploadPath2 = $uploadPath . $fileName2; 
        $fileType2 = pathinfo($imageUploadPath2, PATHINFO_EXTENSION); 
         
        // Permitimos solo unas extensiones
        $allowTypes2 = array('jpg','png','jpeg','gif'); 
        if(in_array($fileType2, $allowTypes2)){ 
            // Image temp source 
            $imageTemp2 = $_FILES["passport_file"]["tmp_name"]; 
             
            // Comprimos el fichero
            $compressedImage2 = compressImage($imageTemp2, $imageUploadPath2, 75); 
             
            if($compressedImage2){ 
                $status2 = 'success'; 
                $statusMsg2 = "La imagen se ha subido satisfactoriamente."; 
  $user->passport_file = $fileName2;
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

$status3 = $statusMsg3 = ''; 
if(isset($_FILES["license_file"])){ 
    $status3 = 'error'; 
    if(!empty($_FILES["license_file"]["name"])) { 
        // File info 
        $fileName3 = basename($_FILES["license_file"]["name"]); 
        $imageUploadPath3 = $uploadPath . $fileName3; 
        $fileType3 = pathinfo($imageUploadPath3, PATHINFO_EXTENSION); 
         
        // Permitimos solo unas extensiones
        $allowTypes3 = array('jpg','png','jpeg','gif'); 
        if(in_array($fileType3, $allowTypes3)){ 
            // Image temp source 
            $imageTemp3 = $_FILES["license_file"]["tmp_name"]; 
             
            // Comprimos el fichero
            $compressedImage3 = compressImage($imageTemp3, $imageUploadPath3, 75); 
             
            if($compressedImage3){ 
                $status3 = 'success'; 
                $statusMsg3 = "La imagen se ha subido satisfactoriamente."; 
  $user->license_file = $fileName3;
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

$status4 = $statusMsg4 = ''; 
if(isset($_FILES["home_file"])){ 
    $status4 = 'error'; 
    if(!empty($_FILES["home_file"]["name"])) { 
        // File info 
        $fileName4 = basename($_FILES["home_file"]["name"]); 
        $imageUploadPath4 = $uploadPath . $fileName4; 
        $fileType4 = pathinfo($imageUploadPath4, PATHINFO_EXTENSION); 
         
        // Permitimos solo unas extensiones
        $allowTypes4 = array('jpg','png','jpeg','gif'); 
        if(in_array($fileType4, $allowTypes4)){ 
            // Image temp source 
            $imageTemp4 = $_FILES["home_file"]["tmp_name"]; 
             
            // Comprimos el fichero
            $compressedImage4 = compressImage($imageTemp4, $imageUploadPath4, 75); 
             
            if($compressedImage4){ 
                $status4 = 'success'; 
                $statusMsg4 = "La imagen se ha subido satisfactoriamente."; 
  $user->home_file = $fileName4;
            }else{ 
                $statusMsg4 = "La compresion de la imagen ha fallado"; 
            } 
        }else{ 
            $statusMsg4 = 'Lo sentimos, solo se permiten imágenes con estas extensiones: JPG, JPEG, PNG, & GIF.'; 
        } 
    }else{ 
        $statusMsg4 = 'Por favor, selecciona una imagen.'; 
    } 
}  
  $user->update();
  
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el cliente " .$_POST["name"]."";
          $user->add();


header('location:./?view=person&opt=all');

elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
$client = PersonData::getById($_GET["id"]);
$client->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el cliente " .$_POST["name"]."";
          $user->add();
header('location:./?view=person&opt=all');

elseif(isset($_GET["opt"]) && $_GET["opt"]=="addproviders"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$user = $_POST["name"];
$base = new Database();
$con = $base->connect();
$sql = "select sup_name from supplier where sup_name=\"".$user."\"";
//print $sql;
$query = $con->query($sql);
$found = false;
while($r = $query->fetch_array()){
  $found = true ;
}

if($found==false):
  $user = new PersonData();
  $user->code_name = $_POST["no"];
  $user->sup_name = $_POST["name"];
  $user->type_id = $_POST["is_type"];
  $user->gtin = $_POST["is_id"];
  $user->sup_address = $_POST["address1"];
  $user->sup_email = $_POST["email1"];
  $user->sup_mobile = $_POST["phone1"];
  $user->add_provider();
  
  $user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Agrego el proveedor " .$_POST["name"]."";
          $user->add();
          
echo 'true';
endif; elseif(isset($_GET["opt"]) && $_GET["opt"]=="updproviders"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $user = PersonData::getById($_POST["user_id"]);
  $user->no = $_POST["no"];
  $user->name = $_POST["name"];
  $user->is_type = $_POST["is_type"];
  $user->is_id = $_POST["is_id"];
  $user->address1 = $_POST["address1"];
  $user->email1 = $_POST["email1"];
  $user->phone1 = $_POST["phone1"];
  $user->update_provider();

  $user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Modifico el proveedor " .$_POST["name"]."";
          $user->add();

echo 'true';
elseif(isset($_GET["opt"]) && $_GET["opt"]=="delproviders"):
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
   $client = PersonData::getBySupId($_GET["id"]);
   $client->delSup();

   $user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino el proveedor " .$_POST["name"]."";
          $user->add();
header('location:./?view=person&opt=providers');

endif;?>