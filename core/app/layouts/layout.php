<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <?php if(isset($_SESSION["user_id"])):?>
    <title><?php echo strtoupper(StockData::getPrincipal()->name); ?> </title>
    <?php else:?>
    <title><?php echo strtoupper(StockData::getFPrincipal(1)->name); ?> </title>
    <?php endif;?>
 <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

 <link href="CF-SYSTEMS/storage/configuration/<?php echo StockData::getFPrincipal(1)->ticket_image; ?>" rel="shortcut icon"/>
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/fullcalendar/main.css">
  
  <!-- Toastr -->
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/toastr/toastr.min.css">


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
  <link rel="stylesheet" href="CF-SYSTEMS/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
  <!-- BS Stepper -->
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/bs-stepper/css/bs-stepper.min.css">
  <!-- dropzonejs -->
  <link rel="stylesheet" href="CF-SYSTEMS/plugins/dropzone/min/dropzone.min.css">
  
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
           <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    
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

.select2.select2-container {
  width: 100% !important;
}

.select2.select2-container .select2-selection {
  border: 1px solid #ccc;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 5px;
  height: 37px;
  margin-bottom: 15px;
  outline: none !important;
  transition: all .15s ease-in-out;
  background: #343a40;
}

.select2.select2-container .select2-selection .select2-selection__rendered {
  color: white;
  line-height: 32px;
  padding-right: 33px;
}

.select2.select2-container .select2-selection .select2-selection__arrow {
  background: #343a40;
  border-left: 1px solid #ccc;
  -webkit-border-radius: 0 3px 3px 0;
  -moz-border-radius: 0 3px 3px 0;
  border-radius: 0 3px 3px 0;
  height: 32px;
  width: 33px;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--single {
  background: #343a40;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--single .select2-selection__arrow {
  -webkit-border-radius: 0 3px 0 0;
  -moz-border-radius: 0 3px 0 0;
  border-radius: 0 3px 0 0;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--multiple {
  border: 1px solid #34495e;
}

.select2.select2-container .select2-selection--multiple {
  height: auto;
  min-height: 34px;
}

.select2.select2-container .select2-selection--multiple .select2-search--inline .select2-search__field {
  margin-top: 0;
  height: 32px;
}

.select2.select2-container .select2-selection--multiple .select2-selection__rendered {
  display: block;
  padding: 0 4px;
  line-height: 29px;
}

.select2.select2-container .select2-selection--multiple .select2-selection__choice {
  background-color: #f8f8f8;
  border: 1px solid #ccc;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  margin: 4px 4px 0 0;
  padding: 0 6px 0 22px;
  height: 24px;
  line-height: 24px;
  font-size: 12px;
  position: relative;
}

.select2.select2-container .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove {
  position: absolute;
  top: 0;
  left: 0;
  height: 22px;
  width: 22px;
  margin: 0;
  text-align: center;
  color: #e74c3c;
  font-weight: bold;
  font-size: 16px;
}

.select2-container .select2-dropdown {
  background: transparent;
  border: none;
  margin-top: -5px;
}

.select2-container .select2-dropdown .select2-search {
  padding: 0;
}

.select2-container .select2-dropdown .select2-search input {
  outline: none !important;
  border: 1px solid #34495e !important;
  border-bottom: none !important;
  padding: 4px 6px !important;
}

.select2-container .select2-dropdown .select2-results {
  padding: 0;
}

.select2-container .select2-dropdown .select2-results ul {
  background: #343a40;
  border: 1px solid #34495e;
}

.select2-container .select2-dropdown .select2-results ul .select2-results__option--highlighted[aria-selected] {
  background-color: #3498db;
}



.nav-pills .nav-link.active,
.show>.nav-pills .nav-link{
    background: #333 !important;
    color: orange !important;
}

ul.nav.nav-pills li > * :hover {
  color: orange;
  
}



</style>

<script>
    
    
</script>
</head>

<?php if(isset($_GET["view"]) && ($_GET["view"]<>"web")):?>   
<body ondragstart="return false" onselectstart="return false" oncontextmenu="return false" class="<?php if(isset($_SESSION["user_id"]) || isset($_SESSION['client_id'])):?> hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed <?php else:?>login-page<?php endif; ?>">

<?php endif; ?>

<?php if(isset($_GET["view"]) && ($_GET["view"]=="web")):?>   
    <body ondragstart="return false" onselectstart="return false" oncontextmenu="return false" class="<?php if(isset($_SESSION["user_id"]) || isset($_SESSION['client_id'])):?> hold-transition dark-mode sidebar-mini sidebar-collapse layout-fixed <?php else:?>login-page<?php endif; ?>">
<?php endif; ?>

<!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center" style="background-color: #333 !important; ">
    <img class="animation__wobble" src="CF-SYSTEMS/storage/configuration/<?php echo StockData::getFPrincipal(1)->ticket_image; ?>" alt="assanpos" height="200" width="200">
  </div>

  
<div class="wrapper">
  <?php if(isset($_SESSION["user_id"])):
  $users = UserData::getById($_SESSION["user_id"]);
  date_default_timezone_set("America/Santo_Domingo");
  
$wt_tot = 0;
foreach(WaitData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id) as $wt):
$wt_tot++;
endforeach;

?>


  <!-- Navbar -->
   <nav class="main-header navbar navbar-expand navbar-dark" style="background-color: #222;">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    
      
    </ul>


<?php

$cs_tot = 0;
foreach(CarsData::getAll() as $cs):
if($cs->kms>=($cs->kms_current+$cs->charge_kms)):
  $cs_tot++;
endif; 
endforeach;

$conf_tot = 0;
foreach(BookingData::getAllBySQL("where firma='' and status=0 and stock_id=".StockData::getPrincipal()->id) as $conf):
$conf_tot++;
endforeach;


$kq_tot = 0;
foreach(ReminderData::getAllBySQL("where status=0 and user_id=".core::$user->id." and stock_id=".StockData::getPrincipal()->id) as $sell):
if(date("Y-m-d",strtotime($sell->start_at))<=date("Y-m-d")):
$kq_tot++;
endif; 
endforeach;


$q0_tot = 0;
$qt_tot = 0;
$qt_tot2 = 0;
foreach(BookingData::getAllBySQL("where type_id=1 and stock_id=".StockData::getPrincipal()->id." and status=0 order by start_at desc") as $qt_totsell):
$q0x_ttime =  date("Y-m-d",strtotime($qt_totsell->start_at)); 
if(date("Y-m-d")==$q0x_ttime):
  $q0_tot++;
endif; 

$qx_ttime =  date("Y-m-d",strtotime($qt_totsell->start_at."- 1 days")); 
if(date("Y-m-d")==$qx_ttime):
  $qt_tot++;
endif; 

$qx_t2ime =  date("Y-m-d",strtotime($qt_totsell->start_at."- 2 days")); 
if(date("Y-m-d")==$qx_t2ime):
  $qt_tot2++;
endif; 
endforeach;

$qt_t0t = 0;
$qt_t2t = 0;
$qt_t2t2 = 0;
foreach(BookingData::getAllBySQL("where type_id=1 and stock_id=".StockData::getPrincipal()->id." and status=1 order by end_at desc") as $q2t_totsell):
$q0xt_ttime =  date("Y-m-d",strtotime($q2t_totsell->end_at)); 
if(date("Y-m-d")==$q0xt_ttime):
  $qt_t0t++;
endif; 


$qxt_ttime = date("Y-m-d",strtotime($q2t_totsell->end_at."- 1 days")); 
if(date("Y-m-d")==$qxt_ttime):
  $qt_t2t++;
endif; 

$qxt_t2ime = date("Y-m-d",strtotime($q2t_totsell->end_at."- 2 days")); 
  if(date("Y-m-d")==$qxt_t2ime):
  $qt_t2t2++;
endif; 

endforeach;

$qt_t3t = 0;
foreach(BookingData::getAllBySQL("where type_id=1 and stock_id=".StockData::getPrincipal()->id." and status=1 order by end_at asc") as $q3t_totsell):
$q3t_ttime = date("Y-m-d",strtotime($q3t_totsell->end_at));
if(date("Y-m-d")>=$q3t_ttime):
  $qt_t3t++;
endif; 
endforeach;

$in_tot = 0;
foreach(CarsData::getAllBySQL("where date_insurance!=0000-00-00 order by date_insurance desc") as $ins_tot):
$insurancx = date("Y-m-d",strtotime($ins_tot->date_insurance."- 30 days"));
if(date("Y-m-d")>=$insurancx):
  $in_tot++;
endif; 
endforeach;

$in_t2t = 0;
foreach(CarsData::getAllBySQL("where date2_insurance!=0000-00-00 order by date2_insurance desc ") as $ins_t2t):
$insurancx2 = date("Y-m-d",strtotime($ins_t2t->date2_insurance."- 30 days"));
if(date("Y-m-d")>=$insurancx2):
  $in_t2t++;
endif; 
endforeach;
?>

  <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->


      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-car"></i>
          <span class="badge badge-danger navbar-badge"><?php echo $qt_t3t;?></span>
        </a>

       <?php if(($qt_t3t)>3):?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right este" style="background-color: #333;">
        <?php else:?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="background-color: #333;">
        <?php endif;?>
          <span class="dropdown-item dropdown-header"><h6><i class="fa fa-car"></i>  
<?php 
switch (Core::$user->language){
 case 'ES': echo "Atrasados"; break;
 case 'EN': echo "Overdue"; break;
}
?>
</h6></span>
         <div class="dropdown-divider"></div>
    <?php if($qt_t3t>0):
    foreach(BookingData::getAllBySQL("where type_id=1 and stock_id=".StockData::getPrincipal()->id." and status=1 order by end_at asc") as $c2tsell):
        $c2t_ttime = date("Y-m-d",strtotime($c2tsell->end_at." -4 hours")); 
        if(date("Y-m-d")>=$c2t_ttime):
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
             
             <p><span class="float-right text-sm text-warning"><i class="far fa-clock mr-1"></i><?php echo date("Y-m-d h:i a", strtotime($c2tsell->end_at." -4 hours")); ?></span></p>
              
              
              
              </div>

            </div>
            <!-- Message End -->
          </a>  
        <?php endif;  endforeach; endif; ?>

    <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Tienes $qt_t3t Aviso de Atrasados"; break;
 case 'EN': echo "You have $qt_t3t Overdue Notice"; break;
}
?>
</a>
        </div>
      </li>
<!--//////////////////////////////////////////////////////////////////////////// -->

<?php if(StockData::getPrincipal()->method=="1"):?> 
<li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-tint"></i>
          <span class="badge badge-warning navbar-badge"><?php echo $cs_tot;?></span>
        </a>

       <?php if(($cs_tot)>3):?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right este" style="background-color: #333;">
        <?php else:?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="background-color: #333;">
        <?php endif;?>    
        <span class="dropdown-item dropdown-header"><h6><i class="fa fa-tint"></i> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Cambio de Aceite"; break;
 case 'EN': echo "Oil Change"; break;
}
?>
</h6></span>
         <div class="dropdown-divider"></div>
    <?php 
    if($cs_tot>0):
    foreach(CarsData::getAll() as $km):
    if($km->kms>=($km->kms_current+$km->charge_kms)):?> 
      <a  class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <div class="media-body">
              <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "REVISION FUE ".number_format($km->kms_current,0,".",",")." KM"; break;
 case 'EN': echo "REVISION WAS ".number_format($km->kms_current,0,".",",")." KM"; break;
}
?></p>
              <p class="text-sm"><i class="fas fa-car"></i> <?php echo strtoupper($km->getBrand()->name." ".$km->name." ".$km->year." ".$km->getExColor()->name)." [".$km->token."] ";?></p>
               <h3 class="dropdown-item-title">
                  <span class="float-right text-sm "><i class="far fa-clock mr-1"></i>
<?php 
switch (Core::$user->language){
 case 'ES': echo number_format($km->kms,0,".",",")." KM ACTUAL"; break;
 case 'EN': echo number_format($km->kms,0,".",",")." CURRENT KM"; break;
}
?></span>
                </h3>
         <p class="text-sm"><span class="badge badge-warning">  
<?php 
switch (Core::$user->language){
 case 'ES': echo "Mantenimiento"; break;
 case 'EN': echo "Maintenance"; break;
}
?><i class="fas fa-history"></i></span></p> 
            </div>
          </div>
            <!-- Message End -->
          </a>
  <?php endif; endforeach; endif; ?>

    <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Tienes $cs_tot Aviso de Aceite"; break;
 case 'EN': echo "You have $cs_tot Oil Warning"; break;
}
?></a>
        </div>
      </li>
<?php endif;?>
<!--//////////////////////////////////////////////////////////////////////////// -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-history"></i>
          <span class="badge badge-secondary navbar-badge"><?php echo ($in_tot+$in_t2t);?></span>
        </a>

       
       <?php if(($in_tot+$in_t2t)>2):?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right este" style="background-color: #333;">
        <?php else:?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="background-color: #333;">
        <?php endif;?>
           <span class="dropdown-item dropdown-header"><h6><i class="fa fa-car"></i> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Vencimiento Seguro"; break;
 case 'EN': echo "Safe Maturity"; break;
}
?></h6></span>
         <div class="dropdown-divider"></div>
    <?php 
    if($in_tot>0):
    foreach(CarsData::getAllBySQL("where date_insurance!=0000-00-00 order by date_insurance desc ") as $ins):
        
       $insurance = date("Y-m-d",strtotime($ins->date_insurance."- 30 days"));
        if(date("Y-m-d")>=$insurance):?>  
        <a  class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <div class="media-body">
              <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "SEGURO DE LEY: "; break;
 case 'EN': echo "LAW INSURANCE:"; break;
}
?></p>
             
              <p class="text-sm"><i class="fas fa-car"></i> <?php echo strtoupper($ins->getBrand()->name." ".$ins->name." ".$ins->year." ".$ins->getExColor()->name)." ".$ins->chassis." [".$ins->token."] ";?></p>
               <h3 class="dropdown-item-title">
                  <span class="float-right text-sm "><i class="far fa-clock mr-1"></i><?php echo date("Y-m-d", strtotime("$ins->date_insurance")); ?></span>
                </h3>
              <?php 
        if(date("Y-m-d",strtotime($ins->date_insurance."- 15 days"))<=date("Y-m-d") && date("Y-m-d")<=date("Y-m-d",strtotime($ins->date_insurance))) :?> 
         <p class="text-sm"><span class="badge badge-warning">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Quedan 15 dias"; break;
 case 'EN': echo "15 days left"; break;
}
?><i class="fas fa-history"></i></span></p> 
        <?php elseif(date("Y-m-d",strtotime($ins->date_insurance."- 30 days"))<=date("Y-m-d") && date("Y-m-d",strtotime($ins->date_insurance."- 15 days"))>=date("Y-m-d") && date("Y-m-d")<=date("Y-m-d",strtotime($ins->date_insurance))) :?> 
              <p class="text-sm"><span class="badge badge-success">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Quedan 30 dias"; break;
 case 'EN': echo "30 days left"; break;
}
?><i class="fas fa-history"></i></span></p>
        <?php elseif(date("Y-m-d",strtotime($ins->date_insurance))>=date("Y-m-d")):?> 
          <p class="text-sm"><span class="badge badge-danger"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Vencido"; break;
 case 'EN': echo "Defeated"; break;
}
?><i class="fas fa-history"></i></span></p> 
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
        if(date("Y-m-d")>=$insurance2):?>  
        <a class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <div class="media-body">
              <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "SEGURO DE FULL: "; break;
 case 'EN': echo "FULL INSURANCE:"; break;
}
?> </p>
             
              <p class="text-sm"><i class="fas fa-car"></i> <?php echo strtoupper($ins2->getBrand()->name." ".$ins2->name." ".$ins2->year." ".$ins2->getExColor()->name)." ".$ins2->chassis." [".$ins2->token."] ";?></p>
               <h3 class="dropdown-item-title">
                  <span class="float-right text-sm "><i class="far fa-clock mr-1"></i><?php echo date("Y-m-d", strtotime("$ins2->date2_insurance")); ?></span>
                </h3>
              <?php 
        
        if(date("Y-m-d",strtotime($ins2->date2_insurance."- 15 days"))<=date("Y-m-d") && date("Y-m-d")<=date("Y-m-d",strtotime($ins2->date2_insurance))) :?> 
         <p class="text-sm"><span class="badge badge-warning"> <?php 
switch (Core::$user->language){
 case 'ES': echo "Quedan 15 dias"; break;
 case 'EN': echo "15 days left"; break;
}
?><i class="fas fa-history"></i></span></p> 
        <?php elseif(date("Y-m-d",strtotime($ins2->date2_insurance."- 30 days"))<=date("Y-m-d") && date("Y-m-d",strtotime($ins2->date2_insurance."- 15 days"))>=date("Y-m-d") && date("Y-m-d")<=date("Y-m-d",strtotime($ins2->date2_insurance))) :?> 
              <p class="text-sm"><span class="badge badge-success"><?php 
switch (Core::$user->language){
 case 'ES': echo "Quedan 30 dias"; break;
 case 'EN': echo "30 days left"; break;
}
?><i class="fas fa-history"></i></span></p>
        <?php elseif(date("Y-m-d",strtotime($ins2->date2_insurance))>=date("Y-m-d")):?> 
          <p class="text-sm"><span class="badge badge-danger"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Vencido"; break;
 case 'EN': echo "Defeated"; break;
}
?><i class="fas fa-history"></i></span></p> 
        <?php endif;?>
            </div>
          </div>
            <!-- Message End -->
          </a>
  <?php endif; endforeach; endif; ?>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Tienes ".($in_tot+$in_t2t)." Aviso de Vencimiento"; break;
 case 'EN': echo "You have ".($in_tot+$in_t2t)." Expiration Notice"; break;
}
?></a>
        </div>
         
      </li>

 <?php if(StockData::getPrincipal()->method=="1"):?> 
<li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-calendar-check"></i>
          <span class="badge badge-info navbar-badge"><?php echo  ($qt_tot+$qt_tot2+$q0_tot);?></span>
        </a>

       <?php if(($qt_tot+$qt_tot2+$q0_tot)>2):?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right este" style="background-color: #333;">
        <?php else:?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="background-color: #333;">
        <?php endif;?>
        <span class="dropdown-item dropdown-header"><h6><i class="fa fa-calendar-check"></i>  
<?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculos A Entregar"; break;
 case 'EN': echo "Vehicles to be delivered"; break;
}
?></h6></span>
         <div class="dropdown-divider"></div>
          <?php 
          if($q0_tot>0 ||$qt_tot>0 || $qt_tot2>0):
      foreach(BookingData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id." and status=0 order by start_at asc") as $qtsell): 
        $q0_ttime = date("Y-m-d",strtotime($qtsell->start_at)); 
        if(date("Y-m-d")==$q0_ttime):
          $product = PersonData::getById($qtsell->person_id);
          $cars = CarsData::getById($qtsell->car_id);?>  
         <div class="dropdown-divider"></div>
        <a  class="dropdown-item dropdown-footer"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Sale Hoy"; break;
 case 'EN': echo "Out Today"; break;
}
?></a>
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
        if(date("Y-m-d")==$qt_ttime):
          $product = PersonData::getById($qtsell->person_id);
          $cars = CarsData::getById($qtsell->car_id);?>  
          <div class="dropdown-divider"></div>
        <a  class="dropdown-item dropdown-footer"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Sale Mañana"; break;
 case 'EN': echo "Comes out tomorrow"; break;
}
?></a>
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
  if(date("Y-m-d")==$qt_t2ime):
          $product = PersonData::getById($qtsell->person_id);
          $cars = CarsData::getById($qtsell->car_id);?>  
          <div class="dropdown-divider"></div>
        <a  class="dropdown-item dropdown-footer"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Sale Pasado Mañana"; break;
 case 'EN': echo "It leaves the day after tomorrow"; break;
}
?></a>
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
          <a href="#" class="dropdown-item dropdown-footer"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Tienes " .($qt_tot+$qt_tot2). " Entrega Nueva"; break;
 case 'EN': echo "You have ".($qt_tot+$qt_tot2). " New Delivery"; break;
}
?></a>
        </div>
         
      </li>
<?php endif;?>

      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-calendar-times"></i>
          <span class="badge badge-success navbar-badge"><?php echo ($qt_t2t+$qt_t2t2+$qt_t0t);?></span>
        </a>
       <?php if(($qt_t2t+$qt_t2t2+$qt_t0t)>2):?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right este" style="background-color: #333;">
        <?php else:?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="background-color: #333;">
        <?php endif;?>
          <span class="dropdown-item dropdown-header"><h6><i class="fa fa-calendar-times"></i>  
<?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculos A Recibir"; break;
 case 'EN': echo "Vehicles To Receive"; break;
}
?></h6></span>
         <div class="dropdown-divider"></div>

          <?php  if($qt_t0t>0 || $qt_t2t>0 || $qt_t2t2>0):
      foreach(BookingData::getAllBySQL("where stock_id=".StockData::getPrincipal()->id." and status=1 order by end_at asc") as $q2tsell):
        $q0t_ttime = date("Y-m-d",strtotime($q2tsell->end_at)); 
        if(date("Y-m-d")==$q0t_ttime):
          $product = PersonData::getById($q2tsell->person_id);
          if($q2tsell->car2_id>0):
          
          $cars = CarsData::getById($q2tsell->car2_id);?>  
         <div class="dropdown-divider"></div>
        <a  class="dropdown-item dropdown-footer"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Entra Hoy"; break;
 case 'EN': echo "Enter Today"; break;
}
?></a>
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
         <?php else: $cars = CarsData::getById($q2tsell->car_id);?>  
         <div class="dropdown-divider"></div>
        <a  class="dropdown-item dropdown-footer"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Entra Hoy"; break;
 case 'EN': echo "Enter Today"; break;
}
?></a>
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
  <?php endif; endif; $q2t_ttime = date("Y-m-d",strtotime($q2tsell->end_at."- 1 days")); 
        if(date("Y-m-d")==$q2t_ttime):
          $product = PersonData::getById($q2tsell->person_id);
          $cars = CarsData::getById($q2tsell->car_id);?>  
         <div class="dropdown-divider"></div>
        <a  class="dropdown-item dropdown-footer"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Entra Mañana"; break;
 case 'EN': echo "Come in tomorrow"; break;
}
?></a>
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
  if(date("Y-m-d")==$q2t_t2imx):
          $product = PersonData::getById($q2tsell->person_id);
          $cars = CarsData::getById($q2tsell->car_id);?>  
          <div class="dropdown-divider"></div>
        <a  class="dropdown-item dropdown-footer"> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Entra Pasado Mañana"; break;
 case 'EN': echo "Enter the day after tomorrow"; break;
}
?></a>
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
          <a href="#" class="dropdown-item dropdown-footer">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Tienes ". ($qt_t0t+$qt_t2t+$qt_t2t2). " Recibo Nuevo"; break;
 case 'EN': echo "Have ". ($qt_t0t+$qt_t2t+$qt_t2t2). " New Receipt"; break;
}
?></a>
        </div>
         
      </li>

  <!-- Messages Dropdown Menu -->


         

<?php 

$cnt_cf = 0;
//////////////////// CONSUMIDOR FINAL  ///////////////// 
      $cfinal = CData::getGroupByDateOp(2,StockData::getPrincipal()->id);
      $cf =  $cfinal[0]->c!=null?$cfinal[0]->c:0;
      if($cf==0 ||  $cf<=10):
      $cnt_cf++;
      endif;
      
$cnt_cfs = 0;
//////////////////// CREDITO FISCAL ///////////////// 
      $cfiscal = CData::getGroupByDateOp(1,StockData::getPrincipal()->id);
      $cf = $cfiscal[0]->c!=null?$cfiscal[0]->c:0;
      if( $cfs==0 ||  $cfs<=10):
      $cnt_cfs++;
      endif;
      
$cnt_cnc = 0;
//////////////////// NOTA DE CREDITO ///////////////// 
      $ccredito = CData::getGroupByDateOp(4,StockData::getPrincipal()->id);
      $cn = $ccredito[0]->c!=null?$ccredito[0]->c:0;
      if($cn==0 ||  $cn<=10):
      $cnt_cnc++;
      endif;
      
$cnt_cnd = 0;
//////////////////// NOTAS DE DEBITO ///////////////// 
      $cdebito = CData::getGroupByDateOp(3,StockData::getPrincipal()->id);
      $cd = $cdebito[0]->c!=null?$cdebito[0]->c:0;
      if($cd==0 ||  $cd<=10):
      $cnt_cnd++;
      endif;
      
$cnt_ccp = 0;
//////////////////// COMPRAS ///////////////// 
      $ccompras = CData::getGroupByDateOp(11,StockData::getPrincipal()->id);
      $ccp = $ccompras[0]->c!=null?$ccompras[0]->c:0;
      if($ccp==0 ||  $ccp<=10):
      $cnt_ccp++;
      endif;
      
$cnt_cru = 0;
//////////////////// REGISTRO UNICO DE INGRESOS ///////////////// 
      $crunico = CData::getGroupByDateOp(12,StockData::getPrincipal()->id);
      $cru = $crunico[0]->c!=null?$crunico[0]->c:0;
      if($cru==0 ||  $cru<=10):
      $cnt_cru++;
      endif;
      
$cnt_cgm = 0;
//////////////////// GASTOS MENORES ///////////////// 
      $cgmenores = CData::getGroupByDateOp(13,StockData::getPrincipal()->id);
      $cgm = $cgmenores[0]->c!=null?$cgmenores[0]->c:0;
      if($cgm==0 ||  $cgm<=10):
      $cnt_cgm++;
      endif;
      
      
$cnt_crs = 0;
//////////////////// REGIMENES ESPECIALES ///////////////// 
      $crespecial = CData::getGroupByDateOp(14,StockData::getPrincipal()->id);
      $crs = $crespecial[0]->c!=null?$crespecial[0]->c:0;
      if($crs==0 ||  $crs<=10):
      $cnt_crs++;
      endif;
      
$cnt_cgob = 0;
//////////////////// GUBERNAMENTAL ///////////////// 
      $cgobernamental = CData::getGroupByDateOp(15,StockData::getPrincipal()->id);
      $cgob = $cgobernamental[0]->c!=null?$cgobernamental[0]->c:0;
      if($cgob==0 ||  $cgob<=10):
      $cnt_cgob++;
      endif;
      
$cnt_cexp = 0;
//////////////////// EXPORTACIONES ///////////////// 
      $cexportaciones = CData::getGroupByDateOp(16,StockData::getPrincipal()->id);
      $cexp = $cexportaciones[0]->c!=null?$cexportaciones[0]->c:0;
      if($cexp==0 ||  $cexp<=10):
      $cnt_cexp++;
      endif;
      
$cnt_cpext = 0;
//////////////////// PAGOS AL EXTERIOR ///////////////// 
      $cpexterior = CData::getGroupByDateOp(17,StockData::getPrincipal()->id);
      $cpext = $cpexterior[0]->c!=null?$cpexterior[0]->c:0;
      if($cpext==0 ||  $cpext<=10):
      $cnt_cpext++;
      endif;

$cnt_cdata = ($cnt_cfs+$cnt_cf+$cnt_cnc+$cnt_cnd+$cnt_ccp+$cnt_cru+$cnt_cgm+$cnt_crs+$cnt_cgob+$cnt_cexp+$cnt_cpext);

$iva_val = StockData::getPrincipal()->imp_val;

if($iva_val>0): ?>

      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-list-ol"></i>
          <span class="badge badge-warning navbar-badge"> <?php echo $cnt_cdata;?>
 </span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right este" style="background-color: #333;">
          <span class="dropdown-item dropdown-header"> <?php echo $cnt_cdata;?>  
<?php 
switch (Core::$user->language){
 case 'ES': echo "Comprobantes DGII"; break;
 case 'EN': echo "DGII receipts"; break;
}
?></span>

         <!-- /////////////// CONSUMIDOR FINAL /////////////-->

<?php 
switch (Core::$user->language){
 case 'ES':  if($cf==0 ||  $cf<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            
            <?php if($cf==0){ echo "<span class='badge badge-danger'>No hay Final.</span>";}
                  else if($cf<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Final.</span>";} 
                  else if($cf<=10){ echo "<span class='badge badge-info'>Quedan 10 Final.</span>";} ?> 
          </a>
          <?php endif;?>
         <!-- //////////////// CREDITO FISCAL /////////// -->
                
                 <?php if($cfs==0 || $cfs<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cfs==0){ echo "<span class='badge badge-danger'>No hay Credito Fiscal.</span>";}
                  else if($cfs<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Credito Fiscal.</span>";} 
                  else if($cfs<=10){ echo "<span class='badge badge-info'>Quedan 10 Credito Fiscal.</span>";} ?> 
          </a>
          <?php endif;?>
          
          
            <!-- //////////////// NOTA DE CREDITO /////////// -->
                
                 <?php if($cn==0 || $cn<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cn==0){ echo "<span class='badge badge-danger'>No hay Notas de Credito.</span>";}
                  else if($cn<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Notas de Credito.</span>";} 
                  else if($cn<=10){ echo "<span class='badge badge-info'>Quedan 10 Notas de Credito.</span>";} ?> 
          </a>
          <?php endif;?>
          
          
            <!-- //////////////// NOTAS DE DEBITO /////////// -->
                
                 <?php if($cd==0 || $cd<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cd==0){ echo "<span class='badge badge-danger'>No hay Notas de Debito.</span>";}
                  else if($cd<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Notas de Debito.</span>";} 
                  else if($cd<=10){ echo "<span class='badge badge-info'>Quedan 10 Notas de Debito.</span>";} ?> 
          </a>
          <?php endif;?>
          
           <!-- //////////////// COMPRAS /////////// -->
                
                 <?php if($ccp==0 || $ccp<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($ccp==0){ echo "<span class='badge badge-danger'>No hay Compras.</span>";}
                  else if($ccp<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Compras.</span>";} 
                  else if($ccp<=10){ echo "<span class='badge badge-info'>Quedan 10 Compras.</span>";} ?> 
          </a>
          <?php endif;?>
          
            <!-- //////////////// REGISTRO UNICO DE INGRESOS /////////// -->
                
                 <?php if($cru==0 || $cru<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cru==0){ echo "<span class='badge badge-danger'>No hay Registro Unico de Ingresos.</span>";}
                  else if($cru<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Registro Unico de Ingresos.</span>";} 
                  else if($cru<=10){ echo "<span class='badge badge-info'>Quedan 10 Registro Unico de Ingresos.</span>";} ?> 
          </a>
          <?php endif;?>


      <!-- //////////////// GASTOS MENORES /////////// -->
                
                 <?php if($cgm==0 || $cgm<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cgm==0){ echo "<span class='badge badge-danger'>No hay Gastos Menores.</span>";}
                  else if($cgm<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Gastos Menores.</span>";} 
                  else if($cgm<=10){ echo "<span class='badge badge-info'>Quedan 10 Gastos Menores.</span>";} ?> 
          </a>
          <?php endif;?>
          

      <!-- //////////////// REGIMENES ESPECIALES /////////// -->
                
                 <?php if($crs==0 || $crs<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($crs==0){ echo "<span class='badge badge-danger'>No hay Regimenes Especiales.</span>";}
                  else if($crs<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Regimenes Especiales.</span>";} 
                  else if($crs<=10){ echo "<span class='badge badge-info'>Quedan 10 Regimenes Especiales.</span>";} ?> 
          </a>
          <?php endif;?>

 <!-- //////////////// GUBERNAMENTAL /////////// -->
                
                 <?php if($cgob==0 || $cgob<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cgob==0){ echo "<span class='badge badge-danger'>No hay Gubernamental.</span>";}
                  else if($cgob<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Gubernamental.</span>";} 
                  else if($cgob<=10){ echo "<span class='badge badge-info'>Quedan 10 Gubernamental.</span>";} ?> 
          </a>
          <?php endif;?>
          
          
          <!-- //////////////// EXPORTACIONES /////////// -->
                
                 <?php if($cexp==0 || $cexp<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cexp==0){ echo "<span class='badge badge-danger'>No hay Exportaciones.</span>";}
                  else if($cexp<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Exportaciones.</span>";} 
                  else if($cexp<=10){ echo "<span class='badge badge-info'>Quedan 10 Exportaciones.</span>";} ?> 
          </a>
          <?php endif;?>
          
          <!-- //////////////// PAGOS AL EXTERIOR /////////// -->
                
                 <?php if($cpext==0 || $cpext<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cpext==0){ echo "<span class='badge badge-danger'>No hay Pagos al Exterior.</span>";}
                  else if($cpext<=10/2){ echo "<span class='badge badge-warning'>Quedan Pocos Pagos al Exterior.</span>";} 
                  else if($cpext<=10){ echo "<span class='badge badge-info'>Quedan 10 Pagos al Exterior.</span>";} ?> 
          </a>
          <?php endif;
          

          
case 'EN': if($cf==0 ||  $cf<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            
            <?php if($cf==0){ echo "<span class='badge badge-danger'>There is no final consumer.</span>";}
                  else if($cf<=10/2){ echo "<span class='badge badge-warning'>Few left Final consumer.</span>";} 
                  else if($cf<=10){ echo "<span class='badge badge-info'>10 left Final consumer.</span>";} ?> 
          </a>
          <?php endif;?>
          
                   <!-- //////////////// CREDITO FISCAL /////////// -->
                
                 <?php if($cfs==0 || $cfs<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cfs==0){ echo "<span class='badge badge-danger'>There is no Tax Credit Proof.</span>";}
                  else if($cfs<=10/2){ echo "<span class='badge badge-warning'>Few tax credits left.</span>";} 
                  else if($cfs<=10){ echo "<span class='badge badge-info'>10 tax credits left.</span>";} ?> 
          </a>
          <?php endif;?>
          
          
            <!-- //////////////// NOTA DE CREDITO /////////// -->
                
                 <?php if($cn==0 || $cn<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cn==0){ echo "<span class='badge badge-danger'>There are no Credit Notes.</span>";}
                  else if($cn<=10/2){ echo "<span class='badge badge-warning'>Few Credit Notes Left.</span>";} 
                  else if($cn<=10){ echo "<span class='badge badge-info'>10 Credit Notes left.</span>";} ?> 
          </a>
          <?php endif;?>
          
          
            <!-- //////////////// NOTAS DE DEBITO /////////// -->
                
                 <?php if($cd==0 || $cd<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cd==0){ echo "<span class='badge badge-danger'>There are no Debit Notes.</span>";}
                  else if($cd<=10/2){ echo "<span class='badge badge-warning'>Few Debit Notes Left.</span>";} 
                  else if($cd<=10){ echo "<span class='badge badge-info'>10 Debit Notes left.</span>";} ?> 
          </a>
          <?php endif;?>
          
           <!-- //////////////// COMPRAS /////////// -->
                
                 <?php if($ccp==0 || $ccp<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($ccp==0){ echo "<span class='badge badge-danger'>There are no purchases.</span>";}
                  else if($ccp<=10/2){ echo "<span class='badge badge-warning'>Few Purchases Left.</span>";} 
                  else if($ccp<=10){ echo "<span class='badge badge-info'>10 Purchases left.</span>";} ?> 
          </a>
          <?php endif;?>
          
            <!-- //////////////// REGISTRO UNICO DE INGRESOS /////////// -->
                
                 <?php if($cru==0 || $cru<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cru==0){ echo "<span class='badge badge-danger'>There is no Single Record of Income.</span>";}
                  else if($cru<=10/2){ echo "<span class='badge badge-warning'>There are few Unique Income Records left.</span>";} 
                  else if($cru<=10){ echo "<span class='badge badge-info'>There are 10 Single Income Registry left.</span>";} ?> 
          </a>
          <?php endif;?>


      <!-- //////////////// GASTOS MENORES /////////// -->
                
                 <?php if($cgm==0 || $cgm<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cgm==0){ echo "<span class='badge badge-danger'>There are no Minor Expenses.</span>";}
                  else if($cgm<=10/2){ echo "<span class='badge badge-warning'>Few Minor Expenses Left.</span>";} 
                  else if($cgm<=10){ echo "<span class='badge badge-info'>10 Minor Expenses Left.</span>";} ?> 
          </a>
          <?php endif;?>
          

      <!-- //////////////// REGIMENES ESPECIALES /////////// -->
                
                 <?php if($crs==0 || $crs<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($crs==0){ echo "<span class='badge badge-danger'>There are no special regimes.</span>";}
                  else if($crs<=10/2){ echo "<span class='badge badge-warning'>Few Special Regimes Left.</span>";} 
                  else if($crs<=10){ echo "<span class='badge badge-info'>10 Special Regimes left.</span>";} ?> 
          </a>
          <?php endif;?>

 <!-- //////////////// GUBERNAMENTAL /////////// -->
                
                 <?php if($cgob==0 || $cgob<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cgob==0){ echo "<span class='badge badge-danger'>There is no Government.</span>";}
                  else if($cgob<=10/2){ echo "<span class='badge badge-warning'>Few Left Government.</span>";} 
                  else if($cgob<=10){ echo "<span class='badge badge-info'>10 Government left.</span>";} ?> 
          </a>
          <?php endif;?>
          
          
          <!-- //////////////// EXPORTACIONES /////////// -->
                
                 <?php if($cexp==0 || $cexp<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cexp==0){ echo "<span class='badge badge-danger'>There are no exports.</span>";}
                  else if($cexp<=10/2){ echo "<span class='badge badge-warning'>Few Exports Left.</span>";} 
                  else if($cexp<=10){ echo "<span class='badge badge-info'>10 Exports left.</span>";} ?> 
          </a>
          <?php endif;?>
          
          <!-- //////////////// PAGOS AL EXTERIOR /////////// -->
                
                 <?php if($cpext==0 || $cpext<=10):?>

           <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-print mr-2"></i>
            <?php if($cpext==0){ echo "<span class='badge badge-danger'>There are no payments abroad.</span>";}
                  else if($cpext<=10/2){ echo "<span class='badge badge-warning'>There are few payments left abroad.</span>";} 
                  else if($cpext<=10){ echo "<span class='badge badge-info'>There are 10 payments left abroad.</span>";} ?> 
          </a>
          <?php endif;
          

}
?>

          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">
<?php 
switch (Core::$user->language){
 case 'ES': echo "Tienes ". $cnt_cdata. " Comprobantes Nuevo"; break;
 case 'EN': echo "Have ". $cnt_cdata. " New receipts"; break;
}
?></a>
        </div>
      </li>
<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->       

<?php endif;?>
<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->       

  


    </ul>
  </nav>
  <!-- /.navbar -->
  <!-- /.navbar -->
   <!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #222;">
    <!-- Brand Logo -->
     <center>
    <a  class="brand-link" style="background-color: #333;">
      <span id="texto"  class="brand-text texto-ajustable"><?php echo strtoupper( StockData::getPrincipal()->name);?></span>
    </a>
    
    <script>
  
  function ajustarTamanoTexto() {
  const elemento = document.getElementById("texto");
  const maxTamano = 8; // Tamaño máximo de la fuente en px
  const minTamano = 15; // Tamaño mínimo
  const baseCaracteres = 20; // Cantidad base antes de reducir
  let texto = elemento.textContent.length;
  
  let nuevoTamano = Math.max(minTamano, maxTamano - (texto - baseCaracteres));
  elemento.style.fontSize = nuevoTamano + "px";
}

ajustarTamanoTexto(); // Llamar al inicio</script>
</center>
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
                 
<?php 
$lang = Core::$user->language;
$onlineText = $lang == 'EN' ? 'Online' : 'En Línea';
$offlineText = $lang == 'EN' ? 'Offline' : 'Sin Conexión';
?>

<span style="display: inline-flex; align-items: center;">
  <span id="estado-circulo" style="
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: green;
    display: inline-block;
    margin-right: 8px;
  "></span>
  <span id="estado-texto"><?php echo $onlineText; ?></span>
</span>
<script>
let estadoAnterior = navigator.onLine; // Estado inicial

function actualizarEstadoConexion() {
  const dot = document.getElementById("estado-circulo");
  const text = document.getElementById("estado-texto");
  const conectado = navigator.onLine;

  if (conectado) {
    dot.style.backgroundColor = "green";
    text.textContent = "<?php echo $onlineText; ?>";
    if (!estadoAnterior) {
      toastr.success("<?php echo $onlineText; ?>");
    }
  } else {
    dot.style.backgroundColor = "red";
    text.textContent = "<?php echo $offlineText; ?>";
    if (estadoAnterior) {
      toastr.error("<?php echo $offlineText; ?>");
    }
  }

  estadoAnterior = conectado; // Actualizar estado
}

// Inicializar
actualizarEstadoConexion();

// Escuchar cambios de red
window.addEventListener('online', actualizarEstadoConexion);
window.addEventListener('offline', actualizarEstadoConexion);
</script>


          </a>
      
        </div></a>

      </div>



      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <?php if(StockData::getPrincipal()->update=="1"): ?>


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
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
              </p>
            </a>
          </li>
          
          
          <li class="nav-item">
            <a href="./?view=alert"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="alert")){ echo "active"; }?>">
              <i class="nav-icon fa fa-exclamation-circle"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo "ALERTAS"; break;
 case 'EN': echo "ALERTS"; break;
}
?>


              </p>
              
            </a>
          </li>
          
 <?php
$pending_web_count = 0;

$pending_web = BookingData::getAllBySQL("
where type='2'
and status=0
and (firma='' OR firma IS NULL)
and stock_id=".StockData::getPrincipal()->id
);

$pending_web_count = count($pending_web);
?>         
              
<li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="web")){ echo "menu-open"; }?>">
    <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="web")){ echo "active"; }?>">

        <i class="nav-icon fa fa-globe"></i>

        <p>
            <?php 
            switch (Core::$user->language){
                case 'ES': echo "PAGINA WEB"; break;
                case 'EN': echo "WEB SITE"; break;
            }
            ?>

            <?php if($pending_web_count > 0): ?>
                <span class="badge badge-danger right">
                    <?php echo $pending_web_count; ?>
                </span>
            <?php endif; ?>

            <i class="right fas fa-angle-left"></i>
        </p>

    </a>

    <ul class="nav nav-treeview">

        <li class="nav-item">
            <a href="./?view=web&opt=all" 
               class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="web" && isset($_GET["opt"]) && $_GET["opt"]=="all")){ echo "active"; }?>" 
               style="color:gray;">

                <i class="far fa-circle nav-icon"></i>

                <p>
                    PENDIENTES

                    <?php if($pending_web_count > 0): ?>
                        <span class="badge badge-warning right">
                            <?php echo $pending_web_count; ?>
                        </span>
                    <?php endif; ?>
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http'); ?>://<?php echo $_SERVER['HTTP_HOST']; ?>/WEB/" 
               class="nav-link" 
               style="color:gray;">
                <i class="far fa-circle nav-icon"></i>
                <p>
                    <?php 
                    switch (Core::$user->language){
                        case 'ES': echo "VER PAGINA"; break;
                        case 'EN': echo "SEE PAGE"; break;
                    }
                    ?>
                </p>
            </a>
        </li>

    </ul>
</li>

          
           <li class="nav-item">
            <a href="./?view=calendar" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="calendar")){ echo "active"; }?>">
             <i class="fa fa-calendar nav-icon"></i>
              <p style="font-size:15px">
<?php ////////////////////////////////////////////////////////////////////////////////  CALENDARIO.  ////////////////////////////////////////////////////////////
switch (Core::$user->language){
 case 'ES': echo "CALENDARIO"; break;
 case 'EN': echo "CALENDAR"; break;
}
?>

              </p>
            </a>
          </li>
          
                 
        
<li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="raffle" || $_GET["view"]=="raffle_ticket")){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="raffle"  || $_GET["view"]=="raffle_ticket")){ echo "active"; }?>">

        <i class="nav-icon fa fa-ticket-alt"></i>

        <p>
            SORTEOS
            <i class="right fas fa-angle-left"></i>
        </p>

    </a>

    <ul class="nav nav-treeview">

        <li class="nav-item">
            <a href="./?view=raffle&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="raffle" && $_GET["opt"]=="all")){ echo "active"; }?>" style="color:gray;">
                <i class="far fa-circle nav-icon"></i>
                <p>SORTEOS</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="./?view=raffle&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="raffle" && $_GET["opt"]=="new")){ echo "active"; }?>" style="color:gray;">
                <i class="far fa-circle nav-icon"></i>
                <p>NUEVO SORTEO</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="./?view=raffle_ticket&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="raffle_ticket" && $_GET["opt"]=="all")){ echo "active"; }?>" style="color:gray;">
                <i class="far fa-circle nav-icon"></i>
                <p>GANADORES</p>
            </a>
        </li>

    </ul>

</li>

          
          
           <li class="nav-item">
            <a href="./?view=tasks&opt=all"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="tasks")){ echo "active"; }?>">
               <i class="nav-icon fa fa-check-square"></i>
    <p>TAREAS 
</p>
              
            </a>
          </li>
          
          
             <li class="nav-item">
            <a href="./?view=predictive"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="predictive")){ echo "active"; }?>">
               <i class="nav-icon fa fa-tint"></i>
    <p>MANTENIMIENTOS 
</p>
              
            </a>
          </li>
          
          
          <li class="nav-item">
  <a href="./?view=simulator" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="simulator")){ echo "active"; }?>">
    
    <i class="nav-icon fa fa-chart-line"></i>

    <p>SIMULADOR FINANCIERO</p>

  </a>
</li>
          <li class="nav-item">
  <a href="./?view=performance" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="performance")){ echo "active"; }?>">
    <i class="nav-icon fa fa-trophy"></i>
    <p>RENDIMIENTO
    
    </p>
  </a>
</li>
  
<?php if(StockData::getPrincipal()->update=="1"): ?>      
             <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="gps")){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="gps")){ echo "active"; }?>">
                  <i class="fa fa-map-marker nav-icon"></i>
                  <p>   
<?php 
switch (Core::$user->language){
 case 'ES': echo "GPS / RASTREO"; break;
 case 'EN': echo "GPS / TRACKING"; break;
}
?></p>
                   <i class="fas fa-angle-left right"></i>
                </a>
                  <ul class="nav nav-treeview">
             
                <li class="nav-item">
                <a href="./?view=gps&opt=map" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="gps" && $_GET["opt"]=="map")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "MAPA"; break;
 case 'EN': echo "MAP"; break;
}
?></p>
                </a>
              </li>
              
              
                <li class="nav-item">
                <a href="./?view=gps&opt=risks" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="gps" && $_GET["opt"]=="risks")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "RIESGOS"; break;
 case 'EN': echo "RISKS"; break;
}
?></p>
                </a>
              </li>
              
               <li class="nav-item">
                <a href="./?view=gps&opt=add" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="gps" && $_GET["opt"]=="add")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "AGREGAR GPS"; break;
 case 'EN': echo "ADD GPS"; break;
}
?></p>
                </a>
              </li>
              
              
              <li class="nav-item">
                <a href="./?view=gps&opt=assign" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="gps" && $_GET["opt"]=="assign")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "ASIGNAR DISPOSITIVO"; break;
 case 'EN': echo "ASSIGN DEVICE"; break;
}
?></p>
                </a>
              </li>
           
              
              
            </ul>
              </li>
           <?php  endif;?>
           
           <li class="nav-item">
            <a href="./?view=meter"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="meter")){ echo "active"; }?>">
              <i class="nav-icon fa fa-tachometer"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo "SALUD DEL NEGOCIO"; break;
 case 'EN': echo "BUSINESS HEALTH"; break;
}
?>
              
              </p>
            </a>
          </li>
         <li class="nav-item">
            <a href="./?view=decisions"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="decisions")){ echo "active"; }?>">
             <i class="nav-icon fa fa-brain"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo "DECISIONES"; break;
 case 'EN': echo "DECISIONS"; break;
}
?>


              </p>
            </a>
          </li>
           
           <?php endif;?>
           
            <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && ($_GET["opt"]=="delivery" || $_GET["opt"]=="all" || $_GET["opt"]=="edit" || $_GET["opt"]=="new" || $_GET["opt"]=="running" || $_GET["opt"]=="cluster" || $_GET["opt"]=="confirmation"|| $_GET["opt"]=="earring") || ($_GET["view"]=="available") || ($_GET["view"]=="contract" && ($_GET["opt"]=="finished" ||$_GET["opt"]=="new" || $_GET["opt"]=="newfree" || $_GET["opt"]=="newhours")))){ echo "menu-open"; }?>">
                
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && ($_GET["opt"]=="delivery" || $_GET["opt"]=="all" || $_GET["opt"]=="edit"  || $_GET["opt"]=="new" || $_GET["opt"]=="running" || $_GET["opt"]=="cluster" || $_GET["opt"]=="confirmation"|| $_GET["opt"]=="earring") || ($_GET["view"]=="available") || ($_GET["view"]=="contract" && ($_GET["opt"]=="finished" || $_GET["opt"]=="new" || $_GET["opt"]=="newfree" || $_GET["opt"]=="newhours")))){ echo "active"; }?>">
              <i class="nav-icon fas fa-car"></i>
              <p>
                OPERACIONES
              <?php  if($wt_tot>0 || $conf_tot>0):?>
               <span class="right badge badge-warning"><?php echo $conf_tot+$wt_tot;?></span> 
<?php endif;?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                       
<?php
////////////////////////////////////////////////////////////////////////////////  DISPONIBILIDAD. ////////////////////////////////////////////////////////////////////////////////
 if (UserPermissionsData::getAllBySQL("where permits_id=12 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(12); ?> 
             <li class="nav-item">
            <a href="./?view=available&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="available")){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p>DISPONIBILIDAD </p>
            </a>
          </li>
 

<?php endif;   


if(StockData::getPrincipal()->method=="1"):
////////////////////////////////////////////////////////////////////////////////  RESERVACIONES. ////////////////////////////////////////////////////////////////////////////////
if (UserPermissionsData::getAllBySQL("where permits_id=9 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(9);  ?>


 <li class="nav-item">
            <a href="./?view=booking&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && ($_GET["opt"]=="new" || $_GET["opt"]=="cluster" || $_GET["opt"]=="confirmation" || $_GET["opt"]=="earring"))){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p><?php 
switch (Core::$user->language){
 case 'ES': echo "RESERVACIONES"; break;
 case 'EN': echo "RESERVATIONS"; break;
}

?> </p>
            </a>
          </li>
          

             <li class="nav-item">
            <a href="./?view=booking&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && ($_GET["opt"]=="all" || $_GET["opt"]=="delivery" || $_GET["opt"]=="edit" ))){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p>ENTREGAR VEHICULO </p>
              
             <?php if($conf_tot>0):?>
               <span class="right badge badge-warning"><?php echo $conf_tot;?></span> 
            <?php endif;?>
            </a>
          </li>
          
           
<?php endif; endif;
////////////////////////////////////////////////////////////////////////////////  RENTAR HOY. ////////////////////////////////////////////////////////////////////////////////

 if(UserPermissionsData::getAllBySQL("where permits_id=17 and user_id=".$_SESSION["user_id"])):?>
 
 <li class="nav-item">
            <a href="./?view=contract&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="new" || $_GET["opt"]=="newfree" || $_GET["opt"]=="newhours"))){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p><?php 
switch (Core::$user->language){
 case 'ES': echo "RENTAR HOY"; break;
 case 'EN': echo "RENT TODAY"; break;
}

?> </p>
            </a>
          </li>
 <?php endif; ?>

 
          <li class="nav-item">
             <a href="./?view=contract&opt=finished" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="finished"))){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p><?php 
switch (Core::$user->language){
 case 'ES': echo "RENTAR COMPLETADAS"; break;
 case 'EN': echo "RENT COMPLETED"; break;
}

?> </p>
            </a>
          </li>
     <?php  ////////////////////////////////////////////////////////////////////////////////  TERMINO DEL MODULO.  //////////////////////////////////////////////////////////// ?>
     
            </ul>
          </li>
          
          
<?php   ////////////////////////////////////////////////////////////////////////////////  VEHICULOS RENTADOS. /////////////////////////////////////////////////////////////////
 
if (UserPermissionsData::getAllBySQL("where permits_id=3 and user_id=".$_SESSION["user_id"])): ?>

           <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="free" || $_GET["opt"]=="running" || $_GET["opt"]=="replace" || $_GET["opt"]=="replacefree"|| $_GET["opt"]=="extend" || $_GET["opt"]=="extendfree" || $_GET["opt"]=="random" || $_GET["opt"]=="extenddate" || $_GET["opt"]=="received" || $_GET["opt"]=="billfree" || $_GET["opt"]=="bill" || $_GET["opt"]=="edit" || $_GET["opt"]=="modal"))){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"])  && ($_GET["view"]=="contract" && ($_GET["opt"]=="free" || $_GET["opt"]=="running" || $_GET["opt"]=="replace" || $_GET["opt"]=="replacefree"|| $_GET["opt"]=="extend" || $_GET["opt"]=="extendfree" || $_GET["opt"]=="random" || $_GET["opt"]=="extenddate" || $_GET["opt"]=="received" || $_GET["opt"]=="billfree" || $_GET["opt"]=="bill" || $_GET["opt"]=="edit" || $_GET["opt"]=="modal"))){ echo "active"; }?>"><i class="fa fa-clipboard-list nav-icon"></i>
              <p style="font-size:15px">
              
<?php 
switch (Core::$user->language){
 case 'ES': echo "GESTION DE RENTAS"; break;
 case 'EN': echo "RENT MANAGEMENT"; break;
}
?>
               
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
 
<?php if(StockData::getPrincipal()->method=="1"):?>             
                <li class="nav-item">
             <a href="./?view=contract&opt=running" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="running" || $_GET["opt"]=="free" || $_GET["opt"]=="received" || $_GET["opt"]=="edit" || $_GET["opt"]=="modal"))){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p><?php 
switch (Core::$user->language){
case 'ES': echo "RECIBIR VEHICULO"; break;
 case 'EN': echo "RECEIVE VEHICLE"; break;
}

?> </p>
            </a>
          </li>  
                       
  <?php else:?>
  
   <li class="nav-item">
             <a href="./?view=contract&opt=free" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="running" || $_GET["opt"]=="free"))){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p><?php 
switch (Core::$user->language){
case 'ES': echo "RECIBIR VEHICULO"; break;
 case 'EN': echo "RECEIVE VEHICLE"; break;
}

?> </p>
            </a>
          </li>  
  
  
  <?php endif;?>  

              
              
   <li class="nav-item">
             <a href="./?view=contract&opt=replace" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="replace" || $_GET["opt"]=="replacefree" || $_GET["opt"]=="random"))){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p><?php 
switch (Core::$user->language){
case 'ES': echo "REEMPLAZAR VEHICULO"; break;
 case 'EN': echo "REPLACE VEHICLE"; break;
}

?> </p>
            </a>
          </li>  
          

<?php if(StockData::getPrincipal()->method=="1"):?>            
              <li class="nav-item">
                <a href="./?view=contract&opt=extend" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="extend" || $_GET["opt"]=="extendfree" || $_GET["opt"]=="extenddate"))){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "EXTENDER FECHA"; break;
 case 'EN': echo "EXTEND DATE"; break;
}
?></p>
                </a>
              </li>
 <?php endif;?>              

  
  
  <?php if(StockData::getPrincipal()->method=="2"):?> 
              <li class="nav-item">
                <a href="./?view=contract&opt=extendfree" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="extendfree" || $_GET["opt"]=="extenddate"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "RENOVAR CONTRATO"; break;
 case 'EN': echo "RENEW CONTRACT"; break;
}
?></p>
                </a>
              </li>
              
  <?php endif;?> 
  
 
 
 <?php if(StockData::getPrincipal()->method=="1"):?>             
                <li class="nav-item">
             <a href="./?view=contract&opt=bill" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="bill" || $_GET["opt"]=="billfree"))){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p><?php 
switch (Core::$user->language){
 case 'ES': echo "FACTURA / CONTRATO"; break;
 case 'EN': echo "INVOICE / CONTRACT"; break;
}

?> </p>
            </a>
          </li>  
                       
  <?php else:?>
  
   <li class="nav-item">
             <a href="./?view=contract&opt=billfree" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="billfree" || $_GET["opt"]=="bill"))){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p><?php 
switch (Core::$user->language){
 case 'ES': echo "FACTURA / CONTRATO"; break;
 case 'EN': echo "INVOICE / CONTRACT"; break;
}

?> </p>
            </a>
          </li>  
  
  
  <?php endif;?> 

               
            </ul>
          </li>
 <?php endif;?>

<?php 
$cotizaciones = CotizationData::getAllbySQL("where stock_id=".StockData::getPrincipal()->id); 
$cotCount = count($cotizaciones);
?>


 <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars" || $_GET["view"]=="crashes" || $_GET["view"]=="incidents" || $_GET["view"]=="key" || $_GET["view"]=="cotization")){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"])  && ($_GET["view"]=="cars" || $_GET["view"]=="crashes" || $_GET["view"]=="incidents" || $_GET["view"]=="key"  || $_GET["view"]=="cotization")){ echo "active"; }?>">
              <i class="nav-icon fas fa-box"></i>
              <p>
                INVENTARIO
                
                
                 <i class="right fas fa-angle-left"></i>
                 
                 <?php if($cotCount > 0): ?>
        <span class="right badge badge-warning"><?php echo $cotCount; ?></span>
      <?php else:?> <?php endif; ?>
                 
                 
              </p>
            </a>
            <ul class="nav nav-treeview">
                
<?php if (UserPermissionsData::getAllBySQL("where permits_id=6 and user_id=".$_SESSION["user_id"])):
////////////////////////////////////////////////////////////////////////////////  VEHICULOS.  //////////////////////////////////////////////////////////////////////////////// 
?>

 <li class="nav-item">
            <a href="./?view=incidents" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="incidents")){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p>INCIDENCIAS </p>
            </a>
          </li>
          
           <li class="nav-item">
            <a href="./?view=key&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="key")){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p>CONTROL LLAVES </p>
            </a>
          </li>
          
          
           <li class="nav-item">
            <a href="./?view=cars&opt=available" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars")){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p><?php 
switch (Core::$user->language){
 case 'ES': echo "VEHICULOS"; break;
 case 'EN': echo "VEHICLES"; break;
}
?> </p>
            </a>
          </li>

     
    <?php endif;
     ////////////////////////////////////////////////////////////////////////////////  COTIZACIONES.  //////////////////////////////////////////////////////////////////////////////// 
     
      if (UserPermissionsData::getAllBySQL("where permits_id=10 and user_id=".$_SESSION["user_id"])):?>


<li class="nav-item">
            <a href="./?view=cotization&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cotization")){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p> <?php 
switch (Core::$user->language){
 case 'ES': echo "COTIZACION"; break;
 case 'EN': echo "PRICE"; break;
}
?> </p>


<?php if($cotCount > 0): ?>
        <span class="right badge badge-warning"><?php echo $cotCount; ?></span>
      <?php endif; ?>
 
            </a>
          </li>
                   
   
<?php endif; ////////////////////////////////////////////////////////////////////////////////  TERMINO DEL MODULO.  //////////////////////////////////////////////////////////// ?>
     
            </ul>
          </li>
          
          
           <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="cogs" || $_GET["view"]=="inventory" || ($_GET["view"]=="person" && $_GET["opt"]=="providers") || $_GET["view"]=="sold")){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"])  && ($_GET["view"]=="cogs" || $_GET["view"]=="inventory" || ($_GET["view"]=="person" && $_GET["opt"]=="providers") || $_GET["view"]=="sold")){ echo "active"; }?>">
              <i class="nav-icon fas fa-briefcase"></i>
              <p>
                SERVICIOS Y VENTAS
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                

      
    <?php if (UserPermissionsData::getAllBySQL("where permits_id=8 and user_id=".$_SESSION["user_id"])): 
 ////////////////////////////////////////////////////////////////////////////////  PIEZAS.  //////////////////////////////////////////////////////////////////////////////// ?>
 
 
  <li class="nav-item">
                <a href="./?view=inventory&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="inventory" || ($_GET["view"]=="person" && $_GET["opt"]=="providers"))){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
  case 'ES': echo "PIEZAS DE REPUESTO"; break;
 case 'EN': echo "SPARE PARTS"; break;
}
?></p>
                </a>
              </li>
              
              
              
               <li class="nav-item">
                <a href="./?view=cogs&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cogs")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "EN TALLER"; break;
 case 'EN': echo "IN WORKSHOP"; break;
}
?></p>
                </a>
              </li>
              
              
              
               <li class="nav-item">
                <a href="./?view=sold&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="sold")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "VENDIDOS"; break;
 case 'EN': echo "SOLD"; break;
}
?></p>
                </a>
              </li>
    
        
   
<?php endif; ////////////////////////////////////////////////////////////////////////////////  TERMINO DEL MODULO.  //////////////////////////////////////////////////////////// ?>
     
            </ul>
          </li>
          
<?php  if (UserPermissionsData::getAllBySQL("where permits_id=7 and user_id=".$_SESSION["user_id"])): ?>           
        <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="messages" || $_GET["view"]=="reminder")){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"])  && ($_GET["view"]=="messages" || $_GET["view"]=="reminder")){ echo "active"; }?>">
              <i class="nav-icon fas fa-calendar"></i>
              <p>
                SEGUIMIENTO
                
        <?php         
      // ============================
      // Contador dinámico entregas
      // ============================
      $base = new Database();
      $con = $base->connect();

      $hoy = date('Y-m-d H:i:s');
      $manana = date('Y-m-d H:i:s', strtotime('+1 day'));

      // Contar entregas
      $sql_count1 = "SELECT COUNT(*) AS total 
                     FROM booking 
                     WHERE status<>3 
                       AND start_at BETWEEN '$hoy' AND '$manana'";
      $count1 = $con->query($sql_count1)->fetch_assoc()['total'];

      // Contar devoluciones
      $sql_count2 = "SELECT COUNT(*) AS total 
                     FROM booking 
                     WHERE status<>3 
                       AND end_at BETWEEN '$hoy' AND '$manana'";
      $count2 = $con->query($sql_count2)->fetch_assoc()['total'];

      $total_registros = $count1 + $count2;

      // Mostrar badge si hay registros
      if($total_registros > 0){
        echo " <span class='right badge badge-danger'>$total_registros</span>";
      }
      ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
  <a href="./?view=messages&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="messages")){ echo "active"; }?>" style="color:gray;">
    <i class="far fa-circle nav-icon"></i>
    <p>
<?php ////////////////////////////////////////////////////////////////////////////////  ENTREGAS.  ////////////////////////////////////////////////////////////
switch (Core::$user->language){
 case 'ES': echo "ENTREGAS"; break;
 case 'EN': echo "DELIVERIES"; break;
}

// ============================
      // Contador dinámico entregas
      // ============================
      $base = new Database();
      $con = $base->connect();

      $hoy = date('Y-m-d H:i:s');
      $manana = date('Y-m-d H:i:s', strtotime('+1 day'));

      // Contar entregas
      $sql_count1 = "SELECT COUNT(*) AS total 
                     FROM booking 
                     WHERE status<>3 
                       AND start_at BETWEEN '$hoy' AND '$manana'";
      $count1 = $con->query($sql_count1)->fetch_assoc()['total'];

      // Contar devoluciones
      $sql_count2 = "SELECT COUNT(*) AS total 
                     FROM booking 
                     WHERE status<>3 
                       AND end_at BETWEEN '$hoy' AND '$manana'";
      $count2 = $con->query($sql_count2)->fetch_assoc()['total'];

      $total_registros = $count1 + $count2;

      // Mostrar badge si hay registros
      if($total_registros > 0){
        echo " <span class='right badge badge-danger'>$total_registros</span>";
      }
?>
    </p>
  </a>
</li>

            
         
          
            
              <li class="nav-item">
            <a href="./?view=reminder" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="reminder")){ echo "active"; }?>" style="color:gray;">
              <i class="far fa-circle nav-icon"></i>
              <p style="font-size:15px">
<?php ////////////////////////////////////////////////////////////////////////////////  RECORDATORIO.  ////////////////////////////////////////////////////////////
switch (Core::$user->language){
 case 'ES': echo "RECORDATORIO"; break;
 case 'EN': echo "REMINDER"; break;
}
?>
             
<?php if ($kq_tot>0):?>
               <span class="right badge badge-danger"><?php echo $kq_tot;?></span>
                <?php endif; ?>
              
               
              </p>
            </a>
          </li>

            </ul>
          </li>
    
<?php endif;  ////////////////////////////////////////////////////////////////////////////////  TERMINO DEL MODULO.  //////////////////////////////////////////////////////////// ?>     

  <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="data" || $_GET["view"]=="maps" || $_GET["view"]=="client360" || $_GET["view"]=="person")){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"])  && ($_GET["view"]=="data" || $_GET["view"]=="maps" || $_GET["view"]=="client360" || $_GET["view"]=="person" )){ echo "active"; }?>">
              <i class="nav-icon fas fa-users"></i>
              <p>
               CLIENTES
               
                <i class="right fas fa-angle-left"></i>
                
              </p>
            </a>
            <ul class="nav nav-treeview">
                
<?php  if (UserPermissionsData::getAllBySQL("where permits_id=2 and user_id=".$_SESSION["user_id"])):?>

<li class="nav-item">
            <a  href="./?view=person&opt=new"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="person" && $_GET["opt"]=="new")){ echo "active"; }?>" style="color:gray;">
              <i class="far fa-circle nav-icon"></i>
              <p style="font-size:15px">
        
<?php  ////////////////////////////////////////////////////////////////////////////////  DATA CREDITO. ////////////////////////////////////////////////////////////
switch (Core::$user->language){
 case 'ES': echo "CREAR NUEVO"; break;
 case 'EN': echo "CREATE NEW"; break;
}
?>
              </p>
            </a>
          </li>

 <li class="nav-item">
            <a  href="./?view=person&opt=all"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="person" && $_GET["opt"]=="all")){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p style="font-size:15px">
        
<?php  ////////////////////////////////////////////////////////////////////////////////  DATA CREDITO. ////////////////////////////////////////////////////////////
switch (Core::$user->language){
 case 'ES': echo "LISTADO"; break;
 case 'EN': echo "LIST"; break;
}
?>
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a  href="./?view=data&opt=all"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="data")){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p style="font-size:15px">
        
<?php  ////////////////////////////////////////////////////////////////////////////////  DATA CREDITO. ////////////////////////////////////////////////////////////
switch (Core::$user->language){
 case 'ES': echo "DATA CREDITO"; break;
 case 'EN': echo "CREDIT DATA"; break;
}
?>
              </p>
            </a>
          </li>


            <li class="nav-item">
            <a href="./?view=maps" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="maps")){ echo "active"; }?>" style="color:gray;">
              <i class="far fa-circle nav-icon"></i>
              <p style="font-size:15px">
<?php ////////////////////////////////////////////////////////////////////////////////  MAPA DEL CLIENTE. ////////////////////////////////////////////////////////////
switch (Core::$user->language){
 case 'ES': echo "MAPA DEL CLIENTE"; break;
 case 'EN': echo "CUSTOMER MAP"; break;
}
?>
             
              </p>
            </a>
          </li>
          
          
           <li class="nav-item">
            <a href="./?view=client360" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="client360")){ echo "active"; }?>" style="color:gray;">
              <i class="far fa-circle nav-icon"></i>
              <p style="font-size:15px">
 HISTORIAL
      
              </p>
            </a>
          </li>
          
          
              
            </ul>
          </li>


<?php endif; ?>
     
<?php ////////////////////////////////////////////////////////////////////////////////  FINANZAS. //////////////////////////////////////////////////////////// 

if (UserPermissionsData::getAllBySQL("where permits_id=13 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(13);  ?>
       <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="flotilla"||$_GET["view"]=="maintenance"||$_GET["view"]=="balance"||$_GET["view"]=="finance"||$_GET["view"]=="receipt"||$_GET["view"]=="boxhistory"||$_GET["view"]=="box"||$_GET["view"]=="spendtype"||$_GET["view"]=="spendtypeisr"||$_GET["view"]=="b"||$_GET["view"]=="credit"||$_GET["view"]=="make"||$_GET["view"]=="payroll"||$_GET["view"]=="banks"||$_GET["view"]=="bank_transactions"||$_GET["view"]=="bank_accounts"||$_GET["view"]=="bank_checks"||$_GET["view"]=="income"||$_GET["view"]=="bills")){ echo "menu-open"; }?>">
           
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="flotilla"||$_GET["view"]=="maintenance"||$_GET["view"]=="balance"||$_GET["view"]=="finance"||$_GET["view"]=="receipt"||$_GET["view"]=="boxhistory"||$_GET["view"]=="box"||$_GET["view"]=="spendtype"||$_GET["view"]=="spendtypeisr"||$_GET["view"]=="b"||$_GET["view"]=="credit"||$_GET["view"]=="make"||$_GET["view"]=="banks"||$_GET["view"]=="bank_transactions"||$_GET["view"]=="bank_accounts"||$_GET["view"]=="bank_checks"||$_GET["view"]=="income"||$_GET["view"]=="bills")){ echo "active"; }?>">
              <i class="nav-icon fa fa-briefcase"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
                <i class="fas fa-angle-left right"></i>
                
              </p>
            </a>
            <ul class="nav nav-treeview">
<?php 
if(UserPermissionsData::getAllBySQL("where permits_id=18 and user_id=".$_SESSION["user_id"])):?>
              <li class="nav-item">
                <a href="./?view=balance" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="balance")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>BALANCE</p>
                </a>
              </li>
         
    
      <li class="nav-item">
      <a href="./?view=income&opt=daily" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="income" && $_GET["opt"]=="daily")){ echo "active"; }?>" style="color:gray;">
       <i class="far fa-circle nav-icon"></i>
        <p>INGRESOS DIARIOS</p>
         
      </a>
    </li>
    
    <li class="nav-item">
      <a href="./?view=income&opt=monthly" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="income" && $_GET["opt"]=="monthly")){ echo "active"; }?>" style="color:gray;">
        <i class="far fa-circle nav-icon"></i>
        <p>INGRESOS MENSUALES</p>
         
      </a>
    </li>
    
      <li class="nav-item">
      <a href="./?view=bills&opt=daily" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="bills" && $_GET["opt"]=="daily")){ echo "active"; }?>" style="color:gray;">
       <i class="far fa-circle nav-icon"></i>
        <p>GASTOS DIARIOS</p>
         
      </a>
    </li>
    
    <li class="nav-item">
      <a href="./?view=bills&opt=monthly" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="bills" && $_GET["opt"]=="monthly")){ echo "active"; }?>" style="color:gray;">
        <i class="far fa-circle nav-icon"></i>
        <p>GASTOS MENSUALES</p>
         
      </a>
    </li>
    
    
              
               <li class="nav-item">
                <a href="./?view=banks&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="banks")){ echo "active"; }?>" style="color:gray;">
                <i class="far fa-circle nav-icon"></i>
                <p>BANCOS</p>
              
                </a>
            </li>
            
            <li class="nav-item">
  <a href="./?view=bank_accounts&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="bank_accounts")){ echo "active"; }?>" style="color:gray;">
    <i class="far fa-circle nav-icon"></i>
    <p>CUENTAS BANCARIAS</p>
     
  </a>
</li>

<li class="nav-item">
  <a href="./?view=bank_transactions&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="bank_transactions")){ echo "active"; }?>" style="color:gray;">
    <i class="far fa-circle nav-icon"></i>
    <p>MOVIMIENTOS BANCARIOS</p>
   
  </a>
</li>
      
  <li class="nav-item">
  <a href="./?view=bank_reconciliations&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="bank_reconciliations")){ echo "active"; }?>" style="color:gray;">
    <i class="far fa-circle nav-icon"></i>
    <p>CONCILIACIONES</p>
    
  </a>
</li>
              
                 <li class="nav-item">
                <a href="./?view=credit&opt=stock" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="credit" && $_GET["opt"]=="stock")){ echo "active"; }?>"  style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>PAGO SUPLIDORES</p>
                </a>
              </li>
              
   
<?php endif; 
if(UserPermissionsData::getAllBySQL("where permits_id=19 and user_id=".$_SESSION["user_id"])):?>
                 <li class="nav-item">
                <a href="./?view=credit&opt=pay" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="credit" && $_GET["opt"]=="pay")){ echo "active"; }?>"  style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p><?php 
switch (Core::$user->language){
  case 'ES': echo "DEUDA CLIENTE"; break;
 case 'EN': echo "CUSTOMER DEBT"; break;
}
?></p>
                </a>
              </li>
              
              
              <li class="nav-item">
                <a href="./?view=payroll&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="payroll")){ echo "active"; }?>"  style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "NOMINA EMPLEADO"; break;
 case 'EN': echo "EMPLOYEE PAYROLL"; break;
}
?></p>
                </a>
              </li>
<?php endif;
if(UserPermissionsData::getAllBySQL("where permits_id=21 and user_id=".$_SESSION["user_id"])):?>
              <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance")){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance")){ echo "active"; }?>"  style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "GASTOS"; break;
 case 'EN': echo "BILLS"; break;
}
?></p>
                   <i class="fas fa-angle-left right"></i>
                </a>
                  <ul class="nav nav-treeview">
              
               <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance" && ($_GET["spends"]=="negocio" || $_GET["spends"]=="Negocio") )){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance" && ($_GET["spends"]=="negocio" || $_GET["spends"]=="Negocio") )){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Local</p>
                   <i class="fas fa-angle-left right"></i>
                </a>
                 <ul class="nav nav-treeview">
             
                <li class="nav-item">
                <a href="./?view=finance&opt=new&spends=negocio" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance" && $_GET["opt"]=="new" && $_GET["spends"]=="negocio")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Crear Nuevo"; break;
 case 'EN': echo "Create New"; break;
}
?></p>
                </a>
              </li>
           
                <li class="nav-item">
                <a href="./?view=finance&opt=all&spends=Negocio" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance"  && $_GET["opt"]=="all" && ($_GET["spends"]=="Negocio"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Listado"; break;
 case 'EN': echo "List"; break;
}
?></p>
                </a>
              </li>
              
            </ul>
              </li>
              
              
            
               <li class="nav-item">
            <a href="./?view=finance&opt=vehicle" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance"  && (($_GET["opt"]=="vehicle")))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></p>
                </a>
              </li>
              
             
              
              
            </ul>
              </li>
         
<?php endif;
if(UserPermissionsData::getAllBySQL("where permits_id=22 and user_id=".$_SESSION["user_id"])):?>
              <li class="nav-item">
                <a href="./?view=box&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="box" || $_GET["view"]=="b")){ echo "active"; }?>"  style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "CORTE CAJA"; break;
 case 'EN': echo "BOX CUT"; break;
}
?></p>
                </a>
              </li>
 <?php endif;
if(UserPermissionsData::getAllBySQL("where permits_id=23 and user_id=".$_SESSION["user_id"])):?>
              <li class="nav-item">
                <a href="./?view=receipt&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="receipt")){ echo "active"; }?>"  style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "COMPROBANTE DGII"; break;
 case 'EN': echo "DGII RECEIPT"; break;
}
?></p>
                </a>
              </li>
<?php endif;?>
              
            </ul>
          </li>
<?php endif; ////////////////////////////////////////////////////////////////////////////////  EXTRAS. //////////////////////////////////////////////////////////// 
if (UserPermissionsData::getAllBySQL("where permits_id=14 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(14);  ?>
        <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="locations"||$_GET["view"]=="brands"||$_GET["view"]=="colors"||$_GET["view"]=="fuel"||$_GET["view"]=="insurance"||$_GET["view"]=="categories"||$_GET["view"]=="places"||$_GET["view"]=="f"||$_GET["view"]=="sure"||$_GET["view"]=="m")){ echo "menu-open"; }?>">

            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="locations"||$_GET["view"]=="brands" ||$_GET["view"]=="colors"||$_GET["view"]=="fuel"||$_GET["view"]=="insurance"||$_GET["view"]=="categories"||$_GET["view"]=="places"||$_GET["view"]=="f"||$_GET["view"]=="sure"||$_GET["view"]=="m")){ echo "active"; }?>">
         <i class="nav-icon fa fa-cubes"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
                <i class="fas fa-angle-left right"></i> 
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./?view=places&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="places")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "AEROPUERTOS"; break;
 case 'EN': echo "AIRPORTS"; break;
}
?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./?view=locations&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="locations")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "UBICACIONES"; break;
 case 'EN': echo "LOCATIONS"; break;
}
?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./?view=brands&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="brands")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "MARCAS VEHICULO"; break;
 case 'EN': echo "VEHICLE BRANDS"; break;
}
?></p>
                </a>
              </li>
            
             
              
              <li class="nav-item">
                <a href="./?view=colors&opt=all"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="colors")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>COLOR</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="./?view=fuel&opt=all"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="fuel")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "COMBUSTIBLE"; break;
 case 'EN': echo "FUEL"; break;
}
?></p>
                </a>
              </li>
           
            <li class="nav-item">
                <a href="./?view=insurance&opt=all"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="insurance")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "SEGURO/VEHICULAR"; break;
 case 'EN': echo "INSURANCE/VEHICLE"; break;
}
?></p>
                </a>
              </li>

               
               
               <li class="nav-item">
                <a href="./?view=m&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="m")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "TIPO/MANTENIMIENTO"; break;
 case 'EN': echo "TYPE/MAINTENANCE"; break;
}
?></p>
                </a>
              </li>
                       
            </ul>
          </li>
 <?php endif; ////////////////////////////////////////////////////////////////////////////////  REPORTES. //////////////////////////////////////////////////////////// 
 
 if (UserPermissionsData::getAllBySQL("where permits_id=15 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(15); ?>
       <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="sellreports"||$_GET["view"]=="maintenancereport"||$_GET["view"]=="vouchersreports"||$_GET["view"]=="vouchersreports606"||$_GET["view"]=="vouchersreports607"||$_GET["view"]=="clientreports"||$_GET["view"]=="spendsreports"||$_GET["view"]=="vendorreports"||$_GET["view"]=="popularproductsreport"||$_GET["view"]=="paymentreport")){ echo "menu-open"; }?>">

            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="sellreports"||$_GET["view"]=="maintenancereport"||$_GET["view"]=="vouchersreports"||$_GET["view"]=="vouchersreports606"||$_GET["view"]=="vouchersreports607"||$_GET["view"]=="clientreports"||$_GET["view"]=="spendsreports"||$_GET["view"]=="vendorreports"||$_GET["view"]=="popularproductsreport"||$_GET["view"]=="paymentreport")){ echo "active"; }?>">
         <i class="nav-icon fas fa-copy"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
                <i class="fas fa-angle-left right"></i>
                
              </p>
            </a>
            <ul class="nav nav-treeview">
      
              <li class="nav-item">
                <a href="./?view=sellreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="sellreports")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "RENTAS"; break;
 case 'EN': echo "RENTALS"; break;
}
?></p>
                </a>
              </li>
              
               <li class="nav-item">
                <a href="./?view=paymentreport" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="paymentreport")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "COBROS PENDIENTES"; break;
 case 'EN': echo "PENDING COLLECTIONS"; break;
}
?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./?view=maintenancereport" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="maintenancereport")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "MANTENIMIENTO"; break;
 case 'EN': echo "MAINTENANCE"; break;
}
?></p>
                </a>
              </li>

               <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="vouchersreports"||$_GET["view"]=="vouchersreports606"||$_GET["view"]=="vouchersreports607")){ echo "menu-open"; }?>">
                <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="vouchersreports"||$_GET["view"]=="vouchersreports606"||$_GET["view"]=="vouchersreports607")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>DGII / 606/ 607</p>
                    <i class="fas fa-angle-left right"></i>
                </a>
                     <ul class="nav nav-treeview">
             
                 <li class="nav-item">
        
                <a href="./?view=vouchersreports606" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="vouchersreports606")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>606</p>
                </a>
              </li>
           
                <li class="nav-item">
                <a href="./?view=vouchersreports607" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="vouchersreports607")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p> 607</p>
                </a>
              </li>
              
                 <li class="nav-item">
        
                <a href="./?view=vouchersreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="vouchersreports")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Todos"; break;
 case 'EN': echo "All"; break;
}
?></p>
                </a>
              </li>
              
            </ul>
            
              </li>
               <li class="nav-item">
                <a href="./?view=spendsreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="spendsreports")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>  
<?php 
switch (Core::$user->language){
 case 'ES': echo "GASTOS"; break;
 case 'EN': echo "BILLS"; break;
}
?> </p>
                </a>
              </li>

               <li class="nav-item">
                <a href="./?view=clientreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="clientreports")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "CLIENTES POPULARES"; break;
 case 'EN': echo "POPULAR CLIENTS"; break;
}
?></p>
                </a>
              </li>

               <li class="nav-item">
                <a href="./?view=vendorreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="vendorreports")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "EMPLEADOS POPULARES"; break;
 case 'EN': echo "POPULAR EMPLOYEES"; break;
}
?></p>
                </a>
              </li>
     
            </ul>
          </li>
 <?php endif; ////////////////////////////////////////////////////////////////////////////////  ADMINISTRACIÓN. //////////////////////////////////////////////////////////// 
 
 
 if (UserPermissionsData::getAllBySQL("where permits_id=16 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(16);  ?>
       <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="users"||$_GET["view"]=="settings" ||$_GET["view"]=="permissions"||$_GET["view"]=="categories2"||$_GET["view"]=="activity"||$_GET["view"]=="session"||$_GET["view"]=="stocks"||$_GET["view"]=="employees" ||$_GET["view"]=="suppliers"||$_GET["view"]=="template_contract")){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users"||$_GET["view"]=="settings" ||$_GET["view"]=="permissions"||$_GET["view"]=="categories2"||$_GET["view"]=="activity"||$_GET["view"]=="session"||$_GET["view"]=="stocks"||$_GET["view"]=="employees" ||$_GET["view"]=="suppliers"||$_GET["view"]=="template_contract")){ echo "active"; }?>">
         <i class="nav-icon fa fa-cog"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
                <i class="fas fa-angle-left right"></i>
                
              </p>
            </a>
            <ul class="nav nav-treeview">
                
             <li class="nav-item"> 
                <a href="./?view=settings" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="settings")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "CONFIGURACION"; break;
 case 'EN': echo "CONFIGURATION"; break;
}
?></p>
                </a>
              </li>
              
              
          
              
               <li class="nav-item">
                <a href="./?view=suppliers&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="suppliers")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>  
<?php 
switch (Core::$user->language){
 case 'ES': echo "SUPLIDORES"; break;
 case 'EN': echo "SUPPLIERS"; break;
}
?></p>
                </a>
              </li>
              
              
              
               <li class="nav-item">
                <a href="./?view=employees&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="employees")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>  
<?php 
switch (Core::$user->language){
 case 'ES': echo "EMPLEADOS"; break;
 case 'EN': echo "EMPLOYEES"; break;
}
?></p>
                </a>
              </li>
              
            
             <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="users")){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>   
<?php 
switch (Core::$user->language){
 case 'ES': echo "USUARIOS"; break;
 case 'EN': echo "USERS"; break;
}
?></p>
                   <i class="fas fa-angle-left right"></i>
                </a>
                  <ul class="nav nav-treeview">
             
                <li class="nav-item">
                <a href="./?view=users&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users" && $_GET["opt"]=="new")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Crear Nuevo"; break;
 case 'EN': echo "Create New"; break;
}
?></p>
                </a>
              </li>
           
                <li class="nav-item">
                <a href="./?view=users&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users" && ($_GET["opt"]=="all" || $_GET["opt"]=="edit"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Listado"; break;
 case 'EN': echo "List"; break;
}
?></p>
                </a>
              </li>
              
            </ul>
              </li>
         
             
               <li class="nav-item">
                <a href="./?view=session&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="session")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>  
<?php 
switch (Core::$user->language){
 case 'ES': echo "INICIO DE SESION"; break;
 case 'EN': echo "LOGIN"; break;
}
?></p>
                </a>
              </li>

               <li class="nav-item">
                <a href="./?view=activity" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="activity")){ echo "active"; }?>" style="color:gray;">
                  <i class="far fa-circle nav-icon"></i>
                  <p> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "ACTIVIDAD"; break;
 case 'EN': echo "ACTIVITY"; break;
}
?>

</p>
                </a>
              </li>


            </ul>
            
             <li class="nav-item">
  <a href="./?action=panic" class="nav-link text-danger" 
     onclick="return confirm('⚠️ ¿Seguro que quieres activar el modo emergencia?')">
    <i class="nav-icon fas fa-exclamation-triangle"></i>
    <p>MODO EMERGENCIA</p>
  </a>
</li>


          </li>
               <?php endif;  endif; ?>
         
        </ul>
        
      <?php else:  ////////////////////////////////////////////////////////////////////////////////  UPDATE = 0 . //////////////////////////////////////////////////////////// ?>
      
     <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

         
      <?php $operations = UserPermissionsData::getAllByPermitsId2($_SESSION["user_id"]);
$person = UserData::getById($_SESSION["user_id"]);
if (($person->username!="admin" || $person->username!="Admin") && $person->password!="90b9aa7e25f80cf4f64e990b78a9fc5ebd6cecad"):

 
   if (UserPermissionsData::getAllBySQL("where permits_id=1 and user_id=".$_SESSION["user_id"])):   $product = PUData::getById(1); ?>
          <li class="nav-item">
            <a href="./?view=home"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="home" || $_GET["view"]=="person" )){ echo "active"; }?>">
              <i class="nav-icon fa fa-home"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
              </p>
            </a>
          </li>
          
           <?php endif; if (UserPermissionsData::getAllBySQL("where permits_id=12 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(12);  ?>
           
 <?php if(StockData::getPrincipal()->update=="1"):?> 
             <li class="nav-item">
            <a href="./?view=available&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="available")){ echo "active"; }?>">
             <i class="nav-icon fa fa-search"></i>
              <p>DISPONIBILIDAD </p>
            </a>
          </li>
 

<?php endif;?>        
         
         <?php if(StockData::getPrincipal()->update=="1"):?>  
          <li class="nav-item">
  <a href="./?view=messages&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="messages")){ echo "active"; }?>">
    <i class="nav-icon fa fa-comments"></i>
    <p>
      <?php 
      // Texto según idioma
      switch (Core::$user->language){
        case 'ES': echo $product->ubicacion; break;
        case 'EN': echo $product->location; break;
      }

      // ============================
      // Contador dinámico entregas
      // ============================
      $base = new Database();
      $con = $base->connect();

      $hoy = date('Y-m-d H:i:s');
      $manana = date('Y-m-d H:i:s', strtotime('+1 day'));

      // Contar entregas
      $sql_count1 = "SELECT COUNT(*) AS total 
                     FROM booking 
                     WHERE status<>3 
                       AND start_at BETWEEN '$hoy' AND '$manana'";
      $count1 = $con->query($sql_count1)->fetch_assoc()['total'];

      // Contar devoluciones
      $sql_count2 = "SELECT COUNT(*) AS total 
                     FROM booking 
                     WHERE status<>3 
                       AND end_at BETWEEN '$hoy' AND '$manana'";
      $count2 = $con->query($sql_count2)->fetch_assoc()['total'];

      $total_registros = $count1 + $count2;

      // Mostrar badge si hay registros
      if($total_registros > 0){
        echo " <span class='right badge badge-danger'>$total_registros</span>";
      }
      ?>
    </p>
  </a>
</li>

<?php else:?>
                   <li class="nav-item">
            <a href="./?view=messages&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="messages")){ echo "active"; }?>">
             <i class="nav-icon fa fa-comments"></i>
              <p>
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
              </p>
            </a>
          </li>
          
        <?php endif;  endif;
     if (UserPermissionsData::getAllBySQL("where permits_id=2 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(2);  ?>

         

          <li class="nav-item">
            <a href="./?view=calendar" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="calendar")){ echo "active"; }?>">
             <i class="nav-icon fa fa-calendar"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
<?php if ($qt_tot>0):?>
               <span class="right badge badge-danger"><?php echo $qt_tot;?></span>
                <?php endif; ?>
              
               
              </p>
            </a>
          </li>
          
          <li class="nav-item">
            <a  href="./?view=data&opt=all"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="data")){ echo "active"; }?>">
             <i class="nav-icon fa fa-credit-card"></i>
              <p style="font-size:15px">
        
                  <?php 
switch (Core::$user->language){
 case 'ES': echo "DATA CREDITO"; break;
 case 'EN': echo "CREDIT DATA"; break;
}
?>
              </p>
            </a>
          </li>

<?php if(StockData::getPrincipal()->update=="1"):?>
            <li class="nav-item">
            <a href="./?view=maps" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="maps")){ echo "active"; }?>">
             <i class="nav-icon fas fa-map-marked-alt"></i>
              <p style="font-size:15px">
 <?php 
switch (Core::$user->language){
 case 'ES': echo "MAPA DEL CLIENTE"; break;
 case 'EN': echo "CUSTOMER MAP"; break;
}
?>
             
              
             
              </p>
            </a>
          </li>
          
<?php endif; endif; 

if(StockData::getPrincipal()->method=="1"):

if (UserPermissionsData::getAllBySQL("where permits_id=9 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(9);  ?>

  <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && $_GET["opt"]=="all") || ($_GET["view"]=="booking" && $_GET["opt"]=="new") || ($_GET["view"]=="booking" && $_GET["opt"]=="earring") || ($_GET["view"]=="booking" && $_GET["opt"]=="confirmation")){ echo "menu-open"; }?>">
          
           <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && $_GET["opt"]=="all") || ($_GET["view"]=="booking" && $_GET["opt"]=="new") || ($_GET["view"]=="booking" && $_GET["opt"]=="earring") || ($_GET["view"]=="booking" && $_GET["opt"]=="confirmation")){ echo "active"; }?>">   <i class="nav-icon fas fa-suitcase-rolling"></i>
           
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo "RESERVACIONES"; break;
 case 'EN': echo "RESERVATIONS"; break;
}

if($wt_tot>0 || $conf_tot>0):?>
               <span class="right badge badge-warning"><?php echo $conf_tot+$wt_tot;?></span> 
<?php endif;?>
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
<?php 
if(UserPermissionsData::getAllBySQL("where permits_id=17 and user_id=".$_SESSION["user_id"])):?>
                <li class="nav-item">
                <a href="./?view=booking&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && $_GET["opt"]=="new")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Crear Nuevo"; break;
 case 'EN': echo "Create New"; break;
}
?></p>
                </a>
              </li>
<?php endif;?>

<li class="nav-item">
                <a href="./?view=booking&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && ($_GET["opt"]=="all" || $_GET["opt"]=="edit" || $_GET["opt"]=="delivery" || $_GET["opt"]=="modal" || $_GET["opt"]=="delivery"))){ echo "active"; }?>"> 
                  <i class="far fa-circle nav-icon"></i>
                  <p>
              
<?php 
switch (Core::$user->language){
 case 'ES': echo "Entregar Vehiculo"; break;
 case 'EN': echo "Deliver Vehicle"; break;
}
?></p>
                </a>
              </li>
                 

   <li class="nav-item">
                <a href="./?view=booking&opt=confirmation" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && ($_GET["opt"]=="confirmation"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Sin Firmar"; break;
 case 'EN': echo "Unsigned"; break;
}
?></p>
                  <span class="right badge badge-info"><?php echo $conf_tot;?></span>
                </a>
              </li>
               
              
               <li class="nav-item">
                <a href="./?view=booking&opt=earring" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && ($_GET["opt"]=="earring"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Por la Web"; break;
 case 'EN': echo "Through the Web"; break;
}
?></p>
                  <span class="right badge badge-danger"><?php echo $wt_tot;?></span>
                </a>
              </li>
             
            </ul>
          </li> 
           
<?php endif; endif; 


if (UserPermissionsData::getAllBySQL("where permits_id=3 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(3);  ?>

<?php if(UserPermissionsData::getAllBySQL("where permits_id=17 and user_id=".$_SESSION["user_id"])):?>

                    <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="new" || $_GET["opt"]=="newfree"))){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="new" || $_GET["opt"]=="newfree"))){ echo "active"; }?>">   <i class="nav-icon fas fa-key"></i>
              <p style="font-size:15px">
<?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

<?php if(StockData::getPrincipal()->method=="1"):?> 
                <li class="nav-item">
                <a href="./?view=contract&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && $_GET["opt"]=="new")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Rango de Fecha"; break;
 case 'EN': echo "Date Range"; break;
}
?></p>
                </a>
              </li>
  <?php endif;?>              
               <li class="nav-item">
                <a href="./?view=contract&opt=newfree" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="newfree"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Abierto"; break;
 case 'EN': echo "Open"; break;
}
?></p>
                </a>
              </li>

            </ul>
          </li>
          
 <?php endif;?>   
          
           <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="free" || $_GET["opt"]=="finished" || $_GET["opt"]=="running" || $_GET["opt"]=="pay" || $_GET["opt"]=="payfree" || $_GET["opt"]=="replace" || $_GET["opt"]=="replacefree" ||  $_GET["opt"]=="bill" ||  $_GET["opt"]=="billfree" || $_GET["opt"]=="extend" || $_GET["opt"]=="extendfree" || $_GET["opt"]=="random" || $_GET["opt"]=="extenddate" || $_GET["opt"]=="received" || $_GET["opt"]=="payment") || ($_GET["view"]=="booking" &&  $_GET["opt"]=="received" || $_GET["opt"]=="modal" || $_GET["opt"]=="delivery"))){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"])  && ($_GET["view"]=="contract" && ($_GET["opt"]=="free" || $_GET["opt"]=="finished" || $_GET["opt"]=="running" || $_GET["opt"]=="pay" || $_GET["opt"]=="payfree" || $_GET["opt"]=="replace" || $_GET["opt"]=="replacefree" || $_GET["opt"]=="bill" ||  $_GET["opt"]=="billfree" || $_GET["opt"]=="extend" || $_GET["opt"]=="extendfree" || $_GET["opt"]=="random" || $_GET["opt"]=="extenddate" || $_GET["opt"]=="received" || $_GET["opt"]=="payment") || ($_GET["view"]=="booking" &&  $_GET["opt"]=="received" || $_GET["opt"]=="modal" || $_GET["opt"]=="delivery"))){ echo "active"; }?>">   <i class="nav-icon fas fa-road"></i>
              <p style="font-size:15px">
              
<?php 
switch (Core::$user->language){
 case 'ES': echo "VEHICULOS RENTADOS"; break;
 case 'EN': echo "RENTED VEHICLES"; break;
}
?>
               
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                
                       
               <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="free" || $_GET["opt"]=="running" || $_GET["opt"]=="received"))){ echo "menu-open"; }?>">
                <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="running" || $_GET["opt"]=="free" || $_GET["opt"]=="received"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Recibir Vehiculo"; break;
 case 'EN': echo "Receive Vehicle"; break;
}
?></p>

                <i class="fas fa-angle-left right"></i>
                </a>
                
                
            <ul class="nav nav-treeview">
   
<?php if(StockData::getPrincipal()->method=="1"):?>    
                  <li class="nav-item">
                <a href="./?view=contract&opt=running" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && $_GET["opt"]=="running" || $_GET["opt"]=="received")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Rango de Fecha"; break;
 case 'EN': echo "Date Range"; break;
}
?></p>
                </a>
              </li>
   <?php endif;?>
   
               <li class="nav-item">
                <a href="./?view=contract&opt=free" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="free"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Abierto"; break;
 case 'EN': echo "Open"; break;
}
?></p>
                </a>
              </li>
              </ul>
              </li>
  <?php if(StockData::getPrincipal()->method=="1"):?>             
               <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="replace" || $_GET["opt"]=="replacefree" || $_GET["opt"]=="random"))){ echo "menu-open"; }?>">
                <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="replace" || $_GET["opt"]=="replacefree" || $_GET["opt"]=="random"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Reemplazar Vehiculo"; break;
 case 'EN': echo "Replace Vehicle"; break;
}
?></p>
                               <i class="fas fa-angle-left right"></i>
                </a>
                
                
            <ul class="nav nav-treeview">
                  <li class="nav-item">
                <a href="./?view=contract&opt=replace" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && $_GET["opt"]=="replace" || $_GET["opt"]=="random")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Rango de Fecha"; break;
 case 'EN': echo "Date Range"; break;
}
?></p>
                </a>
              </li>
              
               <li class="nav-item">
                <a href="./?view=contract&opt=replacefree" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="replacefree"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Abierto"; break;
 case 'EN': echo "Open"; break;
}
?></p>
                </a>
              </li>
              </ul>
 
              </li>
              
              <li class="nav-item">
                <a href="./?view=contract&opt=extend" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="extend" || $_GET["opt"]=="extenddate"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Extender Fecha"; break;
 case 'EN': echo "Extend Date"; break;
}
?></p>
                </a>
              </li>
              
  <?php endif;?>
  
  
  <?php if(StockData::getPrincipal()->method=="2"):?> 
              <li class="nav-item">
                <a href="./?view=contract&opt=extendfree" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="extendfree" || $_GET["opt"]=="extenddate"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Renovar Contrato"; break;
 case 'EN': echo "Renew Contract"; break;
}
?></p>
                </a>
              </li>
              
  <?php endif;?> 
  
  
             
               <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="billfree" || $_GET["opt"]=="bill"))){ echo "menu-open"; }?>">
                <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="bill" || $_GET["opt"]=="billfree"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                      <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Factura / Contrato"; break;
 case 'EN': echo "Invoice / Contract"; break;
}
?></p>
      

                <i class="fas fa-angle-left right"></i>
                </a>
                
                
            <ul class="nav nav-treeview">
     
<?php if(StockData::getPrincipal()->method=="1"):?>      
                  <li class="nav-item">
                <a href="./?view=contract&opt=bill" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && $_GET["opt"]=="bill")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Rango de Fecha"; break;
 case 'EN': echo "Date Range"; break;
}
?></p>
                </a>
              </li>
 <?php endif;?>             
               <li class="nav-item">
                <a href="./?view=contract&opt=billfree" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="billfree"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Abierto"; break;
 case 'EN': echo "Open"; break;
}
?></p>
                </a>
              </li>
              </ul>
              </li>
              
               <li class="nav-item">
                <a href="./?view=contract&opt=finished" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="finished"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Rentas Completadas"; break;
 case 'EN': echo "Completed Rentals"; break;
}
?></p>
                </a>
              </li>
               
            </ul>
          </li>
     <?php endif;
     
     
   
    
     
    if (UserPermissionsData::getAllBySQL("where permits_id=8 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(8); 
    
    if(StockData::getPrincipal()->update=="1"): ?>
    
           <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="inventory" || ($_GET["view"]=="person" && $_GET["opt"]=="providers"))){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="inventory" || ($_GET["view"]=="person" && $_GET["opt"]=="providers"))){ echo "active"; }?>">
             <i class="nav-icon fa fa-cubes"></i>
              <p>
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
                <i class="fas fa-angle-left right"></i>
               
              </p>
            </a>
            <ul class="nav nav-treeview">
                
                <li class="nav-item">
                <a href="./?view=person&opt=providers" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="person" && $_GET["opt"]=="providers")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Suplidores"; break;
 case 'EN': echo "Suppliers"; break;
}
?></p>
                </a>
              </li>
              
               <li class="nav-item">
                <a href="./?view=inventory&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="inventory" && $_GET["opt"]=="new")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Reabastecer"; break;
 case 'EN': echo "Replenish"; break;
}
?></p>
                </a>
              </li>
              
                <li class="nav-item">
                <a href="./?view=inventory&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="inventory" && ($_GET["opt"]=="all"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Listado"; break;
 case 'EN': echo "List"; break;
}
?></p>
                </a>
              </li>
               
            </ul>
          </li>
     <?php endif;  endif; if (UserPermissionsData::getAllBySQL("where permits_id=6 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(6);  ?>

                    <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars") || ($_GET["view"]=="gallery")){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars") || ($_GET["view"]=="gallery")){ echo "active"; }?>">   <i class="nav-icon fa fa-car"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                <a href="./?view=cars&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars" && $_GET["opt"]=="new")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Crear Nuevo"; break;
 case 'EN': echo "Create New"; break;
}
?></p>
                </a>
              </li>
                <li class="nav-item">
                <a href="./?view=cars&opt=available" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars" && ($_GET["opt"]=="available")) || ($_GET["view"]=="gallery")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Disponible"; break;
 case 'EN': echo "Available"; break;
}
?></p>
                </a>
              </li>
<?php if(StockData::getPrincipal()->method=="1"):?>               
               <li class="nav-item">
                <a href="./?view=cars&opt=reserved" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars" && ($_GET["opt"]=="reserved" ))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Reservado"; break;
 case 'EN': echo "Reserved"; break;
}
?></p>
                </a>
              </li>
<?php endif;?>              
               <li class="nav-item">
                <a href="./?view=cars&opt=rented" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars" && ($_GET["opt"]=="rented"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Rentado"; break;
 case 'EN': echo "Rented"; break;
}
?></p>
                </a>
              </li>
              
               <li class="nav-item">
                <a href="./?view=cars&opt=cogs" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars" && ($_GET["opt"]=="cogs" ))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Taller"; break;
 case 'EN': echo "Workshop"; break;
}
?></p>
                </a>
              </li>
             
             
                <li class="nav-item">
                <a href="./?view=cars&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cars" && ($_GET["opt"]=="all"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Todos"; break;
 case 'EN': echo "All"; break;
}
?></p>
                </a>
              </li>
               

            </ul>
          </li>
    <?php endif; if (UserPermissionsData::getAllBySQL("where permits_id=7 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(7); ?>  
              <li class="nav-item">
            <a href="./?view=reminder" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="reminder")){ echo "active"; }?>">
              <i class="nav-icon fa fa-book"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
             
<?php if ($kq_tot>0):?>
               <span class="right badge badge-danger"><?php echo $kq_tot;?></span>
                <?php endif; ?>
              
               
              </p>
            </a>
          </li>
<?php endif;    if (UserPermissionsData::getAllBySQL("where permits_id=10 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(10);  ?>

                    <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="cotization")){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cotization")){ echo "active"; }?>">   <i class="nav-icon fa fa-th-list"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                <a href="./?view=cotization&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cotization" && $_GET["opt"]=="new")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Crear Nuevo"; break;
 case 'EN': echo "Create New"; break;
}
?></p>
                </a>
              </li>
                <li class="nav-item">
                <a href="./?view=cotization&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="cotization" && ($_GET["opt"]=="all" || $_GET["opt"]=="edit"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Listado"; break;
 case 'EN': echo "List"; break;
}
?></p>
                </a>
              </li>

             
            </ul>
          </li>
   
<?php endif;  if (UserPermissionsData::getAllBySQL("where permits_id=13 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(13);  ?>
       <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="maintenance"||$_GET["view"]=="balance"||$_GET["view"]=="finance"||$_GET["view"]=="receipt"||$_GET["view"]=="boxhistory"||$_GET["view"]=="box"||$_GET["view"]=="spendtype"||$_GET["view"]=="spendtypeisr"||$_GET["view"]=="b"||$_GET["view"]=="credit"||$_GET["view"]=="make"||$_GET["view"]=="payroll")){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="maintenance"||$_GET["view"]=="balance"||$_GET["view"]=="finance"||$_GET["view"]=="receipt"||$_GET["view"]=="boxhistory"||$_GET["view"]=="box"||$_GET["view"]=="spendtype"||$_GET["view"]=="spendtypeisr"||$_GET["view"]=="b"||$_GET["view"]=="credit"||$_GET["view"]=="make"||$_GET["view"]=="payroll")){ echo "active"; }?>">
              <i class="nav-icon fa fa-briefcase"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
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
                <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="credit" && ($_GET["opt"]=="payfree" || $_GET["opt"]=="pay"))){ echo "menu-open"; }?>">
                <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="credit" && ($_GET["opt"]=="pay" || $_GET["opt"]=="payfree"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                   <p>
<?php 
switch (Core::$user->language){
  case 'ES': echo "Deuda Cliente"; break;
 case 'EN': echo "Customer Debt"; break;
}
?></p>

                <i class="fas fa-angle-left right"></i>
                </a>
                
                
            <ul class="nav nav-treeview">
   
   <?php if(StockData::getPrincipal()->method=="1"):?> 
                  <li class="nav-item">
                <a href="./?view=credit&opt=pay" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="credit" && $_GET["opt"]=="pay")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Rango de Fecha"; break;
 case 'EN': echo "Date Range"; break;
}
?></p>
                </a>
              </li>
              
<?php endif;?>
              
               <li class="nav-item">
                <a href="./?view=credit&opt=payfree" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="credit" && ($_GET["opt"]=="payfree"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Abierto"; break;
 case 'EN': echo "Open"; break;
}
?></p>
                </a>
              </li>
              </ul>
              </li>
              
               <li class="nav-item">
                <a href="./?view=credit&opt=stock" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="credit" && $_GET["opt"]=="stock")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Deuda Rent Car"; break;
 case 'EN': echo "Rent Car Debt"; break;
}
?></p>
                </a>
              </li>
              
              <li class="nav-item">
                <a href="./?view=payroll&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="payroll")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Nomina Empleado"; break;
 case 'EN': echo "Employee Payroll"; break;
}
?></p>
                </a>
              </li>
<?php endif;
if(UserPermissionsData::getAllBySQL("where permits_id=21 and user_id=".$_SESSION["user_id"])):?>
              <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance")){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Gastos"; break;
 case 'EN': echo "Bills"; break;
}
?></p>
                   <i class="fas fa-angle-left right"></i>
                </a>
                  <ul class="nav nav-treeview">
              
               <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance" && ($_GET["spends"]=="negocio" || $_GET["spends"]=="Negocio") )){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance" && ($_GET["spends"]=="negocio" || $_GET["spends"]=="Negocio") )){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Local</p>
                   <i class="fas fa-angle-left right"></i>
                </a>
                 <ul class="nav nav-treeview">
             
                <li class="nav-item">
                <a href="./?view=finance&opt=new&spends=negocio" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance" && $_GET["opt"]=="new" && $_GET["spends"]=="negocio")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Crear Nuevo"; break;
 case 'EN': echo "Create New"; break;
}
?></p>
                </a>
              </li>
           
                <li class="nav-item">
                <a href="./?view=finance&opt=all&spends=Negocio" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance"  && $_GET["opt"]=="all" && ($_GET["spends"]=="Negocio"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Listado"; break;
 case 'EN': echo "List"; break;
}
?></p>
                </a>
              </li>
              
            </ul>
              </li>
              
              
            
               <li class="nav-item">
            <a href="./?view=finance&opt=vehicle" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance"  && (($_GET["opt"]=="vehicle")))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Vehiculo"; break;
 case 'EN': echo "Vehicle"; break;
}
?></p>
                </a>
              </li>
              
              
                <li  class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance"  && ($_GET["spends"]=="Otros" || $_GET["spends"]=="other"))){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance"  && (($_GET["spends"]=="Otros" || $_GET["spends"]=="other")))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Otros"; break;
 case 'EN': echo "Others"; break;
}
?></p>
                   <i class="fas fa-angle-left right"></i>
                </a>
                 <ul class="nav nav-treeview">
             
                <li class="nav-item">
                <a href="./?view=finance&opt=new&spends=other" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance"  && $_GET["opt"]=="new"  && $_GET["spends"]=="other")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Crear Nuevo"; break;
 case 'EN': echo "Create New"; break;
}
?></p>
                </a>
              </li>
           
                <li class="nav-item">
                <a href="./?view=finance&opt=all&spends=Otros" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="finance"  && $_GET["opt"]=="all"  && ($_GET["spends"]=="Otros"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i> 
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Listado"; break;
 case 'EN': echo "List"; break;
}
?></p>
                </a>
              </li>
              
            </ul>
              </li>
              
              
            </ul>
              </li>
         
<?php endif;
if(UserPermissionsData::getAllBySQL("where permits_id=22 and user_id=".$_SESSION["user_id"])):?>
              <li class="nav-item">
                <a href="./?view=box&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="box" || $_GET["view"]=="b")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Corte Caja"; break;
 case 'EN': echo "Box Cut"; break;
}
?></p>
                </a>
              </li>
 <?php endif;
if(UserPermissionsData::getAllBySQL("where permits_id=23 and user_id=".$_SESSION["user_id"])):?>
              <li class="nav-item">
                <a href="./?view=receipt&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="receipt")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Comprobante DGII"; break;
 case 'EN': echo "DGII receipt"; break;
}
?></p>
                </a>
              </li>
<?php endif;?>
              
            </ul>
          </li>
<?php endif; if (UserPermissionsData::getAllBySQL("where permits_id=14 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(14);  ?>
        <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="locations"||$_GET["view"]=="brands"||$_GET["view"]=="colors"||$_GET["view"]=="fuel"||$_GET["view"]=="insurance"||$_GET["view"]=="categories"||$_GET["view"]=="places"||$_GET["view"]=="f"||$_GET["view"]=="sure"||$_GET["view"]=="m")){ echo "menu-open"; }?>">

            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="locations"||$_GET["view"]=="brands" ||$_GET["view"]=="colors"||$_GET["view"]=="fuel"||$_GET["view"]=="insurance"||$_GET["view"]=="categories"||$_GET["view"]=="places"||$_GET["view"]=="f"||$_GET["view"]=="sure"||$_GET["view"]=="m")){ echo "active"; }?>">
         <i class="nav-icon fa fa-cubes"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
                <i class="fas fa-angle-left right"></i> 
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./?view=places&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="places")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Aeropuertos"; break;
 case 'EN': echo "Airports"; break;
}
?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./?view=locations&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="locations")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Ubicaciones"; break;
 case 'EN': echo "Locations"; break;
}
?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./?view=brands&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="brands")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Marcas Vehiculo"; break;
 case 'EN': echo "Vehicle Brands"; break;
}
?></p>
                </a>
              </li>
            
             
              
              <li class="nav-item">
                <a href="./?view=colors&opt=all"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="colors")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Color</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="./?view=fuel&opt=all"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="fuel")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Combustible"; break;
 case 'EN': echo "Fuel"; break;
}
?></p>
                </a>
              </li>
           
            <li class="nav-item">
                <a href="./?view=insurance&opt=all"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="insurance")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Seguro/Vehicular"; break;
 case 'EN': echo "Insurance/Vehicle"; break;
}
?></p>
                </a>
              </li>

               
               
               <li class="nav-item">
                <a href="./?view=m&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="m")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Tipo/Mantenimiento"; break;
 case 'EN': echo "Type/Maintenance"; break;
}
?></p>
                </a>
              </li>
                       
            </ul>
          </li>
 <?php endif; if (UserPermissionsData::getAllBySQL("where permits_id=15 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(15); ?>
       <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="sellreports"||$_GET["view"]=="maintenancereport"||$_GET["view"]=="vouchersreports"||$_GET["view"]=="vouchersreports606"||$_GET["view"]=="vouchersreports607"||$_GET["view"]=="clientreports"||$_GET["view"]=="spendsreports"||$_GET["view"]=="vendorreports"||$_GET["view"]=="popularproductsreport"||$_GET["view"]=="paymentreport")){ echo "menu-open"; }?>">

            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="sellreports"||$_GET["view"]=="maintenancereport"||$_GET["view"]=="vouchersreports"||$_GET["view"]=="vouchersreports606"||$_GET["view"]=="vouchersreports607"||$_GET["view"]=="clientreports"||$_GET["view"]=="spendsreports"||$_GET["view"]=="vendorreports"||$_GET["view"]=="popularproductsreport"||$_GET["view"]=="paymentreport")){ echo "active"; }?>">
         <i class="nav-icon fas fa-copy"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
                <i class="fas fa-angle-left right"></i>
                
              </p>
            </a>
            <ul class="nav nav-treeview">
      
              <li class="nav-item">
                <a href="./?view=sellreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="sellreports")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Rentas"; break;
 case 'EN': echo "Rentals"; break;
}
?></p>
                </a>
              </li>
              
               <li class="nav-item">
                <a href="./?view=paymentreport" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="paymentreport")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Cobros Pendientes"; break;
 case 'EN': echo "Pending Collections"; break;
}
?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./?view=maintenancereport" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="maintenancereport")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Mantenimiento"; break;
 case 'EN': echo "Maintenance"; break;
}
?></p>
                </a>
              </li>

               <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="vouchersreports"||$_GET["view"]=="vouchersreports606"||$_GET["view"]=="vouchersreports607")){ echo "menu-open"; }?>">
                <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="vouchersreports"||$_GET["view"]=="vouchersreports606"||$_GET["view"]=="vouchersreports607")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>DGII / 606/ 607</p>
                    <i class="fas fa-angle-left right"></i>
                </a>
                     <ul class="nav nav-treeview">
             
                 <li class="nav-item">
        
                <a href="./?view=vouchersreports606" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="vouchersreports606")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>606</p>
                </a>
              </li>
           
                <li class="nav-item">
                <a href="./?view=vouchersreports607" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="vouchersreports607")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p> 607</p>
                </a>
              </li>
              
                 <li class="nav-item">
        
                <a href="./?view=vouchersreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="vouchersreports")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Todos"; break;
 case 'EN': echo "All"; break;
}
?></p>
                </a>
              </li>
              
            </ul>
            
              </li>
               <li class="nav-item">
                <a href="./?view=spendsreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="spendsreports")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>  
<?php 
switch (Core::$user->language){
 case 'ES': echo "Gastos"; break;
 case 'EN': echo "Bills"; break;
}
?> </p>
                </a>
              </li>

               <li class="nav-item">
                <a href="./?view=clientreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="clientreports")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Clientes Populares"; break;
 case 'EN': echo "Popular Clients"; break;
}
?></p>
                </a>
              </li>

               <li class="nav-item">
                <a href="./?view=vendorreports" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="vendorreports")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Empleados Populares"; break;
 case 'EN': echo "Popular Employees"; break;
}
?></p>
                </a>
              </li>
     
            </ul>
          </li>
 <?php endif; if (UserPermissionsData::getAllBySQL("where permits_id=16 and user_id=".$_SESSION["user_id"])): $product = PUData::getById(16);  ?>
       <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="users"||$_GET["view"]=="settings" ||$_GET["view"]=="permissions"||$_GET["view"]=="categories2"||$_GET["view"]=="activity"||$_GET["view"]=="session"||$_GET["view"]=="stocks"||$_GET["view"]=="employees" ||$_GET["view"]=="suppliers"||$_GET["view"]=="template_contract")){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users"||$_GET["view"]=="settings" ||$_GET["view"]=="permissions"||$_GET["view"]=="categories2"||$_GET["view"]=="activity"||$_GET["view"]=="session"||$_GET["view"]=="stocks"||$_GET["view"]=="employees" ||$_GET["view"]=="suppliers"||$_GET["view"]=="template_contract")){ echo "active"; }?>">
         <i class="nav-icon fa fa-cog"></i>
              <p style="font-size:15px">
                  <?php 
switch (Core::$user->language){
 case 'ES': echo $product->ubicacion; break;
 case 'EN': echo $product->location; break;
}
?>
                <i class="fas fa-angle-left right"></i>
                
              </p>
            </a>
            <ul class="nav nav-treeview">
                
             <li class="nav-item">
                <a href="./?view=settings" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="settings")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Configuracion"; break;
 case 'EN': echo "Configuration"; break;
}
?></p>
                </a>
              </li>
              
              
              <li class="nav-item">
                <a href="./?view=template_contract" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="template_contract")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Plantilla de Contrato"; break;
 case 'EN': echo "Contract Template"; break;
}
?></p>
                </a>
              </li>
              
               <li class="nav-item">
                <a href="./?view=suppliers&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="suppliers")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>  
<?php 
switch (Core::$user->language){
 case 'ES': echo "Suplidores"; break;
 case 'EN': echo "Suppliers"; break;
}
?></p>
                </a>
              </li>
              
              
              
               <li class="nav-item">
                <a href="./?view=employees&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="employees")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>  
<?php 
switch (Core::$user->language){
 case 'ES': echo "Empleados"; break;
 case 'EN': echo "Employees"; break;
}
?></p>
                </a>
              </li>
              
            
             <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="users")){ echo "menu-open"; }?>">
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>   
<?php 
switch (Core::$user->language){
 case 'ES': echo "Usuarios"; break;
 case 'EN': echo "Users"; break;
}
?></p>
                   <i class="fas fa-angle-left right"></i>
                </a>
                  <ul class="nav nav-treeview">
             
                <li class="nav-item">
                <a href="./?view=users&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users" && $_GET["opt"]=="new")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Crear Nuevo"; break;
 case 'EN': echo "Create New"; break;
}
?></p>
                </a>
              </li>
           
                <li class="nav-item">
                <a href="./?view=users&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="users" && ($_GET["opt"]=="all" || $_GET["opt"]=="edit"))){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
<?php 
switch (Core::$user->language){
 case 'ES': echo "Listado"; break;
 case 'EN': echo "List"; break;
}
?></p>
                </a>
              </li>
              
            </ul>
              </li>
         
             
               <li class="nav-item">
                <a href="./?view=session&opt=all" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="session")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>  
<?php 
switch (Core::$user->language){
 case 'ES': echo "Inicio de Sesion"; break;
 case 'EN': echo "Login"; break;
}
?></p>
                </a>
              </li>

               <li class="nav-item">
                <a href="./?view=activity" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="activity")){ echo "active"; }?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p> 
<?php 
switch (Core::$user->language){
 case 'ES': echo "Actividad"; break;
 case 'EN': echo "Activity"; break;
}
?></p>
                </a>
              </li>


            </ul>
            
             

          </li>
               <?php endif;  endif; ?>
         
        </ul>
      
    <?php endif; ?>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    
  
       <!-- Control Sidebar -->
  
   <aside class="control-sidebar" style="width:368px; position: fixed; right: 8.5px; outline: none; color: white; cursor: pointer; padding: 5px; font-size: 18px;">
      <!-- DIRECT CHAT -->
<div id="cartofsell" style="background-color:#222;"></div>
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  
       
 <style>
.floating-buttons {
  position: fixed;
  bottom: 20px;
  right: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  z-index: 9999;
}

.floating-btn {
  display: flex;
  align-items: center;
  background-color: #1e1e1e;
  padding: 10px 16px;
  border-radius: 999px;
  text-decoration: none;
  color: white;
  font-family: sans-serif;
  font-size: 16px;
  position: relative;
  transition: background 0.2s ease, color 0.2s ease;
}

.floating-btn:hover {
  background-color: white;
  color: #1e1e1e;
}

.floating-btn:hover i {
  color: #1e1e1e;
}

.floating-btn i {
  font-size: 18px;
  transition: color 0.2s ease;
}

.message-btn {
  padding-right: 40px;
}

.icon-container {
  position: relative;
  margin-right: 10px;
}

.notification-badge {
  position: absolute;
  top: -6px;
  right: -6px;
  background-color: #ff3b30;
  color: white;
  font-size: 11px;
  padding: 2px 6px;
  border-radius: 50%;
  font-weight: bold;
}

.message-text {
  font-weight: 600;
  font-size: 14px;
}

.pulse-ring {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: radial-gradient(circle at center, #00f, #0ff);
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
}


.floating-btn.message-btn:hover {
  color: black !important;
}

.floating-btn.message-btn:hover i {
  color: white !important;
}


  </style>
  
<script type="text/javascript">
      $.get("./?action=get&opt=chat","",function(data2){
        $("#cartofsell").html(data2);
      });
</script>

<?php if(isset($_GET["view"]) && ($_GET["view"]=="home")):?>   

  <div class="floating-buttons">
  <!-- Botón 1: Regresar -->
  <a href="https://wa.me/14019984583" target="_black" class="floating-btn" style="background-color: #4DBE04;">
     <div class="icon-container">
    <i class="fab fa-whatsapp"></i>
    </div>
    <span class="message-text"> SOPORTE</span>
  </a>

 <!-- Botón de CHAT con notificación azul -->
<a onclick="enfocarInput()" data-widget="control-sidebar" data-slide="true" class="floating-btn message-btn" style="background-color: orange;">
  <div class="icon-container">
    <i class="fa fa-comment-dots"></i>
    <span class="notification-badge badge-danger">1</span>
  </div>
  <span class="message-text">CHAT</span>
</a>


  <!-- Botón 3: Otra acción (ej. home) -->
  <a href="./?action=logout"  class="floating-btn" style="background-color: #C70039;">
      <div class="icon-container">
    <i class="fa fa-power-off"> </i>
    </div>
    <span class="message-text"> SALIR</span> 
  </a>
</div>

<?php elseif(isset($_GET["view"], $_GET["opt"]) && !($_GET["view"] == "booking" && $_GET["opt"] == "new") && !($_GET["view"] == "contract" && $_GET["opt"] == "new") && !($_GET["view"] == "contract" && $_GET["opt"] == "newfree")): ?>
<div class="floating-buttons">
  <!-- Botón 1: Regresar -->
  <a onclick="history.back()" class="floating-btn" style="background-color: orange;">
    <i class="fa fa-arrow-left"> Regresar</i>
  </a>
</div>
<?php endif;?>
  </aside>
  
<?php elseif(isset($_SESSION["client_id"])):
$users = PersonData::getById($_SESSION["client_id"]);
$stockname = StockData::getById($users->stock_id);
date_default_timezone_set("America/Santo_Domingo");?>


  <!-- Navbar -->
   <nav class="main-header navbar navbar-expand navbar-dark" style="background-color: #222;">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    
    </ul>

  <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->


    </ul>
  </nav>
  
  <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #222;">
    <!-- Brand Logo -->
     <center>
    <a  class="brand-link" style="background-color: #333;">
      <span id="texto"  class="brand-text texto-ajustable"><?php echo strtoupper($stockname->name);?></span>
    </a>
    
    <script>
  
  function ajustarTamanoTexto() {
  const elemento = document.getElementById("texto");
  const maxTamano = 8; // Tamaño máximo de la fuente en px
  const minTamano = 15; // Tamaño mínimo
  const baseCaracteres = 20; // Cantidad base antes de reducir
  let texto = elemento.textContent.length;
  
  let nuevoTamano = Math.max(minTamano, maxTamano - (texto - baseCaracteres));
  elemento.style.fontSize = nuevoTamano + "px";
}

ajustarTamanoTexto(); // Llamar al inicio</script>
</center>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
<a data-toggle="modal" data-target="#myIMG">

        <div class="image">
          <?php
         
            $url = "CF-SYSTEMS/storage/profiles/man.png";
            if(file_exists($url)):
              echo "<img src='$url'  class='img-circle elevation-2'>";
            endif;
          
          ?>
          
        </div>
        <div class="info">
          <a style="color: #ddd;" class="d-block">
             <?php
               if($users):
           echo $users->name." ".$users->lastname;
          
            endif;
             
                  
                  ?>
<br>
                 
<?php 
$lang = $users->language;
$onlineText = $lang == 'EN' ? 'Online' : 'En Línea';
$offlineText = $lang == 'EN' ? 'Offline' : 'Sin Conexión';
?>

<span style="display: inline-flex; align-items: center;">
  <span id="estado-circulo" style="
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: green;
    display: inline-block;
    margin-right: 8px;
  "></span>
  <span id="estado-texto"><?php echo $onlineText; ?></span>
</span>
<script>
let estadoAnterior = navigator.onLine; // Estado inicial

function actualizarEstadoConexion() {
  const dot = document.getElementById("estado-circulo");
  const text = document.getElementById("estado-texto");
  const conectado = navigator.onLine;

  if (conectado) {
    dot.style.backgroundColor = "green";
    text.textContent = "<?php echo $onlineText; ?>";
    if (!estadoAnterior) {
      toastr.success("<?php echo $onlineText; ?>");
    }
  } else {
    dot.style.backgroundColor = "red";
    text.textContent = "<?php echo $offlineText; ?>";
    if (estadoAnterior) {
      toastr.error("<?php echo $offlineText; ?>");
    }
  }

  estadoAnterior = conectado; // Actualizar estado
}

// Inicializar
actualizarEstadoConexion();

// Escuchar cambios de red
window.addEventListener('online', actualizarEstadoConexion);
window.addEventListener('offline', actualizarEstadoConexion);
</script>


          </a>
      
        </div></a>

      </div>


      <!-- Sidebar Menu -->
       <nav class="mt-2">



        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

         

        <li class="nav-item">
            <a href="./?view=home"  class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="home" || ($_GET["view"]=="booking" && $_GET["opt"]=="signature"))){ echo "active"; }?>">
              <i class="nav-icon fa fa-home"></i>
              <p style="font-size:15px">
                  <?php 
switch ($users->language){
 case 'ES': echo "TABLERO"; break;
 case 'EN': echo "BOARD"; break;
}
?>
              </p>
            </a>
          </li>
          
           
            <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && ($_GET["opt"]=="delivery" || $_GET["opt"]=="all" || $_GET["opt"]=="edit" || $_GET["opt"]=="new" || $_GET["opt"]=="running" || $_GET["opt"]=="cluster" || $_GET["opt"]=="confirmation"|| $_GET["opt"]=="earring") || ($_GET["view"]=="available") || ($_GET["view"]=="contract" && ($_GET["opt"]=="finished" ||$_GET["opt"]=="new" || $_GET["opt"]=="newfree" || $_GET["opt"]=="newhours")))){ echo "menu-open"; }?>">
                
            <a href="#" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && ($_GET["opt"]=="delivery" || $_GET["opt"]=="all" || $_GET["opt"]=="edit"  || $_GET["opt"]=="new" || $_GET["opt"]=="running" || $_GET["opt"]=="cluster" || $_GET["opt"]=="confirmation"|| $_GET["opt"]=="earring") || ($_GET["view"]=="available") || ($_GET["view"]=="contract" && ($_GET["opt"]=="finished" || $_GET["opt"]=="new" || $_GET["opt"]=="newfree" || $_GET["opt"]=="newhours")))){ echo "active"; }?>">
              <i class="nav-icon fas fa-car"></i>
              <p>
                OPERACIONES
             
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                       
    <li class="nav-item">
            <a href="./?view=booking&opt=new" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="booking" && ($_GET["opt"]=="new" || $_GET["opt"]=="cluster" || $_GET["opt"]=="confirmation" || $_GET["opt"]=="earring"))){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p><?php 
switch ($users->language){
 case 'ES': echo "RESERVACIONES"; break;
 case 'EN': echo "RESERVATIONS"; break;
}

?> </p>
            </a>
          </li>

 
 

 
          <li class="nav-item">
             <a href="./?view=contract&opt=finished" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="finished"))){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p><?php 
switch ($users->language){
 case 'ES': echo "RENTAR COMPLETADAS"; break;
 case 'EN': echo "RENT COMPLETED"; break;
}

?> </p>
            </a>
          </li>
            </ul>
          </li>
          
          

           <li class="nav-item <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="free" || $_GET["opt"]=="running" || $_GET["opt"]=="replace" || $_GET["opt"]=="replacefree"|| $_GET["opt"]=="extend" || $_GET["opt"]=="extendfree" || $_GET["opt"]=="random" || $_GET["opt"]=="extenddate" || $_GET["opt"]=="received" || $_GET["opt"]=="billfree" || $_GET["opt"]=="bill" || $_GET["opt"]=="edit" || $_GET["opt"]=="modal"))){ echo "menu-open"; }?>">
          
            <a href="#" class="nav-link <?php if(isset($_GET["view"])  && ($_GET["view"]=="contract" && ($_GET["opt"]=="free" || $_GET["opt"]=="running" || $_GET["opt"]=="replace" || $_GET["opt"]=="replacefree"|| $_GET["opt"]=="extend" || $_GET["opt"]=="extendfree" || $_GET["opt"]=="random" || $_GET["opt"]=="extenddate" || $_GET["opt"]=="received" || $_GET["opt"]=="billfree" || $_GET["opt"]=="bill" || $_GET["opt"]=="edit" || $_GET["opt"]=="modal"))){ echo "active"; }?>"><i class="fa fa-clipboard-list nav-icon"></i>
              <p style="font-size:15px">
              
<?php 
switch ($users->language){
 case 'ES': echo "GESTION DE RENTAS"; break;
 case 'EN': echo "RENT MANAGEMENT"; break;
}
?>
               
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
                <li class="nav-item">
             <a href="./?view=contract&opt=bill" class="nav-link <?php if(isset($_GET["view"]) && ($_GET["view"]=="contract" && ($_GET["opt"]=="bill" || $_GET["opt"]=="billfree"))){ echo "active"; }?>" style="color:gray;">
             <i class="far fa-circle nav-icon"></i>
              <p><?php 
switch ($users->language){
 case 'ES': echo "FACTURA / CONTRATO"; break;
 case 'EN': echo "INVOICE / CONTRACT"; break;
}

?> </p>
            </a>
          </li>  
                       


               
            </ul>
          </li>
 
         
        </ul>
        
     
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    
  
       <!-- Control Sidebar -->
  </aside>
  
  <aside class="control-sidebar" style="width:368px; position: fixed; right: 8.5px; outline: none; color: white; cursor: pointer; padding: 5px; font-size: 18px;">
      <!-- DIRECT CHAT -->
<div id="cartofsell" style="background-color:#222;"></div>
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  
       
 <style>
.floating-buttons {
  position: fixed;
  bottom: 20px;
  right: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  z-index: 9999;
}

.floating-btn {
  display: flex;
  align-items: center;
  background-color: #1e1e1e;
  padding: 10px 16px;
  border-radius: 999px;
  text-decoration: none;
  color: white;
  font-family: sans-serif;
  font-size: 16px;
  position: relative;
  transition: background 0.2s ease, color 0.2s ease;
}

.floating-btn:hover {
  background-color: white;
  color: #1e1e1e;
}

.floating-btn:hover i {
  color: #1e1e1e;
}

.floating-btn i {
  font-size: 18px;
  transition: color 0.2s ease;
}

.message-btn {
  padding-right: 40px;
}

.icon-container {
  position: relative;
  margin-right: 10px;
}

.notification-badge {
  position: absolute;
  top: -6px;
  right: -6px;
  background-color: #ff3b30;
  color: white;
  font-size: 11px;
  padding: 2px 6px;
  border-radius: 50%;
  font-weight: bold;
}

.message-text {
  font-weight: 600;
  font-size: 14px;
}

.pulse-ring {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: radial-gradient(circle at center, #00f, #0ff);
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
}


.floating-btn.message-btn:hover {
  color: black !important;
}

.floating-btn.message-btn:hover i {
  color: white !important;
}


  </style>
  

<?php if(isset($_GET["view"]) && ($_GET["view"]=="home")):?>   

  <div class="floating-buttons">
  <!-- Botón 1: Regresar -->
  <a href="https://wa.me/<?$stockname->phone;?>" target="_black" class="floating-btn" style="background-color: #4DBE04;">
     <div class="icon-container">
    <i class="fab fa-whatsapp"></i>
    </div>
    <span class="message-text"> SOPORTE</span>
  </a>

 <!-- Botón de CHAT con notificación azul -->
<a onclick="enfocarInput()" data-widget="control-sidebar" data-slide="true" class="floating-btn message-btn" style="background-color: orange;">
  <div class="icon-container">
    <i class="fa fa-comment-dots"></i>
    <span class="notification-badge badge-danger">1</span>
  </div>
  <span class="message-text">CHAT</span>
</a>


  <!-- Botón 3: Otra acción (ej. home) -->
  <a href="./?action=logout"  class="floating-btn" style="background-color: #C70039;">
      <div class="icon-container">
    <i class="fa fa-power-off"> </i>
    </div>
    <span class="message-text"> SALIR</span> 
  </a>
</div>

<?php elseif(isset($_GET["view"], $_GET["opt"]) && !($_GET["view"] == "booking" && $_GET["opt"] == "new") && !($_GET["view"] == "contract" && $_GET["opt"] == "new") && !($_GET["view"] == "contract" && $_GET["opt"] == "newfree")): ?>
<div class="floating-buttons">
  <!-- Botón 1: Regresar -->
  <a onclick="history.back()" class="floating-btn" style="background-color: orange;">
    <i class="fa fa-arrow-left"> Regresar</i>
  </a>
</div>
<?php endif; endif;?>
     <!-- Content Wrapper. Contains page content -->
      <?php if(isset($_SESSION["user_id"]) || isset($_SESSION['client_id'])):?>
      <div class="content-wrapper"  style="background-color: #333;">
        <?php View::load("index");?>
      </div><!-- /.content-wrapper -->

   
  
  <footer class="main-footer"  style="background-color: #222;">
    <strong>Copyright &copy; 2019-<?php echo date("Y");?></strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 24.2.29
    </div>
  </footer>
 </body>
 
 

        <?php else:?>
<?php
session_start();

/* ==========================================
   SEGURIDAD DE SESIÓN (ANTI-ROBO DE SESIÓN)
========================================== */
if(!isset($_SESSION["fingerprint"])){
    $_SESSION["fingerprint"] = md5($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
} else {
    if($_SESSION["fingerprint"] !== md5($_SERVER['HTTP_USER_AGENT'] ?? 'unknown')){
        session_unset();
        session_destroy();
        header("Location: ./?login=1");
        exit;
    }
}

$mostrar_login = false;

/* ==========================================
   CONEXION
========================================== */
if(!isset($con) || !($con instanceof mysqli)){
    $base = new Database();
    $con  = $base->connect();

    if(!$con){
        die("Error de conexión");
    }

    mysqli_set_charset($con,"utf8");
}

/* ==========================================
   CONFIGURACION DE COOKIES
========================================== */
$cookieOptions = [
    'path'     => '/',
    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax'
];

/*
|--------------------------------------------------------------------------
| 1. SI ENTRA MANUALMENTE A ?login=1
|--------------------------------------------------------------------------
| Se marca este navegador como que ya visitó el login.
*/
if(isset($_GET["login"]) && $_GET["login"] == "1"){
    $mostrar_login = true;

    setcookie("seen_login", "1", array_merge($cookieOptions, [
        'expires' => time() + (86400 * 365)
    ]));
}

/*
|--------------------------------------------------------------------------
| 2. SI YA TIENE SESIÓN ACTIVA
|--------------------------------------------------------------------------
*/
elseif(isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"])){
    $mostrar_login = true;

    setcookie("seen_login", "1", array_merge($cookieOptions, [
        'expires' => time() + (86400 * 365)
    ]));
}

/*
|--------------------------------------------------------------------------
| 3. SI TIENE COOKIE DE RECORDAR LOGIN
|--------------------------------------------------------------------------
*/
elseif(isset($_COOKIE["remember_token"]) && !empty($_COOKIE["remember_token"])){

    $token = $_COOKIE["remember_token"];
    $now   = date("Y-m-d H:i:s");

    $stmt = $con->prepare("
        SELECT id, stock_id 
        FROM user 
        WHERE remember_token = ?
          AND remember_expire >= ?
        LIMIT 1
    ");

    if($stmt){
        $stmt->bind_param("ss", $token, $now);
        $stmt->execute();
        $stmt->bind_result($uid, $stock_id_db);

        if($stmt->fetch()){
            $mostrar_login = true;

            $_SESSION["user_id"]  = (int)$uid;
            $_SESSION["stock_id"] = (int)$stock_id_db;

            // renovar token y vencimiento
            try{
                $new_token = bin2hex(random_bytes(32));
            } catch(Throwable $e){
                $new_token = bin2hex(openssl_random_pseudo_bytes(32));
            }

            $new_expire = date("Y-m-d H:i:s", strtotime("+30 days"));

            $stmt->close();

            $stmt2 = $con->prepare("
                UPDATE user 
                SET remember_token = ?, remember_expire = ?
                WHERE id = ?
            ");

            if($stmt2){
                $stmt2->bind_param("ssi", $new_token, $new_expire, $uid);
                $stmt2->execute();
                $stmt2->close();
            }

            setcookie("remember_token", $new_token, array_merge($cookieOptions, [
                'expires' => time() + (86400 * 30)
            ]));

            setcookie("seen_login", "1", array_merge($cookieOptions, [
                'expires' => time() + (86400 * 365)
            ]));
        } else {
            $stmt->close();
        }
    }
}

/*
|--------------------------------------------------------------------------
| 4. SI YA ESTE NAVEGADOR HABÍA ENTRADO ANTES AL LOGIN
|--------------------------------------------------------------------------
*/
elseif(isset($_COOKIE["seen_login"]) && $_COOKIE["seen_login"] == "1"){
    $mostrar_login = true;
}
?>

<?php if($mostrar_login): ?>

<style>
body {
  margin: 0;
  padding: 0;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
}

/* capas de fondo */
.bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center 70%;
  background-repeat: no-repeat;
  opacity: 0;
  transition: opacity 1s ease-in-out;
}

/* activa la imagen visible */
.bg.active {
  opacity: 1;
}
</style>

<div class="bg active" style="background-image: url('<?php echo StockData::getFPrincipal(1)->img_1; ?>');"></div>
<div class="bg" style="background-image: url('<?php echo StockData::getFPrincipal(1)->img_2; ?>');"></div>
<div class="bg" style="background-image: url('<?php echo StockData::getFPrincipal(1)->img_3; ?>');"></div>
<div class="bg" style="background-image: url('<?php echo StockData::getFPrincipal(1)->img_4; ?>');"></div>

<script>
const slides = document.querySelectorAll('.bg');
let index = 0;

setInterval(() => {
  slides[index].classList.remove('active');
  index = (index + 1) % slides.length;
  slides[index].classList.add('active');
}, 5000);
</script>

<style>
  html, body{
    width:100%;
    min-height:100vh;
    font-family: Arial, sans-serif;
  }

  .login-wrapper{
    width:100%;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px 15px 90px 15px;
    box-sizing:border-box;
  }

  .login-box-custom{
    max-width:380px;
    width:100%;
    background:rgba(30,30,30,0.35);
    backdrop-filter:blur(10px);
    -webkit-backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,0.12);
    border-radius:18px;
    box-shadow:0 8px 30px rgba(0,0,0,0.25);
    padding:22px 18px 20px 18px;
    text-align:center;
  }

  .lockscreen-logo{
    width:100%;
    margin:0 auto 18px auto;
    text-align:center;
  }

  .lockscreen-logo img{
    width:70%;
    max-width:210px;
    height:auto;
    display:block;
    margin:0 auto;
  }

  .login-form-custom{
    width:100%;
  }

  .login-form-custom .form-control{
    width:100%;
    max-width:300px;
    margin:0 auto 14px auto;
    height:48px;
    border-radius:12px;
    border:1px solid rgba(255,255,255,0.25);
    padding:0 16px;
    font-size:16px;
    background:rgba(255,255,255,0.95);
    color:#333;
    box-sizing:border-box;
  }

  .login-form-custom .form-control:focus{
    outline:none;
    border-color:#3fa9f5;
    box-shadow:0 0 0 3px rgba(63,169,245,0.18);
  }

  .btn-login-custom{
    width:100%;
    max-width:300px;
    height:50px;
    border:none;
    border-radius:12px;
    background:orange;
    color:#fff;
    font-size:1.1rem;
    font-weight:700;
    box-shadow:0 6px 18px rgba(0,0,0,0.20);
    transition:all 0.2s ease;
    cursor:pointer;
  }

  .btn-login-custom:hover{
    filter:brightness(1.08);
    transform:scale(1.02);
  }

  .help-block-custom{
    margin-top:10px;
    color:#fff;
    font-size:15px;
    line-height:1.4;
  }

  .help-link-custom{
    margin-top:10px;
    display:inline-block;
    color:#fff;
    text-decoration:underline;
    font-size:15px;
  }

  .footer-custom{
    position:fixed;
    bottom:0;
    left:0;
    width:100%;
    background:#2f3740;
    color:#fff;
    text-align:center;
    padding:12px 10px;
    font-size:15px;
    box-shadow:0 -2px 10px rgba(0,0,0,0.15);
    z-index:99;
  }

  .footer-custom a{
    color:#fff;
    text-decoration:underline;
    font-weight:bold;
  }

  @media (max-width: 480px){
    .login-wrapper{
      align-items:center;
      padding:20px 14px 85px 14px;
    }

    .login-box-custom{
      max-width:360px;
      padding:20px 15px 18px 15px;
      border-radius:16px;
    }

    .lockscreen-logo img{
      width:68%;
      max-width:190px;
    }

    .login-form-custom .form-control{
      height:46px;
      font-size:16px;
    }

    .btn-login-custom{
      height:48px;
      font-size:1rem;
    }

    .help-block-custom,
    .help-link-custom{
      font-size:14px;
    }

    .footer-custom{
      font-size:14px;
      padding:10px 8px;
    }
  }
</style>

<div class="login-wrapper">
  <div class="login-box-custom">

    <div class="lockscreen-logo">
      <?php
        $url = "CF-SYSTEMS/storage/configuration/".StockData::getFPrincipal(1)->ticket_image;
        if(file_exists($url)){
          echo "<img src='$url' alt='<?php echo StockData::getFPrincipal(1)->name; ?>'>";
        }
      ?>
    </div>

    <form id="login_form1" class="login-form-custom">
      <input type="text" name="username" class="form-control" placeholder="Usuario" required>
      <input type="password" name="password" class="form-control" placeholder="Contraseña" required>

      <button type="submit" class="btn-login-custom" id="btn-login">
        Iniciar Sesión
      </button>
    </form>

    <div class="help-block-custom">
      Usuario &amp; contraseña para iniciar tu sesión
    </div>

   <a href="https://wa.me/<?php echo StockData::getFPrincipal(1)->phone;?>" class="help-link-custom">
      ¿Necesitas ayuda?
    </a>

  </div>
</div>

<footer class="footer-custom">
  &copy; 2019-<?php echo date("Y"); ?>
  <b><a href="https://www.assanpos.com">AssanPos.com</a></b>
</footer>

<script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('service-worker.js')
    .then(function(reg) {
      console.log('Service Worker registrado.', reg);
    })
    .catch(function(err) {
      console.error('Error registrando SW:', err);
    });
}
</script>

<script>
jQuery(document).ready(function(){

  $("#login_form1").submit(function(e){
    e.preventDefault();

    var btn = $("#btn-login");
    btn.prop("disabled", true).text("Validando...");

    $.ajax({
      type: "POST",
      url: "./?action=process&opt=login",
      data: $(this).serialize(),
      dataType: "json", // 🔥 CLAVE
      success: function(res){

        if(res.ok){
          $.jGrowl("Bienvenido", { header: 'Acceso permitido' });
          $.jGrowl("Sistema de <?php echo StockData::getFPrincipal(1)->name; ?>", { header: 'Acceso permitido' });

          setTimeout(function(){
            window.location = res.redirect;
          }, 800);

        }else{
          $.jGrowl(res.message, { header: 'Error de inicio de sesión' });

          btn.prop("disabled", false).text("Iniciar Sesión");
        }

      },
      error: function(xhr){
        console.log("ERROR LOGIN:", xhr.responseText);

        $.jGrowl("Error del sistema", { header: 'Error' });

        btn.prop("disabled", false).text("Iniciar Sesión");
      }
    });

  });

});
</script>


<button id="install-button" style="
  display: none;
  background-color: #0a0a0a;
  color: #ffffff;
  border: none;
  padding: 12px 20px;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  cursor: pointer;
  position: fixed;
  bottom: 65px;
  right: 20px;
  z-index: 9999;
  transition: background-color 0.3s ease;
">
  📲 Instalar App
</button>

<script>
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;

  const installBtn = document.getElementById('install-button');
  installBtn.style.display = 'block';

  installBtn.addEventListener('click', () => {
    installBtn.style.display = 'none';
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then((choiceResult) => {
      if (choiceResult.outcome === 'accepted') {
        console.log('El usuario aceptó instalar');
      } else {
        console.log('El usuario rechazó');
      }
      deferredPrompt = null;
    });
  });
});
</script>


<?php else: ?> 

<?php include __DIR__ . "/../../../WEB/index.php"; ?> <?php endif; ?>

    </body> 
   
      <?php endif;?><!-- AdminLTE App -->
        
<script src="CF-SYSTEMS/dist/js/adminlte.js"></script>
<script>
/* Guard: AdminLTE IFrame plugin reads localStorage["AdminLTE:IFrame:Options"]
   at window.load and crashes when the value is missing/null. */
(function () {
  try {
    var k = "AdminLTE:IFrame:Options";
    var v = localStorage.getItem(k);
    if (v === null || v === "null" || v === "") { localStorage.setItem(k, "{}"); }
  } catch (e) { /* storage unavailable — ignore */ }
})();
</script>



<!-- Toastr -->
<script src="CF-SYSTEMS/plugins/toastr/toastr.min.js"></script>
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
    
     <script type="text/javascript">
      $("#example2").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

      
    </script>
    
     <script type="text/javascript">
      $("#example3").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

      
    </script>
    
     <script type="text/javascript">
      $("#example4").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

      
    </script>
    
     <script type="text/javascript">
      $("#example5").DataTable({
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

  // BS-Stepper Init (guarded: only init when a stepper exists on the page)
  document.addEventListener('DOMContentLoaded', function () {
    var stepperEl = document.querySelector('.bs-stepper');
    if (stepperEl && typeof Stepper !== 'undefined') {
      window.stepper = new Stepper(stepperEl);
    }
  })

  // DropzoneJS Demo Code Start (guarded: only init when the #template node exists)
  if (typeof Dropzone !== 'undefined') { Dropzone.autoDiscover = false; }

  var previewNode = document.querySelector("#template");
  if (previewNode && typeof Dropzone !== 'undefined') {
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
  } // end if(previewNode)

</script>
 
  
  
</html>

