<?php if(isset($_GET["opt"]) && $_GET["opt"] == "add"):
$plate = $_POST['plate'];
$base = new Database();
$con = $base->connect();
$sql = "SELECT name FROM cars WHERE plate = \"$plate\"";
$query = $con->query($sql);

$found = false;
while ($r = $query->fetch_array()) {
    $found = true;
}

if (!$found) {

    // Verificar y agregar insurance_id si no existe
    if (!empty($_POST["insurance_id"])) {
        $insuranceName = $_POST["insurance_id"];
        $sql = "SELECT name FROM insurance WHERE name=\"$insuranceName\"";
        $query = $con->query($sql);
        $found_insurance = false;
        while($r = $query->fetch_array()) {
            $found_insurance = true;
        }

        if(!$found_insurance) {
            $newInsurance = new InsuranceData();
            $newInsurance->name = $insuranceName;
            $newInsurance->add();
        }
    }

    // Verificar y agregar insurance2_id si no existe
    if (!empty($_POST["insurance2_id"])) {
        $insuranceName2 = $_POST["insurance2_id"];
        $sql = "SELECT name FROM insurance WHERE name=\"$insuranceName2\"";
        $query = $con->query($sql);
        $found_insurance2 = false;
        while($r = $query->fetch_array()) {
            $found_insurance2 = true;
        }

        if(!$found_insurance2) {
            $newInsurance2 = new InsuranceData();
            $newInsurance2->name = $insuranceName2;
            $newInsurance2->add();
        }
    }

   // Función para comprimir imágenes manteniendo transparencia en PNG/GIF
function compressImage($source, $destination, $quality) { 
    $imgInfo = getimagesize($source); 
    $mime = $imgInfo['mime']; 

    switch($mime){ 
        case 'image/jpeg': 
            $image = imagecreatefromjpeg($source); 
            // Guardar en JPEG
            imagejpeg($image, $destination, $quality); 
            break; 

        case 'image/png': 
            $image = imagecreatefrompng($source); 
            // Mantener transparencia
            imagealphablending($image, false);
            imagesavealpha($image, true);
            // Guardar como PNG (0 = sin compresión, 9 = máxima)
            $png_compression = 9; 
            imagepng($image, $destination, $png_compression); 
            break; 

        case 'image/gif': 
            $image = imagecreatefromgif($source); 
            // Mantener transparencia si la hay
            $transparencyIndex = imagecolortransparent($image);
            if ($transparencyIndex >= 0) {
                $transparentColor = imagecolorsforindex($image, $transparencyIndex);
                $newImage = imagecreatetruecolor(imagesx($image), imagesy($image));
                $transparency = imagecolorallocate($newImage, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);
                imagefill($newImage, 0, 0, $transparency);
                imagecolortransparent($newImage, $transparency);
                imagecopy($newImage, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                $image = $newImage;
            }
            // Guardar como GIF
            imagegif($image, $destination); 
            break; 

        default: 
            $image = imagecreatefromjpeg($source); 
            imagejpeg($image, $destination, $quality); 
    } 

    return $destination; 
}


    $uploadPath = "CF-SYSTEMS/storage/invoice_files/"; 

    $car = new CarsData();
    $car->stock_id = StockData::getPrincipal()->id;
    $car->provider_id = $_POST["provider_id"];
    $car->provider_price = ($_POST["provider_id"] <> StockData::getPrincipal()->id) ? $_POST["provider_price"] : 0;
    $car->name = $_POST["name"];
    $car->charge_kms = $_POST["charge_kms"];
    $car->token = $_POST["token"];
    $car->kms_current = $_POST["kms_current"];
    $car->year = $_POST["year"];
    $car->brand_id = $_POST["brand_id"];
    $car->category_id = $_POST["category_id"];
    $car->insurance_id = $_POST["insurance_id"];
    $car->insurance2_id = $_POST["insurance2_id"];
    $car->interior_id = $_POST["interior_id"];
    $car->exterior_id = $_POST["exterior_id"];
    $car->chassis = $_POST["chassis"];
    $car->tuition = $_POST["tuition"];
    $car->plate = $_POST["plate"];
    $car->user_id = $_SESSION["user_id"];
    $car->date_insurance = $_POST["date_insurance"];
    $car->date2_insurance = $_POST["date2_insurance"];
    $car->price = $_POST["price"];
    $car->no_batery = $_POST["no_batery"];
    $car->seat = $_POST["seat"];
    $car->transmission_id = $_POST["transmission_id"];
    $car->fuel_id = $_POST["fuel_id"];

    // Subida de imagen invoice
    if(isset($_FILES["image"]) && !empty($_FILES["image"]["name"])) {
        $fileName = uniqid() . '_' . basename($_FILES["image"]["name"]); 
        $imageUploadPath = $uploadPath . $fileName;
        $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg','png','jpeg','gif');
        if(in_array($fileType, $allowTypes)) {
            $imageTemp = $_FILES["image"]["tmp_name"];
            if(compressImage($imageTemp, $imageUploadPath, 75)) {
                $car->invoice_file = $fileName;
            }
        }
    }

    // Subida de imagen insurance
    if(isset($_FILES["insurance_file"]) && !empty($_FILES["insurance_file"]["name"])) {
        $fileName2 = uniqid() . '_' . basename($_FILES["insurance_file"]["name"]); 
        $imageUploadPath2 = $uploadPath . $fileName2;
        $fileType2 = pathinfo($imageUploadPath2, PATHINFO_EXTENSION);
        $allowTypes2 = array('jpg','png','jpeg','gif');
        if(in_array($fileType2, $allowTypes2)) {
            $imageTemp2 = $_FILES["insurance_file"]["tmp_name"];
            if(compressImage($imageTemp2, $imageUploadPath2, 75)) {
                $car->insurance_file = $fileName2;
            }
        }
    }

    // Subida de imagen insurance2
    if(isset($_FILES["insurance2_file"]) && !empty($_FILES["insurance2_file"]["name"])) {
        $fileName3 = uniqid() . '_' . basename($_FILES["insurance2_file"]["name"]); 
        $imageUploadPath3 = $uploadPath . $fileName3;
        $fileType3 = pathinfo($imageUploadPath3, PATHINFO_EXTENSION);
        $allowTypes3 = array('jpg','png','jpeg','gif');
        if(in_array($fileType3, $allowTypes3)) {
            $imageTemp3 = $_FILES["insurance2_file"]["tmp_name"];
            if(compressImage($imageTemp3, $imageUploadPath3, 75)) {
                $car->insurance2_file = $fileName3;
            }
        }
    }

    // Guardar el carro
    $car->add();

    // Registrar acción
    $log = new ACData();
    $log->accion = "Hizo el vehiculo " . $_POST["name"];
    $log->add();

    echo "OK";
    exit;
}

echo "NOT";
exit;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
elseif(isset($_GET["opt"]) && $_GET["opt"] == "upd"):

$id = $_POST['user_id'];
$car = CarsData::getById($id);

if ($car == null) {
    echo "NOT";
    exit;
}

// Actualizar campos
$car->provider_id = $_POST["provider_id"];
$car->provider_price = ($_POST["provider_id"] != StockData::getPrincipal()->id) ? $_POST["provider_price"] : 0;
$car->name = $_POST["name"];
$car->charge_kms = $_POST["charge_kms"];
$car->token = $_POST["token"];
$car->kms_current = $_POST["kms_current"];
$car->year = $_POST["year"];
$car->brand_id = $_POST["brand_id"];
$car->category_id = $_POST["category_id"];
$car->insurance_id = $_POST["insurance_id"];
$car->insurance2_id = $_POST["insurance2_id"];
$car->interior_id = $_POST["interior_id"];
$car->exterior_id = $_POST["exterior_id"];
$car->chassis = $_POST["chassis"];
$car->tuition = $_POST["tuition"];
$car->plate = $_POST["plate"];
$car->user_id = $_SESSION["user_id"];
$car->date_insurance = $_POST["date_insurance"];
$car->date2_insurance = $_POST["date2_insurance"];
$car->price = $_POST["price"];
$car->no_batery = $_POST["no_batery"];
$car->seat = $_POST["seat"];
$car->transmission_id = $_POST["transmission_id"];
$car->fuel_id = $_POST["fuel_id"];

// Ruta de subida
$uploadPath = "CF-SYSTEMS/storage/invoice_files/";

// Función para comprimir imágenes manteniendo transparencia en PNG/GIF
function compressImage($source, $destination, $quality) { 
    $imgInfo = getimagesize($source); 
    $mime = $imgInfo['mime']; 

    switch($mime){ 
        case 'image/jpeg': 
            $image = imagecreatefromjpeg($source); 
            // Guardar en JPEG
            imagejpeg($image, $destination, $quality); 
            break; 

        case 'image/png': 
            $image = imagecreatefrompng($source); 
            // Mantener transparencia
            imagealphablending($image, false);
            imagesavealpha($image, true);
            // Guardar como PNG (0 = sin compresión, 9 = máxima)
            $png_compression = 9; 
            imagepng($image, $destination, $png_compression); 
            break; 

        case 'image/gif': 
            $image = imagecreatefromgif($source); 
            // Mantener transparencia si la hay
            $transparencyIndex = imagecolortransparent($image);
            if ($transparencyIndex >= 0) {
                $transparentColor = imagecolorsforindex($image, $transparencyIndex);
                $newImage = imagecreatetruecolor(imagesx($image), imagesy($image));
                $transparency = imagecolorallocate($newImage, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);
                imagefill($newImage, 0, 0, $transparency);
                imagecolortransparent($newImage, $transparency);
                imagecopy($newImage, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                $image = $newImage;
            }
            // Guardar como GIF
            imagegif($image, $destination); 
            break; 

        default: 
            $image = imagecreatefromjpeg($source); 
            imagejpeg($image, $destination, $quality); 
    } 

    return $destination; 
}


// Subir archivos si se envían
if(isset($_FILES["image"]) && !empty($_FILES["image"]["name"])) {
    $fileName = uniqid() . '_' . basename($_FILES["image"]["name"]);
    $imageUploadPath = $uploadPath . $fileName;
    $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION);
    $allowTypes = array('jpg','png','jpeg','gif');
    if(in_array($fileType, $allowTypes)) {
        $imageTemp = $_FILES["image"]["tmp_name"];
        if(compressImage($imageTemp, $imageUploadPath, 75)) {
            $car->invoice_file = $fileName;
        }
    }
}

if(isset($_FILES["insurance_file"]) && !empty($_FILES["insurance_file"]["name"])) {
    $fileName2 = uniqid() . '_' . basename($_FILES["insurance_file"]["name"]);
    $imageUploadPath2 = $uploadPath . $fileName2;
    $fileType2 = pathinfo($imageUploadPath2, PATHINFO_EXTENSION);
    $allowTypes2 = array('jpg','png','jpeg','gif');
    if(in_array($fileType2, $allowTypes2)) {
        $imageTemp2 = $_FILES["insurance_file"]["tmp_name"];
        if(compressImage($imageTemp2, $imageUploadPath2, 75)) {
            $car->insurance_file = $fileName2;
        }
    }
}

if(isset($_FILES["insurance2_file"]) && !empty($_FILES["insurance2_file"]["name"])) {
    $fileName3 = uniqid() . '_' . basename($_FILES["insurance2_file"]["name"]);
    $imageUploadPath3 = $uploadPath . $fileName3;
    $fileType3 = pathinfo($imageUploadPath3, PATHINFO_EXTENSION);
    $allowTypes3 = array('jpg','png','jpeg','gif');
    if(in_array($fileType3, $allowTypes3)) {
        $imageTemp3 = $_FILES["insurance2_file"]["tmp_name"];
        if(compressImage($imageTemp3, $imageUploadPath3, 75)) {
            $car->insurance2_file = $fileName3;
        }
    }
}

// Guardar actualización
$car->update();

// Registrar acción
$log = new ACData();
$log->accion = "Actualizó el vehículo " . $_POST["name"];
$log->add();

echo "OK";
exit;


elseif(isset($_GET["opt"]) && $_GET["opt"] == "add_offline"):

session_start();
include "../../autoload.php";

header("Content-Type: application/json");

// Leer JSON
$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data["plate"])) {
  echo json_encode("ERROR");
  exit;
}

$plate = $data["plate"];
$base = new Database();
$con = $base->connect();
$sql = "SELECT name FROM cars WHERE plate = \"$plate\"";
$query = $con->query($sql);

$found = false;
while ($r = $query->fetch_array()) {
    $found = true;
}

if (!$found) {

    // Verificar y agregar insurance_id si no existe
    if (!empty($data["insurance_id"])) {
        $insurance1 = $data["insurance_id"];
        $sql = "SELECT name FROM insurance WHERE name=\"$insurance1\"";
        $query = $con->query($sql);
        $found_insurance1 = false;
        while($r = $query->fetch_array()) {
            $found_insurance1 = true;
        }

        if (!$found_insurance1) {
            $ins1 = new InsuranceData();
            $ins1->name = $insurance1;
            $ins1->add();
        }
    }

    // Verificar y agregar insurance2_id si no existe
    if (!empty($data["insurance2_id"])) {
        $insurance2 = $data["insurance2_id"];
        $sql = "SELECT name FROM insurance WHERE name=\"$insurance2\"";
        $query = $con->query($sql);
        $found_insurance2 = false;
        while($r = $query->fetch_array()) {
            $found_insurance2 = true;
        }

        if (!$found_insurance2) {
            $ins2 = new InsuranceData();
            $ins2->name = $insurance2;
            $ins2->add();
        }
    }

    $car = new CarsData();
    $car->stock_id = StockData::getPrincipal()->id;
    $car->provider_id = $data["provider_id"];
    $car->provider_price = ($data["provider_id"] != StockData::getPrincipal()->id) ? $data["provider_price"] : 0;
    $car->name = $data["name"];
    $car->charge_kms = $data["charge_kms"];
    $car->token = $data["token"];
    $car->kms_current = $data["kms_current"];
    $car->year = $data["year"];
    $car->brand_id = $data["brand_id"];
    $car->category_id = $data["category_id"];
    $car->insurance_id = $data["insurance_id"];
    $car->insurance2_id = $data["insurance2_id"];
    $car->interior_id = $data["interior_id"];
    $car->exterior_id = $data["exterior_id"];
    $car->chassis = $data["chassis"];
    $car->tuition = $data["tuition"];
    $car->plate = $data["plate"];
    $car->user_id = $_SESSION["user_id"];
    $car->date_insurance = $data["date_insurance"];
    $car->date2_insurance = $data["date2_insurance"];
    $car->price = $data["price"];
    $car->no_batery = $data["no_batery"];
    $car->seat = $data["seat"];
    $car->transmission_id = $data["transmission_id"];
    $car->fuel_id = $data["fuel_id"];
    $car->add();

    $log = new ACData();
    $log->accion = "Hizo el vehiculo " . $data["name"];
    $log->add();

    echo json_encode("OK");
    exit;
}

echo json_encode("NOT");
exit;


elseif(isset($_GET["opt"]) && $_GET["opt"] == "upd_offline"):

session_start();
include "../../autoload.php";

header("Content-Type: application/json");

// Leer JSON
$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data["user_id"]) || !isset($data["plate"])) {
  echo json_encode("ERROR");
  exit;
}

$car = CarsData::getById($data["user_id"]);
if (!$car) {
  echo json_encode("NOT_FOUND");
  exit;
}

$con = (new Database())->connect();

// Validar si la placa ya existe en otro vehículo
$plate = $data["plate"];
$sql = "SELECT id FROM cars WHERE plate = \"$plate\" AND id != {$data["user_id"]} LIMIT 1";
$query = $con->query($sql);
if ($query && $query->num_rows > 0) {
  echo json_encode("DUPLICATE_PLATE");
  exit;
}

// Verificar y agregar insurance_id si no existe
if (!empty($data["insurance_id"])) {
    $insurance1 = $data["insurance_id"];
    $sql = "SELECT name FROM insurance WHERE name=\"$insurance1\"";
    $query = $con->query($sql);
    $found_ins1 = false;
    while($r = $query->fetch_array()) { $found_ins1 = true; }
    if (!$found_ins1) {
        $ins1 = new InsuranceData();
        $ins1->name = $insurance1;
        $ins1->add();
    }
}

// Verificar y agregar insurance2_id si no existe
if (!empty($data["insurance2_id"])) {
    $insurance2 = $data["insurance2_id"];
    $sql = "SELECT name FROM insurance WHERE name=\"$insurance2\"";
    $query = $con->query($sql);
    $found_ins2 = false;
    while($r = $query->fetch_array()) { $found_ins2 = true; }
    if (!$found_ins2) {
        $ins2 = new InsuranceData();
        $ins2->name = $insurance2;
        $ins2->add();
    }
}

// Actualizar los campos
$car->provider_id = $data["provider_id"];
$car->provider_price = ($data["provider_id"] != StockData::getPrincipal()->id) ? $data["provider_price"] : 0;
$car->name = $data["name"];
$car->charge_kms = $data["charge_kms"];
$car->token = $data["token"];
$car->kms_current = $data["kms_current"];
$car->year = $data["year"];
$car->brand_id = $data["brand_id"];
$car->category_id = $data["category_id"];
$car->insurance_id = $data["insurance_id"];
$car->insurance2_id = $data["insurance2_id"];
$car->interior_id = $data["interior_id"];
$car->exterior_id = $data["exterior_id"];
$car->chassis = $data["chassis"];
$car->tuition = $data["tuition"];
$car->plate = $data["plate"];
$car->user_id = $_SESSION["user_id"];
$car->date_insurance = $data["date_insurance"];
$car->date2_insurance = $data["date2_insurance"];
$car->price = $data["price"];
$car->no_batery = $data["no_batery"];
$car->seat = $data["seat"];
$car->transmission_id = $data["transmission_id"];
$car->fuel_id = $data["fuel_id"];

// Guardar actualización
$car->update();

// Registrar acción
$log = new ACData();
$log->accion = "Actualizó el vehículo " . $data["name"];
$log->add();

echo json_encode("OK");
exit;


elseif(isset($_GET["opt"]) && $_GET["opt"]=="status"):
$category = CarsData::getById($_GET["id"]);
$category->status = $_GET["status"];
$category->update_status();
header('location:./?view=cars&opt=all');

elseif(isset($_GET["opt"]) && $_GET["opt"]=="stock"):
$category = CarsData::getById($_POST["car_id"]);
$category->stock_id=$_POST["stock_id"];
$category->update_stock();
echo 'true';

elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"):
$category = CarsData::getById($_GET["id"]);
$bokg = BookingData::getAllBySQL("where car_id=".$_GET["id"]);
foreach($bokg as $bk){
$bk->del(); 
}
$category->del();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino la vehiculo " .$_POST["name"]."";
          $user->add();
header('location:./?view=cars&opt=all');

elseif(isset($_GET["opt"]) && $_GET["opt"]=="delgalery"):
$category = GaleryData::getById($_GET["id"]);
$category->del();
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino la vehiculo " .$_POST["name"]."";
          $user->add();
header('location:./?view=cars&opt=view&id='.$_GET["cars"]);

elseif(isset($_GET["opt"]) && $_GET["opt"]=="galery"):
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
  $user = new GaleryData();
  $user->car_id = $_POST["car_id"];
  $user->user_id = $_SESSION["user_id"];
  $user->invoice_file = $fileName;
  $user->add();
header('location:./?view=cars&opt=galery');

endif;

?>