<?php 
$place_start = $_POST["place_start"];
$place_end = $_POST["place_end"];
$date_at = $_POST["date_at"];
$date_end = $_POST["date_end"];
$time_pick = $_POST["time_pick"];

if($_POST["web_id"]=="/WEB/index.php"):
header('location:/WEB/main?place_start='.$place_start.'&place_end='.$place_end.'&date_at='.$date_at.'&date_end='.$date_end.'&time_pick='.$time_pick);
else:
header('location:/main?place_start='.$place_start.'&place_end='.$place_end.'&date_at='.$date_at.'&date_end='.$date_end.'&time_pick='.$time_pick);
endif;?>