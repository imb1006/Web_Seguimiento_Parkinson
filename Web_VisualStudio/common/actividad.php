<!DOCTYPE html>
<html lang="es" dir="ltr" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml" class="responsive">
<head>
    <meta charset="UTF-8">
    <title>Página de Inicio</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>


    <script>
        function actualizarEstado() {
            fetch('http://localhost:3000/actividad')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('estadoActividad').innerText = data.estado;
                });
        }
        
        function sendCommand(command) {
            fetch('http://localhost:3000/command', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ command: command }),
            })
            .then(response => response.text())
            .then(data => {
                console.log(data); // Puedes mostrar un mensaje de confirmación si lo deseas
                //document.getElementById('statusDisplay').innerText = data;
            });
        }

        function getArduinoData() {
            fetch('http://localhost:3000/datos')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('contPData').innerText = data.contP;
                    document.getElementById('tiempoData').innerText = data.tiempo;
                    document.getElementById('velocidadData').innerText = data.velocidad;
                    document.getElementById('bloqueosData').innerText = data.bloqueos;
                    document.getElementById('PtotalData').innerText = data.Ptotal;
                    document.getElementById('actividadMinData').innerText = data.actividadMin;
                    document.getElementById('velocidadMediaData').innerText = data.velocidadMedia;
                });
        }    

        setInterval(getArduinoData, 1000); // Actualiza los datos cada segundo
        setInterval(actualizarEstado, 1000); // Actualiza el estado de la actividad cada segundo

    </script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
            background: linear-gradient(135deg, rgba(174, 214, 241, 0.3), rgba(250, 219, 216, 0.3), rgba(245, 183, 177, 0.3), rgba(210, 180, 222, 0.3));
            background-blend-mode: overlay;
            font-family: Arial, sans-serif;
        }

        .content {
            text-align: center;
            position: absolute;
            width: 100%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .welcome-message {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        button[type="submit"]  {
            margin: 10px;
            padding: 10px 20px;
            border: none;
            background-color: #79c3f5;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        button[type="submit"]:hover {
            background-color: #D2B4DE;
        }

        .info-actividad {
            background-color: #fff;
            padding: 20px;
            margin: 20px auto; /* Centra el recuadro */
            max-width: 400px; /* Ancho máximo */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center; /* Centra el texto */
        }


    </style>
</head>

<body>

    <div class="content">
        <div class="welcome-message">
            Bienvenido a tu Página de Inicio
        </div>
        <!-- Aquí puedes agregar más contenido según sea necesario -->
        <div id="arduinoMessage" class="info-actividad">
            <p>Pasos: <span id="contPData">Cargando...</span></p>
            <p>Tiempo: <span id="tiempoData">Cargando...</span></p>
            <p>Velocidad: <span id="velocidadData">Cargando...</span></p>
            <p>Bloqueos: <span id="bloqueosData">Cargando...</span></p>
            <p>Total de Pasos: <span id="PtotalData">Cargando...</span></p>
            <p>Actividad (min): <span id="actividadMinData">Cargando...</span></p>
            <p>Velocidad Media: <span id="velocidadMediaData">Cargando...</span></p>
        </div>
        <button type="submit" onclick="sendCommand('1')">Iniciar Actividad</button>
        <button type="submit" onclick="sendCommand('0')">Finalizar Actividad</button>
    </div>

</body>

</html>
