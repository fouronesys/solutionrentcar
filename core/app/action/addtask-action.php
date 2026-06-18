<?php
if(isset($_SESSION["user_id"])){
  $t = new TaskData();
  $t->stock_id = StockData::getPrincipal()->id;
  $t->user_id = $_SESSION["user_id"];
  $t->source_type = "MANUAL";
  $t->source_key  = "MANUAL";
  $t->ref_table = "";
  $t->ref_id = "NULL";

  $t->title = isset($_POST["title"]) ? trim($_POST["title"]) : "";
  $t->description = isset($_POST["description"]) ? trim($_POST["description"]) : "";
  $t->priority = isset($_POST["priority"]) ? trim($_POST["priority"]) : "MEDIA";
  $t->status = isset($_POST["status"]) ? trim($_POST["status"]) : "PENDIENTE";
  $t->due_date = (isset($_POST["due_date"]) && $_POST["due_date"]!="") ? $_POST["due_date"] : NULL;

  if($t->title!=""){ $t->add(); }
  Core::redir("./?view=tasks&opt=all");
}
?>
