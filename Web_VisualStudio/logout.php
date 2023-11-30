<?php
session_start(); // Iniciar sesión

// Borrar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio o de inicio de sesión
header('Location: login.html');
exit;
?>
