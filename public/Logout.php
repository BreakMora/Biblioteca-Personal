<?php
session_start();
session_destroy();
header('Location: Login.php'); // Redirige al login después de cerrar sesión
exit();
?>
