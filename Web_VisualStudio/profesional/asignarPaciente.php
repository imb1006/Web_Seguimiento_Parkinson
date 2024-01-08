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

// Asegurarse de que el ID del paciente a asignar se ha proporcionado
if (!isset($_GET['id_paciente'])) {
    echo "Error: No se ha proporcionado ID de paciente.";
    exit;
}

$id_paciente = $_GET['id_paciente'];
$id_profesional = $_SESSION['user_id'];

// Ejecutar la consulta SQL
$sql = "INSERT INTO profesional_paciente (id_profesional, id_paciente) VALUES ('$id_profesional', '$id_paciente')";
if ($conn->query($sql) === TRUE) {
    echo "Paciente asignado con éxito.";
    // Después de crear el usuario con éxito
    $_SESSION['message'] = "Paciente asignado con éxito.";
    $_SESSION['redirect'] = "mostrarPacientes.php"; // Nueva bandera para redirección
    header("Location: mostrarPacientes.php");
    exit;
} else {
    $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
    header("Location: mostrarPacientes.php"); // Redirige de nuevo al formulario
    exit;
}

$conn->close();
?>
