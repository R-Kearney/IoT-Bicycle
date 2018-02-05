from pytrack import Pytrack
from LIS2HH12 import LIS2HH12
import pycom
import time
#from network import Bluetooth
# import network
from network import Sigfox
# import machine
import math
import os
import utime
from machine import Timer
from machine import WDT # Watchdog to auto restart if system crashes
from L76GNSS import L76GNSS # GPS
import socket
import gc
from machine import RTC

pycom.heartbeat(False)
wdt = WDT(timeout=120000)  # enable Watchdog with a timeout of 2 minutes

py = Pytrack()
# enable wakeup source from INT pin
py.setup_int_pin_wake_up(False)
py.setup_int_wake_up(True, True)
acc = LIS2HH12()
acc.enable_activity_interrupt(100, 20) # Very sensitive
maxSleep = 86400 # Max time the device is allowed to sleep for (seconds). 24 hours

sigfox = Sigfox(mode=Sigfox.SIGFOX, rcz=Sigfox.RCZ1) # init Sigfox for RCZ1 (Europe)
s = socket.socket(socket.AF_SIGFOX, socket.SOCK_RAW) # create a Sigfox socket
s.setblocking(True) # make the socket blocking
s.setsockopt(socket.SOL_SIGFOX, socket.SO_RX, False) # configure it as uplink only
sigfoxCounter = 0 # count numbre of sigfox trnasmissions (max 140 a day)

gc.enable()
l76 = L76GNSS(py, timeout=30) # setup GPS

gc.collect()
noLock = 0
lock = False
pycom.rgbled(0xFF0000) # red
print("Activity")
asleepTime = maxSleep - py.get_sleep_remaining() # seconds, not accurate

print("We were asleep for ", asleepTime , " Seconds.")
if (asleepTime < 200): # limit the max frequency of location pulls
    time.sleep(60)

while (lock == False) and (noLock < 3): # Try get GPS 3 Times before giving up and going to Sleep
    coord = l76.coordinates(debug = False)
    print("{} - {} KB".format(coord, gc.mem_free()/1000))
    if not all(coord): # returns false if none is in the truple
        print("No lock found")
        noLock = noLock + 1
        wdt.feed() # tell watchdog we're stll alive
    else:
        print("lock found")
        lock = True
        wdt.feed() # tell watchdog were stll alive
        #coordStr = ' '.join(map(str,float_to_hex(coord)))
        coordHex = (hex(int(coord[0] * 100000))[2:] + hex(int(coord[1] * 100000))[2:]) # 5 dec places is plenty acctraue. (4 is still good)
        messageToSend = coordHex
        tempMessage = []
        bytesToSend = 0
        lastCount = 0
        while bytesToSend < len(messageToSend):
            bytesToSend = bytesToSend + 12
            s.send(str(messageToSend[lastCount:bytesToSend]))
            lastCount = bytesToSend
            sigfoxCounter = sigfoxCounter + 1
            print("Sigfox messages sent: ", sigfoxCounter)
        time.sleep(5) # let everything go through

# go to sleep for 24 hours maximum if no accelerometer interrupt happens
print("Last Sleep length: ", asleepTime, "seconds")
print("Going to sleep...")
py.setup_sleep(maxSleep)
py.go_to_sleep()
