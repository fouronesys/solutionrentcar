<?php
require_once "config.php";

$token = $_GET["token"] ?? "";

if($token != ""){
    $_SESSION["klarna_status"] = "rejected";
    $_SESSION["klarna_token"] = $token;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Klarna Cancelled</title>
</head>
<body>
<script>
localStorage.setItem("klarna_status_<?php echo htmlspecialchars($token); ?>", "rejected");

if(window.opener){
    window.opener.postMessage({
        type: "klarna_result",
        status: "rejected",
        token: "<?php echo htmlspecialchars($token); ?>"
    }, "*");
}

document.body.innerHTML = "<h2>Klarna no fue aprobado.</h2><p>Use WhatsApp / Pay later.</p>";
setTimeout(function(){
    window.close();
}, 2500);
</script>
</body>
</html>