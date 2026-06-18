<?php
if(isset($_SESSION["user_id"])){
  $inc = new IncidentData();
  $inc->stock_id = StockData::getPrincipal()->id;
  $inc->user_id = $_SESSION["user_id"];

  $inc->person_id = isset($_POST["person_id"]) ? intval($_POST["person_id"]) : "NULL";
  $inc->car_id = isset($_POST["car_id"]) ? intval($_POST["car_id"]) : "NULL";
  $inc->booking_id = isset($_POST["booking_id"]) ? intval($_POST["booking_id"]) : "NULL";
  $inc->maintenance_id = isset($_POST["maintenance_id"]) ? intval($_POST["maintenance_id"]) : "NULL";

  $inc->code = isset($_POST["code"]) ? trim($_POST["code"]) : "";
  $inc->title = isset($_POST["title"]) ? trim($_POST["title"]) : "";
  $inc->description = isset($_POST["description"]) ? trim($_POST["description"]) : "";
  $inc->category = isset($_POST["category"]) ? trim($_POST["category"]) : "";
  $inc->severity = isset($_POST["severity"]) ? trim($_POST["severity"]) : "LEVE";
  $inc->status = isset($_POST["status"]) ? trim($_POST["status"]) : "ABIERTO";
  $inc->cost = isset($_POST["cost"]) ? floatval($_POST["cost"]) : 0;
  $inc->due_date = isset($_POST["due_date"]) && $_POST["due_date"]!="" ? $_POST["due_date"] : NULL;

  if($inc->title!=""){
    $inc->add();
  }
  Core::redir("./?view=incidents&opt=all");
}
?>