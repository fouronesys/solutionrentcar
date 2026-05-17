<?php
$con = Database::getCon();
$dbOk = false;
if ($con) {
    $r = @$con->query("SELECT 1 AS ok");
    if ($r && $r->fetch_assoc()) $dbOk = true;
}
ApiResponse::ok([
    'status' => 'ok',
    'db'     => $dbOk,
    'time'   => date('c'),
]);
