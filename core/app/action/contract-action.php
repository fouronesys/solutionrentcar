<?php 


// ======================================================
// 🔧 FUNCIÓN GLOBAL compressImage() (única y segura)
// ======================================================
if (!function_exists('compressImage')) {
    function compressImage($source, $destination, $quality = 75) {
        if (!file_exists($source)) return false;
        
        $info = @getimagesize($source);
        if (!$info) return false;
        
        $mime = $info['mime'] ?? 'image/jpeg';
        switch ($mime) {
            case 'image/jpeg': $image = imagecreatefromjpeg($source); break;
            case 'image/png':  $image = imagecreatefrompng($source); break;
            case 'image/gif':  $image = imagecreatefromgif($source); break;
            default: return false;
        }

        imagejpeg($image, $destination, $quality);
        imagedestroy($image);
        return $destination;
    }
}



if(isset($_GET["opt"]) && $_GET["opt"]=="add"){
//////////////////////////////////////////////////////////////////////////  VEHICULO EXTERNO ///////////////////////////////////////////////////////////////////////////
if ($_POST["method"]==3):

if ($_POST["nuevo_cliente_activo"] > 0):



$uploadPath = "CF-SYSTEMS/storage/invoice_files/"; 
$location = StatesData::getById($_POST["location"]);

/* ================================
 * FUNCIONES AUXILIARES
 * ================================ */
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
        if (strlen($p) > 0 && strlen($prefijo) < 3) $prefijo .= $p[0];
    }
    if (strlen($prefijo) < 2) $prefijo .= substr($nombre_limpio, 0, 3 - strlen($prefijo));
    return strtoupper($prefijo) . "-";
}
function limpiarTelefono($telefono) {
    return preg_replace('/\D/', '', $telefono);
}

/* ================================
 * DATOS CLIENTE
 * ================================ */
$nombreCliente = StockData::getPrincipal()->name;
$telefonoIngresado = $_POST["phone"];
$matriculaGenerada = generarMatricula($nombreCliente, $telefonoIngresado);

$user = new PersonData();
$user->name = $_POST["name"];
$user->no = $_POST["no"];
$user->rnc = $_POST["rnc"];
$user->language = $_POST["language"];
$user->birthday = $_POST["birthday"];
$user->gender = $_POST["gender"];
$user->username = $matriculaGenerada;
$user->password = sha1(md5($matriculaGenerada));
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

/* ================================
 * SUBIDA INDEPENDIENTE DE ARCHIVOS
 * ================================ */
$imagenes = [
    "invoice_file"  => "cedula",
    "passport_file" => "pasaporte",
    "license_file"  => "licencia",
    "home_file"     => "residencia"
];

$allowTypes = ['jpg', 'jpeg', 'png', 'gif'];

foreach ($imagenes as $campo => $nombreCampo) {
    if (isset($_FILES[$campo]) && !empty($_FILES[$campo]["name"])) {
        $fileName = $nombreCampo . '_' . time() . '.' . pathinfo($_FILES[$campo]["name"], PATHINFO_EXTENSION);
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $imageTemp = $_FILES[$campo]["tmp_name"];
        $imageUploadPath = $uploadPath . $fileName;

        if (in_array($fileType, $allowTypes)) {
            if ($_FILES[$campo]["size"] <= 2 * 1024 * 1024) {
                $compressedImage = compressImage($imageTemp, $imageUploadPath, 75);
                if ($compressedImage) {
                    $user->$campo = $fileName;
                } else {
                    error_log("⚠️ No se pudo comprimir la imagen de $nombreCampo");
                }
            } else {
                error_log("⚠️ Imagen de $nombreCampo excede los 2MB");
            }
        } else {
            error_log("❌ Tipo de archivo no permitido para $nombreCampo: $fileType");
        }
    } else {
        // No se subió este archivo → no da error
        $user->$campo = null;
        error_log("ℹ️ No se subió archivo para $nombreCampo");
    }
}

/* ================================
 * GUARDAR CLIENTE
 * ================================ */
$user->stock_id = StockData::getPrincipal()->id;
$user->add();

$id_persons = PersonData::getAllByID();
$id_person = !empty($id_persons[0]->id) ? $id_persons[0]->id : 0;

else:
    $id_person = $_POST["person_id"];
endif;

// =====================================================
// 🏢 SUPLIDOR: verificar si ya existe o crearlo
// =====================================================
if (isset($_POST["stock_id2"]) && !empty($_POST["stock_id2"])) {
    $nombreSuplidor = trim($_POST["stock_id2"]);

    // Buscar suplidor por nombre
    $supplierExistente = StockData::getAllBySQL("WHERE name = '$nombreSuplidor'");

    if ($supplierExistente) {
        // ✅ Existe → usamos su ID
        $supplier_id = $supplierExistente->id;
    } else {
        // 🚀 No existe → lo creamos
        $supplier = new StockData();
        $supplier->name    = $nombreSuplidor;
        $supplier->location = StockData::getPrincipal()->location;
        $supplier->imp_val = StockData::getPrincipal()->imp_val;
        $supplier->add_ext();

        // Obtener ID del suplidor recién creado
        $id_supplier = StockData::getAllByID();
        $supplier_id = $id_supplier[0]->id!=null?$id_supplier[0]->id:0;
    }
}

// =====================================================
// 🚘 VEHÍCULO: verificar si ya existe o crearlo
// =====================================================
if (isset($_POST["stock_id2"]) && !empty($_POST["stock_id2"])):
    $placa  = trim($_POST["cars2_plate"]);
    $chasis = trim($_POST["cars2_chassis"]);
    $provider_price = ($_POST["rpayment"]);
    
$base = new Database();
$con = $base->connect();
$sql = "select SQL_BIG_RESULT * from cars where plate= \"".$placa."\" and chassis= \"".$chasis."\"";
//print $sql;
$query = $con->query($sql);
$found = false;  
while($carExistente = $query->fetch_array()){
$car_id = $carExistente['id'];
$found = true;  
}

    if ($found == false):
        // 🚀 No existe → lo creamos
        $car = new CarsData();
        $car->provider_id = $supplier_id;  // 👈 Relación con suplidor
        $car->provider_price = $provider_price;  // 👈 Relación con suplidor
        $car->price       = intval(str_replace(",", "", $_POST["rpayment"]));
        $car->brand_id    = $_POST["cars2_brand"];
        $car->name        = $_POST["cars2_name"];
        $car->category_id = $_POST["cars2_category"];
        $car->year        = $_POST["cars2_year"];
        $car->plate       = $placa;
        $car->chassis     = $chasis;
        $car->status      = 1;
        $car->stock_id    = StockData::getPrincipal()->id;
        $car->user_id     = $_SESSION["user_id"];
        $car->add_ext();

        // Obtener ID del carro recién creado
        $id_car = CarsData::getAllByID();
        $car_id = $id_car[0]->id!=null?$id_car[0]->id:0;
    endif;


endif;


    // aqui sigue igual que los demas metodo

    if (!empty($car_id)) {

    $user = new BookingData();
    $user->start_at    = $_POST["start_at"];
    $user->payment_day = $_POST["payment_day"];
    $user->type_id     = $_POST["type_id"];

    if ($_POST["type_id"] == 1) {
        $user->end_at = $_POST["end_at"];
    } else {
        $user->end_at = $_POST["selectdate"];
    }

    $user->place_start = ($_POST["place_start2"] > 0) ? $_POST["place_start2"] : $_POST["place_start"];
    $user->place_end   = ($_POST["place_end2"] > 0) ? $_POST["place_end2"] : $_POST["place_end"];

    $user->person_id  = $id_person;
    $user->person2_id = ($_POST["person2_id"] > 0) ? $_POST["person2_id"] : 0;

    $user->location  = $_POST["location"];
    $user->stock_id  = StockData::getPrincipal()->id;
    $user->type_sure = $_POST["type_sure"];
    $user->sure      = $_POST["sure"];
    $user->fuel      = $_POST["fuel"];
    $user->car_id    = $car_id;
    $user->car2_id   = ($_POST["car2_id"] > 0) ? $_POST["car2_id"] : 0;
    $user->type      = 1;

    // =============================
    // ✅ LIMPIAR VALORES MONETARIOS
    // =============================
    $price2 = floatval(str_replace(",", "", $_POST["price2"] ?? 0));
    $xtotal = floatval(str_replace(",", "", $_POST["xtotal"] ?? 0));
    $plane  = floatval(str_replace(",", "", $_POST["plane"] ?? 0));
    $card   = floatval(str_replace(",", "", $_POST["card"] ?? 0));
    $iva    = floatval(str_replace(",", "", $_POST["iva"] ?? 0));

    // Guardar precio base
    $user->price = $price2;

    // =============================
    // ✅ SI PAGA CON TARJETA (f_id = 3)
    // =============================
    if ($_POST["f_id"] == 3) {
        $user->card = $card * (StockData::getPrincipal()->card / 100);
    } else {
        $user->card = 0;
    }

    // =============================
    // ✅ TOTAL CORRECTO
    // =============================
    $user->total = ($price2*$_POST["day"]) + $xtotal + $plane + $card + $iva;

    $user->payment = floatval(str_replace(",", "", $_POST["payment"]));
    $user->day     = $_POST["day"];
    $user->deposit = 0;
    $user->f_id    = $_POST["f_id"];
    $user->xtotal  = $_POST["xtotal"];

    $user->unit_extra1  = $_POST["unit_extra1"];
    $user->price_extra1 = $_POST["price_extra1"];
    $user->unit_extra2  = $_POST["unit_extra2"];
    $user->price_extra2 = $_POST["price_extra2"];
    $user->unit_extra3  = $_POST["unit_extra3"];
    $user->price_extra3 = $_POST["price_extra3"];

    // ✅ CORREGIDO (antes copiabas extra3)
    $user->unit_extra4  = $_POST["unit_extra4"];
    $user->price_extra4 = $_POST["price_extra4"];

    // USD y tasa
    $user->usd_price  = $_POST["usd_price"];
    $user->tasa_dolar = $_POST["tasa_dolar"];

    // IVA - comprobante
    $receiptIdAndName   = explode("-", $_POST["type_iva"]);
    $user->iva          = $_POST["iva"];
    $user->value_iva    = $_POST["value_iva"];
    $user->type_iva     = $receiptIdAndName[0];
    $user->number_iva   = $receiptIdAndName[1] . "" . $receiptIdAndName[2];

    $user->user_id = $_SESSION["user_id"];
    $user->plane   = $_POST["plane"];
    $user->status  = 1;

    $user->add();

    $spends = BookingData::getAllByID()[0]->id ?? 0;
    $kmx = CarsData::getById($car_id);
    $kmx->status = 2;
    $kmx->update_status();

    $payment = new PaymentData();
    $payment->sell_id = $spends;
    $payment->val = $total;
    $payment->user_id = $_SESSION["user_id"];
    $payment->stock_id = StockData::getPrincipal()->id;
    $payment->person_id = $id_person;
    $payment->is_stock = 0;
    $payment->add();
    
    $payment2 = new PaymentData();
    $payment2->sell_id = $spends;
    $payment2->val = ($_POST["rpayment"]*$_POST["day"]);
    $payment2->user_id = $_SESSION["user_id"];
    $payment2->stock_id = StockData::getPrincipal()->id;
    $payment2->person_id = $supplier_id;
    $payment2->is_stock = 1;
    $payment2->add();

    if ($_POST["payment"] > 0) {
        $payment3 = new PaymentData();
        $payment3->sell_id = $spends;
        $payment3->val = -1 * $_POST["payment"];
        $payment3->user_id = $_SESSION["user_id"];
        $payment3->stock_id = StockData::getPrincipal()->id;
        $payment3->person_id = $id_person;
        $payment3->is_stock = 0;
        $payment3->add_payment();
    }
    
   
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carpeta donde se guardarán las imágenes
$uploadDir = "danger/";

// Crear la carpeta si no existe
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Lista de nombres de los inputs
$inputNames = [
    "image1", "image2", "image3", "image4", "image5", "image6", "image7", "image8", "image9", "image10", 
    "image11", "image12", "image13", "image14", "image15", "image16", "image17", "image18", "image19", "image20", 
    "image21", "image22", "image23", "image24", "image25", "image26", "image27", "image28", "image29"
];


// Array para almacenar los nombres de los archivos
$imageNames = [];
$errors = [];

// Procesar las imágenes de forma más eficiente
foreach ($inputNames as $inputName) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES[$inputName]['tmp_name'];
        $originalName = basename($_FILES[$inputName]['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // Generar un nuevo nombre único para cada imagen
        $newFileName = time() . "_" . uniqid() . ".jpg"; // Convertimos todo a JPG
        $targetPath = $uploadDir . $newFileName;

        // Primero mover el archivo al directorio destino
        if (move_uploaded_file($fileTmp, $targetPath)) {
            // Comprimir la imagen después de moverla
            if (compressImage($targetPath, $targetPath)) {
                $imageNames[] = $newFileName;
            } else {
                $errors[] = "Error al procesar la imagen: " . $originalName;
            }
        } else {
            $errors[] = "Error al mover el archivo: " . $originalName;
        }
    } elseif (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = "Error en el archivo " . $_FILES[$inputName]['name'] . ": Código " . $_FILES[$inputName]['error'];
    }
}

// Verificar si se subió al menos una imagen
if (!empty($imageNames)) {
    $danger = implode("|", $imageNames);
}


// Lista de nombres de los inputs
$commentName = [
    "comment1", "comment2", "comment3", "comment4", "comment5", "comment6"
];

// Array para almacenar los nombres de los archivos
$commentNames = [];
foreach ($commentName as $comment) {
if (isset($_POST[$comment])){
$commentNames[] = $_POST[$comment];
}
}

// Verificar si se subió al menos una imagen
if (!empty($commentNames)) {
    $comentario = implode("|", $commentNames);
}

$img = $_POST["base64"];
$img = str_replace('data:image/png;base64,', '', $img);
$fileData = base64_decode($img);
$fileName = "firmas/".uniqid().'.png';

file_put_contents($fileName, $fileData);

    $xuser = BookingData::getById($spends);
    $xuser->firma = $fileName;
	$xuser->update_firma();
	
	$user = new DeliveryData();
	$user->firma = $fileName;
	$user->danger = $danger;
	$user->method = 2;
	$user->cat = isset($_POST["cat"])?1:0;
	$user->radio = isset($_POST["radio"])?1:0;
	$user->replacement = isset($_POST["replacement"])?1:0;
	$user->antenna = isset($_POST["antenna"])?1:0;
	$user->keyring = isset($_POST["keyring"])?1:0;
	$user->carpets = isset($_POST["carpets"])?1:0;
	$user->belts = isset($_POST["belts"])?1:0;
	$user->roof_lining = isset($_POST["roof_lining"])?1:0;
	$user->mirrors = isset($_POST["mirrors"])?1:0;
	$user->board = isset($_POST["board"])?1:0;
	$user->rearview = isset($_POST["rearview"])?1:0;
	$user->watches = isset($_POST["watches"])?1:0;
	$user->document = isset($_POST["document"])?1:0;
	$user->lighter = isset($_POST["lighter"])?1:0;
	$user->crystals = isset($_POST["crystals"])?1:0;
	$user->cd = isset($_POST["cd"])?1:0;
	$user->fuel = $_POST["fuel"];
	$user->kms = $kmx->kms;
	$user->bumper = isset($_POST["bumper"])?1:0;
	$user->equalizer = isset($_POST["equalizer"])?1:0;
	$user->cup_holder = isset($_POST["cup_holder"])?1:0;
	$user->plate = isset($_POST["plate"])?1:0;
	$user->seats = isset($_POST["seats"])?1:0;
	$user->logo = isset($_POST["logo"])?1:0;
	$user->batery = isset($_POST["batery"])?1:0;
	$user->top = isset($_POST["top"])?1:0;
	$user->comment = $comentario;
	$user->no_batery = $_POST["no_batery"];
	$user->car_id = $_POST["car_id"];
	$user->booking_id = $spends;
    $user->user_id = $xuser->user_id;
    $user->receiver_id = 0;
    $user->delivery_id = $_SESSION["user_id"];
	$user->add();
	

    $log = new ACData();
    $log->user_id = $_SESSION["user_id"];
    $log->accion = "Agrego la reserva";
    $log->add();

    header("Location: ./?view=contract&opt=modal&id=$spends");
    exit;
} else {
    header("Location: ./?view=booking&opt=earring");
    exit;
}
////////////////////////////////////////////////////////////////////////// DIFERENTE DE VEHICULO EXTERNO ///////////////////////////////////////////////////////////////////////////
elseif ($_POST["method"]<>3):

$car_id = intval($_POST["car_id"]);
$found = false;
$bokg = BookingData::getAllBySQL("WHERE car_id = $car_id");
$start_at = date("Y-m-d", strtotime($_POST["start_at"]));

foreach ($bokg as $bk):
    $start_bk = date("Y-m-d", strtotime($bk->start_at));
    $end_bk = date("Y-m-d", strtotime($bk->end_at));
    $end_at = ($_POST["type_id"] == 1) ? date("Y-m-d", strtotime($_POST["end_at"])) : date("Y-m-d", strtotime($_POST["selectdate"]));

    if ((($start_at == $start_bk) || ($end_at == $end_bk)) && $bk->car_id == $car_id) {
        $found = true;
        break;
    }
endforeach;

  
    if($_POST["nuevo_cliente_activo"]>0):
        
    

 
// Ruta subida
$uploadPath = "CF-SYSTEMS/storage/invoice_files/"; 

if(StockData::getPrincipal()->update==1):

$location = StatesData::getById($_POST["location"]);
 
else:

$location = LocationData::getById($_POST["location"]);

endif;

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

    $id_persons = PersonData::getAllByID();
    $persons = $id_persons[0]->id!=null?$id_persons[0]->id:0;
    
    $id_person = $persons;
    else:
    
    $id_person = $_POST["person_id"];
        
    endif;

    if (!$found && !empty($car_id)) {
    // aqui sigue igual que los demas metodo
    $user = new BookingData();
    $user->start_at    = $_POST["start_at"];
    $user->payment_day = $_POST["payment_day"];
    $user->type_id     = $_POST["type_id"];

    if ($_POST["type_id"] == 1) {
        $user->end_at = $_POST["end_at"];
    } else {
        $user->end_at = $_POST["selectdate"];
    }

    $user->place_start = ($_POST["place_start2"] > 0) ? $_POST["place_start2"] : $_POST["place_start"];
    $user->place_end   = ($_POST["place_end2"] > 0) ? $_POST["place_end2"] : $_POST["place_end"];

    $user->person_id  = $id_person;
    $user->person2_id = ($_POST["person2_id"] > 0) ? $_POST["person2_id"] : 0;

    $user->location  = $_POST["location"];
    $user->stock_id  = StockData::getPrincipal()->id;
    $user->type_sure = $_POST["type_sure"];
    $user->sure      = $_POST["sure"];
    $user->fuel      = $_POST["fuel"];
    $user->car_id    = $car_id;
    $user->car2_id   = ($_POST["car2_id"] > 0) ? $_POST["car2_id"] : 0;
    $user->type      = 1;

    // =============================
    // ✅ LIMPIAR VALORES MONETARIOS
    // =============================
    $price2 = floatval(str_replace(",", "", $_POST["price2"] ?? 0));
    $xtotal = floatval(str_replace(",", "", $_POST["xtotal"] ?? 0));
    $plane  = floatval(str_replace(",", "", $_POST["plane"] ?? 0));
    $card   = floatval(str_replace(",", "", $_POST["card"] ?? 0));
    $iva    = floatval(str_replace(",", "", $_POST["iva"] ?? 0));

    // Guardar precio base
    $user->price = $price2;

    // =============================
    // ✅ SI PAGA CON TARJETA (f_id = 3)
    // =============================
    if ($_POST["f_id"] == 3) {
        $user->card = $card * (StockData::getPrincipal()->card / 100);
    } else {
        $user->card = 0;
    }

    // =============================
    // ✅ TOTAL CORRECTO
    // =============================
    $user->total = ($price2*$_POST["day"]) + $xtotal + $plane + $card + $iva;

    $user->payment = floatval(str_replace(",", "", $_POST["payment"]));
    $user->day     = $_POST["day"];
    $user->deposit = 0;
    $user->f_id    = $_POST["f_id"];
    $user->xtotal  = $_POST["xtotal"];

    $user->unit_extra1  = $_POST["unit_extra1"];
    $user->price_extra1 = $_POST["price_extra1"];
    $user->unit_extra2  = $_POST["unit_extra2"];
    $user->price_extra2 = $_POST["price_extra2"];
    $user->unit_extra3  = $_POST["unit_extra3"];
    $user->price_extra3 = $_POST["price_extra3"];

    // ✅ CORREGIDO (antes copiabas extra3)
    $user->unit_extra4  = $_POST["unit_extra4"];
    $user->price_extra4 = $_POST["price_extra4"];

    // USD y tasa
    $user->usd_price  = $_POST["usd_price"];
    $user->tasa_dolar = $_POST["tasa_dolar"];

    // IVA - comprobante
    $receiptIdAndName   = explode("-", $_POST["type_iva"]);
    $user->iva          = $_POST["iva"];
    $user->value_iva    = $_POST["value_iva"];
    $user->type_iva     = $receiptIdAndName[0];
    $user->number_iva   = $receiptIdAndName[1] . "" . $receiptIdAndName[2];

    $user->user_id = $_SESSION["user_id"];
    $user->plane   = $_POST["plane"];
    $user->status  = 1;

    $user->add();

    $spends = BookingData::getAllByID()[0]->id ?? 0;
    $kmx = CarsData::getById($car_id);
    $kmx->status = 2;
    $kmx->update_status();

    $payment = new PaymentData();
    $payment->sell_id = $spends;
    $payment->val = $total;
    $payment->user_id = $_SESSION["user_id"];
    $payment->stock_id = StockData::getPrincipal()->id;
    $payment->person_id = $id_person;
    $payment->add();

    if ($_POST["payment"] > 0) {
        $payment2 = new PaymentData();
        $payment2->sell_id = $spends;
        $payment2->val = -1 * $_POST["payment"];
        $payment2->user_id = $_SESSION["user_id"];
        $payment2->stock_id = StockData::getPrincipal()->id;
        $payment2->person_id = $id_person;
        $payment2->add_payment();
    }

   
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carpeta donde se guardarán las imágenes
$uploadDir = "danger/";

// Crear la carpeta si no existe
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Lista de nombres de los inputs
$inputNames = [
    "image1", "image2", "image3", "image4", "image5", "image6", "image7", "image8", "image9", "image10", 
    "image11", "image12", "image13", "image14", "image15", "image16", "image17", "image18", "image19", "image20", 
    "image21", "image22", "image23", "image24", "image25", "image26", "image27", "image28", "image29"
];



// Array para almacenar los nombres de los archivos
$imageNames = [];
$errors = [];

// Procesar las imágenes de forma más eficiente
foreach ($inputNames as $inputName) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES[$inputName]['tmp_name'];
        $originalName = basename($_FILES[$inputName]['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // Generar un nuevo nombre único para cada imagen
        $newFileName = time() . "_" . uniqid() . ".jpg"; // Convertimos todo a JPG
        $targetPath = $uploadDir . $newFileName;

        // Primero mover el archivo al directorio destino
        if (move_uploaded_file($fileTmp, $targetPath)) {
            // Comprimir la imagen después de moverla
            if (compressImage($targetPath, $targetPath)) {
                $imageNames[] = $newFileName;
            } else {
                $errors[] = "Error al procesar la imagen: " . $originalName;
            }
        } else {
            $errors[] = "Error al mover el archivo: " . $originalName;
        }
    } elseif (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = "Error en el archivo " . $_FILES[$inputName]['name'] . ": Código " . $_FILES[$inputName]['error'];
    }
}

// Verificar si se subió al menos una imagen
if (!empty($imageNames)) {
    $danger = implode("|", $imageNames);
}


// Lista de nombres de los inputs
$commentName = [
    "comment1", "comment2", "comment3", "comment4", "comment5", "comment6"
];

// Array para almacenar los nombres de los archivos
$commentNames = [];
foreach ($commentName as $comment) {
if (isset($_POST[$comment])){
$commentNames[] = $_POST[$comment];
}
}

// Verificar si se subió al menos una imagen
if (!empty($commentNames)) {
    $comentario = implode("|", $commentNames);
}

// =====================================================
// ✍️ VALIDAR Y GUARDAR FIRMA DEL CLIENTE
// =====================================================
$firma = "";

// Verifica si viene el campo base64 desde el formulario
if (!empty($_POST["base64"])) {
    $img = $_POST["base64"];

    // 🔹 Comprobar si tiene contenido real (no vacío, no solo el encabezado)
    if (strlen($img) > 500 && strpos($img, "data:image") !== false) {
        
        // 🔹 Limpiar el contenido Base64
        $img = str_replace('data:image/png;base64,', '', $img);

        // 🔹 Decodificar imagen
        $fileData = base64_decode($img);

        // 🔹 Generar nombre único y guardar
        $fileName = "firmas/".uniqid().'.png';

        if (file_put_contents($fileName, $fileData)) {
            // ✅ Se guardó correctamente
            $firma = $fileName;
        }
    }
}

// =====================================================
// 💾 ACTUALIZAR EN BASE DE DATOS SOLO SI REALMENTE FIRMÓ
// =====================================================
if (!empty($firma)) {
        $xuser = BookingData::getById($spends);
        $xuser->firma = $firma;
        $xuser->update_firma();
        
}

	$user = new DeliveryData();
	$user->firma = $firma;
	$user->danger = $danger;
	$user->method = 2;
	$user->cat = isset($_POST["cat"])?1:0;
	$user->radio = isset($_POST["radio"])?1:0;
	$user->replacement = isset($_POST["replacement"])?1:0;
	$user->antenna = isset($_POST["antenna"])?1:0;
	$user->keyring = isset($_POST["keyring"])?1:0;
	$user->carpets = isset($_POST["carpets"])?1:0;
	$user->belts = isset($_POST["belts"])?1:0;
	$user->roof_lining = isset($_POST["roof_lining"])?1:0;
	$user->mirrors = isset($_POST["mirrors"])?1:0;
	$user->board = isset($_POST["board"])?1:0;
	$user->rearview = isset($_POST["rearview"])?1:0;
	$user->watches = isset($_POST["watches"])?1:0;
	$user->document = isset($_POST["document"])?1:0;
	$user->lighter = isset($_POST["lighter"])?1:0;
	$user->crystals = isset($_POST["crystals"])?1:0;
	$user->cd = isset($_POST["cd"])?1:0;
	$user->fuel = $_POST["fuel"];
	$user->kms = $kmx->kms;
	$user->bumper = isset($_POST["bumper"])?1:0;
	$user->equalizer = isset($_POST["equalizer"])?1:0;
	$user->cup_holder = isset($_POST["cup_holder"])?1:0;
	$user->plate = isset($_POST["plate"])?1:0;
	$user->seats = isset($_POST["seats"])?1:0;
	$user->logo = isset($_POST["logo"])?1:0;
	$user->batery = isset($_POST["batery"])?1:0;
	$user->top = isset($_POST["top"])?1:0;
	$user->comment = $comentario;
	$user->no_batery = $_POST["no_batery"];
	$user->car_id = $_POST["car_id"];
	$user->booking_id = $spends;
    $user->user_id = $xuser->user_id;
    $user->receiver_id = 0;
    $user->delivery_id = $_SESSION["user_id"];
	$user->add();
	

    $log = new ACData();
    $log->user_id = $_SESSION["user_id"];
    $log->accion = "Agrego la reserva";
    $log->add();

    header("Location: ./?view=contract&opt=modal&id=$spends");
    exit;
} else {
    header("Location: ./?view=booking&opt=earring");
    exit;
}
endif;
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="upd"){

	// =========================
	// Helpers (NO ROMPEN NADA)
	// =========================
	function toNumber($v){
		// quita separadores de miles y deja decimal como punto si viene
		// soporta: "1,234.56" | "1.234,56" | "1234" | "1,234"
		$v = trim((string)$v);
		if($v==="") return 0;

		// si trae ambos ("," y ".") asumimos el ÚLTIMO separador como decimal
		$hasComma = strpos($v, ",") !== false;
		$hasDot   = strpos($v, ".") !== false;

		if($hasComma && $hasDot){
			$lastComma = strrpos($v, ",");
			$lastDot   = strrpos($v, ".");
			if($lastComma > $lastDot){
				// decimal es coma -> quitamos puntos de miles y cambiamos coma por punto
				$v = str_replace(".", "", $v);
				$v = str_replace(",", ".", $v);
			}else{
				// decimal es punto -> quitamos comas de miles
				$v = str_replace(",", "", $v);
			}
		}else{
			// si solo hay coma, asumimos coma como separador de miles (como venías usando)
			// (si tú usas coma como decimal, cámbialo aquí)
			$v = str_replace(",", "", $v);
		}

		return floatval($v);
	}

	// =========================
	// Valores base (limpios)
	// =========================
	$total_base  = toNumber($_POST["total"] ?? 0);      // Total Reserva (sin iva/card/otros si así lo manejas)
	$xtotal      = toNumber($_POST["xtotal"] ?? 0);     // Total Extra
	$plane       = toNumber($_POST["plane"] ?? 0);      // Otros Cobros
	$payment_in  = toNumber($_POST["payment"] ?? 0);    // Abono
	$remainingIn = toNumber($_POST["remaining"] ?? 0);  // Restante (viene formateado)

	// =========================
	// Porcentajes dinámicos
	// =========================
	$card_percent = 0;
	if(isset($_POST["f_id"]) && intval($_POST["f_id"])==3){
		$card_percent = 3; // tu regla actual
	}

	$iva_percent = 0;
	if(isset($_POST["iva"]) && intval($_POST["iva"])==18){
		$iva_percent = 18; // tu regla actual
	}

	// =========================
	// Cálculo de montos
	// =========================
	$base_para_imp = ($total_base + $xtotal);

	$card_amount = ($card_percent>0) ? ($base_para_imp * ($card_percent/100)) : 0;
	$iva_amount  = ($iva_percent>0)  ? ($base_para_imp * ($iva_percent/100))  : 0;

	// total final guardado (incluye: total + extra + otros + card + iva)
	$total_final = ($total_base + $xtotal + $plane) + ($card_amount + $iva_amount);

	// restante real (por si el front manda formato raro)
	$remaining_calc = $total_final - $payment_in;

	// =========================
	// Datos del vehículo / booking
	// =========================
	$bk = CarsData::getById($_POST["car_id"]);
	$k  = BookingData::getById($_POST["user_id"]);

	$k->person_id  = $_POST["person_id"];
	$k->person2_id = $_POST["person2_id"];
	$k->start_at   = $_POST["start_at"];
	$k->end_at     = $_POST["end_at"];

	$k->type_sure  = $_POST["type_sure"];
	$k->sure       = $_POST["sure"];

	$k->f_id       = $_POST["f_id"];
	$k->fuel       = $_POST["fuel"];

	// ✅ FIX: en vez de (>0) usamos empty() para texto
	if(!empty(trim($_POST["place_start2"] ?? ""))){
		$k->place_start = $_POST["place_start2"];
	}else{
		$k->place_start = $_POST["place_start"];
	}

	if(!empty(trim($_POST["place_end2"] ?? ""))){
		$k->place_end = $_POST["place_end2"];
	}else{
		$k->place_end = $_POST["place_end"];
	}

	// ✅ FIX: guarda % (no dependas de $_POST["card"] que no siempre viene)
	$k->card = $card_percent;
	$k->iva  = $iva_percent;

	$k->day      = intval($_POST["day"]);
	$k->car_id   = $bk->id;

	$k->price_stock = $bk->provider_price;
	$k->stock_id    = StockData::getPrincipal()->id;

	// ✅ FIX: precio con decimales
	$k->price   = toNumber($_POST["price2"] ?? 0);

	// ✅ FIX: totales con decimales
	$k->xtotal  = $xtotal;
	$k->plane   = $plane;
	$k->payment = $payment_in;
	$k->total   = $total_final;

	$k->update();


	// =========================
	// Actualizar primer registro (si existe)
	// =========================
	foreach(PaymentData::getAllBySQL("where payment_type_id=1 and booking_id=".$_POST["user_id"]." order by created_at asc limit 1") as $payment2):
		// ✅ Mejor: guarda el restante calculado (o usa $remainingIn si lo prefieres)
		$payment2->val = $remaining_calc;
		$payment2->update();
	endforeach;


	// =========================
	// Registrar el abono (si aplica)
	// =========================
	if($payment_in > 0){
		$payment2 = new PaymentData();
		$payment2->sell_id  = $_POST["user_id"];  // así lo tienes en add_payment()
		$payment2->val      = -1 * $payment_in;
		$payment2->user_id  = $_SESSION["user_id"];
		$payment2->stock_id = StockData::getPrincipal()->id;
		$payment2->person_id= $_POST["person_id"];
		$payment2->add_payment();
	}


	// =========================
	// LOG
	// =========================
	$ac = new ACData();
	$ac->user_id = $_SESSION["user_id"];
	$ac->accion  = "Hizo un cambio en la reserva # ".$k->id;
	$ac->add();


	header('location:./?view=contract&opt=modal&id='.$k->id);
	
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="updrandom"){

    $k = BookingData::getById($_POST["user_id"]);
	$k->start_at = $_POST["start_at"];
	$k->end_at = $_POST["end_at"];
	$k->e2d_at = $_POST["end_at"];
	$k->total = $_POST["total"];
	$k->price = $_POST["price"];
	$k->day = $_POST["day"];
	$k->upd_random();
	
if ($_POST["remaining"]>0):
     $payment2 = new PaymentData();
			 		$payment2->sell_id = $k->id;
				 	$payment2->val = $_POST["remaining"];
				 	$payment2->user_id = $_SESSION["user_id"];
                    $payment2->stock_id = StockData::getPrincipal()->id;
				 	$payment2->person_id=$k->person_id;
				 	$payment2->add();	
else: 

foreach(PaymentData::getAllBySQL("where payment_type_id=1 and booking_id=".$_POST["user_id"]." order by created_at asc limit 1") as $payment2):
$payment2->val = $payment2->val-abs($_POST["remaining"]);
$payment2->update();	
endforeach;   

endif; 

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo un cambio en la reserva de # ".$user->person_id;
          $user->add();

echo 'true';
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="updrandom2"){


       
    $k = BookingData::getById($_POST["user_id"]);
		if($_POST["place_start2"]>0):
	$k->place_start = $_POST["place_start2"];
	else:
	$k->place_start = $_POST["place_start"];	    
	endif;
	if($_POST["place_end2"]>0):
	$k->place_end = $_POST["place_end2"];
	else:
	$k->place_end = $_POST["place_end"];	    
	endif;
	if($_POST["car2_id"]>0):
	$k->car2_id = $_POST["car2_id"];
	else:
	$k->car2_id = 0;
	endif;
	$k->upd_random2();
	

   
if ($_POST["car2_id"]>0):
    $jk = BookingData::getById($_POST["user_id"]);
	$jk->st2rt_at = date("Y-m-d h:i:s");
	$jk->upd_start();
	
	 if (($q->price_stock>0) and ($qx->user_id==$q->user_id)):
	
	$jz = BookingData::getById($_POST["user_id"]+1);
	$jz->st2rt_at = date("Y-m-d h:i:s");
	$jz->upd_start();
	
	endif;
	
	$kmx = CarsData::getById($_POST["car_id"]);
	$kmx->kms=$_POST["kms"];
    if ($_POST["kms"]<=($kmx->kms_current+$kmx->charge_kms)):
	$kmx->status = 0;
	else:
	$kmx->status = 3;
	$kmx->update_kms();
    endif;


error_reporting(E_ALL);
ini_set('display_errors', 1);


// =============================
// LISTAS DE INPUTS
// =============================
// Bloque 1 (danger)
$inputNames1 = [
    "image1","image2","image3","image4","image5","image6","image7","image8","image9","image10",
    "image11","image12","image13","image14","image15","image16","image17","image18","image19","image20",
    "image21","image22","image23","image24","image25","image26","image27","image28","image29"
];
$commentNames1 = ["comment1","comment2","comment3","comment4","comment5","comment6"];

// Bloque 2 (secure)
$inputNames2 = [
    "secundary_image1","secundary_image2","secundary_image3","secundary_image4","secundary_image5","secundary_image6","secundary_image7","secundary_image8","secundary_image9","secundary_image10","secundary_image11","secundary_image12","secundary_image13","secundary_image14","secundary_image15","secundary_image16","secundary_image17","secundary_image18","secundary_image19","secundary_image20","secundary_image21","secundary_image22","secundary_image23","secundary_image24","secundary_image25","secundary_image26","secundary_image27","secundary_image28","secundary_image29"
];
$commentNames2 = ["secundary_comment1","secundary_comment2","secundary_comment3","secundary_comment4","secundary_comment5","secundary_comment6"];

// =============================
// BLOQUE 1 -> danger
// =============================
$uploadDir1 = "danger/";
if (!is_dir($uploadDir1)) mkdir($uploadDir1, 0777, true);

$imageNames1 = [];
foreach ($inputNames1 as $inputName) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES[$inputName]['tmp_name'];
        $newFileName = time() . "_" . uniqid() . ".jpg";
        $targetPath = $uploadDir1 . $newFileName;
        if (move_uploaded_file($fileTmp, $targetPath)) {
            if (compressImage($targetPath, $targetPath)) {
                $imageNames1[] = $newFileName;
            }
        }
    }
}
if (!empty($imageNames1)) {
    $danger = implode("|", $imageNames1);
}

// Comentarios bloque 1
$comments1 = [];
foreach ($commentNames1 as $c) {
    if (!empty($_POST[$c])) {
        $comments1[] = $_POST[$c];
    }
}
if (!empty($comments1)) {
    $comentario = implode("|", $comments1);
}

// =============================
// BLOQUE 2 -> secure
// =============================
$uploadDir2 = "danger/";
if (!is_dir($uploadDir2)) mkdir($uploadDir2, 0777, true);

$imageNames2 = [];
foreach ($inputNames2 as $inputName) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES[$inputName]['tmp_name'];
        $newFileName = time() . "_" . uniqid() . ".jpg";
        $targetPath = $uploadDir2 . $newFileName;
        if (move_uploaded_file($fileTmp, $targetPath)) {
            if (compressImage($targetPath, $targetPath)) {
                $imageNames2[] = $newFileName;
            }
        }
    }
}
if (!empty($imageNames2)) {
    $secure = implode("|", $imageNames2);
}

// Comentarios bloque 2
$comments2 = [];
foreach ($commentNames2 as $c) {
    if (!empty($_POST[$c])) {
        $comments2[] = $_POST[$c];
    }
}
if (!empty($comments2)) {
    $comentario2 = implode("|", $comments2);
}

    $xuser = BookingData::getById($_POST["user_id"]);

	$user = new DeliveryData();
	$user->firma = $xuser->firma;
	$user->danger = $danger;
	$user->method = 1;
	$user->cat = isset($_POST["cat"])?1:0;
	$user->radio = isset($_POST["radio"])?1:0;
	$user->replacement = isset($_POST["replacement"])?1:0;
	$user->antenna = isset($_POST["antenna"])?1:0;
	$user->keyring = isset($_POST["keyring"])?1:0;
	$user->carpets = isset($_POST["carpets"])?1:0;
	$user->belts = isset($_POST["belts"])?1:0;
	$user->roof_lining = isset($_POST["roof_lining"])?1:0;
	$user->mirrors = isset($_POST["mirrors"])?1:0;
	$user->board = isset($_POST["board"])?1:0;
	$user->rearview = isset($_POST["rearview"])?1:0;
	$user->watches = isset($_POST["watches"])?1:0;
	$user->document = isset($_POST["document"])?1:0;
	$user->lighter = isset($_POST["lighter"])?1:0;
	$user->crystals = isset($_POST["crystals"])?1:0;
	$user->cd = isset($_POST["cd"])?1:0;
	$user->fuel = $_POST["fuel"];
	$user->kms = $_POST["kms"];
	$user->bumper = isset($_POST["bumper"])?1:0;
	$user->equalizer = isset($_POST["equalizer"])?1:0;
	$user->cup_holder = isset($_POST["cup_holder"])?1:0;
	$user->plate = isset($_POST["plate"])?1:0;
	$user->seats = isset($_POST["seats"])?1:0;
	$user->logo = isset($_POST["logo"])?1:0;
	$user->batery = isset($_POST["batery"])?1:0;
	$user->top = isset($_POST["top"])?1:0;
	$user->comment = $_POST["comment"];
	$user->no_batery = $_POST["no_batery"];
	$user->car_id = $_POST["car_id"];
	$user->booking_id = $_POST["user_id"];
    $user->user_id = $_SESSION["user_id"];
	$user->add();
	
	
	$user = new DeliveryData();
	$user->firma = $xuser->firma;
	$user->danger = $secure;
	$user->method = 2;
	$user->cat = isset($_POST["cat"])?1:0;
	$user->radio = isset($_POST["radio"])?1:0;
	$user->replacement = isset($_POST["replacement"])?1:0;
	$user->antenna = isset($_POST["antenna"])?1:0;
	$user->keyring = isset($_POST["keyring"])?1:0;
	$user->carpets = isset($_POST["carpets"])?1:0;
	$user->belts = isset($_POST["belts"])?1:0;
	$user->roof_lining = isset($_POST["roof_lining"])?1:0;
	$user->mirrors = isset($_POST["mirrors"])?1:0;
	$user->board = isset($_POST["board"])?1:0;
	$user->rearview = isset($_POST["rearview"])?1:0;
	$user->watches = isset($_POST["watches"])?1:0;
	$user->document = isset($_POST["document"])?1:0;
	$user->lighter = isset($_POST["lighter"])?1:0;
	$user->crystals = isset($_POST["crystals"])?1:0;
	$user->cd = isset($_POST["cd"])?1:0;
	$user->fuel = $_POST["fuel2"];
	$user->kms = $kmx2->kms;
	$user->bumper = isset($_POST["bumper"])?1:0;
	$user->equalizer = isset($_POST["equalizer"])?1:0;
	$user->cup_holder = isset($_POST["cup_holder"])?1:0;
	$user->plate = isset($_POST["plate"])?1:0;
	$user->seats = isset($_POST["seats"])?1:0;
	$user->logo = isset($_POST["logo"])?1:0;
	$user->batery = isset($_POST["batery"])?1:0;
	$user->top = isset($_POST["top"])?1:0;
	$user->comment = $_POST["comment"];
	$user->no_batery = $_POST["no_batery"];
	$user->car_id = $_POST["car_id"];
	$user->booking_id = $_POST["user_id"];
    $user->user_id = $_SESSION["user_id"];
    $user->random = 1;
	$user->add();
endif;

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo un cambio en la reserva de # ".$user->person_id;
          $user->add();

header('location:./?view=contract&opt=modal&id='.$_POST["user_id"]);    
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="received"){
    
    $bk = BookingData::getById($_POST["user_id"]);
	$bk->status = 3;
	$bk->update_status();
	
	$kmx = CarsData::getById($bk->car_id);
	$kmx->status = 0;
	$kmx->kms=$_POST["kms"];
    if ($_POST["kms"]<=($kmx->kms_current+$kmx->charge_kms)):
	$kmx->status = 0;
	else:
	$kmx->status = 3;
    endif;
	$kmx->update_kms();
	

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carpeta donde se guardarán las imágenes
$uploadDir = "danger/";

// Crear la carpeta si no existe
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Lista de nombres de los inputs
$inputNames = [
    "image1", "image2", "image3", "image4", "image5", "image6", "image7", "image8", "image9", "image10", 
    "image11", "image12", "image13", "image14", "image15", "image16", "image17", "image18", "image19", "image20", 
    "image21", "image22", "image23", "image24", "image25", "image26", "image27", "image28", "image29"
];


// Array para almacenar los nombres de los archivos
$imageNames = [];
$errors = [];

// Procesar las imágenes de forma más eficiente
foreach ($inputNames as $inputName) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES[$inputName]['tmp_name'];
        $originalName = basename($_FILES[$inputName]['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // Generar un nuevo nombre único para cada imagen
        $newFileName = time() . "_" . uniqid() . ".jpg"; // Convertimos todo a JPG
        $targetPath = $uploadDir . $newFileName;

        // Primero mover el archivo al directorio destino
        if (move_uploaded_file($fileTmp, $targetPath)) {
            // Comprimir la imagen después de moverla
            if (compressImage($targetPath, $targetPath)) {
                $imageNames[] = $newFileName;
            } else {
                $errors[] = "Error al procesar la imagen: " . $originalName;
            }
        } else {
            $errors[] = "Error al mover el archivo: " . $originalName;
        }
    } elseif (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = "Error en el archivo " . $_FILES[$inputName]['name'] . ": Código " . $_FILES[$inputName]['error'];
    }
}

// Verificar si se subió al menos una imagen
if (!empty($imageNames)) {
    $danger = implode("|", $imageNames);
}


// Lista de nombres de los inputs
$commentName = [
    "comment1", "comment2", "comment3", "comment4", "comment5", "comment6"
];

// Array para almacenar los nombres de los archivos
$commentNames = [];
foreach ($commentName as $comment) {
if (isset($_POST[$comment])){
$commentNames[] = $_POST[$comment];
}
}

// Verificar si se subió al menos una imagen
if (!empty($commentNames)) {
    $comentario = implode("|", $commentNames);
}

$img = $_POST["base64"];
$img = str_replace('data:image/png;base64,', '', $img);
$fileData = base64_decode($img);
$fileName = "firmas/".uniqid().'.png';

file_put_contents($fileName, $fileData);

    $xuser = BookingData::getById($_POST["user_id"]);
    if(!empty($xuser->firma)):
    $xuser = BookingData::getById($_POST["user_id"]);
    $xuser->firma = $fileName;
	$xuser->update_firma();
    endif;

	$user = new DeliveryData();
	$user->firma = $fileName;
	$user->danger = $danger;
	$user->cat = isset($_POST["cat"])?1:0;
	$user->radio = isset($_POST["radio"])?1:0;
	$user->replacement = isset($_POST["replacement"])?1:0;
	$user->antenna = isset($_POST["antenna"])?1:0;
	$user->keyring = isset($_POST["keyring"])?1:0;
	$user->carpets = isset($_POST["carpets"])?1:0;
	$user->belts = isset($_POST["belts"])?1:0;
	$user->roof_lining = isset($_POST["roof_lining"])?1:0;
	$user->mirrors = isset($_POST["mirrors"])?1:0;
	$user->board = isset($_POST["board"])?1:0;
	$user->rearview = isset($_POST["rearview"])?1:0;
	$user->watches = isset($_POST["watches"])?1:0;
	$user->document = isset($_POST["document"])?1:0;
	$user->lighter = isset($_POST["lighter"])?1:0;
	$user->crystals = isset($_POST["crystals"])?1:0;
	$user->cd = isset($_POST["cd"])?1:0;
	$user->kms=$_POST["kms"];
	$user->fuel = $_POST["fuel"];
	$user->method = 1;
	$user->bumper = isset($_POST["bumper"])?1:0;
	$user->equalizer = isset($_POST["equalizer"])?1:0;
	$user->cup_holder = isset($_POST["cup_holder"])?1:0;
	$user->plate = isset($_POST["plate"])?1:0;
	$user->seats = isset($_POST["seats"])?1:0;
	$user->logo = isset($_POST["logo"])?1:0;
	$user->batery = isset($_POST["batery"])?1:0;
	$user->top = isset($_POST["top"])?1:0;
	$user->comment = $comentario;
	$user->no_batery = $_POST["no_batery"];
	$user->car_id = $bk->car_id;
	$user->booking_id = $_POST["user_id"];
    $user->user_id = $bk->user_id;
    $user->receiver_id = $_SESSION["user_id"];
    $user->delivery_id = 0;
	$user->add();

          $user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Verifico la reserva al entrar";
          $user->add();
          
          
header('location:./?view=contract&opt=modal&id='.$_POST['user_id']);

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="status"){
	$user = BookingData::getById($_GET["id"]);
	$user->status = $_GET["status"];
	$user->update_status();
    
    $user = CarsData::getById($_GET["car"]);
	$user->status = $_GET["excar"];
	$user->update_status();

header('location:./?view=contrat&opt=all');
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"){
$q = BookingData::getById($_GET["id"]);
$qx = BookingData::getById($q->id+1);

foreach(DeliveryData::getAllBySQL("where booking_id=".$q->id) as $delivery):
$delivery->del();
endforeach;
    
$user = CarsData::getById($q->car_id);
	$user->status = 0;
	$user->update_status();
	
if($q->price_stock>0 and $qx->user_id==$q->user_id):

$q->del();

foreach(PaymentData::getAllBySQL("where booking_id=".$q->id) as $payment):
$payment->del();
endforeach;

$qx->del();

foreach(PaymentData::getAllBySQL("where booking_id=".$qx->id) as $paymenx):
$paymenx->del();
endforeach;


else:

$q->del();

foreach(PaymentData::getAllBySQL("where booking_id=".$_GET["id"]) as $payment):
$payment->del();
endforeach;

endif;
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino la reserva";
          $user->add();
header('location:./?view=contract&opt=running');

    
}else if(isset($_GET["opt"]) && $_GET["opt"]=="what"){
	$persxn = PersonData::getById($_GET["person_id"]);
	$carx = CarsData::getById($_GET["car_id"]);
	$sell = BookingData::getById($_GET["id"]);
	
	
	if(empty($persxn->username)):
	
	$xperson = PersonData::getById($_GET["person_id"]);

    function generarMatricula($prefijo = '2024') {
    // Generar un número aleatorio único
    $numeroUnico = mt_rand(1000, 9999);  // Número aleatorio entre 1000 y 9999

    // Obtener el último ID de la base de datos o algún otro identificador
    // Supongamos que estás usando una base de datos y tienes el último ID de alumno
    $ultimoIdAlumno = obtenerUltimoIdAlumno();  // Función ficticia para obtener el último ID
    
    // Incrementar el ID para la nueva matrícula
    $nuevoId = $ultimoIdAlumno + 1;

    // Combinar el prefijo, el nuevo ID y el número único para formar la matrícula
    $matricula = $prefijo . '-' . str_pad($nuevoId, 4, '0', STR_PAD_LEFT) . '-' . $numeroUnico;

    return $matricula;
}

function obtenerUltimoIdAlumno() {
    // Esta es una función de ejemplo que debería ser reemplazada con la lógica real para obtener el último ID de alumno
    // Puede ser una consulta a la base de datos, por ejemplo
    return $persxn->phone;  // Ejemplo de ID actual
}

// Generar una matrícula
$matriculaGenerada = generarMatricula();
  
  $xperson->username = $matriculaGenerada;
  $xperson->password = sha1(md5($matriculaGenerada));
  $xperson->update_username();
	   
	endif;
    
    
    // Obtener los datos del formulario
    $nombre = strtoupper($persxn->name);
    $correo = strtoupper($persxn->email);
    $person_phone = strtoupper($persxn->phone);
    $day = $sell->day;
    $precio = $sell->price;
    $entregar = strtoupper($sell->place_start);
    $recibir = strtoupper($sell->place_end);
    $inicio = date("d-m-Y h:i a",strtotime($sell->start_at));
    $termino = date("d-m-Y h:i a",strtotime($sell->end_at));
    $carro =  strtoupper($carx->getBrand()->name." ".$carx->name." ".$carx->year);
    $total_total =  number_format($sell->total,2,".",",");
    $web_url = "https://".StockData::getPrincipal()->web_url."/?%26username=".$persxn->username;
    
    // Número de teléfono (incluye el código del país, sin signos + o espacios)
    $telefono = preg_replace('/\D/', '', $persxn->phone); // Reemplaza con tu número de WhatsApp
    
    // Crear el mensaje
    $texto = "DETALLE DE LA RESERVACIÓN"."%0A" ." ". "%0A" ."NOMBRE DEL CLIENTE: ".$nombre. "%0A" ."EMAIL: ".$correo. "%0A" ."TELEFONO: ".$person_phone. "%0A" ."FECHA INICIO: ".$inicio. "%0A" ."FECHA TERMINO: ".$termino."%0A" ." ". "%0A" ."VEHICULO: ".$carro. "%0A" ."PRECIO: ".$precio. "%0A" ."DIA: ".$day. "%0A" ."TOTAL: ".$total_total. "%0A" ."ENTREGAR AL CLIENTE: ".$entregar. "%0A" ."RECIBIR DEL CLIENTE: ".$recibir."%0A" ." ". "%0A" ."INFORMACION DE LA RESERVACION"."%0A" ." ". "%0A"."URL:".$web_url;
    
    // Generar el enlace de WhatsApp
    $enlace = "https://api.whatsapp.com/send?phone=$telefono&text=$texto";
    
    // Redirigir al usuario a WhatsApp
    header("Location: $enlace");
    exit();
    
}
?>