import serial
import requests
import time
import math

# Configura el puerto serie
ser = serial.Serial('COM6', 9600)  # Ajusta el puerto COM y el baudrate

def send_command_to_arduino(command):
    """Envía un comando al Arduino."""
    print(f"Enviando a Arduino: {command}")  # Imprime el dato a enviar
    ser.write(command.encode())
    
def send_data_to_server(endpoint, data):
    """Envía datos al servidor."""
    url = f'http://localhost:3000/{endpoint}'
    try:
        with requests.post(url, json={endpoint: data}) as response:
            print(f"Enviado a {url}: {data}")
    except requests.exceptions.RequestException as e:
        print(f"Error al enviar a {url}: {e}")

while True:
    # Recibe datos de Arduino y los envía al servidor
    if ser.in_waiting:
        line = ser.readline().decode('utf-8').rstrip()
        print(f"Recibido de Arduino: {line}")
        
        if line == "IZQUIERDA":
            send_data_to_server('izquierda', True)  # Enviar el estado de "IZQUIERDA" al servidor
        else:
            # Siempre que se recibe algo que no es IZQUIERDA, se desactiva este estado
            send_data_to_server('izquierda', False)

        if line in ['0', '1']:  # Si es un mensaje de inicio/fin de actividad
            send_data_to_server('actividad', {'estado': 'detenida' if line == '0' else 'iniciada'})

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
    try:
        with requests.get('http://localhost:3000/command') as response:
            if response.status_code == 200:
                data = response.json()['command']
                if data:
                    print(f"Comando recibido del servidor: {data}")
                    send_command_to_arduino(data)
                else:
                    print("No hay comando para enviar al Arduino")
            else:
                print(f"Error al solicitar comando: Estado {response.status_code}")
    except requests.exceptions.RequestException as e:
        print(f"Error al conectar con el servidor: {e}")
        
    time.sleep(0.1)  # Pausa de 0.1 segundos tras cada iteración para no sobrecargar el puerto