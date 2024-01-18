<!DOCTYPE html>
<html lang="es" dir="ltr" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml" class="responsive">


<body>
    <?php
    // Verifica si la sesión ya está iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    };
    echo "<script type='text/javascript'>\n";
    echo "var userType = '" . $_SESSION['user_type'] . "';\n";
    echo "</script>\n";
    ?>

    <section id="nav-bar">
        <nav class="navbar navbar-expand-lg bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Marchando con Párkinson</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../admin/inicioAdmin.php">Inicio</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                              Mi cuenta
                            </a>
                            <ul class="dropdown-menu">
                              <li><a class="dropdown-item" href="../common/actualizarCorreoHTML.php">Actualizar correo</a></li>
                              <li><a class="dropdown-item" href="../common/cambiarContraseñaHTML.php">Cambiar contraseña</a></li>
                              <li><hr class="dropdown-divider"></li>
                              <li><a class="dropdown-item" href="#" onclick="confirmarAccionConId('eliminarCuenta', <?php echo htmlspecialchars($_SESSION['user_id']); ?>)">Eliminar cuenta</a></li>
                            </ul>
                          </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="confirmarAccion('cerrarSesion')">Cerrar sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </section>

    <script src="../js/confirmacion.js"></script>

</body>
</html>
