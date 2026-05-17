<?php 
if($_SERVER['HTTP_HOST']=="amprental.assanpos.com"): 
$title = StockData::getFPrincipal(3)->name;
$type_img = StockData::getFPrincipal(3)->type_img;   
$ticket_image = StockData::getFPrincipal(3)->ticket_image;
elseif($_SERVER['HTTP_HOST']=="oderbirentcar.assanpos.com"):  
$title = StockData::getFPrincipal(2)->name;
$type_img = StockData::getFPrincipal(2)->type_img;  
$ticket_image = StockData::getFPrincipal(2)->ticket_image;
elseif($_SERVER['HTTP_HOST']=="grrentcar.assanpos.com"):  
$title = StockData::getFPrincipal(4)->name;
$type_img = StockData::getFPrincipal(4)->type_img;
$ticket_image = StockData::getFPrincipal(4)->ticket_image;
else:  
$title = StockData::getFPrincipal(1)->name;
$type_img = StockData::getFPrincipal(1)->type_img;
$ticket_image = StockData::getFPrincipal(1)->ticket_image;
endif;

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?> </title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0 maximum-scale=1.0, user-scalable=0">

 <link href="CF-SYSTEMS/storage/configuration/assanpos.png" rel="shortcut icon"/>
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/fullcalendar/main.css">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="CF-SYSTEMS/dist/css/adminlte.min.css">

    <link rel="stylesheet" href="CF-SYSTEMS/plugins/morris/morris.css">
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/morris/example.css">
  <link href="CF-SYSTEMS/vendor/jGrowl/jquery.jgrowl.css" rel="stylesheet" media="screen"/>
    <!-- summernote -->
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/summernote/summernote-bs4.min.css">
  <!-- CodeMirror -->
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/codemirror/codemirror.css">
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/codemirror/theme/monokai.css">
  <!-- SimpleMDE -->
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/simplemde/simplemde.min.css">

    <link rel="stylesheet" href="CF-SYSTEMS/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

    <link rel="stylesheet" href="CF-SYSTEMS/plugins/daterangepicker/daterangepicker.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="CF-SYSTEMS//bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
  <!-- BS Stepper -->
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/bs-stepper/css/bs-stepper.min.css">
  <!-- dropzonejs -->
  <link rel="stylesheet" href="CF-SYSTEMS//plugins/dropzone/min/dropzone.min.css">
  
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
          <script src="CF-SYSTEMS/plugins/jquery/jquery-2.1.4.min.js"></script>
<script src="CF-SYSTEMS/plugins/morris/raphael-min.js"></script>

<script src="CF-SYSTEMS/plugins/morris/morris.js"></script>
<link href="CF-SYSTEMS/plugins/apexcharts/apexcharts.css" rel="stylesheet">
<script type="text/javascript" src="CF-SYSTEMS/plugins/apexcharts/apexcharts.min.js"></script>

 <link rel="stylesheet" href="CF-SYSTEMS/plugins/datatables/dataTables.bootstrap.css">



          <script src="CF-SYSTEMS/plugins/jspdf/jspdf.min.js"></script>
          <script src="CF-SYSTEMS/plugins/jspdf/jspdf.plugin.autotable.js"></script>
    
<style type="text/css">

body::-webkit-scrollbar {
  width: 5px;
}

body::-webkit-scrollbar-track {
  background-color: rgba(0, 0, 0, 0.4);
  border-radius: 10px;
}
body::-webkit-scrollbar-thumb {
  background-color: #777;
  border-radius: 10px;
}


.este {
  height: 25em;
  line-height: 1em;
  overflow-x: hidden;
  overflow-y: scroll;
  width: 100%;

}

.este::-webkit-scrollbar {
  width: 7.5px;
}

.este::-webkit-scrollbar-track {
  background-color: rgba(0, 0, 0, 0.4);
  border-radius: 10px;
}
.este::-webkit-scrollbar-thumb {
  background-color: #777;
  border-radius: 10px;
}

</style>

</head>

<body ondragstart="return false" onselectstart="return false" oncontextmenu="return false" class="<?php if(isset($_SESSION["user_id"]) || isset($_SESSION["client_id"])):?> hold-transition dark-mode sidebar-mini sidebar-collapse layout-fixed layout-navbar-fixed <?php else:?>login-page<?php endif; ?>">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble" src="CF-SYSTEMS/storage/configuration/assanpos.png" alt="ASSANPOS" height="60" width="60">
  </div>

<div class="wrapper">
  <?php if(isset($_SESSION["user_id"])):
  $iva_val = StockData::getPrincipal()->imp-val;
  $users = UserData::getById($_SESSION["user_id"]);?>
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link"><?php echo $users->getK()->name;?></a>
      </li>
      
    </ul>

<?php

$msgs = MessageData::getUnreadedByUserId($_SESSION["user_id"],0);

foreach(CarsData::getAll() as $cs):
if($cs->kms>=($cs->kms_current+StockData::getPrincipal()->kms)):
  $cs_tot++;
endif; 
endforeach;

foreach(BookingData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id." and status=0 order by start_at desc") as $qt_totsell):

$q0x_ttime =  date("Y-m-d",strtotime($qt_totsell->start_at)); 
if(date("Y-m-d", strtotime('-6 hours'))==$q0x_ttime):
  $q0_tot++;
endif; 

$qx_ttime =  date("Y-m-d",strtotime($qt_totsell->start_at."- 1 days")); 
if(date("Y-m-d", strtotime('-6 hours'))==$qx_ttime):
  $qt_tot++;
endif; 

$qx_t2ime =  date("Y-m-d",strtotime($qt_totsell->start_at."- 2 days")); 
if(date("Y-m-d", strtotime('-6 hours'))==$qx_t2ime):
  $qt_tot2++;
endif; 
endforeach;

foreach(BookingData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id." and status=1 order by end_at desc") as $q2t_totsell):
$q0xt_ttime =  date("Y-m-d",strtotime($q2t_totsell->end_at)); 
if(date("Y-m-d", strtotime('-6 hours'))==$q0xt_ttime):
  $qt_t0t++;
endif; 


$qxt_ttime = date("Y-m-d",strtotime($q2t_totsell->end_at."- 1 days")); 
if(date("Y-m-d", strtotime('-6 hours'))==$qxt_ttime):
  $qt_t2t++;
endif; 

$qxt_t2ime = date("Y-m-d",strtotime($q2t_totsell->end_at."- 2 days")); 
  if(date("Y-m-d", strtotime('-6 hours'))==$qxt_t2ime):
  $qt_t2t2++;
endif; 

endforeach;

foreach(BookingData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id." and status=1 order by end_at asc") as $q3t_totsell):
$q3t_ttime = date("Y-m-d",strtotime($q3t_totsell->end_at));
if(date("Y-m-d", strtotime('-6 hours'))>=$q3t_ttime):
  $qt_t3t++;
endif; 

endforeach;

foreach(CarsData::getAllBySQL("where date_insurance!=0000-00-00 order by date_insurance desc") as $ins_tot):
$insurancx = date("Y-m-d",strtotime($ins_tot->date_insurance."- 30 days"));
if(date("Y-m-d", strtotime('-6 hours'))>=$insurancx):
  $in_tot++;
endif; 
endforeach;

foreach(CarsData::getAllBySQL("where date2_insurance!=0000-00-00 order by date2_insurance desc ") as $ins_t2t):
$insurancx2 = date("Y-m-d",strtotime($ins_t2t->date2_insurance."- 30 days"));
if(date("Y-m-d", strtotime('-6 hours'))>=$insurancx2):
  $in_t2t++;
endif; 
endforeach;


?>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Bell -->
      <?php $__notif_unread = NotificationData::countUnread('user', intval($_SESSION['user_id'])); ?>
      <li class="nav-item dropdown" id="notifBellLi">
        <a class="nav-link" data-toggle="dropdown" href="#" id="notifBellLink">
          <i class="fa fa-bell"></i>
          <span class="badge badge-danger navbar-badge" id="notifBellBadge" <?php if($__notif_unread<=0) echo 'style="display:none;"'; ?>><?php echo $__notif_unread; ?></span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header"><h6><i class="fa fa-bell"></i> Notificaciones</h6></span>
          <div class="dropdown-divider"></div>
          <div id="notifBellList" style="max-height:320px;overflow-y:auto;">
            <div class="dropdown-item text-muted" style="text-align:center;">Cargando...</div>
          </div>
          <div class="dropdown-divider"></div>
          <a href="./?view=notifications&opt=all" class="dropdown-item dropdown-footer"><i class="fa fa-inbox"></i> Ver todas</a>
          <a href="./?view=notifications&opt=preferences" class="dropdown-item dropdown-footer"><i class="fa fa-cog"></i> Preferencias</a>
        </div>
      </li>
      <script>
      (function(){
        function notifEsc(s){ return $('<div>').text(s||'').html(); }
        function notifLoad(){
          $.get('./?action=notification&opt=list&limit=8', function(r){
            if(!r||!r.ok) return;
            var b=$('#notifBellBadge');
            if(r.unread>0){ b.text(r.unread).show(); } else { b.hide(); }
            var $l=$('#notifBellList'); $l.empty();
            if(!r.items||r.items.length===0){
              $l.append('<div class="dropdown-item text-muted" style="text-align:center;">Sin notificaciones</div>'); return;
            }
            r.items.forEach(function(it){
              var bg=it.read?'':'background:#3a2f00;color:#fff;';
              var html='<a class="dropdown-item" href="'+(it.url||'./?view=notifications&opt=all')+'" data-id="'+it.id+'" style="white-space:normal;'+bg+'">'
                +'<div><b>'+notifEsc(it.title)+'</b></div>'
                +'<div style="font-size:12px;">'+notifEsc((it.body||'').replace(/<[^>]+>/g,'').substring(0,80))+'</div>'
                +'<div style="font-size:11px;color:#aaa;">'+notifEsc(it.created)+'</div></a>';
              $l.append(html);
            });
            $l.find('a').on('click', function(){
              var id=$(this).data('id'); if(id){ $.post('./?action=notification&opt=mark_read', {id:id}); }
            });
          }, 'json');
        }
        $(document).ready(function(){
          notifLoad();
          setInterval(notifLoad, 60000);
          $('#notifBellLink').on('click', function(){ notifLoad(); });
        });
      })();
      </script>
      <!-- Navbar Search -->

      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-car"></i>
          <span class="badge badge-danger navbar-badge"><?php echo $qt_t3t;?></span>
        </a>

       <?php if(($qt_t3t)>3):?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right este">
        <?php else:?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <?php endif;?>
          <span class="dropdown-item dropdown-header"><h6><i class="fa fa-car"></i> Atrasados</h6></span>
         <div class="dropdown-divider"></div>
    <?php 
    foreach(BookingData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id." and status=1 order by end_at asc ") as $c2tsell):
        $c2t_ttime = date("Y-m-d",strtotime($c2tsell->end_at)); 
        if(date("Y-m-d", strtotime('-6 hours'))>=$c2t_ttime):
          $product = PersonData::getById($c2tsell->person_id);
          $cars = CarsData::getById($c2tsell->car_id);?>  
        <a href="./?view=calendar" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <div class="media-body">
                
                <h3 class="dropdown-item-title">
                  <?php echo $product->phone;?>
                </h3>
               
              <p class="text-sm text-green"><i class="fas fa-star"></i> <?php echo strtoupper($product->name);?></p>
              <p class="text-sm"><i class="fas fa-car"></i> <?php echo strtoupper($cars->getBrand()->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name)." [".$cars->token."] ";?></p>
             
             <p><span class="float-right text-sm text-warning"><i class="far fa-clock mr-1"></i><?php echo date("Y-m-d h:i a", strtotime("$c2tsell->end_at")); ?></span></p>
              
              
              
              </div>

            </div>
            <!-- Message End -->
          </a>  
        <?php endif; endforeach; ?>

    <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Tienes <?php echo  $qt_t3t;?> Aviso de No Entregado</a>
        </div>
      </li>
<!--//////////////////////////////////////////////////////////////////////////// -->


      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-tint"></i>
          <span class="badge badge-warning navbar-badge"><?php echo $cs_tot;?></span>
        </a>

       <?php if(($cs_tot)>3):?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right este">
        <?php else:?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <?php endif;?>    
        <span class="dropdown-item dropdown-header"><h6><i class="fa fa-tint"></i> Cambio de Aceite</h6></span>
         <div class="dropdown-divider"></div>
    <?php 
    if(count($cs_tot)>0):
    foreach(CarsData::getAll() as $km):
    if($km->kms>=($km->kms_current+StockData::getPrincipal()->kms)):?> 
      <a  class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <div class="media-body">
              <p><?php echo "REVISION FUE: " .number_format($km->kms_current,0,".",",")." KM"; ?></p>
              <p class="text-sm"><i class="fas fa-car"></i> <?php echo strtoupper($km->getBrand()->name." ".$km->name." ".$km->year." ".$km->getExColor()->name)." [".$km->token."] ";?></p>
               <h3 class="dropdown-item-title">
                  <span class="float-right text-sm "><i class="far fa-clock mr-1"></i><?php echo number_format($km->kms,0,".",",")." KM ACTUAL"; ?></span>
                </h3>
         <p class="text-sm"><span class="badge badge-warning"> Mantenimiento<i class="fas fa-history"></i></span></p> 
            </div>
          </div>
            <!-- Message End -->
          </a>
  <?php endif; endforeach; endif; ?>

    <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Tienes <?php echo $cs_tot;?> Aviso de Aceite</a>
        </div>
      </li>
<!--//////////////////////////////////////////////////////////////////////////// -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-history"></i>
          <span class="badge badge-secondary navbar-badge"><?php echo ($in_tot+$in_t2t);?></span>
        </a>

       
       <?php if(($in_tot+$in_t2t)>3):?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right este">
        <?php else:?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <?php endif;?>
           <span class="dropdown-item dropdown-header"><h6><i class="fa fa-car"></i> Vencimiento Seguro</h6></span>
         <div class="dropdown-divider"></div>
    <?php 
    if($in_tot>0):
    foreach(CarsData::getAllBySQL("where date_insurance!=0000-00-00 order by date_insurance desc ") as $ins):
        
       $insurance = date("Y-m-d",strtotime($ins->date_insurance."- 30 days"));
        if(date("Y-m-d", strtotime('-6 hours'))>=$insurance):?>  
        <a  class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <div class="media-body">
              <p>SEGURO DE LEY: </p>
             
              <p class="text-sm"><i class="fas fa-car"></i> <?php echo strtoupper($ins->getBrand()->name." ".$ins->name." ".$ins->year." ".$ins->getExColor()->name)." ".$ins->chassis." [".$ins->token."] ";?></p>
               <h3 class="dropdown-item-title">
                  <span class="float-right text-sm "><i class="far fa-clock mr-1"></i><?php echo date("Y-m-d", strtotime("$ins->date_insurance")); ?></span>
                </h3>
              <?php 
        if(date("Y-m-d",strtotime($ins->date_insurance."- 15 days"))<=date("Y-m-d") && date("Y-m-d")<=date("Y-m-d",strtotime($ins->date_insurance))) :?> 
         <p class="text-sm"><span class="badge badge-warning"> Quedan 15 dias<i class="fas fa-history"></i></span></p> 
        <?php elseif(date("Y-m-d",strtotime($ins->date_insurance."- 30 days"))<=date("Y-m-d") && date("Y-m-d",strtotime($ins->date_insurance."- 15 days"))>=date("Y-m-d") && date("Y-m-d")<=date("Y-m-d",strtotime($ins->date_insurance))) :?> 
              <p class="text-sm"><span class="badge badge-success">Quedan 30 dias<i class="fas fa-history"></i></span></p>
        <?php elseif(date("Y-m-d",strtotime($ins->date_insurance))>=date("Y-m-d")):?> 
          <p class="text-sm"><span class="badge badge-danger"> Vencido<i class="fas fa-history"></i></span></p> 
        <?php endif;?>
            </div>
          </div>
            <!-- Message End -->
          </a>
  <?php endif; endforeach; endif; ?>


    <?php 
    if($in_t2t>0):
    foreach(CarsData::getAllBySQL("where  date2_insurance!=0000-00-00 order by date2_insurance desc ") as $ins2):
        
       $insurance2 = date("Y-m-d",strtotime($ins2->date2_insurance."- 30 days"));
        if(date("Y-m-d", strtotime('-6 hours'))>=$insurance2):?>  
        <a class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <div class="media-body">
              <p>SEGURO DE FULL: </p>
             
              <p class="text-sm"><i class="fas fa-car"></i> <?php echo strtoupper($ins2->getBrand()->name." ".$ins2->name." ".$ins2->year." ".$ins2->getExColor()->name)." ".$ins2->chassis." [".$ins2->token."] ";?></p>
               <h3 class="dropdown-item-title">
                  <span class="float-right text-sm "><i class="far fa-clock mr-1"></i><?php echo date("Y-m-d", strtotime("$ins2->date2_insurance")); ?></span>
                </h3>
              <?php 
        
        if(date("Y-m-d",strtotime($ins2->date2_insurance."- 15 days"))<=date("Y-m-d") && date("Y-m-d")<=date("Y-m-d",strtotime($ins2->date2_insurance))) :?> 
         <p class="text-sm"><span class="badge badge-warning"> Quedan 15 dias<i class="fas fa-history"></i></span></p> 
        <?php elseif(date("Y-m-d",strtotime($ins2->date2_insurance."- 30 days"))<=date("Y-m-d") && date("Y-m-d",strtotime($ins2->date2_insurance."- 15 days"))>=date("Y-m-d") && date("Y-m-d")<=date("Y-m-d",strtotime($ins2->date2_insurance))) :?> 
              <p class="text-sm"><span class="badge badge-success">Quedan 30 dias<i class="fas fa-history"></i></span></p>
        <?php elseif(date("Y-m-d",strtotime($ins2->date2_insurance))>=date("Y-m-d")):?> 
          <p class="text-sm"><span class="badge badge-danger"> Vencido<i class="fas fa-history"></i></span></p> 
        <?php endif;?>
            </div>
          </div>
            <!-- Message End -->
          </a>
  <?php endif; endforeach; endif; ?>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Tienes <?php echo  ($in_tot+$in_t2t);?> Aviso de Vencimiento</a>
        </div>
         
      </li>



      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-calendar-check"></i>
          <span class="badge badge-info navbar-badge"><?php echo  ($qt_tot+$qt_tot2+$q0_tot);?></span>
        </a>

       <?php if(($qt_tot+$qt_tot2+$q0_tot)>3):?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right este">
        <?php else:?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <?php endif;?>
        <span class="dropdown-item dropdown-header"><h6><i class="fa fa-calendar-check"></i>  Vehiculos A Entregar</h6></span>
         <div class="dropdown-divider"></div>
          <?php 
          if($q0_tot>0 ||$qt_tot>0 || $qt_tot2>0):
      foreach(BookingData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id." and status=0 order by start_at asc") as $qtsell): 
        $q0_ttime = date("Y-m-d",strtotime($qtsell->start_at)); 
        if(date("Y-m-d", strtotime('-6 hours'))==$q0_ttime):
          $product = PersonData::getById($qtsell->person_id);
          $cars = CarsData::getById($qtsell->car_id);?>  
         <div class="dropdown-divider"></div>
        <a  class="dropdown-item dropdown-footer"> Sale Hoy</a>
          <div class="dropdown-divider"></div>

        <a href="./?view=calendar" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <div class="media-body">
                
                <h3 class="dropdown-item-title">
                  <?php echo $product->phone;?>
                </h3>
               
              <p class="text-sm text-green"><i class="fas fa-star"></i> <?php echo strtoupper($product->name);?></p>
              <p class="text-sm"><i class="fas fa-car"></i> <?php echo strtoupper($cars->getBrand()->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name)." [".$cars->token."] ";?></p>
             
             <p><span class="float-right text-sm text-warning"><i class="far fa-clock mr-1"></i><?php echo date("Y-m-d h:i a", strtotime("$qtsell->start_at")); ?></span></p>
              
              
              
              </div>

            </div>
            <!-- Message End -->
          </a>
  <?php endif;
       
        $qt_ttime = date("Y-m-d",strtotime($qtsell->start_at."- 1 days"));  
        if(date("Y-m-d", strtotime('-6 hours'))==$qt_ttime):
          $product = PersonData::getById($qtsell->person_id);
          $cars = CarsData::getById($qtsell->car_id);?>  
          <div class="dropdown-divider"></div>
        <a  class="dropdown-item dropdown-footer"> Sale Mañana</a>
          <div class="dropdown-divider"></div>

        <a href="./?view=calendar" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <div class="media-body">
                 
               <h3 class="dropdown-item-title">
                <?php echo $product->phone;?>
                </h3>
               
        <p class="text-sm text-green"><i class="fas fa-star"></i> <?php echo strtoupper($product->name);?></p>
              <p class="text-sm"><i class="fas fa-car"></i> <?php echo strtoupper($cars->getBrand()->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name)." [".$cars->token."] ";?></p>
              
             
             <p><span class="float-right text-sm text-warning"><i class="far fa-clock mr-1"></i><?php echo date("Y-m-d h:i a", strtotime("$qtsell->start_at")); ?></span></p>
              
              
              </div>

            </div>
            <!-- Message End -->
          </a>

  <?php endif; 
  $qt_t2ime = date("Y-m-d",strtotime($qtsell->start_at."- 2 days"));
  if(date("Y-m-d", strtotime('-6 hours'))==$qt_t2ime):
          $product = PersonData::getById($qtsell->person_id);
          $cars = CarsData::getById($qtsell->car_id);?>  
          <div class="dropdown-divider"></div>
        <a  class="dropdown-item dropdown-footer"> Sale Pasado Mañana</a>
          <div class="dropdown-divider"></div>

        <a href="./?view=calendar" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <div class="media-body">
                 
               <h3 class="dropdown-item-title">
                <?php echo $product->phone;?>
                </h3>
               
        <p class="text-sm text-green"><i class="fas fa-star"></i> <?php echo strtoupper($product->name);?></p>
              <p class="text-sm"><i class="fas fa-car"></i> <?php echo strtoupper($cars->getBrand()->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name)." [".$cars->token."] ";?></p>
              
             
             <p><span class="float-right text-sm text-warning"><i class="far fa-clock mr-1"></i><?php echo date("Y-m-d h:i a", strtotime("$qtsell->start_at")); ?></span></p>
              
              
              </div>

            </div>
            <!-- Message End -->
          </a>
  <?php endif; endforeach; endif; ?>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Tienes <?php echo ($qt_tot+$q0t_tot+$qt_tot2);?> Entrega Nueva</a>
        </div>
         
      </li>



      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-calendar-times"></i>
          <span class="badge badge-success navbar-badge"><?php echo ($qt_t2t+$qt_t2t2+$qt_t0t);?></span>
        </a>
       <?php if(($qt_t2t+$qt_t2t2+$qt_t0t)>3):?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right este">
        <?php else:?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <?php endif;?>
          <span class="dropdown-item dropdown-header"><h6><i class="fa fa-calendar-times"></i>  Vehiculos A Recibir</h6></span>
         <div class="dropdown-divider"></div>
          <?php 
          if($qt_t0t>0 || $qt_t2t>0 || $qt_t2t2>0):
      foreach(BookingData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id." and status=1 order by end_at asc") as $q2tsell):
        $q0t_ttime = date("Y-m-d",strtotime($q2tsell->end_at)); 
        if(date("Y-m-d", strtotime('-6 hours'))==$q0t_ttime):
          $product = PersonData::getById($q2tsell->person_id);
          $cars = CarsData::getById($q2tsell->car_id);?>  
         <div class="dropdown-divider"></div>
        <a  class="dropdown-item dropdown-footer"> Entra Hoy</a>
          <div class="dropdown-divider"></div>

        <a href="./?view=calendar" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <div class="media-body">
                
                <h3 class="dropdown-item-title">
                  <?php echo $product->phone;?>
                </h3>
               
              <p class="text-sm text-green"><i class="fas fa-star"></i> <?php echo strtoupper($product->name);?></p>
              <p class="text-sm"><i class="fas fa-car"></i> <?php echo strtoupper($cars->getBrand()->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name)." [".$cars->token."] ";?></p>
             
             <p><span class="float-right text-sm text-warning"><i class="far fa-clock mr-1"></i><?php echo date("Y-m-d h:i a", strtotime("$q2tsell->end_at")); ?></span></p>
              
              
              
              </div>

            </div>
            <!-- Message End -->
          </a>
  <?php endif;
        $q2t_ttime = date("Y-m-d",strtotime($q2tsell->end_at."- 1 days")); 
        if(date("Y-m-d", strtotime('-6 hours'))==$q2t_ttime):
          $product = PersonData::getById($q2tsell->person_id);
          $cars = CarsData::getById($q2tsell->car_id);?>  
         <div class="dropdown-divider"></div>
        <a  class="dropdown-item dropdown-footer"> Entra Mañana</a>
          <div class="dropdown-divider"></div>

        <a href="./?view=calendar" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <div class="media-body">
                
                <h3 class="dropdown-item-title">
                  <?php echo $product->phone;?>
                </h3>
               
              <p class="text-sm text-green"><i class="fas fa-star"></i> <?php echo strtoupper($product->name);?></p>
              <p class="text-sm"><i class="fas fa-car"></i> <?php echo strtoupper($cars->getBrand()->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name)." [".$cars->token."] ";?></p>
             
             <p><span class="float-right text-sm text-warning"><i class="far fa-clock mr-1"></i><?php echo date("Y-m-d h:i a", strtotime("$q2tsell->end_at")); ?></span></p>
              
              
              
              </div>

            </div>
            <!-- Message End -->
          </a>
  <?php endif;
  $q2t_t2imx = date("Y-m-d",strtotime($q2tsell->end_at."- 2 days")); 
  if(date("Y-m-d", strtotime('-6 hours'))==$q2t_t2imx):
          $product = PersonData::getById($q2tsell->person_id);
          $cars = CarsData::getById($q2tsell->car_id);?>  
          <div class="dropdown-divider"></div>
        <a  class="dropdown-item dropdown-footer"> Entra Pasado Mañana</a>
          <div class="dropdown-divider"></div>
        <a href="./?view=calendar" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                <?php echo $product->phone;?>
                </h3>
              
              <p class="text-sm text-green"><i class="fas fa-star"></i> <?php echo strtoupper($product->name);?></p>
              <p class="text-sm"><i class="fas fa-car"></i> <?php echo strtoupper($cars->getBrand()->name." ".$cars->name." ".$cars->year." ".$cars->getExColor()->name)." [".$cars->token."] ";?></p>
              
             <p><span class="float-right text-sm text-warning"><i class="far fa-clock mr-1"></i><?php echo date("Y-m-d h:i a", strtotime("$q2tsell->end_at")); ?></span></p>
              
              </div>

            </div>
            <!-- Message End -->
          </a>
           
  <?php endif; endforeach; endif;  ?>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Tienes <?php echo  ($qt_t0t+$qt_t2t+$qt_t2t2);?> Recibo Nuevo</a>
        </div>
         
      </li>

  <!-- Messages Dropdown Menu -->

      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge"><?php echo count($msgs);?></span>
         </a>

        
       <?php if(count($msgs)>3):?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right este">
        <?php else:?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <?php endif;?>
          <?php foreach($msgs as $i):?>
           <a href="./?view=messages&opt=open&code=<?php echo $i->code;?>" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="<?php echo $i->url;?>" class="img-circle" style="width: 25%"> 
              <div class="media-body">
                <h3 class="dropdown-item-title">
                 <?php if($i->user_from!=$_SESSION["user_id"]):?>
                    <?php $u = $i->getFrom(); echo $u->name." ".$u->lastname;?>
                    <?php elseif($i->user_to!=$_SESSION["user_id"]):?>
                    <?php $u = $i->getTo(); echo $u->name." ".$u->lastname;?>
                  <?php endif; ?>
                
                                 </h3>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> <?php echo $i->created_at;?></p>
              
                <p class="text-sm"><?php echo $i->message;?></p>
              
              </div>

            </div>
            <!-- Message End -->
          </a>
           <?php endforeach; ?>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Tienes <?php echo count($msgs);?> Mensajes nuevos</a>
        </div>
         
      </li>
         


<!-- ///////////////////////// CONSUMIDOR FINAL /////////////// -->
<?php
      $cfinal = array();
      $cfinal = CFData::getGroupByDateOp();
      $cf = $cfinal[0]->c!=null?$cfinal[0]->c:0; 
      if( $cf==0 ||  $cf<=10){
      $cnt_cf++;
      }

/////////////////////////// CREDITO FISCAL ///////////////
      $cfiscal = array();
      $cfiscal = CFSData::getGroupByDateOp();
      $cf = $cfiscal[0]->c!=null?$cfiscal[0]->c:0; 
      if( $cfs==0 ||  $cfs<=10){
      $cnt_cfs++;
      }
/////////////////////// GUBERNAMENTAL ////////////////// 
      $cgob = array();
      $cgob = CGData::getGroupByDateOp();
      $cg = $cgob[0]->c!=null?$cgob[0]->c:0; 
      if( $cg==0 ||  $cg<=10){
      $cnt_cg++;
      }
//////////////////// NOTA DE CREDITO ///////////////// 
      $cnc = array();
      $cnc = CNCData::getGroupByDateOp();
      $cn = $cnc[0]->c!=null?$cnc[0]->c:0; 
      if( $cn==0 ||  $cn<=10){
      $cnt_cnc++;
      }
///////////////////// REGIMEN ESPECIAL ////////////// 
      $crs = array();
      $crs = CRSData::getGroupByDateOp();
      $cr = $crs[0]->c!=null?$crs[0]->c:0; 
      if( $cr==0 ||  $cr<=10){
      $cnt_crs++;
      }
      $cnt_cfg = ($cnt_cfs+$cnt_cg+$cnt_cnc+$cnt_crs+$cnt_cf);
?>

 <?php if ($iva_val>0):?>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-list-ol"></i>
          <span class="badge badge-warning navbar-badge"> <?php echo $cnt_cfg;?> </span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header"> <?php echo $cnt_cfg;?>  Notificaciones DGII</span>

         <!-- /////////////// CONSUMIDOR FINAL /////////////-->

                 <?php if($cf==0 || $cf<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cf==0){ echo "<span class='badge badge-danger'>No hay Comprobante Final.</span>";}
                  else if($cf<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Comprobante Final.</span>";} 
                  else if($cf<=10){ echo "<span class='badge badge-info'>Quedan 10 Comprobante Final.</span>";} ?> 
          </a>
          <?php endif;?>

         <!-- //////////////// CREDITO FISCAL /////////// -->
                
                 <?php if($cfs==0 || $cfs<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cfs==0){ echo "<span class='badge badge-danger'>No hay Comprobante Credito Fiscal.</span>";}
                  else if($cfs<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Comprobante Credito Fiscal.</span>";} 
                  else if($cfs<=10){ echo "<span class='badge badge-info'>Quedan 10 Comprobante Credito Fiscal.</span>";} ?> 
          </a>
          <?php endif;?>

          <!-- //////////////// GUBERNAMENTAL ///////// -->
        
                 <?php if( $cg==0 ||  $cg<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cg==0){ echo "<span class='badge badge-danger'>No hay Comprobante Gubernamental.</span>";}
                  else if($cg<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Comprobante Gubernamental.</span>";} 
                  else if($cg<=10){ echo "<span class='badge badge-info'>Quedan 10 Comprobante Gubernamental.</span>";} ?> 
          </a>
          <?php endif;?>

         <!-- ////////////// NOTA DE CREDITO ///////// -->

          <?php if( $cn==0 ||  $cn<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cn==0){ echo "<span class='badge badge-danger'>No hay Comprobante de Credito.</span>";}
                  else if($cn<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Comprobante de Credito.</span>";} 
                  else if($cn<=10){ echo "<span class='badge badge-info'>Quedan 10 Comprobante de Credito.</span>";} ?> 
          </a>
          <?php endif;?>

            <!-- /////////// REGIMEN ESPECIAL ////////// -->

        <?php if( $cr==0 ||  $cr<=10):?>
           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cr==0){ echo "<span class='badge badge-danger'>No hay Comprobante de Regimen.</span>";}
                  else if($cr<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Comprobante de  Regimen.</span>";} 
                  else if($cr<=10){ echo "<span class='badge badge-info'>Quedan 10 Comprobante de Regimen.</span>";} ?> 
          </a>
          <?php endif;?>
         
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Ver todas las notificaciones</a>
        </div>
      </li>

       <?php endif;?>
<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->       


    </ul>
  </nav>
  <!-- /.navbar -->
   <!-- Main Sidebar Container -->
<aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a  class="brand-link" style="color: white;">
      <img src="CF-SYSTEMS/storage/configuration/assanpos.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">ASSANPOS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
<a data-toggle="modal" data-target="#myIMG">

        <div class="image">
          <?php
          if($users->image!=""){
            $url = "CF-SYSTEMS/storage/profiles/".$users->image;
            if(file_exists($url)){
              echo "<img src='$url'  class='img-circle elevation-2'>";
            }
          }
          ?>
          
        </div>
        <div class="info">
          <a style="color: #ddd;" class="d-block">
             <?php
               if($users){
           echo $users->name." ".$users->lastname;
          
            }
             
                  
                  ?>
<br>
                    <i class="fa fa-circle text-green"></i> En Linea 
          </a>
      
        </div></a>


      </div>
<!-- SidebarSearch Form -->
      <div class="form-inline">
         <form id="lsearch">
        <div class="input-group">
          <input id="product_layout" class="form-control form-control-sidebar" autocomplete="off" placeholder="Escribir la Ficha" type="search" aria-label="Search" >
              
          <div class="input-group-append">
            <button class="btn btn-sidebar" disabled>
              <i class="fas fa-search fa-fw"></i>
            </button>

          </div>
        </div>
      </form>
      </div>

<script type="text/javascript">
function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items" );
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div" );
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
      closeAllLists(e.target);
  });
}

/*An array containing all the country names in the world:*/
var countries = ["<?php foreach (CarsData::getAll() as $client):?>
<?php echo $client->token." ".$client->getBrand()->name." ".$client->name." ".$client->year." ".$client->plate."<br>";?>
<?php foreach(BookingData::getAllBySQL("where status=1 and car_id=".$client->id) as $suc):echo  "Entregar: ".date("Y-m-d",strtotime($suc->start_at))."<br>"; echo  " Regresa: ".date("Y-m-d",strtotime($suc->end_at))."<br>"; endforeach;?>","<?php endforeach; ?>"];

/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
autocomplete(document.getElementById("product_layout"), countries);
</script>

<style type="text/css">


/*the container must be positioned relative:*/
.autocomplete {
  position: relative;
  display: inline-block;
}

input {
  border: 1px solid transparent;
  background-color: #343a40;
  padding: 10px;
  font-size: 16px;
}

input[type=text] {
  background-color: #343a40;
  width: 100%;
}

input[type=submit] {
  background-color: DodgerBlue;
  color: #fff;
  cursor: pointer;
}

.autocomplete-items {
  position: absolute;
  border: 1px solid #343a40;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #343a40; 
  border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #383f45; 
}

/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}
</style>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

         
      <?php $operations = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);
$person = UserData::getById($_SESSION["user_id"]);
if (($person->username!="admin" || $person->username!="Admin") && $person->password!="90b9aa7e25f80cf4f64e990b78a9fc5ebd6cecad"):

 
   if (UserPermissionsData::getAllBySQL("where permits_id=1 and user_id=".$_SESSION["user_id"])):   $product = PUData::getById(1); ?>
          <li class="nav-item">
            <a href="./?view=home"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="home")){ echo "active"; }?>">
              <i class="nav-icon fa fa-home"></i>
              <p style="font-size:15px">
               <?php echo $product->location;?>
              </p>
            </a>
          </li>
        <?php endif;  
     if (UserPermissionsData::getAllBySQL("where permits_id=2 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(2);  ?>

         

          <li class="nav-item">
            <a href="./?view=calendar" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="calendar")){ echo "active"; }?>">
             <i class="nav-icon fa fa-calendar"></i>
              <p style="font-size:15px">
               <?php echo $product->location;?>
<?php if ($qt_tot>0):?>
               <span class="right badge badge-danger"><?php echo $qt_tot;?></span>
                <?php endif; ?>
              
               
              </p>
            </a>
          </li>
<?php endif;  
  if (UserPermissionsData::getAllBySQL("where permits_id=5 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(5);   if (Core::$user->username=="krtavarez"):?>
                    <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="stocks")){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="stocks")){ echo "active"; }?>">   <i class="nav-icon fa fa-building"></i>
              <p style="font-size:15px">
               <?php echo $product->location;?> 
               
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
             
                <li class="nav-item">
                <a href="./?view=stocks&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="stocks" && $_GET["opt"]=="new")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Nuevo</p>
                </a>
              </li>
           
                <li class="nav-item">
                <a href="./?view=stocks&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="stocks" && ($_GET["opt"]=="all" || $_GET["opt"]=="edit"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Listado</p>
                </a>
              </li>
            </ul>
          </li>
           <?php endif; endif;   if (UserPermissionsData::getAllBySQL("where permits_id=3 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(3);  ?>

                    <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract")){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract")){ echo "active"; }?>">   <i class="nav-icon fa fa-edit"></i>
              <p style="font-size:15px">
               <?php echo $product->location;?> 
               
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                
<?php if(UserPermissionsData::getAllBySQL("where permits_id=17 and user_id=".$_SESSION["user_id"])):?>
                <li class="nav-item">
                <a href="./?view=contract&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && $_GET["opt"]=="new")|| ($_GET["view"]=="contract" && $_GET["opt"]=="modal")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Nuevo</p>
                </a>
              </li>
 <?php endif;?>               
               <li class="nav-item">
                <a href="./?view=contract&opt=running" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="running" || $_GET["opt"]=="received" || $_GET["opt"]=="random"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Por Recibir</p>
                </a>
              </li>

               <li class="nav-item">
                <a href="./?view=contract&opt=finished" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="finished"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Completado</p>
                </a>
              </li>
               
            </ul>
          </li>
     <?php endif;   if (UserPermissionsData::getAllBySQL("where permits_id=6 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(6);  ?>

                    <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars")){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars")){ echo "active"; }?>">   <i class="nav-icon fa fa-car"></i>
              <p style="font-size:15px">
               <?php echo $product->location;?> 
               
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                <a href="./?view=cars&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars" && $_GET["opt"]=="new")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Nuevo</p>
                </a>
              </li>
                <li class="nav-item">
                <a href="./?view=cars&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars" && ($_GET["opt"]=="all" || $_GET["opt"]=="edit"|| $_GET["opt"]=="description"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Listado</p>
                </a>
              </li>

                <li class="nav-item">
                <a href="./?view=cars&opt=galery" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars" && ($_GET["opt"]=="galery" || $_GET["opt"]=="view"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Galeria</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="./?view=cars&opt=traspase" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars" && ($_GET["opt"]=="traspase"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Traspasos</p>
                </a>
              </li>
            </ul>
          </li>
    <?php endif; if (UserPermissionsData::getAllBySQL("where permits_id=7 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(7); ?>  
              <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="tariff")){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="tariff")){ echo "active"; }?>">   <i class="nav-icon fa fa-list-ul"></i>
              <p style="font-size:15px">
               <?php echo $product->location;?> 
               
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                <a href="./?view=tariff&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="tariff" && $_GET["opt"]=="new")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Nuevo</p>
                </a>
              </li>
                <li class="nav-item">
                <a href="./?view=tariff&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="tariff" && ($_GET["opt"]=="all" || $_GET["opt"]=="edit"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Listado</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./?view=tariff&opt=package" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="tariff" && $_GET["opt"]=="package")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Paquete</p>
                </a>
              </li>
            </ul>
          </li>
<?php endif; if (UserPermissionsData::getAllBySQL("where permits_id=9 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(9);  ?>

  <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking")){ echo "menu-open"; }?>">
          
           <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking")){ echo "active"; }?>">   <i class="nav-icon fa fa-history"></i>
              <p style="font-size:15px">
               <?php echo $product->location;?> 
               
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
<?php 
if(UserPermissionsData::getAllBySQL("where permits_id=17 and user_id=".$_SESSION["user_id"])):?>
                <li class="nav-item">
                <a href="./?view=booking&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && $_GET["opt"]=="new")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Nuevo</p>
                </a>
              </li>
<?php endif;?>
                <li class="nav-item">
                <a href="./?view=booking&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && ($_GET["opt"]=="all" || $_GET["opt"]=="edit" || $_GET["opt"]=="delivery"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Por Entregar</p>
                </a>
              </li>
             
            </ul>
          </li> 
           
<?php endif; if (UserPermissionsData::getAllBySQL("where permits_id=8 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(8);  ?>
           <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="person")){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="person")){ echo "active"; }?>">
             <i class="nav-icon fa fa-users"></i>
              <p>
               <?php echo $product->location;?>

                <i class="fas fa-angle-left right"></i>
               
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                <a href="./?view=person&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="person" && $_GET["opt"]=="new")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Nuevo</p>
                </a>
              </li>
                <li class="nav-item">
                <a href="./?view=person&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="person" && ($_GET["opt"]=="all" || $_GET["opt"]=="edit"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Listado</p>
                </a>
              </li>
                </a>
              </li>
            </ul>
          </li>
     <?php endif;   if (UserPermissionsData::getAllBySQL("where permits_id=10 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(10);  ?>

                    <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="cotization")){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cotization")){ echo "active"; }?>">   <i class="nav-icon fa fa-th-list"></i>
              <p style="font-size:15px">
               <?php echo $product->location;?> 
               
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                <a href="./?view=cotization&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cotization" && $_GET["opt"]=="new")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Nueva</p>
                </a>
              </li>
                <li class="nav-item">
                <a href="./?view=cotization&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cotization" && ($_GET["opt"]=="all" || $_GET["opt"]=="edit"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Listado</p>
                </a>
              </li>

             
            </ul>
          </li>
    <?php endif; if (UserPermissionsData::getAllBySQL("where permits_id=12 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(12);  ?>

                   <li class="nav-item">
            <a href="./?view=messages&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="messages")){ echo "active"; }?>">
             <i class="nav-icon fa fa-comments"></i>
              <p>
               <?php echo $product->location;?>
               
              </p>
            </a>
          </li>
<?php endif;  if (UserPermissionsData::getAllBySQL("where permits_id=13 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(13);  ?>
       <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="maintenance"||$_GET["view"]=="balance"||$_GET["view"]=="finance"||$_GET["view"]=="receipt"||$_GET["view"]=="boxhistory"||$_GET["view"]=="box"||$_GET["view"]=="spendtype"||$_GET["view"]=="spendtypeisr"||$_GET["view"]=="b"||$_GET["view"]=="credit"||$_GET["view"]=="make")){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="maintenance"||$_GET["view"]=="balance"||$_GET["view"]=="finance"||$_GET["view"]=="receipt"||$_GET["view"]=="boxhistory"||$_GET["view"]=="box"||$_GET["view"]=="spendtype"||$_GET["view"]=="spendtypeisr"||$_GET["view"]=="b"||$_GET["view"]=="credit"||$_GET["view"]=="make")){ echo "active"; }?>">
              <i class="nav-icon fa fa-briefcase"></i>
              <p style="font-size:15px">
               <?php echo $product->location;?>
                <i class="fas fa-angle-left right"></i>
                
              </p>
            </a>
            <ul class="nav nav-treeview">
<?php 
if(UserPermissionsData::getAllBySQL("where permits_id=18 and user_id=".$_SESSION["user_id"])):?>
              <li class="nav-item">
                <a href="./?view=balance" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="balance")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Balance</p>
                </a>
              </li>
<?php endif; 
if(UserPermissionsData::getAllBySQL("where permits_id=19 and user_id=".$_SESSION["user_id"])):?>
               <li class="nav-item">
                <a href="./?view=credit&opt=clients" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="credit")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Deuda Cliente</p>
                </a>
              </li>
              
               <li class="nav-item">
                <a href="./?view=credit&opt=stock" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="stock")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Deuda Rent Car</p>
                </a>
              </li>
<?php endif;
if(UserPermissionsData::getAllBySQL("where permits_id=21 and user_id=".$_SESSION["user_id"])):?>
              <li class="nav-item">
                <a href="./?view=finance&opt=all&spends=Negocio" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Gastos</p>
                </a>
              </li>
<?php endif;
if(UserPermissionsData::getAllBySQL("where permits_id=22 and user_id=".$_SESSION["user_id"])):?>
              <li class="nav-item">
                <a href="./?view=box&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="box" || $_GET["view"]=="b")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Corte Caja</p>
                </a>
              </li>
 <?php endif;
if(UserPermissionsData::getAllBySQL("where permits_id=23 and user_id=".$_SESSION["user_id"])):?>
              <li class="nav-item">
                <a href="./?view=receipt&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="receipt")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Comprobantes DGI</p>
                </a>
              </li>
<?php endif;?>
              
            </ul>
          </li>
<?php endif; if (UserPermissionsData::getAllBySQL("where permits_id=14 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(14);  ?>
        <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="locations"||$_GET["view"]=="brands"||$_GET["view"]=="colors"||$_GET["view"]=="fuel"||$_GET["view"]=="insurance"||$_GET["view"]=="categories"||$_GET["view"]=="places"||$_GET["view"]=="f")){ echo "menu-open"; }?>">

            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="locations"||$_GET["view"]=="brands" ||$_GET["view"]=="colors"||$_GET["view"]=="fuel"||$_GET["view"]=="insurance"||$_GET["view"]=="categories"||$_GET["view"]=="places"||$_GET["view"]=="f")){ echo "active"; }?>">
         <i class="nav-icon fa fa-cubes"></i>
              <p style="font-size:15px">
                <?php echo $product->location;?>
                <i class="fas fa-angle-left right"></i> 
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./?view=places&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="places")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Lugares</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./?view=locations&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="locations")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Regiones</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./?view=brands&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="brands")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Marcas</p>
                </a>
              </li>
            
              <li class="nav-item">
                <a href="./?view=categories&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="categories")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Categorias</p>
                </a>
              </li>
              
              <li class="nav-item">
                <a href="./?view=colors&opt=all"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="colors")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Color</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="./?view=sure&opt=all"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="sure")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Seguro/Deducible</p>
                </a>
              </li>
           
            <li class="nav-item">
                <a href="./?view=insurance&opt=all"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="insurance")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Seguro/Vehicular</p>
                </a>
              </li>

               <li class="nav-item">
                <a href="./?view=f&opt=all"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="f")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Forma de Pago</p>
                </a>
              </li>
                         
            </ul>
          </li>
 <?php endif; if (UserPermissionsData::getAllBySQL("where permits_id=15 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(15); ?>
       <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="sellreports"||$_GET["view"]=="maintenancereport"||$_GET["view"]=="vouchersreports"||$_GET["view"]=="vouchersreports606"||$_GET["view"]=="vouchersreports607"||$_GET["view"]=="clientreports"||$_GET["view"]=="spendsreports"||$_GET["view"]=="vendorreports"||$_GET["view"]=="popularproductsreport"||$_GET["view"]=="paymentreport")){ echo "menu-open"; }?>">

            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="sellreports"||$_GET["view"]=="maintenancereport"||$_GET["view"]=="vouchersreports"||$_GET["view"]=="vouchersreports606"||$_GET["view"]=="vouchersreports607"||$_GET["view"]=="clientreports"||$_GET["view"]=="spendsreports"||$_GET["view"]=="vendorreports"||$_GET["view"]=="popularproductsreport"||$_GET["view"]=="paymentreport")){ echo "active"; }?>">
         <i class="nav-icon fas fa-copy"></i>
              <p style="font-size:15px">
                <?php echo $product->location;?>
                <i class="fas fa-angle-left right"></i>
                
              </p>
            </a>
            <ul class="nav nav-treeview">
      
              <li class="nav-item">
                <a href="./?view=sellreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="sellreports")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Rentas</p>
                </a>
              </li>
              
               <li class="nav-item">
                <a href="./?view=paymentreport" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="paymentreport")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Cobros Pendientes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./?view=maintenancereport" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="maintenancereport")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Mantenimientos</p>
                </a>
              </li>

               <li class="nav-item">
                <a href="./?view=vouchersreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="vouchersreports"||$_GET["view"]=="vouchersreports606"||$_GET["view"]=="vouchersreports607")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>DGII / 606/ 607</p>
                </a>
              </li>
               <li class="nav-item">
                <a href="./?view=spendsreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="spendsreports")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p> Gastos </p>
                </a>
              </li>

               <li class="nav-item">
                <a href="./?view=clientreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="clientreports")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Clientes Populares</p>
                </a>
              </li>

               <li class="nav-item">
                <a href="./?view=vendorreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="vendorreports")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Vendedores Populares</p>
                </a>
              </li>
     
            </ul>
          </li>
 <?php endif; if (UserPermissionsData::getAllBySQL("where permits_id=16 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(16);  ?>
       <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="users"||$_GET["view"]=="settings" ||$_GET["view"]=="permissions"||$_GET["view"]=="categories2"||$_GET["view"]=="activity"||$_GET["view"]=="session"||$_GET["view"]=="stocks")){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users"||$_GET["view"]=="settings" ||$_GET["view"]=="permissions"||$_GET["view"]=="categories2"||$_GET["view"]=="activity"||$_GET["view"]=="session"||$_GET["view"]=="stocks")){ echo "active"; }?>">
         <i class="nav-icon fa fa-cog"></i>
              <p style="font-size:15px">
                <?php echo $product->location;?>
                <i class="fas fa-angle-left right"></i>
                
              </p>
            </a>
            <ul class="nav nav-treeview">
                
             <li class="nav-item">
                <a href="./?view=settings" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="settings")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p> Configuracion</p>
                </a>
              </li>
            
             <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="users")){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p> Usuarios</p>
                   <i class="fas fa-angle-left right"></i>
                </a>
                  <ul class="nav nav-treeview">
             
                <li class="nav-item">
                <a href="./?view=users&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users" && $_GET["opt"]=="new")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Nuevo</p>
                </a>
              </li>
           
                <li class="nav-item">
                <a href="./?view=users&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users" && ($_GET["opt"]=="all"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Listado</p>
                </a>
              </li>
              
               <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="users" && ($_GET["opt"]=="newtype" || $_GET["opt"]=="type"))){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users" && ($_GET["opt"]=="newtype" || $_GET["opt"]=="type"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tipo</p>
                   <i class="fas fa-angle-left right"></i>
                </a>
                 <ul class="nav nav-treeview">
             
                <li class="nav-item">
                <a href="./?view=users&opt=newtype" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users" && $_GET["opt"]=="newtype")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Nuevo</p>
                </a>
              </li>
           
                <li class="nav-item">
                <a href="./?view=users&opt=type" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users" && ($_GET["opt"]=="type"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Listado</p>
                </a>
              </li>
              
              
            </ul>
              </li>
            </ul>
              </li>
         
             
               <li class="nav-item">
                <a href="./?view=session&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="session")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Inicio de Sesion</p>
                </a>
              </li>

               <li class="nav-item">
                <a href="./?view=activity" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="activity")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Actividad</p>
                </a>
              </li>


            </ul>
          </li>
               <?php endif;  endif; ?>
         
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>

    <!-- /.sidebar -->
<div class="sidebar-custom">
      <a  href="./?action=logout" style="color:white;" class="btn btn-link"><i class="fa  fa-power-off"></i></i></a>
      <a href="https://wa.me/18294945670" class="btn btn-link" style="color:white;">Soporte <i class="fa fa-question"></i></a>
    </div>
  </aside>
<!-- Modal -->
<div class="modal fade" id="myIMG" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-body">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title" id="myModalLabel">Cambiar Foto</h4>
</div>
<div class="modal-body">
<form method="post" class="form-horizontal" action="./?action=users&opt=updprofile" enctype="multipart/form-data">
  <div class="col-md-12 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 control-label">Foto del Perfil</label>
    <input type="file" name="image">
    
    </div>

<div class="col-md-12 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre</label>
    <input type="text" name="name" class="form-control" value="<?php echo Core::$user->name;?>" >
    
    </div>


<div class="col-md-12 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 control-label">Apellido</label>
    <input type="text" name="lastname" class="form-control"  value="<?php echo Core::$user->lastname;?>">
    
    </div>
<br>
<input type="hidden" name="user_id" class="form-control"  value="<?php echo Core::$user->id;?>">
    
<button type="submit" class="btn btn-block btn-success">Actualizar</button>
</form>
</div>

</div>
</div>
</div>  
<?php elseif(isset($_SESSION["client_id"])):
  $users = PersonData::getById($_SESSION["client_id"]);
  $msgs = MessageData::getUnreadedByUserId($_SESSION["client_id"],1);?>
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link">AREA DE CLIENTE</a>
      </li>
      
    </ul>
    
    <ul class="navbar-nav ml-auto">
  <!-- Messages Dropdown Menu -->

      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge"><?php echo count($msgs);?></span>
         </a>

        
       <?php if(count($msgs)>3):?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right este">
        <?php else:?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <?php endif;?>
          <?php foreach($msgs as $i):?>
           <a href="./?view=messages&opt=open&code=<?php echo $i->code;?>" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="<?php echo $i->url;?>" class="img-circle" style="width: 25%"> 
              <div class="media-body">
                <h3 class="dropdown-item-title">
                 <?php if($i->user_from!=$_SESSION["client_id"]):?>
                    <?php $u = $i->getFrom(); echo $u->name." ".$u->lastname;?>
                    <?php elseif($i->user_to!=$_SESSION["client_id"]):?>
                    <?php $u = $i->getTo(); echo $u->name." ".$u->lastname;?>
                  <?php endif; ?>
                
                                 </h3>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> <?php echo $i->created_at;?></p>
              
                <p class="text-sm"><?php echo $i->message;?></p>
              
              </div>

            </div>
            <!-- Message End -->
          </a>
           <?php endforeach; ?>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Tienes <?php echo count($msgs);?> Mensajes nuevos</a>
        </div>
         
      </li>
         

    </ul>
  </nav>
  <!-- /.navbar -->
   <!-- Main Sidebar Container -->
<aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a  class="brand-link" style="color: white;">
      <img src="CF-SYSTEMS/storage/configuration/<?php echo $ticket_image;?>" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light"><?php echo strtoupper($title);?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
<a data-toggle="modal" data-target="#myIMG">

        <div class="image">
          <?php
            $url = "CF-SYSTEMS/storage/profiles/user.png";
            if(file_exists($url)){
              echo "<img src='$url'  class='img-circle elevation-2'>";
            }
         
          ?>
          
        </div>
        <div class="info">
          <a style="color: #ddd;" class="d-block">
             <?php
               if($users){
           echo $users->name;
          
            }
             
                  
                  ?>
<br>
                    <i class="fa fa-circle text-green"></i> En Linea 
          </a>
      
        </div></a>


      </div>


      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

          <li class="nav-item">
            <a href="./?view=home"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="home")){ echo "active"; }?>">
              <i class="nav-icon fa fa-home"></i>
              <p style="font-size:15px">
               TABLERO
              </p>
            </a>
          </li>
<li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="stocks")){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" || $_GET["view"]=="contract")){ echo "active"; }?>">   <i class="nav-icon fa fa-building"></i>
              <p style="font-size:15px">
               RESERVAS
               
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
        
                <li class="nav-item">
                <a href="./?view=contract&opt=clients" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="clients"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Completadas</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item">
            <a href="./?view=messages&opt=clients" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="messages")){ echo "active"; }?>">
             <i class="nav-icon fa fa-comments"></i>
              <p>
               MENSAJERIA
               
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>

    <!-- /.sidebar -->
<div class="sidebar-custom">
      <a  href="./?action=logout" style="color:white;" class="btn btn-link"><i class="fa  fa-power-off"></i></i></a>
      <a href="https://wa.me/18294945670" class="btn btn-link" style="color:white;">Soporte <i class="fa fa-question"></i></a>
    </div>
  </aside>
<!-- Modal -->
<div class="modal fade" id="myIMG" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-body">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title" id="myModalLabel">Cambiar Foto</h4>
</div>
<div class="modal-body">
<form method="post" class="form-horizontal" action="./?action=users&opt=updprofile" enctype="multipart/form-data">
  <div class="col-md-12 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 control-label">Foto del Perfil</label>
    <input type="file" name="image">
    
    </div>

<div class="col-md-12 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nombre</label>
    <input type="text" name="name" class="form-control" value="<?php echo Core::$user->name;?>" >
    
    </div>


<div class="col-md-12 col-12">

    <label for="inputEmail1" class="col-md-12 col-12 control-label">Apellido</label>
    <input type="text" name="lastname" class="form-control"  value="<?php echo Core::$user->lastname;?>">
    
    </div>
<br>
<input type="hidden" name="user_id" class="form-control"  value="<?php echo Core::$user->id;?>">
    
<button type="submit" class="btn btn-block btn-success">Actualizar</button>
</form>
</div>

</div>
</div>
</div>  
 <?php endif;?>
     <!-- Content Wrapper. Contains page content -->
      <?php if(isset($_SESSION["user_id"]) || isset($_SESSION["client_id"])):?>
      <div class="content-wrapper">
        <?php View::load("index");?>
      </div><!-- /.content-wrapper -->

       <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2019-<?php echo date("Y");?></strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 24.2.29
    </div>
  </footer>
 </body>
        <?php else:?>
<body ondragstart="return false" onselectstart="return false" oncontextmenu="return false" style="background-image: url(CF-SYSTEMS/storage/configuration/<?php echo $type_img;?>); background-size:100% 100%; margin-top: 10%; ">
 <!-- Automatic element centering -->
<div class="lockscreen-wrapper my-2">
  <div class="lockscreen-logo"><?php
        
            $url = "CF-SYSTEMS/storage/configuration/$ticket_image";
            if(file_exists($url)){
              echo "<img src='$url'  class='elevation-2' style='width:100px; height:100px; border-radius:150px; background-color:#fff;'>";
            }
          
          ?>
          </div>

  <div class="lockscreen-logo" style="background-color: rgba(0,0,0,0.9); color:white;"><h3>

    <i class="fa fa-car" style="margin: 2%; width:90%; "> <?php echo $title; ?></i></h3>
  </div>
  <br>
  <!-- User name -->
  
  <!-- START LOCK SCREEN ITEM -->
  <div class="lockscreen-item">
    <!-- lockscreen image -->
    <div class="lockscreen-image">
      <img src="CF-SYSTEMS/storage/profiles/user.png" alt="User Image">
    </div>
    <!-- /.lockscreen-image -->

    <!-- lockscreen credentials (contains the form) -->
    <form class="lockscreen-credentials" id="login_form1" method="post">
      <div class="input-group">
         <input type="text" name="username" autocomplete="off" autofocus="" required class="form-control" placeholder="Usuario">
         <input type="password" name="password" required class="form-control" placeholder="Password" >
        <div class="input-group-append">
          <button type="submit" class="btn">
            <i class="fas fa-arrow-right text-muted"></i>
          </button>
        </div>
      </div>
    </form>
    <!-- /.lockscreen credentials -->

  </div>
  <!-- /.lockscreen-item -->
<div class="help-block text-center" style="color:white; background-color: rgba(0,0,0,0.9)">
    Usuario & contraseña para iniciar tu sesión
  </div>
  
<div class="text-center my-2" style="background-color: rgba(0,0,0,0.9)">
    <a href="https://wa.me/18294945670" style="color:white;">¿Necesitas ayuda?</a>
  </div>

  <div class="lockscreen-footer text-center " style="color:white; background-color: rgba(0,0,0,0.9)">
    Copyright &copy; 2019-<?php echo date("Y");?> <b><a href="https://www.assanpos.com" style="color:white;">ASSANPOS.com</a></b><br>
    Todos los derechos reservados
  </div>
</div>
<!-- /.center -->
 <script>
            jQuery(document).ready(function(){
            jQuery("#login_form1").submit(function(e){
                e.preventDefault();
                var formData = jQuery(this).serialize();
                $.ajax({
                  type: "POST",
                  url: "./?action=process&opt=login",
                  data: formData,
                  success: function(html){
                  if(html=='true')
                  {
                  $.jGrowl("Bienvenidos a ASSANPOS", { sticky: true });
                  $.jGrowl("Sistema de Inventario & Facturacion", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=home'  }, delay); 
                     
                  }else if(html=='profile')
                  {
                  $.jGrowl("Bienvenidos a ASSANPOS", { sticky: true });
                  $.jGrowl("Sistema de Inventario & Facturacion", { header: 'Acceso permitido' });
                  var delay = 1000;
                    setTimeout(function(){ window.location = './?view=profile'  }, delay); 
                  }else{
                  $.jGrowl("Por favor verifique su nombre de usuario y contraseña", { header: 'Error de inicio de sesion' });
                  }
                  }
                });
                return false;
              });
            });
            </script>
   
    </body> 
   
      <?php endif;?><!-- AdminLTE App -->
<script src="CF-SYSTEMS/dist/js/adminlte.js"></script>

<!-- PAGE PLUGINS -->
<script src="CF-SYSTEMS/plugins/dropzone/min/dropzone.min.js"></script>
<script src="CF-SYSTEMS/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- Select2 -->
<script src="CF-SYSTEMS/plugins/select2/js/select2.full.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="CF-SYSTEMS/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<!-- InputMask -->
<script src="CF-SYSTEMS/plugins/moment/moment.min.js"></script>
<script src="CF-SYSTEMS/plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="CF-SYSTEMS/plugins/daterangepicker/daterangepicker.js"></script>
<!-- bootstrap color picker -->
<script src="CF-SYSTEMS/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="CF-SYSTEMS/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- BS-Stepper -->
<script src="CF-SYSTEMS/plugins/bs-stepper/js/bs-stepper.min.js"></script>
<!-- jQuery Mapael -->
<script src="CF-SYSTEMS/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
<script src="CF-SYSTEMS/plugins/raphael/raphael.min.js"></script>
<script src="CF-SYSTEMS/plugins/jquery-mapael/jquery.mapael.min.js"></script>
<script src="CF-SYSTEMS/plugins/jquery-mapael/maps/usa_states.min.js"></script>
<!-- ChartJS -->
<script src="CF-SYSTEMS/plugins/chart.js/Chart.min.js"></script>
<script src="CF-SYSTEMS/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="CF-SYSTEMS/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

    </div><!-- ./wrapper -->
       <script src="CF-SYSTEMS/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- AdminLTE App -->
    <script src="CF-SYSTEMS/plugins/dist/js/app.min.js" type="text/javascript"></script>

<script src="CF-SYSTEMS/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="CF-SYSTEMS/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="CF-SYSTEMS/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="CF-SYSTEMS/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="CF-SYSTEMS/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="CF-SYSTEMS/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="CF-SYSTEMS/plugins/jszip/jszip.min.js"></script>

<!-- Summernote -->
<script src="CF-SYSTEMS/plugins/summernote/summernote-bs4.min.js"></script>
<!-- CodeMirror -->
<script src="CF-SYSTEMS/plugins/codemirror/codemirror.js"></script>
<script src="CF-SYSTEMS/plugins/codemirror/mode/css/css.js"></script>
<script src="CF-SYSTEMS/plugins/codemirror/mode/xml/xml.js"></script>
<script src="CF-SYSTEMS/plugins/codemirror/mode/htmlmixed/htmlmixed.js"></script>

<script src="CF-SYSTEMS/plugins/pdfmake/pdfmake.min.js"></script>
<script src="CF-SYSTEMS/plugins/pdfmake/vfs_fonts.js"></script>
<script src="CF-SYSTEMS/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="CF-SYSTEMS/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="CF-SYSTEMS/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script src="CF-SYSTEMS/plugins/fullcalendar/main.js"></script>


<!-- Bootstrap -->
<script src="CF-SYSTEMS/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- jQuery UI -->
<script src="CF-SYSTEMS/plugins/jquery-ui/jquery-ui.min.js"></script>

<!-- fullCalendar 2.2.5 -->


    <script type="text/javascript">
      $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

      
    </script>
    <!-- Optionally, you can add Slimscroll and FastClick plugins.
          Both of these plugins are recommended to enhance the
          user experience. Slimscroll is required when using the
          fixed layout. -->
<script src="CF-SYSTEMS/vendor/jGrowl/jquery.jgrowl.js"></script>   
        <script>
        $(function() {
          $('.tooltip').tooltip();  
          $('.tooltip-left').tooltip({ placement: 'left' });  
          $('.tooltip-right').tooltip({ placement: 'right' });  
          $('.tooltip-top').tooltip({ placement: 'top' });  
          $('.tooltip-bottom').tooltip({ placement: 'bottom' });
          $('.popover-left').popover({placement: 'left', trigger: 'hover'});
          $('.popover-right').popover({placement: 'right', trigger: 'hover'});
          $('.popover-top').popover({placement: 'top', trigger: 'hover'});
          $('.popover-bottom').popover({placement: 'bottom', trigger: 'hover'});
          $('.notification').click(function() {
            var $id = $(this).attr('id');
            switch($id) {
              case 'notification-sticky':
                $.jGrowl("Stick this!", { sticky: true });
              break;
              case 'notification-header':
                $.jGrowl("A message with a header", { header: 'Important' });
              break;
              default:
                $.jGrowl("Hello world!");
              break;
            }
          });
        });
        </script>
<script type="text/javascript">
      const $dropdown = $(".dropdown");
const $dropdownToggle = $(".dropdown-toggle");
const $dropdownMenu = $(".dropdown-menu");
const showClass = "show";
 
$(window).on("load resize", function() {
  if (this.matchMedia("(min-width: 768px)").matches) {
    $dropdown.hover(
      function() {
        const $this = $(this);
        $this.addClass(showClass);
        $this.find($dropdownToggle).attr("aria-expanded", "true");
        $this.find($dropdownMenu).addClass(showClass);
      },
      function() {
        const $this = $(this);
        $this.removeClass(showClass);
        $this.find($dropdownToggle).attr("aria-expanded", "false");
        $this.find($dropdownMenu).removeClass(showClass);
      }
    );
  } else {
    $dropdown.off("mouseenter mouseleave");
  }
});

    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    //Money Euro
    $('[data-mask]').inputmask()

    //Date picker
    $('#reservationdate').datetimepicker({
        format: 'L'
    });

    //Date and time picker
    $('#reservationdatetime').datetimepicker({ icons: { time: 'far fa-clock' } });

    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'DD/MM/YYYY hh:mm a'
      }
    })
    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#reportrange span').html(start.format('D MMMM, YYYY') + ' - ' + end.format('D MMMM, YYYY'))
      }
    )

    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })

    //Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox()

    //Colorpicker
    $('.my-colorpicker1').colorpicker()
    //color picker with addon
    $('.my-colorpicker2').colorpicker()

    $('.my-colorpicker2').on('colorpickerChange', function(event) {
      $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
    })

    $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    })

  // BS-Stepper Init
  document.addEventListener('DOMContentLoaded', function () {
    window.stepper = new Stepper(document.querySelector('.bs-stepper'))
  })

  // DropzoneJS Demo Code Start
  Dropzone.autoDiscover = false

  // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
  var previewNode = document.querySelector("#template")
  previewNode.id = ""
  var previewTemplate = previewNode.parentNode.innerHTML
  previewNode.parentNode.removeChild(previewNode)

  var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
    url: "/target-url", // Set the url
    thumbnailWidth: 80,
    thumbnailHeight: 80,
    parallelUploads: 20,
    previewTemplate: previewTemplate,
    autoQueue: false, // Make sure the files aren't queued until manually added
    previewsContainer: "#previews", // Define the container to display the previews
    clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
  })

  myDropzone.on("addedfile", function(file) {
    // Hookup the start button
    file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file) }
  })

  // Update the total progress bar
  myDropzone.on("totaluploadprogress", function(progress) {
    document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
  })

  myDropzone.on("sending", function(file) {
    // Show the total progress bar when upload starts
    document.querySelector("#total-progress").style.opacity = "1"
    // And disable the start button
    file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
  })

  // Hide the total progress bar when nothing's uploading anymore
  myDropzone.on("queuecomplete", function(progress) {
    document.querySelector("#total-progress").style.opacity = "0"
  })

  // Setup the buttons for all transfers
  // The "add files" button doesn't need to be setup because the config
  // `clickable` has already been specified.
  document.querySelector("#actions .start").onclick = function() {
    myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
  }
  document.querySelector("#actions .cancel").onclick = function() {
    myDropzone.removeAllFiles(true)
  }
  // DropzoneJS Demo Code End
</script>
  
</html>

