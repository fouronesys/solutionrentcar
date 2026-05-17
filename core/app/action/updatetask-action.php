<?php
if(isset($_SESSION["user_id"]) && isset($_POST["id"])){
  $t = TaskData::getById(intval($_POST["id"]));
  if($t){
    $t->user_id = $_SESSION["user_id"];
    $t->title = isset($_POST["title"]) ? trim($_POST["title"]) : $t->title;
    $t->description = isset($_POST["description"]) ? trim($_POST["description"]) : $t->description;
    $t->priority = isset($_POST["priority"]) ? trim($_POST["priority"]) : $t->priority;
    $t->status = isset($_POST["status"]) ? trim($_POST["status"]) : $t->status;
    $t->due_date = (isset($_POST["due_date"]) && $_POST["due_date"]!="") ? $_POST["due_date"] : NULL;
    $t->update();
  }
  Core::redir("./?view=tasks&opt=all");
}
?>