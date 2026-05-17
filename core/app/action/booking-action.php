<?php
// ======================================================
// ✅ COMPATIBILIDAD PHP 8.4
// ======================================================
error_reporting(E_ALL);
ini_set('display_errors', '1');

$found = false;
$danger = '';
$comentario = '';
$total = 0;
$card = 0;
$iva = 0;

if (!function_exists('post84')) {
    function post84($key, $default = '') {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
}

if (!function_exists('get84')) {
    function get84($key, $default = '') {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
}

if (!function_exists('toNumber84')) {
    function toNumber84($value) {
        $value = trim((string)$value);
        if ($value === '') return 0;

        $hasComma = strpos($value, ',') !== false;
        $hasDot   = strpos($value, '.') !== false;

        if ($hasComma && $hasDot) {
            $lastComma = strrpos($value, ',');
            $lastDot   = strrpos($value, '.');

            if ($lastComma > $lastDot) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                $value = str_replace(',', '', $value);
            }
        } else {
            $value = str_replace(',', '', $value);
        }

        return floatval($value);
    }
}

if (!function_exists('safeText84')) {
    function safeText84($value, $default = '') {
        $value = trim((string)$value);
        return $value !== '' ? $value : $default;
    }
}

if (!function_exists('pickPlace84')) {
    function pickPlace84($primaryKey, $secondaryKey, $default = 'No especificado') {
        $primary = isset($_POST[$primaryKey]) ? trim((string)$_POST[$primaryKey]) : '';
        $secondary = isset($_POST[$secondaryKey]) ? trim((string)$_POST[$secondaryKey]) : '';

        if ($primary !== '' && $primary !== '0') {
            return $primary;
        }

        if ($secondary !== '' && $secondary !== '0') {
            return $secondary;
        }

        return $default;
    }
}


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


if(isset($_GET["opt"]) && $_GET["opt"]=="add"):

////////////////////////////////////////////////////////////////////////// DIFERENTE DE VEHICULO EXTERNO ///////////////////////////////////////////////////////////////////////////
if (intval($_POST["method"] ?? 0) == 3):

if (intval($_POST["nuevo_cliente_activo"] ?? 0) > 0):



$uploadPath = "CF-SYSTEMS/storage/invoice_files/"; 
$location = StatesData::getById($_POST["location"]);

/* ================================
 * FUNCIONES AUXILIARES
 * ================================ */
if (!function_exists('generarMatricula')) {
    function generarMatricula($nombreCliente, $telefonoRaw) {
        $prefijo = generarPrefijo($nombreCliente);
        $telefono = limpiarTelefono($telefonoRaw);
        return $prefijo . $telefono;
    }
}

if (!function_exists('generarPrefijo')) {
    function generarPrefijo($nombre) {
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT', (string)$nombre);
        if ($ascii === false) { $ascii = (string)$nombre; }
        $nombre_limpio = strtoupper(preg_replace('/[^A-Z]/', '', $ascii));
        $palabras = explode(" ", $nombre_limpio);
        $prefijo = "";
        foreach ($palabras as $p) {
            if (strlen($p) > 0 && strlen($prefijo) < 3) $prefijo .= $p[0];
        }
        if (strlen($prefijo) < 2) $prefijo .= substr($nombre_limpio, 0, 3 - strlen($prefijo));
        return strtoupper($prefijo) . "-";
    }
}

if (!function_exists('limpiarTelefono')) {
    function limpiarTelefono($telefono) {
        return preg_replace('/\D/', '', (string)$telefono);
    }
}

/* ================================
 * DATOS CLIENTE
 * ================================ */

$nombreCliente = StockData::getPrincipal()->name;
$telefonoIngresado = $_POST["phone"] ?? "";
$matriculaGenerada = generarMatricula($nombreCliente, $telefonoIngresado);

$user = new PersonData();

$user->name = $_POST["name"] ?? "";
$user->no = $_POST["no"] ?? "";
$user->rnc = $_POST["rnc"] ?? "";

$user->language = $_POST["language"] ?? "";
$user->birthday = $_POST["birthday"] ?? "";
$user->gender = $_POST["gender"] ?? "";

$user->username = $matriculaGenerada;
$user->password = sha1(md5($matriculaGenerada));

$user->reference = $_POST["reference"] ?? "";
$user->location = $_POST["location"] ?? "";

$user->longitud = $location->longitud ?? "";
$user->latitud = $location->latitud ?? "";

$user->license = $_POST["license"] ?? "";

$user->email = $_POST["email"] ?? "";

/* =========================================
 * 🔥 FIX PHP 8.4
 * ========================================= */
$user->expirelicense = $_POST["expirelicense"] ?? "";
$user->issuedlicense = $_POST["issuedlicense"] ?? "";

$user->phone = $_POST["phone"] ?? "";
$user->phone2 = $_POST["phone2"] ?? "";

$user->passport = $_POST["passport"] ?? "";
$user->nationality = $_POST["nationality"] ?? "";

$user->address = $_POST["address"] ?? "";
$user->address2 = $_POST["address2"] ?? "";

$user->user_id = $_SESSION["user_id"] ?? 0;

$user->invoice_date = $_POST["invoice_date"] ?? "";
$user->passport_date = $_POST["passport_date"] ?? "";
$user->license_date = $_POST["license_date"] ?? "";
$user->home_date = $_POST["home_date"] ?? "";

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
        $user->$campo = "";
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

    if (!empty($car_id)):
     $user = new BookingData();

    $user->start_at   = $_POST["start_at"];
    $user->comment   = $_POST["comment"];
    $user->payment_day = $_POST["payment_day"];
    $user->type_id     = $_POST["type_id"];

    if($_POST["type_id"] == 1){
    $user->end_at = $_POST["end_at"];
    }else{
    $user->end_at = $_POST["selectdate"];
    }

    $user->place_start = pickPlace84("place_start2", "place_start");
    $user->place_end   = pickPlace84("place_end2", "place_end");

    $user->person_id = $id_person;
    $user->person2_id = ($_POST["person2_id"] > 0) ? $_POST["person2_id"] : 0;

    $user->location = $_POST["location"] ?? "";
    $user->stock_id = StockData::getPrincipal()->id;
    $user->type_sure = $_POST["type_sure"];
    $user->sure      = $_POST["sure"];
    $user->fuel      = $_POST["fuel"];
    $user->car_id    = $car_id;
    $user->car2_id   = ($_POST["car2_id"] > 0) ? $_POST["car2_id"] : 0;

    $user->type = 1;
    
    

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
    if($_POST["f_id"] == 3){
    $user->card = $card * (StockData::getPrincipal()->card / 100);
    } else {
    $user->card = 0;
    }

    // =============================
    // ✅ TOTAL CORRECTO
    // =============================
    $total = ($price2*$_POST["day"]) + $xtotal + $plane + $card + $iva;
    $user->total = $total;

    // =================================
    // ✅ RESTO DE CAMPOS
    // =================================
    $user->payment = intval(str_replace(",", "", $_POST["payment"]));
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

    // ❌ ERROR QUE TENÍAS: estabas copiando extra3 a extra4
    $user->unit_extra4  = $_POST["unit_extra4"];
    $user->price_extra4 = $_POST["price_extra4"];
    
     // USD y tasa
    $user->usd_price  = $_POST["usd_price"];
    $user->tasa_dolar = $_POST["tasa_dolar"];

    // IVA - comprobante
    $receiptIdAndName = explode("-", $_POST["type_iva"] ?? "");
    $user->iva        = $_POST["iva"] ?? 0;
    $user->value_iva = toNumber84($_POST["value_iva"] ?? 0);
    $user->type_iva   = $receiptIdAndName[0] ?? 0;
    $user->number_iva = ($receiptIdAndName[1] ?? "") . "" . ($receiptIdAndName[2] ?? "");

    $user->user_id = $_SESSION["user_id"] ?? 0;
    $user->plane   = $_POST["plane"];
    $user->status  = 0;

    $_notif_addRes1 = $user->add();

	$_notif_bookingId = (is_array($_notif_addRes1) && isset($_notif_addRes1[1])) ? intval($_notif_addRes1[1]) : 0;
	$_notif_stockId = intval(StockData::getPrincipal()->id);
	$_notif_personObj = PersonData::getById($id_person);
	$_notif_pname = isset($_notif_personObj->name) ? $_notif_personObj->name : '';
	NotificationService::notifyStockUsers($_notif_stockId, NotificationService::EVENT_BOOKING_CREATED,
		'Nueva reserva creada', 'Cliente: '.htmlspecialchars($_notif_pname).' — Reserva #'.$_notif_bookingId,
		['booking_id' => $_notif_bookingId, 'url' => './?view=booking&opt=modal&id='.$_notif_bookingId]);
	if(intval($id_person) > 0){
		NotificationService::notify('client', intval($id_person), NotificationService::EVENT_BOOKING_CREATED,
			'Tu reserva fue creada', 'Hemos registrado tu reserva #'.$_notif_bookingId.'. Gracias por confiar en nosotros.',
			['booking_id' => $_notif_bookingId, 'stock_id' => $_notif_stockId]);
	}
	$persxn = PersonData::getById($id_person);
	
    $kmx = CarsData::getById($car_id);
	$kmx->status = 1;
	$kmx->update_status();

    $id_speds = BookingData::getAllByID();
    $spends = $id_speds[0]->id!=null?$id_speds[0]->id:0;
    
    $payment = new PaymentData();
    $payment->sell_id = $spends;
    $payment->val = $total;
    $payment->user_id = $_SESSION["user_id"];
    $payment->stock_id = StockData::getPrincipal()->id;
    $payment->person_id = $id_person;
    $payment->is_stock = 0;
    $payment->add();

    if ($_POST["payment"] > 0):
        $payment3 = new PaymentData();
        $payment3->sell_id = $spends;
        $payment3->val = -1 * $_POST["payment"];
        $payment3->user_id = $_SESSION["user_id"];
        $payment3->stock_id = StockData::getPrincipal()->id;
        $payment3->person_id = $id_person;
        $payment3->is_stock = 0;
        $payment3->add_payment();
    endif;	
  
  

   header('location:./?view=booking&opt=modal&id='.$spends); 
   exit;
   
else:
    header("Location: ./?view=booking&opt=earring");
    exit;
  
endif;

  
////////////////////////////////////////////////////////////////////////// DIFERENTE DE VEHICULO EXTERNO ///////////////////////////////////////////////////////////////////////////
elseif (intval($_POST["method"] ?? 0) != 3):

    
if (intval($_POST["iva"] ?? 0) == 18) {
    $receiptName = explode("-", $_POST["type_iva"] ?? "");
    $x = CData::getById($receiptName[0] ?? 0);
    $x->de = ($x->de + 1);
    $x->update2();
}


$car_id = $_POST["car_id"];
$bokg = BookingData::getAllBySQL("where car_id=".$car_id);

$found = false;
foreach($bokg as $bk) {
    $start_at = date("Y-m-d",strtotime($_POST["start_at"]));
    $start_bk = date("Y-m-d",strtotime($bk->start_at));
    if($_POST["type_id"]==1):
        $end_at = date("Y-m-d",strtotime($_POST["end_at"]));
    else:
        $end_at = date("Y-m-d",strtotime($_POST["selectdate"]));
    endif;
    $end_bk = date("Y-m-d",strtotime($bk->end_at));

    if ((($start_at==$start_bk) || ($end_at==$end_bk)) and $bk->car_id==$car_id and $bk->status==1) {
        $found = true;
        break; // No sigas buscando, ya hay conflicto
    }
}

if($found==false) {
if (!empty($_POST["car_id"])) {   
    
$cars = CarsData::getById($_POST["car_id"]);


if(intval($_POST["nuevo_cliente_activo"] ?? 0)>0):
        

 
// Ruta subida
$uploadPath = "CF-SYSTEMS/storage/invoice_files/"; 


$location = LocationData::getById($_POST["location"]);
  

if (!function_exists('generarMatricula')) {
    function generarMatricula($nombreCliente, $telefonoRaw) {
        $prefijo = generarPrefijo($nombreCliente);
        $telefono = limpiarTelefono($telefonoRaw);
        return $prefijo . $telefono;
    }
}

if (!function_exists('generarPrefijo')) {
    function generarPrefijo($nombre) {
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT', (string)$nombre);
        if ($ascii === false) { $ascii = (string)$nombre; }
        $nombre_limpio = strtoupper(preg_replace('/[^A-Z]/', '', $ascii));
        $palabras = explode(" ", $nombre_limpio);
        $prefijo = "";
        foreach ($palabras as $p) {
            if (strlen($p) > 0 && strlen($prefijo) < 3) $prefijo .= $p[0];
        }
        if (strlen($prefijo) < 2) $prefijo .= substr($nombre_limpio, 0, 3 - strlen($prefijo));
        return strtoupper($prefijo) . "-";
    }
}

if (!function_exists('limpiarTelefono')) {
    function limpiarTelefono($telefono) {
        return preg_replace('/\D/', '', (string)$telefono);
    }
}


$nombreCliente = StockData::getPrincipal()->name;
$telefonoIngresado = $_POST["phone"] ?? ""; // O viene de $_POST["phone"]


// Generar una matrícula
$matriculaGenerada = generarMatricula($nombreCliente, $telefonoIngresado);
  
 
$user = new PersonData();
  $user->name = $_POST["name"] ?? "";
  $user->no = $_POST["no"] ?? "";
  $user->rnc = $_POST["rnc"] ?? "";
  $user->language = $_POST["language"] ?? "";
  $user->birthday = $_POST["birthday"] ?? "";
  $user->gender = $_POST["gender"] ?? "";
  $user->username = $matriculaGenerada;
  $user->password =  $user->password = sha1(md5($matriculaGenerada));
  $user->reference = $_POST["reference"] ?? "";
  $user->location = $_POST["location"] ?? "";
  $user->longitud = $location->longitud ?? "";
  $user->latitud = $location->latitud ?? "";
  $user->license = $_POST["license"] ?? "";
  $user->email = $_POST["email"] ?? "";
  $user->expirelicense = $_POST["expirelicense"] ?? "";
  $user->issuedlicense = $_POST["issuedlicense"] ?? "";
  $user->phone = $_POST["phone"] ?? "";
  $user->phone2 = $_POST["phone2"] ?? "";
  $user->passport = $_POST["passport"] ?? "";
  $user->nationality = $_POST["nationality"] ?? "";
  $user->address = $_POST["address"] ?? "";
  $user->address2 = $_POST["address2"] ?? "";
  $user->user_id = $_SESSION["user_id"] ?? 0;
  $user->invoice_date = $_POST["invoice_date"] ?? "";
  $user->passport_date = $_POST["passport_date"] ?? "";
  $user->license_date = $_POST["license_date"] ?? "";
  $user->home_date = $_POST["home_date"] ?? "";
  
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

        
    // aqui sigue igual que los demas metodo

    $user = new BookingData();
    $user->start_at   = $_POST["start_at"];
    $user->payment_day = $_POST["payment_day"];
    $user->type_id     = $_POST["type_id"];

    if($_POST["type_id"] == 1){
    $user->end_at = $_POST["end_at"];
    }else{
    $user->end_at = $_POST["selectdate"];
    }

    $user->place_start = pickPlace84("place_start2", "place_start");
    $user->place_end   = pickPlace84("place_end2", "place_end");

    $user->person_id = $id_person;
    $user->person2_id = ($_POST["person2_id"] > 0) ? $_POST["person2_id"] : 0;

    $user->location = $_POST["location"] ?? "";
    $user->stock_id = StockData::getPrincipal()->id;
    $user->type_sure = $_POST["type_sure"];
    $user->sure      = $_POST["sure"];
    $user->fuel      = $_POST["fuel"];
    $user->car_id    = $car_id;
    $user->car2_id   = ($_POST["car2_id"] > 0) ? $_POST["car2_id"] : 0;

    $user->type = 1;

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
    if($_POST["f_id"] == 3){
    $user->card = $card * (StockData::getPrincipal()->card / 100);
    } else {
    $user->card = 0;
    }

    // =============================
    // ✅ TOTAL CORRECTO
    // =============================
    $total = ($price2*$_POST["day"]) + $xtotal + $plane + $card + $iva;
    $user->total = $total;

    // =================================
    // ✅ RESTO DE CAMPOS
    // =================================
    $user->payment = intval(str_replace(",", "", $_POST["payment"]));
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

    // ❌ ERROR QUE TENÍAS: estabas copiando extra3 a extra4
    $user->unit_extra4  = $_POST["unit_extra4"];
    $user->price_extra4 = $_POST["price_extra4"];

    // IVA - comprobante
    $receiptIdAndName = explode("-", $_POST["type_iva"] ?? "");
    $user->iva        = $_POST["iva"] ?? 0;
    $user->value_iva = toNumber84($_POST["value_iva"] ?? 0);
    $user->type_iva   = $receiptIdAndName[0] ?? 0;
    $user->number_iva = ($receiptIdAndName[1] ?? "") . "" . ($receiptIdAndName[2] ?? "");

    $user->user_id = $_SESSION["user_id"] ?? 0;
    $user->plane   = $_POST["plane"];
    $user->status  = 0;

    $_notif_addRes2 = $user->add();

	$_notif_bookingId2 = (is_array($_notif_addRes2) && isset($_notif_addRes2[1])) ? intval($_notif_addRes2[1]) : 0;
	$_notif_stockId2 = intval(StockData::getPrincipal()->id);
	$_notif_personObj2 = PersonData::getById($id_person);
	$_notif_pname2 = isset($_notif_personObj2->name) ? $_notif_personObj2->name : '';
	NotificationService::notifyStockUsers($_notif_stockId2, NotificationService::EVENT_BOOKING_CREATED,
		'Nueva reserva creada', 'Cliente: '.htmlspecialchars($_notif_pname2).' — Reserva #'.$_notif_bookingId2,
		['booking_id' => $_notif_bookingId2, 'url' => './?view=booking&opt=modal&id='.$_notif_bookingId2]);
	if(intval($id_person) > 0){
		NotificationService::notify('client', intval($id_person), NotificationService::EVENT_BOOKING_CREATED,
			'Tu reserva fue creada', 'Hemos registrado tu reserva #'.$_notif_bookingId2.'. Gracias.',
			['booking_id' => $_notif_bookingId2, 'stock_id' => $_notif_stockId2]);
	}

	$persxn = PersonData::getById($id_person);
	
	$kmx = CarsData::getById($_POST["car_id"]);
	$kmx->status = 1;
	$kmx->update_status();

    $id_speds = BookingData::getAllByID();
    $spends = $id_speds[0]->id!=null?$id_speds[0]->id:0;
    
                $payment = new PaymentData();
			 	$payment->sell_id = $spends;
			 	$payment->val = $total;
                $payment->user_id = $_SESSION["user_id"];
                $payment->stock_id = StockData::getPrincipal()->id;
			 	$payment->person_id=$id_person;
                $payment->is_stock = 0;
			 	$payment->add();

				if($_POST["payment"]>0):
					$payment2 = new PaymentData();
			 		$payment2->sell_id = $spends;
				 	$payment2->val = -1*$_POST["payment"];
				 	$payment2->user_id = $_SESSION["user_id"];
                    $payment2->stock_id = StockData::getPrincipal()->id;
				 	$payment2->person_id=$id_person;
				 	$payment2->is_stock = 0;
				 	$payment2->add_payment();			 	
endif;			 	


header('location:./?view=booking&opt=modal&id='.$spends);    

          
}else{
header('location:./?view=booking&opt=earring');    
}

}

endif;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
elseif(isset($_GET["opt"]) && $_GET["opt"]=="earring"):

    if(!isset($_POST["user_id"]) || intval($_POST["user_id"]) <= 0){
        Core::redir("./?view=web&opt=all");
        exit;
    }

    $booking_id = intval($_POST["user_id"]);

    $bkg = BookingData::getById($booking_id);

    if(!$bkg){
        Core::redir("./?view=web&opt=all");
        exit;
    }

    $person_id = isset($_POST["person_id"]) ? intval($_POST["person_id"]) : 0;
    $person2_id = isset($_POST["person2_id"]) && $_POST["person2_id"] != "" ? intval($_POST["person2_id"]) : 0;
    $car_id = isset($_POST["car_id"]) ? intval($_POST["car_id"]) : 0;

    $start_at = isset($_POST["start_at"]) ? $_POST["start_at"] : "";
    $end_at   = isset($_POST["end_at"]) ? $_POST["end_at"] : "";

    $day = isset($_POST["day"]) ? floatval($_POST["day"]) : 0;
    $price = isset($_POST["price2"]) ? floatval($_POST["price2"]) : 0;
    $total = isset($_POST["total"]) ? floatval($_POST["total"]) : 0;
    $payment_val = isset($_POST["payment"]) ? floatval($_POST["payment"]) : 0;

    $xtotal = isset($_POST["xtotal"]) ? floatval($_POST["xtotal"]) : 0;
    $plane = isset($_POST["plane"]) ? floatval($_POST["plane"]) : 0;
    $sure = isset($_POST["sure"]) ? floatval($_POST["sure"]) : 0;
    $deposit = isset($_POST["deposit"]) ? floatval($_POST["deposit"]) : 0;

    $type_sure = isset($_POST["type_sure"]) ? intval($_POST["type_sure"]) : 0;
    $f_id = isset($_POST["f_id"]) ? intval($_POST["f_id"]) : 0;
    $fuel = isset($_POST["fuel"]) ? $_POST["fuel"] : "R";

    $place_start = "";
    $place_end = "";

    if(isset($_POST["place_start2"]) && trim($_POST["place_start2"]) != ""){
        $place_start = trim($_POST["place_start2"]);
    }elseif(isset($_POST["place_start"])){
        $place_start = trim($_POST["place_start"]);
    }

    if(isset($_POST["place_end2"]) && trim($_POST["place_end2"]) != ""){
        $place_end = trim($_POST["place_end2"]);
    }elseif(isset($_POST["place_end"])){
        $place_end = trim($_POST["place_end"]);
    }

    $unit_carseat = isset($_POST["unit_carseat"]) ? intval($_POST["unit_carseat"]) : 0;
    $price_carseat = isset($_POST["price_carseat"]) ? floatval($_POST["price_carseat"]) : 0;

    $unit_wifi = isset($_POST["unit_wifi"]) ? intval($_POST["unit_wifi"]) : 0;
    $price_wifi = isset($_POST["price_wifi"]) ? floatval($_POST["price_wifi"]) : 0;

    $unit_trailer = isset($_POST["unit_trailer"]) ? intval($_POST["unit_trailer"]) : 0;
    $price_trailer = isset($_POST["price_trailer"]) ? floatval($_POST["price_trailer"]) : 0;

    $iva = isset($_POST["iva"]) ? StockData::getPrincipal()->imp_val : 0;
    $card = StockData::getPrincipal()->card;

    if($total <= 0){
        $total = ($price * $day) + $xtotal + $plane;
    }

    $bkg->person_id = $person_id;
    $bkg->person2_id = $person2_id;
    $bkg->car_id = $car_id;
    $bkg->start_at = $start_at;
    $bkg->end_at = $end_at;
    $bkg->day = $day;
    $bkg->price = $price;
    $bkg->total = $total;
    $bkg->xtotal = $xtotal;
    $bkg->plane = $plane;
    $bkg->sure = $sure;
    $bkg->deposit = $deposit;
    $bkg->type_sure = $type_sure;
    $bkg->f_id = $f_id;
    $bkg->fuel = $fuel;
    $bkg->place_start = $place_start;
    $bkg->place_end = $place_end;
    $bkg->unit_carseat = $unit_carseat;
    $bkg->price_carseat = $price_carseat;
    $bkg->unit_wifi = $unit_wifi;
    $bkg->price_wifi = $price_wifi;
    $bkg->unit_trailer = $unit_trailer;
    $bkg->price_trailer = $price_trailer;
    $bkg->iva = $iva;
    $bkg->card = $card;
    $bkg->status = 1;
    $bkg->type = 1;
    $bkg->update_process();
    
    $spends = $booking_id;

    $payment = new PaymentData();
    $payment->sell_id = $spends;
    $payment->val = $total;
    $payment->user_id = $_SESSION["user_id"];
    $payment->stock_id = StockData::getPrincipal()->id;
    $payment->person_id = $person_id;
    $payment->is_stock = 0;
    $payment->add();

    if($payment_val > 0){
        $payment2 = new PaymentData();
        $payment2->sell_id = $spends;
        $payment2->val = -1 * $payment_val;
        $payment2->user_id = $_SESSION["user_id"];
        $payment2->stock_id = StockData::getPrincipal()->id;
        $payment2->person_id = $person_id;
        $payment2->is_stock = 0;
        $payment2->add_payment();
    }

    if($car_id > 0){
        $xcar = CarsData::getById($car_id);

        if($xcar){
            $xcar->status = 1;
            $xcar->update_status();
        }
    }

    $user = new ACData();
    $user->user_id = $_SESSION["user_id"] ?? 0;
    $user->accion = "Procesó reserva web pendiente #".$spends;
    $user->add();

    header('location:./?view=booking&opt=modal&id='.$spends);
    exit;
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
elseif(isset($_GET["opt"]) && $_GET["opt"]=="addcotiz"):

$car_id = $_POST["car_id"];
$bokg = BookingData::getAllBySQL("where car_id=".$car_id);
foreach($bokg as $bk):

$start_at = date("Y-m-d",strtotime($_POST["start_at"]));
$start_bk = date("Y-m-d",strtotime($bk->start_at));

$end_at = date("Y-m-d",strtotime($_POST["end_at"]));
$end_bk = date("Y-m-d",strtotime($bk->end_at));

if ((($start_at==$start_bk) || ($end_at==$end_bk)) and $bk->car_id==$car_id):
$found = true;
else:
$found = false;	
endif; endforeach;

if($found==false) {
    if($_POST["f_id"]==3): $card = ($_POST["total"]+$_POST["xtotal"])*(3/100); else: $card = 0; endif;
    if(isset($_POST["iva"])): $iva = ($_POST["total"]+$_POST["xtotal"])*(18/100); else: $iva = 0; endif;
  
	if($bk->provider_price>0):
	   $user = new BookingData();
	$user->start_at = $_POST["start_at"];
	$user->end_at = $_POST["end_at"];
	$user->place_start = pickPlace84("place_start2", "place_start");
	$user->place_end   = pickPlace84("place_end2", "place_end");
	$user->person_id = $_POST["person_id"];
	$user->person2_id = $_POST["person2_id"];
	$user->location = $_POST["location"] ?? "";
	$user->stock_id = StockData::getPrincipal()->id;
	$user->type_sure = $_POST["type_sure"];
	$user->sure = $_POST["sure"];
	$user->fuel = $_POST["fuel"];
	$user->f_id = $_POST["f_id"];
	$user->card = $_POST["card"];
	$user->unit_carseat = $_POST["unit_carseat"];
	$user->price_carseat = $_POST["price_carseat"];
	$user->unit_wifi = $_POST["unit_wifi"];
	$user->price_wifi = $_POST["price_wifi"];
	$user->unit_trailer = $_POST["unit_trailer"];
	$user->price_trailer = $_POST["price_trailer"];
	$user->iva = $iva;
	$user->car_id = $_POST["car_id"];
	$user->car2_id = $_POST["car2_id"];
	if ($_POST["car2_id"]=="0") {$user->type = 1;}else{$user->type = 2;}
	if ($_POST["price"]>0) {$user->price = $_POST["price"];}
	else{
	$divisa = StockData::getPrincipal()->divisa;
	if ($_POST["divisa_id"]==1) {
	$user->price = intval(str_replace(",", "", $_POST["price2"]));
    
$price2 = floatval(str_replace(",", "", $_POST["price2"] ?? 0));
$xtotal = floatval(str_replace(",", "", $_POST["xtotal"] ?? 0));
$plane  = floatval(str_replace(",", "", $_POST["plane"] ?? 0));
$card   = floatval(str_replace(",", "", $_POST["card"] ?? 0));
$iva    = floatval(str_replace(",", "", $_POST["iva"] ?? 0));

$total = $price2 + $xtotal + $plane + $card + $iva;

	
	$user->total = $total;
	$user->payment = intval(str_replace(",", "", $_POST["payment"]));
	$user->xtotal = intval(str_replace(",", "", $_POST["xtotal"]));
	}else{
	$user->price = intval(str_replace(",", "", $_POST["price2"]/$divisa));
	$user->total = intval(str_replace(",", "", ((($_POST["total"]+$_POST["xtotal"]+$_POST["plane"])+($card+$iva))/$divisa)));
	$user->payment = intval(str_replace(",", "", ($_POST["payment"]/$divisa)));
	$user->xtotal = intval(str_replace(",", "", $_POST["xtotal"]));
	}
	
	}
	$user->day = $_POST["day"];
	$user->deposit = $_POST["deposit"];
    $user->user_id = $_SESSION["user_id"] ?? 0;
	$user->plane = $_POST["plane"];
	$user->price_stock = $bk->provider_price;
	$user->add_booking();
	
	 $id_speds = BookingData::getAllByID();
    $spends = $id_speds[0]->id!=null?$id_speds[0]->id:0;
                $payment = new PaymentData();
			 	$payment->sell_id = $spends;
			 	$payment->val = $total;
			 	$payment->user_id = $_SESSION["user_id"];
                $payment->stock_id = StockData::getPrincipal()->id;
			 	$payment->person_id=$_POST["person_id"];
			 	$payment->is_stock = 0;
			 	$payment->add();
 if($_POST["payment"]>0):
					$payment2 = new PaymentData();
			 		$payment2->sell_id = $spends;
				 	$payment2->val = -1*$_POST["payment"];
				 	$payment2->user_id = $_SESSION["user_id"];
                    $payment2->stock_id = StockData::getPrincipal()->id;
				 	$payment2->person_id=$_POST["person_id"];
                    $payment2->is_stock = 0;
				 	$payment2->add_payment();			 	
endif;	
	
	$stoxk = StockData::getPrincipal();
	$persxn = PersonData::getById($_POST["person_id"]);
	$carx = CarsData::getById($_POST["car_id"]);

    $base = new Database();
$con = $base->connect();
$sql = "select name from person where name=\"".StockData::getPrincipal()->name."\" and stock_id=".$_POST["stock_id"];
$query = $con->query($sql);
$found = false;
while($r = $query->fetch_array()){
	$found = true ;
}


if($found==false) {
$user = new PersonData();
	$user->name = StockData::getPrincipal()->name;
	$user->phone = StockData::getPrincipal()->phone;
	$user->address = StockData::getPrincipal()->address;
	$user->no = StockData::getPrincipal()->no;
	$user->stock_id = $_POST["stock_id"];
	$user->is_rental = 1;
	$user->add_ext();

}

foreach(PersonData::getAllBySQL("where name='".StockData::getPrincipal()->name."' and stock_id=".$_POST["stock_id"]) as $key):
$user = PersonData::getById($key->id);
$person_id=$key->id; 	
endforeach;
  
	$user = new BookingData();
	$user->start_at = $_POST["start_at"];
	$user->end_at = $_POST["end_at"];
	$user->place_start = pickPlace84("place_start2", "place_start");
	$user->place_end   = pickPlace84("place_end2", "place_end");
	$user->person_id = $person_id;
	$user->person2_id = $_POST["person2_id"];
	$user->location = $_POST["location"] ?? "";
	$user->stock_id = $_POST["stock_id"];
	$user->type_sure = $_POST["type_sure"];
	$user->sure = $_POST["sure"];
	$user->fuel = $_POST["fuel"];
	$user->f_id = $_POST["f_id"];
	$user->card = $_POST["card"];
	$user->unit_carseat = $_POST["unit_carseat"];
	$user->price_carseat = $_POST["price_carseat"];
	$user->unit_wifi = $_POST["unit_wifi"];
	$user->price_wifi = $_POST["price_wifi"];
	$user->unit_trailer = $_POST["unit_trailer"];
	$user->price_trailer = $_POST["price_trailer"];
	$user->iva = $iva;
	$user->car_id = $_POST["car_id"];
	$user->car2_id = $_POST["car2_id"];
	if ($_POST["car2_id"]=="0") {$user->type = 1;}else{$user->type = 2;}
	if ($_POST["price"]>0) {$user->price = $_POST["price"];}else{
	$user->price = intval(str_replace(",", "", $bk->provider_price));
	$user->total = intval(str_replace(",", "", $bk->provider_price*$_POST["day"]));
	$user->xtotal = 0;
	$user->payment =0;
	}
	$user->day = $_POST["day"];
	$user->deposit = 0;
    $user->user_id = $_SESSION["user_id"] ?? 0;
	$user->plane = $_POST["plane"];
	$user->price_stock = $bk->provider_price;
	$user->add_booking();
	
	else:
	    
	  $user = new BookingData();
	$user->start_at = $_POST["start_at"];
	$user->end_at = $_POST["end_at"];
	$user->place_start = pickPlace84("place_start2", "place_start");
	$user->place_end   = pickPlace84("place_end2", "place_end");
	$user->person_id = $_POST["person_id"];
	$user->person2_id = $_POST["person2_id"];
	$user->location = $_POST["location"] ?? "";
	$user->stock_id = StockData::getPrincipal()->id;
	$user->type_sure = $_POST["type_sure"];
	$user->fuel = $_POST["fuel"];
	$user->unit_carseat = $_POST["unit_carseat"];
	$user->price_carseat = $_POST["price_carseat"];
	$user->unit_wifi = $_POST["unit_wifi"];
	$user->price_wifi = $_POST["price_wifi"];
	$user->unit_trailer = $_POST["unit_trailer"];
	$user->price_trailer = $_POST["price_trailer"];
	$user->iva = $iva;
	$user->sure = $_POST["sure"];
	$user->f_id = $_POST["f_id"];
	$user->card = $_POST["card"];
	$user->car_id = $_POST["car_id"];
	$user->car2_id = $_POST["car2_id"];
	if ($_POST["car2_id"]=="0") {$user->type = 1;}else{$user->type = 2;}
	if ($_POST["price"]>0) {$user->price = $_POST["price"];}
	else{
	$user->price = intval(str_replace(",", "", $_POST["price2"]));
	
$price2 = floatval(str_replace(",", "", $_POST["price2"] ?? 0));
$xtotal = floatval(str_replace(",", "", $_POST["xtotal"] ?? 0));
$plane  = floatval(str_replace(",", "", $_POST["plane"] ?? 0));
$card   = floatval(str_replace(",", "", $_POST["card"] ?? 0));
$iva    = floatval(str_replace(",", "", $_POST["iva"] ?? 0));

$total = $price2 + $xtotal + $plane + $card + $iva;

	
	$user->total = $total;
	$user->xtotal = intval(str_replace(",", "", $_POST["xtotal"]));
	$user->payment = intval(str_replace(",", "", $_POST["payment"]));
	}
	$user->day = $_POST["day"];
	$user->deposit = $_POST["deposit"];
    $user->user_id = $_SESSION["user_id"] ?? 0;
	$user->plane = $_POST["plane"];
	$user->add_booking();
	
	
	 $id_speds = BookingData::getAllByID();
    $spends = $id_speds[0]->id!=null?$id_speds[0]->id:0;
                $payment = new PaymentData();
			 	$payment->sell_id = $spends;
			 	$payment->val = $total;
			 	$payment->user_id = $_SESSION["user_id"];
                $payment->stock_id = StockData::getPrincipal()->id;
			 	$payment->person_id=$_POST["person_id"];
			 	$payment->add();
 if($_POST["payment"]>0):
					$payment2 = new PaymentData();
			 		$payment2->sell_id = $spends;
				 	$payment2->val = -1*$_POST["payment"];
				 	$payment2->user_id = $_SESSION["user_id"];
                    $payment2->stock_id = StockData::getPrincipal()->id;
				 	$payment2->person_id=$_POST["person_id"];
				 	$payment2->add_payment();			 	
endif;	
	
	endif;
	
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"] ?? 0;
          $user->accion = "Agrego la reserva";
          $user->add();
          

$xcar = CarsData::getById($_POST["car_id"]);
$xcar->status = 1;
$xcar->update_status();

$operation = OperationData::getById($_POST["cotiz_id"]);
$operation->del();
        
echo 'true';
}else{
echo 'false'; 
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
elseif(isset($_GET["opt"]) && $_GET["opt"]=="delivery"):

	$bkg = BookingData::getById($_POST["user_id"]);
	$bkg->status = 1;
	$bkg->update_status();

	$kmx = CarsData::getById($bkg->car_id);
	$kmx->status = 2;
	$kmx->update_status();

	$_notif_bp = PersonData::getById($bkg->person_id);
	$_notif_bn = isset($_notif_bp->name) ? $_notif_bp->name : '';
	NotificationService::notifyStockUsers(intval($bkg->stock_id), NotificationService::EVENT_BOOKING_DELIVERED,
		'Vehículo entregado', 'Reserva #'.intval($bkg->id).' entregada a '.htmlspecialchars($_notif_bn),
		['booking_id' => intval($bkg->id), 'url' => './?view=booking&opt=modal&id='.intval($bkg->id)]);
	if(intval($bkg->person_id) > 0){
		NotificationService::notify('client', intval($bkg->person_id), NotificationService::EVENT_BOOKING_DELIVERED,
			'Tu vehículo fue entregado', 'Tu reserva #'.intval($bkg->id).' está activa. ¡Disfruta tu viaje!',
			['booking_id' => intval($bkg->id), 'stock_id' => intval($bkg->stock_id)]);
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

if (is_null($bkg->firma) || $bkg->firma === ''):

$img = $_POST["base64"];
$img = str_replace('data:image/png;base64,', '', $img);
$fileData = base64_decode($img);
$fileName = "firmas/".uniqid().'.png';

file_put_contents($fileName, $fileData);

$xuser = BookingData::getById($_POST["user_id"]);
$xuser->firma = $fileName;
$xuser->update_firma();

else:
$fileName = $bkg->firma;  
endif;

$xuser = BookingData::getById($_POST["user_id"]);
$cars_id = CarsData::getById($xuser->car_id);

if($cars_id->provider_price>0):

    $payment2 = new PaymentData();
    $payment2->sell_id = $xuser->id;
    $payment2->val = ($cars_id->provider_price*$xuser->day);
    $payment2->user_id = $_SESSION["user_id"];
    $payment2->stock_id = StockData::getPrincipal()->id;
    $payment2->person_id = $cars_id->provider_id;
    $payment2->is_stock = 1;
    $payment2->add();
    
endif;

	$user = new DeliveryData();
	$user->firma = $fileName;
	$user->danger = $danger;
	$user->method = 2;
	$user->kms = $kmx->kms;
	$user->fuel = $bkg->fuel;
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
	$user->car_id = $bkg->car_id;
	$user->booking_id = $_POST["user_id"];
    $user->user_id = $bkg->user_id;
    $user->receiver_id = 0;
    $user->delivery_id = $_SESSION["user_id"];
	$user->add();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"] ?? 0;
          $user->accion = "Verifico la reserva al salir";
          $user->add();


header('location:./?view=contract&opt=modal&id='.$_POST["user_id"]);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"):
$q = BookingData::getById($_GET["id"]);

$user = CarsData::getById($q->car_id);

if($user->status == 2):
	$user->status = 2;
else:
    $user->status = 0;
endif;
	$user->update_status();
	
foreach(PaymentData::getAllBySQL("where booking_id=".$_GET["id"]) as $payment):
$payment->del();
endforeach;

$q->del();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"] ?? 0;
          $user->accion = "Elimino la reserva";
          $user->add();
          
header('location:./?view=booking&opt=all');
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
elseif(isset($_GET["opt"]) && $_GET["opt"]=="updrandom"):

	// =========================
	// Helpers (NO ROMPEN NADA)
	// =========================
	if (!function_exists("toNumber")) {
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
	$k->place_start = pickPlace84("place_start2", "place_start");
	$k->place_end   = pickPlace84("place_end2", "place_end");

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


	header('location:./?view=booking&opt=modal&id='.$k->id);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
elseif(isset($_GET["opt"]) && $_GET["opt"]=="what"):

    $stoxk  = StockData::getPrincipal();
    $persxn = PersonData::getById($_GET["person_id"]);
    $sell   = BookingData::getById($_GET["id"]);
    $carx   = CarsData::getById($_GET["car_id"]);


    /* =========================
       FUNCION NORMALIZAR TELEFONO
       - Reconoce: 8006566561 / (781) 469-2878 / +1 (781)...
       - Deja solo dígitos
       - Si son 10 dígitos, agrega "1" delante
       - Si son más de 11, toma los últimos 11
       ========================= */
    if (!function_exists("normalizarTelefono")) {
    function normalizarTelefono($telefono){
        $telefono = preg_replace('/\D/', '', (string)$telefono);

        if(strlen($telefono) == 10){
            $telefono = "1".$telefono;
        }

        if(strlen($telefono) > 11){
            $telefono = substr($telefono, -11);
        }

        return $telefono;
    }
}


    /* =========================
       ELEGIR TELEFONO: phone o phone2
       ========================= */
    $telefonoBase = !empty($persxn->phone) ? $persxn->phone : $persxn->phone2;
    $telefonoLimpio = normalizarTelefono($telefonoBase);

    if(empty($telefonoLimpio)){
        die("El cliente no tiene teléfono registrado (phone / phone2).");
    }


    /* =========================
       CREAR USERNAME = TELEFONO (si username está vacío)
       ========================= */
    if(empty($persxn->username)):

        $xperson = PersonData::getById($_GET["person_id"]);

        $telefonoBaseUsername = !empty($xperson->phone) ? $xperson->phone : $xperson->phone2;
        $telefonoUsername = normalizarTelefono($telefonoBaseUsername);

        if(!empty($telefonoUsername)){
            $xperson->username = $telefonoUsername;
            $xperson->password = sha1(md5($telefonoUsername));
            $xperson->update_username();
        }

    endif;


    /* =========================
       DATOS DE RESERVA
       ========================= */
    $nombre = strtoupper($persxn->name);
    $correo = strtoupper($persxn->email);
    $person_phone = strtoupper($telefonoBase); // muestra el original (phone o phone2)

    $day    = $sell->day;
    $precio = $sell->price;
    $xtotal = $sell->xtotal;
    $plane  = $sell->plane;
    $card   = $sell->card;
    $iva    = $sell->iva;

    $total = $precio + $xtotal + $plane + $card + $iva;

    $entregar = strtoupper($sell->place_start);
    $recibir  = strtoupper($sell->place_end);

    $inicio  = date("d-m-Y h:i a", strtotime($sell->start_at));
    $termino = date("d-m-Y h:i a", strtotime($sell->end_at));

    $carro = strtoupper($carx->getBrand()->name." ".$carx->name." ".$carx->year);
    $total_total = number_format($total, 2, ".", ",");


    /* =========================
       URL PARA FIRMAR
       ========================= */
    $web_url = "https://rentals.assanpos.com/?username=".$telefonoLimpio."&password=".$telefonoLimpio;


    /* =========================
       TELEFONO PARA WHATSAPP (solo números)
       ========================= */
    $telefono = $telefonoLimpio;


    /* =========================
       TEXTO SEGUN IDIOMA
       ========================= */
    switch ($persxn->language){

        case 'ES':
            $texto = "DETALLE DE LA RESERVACIÓN%0A%0A".
            "NOMBRE DEL CLIENTE: ".$nombre."%0A".
            "CORREO: ".$correo."%0A".
            "TELEFONO: ".$person_phone."%0A".
            "FECHA INICIO: ".$inicio."%0A".
            "FECHA TERMINO: ".$termino."%0A%0A".
            "VEHICULO: ".$carro."%0A".
            "PRECIO: ".$precio." ".StockData::getPrincipal()->currency."%0A".
            "DIA: ".$day."%0A".
            "TOTAL: ".$total_total." ".StockData::getPrincipal()->currency."%0A".
            "ENTREGAR AL CLIENTE: ".$entregar."%0A".
            "RECIBIR DEL CLIENTE: ".$recibir."%0A%0A".
            "FAVOR FIRMAR AQUI PARA CONFIRMAR LA RESERVA:%0A%0A".
            "URL: ".$web_url;
        break;

        case 'EN':
            $texto = "RESERVATION DETAILS%0A%0A".
            "CUSTOMER NAME: ".$nombre."%0A".
            "EMAIL: ".$correo."%0A".
            "PHONE: ".$person_phone."%0A".
            "START DATE: ".$inicio."%0A".
            "END DATE: ".$termino."%0A%0A".
            "VEHICLE: ".$carro."%0A".
            "PRICE: ".$precio." ".StockData::getPrincipal()->currency."%0A".
            "DAY: ".$day."%0A".
            "TOTAL: ".$total_total." ".StockData::getPrincipal()->currency."%0A".
            "DELIVERY TO THE CUSTOMER: ".$entregar."%0A".
            "RECEIVE FROM CUSTOMER: ".$recibir."%0A%0A".
            "PLEASE SIGN HERE TO CONFIRM YOUR RESERVATION:%0A%0A".
            "URL: ".$web_url;
        break;

        default:
            // si no tiene idioma, cae a ES por defecto
            $texto = "DETALLE DE LA RESERVACIÓN%0A%0A".
            "NOMBRE DEL CLIENTE: ".$nombre."%0A".
            "CORREO: ".$correo."%0A".
            "TELEFONO: ".$person_phone."%0A".
            "FECHA INICIO: ".$inicio."%0A".
            "FECHA TERMINO: ".$termino."%0A%0A".
            "VEHICULO: ".$carro."%0A".
            "PRECIO: ".$precio." ".StockData::getPrincipal()->currency."%0A".
            "DIA: ".$day."%0A".
            "TOTAL: ".$total_total." ".StockData::getPrincipal()->currency."%0A".
            "ENTREGAR AL CLIENTE: ".$entregar."%0A".
            "RECIBIR DEL CLIENTE: ".$recibir."%0A%0A".
            "FAVOR FIRMAR AQUI PARA CONFIRMAR LA RESERVA:%0A%0A".
            "URL: ".$web_url;
        break;
    }


    /* =========================
       ENLACE WHATSAPP + REDIRECCION
       ========================= */
    $enlace = "https://api.whatsapp.com/send?phone=".$telefono."&text=".$texto;

    header("Location: ".$enlace);
    exit();
    
elseif(isset($_GET["opt"]) && $_GET["opt"]=="signature"):
    
$img = $_POST["base64"];
$img = str_replace('data:image/png;base64,', '', $img);
$fileData = base64_decode($img);
$fileName = "firmas/".uniqid().'.png';

file_put_contents($fileName, $fileData);

$xuser = BookingData::getById($_POST["user_id"]);
$xuser->firma = $fileName;
$xuser->update_firma();

header('location:./?view=home');   
    

endif;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>