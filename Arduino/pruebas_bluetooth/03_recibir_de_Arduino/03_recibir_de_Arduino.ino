#include <SoftwareSerial.h>

// Define los pines para la comunicación serial con el HC-05
int RX_PIN = 10; // Define el pin RX, conectado al TX del HC-05
int TX_PIN = 11; // Define el pin TX, conectado al RX del HC-05

// Crea una instancia de SoftwareSerial
SoftwareSerial btSerial(RX_PIN, TX_PIN); // RX, TX

void setup() {
  // Inicia la comunicación serial con el PC
  Serial.begin(9600);

  // Inicia la comunicación serial con el HC-05
  btSerial.begin(9600);

  // Espera a que la comunicación serial esté lista
  while (!Serial) {
    ; // espera a que la conexión serial esté lista
  }

  Serial.println("Comunicación Bluetooth iniciada");
}

void loop() {
  // Envía un mensaje al HC-05 cada 2 segundos
  btSerial.println("Hola desde Arduino");
  delay(2000);
}
