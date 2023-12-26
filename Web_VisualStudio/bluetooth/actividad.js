
<script src="../js/confirmacion.js"></script>


function actualizarDatosActividad() {
    fetch('http://localhost:3000/data')
        .then(response => response.json())
        .then(data => {
            if (data.data === 'IZQUIERDA') {
                // Mostrar alerta izquierda parpadeante
                document.getElementById('datosActividad').innerHTML = '<span class="parpadeo">IZQUIERDA</span>';
            } else {
                // Mostrar datos normales
                document.getElementById('datosActividad').innerHTML = data.data;
            }
        });
}

// Agregar clase para parpadeo
const style = document.createElement('style');
style.innerHTML = `
    .parpadeo {
        animation: parpadeo 1s infinite;
    }
    @keyframes parpadeo {  
        50% { opacity: 0; }
    }
`;
document.head.appendChild(style);


function iniciarActividad() {
    fetch('http://localhost:3000/start-activity', { method: 'POST' })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            setInterval(actualizarDatosActividad, 1000); // Actualizar cada segundo
        })

        .catch(error => {
            console.error("Error al iniciar la actividad:", error);
        });
}

function finalizarActividad() {
    fetch('http://localhost:3000/stop-activity', { method: 'POST' })
        .then(response => response.text())
        .then(data => console.log(data));
        
        confirmarAccion('finalizarActividad'); // Llamar a la función confirmarAccion
}


