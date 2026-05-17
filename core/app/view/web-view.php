<?php
if(isset($_SESSION["user_id"])):

$users = BookingData::getAllBySQL("
where type='2'
and status=0
and (firma='' OR firma IS NULL)
and stock_id=".StockData::getPrincipal()->id."
order by id desc
");

$TicketMm = StockData::getPrincipal()->ticket_mm;
$method = StockData::getPrincipal()->method;
?>

<style>


.pending-card .card-header{
    background:#111827;
    border-bottom:1px solid rgba(255,255,255,.08);
}

.pending-table{
    color:white;
    margin:0;
}

.pending-table thead th{
    background:#111827;
    color:#facc15;
    border-color:#333 !important;
    font-size:13px;
    white-space:nowrap;
}

.pending-table tbody td{
    border-color:#333 !important;
    vertical-align:middle !important;
    font-size:13px;
}



.pending-table tbody tr:hover{
    background:#2b2b2b;
}

.client-box{
    display:flex;
    flex-direction:column;
}

.client-box b{
    color:white;
}

.client-box small{
    color:#cbd5e1;
}

.top-box{
    background:#111827;
    border-radius:16px;
    padding:18px;
    color:white;
    margin-bottom:15px;
    border:1px solid rgba(255,255,255,.08);
}

.top-box h3{
    margin:0;
    font-size:28px;
    font-weight:900;
}

.top-box p{
    margin:0;
    color:#cbd5e1;
    font-weight:700;
}

.btn-custom{
    border-radius:10px;
    font-size:12px;
    font-weight:800;
    margin:2px;
}

@media(max-width:768px){

    .pending-table th,
    .pending-table td{
        white-space:nowrap;
        font-size:12px;
    }

    .top-box h3{
        font-size:22px;
    }

}
</style>

<section class="content">

<div class="container-fluid">

<div class="content-header">

<div class="row mb-2">

<div class="col-sm-6">

<h1 class="m-0 text-white">

<i class="fa fa-calendar-check"></i>
Reservaciones Web Pendientes

</h1>

</div>

<div class="col-sm-6">

<ol class="breadcrumb float-sm-right">

<li class="breadcrumb-item active">
<i class="fa fa-home"></i> Tablero
</li>

<li class="breadcrumb-item active">
Reservaciones
</li>

</ol>

</div>

</div>

</div>

<div class="row">

<div class="col-md-4 col-12">

<div class="top-box">

<p>Reservaciones Pendientes</p>

<h3>
<?php echo count($users); ?>
</h3>

</div>

</div>

<div class="col-md-4 col-12">

<div class="top-box">

<p>Método</p>

<h3>
<?php echo $method; ?>
</h3>

</div>

</div>

<div class="col-md-4 col-12">

<div class="top-box">

<p>Ticket URL</p>

<h3 style="font-size:16px;">
<?php echo $TicketMm; ?>
</h3>

</div>

</div>

</div>

<div class="card pending-card">

<div class="card-header">

<h3 class="card-title text-warning">

<i class="fa fa-clock"></i>
Reservaciones Web Pendientes

</h3>

</div>

<?php if(count($users)>0): ?>

<div class="card-body"  style="background-color:#222;">

<div class="table-responsive">

<table class="table table-bordered pending-table" id="example2">

<thead style="background-color:#333; color:white;">

<tr>

<th>Cliente</th>

<th>Vehículo</th>

<th>Desde</th>

<th>Hasta</th>

<th>Días</th>

<th>Total</th>

<th>Pendiente</th>

<th>Acción</th>

</tr>

</thead>

<tbody>

<?php foreach($users as $user): ?>

<?php

$totpayments = 0;

$payments = PaymentData::getByPayment($user->id);

$totpayments = isset($payments[0]->t) && $payments[0]->t != null
? $payments[0]->t
: 0;

$car_text = "NO VEHÍCULO";

$car = $user->getCars();

if($car){

    $brand_name = "";

    $brand = BrandData::getById($car->brand_id);

    if($brand){
        $brand_name = $brand->name;
    }

    $car_text = $brand_name." ".$car->name." ".$car->year." [".$car->token."]";

}

$person_name = "SIN CLIENTE";

$person = $user->getPerson();

if($person){
    $person_name = $person->name;
}

?>

<tr style="color:white;">

<td>

<?php echo $person_name; ?>

</td>

<td>

<?php

if ($totpayments==0){

echo '<span class="description-percentage text-danger">
<i class="fas fa-caret-right"></i>
</span> ';

}elseif ($totpayments>0 && $totpayments<$user->total){

echo '<span class="description-percentage text-warning">
<i class="fas fa-caret-right"></i>
</span> ';

}elseif ($user->total==$totpayments){

echo '<span class="description-percentage text-success">
<i class="fas fa-caret-right"></i>
</span> ';

}

echo $car_text;

?>

</td>

<td>

<?php echo $user->start_at; ?>

</td>

<td>

<?php echo $user->end_at; ?>

</td>

<td>

<?php echo $user->day; ?>

</td>

<td>

<?php echo number_format($user->total,2,".",","); ?>

</td>

<td>

<?php echo number_format(($user->total-$totpayments),2,".",","); ?>

</td>

<td class="text-right py-0 align-middle">


<a 
href="./?view=booking&opt=newearring&id=<?php echo $user->id; ?>" 
class="btn btn-success btn-block btn-sm">

<i class="fa fa-check"></i> Completar

</a>

<?php 

$person_delete = PersonData::getById($user->person_id);

if($person_delete && $person_delete->is_rental==0):

?>

<a 
href="./?action=booking&opt=del&id=<?php echo $user->id; ?>" 
class="btn btn-danger btn-block btn-sm">

<i class="fa fa-trash"></i> Eliminar

</a>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

<?php else: ?>

<div class="text-center p-5">

<h4 class="text-muted">

<i class="fa fa-calendar-times"></i>

<br><br>

No hay reservaciones pendientes.

</h4>

</div>

<?php endif; ?>

</div>

</div>

</section>

<script>
$(document).ready(function(){

    if ($.fn.DataTable.isDataTable('#example2')) {
        $('#example2').DataTable().destroy();
    }

    $('#example2').DataTable({
        responsive: true,
        autoWidth: false
    });

});
</script>

<?php endif; ?>