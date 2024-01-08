<!DOCTYPE html>
<html lang="es" dir="ltr" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml" class="responsive">
<head>
    <meta charset="UTF-8">
    <title>Listado de Pacientes</title>
    <!-- Incluye los mismos estilos y scripts que inicioAdmin.php -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
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
    session_start();
    include 'menu.php'; // Asegúrate de que este es el menú correcto para esta página

    // Conexión a la base de datos
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "webparkinson";
    $conn = new mysqli($servername, $db_username, $db_password, $db_name);

    $nombreProfesional = "";
    $pacientes = [];

    // Verificar si el ID del profesional se ha pasado a través de la URL
    if (isset($_GET['id_usuario'])) {
        $idProfesional = $_GET['id_usuario'];

        // Consulta para obtener el nombre del profesional
        $sqlNombre = "SELECT nombre, apellidos FROM usuarios WHERE id_usuario = $idProfesional";
        $resultadoNombre = $conn->query($sqlNombre);
        if ($resultadoNombre->num_rows > 0) {
            $filaNombre = $resultadoNombre->fetch_assoc();
            $nombreProfesional = $filaNombre['nombre'] . " " . $filaNombre['apellidos'];
        }

        // Consulta SQL para obtener los pacientes asignados al profesional
        $sqlPacientes = "SELECT p.id_paciente, u.nombre, u.apellidos, u.correo_electronico
                FROM pacientes p
                JOIN profesional_paciente pp ON p.id_paciente = pp.id_paciente
                JOIN usuarios u ON p.id_paciente = u.id_usuario
                WHERE pp.id_profesional = $idProfesional";
        $result = $conn->query($sqlPacientes);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $pacientes[] = $row;
            }
        }
    } else {
        echo "No se ha especificado un profesional.";
    }

    $conn->close();
    ?>

    <div class="content">
        <div class="welcome-message">
            Listado de Pacientes de <?php echo htmlspecialchars($nombreProfesional); ?> 
        </div>
        <div class="tabla-usuarios">
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
            <button type="submit" onclick="location.href='inicioAdmin.php'">Menú Usuarios</button>
        </div>
    </div>

</body>
</html>
