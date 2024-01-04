function realizarRedireccion(userType) {
    let urlRedireccion;
    if (userType === 'administrador') {
        urlRedireccion = '../admin/inicioAdmin.php';
    } else if (userType === 'profesional') {
        urlRedireccion = '../profesional/inicioProfesional.php';
    } else if (userType === 'paciente') {
        urlRedireccion = '../paciente/inicioPaciente.php';
    } else {
        urlRedireccion = '../common/logout.php'; // O una página por defecto
    }
    window.location.href = urlRedireccion;
}

function confirmarAccion(accion) {
    let mensaje;
    let urlRedireccion;

    if (accion === 'eliminarCuenta') {
        mensaje = "¿Quieres eliminar tu cuenta?";
        realizarRedireccion(userType);
    } else if (accion === 'cerrarSesion') {
        mensaje = "¿Quieres cerrar sesión?";
        urlRedireccion = '../common/logout.php';
    } else if (accion === 'finalizarActividad') {
        mensaje = "¿Deseas guardar los datos de la actividad?";
        console.log('userType:', userType, 'userId:', userId);
        if (confirm(mensaje)) {
            // Los datos se almacenan en la base de datos y después las variables de arduino se resetean
            // Enviar solicitud al servidor para guardar los datos
            fetch('http://localhost:3000/guardarActividad', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ userId: userId })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Datos guardados", data);
                return;
            })
            .catch(error => {
                console.error("Error al guardar datos", error);
            });
            realizarRedireccion(userType); // Usuario redirigido a su página de inicio
            return;
        } else {
            // Los datos de todas las variables se resetean y no se guardan en la BD
            realizarRedireccion(userType); // Usuario redirigido a su página de inicio
            return;
        }
    } 

    if (mensaje && confirm(mensaje)) {
        window.location.href = urlRedireccion;
    }
}

function confirmarAccionConId(accion, id) {
    let mensaje;
    let urlRedireccion;

    if (accion === 'eliminarPacienteAsignado') {
        mensaje = "¿Estás seguro de que quieres eliminar este paciente asignado?";
        if (confirm(mensaje)) {
            urlRedireccion = '../profesional/quitarPaciente.php?id_paciente=' + id;
            return;
        } else {
            realizarRedireccion(userType); // Usuario redirigido a su página de inicio
            return;
        }
    }

    if (mensaje && confirm(mensaje)) {
        window.location.href = urlRedireccion;
    }
}