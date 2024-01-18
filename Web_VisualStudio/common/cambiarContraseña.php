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
if (empty($_POST['actual_password']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
    $_SESSION['message'] = "Todos los campos son obligatorios.";
    header("Location: cambiarContraseñaHTML.php");
    exit;
}

$actualPassword = $_POST['actual_password'];
$newPassword = $_POST['password'];
$confirmPassword = $_POST['confirm_password'];
$userId = $_SESSION['user_id'];

// Verificar que las nuevas contraseñas coincidan
if ($newPassword != $confirmPassword) {
    $_SESSION['message'] = "Las nuevas contraseñas no coinciden.";
    header("Location: cambiarContraseñaHTML.php");
    exit;
}

// Verificar la contraseña actual
$sql = "SELECT contrasena FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($actualPassword !== $user['contrasena']) {
    $_SESSION['message'] = "La contraseña actual es incorrecta.";
    header("Location: cambiarContraseñaHTML.php");
    exit;
}

// Actualizar contraseña
$sql = "UPDATE usuarios SET contrasena = ? WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $newPassword, $userId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['message'] = "Contraseña actualizada con éxito.";

    if ($_SESSION['user_type'] === 'profesional') {
        $_SESSION['redirect'] = "../profesional/inicioProfesional.php"; // bandera para redirección
    } else if ($_SESSION['user_type'] === 'paciente') {
        $_SESSION['redirect'] = "../paciente/inicioPaciente.php"; // bandera para redirección
    } else if ($_SESSION['user_type'] === 'administrador') {
        $_SESSION['redirect'] = "../admin/inicioAdmin.php"; // bandera para redirección
    }
    
} else {
    $_SESSION['message'] = "Error al actualizar la contraseña.";
}

header("Location: cambiarContraseñaHTML.php");
exit;

$conn->close();
?>
