# iot-arqui2-p1
Este repositorio contiene los archivos esenciales y documentación para la configuración y despliegue de un sistema IoT con ESP32 y una máquina virtual en Raspbian. Incluye configuraciones para MQTT, Node-RED, MariaDB y una interfaz web en PHP.
# 🔧 Tecnologías incluidas
- **ESP32** con código en C++ utilizando PlatformIO.
- **Raspbian OS** alojando los servicios de backend.
- **MariaDB** para el almacenamiento de datos.
- **phpMyAdmin** para la gestión de la base de datos.
- **Node-RED** para la automatización de flujos de datos.
- **EMQX y Mosquitto** como brokers MQTT para la comunicación entre dispositivos.

## 📁 Contenido del repositorio
- 📜 **Configuraciones** → Archivos esenciales para la configuración de servicios (`mosquitto.conf`, `emq.conf`, `php.ini`).
- 🖥️ **Código fuente** → Scripts y programas de control para la comunicación y procesamiento de datos.
- 🗄️ **Base de datos** → Archivos `.sql` para la estructura de la base de datos y su inicialización.
- 📝 **Documentación** → Instrucciones detalladas para la instalación y puesta en marcha del sistema.

## ⚙️ Despliegue y administración
Para actualizar los archivos en el servidor, se recomienda clonar este repositorio en la máquina virtual y ejecutar los cambios manualmente o mediante scripts de automatización.
