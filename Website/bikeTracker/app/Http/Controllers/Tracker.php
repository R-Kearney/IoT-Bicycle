<?php

namespace App\Http\Controllers;

use Request;
use Input;
use DB;
use Session;
use Validator;

class Tracker extends Controller
{
    public function updateLocation()
    {
        $rules = [
           'device' => 'required|string|max:10', // upto 8 char HEX
           'data' => 'required|string|max:100', // 12 chars HEX
       ];

        $validator = Validator::make(Input::all(), $rules);
        $isConnectionSecure = Request::secure();
        $origin = ip2long((string) Request::getClientIp());
        $dataTrusted = false; // Only True if data comes from Sigfox networks.
        $sigfoxIPL = ip2long('185.110.97.0'); // Sigfox ip range 185.110.97.0/24
        $sigfoxIPH = ip2long('185.110.97.255');

        // Check if data came from Sigfox
        if (($origin <= $sigfoxIPH && $sigfoxIPL <= $origin) && $isConnectionSecure == true) {
            echo "<h1> IP Came from Sigfox and is Secure :)</h1>";
            $dataTrusted = true;
        }

        if ($validator->fails()) {
            // var_dump(\Input::all());
            // echo "Validator Failed!";
            return; // Is this ok
        } elseif ($dataTrusted == true) {
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


            echo $userBikeTrackerID . " " . $bikeCoords[0] ." " . $bikeCoords[1];



            Session::flash('success', 'Your profile was updated.');

            // return \Redirect::to('home');
        } else { // Data not trusted
            echo("Not Trusted");
        }
    }
}
