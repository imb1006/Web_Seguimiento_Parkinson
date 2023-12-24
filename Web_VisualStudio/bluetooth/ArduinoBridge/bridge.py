import serial
import requests

# Configura el puerto serie
ser = serial.Serial('COM6', 9600)  # Ajusta el puerto COM y el baudrate

while True:
    # Recibe datos de Arduino y los envía al servidor
    if ser.in_waiting:
        line = ser.readline().decode('utf-8').rstrip()
        requests.post('http://localhost:3000/data', json={'data': line})
    
    # Recibe comandos del servidor y los envía a Arduino
    response = requests.get('http://localhost:3000/command')
    if response.status_code == 200:
        data = response.json()['command']
        if data:  # Verifica que data no esté vacío
            print(f"Enviando: {data}")  # Imprime el dato a enviar
            ser.write(data.encode())