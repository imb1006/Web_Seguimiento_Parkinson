<!DOCTYPE html>
<html lang="es" dir="ltr" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml" class="responsive">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Actividades</title>

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
            margin-bottom: 20px;
        }

        .content {
            text-align: center;
            width: 100%;
            top: 50%;
            left: 50%;
            overflow: auto;
        }

        .tabla-actividades {
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

    $actividades = [];
    $id_paciente = 0;

    if ($_SESSION['user_type'] == 'paciente') {
        $id_paciente = $_SESSION['user_id'];
        include '../paciente/menu.php'; // Incluye el menú
    } elseif ($_SESSION['user_type'] == 'profesional') {
        if (isset($_GET['id_paciente']) && is_numeric($_GET['id_paciente'])) {
            $id_paciente = $_GET['id_paciente'];
        }
        include '../profesional/menu.php'; // Incluye el menú
    }


    // Variables para estadísticas
    $totalBloqueos = 0;
    $totalVelocidadMedia = 0;
    $totalPasos = 0;
    $totalDuracion = 0;
    $contadorActividades = 0;

    // Consulta SQL para obtener las actividades del paciente
    $sql = "SELECT * FROM actividades WHERE id_paciente = $id_paciente";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $actividades[] = $row;
            $totalBloqueos += $row['numero_bloqueos'];
            $totalVelocidadMedia += $row['velocidad_media'];
            $totalPasos += $row['numero_pasos'];
            $totalDuracion += $row['duracion'];
            $contadorActividades++;
        }
    }

    // Cálculo de estadísticas
    $mediaBloqueos = $contadorActividades > 0 ? $totalBloqueos / $contadorActividades : 0;
    $mediaVelocidadMedia = $contadorActividades > 0 ? $totalVelocidadMedia / $contadorActividades : 0;
    $mediaPasos = $contadorActividades > 0 ? $totalPasos / $contadorActividades : 0;
    $mediaDuracion = $contadorActividades > 0 ? $totalDuracion / $contadorActividades : 0;


    $conn->close();
    ?>

    <div class="content">
        <div class="welcome-message">Estadísticas globales</div>
        <div class="tabla-actividades">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Total de Actividades</th>
                        <th>Media de Bloqueos</th>
                        <th>Media de Velocidad Media</th>
                        <th>Media de Pasos</th>
                        <th>Media de Duración</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $contadorActividades; ?></td>
                        <td><?php echo round($mediaBloqueos, 2); ?></td>
                        <td><?php echo round($mediaVelocidadMedia, 2); ?></td>
                        <td><?php echo round($mediaPasos, 2); ?></td>
                        <td><?php echo round($mediaDuracion, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="welcome-message">Tabla actividades </div>
        <div class="tabla-actividades">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Actividad</th>
                        <th>Número de Bloqueos</th>
                        <th>Velocidad Media</th>
                        <th>Número de Pasos</th>
                        <th>Duración</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($actividades as $actividad): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($actividad['id_actividad']); ?></td>
                        <td><?php echo htmlspecialchars($actividad['numero_bloqueos']); ?></td>
                        <td><?php echo htmlspecialchars($actividad['velocidad_media']); ?></td>
                        <td><?php echo htmlspecialchars($actividad['numero_pasos']); ?></td>
                        <td><?php echo htmlspecialchars($actividad['duracion']); ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php
                if (empty($actividades)) {
                    echo "<tr><td colspan='6'>No hay actividades registradas para este paciente.</td></tr>";
                } 
                ?>
                </tbody>
            </table>
        </div>
        <div class="botones">
            <?php
            // Asegúrate de que la sesión está iniciada
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Verifica el tipo de usuario y muestra los botones correspondientes
            if (isset($_SESSION['user_type'])) {
                if ($_SESSION['user_type'] == 'paciente') {
                    echo '<button type="submit" onclick="location.href= \'../paciente/inicioPaciente.php\'">Volver a Inicio</button>';
                } elseif ($_SESSION['user_type'] == 'profesional') {
                    echo '<button type="submit" onclick="location.href=\'../profesional/inicioProfesional.php\'">Volver a Inicio</button>';
                    // Asegúrate de tener el id_paciente para redirigir correctamente
                    if (isset($_GET['id_paciente'])) {
                        $id_paciente = $_GET['id_paciente'];
                        echo '<button type="submit" onclick="location.href=\'../profesional/infoPaciente.php?id_paciente=' . $id_paciente . '\'">Información del Paciente</button>';
                    }
                }
            }
            ?>

        </div>
    </div>

</body>
</html>