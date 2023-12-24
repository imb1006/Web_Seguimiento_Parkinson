// Importar los módulos necesarios
const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');

const app = express(); // Crea aplicación Express

// Configura el middleware para CORS y bodyParser
app.use(cors());
app.use(bodyParser.json());

// Inicializa variables para almacenar los datos del Arduino y los comandos a enviar
let dataFromArduino = "No data";
let commandToSend = "";
let ledStatus = "off"; // Estado del LED

// Ruta para recibir datos del Arduino
app.post('/data', (req, res) => {
    dataFromArduino = req.body.data;
    res.status(200).send('Data received');
});

// Ruta para enviar datos al Arduino
app.post('/command', (req, res) => {
    commandToSend = req.body.command;
    console.log(`Comando recibido: ${commandToSend}`); // Imprime el comando recibido
    ledStatus = commandToSend === '1' ? "on" : "off";
    res.status(200).send(`LED turned ${ledStatus}`);
});

// Ruta para obtener los últimos datos del Arduino
app.get('/data', (req, res) => {
    res.json({ data: dataFromArduino });
});

// Ruta para obtener el último comando para el Arduino
app.get('/command', (req, res) => {
    res.json({ command: commandToSend });
    commandToSend = ""; //  resetear el comando después de enviarlo
});

app.get('/status', (req, res) => {
    res.json({ ledStatus: ledStatus });
});

// Ruta para iniciar la actividad (iniciar funcionamiento del MPU6050)
app.post('/start-activity', (req, res) => {
    commandToSend = 'START'; // Comando para iniciar actividad
    res.status(200).send('Activity started');
});

// Ruta para finalizar la actividad (finalizar funcionamiento del MPU6050)
app.post('/stop-activity', (req, res) => {
    commandToSend = 'STOP'; // Comando para finalizar actividad
    res.status(200).send('Activity stopped');
});

// Inicia el servidor en el puerto 3000
app.listen(3000, () => console.log('Server running on port 3000'));
