
// Control del MPU6050 mediante la libreria I2C 
// Libreria MPU6050.h requiere I2Cdev.h
// Libreria I2Cdev.h requiere Wire.h

#include "I2Cdev.h"
#include "MPU6050.h"
#include "Wire.h"
#include <LiquidCrystal.h>

// El MPU6050 presenta por defecto una direccion de 0x68
MPU6050 mpu;

// Creacion del objeto LCD (RS, EN, D4, D5, D6, D7)
LiquidCrystal lcd(12,11,2,3,4,5);

// Valores RAW (sin procesar) del acelerometro y giroscopio en los ejes x,y,z
int ax, ay, az;
int gx, gy, gz;

////////////////////////////////// VARIABLES /////////////////////////////////////

String texto = "";  // texto de entrada
int boton1 = 0;     // flag para el botón START
float contP = 0;    // numero de pasos
int pierna = 0;     // flag paso 0: vertical o atrás, 1: delante

unsigned long tiempo1 = 0;  // tiempo de inicio
unsigned long tiempo2 = 0;  // tiempo de fin
unsigned long tiempo = 0;   // tiempo transcurrido

int cont_espera = 0;  // tiempo de espera entre un paso y el siguiente
int altura = 0;       // altura del paciente
float cte = 0.000f;   // constante para obtener la longitud del paso
float velocidad;      // velocidad
float frecuencia;     // frecuencia
unsigned long t_prev = 0; // tiempo anterior
float dt = 0;             // derivar respecto al tiempo
float angX, angY;   // angulos x e y
float angX_prev, angY_prev; // angulos x e y previos


////////////////////////////////// FUNCIONES /////////////////////////////////////

void leerSerial()
{
  // Funcion que lee el Monitor Serie y comprueba si se introdujo algun texto.
  if (Serial.available()>0){    
     texto = Serial.readString();    
  }
}



void get_orientacion(){
  // Funcion que obtiene la orientacion del sensor aplicando un filtro complementario al angulo de inclinacion y de rotacion.
  
  // Leer las aceleraciones y velocidades angulares
  mpu.getAcceleration(&ax, &ay, &az);
  mpu.getRotation(&gx, &gy, &gz);
  
  dt = (millis()-t_prev)/1000.0;
  t_prev=millis();
  
  //Calcular los angulos con acelerometro
  float accel_angX=atan(ay/sqrt(pow(ax,2) + pow(az,2)))*(180.0/3.14);
  float accel_angY=atan(-ax/sqrt(pow(ay,2) + pow(az,2)))*(180.0/3.14);
      
  //Calcular angulo de rotacion con giroscopio y filtro complemento  
  angX = 0.98*(angX_prev+(gx/131)*dt) + 0.02*accel_angX;
  angY = 0.98*(angY_prev+(gy/131)*dt) + 0.02*accel_angY;
      
      
  angX_prev=angX;
  angY_prev=angY;
    
  //Angulos 
  //Serial.print("Eje X:  ");
  //Serial.print(angX); 
  //Serial.print("Eje Y: ");
  //Serial.println(angY);
  }



void contarPasos()
{
  // Funcion que lee las aceleraciones y contabiliza los pasos.
   
    cont_espera = cont_espera + 10; // contar el tiempo que tarda en dar un paso
    
    
    // VALORES PARA EL PIE
    // si el pie se ha levantado

    if ((angX > -15.2 && angY < -11)) { 
       if (pierna == 0){ // si antes el pie estaba apoyado
            pierna = 1;
       }
    }
        
    // si el pie esta apoyado
    if ((angX >= -18 && angX <=-16) &&  (angY >= -9 && angY <= -3)) { 
       // si antes el pie estaba levantado
       if (pierna == 1){ 
        pierna = 0;
        cont_espera = 0;
        contP += 2;
            
       }
    }    
}

////////////////////////////////////////////////////////// CONFIGURACIÓN DEL ///////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////    PROGRAMA       ///////////////////////////////////////////////////////////


void setup() 
{
    
  pinMode(8,INPUT); //BOTON1
  pinMode(7,INPUT); //BOTON2
  

  Serial.begin(9600); // Iniciar puerto serial
  Wire.begin(); // Iniciar I2C
  mpu.initialize(); // Iniciar sensor
  lcd.begin(16, 2); // Iniciar LCD
  
  // Comprobar que el sensor está iniciado correctamente
  if (mpu.testConnection()) Serial.println("Sensor iniciado correctamente");
    else Serial.println("Error al iniciar el sensor");

  // Introducir la altura
  Serial.print("Introduce la altura del paciente (cm): ");
  delay(3000);

  // Mostrar la altura y convertirla a entero
  leerSerial();
  Serial.println(texto);  
  lcd.setCursor(0, 0);  
  lcd.print(texto);
  altura = texto.toInt();
   
  // Introducir el sexo
  Serial.print("Introduce el sexo del paciente: ");
  delay(3000);

  // Mostrar el sexo
  leerSerial();
  Serial.println(texto);
  lcd.setCursor(5, 0);  
  lcd.print(texto);
  
   
  // Si se trata de una mujer
  if (texto == "mujer"){
    cte = 0.413f;  
    
  // si se trata de un hombre
  } else { 
    cte = 0.415f;
  }
    
  Serial.println("Presionar START...");
  lcd.setCursor(2, 1);  
  lcd.print("Presionar START...");
}

////////////////////////////////////////////////////////// PROGRAMA PRINCIPAL ///////////////////////////////////////////////////////////

void loop() 
{
  // PRESIONAR BOTON START
  if (digitalRead(8) == HIGH){
    boton1 = 1;              // cambiar el estado del boton
    tiempo1 = millis()/1000; // inicializamos el tiempo
    lcd.clear();             // borrar el contenido del lcd
    
    
        
  } 
  
  // INICIO DE LA MARCHA
  if (boton1 == 1){ 
    // Leer el tiempo en ese instante y dividirlo entre 1000 para obtener el tiempo en segundos
    tiempo2 = millis()/1000;
    
    // obtener orientacion
    get_orientacion();

    // PASOS
    //Serial.print("Pasos: ");
    contarPasos(); // contamos los pasos 
    //Serial.println(contP);
  
    // TIEMPO
    //Serial.print("Tiempo (s): ");
    
    tiempo += tiempo2 - tiempo1; 
    //Serial.println(tiempo);   
    tiempo1 = tiempo2; // Actualizar tiempo
          
    // FRECUENCIA
    frecuencia = (contP/tiempo);

    //VELOCIDAD
    lcd.print("Velocidad");
    velocidad = frecuencia*60*60*cte*altura/100000.;
    //Serial.println(velocidad);
    lcd.setCursor(0, 0);  
    lcd.print(velocidad);

    // Si tarda un tiempo en dar el siguiente paso 
    if (cont_espera > 1000){ 
      //Serial.println("Izquierda"); //mostrar la pierna que se debe mover
      lcd.setCursor(0, 1);  
      lcd.print("Izquierda");
      tiempo = 0;
      contP = 0;
    }   
  }

  // FIN DE LA MARCHA
  if (digitalRead(7)==HIGH){ 
    boton1 = 0;

    lcd.clear();  
    lcd.setCursor(0, 0);
    lcd.print("Apagado");
    Serial.println("Apagado");
    while(1);
     }

  delay(10); //leer cada 10 milisegundos
}
