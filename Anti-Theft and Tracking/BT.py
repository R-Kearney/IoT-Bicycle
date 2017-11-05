from network import Bluetooth
import pycom

BTDeviceConnected = 0
pycom.heartbeat(False)


def activateBLE():
    from network import Bluetooth
    bluetooth = Bluetooth()
    bluetooth.set_advertisement(name='IoTBike', service_uuid=b'1234567890123456')

    bluetooth.callback(trigger=Bluetooth.CLIENT_CONNECTED | Bluetooth.CLIENT_DISCONNECTED, handler=BLE_Activity)

    bluetooth.advertise(True)

    srv1 = bluetooth.service(uuid=b'1234567890123456', isprimary=True)

    chr1 = srv1.characteristic(uuid=b'ab34567890123456', value=5)

    char1_read_counter = 0
    def char1_cb_handler(chr):
        global char1_read_counter
        char1_read_counter += 1

        events = chr.events()
        if  events & Bluetooth.CHAR_WRITE_EVENT:
            print("Write request with value = {}".format(chr.value()))
        else:
            if char1_read_counter < 3:
                print('Read request on char 1')
            else:
                return 'ABC DEF'

    char1_cb = chr1.callback(trigger=Bluetooth.CHAR_WRITE_EVENT | Bluetooth.CHAR_READ_EVENT, handler=char1_cb_handler)

def BLE_Activity (bt_o):
    from network import Bluetooth
    events = bt_o.events()
    global BTDeviceConnected
    if  events & Bluetooth.CLIENT_CONNECTED:
        print("Client connected")
        BTDeviceConnected = 1
    elif events & Bluetooth.CLIENT_DISCONNECTED:
        print("Client disconnected")
        BTDeviceConnected = 0


activateBLE()
while True:
    if BTDeviceConnected == 0:
        pycom.rgbled(0xFF0000) # red
    else:
        pycom.rgbled(0x00FF00) # green
