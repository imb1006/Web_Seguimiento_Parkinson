import serial

def test_port(port_name):
    try:
        ser = serial.Serial(port_name, 9600, timeout=1)
        ser.close()
        print(f"Conexión exitosa en {port_name}")
    except serial.SerialException as e:
        print(f"Error al abrir el puerto {port_name}: {e}")

test_port('COM5')
test_port('COM6')
