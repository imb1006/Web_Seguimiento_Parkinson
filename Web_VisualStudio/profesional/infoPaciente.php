<?php 
// Establecer una cookie para el ID del paciente antes de cualquier salida HTML
if (isset($_GET['id_paciente'])) {
    setcookie('id_paciente', $_GET['id_paciente'], time() + 86400, "/"); // La cookie expira en 1 día
}
?>

<!DOCTYPE html>
<html lang="es" dir="ltr" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml" class="responsive">
<head>
    <meta charset="UTF-8">
    <title>Información del Paciente</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="../js/confirmacion.js"></script>

    <script>
        function iniciarActividadYRedirigir() {
            fetch(`http://localhost:3000/datosPaciente?id_paciente=<?php echo $_COOKIE['id_paciente']; ?>`)
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
    include 'menu.php'; // Asegúrate de que este es el menú correcto para esta página

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

    // Obtener el ID del paciente desde la URL
    $id_paciente = isset($_GET['id_paciente']) ? $_GET['id_paciente'] : 0;

    // Consulta SQL para obtener la información del paciente
    $sql = "SELECT p.altura, p.sexo, u.correo_electronico, u.nombre, u.apellidos
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
        $nombre = $row['nombre'];
        $apellidos = $row['apellidos'];
    } else {
        echo "No se encontraron datos del paciente.";
    }

    $conn->close();
    ?>

    <div class="content">
        <div class="welcome-message">
            Información de <?php echo $nombre . " " . $apellidos; ?> 
        </div>
        <div class="info-paciente">
            <p>Sexo: <?php echo $sexo; ?></p>
            <p>Altura: <?php echo $altura; ?> cm</p>
            <p>Email: <?php echo $email; ?></p>
        </div>
        <div class="botones-actividades">
            <button type="button" onclick="location.href='inicioProfesional.php'">Menú Pacientes</button>
            <button type="button" onclick="iniciarActividadYRedirigir()">Realizar Actividad</button>
            <button type="button" onclick="location.href='../common/consultaActividades.php?id_paciente=<?php echo $id_paciente; ?>'">Actividades y Estadísticas</button>
        </div>
    </div>

</body>
</html>