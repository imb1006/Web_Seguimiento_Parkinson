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
  if (btSerial.available()) {
    char received = btSerial.read();
    Serial.print("Recibido: "); // Imprime el dato recibido
    Serial.println(received);

    if (received == '1') {
      digitalWrite(LED_PIN, HIGH);
    } else if (received == '0') {
      digitalWrite(LED_PIN, LOW);
    }
  }
}
