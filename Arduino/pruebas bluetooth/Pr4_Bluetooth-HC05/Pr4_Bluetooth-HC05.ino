#include <SoftwareSerial.h>
SoftwareSerial BT(10, 11);


void setup() {
  BT.begin(38400);
  Serial.begin(9600);

}

void loop() {
  if (Serial.available() > 0) {
    BT.write(Serial.read());
  }
  if (BT.available() > 0) {
    Serial.write(BT.read());
  }
}
