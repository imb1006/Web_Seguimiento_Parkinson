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
        mensaje = "¿Deseas guardar los datos de la actividad?";
        if (confirm(mensaje)) {
            // Los datos se almacenan en la base de datos y después las variables de arduino se resetean
            print("Datos guardados");
            return;
        } else {
            // Los datos de todas las variables se resetean y no se guardan
            print("Datos descartados");
            return;
        }
    }

    if (mensaje && confirm(mensaje)) {
        window.location.href = urlRedireccion;
    }
}