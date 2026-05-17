<?php
// Cargar modelos y datos
include "../../../core/controller/Core.php";
include "../../../core/controller/Database.php";
include "../../../core/controller/Executor.php";
include "../../../core/controller/Model.php";
include "../../../core/app/model/BookingData.php";
include "../../../core/app/model/PersonData.php";
include "../../../core/app/model/CarsData.php";
include "../../../core/app/model/SureData.php";
include "../../../core/app/model/StockData.php";

// Función para formatear los datos
function get_postemplate_rencar($id) {
    $sell = BookingData::getById($id);
    $client = PersonData::getById($sell->person_id);
    $car = CarsData::getById($sell->car_id);
    $stock = StockData::getPrincipal();

    $subtotal = ($sell->price * $sell->day) + $sell->xtotal;
    $card = ($sell->total * ($sell->card / 100));
    $itbis = $sell->iva > 0 ? $subtotal * ($sell->iva / 100) : 0;

    return [
        'store_name' => $stock->name,
        'store_address' => $stock->address,
        'store_phone' => $stock->mobile,
        'store_email' => $stock->email,
        'logo_url' => "../assets/img/logo.png", // o usa $stock->logo si aplica

        'customer_name' => $client->name,
        'customer_phone' => $client->phone,
        'customer_address' => $client->address,
        'start_at' => $sell->start_at,
        'end_at' => $sell->end_at,
        'place_start' => $sell->place_start,
        'place_end' => $sell->place_end,

        'vehicle_category' => $car->getCategory()->name,
        'vehicle_brand' => $car->getBrand()->name,
        'vehicle_model' => $car->name,
        'vehicle_year' => $car->year,
        'vehicle_color' => $car->getExColor()->name,
        'vehicle_plate' => $car->plate,

        'day' => $sell->day,
        'price' => $sell->price,
        'subtotal' => $subtotal,
        'sure' => $sell->sure,
        'card_fee' => $card,
        'others' => $sell->plane,
        'itbis' => $itbis,
        'total' => ($sell->total - $sell->total) + ($sell->sure + $card + $sell->plane),

        'language' => $client->language,
        'signature' => $sell->firma ? '../' . $sell->firma : null,
        'note' => $sell->note,
        'booking_id' => $sell->id
    ];
}

$id = $_GET['id'];
$data = get_postemplate_rencar($id);
?>

<div class="receipt-template p-4 text-white" style="background-color: #1e1e1e; font-size: 13px;">
  <div class="text-center mb-3">
    <img src="<?= $data['logo_url'] ?>" style="height: 60px;">
    <h4 class="mt-2 mb-0 text-uppercase"><?= strtoupper($data['store_name']) ?></h4>
    <p class="mb-0"><?= $data['store_address'] ?></p>
    <p class="mb-0">Tel: <?= $data['store_phone'] ?> | <?= $data['store_email'] ?></p>
  </div>

  <hr style="border-color: #666;">

  <div class="row mb-2">
    <div class="col-md-6">
      <b>Cliente:</b> <?= strtoupper($data['customer_name']) ?><br>
      <b>Teléfono:</b> <?= $data['customer_phone'] ?><br>
      <b>Dirección:</b> <?= $data['customer_address'] ?>
    </div>
    <div class="col-md-6">
      <b>Desde:</b> <?= date("d/m/Y h:i A", strtotime($data['start_at'])) ?><br>
      <b>Hasta:</b> <?= date("d/m/Y h:i A", strtotime($data['end_at'])) ?><br>
      <b>Entrega:</b> <?= $data['place_start'] ?><br>
      <b>Devolución:</b> <?= $data['place_end'] ?>
    </div>
  </div>

  <h5 class="mt-3">Datos del Vehículo</h5>
  <table class="table table-bordered table-dark table-sm">
    <tbody>
      <tr><td><b>Marca:</b> <?= $data['vehicle_brand'] ?></td><td><b>Modelo:</b> <?= $data['vehicle_model'] ?></td></tr>
      <tr><td><b>Año:</b> <?= $data['vehicle_year'] ?></td><td><b>Color:</b> <?= $data['vehicle_color'] ?></td></tr>
      <tr><td colspan="2"><b>Placa:</b> <?= $data['vehicle_plate'] ?></td></tr>
    </tbody>
  </table>

  <h5 class="mt-4">Resumen de Pago</h5>
  <table class="table table-dark table-sm">
    <tbody>
      <tr><td>Días</td><td><?= $data['day'] ?></td></tr>
      <tr><td>Precio por día</td><td><?= number_format($data['price'],2) ?></td></tr>
      <tr><td>Subtotal</td><td><?= number_format($data['subtotal'],2) ?></td></tr>
      <tr><td>Seguro</td><td><?= number_format($data['sure'],2) ?></td></tr>
      <tr><td>ITBIS</td><td><?= number_format($data['itbis'],2) ?></td></tr>
      <tr><td>Otros cargos</td><td><?= number_format($data['others'],2) ?></td></tr>
      <tr><td>Tarjeta</td><td><?= number_format($data['card_fee'],2) ?></td></tr>
      <tr class="bg-secondary"><td><b>Total</b></td><td><b><?= number_format($data['total'],2) ?></b></td></tr>
    </tbody>
  </table>

  <h6 class="mt-4 mb-2">Nota:</h6>
  <p style="white-space: pre-wrap;"><?= nl2br($data['note']) ?></p>

  <?php if ($data['signature']) : ?>
    <div class="text-center mt-4">
      <p class="mb-1">Firma del Cliente:</p>
      <img src="<?= $data['signature'] ?>" style="height: 80px; border:1px solid #888;">
    </div>
  <?php endif; ?>
</div>

