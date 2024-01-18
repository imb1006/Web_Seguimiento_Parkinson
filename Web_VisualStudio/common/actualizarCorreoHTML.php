<!DOCTYPE html>
<html lang="es" dir="ltr" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml" class="responsive">

<?php
session_start();
?>

<head>
    <meta charset="UTF-8">
    <title>Actualización de Correo Electrónico</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <script src="../js/confirmacion.js"></script>

    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById('password');
            var passwordIcon = document.querySelector('.password-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
    </script>

    <style>
         /* Centrar verticalmente */
         html, body {
            height: 100%;
            margin: 0;
            background: linear-gradient(135deg, rgba(174, 214, 241, 0.3), rgba(250, 219, 216, 0.3), rgba(245, 183, 177, 0.3), rgba(210, 180, 222, 0.3));
            background-blend-mode: overlay;
            font-family: Arial, sans-serif;
        }

        .welcome-message {
            color: #333;
            font-size: 24px;
            margin-bottom: 40px;
        }

        .content {
            text-align: center;
            position: absolute;
            width: 100%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .contenedor {
            background-color: #fff;
            padding: 20px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: auto;
            max-width: 50%;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            border-radius: 10px; /* Más redondeado */
            border: 1px solid #ccc; /* Borde gris claro */
            padding: 8px; /* Ajuste de padding para reducir el tamaño */
            width: 90%; /* Ajustar el ancho dentro del contenedor blanco */
            margin-bottom: 10px;
            background-color: #f7f7f7; /* Fondo grisáceo claro */
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); /* Sombra interior suave */
        }

        input[type="password"] {
            padding-right: 30px; /* Espacio para el ícono del ojo */
        }

        button[type="button"]  {
            margin: 10px;
            padding: 10px 20px;
            border: none;
            background-color: #79c3f5;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        button[type="button"]:hover {
            background-color: #D2B4DE;
        }

        .btn-style {
            margin: 10px;
            padding: 10px 20px;
            border: none;
            background-color: #79c3f5;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .btn-style:hover {
            background-color: #D2B4DE;
        }

        @media (max-width: 768px) {
            /* Ajustes para pantallas más pequeñas */
            .content {
                padding: 10px;
            }
        }

        .contenedor div {
            margin-bottom: 15px; /* Aumenta el espacio entre campos */
        }

        .form-field {
            position: relative;
            /* otros estilos */
        }

        .form-field .password-icon {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
        }

    </style>
</head>
<body>
    <?php

    // Manejo de redirección con mensaje de éxito
    if (isset($_SESSION['redirect'])) {
        echo "<script type='text/javascript'>
                alert('" . addslashes($_SESSION['message']) . "');
                window.location.href = '" . $_SESSION['redirect'] . "';
            </script>";
        unset($_SESSION['message']);
        unset($_SESSION['redirect']);
        exit; // Detiene la ejecución del script
    }


    // Mostrar mensaje si está establecido
    if (isset($_SESSION['message'])) {
        echo "<script>alert('" . addslashes($_SESSION['message']) . "');</script>";
        unset($_SESSION['message']);
    }

    if ($_SESSION['user_type'] === 'profesional') {
        include '../profesional/menu.php'; // Incluye el menú para profesionales
    } else if ($_SESSION['user_type'] === 'paciente') {
        include '../paciente/menu.php'; // Incluye el menú para pacientes
    } else if ($_SESSION['user_type'] === 'administrador') {
        include '../admin/menu.php'; // Incluye el menú para pacientes
    } else {
        header('Location: logout.php'); // Redirige a la página de cierre de sesión
        exit(); // Finaliza la ejecución del script
    }

    // Conexión a la base de datos
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "webparkinson";
    $conn = new mysqli($servername, $db_username, $db_password, $db_name);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $conn->close();
    ?>

    <div class="content">
        <div class="welcome-message">Actualizar Correo Electrónico</div>
        <form id="actualizarCorreo" action="actualizarCorreo.php" method="post">
            <div class="contenedor">
                <div><input type="email" id="newEmail" name="newEmail" placeholder="Nuevo Correo Electrónico" required></div>
                <div><input type="email" id="confirm_email" name="confirm_email" placeholder="Repetir Nuevo Correo Electrónico" required></div>
                <div class="form-field">
                    <input type="password" id="password" name="password" placeholder="Contraseña" required>
                    <i class="fas fa-eye password-icon" onclick="togglePasswordVisibility()"></i>
                </div>
            </div>
            <div>
                <button type="button" onclick="confirmarAccion('actualizarCorreo')">Aplicar Cambios</button>
            </div>
        </form>
    </div>

</body>
</html>
