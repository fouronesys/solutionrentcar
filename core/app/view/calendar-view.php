<!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
       
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">
            <div class="sticky-top mb-3">
              <div class="card"  style="background-color: #222;">
                <div class="card-header">
                  <h4 class="card-title"> 
<?php 
switch (Core::$user->language){
  case 'ES': echo 'Listado'; break;
  case 'EN': echo 'List'; break;
}
?>:</h4>
                </div>
                <div class="card-body">
                  
                  <form id="filtersells">
  <input type="hidden" name="view" value="sells">
  <div class="row">

    <div class="col-10  col-md-10 ">
    <label><?php 
switch (Core::$user->language){
  case 'ES': echo 'Fecha inicio'; break;
  case 'EN': echo 'Start date'; break;
}
?>:</label>
    <input type="month" name="start_at" value="<?php echo date("Y-m"); ?>" required class="form-control" style="background-color: #333;">
  </div>
  <div class="col-md-1 col-1">
    <label><?php 
switch (Core::$user->language){
  case 'ES': echo 'Buscar'; break;
  case 'EN': echo 'Search'; break;
}
?></label>
    <button type="submit" class="btn btn-primary" style="background-color: orange;"> <i class="fa fa-search"></i></button>
  </div>

</div>
</form>

<br>
<div class="allfiltersells"></div>

<script type="text/javascript">
  $(document).ready(function(){
    $.get("./?action=filter&opt=calendar",$("#filtersells").serialize(),function(data){
      $(".allfiltersells").html(data);
    });

    $("#filtersells").submit(function(e){
      e.preventDefault();
    $.get("./?action=filter&opt=calendar",$("#filtersells").serialize(),function(data){
      $(".allfiltersells").html(data);
    });

    })
  });
</script>

            <!-- Message End -->
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
        
            </div>
          </div>
          <!-- /.col -->
          <div class="col-md-8">
            <div class="card card-primary" style="background-color: #222;">
              <div class="card-body p-0">

                  <!-- THE CALENDAR -->
                <div class="card-footer">
                <div class="row">
          <div class="col-md-8">
            <h6 class="m-0"><i class='fa fa-calendar'></i> <?php 
switch (Core::$user->language){
  case 'ES': echo 'CALENDARIO DE VEHICULOS'; break;
  case 'EN': echo 'VEHICLE CALENDAR'; break;
}
?></h6>
          </div><!-- /.col -->
          
       <div class="col-sm-4">
  <h6 class="m-0 float-sm-right">
    <li class="breadcrumb-item active">
      <i class='fa fa-history'></i> 
      <span id="reloj"></span>
    </li>
  </h6>
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
<hr>
                  <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                      <h5 class="description-header"><?php echo count(BookingData::getAllBySQL("where status=0 and stock_id=".StockData::getPrincipal()->id)); ?> <?php 
switch (Core::$user->language){
  case 'ES': echo 'VEHICULO(S)'; break;
  case 'EN': echo 'VEHICLE(S)'; break;
}
?></h5>
                      <span class="description-text text-info"><?php 
switch (Core::$user->language){
  case 'ES': echo 'POR ENTREGAR'; break;
  case 'EN': echo 'TO BE DELIVERED'; break;
}
?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                      
                      <h5 class="description-header"><?php echo count(BookingData::getAllBySQL("where status=1 and stock_id=".StockData::getPrincipal()->id)); ?> <?php 
switch (Core::$user->language){
  case 'ES': echo 'VEHICULO(S)'; break;
  case 'EN': echo 'VEHICLE(S)'; break;
}
?></h5>
                      <span class="description-text text-success"><?php 
switch (Core::$user->language){
  case 'ES': echo 'POR RECIBIR'; break;
  case 'EN': echo 'TO RECEIVE'; break;
}
?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                     
                      <h5 class="description-header">
                        <?php $in_tot=0; foreach(BookingData::getAllBySQL("where status=1 and stock_id=".StockData::getPrincipal()->id) as $ar):
                              $arrears = date("Y-m-d",strtotime($ar->end_at));
                              if(date("Y-m-d")>=$arrears): $in_tot++; endif; endforeach; echo $in_tot ?>
                      <?php 
switch (Core::$user->language){
  case 'ES': echo 'VEHICULO(S)'; break;
  case 'EN': echo 'VEHICLE(S)'; break;
}
?></h5>
                      <span class="description-text text-red"><?php 
switch (Core::$user->language){
  case 'ES': echo 'ATRASADOS'; break;
  case 'EN': echo 'DELAYED'; break;
}
?> 
                       </span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-3 col-6">
                    <div class="description-block">
                     
                      <h5 class="description-header"><?php echo count(BookingData::getAllBySQL("where status=1 and car2_id>0 and stock_id=".StockData::getPrincipal()->id)); ?> <?php 
switch (Core::$user->language){
  case 'ES': echo 'VEHICULO(S)'; break;
  case 'EN': echo 'VEHICLE(S)'; break;
}
?></h5>
                      <span class="description-text text-warning"><?php 
switch (Core::$user->language){
  case 'ES': echo 'REEMPLAZADOS'; break;
  case 'EN': echo 'REPLACED'; break;
}
?></span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                </div>
       
              </div>

                <div id="calendar"></div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  

<!-- Page specific script -->
<script>
  $(function () {

    /* initialize the external events
     -----------------------------------------------------------------*/
    function ini_events(ele) {
      ele.each(function () {

        // create an Event Object (https://fullcalendar.io/docs/event-object)
        // it doesn't need to have a start or end
        var eventObject = {
          title: $.trim($(this).text()) // use the element's text as the event title
        }

        // store the Event Object in the DOM element so we can get to it later
        $(this).data('eventObject', eventObject)

        // make the event draggable using jQuery UI
        $(this).draggable({
          zIndex        : 1070,
          revert        : true, // will cause the event to go back to its
          revertDuration: 0  //  original position after the drag
        })

      })
    }

    ini_events($('#external-events div.external-event'))

    /* initialize the calendar
     -----------------------------------------------------------------*/
    //Date for the calendar events (dummy data)
    var date = new Date()
    var d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear()

    var Calendar = FullCalendar.Calendar;
    var Draggable = FullCalendar.Draggable;

  
    var checkbox = document.getElementById('drop-remove');
    var calendarEl = document.getElementById('calendar');

    // initialize the external events
    // -----------------------------------------------------------------

  
    var calendar = new Calendar(calendarEl, {
      headerToolbar: {
        left  : 'today,prev,next',
        center: 'title',
        right : 'dayGridMonth,timeGridWeek,timeGridDay'
      },

    
      themeSystem: 'bootstrap',
      //Random default events
      events: [
      <?php foreach(BookingData::getAllBySQL("where st2rt_at!='0000-00-00 00:00:00' and type=2 and car2_id>0 and status=1 and stock_id=".StockData::getPrincipal()->id) as $opx): 
            $arrears = date("Y-m-d",strtotime($opx->end_at));
            $product = PersonData::getById($opx->person_id);
            if(date("Y-m-d")<$arrears):?>
        {
          title      : "<?php $brand = BrandData::getById($opx->getCars2()->brand_id); echo strtoupper($product->name." - ".$brand->name." ".$opx->getCars2()->name." ".$opx->getCars2()->year." ".$opx->getCars2()->plate);?>",
          start          : "<?php echo $opx->start_at; ?>",
          end            : "<?php echo $opx->st2rt_at; ?>",
          allDay         : false,
          url            : 'https://wa.me/<?php echo $product->phone; ?>',
          backgroundColor: "#FFC600",
          borderColor    : "white"
          
        },
        {
          title      : "<?php $brand0 = BrandData::getById($opx->getCars()->brand_id); echo strtoupper($product->name." - ".$brand0->name." ".$opx->getCars()->name." ".$opx->getCars()->year." ".$opx->getCars()->plate);?>",
          start          : "<?php echo $opx->st2rt_at; ?>",
          end            : "<?php echo $opx->end_at; ?>",
          allDay         : false,
          url            : 'https://wa.me/<?php echo $product->phone; ?>',
          backgroundColor: "#28a745",
          borderColor    : "white"
          
        },
<?php endif; endforeach; ?>
///////////////////////////////////////////////////////////////////////////////////////////////////
<?php foreach(BookingData::getAllBySQL("where car2_id=0 and status=1 and stock_id=".StockData::getPrincipal()->id) as $opx1): 
       $arrears = date("Y-m-d",strtotime($opx1->end_at));
       
  $product1 = PersonData::getById($opx1->person_id);
  if(date("Y-m-d")<=$arrears):?>
        {
          title      : "<?php $brand1 = BrandData::getById($opx1->getCars()->brand_id); echo strtoupper($product1->name." - ".$brand1->name." ".$opx1->getCars()->name." ".$opx1->getCars()->year." ".$opx1->getCars()->plate);?>",
          start          : "<?php echo $opx1->start_at; ?>",
          end            : "<?php echo $opx1->end_at; ?>",
          allDay         : false,
          url            : 'https://wa.me/<?php echo $product1->phone; ?>',
          backgroundColor: "#28a745 ",
          borderColor    : "white"
          
        },
<?php endif; endforeach; ?>

///////////////////////////////////////////////////////////////////////////////////////////////////
<?php foreach(BookingData::getAllBySQL("where car2_id<>0 and status=1 and stock_id=".StockData::getPrincipal()->id) as $opx1): 
       $arrears = date("Y-m-d",strtotime($opx1->end_at));
       
  $product1 = PersonData::getById($opx1->person_id);
  if(date("Y-m-d")<=$arrears):?>
        {
          title      : "<?php $brand1 = BrandData::getById($opx1->getCars()->brand_id); echo strtoupper($product1->name." - ".$brand1->name." ".$opx1->getCars()->name." ".$opx1->getCars()->year." ".$opx1->getCars()->plate);?>",
          start          : "<?php echo $opx1->start_at; ?>",
          end            : "<?php echo $opx1->end_at; ?>",
          allDay         : false,
          url            : 'https://wa.me/<?php echo $product1->phone; ?>',
          backgroundColor: "#FFC600 ",
          borderColor    : "white"
          
        },
<?php endif; endforeach; ?>
////////////////////////////////////////////////////////////////////////////////////////////////      
<?php foreach(BookingData::getAllBySQL("where st2rt_at!='0000-00-00 00:00:00' and type=1 and car2_id>0 and  status=1 and stock_id=".StockData::getPrincipal()->id) as $opx3): 
  $arrears = date("Y-m-d",strtotime($opx3->end_at));
  $product3 = PersonData::getById($opx3->person_id);
  if(date("Y-m-d")<$arrears):?>
        {
          title      : "<?php $brand3 = BrandData::getById($opx3->getCars()->brand_id); echo strtoupper($product3->name." - ".$brand3->name." ".$opx3->getCars()->name." ".$opx3->getCars()->year." ".$opx3->getCars()->plate);?>",
          start          : "<?php echo $opx3->start_at; ?>",
          end            : "<?php echo $opx3->st2rt_at; ?>",
          allDay         : false,
          url            : 'https://wa.me/<?php echo $product3->phone; ?>',
          backgroundColor: "#28a745",
          borderColor    : "white"
          
        },

        {
          title      : "<?php $brand4 = BrandData::getById($opx3->getCars2()->brand_id); echo strtoupper($product3->name." - ".$brand4->name." ".$opx3->getCars2()->name." ".$opx3->getCars2()->year." ".$opx3->getCars2()->plate);?>",
          start          : "<?php echo $opx3->st2rt_at; ?>",
          end            : "<?php echo $opx3->end_at; ?>",
          allDay         : false,
          url            : 'https://wa.me/<?php echo $product3->phone; ?>',
          backgroundColor: "#FFC600",
          borderColor    : "white"
          
        },
<?php endif; endforeach; ?>
//////////////////////////////////////////////////////////////////////////////////////////////      


<?php foreach(BookingData::getAllBySQL("where st2rt_at='0000-00-00 00:00:00' and type=2 and car2_id>0 and  status=1 and stock_id=".StockData::getPrincipal()->id) as $opx4): 
       $arrears = date("Y-m-d",strtotime($opx4->end_at));
  $product4 = PersonData::getById($opx4->person_id);
  if(date("Y-m-d")<$arrears):?>
        {
          title      : "<?php $brand5 = BrandData::getById($opx4->getCars2()->brand_id); echo strtoupper($product4->name." - ".$brand5->name." ".$opx4->getCars2()->name." ".$opx4->getCars2()->year." ".$opx4->getCars2()->plate);?>",
          start          : "<?php echo $opx4->start_at; ?>",
          end            : "<?php echo $opx4->end_at; ?>",
          allDay         : false,
          url            : 'https://wa.me/<?php echo $product4->phone; ?>',
          backgroundColor: "#FFC600",
          borderColor    : "white"
          
        },

<?php endif; endforeach; ?>
////////////////////////////////////////////////////////////////////////////////////////////            
<?php foreach(BookingData::getAllBySQL("where status=0 and stock_id=".StockData::getPrincipal()->id) as $opx5): 
      $product5 = PersonData::getById($opx5->person_id); 
        ?>
        {
          title      : "<?php $brand6 = BrandData::getById($opx5->getCars()->brand_id); echo strtoupper($product5->name." - ".$brand6->name." ".$opx5->getCars()->name." ".$opx5->getCars()->year);?>",
          start          : "<?php echo $opx5->start_at; ?>",
          end            : "<?php echo $opx5->end_at; ?>",
          allDay         : false,
          url            : 'https://wa.me/<?php echo $product5->phone; ?>',
          backgroundColor: "#00D2F8",
          borderColor    : "white"
          
        },
<?php endforeach; ?>
      ],
      editable  : false,
      droppable :true, // this allows things to be dropped onto the calendar !!!
      drop      : function(info) {
        // is the "remove after drop" checkbox checked?
        if (checkbox.checked) {
          // if so, remove the element from the "Draggable Events" list
          info.draggedEl.parentNode.removeChild(info.draggedEl);
        }
      }
    });

    calendar.render();
    // $('#calendar').fullCalendar()

  
   
  })
</script>
