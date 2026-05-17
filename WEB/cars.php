<?php include "layout/header.php"; ?>

<?php
$base = new Database();
$con = $base->connect();

function car_value($row, $keys, $default = ""){
  foreach($keys as $key){
    if(isset($row[$key]) && trim($row[$key]) !== ""){
      return $row[$key];
    }
  }
  return $default;
}

$sql = "SELECT * FROM cars ORDER BY id DESC";
$query = $con->query($sql);
?>

<style>
body{
  background:#fff;
  color:#111827;
}

.cars-hero{
  background:#0f172a;
  padding:150px 0 90px;
  color:#fff;
}

.cars-hero h1{
  font-size:64px;
  font-weight:900;
  color:#fff;
}

.cars-hero p{
  color:rgba(255,255,255,.70);
  font-size:18px;
  max-width:650px;
}

.section-mini{
  color:rgba(<?php echo $mainColor; ?>,1);
  font-size:13px;
  font-weight:900;
  letter-spacing:2px;
  text-transform:uppercase;
  margin-bottom:15px;
  display:block;
}

.section-title{
  font-size:52px;
  font-weight:900;
  color:#111827;
}

.car-card{
  background:#fff;
  border-radius:28px;
  overflow:hidden;
  border:1px solid rgba(<?php echo $mainColor; ?>,0.06);
  transition:.35s;
  height:100%;
  box-shadow:0 15px 40px rgba(<?php echo $mainColor; ?>,0.06);
}

.car-card:hover{
  transform:translateY(-8px);
  box-shadow:0 30px 80px rgba(<?php echo $mainColor; ?>,0.13);
}

.car-img{
  height:270px;
  background-size:cover;
  background-position:center;
  position:relative;
}

.car-status{
  position:absolute;
  top:18px;
  left:18px;
  background:#fff;
  color:rgba(<?php echo $mainColor; ?>,1);
  padding:8px 16px;
  border-radius:999px;
  font-size:12px;
  font-weight:900;
}

.car-body{
  padding:28px;
}

.car-name{
  font-size:26px;
  font-weight:900;
  color:#111827;
  margin-bottom:12px;
}

.car-desc{
  color:#64748b;
  line-height:1.8;
  margin-bottom:22px;
}

.car-info{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:10px;
  margin-bottom:24px;
}

.car-info span{
  background:#f8fafc;
  border-radius:16px;
  padding:12px 8px;
  text-align:center;
  font-size:13px;
  font-weight:800;
  color:#64748b;
}

.car-info i{
  display:block;
  color:rgba(<?php echo $mainColor; ?>,1);
  font-size:18px;
  margin-bottom:5px;
}

.car-footer{
  display:block;
}

.car-price{
  font-size:30px;
  font-weight:900;
  color:#111827;
}

.car-price span{
  display:block;
  font-size:13px;
  color:#64748b;
}

.car-btn{
  background:#111827;
  color:#fff !important;
  padding:15px 24px;
  border-radius:18px;
  font-weight:900;
  white-space:nowrap;
}

.car-btn:hover{
  background:rgba(<?php echo $mainColor; ?>,1);
}

.empty-box{
  background:#fff;
  border:1px solid rgba(<?php echo $mainColor; ?>,0.08);
  border-radius:30px;
  padding:60px 30px;
  text-align:center;
}

.empty-box i{
  font-size:60px;
  color:rgba(<?php echo $mainColor; ?>,1);
  margin-bottom:20px;
}

/* ========================= */
/* BOOKING */
/* ========================= */

.car-booking-box{
  margin-top:25px;
}

.car-price-box{
  margin-bottom:18px;
}

.car-date-row{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:14px;
  margin-bottom:14px;
}

.car-date-field label{
  display:block;
  font-size:13px;
  font-weight:900;
  color:#64748b;
  margin-bottom:7px;
}

.car-date-field input{
  height:52px;
  border-radius:16px;
  border:1px solid #dbe3ee;
  padding:0 12px;
  font-weight:800;
  margin-bottom:0;
}

.validate-date-btn{
  width:100%;
  height:54px;
  border:none;
  border-radius:18px;
  background:#111827;
  color:#fff;
  font-weight:900;
  font-size:15px;
}

.validate-date-btn:hover{
  background:rgba(<?php echo $mainColor; ?>,1);
}

.availability-msg{
  margin-top:12px;
  font-weight:900;
  font-size:14px;
  text-align:center;
  min-height:22px;
}

.reserve-final-btn{
  display:none;
  width:100%;
  margin-top:12px;
  text-align:center;
  background:rgba(<?php echo $mainColor; ?>,1);
  color:#fff !important;
  padding:15px 24px;
  border-radius:18px;
  font-weight:900;
}

@media(max-width:767px){

  .cars-hero{
    padding:130px 0 70px;
  }

  .cars-hero h1{
    font-size:42px;
  }

  .section-title{
    font-size:36px;
  }

}

@media(max-width:480px){

  .car-date-row{
    grid-template-columns:1fr;
  }

}
</style>

<section class="cars-hero">
  <div class="container">

    <span class="section-mini">
      Our Fleet
    </span>

    <h1>
      Choose Your Perfect Car
    </h1>

    <p>
      Explore our premium vehicles and book the right car for your next trip, business meeting or special occasion.
    </p>

  </div>
</section>

<section class="ftco-section">
  <div class="container">

    <div class="row justify-content-center mb-5">

      <div class="col-lg-8 text-center">

        <span class="section-mini">
          Available Cars
        </span>

        <h2 class="section-title">
          Premium Rental Fleet
        </h2>

      </div>

    </div>

    <div class="row">

      <?php if($query && $query->num_rows > 0): ?>

        <?php while($car = $query->fetch_array()): ?>

        <?php

        $car_id = car_value($car, ["id", "car_id"], 0);

        /* BRAND */

        $brand_name = "";

        $brand_id = car_value($car, ["brand_id"], 0);

        if(intval($brand_id) > 0){

          $brand = BrandData::getById(intval($brand_id));

          if($brand && isset($brand->name)){
            $brand_name = strtoupper((string)$brand->name);
          }

        }

        /* YEAR */

        $car_year = "";

        $year_tmp = car_value($car, ["year"], "");

        if(trim((string)$year_tmp) != ""){
          $car_year = trim((string)$year_tmp);
        }

        /* TOKEN */

        $car_token = "";

        $token_tmp = car_value($car, ["token"], "");

        if(trim((string)$token_tmp) != ""){
          $car_token = trim((string)$token_tmp);
        }

        /* NAME */

        $name_tmp = car_value($car, ["name", "title", "model", "car_name"], "");

        if(trim((string)$name_tmp) != ""){

          $name = strtoupper(
            trim(
              $brand_name." ".
              (string)$name_tmp." ".
              $car_year." [ ".
              $car_token." ]"
            )
          );

        }else{

          $name = "VEHICULO";

        }

        /* DESCRIPTION */

        $description = car_value(
          $car,
          ["description", "descripcion", "details", "note"],
          "Luxury vehicle with premium comfort, modern technology and excellent performance."
        );

        /* PRICE */

        $price = car_value(
          $car,
          ["price", "price_day", "precio", "precio_dia", "rent_price", "daily_price", "precio_dia_usd", "day_price"],
          "0"
        );

        /* EXTRA */

        $fuel = car_value($car, ["fuel", "fuel_type", "combustible"], "Gasoline");

        $transmission = car_value($car, ["transmission", "transmision"], "Automatic");

        $seats = car_value($car, ["seats", "passengers", "pasajeros"], "5");

        /* IMAGE */

        $image = "img/default.jpg";

        $image_name = car_value(
          $car,
          ["invoice_file", "image", "img", "photo", "picture", "car_image", "image1", "imagen", "main_image"],
          ""
        );

        if($image_name != ""){

          $image_name = trim((string)$image_name);

          $possible_paths = array(
            "../../CF-SYSTEMS/storage/invoice_files/".$image_name,
            "../CF-SYSTEMS/storage/invoice_files/".$image_name,
            "CF-SYSTEMS/storage/invoice_files/".$image_name,
            $image_name
          );

          foreach($possible_paths as $path){

            if(file_exists($path)){
              $image = $path;
              break;
            }

          }

        }

        ?>

        <div class="col-lg-4 col-md-6 mb-4">

          <div class="car-card">

            <div class="car-img" style="background-image:url('<?php echo $image; ?>');">

              <div class="car-status">
                Available
              </div>

            </div>

            <div class="car-body">

              <h3 class="car-name">
                <?php echo strtoupper($name); ?>
              </h3>

              <p class="car-desc">
                <?php echo $description; ?>
              </p>

              <div class="car-info">

                <span>
                  <i class="fa fa-gas-pump"></i>
                  <?php echo $fuel; ?>
                </span>

                <span>
                  <i class="fa fa-cog"></i>
                  <?php echo $transmission; ?>
                </span>

                <span>
                  <i class="fa fa-users"></i>
                  <?php echo $seats; ?> Seats
                </span>

              </div>

              <div class="car-footer">

                <div class="car-booking-box">

                  <div class="car-price-box">

                    <div class="car-price">
                      RD$ <?php echo number_format(floatval($price), 2); ?>
                      <span>Per Day</span>
                    </div>

                  </div>

                  <div class="car-date-row">

                    <div class="car-date-field">

                      <label>
                        Desde
                      </label>

                      <input 
                        type="date" 
                        class="form-control check-from" 
                        data-car="<?php echo $car_id; ?>"
                      >

                    </div>
                    <br>

                    <div class="car-date-field">

                      <label>
                        Hasta
                      </label>

                      <input 
                        type="date" 
                        class="form-control check-to" 
                        data-car="<?php echo $car_id; ?>"
                      >

                    </div>

                  </div>

                  <button 
                    type="button" 
                    class="validate-date-btn" 
                    data-car="<?php echo $car_id; ?>"
                  >
                    Validar Disponibilidad
                  </button>

                  <div 
                    id="availability-msg-<?php echo $car_id; ?>" 
                    class="availability-msg"
                  ></div>

                  <a 
                    href="#" 
                    id="reserve-btn-<?php echo $car_id; ?>" 
                    class="reserve-final-btn"
                  >
                    Reservar Ahora
                  </a>

                </div>

              </div>

            </div>

          </div>

        </div>

        <?php endwhile; ?>

      <?php else: ?>

      <div class="col-md-12">

        <div class="empty-box">

          <i class="fa fa-car-side"></i>

          <h3>
            No cars available
          </h3>

          <p>
            There are no rental vehicles available at this moment.
          </p>

        </div>

      </div>

      <?php endif; ?>

    </div>

  </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function(){

    function formatDate(date){
        return date.toISOString().split("T")[0];
    }

    function addDays(dateString, days){
        let d = new Date(dateString + "T00:00:00");
        d.setDate(d.getDate() + days);
        return formatDate(d);
    }

    function diffDays(from, to){
        let d1 = new Date(from + "T00:00:00");
        let d2 = new Date(to + "T00:00:00");
        return Math.floor((d2 - d1) / (1000 * 60 * 60 * 24));
    }

    let today = formatDate(new Date());

    document.querySelectorAll(".check-from, .check-to").forEach(function(input){
        input.min = today;
    });

    document.querySelectorAll(".check-from").forEach(function(fromInput){
        fromInput.addEventListener("change", function(){
            let carId = this.getAttribute("data-car");
            let toInput = document.querySelector(".check-to[data-car='" + carId + "']");

            let minTo = addDays(this.value, 3);

            toInput.min = minTo;

            if(toInput.value === "" || toInput.value < minTo){
                toInput.value = minTo;
            }
        });
    });

    document.querySelectorAll(".validate-date-btn").forEach(function(btn){

        btn.addEventListener("click", function(){

            let carId = this.getAttribute("data-car");

            let fromInput = document.querySelector(".check-from[data-car='" + carId + "']");
            let toInput   = document.querySelector(".check-to[data-car='" + carId + "']");

            let from = fromInput.value;
            let to   = toInput.value;

            let msgBox = document.getElementById("availability-msg-" + carId);
            let reserveBtn = document.getElementById("reserve-btn-" + carId);

            reserveBtn.style.display = "none";
            reserveBtn.href = "#";

            if(from === "" || to === ""){
                msgBox.innerHTML = "Seleccione fecha desde y fecha hasta.";
                msgBox.style.color = "red";
                return;
            }

            if(to < from){
                msgBox.innerHTML = "La fecha final no puede ser menor que la inicial.";
                msgBox.style.color = "red";
                return;
            }

            if(diffDays(from, to) < 3){
                let minTo = addDays(from, 3);

                toInput.value = minTo;
                toInput.min = minTo;

                msgBox.innerHTML = "La reserva mínima es de 3 días. La fecha hasta debe ser mínimo: " + minTo;
                msgBox.style.color = "red";
                return;
            }

            msgBox.innerHTML = "Validando disponibilidad...";
            msgBox.style.color = "#111827";

            let formData = new FormData();
            formData.append("car_id", carId);
            formData.append("from", from);
            formData.append("to", to);

            fetch("./check_availability.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {

                if(data.status === "available"){

                    msgBox.innerHTML = data.message;
                    msgBox.style.color = "green";

                    reserveBtn.href = data.url;
                    reserveBtn.style.display = "block";

                }else{

                    msgBox.innerHTML = data.message;
                    msgBox.style.color = "red";

                    if(data.next_date){
                        fromInput.value = data.next_date;

                        let minTo = addDays(data.next_date, 3);

                        toInput.value = minTo;
                        toInput.min = minTo;
                    }

                }

            })
            .catch(error => {
                msgBox.innerHTML = "Error validando disponibilidad.";
                msgBox.style.color = "red";
            });

        });

    });

});
</script>

<?php include "layout/footer.php"; ?>