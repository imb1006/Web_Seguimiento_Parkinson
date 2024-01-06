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

    $_SESSION['message'] = "Nuevo usuario creado con éxito. ID: " . $last_id;
    header("Location: crearUsuarioHTML.php"); // Redirige de nuevo al formulario
    exit;
} else {
    $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
    header("Location: crearUsuarioHTML.php"); // Redirige de nuevo al formulario
    exit;
}

// Lógica adicional para asignar pacientes a profesionales

$conn->close();
?>