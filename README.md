# My Final Year Project - An IoT Bicycle

The goal of this project is to demonstrate the use of smart modular sensors to improve the cycling experience.
To ensure the success of this project I will be focusing on two sensors.

    - Bicycle Anti-Theft and Tracking
    - Peddle Pressure Sensor

The sensor modules are fully modular and rely on the very powerful Sigfox network for low power communications to my backend. The sensors themselves are based on the Pycom SiPy boards (pycom.io) which gives you access to Wi-Fi, BLE and Sigfox on a single low power board. The boards are based on the ESP32 and are programmed in micro Python to allow for quick prototyping. 

This is still a work in progress

[[https://github.com/R-Kearney/IoT-Bike/blob/master/Website/Eg_Timeline.jpg|alt=Web App Example Timeline]]

## Web App
To extend the battery life of the sensors I will be utilising the cloud to process all sensor data and provide an easy to use experience to the end user. The backend is built using Laravel (laravel.com) to allow for quick prototyping and a consumer ready system. 


## Bicycle Anti-Theft and Tracking
This sensor will use a Pycom PyTrack along with the SiPy to allow the bike to be tracked using GPS.
When an accelerometer interrupt is triggered, the system will check if the registered smart phone is within Bluetooth Low Energy range. If it is, the devices will track the user’s position as he/she cycles. If the smart phone is not within range, an alarm will be activated to deter the theft of the bike. The Bikes GPS location will be recorded and user notified.

The web app will allow the end user to view past trips on a map and see trip information such as distance and average speed.

## Peddle Pressure Sensor
The peddle pressure sensor will use 4 capacitive force sensors to record the users peddling power. This data can be shown on a HUD or smart phone to tell the user if they are in the correct gear. 

Each sensor will have its own Sigfox communication allowing them to be installed in any order and removes the need for a base station.

### Implemented Features
1. Web app shows bike info and last location
2. Web app shows a user’s timeline for selected date and displays on a map
3. Bike Tracking - wakes on accelerometer interrupt and gets GPS location before sending to backend via Sigfox


### In Progress
1. Anti-Theft - Bluetooth LE check when users smart phone is near by

### Features still to be Implemented
1. Peddle Pressure sensor
2. Anti-Theft - user notification and local alarm to deter theft
