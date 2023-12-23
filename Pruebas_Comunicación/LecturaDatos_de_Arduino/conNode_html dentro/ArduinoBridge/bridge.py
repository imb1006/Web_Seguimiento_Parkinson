import serial
import requests

# Configura el puerto serie
ser = serial.Serial('COM5', 9600)  # Ajusta el puerto COM y el baudrate

while True:
    if ser.in_waiting:
        line = ser.readline().decode('utf-8').rstrip()
        requests.post('http://localhost:3000/data', json={'data': line})


#while True:
 #   if ser.in_waiting:
  #      line = ser.readline().decode('utf-8').rstrip()
   #     print("Datos recibidos:", line)  # Agrega esta línea para depuración
    #    requests.post('http://localhost:3000/data', json={'data': line})

