<?php
session_start();
session_destroy(); // Destruye la sesión activa
header("Location: login.html"); // Redirige al login
exit;
?>
