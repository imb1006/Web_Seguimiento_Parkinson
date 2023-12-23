from flask import Flask, render_template
from flask_socketio import SocketIO
import serial
import threading

app = Flask(__name__)
app.config['SECRET_KEY'] = 'secret!'
socketio = SocketIO(app)

def read_from_port(ser):
    while True:
        reading = ser.readline().decode('utf-8').rstrip()
        socketio.emit('newdata', {'data': reading})

@app.route('/')
def index():
    return render_template('index.html')

if __name__ == '__main__':
    ser = serial.Serial('COM5', 9600)  
    t = threading.Thread(target=read_from_port, args=(ser,))
    t.start()
    socketio.run(app, debug=True)
