import pycom
import time
from network import Sigfox
import math
import os
import utime
from machine import Timer
from machine import WDT # Watchdog to auto restart if system crashes
import socket
import gc # garbage collector
from machine import RTC
from machine import I2C

i2c = I2C(0, I2C.MASTER)             # create and init as a master
i2c.init(I2C.MASTER, baudrate=1000) # init as a master


sigfox = Sigfox(mode=Sigfox.SIGFOX, rcz=Sigfox.RCZ1) # init Sigfox for RCZ1 (Europe)
s = socket.socket(socket.AF_SIGFOX, socket.SOCK_RAW) # create a Sigfox socket
s.setblocking(True) # make the socket blocking
s.setsockopt(socket.SOL_SIGFOX, socket.SO_RX, False) # configure it as uplink only
sigfoxCounter = 0 # count numbre of sigfox trnasmissions (max 140 a day)


wdt = WDT(timeout=600000)  # enable Watchdog with a timeout of 10 minutes
gc.enable() # garbage collector


class PedalSensor:
    readingCount = 0
    accumulatedData = 0

    # sensor location would be 'tLS' which is topLeftSensor
    # address is the i2c address - 0x04
    def __init__(self, location, address):
      self.location = location
      self.address = address

    # Give a sensor a new address
    # newAddress should be in the form b'\x08'
    # oldaddress should be in the hex form 0x04 or 4
    # EG: changeSensorAddress(b'\x0a')
    # Final command EG: i2c.writeto(0x04, b'\x02\0\1\x0d\xFF')
    def changeSensorAddress(self, newAddress, oldAddress=4):
        i2c.writeto(oldAddress, b'\x02\0\1' + newAddress + b'\xFF')

    # Map the cap sensor values to Newtons
    def map(self, value, leftMin, leftMax, rightMin, rightMax):
      # Figure out how 'wide' each range is
      leftSpan = leftMax - leftMin
      rightSpan = rightMax - rightMin
      # Convert the left range into a 0-1 range (float)
      valueScaled = float(value - leftMin) / float(leftSpan)
      # Convert the 0-1 range into a value in the right range.
      result = rightMin + (valueScaled * rightSpan)
      if result < 0:
          return 0
      else:
          return result


    # Send data to cloud over sigfox
    def sendData(self, message):
      messageToSend = str(message)
      tempMessage = []
      bytesToSend = 0
      lastCount = 0
      sigfoxCounter = 0
      while bytesToSend < len(messageToSend):
          bytesToSend = bytesToSend + 12
          s.send(str(messageToSend[lastCount:bytesToSend]))
          lastCount = bytesToSend
          sigfoxCounter = sigfoxCounter + 1
          print("Sigfox messages sent: ", sigfoxCounter)
      time.sleep(5) # let everything go through
      return

    # reads sensor value from i2c
    # maps the value to sensor range of 0-100 Newtons
    # retuns Newtons
    def readFromSensor(self):
        try: # Try to read from Sensors
            sensorValue = i2c.readfrom(self.address, 6)
        except OSError:
            print("Error reading data, check connections. Sensor Location - ", self.location, " Sensor address - ", hex(self.address))
        sensorValue = int.from_bytes(sensorValue[4:], 'big')
        # sensor range 256 - 767 and 0 - 100 Newtons
        sensorValue = self.map(sensorValue, 256, 767, 0, 100)
        return sensorValue

    # reads sensor data,
    # calculates 5min average
    # sends data
    def tick(self):
        sensorValue = self.readFromSensor()
        # print (self.location, " - ", sensorValue , "Newtons ", "Count: " , self.readingCount)
        if sensorValue >= 1: # Sensor is Active
            self.accumulatedData += sensorValue
            self.readingCount += 1
            if self.readingCount == 5: # send data every 5min
                data = round((self.accumulatedData/5), 2) # avg force over the last 5 values
                data = str(self.location + "-" + str(data))
                # print("AVG force - ", data)
                self.sendData(data)
                self.accumulatedData = 0 # clear accumulatedData
                self.readingCount = 0
                return True
        else:
            return False


# Initilise sensors
topLeftSensor = PedalSensor("tLS", 0x0a)
topRightSensor = PedalSensor("tRS", 0x0b)
bottomRightSensor = PedalSensor("bRS", 0x0c)
bottomLeftSensor = PedalSensor("bLS", 0x0d)


# read sensor data every minute
while(True):
    topLeftSensor.tick()
    topRightSensor.tick()
    bottomLeftSensor.tick()
    bottomRightSensor.tick()
    wdt.feed() # tell watchdog we're stll alive

    time.sleep(60) # Sleep for a minute
