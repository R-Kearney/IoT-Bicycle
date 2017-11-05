import machine
import math
import network
import os
import time
import utime
from machine import RTC
from machine import SD
from machine import Timer
from L76GNSS import L76GNSS # GPS
#from LIS2HH12 import LIS2HH12 # Accelerometer
import pycom
from pytrack import Pytrack
import network
import time
from network import Sigfox
import socket
import gc

wlan = network.WLAN(mode=network.WLAN.STA)
wlan.connect('BabyQueefi', auth=(network.WLAN.WPA2, 'lordofthepings'))
while not wlan.isconnected():
    time.sleep_ms(50)
print(wlan.ifconfig())


sigfox = Sigfox(mode=Sigfox.SIGFOX, rcz=Sigfox.RCZ1) # init Sigfox for RCZ1 (Europe)
s = socket.socket(socket.AF_SIGFOX, socket.SOCK_RAW) # create a Sigfox socket
s.setblocking(True) # make the socket blocking
s.setsockopt(socket.SOL_SIGFOX, socket.SO_RX, False) # configure it as uplink only
sigfoxCounter = 0 # count numbre of sigfox trnasmissions (max 140 a day)


time.sleep(2)
gc.enable()

# setup rtc
rtc = machine.RTC()
rtc.ntp_sync("pool.ntp.org")
utime.sleep_ms(750)
print('\nRTC Set from NTP to UTC:', rtc.now())
utime.timezone(7200)
print('Adjusted from UTC to EST timezone', utime.localtime(), '\n')
py = Pytrack()
l76 = L76GNSS(py, timeout=30)
chrono = Timer.Chrono()
chrono.start()

# # Accelerometer
# # enable activity and also inactivity interrupts, using the default callback handler
# py.setup_int_wake_up(True, True)
# acc = LIS2HH12()
# # enable the activity/inactivity interrupts
# # set the accelereation threshold to 2000mG (2G) and the min duration to 200ms
# acc.enable_activity_interrupt(2000, 200)


while (True):

    coord = l76.coordinates(debug = False)
    #f.write("{} - {}\n".format(coord, rtc.now()))
    print("{} - {} - {}".format(coord, rtc.now(), gc.mem_free()))
    if not all(coord): # returns false if none is in the truple
        print("No lock found")
    else:
        print("lock found")
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

        #time.sleep(10)


    # # check if we were awaken due to activity
    # if acc.activity():
    #     pycom.rgbled(0xFF0000)
    # else:
    #     pycom.rgbled(0x00FF00)  # timer wake-up
    # time.sleep(0.1)
