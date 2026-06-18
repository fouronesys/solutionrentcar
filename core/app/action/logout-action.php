<?php
session_start();

// eliminar sesión de usuario
if(isset($_SESSION['user_id'])){

    $user = new SSData();
    $user->user_id = $_SESSION['user_id'];
    $user->update();

    unset($_SESSION['user_id']);
    unset($_SESSION['stock_id']);
}

// ❌ eliminar cookie de autenticación
setcookie("remember_token", "", time() - 3600, "/");

// ⚠️ IMPORTANTE:
// NO eliminar esta cookie
// setcookie("seen_login", "", time() - 3600, "/");  <-- NO HACER

session_destroy();

// redirigir al login
header("Location: /?login=1");
exit;
?>