// Importar los módulos necesarios
const express = require('express');
const mysql = require('mysql');
const bodyParser = require('body-parser');
const cors = require('cors');

const app = express(); // Crea aplicación Express
app.use(express.json());

// Configura el middleware para CORS y bodyParser
app.use(cors());
app.use(bodyParser.json());

// Configurar la conexión a la base de datos
const db = mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "webparkinson"
});

db.connect(err => {
    if (err) {
        console.error('Error al conectar a la base de datos:', err);
        return;
    }
    console.log('Conexión a la base de datos establecida');
});

// Inicializa variables para almacenar los datos del Arduino y los comandos a enviar
let contP = 0;
let tiempo = 0;
let velocidad = 0;
let bloqueos = 0;
let Ptotal = 0;
let actividadMin = 0;
let velocidadMedia = 0;
let estadoActividad = 'esperando'; // Estado inicial de la actividad
let commandToSend = "";
let izquierdaFlag = false; // Manejar bloqueos "IZQUIERDA"


// Ruta para recibir comandos
app.post('/command', (req, res) => {
    commandToSend = req.body.command;
    estadoActividad = commandToSend === '1' ? 'iniciada' : commandToSend === '0' ? 'finalizada' : estadoActividad;
    console.log(`Comando recibido: ${commandToSend}`); // Verifica que se recibe el comando
    res.status(200).send(`Comando ${commandToSend} recibido`);
});

// Ruta para enviar comandos
app.get('/command', (req, res) => {
    res.json({ command: commandToSend, estadoActividad: estadoActividad});
    //commandToSend = ""; // Opcional: resetear el comando después de enviarlo
});

// Nuevo endpoint para confirmar la recepción del comando
app.post('/confirmCommand', (req, res) => {
    if (req.body.received) {
        commandToSend = ""; // Resetea el comando solo después de la confirmación
        res.status(200).send('Comando confirmado y reseteado');
    } else {
        res.status(400).send('Confirmación no recibida');
    }
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

app.post('/izquierda', (req, res) => {
    izquierdaFlag = req.body.izquierda; // Recibe y actualiza el estado de "IZQUIERDA"
    res.status(200).send('Estado IZQUIERDA actualizado');
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
        velocidadMedia: velocidadMedia,
        izquierda: izquierdaFlag
    });
});

// Endpoint para guardar datos de actividad
app.post('/guardarActividad', (req, res) => {
    const idPaciente = req.body.userId;

    const query = `
        INSERT INTO actividades 
        (id_paciente, numero_bloqueos, velocidad_media, numero_pasos, duracion) 
        VALUES (?, ?, ?, ?, ?)
    `;

    db.query(query, [idPaciente, bloqueos, velocidadMedia, Ptotal, actividadMin], (error, results) => {
        if (error) {
            console.error('Error al insertar en la base de datos:', error);
            res.status(500).send('Error al guardar datos de actividad');
            return;
        }
        res.send('Datos de actividad guardados correctamente');
    });
});

// Nueva ruta para obtener datos del paciente
app.get('/datosPaciente', (req, res) => {
    const idPaciente = req.query.id_paciente;

    const query = `
        SELECT altura, sexo 
        FROM pacientes 
        WHERE id_paciente = ?`;

    db.query(query, [idPaciente], (error, results) => {
        if (error) {
            console.error('Error al obtener datos del paciente:', error);
            res.status(500).send('Error al obtener datos del paciente');
            return;
        }
        if (results.length > 0) {
            res.json(results[0]); // Envía los datos del paciente a Arduino
        } else {
            res.status(404).send('Paciente no encontrado');
        }
    });
});

// Inicia el servidor en el puerto 3000
app.listen(3000, () => console.log('Server running on port 3000'));
