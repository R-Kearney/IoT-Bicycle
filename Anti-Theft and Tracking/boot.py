import network
import time
# setup as a station
wlan = network.WLAN(mode=network.WLAN.STA)
wlan.connect('Wifi Network Name', auth=(network.WLAN.WPA2, 'Password'))
while not wlan.isconnected():
    time.sleep_ms(50)
print(wlan.ifconfig())
# now use socket as usual
