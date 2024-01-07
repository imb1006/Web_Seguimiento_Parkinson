<!DOCTYPE html>
<html lang="es" dir="ltr" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml" class="responsive">
<head>
    <meta charset="UTF-8">
    <title>Página de Inicio</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <?php
        // Verifica si la sesión ya está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        };
        echo "<script type='text/javascript'>\n";
        echo "var userId = '" . $_SESSION['user_id'] . "';\n";
        echo "</script>\n";
    ?>

    <script>

        function iniciarActividadYRedirigir() {
            fetch(`http://localhost:3000/datosPaciente?id_paciente=${userId}`)
                .then(response => response.json())
                .then(datosPaciente => {
                    console.log(datosPaciente); // Verificar datos en consola
                    enviarDatosArduino(datosPaciente);
                })
                .then(() => {
                    window.location.href = '../common/actividad.php'; // Redirige a actividad.php
                })
                .catch(error => console.error('Error:', error));
        }

        function enviarDatosArduino(datosPaciente) {
            fetch('http://localhost:3000/command', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ command: `altura:${datosPaciente.altura},sexo:${datosPaciente.sexo}` })
            })
            .then(response => response.text())
            .then(data => console.log(data))
            .catch(error => console.error('Error:', error));
        }

</script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
            background: linear-gradient(135deg, rgba(174, 214, 241, 0.3), rgba(250, 219, 216, 0.3), rgba(245, 183, 177, 0.3), rgba(210, 180, 222, 0.3));
            background-blend-mode: overlay;
            font-family: Arial, sans-serif;
        }

        .content {
            text-align: center;
            position: absolute;
            width: 100%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .welcome-message {
            color: #333;
            font-size: 24px;
            margin-bottom: 40px;
        }
        
        .info-paciente {
            background-color: #fff;
            padding: 20px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: auto;
            max-width: 80%;
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
    include 'menu.php';

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

    // Obtener información del paciente
    $id_paciente = $_SESSION['user_id'];

    // Consulta SQL
    $sql = "SELECT p.altura, p.sexo, u.correo_electronico
        FROM pacientes p
        JOIN usuarios u ON p.id_paciente = u.id_usuario
        WHERE p.id_paciente = $id_paciente";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Obtener datos del paciente
        $row = $result->fetch_assoc();
        $altura = $row['altura'];
        $sexo = $row['sexo'];
        $email = $row['correo_electronico'];
        
        // Obtener todos los profesionales asignados
        $profesionales = [];
        $profesionalQuery = "SELECT nombre, apellidos FROM usuarios WHERE tipo_usuario = 'profesional'";
        $profesionalResult = $conn->query($profesionalQuery);

        while($profesionalRow = $profesionalResult->fetch_assoc()) {
            $nombreCompleto = $profesionalRow['nombre'] . ' ' . $profesionalRow['apellidos'];
            $profesionales[] = $nombreCompleto;
        }
    } else {
        echo "No se encontraron datos del paciente.";
    }

    $conn->close();

    ?>

    <div class="content">
        <div class="welcome-message">
            Bienvenido - <?php echo $_SESSION['nombre']; ?> <?php echo $_SESSION['apellidos']; ?> 
        </div>
        <div class="info-paciente">
            <h3>Información Personal</h3>
            <p>Sexo: <?php echo $sexo; ?></p>
            <p>Altura: <?php echo $altura; ?> cm</p>
            <p>Email: <?php echo $email; ?></p>
            <p>Profesional/es Asignado/s: <?php echo implode(", ", $profesionales); ?></p>
        </div>
        <div class="botones-actividades">
            <button type="submit" onclick="iniciarActividadYRedirigir()">Realizar Actividad</button>
            <button type="submit" onclick="location.href='../common/consultaActividades.php'">Actividades y Estadísticas</button>
        </div>
    </div>

<script src="../js/confirmacion.js"></script>

</body>
</html>