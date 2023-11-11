En esta carpeta (sensor 1.0) se encuentran los archivos .ino necesarios para el funcionamiento del prototipo del TFG de Sara Gonzalez sin ninguna modificación.  
  
Descripción de los archivos:
-	**MPU6050.ino** -> contiene el programa de funcionamiento del prototipo con un módulo LCD 16x2
-	**MPU6050-lcd20.ino** -> contiene el programa de funcionamiento del prototipo con un módulo LCD 20x4 conectado mediante el módulo I2C 
-	**calibracionDMP.ino** -> necesario para llevar a calibrar el DMP del módulo MPU-6050 y así obtener los offsets del acelerómetro y del giroscopio. Es necesario ejecutarlo en el Arduino antes de cargar el programa principal. Este proceso se haría en la fábrica, antes de que le llegue al usuario.
-	**MPU6050-dmp.ino** -> (extra para pruebas) activa el DMP del módulo MPU-6050. Determina y muestra el número de pasos realizados por el paciente en función de los valores pitch, roll y yaw.
-	**MPU6050-filtro.ino** -> (extra para pruebas) determinar la orientación del sensor a partir del ángulo de inclinación y de rotación obtenidos previamente.

