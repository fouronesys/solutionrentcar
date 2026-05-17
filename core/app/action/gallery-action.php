<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){
    

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
 

$user = new GaleryData();
    
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

$user->car_id = $_POST["car_id"];
$user->user_id = $_SESSION["user_id"];
$user->add();

header('location:./?view=gallery&opt=all&id='.$_POST["car_id"]);


	
}

else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){
$category = GaleryData::getById($_GET["id"]);
$category->del();
header('location:./?view=gallery&opt=all&id='.$_GET["id"]);
}


?>