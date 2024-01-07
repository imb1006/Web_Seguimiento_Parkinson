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

// Asegurarse de que el ID del usuario a eliminar se ha proporcionado
if (!isset($_GET['id_usuario'])) {
    echo "Error: No se ha proporcionado ID de usuario.";
    exit;
}

$id_usuario = $_GET['id_usuario'];

// Obtener el tipo de usuario
$sql_tipo = "SELECT tipo_usuario FROM usuarios WHERE id_usuario = '$id_usuario'";
$resultado_tipo = $conn->query($sql_tipo);

if ($resultado_tipo->num_rows > 0) {
    $fila = $resultado_tipo->fetch_assoc();
    $tipo_usuario = $fila['tipo_usuario'];

    if ($tipo_usuario == 'administrador') {
        // Eliminar usuario administrador
        $sql_eliminar = "DELETE FROM usuarios WHERE id_usuario = '$id_usuario'";
        $conn->query($sql_eliminar);

    } elseif ($tipo_usuario == 'profesional') {
        // Obtener los pacientes del profesional a eliminar
        $sql_pacientes = "SELECT id_paciente FROM profesional_paciente WHERE id_profesional = '$id_usuario'";
        $resultado_pacientes = $conn->query($sql_pacientes);
    
        while ($paciente = $resultado_pacientes->fetch_assoc()) {
            // Encontrar al profesional con menos pacientes
            $sql_min_profesional = "SELECT u.id_usuario as id_profesional, IFNULL(COUNT(pp.id_paciente), 0) as num_pacientes
                                    FROM usuarios u
                                    LEFT JOIN profesional_paciente pp ON u.id_usuario = pp.id_profesional
                                    WHERE u.tipo_usuario = 'profesional' AND u.id_usuario != '$id_usuario'
                                    GROUP BY u.id_usuario
                                    ORDER BY num_pacientes ASC, u.id_usuario ASC
                                    LIMIT 1";
            $resultado_min_profesional = $conn->query($sql_min_profesional);
            $profesional_min = $resultado_min_profesional->fetch_assoc();
            $id_profesional_min = $profesional_min['id_profesional'];
    
            // Reasignar el paciente al profesional con menos pacientes
            $id_paciente_actual = $paciente['id_paciente'];
            $sql_reasignar = "UPDATE profesional_paciente SET id_profesional = '$id_profesional_min' WHERE id_paciente = '$id_paciente_actual'";
            $conn->query($sql_reasignar);
        }
    
        // Eliminar usuario profesional
        $sql_eliminar = "DELETE FROM usuarios WHERE id_usuario = '$id_usuario'";
        $conn->query($sql_eliminar);
        
    } elseif ($tipo_usuario == 'paciente') {
        // Eliminar todas las actividades del paciente
        $sql_eliminar_actividades = "DELETE FROM actividades WHERE id_paciente = '$id_usuario'";
        $conn->query($sql_eliminar_actividades);

        // Eliminar relaciones profesional-paciente
        $sql_eliminar_relaciones = "DELETE FROM profesional_paciente WHERE id_paciente = '$id_usuario'";
        $conn->query($sql_eliminar_relaciones);

        // Eliminar paciente
        $sql_eliminar_paciente = "DELETE FROM pacientes WHERE id_paciente = '$id_usuario'";
        $conn->query($sql_eliminar_paciente);

        // Eliminar usuario
        $sql_eliminar_usuario = "DELETE FROM usuarios WHERE id_usuario = '$id_usuario'";
        $conn->query($sql_eliminar_usuario);
    }

    // Redirigir de vuelta a la página de administración
    header("Location: inicioAdmin.php");
} else {
    echo "Usuario no encontrado.";
}

$conn->close();
?>