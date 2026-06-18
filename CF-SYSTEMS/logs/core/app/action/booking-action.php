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

////////////////////////////////////////////////////////////////////////// DIFERENTE DE VEHICULO EXTERNO ///////////////////////////////////////////////////////////////////////////
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

if (!empty($car_id)):
    
  $user = new BookingData();
	$user->start_at = $_POST["start_at"];
	$user->payment_day = $_POST["payment_day"];
	$user->type_id = $_POST["type_id"];
	if($_POST["type_id"]==1):
	$user->end_at = $_POST["end_at"];
	elseif($_POST["type_id"]==2):
	$user->end_at = $_POST["selectdate"];   
	endif;
	if($_POST["place_start2"]>0):
	$user->place_start = $_POST["place_start2"];
	else:
	$user->place_start = $_POST["place_start"];	    
	endif;
	if($_POST["place_end2"]>0):
	$user->place_end = $_POST["place_end2"];
	else:
	$user->place_end = $_POST["place_end"];	    
	endif;
	$user->person_id = $id_person;
	
	if($_POST["person2_id"]>0):
	$user->person2_id = $_POST["person2_id"];
	else:
	$user->person2_id = 0;
	endif;
    
    $user->location = $_POST["location"];
	$user->stock_id = StockData::getPrincipal()->id;
	$user->type_sure = $_POST["type_sure"];
	$user->sure = $_POST["sure"];
	$user->fuel = $_POST["fuel"];
	$user->car_id = $car_id;
	if($_POST["car2_id"]>0):
	$user->car2_id = $_POST["car2_id"];
	else:
	$user->car2_id = 0;
	endif;
	$user->type = 1;
	$user->price = intval(str_replace(",", "", $_POST["price2"]));
	if($_POST["f_id"]==3):
	$card = intval(str_replace(",", "", $_POST["total"]));
	$user->card = $card*(StockData::getPrincipal()->card/100);
	else:
	$user->card = 0;
	endif;
	$user->total = intval(str_replace(",", "", $_POST["total"]));
	$user->payment = intval(str_replace(",", "", $_POST["payment"]));
	$user->day = $_POST["day"];
	$user->deposit = 0;
	$user->f_id = $_POST["f_id"];
	$user->xtotal = $_POST["xtotal"];
	$user->unit_extra1 = $_POST["unit_extra1"];
	$user->price_extra1 = $_POST["price_extra1"];
	$user->unit_extra2 = $_POST["unit_extra2"];
	$user->price_extra2 = $_POST["price_extra2"];
	$user->unit_extra3 = $_POST["unit_extra3"];
	$user->price_extra3 = $_POST["price_extra3"];
	$user->unit_extra4 = $_POST["unit_extra3"];
	$user->price_extra4 = $_POST["price_extra3"];
    
    $receiptIdAndName = explode("-", $_POST["type_iva"]);

    $user->iva = $_POST["iva"];
    $user->value_iva = $_POST["value_iva"];
    $user->type_iva = $receiptIdAndName[0];
    $user->number_iva = $receiptIdAndName[1]."".$receiptIdAndName[2];
    
    $user->user_id = $_SESSION["user_id"];
	$user->plane = $_POST["plane"];
	$user->status = 0;
	$user->add();
	
	$persxn = PersonData::getById($id_person);
	
    $kmx = CarsData::getById($car_id);
	$kmx->status = 1;
	$kmx->update_status();

    $id_speds = BookingData::getAllByID();
    $spends = $id_speds[0]->id!=null?$id_speds[0]->id:0;
    
    $payment = new PaymentData();
    $payment->sell_id = $spends;
    $payment->val = $_POST["total"];
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
elseif ($_POST["method"]<>3):

    
if ($_POST["iva"] == 18) {
    $receiptName = explode("-", $_POST["type_iva"]);
    $x = CData::getById($receiptName[0]);
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


if($_POST["nuevo_cliente_activo"]>0):
        

 
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

    $id_persons = PersonData::getAllByID();
    $persons = $id_persons[0]->id!=null?$id_persons[0]->id:0;
    
    $id_person = $persons;
    else:
    
    $id_person = $_POST["person_id"];
        
    endif;

        
    $user = new BookingData();
	$user->start_at = $_POST["start_at"];
	$user->payment_day = $_POST["payment_day"];
	$user->type_id = $_POST["type_id"];
	if($_POST["type_id"]==1):
	$user->end_at = $_POST["end_at"];
	elseif($_POST["type_id"]==2):
	$user->end_at = $_POST["selectdate"];   
	endif;
	if($_POST["place_start2"]>0):
	$user->place_start = $_POST["place_start2"];
	else:
	$user->place_start = $_POST["place_start"];	    
	endif;
	if($_POST["place_end2"]>0):
	$user->place_end = $_POST["place_end2"];
	else:
	$user->place_end = $_POST["place_end"];	    
	endif;
	$user->person_id = $id_person;
	
	if($_POST["person2_id"]>0):
	$user->person2_id = $_POST["person2_id"];
	else:
	$user->person2_id = 0;
	endif;
    
    $user->location = $_POST["location"];
	$user->stock_id = StockData::getPrincipal()->id;
	$user->type_sure = $_POST["type_sure"];
	$user->sure = $_POST["sure"];
	$user->fuel = $_POST["fuel"];
	$user->car_id = $_POST["car_id"];
	if($_POST["car2_id"]>0):
	$user->car2_id = $_POST["car2_id"];
	else:
	$user->car2_id = 0;
	endif;
	$user->type = 1;
	$user->price = intval(str_replace(",", "", $_POST["price2"]));
	if($_POST["f_id"]==3):
	$card = intval(str_replace(",", "", $_POST["total"]));
	$user->card = $card*(StockData::getPrincipal()->card/100);
	else:
	$user->card = 0;
	endif;
	$user->total = intval(str_replace(",", "", $_POST["total"]));
	$user->payment = intval(str_replace(",", "", $_POST["payment"]));
	$user->day = $_POST["day"];
	$user->deposit = 0;
	$user->f_id = $_POST["f_id"];
	$user->xtotal = $_POST["xtotal"];
	$user->unit_extra1 = $_POST["unit_extra1"];
	$user->price_extra1 = $_POST["price_extra1"];
	$user->unit_extra2 = $_POST["unit_extra2"];
	$user->price_extra2 = $_POST["price_extra2"];
	$user->unit_extra3 = $_POST["unit_extra3"];
	$user->price_extra3 = $_POST["price_extra3"];
	$user->unit_extra4 = $_POST["unit_extra3"];
	$user->price_extra4 = $_POST["price_extra3"];
    
    $receiptIdAndName = explode("-", $_POST["type_iva"]);

    $user->iva = $_POST["iva"];
    $user->value_iva = $_POST["value_iva"];
    $user->type_iva = $receiptIdAndName[0];
    $user->number_iva = $receiptIdAndName[1]."".$receiptIdAndName[2];
    
    $user->user_id = $_SESSION["user_id"];
	$user->plane = $_POST["plane"];
	$user->status = 0;
	$user->add();
	
	$persxn = PersonData::getById($id_person);
	
    $kmx = CarsData::getById($_POST["car_id"]);
	$kmx->status = 1;
	$kmx->update_status();

    $id_speds = BookingData::getAllByID();
    $spends = $id_speds[0]->id!=null?$id_speds[0]->id:0;
    
                $payment = new PaymentData();
			 	$payment->sell_id = $spends;
			 	$payment->val = $_POST["total"];
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

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="earring"){

    if($_POST["f_id"]==3): $card = ($_POST["total"]+$_POST["xtotal"])*(3/100); else: $card = 0; endif;
    if(isset($_POST["iva"])): $iva = ($_POST["total"]+$_POST["xtotal"])*(18/100); else: $iva = 0; endif;

	$user = new BookingData();
	$user->start_at = $_POST["start_at"];
	$user->end_at = $_POST["end_at"];
	if($_POST["place_start2"]>0):
	$user->place_start = $_POST["place_start2"];
	else:
	$user->place_start = $_POST["place_start"];	    
	endif;
	if($_POST["place_end2"]>0):
	$user->place_end = $_POST["place_end2"];
	else:
	$user->place_end = $_POST["place_end"];	    
	endif;
	$user->person_id = $_POST["person_id"];
	$user->person2_id = $_POST["person2_id"];
	$user->location = $_POST["location"];
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
	$user->car2_id = 0;
	$user->type = 1;
	$user->price = intval(str_replace(",", "", $_POST["price2"]));
	$user->total = intval(str_replace(",", "", (($_POST["total"]+$_POST["xtotal"]+$_POST["plane"])+($card+$iva))));
	$user->xtotal = intval(str_replace(",", "", $_POST["xtotal"]));
	$user->payment = intval(str_replace(",", "", $_POST["payment"]));
	$user->day = $_POST["day"];
	$user->deposit = $_POST["deposit"];
    $user->user_id = $_SESSION["user_id"];
	$user->plane = $_POST["plane"];
	$user->add_booking();
	
	
	$id_speds = BookingData::getAllByID();
    $spends = $id_speds[0]->id!=null?$id_speds[0]->id:0;
    
                $payment = new PaymentData();
			 	$payment->sell_id = $spends;
			 	$payment->val = (($_POST["total"]+$_POST["xtotal"]+$_POST["plane"])+($card+$iva));
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

$xcar = CarsData::getById($_POST["car_id"]);
$xcar->status = 1;
$xcar->update_status();

$wait = WaitData::getById($_POST["user_id"]);
$wait->del();

$stoxk = StockData::getPrincipal();
	$persxn = PersonData::getById($_POST["person_id"]);
	$carx = CarsData::getById($_POST["car_id"]);
	
	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Agrego la reserva";
          $user->add();
         
          
header('location:./?view=booking&opt=modal&id='.$spends);    

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="addcotiz"){

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
	if($_POST["place_start2"]>0):
	$user->place_start = $_POST["place_start2"];
	else:
	$user->place_start = $_POST["place_start"];	    
	endif;
	if($_POST["place_end2"]>0):
	$user->place_end = $_POST["place_end2"];
	else:
	$user->place_end = $_POST["place_end"];	    
	endif;
	$user->person_id = $_POST["person_id"];
	$user->person2_id = $_POST["person2_id"];
	$user->location = $_POST["location"];
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
	$user->total = intval(str_replace(",", "", (($_POST["total"]+$_POST["xtotal"]+$_POST["plane"])+($card+$iva))));
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
    $user->user_id = $_SESSION["user_id"];
	$user->plane = $_POST["plane"];
	$user->price_stock = $bk->provider_price;
	$user->add_booking();
	
	 $id_speds = BookingData::getAllByID();
    $spends = $id_speds[0]->id!=null?$id_speds[0]->id:0;
                $payment = new PaymentData();
			 	$payment->sell_id = $spends;
			 	$payment->val = (($_POST["total"]+$_POST["xtotal"]+$_POST["plane"])+($card+$iva));
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
	if($_POST["place_start2"]>0):
	$user->place_start = $_POST["place_start2"];
	else:
	$user->place_start = $_POST["place_start"];	    
	endif;
	if($_POST["place_end2"]>0):
	$user->place_end = $_POST["place_end2"];
	else:
	$user->place_end = $_POST["place_end"];	    
	endif;
	$user->person_id = $person_id;
	$user->person2_id = $_POST["person2_id"];
	$user->location = $_POST["location"];
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
    $user->user_id = $_SESSION["user_id"];
	$user->plane = $_POST["plane"];
	$user->price_stock = $bk->provider_price;
	$user->add_booking();
	
	else:
	    
	  $user = new BookingData();
	$user->start_at = $_POST["start_at"];
	$user->end_at = $_POST["end_at"];
	if($_POST["place_start2"]>0):
	$user->place_start = $_POST["place_start2"];
	else:
	$user->place_start = $_POST["place_start"];	    
	endif;
	if($_POST["place_end2"]>0):
	$user->place_end = $_POST["place_end2"];
	else:
	$user->place_end = $_POST["place_end"];	    
	endif;
	$user->person_id = $_POST["person_id"];
	$user->person2_id = $_POST["person2_id"];
	$user->location = $_POST["location"];
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
	$user->total = intval(str_replace(",", "", (($_POST["total"]+$_POST["xtotal"]+$_POST["plane"])+($card+$iva))));
	$user->xtotal = intval(str_replace(",", "", $_POST["xtotal"]));
	$user->payment = intval(str_replace(",", "", $_POST["payment"]));
	
	}
	$user->day = $_POST["day"];
	$user->deposit = $_POST["deposit"];
    $user->user_id = $_SESSION["user_id"];
	$user->plane = $_POST["plane"];
	$user->add_booking();
	
	
	 $id_speds = BookingData::getAllByID();
    $spends = $id_speds[0]->id!=null?$id_speds[0]->id:0;
                $payment = new PaymentData();
			 	$payment->sell_id = $spends;
			 	$payment->val = (($_POST["total"]+$_POST["xtotal"]+$_POST["plane"])+($card+$iva));
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
          $user->user_id = $_SESSION["user_id"];
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


}elseif(isset($_GET["opt"]) && $_GET["opt"]=="delivery"){

	$bkg = BookingData::getById($_POST["user_id"]);
	$bkg->status = 1;
	$bkg->update_status();

	$kmx = CarsData::getById($bkg->car_id);
	$kmx->status = 2;
	$kmx->update_status();
	

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
	$user->fuel = $bk->fuel;
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
	$user->car_id = $bk->car_id;
	$user->booking_id = $_POST["user_id"];
    $user->user_id = $bk->user_id;
    $user->receiver_id = 0;
    $user->delivery_id = $_SESSION["user_id"];
	$user->add();

$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Verifico la reserva al salir";
          $user->add();


header('location:./?view=contract&opt=modal&id='.$_POST["user_id"]);

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="del"){
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
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Elimino la reserva";
          $user->add();
          
header('location:./?view=booking&opt=all');

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}elseif(isset($_GET["opt"]) && $_GET["opt"]=="updrandom"){

if($_POST["f_id"]==3): $card = ($_POST["total"]+$_POST["xtotal"])*(3/100); else: $card = 0; endif;
if($_POST["iva"]==18): $iva = ($_POST["total"]+$_POST["xtotal"])*(18/100); else: $iva = 0; endif;

	$bk = CarsData::getById($_POST["car_id"]);
	
   	$k = BookingData::getById($_POST["user_id"]);
	$k->person_id = $_POST["person_id"];
	$k->person2_id = $_POST["person2_id"];
	$k->start_at = $_POST["start_at"];
	$k->end_at = $_POST["end_at"];
	$k->type_sure = $_POST["type_sure"];
	$k->sure = $_POST["sure"];
	$k->f_id = $_POST["f_id"];
	$k->fuel = $_POST["fuel"];
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
	$k->card = $_POST["card"];
	$k->day = $_POST["day"];
	$k->car_id = $bk->id;
	$k->price_stock = $bk->provider_price;
	$k->stock_id = StockData::getPrincipal()->id;

	$k->price = intval(str_replace(",", "", $_POST["price2"]));
	$k->total = intval(str_replace(",", "", (($_POST["total"]+$_POST["xtotal"]+$_POST["plane"])+($card+$iva))));
	$k->xtotal = intval(str_replace(",", "", $_POST["xtotal"]));
	$k->payment = intval(str_replace(",", "", $_POST["payment"]));
	$k->update();


              foreach(PaymentData::getAllBySQL("where payment_type_id=1 and booking_id=".$_POST["user_id"]." order by created_at asc limit 1") as $payment2):
			  $payment2->val = $_POST["remaining"];
			  $payment2->update();	
              endforeach; 
              
              
                    if($_POST["payment"]>0):
					$payment2 = new PaymentData();
			 		$payment2->sell_id = $_POST["user_id"];
				 	$payment2->val = -1*$_POST["payment"];
				 	$payment2->user_id = $_SESSION["user_id"];
                    $payment2->stock_id = StockData::getPrincipal()->id;
				 	$payment2->person_id=$_POST["person_id"];
				 	$payment2->add_payment();			 	
                    endif;	
              
			
$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo un cambio en la reserva de # ".$user->person_id;
          $user->add();


header('location:./?view=booking&opt=modal&id='.$k->id);  
    
}else if(isset($_GET["opt"]) && $_GET["opt"]=="what"){

	$stoxk = StockData::getPrincipal();
	$persxn = PersonData::getById($_GET["person_id"]);
	$sell = BookingData::getById($_GET["id"]);
	$carx = CarsData::getById($_GET["car_id"]);
	
	
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
  $xperson->password =  sha1(md5($matriculaGenerada));
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
    $total_total =  number_format($precio*$day,2,".",",");
   
   function limpiarTelefono($telefono) {
    return preg_replace('/\D/', '', $telefono); // Elimina todo excepto dígitos
   }
 
   $telefonoCrudo = $persxn->phone; // Ej: "+1 (829) 674-1075"
   $telefonoLimpio = limpiarTelefono($telefonoCrudo);

   $web_url = "https://clients.assanpos.com/?username=" . $telefonoLimpio;
    
    // Número de teléfono (incluye el código del país, sin signos + o espacios)
    $telefono = preg_replace('/\D/', '', "+".$persxn->phone); // Reemplaza con tu número de WhatsApp
    
  switch ($persxn->language){
  case 'ES':  $texto = "DETALLE DE LA RESERVACIÓN"."%0A" ." ". "%0A" ."NOMBRE DEL CLIENTE: ".$nombre. "%0A" ."CORREO: ".$correo. "%0A" ."TELEFONO: ".$person_phone. "%0A" ."FECHA INICIO: ".$inicio. "%0A" ."FECHA TERMINO: ".$termino."%0A" ." ". "%0A" ."VEHICULO: ".$carro. "%0A" ."PRECIO: ".$precio." ".StockData::getPrincipal()->currency. "%0A" ."DIA: ".$day. "%0A" ."TOTAL: ".$total_total." ".StockData::getPrincipal()->currency. "%0A" ."ENTREGAR AL CLIENTE: ".$entregar. "%0A" ."RECIBIR DEL CLIENTE: ".$recibir."%0A" ." ". "%0A" ."FAVOR FIRMAR AQUI PARA CONFIRMAR LA RESERVA: "."%0A" ." ". "%0A"."URL: ".$web_url; 
  break;
  
  case 'EN':  $texto = "RESERVATION DETAILS"."%0A" ." ". "%0A" ."CUSTOMER NAME: ".$nombre. "%0A" ."EMAIL: ".$correo. "%0A" ."PHONE: ".$person_phone. "%0A" ."START DATE: ".$inicio. "%0A" ."END DATE: ".$termino."%0A" ." ". "%0A" ."VEHICLE: ".$carro. "%0A" ."PRICE: ".$precio." ".StockData::getPrincipal()->currency. "%0A" ."DAY: ".$day. "%0A" ."TOTAL: ".$total_total." ".StockData::getPrincipal()->currency. "%0A" ."DELIVERY TO THE CUSTOMER: ".$entregar. "%0A" ."RECEIVE FROM CUSTOMER: ".$recibir."%0A" ." ". "%0A" ."PLEASE SIGN HERE TO CONFIRM YOUR RESERVATION: "."%0A" ." ". "%0A"."URL: ".$web_url;
  break;
}
    
    // Crear el mensaje
   
    
    // Generar el enlace de WhatsApp
    $enlace = "https://api.whatsapp.com/send?phone=$telefono&text=$texto";
    
    // Redirigir al usuario a WhatsApp
    header("Location: $enlace");
    exit();
    
    

}

?>