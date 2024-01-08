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
if (empty($_POST['nombre']) || empty($_POST['apellidos']) || empty($_POST['email']) || empty($_POST['confirm_email']) || empty($_POST['password']) || empty($_POST['confirm_password']) ) {
    $_SESSION['message'] = "Todos los campos son obligatorios.";
    header("Location: nuevoPacienteHTML.php"); // Redirige de nuevo al formulario
    exit;
}

// Extraer datos del formulario
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$email = $_POST['email'];
$confirm_email = $_POST['confirm_email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$tipo_usuario = 'paciente';
$altura = $_POST['altura'];
$sexo = $_POST['sexo'];

// Insertar usuario en la base de datos
$sql = "INSERT INTO usuarios (nombre, apellidos, correo_electronico, contrasena, tipo_usuario) VALUES ('$nombre', '$apellidos', '$email', '$password', '$tipo_usuario')";
if ($conn->query($sql) === TRUE) {
    $last_id = $conn->insert_id;

    // Insertar el paciente en la base de datos
    $sql_paciente = "INSERT INTO pacientes (id_paciente, altura, sexo) VALUES ('$last_id', '$altura', '$sexo')";
    $conn->query($sql_paciente);

    // Asignar el paciente al profesional
    $id_profesional = $_POST['id_profesional'];
    $sql_asignar = "INSERT INTO profesional_paciente (id_profesional, id_paciente) VALUES ('$id_profesional', '$last_id')";
    $conn->query($sql_asignar);

    // Después de crear el usuario con éxito
    $_SESSION['message'] = "Nuevo paciente creado con éxito. ID: " . $last_id;
    $_SESSION['redirect'] = "inicioProfesional.php"; // Nueva bandera para redirección
    header("Location: nuevoPacienteHTML.php");
    exit;
} else {
    $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
    header("Location: nuevoPacienteHTML.php"); // Redirige de nuevo al formulario
    exit;
}

?>
