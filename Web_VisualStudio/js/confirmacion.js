function confirmarAccion(accion) {
    let mensaje;
    let urlRedireccion;

    if (accion === 'eliminarCuenta') {
        mensaje = "¿Estás seguro de que deseas eliminar tu cuenta?";
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
        mensaje = "¿Estás seguro de que deseas cerrar sesión?";
        urlRedireccion = '../common/logout.php';
    }

    if (confirm(mensaje)) {
        window.location.href = urlRedireccion;
    }
}
