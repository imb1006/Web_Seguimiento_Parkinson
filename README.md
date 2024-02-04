# Web_Seguimiento_Parkinson

El contenido de este repositorio contiene los materiales necesarios para seguir el desarrollo del Trabajo de Fin de Grado de Ingeniería de la Salud, elaborado por Inés Martos Barbero.

El proyecto se basa un trabajo previo desarrollado por Sara González Bárcena (disponible en [este enlace](https://github.com/saragonzalezbarcena/TFG_Deteccion_Activ_Muscular)), para lograr la creación de un sitio web que se comunica en tiempo real y permite el manejo de un dispositivo de monitorización de la marcha en personas con Párkinson.

## Estructura del repositorio
Esta breve explicación pretende ayudar a cualquier persona interesada en el proyecto a navegar por este repositorio. El usuario podrá encontrar información detallada sobre cada directorio y archivo en el documento **anexos.pdf** (*Anexo C.1 Estructura de directorios*).

- **LICENSE**. Documento de la licencia Apache License 2.0 utilizada para este proyecto
- **Arduino/**.Contiene todo el material, desde scripts hasta librerías, relacionado con el desarrollo del trabajo en lo relativo al software para la placa Arduino.
- **Documentacion_Overleaf/**. Carpeta que contiene todos los archivos empleados en el desarrollo de la memoria con la herramienta Overleaf. Incluye los documentos pdf de la memoria y anexos.
- **Pruebas_Comunicación/**. Incluye una serie de archivos y carpetas utilizados en la realización de pruebas de comunicación entre el servidor Node.js y Arduino, un proceso coordinado por el archivo bridge.py.
- **Web_VisualStudio.zip**. Archivo comprimido que tiene el mismo contenido que la carpeta Web_VisualStudio/. Se incluye para facilitar la descarga de todos los documentos necesarios para el despliegue de la aplicación web.
- **Web_VisualStudio/**. Contiene, almacenados en subcarpetas, todos los archivos y scripts de código necesarios para la creación de la página web
- 


## Resumen
Los problemas motores característicos de la Enfermedad de Párkinson (EP) afectan significativamente la función de la marcha, provocando episodios de congelación de la marcha en las etapas más críticas. Esto repercute considerablemente en la calidad de vida de las personas con EP.

Los dispositivos de monitorización disponibles para esta enfermedad son caros y escasos, y son aún menos los enfocados en analizar los parámetros de la marcha. La recopilación y análisis de esta información son esenciales para facilitar la toma de decisiones objetivas e informadas por parte de los profesionales sobre la modificación del tratamiento y adaptación de terapias.

Continuando con un proyecto anterior, cuyo objetivo era proporcionar una herramienta de apoyo en el ámbito clínico y de ayuda para pacientes, se han realizado pequeñas mejoras en el hardware del dispositivo utilizado para el registro de datos y se ha desarrollado un software, concretamente un sitio web. Este avance ha permitido el funcionamiento inalámbrico del dispositivo mediante el empleo de Bluetooth para la comunicación con el servidor web. La transmisión de datos se realiza en tiempo real, lo que permite su visualización desde una interfaz simple que también posibilita la gestión de la recogida de datos. La innovación de la plataforma web consiste en permitir tanto a profesionales como a pacientes acceder de forma sencilla a la información más relevante.


## Abstract
The characteristic motor problems of Parkinson's Disease (PD) significantly affect gait function, causing freezing of gait episodes in the most critical stages. This considerably impacts the quality of life of people with PD.

The monitoring devices available for this disease are expensive and scarce, and even fewer focus on analyzing gait parameters. The collection and analysis of this information are essential to facilitate objective and informed decision-making by professionals regarding treatment modification and therapy adaptation.

Continuing with a previous project, whose goal was to provide a support tool in the clinical setting and aid for patients, small improvements have been made to the hardware of the device used for data recording, and software has been developed, specifically a website. This advancement has enabled the wireless operation of the device through the use of Bluetooth for communication with the web server. Data transmission occurs in real-time, allowing its visualization from a simple interface that also enables the management of data collection. The innovation of the web platform lies in allowing both professionals and patients to easily access the most relevant information.
