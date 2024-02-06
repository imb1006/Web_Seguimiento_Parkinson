<!DOCTYPE html>
<html lang="es" dir="ltr" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml" class="responsive">
<head>
    <meta charset="UTF-8">
    <title>Página de Inicio del Profesional</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <script src="../js/confirmacion.js"></script>
    
    <style>
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
            width: 100%;
            top: 50%;
            left: 50%;
            overflow: auto;
        }

        .tabla-usuarios {
            background-color: #fff;
            padding: 20px;
            margin: 20px auto; /* Centra el recuadro */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: auto; /* Ancho automático basado en el contenido */
            max-width: 80%; /* Máximo ancho permitido */
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, .05);
        }

        .table {
            margin: auto; /* Centra la tabla dentro del recuadro */
            max-width: 100%; /* Máximo ancho de la tabla */
        }

        .icono-papelera {
            background: none;
            border: none;
            cursor: pointer;
            color: black; /* Color del icono */
        }

        .icono-papelera:hover {
            color: #555; /* Color del icono al pasar el mouse por encima */
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

    </style>
</head>

<body>
    
    <?php
    // Verifica si la sesión ya está iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }; 
    include 'menu.php'; // Incluye el menú para el administrador

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

    // Consulta SQL para obtener todos los usuarios
    $idUsuarioActual = $_SESSION['user_id']; 
    $sql = "SELECT id_usuario, nombre, apellidos, correo_electronico, tipo_usuario FROM usuarios WHERE id_usuario != $idUsuarioActual";    $result = $conn->query($sql);
    $usuarios = [];

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
    } else {
        echo "No hay usuarios registrados.";
    }

    $conn->close();
    ?>

    <div class="content">
        <div class="welcome-message">
            Panel de Administración - <?php echo $_SESSION['nombre']; ?> <?php echo $_SESSION['apellidos']; ?> 
        </div>
        <div class="tabla-usuarios">
            <h3>Usuarios Registrados</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Correo Electrónico</th>
                        <th>Tipo de Usuario</th>
                        <th colspan="2"></th> <!-- Columna para botones de acción y Listado Pacientes -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['apellidos']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['correo_electronico']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['tipo_usuario']); ?></td>
                            <td>
                                <?php if ($usuario['tipo_usuario'] == 'profesional'): ?>
                                    <!-- Botón Consultar Listado de Pacientes -->
                                    <button type="submit" onclick = "location.href='listadoPacientes.php?id_usuario=<?php echo htmlspecialchars($usuario['id_usuario']); ?>'">Listado Pacientes</button>
                                <?php endif; ?>
                                <!-- Botón Eliminar -->
                                <button onclick="confirmarAccionConId('eliminarUsuario', <?php echo htmlspecialchars($usuario['id_usuario']); ?>)" class="icono-papelera">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="botones-actividades">
            <button type="submit" onclick="location.href='crearUsuarioHTML.php'">Nuevo usuario</button>
        </div>
    </div>

</body>
</html>