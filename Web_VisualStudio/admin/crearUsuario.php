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

// Verificar si el correo electrónico ya existe
$sql = "SELECT * FROM usuarios WHERE correo_electronico = '$email'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $_SESSION['message'] = "El correo electrónico ya está en uso.";
    header("Location: crearUsuarioHTML.php");
    exit;
}

// Insertar usuario en la base de datos
$sql = "INSERT INTO usuarios (nombre, apellidos, correo_electronico, contrasena, tipo_usuario) VALUES ('$nombre', '$apellidos', '$email', '$password', '$tipo_usuario')";
if ($conn->query($sql) === TRUE) {
    $last_id = $conn->insert_id;

    // Si el usuario es un paciente, insertar en la tabla 'pacientes'
    if ($tipo_usuario == 'paciente') {
        $altura = $_POST['altura'];
        $sexo = $_POST['sexo'];

        $sql_paciente = "INSERT INTO pacientes (id_paciente, altura, sexo) VALUES ('$last_id', '$altura', '$sexo')";
        $conn->query($sql_paciente);

        // Asignar paciente a profesional
        $asignarProfesional = $_POST['asignarProfesional'];
        if ($asignarProfesional == 'auto') {
            // Seleccionar un profesional aleatoriamente
            $sql_profesional = "SELECT id_usuario FROM usuarios WHERE tipo_usuario = 'profesional' ORDER BY RAND() LIMIT 1";
            $resultado = $conn->query($sql_profesional);
            $fila = $resultado->fetch_assoc();
            $id_profesional = $fila['id_usuario'];
        } else {
            // Usar el profesional seleccionado
            $id_profesional = $asignarProfesional;
        }

        // Insertar en la tabla 'profesional_paciente'
        $sql_prof_pac = "INSERT INTO profesional_paciente (id_profesional, id_paciente) VALUES ('$id_profesional', '$last_id')";
        $conn->query($sql_prof_pac);
    }

    // Después de crear el usuario con éxito
    $_SESSION['message'] = "Nuevo usuario creado con éxito. ID: " . $last_id;
    $_SESSION['redirect'] = "inicioAdmin.php"; // Nueva bandera para redirección
    header("Location: crearUsuarioHTML.php");
    exit;
} else {
    $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
    header("Location: crearUsuarioHTML.php"); // Redirige de nuevo al formulario
    exit;
}

$conn->close();
?>