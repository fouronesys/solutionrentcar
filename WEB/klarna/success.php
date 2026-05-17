<?php
require_once "config.php";

$token = $_GET["token"] ?? "";

if($token != ""){
    $_SESSION["klarna_status"] = "approved";
    $_SESSION["klarna_token"] = $token;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Klarna Approved</title>
</head>
<body>
<script>
localStorage.setItem("klarna_status_<?php echo htmlspecialchars($token); ?>", "approved");

if(window.opener){
    window.opener.postMessage({
        type: "klarna_result",
        status: "approved",
        token: "<?php echo htmlspecialchars($token); ?>"
    }, "*");
}

document.body.innerHTML = "<h2>Pago aprobado por Klarna.</h2><p>Puede cerrar esta pestaña y confirmar la reservación.</p>";
setTimeout(function(){
    window.close();
}, 2000);
</script>
</body>
</html>