<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):$go = isset($_GET["go"]) ? $_GET["go"] : "";

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
  ✅ AHORA EL BUSCADOR ES 100% EN VIVO (SIN ACTION / SIN RECARGAR)
  - Ya NO se usa go=name para filtrar en el servidor.
  - Se cargan los vehículos normalmente y el buscador filtra las tarjetas con JS.
*/
$products = CarsData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id);?>

<!-- =========================
     SECTION COMPLETO (BUSCADOR EN VIVO SIN ACTION)
========================= -->

<style>
/* ====== SECTION HEADER ====== */
.rc-section{ padding: 30px 0; }
.rc-header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:15px;
  margin-bottom: 18px;
  flex-wrap: wrap;
}
.rc-title{
  font-size: 54px;
  font-weight: 900;
  margin: 0;
  color:white;
  line-height: 1;
}
@media (max-width: 992px){
  .rc-title{ font-size: 34px; }
}

/* ====== RIGHT SIDE (BUSCADOR + BOTON) ====== */
.rc-right{
  display:flex;
  align-items:center;
  gap:12px;
  flex-wrap: wrap;
}
.rc-search{
  display:flex;
  align-items:center;
  gap:10px;
  background:#1f1f1f;
  border:1px solid rgba(255,255,255,.15);
  border-radius: 10px;
  padding: 8px 10px;
}
.rc-search i{ color:#ffc107; }
.rc-search input{
  background: transparent;
  border: none;
  outline: none;
  color: #fff;
  width: 300px;
  font-weight: 800;
}
.rc-search input::placeholder{ color: rgba(255,255,255,.55); }
.rc-clear{
  background: transparent;
  border: none;
  color: rgba(255,255,255,.75);
  font-weight: 900;
  cursor:pointer;
  padding: 6px 8px;
  border-radius: 8px;
}
.rc-clear:hover{ background: rgba(255,255,255,.08); }

@media (max-width: 576px){
  
  .rc-search input{ width: 280px; }
  
  .rc-btn-all{ width: 94px; }
  
  .rc-btn-all .txt{display:none;}

}

/* ====== BTN ALL ====== */
.rc-btn-all{
  display:inline-flex;
  align-items:center;
  gap:10px;
  background:#ffc107;
  color:#111;
  border:none;
  padding: 12px 18px;
  border-radius: 8px;
  font-weight: 900;
  text-decoration:none;
}

.rc-btn-all .rc-btn-icon{
  background:#111;
  color:#fff;
  width:36px;
  height:36px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius: 6px;
}

.rc-btn-plus{
  display:inline-flex;
  align-items:center;
  gap:10px;
  background:white;
  color:#111;
  border:none;
  padding: 12px 18px;
  border-radius: 8px;
  font-weight: 900;
  text-decoration:none;
}

.rc-btn-plus .rc-btn-icon{
  background:#111;
  color:#fff;
  width:36px;
  height:36px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius: 6px;
}

/* ====== GRID (3 POR FILA EN PC) ====== */
.rc-grid{
  display:grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
}
@media (max-width: 1400px){
  .rc-grid{ grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 992px){
  .rc-grid{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 576px){
  .rc-grid{ grid-template-columns: 1fr; }
}

/* ====== CARD ====== */
.rc-card{
  background:#f5f5f5;
  border-radius: 22px;
  padding: 18px;
  overflow:hidden;
  min-height: 420px;
  position:relative;
}

/* ====== IMAGE ====== */
.rc-carimg-wrap{
  width:100%;
  height: 190px;
  display:flex;
  align-items:center;
  justify-content:center;
  margin-bottom: 12px;
}
.rc-carimg{
  max-width: 92%;
  max-height: 180px;
  object-fit: contain;
  filter: drop-shadow(0 10px 10px rgba(0,0,0,.12));
}

/* ====== title + price row ====== */
.rc-row{
  display:flex;
  align-items:flex-end;
  justify-content:space-between;
  gap: 10px;
  margin-top: 2px;
}
.rc-name{
  font-size: 15px;
  font-weight: 900;
  color:#111;
  margin:0;
  text-transform: none;
  line-height: 1.1;
}
.rc-sub{
  margin: 5px 0 0 0;
  color:#777;
  font-weight:700;
  font-size: 12px;
}
.rc-price{
  font-size: 16px;
  font-weight: 900;
  color:#111;
  margin:0;
  white-space:nowrap;
}
.rc-price small{
  font-size: 12px;
  font-weight: 700;
  color:#777;
}

/* divider */
.rc-divider{
  height:1px;
  background: rgba(0,0,0,.10);
  margin: 12px 0;
}

/* ====== specs (CENTRADO) ====== */
.rc-specs{
  display:grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
  text-align:center;
}
.rc-spec{
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  gap:4px;
}
.rc-spec i{
  font-size:18px;
  color:#111;
  opacity:.85;
}
.rc-spec .label{
  font-size:11px;
  color:#666;
  margin:0;
  line-height:1;
}
.rc-spec .value{
  font-size:13px;
  font-weight:900;
  color:#111;
  margin:0;
  line-height:1.1;
  text-transform: uppercase;
}

/* ====== actions (SOLO ICONOS + TOOLTIP) ====== */
.rc-actions{
  margin-top: 12px;
  display:flex;
  flex-wrap:wrap;
  gap: 8px;
}
.rc-actions a{
  position:relative;
  text-decoration:none;
  font-weight: 900;
  border-radius: 10px;
  padding: 9px 10px;
  border: 1px solid rgba(0,0,0,.10);
  background:#fff;
  color:#111;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width: 40px;
}
.rc-actions a i{ font-size: 15px; }
.rc-actions a:hover{ background:#f0f0f0; }
.rc-actions a.main{
  background:#ffc107;
  border-color:#ffc107;
}
.rc-actions a.main:hover{
  background:#e0a800;
  border-color:#e0a800;
}
.rc-actions a.danger{
  background:#dc3545;
  border-color:#dc3545;
  color:#fff;
}
.rc-actions a.danger:hover{
  background:#c82333;
  border-color:#c82333;
  color:#fff;
}

/* tooltip */
.rc-actions a::after{
  content: attr(data-title);
  position:absolute;
  bottom:120%;
  left:50%;
  transform:translateX(-50%);
  background:#111;
  color:#fff;
  padding:6px 10px;
  font-size:12px;
  border-radius:6px;
  white-space:nowrap;
  opacity:0;
  pointer-events:none;
  transition: all .15s ease;
  z-index: 20;
}
.rc-actions a::before{
  content:"";
  position:absolute;
  bottom:110%;
  left:50%;
  transform:translateX(-50%);
  border:6px solid transparent;
  border-top-color:#111;
  opacity:0;
  transition: all .15s ease;
  z-index: 20;
}
.rc-actions a:hover::after,
.rc-actions a:hover::before,
.rc-actions a:focus::after,
.rc-actions a:focus::before{
  opacity:1;
}

/* ====== EMPTY RESULT ====== */
.rc-empty{
  display:none;
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  color:#fff;
  padding: 14px 16px;
  border-radius: 12px;
  margin-top: 14px;
  font-weight: 900;
}
</style>

<script>
function confirmAction(message){
  return confirm(message);
}

/* ✅ BUSCADOR EN VIVO (FILTRA TARJETAS SIN RECARGAR) */
document.addEventListener("DOMContentLoaded", function(){
  var input = document.getElementById("rcQuickSearch");
  var clearBtn = document.getElementById("rcClearSearch");
  var cards = Array.from(document.querySelectorAll(".rc-card[data-search]"));
  var emptyBox = document.getElementById("rcEmpty");

  function normalize(s){
    return (s || "").toString().toLowerCase().trim();
  }

  function filterCards(){
    var q = normalize(input.value);
    var visible = 0;

    cards.forEach(function(card){
      var haystack = normalize(card.getAttribute("data-search"));
      var show = (q === "" || haystack.indexOf(q) !== -1);
      card.style.display = show ? "" : "none";
      if(show) visible++;
    });

    if(emptyBox){
      emptyBox.style.display = (visible === 0) ? "block" : "none";
    }
  }

  if(input){
    input.addEventListener("input", filterCards);
    input.addEventListener("keyup", function(e){
      if(e.key === "Escape"){
        input.value = "";
        filterCards();
      }
    });
  }

  if(clearBtn){
    clearBtn.addEventListener("click", function(e){
      e.preventDefault();
      if(input){
        input.value = "";
        input.focus();
        filterCards();
      }
    });
  }
});
</script>

<section class="rc-section">
  <div class="container">

    <div class="rc-header">
      <h2 class="rc-title"><i class='fa fa-car'></i> <?php 
      switch (Core::$user->language){
        case 'ES': echo "Todos los Vehículos"; break;
        case 'EN': echo "All Vehicles"; break;
      }
      ?></h2>
       <a class="rc-btn-plus" href="./?view=cars&opt=new">
         CREAR NUEVO
          <span class="rc-btn-icon"><i class="fa fa-plus"></i></span>
        </a>

      <div class="rc-right">

        <!-- ✅ BUSCADOR RAPIDO (SIN FORM / SIN ACTION) -->
        <div class="rc-search">
          <i class="fa fa-search"></i>
          <input id="rcQuickSearch" type="text"
                 placeholder="<?php switch (Core::$user->language){ case 'ES': echo "Buscar por marca, modelo, placa, año, color..."; break; case 'EN': echo "Search by brand, model, plate, year, color..."; break; } ?>">
          <button class="rc-clear" id="rcClearSearch" type="button">
            <?php switch (Core::$user->language){ case 'ES': echo "LIMPIAR"; break; case 'EN': echo "CLEAR"; break; } ?>
          </button>
        </div>

        <!-- BOTON VER TODOS -->
       
 <?php
/* =========================
   CONTADORES (BADGES)
   - Ajusta el nombre de la tabla si no es "cars"
   - Ajusta los status si en tu sistema son distintos
========================= */
$cars_table = "cars"; // 👈 si tu tabla se llama distinto, cámbiala aquí

$base = new Database();
$con  = $base->connect();

$stock_id = StockData::getPrincipal()->id;

// ✅ Disponible (según tu lógica vieja: status=0 o status=3)
$qAvail  = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id AND status=0");
$count_available = (int)($qAvail ? $qAvail->fetch_assoc()['c'] : 0);

// ✅ Reservados (ASUMIDO: status=1)
$qRes    = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id AND status=1");
$count_reserved = (int)($qRes ? $qRes->fetch_assoc()['c'] : 0);

// ✅ Rentados (ASUMIDO: status=2)
$qRent   = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id AND status=2");
$count_rented = (int)($qRent ? $qRent->fetch_assoc()['c'] : 0);

// ✅ Todos
$qAll    = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id");
$count_all = (int)($qAll ? $qAll->fetch_assoc()['c'] : 0);
?>

<style>
/* ====== BADGE (NUMERITO) ====== */
.rc-btn-icon{ position:relative; }  /* para poder posicionar el badge */
.rc-badge{
  position:absolute;
  top:-8px;
  right:-10px;
  background:#dc3545;
  color:#fff;
  font-weight:900;
  font-size:12px;
  min-width:22px;
  height:22px;
  padding:0 6px;
  border-radius: 999px;
  display:flex;
  align-items:center;
  justify-content:center;
  border:2px solid #111; /* se ve bien sobre el botón amarillo */
}

/* ✅ En móvil, el botón se ve como icono y el badge queda perfecto */
@media (max-width: 576px){
  .rc-badge{
    top:-6px;
    right:-8px;
    min-width:20px;
    height:20px;
    font-size:11px;
  }
}
</style>

<!-- =========================
     BOTONES CON ICONO + CONTADOR
========================= -->

<a class="rc-btn-all" href="./?view=cars&opt=available">
  <span class="txt">DISPONIBLE</span>
  <span class="rc-btn-icon">
    <i class="fa fa-car"></i>
    <span class="rc-badge"><?php echo $count_available; ?></span>
  </span>
</a>

<a class="rc-btn-all" href="./?view=cars&opt=reserved">
  <span class="txt">RESERVADOS</span>
  <span class="rc-btn-icon">
    <i class="fa fa-calendar-check"></i>
    <span class="rc-badge"><?php echo $count_reserved; ?></span>
  </span>
</a>

<a class="rc-btn-all" href="./?view=cars&opt=rented">
  <span class="txt">RENTADO</span>
  <span class="rc-btn-icon">
    <i class="fa fa-key"></i>
    <span class="rc-badge"><?php echo $count_rented; ?></span>
  </span>
</a>

<a class="rc-btn-all" href="./?view=cars&opt=all">
  <span class="txt">TODOS</span>
  <span class="rc-btn-icon">
    <i class="fa fa-list"></i>
    <span class="rc-badge"><?php echo $count_all; ?></span>
  </span>
</a>


      </div>
    </div>

    <div id="rcEmpty" class="rc-empty">
      <?php switch (Core::$user->language){ case 'ES': echo "No se encontraron vehículos con esa búsqueda."; break; case 'EN': echo "No vehicles found for that search."; break; } ?>
    </div>

    <div class="rc-grid">

      <?php foreach($products as $sells):?>

        <?php
          // ✅ Texto que se usa para buscar (puedes agregar más campos si quieres)
          $searchText = trim(
            $sells->getBrand()->name." ".$sells->name." | ".
            $sells->getExColor()->name." | ".
            $sells->year." | ".
            $sells->plate." | ".
            $sells->token." | ".
            $sells->getCategory()->name
          );
          $searchTextAttr = htmlspecialchars($searchText, ENT_QUOTES, 'UTF-8');
        ?>

        <div class="rc-card" data-search="<?php echo $searchTextAttr; ?>">

          <div class="rc-carimg-wrap">
            <?php if(!empty($sells->invoice_file)):?>
  <img class="rc-carimg"
       src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>"
       alt="car"
       loading="lazy"
       decoding="async"
       width="900"
       height="450">
<?php else:?>
  <img class="rc-carimg"
       src="https://via.placeholder.com/900x450?text=NO+IMAGE"
       alt="car"
       loading="lazy"
       decoding="async"
       width="900"
       height="450">
<?php endif;?>
          </div>

          <div class="rc-row">
            <div>
              <h3 class="rc-name"><?php echo $sells->getBrand()->name." ".$sells->name; ?></h3>
              <p class="rc-sub">
                <?php echo strtoupper($sells->getExColor()->name); ?> • <?php echo strtoupper($sells->year); ?>
              </p>
            </div>

            <p class="rc-price">
              <?php echo "$".number_format((float)$sells->price, 0); ?>
              <small>/Por dia</small>
            </p>
          </div>

          <div class="rc-divider"></div>

          <div class="rc-specs">

            <div class="rc-spec">
              <i class="fa fa-user"></i>
              <div>
                <p class="label">Asiento</p>
                <p class="value"><?php echo !empty($sells->seats) ? $sells->seats : "4"; ?></p>
              </div>
            </div>

            <div class="rc-spec">
              <i class="fa fa-cogs"></i>
              <div>
                <p class="label">Transmision</p>
                <p class="value"><?php echo !empty($sells->transmission) ? $sells->transmission : "Automatica"; ?></p>
              </div>
            </div>

            <div class="rc-spec">
              <i class="fa fa-credit-card"></i>
              <div>
                <p class="label">Placa</p>
                <p class="value"><?php echo !empty($sells->plate) ? $sells->plate : "No Tiene"; ?></p>
              </div>
            </div>

          </div>

          <div class="rc-actions">

            <a href="./?view=cars&opt=description&id=<?php echo $sells->id;?>"
               data-title="INFO"><i class="fa fa-eye"></i></a>

            <a href="./?view=gallery&opt=all&id=<?php echo $sells->id;?>"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "GALERIA"; break; case 'EN': echo "GALLERY"; break; } ?>">
               <i class="fa fa-image"></i></a>

            <a class="main" href="./?view=cars&opt=edit&id=<?php echo $sells->id;?>"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "EDITAR"; break; case 'EN': echo "EDIT"; break; } ?>">
               <i class="fa fa-edit"></i></a>

            <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=5"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "VENDIDO"; break; case 'EN': echo "SOLD"; break; } ?>"
               onclick="return confirmAction('<?php
                  switch (Core::$user->language){
                    case 'ES': echo "¿Estás seguro de que deseas marcar este vehículo como VENDIDO?"; break;
                    case 'EN': echo "Are you sure you want to mark this vehicle as SOLD?"; break;
                  }
               ?>');"><i class="fa fa-dollar-sign"></i></a>

            <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=4"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "TALLER"; break; case 'EN': echo "WORKSHOP"; break; } ?>"
               onclick="return confirmAction('<?php
                  switch (Core::$user->language){
                    case 'ES': echo "¿Estás seguro de que deseas pasar este vehículo a TALLER?"; break;
                    case 'EN': echo "Are you sure you want to move this vehicle to WORKSHOP?"; break;
                  }
               ?>');"><i class="fa fa-cog"></i></a>
               
                <a class="main" style="background-color:red; color: white;" href="./?view=cars&opt=history&id=<?php echo $sells->id;?>"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "RESERVAS"; break; case 'EN': echo "RESERVED"; break; } ?>">
               <i class="fa fa-history"></i></a>

          
          </div>

        </div>

      <?php endforeach;?>

    </div>

  </div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="available"):$go = isset($_GET["go"]) ? $_GET["go"] : "";

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
  ✅ AHORA EL BUSCADOR ES 100% EN VIVO (SIN ACTION / SIN RECARGAR)
  - Ya NO se usa go=name para filtrar en el servidor.
  - Se cargan los vehículos normalmente y el buscador filtra las tarjetas con JS.
*/
$products = CarsData::getAllBySQL("where status=0 and stock_id=".StockData::getPrincipal()->id);?>

<!-- =========================
     SECTION COMPLETO (BUSCADOR EN VIVO SIN ACTION)
========================= -->

<style>
/* ====== SECTION HEADER ====== */
.rc-section{ padding: 30px 0; }
.rc-header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:15px;
  margin-bottom: 18px;
  flex-wrap: wrap;
}
.rc-title{
  font-size: 54px;
  font-weight: 900;
  margin: 0;
  color:white;
  line-height: 1;
}
@media (max-width: 992px){
  .rc-title{ font-size: 34px; }
}

/* ====== RIGHT SIDE (BUSCADOR + BOTON) ====== */
.rc-right{
  display:flex;
  align-items:center;
  gap:12px;
  flex-wrap: wrap;
}
.rc-search{
  display:flex;
  align-items:center;
  gap:10px;
  background:#1f1f1f;
  border:1px solid rgba(255,255,255,.15);
  border-radius: 10px;
  padding: 8px 10px;
}
.rc-search i{ color:#ffc107; }
.rc-search input{
  background: transparent;
  border: none;
  outline: none;
  color: #fff;
  width: 300px;
  font-weight: 800;
}
.rc-search input::placeholder{ color: rgba(255,255,255,.55); }
.rc-clear{
  background: transparent;
  border: none;
  color: rgba(255,255,255,.75);
  font-weight: 900;
  cursor:pointer;
  padding: 6px 8px;
  border-radius: 8px;
}
.rc-clear:hover{ background: rgba(255,255,255,.08); }

@media (max-width: 576px){
  .rc-search input{ width: 280px; }
  
  .rc-btn-all{ width: 94px; }
  
  .rc-btn-all .txt{display:none;}
}

/* ====== BTN ALL ====== */
.rc-btn-all{
  display:inline-flex;
  align-items:center;
  gap:10px;
  background:#ffc107;
  color:#111;
  border:none;
  padding: 12px 18px;
  border-radius: 8px;
  font-weight: 900;
  text-decoration:none;
}

.rc-btn-all .rc-btn-icon{
  background:#111;
  color:#fff;
  width:36px;
  height:36px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius: 6px;
}

.rc-btn-plus{
  display:inline-flex;
  align-items:center;
  gap:10px;
  background:white;
  color:#111;
  border:none;
  padding: 12px 18px;
  border-radius: 8px;
  font-weight: 900;
  text-decoration:none;
}

.rc-btn-plus .rc-btn-icon{
  background:#111;
  color:#fff;
  width:36px;
  height:36px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius: 6px;
}

/* ====== GRID (3 POR FILA EN PC) ====== */
.rc-grid{
  display:grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
}
@media (max-width: 1400px){
  .rc-grid{ grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 992px){
  .rc-grid{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 576px){
  .rc-grid{ grid-template-columns: 1fr; }
}

/* ====== CARD ====== */
.rc-card{
  background:#f5f5f5;
  border-radius: 22px;
  padding: 18px;
  overflow:hidden;
  min-height: 420px;
  position:relative;
}

/* ====== IMAGE ====== */
.rc-carimg-wrap{
  width:100%;
  height: 190px;
  display:flex;
  align-items:center;
  justify-content:center;
  margin-bottom: 12px;
}
.rc-carimg{
  max-width: 92%;
  max-height: 180px;
  object-fit: contain;
  filter: drop-shadow(0 10px 10px rgba(0,0,0,.12));
}

/* ====== title + price row ====== */
.rc-row{
  display:flex;
  align-items:flex-end;
  justify-content:space-between;
  gap: 10px;
  margin-top: 2px;
}
.rc-name{
  font-size: 15px;
  font-weight: 900;
  color:#111;
  margin:0;
  text-transform: none;
  line-height: 1.1;
}
.rc-sub{
  margin: 5px 0 0 0;
  color:#777;
  font-weight:700;
  font-size: 12px;
}
.rc-price{
  font-size: 16px;
  font-weight: 900;
  color:#111;
  margin:0;
  white-space:nowrap;
}
.rc-price small{
  font-size: 12px;
  font-weight: 700;
  color:#777;
}

/* divider */
.rc-divider{
  height:1px;
  background: rgba(0,0,0,.10);
  margin: 12px 0;
}

/* ====== specs (CENTRADO) ====== */
.rc-specs{
  display:grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
  text-align:center;
}
.rc-spec{
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  gap:4px;
}
.rc-spec i{
  font-size:18px;
  color:#111;
  opacity:.85;
}
.rc-spec .label{
  font-size:11px;
  color:#666;
  margin:0;
  line-height:1;
}
.rc-spec .value{
  font-size:13px;
  font-weight:900;
  color:#111;
  margin:0;
  line-height:1.1;
  text-transform: uppercase;
}

/* ====== actions (SOLO ICONOS + TOOLTIP) ====== */
.rc-actions{
  margin-top: 12px;
  display:flex;
  flex-wrap:wrap;
  gap: 8px;
}
.rc-actions a{
  position:relative;
  text-decoration:none;
  font-weight: 900;
  border-radius: 10px;
  padding: 9px 10px;
  border: 1px solid rgba(0,0,0,.10);
  background:#fff;
  color:#111;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width: 40px;
}
.rc-actions a i{ font-size: 15px; }
.rc-actions a:hover{ background:#f0f0f0; }
.rc-actions a.main{
  background:#ffc107;
  border-color:#ffc107;
}
.rc-actions a.main:hover{
  background:#e0a800;
  border-color:#e0a800;
}
.rc-actions a.danger{
  background:#dc3545;
  border-color:#dc3545;
  color:#fff;
}
.rc-actions a.danger:hover{
  background:#c82333;
  border-color:#c82333;
  color:#fff;
}

/* tooltip */
.rc-actions a::after{
  content: attr(data-title);
  position:absolute;
  bottom:120%;
  left:50%;
  transform:translateX(-50%);
  background:#111;
  color:#fff;
  padding:6px 10px;
  font-size:12px;
  border-radius:6px;
  white-space:nowrap;
  opacity:0;
  pointer-events:none;
  transition: all .15s ease;
  z-index: 20;
}
.rc-actions a::before{
  content:"";
  position:absolute;
  bottom:110%;
  left:50%;
  transform:translateX(-50%);
  border:6px solid transparent;
  border-top-color:#111;
  opacity:0;
  transition: all .15s ease;
  z-index: 20;
}
.rc-actions a:hover::after,
.rc-actions a:hover::before,
.rc-actions a:focus::after,
.rc-actions a:focus::before{
  opacity:1;
}

/* ====== EMPTY RESULT ====== */
.rc-empty{
  display:none;
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  color:#fff;
  padding: 14px 16px;
  border-radius: 12px;
  margin-top: 14px;
  font-weight: 900;
}
</style>

<script>
function confirmAction(message){
  return confirm(message);
}

/* ✅ BUSCADOR EN VIVO (FILTRA TARJETAS SIN RECARGAR) */
document.addEventListener("DOMContentLoaded", function(){
  var input = document.getElementById("rcQuickSearch");
  var clearBtn = document.getElementById("rcClearSearch");
  var cards = Array.from(document.querySelectorAll(".rc-card[data-search]"));
  var emptyBox = document.getElementById("rcEmpty");

  function normalize(s){
    return (s || "").toString().toLowerCase().trim();
  }

  function filterCards(){
    var q = normalize(input.value);
    var visible = 0;

    cards.forEach(function(card){
      var haystack = normalize(card.getAttribute("data-search"));
      var show = (q === "" || haystack.indexOf(q) !== -1);
      card.style.display = show ? "" : "none";
      if(show) visible++;
    });

    if(emptyBox){
      emptyBox.style.display = (visible === 0) ? "block" : "none";
    }
  }

  if(input){
    input.addEventListener("input", filterCards);
    input.addEventListener("keyup", function(e){
      if(e.key === "Escape"){
        input.value = "";
        filterCards();
      }
    });
  }

  if(clearBtn){
    clearBtn.addEventListener("click", function(e){
      e.preventDefault();
      if(input){
        input.value = "";
        input.focus();
        filterCards();
      }
    });
  }
});
</script>

<section class="rc-section">
  <div class="container">

    <div class="rc-header">
      <h2 class="rc-title"><i class='fa fa-car'></i> <?php 
      switch (Core::$user->language){
        case 'ES': echo "Vehículos Disponible"; break;
        case 'EN': echo "Available vehicles"; break;
      }
      ?></h2>
       <a class="rc-btn-plus" href="./?view=cars&opt=new">
         CREAR NUEVO
          <span class="rc-btn-icon"><i class="fa fa-plus"></i></span>
        </a>

      <div class="rc-right">

        <!-- ✅ BUSCADOR RAPIDO (SIN FORM / SIN ACTION) -->
        <div class="rc-search">
          <i class="fa fa-search"></i>
          <input id="rcQuickSearch" type="text"
                 placeholder="<?php switch (Core::$user->language){ case 'ES': echo "Buscar por marca, modelo, placa, año, color..."; break; case 'EN': echo "Search by brand, model, plate, year, color..."; break; } ?>">
          <button class="rc-clear" id="rcClearSearch" type="button">
            <?php switch (Core::$user->language){ case 'ES': echo "LIMPIAR"; break; case 'EN': echo "CLEAR"; break; } ?>
          </button>
        </div>

        <!-- BOTON VER TODOS -->
       
        
 <?php
/* =========================
   CONTADORES (BADGES)
   - Ajusta el nombre de la tabla si no es "cars"
   - Ajusta los status si en tu sistema son distintos
========================= */
$cars_table = "cars"; // 👈 si tu tabla se llama distinto, cámbiala aquí

$base = new Database();
$con  = $base->connect();

$stock_id = StockData::getPrincipal()->id;

// ✅ Disponible (según tu lógica vieja: status=0 o status=3)
$qAvail  = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id AND status=0");
$count_available = (int)($qAvail ? $qAvail->fetch_assoc()['c'] : 0);

// ✅ Reservados (ASUMIDO: status=1)
$qRes    = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id AND status=1");
$count_reserved = (int)($qRes ? $qRes->fetch_assoc()['c'] : 0);

// ✅ Rentados (ASUMIDO: status=2)
$qRent   = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id AND status=2");
$count_rented = (int)($qRent ? $qRent->fetch_assoc()['c'] : 0);

// ✅ Todos
$qAll    = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id");
$count_all = (int)($qAll ? $qAll->fetch_assoc()['c'] : 0);
?>

<style>
/* ====== BADGE (NUMERITO) ====== */
.rc-btn-icon{ position:relative; }  /* para poder posicionar el badge */
.rc-badge{
  position:absolute;
  top:-8px;
  right:-10px;
  background:#dc3545;
  color:#fff;
  font-weight:900;
  font-size:12px;
  min-width:22px;
  height:22px;
  padding:0 6px;
  border-radius: 999px;
  display:flex;
  align-items:center;
  justify-content:center;
  border:2px solid #111; /* se ve bien sobre el botón amarillo */
}

/* ✅ En móvil, el botón se ve como icono y el badge queda perfecto */
@media (max-width: 576px){
  .rc-badge{
    top:-6px;
    right:-8px;
    min-width:20px;
    height:20px;
    font-size:11px;
  }
}
</style>

<!-- =========================
     BOTONES CON ICONO + CONTADOR
========================= -->

<a class="rc-btn-all" href="./?view=cars&opt=available">
  <span class="txt">DISPONIBLE</span>
  <span class="rc-btn-icon">
    <i class="fa fa-car"></i>
    <span class="rc-badge"><?php echo $count_available; ?></span>
  </span>
</a>

<a class="rc-btn-all" href="./?view=cars&opt=reserved">
  <span class="txt">RESERVADOS</span>
  <span class="rc-btn-icon">
    <i class="fa fa-calendar-check"></i>
    <span class="rc-badge"><?php echo $count_reserved; ?></span>
  </span>
</a>

<a class="rc-btn-all" href="./?view=cars&opt=rented">
  <span class="txt">RENTADO</span>
  <span class="rc-btn-icon">
    <i class="fa fa-key"></i>
    <span class="rc-badge"><?php echo $count_rented; ?></span>
  </span>
</a>

<a class="rc-btn-all" href="./?view=cars&opt=all">
  <span class="txt">TODOS</span>
  <span class="rc-btn-icon">
    <i class="fa fa-list"></i>
    <span class="rc-badge"><?php echo $count_all; ?></span>
  </span>
</a>


      </div>
    </div>

    <div id="rcEmpty" class="rc-empty">
      <?php switch (Core::$user->language){ case 'ES': echo "No se encontraron vehículos con esa búsqueda."; break; case 'EN': echo "No vehicles found for that search."; break; } ?>
    </div>

    <div class="rc-grid">

      <?php foreach($products as $sells):?>

        <?php
          // ✅ Texto que se usa para buscar (puedes agregar más campos si quieres)
          $searchText = trim(
            $sells->getBrand()->name." ".$sells->name." | ".
            $sells->getExColor()->name." | ".
            $sells->year." | ".
            $sells->plate." | ".
            $sells->token." | ".
            $sells->getCategory()->name
          );
          $searchTextAttr = htmlspecialchars($searchText, ENT_QUOTES, 'UTF-8');
        ?>

        <div class="rc-card" data-search="<?php echo $searchTextAttr; ?>">

          <div class="rc-carimg-wrap">
            <?php if(!empty($sells->invoice_file)):?>
  <img class="rc-carimg"
       src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>"
       alt="car"
       loading="lazy"
       decoding="async"
       width="900"
       height="450">
<?php else:?>
  <img class="rc-carimg"
       src="https://via.placeholder.com/900x450?text=NO+IMAGE"
       alt="car"
       loading="lazy"
       decoding="async"
       width="900"
       height="450">
<?php endif;?>
          </div>

          <div class="rc-row">
            <div>
              <h3 class="rc-name"><?php echo $sells->getBrand()->name." ".$sells->name; ?></h3>
              <p class="rc-sub">
                <?php echo strtoupper($sells->getExColor()->name); ?> • <?php echo strtoupper($sells->year); ?>
              </p>
            </div>

            <p class="rc-price">
              <?php echo "$".number_format((float)$sells->price, 0); ?>
              <small>/Por dia</small>
            </p>
          </div>

          <div class="rc-divider"></div>

          <div class="rc-specs">

            <div class="rc-spec">
              <i class="fa fa-user"></i>
              <div>
                <p class="label">Asiento</p>
                <p class="value"><?php echo !empty($sells->seats) ? $sells->seats : "4"; ?></p>
              </div>
            </div>

            <div class="rc-spec">
              <i class="fa fa-cogs"></i>
              <div>
                <p class="label">Transmision</p>
                <p class="value"><?php echo !empty($sells->transmission) ? $sells->transmission : "Automatica"; ?></p>
              </div>
            </div>

            <div class="rc-spec">
              <i class="fa fa-credit-card"></i>
              <div>
                <p class="label">Placa</p>
                <p class="value"><?php echo !empty($sells->plate) ? $sells->plate : "No Tiene"; ?></p>
              </div>
            </div>

          </div>

          <div class="rc-actions">

            <a href="./?view=cars&opt=description&id=<?php echo $sells->id;?>"
               data-title="INFO"><i class="fa fa-eye"></i></a>

            <a href="./?view=gallery&opt=all&id=<?php echo $sells->id;?>"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "GALERIA"; break; case 'EN': echo "GALLERY"; break; } ?>">
               <i class="fa fa-image"></i></a>

            <a class="main" href="./?view=cars&opt=edit&id=<?php echo $sells->id;?>"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "EDITAR"; break; case 'EN': echo "EDIT"; break; } ?>">
               <i class="fa fa-edit"></i></a>

            <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=5"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "VENDIDO"; break; case 'EN': echo "SOLD"; break; } ?>"
               onclick="return confirmAction('<?php
                  switch (Core::$user->language){
                    case 'ES': echo "¿Estás seguro de que deseas marcar este vehículo como VENDIDO?"; break;
                    case 'EN': echo "Are you sure you want to mark this vehicle as SOLD?"; break;
                  }
               ?>');"><i class="fa fa-dollar-sign"></i></a>

            <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=4"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "TALLER"; break; case 'EN': echo "WORKSHOP"; break; } ?>"
               onclick="return confirmAction('<?php
                  switch (Core::$user->language){
                    case 'ES': echo "¿Estás seguro de que deseas pasar este vehículo a TALLER?"; break;
                    case 'EN': echo "Are you sure you want to move this vehicle to WORKSHOP?"; break;
                  }
               ?>');"><i class="fa fa-cog"></i></a>
               
                <a class="main" style="background-color:red; color: white;" href="./?view=cars&opt=history&id=<?php echo $sells->id;?>"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "RESERVAS"; break; case 'EN': echo "RESERVED"; break; } ?>">
               <i class="fa fa-history"></i></a>

            <?php
              $base = new Database();
              $con = $base->connect();
              $sl = "select SQL_BIG_RESULT * from permits_user where user_id=".$_SESSION["user_id"];
              $qry = $con->query($sl);

              while($x = $qry->fetch_array()){
                if ($x['permits_id']==4):
            ?>
              <a class="danger"
                 href="./?action=cars&opt=del&id=<?php echo $sells->id;?>"
                 data-title="<?php switch (Core::$user->language){ case 'ES': echo "ELIMINAR"; break; case 'EN': echo "DELETE"; break; } ?>"
                 onclick="return confirmAction('<?php
                    switch (Core::$user->language){
                      case 'ES': echo "¿Estás seguro de que deseas eliminar este registro?"; break;
                      case 'EN': echo "Are you sure you want to delete this record?"; break;
                    }
                 ?>');"><i class="fa fa-trash"></i></a>
            <?php
                endif;
              }
            ?>

          </div>

        </div>

      <?php endforeach;?>

    </div>

  </div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="reserved"):$go = isset($_GET["go"]) ? $_GET["go"] : "";

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
  ✅ AHORA EL BUSCADOR ES 100% EN VIVO (SIN ACTION / SIN RECARGAR)
  - Ya NO se usa go=name para filtrar en el servidor.
  - Se cargan los vehículos normalmente y el buscador filtra las tarjetas con JS.
*/
$products = CarsData::getAllBySQL("where status=1 and stock_id=".StockData::getPrincipal()->id);?>

<!-- =========================
     SECTION COMPLETO (BUSCADOR EN VIVO SIN ACTION)
========================= -->

<style>
/* ====== SECTION HEADER ====== */
.rc-section{ padding: 30px 0; }
.rc-header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:15px;
  margin-bottom: 18px;
  flex-wrap: wrap;
}
.rc-title{
  font-size: 54px;
  font-weight: 900;
  margin: 0;
  color:white;
  line-height: 1;
}
@media (max-width: 992px){
  .rc-title{ font-size: 34px; }
}

/* ====== RIGHT SIDE (BUSCADOR + BOTON) ====== */
.rc-right{
  display:flex;
  align-items:center;
  gap:12px;
  flex-wrap: wrap;
}
.rc-search{
  display:flex;
  align-items:center;
  gap:10px;
  background:#1f1f1f;
  border:1px solid rgba(255,255,255,.15);
  border-radius: 10px;
  padding: 8px 10px;
}
.rc-search i{ color:#ffc107; }
.rc-search input{
  background: transparent;
  border: none;
  outline: none;
  color: #fff;
  width: 300px;
  font-weight: 800;
}
.rc-search input::placeholder{ color: rgba(255,255,255,.55); }
.rc-clear{
  background: transparent;
  border: none;
  color: rgba(255,255,255,.75);
  font-weight: 900;
  cursor:pointer;
  padding: 6px 8px;
  border-radius: 8px;
}
.rc-clear:hover{ background: rgba(255,255,255,.08); }

@media (max-width: 576px){
  
  .rc-search input{ width: 280px; }
  
  .rc-btn-all{ width: 94px; }
  
  .rc-btn-all .txt{display:none;}
}

/* ====== BTN ALL ====== */
.rc-btn-all{
  display:inline-flex;
  align-items:center;
  gap:10px;
  background:#ffc107;
  color:#111;
  border:none;
  padding: 12px 18px;
  border-radius: 8px;
  font-weight: 900;
  text-decoration:none;
}

.rc-btn-all .rc-btn-icon{
  background:#111;
  color:#fff;
  width:36px;
  height:36px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius: 6px;
}

.rc-btn-plus{
  display:inline-flex;
  align-items:center;
  gap:10px;
  background:white;
  color:#111;
  border:none;
  padding: 12px 18px;
  border-radius: 8px;
  font-weight: 900;
  text-decoration:none;
}

.rc-btn-plus .rc-btn-icon{
  background:#111;
  color:#fff;
  width:36px;
  height:36px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius: 6px;
}

/* ====== GRID (3 POR FILA EN PC) ====== */
.rc-grid{
  display:grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
}
@media (max-width: 1400px){
  .rc-grid{ grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 992px){
  .rc-grid{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 576px){
  .rc-grid{ grid-template-columns: 1fr; }
}

/* ====== CARD ====== */
.rc-card{
  background:#f5f5f5;
  border-radius: 22px;
  padding: 18px;
  overflow:hidden;
  min-height: 420px;
  position:relative;
}

/* ====== IMAGE ====== */
.rc-carimg-wrap{
  width:100%;
  height: 190px;
  display:flex;
  align-items:center;
  justify-content:center;
  margin-bottom: 12px;
}
.rc-carimg{
  max-width: 92%;
  max-height: 180px;
  object-fit: contain;
  filter: drop-shadow(0 10px 10px rgba(0,0,0,.12));
}

/* ====== title + price row ====== */
.rc-row{
  display:flex;
  align-items:flex-end;
  justify-content:space-between;
  gap: 10px;
  margin-top: 2px;
}
.rc-name{
  font-size: 15px;
  font-weight: 900;
  color:#111;
  margin:0;
  text-transform: none;
  line-height: 1.1;
}
.rc-sub{
  margin: 5px 0 0 0;
  color:#777;
  font-weight:700;
  font-size: 12px;
}
.rc-price{
  font-size: 16px;
  font-weight: 900;
  color:#111;
  margin:0;
  white-space:nowrap;
}
.rc-price small{
  font-size: 12px;
  font-weight: 700;
  color:#777;
}

/* divider */
.rc-divider{
  height:1px;
  background: rgba(0,0,0,.10);
  margin: 12px 0;
}

/* ====== specs (CENTRADO) ====== */
.rc-specs{
  display:grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
  text-align:center;
}
.rc-spec{
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  gap:4px;
}
.rc-spec i{
  font-size:18px;
  color:#111;
  opacity:.85;
}
.rc-spec .label{
  font-size:11px;
  color:#666;
  margin:0;
  line-height:1;
}
.rc-spec .value{
  font-size:13px;
  font-weight:900;
  color:#111;
  margin:0;
  line-height:1.1;
  text-transform: uppercase;
}

/* ====== actions (SOLO ICONOS + TOOLTIP) ====== */
.rc-actions{
  margin-top: 12px;
  display:flex;
  flex-wrap:wrap;
  gap: 8px;
}
.rc-actions a{
  position:relative;
  text-decoration:none;
  font-weight: 900;
  border-radius: 10px;
  padding: 9px 10px;
  border: 1px solid rgba(0,0,0,.10);
  background:#fff;
  color:#111;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width: 40px;
}
.rc-actions a i{ font-size: 15px; }
.rc-actions a:hover{ background:#f0f0f0; }
.rc-actions a.main{
  background:#ffc107;
  border-color:#ffc107;
}
.rc-actions a.main:hover{
  background:#e0a800;
  border-color:#e0a800;
}
.rc-actions a.danger{
  background:#dc3545;
  border-color:#dc3545;
  color:#fff;
}
.rc-actions a.danger:hover{
  background:#c82333;
  border-color:#c82333;
  color:#fff;
}

/* tooltip */
.rc-actions a::after{
  content: attr(data-title);
  position:absolute;
  bottom:120%;
  left:50%;
  transform:translateX(-50%);
  background:#111;
  color:#fff;
  padding:6px 10px;
  font-size:12px;
  border-radius:6px;
  white-space:nowrap;
  opacity:0;
  pointer-events:none;
  transition: all .15s ease;
  z-index: 20;
}
.rc-actions a::before{
  content:"";
  position:absolute;
  bottom:110%;
  left:50%;
  transform:translateX(-50%);
  border:6px solid transparent;
  border-top-color:#111;
  opacity:0;
  transition: all .15s ease;
  z-index: 20;
}
.rc-actions a:hover::after,
.rc-actions a:hover::before,
.rc-actions a:focus::after,
.rc-actions a:focus::before{
  opacity:1;
}

/* ====== EMPTY RESULT ====== */
.rc-empty{
  display:none;
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  color:#fff;
  padding: 14px 16px;
  border-radius: 12px;
  margin-top: 14px;
  font-weight: 900;
}
</style>

<script>
function confirmAction(message){
  return confirm(message);
}

/* ✅ BUSCADOR EN VIVO (FILTRA TARJETAS SIN RECARGAR) */
document.addEventListener("DOMContentLoaded", function(){
  var input = document.getElementById("rcQuickSearch");
  var clearBtn = document.getElementById("rcClearSearch");
  var cards = Array.from(document.querySelectorAll(".rc-card[data-search]"));
  var emptyBox = document.getElementById("rcEmpty");

  function normalize(s){
    return (s || "").toString().toLowerCase().trim();
  }

  function filterCards(){
    var q = normalize(input.value);
    var visible = 0;

    cards.forEach(function(card){
      var haystack = normalize(card.getAttribute("data-search"));
      var show = (q === "" || haystack.indexOf(q) !== -1);
      card.style.display = show ? "" : "none";
      if(show) visible++;
    });

    if(emptyBox){
      emptyBox.style.display = (visible === 0) ? "block" : "none";
    }
  }

  if(input){
    input.addEventListener("input", filterCards);
    input.addEventListener("keyup", function(e){
      if(e.key === "Escape"){
        input.value = "";
        filterCards();
      }
    });
  }

  if(clearBtn){
    clearBtn.addEventListener("click", function(e){
      e.preventDefault();
      if(input){
        input.value = "";
        input.focus();
        filterCards();
      }
    });
  }
});
</script>

<section class="rc-section">
  <div class="container">

    <div class="rc-header">
      <h2 class="rc-title"><i class='fa fa-car'></i> <?php 
      switch (Core::$user->language){
        case 'ES': echo "Vehículos Reservados"; break;
        case 'EN': echo "Reserved Vehicles"; break;
      }
      ?></h2>
       <a class="rc-btn-plus" href="./?view=cars&opt=new">
         CREAR NUEVO
          <span class="rc-btn-icon"><i class="fa fa-plus"></i></span>
        </a>

      <div class="rc-right">

        <!-- ✅ BUSCADOR RAPIDO (SIN FORM / SIN ACTION) -->
        <div class="rc-search">
          <i class="fa fa-search"></i>
          <input id="rcQuickSearch" type="text"
                 placeholder="<?php switch (Core::$user->language){ case 'ES': echo "Buscar por marca, modelo, placa, año, color..."; break; case 'EN': echo "Search by brand, model, plate, year, color..."; break; } ?>">
          <button class="rc-clear" id="rcClearSearch" type="button">
            <?php switch (Core::$user->language){ case 'ES': echo "LIMPIAR"; break; case 'EN': echo "CLEAR"; break; } ?>
          </button>
        </div>

        <!-- BOTON VER TODOS -->
       
        
<?php
/* =========================
   CONTADORES (BADGES)
   - Ajusta el nombre de la tabla si no es "cars"
   - Ajusta los status si en tu sistema son distintos
========================= */
$cars_table = "cars"; // 👈 si tu tabla se llama distinto, cámbiala aquí

$base = new Database();
$con  = $base->connect();

$stock_id = StockData::getPrincipal()->id;

// ✅ Disponible (según tu lógica vieja: status=0 o status=3)
$qAvail  = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id AND status=0");
$count_available = (int)($qAvail ? $qAvail->fetch_assoc()['c'] : 0);

// ✅ Reservados (ASUMIDO: status=1)
$qRes    = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id AND status=1");
$count_reserved = (int)($qRes ? $qRes->fetch_assoc()['c'] : 0);

// ✅ Rentados (ASUMIDO: status=2)
$qRent   = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id AND status=2");
$count_rented = (int)($qRent ? $qRent->fetch_assoc()['c'] : 0);

// ✅ Todos
$qAll    = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id");
$count_all = (int)($qAll ? $qAll->fetch_assoc()['c'] : 0);
?>

<style>
/* ====== BADGE (NUMERITO) ====== */
.rc-btn-icon{ position:relative; }  /* para poder posicionar el badge */
.rc-badge{
  position:absolute;
  top:-8px;
  right:-10px;
  background:#dc3545;
  color:#fff;
  font-weight:900;
  font-size:12px;
  min-width:22px;
  height:22px;
  padding:0 6px;
  border-radius: 999px;
  display:flex;
  align-items:center;
  justify-content:center;
  border:2px solid #111; /* se ve bien sobre el botón amarillo */
}

/* ✅ En móvil, el botón se ve como icono y el badge queda perfecto */
@media (max-width: 576px){
  .rc-badge{
    top:-6px;
    right:-8px;
    min-width:20px;
    height:20px;
    font-size:11px;
  }
}
</style>

<!-- =========================
     BOTONES CON ICONO + CONTADOR
========================= -->

<a class="rc-btn-all" href="./?view=cars&opt=available">
  <span class="txt">DISPONIBLE</span>
  <span class="rc-btn-icon">
    <i class="fa fa-car"></i>
    <span class="rc-badge"><?php echo $count_available; ?></span>
  </span>
</a>

<a class="rc-btn-all" href="./?view=cars&opt=reserved">
  <span class="txt">RESERVADOS</span>
  <span class="rc-btn-icon">
    <i class="fa fa-calendar-check"></i>
    <span class="rc-badge"><?php echo $count_reserved; ?></span>
  </span>
</a>

<a class="rc-btn-all" href="./?view=cars&opt=rented">
  <span class="txt">RENTADO</span>
  <span class="rc-btn-icon">
    <i class="fa fa-key"></i>
    <span class="rc-badge"><?php echo $count_rented; ?></span>
  </span>
</a>

<a class="rc-btn-all" href="./?view=cars&opt=all">
  <span class="txt">TODOS</span>
  <span class="rc-btn-icon">
    <i class="fa fa-list"></i>
    <span class="rc-badge"><?php echo $count_all; ?></span>
  </span>
</a>



      </div>
    </div>

    <div id="rcEmpty" class="rc-empty">
      <?php switch (Core::$user->language){ case 'ES': echo "No se encontraron vehículos con esa búsqueda."; break; case 'EN': echo "No vehicles found for that search."; break; } ?>
    </div>

    <div class="rc-grid">

      <?php foreach($products as $sells):?>

        <?php
          // ✅ Texto que se usa para buscar (puedes agregar más campos si quieres)
          $searchText = trim(
            $sells->getBrand()->name." ".$sells->name." | ".
            $sells->getExColor()->name." | ".
            $sells->year." | ".
            $sells->plate." | ".
            $sells->token." | ".
            $sells->getCategory()->name
          );
          $searchTextAttr = htmlspecialchars($searchText, ENT_QUOTES, 'UTF-8');
        ?>

        <div class="rc-card" data-search="<?php echo $searchTextAttr; ?>">

          <div class="rc-carimg-wrap">
           <?php if(!empty($sells->invoice_file)):?>
  <img class="rc-carimg"
       src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>"
       alt="car"
       loading="lazy"
       decoding="async"
       width="900"
       height="450">
<?php else:?>
  <img class="rc-carimg"
       src="https://via.placeholder.com/900x450?text=NO+IMAGE"
       alt="car"
       loading="lazy"
       decoding="async"
       width="900"
       height="450">
<?php endif;?>
          </div>

          <div class="rc-row">
            <div>
              <h3 class="rc-name"><?php echo $sells->getBrand()->name." ".$sells->name; ?></h3>
              <p class="rc-sub">
                <?php echo strtoupper($sells->getExColor()->name); ?> • <?php echo strtoupper($sells->year); ?>
              </p>
            </div>

            <p class="rc-price">
              <?php echo "$".number_format((float)$sells->price, 0); ?>
              <small>/Por dia</small>
            </p>
          </div>

          <div class="rc-divider"></div>

          <div class="rc-specs">

            <div class="rc-spec">
              <i class="fa fa-user"></i>
              <div>
                <p class="label">Asiento</p>
                <p class="value"><?php echo !empty($sells->seats) ? $sells->seats : "4"; ?></p>
              </div>
            </div>

            <div class="rc-spec">
              <i class="fa fa-cogs"></i>
              <div>
                <p class="label">Transmision</p>
                <p class="value"><?php echo !empty($sells->transmission) ? $sells->transmission : "Automatica"; ?></p>
              </div>
            </div>

            <div class="rc-spec">
              <i class="fa fa-credit-card"></i>
              <div>
                <p class="label">Placa</p>
                <p class="value"><?php echo !empty($sells->plate) ? $sells->plate : "No Tiene"; ?></p>
              </div>
            </div>

          </div>

          <div class="rc-actions">

            <a href="./?view=cars&opt=description&id=<?php echo $sells->id;?>"
               data-title="INFO"><i class="fa fa-eye"></i></a>

            <a href="./?view=gallery&opt=all&id=<?php echo $sells->id;?>"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "GALERIA"; break; case 'EN': echo "GALLERY"; break; } ?>">
               <i class="fa fa-image"></i></a>

            <a class="main" href="./?view=cars&opt=edit&id=<?php echo $sells->id;?>"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "EDITAR"; break; case 'EN': echo "EDIT"; break; } ?>">
               <i class="fa fa-edit"></i></a>

            <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=5"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "VENDIDO"; break; case 'EN': echo "SOLD"; break; } ?>"
               onclick="return confirmAction('<?php
                  switch (Core::$user->language){
                    case 'ES': echo "¿Estás seguro de que deseas marcar este vehículo como VENDIDO?"; break;
                    case 'EN': echo "Are you sure you want to mark this vehicle as SOLD?"; break;
                  }
               ?>');"><i class="fa fa-dollar-sign"></i></a>

            <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=4"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "TALLER"; break; case 'EN': echo "WORKSHOP"; break; } ?>"
               onclick="return confirmAction('<?php
                  switch (Core::$user->language){
                    case 'ES': echo "¿Estás seguro de que deseas pasar este vehículo a TALLER?"; break;
                    case 'EN': echo "Are you sure you want to move this vehicle to WORKSHOP?"; break;
                  }
               ?>');"><i class="fa fa-cog"></i></a>
               
               <a class="main" style="background-color:red; color: white;" href="./?view=cars&opt=history&id=<?php echo $sells->id;?>"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "RESERVAS"; break; case 'EN': echo "RESERVED"; break; } ?>">
               <i class="fa fa-history"></i></a>

          

          </div>

        </div>

      <?php endforeach;?>

    </div>

  </div>
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="rented"):$go = isset($_GET["go"]) ? $_GET["go"] : "";

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
  ✅ AHORA EL BUSCADOR ES 100% EN VIVO (SIN ACTION / SIN RECARGAR)
  - Ya NO se usa go=name para filtrar en el servidor.
  - Se cargan los vehículos normalmente y el buscador filtra las tarjetas con JS.
*/
$products = CarsData::getAllBySQL("where status=2 and stock_id=".StockData::getPrincipal()->id);?>

<!-- =========================
     SECTION COMPLETO (BUSCADOR EN VIVO SIN ACTION)
========================= -->

<style>
/* ====== SECTION HEADER ====== */
.rc-section{ padding: 30px 0; }
.rc-header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:15px;
  margin-bottom: 18px;
  flex-wrap: wrap;
}
.rc-title{
  font-size: 54px;
  font-weight: 900;
  margin: 0;
  color:white;
  line-height: 1;
}
@media (max-width: 992px){
  .rc-title{ font-size: 34px; }
}

/* ====== RIGHT SIDE (BUSCADOR + BOTON) ====== */
.rc-right{
  display:flex;
  align-items:center;
  gap:12px;
  flex-wrap: wrap;
}
.rc-search{
  display:flex;
  align-items:center;
  gap:10px;
  background:#1f1f1f;
  border:1px solid rgba(255,255,255,.15);
  border-radius: 10px;
  padding: 8px 10px;
}
.rc-search i{ color:#ffc107; }
.rc-search input{
  background: transparent;
  border: none;
  outline: none;
  color: #fff;
  width: 300px;
  font-weight: 800;
}
.rc-search input::placeholder{ color: rgba(255,255,255,.55); }
.rc-clear{
  background: transparent;
  border: none;
  color: rgba(255,255,255,.75);
  font-weight: 900;
  cursor:pointer;
  padding: 6px 8px;
  border-radius: 8px;
}
.rc-clear:hover{ background: rgba(255,255,255,.08); }

@media (max-width: 576px){
  
  .rc-search input{ width: 280px; }
  
  .rc-btn-all{ width: 94px; }
  
  .rc-btn-all .txt{display:none;}
}

/* ====== BTN ALL ====== */
.rc-btn-all{
  display:inline-flex;
  align-items:center;
  gap:10px;
  background:#ffc107;
  color:#111;
  border:none;
  padding: 12px 18px;
  border-radius: 8px;
  font-weight: 900;
  text-decoration:none;
}

.rc-btn-all .rc-btn-icon{
  background:#111;
  color:#fff;
  width:36px;
  height:36px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius: 6px;
}

.rc-btn-plus{
  display:inline-flex;
  align-items:center;
  gap:10px;
  background:white;
  color:#111;
  border:none;
  padding: 12px 18px;
  border-radius: 8px;
  font-weight: 900;
  text-decoration:none;
}

.rc-btn-plus .rc-btn-icon{
  background:#111;
  color:#fff;
  width:36px;
  height:36px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius: 6px;
}

/* ====== GRID (3 POR FILA EN PC) ====== */
.rc-grid{
  display:grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
}
@media (max-width: 1400px){
  .rc-grid{ grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 992px){
  .rc-grid{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 576px){
  .rc-grid{ grid-template-columns: 1fr; }
}

/* ====== CARD ====== */
.rc-card{
  background:#f5f5f5;
  border-radius: 22px;
  padding: 18px;
  overflow:hidden;
  min-height: 420px;
  position:relative;
}

/* ====== IMAGE ====== */
.rc-carimg-wrap{
  width:100%;
  height: 190px;
  display:flex;
  align-items:center;
  justify-content:center;
  margin-bottom: 12px;
}
.rc-carimg{
  max-width: 92%;
  max-height: 180px;
  object-fit: contain;
  filter: drop-shadow(0 10px 10px rgba(0,0,0,.12));
}

/* ====== title + price row ====== */
.rc-row{
  display:flex;
  align-items:flex-end;
  justify-content:space-between;
  gap: 10px;
  margin-top: 2px;
}
.rc-name{
  font-size: 15px;
  font-weight: 900;
  color:#111;
  margin:0;
  text-transform: none;
  line-height: 1.1;
}
.rc-sub{
  margin: 5px 0 0 0;
  color:#777;
  font-weight:700;
  font-size: 12px;
}
.rc-price{
  font-size: 16px;
  font-weight: 900;
  color:#111;
  margin:0;
  white-space:nowrap;
}
.rc-price small{
  font-size: 12px;
  font-weight: 700;
  color:#777;
}

/* divider */
.rc-divider{
  height:1px;
  background: rgba(0,0,0,.10);
  margin: 12px 0;
}

/* ====== specs (CENTRADO) ====== */
.rc-specs{
  display:grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
  text-align:center;
}
.rc-spec{
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  gap:4px;
}
.rc-spec i{
  font-size:18px;
  color:#111;
  opacity:.85;
}
.rc-spec .label{
  font-size:11px;
  color:#666;
  margin:0;
  line-height:1;
}
.rc-spec .value{
  font-size:13px;
  font-weight:900;
  color:#111;
  margin:0;
  line-height:1.1;
  text-transform: uppercase;
}

/* ====== actions (SOLO ICONOS + TOOLTIP) ====== */
.rc-actions{
  margin-top: 12px;
  display:flex;
  flex-wrap:wrap;
  gap: 8px;
}
.rc-actions a{
  position:relative;
  text-decoration:none;
  font-weight: 900;
  border-radius: 10px;
  padding: 9px 10px;
  border: 1px solid rgba(0,0,0,.10);
  background:#fff;
  color:#111;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width: 40px;
}
.rc-actions a i{ font-size: 15px; }
.rc-actions a:hover{ background:#f0f0f0; }
.rc-actions a.main{
  background:#ffc107;
  border-color:#ffc107;
}
.rc-actions a.main:hover{
  background:#e0a800;
  border-color:#e0a800;
}
.rc-actions a.danger{
  background:#dc3545;
  border-color:#dc3545;
  color:#fff;
}
.rc-actions a.danger:hover{
  background:#c82333;
  border-color:#c82333;
  color:#fff;
}

/* tooltip */
.rc-actions a::after{
  content: attr(data-title);
  position:absolute;
  bottom:120%;
  left:50%;
  transform:translateX(-50%);
  background:#111;
  color:#fff;
  padding:6px 10px;
  font-size:12px;
  border-radius:6px;
  white-space:nowrap;
  opacity:0;
  pointer-events:none;
  transition: all .15s ease;
  z-index: 20;
}
.rc-actions a::before{
  content:"";
  position:absolute;
  bottom:110%;
  left:50%;
  transform:translateX(-50%);
  border:6px solid transparent;
  border-top-color:#111;
  opacity:0;
  transition: all .15s ease;
  z-index: 20;
}
.rc-actions a:hover::after,
.rc-actions a:hover::before,
.rc-actions a:focus::after,
.rc-actions a:focus::before{
  opacity:1;
}

/* ====== EMPTY RESULT ====== */
.rc-empty{
  display:none;
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  color:#fff;
  padding: 14px 16px;
  border-radius: 12px;
  margin-top: 14px;
  font-weight: 900;
}
</style>

<script>
function confirmAction(message){
  return confirm(message);
}

/* ✅ BUSCADOR EN VIVO (FILTRA TARJETAS SIN RECARGAR) */
document.addEventListener("DOMContentLoaded", function(){
  var input = document.getElementById("rcQuickSearch");
  var clearBtn = document.getElementById("rcClearSearch");
  var cards = Array.from(document.querySelectorAll(".rc-card[data-search]"));
  var emptyBox = document.getElementById("rcEmpty");

  function normalize(s){
    return (s || "").toString().toLowerCase().trim();
  }

  function filterCards(){
    var q = normalize(input.value);
    var visible = 0;

    cards.forEach(function(card){
      var haystack = normalize(card.getAttribute("data-search"));
      var show = (q === "" || haystack.indexOf(q) !== -1);
      card.style.display = show ? "" : "none";
      if(show) visible++;
    });

    if(emptyBox){
      emptyBox.style.display = (visible === 0) ? "block" : "none";
    }
  }

  if(input){
    input.addEventListener("input", filterCards);
    input.addEventListener("keyup", function(e){
      if(e.key === "Escape"){
        input.value = "";
        filterCards();
      }
    });
  }

  if(clearBtn){
    clearBtn.addEventListener("click", function(e){
      e.preventDefault();
      if(input){
        input.value = "";
        input.focus();
        filterCards();
      }
    });
  }
});
</script>

<section class="rc-section">
  <div class="container">

    <div class="rc-header">
      <h2 class="rc-title"><i class='fa fa-car'></i> <?php 
      switch (Core::$user->language){
        case 'ES': echo "Vehículos Rentados"; break;
        case 'EN': echo "Rented Vehicles"; break;
      }
      ?></h2>
       <a class="rc-btn-plus" href="./?view=cars&opt=new">
         CREAR NUEVO
          <span class="rc-btn-icon"><i class="fa fa-plus"></i></span>
        </a>

      <div class="rc-right">

        <!-- ✅ BUSCADOR RAPIDO (SIN FORM / SIN ACTION) -->
        <div class="rc-search">
          <i class="fa fa-search"></i>
          <input id="rcQuickSearch" type="text"
                 placeholder="<?php switch (Core::$user->language){ case 'ES': echo "Buscar por marca, modelo, placa, año, color..."; break; case 'EN': echo "Search by brand, model, plate, year, color..."; break; } ?>">
          <button class="rc-clear" id="rcClearSearch" type="button">
            <?php switch (Core::$user->language){ case 'ES': echo "LIMPIAR"; break; case 'EN': echo "CLEAR"; break; } ?>
          </button>
        </div>

        <!-- BOTON VER TODOS -->
       
  <?php
/* =========================
   CONTADORES (BADGES)
   - Ajusta el nombre de la tabla si no es "cars"
   - Ajusta los status si en tu sistema son distintos
========================= */
$cars_table = "cars"; // 👈 si tu tabla se llama distinto, cámbiala aquí

$base = new Database();
$con  = $base->connect();

$stock_id = StockData::getPrincipal()->id;

// ✅ Disponible (según tu lógica vieja: status=0 o status=3)
$qAvail  = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id AND status=0");
$count_available = (int)($qAvail ? $qAvail->fetch_assoc()['c'] : 0);

// ✅ Reservados (ASUMIDO: status=1)
$qRes    = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id AND status=1");
$count_reserved = (int)($qRes ? $qRes->fetch_assoc()['c'] : 0);

// ✅ Rentados (ASUMIDO: status=2)
$qRent   = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id AND status=2");
$count_rented = (int)($qRent ? $qRent->fetch_assoc()['c'] : 0);

// ✅ Todos
$qAll    = $con->query("SELECT COUNT(*) AS c FROM $cars_table WHERE stock_id=$stock_id");
$count_all = (int)($qAll ? $qAll->fetch_assoc()['c'] : 0);
?>

<style>
/* ====== BADGE (NUMERITO) ====== */
.rc-btn-icon{ position:relative; }  /* para poder posicionar el badge */
.rc-badge{
  position:absolute;
  top:-8px;
  right:-10px;
  background:#dc3545;
  color:#fff;
  font-weight:900;
  font-size:12px;
  min-width:22px;
  height:22px;
  padding:0 6px;
  border-radius: 999px;
  display:flex;
  align-items:center;
  justify-content:center;
  border:2px solid #111; /* se ve bien sobre el botón amarillo */
}

/* ✅ En móvil, el botón se ve como icono y el badge queda perfecto */
@media (max-width: 576px){
  .rc-badge{
    top:-6px;
    right:-8px;
    min-width:20px;
    height:20px;
    font-size:11px;
  }
}
</style>

<!-- =========================
     BOTONES CON ICONO + CONTADOR
========================= -->

<a class="rc-btn-all" href="./?view=cars&opt=available">
  <span class="txt">DISPONIBLE</span>
  <span class="rc-btn-icon">
    <i class="fa fa-car"></i>
    <span class="rc-badge"><?php echo $count_available; ?></span>
  </span>
</a>

<a class="rc-btn-all" href="./?view=cars&opt=reserved">
  <span class="txt">RESERVADOS</span>
  <span class="rc-btn-icon">
    <i class="fa fa-calendar-check"></i>
    <span class="rc-badge"><?php echo $count_reserved; ?></span>
  </span>
</a>

<a class="rc-btn-all" href="./?view=cars&opt=rented">
  <span class="txt">RENTADO</span>
  <span class="rc-btn-icon">
    <i class="fa fa-key"></i>
    <span class="rc-badge"><?php echo $count_rented; ?></span>
  </span>
</a>

<a class="rc-btn-all" href="./?view=cars&opt=all">
  <span class="txt">TODOS</span>
  <span class="rc-btn-icon">
    <i class="fa fa-list"></i>
    <span class="rc-badge"><?php echo $count_all; ?></span>
  </span>
</a>



      </div>
    </div>

    <div id="rcEmpty" class="rc-empty">
      <?php switch (Core::$user->language){ case 'ES': echo "No se encontraron vehículos con esa búsqueda."; break; case 'EN': echo "No vehicles found for that search."; break; } ?>
    </div>

    <div class="rc-grid">

      <?php foreach($products as $sells):?>

        <?php
          // ✅ Texto que se usa para buscar (puedes agregar más campos si quieres)
          $searchText = trim(
            $sells->getBrand()->name." ".$sells->name." | ".
            $sells->getExColor()->name." | ".
            $sells->year." | ".
            $sells->plate." | ".
            $sells->token." | ".
            $sells->getCategory()->name
          );
          $searchTextAttr = htmlspecialchars($searchText, ENT_QUOTES, 'UTF-8');
        ?>

        <div class="rc-card" data-search="<?php echo $searchTextAttr; ?>">

          <div class="rc-carimg-wrap">
            <?php if(!empty($sells->invoice_file)):?>
  <img class="rc-carimg"
       src="CF-SYSTEMS/storage/invoice_files/<?php echo $sells->invoice_file; ?>"
       alt="car"
       loading="lazy"
       decoding="async"
       width="900"
       height="450">
<?php else:?>
  <img class="rc-carimg"
       src="https://via.placeholder.com/900x450?text=NO+IMAGE"
       alt="car"
       loading="lazy"
       decoding="async"
       width="900"
       height="450">
<?php endif;?>
          </div>

          <div class="rc-row">
            <div>
              <h3 class="rc-name"><?php echo $sells->getBrand()->name." ".$sells->name; ?></h3>
              <p class="rc-sub">
                <?php echo strtoupper($sells->getExColor()->name); ?> • <?php echo strtoupper($sells->year); ?>
              </p>
            </div>

            <p class="rc-price">
              <?php echo "$".number_format((float)$sells->price, 0); ?>
              <small>/Por dia</small>
            </p>
          </div>

          <div class="rc-divider"></div>

          <div class="rc-specs">

            <div class="rc-spec">
              <i class="fa fa-user"></i>
              <div>
                <p class="label">Asiento</p>
                <p class="value"><?php echo !empty($sells->seats) ? $sells->seats : "4"; ?></p>
              </div>
            </div>

            <div class="rc-spec">
              <i class="fa fa-cogs"></i>
              <div>
                <p class="label">Transmision</p>
                <p class="value"><?php echo !empty($sells->transmission) ? $sells->transmission : "Automatica"; ?></p>
              </div>
            </div>

            <div class="rc-spec">
              <i class="fa fa-credit-card"></i>
              <div>
                <p class="label">Placa</p>
                <p class="value"><?php echo !empty($sells->plate) ? $sells->plate : "No Tiene"; ?></p>
              </div>
            </div>

          </div>

          <div class="rc-actions">

            <a href="./?view=cars&opt=description&id=<?php echo $sells->id;?>"
               data-title="INFO"><i class="fa fa-eye"></i></a>

            <a href="./?view=gallery&opt=all&id=<?php echo $sells->id;?>"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "GALERIA"; break; case 'EN': echo "GALLERY"; break; } ?>">
               <i class="fa fa-image"></i></a>

            <a class="main" href="./?view=cars&opt=edit&id=<?php echo $sells->id;?>"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "EDITAR"; break; case 'EN': echo "EDIT"; break; } ?>">
               <i class="fa fa-edit"></i></a>

            <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=5"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "VENDIDO"; break; case 'EN': echo "SOLD"; break; } ?>"
               onclick="return confirmAction('<?php
                  switch (Core::$user->language){
                    case 'ES': echo "¿Estás seguro de que deseas marcar este vehículo como VENDIDO?"; break;
                    case 'EN': echo "Are you sure you want to mark this vehicle as SOLD?"; break;
                  }
               ?>');"><i class="fa fa-dollar-sign"></i></a>

            <a href="./?action=cars&opt=status&id=<?php echo $sells->id;?>&status=4"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "TALLER"; break; case 'EN': echo "WORKSHOP"; break; } ?>"
               onclick="return confirmAction('<?php
                  switch (Core::$user->language){
                    case 'ES': echo "¿Estás seguro de que deseas pasar este vehículo a TALLER?"; break;
                    case 'EN': echo "Are you sure you want to move this vehicle to WORKSHOP?"; break;
                  }
               ?>');"><i class="fa fa-cog"></i></a>
               
                <a class="main" style="background-color:red; color: white;" href="./?view=cars&opt=history&id=<?php echo $sells->id;?>"
               data-title="<?php switch (Core::$user->language){ case 'ES': echo "RESERVAS"; break; case 'EN': echo "RESERVED"; break; } ?>">
               <i class="fa fa-history"></i></a>

            

          </div>

        </div>

      <?php endforeach;?>

    </div>

  </div>
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="cogs"):?>

<section class="content">
<div class="row">
  <div class="col-md-12">
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><i class='fa fa-car'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Listado de Vehiculos"; break;
 case 'EN': echo "List of Vehicles"; break;
}
?></h1>
          </div><!-- /.col -->
          
           <div class="col-sm-6">
  <ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">
      <i class='fa fa-history'></i> 
      <span id="reloj"></span>
    </li>
  </ol>
</div><!-- /.col -->

<script>
// Función para actualizar la hora en tiempo real
function actualizarReloj() {
  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  document.getElementById("reloj").textContent = `${horas}:${minutos}:${segundos}`;
}

// Actualiza cada segundo
setInterval(actualizarReloj, 1000);
actualizarReloj(); // Llamada inicial
</script>
    
    </div>
            
 <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
    
       <form id="ssearch">
     <div class="input-group">
           <span class="input-group-text"><i class="fa fa-search"></i></span>
<input style="background-color:#222;" type="hidden" name="view" value="sell">
        <input style="background-color:#222;" type="search"  aria-label="Search" autocomplete="off" id="product_name" name="product_name" class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Buscar Modelo o Año"; break;  case 'EN': echo "Search Model or Year"; break; } ?>">
      </form>
    </div>
  </div>
 
    

      <script type="text/javascript">
  $("#product_name").keyup(function(){
//    $("#searchp").submit();
searchx();
  });

//  $("#searchp").on("submit",function(e){
  //  e.preventDefault();
function searchx(){
    name = $("#product_name").val();
    console.log(name);
    if(name!=""){
    $.get("./?action=get&opt=cogs&stock=<?php echo StockData::getPrincipal()->id;?>&id=<?php echo $_GET["id"];?>","product_name="+name+"&go=name",function(data){
      $("#allproducts").html(data);
    });
    
    }else{
    $.get("./?action=get&opt=cogs&stock=<?php echo StockData::getPrincipal()->id;?>","",function(data2){
        $("#allproducts").html(data2);
      

  }); 
    }
}
      $.get("./?action=get&opt=cogs&stock=<?php echo StockData::getPrincipal()->id;?>","",function(data2){
        $("#allproducts").html(data2);
        console.log(data2);
      });

   $("#mesero").click(function(){
      $.get("./?action=get&opt=products","",function(data){
        $(".steps").html(data);       
      });
    });

  </script>
    </div>
    <br>

<div id="allproducts"></div>
  
  </div>
</div>
    </div><!-- /.row -->
      </div><!-- /.container-fluid -->
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="new"):?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h1 class="m-0"><i class='fa fa-car'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Agregar Vehiculo"; break;
 case 'EN': echo "Add Vehicle"; break;
}
?></h1>
          </div><!-- /.col -->
          
            <div class="col-sm-6">
  <ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">
      <i class='fa fa-history'></i> 
      <span id="reloj"></span>
    </li>
  </ol>
</div><!-- /.col -->

<script>
// Función para actualizar la hora en tiempo real
function actualizarReloj() {
  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  document.getElementById("reloj").textContent = `${horas}:${minutos}:${segundos}`;
}

// Actualiza cada segundo
setInterval(actualizarReloj, 1000);
actualizarReloj(); // Llamada inicial
</script>

        </div>
        
      
          <div class="card" style="background-color:#222;">
<div class="card-body">
<form method="post" id="form-carro" class="form-horizontal" enctype="multipart/form-data">
 <div class="row">

    <div class="col-md-12 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 control-label"> <?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Vehicle"; break;
 case 'EN': echo "Vehicle photo"; break;
}
?></label>
    <input style="background-color:#222;" type="file" name="image">
    </div>



     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Suplidor"; break;
 case 'EN': echo "Supplier"; break;
}
?></label>
       <select style="background-color:#222;" required name="provider_id" id="provider_id" class="form-control select2" >
    <?php foreach(SuppliersData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
     <div class="col-md-2 col-12" id="provider_price">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Suplidor"; break;
 case 'EN': echo "Price/Supplier"; break;
}
?></label>
      <input style="background-color:#222;" type="number" name="provider_price" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Suplidor"; break;
 case 'EN': echo "Price/Supplier"; break;
}
?>">
    </div>
    
    <script>
    
    document.getElementById("provider_price").style.display = "none";
    
    $('#provider_id').change(function(){
    
    var getSelectValue = document.getElementById("provider_id").value;
    
    if(getSelectValue==<?php echo StockData::getPrincipal()->id;?>){
    document.getElementById("provider_price").style.display = "none";
    }else{
    document.getElementById("provider_price").style.display = "inline-block";   
    }
    
    });
    </script>
    
    
    

     <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "(MI/KMS) Actual"; break;
 case 'EN': echo "(MI/KMS) Current"; break;
}
?></label>
      <input style="background-color:#222;" type="number" name="kms_current" autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "(MI/KMS) Actual"; break;
 case 'EN': echo "(MI/KMS) Current"; break;
}
?>">
    </div>
    
    <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Cambio de Aceite"; break;
 case 'EN': echo "Oil Change"; break;
}
?></label>
      <input style="background-color:#222;" type="number" name="charge_kms" autocomplete="off"  class="form-control" placeholder="(MI/KMS)?">
    </div>


     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Ficha"; break;
 case 'EN': echo "File"; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="token" autocomplete="off"  class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Del Vehiculo"; break;  case 'EN': echo "From the Vehicle"; break; } ?>">
    </div>
 </div>


<div class="row">

     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Marca"; break;
 case 'EN': echo "Brand"; break;
}
?></label>
      <?php $clients = BrandData::getAll();?>
    <select style="background-color:#222;" required name="brand_id" class="form-control select2">
      <option value="">--- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> ---</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

     <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Modelo"; break;
 case 'EN': echo "Model"; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="name" autocomplete="off"  class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Del Vehiculo"; break;  case 'EN': echo "From the Vehicle"; break; } ?>">
    </div>

    <div class="col-md-1 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Año"; break;
 case 'EN': echo "Year"; break;
}
?> </label>
      <input style="background-color:#222;" type="text" value="<?php echo date("Y");?>" name="year" autocomplete="off"  class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Del Vehiculo"; break;  case 'EN': echo "From the Vehicle"; break; } ?>">
    </div>


<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Categoria"; break;
 case 'EN': echo "Category"; break;
}
?></label>
      <?php $clients = CategoryData::getAll();?>
    <select style="background-color:#222;" name="category_id" class="form-control select2" required>
      <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Color interior"; break;
 case 'EN': echo "Interior color"; break;
}
?></label>
      <?php $clients = ColorData::getAll();?>
    <select style="background-color:#222;" name="interior_id" class="form-control select2" required>
     <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Color exterior"; break;
 case 'EN': echo "Exterior color"; break;
}
?></label>
      <?php $clients = ColorData::getAll();?>
    <select style="background-color:#222;" name="exterior_id" class="form-control select2" required>
    <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>


<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Placa"; break;
 case 'EN': echo "Plate No."; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="plate" autocomplete="off" required class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Placa"; break;
 case 'EN': echo "Plate No."; break;
}
?>">
    </div>
    
         
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Transmisión"; break;
 case 'EN': echo "Transmission"; break;
}
?></label>
      <?php $clients = TransmissionData::getAll();?>
    <select style="background-color:#222;" required name="transmission_id" class="form-control select2">
    <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Combustible"; break;
 case 'EN': echo "Fuel"; break;
}
?></label>
      <?php $clients = FuelData::getAll();?>
    <select style="background-color:#222;" required name="fuel_id" class="form-control select2">
    <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach($clients as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
    
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Matricula"; break;
 case 'EN': echo "Registration No."; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="tuition" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Matricula"; break;
 case 'EN': echo "Registration No."; break;
}
?>">
    </div>
    <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Asientos"; break;
 case 'EN': echo "Number of Seats"; break;
}
?></label>
      <input style="background-color:#222;" type="number" required  name="seat" value="5" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Asientos"; break;
 case 'EN': echo "Number of Seats"; break;
}
?>">
    </div>
    
    <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Chasis"; break;
 case 'EN': echo "Chassis No."; break;
}
?></label>
      <input style="background-color:#222;" type="text"  name="chassis" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Chasis"; break;
 case 'EN': echo "Chassis No."; break;
}
?>">
    </div>
    
    
     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Pagina Web"; break;
 case 'EN': echo "Price/Website"; break;
}
?></label>
      <input style="background-color:#222;" type="number" required  name="price" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Pagina Web"; break;
 case 'EN': echo "Price/Website"; break;
}
?>">
    </div>

<div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Seguro de Ley"; break;
 case 'EN': echo "Legal Insurance"; break;
}
?></label>
    <select style="background-color:#222;" name="insurance_id" class="form-control select2">
    <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
        <?php foreach(InsuranceData::getAll() as $ins):?>
        <option value="<?php echo $ins->name;?>"><?php echo $ins->name;?></option>
        <?php endforeach;?>
    </select>
    </div>

  <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
      <input style="background-color:#222;" type="date" name="date_insurance" autocomplete="off" class="form-control">
    </div>

 <div class="col-md-4 col-12">
    
    <input style="background-color:#222;" type="file" class="my-3" name="insurance_file">
</div>

<div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Seguro Full"; break;
 case 'EN': echo "Full Insurance"; break;
}
?></label>
       <select style="background-color:#222;" name="insurance2_id" class="form-control select2">
      <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
        <?php foreach(InsuranceData::getAll() as $ins):?>
        <option value="<?php echo $ins->name;?>"><?php echo $ins->name;?></option>
        <?php endforeach;?>
    </select>
    </div>

  <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
      <input style="background-color:#222;" type="date" name="date2_insurance" autocomplete="off" class="form-control">
    </div>

 <div class="col-md-4 col-12">
    
    <input style="background-color:#222;" type="file" class="my-3" name="insurance2_file">
</div>


</div>
<div class="row my-2">
               
                
                <div class="col-md-12 col-12">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Agregar"; break;
 case 'EN': echo "Add"; break;
}
?></button>
                 
                </div>
              </div>
</form>
</div>
</div>

</div>
</div>
</div>
  </div>
</div>

<script>
function formToJSON(form) {
  const data = new FormData(form);
  const json = {};
  data.forEach((value, key) => {
    if (!json[key]) {
      json[key] = value;
    }
  });
  return json;
}


// Guardar carros localmente si no hay internet
function guardarOffline(carro) {
  let carros = JSON.parse(localStorage.getItem("carros_pendientes")) || [];
  carros.push(carro);
  localStorage.setItem("carros_pendientes", JSON.stringify(carros));
  toastr.info('Guardado localmente. Se enviará cuando vuelva el internet.');
}

// Sincronizar carros cuando vuelva la conexión
function sincronizarcarros() {
  let carros = JSON.parse(localStorage.getItem("carros_pendientes")) || [];
  if (carros.length > 0 && navigator.onLine) {
    carros.forEach((carro, i) => {
      fetch("./?action=cars&opt=add_offline", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(carro)
})

      .then(res => res.text())
      .then(resp => {
        if (resp.trim() === "OK") {
          carros.splice(i, 1);
          localStorage.setItem("carros_pendientes", JSON.stringify(carros));
        }
      });
    });
  }
}

document.getElementById("form-carro").addEventListener("submit", function(e) {
  e.preventDefault();

  const form = document.getElementById("form-carro");
  const carroJSON = formToJSON(form); // Se define aquí

  if (navigator.onLine) {
    const formData = new FormData(form);

    fetch("./?action=cars&opt=add", {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(resp => {
      if (resp.trim() === "OK") {
        toastr.success('Registro agregado correctamente.');
          var delay = 1000;
         setTimeout(function(){ window.location = './?view=cars&opt=all'  }, delay); 
      } else {
        toastr.error('Ya existe ese registro.');
      }
    })
    .catch(() => guardarOffline(carroJSON));
  } else {
    guardarOffline(carroJSON);
  }
});


// Intentar sincronizar cada 5 segundos
setInterval(() => {
  if (navigator.onLine) sincronizarcarros();
}, 5000);

</script>

</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"):
$user = CarsData::getById($_GET["id"]);?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0"><i class='fa fa-edit'></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Editar Vehiculo"; break;
 case 'EN': echo "Edit Vehicle"; break;
}
?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
  <ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">
      <i class='fa fa-history'></i> 
      <span id="reloj"></span>
    </li>
  </ol>
</div><!-- /.col -->

<script>
// Función para actualizar la hora en tiempo real
function actualizarReloj() {
  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  document.getElementById("reloj").textContent = `${horas}:${minutos}:${segundos}`;
}

// Actualiza cada segundo
setInterval(actualizarReloj, 1000);
actualizarReloj(); // Llamada inicial
</script>
        </div>
          <div class="card" style="background-color:#222;">
<div class="card-body">
<form method="post" id="form-carro" class="form-horizontal" enctype="multipart/form-data">
 <div class="row">

  <div class="col-md-12 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"> <?php 
switch (Core::$user->language){
 case 'ES': echo "Foto Vehicle"; break;
 case 'EN': echo "Vehicle photo"; break;
}
?></label>
    <?php if($user->invoice_file!=""):?>
    <a href="./../CF-SYSTEMS/storage/invoice_files/<?php echo $user->invoice_file;?>"  class="btn btn-default"><i class="fa fa-file"></i> Archivo carro (<?php echo $user->invoice_file; ?>)</a>
    <?php endif; ?>
    <input style="background-color:#222;" type="file" class="my-2"  name="image">
    </div>
    

     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Suplidor"; break;
 case 'EN': echo "Supplier"; break;
}
?></label>
       <select style="background-color:#222;" required name="provider_id" id="provider_id" class="form-control select2" >
    <?php foreach(SuppliersData::getAll() as $client):?>
      <option value="<?php echo $client->id;?>"><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
     <div class="col-md-2 col-12" id="provider_price">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Suplidor"; break;
 case 'EN': echo "Price/Supplier"; break;
}
?></label>
      <input style="background-color:#222;" type="number" name="provider_price" value="<?php echo utf8_decode($user->provider_price);?>"  autocomplete="off"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Suplidor"; break;
 case 'EN': echo "Price/Supplier"; break;
}
?>">
    </div>
    
    <script>
    
    document.getElementById("provider_price").style.display = "none";
    
    $('#provider_id').change(function(){
    
    var getSelectValue = document.getElementById("provider_id").value;
    
    if(getSelectValue==<?php echo StockData::getPrincipal()->id;?>){
    document.getElementById("provider_price").style.display = "none";
    }else{
    document.getElementById("provider_price").style.display = "inline-block";   
    }
    
    });
    </script>
    
    
    

     <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "(MI/KMS) Actual"; break;
 case 'EN': echo "(MI/KMS) Current"; break;
}
?></label>
      <input style="background-color:#222;" type="number" name="kms_current" autocomplete="off" value="<?php echo utf8_decode($user->kms_current);?>"   class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "(MI/KMS) Actual"; break;
 case 'EN': echo "(MI/KMS) Current"; break;
}
?>">
    </div>
    
    <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Cambio de Aceite"; break;
 case 'EN': echo "Oil Change"; break;
}
?></label>
      <input style="background-color:#222;" type="number" name="charge_kms" autocomplete="off" value="<?php echo utf8_decode($user->charge_kms);?>"  class="form-control" placeholder="(MI/KMS)?">
    </div>


     <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Ficha"; break;
 case 'EN': echo "File"; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="token" autocomplete="off" value="<?php echo utf8_decode($user->token);?>"  class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Del Vehiculo"; break;  case 'EN': echo "From the Vehicle"; break; } ?>">
    </div>

     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Marca"; break;
 case 'EN': echo "Brand"; break;
}
?></label>
      <?php $clients = BrandData::getAll();?>
    <select style="background-color:#222;" required name="brand_id" class="form-control select2">
      <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach($clients as $client):?>
     <option value="<?php echo $client->id;?>"<?php if($user->brand_id!=null&& $user->brand_id==$client->id){ echo "selected";}?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

     <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Modelo"; break;
 case 'EN': echo "Model"; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="name" value="<?php echo utf8_decode($user->name);?>" autocomplete="off"  class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Del Vehiculo"; break;  case 'EN': echo "From the Vehicle"; break; } ?>">
    </div>

    <div class="col-md-1 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Año"; break;
 case 'EN': echo "Year"; break;
}
?> </label>
      <input style="background-color:#222;" type="text"  value="<?php echo utf8_decode($user->year);?>" name="year" autocomplete="off"  class="form-control" placeholder="<?php  switch (Core::$user->language){  case 'ES': echo "Del Vehiculo"; break;  case 'EN': echo "From the Vehicle"; break; } ?>">
    </div>


<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Categoria"; break;
 case 'EN': echo "Category"; break;
}
?></label>
    <select style="background-color:#222;" name="category_id" class="form-control select2" required>
    <?php foreach(CategoryData::getAll() as $client):?>
     <option value="<?php echo $client->id;?>"<?php if($user->category_id!=null&& $user->category_id==$client->id){ echo "selected";}?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Color interior"; break;
 case 'EN': echo "Interior color"; break;
}
?></label>
    <select style="background-color:#222;" name="interior_id" class="form-control select2" required>
    <?php foreach(ColorData::getAll() as $client):?>
     <option value="<?php echo $client->id;?>"<?php if($user->interior_id!=null&& $user->interior_id==$client->id){ echo "selected";}?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>

<div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Color exterior"; break;
 case 'EN': echo "Exterior color"; break;
}
?></label>
    <select style="background-color:#222;" name="exterior_id" class="form-control select2" required>
    <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
    <?php foreach(ColorData::getAll() as $client):?>
     <option value="<?php echo $client->id;?>"<?php if($user->exterior_id!=null&& $user->exterior_id==$client->id){ echo "selected";}?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>


<div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Placa"; break;
 case 'EN': echo "Plate No."; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="plate"  value="<?php echo utf8_decode($user->plate);?>" autocomplete="off" required class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Placa"; break;
 case 'EN': echo "Plate No."; break;
}
?>">
    </div>
    
    
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Matricula"; break;
 case 'EN': echo "Registration No."; break;
}
?></label>
      <input style="background-color:#222;" type="text" name="tuition" value="<?php echo utf8_decode($user->tuition);?>" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Matricula"; break;
 case 'EN': echo "Registration No."; break;
}
?>">
    </div>
    <div class="col-md-2 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Asientos"; break;
 case 'EN': echo "Number of Seats"; break;
}
?></label>
      <input style="background-color:#222;" type="number" required  name="seat"  value="<?php echo utf8_decode($user->seat);?>"  autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Asientos"; break;
 case 'EN': echo "Number of Seats"; break;
}
?>">
    </div>
    
    
    <div class="col-md-5 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Chasis"; break;
 case 'EN': echo "Chassis No."; break;
}
?></label>
      <input style="background-color:#222;" type="text"  name="chassis" autocomplete="off" value="<?php echo utf8_decode($user->chassis);?>"  class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. de Chasis"; break;
 case 'EN': echo "Chassis No."; break;
}
?>">
    </div>
    
     
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Transmisión"; break;
 case 'EN': echo "Transmission"; break;
}
?></label>
    <select style="background-color:#222;" required name="transmission_id" class="form-control select2">
    <?php foreach(TransmissionData::getAll() as $client):?>
     <option value="<?php echo $client->id;?>"<?php if($user->transmission_id!=null&& $user->transmission_id==$client->id){ echo "selected";}?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
    <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Combustible"; break;
 case 'EN': echo "Fuel"; break;
}
?></label>
    <select style="background-color:#222;" required name="fuel_id" class="form-control select2">
    <?php foreach(FuelData::getAll() as $client):?>
    <option value="<?php echo $client->id;?>"<?php if($user->fuel_id!=null&& $user->fuel_id==$client->id){ echo "selected";}?>><?php echo $client->name;?></option>
    <?php endforeach;?>
      </select>
    </div>
    
     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "No. Bateria"; break;
 case 'EN': echo "No. Battery"; break;
}
?></label>
      <input style="background-color:#222;" type="number"  name="no_batery" value="<?php echo utf8_decode($user->no_batery);?>" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "No. Bateria"; break;
 case 'EN': echo "No. Battery"; break;
}
?>">
    </div>
    
     <div class="col-md-3 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Pagina Web"; break;
 case 'EN': echo "Price/Website"; break;
}
?></label>
      <input style="background-color:#222;" type="number" required  name="price" value="<?php echo utf8_decode($user->price);?>" autocomplete="off" class="form-control" placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Precio/Pagina Web"; break;
 case 'EN': echo "Price/Website"; break;
}
?>">
    </div>

<div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Seguro de Ley"; break;
 case 'EN': echo "Legal Insurance"; break;
}
?></label>
    <select style="background-color:#222;" name="insurance_id" class="form-control select2">
        <?php foreach(InsuranceData::getAll() as $ins):?>
        <option value="<?php echo $ins->name;?>"><?php echo $ins->name;?></option>
        <?php endforeach;?>
    </select>
    </div>

  <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
      <input style="background-color:#222;" type="date" value="<?php echo utf8_decode($user->date_insurance);?>" name="date_insurance" autocomplete="off" class="form-control">
    </div>

 <div class="col-md-4 col-12">
    
    <input style="background-color:#222;" type="file" class="my-3" name="insurance_file">
</div>

<div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Seguro Full"; break;
 case 'EN': echo "Full Insurance"; break;
}
?></label>
       <select style="background-color:#222;" name="insurance2_id" class="form-control select2">
      <option value="">-- <?php  switch (Core::$user->language){  case 'ES': echo "ELEGIR"; break;  case 'EN': echo "CHOOSE"; break; } ?> --</option>
        <?php foreach(InsuranceData::getAll() as $ins):?>
        <option value="<?php echo $ins->name;?>"><?php echo $ins->name;?></option>
        <?php endforeach;?>
    </select>
    </div>

  <div class="col-md-4 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label"><?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento"; break;
 case 'EN': echo "Maturity"; break;
}
?></label>
      <input style="background-color:#222;" type="date" value="<?php echo utf8_decode($user->date2_insurance);?>" name="date2_insurance" autocomplete="off" class="form-control">
    </div>

 <div class="col-md-4 col-12">
    
    <input style="background-color:#222;" type="file" class="my-3" name="insurance2_file">
</div>


</div>
<div class="row my-2">
              
                <input style="background-color:#222;" type="hidden" name="user_id" value="<?php echo $user->id;?>">
                <div class="col-md-12 col-12">
                   <button class="btn btn-primary btn-block btn-sm"><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Actualizar"; break;
 case 'EN': echo "Update"; break;
}
?></button>
                 
                </div>
              </div>
</form>

</div>
</div>

</div>
</div>
</div>
  </div>
</div>

<script>
function formToJSON(form) {
  const data = new FormData(form);
  const json = {};
  data.forEach((value, key) => {
    if (!json[key]) {
      json[key] = value;
    }
  });
  return json;
}


// Guardar carros localmente si no hay internet
function guardarOffline(carro) {
  let carros = JSON.parse(localStorage.getItem("carros_pendientes")) || [];
  carros.push(carro);
  localStorage.setItem("carros_pendientes", JSON.stringify(carros));
  toastr.info('Guardado localmente. Se enviará cuando vuelva el internet.');
}

// Sincronizar carros cuando vuelva la conexión
function sincronizarcarros() {
  let carros = JSON.parse(localStorage.getItem("carros_pendientes")) || [];
  if (carros.length > 0 && navigator.onLine) {
    carros.forEach((carro, i) => {
      fetch("./?action=cars&opt=upd_offline", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(carro)
})

      .then(res => res.text())
      .then(resp => {
        if (resp.trim() === "OK") {
          carros.splice(i, 1);
          localStorage.setItem("carros_pendientes", JSON.stringify(carros));
        }
      });
    });
  }
}

document.getElementById("form-carro").addEventListener("submit", function(e) {
  e.preventDefault();

  const form = document.getElementById("form-carro");
  const carroJSON = formToJSON(form); // Se define aquí

  if (navigator.onLine) {
    const formData = new FormData(form);

    fetch("./?action=cars&opt=upd", {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(resp => {
      if (resp.trim() === "OK") {
        toastr.success('Registro actualizado correctamente.');
         var delay = 1000;
         setTimeout(function(){ window.location = './?view=cars&opt=all'  }, delay); 
      } else {
        toastr.error('Ya existe ese registro.');
      }
    })
    .catch(() => guardarOffline(carroJSON));
  } else {
    guardarOffline(carroJSON);
  }
});


// Intentar sincronizar cada 5 segundos
setInterval(() => {
  if (navigator.onLine) sincronizarcarros();
}, 5000);

</script>


 <div class="floating-buttons">
    <a onclick="history.back()"  class="floating-1"><i class="fa fa-arrow-left"></i></a>
  </div>
  
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="description"):
$user = CarsData::getById($_GET["id"]);?>
<section class="content">
<div class="row">
  <div class="col-md-12">

 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0"><i class='fa fa-car'></i>  <?php  switch (Core::$user->language){  case 'ES': echo "Informacion Del Vehiculo"; break;  case 'EN': echo "Vehicle Information"; break; } ?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
  <ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">
      <i class='fa fa-history'></i> 
      <span id="reloj"></span>
    </li>
  </ol>
</div><!-- /.col -->

<script>
// Función para actualizar la hora en tiempo real
function actualizarReloj() {
  const ahora = new Date();
  const horas = String(ahora.getHours()).padStart(2, '0');
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  document.getElementById("reloj").textContent = `${horas}:${minutos}:${segundos}`;
}

// Actualiza cada segundo
setInterval(actualizarReloj, 1000);
actualizarReloj(); // Llamada inicial
</script>
        </div>

        <div class="row">
            <div class="col-md-4">
            
                  <div class="card" style="background-color:#222;">
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <div class="card-body">
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Nombre del Rent Car"; break;  case 'EN': echo "Rent Car Name"; break; } ?>: </label>
                        <?php echo $user->getStock()->name;?>
                        <br>
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Vehiculo"; break;  case 'EN': echo "Vehicle"; break; } ?>: </label>
                        <?php echo $user->getBrand()->name;?><br>
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Modelo"; break;  case 'EN': echo "Model"; break; } ?>: </label>
                        <?php echo $user->name;?>
                        <br>
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Año"; break;  case 'EN': echo "Year"; break; } ?>: </label>
                        <?php echo $user->year;?>
                        <br>
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Categoria"; break;  case 'EN': echo "Category"; break; } ?>: </label>
                        <?php echo $user->getCategory()->name;?>
                        <br>
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Color Interior"; break;  case 'EN': echo "Interior color"; break; } ?>: </label>
                        <?php echo $user->getInColor()->name;?> 
                        <br>
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Color Exterior"; break;  case 'EN': echo "Exterior color"; break; } ?>: </label>
                        <?php echo $user->getExColor()->name;?>
                       
                      </div>
                    </div>
                  </div>
            </div>


            <div class="col-md-4">
            
                  <div class="card" style="background-color:#222;">
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <div class="card-body">
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Aseguradora"; break;  case 'EN': echo "Insurance"; break; } ?>: </label>
                        <?php echo $user->insurance_id;?>
                        <br>
                        <label><?php  switch (Core::$user->language){  case 'ES': echo "Vencimiento"; break;  case 'EN': echo "Maturity"; break; } ?>: </label>
                        <?php echo  date("d-m-Y",strtotime($user->date_insurance));?>
                        
                        <br>
                        
                    </div>
                  </div>


            </div>
             <div class="card" style="background-color:#222;">
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <center>
                        <label class="my-2"><?php  switch (Core::$user->language){  case 'ES': echo "Foto del Seguro"; break;  case 'EN': echo "Insurance Photo"; break; } ?>: </label>
                          <div class="card-body">
                        <?php if ($user->insurance_file!=""):?>
  <img src="../CF-SYSTEMS/storage/invoice_files/<?php echo $user->insurance_file;?>" class="product-image" style="width: 50%;">
                         <?php endif;?>
                      </div>
                      </center>
                    </div>
                  </div>
            </div>

            <div class="col-md-4">
            
                  <div class="card" style="background-color:#222;">
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <center>
                        <label class="my-2"><?php  switch (Core::$user->language){  case 'ES': echo "Foto Del Vehiculo"; break;  case 'EN': echo "Vehicle Photo"; break; } ?>: </label>
                      <div class="card-body">
                        <?php if ($user->invoice_file!=""):?>
  <img src="../CF-SYSTEMS/storage/invoice_files/<?php echo $user->invoice_file;?>" class="product-image" style="width: 50%;">
                         <?php endif;?>
                      </div>
                    </center>
                  </div>
                </div>




              </div>
</form>
            </div>

            </div>
            </div>
</div>

</div>
</div>
</div>
  </div>
</div>


 <div class="floating-buttons">
    <a onclick="history.back()"  class="floating-1"><i class="fa fa-arrow-left"></i></a>
  </div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="history"): 
$user = CarsData::getById($_GET["id"]);
$TicketMm = StockData::getPrincipal()->ticket_mm;
?>

<section class="content">
<div class="container-fluid">

  <div class="row mb-2">
    <div class="col-sm-8">
      <h1 class="m-0" style="color:white;">
        <i class="fa fa-history"></i> Historial del Vehículo
      </h1>

      <?php if($user): ?>
        <p style="color:#ccc;">
          <strong><?php echo $user->name; ?></strong>
          - Placa: <?php echo $user->plate; ?>
        </p>
      <?php endif; ?>
    </div>

  </div>

  <?php if(!$user): ?>
    <div class="alert alert-danger">Vehículo no encontrado</div>
  <?php else: ?>

  <?php
  $base = new Database();
  $con  = $base->connect();
  mysqli_set_charset($con,"utf8");

  $vehicle_id = intval($_GET["id"]);

  $sql = "
    SELECT 
      b.id,
      b.start_at,
      b.end_at,
      b.created_at,
      b.status,
      b.type,
      p.name AS cliente
    FROM booking b
    LEFT JOIN person p ON p.id = b.person_id
    WHERE b.car_id = '".$vehicle_id."'
    ORDER BY b.start_at ASC
  ";

  $query = $con->query($sql);
  ?>

  <div class="card" style="background:#222; color:white;">
    <div class="card-body">

      <?php if(!$query || $query->num_rows==0): ?>
        <div class="alert alert-warning">
          Este vehículo no tiene historial de reservaciones ni rentas.
        </div>
      <?php else: ?>

      <div class="table-responsive">
        <table class="table table-bordered table-hover" style="color:white;">
          <thead style="background:#111;">
            <tr>
              <th>Cliente</th>
              <th>Desde</th>
              <th>Hasta</th>
              <th>Acción</th>
            </tr>
          </thead>

          <tbody>
            <?php while($row = $query->fetch_assoc()): ?>
              <tr>

               

                <td>
                  <?php echo !empty($row["cliente"]) ? $row["cliente"] : "N/D"; ?>
                </td>

                <td><?php echo $row["start_at"]; ?></td>
                <td><?php echo $row["end_at"]; ?></td>

                <td>
                 <a href="<?php echo $TicketMm; ?>/ticket.php?id=<?php echo $row['id']; ?>"
         class="btn btn-info btn-sm"
         onclick="return manejarVisualizacionPDF(this.href, event)">
         <i class="fa fa-eye"></i>
      </a>
      
      
                </td>

              </tr>
            <?php endwhile; ?>
          </tbody>

        </table>
      </div>



<!-- Modal PDF -->
<div id="modalPDF" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:#000a; z-index:9999;">
  <div style="position:relative; width:90%; height:90%; margin:5% auto; background:#1e1e1e; border-radius:10px; overflow:hidden; padding-top:80px;">
    <div style="position:absolute; top:20px; right:20px; display:flex; flex-direction:column; gap:15px; z-index:1000;">
      <button onclick="imprimirPDF()" style="background:#28a745; color:#fff; border:none; padding:12px 20px; border-radius:40px; font-weight:bold; font-size:16px;"><i class="fa fa-print"></i> IMPRIMIR</button>
      <a id="btnDescargar" href="#" download style="background:#007bff; color:#fff; padding:12px 20px; border-radius:40px; font-weight:bold; font-size:16px; text-decoration:none;"><i class="fa fa-download"></i> DESCARGAR</a>
      <button onclick="cerrarPDF()" style="background:#c40030; color:#fff; border:none; padding:12px 20px; border-radius:40px; font-weight:bold; font-size:16px;"><i class="fa fa-times"></i> CERRAR</button>
    </div>
    <iframe id="iframePDF" src="" style="position:absolute; top:0; left:0; width:100%; height:100%; border:none;"></iframe>
  </div>
</div>

<script>
function manejarVisualizacionPDF(url, event) {
  const esPC = window.innerWidth >= 1024;
  if (esPC) {
    event.preventDefault();
    document.getElementById('iframePDF').src = url;
    document.getElementById('btnDescargar').href = url;
    document.getElementById('modalPDF').style.display = 'block';
    return false;
  }
  return true;
}
function cerrarPDF() {
  document.getElementById('modalPDF').style.display = 'none';
  document.getElementById('iframePDF').src = '';
  document.getElementById('btnDescargar').href = '#';
}
function imprimirPDF() {
  const iframe = document.getElementById('iframePDF');
  iframe.focus();
  iframe.contentWindow.print();
}
</script>
      <?php endif; ?>

    </div>
  </div>

  <?php endif; ?>

</div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="available_pdf"): ?>

<?php
if(!isset($_SESSION["user_id"])){
  Core::redir("./");
}

require_once("CF-SYSTEMS/fpdf/fpdf.php"); // AJUSTA LA RUTA SI TU FPDF ESTÁ EN OTRO LADO

$av_from = isset($_POST["av_from"]) ? trim($_POST["av_from"]) : "";
$av_to   = isset($_POST["av_to"]) ? trim($_POST["av_to"]) : "";

$base = new Database();
$con  = $base->connect();
mysqli_set_charset($con,"utf8");

$selstock = isset($_SESSION["stock_id"]) ? intval($_SESSION["stock_id"]) : 0;

$av_cars = array();

if($av_from!="" && $av_to!=""){

  $sql = "
    SELECT c.*
    FROM cars c
    WHERE c.stock_id='$selstock'
    AND c.id NOT IN (
      SELECT r.car_id
      FROM reservations r
      WHERE
        r.stock_id='$selstock'
        AND (
          ('$av_from' BETWEEN r.date_at AND r.date_out)
          OR
          ('$av_to' BETWEEN r.date_at AND r.date_out)
          OR
          (r.date_at BETWEEN '$av_from' AND '$av_to')
          OR
          (r.date_out BETWEEN '$av_from' AND '$av_to')
        )
    )
    ORDER BY c.id DESC
  ";

  $q = $con->query($sql);
  while($row = $q->fetch_assoc()){
    $av_cars[] = $row;
  }
}

class PDF extends FPDF {
  function Header(){
    $this->SetFont('Arial','B',16);
    $this->SetTextColor(0,0,0);
    $this->Cell(0,10,utf8_decode('Disponibilidad de Vehículos'),0,1,'C');
    $this->Ln(2);
  }
}

$pdf = new PDF('P','mm','A4');
$pdf->SetAutoPageBreak(true,15);
$pdf->AddPage();

$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,utf8_decode('Fecha desde: '.$av_from.'   |   Fecha hasta: '.$av_to),0,1,'L');
$pdf->Cell(0,8,utf8_decode('Total disponibles: '.count($av_cars)),0,1,'L');
$pdf->Ln(3);

foreach($av_cars as $c){

  $photo = "";
  if(!empty($c["image"])) {
    $photo = $c["image"];
  } elseif(!empty($c["images"])) {
    $imgs = explode(",", $c["images"]);
    $photo = trim($imgs[0]);
  }

  if($photo!=""){
    if(strpos($photo, "http://")===false && strpos($photo, "https://")===false){
      $photo = __DIR__."/storage/vehicles/".$photo; // AJUSTA ESTA RUTA
    }
  }

  if($pdf->GetY() > 230){
    $pdf->AddPage();
  }

  $startY = $pdf->GetY();

  // Caja
  $pdf->SetDrawColor(180,180,180);
  $pdf->Rect(10, $startY, 190, 58);

  // Imagen
  if($photo!="" && file_exists($photo)){
    $pdf->Image($photo, 12, $startY+3, 45, 35);
  } else {
    $pdf->Rect(12, $startY+3, 45, 35);
    $pdf->SetFont('Arial','',10);
    $pdf->Text(22, $startY+22, 'SIN FOTO');
  }

  // Datos
  $pdf->SetXY(62, $startY+4);
  $pdf->SetFont('Arial','B',13);
  $pdf->MultiCell(130,6,utf8_decode($c["name"] ?? ""));

  $pdf->SetFont('Arial','',11);
  $pdf->SetX(62);
  $pdf->Cell(60,7,utf8_decode('Placa: '.($c["plate"] ?? "")),0,0);
  $pdf->Cell(60,7,utf8_decode('ID: '.($c["token"] ?? "")),0,1);

  $pdf->SetX(62);
  $pdf->Cell(60,7,utf8_decode('Año: '.($c["year"] ?? "")),0,0);
  $pdf->Cell(60,7,utf8_decode('Color: '.($c["color"] ?? "")),0,1);

  $pdf->SetX(62);
  $pdf->Cell(60,7,utf8_decode('Transmisión: '.($c["transmission"] ?? "")),0,0);
  $pdf->Cell(60,7,utf8_decode('Precio / día: '.($c["price"] ?? "")),0,1);

  $pdf->Ln(14);
}

$pdf->Output("I","disponibilidad_vehiculos.pdf");
exit;
?>

<?php endif; ?>