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

        .tabla-pacientes {
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

    </style>
</head>

<body>
    
    <?php 
    include 'menu.php'; // Incluye el menú

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

    // Obtener información del profesional
    $id_profesional = $_SESSION['user_id'];

    // Consulta SQL para obtener los pacientes asignados al profesional
    $sql = "SELECT u.id_usuario, u.nombre, u.apellidos, u.correo_electronico
            FROM usuarios u 
            JOIN profesional_paciente pp ON u.id_usuario = pp.id_paciente 
            WHERE pp.id_profesional = $id_profesional";

    $result = $conn->query($sql);
    $pacientes = [];

    if ($result->num_rows > 0) {
        // Almacenar los nombres de los pacientes
        while($row = $result->fetch_assoc()) {
            $pacientes[] = $row;
        }
    } 

    $conn->close();
    ?>

    <div class="content">
        <div class="welcome-message">
            Inicio - <?php echo $_SESSION['nombre']; ?> <?php echo $_SESSION['apellidos']; ?> 
        </div>
        <div class="tabla-pacientes">
            <h3>Pacientes Asignados</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Correo Electrónico</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pacientes as $paciente): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($paciente['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['apellidos']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['correo_electronico']); ?></td>
                            <td>
                                <!-- Botón Consultar Paciente -->
                                <button type="button" onclick = "location.href='infoPaciente.php?id_paciente=<?php echo htmlspecialchars($paciente['id_usuario']); ?>'">Consultar Paciente</button>
                            </td>
                            <td>
                                <!-- Botón Eliminar -->
                                <button onclick="confirmarAccionConId('eliminarPacienteAsignado', <?php echo htmlspecialchars($paciente['id_usuario']); ?>)" class="icono-papelera">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php
                    if (empty($pacientes)) {
                        echo "<tr><td colspan='3'>No hay pacientes asignados a este profesional.</td></tr>";
                    } 
                    ?>
                </tbody>
            </table>
        </div>
        <div class="botones-actividades">
            <button type="button" onclick="location.href='mostrarPacientes.php?id_profesional=<?php echo $id_profesional; ?>'">Asignar Paciente Existente</button>
            <button type="button" onclick="location.href='nuevoPacienteHTML.php'">Crear Nuevo Paciente</button>
        </div>
    </div>

</body>
</html>