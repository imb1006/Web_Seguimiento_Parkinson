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

// Asegurarse de que el ID del paciente a quitar se ha proporcionado
if (!isset($_GET['id_paciente'])) {
    echo "Error: No se ha proporcionado ID de paciente.";
    exit;
}

$id_paciente = $_GET['id_paciente'];
$id_profesional = $_SESSION['user_id'];

// Eliminar la relación profesional-paciente
$sql_eliminar_relacion = "DELETE FROM profesional_paciente WHERE id_profesional = '$id_profesional' AND id_paciente = '$id_paciente'";
$conn->query($sql_eliminar_relacion);

// Comprobar si el paciente está asignado a algún otro profesional
$sql_comprobar = "SELECT * FROM profesional_paciente WHERE id_paciente = '$id_paciente'";
$resultado = $conn->query($sql_comprobar);

if ($resultado->num_rows == 0) {
    // El paciente no está asignado a ningún otro profesional
    // Eliminar actividades del paciente
    $sql_eliminar_actividades = "DELETE FROM actividades WHERE id_paciente = '$id_paciente'";
    $conn->query($sql_eliminar_actividades);

    // Eliminar paciente de la tabla de pacientes
    $sql_eliminar_paciente = "DELETE FROM pacientes WHERE id_paciente = '$id_paciente'";
    $conn->query($sql_eliminar_paciente);

    // Eliminar paciente de la tabla de usuarios
    $sql_eliminar_usuario = "DELETE FROM usuarios WHERE id_usuario = '$id_paciente'";
    $conn->query($sql_eliminar_usuario);
}

$conn->close();

// Redirigir de vuelta a la página de inicio del profesional
header("Location: inicioProfesional.php");
?>
