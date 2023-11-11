//GND - GND
//VCC - VCC
//SDA - Pin A4
//SCL - Pin A5
//INT - Pin 2

#include "I2Cdev.h"
#include "MPU6050_6Axis_MotionApps20.h"
#include <LiquidCrystal_I2C.h>

#if I2CDEV_IMPLEMENTATION == I2CDEV_ARDUINO_WIRE
#include "Wire.h"

#endif

// class default I2C address is 0x68
// specific I2C addresses may be passed as a parameter here
// AD0 low = 0x68
// AD0 high = 0x69
MPU6050 mpu;

// Crear el objeto LCD
LiquidCrystal_I2C lcd(0x27, 20,4);

#define INTERRUPT_PIN 2



// MPU control/status vars
bool dmpReady = false;  // set true if DMP init was successful
uint8_t mpuIntStatus;   // holds actual interrupt status byte from MPU
uint8_t devStatus;      // return status after each device operation (0 = success, !0 = error)
uint16_t packetSize;    // expected DMP packet size (default is 42 bytes)
uint16_t fifoCount;     // count of all bytes currently in FIFO
uint8_t fifoBuffer[64]; // FIFO storage buffer

Quaternion q;           // [w, x, y, z]
VectorInt16 aa;         // [x, y, z]
VectorInt16 aaReal;     // [x, y, z]
VectorInt16 aaWorld;    // [x, y, z]
VectorFloat gravity;    // [x, y, z]
float ypr[3];           // [yaw, pitch, roll]

volatile bool mpuInterrupt = false;



////////////////////////////////// VARIABLES /////////////////////////////////////

String texto = "";  // texto de entrada
int boton1 = 0;     // flag para el botón START
float contP = 0;    // numero de pasos
int bloqueos = 0;   // numero de bloqueos
int cont = 0;       // flag de bloqueos
int pierna = 0;     // flag paso 0: vertical o atras, 1: delante


void dmpDataReady() {
    mpuInterrupt = true;
}


void leerSerial()
{
  // Funcion que lee el Monitor Serie y comprueba si se introdujo algun texto.
  
  if (Serial.available()>0){    
     texto = Serial.readString();    
  }
}


void contarPasos()
{
  // Funcion que lee los valores del acelerometro y contabiliza los pasos realizados.    
    // VALORES PARA LA PIERNA
    if (ypr[0] * 180/M_PI >= -171){
      if (pierna == 0){ // si antes la pierna estaba en posición de reposo
        pierna = 1;
      }
      // si la pierna esta en posicion vertical o hacia detras (posicion de reposo)
    } 
    
    if ((ypr[0] * 180/M_PI <= -175) && (ypr[1] * 180/M_PI >= 151) && (ypr[2] * 180/M_PI >= 179)) {
      if (pierna == 1){ // si antes estaba en movimiento
        pierna = 0;
        contP += 2; 
      }
    }    
}


void mostrar_valores()
{
  // Mostrar Yaw, Pitch, Roll
  Serial.print("ypr\t");
  Serial.print(ypr[0] * 180/M_PI);
  Serial.print("\t");
  Serial.print(ypr[1] * 180/M_PI);
  Serial.print("\t");
  Serial.println(ypr[2] * 180/M_PI);
}




void setup() {
    // join I2C bus (I2Cdev library doesn't do this automatically)
    #if I2CDEV_IMPLEMENTATION == I2CDEV_ARDUINO_WIRE
        Wire.begin();
        Wire.setClock(400000); // 400kHz I2C clock. Comment this line if having compilation difficulties
    #elif I2CDEV_IMPLEMENTATION == I2CDEV_BUILTIN_FASTWIRE
        Fastwire::setup(400, true);
    #endif

    pinMode(8,INPUT); //BOTON1
    pinMode(7,INPUT); //BOTON2
    Serial.begin(9600);

    // Iniciar MPU6050
    Serial.println(F("Initializing I2C devices..."));
    mpu.initialize();
    //pinMode(INTERRUPT_PIN, INPUT);

    lcd.init();         // Iniciar LCD
    lcd.backlight();
    lcd.clear();

    // Comprobar  conexion
    //Serial.println(F("Testing device connections..."));
    Serial.println(mpu.testConnection() ? F("MPU6050 connection successful") : F("MPU6050 connection failed"));

    // Iniciar DMP
    Serial.println(F("Initializing DMP..."));
    devStatus = mpu.dmpInitialize();

    // Valores de calibracion
    mpu.setXGyroOffset(122);
    mpu.setYGyroOffset(-8);
    mpu.setZGyroOffset(83);
    mpu.setZAccelOffset(-2084);

    // Activar DMP
    if (devStatus == 0) {
        Serial.println(F("Enabling DMP..."));
        mpu.setDMPEnabled(true);

        // Activar interrupcion
        attachInterrupt(digitalPinToInterrupt(INTERRUPT_PIN), dmpDataReady, RISING);
        mpuIntStatus = mpu.getIntStatus();

        //Serial.println(F("DMP ready! Waiting for first interrupt..."));
        dmpReady = true;

        // get expected DMP packet size for later comparison
        packetSize = mpu.dmpGetFIFOPacketSize();
    } else {
        // ERROR!
        // 1 = initial memory load failed
        // 2 = DMP configuration updates failed
        // (if it's going to break, usually the code will be 1)
        Serial.print(F("DMP Initialization failed (code "));
        Serial.print(devStatus);
        Serial.println(F(")"));
    }
    
      
  lcd.setCursor(6,1);
  lcd.print("Presionar");
  lcd.setCursor(8, 2);
  lcd.print("START");
}

void loop() {
    // Si fallo al iniciar, parar programa
    if (!dmpReady) return;

    // Ejecutar mientras no hay interrupcion
    //while (!mpuInterrupt && fifoCount < packetSize) {
        // AQUI EL RESTO DEL CODIGO DE TU PROGRRAMA
        
    //}

    mpuInterrupt = false;
    mpuIntStatus = mpu.getIntStatus();

    // Obtener datos del FIFO
    fifoCount = mpu.getFIFOCount();

    // Controlar overflow
    if ((mpuIntStatus & 0x10) || fifoCount == 1024) {
        mpu.resetFIFO();
        //Serial.println(F("FIFO overflow!"));
    } 
    else if (mpuIntStatus & 0x02) {
        // wait for correct available data length, should be a VERY short wait
        while (fifoCount < packetSize) fifoCount = mpu.getFIFOCount();

        // read a packet from FIFO
        mpu.getFIFOBytes(fifoBuffer, packetSize);
        
        // track FIFO count here in case there is > 1 packet available
        // (this lets us immediately read more without waiting for an interrupt)
        fifoCount -= packetSize;

        // Mostrar Yaw, Pitch, Roll
        mpu.dmpGetQuaternion(&q, fifoBuffer);
        mpu.dmpGetGravity(&gravity, &q);
        mpu.dmpGetYawPitchRoll(ypr, &q, &gravity);
    
        // PRESIONAR BOTON START
        if (digitalRead(8) == HIGH){
          
          boton1 = 1;              // cambiar el estado del boton
          lcd.clear();             // borrar el contenido del lcd
        }
        // INICIO DE LA MARCHA
        if (boton1 == 1){ 
          
          // PASOS
          Serial.println(contP);
          lcd.setCursor(0,0);
          lcd.print("Pasos:");
          lcd.setCursor(7, 0);
          lcd.print(contP);
          
          contarPasos(); 
          
          
        }
        // FIN DE LA MARCHA
        if (digitalRead(7)==HIGH){ 
          boton1 = 0;
          lcd.clear();
          lcd.setCursor(8, 1);
          lcd.print("FIN");
        }
    }

  
}
