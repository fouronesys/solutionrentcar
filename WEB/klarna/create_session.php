<?php
require_once "config.php";

header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents("php://input"), true);

$total = floatval($data["total"] ?? 0);
$fullname = trim($data["fullname"] ?? "Customer");
$phone = trim($data["phone"] ?? "");
$car_text = trim($data["car_text"] ?? "Vehicle Reservation");

if($total <= 0){
    echo json_encode([
        "status" => "error",
        "message" => "Total inválido."
    ]);
    exit;
}

$amount = intval(round($total * 100));
$token = "KLARNA-" . time() . "-" . rand(1000,9999);

$_SESSION["klarna_token"] = $token;
$_SESSION["klarna_status"] = "pending";

$payment_payload = [
    "purchase_country" => "US",
    "purchase_currency" => "USD",
    "locale" => "en-US",
    "intent" => "buy",
    "order_amount" => $amount,
    "order_tax_amount" => 0,
    "order_lines" => [
        [
            "type" => "physical",
            "reference" => "RENTCAR",
            "name" => $car_text,
            "quantity" => 1,
            "unit_price" => $amount,
            "tax_rate" => 0,
            "total_amount" => $amount,
            "total_tax_amount" => 0
        ]
    ]
];

$ch = curl_init(KLARNA_BASE_URL . "/payments/v1/sessions");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payment_payload),
    CURLOPT_HTTPHEADER => [
        "Authorization: Basic " . base64_encode(KLARNA_USERNAME . ":" . KLARNA_PASSWORD),
        "Content-Type: application/json"
    ]
]);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$payment_result = json_decode($response, true);

if($httpcode < 200 || $httpcode >= 300 || empty($payment_result["session_id"])){
    echo json_encode([
        "status" => "error",
        "message" => $payment_result["error_messages"][0] ?? "No se pudo crear la sesión de pago Klarna.",
        "debug" => $payment_result
    ]);
    exit;
}

$session_id = $payment_result["session_id"];

$hpp_payload = [
    "payment_session_url" => KLARNA_BASE_URL . "/payments/v1/sessions/" . $session_id,
    "merchant_urls" => [
        "success" => KLARNA_SUCCESS_URL . "?token=" . urlencode($token),
        "cancel" => KLARNA_CANCEL_URL . "?token=" . urlencode($token),
        "back" => KLARNA_BACK_URL
    ],
    "options" => [
        "place_order_mode" => "NONE"
    ]
];

$ch = curl_init(KLARNA_BASE_URL . "/hpp/v1/sessions");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($hpp_payload),
    CURLOPT_HTTPHEADER => [
        "Authorization: Basic " . base64_encode(KLARNA_USERNAME . ":" . KLARNA_PASSWORD),
        "Content-Type: application/json"
    ]
]);

$hpp_response = curl_exec($ch);
$hpp_httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$hpp_result = json_decode($hpp_response, true);

$checkout_url = $hpp_result["redirect_url"] ?? $hpp_result["distribution_url"] ?? "";

if($hpp_httpcode < 200 || $hpp_httpcode >= 300 || $checkout_url == ""){
    echo json_encode([
        "status" => "error",
        "message" => $hpp_result["error_messages"][0] ?? "No se pudo crear la página de pago Klarna.",
        "debug" => $hpp_result
    ]);
    exit;
}

echo json_encode([
    "status" => "success",
    "checkout_url" => $checkout_url,
    "klarna_token" => $token
]);