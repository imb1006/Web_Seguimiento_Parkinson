// Control del MPU6050 mediante la libreria I2C 
// Libreria MPU6050.h requiere I2Cdev.h
// Libreria I2Cdev.h requiere Wire.h

#include "I2Cdev.h"
#include "MPU6050.h"
#include "Wire.h"
#include <LiquidCrystal_I2C.h>
#include <SoftwareSerial.h>

// El MPU6050 presenta por defecto una direccion de 0x68
MPU6050 mpu;

// Crear el objeto LCD
LiquidCrystal_I2C lcd(0x27, 16,2);

// Crear instancia de SoftwareSerial para el HC-05
SoftwareSerial btSerial(10, 11);

// Valores sin procesar (valores RAW) del acelerometro y giroscopio (ejes x, y, z)
int ax, ay, az; // rango por defecto: -2g a +2g
int gx, gy, gz; // rango por defecto: -250°/sec a +250°/sec

// Valores en el Sistema Internacional
const float accEscala = (2.0 * 9.81 )/ 32768.0;
const float gyroEscala = 250.0 / 32768.0;

////////////////////////////////// VARIABLES /////////////////////////////////////
char command; // Variable para almacenar el comando recibido

String texto = "";  // texto de entrada
int boton1 = 3;     // flag para el botón START
float contP = 0;    // numero de pasos entre bloqueos
float Ptotal = 0;   // numero total de pasos
int bloqueos = 0;   // numero de bloqueos
int cont = 0;       // flag de bloqueos
int pierna = 0;     // flag paso 0: vertical o atras, 1: delante

unsigned long tiempo1 = 0;          // inicio
unsigned long tiempo2 = 0;          // fin
unsigned long tiempo = 0;           // tiempo transcurrido
unsigned long tiempo_actividad = 0; // tiempo de actividad total

float cont_espera = 0;  // tiempo entre un paso y el siguiente
int altura = 0;         // altura del paciente
float cte = 0.000f;     // constante para obtener la longitud del paso
float velocidad = 0.0;  // velocidad
float sum_vel = 0.0;    // sumatorio de la velocidad
int cont_vel = 0;       // contador de lecturas de la velocidad
float velocidadMedia = 0.0;   // velocidad media
float frecuencia;       // frecuencia
float actividadMin;    // tiempo de actividad en minutos


////////////////////////////////// FUNCIONES ///////////////////////////////////// 

void contarPasos(){
    // Funcion que lee los valores del acelerometro y contabiliza los pasos.
    cont_espera+= 0.5; // contar el tiempo que tarda en dar un paso
    
        // VALORES PARA LA PIERNA
    if ((ay * accEscala) >= 0.5 ) {  // si la pierna izquierda está hacia delante 
      if (pierna == 0){ // si antes estaba en posición de reposo
        pierna = 1; // actualiza el estado de la pierna a "en movimiento".
      }
    // si la pierna está en posición vertical o hacia detrás (posición de reposo)
    } else {
      if (pierna == 1){ // si antes estaba en movimiento
        pierna = 0; // actualiza el estado de la pierna a "en reposo".
        cont_espera = 0; // reinicia el contador de espera.
        contP += 2; // incrementa el contador de pasos en 2.
        Ptotal +=2; //incrementa el nº de pasos totales en 2.
      }
    }        
}

void resetActivity() {
  // Reinicia todas las variables y estados relevantes
  contP = 0;
  tiempo = 0;
  tiempo_actividad = 0;
  bloqueos = 0;
  sum_vel = 0.0;
  cont_vel = 0;
  velocidadMedia = 0.0;
  cont_espera = 0;
  Ptotal = 0;
}

void finalizarActividad(){
  // Número de bloqueos
    lcd.setCursor(0,0);
    lcd.print("Bloqueos:");
    lcd.setCursor(10, 0);  
    lcd.print(bloqueos);

    // Enviar datos por Bluetooth, solo si no son NaN ni Inf
    if (!isnan(bloqueos)&& !isinf(bloqueos)) {
        btSerial.print("bloqueos:");
        btSerial.println(bloqueos);
    }

    // Número total de pasos

    // Enviar datos por Bluetooth, solo si no son NaN ni Inf
    if (!isnan(Ptotal)&& !isinf(Ptotal)) {
        btSerial.print("Ptotal:");
        btSerial.println(Ptotal);
    }
    
    // Tiempo total de actividad
    actividadMin = tiempo_actividad/60.;
    lcd.setCursor(0, 1); 
    lcd.print(actividadMin);
    lcd.setCursor(4, 1);  
    lcd.print("min");

    // Enviar datos por Bluetooth, solo si no son NaN ni Inf
    if (!isnan(actividadMin)&& !isinf(actividadMin)) {
        btSerial.print("actividadMin:");
        btSerial.println(actividadMin);
    }

    // Velocidad media
    lcd.setCursor(8, 1);  
    // Cálculo de la velocidad media solo si cont_vel > 0
    if (cont_vel > 0) {
        velocidadMedia = sum_vel / cont_vel;
    }
    lcd.print(velocidadMedia);
    lcd.setCursor(12, 1); 
    lcd.print("Km/h");

    // Enviar datos por Bluetooth, solo si no son NaN ni Inf
    if (!isnan(velocidadMedia)&& !isinf(velocidadMedia)) {
        btSerial.print("velocidadMedia:");
        btSerial.println(velocidadMedia);
    }

    resetActivity(); // Llama a una función para resetear la actividad
}

////////////////////////////////////////////////////////// CONFIGURACIÓN DEL ///////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////    PROGRAMA       ///////////////////////////////////////////////////////////

void setup(){
  
  pinMode(8,INPUT); //BOTON1
  pinMode(7,INPUT); //BOTON2

  Serial.begin(9600);     // Iniciar la comunicación Serial con el PC
  btSerial.begin(9600);   // Iniciar la comunicación Serial con el HC-05
  Wire.begin();           // Iniciar I2C
  mpu.initialize();       // Iniciar mpu
  lcd.init();             // Iniciar LCD
  lcd.backlight();
  lcd.clear();
 
  // Introducir la altura
  Serial.print("Introduce la altura del paciente (cm): ");
  delay(3000);

  // Mostrar la altura y convertirla a entero
  texto = 155; // fijo para pruebas ------------------------
  Serial.println(texto);
  altura = texto.toInt(); 

  // Introducir el sexo
  Serial.print("Introduce el sexo del paciente: ");
  delay(3000);

  // Mostrar el sexo
  texto = "mujer"; // fijo para pruebas--------------------
  Serial.println(texto);
    
  if (texto == "mujer"){
    cte = 0.413f;  // Si se trata de una mujer
  } else { 
    cte = 0.415f; // si se trata de un hombre
  }
    
  lcd.setCursor(0, 0);  
  lcd.print("Presionar START");
}


////////////////////////////////////////////////////////// PROGRAMA PRINCIPAL ///////////////////////////////////////////////////////////

void loop(){
  
  // Verificar si hay un comando disponible a través de Bluetooth
  if (btSerial.available()) {
    char command = btSerial.read();

    if (command == '1') {
      boton1 = 1; // Iniciar la actividad como si se presionara el botón físico
      tiempo1 = millis() / 1000;
      lcd.clear();
    } else if (command == '0') {
      boton1 = 0; // Finalizar la actividad como si se presionara el botón físico
      lcd.clear();
      finalizarActividad();
    }
  }

  // Verificar si hay acción disponible a través de hardware
   
  if (digitalRead(8) == HIGH){  // botón START presionado 
    boton1 = 1;                 // cambiar el estado del boton
    btSerial.println('1');      // Enviar estado al script de Python
    tiempo1 = millis()/1000;    // inicializamos el tiempo
    lcd.clear();                // borrar el contenido del lcd   
  } 
  if (digitalRead(7) == HIGH){  // botón STOP presionado
    boton1 = 0;                 // cambiar el estado del boton
    btSerial.println('0');      // Enviar estado al script de Python
    lcd.clear();                // borrar el contenido del lcd
    finalizarActividad();
  }

  // Funcionamiento según el estado del botón
  
  if (boton1 == 1){ // INICIO DE LA MARCHA
    // Leer el tiempo en ese instante y dividirlo entre 1000 para obtener el tiempo en segundos
    tiempo2 = millis()/1000;
    mpu.getAcceleration(&ax, &ay, &az); // leer ejes del acelerometro
    mpu.getRotation(&gx, &gy, &gz);     // leer ejes del giroscopio
      
    // PASOS
    contarPasos(); // contamos los pasos
    lcd.setCursor(0, 1);  
    lcd.print("Pasos:");
    lcd.setCursor(7, 1);  
    lcd.print(contP);
    
    // Enviar datos por Bluetooth, solo si no son 0, NaN ni Inf
    if (contP > 0 && !isnan(contP) && !isinf(contP)) {
        btSerial.print("contP:");
        btSerial.println(contP);
    }
    
    // TIEMPO
    lcd.setCursor(0, 0);  
    lcd.print("Tiempo:"); 
    lcd.setCursor(8, 0);  
    lcd.print(tiempo);
    lcd.setCursor(11, 0);  
    lcd.print("s");

    // Enviar datos por Bluetooth, solo si no son 0, NaN ni Inf
    if (tiempo > 0 && !isnan(tiempo)&& !isinf(tiempo)) {
        btSerial.print("tiempo:");
        btSerial.println(tiempo);
    }
    
    // FRECUENCIA
    frecuencia = (contP/tiempo);

    //VELOCIDAD (Km/h)
    velocidad = frecuencia*60*60*cte*altura/100000.;

    lcd.setCursor(0, 0);  
    lcd.print("Velocidad:");
    lcd.setCursor(10, 0);  
    lcd.print(velocidad);
    lcd.setCursor(14, 0);  
    lcd.print("Km/h");

    // Enviar datos por Bluetooth, solo si no son 0, NaN ni Inf
    if (velocidad > 0 && !isnan(velocidad)&& !isinf(velocidad)) {
        btSerial.print("velocidad:");
        btSerial.println(velocidad);
    }
    
    // mientras entre un paso y el siguiente haya menos de 5 segundos
    if (cont_espera < 5){
      tiempo += tiempo2 - tiempo1;           // incrementar tiempo 
      tiempo_actividad += tiempo2 - tiempo1; // incrementar tiempo actividad  
      cont = 0;                              // contador a 0 

      // si la velocidad es menor de 100
      if (velocidad < 100){
        sum_vel = sum_vel + velocidad ; // sumar velocidad al total
        cont_vel ++;                    // incrementar contador
      } 
    }
      
    tiempo1 = tiempo2; // Actualizar tiempo
     
    // Si tarda más de 5 segundos en dar el siguiente paso 
    if (cont_espera >= 5){ 
      cont++; // incrementar contador

      // la primera vez que detecta el bloqueo
      if (cont == 1){
        bloqueos++; // incrementar el número de bloqueos  
      }

      // mostrar en el lcd un mensaje de aviso
      lcd.clear();    
      lcd.setCursor(3, 0);  
      lcd.print("IZQUIERDA");

      btSerial.println("IZQUIERDA");  //ahora por bluetooth
      
      // restart
      tiempo = 0; 
      contP = 0;  
    } 
  } 
  delay(500); // leer cada medio segundo
}
