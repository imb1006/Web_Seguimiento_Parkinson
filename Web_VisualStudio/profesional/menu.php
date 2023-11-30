<!DOCTYPE html>
<html lang="es" dir="ltr" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml" class="responsive" style="">

<body>
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
                            <a class="nav-link" href="inicioProfesionalHTML.php">Inicio</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                              Mi cuenta
                            </a>
                            <ul class="dropdown-menu">
                              <li><a class="dropdown-item" href="../actualizarCorreo.html">Actualizar correo</a></li>
                              <li><a class="dropdown-item" href="../cambioContraseña.html">Cambiar contraseña</a></li>
                              <li><hr class="dropdown-divider"></li>
                              <li><a class="dropdown-item" href="#" onclick="confirmarAccion('eliminarCuenta')">Eliminar cuenta</a></li>
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

    <?php
    session_start();
    echo "<script type='text/javascript'>\n";
    echo "var userType = '" . $_SESSION['user_type'] . "';\n";
    echo "</script>\n";
    ?>

    <script src="../confirmacion.js"></script>
</body>
</html>
