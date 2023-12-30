function confirmarAccion(accion) {
    let mensaje;
    let urlRedireccion;

    if (accion === 'eliminarCuenta') {
        mensaje = "¿Quieres eliminar tu cuenta?";
        if (userType === 'administrador') {
            urlRedireccion = 'admin/eliminarCuenta.php';
        } else if (userType === 'profesional') {
            urlRedireccion = 'profesional/eliminarCuenta.php';
        } else if (userType === 'paciente') {
            urlRedireccion = 'paciente/eliminarCuenta.php';
        } else {
            urlRedireccion = '../common/logout.php'; // URL por defecto
        }
    } else if (accion === 'cerrarSesion') {
        mensaje = "¿Quieres cerrar sesión?";
        urlRedireccion = '../common/logout.php';
    } else if (accion === 'finalizarActividad') {
        mensaje = "¿Quieres finalizar la actividad?";
        if (confirm(mensaje)) {
            // Pregunta adicional para guardar o descartar los datos
            if (confirm("¿Deseas guardar los datos de la actividad?")) {
                // Lógica para guardar los datos
                console.log("Datos guardados");
                // Aquí puedes redirigir a la página de guardado o manejar el guardado de datos
            } else {
                // Lógica para descartar los datos
                console.log("Datos descartados");
                // Aquí puedes redirigir a otra página o manejar el descarte de datos
            }
            return; // Importante para evitar que se ejecute el último confirm
        }
    }

    if (mensaje && confirm(mensaje)) {
        window.location.href = urlRedireccion;
    }
}