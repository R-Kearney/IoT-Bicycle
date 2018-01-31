<?php

namespace App\Http\Controllers;

use Request;
use Input;
use DB;
use Session;
use Validator;

class DataUpload extends Controller
{
    // Verify location and validate data structure
    public function verifyData()
    {
        $rules = [
         'device' => 'required|string|max:10', // upto 8 char HEX
         'data' => 'required|string|max:100', // 12 chars HEX
        ];

        $validator = Validator::make(Input::all(), $rules);
        $isConnectionSecure = Request::secure();
        $origin = ip2long((string) Request::getClientIp());
        $sigfoxIPL = ip2long('185.110.97.0'); // Sigfox ip range 185.110.97.0/24
        $sigfoxIPH = ip2long('185.110.97.255');


        if ($validator->fails()) {
            // echo "Validator Failed!";
            return false;
            // Check if data came from Sigfox
        } elseif (($origin <= $sigfoxIPH && $sigfoxIPL <= $origin) && $isConnectionSecure == true) {
            echo "<h1> IP Came from Sigfox and is Secure :)</h1>";
            return true;
        } else {
            return false;
        }
        //return true; // testing
    }

    // Saves device location data into database.
    public function updateLocation()
    {
        if ($this->verifyData()  == true) {
            $userBikeTrackerID = Input::get('device'); //hex
            // device location example "353036316435786431363534"
            // unHex will result in "5061d5xd1654"
            // unhex "5061d5" and "d1654" seperately to give float lat and long
            // the "x" reperesents a negative value.
            $bikeLocationHex = hex2bin(Input::get('data'));
            $bikeCoords = explode("x", $bikeLocationHex); // TODO this will only work for negative second value
            $bikeCoords[0] = hexdec($bikeCoords[0]) / 100000;
            $bikeCoords[1] = hexdec($bikeCoords[1]) / -100000;

            DB::table('gpslocations')->insert(array(
             'bikeTrackerID' => $userBikeTrackerID,
             'lat' => $bikeCoords[0],
             'long' => $bikeCoords[1],
             'updated_at' => date('Y-m-d G:i:s'),
            ));

            return \Redirect::to('home');
        } else { // Data not trusted
            echo("Not Trusted");
        }
    }

    public function updatePedal()
    {
        if ($this->verifyData()  == true) {
            $userBikePedalID = Input::get('device'); //hex
            // Data is comprised of sensor location (tLS) and sensor data in float (2 decimal places)
            // EG: tLS-6.5
            //
            // '-' is used to separate address and data
            $rawData = hex2bin(Input::get('data'));
            $rawData = explode("-", $rawData);
            $sensorLocation = $rawData[0];
            $data = $rawData[1];

            DB::table('pedaldata')->insert(array(
             'bikePedalID' => $userBikePedalID,
             'sensorLocation' => $sensorLocation,
             'data' => $data,
             'updated_at' => date('Y-m-d G:i:s'),
             'created_at' => date('Y-m-d G:i:s'),
            ));

            return \Redirect::to('home');
        } else { // Data not trusted
            echo("Not Trusted");
        }
    }
}
