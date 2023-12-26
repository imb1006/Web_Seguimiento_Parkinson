// Importar los módulos necesarios
const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');

const app = express(); // Crea aplicación Express

// Configura el middleware para CORS y bodyParser
app.use(cors());
app.use(bodyParser.json());

// Inicializa variables para almacenar los datos del Arduino y los comandos a enviar
let contP = 0;
let tiempo = 0;
let velocidad = 0;
let bloqueos = 0;
let Ptotal = 0;
let actividadMin = 0;
let velocidadMedia = 0;
let commandToSend = "";

app.post('/command', (req, res) => {
    commandToSend = req.body.command;
    console.log(`Comando recibido: ${commandToSend}`); // Verifica que se recibe el comando
    res.status(200).send(`Comando ${commandToSend} recibido`);
});

app.get('/command', (req, res) => {
    res.json({ command: commandToSend });
    commandToSend = ""; // Opcional: resetear el comando después de enviarlo
});

// Rutas para recibir cada tipo de dato
app.post('/contP', (req, res) => {
    contP = req.body.contP;
    res.status(200).send('Contador de Pasos recibido');
});

app.post('/tiempo', (req, res) => {
    tiempo = req.body.tiempo;
    res.status(200).send('Tiempo recibido');
});

app.post('/velocidad', (req, res) => {
    velocidad = req.body.velocidad;
    res.status(200).send('Velocidad recibida');
});

app.post('/bloqueos', (req, res) => {
    bloqueos = req.body.bloqueos;
    res.status(200).send('Bloqueos recibidos');
});

app.post('/Ptotal', (req, res) => {
    Ptotal = req.body.Ptotal;
    res.status(200).send('Total de Pasos recibido');
});

app.post('/actividadMin', (req, res) => {
    actividadMin = req.body.actividadMin;
    res.status(200).send('Actividad en Minutos recibida');
});

app.post('/velocidadMedia', (req, res) => {
    velocidadMedia = req.body.velocidadMedia;
    res.status(200).send('Velocidad Media recibida');
});

// Ruta para obtener todos los datos
app.get('/datos', (req, res) => {
    res.json({
        contP: contP,
        tiempo: tiempo,
        velocidad: velocidad,
        bloqueos: bloqueos,
        Ptotal: Ptotal,
        actividadMin: actividadMin,
        velocidadMedia: velocidadMedia
    });
});

// Ruta para recibir datos del Arduino
/*/app.post('/data', (req, res) => {
    dataFromArduino = req.body.data;
    console.log(`Datos de Arduino: ${dataFromArduino}`); // Para depuración
    res.status(200).send('Data received');
});

// Ruta para enviar datos al Arduino
app.post('/command', (req, res) => {
    commandToSend = req.body.command;
    console.log(`Comando recibido: ${commandToSend}`); // Imprime el comando recibido
    //ledStatus = commandToSend === '1' ? "on" : "off";
    //res.status(200).send(`LED turned ${ledStatus}`);
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
});*/

// Inicia el servidor en el puerto 3000
app.listen(3000, () => console.log('Server running on port 3000'));
