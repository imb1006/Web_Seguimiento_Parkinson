import serial
import requests

# Configura el puerto serie
ser = serial.Serial('COM5', 9600)  # Ajusta el puerto COM y el baudrate

def send_command_to_arduino(command):
    """Envía un comando al Arduino."""
    print(f"Enviando: {command}")  # Imprime el dato a enviar
    ser.write(command.encode())
    
def send_data_to_server(endpoint, data):
    """Envía datos al servidor."""
    url = f'http://localhost:3000/{endpoint}'
    requests.post(url, json={endpoint: data})
    print(f"Enviado a {url}: {data}")

while True:
    
    # Recibe datos de Arduino y los envía al servidor
    if ser.in_waiting:
        line = ser.readline().decode('utf-8').rstrip()
        print(f"Recibido de Arduino: {line}")

        # Extraer el tipo de dato y el valor
        parts = line.split(":")
        if len(parts) == 2:
            data_type, value = parts
            try:
                value = float(value)  # Convertir el valor a flotante
                send_data_to_server(data_type, value)
            except ValueError:
                print("Error: no se pudo convertir el dato a flotante")

    # Recibe comandos del servidor y los envía a Arduino
    #print("Solicitando comando al servidor...")
    response = requests.get('http://localhost:3000/command')
    #print(f"Respuesta recibida del servidor: {response.status_code}")
    if response.status_code == 200:
        data = response.json()['command']
        if data:  # Verifica que data no esté vacío
            send_command_to_arduino(data)
            print("No hay comando para enviar al Arduino")