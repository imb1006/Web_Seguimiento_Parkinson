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
if (empty($_POST['newEmail']) || empty($_POST['confirm_email']) || empty($_POST['password'])) {
    $_SESSION['message'] = "Todos los campos son obligatorios.";
    header("Location: actualizarCorreoHTML.php");
    exit;
}

// Extraer datos del formulario
$newEmail = $_POST['newEmail'];
$confirmEmail = $_POST['confirm_email'];
$password = $_POST['password'];
$userId = $_SESSION['user_id'];


// Verificar que los correos electrónicos coincidan
if ($newEmail != $confirmEmail) {
    $_SESSION['message'] = "Los correos electrónicos no coinciden.";
    header("Location: actualizarCorreoHTML.php");
    exit;
}

// Verificar formato de correo electrónico
if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message'] = "Formato de correo electrónico inválido.";
    header("Location: actualizarCorreoHTML.php");
    exit;
}

// Verificar la contraseña actual
$sql = "SELECT contrasena FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($password !== $user['contrasena']) {
    $_SESSION['message'] = "Contraseña incorrecta.";
    header("Location: actualizarCorreoHTML.php");
    exit;
}

// Actualizar correo electrónico
$sql = "UPDATE usuarios SET correo_electronico = ? WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $newEmail, $userId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['message'] = "Correo electrónico actualizado con éxito.";

    if ($_SESSION['user_type'] === 'profesional') {
        $_SESSION['redirect'] = "../profesional/inicioProfesional.php"; // bandera para redirección
    } else if ($_SESSION['user_type'] === 'paciente') {
        $_SESSION['redirect'] = "../paciente/inicioPaciente.php"; // bandera para redirección
    } else if ($_SESSION['user_type'] === 'administrador') {
        $_SESSION['redirect'] = "../admin/inicioAdmin.php"; // bandera para redirección
    }
    
} else {
    $_SESSION['message'] = "Error al actualizar el correo electrónico.";
}

header("Location: actualizarCorreoHTML.php");
exit;

$conn->close();
?>
