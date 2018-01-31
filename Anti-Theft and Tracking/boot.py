import network
import time
# setup as a station
wlan = network.WLAN(mode=network.WLAN.STA)
wlan.connect('SSID', auth=(network.WLAN.WPA2, 'PASSWORD'))
i = 0
while not wlan.isconnected():
    time.sleep_ms(50)
    i += 1
    if (i == 3): # try to connect to WiFi 3 times before giving up
        break
print(wlan.ifconfig())

# now use socket as usual
