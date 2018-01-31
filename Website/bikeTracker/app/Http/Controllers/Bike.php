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
use Validator;
use Session;

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
        $user = DB::table('users')->where('bikeTrackerID', '=', $userBikeTrackerID)->first();
        if ($bikeLocation == null) { // GPS not synced for this bike
            $bikeLocation = new stdClass();
            $bikeLocation->lat = " ";
            $bikeLocation->long = " ";
            $bikeLocation->updated_at = " ";
            $bikeLocation->distance = 0.0; // Distance in km for selected date
        } else { // Calculate total distance cycled
            $allLocationData = DB::table('gpslocations')->where('bikeTrackerID', '=', $userBikeTrackerID)->orderBy('updated_at', 'desc')->get();
            $bikeLocation->distance = $this->calculateDistCycled($allLocationData); // get total distance cycled
        }
        //Send variables to the view
        return View::make('home', compact('bikeLocation', 'bike', 'user'));
    }

    /**
   * Get data for the timeline. Bike data and location data for the specified time period
   */
    public function timeline()
    {
        if (Input::get('date')) {
            $selectedDate = Input::get('date');
        } else {
            $selectedDate = date('Y-m-d');
        }

        //find customer
        $bikeOwner = Auth::user();
        $userBikeTrackerID = Auth::user()->bikeTrackerID;
        $bike = DB::table('bikes')->where('bikeTrackerID', '=', $userBikeTrackerID)->first();
        $bikeLocation = DB::table('gpslocations')->where('bikeTrackerID', '=', $userBikeTrackerID)
         ->whereDate('updated_at', '=', $selectedDate)->orderBy('updated_at', 'desc')->get();
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
            $bikeLocation->avgSpeed = $this->calculateAvgSpeed($bikeLocation);
            $bikeLocation = $this->snapToRoad($bikeLocation);
        }
        //Send variables to the view
        return View::make('timeline', ['bike' => $bike], ['bikeLocation' => $bikeLocation]);
    }


    /**
    * Calculate distance cycled for the selected data
    * returns KM
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
    * returns km/h
    */
    public function calculateAvgSpeed($bikeLocation)
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
    * returns new bikLocation object with location points
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
        $userDetials = DB::table('users')->where('bikeTrackerID', '=', $userBikeTrackerID)->first();
        // only send pedalID's
        $user = new stdClass();
        $user->bikePedalID = $userDetials->bikePedalID;
        $user->bikePedalID_2 = $userDetials->bikePedalID_2;
        //show the edit form
        return View::make('editBike', ['user' => $user], ['bike' => $bike]);
    }


    /**
    * Function to update the bike profile
    */
    public function update()
    {
        $rules = [
           'bikeName' => 'required|string|max:100',
           'bikeColour' => 'nullable|string|max:100',
           'bikeType' => 'nullable|string|max:100',
           'bikeMake' => 'nullable|string|max:100',
           'bikePedalID' => 'nullable|string|max:100',
           'bikePedalID_2' => 'nullable|string|max:100',
       ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('editBike')
                ->withErrors($validator)
                ->withInput();
        } else { // data passed the Validator
            $userBikeTrackerID = Auth::user()->bikeTrackerID;
            $bikeName = Input::get('bikeName');
            $bikeColour = Input::get('bikeColour');
            $bikeType = Input::get('bikeType');
            $bikeMake = Input::get('bikeMake');
            $bikePedalID = Input::get('bikePedalID');
            $bikePedalID_2 = Input::get('bikePedalID_2');

            DB::table('bikes')->where('bikeTrackerID', '=', $userBikeTrackerID)->update(array(
             'bikeName' => $bikeName,
             'bikeColour' => $bikeColour,
             'bikeType' => $bikeType,
             'bikeMake' => $bikeMake,
            ));

            DB::table('users')->where('bikeTrackerID', '=', $userBikeTrackerID)->update(array(
             'bikePedalID' => $bikePedalID,
             'bikePedalID_2' => $bikePedalID_2,
            ));

            Session::flash('success', 'Your profile was updated.');

            return Redirect::to('home');
        }
    }

    /**
   * Get data for the cycling dynamics page.
   */
    public function dynamics()
    {
        if (Input::get('date')) {
            $selectedDate = Input::get('date');
        } else {
            $selectedDate = date('Y-m-d');
        }

        //find customer
        $bikeOwner = Auth::user();
        $userBikeTrackerID = Auth::user()->bikeTrackerID;
        $bike = DB::table('bikes')->where('bikeTrackerID', '=', $userBikeTrackerID)->first();
        // set which pedal was selected
        if (Input::get('Pedal') == 1) {
            $userPedalID = DB::table('users')->where('bikeTrackerID', '=', $userBikeTrackerID)->first()->bikePedalID;
        } else {
            $userPedalID = DB::table('users')->where('bikeTrackerID', '=', $userBikeTrackerID)->first()->bikePedalID_2;
        }
        // pull data from the DB
        $bikeDynamics = DB::table('pedaldata')->where('bikePedalID', '=', $userPedalID)
        ->whereDate('updated_at', '=', $selectedDate)->orderBy('updated_at', 'desc')->get();

        if ($bikeDynamics->first() == null or $userPedalID == null) { // Data not synced for this bike
            $bikeDynamics = new stdClass();
            $bikeDynamics->updated_at = " ";
            $bikeDynamics->isData = false;
            $bikeDynamics->bikePedalID = " ";
            $bikeDynamics->calculatedNewtons = 0; // Watts would be better but wouldn't be accurate.
            $bikeDynamics->calculatedEfficency = 0;
            $bikeDynamics->pedalPower = [40, 60]; // sample data
            $bikeDynamics->pedalPowerLocationMin = [12, 33, 44, 44]; // sample data
            $bikeDynamics->pedalPowerLocationMax = [65, 59, 80, 81]; // sample data
        } else {
            $bikeDynamics->isData = true;
            $bikeDynamics->bikePedalID = $userPedalID;
            $bikeDynamics->pedalPower = $this->pedalPower($bikeDynamics); // [left, right, total]
            $bikeDynamics->pedalPowerLocationMin = $this->pedalPowerLocation($bikeDynamics, 0); // 0 = min, [tRS, bRS, bLS, tLS]
            $bikeDynamics->pedalPowerLocationMax = $this->pedalPowerLocation($bikeDynamics, 1); // 1 = max, [tRS, bRS, bLS, tLS]
            $bikeDynamics->calculatedEfficency = $this->pedalEfficiency($bikeDynamics);
            $bikeDynamics->calculatedNewtons =  $bikeDynamics->pedalPower[2]; // Watts be better but wouldn't be accurate.
        }

        // Generate the charts
        // Pedal power left
        $chartPowerLeft= app()->chartjs
        ->name('powerLeft')
        ->type('pie')
        ->datasets([
            [
                'backgroundColor' => ['#FF6384', '#fff'],
                'hoverBackgroundColor' => ['#FF6384', '#fff'],
                'data' => [$bikeDynamics->pedalPower[0], $bikeDynamics->pedalPower[1]]
            ]
        ])
        ->options([]);

        // Pedal power Right
        $chartPowerRight = app()->chartjs
        ->name('powerRight')
        ->type('pie')
        ->datasets([
            [
                'backgroundColor' => ['#36A2EB', '#fff'],
                'hoverBackgroundColor' => ['#36A2EB', '#fff'],
                'data' => [$bikeDynamics->pedalPower[1], $bikeDynamics->pedalPower[0]]
            ]
        ])
        ->options([]);

        // Power location
        $chartPowerLocation= app()->chartjs
        ->name('powerLocation')
        ->type('radar')
        ->labels(['Top', 'Right', 'Bottom', 'Left'])
        ->datasets([
            [
                'backgroundColor' => "rgba(255, 99, 132, 0)",
                'borderColor' => "rgba(255, 99, 132, 1)",
                "pointBorderColor" => "rgba(255, 99, 132, 1)",
                "pointBackgroundColor" => "rgba(255, 99, 132, 1)",
                "pointHoverBackgroundColor" => "#fff",
                "pointHoverBorderColor" => "rgba(255, 99, 132, 1)",
                'data' => $bikeDynamics->pedalPowerLocationMax,

            ],
            [
                'backgroundColor' => "rgba(54, 162, 235, 0)",
                'borderColor' => "rgba(54, 162, 235, 1)",
                "pointBorderColor" => "rgba(54, 162, 235, 1)",
                "pointBackgroundColor" => "rgba(54, 162, 235, 1)",
                "pointHoverBackgroundColor" => "#fff",
                "pointHoverBorderColor" => "rgba(54, 162, 235, 1)",
                'data' => $bikeDynamics->pedalPowerLocationMin,


            ]
        ])
        ->options([]);

        $chartPowerLocation->optionsRaw("{
            legend: {
                display:false
            },
            scale: {
				         ticks: {
					         display: false,
				         }
			         }
        }");


        return View::make('dynamics', compact('chartPowerLeft', 'chartPowerRight', 'chartPowerLocation'), ['bikeDynamics' => $bikeDynamics]);
    }

    /**
    *
    * reads power for selected pedal and day.
    * calculates the power on left and right side of the pedal
    * puts the power levels into a percentage of total power
    * rounds it and sends it back.
    * returns array[left, right]
    */
    public function pedalPower($bikeDynamics)
    {
        $left = 0;
        $right = 0;
        foreach ($bikeDynamics as $bikeDynamicsTemp) {
            if ($bikeDynamicsTemp->sensorLocation == "tLS" or $bikeDynamicsTemp->sensorLocation == "bLS") {
                $left = $left + $bikeDynamicsTemp->data;
            } else {
                $right = $right + $bikeDynamicsTemp->data;
            }
        }
        $totalPower = $left + $right;
        $normLeft = round($left * (100/$totalPower)); // calculate percentage
        $normRight = round($right * (100/$totalPower)); // calculate percentage
        return [$normLeft, $normRight, $totalPower];
    }

    /**
    *
    * reads power for selected pedal and day.
    * Gets either min or max (0 = min) power for each sensor location
    * returns array[tRS, bRS, bLS, tLS]
    */
    public function pedalPowerLocation($bikeDynamics, $max)
    {
        $tRS = array();
        $bRS = array();
        $bLS = array();
        $tLS = array();
        foreach ($bikeDynamics as $bikeDynamicsTemp) {
            if ($bikeDynamicsTemp->sensorLocation == "tRS") {
                $tRS[] = $bikeDynamicsTemp->data;
            } elseif ($bikeDynamicsTemp->sensorLocation == "bRS") {
                $bRS[] = $bikeDynamicsTemp->data;
            } elseif ($bikeDynamicsTemp->sensorLocation == "bLS") {
                $bLS[] = $bikeDynamicsTemp->data;
            } elseif ($bikeDynamicsTemp->sensorLocation == "tLS") {
                $tLS[] = $bikeDynamicsTemp->data;
            }
        }

        // place 0 in array's if they have no data
        $tRS = $tRS ?: [0];
        $bRS = $bRS ?: [0];
        $bLS = $bLS ?: [0];
        $tLS = $tLS ?: [0];

        if ($max == 1) {
            return [max($tRS), max($bRS), max($bLS), max($tLS)];
        } else {
            return [min($tRS), min($bRS), min($bLS), min($tLS)];
        }
    }

    /**
    *
    * reads power for selected pedal and day.
    * calculates cycling Efficency based on how closely related the power is per location
    * 100% efficency is all sensors reading the same data EG: force is applied directly to the center of the pedal.
    * returns a percentage
    */
    public function pedalEfficiency($bikeDynamics)
    {
        $left = 0;
        $right = 0;
        foreach ($bikeDynamics as $bikeDynamicsTemp) {
            if ($bikeDynamicsTemp->sensorLocation == "tLS" or $bikeDynamicsTemp->sensorLocation == "bLS") {
                $left = $left + $bikeDynamicsTemp->data;
            } else {
                $right = $right + $bikeDynamicsTemp->data;
            }
        }
        $totalPower = $left + $right;
        $normLeft = round($left * (100/$totalPower));
        //$normRight = round($right * (100/$totalPower));

        $top = 0;
        $bottom = 0;
        foreach ($bikeDynamics as $bikeDynamicsTemp) {
            if ($bikeDynamicsTemp->sensorLocation == "tLS" or $bikeDynamicsTemp->sensorLocation == "tRS") {
                $top = $top + $bikeDynamicsTemp->data;
            } else {
                $bottom = $bottom + $bikeDynamicsTemp->data;
            }
        }
        $totalPower = $top + $bottom;
        $normTop = round($top * (100/$totalPower));
        //$normBottom = round($bottom * (100/$totalPower));

        $efficency = abs((abs($normLeft - 50) + abs($normTop - 50) - 100) / 2); // difference from center (perfect distrubution of power)
        $efficency = $efficency * 100/50; // get in a percentage

        return $efficency;
    }
}
