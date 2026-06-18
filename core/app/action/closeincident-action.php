<?php
if(isset($_SESSION["user_id"]) && isset($_GET["id"])){
  $inc = IncidentData::getById(intval($_GET["id"]));
  if($inc){
    $inc->close();
  }
  Core::redir("./?view=incidents&opt=all");
}
?>

