<!DOCTYPE html>
<html lang="es" dir="ltr" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml" class="responsive">
<head>
    <meta charset="UTF-8">
    <title>Página de Inicio</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
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
            margin-bottom: 20px;
        }
        

        .info-paciente {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
    $sql = "SELECT p.altura, p.sexo, u.correo_electronico, pp.id_profesional
        FROM pacientes p
        JOIN usuarios u ON p.id_paciente = u.id_usuario
        LEFT JOIN profesional_paciente pp ON p.id_paciente = pp.id_paciente
        WHERE p.id_paciente = $id_paciente";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Obtener datos del paciente
        $row = $result->fetch_assoc();
        $altura = $row['altura'];
        $sexo = $row['sexo'];
        $email = $row['correo_electronico'];
        $profesionales = [];


        // Obtener todos los profesionales asignados
        do {
            $id = $row['id_profesional'];
            $profesionalQuery = "SELECT nombre, apellidos FROM usuarios WHERE id_usuario = $id";
            $profesionalResult = $conn->query($profesionalQuery);

            if ($profesionalResult->num_rows > 0) {
                $profesionalRow = $profesionalResult->fetch_assoc();
                $nombreCompleto = $profesionalRow['nombre'] . ' ' . $profesionalRow['apellidos'];
                $profesionales[] = $nombreCompleto;
            }
        } while ($row = $result->fetch_assoc());
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
            <button type="submit" data-bs-toggle="modal" data-bs-target="#actividadModal">Iniciar Actividad</button>
            <button type="submit" onclick="location.href='../common/actividadesEstadisticas.php'">Actividades y Estadísticas</button>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="actividadModal" tabindex="-1" aria-labelledby="actividadModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="actividadModalLabel">Iniciar Actividad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Aquí van los campos -->
                    <form id="actividadForm">
                        <div class="mb-3">
                            <label for="distancia" class="form-label">Distancia</label>
                            <input type="text" class="form-control" id="distancia">
                        </div>
                        <div class="mb-3">
                            <label for="velocidad" class="form-label">Velocidad</label>
                            <input type="text" class="form-control" id="velocidad">
                        </div>
                        <div class="mb-3">
                            <label for="pasos" class="form-label">Pasos</label>
                            <input type="number" class="form-control" id="pasos">
                        </div>
                        <div class="mb-3">
                            <label for="bloqueos" class="form-label">Bloqueos</label>
                            <input type="number" class="form-control" id="bloqueos">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" onclick="confirmarAccion('finalizarActividad')">Finalizar Actividad</button>
                </div>
            </div>
        </div>
    </div>


    <!-- JavaScript para manejar el modal y el formulario -->
    <script>
        document.getElementById('actividadForm').addEventListener('submit', function(event) {
            event.preventDefault();
            // Aquí puedes agregar el código para manejar los datos del formulario
            console.log('Finalizar Actividad');
            // Cierra el modal
            var modalEl = document.getElementById('actividadModal');
            var modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
        });
    </script>



</body>
</html>