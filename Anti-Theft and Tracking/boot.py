import network
import time
# setup as a station
wlan = network.WLAN(mode=network.WLAN.STA)
wlan.connect('BabyQueefi', auth=(network.WLAN.WPA2, 'lordofthepings'))
while not wlan.isconnected():
    time.sleep_ms(50)
print(wlan.ifconfig())

machine.main('main.py')
# now use socket as usual
