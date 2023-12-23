const express = require('express');
const app = express();
const bodyParser = require('body-parser');

// Sirve archivos estáticos desde la carpeta 'public'
app.use(express.static('public'));

// Para archivos fuera del proyecto ('ArduinoServer')
//const cors = require('cors');
//app.use(cors());


app.use(bodyParser.json());

let dataFromArduino = "No data";

app.post('/data', (req, res) => {
    dataFromArduino = req.body.data;
    res.status(200).send('Data received');
});

app.get('/data', (req, res) => {
    res.json({ data: dataFromArduino });
});

app.listen(3000, () => console.log('Server running on port 3000'));
