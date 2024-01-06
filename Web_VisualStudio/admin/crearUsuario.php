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

// Validar que todos los campos estén completos
if (empty($_POST['nombre']) || empty($_POST['apellidos']) || empty($_POST['email']) || empty($_POST['confirm_email']) || empty($_POST['password']) || empty($_POST['confirm_password']) || empty($_POST['tipo_usuario'])) {
    $_SESSION['message'] = "Todos los campos son obligatorios.";
    header("Location: crearUsuarioHTML.php"); // Redirige de nuevo al formulario
    exit;
}

// Extraer datos del formulario
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$email = $_POST['email'];
$confirm_email = $_POST['confirm_email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$tipo_usuario = $_POST['tipo_usuario'];

// Verificar correos electrónicos y contraseñas
if ($email != $confirm_email) {
    $_SESSION['message'] = "Los correos electrónicos no coinciden.";
    header("Location: crearUsuarioHTML.php"); // Redirige de nuevo al formulario
    exit;
}
if ($password != $confirm_password) {
    $_SESSION['message'] = "Las contraseñas no coinciden.";
    header("Location: crearUsuarioHTML.php"); // Redirige de nuevo al formulario
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message'] = "Formato de correo electrónico inválido.";
    header("Location: crearUsuarioHTML.php"); // Redirige de nuevo al formulario
    exit;
}

// Verificar si el usuario ya existe
$sql = "SELECT * FROM usuarios WHERE nombre = '$nombre' AND apellidos = '$apellidos' AND correo_electronico = '$email' AND tipo_usuario = '$tipo_usuario'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $_SESSION['message'] = "Ya existe un usuario con estos datos.";
    header("Location: crearUsuarioHTML.php"); // Redirige de nuevo al formulario
    exit;
}

// Insertar usuario en la base de datos
$sql = "INSERT INTO usuarios (nombre, apellidos, correo_electronico, contrasena, tipo_usuario) VALUES ('$nombre', '$apellidos', '$email', '$password', '$tipo_usuario')";
if ($conn->query($sql) === TRUE) {
    $last_id = $conn->insert_id;

    // Si el usuario es un profesional y se seleccionó la opción "auto"
    if ($tipo_usuario == 'profesional' && $_POST['asignarPacientes'] == "auto") {
        // Calcular el promedio de pacientes por profesional
        $sql = "SELECT AVG(paciente_count) as promedio FROM (SELECT COUNT(id_paciente) as paciente_count FROM profesional_paciente GROUP BY id_profesional) as subquery";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $promedioPacientes = ceil($row['promedio']);

        // Encuentra profesionales con más pacientes que el promedio
        $sql = "SELECT id_profesional FROM profesional_paciente GROUP BY id_profesional HAVING COUNT(id_paciente) > $promedioPacientes";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $profesionalConMasPacientes = $row['id_profesional'];

            // Reasignar un paciente de ese profesional al nuevo profesional
            $sqlUpdate = "UPDATE profesional_paciente SET id_profesional = $last_id WHERE id_profesional = $profesionalConMasPacientes ORDER BY RAND() LIMIT 1";
            $conn->query($sqlUpdate);

            // Verificar si ya se alcanzó el equilibrio
            $sqlCheck = "SELECT COUNT(id_paciente) as num_pacientes FROM profesional_paciente WHERE id_profesional = $last_id";
            $resultCheck = $conn->query($sqlCheck);
            $rowCheck = $resultCheck->fetch_assoc();
            if ($rowCheck['num_pacientes'] >= $promedioPacientes) {
                break; // Salir del bucle si se alcanza el promedio
            }
        }
    }


    $_SESSION['message'] = "Nuevo usuario creado con éxito. ID: " . $last_id;
    header("Location: crearUsuarioHTML.php"); // Redirige de nuevo al formulario
    exit;
} else {
    $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
    header("Location: crearUsuarioHTML.php"); // Redirige de nuevo al formulario
    exit;
}

// Lógica adicional para asignar pacientes a profesionales y viceversa


if ($tipo_usuario == 'paciente'){
    $altura = $_POST['altura'];
    $sexo = $_POST['asignarPacientes'];
    $asignar = $_POST['asignarProfesional'];



}

$conn->close();
?>