<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* --------------------------------------------------------------------
 * Aggressive no-cache headers
 * ------------------------------------------------------------------
 * Mobile Safari and Chrome are particularly aggressive at caching
 * back-navigation HTML, which has caused users to keep seeing stale
 * pages (raw i18n keys like "nav_home") long after we shipped the fix
 * server-side. These headers — combined with the meta tags in <head> —
 * make sure every request gets a fresh render.
 * ------------------------------------------------------------------ */
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

include_once __DIR__ . "/../lang.php";

/* ================= INCLUDES SEGUROS PHP 8.4 ================= */
include_once __DIR__ . "/../../core/controller/Core.php";
include_once __DIR__ . "/../../core/controller/Database.php";
include_once __DIR__ . "/../../core/controller/Executor.php";
include_once __DIR__ . "/../../core/controller/Model.php";

include_once __DIR__ . "/../../core/app/model/StockData.php";
include_once __DIR__ . "/../../core/app/model/PersonData.php";
include_once __DIR__ . "/../../core/app/model/FuelData.php";
include_once __DIR__ . "/../../core/app/model/TransmissionData.php";
include_once __DIR__ . "/../../core/app/model/CategoryData.php";
include_once __DIR__ . "/../../core/app/model/UserData.php";
include_once __DIR__ . "/../../core/app/model/KData.php";
include_once __DIR__ . "/../../core/app/model/CarsData.php";
include_once __DIR__ . "/../../core/app/model/ColorData.php";
include_once __DIR__ . "/../../core/app/model/BrandData.php";
include_once __DIR__ . "/../../core/app/model/BookingData.php";

/* ================= URL ================= */

$WEBRL = $_SERVER['HTTP_HOST'] ?? '';
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";

$home = $protocol . $WEBRL;

/* ================= BASE URL AUTOMATICA ================= */

$request_uri = $_SERVER['REQUEST_URI'] ?? '';

if (
    strpos($request_uri, '/WEB/') !== false
) {

    $base_url = $protocol . $WEBRL . "/WEB/";

} else {

    $base_url = $protocol . $WEBRL . "/WEB/";

}

/* ================= CONFIG ================= */
$selstock     = 0;
$title        = "";
$ticket_image = "";
$web_type     = "";
$webimg       = "";
$text         = "";
$type_img     = "";

$WEBRL_SAFE = addslashes($WEBRL);
$home_SAFE  = addslashes($home);

$stocks = StockData::getAllBySQL("
    WHERE web_url = '$WEBRL_SAFE'
    OR web_url2 = '$WEBRL_SAFE'
    OR web_url = '$home_SAFE'
    OR web_url2 = '$home_SAFE'
    LIMIT 1
");

if (empty($stocks)) {
    $stocks = StockData::getAllBySQL("ORDER BY id ASC LIMIT 1");
}

$phone = "";
$color = array(220, 38, 38);

if (!empty($stocks)) {

    $s = $stocks[0];

    $selstock = isset($s->id) ? intval($s->id) : 0;
    $phone = isset($s->phone) ? $s->phone : "";
    $web_type = isset($s->web_type) ? $s->web_type : "";
    $webimg   = isset($s->web_img) ? $s->web_img : "";
    $text     = isset($s->web_text) ? $s->web_text : "";
    $title    = isset($s->name) && trim((string)$s->name) != "" ? $s->name : "RENT CAR";
    $type_img = isset($s->type_img) ? $s->type_img : "";

    if (isset($s->ticket_image) && trim((string)$s->ticket_image) != "") {
        $ticket_image = trim((string)$s->ticket_image);
    }

    if (isset($s->color) && trim((string)$s->color) != "") {
        $tmp_color = explode(",", $s->color);

        if (count($tmp_color) >= 3) {
            $color = array(
                intval($tmp_color[0]),
                intval($tmp_color[1]),
                intval($tmp_color[2])
            );
        }
    }
}

$mainColor = intval($color[0]) . "," . intval($color[1]) . "," . intval($color[2]);


$color = array($mainColor);

$title_safe        = htmlspecialchars((string)$title, ENT_QUOTES, 'UTF-8');
$ticket_image_safe = htmlspecialchars((string)$ticket_image, ENT_QUOTES, 'UTF-8');
$base_url_safe     = htmlspecialchars((string)$base_url, ENT_QUOTES, 'UTF-8');

$version = time();
?>

<!DOCTYPE html>
<html lang="<?php echo $LANG; ?>">
<head>

<title><?php echo $title_safe; ?></title>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<link href="../../CF-SYSTEMS/storage/configuration/<?php echo $ticket_image_safe; ?>?v=<?php echo $version; ?>" rel="shortcut icon">

<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/bootstrap.min.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/open-iconic-bootstrap.min.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/animate.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/owl.carousel.min.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/owl.theme.default.min.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/magnific-popup.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/aos.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/ionicons.min.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/bootstrap-datepicker.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/jquery.timepicker.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/flaticon.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/icomoon.css?v=<?php echo $version; ?>">
<link rel="stylesheet" href="<?php echo $base_url_safe; ?>css/style.css?v=<?php echo $version; ?>">

<style>

:root{
    --main-color:rgb(<?php echo $mainColor; ?>);
}

*{
    font-family:Arial, Helvetica, sans-serif;
}

html,
body{
    background:#ffffff !important;
    color:#111827 !important;
    margin:0;
    padding:0;
    overflow-x:hidden;
}

body{
    padding-top:105px !important;
}

/* ================= FIX GENERAL PHP 8.4 ================= */

.ftco-section{
    position:relative !important;
    display:block !important;
    visibility:visible !important;
    opacity:1 !important;
    overflow:visible !important;
    background:#fff;
}

.section-mini,
.section-title,
.section-subtitle{
    display:block !important;
    visibility:visible !important;
    opacity:1 !important;
    position:relative !important;
    z-index:20 !important;
}

.section-mini{
    color:var(--main-color) !important;
}

.section-title{
    color:#111827 !important;
}

.section-subtitle{
    color:#64748b !important;
}

/* ================= NAVBAR ================= */

#ftco-navbar{
    background:#ffffff !important;
    border-bottom:1px solid rgba(<?php echo $mainColor; ?>,0.06);
    padding:22px 0;
    transition:.3s;
    position:fixed !important;
    top:0;
    left:0;
    right:0;
    z-index:9999 !important;
    box-shadow:0 10px 40px rgba(0,0,0,.04);
}

#ftco-navbar > .container{
    display:flex !important;
    flex-wrap:wrap;
    align-items:center;
    justify-content:space-between;
    gap:12px;
}

.navbar-brand{
    display:flex !important;
    align-items:center;
    gap:14px;
    color:#111827 !important;
    font-size:28px;
    font-weight:900;
    letter-spacing:-1px;
    text-decoration:none !important;
}

.logo-box{
    width:58px;
    height:58px;
    border-radius:18px;
    background:#fff;
    overflow:hidden;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 10px 30px rgba(<?php echo $mainColor; ?>,0.10);
    flex:none;
}

.logo-box img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.brand-mini{
    display:block;
    font-size:11px;
    color:var(--main-color) !important;
    font-weight:800;
    letter-spacing:2px;
    margin-bottom:2px;
    text-transform:uppercase;
}

.navbar-dark .navbar-nav .nav-link{
    color:#111827 !important;
    font-size:15px;
    font-weight:700;
    padding:14px 18px !important;
    border-radius:999px;
    transition:.3s;
}

.navbar-dark .navbar-nav .nav-link:hover{
    color:var(--main-color) !important;
    background:rgba(<?php echo $mainColor; ?>,0.08);
}

.navbar-dark .navbar-nav .active>.nav-link{
    color:var(--main-color) !important;
}

/* ================= BUTTON ================= */

.btn-book{
    background:#111827 !important;
    color:#ffffff !important;
    border-radius:999px !important;
    padding:14px 30px !important;
    margin-left:10px;
    box-shadow:0 15px 35px rgba(0,0,0,.18);
    min-width:135px;
    text-align:center !important;
    display:inline-flex !important;
    align-items:center !important;
    justify-content:center !important;
    font-size:14px !important;
    font-weight:900 !important;
    line-height:1 !important;
    height:auto !important;
    text-transform:uppercase;
    white-space:nowrap;
    opacity:1 !important;
    visibility:visible !important;
}

.navbar-dark .navbar-nav .nav-link.btn-book{
    color:#ffffff !important;
}

.navbar-dark .navbar-nav .nav-link.btn-book:hover{
    color:#ffffff !important;
    background:var(--main-color) !important;
    transform:translateY(-2px);
}

.btn-book:hover{
    background:var(--main-color) !important;
    transform:translateY(-2px);
}

/* ================= MOBILE ================= */

.navbar-toggler{
    width:46px;
    height:46px;
    padding:0;
    border:1px solid rgba(0,0,0,0.08);
    border-radius:10px;
    background:#ffffff;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 2px 6px rgba(0,0,0,.06);
    transition:background .2s ease, border-color .2s ease;
}

.navbar-toggler:hover,
.navbar-toggler:focus{
    background:#f9fafb;
    border-color:rgba(0,0,0,0.18);
    outline:none;
    box-shadow:0 2px 8px rgba(0,0,0,.10);
}

.navbar-toggler i{
    color:#0f172a;
    font-size:20px;
    line-height:1;
    font-weight:normal !important;
}

/* Fallback hamburger (3 lineas) si Font Awesome no carga */
.navbar-toggler .toggler-fallback{
    display:inline-block;
    width:22px;
    height:16px;
    position:relative;
}
.navbar-toggler .toggler-fallback span{
    position:absolute;
    left:0;
    right:0;
    height:2px;
    background:#0f172a;
    border-radius:2px;
}
.navbar-toggler .toggler-fallback span:nth-child(1){ top:0; }
.navbar-toggler .toggler-fallback span:nth-child(2){ top:7px; }
.navbar-toggler .toggler-fallback span:nth-child(3){ top:14px; }

/* ================= LANG SWITCHER ================= */

.lang-switcher{
    display:flex;
    align-items:center;
    gap:6px;
    margin-left:10px;
}

.lang-btn{
    display:inline-flex !important;
    align-items:center;
    gap:4px;
    padding:8px 14px !important;
    border-radius:999px !important;
    font-size:13px !important;
    font-weight:800 !important;
    color:#111827 !important;
    background:rgba(0,0,0,0.05) !important;
    text-decoration:none;
    transition:.25s;
    border:1.5px solid transparent;
}

.lang-btn:hover{
    background:rgba(<?php echo $mainColor; ?>,0.12) !important;
    color:var(--main-color) !important;
    border-color:rgba(<?php echo $mainColor; ?>,0.20);
}

.lang-btn.lang-active{
    background:var(--main-color) !important;
    color:#fff !important;
    border-color:var(--main-color);
}

@media(max-width:991px){

    .lang-switcher{
        margin-left:0;
        margin-top:8px;
        justify-content:center;
    }

}

@media(max-width:991px){

    body{
        padding-top:80px !important;
    }

    #ftco-navbar{
        padding:10px 0;
    }

    #ftco-navbar > .container{
        flex-wrap:nowrap;
    }

    .navbar-brand{
        font-size:15px;
        flex:1 1 auto;
        min-width:0;
        max-width:calc(100% - 60px);
        overflow:hidden;
    }

    .navbar-brand > div:last-child{
        min-width:0;
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    .navbar-toggler{
        flex:0 0 auto;
        margin-left:auto;
    }

    .navbar-collapse{
        flex-basis:100%;
    }

    .logo-box{
        width:42px;
        height:42px;
        border-radius:12px;
    }

    .brand-mini{
        font-size:10px !important;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .navbar-collapse{
        background:#fff;
        margin-top:18px;
        padding:18px;
        border-radius:25px;
        box-shadow:0 20px 50px rgba(0,0,0,.08);
    }

    .navbar-dark .navbar-nav .nav-link{
        margin-bottom:8px;
    }

    .btn-book{
        margin-left:0;
        margin-top:10px;
        text-align:center;
    }

}

</style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar ftco-navbar-light" id="ftco-navbar">

<div class="container">
<a class="navbar-brand" href="<?php echo $base_url_safe; ?>">

    <div class="logo-box">
        <?php if (!empty($ticket_image)): ?>
        <img 
        src="<?php echo $base_url_safe; ?>../CF-SYSTEMS/storage/configuration/<?php echo $ticket_image_safe; ?>?v=<?php echo $version; ?>"
        onerror="this.onerror=null; this.src='<?php echo $base_url_safe; ?>img/car-1.jpg';"
        alt="<?php echo $title_safe; ?>"
        loading="lazy"
        decoding="async"
        >
        <?php else: ?>
        <i class="fa fa-car" style="font-size:24px;color:var(--main-color);"></i>
        <?php endif; ?>
    </div>

    <div>
        <?php if (!empty(trim($phone))): ?>
        <span class="brand-mini">
           <?php echo "PHONE: ".htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <?php else: ?>
        <span class="brand-mini">
           <?php echo __('rent_a_car'); ?>
        </span>
        <?php endif; ?>

        <?php echo strtoupper($title_safe); ?>
    </div>

</a>

<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-label="Menu" aria-controls="ftco-nav" aria-expanded="false">

    <span class="toggler-fallback" aria-hidden="true"><span></span><span></span><span></span></span>

</button>

<div class="collapse navbar-collapse" id="ftco-nav">

<ul class="navbar-nav ml-auto align-items-lg-center">

<li class="nav-item active">
    <a href="<?php echo $base_url_safe; ?>" class="nav-link">
        <?php echo __('nav_home'); ?>
    </a>
</li>

<li class="nav-item">
    <a href="<?php echo $base_url_safe; ?>cars.php" class="nav-link">
        <?php echo __('nav_cars'); ?>
    </a>
</li>

<li class="nav-item">
    <a href="<?php echo $base_url_safe; ?>about.php" class="nav-link">
        <?php echo __('nav_about'); ?>
    </a>
</li>

<li class="nav-item">
    <a href="<?php echo $base_url_safe; ?>services.php" class="nav-link">
        <?php echo __('nav_services'); ?>
    </a>
</li>

<li class="nav-item">
    <a href="<?php echo $base_url_safe; ?>contact.php" class="nav-link">
        <?php echo __('nav_contact'); ?>
    </a>
</li>

<?php if(isset($_SESSION['client_id']) && intval($_SESSION['client_id']) > 0):
    if(!class_exists('NotificationData')){
        @include_once __DIR__ . "/../../core/app/model/NotificationData.php";
        @include_once __DIR__ . "/../../core/app/model/NotificationPreferenceData.php";
        @include_once __DIR__ . "/../../core/controller/NotificationService.php";
    }
    $__cli_unread = class_exists('NotificationData') ? NotificationData::countUnread('client', intval($_SESSION['client_id'])) : 0;
?>
<li class="nav-item dropdown" id="cliNotifBellLi" style="position:relative;">
      <a href="#" class="nav-link" id="cliNotifBellLink" style="position:relative;" title="<?php echo __('notif_title'); ?>">
          <i class="fa fa-bell"></i>
          <span id="cliNotifBellBadge" style="position:absolute;top:6px;right:-2px;background:#e11d48;color:#fff;border-radius:50%;padding:0 6px;font-size:11px;font-weight:bold;line-height:16px;min-width:16px;text-align:center;<?php echo $__cli_unread > 0 ? '' : 'display:none;'; ?>"><?php echo intval($__cli_unread); ?></span>
      </a>
      <div id="cliNotifBellMenu" style="display:none;position:absolute;right:0;top:100%;width:340px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 8px 20px rgba(0,0,0,0.15);z-index:9999;">
          <div style="padding:10px 14px;border-bottom:1px solid #e5e7eb;font-weight:600;color:#111;"><?php echo __('notif_title'); ?></div>
          <div id="cliNotifBellList" style="max-height:340px;overflow-y:auto;color:#111;">
              <div style="padding:14px;text-align:center;color:#888;">…</div>
          </div>
          <div style="padding:8px 14px;border-top:1px solid #e5e7eb;text-align:center;">
              <a href="notifications.php" style="color:#1d4ed8;font-size:13px;"><?php echo __('notif_title'); ?></a>
              &nbsp;·&nbsp;
              <a href="notifications-preferences.php" style="color:#6b7280;font-size:13px;"><?php echo __('notif_preferences'); ?></a>
          </div>
      </div>
      <script>
      (function(){
          function cliEsc(s){ var d=document.createElement('div'); d.innerText=s||''; return d.innerHTML; }
          function cliRender(items, unread){
              var badge=document.getElementById('cliNotifBellBadge');
              if(unread>0){ badge.innerText=unread; badge.style.display='inline-block'; } else { badge.style.display='none'; }
              var box=document.getElementById('cliNotifBellList');
              if(!items||items.length===0){ box.innerHTML='<div style="padding:14px;text-align:center;color:#888;"><?php echo __('notif_empty'); ?></div>'; return; }
              box.innerHTML='';
              items.forEach(function(it){
                  var bg=it.read?'#fff':'#fef3c7';
                  var href = it.url && String(it.url).length > 0 ? it.url : 'notifications.php';
                  var html='<a href="'+cliEsc(href)+'" data-id="'+it.id+'" data-read="'+(it.read?1:0)+'" class="cli-notif-it" style="display:block;padding:10px 14px;border-bottom:1px solid #f3f4f6;text-decoration:none;color:#111;background:'+bg+';">'
                      +'<div style="font-weight:600;font-size:13px;">'+cliEsc(it.title)+'</div>'
                      +'<div style="font-size:12px;color:#555;margin-top:2px;">'+cliEsc((it.body||'').replace(/<[^>]+>/g,'').substring(0,90))+'</div>'
                      +'<div style="font-size:11px;color:#9ca3af;margin-top:2px;">'+cliEsc(it.created)+'</div></a>';
                  box.insertAdjacentHTML('beforeend', html);
              });
          }
          function cliLoad(){
              fetch('notification-action.php?opt=list&limit=8',{credentials:'same-origin'}).then(function(r){return r.json();}).then(function(j){
                  if(j&&j.ok) cliRender(j.items||[], j.unread||0);
              }).catch(function(){});
          }
          document.addEventListener('DOMContentLoaded', function(){
              cliLoad();
              setInterval(cliLoad, 60000);
              var link=document.getElementById('cliNotifBellLink');
              var menu=document.getElementById('cliNotifBellMenu');
              link.addEventListener('click', function(e){
                  e.preventDefault(); e.stopPropagation();
                  menu.style.display = (menu.style.display==='none' || !menu.style.display) ? 'block' : 'none';
                  if(menu.style.display==='block') cliLoad();
              });
              document.addEventListener('click', function(e){
                  if(!document.getElementById('cliNotifBellLi').contains(e.target)) menu.style.display='none';
              });
              document.getElementById('cliNotifBellList').addEventListener('click', function(e){
                  var a = e.target.closest('a.cli-notif-it');
                  if(!a) return;
                  var id = a.getAttribute('data-id');
                  var alreadyRead = a.getAttribute('data-read') === '1';
                  if(id && !alreadyRead){
                      try{
                          var fd = new FormData(); fd.append('id', id);
                          fetch('notification-action.php?opt=mark_read', {method:'POST', body:fd, credentials:'same-origin', keepalive:true});
                      }catch(err){}
                  }
              });
          });
      })();
      </script>
  </li>
<?php endif; ?>

<li class="nav-item lang-switcher">
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    $query = $_GET;
    $query['lang'] = 'es';
    $qs_es = http_build_query($query);
    $query['lang'] = 'en';
    $qs_en = http_build_query($query);
    ?>
    <a href="?<?php echo $qs_es; ?>" class="lang-btn <?php echo ($LANG === 'es') ? 'lang-active' : ''; ?>">
        🇩🇴 ES
    </a>
    <a href="?<?php echo $qs_en; ?>" class="lang-btn <?php echo ($LANG === 'en') ? 'lang-active' : ''; ?>">
        🇺🇸 EN
    </a>
</li>

</ul>

</div>

</div>

</nav>