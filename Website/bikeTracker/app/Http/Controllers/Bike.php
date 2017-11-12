<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use stdClass;
use Auth;
use DB;
use View;
use Input;
use Geotools;
use Redirect;

// use Client;

class Bike extends Controller
{


     /**
    * Pull data for the bike and current location
    */
    public function view()
    {
        //find customer
        $bikeOwner = Auth::user();
        $userBikeTrackerID = Auth::user()->bikeTrackerID;
        $bike = DB::table('bikes')->where('bikeTrackerID', '=', $userBikeTrackerID)->first();
        $bikeLocation = DB::table('gpslocations')->where('bikeTrackerID', '=', $userBikeTrackerID)->orderBy('updated_at', 'desc')->first();
        $distanceCycled = 0.0; // Distance in km for selected date
        if ($bikeLocation == null) { // GPS not synced for this bike
            $bikeLocation = new stdClass();
            $bikeLocation->lat = " ";
            $bikeLocation->long = " ";
            $bikeLocation->updated_at = " ";
            $bikeLocation->distance = $distanceCycled;
        } else { // Calculate total distance cycled
            $allLocationData = DB::table('gpslocations')->where('bikeTrackerID', '=', $userBikeTrackerID)->orderBy('updated_at', 'desc')->get();
            $bikeLocation->distance = $this->calculateDistCycled($allLocationData);
        }
        //show the edit form
        return View::make('home', ['bike' => $bike], ['bikeLocation' => $bikeLocation]);
    }

    /**
   * Get data for the timeline. Bike data and location data for the specified time period
   */
    public function timeline()
    {
        if (Input::get('date')) {
            $showRouteForDate = Input::get('date');
        } else {
            $showRouteForDate = date('Y-m-d');
        }

        //find customer
        $bikeOwner = Auth::user();
        $userBikeTrackerID = Auth::user()->bikeTrackerID;
        $bike = DB::table('bikes')->where('bikeTrackerID', '=', $userBikeTrackerID)->first();
        $bikeLocation = DB::table('gpslocations')->where('bikeTrackerID', '=', $userBikeTrackerID)
         ->whereDate('updated_at', '=', $showRouteForDate)->orderBy('updated_at', 'desc')->get();
        $bikeLocation->distance = 0; // Distance in km for selected date
        $bikeLocation->avgSpeed = 0; // Avg speed in km for selected date
        $bikeLocation->isData = true;

        if ($bikeLocation->first() == null) { // GPS not synced for this bike
            $bikeLocation = new stdClass();
            $bikeLocation->lat = " ";
            $bikeLocation->long = " ";
            $bikeLocation->updated_at = " ";
            $bikeLocation->isData = false;
            $bikeLocation->distance = 0;
        } elseif (sizeof($bikeLocation) > 1) { // Some Functions to make the data nicer
            $bikeLocation->distance = $this->calculateDistCycled($bikeLocation);
            $bikeLocation->avgSpeed = $this->calculateAvg($bikeLocation);
            $bikeLocation = $this->snapToRoad($bikeLocation);
        }
        //show the edit form
        return View::make('timeline', ['bike' => $bike], ['bikeLocation' => $bikeLocation]);
    }


    /**
    * Calculate distance cycled
    *
    */
    public function calculateDistCycled($bikeLocation)
    {
        $distanceCycled = 0.0; // Distance in km for selected date
        $i = 0;
        foreach ($bikeLocation as $bikeLocationTemp) {
            $location = Geotools::coordinate([$bikeLocationTemp->lat, $bikeLocationTemp->long]);
            if ($i != 0) {
                $distanceCycled = $distanceCycled + Geotools::distance()->setFrom($location)->setTo($lastLocation)->in('km')->haversine();
            }
            $lastLocation = Geotools::coordinate([$bikeLocationTemp->lat, $bikeLocationTemp->long]);
            $i = 1;
        }
        $distanceCycled = number_format($distanceCycled, 2);
        return $distanceCycled;
    }


    /**
    * Calculate Avgerage Speed over the time period
    *
    */
    public function calculateAvg($bikeLocation)
    {
        $avgSpeed = 0.0; // avg speed in km/h for selected date
        $distanceCycled = 0;
        $timeDiff = 0;
        $i = 0;
        foreach ($bikeLocation as $bikeLocationTemp) {
            $location = Geotools::coordinate([$bikeLocationTemp->lat, $bikeLocationTemp->long]);
            $time = strtotime($bikeLocationTemp->updated_at);
            if ($i != 0) {
                $distanceCycled = $distanceCycled + Geotools::distance()->setFrom($location)->setTo($lastLocation)->in('km')->haversine();
                $timeDiff = $timeDiff + (($lastTime - $time) / 60); // Time difference in minutes
            }
            $lastLocation = Geotools::coordinate([$bikeLocationTemp->lat, $bikeLocationTemp->long]);
            $lastTime = strtotime($bikeLocationTemp->updated_at);
            $i = 1;
        }
        $avgSpeed = ($distanceCycled / $timeDiff) * 60; // avg speed in hours
        $avgSpeed = number_format($avgSpeed, 2);
        return $avgSpeed;
    }


    /**
    * Snap GPS points to the nearest Road with Google Maps Road API
    * Google's API only accepts 100 at a time and replies with Json
    *
    */
    public function snapToRoad($bikeLocation)
    {
        $i = 0;
        $k = 0;
        $parameters = "";
        foreach ($bikeLocation as $bikeLocationTemp) {
            if ($i != 0) { // Format correctly for google API
                $parameters = $parameters . "|";
            }
            $parameters = $parameters . $bikeLocationTemp->lat . "," . $bikeLocationTemp->long;
            $i++;
            if ($i >= 100) { // Break into 100 Coord blocks
                $parametersFull[$k] = $parameters;
                $parameters = "";
                $i = 0;
                $k++;
            }
        }
        $parametersFull[$k] = $parameters;
        $client = new Client(); // HTTP Client for Google API
        $noPoints = sizeof($bikeLocation);
        $lastUpdated = $bikeLocation[$noPoints-1]->updated_at;
        $bikeTrackerID = $bikeLocation[0]->bikeTrackerID;
        $i = 0;
        foreach ($parametersFull as $parameters) { // For each 100 block of Coords send off and put into new objects
            $newPoints = $client->request('GET', 'https://roads.googleapis.com/v1/snapToRoads?path=' . $parameters . '&interpolate=true&key=' . env("GOOGLE_MAPS_KEY"));
            $newPoints = json_decode($newPoints->getBody())->snappedPoints;

            foreach ($newPoints as $locations) { // put into $bikeLocation object
                if ($i < $noPoints) {
                    $bikeLocation[$i]->lat = $locations->location->latitude;
                    $bikeLocation[$i]->long = $locations->location->longitude;
                } else { // More points than before so make new objects
                    $bikeLocation[$i] = new stdClass();
                    $bikeLocation[$i]->bikeTrackerID = $bikeTrackerID;
                    $bikeLocation[$i]->lat = $locations->location->latitude;
                    $bikeLocation[$i]->long = $locations->location->longitude;
                    $bikeLocation[$i]->updated_at = $lastUpdated;
                }
                $i++;
            }
        }
        //print_r($bikeLocation);
        return $bikeLocation;
    }



    /*
    * Show the edit bike profile page
    */
    public function edit()
    {
        //find customer
        $bikeOwner = Auth::user();
        $userBikeTrackerID = Auth::user()->bikeTrackerID;
        $bike = DB::table('bikes')->where('bikeTrackerID', '=', $userBikeTrackerID)->first();

        //show the edit form
        return View::make('editBike')->with('bike', $bike);
    }


    /**
    * Function to update the bike profile
    */
    public function update()
    {
        $rules = [
           'bikeName' => 'required|string|max:100',
           'bikeColour' => 'string|max:100',
           'bikeType' => 'string|max:100',
           'bikeMake' => 'string|max:100',
       ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('editBike')
                ->withErrors($validator)
                ->withInput();
        } else {
            //$user = User::find($id);

            $userBikeTrackerID = Auth::user()->bikeTrackerID;
            $bikeName = Input::get('bikeName');
            $bikeColour = Input::get('bikeColour');
            $bikeType = Input::get('bikeType');
            $bikeMake = Input::get('bikeMake');

            DB::table('bikes')->where('bikeTrackerID', '=', $userBikeTrackerID)->update(array(
             'bikeName' => $bikeName,
             'bikeColour' => $bikeColour,
             'bikeType' => $bikeType,
             'bikeMake' => $bikeMake,
            ));

            Session::flash('success', 'Your profile was updated.');

            return Redirect::to('home');
        }
    }
}
