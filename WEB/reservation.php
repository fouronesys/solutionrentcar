<?php
ob_start();
include "layout/header.php";

/*
|--------------------------------------------------------------------------
| /WEB/reservation.php
|--------------------------------------------------------------------------
*/

$base = new Database();
$con = $base->connect();

function web_clean($con, $value){
    return $con->real_escape_string(trim((string)($value ?? "")));
}

function web_digits($value){
    return preg_replace('/[^0-9]/', '', (string)$value);
}

function web_get_car_stock($con, $car_id){
    $car_id = intval($car_id);
    if($car_id <= 0){ return 1; }

    $q = $con->query("SELECT stock_id FROM cars WHERE id=$car_id LIMIT 1");
    if($q && $q->num_rows > 0){
        $r = $q->fetch_assoc();
        return intval($r["stock_id"] ?? 1) > 0 ? intval($r["stock_id"] ?? 1) : 1;
    }

    return 1;
}

function web_find_or_create_person($con, $stock_id, $fullname, $no, $passport, $phone){

    $stock_id = intval($stock_id);
    $fullname_clean = web_clean($con, $fullname);
    $no_clean       = web_clean($con, $no);
    $passport_clean = web_clean($con, $passport);
    $phone_clean    = web_clean($con, $phone);

    $where = [];

    if($fullname_clean != ""){
        $where[] = "LOWER(TRIM(name))='".strtolower($fullname_clean)."'";
    }

    if($no_clean != ""){
        $where[] = "no='$no_clean'";
    }

    if($passport_clean != ""){
        $where[] = "passport='$passport_clean'";
    }

    if(count($where) > 0){
        $sql = "
            SELECT id
            FROM person
            WHERE stock_id=$stock_id
            AND (".implode(" OR ", $where).")
            ORDER BY id DESC
            LIMIT 1
        ";

        $q = $con->query($sql);
        if($q && $q->num_rows > 0){
            $r = $q->fetch_assoc();
            return intval($r["id"]);
        }
    }

    $username = web_digits($phone_clean);

    if($username == ""){
        $username = "WEB".time();
    }

    $person = new PersonData();
    $person->name = $fullname_clean;
    $person->no = $no_clean;
    $person->passport = $passport_clean;
    $person->phone = $phone_clean;
    $person->phone2 = "";
    $person->email = "";
    $person->address = "";
    $person->address2 = "";
    $person->rnc = "";
    $person->language = "EN";
    $person->birthday = "";
    $person->gender = "";
    $person->username = $username;
    $person->password = sha1(md5($username));
    $person->reference = "WEB RESERVATION";
    $person->location = 0;
    $person->longitud = "";
    $person->latitud = "";
    $person->license = "";
    $person->expirelicense = "";
    $person->issuedlicense = "";
    $person->nationality = "";
    $person->user_id = 0;
    $person->invoice_date = "";
    $person->passport_date = "";
    $person->license_date = "";
    $person->home_date = "";
    $person->invoice_file = "";
    $person->passport_file = "";
    $person->license_file = "";
    $person->home_file = "";
    $person->stock_id = $stock_id;

    $person->add();

    $ids = PersonData::getAllByID();
    return isset($ids[0]->id) && $ids[0]->id != null ? intval($ids[0]->id) : 0;
}

if(isset($_POST["save_web_reservation"]) && $_POST["save_web_reservation"] == "1"){

    if(ob_get_length()){
        ob_clean();
    }

    header("Content-Type: application/json; charset=utf-8");

    $fullname = web_clean($con, $_POST["fullname"] ?? "");
    $no       = web_clean($con, $_POST["no"] ?? "");
    $passport = web_clean($con, $_POST["passport"] ?? "");
    $phone    = web_clean($con, $_POST["phone"] ?? "");

    $car_id = intval($_POST["car_id"] ?? 0);
    $from   = web_clean($con, $_POST["from"] ?? "");
    $to     = web_clean($con, $_POST["to"] ?? "");

    $price_day = floatval(str_replace(",", "", $_POST["price_day"] ?? 0));
    $days      = intval($_POST["total_days"] ?? 0);
    $total_reservation = floatval(str_replace(",", "", $_POST["total_reservation"] ?? 0));

    $pickup  = web_clean($con, $_POST["pickup_location"] ?? "");
    $return  = web_clean($con, $_POST["return_location"] ?? "");
    $message = web_clean($con, $_POST["message"] ?? "");
    $payment_method = web_clean($con, $_POST["payment_method"] ?? "whatsapp");
    $klarna_approved = web_clean($con, $_POST["klarna_approved"] ?? "0");
    $klarna_token = web_clean($con, $_POST["klarna_token"] ?? "");

    if($fullname == "" || $phone == "" || $car_id <= 0 || $from == "" || $to == ""){
        echo json_encode([
            "status" => "error",
            "message" => "Faltan datos obligatorios."
        ]);
        exit;
    }

    if($payment_method == "klarna" && $klarna_approved != "1"){
        echo json_encode([
            "status" => "error",
            "message" => "Primero debe completar y aprobar el pago con Klarna antes de guardar la reservación."
        ]);
        exit;
    }

    $stock_id = web_get_car_stock($con, $car_id);

    if($days <= 0 && $from != "" && $to != ""){
        try{
            $d1 = new DateTime($from);
            $d2 = new DateTime($to);
            $days = $d1->diff($d2)->days;
            if($days < 1){ $days = 1; }
        }catch(Exception $e){
            $days = 1;
        }
    }

    if($total_reservation <= 0){
        $total_reservation = $price_day * $days;
    }

    $id_person = web_find_or_create_person($con, $stock_id, $fullname, $no, $passport, $phone);

    if($id_person <= 0){
        echo json_encode([
            "status" => "error",
            "message" => "No se pudo crear o localizar el cliente."
        ]);
        exit;
    }

    if(!empty($car_id)){

        $user = new BookingData();

        $user->start_at = $from;
        $user->comment = $message . " | Payment Method: " . $payment_method . " | Klarna Token: " . $klarna_token;
        $user->payment_day = "";
        $user->type_id = 1;
        $user->end_at = $to;

        $user->place_start = $pickup;
        $user->place_end = $return;

        $user->person_id = $id_person;
        $user->person2_id = 0;

        $user->location = 0;
        $user->stock_id = $stock_id;
        $user->type_sure = 0;
        $user->sure = 0;
        $user->fuel = "R";
        $user->car_id = $car_id;
        $user->car2_id = 0;

        $user->type = 2;

        $price2 = floatval($price_day);
        $xtotal = 0;
        $plane  = 0;
        $card   = 0;
        $iva    = 0;

        $user->price = $price2;

        $total = ($price2 * $days) + $xtotal + $plane + $card + $iva;

        if($total_reservation > 0){
            $total = $total_reservation;
        }

        $user->total = $total;
        $user->payment = 0;
        $user->day = $days;
        $user->deposit = 0;
        $user->f_id = 0;
        $user->xtotal = 0;

        $user->unit_extra1  = 0;
        $user->price_extra1 = 0;
        $user->unit_extra2  = 0;
        $user->price_extra2 = 0;
        $user->unit_extra3  = 0;
        $user->price_extra3 = 0;
        $user->unit_extra4  = 0;
        $user->price_extra4 = 0;

        $user->usd_price = 0;
        $user->tasa_dolar = 0;

        $user->iva = 0;
        $user->value_iva = 0;
        $user->type_iva = 0;
        $user->number_iva = "";

        $user->user_id = 0;
        $user->plane = 0;
        $user->status = 0;

        $_notif_resvRes = $user->add();

        $id_speds = BookingData::getAllByID();
        $spends = (is_array($_notif_resvRes) && isset($_notif_resvRes[1]) && intval($_notif_resvRes[1]) > 0)
            ? intval($_notif_resvRes[1])
            : (isset($id_speds[0]->id) && $id_speds[0]->id != null ? intval($id_speds[0]->id) : 0);

        if(!class_exists('NotificationService')){
            @include_once __DIR__ . "/../core/app/model/NotificationData.php";
            @include_once __DIR__ . "/../core/app/model/NotificationPreferenceData.php";
            @include_once __DIR__ . "/../core/controller/NotificationService.php";
        }
        if(class_exists('NotificationService') && $spends > 0){
            $_wn_person = PersonData::getById($id_person);
            $_wn_name = isset($_wn_person->name) ? $_wn_person->name : '';
            NotificationService::notifyStockUsers(intval($stock_id), NotificationService::EVENT_BOOKING_WEB,
                'Nueva reserva desde la web', 'Cliente: '.htmlspecialchars($_wn_name).' — Reserva #'.$spends,
                ['booking_id' => $spends, 'url' => './?view=booking&opt=earring']);
            if(intval($id_person) > 0){
                NotificationService::notify('client', intval($id_person), NotificationService::EVENT_BOOKING_WEB,
                    'Recibimos tu solicitud de reserva', 'Hemos recibido tu solicitud #'.$spends.'. Te contactaremos pronto.',
                    ['booking_id' => $spends, 'stock_id' => intval($stock_id)]);
            }
        }

        echo json_encode([
            "status" => "success",
            "message" => "Reservación agregada correctamente.",
            "booking_id" => $spends,
            "person_id" => $id_person,
            "payment_method" => $payment_method
        ]);
        exit;
    }

    echo json_encode([
        "status" => "error",
        "message" => "Vehículo inválido."
    ]);
    exit;
}

$car_id = isset($_GET["car_id"]) ? intval($_GET["car_id"]) : 0;
$from   = isset($_GET["from"]) ? trim($_GET["from"]) : "";
$to     = isset($_GET["to"]) ? trim($_GET["to"]) : "";

$selected_car_text = "VEHICULO NO SELECCIONADO";

$car_price = 0;
$total_days = 0;
$total_reservation = 0;

if($car_id > 0){

    $sql_car = "
        SELECT 
            c.*,
            b.name AS brand_name
        FROM cars c
        LEFT JOIN brand b ON b.id = c.brand_id
        WHERE c.id = $car_id
        LIMIT 1
    ";

    $query_car = $con->query($sql_car);

    if($query_car && $query_car->num_rows > 0){
        $car = $query_car->fetch_array();

        $brand = strtoupper($car["brand_name"] ?? "");
        $name  = strtoupper($car["name"] ?? "VEHICULO");
        $year  = strtoupper($car["year"] ?? "");
        $token = strtoupper($car["token"] ?? "");

        $selected_car_text = trim($brand." ".$name." ".$year." [ ".$token." ]");

        $car_price = floatval(
            $car["price"] ?? 
            $car["price_day"] ?? 
            $car["precio"] ?? 
            $car["precio_dia"] ?? 
            $car["rent_price"] ?? 
            $car["daily_price"] ?? 
            0
        );
    }
}

if($from != "" && $to != ""){
    try{
        $date_from = new DateTime($from);
        $date_to   = new DateTime($to);

        $total_days = $date_from->diff($date_to)->days;

        if($total_days < 1){
            $total_days = 1;
        }

        $total_reservation = $total_days * $car_price;

    }catch(Exception $e){
        $total_days = 0;
        $total_reservation = 0;
    }
}

$whatsapp = StockData::getFPrincipal(1)->phone ?? "";
$whatsapp = preg_replace('/[^0-9]/', '', $whatsapp);

if(substr($whatsapp,0,1)!="1"){
    $whatsapp = "1".$whatsapp;
}
?>

<style>
.reservation-hero{
    background:linear-gradient(rgba(0,0,0,.65),rgba(0,0,0,.65)),
    url('../../IMG_4621.jpg') center center/cover no-repeat;
    min-height:420px;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    color:#fff;
}

.reservation-hero h1{
    font-size:70px;
    font-weight:900;
    margin-bottom:15px;
    color:#fff;
}

.reservation-section{
    padding:90px 0;
    background:#f8fafc;
}

.reservation-card{
    background:#fff;
    border-radius:30px;
    padding:50px;
    box-shadow:0 15px 50px rgba(0,0,0,.06);
}

.reservation-title{
    font-size:42px;
    font-weight:900;
    margin-bottom:15px;
}

.reservation-subtitle{
    color:#64748b;
    margin-bottom:40px;
}

.form-control,
select{
    width:100%;
    height:58px;
    border-radius:18px;
    border:1px solid rgba(0,0,0,.08);
    padding-left:18px;
    box-shadow:none !important;
    margin-bottom:25px;
}

textarea.form-control{
    height:150px;
    padding-top:15px;
    resize:none;
}

.reserve-btn{
    background:rgba(<?php echo $mainColor; ?>,1);
    border:none;
    color:#fff;
    height:58px;
    padding:0 35px;
    border-radius:18px;
    font-weight:900;
    transition:.3s;
}

.reserve-btn:hover{
    background:#111827;
}

.reserve-btn:disabled,
.klarna-btn:disabled{
    opacity:.55;
    cursor:not-allowed;
}

.klarna-btn{
    display:none;
    background:#ffb3c7;
    border:none;
    color:#111827;
    height:58px;
    padding:0 35px;
    border-radius:18px;
    font-weight:900;
    transition:.3s;
    margin-right:12px;
}

.klarna-btn:hover{
    background:#111827;
    color:#fff;
}

.locked-field{
    background:#f1f5f9 !important;
    color:#0f172a !important;
    font-weight:800;
    cursor:not-allowed;
}

.total-box{
    background:#0f172a;
    color:#fff;
    border-radius:22px;
    padding:25px;
    margin-bottom:30px;
}

.total-box h4{
    color:#fff;
    font-weight:900;
    margin-bottom:15px;
}

.total-line{
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:1px solid rgba(255,255,255,.15);
    padding:10px 0;
    font-size:16px;
}

.total-line:last-child{
    border-bottom:none;
    font-size:22px;
    font-weight:900;
}

.payment-box{
    background:transparent;
    border:none;
    border-radius:0;
    padding:0;
    margin-bottom:20px;
}

.payment-note{
    display:none;
    background:#fff7ed;
    color:#9a3412;
    border:1px solid #fed7aa;
    padding:12px 15px;
    border-radius:14px;
    font-weight:700;
    font-size:14px;
    margin-bottom:15px;
}

.alert-web-save{
    display:none;
    margin-bottom:25px;
    border-radius:18px;
    padding:15px 20px;
    font-weight:800;
}

.alert-web-save.success{
    display:block;
    background:#dcfce7;
    color:#166534;
}

.alert-web-save.error{
    display:block;
    background:#fee2e2;
    color:#991b1b;
}

@media(max-width:991px){

    .reservation-hero h1{
        font-size:48px;
    }

    .reservation-card{
        padding:30px;
    }

    .total-line{
        font-size:15px;
    }

    .total-line:last-child{
        font-size:20px;
    }

}
</style>

<section class="reservation-hero">
<div class="container">
<h1>Book Your Vehicle</h1>
<p>Reserve your luxury vehicle quickly and easily.</p>
</div>
</section>

<section class="reservation-section">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-10">

<div class="reservation-card">

<h2 class="reservation-title">
Reservation Form
</h2>

<p class="reservation-subtitle">
Complete the form below to reserve your vehicle.
</p>

<div id="saveAlert" class="alert-web-save"></div>

<form id="reservationForm">

<div class="row">

<div class="col-md-6">
<input 
type="text" 
name="fullname" 
class="form-control" 
placeholder="Full Name" 
required
>
</div>

<div class="col-md-6">
<input 
type="text" 
name="no" 
class="form-control" 
placeholder="ID Card Number" 
required
>
</div>

<div class="col-md-6">
<input 
type="text" 
name="passport" 
class="form-control" 
placeholder="Passport Number"
>
</div>

<div class="col-md-6">
<input 
type="text" 
name="phone" 
class="form-control" 
placeholder="Phone Number" 
required
>
</div>

<div class="col-md-6">
<input 
type="text" 
id="car_text"
class="form-control locked-field" 
value="<?php echo htmlspecialchars($selected_car_text); ?>" 
readonly
>

<input 
type="hidden" 
name="car_id" 
value="<?php echo $car_id; ?>"
>
</div>

<div class="col-md-6">
<input 
type="text" 
class="form-control locked-field" 
value="US$ <?php echo number_format($car_price, 2); ?> per day" 
readonly
>

<input 
type="hidden" 
name="price_day" 
value="<?php echo $car_price; ?>"
>
</div>

<div class="col-md-6">
<input 
type="date" 
class="form-control locked-field" 
value="<?php echo htmlspecialchars($from); ?>" 
readonly
>

<input 
type="hidden" 
name="from" 
value="<?php echo htmlspecialchars($from); ?>"
>
</div>

<div class="col-md-6">
<input 
type="date" 
class="form-control locked-field" 
value="<?php echo htmlspecialchars($to); ?>" 
readonly
>

<input 
type="hidden" 
name="to" 
value="<?php echo htmlspecialchars($to); ?>"
>
</div>

<div class="col-12">
<div class="total-box">

<h4>Reservation Summary</h4>

<div class="total-line">
<span>Price per day</span>
<strong>US$ <?php echo number_format($car_price, 2); ?></strong>
</div>

<div class="total-line">
<span>Total days</span>
<strong><?php echo $total_days; ?> days</strong>
</div>

<div class="total-line">
<span>Total reservation</span>
<strong>US$ <?php echo number_format($total_reservation, 2); ?></strong>
</div>

</div>
</div>

<input 
type="hidden" 
name="total_days" 
value="<?php echo $total_days; ?>"
>

<input 
type="hidden" 
name="total_reservation" 
value="<?php echo $total_reservation; ?>"
>

<div class="col-md-6">
<input 
type="text" 
name="pickup_location" 
class="form-control" 
placeholder="Pick Up Location"
>
</div>

<div class="col-md-6">
<input 
type="text" 
name="return_location" 
class="form-control" 
placeholder="Return Location"
>
</div>

<div class="col-md-12">
<input 
type="text"
name="message" 
class="form-control" 
placeholder="Additional Information"
>
</div>

<div class="col-md-12">
<div class="payment-box">
<label style="font-weight:900;color:#0f172a;margin-bottom:10px;display:block;">
Payment Method
</label>

<select name="payment_method" id="payment_method" class="form-control" required>
    <option value="whatsapp">WhatsApp / Pay later</option>
    <option value="klarna">Klarna / Pay in installments</option>
</select>

<div id="klarnaNote" class="payment-note">
    Primero complete y apruebe el pago con Klarna. Después se habilitará el botón de confirmación para guardar la reservación.
</div>
</div>
</div>

<input type="hidden" name="klarna_approved" id="klarna_approved" value="0">
<input type="hidden" name="klarna_token" id="klarna_token" value="">

<div class="col-12 mt-3">
<button type="button" class="klarna-btn" id="klarnaPayBtn">
Pay with Klarna
</button>

<button type="submit" class="reserve-btn" id="reserveSubmitBtn">
Complete Reservation
</button>
</div>

</div>

</form>

</div>
</div>
</div>
</div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function(){

    const form = document.getElementById("reservationForm");
    const btn = document.getElementById("reserveSubmitBtn");
    const alertBox = document.getElementById("saveAlert");
    const paymentMethodSelect = document.getElementById("payment_method");
    const klarnaNote = document.getElementById("klarnaNote");
    const klarnaPayBtn = document.getElementById("klarnaPayBtn");
    const klarnaApprovedInput = document.getElementById("klarna_approved");
    const klarnaTokenInput = document.getElementById("klarna_token");

    let klarnaApproved = false;
    let klarnaToken = "";

    function showAlert(type, message){
        alertBox.className = "alert-web-save " + type;
        alertBox.innerHTML = message;
    }

    function resetKlarnaApproval(){
        klarnaApproved = false;
        klarnaToken = "";
        klarnaApprovedInput.value = "0";
        klarnaTokenInput.value = "";
    }

    function setKlarnaApproved(token){
        klarnaApproved = true;
        klarnaToken = token || ("KLARNA-" + Date.now());
        klarnaApprovedInput.value = "1";
        klarnaTokenInput.value = klarnaToken;

        btn.disabled = false;
        btn.innerHTML = "Complete Reservation";
        klarnaPayBtn.disabled = true;
        klarnaPayBtn.innerHTML = "Klarna Approved";
        showAlert("success", "Pago aprobado por Klarna. Ahora puede confirmar la reservación.");
    }

    function setKlarnaRejected(){
        resetKlarnaApproval();
        btn.disabled = true;
        klarnaPayBtn.disabled = false;
        klarnaPayBtn.innerHTML = "Pay with Klarna";
        showAlert("error", "Klarna no aprobó el pago. Use el método WhatsApp / Pay later para recibir la solicitud.");
    }

    function updatePaymentUI(){
        resetKlarnaApproval();

        if(paymentMethodSelect.value === "klarna"){
            klarnaNote.style.display = "block";
            klarnaPayBtn.style.display = "inline-block";
            klarnaPayBtn.disabled = false;
            klarnaPayBtn.innerHTML = "Pay with Klarna";

            btn.disabled = true;
            btn.innerHTML = "Waiting Klarna Approval";
            showAlert("error", "Debe pagar y aprobar con Klarna antes de confirmar la reservación.");
        }else{
            klarnaNote.style.display = "none";
            klarnaPayBtn.style.display = "none";
            klarnaPayBtn.disabled = false;
            klarnaPayBtn.innerHTML = "Pay with Klarna";

            btn.disabled = false;
            btn.innerHTML = "Complete Reservation";
            showAlert("", "");
        }
    }

    paymentMethodSelect.addEventListener("change", updatePaymentUI);

    klarnaPayBtn.addEventListener("click", function(){

        if(!form.checkValidity()){
            form.reportValidity();
            return;
        }

        let fullname = document.querySelector("[name='fullname']").value;
        let phone = document.querySelector("[name='phone']").value;
        let carText = document.getElementById("car_text").value;
        let from = document.querySelector("[name='from']").value;
        let to = document.querySelector("[name='to']").value;
        let priceDay = document.querySelector("[name='price_day']").value;
        let totalDays = document.querySelector("[name='total_days']").value;
        let totalReservation = document.querySelector("[name='total_reservation']").value;

        klarnaPayBtn.disabled = true;
        klarnaPayBtn.innerHTML = "Opening Klarna...";
        showAlert("success", "Abriendo Klarna. Complete el pago en la nueva pestaña.");

        fetch("klarna/create_session.php", {
            method: "POST",
            headers:{
                "Content-Type":"application/json"
            },
            body: JSON.stringify({
                total: totalReservation,
                price_day: priceDay,
                total_days: totalDays,
                fullname: fullname,
                phone: phone,
                car_text: carText,
                from: from,
                to: to
            })
        })
        .then(r => r.json())
        .then(klarna => {

            if(klarna.status === "success" && klarna.checkout_url !== ""){

                klarnaToken = klarna.klarna_token || ("KLARNA-" + Date.now());
                klarnaTokenInput.value = klarnaToken;

                localStorage.removeItem("klarna_status_" + klarnaToken);

                let klarnaWindow = window.open(klarna.checkout_url, "_blank");

                if(!klarnaWindow){
                    showAlert("error", "El navegador bloqueó la pestaña de Klarna. Permita popups e intente de nuevo.");
                    klarnaPayBtn.disabled = false;
                    klarnaPayBtn.innerHTML = "Pay with Klarna";
                }else{
                    showAlert("success", "Klarna fue abierto. Cuando el pago sea aprobado, se habilitará el botón de confirmación.");
                }

            }else{
                showAlert("error", klarna.message || "No se pudo abrir Klarna.");
                klarnaPayBtn.disabled = false;
                klarnaPayBtn.innerHTML = "Pay with Klarna";
            }

        })
        .catch(error => {
            showAlert("error", "Error conectando con Klarna.");
            klarnaPayBtn.disabled = false;
            klarnaPayBtn.innerHTML = "Pay with Klarna";
        });

    });

    window.addEventListener("message", function(event){

        if(!event.data || event.data.type !== "klarna_result"){
            return;
        }

        if(event.data.status === "approved"){
            setKlarnaApproved(event.data.token || klarnaToken);
        }

        if(event.data.status === "rejected"){
            setKlarnaRejected();
        }

    });

    setInterval(function(){

        if(klarnaToken === ""){
            return;
        }

        let status = localStorage.getItem("klarna_status_" + klarnaToken);

        if(status === "approved"){
            localStorage.removeItem("klarna_status_" + klarnaToken);
            setKlarnaApproved(klarnaToken);
        }

        if(status === "rejected"){
            localStorage.removeItem("klarna_status_" + klarnaToken);
            setKlarnaRejected();
        }

    }, 1000);

    form.addEventListener("submit", function(e){

        e.preventDefault();

        let fullname = document.querySelector("[name='fullname']").value;
        let cedula = document.querySelector("[name='no']").value;
        let passport = document.querySelector("[name='passport']").value;
        let phone = document.querySelector("[name='phone']").value;

        let carText = document.getElementById("car_text").value;

        let from = document.querySelector("[name='from']").value;
        let to = document.querySelector("[name='to']").value;

        let priceDay = document.querySelector("[name='price_day']").value;
        let totalDays = document.querySelector("[name='total_days']").value;
        let totalReservation = document.querySelector("[name='total_reservation']").value;

        let pickup = document.querySelector("[name='pickup_location']").value;
        let retorno = document.querySelector("[name='return_location']").value;

        let message = document.querySelector("[name='message']").value;
        let paymentMethod = document.querySelector("[name='payment_method']").value;

        if(paymentMethod === "klarna" && klarnaApproved !== true){
            showAlert("error", "Debe completar y aprobar el pago con Klarna antes de confirmar la reservación.");
            return;
        }

        let whatsappNumber = "<?php echo $whatsapp; ?>";

        let text = 
`*NEW VEHICLE RESERVATION*

*Customer:* ${fullname}
*ID Card:* ${cedula}
*Passport:* ${passport}
*Phone:* ${phone}

*Vehicle:* ${carText}

*From:* ${from}
*To:* ${to}

*Price Per Day:* US$ ${parseFloat(priceDay).toFixed(2)}
*Total Days:* ${totalDays}
*Total Reservation:* US$ ${parseFloat(totalReservation).toFixed(2)}

*Payment Method:* ${paymentMethod}

*Pick Up Location:* ${pickup}
*Return Location:* ${retorno}

*Additional Information:* ${message}`;

        let url = "https://wa.me/" + whatsappNumber + "?text=" + encodeURIComponent(text);

        let formData = new FormData(form);
        formData.append("save_web_reservation", "1");
        formData.append("car_text", carText);
        formData.append("price_day", priceDay);
        formData.append("total_days", totalDays);
        formData.append("total_reservation", totalReservation);
        formData.append("payment_method", paymentMethod);
        formData.append("klarna_approved", klarnaApprovedInput.value);
        formData.append("klarna_token", klarnaTokenInput.value);

        btn.disabled = true;
        btn.innerHTML = "Saving reservation...";
        showAlert("", "");

        fetch("reservation.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(save => {

            if(save.status === "success"){

                showAlert("success", save.message + " #" + save.booking_id);

                if(paymentMethod === "whatsapp"){
                    window.open(url, "_blank");
                }

                form.reset();

                document.querySelector("[name='from']").value = "<?php echo htmlspecialchars($from); ?>";
                document.querySelector("[name='to']").value = "<?php echo htmlspecialchars($to); ?>";
                document.querySelector("[name='price_day']").value = "<?php echo $car_price; ?>";
                document.querySelector("[name='total_days']").value = "<?php echo $total_days; ?>";
                document.querySelector("[name='total_reservation']").value = "<?php echo $total_reservation; ?>";
                document.getElementById("car_text").value = "<?php echo htmlspecialchars($selected_car_text); ?>";

                paymentMethodSelect.value = "whatsapp";
                resetKlarnaApproval();
                updatePaymentUI();

                showAlert("success", save.message + " #" + save.booking_id);

            }else{
                showAlert("error", save.message);
                btn.disabled = false;
                btn.innerHTML = "Complete Reservation";
            }

        })
        .catch(error => {
            showAlert("error", "Error saving reservation.");
            btn.disabled = false;
            btn.innerHTML = "Complete Reservation";
        });

    });

    updatePaymentUI();

});
</script>

<?php include "layout/footer.php"; ?>