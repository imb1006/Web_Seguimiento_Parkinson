<?php

session_start(); // Iniciar sesión al comienzo del script


// Conexión a la base de datos
$servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "webparkinson";

// Crear conexión
$conn = new mysqli($servername, $db_username, $db_password, $db_name);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se recibieron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $userType = $_POST['userType'];

    // Validar campos vacíos
    if (empty($email) || empty($password) || empty($userType)) {
        echo "Por favor, complete todos los campos.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Validar formato de correo electrónico
        echo "Formato de correo electrónico no válido.";
    } else {
        // Preparar la consulta SQL
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo_electronico = ? AND contrasena = ? AND tipo_usuario = ?");
        $stmt->bind_param("sss", $email, $password, $userType);

        // Ejecutar la consulta
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Usuario encontrado
            $row = $result->fetch_assoc();
            
            // Almacenar información del usuario en la sesión
            $_SESSION['user_id'] = $row['id_usuario'];
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['apellidos'] = $row['apellidos'];
            $_SESSION['user_email'] = $email;
            $_SESSION['user_type'] = $row['tipo_usuario'];

            //Redireccionar según el tipo de usuario
            switch ($row['tipo_usuario']) {
                case 'administrador':
                    header('Location: ../admin/inicioAdmin.php');
                    break;
                case 'profesional':
                    header('Location: ../profesional/inicioProfesional.php');
                    break;
                case 'paciente':
                    header('Location: ../paciente/inicioPaciente.php');
                    break;
            }
        } else {
            // Usuario no encontrado o credenciales incorrectas -> salta una notificación
            echo "<script>alert('Usuario no encontrado o credenciales incorrectas.');</script>";
            echo "<script>setTimeout(function(){window.location.href='login.html'},500);</script>";
        }

        $stmt->close();
    }
}

$conn->close();
?>
