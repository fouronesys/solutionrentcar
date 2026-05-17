<?php
if(isset($_SESSION["user_id"]) && isset($_GET["id"])){
  $days = isset($_GET["days"]) ? intval($_GET["days"]) : 3;
  $t = TaskData::getById(intval($_GET["id"]));
  if($t){ $t->postpone($days); }
  Core::redir("./?view=tasks&opt=all");
}
?>