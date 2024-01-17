#include <SoftwareSerial.h>

int RX_PIN = 10;
int TX_PIN = 11;
int LED_PIN = 13;

SoftwareSerial btSerial(RX_PIN, TX_PIN);

void setup() {
  pinMode(LED_PIN, OUTPUT);
  Serial.begin(9600);
  btSerial.begin(9600);
  while (!Serial) {
    ; // Espera a que la conexión serial esté lista
  }
  Serial.println("Comunicación Bluetooth iniciada");
}

void loop() {
  // Envía un mensaje constante
  //btSerial.println("Hola desde Arduino");
  //delay(2000);

  // Escucha comandos para el LED
  if (btSerial.available()) {
    char received = btSerial.read();
    Serial.print("Recibido: ");
    Serial.println(received);

    if (received == '1') {
      digitalWrite(LED_PIN, HIGH);
      btSerial.println("LED Encendido");
    } else if (received == '0') {
      digitalWrite(LED_PIN, LOW);
      btSerial.println("LED Apagado");
    }
  }
}
