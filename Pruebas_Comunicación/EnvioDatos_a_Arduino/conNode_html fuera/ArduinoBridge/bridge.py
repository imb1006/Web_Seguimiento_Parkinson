import serial
import requests

# Configura el puerto serie
ser = serial.Serial('COM5', 9600)  # Ajusta el puerto COM y el baudrate

while True:
    response = requests.get('http://localhost:3000/command')
    if response.status_code == 200:
        data = response.json()['command']
        if data:  # Verifica que data no esté vacío
            print(f"Enviando: {data}")  # Imprime el dato a enviar
            ser.write(data.encode())