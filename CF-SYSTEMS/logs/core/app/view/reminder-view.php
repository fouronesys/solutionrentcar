

     <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
                 <div class="col-md-4 my-3">

            <!-- /.card -->
              <div class="card" style="background-color:#222;">
                <div class="card-header">
                  <h3 class="card-title"><?php 
switch (Core::$user->language){
 case 'ES': echo "Crear Recordatorio"; break;
 case 'EN': echo "Create Reminder"; break;
}
?></h3>
                </div>
    <?php if(isset($_GET["id"]) && $_GET["id"]!=""): $user = ReminderData::getById($_GET["id"]);?>
       <form id="updquotes">
                <div class="card-body">
                 
 <label><?php 
switch (Core::$user->language){
 case 'ES': echo "Fecha"; break;
 case 'EN': echo "Date"; break;
}
?>:</label>
 <input type="datetime-local" name="start_at" class="form-control" value="<?php echo $user->start_at;?>" >
 <label><?php 
switch (Core::$user->language){
 case 'ES': echo "Mensaje"; break;
 case 'EN': echo "Message"; break;
}
?>:</label>
 <textarea id="new-event" name="name" required type="text" class="form-control my-2 " rows="2"  placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Mensaje"; break;
 case 'EN': echo "Write Message"; break;
}
?>"><?php echo $user->name;?></textarea>
                    <input type="hidden" name="user_id" class="form-control" value="<?php echo $user->id;?>" >
                    <button type="submit" class="btn btn-warning btn-block"><i class="fa fa-edit"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Actualizar"; break;
 case 'EN': echo "Update"; break;
}
?> </button>
    </form>
               <script type="text/javascript">
         jQuery(document).ready(function(){
            jQuery("#updquotes").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=reminder&opt=update",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Recordatorio Exito!", { sticky: true });
                  $.jGrowl("Se Actualizo la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=reminder'  }, delay); 
                     
                  }else{
                  $.jGrowl("No se Puede duplicar datos", { header: 'Error al Agregar' });
                  }
                  }
                });
                return false;
              });
            });
            
    </script>
       
    <?php else:?>
       <form id="addquotes">
                <div class="card-body">
                 
 <label><?php 
switch (Core::$user->language){
 case 'ES': echo "Fecha"; break;
 case 'EN': echo "Date"; break;
}
?>:</label>
 <input type="datetime-local" name="start_at" class="form-control " >
 <label><?php 
switch (Core::$user->language){
 case 'ES': echo "Mensaje"; break;
 case 'EN': echo "Message"; break;
}
?>:</label>
 <textarea id="new-event" name="name" required type="text" class="form-control my-2 " rows="2"  placeholder="<?php 
switch (Core::$user->language){
 case 'ES': echo "Escribir Mensaje"; break;
 case 'EN': echo "Write Message"; break;
}
?>"></textarea>

                    <button type="submit" class="btn btn-warning btn-block"><i class="fa fa-plus"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Agregar"; break;
 case 'EN': echo "Add"; break;
}
?> </button>
    </form>
         
         
            <script type="text/javascript">
         jQuery(document).ready(function(){
            jQuery("#addquotes").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=reminder&opt=add",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Recordatorio Exito!", { sticky: true });
                  $.jGrowl("Se Agrego la Base Datos", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=reminder'  }, delay); 
                     
                  }else{
                  $.jGrowl("No se Puede duplicar datos", { header: 'Error al Agregar' });
                  }
                  }
                });
                return false;
              });
            });
            
    </script>
          
    <?php endif;?>
             
                    <!-- /btn-group -->
                 
                </div>
              </div>
         
            <div class="card" style="background-color:#222;">
                <div class="card-header">
                  <h3 class="card-title"><?php 
switch (Core::$user->language){
 case 'ES': echo "Recordatorio Hoy"; break;
 case 'EN': echo "Reminder Today"; break;
}
?></h3>
                </div>
               
<?php foreach(ReminderData::getAllBySQL("where status=0 and user_id=".core::$user->id." and stock_id=".StockData::getPrincipal()->id) as $sell):
if(date("Y-m-d",strtotime($sell->start_at))<=date("Y-m-d")):?>
            
              <div class="card-header">
                 
               <h3 class="dropdown-item-title">
                  <a href="./?action=reminder&opt=upd&id=<?php echo $sell->id; ?>" class="btn btn-success btn-xs"><i class="fa fa-check"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Realizado"; break;
 case 'EN': echo "Done"; break;
}
?> </a>   <a href="./?action=reminder&opt=del&id=<?php echo $sell->id; ?>" class="btn btn-danger btn-xs"><i class="fa fa-times"></i> <?php 
switch (Core::$user->language){
 case 'ES': echo "Eliminar"; break;
 case 'EN': echo "Delete"; break;
}
?> </a>  
                 <span class="float-right text-sm text-warning"><i class="far fa-clock mr-1"></i><?php echo date("d-m-Y h:i",strtotime($sell->start_at));?></span>
                <p class="my-2"><?php echo $sell->name;?></p>
                </h3>
               
              </div>

           
<?php endif; endforeach; ?>


                 
                </div>
          </div>
          
          
      
     
   
          <!-- /.col -->
          <div class="col-md-8 my-3">


            <div class="card card-warning" style="background-color:#222;">
              <div class="card-body p-0">
                <!-- THE CALENDAR -->
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
      <?php foreach(ReminderData::getAllBySQL("where status=0 and user_id= ".core::$user->id." and stock_id=".StockData::getPrincipal()->id) as $opx):?>
        {
          title      : "<?php  echo strtoupper($opx->name);?>",
          start          : "<?php echo $opx->start_at; ?>",
          end            : "<?php echo $opx->start_at; ?>",
          url            : './?view=reminder&id=<?php echo $opx->id; ?>',
          allDay         : false,
          backgroundColor: "#FFC600",
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
