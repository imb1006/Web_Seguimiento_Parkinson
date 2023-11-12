// Control del MPU6050 mediante la libreria I2C 
// Libreria MPU6050.h requiere I2Cdev.h
// Libreria I2Cdev.h requiere Wire.h

#include "I2Cdev.h"
#include "MPU6050.h"
#include "Wire.h"
#include <LiquidCrystal_I2C.h>


// El MPU6050 presenta por defecto una direccion de 0x68
MPU6050 mpu;

// Crear el objeto LCD
LiquidCrystal_I2C lcd(0x27, 16,2);


// Valores sin procesar (valores RAW) del acelerometro y giroscopio (ejes x, y, z)
int ax, ay, az; // rango por defecto: -2g a +2g
int gx, gy, gz; // rango por defecto: -250°/sec a +250°/sec


// Valores en el Sistema Internacional
const float accEscala = (2.0 * 9.81 )/ 32768.0;
const float gyroEscala = 250.0 / 32768.0;

////////////////////////////////// VARIABLES /////////////////////////////////////
String texto = "";  // texto de entrada
int boton1 = 0;     // flag para el botón START
float contP = 0;    // numero de pasos
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
float mean_vel = 0.0;   // velocidad media
float frecuencia;       // frecuencia
float actividad_min;    // tiempo de actividad en minutos




////////////////////////////////// FUNCIONES ///////////////////////////////////// 


void printMedidas()
{
  // Funcion que muestra los valores de salida de los sensores inerciales en el Sistema Internacional.
      
  Serial.print(F("a[x y z](m/s2):\t"));
  Serial.print(ax * accEscala); Serial.print("\t");
  Serial.print(ay * accEscala); Serial.print("\t");
  Serial.println(az * accEscala); 
  Serial.print(F("g[x y z](deg/s):\t"));
  Serial.print(gx * gyroEscala); Serial.print("\t");
  Serial.print(gy * gyroEscala); Serial.print("\t");
  Serial.println(gz * gyroEscala);

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
  // Funcion que lee los valores del acelerometro y contabiliza los pasos.
  
    cont_espera = cont_espera + 0.5; // contar el tiempo que tarda en dar un paso
    
    
    // VALORES PARA LA PIERNA
    // si la pierna izquierda está hacia delante
    if ((ay * accEscala) >= 0.5 ) {  
      if (pierna == 0){ // si antes la pierna estaba en posición de reposo
        pierna = 1;
      }
    // si la pierna está en posición vertical o hacia detrás (posición de reposo)
    } else {
      if (pierna == 1){ // si antes estaba en movimiento
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
  Wire.begin();       // Iniciar I2C
  mpu.initialize();   // Iniciar mpu
  lcd.init();         // Iniciar LCD
  lcd.backlight();
  lcd.clear();
 
  // Comprobar que el sensor esta iniciado correctamente
  if (mpu.testConnection()) Serial.println("Sensor se ha iniciado correctamente");
    else Serial.println("Se ha producido un error al iniciar el sensor");

  // Introducir la altura
  Serial.print("Introduce la altura del paciente (cm): ");
  delay(3000);

  // Mostrar la altura y convertirla a entero
  //leerSerial(); // comentado para pruebas ----------------
  texto = 155; // fijo para pruebas ------------------------
  Serial.println(texto);
  altura = texto.toInt(); 

  // Introducir el sexo
  Serial.print("Introduce el sexo del paciente: ");
  delay(3000);

  // Mostrar el sexo
  //leerSerial();//comentado para pruebas ------------------
  texto = "mujer"; // fijo para pruebas--------------------
  Serial.println(texto);
     
  // Si se trata de una mujer
  if (texto == "mujer"){
    cte = 0.413f;  
    
  // si se trata de un hombre
  } else { 
    cte = 0.415f;
  }
    
  //Serial.println("Presionar START...");
  lcd.setCursor(0, 0);  
  lcd.print("Presionar START");
  
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
    mpu.getAcceleration(&ax, &ay, &az); // leer ejes del acelerometro
    mpu.getRotation(&gx, &gy, &gz);     // leer ejes del giroscopio
  
    //printMedidas();
    
    // PASOS
    //Serial.print("Pasos: ");
    contarPasos(); // contamos los pasos 
    //Serial.println(contP);
    lcd.setCursor(0, 1);  
    lcd.print("Pasos:");
    lcd.setCursor(7, 1);  
    lcd.print(contP);
    
    // TIEMPO
    //Serial.print("Tiempo (s): ");
    //Serial.println(tiempo);
    lcd.setCursor(0, 0);  
    lcd.print("Tiempo:"); 
    lcd.setCursor(8, 0);  
    lcd.print(tiempo);
    lcd.setCursor(11, 0);  
    lcd.print("s");

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

    
    // mientras entre un paso y el siguiente haya menos de 5 segundos
    if (cont_espera < 5){
      tiempo += tiempo2 - tiempo1;           // incrementar tiempo 
      tiempo_actividad += tiempo2 - tiempo1; // incrementar tiempo actividad  
      cont = 0;                              // contador a 0 

      // si la velocidad es menor de 100
      if (velocidad < 100){
        sum_vel = sum_vel + velocidad ; // sumar velocidad al total
        cont_vel ++;                    // incrementar contador

        // mostrar velocidad      
        Serial.print("Velocidad:");
        Serial.print(velocidad);
        Serial.println(" Km/h");  
      } 
    }
  
    // Actualizar tiempo
    tiempo1 = tiempo2; 
     
    
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
      
      // restart
      tiempo = 0; 
      contP = 0;  
     
    } 

  }

   
  // FIN DE LA MARCHA
  if (digitalRead(7)==HIGH){ 
    boton1 = 0;
    
    lcd.clear(); 

    // Número de bloqueos
    lcd.setCursor(0,0);
    lcd.print("Bloqueos:");
    lcd.setCursor(10, 0);  
    lcd.print(bloqueos);
    
    //Serial.print(F("Número de bloqueos:\t"));
    //Serial.println(bloqueos);

    // Tiempo total de actividad
    lcd.setCursor(0, 1); 
    //lcd.print("Actividad:");
    //lcd.setCursor(11, 1);  
    actividad_min = tiempo_actividad/60.;
    lcd.print(actividad_min);
    lcd.setCursor(4, 1);  
    lcd.print("min");
    
    //Serial.print(F("Tiempo de actividad:\t"));
    //Serial.print(actividad); 
    //Serial.println(" min");


    // Velocidad media
    //lcd.setCursor(0, 2); 
    //lcd.print("V media:");
    lcd.setCursor(8, 1);  
    mean_vel = sum_vel/cont_vel;
    lcd.print(mean_vel);
    lcd.setCursor(12, 1); 
    lcd.print("Km/h");
    //Serial.print(F("Velocidad media:\t"));
    //Serial.print(mean_vel);
    //Serial.print(" Km/h");
    //while(1);
       
     }

  delay(500); // leer cada medio segundo
  
  

}
