<?php
if(isset($_SESSION["user_id"]) && isset($_GET["id"])){
  $t = TaskData::getById(intval($_GET["id"]));
  if($t){ $t->reopen(); }
  Core::redir("./?view=tasks&opt=all");
}
?>