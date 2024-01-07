<!DOCTYPE html>
<html lang="es">

<?php
session_start();

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
?>

<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <script src="../js/confirmacion.js"></script>

    <script>
        function mostrarCampos() {
            var tipoUsuario = document.getElementById("tipo_usuario").value;
            document.getElementById("camposPaciente").style.display = tipoUsuario === "paciente" ? "block" : "none";
        }
    </script>
    
    <style>
        body, html {
            height: 100%;
            margin: 0;
            background: linear-gradient(135deg, rgba(174, 214, 241, 0.3), rgba(250, 219, 216, 0.3), rgba(245, 183, 177, 0.3), rgba(210, 180, 222, 0.3));
            background-blend-mode: overlay;
            font-family: Arial, sans-serif;
            background-attachment: fixed;
        }

        .welcome-message {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .content {
            text-align: center;
            width: 100%;
            max-width: 800px; /* Ajusta según tus necesidades */
            margin: auto;
            padding: 20px;
        }

        .contenedor {
            background-color: #fff;
            padding: 20px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: auto;
            max-width: 80%;
        }

        input[type="text"], input[type="email"], input[type="password"], input[type="number"] {
            border-radius: 10px; /* Más redondeado */
            border: 1px solid #ccc; /* Borde gris claro */
            padding: 8px; /* Ajuste de padding para reducir el tamaño */
            width: 90%; /* Reducir el ancho */
            margin-bottom: 10px;
            background-color: #f7f7f7; /* Fondo grisáceo claro */
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); /* Sombra interior suave */
        }

        button[type="submit"]  {
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

        button[type="submit"]:hover {
            background-color: #D2B4DE;
        }

        .botones-formulario {
            text-align: center;
            margin-top: 20px;
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

    </style>
</head>
<body>
    <?php include 'menu.php'; // Incluye el menú ?>

    <?php
    // Conexión a la base de datos aquí
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "webparkinson";
    $conn = new mysqli($servername, $db_username, $db_password, $db_name);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consulta para obtener profesionales
    $sql = "SELECT id_usuario, nombre, apellidos FROM usuarios WHERE tipo_usuario = 'profesional'";
    $result = $conn->query($sql);
    $profesionales = [];

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $profesionales[] = $row;
        }
    }

    $conn->close();
    ?>

    <div class="content">
        <div class="welcome-message">Información general </div>
        <form action="crearUsuario.php" method="post" id="formCrearUsuario">
            <div class="contenedor">
                <div><input type="text" id="nombre" name="nombre" placeholder="Nombre" required></div>
                <div><input type="text" id="apellidos" name="apellidos" placeholder="Apellidos" required></div>
                <div><input type="email" id="email" name="email" placeholder="Correo Electrónico" required></div>
                <div><input type="email" id="confirm_email" name="confirm_email" placeholder="Repetir Correo Electrónico" required></div>
                <div><input type="password" id="password" name="password" placeholder="Contraseña" required></div>
                <div><input type="password" id="confirm_password" name="confirm_password" placeholder="Repetir Contraseña" required></div>
            </div>
            
            <div class="welcome-message">Tipo de usuario </div>
            <div class="contenedor">
                <label for="tipo_usuario">Tipo de Usuario</label>
                <select id="tipo_usuario" name="tipo_usuario" onchange="mostrarCampos()">
                    <option value="administrador">Administrador</option>
                    <option value="profesional">Profesional</option>
                    <option value="paciente">Paciente</option>
                </select>

                <!-- Campos para Paciente -->
                <div id="camposPaciente" style="display:none;">
                    <div><input type="number" id="altura" name="altura" placeholder="Altura (ej: 170)" required></div>
                    <div>
                        <label>Sexo:</label>
                        <label for="M">
                            <input type="radio" id="M" name="sexo" value="M">
                            Masculino
                        </label>
                        <label for="F">
                            <input type="radio" id="F" name="sexo" value="F">
                            Femenino
                        </label><br>                  
                    </div>
                    <div>
                        <label for="asignarProfesional">Asignar Profesional: </label>
                        <select id="asignarProfesional" name="asignarProfesional">
                            <option value="auto">Automático</option>
                            <?php foreach ($profesionales as $profesional): ?>
                                <option value="<?php echo $profesional['id_usuario']; ?>">
                                    <?php echo $profesional['nombre'] . " " . $profesional['apellidos']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

            </div>

            <!-- Botones -->
            <div class="botones-formulario">
                <button type="submit" onclick="confirmarAccion('descartarUsuario')">Descartar Cambios</button>
                <button type="submit" onclick="confirmarAccion('crearUsuario')">Guardar Cambios</button>
            </div>

        </form>
        
    </div>
</body>
</html>