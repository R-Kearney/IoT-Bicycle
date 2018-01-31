# My Final Year Project - An IoT Bicycle

The goal of this project is to demonstrate the use of smart modular sensors to improve the cycling experience.
To ensure the success of this project I will be focusing on two modules.

    - Bicycle Anti-Theft and Tracking
    - Cycling dynamics with a smart pedal

The sensor modules are fully modular and rely on the very powerful Sigfox network for low power communications to my backend. The sensors themselves are based on the Pycom SiPy boards (pycom.io) which gives you access to Wi-Fi, BLE and Sigfox on a single low power board. The boards are based on the ESP32 and are programmed in micro Python to allow for quick prototyping. 

This is still a work in progress

![Web App Example Timeline](https://github.com/R-Kearney/IoT-Bike/blob/master/Website/Eg_Timeline.jpg)


## Web App
To extend the battery life of the sensors I will be utilizing the cloud to process all sensor data and provide an easy to use experience to the end user. The backend is built using the Laravel PHP framework (laravel.com) to allow for quick prototyping and a consumer ready system. 


## Bicycle Anti-Theft and Tracking
This sensor will use a Pycom PyTrack along with the SiPy to allow the bike to be tracked using GPS.
When an accelerometer interrupt is triggered, the module will wake up from deep sleep and acquire the bikes GPS location. The location is sent to the web app where the user can track their bike if it is stolen. 

The Web App will allow the end user to view past trips on a map and see trip information such as distance and average speed.

## Cycling Dynamics
A smart pedal will use 4 capacitive force sensors to record the users peddling power. The capacitive force sensors are made by singletact and communicate to a second Pycom SiPy board over i2c. The board reads and maps the force before sending to the web app for processing. The web app displays the users cycling dynamics and peddling efficiency.

![Web App Example Cycling Dynamics](https://github.com/R-Kearney/IoT-Bike/blob/master/Website/Eg_Cycling_Dynamics.jpg)
![Smart Pedal](https://github.com/R-Kearney/IoT-Bike/blob/master/Smart_Pedal.jpg)

### Possible future features
1. Better power management by utilizing WiFi or cellular location instead of relying on high power consumption GPS.
2. Tire pressure sensor
3. Real time audible cues to improve cycling dynamics
4. Google Assistant and IFTTT Integration